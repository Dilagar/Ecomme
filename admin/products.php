<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$action = get_param('action', 'list');

// Load categories for dropdown
$cats_res = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name ASC");
$categories = [];
while ($cats_res && $row = mysqli_fetch_assoc($cats_res)) { $categories[] = $row; }

function handle_image_upload($fieldName) {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        return '';
    }
    $name = $_FILES[$fieldName]['name'];
    $tmp = $_FILES[$fieldName]['tmp_name'];
    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp'];
    if (!in_array($ext, $allowed)) { return ''; }
    if (!is_dir(__DIR__ . '/../uploads')) { @mkdir(__DIR__ . '/../uploads', 0777, true); }
    $newName = uniqid('p_', true) . '.' . $ext;
    $dest = __DIR__ . '/../uploads/' . $newName;
    if (move_uploaded_file($tmp, $dest)) {
        return $newName;
    }
    return '';
}

if ($action === 'create' && is_post()) {
    $name = post_param('name');
    $slug = slugify($name);
    $category_id = (int)post_param('category_id');
    $price = (float)post_param('price');
    $description = post_param('description');
    $stock = (int)post_param('stock');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $imageFile = handle_image_upload('image');

    $name_s = mysqli_real_escape_string($conn, $name);
    $slug_s = mysqli_real_escape_string($conn, $slug);
    $desc_s = mysqli_real_escape_string($conn, $description);
    $img_s = mysqli_real_escape_string($conn, $imageFile);

    mysqli_query($conn, "INSERT INTO products (category_id,name,slug,price,description,image,stock,is_active) VALUES ($category_id,'$name_s','$slug_s',$price,'$desc_s','$img_s',$stock,$is_active)");
    redirect('/Ecomme/admin/products.php');
}

if ($action === 'update' && is_post()) {
    $id = (int)post_param('id');
    $name = post_param('name');
    $slug = slugify($name);
    $category_id = (int)post_param('category_id');
    $price = (float)post_param('price');
    $description = post_param('description');
    $stock = (int)post_param('stock');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $imageFile = handle_image_upload('image');

    $name_s = mysqli_real_escape_string($conn, $name);
    $slug_s = mysqli_real_escape_string($conn, $slug);
    $desc_s = mysqli_real_escape_string($conn, $description);

    if ($imageFile) {
        $img_s = mysqli_real_escape_string($conn, $imageFile);
        $sql = "UPDATE products SET category_id=$category_id, name='$name_s', slug='$slug_s', price=$price, description='$desc_s', image='$img_s', stock=$stock, is_active=$is_active WHERE id=$id LIMIT 1";
    } else {
        $sql = "UPDATE products SET category_id=$category_id, name='$name_s', slug='$slug_s', price=$price, description='$desc_s', stock=$stock, is_active=$is_active WHERE id=$id LIMIT 1";
    }
    mysqli_query($conn, $sql);
    redirect('/Ecomme/admin/products.php');
}

if ($action === 'delete') {
    $id = (int)get_param('id');
    mysqli_query($conn, "DELETE FROM products WHERE id=$id LIMIT 1");
    redirect('/Ecomme/admin/products.php');
}

$editing = null;
if ($action === 'edit') {
    $id = (int)get_param('id');
    $res = mysqli_query($conn, "SELECT * FROM products WHERE id=$id LIMIT 1");
    $editing = $res ? mysqli_fetch_assoc($res) : null;
}

$products = mysqli_query($conn, "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id=p.category_id ORDER BY p.id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Products</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <div class="row">
        <div class="col">
            <div class="admin-card">
                <h3><?php echo $editing ? 'Edit Product' : 'Add Product'; ?></h3>
                <form method="post" action="?action=<?php echo $editing ? 'update' : 'create'; ?>" enctype="multipart/form-data">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?php echo (int)$editing['id']; ?>">
                    <?php endif; ?>
                    <label>Name</label>
                    <input type="text" name="name" value="<?php echo e($editing['name'] ?? ''); ?>" required>
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($editing && (int)$editing['category_id']===(int)$cat['id'])?'selected':''; ?>><?php echo e($cat['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" value="<?php echo e($editing['price'] ?? '0.00'); ?>" required>
                    <label>Description</label>
                    <textarea name="description" rows="4"><?php echo e($editing['description'] ?? ''); ?></textarea>
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*">
                    <?php if ($editing && !empty($editing['image'])): ?>
                        <img src="/Ecomme/uploads/<?php echo e($editing['image']); ?>" alt="" style="max-width:120px;display:block;margin:6px 0;">
                    <?php endif; ?>
                    <label>Stock</label>
                    <input type="number" name="stock" value="<?php echo e($editing['stock'] ?? '0'); ?>" required>
                    <label>
                        <input type="checkbox" name="is_active" <?php echo ($editing ? ((int)$editing['is_active']===1?'checked':'') : 'checked'); ?>> Active
                    </label>
                    <button type="submit"><?php echo $editing ? 'Update' : 'Create'; ?></button>
                </form>
            </div>
        </div>
        <div class="col">
            <div class="admin-card">
                <h3>All Products</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Stock Status</th>
                            <th>Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($products)): ?>
                        <tr>
                            <td><?php echo (int)$row['id']; ?></td>
                            <td><?php echo e($row['name']); ?></td>
                            <td><?php echo e($row['category_name']); ?></td>
                            <td>₹<?php echo e(number_format((float)$row['price'],2)); ?></td>
                            <td><?php echo (int)$row['stock']; ?></td>
                            <td>
                                <?php $isOut = ((int)$row['stock'] <= 0); ?>
                                <span style="padding:4px 8px;border-radius:12px;font-size:12px;<?php echo $isOut?'background:#ffe0e0;color:#a10000;':'background:#e5ffea;color:#006b1f;'; ?>">
                                    <?php echo $isOut ? 'Out of Stock' : 'In Stock'; ?>
                                </span>
                            </td>
                            <td><?php echo ((int)$row['is_active']===1?'Yes':'No'); ?></td>
                            <td class="admin-actions">
                                <a href="?action=edit&id=<?php echo (int)$row['id']; ?>" class="edit">Edit</a>
                                <a href="?action=delete&id=<?php echo (int)$row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                            </td>
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


