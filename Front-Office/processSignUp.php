<?php
session_start();
require_once '../config/Database.php';
require_once '../src/User.php';

use App\Config\Database;
use App\Src\User;

/* if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
} */



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

            // Check if the email already exists
            if ($user->findByEmail($email) !== null) {
                $_SESSION['error'] = 'Email is already registered.';
                header("Location: UserSingUp.php");
                exit;
            }

            // Assign a default role (e.g., 'author') and create the user
            $role = 'author'; // Default role
            $user->save();

            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            header("Location: Author.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . htmlspecialchars($e->getMessage());
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




/* if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username'], $_POST['email'], $_POST['password'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        try {
            $pdo = Database::connect();
            $user = new User($pdo);

            // Check if the email already exists
            if ($user->findByEmail($email) !== null) {
                $_SESSION['error'] = 'Email is already registered.';
                header("Location: UserSingUp.php");
                exit;
            }

            // Assign a default role (e.g., 'author') and create the user
            $role = 'author'; // Default role
            $user->create($username, $email, $password, $role);

            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

            header("Location: Author.php");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . htmlspecialchars($e->getMessage());
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
} */

