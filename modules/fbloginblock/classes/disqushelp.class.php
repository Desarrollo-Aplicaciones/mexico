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

class disqushelp extends Module{
	
	private $_http_host;
	private $_name;
    private $_social_type = 22;
	
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
	


    public function disqusLogin($_data){
    	
    	
    	
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	$name_module = $this->_name;
    	
    	if (version_compare(_PS_VERSION_, '1.5', '>')){
    		$cookie = new Cookie('ref');
    		$cookie->http_referer_custom = $http_referer;
    	}

        include_once(_PS_MODULE_DIR_.$this->_name.'/lib/disqus/DisqusAPI.class.php');
    	
    	
    	
    	$PUBLIC_KEY = Configuration::get($name_module.'dci');
    	$PUBLIC_KEY = trim($PUBLIC_KEY);
    	$SECRET_KEY = Configuration::get($name_module.'dcs');
    	$SECRET_KEY = trim($SECRET_KEY);
    	$redirect = Configuration::get($name_module.'dru');
    	$redirect = trim($redirect);
    	
    	if(Tools::strlen($PUBLIC_KEY)==0 || Tools::strlen($SECRET_KEY)==0 || Tools::strlen($redirect)==0){
    		echo "Error: Please fill Disqus Client Id, Disqus Client Secret, Disqus Callback URL in the module settings!";
    		exit;
    	}
    	
    	
    	//This is a all-in-one example of API authentication and making API calls using OAuth
    	//More information on using OAuth with Disqus can be found here: http://disqus.com/api/docs/auth/
    	
    	
    	$endpoint = 'https://disqus.com/api/oauth/2.0/authorize?';
    	$client_id = $PUBLIC_KEY;
    	$scope = 'read,write,email';
    	$response_type = 'code';
    	
    	
    	// Get the code to request access
    	
    	$CODE = Tools::getValue('code');
    	
    	if($CODE){
    	
    		// Build the URL and request the authentication token
    		extract($_POST);
    	
    		$authorize = "authorization_code";
    	
    		$url = 'https://disqus.com/api/oauth/2.0/access_token/?';
    		$fields = array(
    		  'grant_type'=>urlencode($authorize),
    		  'client_id'=>urlencode($PUBLIC_KEY),
    		  'client_secret'=>urlencode($SECRET_KEY),
    		  'redirect_uri'=>urlencode($redirect),
    		  'code'=>urlencode($CODE)
    		);
    	
    		
    		$fields_string = '';
    		//url-ify the data for the POST
    		foreach($fields as $key=>$value) {
    			$fields_string .= $key.'='.$value.'&';
    		}
    		rtrim($fields_string, "&");
    	
    		//open connection
    		$ch = curl_init();
    	
    		//set the url, number of POST vars, POST data
    		curl_setopt($ch,CURLOPT_URL,$url);
    		curl_setopt($ch,CURLOPT_POST,count($fields));
    		curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
    		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    	
    		//execute post
    		$data = curl_exec($ch);
    	
    		//close connection
    		curl_close($ch);
    	
    		//turn the string into a object
    		$auth_results = Tools::jsonDecode($data);
    	
    	
    		$access_token = $auth_results->access_token;
    	
    		//Setting the correct endpoint
    		$cases_endpoint = 'https://disqus.com/api/3.0/users/details.json?';
    	
    		//Calling the function to getData
    	
    		$disqusapi = new DisqusAPI();
    	
    		$user_details = $disqusapi->getData($cases_endpoint, $SECRET_KEY, $access_token);
    	
    	
    		$email_address = $user_details->response->email;
    		$first_name = $user_details->response->username;
    		$last_name = $user_details->response->username;
    	
    	

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
    		$auth_url = $endpoint.'&client_id='.$client_id.'&scope='.$scope.'&response_type='.$response_type.'&redirect_uri='.$redirect;
    		redirect_custom_fbloginblock($auth_url);
    		exit;
    	}
    	
    }
}