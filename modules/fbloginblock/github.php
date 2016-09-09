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


include(dirname(__FILE__).'/lib/github/GithubAPI.class.php');
include(dirname(__FILE__).'/classes/githubhelp.class.php');



$client_id = Configuration::get($name_module.'gici');
$client_id = trim($client_id);
$client_secret = Configuration::get($name_module.'gics');
$client_secret = trim($client_secret);
$redirect_uri = Configuration::get($name_module.'giru');
$redirect_uri = trim($redirect_uri);

if(Tools::strlen($client_id)==0 || Tools::strlen($client_secret)==0 || Tools::strlen($redirect_uri)==0){
	echo "Error: Please fill Github Client Id, Github Client Secret, Github Callback URL in the module settings!";
	exit;
}


$appName = Configuration::get('PS_SHOP_NAME');


$code = Tools::getValue('code');

if($code)
{
	$fields = array( 'client_id'=>$client_id, 'client_secret'=>$client_secret, 'code'=>$code);
	$postvars = '';
	foreach($fields as $key=>$value) {
		$postvars .= $key . "=" . $value . "&";
	}

	$data = array('url' => 'https://github.com/login/oauth/access_token',
				  'data' => $postvars,
				  'header' => array("Content-Type: application/x-www-form-urlencoded","Accept: application/json"),
				  'method' => 'POST'
				 );

	$githubapi = new GithubAPI();
	
	$gitResponce = Tools::jsonDecode($githubapi->curlRequest($data));
	
	//echo "<pre>"; var_dump($gitResponce);

	if($gitResponce->access_token)
	{
		$data = array('url' => 'https://api.github.com/user?access_token='.$gitResponce->access_token,
					  'header' => array("Content-Type: application/x-www-form-urlencoded","User-Agent: ".$appName,"Accept: application/json"),
					  'method' => 'GET'
					 );

		$gitUser = Tools::jsonDecode($githubapi->curlRequest($data));

		//echo "<pre>"; var_dump($gitUser);
		
		
		if(!$gitUser->email){
			echo 'You don\'t have public email in your Github Account. Go to Github -> Settings -> Public email -> Select your Email!';exit;
		}
		
		//$first_name = $gitUser->name;
		//$last_name = $gitUser->name;
		
		$first_name = $gitUser->login;
		$last_name = $gitUser->login;
		
		
		$email_address = $gitUser->email;
		
		$data_profile = array(
				'first_name'=>$first_name,
				'last_name'=>$last_name,
				'email'=>$email_address,
				
		);
		$githubhelp = new githubhelp();
		$githubhelp->userLog(
								array(
										'data'=>$data_profile,
										'http_referer_custom'=>$http_referer
									 )
								);
		
		

	}
	else
	{
		echo "Some error occured try again"; exit;
	}
}
else
{
	
	$redirect_uri = "https://github.com/login/oauth/authorize?scope=user:email&client_id=".$client_id;
	Tools::redirect($redirect_uri);
	exit;
}




        
?>