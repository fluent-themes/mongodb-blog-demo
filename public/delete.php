<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Database;

$db = new Database();

$id = $_GET['id'] ?? '';
if ($id) {
    $db->delete($id);
}

header('Location: index.php');
exit;
