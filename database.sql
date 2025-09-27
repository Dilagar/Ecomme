-- Core E-commerce Schema
-- Engine: InnoDB, Charset: utf8mb4

SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE TABLE IF NOT EXISTS admins (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(190) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) DEFAULT NULL,
  google_id VARCHAR(190) DEFAULT NULL,
  name VARCHAR(190) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_users_google_id (google_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(190) NOT NULL,
  slug VARCHAR(190) NOT NULL UNIQUE,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  description TEXT,
  image VARCHAR(255) DEFAULT NULL,
  stock INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_products_category_id (category_id),
  CONSTRAINT fk_products_category_id FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS carts (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_carts_user_id (user_id),
  CONSTRAINT fk_carts_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS cart_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cart_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_cart_product (cart_id, product_id),
  CONSTRAINT fk_cart_items_cart_id FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  CONSTRAINT fk_cart_items_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS wishlists (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_wishlists_user_id (user_id),
  CONSTRAINT fk_wishlists_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS wishlist_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  wishlist_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_wishlist_product (wishlist_id, product_id),
  CONSTRAINT fk_wishlist_items_wishlist_id FOREIGN KEY (wishlist_id) REFERENCES wishlists(id) ON DELETE CASCADE,
  CONSTRAINT fk_wishlist_items_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS addresses (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  full_name VARCHAR(190) NOT NULL,
  line1 VARCHAR(255) NOT NULL,
  line2 VARCHAR(255) DEFAULT NULL,
  city VARCHAR(120) NOT NULL,
  state VARCHAR(120) DEFAULT NULL,
  postal_code VARCHAR(40) NOT NULL,
  country VARCHAR(120) NOT NULL,
  phone VARCHAR(60) DEFAULT NULL,
  is_default TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_addresses_user_id (user_id),
  CONSTRAINT fk_addresses_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  address_id INT UNSIGNED NOT NULL,
  status ENUM('on_process','shipped','delivered','cancelled') NOT NULL DEFAULT 'on_process',
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_orders_user_id (user_id),
  KEY idx_orders_address_id (address_id),
  CONSTRAINT fk_orders_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_orders_address_id FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS order_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (id),
  KEY idx_order_items_order_id (order_id),
  CONSTRAINT fk_order_items_order_id FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_order_items_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed an initial admin (change password after import)
INSERT INTO admins (email, password)
VALUES ('admin@example.com', 'Admin@123')
ON DUPLICATE KEY UPDATE email = email;

-- Seed categories
INSERT INTO categories (name, slug) VALUES
('Electronics', 'electronics'),
('Sports', 'sports'),
('Books', 'books'),
('Video Games', 'video-games'),
('Others', 'others')
ON DUPLICATE KEY UPDATE name = name;



