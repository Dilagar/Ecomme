<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$action = get_param('action', 'list');

if ($action === 'update_status' && is_post()) {
    $order_id = (int)post_param('order_id');
    $new_status = post_param('status');
    $valid_statuses = ['on_process', 'shipped', 'delivered', 'cancelled'];
    
    if (in_array($new_status, $valid_statuses)) {
        $status_safe = mysqli_real_escape_string($conn, $new_status);
        mysqli_query($conn, "UPDATE orders SET status='$status_safe' WHERE id=$order_id LIMIT 1");
    }
    redirect('/Ecomme/admin/orders.php');
}

$orders = mysqli_query($conn, "
    SELECT o.*, u.email as user_email, a.full_name, a.line1, a.city, a.postal_code, a.country
    FROM orders o 
    JOIN users u ON u.id = o.user_id 
    JOIN addresses a ON a.id = o.address_id 
    ORDER BY o.id ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Orders</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><strong>Admin</strong> - Orders</div>
    <div>
        <a href="/Ecomme/admin/categories.php">Categories</a> |
        <a href="/Ecomme/admin/products.php">Products</a> |
        <a href="/Ecomme/admin/logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Order Management</h2>
    
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while($order = mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td><strong>#<?php echo (int)$order['id']; ?></strong></td>
                    <td><?php echo e($order['user_email']); ?></td>
                    <td>
                        <?php echo e($order['full_name']); ?><br>
                        <?php echo e($order['line1']); ?>, <?php echo e($order['city']); ?><br>
                        <?php echo e($order['postal_code']); ?>, <?php echo e($order['country']); ?>
                    </td>
                    <td>₹<?php echo e(number_format((float)$order['total_amount'],2)); ?></td>
                    <td>
                        <form method="post" action="?action=update_status" style="display:inline;">
                            <input type="hidden" name="order_id" value="<?php echo (int)$order['id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="on_process" <?php echo $order['status']==='on_process'?'selected':''; ?>>On Process</option>
                                <option value="shipped" <?php echo $order['status']==='shipped'?'selected':''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['status']==='delivered'?'selected':''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['status']==='cancelled'?'selected':''; ?>>Cancelled</option>
                            </select>
                        </form>
                    </td>
                    <td><?php echo e($order['created_at']); ?></td>
                    <td>
                        <a href="/Ecomme/admin/order_details.php?id=<?php echo (int)$order['id']; ?>">View Details</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
