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

class wordpresshelp extends Module{
	
	private $_http_host;
	private $_name;

    private $_social_type = 52;
	
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
	

    public function wordpressLogin($_data){
    	 
    	
    	$name_module = $this->_name;
    	
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}  	
    	
    	include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
    	$obj_module = new $name_module();
    	$redirect_uri = $obj_module->getRedirectURL(array('typelogin'=>'wordpress','is_settings'=>1));
    	
    	$wci = Configuration::get($name_module.'wci');
    	$wci = trim($wci);
    	$wcs = Configuration::get($name_module.'wcs');
    	$wcs = trim($wcs);
    	
    	
    	$code = Tools::getValue('code');
    	 
    	if($code)
    	{
    		$curl = curl_init( 'https://public-api.wordpress.com/oauth2/token' );
    		curl_setopt( $curl, CURLOPT_POST, true );
    		curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
    				'client_id' => $wci,
    				'redirect_uri' => $redirect_uri,
    				'client_secret' => $wcs,
    				'code' => $code, // The code from the previous request
    				'grant_type' => 'authorization_code'
    		) );
    		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
    		$auth = curl_exec( $curl );
    		
    		$secret = Tools::jsonDecode($auth);
    		 
    		$access_key = $secret->access_token;
    		
    		
    		$options  = array (
    				'http' =>
    				array (
    						'ignore_errors' => true,
    						'header' =>
    						array (
    								0 => 'authorization: Bearer '.$access_key,
    						),
    				),
    		);
    		
    		$context  = stream_context_create( $options );
    		$response = Tools::file_get_contents(
    				'https://public-api.wordpress.com/rest/v1/me/',
    				false,
    				$context
    		);
    		$response = Tools::jsonDecode( $response );
    		
    		$first_name = $response->username;
    		$last_name =  $response->username;
    		 
    		 
    		$email_address = $response->email;


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
                    'http_referer_custom'=>$http_referer,
                    'type'=>$this->_social_type,
                )
            );
            ## add new functional for auth and create user ##

    		

    		
    	} else {
    		$redirect_uri = "https://public-api.wordpress.com/oauth2/authorize?client_id=".$wci."&redirect_uri=".$redirect_uri."&response_type=code";
    		redirect_custom_fbloginblock($redirect_uri);
    		exit;
    	}
    	
    }
}