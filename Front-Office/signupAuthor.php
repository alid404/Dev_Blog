<?php
session_start();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../admin/crud_articles.php';
require_once __DIR__ . '/crud_user.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login and Registration Form | Dev blog</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@48,400,0,0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- <link rel="stylrsheet" href="assets/css/styleLogin.css"> -->
    <style>
    .sidebar-link {
        padding: 10px 15px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .sidebar-link:hover {
        background-color: #4c51bf;
        transform: scale(1.05);
        color: white;
    }

    #sidebar {
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        z-index: 50;
    }

    #sidebar.active {
        transform: translateX(0);
    }

    #mobile-menu-overlay {
        z-index: 40;
        transition: opacity 0.3s ease-in-out;
    }

    main {
        transition: all 0.3s ease-in-out;
    }

    main.active {
        margin-left: 0;
    }
</style>
</head>

<body class="bg-gray-900 min-h-screen flex">

    <!-- Sidebar -->
    <div id="sidebar" class="w-64 bg-gray-800 text-white fixed h-full p-6 transition-transform">
        <div class="flex flex-col items-center space-y-6">
            <!-- Profile Image -->
            <img src="../admin/img/profile2.svg" alt="Profile Picture" class="w-24 h-24 rounded-full border-4 border-indigo-600">
            <p class="text-xl font-semibold"><?= htmlspecialchars($_SESSION['author3']) ?></p>
            
            <!-- Sidebar Links -->
            <div class="space-y-4 mt-6">
                <a href="add_article.php" class="block sidebar-link">Add Article</a>
                <a href="my_articles.php" class="block sidebar-link">See My Articles</a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="ml-0 w-full p-12 transition-all">
        <header class="bg-gray-800 shadow-lg">
            <nav class="container mx-auto px-4 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Hamburger Menu -->
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

                    <div class="flex items-center space-x-4">
                        <?php if (isset($_SESSION['username'])): ?>
                            <div class="flex items-center space-x-2">
                                <p class="text-white"><?= htmlspecialchars($_SESSION['username']) ?></p>
                                <img src="../admin/img/profile2.svg" alt="Profile Picture" class="h-8 w-8 rounded-full">
                            </div>
                        <?php else: ?>
                            <a href="UserSingUp.php" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                                Log out
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </nav>
        </header>

        <main class="container mx-auto px-4 py-12">
            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                <?php foreach ($articles as $article) : ?>
                    <article class="bg-gray-800 rounded-xl shadow-lg overflow-hidden transform hover:scale-105 transition duration-200">
                        <div class="p-4 flex items-center justify-between text-sm text-gray-400">
                            <time datetime="<?= htmlspecialchars($article['created_at']) ?>" class="flex items-center">
                                <span class="material-symbols-rounded mr-1">calendar_today</span>
                                <?= htmlspecialchars($article['created_at']) ?>
                            </time>
                            <span class="bg-indigo-600 text-white text-xs px-3 py-1 rounded-full">
                                <?= htmlspecialchars($article['category_name']) ?>
                            </span>
                        </div>

                        <div class="p-6">
                            <h3 class="text-xl font-bold text-white mb-3">
                                <?= htmlspecialchars($article['title']) ?>
                            </h3>
                            <p class="text-gray-300 mb-4 line-clamp-3">
                                <?= htmlspecialchars($article['content']) ?>
                            </p>
                            <a href="#" class="inline-flex items-center text-indigo-400 font-medium hover:text-indigo-300 transition">
                                Read more 
                                <span class="material-symbols-rounded ml-1">arrow_forward</span>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </main>
    </main>

    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden" id="mobile-menu-overlay"></div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('mobile-menu-overlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('hidden');
        const hamburger = document.getElementById('hamburger');
        const isExpanded = sidebar.classList.contains('active');
        hamburger.setAttribute('aria-expanded', isExpanded);
    }

    document.getElementById('mobile-menu-overlay').addEventListener('click', toggleSidebar);
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && document.getElementById('sidebar').classList.contains('active')) {
            toggleSidebar();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 1024 && document.getElementById('sidebar').classList.contains('active')) {
            toggleSidebar();
        }
    });
</script>
</body>

</html>
