<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function get_logged_user() {
    return $_SESSION['user'] ?? null;
}

function is_admin() {
    return isset($_SESSION['user']) && $_SESSION['user']['profile'] === 'administrador';
}

function is_atendente() {
    return isset($_SESSION['user']) && ($_SESSION['user']['profile'] === 'atendente' || $_SESSION['user']['profile'] === 'administrador');
}