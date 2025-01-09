<?php
require_once __DIR__ . '/../config/Database.php';
use App\Config\Database;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_article'])) {
    session_start();

    // Validate and sanitize inputs
    $title = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $content = filter_input(INPUT_POST, 'content', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $excerpt = filter_input(INPUT_POST, 'excerpt', FILTER_SANITIZE_STRING);
    $metaDescription = filter_input(INPUT_POST, 'meta_description', FILTER_SANITIZE_STRING);
    $featuredImage = filter_input(INPUT_POST, 'featured_image', FILTER_VALIDATE_URL);
    $categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $scheduledDate = $_POST['scheduled_date'] ?? null;
    $tags = $_POST['tags'] ?? [];

    // Validate required fields
    if (empty($title) || empty($content) || empty($categoryId)) {
        $_SESSION['error'] = "Please fill all required fields.";
        header("Location: Author.php");
        exit();
    }

    // Create slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));

    try {
        $pdo = Database::connect();
        $pdo->beginTransaction();

        // Insert article
        $sql = "INSERT INTO articles 
                (title, slug, content, excerpt, meta_description, category_id, 
                featured_image, scheduled_date, author_id) 
                VALUES 
                (:title, :slug, :content, :excerpt, :meta_desc, :category_id, 
                :featured_image, :scheduled_date, :author_id)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title' => $title,
            ':slug' => $slug,
            ':content' => $content,
            ':excerpt' => $excerpt,
            ':meta_desc' => $metaDescription,
            ':category_id' => $categoryId,
            ':featured_image' => $featuredImage,
            ':scheduled_date' => $scheduledDate,
            ':author_id' => $_SESSION['author_id']
        ]);

        $articleId = $pdo->lastInsertId();

        // Insert tags if any
        if (!empty($tags)) {
            $tagSql = "INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)";
            $tagStmt = $pdo->prepare($tagSql);

            foreach ($tags as $tagId) {
                $tagStmt->execute([
                    ':article_id' => $articleId,
                    ':tag_id' => $tagId
                ]);
            }
        }

        $pdo->commit();

        $_SESSION['success'] = "Article created successfully!";
        header("Location: Author.php");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating article: " . $e->getMessage();
        header("Location: Author.php");
        exit();
    }
}