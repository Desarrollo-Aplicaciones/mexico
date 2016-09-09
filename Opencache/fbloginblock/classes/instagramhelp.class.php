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

class instagramhelp {
	private $_http_host;
    private $_http_referer;
    private $_name;
    
    
	
	public function __construct($data){
		$this->_http_referer = isset($data['http_referer'])?$data['http_referer']:'';

		$this->_name = "fbloginblock";
		
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
	
	public function translite($str){
	
	static $tbl= array(
		' '=>"",'('=>'',')'=>'',','=>'','.'=>'','-'=>'','_'=>'',
		'+'=>'','&'=>'',1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'',9=>''
	);

    return strtr($str, $tbl);
	}
	
	 private function deldigit($str){
    	$arr_out = array('');
		$arr_in = array(0,1,2,3,4,5,6,7,8,9);

		$textout = str_replace($arr_in,$arr_out,$str);
		
		return $textout;
    
    }
    
	
	public function createUser($_data){
				
			$instagram_id = $_data['id'];
			
			//// create new user ////
			$gender = 2;
			$id_default_group = (int)Configuration::get($this->_name.'defaultgroup');
 			
			$firstname = $this->deldigit(pSQL($this->translite($_data['username'])));
			$lastname = $this->deldigit(pSQL($this->translite($_data['username'])));
			#### show popup for instagram customer which not changed email address  #####
			$email = Tools::strtolower($this->translite($_data['username']))."@instagram.com";
			#### show popup for instagram customer which not changed email address  #####

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
			
			
			// insert record into customerXfacebook table
			$sql_exists= 'SELECT `user_id`
					FROM `'._DB_PREFIX_.'instagram_spm`
					WHERE `instagram_id` = '.(int)($instagram_id).' AND id_shop = '.(int)$this->getIdShop().'
					LIMIT 1';
			$result_exists = Db::getInstance()->ExecuteS($sql_exists);
			$user_id = isset($result_exists[0]['user_id'])?$result_exists[0]['user_id']:0;
			if($user_id){
				$sql_del = 'DELETE FROM `'._DB_PREFIX_.'instagram_spm` WHERE `user_id` = '.(int)($user_id).' 
							AND id_shop = '.(int)$this->getIdShop().'';
				Db::getInstance()->Execute($sql_del);
			}
			
			$sql = 'INSERT into `'._DB_PREFIX_.'instagram_spm` SET
						   user_id = '.(int)$insert_id.', 
						   instagram_id = '.(int)$instagram_id.' , 
						   id_shop = '.(int)$this->getIdShop().',
						   username = "'.pSQL($_data['username']).'", 
						   name = "'.pSQL($_data['fullname']).'", 
						   bio = "'.pSQL($_data['bio']).'",
						   website = "'.pSQL($_data['website']).'"
							';
			Db::getInstance()->Execute($sql);
			//// end create new user ///
			
			
			
			// auth customer
			$cookie = $this->context->cookie;
			$customer = new Customer();
	        $authentication = $customer->getByEmail(trim($email), trim($real_passwd));
	        if (!$authentication OR !$customer->id) {
	        	//$status = 'error';
				echo 'authentication failed!';
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
    													  'type'=>7,
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
			
			
	}
	
	public function loginUser($_data){

			
			$instagram_id = $_data['id'];
			$cookie = $this->context->cookie;
			// authentication
			
			if(version_compare(_PS_VERSION_, '1.5', '>')){
			$sql = '
	        	SELECT c.* FROM `'._DB_PREFIX_   .'customer` c 
	        		left join '._DB_PREFIX_.'instagram_spm tc
	        		on(tc.user_id = c.id_customer)
		        WHERE c.`active` = 1 AND tc.`instagram_id` = '.(int)($instagram_id).'  AND c.id_shop = '.(int)$this->getIdShop().'
		        AND tc.id_shop = '.(int)$this->getIdShop().'
		        AND c.`deleted` = 0 '.(defined('_MYSQL_ENGINE_')?'AND c.`is_guest` = 0':'').'
		        ';
			} else {
				$sql = '
	        	SELECT c.* FROM `'._DB_PREFIX_   .'customer` c 
	        		left join '._DB_PREFIX_.'instagram_spm tc
	        		on(tc.user_id = c.id_customer)
		        WHERE c.`active` = 1 AND tc.`instagram_id` = '.(int)($instagram_id).'
		        AND c.`deleted` = 0 '.(defined('_MYSQL_ENGINE_')?'AND c.`is_guest` = 0':'').'
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
	}
	
	
	
	public function login($data){
		
		$id = isset($data['id'])?$data['id']:0;
			  
	  	if ($id){
			  		
			  	$result = $this->checkExist($id);
			         
			  	$result_dublicate = $this->checkForDublicate(array('id_customer'=>$result));
			    $exists_mail = $result_dublicate['exists_mail'];
			        
			    $auth = 0;
				if($result && $exists_mail){
					$auth = 1;
				}
				
			    if(!$result && $exists_mail){
			    	// insert record into customerXinstagram table
					$sql = 'INSERT into `'._DB_PREFIX_.'instagram_spm` SET
								   user_id = '.(int)$result_dublicate['user_id'].', 
								   instagram_id = '.(int)$id.', 
								   id_shop = '.(int)$this->getIdShop().'';
					Db::getInstance()->Execute($sql);
					$auth = 1;
			
				}
				
				  if ($auth == 0){
			         $this->createUser($data);
			      } else {
			          $this->loginUser($data);
			      }
			      
			      
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
	}
	
	 public function checkExist($id){
	 	
	 	$result = Db::getInstance()->ExecuteS('SELECT `user_id`
					FROM `'._DB_PREFIX_.'instagram_spm`
					WHERE `instagram_id` = '.(int)($id).' AND id_shop = '.(int)$this->getIdShop().'
					LIMIT 1');
			$customer_id = isset($result[0]['user_id'])?(int)$result[0]['user_id']:0;
		return $customer_id;
	 }
	
	
public function checkForDublicate($data){
		//chek for dublicate
	
			if(version_compare(_PS_VERSION_, '1.5', '>')){
				$sql = '
		        	SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        WHERE `active` = 1 AND `id_customer` = \''.(int)($data['id_customer']).'\' 
			        AND id_shop = '.(int)$this->getIdShop().' 
			        AND `deleted` = 0 '.(@defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
			        ';
			} else {
				$sql = '
		        	SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        WHERE `active` = 1 AND `id_customer` = \''.(int)($data['id_customer']).'\'  
			        AND `deleted` = 0 '.(@defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
			        ';
			}
			$result_exists_mail = Db::getInstance()->GetRow($sql);
			if($result_exists_mail)
				return array('exists_mail' => 1, 'user_id' => $result_exists_mail['id_customer']);
			else
				return array('exists_mail' => 0, 'user_id' =>0);
		
	}
	
private function getIdShop(){
    	if(version_compare(_PS_VERSION_, '1.5', '>')){
        	$id_shop = Context::getContext()->shop->id;
        } else {
        	$id_shop = 0;
        }
        return $id_shop;
    }
    
    
    private function _redirect($url){
    
          Tools::redirect($url);
          
    }
}