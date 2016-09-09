<?php

/*
  @copyright  2007-2011 PrestaShop SA
  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*/

/***************************************************************************************************
* Copyright(c) @2011 ANTERP SOLUTIONS. All rights reserved.
* Website				http://www.anterp.com
* Authors		    	tclim
* Date Created     		May 26, 2012 4:38:48 PM
* 
* Additional License	This software require you to buy from ANTERP SOLUTIONS. 
* 						You have no right to redistribute this program.
* 
* Description			Data Sync Suites developed and distributed by ANTERP SOLUTIONS.
*  
 **************************************************************************************************/

	//Time Out Setting
	// Set 0 = No timeout (Caution - it will cause unexpected usage if you set to unlimited timeout)
	@set_time_limit(0);
	
	//System
	include(dirname(__FILE__).'/../../config/config.inc.php');
	
	//Setting Load Config URL
	$url = '';
	
	if (isset ($_GET['posturl'])) {
		$url = trim($_GET['posturl']);
	} else if (isset ($_POST['posturl'])) {
		$url = trim($_POST['posturl']);
	}

	$type = 'POST';
	 
	if (isset ($_GET['type'])) {
		$type = trim($_GET['type']);
	} else if (isset ($_POST['type'])) {
		$type = trim($_POST['type']);
	}	
 	
	if (!empty($url)) {
	 	//Calling the asynchronously process...Do not return anything
	 	curl_request_async($url, $type);
	}
 	
 	// $type must equal 'GET' or 'POST'
  function curl_request_async($url, $type='POST')
  {
  	  $post_string = '';
      $parts=parse_url($url);

      $fp = fsockopen($parts['host'],
          isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);

      // Data goes in the path for a GET request
      if('GET' == $type) $parts['path'] .= '?'.$post_string;

      $out = "$type ".$parts['path']." HTTP/1.1\r\n";
      $out.= "Host: ".$parts['host']."\r\n";
      $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
      $out.= "Content-Length: ".strlen($post_string)."\r\n";
      $out.= "Connection: Close\r\n\r\n";
      // Data goes in the request body for a POST request
      if ('POST' == $type && isset($post_string)) $out.= $post_string;

      fwrite($fp, $out);
      fclose($fp);
  }

?>