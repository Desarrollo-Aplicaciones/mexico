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
	
	
   include_once dirname(__FILE__).'/lib/instagram/instagram.class.php';
   include_once(dirname(__FILE__).'/classes/instagramhelp.class.php');
    
    $name_module = "fbloginblock";
	$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';
	
	$cookie = new Cookie('ref');
	$cookie->http_referer_custom = $http_referer;
	
	$obj_instagramhelp = new instagramhelp(
										array('http_referer'=>$http_referer )
									);
	
	$client_id = Configuration::get($name_module.'ici');
	$client_id = trim($client_id);
	$client_secret = Configuration::get($name_module.'ics');
	$client_secret = trim($client_secret);
	$callback = Configuration::get($name_module.'iru');
	$callback = trim($callback);
	
	$instagram = new Instagram(array(
			'apiKey'      => $client_id,
			'apiSecret'   => $client_secret,
			'apiCallback' => $callback
	));
	
	
// Receive OAuth code parameter
$code = Tools::getValue('code');



// Check whether the user has granted access
if (true === isset($code)) {

  // Receive OAuth token object
  $data = $instagram->getOAuthToken($code);
  // Take a look at the API response
   
		if(empty($data->user->username))
		{
			
			Tools::redirect($instagram->getLoginUrl());exit;
		
		}
		else
		{
			$_SESSION['userdetails']=$data;
			
			$username = $data->user->username;
			$fullname=$data->user->full_name;
			$bio=$data->user->bio;
			$website=$data->user->website;
			$id=$data->user->id;
			$token=$data->access_token;
				
			$data_instagram = array('username'=>$username,
									'fullname'=>$fullname,
									'bio'=>$bio,
									'website'=>$website,
									'id'=>$id,
									'token'=>$token,
									);
			
			
			
			
			
			
			$obj_instagramhelp->login($data_instagram);
		
		}

} 
else 
{
	// Check whether an error occurred
	if (Tools::getValue('error')) 
	{
		echo 'An error occurred: '.Tools::getValue('error_description'); exit;
	}

}

?>
