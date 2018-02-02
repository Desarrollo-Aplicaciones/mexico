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

class updatesocialapiemail {
	
	private $_http_host;
	private $_name;
	
	public function __construct(){
		
		$this->_name =  'fbloginblock'; 
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$this->_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
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

        if(version_compare(_PS_VERSION_, '1.7', '>')) {

            ### for 1.7 ps ###
            $id_lang = (int)($cookie->id_lang);
            $iso_code = Language::getIsoById($id_lang);
            $dir_mails = _PS_MODULE_DIR_ . $this->_name.'/mails/';

            if (is_dir($dir_mails . $iso_code . '/')) {
                $id_lang_mail = $id_lang;
            }
            else {
                $id_lang_mail = Language::getIdByIso('en');
            }
            ### for 1.7 ps ###

            @Mail::Send($id_lang_mail, 'account17', 'Welcome!',
                array('{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname,
                    '{email}' => $customer->email,
                    '{passwd}' => $real_passwd),
                $customer->email, $customer->firstname . ' ' . $customer->lastname, NULL, NULL,
                NULL, NULL, dirname(__FILE__) . '/../mails/');

        }else {
            @Mail::Send((int)($cookie->id_lang), 'account', 'Welcome!',
                array('{firstname}' => $customer->firstname,
                    '{lastname}' => $customer->lastname,
                    '{email}' => $customer->email,
                    '{passwd}' => $real_passwd),
                $customer->email,
                $customer->firstname . ' ' . $customer->lastname);
        }
		
	}
	
	
}