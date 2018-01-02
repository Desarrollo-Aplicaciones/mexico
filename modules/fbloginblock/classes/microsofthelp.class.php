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

class microsofthelp extends Module{
	
	private $_http_host;
	private $_name;

    private $_social_type = 5;
	
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
	

    
    public function microsoftLogin($_data){
    	 
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/http.php';
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/oauth_client.php';
    	 
    	
    	$name_module = $this->_name;
    	
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}    	
    	
    	$client = new oauth_client_class();
    	$client->server = 'Microsoft';
    	
    	
    	include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
    	$obj_module = new $name_module();
    	$redirect_uri = $obj_module->getRedirectURL(array('typelogin'=>'microsoft','is_settings'=>1));
    	$client->redirect_uri = $redirect_uri;
    	
    	
    	
    	$mclientid = Configuration::get($name_module.'mclientid');
    	$mclientid = trim($mclientid);
    	
    	$mclientsecret = Configuration::get($name_module.'mclientsecret');
    	$mclientsecret = trim($mclientsecret);
    	
    	$client->client_id = $mclientid;
    	$application_line = __LINE__;
    	$client->client_secret = $mclientsecret;
    	
    	if(Tools::strlen($client->client_id) == 0
    			|| Tools::strlen($client->client_secret) == 0)
    		die('Please go to Microsoft Live Connect Developer Center page '.
    				'https://manage.dev.live.com/AddApplication.aspx and create a new'.
    				'application, and in the line '.$application_line.
    				' set the client_id to Client ID and client_secret with Client secret. '.
    				'The callback URL must be '.$client->redirect_uri.' but make sure '.
    				'the domain is valid and can be resolved by a public DNS.');
    	
    	/* API permissions
    	 */
    	$client->scope = 'wl.basic wl.emails wl.birthday';
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
    						'https://apis.live.net/v5.0/me',
    						'GET', array(), array('FailOnAccessError'=>true), $user);
    			}
    		}
    		$success = $client->Finalize($success);
    	}
    	if($client->exit)
    		exit;
    	if($success)
    	{
    		 
    		$last_name = $user->last_name;
    		$first_name = $user->first_name;
    		$email_address = isset($user->emails->preferred)?$user->emails->preferred:$user->emails->account;


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
    		echo 'Error:'.HtmlSpecialChars($client->error);
    	}
    }
}