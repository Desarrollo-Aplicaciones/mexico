<?php
/*********************************************************************************************************
 * Copyright(c) @2010 - 2013 ANTERP SOLUTIONS. All rights reserved.
 * Website    			: 	www.cartbooks.com
 * Address        		: 	J119, Jalan Perkasa 8,
 * 						  	Taman Salak Selatan,
 *	 					  	57100 Kuala Lumpur
 * 						  	Malaysia.
 *
 * This IS NOT FREE software.
 *
 * Licensed Software    :   Subject to the terms and conditions of this Agreement, ANTERP SOLUTIONS and its suppliers
 * 							grant to Customer ("Customer") a nonexclusive, non-transferable license, without the right
 * 							to sublicense, to use the Licensed Software, in object-code form only, solely for Customer's
 * 							internal business, research, or educational purposes. Customer may install up to one (1) copy
 * 							of the Licensed Software provided that only one (1) copy is in use at any given time
 *
 * Additional License	:	This software is the Intellectual Property of ANTERP SOLUTIONS.
 * 							No part of this software can be distributed without authorization from
 *	 						ANTERP SOLUTIONS.
 *
 *Description	 		:	Data Sync Suites developed and distributed by ANTERP SOLUTIONS.
 *
 * Authors				:	tclim
 * Date Created  		:	June 13, 2012 9:50:04 PM
 ********************************************************************************************************/

//Default Timezone
define('DEFAULT_TIMEZONE', 'America/Mexico_City');

//Default Timezone
if (!ini_get('date.timezone'))
     ini_set('date.timezone', DEFAULT_TIMEZONE);

//Logfile
define('ACCT_LOG_FILE', 'prestashop_accounting' . '_' . date('Y-m-d') . '.log');
define('SUGAR_LOG_FILE', 'prestashop_sugar' . '_' . date('Y-m-d') . '.log');

//Your Store ID
define('STORE_ID', "1"); //Default to main store

//PrestaShop Root Directory Name
define('STORE_WEB_ROOT', '');
//PrestaShop Directory
define('STORE_PATH', '/var/www/' . STORE_WEB_ROOT . '/');
//Image Temp
define('STORE_IMAGE', 'img/tmp');
//Default PORT
define('DEFAULT_PORT', '80');
$_SERVER["SERVER_NAME"] = "farmalisto.com.co";
?>