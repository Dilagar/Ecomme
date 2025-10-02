<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';
require_user();
$user_id = (int)$_SESSION['user_id'];

$action = get_param('action', 'list');

if ($action === 'create' && is_post()) {
    $full_name = mysqli_real_escape_string($conn, post_param('full_name'));
    $line1 = mysqli_real_escape_string($conn, post_param('line1'));
    $line2 = mysqli_real_escape_string($conn, post_param('line2'));
    $city = mysqli_real_escape_string($conn, post_param('city'));
    $state = mysqli_real_escape_string($conn, post_param('state'));
    $postal_code = mysqli_real_escape_string($conn, post_param('postal_code'));
    $country = mysqli_real_escape_string($conn, post_param('country'));
    $phone = mysqli_real_escape_string($conn, post_param('phone'));
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    if ($is_default) { mysqli_query($conn, "UPDATE addresses SET is_default=0 WHERE user_id=$user_id"); }
    mysqli_query($conn, "INSERT INTO addresses (user_id, name, address_line1, address_line2, city, state, postal_code, country, phone, is_default) VALUES ($user_id,'$full_name','$line1','$line2','$city','$state','$postal_code','$country','$phone',$is_default)");
    redirect('/Ecomme/public/addresses.php');
}

if ($action === 'update' && is_post()) {
    $id = (int)post_param('id');
    $full_name = mysqli_real_escape_string($conn, post_param('name'));
    $line1 = mysqli_real_escape_string($conn, post_param('address_line1'));
    $line2 = mysqli_real_escape_string($conn, post_param('address_line2'));
    $city = mysqli_real_escape_string($conn, post_param('city'));
    $state = mysqli_real_escape_string($conn, post_param('state'));
    $postal_code = mysqli_real_escape_string($conn, post_param('postal_code'));
    $country = mysqli_real_escape_string($conn, post_param('country'));
    $phone = mysqli_real_escape_string($conn, post_param('phone'));
    $is_default = isset($_POST['is_default']) ? 1 : 0;
    if ($is_default) { mysqli_query($conn, "UPDATE addresses SET is_default=0 WHERE user_id=$user_id"); }
    mysqli_query($conn, "UPDATE addresses SET name='$full_name', address_line1='$line1', address_line2='$line2', city='$city', state='$state', postal_code='$postal_code', country='$country', phone='$phone', is_default=$is_default WHERE id=$id AND user_id=$user_id LIMIT 1");
    redirect('/Ecomme/public/addresses.php');
}

if ($action === 'delete') {
    $id = (int)get_param('id');
    mysqli_query($conn, "DELETE FROM addresses WHERE id=$id AND user_id=$user_id LIMIT 1");
    redirect('/Ecomme/public/addresses.php');
}

$editing = null;
if ($action === 'edit') {
    $id = (int)get_param('id');
    $res = mysqli_query($conn, "SELECT * FROM addresses WHERE id=$id AND user_id=$user_id LIMIT 1");
    $editing = $res ? mysqli_fetch_assoc($res) : null;
}

$res = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id=$user_id ORDER BY is_default ASC, id ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Addresses</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="topbar">
    <div><a href="/Ecomme/public/index.php"><strong>MyShop</strong></a></div>
    <div>
        <a href="/Ecomme/public/checkout.php">Checkout</a>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col">
            <div class="card">
                <h3><?php echo $editing ? 'Edit Address' : 'Add Address'; ?></h3>
                <form method="post" action="?action=<?php echo $editing ? 'update' : 'create'; ?>">
                    <?php if ($editing): ?>
                        <input type="hidden" name="id" value="<?php echo (int)$editing['id']; ?>">
                    <?php endif; ?>
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo e($editing['name'] ?? ''); ?>" required>
                    <label>Address Line 1</label>
                    <input type="text" name="address_line1" value="<?php echo e($editing['address_line1'] ?? ''); ?>" required>
                    <label>Address Line 2</label>
                    <input type="text" name="address_line2" value="<?php echo e($editing['address_line2'] ?? ''); ?>">
                    <label>City</label>
                    <input type="text" name="city" value="<?php echo e($editing['city'] ?? ''); ?>" required>
                    <label>State</label>
                    <input type="text" name="state" value="<?php echo e($editing['state'] ?? ''); ?>">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" value="<?php echo e($editing['postal_code'] ?? ''); ?>" required>
                    <label>Country</label>
                    <input type="text" name="country" value="<?php echo e($editing['country'] ?? ''); ?>" required>
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo e($editing['phone'] ?? ''); ?>">
                    <label><input type="checkbox" name="is_default" <?php echo ($editing && (int)$editing['is_default']===1)?'checked':''; ?>> Make default</label>
                    <button type="submit"><?php echo $editing ? 'Update' : 'Create'; ?></button>
                </form>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <h3>Saved Addresses</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th><th>Address</th><th>Phone</th><th>Default</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><?php echo e($row['name']); ?></td>    
                            <td><?php echo e($row['address_line1']); ?>, <?php echo e($row['city']); ?>, <?php echo e($row['postal_code']); ?>, <?php echo e($row['country']); ?></td>
                            <td><?php echo e($row['phone']); ?></td>
                            <td><?php echo ((int)$row['is_default']===1?'Yes':'No'); ?></td>
                            <td>
                                <a href="?action=edit&id=<?php echo (int)$row['id']; ?>">Edit</a>
                                <a href="?action=delete&id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Delete address?');">Delete</a>
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


