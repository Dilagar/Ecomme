# E-commerce Project Documentation

## Admin Access Information
- **Admin Login Path**: `/Ecomme/admin/index.php`
- **Default Admin Credentials**:
  - Email: admin@example.com
  - Password: admin123
  
## Project Navigation Guide

### Public Pages
- **Home**: `/Ecomme/public/index.php` - Main product listing
- **Product Details**: `/Ecomme/public/product.php?id=[product_id]` - View single product
- **Login**: `/Ecomme/public/login.php` - User login
- **Register**: `/Ecomme/public/register.php` - User registration
- **Dashboard**: `/Ecomme/public/dashboard.php` - User account dashboard
- **Cart**: `/Ecomme/public/cart.php` - Shopping cart
- **Checkout**: `/Ecomme/public/checkout.php` - Checkout process
- **Order Success**: `/Ecomme/public/order_success.php` - Order confirmation
- **Wishlist**: `/Ecomme/public/wishlist.php` - User wishlist

### Admin Pages
- **Admin Login**: `/Ecomme/admin/index.php` - Admin authentication
- **Categories**: `/Ecomme/admin/categories.php` - Manage product categories
- **Products**: `/Ecomme/admin/products.php` - Manage products
- **Orders**: `/Ecomme/admin/orders.php` - View and manage orders
- **Order Details**: `/Ecomme/admin/order_details.php?id=[order_id]` - View specific order

## Security Notes
- Admin passwords are now encrypted using PHP's password_hash() function
- User passwords are encrypted using bcrypt hashing

## Development Notes
- The project uses PHP with MySQL database
- CSS styling is located in `/Ecomme/assets/styles.css`
- Product images are stored in the `/Ecomme/uploads/` directory