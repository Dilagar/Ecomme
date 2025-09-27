<?php
require_once __DIR__ . '/../lib/auth.php';

if (user_logged_in()) { redirect('/Ecomme/public/dashboard.php'); }

$error = '';
$success = '';
if (is_post()) {
    $name = post_param('name');
    $email = post_param('email');
    $password = post_param('password');
    $confirm = post_param('confirm');
    if ($password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        $res = user_register($name, $email, $password);
        if ($res === true) {
            $success = 'Registration successful. You can now login.';
        } else {
            $error = $res;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>User Register</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>
<div class="container" style="max-width:520px;margin:40px auto;">
    <div class="card">
        <h2>Register</h2>
        <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
        <form method="post">
            <label>Name</label>
            <input type="text" name="name" required>
            <label>Email</label>
            <input type="email" name="email" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <label>Confirm Password</label>
            <input type="password" name="confirm" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="/Ecomme/public/login.php">Login</a></p>
    </div>
</div>
</body>
</html>


