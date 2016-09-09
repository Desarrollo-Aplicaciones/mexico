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

$_GET['controller'] = 'all'; 
$_GET['fc'] = 'module';
$_GET['module'] = 'fbloginblock';


include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

require_once(dirname(__FILE__).'/backward_compatibility/backward.php');


$action = Tools::getValue('p');
$http_referer = Tools::getValue('http_referer');

$cookie = new Cookie('ref');
$cookie->http_referer_custom = $http_referer;
parse_str(parse_url($_SERVER["HTTP_REFERER"])['query'], $queryString);
$cookie->http_back = ( isset($queryString['back']) && !empty($queryString['back']) ) ? $queryString['back'] : 'my-account.php';

$name_module = 'fbloginblock';



switch($action){
	case 'yahoo':
		include(dirname(__FILE__).'/classes/login.class.php');
		$obj_login = new login(array('p'=>$action,'http_referer'=>$http_referer));
		
		include(dirname(__FILE__).'/lib/openId/openid.php');
		include(dirname(__FILE__).'/lib/openId/provider/provider.php');
		$obj_login->loginYahoo();
	break;
	case 'login':
		include(dirname(__FILE__).'/classes/login.class.php');
		$obj_login = new login(array('p'=>$action,'http_referer'=>$http_referer));
		
		include(dirname(__FILE__).'/lib/openId/openid.php');
		include(dirname(__FILE__).'/lib/openId/provider/provider.php');
		$obj_login->loginYahoo();
	break;
	default:
		$oci = Configuration::get($name_module.'oci');
		$oci = trim($oci);
		$ocs = Configuration::get($name_module.'ocs');
		$ocs = trim($ocs);
		$oru = Configuration::get($name_module.'oru');
		$oru = trim($oru);
		
		if(Tools::strlen($oci)==0 || Tools::strlen($ocs)==0 || Tools::strlen($oru)==0){
			echo "Error: Please fill Google Client Id, Google Client Secret, Google Callback URL in the module settings!";
			exit;
		}
		
		require(dirname(__FILE__).'/lib/google/Google_Client.php');
		require(dirname(__FILE__).'/lib/google/contrib/Google_Oauth2Service.php');
		
		
		$client = new Google_Client();
		
		$client->setClientId($oci);
        $client->setClientSecret($ocs);
        $client->setRedirectUri($oru);
        
		$client->setApplicationName(
									array(
										'application_name'=>Configuration::get('PS_SHOP_NAME'),
									)
		
									);
		
		$oauth2 = new Google_Oauth2Service($client);
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
		}
		
		if(!Tools::getValue('code')) {
			
			 $url = $client->createAuthUrl();
			 Tools::redirect($url);
           	
			 exit();
		
		} else {
			  $client->authenticate(Tools::getValue('code'));
			  $_SESSION['token'] = $client->getAccessToken();
		 }
		
		if (isset($_SESSION['token'])) {
		 $client->setAccessToken($_SESSION['token']);
		}
		
		if (isset($_REQUEST['logout'])) {
		  unset($_SESSION['token']);
		  $client->revokeToken();
		}
		
		if ($client->getAccessToken()) {
		   $user = $oauth2->userinfo->get();
		
		  
		   $first_name= $user['given_name'];
		   $last_name = $user['family_name'];
		   $email = $user['email'];
		   $gender = isset($user['gender'])?$user['gender']:''; //female or mail
		  
		     
		   $data_profile = array('first_name'=>$first_name,
    					 		 'last_name'=>$last_name,
    					 		 'email'=>$email
    					 		);
    
    	   include(dirname(__FILE__).'/classes/googlehelp.class.php');
		   $googlehelp = new googlehelp();
    	   $googlehelp->userLog(
    					 array('data'=>$data_profile, 
    						   'http_referer_custom'=>$http_referer 
							  )
					     );
		  $_SESSION['token'] = $client->getAccessToken();
		} else {
		  $authUrl = $client->createAuthUrl();
		}
				
		
	break;
}						   
					   

        
?>