<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();

// Get current page for highlighting active menu item
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<div class="admin-topbar">
    <div class="admin-logo">
        <a href="/Ecomme/admin/categories.php"><strong>MyShop Admin</strong></a>
    </div>
    <div class="admin-nav">
        <a href="/Ecomme/admin/categories.php" class="<?php echo ($current_page == 'categories.php') ? 'active' : ''; ?>">
            Categories
        </a>
        <a href="/Ecomme/admin/products.php" class="<?php echo ($current_page == 'products.php') ? 'active' : ''; ?>">
            Products
        </a>
        <a href="/Ecomme/admin/orders.php" class="<?php echo ($current_page == 'orders.php') ? 'active' : ''; ?>">
            Orders
        </a>
        <a href="http://localhost/Ecomme/public/index.php" target="_blank">
            View Store
        </a>
        <a href="/Ecomme/admin/logout.php" class="logout-btn">
            Logout
        </a>
    </div>
</div>
<div class="admin-breadcrumb">
    <div class="breadcrumb-container">
        <span>Admin</span> &gt; 
        <?php 
        switch($current_page) {
            case 'categories.php':
                echo '<span class="current">Categories</span>';
                break;
            case 'products.php':
                echo '<span class="current">Products</span>';
                break;
            case 'orders.php':
                echo '<span class="current">Orders</span>';
                break;
            case 'order_details.php':
                echo '<a href="/Ecomme/admin/orders.php">Orders</a> &gt; <span class="current">Order Details</span>';
                break;
            default:
                echo '<span class="current">Dashboard</span>';
        }
        ?>
    </div>
</div>