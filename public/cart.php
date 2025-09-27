<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

// Simple session cart for guest users; for logged-in users, you can sync to DB later
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

$action = get_param('action');

if ($action === 'add' && is_post()) {
    $pid = (int)post_param('product_id');
    $res = mysqli_query($conn, "SELECT id, name, price, stock FROM products WHERE id=$pid AND is_active=1 LIMIT 1");
    $p = $res ? mysqli_fetch_assoc($res) : null;
    if ($p && (int)$p['stock'] > 0) {
        if (!isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid] = 1;
        } else {
            $_SESSION['cart'][$pid] += 1;
        }
    }
    redirect('/Ecomme/public/cart.php');
}

if ($action === 'update' && is_post()) {
    foreach ($_POST['qty'] as $pid => $qty) {
        $pid = (int)$pid; $qty = max(0, (int)$qty);
        if ($qty === 0) { unset($_SESSION['cart'][$pid]); }
        else { $_SESSION['cart'][$pid] = $qty; }
    }
    redirect('/Ecomme/public/cart.php');
}

if ($action === 'remove') {
    $pid = (int)get_param('pid');
    unset($_SESSION['cart'][$pid]);
    redirect('/Ecomme/public/cart.php');
}

$items = [];
$subtotal = 0.0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $res = mysqli_query($conn, "SELECT id, name, price, stock, slug, image FROM products WHERE id IN ($ids) AND is_active=1");
    while ($row = mysqli_fetch_assoc($res)) {
        $qty = (int)($_SESSION['cart'][$row['id']] ?? 0);
        if ($qty > 0) {
            $row['qty'] = $qty;
            $row['line_total'] = $qty * (float)$row['price'];
            $row['out_of_stock'] = ((int)$row['stock'] <= 0);
            $items[] = $row;
            $subtotal += $row['line_total'];
        }
    }
}
$total = $subtotal; // no shipping/tax for simplicity
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cart</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/wishlist.php">Wishlist</a>
    </div>
</div>
<div class="container">
    <h2>Your Cart</h2>
    <form method="post" action="?action=update">
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="5">Cart is empty.</td></tr>
        <?php else: ?>
            <?php foreach ($items as $it): ?>
                <tr>
                    <td>
                        <a href="/Ecomme/public/product.php?slug=<?php echo e($it['slug']); ?>"><?php echo e($it['name']); ?></a>
                        <?php if ($it['out_of_stock']): ?>
                            <br><span style="color:red;font-size:12px;">Out of Stock</span>
                        <?php endif; ?>
                    </td>
                    <td>₹<?php echo e(number_format((float)$it['price'],2)); ?></td>
                    <td style="max-width:120px">
                        <input type="number" min="0" name="qty[<?php echo (int)$it['id']; ?>]" value="<?php echo (int)$it['qty']; ?>" <?php echo $it['out_of_stock']?'disabled':''; ?>>
                    </td>
                    <td>₹<?php echo e(number_format((float)$it['line_total'],2)); ?></td>
                    <td><a href="?action=remove&pid=<?php echo (int)$it['id']; ?>">Remove</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <div class="row">
        <div class="col"></div>
        <div class="col" style="max-width:320px">
            <div class="card">
                <div><strong>Subtotal:</strong> ₹<?php echo e(number_format((float)$subtotal,2)); ?></div>
                <div><strong>Total:</strong> ₹<?php echo e(number_format((float)$total,2)); ?></div>
                <div class="row">
                    <div class="col"><button type="submit">Update</button></div>
                    <div class="col"><a href="/Ecomme/public/checkout.php"><button type="button" <?php echo empty($items)?'disabled':''; ?>>Checkout</button></a></div>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>
</body>
</html>


