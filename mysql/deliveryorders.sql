-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 11, 2018 at 08:51 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
--
-- Database: `deliveryorders`
--

-- --------------------------------------------------------

--
-- Table structure for table `distance`
--

CREATE TABLE IF NOT EXISTS `distance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_latitude` varchar(25) NOT NULL,
  `start_longitude` varchar(25) NOT NULL,
  `end_latitude` varchar(25) NOT NULL,
  `end_longitude` varchar(25) NOT NULL,
  `distance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `distance`
--

INSERT INTO `distance` (`id`, `start_latitude`, `start_longitude`, `end_latitude`, `end_longitude`, `distance`) VALUES
(1, '28.704060', '77.102493', '28.535517', '77.391029', '46732'),
(2, '28.704060', '77.102493', '29.535517', '77.391029', '128287'),
(3, '28.704060', '77.102493', '22.535510', '77.391044', '912242');

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11)  NOT NULL AUTO_INCREMENT,
  `status` tinyint(4) NOT NULL,
  `distance_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `status`, `distance_id`) VALUES
(1, 1, '1'),
(2, 1, '1'),
(3, 1, '1'),
(4, 1, '1'),
(5, 0, '1'),
(6, 1, '1'),
(7, 1, '1'),
(8, 1, '2'),
(9, 1, '1'),
(10, 1, '2'),
(11, 1, '1'),
(12, 1, '1'),
(13, 0, '1'),
(14, 0, '1'),
(15, 1, '1'),
(16, 1, '1'),
(17, 0, '1'),
(18, 0, '1'),
(19, 1, '1'),
(20, 0, '1'),
(21, 0, '1'),
(22, 0, '1'),
(23, 0, '1'),
(24, 1, '1'),
(25, 0, '3'),
(49, 1, '1'),
(50, 1, '1'),
(51, 1, '1'),
(52, 1, '1'),
(53, 1, '1'),
(54, 0, '1'),
(55, 0, '1'),
(56, 0, '1'),
(57, 0, '1'),
(58, 0, '1'),
(59, 0, '1'),
(60, 1, '3');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
