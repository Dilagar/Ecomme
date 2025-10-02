<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$id = (int)get_param('id');
if ($id <= 0) { redirect('/Ecomme/public/index.php'); }

$res = mysqli_query($conn, "SELECT o.*, u.email, a.name, a.address_line1, a.city, a.postal_code, a.country FROM orders o JOIN users u ON u.id=o.user_id JOIN addresses a ON a.id=o.address_id WHERE o.id=$id LIMIT 1");
$order = $res ? mysqli_fetch_assoc($res) : null;
if (!$order) { redirect('/Ecomme/public/index.php'); }
$items = mysqli_query($conn, "SELECT oi.*, p.name FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=$id");
?>
<!DOCTYPE html>
<html>
<head>
    <div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
</div>
    <meta charset="utf-8">
    <title>Invoice #<?php echo (int)$order['id']; ?></title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="container">
    <h2>Invoice #<?php echo (int)$order['id']; ?></h2>
    <div class="card">
        <div><strong>Date:</strong> <?php echo e($order['created_at']); ?></div>
        <div><strong>Billed To:</strong> <?php echo e($order['name']); ?>, <?php echo e($order['address_line1']); ?>, <?php echo e($order['city']); ?>, <?php echo e($order['postal_code']); ?>, <?php echo e($order['country']); ?></div>
        <div><strong>Email:</strong> <?php echo e($order['email']); ?></div>
        <div><strong>Status:</strong> <?php echo e($order['status']); ?></div>
    </div>
    <div class="card">
        <table>
            <thead>
                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
            </thead>
            <tbody>
            <?php $grand=0.0; while ($it = mysqli_fetch_assoc($items)): $line=$it['price']*$it['quantity']; $grand+=$line; ?>
                <tr>
                    <td><?php echo e($it['name']); ?></td>
                    <td><?php echo (int)$it['quantity']; ?></td>
                    <td>₹<?php echo e(number_format((float)$it['price'],2)); ?></td>
                    <td>₹<?php echo e(number_format((float)$line,2)); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
        <div style="text-align:right;margin-top:10px"><strong>Grand Total:</strong> ₹<?php echo e(number_format((float)$grand,2)); ?></div>
    </div>
</div>
</body>
</html>


