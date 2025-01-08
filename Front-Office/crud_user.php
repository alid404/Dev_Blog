<?php
namespace App\Crud;
use App\Config\Database;
use PDO;
use PDOException;

class UserCrud{
    public static function getUsersCount()
    {
        try {
            $connection = Database::connect();
            $query = $connection->query("SELECT COUNT(*) AS total FROM users");
            $result = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            throw new PDOException("$user doesn't exist: " . $e->getMessage());
        }
    }
}

$total_users = UserCrud::getUsersCount();
$sql = "SELECT c.name AS category_name, COUNT(a.id) AS article_count
        FROM categories c
        LEFT JOIN articles a ON c.id = a.category_id
        GROUP BY c.id";
        
$pdo = Database::connect();
$stmt = $pdo->query($sql);
$category_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

