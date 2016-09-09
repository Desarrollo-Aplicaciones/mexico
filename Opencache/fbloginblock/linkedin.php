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
	
	
    include_once dirname(__FILE__).'/lib/oAuth/linkedinoAuth.php';
    include_once dirname(__FILE__).'/classes/linkedinhelp.class.php';
    
    $name_module = "fbloginblock";
	$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';
	
	$cookie = new Cookie('ref');
	$cookie->http_referer_custom = $http_referer;
	
	
	$lapikey = Configuration::get($name_module.'lapikey');
	$lapikey = trim($lapikey);
	$lsecret = Configuration::get($name_module.'lsecret');
	$lsecret = trim($lsecret);
	
	$data = array(
				  'access' => $lapikey,
				  'secret' => $lsecret, 
				);
	
				
	//var_dump($data); exit;			
	$linkedinhelp = new linkedinhelp();			
    $_http_host = $linkedinhelp->getBaseUrlCustom();
				
				
	$config = $data;
	
	if(Tools::strlen($config['access'])==0 || Tools::strlen($config['secret'])==0)
	 die("Error: Please fill LinkedIn API Key and LinkedIn Secret Key in the settings of the module.");
	
    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    $linkedin = new LinkedIn($config['access'], $config['secret'], $_http_host . 'modules/'.$name_module.'/linkedinauth.php' );
	//    $linkedin->debug = true;

    # Now we retrieve a request token. It will be set as $linkedin->request_token
    $linkedin->getRequestToken();
    $_SESSION['requestToken'] = serialize($linkedin->request_token);
  
    # With a request token in hand, we can generate an authorization URL, which we'll direct the user to
    ## echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
    $url = $linkedin->generateAuthorizeUrl();
   	Tools::redirect($url);
   
?>
