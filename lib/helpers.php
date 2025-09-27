<?php
session_start();

function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect($path) {
    header('Location: ' . $path);
    exit;
}

function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function get_param($key, $default = '') {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

function post_param($key, $default = '') {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function slugify($text) {
    $text = strtolower(preg_replace('~[^\pL\d]+~u', '-', $text));
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    return $text ?: 'n-a';
}

?>

