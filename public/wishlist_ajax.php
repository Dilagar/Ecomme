<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

// Ensure we're handling an AJAX request
header('Content-Type: application/json');

if (!isset($_SESSION['wishlist'])) { 
    $_SESSION['wishlist'] = []; 
}

$response = ['success' => false, 'message' => 'Invalid request'];

// Add to wishlist
if (isset($_POST['product_id'])) {
    $pid = (int)$_POST['product_id'];
    $res = mysqli_query($conn, "SELECT id, stock FROM products WHERE id=$pid AND is_active=1 LIMIT 1");
    $p = $res ? mysqli_fetch_assoc($res) : null;
    
    if ($p && (int)$p['stock'] > 0) {
        $_SESSION['wishlist'][$pid] = 1;
        $response = [
            'success' => true, 
            'message' => 'Product added to wishlist',
            'wishlist_count' => count($_SESSION['wishlist'])
        ];
    } else {
        $response = ['success' => false, 'message' => 'Product is out of stock or not available'];
    }
}

echo json_encode($response);
exit;