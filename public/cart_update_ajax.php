<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get product ID and quantity from POST data
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
    
    // Validate inputs
    if ($product_id <= 0) {
        $response['message'] = 'Invalid product ID';
        echo json_encode($response);
        exit;
    }
    
    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    // Update cart
    if ($quantity <= 0) {
        // Remove item if quantity is zero or negative
        unset($_SESSION['cart'][$product_id]);
        $response['success'] = true;
        $response['message'] = 'Item removed from cart';
    } else {
        // Check if product exists and has stock
        $res = mysqli_query($conn, "SELECT id, stock FROM products WHERE id=$product_id AND is_active=1 LIMIT 1");
        $product = $res ? mysqli_fetch_assoc($res) : null;
        
        if ($product) {
            // Update quantity (limit to available stock)
            $available_stock = (int)$product['stock'];
            $final_quantity = min($quantity, $available_stock);
            
            $_SESSION['cart'][$product_id] = $final_quantity;
            
            $response['success'] = true;
            $response['message'] = 'Cart updated successfully';
            $response['data']['quantity'] = $final_quantity;
        } else {
            $response['message'] = 'Product not found or inactive';
        }
    }
    
    // Calculate new cart totals
    $subtotal = 0;
    $items = [];
    
    if (!empty($_SESSION['cart'])) {
        $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
        $res = mysqli_query($conn, "SELECT id, price FROM products WHERE id IN ($ids) AND is_active=1");
        
        while ($row = mysqli_fetch_assoc($res)) {
            $qty = (int)($_SESSION['cart'][$row['id']] ?? 0);
            if ($qty > 0) {
                $line_total = $qty * (float)$row['price'];
                $subtotal += $line_total;
            }
        }
    }
    
    $response['data']['subtotal'] = $subtotal;
    $response['data']['total'] = $subtotal; // No shipping/tax for simplicity
} else {
    $response['message'] = 'Invalid request method';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>