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
-- Table structure for table `ps_csmegamenu`
--

CREATE TABLE IF NOT EXISTS `ps_csmegamenu` (
  `id_menu` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `number_column` int(10) unsigned DEFAULT '0',
  `width` int(10) unsigned DEFAULT '0',
  `link_of_title` varchar(300) DEFAULT NULL,
  `display` tinyint(1) NOT NULL DEFAULT '1',
  `position` int(10) unsigned DEFAULT '0',
  PRIMARY KEY (`id_menu`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- Dumping data for table `ps_csmegamenu`
--

INSERT INTO `ps_csmegamenu` (`id_menu`, `number_column`, `width`, `link_of_title`, `display`, `position`) VALUES
(3, 0, 0, '{megamenu_url}index.php', 1, 1),
(4, 2, 320, '{megamenu_url}index.php?id_category=3&controller=category', 1, 2),
(5, 3, 480, '{megamenu_url}index.php?controller=manufacturer', 1, 3),
(6, 1, 160, '{megamenu_url}index.php?id_cms_category=1&controller=cms', 1, 4),
(7, 2, 320, '{megamenu_url}index.php?id_category=3&controller=category', 1, 5),
(8, 1, 160, '{megamenu_url}index.php?id_category=3&controller=category', 1, 6),
(9, 2, 320, '{megamenu_url}index.php?id_product=2&controller=product&id_lang=1', 1, 7),
(10, 5, 1180, '{megamenu_url}index.php?id_product=2&controller=product&id_lang=1', 1, 8),
(11, 3, 480, '{megamenu_url}index.php?id_product=2&controller=product&id_lang=1', 1, 9),
(12, 0, 0, '{megamenu_url}index.php?plidc=2&fc=module&module=plblog&controller=listpost', 1, 10);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
