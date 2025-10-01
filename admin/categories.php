<?php
require_once __DIR__ . '/../lib/auth.php';
require_admin();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

$action = get_param('action', 'list');

if ($action === 'create' && is_post()) {
    
    $name = post_param('name');
    $slug = slugify($name);
    $name_safe = mysqli_real_escape_string($conn, $name);
    $slug_safe = mysqli_real_escape_string($conn, $slug);
    mysqli_query($conn, "INSERT INTO categories (name, slug) VALUES ('{$name_safe}','{$slug_safe}')");
    redirect('/Ecomme/admin/categories.php');
}

if ($action === 'update' && is_post()) {
    $id = (int)post_param('id');
    $name = post_param('name');
    $slug = slugify($name);
    $name_safe = mysqli_real_escape_string($conn, $name);
    $slug_safe = mysqli_real_escape_string($conn, $slug);
    mysqli_query($conn, "UPDATE categories SET name='{$name_safe}', slug='{$slug_safe}' WHERE id={$id} LIMIT 1");
    redirect('/Ecomme/admin/categories.php');
}

if ($action === 'delete') {
    $id = (int)get_param('id');
    mysqli_query($conn, "DELETE FROM categories WHERE id={$id} LIMIT 1");
    redirect('/Ecomme/admin/categories.php');
}

$editing = null;
if ($action === 'edit') {
    $id = (int)get_param('id');
    $res = mysqli_query($conn, "SELECT * FROM categories WHERE id={$id} LIMIT 1");
    $editing = $res ? mysqli_fetch_assoc($res) : null;
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Categories</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<?php include __DIR__ . '/header.php'; ?>

<div class="admin-container">
    <div class="row">
        <div class="col">
            <div class="admin-card">
                <h3><?php echo $editing ? 'Edit Category' : 'Add Category'; ?></h3>
                <form method="post" action="?action=<?php echo $editing ? 'update' : 'create'; ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?php echo (int)$editing['id']; ?>">
                    <?php endif; ?>
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" value="<?php echo e($editing['name'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="<?php echo $editing ? 'admin-login-btn' : 'admin-login-btn'; ?>"><?php echo $editing ? 'Update Category' : 'Create Category'; ?></button>
                    <?php if ($editing): ?>
                        <a href="/Ecomme/admin/categories.php" style="margin-left: 10px; text-decoration: none;">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="col">
            <div class="admin-card">
                <h3>All Categories</h3>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($row = mysqli_fetch_assoc($categories)): ?>
                        <tr>
                            <td><?php echo (int)$row['id']; ?></td>
                            <td><?php echo e($row['name']); ?></td>
                            <td><?php echo e($row['slug']); ?></td>
                            <td class="admin-actions">
                                <a href="?action=edit&id=<?php echo (int)$row['id']; ?>" class="edit">Edit</a>
                                <a href="?action=delete&id=<?php echo (int)$row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
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


