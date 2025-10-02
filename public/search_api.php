<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../lib/helpers.php';

// Set header to return JSON
header('Content-Type: application/json');

// Get the search letter from the request
$letter = isset($_GET['letter']) ? $_GET['letter'] : '';

// Validate the letter (should be a single character)
if (strlen($letter) > 0) {
    $letter_s = mysqli_real_escape_string($conn, $letter);
    
    // Query products that start with the given letter
    $sql = "SELECT p.id, p.name, p.slug, p.price, p.image, c.name AS category_name 
            FROM products p 
            JOIN categories c ON c.id = p.category_id 
            WHERE p.is_active = 1 AND p.name LIKE '$letter_s%' 
            ORDER BY p.name ASC 
            LIMIT 10";
    
    $result = mysqli_query($conn, $sql);
    
    $products = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'slug' => $row['slug'],
                'price' => $row['price'],
                'image' => $row['image'],
                'category' => $row['category_name']
            ];
        }
    }
    
    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a search letter'
    ]);
}