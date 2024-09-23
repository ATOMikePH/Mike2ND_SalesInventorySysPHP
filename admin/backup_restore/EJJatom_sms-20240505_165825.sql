CREATE DATABASE IF NOT EXISTS `atom_sms`;

USE `atom_sms`;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `back_order_list`;

CREATE TABLE `back_order_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `receiving_id` int(30) NOT NULL,
  `po_id` int(30) NOT NULL,
  `bo_code` varchar(50) NOT NULL,
  `supplier_id` int(30) NOT NULL,
  `amount` float NOT NULL,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `tax_perc` float NOT NULL DEFAULT 0,
  `tax` float NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = pending, 1 = partially received, 2 =received',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaction_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `po_id` (`po_id`),
  KEY `receiving_id` (`receiving_id`),
  CONSTRAINT `back_order_list_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `back_order_list_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_order_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `back_order_list_ibfk_3` FOREIGN KEY (`receiving_id`) REFERENCES `receiving_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `bo_items`;

CREATE TABLE `bo_items` (
  `bo_id` int(30) NOT NULL,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `unit` varchar(50) NOT NULL,
  `total` float NOT NULL DEFAULT 0,
  KEY `item_id` (`item_id`),
  KEY `bo_id` (`bo_id`),
  CONSTRAINT `bo_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bo_items_ibfk_2` FOREIGN KEY (`bo_id`) REFERENCES `back_order_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `brands`;

CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `company` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `type` int(11) NOT NULL DEFAULT 4,
  `datetime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `brands` VALUES
('1', 'Boysen', 'Pacific Paint Philippines', '1', '2024-04-22 00:05:38', '4', '2024-04-25 00:18:42'),
('2', 'Generic', 'Universal Items with No specific brands.', '1', '2024-04-22 09:01:23', '4', '2024-04-25 00:18:42'),
('3', 'No Brand', '', '1', '2024-04-22 09:01:35', '4', '2024-04-25 00:18:42'),
('4', 'Wilcon', 'Wilcon Depot', '1', '2024-04-22 10:02:34', '4', '2024-04-25 00:18:42'),
('5', 'Davies', 'Davies Paints Philippines', '1', '2024-04-22 10:02:55', '4', '2024-04-25 00:18:42'),
('6', 'Republic Cement', '', '1', '2024-04-22 10:03:08', '4', '2024-04-25 00:18:42'),
('7', 'Holcim Philippines', '', '1', '2024-04-22 10:03:24', '4', '2024-04-25 00:18:42'),
('8', 'Trimar', 'Trimar Construction', '1', '2024-04-22 10:09:41', '4', '2024-04-25 00:18:42'),
('9', 'RCPJC', 'RCPJC Concrete Ready Mix & Development Corporation', '1', '2024-04-22 10:09:55', '4', '2024-04-25 00:18:42'),
('10', 'Pioneer', 'Pioneer Adhesives', '1', '2024-04-22 10:10:08', '4', '2024-04-25 00:18:42'),
('11', 'Neltex', 'Neltex Development Co., Inc.', '1', '2024-04-22 10:10:32', '4', '2024-04-25 00:18:42'),
('12', 'Stanley', 'Stanley Philippines', '1', '2024-04-22 10:10:48', '4', '2024-04-25 00:18:42'),
('13', 'DeWalt', 'DeWalt Philippines', '1', '2024-04-22 10:11:05', '4', '2024-04-25 00:18:42'),
('14', 'Welcoat', 'Boysen Philippines', '1', '2024-04-22 10:12:28', '4', '2024-04-25 00:18:42'),
('15', 'Union', 'Philcement Corporation', '1', '2024-04-22 10:14:12', '4', '2024-04-25 00:18:42'),
('17', 'Aiko', '', '1', '2024-04-24 10:43:47', '4', '2024-04-25 00:18:42'),
('18', 'America', '', '1', '2024-04-24 10:43:56', '4', '2024-04-25 00:18:42'),
('19', 'Bull', '', '1', '2024-04-24 10:44:05', '4', '2024-04-25 00:18:42'),
('20', 'Focus', '', '1', '2024-04-24 10:44:14', '4', '2024-04-25 00:18:42'),
('21', 'Goneo', '', '1', '2024-04-24 10:44:22', '4', '2024-04-25 00:18:42'),
('22', 'Koten', '', '1', '2024-04-24 10:44:28', '4', '2024-04-25 00:18:42'),
('23', 'Royu', '', '1', '2024-04-24 10:44:49', '4', '2024-04-28 22:34:41'),
('25', '11', '', '0', '2024-04-25 22:02:10', '4', '2024-04-25 22:02:14'),
('26', 'sadas', '', '0', '2024-04-25 22:10:49', '4', '2024-04-25 22:10:54');

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `type` int(11) NOT NULL DEFAULT 2,
  `datec` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` VALUES
('3', 'BLUE FITTINGS', '2024-04-22 10:17:11', '1', '2', '2024-04-25'),
('22', 'BLACK FITTINGS', '2024-04-22 10:17:19', '1', '2', '2024-04-25'),
('23', 'ELECTRICAL PIPES', '2024-04-22 10:17:34', '1', '2', '2024-04-25'),
('24', 'SANITARY', '2024-04-22 10:17:48', '1', '2', '2024-04-25'),
('25', 'PPR', '2024-04-22 10:18:07', '1', '2', '2024-04-25'),
('26', 'PAINTS', '2024-04-22 10:18:11', '1', '2', '2024-04-25'),
('27', 'ELECTRICAL', '2024-04-22 10:18:29', '1', '2', '2024-04-25'),
('28', 'OTHERS', '2024-04-22 10:18:44', '1', '2', '2024-04-25');

DROP TABLE IF EXISTS `cust_returnitem`;

CREATE TABLE `cust_returnitem` (
  `cusret_code` int(30) NOT NULL,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `unit` varchar(50) NOT NULL,
  `total` float NOT NULL DEFAULT 0,
  `item_condition` varchar(255) NOT NULL,
  KEY `fk_item_id` (`item_id`),
  KEY `fk_cust_returnitem_cusret_code` (`cusret_code`),
  CONSTRAINT `cust_returnitem_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`),
  CONSTRAINT `fk_cust_returnitem_cusret_code` FOREIGN KEY (`cusret_code`) REFERENCES `cust_returnlist` (`id`),
  CONSTRAINT `fk_item_id` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `cust_returnlist`;

CREATE TABLE `cust_returnlist` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `sales_order_id` int(30) NOT NULL,
  `customer` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_perc` float NOT NULL DEFAULT 0,
  `tax` decimal(10,2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `remarks` text DEFAULT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `discounts`;

CREATE TABLE `discounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `disc_per` float NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `type` int(11) NOT NULL DEFAULT 3,
  `datetime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `discounts` VALUES
('1', 'Senior Citizen Discount', '20', '1', '3', '2024-04-25 00:19:05'),
('2', 'PWD Discount', '20', '1', '3', '2024-04-25 00:19:05'),
('3', 'Student Discount', '10', '1', '3', '2024-04-25 00:19:05'),
('4', 'Government Employee', '15', '1', '3', '2024-04-25 00:19:05'),
('5', 'Birthday Discount', '25', '1', '3', '2024-04-25 00:19:05'),
('6', '50% Promotional', '50', '1', '3', '2024-04-25 00:19:05'),
('7', '20% Promotional', '20', '1', '3', '2024-04-25 00:19:05'),
('8', '25% Promotional', '25', '1', '3', '2024-04-25 00:19:05'),
('9', 'No Discount', '0', '1', '3', '2024-04-25 00:19:05'),
('10', 'Free Gift Discount', '100', '1', '3', '2024-04-25 00:19:05'),
('11', 'Business Fan Discount', '20', '1', '3', '2024-04-25 00:19:05'),
('12', '75% Promotional', '75', '1', '3', '2024-04-25 00:19:05'),
('15', '11test', '11', '0', '3', '2024-04-25 22:12:04');

DROP TABLE IF EXISTS `expenses`;

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(250) NOT NULL DEFAULT 'Others',
  `name` varchar(50) NOT NULL,
  `vendor` varchar(255) NOT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `remarks` varchar(255) NOT NULL,
  `description` text NOT NULL DEFAULT 'No description.',
  `transaction_date` date NOT NULL,
  `input_by` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `item_list`;

CREATE TABLE `item_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `brand_id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `image` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `cogs` float NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `datetime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `type` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=175 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `item_list` VALUES
('165', 'Flat Latex White B-701', '1', '26', 'PAI_001', '../uploads/item/flat latex white.jpg', '1', '0', '2024-04-23 23:09:09', '2024-04-28 21:21:04', '2024-04-28 21:21:04', '1'),
('166', 'LED', '17', '27', 'ELE_001', '../uploads/item/12W-LED-Bulb-Aiko.jpg', '1', '0', '2024-04-24 10:46:25', '2024-04-29 00:20:08', '2024-04-29 00:20:08', '1'),
('171', 'Threaded Tee', '3', '3', 'BLUFIT_001', '../uploads/item/THREADED-WATER-TEE1.jpg', '1', '0', '2024-04-28 21:23:24', '2024-04-28 21:23:24', '2024-04-28 21:23:24', '1'),
('172', 'Coupling', '3', '3', 'BLUFIT_002', '../uploads/item/8ca79cb9e5e527b3885f82c1cc43ff18.jpg', '1', '0', '2024-04-28 21:25:19', '2024-04-28 21:25:19', '2024-04-28 21:25:19', '1'),
('173', 'Union Patente', '3', '3', 'BLUFIT_003', '../uploads/item/dc7ebbe3afefb91b4a7381a2283dfe75.jpg', '1', '0', '2024-04-28 21:26:56', '2024-04-28 21:26:56', '2024-04-28 21:26:56', '1'),
('174', 'PVC Pipe', '3', '3', 'BLUFIT_004', '../uploads/item/R (1).jpg', '1', '0', '2024-04-28 21:28:45', '2024-04-28 21:28:55', '2024-04-28 21:28:55', '1');

DROP TABLE IF EXISTS `po_items`;

CREATE TABLE `po_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(30) NOT NULL,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `unit` varchar(50) NOT NULL,
  `total` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `po_id` (`po_id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `po_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_order_list` (`id`) ON DELETE CASCADE,
  CONSTRAINT `po_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `price_unit_order`;

CREATE TABLE `price_unit_order` (
  `pu_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `cost` float NOT NULL DEFAULT 0,
  `bbalance` int(50) NOT NULL DEFAULT 0,
  `reorder` int(50) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`pu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `purchase_order_list`;

CREATE TABLE `purchase_order_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `po_code` varchar(50) NOT NULL,
  `supplier_id` int(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_name` varchar(255) NOT NULL,
  `p_specify` varchar(255) NOT NULL,
  `tax_perc` float NOT NULL DEFAULT 0,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sub_tax` decimal(10,2) NOT NULL,
  `amount_tendered` decimal(10,2) NOT NULL,
  `p_mode` varchar(50) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remarks` text NOT NULL,
  `input_by` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = pending, 1 = partially received, 2 =received',
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaction_datetime` datetime NOT NULL,
  `sstatus` tinyint(1) NOT NULL DEFAULT 1,
  `type` int(11) NOT NULL DEFAULT 2,
  PRIMARY KEY (`id`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `purchase_order_list_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `receiving_list`;

CREATE TABLE `receiving_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `form_id` int(30) NOT NULL,
  `from_order` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=PO ,2 = BO',
  `amount` float NOT NULL DEFAULT 0,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `tax_perc` float NOT NULL DEFAULT 0,
  `tax` float NOT NULL DEFAULT 0,
  `stock_ids` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `input_by` varchar(50) NOT NULL,
  `transaction_datetime` datetime NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `sales_list`;

CREATE TABLE `sales_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `sales_code` varchar(50) NOT NULL,
  `client` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `discount_name` varchar(255) NOT NULL,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` float NOT NULL DEFAULT 0,
  `tax_perc` float NOT NULL DEFAULT 0,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sub_tax` decimal(10,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `stock_ids` text NOT NULL,
  `input_by` varchar(255) NOT NULL,
  `amount_tendered` decimal(10,2) NOT NULL,
  `p_mode` varchar(50) NOT NULL,
  `p_specify` varchar(255) NOT NULL,
  `change_amount` double(10,2) NOT NULL,
  `cog` decimal(10,2) NOT NULL DEFAULT 0.00,
  `cogs_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaction_datetime` datetime NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 1,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `role` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `security`;

CREATE TABLE `security` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `default_pin` text NOT NULL DEFAULT '670b14728ad9902aecba32e22fa4f6bd',
  `current_pin` text NOT NULL DEFAULT '670b14728ad9902aecba32e22fa4f6bd',
  `1_question` text NOT NULL DEFAULT '7b06ba5737320a90c0ead974d4638eaf',
  `1_answer` text NOT NULL,
  `2_question` text NOT NULL DEFAULT 'af58f9b732648289433c0e4c0da02666',
  `2_answer` text NOT NULL,
  `last_update_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `security` VALUES
('1', '670b14728ad9902aecba32e22fa4f6bd', '670b14728ad9902aecba32e22fa4f6bd\r\n', '7b06ba5737320a90c0ead974d4638eaf', '', 'af58f9b732648289433c0e4c0da02666', '', '2024-03-02 14:29:45');

DROP TABLE IF EXISTS `stock_list`;

CREATE TABLE `stock_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `cogs` float NOT NULL DEFAULT 0,
  `unit` varchar(250) DEFAULT NULL,
  `price` float NOT NULL DEFAULT 0,
  `total` float NOT NULL DEFAULT current_timestamp(),
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=IN , 2=OUT',
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  CONSTRAINT `stock_list_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=274 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `supp_returnlist`;

CREATE TABLE `supp_returnlist` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `return_code` varchar(50) NOT NULL,
  `supplier_id` int(30) NOT NULL DEFAULT 1,
  `category_id` int(30) NOT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_name` varchar(255) NOT NULL,
  `p_specify` varchar(255) NOT NULL,
  `tax_perc` float NOT NULL DEFAULT 0,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sub_tax` decimal(10,2) NOT NULL,
  `amount_tendered` decimal(10,2) NOT NULL,
  `p_mode` varchar(50) NOT NULL,
  `change_amount` decimal(10,2) NOT NULL,
  `remarks` text DEFAULT NULL,
  `stock_ids` text NOT NULL,
  `input_by` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaction_datetime` datetime NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `type` int(11) NOT NULL DEFAULT 4,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `supplier_list`;

CREATE TABLE `supplier_list` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `cperson` text NOT NULL,
  `contact` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `type` int(11) NOT NULL DEFAULT 5,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `datetime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `supplier_list` VALUES
('48', 'EJJ Construction Supplies Trading', '', 'Jiematt Caaya', '', '1', '5', '2024-04-22 10:00:20', '2024-04-24 21:06:06'),
('49', 'Subic Supplies Hardware Inc.', '', '', '', '1', '5', '2024-04-22 10:00:59', '2024-04-22 10:00:59'),
('50', 'Manila Exports Inc.', '', '', '', '1', '5', '2024-04-22 10:01:10', '2024-04-22 10:01:10'),
('51', 'Philippines National Hardware Trading', '', '', '', '1', '5', '2024-04-22 10:01:40', '2024-04-22 10:01:40'),
('56', 'sdsadsadas', '', '', '', '0', '5', '2024-04-25 21:52:45', '2024-04-25 21:52:48');

DROP TABLE IF EXISTS `supplier_product`;

CREATE TABLE `supplier_product` (
  `sp_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `supplier_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`sp_id`)
) ENGINE=InnoDB AUTO_INCREMENT=79 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `supplier_product` VALUES
('73', '48', '165', '15', '40.00', '1'),
('75', '48', '166', '28', '80.00', '1'),
('76', '48', '166', '29', '160.00', '1'),
('77', '48', '165', '14', '40.00', '1'),
('78', '48', '165', '16', '0.00', '1');

DROP TABLE IF EXISTS `system_info`;

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `system_info` VALUES
('1', 'name', 'EJJ Construction Supplies Trading'),
('2', 'tin_num', 'xxxx-xxxx-xxxx-xxxx'),
('3', 'company_address', 'Govic Highway Purok 7 San Nicolas, Castillejos, Zambales, Philippines'),
('4', 'phone_num', '(+63) 948 895 5360'),
('5', 'company_email', 'ejjconstructionsuppliestrading@gmail.com'),
('6', 'short_name', 'EJJTRACK v1.0b'),
('7', 'owner_name', 'Jiematt Caaya'),
('9', 'receipt_footer', 'Building Dreams, One Block at a Time. Together, let us build a solid foundation for your projects!'),
('11', 'logo', 'uploads/logo-1714731017.png'),
('13', 'user_avatar', 'uploads/user_avatar.jpg'),
('14', 'cover', 'uploads/img-bg.png'),
('15', 'content', 'Array'),
('16', 'MAX_FILE_SIZE', '2097152');

DROP TABLE IF EXISTS `system_log`;

CREATE TABLE `system_log` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `user_name` text DEFAULT NULL,
  `system` text DEFAULT NULL,
  `action_description` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `system_log` VALUES
('10', '1', '', '', 'succesfully Logged Out the System.', '2', '2024-05-05 22:58:09'),
('11', '1', 'ATOMUS', '', 'succesfully Logged In the System.', '1', '2024-05-05 22:58:19');

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 6,
  `datetime` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `units` VALUES
('1', 'PC', '1', '6', '2024-04-25 22:16:02'),
('2', 'BOX', '1', '6', '2024-04-25 22:16:02'),
('3', 'PACK', '1', '6', '2024-04-25 22:16:02'),
('4', 'SACK', '1', '6', '2024-04-25 22:16:02'),
('5', '1.5L', '1', '6', '2024-04-25 22:16:02'),
('7', '800ML', '1', '6', '2024-04-25 22:16:02'),
('8', '3/16x1/2 2.5mm', '1', '6', '2024-04-25 22:16:02'),
('9', '3/16x1/2 3mm', '1', '6', '2024-04-25 22:16:02'),
('10', '3/16x2', '1', '6', '2024-04-25 22:16:02'),
('14', 'GALLON', '1', '6', '2024-04-25 22:16:02'),
('15', 'PAIL', '1', '6', '2024-04-25 22:16:02'),
('16', '1 LITER', '1', '6', '2024-04-25 22:16:02'),
('17', '2 LITER', '1', '6', '2024-04-25 22:16:02'),
('18', '1.5 LITER', '1', '6', '2024-04-25 22:16:02'),
('19', '2mm PC', '1', '6', '2024-04-25 22:16:02'),
('20', '2mm BOX', '1', '6', '2024-04-25 22:16:02'),
('21', '3.5mm PC', '1', '6', '2024-04-25 22:16:02'),
('22', '3.5mm BOX', '1', '6', '2024-04-25 22:16:02'),
('23', '3/4x1/2', '1', '6', '2024-04-25 22:16:02'),
('24', '1/2', '1', '6', '2024-04-25 22:16:02'),
('25', '3/4', '1', '6', '2024-04-25 22:16:02'),
('27', '1/8 90 Degree', '1', '6', '2024-04-25 22:16:02'),
('28', '12 Wtz', '1', '6', '2024-04-25 22:16:02'),
('29', '15 Wtz', '1', '6', '2024-04-25 22:16:02'),
('30', '111', '0', '6', '2024-04-25 22:16:02');

DROP TABLE IF EXISTS `user_log`;

CREATE TABLE `user_log` (
  `id` int(30) NOT NULL AUTO_INCREMENT,
  `user_id` int(50) DEFAULT NULL,
  `item_name` text DEFAULT NULL,
  `supplier_name` text DEFAULT NULL,
  `category_name` text DEFAULT NULL,
  `expense_name` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `action_description` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=189 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `user_meta`;

CREATE TABLE `user_meta` (
  `user_id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(50) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `confirm_password` varchar(255) NOT NULL,
  `salutation` varchar(250) DEFAULT NULL,
  `avatar` text DEFAULT '\'uploads/avatar-2.jpg?v=1635920566\'',
  `last_login` datetime NOT NULL,
  `type` int(1) NOT NULL DEFAULT 0,
  `session_status` tinyint(1) DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES
('1', 'Programmer Michael', '', 'Cabalona', 'ATOMUS', '21232f297a57a5a743894a0e4a801fc3', '', '', 'uploads/avatar-1.png?v=1713542877', '2024-05-05 22:58:19', '1', '1', '2024-04-20 00:05:51', '2024-05-05 22:58:19'),
('2', 'Michael', '', 'Cabalona', 'ATOMUS1', '21232f297a57a5a743894a0e4a801fc3', '', 'Mr.', 'uploads/avatar-10.png?v=1709362917', '2024-04-20 00:03:52', '1', '0', '2021-11-03 14:21:28', '2024-04-20 00:03:52'),
('12', 'Stocks Inventory Personnel', '', ' ', 'user', 'ee11cbb19052e40b07aac0ca060c23ee', '', '', 'uploads/avatar-2.jpg?v=1635920566', '2024-04-19 23:22:06', '3', '0', '2023-12-15 00:12:36', '2024-04-19 23:22:06'),
('14', 'Jiematt', '', 'Caaya', 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 'Mr.', 'uploads/avatar-14.png?v=1710677464', '2024-04-25 14:27:03', '1', '0', '2024-03-02 15:07:54', '2024-04-25 14:27:03'),
('15', 'Accounting', '', 'Personnel', 'account', 'e268443e43d93dab7ebef303bbe9642f', '', '', 'uploads/avatar-15.png?v=1709530186', '2024-04-19 23:21:09', '2', '0', '2024-03-04 13:28:21', '2024-04-19 23:21:09'),
('24', 'Bartolome', '', 'Javillonar', 'bart', '21232f297a57a5a743894a0e4a801fc3', '', 'Mr.', 'uploads/avatar-24.png?v=1713544569', '2024-04-28 17:50:54', '1', '0', '2024-04-20 00:36:09', '2024-04-28 17:50:54');

DROP TABLE IF EXISTS `vendors`;

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendor_name` varchar(255) NOT NULL,
  `TIN_NUM` varchar(50) DEFAULT NULL,
  `vendor_address` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET foreign_key_checks = 1;

