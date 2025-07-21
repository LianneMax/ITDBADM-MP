-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 21, 2025 at 01:22 PM
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
(20, 1, '2025-07-21', 12000, 'Processing', 3);

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
(4, 4, 'Razer DeathAdder', 'Gaming Mouse', 60, 3500),
(5, 5, 'JBL Flip 5', 'Portable Bluetooth Speaker', 24, 7000),
(6, 1, 'Airpods Max', 'Wireless Headphones', 1900, 35000),
(7, 2, 'LG UltraGear 27GN950', 'Gaming Monitor', 1500, 25000),
(8, 3, 'Corsair K95 RGB Platinum', 'Mechanical Gaming Keyboard', 1200, 8000),
(9, 4, 'Logitech G502 HERO', 'High-Performance Gaming Mouse', 1000, 4000),
(10, 5, 'Bose SoundLink Revolve+', 'Portable Bluetooth Speaker', 800, 9000);

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
(5, 'Customer', 'David', 'Tan', 'david_tan@dlsu.edu.ph', 'david'),
(6, 'Customer', 'Ella', 'Santos', 'ella_santos@dlsu.edu.ph', 'ella');

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
