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

class scoophelp extends Module{
	
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
	
	public function getBaseUrlCustom(){
		return $this->_http_host;
	}
	
	
	public function userLog($_data){
		$data_linkedin = $_data['data'];	

		$_email = $data_linkedin['email'];
		
		$_data_user =  $this->checkExist($_email);
        $_customer_id 	= (int) $_data_user['customer_id'];
        $_result = $_data_user['result'];	
        
        if (!$_customer_id)
            $this->createUser($data_linkedin);
        else
            $this->loginUser($_customer_id,$_result);
            
        $http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
	
        
		if(Tools::strlen($http_referer)==0 && version_compare(_PS_VERSION_, '1.5', '>')){
		     	$cookie = new Cookie('ref');
		      	
		     	$http_referer = $cookie->http_referer_custom;
		      	$cookie->http_referer_custom = '';
		}
        
        require_once(_PS_MODULE_DIR_.$this->_name.'/fbloginblock.php');
        $obj = new fbloginblock();
        $data_order_page = $obj->getOrderPage(array('http_referrer'=>$http_referer));
        $uri = $data_order_page['uri'];    
        
		if((int)Configuration::get('redirpage') == 1 || $data_order_page['order_page'] == 1){ 
				echo '<script>
						window.opener.location.href = \''.$this->_http_host.$uri.'\';
						window.opener.focus();
						window.close();
				</script>';
			} else {
				echo '<script>window.opener.location.reload(true);window.opener.focus();window.close();</script>';
			}
    }
    
private function loginUser($_customer_id, $result){
    	$cookie = $this->context->cookie;
		// authentication
		if ($result){
		    $customer = new Customer();
		    
		    $customer->id = $_customer_id;
		    unset($result['id_customer']);
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
	    
	    ### add customer to statistics ###
	    include_once(_PS_MODULE_DIR_.$this->_name.'/classes/statisticshelp.class.php');
	    $obj_help = new statisticshelp();
	    $obj_help->addCustomerToStatistics(
	    		array('customer_id'=>$customer->id,
	    				'email'=>$customer->email,
	    				'id_shop'=>$this->getIdShop(),
	    				'type'=>51,
	    		)
	    );
	    ### add customer to statistics ###
	    
	    if(version_compare(_PS_VERSION_, '1.5', '>')){
			Hook::exec('actionAuthentication');
		} else {
		       	Module::hookExec('authentication');
		}
    }
    
    
 private function createUser($_data){

 			//// create new user ////
			$gender = 1;
 			$id_default_group = (int)Configuration::get($this->_name.'defaultgroup');
			
			$firstname = $this->deldigit(pSQL($_data['first_name']));
			$lastname = $this->deldigit(pSQL($_data['last_name']));
			
			$email = $_data['email'];

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
			
		
			
			$_data_user_exist =  $this->checkExist($email);
			$_customer_id_exits = (int) $_data_user_exist['customer_id'];
			if($_customer_id_exits){
				
				$cookie = $this->context->cookie;
				// authentication
				if(version_compare(_PS_VERSION_, '1.5', '>')){
				$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        	WHERE `active` = 1 AND `email` = \''.pSQL($email).'\'  
			        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' 
			        	AND `id_shop` = '.(int)$this->getIdShop().'
			        	'; 	
				} else {
				$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        	WHERE `active` = 1 AND `email` = \''.pSQL($email).'\'  
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
					Hook::exec('actionAuthentication');
				} else {
				       	Module::hookExec('authentication');
				}
	        
			} else {
			
			if(version_compare(_PS_VERSION_, '1.5', '>')){
				
			$id_shop_group = (int)Configuration::get($this->_name.'defaultgroup');
				if(!$id_shop_group)
					$id_shop_group = Context::getContext()->shop->id_shop_group;
				
			$sql = 'insert into `'._DB_PREFIX_.'customer` SET
						id_shop = '.(int)$this->getIdShop().', id_shop_group = '.(int)$id_shop_group.',
						id_gender = '.(int)$gender.', id_default_group = '.(int)$id_default_group.',
						firstname = \''.pSQL($firstname).'\', lastname = \''.pSQL($lastname).'\',
						email = \''.pSQL($email).'\', passwd = \''.pSQL($passwd).'\',
						last_passwd_gen = \''.pSQL($last_passwd_gen).'\',
						secure_key = \''.pSQL($secure_key).'\', active = '.(int)$active.',
						date_add = \''.pSQL($date_add).'\', date_upd = \''.pSQL($date_upd).'\' ';
			
			} else {

			
			$sql = 'insert into `'._DB_PREFIX_.'customer` SET
						id_gender = '.(int)$gender.', id_default_group = '.(int)$id_default_group.',
						firstname = \''.pSQL($firstname).'\', lastname = \''.pSQL($lastname).'\',
						email = \''.pSQL($email).'\', passwd = \''.pSQL($passwd).'\',
						last_passwd_gen = \''.pSQL($last_passwd_gen).'\',
						secure_key = \''.pSQL($secure_key).'\', active = '.(int)$active.',
						date_add = \''.pSQL($date_add).'\', date_upd = \''.pSQL($date_upd).'\' ';
			
			}
			
			
			Db::getInstance()->Execute($sql);
			$insert_id = Db::getInstance()->Insert_ID();
			
			
			
			// insert record in customer group
			$id_group = (int)Configuration::get($this->_name.'defaultgroup');
			
			$sql = 'INSERT into `'._DB_PREFIX_.'customer_group` SET 
						   id_customer = '.(int)$insert_id.', id_group = '.(int)$id_group.' ';
			Db::getInstance()->Execute($sql);
			
		
			// auth customer
			$cookie = $this->context->cookie;
			$customer = new Customer();
	        $authentication = $customer->getByEmail(trim($email), trim($real_passwd));
	        if (!$authentication OR !$customer->id) {
	        	echo 'Authentication failed!'; exit;
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
	            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/statisticshelp.class.php');
    			$obj_help = new statisticshelp();
    			$obj_help->addCustomerToStatistics(
    												array('customer_id'=>$customer->id,
    													  'email'=>$customer->email,
    												      'id_shop'=>$this->getIdShop(),
    													  'type'=>51,
    													  )
    											   );
    			### add customer to statistics ###
	            
	            if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) OR Cart::getNbProducts($cookie->id_cart) == 0))
	                $cookie->id_cart = (int)(Cart::lastNoneOrderedCart((int)($customer->id)));
	        
				if(version_compare(_PS_VERSION_, '1.5', '>')){
					Hook::exec('actionAuthentication');
				} else {
				       	Module::hookExec('authentication');
				}
	        }

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

                    Mail::Send($id_lang_mail, 'account17', 'Welcome!',
                        array('{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{email}' => $customer->email,
                            '{passwd}' => $real_passwd),
                        $customer->email, $customer->firstname . ' ' . $customer->lastname, NULL, NULL,
                        NULL, NULL, dirname(__FILE__) . '/../mails/');

                }else {
                    Mail::Send((int)($cookie->id_lang), 'account', 'Welcome!',
                        array('{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{email}' => $customer->email,
                            '{passwd}' => $real_passwd),
                        $customer->email,
                        $customer->firstname . ' ' . $customer->lastname);
                }
			}
    }
    
    
    
 	private function deldigit($str){
    	$arr_out = array('');
		$arr_in = array(0,1,2,3,4,5,6,7,8,9,'_','(',')',',','.','-','+','&');

		$textout = str_replace($arr_in,$arr_out,$str);
		
		return $textout;
    
    }
    
	private function getIdShop(){
    	if(version_compare(_PS_VERSION_, '1.5', '>')){
        	$id_shop = Context::getContext()->shop->id;
        } else {
        	$id_shop = 0;
        }
        return $id_shop;
    }
    
	private function checkExist($email){        
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		        	WHERE  `email` = \''.pSQL($email).'\'
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' 
		        	AND `id_shop` = '.(int)$this->getIdShop().'
		        	'; 	
		} else {
			$sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer` 
		        	WHERE  `email` = \''.pSQL($email).'\'
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'
		        	'; 
		}
		
		$result = Db::getInstance()->GetRow($sql);
		
		$_customer = $result['id_customer'];

        ## if customer disabled ##
        if(!empty($result) && $result['active'] == 0){
            include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
            $obj = new $this->_name();
            $data_tr = $obj->translateCustom();
            echo $data_tr['disabled'];exit;
        }
        ## if customer disabled ##
		
		return array('customer_id' => $_customer, 'result' => $result);
    }
    
    
    public function scoopLogin($_data){
    	 
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/http.php';
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/oauth_client.php';
    	 
    	
    	$name_module = $this->_name;
    	
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}    	
    	
    	
    	$client = new oauth_client_class();
    	$client->server = 'Scoop.it';
    	
    	
    	include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
    	$obj_module = new $name_module();
    	$redirect_uri = $obj_module->getRedirectURL(array('typelogin'=>'scoop','is_settings'=>1));
    	$client->redirect_uri = $redirect_uri;
    	
    	
    	$sci = Configuration::get($name_module.'sci');
    	$sci = trim($sci);
    	$scs = Configuration::get($name_module.'scs');
    	$scs = trim($scs);
    	
    
    	
    	$client->client_id = $sci;
    	$application_line = __LINE__;
    	$client->client_secret = $scs;
    	
    	if(Tools::strlen($client->client_id) == 0
    			|| Tools::strlen($client->client_secret) == 0)
			    		die('Please go to Scoop.it Apps page https://www.scoopit.com/developers/apps , '.
						'create an application, and in the line '.$application_line.
						' set the client_id to Consumer key and client_secret with Consumer secret. '.
						'The Callback URL must be '.$client->redirect_uri).' Make sure this URL is '.
						'not in a private network and accessible to the Scoop.it site.';
    	
    	/* API permissions
    	 */
    	$client->scope = 'email';
    	if(($success = $client->Initialize()))
    	{
    		if(($success = $client->Process()))
    		{
    			if(Tools::strlen($client->authorization_error))
    			{
    				$client->error = $client->authorization_error;
    				$success = false;
    			}
    			elseif(Tools::strlen($client->access_token))
    			{
    				$success = $client->CallAPI(
    						'https://www.scoop.it/api/1/profile',
    						'GET', array(), array('FailOnAccessError'=>true), $user);
    			}
    		}
    		$success = $client->Finalize($success);
    	}
    	if($client->exit)
    		exit;
    	if($success)
    	{

    		$last_name = $user->name_details->surname;
    		$first_name = $user->name_details->familiar_name;
    		$email_address = $user->email;
    		$data_profile = array('first_name'=>$first_name,
    				'last_name'=>$last_name,
    				'email'=>$email_address
    		);
    		
    		//var_dump($data_profile);
    		
    		//echo "<pre>"; var_dump($user);exit;
    		  
    		$this->userLog(
    				array('data'=>$data_profile,
    						'http_referer_custom'=>$http_referer
    				)
    		);
    	
    	}
    	else
    	{
    		echo 'Error:'.HtmlSpecialChars($client->error);
    	}
    }
}