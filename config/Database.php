<?php
// File: config/Database.php

namespace App\Config;

use PDO;
use PDOException;

class Database {
    private static $host = "localhost";
    private static $db_name = "devblog_db";
    private static $username = "root";
    private static $password = "";
    private static $conn = null;

    public static function connect() {
        try {
            if (self::$conn === null) {
                self::$conn = new PDO(
                    "mysql:host=" . self::$host . ";dbname=" . self::$db_name,
                    self::$username,
                    self::$password
                );
                self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            }
            return self::$conn;
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
            return null;
        }
    }
}