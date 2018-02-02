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

class userhelp extends Module{
	
	private $_http_host;
	private $_name;
    private $_customer_linked_social_account = 'customer_linked_social_account';
	
	public function __construct(){
		
		$this->_name = 'fbloginblock';
		
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

        $cookie_context = $this->context->cookie;

        $social_type = isset($_data['type'])?$_data['type']:0;
        $auth = isset($_data['auth'])?$_data['auth']:0;

        $data_customer = $_data['data_profile'];
        $_email = $data_customer['email'];


        ## link account ##

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $cookie_clsa = new Cookie('clsa');
            $linksocialaccount = $cookie_clsa->linksocialaccount;
            if($linksocialaccount == 1)
                unset($cookie_clsa);
        } else {
            $value_linksocialaccount = getcookie_fbloginblock(array('type'=>'linksocialaccount'));
            $linksocialaccount = $value_linksocialaccount['value'];
            setcookie_fbloginblock(array('type'=>'linksocialaccount','value'=>null));
        }


        $email_for_check = null;
        $is_logged = isset($cookie_context->id_customer)?$cookie_context->id_customer:0;
        ### customer_linked_social_account only if customer is logged in ###
        if($linksocialaccount == 1 && $is_logged){



            ## handle same social connects ##
            switch($social_type){
                case 2:
                    //twitter
                    $email_for_check= $_data['twitter_id'];
                    break;
                case 7:
                    //instagram
                    $email_for_check= $_data['instagram_id'];
                    break;
                case 54:
                    //pinterest
                    $email_for_check= $_data['pinterest_id'];
                    break;
                case 53:
                    //tumblr
                    $email_for_check= $_data['tumblr_id'];
                    break;
                default:
                    $email_for_check = $_email;
                break;
            }
            ## handle same social connects ##

            $_data_linked_social_account_customer = $this->checkExistLinkedAccount(array('email'=>$email_for_check));
            $_email_linked_social_account_customer = $_data_linked_social_account_customer['email'];


            if(!$_email_linked_social_account_customer
                || $email_for_check == $cookie_context->email //if customer link social connect, with help customer create your account at store
            ){
                $id_customer_linked = $cookie_context->id_customer;
                $this->addRecordInCustomerLinkedSocialAccountTable(array('type'=>$social_type,'id_customer'=>$id_customer_linked,'email'=>$email_for_check));

                include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
                $obj = new $this->_name();
                $data_seo = $obj->getSEOURLs();
                $url_link_account = $data_seo['url_link_account'];


                echo '<script>
						window.opener.location.href = \''  . $url_link_account . '\';
						window.opener.focus();
						window.close();
				</script>';

                exit;
            }
        }
        ### customer_linked_social_account only if customer is logged in ###
        ## link account ##






            ### fix when api do not provide email ###

            // exclude facebook connect
            if ($social_type != 1) {

                $is_false_generated = 0;
                if (empty($_email)) {
                    srand((double)microtime() * 1000000);
                    $em = Tools::substr(uniqid(rand()), 0, 12);
                    $_email = $em . '@api-not-provide-email-' . $social_type . '.com';
                    $is_false_generated = 1;

                    $_data['data_profile']['email'] = $_email;
                }

                $_data['is_false_generated'] = $is_false_generated;
            }
            // exclude facebook connect

            ### fix when api do not provide email ###


            ## check on the existence customer, for order to just logged in ##
            $_data_linked_social_account_customer = $this->checkExistLinkedAccount(array('email' => (($email_for_check)?$email_for_check:$_email)) );
            $_email_linked_social_account_customer = $_data_linked_social_account_customer['email'];
            if ($_email_linked_social_account_customer) {
                $_email = $_email_linked_social_account_customer;
            }
            ## check on the existence customer, for order to just logged in ##


            $_data_user = $this->checkExist($_email);
            $_customer_id = (int)$_data_user['customer_id'];
            $_result = $_data_user['result'];





            if (!$_customer_id
                && $auth == 0 // parameter auth only for facebook, twitter, instagram connect
            ) {
                $_data['cookie'] = $cookie_context;
                $this->createUser($_data);
            } else {
                $this->loginUser(array('type' => $social_type, 'customer_id' => $_customer_id, 'result' => $_result, 'data' => $_data, 'cookie'=>$cookie_context));
            }


            ### redirect ###

            $http_referer = isset($_data['http_referer_custom']) ? $_data['http_referer_custom'] : '';

            if (Tools::strlen($http_referer) == 0 && version_compare(_PS_VERSION_, '1.5', '>')) {
                $cookie = new Cookie('ref');
                $http_referer = $cookie->http_referer_custom;
                $cookie->http_referer_custom = '';
            }


            require_once(_PS_MODULE_DIR_.$this->_name . '/fbloginblock.php');
            $obj = new fbloginblock();
            $data_order_page = $obj->getOrderPage(array('http_referrer' => $http_referer));
            $uri = $data_order_page['uri'];

            if ($social_type != 24 // EXCLUDE amazon connect
            ) {


                if ((int)Configuration::get('redirpage') == 1 || $data_order_page['order_page'] == 1
                    //|| (!Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Configuration::get('PS_SSL_ENABLED'))
                ) {

                    if($data_order_page['order_page'] == 1){
                        $location_href = $data_order_page['http_referrer_orig'];
                    } else {
                        $location_href = $this->_http_host . $uri;
                    }
                    echo '<script>
						window.opener.location.href = \'' . $location_href . '\';
						window.opener.focus();
						window.close();
				        </script>';
                } else {

                    if(Tools::strlen($data_order_page['http_referrer_orig'])>0
                        && (!Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Configuration::get('PS_SSL_ENABLED'))
                      ){

                        echo '<script>
						window.opener.location.href = \'' . $data_order_page['http_referrer_orig'] . '\';
						window.opener.focus();
						window.close();
				        </script>';
                    } else {

                        echo '<script>window.opener.location.reload(true);window.opener.focus();window.close();</script>';
                    }
                }

            } else {

                // only for amazon connect

                if ((int)Configuration::get('redirpage') == 1 || $data_order_page['order_page'] == 1
                    //|| (!Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Configuration::get('PS_SSL_ENABLED'))
                ) {
                    if ($data_order_page['order_page'] == 1) {
                        $amazon_url = $data_order_page['http_referrer_orig'];

                    } else {
                        $amazon_url = $this->_http_host . $uri;
                    }


                } else {

                    if(Tools::strlen($data_order_page['http_referrer_orig'])>0
                        && (!Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Configuration::get('PS_SSL_ENABLED'))
                    ) {

                        $amazon_url = $data_order_page['http_referrer_orig'];

                    } else {

                        $cookie = new Cookie('refamazon');
                        $order_page_amazon = $cookie->order_page_amazon;

                        if($order_page_amazon){
                            $link = new Link();
                            $id_lang = (int)$cookie_context->id_lang;
                            $amazon_url = $link->getPageLink("order", true, $id_lang,null,false,$this->getIdShop());


                        } else {

                            $req_uri_amazon = $cookie->req_uri_amazon;
                            $amazon_url = $req_uri_amazon;
                            header('Location: '.$amazon_url);
                            exit;

                        }
                    }



                }

                // only for amazon connect
                return $amazon_url;
            }

            ### redirect ###


    }
    
private function loginUser($_data){

        $_customer_id = $_data['customer_id'];
        $result = $_data['result'];
        $social_type = $_data['type'];
        $data = $_data['data'];

    	$cookie = $_data['cookie'];
		// authentication
		if ($result){
		    $customer = new Customer();
		    
		    $customer->id = $_customer_id;
		    unset($result['id_customer']);
		    foreach ($result AS $key => $value)
		       if (key_exists($key, $customer))
		             $customer->{$key} = $value;
	     }

        $this->authenticationUser(array('cookie'=>$cookie,'customer'=>$customer,'social_type'=>$social_type,'data'=>$data));

    }
    
    
 private function createUser($_data){

 			//// create new user ////



            $data_profile = $_data['data_profile'];

            $social_type = $_data['type'];

			$gender = isset($data_profile['gender'])?$data_profile['gender']:1;

            $is_false_generated = isset($_data['is_false_generated'])?$_data['is_false_generated']:0;
			
 			$id_default_group = (int)Configuration::get($this->_name.'defaultgroup');

            $first_name = $data_profile['first_name'];
            $last_name = $data_profile['last_name'];
			$firstname = $this->deldigit(pSQL($first_name));
			$lastname = $this->deldigit(pSQL($last_name));
            $email = $data_profile['email'];


             if(Tools::strlen($first_name)==0 || Tools::strlen($lastname) == 0){
                 echo 'Empty First Name or Last Name!';
                 exit;
             }

             $birthday = '';
             $birthday_data = isset($data_profile['birthday'])?$data_profile['birthday']:'';

             if(Tools::strlen($birthday_data)>0){
                 $birthday = strtotime($data_profile['birthday']);
                 $birthday = date("Ymd",$birthday);
                 $birthday = 'birthday = \''.pSQL($birthday).'\',';
             }

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
				
			//$id_shop_group = (int)Configuration::get($this->_name.'defaultgroup');
			//	if(!$id_shop_group)
			$id_shop_group = Context::getContext()->shop->id_shop_group;

                /*$sql = 'insert into `'._DB_PREFIX_.'customer` SET
						id_shop = '.(int)$this->getIdShop().', id_shop_group = '.(int)$id_shop_group.',
						id_gender = '.(int)$gender.', id_default_group = '.(int)$id_default_group.',
						firstname = \''.pSQL($firstname).'\', lastname = \''.pSQL($lastname).'\',
						email = \''.pSQL($email).'\', passwd = \''.pSQL($passwd).'\',
						'.$birthday.'
						last_passwd_gen = \''.pSQL($last_passwd_gen).'\',
						newsletter = 1, newsletter_date_add = \''.pSQL($date_upd).'\',
						secure_key = \''.pSQL($secure_key).'\', active = '.(int)$active.',
						date_add = \''.pSQL($date_add).'\', date_upd = \''.pSQL($date_upd).'\' ';*/

			$sql = 'insert into `'._DB_PREFIX_.'customer` SET
						id_shop = '.(int)$this->getIdShop().', id_shop_group = '.(int)$id_shop_group.',
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
			$id_group = (int)Configuration::get($this->_name.'defaultgroup');
			
			$sql = 'INSERT into `'._DB_PREFIX_.'customer_group` SET 
						   id_customer = '.(int)$insert_id.', id_group = '.(int)$id_group.' ';
			Db::getInstance()->Execute($sql);


            ## handle same social connects ##
            switch($social_type){
                case 1:
                    //facebook
                    $me_facebook_id= $_data['me_facebook_id'];
                    include_once _PS_MODULE_DIR_.$this->_name.'/classes/facebookhelp.class.php';
                    $facebookhelp = new facebookhelp();
                    $facebookhelp->insertCustomerXFacebook(array('insert_id'=>$insert_id,'me_facebook_id'=>$me_facebook_id,'id_shop'=>$this->getIdShop()));
                break;
                case 2:
                    //twitter
                    $twitter_id= $_data['twitter_id'];
                    include_once _PS_MODULE_DIR_.$this->_name.'/classes/twitter.class.php';
                    $twitterhelp = new twitter();
                    $twitterhelp->insertCustomerXTwitter(array('insert_id'=>$insert_id,'twitter_id'=>$twitter_id,'id_shop'=>$this->getIdShop()));
                break;
                case 7:
                    //instagram
                    $instagram_id= $_data['instagram_id'];
                    include_once _PS_MODULE_DIR_.$this->_name.'/classes/instagramhelp.class.php';
                    $instagramhelp = new instagramhelp();
                    $instagramhelp->insertCustomerXInstagram(array('insert_id'=>$insert_id,'instagram_id'=>$instagram_id,'id_shop'=>$this->getIdShop()));
                break;
                case 8:
                    //paypal
                    include_once _PS_MODULE_DIR_.$this->_name.'/classes/paypalhelp.class.php';
                    $paypalhelp = new paypalhelp();
                    $paypalhelp->addAddress(array('id_customer'=>$insert_id,'data'=>$_data['data_paypal']));
                break;
                case 54:
                    //pinterest
                    $pinterest_id= $_data['pinterest_id'];
                    include_once _PS_MODULE_DIR_.$this->_name.'/classes/pinteresthelp.class.php';
                    $pinteresthelp = new pinteresthelp();
                    $pinteresthelp->insertCustomerXPinterest(array('insert_id'=>$insert_id,'pinterest_id'=>$pinterest_id,'id_shop'=>$this->getIdShop()));
                break;
                case 53:
                    //tumblr
                    $tumblr_id= $_data['tumblr_id'];
                    include_once _PS_MODULE_DIR_.$this->_name.'/classes/tumblrhelp.class.php';
                    $tumblrhelp = new tumblrhelp();
                    $tumblrhelp->insertCustomerXTumblr(array('insert_id'=>$insert_id,'tumblr_id'=>$tumblr_id,'id_shop'=>$this->getIdShop()));
                break;
            }
            ## handle same social connects ##
			
		
			// auth customer
			$cookie = $_data['cookie'];
			//$customer = new Customer();
	        //$authentication = $customer->getByEmail(trim($email), trim($real_passwd));




	        if (
                //!$authentication OR !$customer->id
                !$insert_id
                ) {
	        	echo 'Authentication failed!'; exit;
	        }
	        else
	        {
                $customer = new Customer((int)$insert_id);
                $this->authenticationUser(array('cookie'=>$cookie,'customer'=>$customer,'social_type'=>$social_type,'data'=>$_data));


	        }
			
			if(!$is_false_generated) {
                $cookie = $this->context->cookie;


                require_once(_PS_MODULE_DIR_.$this->_name . '/fbloginblock.php');
                $obj = new fbloginblock();
                $data_translate = $obj->translateCustom();

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


                    Mail::Send($id_lang_mail, 'account17', $data_translate['subject'],
                        array('{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{email}' => $customer->email,
                            '{passwd}' => $real_passwd),
                        $customer->email, $customer->firstname . ' ' . $customer->lastname, NULL, NULL,
                        NULL, NULL, dirname(__FILE__) . '/../mails/');

                }else {

                    Mail::Send((int)($cookie->id_lang), 'account', $data_translate['subject'],
                        array('{firstname}' => $customer->firstname,
                            '{lastname}' => $customer->lastname,
                            '{email}' => $customer->email,
                            '{passwd}' => $real_passwd),
                        $customer->email,
                        $customer->firstname . ' ' . $customer->lastname);
                }
            }

    }


    public function authenticationUser($data){

        $cookie = $data['cookie'];
        $customer = $data['customer'];
        $social_type = $data['social_type'];
        $data = $data['data'];



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
                'type'=>$social_type,
            )
        );
        ### add customer to statistics ###


        ### add record in table customer_linked_social_account ###

        ## handle same social connects ##
        switch($social_type){
            case 2:
                //twitter
                $email_for_check= $data['twitter_id'];
                break;
            case 7:
                //instagram
                $email_for_check= $data['instagram_id'];
                break;
            case 54:
                //pinterest
                $email_for_check= $data['pinterest_id'];
                break;
            case 53:
                //tumblr
                $email_for_check= $data['tumblr_id'];
                break;
            default:
                $email_for_check = $customer->email;
                break;
        }
        ## handle same social connects ##

        $this->addRecordInCustomerLinkedSocialAccountTable(array('type'=>$social_type,'id_customer'=>$customer->id,'email'=>$email_for_check));
        ### add record in table customer_linked_social_account ###


        if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) OR Cart::getNbProducts($cookie->id_cart) == 0))
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
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.(int)$this->getIdShop().'
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


    private function checkExistLinkedAccount($data){

        $email = $data['email'];


            $sql = 'SELECT id_customer FROM `'._DB_PREFIX_.$this->_customer_linked_social_account.'`
		        	WHERE  `email` = \''.pSQL($email).'\'
		        	AND `id_shop` = '.(int)$this->getIdShop().'
		        	';


        $result = Db::getInstance()->GetRow($sql);

        $id_customer = isset($result['id_customer'])?$result['id_customer']:0;



        if(version_compare(_PS_VERSION_, '1.5', '>')){
            $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
		        	WHERE  `id_customer` = \''.(int)($id_customer).'\'
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.(int)$this->getIdShop().'
		        	';
        } else {
            $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
		        	WHERE  `id_customer` = \''.(int)($id_customer).'\'
		        	AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'
		        	';
        }

        $result = Db::getInstance()->GetRow($sql);

        $email = isset($result['email'])?$result['email']:null;




        return array('id_customer' => $id_customer, 'email' => $email);
    }


    public function addRecordInCustomerLinkedSocialAccountTable($data){
        $type = $data['type'];



        $id_customer = $data['id_customer'];
        $email = $data['email'];


        ## add record in table customer_linked_social_account ##

        $sql_is_exist = 'select COUNT(*) as count from `'. _DB_PREFIX_ . $this->_customer_linked_social_account. '`
        					where id_shop = \'' . (int)$this->getIdShop() . '\' AND type = \'' . (int)$type . '\'  AND id_customer = \'' . (int)$id_customer . '\'';

        $data_is_exist = Db::getInstance()->getRow($sql_is_exist);

        if($data_is_exist['count'] == 0) {


           ## fixed bug, when user change email, and user again try register on the site ##
           $sql_delete = 'delete from `' . _DB_PREFIX_ . $this->_customer_linked_social_account . '` where
							    id_shop = \'' . (int)$this->getIdShop() . '\' AND type = \'' . (int)$type . '\'  AND id_customer = \'' . (int)$id_customer . '\' ';
           Db::getInstance()->Execute($sql_delete);
           ## fixed bug, when user change email, and user again try register on the site ##

           $sql = 'insert into `' . _DB_PREFIX_ . $this->_customer_linked_social_account . '`
                    set
                    id_customer = \'' . (int)($id_customer) . '\',
                    `id_shop` = ' . (int)$this->getIdShop() . ',
                    `type` = ' . (int)$type . ',
                    `email` = \'' . pSQL($email) . '\'
                    ';
           Db::getInstance()->Execute($sql);
        }

        ## add record in table customer_linked_social_account ##
    }


    public function getLinkedAccountsForCustomer($data){

        $id_customer = $data['id_customer'];

        $sql = 'select * from `' . _DB_PREFIX_ . $this->_customer_linked_social_account . '`
                                where id_shop = \'' . (int)$this->getIdShop() . '\'  AND id_customer = \'' . (int)$id_customer . '\' ';
        $data_get = Db::getInstance()->ExecuteS($sql);

        return array('data'=>$data_get);


    }


    public function deleteLinkedAccount($data){

        $type= $data['type'];
        $id_customer = $data['id_customer'];


        ## fixed bug, when user change email, and user again try register on the site ##
        $sql_delete = 'delete from `' . _DB_PREFIX_ . $this->_customer_linked_social_account . '` where
							    id_shop = \'' . (int)$this->getIdShop() . '\' AND type = \'' . (int)$type . '\'  AND id_customer = \'' . (int)$id_customer . '\' ';
        Db::getInstance()->Execute($sql_delete);
        ## fixed bug, when user change email, and user again try register on the site ##

        include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
        $obj = new $this->_name();
        $data_seo = $obj->getSEOURLs();
        $url_unlink_account = $data_seo['url_unlink_account'];


        Tools::redirect($url_unlink_account);
        exit;
    }





}