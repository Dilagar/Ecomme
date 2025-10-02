<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$order_id = (int)get_param('id');
if ($order_id <= 0) { redirect('/Ecomme/admin/orders.php'); }

$order = mysqli_query($conn, "
    SELECT o.*, u.email as user_email, a.name as full_name, a.address_line1 as line1, a.address_line2 as line2, a.city, a.state, a.postal_code, a.country, a.phone
    FROM orders o 
    JOIN users u ON u.id = o.user_id 
    JOIN addresses a ON a.id = o.address_id 
    WHERE o.id = $order_id 
    LIMIT 1
");
$order = $order ? mysqli_fetch_assoc($order) : null;
if (!$order) { redirect('/Ecomme/admin/orders.php'); }

$items = mysqli_query($conn, "
    SELECT oi.*, p.name, p.slug
    FROM order_items oi 
    JOIN products p ON p.id = oi.product_id 
    WHERE oi.order_id = $order_id
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order #<?php echo (int)$order['id']; ?> Details</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><strong>Admin</strong> - Order #<?php echo (int)$order['id']; ?></div>
    <div>
        <a href="/Ecomme/admin/orders.php">← Back to Orders</a>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <h3>Order Information</h3>
                <p><strong>Order ID:</strong> #<?php echo (int)$order['id']; ?></p>
                <p><strong>Date:</strong> <?php echo e($order['created_at']); ?></p>
                <p><strong>Status:</strong> 
                    <span style="text-transform:capitalize;padding:4px 8px;background:#e0e0e0;border-radius:4px;">
                        <?php echo e(str_replace('_', ' ', $order['status'])); ?>
                    </span>
                </p>
                <p><strong>Total:</strong> ₹<?php echo e(number_format((float)$order['total_amount'],2)); ?></p>
            </div>
        </div>
        
        <div class="col">
            <div class="card">
                <h3>Customer Information</h3>
                <p><strong>Email:</strong> <?php echo e($order['user_email']); ?></p>
                <p><strong>Name:</strong> <?php echo e($order['full_name']); ?></p>
                <p><strong>Phone:</strong> <?php echo e($order['phone']); ?></p>
            </div>
        </div>
    </div>
    
    <div class="card">
        <h3>Shipping Address</h3>
        <p>
            <?php echo e($order['full_name']); ?><br>
            <?php echo e($order['line1']); ?><br>
            <?php if ($order['line2']): ?>
                <?php echo e($order['line2']); ?><br>
            <?php endif; ?>
            <?php echo e($order['city']); ?>, <?php echo e($order['state']); ?> <?php echo e($order['postal_code']); ?><br>
            <?php echo e($order['country']); ?>
        </p>
    </div>
    
    <div class="card">
        <h3>Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php while($item = mysqli_fetch_assoc($items)): ?>
                <tr>
                    <td>
                        <a href="/Ecomme/public/product.php?slug=<?php echo e($item['slug']); ?>" target="_blank">
                            <?php echo e($item['name']); ?>
                        </a>
                    </td>
                    <td><?php echo (int)$item['quantity']; ?></td>
                    <td>₹<?php echo e(number_format((float)$item['price'],2)); ?></td>
                    <td>₹<?php echo e(number_format((float)$item['price'] * (int)$item['quantity'],2)); ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <div class="card">
        <h3>Update Order Status</h3>
        <form method="post" action="/Ecomme/admin/orders.php?action=update_status">
            <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
            <select name="status">
                <option value="on_process" <?php echo $order['status']==='on_process'?'selected':''; ?>>On Process</option>
                <option value="shipped" <?php echo $order['status']==='shipped'?'selected':''; ?>>Shipped</option>
                <option value="delivered" <?php echo $order['status']==='delivered'?'selected':''; ?>>Delivered</option>
                <option value="cancelled" <?php echo $order['status']==='cancelled'?'selected':''; ?>>Cancelled</option>
            </select>
            <button type="submit">Update Status</button>
        </form>
    </div>
</div>
</body>
</html>
