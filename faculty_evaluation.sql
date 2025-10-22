-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2025 at 05:47 AM
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
-- Database: `faculty_evaluation`
--

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `question1` int(11) NOT NULL,
  `question2` int(11) NOT NULL,
  `question3` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`id`, `user_id`, `student_name`, `student_id`, `department`, `faculty_name`, `subject`, `question1`, `question2`, `question3`, `comments`, `created_at`) VALUES
(1, 6, 'gio salvador', '234658', 'CCJE', 'prof-santos', 'dasdasd', 5, 4, 3, 'dasdas', '2025-10-22 10:53:03'),
(2, 6, 'Anonymous', '234658', 'CCJE', 'prof-garcia', 'dasdasd', 5, 4, 4, 'fasfdasdas', '2025-10-22 10:54:39');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `department` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `name`, `email`, `department`, `status`, `created_at`) VALUES
(1, 'Prof. Maria Santos', 'maria.santos@wesleyan.edu.ph', 'CECT', 'active', '2025-10-22 10:30:16'),
(2, 'Prof. Juan Reyes', 'juan.reyes@wesleyan.edu.ph', 'CCJE', 'active', '2025-10-22 10:30:16'),
(3, 'Prof. Ana Cruz', 'ana.cruz@wesleyan.edu.ph', 'CON', 'active', '2025-10-22 10:30:16'),
(4, 'Prof. Pedro Garcia', 'pedro.garcia@wesleyan.edu.ph', 'CBA', 'active', '2025-10-22 10:30:16'),
(6, 'Jai Dela Cruz', 'Jai@wesleyan.edu.ph', 'CECT', 'active', '2025-10-22 10:35:37'),
(9, 'Joshua Garcia', 'Josh@gmail.com', 'CHTM', 'active', '2025-10-22 10:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_order` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `question_text`, `question_order`, `is_active`, `created_at`) VALUES
(1, 'The instructor demonstrates mastery of the subject matter', 1, 1, '2025-10-22 10:30:16'),
(2, 'The instructor explains concepts clearly and effectively', 2, 1, '2025-10-22 10:30:16'),
(3, 'The instructor is approachable and responsive to student concerns', 3, 1, '2025-10-22 10:30:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('student','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `created_at`, `updated_at`) VALUES
(6, 'gio salvador', 'gio@wesleyan.edu.ph', '$2y$10$SYTh8edkEgpWwOD.opIOReHM3g4R6.kicrZv4yaz6zZNxI9trgUbS', 'student', '2025-10-21 15:02:16', '2025-10-21 15:02:16'),
(7, 'Juan Dela Cruz', 'Juan@wesleyan.edu.ph', '$2y$10$xBXNfjxdKnqZJ/kkrtOSAOTZED3e4sb8gz/sKdo3fPaK52VffQ6TK', 'student', '2025-10-21 15:18:41', '2025-10-21 15:18:41'),
(11, 'Administrator', 'admin@wesleyan.edu.ph', '$2y$10$sIxC7P87vahFV7FWZRqxSeLCh.dPpohxw40X6kYgyZtt3z5gXMgqq', 'admin', '2025-10-22 02:27:10', '2025-10-22 02:27:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_user_type` (`user_type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
