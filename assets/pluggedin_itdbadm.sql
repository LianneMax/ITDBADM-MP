DROP DATABASE IF EXISTS pluggedin_itdbadm;
CREATE DATABASE pluggedin_itdbadm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pluggedin_itdbadm;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 22, 2025 at 02:08 PM
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
-- Database: `pluggedin_itdbadm`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `add_new_product` (IN `product_name` VARCHAR(45), IN `category_code` INT, IN `description` VARCHAR(45), IN `stock_qty` INT, IN `srp_php` FLOAT)   BEGIN
   INSERT INTO products (product_name, category_code, description, stock_qty, srp_php)
   VALUES (product_name, category_code, description, stock_qty, srp_php);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_customer_account` (IN `customer_id` INT)   BEGIN
   DELETE FROM orders WHERE user_id = customer_id;
   DELETE FROM cart WHERE user_id = customer_id;
   DELETE FROM isfavorite WHERE user_id = customer_id;
   DELETE FROM users WHERE user_id = customer_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `delete_product` (IN `input_product_code` INT)   BEGIN
  DELETE FROM cart WHERE product_code = input_product_code;
  DELETE FROM isfavorite WHERE product_code = input_product_code;
  DELETE FROM order_items WHERE product_code = input_product_code;

  DELETE FROM products WHERE product_code = input_product_code;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_order_status` (IN `input_order_id` INT, IN `new_status` VARCHAR(45))   BEGIN
   UPDATE orders SET order_status = new_status WHERE order_id = input_order_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_product_stock` (IN `input_product_code` INT, IN `new_stock` INT)   BEGIN
   UPDATE products SET stock_qty = new_stock WHERE product_code = input_product_code;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_code` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `product_code`, `quantity`, `date_added`) VALUES
(18, 1, 5, 1, '2025-07-21 10:03:27'),
(19, 1, 10, 1, '2025-07-21 10:30:06');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_code` int(11) NOT NULL,
  `category_name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_code`, `category_name`) VALUES
(1, 'Headphones'),
(2, 'Monitors'),
(3, 'Keyboards'),
(4, 'Mice'),
(5, 'Speakers');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `currency_code` int(11) NOT NULL,
  `price_php` varchar(45) DEFAULT NULL,
  `currency_name` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`currency_code`, `price_php`, `currency_name`) VALUES
(1, '0.041', 'KRW'),
(2, '57.24', 'USD'),
(3, '1', 'PHP');

-- --------------------------------------------------------

--
-- Table structure for table `customer_deletion_log`
--

CREATE TABLE `customer_deletion_log` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `deletion_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_deletion_log`
--

INSERT INTO `customer_deletion_log` (`user_id`, `first_name`, `last_name`, `deletion_date`) VALUES
(8, 'Delete ', 'This', '2025-07-22 10:53:21'),
(9, 'Delete ', 'This', '2025-07-22 10:53:00');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_log`
--

CREATE TABLE `inventory_log` (
  `product_code` int(11) NOT NULL,
  `old_qty` int(11) DEFAULT NULL,
  `new_qty` int(11) DEFAULT NULL,
  `change_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `inventory_log`
--

INSERT INTO `inventory_log` (`product_code`, `old_qty`, `new_qty`, `change_date`) VALUES
(4, 60, -100, '2025-07-22 10:56:20'),
(4, -100, 100, '2025-07-22 10:59:51'),
(5, 24, -1, '2025-07-22 10:56:01'),
(5, -1, 100, '2025-07-22 11:03:57'),
(6, 1900, -1, '2025-07-22 10:43:02'),
(6, -1, 100, '2025-07-22 11:04:25'),
(8, 1200, 1201, '2025-07-22 11:01:53'),
(11, 1, 100, '2025-07-21 20:45:23'),
(12, 1, 100, '2025-07-22 11:52:10');

-- --------------------------------------------------------

--
-- Table structure for table `isfavorite`
--

CREATE TABLE `isfavorite` (
  `user_id` int(11) NOT NULL,
  `product_code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `isfavorite`
--

INSERT INTO `isfavorite` (`user_id`, `product_code`) VALUES
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 10);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` date DEFAULT NULL,
  `totalamt_php` float NOT NULL,
  `order_status` varchar(45) DEFAULT NULL,
  `currency_code` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `totalamt_php`, `order_status`, `currency_code`) VALUES
(20, 1, '2025-07-21', 12000, 'Delivered', 3);

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `order_status_logging_trigger` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
   IF OLD.order_status != NEW.order_status THEN
      INSERT INTO order_status_log (order_id, old_status, new_status, change_date)
      VALUES (NEW.order_id, OLD.order_status, NEW.order_status, NOW());
   END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_code` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `srp_php` float DEFAULT NULL,
  `totalprice_php` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `product_code`, `quantity`, `srp_php`, `totalprice_php`) VALUES
(1, 20, 5, 1, 7000, 7000),
(2, 20, 3, 1, 5000, 5000);

-- --------------------------------------------------------

--
-- Table structure for table `order_status_log`
--

CREATE TABLE `order_status_log` (
  `order_id` int(11) NOT NULL,
  `old_status` varchar(45) DEFAULT NULL,
  `new_status` varchar(45) DEFAULT NULL,
  `change_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_status_log`
--

INSERT INTO `order_status_log` (`order_id`, `old_status`, `new_status`, `change_date`) VALUES
(20, 'Delivered', 'Shipped', '2025-07-22 10:20:31'),
(20, 'Shipped', 'Processing', '2025-07-22 10:23:45'),
(20, 'Processing', 'Delivered', '2025-07-22 11:32:51'),
(20, 'Delivered', 'Processing', '2025-07-22 11:34:07'),
(20, 'Processing', 'Shipped', '2025-07-22 11:34:27'),
(20, 'Shipped', 'Delivered', '2025-07-22 11:34:43');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `currency_code` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `totalamt_php` float NOT NULL,
  `payment_status` enum('paid','unpaid') NOT NULL,
  `payment_method` enum('card','ewallet','cash') NOT NULL,
  `payment_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `currency_code`, `order_id`, `totalamt_php`, `payment_status`, `payment_method`, `payment_date`) VALUES
(1, 3, 20, 12000, 'unpaid', 'cash', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_code` int(11) NOT NULL,
  `category_code` int(11) DEFAULT NULL,
  `product_name` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `stock_qty` int(11) DEFAULT NULL,
  `srp_php` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_code`, `category_code`, `product_name`, `description`, `stock_qty`, `srp_php`) VALUES
(1, 1, 'Sony WH-1000XM4', 'Noise Cancelling Headphones', 50, 12000),
(2, 2, 'Samsung 27\" Monitor', '4K UHD Display', 30, 15000),
(3, 3, 'Logitech MX Keys', 'Wireless Keyboard', 39, 5000),
(4, 4, 'Razer DeathAdder', 'Gaming Mouse', 100, 3500),
(5, 5, 'JBL Flip 5', 'Portable Bluetooth Speaker', 100, 7000),
(6, 1, 'Airpods Max', 'Wireless Headphones', 100, 35000),
(7, 2, 'LG UltraGear 27GN950', 'Gaming Monitor', 1500, 25000),
(8, 3, 'Corsair K95 RGB Platinum', 'Mechanical Gaming Keyboard', 1201, 8000),
(9, 4, 'Logitech G502 HERO', 'High-Performance Gaming Mouse', 1000, 4000),
(10, 5, 'Bose SoundLink Revolve+', 'Portable Bluetooth Speaker', 800, 9000),
(11, 1, 'test', 'test', 100, 1),
(12, 4, 'test2', 'test2', 100, 1);

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `inventory_adjustment_trigger` AFTER UPDATE ON `products` FOR EACH ROW BEGIN
   IF OLD.stock_qty != NEW.stock_qty THEN
      INSERT INTO inventory_log (product_code, old_qty, new_qty, change_date)
      VALUES (NEW.product_code, OLD.stock_qty, NEW.stock_qty, current_timestamp());
   END IF;
END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `prevent_negative_inventory` BEFORE UPDATE ON `products` FOR EACH ROW BEGIN
   DECLARE available_qty INT;
   SELECT stock_qty INTO available_qty FROM products WHERE product_code = NEW.product_code;
   IF available_qty > NEW.stock_qty THEN
      SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Insufficient inventory';
   END IF;
END
$$
DELIMITER ;

DELIMITER $$
CREATE TRIGGER `product_deletion_log_trigger` AFTER DELETE ON `products` FOR EACH ROW BEGIN
  INSERT INTO product_deletion_log (
    product_code, product_name, category_code,
    description, deletion_date
  )
  VALUES (
    OLD.product_code, OLD.product_name, OLD.category_code,
    OLD.description, CURRENT_TIMESTAMP()
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `product_deletion_log`
--

CREATE TABLE `product_deletion_log` (
  `product_code` int(11) NOT NULL,
  `product_name` varchar(45) DEFAULT NULL,
  `category_code` int(11) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `deletion_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_role` varchar(25) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_role`, `first_name`, `last_name`, `email`, `password`) VALUES
(1, 'Customer', 'Alyssa', 'Mansueto', 'alyssa_mansueto@dlsu.edu.ph', 'alyssa'),
(2, 'Customer', 'Max', 'Balbastro', 'maxbalbastro@gmail.com', 'ilovejuls'),
(3, 'Admin', 'Brian', 'Lopez', 'brian_lopez@dlsu.edu.ph', 'brian'),
(4, 'Staff', 'Carla', 'Reyes', 'carla_reyes@dlsu.edu.ph', 'carla'),
(6, 'Customer', 'Ella', 'Santos', 'ella_santos@dlsu.edu.ph', 'ella');

--
-- Triggers `users`
--
DELIMITER $$
CREATE TRIGGER `customer_deletion_log_trigger` AFTER DELETE ON `users` FOR EACH ROW BEGIN
   INSERT INTO customer_deletion_log (user_id, first_name, last_name, deletion_date)
   VALUES (OLD.user_id, OLD.first_name, OLD.last_name, CURRENT_TIMESTAMP());
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD UNIQUE KEY `unique_user_product` (`user_id`,`product_code`),
  ADD KEY `product_code` (`product_code`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_code`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`currency_code`);

--
-- Indexes for table `customer_deletion_log`
--
ALTER TABLE `customer_deletion_log`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `inventory_log`
--
ALTER TABLE `inventory_log`
  ADD PRIMARY KEY (`product_code`,`change_date`),
  ADD KEY `change_date_idx` (`change_date`);

--
-- Indexes for table `isfavorite`
--
ALTER TABLE `isfavorite`
  ADD PRIMARY KEY (`user_id`,`product_code`),
  ADD KEY `product_code_idx` (`product_code`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id_idx` (`user_id`),
  ADD KEY `currency_code_idx` (`currency_code`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `product_code_idx` (`product_code`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_product_code` (`product_code`);

--
-- Indexes for table `order_status_log`
--
ALTER TABLE `order_status_log`
  ADD PRIMARY KEY (`order_id`,`change_date`),
  ADD KEY `change_date_idx` (`change_date`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payments_currency_code` (`currency_code`),
  ADD KEY `fk_payments_order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_code`),
  ADD KEY `category_code_idx` (`category_code`);

--
-- Indexes for table `product_deletion_log`
--
ALTER TABLE `product_deletion_log`
  ADD PRIMARY KEY (`product_code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id_UNIQUE` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_code` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_code`) REFERENCES `products` (`product_code`);

--
-- Constraints for table `isfavorite`
--
ALTER TABLE `isfavorite`
  ADD CONSTRAINT `fk_isfavorite_product_code` FOREIGN KEY (`product_code`) REFERENCES `products` (`product_code`),
  ADD CONSTRAINT `fk_isfavorite_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `currency_code` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`currency_code`),
  ADD CONSTRAINT `user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `fk_order_items_product_code` FOREIGN KEY (`product_code`) REFERENCES `products` (`product_code`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_currency_code` FOREIGN KEY (`currency_code`) REFERENCES `currencies` (`currency_code`),
  ADD CONSTRAINT `fk_payments_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `category_code` FOREIGN KEY (`category_code`) REFERENCES `categories` (`category_code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
