<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

// Ensure we're handling an AJAX request
header('Content-Type: application/json');

if (!isset($_SESSION['cart'])) { 
    $_SESSION['cart'] = []; 
}

$response = ['success' => false, 'message' => 'Invalid request'];

// Add to cart
if (isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Check if product exists and is in stock
    $check_product = mysqli_query($conn, "SELECT id, stock FROM products WHERE id=$product_id AND is_active=1 LIMIT 1");
    if ($check_product && mysqli_num_rows($check_product) > 0) {
        $product = mysqli_fetch_assoc($check_product);
        if ((int)$product['stock'] > 0) {
            // Add to cart
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = $quantity;
            } else {
                $_SESSION['cart'][$product_id] += $quantity;
            }
            
            $response = [
                'success' => true, 
                'message' => 'Product added to cart',
                'cart_count' => array_sum($_SESSION['cart'])
            ];
        } else {
            $response = ['success' => false, 'message' => 'Product is out of stock'];
        }
    } else {
        $response = ['success' => false, 'message' => 'Product not found'];
    }
}

echo json_encode($response);
exit;