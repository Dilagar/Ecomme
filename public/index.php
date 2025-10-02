<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';

// Handle Add to Cart form submission
if (isset($_POST['add_to_cart']) && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Check if product exists and is in stock
    $check_product = mysqli_query($conn, "SELECT id, stock FROM products WHERE id=$product_id AND is_active=1 LIMIT 1");
    if ($check_product && mysqli_num_rows($check_product) > 0) {
        $product = mysqli_fetch_assoc($check_product);
        if ((int)$product['stock'] > 0) {
            // Add to cart
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                $_SESSION['cart'][$product_id] += $quantity;
            }
            
            // Redirect to cart page
            redirect('/Ecomme/public/cart.php');
        }
    }
}

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
<?php include __DIR__ . '/modern-header.php'; ?>

<script>
    // Function to show cart notification
    function showCartNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-success';
        notification.innerHTML = `<i class="fas fa-check-circle"></i> ${message}`;
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>

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
    <div class="card mb-4" style="border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
        <form method="get" class="row" style="align-items: center; padding: 15px;">
            <div class="col" style="flex: 2;">
                <div class="form-group mb-0">
                    <label class="form-label">Search Products</label>
                    <div style="display: flex;">
                        <input type="text" name="q" class="form-control" placeholder="Search products..." value="<?php echo e($q); ?>">
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group mb-0">
                    <label class="form-label">Sort By</label>
                    <select name="sort" class="form-control">
                        <option value="">Default</option>
                        <option value="price_asc" <?php echo $sort==='price_asc'?'selected':''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $sort==='price_desc'?'selected':''; ?>>Price: High to Low</option>
                        <option value="newest" <?php echo $sort==='newest'?'selected':''; ?>>Newest</option>
                    </select>
                </div>
            </div>
            <div class="col" style="max-width: 160px;">
                <button type="submit" class="btn btn-primary" style="margin-top: 24px;">
                    <i class="fas fa-filter"></i> Apply
                </button>
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

    <div class="product-grid">
        <?php if ($debug['num_rows'] > 0): ?>
            <?php while($row = mysqli_fetch_assoc($res)): ?>
                <div class="product-card">
                    <div class="product-image">
                        <a href="/Ecomme/public/product.php?slug=<?php echo e($row['slug']); ?>">
                            <?php if (!empty($row['image'])): ?>
                                <img src="/Ecomme/uploads/<?php echo e($row['image']); ?>" alt="<?php echo e($row['name']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/300x300" alt="No image">
                            <?php endif; ?>
                        </a>
                        
                        <?php if ($row['stock'] <= 0): ?>
                            <div class="product-badge" style="background-color: var(--danger-color);">Out of Stock</div>
                        <?php endif; ?>
                        
                        <div class="product-actions">
                            <a href="javascript:void(0);" onclick="addToWishlist(<?php echo $row['id']; ?>)" class="product-action-btn">
                                <i class="fas fa-heart"></i>
                            </a>
                            <a href="/Ecomme/public/product.php?slug=<?php echo e($row['slug']); ?>" class="product-action-btn">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="product-info">
                        <div class="product-category"><?php echo e($row['category_name']); ?></div>
                        <h3 class="product-title">
                            <a href="/Ecomme/public/product.php?slug=<?php echo e($row['slug']); ?>"><?php echo e($row['name']); ?></a>
                        </h3>
                        <div class="product-price">
                            <span class="current-price">₹<?php echo e(number_format((float)$row['price'],2)); ?></span>
                        </div>
                        
                        <?php if ($row['stock'] > 0): ?>
                            <button type="button" onclick="addToCart(<?php echo $row['id']; ?>)" class="add-to-cart">
                                <i class="fas fa-shopping-cart"></i> Add to Cart
                            </button>
                        <?php else: ?>
                            <button type="button" class="add-to-cart" disabled style="background-color: var(--gray-color);">
                                Out of Stock
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px;">
                <i class="fas fa-search" style="font-size: 48px; color: var(--gray-color); margin-bottom: 20px;"></i>
                <h3>No products available</h3>
                <p>Please check back later or try a different search.</p>
            </div>
        <?php endif; ?>
    </div>
<?php include __DIR__ . '/modern-footer.php'; ?>

<div id="notification-container"></div>

<script>
// Notification system
function showNotification(message, type = 'success') {
    const container = document.getElementById('notification-container');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    const icon = type === 'success' ? 'check-circle' : 'exclamation-circle';
    
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${icon} notification-icon"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="this.parentElement.remove()">×</button>
    `;
    
    container.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'fadeOut 0.5s forwards';
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}

// Add to wishlist function
function addToWishlist(productId) {
    fetch('/Ecomme/public/wishlist_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to wishlist!');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to add to wishlist', 'error');
    });
}

// Add to cart function
function addToCart(productId) {
    fetch('/Ecomme/public/cart_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId + '&quantity=1'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart!');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Failed to add to cart', 'error');
    });
}
</script>


