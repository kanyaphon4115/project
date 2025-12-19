-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2025 at 03:52 AM
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
-- Database: `pet_home`
--

-- --------------------------------------------------------

--
-- Table structure for table `adopt_forms`
--

CREATE TABLE `adopt_forms` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dog_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `contact` varchar(255) NOT NULL,
  `area` text DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `time_home` int(11) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `family_agree` varchar(20) DEFAULT NULL,
  `care_time` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `adopt_forms`
--

INSERT INTO `adopt_forms` (`id`, `user_id`, `dog_id`, `fullname`, `address`, `phone`, `contact`, `area`, `experience`, `time_home`, `reason`, `family_agree`, `care_time`, `created_at`) VALUES
(1, 7, 0, 'เชี่ยวชาญ ว่องไว', 'กรุงเทพ', '0659874561', '', 'บ้านมีพื้นที่กว้าง มีลานหญ้าให้สุนัขวิ่งเล่น', 'เคยเลี้ยงมาก่อน', 10, 'อยากมีสัตว์เลี้ยงสักตัว มาอยู่ด้วยคลายเหงา', 'Yes', 'ทำงาน freelance สามารถอยู่ดูได้ตลอดเวลา', '2025-12-19 00:11:14'),
(2, 7, 2, 'เชี่ยวชาญ ว่องไว', NULL, NULL, '', 'บ้านมีพื้นที่กว้าง มีลานหญ้าให้สุนัขวิ่งเล่น', 'เคยเลี้ยงมาก่อน', 10, 'อยากมีสัตว์เลี้ยงสักตัว มาอยู่ด้วยคลายเหงา', 'Yes', 'ทำงาน freelance สามารถอยู่ดูได้ตลอดเวลา', '2025-12-19 00:29:14'),
(3, 7, 3, 'เชี่ยวชาญ ว่องไว', NULL, NULL, '', 'บ้านมีพื้นที่กว้าง มีลานหญ้าให้สุนัขวิ่งเล่น', 'เคยเลี้ยงมาก่อน', 10, 'อยากมีสัตว์เลี้ยงสักตัว มาอยู่ด้วยคลายเหงา', 'Yes', 'ทำงาน freelance สามารถอยู่ดูได้ตลอดเวลา', '2025-12-19 00:30:40'),
(4, 7, 1, 'เชี่ยวชาญ ว่องไว', NULL, NULL, '', 'บ้านมีพื้นที่กว้าง มีลานหญ้าให้สุนัขวิ่งเล่น', 'เคยเลี้ยงมาก่อน', 10, 'อยากมีสัตว์เลี้ยงสักตัว มาอยู่ด้วยคลายเหงา', 'Yes', 'ทำงาน freelance สามารถอยู่ดูได้ตลอดเวลา', '2025-12-19 00:30:49'),
(5, 7, 4, 'เชี่ยวชาญ ว่องไว', NULL, NULL, '', 'บ้านมีพื้นที่กว้าง มีลานหญ้าให้สุนัขวิ่งเล่น', 'เคยเลี้ยงมาก่อน', 10, 'อยากมีสัตว์เลี้ยงสักตัว มาอยู่ด้วยคลายเหงา', 'Yes', 'ทำงาน freelance สามารถอยู่ดูได้ตลอดเวลา', '2025-12-19 00:30:54');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `sender_id`, `receiver_id`, `message`, `created_at`) VALUES
(1, 1, 2, 'Hello, can I ask you something?', '2025-12-17 19:52:26'),
(2, 2, 1, 'Sure, how can I help you?', '2025-12-17 19:52:26'),
(3, 1, 2, 'Where can I pick up the dog?', '2025-12-17 19:52:26'),
(4, 2, 1, 'You can come to Chonburi 😊', '2025-12-17 19:52:26');

-- --------------------------------------------------------

--
-- Table structure for table `dogs`
--

CREATE TABLE `dogs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `breed` varchar(120) NOT NULL,
  `age` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dogs`
--

INSERT INTO `dogs` (`id`, `name`, `breed`, `age`, `gender`, `description`, `image`, `created_at`) VALUES
(1, 'Buggy', 'Jack Russell', '6 months', 'Male', 'Energetic and playful Jack Russell.', 'pets/Buggy.jpg', '2025-12-17 19:52:45'),
(2, 'Peach', 'Shih Tzu', '4 months', 'Female', 'Calm and lovely Shih Tzu puppy.', 'pets/Peach.jpg', '2025-12-17 19:52:45'),
(3, 'Gary', 'Yorkshire Terrier', '3 years', 'Female', 'Small but brave Yorkie.', 'pets/Gary.jpg', '2025-12-17 19:52:45'),
(4, 'Willie', 'Samoyed', '1.5 years', 'Male', 'Fluffy Samoyed with a happy smile.', 'pets/Willie.jpg', '2025-12-17 19:52:45'),
(5, 'Kiwi', 'Yorkshire Terrier', '1 year', 'Male', 'Friendly and curious dog.', 'pets/Kiwi.jpg', '2025-12-17 19:52:45'),
(6, 'Milo', 'Yorkshire Terrier', '4 months', 'Male', 'Small but brave Yorkie puppy, loves attention and cuddles.', 'pets/Milo.jpg', '2025-12-18 18:39:00'),
(7, 'Bella', 'Yorkshire Terrier', '2 years', 'Female', 'Gentle Yorkie who is calm, friendly, and easy to train.', 'pets/Bella.jpg', '2025-12-18 18:39:00'),
(8, 'Coco', 'Shih Tzu', '5 months', 'Female', 'Sweet Shih Tzu puppy, calm and loves being carried.', 'pets/Coco.jpg', '2025-12-18 18:39:00'),
(9, 'Teddy', 'Shih Tzu', '1 year', 'Male', 'Fluffy Shih Tzu with a playful mood and a happy smile.', 'pets/Teddy.jpg', '2025-12-18 18:39:00'),
(10, 'Luna', 'Samoyed', '1.5 years', 'Female', 'Fluffy Samoyed with a cheerful vibe and loves people.', 'pets/Luna.jpg', '2025-12-18 18:39:00'),
(11, 'Nala', 'Jack Russell', '8 weeks', 'Female', 'Energetic little pup, curious and super active.', 'pets/Nala.jpg', '2025-12-18 18:39:00'),
(12, 'Charlie', 'Corgi', '6 months', 'Male', 'Playful corgi puppy who loves to follow you everywhere.', 'pets/Charlie.jpg', '2025-12-18 18:39:00');

-- --------------------------------------------------------

--
-- Table structure for table `donate_bank`
--

CREATE TABLE `donate_bank` (
  `id` int(11) NOT NULL,
  `donor_name` varchar(255) NOT NULL,
  `slip_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donate_bank`
--

INSERT INTO `donate_bank` (`id`, `donor_name`, `slip_path`, `created_at`) VALUES
(1, 'เชี่ยวชาญ ว่องไว', '1766106573_9556.png', '2025-12-19 01:09:33');

-- --------------------------------------------------------

--
-- Table structure for table `donate_items`
--

CREATE TABLE `donate_items` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `items` text NOT NULL,
  `send_type` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `donate_items`
--

INSERT INTO `donate_items` (`id`, `fullname`, `contact`, `items`, `send_type`, `created_at`) VALUES
(5, 'เชี่ยวชาญ ว่องไว', '0631934486', 'อาหาร 8 กระสอบ', 'จัดส่งเองที่ศูนย์', '2025-12-19 01:44:00');

-- --------------------------------------------------------

--
-- Table structure for table `form`
--

CREATE TABLE `form` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `pass` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form`
--

INSERT INTO `form` (`id`, `email`, `username`, `pass`, `role`) VALUES
(1, 'kanyaporn4115k@gmail.com', NULL, '123456', 'admin'),
(5, 'crazylife555@gmail.com', NULL, '232324', 'user'),
(7, 'wefer6381@gmail.com', NULL, '$2y$10$ONSXjOLoHg25Ug1rqwu/PeuuwucGp6rrHyr71ReCxD1ICShBTa31G', 'admin'),
(8, 'dfhgfhgjghjgh@gmail.com', NULL, '223', 'user'),
(9, 'rungpailinn@gmail.com', NULL, '$2y$10$uLIGrYQVSZbb0FxeMKhKt.aWXv7qQR2kLXm6sIztJ2Rm0nzSeh.wW', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `otp_hash`, `expires_at`, `used`, `attempts`, `created_at`) VALUES
(1, 7, '$2y$10$CO9ZESJM7YEm7pnQDLx3K.kjW1xSmA41e207bddfxX1kWsZhnABoq', '2025-12-17 22:12:44', 0, 0, '2025-12-17 21:02:44'),
(2, 7, '$2y$10$B6VSgVnUGzYUvH8wHwiBTehCBEa4EBS1Zvhu2zcWKbz1Sj6HZe5z6', '2025-12-17 22:15:04', 0, 0, '2025-12-17 21:05:04'),
(3, 7, '$2y$10$Dag.Qo73Ktwo1eaFXr2fTeJf2DBjxiuCJ7zfZ3PfyKBAiAqI0j6VW', '2025-12-17 22:15:45', 0, 0, '2025-12-17 21:05:45'),
(4, 7, '$2y$10$0niXsFDfX9PIZyaOFeKEkuh7c.0zIzxRYg36rWsVHEvPanHlMzsz.', '2025-12-18 04:22:13', 0, 0, '2025-12-17 21:12:13'),
(5, 8, '$2y$10$jzRn034m9JUGYMPuQQtv7eZKQoqvS2PgzV2i9RlM1.SNXCyQWJupy', '2025-12-18 04:25:24', 0, 0, '2025-12-17 21:15:24'),
(6, 7, '$2y$10$x.zRXLj/TIqWGcscTJEzm.NXL0.fJ.n1xTBBKYvOaEnjYtvOpCY2.', '2025-12-18 04:28:50', 1, 0, '2025-12-17 21:18:50'),
(7, 7, '$2y$10$F6BvH7rR3hwmv.RuoJ5goeqP.MirtGa75usP2mpBw0bGqCgjEESha', '2025-12-18 04:48:31', 1, 0, '2025-12-17 21:38:31'),
(8, 7, '$2y$10$t4BsPpWU3Hn/BXfgyJO.uu6P9vn4EcaQ4RIa6YRZvU.escSXGxRlK', '2025-12-18 05:09:11', 1, 0, '2025-12-17 21:59:11'),
(9, 7, '$2y$10$HGzLIz5RGd3hqMSjiQVykOENDiXnvuQ1611EacxGZSJPcpIJ34NFO', '2025-12-18 05:16:09', 1, 0, '2025-12-17 22:06:09'),
(10, 7, '$2y$10$f.3qBpW047Uis5SP8UiIyuevr/WuR6KGItxkE/T2j1V/m8XSJuTsW', '2025-12-19 00:19:37', 0, 0, '2025-12-18 17:09:37'),
(11, 9, '$2y$10$uTBssMcALHM11OsUWiRVQOPvZEUpIfoy/Fm3xtZ0lM1v6Hk1/br1i', '2025-12-19 00:21:57', 1, 0, '2025-12-18 17:11:57'),
(12, 7, '$2y$10$N9/D50pNyZhOj2U8HYlcF.tnEtzcSTMjPWFiszZ/g0UVuQ/fCxS2y', '2025-12-19 01:02:06', 0, 0, '2025-12-18 17:52:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `adopt_forms`
--
ALTER TABLE `adopt_forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dogs`
--
ALTER TABLE `dogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donate_bank`
--
ALTER TABLE `donate_bank`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donate_items`
--
ALTER TABLE `donate_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form`
--
ALTER TABLE `form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `expires_at` (`expires_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `adopt_forms`
--
ALTER TABLE `adopt_forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dogs`
--
ALTER TABLE `dogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `donate_bank`
--
ALTER TABLE `donate_bank`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `donate_items`
--
ALTER TABLE `donate_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `form`
--
ALTER TABLE `form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
