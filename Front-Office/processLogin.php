<?php
session_start();
require_once '../config/Database.php';
require_once '../src/User.php';

use App\Config\Database;
use App\Src\User;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email'], $_POST['password'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        try {
            $pdo = Database::connect();
            $user = new User($pdo);

            // Fetch user details by email
            $foundUser = $user->findByEmail($email);
            if ($foundUser !== null && password_verify($password, $foundUser['password_hash'])) {
                $_SESSION['user_id'] = $foundUser['id_user'];
                $_SESSION['username'] = $foundUser['username'];
                $_SESSION['role'] = $foundUser['role'];
                if ($foundUser['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php"); 
                    exit;
                } else {
                    header("Location: succesSignUp.php"); 
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Email or Password is incorrect.';
                header("Location: UserSingUp.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Connection Error: ' . htmlspecialchars($e->getMessage());
            header("Location: UserSingUp.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'Fill out all the required fields.';
        header("Location: UserSingUp.php");
        exit;
    }
} else {
    $_SESSION['error'] = 'Access Denied';
    header("Location: UserSingUp.php");
    exit;
}
