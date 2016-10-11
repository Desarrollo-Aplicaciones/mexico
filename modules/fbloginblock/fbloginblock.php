<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category social_networks
 * @package fbloginblock
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

class fbloginblock extends Module
{
	private $_http_referer;
	private $_is15;
	private $_is16;
	private $_translations;
	private $_multiple_lang;
	
	private $_step = 25;
	
	
	
	public function __construct()	
 	{
 	 	$this->name = 'fbloginblock';
 	 	$this->version = '1.4.9';
 	 	$this->tab = 'social_networks';
 	 	$this->author = 'SPM';
 	 	$this->module_key = "86adfe9f51496e857a90dfc487b2e79a";
 	 	
 	 	require_once(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
 	 	
 	 	
 	 	if(version_compare(_PS_VERSION_, '1.5', '>'))
			$this->_is15 = 1;
		else
			$this->_is15 = 0;
			
 		if(version_compare(_PS_VERSION_, '1.6', '>')){
 	 		$this->bootstrap = false;
 	 	}	
			
		if(version_compare(_PS_VERSION_, '1.6', '>'))
			$this->_is16 = 1;
		else
			$this->_is16 = 0;
			
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			if(sizeof(Language::getLanguages(true))>1){
				$this->_multiple_lang = 1;
			} else {
				$this->_multiple_lang = 0;
			}
		} else {
			
			// ps 1.3
			if(version_compare(_PS_VERSION_, '1.4', '<')){
				$this->_multiple_lang = 0;
			}else{
				if(sizeof(Language::getLanguages(true))>1){
					$this->_multiple_lang = 1;
				} else {
					$this->_multiple_lang = 0;
				}
			}
            				
		}
		
		//$this->bootstrap = true;
			
		parent::__construct();
		$this->page = basename(__FILE__, '.php');
		
		
		$this->displayName = $this->l('Fb, Tw, G+1 etc. Connects 11 in 1 + Statistics');
		$this->description = $this->l('Add Fb, Tw, G+1 etc. Connects 11 in 1  + Statistics');
		
		$this->confirmUninstall = $this->l('Are you sure you want to remove it ? Be careful, all your configuration and your data will be lost');
		
		$this->_http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
		
		
		$this->_translations = array('facebook'=>$this->l('Error: Please fill Facebook App Id and Facebook Secret Key in the module settings'),
									 'twitter'=>$this->l('Error: Please fill Consumer key and Consumer secret in the module settings'),
									 'linkedin'=>$this->l('Error: Please fill LinkedIn API Key and LinkedIn Secret Key in the module settings'),
									 'microsoft'=>$this->l('Error: Please fill Microsoft Live Client ID and Microsoft Live Client Secret in the module settings'),
									 'google'=>$this->l('Error: Please fill Google Client Id and Google Client Secret in the module settings'),
									 'instagram'=>$this->l('Error: Please fill Instagram Client Id and Instagram Client Secret in the module settings'),
				
									 'foursquare'=>$this->l('Error: Please fill Foursquare Client Id and Foursquare Client Secret in the module settings'),
									 'github'=>$this->l('Error: Please fill Github Client Id and Github Client Secret in the module settings'),
				
									 'disqus'=>$this->l('Error: Please fill Disqus API Key and Disqus API Secret in the module settings'),
				
								     'amazon'=>$this->l('Error: Please fill Amazon Client ID and Amazon Allowed Return URL in the module settings'),
									 );

        $this->initContext();
 	}
 	
 	
 	private function initContext()
	{
	  $this->context = Context::getContext();
	  if (version_compare(_PS_VERSION_, '1.5', '>')){
	 	 $this->context->currentindex = AdminController::$currentIndex;
	  } else {
	  	$this->context->currentindex = $this->currentindex;
          }
        }
 	
	public function install()
	{
		
	 	if (!parent::install())
	 		return false;
	 		
	 	Configuration::updateValue($this->name.'defaultgroup', 3);
	 	
	 	$languages = Language::getLanguages(false);
	 	foreach ($languages as $language){
	 		$i = $language['id_lang'];
	 		$authp = $this->l('Connect with:');
	 		Configuration::updateValue($this->name.'authp_'.$i, $authp);
	 	}
	 	
	 	$prefix = "txt";
	 	Configuration::updateValue($this->name.'_top'.$prefix, 'top'.$prefix);
	 	Configuration::updateValue($this->name.'_authpage'.$prefix, 'authpage'.$prefix);
	 	Configuration::updateValue($this->name.'_footer'.$prefix, 'footer'.$prefix);
	 	

	 	
	 	Configuration::updateValue($this->name.'iauth', 1);
	 	
	 	$languages = Language::getLanguages(false);
	 	foreach ($languages as $language){
	 		$i = $language['id_lang'];
	 		$txtauthp = $this->l('You can use any of the login buttons above to automatically create an account on our shop.');
	 		Configuration::updateValue($this->name.'txtauthp_'.$i, $txtauthp);
	 	}
	 	
	 	
	 	if(version_compare(_PS_VERSION_, '1.6', '>')){
	 		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
	 	} else {
	 		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
	 	}
	 	
	 	
 		// yahoo
	 	Configuration::updateValue($this->name.'y_on', 1);
		
	 	//microsoft connect
	 	Configuration::updateValue($this->name.'m_on', 1);
			
	 		
	 	// linkedin connect
	 	Configuration::updateValue($this->name.'l_on', 1);
			
	  	//twitter connect
	 	Configuration::updateValue($this->name.'t_on', 1);
		
	 	
	 	// facebook connect
	 	Configuration::updateValue($this->name.'f_on', 1);
			
	 
	 	// google connect
	 	Configuration::updateValue($this->name.'g_on', 1);
	 	
	 	
	 	// changes OAuth 2.0
	 	Configuration::updateValue($this->name.'oru', $_http_host.'modules/'.$this->name.'/login.php');
		// changes OAuth 2.0
	 	
		
			
	 	
	 	// instagram connect
	 	Configuration::updateValue($this->name.'i_on', 1);
	 	 
	 	// changes OAuth 2.0
	 	Configuration::updateValue($this->name.'iru', $_http_host.'modules/'.$this->name.'/instagram.php');
	 	// changes OAuth 2.0
	 	 
	 	
	 	
	 	// foursquare connect
	 	Configuration::updateValue($this->name.'fs_on', 1);
	 	Configuration::updateValue($this->name.'fsru', $_http_host.'modules/'.$this->name.'/foursquare.php');
	 	// foursquare connect
	 	
	 	
	 	// github connect
	 	Configuration::updateValue($this->name.'gi_on', 1);
	 	Configuration::updateValue($this->name.'giru', $_http_host.'modules/'.$this->name.'/github.php');
	 		 
	 	
	 	// disqus connect
	 	Configuration::updateValue($this->name.'d_on', 1);
	 	Configuration::updateValue($this->name.'dru', $_http_host.'modules/'.$this->name.'/disqus.php');
	 	 
	 	// amazon connect
	 	Configuration::updateValue($this->name.'a_on', 1);
	 	Configuration::updateValue($this->name.'aru', $_http_host.'modules/'.$this->name.'/amazon.php');
	 	 
	 	
	 	
	 	$array_need = array('f','t','g','y','l','m','i','p','fs','gi','d','a');
	 	foreach($array_need as $prefix){
	 		Configuration::updateValue($this->name.'sztop'.$prefix, 'ltop'.$prefix);
	 		Configuration::updateValue($this->name.'szrightcolumn'.$prefix, 'srightcolumn'.$prefix);
	 		Configuration::updateValue($this->name.'szleftcolumn'.$prefix, 'sleftcolumn'.$prefix);
	 		
	 		Configuration::updateValue($this->name.'szfooter'.$prefix, 'lfooter'.$prefix);
	 			
	 		Configuration::updateValue($this->name.'szauthpage'.$prefix, 'lsauthpage'.$prefix);
	 		Configuration::updateValue($this->name.'szwelcome'.$prefix, 'smwelcome'.$prefix);
	 		
	 		
	 		Configuration::updateValue($this->name.'_top'.$prefix, 'top'.$prefix);
	 		Configuration::updateValue($this->name.'_rightcolumn'.$prefix, 'rightcolumn'.$prefix);
	 		Configuration::updateValue($this->name.'_leftcolumn'.$prefix, 'leftcolumn'.$prefix);
	 		 
	 		Configuration::updateValue($this->name.'_authpage'.$prefix, 'authpage'.$prefix);
	 		Configuration::updateValue($this->name.'_welcome'.$prefix, 'welcome'.$prefix);
	 	}
	 	
	 	
	 	if (!$this->registerHook('leftColumn') 
			|| !$this->registerHook('rightColumn') 
			|| !$this->registerHook('header') 
			|| !$this->createCustomerTbl() 
	 		|| !$this->_createFolderAndSetPermissions()
	 		|| !$this->createUserTwitterTable()
	 		|| !$this->_createInstagramTable()
	 		
	 		)
			return false;
	 	
	 	return true;
	}
	
	public function uninstall()
	{
		
		
		if (!$this->uninstallTable() || !parent::uninstall()
			)
			return false;
		return true;
	}
	
	public function uninstallTable() {
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_img');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'fb_customer');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'tw_customer');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'customers_statistics_spm');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'instagram_spm');
		
		return true;
	}
	
	
	private function _createFolderAndSetPermissions(){
		
		$prev_cwd = getcwd();
		
		$module_dir = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
		@chdir($module_dir);
		
		//folder logo
		
		$module_dir_img = $module_dir.$this->name.DIRECTORY_SEPARATOR; 
		@mkdir($module_dir_img, 0777);

		@chdir($prev_cwd);
		
		return true;
	} 
	
	public function createCustomerTbl()
	{
	
		
		$db = Db::getInstance();
		
		$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'fb_customer (
				  `customer_id` int(10) NOT NULL,
				  `fb_id` bigint(20) NOT NULL,
				  `id_shop` int(11) NOT NULL default \'0\',
				  UNIQUE KEY `FBLOGINBLOCK_CUSTOMER` (`customer_id`,`fb_id`,`id_shop`)
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		$db->Execute($query);

		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.$this->name.'_img` (
					  `id` int(11) NOT NULL auto_increment,
					  `img` text,
					  `id_shop` int(11) NOT NULL default \'0\',
					  `type` int(11) NOT NULL default \'1\',
					  PRIMARY KEY  (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;';
		
		/*
		 *  COMMENT \'1 - Facebook, 2 - Google, 3 - Paypal, 
					  4 - Facebook small, 5 - Google small, 6 - Paypal small, 
					  7 - Twitter, 8 - Twitter small, 9 - Yahoo, 10 - Yahoo small,
					  11 - LinkedIn, 12 - LinkedIn Small,  
					  13 - Microsoft, 14 - Microsoft Small, 15 - Instagramm, 16 - Instagramm small
					  17 - Facebook large_small image, 18 - Facebook small_micro image,
					  19 - Twitter large_small image, 20 - Twitter small_micro image,
					  21 - Paypal large_small image, 22 - Paypal small_micro image,
					  23 - Google large_small image, 24 - Google small_micro image,
					  25 - Yahoo large_small image, 26 - Yahoo small_micro image,
					  27 - Linkedin large_small image, 28 - Linkedin small_micro image,
					  29 - Microsoft large_small image, 30 - Microsoft small_micro image,
					  31 - Instagramm large_small image, 32 - Instagramm small_micro image,
					  33 - Foursquare, 34 - Foursquare small image,
					  35 - Foursquare large_small image, 36 - Foursquare small_micro image,
					  37 - Github, 38 - Github small image,
					  39 - Github large_small image, 40 - Github small_micro image,
					  41 - Disqus, 42 - Disqus small image,
					  43 - Disqus large_small image, 44 - Disqus small_micro image,
					    \',
		 */
		$db->Execute($sql);
		
		
		
		$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'customers_statistics_spm (
				  `id` int(11) NOT NULL auto_increment,
				  `customer_id` int(10) NOT NULL,
				  `email` text,
				  `id_shop` int(11) NOT NULL default \'0\',
				  `type` int(11) NOT NULL default \'0\' ,
				  PRIMARY KEY  (`id`)
				  ) ENGINE=InnoDB DEFAULT CHARSET=utf8';
		$db->Execute($query);
		
		return true;
			
	
	}
	
	public function createUserTwitterTable(){
		
		$db = Db::getInstance();
		$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'tw_customer (
				  	 `id` int(11) NOT NULL AUTO_INCREMENT,
					  `twitter_id` bigint(20) NOT NULL,
					  `user_id` int(11) NOT NULL,
					  `id_shop` int(11) NOT NULL default \'0\',
					   UNIQUE KEY `FBTWCONNECT_CUSTOMER` (`twitter_id`,`user_id`,`id_shop`),
					  PRIMARY KEY (`id`)
					) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").'  DEFAULT CHARSET=utf8';
		$db->Execute($query);
		return true;
		
	}
	
	
	private function _createInstagramTable(){
		$db = Db::getInstance();
		$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'instagram_spm
					(
						id INT PRIMARY KEY AUTO_INCREMENT,
						username VARCHAR(70),
						name VARCHAR(100),
						bio TEXT,
						website VARCHAR(200),
						instagram_id bigint(20) NOT NULL,
						`user_id` int(11) NOT NULL,
						`id_shop` int(11) NOT NULL default \'0\',
						instagram_access_token VARCHAR(200)
					) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").'  DEFAULT CHARSET=utf8;';
		
		$db->Execute($query);
		return true;
	}
	
public function getOrderPage($data = null){
    	$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;
		
		
		$http_referrer = isset($data['http_referrer'])?$data['http_referrer']:$this->_http_referer;
		
    	$id_lang = (int)$cookie->id_lang;
    	
    	$iso_lang = Language::getIsoById((int)($id_lang))."/";   
			
		if(!$this->_multiple_lang)
			$iso_lang = "";   
			
			
		$smarty->assign($this->name.'iso_lang', $iso_lang);
			
			
		
		if(Configuration::get('PS_REWRITING_SETTINGS') && version_compare(_PS_VERSION_, '1.4', '>')){
    		$smarty->assign($this->name.'is_rewrite', 1);
    	} else {
    		$smarty->assign($this->name.'is_rewrite',0);
    	}
			
		// if order page    
   	   if(version_compare(_PS_VERSION_, '1.5', '>')){
	        $data = explode("?",$http_referrer);
	    	$data  = end($data);
	    	$data_url_rewrite_on = explode("/",$http_referrer);
	    	$data_url_rewrite_on = end($data_url_rewrite_on);
	    	
	    	$link = new Link();
			$my_account = $link->getPageLink("my-account", true, $id_lang);
	    	
			$order = $link->getPageLink("order", true, $id_lang); 
			
	        if(version_compare(_PS_VERSION_, '1.6', '>')){
				$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
			} else {
				$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
			}
			
			$order = str_replace($_http_host.$iso_lang,'',$order);
			
			if(Configuration::get('PS_REWRITING_SETTINGS'))
	    		$uri = str_replace($_http_host,'',$my_account);
	    	else 
	    		$uri = 'index.php?controller=my-account&id_lang='.$id_lang;
	    		
	    	
	    		
	    	$order_page = 0;
	        if($data == 'controller=order' || $data_url_rewrite_on == 'order' || $data == $order || $data_url_rewrite_on == $order ||
	        	$data == 'controller=quick-order' || $data_url_rewrite_on == 'quick-order'){
	        		
	        	$order_page = 1;
	    		if($data == 'controller=order' || $data == 'controller=quick-order' || $data == $order)
	    			$uri = 'index.php?controller=order&step=1&id_lang='.$id_lang;
	    		elseif($data_url_rewrite_on == 'order' || $data_url_rewrite_on == 'quick-order' || $data_url_rewrite_on == $order)
	    		 	$uri = $iso_lang.$order.'?step=1';
	    		 	
	    		 
	    	}
	    	$smarty->assign($this->name.'order_page', $order_page);
	    } else {
	    	$data = explode("/",$http_referrer);
	    	$data  = end($data);
	    	
	    	if(Configuration::get('PS_REWRITING_SETTINGS') && version_compare(_PS_VERSION_, '1.4', '>'))
	    		$uri = $iso_lang.'my-account';
	    	else 
	    		$uri = 'my-account.php?id_lang='.$id_lang;
	    	$order_page = 0;
	    	if($data == 'order.php' 
	    	|| $data == 'order'
	    	){
	    		$order_page = 1;
	    		if($data == 'order.php')
	    			$uri = 'order.php?step=1&id_lang='.$id_lang;
	    		elseif($data == 'order')
	    		 	$uri = $iso_lang.'order?step=1';
	    		 	
	    	}
	    	$smarty->assign($this->name.'order_page', $order_page);
	    }

	    if ( (int)$this->context->cart->nbProducts() > 0 ) {
        	$uri = "index.php?controller=order";
        } else {
        	parse_str(parse_url($_SERVER["REQUEST_URI"])['query'], $queryString);
			$uri = ( isset($queryString['back']) && !empty($queryString['back']) ) ? $queryString['back'] : 'my-account.php';
        }

	    $smarty->assign($this->name.'uri', $uri);
    	// if order page
    	$smarty->assign($this->name.'http_referer', $http_referrer);
    	
    	return array('uri'=>$uri);
    }
	
	
 public function hookHeader($params){
    	$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;
		
		
		
    	$data_fb = $this->getfacebooklib((int)$params['cookie']->id_lang);
		$smarty->assign($this->name.'lang', $data_fb['lng_iso']);
		
    	$is_ps5 = 0;
    	if(version_compare(_PS_VERSION_, '1.5', '>')){
    		$is_ps5 = 1;	
    	}
    	$smarty->assign($this->name.'is_ps5', $is_ps5);
    	
    	$is_logged = isset($cookie->id_customer)?$cookie->id_customer:0;
		$smarty->assign($this->name.'islogged', $is_logged);
		
		
		
		include_once(dirname(__FILE__).'/classes/facebookhelp.class.php');
		$obj = new facebookhelp();
		$data_img = $obj->getImages();
		 
		####### images and positions ####
		$data_connects_array_prefix = $this->getConnetsArrayPrefix();
		foreach($data_connects_array_prefix as $prefix_short => $prefix_full){
		
		$facebookimg = $data_img[$prefix_full];
		$facebooksmallimg = $data_img[$prefix_full.'small'];
		$facebookimglarge_small = $data_img[$prefix_full.'large_small'];
		$facebookimgmicro_small = $data_img[$prefix_full.'micro_small'];
		 
		$array_f_head = array("top","footer","authpage","welcome");
		 
		foreach($array_f_head as $prefix_hook){
		
				switch(Configuration::get(($this->name.'sz'.$prefix_hook.$prefix_short))){
				case 'l'.$prefix_hook.$prefix_short:
				$facebook_img = $facebookimg;
				break;
				case 'ls'.$prefix_hook.$prefix_short:
				$facebook_img = $facebookimglarge_small;
				break;
				case 's'.$prefix_hook.$prefix_short:
				$facebook_img = $facebooksmallimg;
				break;
				case 'sm'.$prefix_hook.$prefix_short:
				$facebook_img = $facebookimgmicro_small;
				break;
				default:
				$facebook_img = $facebooksmallimg;
				break;
				}
				$smarty->assign($this->name.$prefix_short.$prefix_hook.'img', $facebook_img);
		}
		 
		 
		 
					$smarty->assign($this->name.$prefix_short.'_on', Configuration::get($this->name.$prefix_short.'_on'));
		
					$smarty->assign($this->name.'_top'.$prefix_short, Configuration::get($this->name.'_top'.$prefix_short));
					$smarty->assign($this->name.'_footer'.$prefix_short, Configuration::get($this->name.'_footer'.$prefix_short));
					$smarty->assign($this->name.'_authpage'.$prefix_short, Configuration::get($this->name.'_authpage'.$prefix_short));
					$smarty->assign($this->name.'_welcome'.$prefix_short, Configuration::get($this->name.'_welcome'.$prefix_short));
		}
		####### images and positions ####
		
    	
		// facebook connect
		
		$smarty->assign('blockfacebookappid', Configuration::get($this->name.'appid'));
		$smarty->assign('blockfacebooksecret', Configuration::get($this->name.'secret'));
		 
		// facebook connect
		
    	
    	$id_lang = (int)$cookie->id_lang;
    	
    	### set variables for order page ####
		$this->getOrderPage();
    	### set variables for order page ####
	    
    	// facebook connect
    	
    	
    	// paypal connect
		
		$clientid = Configuration::get($this->name.'clientid');
		$psecret = Configuration::get($this->name.'psecret');
		$pcallback = Configuration::get($this->name.'pcallback');
		
		if(Tools::strlen($clientid)>0 && Tools::strlen($psecret)>0 && Tools::strlen($pcallback)>0){
			$smarty->assign($this->name.'pconf', 1);
    	} else {
    		$smarty->assign($this->name.'pconf', 0);
    	}
    	// paypal connect
    	
    	// twitter connect
		
    	$consumer_key = Configuration::get($this->name.'twitterconskey');
		$consumer_secret = Configuration::get($this->name.'twitterconssecret');
		if(Tools::strlen($consumer_key)>0 && Tools::strlen($consumer_secret)>0){
			$smarty->assign($this->name.'tconf', 1);
    	} else {
    		$smarty->assign($this->name.'tconf', 0);
    	}

    	// twitter connect
    	
    	
    	// linkedin connect
			
		$lapikey = Configuration::get($this->name.'lapikey');
		$lsecret = Configuration::get($this->name.'lsecret');
		
		if(Tools::strlen($lapikey)>0 && Tools::strlen($lsecret)>0){
			$smarty->assign($this->name.'lconf', 1);
    	} else {
    		$smarty->assign($this->name.'lconf', 0);
    	}
    	// linkedin connect
    	
    	
    	// microsoft connect
		
		$mclientid = Configuration::get($this->name.'mclientid');
		$mclientsecret = Configuration::get($this->name.'mclientsecret');
		
		if(Tools::strlen($mclientid)>0 && Tools::strlen($mclientsecret)>0){
			$smarty->assign($this->name.'mconf', 1);
    	} else {
    		$smarty->assign($this->name.'mconf', 0);
    	}
    	// microsoft connect
    	
    	
    	// google connect
    	$oci = Configuration::get($this->name.'oci');
		$ocs = Configuration::get($this->name.'ocs');
		$oru = Configuration::get($this->name.'oru');
		
		if(Tools::strlen($oci)>0 && Tools::strlen($ocs)>0 && Tools::strlen($oru)>0){
			$smarty->assign($this->name.'gconf', 1);
    	} else {
    		$smarty->assign($this->name.'gconf', 0);
    	}
    	// google connect
    	
    	
    	
    	
    	
    	// instagram connect
    	$ici = Configuration::get($this->name.'ici');
    	$ics = Configuration::get($this->name.'ics');
    	$iru = Configuration::get($this->name.'iru');
    	
    	if(Tools::strlen($ici)>0 && Tools::strlen($ics)>0 && Tools::strlen($iru)>0){
    		$smarty->assign($this->name.'iconf', 1);
    	} else {
    		$smarty->assign($this->name.'iconf', 0);
    	}
    	// instagram connect
    	
    	
    	
    	// foursquare connect
    	$fsci = Configuration::get($this->name.'fsci');
    	$fscs = Configuration::get($this->name.'fscs');
    	$fsru = Configuration::get($this->name.'fsru');
    	
    	if(Tools::strlen($fsci)>0 && Tools::strlen($fscs)>0 && Tools::strlen($fsru)>0){
    		$smarty->assign($this->name.'fsconf', 1);
    	} else {
    		$smarty->assign($this->name.'fsconf', 0);
    	}
    	// foursquare connect
    	
    	
    	// github connect
    	$gici = Configuration::get($this->name.'gici');
    	$gics = Configuration::get($this->name.'gics');
    	$giru = Configuration::get($this->name.'giru');
    	 
    	if(Tools::strlen($gici)>0 && Tools::strlen($gics)>0 && Tools::strlen($giru)>0){
    		$smarty->assign($this->name.'giconf', 1);
    	} else {
    		$smarty->assign($this->name.'giconf', 0);
    	}
    	// github connect
    	
    	
    	// disqus connect
    	$dci = Configuration::get($this->name.'dci');
    	$dcs = Configuration::get($this->name.'dcs');
    	$dru = Configuration::get($this->name.'dru');
    	
    	if(Tools::strlen($dci)>0 && Tools::strlen($dcs)>0 && Tools::strlen($dru)>0){
    		$smarty->assign($this->name.'dconf', 1);
    	} else {
    		$smarty->assign($this->name.'dconf', 0);
    	}
    	// disqus connect
    	
    	
    	$smarty->assign($this->name.'http_referer', $this->_http_referer);
    	
    	$smarty->assign($this->name.'is16', $this->_is16);
		
    	
    	$data_errors = $this->_translations;
    	$smarty->assign('gerror', $data_errors['google']);
    	$smarty->assign('ferror', $data_errors['facebook']);
    	$smarty->assign('terror', $data_errors['twitter']);
    	$smarty->assign('lerror', $data_errors['linkedin']);
    	$smarty->assign('merror', $data_errors['microsoft']);
    	$smarty->assign('ierror', $data_errors['instagram']);
    	$smarty->assign('fserror', $data_errors['foursquare']);
    	$smarty->assign('gierror', $data_errors['github']);
    	$smarty->assign('derror', $data_errors['disqus']);
    	$smarty->assign('aerror', $data_errors['amazon']);
    	
    	
    	$smarty->assign($this->name.'authp', Configuration::get($this->name.'authp_'.$id_lang));
    	
    	$prefix = "txt";
    	$smarty->assign($this->name.'_top'.$prefix, Configuration::get($this->name.'_top'.$prefix));
    	$smarty->assign($this->name.'_footer'.$prefix, Configuration::get($this->name.'_footer'.$prefix));
    	$smarty->assign($this->name.'_authpage'.$prefix, Configuration::get($this->name.'_authpage'.$prefix));
    	
    	
    	$smarty->assign($this->name.'iauth', Configuration::get($this->name.'iauth'));
    	$smarty->assign($this->name.'txtauthp', Configuration::get($this->name.'txtauthp_'.$id_lang));
    	 
    	
    	
    	
    	
    	#### show popup for twitter customer which not changed email address  #####
		if(Configuration::get('PS_REWRITING_SETTINGS')){
			$request_uri = $_SERVER["REQUEST_URI"];
		} else {
			$request_uri = $_SERVER["REQUEST_URI"];
			$request_uri = str_replace("index.php","",$request_uri);
		}
		
		
	    $link = new Link();
	    
	    if(version_compare(_PS_VERSION_, '1.4', '<')){
			$my_account = $link->getCustomLink("my-account", true, $id_lang);
	    } else {
	    	$my_account = $link->getPageLink("my-account", true, $id_lang);
	    }
	    
			
		$req_uri = explode("/",$request_uri);
		$req_uri = end($req_uri);
		
		
		$is_my_account_page = stripos($my_account,$req_uri);
		
		$is_twitter_customer = 0;
		if($cookie->id_customer){
			$customer_email = $cookie->email;
			
			$is_twitter_customer = stripos($customer_email,"twitter.com");
			
			$smarty->assign($this->name.'cid', $cookie->id_customer);
		}
		
		$show_twitter_popup = 0;
		if($is_my_account_page && $is_twitter_customer)
			$show_twitter_popup = 1;
			
		$smarty->assign($this->name.'twpopup', $show_twitter_popup);	
		
		
		/// instagram ////
		$is_instagram_customer = 0;
		if($cookie->id_customer){
			$customer_email = $cookie->email;
				
			$is_instagram_customer = stripos($customer_email,"instagram.com");
				
			$smarty->assign($this->name.'cid', $cookie->id_customer);
		}
		
		$show_instagram_popup = 0;
		if($is_my_account_page && $is_instagram_customer)
			$show_instagram_popup = 1;
			
		$smarty->assign($this->name.'inpopup', $show_instagram_popup);
		/// instagram ////
		
		
		// amazon connect
		$aci = Configuration::get($this->name.'aci');
		$aru = Configuration::get($this->name.'aru');
		$smarty->assign($this->name.'amazonci',$aci);
		
		if(Tools::strlen($aci)>0 && Tools::strlen($aru)>0){
			$smarty->assign($this->name.'aconf', 1);
		} else {
			$smarty->assign($this->name.'aconf', 0);
		}
		 
		if (Configuration::get('PS_SSL_ENABLED') == 0)
		{
			$smarty->assign($this->name.'ssltxt', 'Note: SSL has not enabled on this server');
			$smarty->assign($this->name.'is_ssl',0);
		} else {
			$smarty->assign($this->name.'is_ssl',1);
		}
		 
		// amazon connect
		
		
		#### show popup for twitter customer which not changed email address  #####
		
		$data_tw = $this->twTranslate();
		$smarty->assign($this->name.'tw_one', $data_tw['twitter_one']);
		$smarty->assign($this->name.'tw_two', $data_tw['twitter_two']);
		
		$smarty->assign($this->name.'in_one', $data_tw['instagram_one']);
		$smarty->assign($this->name.'in_two', $data_tw['instagram_two']);
		
		return $this->display(__FILE__, 'views/templates/hooks/head.tpl');
    	
    }
    
    #### show popup for twitter customer which not changed email address  #####
    public function twTranslate(){
    	return array('valid_email' => $this->l('This email address is not valid'),
    				 'exists_customer' => $this->l('An account using this email address has already been registered.'),
    				 'send_email' => $this->l('Password has been sent to your mailbox:'),
    				 'log_in' => $this->l('You must be log in.'),
    	 			 'twitter_one'=>$this->l('You have linked your Account to your Twitter profile'),
					 'twitter_two'=>$this->l('Because Twitter does not give us your e-mail address, your account was created with a false generic e-mail. Please update your e-mail address now by filling it out below.'),
    				 'instagram_one'=>$this->l('You have linked your Account to your Instagram profile'),
    				 'instagram_two'=>$this->l('Because Instagram does not give us your e-mail address, your account was created with a false generic e-mail. Please update your e-mail address now by filling it out below.'),
    				
    				);
    }
	#### show popup for twitter customer which not changed email address  #####
	
public  function hookLeftColumn($params)
	{
		$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;
		
		
		$data_fb = $this->getfacebooklib((int)$params['cookie']->id_lang);
		$smarty->assign($this->name.'lang', $data_fb['lng_iso']);
		
    	$cart = $this->context->cart;
    	
    	$is_logged = isset($params['cookie']->id_customer)?$params['cookie']->id_customer:0;
    	
		$smarty->assign(array(
			'cart' => $cart,
			'cart_qties' => $cart->nbProducts(),
			'logged' => $is_logged,
			'customerName' => ($cookie->logged ? $cookie->customer_firstname.' '.$cookie->customer_lastname : false),
			'firstName' => ($cookie->logged ? $cookie->customer_firstname : false),
			'lastName' => ($cookie->logged ? $cookie->customer_lastname : false)
		));
		
		
		$smarty->assign($this->name.'islogged', $is_logged);
		
		include_once(dirname(__FILE__).'/classes/facebookhelp.class.php');
		$obj = new facebookhelp();
		$data_img = $obj->getImages();
		 
		
		####### images and positions ####
		$data_connects_array_prefix = $this->getConnetsArrayPrefix();
		foreach($data_connects_array_prefix as $prefix_short => $prefix_full){
		
			$facebookimg = $data_img[$prefix_full];
			$facebooksmallimg = $data_img[$prefix_full.'small'];
			$facebookimglarge_small = $data_img[$prefix_full.'large_small'];
			$facebookimgmicro_small = $data_img[$prefix_full.'micro_small'];
				
			$array_f_head = array("leftcolumn");
				
			foreach($array_f_head as $prefix_hook){
		
				switch(Configuration::get(($this->name.'sz'.$prefix_hook.$prefix_short))){
					case 'l'.$prefix_hook.$prefix_short:
						$facebook_img = $facebookimg;
						break;
					case 'ls'.$prefix_hook.$prefix_short:
						$facebook_img = $facebookimglarge_small;
						break;
					case 's'.$prefix_hook.$prefix_short:
						$facebook_img = $facebooksmallimg;
						break;
					case 'sm'.$prefix_hook.$prefix_short:
						$facebook_img = $facebookimgmicro_small;
						break;
					default:
						$facebook_img = $facebooksmallimg;
						break;
				}
				$smarty->assign($this->name.$prefix_short.$prefix_hook.'img', $facebook_img);
			}
				
				
				
			
			$smarty->assign($this->name.$prefix_short.'leftimg', $facebook_img);
			 
			$smarty->assign($this->name.$prefix_short.'_on', Configuration::get($this->name.$prefix_short.'_on'));
			
			$smarty->assign($this->name.'_leftcolumn'.$prefix_short, Configuration::get($this->name.'_leftcolumn'.$prefix_short));
		}
		####### images and positions ####
		
		
    	
    	
    
    	// paypal connect
	    	
		$clientid = Configuration::get($this->name.'clientid');
		$psecret = Configuration::get($this->name.'psecret');
		$pcallback = Configuration::get($this->name.'pcallback');
		if(Tools::strlen($clientid)>0 && Tools::strlen($psecret)>0 && Tools::strlen($pcallback)>0){
			$smarty->assign($this->name.'pconf', 1);
    	} else {
    		$smarty->assign($this->name.'pconf', 0);
    	}
    	// paypal connect
    	
    	// twitter connect
			
    	$consumer_key = Configuration::get($this->name.'twitterconskey');
		$consumer_secret = Configuration::get($this->name.'twitterconssecret');
		if(Tools::strlen($consumer_key)>0 && Tools::strlen($consumer_secret)>0){
			$smarty->assign($this->name.'tconf', 1);
    	} else {
    		$smarty->assign($this->name.'tconf', 0);
    	}
    	// twitter connect
    	
    	// linkedin connect
		
		$lapikey = Configuration::get($this->name.'lapikey');
		$lsecret = Configuration::get($this->name.'lsecret');
		
		if(Tools::strlen($lapikey)>0 && Tools::strlen($lsecret)>0){
			$smarty->assign($this->name.'lconf', 1);
    	} else {
    		$smarty->assign($this->name.'lconf', 0);
    	}
    	
    	// linkedin connect
    		### set variables for order page ####
		$this->getOrderPage();
    	### set variables for order page #### 
    	
    		
    	$smarty->assign($this->name.'http_referer', $this->_http_referer);
    	$smarty->assign($this->name.'is15', $this->_is15);
    	
		
    	// microsoft connect
		    	
		$mclientid = Configuration::get($this->name.'mclientid');
		$mclientsecret = Configuration::get($this->name.'mclientsecret');
		
		if(Tools::strlen($mclientid)>0 && Tools::strlen($mclientsecret)>0){
			$smarty->assign($this->name.'mconf', 1);
    	} else {
    		$smarty->assign($this->name.'mconf', 0);
    	}
    	
    	// microsoft connect
    	
    	
    	
    	 
    	// instagram connect
    	$ici = Configuration::get($this->name.'ici');
    	$ics = Configuration::get($this->name.'ics');
    	$iru = Configuration::get($this->name.'iru');
    	 
    	if(Tools::strlen($ici)>0 && Tools::strlen($ics)>0 && Tools::strlen($iru)>0){
    		$smarty->assign($this->name.'iconf', 1);
    	} else {
    		$smarty->assign($this->name.'iconf', 0);
    	}
    	// instagram connect
    	
    	
    	// google connect
    	$oci = Configuration::get($this->name.'oci');
		$ocs = Configuration::get($this->name.'ocs');
		$oru = Configuration::get($this->name.'oru');
		
		if(Tools::strlen($oci)>0 && Tools::strlen($ocs)>0 && Tools::strlen($oru)>0){
			$smarty->assign($this->name.'gconf', 1);
    	} else {
    		$smarty->assign($this->name.'gconf', 0);
    	}
    	// google connect
    	
    	
    	
    	// foursquare connect
    	$fsci = Configuration::get($this->name.'fsci');
    	$fscs = Configuration::get($this->name.'fscs');
    	$fsru = Configuration::get($this->name.'fsru');
    	 
    	if(Tools::strlen($fsci)>0 && Tools::strlen($fscs)>0 && Tools::strlen($fsru)>0){
    		$smarty->assign($this->name.'fsconf', 1);
    	} else {
    		$smarty->assign($this->name.'fsconf', 0);
    	}
    	// foursquare connect
    	
    	
    	// github connect
    	$gici = Configuration::get($this->name.'gici');
    	$gics = Configuration::get($this->name.'gics');
    	$giru = Configuration::get($this->name.'giru');
    	
    	if(Tools::strlen($gici)>0 && Tools::strlen($gics)>0 && Tools::strlen($giru)>0){
    		$smarty->assign($this->name.'giconf', 1);
    	} else {
    		$smarty->assign($this->name.'giconf', 0);
    	}
    	// github connect
    	
    	
    	// disqus connect
    	$dci = Configuration::get($this->name.'dci');
    	$dcs = Configuration::get($this->name.'dcs');
    	$dru = Configuration::get($this->name.'dru');
    	 
    	if(Tools::strlen($dci)>0 && Tools::strlen($dcs)>0 && Tools::strlen($dru)>0){
    		$smarty->assign($this->name.'dconf', 1);
    	} else {
    		$smarty->assign($this->name.'dconf', 0);
    	}
    	// disqus connect
    	
    	
    	// amazon connect
    	$aci = Configuration::get($this->name.'aci');
    	$aru = Configuration::get($this->name.'aru');
    	$smarty->assign($this->name.'amazonci',$aci);
    	 
    	if(Tools::strlen($aci)>0 && Tools::strlen($aru)>0){
    		$smarty->assign($this->name.'aconf', 1);
    	} else {
    		$smarty->assign($this->name.'aconf', 0);
    	}
    	// amazon connect
    	
    	$data_errors = $this->_translations;
    	$smarty->assign('gerror', $data_errors['google']);
    	$smarty->assign('ferror', $data_errors['facebook']);
    	$smarty->assign('terror', $data_errors['twitter']);
    	$smarty->assign('lerror', $data_errors['linkedin']);
    	$smarty->assign('merror', $data_errors['microsoft']);
    	$smarty->assign('ierror', $data_errors['instagram']);
    	$smarty->assign('fserror', $data_errors['foursquare']);
    	$smarty->assign('gierror', $data_errors['github']);
    	$smarty->assign('derror', $data_errors['disqus']);
    	$smarty->assign('aerror', $data_errors['amazon']);
    	
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			return $this->display(__FILE__, 'views/templates/hooks/left15.tpl');
		} else {
			return $this->display(__FILE__, 'views/templates/hooks/left.tpl');
		}

		
	}
	
	public function hookRightColumn($params)
	{
		$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;
		
		
		$data_fb = $this->getfacebooklib((int)$params['cookie']->id_lang);
		$smarty->assign($this->name.'lang', $data_fb['lng_iso']);
			
    	$cart = $this->context->cart;
    	
    	$is_logged = isset($params['cookie']->id_customer)?$params['cookie']->id_customer:0;
		
		$smarty->assign(array(
			'cart' => $cart,
			'cart_qties' => $cart->nbProducts(),
			'logged' => $is_logged,
			'customerName' => ($cookie->logged ? $cookie->customer_firstname.' '.$cookie->customer_lastname : false),
			'firstName' => ($cookie->logged ? $cookie->customer_firstname : false),
			'lastName' => ($cookie->logged ? $cookie->customer_lastname : false)
		));
		
		include_once(dirname(__FILE__).'/classes/facebookhelp.class.php');
		$obj = new facebookhelp();
		$data_img = $obj->getImages();
		
		
		####### images and positions ####
		$data_connects_array_prefix = $this->getConnetsArrayPrefix();
		foreach($data_connects_array_prefix as $prefix_short => $prefix_full){
		
			$facebookimg = $data_img[$prefix_full];
			$facebooksmallimg = $data_img[$prefix_full.'small'];
			$facebookimglarge_small = $data_img[$prefix_full.'large_small'];
			$facebookimgmicro_small = $data_img[$prefix_full.'micro_small'];
		
			$array_f_head = array("rightcolumn");
		
			foreach($array_f_head as $prefix_hook){
		
				switch(Configuration::get(($this->name.'sz'.$prefix_hook.$prefix_short))){
					case 'l'.$prefix_hook.$prefix_short:
						$facebook_img = $facebookimg;
						break;
					case 'ls'.$prefix_hook.$prefix_short:
						$facebook_img = $facebookimglarge_small;
						break;
					case 's'.$prefix_hook.$prefix_short:
						$facebook_img = $facebooksmallimg;
						break;
					case 'sm'.$prefix_hook.$prefix_short:
						$facebook_img = $facebookimgmicro_small;
						break;
					default:
						$facebook_img = $facebooksmallimg;
						break;
				}
				$smarty->assign($this->name.$prefix_short.$prefix_hook.'img', $facebook_img);
			}
		
		
		
				
			$smarty->assign($this->name.$prefix_short.'rightimg', $facebook_img);
		
			$smarty->assign($this->name.$prefix_short.'_on', Configuration::get($this->name.$prefix_short.'_on'));
				
			$smarty->assign($this->name.'_rightcolumn'.$prefix_short, Configuration::get($this->name.'_rightcolumn'.$prefix_short));
		}
		####### images and positions ####
		
		
		
		    	
    	// paypal connect
				
    	$clientid = Configuration::get($this->name.'clientid');
		$psecret = Configuration::get($this->name.'psecret');
		$pcallback = Configuration::get($this->name.'pcallback');
		if(Tools::strlen($clientid)>0 && Tools::strlen($psecret)>0 && Tools::strlen($pcallback)>0){
			$smarty->assign($this->name.'pconf', 1);
    	} else {
    		$smarty->assign($this->name.'pconf', 0);
    	}
    	// paypal connect
    	
    	// twitter connect
		$consumer_key = Configuration::get($this->name.'twitterconskey');
		$consumer_secret = Configuration::get($this->name.'twitterconssecret');
		if(Tools::strlen($consumer_key)>0 && Tools::strlen($consumer_secret)>0){
			$smarty->assign($this->name.'tconf', 1);
    	} else {
    		$smarty->assign($this->name.'tconf', 0);
    	}
    	// twitter connect
    	
    	// linkedin connect
			
		$lapikey = Configuration::get($this->name.'lapikey');
		$lsecret = Configuration::get($this->name.'lsecret');
		
		if(Tools::strlen($lapikey)>0 && Tools::strlen($lsecret)>0){
			$smarty->assign($this->name.'lconf', 1);
    	} else {
    		$smarty->assign($this->name.'lconf', 0);
    	}
    	
    	// linkedin connect
		
    	$smarty->assign($this->name.'islogged', $is_logged);
		
    	### set variables for order page ####
		$this->getOrderPage();
    	### set variables for order page #### 
    	
    
    	$smarty->assign($this->name.'http_referer', $this->_http_referer);
    	$smarty->assign($this->name.'is15', $this->_is15);
    	
    	
    	// microsoft connect
		
		$mclientid = Configuration::get($this->name.'mclientid');
		$mclientsecret = Configuration::get($this->name.'mclientsecret');
		
		if(Tools::strlen($mclientid)>0 && Tools::strlen($mclientsecret)>0){
			$smarty->assign($this->name.'mconf', 1);
    	} else {
    		$smarty->assign($this->name.'mconf', 0);
    	}
    	
    	// microsoft connect
    	
    	
    	// instagram connect
    	
    	// instagram connect
    	$ici = Configuration::get($this->name.'ici');
    	$ics = Configuration::get($this->name.'ics');
    	$iru = Configuration::get($this->name.'iru');
    	 
    	if(Tools::strlen($ici)>0 && Tools::strlen($ics)>0 && Tools::strlen($iru)>0){
    		$smarty->assign($this->name.'iconf', 1);
    	} else {
    		$smarty->assign($this->name.'iconf', 0);
    	}
    	// instagram connect
    	
    	
    	// google connect
    	$oci = Configuration::get($this->name.'oci');
		$ocs = Configuration::get($this->name.'ocs');
		$oru = Configuration::get($this->name.'oru');
		
		if(Tools::strlen($oci)>0 && Tools::strlen($ocs)>0 && Tools::strlen($oru)>0){
			$smarty->assign($this->name.'gconf', 1);
    	} else {
    		$smarty->assign($this->name.'gconf', 0);
    	}
    	// google connect
    	
    	
    	// foursquare connect
    	$fsci = Configuration::get($this->name.'fsci');
    	$fscs = Configuration::get($this->name.'fscs');
    	$fsru = Configuration::get($this->name.'fsru');
    	
    	if(Tools::strlen($fsci)>0 && Tools::strlen($fscs)>0 && Tools::strlen($fsru)>0){
    		$smarty->assign($this->name.'fsconf', 1);
    	} else {
    		$smarty->assign($this->name.'fsconf', 0);
    	}
    	// foursquare connect
    	
    	
    	// github connect
    	$gici = Configuration::get($this->name.'gici');
    	$gics = Configuration::get($this->name.'gics');
    	$giru = Configuration::get($this->name.'giru');
    	 
    	if(Tools::strlen($gici)>0 && Tools::strlen($gics)>0 && Tools::strlen($giru)>0){
    		$smarty->assign($this->name.'giconf', 1);
    	} else {
    		$smarty->assign($this->name.'giconf', 0);
    	}
    	// github connect
    	
    	
    	// disqus connect
    	$dci = Configuration::get($this->name.'dci');
    	$dcs = Configuration::get($this->name.'dcs');
    	$dru = Configuration::get($this->name.'dru');
    	
    	if(Tools::strlen($dci)>0 && Tools::strlen($dcs)>0 && Tools::strlen($dru)>0){
    		$smarty->assign($this->name.'dconf', 1);
    	} else {
    		$smarty->assign($this->name.'dconf', 0);
    	}
    	// disqus connect
    	
    	// amazon connect
    	$aci = Configuration::get($this->name.'aci');
    	$aru = Configuration::get($this->name.'aru');
    	$smarty->assign($this->name.'amazonci',$aci);
    	
    	if(Tools::strlen($aci)>0 && Tools::strlen($aru)>0){
    		$smarty->assign($this->name.'aconf', 1);
    	} else {
    		$smarty->assign($this->name.'aconf', 0);
    	}
    	// amazon connect
    	
    	
    	
    	$data_errors = $this->_translations;
    	$smarty->assign('gerror', $data_errors['google']);
    	$smarty->assign('ferror', $data_errors['facebook']);
    	$smarty->assign('terror', $data_errors['twitter']);
    	$smarty->assign('lerror', $data_errors['linkedin']);
    	$smarty->assign('merror', $data_errors['microsoft']);
    	$smarty->assign('ierror', $data_errors['instagram']);
    	$smarty->assign('fserror', $data_errors['foursquare']);
    	$smarty->assign('gierror', $data_errors['github']);
    	$smarty->assign('derror', $data_errors['disqus']);
    	$smarty->assign('aerror', $data_errors['amazon']);
    	
    	
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			return $this->display(__FILE__, 'views/templates/hooks/right15.tpl');
		} else {
			return $this->display(__FILE__, 'views/templates/hooks/right.tpl');
		}		
	}
	

	
    
    public function getfacebooklib($id_lang){
    	
    	$lang = new Language((int)$id_lang);
		
    	$lng_code = isset($lang->language_code)?$lang->language_code:$lang->iso_code;
    	if(strstr($lng_code, '-')){
			$res = explode('-', $lng_code);
			$language_iso = Tools::strtolower($res[0]).'_'.Tools::strtoupper($res[1]);
		} else {
			$language_iso = Tools::strtolower($lng_code).'_'.Tools::strtoupper($lng_code);
		}
			
			
		if (!in_array($language_iso, $this->getfacebooklocale()))
			$language_iso = "en_US";
		
		if (Configuration::get('PS_SSL_ENABLED') == 1)
			$url = "https://";
		else
			$url = "http://";
		
		return array('url'=>$url . 'connect.facebook.net/'.$language_iso.'/all.js#xfbml=1',
					  'lng_iso' => $language_iso);
    }
    
	public function getfacebooklocale()
	{
		$locales = array();

		if (($xml=simplexml_load_file(_PS_MODULE_DIR_ . $this->name."/lib/facebook_locales.xml")) === false)
			return $locales;
			
		$result = $xml->xpath('/locales/locale/codes/code/standard/representation');

		foreach ($result as $locale)
		{
			//list($k, $node) = each($locale);
			//$locales[] = $node;
			$locales[] = @current($locale);
		}
			
		return $locales;
	}
    
	
	public function getContent()
    {
    	$cookie = $this->context->cookie;
		$currentIndex = $this->context->currentindex;
    	
    	$this->_html = '';
    	
    	$this->_html .= $this->_headercssfiles();
    	
    	include_once(dirname(__FILE__).'/classes/facebookhelp.class.php');
		$obj = new facebookhelp();
			
     if (Tools::isSubmit('submitbasic'))
        {
        	
        	
        	Configuration::updateValue($this->name.'defaultgroup', Tools::getValue('defaultgroup'));
        	
        	$languages = Language::getLanguages(false);
        	foreach ($languages as $language){
        		$i = $language['id_lang'];
        		Configuration::updateValue($this->name.'authp_'.$i, Tools::getValue('authp_'.$i));
        	}
        	 
        	$prefix = "txt";
        	Configuration::updateValue($this->name.'_top'.$prefix, Tools::getValue('top'.$prefix));
        	Configuration::updateValue($this->name.'_footer'.$prefix, Tools::getValue('footer'.$prefix));
        	Configuration::updateValue($this->name.'_authpage'.$prefix, Tools::getValue('authpage'.$prefix));
        	
        	
        	
        	Configuration::updateValue($this->name.'iauth', Tools::getValue('iauth'));
        	
        	$languages = Language::getLanguages(false);
        	foreach ($languages as $language){
        		$i = $language['id_lang'];
        		Configuration::updateValue($this->name.'txtauthp_'.$i, Tools::getValue('txtauthp_'.$i));
        	}
        	
			$this->_html .= '<script>init_tabs(9);</script>';
		}
		
		
		 if (Tools::isSubmit('submity'))
        {
        	
        	
        	
        	// yahoo connect
       	    Configuration::updateValue($this->name.'y_on', Tools::getValue('y_on'));
		 		
        	Configuration::updateValue($this->name.'_topy', Tools::getValue('topy'));
        	Configuration::updateValue($this->name.'_rightcolumny', Tools::getValue('rightcolumny'));
        	Configuration::updateValue($this->name.'_leftcolumny', Tools::getValue('leftcolumny'));
        	Configuration::updateValue($this->name.'_footery', Tools::getValue('footery'));
        	Configuration::updateValue($this->name.'_authpagey', Tools::getValue('authpagey'));
        	Configuration::updateValue($this->name.'_welcomey', Tools::getValue('welcomey'));
        	
        	$prefix = "y";
        	Configuration::updateValue($this->name.'sztop'.$prefix, Tools::getValue('sztop'.$prefix));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix, Tools::getValue('szrightcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix, Tools::getValue('szleftcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szfooter'.$prefix, Tools::getValue('szfooter'.$prefix));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix, Tools::getValue('szauthpage'.$prefix));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix, Tools::getValue('szwelcome'.$prefix));
        	
        	// save yahoo connect image	
			if(!empty($_FILES['post_image_yahoo']['name'])){
				$obj->saveImage(array('type'=>'yahoo'));
			}
			
	        // save yahoo connect small image	
			if(!empty($_FILES['post_image_yahoosmall']['name'])){
				$obj->saveImage(array('type'=>'yahoosmall'));
			}
			
			// save yahoo connect large_small image
			if(!empty($_FILES['post_image_yahoolarge_small']['name'])){
				$obj->saveImage(array('type'=>'yahoolarge_small'));
			}
				
			// save yahoo connect micro_small image
			if(!empty($_FILES['post_image_yahoomicro_small']['name'])){
				$obj->saveImage(array('type'=>'yahoomicro_small'));
			}
			
			$this->_html .= '<script>init_tabs(4);</script>';
			// yahoo connect
        }	
			
        if (Tools::isSubmit('submitt'))
        {
       	   // twitter connect
       	    Configuration::updateValue($this->name.'twitterconskey', Tools::getValue('twitterconskey'));
	    	Configuration::updateValue($this->name.'twitterconssecret', Tools::getValue('twitterconssecret'));
	    
        	Configuration::updateValue($this->name.'t_on', Tools::getValue('t_on'));
		 		
        	Configuration::updateValue($this->name.'_topt', Tools::getValue('topt'));
        	Configuration::updateValue($this->name.'_rightcolumnt', Tools::getValue('rightcolumnt'));
        	Configuration::updateValue($this->name.'_leftcolumnt', Tools::getValue('leftcolumnt'));
        	Configuration::updateValue($this->name.'_footert', Tools::getValue('footert'));
        	Configuration::updateValue($this->name.'_authpaget', Tools::getValue('authpaget'));
        	Configuration::updateValue($this->name.'_welcomet', Tools::getValue('welcomet'));
        	
        	$prefix = "t";
        	Configuration::updateValue($this->name.'sztop'.$prefix, Tools::getValue('sztop'.$prefix));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix, Tools::getValue('szrightcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix, Tools::getValue('szleftcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szfooter'.$prefix, Tools::getValue('szfooter'.$prefix));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix, Tools::getValue('szauthpage'.$prefix));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix, Tools::getValue('szwelcome'.$prefix));
        	
			// save twitter connect image	
			if(!empty($_FILES['post_image_twitter']['name'])){
				$obj->saveImage(array('type'=>'twitter'));	
			}
			
	        // save twitter connect small image	
			if(!empty($_FILES['post_image_twittersmall']['name'])){
				$obj->saveImage(array('type'=>'twittersmall'));	
			}
			
			
			// save twitter connect large_small image
			if(!empty($_FILES['post_image_twitterlarge_small']['name'])){
				$obj->saveImage(array('type'=>'twitterlarge_small'));
			}
			
			// save twitter connect micro_small image
			if(!empty($_FILES['post_image_twittermicro_small']['name'])){
				$obj->saveImage(array('type'=>'twittermicro_small'));
			}
			
			
			
        	$this->_html .= '<script>init_tabs(2);</script>';
			
        	
        	
        	// twitter connect
        }
        	
        if (Tools::isSubmit('submitf'))
        {
       	
        	// facebook connect
        	Configuration::updateValue($this->name.'appid', Tools::getValue('appid'));
        	Configuration::updateValue($this->name.'secret', Tools::getValue('secret'));
        	
        	Configuration::updateValue($this->name.'f_on', Tools::getValue('f_on'));
		 		
        	Configuration::updateValue($this->name.'_topf', Tools::getValue('topf'));
        	Configuration::updateValue($this->name.'_rightcolumnf', Tools::getValue('rightcolumnf'));
        	Configuration::updateValue($this->name.'_leftcolumnf', Tools::getValue('leftcolumnf'));
        	Configuration::updateValue($this->name.'_footerf', Tools::getValue('footerf'));
        	Configuration::updateValue($this->name.'_authpagef', Tools::getValue('authpagef'));
        	Configuration::updateValue($this->name.'_welcomef', Tools::getValue('welcomef'));
        	
		 	$prefix = "f";
        	Configuration::updateValue($this->name.'sztop'.$prefix, Tools::getValue('sztop'.$prefix));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix, Tools::getValue('szrightcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix, Tools::getValue('szleftcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szfooter'.$prefix, Tools::getValue('szfooter'.$prefix));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix, Tools::getValue('szauthpage'.$prefix));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix, Tools::getValue('szwelcome'.$prefix));
        	 
        	
			
			// save facebook connect image	
			if(!empty($_FILES['post_image_facebook']['name'])){
				$obj->saveImage(array('type'=>'facebook'));	
			}
			
	        // save facebook connect small image	
			if(!empty($_FILES['post_image_facebooksmall']['name'])){
				$obj->saveImage(array('type'=>'facebooksmall'));	
			}
			
			
			// save facebook connect large_small image
			if(!empty($_FILES['post_image_facebooklarge_small']['name'])){
				$obj->saveImage(array('type'=>'facebooklarge_small'));
			}
			
			// save facebook connect micro_small image
			if(!empty($_FILES['post_image_facebookmicro_small']['name'])){
				$obj->saveImage(array('type'=>'facebookmicro_small'));
			}
			
			$this->_html .= '<script>init_tabs(8);</script>';
			
			
        }

        if (Tools::isSubmit('submitg'))
        {
        
        	// google connect
        	Configuration::updateValue($this->name.'g_on', Tools::getValue('g_on'));
		 		
        	Configuration::updateValue($this->name.'_topg', Tools::getValue('topg'));
        	Configuration::updateValue($this->name.'_rightcolumng', Tools::getValue('rightcolumng'));
        	Configuration::updateValue($this->name.'_leftcolumng', Tools::getValue('leftcolumng'));
        	Configuration::updateValue($this->name.'_footerg', Tools::getValue('footerg'));
        	Configuration::updateValue($this->name.'_authpageg', Tools::getValue('authpageg'));
        	Configuration::updateValue($this->name.'_welcomeg', Tools::getValue('welcomeg'));
        	
        	
        	$prefix = "g";
        	Configuration::updateValue($this->name.'sztop'.$prefix, Tools::getValue('sztop'.$prefix));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix, Tools::getValue('szrightcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix, Tools::getValue('szleftcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szfooter'.$prefix, Tools::getValue('szfooter'.$prefix));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix, Tools::getValue('szauthpage'.$prefix));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix, Tools::getValue('szwelcome'.$prefix));
        	
        	// changes OAuth 2.0
	 	
		 	Configuration::updateValue($this->name.'oci', Tools::getValue('oci'));
        	Configuration::updateValue($this->name.'ocs', Tools::getValue('ocs'));
        	Configuration::updateValue($this->name.'oru', Tools::getValue('oru'));
        	
        	// changes OAuth 2.0
	 	
			
			// save google connect image	
			if(!empty($_FILES['post_image_google']['name'])){
				$obj->saveImage(array('type'=>'google'));	
			}
			
	        // save google connect image	
			if(!empty($_FILES['post_image_googlesmall']['name'])){
				$obj->saveImage(array('type'=>'googlesmall'));	
			}
			
			// save google connect large_small image
			if(!empty($_FILES['post_image_googlelarge_small']['name'])){
				$obj->saveImage(array('type'=>'googlelarge_small'));
			}
				
			// save google connect micro_small image
			if(!empty($_FILES['post_image_googlemicro_small']['name'])){
				$obj->saveImage(array('type'=>'googlemicro_small'));
			}
			
			$this->_html .= '<script>init_tabs(3);</script>';
        }
        	
        

         if (Tools::isSubmit('submitl'))
        {
			// linkedin connect
        	
			Configuration::updateValue($this->name.'lapikey', Tools::getValue('lapikey'));
        	Configuration::updateValue($this->name.'lsecret', Tools::getValue('lsecret'));
			
        	Configuration::updateValue($this->name.'l_on', Tools::getValue('l_on'));
		 		
        	Configuration::updateValue($this->name.'_topl', Tools::getValue('topl'));
        	Configuration::updateValue($this->name.'_rightcolumnl', Tools::getValue('rightcolumnl'));
        	Configuration::updateValue($this->name.'_leftcolumnl', Tools::getValue('leftcolumnl'));
        	Configuration::updateValue($this->name.'_footerl', Tools::getValue('footerl'));
        	Configuration::updateValue($this->name.'_authpagel', Tools::getValue('authpagel'));
        	Configuration::updateValue($this->name.'_welcomel', Tools::getValue('welcomel'));
        	
		 	
        	$prefix = "l";
        	Configuration::updateValue($this->name.'sztop'.$prefix, Tools::getValue('sztop'.$prefix));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix, Tools::getValue('szrightcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix, Tools::getValue('szleftcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szfooter'.$prefix, Tools::getValue('szfooter'.$prefix));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix, Tools::getValue('szauthpage'.$prefix));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix, Tools::getValue('szwelcome'.$prefix));
        	
        	
			// save linkedin connect image	
			if(!empty($_FILES['post_image_linkedin']['name'])){
				$obj->saveImage(array('type'=>'linkedin'));	
			}
			
	        // save linkedin connect small image	
			if(!empty($_FILES['post_image_linkedinsmall']['name'])){
				$obj->saveImage(array('type'=>'linkedinsmall'));	
			}
			
			// save linkedin connect large_small image
			if(!empty($_FILES['post_image_linkedinlarge_small']['name'])){
				$obj->saveImage(array('type'=>'linkedinlarge_small'));
			}
			
			// save linkedin connect micro_small image
			if(!empty($_FILES['post_image_linkedinmicro_small']['name'])){
				$obj->saveImage(array('type'=>'linkedinmicro_small'));
			}
			
			$this->_html .= '<script>init_tabs(6);</script>';
        }
			
         if (Tools::isSubmit('submitm'))
        {
        	// microsoft connect
        	
			Configuration::updateValue($this->name.'mclientid', Tools::getValue('mclientid'));
        	Configuration::updateValue($this->name.'mclientsecret', Tools::getValue('mclientsecret'));
			
        	Configuration::updateValue($this->name.'m_on', Tools::getValue('m_on'));
		 		
        	Configuration::updateValue($this->name.'_topm', Tools::getValue('topm'));
        	Configuration::updateValue($this->name.'_rightcolumnm', Tools::getValue('rightcolumnm'));
        	Configuration::updateValue($this->name.'_leftcolumnm', Tools::getValue('leftcolumnm'));
        	Configuration::updateValue($this->name.'_footerm', Tools::getValue('footerm'));
        	Configuration::updateValue($this->name.'_authpagem', Tools::getValue('authpagem'));
        	Configuration::updateValue($this->name.'_welcomem', Tools::getValue('welcomem'));
        	
        	$prefix = "m";
        	Configuration::updateValue($this->name.'sztop'.$prefix, Tools::getValue('sztop'.$prefix));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix, Tools::getValue('szrightcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix, Tools::getValue('szleftcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szfooter'.$prefix, Tools::getValue('szfooter'.$prefix));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix, Tools::getValue('szauthpage'.$prefix));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix, Tools::getValue('szwelcome'.$prefix));
			
			// save microsoft connect image	
			if(!empty($_FILES['post_image_microsoft']['name'])){
				$obj->saveImage(array('type'=>'microsoft'));	
			}
			
	        // save microsoft connect small image	
			if(!empty($_FILES['post_image_microsoftsmall']['name'])){
				$obj->saveImage(array('type'=>'microsoftsmall'));	
			}
			
			// save microsoft connect large_small image
			if(!empty($_FILES['post_image_microsoftlarge_small']['name'])){
				$obj->saveImage(array('type'=>'microsoftlarge_small'));
			}
				
			// save microsoft connect micro_small image
			if(!empty($_FILES['post_image_microsoftmicro_small']['name'])){
				$obj->saveImage(array('type'=>'microsoftmicro_small'));
			}
			
			$this->_html .= '<script>init_tabs(7);</script>';
        }
        
        
        if (Tools::isSubmit('submiti'))
        {
        
        	// instagram connect
        	Configuration::updateValue($this->name.'i_on', Tools::getValue('i_on'));
        	 
        	Configuration::updateValue($this->name.'_topi', Tools::getValue('topi'));
        	Configuration::updateValue($this->name.'_rightcolumni', Tools::getValue('rightcolumni'));
        	Configuration::updateValue($this->name.'_leftcolumni', Tools::getValue('leftcolumni'));
        	Configuration::updateValue($this->name.'_footeri', Tools::getValue('footeri'));
        	Configuration::updateValue($this->name.'_authpagei', Tools::getValue('authpagei'));
        	Configuration::updateValue($this->name.'_welcomei', Tools::getValue('welcomei'));
        	 
        	$prefix = "i";
        	Configuration::updateValue($this->name.'sztop'.$prefix, Tools::getValue('sztop'.$prefix));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix, Tools::getValue('szrightcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix, Tools::getValue('szleftcolumn'.$prefix));
        	Configuration::updateValue($this->name.'szfooter'.$prefix, Tools::getValue('szfooter'.$prefix));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix, Tools::getValue('szauthpage'.$prefix));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix, Tools::getValue('szwelcome'.$prefix));
        	
        	// changes OAuth 2.0
        	 
        	Configuration::updateValue($this->name.'ici', Tools::getValue('ici'));
        	Configuration::updateValue($this->name.'ics', Tools::getValue('ics'));
        	Configuration::updateValue($this->name.'iru', Tools::getValue('iru'));
        	 
        	// changes OAuth 2.0
        	 
        		
        	// save instagram connect image
        	if(!empty($_FILES['post_image_instagram']['name'])){
        		$obj->saveImage(array('type'=>'instagram'));
        	}
        		
        	// save instagram connect image
        	if(!empty($_FILES['post_image_instagramsmall']['name'])){
        		$obj->saveImage(array('type'=>'instagramsmall'));
        	}
        	
        	// save instagram connect large_small image
        	if(!empty($_FILES['post_image_instagramlarge_small']['name'])){
        		$obj->saveImage(array('type'=>'instagramlarge_small'));
        	}
        	
        	// save instagram connect micro_small image
        	if(!empty($_FILES['post_image_instagrammicro_small']['name'])){
        		$obj->saveImage(array('type'=>'instagrammicro_small'));
        	}
        	
        	$this->_html .= '<script>init_tabs(11);</script>';
        }
        
        
        $prefix_fs = "fs";
        if (Tools::isSubmit('submit'.$prefix_fs))
        {
        
        	// foursquare connect
        	Configuration::updateValue($this->name.$prefix_fs.'_on', Tools::getValue($prefix_fs.'_on'));
        
        	Configuration::updateValue($this->name.'_top'.$prefix_fs, Tools::getValue('top'.$prefix_fs));
        	Configuration::updateValue($this->name.'_rightcolumn'.$prefix_fs, Tools::getValue('rightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_leftcolumn'.$prefix_fs, Tools::getValue('leftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_footer'.$prefix_fs, Tools::getValue('footer'.$prefix_fs));
        	Configuration::updateValue($this->name.'_authpage'.$prefix_fs, Tools::getValue('authpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'_welcome'.$prefix_fs, Tools::getValue('welcome'.$prefix_fs));
        
        	Configuration::updateValue($this->name.'sztop'.$prefix_fs, Tools::getValue('sztop'.$prefix_fs));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix_fs, Tools::getValue('szrightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix_fs, Tools::getValue('szleftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szfooter'.$prefix_fs, Tools::getValue('szfooter'.$prefix_fs));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix_fs, Tools::getValue('szauthpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix_fs, Tools::getValue('szwelcome'.$prefix_fs));
        	 
        	
        	Configuration::updateValue($this->name.$prefix_fs.'ci', Tools::getValue($prefix_fs.'ci'));
        	Configuration::updateValue($this->name.$prefix_fs.'cs', Tools::getValue($prefix_fs.'cs'));
        	Configuration::updateValue($this->name.$prefix_fs.'ru', Tools::getValue($prefix_fs.'ru'));
        
        	
        
        	// save foursquare connect image
        	if(!empty($_FILES['post_image_foursquare']['name'])){
        		$obj->saveImage(array('type'=>'foursquare'));
        	}
        
        	// save foursquare connect image
        	if(!empty($_FILES['post_image_foursquaresmall']['name'])){
        		$obj->saveImage(array('type'=>'foursquaresmall'));
        	}
        	 
        	// save foursquare connect large_small image
        	if(!empty($_FILES['post_image_foursquarelarge_small']['name'])){
        		$obj->saveImage(array('type'=>'foursquarelarge_small'));
        	}
        	 
        	// save foursquare connect micro_small image
        	if(!empty($_FILES['post_image_foursquaremicro_small']['name'])){
        		$obj->saveImage(array('type'=>'foursquaremicro_small'));
        	}
        	 
        	$this->_html .= '<script>init_tabs(12);</script>';
        }
        
        
        $prefix_fs = "gi";
        if (Tools::isSubmit('submit'.$prefix_fs))
        {
        
        	// github connect
        	Configuration::updateValue($this->name.$prefix_fs.'_on', Tools::getValue($prefix_fs.'_on'));
        
        	Configuration::updateValue($this->name.'_top'.$prefix_fs, Tools::getValue('top'.$prefix_fs));
        	Configuration::updateValue($this->name.'_rightcolumn'.$prefix_fs, Tools::getValue('rightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_leftcolumn'.$prefix_fs, Tools::getValue('leftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_footer'.$prefix_fs, Tools::getValue('footer'.$prefix_fs));
        	Configuration::updateValue($this->name.'_authpage'.$prefix_fs, Tools::getValue('authpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'_welcome'.$prefix_fs, Tools::getValue('welcome'.$prefix_fs));
        
        	Configuration::updateValue($this->name.'sztop'.$prefix_fs, Tools::getValue('sztop'.$prefix_fs));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix_fs, Tools::getValue('szrightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix_fs, Tools::getValue('szleftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szfooter'.$prefix_fs, Tools::getValue('szfooter'.$prefix_fs));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix_fs, Tools::getValue('szauthpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix_fs, Tools::getValue('szwelcome'.$prefix_fs));
        
        	 
        	Configuration::updateValue($this->name.$prefix_fs.'ci', Tools::getValue($prefix_fs.'ci'));
        	Configuration::updateValue($this->name.$prefix_fs.'cs', Tools::getValue($prefix_fs.'cs'));
        	Configuration::updateValue($this->name.$prefix_fs.'ru', Tools::getValue($prefix_fs.'ru'));
        
        	 
        
        	// save github connect image
        	if(!empty($_FILES['post_image_github']['name'])){
        		$obj->saveImage(array('type'=>'github'));
        	}
        
        	// save github connect image
        	if(!empty($_FILES['post_image_githubsmall']['name'])){
        		$obj->saveImage(array('type'=>'githubsmall'));
        	}
        
        	// save github connect large_small image
        	if(!empty($_FILES['post_image_githublarge_small']['name'])){
        		$obj->saveImage(array('type'=>'githublarge_small'));
        	}
        
        	// save github connect micro_small image
        	if(!empty($_FILES['post_image_githubmicro_small']['name'])){
        		$obj->saveImage(array('type'=>'githubmicro_small'));
        	}
        
        	$this->_html .= '<script>init_tabs(13);</script>';
        }
        
        
        
        $prefix_fs = "d";
        if (Tools::isSubmit('submit'.$prefix_fs))
        {
        
        	// disqus connect
        	Configuration::updateValue($this->name.$prefix_fs.'_on', Tools::getValue($prefix_fs.'_on'));
        
        	Configuration::updateValue($this->name.'_top'.$prefix_fs, Tools::getValue('top'.$prefix_fs));
        	Configuration::updateValue($this->name.'_rightcolumn'.$prefix_fs, Tools::getValue('rightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_leftcolumn'.$prefix_fs, Tools::getValue('leftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_footer'.$prefix_fs, Tools::getValue('footer'.$prefix_fs));
        	Configuration::updateValue($this->name.'_authpage'.$prefix_fs, Tools::getValue('authpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'_welcome'.$prefix_fs, Tools::getValue('welcome'.$prefix_fs));
        
        	Configuration::updateValue($this->name.'sztop'.$prefix_fs, Tools::getValue('sztop'.$prefix_fs));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix_fs, Tools::getValue('szrightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix_fs, Tools::getValue('szleftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szfooter'.$prefix_fs, Tools::getValue('szfooter'.$prefix_fs));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix_fs, Tools::getValue('szauthpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix_fs, Tools::getValue('szwelcome'.$prefix_fs));
        
        
        	Configuration::updateValue($this->name.$prefix_fs.'ci', Tools::getValue($prefix_fs.'ci'));
        	Configuration::updateValue($this->name.$prefix_fs.'cs', Tools::getValue($prefix_fs.'cs'));
        	Configuration::updateValue($this->name.$prefix_fs.'ru', Tools::getValue($prefix_fs.'ru'));
        
        
        
        	// save disqus connect image
        	if(!empty($_FILES['post_image_disqus']['name'])){
        		$obj->saveImage(array('type'=>'disqus'));
        	}
        
        	// save disqus connect image
        	if(!empty($_FILES['post_image_disqussmall']['name'])){
        		$obj->saveImage(array('type'=>'disqussmall'));
        	}
        
        	// save disqus connect large_small image
        	if(!empty($_FILES['post_image_disquslarge_small']['name'])){
        		$obj->saveImage(array('type'=>'disquslarge_small'));
        	}
        
        	// save disqus connect micro_small image
        	if(!empty($_FILES['post_image_disqusmicro_small']['name'])){
        		$obj->saveImage(array('type'=>'disqusmicro_small'));
        	}
        
        	$this->_html .= '<script>init_tabs(14);</script>';
        }
        
        
        $prefix_fs = "a";
        if (Tools::isSubmit('submit'.$prefix_fs))
        {
        
        	// disqus connect
        	Configuration::updateValue($this->name.$prefix_fs.'_on', Tools::getValue($prefix_fs.'_on'));
        
        	Configuration::updateValue($this->name.'_top'.$prefix_fs, Tools::getValue('top'.$prefix_fs));
        	Configuration::updateValue($this->name.'_rightcolumn'.$prefix_fs, Tools::getValue('rightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_leftcolumn'.$prefix_fs, Tools::getValue('leftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'_footer'.$prefix_fs, Tools::getValue('footer'.$prefix_fs));
        	Configuration::updateValue($this->name.'_authpage'.$prefix_fs, Tools::getValue('authpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'_welcome'.$prefix_fs, Tools::getValue('welcome'.$prefix_fs));
        
        	Configuration::updateValue($this->name.'sztop'.$prefix_fs, Tools::getValue('sztop'.$prefix_fs));
        	Configuration::updateValue($this->name.'szrightcolumn'.$prefix_fs, Tools::getValue('szrightcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szleftcolumn'.$prefix_fs, Tools::getValue('szleftcolumn'.$prefix_fs));
        	Configuration::updateValue($this->name.'szfooter'.$prefix_fs, Tools::getValue('szfooter'.$prefix_fs));
        	Configuration::updateValue($this->name.'szauthpage'.$prefix_fs, Tools::getValue('szauthpage'.$prefix_fs));
        	Configuration::updateValue($this->name.'szwelcome'.$prefix_fs, Tools::getValue('szwelcome'.$prefix_fs));
        
        
        	Configuration::updateValue($this->name.$prefix_fs.'ci', Tools::getValue($prefix_fs.'ci'));
        	Configuration::updateValue($this->name.$prefix_fs.'ru', Tools::getValue($prefix_fs.'ru'));
        
        
        
        	// save amazon connect image
        	if(!empty($_FILES['post_image_amazon']['name'])){
        		$obj->saveImage(array('type'=>'amazon'));
        	}
        
        	// save amazon connect image
        	if(!empty($_FILES['post_image_amazonsmall']['name'])){
        		$obj->saveImage(array('type'=>'amazonsmall'));
        	}
        
        	// save amazon connect large_small image
        	if(!empty($_FILES['post_image_amazonlarge_small']['name'])){
        		$obj->saveImage(array('type'=>'amazonlarge_small'));
        	}
        
        	// save amazon connect micro_small image
        	if(!empty($_FILES['post_image_amazonmicro_small']['name'])){
        		$obj->saveImage(array('type'=>'amazonmicro_small'));
        	}
        
        	$this->_html .= '<script>init_tabs(16);</script>';
        }
        

        if(Tools::isSubmit('cancel_search')){
        	$url = $currentIndex.'&tab=AdminModules&pageitems&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
        	Tools::redirectAdmin($url);
       
        }
        if (Tools::isSubmit('pageitems') || Tools::isSubmit('find') || Tools::isSubmit('search_query')) {
     		$this->_html .= '<script>init_tabs(10);</script>';
        }
    	
        $this->_html .= $this->_displayForm();
        
       
        
        return $this->_html;
    }
    
    

	

    
	
    
private function _displayForm()
     {
     	
     	
     	$_html = '';
     	
     	
     	  
     	$_html .= '
		<fieldset class="display-form">
					<legend><img src="../modules/'.$this->name.'/logo.gif"  />
					'.$this->displayName.':</legend>';
					

     	
     	
     	
		$_html .= '<fieldset class="'.$this->name.'-menu">
			<legend>'.$this->l('Settings').':</legend>
		<ul class="leftMenu">
			<li><a href="javascript:void(0)" onclick="tabs_custom(1)" id="tab-menu-1" class="selected"><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Welcome').'</a></li>
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(9)" id="tab-menu-9"><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Basic Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(8)" id="tab-menu-8"><img src="../modules/'.$this->name.'/views/img/settings_f.png" />'.$this->l('Facebook Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(2)" id="tab-menu-2"><img src="../modules/'.$this->name.'/views/img/settings_t.png"  />'.$this->l('Twitter Settings').'</a></li>
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(16)" id="tab-menu-16"><img src="../modules/'.$this->name.'/views/img/settings_a.png"  />'.$this->l('Amazon Settings').'</a></li>
			
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(3)" id="tab-menu-3"><img src="../modules/'.$this->name.'/views/img/settings_g.png"  />'.$this->l('Google Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(4)" id="tab-menu-4"><img src="../modules/'.$this->name.'/views/img/settings_y.png"  />'.$this->l('Yahoo Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(6)" id="tab-menu-6"><img src="../modules/'.$this->name.'/views/img/settings_l.png"  />'.$this->l('LinkedIn Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(7)" id="tab-menu-7"><img src="../modules/'.$this->name.'/views/img/settings_m.png"  />'.$this->l('Microsoft Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(11)" id="tab-menu-11"><img src="../modules/'.$this->name.'/views/img/settings_i.png"  />'.$this->l('Instargram Settings').'</a></li>
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(12)" id="tab-menu-12"><img src="../modules/'.$this->name.'/views/img/settings_fs.png"  />'.$this->l('Foursquare Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(13)" id="tab-menu-13"><img src="../modules/'.$this->name.'/views/img/settings_gi.png"  />'.$this->l('Github Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(14)" id="tab-menu-14"><img src="../modules/'.$this->name.'/views/img/settings_d.png"  />'.$this->l('Disqus Settings').'</a></li>
			
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(10)" id="tab-menu-10"><img src="../modules/'.$this->name.'/views/img/statistics.png"  />'.$this->l('Statistics').'</a></li>
			<li>&nbsp;</li>
			<li><a href="http://addons.prestashop.com/en/2_community?contributor=61669" target="_blank"><img src="../modules/'.$this->name.'/views/img/spm-logo.png"  />'.$this->l('Other Modules').'</a></li>
			
			</ul>
		</fieldset>
			
			<div class="'.$this->name.'-content">';
				$_html .= '<div id="tabs-1">'.$this->_welcome().'</div>';
				$_html .= '<div id="tabs-9">'.$this->_basicSettings().'</div>';
				$_html .= '<div id="tabs-8">'.$this->_drawFacebookSettingsForm().'</div>';
				$_html .= '<div id="tabs-2">'.$this->_drawTwitterSettingsForm().'</div>';
				
				$_html .= '<div id="tabs-16">'.$this->_drawAmazonSettingsForm().'</div>';
				
				
				$_html .= '<div id="tabs-3">'.$this->_drawGoogleSettingsForm().'</div>';
				$_html .= '<div id="tabs-4">'.$this->_drawYahooSettingsForm().'</div>';
     			$_html .= '<div id="tabs-6">'.$this->_drawLinkedInSettingsForm().'</div>';
     			$_html .= '<div id="tabs-7">'.$this->_drawMicrosoftSettingsForm().'</div>';
     			$_html .= '<div id="tabs-11">'.$this->_drawInstagramSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-12">'.$this->_drawFoursquareSettingsForm().'</div>';
     			$_html .= '<div id="tabs-13">'.$this->_drawGithubSettingsForm().'</div>';
     			$_html .= '<div id="tabs-14">'.$this->_drawDisqusSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-10">'.$this->_statistics().'</div>';
     			
     			
     		
					$_html .= '<div style="clear:both"></div>';
			
			
			$_html .= '</div>';
			
			
		
		$_html .= '</fieldset>	';
		
			
		return $_html;
     	
    }
    
    
   
    
    
    private function _facebookhelp(){
    	$_html = '';
    	
    	// callback_url
	 	if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}
		// callback_url
		
    	$_html .= '<fieldset>
					<legend>'.$this->l('HELP').'</legend>';
    	
    	
    	$_html .= '<div class="item-help-info">
    				<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    			  </div>';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 1:'.
    				'</div>';
    	
    	$_html .= '
    			<div class="item-help-info">
    			<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://developers.facebook.com/apps/">Facebook Developer</a> '.$this->l('link and log in with your facebook credentials').'.
				</div>
				
				<div class="item-help-info">
				<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on').' <b>"+ Add New App"</b> '.$this->l('button').'. '.$this->l('Select a platform to get started').': <b>"Website"</b>.  
				</div>
				
				<div class="item-help-info">
				<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on').' <b>"Skip and Create App ID"</b>.  
				</div>
				
				
				<div class="item-help-info">
				<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('A pop-up box will appear, enter').' <b>"Display Name"</b> '.$this->l('and select').' <b>"Category"</b> '.$this->l('for app and press').' <b>"Create App"</b> '.$this->l('button').'.
				</div>
				
				<div class="item-help-info">
				<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"Settings"</b> '.$this->l('in the menu from left sidebar then Click on').' <b>"+Add Platform"</b>.
				</div>
				
				<div class="item-help-info">
				<b>6.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on').' <b>"+ Add Platform"</b> '.$this->l('and select').' <b>"Website"</b> '.$this->l('platform').'".
				</div>

				<div class="item-help-info">
				<b>6.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Site URL"</b>: 
				<input type="text" value="'.$_http_host.'" style="width:450px">
				</div>
				
				<div class="item-help-info">
				<b>6.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your e-mail in').' <b>"Contact Email"</b> '.$this->l('to make app availble to all user').'. 
				</div>
				
				<div class="item-help-info">
				<b>6.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('After that click on').' <b>"Save Changes"</b> '.$this->l('button').'.
				</div>
			
				<div class="item-help-info">
				<b>7.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"Status &amp; Review"</b> '.$this->l('in the menu at left sidebar and change').' <b>"App status"</b> '.$this->l('to').' <b>"Yes"</b>. '.$this->l('A pop-up box will appear for confirmation and click').' <b>"Confirm"</b> '.$this->l('button in the popup').'.
				</div>
				
				<div class="item-help-info">
				<b>8.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"Dashboard"</b> '.$this->l('in the menu from left sidebar').'. '.$this->l('Add').' <b>"API Key"</b> '.$this->l('and').' <b>"Secret Key"</b> '.$this->l('to this form').'.
				</div>
			';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 2:'.
    				'</div>';
    				
    	$_html .= 	'<div class="item-help-info">'
    					.$this->l('To configure the "Facebook API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf" 
    								style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    				'</div>';		
    				
    	
    	
    	
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
 private function _twitterhelp(){
    	$_html = '';
    	
    	// callback_url
	 	if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}
		$callback_url = $_http_host.'modules/'.$this->name.'/twitter.php?action=callback';
		// callback_url
		
    	$_html .= '<fieldset>
					<legend>'.$this->l('HELP').'</legend>';
    	
    	
    	$_html .= '<div class="item-help-info">
    				<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    			  </div>';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 1:'.
    				'</div>';
    	
    	$_html .= '
    			<div class="item-help-info">
    			<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://dev.twitter.com/apps">Twitter Developers</a> '.$this->l('link and login with your credentials').'.
				</div>
				
				<div class="item-help-info">
				<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create New App"</b> '.$this->l('button').'.  
				</div>
				
				<div class="item-help-info">
				<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill out all the required fields').':
				</div>

				<div class="item-help-info">
				<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your name in').' <b>"Name"</b> '.$this->l('field').'. 
				</div>
				
				<div class="item-help-info">
				<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your description in').' <b>"Description"</b> '.$this->l('field').'. 
				</div>
				
				<div class="item-help-info">
				<b>3.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Website"</b> '.$this->l('field').': 
				<input type="text" value="'.$_http_host.'" style="width:450px">
				</div>
				
				<div class="item-help-info">
				<b>3.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Callback URL"</b> '.$this->l('field').': 
				<input type="text" value="'.$callback_url.'" style="width:450px">
				</div>
				
				<div class="item-help-info">
				<b>3.5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Read and agree to rules, and then click').' <b>"Create your Twitter application"</b> '.$this->l('button').'.
				</div>
			
				<div class="item-help-info">
				<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').'  <b>"Permissions"</b> '.$this->l('tab, and set Access').' <b>"Read and Write"</b>.
				</div>
				
				<div class="item-help-info">
				<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').'  <b>"Keys and Access Tokens"</b> '.$this->l('tab').'.
				</div>
				
				<div class="item-help-info">
				<b>5.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy').' <b>"API key"</b>' .$this->l('and').' <b>"API secret"</b>.
				</div>
			';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 2:'.
    				'</div>';
    				
    	$_html .= 	'<div class="item-help-info">'
    					.$this->l('To configure the "Twitter API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf" 
    								style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    				'</div>';		
    				
    					
    	
    	
    	
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
private function _googlehelp(){
    	$_html = '';
    	
    	// callback_url
	 	if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
			
			$_host_url = _PS_BASE_URL_SSL_;
			
		} else {
			$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
			
			$_host_url = _PS_BASE_URL_SSL_;
		}
		$callback_url = $_http_host.'modules/'.$this->name.'/login.php';
		// callback_url
		
    	$_html .= '<fieldset>
					<legend>'.$this->l('HELP').'</legend>';
    	
    	
    	$_html .= '<div class="item-help-info">
    				<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    			  </div>';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 1:'.
    				'</div>';
    	
    				
    	
    				
    	$_html .= '
    			<div class="item-help-info">
    			<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://console.developers.google.com/project">Google Developers console</a> '.$this->l('link and login with your credentials').'.
				</div>
				
				<div class="item-help-info">
				<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"CREATE PROJECT"</b> '.$this->l('button').'.   
				</div>
				
				<div class="item-help-info">
				<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Wait a few seconds until your project will be created').'
				</div>

				<div class="item-help-info">
				<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').'  <b>"APIs & auth"</b> -> <b>"Consert screen"</b> 
				</div>
				
				<div class="item-help-info">
				<b>4.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your email address in').' <b>"Email address"</b> '.$this->l('field').'. 
				</div>
				
				<div class="item-help-info">
				<b>4.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your product name in').' <b>"Product name"</b> '.$this->l('field').'. 
				</div>
				
				<div class="item-help-info">
				<b>4.3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Save"</b> '.$this->l('button').'.   
				</div>
				
				
				<div class="item-help-info">
				<b>5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <b>"APIs & auth"</b> -> <b>"Credentials"</b> '.$this->l('and click').' <b>"Create new Client ID"</b>. 
				</div>
				
				<div class="item-help-info">
				<b>5.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"APLICATION TYPE"</b> '.$this->l('Web Application').'. 
				</div>
				
				<div class="item-help-info">
				<b>5.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Authorized JavaScript origins"</b> '.$this->l('field').': 
				<input type="text" value="'.$_host_url.'" style="width:450px">
				</div>
				
				<div class="item-help-info">
				<b>5.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Authorized redirect URIs"</b> '.$this->l('field').': 
				<input type="text" value="'.$callback_url.'" style="width:450px">
				</div>
				
				<div class="item-help-info">
				<b>5.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Create Client ID"</b> '.$this->l('button').'.
				</div>
			
				
				<div class="item-help-info">
				<b>6.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the Google generated').' <b>"CLIENT ID"</b>' .$this->l('and').' <b>"CLIENT SECRET"</b>.
				</div>
			';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 2:'.
    				'</div>';
    				
    	$_html .= 	'<div class="item-help-info">'
    					.$this->l('To configure the "Google API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf" 
    								style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    				'</div>';		
    				
    					
    	
    	
    	
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
 private function _linkedinhelp(){
    	$_html = '';
    	 
    	// callback_url
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    	} else {
    		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    	}
    	// callback_url
    
    	$_html .= '<fieldset>
    	<legend>'.$this->l('HELP').'</legend>';
    	 
    	 
    	$_html .= '<div class="item-help-info">
    	<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    	</div>';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 1:'.
    	'</div>';
    	 
    	$_html .= '
    	<div class="item-help-info">
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://www.linkedin.com/secure/developer">LinkedIn developers</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create Application"</b> '.$this->l('link').'.
    	</div>
    
    	<br/>
    	
    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;Fill the <b>"Create a New Application"</b> '.$this->l('form').':
    	</div>
    
    	<div class="item-help-info">
    	<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select or Create your').' <b>"Company Name"</b>.
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your site Name in').' <b>"Name"</b> '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your site Description in').' <b>"Description"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill your').' <b>"Application Logo URL"</b>.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select your Application in').' <b>"Application Use"</b> '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.6</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Website URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$_http_host.'" style="width:450px">
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.7</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill your').' <b>"Business Email"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.8</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill your').' <b>"Business Phone"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.9</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Submit"</b> '.$this->l('button').'.
    	</div>
    		
    
    	<br/>
    	
    
    	<div class="item-help-info">
    	<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Section').' <b>"Authentication"</b>:
    	</div>
    
    	<div class="item-help-info">
    	<b>5.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select in').' <b>"Default Application Permissions"</b> '.$this->l('fields').' <b>"r_basicprofile"</b> and  <b>"r_emailaddress"</b>.
    	</div>
    
    	
    	<div class="item-help-info">
    	<b>5.2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Update"</b> '.$this->l('button').'.
    	</div>
    		
    	<br/>
    
    	<div class="item-help-info">
    	<b>6.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the').' <b>"Client ID (LinkedIn API Key)"</b>' .$this->l('and').' <b>"Client Secret (LinkedIn Secret Key)"</b>.
    	</div>
    	';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "LinkedIn API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    		
    
    	 
    	 
    	 
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
private function _microsofthelp(){
    	$_html = '';
    	
    	// callback_url
	 	if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}
		$callback_url = $_http_host.'modules/'.$this->name.'/microsoft.php';
		// callback_url
		
    	$_html .= '<fieldset>
					<legend>'.$this->l('HELP').'</legend>';
    	
    	
    	$_html .= '<div class="item-help-info">
    				<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    			  </div>';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 1:'.
    				'</div>';
    	
    	
    				
    	$_html .= '
    			<div class="item-help-info">
    			<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://account.live.com/developers/applications/create?tou=1">Developer center Microsoft</a> '.$this->l('link and login with your credentials').'.
				</div>
				
				<div class="item-help-info">
				<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create application"</b> '.$this->l('button').'.   
				</div>
				
				<div class="item-help-info">
				<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your application name in').' <b>"Application name"</b> '.$this->l('field').'. 
				</div>
				
				<div class="item-help-info">
				<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Choose your').' <b>"Language"</b>. 
				</div>
				
				<div class="item-help-info">
				<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Accept"</b> '.$this->l('button').'.   
				</div>
				
				
				
				<div class="item-help-info">
				<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').'  <b>"API Settings"</b>
				</div>
				
				<div class="item-help-info">
				<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Redirect URL"</b> '.$this->l('field').': 
				<input type="text" value="'.$callback_url.'" style="width:450px">
				</div>
				
				
				<div class="item-help-info">
				<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save"</b> '.$this->l('button').'.
				</div>
				
				<div class="item-help-info">
				<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').'  <b>"App Settings"</b>
				</div>
			
				<div class="item-help-info">
				<b>6.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the').' <b>"Client Id"</b>' .$this->l('and').' <b>"Client secret key"</b> '.$this->l('under').' <b>"Application Settings"</b> '.$this->l('in left menu').'.
				</div>
			';
    	
    	$_html .= '<div class="item-help-info way-color">'
    				.$this->l('Way').' 2:'.
    				'</div>';
    				
    	$_html .= 	'<div class="item-help-info">'
    					.$this->l('To configure the "Microsoft API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf" 
    								style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    				'</div>';		
    				
    			
    					
    	
    	
    	
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    private function _instagramhelp(){
    	$_html = '';
    	 
    	// callback_url
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    			
    		$_host_url = _PS_BASE_URL_SSL_;
    			
    	} else {
    		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    			
    		$_host_url = _PS_BASE_URL_SSL_;
    	}
    	$callback_url = $_http_host.'modules/'.$this->name.'/instagram.php';
    	// callback_url
    
    	$_html .= '<fieldset>
    	<legend>'.$this->l('HELP').'</legend>';
    	 
    	 
    	$_html .= '<div class="item-help-info">
    	<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    	</div>';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 1:'.
    	'</div>';
    	 
    
    	 
    
    	$_html .= '
    	<div class="item-help-info">
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://instagram.com/developer/">Instagram Developer Documentation</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"REGISTER YOUR APPLICATION"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').'  <b>"Basic TAB"</b>
    	</div>
    
    	<div class="item-help-info">
    	<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Application Name in').' <b>"Application Name"</b> '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Description in').' <b>"Description"</b> '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Website URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px">
    	</div>
    
    	<div class="item-help-info">
    	<b>3.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Redirect URI(s)"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="width:450px">
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>3.5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Register"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the Instagram generated').' <b>"CLIENT ID"</b>' .$this->l('and').' <b>"CLIENT SECRET"</b>.
    	</div>
    	';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Instagram API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    		
    	 
    	 
    	 
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
    private function _foursquarehelp(){
    	$_html = '';
    	 
    	// callback_url
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    			
    		$_host_url = _PS_BASE_URL_SSL_;
    			
    	} else {
    		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    			
    		$_host_url = _PS_BASE_URL_SSL_;
    	}
    	$callback_url = $_http_host.'modules/'.$this->name.'/foursquare.php';
    	// callback_url
    	
    	$_html .= '<fieldset>
    	<legend>'.$this->l('HELP').'</legend>';
    	 
    	 
    	$_html .= '<div class="item-help-info">
    	<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    	</div>';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 1:'.
    	'</div>';
    	 
    	
    	 
    	
    	$_html .= '
    	<div class="item-help-info">
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://foursquare.com/developers/apps">Foursquare Developers</a> '.$this->l('link and login with your credentials').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"CREATE A NEW APP"</b> '.$this->l('button').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your app name in ').' <b>"Your app name"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Section ').' <b>"Web addresses"</b>
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Download / welcome page url"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Your privacy policy url"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Redirect URI(s)"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="width:450px" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"SAVE CHANGES"</b> '.$this->l('button').'.
    	</div>
    		
    	
    	<div class="item-help-info">
    	<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"CLIENT ID"</b>' .$this->l('and').' <b>"CLIENT SECRET"</b>.
    	</div>
    	';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    	
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Foursquare API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    	
    		
    	 
    	 
    	 
    	$_html .= '</fieldset>';
    	return $_html;
    } 
    
    private function _githubhelp(){
    	$_html = '';
    	
    	// callback_url
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    		 
    		$_host_url = _PS_BASE_URL_SSL_;
    		 
    	} else {
    		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    		 
    		$_host_url = _PS_BASE_URL_SSL_;
    	}
    	$callback_url = $_http_host.'modules/'.$this->name.'/github.php';
    	// callback_url
    	 
    	$_html .= '<fieldset>
    	<legend>'.$this->l('HELP').'</legend>';
    	
    	
    	$_html .= '<div class="item-help-info">
    	<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    	</div>';
    	
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 1:'.
    	'</div>';
    	
    	 
    	
    	 
    	$_html .= '
    	<div class="item-help-info">
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://github.com/settings/applications/new">Developer applications</a> '.$this->l('link and login with your credentials').'.
    	</div>
    	 
    	 
    	<div class="item-help-info">
    	<b>2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Application name in ').' <b>"Application name"</b> '.$this->l('field').'.
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Homepage URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px" />
    	</div>

    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Application description in ').' <b>"Application description"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Authorization callback URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="width:450px" />
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Register application"</b> '.$this->l('button').'.
    	</div>
    	
    	 
    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"Client ID"</b>' .$this->l('and').' <b>"Client Secret"</b>.
    	</div>
    	';
    	
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    	 
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Github API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    	 
    	
    	
    	
    	
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    private function _disqushelp(){
    	$_html = '';
    	 
    	// callback_url
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    		 
    		$_host_url = _PS_BASE_URL_SSL_;
    		 
    	} else {
    		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    		 
    		$_host_url = _PS_BASE_URL_SSL_;
    	}
    	$callback_url = $_http_host.'modules/'.$this->name.'/disqus.php';
    	// callback_url
    	
    	$_html .= '<fieldset>
    	<legend>'.$this->l('HELP').'</legend>';
    	 
    	 
    	$_html .= '<div class="item-help-info">
    	<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    	</div>';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 1:'.
    	'</div>';
    	 
    	
    	 
    	
    	$_html .= '
    	<div class="item-help-info">
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://disqus.com/api/applications/">API Disqus</a> '.$this->l('link and login with your credentials').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Register new application"</b> '.$this->l('button').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Label"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Description"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Organization"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Website"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Captcha"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.6</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Register new application"</b> '.$this->l('button').'.
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"Authentication"</b>
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Callback URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="width:450px" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select Default Access ').' <b>"Read and write"</b>
    	</div>
    	 
    	
    	<div class="item-help-info">
    	<b>4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"Organization"</b>
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Organization"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Terms of Service URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save changes"</b> '.$this->l('button').'.
    	</div>
    	 
    	
    	
    	<div class="item-help-info">
    	<b>5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"Details -> OAuth Settings"</b>
    	</div>
    	
    	<div class="item-help-info">
    	<b>5.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"API Key"</b>' .$this->l('and').' <b>"API Secret"</b>.
    	</div>
    	';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    	
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Disqus API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    	
    	 
    	 
    	 
    	 
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    private function _amazonhelp(){
    	$_html = '';
    
    	// callback_url
    
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    		 
    		$_host_url = $_http_host;
    
    		$js_origins = Tools::getShopDomainSsl(true, true);
    		 
    	} else {
    		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    		 
    		$_host_url = $_http_host;
    
    		$js_origins = _PS_BASE_URL_SSL_;
    		 
    	}
    	 
    	$callback_url = $_http_host.'modules/'.$this->name.'/amazon.php';
    
    	$_html .= '<fieldset>
    	<legend>'.$this->l('HELP').'</legend>';
    
    
    	$_html .= '<div class="item-help-info">
    	<b>'.$this->l('Configure the API selecting comfortable way for you').':</b>
    	</div>';
    
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 1:'.
    	'</div>';
    
    
    	$_html .= '
    	<div class="item-help-info">
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="http://login.amazon.com/manageApps">http://login.amazon.com/manageApps</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"Application Information"</b>
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Name"</b> '.$this->l('field').'.
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Description"</b> '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Privacy Notice URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px" />
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Upload your ').' <b>"Logo Image File"</b>. '.$this->l('The logo will be automatically resized to 50 x150 pixels. The following formats are accepted: PNG, JPEG, GIF').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save"</b> '.$this->l('button').'.
    	</div>
    
    
    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"Web Settings"</b>
    	</div>
    
    
    	<div class="item-help-info">
    	<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Allowed JavaScript Origins"</b> '.$this->l('field').':
    	<input type="text" value="'.$js_origins.'" style="width:450px" />
    	</div>
    	 
    	<div class="item-help-info">
    	<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Allowed Return URLs"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="width:450px" />
    	</div>
    	 
    	<div class="item-help-info">
    	<b>3.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save"</b> '.$this->l('button').'.
    	</div>
    	 
    	 
    	<div class="item-help-info">
    	<b>3.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"Client ID"</b>.
    	</div>
    	 
    	';
    	 
    
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Amazon API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    
    
    
    
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
 private function _statistics(){
    	$cookie = $this->context->cookie;
		
    	$currentIndex = $this->context->currentindex;
    	
    	$_html = '';
    	
    	
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/views/img/statistics.png" />'.$this->l('Statistics').'</legend>
					
					';
    	
    	
    	if(Tools::getValue('pageitems')){
        	$start = Tools::getValue('pageitems');
        } else {
        	$start = 0;
        }
        
    	include_once(dirname(__FILE__).'/classes/statisticshelp.class.php');
    	$obj_help = new statisticshelp();
		
    	if(Tools::getValue('search_query')){
			$data = $obj_help->getCustomersSearch(array('search_query'=>Tools::getValue('search_query')));
		} else {
			$step = $this->_step;
			$data = $obj_help->getCustomers(array('start'=>$start,'step'=>$step));
		}

		$count_all = $data['count_all'];
    	$data_info = $data['data'];
    	
    	//echo "<pre>"; var_dump($data_info);exit;
    	
    	if($count_all>0){

    	if(Tools::getValue('search_query')){
    	$_html .= '<div style="margin:10px;float:left">';
    	$_html .= '<b style="font-size:16px">'.$this->l('Search'). '&nbsp;&nbsp;"'.Tools::getValue('search_query').'"</b>';
   	    $_html .= '<br/><br/><b>'.$count_all.'&nbsp;'.$this->l('results have been found.').'</b>';
    	$_html .= '</div>';	
    	}
    	
    	
    	if(!Tools::getValue('search_query')){
    	$data_total = $obj_help->totalCustomers();
    	
    	$_html .= '<div style="margin:10px;float:left">';
    	$_html .= '<b>'.$this->l('Total number of registrations').':</b> '.$data_total['count_all'];
    	$_html .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    	foreach($this->getAvaiableTypes() as $text_type => $id_type){
    			
    		if($text_type=="foursquare")
	    		$key_prefix = "fs";
	    	elseif($text_type=="github")
	    		$key_prefix = "gi";
	    	else 
    			$key_prefix = Tools::substr($text_type,0,1);
    			
    		
	    		$_html .= '<b><img src="../modules/'.$this->name.'/views/img/settings_'.$key_prefix.'.png" id="'.$id_type.'" />'.ucwords($text_type).':</b> '.$data_total['count_types'][$text_type];
	    		$_html .= '&nbsp;&nbsp;&nbsp;';
	    	}
    	
    	$_html .= '</div>';
    	}

    	$_html .= '<div style="margin:10px;float:right">';
    	$_html .= '<form method="post" action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" name="find">';
    	if(Tools::getValue('search_query')){
    	$_html .= '<a onclick="window.location.href = \''.$currentIndex.'&tab=AdminModules&cancel_search=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'\';"
								   style="text-decoration: underline; font-size: 11px; cursor: pointer; margin-right: 5px;">
								   '.$this->l('Clear search').'</a>';
    	}	
    	$_html .= '<input type="text" value="'.$this->l('Find Customer').'"
									   onfocus="if(this.value == \''.$this->l('Find Customer').'\') {this.value=\'\';}" 
									   onblur="if(this.value == \'\') {this.value=\''.$this->l('Find Customer').'\';}" 
									   id="search_query" size="25" name="search_query">
							<input type="image" src="../modules/'.$this->name.'/views/img/adv_search.png" />		   
								';
    	$_html .= '</form>';
    	$_html .= '</div>';	
    	$_html .= '<div style="clear:both"></div>';
    	
    	
    	$_html .= '<table class="table  customer" style="width: 100%; margin-bottom:10px;">';
    	
    	$_html .= '<tr>';
    		$_html .= '<th style="padding:5px 1px">'.$this->l('ID').'</th>';
	    	$_html .= '<th style="padding:5px 1px">'.$this->l('User Name').'</th>';
    		
	    	if(version_compare(_PS_VERSION_, '1.5', '>')){
	    		$_html .= '<th style="padding:5px 1px">'.$this->l('Shop').'</th>';
	    	}
	    	
	    	$_html .= '<th style="padding:5px 1px">'.$this->l('Social Connect').'</th>';
	    	
	    $_html .= '</tr>';
    	
    	$data_avaiable_types = $this->getAvaiableSocialTypes();
    	
	    foreach($data_info as $_items){
    		$uid = $_items['id'];
    		$name_user = $_items['firstname']. ' '.$_items['lastname'];
    		$name_shop = $_items['name_shop'];
    		
    		$_html .= '<tr>';
    			$_html .= '<td>'.$uid.'</td>';
    			
	    		$type = $_items['type'];
	    		$text_type =$data_avaiable_types[$type];
	    		
	    		
	    		if($text_type=="foursquare")
	    			$key_prefix = "fs";
	    		elseif($text_type=="github")
	    			$key_prefix = "gi";
	    		else
	    			$key_prefix = Tools::substr($text_type,0,1);
	    			 
    			//var_dump($currentIndex);
    			
    			if(version_compare(_PS_VERSION_, '1.5', '>')){
    				$admin_url_to_customer = 'index.php?controller=AdminCustomers&id_customer='.$uid.'&updatecustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'';
    			} else {
    				$admin_url_to_customer = 'index.php?tab=AdminCustomers&id_customer='.$uid.'&updatecustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($cookie->id_employee)).'';
    			}
    			
    			$_html .= '<td><img src="../modules/'.$this->name.'/views/img/settings_'.$key_prefix.'.png" />
    						<a style="text-decoration:underline" href="'.$admin_url_to_customer.'"
    						title="'.$name_user.'" target="_blank">'.$name_user.'</a>
    						</td>';
    			
	    		
    			
    			if(Tools::strlen($name_shop)>0){
    				$_html .= '<td>'.$name_shop.'</td>';
    			}
    			
    			$_html .= '<td><img src="../modules/'.$this->name.'/views/img/settings_'.$key_prefix.'.png" />'.ucwords($text_type).'</td>';
    			
		   $_html .= '</tr>';
    	}
    	$_html .= '</table>';
    	
    	if(Tools::getValue('search_query')){
    		// empty
    	} else {
	    	$paging = $obj_help->PageNav($start,$count_all,$this->_step, 
										array('admin' => 1,'currentIndex'=>$currentIndex,
											  'token' => '&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)),
											  'item' => 'items',
											  'text_page' => $this->l('Page')
										));
	    	
			$_html .= '<div style="margin:5px">';
			$_html .= $paging;
			$_html .= '</div>';
	    }
    								
    	} else {
    		
    		$_html .= '<div style="text-align:center;border:1px solid #CCCCCC;padding:10px">
    			'.$this->l('There are not items yet').'';
    		if(Tools::getValue('search_query')){	
    		$_html .= '<a onclick="window.location.href = \''.$currentIndex.'&tab=AdminModules&cancel_search=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'\';"
								   style="text-decoration: underline; font-size: 11px; cursor: pointer; margin-left: 10px;"
								   >'.$this->l('Go to Statistics').'</a>';
    		}
    		$_html .= '</div>';	
    	}
    	
    	$_html .= '</fieldset>';
    	return $_html;
    }
    

public function getAvaiableTypes(){
		return array('facebook' => 1, 'twitter'=>2, 'google'=>3, 'linkedin'=>4, 'microsoft'=>5, 'yahoo'=>6, 'instagram'=>7,'foursquare'=>20,'github'=>21,'disqus'=>22,'amazon'=>24);
	}
    
public function getAvaiableSocialTypes(){
		return array(1=> 'facebook', 2=>'twitter', 3=>'google', 4=>'linkedin', 5=>'microsoft', 6=>'yahoo', 7=>'instagram',20=>'foursquare',21=>'github',22=>'disqus',24=>'amazon');
	}
    

	private function _is_curl_installed() {
		if  (function_exists('curl_init')) {
			return true;
		}
		else {
			return false;
		}
	}
	
	
private function _welcome(){
 	
		$_html  = '';
		
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Welcome').'</legend>
					
					';
    	
    	$_html .=  $this->l('Welcome and thank you for purchasing the module.').
    			'<br/>';
    	
    	
    		
    		if (!$this->_is_curl_installed()) {
    			if(version_compare(_PS_VERSION_, '1.6', '>')){
    				$_html .= "<p class=\"alert alert-danger\">CURL PHP extension is disabled</p>";
    			} else {
	    			$_html .= "<div style='text-align:center;padding:5px;border:1px solid red;font-weight:bold;margin-bottom:10px'>";
	    			$_html .= "CURL PHP extension is <span style=\"color:red\">DISABLED</span> on this server";
	    			$_html .= "</div>";
    			}
    			 
    		} else {
    			
    			
    			if(version_compare(_PS_VERSION_, '1.6', '>')){
    				$_html .= "<p class=\"alert alert-success\">CURL PHP extension is enabled</p>";
    			} else {
    				$_html .= "<div style='text-align:center;padding:5px;border:1px solid green;font-weight:bold;margin-bottom:10px'>";
    				$_html .= "CURL PHP extension is <span style=\"color:green\">ENABLED</span> on this server";
    				$_html .= "</div>";
    			}
    		}
    		
    	$_html .=	'</fieldset>'; 
    			
    	return $_html;
    }
    
    
    private function _drawAmazonSettingsForm(){
    	$_html = '';
    
    	$_html .= $this->_amazonhelp ();
    	 
    	 
    	 
    	 
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_html .= "<p class=\"alert alert-danger\">Note: To enable Amazon Connect, Please make sure that \"SSL\" has enabled on your server </p>";
    	} else {
    		$_html .= "<div style='text-align:center;padding:5px;border:1px solid red;font-weight:bold;margin-bottom:10px'>";
    		$_html .= "Note: To enable Amazon Connect, Please make sure that \"SSL\" has enabled on your server ";
    		$_html .= "</div>";
    	}
    	 
    	 
    
    	$_html .= '
    	<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
    
    	$_html .= '<fieldset>
    	<legend><img src="../modules/' . $this->name . '/views/img/settings_a.png" />' . $this->l ( 'Amazon Settings' ) . '</legend>
    
    	';
    
    	// enable or disable vouchers
    	$_html .= '<label>' . $this->l ( 'Enable or Disable Amazon Connect' ) . ':</label>
    	<div class="margin-form">
    
    	<input type="radio" value="1" id="text_list_on" name="a_on" onclick="enableOrDisableAmazon(1)"
    	' . (Tools::getValue ( 'a_on', Configuration::get ( $this->name . 'a_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_on" class="t">
    	<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
    	</label>
    
    	<input type="radio" value="0" id="text_list_off" name="a_on" onclick="enableOrDisableAmazon(0)"
    	' . (! Tools::getValue ( 'a_on', Configuration::get ( $this->name . 'a_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_off" class="t">
    	<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
    	</label>
    
    	<p class="clear">' . $this->l ( 'Enable or Disable Amazon Connect' ) . '.</p>
    	</div>';
    
    	$_html .= '<script type="text/javascript">
    	function enableOrDisableAmazon(id)
    	{
    	if(id==0){
    	$("#block-amazon-settings").hide(200);
    } else {
    $("#block-amazon-settings").show(200);
    }
     
    }
    </script>';
    
    	$_html .= '<div id="block-amazon-settings" ' . (Configuration::get ( $this->name . 'a_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
    
    	// changes OAuth 2.0
    
    	// Google Client Id
    	$_html .= '<label>' . $this->l ( 'Amazon Client ID' ) . ':</label>
    
    	<div class="margin-form">
    	<input type="text" name="aci"  style="width:400px"
    	value="' . Tools::getValue ( 'aci', Configuration::get ( $this->name . 'aci' ) ) . '">
    
    	</div>';
    
    	 
    	$_html .= '<label>' . $this->l ( 'Amazon Allowed Return URL' ) . ':</label>
    
    	<div class="margin-form">
    	<input type="text" name="aru"  style="width:400px" value="' . Tools::getValue ( 'aru', Configuration::get ( $this->name . 'aru' ) ) . '">
    	&nbsp;<span style="color:red;font-size:14px;">Amazon Allowed Return URL <b>MUST BE WITH HTTPS</b> !</span>
    
    
    	</div>';
    	// changes OAuth 2.0
    
    	$_html .= '<br/><br/>';
    
    	$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Amazon Connect Button' ), 'prefix' => 'a' ) );
    
    	$_html .= '<br/><br/>';
    
    	$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Amazon Connect Large Image' ), 'title_medium' => $this->l ( 'Amazon Connect Medium Image' ),
    			'title_small' => $this->l ( 'Amazon Connect Small Image' ), 'title_very_small' => $this->l ( 'Amazon Connect Very Small Image' ),
    			'prefix_short' => 'a', 'prefix' => 'amazon' ) );
    
    	$_html .= '</div>';
    
    	$_html .= $this->_updateButton ( array ('name' => 'amazon', 'prefix' => 'a' ) );
    
    	$_html .= '</fieldset>';
    
    	$_html .= '</form>';
    
    	return $_html;
    }
    
    private function _drawDisqusSettingsForm(){
    	$_html = '';
		
		$_html .= $this->_disqushelp ();
		$_html .= '<br/>';
		
		$_html .= '
    	<form action="' . Tools::safeOutput ( $_SERVER ['REQUEST_URI'] ) . '" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
    	<legend><img src="../modules/' . $this->name . '/views/img/settings_d.png" />' . $this->l ( 'Disqus Settings' ) . '</legend>
    	 
    	';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Disqus Connect' ) . ':</label>
    	<div class="margin-form">
    	 
    	<input type="radio" value="1" id="text_list_on" name="d_on" onclick="enableOrDisableDisqus(1)"
    	' . (Tools::getValue ( 'd_on', Configuration::get ( $this->name . 'd_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_on" class="t">
    	<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
    	</label>
    	 
    	<input type="radio" value="0" id="text_list_off" name="d_on" onclick="enableOrDisableDisqus(0)"
    	' . (! Tools::getValue ( 'd_on', Configuration::get ( $this->name . 'd_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_off" class="t">
    	<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
    	</label>
    	 
    	<p class="clear">' . $this->l ( 'Enable or Disable Disqus Connect' ) . '.</p>
    	</div>';
		
		$_html .= '<script type="text/javascript">
    	function enableOrDisableDisqus(id)
    	{
    	if(id==0){
    	$("#block-disqus-settings").hide(200);
    	} else {
    	$("#block-disqus-settings").show(200);
    	}
    	
    	}
    	</script>';
		
		$_html .= '<div id="block-disqus-settings" ' . (Configuration::get ( $this->name . 'd_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		// Google Client Id
		$_html .= '<label>' . $this->l ( 'Disqus API Key' ) . ':</label>
    	 
    	<div class="margin-form">
    	<input type="text" name="dci"  style="width:400px"
    	value="' . Tools::getValue ( 'dci', Configuration::get ( $this->name . 'dci' ) ) . '">
    	 
    	</div>';
		
		// Google Client Secret
		$_html .= '<label>' . $this->l ( 'Disqus API Secret' ) . ':</label>
    	 
    	<div class="margin-form">
    	<input type="text" name="dcs"  style="width:400px"
    	value="' . Tools::getValue ( 'dcs', Configuration::get ( $this->name . 'dcs' ) ) . '">
    	 
    	 
    	</div>';
		
		$_html .= '<label>' . $this->l ( 'Disqus Callback URL' ) . ':</label>
    	 
    	<div class="margin-form">
    	<input type="text" name="dru"  style="width:400px"
    	value="' . Tools::getValue ( 'dru', Configuration::get ( $this->name . 'dru' ) ) . '">
    	 
    	 
    	</div>';
		// changes OAuth 2.0
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Disqus Connect Button' ), 'prefix' => 'd' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Disqus Connect Large Image' ), 'title_medium' => $this->l ( 'Disqus Connect Medium Image' ), 
												  'title_small' => $this->l ( 'Disqus Connect Small Image' ), 'title_very_small' => $this->l ( 'Disqus Connect Very Small Image' ), 
												  'prefix_short' => 'd', 'prefix' => 'disqus' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'disqus', 'prefix' => 'd' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
    }
    
    
    private function _drawGithubSettingsForm(){
    	$_html = '';
		
		$_html .= $this->_githubhelp();
		$_html .= '<br/>';
		
		$_html .= '
    	<form action="' . Tools::safeOutput ( $_SERVER ['REQUEST_URI'] ) . '" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
    	<legend><img src="../modules/' . $this->name . '/views/img/settings_gi.png" />' . $this->l ( 'Github Settings' ) . '</legend>
    	
    	';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Github Connect' ) . ':</label>
    	<div class="margin-form">
    	
    	<input type="radio" value="1" id="text_list_on" name="gi_on" onclick="enableOrDisableGithub(1)"
    	' . (Tools::getValue ( 'gi_on', Configuration::get ( $this->name . 'gi_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_on" class="t">
    	<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
    	</label>
    	
    	<input type="radio" value="0" id="text_list_off" name="gi_on" onclick="enableOrDisableGithub(0)"
    	' . (! Tools::getValue ( 'gi_on', Configuration::get ( $this->name . 'gi_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_off" class="t">
    	<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
    	</label>
    	
    	<p class="clear">' . $this->l ( 'Enable or Disable Github Connect' ) . '.</p>
    	</div>';
		
		$_html .= '<script type="text/javascript">
    	function enableOrDisableGithub(id)
    	{
    	if(id==0){
    	$("#block-github-settings").hide(200);
    	} else {
    	$("#block-github-settings").show(200);
    	}
    	 
    	}
    	</script>';
		
		$_html .= '<div id="block-github-settings" ' . (Configuration::get ( $this->name . 'gi_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		// Google Client Id
		$_html .= '<label>' . $this->l ( 'Github Client Id' ) . ':</label>
    	
    	<div class="margin-form">
    	<input type="text" name="gici"  style="width:400px"
    	value="' . Tools::getValue ( 'gici', Configuration::get ( $this->name . 'gici' ) ) . '">
    	
    	</div>';
		
		// Google Client Secret
		$_html .= '<label>' . $this->l ( 'Github Client Secret' ) . ':</label>
    	
    	<div class="margin-form">
    	<input type="text" name="gics"  style="width:400px"
    	value="' . Tools::getValue ( 'gics', Configuration::get ( $this->name . 'gics' ) ) . '">
    	
    	
    	</div>';
		
		$_html .= '<label>' . $this->l ( 'Github Callback URL' ) . ':</label>
    	
    	<div class="margin-form">
    	<input type="text" name="giru"  style="width:400px"
    	value="' . Tools::getValue ( 'giru', Configuration::get ( $this->name . 'giru' ) ) . '">
    	
    	
    	</div>';
		// changes OAuth 2.0
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Github Connect Button' ), 'prefix' => 'gi' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Github Connect Large Image' ), 'title_medium' => $this->l ( 'Github Connect Medium Image' ), 
												  'title_small' => $this->l ( 'Github Connect Small Image' ), 'title_very_small' => $this->l ( 'Github Connect Very Small Image' ), 
												   'prefix_short' => 'gi', 'prefix' => 'github' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'Github', 'prefix' => 'gi' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
    }
    
    private function _drawFoursquareSettingsForm() {
		$_html = '';
		
		$_html .= $this->_foursquarehelp();
		$_html .= '<br/>';
		
		$_html .= '
    	<form action="' . Tools::safeOutput ( $_SERVER ['REQUEST_URI'] ) . '" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
    	<legend><img src="../modules/' . $this->name . '/views/img/settings_fs.png" />' . $this->l ( 'Foursquare Settings' ) . '</legend>
    		
    	';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Foursquare Connect' ) . ':</label>
    	<div class="margin-form">
    
    	<input type="radio" value="1" id="text_list_on" name="fs_on" onclick="enableOrDisableFoursquare(1)"
    	' . (Tools::getValue ( 'fs_on', Configuration::get ( $this->name . 'fs_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_on" class="t">
    	<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
    	</label>
    		
    	<input type="radio" value="0" id="text_list_off" name="fs_on" onclick="enableOrDisableFoursquare(0)"
    	' . (! Tools::getValue ( 'fs_on', Configuration::get ( $this->name . 'fs_on' ) ) ? 'checked="checked" ' : '') . '>
    	<label for="dhtml_off" class="t">
    	<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
    	</label>
    		
    	<p class="clear">' . $this->l ( 'Enable or Disable Foursquare Connect' ) . '.</p>
    	</div>';
		
		$_html .= '<script type="text/javascript">
    	function enableOrDisableFoursquare(id)
    	{
	    	if(id==0){
	    		$("#block-foursquare-settings").hide(200);
		    } else {
		    	$("#block-foursquare-settings").show(200);
		    }
    	
    	}
    </script>';
		
		$_html .= '<div id="block-foursquare-settings" ' . (Configuration::get ( $this->name . 'fs_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		// Google Client Id
		$_html .= '<label>' . $this->l ( 'Foursquare Client Id' ) . ':</label>
    	 
    	<div class="margin-form">
    	<input type="text" name="fsci"  style="width:400px"
    	value="' . Tools::getValue ( 'fsci', Configuration::get ( $this->name . 'fsci' ) ) . '">
    		
    	</div>';
		
		// Google Client Secret
		$_html .= '<label>' . $this->l ( 'Foursquare Client Secret' ) . ':</label>
    	 
    	<div class="margin-form">
    	<input type="text" name="fscs"  style="width:400px"
    	value="' . Tools::getValue ( 'fscs', Configuration::get ( $this->name . 'fscs' ) ) . '">
    		
    
    	</div>';
		
		$_html .= '<label>' . $this->l ( 'Foursquare Callback URL' ) . ':</label>
    	 
    	<div class="margin-form">
    	<input type="text" name="fsru"  style="width:400px"
    	value="' . Tools::getValue ( 'fsru', Configuration::get ( $this->name . 'fsru' ) ) . '">
    		
    
    	</div>';
		// changes OAuth 2.0
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Foursquare Connect Button' ), 'prefix' => 'fs' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Foursquare Connect Large Image' ), 'title_medium' => $this->l ( 'Foursquare Connect Medium Image' ), 
												  'title_small' => $this->l ( 'Foursquare Connect Small Image' ), 'title_very_small' => $this->l ( 'Foursquare Connect Very Small Image' ), 
												  'prefix_short' => 'fs', 'prefix' => 'foursquare' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'Foursquare', 'prefix' => 'fs' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
    
    private function _drawInstagramSettingsForm(){
    	$_html = '';
    
    	$_html .= $this->_instagramhelp();
    	$_html .='<br/>';
    
    	$_html .= '
    	<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
    	 
    	$_html .= '<fieldset>
    	<legend><img src="../modules/'.$this->name.'/views/img/settings_i.png" />'.$this->l('Instagram Settings').'</legend>
    
    	';
    
    	// enable or disable vouchers
    	$_html .= '<label>'.$this->l('Enable or Disable Instagram Connect').':</label>
    	<div class="margin-form">
    	 
    	<input type="radio" value="1" id="text_list_on" name="i_on" onclick="enableOrDisableInstagram(1)"
    	'.(Tools::getValue('i_on', Configuration::get($this->name.'i_on')) ? 'checked="checked" ' : '').'>
    	<label for="dhtml_on" class="t">
    	<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
    	</label>
    
    	<input type="radio" value="0" id="text_list_off" name="i_on" onclick="enableOrDisableInstagram(0)"
    	'.(!Tools::getValue('i_on', Configuration::get($this->name.'i_on')) ? 'checked="checked" ' : '').'>
    	<label for="dhtml_off" class="t">
    	<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
    	</label>
    
    	<p class="clear">'.$this->l('Enable or Disable Instagram Connect').'.</p>
    	</div>';
    
    	$_html .= '<script type="text/javascript">
    	function enableOrDisableInstagram(id)
    	{
    	if(id==0){
    	$("#block-instagram-settings").hide(200);
		    } else {
		    $("#block-instagram-settings").show(200);
		    }
		    
		}
    	</script>';
    
    	$_html .= '<div id="block-instagram-settings" '.(Configuration::get($this->name.'i_on')==1?'style="display:block"':'style="display:none"').'>';
    
    	
    	// Instagram Client Id
    	$_html .= '<label>'.$this->l('Instagram Client Id').':</label>
    
    	<div class="margin-form">
    	<input type="text" name="ici"  style="width:400px"
    	value="'.Tools::getValue('ici', Configuration::get($this->name.'ici')).'">
    
    	</div>';
    
    	// Instagram Client Secret
    	$_html .= '<label>'.$this->l('Instagram Client Secret').':</label>
    
    	<div class="margin-form">
    	<input type="text" name="ics"  style="width:400px"
    	value="'.Tools::getValue('ics', Configuration::get($this->name.'ics')).'">
    
    	 
    	</div>';
    	 
    	 
    	$_html .= '<label>'.$this->l('Instagram Callback URL').':</label>
    
    	<div class="margin-form">
    	<input type="text" name="iru"  style="width:400px"
    	value="'.Tools::getValue('iru', Configuration::get($this->name.'iru')).'">
    
    	 
    	</div>';
    	// changes OAuth 2.0
    
    	 
    	
    	$_html .= '<br/><br/>';
    	
    	$_html .= $this->_positionConnect(
    			array(
    					'title'=>$this->l('Position Instagram Connect Button'),
    					'prefix'=>'i'
    			)
    	);
    	
    	$_html .= '<br/><br/>';
    	
    	$_html .= $this->_imagesConnects(
    			array(
    					'title_large'=>$this->l('Instagram Connect Large Image'),
    					'title_medium'=>$this->l('Instagram Connect Medium Image'),
    					'title_small'=>$this->l('Instagram Connect Small Image'),
    					'title_very_small'=>$this->l('Instagram Connect Very Small Image'),
    					'prefix_short'=>'i',
    					'prefix'=>'instagram',
    			)
    	);
    	
    	
    	
    	
    	
        	    	
        	    	$_html .= '</div>';
        			
        	    	$_html .= $this->_updateButton(array('name'=>'Instagram','prefix'=>'i'));
        	    	
        	    	
        			$_html .=	'</fieldset>'; 
        			
        			$_html .= '</form>';
        	    	
        	    	return $_html;
        }
    
private function _drawYahooSettingsForm(){
    	$_html = '';
    	
    	  $_html .= '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
      
    	
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/views/img/settings_y.png" />'.$this->l('Yahoo Settings').'</legend>
					
					';
    	
    	// enable or disable vouchers
    	$_html .= '<label>'.$this->l('Enable or Disable Yahoo Connect').':</label>
				<div class="margin-form">
				
					<input type="radio" value="1" id="text_list_on" name="y_on" onclick="enableOrDisableYahoo(1)"
							'.(Tools::getValue('y_on', Configuration::get($this->name.'y_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					
					<input type="radio" value="0" id="text_list_off" name="y_on" onclick="enableOrDisableYahoo(0)"
						   '.(!Tools::getValue('y_on', Configuration::get($this->name.'y_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
					<p class="clear">'.$this->l('Enable or Disable Yahoo Connect').'.</p>
				</div>';
    	
    	$_html .= '<script type="text/javascript">
			    	function enableOrDisableYahoo(id)
						{
						if(id==0){
							$("#block-yahoo-settings").hide(200);
						} else {
							$("#block-yahoo-settings").show(200);
						}
							
						}
					</script>';
    	
		$_html .= '<div id="block-yahoo-settings" '.(Configuration::get($this->name.'y_on')==1?'style="display:block"':'style="display:none"').'>';
    	
    	
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect(
				array(
						'title'=>$this->l('Position Yahoo Connect Button'),
						'prefix'=>'y'
				)
		);
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects(
				array(
						'title_large'=>$this->l('Yahoo Connect Large Image'),
						'title_medium'=>$this->l('Yahoo Connect Medium Image'),
						'title_small'=>$this->l('Yahoo Connect Small Image'),
						'title_very_small'=>$this->l('Yahoo Connect Very Small Image'),
						'prefix_short'=>'y',
						'prefix'=>'yahoo',
				)
		);
		
		
    	
    	$_html .= '</div>';
		
    	$_html .= $this->_updateButton(array('name'=>'Yahoo','prefix'=>'y'));
    	
    	
		$_html .=	'</fieldset>'; 
		
		$_html .= '</form>';
    	
    	return $_html;
    }
    
     private function _drawMicrosoftSettingsForm(){
    	$_html = '';
    	
    	
    	$_html .= $this->_microsofthelp();
    	
    	$_html .='<br/>';
    	
    	  $_html .= '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
      
    	  
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/views/img/settings_m.png" />'.$this->l('Microsoft Live Settings').'</legend>
					
					';
    	
    	// enable or disable vouchers
    	$_html .= '<label>'.$this->l('Enable or Disable Microsoft Live Connect').':</label>
				<div class="margin-form">
				
					<input type="radio" value="1" id="text_list_on" name="m_on" onclick="enableOrDisableMicrosoft(1)"
							'.(Tools::getValue('m_on', Configuration::get($this->name.'m_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					
					<input type="radio" value="0" id="text_list_off" name="m_on" onclick="enableOrDisableMicrosoft(0)"
						   '.(!Tools::getValue('m_on', Configuration::get($this->name.'m_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
					<p class="clear">'.$this->l('Enable or Disable Microsoft Live Connect').'.</p>
				</div>';
    	
    	$_html .= '<script type="text/javascript">
			    	function enableOrDisableMicrosoft(id)
						{
						if(id==0){
							$("#block-microsoft-settings").hide(200);
						} else {
							$("#block-microsoft-settings").show(200);
						}
							
						}
					</script>';
    	
		$_html .= '<div id="block-microsoft-settings" '.(Configuration::get($this->name.'m_on')==1?'style="display:block"':'style="display:none"').'>';
    	
    	
		// Facebook Application Id
    	$_html .= '<label>'.$this->l('Microsoft Live Client ID').':</label>
    			
    				<div class="margin-form">
					<input type="text" name="mclientid"  style="width:400px"
			                		value="'.Tools::getValue('mclientid', Configuration::get($this->name.'mclientid')).'">
					
				</div>';
    	
    	// Facebook Secret Key
		$_html .= '<label>'.$this->l('Microsoft Live Client Secret').':</label>
    			
    				<div class="margin-form">
					<input type="text" name="mclientsecret"  style="width:400px"
			                		value="'.Tools::getValue('mclientsecret', Configuration::get($this->name.'mclientsecret')).'">
					
				
				</div>';
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect(
				array(
						'title'=>$this->l('Position Microsoft Live Connect Button'),
						'prefix'=>'m'
				)
		);
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects(
				array(
						'title_large'=>$this->l('Microsoft Live Connect Large Image'),
						'title_medium'=>$this->l('Microsoft Live Connect Medium Image'),
						'title_small'=>$this->l('Microsoft Live Connect Small Image'),
						'title_very_small'=>$this->l('Microsoft Live Connect Very Small Image'),
						'prefix_short'=>'m',
						'prefix'=>'microsoft',
				)
		);
		
		
		
		
		
    	
    	$_html .= '</div>';
    	
		$_html .= $this->_updateButton(array('name'=>'Microsoft','prefix'=>'m'));
    	
    	
		$_html .=	'</fieldset>'; 
		
		
    	$_html .= '</form>';
    	return $_html;
    }
    

    private function _drawLinkedInSettingsForm(){
    	$_html = '';
    	
    	
    	$_html .= $this->_linkedinhelp();
    	$_html .='<br/>';
    	
    	  $_html .= '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
      
    	  
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/views/img/settings_l.png" />'.$this->l('LinkedIn Settings').'</legend>
					
					';
    	
    	// enable or disable vouchers
    	$_html .= '<label>'.$this->l('Enable or Disable LinkedIn Connect').':</label>
				<div class="margin-form">
				
					<input type="radio" value="1" id="text_list_on" name="l_on" onclick="enableOrDisableLinkedIn(1)"
							'.(Tools::getValue('l_on', Configuration::get($this->name.'l_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					
					<input type="radio" value="0" id="text_list_off" name="l_on" onclick="enableOrDisableLinkedIn(0)"
						   '.(!Tools::getValue('l_on', Configuration::get($this->name.'l_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
					<p class="clear">'.$this->l('Enable or Disable LinkedIn Connect').'.</p>
				</div>';
    	
    	$_html .= '<script type="text/javascript">
			    	function enableOrDisableLinkedIn(id)
						{
						if(id==0){
							$("#block-linkedin-settings").hide(200);
						} else {
							$("#block-linkedin-settings").show(200);
						}
							
						}
					</script>';
    	
		$_html .= '<div id="block-linkedin-settings" '.(Configuration::get($this->name.'l_on')==1?'style="display:block"':'style="display:none"').'>';
    	
    	
		// Facebook Application Id
    	$_html .= '<label>'.$this->l('LinkedIn API Key').':</label>
    			
    				<div class="margin-form">
					<input type="text" name="lapikey"  style="width:400px"
			                		value="'.Tools::getValue('lapikey', Configuration::get($this->name.'lapikey')).'">
					
				</div>';
    	
    	// Facebook Secret Key
		$_html .= '<label>'.$this->l('LinkedIn Secret Key').':</label>
    			
    				<div class="margin-form">
					<input type="text" name="lsecret"  style="width:400px"
			                		value="'.Tools::getValue('lsecret', Configuration::get($this->name.'lsecret')).'">
					
				
				</div>';
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect(
				array(
						'title'=>$this->l('Position LinkedIn Connect Button'),
						'prefix'=>'l'
				)
		);
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects(
				array(
						'title_large'=>$this->l('LinkedIn Connect Large Image'),
						'title_medium'=>$this->l('LinkedIn Connect Medium Image'),
						'title_small'=>$this->l('LinkedIn Connect Small Image'),
						'title_very_small'=>$this->l('LinkedIn Connect Very Small Image'),
						'prefix_short'=>'l',
						'prefix'=>'linkedin',
				)
		);
		
		
		
		
		
    	
    	$_html .= '</div>';
		
    	$_html .= $this->_updateButton(array('name'=>'LinkedIn','prefix'=>'l'));
    	
    	
		$_html .=	'</fieldset>'; 
		
		$_html .= '</form>';
    	
    	return $_html;
    }
    
    

    
private function _drawTwitterSettingsForm(){
    	$_html = '';
    	
    	
    	$_html .= $this->_twitterhelp();
    	$_html .='<br/>';
    	
    	  $_html .= '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
      
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/views/img/settings_t.png"  />'.$this->l('Twitter Settings').'</legend>';

    	// enable or disable vouchers
    	$_html .= '<label>'.$this->l('Enable or Disable Twitter Connect').':</label>
				<div class="margin-form">
				
					<input type="radio" value="1" id="text_list_on" name="t_on" onclick="enableOrDisableTwitter(1)"
							'.(Tools::getValue('t_on', Configuration::get($this->name.'t_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					
					<input type="radio" value="0" id="text_list_off" name="t_on" onclick="enableOrDisableTwitter(0)"
						   '.(!Tools::getValue('t_on', Configuration::get($this->name.'t_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
					<p class="clear">'.$this->l('Enable or Disable Twitter Connect').'.</p>
				</div>';
    	
    	$_html .= '<script type="text/javascript">
			    	function enableOrDisableTwitter(id)
						{
						if(id==0){
							$("#block-twitter-settings").hide(200);
						} else {
							$("#block-twitter-settings").show(200);
						}
							
						}
					</script>';
    	
		$_html .= '<div id="block-twitter-settings" '.(Configuration::get($this->name.'t_on')==1?'style="display:block"':'style="display:none"').'>';
    	
    	
		$_html .= '<label>'.$this->l('Consumer key:').'</label>
    			
    				<div class="margin-form">
					<input type="text" name="twitterconskey"  style="width:400px"
			               value="'.Tools::getValue('twitterconskey', Configuration::get($this->name.'twitterconskey')).'"
			               >
			         
					
			       </div>';
		
		$_html .= '<label>'.$this->l('Consumer secret:').'</label>
    			
    				<div class="margin-form">
					<input type="text" name="twitterconssecret"  style="width:400px"
			               value="'.Tools::getValue('twitterconssecret', Configuration::get($this->name.'twitterconssecret')).'">
					  
					
					
					
			       </div>';
		
		
		$_html .= '<br/><br/>';
		
		
		$_html .= $this->_positionConnect(
				array(
						'title'=>$this->l('Position Twitter Connect Button'),
						'prefix'=>'t'
				)
		);
		
		$_html .= '<br/><br/>';
		
		
		
		$_html .= $this->_imagesConnects(
				array(
						'title_large'=>$this->l('Twitter Connect Large Image'),
						'title_medium'=>$this->l('Twitter Connect Medium Image'),
						'title_small'=>$this->l('Twitter Connect Small Image'),
						'title_very_small'=>$this->l('Twitter Connect Very Small Image'),
						'prefix_short'=>'t',
						'prefix'=>'twitter',
				)
		);
					
		
	
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton(array('name'=>'Twitter','prefix'=>'t'));
    	
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
    	
    	return $_html;
    }
    
	
    
	private function _drawGoogleSettingsForm(){
    	$_html = '';
    	
    	$_html .= $this->_googlehelp();
    	$_html .='<br/>';
    	
    	  $_html .= '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
      
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/views/img/settings_g.png" />'.$this->l('Google Settings').'</legend>
					
					';
    	
    	// enable or disable vouchers
    	$_html .= '<label>'.$this->l('Enable or Disable Google Connect').':</label>
				<div class="margin-form">
				
					<input type="radio" value="1" id="text_list_on" name="g_on" onclick="enableOrDisableGoogle(1)"
							'.(Tools::getValue('g_on', Configuration::get($this->name.'g_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					
					<input type="radio" value="0" id="text_list_off" name="g_on" onclick="enableOrDisableGoogle(0)"
						   '.(!Tools::getValue('g_on', Configuration::get($this->name.'g_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
					<p class="clear">'.$this->l('Enable or Disable Google Connect').'.</p>
				</div>';
    	
    	$_html .= '<script type="text/javascript">
			    	function enableOrDisableGoogle(id)
						{
						if(id==0){
							$("#block-google-settings").hide(200);
						} else {
							$("#block-google-settings").show(200);
						}
							
						}
					</script>';
    	
		$_html .= '<div id="block-google-settings" '.(Configuration::get($this->name.'g_on')==1?'style="display:block"':'style="display:none"').'>';
    	
    	// changes OAuth 2.0
	 	
		// Google Client Id
		$_html .= '<label>'.$this->l('Google Client Id').':</label>
    			
    				<div class="margin-form">
					<input type="text" name="oci"  style="width:400px"
			                		value="'.Tools::getValue('oci', Configuration::get($this->name.'oci')).'">
					
				</div>';
    	
    	// Google Client Secret
		$_html .= '<label>'.$this->l('Google Client Secret').':</label>
    			
    				<div class="margin-form">
					<input type="text" name="ocs"  style="width:400px"
			                		value="'.Tools::getValue('ocs', Configuration::get($this->name.'ocs')).'">
					
				
				</div>';
		
		
		$_html .= '<label>'.$this->l('Google Callback URL').':</label>
    			
    				<div class="margin-form">
					<input type="text" name="oru"  style="width:400px"
			                		value="'.Tools::getValue('oru', Configuration::get($this->name.'oru')).'">
					
				
				</div>';
		// changes OAuth 2.0
	 	
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect(
				array(
						'title'=>$this->l('Position Google Connect Button'),
						'prefix'=>'g'
				)
		);
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects(
				array(
						'title_large'=>$this->l('Google Connect Large Image'),
						'title_medium'=>$this->l('Google Connect Medium Image'),
						'title_small'=>$this->l('Google Connect Small Image'),
						'title_very_small'=>$this->l('Google Connect Very Small Image'),
						'prefix_short'=>'g',
						'prefix'=>'google',
				)
		);
					
		
		
    	
    	$_html .= '</div>';
		
    	$_html .= $this->_updateButton(array('name'=>'Google','prefix'=>'g'));
    	
    	
		$_html .=	'</fieldset>'; 
		
		$_html .= '</form>';
    	
    	return $_html;
    }
    
    
    private function _positionConnect($data){
    	
    	$title = $data['title'];
    	$prefix = $data['prefix'];
    	
    	$_html = '';
    	
    	$_html .= '<label>'.$title.':</label>
    	 
    	';
    	 
    	/* $top = Configuration::get($this->name.'_top'.$prefix);
    	$rightcolumn = Configuration::get($this->name.'_rightcolumn'.$prefix);
    	$leftcolumn  = Configuration::get($this->name.'_leftcolumn'.$prefix);
    	$footer = Configuration::get($this->name.'_footer'.$prefix);
    	$authpage  = Configuration::get($this->name.'_authpage'.$prefix);
    	$welcome = Configuration::get($this->name.'_welcome'.$prefix); */
    	
    	
    	$hooks_array = array(
    						'top'=>$this->l('Top'),
    					    'rightcolumn'=>$this->l('Right Column'),
    						'leftcolumn'=>$this->l('Left Column'),
    			   			'footer'=>$this->l('Footer'),
    						'authpage'=>$this->l('Authentication page'),
    						'welcome'=>$this->l('Near with text Welcome'),
    						);
    	
    			
    	$_html .= '<style>
    				.choose_hooks td{font-size:13px;padding:5px 0}
    				.choose_hooks td.title_hook{width:20%}
    			</style>
    	        		
    	        		<div class="margin-form choose_hooks">';
    		    			
    		
    				$_html .= '<table style="width:60%;">';
    				foreach($hooks_array as $k=>$item){
    					
    					$_html .= '<tr>';
    						$_html .= '<td class="title_hook">'.$item.'</td>';
    						
    						$current_item = Configuration::get($this->name.'_'.$k.$prefix);
    						
    						$_html .= '<td class="title_hook">
    									<input type="checkbox" name="'.$k. $prefix.'" '. ($current_item == $k.$prefix ? 'checked="checked"' : '').' value="'.$k.$prefix.'"/>
    								  </td>';
    						
    						$si_img = Configuration::get($this->name.'sz'.$k.$prefix);
    						$_html .= '<td class="title_hook">
    									<select name="sz'.$k. $prefix.'">
	    									<option value="l'.$k.$prefix.'" '. ($si_img == 'l'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Large Image').'</option>
	    									<option value="ls'.$k.$prefix.'" '. ($si_img == 'ls'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Medium Image').'</option>
	    									<option value="s'.$k.$prefix.'" '. ($si_img == 's'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Small Image').'</option>
	    									<option value="sm'.$k.$prefix.'" '. ($si_img == 'sm'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Very Small Image').'</option>
    									</select>
    									</td>';
    						
    					$_html .= '</tr>';
    					
    				}
    				$_html .= '</table>';
    	
    		    			
    		    			
    		    		$_html .= '</div>';
    					
    	return $_html;
    			
    	
    }
    
    
    private function _imagesConnects($data){
    	
    $title_large = 	$data['title_large'];
    $title_medium = $data['title_medium'];
    $title_small = $data['title_small'];
    $title_very_small = $data['title_very_small'];
    
    $prefix_short = $data['prefix_short'];
    $prefix = $data['prefix'];
    
    include_once(dirname(__FILE__).'/classes/facebookhelp.class.php');
    $obj = new facebookhelp();
    $data_img = $obj->getImages(array('admin'=>1));
     
     
    
    $_html = '';

    $_html .= '<label>'.$title_large.'</label>
     
    <div class="margin-form">
    <input type="file" name="post_image_'.$prefix.'" id="post_image_'.$prefix.'" />';
     
    
    $_html .= '&nbsp;&nbsp;&nbsp;<img id="image'.$prefix_short.'" src="'.$data_img[$prefix].'">';
     
    if(Tools::strlen($data_img[$prefix.'_block'])>0)
    	$_html .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="image'.$prefix_short.'-click" style="text-decoration:underline" onclick="return_default_img(\''.$prefix.'\',\''.$this->l('Are you sure you want to remove this item?').'\')">'.$this->l('Click here to return the default image').'</a>';
    
    $_html .= '<p>Allow formats *.jpg; *.jpeg; *.png; *.gif.</p>';
    $_html .= '</div>';
     
     
     
    $large_small = "large_small";
    $_html .= '<label>'.$title_medium.'</label>
     
    <div class="margin-form">
    <input type="file" name="post_image_'.$prefix.$large_small.'" id="post_image_'.$prefix.$large_small.'" />';
     
     
    $_html .= '&nbsp;&nbsp;&nbsp;<img id="image'.$prefix_short.$large_small.'" src="'.$data_img[$prefix.$large_small].'">';
     
    if(Tools::strlen($data_img[$prefix.'_block'.$large_small])>0)
    	$_html .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="image'.$prefix_short.'-click'.$large_small.'" style="text-decoration:underline" onclick="return_default_img(\''.$prefix.$large_small.'\',\''.$this->l('Are you sure you want to remove this item?').'\')">'.$this->l('Click here to return the default image').'</a>';
    
    $_html .= '<p>Allow formats *.jpg; *.jpeg; *.png; *.gif.</p>';
    $_html .= '</div>';
     
     
     
     
    $_html .= '<label>'.$title_small.'</label>
     
    <div class="margin-form">
    <input type="file" name="post_image_'.$prefix.'small" id="post_image_'.$prefix.'small" />';
     
     
    $_html .= '&nbsp;&nbsp;&nbsp;<img id="image'.$prefix_short.'small" src="'.$data_img[$prefix.'small'].'">';
     
    if(Tools::strlen($data_img[$prefix.'_blocksmall'])>0)
    	$_html .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="image'.$prefix_short.'-clicksmall" style="text-decoration:underline" onclick="return_default_img(\''.$prefix.'small\',\''.$this->l('Are you sure you want to remove this item?').'\')">'.$this->l('Click here to return the default image').'</a>';
    
    $_html .= '<p>Allow formats *.jpg; *.jpeg; *.png; *.gif.</p>';
    $_html .= '</div>';
    
     
     
     
    $micro_small = "micro_small";
    $_html .= '<label>'.$title_very_small.'</label>
    
    <div class="margin-form">
    <input type="file" name="post_image_'.$prefix.$micro_small.'" id="post_image_'.$prefix.$micro_small.'" />';
    
    
    $_html .= '&nbsp;&nbsp;&nbsp;<img id="image'.$prefix_short.$micro_small.'" src="'.$data_img[$prefix.$micro_small].'">';
    
    if(Tools::strlen($data_img[$prefix.'_block'.$micro_small])>0)
    	$_html .= '&nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="image'.$prefix_short.'-click'.$micro_small.'" style="text-decoration:underline" onclick="return_default_img(\''.$prefix.$micro_small.'\',\''.$this->l('Are you sure you want to remove this item?').'\')">'.$this->l('Click here to return the default image').'</a>';
     
    $_html .= '<p>Allow formats *.jpg; *.jpeg; *.png; *.gif.</p>';
    $_html .= '</div>';
    
    return $_html;
    
    }
    
	private function _drawFacebookSettingsForm(){
    	$_html = '';
    	
    	
    	
    	$_html .= $this->_facebookhelp();
    	$_html .='<br/>';
    	
    	  $_html .= '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
      
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/views/img/settings_f.png" />'.$this->l('Facebook Settings').'</legend>
					
					';
    	
    	// enable or disable vouchers
    	$_html .= '<label>'.$this->l('Enable or Disable Facebook Connect').':</label>
				<div class="margin-form">
				
					<input type="radio" value="1" id="text_list_on" name="f_on" onclick="enableOrDisableFacebook(1)"
							'.(Tools::getValue('f_on', Configuration::get($this->name.'f_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_on" class="t"> 
						<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
					</label>
					
					<input type="radio" value="0" id="text_list_off" name="f_on" onclick="enableOrDisableFacebook(0)"
						   '.(!Tools::getValue('f_on', Configuration::get($this->name.'f_on')) ? 'checked="checked" ' : '').'>
					<label for="dhtml_off" class="t">
						<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
					</label>
					
					<p class="clear">'.$this->l('Enable or Disable Facebook Connect').'.</p>
				</div>';
    	
    	$_html .= '<script type="text/javascript">
			    	function enableOrDisableFacebook(id)
						{
						if(id==0){
							$("#block-facebook-settings").hide(200);
						} else {
							$("#block-facebook-settings").show(200);
						}
							
						}
					</script>';
    	
		$_html .= '<div id="block-facebook-settings" '.(Configuration::get($this->name.'f_on')==1?'style="display:block"':'style="display:none"').'>';
    	
    	
		// Facebook Application Id
    	$_html .= '<label>'.$this->l('Facebook Application Id:').'</label>
    			
    				<div class="margin-form">
					<input type="text" name="appid"  style="width:400px"
			                		value="'.Tools::getValue('appid', Configuration::get($this->name.'appid')).'">
					
				</div>';
    	
    	// Facebook Secret Key
		$_html .= '<label>'.$this->l('Facebook Secret Key:').'</label>
    			
    				<div class="margin-form">
					<input type="text" name="secret"  style="width:400px"
			                		value="'.Tools::getValue('secret', Configuration::get($this->name.'secret')).'">
					
				
				</div>';
		
		
		$_html .= '<br/><br/>';
		
		
		$_html .= $this->_positionConnect(
										  array(
										  		'title'=>$this->l('Position Facebook Connect Button'),
										  		'prefix'=>'f'
										  		)
										 );
		
		
		$_html .= '<br/><br/>';
		
		
		$_html .= $this->_imagesConnects(
										 array(
										 		'title_large'=>$this->l('Facebook Connect Large Image'),
										 		'title_medium'=>$this->l('Facebook Connect Medium Image'),
										 		'title_small'=>$this->l('Facebook Connect Small Image'),
										 		'title_very_small'=>$this->l('Facebook Connect Very Small Image'),
										 		'prefix_short'=>'f',
										 		'prefix'=>'facebook',
										 	  )
										);
			 
		
		
		
    	
    	
    	$_html .= '</div>';
		
    	$_html .= $this->_updateButton(array('name'=>'Facebook','prefix'=>'f'));
    	
		$_html .=	'</fieldset>'; 
		
		$_html .= '</form>';
    	
    	return $_html;
    }
    
private function _basicSettings(){
    	
    	$_html = '';
    	
    	  $_html .= '
        <form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
      
    	$_html .= '<fieldset>
					<legend><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Basic Settings').'</legend>
					
					';
    	$_html .= '<label>'.$this->l('Select your default customer group').':</label>
				<div class="margin-form">
					<select class=" select" name="defaultgroup" 
							id="defaultgroup">
						<option '.((Tools::getValue('defaultgroup', Configuration::get($this->name.'defaultgroup')) == '1') ? 'selected="selected" ' : '').' value="1">'.$this->l('Visitor').'</option>
						<option '.((Tools::getValue('defaultgroup', Configuration::get($this->name.'defaultgroup')) == '2') ? 'selected="selected" ' : '').' value="2">'.$this->l('Guest').'</option>
						<option '.((Tools::getValue('defaultgroup', Configuration::get($this->name.'defaultgroup')) == '3') ? 'selected="selected" ' : '').' value="3">'.$this->l('Customer').'</option>
					</select>
					<p class="clear">'.$this->l('This will use the default group once each customer is creating his own account through a social connector.').'</p>
				</div>';
    	
    	
    	
    	$_html .= '<br/><br/>';
    	
    	
    	$divLangName = "authp¤txtauthp";
    	
    	
    	 
    	// text on auth page
    	 
    	$_html .= '<label>'.$this->l('Text before logins').':</label>
    	 
    	<div class="margin-form">';
    	
    	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
    	$languages = Language::getLanguages(false);
    	
    	foreach ($languages as $language){
    		$id_lng = (int)$language['id_lang'];
    		$authp = Configuration::get($this->name.'authp'.'_'.$id_lng);
    	
    	
    		$_html .= '	<div id="authp_'.$language['id_lang'].'"
    		style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;width:85%"
    		>
    	
    		<input type="text" style="width:97%"
    					id="authp_'.$language['id_lang'].'"
    					name="authp_'.$language['id_lang'].'"
    					value="'.htmlentities(Tools::stripslashes($authp), ENT_COMPAT, 'UTF-8').'"/>
    		</div>';
    	}
    	$_html .= '';
    	ob_start();
    	$this->displayFlags($languages, $defaultLanguage, $divLangName, 'authp');
    	$displayflags = ob_get_clean();
    	$_html .= $displayflags;
    	
    	
    	$_html .= '<div style="clear:both"></div>';
    		
    	$_html .= '</div>';
    	// text on auth page
    	
    	
    	// Position Instagram Connect
    	$_html .= '<label>'.$this->l('Position Text before logins').':</label>
    	
    	';
    	$prefix = "txt";
    	
    	$top = Configuration::get($this->name.'_top'.$prefix);
    	$footer = Configuration::get($this->name.'_footer'.$prefix);
    	$authpage = Configuration::get($this->name.'_authpage'.$prefix);
    	
    	ob_start();?>
<style>
.choose_hooks input {
	margin-bottom: 10px
}
</style>

<div class="margin-form choose_hooks">
	<table style="width: 80%;">
		<tr>
			<td style="width: 33%; font-size: 12px">Footer</td>
			<td style="width: 33%; font-size: 12px">Authentication page</td>
			<td style="width: 33%; font-size: 12px">Top</td>
		</tr>
		<tr>
			<td><input type="checkbox" name="footer<?php echo $prefix?>"
				<?php echo ($footer == 'footer'.$prefix ? 'checked="checked"' : '')?>
				value="footer<?php echo $prefix?>" /></td>
			<td><input type="checkbox" name="authpage<?php echo $prefix?>"
				<?php echo ($authpage == 'authpage'.$prefix ? 'checked="checked"' : '')?>
				value="authpage<?php echo $prefix?>" /></td>
			<td><input type="checkbox" name="top<?php echo $prefix?>"
				<?php echo ($top == 'top'.$prefix ? 'checked="checked"' : '')?>
				value="top<?php echo $prefix?>" /></td>
		</tr>

	</table>
</div>


<?php 	$_html .= ob_get_contents();
    	  	ob_end_clean();
    	
    	$_html .= '<br/><br/>';
    	
    	
    	
    	// enable or disable information block on the account page
    	$_html .= '<label>'.$this->l('Show information block on the account page').'?</label>
    	
		    	<div class="margin-form">
		    	
			    	<input type="radio" value="1" id="text_list_on" name="iauth"  onclick="enableOrDisableIAuth(1)"
			    	'.(Tools::getValue('iauth', Configuration::get($this->name.'iauth')) ? 'checked="checked" ' : '').'>
			    	<label for="dhtml_on" class="t">
			    	<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
			    	</label>
			    		
			    	<input type="radio" value="0" id="text_list_off" name="iauth" onclick="enableOrDisableIAuth(0)"
			    	'.(!Tools::getValue('iauth', Configuration::get($this->name.'iauth')) ? 'checked="checked" ' : '').'>
			    	<label for="dhtml_off" class="t">
			    	<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
			    	</label>
		    		
		    	<p class="clear">'.$this->l('Show information block on the account page').'.</p>
    	</div>';
    	
    	$_html .= '<script type="text/javascript">
    	function enableOrDisableIAuth(id)
    	{
    	if(id==0){
    	$("#block-iauth-settings").hide(200);
    	} else {
    	$("#block-iauth-settings").show(200);
    	}
    	
    	}
    	</script>';
    	
    	$_html .= '<div id="block-iauth-settings" '.(Configuration::get($this->name.'iauth')==1?'style="display:block"':'style="display:none"').'>';
    	
    	 
    	
    	
    	// text on auth page
    	
    	$_html .= '<label>'.$this->l('Text in the information block on the account page').':</label>
    	
    	<div class="margin-form">';
    	 
    	$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
    	$languages = Language::getLanguages(false);
    	 
    	foreach ($languages as $language){
    		$id_lng = (int)$language['id_lang'];
    		$txtauthp = Configuration::get($this->name.'txtauthp'.'_'.$id_lng);
    		 
    		 
    		$_html .= '	<div id="txtauthp_'.$language['id_lang'].'"
    		style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;width:85%"
    		>
    		 
    		<input type="text" style="width:97%"
    		id="txtauthp_'.$language['id_lang'].'"
    		name="txtauthp_'.$language['id_lang'].'"
    		value="'.htmlentities(Tools::stripslashes($txtauthp), ENT_COMPAT, 'UTF-8').'"/>
    		</div>';
    	}
    	$_html .= '';
    	ob_start();
    	$this->displayFlags($languages, $defaultLanguage, $divLangName, 'txtauthp');
    	$displayflags = ob_get_clean();
    	$_html .= $displayflags;
    	 
    	 
    	$_html .= '<div style="clear:both"></div>';
    	
    	$_html .= '</div>';
    	// text on auth page
    	
    	$_html .= '</div>';
    	 
    	
    	
    	$_html .= $this->_updateButton(array('name'=>'Basic','prefix'=>'basic'));
    	
		$_html .=	'</fieldset>'; 
		
		$_html .= '</form>';
    	
    	return $_html;
    }
    
 private function _updateButton($data){
 	
 		$name = isset($data['name'])?$data['name']:'';
 		$prefix = isset($data['prefix'])?$data['prefix']:'';
 		
    	$_html = '';
    	$_html .= '<p class="center" class="update-button" style="text-align:center;padding: 10px; margin-top: 10px;">
					<input type="submit" name="submit'.$prefix.'" value="'.$this->l('Update').' '.$name.' '.$this->l('settings').'" 
                		   class="button"  />
                	</p>';
    	
    	
    	
    	return $_html;
    	
    }
    
    
private function _headercssfiles(){
		$_html = '';
    
		$_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/javascript.js"></script>';
    	
		$_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/views/css/custom_menu.css" type="text/css" />';
    	$_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/custom_menu.js"></script>';
    
    	$_html .= '<style type="text/css">';
    	
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_html .= '.nobootstrap{min-width:inherit!important}
						.nobootstrap fieldset, .nobootstrap legend{
							background-color:white!important;
							border-radius: 4px;
						}
						
						.nobootstrap .display-form input,
						.nobootstrap .display-form select{
							border-radius: 4px;	
						}
						
						.nobootstrap .display-form .update-button{
							border:0px!important
						}
    		
    		
    					.displayed_flag {
							float: left;
							margin: 4px 0 0 4px;
						}
						
						.language_flags {
							display: none;
							float: left;
							background: #FFF;
							margin: 4px;
							padding: 8px;
							width: 80px;
							border: 1px solid #555;
						}
						.pointer {
						    cursor: pointer;
						}
						
						.display-form .alert-success {
					    		background-color: #dff0d8;
					    		border-color: #d6e9c6;
					    		color: #3c763d;
					    		padding:10px;
    					}
    					.display-form .alert-danger 
    					{
						    background-color: #f2dede;
						    border-color: #ebccd1;
						    color: #a94442;
						    padding:10px;
						}
    		
    					';
    		
    		
    	} else {
    	$_html .= '.update-button{border: 1px solid #EBEDF4;}';
    	}
    	
    	$_html .= '</style>';
    	
    	
    	
    	return $_html;
	}
	
	public function translateCustom(){
		return array('billing_address'=>$this->l('Delivery Address'));
	}		
	
	public function getConnetsArrayPrefix(){
		
	return array(
				 'f'=>'facebook',
				 't'=>'twitter',
				 'g'=>'google',
				 'y'=>'yahoo',
			     'l'=>'linkedin',
				 'm'=>'microsoft',
				 'i'=>'instagram',	
				 'p'=>'paypal',
				 'fs'=>'foursquare',
				 'gi'=>'github',
				 'd'=>'disqus',
				  'a'=>'amazon',
				);	
	}
	
	
}