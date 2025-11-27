<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

$db = new Database();
$error = '';

$id = $_GET['id'] ?? '';
if(!$id) {
    header('Location: index.php');
    exit;
}

$post = $db->getById($id);
if (!$post){
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if ($title && $content && $category){
        $db->update($id, [
            'title' => $title,
            'content' => $content,
            'category' => $category
        ]);
        header('Location: view.php?id=' . $id);
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
    <title>Edit Post - SimpleBlog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><a href="index.php">SimpleBlog</a></h1>
        </header>

        <h2>Edit Post</h2>

        <?php if ($error): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="post-form">
            <label for="title">Title</label>
            <input type="text" id="title" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? $post['title']) ?>">

            <label for="category">Category</label>
            <input type="text" id="category" name="category" required value="<?= htmlspecialchars($_POST['category'] ?? $post['category']) ?>">

            <label for="content">Content</label>
            <textarea id="content" name="content" rows="10" required><?= htmlspecialchars($_POST['content'] ?? $post['content']) ?></textarea>

            <button type="submit" class="btn">Update Post</button>
        </form>

        <a href="view.php?id=<?= $id ?>">&laquo; Cancel</a>
    </div>
</body>
</html>
