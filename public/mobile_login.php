<?php
require_once __DIR__ . '/../lib/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (user_logged_in()) { redirect('/Ecomme/public/index.php'); }

$error = '';
if (is_post()) {
    $phone = post_param('phone');
    $password = post_param('password');
    if (!empty($phone) && !empty($password)) {
        if (user_login_mobile($phone, $password)) {
            redirect('/Ecomme/public/index.php');
        } else {
            $error = 'Invalid mobile number or password';
        }
    } else {
        $error = 'Please enter mobile number and password';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mobile Login</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="container" style="max-width:480px;margin:40px auto;">
    <div class="card">
        <h2>Mobile Login</h2>
        <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="phone">Mobile Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your mobile number" pattern="[0-9]{10,15}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div style="margin-top:15px;">
            <a href="/Ecomme/public/mobile_register.php">Create mobile account</a>
        </div>
    </div>
</div>
</body>
</html>


