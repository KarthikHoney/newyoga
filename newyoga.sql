-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 14, 2024 at 11:36 AM
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
-- Database: `newyoga`
--

-- --------------------------------------------------------

--
-- Table structure for table `grade`
--

CREATE TABLE `grade` (
  `id` int(255) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp(),
  `student_id` int(255) NOT NULL,
  `trainer_id` int(255) NOT NULL,
  `admin_id` varchar(255) NOT NULL,
  `grade` int(255) NOT NULL,
  `payment` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grade`
--

INSERT INTO `grade` (`id`, `date`, `student_id`, `trainer_id`, `admin_id`, `grade`, `payment`) VALUES
(1, '2024-10-14', 1, 0, 'admin', 12, 1200),
(2, '2024-10-14', 1, 0, '', 7, 700),
(3, '2024-10-14', 1, 0, '', 6, 600),
(4, '2024-10-14', 15, 1, '', 7, 700),
(5, '2024-10-14', 2, 0, '', 10, 1000),
(6, '2024-10-14', 3, 0, '', 11, 1100),
(7, '2024-10-14', 3, 1, '', 3, 300),
(8, '2024-10-14', 3, 0, '', 12, 1200);

-- --------------------------------------------------------

--
-- Table structure for table `individual_student`
--

CREATE TABLE `individual_student` (
  `id` int(255) NOT NULL,
  `registeredBy` varchar(255) NOT NULL,
  `trainer_id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `parentname` varchar(255) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `wnumber` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `date` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `individual_student`
--

INSERT INTO `individual_student` (`id`, `registeredBy`, `trainer_id`, `name`, `parentname`, `gmail`, `dob`, `password`, `wnumber`, `number`, `address`, `date`) VALUES
(1, 'admin', 0, 'Prasath', 'siva', 'prasath@gmail.com', '2024-10-15', 'prasath', '9876542310', '8796543210', 'Muthialpet', '2024-10-14'),
(2, 'Harish', 1, 'karthi', 'ravi', 'kar@gmail.com', '2024-10-14', 'karthi', '8975462310', '8794651320', 'Villipuram', '2024-10-14'),
(3, 'Harish', 1, 'Guru', 'test', 'guruGmail.com', '2024-10-18', 'wwww', '7845122145', '7845125487', 'yrtyrtyrtyrt', '2024-10-14');

-- --------------------------------------------------------

--
-- Table structure for table `trainer`
--

CREATE TABLE `trainer` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `studio` varchar(255) NOT NULL,
  `registeredBy` varchar(255) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `number` varchar(255) NOT NULL,
  `wnumber` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer`
--

INSERT INTO `trainer` (`id`, `name`, `studio`, `registeredBy`, `gmail`, `password`, `number`, `wnumber`, `address`) VALUES
(1, 'Harish', 'hari studio', '', 'hari@gmail.com', 'hari', '8795462310', '8974563210', 'Muthialpet');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `individual_student`
--
ALTER TABLE `individual_student`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trainer`
--
ALTER TABLE `trainer`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grade`
--
ALTER TABLE `grade`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `individual_student`
--
ALTER TABLE `individual_student`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `trainer`
--
ALTER TABLE `trainer`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
