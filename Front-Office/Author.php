<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../admin/crud_articles.php';
require_once __DIR__ . '/crud_user.php';

// Check if user is logged in and is an author
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'author') {
    header('Location: UserSignUp.php');
    exit();
}

// Get categories and tags
$article = new Src\Article();
$categories = $article->getCategories();
$tags = $article->getTags();

use App\Config\Database;

try {
    $pdo = Database::connect();
    
    // Get all articles
    $sql = "SELECT a.id AS article_id, a.title, a.featured_image, a.content, 
            a.created_at, a.views, c.name AS category_name, 
            GROUP_CONCAT(t.name) AS tags
            FROM articles a
            JOIN categories c ON a.category_id = c.id
            LEFT JOIN article_tags at ON a.id = at.article_id
            LEFT JOIN tags t ON at.tag_id = t.id
            GROUP BY a.id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $allArticles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get author's articles using session author_id
    $authorSql = "SELECT a.id AS article_id, a.title, a.featured_image, a.content, 
                  a.created_at, a.views, c.name AS category_name, 
                  GROUP_CONCAT(t.name) AS tags
                  FROM articles a
                  JOIN categories c ON a.category_id = c.id
                  LEFT JOIN article_tags at ON a.id = at.article_id
                  LEFT JOIN tags t ON at.tag_id = t.id
                  WHERE a.author_id = :author_id
                  GROUP BY a.id";
    
    $authorStmt = $pdo->prepare($authorSql);
    $authorStmt->execute(['author_id' => $_SESSION['author_id']]);
    $authorArticles = $authorStmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error fetching articles: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Author Dashboard | Dev blog</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.5rem;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-900 min-h-screen">

<!-- Add this right after the <body> tag -->
<?php
if (isset($_SESSION['success'])) {
    echo '<div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 bg-green-500 text-white px-6 py-3 rounded-lg">' 
         . htmlspecialchars($_SESSION['success']) . 
         '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 bg-red-500 text-white px-6 py-3 rounded-lg">' 
         . htmlspecialchars($_SESSION['error']) . 
         '</div>';
    unset($_SESSION['error']);
}
?>

    <!-- Navigation Bar -->
    <header class="bg-gray-800 shadow-lg fixed w-full z-50">
        <nav class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button id="hamburger" 
                            class="text-white material-symbols-rounded p-2 hover:bg-gray-700 rounded-lg transition-colors" 
                            onclick="toggleSidebar()">menu
                    </button>
                    <a href="#" class="flex items-center space-x-2">
                        <img src="./assets/images/logo.jpg" alt="logo" class="h-10 w-10 rounded-full">
                        <h2 class="text-white text-xl font-bold">Blog</h2>
                    </a>
                </div>

                <ul class="hidden lg:flex items-center space-x-8 text-gray-300">
                    <li><a href="#" class="hover:text-white transition">Home</a></li>
                    <li><a href="#" class="hover:text-white transition">Articles</a></li>
                    <li><a href="#" class="hover:text-white transition">About us</a></li>
                    <li><a href="#" class="hover:text-white transition">Contact us</a></li>
                </ul>

                <div class="flex items-center space-x-2">
    <p class="text-white"><?= htmlspecialchars($_SESSION['username']) ?></p>
    <img src="../admin/img/profile2.svg" alt="Profile Picture" class="h-8 w-8 rounded-full">
</div>
                    <a href="UserSingUp.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Log out
                    </a>
                </div>
            </div>
        </nav>
    </header>


    <!-- Main Content with Sidebar -->
    <div class="flex pt-16">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-gray-800 text-white fixed h-full p-6 transition-transform">
            <div class="flex flex-col items-center space-y-6">
                <img src="../admin/img/profile2.svg" alt="Profile Picture" class="w-24 h-24 rounded-full border-4 border-indigo-600">
                <div class="space-y-4 mt-6">
                    <button onclick="showAddArticleModal()" class="block sidebar-link w-full text-left px-4 py-2 hover:bg-indigo-600 rounded transition-colors">Add Article</button>
                    <button onclick="toggleArticles(true)" class="block sidebar-link w-full text-left px-4 py-2 hover:bg-indigo-600 rounded transition-colors">See My Articles</button>
                    <button onclick="toggleArticles(false)" class="block sidebar-link w-full text-left px-4 py-2 hover:bg-indigo-600 rounded transition-colors">All Articles</button>
                </div>
            </div>
        </div>

                <!-- Articles Content -->
                <div class="ml-64 p-6 w-full">
            <!-- All Articles Grid -->
            <div id="allArticlesGrid" class="hidden">
                <h2 class="text-2xl font-bold text-white mb-6">All Articles</h2>
                <?php if (empty($allArticles)): ?>
                    <div class="bg-gray-800 p-6 rounded-lg text-center">
                        <p class="text-white text-lg">No articles found.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($allArticles as $article): ?>
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                                <img src="<?= htmlspecialchars($article['featured_image'] ?? './assets/placeholder.jpg') ?>" 
                                     alt="Article Image" 
                                     class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="text-white font-bold text-xl mb-2"><?= htmlspecialchars($article['title']) ?></h3>
                                    <p class="text-gray-400 text-sm mb-2">
                                        Category: <?= htmlspecialchars($article['category_name']) ?>
                                    </p>
                                    <p class="text-gray-400 text-sm mb-4">
                                        Tags: <?= htmlspecialchars($article['tags'] ?? 'No tags') ?>
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 text-sm">
                                            <?= date('F j, Y', strtotime($article['created_at'])) ?>
                                        </span>
                                        <span class="text-gray-500 text-sm">
                                            Views: <?= $article['views'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- My Articles Grid -->
            <div id="myArticlesGrid" class="hidden">
                <h2 class="text-2xl font-bold text-white mb-6">My Articles</h2>
                <?php if (empty($authorArticles)): ?>
                    <div class="bg-gray-800 p-6 rounded-lg text-center">
                        <p class="text-white text-lg">You haven't created any articles yet.</p>
                        <button onclick="showAddArticleModal()" 
                                class="mt-4 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                            Create Your First Article
                        </button>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($authorArticles as $article): ?>
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg">
                                <img src="<?= htmlspecialchars($article['featured_image'] ?? './assets/placeholder.jpg') ?>" 
                                     alt="Article Image" 
                                     class="w-full h-48 object-cover">
                                <div class="p-4">
                                    <h3 class="text-white font-bold text-xl mb-2"><?= htmlspecialchars($article['title']) ?></h3>
                                    <p class="text-gray-400 text-sm mb-2">
                                        Category: <?= htmlspecialchars($article['category_name']) ?>
                                    </p>
                                    <p class="text-gray-400 text-sm mb-4">
                                        Tags: <?= htmlspecialchars($article['tags '] ?? 'No tags') ?>
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 text-sm">
                                            <?= date('F j, Y', strtotime($article['created_at'])) ?>
                                        </span>
                                        <span class="text-gray-500 text-sm">
                                            Views: <?= $article['views'] ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

        <!-- Modal for adding article -->
<div class="modal" id="addArticleModal">
    <div class="modal-content bg-gray-800 text-white">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Create New Article</h2>
            <button onclick="hideAddArticleModal()" class="text-gray-400 hover:text-white">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>
        
        <form action="author_articles.php" method="POST" class="space-y-6">
            <div>
                <label for="title" class="block text-sm font-medium mb-2">Title</label>
                <input type="text" 
                       id="title" 
                       name="title" 
                       required
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="content" class="block text-sm font-medium mb-2">Content</label>
                <textarea id="content" 
                          name="content" 
                          rows="6" 
                          required
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label for="excerpt" class="block text-sm font-medium mb-2">Excerpt</label>
                <textarea id="excerpt" 
                          name="excerpt" 
                          rows="3"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label for="meta_description" class="block text-sm font-medium mb-2">Meta Description</label>
                <textarea id="meta_description" 
                          name="meta_description" 
                          rows="2"
                          maxlength="160"
                          class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
            </div>

            <div>
                <label for="featured_image" class="block text-sm font-medium mb-2">Featured Image URL</label>
                <input type="url" 
                       id="featured_image" 
                       name="featured_image"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium mb-2">Category</label>
                <select id="category_id" 
                        name="category_id" 
                        required
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= htmlspecialchars($category['id']) ?>">
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Tags</label>
                <div class="grid grid-cols-2 gap-2">
                    <?php foreach ($tags as $tag): ?>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" 
                                   name="tags[]" 
                                   value="<?= htmlspecialchars($tag['id']) ?>"
                                   class="rounded bg-gray-700 border-gray-600">
                            <span><?= htmlspecialchars($tag['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div>
                <label for="scheduled_date" class="block text-sm font-medium mb-2">Scheduled Date</label>
                <input type="datetime-local" 
                       id="scheduled_date" 
                       name="scheduled_date"
                       class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" 
                        onclick="hideAddArticleModal()"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg transition-colors">
                    Cancel
                </button>
                <button type="submit" 
                        name="add_article"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors">
                    Create Article
                </button>
            </div>
        </form>
    </div>
</div>
<script>
        function showAddArticleModal() {
            document.getElementById('addArticleModal').classList.add('show');
        }

        function hideAddArticleModal() {
            document.getElementById('addArticleModal').classList.remove('show');
        }

        function toggleArticles(showMyArticles) {
            const allArticlesGrid = document.getElementById('allArticlesGrid');
            const myArticlesGrid = document.getElementById('myArticlesGrid');
            
            if (showMyArticles) {
                allArticlesGrid.classList.add('hidden');
                myArticlesGrid.classList.remove('hidden');
            } else {
                allArticlesGrid.classList.remove('hidden');
                myArticlesGrid.classList.add('hidden');
            }
        }

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                hideAddArticleModal();
            }
        }
    </script>
</body>
</html>