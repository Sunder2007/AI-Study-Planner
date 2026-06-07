-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 05, 2026 at 06:04 PM
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
-- Database: `study_planner`
--

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `subject_name`, `exam_date`, `user_id`) VALUES
(1, 'Advance Java', '2026-07-01', 1),
(5, 'Operating System', '2026-07-03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `completed_units` int(11) DEFAULT NULL,
  `total_units` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`id`, `subject_name`, `completed_units`, `total_units`, `user_id`) VALUES
(1, 'Data Structure', 5, 5, 1),
(3, 'DBMS', 4, 5, 1),
(4, 'Operating System', 5, 8, 1),
(5, 'Advance Java', 6, 7, 1),
(6, 'Applied Physics', 4, 9, 1),
(7, 'Communication Skills', 8, 8, 1),
(8, 'Communication Skills', 4, 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(1, 'Sunder Kumar', 'sunder@test.com', '12345', '2026-06-03 10:48:58'),
(2, 'Navin Tripathi', 'navin@test.com', '12345', '2026-06-04 08:02:20'),
(4, 'Tushar Singh Rawat', 'tushar@test.com', '12345', '2026-06-04 09:37:32'),
(5, 'Aman Tiwari', 'aman@test.com', '12345', '2026-06-04 09:39:15');

-- --------------------------------------------------------

--
-- Table structure for table `study_plans`
--

CREATE TABLE `study_plans` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `study_date` date DEFAULT NULL,
  `study_hours` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_plans`
--

INSERT INTO `study_plans` (`id`, `subject_name`, `study_date`, `study_hours`, `user_id`) VALUES
(1, 'Data Structure', '2026-06-01', 5, 1),
(2, 'DBMS', '2026-06-02', 4, 1),
(3, 'DBMS', '2026-05-06', 6, 2),
(4, 'Communication Skills ', '2026-06-05', 6, 1),
(5, 'Applied Physics', '2026-06-02', 5, 1),
(6, 'Energy Conservation', '2026-06-05', 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `teacher_name` varchar(100) DEFAULT NULL,
  `total_units` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `subject_name`, `teacher_name`, `total_units`, `user_id`) VALUES
(2, 'DBMS', 'Deepika Mam', 5, 1),
(3, 'Operating System', 'Deepika Thakur', 7, 1),
(4, 'Web Development using Python', 'Shalani Rai', 6, 1),
(5, 'Advance Java', 'Dipika Mam', 8, 1),
(6, 'Applied Physics', 'Jitendra Sir', 9, 1),
(7, 'Communication Skills ', 'Piyush Sir', 8, 1),
(8, 'Applied Chemistry', 'Neha Mam', 8, 1),
(9, 'Applied Maths', 'Priyanka Mam', 10, 1),
(10, 'Energy Conservation', 'Jitentra Kumar ', 7, 1),
(11, 'DBMS', 'Deepika Mam', 6, 2),
(12, 'IMED', 'Priyanka mam', 8, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `study_plans`
--
ALTER TABLE `study_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `study_plans`
--
ALTER TABLE `study_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
