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

class linkedinhelp extends Module{
	
	private $_http_host;
	private $_name;
    private $_social_type = 4;
	
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
	
	public function getBaseUrlCustom(){
		return $this->_http_host;
	}
	
	
	public function userLog($_data){


		$data_linkedin = $_data['data'];

        $http_referer = $_data['http_referer_custom'];

        ## add new functional for auth and create user ##

        include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
        $userhelp = new userhelp();
        $userhelp->userLog(
            array(
                'data_profile'=>$data_linkedin,
                'http_referer_custom'=>$http_referer,
                'type'=>$this->_social_type,
            )
        );
        ## add new functional for auth and create user ##


    }



    public function linkedinLogin(){

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            session_start_fbloginblock();
        }
    	 
    	$lapikey = Configuration::get($this->_name.'lapikey');
    	$lapikey = trim($lapikey);
    	$lsecret = Configuration::get($this->_name.'lsecret');
    	$lsecret = trim($lsecret);
    	
    	$data = array(
    			'access' => $lapikey,
    			'secret' => $lsecret,
    	);
    	
    	
    	$_http_host = $this->getBaseUrlCustom();
    	
    	
    	$config = $data;
    	
    	if(Tools::strlen($config['access'])==0 || Tools::strlen($config['secret'])==0)
    		die("Error: Please fill LinkedIn API Key and LinkedIn Secret Key in the settings of the module.");
    	
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/oAuth/linkedinoAuth.php';


        include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
        $obj = new $this->_name();

        $redirect_uri = $obj->getRedirectURL(array('typelogin'=>'linkedinauth','is_settings'=>1));

        /*if(version_compare(_PS_VERSION_, '1.7', '<')) {
            $redirect_uri = $_http_host . 'modules/'.$this->_name.'/linkedinauth.php';
        } else {

            ### only for prestashop 1.7.x.x ###
            $custom_ssl_var = 0;
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                $custom_ssl_var = 1;

            $cookie = $this->context->cookie;
            $id_lang = (int)$cookie->id_lang;
            $id_shop = Context::getContext()->shop->id;

            $link = new Link();
            $redirect_uri = $link->getModuleLink($this->_name, 'linkedinauth', array(), $custom_ssl_var, $id_lang, $id_shop);

            include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
            $obj = new $this->_name();

            $lang_iso = $obj->getLangISO();
            if(Tools::strlen($lang_iso)>0)
                $redirect_uri = str_replace("/".$lang_iso."/","/",$redirect_uri);

            ### only for prestashop 1.7.x.x ###
        }*/

    	 
    	# First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
    	$linkedin = new LinkedIn($config['access'], $config['secret'], $redirect_uri);
    	//    $linkedin->debug = true;
    	
    	# Now we retrieve a request token. It will be set as $linkedin->request_token
    	$linkedin->getRequestToken();
    	$_SESSION['requestToken'] = serialize($linkedin->request_token);
    	
    	# With a request token in hand, we can generate an authorization URL, which we'll direct the user to
    	## echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
    	$url = $linkedin->generateAuthorizeUrl();
    	redirect_custom_fbloginblock($url);
    }
    
}