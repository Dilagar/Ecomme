<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$id = (int)get_param('id');
if ($id <= 0) { redirect('/Ecomme/public/index.php'); }

$res = mysqli_query($conn, "SELECT o.*, a.full_name, a.line1, a.city, a.postal_code, a.country FROM orders o JOIN addresses a ON a.id=o.address_id WHERE o.id=$id LIMIT 1");
$order = $res ? mysqli_fetch_assoc($res) : null;
if (!$order) { 
    echo "Order not found. Error: " . mysqli_error($conn);
    exit;
}

$items = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$id");
if (!$items) {
    echo "Failed to fetch order items. Error: " . mysqli_error($conn);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Success</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/index.php">Continue Shopping</a>
    </div>
</div>
<div class="container">
    <div class="card">
        <h2>Order #<?php echo (int)$order['id']; ?> Confirmed</h2>
        <p>Shipping to: <?php echo e($order['full_name']); ?>, <?php echo e($order['line1']); ?>, <?php echo e($order['city']); ?>, <?php echo e($order['postal_code']); ?>, <?php echo e($order['country']); ?></p>
        <p>Status: <?php echo e($order['status']); ?></p>
        <p>Total: ₹<?php echo e(number_format((float)$order['total_amount'],2)); ?></p>
        <a href="/Ecomme/public/invoice.php?id=<?php echo (int)$order['id']; ?>"><button>View Invoice</button></a>
    </div>
    <div class="card">
        <h3>Items</h3>
        <table>
            <thead>
                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php while ($it = mysqli_fetch_assoc($items)): ?>
                    <tr>
                        <td><?php echo e($it['name']); ?></td>
                        <td><?php echo (int)$it['quantity']; ?></td>
                        <td>₹<?php echo e(number_format((float)$it['price'],2)); ?></td>
                        <td>₹<?php echo e(number_format((float)$it['price'] * (int)$it['quantity'],2)); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>


