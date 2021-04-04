<?php
require_once "env.php";
if (!isset($_SESSION)) {
    session_start();
}

require_once "database.php";

$errors = [];
$email = "";
$password = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    // $type = "a";

    if (!$email) {
        $errors[] = "Email is required";
    }

    if (!$password) {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        if (password_verify($email, $_ENV["EMAIL"]) && password_verify($password, $_ENV["PASSWORD"])) {
            $_SESSION["admin"] = true;
            header("Location: /adrow-time-record/dashboard.php");
            exit;
        } else {
            $errors[] = "Email or Password incorrect";
            $_SESSION["errors"] = $errors;
        }
    } else {
        $_SESSION["errors"] = $errors;
    }
}

header('Location: ' . $_SERVER['HTTP_REFERER']);
?>