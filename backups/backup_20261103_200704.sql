SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned DEFAULT NULL,
  `action` varchar(60) NOT NULL,
  `details` varchar(500) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `activity_log` VALUES ('1','1','login','admin@demo.test','::1','2026-11-01 16:17:59');
INSERT INTO `activity_log` VALUES ('2','1','settings_saved','company_name,currency,session_timeout_min,low_stock_threshold,overstock_multiplier,supplier_delay_days,email_enabled,smtp_host,smtp_port,smtp_user,smtp_pass,mail_from,sms_enabled,sms_username,sms_api_key,forecast_schedule,forecast_horizons','::1','2026-11-01 16:19:29');
INSERT INTO `activity_log` VALUES ('3','1','logout','','::1','2026-11-01 16:20:03');
INSERT INTO `activity_log` VALUES ('4','6','account_requested','daggie841@gmail.com','::1','2026-11-01 16:20:51');
INSERT INTO `activity_log` VALUES ('5','1','login','admin@demo.test','::1','2026-11-01 16:20:58');
INSERT INTO `activity_log` VALUES ('6','1','request_approved','daggie841@gmail.com (role 5)','::1','2026-11-01 16:21:40');
INSERT INTO `activity_log` VALUES ('7','1','logout','','::1','2026-11-01 16:21:54');
INSERT INTO `activity_log` VALUES ('8','2','login','warehouse@demo.test','::1','2026-11-01 16:22:17');
INSERT INTO `activity_log` VALUES ('9','2','logout','','::1','2026-11-01 16:22:34');
INSERT INTO `activity_log` VALUES ('10','3','login','procurement@demo.test','::1','2026-11-01 16:22:59');
INSERT INTO `activity_log` VALUES ('11','3','logout','','::1','2026-11-01 16:23:22');
INSERT INTO `activity_log` VALUES ('12','4','login','sales@demo.test','::1','2026-11-01 16:23:28');
INSERT INTO `activity_log` VALUES ('13','4','logout','','::1','2026-11-01 16:23:59');
INSERT INTO `activity_log` VALUES ('14','5','login_failed','Bad password for manager@demo.test','::1','2026-11-01 16:24:44');
INSERT INTO `activity_log` VALUES ('15','2','login','warehouse@demo.test','::1','2026-11-01 16:24:50');
INSERT INTO `activity_log` VALUES ('16','2','logout','','::1','2026-11-01 16:24:52');
INSERT INTO `activity_log` VALUES ('17','5','login','manager@demo.test','::1','2026-11-01 16:24:59');
INSERT INTO `activity_log` VALUES ('18','2','login','warehouse@demo.test','::1','2026-11-03 21:47:06');
INSERT INTO `activity_log` VALUES ('19','2','logout','','::1','2026-11-03 21:47:11');
INSERT INTO `activity_log` VALUES ('20','1','login','admin@demo.test','::1','2026-11-03 21:47:17');
DROP TABLE IF EXISTS `alerts`;
CREATE TABLE `alerts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(40) NOT NULL,
  `severity` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `message` varchar(300) NOT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `status` enum('open','acknowledged','resolved') NOT NULL DEFAULT 'open',
  `notified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `categories` VALUES ('5','Accessories');
INSERT INTO `categories` VALUES ('4','CCTV');
INSERT INTO `categories` VALUES ('1','Laptops');
INSERT INTO `categories` VALUES ('3','Networking');
INSERT INTO `categories` VALUES ('2','Phones');
INSERT INTO `categories` VALUES ('6','Storage');
DROP TABLE IF EXISTS `chat_history`;
CREATE TABLE `chat_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `question` text NOT NULL,
  `answer` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `chat_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `forecasts`;
CREATE TABLE `forecasts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `horizon_days` smallint(5) unsigned NOT NULL,
  `predicted_qty` decimal(12,2) NOT NULL,
  `lower` decimal(12,2) DEFAULT NULL,
  `upper` decimal(12,2) DEFAULT NULL,
  `run_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`,`horizon_days`),
  KEY `run_at` (`run_at`),
  CONSTRAINT `forecasts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `goods_receipts`;
CREATE TABLE `goods_receipts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `po_id` int(10) unsigned NOT NULL,
  `received_by` int(10) unsigned DEFAULT NULL,
  `received_at` datetime NOT NULL DEFAULT current_timestamp(),
  `notes` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `po_id` (`po_id`),
  KEY `received_by` (`received_by`),
  CONSTRAINT `goods_receipts_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`),
  CONSTRAINT `goods_receipts_ibfk_2` FOREIGN KEY (`received_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `locations`;
CREATE TABLE `locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  `address` varchar(200) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `locations` VALUES ('1','Main Warehouse','Industrial Area','active');
INSERT INTO `locations` VALUES ('2','Town Shop','CBD','active');
INSERT INTO `locations` VALUES ('3','Showroom','Westlands','active');
DROP TABLE IF EXISTS `model_metrics`;
CREATE TABLE `model_metrics` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `model` varchar(30) NOT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  `mape` decimal(8,3) DEFAULT NULL,
  `rmse` decimal(12,3) DEFAULT NULL,
  `run_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `model_metrics_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `token_hash` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `token_hash` (`token_hash`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `po_items`;
CREATE TABLE `po_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `po_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `qty_ordered` int(11) NOT NULL,
  `qty_received` int(11) NOT NULL DEFAULT 0,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `po_id` (`po_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `po_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `po_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sku` varchar(40) NOT NULL,
  `barcode` varchar(60) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `category_id` int(10) unsigned DEFAULT NULL,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reorder_level` int(11) NOT NULL DEFAULT 10,
  `safety_stock` int(11) NOT NULL DEFAULT 5,
  `supplier_id` int(10) unsigned DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `category_id` (`category_id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `products` VALUES ('1','SKU0001','600000000001','Business Laptop 14\" i5','1','48000.00','59000.00','10','5','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('2','SKU0002','600000000002','Student Laptop 15\" i3','1','31000.00','38500.00','10','5','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('3','SKU0003','600000000003','Gaming Laptop 16\" Ryzen 7','1','95000.00','115000.00','10','5','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('4','SKU0004','600000000004','Ultrabook 13\" i7','1','82000.00','98000.00','10','5','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('5','SKU0005','600000000005','Chromebook 11\"','1','19000.00','24000.00','10','5','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('6','SKU0006','600000000006','Smartphone 128GB','2','17000.00','21500.00','10','5','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('7','SKU0007','600000000007','Smartphone 64GB','2','11000.00','14500.00','20','10','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('8','SKU0008','600000000008','Budget Phone 32GB','2','6000.00','8000.00','20','10','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('9','SKU0009','600000000009','Tablet 10\" 64GB','2','13500.00','17000.00','20','10','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('10','SKU0010','600000000010','Feature Phone Dual SIM','2','1500.00','2200.00','40','20','5','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('11','SKU0011','600000000011','WiFi Router AC1200','3','2800.00','4200.00','20','10','3','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('12','SKU0012','600000000012','WiFi Router AX3000','3','7500.00','10500.00','20','10','3','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('13','SKU0013','600000000013','24-Port Gigabit Switch','3','9500.00','13500.00','20','10','3','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('14','SKU0014','600000000014','8-Port Gigabit Switch','3','2200.00','3400.00','20','10','3','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('15','SKU0015','600000000015','Cat6 Cable 305m Box','3','9000.00','12500.00','20','10','5','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('16','SKU0016','600000000016','Wireless Access Point','3','4800.00','7000.00','20','10','3','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('17','SKU0017','600000000017','CCTV Dome Camera 2MP','4','2600.00','4000.00','20','10','4','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('18','SKU0018','600000000018','CCTV Bullet Camera 5MP','4','3800.00','5800.00','20','10','4','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('19','SKU0019','600000000019','8-Channel DVR','4','8500.00','12000.00','20','10','4','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('20','SKU0020','600000000020','Surveillance HDD 2TB','4','6500.00','8800.00','20','10','4','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('21','SKU0021','600000000021','Wireless Mouse','5','450.00','850.00','40','20','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('22','SKU0022','600000000022','USB Keyboard','5','600.00','1100.00','40','20','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('23','SKU0023','600000000023','Laptop Bag 15\"','5','900.00','1700.00','40','20','5','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('24','SKU0024','600000000024','HDMI Cable 2m','5','250.00','550.00','40','20','5','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('25','SKU0025','600000000025','Power Bank 20000mAh','5','1800.00','2800.00','40','20','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('26','SKU0026','600000000026','USB Flash Drive 64GB','6','550.00','950.00','40','20','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('27','SKU0027','600000000027','Portable SSD 1TB','6','9500.00','13000.00','20','10','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('28','SKU0028','600000000028','SD Card 128GB','6','1100.00','1800.00','40','20','1','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('29','SKU0029','600000000029','External HDD 2TB','6','6000.00','8200.00','20','10','2','active','2026-11-01 11:18:42');
INSERT INTO `products` VALUES ('30','SKU0030','600000000030','Internal SSD 500GB','6','4200.00','5900.00','20','10','1','active','2026-11-01 11:18:42');
DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `po_number` varchar(30) NOT NULL,
  `supplier_id` int(10) unsigned NOT NULL,
  `status` enum('draft','approved','sent','partial','closed','cancelled') NOT NULL DEFAULT 'draft',
  `source` enum('manual','system') NOT NULL DEFAULT 'manual',
  `created_by` int(10) unsigned DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `po_number` (`po_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `status` (`status`),
  KEY `created_by` (`created_by`),
  KEY `approved_by` (`approved_by`),
  CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`),
  CONSTRAINT `purchase_orders_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `receipt_items`;
CREATE TABLE `receipt_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `qty_accepted` int(11) NOT NULL DEFAULT 0,
  `qty_damaged` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `receipt_id` (`receipt_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `receipt_items_ibfk_1` FOREIGN KEY (`receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `receipt_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `reorder_recommendations`;
CREATE TABLE `reorder_recommendations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `suggested_qty` int(11) NOT NULL,
  `reorder_point` int(11) NOT NULL,
  `safety_stock` int(11) NOT NULL,
  `expected_stockout_date` date DEFAULT NULL,
  `run_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `reorder_recommendations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `label` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `roles` VALUES ('1','admin','System Administrator');
INSERT INTO `roles` VALUES ('2','warehouse','Warehouse / Store Officer');
INSERT INTO `roles` VALUES ('3','procurement','Procurement Officer');
INSERT INTO `roles` VALUES ('4','sales','Sales Staff');
INSERT INTO `roles` VALUES ('5','manager','Manager');
DROP TABLE IF EXISTS `sale_items`;
CREATE TABLE `sale_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_id` (`sale_id`),
  KEY `product_id` (`product_id`),
  KEY `location_id` (`location_id`),
  CONSTRAINT `sale_items_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `sale_items_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `sales`;
CREATE TABLE `sales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `receipt_no` varchar(30) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `status` enum('completed','void') NOT NULL DEFAULT 'completed',
  `user_id` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_no` (`receipt_no`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key` varchar(80) NOT NULL,
  `setting_value` text DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `settings` VALUES ('company_name','Inventory And S upply Chain Management');
INSERT INTO `settings` VALUES ('currency','KES');
INSERT INTO `settings` VALUES ('demo_data','0');
INSERT INTO `settings` VALUES ('email_enabled','0');
INSERT INTO `settings` VALUES ('forecast_horizons','7,30,90');
INSERT INTO `settings` VALUES ('forecast_schedule','02:00');
INSERT INTO `settings` VALUES ('low_stock_threshold','10');
INSERT INTO `settings` VALUES ('mail_from','noreply@example.com');
INSERT INTO `settings` VALUES ('overstock_multiplier','3');
INSERT INTO `settings` VALUES ('session_timeout_min','30');
INSERT INTO `settings` VALUES ('sms_api_key','');
INSERT INTO `settings` VALUES ('sms_enabled','0');
INSERT INTO `settings` VALUES ('sms_username','');
INSERT INTO `settings` VALUES ('smtp_host','');
INSERT INTO `settings` VALUES ('smtp_pass','');
INSERT INTO `settings` VALUES ('smtp_port','587');
INSERT INTO `settings` VALUES ('smtp_user','');
INSERT INTO `settings` VALUES ('supplier_delay_days','3');
DROP TABLE IF EXISTS `stock`;
CREATE TABLE `stock` (
  `product_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`product_id`,`location_id`),
  KEY `location_id` (`location_id`),
  CONSTRAINT `stock_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `stock` VALUES ('1','1','10');
INSERT INTO `stock` VALUES ('1','3','0');
INSERT INTO `stock` VALUES ('2','1','0');
INSERT INTO `stock` VALUES ('2','3','9');
INSERT INTO `stock` VALUES ('3','1','0');
INSERT INTO `stock` VALUES ('3','3','1');
INSERT INTO `stock` VALUES ('4','1','20');
INSERT INTO `stock` VALUES ('4','2','3');
INSERT INTO `stock` VALUES ('4','3','6');
INSERT INTO `stock` VALUES ('5','1','0');
INSERT INTO `stock` VALUES ('5','3','3');
INSERT INTO `stock` VALUES ('6','1','40');
INSERT INTO `stock` VALUES ('6','3','6');
INSERT INTO `stock` VALUES ('7','1','0');
INSERT INTO `stock` VALUES ('7','3','4');
INSERT INTO `stock` VALUES ('8','1','20');
INSERT INTO `stock` VALUES ('8','2','17');
INSERT INTO `stock` VALUES ('8','3','9');
INSERT INTO `stock` VALUES ('9','1','80');
INSERT INTO `stock` VALUES ('9','3','18');
INSERT INTO `stock` VALUES ('10','1','160');
INSERT INTO `stock` VALUES ('10','3','35');
INSERT INTO `stock` VALUES ('11','1','0');
INSERT INTO `stock` VALUES ('11','2','19');
INSERT INTO `stock` VALUES ('11','3','17');
INSERT INTO `stock` VALUES ('12','1','40');
INSERT INTO `stock` VALUES ('12','3','14');
INSERT INTO `stock` VALUES ('13','1','20');
INSERT INTO `stock` VALUES ('13','2','5');
INSERT INTO `stock` VALUES ('14','1','6');
INSERT INTO `stock` VALUES ('14','2','9');
INSERT INTO `stock` VALUES ('14','3','10');
INSERT INTO `stock` VALUES ('15','1','40');
INSERT INTO `stock` VALUES ('15','2','2');
INSERT INTO `stock` VALUES ('15','3','13');
INSERT INTO `stock` VALUES ('16','1','6');
INSERT INTO `stock` VALUES ('16','3','15');
INSERT INTO `stock` VALUES ('17','1','40');
INSERT INTO `stock` VALUES ('17','2','2');
INSERT INTO `stock` VALUES ('18','1','80');
INSERT INTO `stock` VALUES ('19','1','20');
INSERT INTO `stock` VALUES ('19','3','18');
INSERT INTO `stock` VALUES ('20','1','40');
INSERT INTO `stock` VALUES ('20','2','2');
INSERT INTO `stock` VALUES ('21','1','80');
INSERT INTO `stock` VALUES ('21','3','19');
INSERT INTO `stock` VALUES ('22','1','160');
INSERT INTO `stock` VALUES ('23','1','40');
INSERT INTO `stock` VALUES ('24','1','40');
INSERT INTO `stock` VALUES ('24','2','29');
INSERT INTO `stock` VALUES ('24','3','39');
INSERT INTO `stock` VALUES ('25','1','0');
INSERT INTO `stock` VALUES ('25','2','13');
INSERT INTO `stock` VALUES ('26','1','13');
INSERT INTO `stock` VALUES ('26','3','31');
INSERT INTO `stock` VALUES ('27','1','0');
INSERT INTO `stock` VALUES ('27','2','12');
INSERT INTO `stock` VALUES ('27','3','4');
INSERT INTO `stock` VALUES ('28','1','80');
INSERT INTO `stock` VALUES ('28','3','26');
INSERT INTO `stock` VALUES ('29','1','20');
INSERT INTO `stock` VALUES ('29','3','7');
INSERT INTO `stock` VALUES ('30','1','6');
INSERT INTO `stock` VALUES ('30','2','4');
INSERT INTO `stock` VALUES ('30','3','7');
DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE `stock_movements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `location_id` int(10) unsigned NOT NULL,
  `type` enum('receipt','sale','issue','transfer','adjustment') NOT NULL,
  `quantity` int(11) NOT NULL,
  `reference` varchar(60) DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `reason` varchar(200) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `location_id` (`location_id`),
  KEY `created_at` (`created_at`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `stock_movements_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `supplier_prices`;
CREATE TABLE `supplier_prices` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `price_date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `supplier_prices_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `supplier_prices_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `supplier_risk`;
CREATE TABLE `supplier_risk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` int(10) unsigned NOT NULL,
  `risk_score` tinyint(3) unsigned NOT NULL,
  `predicted_delay_days` decimal(5,1) DEFAULT NULL,
  `reason` varchar(250) DEFAULT NULL,
  `run_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `supplier_risk_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `lead_time_days` smallint(5) unsigned NOT NULL DEFAULT 7,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `suppliers` VALUES ('1','TechSource Kenya','Mary Wanjiru','sales@techsource.example','0711000001','5','active');
INSERT INTO `suppliers` VALUES ('2','Nairobi Digital Supplies','Peter Otieno','orders@nds.example','0711000002','7','active');
INSERT INTO `suppliers` VALUES ('3','Savanna Networks Ltd','Grace Achieng','info@savanna.example','0711000003','10','active');
INSERT INTO `suppliers` VALUES ('4','SecureView Distributors','Ali Hassan','sales@secureview.example','0711000004','14','active');
INSERT INTO `suppliers` VALUES ('5','Rift Valley Imports','John Kamau','rvi@imports.example','0711000005','21','active');
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role_id` tinyint(3) unsigned DEFAULT NULL,
  `status` enum('pending','active','disabled') NOT NULL DEFAULT 'pending',
  `failed_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `must_change` tinyint(1) NOT NULL DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `users` VALUES ('1','System Admin','admin@demo.test','$2y$10$A7XIgByKZoEO3vFZHsKIm.yQ43RAdhic4m0KJPAKPQK/9DxNpeDly','1','active','0',NULL,'0','2026-11-03 21:47:17','2026-11-01 11:18:42');
INSERT INTO `users` VALUES ('2','Wanjiku Store','warehouse@demo.test','$2y$10$A7XIgByKZoEO3vFZHsKIm.yQ43RAdhic4m0KJPAKPQK/9DxNpeDly','2','active','0',NULL,'0','2026-11-03 21:47:06','2026-11-01 11:18:42');
INSERT INTO `users` VALUES ('3','Otieno Buyer','procurement@demo.test','$2y$10$A7XIgByKZoEO3vFZHsKIm.yQ43RAdhic4m0KJPAKPQK/9DxNpeDly','3','active','0',NULL,'0','2026-11-01 16:22:59','2026-11-01 11:18:42');
INSERT INTO `users` VALUES ('4','Achieng Sales','sales@demo.test','$2y$10$A7XIgByKZoEO3vFZHsKIm.yQ43RAdhic4m0KJPAKPQK/9DxNpeDly','4','active','0',NULL,'0','2026-11-01 16:23:28','2026-11-01 11:18:42');
INSERT INTO `users` VALUES ('5','Kamau Manager','manager@demo.test','$2y$10$A7XIgByKZoEO3vFZHsKIm.yQ43RAdhic4m0KJPAKPQK/9DxNpeDly','5','active','0',NULL,'0','2026-11-01 16:24:59','2026-11-01 11:18:42');
INSERT INTO `users` VALUES ('6','douglas njuguna','daggie841@gmail.com','$2y$10$vI2jb4KUoU/.VbP/ih6x7OqNVWl75r.ML8F.KSXf0YCBuhxg9seW6','5','active','0',NULL,'0',NULL,'2026-11-01 16:20:51');
SET FOREIGN_KEY_CHECKS=1;
