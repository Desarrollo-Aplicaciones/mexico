<?php

/*
  @copyright  2007-2011 PrestaShop SA
  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/***************************************************************************************************
* Copyright(c) @2011 ANTERP SOLUTIONS. All rights reserved.
* Website				http://www.cartbooks.com
* Authors		    	tclim
* Date Created     		May 26, 2012 4:38:48 PM
* 
* Additional License	This software require you to buy from ANTERP SOLUTIONS. 
* 						You have no right to redistribute this program.
* 
* Description			Data Sync Suites developed and distributed by ANTERP SOLUTIONS.
*  
 **************************************************************************************************/

	// Create databases
	$sql = array();

	// Create Category Table in Database
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sync_result` (
			  `tid` int(12) unsigned NOT NULL AUTO_INCREMENT,
			  `user_id` varchar(150) NOT NULL,
			  `sync_action_cd` varchar(10) NOT NULL,
			  `sync_module_cd` varchar(30) NOT NULL,
			  `sync_option_cd` varchar(30) NOT NULL,
			  `source` varchar(50) NOT NULL,
			  `destination` varchar(50) NOT NULL,
			  `first_sync` datetime DEFAULT NULL,
			  `last_sync` datetime DEFAULT NULL,
			  `status_cd` char(5) DEFAULT NULL,
			  PRIMARY KEY (`tid`),
			  UNIQUE KEY `UK_sync_result#module` (`sync_module_cd`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';


	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sync_config` (
		  `tid` int(12) unsigned NOT NULL AUTO_INCREMENT,
		  `sync_module_cd` varchar(30) NOT NULL,
		  `sync_option_cd` varchar(30) NOT NULL,
		  `source` varchar(50) NOT NULL,
		  `destination` varchar(50) NOT NULL,
		  `sync_status` varchar(100) DEFAULT NULL,
		  `is_active` tinyint(1) DEFAULT \'0\',
		  `is_system` tinyint(1) NOT NULL DEFAULT \'0\',
		  `created_by` varchar(50) DEFAULT NULL,
		  `dt_created` datetime DEFAULT NULL,
		  `updated_by` varchar(50) DEFAULT NULL,
		  `dt_updated` datetime DEFAULT NULL,
		  PRIMARY KEY (`tid`),
		  UNIQUE KEY `UK_sync_config#module#option#source#destination` (`sync_module_cd`,`sync_option_cd`, `source`,`destination`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sync_tracker` (
		  `tid` int(12) unsigned NOT NULL AUTO_INCREMENT,
		  `user_id` varchar(150) NOT NULL,
		  `sync_module_cd` varchar(30) NOT NULL,
		  `key1` varchar(36) NOT NULL,
		  `value1` varchar(100) DEFAULT NULL,
		  `key2` varchar(36) NOT NULL,
		  `value2` varchar(100) DEFAULT NULL,
		  `modifiedtime` datetime DEFAULT NULL,
		  PRIMARY KEY (`tid`),
		  KEY `UK_sync_tracker#module#key1` (`sync_module_cd`,`key1`),
		  KEY `UK_sync_tracker#module#key2` (`sync_module_cd`,`key2`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sync_lookup` (
		  `tid` int(12) unsigned NOT NULL AUTO_INCREMENT,
		  `store_id` int(11) NOT NULL DEFAULT \'0\',
		  `sync_group_name` varchar(150) NOT NULL,
		  `name` varchar(150) NOT NULL,
		  `ref_key` varchar(45) NOT NULL,
		  `is_system` tinyint(1) NOT NULL DEFAULT \'0\',
		  `created_by` varchar(150) NOT NULL,
		  `dt_created` datetime NOT NULL,
		  `updated_by` varchar(150) NOT NULL,
		  `dt_updated` datetime NOT NULL,
		  `is_active` tinyint(1) NOT NULL,
		  PRIMARY KEY (`tid`),
		  UNIQUE KEY `UK_sync_lookup#store_id#sync_group_name#name` (`store_id`,`sync_group_name`,`name`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

	
	$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sync_role` (  
      `tid` int(12) unsigned NOT NULL AUTO_INCREMENT,      
      `store_id` int(11) NOT NULL DEFAULT \'0\',      
      `sync_group_name` varchar(150)  NOT NULL,      
      `id` varchar(150)  NOT NULL,      
      `name` varchar(150)  NOT NULL,      
      `ref_key` varchar(45)  NOT NULL,      
      `is_system` tinyint(1) NOT NULL DEFAULT \'0\',      
      `created_by` varchar(150)  NOT NULL,      
      `dt_created` datetime NOT NULL,      
      `updated_by` varchar(150)  NOT NULL,      
      `dt_updated` datetime NOT NULL,      
      `is_active` tinyint(1) NOT NULL,      
      PRIMARY KEY (`tid`),      
      UNIQUE KEY `UK_sync_role#store_id#sync_group_name#name` (`store_id`,`sync_group_name`,`name`)      
      ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';

$sql[] = "DELETE FROM `"._DB_PREFIX_."sync_config` WHERE destination = 'sugarcrm';";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_config` (`sync_module_cd`, `sync_option_cd`, `source`, `destination`, `sync_status`, `is_active`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`) VALUES ('PS_PRODUCT_CATEGORY', '1WAY', 'shoppingcart', 'sugarcrm', null, 1, 0, 'system', now(), 'system', now());";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_config` (`sync_module_cd`, `sync_option_cd`, `source`, `destination`, `sync_status`, `is_active`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`) VALUES ('PS_CURRENCY', '1WAY', 'shoppingcart', 'sugarcrm', null, 1, 0, 'system', now(), 'system', now());";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_config` (`sync_module_cd`, `sync_option_cd`, `source`, `destination`, `sync_status`, `is_active`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`) VALUES ('PS_ITEM', '1WAY', 'shoppingcart', 'sugarcrm', null, 1, 0, 'system', now(), 'system', now());";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_config` (`sync_module_cd`, `sync_option_cd`, `source`, `destination`, `sync_status`, `is_active`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`) VALUES ('PS_CUSTOMER', '1WAY', 'shoppingcart', 'sugarcrm', null, 1, 0, 'system', now(), 'system', now());";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_config` (`sync_module_cd`, `sync_option_cd`, `source`, `destination`, `sync_status`, `is_active`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`) VALUES ('PS_SALESINVOICE', '1WAY', 'shoppingcart', 'sugarcrm', null, 1, 0, 'system', now(), 'system', now());";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_config` (`sync_module_cd`, `sync_option_cd`, `source`, `destination`, `sync_status`, `is_active`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`) VALUES ('PS_VOIDINVOICE', '1WAY', 'shoppingcart', 'sugarcrm', null, 1, 0, 'system', now(), 'system', now());";

$sql[] = "DELETE FROM `"._DB_PREFIX_."sync_lookup` WHERE sync_group_name = 'cartversioning';";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.4.x', '1.4.4', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.5.x', '1.4.5', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.6.x', '1.4.6', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.7.x', '1.4.7', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.8.x', '1.4.8', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.9.x', '1.4.9', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.10.x', '1.4.10', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.4.11.x', '1.4.11', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.5.0.x', '1.5.0', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.5.1.x', '1.5.1', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.5.2.x', '1.5.2', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.5.3.x', '1.5.3', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.5.4.x', '1.5.4', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'cartversioning', '1.5.5.x', '1.5.5', 0, 'admin', now(), 'admin', now(), 1);";


$sql[] = "DELETE FROM `"._DB_PREFIX_."sync_lookup` WHERE sync_group_name = 'server';";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'server', 'Shared Hosting', 'SH', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'server', 'VPS / Cloud / Dedicated Server', 'VPS', 0, 'admin', now(), 'admin', now(), 1);";
		
$sql[] = "DELETE FROM `"._DB_PREFIX_."sync_lookup` WHERE sync_group_name = 'memory_limit';";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'memory_limit', '64MB - Shared Hosting', '64', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'memory_limit', '128MB', '128', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'memory_limit', '256MB', '256', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'memory_limit', '512MB', '512', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'memory_limit', '> 1024MB', '1024', 0, 'admin', now(), 'admin', now(), 1);";

$sql[] = "DELETE FROM `"._DB_PREFIX_."sync_lookup` WHERE sync_group_name = 'item_code_mapping';";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'item_code_mapping', 'Product ID', 'id_product', 0, 'admin', now(), 'admin', now(), 1);";
//$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'item_code_mapping', 'UPC', 'upc', 0, 'admin', now(), 'admin', now(), 1);";
//$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'item_code_mapping', 'EAN13', 'ean13', 0, 'admin', now(), 'admin', now(), 1);";

$sql[] = "DELETE FROM `"._DB_PREFIX_."sync_lookup` WHERE sync_group_name = 'part_number_mapping';";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'part_number_mapping', 'Product ID', 'id_product', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'part_number_mapping', 'Reference', 'reference', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'part_number_mapping', 'Supplier Reference', 'supplier_reference', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'part_number_mapping', 'UPC', 'upc', 0, 'admin', now(), 'admin', now(), 1);";
$sql[] = "INSERT INTO `"._DB_PREFIX_."sync_lookup` (`store_id`, `sync_group_name`, `name`, `ref_key`, `is_system`, `created_by`, `dt_created`, `updated_by`, `dt_updated`, `is_active`) VALUES (1, 'part_number_mapping', 'EAN13', 'ean13', 0, 'admin', now(), 'admin', now(), 1);";


$sql[] = "ALTER TABLE `"._DB_PREFIX_."currency` ADD COLUMN `date_upd` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP;";
$sql[] = "UPDATE `"._DB_PREFIX_."currency` set date_upd = now();";
?>