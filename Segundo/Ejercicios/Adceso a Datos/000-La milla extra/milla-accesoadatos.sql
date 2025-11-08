-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 06, 2025 at 05:08 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `milla-accesoadatos`
--

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int NOT NULL,
  `type` varchar(50) COLLATE utf8mb3_spanish_ci NOT NULL,
  `message` text COLLATE utf8mb3_spanish_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `code` varchar(100) COLLATE utf8mb3_spanish_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `status` varchar(30) COLLATE utf8mb3_spanish_ci NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `code`, `created_at`, `status`) VALUES
(1, 'ORD-20251106-163150-798', '2025-11-06 16:31:50', 'pending'),
(2, 'ORD-20251106-175334-342', '2025-11-06 17:53:34', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, 19.90),
(2, 2, 5, 1, 700.00),
(3, 2, 3, 1, 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(150) COLLATE utf8mb3_spanish_ci NOT NULL,
  `sku` varchar(100) COLLATE utf8mb3_spanish_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `stock` int NOT NULL DEFAULT '0',
  `provider_id` int DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `sku`, `price`, `stock`, `provider_id`, `created_at`) VALUES
(1, 'Teclado mecánico', 'SKU-KEY-001', 59.90, 100, 1, '2025-11-06 16:16:23'),
(2, 'Ratón óptico', 'SKU-MOU-002', 19.90, 150, 1, '2025-11-06 16:16:23'),
(3, 'Monitor', 'SKU-KEY-003', 20.00, 5, 1, '2025-11-06 16:53:18'),
(4, 'ordenador', 'SKU-KEY-004', 400.00, 5, 2, '2025-11-06 16:54:15'),
(5, 'portatil', 'SKU-MOU-007', 700.00, 7, 3, '2025-11-06 17:53:08'),
(6, 'asdas', 'SKU-KEY-009', 66.00, 6, NULL, '2025-11-06 17:58:51');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` int NOT NULL,
  `name` varchar(120) COLLATE utf8mb3_spanish_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb3_spanish_ci NOT NULL,
  `phone` varchar(50) COLLATE utf8mb3_spanish_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'Proveedor Demo', 'proveedor@example.com', '+34 600 000 000', '2025-11-06 16:16:23'),
(2, 'pcprogramacion', 'desarrollo@pcprogramacion.es', '628639355', '2025-11-06 16:53:44'),
(3, 'PCcomponentes', 'PCcomponentes@gmail.com', '666666666', '2025-11-06 17:52:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_items_order` (`order_id`),
  ADD KEY `fk_items_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_products_provider` (`provider_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `fk_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_provider` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
