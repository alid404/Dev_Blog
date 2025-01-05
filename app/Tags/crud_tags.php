<?php
require_once '../config/Database.php';
require_once '../src/Tag.php';

use App\Config\Database;
use App\Src\Tag;

$pdo = Database::connect();

try {
    $tagModel = new Tag($pdo);
    $tags = $tagModel->getAllTags();
    if (!$tags) {
        $tags = [];
    }
} catch (Exception $e) {
    $tags = [];
    echo "Erreur : " . $e->getMessage();
}
$tagLabels = array_column($tags, 'name');
$tagCounts = array_column($tags, 'count'); 

// CREATE 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tag_name'])) {
    $tagName = $_POST['tag_name'];
    $tagManager = new Tag($pdo);
    $tagManager->createTag($tagName);
    header("Location: tags.php");
    exit;
}

// UPDATE 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tagEdit_name']) && isset($_POST['tag_id'])) {
    $tagName = $_POST['tagEdit_name'];
    $tagId = $_POST['tag_id'];
    $tagManager = new Tag($pdo);
    $tagManager->updateTag($tagId, $tagName);
    header("Location: tags.php");
    exit;
}

// DELETE 
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $tagManager = new Tag($pdo);
    $tagManager->deleteTag($id);
    header("Location: tags.php");
    exit;
}
