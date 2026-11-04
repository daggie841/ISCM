-- Inventory AI schema (MySQL 8 / MariaDB 10.4+). Import after creating the database.
CREATE DATABASE IF NOT EXISTS inventory_ai CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventory_ai;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE roles (
  id TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(30) NOT NULL UNIQUE,
  label VARCHAR(60) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE users (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role_id TINYINT UNSIGNED NULL,
  status ENUM('pending','active','disabled') NOT NULL DEFAULT 'pending',
  failed_attempts TINYINT UNSIGNED NOT NULL DEFAULT 0,
  locked_until DATETIME NULL,
  must_change TINYINT(1) NOT NULL DEFAULT 0,
  last_login DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE password_resets (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  token_hash CHAR(64) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (token_hash),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE categories (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE locations (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL UNIQUE,
  address VARCHAR(200) NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

CREATE TABLE suppliers (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  contact VARCHAR(100) NULL,
  email VARCHAR(150) NULL,
  phone VARCHAR(30) NULL,
  lead_time_days SMALLINT UNSIGNED NOT NULL DEFAULT 7,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB;

CREATE TABLE products (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  sku VARCHAR(40) NOT NULL UNIQUE,
  barcode VARCHAR(60) NULL UNIQUE,
  name VARCHAR(150) NOT NULL,
  category_id INT UNSIGNED NULL,
  unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  reorder_level INT NOT NULL DEFAULT 10,
  safety_stock INT NOT NULL DEFAULT 5,
  supplier_id INT UNSIGNED NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (category_id), INDEX (supplier_id),
  FOREIGN KEY (category_id) REFERENCES categories(id),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB;

CREATE TABLE stock (
  product_id INT UNSIGNED NOT NULL,
  location_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  PRIMARY KEY (product_id, location_id),
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB;

CREATE TABLE stock_movements (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  location_id INT UNSIGNED NOT NULL,
  type ENUM('receipt','sale','issue','transfer','adjustment') NOT NULL,
  quantity INT NOT NULL,
  reference VARCHAR(60) NULL,
  user_id INT UNSIGNED NULL,
  reason VARCHAR(200) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (product_id), INDEX (location_id), INDEX (created_at),
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (location_id) REFERENCES locations(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE supplier_prices (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  supplier_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  price DECIMAL(12,2) NOT NULL,
  price_date DATE NOT NULL,
  INDEX (supplier_id), INDEX (product_id),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE purchase_orders (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  po_number VARCHAR(30) NOT NULL UNIQUE,
  supplier_id INT UNSIGNED NOT NULL,
  status ENUM('draft','approved','sent','partial','closed','cancelled') NOT NULL DEFAULT 'draft',
  source ENUM('manual','system') NOT NULL DEFAULT 'manual',
  created_by INT UNSIGNED NULL,
  approved_by INT UNSIGNED NULL,
  expected_date DATE NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  approved_at DATETIME NULL,
  sent_at DATETIME NULL,
  INDEX (supplier_id), INDEX (status),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
  FOREIGN KEY (created_by) REFERENCES users(id),
  FOREIGN KEY (approved_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE po_items (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  po_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  qty_ordered INT NOT NULL,
  qty_received INT NOT NULL DEFAULT 0,
  unit_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
  INDEX (po_id), INDEX (product_id),
  FOREIGN KEY (po_id) REFERENCES purchase_orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE goods_receipts (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  po_id INT UNSIGNED NOT NULL,
  received_by INT UNSIGNED NULL,
  received_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  notes VARCHAR(300) NULL,
  INDEX (po_id),
  FOREIGN KEY (po_id) REFERENCES purchase_orders(id),
  FOREIGN KEY (received_by) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE receipt_items (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  receipt_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  qty_accepted INT NOT NULL DEFAULT 0,
  qty_damaged INT NOT NULL DEFAULT 0,
  INDEX (receipt_id),
  FOREIGN KEY (receipt_id) REFERENCES goods_receipts(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE sales (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  receipt_no VARCHAR(30) NOT NULL UNIQUE,
  customer_name VARCHAR(100) NULL,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('completed','void') NOT NULL DEFAULT 'completed',
  user_id INT UNSIGNED NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (created_at),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE sale_items (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  sale_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  location_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL,
  INDEX (sale_id), INDEX (product_id),
  FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB;

CREATE TABLE forecasts (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  horizon_days SMALLINT UNSIGNED NOT NULL,
  predicted_qty DECIMAL(12,2) NOT NULL,
  lower DECIMAL(12,2) NULL,
  upper DECIMAL(12,2) NULL,
  run_at DATETIME NOT NULL,
  INDEX (product_id, horizon_days), INDEX (run_at),
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE model_metrics (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  model VARCHAR(30) NOT NULL,
  product_id INT UNSIGNED NULL,
  mape DECIMAL(8,3) NULL,
  rmse DECIMAL(12,3) NULL,
  run_at DATETIME NOT NULL,
  INDEX (product_id),
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE reorder_recommendations (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  product_id INT UNSIGNED NOT NULL,
  suggested_qty INT NOT NULL,
  reorder_point INT NOT NULL,
  safety_stock INT NOT NULL,
  expected_stockout_date DATE NULL,
  run_at DATETIME NOT NULL,
  INDEX (product_id),
  FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB;

CREATE TABLE supplier_risk (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  supplier_id INT UNSIGNED NOT NULL,
  risk_score TINYINT UNSIGNED NOT NULL,
  predicted_delay_days DECIMAL(5,1) NULL,
  reason VARCHAR(250) NULL,
  run_at DATETIME NOT NULL,
  INDEX (supplier_id),
  FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB;

CREATE TABLE alerts (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  type VARCHAR(40) NOT NULL,
  severity ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  message VARCHAR(300) NOT NULL,
  product_id INT UNSIGNED NULL,
  supplier_id INT UNSIGNED NULL,
  status ENUM('open','acknowledged','resolved') NOT NULL DEFAULT 'open',
  notified TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (status), INDEX (created_at)
) ENGINE=InnoDB;

CREATE TABLE chat_history (
  id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNSIGNED NOT NULL,
  question TEXT NOT NULL,
  answer TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id),
  FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB;

CREATE TABLE settings (
  setting_key VARCHAR(80) PRIMARY KEY,
  setting_value TEXT NULL
) ENGINE=InnoDB;

CREATE TABLE activity_log (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id INT UNSIGNED NULL,
  action VARCHAR(60) NOT NULL,
  details VARCHAR(500) NULL,
  ip VARCHAR(45) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX (user_id), INDEX (action), INDEX (created_at)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;
