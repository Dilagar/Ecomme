<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$success = '';
$error = '';

// Process form submission
if (is_post()) {
    $email = post_param('email');
    $password = post_param('password');
    $confirm_password = post_param('confirm_password');
    
    // Validate input
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } else {
        // Check if email already exists
        global $conn;
        $email_safe = mysqli_real_escape_string($conn, $email);
        $check_sql = "SELECT id FROM admins WHERE email = '$email_safe' LIMIT 1";
        $check_result = mysqli_query($conn, $check_sql);
        
        if ($check_result && mysqli_num_rows($check_result) > 0) {
            $error = 'Email already exists';
        } else {
            // Create hashed password
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $password_hash_safe = mysqli_real_escape_string($conn, $password_hash);
            
            // Insert new admin
            $insert_sql = "INSERT INTO admins (email, password_hash) VALUES ('$email_safe', '$password_hash_safe')";
            if (mysqli_query($conn, $insert_sql)) {
                $success = 'Admin account created successfully';
            } else {
                $error = 'Error creating admin account: ' . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Create Admin Account - MyShop</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
    <style>
        .admin-create-container {
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
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn-primary {
            background-color: #4a6cf7;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .btn-primary:hover {
            background-color: #3a5cf7;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <div class="admin-create-container">
        <div class="admin-header">
            <h2>Create Admin Account</h2>
            <div class="subtitle">Set up a new administrator account</div>
        </div>
        
        <?php if (!empty($success)): ?>
        <div class="alert alert-success">
            <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn-primary">Create Admin Account</button>
            </div>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="/Ecomme/admin/index.php">Back to Admin Login</a>
        </div>
    </div>
</body>
</html>