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
 * Date Created  		:	Oct 31, 2012 11:12:04 AM
 ********************************************************************************************************/

	// Default Configuration
	require_once('config/config_default.php');
	
	if (file_exists('install/loader-wizard.php')) {
		header("Location: install/loader-wizard.php");
		exit;
	}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head >
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>CartBooks.com | Free Online Accounting Software That Seamlessly Integrated With Online Shopping Cart</title>
<meta name="description" content="CartBooks provide free accounting for small business that seamlessly integrated with online shopping cart system."/>
<meta name="keywords" content="cartboooks, cartbooks accounting, free accounting, free bookkeeping software, free accounting software, autocount, free shopping cart accounting, ecommerce accounting, ecommerce accounting software, ecommerce accounting integration, ecommerce integration accounting software, crm accounting software, shopping cart accounting software" />
<meta name="robots" content="index, follow">
<meta name="author" content="tclim">
<meta name="copyright" content="cartbooks">
<style type="text/css">
html { 
	background: url(images/bg-cartbooks.png) no-repeat center center fixed; 
	-webkit-background-size: cover;
	-moz-background-size: cover;
	-o-background-size: cover;
	background-size: cover;
}
#footer_text {
height: 60px;
padding-top: 20px;
padding-right: 20px;
border-top: 2px solid #EDEEEF;
text-align: right;
color: #555555;
font-size: 0.8em;
clear: both;
}
#footer_text a:link, #footer_text a:visited {
color: #555555;
text-decoration: none;
}
#footer_text a:hover, #footer_text a:active {
color: #0e63b8;
text-decoration: underline;
}
</style>
</head>
<body>
<h1>CartBooks Accounting Software For Small Business</h1>
<div><a href="http://www.cartbooks.com" title="CartBooks Accounting Software"><img src="images/cartbooks.png" alt="CartBooks Accounting Software" /></a> A Free Online Accounting Software That Seamlessly Integrated With Online Shopping Cart</div>
<div class="clear">&nbsp;</div>
<div class="clear">&nbsp;</div>
<div>Visit us at <a href="http://www.cartbooks.com/" title="Visit us at cartbooks.com">www.cartbooks.com</a></div>
<div class="clear">&nbsp;</div>
<div class="clear">&nbsp;</div>
<div><a href="http://www.cartbooks.com" title="Shopping Cart Accounting Integration"><img src="images/cartbooks-shopping-cart-integration.jpg" alt="Shopping Cart Accounting Integration" /></a></div>
<div id="footer_text">Free Accounting Software For Small Business<br />&copy;2011 - <?php echo date("Y") ?>&nbsp;<a href="http://www.cartbooks.com" title="CartBooks Accounting Software">CartBooks.com. All rights reserved.</a></div>
</body>
</html> 
