-- Migration: Add cart_items table for persistent carts
-- Run this once to add the cart_items table to your existing database.

USE beadcraft_store;

CREATE TABLE IF NOT EXISTS cart_items (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  customer_id  INT NOT NULL,
  product_id   INT NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  price        DECIMAL(10,2) NOT NULL,
  image        VARCHAR(500),
  quantity     INT NOT NULL DEFAULT 1,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;
