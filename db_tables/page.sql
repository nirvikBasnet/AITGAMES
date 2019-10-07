-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 29, 2019 at 05:18 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `data2`
--

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE `page` (
  `page_id` int(11) NOT NULL COMMENT 'page id',
  `name` varchar(128) NOT NULL,
  `url` varchar(128) NOT NULL,
  `menu` varchar(64) NOT NULL,
<<<<<<< HEAD
  `menu_order` int(2) NOT NULL DEFAULT '0',
  `level` int(1) NOT NULL DEFAULT '0',
  `content` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1'
=======
  `menu_order` int(2) NOT NULL DEFAULT 0,
  `level` int(1) NOT NULL DEFAULT 0,
  `content` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
>>>>>>> fd2b4cec4ad976e60e00e3f38b9c827b8863d66f
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`page_id`, `name`, `url`, `menu`, `menu_order`, `level`, `content`, `active`) VALUES
(1, 'Welcome to the shop', 'index.php', 'Home', 1, 1, 'Welcome to our shop. Buy stuff here.', 1),
(2, 'Log in to your account', 'login.php', 'Login', 2, 0, 'Log in to your account. If you don\'t have an account you can register <a href=\"/register\">Here</a>', 1),
(3, 'Register for a free account', 'register.php', 'Register', 3, 0, 'Register for a free account. Already have an account? Log in <a href=\"/login\">here</a>', 1),
(4, 'Your Account Details', 'account.php', 'Account', 4, 2, 'Your account details.', 1),
(5, 'Log out of your account', 'logout.php', 'Logout', 6, 2, 'Sign out of your account', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`page_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `page`
  MODIFY `page_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'page id', AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
