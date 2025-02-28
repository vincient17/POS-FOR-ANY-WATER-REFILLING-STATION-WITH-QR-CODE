-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 13, 2025 at 06:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `water_refilling`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_borrowedgallon`
--

CREATE TABLE `tbl_borrowedgallon` (
  `borrowed_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `gallon_id` int(11) NOT NULL,
  `date_borrowed` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_borrowedgallon`
--

INSERT INTO `tbl_borrowedgallon` (`borrowed_id`, `cus_id`, `gallon_id`, `date_borrowed`) VALUES
(1, 2, 1, '2024-12-16 13:50:27'),
(2, 2, 2, '2024-12-16 13:50:27'),
(3, 2, 3, '2024-12-16 13:50:27'),
(4, 2, 4, '2024-12-16 13:50:27'),
(5, 3, 5, '2024-12-16 13:50:33'),
(6, 3, 6, '2024-12-16 13:50:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_cash_denomination`
--

CREATE TABLE `tbl_cash_denomination` (
  `id` int(11) NOT NULL,
  `unit_1` int(11) DEFAULT 0,
  `unit_5` int(11) DEFAULT 0,
  `unit_10` int(11) DEFAULT 0,
  `unit_20` int(11) DEFAULT 0,
  `unit_50` int(11) DEFAULT 0,
  `unit_100` int(11) DEFAULT 0,
  `unit_200` int(11) DEFAULT 0,
  `unit_500` int(11) DEFAULT 0,
  `unit_1000` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_cash_denomination`
--

INSERT INTO `tbl_cash_denomination` (`id`, `unit_1`, `unit_5`, `unit_10`, `unit_20`, `unit_50`, `unit_100`, `unit_200`, `unit_500`, `unit_1000`) VALUES
(1, 0, 0, 0, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_category`
--

CREATE TABLE `tbl_category` (
  `category_id` int(11) NOT NULL,
  `category_description` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_category`
--

INSERT INTO `tbl_category` (`category_id`, `category_description`) VALUES
(1, 'Water'),
(2, 'Container (w/Faucet)');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customers`
--

CREATE TABLE `tbl_customers` (
  `cus_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `address` varchar(80) NOT NULL,
  `cp_num` varchar(15) NOT NULL,
  `cus_unique_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customers`
--

INSERT INTO `tbl_customers` (`cus_id`, `name`, `address`, `cp_num`, `cus_unique_code`) VALUES
(1, 'Guest', '', '', '0000000000'),
(2, 'Carl Vincient Luib', 'P-6 Don Ruben, San Jose, Dinagat Islands', '9883794737', '3876927967'),
(3, 'Angel Morales', 'P-4 Justiniana, San Jose, Dinagat Islands', '9739748363', '9621940580'),
(4, 'Nick Trazo ', 'P-5 Aurelio, San Jose, Dinagat Islands', '9376583938', '2877369049'),
(5, 'Annamarie Tubog', 'P-2 Sta. Cruz, San Jose, Dinagat Islands', '9374785738', '5795628295');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_gallon`
--

CREATE TABLE `tbl_gallon` (
  `gallon_id` int(11) NOT NULL,
  `unique_code` varchar(30) NOT NULL,
  `status` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_gallon`
--

INSERT INTO `tbl_gallon` (`gallon_id`, `unique_code`, `status`) VALUES
(1, 'Purified_WRS00001', 'Borrowed'),
(2, 'Purified_WRS00002', 'Borrowed'),
(3, 'Purified_WRS00003', 'Borrowed'),
(4, 'Purified_WRS00004', 'Borrowed'),
(5, 'Purified_WRS00005', 'Borrowed'),
(6, 'Purified_WRS00006', 'Borrowed'),
(7, 'Purified_WRS00007', 'New'),
(8, 'Purified_WRS00008', 'New'),
(9, 'Purified_WRS00009', 'New'),
(10, 'Purified_WRS00010', 'New');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_products`
--

CREATE TABLE `tbl_products` (
  `product_id` int(11) NOT NULL,
  `p_name` varchar(30) NOT NULL,
  `p_price` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_products`
--

INSERT INTO `tbl_products` (`product_id`, `p_name`, `p_price`, `quantity`, `category_id`) VALUES
(1, 'Refill', 20, 99999999, 1),
(2, 'Container', 220, 7, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_remit`
--

CREATE TABLE `tbl_remit` (
  `remit_id` int(11) NOT NULL,
  `p1` int(11) NOT NULL,
  `p5` int(11) NOT NULL,
  `p10` int(11) NOT NULL,
  `p20` int(11) NOT NULL,
  `p50` int(11) NOT NULL,
  `p100` int(11) NOT NULL,
  `p200` int(11) NOT NULL,
  `p500` int(11) NOT NULL,
  `p1000` int(11) NOT NULL,
  `date_remitted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_remit`
--

INSERT INTO `tbl_remit` (`remit_id`, `p1`, `p5`, `p10`, `p20`, `p50`, `p100`, `p200`, `p500`, `p1000`, `date_remitted`) VALUES
(2, 0, 0, 0, 0, 0, 2, 1, 2, 0, '2024-12-16 21:41:09'),
(3, 0, 0, 0, 2, 0, 0, 1, 0, 0, '2024-12-17 14:06:50');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_returnedgallon`
--

CREATE TABLE `tbl_returnedgallon` (
  `returned_id` int(11) NOT NULL,
  `gallon_id` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `date_borrowed` datetime NOT NULL,
  `date_returned` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_sales`
--

CREATE TABLE `tbl_sales` (
  `sales_ID` int(11) NOT NULL,
  `cus_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_service` varchar(8) NOT NULL,
  `total_order` int(11) NOT NULL,
  `total_price` int(11) NOT NULL,
  `date_sold` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_sales`
--

INSERT INTO `tbl_sales` (`sales_ID`, `cus_id`, `product_id`, `customer_service`, `total_order`, `total_price`, `date_sold`) VALUES
(1, 2, 1, 'Pick-up', 12, 240, '2024-12-16 13:32:32'),
(2, 4, 2, 'Deliver', 3, 660, '2024-12-16 13:35:44'),
(3, 5, 1, 'Pick-up', 5, 100, '2024-12-16 13:36:00'),
(4, 3, 1, 'Deliver', 20, 400, '2024-12-16 13:36:15'),
(5, 1, 1, 'Pick-up', 12, 240, '2024-12-17 06:05:50');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_system_setup`
--

CREATE TABLE `tbl_system_setup` (
  `setup_id` int(11) NOT NULL,
  `WRS_name` varchar(100) NOT NULL,
  `WRS_acronym` varchar(20) NOT NULL,
  `WRS_logo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_system_setup`
--

INSERT INTO `tbl_system_setup` (`setup_id`, `WRS_name`, `WRS_acronym`, `WRS_logo`) VALUES
(1, 'Purified Water Refilling Station', 'Purified_WRS', 'sample_logo.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_borrowedgallon`
--
ALTER TABLE `tbl_borrowedgallon`
  ADD PRIMARY KEY (`borrowed_id`);

--
-- Indexes for table `tbl_cash_denomination`
--
ALTER TABLE `tbl_cash_denomination`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_category`
--
ALTER TABLE `tbl_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  ADD PRIMARY KEY (`cus_id`);

--
-- Indexes for table `tbl_gallon`
--
ALTER TABLE `tbl_gallon`
  ADD PRIMARY KEY (`gallon_id`);

--
-- Indexes for table `tbl_products`
--
ALTER TABLE `tbl_products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `tbl_remit`
--
ALTER TABLE `tbl_remit`
  ADD PRIMARY KEY (`remit_id`);

--
-- Indexes for table `tbl_returnedgallon`
--
ALTER TABLE `tbl_returnedgallon`
  ADD PRIMARY KEY (`returned_id`);

--
-- Indexes for table `tbl_sales`
--
ALTER TABLE `tbl_sales`
  ADD PRIMARY KEY (`sales_ID`);

--
-- Indexes for table `tbl_system_setup`
--
ALTER TABLE `tbl_system_setup`
  ADD PRIMARY KEY (`setup_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_borrowedgallon`
--
ALTER TABLE `tbl_borrowedgallon`
  MODIFY `borrowed_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_cash_denomination`
--
ALTER TABLE `tbl_cash_denomination`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_category`
--
ALTER TABLE `tbl_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  MODIFY `cus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_gallon`
--
ALTER TABLE `tbl_gallon`
  MODIFY `gallon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_products`
--
ALTER TABLE `tbl_products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_remit`
--
ALTER TABLE `tbl_remit`
  MODIFY `remit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_returnedgallon`
--
ALTER TABLE `tbl_returnedgallon`
  MODIFY `returned_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_sales`
--
ALTER TABLE `tbl_sales`
  MODIFY `sales_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_system_setup`
--
ALTER TABLE `tbl_system_setup`
  MODIFY `setup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
