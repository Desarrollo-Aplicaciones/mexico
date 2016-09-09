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
	

    require_once(dirname(__FILE__).'/lib/oAuth/linkedinoAuth.php');
    require_once(dirname(__FILE__).'/lib/oAuth/class.linkedClass.php');
    include_once dirname(__FILE__).'/classes/linkedinhelp.class.php';
    $linkedinhelp = new linkedinhelp();		
    
    $name_module = "fbloginblock";
	$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';
	
	$lapikey = Configuration::get($name_module.'lapikey');
	$lapikey = trim($lapikey);
	$lsecret = Configuration::get($name_module.'lsecret');
	$lsecret = trim($lsecret);
	
	$data = array(
				  'access' => $lapikey,
				  'secret' => $lsecret, 
				);
				
	
			
    $linkedClass   =   new linkedClass($data);
    # First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    $linkedin = new LinkedIn($data['access'], $data['secret']);
    //$linkedin->debug = true;

   if (isset($_REQUEST['oauth_verifier'])){
        $_SESSION['oauth_verifier']     = $_REQUEST['oauth_verifier'];

        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
        $linkedin->getAccessToken($_REQUEST['oauth_verifier']);
        $_SESSION['oauth_access_token'] = serialize($linkedin->access_token);
   }
   else{
        $linkedin->request_token    =   unserialize($_SESSION['requestToken']);
        $linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
        $linkedin->access_token     =   unserialize($_SESSION['oauth_access_token']);
   }
   $content1 = $linkedClass->linkedinGetUserInfo($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);

   
    $xml   = simplexml_load_string($content1);
    
    
    $first_name = '';
    $last_name = '';
    $email_address = '';
    
    foreach ($xml as $name => $element) {
    	switch($name){
    	case 'first-name':
    		$first_name = $element;
    	break;
    	case 'last-name':
    		$last_name = $element;
    	break;
    	case 'email-address':
    		$email_address = $element;
    	break;
    	}
    }
    
    $data_profile = array('first_name'=>$first_name,
    					  'last_name'=>$last_name,
    					  'email'=>$email_address
    					 );
    
    $linkedinhelp->userLog(
    					 array('data'=>$data_profile, 
    						   'http_referer_custom'=>$http_referer 
							  )
					     );
    
    
    
    
?>