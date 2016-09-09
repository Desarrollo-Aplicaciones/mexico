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


include(dirname(__FILE__).'/classes/amazonhelp.class.php');



$aci = Configuration::get($name_module.'aci');
$aci = trim($aci);


$aru = Configuration::get($name_module.'aru');
$aru = trim($aru);

if(Tools::strlen($aci)==0 || Tools::strlen($aru)==0){
	echo "Error: Please fill Amazon Client ID and Amazon Allowed Return URL in the module settings!";
	exit;
}

if (Configuration::get('PS_SSL_ENABLED') == 0)
{
	echo 'Note: To enable Amazon Connect, Please make sure that "SSL" has enabled on your server';exit; 
}


// verify that the access token belongs to us
$c = curl_init('https://api.amazon.com/auth/o2/tokeninfo?access_token=' . urlencode($_REQUEST['access_token']));
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

$r = curl_exec($c);
curl_close($c);
$d = Tools::jsonDecode($r);



if ($d->aud != $aci) {
	echo 'Page not found'; exit;
}

// exchange the access token for user profile
$c = curl_init('https://api.amazon.com/user/profile');
curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: bearer ' . $_REQUEST['access_token']));
curl_setopt($c, CURLOPT_RETURNTRANSFER, true);

$r = curl_exec($c);
curl_close($c);
$d = Tools::jsonDecode($r);

//echo sprintf('%s %s %s', $d->name, $d->email, $d->user_id);


$first_name = $d->name;
$last_name = $d->name;
$email_address = $d->email;

$data_profile = array(
		'first_name'=>$first_name,
		'last_name'=>$last_name,
		'email'=>$email_address,

);


$amazonhelp = new amazonhelp();
$red_url =$amazonhelp->userLog(
		array(
				'data'=>$data_profile,
				'http_referer_custom'=>$http_referer
		)
);

Tools::redirect($red_url);

exit;
