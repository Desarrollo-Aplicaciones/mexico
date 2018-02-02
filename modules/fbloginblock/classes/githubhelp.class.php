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

class githubhelp extends Module{
	
	private $_http_host;
	private $_name;
    private $_social_type = 21;
	
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
	

    public function githubLogin($_data){
    	
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	
    	
    	
    	$name_module = $this->_name;
    	
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}

        include_once(_PS_MODULE_DIR_.$this->_name.'/lib/github/GithubAPI.class.php');
    	
    	
    	
    	$client_id = Configuration::get($name_module.'gici');
    	$client_id = trim($client_id);
    	$client_secret = Configuration::get($name_module.'gics');
    	$client_secret = trim($client_secret);
    	$redirect_uri = Configuration::get($name_module.'giru');
    	$redirect_uri = trim($redirect_uri);
    	
    	if(Tools::strlen($client_id)==0 || Tools::strlen($client_secret)==0 || Tools::strlen($redirect_uri)==0){
    		echo "Error: Please fill Github Client Id, Github Client Secret, Github Callback URL in the module settings!";
    		exit;
    	}
    	
    	
    	$appName = Configuration::get('PS_SHOP_NAME');
    	
    	
    	$code = Tools::getValue('code');
    	
    	if($code)
    	{
    		$fields = array( 'client_id'=>$client_id, 'client_secret'=>$client_secret, 'code'=>$code);
    		$postvars = '';
    		foreach($fields as $key=>$value) {
    			$postvars .= $key . "=" . $value . "&";
    		}
    	
    		$data = array('url' => 'https://github.com/login/oauth/access_token',
    				'data' => $postvars,
    				'header' => array("Content-Type: application/x-www-form-urlencoded","Accept: application/json"),
    				'method' => 'POST'
    		);
    	
    		$githubapi = new GithubAPI();
    	
    		$gitResponce = Tools::jsonDecode($githubapi->curlRequest($data));
    	
    		//echo "<pre>"; var_dump($gitResponce);
    	
    		if($gitResponce->access_token)
    		{
    			$data = array('url' => 'https://api.github.com/user?access_token='.$gitResponce->access_token,
    					'header' => array("Content-Type: application/x-www-form-urlencoded","User-Agent: ".$appName,"Accept: application/json"),
    					'method' => 'GET'
    			);
    	
    			$gitUser = Tools::jsonDecode($githubapi->curlRequest($data));
    	
    			//echo "<pre>"; var_dump($gitUser);
    	
    	
    			if(!$gitUser->email){
    				echo 'You don\'t have public email in your Github Account. Go to Github -> Settings -> Public email -> Select your Email!';exit;
    			}
    	
    			
    			$first_name = $gitUser->login;
    			$last_name = $gitUser->login;
    			$email_address = $gitUser->email;
    	

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
    	
    	
    	
    		}
    		else
    		{
    			echo "Some error occured try again"; exit;
    		}
    	}
    	else
    	{
    	
    		$redirect_uri = "https://github.com/login/oauth/authorize?scope=user:email&client_id=".$client_id;
    		redirect_custom_fbloginblock($redirect_uri);
    		exit;
    	}
    }
}