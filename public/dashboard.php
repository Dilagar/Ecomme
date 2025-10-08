<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_user();
$user_id = (int)$_SESSION['user_id'];

// Fetch current user to know if phone is already linked
$user_res = mysqli_query($conn, "SELECT id,name,email,phone FROM users WHERE id=".$user_id." LIMIT 1");
$current_user = $user_res ? mysqli_fetch_assoc($user_res) : null;
$link_error = '';
$link_success = '';

// Handle link mobile number submission
if (is_post() && isset($_POST['action']) && $_POST['action'] === 'link_mobile') {
    $phone = post_param('phone');
    $password = post_param('password');
    $confirm = post_param('confirm');
    
    if (empty($phone) || empty($password) || empty($confirm)) {
        $link_error = 'All fields are required';
    } elseif ($password !== $confirm) {
        $link_error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $link_error = 'Password must be at least 8 characters long';
    } else {
        $result = user_link_mobile($user_id, $phone, $password);
        if ($result === true) {
            $link_success = 'Mobile number linked successfully. You can now login via mobile + password.';
            // Refresh user data
            $user_res = mysqli_query($conn, "SELECT id,name,email,phone FROM users WHERE id=".$user_id." LIMIT 1");
            $current_user = $user_res ? mysqli_fetch_assoc($user_res) : $current_user;
        } else {
            $link_error = $result;
        }
    }
}

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
            <?php if (!empty($link_error)): ?>
                <div class="alert alert-error" style="margin-bottom:15px;"><?php echo e($link_error); ?></div>
            <?php endif; ?>
            <?php if (!empty($link_success)): ?>
                <div class="alert alert-success" style="margin-bottom:15px;"><?php echo e($link_success); ?></div>
            <?php endif; ?>
            
            <?php if (!$current_user || empty($current_user['phone'])): ?>
            <div class="card" style="margin-bottom:20px;">
                <h3>Link Your Mobile Number</h3>
                <p style="color:#555;">Add a mobile number and password to sign in without Google next time.</p>
                <form method="post">
                    <input type="hidden" name="action" value="link_mobile">
                    <div class="form-group">
                        <label for="phone">Mobile Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your mobile number" pattern="[0-9]{10,15}" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Set Password</label>
                        <input type="password" id="password" name="password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm">Confirm Password</label>
                        <input type="password" id="confirm" name="confirm" minlength="8" required>
                    </div>
                    <button type="submit" class="btn">Link Mobile</button>
                </form>
            </div>
            <?php endif; ?>
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


