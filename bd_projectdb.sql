-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2026 at 04:15 AM
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
-- Database: `bd_projectdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_tbl`
--

CREATE TABLE `customer_tbl` (
  `customerid` varchar(8) NOT NULL,
  `customerEmail` varchar(100) NOT NULL,
  `customerName` varchar(250) NOT NULL,
  `customerPhone` varchar(12) NOT NULL,
  `customerNIC` varchar(12) NOT NULL,
  `customerGender` varchar(6) NOT NULL,
  `customerBirthday` date NOT NULL,
  `d_status` int(11) NOT NULL,
  `c_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_tbl`
--

INSERT INTO `customer_tbl` (`customerid`, `customerEmail`, `customerName`, `customerPhone`, `customerNIC`, `customerGender`, `customerBirthday`, `d_status`, `c_date`) VALUES
('CUS00001', 'udara@gmail.com', 'Udar7', '0766471001', '200351900123', 'Female', '2025-06-18', 0, '2026-06-16 03:07:03'),
('CUS00002', 'Isuru@gmail.com', 'Isuru', '0766467644', '768900654333', 'Male', '2022-06-20', 0, '2026-02-01 07:44:07'),
('CUS00003', 'malaya@gmail.com', 'Malay', '0766467644', '768900654333', 'Male', '2022-06-20', 0, '2026-02-01 07:44:07'),
('CUS00004', 'dewmi@gmail.com', 'Dewmi', '', '', '', '0000-00-00', 0, '2026-02-01 07:44:07'),
('CUS00005', 'dew@gmail.com', 'dew', '0765645342', '200567895678', 'Male', '2005-10-18', 0, '2026-02-01 08:25:52'),
('CUS00006', 'Yasiru@gmail.com', 'Yasiru', '0768975678', '200451789000', 'Male', '2004-05-20', 0, '2026-02-06 01:43:59'),
('CUS00007', 'Rowinya@gmal.com', 'Rowinya', '0764532123', '2003517896', 'Female', '2004-07-15', 0, '2026-06-20 07:11:04'),
('CUS00008', 'dilsharisadeepa2003@gmail.com', 'Sadeepa', '0766479767', '200351900347', 'Female', '2003-01-19', 0, '2026-06-27 08:34:30'),
('CUS00009', 'Akila@gmail.com', 'Akila', '0765643219', '20024567890', 'Male', '2022-10-18', 1, '2026-06-21 02:29:16'),
('CUS00010', 'sadeepa@gmail.com', 'Sadeepa Dilshari', '0764457839', '200351900347', 'Female', '2003-01-19', 0, '2026-06-27 07:15:10');

-- --------------------------------------------------------

--
-- Table structure for table `login_tbl`
--

CREATE TABLE `login_tbl` (
  `loginid` varchar(11) NOT NULL,
  `loginEmail` varchar(200) NOT NULL,
  `loginPassword` varchar(200) NOT NULL,
  `loginRole` varchar(30) NOT NULL,
  `loginStatus` int(1) NOT NULL,
  `d_Status` int(1) NOT NULL,
  `c_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_tbl`
--

INSERT INTO `login_tbl` (`loginid`, `loginEmail`, `loginPassword`, `loginRole`, `loginStatus`, `d_Status`, `c_date`) VALUES
('CUS00006', 'Yasiru@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'customer', 1, 0, '2026-06-21 18:04:07'),
('CUS00007', 'Rowinya@gmal.com', '827ccb0eea8a706c4c34a16891f84e7b', 'customer', 1, 0, '2026-06-22 02:22:08'),
('CUS00008', 'dilsharisadeepa2003@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'admin', 1, 0, '2026-06-27 08:35:20'),
('CUS00009', 'esd', '234567', '0', 1, 1, '2026-06-20 06:37:50'),
('CUS00010', 'sadeepa@gmail.com', '123456', 'customer', 1, 0, '2026-06-27 07:15:10');

-- --------------------------------------------------------

--
-- Table structure for table `product_tbl`
--

CREATE TABLE `product_tbl` (
  `productid` varchar(10) NOT NULL,
  `productName` varchar(200) NOT NULL,
  `productDetails` varchar(300) NOT NULL,
  `category` varchar(20) NOT NULL,
  `image` varchar(500) NOT NULL,
  `supplier` varchar(20) NOT NULL,
  `d_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer_tbl`
--
ALTER TABLE `customer_tbl`
  ADD PRIMARY KEY (`customerid`),
  ADD UNIQUE KEY `customerEmail` (`customerEmail`);

--
-- Indexes for table `login_tbl`
--
ALTER TABLE `login_tbl`
  ADD PRIMARY KEY (`loginid`);

--
-- Indexes for table `product_tbl`
--
ALTER TABLE `product_tbl`
  ADD PRIMARY KEY (`productid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
