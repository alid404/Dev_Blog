<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use config\Database;
use Src\tags\Tag;

$database = new Database("dev_blog");
$db = $database->getConnection();

$tag = new Tag($db);

// ADD 
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['name'])) {
        $tag_name = htmlspecialchars(strip_tags($_POST['name']));

        $tag->name = $tag_name;
        if ($tag->create()) {
            header("Location: ../../public/pages/tag.php");
        } else {
            echo "Failed to create tag.";
        }
    } else {
        echo "tag name is required.";
    }
} else {
    echo "Invalid request method.";
}

// delete 
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    $tag_id = $_GET['id'];

    if (!empty($tag_id)) {
        $tag->id = $tag_id;
        if ($tag->delete()) {
            header("Location: ../../public/pages/tag.php");
            exit();
        } else {
            echo "Failed to delete tag.";
        }
    } else {
        echo "Invalid tag ID.";
    }
}

// find the tag by its id
if (isset($_GET['id'])) {
    $tag_id = $_GET['id'];
    $tag->id = $tag_id;
    $tag_data = $tag->readOne(); 
    if ($tag_data) {
        $current_name = $tag_data['name'];
    } else {
        echo "Tag not found!";
        exit();
    }
}

// Update 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'])) {
    $tag_name = htmlspecialchars(strip_tags($_POST['name']));

    $tag->name = $tag_name;
    if ($tag->update()) {
        header("Location: ../../public/pages/tag.php");
        exit();
    } else {
        echo "Failed to update tag.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <title>Update Category</title>
</head>
<body>
    <main class="w-full flex-grow p-6">
        <div class="bg-gray-800 shadow-lg rounded-lg w-full max-w-2xl mx-auto p-8">
            <h1 class="text-2xl font-bold text-gray-100 mb-6">Modifier Category</h1>
            <form action="../../src/tags/tagUpdate.php?id=<?= $tag_id; ?>" method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-400">Tag Name</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="w-full mt-1 p-3 bg-gray-700 text-gray-200 border border-gray-600 rounded-md focus:ring focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                        value="<?= isset($current_name) ? htmlspecialchars($current_name) : ''; ?>" 
                        placeholder="Enter category name" 
                        required
                    />
                </div>
                <div>
                    <button 
                        type="submit" 
                        class="w-full bg-yellow-600 text-white py-3 px-4 rounded-md hover:bg-yellow-700 focus:ring focus:ring-yellow-500"
                    >
                        Modifier Tag
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>

