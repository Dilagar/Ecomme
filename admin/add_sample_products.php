<?php
require_once __DIR__ . '/../config/db.php';

// First, ensure we have at least one category
$check_categories = mysqli_query($conn, "SELECT COUNT(*) as count FROM categories");
$category_count = mysqli_fetch_assoc($check_categories)['count'];

if ($category_count == 0) {
    // Add sample categories if none exist
    $categories = [
        ['name' => 'Electronics', 'slug' => 'electronics'],
        ['name' => 'Clothing', 'slug' => 'clothing'],
        ['name' => 'Books', 'slug' => 'books'],
        ['name' => 'Home & Kitchen', 'slug' => 'home-kitchen']
    ];
    
    foreach ($categories as $category) {
        mysqli_query($conn, "INSERT INTO categories (name, slug) VALUES ('{$category['name']}', '{$category['slug']}')");
    }
    
    echo "Added sample categories.<br>";
}

// Get category IDs
$categories_result = mysqli_query($conn, "SELECT id, name FROM categories");
$categories = [];
while ($row = mysqli_fetch_assoc($categories_result)) {
    $categories[$row['name']] = $row['id'];
}

// Check if products already exist
$check_products = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
$product_count = mysqli_fetch_assoc($check_products)['count'];

if ($product_count == 0) {
    // Sample products data
    $products = [
        [
            'name' => 'Smartphone X',
            'slug' => 'smartphone-x',
            'description' => 'Latest smartphone with advanced features',
            'price' => 599.99,
            'stock' => 50,
            'category' => 'Electronics',
            'is_active' => 1
        ],
        [
            'name' => 'Laptop Pro',
            'slug' => 'laptop-pro',
            'description' => 'Powerful laptop for professionals',
            'price' => 1299.99,
            'stock' => 25,
            'category' => 'Electronics',
            'is_active' => 1
        ],
        [
            'name' => 'Men\'s T-Shirt',
            'slug' => 'mens-tshirt',
            'description' => 'Comfortable cotton t-shirt',
            'price' => 24.99,
            'stock' => 100,
            'category' => 'Clothing',
            'is_active' => 1
        ],
        [
            'name' => 'Women\'s Jeans',
            'slug' => 'womens-jeans',
            'description' => 'Stylish and durable jeans',
            'price' => 49.99,
            'stock' => 75,
            'category' => 'Clothing',
            'is_active' => 1
        ],
        [
            'name' => 'Programming Guide',
            'slug' => 'programming-guide',
            'description' => 'Comprehensive guide to modern programming',
            'price' => 34.99,
            'stock' => 30,
            'category' => 'Books',
            'is_active' => 1
        ],
        [
            'name' => 'Coffee Maker',
            'slug' => 'coffee-maker',
            'description' => 'Automatic coffee maker for home use',
            'price' => 79.99,
            'stock' => 40,
            'category' => 'Home & Kitchen',
            'is_active' => 1
        ]
    ];
    
    // Insert products
    foreach ($products as $product) {
        $category_id = isset($categories[$product['category']]) ? $categories[$product['category']] : 1;
        
        $sql = "INSERT INTO products (name, slug, description, price, stock, category_id, is_active) 
                VALUES (
                    '{$product['name']}', 
                    '{$product['slug']}', 
                    '{$product['description']}', 
                    {$product['price']}, 
                    {$product['stock']}, 
                    {$category_id}, 
                    {$product['is_active']}
                )";
        
        if (mysqli_query($conn, $sql)) {
            echo "Added product: {$product['name']}<br>";
        } else {
            echo "Error adding product {$product['name']}: " . mysqli_error($conn) . "<br>";
        }
    }
    
    echo "<p>Sample products have been added successfully!</p>";
} else {
    echo "<p>Products already exist in the database. No sample products were added.</p>";
}

echo "<p><a href='/Ecomme/public/index.php'>Go to homepage</a></p>";
?>