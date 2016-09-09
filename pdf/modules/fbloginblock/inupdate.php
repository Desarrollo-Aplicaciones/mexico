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
ob_start();
$status = 'success';
$message = '';
$content = '';


//@ini_set('display_errors', 'on');
//@error_reporting(E_ALL | E_STRICT);

$customer_id = Tools::getValue('cid');

$name_module = "fbloginblock";
if (version_compare(_PS_VERSION_, '1.5', '<')){
	require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
} else{
	$cookie = Context::getContext()->cookie;
}

include(dirname(__FILE__).'/fbloginblock.php');
$obj = new fbloginblock();

$tw_translate = $obj->twTranslate();

if($cookie->id_customer == $customer_id){
	$email = trim(Tools::getValue('email'));
	
	if (!Validate::isEmail($email)){
		$status = 'error';
		$message = $tw_translate['valid_email'];
	} elseif ($cookie->email != $email && Customer::customerExists($email, true)){
		$status = 'error';
		$message = $tw_translate['exists_customer'];
	}
	
	if($status!='error'){
		
		include(dirname(__FILE__).'/classes/inupdate.class.php');
		$inupdate = new inupdate();
		$inupdate->updateItem(array('email'=>$email,'id_customer'=> $customer_id));
		$content = $tw_translate['send_email'].' '.$email;
	}
	
} else {
	$status = 'error';
	$message = $tw_translate['log_in'];
}

	 
$response = new stdClass();
ob_get_clean();
//$content = ob_get_clean();
$response->status = $status;
$response->message = $message;	
$response->params = array('content' => $content);
echo Tools::jsonEncode($response);
