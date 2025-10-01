<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$slug = get_param('slug');
if ($slug === '') { redirect('/Ecomme/public/index.php'); }
$slug_s = mysqli_real_escape_string($conn, $slug);
$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.slug='$slug_s' LIMIT 1";
$res = mysqli_query($conn, $sql);
$product = $res ? mysqli_fetch_assoc($res) : null;
if (!$product || (int)$product['is_active'] !== 1) { redirect('/Ecomme/public/index.php'); }

$out_of_stock = ((int)$product['stock'] <= 0);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo e($product['name']); ?> - MyShop</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
    <script>
        // Function to show cart notification
        function showCartNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'alert-cart';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</head>
<body>
<?php 
include __DIR__ . '/header.php';

// Show notification if product was added to cart
if (isset($_SESSION['cart_message'])) {
    echo '<script>showCartNotification("' . $_SESSION['cart_message'] . '");</script>';
    unset($_SESSION['cart_message']);
}
?>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <?php if (!empty($product['image'])): ?>
                    <img src="/Ecomme/uploads/<?php echo e($product['image']); ?>" alt="" style="max-width:100%;border-radius:6px">
                <?php endif; ?>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <h2><?php echo e($product['name']); ?></h2>
                <div><?php echo e($product['category_name']); ?></div>
                <div style="margin:8px 0"><strong>₹<?php echo e(number_format((float)$product['price'],2)); ?></strong></div>
                <p><?php echo nl2br(e($product['description'])); ?></p>
                <?php if ($out_of_stock): ?>
                    <div class="alert alert-error">Out of Stock</div>
                <?php endif; ?>
                <div class="row">
                    <div class="col">
                        <form method="post" action="/Ecomme/public/cart.php?action=add" onsubmit="showCartNotification('<?php echo e($product['name']); ?> added to cart!');">
                            <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                            <button type="submit" <?php echo $out_of_stock?'disabled':''; ?>>Add to Cart</button>
                        </form>
                    </div>
                    <div class="col">
                        <form method="post" action="/Ecomme/public/wishlist.php?action=add">
                            <input type="hidden" name="product_id" value="<?php echo (int)$product['id']; ?>">
                            <button type="submit" <?php echo $out_of_stock?'disabled':''; ?>>Add to Wishlist</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>


