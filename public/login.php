<?php
require_once __DIR__ . '/../lib/auth.php';

if (user_logged_in()) { redirect('/Ecomme/public/dashboard.php'); }

$error = '';
if (is_post()) {
    $email = post_param('email');
    $password = post_param('password');
    if (user_login($email, $password)) {
        redirect('/Ecomme/public/dashboard.php');
    } else {
        $error = 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Login</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container" style="max-width:480px;margin:40px auto;">
    <div class="card">
        <h2>Login</h2>
        <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
        <form method="post">
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="/Ecomme/public/register.php">Register</a></p>
        <hr>
        <form method="post" action="#" onsubmit="alert('Google OAuth placeholder'); return false;">
            <button type="submit">Continue with Google</button>
        </form>
    </div>
    
</div>
</body>
</html>


