<?php
require_once '../src/Article.php';
require_once '../config/Database.php';

use Src\Article;
use App\Config\Database;

$article = new Article();

//show the accepted Articles
try {
    $pdo = Database::connect();
    $sql = "SELECT a.id AS article_id, a.title, a.slug, a.content, a.featured_image, a.excerpt,a.status, 
            a.meta_description, DATE(a.created_at) AS created_at, a.views, 
            c.name AS category_name, 
            COALESCE(GROUP_CONCAT(t.name), '') AS tags
            FROM articles a
            JOIN categories c ON a.category_id = c.id
            LEFT JOIN article_tags at ON a.id = at.article_id
            LEFT JOIN tags t ON at.tag_id = t.id
            WHERE a.status = 'accepte'
            GROUP BY a.id ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des articles : " . $e->getMessage());
}

// CREATE AN ARTICLE
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_article'])) {
    $article->setTitle($_POST['title']);
    $article->setContent($_POST['content']);
    $article->setExcerpt($_POST['excerpt']);
    $article->setMetaDescription($_POST['meta_description']);
    $article->setCategoryId($_POST['category_id']);
    $article->setScheduledDate($_POST['scheduled_date']);
    $article->setAuthorId($_SESSION['author_id']); // Add the author ID
    $article->setStatus('soumis'); // Set initial status as submitted

    if (isset($_POST['featured_image']) && !empty($_POST['featured_image'])) {
        $imageUrl = $_POST['featured_image'];

        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            $article->setFeaturedImage($imageUrl);
        } else {
            echo "L'URL de l'image est invalide.";
            exit;
        }
    }

    $tagIds = isset($_POST['tags']) ? $_POST['tags'] : [];

    if ($article->create($tagIds)) {
        header("Location: /Dev.to_Blogging_Plateform/admin/articles.php");
        exit;
    } else {
        echo "Erreur lors de la création de l'article.";
    }
}


/* if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_article'])) {
    try {
        // Validate required fields
        $requiredFields = ['title', 'content', 'category_id'];
        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                $errors[] = ucfirst($field) . " is required.";
            }
        }

        if (!empty($errors)) {
            // Store errors in session
            $_SESSION['article_errors'] = $errors;
            header("Location: author_dashboard.php");
            exit;
        }

        // Prepare article data
        $articleData = [
            'title' => $_POST['title'],
            'content' => $_POST['content'],
            'excerpt' => $_POST['excerpt'] ?? '',
            'meta_description' => $_POST['meta_description'] ?? '',
            'category_id' => $_POST['category_id'],
            'featured_image' => $_POST['featured_image'] ?? null,
            'scheduled_date' => !empty($_POST['scheduled_date']) ? $_POST['scheduled_date'] : null,
            'tags' => $_POST['tags'] ?? [],
            'user_id' => $_SESSION['user_id']
        ];

        // Set article properties
        $article->setTitle($articleData['title']);
        $article->setContent($articleData['content']);
        $article->setExcerpt($articleData['excerpt']);
        $article->setMetaDescription($articleData['meta_description']);
        $article->setCategoryId($articleData['category_id']);
        $article->setScheduledDate($articleData['scheduled_date']);
        $article->setStatus('soumis');
        $article->setUserId($articleData['user_id']);

        // Set featured image if provided
        if ($articleData['featured_image']) {
            $article->setFeaturedImage($articleData['featured_image']);
        }

        // Create article and handle tags
        $result = $article->create($articleData['tags']);

        if ($result) {
            // Success message
            $_SESSION['success_message'] = "Article created successfully and submitted for review.";
            header("Location: author_dashboard.php");
            exit;
        } else {
            // Error message
            $_SESSION['error_message'] = "Failed to create article. Please try again.";
            header("Location: author_dashboard.php");
            exit;
        }
    } catch (Exception $e) {
        // Log the error and show a generic error message
        error_log("Article creation error: " . $e->getMessage());
        $_SESSION['error_message'] = "An unexpected error occurred. Please try again.";
        header("Location: author_dashboard.php");
        exit;
    }
}


// DELETE
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $articleId = (int)$_GET['id'];
    if ($articleId > 0) {
        if ($article->delete($articleId)) {
            header('Location: articles.php');
            exit;
        } else {
            echo "Erreur lors de la suppression de l'article.";
        }
    }
} */

// UPDATE
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_article'])) {
    $article->setId($_POST['id']);
    $article->setTitle($_POST['title_edit'] ?? null);
    $article->setContent($_POST['content_edit'] ?? null);
    $article->setExcerpt($_POST['excerpt_edit'] ?? '');
    $article->setMetaDescription($_POST['meta_description_edit'] ?? '');
    $article->setCategoryId($_POST['category_id'] ?? null);
    $article->setScheduledDate($_POST['scheduled_date'] ?? null);

    if (isset($_POST['featured_image']) && filter_var($_POST['featured_image'], FILTER_VALIDATE_URL)) {
        $article->setFeaturedImage($_POST['featured_image']);
    }

    $tagIds = $_POST['tags'] ?? [];

    // validation 
    if (!$article->getTitle() || !$article->getContent() || !$article->getCategoryId()) {
        echo "Les champs obligatoires sont manquants.";
        exit;
    }

    // the Update
    if ($article->update($tagIds)) {
        header("Location: /Dev.to_Blogging_Plateform/admin/articles.php");
        exit;
    } else {
        echo "Erreur lors de la mise à jour de l'article.";
    }
}

//show the articles 
try {
    $pdo = Database::connect();
    $sql = "SELECT a.id AS article_id, a.title, a.slug, a.content, a.featured_image, a.excerpt,a.status, 
            a.meta_description, DATE(a.created_at) AS created_at, a.views, 
            c.name AS category_name, 
            COALESCE(GROUP_CONCAT(t.name), '') AS tags
            FROM articles a
            JOIN categories c ON a.category_id = c.id
            LEFT JOIN article_tags at ON a.id = at.article_id
            LEFT JOIN tags t ON at.tag_id = t.id
            WHERE a.status = 'soumis'
            GROUP BY a.id ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $articlesSoumis = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur lors de la récupération des articles : " . $e->getMessage());
}
