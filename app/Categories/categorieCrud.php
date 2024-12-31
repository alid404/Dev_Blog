<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use config\Database;
use app\categories\Category;

$database = new Database("dev-blog");
$db = $database->getConnection();

$category = new Category($db); 


if ($_SERVER["REQUEST_METHOD"] === "POST") {  // add the category
    if (isset($_POST['name'])) {
        $category_name = htmlspecialchars(strip_tags($_POST['name']));

        $category->name = $category_name;
        if ($category->create()) {
            header("Location: ../../public/pages/categorie.php");
        } else {
            echo "Failed to create category.";
        }
    } else {
        echo "Category name is required.";
    }
} else {
    echo "Invalid request method.";
}


if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {  // delete the category
    $category_id = $_GET['id'];

    if (!empty($category_id)) {
        $category->id = $category_id;
        if ($category->delete()) {
            header("Location: ../../public/pages/categorie.php");
            exit();
        } else {
            echo "Failed to delete category.";
        }
    } else {
        echo "Invalid category ID.";
    }
}

// Fetch category by id for update
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];
    $category->id = $category_id;
    $category_data = $category->readOne(); 
    if ($category_data) {
        $current_name = $category_data['name'];
    } else {
        echo "Category not found!";
        exit();
    }
}

// Update category data
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'])) {
    $category_name = htmlspecialchars(strip_tags($_POST['name']));

    $category->name = $category_name;
    if ($category->update()) {
        header("Location: ../../public/pages/categorie.php");
        exit();
    } else {
        echo "Failed to update category.";
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
            <h1 class="text-2xl font-bold text-gray-100 mb-6">Update Category</h1>
            <form action="../../src/categories/categorieUpdate.php?id=<?= $category_id; ?>" method="POST" class="space-y-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-400">Category Name</label>
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
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>
