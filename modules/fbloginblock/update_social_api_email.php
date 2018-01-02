<?php
/**
 * 2011 - 2017 StorePrestaModules SPM LLC.
 *
 * MODULE fbloginblock
 *
 * @author    SPM <kykyryzopresto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   1.7.7
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=61669
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(_PS_ROOT_DIR_.'/init.php');
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

include_once(_PS_MODULE_DIR_.$name_module.'/fbloginblock.php');
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

        include_once(_PS_MODULE_DIR_.$name_module.'/classes/updatesocialapiemail.class.php');
		$inupdate = new updatesocialapiemail();
		$inupdate->updateItem(array('email'=>$email,'id_customer'=> $customer_id));
		$content = $tw_translate['send_email'].' '.$email;
	}
	
} else {
	$status = 'error';
	$message = $tw_translate['log_in'];
}

	 
$response = new stdClass();
ob_get_clean();
$response->status = $status;
$response->message = $message;	
$response->params = array('content' => $content);
echo json_encode($response);
