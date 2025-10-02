<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_user();
$user_id = (int)$_SESSION['user_id'];

$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$user_id ORDER BY id ASC");
$wishlist_ids = isset($_SESSION['wishlist']) ? array_keys($_SESSION['wishlist']) : [];
$wishlist = [];
if (!empty($wishlist_ids)) {
    $ids = implode(',', array_map('intval', $wishlist_ids));
    $wishlist_res = mysqli_query($conn, "SELECT id,name,price,slug,stock FROM products WHERE id IN ($ids) AND is_active=1");
    while ($row = mysqli_fetch_assoc($wishlist_res)) { 
        $row['out_of_stock'] = ((int)$row['stock'] <= 0);
        $wishlist[] = $row; 
    }
}
$addresses = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id=$user_id ORDER BY is_default DESC, id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>My Account</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/addresses.php">Addresses</a>
    </div>
    
</div>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <h3>Order History</h3>
                <table>
                    <thead><tr><th>ID</th><th>Date</th><th>Status</th><th>Total</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while ($o = mysqli_fetch_assoc($orders)): ?>
                            <tr>
                                <td><a href="/Ecomme/public/invoice.php?id=<?php echo (int)$o['id']; ?>"><?php echo (int)$o['id']; ?></a></td>
                                <td><?php echo e($o['created_at']); ?></td>
                                <td>
                                    <span style="text-transform:capitalize;padding:4px 8px;background:#e0e0e0;border-radius:4px;">
                                        <?php echo e(str_replace('_', ' ', $o['status'])); ?>
                                    </span>
                                </td>
                                <td>₹<?php echo e(number_format((float)$o['total_amount'],2)); ?></td>
                                <td><a href="/Ecomme/public/order_details.php?id=<?php echo (int)$o['id']; ?>" class="btn btn-sm">View</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <h3>Wishlist</h3>
                <table>
                    <thead><tr><th>Product</th><th>Price</th><th></th></tr></thead>
                    <tbody>
                        <?php if (empty($wishlist)): ?>
                            <tr><td colspan="3">No items.</td></tr>
                        <?php else: foreach ($wishlist as $w): ?>
                            <tr>
                                <td>
                                    <a href="/Ecomme/public/product.php?slug=<?php echo e($w['slug']); ?>"><?php echo e($w['name']); ?></a>
                                    <?php if ($w['out_of_stock']): ?>
                                        <br><span style="color:red;font-size:12px;">Out of Stock</span>
                                    <?php endif; ?>
                                </td>
                                <td>₹<?php echo e(number_format((float)$w['price'],2)); ?></td>
                                <td><a href="/Ecomme/public/wishlist.php?action=remove&pid=<?php echo (int)$w['id']; ?>">Remove</a></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card">
                <h3>Saved Addresses</h3>
                <table>
                    <thead><tr><th>Name</th><th>Address</th><th>Default</th></tr></thead>
                    <tbody>
                        <?php while ($a = mysqli_fetch_assoc($addresses)): ?>
                            <tr>
                                <td><?php echo e($a['name']); ?></td>
                                <td><?php echo e($a['address_line1']); ?>, <?php echo e($a['city']); ?>, <?php echo e($a['postal_code']); ?></td>
                                <td><?php echo ((int)$a['is_default']===1?'Yes':'No'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>


