<?php
require_once __DIR__ . '/../lib/auth.php';

if (user_logged_in()) { redirect('/Ecomme/public/index.php'); }

$error = '';
if (is_post()) {
    $email = post_param('email');
    $password = post_param('password');
    if (user_login($email, $password)) {
        redirect('/Ecomme/public/index.php');
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
        <?php
        // Check if Google OAuth is configured
        $google_enabled = false;
        $google_login_url = '#';
        
        if (file_exists(__DIR__ . '/../config/google_auth.php') && file_exists(__DIR__ . '/../vendor/autoload.php')) {
            try {
                require_once __DIR__ . '/../config/google_auth.php';
                require_once __DIR__ . '/../vendor/autoload.php';
                
                // Initialize the Google Client
                $client = new Google_Client();
                $client->setClientId(GOOGLE_CLIENT_ID);
                $client->setClientSecret(GOOGLE_CLIENT_SECRET);
                $client->setRedirectUri(GOOGLE_REDIRECT_URI);
                $client->addScope(GOOGLE_SCOPES);
                
                // Create the Google login URL
                $google_login_url = $client->createAuthUrl();
                $google_enabled = true;
            } catch (Exception $e) {
                // Google OAuth is not properly configured
                $google_enabled = false;
            }
        }
        ?>
        <a href="#" 
           class="google-btn" 
           style="display:block;text-align:center;padding:10px;background:#4285F4;color:white;text-decoration:none;border-radius:4px;margin-bottom:10px;"
           onclick="googleLogin(); return false;">
            Continue with Google
        </a>
        
        <script>
        function googleLogin() {
            <?php if ($google_enabled): ?>
            window.location.href = "<?php echo $google_login_url; ?>";
            <?php else: ?>
            alert("To enable Google login, you need to set up Google OAuth credentials in the config/google_auth.php file. Please replace YOUR_CLIENT_ID and YOUR_CLIENT_SECRET with actual values from the Google Cloud Console.");
            <?php endif; ?>
        }
        </script>
    </div>
    
</div>
</body>
</html>


