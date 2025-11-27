<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

$db = new Database();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$searchQuery = $_GET['q'] ?? '';

if ($searchQuery) {
    $posts = $db->search($searchQuery);
    $total = count($posts);
    $totalPages = 1;
} else {
    $result = $db->getAll($page, 5);
    $posts = $result['posts'];
    $total = $result['total'];
    $totalPages = ceil($total / 5);
}

$categories = $db->countByCategory();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimpleBlog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>SimpleBlog</h1>
            <a href="create.php" class="btn">+ New Post</a>
        </header>

        <form class="search-form" method="GET">
            <input type="text" name="q" placeholder="Search by title..." value="<?= htmlspecialchars($searchQuery) ?>">
            <button type="submit">Search</button>
        </form>

        <div class="main-layout">
            <main>
                <?php if (empty($posts)): ?>
                    <p>No posts found.</p>
                <?php else: ?>
                    <?php foreach($posts as $post): ?>
                    <article class="post-card">
                        <h2><a href="view.php?id=<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h2>
                        <p class="meta">
                            <span class="category"><?= htmlspecialchars($post['category']) ?></span>
                            &bull; <?= $post['createdAt'] ?>
                        </p>
                        <p><?= htmlspecialchars(substr($post['content'], 0, 150)) ?>...</p>
                    </article>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if ($totalPages > 1 && !$searchQuery): ?>
                <div class="pagination">
                    <?php if($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>">&laquo; Prev</a>
                    <?php endif; ?>
                    
                    <span>Page <?= $page ?> of <?= $totalPages ?></span>
                    
                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </main>

            <aside>
                <h3>Categories</h3>
                <ul>
                <?php foreach($categories as $cat): ?>
                    <li><?= htmlspecialchars($cat['category']) ?> (<?= $cat['count'] ?>)</li>
                <?php endforeach; ?>
                </ul>
            </aside>
        </div>
    </div>
</body>
</html>
