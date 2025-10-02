<?php
require_once __DIR__ . '/../lib/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyShop - Modern E-commerce</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Ecomme/assets/styles.css">
    <link rel="stylesheet" href="/Ecomme/assets/modern-styles.css">
    <link rel="stylesheet" href="/Ecomme/public/assets/notifications.css">
</head>
<body>

<header class="header">
    <div class="header-top">
        <div class="container">
            <div style="display: flex; justify-content: space-between;">
                <div>
                    <span><i class="fas fa-phone-alt"></i> +91 9876543219</span>
                    <span class="ml-3"><i class="fas fa-envelope"></i>admin@example.com</span>
                </div>
                <div>
                    <?php if (!empty($_SESSION['user_id'])): ?>
                        <span>Welcome, <?php echo e($_SESSION['user_name']); ?>!</span>
                    <?php else: ?>
                        <a href="/Ecomme/public/login.php" style="color: white; margin-right: 15px; text-decoration: none;">
                            <i class="fas fa-user"></i> Login
                        </a>
                        <a href="/Ecomme/public/register.php" style="color: white; text-decoration: none;">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="header-main">
        <div class="container" style="display: flex; align-items: center; justify-content: space-between;">
            <div class="logo">
                <a href="/Ecomme/public/index.php">MyShop</a>
            </div>
            
            <div class="search-bar">
                <form class="search-form" action="/Ecomme/public/index.php" method="get">
                    <input type="text" name="q" id="search-input" class="search-input" placeholder="Search for products..." autocomplete="off">
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <style>
                .search-results-dropdown {
                    position: absolute;
                    width: 100%;
                    max-height: 400px;
                    overflow-y: auto;
                    background: white;
                    border: 1px solid #ddd;
                    border-radius: 0 0 4px 4px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    z-index: 1000;
                    display: none;
                }
                .search-result-item {
                    padding: 10px 15px;
                    border-bottom: 1px solid #eee;
                    display: flex;
                    align-items: center;
                    cursor: pointer;
                }
                .search-result-item:hover {
                    background-color: #f9f9f9;
                }
                .search-result-image {
                    width: 40px;
                    height: 40px;
                    object-fit: cover;
                    margin-right: 10px;
                }
                .search-result-info {
                    flex: 1;
                }
                .search-result-name {
                    font-weight: 500;
                    margin-bottom: 3px;
                }
                .search-result-price {
                    color: #e44d26;
                    font-weight: 600;
                }
                .search-result-category {
                    font-size: 12px;
                    color: #777;
                }
            </style>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const searchInput = document.getElementById('search-input');
                    const searchResults = document.getElementById('search-results');
                    
                    // Add event listener for input changes
                    searchInput.addEventListener('input', function() {
                        const letter = this.value.trim();
                        
                        // Clear results if input is empty
                        if (letter === '') {
                            searchResults.style.display = 'none';
                            searchResults.innerHTML = '';
                            return;
                        }
                        
                        // Fetch products starting with the letter
                        fetch(`/Ecomme/public/search_api.php?letter=${encodeURIComponent(letter)}`)
                            .then(response => response.json())
                            .then(data => {
                                searchResults.innerHTML = '';
                                
                                if (data.success && data.products.length > 0) {
                                    // Display search results
                                    data.products.forEach(product => {
                                        const resultItem = document.createElement('div');
                                        resultItem.className = 'search-result-item';
                                        resultItem.innerHTML = `
                                            <img src="/Ecomme/uploads/${product.image}" alt="${product.name}" class="search-result-image" onerror="this.src='/Ecomme/assets/placeholder.png'">
                                            <div class="search-result-info">
                                                <div class="search-result-name">${product.name}</div>
                                                <div class="search-result-price">₹${parseFloat(product.price).toFixed(2)}</div>
                                                <div class="search-result-category">${product.category}</div>
                                            </div>
                                        `;
                                        
                                        // Add click event to navigate to product page
                                        resultItem.addEventListener('click', function() {
                                            window.location.href = `/Ecomme/public/product.php?slug=${product.slug}`;
                                        });
                                        
                                        searchResults.appendChild(resultItem);
                                    });
                                    
                                    searchResults.style.display = 'block';
                                } else {
                                    // No results found
                                    const noResults = document.createElement('div');
                                    noResults.className = 'search-result-item';
                                    noResults.textContent = 'No products found';
                                    searchResults.appendChild(noResults);
                                    searchResults.style.display = 'block';
                                }
                            })
                            .catch(error => {
                                console.error('Error fetching search results:', error);
                            });
                    });
                    
                    // Hide search results when clicking outside
                    document.addEventListener('click', function(event) {
                        if (!searchInput.contains(event.target) && !searchResults.contains(event.target)) {
                            searchResults.style.display = 'none';
                        }
                    });
                });
            </script>
            </div>
            
            <div class="header-actions">
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <a href="/Ecomme/public/dashboard.php" class="action-icon">
                        <i class="fas fa-user-circle"></i>
                    </a>
                <?php endif; ?>
                
                <a href="/Ecomme/public/wishlist.php" class="action-icon">
                    <i class="fas fa-heart"></i>
                    <?php
                    $wishlist_count = 0;
                    if (!empty($_SESSION['user_id'])) {
                        // Check if wishlist table exists before querying
                        global $conn;
                        $user_id = $_SESSION['user_id'];
                        
                        // Use session-based wishlist count instead of database query
                        if (isset($_SESSION['wishlist_count'])) {
                            $wishlist_count = $_SESSION['wishlist_count'];
                        }
                    }
                    if ($wishlist_count > 0): ?>
                        <span class="badge"><?php echo $wishlist_count; ?></span>
                    <?php endif; ?>
                </a>
                
                <a href="/Ecomme/public/cart.php" class="action-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php
                    $cart_count = 0;
                    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            if (is_array($item) && isset($item['quantity'])) {
                                $cart_count += $item['quantity'];
                            }
                        }
                    }
                    if ($cart_count > 0): ?>
                        <span class="badge"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </div>
    
    <nav class="main-nav">
        <div class="container">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="/Ecomme/public/index.php" class="nav-link">Home</a>
                </li>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link">Categories</a>
                    <div class="dropdown-menu">
                        <?php
                        // Fetch categories from database
                        global $conn;
                        $cat_query = "SELECT id, name, slug FROM categories ORDER BY name";
                        $cat_result = mysqli_query($conn, $cat_query);
                        if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                            while ($category = mysqli_fetch_assoc($cat_result)) {
                                echo '<a href="/Ecomme/public/index.php?category=' . $category['id'] . '" class="dropdown-item">' . e($category['name']) . '</a>';
                            }
                        } else {
                            echo '<a href="#" class="dropdown-item">No categories found</a>';
                        }
                        ?>
                    </div>
                </li>
                <li class="nav-item">
                    <a href="/Ecomme/public/index.php" class="nav-link">Products</a>
                </li>
                <?php if (!empty($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a href="/Ecomme/public/dashboard.php" class="nav-link">My Account</a>
                    </li>
                    <li class="nav-item">
                        <a href="/Ecomme/public/logout.php" class="nav-link">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
</header>

<div class="container">
    <nav class="breadcrumb">
        <ul class="breadcrumb-list">
            <!-- <li class="breadcrumb-item">
                <a href="/Ecomme/public/index.php" class="breadcrumb-link">Home</a> 
            </li> -->
            <?php
            $current_page = basename($_SERVER['PHP_SELF']);
            
            switch($current_page) {
                case 'product.php':
                    echo '<li class="breadcrumb-item active"><a href="#" class="breadcrumb-link">Product Details</a></li>';
                    break;
                case 'cart.php':
                    echo '<li class="breadcrumb-item active"><a href="/Ecomme/public/cart.php" class="breadcrumb-link">Shopping Cart</a></li>';
                    break;
                case 'checkout.php':
                    echo '<li class="breadcrumb-item"><a href="/Ecomme/public/cart.php" class="breadcrumb-link">Cart</a></li>';
                    echo '<li class="breadcrumb-item active"><a href="/Ecomme/public/checkout.php" class="breadcrumb-link">Checkout</a></li>';
                    break;
                case 'login.php':
                    echo '<li class="breadcrumb-item active"><a href="/Ecomme/public/login.php" class="breadcrumb-link">Login</a></li>';
                    break;
                case 'register.php':
                    echo '<li class="breadcrumb-item active"><a href="/Ecomme/public/register.php" class="breadcrumb-link">Register</a></li>';
                    break;
                case 'dashboard.php':
                    echo '<li class="breadcrumb-item active"><a href="/Ecomme/public/dashboard.php" class="breadcrumb-link">My Account</a></li>';
                    break;
                case 'wishlist.php':
                    echo '<li class="breadcrumb-item active"><a href="/Ecomme/public/wishlist.php" class="breadcrumb-link">My Wishlist</a></li>';
                    break;
            }
            ?>
        </ul>
    </nav>