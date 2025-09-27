<?php
require_once __DIR__ . '/../lib/helpers.php';
?>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/cart.php">Cart</a> |
        <a href="/Ecomme/public/wishlist.php">Wishlist</a> |
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/Ecomme/public/dashboard.php">Dashboard</a> |
            <a href="/Ecomme/public/logout.php">Logout</a>
        <?php else: ?>
            <a href="/Ecomme/public/login.php">Login</a> |
            <a href="/Ecomme/public/register.php">Register</a>
        <?php endif; ?>
    </div>
</div>


