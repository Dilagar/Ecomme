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
        .admin-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .admin-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .admin-header .subtitle {
            color: #666;
            font-size: 14px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .admin-login-btn {
            width: 100%;
            padding: 12px;
            background-color: #4a69bd;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .admin-login-btn:hover {
            background-color: #3c58a8;
        }
        .back-to-store {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-store a {
            color: #4a69bd;
            text-decoration: none;
        }
        .back-to-store a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="admin-login-container">
    <div class="admin-header">
        <h2>Admin Login</h2>
        <div class="subtitle">Access your store management dashboard</div>
    </div>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required placeholder="Enter your email">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter your password">
        </div>
        <button type="submit" class="admin-login-btn">Login to Dashboard</button>
    </form>
    <!-- <div class="back-to-store">
        <a href="/Ecomme/public/index.php">← Back to Store</a>
    </div> -->
</div>
</body>
</html>


