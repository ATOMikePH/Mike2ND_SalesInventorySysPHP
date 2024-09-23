-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 16, 2024 at 04:57 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `atom_sms`
--

-- --------------------------------------------------------

--
-- Table structure for table `back_order_list`
--

CREATE TABLE `back_order_list` (
  `id` int(30) NOT NULL,
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
  `transaction_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `back_order_list`
--

INSERT INTO `back_order_list` (`id`, `receiving_id`, `po_id`, `bo_code`, `supplier_id`, `amount`, `discount_perc`, `discount`, `tax_perc`, `tax`, `remarks`, `status`, `date_created`, `date_updated`, `transaction_datetime`) VALUES
(5, 30, 37, 'BO-0001', 41, 11.2, 0, 0, 12, 1.2, NULL, 0, '2024-04-16 09:44:55', '2024-04-16 09:44:55', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `bo_items`
--

CREATE TABLE `bo_items` (
  `bo_id` int(30) NOT NULL,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `unit` varchar(50) NOT NULL,
  `total` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bo_items`
--

INSERT INTO `bo_items` (`bo_id`, `item_id`, `quantity`, `price`, `unit`, `total`) VALUES
(5, 131, 1, 10, 'PC', 10);

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(30) NOT NULL,
  `name` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `datetime`, `status`) VALUES
(1, 'BLUE FITTINGS', '2023-12-14 15:59:07', 1),
(2, 'BLACK FITTINGS', '2023-12-14 16:03:55', 1),
(3, 'ELECTRICAL PIPES', '2023-12-14 16:03:55', 1),
(4, 'SANITARY', '2023-12-14 16:03:55', 1),
(5, 'PPR', '2023-12-14 16:03:55', 1),
(6, 'PAINTS', '2023-12-14 16:03:55', 1),
(7, 'ELECTRICAL', '2023-12-14 16:03:55', 1),
(18, 'OTHERS', '2024-03-02 15:24:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cust_returnitem`
--

CREATE TABLE `cust_returnitem` (
  `cusret_code` int(30) NOT NULL,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `unit` varchar(50) NOT NULL,
  `total` float NOT NULL DEFAULT 0,
  `item_condition` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cust_returnlist`
--

CREATE TABLE `cust_returnlist` (
  `id` int(30) NOT NULL,
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
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `type` varchar(250) NOT NULL DEFAULT 'Others',
  `name` varchar(50) NOT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `remarks` varchar(255) NOT NULL,
  `description` text NOT NULL DEFAULT 'No description.',
  `transaction_date` date NOT NULL,
  `input_by` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `type`, `name`, `amount`, `remarks`, `description`, `transaction_date`, `input_by`, `date_created`, `date_updated`) VALUES
(46, 'Utilities', 'Electricity - Month of February', 1800, 'Paid', '', '2024-02-15', 'Michael Cabalona', '2024-03-14 11:13:19', '2024-03-14 11:16:53'),
(47, 'Utilities', 'Electricity - Month of March', 1600, 'Paid', '', '2024-03-14', 'Michael Cabalona', '2024-03-14 11:14:29', '2024-03-14 11:14:29'),
(48, 'Miscellaneous', 'Labor Fee', 500, 'Paid', '', '2024-03-20', 'Michael Cabalona', '2024-03-25 12:28:24', '2024-03-25 12:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `item_list`
--

CREATE TABLE `item_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL DEFAULT 'N/A',
  `category_id` int(30) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 1.00,
  `bbalance` int(50) DEFAULT NULL,
  `sku` varchar(255) NOT NULL,
  `image` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `cogs` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_list`
--

INSERT INTO `item_list` (`id`, `name`, `description`, `category_id`, `cost`, `bbalance`, `sku`, `image`, `status`, `date_created`, `date_updated`, `cogs`) VALUES
(131, ' Threaded Tee 3/4', 'No Description', 1, 103.00, 151, 'BLUFIT_001', '../uploads/item/BLUE-UPVC-TEE-THREAD-25MM-341-600x600.jpg', 1, '2024-03-02 15:12:29', '2024-03-31 17:20:42', 0.00),
(132, 'Coupling 1', 'No Description', 1, 100.00, 50, '', '', 1, '2024-03-02 15:13:27', '2024-03-02 15:13:27', 0.00),
(133, 'Tee 2x3', 'No Description', 2, 100.00, 0, '', '', 1, '2024-03-02 15:18:06', '2024-03-02 15:18:06', 0.00),
(134, 'Wye 4x2', 'No Description', 2, 100.00, 0, '', '', 1, '2024-03-02 15:18:40', '2024-03-02 15:18:40', 0.00),
(135, 'Atlas Short Elbow 3/4', 'No Description', 3, 100.00, 40, '', '', 1, '2024-03-02 15:19:54', '2024-03-02 15:19:54', 0.00),
(136, 'PVC Clamp 3/4', 'No Description', 3, 100.00, 500, '', '', 1, '2024-03-02 15:20:38', '2024-03-02 15:20:38', 0.00),
(137, 'Clean Out 4', 'No Description', 4, 100.00, 64, 'SAN_001', '', 1, '2024-03-02 15:21:15', '2024-03-31 19:32:43', 0.00),
(138, 'Sundex Tee 3x4', 'No Description', 4, 100.00, 11, '', '', 1, '2024-03-02 15:21:36', '2024-03-02 15:21:54', 0.00),
(139, 'PPR Male Tee Adapter 1/2', 'No Description', 5, 100.00, 200, '', '', 1, '2024-03-02 15:22:30', '2024-03-02 15:22:30', 0.00),
(140, 'Union Patente 1/2', 'No Description', 5, 100.00, 207, '', '', 1, '2024-03-02 15:22:44', '2024-03-02 15:22:44', 0.00),
(141, 'Boysen Flat Latex White B-701', 'Pail', 6, 100.00, 22, 'PAI_001', '', 1, '2024-03-02 15:23:15', '2024-03-31 19:27:57', 0.00),
(142, 'Boysen Flat Wall Enamel B800', 'Gallon', 6, 100.00, 4, 'PAI_002', '', 1, '2024-03-02 15:23:35', '2024-03-31 19:28:19', 0.00),
(143, 'America Uni Adapter White', 'No Description', 7, 100.00, 10, 'ELE_001', '../uploads/item/baf75ed5-c86a-4bd7-9143-6b107fe556db.png', 1, '2024-03-02 15:23:54', '2024-03-31 19:33:30', 0.00),
(144, 'Phelps Dodges 2mm', 'No Description', 7, 100.00, 2, '', '', 1, '2024-03-02 15:24:08', '2024-03-02 15:24:08', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `journalentry`
--

CREATE TABLE `journalentry` (
  `EntryID` int(11) NOT NULL,
  `EntryDate` date DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `DebitAccount` int(11) DEFAULT NULL,
  `CreditAccount` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pi_items`
--

CREATE TABLE `pi_items` (
  `idd` int(11) NOT NULL,
  `invoice_id` int(30) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `quantity` int(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pi_items`
--

INSERT INTO `pi_items` (`idd`, `invoice_id`, `item_name`, `quantity`, `price`, `unit`, `total`) VALUES
(2, 3, 'Gas', 9, 50.00, 'PC', 450.00),
(3, 4, 'kilo', 5, 11.00, 'PC', 55.00),
(7, 5, 'Gas', 1, 50.00, 'PC', 50.00),
(8, 6, 'Gas', 1, 50.00, 'PC', 50.00),
(9, 7, 'Gas', 12, 12.00, '12', 144.00),
(10, 8, 'Gas', 2, 2.00, '12', 4.00);

-- --------------------------------------------------------

--
-- Table structure for table `po_items`
--

CREATE TABLE `po_items` (
  `id` int(11) NOT NULL,
  `po_id` int(30) NOT NULL,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `price` float NOT NULL DEFAULT 0,
  `unit` varchar(50) NOT NULL,
  `total` float NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_items`
--

INSERT INTO `po_items` (`id`, `po_id`, `item_id`, `quantity`, `price`, `unit`, `total`) VALUES
(5, 37, 131, 5, 10, 'PC', 50),
(6, 38, 131, 5, 10, 'PC', 50),
(7, 38, 135, 1, 60, 'PC', 60),
(8, 38, 143, 1, 50, 'PC', 50);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_list`
--

CREATE TABLE `purchase_order_list` (
  `id` int(30) NOT NULL,
  `po_code` varchar(50) NOT NULL,
  `supplier_id` int(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
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
  `transaction_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_order_list`
--

INSERT INTO `purchase_order_list` (`id`, `po_code`, `supplier_id`, `amount`, `discount_perc`, `discount`, `tax_perc`, `tax`, `sub_tax`, `amount_tendered`, `p_mode`, `change_amount`, `remarks`, `input_by`, `status`, `date_created`, `date_updated`, `transaction_datetime`) VALUES
(37, 'PO-0001', 41, 56.00, 0, 0.00, 12, 6.00, 0.00, 60.00, 'Cash', 4.00, '', 'Raymond Gregorio', 1, '2024-04-16 09:41:41', '2024-04-16 09:44:55', '2024-04-16 09:41:00'),
(38, 'PO-0002', 41, 179.20, 0, 0.00, 12, 19.20, 0.00, 200.00, 'Cash', 20.80, '', 'Raymond Gregorio', 2, '2024-04-16 10:08:37', '2024-04-16 10:08:51', '2024-04-16 10:08:00');

-- --------------------------------------------------------

--
-- Table structure for table `receiving_list`
--

CREATE TABLE `receiving_list` (
  `id` int(30) NOT NULL,
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
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `receiving_list`
--

INSERT INTO `receiving_list` (`id`, `form_id`, `from_order`, `amount`, `discount_perc`, `discount`, `tax_perc`, `tax`, `stock_ids`, `remarks`, `input_by`, `transaction_datetime`, `date_created`, `date_updated`) VALUES
(30, 37, 1, 44.8, 0, 0, 12, 4.8, '187', '', 'Raymond Gregorio', '2024-04-16 09:41:00', '2024-04-16 09:41:51', '2024-04-16 09:44:55'),
(31, 38, 1, 179.2, 0, 0, 12, 19.2, '188,189,190', '', 'Raymond Gregorio', '2024-04-16 10:08:00', '2024-04-16 10:08:51', '2024-04-16 10:08:51');

-- --------------------------------------------------------

--
-- Table structure for table `sales_list`
--

CREATE TABLE `sales_list` (
  `id` int(30) NOT NULL,
  `sales_code` varchar(50) NOT NULL,
  `client` text DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
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
  `transaction_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_list`
--

INSERT INTO `sales_list` (`id`, `sales_code`, `client`, `amount`, `discount_name`, `discount_perc`, `discount`, `tax_perc`, `tax`, `sub_tax`, `remarks`, `stock_ids`, `input_by`, `amount_tendered`, `p_mode`, `p_specify`, `change_amount`, `cog`, `cogs_total`, `date_created`, `date_updated`, `transaction_datetime`) VALUES
(39, 'SALE-0003', 'Guest', 576.80, '', 0, 0, 12, 61.80, 0.00, '', '177', 'Jiematt Caaya', 600.00, 'Online Payment', '', 23.20, 0.00, 0.00, '2024-03-31 20:54:50', '2024-04-10 12:51:38', '2024-04-10 20:54:00'),
(41, 'SALE-0002', 'Jose Pablo', 224.00, '', 0, 0, 12, 24.00, 0.00, '', '180', 'Programmer Michael Cabalona', 300.00, 'Cash', '', 76.00, 0.00, 0.00, '2024-04-10 13:01:30', '2024-04-10 13:01:30', '2024-04-08 13:00:00'),
(43, 'SALE-0001', 'Jose Pablo', 576.80, '', 0, 0, 12, 61.80, 0.00, '', '182', 'Programmer Michael Cabalona', 600.00, 'Credit Card', 'VISA', 23.20, 0.00, 0.00, '2024-04-10 13:04:07', '2024-04-10 13:04:07', '2024-04-10 13:03:00'),
(44, 'SALE-0004', 'Guest', 576.80, '', 0, 0, 12, 61.80, 0.00, '', '183', 'Programmer Michael Cabalona', 600.00, 'Cash', 'Delivery', 23.20, 0.00, 0.00, '2024-04-14 10:31:00', '2024-04-14 12:54:57', '2024-04-14 10:29:00'),
(45, 'SALE-0005', 'Guest', 111.90, '', 3, 3.09, 12, 11.99, 0.00, '', '184', 'Programmer Michael Cabalona', 112.00, 'Online Payment', 'Paymaya', 0.10, 0.00, 0.00, '2024-04-15 20:29:48', '2024-04-15 20:29:48', '2024-04-15 20:27:00'),
(46, 'SALE-0006', 'Guest', 571.03, '', 1, 5.15, 12, 61.18, 0.00, '', '185', 'Raymond Gregorio', 600.00, 'Cash', 'Delivery', 28.97, 0.00, 0.00, '2024-04-16 08:40:40', '2024-04-16 08:40:40', '2024-04-16 08:40:00');

-- --------------------------------------------------------

--
-- Table structure for table `security`
--

CREATE TABLE `security` (
  `id` int(11) NOT NULL,
  `default_pin` text NOT NULL DEFAULT '670b14728ad9902aecba32e22fa4f6bd',
  `current_pin` text NOT NULL DEFAULT '670b14728ad9902aecba32e22fa4f6bd',
  `1_question` text NOT NULL DEFAULT '7b06ba5737320a90c0ead974d4638eaf',
  `1_answer` text NOT NULL,
  `2_question` text NOT NULL DEFAULT 'af58f9b732648289433c0e4c0da02666',
  `2_answer` text NOT NULL,
  `last_update_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `security`
--

INSERT INTO `security` (`id`, `default_pin`, `current_pin`, `1_question`, `1_answer`, `2_question`, `2_answer`, `last_update_at`) VALUES
(1, '670b14728ad9902aecba32e22fa4f6bd', '670b14728ad9902aecba32e22fa4f6bd\r\n', '7b06ba5737320a90c0ead974d4638eaf', '', 'af58f9b732648289433c0e4c0da02666', '', '2024-03-02 14:29:45');

-- --------------------------------------------------------

--
-- Table structure for table `stock_list`
--

CREATE TABLE `stock_list` (
  `id` int(30) NOT NULL,
  `item_id` int(30) NOT NULL,
  `quantity` int(30) NOT NULL,
  `cogs` float NOT NULL DEFAULT 0,
  `unit` varchar(250) DEFAULT NULL,
  `price` float NOT NULL DEFAULT 0,
  `total` float NOT NULL DEFAULT current_timestamp(),
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=IN , 2=OUT',
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_list`
--

INSERT INTO `stock_list` (`id`, `item_id`, `quantity`, `cogs`, `unit`, `price`, `total`, `type`, `date_created`) VALUES
(187, 131, 4, 0, 'PC', 10, 40, 1, '2024-04-16 09:44:55'),
(188, 131, 5, 0, 'PC', 10, 50, 1, '2024-04-16 10:08:51'),
(189, 135, 1, 0, 'PC', 60, 60, 1, '2024-04-16 10:08:51'),
(190, 143, 1, 0, 'PC', 50, 50, 1, '2024-04-16 10:08:51');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_list`
--

CREATE TABLE `supplier_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `address` text NOT NULL,
  `cperson` text NOT NULL,
  `contact` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_list`
--

INSERT INTO `supplier_list` (`id`, `name`, `address`, `cperson`, `contact`, `status`, `date_created`, `date_updated`) VALUES
(41, 'ACE Hardware', '', '', '', 1, '2024-03-02 13:37:03', '2024-03-02 15:12:51'),
(43, 'AB Factory', '', '', '', 1, '2024-03-02 19:04:10', '2024-03-02 19:04:10'),
(44, 'San Lucena Hardware', '', '', '', 1, '2024-03-02 19:04:20', '2024-03-02 19:04:20'),
(45, 'Olongapo Hardware Shop Inc.', '', '', '', 1, '2024-03-02 19:04:39', '2024-03-02 19:04:39'),
(46, 'Subic Supplies Trading Inc.', '', '', '', 1, '2024-03-02 19:04:57', '2024-03-02 19:05:14');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_product`
--

CREATE TABLE `supplier_product` (
  `sp_id` int(11) NOT NULL,
  `supplier_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `supplier_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL COMMENT '0 = disabled, 1 = enabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier_product`
--

INSERT INTO `supplier_product` (`sp_id`, `supplier_id`, `product_id`, `supplier_price`, `status`) VALUES
(49, 41, 131, 10.00, 1),
(50, 43, 131, 10.00, 1),
(51, 43, 143, 0.00, 0),
(52, 46, 136, 0.00, 0),
(53, 44, 141, 0.00, 0),
(54, 41, 143, 50.00, 1),
(55, 41, 135, 60.00, 1);

-- --------------------------------------------------------

--
-- Table structure for table `supp_returnlist`
--

CREATE TABLE `supp_returnlist` (
  `id` int(30) NOT NULL,
  `return_code` varchar(50) NOT NULL,
  `supplier_id` int(30) NOT NULL DEFAULT 1,
  `category_id` int(30) NOT NULL,
  `amount` float NOT NULL DEFAULT 0,
  `discount_perc` float NOT NULL DEFAULT 0,
  `discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_perc` float NOT NULL DEFAULT 0,
  `tax` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remarks` text DEFAULT NULL,
  `stock_ids` text NOT NULL,
  `input_by` varchar(255) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `transaction_datetime` datetime NOT NULL,
  `return_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supp_returnlist`
--

INSERT INTO `supp_returnlist` (`id`, `return_code`, `supplier_id`, `category_id`, `amount`, `discount_perc`, `discount`, `tax_perc`, `tax`, `remarks`, `stock_ids`, `input_by`, `date_created`, `date_updated`, `transaction_datetime`, `return_date`) VALUES
(26, 'R-0001', 44, 0, 59.994, 1, 0.60, 1, 0.59, '', '159', 'Michael Cabalona', '2024-03-25 12:49:19', '2024-03-25 12:49:19', '2024-03-13 12:48:00', '2024-03-31');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'EJJ Construction Supplies Trading'),
(2, 'tin_num', 'xxxx-xxxx-xxxx-xxxx'),
(3, 'company_address', 'Govic Highway Purok 7 San Nicolas, Castillejos, Zambales, Philippines'),
(4, 'phone_num', '(+63) 948 895 5360'),
(5, 'company_email', 'ejjconstructionsuppliestrading@gmail.com'),
(6, 'short_name', 'EJJTRACK'),
(7, 'owner_name', 'Jiematt Caaya'),
(9, 'receipt_footer', 'Building Dreams, One Block at a Time. Together, let us build a solid foundation for your projects!'),
(11, 'logo', 'uploads/logo-1709362115.png'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/img-bg.png'),
(15, 'content', 'Array'),
(16, 'MAX_FILE_SIZE', '2097152');

-- --------------------------------------------------------

--
-- Table structure for table `system_log`
--

CREATE TABLE `system_log` (
  `id` int(30) NOT NULL,
  `user_name` text DEFAULT NULL,
  `action_description` varchar(255) NOT NULL,
  `action_description_2` varchar(255) DEFAULT NULL,
  `type` text DEFAULT NULL,
  `last_update` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `salutation` varchar(250) DEFAULT NULL,
  `avatar` text DEFAULT '\'uploads/avatar-2.jpg?v=1635920566\'',
  `last_login` datetime DEFAULT NULL,
  `type` int(1) NOT NULL DEFAULT 0,
  `session_status` tinyint(1) DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `salutation`, `avatar`, `last_login`, `type`, `session_status`, `date_added`, `date_updated`) VALUES
(1, 'Programmer', '', 'Michael Cabalona', 'ATOMUS', '21232f297a57a5a743894a0e4a801fc3', '', 'uploads/avatar-10.png?v=1709362917', '2024-04-16 10:38:40', 1, 1, '2021-01-20 14:02:37', '2024-04-16 10:38:40'),
(2, 'Michael', '', 'Cabalona', 'ATOMUS1', '21232f297a57a5a743894a0e4a801fc3', 'Mr.', 'uploads/avatar-10.png?v=1709362917', '2024-04-03 20:19:23', 1, 0, '2021-11-03 14:21:28', '2024-04-03 20:19:23'),
(12, 'Stocks Inventory Personnel', '', ' ', 'user', 'ee11cbb19052e40b07aac0ca060c23ee', '', 'uploads/avatar-2.jpg?v=1635920566', '2024-04-15 09:15:45', 3, 0, '2023-12-15 00:12:36', '2024-04-15 09:15:45'),
(14, 'Jiematt', '', 'Caaya', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Mr.', 'uploads/avatar-14.png?v=1710677464', '2024-04-15 14:20:14', 1, 0, '2024-03-02 15:07:54', '2024-04-15 14:20:14'),
(15, 'Accounting', '', 'Personnel', 'account', 'e268443e43d93dab7ebef303bbe9642f', '', 'uploads/avatar-15.png?v=1709530186', '2024-04-15 09:15:27', 2, 0, '2024-03-04 13:28:21', '2024-04-15 09:15:27');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `prevent_delete_user_1` BEFORE DELETE ON `users` FOR EACH ROW BEGIN
    IF OLD.id = 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot delete user with id 1, Please contact the owner of this system (Michael Cabalona)\r\nAt email Michael.Cabalona.28@gmail.com,\r\nAt FB Michael.Cabalona.028 ';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `prevent_update_sensitive_columns` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.id = 1 AND (NEW.firstname != OLD.firstname OR NEW.lastname != OLD.lastname OR NEW.username != OLD.username OR NEW.password != OLD.password OR NEW.salutation != OLD.salutation OR NEW.avatar != OLD.avatar) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'You must provide the correct password to update sensitive columns.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_log`
--

CREATE TABLE `user_log` (
  `id` int(30) NOT NULL,
  `user_id` int(50) DEFAULT NULL,
  `item_name` text DEFAULT NULL,
  `supplier_name` text DEFAULT NULL,
  `category_name` text DEFAULT NULL,
  `expense_name` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `action_description` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_log`
--

INSERT INTO `user_log` (`id`, `user_id`, `item_name`, `supplier_name`, `category_name`, `expense_name`, `remarks`, `action_description`, `type`, `datetime`) VALUES
(24, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-02 17:54:27'),
(33, 10, '', 'kOLEHIYO NG SUBIC', '', '', '', 'added', 2, '2024-03-02 18:04:05'),
(34, 10, '', 'kOLEHIYO NG SUBICs', '', '', '', 'modified', 2, '2024-03-02 18:04:18'),
(35, 10, '', 'kOLEHIYO NG SUBICs', '', '', '', 'deleted', 2, '2024-03-02 18:04:22'),
(36, 10, '', '', 'ww', '', '', 'added', 3, '2024-03-02 18:07:08'),
(37, 10, '', '', 'ww', '', '', 'modified', 3, '2024-03-02 18:07:13'),
(38, 10, '', '', 'ww', '', '', 'deleted', 3, '2024-03-02 18:07:16'),
(39, 14, '', 'AB Factory', '', '', '', 'added', 2, '2024-03-02 19:04:10'),
(40, 14, '', 'San Lucena Hardware', '', '', '', 'added', 2, '2024-03-02 19:04:20'),
(41, 14, '', 'Olongapo Hardware Shop Inc.', '', '', '', 'added', 2, '2024-03-02 19:04:39'),
(42, 14, '', 'Subic Supplies Trading', '', '', '', 'added', 2, '2024-03-02 19:04:57'),
(43, 14, '', 'Subic Supplies Tradingss', '', '', '', 'modified', 2, '2024-03-02 19:05:06'),
(44, 14, '', 'Subic Supplies Tradings', '', '', '', 'modified', 2, '2024-03-02 19:05:09'),
(45, 14, '', 'Subic Supplies Trading Inc.', '', '', '', 'modified', 2, '2024-03-02 19:05:14'),
(46, 14, '', 'ad', '', '', '', 'added', 2, '2024-03-02 19:05:21'),
(47, 14, '', 'ad', '', '', '', 'deleted', 2, '2024-03-02 19:05:28'),
(48, 14, 'Apperson Fortune', '', '', '', '', 'added', 1, '2024-03-02 19:50:18'),
(49, 14, 'Phelps Dodges aaa', '', '', '', '', 'added', 1, '2024-03-02 19:52:52'),
(50, 14, 'Phelps Dodges aaaa', '', '', '', '', 'added', 1, '2024-03-02 19:53:37'),
(51, 14, 'Phelps Dodges aaaa', '', '', '', '', 'deleted', 1, '2024-03-02 19:55:11'),
(52, 14, 'Phelps Dodges aaa', '', '', '', '', 'deleted', 1, '2024-03-02 19:55:22'),
(53, 14, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-02 19:57:42'),
(54, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-02 20:56:31'),
(55, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-02 20:56:39'),
(56, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:00:05'),
(57, 10, 'America Uni Adapter White', '', '', '', '', 'modified', 1, '2024-03-03 11:01:11'),
(58, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:02:39'),
(59, 10, 'America Uni Adapter White', '', '', '', '', 'modified', 1, '2024-03-03 11:03:02'),
(60, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:04:04'),
(61, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:09:18'),
(62, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:10:11'),
(63, 10, 'a', '', '', '', '', 'added', 1, '2024-03-03 11:10:32'),
(64, 10, 'a', '', '', '', '', 'deleted', 1, '2024-03-03 11:10:42'),
(65, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:14:26'),
(66, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:17:15'),
(67, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:18:37'),
(68, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:21:09'),
(69, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:26:03'),
(70, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:28:25'),
(71, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:28:36'),
(72, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:29:22'),
(73, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:29:40'),
(74, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:33:10'),
(75, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:52:02'),
(76, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:52:44'),
(77, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:56:58'),
(78, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:57:41'),
(79, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:58:39'),
(80, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:59:39'),
(81, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-03 11:59:49'),
(82, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-05 19:25:05'),
(83, 10, 'Apperson', '', '', '', '', 'added', 1, '2024-03-10 11:41:54'),
(84, 10, 'Apperson', '', '', '', '', 'deleted', 1, '2024-03-10 11:41:59'),
(85, 10, '', '', 'BLUE FITTINGS', '', '', 'modified', 3, '2024-03-14 12:03:04'),
(86, 10, '', '', 'BLUE FITTINGS', '', '', 'modified', 3, '2024-03-14 12:03:09'),
(87, 10, ' Threaded Tee 3/4', '', '', '', '', 'modified', 1, '2024-03-14 12:05:07'),
(88, 14, 'America Uni Adapter White', '', '', '', '', 'modified', 1, '2024-03-17 17:39:15'),
(89, 10, 'America Uni Adapter White', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-29 13:04:57'),
(90, 10, 'MICHAEL BALINGIT CABALONA', NULL, NULL, NULL, NULL, 'added', 1, '2024-03-31 14:20:47'),
(91, 10, 'MICHAEL BALINGIT CABALONA', NULL, NULL, NULL, NULL, 'deleted', 1, '2024-03-31 14:27:38'),
(92, 10, 'Jose Jose', NULL, NULL, NULL, NULL, 'added', 1, '2024-03-31 14:43:55'),
(93, 10, 'MICHAEL BALINGIT CABALONA', NULL, NULL, NULL, NULL, 'added', 1, '2024-03-31 14:46:32'),
(94, 10, 'MICHAEL BALINGIT CABALONA', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 14:46:53'),
(95, 10, 'MICHAEL BALINGIT CABALO', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 14:55:21'),
(96, 10, 'MICHAEL BALINGIT ', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 14:55:31'),
(97, 10, 'Manuel Andrea Concepcion', NULL, NULL, NULL, NULL, 'added', 1, '2024-03-31 14:55:46'),
(98, 10, 'wqw', NULL, NULL, NULL, NULL, 'added', 1, '2024-03-31 14:57:51'),
(99, 10, 'wqw', NULL, NULL, NULL, NULL, 'added', 1, '2024-03-31 15:01:35'),
(100, 10, 'wqw', NULL, NULL, NULL, NULL, 'deleted', 1, '2024-03-31 15:02:01'),
(101, 14, ' Threaded Tee 3/4', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 17:20:42'),
(102, 14, 'Boysen Flat Latex White B-701', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:27:57'),
(103, 14, 'Boysen Flat Wall Enamel B800', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:28:19'),
(104, 14, 'Clean Out 4', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:30:54'),
(105, 14, 'Jose', NULL, NULL, NULL, NULL, 'added', 1, '2024-03-31 19:31:57'),
(106, 14, 'Jose', NULL, NULL, NULL, NULL, 'deleted', 1, '2024-03-31 19:32:06'),
(107, 14, 'Clean Out 4', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:32:16'),
(108, 14, 'Clean Out 4', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:32:32'),
(109, 14, 'Clean Out 4', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:32:38'),
(110, 14, 'Clean Out 4', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:32:43'),
(111, 14, 'America Uni Adapter White', NULL, NULL, NULL, NULL, 'modified', 1, '2024-03-31 19:33:30'),
(112, 1, 'MICHAEL BALINGIT ', NULL, NULL, NULL, NULL, 'deleted', 1, '2024-04-14 12:35:10'),
(113, 1, NULL, NULL, 'aa', NULL, NULL, 'added', 3, '2024-04-14 12:36:38'),
(114, 1, NULL, NULL, 'aa', NULL, NULL, 'modified', 3, '2024-04-14 12:37:06'),
(115, 1, NULL, NULL, 'aa', NULL, NULL, 'deleted', 3, '2024-04-14 12:38:16'),
(116, 1, NULL, NULL, 'BLUE FITTINGS', NULL, NULL, 'modified', 3, '2024-04-14 12:38:27'),
(117, 1, NULL, NULL, 'BLUE FITTINGS', NULL, NULL, 'modified', 3, '2024-04-14 12:38:33');

-- --------------------------------------------------------

--
-- Table structure for table `user_meta`
--

CREATE TABLE `user_meta` (
  `user_id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `vendor_name` varchar(255) NOT NULL,
  `TIN_NUM` varchar(50) DEFAULT NULL,
  `vendor_address` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `vendor_name`, `TIN_NUM`, `vendor_address`, `contact_person`, `email`, `phone`) VALUES
(3, 'ZAMECO II Electric Cooperative', 'xxxx-xxxx-xxxx-xxxx', 'Magsaysay, Castillejos, Zambales', '', 'zameco2@gmail.com', '0939-938-9794');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `back_order_list`
--
ALTER TABLE `back_order_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `receiving_id` (`receiving_id`);

--
-- Indexes for table `bo_items`
--
ALTER TABLE `bo_items`
  ADD KEY `item_id` (`item_id`),
  ADD KEY `bo_id` (`bo_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cust_returnitem`
--
ALTER TABLE `cust_returnitem`
  ADD KEY `fk_item_id` (`item_id`),
  ADD KEY `fk_cust_returnitem_cusret_code` (`cusret_code`);

--
-- Indexes for table `cust_returnlist`
--
ALTER TABLE `cust_returnlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `item_list`
--
ALTER TABLE `item_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `journalentry`
--
ALTER TABLE `journalentry`
  ADD PRIMARY KEY (`EntryID`);

--
-- Indexes for table `pi_items`
--
ALTER TABLE `pi_items`
  ADD PRIMARY KEY (`idd`);

--
-- Indexes for table `po_items`
--
ALTER TABLE `po_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_id` (`po_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `purchase_order_list`
--
ALTER TABLE `purchase_order_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `receiving_list`
--
ALTER TABLE `receiving_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales_list`
--
ALTER TABLE `sales_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `security`
--
ALTER TABLE `security`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `supplier_list`
--
ALTER TABLE `supplier_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_product`
--
ALTER TABLE `supplier_product`
  ADD PRIMARY KEY (`sp_id`);

--
-- Indexes for table `supp_returnlist`
--
ALTER TABLE `supp_returnlist`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_log`
--
ALTER TABLE `system_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_log`
--
ALTER TABLE `user_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_meta`
--
ALTER TABLE `user_meta`
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `back_order_list`
--
ALTER TABLE `back_order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `cust_returnlist`
--
ALTER TABLE `cust_returnlist`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `item_list`
--
ALTER TABLE `item_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `pi_items`
--
ALTER TABLE `pi_items`
  MODIFY `idd` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `purchase_order_list`
--
ALTER TABLE `purchase_order_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `receiving_list`
--
ALTER TABLE `receiving_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `sales_list`
--
ALTER TABLE `sales_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `security`
--
ALTER TABLE `security`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `stock_list`
--
ALTER TABLE `stock_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=191;

--
-- AUTO_INCREMENT for table `supplier_list`
--
ALTER TABLE `supplier_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `supplier_product`
--
ALTER TABLE `supplier_product`
  MODIFY `sp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `supp_returnlist`
--
ALTER TABLE `supp_returnlist`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `system_log`
--
ALTER TABLE `system_log`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `user_log`
--
ALTER TABLE `user_log`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `back_order_list`
--
ALTER TABLE `back_order_list`
  ADD CONSTRAINT `back_order_list_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `back_order_list_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_order_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `back_order_list_ibfk_3` FOREIGN KEY (`receiving_id`) REFERENCES `receiving_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bo_items`
--
ALTER TABLE `bo_items`
  ADD CONSTRAINT `bo_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bo_items_ibfk_2` FOREIGN KEY (`bo_id`) REFERENCES `back_order_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cust_returnitem`
--
ALTER TABLE `cust_returnitem`
  ADD CONSTRAINT `cust_returnitem_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`),
  ADD CONSTRAINT `fk_cust_returnitem_cusret_code` FOREIGN KEY (`cusret_code`) REFERENCES `cust_returnlist` (`id`),
  ADD CONSTRAINT `fk_item_id` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`);

--
-- Constraints for table `po_items`
--
ALTER TABLE `po_items`
  ADD CONSTRAINT `po_items_ibfk_1` FOREIGN KEY (`po_id`) REFERENCES `purchase_order_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `po_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_order_list`
--
ALTER TABLE `purchase_order_list`
  ADD CONSTRAINT `purchase_order_list_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `supplier_list` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_list`
--
ALTER TABLE `stock_list`
  ADD CONSTRAINT `stock_list_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `item_list` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
