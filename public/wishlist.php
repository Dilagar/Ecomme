<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

if (!isset($_SESSION['wishlist'])) { $_SESSION['wishlist'] = []; }

$action = get_param('action');

if ($action === 'add' && is_post()) {
    $pid = (int)post_param('product_id');
    $res = mysqli_query($conn, "SELECT id, stock FROM products WHERE id=$pid AND is_active=1 LIMIT 1");
    $p = $res ? mysqli_fetch_assoc($res) : null;
    if ($p && (int)$p['stock'] > 0) {
        $_SESSION['wishlist'][$pid] = 1;
    }
    redirect('/Ecomme/public/wishlist.php');
}

if ($action === 'remove') {
    $pid = (int)get_param('pid');
    unset($_SESSION['wishlist'][$pid]);
    redirect('/Ecomme/public/wishlist.php');
}

$items = [];
if (!empty($_SESSION['wishlist'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['wishlist'])));
    $res = mysqli_query($conn, "SELECT id, name, price, slug, image, stock FROM products WHERE id IN ($ids) AND is_active=1");
    while ($row = mysqli_fetch_assoc($res)) { 
        $row['out_of_stock'] = ((int)$row['stock'] <= 0);
        $items[] = $row; 
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Wishlist</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/cart.php">Cart</a>
    </div>
</div>
<div class="container">
    <h2>Wishlist</h2>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($items)): ?>
            <tr><td colspan="3">No items in wishlist.</td></tr>
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
                    <td><a href="?action=remove&pid=<?php echo (int)$it['id']; ?>">Remove</a></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>


