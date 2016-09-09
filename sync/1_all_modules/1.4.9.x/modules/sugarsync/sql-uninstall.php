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

	// Init
	$sql = array();
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'sync_result`;';	
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'sync_config`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'sync_tracker`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'sync_lookup`;';
	$sql[] = 'DROP TABLE IF EXISTS `'._DB_PREFIX_.'sync_role`;';
	$sql[] = "ALTER TABLE `"._DB_PREFIX_."currency` DROP COLUMN `date_upd`;";

?>