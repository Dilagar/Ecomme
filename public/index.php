<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';

$q = get_param('q');
$sort = get_param('sort'); // price_asc | price_desc | newest

$where = " WHERE p.is_active=1 ";
if ($q !== '') {
    $q_s = mysqli_real_escape_string($conn, $q);
    $where .= " AND (p.name LIKE '%$q_s%' OR p.description LIKE '%$q_s%') ";
}

$order = " ORDER BY p.id DESC ";
if ($sort === 'price_asc') { $order = " ORDER BY p.price ASC "; }
if ($sort === 'price_desc') { $order = " ORDER BY p.price DESC "; }
if ($sort === 'newest') { $order = " ORDER BY p.id DESC "; }

$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id=p.category_id $where $order";
$res = mysqli_query($conn, $sql);

// Debug information
$debug = [];
$debug['sql'] = $sql;
$debug['error'] = mysqli_error($conn);
$debug['num_rows'] = $res ? mysqli_num_rows($res) : 0;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shop</title>
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
<?php include __DIR__ . '/header.php'; ?>

<?php
// Handle add to cart action
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    if ($quantity > 0 && $product_id > 0) {
        // Get product info
        $product_query = "SELECT name FROM products WHERE id = $product_id LIMIT 1";
        $product_result = mysqli_query($conn, $product_query);
        $product_name = "";
        
        if ($product_result && mysqli_num_rows($product_result) > 0) {
            $product = mysqli_fetch_assoc($product_result);
            $product_name = $product['name'];
        }
        
        // Add to cart logic
        if (user_logged_in()) {
            $user_id = $_SESSION['user_id'];
            
            // Check if user has a cart
            $cart_query = "SELECT id FROM carts WHERE user_id = $user_id LIMIT 1";
            $cart_result = mysqli_query($conn, $cart_query);
            $cart_id = 0;
            
            if ($cart_result && mysqli_num_rows($cart_result) > 0) {
                $cart = mysqli_fetch_assoc($cart_result);
                $cart_id = $cart['id'];
            } else {
                // Create new cart
                mysqli_query($conn, "INSERT INTO carts (user_id) VALUES ($user_id)");
                $cart_id = mysqli_insert_id($conn);
            }
            
            // Check if product already in cart
            $item_query = "SELECT id, quantity FROM cart_items WHERE cart_id = $cart_id AND product_id = $product_id LIMIT 1";
            $item_result = mysqli_query($conn, $item_query);
            
            if ($item_result && mysqli_num_rows($item_result) > 0) {
                // Update quantity
                $item = mysqli_fetch_assoc($item_result);
                $new_quantity = $item['quantity'] + $quantity;
                mysqli_query($conn, "UPDATE cart_items SET quantity = $new_quantity WHERE id = " . $item['id']);
            } else {
                // Add new item
                mysqli_query($conn, "INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ($cart_id, $product_id, $quantity)");
            }
            
            // Set success message
            $_SESSION['cart_message'] = "$product_name added to cart successfully!";
        } else {
            // Redirect to login
            redirect('/Ecomme/public/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        }
    }
}
?>

<?php if (isset($_SESSION['cart_message'])): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        showCartNotification("<?php echo $_SESSION['cart_message']; ?>");
    });
</script>
<?php unset($_SESSION['cart_message']); endif; ?>
<div class="container">
    <div class="card">
        <form method="get" class="row" style="align-items:flex-end;">
            <div class="col">
                <label>Search</label>
                <input type="text" name="q" placeholder="Search products" value="<?php echo e($q); ?>">
            </div>
            <div class="col">
                <label>Sort</label>
                <select name="sort">
                    <option value="">Default</option>
                    <option value="price_asc" <?php echo $sort==='price_asc'?'selected':''; ?>>Price: Low to High</option>
                    <option value="price_desc" <?php echo $sort==='price_desc'?'selected':''; ?>>Price: High to Low</option>
                    <option value="newest" <?php echo $sort==='newest'?'selected':''; ?>>Newest</option>
                </select>
            </div>
            <div class="col" style="max-width:160px">
                <button type="submit">Apply</button>
            </div>
        </form>
    </div>

    <!-- Debug information -->
    <?php if ($debug['num_rows'] == 0): ?>
    <div class="card" style="margin-bottom: 20px;">
        <h3>No Products Found</h3>
        <p>Possible reasons:</p>
        <ul>
            <li>No products have been added to the database yet</li>
            <li>All products are marked as inactive or out of stock</li>
            <li>Database connection issue: <?php echo $debug['error'] ? e($debug['error']) : 'No error reported'; ?></li>
        </ul>
        <p>SQL Query: <code><?php echo e($debug['sql']); ?></code></p>
        <p>Number of rows: <?php echo $debug['num_rows']; ?></p>
    </div>
    <?php endif; ?>

    <div class="row" style="display:flex; flex-wrap:wrap; gap:16px">
        <?php if ($debug['num_rows'] > 0): ?>
            <?php while($row = mysqli_fetch_assoc($res)): ?>
                <div class="card" style="flex: 1 1 calc(33.333% - 16px); box-sizing:border-box;">
                    <a href="/Ecomme/public/product.php?slug=<?php echo e($row['slug']); ?>">
                        <?php if (!empty($row['image'])): ?>
                            <img src="/Ecomme/uploads/<?php echo e($row['image']); ?>" alt="" style="width:100%;max-height:200px;object-fit:cover;border-radius:6px">
                        <?php endif; ?>
                    </a>
                    <h3 style="margin-top:10px;"><?php echo e($row['name']); ?></h3>
                    <div><?php echo e($row['category_name']); ?></div>
                    <div style="margin:6px 0"><strong>₹<?php echo e(number_format((float)$row['price'],2)); ?></strong></div>
                <?php if ($row['stock'] <= 0): ?>
                    <div style="color: #e74c3c; font-weight: bold; margin: 10px 0;">Out of Stock</div>
                <?php endif; ?>
                <div class="row">
                    <div class="col" style="max-width:50%">
                        <a href="/Ecomme/public/product.php?slug=<?php echo e($row['slug']); ?>">
                            <button type="button">View</button>
                        </a>
                    </div>
                    <div class="col" style="max-width:50%">
                        <?php if ($row['stock'] > 0): ?>
                            <form method="post" action="/Ecomme/public/index.php">
                                <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_to_cart" class="add-to-cart-btn">Add to Cart</button>
                            </form>
                        <?php else: ?>
                            <button type="button" disabled style="opacity: 0.5; cursor: not-allowed;">Add to Cart</button>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card" style="width: 100%; text-align: center; padding: 30px;">
                <h3>No products available</h3>
                <p>Please check back later or try a different search.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>


