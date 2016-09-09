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




$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';
	


$name_module = 'fbloginblock';

$cookie = new Cookie('ref');
$cookie->http_referer_custom = $http_referer;


include(dirname(__FILE__).'/lib/foursquare/FoursquareAPI.class.php');
include(dirname(__FILE__).'/classes/foursquarehelp.class.php');


$client_id = Configuration::get($name_module.'fsci');
$client_id = trim($client_id);
$client_secret = Configuration::get($name_module.'fscs');
$client_secret = trim($client_secret);
$redirect_uri = Configuration::get($name_module.'fsru');
$redirect_uri = trim($redirect_uri);

if(Tools::strlen($client_id)==0 || Tools::strlen($client_secret)==0 || Tools::strlen($redirect_uri)==0){
	echo "Error: Please fill Foursquare Client Id, Foursquare Client Secret, Foursquare Callback URL in the module settings!";
	exit;
}


$foursquare = new FoursquareAPI($client_id,$client_secret);

// Getting request  token
$code = Tools::getValue('code');
if($code){
	$auth_token = $foursquare->GetToken($code, $redirect_uri);
	
	
	// Load the Foursquare API library
	$foursquare_current = new FoursquareAPI();
	$foursquare_current->SetAccessToken($auth_token);
	
	// Perform a request to getting user details
	$response = $foursquare_current->GetPrivate("users/self");
	$user_data = Tools::jsonDecode($response);
	
	//print_r($user_data);
	/* echo '<b>Userid:</b>   '. $user_data->response->user->id.'<br>';
	echo '<b>FirstName:</b>  '.$user_data->response->user->firstName.'<br>';
	echo '<b>LastName:</b>   '.$user_data->response->user->lastName.'<br>';
	echo '<b>Gender:</b>    '.$user_data->response->user->gender.'<br>';
	echo '<b>Relationship:</b> '.$user_data->response->user->relationship.'<br>';
	echo '<b>HomeCity:</b>   '.$user_data->response->user->homeCity.'<br>';
	echo '<b>Email:</b>    '.$user_data->response->user->contact->email.'<br>';
	echo '<b>Facebook Id:</b>    '.$user_data->response->user->contact->facebook.'<br>'; */
	
	$first_name = $user_data->response->user->firstName;
	$last_name = $user_data->response->user->lastName;
	$email_address = $user_data->response->user->contact->email;
	$gender = $user_data->response->user->gender;
	
	$data_profile = array(
						  'first_name'=>$first_name,
						  'last_name'=>$last_name,
						  'email'=>$email_address,
						  'gender'=>$gender,
						);
	$foursquarehelp = new foursquarehelp();
	$foursquarehelp->userLog(
			array('data'=>$data_profile,
				  'http_referer_custom'=>$http_referer
			)
	);
	
	exit;
} else {
	 Tools::redirect($foursquare->AuthenticationLink($redirect_uri));
	 exit;
}

        
?>