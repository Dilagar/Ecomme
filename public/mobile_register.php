<?php
require_once __DIR__ . '/../lib/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$linkingMobile = user_logged_in();
$next = get_param('next');
$error = '';
$success = '';

if (is_post()) {
    $phone = post_param('phone');
    $password = post_param('password');
    $confirm = post_param('confirm');

    if (empty($phone) || empty($password) || empty($confirm)) {
        $error = 'Mobile, password and confirm password are required';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match';
    } else {
        if ($linkingMobile) {
            $res = user_link_mobile($_SESSION['user_id'], $phone, $password);
            if ($res === true) {
                $success = 'Mobile number linked successfully.';
                if ($next === 'index') {
                    redirect('/Ecomme/public/index.php');
                }
                redirect('/Ecomme/public/dashboard.php');
            } else {
                $error = $res;
            }
        } else {
            $name = 'Mobile User';
            $res = user_register_mobile($name, $phone, $password);
            if ($res === true) {
                // Auto-login
                if (user_login_mobile($phone, $password)) {
                    redirect('/Ecomme/public/index.php');
                }
                $success = 'Registration successful. You can now login.';
            } else {
                $error = $res;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $linkingMobile ? 'Link Mobile Number' : 'Mobile Registration'; ?></title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="container" style="max-width:520px;margin:40px auto;">
    <div class="card">
        <h2><?php echo $linkingMobile ? 'Link Your Mobile Number' : 'Create Account with Mobile'; ?></h2>
        <p style="color:#555;">
            <?php echo $linkingMobile ? 'Add a mobile number and password to sign in without Google next time.' : 'Register with your mobile number and set a password.'; ?>
        </p>
        <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="phone">Mobile Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your mobile number" pattern="[0-9]{10,15}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" minlength="8" required>
            </div>
            <div class="form-group">
                <label for="confirm">Confirm Password</label>
                <input type="password" id="confirm" name="confirm" minlength="8" required>
            </div>
            <button type="submit" class="btn"><?php echo $linkingMobile ? 'Link Mobile' : 'Register'; ?></button>
        </form>
        <div style="margin-top:15px;">
            <a href="/Ecomme/public/mobile_login.php">Already have mobile login? Sign in</a>
        </div>
    </div>
</div>
</body>
</html>


