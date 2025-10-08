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

function user_register_mobile($name, $phone, $password) {
    global $conn;
    
    // Validate mobile number
    if (!validate_mobile_number($phone)) {
        return 'Invalid mobile number format';
    }
    
    // Format mobile number
    $phone = format_mobile_number($phone);
    
    $name_s = mysqli_real_escape_string($conn, $name);
    $phone_s = mysqli_real_escape_string($conn, $phone);
    
    // Check if mobile number already exists
    $exists = mysqli_query($conn, "SELECT id FROM users WHERE phone='".$phone_s."' LIMIT 1");
    if ($exists && mysqli_num_rows($exists) > 0) {
        return 'Mobile number already registered';
    }
    
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $hash_s = mysqli_real_escape_string($conn, $hash);
    $ok = mysqli_query($conn, "INSERT INTO users (name, phone, password_hash) VALUES ('{$name_s}','{$phone_s}','{$hash_s}')");
    if ($ok) { return true; }
    return 'Registration failed';
}

function user_link_mobile($user_id, $phone, $password) {
    global $conn;
    
    $user_id = (int)$user_id;
    if ($user_id <= 0) { return 'Invalid user'; }
    
    if (!validate_mobile_number($phone)) {
        return 'Invalid mobile number format';
    }
    
    $phone = format_mobile_number($phone);
    $phone_s = mysqli_real_escape_string($conn, $phone);
    
    // Ensure the mobile number is not used by another account
    $exists = mysqli_query($conn, "SELECT id FROM users WHERE phone='".$phone_s."' AND id<>".$user_id." LIMIT 1");
    if ($exists && mysqli_num_rows($exists) > 0) {
        return 'Mobile number already in use';
    }
    
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $hash_s = mysqli_real_escape_string($conn, $hash);
    $ok = mysqli_query($conn, "UPDATE users SET phone='".$phone_s."', password_hash='".$hash_s."' WHERE id=".$user_id." LIMIT 1");
    if ($ok) {
        $_SESSION['user_phone'] = $phone;
        return true;
    }
    return 'Failed to link mobile number';
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

function user_login_mobile($phone, $password) {
    global $conn;
    
    // Validate mobile number
    if (!validate_mobile_number($phone)) {
        return false;
    }
    
    // Format mobile number
    $phone = format_mobile_number($phone);
    
    $phone_s = mysqli_real_escape_string($conn, $phone);
    $res = mysqli_query($conn, "SELECT * FROM users WHERE phone='".$phone_s."' LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
        $user = mysqli_fetch_assoc($res);
        if (!empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_phone'] = $user['phone'];
            $_SESSION['logged_in'] = true;
            return true;
        }
    }
    return false;
}

function user_logout() {
    unset($_SESSION['user_id'], $_SESSION['user_email'], $_SESSION['user_name'], $_SESSION['user_phone'], $_SESSION['logged_in']);
}

function validate_mobile_number($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if it's a valid mobile number (10-15 digits)
    if (strlen($phone) < 10 || strlen($phone) > 15) {
        return false;
    }
    
    // Basic mobile number validation
    return preg_match('/^[0-9]{10,15}$/', $phone);
}

function format_mobile_number($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Add country code if not present (assuming +1 for US/Canada, adjust as needed)
    if (strlen($phone) === 10) {
        $phone = '1' . $phone;
    }
    
    return $phone;
}

?>

