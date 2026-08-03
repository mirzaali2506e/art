-- ============================================================
--  BeadCraft Store - Database Schema
--  Import this file into MySQL to create all tables.
--  Usage:  mysql -u root -p < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS beadcraft_store
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE beadcraft_store;

-- ----------------------------------------------------------
--  Admin users
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  full_name     VARCHAR(100) NOT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Product categories
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  parent_id   INT NULL,
  name        VARCHAR(150) NOT NULL,
  slug        VARCHAR(150) NOT NULL UNIQUE,
  description TEXT,
  image       VARCHAR(255),
  sort_order  INT DEFAULT 0,
  is_featured TINYINT(1) DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Products
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS products (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name        VARCHAR(200) NOT NULL,
  slug        VARCHAR(200) NOT NULL UNIQUE,
  description TEXT,
  price       DECIMAL(10,2) NOT NULL,
  sale_price  DECIMAL(10,2) NULL,
  stock       INT DEFAULT 0,
  image       VARCHAR(255),
  gallery     TEXT,            -- comma-separated extra image URLs
  is_featured TINYINT(1) DEFAULT 0,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Customers
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  phone         VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  address       TEXT,
  city          VARCHAR(100),
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Orders
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS orders (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  customer_id      INT NULL,
  customer_name    VARCHAR(100)  NOT NULL,
  customer_email   VARCHAR(150) NOT NULL,
  customer_phone   VARCHAR(20)  NOT NULL,
  shipping_address TEXT         NOT NULL,
  city             VARCHAR(100) NOT NULL,
  subtotal         DECIMAL(10,2) NOT NULL,
  shipping_fee     DECIMAL(10,2) NOT NULL DEFAULT 0,
  total            DECIMAL(10,2) NOT NULL,
  status           ENUM('pending','processing','shipped','delivered','cancelled')
                   DEFAULT 'pending',
  notes            TEXT,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Order items (line items for each order)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS order_items (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  order_id     INT NOT NULL,
  product_id   INT NULL,
  product_name VARCHAR(200) NOT NULL,
  price        DECIMAL(10,2) NOT NULL,
  quantity     INT NOT NULL,
  image        VARCHAR(255),
  FOREIGN KEY (order_id)   REFERENCES orders(id)   ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Product reviews
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  product_id  INT NOT NULL,
  name        VARCHAR(100) NOT NULL,
  rating      TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment     TEXT,
  is_approved TINYINT(1) DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Site settings (key-value store)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS settings (
  id    INT AUTO_INCREMENT PRIMARY KEY,
  skey  VARCHAR(100) NOT NULL UNIQUE,
  sval  TEXT
) ENGINE=InnoDB;

-- ----------------------------------------------------------
--  Indexes for performance
-- ----------------------------------------------------------
CREATE INDEX idx_products_category   ON products(category_id);
CREATE INDEX idx_products_featured   ON products(is_featured);
CREATE INDEX idx_products_active     ON products(is_active);
CREATE INDEX idx_categories_parent   ON categories(parent_id);
CREATE INDEX idx_orders_customer     ON orders(customer_id);
CREATE INDEX idx_orders_status       ON orders(status);
CREATE INDEX idx_order_items_order   ON order_items(order_id);
CREATE INDEX idx_reviews_product     ON reviews(product_id);
CREATE INDEX idx_reviews_approved    ON reviews(is_approved);

-- ----------------------------------------------------------
--  Default admin user
--  Username: admin   Password: admin123
--  (Change the password immediately after first login)
-- ----------------------------------------------------------
INSERT INTO admin_users (username, password_hash, full_name)
VALUES ('admin', '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy', 'Store Admin');

-- ----------------------------------------------------------
--  Default settings
-- ----------------------------------------------------------
INSERT INTO settings (skey, sval) VALUES
  ('site_name',     'BeadCraft Store'),
  ('whatsapp',      '03001234567'),
  ('email',         'info@beadcraftstore.com'),
  ('address',       'Karachi, Pakistan'),
  ('shipping_fee',  '320'),
  ('currency',      'PKR'),
  ('instagram',     'https://instagram.com'),
  ('facebook',      'https://facebook.com');
