-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 07:06 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hospital_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` enum('scheduled','completed','cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `doctor_id`, `patient_id`, `date`, `time`, `status`) VALUES
(1, 1, 1, '2025-01-11', '04:04:00', 'scheduled'),
(4, 1, 2, '2025-01-31', '20:30:00', 'scheduled'),
(5, 1, 3, '2025-01-31', '20:48:00', 'completed'),
(6, 1, 10, '2025-05-07', '20:49:00', 'scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests`
--

CREATE TABLE `lab_tests` (
  `test_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `test_name` varchar(100) NOT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `completion_date` datetime DEFAULT NULL,
  `result_value` varchar(255) DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `test_type` varchar(100) NOT NULL,
  `priority` varchar(100) NOT NULL,
  `notes` varchar(1000) NOT NULL,
  `normal_range` int(100) NOT NULL,
  `diagnosis` varchar(500) NOT NULL,
  `treatment_plan` varchar(500) NOT NULL,
  `doctor_notes` varchar(500) NOT NULL,
  `diagnosis_date` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lab_tests`
--

INSERT INTO `lab_tests` (`test_id`, `patient_id`, `test_name`, `request_date`, `completion_date`, `result_value`, `status`, `test_type`, `priority`, `notes`, `normal_range`, `diagnosis`, `treatment_plan`, `doctor_notes`, `diagnosis_date`) VALUES
(2, 2, 'فحص بول', '2025-01-30 22:39:45', '2025-01-30 23:17:54', 'الالال', '', 'urine', 'normal', '', 0, 'ممممممممممم', 'ممممممممممممم', 'ممممممممممم', '2025-01-31 00:30:14'),
(3, 1, 'فحص دم', '2025-01-30 22:41:54', '2025-01-30 22:57:58', 'jjjj', '', 'blood', 'normal', '', 90, '', '', '', ''),
(4, 10, 'فحص دم', '2025-01-30 23:02:04', NULL, NULL, 'pending', 'blood', 'normal', '', 0, '', '', '', ''),
(5, 2, 'أشعة', '2025-01-31 00:51:14', NULL, NULL, 'pending', 'xray', 'high', '', 0, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `lab_tests_catalog`
--

CREATE TABLE `lab_tests_catalog` (
  `test_id` int(11) NOT NULL,
  `test_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lab_tests_catalog`
--

INSERT INTO `lab_tests_catalog` (`test_id`, `test_name`) VALUES
(1, 'Blood Test'),
(2, 'X-Ray'),
(3, 'MRI'),
(4, 'CT Scan'),
(5, 'Urine Test');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `unique_id` varchar(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `name`, `date_of_birth`, `gender`, `contact_info`, `medical_history`, `unique_id`) VALUES
(1, 'نورا معاوية', '2025-01-09', 'female', '090705504', '', '8932684'),
(2, 'مناهل', '2025-01-29', 'female', '0912345678', '', '9802530'),
(3, 'نن', '2025-01-29', 'male', '0912345678', '', '2086011'),
(10, 'يتيتيت', '2025-01-30', 'male', '0912345678', '', '2434567');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('receptionist','doctor','lab_technician') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'walieldeen', 'hassenboss149@gmail.com', '$2y$10$TgBqNKFHgRYRYKGIR8bFi.vL56KJsAXDp/CG88uVAytek1Vfc2WKS', 'doctor'),
(3, 'dd', '374c7aa0ae@emailfoxi.pro', '$2y$10$muGLOxMwo/ApK2Qqlsoq3uN78C8W5JLyTjF3thuNC33Pf7urJVEJW', 'doctor'),
(4, 'wali', 'lemini4367@kazvi.com', '$2y$10$QHTeCD.sxXtL.C48oRuwa.H3H7554VQb0UMx/n3UFDEUafQspbT.a', 'lab_technician'),
(5, 'wali', 'nomhus@thnen.com', '$2y$10$3q3E3Hwrf5avhDdIP8Q63eKoKNvPfHQjYxeVMRJ1KI1u74UCsYHni', 'receptionist'),
(6, 'INT', 'iceicebaby656@yahoo.com', '$2y$10$A6N2EFtt.zUS5eLOC4v6C.7AbiqP7w3DpjroJ6ZP2t4UzhM/sL1My', 'lab_technician'),
(9, 'Worlds of Education', 'aishaalsadig10@gmail.com', '$2y$10$P4dy.0PmRgx4Vd1KcLXOJOkA0sUajNM5Gg3OrolrRP6eizdPmNAl.', 'doctor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD PRIMARY KEY (`test_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `lab_tests_catalog`
--
ALTER TABLE `lab_tests_catalog`
  ADD PRIMARY KEY (`test_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `unique_id` (`unique_id`);

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
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `lab_tests`
--
ALTER TABLE `lab_tests`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lab_tests_catalog`
--
ALTER TABLE `lab_tests_catalog`
  MODIFY `test_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `lab_tests`
--
ALTER TABLE `lab_tests`
  ADD CONSTRAINT `lab_tests_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
