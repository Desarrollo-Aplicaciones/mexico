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

$name_module = "fbloginblock";
if (version_compare(_PS_VERSION_, '1.5', '<')){
	require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
} else{
	$cookie = Context::getContext()->cookie;
}


$action = $_REQUEST['action'];


switch ($action){
	case 'login':
		include(dirname(__FILE__).'/lib/Facebook/Exception.php');
		include(dirname(__FILE__).'/lib/Facebook/Api.php');
		
		$secret = $_REQUEST['secret'];
		$appid = $_REQUEST['appid'];
		$facebook = new Facebook_Api(array(
		  'appId'  => $appid,
		  'secret' => $secret,
		  'cookie' => true,
		));
		
		$fb_session = $facebook->getSession();
		
		// 	Session based API call.
		if ($fb_session) {
		  try {
		    $uid = $facebook->getUser();
		    $me = $facebook->api('/me');
		  } catch (Facebook_Exception $e) {
		    $status = 'error';
			$message = $e;
		  }
		}
		
		
		### fix for updated API ###
		if(empty($me['email'])){
		
			$access_token = $facebook->getAccessToken();
		
			$url_fix = 'https://graph.facebook.com/me?access_token='.$access_token.'&fields=email,id,first_name,last_name,name,birthday,gender';
		
			if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
				$data = Tools::file_get_contents($url_fix);
			} else {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url_fix);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$data = curl_exec($ch);
			}
		
			$me = Tools::jsonDecode($data);
			$me = (array)$me;

			
		}

		if((empty($me['first_name']) || empty($me['last_name'])) && !empty($me['email'])){
				$explode_data = explode("@",$me['email']);
				$name_email = $explode_data[0];
				$name_email = preg_replace('/[^a-zA-Z]/', '', $name_email);
				$me['first_name'] = $name_email;
				$me['last_name'] = $name_email;
		}

		
		
		
		### fix for updated API ###
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
        	$id_shop = Context::getContext()->shop->id;
        } else {
        	$id_shop = 0;
        }
        
        if(empty($me['email'])){
        	$status = 'error';
			$message = 'You don\'t have primary email in your Facebook Account. Go to Facebook -> Settings -> General -> Email and set Primary email!';
        } else {
        
        // Desactiva la opción de invitado, 
		Db::getInstance()->update('customer', array(
			'is_guest' => 0
		), "`email` = '" . $me['email'] . "' AND `is_guest` <> 0");
		
		if (is_array($me)) {
			$sql= 'SELECT `customer_id`
					FROM `'._DB_PREFIX_.'fb_customer`
					WHERE `fb_id` = '.(int)($me['id']).' AND `id_shop` = '.(int)($id_shop).'
					LIMIT 1';
			$result = Db::getInstance()->ExecuteS($sql);
			
			if(sizeof($result)>0)
				$customer_id = $result[0]['customer_id'];
			else
				$customer_id = 0;
		}
		
		$exists_mail = 0;
		//chek for dublicate
		if(!empty($me['email'])){
			if(version_compare(_PS_VERSION_, '1.5', '>')){
			$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		        	WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'  
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.(int)($id_shop).'';
			} else {
			$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		        	WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'  
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'';
			}
			$result_exists_mail = Db::getInstance()->GetRow($sql);
			if($result_exists_mail)
				$exists_mail = 1;
		}
		
		$auth = 0;
		if($customer_id && $exists_mail){
			$auth = 1;
		}

		if(empty($customer_id) &&  $exists_mail){
			// insert record into customerXfacebook table
			$sql = 'INSERT into `'._DB_PREFIX_.'fb_customer` SET
						   customer_id = '.(int)$result_exists_mail['id_customer'].', 
						   fb_id = '.(int)$me['id'].',
						   id_shop = '.(int)$id_shop.' ';
			Db::getInstance()->Execute($sql);
			
			$auth = 1;
		}
		
		
		
		if($auth){
			
			// authentication
			if(version_compare(_PS_VERSION_, '1.5', '>')){
			$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		        	WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'  
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.(int)($id_shop).'
		        	'; 	
			} else {
			$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		        	WHERE `active` = 1 AND `email` = \''.pSQL($me['email']).'\'  
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'
		        	'; 
			}
			$result = Db::getInstance()->GetRow($sql);
			
			if ($result){
			    $customer = new Customer();
			    
			    $customer->id = $result['id_customer'];
		        foreach ($result AS $key => $value)
		            if (key_exists($key, $customer))
		                $customer->{$key} = $value;
	        }
	        
	        $cookie->id_customer = (int)($customer->id);
	        $cookie->customer_lastname = $customer->lastname;
	        $cookie->customer_firstname = $customer->firstname;
	        $cookie->logged = 1;
	        $cookie->passwd = $customer->passwd;
	        $cookie->email = $customer->email;
	        if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) 
	        	OR Cart::getNbProducts($cookie->id_cart) == 0))
	            $cookie->id_cart = (int)(Cart::lastNoneOrderedCart((int)($customer->id)));
			if(version_compare(_PS_VERSION_, '1.5', '>')){
				
				//echo "<pre>";
				//var_dump(debug_backtrace()); echo "</pre>"; exit();
				Hook::exec('authentication');
				/*$file = fopen("archivo.txt", "w");
				fwrite($file, "Prueba" . PHP_EOL);
				fwrite($file, print_r(debug_backtrace(), true) . PHP_EOL);
				fclose($file);*/
				//Tools::redirect('index.php?controller=order');
			} else {
			       	Module::hookExec('authentication');
			}
	        
	   	
		} else {
			$fb_id = $me['id'];
		
			//// create new user ////
			$gender = ($me['gender'] == 'male')?1:2;
			
 			$id_default_group = (int)Configuration::get($name_module.'defaultgroup');
			$firstname = pSQL($me['first_name']);
			$lastname = pSQL($me['last_name']);
			$email = $me['email'];

			// generate passwd
			srand((double)microtime()*1000000);
			$passwd = Tools::substr(uniqid(rand()),0,12);
			$real_passwd = $passwd; 
			$passwd = md5(pSQL(_COOKIE_KEY_.$passwd)); 
			
			$last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));
			$secure_key = md5(uniqid(rand(), true));
			$active = 1;
			$date_add = date('Y-m-d H:i:s'); //'2011-04-04 18:29:15';
			$date_upd = $date_add;
			
			if(Tools::strlen($me['first_name'])==0 || Tools::strlen($me['last_name']) == 0){
				$status = 'error';
				$message = 'Empty First Name and Last Name!';
				exit;
			}
			$birthday = '';
			if(Tools::strlen($me['birthday'])>0){
				$birthday = strtotime($me['birthday']);
				$birthday = date("Ymd",$birthday);
				$birthday = 'birthday = \''.pSQL($birthday).'\',';
			}


			if(version_compare(_PS_VERSION_, '1.5', '>')){
				
				$id_shop_group = (int)Configuration::get($name_module.'defaultgroup');
				if(!$id_shop_group)
					$id_shop_group = Context::getContext()->shop->id_shop_group;
				
				
				$sql = 'insert into `'._DB_PREFIX_.'customer` SET 
						   id_shop = '.(int)$id_shop.', id_shop_group = '.(int)$id_shop_group.',
						   id_gender = '.(int)$gender.', id_default_group = '.(int)$id_default_group.',
						   firstname = \''.pSQL($firstname).'\', lastname = \''.pSQL($lastname).'\',
						   email = \''.pSQL($email).'\', passwd = \''.pSQL($passwd).'\',
						   '.$birthday.'
						   last_passwd_gen = \''.pSQL($last_passwd_gen).'\',
						   secure_key = \''.pSQL($secure_key).'\', active = '.(int)$active.',
						   date_add = \''.pSQL($date_add).'\', date_upd = \''.pSQL($date_upd).'\' ';
			
			} else {

			$sql = 'insert into `'._DB_PREFIX_.'customer` SET 
						   id_gender = '.(int)$gender.', id_default_group = '.(int)$id_default_group.',
						   firstname = \''.pSQL($firstname).'\', lastname = \''.pSQL($lastname).'\',
						   email = \''.pSQL($email).'\', passwd = \''.pSQL($passwd).'\',
						    '.$birthday.'
						   last_passwd_gen = \''.pSQL($last_passwd_gen).'\',
						   secure_key = \''.pSQL($secure_key).'\', active = '.(int)$active.',
						   date_add = \''.pSQL($date_add).'\', date_upd = \''.pSQL($date_upd).'\' ';
			
			}
			
			Db::getInstance()->Execute($sql);
			$insert_id = Db::getInstance()->Insert_ID();
				
			
			
			// insert record in customer group
			
			$id_group = (int)Configuration::get($name_module.'defaultgroup');
			$sql = 'INSERT into `'._DB_PREFIX_.'customer_group` SET 
						   id_customer = '.(int)$insert_id.', id_group = '.(int)$id_group.' ';
			Db::getInstance()->Execute($sql);
			
			
			
			
			// insert record into customerXfacebook table
			$sql_exists= 'SELECT `customer_id`
					FROM `'._DB_PREFIX_.'fb_customer`
					WHERE `fb_id` = '.(int)($me['id']).' AND `id_shop` = '.(int)($id_shop).'
					LIMIT 1';
			$result_exists = Db::getInstance()->ExecuteS($sql_exists);
			if(sizeof($result_exists)>0)
				$customer_id = $result_exists[0]['customer_id'];
			else
				$customer_id = 0;
				
			if($customer_id){
				$sql_del = 'DELETE FROM `'._DB_PREFIX_.'fb_customer` WHERE `customer_id` = '.(int)$customer_id.' AND `id_shop` = '.(int)$id_shop.'';
				Db::getInstance()->Execute($sql_del);
				
			}
			
				$sql = 'INSERT into `'._DB_PREFIX_.'fb_customer` SET
							   customer_id = '.(int)$insert_id.', fb_id = '.(int)$fb_id.', id_shop = '.(int)$id_shop.' ';
				Db::getInstance()->Execute($sql);
							
			//// end create new user ///
			
			
			// auth customer
			$customer = new Customer();
	        $authentication = $customer->getByEmail(trim($email), trim($real_passwd));
	        if (!$authentication OR !$customer->id) {
	        	$status = 'error';
				$message = 'authentication failed!';
	        }
	        else
	        {
	            $cookie->id_customer = (int)($customer->id);
	            $cookie->customer_lastname = $customer->lastname;
	            $cookie->customer_firstname = $customer->firstname;
	            $cookie->logged = 1;
	            $cookie->passwd = $customer->passwd;
	            $cookie->email = $customer->email;
	            
	            ### add customer to statistics ###
	            include_once(dirname(__FILE__).'/classes/statisticshelp.class.php');
    			$obj_help = new statisticshelp();
    			$obj_help->addCustomerToStatistics(
    												array('customer_id'=>$customer->id,
    													  'email'=>$customer->email,
    												      'id_shop'=>$id_shop,
    													  'type'=>1,
    													  )
    											   );
    			### add customer to statistics ###
	            
	            
	            if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) OR Cart::getNbProducts($cookie->id_cart) == 0))
	                $cookie->id_cart = (int)(Cart::lastNoneOrderedCart((int)($customer->id)));
		        if(version_compare(_PS_VERSION_, '1.5', '>')){
					Hook::exec('authentication');
				} else {
				       	Module::hookExec('authentication');
				}
	        }
			
			
			Mail::Send((int)($cookie->id_lang), 'account', 'Welcome!', 
    						array('{firstname}' => $customer->firstname, 
    							  '{lastname}' => $customer->lastname, 
    							  '{email}' => $customer->email, 
    							  '{passwd}' => $real_passwd), 
    							  $customer->email,
    							  $customer->firstname.' '.$customer->lastname);
			
		}
		
        }
		
	break;
	case 'logout':
	break;
	default:
		$status = 'error';
		$message = 'Unknown parameters!';
	break;
}


$response = new stdClass();
$content = ob_get_clean();
$response->status = $status;
$response->message = $message;	
$response->params = array('content' => $content);
echo Tools::jsonEncode($response);


?>