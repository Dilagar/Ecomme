<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_user();

$user_id = (int)$_SESSION['user_id'];
$order_id = (int)get_param('id');

// Get order details
$order_query = "SELECT * FROM orders WHERE id = $order_id AND user_id = $user_id LIMIT 1";
$order_result = mysqli_query($conn, $order_query);
$order = mysqli_fetch_assoc($order_result);

if (!$order) {
    redirect('/Ecomme/public/dashboard.php');
}

// Get order items
$items_query = "SELECT oi.*, p.name, p.slug, p.image FROM order_items oi 
                LEFT JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = $order_id";
$items_result = mysqli_query($conn, $items_query);

// Address details removed as per requirement
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Order Details #<?php echo $order_id; ?></title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
    <style>
        .order-details {
            margin-bottom: 20px;
        }
        .order-details h3 {
            margin-bottom: 10px;
        }
        .order-meta {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .order-meta p {
            margin: 5px 0;
        }
        .address-box {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<?php include __DIR__ . '/modern-header.php'; ?>

<div class="container">
    <a href="/Ecomme/public/dashboard.php" class="back-link">&larr; Back to Dashboard</a>
    
    <div class="order-details">
        <h2>Order #<?php echo $order_id; ?> Details</h2>
        
        <div class="order-meta">
            <p><strong>Order Date:</strong> <?php echo e($order['created_at']); ?></p>
            <p><strong>Status:</strong> 
                <span style="text-transform:capitalize;padding:4px 8px;background:#e0e0e0;border-radius:4px;">
                    <?php echo e(str_replace('_', ' ', $order['status'])); ?>
                </span>
            </p>
            <p><strong>Total Amount:</strong> ₹<?php echo e(number_format((float)$order['total_amount'],2)); ?></p>
        </div>
        
        <div class="row">
            <div class="col">
                <h3>Products</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = mysqli_fetch_assoc($items_result)): ?>
                            <tr>
                                <td>
                                    <a href="/Ecomme/public/product.php?slug=<?php echo e($item['slug']); ?>">
                                        <?php echo e($item['name']); ?>
                                    </a>
                                </td>
                                <td>₹<?php echo e(number_format((float)$item['price'],2)); ?></td>
                                <td><?php echo (int)$item['quantity']; ?></td>
                                <td>₹<?php echo e(number_format((float)($item['price'] * $item['quantity']),2)); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                            <td>₹<?php echo e(number_format((float)$order['total_amount'],2)); ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        
        <!-- Shipping address section removed as per requirement -->
    </div>
</div>

<?php include __DIR__ . '/modern-footer.php'; ?>
</body>
</html>