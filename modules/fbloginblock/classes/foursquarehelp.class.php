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

class foursquarehelp extends Module{
	
	private $_http_host;
	private $_name;
    private $_social_type = 20;
	
	public function __construct(){
		
		$this->_name = 'fbloginblock';
		
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
	

    
    public function foursquareLogin($_data){
    	
    	
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	
    	$name_module = $this->_name;
    	
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}


        include_once(_PS_MODULE_DIR_.$this->_name.'/lib/foursquare/FoursquareAPI.class.php');
    	
    	
    	$client_id = Configuration::get($name_module.'fsci');
    	$client_id = trim($client_id);
    	$client_secret = Configuration::get($name_module.'fscs');
    	$client_secret = trim($client_secret);
    	$redirect_uri = Configuration::get($name_module.'fsru');
    	$redirect_uri = trim($redirect_uri);
    	
    	if(Tools::strlen($client_id)==0 || Tools::strlen($client_secret)==0 || Tools::strlen($redirect_uri)==0){
    		echo "Error: Please fill Foursquare Client Id, Foursquare Client Secret, Foursquare Callback URL in the module settings!";
    		exit;
    	}
    	
    	
    	$foursquare = new FoursquareAPI($client_id,$client_secret);
    	
    	// Getting request  token
    	$code = Tools::getValue('code');
    	if($code){
    		$auth_token = $foursquare->GetToken($code, $redirect_uri);
    	
    	
    		// Load the Foursquare API library
    		$foursquare_current = new FoursquareAPI();
    		$foursquare_current->SetAccessToken($auth_token);
    	
    		// Perform a request to getting user details
    		$response = $foursquare_current->GetPrivate("users/self");
    		$user_data = Tools::jsonDecode($response);
    	
    		//print_r($user_data);
    		/* echo '<b>Userid:</b>   '. $user_data->response->user->id.'<br>';
    		 echo '<b>FirstName:</b>  '.$user_data->response->user->firstName.'<br>';
    		echo '<b>LastName:</b>   '.$user_data->response->user->lastName.'<br>';
    		echo '<b>Gender:</b>    '.$user_data->response->user->gender.'<br>';
    		echo '<b>Relationship:</b> '.$user_data->response->user->relationship.'<br>';
    		echo '<b>HomeCity:</b>   '.$user_data->response->user->homeCity.'<br>';
    		echo '<b>Email:</b>    '.$user_data->response->user->contact->email.'<br>';
    		echo '<b>Facebook Id:</b>    '.$user_data->response->user->contact->facebook.'<br>'; */
    	
    		$first_name = $user_data->response->user->firstName;
    		$last_name = $user_data->response->user->lastName;
    		$email_address = $user_data->response->user->contact->email;
    		$gender = $user_data->response->user->gender;
    	

            ## add new functional for auth and create user ##
            $data_profile = array(
                'email'=>$email_address,
                'first_name'=>$first_name,
                'last_name'=>$last_name,
                'gender'=>$gender,

            );

            include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
            $userhelp = new userhelp();
            $userhelp->userLog(
                array(
                    'data_profile'=>$data_profile,
                    'http_referer_custom'=>$http_referer,
                    'type'=>$this->_social_type,
                )
            );
            ## add new functional for auth and create user ##

    	
    		exit;
    	} else {
    		redirect_custom_fbloginblock($foursquare->AuthenticationLink($redirect_uri));
    		exit;
    	}
    	
    }
    
}