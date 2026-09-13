<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLogin() {
    if (!isset($_SESSION['userID'])) {
        header("Location: login.php");
        exit();
    }
}

function isAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
        header("Location: index.php");
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isset($_SESSION['userID'])) {
        if ($_SESSION['role'] === 'Admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();
    }
}

function getUserRole() {
    return $_SESSION['role'] ?? 'Guest';
}

function getUsername() {
    return $_SESSION['username'] ?? 'Guest';
}
?>

