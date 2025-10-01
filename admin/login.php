<?php
require_once __DIR__ . '/../lib/auth.php';

if (admin_logged_in()) {
    redirect('/Ecomme/admin/categories.php');
}

$error = '';
if (is_post()) {
    $email = post_param('email');
    $password = post_param('password');
    if (admin_login($email, $password)) {
        redirect('/Ecomme/admin/categories.php');
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Login - MyShop</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
    <style>
        .admin-login-container {
            max-width: 420px;
            margin: 60px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .admin-login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .admin-login-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .admin-login-form .form-group {
            margin-bottom: 20px;
        }
        .admin-login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        .admin-login-form input[type="email"],
        .admin-login-form input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .admin-login-form button {
            width: 100%;
            padding: 12px;
            background-color: #4a6cf7;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .admin-login-form button:hover {
            background-color: #3a5bd9;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-login-header">
            <h1>Admin Login</h1>
            <p>Enter your credentials to access the admin panel</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo e($error); ?></div>
        <?php endif; ?>
        
        <form method="post" class="admin-login-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>