-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2026 at 03:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `customer_id`, `product_id`, `quantity`, `added_at`) VALUES
(14, 1, 16, 5, '2026-05-08 12:24:48'),
(15, 1, 14, 1, '2026-05-08 13:42:02');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `password`, `phone`, `address`, `status`, `created_at`) VALUES
(1, 'arefinronok', 'arefinronok13@gmail.com', '$2y$10$8uYtJFhf68fQRvS3yWJzT.4F62xwAlbzfUZhoappB1o07BcPK49gy', '0132160835', 'Dhaka', 'active', '2026-05-03 17:12:37');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_no` varchar(30) NOT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_email` varchar(150) DEFAULT NULL,
  `customer_phone` varchar(30) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `unit_price` decimal(12,2) DEFAULT NULL,
  `total_price` decimal(12,2) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('pending','completed','delivered','cancelled') DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_method` enum('cash','bank_transfer') DEFAULT 'cash',
  `transaction_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_no`, `customer_name`, `customer_email`, `customer_phone`, `customer_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `address`, `status`, `notes`, `created_at`, `payment_method`, `transaction_id`) VALUES
(1, 'TM-20260502-0750', 'Akondo', 'akondo@gmail.com', '01321321321', NULL, NULL, 3, 150000.00, 450000.00, NULL, 'delivered', '', '2026-05-02 21:00:40', 'cash', NULL),
(2, 'TM-20260503-9913', 'arefinronok', 'arefinronok13@gmail.com', '0132160835', 1, NULL, 1, NULL, 650.00, 'Dhaka', 'delivered', '', '2026-05-03 18:23:19', 'cash', NULL),
(3, 'TM-20260503-2485', 'arefinronok', 'arefinronok13@gmail.com', '0132160835', 1, NULL, 1, NULL, 6800.00, 'Dhaka', 'cancelled', '', '2026-05-03 19:52:17', 'cash', NULL),
(4, 'TM-20260503-3648', 'arefinronok', 'arefinronok13@gmail.com', '0132160835', 1, NULL, 1, NULL, 75000.00, 'Dhaka', 'delivered', '', '2026-05-03 20:17:36', 'cash', NULL),
(5, 'TM-20260504-5221', 'arefinronok', 'arefinronok13@gmail.com', '0132160835', 1, NULL, 1, NULL, 15000.00, 'Dhaka', 'delivered', '', '2026-05-04 05:29:35', 'bank_transfer', '01005127175'),
(6, 'TM-20260505-9164', 'arefinronok', 'arefinronok13@gmail.com', '0132160835', 1, NULL, 1, NULL, 15000.00, 'Dhaka', 'delivered', '', '2026-05-05 06:55:41', 'bank_transfer', '01005127175'),
(7, 'ORD1778009615', 'Arefin Ronok', NULL, '01321608350', NULL, NULL, 1, NULL, 87900.00, 'Dhaka\r\nGazipur', 'cancelled', NULL, '2026-05-05 19:33:35', 'cash', NULL),
(8, 'ORD1778009691', 'Arefin Ronok', NULL, '01321608350', NULL, NULL, 1, NULL, 87900.00, 'Dhaka\r\nGazipur', 'completed', NULL, '2026-05-05 19:34:51', 'cash', NULL),
(9, 'ORD1778010502', 'Arefin Ronok', NULL, '01321608350', NULL, NULL, 1, NULL, 6500.00, 'Dhaka\r\nGazipur', 'cancelled', NULL, '2026-05-05 19:48:22', 'cash', NULL),
(10, 'TM-20260507-C1EA', 'Tom', NULL, '01321560835', NULL, NULL, 1, NULL, 21500.00, 'Gazipur', 'pending', NULL, '2026-05-07 07:44:57', 'cash', NULL),
(11, 'TM-20260507-1345', 'arefinronok', 'arefinronok13@gmail.com', '0132160835', 1, NULL, 1, NULL, 6500.00, 'Dhaka', 'completed', '', '2026-05-07 10:44:39', 'cash', ''),
(12, 'TM-20260507-1046', 'arefinronok', 'arefinronok13@gmail.com', '0132160835', 1, NULL, 1, NULL, 60000.06, 'Dhaka', 'pending', '', '2026-05-07 11:13:39', 'cash', '');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(200) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `quantity`, `unit_price`, `subtotal`, `price`) VALUES
(1, 2, NULL, 'Safety Helmet', 1, 650.00, 650.00, 0.00),
(2, 3, NULL, 'Angle Grinder 4.5\"', 1, 6800.00, 6800.00, 0.00),
(3, 4, 14, 'Roof Mounted Fan', 5, 15000.00, 75000.00, 0.00),
(4, 5, 14, 'Roof Mounted Fan', 1, 15000.00, 15000.00, 0.00),
(5, 6, 14, 'Roof Mounted Fan (HTF-48)', 1, 15000.00, 15000.00, 0.00),
(6, 8, NULL, 'Industrial & Commercial Dehumidifier in Bangladesh', 3, 0.00, 0.00, 25800.00),
(7, 8, NULL, 'Rood Exhaust fan', 1, 0.00, 0.00, 10500.00),
(8, 9, NULL, 'Roof Mounted Fan (HATARI-36)', 1, 0.00, 0.00, 6500.00),
(9, 10, NULL, 'Roof Mounted Fan (HATARI-36)', 1, 0.00, 0.00, 6500.00),
(10, 10, NULL, 'Roof Mounted Fan (HTF-48)', 1, 0.00, 0.00, 15000.00),
(11, 11, 16, 'Roof Mounted Fan (HATARI-36)', 1, 6500.00, 6500.00, 0.00),
(12, 12, NULL, 'Catto', 6, 10000.01, 60000.06, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `min_qty` int(11) NOT NULL DEFAULT 5,
  `supplier` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_url` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `quantity` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `min_qty`, `supplier`, `description`, `created_at`, `image_url`, `stock`, `quantity`) VALUES
(14, 'Roof Mounted Fan (HTF-48)', 'Industrial Cooling System', 15000.00, 5, '0', 'Roof Mounted Fan (HTF-48)\r\nCapacity: 44,000 M³/H\r\nBlade Diameter: 50”\r\nDimension: 239.6 x 160 (94,70) cm\r\nVoltage: 380V / 50Hz, 3 Phase\r\nMotor Power: 2.2 kW\r\nRPM: 590', '2026-05-03 20:12:50', 'uploads/products/product-1777957135-e9eb0c8b.png', 18, 17),
(15, 'Rood Exhaust fan', 'Exhaust Fan', 10500.00, 10, '0', 'Body: FRP\r\nBlade: Aluminium Alloy / FRP\r\nNo. of Blades: 03 Nos. per set\r\nMotor: IP 55 grade (F class insulation)\r\nRain Protection: FRP\r\nCountry of Origin: Thailand', '2026-05-05 04:53:30', 'uploads/products/product-1777956810-ac97dfcf.png', 30, 30),
(16, 'Roof Mounted Fan (HATARI-36)', 'Industrial Cooling System', 6500.00, 15, '0', 'Capacity: 305,000 M³/H\r\nBlade Diameter: 30”\r\nDimension: 120 x 152 (96,55) cm\r\nVoltage: 380V / 50Hz, 3 Phase\r\nMotor Power: 0.75 kW\r\nRPM: 700', '2026-05-05 05:02:31', 'uploads/products/product-1777957351-aed1ad85.png', 70, 68),
(17, 'Industrial & Commercial Dehumidifier in Bangladesh', 'Industrial Cooling System', 25800.00, 5, '0', 'Dehumidifier Specifications\r\nModel No.	TMEC-DHF-001\r\nDisplay	LED Display\r\nPower-off Memory Function	Yes\r\nSize (H×W×L)	H1600 × W610 × L400 mm\r\nController	Automatic Defrost System\r\nAutomatic Humidity Control	Yes\r\nIntelligent Control System	Microcomputer Based\r\nTrademark	DXSL or OEM\r\nCertification	CE, ROHS, CCC, ISO9001\r\nOrigin	China', '2026-05-05 05:09:41', 'uploads/products/product-1777957781-6d776450.png', 35, 35),
(19, 'FRP Wall Exhaust Fan', 'Exhaust Fan', 4500.00, 5, '0', 'Key Features of FRP Wall Exhaust Fans\r\nCorrosion Resistant: Withstands harsh chemicals and saline air.\r\nHigh Airflow Capacity: Designed to quickly remove heat and fumes.\r\nLow Noise Operation: Smooth and quiet performance even at high speed.\r\nEnergy Efficient: Consumes less power while providing maximum ventilation.\r\nCustom Sizes Available: Can be manufactured to suit specific building needs.', '2026-05-08 13:53:29', 'uploads/products/product-1778248409-912a01d8.png', 0, 25),
(21, 'Push Pull Exhaust Fan', 'Exhaust Fan', 4000.00, 5, '0', 'Dual-Action Mechanism: Simultaneous air intake and exhaust for maximum efficiency.\r\nEnergy Efficiency: Optimized motors reduce electricity consumption.\r\nDurable Build: Made with high-quality materials for long-lasting performance.\r\nWeather Resistance: Many models are weatherproof, ensuring functionality even in harsh environments.\r\nEasy Installation: Compatible with various mounting options.', '2026-05-08 13:58:24', 'uploads/products/product-1778248704-4eaa4b89.png', 0, 15),
(22, 'Drop Hammer Exhaust Fan', 'Exhaust Fan', 3800.00, 5, '0', 'Automatic Louvers:\r\nThe “drop hammer” mechanism allows the louvers to open when the fan is running and close when it’s off, preventing backflow and protecting against dust, rain, and pests.\r\nHigh Airflow Efficiency:\r\nThese fans are designed to move large volumes of air efficiently, helping maintain optimal airflow in large spaces.\r\nDurable Construction:\r\nTypically made from galvanized steel or other weather-resistant materials to withstand harsh environments.', '2026-05-08 14:02:06', 'uploads/products/product-1778248926-af3296b2.png', 0, 23),
(23, 'Axial Blower Fan', 'Ventilation', 5200.00, 5, '0', 'Energy Efficiency: They provide high efficiency in low-pressure, large-volume applications.Industrial\r\n Durability: Often built with high-quality materials and motor protections, such as IP55 standards and Class F insulation for withstanding harsh conditions.Operational \r\nRange: Suitable for high-speed industrial needs, ranging from 1,000 to 80,000 \\(\\text{m}^3\\text{/h}\\) of airflow', '2026-05-08 14:07:22', 'uploads/products/product-1778249242-53e668d3.png', 0, 32);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `created_at`) VALUES
(2, 'Admin User', 'admin@toolmasterbd.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active', '2026-05-02 20:49:21'),
(3, 'Jackson', 'jack@gmail.com', '$2y$10$1323neht.hJgNFxH/2p5cOnR7QlQS0ExyjQVP.4EC5uo6O8UsiDP.', 'staff', 'active', '2026-05-05 17:44:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cart_item` (`customer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_no` (`order_no`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
