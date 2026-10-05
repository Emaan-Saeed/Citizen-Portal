-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 05, 2026 at 02:42 PM
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
-- Database: `citizen_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(200) DEFAULT NULL,
  `priority` varchar(20) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `category`, `subject`, `description`, `location`, `priority`, `file`, `status`, `created_at`) VALUES
(9, 1, 'Education', 'unavailability of professional teachers', 'we want more qualified teachers', 'saddar, rawalpindi', 'Medium', '', 'Resolved', '2026-02-07 21:01:30'),
(10, 2, 'Water', 'Shortage of Water supply in our area', 'From last 2 days the water lines are dead. Please resolve the issue at earliest.', 'Tench Bhatta, Rawalpindi', 'High', '1770537520_WhatsApp Image 2026-02-05 at 6.16.26 PM.jpeg', 'Pending', '2026-02-08 07:58:40'),
(11, 1, 'Road', 'Massacre of abc Road', 'Please reconstruct abc road as it causes accidents in city.', 'Islamabad', 'Low', '1770572320_ChatGPT Image Jan 10, 2026, 01_25_43 AM.png', 'Pending', '2026-02-08 17:38:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `cnic` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `cnic`, `email`, `phone`, `password`, `city`) VALUES
(1, 'Emaan Saeed', '34567898763234567', 'emaaantious@gmail.com', '03002345677', 'allah123\r\n\r\n', 'rwp'),
(2, 'Fizza Aamir', '37405234567876543', 'fizza@gmail.com', '03007657765432', '$2y$10$Zvp6nq3PJXRcUAsA2PCqn.4G.vZvJr5cHCF1Pg.0XYzoZ4RWlvRpO', 'rwp'),
(3, 'Harram Saeed', '34567890', 'harram@gmail.com', '1234567890', '$2y$10$3dwA9N9BJw6525CYl.6FpOYdkBi0yQIvmaNa2FbT.cv.fc1BcnTpC', 'Islamabad');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
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
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
