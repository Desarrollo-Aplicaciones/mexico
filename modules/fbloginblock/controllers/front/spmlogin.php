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

class FbloginblockSpmloginModuleFrontController extends ModuleFrontController
{
	private $_login_type = '';
	private $_name_module = 'fbloginblock';
	
	
	public function init()
	{
		parent::init();
		
	}
	
	public function postProcess()
	{
		parent::postProcess();

        ### customer_linked_social_account only if customer is logged in ###
        $cookie_context = Context::getContext()->cookie;
        $is_logged = isset($cookie_context->id_customer)?$cookie_context->id_customer:0;
        $linksocialaccount = Tools::getValue('linksocialaccount');
        if(version_compare(_PS_VERSION_, '1.5', '>') && $linksocialaccount == 1 && $is_logged){
            $cookie_clsa = new Cookie('clsa');
            $cookie_clsa->linksocialaccount = $linksocialaccount;
        }
        ### customer_linked_social_account only if customer is logged in ###


		require_once(_PS_MODULE_DIR_ . ''.$this->_name_module.'/'.$this->_name_module.'.php');
		$obj = new $this->_name_module();
		$avaiable_connects = $obj->getConnetsArrayPrefix();
		$avaiable_connects_data = array();
		foreach($avaiable_connects as $val_data){
            $val = $val_data['prefix'];
			array_push($avaiable_connects_data,$val);
		}
		array_push($avaiable_connects_data,'login');
		array_push($avaiable_connects_data,'paypalconnect');
        array_push($avaiable_connects_data,'vk');
		
		
		$typelogin = Tools::getValue('typelogin');


		if(in_array($typelogin, $avaiable_connects_data)){
			$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';
			switch($typelogin){
				case 'facebook':
                    include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/facebookhelp.class.php');
					$obj = new facebookhelp();
					$obj->facebookLogin(array('http_referer_custom'=>$http_referer));
				break;	
				case 'twitter':
                    include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/twitter.class.php');
					
					$consumer_key = Configuration::get($this->_name_module.'twitterconskey');
					$consumer_key = trim($consumer_key);
					$consumer_secret = Configuration::get($this->_name_module.'twitterconssecret');
					$consumer_secret = trim($consumer_secret);
					$callback = "";
					
					$data = array('key'=>$consumer_key,
							'secret' =>$consumer_secret,
							'callback' => $callback,
							'http_referer'=>$http_referer
					);
					
					$obj = new twitter($data);
					$action = isset($_REQUEST['action'])?$_REQUEST['action']:'';
					$obj->twitterLogin(array('action'=>$action));
				break;
				case 'login':
                    include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/login.class.php');
					$action = Tools::getValue('p');
					$http_referer = Tools::getValue('http_referer');
						
					$obj = new login(array('p'=>$action,'http_referer'=>$http_referer));
					
					$obj->GoogleAndYahooLoginLogin();
				break;	
				case 'linkedin':
					$cookie = new Cookie('ref');
					$cookie->http_referer_custom = $http_referer;

                    include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/linkedinhelp.class.php');
					$obj = new linkedinhelp();
					$obj->linkedinLogin();
				break;
				case 'microsoft':
                    include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/microsofthelp.class.php');
					$obj = new microsofthelp();
					$obj->microsoftLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'instagram':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/instagramhelp.class.php');
					$obj = new instagramhelp(array('http_referer'=>$http_referer));
					$obj->instagramLogin(array('http_referer_custom'=>$http_referer));
				break;	
				case 'foursquare':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/foursquarehelp.class.php');
					$obj = new foursquarehelp();
					$obj->foursquareLogin(array('http_referer_custom'=>$http_referer));
				break;	
				case 'github':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/githubhelp.class.php');
					$obj = new githubhelp();
					$obj->githubLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'disqus':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/disqushelp.class.php');
					$obj = new disqushelp();
					$obj->disqusLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'dropbox':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/dropboxhelp.class.php');
					$obj = new dropboxhelp();
					$obj->dropboxLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'scoop':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/scoophelp.class.php');
					$obj = new scoophelp();
					$obj->scoopLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'wordpress':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/wordpresshelp.class.php');
					$obj = new wordpresshelp();
					$obj->wordpressLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'tumblr':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/tumblrhelp.class.php');
					$obj = new tumblrhelp();
					$obj->tumblrLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'pinterest':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/pinteresthelp.class.php');
					$obj = new pinteresthelp();
					$obj->pinterestLogin(array('http_referer_custom'=>$http_referer));
				break;
				/*case 'oklass':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/oklasshelp.class.php');
					$obj = new oklasshelp();
					$obj->oklassLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'mailru':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/mailruhelp.class.php');
					$obj = new mailruhelp();
					$obj->mailruLogin(array('http_referer_custom'=>$http_referer));
				break;
				case 'yandex':
					include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/yandexhelp.class.php');
					$obj = new yandexhelp();
					$obj->yandexLogin(array('http_referer_custom'=>$http_referer));
				break;*/
                case 'paypalconnect':
                    include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/paypalhelp.class.php');
                    $obj = new paypalhelp();
                    $obj->paypalLogin(array('http_referer_custom'=>$http_referer));
                break;
                case 'vk':
                    include_once(_PS_MODULE_DIR_.$this->_name_module.'/classes/vkontaktehelp.class.php');
                    $obj = new vkontaktehelp();
                    $obj->vkontakteLogin(array('http_referer_custom'=>$http_referer));
                break;
                default:
					echo 'Not able to connect with social site. Incorrect parameters!';
				break;

			}
            exit;
		} else {
			echo 'Invalid connect type!'; exit;
		}
		
		
		
		
	}
	
	
	
}