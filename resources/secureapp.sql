-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 13, 2021 at 02:43 PM
-- Server version: 10.4.20-MariaDB
-- PHP Version: 8.0.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secureapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `file_name` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `title`, `body`, `file_name`, `created_at`) VALUES
(45, 4, 'Beki Complaint 1', 'Insufficient Light', 'lpbuTLj3li1MC6IonLHA3F97q.pdf', '2021-09-13 09:13:07'),
(46, 4, 'Beki Complaint 2', 'No FILE', NULL, '2021-09-13 09:13:19');

-- --------------------------------------------------------

--
-- Table structure for table `secret_questions`
--

CREATE TABLE `secret_questions` (
  `id` int(11) NOT NULL,
  `question` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `secret_questions`
--

INSERT INTO `secret_questions` (`id`, `question`) VALUES
(1, 'What is your first pet\'s name?'),
(2, 'Where did you go to elementary school?'),
(3, 'Where did you go to high school?'),
(4, 'What is your uncle\'s name?'),
(5, 'When did you buy a car?'),
(6, 'What is your first child\'s name?');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 1,
  `failed_attempts` int(11) NOT NULL DEFAULT 0,
  `questions` varchar(11) DEFAULT NULL,
  `answers` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `status`, `failed_attempts`, `questions`, `answers`, `created_at`) VALUES
(1, 'Abel Tefera', 'abel@gmail.com', '$2y$10$ToCNpldFTJy5uE1TKtPMJ.4cLIlNUxipYQZ75EfskPBgweeQMIGUO', 1, 1, 0, '1,2,4', 'Tony,Canan,Sammy', '2021-08-29 13:50:14'),
(2, 'Dagmawi Negussu', 'dagi@gmail.com', '$2y$10$4JD6t9qWbzgx6OhCdjw5/e/HjOkSRzKMaYBvCk/4MQK3sawc3kCOS', 0, 1, 0, '1,3,5', 'Oddy,AB Info,Yesterday', '2021-09-07 16:46:07'),
(3, 'Hana Tesfaye', 'hana@gmail.com', '$2y$10$rvU3CErzJU7U5JPs3QxI5uqPntbYTbGU2xI4xdNAkBnkO4LSaJp96', 0, 1, 0, '5,4,3', 'Never,Gambino,Somewhere', '2021-09-07 16:46:36'),
(4, 'Beki John', 'beki@gmail.com', '$2y$10$uHgwWwkRPA71JaRkoYOe1OfcYXUjgmPWJIawWp6kxdMD2uYWPrKRC', 0, 1, 0, '5,3,1', 'July 2012,Cathedral,Gashe', '2021-09-09 17:04:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `secret_questions`
--
ALTER TABLE `secret_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `secret_questions`
--
ALTER TABLE `secret_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
