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

class twupdate {
	
	private $_http_host;
	private $_name;
	
	public function __construct(){
		
		$this->_name =  'fbloginblock'; 
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$this->_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
		}
		
		if (version_compare(_PS_VERSION_, '1.5', '<')){
			require_once(_PS_MODULE_DIR_.$this->_name.'/backward_compatibility/backward.php');
		}
	
	
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}
	
	

	public function updateItem($data){
		
		$email = $data['email'];
		$id_customer = $data['id_customer'];
		
		// generate passwd
		srand((double)microtime()*1000000);
		$passwd = Tools::substr(uniqid(rand()),0,12);
		$real_passwd = $passwd; 
		$passwd = md5(pSQL(_COOKIE_KEY_.$passwd)); 
			
		$last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));
		
			
		$sql = 'UPDATE `'._DB_PREFIX_.'customer`
						SET passwd = \''.pSQL($passwd).'\',
						last_passwd_gen = \''.pSQL($last_passwd_gen).'\',
						email = \''.pSQL($email).'\'
						WHERE id_customer = '.(int)$id_customer.'
						';
		Db::getInstance()->Execute($sql);
		
		
		
        
		if(version_compare(_PS_VERSION_, '1.5', '>')){
		$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		       	WHERE `active` = 1 AND `email` = \''.pSQL($email).'\'  
		       	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.(int)Context::getContext()->shop->id.'
		       	'; 	
		} else {
		$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		       	WHERE `active` = 1 AND `email` = \''.pSQL($email).'\'  
		       	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'
		       	'; 
		}
		$result = Db::getInstance()->GetRow($sql);
		
		$customer = new Customer();
		$customer->id = $result['id_customer'];
		   foreach ($result AS $key => $value)
		        if (key_exists($key, $customer))
		             $customer->{$key} = $value;
		                
		$cookie = $this->context->cookie;
		$cookie->email = $customer->email;
		$cookie->passwd = $passwd;
		@Mail::Send((int)($cookie->id_lang), 'account', 'Welcome!', 
    						array('{firstname}' => $customer->firstname, 
    							  '{lastname}' => $customer->lastname, 
    							  '{email}' => $customer->email, 
    							  '{passwd}' => $real_passwd), 
    							  $customer->email,
    							  $customer->firstname.' '.$customer->lastname);
		
	}
	
	
}