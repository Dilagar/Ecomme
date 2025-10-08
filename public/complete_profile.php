<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';
require_once __DIR__ . '/../lib/auth.php';

require_user();

$user_id = (int)$_SESSION['user_id'];
$error = '';
$success = '';

// Load current name
$res = mysqli_query($conn, "SELECT name FROM users WHERE id=".$user_id." LIMIT 1");
$row = $res ? mysqli_fetch_assoc($res) : null;
$current_name = $row ? $row['name'] : '';

// Helper: does name include a space (first + last)
function has_surname($name) {
    return strpos(trim((string)$name), ' ') !== false;
}

if (is_post()) {
    $name = trim(post_param('name'));
    if ($name === '') {
        $error = 'Full name is required.';
    } elseif (!has_surname($name)) {
        $error = 'Please include your surname (last name).';
    } else {
        $name_s = mysqli_real_escape_string($conn, $name);
        if (mysqli_query($conn, "UPDATE users SET name='".$name_s."' WHERE id=".$user_id." LIMIT 1")) {
            $_SESSION['user_name'] = $name;
            $success = 'Profile updated.';
            $next = get_param('next');
            if ($next === 'mobile') {
                redirect('/Ecomme/public/mobile_register.php?next=index');
            } else {
                redirect('/Ecomme/public/dashboard.php');
            }
        } else {
            $error = 'Failed to update name. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Complete Your Profile</title>
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
</head>
<body>
<div class="container" style="max-width:520px;margin:40px auto;">
    <div class="card">
        <h2>Complete Your Profile</h2>
        <p>Please enter your full name including surname (last name).</p>
        <?php if ($error): ?><div class="alert alert-error"><?php echo e($error); ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?php echo e($success); ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="name">Full Name (First and Last)</label>
                <input type="text" id="name" name="name" value="<?php echo e($current_name); ?>" required>
            </div>
            <button type="submit" class="btn">Save and Continue</button>
        </form>
    </div>
</div>
</body>
</html>


