<?php
require_once __DIR__ . '/../lib/auth.php';

// Check if admin is logged in, otherwise redirect to login page
if (!admin_logged_in()) {
    redirect('/Ecomme/admin/login.php');
}

// Admin is logged in, redirect to categories page
redirect('/Ecomme/admin/categories.php');
?>
        
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


