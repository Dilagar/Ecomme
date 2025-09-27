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
    <title>Admin Login</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="container" style="max-width:420px;margin:60px auto;">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>


