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

class paypalhelp extends Module{
	
	private $_http_host;
	private $_name;
    private $_social_type = 8;
	
	public function __construct(){
		$this->_name =  'fbloginblock'; 
			
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

    private function deldigit($str){
        $arr_out = array('');
        $arr_in = array(0,1,2,3,4,5,6,7,8,9,'_','(',')',',','.','-','+','&');

        $textout = str_replace($arr_in,$arr_out,$str);

        return $textout;

    }
    
    public function addAddress($data){
    		$id_customer = $data['id_customer'];
    	
    		$data = $data['data'];



    		#### add ###
    		$address = new Address();
    		$id_country = Country::getByIso($data->address->country);
    		
    		//$id_country = Country::getIdByName(null,$data->address->region);
    		
    		 $country = new Country($id_country);
    		if ($country->contains_states){
                $states = State::getStatesByIdCountry($id_country);
                
                if (is_array($states) && sizeof($states)){
                	$address->id_state = State::getIdByIso($data->address->region);
                    //$address->id_state = $states[0]['id_state'];
                }                                                
            } 
            
            $address->id_customer = $id_customer;
            $address->firstname = $this->deldigit(pSQL($data->given_name));
            $address->lastname = $this->deldigit(pSQL($data->family_name));                    
            $address->address1 = $data->address->street_address;
            $address->city = $data->address->locality;                    
            $address->postcode = $data->address->postal_code;
            $address->id_country = $id_country;
            
            require_once(_PS_MODULE_DIR_.$this->_name.'/fbloginblock.php');
            $fbloginblock = new fbloginblock();
            $data_translate = $fbloginblock->translateCustom();
            
            $address->alias = $data_translate['billing_address'];
            
            $address->save();
            
            #### add ####
    		
    		
    }
    

    
    public function paypalLogin($_data){
    
    	 
    	 
    	$name_module = $this->_name;
    	 
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	 
    	if (version_compare(_PS_VERSION_, '1.5', '>')){
    		$cookie = new Cookie('ref');
    		$cookie->http_referer_custom = $http_referer;
    	}
    	 
    	 
    	require_once(_PS_MODULE_DIR_.$this->_name.'/lib/paypal/auth.php');
    	
    	
    	$data = array(
    			'key' => Configuration::get($name_module.'clientid'),
    			'secret' => Configuration::get($name_module.'psecret'),
    			'scopes' => 'openid profile email address',
    			'return_url' => Configuration::get($name_module.'pcallback')
    	);
        //echo "<pre>";var_dump($data);exit;

    	$ppaccess = new PayPalAccess($data);
    	
    	//if the code parameter is available, the user has gone through the auth process
    	if (Tools::getValue('code')){
    	
    		$ppaccess->get_access_token();
    	
    		//use access token to get user profile
    		$profile = $ppaccess->get_profile();


            $first_name = $profile->given_name;
            $last_name = $profile->family_name;
            $email_address = $profile->email;

            ## add new functional for auth and create user ##
            $data_profile = array(
                'email'=>$email_address,
                'first_name'=>$first_name,
                'last_name'=>$last_name,


            );

            include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
            $userhelp = new userhelp();
            $userhelp->userLog(
                array(
                    'data_profile'=>$data_profile,
                    'data_paypal'=>$profile,
                    'http_referer_custom'=>$http_referer,
                    'type'=>$this->_social_type,
                )
            );
            ## add new functional for auth and create user ##


    	
    		//log the user out
    		$ppaccess->end_session();
    		//if the code parameter is not available, the user should be pushed to auth
    	} else {
    		//handle case where there was an error during auth (e.g. the user didn't log in / refused permissions / invalid_scope)
    		if (Tools::getValue('error_uri')){
    			echo "Error";
    			//this is the first time the user has come to log in
    		} else {
    			//get auth url and redirect user browser to PayPal to log in
    			$url = $ppaccess->get_auth_url();
    			redirect_custom_fbloginblock($url);
    		}
    	}
    	
    	 
    	 
    	 
    }
}