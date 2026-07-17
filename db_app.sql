-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2026 at 05:10 AM
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
-- Database: `db_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `batch`
--

CREATE TABLE `batch` (
  `id` bigint(10) NOT NULL,
  `sale_order` bigint(10) NOT NULL,
  `seq` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `finished` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` bigint(10) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `destination` varchar(255) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category`, `description`, `destination`, `available`) VALUES
(1, 'Otros', 'Productos sin categoria. Estos no se muestran en el menu.', 'cocina', 1),
(15, 'Cervezas', '', 'barra', 1),
(16, 'Comida', '', 'cocina', 1),
(17, 'Cocteles', '', 'barra', 1),
(18, 'Cafes', '', 'barra', 1);

-- --------------------------------------------------------

--
-- Table structure for table `combo`
--

CREATE TABLE `combo` (
  `id` bigint(10) NOT NULL,
  `combo` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `combo_groups`
--

CREATE TABLE `combo_groups` (
  `id` bigint(10) NOT NULL,
  `combo` bigint(10) NOT NULL,
  `group` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `instruction` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `combo_groups_products`
--

CREATE TABLE `combo_groups_products` (
  `id` bigint(10) NOT NULL,
  `combo_group` bigint(10) NOT NULL,
  `product` bigint(10) DEFAULT NULL,
  `variant` bigint(10) DEFAULT NULL,
  `extra` bigint(10) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `qty` int(10) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `combo_item_selected`
--

CREATE TABLE `combo_item_selected` (
  `id` bigint(10) NOT NULL,
  `combo` bigint(10) NOT NULL,
  `item` bigint(10) NOT NULL,
  `type_item` varchar(255) NOT NULL,
  `name_item` text NOT NULL,
  `group_item` bigint(10) NOT NULL,
  `name_group_item` text NOT NULL,
  `type_group_item` varchar(255) NOT NULL,
  `qty` int(10) NOT NULL,
  `forean` bigint(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `extra`
--

CREATE TABLE `extra` (
  `id` bigint(10) NOT NULL,
  `extra` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(10) NOT NULL,
  `batch` bigint(10) NOT NULL,
  `sale_order` bigint(10) NOT NULL,
  `type` varchar(255) NOT NULL,
  `product_id` bigint(10) DEFAULT NULL,
  `variant_id` bigint(10) DEFAULT NULL,
  `combo_id` bigint(10) DEFAULT NULL,
  `extra_id` bigint(10) DEFAULT NULL,
  `extra_item` bigint(10) DEFAULT NULL,
  `name` text NOT NULL,
  `qty` int(10) NOT NULL,
  `price_unit` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `note` text DEFAULT NULL,
  `added_at` datetime NOT NULL,
  `destination` varchar(255) NOT NULL,
  `realized` tinyint(1) NOT NULL DEFAULT 0,
  `payed` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materia`
--

CREATE TABLE `materia` (
  `id` bigint(10) NOT NULL,
  `materia` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `presentation` varchar(255) NOT NULL,
  `content_presentation` int(10) NOT NULL,
  `metric` varchar(255) NOT NULL,
  `content_unit` int(10) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materia_adjustment`
--

CREATE TABLE `materia_adjustment` (
  `id` bigint(10) NOT NULL,
  `date` datetime NOT NULL,
  `adjustment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `materia_purchases`
--

CREATE TABLE `materia_purchases` (
  `id` bigint(10) NOT NULL,
  `adjustment` bigint(10) NOT NULL,
  `materia` bigint(10) NOT NULL,
  `qty` int(11) NOT NULL,
  `units` decimal(10,2) NOT NULL,
  `amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` bigint(10) NOT NULL,
  `product` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `img` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `category` bigint(10) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipe`
--

CREATE TABLE `recipe` (
  `id` bigint(10) NOT NULL,
  `product` bigint(10) NOT NULL,
  `type_product` varchar(255) NOT NULL,
  `materia` bigint(10) NOT NULL,
  `type_materia` varchar(255) NOT NULL,
  `value` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sale_order`
--

CREATE TABLE `sale_order` (
  `id` bigint(10) NOT NULL,
  `delivery` varchar(255) NOT NULL,
  `n_table` int(10) NOT NULL,
  `client` text DEFAULT NULL,
  `waiter` bigint(10) NOT NULL,
  `status` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `modified_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `deposit` tinyint(1) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(10) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `value` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `keyword`, `value`) VALUES
(1, 'tables', 40);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` bigint(10) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` text NOT NULL,
  `name` varchar(255) NOT NULL,
  `rol` varchar(255) NOT NULL,
  `img` text NOT NULL DEFAULT 'default.webp',
  `add` tinyint(1) NOT NULL,
  `edit` tinyint(1) NOT NULL,
  `trash` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `name`, `rol`, `img`, `add`, `edit`, `trash`, `status`) VALUES
(1, 'DaryTeodoro', '$2y$10$s8tIVo9PIYPQVvxzz/AjgOoXPb8bObUhtoHIcYZ3YZt20L5q8cOD.', 'Darinel Teodoro', 'administrador', 'default.webp', 1, 1, 1, 1),
(2, 'Eiko', '$2y$10$YzIWl8i5Ok9VwGVVFj0.zeT0LpYVerXuW6R1PgwA7zJ/a9sLLkeI2', 'Eiko Yashimoto', 'cajero', 'default.webp', 0, 0, 0, 0),
(3, 'Nano', '$2y$10$Hc2xeY5cLIr2H5cjKTroIehos7LH88IPf9ANO08jmes/xn50JxVEO', 'Eiai Nano', 'cocina', 'default.webp', 0, 0, 0, 0),
(4, 'Ramiro', '$2y$10$/gcAPPg5uu8a2PLCzuZVr.vz07iWlORcLEWQeMYdLNwqyNHCuY0rG', 'Ramiro Sanchez', 'cocina', 'default.webp', 0, 0, 0, 0),
(5, 'Ryo', '$2y$10$Yyq7yb8rRfkCeh17b3RIquSO5wT1osSBvQWyRxGh.zuMbzrkT4MAC', 'Ryo Sakamoto', 'mesero', 'default.webp', 0, 0, 0, 0),
(6, 'Sasha', '$2y$10$1iCZXUMn.9v8dYwkjX93QORt9WP3gS.GcJH65PpbmuoDwPTGrl49.', 'Sasha', 'mesero', 'default.webp', 0, 0, 0, 0),
(7, 'Antony', '$2y$10$ExYYH47GVT62NHgm/yPkne13ArmQ7ko6XO345ZKuae/SG4/KemA/K', 'Antonio Flores', 'administrador', 'default.webp', 0, 0, 0, 0),
(8, 'Beto', '$2y$10$WlgT4F58nH07T.dinaa2F.gTRIXzM6DlstWhgbBBU685ObzkJ1ehS', 'Alberto Perez', 'cocina', 'default.webp', 0, 0, 0, 1),
(9, 'xxxTentation', '$2y$10$kpr1qW4uObEMXr5i9jfmPOvnHlmrs11augX9WPJ3ZtaKTFM7tvlcm', 'Lil Peep', 'administrador', 'default.webp', 0, 0, 0, 0),
(10, 'xxxTentation', '$2y$10$RUnaYhL85GN3IDga5daoLeXcV1uonjhvVwN4d/TLG0mroN9u1nkrW', 'Lil Peep', 'administrador', 'default.webp', 0, 0, 0, 0),
(11, 'DaryTeodoro1', '$2y$10$5H1VDtsjXsImk1v8VS7VQeYIa3N0/gag4RNN6HUrJ57gMcAezATcW', 'Darinel Teodoro', 'administrador', 'default.webp', 0, 0, 0, 0),
(12, 'Antony', '$2y$10$fAwdx/lsIdIOvQSPOT0V3ebdh4QAE56eWtPlm2LPoa99qqXgXfM0K', 'Antonio Flores', 'cajero', 'default.webp', 0, 0, 0, 0),
(13, 'Antony', '$2y$10$lQw1ol5CmYfm0HFFHlmH9erMkxC5MST.qmjX1DoMrbqLbbkWolaMi', 'Antonio Flores', 'mesero', 'default.webp', 0, 0, 0, 1),
(14, 'Marcos', '$2y$10$hIXBXpPCjh.C3aBtUZEN6euIWEz5Ilt1G5OvSCpBptHV0DwaROc7q', 'Marcos Cruz', 'cajero', 'default.webp', 0, 0, 0, 1),
(15, 'Sara', '$2y$10$tzGLd5zBaK/KPRW7o7hS1.z413rUpu94fmIj4BQ98yaECyEMYAcsG', 'Sarah', 'barra', 'default.webp', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `variant`
--

CREATE TABLE `variant` (
  `id` bigint(10) NOT NULL,
  `product` bigint(10) NOT NULL,
  `variant` varchar(255) NOT NULL,
  `increase` decimal(10,2) NOT NULL,
  `available` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `batch`
--
ALTER TABLE `batch`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `combo`
--
ALTER TABLE `combo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `combo_groups`
--
ALTER TABLE `combo_groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `combo_groups_products`
--
ALTER TABLE `combo_groups_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `combo_item_selected`
--
ALTER TABLE `combo_item_selected`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `extra`
--
ALTER TABLE `extra`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materia`
--
ALTER TABLE `materia`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materia_adjustment`
--
ALTER TABLE `materia_adjustment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materia_purchases`
--
ALTER TABLE `materia_purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `recipe`
--
ALTER TABLE `recipe`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sale_order`
--
ALTER TABLE `sale_order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variant`
--
ALTER TABLE `variant`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `batch`
--
ALTER TABLE `batch`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `combo`
--
ALTER TABLE `combo`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `combo_groups`
--
ALTER TABLE `combo_groups`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `combo_groups_products`
--
ALTER TABLE `combo_groups_products`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `combo_item_selected`
--
ALTER TABLE `combo_item_selected`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `extra`
--
ALTER TABLE `extra`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materia`
--
ALTER TABLE `materia`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materia_adjustment`
--
ALTER TABLE `materia_adjustment`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `materia_purchases`
--
ALTER TABLE `materia_purchases`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipe`
--
ALTER TABLE `recipe`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sale_order`
--
ALTER TABLE `sale_order`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `variant`
--
ALTER TABLE `variant`
  MODIFY `id` bigint(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
