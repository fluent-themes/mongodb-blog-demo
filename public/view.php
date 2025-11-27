<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

$db = new Database();

$id = $_GET['id'] ?? '';
if (!$id) {
    header('Location: index.php');
    exit;
}

$post = $db->getById($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $post ? htmlspecialchars($post['title']) : 'Not Found' ?> - SimpleBlog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><a href="index.php">SimpleBlog</a></h1>
        </header>

        <?php if (!$post): ?>
            <p>Post not found.</p>
            <a href="index.php">&laquo; Back to posts</a>
        <?php else: ?>
            <article class="single-post">
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p class="meta">
                    <span class="category"><?= htmlspecialchars($post['category']) ?></span>
                    &bull; <?= $post['createdAt'] ?>
                </p>
                <div class="content">
                    <?= nl2br(htmlspecialchars($post['content'])) ?>
                </div>
                <div class="actions">
                    <a href="edit.php?id=<?= $post['id'] ?>" class="btn">Edit</a>
                    <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this post?')">Delete</a>
                </div>
            </article>
            <a href="index.php">&laquo; Back to posts</a>
        <?php endif; ?>
    </div>
</body>
</html>
