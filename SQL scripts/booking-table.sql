-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 19, 2021 at 11:15 PM
-- Server version: 8.0.18
-- PHP Version: 7.4.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bnb`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `bookingID` int(10) NOT NULL,
  `roomID` int(10) UNSIGNED NOT NULL,
  `customerID` int(10) UNSIGNED NOT NULL,
  `roomname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `roomtype` char(1) NOT NULL,
  `beds` int(11) NOT NULL,
  `checkindate` varchar(50) NOT NULL,
  `checkoutdate` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `contactno` varchar(50) DEFAULT NULL,
  `extras` varchar(300) DEFAULT NULL,
  `reviews` varchar(500) DEFAULT NULL  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`bookingID`, `roomID`, `customerID`, `roomname`, `roomtype`, `beds`,`checkindate`, `checkoutdate`, `firstname`, `lastname`, `contactno`, `extras`, `reviews`) VALUES
(1, 1, 3, 'Kellie','S', 5, '2021-01-21', '2021-01-23', 'Irene', 'Walker', '(001) 123-3456', 'nothing', 'good'),
(2, 1, 1, 'Kellie',  'S', 5,'2021-01-20', '2021-01-23', 'Garrison', 'Jordan', '(123) 123-5645', '', 'nothing'),
(10, 3, 4, 'Scarlett', 'D', 2, '2021-01-29', '2021-02-02', 'Forrest', 'Baldwin', '(001) 123-7890', 'extra bed', NULL),
(11, 12, 11, 'Preston',  'D', 2,'2021-01-21', '2021-01-27', 'Castor', 'Kelly', '(001) 123-4321', 'sea view', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`bookingID`),
  ADD KEY `roomID` (`roomID`),
  ADD KEY `customerID` (`customerID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `bookingID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `customerID` FOREIGN KEY (`customerID`) REFERENCES `customer` (`customerID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `roomID` FOREIGN KEY (`roomID`) REFERENCES `room` (`roomID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
