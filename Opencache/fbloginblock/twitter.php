<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category social_networks
 * @package fbloginblock
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

require_once(dirname(__FILE__).'/backward_compatibility/backward.php');


$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
$http_referer = isset($_REQUEST['http_referer'])?urldecode($_REQUEST['http_referer']):'';


$name_module = 'fbloginblock';

$cookie = new Cookie('ref');
$cookie->http_referer_custom = $http_referer;


include(dirname(__FILE__).'/lib/twitteroauth/twitteroauth.php');
include(dirname(__FILE__).'/classes/twitter.class.php');

$consumer_key = Configuration::get($name_module.'twitterconskey');
$consumer_key = trim($consumer_key);
$consumer_secret = Configuration::get($name_module.'twitterconssecret');
$consumer_secret = trim($consumer_secret);
$callback = "";

$obj_twitter = new twitter(array('key'=>$consumer_key,
								 'secret' =>$consumer_secret,
								 'callback' => $callback,
								 'http_referer'=>$http_referer )
						   );

switch($action){
	case 'callback':
		$obj_twitter->callback();
	break;
	case 'connect':
		$obj_twitter->connect();
	break;
	case 'login':
		$obj_twitter->login();
	break;
	default:
		$obj_twitter->login();
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
		}
	break;
}						   
						   

        
?>