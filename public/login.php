<?php
require_once __DIR__ . '/../lib/auth.php';

if (user_logged_in()) { redirect('/Ecomme/public/index.php'); }

$error = '';
if (is_post()) {
    $email = post_param('email');
    $phone = post_param('phone');
    $password = post_param('password');
    $password_hash = post_param('password_hash');
    
    if (!empty($phone) && !empty($password)) {
        // Mobile number + password login
        if (user_login_mobile($phone, $password)) {
            redirect('/Ecomme/public/index.php');
        } else {
            $error = 'Invalid mobile number or password';
        }
    } else if (!empty($phone) && !empty($password_hash)) {
        // Phone + password_hash login (legacy)
        if (user_login_by_phone($phone, $password_hash)) {
            redirect('/Ecomme/public/index.php');
        } else {
            $error = 'Invalid phone or password hash';
        }
    } else if (!empty($email) && !empty($password)) {
        // Email + password login
        if (user_login($email, $password)) {
            redirect('/Ecomme/public/index.php');
        } else {
            $error = 'Invalid credentials';
        }
    } else {
        $error = 'Please provide either email/password or mobile number/password';
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
        <!-- <h2>Login</h2>
        <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
        <form method="post">
            <div style="margin-bottom: 20px;">
                <label>Login with Email or Mobile Number</label>
                <input type="text" name="email" placeholder="Email or Mobile Number" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login</button>
        </form> -->
        
        <hr style="margin: 20px 0;">
        <h3 style="text-align: center; margin-bottom: 15px;">Login with Mobile Number</h3>
        <form method="post">
            <div style="margin-bottom: 20px;">
                <label>Mobile Number</label>
                <input type="tel" name="phone" placeholder="Enter your mobile number" required>
            </div>
            <div style="margin-bottom: 20px;">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button type="submit">Login with Mobile</button>
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
                $client->setScopes(GOOGLE_SCOPES);
                
                // Create the Google login URL
                $google_login_url = $client->createAuthUrl();
                $google_enabled = true;
            } catch (Exception $e) {
                // Google OAuth is not properly configured
                $google_enabled = false;
            }
        }
        ?>
		<div class="social-login">
			<?php if ($google_enabled): ?>
			<a href="<?php echo $google_login_url; ?>" 
			   class="google-btn" 
			   style="display:flex;align-items:center;justify-content:center;padding:10px;background:#fff;color:#757575;text-decoration:none;border-radius:4px;margin-bottom:10px;border:1px solid #ddd;font-weight:500;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
				<img src="https://developers.google.com/identity/images/g-logo.png" alt="Google logo" style="width:18px;height:18px;margin-right:10px;">
				Sign in with Google
			</a>
			<?php else: ?>
			<p style="color:#a00;margin:10px 0;">Google login is not configured. Please set credentials in <code>config/google_auth.php</code>.</p>
			<?php endif; ?>
		</div>
    </div>
    
</div>
</body>
</html>


