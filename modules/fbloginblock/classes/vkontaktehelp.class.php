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

class vkontaktehelp extends Module{
	

	private $_name;
    private $_social_type = 58;
	
	public function __construct(){
		
		$this->_name = 'fbloginblock';

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
	

    
    public function vkontakteLogin($_data){
    
    	 
    	 
    	$name_module = $this->_name;
    	 
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	 
    	if (version_compare(_PS_VERSION_, '1.5', '>')){
    		$cookie = new Cookie('ref');
    		$cookie->http_referer_custom = $http_referer;
    	}
    	 
    	 
    	 
    	include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
    	$obj_module = new $name_module();
    	$redirect_url = $obj_module->getRedirectURL(array('typelogin'=>'vkontakte','is_settings'=>1));
    	 
    	
    	
    	$VK_APP_ID = Configuration::get($name_module.'vci');
    	$VK_APP_ID = trim($VK_APP_ID);
    	$VK_SECRET_CODE = Configuration::get($name_module.'vcs');
    	$VK_SECRET_CODE = trim($VK_SECRET_CODE);
    	$redirect_url = trim($redirect_url);
    	
    	if(Tools::strlen($VK_APP_ID)==0 || Tools::strlen($VK_SECRET_CODE)==0){
    		echo "Error: Please fill Vkontakte ID, Vkontakte Secret Key in the module settings!";
    		exit;
    	}
    	
    	
    	$scope = "id,first_name,last_name,email";
    	
    	
    	$code = Tools::getValue('code');
    	
    	if($code) {
    	
    		$vk_grand_url = "https://api.vk.com/oauth/access_token?client_id=".$VK_APP_ID."&client_secret=".$VK_SECRET_CODE."&code=".$code."&redirect_uri=".$redirect_url;
    	
    		// отправляем запрос на получения access token
    		$resp = Tools::file_get_contents($vk_grand_url);
    		$data = Tools::jsonDecode($resp, true);
    	
    	
    		$vk_access_token = $data['access_token'];
    		$vk_uid =  $data['user_id'];
    		$email_address  = $data['email'];
    	

    	
    		// 	обращаемся к ВК Api, получаем имя, фамилию и ID пользователя вконтакте
    		// 	метод users.get
    		$url_data = "https://api.vk.com/method/users.get?uids=".$vk_uid."&access_token=".$vk_access_token."&fields=".$scope;
    		$res = Tools::file_get_contents($url_data);
    		$data = Tools::jsonDecode($res, true);
    	
    		$user_info = $data['response'][0];

    		$first_name = $user_info['first_name'];
    		$last_name = $user_info['last_name'];



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
    	
    	
    	
    		exit;
    	
    	} else {
    	
    		$red_url = "https://oauth.vk.com/authorize?client_id=".$VK_APP_ID."&scope=".$scope."&redirect_uri=".$redirect_url."&response_type=code";
    		redirect_custom_fbloginblock($red_url);
    		exit;
    	}
    	 
    	
    	 
    	 
    	 
    }
}