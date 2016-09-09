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
-- Table structure for table `ps_csmegamenu_option`
--

CREATE TABLE IF NOT EXISTS `ps_csmegamenu_option` (
  `id_option` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_menu` int(10) unsigned NOT NULL,
  `type_option` int(10) unsigned NOT NULL,
  `position_option` int(10) unsigned DEFAULT '0',
  `content_option` text,
  PRIMARY KEY (`id_option`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `ps_csmegamenu_option`
--

INSERT INTO `ps_csmegamenu_option` (`id_option`, `id_menu`, `type_option`, `position_option`, `content_option`) VALUES
(5, 4, 0, 1, '{"opt_fill_column":"2","opt_show_image_cat":"1","opt_image_size_cate":"small_default","opt_show_sub_cat":"0","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}'),
(6, 5, 3, 8, '{"opt_show_image_manu":"1","opt_image_size_manu":"small_default","opt_show_name_manu":"1","opt_list_manu":["1","3","2"],"opt_fill_column":"3"}'),
(7, 6, 4, 7, '{"footerBox":["0_1","0_2","0_3","0_4","0_5","0_6"],"opt_fill_column":"1"}'),
(8, 7, 0, 6, '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"small_default","opt_show_sub_cat":"1","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}'),
(9, 7, 0, 9, '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"small_default","opt_show_sub_cat":"1","opt_show_parent_cat":"1","opt_id_parent_cat":"4"}'),
(10, 7, 0, 12, '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"small_default","opt_show_sub_cat":"1","opt_show_parent_cat":"1","opt_id_parent_cat":"5"}'),
(11, 7, 0, 13, '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"small_default","opt_show_sub_cat":"1","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}'),
(12, 9, 1, 3, '{"opt_show_image_product":"1","opt_image_size_product":"small_default","input_hidden_id":"2-1-5-3-","input_hidden_name":"iPod shuffle\\u00a4iPod Nano\\u00a4iPod touch\\u00a4MacBook Air\\u00a4","opt_fill_column":"2"}'),
(13, 11, 2, 2, '{&quot;opt_content_static&quot;:{&quot;1&quot;:&quot;&lt;h3&gt;&lt;img src=\\&quot;{megamenu_url}img\\/cms\\/21.png\\&quot; alt=\\&quot;\\&quot; width=\\&quot;203\\&quot; height=\\&quot;135\\&quot; \\/&gt;Suspendisse ac pulvinar nulla&lt;\\/h3&gt;\\r\\n&lt;p style=\\&quot;padding-right: 10px;\\&quot;&gt;Praesent tempor turpis cursus tortor vulputate lemen egestas diam iaculis Aliquam erat volutpat Eu nulla.&lt;\\/p&gt;\\r\\n&lt;h3&gt;Class aptent taciti sociosquis&lt;\\/h3&gt;\\r\\n&lt;p style=\\&quot;padding-right: 10px;\\&quot;&gt;Lorem ipsum dolor sit amet consectetur adipiscing elit Nullam hendrerit nisl nec lorem gravida at tempor.&lt;\\/p&gt;\\r\\n&lt;h3&gt;Fusce placerat risus faucibus&lt;\\/h3&gt;\\r\\n&lt;p style=\\&quot;padding-right: 10px;\\&quot;&gt;Cum sociis natoque penatibus et magnis montes nascetur ridiculus mus Curabitur hendrerit nisl nec.&lt;\\/p&gt;&quot;,&quot;2&quot;:&quot;&quot;,&quot;3&quot;:&quot;&quot;,&quot;4&quot;:&quot;&quot;,&quot;5&quot;:&quot;&lt;p&gt;&lt;img src=\\&quot;{megamenu_url}img\\/cms\\/2013-02-19_094557.png\\&quot; alt=\\&quot;\\&quot; width=\\&quot;1326\\&quot; height=\\&quot;190\\&quot; \\/&gt;&lt;\\/p&gt;&quot;,&quot;6&quot;:&quot;&quot;},&quot;opt_fill_column&quot;:&quot;3&quot;}'),
(14, 8, 0, 5, '{"opt_fill_column":"1","opt_show_image_cat":"0","opt_image_size_cate":"small_default","opt_show_sub_cat":"1","opt_show_parent_cat":"0","opt_id_parent_cat":"3"}'),
(15, 10, 2, 4, '{&quot;opt_content_static&quot;:{&quot;1&quot;:&quot;&lt;h3&gt;Sample Static Block&lt;\\/h3&gt;\\r\\n&lt;p style=\\&quot;padding-right: 10px;\\&quot;&gt;Praesent tempor turpis cursus tortor vulputate lemen egestas diam iaculis Aliquam erat volutpat Eu nulla.&lt;\\/p&gt;&quot;,&quot;2&quot;:&quot;&quot;,&quot;3&quot;:&quot;&quot;,&quot;4&quot;:&quot;&quot;,&quot;5&quot;:&quot;&lt;h3&gt;Sample Static Block&lt;\\/h3&gt;\\r\\n&lt;p style=\\&quot;padding-right: 10px;\\&quot;&gt;Praesent tempor turpis cursus tortor vulputate lemen egestas diam iaculis Aliquam erat volutpat Eu nulla.&lt;\\/p&gt;&quot;,&quot;6&quot;:&quot;&quot;},&quot;opt_fill_column&quot;:&quot;1&quot;}'),
(16, 10, 1, 10, '{"opt_show_image_product":"1","opt_image_size_product":"small_default","input_hidden_id":"2-2-3-6-","input_hidden_name":"iPod shuffle\\u00a4iPod shuffle\\u00a4MacBook Air\\u00a4Belkin Leather Folio for iPod nano - Black \\/ Chocolate\\u00a4","opt_fill_column":"2"}'),
(17, 10, 0, 11, '{"opt_fill_column":"2","opt_show_image_cat":"0","opt_image_size_cate":"small_default","opt_show_sub_cat":"1","opt_show_parent_cat":"1","opt_id_parent_cat":"3"}');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
