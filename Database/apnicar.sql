-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 09, 2026 at 04:27 PM
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
-- Database: `apnicar`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'maliom33', 'maliom2508@gmail.com', '5946aec00a35fd9d956898130f237102ed012d95', '2025-11-10 22:17:39');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `admin_email` varchar(255) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `admin_email`, `booking_id`, `action`, `note`, `created_at`) VALUES
(6, 'maliom2508@gmail.com', 574681292, 'approve', 'Approved booking by admin maliom2508@gmail.com', '2025-11-11 00:38:15'),
(7, 'maliom2508@gmail.com', 574681293, 'approve', 'Approved booking by admin maliom2508@gmail.com', '2025-11-11 00:49:08'),
(8, 'maliom2508@gmail.com', 574681293, 'approve', 'Approved booking by admin maliom2508@gmail.com', '2025-11-11 00:50:40'),
(9, 'maliom2508@gmail.com', 574681294, 'approve', 'Approved booking by admin maliom2508@gmail.com', '2025-11-15 13:44:27'),
(10, 'maliom2508@gmail.com', 574681296, 'approve', 'Approved booking by admin maliom2508@gmail.com', '2025-11-20 15:38:35'),
(11, 'maliom2508@gmail.com', 574681296, 'release', 'Released booking and made car available by admin maliom2508@gmail.com', '2025-11-20 15:41:30'),
(12, 'maliom2508@gmail.com', 574681297, 'approve', 'Approved booking by admin maliom2508@gmail.com', '2025-11-20 15:41:39'),
(13, 'maliom2508@gmail.com', 574681298, 'reject', 'Rejected by admin', '2025-11-22 13:54:28'),
(14, 'maliom2508@gmail.com', 574681295, 'reject', 'Rejected by admin', '2025-11-22 13:54:33'),
(15, 'maliom2508@gmail.com', 574681299, 'approve', 'Approved booking by admin maliom2508@gmail.com', '2025-11-22 14:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `car_id` int(20) NOT NULL,
  `car_name` varchar(50) NOT NULL,
  `car_nameplate` varchar(50) NOT NULL,
  `car_img` varchar(50) DEFAULT 'NA',
  `ac_price` float NOT NULL,
  `non_ac_price` float NOT NULL,
  `ac_price_per_day` float NOT NULL,
  `non_ac_price_per_day` float NOT NULL,
  `car_availability` varchar(10) NOT NULL,
  `booked_until` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`car_id`, `car_name`, `car_nameplate`, `car_img`, `ac_price`, `non_ac_price`, `ac_price_per_day`, `non_ac_price_per_day`, `car_availability`, `booked_until`) VALUES
(1, 'Audi A4', 'GA3KA6969', 'assets/img/cars/audi-a4.jpg', 36, 26, 5200, 2600, 'yes', NULL),
(2, 'Hyundai Creta', 'BA2CH2020', 'assets/img/cars/creta.jpg', 22, 12, 2900, 1400, 'yes', NULL),
(3, 'BMW 6-Series', 'BA10PA5555', 'assets/img/cars/bmw6.jpg', 39, 30, 6950, 5999, 'yes', NULL),
(4, 'Mercedes-Benz E-Class', 'BA10CH6009', 'assets/img/cars/mcec.jpg', 45, 30, 7200, 5200, 'yes', NULL),
(6, 'Ford EcoSport', 'GA4PA2587', 'assets/img/cars/ecosport.png', 21, 13, 3890, 2600, 'yes', NULL),
(8, 'Land Rover Range Rover Sport', 'GA5KH9669', 'assets/img/cars/rangero.jpg', 36, 26, 6000, 4600, 'yes', NULL),
(9, 'MG Hector', 'GA6PA6666', 'assets/img/cars/mghector.jpg', 20, 12, 2900, 1400, 'yes', NULL),
(10, 'Honda CR-V', 'TN17MS1997', 'assets/img/cars/hondacr.jpg', 22, 15, 2850, 1400, 'yes', NULL),
(11, 'Mahindra XUV 500', 'KA12EX1883', 'assets/img/cars/Mahindra XUV.jpg', 15, 13, 3000, 2600, 'yes', NULL),
(12, 'Toyota Fortuner', 'GA08MX1997', 'assets/img/cars/Fortuner.png', 16, 14, 3200, 2800, 'yes', NULL),
(13, 'Hyundai Veloster', 'BA20PA5685', 'assets/img/cars/hyundai0.png', 23, 15, 4500, 3500, 'yes', NULL),
(14, 'Jaguar XF', 'GA8KH8866', 'assets/img/cars/jaguarxf.jpg', 39, 29, 6100, 4380, 'yes', NULL),
(15, 'Thar', 'MH19CA9927', 'assets/img/cars/car_17627220839974.jpg', 30, 6000, 120, 1200, 'no', '2025-11-25'),
(16, 'Honda Amaze', 'MH19AC1404', 'assets/img/cars/car_17628015618191.jpeg', 40, 20, 5000, 2000, 'yes', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `name` varchar(20) NOT NULL,
  `e_mail` varchar(30) NOT NULL,
  `message` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`name`, `e_mail`, `message`) VALUES
('Nikhil', 'nikhil@gmail.com', 'Hope this works.');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `type`, `created_at`, `used`) VALUES
(1, 'shubham123@gmail.com', '898c3e75324368479f1a93d8b2d380b0', 'user', '2025-11-15 09:47:51', 0),
(2, 'shubham123@gmail.com', 'e4f49529e849768d550444da9f3e94eb', 'user', '2025-11-15 09:48:07', 0),
(3, 'maliom2508@gmail.com', 'e5da42d561c18f492e8cf327bb19ccb0', 'admin', '2025-11-19 08:48:48', 1),
(4, 'shubham123@gmail.com', '92474504145d97c12dad08fe6d0ced24', 'user', '2025-11-19 08:59:33', 1);

-- --------------------------------------------------------

--
-- Table structure for table `rentedcars`
--

CREATE TABLE `rentedcars` (
  `id` int(100) NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `car_id` int(20) NOT NULL,
  `booking_date` date NOT NULL,
  `rent_start_date` date NOT NULL,
  `rent_end_date` date NOT NULL,
  `fare` double NOT NULL,
  `charge_type` varchar(25) NOT NULL DEFAULT 'days',
  `distance` double DEFAULT NULL,
  `no_of_days` int(50) DEFAULT NULL,
  `total_amount` double DEFAULT NULL,
  `return_status` varchar(10) NOT NULL,
  `booking_status` varchar(20) DEFAULT 'pending',
  `payment_status` varchar(20) DEFAULT 'unpaid',
  `payment_method` varchar(50) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `returned_at` datetime DEFAULT NULL,
  `car_condition` varchar(50) DEFAULT NULL,
  `odometer_return` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `rentedcars`
--

INSERT INTO `rentedcars` (`id`, `email`, `car_id`, `booking_date`, `rent_start_date`, `rent_end_date`, `fare`, `charge_type`, `distance`, `no_of_days`, `total_amount`, `return_status`, `booking_status`, `payment_status`, `payment_method`, `paid_at`, `returned_at`, `car_condition`, `odometer_return`) VALUES
(574681292, 'maliom450@gmail.com', 1, '2025-11-10', '2025-11-12', '2025-11-13', 5200, 'per_day', 0, 1, 5200, 'R', 'approved', 'unpaid', NULL, NULL, '2025-11-11 00:42:06', 'good', 100),
(574681293, 'shubham123@gmail.com', 15, '2025-11-10', '2025-11-12', '2025-11-14', 120, 'per_day', 0, 2, 240, 'R', 'approved', 'paid', 'upi', '2025-11-11 00:50:06', '2025-11-11 00:50:33', 'good', 120),
(574681294, 'maliom450@gmail.com', 15, '2025-11-15', '2025-11-16', '2025-11-18', 120, 'per_day', 0, 2, 240, 'R', 'approved', 'paid', 'cash', '2025-11-15 13:46:06', '2025-11-20 15:41:00', 'good', 100),
(574681295, 'maliom450@gmail.com', 15, '2025-11-15', '2025-11-16', '2025-11-18', 120, 'per_day', 0, 2, 240, 'NR', 'rejected', 'unpaid', NULL, NULL, NULL, NULL, NULL),
(574681296, 'maliom450@gmail.com', 2, '2025-11-20', '2025-11-20', '2025-11-21', 2900, 'per_day', 0, 1, 2900, 'R', 'approved', 'paid', 'cash', '2025-11-20 15:39:34', '2025-11-20 15:40:00', 'good', 200),
(574681297, 'maliom450@gmail.com', 1, '2025-11-20', '2025-11-21', '2025-11-22', 5200, 'per_day', 0, 1, 5200, 'R', 'approved', 'unpaid', NULL, NULL, '2025-11-22 13:53:19', 'good', 100),
(574681298, 'maliom450@gmail.com', 1, '2025-11-20', '2025-11-21', '2025-11-22', 5200, 'per_day', 0, 1, 5200, 'NR', 'rejected', 'unpaid', NULL, NULL, NULL, NULL, NULL),
(574681299, 'maliom450@gmail.com', 15, '2025-11-22', '2025-11-23', '2025-11-25', 120, 'per_day', 0, 2, 240, 'NR', 'approved', 'unpaid', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userdetails`
--

CREATE TABLE `userdetails` (
  `name` text NOT NULL,
  `licenceNumber` text NOT NULL,
  `phoneNumber` text NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `gender` text NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userdetails`
--

INSERT INTO `userdetails` (`name`, `licenceNumber`, `phoneNumber`, `email`, `password`, `gender`, `is_admin`) VALUES
('Om Devendra Mali', 'ABC12345DEMAHA12', '8275330557', 'maliom4500@gmail.com', '8f9ed526e78049778be8fb6f5fbaf6574eb47ea4', 'male', 0),
('Om Devendra Mali', 'ABC12345DEMAHA12', '8275330557', 'maliom450@gmail.com', '5946aec00a35fd9d956898130f237102ed012d95', 'male', 0),
('Shubham Suresh Chravande', 'ABC12345DEMAHA12', '9172391801', 'shubham123@gmail.com', '5946aec00a35fd9d956898130f237102ed012d95', 'male', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`car_id`),
  ADD UNIQUE KEY `car_nameplate` (`car_nameplate`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `token` (`token`);

--
-- Indexes for table `rentedcars`
--
ALTER TABLE `rentedcars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `fk_rentedcars_userdetails` (`email`);

--
-- Indexes for table `userdetails`
--
ALTER TABLE `userdetails`
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `car_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rentedcars`
--
ALTER TABLE `rentedcars`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=574681300;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `rentedcars`
--
ALTER TABLE `rentedcars`
  ADD CONSTRAINT `fk_rentedcars_userdetails` FOREIGN KEY (`email`) REFERENCES `userdetails` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rentedcars_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`car_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
