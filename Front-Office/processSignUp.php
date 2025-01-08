<?php
session_start();
require_once '../config/Database.php';
require_once '../src/User.php';

use App\Config\Database;
use App\Src\User;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash passworde
        $cost = [12];
        $passwordHash = password_hash($password, PASSWORD_DEFAULT, $cost);

        $pdo = Database::connect();

        $user = new User($pdo);
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPasswordHash($passwordHash);

        try {
            if ($user->save()) {
                $_SESSION['message'] = 'Welcome! You Are Now Regitered With Us.';
                header("Location: succesSignUp.php");
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'Ther Have been An Error In you Sign-Up : ' . $e->getMessage();
            header("Location: UserSingUp.php");
            exit;
        }
    } else {
        $_SESSION['error'] = 'All the Fields Are Required';
        header("Location: UserSingUp.php");
        exit;
    }
} else {
    $_SESSION['error'] = 'Access Denied';
    header("Location: UserSingUp.php");
    exit;
}
