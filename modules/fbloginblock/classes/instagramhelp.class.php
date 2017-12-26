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

class instagramhelp {
	private $_http_host;
    private $_http_referer;
    private $_name;

    private $_social_type = 7;
    
    
	public function __construct($data = null){
		$this->_http_referer = isset($data['http_referer'])?$data['http_referer']:'';

		$this->_name = "fbloginblock";
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$this->_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}


        if (version_compare(_PS_VERSION_, '1.7', '<')){
            require_once(_PS_MODULE_DIR_.$this->_name.'/backward_compatibility/backward.php');
        }

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_ . $this->_name . '/backward_compatibility/backward_functions.php');
        }
		
	
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}
	
	public function translite($str){
	
		$str  = str_replace(array('"','№','\\','%',';',"®","'",'"','`','?','!','.','=',':','&','+',',','’', ')', '(', '$', '{', '}','/', "\\",'#','\'','#174;','#39;','#160;','#246;','™','&amp;','amp;'), array(''), $str );
		
	$arrru = array ("А","а","Б","б","В","в","Г","г","Д","д","Е","е","Ё","ё","Ж","ж","З","з","И","и","Й","й","К","к","Л","л","М","м","Н","н", "О","о","П","п","Р","р","С","с","Т","т","У","у","Ф","ф","Х","х","Ц","ц","Ч","ч","Ш","ш","Щ","щ","Ъ","ъ","Ы","ы","Ь", "ь","Э","э","Ю","ю","Я","я",
    " ","-",",","«","»","+","/","(",")",".");
		
    	$arren = array ("a","a","b","b","v","v","g","g","d","d","e","e","e","e","zh","zh","z","z","i","i","y","y","k","k","l","l","m","m","n","n", "o","o","p","p","r","r","s","s","t","t","u","u","ph","f","h","h","c","c","ch","ch","sh","sh","sh","sh","","","i","i","","","e", "e","yu","yu","ya","ya",
    			"-","-","","","","","","","","","");
    
    	$textout = '';
    	$textout = str_replace($arrru,$arren,$str);

        $textout = str_replace(array('--','-','_'),array(''),$textout);
    	return Tools::strtolower($textout);
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
			$gender = 1;
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
	            include_once(_PS_MODULE_DIR_.$this->_name.'/classes/statisticshelp.class.php');
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
	        
	        ### add customer to statistics ###
	        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/statisticshelp.class.php');
	        $obj_help = new statisticshelp();
	        $obj_help->addCustomerToStatistics(
	        		array('customer_id'=>$customer->id,
	        				'email'=>$customer->email,
	        				'id_shop'=>$this->getIdShop(),
	        				'type'=>7,
	        		)
	        );
	        ### add customer to statistics ###
	        
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

                ## add new functional for auth and create user ##
                $first_name = $this->deldigit(pSQL($this->translite($data['username'])));
                $last_name = $this->deldigit(pSQL($this->translite($data['username'])));
                if($auth == 1){
                    $email = $result_dublicate['email'];
                } else {
                    $email = null;
                }




                $username = isset($data['username'])?$data['username']:'';
                $fullname = isset($data['fullname'])?$data['fullname']:'';
                $bio = isset($data['bio'])?$data['bio']:'';
                $website = isset($data['website'])?$data['website']:'';

                $data_profile = array(
                    'email'=>$email,
                    'first_name'=>$first_name,
                    'last_name'=>$last_name,

                    'username'=>$username,
                    'fullname'=>$fullname,
                    'bio'=>$bio,
                    'website'=>$website,

                );

                include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
                $userhelp = new userhelp();
                $userhelp->userLog(
                    array(
                        'data_profile'=>$data_profile,
                        'http_referer_custom'=>$this->_http_referer,
                        'instagram_id'=>$id,
                        'type'=>$this->_social_type,
                        'auth'=>$auth,
                    )
                );
                ## add new functional for auth and create user ##


			       
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
			        WHERE   `id_customer` = \''.(int)($data['id_customer']).'\'
			        AND id_shop = '.(int)$this->getIdShop().' 
			        AND `deleted` = 0 '.(@defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
			        ';
			} else {
				$sql = '
		        	SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        WHERE  `id_customer` = \''.(int)($data['id_customer']).'\'
			        AND `deleted` = 0 '.(@defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
			        ';
			}
			$result_exists_mail = Db::getInstance()->GetRow($sql);

            ## if customer disabled ##
            if(!empty($result_exists_mail) && $result_exists_mail['active'] == 0){
                include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
                $obj = new $this->_name();
                $data_tr = $obj->translateCustom();
                echo $data_tr['disabled'];exit;
            }
            ## if customer disabled ##

            $email = isset($result_exists_mail['email'])?$result_exists_mail['email']:null;

			if($result_exists_mail)
				return array('exists_mail' => 1,  'email'=>$email, 'user_id' => $result_exists_mail['id_customer']);
			else
				return array('exists_mail' => 0,  'email'=>$email, 'user_id' =>0);
		
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
    
          redirect_custom_fbloginblock($url);
          
    }
    
    
    public function instagramLogin($_data){
    	
    	$name_module = $this->_name;
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	
    	
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}
    	
    	
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/instagram/instagram.class.php';
    	 
    	 
    	
    	
    	$client_id = Configuration::get($name_module.'ici');
    	$client_id = trim($client_id);
    	$client_secret = Configuration::get($name_module.'ics');
    	$client_secret = trim($client_secret);
    	$callback = Configuration::get($name_module.'iru');
    	$callback = trim($callback);
    	
    	$instagram = new Instagram(array(
    			'apiKey'      => $client_id,
    			'apiSecret'   => $client_secret,
    			'apiCallback' => $callback
    	));
    	
    	
    	// Receive OAuth code parameter
    	$code = Tools::getValue('code');
    	
    	
    	
    	// Check whether the user has granted access
    	if (true === isset($code)) {
    	
    		// Receive OAuth token object
    		$data = $instagram->getOAuthToken($code);
    		// Take a look at the API response
    		 
    		if(empty($data->user->username))
    		{
    				
    			redirect_custom_fbloginblock($instagram->getLoginUrl());exit;
    	
    		}
    		else
    		{
    			$_SESSION['userdetails']=$data;
    				
    			$username = $data->user->username;
    			$fullname=$data->user->full_name;
    			$bio=$data->user->bio;
    			$website=$data->user->website;
    			$id=$data->user->id;
    			$token=$data->access_token;
    	
    			$data_instagram = array('username'=>$username,
    					'fullname'=>$fullname,
    					'bio'=>$bio,
    					'website'=>$website,
    					'id'=>$id,
    					'token'=>$token,
    			);
    				
    				
    			$this->login($data_instagram);
    	
    		}
    	
    	}
    	else
    	{
    		// Check whether an error occurred
    		if (Tools::getValue('error'))
    		{
    			echo 'An error occurred: '.Tools::getValue('error_description'); exit;
    		}
    	
    	}
    	
    	
    }

    public function insertCustomerXInstagram($data){

        $instagram_id = $data['instagram_id'];
        $insert_id = $data['insert_id'];
        $id_shop = $data['id_shop'];

        $username = isset($data['username'])?$data['username']:'';
        $fullname = isset($data['fullname'])?$data['fullname']:'';
        $bio = isset($data['bio'])?$data['bio']:'';
        $website = isset($data['website'])?$data['website']:'';

        $sql_exists= 'SELECT `user_id`
					FROM `'._DB_PREFIX_.'instagram_spm`
					WHERE `instagram_id` = '.(int)($instagram_id).' AND id_shop = '.(int)$id_shop.'
					LIMIT 1';
        $result_exists = Db::getInstance()->ExecuteS($sql_exists);
        $user_id = isset($result_exists[0]['user_id'])?$result_exists[0]['user_id']:0;
        if($user_id){
            $sql_del = 'DELETE FROM `'._DB_PREFIX_.'instagram_spm` WHERE `user_id` = '.(int)($user_id).'
							AND id_shop = '.(int)$id_shop.'';
            Db::getInstance()->Execute($sql_del);
        }

        $sql = 'INSERT into `'._DB_PREFIX_.'instagram_spm` SET
						   user_id = '.(int)$insert_id.',
						   instagram_id = '.(int)$instagram_id.' ,
						   id_shop = '.(int)$id_shop.',
						   username = "'.pSQL($username).'",
						   `name` = "'.pSQL($fullname).'",
						   bio = "'.pSQL($bio).'",
						   website = "'.pSQL($website).'"
							';
        Db::getInstance()->Execute($sql);
    }
    
}