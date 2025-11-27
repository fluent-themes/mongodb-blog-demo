<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

$db = new Database();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($title && $content && $category) {
        $db->create([
            'title' => $title,
            'content' => $content,
            'category' => $category
        ]);
        header('Location: index.php');
        exit;
    } else {
        $error = 'All fields are required.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post - SimpleBlog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><a href="index.php">SimpleBlog</a></h1>
        </header>

        <h2>Create New Post</h2>

        <?php if($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="post-form">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">

            <label for="category">Category</label>
            <input type="text" id="category" name="category" required value="<?= htmlspecialchars($_POST['category'] ?? '') ?>">

            <label for="content">Content</label>
            <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($_POST['content'] ?? '') ?></textarea>

            <button type="submit" class="btn">Create Post</button>
        </form>

        <a href="index.php">&laquo; Cancel</a>
    </div>
</body>
</html>
