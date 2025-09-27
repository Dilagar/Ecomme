<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/helpers.php';

function admin_logged_in() {
    return !empty($_SESSION['admin_id']);
}

function require_admin() {
    if (!admin_logged_in()) {
        redirect('/Ecomme/admin/index.php');
    }
}

function admin_login($email, $password) {
    global $conn;
    $email_safe = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT * FROM admins WHERE email='" . $email_safe . "' LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($res && mysqli_num_rows($res) === 1) {
        $admin = mysqli_fetch_assoc($res);
        // Plaintext comparison as requested (no hashing)
        if ($password === $admin['password']) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];
            return true;
        }
    }
    return false;
}

function admin_logout() {
    unset($_SESSION['admin_id'], $_SESSION['admin_email']);
}

function user_logged_in() {
    return !empty($_SESSION['user_id']);
}

function require_user() {
    if (!user_logged_in()) {
        redirect('/Ecomme/public/login.php');
    }
}

function user_register($name, $email, $password) {
    global $conn;
    $name_s = mysqli_real_escape_string($conn, $name);
    $email_s = mysqli_real_escape_string($conn, $email);
    $exists = mysqli_query($conn, "SELECT id FROM users WHERE email='".$email_s."' LIMIT 1");
    if ($exists && mysqli_num_rows($exists) > 0) {
        return 'Email already registered';
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $hash_s = mysqli_real_escape_string($conn, $hash);
    $ok = mysqli_query($conn, "INSERT INTO users (name, email, password_hash) VALUES ('{$name_s}','{$email_s}','{$hash_s}')");
    if ($ok) { return true; }
    return 'Registration failed';
}

function user_login($email, $password) {
    global $conn;
    $email_s = mysqli_real_escape_string($conn, $email);
    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='".$email_s."' LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);
        if (!empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
    }
    return false;
}

function user_logout() {
    unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name']);
}

?>

