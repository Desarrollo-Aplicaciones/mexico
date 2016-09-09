-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 04, 2013 at 09:27 AM
-- Server version: 5.5.27
-- PHP Version: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `prestashop_quality`
--

-- --------------------------------------------------------

--
-- Table structure for table `ps_csmegamenu_lang`
--

CREATE TABLE IF NOT EXISTS `ps_csmegamenu_lang` (
  `id_menu` int(10) unsigned NOT NULL,
  `id_lang` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `csmegamenu_lang_index` (`id_menu`,`id_lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ps_csmegamenu_lang`
--

INSERT INTO `ps_csmegamenu_lang` (`id_menu`, `id_lang`, `title`) VALUES
(3, 1, 'Homepage'),
(3, 2, 'Homepage'),
(3, 3, 'Homepage'),
(3, 4, 'Homepage'),
(3, 5, 'Page d''accueil'),
(3, 6, 'Homepage'),
(4, 1, 'Ipod'),
(4, 2, 'Ipod'),
(4, 3, 'Ipod'),
(4, 4, 'Ipod'),
(4, 5, 'Ipod'),
(4, 6, 'Ipod'),
(5, 1, 'Manufacture'),
(5, 2, 'Manufacture'),
(5, 3, 'Manufacture'),
(5, 4, 'Manufacture'),
(5, 5, 'Manufacture'),
(5, 6, 'Manufacture'),
(6, 1, 'Information'),
(6, 2, 'Information'),
(6, 3, 'Information'),
(6, 4, 'Information'),
(6, 5, 'Information'),
(6, 6, 'Information'),
(7, 1, 'Category 2'),
(7, 2, 'Category 2'),
(7, 3, 'Category 2'),
(7, 4, 'Category 2'),
(7, 5, 'Category 2'),
(7, 6, 'Category 2'),
(8, 1, 'category 3'),
(8, 2, 'category 3'),
(8, 3, 'category 3'),
(8, 4, 'category 3'),
(8, 5, 'category 3'),
(8, 6, 'category 3'),
(9, 1, 'Product Together'),
(9, 2, 'Product Together'),
(9, 3, 'Product Together'),
(9, 4, 'Product Together'),
(9, 5, 'Product Together'),
(9, 6, 'Product Together'),
(10, 1, 'Combination'),
(10, 2, 'Combination'),
(10, 3, 'Combination'),
(10, 4, 'Combination'),
(10, 5, 'Combination'),
(10, 6, 'Combination'),
(11, 1, 'Static block'),
(11, 2, 'Static block'),
(11, 3, 'Static block'),
(11, 4, 'Static block'),
(11, 5, 'Static block'),
(11, 6, 'Static block'),
(12, 1, 'Blog'),
(12, 2, 'Blog'),
(12, 3, 'Blog'),
(12, 4, 'Blog'),
(12, 5, 'Blog'),
(12, 6, 'Blog');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
