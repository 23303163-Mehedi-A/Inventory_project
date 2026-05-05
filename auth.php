<?php
if (!isset($_SESSION)) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /inventory_project/login.php");
        exit;
    }
}

function require_admin() {
    if ($_SESSION['role'] !== 'admin') {
        header("Location: /inventory_project/dashboard.php");
        exit;
    }
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function is_staff() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'staff';
}
?>