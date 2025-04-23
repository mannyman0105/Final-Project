-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 04:24 AM
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
-- Database: `finalproject`
--

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `issue_id` int(11) NOT NULL,
  `person_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `issue_id`, `person_id`, `content`, `created_at`, `updated_at`) VALUES
(6, 16, 14, 'Hi Carl! We will add a password reset feature soon!', '2025-04-23 00:16:38', '2025-04-23 00:16:38'),
(8, 18, 27, 'oh...', '2025-04-23 01:58:35', '2025-04-23 01:58:35');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('OPEN','CLOSED') DEFAULT 'OPEN',
  `person_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `subject` varchar(255) NOT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `title`, `description`, `status`, `person_id`, `created_at`, `subject`, `pdf_file`, `filename`) VALUES
(16, 'User', 'How do I reset my password? I struggle to login everytime.', 'OPEN', 27, '2025-04-23 00:15:38', 'I need to reset my password.', '../uploads/1745367338_carlnu.PNG', NULL),
(18, NULL, 'I am blind.', 'OPEN', 31, '2025-04-23 01:02:03', 'I can\'t see', NULL, NULL),
(23, NULL, 'I will buy this app from you if you want. Name a price!', 'OPEN', 35, '2025-04-23 01:53:53', 'Let me rebuild this app', NULL, NULL),
(24, NULL, 'It is literally on fire.', 'OPEN', 36, '2025-04-23 01:54:32', 'My computer keeps overheating', NULL, NULL),
(25, NULL, 'I legit can\'t use the app. I am in the middle of nowhere.', 'OPEN', 37, '2025-04-23 01:56:29', 'My plane crashed', NULL, NULL),
(26, NULL, 'I don\'t know where to go from here.', 'OPEN', 38, '2025-04-23 01:57:38', 'I am lost', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `persons`
--

CREATE TABLE `persons` (
  `id` int(11) NOT NULL,
  `fname` varchar(100) DEFAULT NULL,
  `lname` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `title` enum('Admin','User') NOT NULL,
  `filename` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `persons`
--

INSERT INTO `persons` (`id`, `fname`, `lname`, `email`, `password`, `title`, `filename`) VALUES
(14, 'Emmanuel', 'Danforth', 'mannyman0105@gmail.com', '$2y$10$tsUnK2nkdMxeRzPlAdVab.4WcR/L7term4M4ZalscSx/sU/wi28GC', 'Admin', NULL),
(27, 'Carl', 'Nube', 'carlnu123@gmail.com', '$2y$10$6R68vkgCXFG/4cgIpTNNBOi49ZTPvcDErvXprqJIfSC0AQK5ih96a', 'User', NULL),
(31, 'Matthew', 'Murdock', 'ddmm@gmail.com', '$2y$10$6MB3s.elJfo05EIhAj.ffecj/FYt8rtILLb0Tdl/N4xheGJ.NXRta', 'Admin', NULL),
(35, 'Tony', 'Stark', 'ironman@gmail.com', '$2y$10$LkqGICzGWC5ySaRGREGiTuV5Ux8ggthzrllul9gmnaUYXb9tMmnH2', 'Admin', NULL),
(36, 'Johnny', 'Storm', 'flamon@gmail.com', '$2y$10$X4SMtXMSYEz5YCF3TC2hWO/ic6LznS7zx5xSXIrIIHjfcyLZxA2H.', 'User', NULL),
(37, 'Mark', 'Sloan', 'mcsteamy@gmail.com', '$2y$10$czijSdqUX.MKp/Av4JCysueRHK75JSFtY508rAF2skY2M9Vh4wM0W', 'Admin', NULL),
(38, 'Flat', 'Stanley', 'flatman@gmail.com', '$2y$10$DhHbBuM2xmZq.lGZQZzSLuewNx7A7MBxbeDpzktk4kH6Z5kD6Y8AW', 'User', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `issue_id` (`issue_id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`),
  ADD KEY `person_id` (`person_id`);

--
-- Indexes for table `persons`
--
ALTER TABLE `persons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `persons`
--
ALTER TABLE `persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`);

--
-- Constraints for table `issues`
--
ALTER TABLE `issues`
  ADD CONSTRAINT `issues_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `persons` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
