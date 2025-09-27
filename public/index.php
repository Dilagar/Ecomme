<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$q = get_param('q');
$sort = get_param('sort'); // price_asc | price_desc | newest

$where = " WHERE p.is_active=1 AND p.stock > 0 ";
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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Shop</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/cart.php">Cart</a> |
        <a href="/Ecomme/public/wishlist.php">Wishlist</a>
    </div>
    
</div>
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

    <div class="row" style="display:flex; flex-wrap:wrap; gap:16px">
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
                <div class="row">
                    <div class="col" style="max-width:50%">
                        <a href="/Ecomme/public/product.php?slug=<?php echo e($row['slug']); ?>">
                            <button>View</button>
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>


