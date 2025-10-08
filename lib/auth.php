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
        
        // Verify password with password_hash
        if (isset($admin['password_hash']) && !empty($admin['password_hash'])) {
            if (password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                return true;
            }
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

function user_register($name, $email, $password, $phone = null) {
    global $conn;
    $name_s = mysqli_real_escape_string($conn, $name);
    $email_s = mysqli_real_escape_string($conn, $email);
    $phone_s = $phone ? mysqli_real_escape_string($conn, $phone) : 'NULL';
    $exists = mysqli_query($conn, "SELECT id FROM users WHERE email='".$email_s."' LIMIT 1");
    if ($exists && mysqli_num_rows($exists) > 0) {
        return 'Email already registered';
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $hash_s = mysqli_real_escape_string($conn, $hash);
    $ok = mysqli_query($conn, "INSERT INTO users (name, email, phone, password_hash) VALUES ('{$name_s}','{$email_s}',{$phone_s},'{$hash_s}')");
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
            $_SESSION['user_phone'] = $user['phone'];
            return true;
        }
    }
    return false;
}

function user_login_by_phone($phone, $password_hash) {
    global $conn;
    $phone_s = mysqli_real_escape_string($conn, $phone);
    $password_hash_s = mysqli_real_escape_string($conn, $password_hash);
    $res = mysqli_query($conn, "SELECT * FROM users WHERE phone='".$phone_s."' AND password_hash='".$password_hash_s."' LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_phone'] = $user['phone'];
        $_SESSION['logged_in'] = true;
        return true;
    }
    return false;
}

function user_logout() {
    unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name'], $_SESSION['user_phone'], $_SESSION['logged_in']);
}

?>

