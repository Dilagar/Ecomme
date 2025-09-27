<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_user();
$user_id = (int)$_SESSION['user_id'];

if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }
if (empty($_SESSION['cart'])) { redirect('/Ecomme/public/cart.php'); }

// Debug: Show cart contents
if (isset($_GET['debug'])) {
    echo "<pre>Cart contents: ";
    print_r($_SESSION['cart']);
    echo "</pre>";
    exit;
}

$addr_res = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id=$user_id ORDER BY is_default DESC, id DESC");
$addresses = [];
while ($r = mysqli_fetch_assoc($addr_res)) { $addresses[] = $r; }

$error = '';

if (is_post()) {
    $address_id = (int)post_param('address_id');
    if ($address_id <= 0) {
        $error = 'Please select a delivery address.';
    } else {
        // Build order from cart, check stock, reduce stock, create order/items
        $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
        $res = mysqli_query($conn, "SELECT id, name, price, stock FROM products WHERE id IN ($ids) AND is_active=1");
        $products = [];
        while ($row = mysqli_fetch_assoc($res)) { $products[$row['id']] = $row; }

        $total = 0.0;
        foreach ($_SESSION['cart'] as $pid => $qty) {
            $pid = (int)$pid; $qty = (int)$qty;
            if (!isset($products[$pid])) { continue; }
            if ($products[$pid]['stock'] < $qty) {
                $error = 'Insufficient stock for some items.';
                break;
            }
            $total += $qty * (float)$products[$pid]['price'];
        }

        if ($error === '') {
            $insert_order = mysqli_query($conn, "INSERT INTO orders (user_id,address_id,status,total_amount) VALUES ($user_id,$address_id,'on_process',$total)");
            if (!$insert_order) {
                $error = 'Failed to create order: ' . mysqli_error($conn);
            } else {
                $order_id = mysqli_insert_id($conn);
                foreach ($_SESSION['cart'] as $pid => $qty) {
                    $pid = (int)$pid; $qty = (int)$qty;
                    if (!isset($products[$pid])) { continue; }
                    $price = (float)$products[$pid]['price'];
                    $insert_item = mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id,$pid,$qty,$price)");
                    if (!$insert_item) {
                        $error = 'Failed to add order item: ' . mysqli_error($conn);
                        break;
                    }
                    mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id=$pid");
                }
                if ($error === '') {
                    $_SESSION['cart'] = [];
                    redirect('/Ecomme/public/order_success.php?id=' . $order_id);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/addresses.php">Manage Addresses</a>
    </div>
</div>
<div class="container">
    <h2>Checkout</h2>
    <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
    <form method="post">
        <div class="card">
            <h3>Select Delivery Address</h3>
            <?php if (empty($addresses)): ?>
                <div class="alert alert-error">No addresses found. <a href="/Ecomme/public/addresses.php">Add one</a>.</div>
            <?php endif; ?>
            <?php foreach ($addresses as $addr): ?>
                <label style="display:block;border:1px solid #eee;padding:10px;border-radius:6px;margin:6px 0;">
                    <input type="radio" name="address_id" value="<?php echo (int)$addr['id']; ?>" <?php echo ((int)$addr['is_default']===1)?'checked':''; ?>>
                    <?php echo e($addr['full_name']); ?>, <?php echo e($addr['line1']); ?>, <?php echo e($addr['city']); ?>, <?php echo e($addr['postal_code']); ?>, <?php echo e($addr['country']); ?>
                </label>
            <?php endforeach; ?>
        </div>
        <div class="card">
            <h3>Payment</h3>
            <p>Mock payment success will be applied.</p>
        </div>
        <button type="submit">Confirm Order</button>
    </form>
</div>
</body>
</html>


