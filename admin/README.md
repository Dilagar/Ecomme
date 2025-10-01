# Admin Panel Documentation

## Admin Login Information

### Access Path
The admin panel can be accessed at: `http://localhost/Ecomme/admin/index.php`

### Default Admin Account
To create an admin account, use the admin creation tool:
- URL: `http://localhost/Ecomme/admin/create_admin.php`
- Fill in your email and password (minimum 8 characters)

## Admin Panel Features

### Categories Management
- URL: `/Ecomme/admin/categories.php`
- Add, edit, and delete product categories
- Each category requires a name and slug

### Products Management
- URL: `/Ecomme/admin/products.php`
- Add, edit, and delete products
- Upload product images
- Set product details (name, price, description, stock)

### Orders Management
- URL: `/Ecomme/admin/orders.php`
- View all customer orders
- Check order details at `/Ecomme/admin/order_details.php?id=[order_id]`

## Security Information

- Admin passwords are securely encrypted using PHP's password_hash() with BCRYPT
- Session management prevents unauthorized access
- Input validation protects against common security vulnerabilities

## Troubleshooting

If you encounter login issues:
1. Ensure your database connection is working properly
2. Check that the admins table exists in your database
3. Verify that you're using the correct email and password
4. If you've forgotten your password, you'll need to reset it directly in the database

## Database Structure

The admin authentication uses the following database table:

```sql
CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```