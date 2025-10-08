<?php
require_once __DIR__ . '/../lib/auth.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, allow mobile linking mode to proceed; otherwise redirect
$mode = get_param('mode');
$linkingMobile = false;
if (user_logged_in()) {
    if ($mode === 'mobile') {
        $linkingMobile = true;
    } else {
        redirect('/Ecomme/public/index.php');
    }
}

$error = '';
$success = '';
$default_mode = ($mode === 'mobile') ? 'mobile' : 'email';
if (is_post()) {
    $name = post_param('name');
    $email = post_param('email');
    $phone = post_param('phone');
    $password = post_param('password');
    $confirm = post_param('confirm');
    $registration_type = post_param('registration_type', $default_mode);
    
    // Enhanced validation
    if ($linkingMobile) {
        // Linking mobile to existing logged-in account
        if (empty($phone) || empty($password) || empty($confirm)) {
            $error = 'Mobile, password and confirm password are required';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match';
        } else {
            $res = user_link_mobile($_SESSION['user_id'], $phone, $password);
            if ($res === true) {
                $success = 'Mobile number linked successfully.';
                redirect('/Ecomme/public/dashboard.php');
            } else {
                $error = $res;
            }
        }
    } else {
        if (empty($name) || empty($password) || empty($confirm)) {
            $error = 'Name, password and confirm password are required';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters long';
        } elseif ($password !== $confirm) {
            $error = 'Passwords do not match';
        } elseif ($registration_type === 'email' && (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))) {
            $error = 'Valid email is required for email registration';
        } elseif ($registration_type === 'mobile' && empty($phone)) {
            $error = 'Mobile number is required for mobile registration';
        } else {
            if ($registration_type === 'mobile') {
                // Mobile registration
                $res = user_register_mobile($name, $phone, $password);
                if ($res === true) {
                    $success = 'Mobile registration successful. You can now login.';
                    // Auto-login after registration
                    if (user_login_mobile($phone, $password)) {
                        redirect('/Ecomme/public/index.php');
                    }
                } else {
                    $error = $res;
                }
            } else {
                // Email registration
                $res = user_register($name, $email, $password);
                if ($res === true) {
                    $success = 'Email registration successful. You can now login.';
                    // Auto-login after registration
                    if (user_login($email, $password)) {
                        redirect('/Ecomme/public/index.php');
                    }
                } else {
                    $error = $res;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Account - MyShop</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
    <style>
        .register-container {
            max-width: 520px;
            margin: 40px auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .register-header {
            text-align: center;
            margin-bottom: 25px;
        }
        .register-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .password-requirements {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        .btn-register {
            background-color: #4a6cf7;
            color: white;
            border: none;
            padding: 14px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            margin-top: 10px;
        }
        .btn-register:hover {
            background-color: #3a5cf7;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="register-container">
    <div class="register-header">
        <h2><?php echo $linkingMobile ? 'Link Your Mobile Number' : 'Create Your Account'; ?></h2>
        <p><?php echo $linkingMobile ? 'Add a mobile number and password to your existing account' : 'Join our community and start shopping today'; ?></p>
    </div>
    
    <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
    
    <?php if (!$linkingMobile): ?>
    <!-- Registration Type Toggle -->
    <div style="margin-bottom: 20px; text-align: center;">
        <!-- Optional email mode could be added here if desired -->
        <label>
            <input type="radio" name="registration_type" value="mobile" onchange="toggleRegistrationType()" <?php echo ($default_mode==='mobile'?'checked':''); ?>> Mobile Registration
        </label>
    </div>
    <?php endif; ?>

    <form method="post" id="registrationForm">
        <input type="hidden" name="registration_type" id="registration_type" value="<?php echo e($linkingMobile ? 'mobile' : $default_mode); ?>">
        
        <?php if (!$linkingMobile): ?>
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
        </div>
        <?php endif; ?>
        
        <?php if (!$linkingMobile): ?>
        <div class="form-group" id="emailGroup" style="<?php echo ($default_mode==='mobile'?'display:none;':''); ?>">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">
        </div>
        <?php endif; ?>
        
        <div class="form-group" id="phoneGroup" style="<?php echo ($linkingMobile || $default_mode==='mobile'?'':'display: none;'); ?>">
            <label for="phone">Mobile Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your mobile number (e.g., 1234567890)" value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>" pattern="[0-9]{10,15}" title="Please enter a valid mobile number (10-15 digits)">
            <div class="password-requirements">Mobile number must be 10-15 digits</div>
        </div>
        
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required minlength="8">
            <div class="password-requirements">Password must be at least 8 characters long</div>
        </div>
        
        <div class="form-group">
            <label for="confirm">Confirm Password</label>
            <input type="password" id="confirm" name="confirm" required minlength="8">
        </div>
        
        <button type="submit" class="btn-register">Create Account</button>
    </form>
    
    <script>
    function toggleRegistrationType() {
        const selectedRadio = document.querySelector('input[name="registration_type"]:checked');
        const emailGroup = document.getElementById('emailGroup');
        const phoneGroup = document.getElementById('phoneGroup');
        const emailInput = document.getElementById('email');
        const phoneInput = document.getElementById('phone');
        const registrationType = document.getElementById('registration_type');
        
        const mode = selectedRadio ? selectedRadio.value : registrationType.value;
        
        if (mode === 'email') {
            emailGroup.style.display = 'block';
            phoneGroup.style.display = 'none';
            if (emailInput) emailInput.required = true;
            if (phoneInput) phoneInput.required = false;
            registrationType.value = 'email';
        } else {
            emailGroup.style.display = 'none';
            phoneGroup.style.display = 'block';
            if (emailInput) emailInput.required = false;
            if (phoneInput) phoneInput.required = true;
            registrationType.value = 'mobile';
        }
    }
    
    // Initialize form display based on default mode on load
    document.addEventListener('DOMContentLoaded', function(){
        // Force mobile mode when linking mobile while logged-in
        var linking = <?php echo $linkingMobile ? 'true' : 'false'; ?>;
        if (linking) {
            var hidden = document.getElementById('registration_type');
            if (hidden) hidden.value = 'mobile';
        }
        toggleRegistrationType();
    });
    
    // Mobile number validation
    function validateMobileNumber(phone) {
        const cleanPhone = phone.replace(/[^0-9]/g, '');
        return cleanPhone.length >= 10 && cleanPhone.length <= 15;
    }
    
    // Add form validation
    document.getElementById('registrationForm').addEventListener('submit', function(e) {
        const registrationType = document.getElementById('registration_type').value;
        const phoneInput = document.getElementById('phone');
        
        if (registrationType === 'mobile') {
            if (!validateMobileNumber(phoneInput.value)) {
                e.preventDefault();
                alert('Please enter a valid mobile number (10-15 digits)');
                phoneInput.focus();
                return false;
            }
        }
    });
    </script>
    
    <div class="login-link">
        <p>Already have an account? <a href="/Ecomme/public/login.php">Login</a></p>
    </div>
</div>
    </div>
</div>
</body>
</html>


