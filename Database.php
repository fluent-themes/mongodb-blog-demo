<?php
namespace App;

use MongoDB\Client;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use MongoDB\BSON\UTCDateTime;
use Dotenv\Dotenv;

class Database
{
    private $client;
    private $collection;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__));
        $dotenv->load();

        $this->client = new Client($_ENV['MONGODB_URI']);
        $db = $this->client->selectDatabase($_ENV['MONGODB_DB']);
        $this->collection = $db->selectCollection($_ENV['MONGODB_COLLECTION']);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getAll(int $page = 1, int $perPage = 5): array
    {
        $skip = ($page - 1) * $perPage;
        
        $cursor = $this->collection->find(
            [],
            [
                'sort' => ['createdAt' => -1],
                'skip' => $skip,
                'limit' => $perPage
            ]
        );

        $posts = [];
        foreach($cursor as $doc) {
            $posts[] = $this->docToArray($doc);
        }

        $total = $this->collection->countDocuments();

        return ['posts' => $posts, 'total' => $total];
    }

    /**
     * @param string $id
     * @return array|null
     */
    public function getById(string $id): ?array
    {
        try {
            $doc = $this->collection->findOne(['_id' => new ObjectId($id)]);
            return $doc ? $this->docToArray($doc) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        $doc = [
            'title' => $data['title'],
            'content' => $data['content'],
            'category' => $data['category'],
            'createdAt' => new UTCDateTime()
        ];

        return $this->collection->insertOne($doc);
    }

    /**
     * @param string $id
     * @param array $data
     */
    public function update(string $id, array $data)
    {
        return $this->collection->updateOne(
            ['_id' => new ObjectId($id)],
            ['$set' => [
                'title' => $data['title'],
                'content' => $data['content'],
                'category' => $data['category']
            ]]
        );
    }

    public function delete(string $id)
    {
        return $this->collection->deleteOne(['_id' => new ObjectId($id)]);
    }

    /**
     * @param string $query
     * @return array
     */
    public function search(string $query): array
    {
        $regex = new Regex($query, 'i');
        $cursor = $this->collection->find(
            ['title' => $regex],
            ['sort' => ['createdAt' => -1]]
        );

        $posts = [];
        foreach ($cursor as $doc){
            $posts[] = $this->docToArray($doc);
        }
        return $posts;
    }

    //Aggregation for category counts
    public function countByCategory(): array
    {
        $pipeline = [
            ['$group' => [
                '_id' => '$category',
                'count' => ['$sum' => 1]
            ]],
            ['$sort' => ['count' => -1]]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        
        $results = [];
        foreach($cursor as $row) {
            $results[] = [
                'category' => $row['_id'] ?? 'Uncategorized',
                'count' => $row['count']
            ];
        }
        return $results;
    }

    /**
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getPostsByDateRange(string $startDate, string $endDate): array
    {
        $start = new UTCDateTime(strtotime($startDate) * 1000);
        $end = new UTCDateTime(strtotime($endDate) * 1000);

        $pipeline = [
            ['$match' => [
                'createdAt' => ['$gte' => $start, '$lte' => $end]
            ]],
            ['$group' => [
                '_id' => ['$dateToString' => ['format' => '%Y-%m-%d', 'date' => '$createdAt']],
                'count' => ['$sum' => 1]
            ]],
            ['$sort' => ['_id' => 1]]
        ];

        $cursor = $this->collection->aggregate($pipeline);
        
        $results = [];
        foreach ($cursor as $row){
            $results[] = [
                'date' => $row['_id'],
                'count' => $row['count']
            ];
        }
        return $results;
    }

    //Create indexes for query optimization
    public function ensureIndexes(): void
    {
        // Index on title for search queries
        $this->collection->createIndex(['title' => 'text']);
        
        // Index on category for filtering
        $this->collection->createIndex(['category' => 1]);
        
        // Index on createdAt for sorting and date range queries
        $this->collection->createIndex(['createdAt' => -1]);
    }

    private function docToArray($doc): array
    {
        $createdAt = $doc['createdAt'] ?? null;
        $dateStr = '';
        if ($createdAt instanceof UTCDateTime) {
            $dateStr = $createdAt->toDateTime()->format('M j, Y');
        }

        return [
            'id' => (string) $doc['_id'],
            'title' => $doc['title'] ?? '',
            'content' => $doc['content'] ?? '',
            'category' => $doc['category'] ?? '',
            'createdAt' => $dateStr
        ];
    }
}
