<?php
require_once __DIR__ . '/../lib/helpers.php';
?>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div class="main-nav">
        <a href="/Ecomme/public/index.php">Home</a> |
        <a href="/Ecomme/public/index.php">Products</a> |
        
        <!-- Product Categories Dropdown -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle">Categories</a>
            <div class="dropdown-menu">
                <?php
                // Fetch categories from database
                global $conn;
                $cat_query = "SELECT id, name, slug FROM categories ORDER BY name";
                $cat_result = mysqli_query($conn, $cat_query);
                if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                    while ($category = mysqli_fetch_assoc($cat_result)) {
                        echo '<a href="/Ecomme/public/index.php?category=' . $category['id'] . '">' . e($category['name']) . '</a>';
                    }
                } else {
                    echo '<a href="#">No categories found</a>';
                }
                ?>
            </div>
        </div> |
        
        <a href="/Ecomme/public/cart.php">Cart</a> |
        <a href="/Ecomme/public/wishlist.php">Wishlist</a> |
        <?php if (!empty($_SESSION['user_id'])): ?>
            <a href="/Ecomme/public/dashboard.php">Dashboard</a> |
            <a href="/Ecomme/public/logout.php">Logout</a>
            <span class="user-welcome">(Welcome, <?php echo e($_SESSION['user_name']); ?>)</span>
        <?php else: ?>
            <a href="/Ecomme/public/login.php">Login</a> |
            <a href="/Ecomme/public/register.php">Register</a>
        <?php endif; ?>
    </div>
</div>
<div class="breadcrumb">
    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    echo '<a href="/Ecomme/public/index.php">Home</a>';
    
    switch($current_page) {
        case 'product.php':
            echo ' > <a href="#">Product Details</a>';
            break;
        case 'cart.php':
            echo ' > <a href="/Ecomme/public/cart.php">Shopping Cart</a>';
            break;
        case 'checkout.php':
            echo ' > <a href="/Ecomme/public/cart.php">Cart</a> > <a href="/Ecomme/public/checkout.php">Checkout</a>';
            break;
        case 'login.php':
            echo ' > <a href="/Ecomme/public/login.php">Login</a>';
            break;
        case 'register.php':
            echo ' > <a href="/Ecomme/public/register.php">Register</a>';
            break;
        case 'dashboard.php':
            echo ' > <a href="/Ecomme/public/dashboard.php">My Account</a>';
            break;
        case 'wishlist.php':
            echo ' > <a href="/Ecomme/public/wishlist.php">My Wishlist</a>';
            break;
    }
    ?>
</div>


