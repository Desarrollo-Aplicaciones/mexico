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

class login {
	
	private $_action;
	private $_http_host;
	private $_http_referer;
	private $_name;
	
	public function __construct($data){
		
		$this->_name =  'fbloginblock'; 
		
		$this->_action = $data['p'];
		$this->_http_referer = isset($data['http_referer'])?$data['http_referer']:'';
		
		
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
	
 	public function mainAction(){
 		
 		$this->loginYahoo();
 		
 		
  	}
  	
  	
 	private function userLog($_data){				
		$_email = ($_data['contact/email']) ? $_data['contact/email'] : '';	
		
		$openid_identifier = isset($_SESSION['openid_identifier'])?$_SESSION['openid_identifier']:null;
		
		if ($openid_identifier && $_email == '')
			$_email = $openid_identifier;
			
		
		
		$_data_user =  $this->checkExist($_email);
        $_customer_id 	= (int) $_data_user['customer_id'];
        $_result = $_data_user['result'];	
        
        if (!$_customer_id)
            $this->createUser($_data,$_email);
        else
            $this->loginUser($_customer_id,$_result);
            
         
 		$http_referer =  $this->_http_referer; 
 		
 		if(Tools::strlen($http_referer)==0){
		     	$cookie = new Cookie('ref');
		      	
		     	$http_referer = $cookie->http_referer_custom;
		      	$cookie->http_referer_custom = '';
		}
        
        require_once(dirname(__FILE__).'/../fbloginblock.php');
        $obj = new fbloginblock();
        $data_order_page = $obj->getOrderPage(array('http_referrer'=>$http_referer));
        $uri = $data_order_page['uri'];
        
	      
		echo '<script>
				 window.opener.location.href = \''.$this->_http_host.$uri.'\';
				 window.opener.focus();
			     window.close();
				</script>';
    }
    
    private function loginUser($_customer, $result){
    	$cookie = $this->context->cookie;
		// authentication
		if ($result){
		    $customer = new Customer();
		    
		    $customer->id = $_customer;
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
	    if(version_compare(_PS_VERSION_, '1.5', '>')){
			Hook::exec('actionAuthentication');
		} else {
		       	Module::hookExec('authentication');
		}
    }
    
    private function deldigit($str){
    	$arr_out = array('');
		$arr_in = array(0,1,2,3,4,5,6,7,8,9,'_','(',')',',','.','-','+','&');

		$textout = str_replace($arr_in,$arr_out,$str);
		
		return $textout;
    
    }


    
    private function createUser($_data,$_email){
    		
		
			//// create new user ////
			//$gender = 1;
			$gender = ((isset($_data['person/gender']) && $_data['person/gender'] == 'M') || !isset($_data['person/gender']))?1:2;
			
    	
    	
    		$id_default_group = (int)Configuration::get($this->_name.'defaultgroup');
			
			if(isset($_data['namePerson/first']) && isset($_data['namePerson/last'])){
				$firstname = $this->deldigit(pSQL($_data['namePerson/first']));
				$lastname = $this->deldigit(pSQL($_data['namePerson/last']));
			}elseif(isset($_data['namePerson'])){
				$pattern = ("|^(.+?) (.*)$|su");
				preg_match($pattern, $_data['namePerson'], $arg);
				
				if(sizeof($arg)>0){
				$firstname = $this->deldigit(pSQL($arg[1]));
				$lastname = $this->deldigit(pSQL($arg[2]));
				} else {
					$firstname = $this->deldigit(pSQL($_data['namePerson']));
					$lastname = $this->deldigit(pSQL($_data['namePerson']));
				}
			}else{
				$firstname = $this->deldigit(pSQL($_data['namePerson/friendly']));
				$lastname = $this->deldigit(pSQL($_data['namePerson/friendly']));
			}
			
			
			$email = $_data['contact/email'];

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
			
			if(Tools::strlen($firstname)==0 || Tools::strlen($lastname) == 0){
				
				$pattern = ("|(.*)@.*|U");
				preg_match_all($pattern, $_email, $arg, PREG_SET_ORDER);
				$out  =  isset($arg[0][1])?$arg[0][1]:null;
				$firstname = $out;
				$lastname = $out;
				
				// openID fixed
				if(empty($out) && Tools::strlen($_email)>0){
					$email = $_email."@gmail.com";
					$firstname = $this->deldigit($_email);
					$lastname = $this->deldigit($_email);
					
					$_data_user =  $this->checkExist($email);
        			$_customer_id 	= (int) $_data_user['customer_id'];
        			// livejournal
        			if($_customer_id){
        				$_random_digit = rand(1,80);
        				$email = $_email.$_random_digit."@gmail.com";
        			}
        		} 
        		
        		
        		if(Tools::strlen($firstname)==0 || Tools::strlen($lastname)==0) {
					echo 'Empty First Name and Last Name!';
					break;
				}
				
				
			}
			
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
	        	echo 'Authentication failed!';exit;
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
	            include_once(dirname(__FILE__).'/statisticshelp.class.php');
    			$obj_help = new statisticshelp();
    			$obj_help->addCustomerToStatistics(
    												array('customer_id'=>$customer->id,
    													  'email'=>$customer->email,
    												      'id_shop'=>$this->getIdShop(),
    													  'type'=>6,
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
			
			
			Mail::Send((int)($cookie->id_lang), 'account', 'Welcome!', 
    						array('{firstname}' => $customer->firstname, 
    							  '{lastname}' => $customer->lastname, 
    							  '{email}' => $customer->email, 
    							  '{passwd}' => $real_passwd), 
    							  $customer->email,
    							  $customer->firstname.' '.$customer->lastname);
			}
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
		
		$_customer = $result['id_customer'];
		
		return array('customer_id' => $_customer, 'result' => $result);
    }
    
	public function loginpro($data){
		$pro = $data['pro'];
        try {
            $openid = new LightOpenID($this->_http_host);
			
            if(!$openid->mode) {
                if($pro) {
                    $openid->required = array('namePerson/friendly', 'contact/email', 'namePerson/first', 'namePerson/last','person/gender', 'namePerson');
                    $openid->identity = 'https://www.google.com/accounts/o8/id';  
                    Tools::redirect($openid->authUrl());                
                   
                }
            } elseif($openid->mode == 'cancel') {
                echo 'User has canceled authentication!';
            } else {
                echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';
                $this->userLog($openid->getAttributes());
            }
        } catch(ErrorException $e) {
            echo $e->getMessage();
        }
    }
    
	public function loginYahoo(){		
		try {
			//var_Dump($this->_http_host);exit;
			
				$openid = new LightOpenID($this->_http_host);
			
            if(!$openid->mode) {
                //if(isset($_POST['openid_identifier'])) {
					$_SESSION['openid_identifier'] = 'me.yahoo.com/';
					
                    //$service_url = $_POST['service_url'];
                    $openid->required = array('contact/email');
           			$openid->optional = array('namePerson', 'namePerson/friendly');
                                        
                    //$openid->identity = $service_url.$_POST['openid_identifier'];
                      
                    $openid->identity = 'me.yahoo.com/';
                    
                    Tools::redirect($openid->authUrl());
                    
                   
                //}
            } elseif($openid->mode == 'cancel') {
                echo 'User has canceled authentication!';
            } else {
                echo 'User ' . ($openid->validate() ? $openid->identity . ' has ' : 'has not ') . 'logged in.';                		
				$this->userLog($openid->getAttributes());
            }
        } catch(ErrorException $e) {
            echo $e->getMessage();
        }
  }
    
    
	
    
}

?>