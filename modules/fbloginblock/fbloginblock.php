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

if (!defined('_PS_VERSION_'))
	exit;

class fbloginblock extends Module
{
	private $_http_referer;
	private $_is15;
	private $_is16;
	private $_translations;
	private $_multiple_lang;

	
	private $_step = 25;

    private $_is_instagram = 0;
    private $_is_mailru = 0;
    private $_is_odnoklassniki = 0;
    private $_is_yandex = 0;

    private $_all_social_types;


    public function __construct()
 	{
 	 	$this->name = 'fbloginblock';
 	 	$this->version = '1.8.0';
 	 	$this->tab = 'social_networks';
 	 	$this->author = 'SPM';
 	 	$this->module_key = "86adfe9f51496e857a90dfc487b2e79a";
 	 	$this->bootstrap = true;
 	 	$this->need_instance = 0;
 	 	//$this->ps_versions_compliancy = array('min' => '1.5', 'max' => _PS_VERSION_);
 	 	
 	 	$this->_html = '';

        if(version_compare(_PS_VERSION_, '1.7', '<'))
            require_once(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_ . $this->name . '/backward_compatibility/backward_functions.php');
        }
 	 	
 	 	if(version_compare(_PS_VERSION_, '1.5', '>'))
			$this->_is15 = 1;
		else
			$this->_is15 = 0;
			
 			
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
		

			
		parent::__construct();
		$this->page = basename(__FILE__, '.php');
		
		
		$this->displayName = $this->l('Social Connects 16 in 1 + Statistics');
		$this->description = $this->l('Add Social Connects 16 in 1 + Statistics');
		
		$this->confirmUninstall = $this->l('Are you sure you want to remove it ? Be careful, all your configuration and your data will be lost');


		$this->_http_referer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';



        ## handle types , when customer does not have email ##
        $_all_social_types = $this->getConnetsArrayPrefix();

        $_all_social_types_tmp = array();
        foreach($_all_social_types as $_key_type => $_values_type) {

            $_digit_type = $_values_type['type'];
            $_name_type = Tools::ucfirst($_values_type['prefix']);

            $_all_social_types_tmp_item = array();
            $_all_social_types_tmp_item['prefix'] = $_key_type;
            $_all_social_types_tmp_item['name'] = $_name_type;

            $_all_social_types_tmp[$_digit_type] = $_all_social_types_tmp_item;
            /*$this->_all_social_types = array(
                1 => array('prefix' => 'f', 'name' => 'Facebook'),
            );*/

        }

        $this->_all_social_types = $_all_social_types_tmp;
        ## handle types , when customer does not have email ##

		
		$this->initContext();


        ## prestashop 1.7 ##
        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_.$this->name.'/classes/ps17helpfbloginblock.class.php');
            $ps17help = new ps17helpfbloginblock();
            $ps17help->setMissedVariables();
        } else {
            $smarty = $this->context->smarty;
            $smarty->assign($this->name.'is17' , 0);
        }
        ## prestashop 1.7 ##
 	}
 	

 	
 	
 	private function initContext()
	{
	  $this->context = Context::getContext();
	  if (version_compare(_PS_VERSION_, '1.5', '>')){
	 	 $this->context->currentindex = AdminController::$currentIndex;
	  } else {
	  	$variables14 = variables_fbloginblock14();
	  	$this->context->currentindex = $variables14['currentindex'];
	  }


        $this->context->custom_cookie_api_popup = $_COOKIE;

	}
 	
	public function install()
	{
		
		### fixed bug for override ###
		if (version_compare(_PS_VERSION_, '1.5', '>')){
			
			$AdminCustomersController_folder = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."override".DIRECTORY_SEPARATOR."controllers".DIRECTORY_SEPARATOR."admin".DIRECTORY_SEPARATOR;
			$overrides_hack = $AdminCustomersController_folder."AdminCustomersController.php";
			if(!file_exists($overrides_hack)){
				$AdminCustomersController = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR."backward_compatibility".DIRECTORY_SEPARATOR."AdminCustomersController.php";
				custom_copy_fbloginblock($AdminCustomersController,$AdminCustomersController_folder."AdminCustomersController.php");
			}
		}
		### fixed bug for override ###
		
		
	 	if (!parent::install())
	 		return false;
	 		
	 	
	 	Configuration::updateValue('redirpage', 2);
	 	
	 	if (version_compare(_PS_VERSION_, '1.5', '>')){
	 		Configuration::updateValue($this->name.'defaultgroup', 3);
	 	} else {
	 		Configuration::updateValue($this->name.'defaultgroup', 1);
	 	}
	 	
	 	$languages = Language::getLanguages(false);
	 	foreach ($languages as $language){
	 		$i = $language['id_lang'];
	 		$authp = $this->l('Connect with:');
	 		Configuration::updateValue($this->name.'authp_'.$i, $authp);
	 		
	 		$txtauthp = $this->l('You can use any of the login buttons above to automatically create an account on our shop.');
	 		Configuration::updateValue($this->name.'txtauthp_'.$i, $txtauthp);
	 	}
	 	
	 	$prefix = "txt";
	 	Configuration::updateValue($this->name.'_top'.$prefix, 'top'.$prefix);
	 	Configuration::updateValue($this->name.'_authpage'.$prefix, 'authpage'.$prefix);
	 	Configuration::updateValue($this->name.'_footer'.$prefix, 'footer'.$prefix);



        Configuration::updateValue($this->name.'_rcblock', 'rcblock');
        Configuration::updateValue($this->name.'_lcblock', 'lcblock');

        Configuration::updateValue($this->name.'iauth', 1);

        Configuration::updateValue($this->name.'is_soc_link', 1);


        if($this->_is15) {

            $all_shops = Shop::getShops();

            foreach($all_shops as $_shop){

                $id_shop_group = (int)$_shop['id_shop_group'];
                $id_shop = (int)$_shop['id_shop'];


                // google connect
                Configuration::updateValue($this->name.'oru', $this->getRedirectURL(array('typelogin'=>'google','is_settings'=>1)), false,$id_shop_group, $id_shop);
                // google connect

                if($this->_is_instagram) {
                    // instagram connect
                    Configuration::updateValue($this->name . 'iru', $this->getRedirectURL(array('typelogin' => 'instagram', 'is_settings' => 1)), false, $id_shop_group, $id_shop);
                    // instagram connect
                }

                // foursquare connect
                Configuration::updateValue($this->name.'fsru', $this->getRedirectURL(array('typelogin'=>'foursquare','is_settings'=>1)), false, $id_shop_group, $id_shop);
                // foursquare connect

                // github connect
                Configuration::updateValue($this->name.'giru', $this->getRedirectURL(array('typelogin'=>'github','is_settings'=>1)), false, $id_shop_group, $id_shop);

                // disqus connect
                Configuration::updateValue($this->name.'dru', $this->getRedirectURL(array('typelogin'=>'disqus','is_settings'=>1)), false, $id_shop_group, $id_shop);

                // amazon connect
                Configuration::updateValue($this->name.'aru', $this->getRedirectURL(array('typelogin'=>'amazon','is_settings'=>1)), false, $id_shop_group, $id_shop);

                //paypal connect
                Configuration::updateValue($this->name.'pcallback', $this->getRedirectURL(array('typelogin'=>'paypal','is_settings'=>1)), false, $id_shop_group, $id_shop);
            }



        } else {
            // google connect
            Configuration::updateValue($this->name . 'oru', $this->getRedirectURL(array('typelogin' => 'google', 'is_settings' => 1)));
            // google connect

            if($this->_is_instagram) {
                // instagram connect
                Configuration::updateValue($this->name . 'iru', $this->getRedirectURL(array('typelogin' => 'instagram', 'is_settings' => 1)));
                // instagram connect
            }

            // foursquare connect
            Configuration::updateValue($this->name . 'fsru', $this->getRedirectURL(array('typelogin' => 'foursquare', 'is_settings' => 1)));
            // foursquare connect

            // github connect
            Configuration::updateValue($this->name . 'giru', $this->getRedirectURL(array('typelogin' => 'github', 'is_settings' => 1)));

            // disqus connect
            Configuration::updateValue($this->name . 'dru', $this->getRedirectURL(array('typelogin' => 'disqus', 'is_settings' => 1)));

            // amazon connect
            Configuration::updateValue($this->name . 'aru', $this->getRedirectURL(array('typelogin' => 'amazon', 'is_settings' => 1)));

            //paypal connect
            Configuration::updateValue($this->name . 'pcallback', $this->getRedirectURL(array('typelogin' => 'paypal', 'is_settings' => 1)));
        }
	 	
	 	

        $array_need = $this->getConnetsArrayPrefix();
	 	foreach($array_need as $prefix => $val){
	 		Configuration::updateValue($this->name.'sztop'.$prefix, 'bltop'.$prefix);
	 		Configuration::updateValue($this->name.'szrightcolumn'.$prefix, 'bsrightcolumn'.$prefix);
	 		Configuration::updateValue($this->name.'szleftcolumn'.$prefix, 'bsleftcolumn'.$prefix);
	 		
	 		Configuration::updateValue($this->name.'szfooter'.$prefix, 'blfooter'.$prefix);

            Configuration::updateValue($this->name.'szbeforeauthpage'.$prefix, 'blsbeforeauthpage'.$prefix);
	 		Configuration::updateValue($this->name.'szauthpage'.$prefix, 'blsauthpage'.$prefix);
	 		Configuration::updateValue($this->name.'szwelcome'.$prefix, 'bsmwelcome'.$prefix);
            Configuration::updateValue($this->name.'szchook'.$prefix, 'blschook'.$prefix);
	 		
	 		
	 		Configuration::updateValue($this->name.'_top'.$prefix, 'top'.$prefix);
            Configuration::updateValue($this->name.'_footer'.$prefix, 'footer'.$prefix);
	 		Configuration::updateValue($this->name.'_rightcolumn'.$prefix, 'rightcolumn'.$prefix);
	 		Configuration::updateValue($this->name.'_leftcolumn'.$prefix, 'leftcolumn'.$prefix);

            Configuration::updateValue($this->name.'_beforeauthpage'.$prefix, 'beforeauthpage'.$prefix);
	 		Configuration::updateValue($this->name.'_authpage'.$prefix, 'authpage'.$prefix);
	 		Configuration::updateValue($this->name.'_welcome'.$prefix, 'welcome'.$prefix);

            Configuration::updateValue($this->name.'_chook'.$prefix, 'chook'.$prefix);
	 		
	 		Configuration::updateValue($this->name.$prefix.'_on', 1);
	 	}
	 	
	 	if($this->_is15 == 1)
	 		$this->createAdminTabs();

        ## dashboard statistics ##
        if($this->_is16){
            if(!$this->registerHook('dashboardZoneOne')) {
                return false;
            }
        }
        ## dashboard statistics ##


	 	if (!$this->registerHook('leftColumn') 
			|| !$this->registerHook('rightColumn') 
			|| !$this->registerHook('header')
            || !$this->registerHook('customerAccount')
            || !$this->registerHook('myAccountBlock')
	 		|| !((version_compare(_PS_VERSION_, '1.5', '>'))? $this->registerHook('DisplayBackOfficeHeader') : true)
	 		|| !$this->createCustomerTbl() 
	 		|| !$this->_createFolderAndSetPermissions()
	 		|| !$this->createUserTwitterTable()
	 		|| !(($this->_is_instagram == 1)?$this->_createInstagramTable():true)
	 		|| !$this->createTumblrTable()
	 		|| !$this->createPinterestTable()
            || !$this->createLinkedSocialConnectTable()
	 		|| !(($this->_is_odnoklassniki)?$this->createOklassTable():true)
	 		|| !((version_compare(_PS_VERSION_, '1.5', '>'))? $this->registerHook('socialConnectSpm') : true)
	 		)
			return false;
	 	
	 	
	 	$path_to_delete_class_index = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR."class_index.php";
	 	if(file_exists($path_to_delete_class_index)){
	 		@unlink($path_to_delete_class_index);
	 	}
	 	 
	 	
	 	return true;
	}


    public function hookDashboardZoneOne($params)
    {
        $smarty = $this->context->smarty;
        include_once(_PS_MODULE_DIR_.$this->name.'/classes/statisticshelp.class.php');
        $obj_help = new statisticshelp();
        $data_total = $obj_help->totalCustomers();
        $count_all = $data_total['count_all'];

        $data = array();

        foreach($this->getConnetsArrayPrefix() as $data_prefix){

            $text_type = $data_prefix['prefix'];
            //$id_type = $data_prefix['type'];


            $data[$text_type] = $data_total['count_types'][$text_type];
        }

        arsort($data);

        $smarty->assign($this->name.'data_dash', $data);
        $smarty->assign($this->name.'call_dash', $count_all);

        return $this->display(__FILE__, 'views/templates/hooks/dashboard_zone_one.tpl');
    }
	
	public function hookDisplayBackOfficeHeader()
	{
	
		if(version_compare(_PS_VERSION_, '1.6', '>')) {
            $base_dir = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;


            $css = '';
            $css .= '<style type="text/css">
					.icon-AdminStat:before {
					content: url("' . $base_dir . 'modules/' . $this->name . '/views/img/statistics.png");
					}
				</style>
		';

            return $css;
        }
	}
	
	public function uninstall()
	{
		
		if($this->_is15 == 1)
			$this->uninstallTab();
		
		
		 
		$languages = Language::getLanguages(false);
		foreach ($languages as $language){
			$i = $language['id_lang'];
			Configuration::deleteByName($this->name.'authp_'.$i);
			Configuration::deleteByName($this->name.'txtauthp_'.$i);
		}
		 
		$prefix = "txt";
		 
		
		$array_need = $this->getConnetsArrayPrefix();
		foreach($array_need as $prefix => $val){
			Configuration::deleteByName($this->name.'sztop'.$prefix);
			Configuration::deleteByName($this->name.'szrightcolumn'.$prefix);
			Configuration::deleteByName($this->name.'szleftcolumn'.$prefix);
		
			Configuration::deleteByName($this->name.'szfooter'.$prefix);
				
			Configuration::deleteByName($this->name.'szauthpage'.$prefix);
			Configuration::deleteByName($this->name.'szwelcome'.$prefix);
			Configuration::deleteByName($this->name.'szchook'.$prefix);
		
			Configuration::deleteByName($this->name.'_chook'.$prefix);
			Configuration::deleteByName($this->name.'_top'.$prefix);
			Configuration::deleteByName($this->name.'_footer'.$prefix);
			Configuration::deleteByName($this->name.'_rightcolumn'.$prefix);
			Configuration::deleteByName($this->name.'_leftcolumn'.$prefix);
				
			Configuration::deleteByName($this->name.'_authpage'.$prefix);
			Configuration::deleteByName($this->name.'_welcome'.$prefix);


			Configuration::deleteByName($this->name.$prefix.'_on');
		}
		
		
		if (!$this->uninstallTable() || !parent::uninstall()
			|| !Configuration::deleteByName($this->name.'defaultgroup')
			|| !Configuration::deleteByName($this->name.'_top'.$prefix)
			|| !Configuration::deleteByName($this->name.'_authpage'.$prefix)
			|| !Configuration::deleteByName($this->name.'_footer'.$prefix)
			|| !Configuration::deleteByName($this->name.'iauth')
            || !Configuration::deleteByName($this->name.'is_soc_link')
			|| !Configuration::deleteByName('redirpage')
			)
			return false;
		return true;
	}
	
	public function uninstallTable() {
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.$this->name.'_img');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'fb_customer');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'tw_customer');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'customers_statistics_spm');
        if($this->_is_instagram) {
            Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'instagram_spm');
        }
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'tumblr_spm');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'pinterest_spm');
        if($this->_is_odnoklassniki) {
            Db::getInstance()->Execute('DROP TABLE IF EXISTS ' . _DB_PREFIX_ . 'oklass_spm');
        }

        Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'customer_linked_social_account');

		
		return true;
	}
	
	
	private function _createFolderAndSetPermissions(){
		
		$prev_cwd = getcwd();
		
		$module_dir = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR;
		@chdir($module_dir);
		
		//folder logo
		
		$module_dir_img = $module_dir.$this->name.DIRECTORY_SEPARATOR; 
		@mkdir($module_dir_img, 0777);

		@chdir($prev_cwd);
		
		return true;
	} 
	
	public function createAdminTabs(){
	
	
		$langs = Language::getLanguages();
		
		$tab0 = new Tab();
		$tab0->class_name = "AdminStat";
		$tab0->module = $this->name;
		$tab0->id_parent = 0;
		foreach ($langs as $l) {
			$tab0->name[$l['id_lang']] = $this->l('Statistics');
		}
		$tab0->save();
		$main_tab_id = $tab0->id;
		
		$tab1 = new Tab();
		$tab1->class_name = "AdminStatistics";
		$tab1->module = $this->name;
		$tab1->id_parent = $main_tab_id;
		foreach ($langs as $l) {
			$tab1->name[$l['id_lang']] = $this->l('Customer Statistics');
		}
		$tab1->save();


        $tab_ajax = new Tab();
        $tab_ajax->module = $this->name;
        $tab_ajax->active = 0;
        $tab_ajax->class_name = 'AdminFbloginblockajax';
        $tab_ajax->id_parent = (int)Tab::getIdFromClassName($this->name);
        foreach (Language::getLanguages(true) as $lang)
            $tab_ajax->name[$lang['id_lang']] = 'Fbloginblockajax';
        $tab_ajax->add();
	
	}
	
	private function uninstallTab(){
	
	 	$tab_id = Tab::getIdFromClassName("AdminStat");
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminStatistics");
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }


        $tab_id = Tab::getIdFromClassName("AdminFbloginblockajax");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }
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
				  '.(version_compare(_PS_VERSION_, '1.5', '>')?'email_stat':'email').' text,
				  `id_shop` int(11) NOT NULL default \'0\',
				  `type` int(11) NOT NULL default \'0\' ,
				  PRIMARY KEY  (`id`),
				  KEY customer_id (customer_id),
				  KEY id_shop (id_shop),
				  KEY `type` (`type`)
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
	
	public function createTumblrTable(){
		$db = Db::getInstance();
		$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'tumblr_spm
		(
		id INT PRIMARY KEY AUTO_INCREMENT,
		tumblr_id varchar(500) NOT NULL,
		`user_id` int(11) NOT NULL,
		`id_shop` int(11) NOT NULL default \'0\'
		) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").'  DEFAULT CHARSET=utf8;';
		
		$db->Execute($query);
		return true;
	}
	
	public function createPinterestTable(){
		$db = Db::getInstance();
		$query = 'CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'pinterest_spm
		(
		id INT PRIMARY KEY AUTO_INCREMENT,
		pinterest_id bigint(20) NOT NULL,
		`user_id` int(11) NOT NULL,
		`id_shop` int(11) NOT NULL default \'0\'
		) ENGINE='.(defined('_MYSQL_ENGINE_')?_MYSQL_ENGINE_:"MyISAM").'  DEFAULT CHARSET=utf8;';
		
		$db->Execute($query);
		return true;
	}
	
	public function createOklassTable(){

            $db = Db::getInstance();
            $query = 'CREATE TABLE IF NOT EXISTS ' . _DB_PREFIX_ . 'oklass_spm
		(
		id INT PRIMARY KEY AUTO_INCREMENT,
		oklass_id bigint(20) NOT NULL,
		`user_id` int(11) NOT NULL,
		`id_shop` int(11) NOT NULL default \'0\'
		) ENGINE=' . (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : "MyISAM") . '  DEFAULT CHARSET=utf8;';

            $db->Execute($query);

		return true;
	}


    public function createLinkedSocialConnectTable()
    {


        $db = Db::getInstance();

        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'customer_linked_social_account` (
					  `id_customer` int(11) NOT NULL auto_increment,
					  `id_shop` int(11) NOT NULL default \'0\',
					  `type` int(11) NOT NULL default \'0\',
					  `email` text,
                      KEY `id_customer` (`id_customer`),
                      KEY `id_shop` (`id_shop`),
                      KEY `type` (`type`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8;';

        $db->Execute($sql);


        return true;


    }
	
	public function hooksocialConnectSpm($params)
	{
		$this->settings($params);
		
		return $this->display(__FILE__, 'views/templates/hooks/socialconnectsmp.tpl');
	}


    public function getOrderPage($data = null){
        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;


        $http_referrer = isset($data['http_referrer'])?$data['http_referrer']:$this->_http_referer;



        $http_referrer_orig = $http_referrer;

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
        $order_page = 0;

        if(version_compare(_PS_VERSION_, '1.5', '>')){

            $data = explode("?",$http_referrer);
            $data  = end($data);
            $data_url_rewrite_on = explode("/",$http_referrer);
            $data_url_rewrite_on = end($data_url_rewrite_on);
            $data_url_rewrite_on = str_replace("?addingCartRule=1","",$data_url_rewrite_on);

            $link = new Link();
            $my_account = $link->getPageLink("my-account", true, $id_lang,null,false,$this->getIdShop());


            //$authentication = $link->getPageLink("authentication", true, $id_lang,null,false,$this->getIdShop());




            $order = $link->getPageLink("order", true, $id_lang,null,false,$this->getIdShop());
            $order_orig = $order;
            //var_dump($order);

            $quick_order = $link->getPageLink("order-opc", true, $id_lang,null,false,$this->getIdShop());


            $req_uri = $_SERVER['REQUEST_URI'];

            // for my account
            //$req_uri_for_my_account1 = $req_uri;
            // for my account

            $data_quick_order = explode("?",$req_uri);
            $data_quick_order  = end($data_quick_order);

            // for my account page
            //$req_uri_for_my_account2 = $data_quick_order;
            // for my account

            $data_ur_quick_order_rewrite_on = explode("/",$req_uri);
            $data_ur_quick_order_rewrite_on = end($data_ur_quick_order_rewrite_on);


            if(version_compare(_PS_VERSION_, '1.6', '>')){
                $_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
            } else {
                $_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
            }

            if($this->_is15) {
                $current_shop_id = Shop::getContextShopID();

                if($current_shop_id) {

                    $is_ssl = false;
                    if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || Configuration::get('PS_SSL_ENABLED'))
                        $is_ssl = true;

                    $shop_obj = new Shop($current_shop_id);

                    $_http_host = $shop_obj->getBaseURL($is_ssl);

                }

            }

            if(!Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Configuration::get('PS_SSL_ENABLED')){
                $_http_host = str_replace("https://","http://",$_http_host); // this is prevent bug if page have https
                $_http_host = str_replace("http://","https://",$_http_host);
            }


            $order = str_replace($_http_host.$iso_lang,'',$order);
            $quick_order = str_replace($_http_host.$iso_lang,'',$quick_order);

            if(Configuration::get('PS_REWRITING_SETTINGS'))
                $uri = str_replace($_http_host,'',$my_account);
            else
                $uri = 'index.php?controller=my-account&id_lang='.$id_lang;


            /*if(version_compare(_PS_VERSION_, '1.6', '>')){
                $_http_host_only = Tools::getShopDomainSsl(true, true);
            } else {
                $_http_host_only = _PS_BASE_URL_SSL_;
            }*/



            if(
                str_replace("?".$data,"",$http_referrer_orig) == $order_orig ||
                //$_http_host_only.str_replace("?".$req_uri_for_my_account2,"",$req_uri_for_my_account1) == $authentication ||

                $data == 'controller=order' || $data_url_rewrite_on == 'order' || $data == $order || $data_url_rewrite_on == $order ||
                $data == 'controller=quick-order'
                || $data_url_rewrite_on == 'quick-order' ||  $data_quick_order == $quick_order ||  $data_ur_quick_order_rewrite_on == $quick_order
                || $data == 'controller=cart&action=show' || $data == 'action=show'){

                $order_page = 1;


                if($data == 'controller=order' || $data == $order || $data == 'controller=cart&action=show') {

                    $uri = 'index.php?controller=order&step=1&id_lang=' . $id_lang;
                }elseif($data_url_rewrite_on == 'order' || $data == 'action=show' || $data_url_rewrite_on == $order) {

                    $uri = $iso_lang . $order . '?step=1';
                } elseif($data == 'controller=quick-order' || $data_quick_order == $quick_order){

                    $uri = 'index.php?controller=order-opc&step=1&id_lang=' . $id_lang;
                } elseif($data_url_rewrite_on == 'quick-order' ||  $data_ur_quick_order_rewrite_on == $quick_order){

                    $uri = $iso_lang . $quick_order;
                }


            }
            $smarty->assign($this->name.'order_page', $order_page);
        } else {
            $data = explode("/",$http_referrer);
            $data  = end($data);

            if(Configuration::get('PS_REWRITING_SETTINGS') && version_compare(_PS_VERSION_, '1.4', '>'))
                $uri = $iso_lang.'my-account';
            else
                $uri = 'my-account.php?id_lang='.$id_lang;
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

        $smarty->assign($this->name.'uri', $uri);


        // only for amazon conenct //
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $cookie = new Cookie('refamazon');
            $cookie->order_page_amazon = $order_page;
            $cookie->req_uri_amazon = $_SERVER['REQUEST_URI'];

        }
        // only for amazon conenct //



        if(version_compare(_PS_VERSION_, '1.5', '>')) {
            // if order page only when in the admin panel -> Preferences -> General: "Enable SSL" = "YES" and "Enable SSL on all pages" = NO

            if ($order_page == 1 && (!Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Configuration::get('PS_SSL_ENABLED'))) {

                if (version_compare(_PS_VERSION_, '1.6', '>')) {
                    $http_host = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;
                } else {
                    $http_host = _PS_BASE_URL_SSL_ . __PS_BASE_URI__;
                }
                $http_referrer = $http_host . $uri;
            }
            // if order page only when in the admin panel -> Preferences -> General: "Enable SSL" = "YES" and "Enable SSL on all pages" = NO

            // if order page
            if ($order == str_replace($_http_host, "", $http_referrer)) {

                $uri = $order;
                $order_page = 1;
            }
            // if order page

            // if quick order page
            if ($quick_order == str_replace($_http_host, "", $http_referrer)) {

                $uri = $quick_order;
                $order_page = 1;
            }
            // if quick order page
        }

        $smarty->assign($this->name.'http_referer', $http_referrer);






        $prefix_oder = '';
        if($order_page) {

            $delimeter_rewrite = "&";
            if(Configuration::get('PS_REWRITING_SETTINGS')){
                $delimeter_rewrite = "?";
            }

            $prefix_oder = $delimeter_rewrite.'step=1';
        }






        return array('uri'=>$uri,'order_page'=>$order_page,'http_referrer'=>$http_referrer.$prefix_oder,'http_referrer_orig'=>$http_referrer_orig.$prefix_oder);
    }



    public function hookHeader($params){
    	$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;
		
		
		$id_lang = (int)$cookie->id_lang;

         $is_logged = isset($cookie->id_customer)?1:0;
         $smarty->assign($this->name.'islogged', $is_logged);


         if(version_compare(_PS_VERSION_, '1.5', '>') && !$is_logged){
             $this->context->controller->addCSS(($this->_path).'views/css/font-awesome.min.css', 'all');
             $this->context->controller->addCSS(($this->_path).'views/css/'.$this->name.'.css', 'all');
             $this->context->controller->addJS($this->_path . 'views/js/'.$this->name.'.js');

         }

         if(version_compare(_PS_VERSION_, '1.5', '>')) {
             if (Configuration::get($this->name . '_rcblock') == 'rcblock' || Configuration::get($this->name . '_lcblock') == 'lcblock') {
                 $this->context->controller->addCSS(($this->_path) . 'views/css/blocks-' . $this->name . '.css', 'all');
             }
         }
		
		$smarty->assign($this->name.'authp', Configuration::get($this->name.'authp_'.$id_lang));
    	
    	$prefix = "txt";
    	$smarty->assign($this->name.'_top'.$prefix, Configuration::get($this->name.'_top'.$prefix));
    	$smarty->assign($this->name.'_footer'.$prefix, Configuration::get($this->name.'_footer'.$prefix));
    	$smarty->assign($this->name.'_authpage'.$prefix, Configuration::get($this->name.'_authpage'.$prefix));
    	
    	$smarty->assign($this->name.'iauth', Configuration::get($this->name.'iauth'));
    	$smarty->assign($this->name.'txtauthp', Configuration::get($this->name.'txtauthp_'.$id_lang));


        $smarty->assign($this->name.'_rcblock', Configuration::get($this->name.'_rcblock'));
        $smarty->assign($this->name.'_lcblock', Configuration::get($this->name.'_lcblock'));



        $data_tw = $this->twTranslate();

        $smarty->assign($this->name.'api_one_1', $data_tw['api_one_1']);
        $smarty->assign($this->name.'api_one_2', $data_tw['api_one_2']);
        $smarty->assign($this->name.'api_two1', $data_tw['api_two1']);
        $smarty->assign($this->name.'api_two2', $data_tw['api_two2']);

        $this->settings($params);


    	
    	#### show popup for twitter customer which not changed email address  #####

         /// if social api not provide email ////
         $is_exists_provide_email = 0;
         if($cookie->id_customer){
             $customer_email = $cookie->email;


             $is_exists_provide_email = stripos($customer_email,"api-not-provide-email");
             $smarty->assign($this->name.'cid', $cookie->id_customer);
         }

        //var_dump($is_exists_provide_email);exit;




         $is_not_provide_email = 0;
         $htmlapipopup = '';
         if($is_exists_provide_email){


             $social_type_not_provide = explode("-",$customer_email);
             $social_type_not_provide = end($social_type_not_provide);
             $social_type_not_provide = explode(".",$social_type_not_provide);
             $social_type_not_provide = current($social_type_not_provide);

             $data_all_social_types = $this->_all_social_types[$social_type_not_provide];
             $name_cosial_type = isset($data_all_social_types['name'])?$data_all_social_types['name']:'';
             $name_cosial_prefix = isset($data_all_social_types['prefix'])?$data_all_social_types['prefix']:'';


             $cookie_is_hidden = $this->context->custom_cookie_api_popup;
             $name_cookie = "popup-".$name_cosial_prefix;
             $is_hidden = isset($cookie_is_hidden[$name_cookie])?$cookie_is_hidden[$name_cookie]:0;


             if($is_hidden !== 'hidden') {

                 if(version_compare(_PS_VERSION_, '1.5', '>')) {
                     $this->context->controller->addJS($this->_path . 'views/js/' . $this->name . '-apipopup.js');
                     $this->context->controller->addCSS(($this->_path) . 'views/css/' . $this->name . '-apipopup.css', 'all');
                 }

                 $smarty->assign($this->name . 'stname', $name_cosial_type);
                 $smarty->assign($this->name . 'stprefix', $name_cosial_prefix);

                 $is_not_provide_email = 1;


                 $htmlapipopup = $this->display(__FILE__, 'views/templates/hooks/apipopup.tpl');
                 $htmlapipopup = str_replace("\n", "", $htmlapipopup);
             }

         }

         $smarty->assign($this->name.'htmlapipopup', $htmlapipopup);
         $smarty->assign($this->name.'apipopup', $is_not_provide_email);
         /// if social api not provide email ////
		
		
		#### show popup for twitter customer which not changed email address  #####
		





        // Separate JS and views content in template files
        $login_buttons_footer =  $this->display(__FILE__, 'views/templates/hooks/login_buttons_footer.tpl');
        $login_buttons_footer = str_replace("\n","",$login_buttons_footer);
        $smarty->assign($this->name.'lbfooter', $login_buttons_footer);

        $login_buttons_top =  $this->display(__FILE__, 'views/templates/hooks/login_buttons_top.tpl');
        $login_buttons_top = str_replace("\n","",$login_buttons_top);
        $smarty->assign($this->name.'lbtop', $login_buttons_top);

        $login_buttons_authpage =  $this->display(__FILE__, 'views/templates/hooks/login_buttons_authpage.tpl');
        $login_buttons_authpage = str_replace("\n","",$login_buttons_authpage);
        $smarty->assign($this->name.'lbauthpage', $login_buttons_authpage);


        $login_buttons_beforeauthpage =  $this->display(__FILE__, 'views/templates/hooks/login_buttons_beforeauthpage.tpl');
        $login_buttons_beforeauthpage = str_replace("\n","",$login_buttons_beforeauthpage);
        $smarty->assign($this->name.'lbbauthpage', $login_buttons_beforeauthpage);

        $login_buttons_welcome =  $this->display(__FILE__, 'views/templates/hooks/login_buttons_welcome.tpl');
        $login_buttons_welcome = str_replace("\n","",$login_buttons_welcome);
        $smarty->assign($this->name.'lbwelcome', $login_buttons_welcome);





        // Separate JS and views content in template files

		return $this->display(__FILE__, 'views/templates/hooks/head.tpl');
    	
    }
    
    #### show popup for twitter customer which not changed email address  #####
    public function twTranslate(){
    	return array('valid_email' => $this->l('This email address is not valid'),
    				 'exists_customer' => $this->l('An account using this email address has already been registered.'),
    				 'send_email' => $this->l('Password has been sent to your mailbox:'),
    				 'log_in' => $this->l('You must be log in.'),



                    'api_one_1'=>$this->l('You have linked your Account to your'),
                    'api_one_2'=>$this->l('profile'),
                    'api_two1'=>$this->l('Because'),
                    'api_two2'=>$this->l('does not give us your e-mail address, your account was created with a false generic e-mail. Please update your e-mail address now by filling it out below.'),
    			
    				);
    }
	#### show popup for twitter customer which not changed email address  #####
	
public  function hookLeftColumn($params)
	{
		$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;
		
		
		
		
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
		
		
		
		$this->settings($params);


    	
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
		
		
		$this->settings($params);
    	
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			return $this->display(__FILE__, 'views/templates/hooks/right15.tpl');
		} else {
			return $this->display(__FILE__, 'views/templates/hooks/right.tpl');
		}		
	}

    public function hookCustomerAccount($params)
    {
        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;
        $smarty->assign($this->name.'is16', $this->_is16);

        $smarty->assign($this->name.'is_ps15', $this->_is15);



        $is_logged = isset($cookie->id_customer)?$cookie->id_customer:0;
        $smarty->assign($this->name.'islogged', $is_logged);


        $id_lang = (int)($cookie->id_lang);
        $data_seo_url = $this->getSEOURLs(array('id_lang'=>$id_lang));

        $account_url = $data_seo_url['account_url'];
        $smarty->assign($this->name.'account_url', $account_url);

        $smarty->assign($this->name.'is_soc_link', Configuration::get($this->name.'is_soc_link'));

        if($is_logged)
            return $this->display(__FILE__, 'views/templates/hooks/my-account.tpl');
    }

    public function hookMyAccountBlock($params)
    {
        $smarty = $this->context->smarty;
        $cookie = $this->context->cookie;
        $smarty->assign($this->name.'is16', $this->_is16);

        $smarty->assign($this->name.'is_ps15', $this->_is15);


        $is_logged = isset($cookie->id_customer)?$cookie->id_customer:0;
        $smarty->assign($this->name.'islogged', $is_logged);


        $id_lang = (int)($cookie->id_lang);
        $data_seo_url = $this->getSEOURLs(array('id_lang'=>$id_lang));

        $account_url = $data_seo_url['account_url'];

        $smarty->assign($this->name.'account_url', $account_url);

        $smarty->assign($this->name.'is_soc_link', Configuration::get($this->name.'is_soc_link'));

        if($is_logged)
            return $this->display(__FILE__, 'views/templates/hooks/my-account-block.tpl');
    }
	

	public function settings($params = null){
		$smarty = $this->context->smarty;
		$cookie = $this->context->cookie;

        $is_logged = isset($cookie->id_customer)?1:0;
        $smarty->assign($this->name.'islogged', $is_logged);

        $smarty->assign($this->name.'is_instagram', $this->_is_instagram);
        $smarty->assign($this->name.'is_odnoklassniki', $this->_is_odnoklassniki);
        $smarty->assign($this->name.'is_mailru', $this->_is_mailru);
        $smarty->assign($this->name.'is_yandex', $this->_is_yandex);
		
		
		$data_fb = $this->getfacebooklib((int)$cookie->id_lang);
		$smarty->assign($this->name.'lang', $data_fb['lng_iso']);
		
		$is_ps5 = 0;
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$is_ps5 = 1;
		}
		$smarty->assign($this->name.'is_ps5', $is_ps5);
		$smarty->assign($this->name.'is15', $is_ps5);
		 

		
		
		include_once(_PS_MODULE_DIR_.$this->name.'/classes/facebookhelp.class.php');
		$obj = new facebookhelp();
		$data_img = $obj->getImages();
			
		####### images and positions ####
		$data_connects_array_prefix = $this->getConnetsArrayPrefix();

        $smarty->assign($this->name.'allcon', $data_connects_array_prefix);


		foreach($data_connects_array_prefix as $prefix_short => $_data_item){

        $prefix_full =  $_data_item['prefix'];
		$facebookimg = $data_img[$prefix_full];
		$facebooksmallimg = $data_img[$prefix_full.'small'];
		$facebookimglarge_small = $data_img[$prefix_full.'large_small'];
		$facebookimgmicro_small = $data_img[$prefix_full.'micro_small'];
			
		$array_f_head = array("top","footer","beforeauthpage","authpage","welcome","leftcolumn","rightcolumn","chook");
			
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

            case 'bl'.$prefix_hook.$prefix_short:
                $facebook_img = 1;
            break;
            case 'bls'.$prefix_hook.$prefix_short:
                $facebook_img = 2;
            break;
            case 'bs'.$prefix_hook.$prefix_short:
                $facebook_img = 3;
                break;
            case 'bsm'.$prefix_hook.$prefix_short:
                $facebook_img = 4;
            break;

			default:
				$facebook_img = $facebooksmallimg;
			break;
		}
		$smarty->assign($this->name.$prefix_short.$prefix_hook.'img', $facebook_img);
		}
			
			
			
		$smarty->assign($this->name.$prefix_short.'_on', Configuration::get($this->name.$prefix_short.'_on'));
		
		$smarty->assign($this->name.'_top'.$prefix_short, Configuration::get($this->name.'_top'.$prefix_short));
		
		$smarty->assign($this->name.'_chook'.$prefix_short, Configuration::get($this->name.'_chook'.$prefix_short));
			
		$smarty->assign($this->name.'_footer'.$prefix_short, Configuration::get($this->name.'_footer'.$prefix_short));
		$smarty->assign($this->name.'_authpage'.$prefix_short, Configuration::get($this->name.'_authpage'.$prefix_short));
            $smarty->assign($this->name.'_beforeauthpage'.$prefix_short, Configuration::get($this->name.'_beforeauthpage'.$prefix_short));
		$smarty->assign($this->name.'_welcome'.$prefix_short, Configuration::get($this->name.'_welcome'.$prefix_short));
		
		
		$smarty->assign($this->name.'_leftcolumn'.$prefix_short, Configuration::get($this->name.'_leftcolumn'.$prefix_short));
		
		$smarty->assign($this->name.'_rightcolumn'.$prefix_short, Configuration::get($this->name.'_rightcolumn'.$prefix_short));
		}
		####### images and positions ####
		
		
		
		### set variables for order page ####
		$order_variables = $this->getOrderPage();
		### set variables for order page ####
		
		
		 
		// facebook connect
		
		$appid = Configuration::get($this->name.'appid');
		$secret = Configuration::get($this->name.'secret');
		
		if(Tools::strlen($appid)>0 && Tools::strlen($secret)>0){
		$smarty->assign($this->name.'fconf', 1);
		} else {
		$smarty->assign($this->name.'fconf', 0);
		}
		
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
			$smarty->assign($this->name.'ssltxt', $this->l('Note : SSL has not enabled on this server'));
			$smarty->assign($this->name.'is_ssl',0);
		} else {
			$smarty->assign($this->name.'is_ssl',1);
            $smarty->assign($this->name.'ssltxt', '');
		}


		// amazon connect
		
		
		// dropbox connect
		$dbci = Configuration::get($this->name.'dbci');
		$dbcs = Configuration::get($this->name.'dbcs');
		
		if(Tools::strlen($dbci)>0 && Tools::strlen($dbcs)>0){
			$smarty->assign($this->name.'dbconf', 1);
		} else {
			$smarty->assign($this->name.'dbconf', 0);
		}
		
		//$whitelist = array( '127.0.0.1', '::1' );
		if (Configuration::get('PS_SSL_ENABLED') == 0 
			//&& !in_array( $_SERVER['REMOTE_ADDR'], $whitelist)
            )
		{
			$smarty->assign($this->name.'ssltxtdb', $this->l('Note : SSL has not enabled on this server'));
			$smarty->assign($this->name.'is_ssldb',0);

		} else {
			$smarty->assign($this->name.'is_ssldb',1);
		}
		
		// dropbox connect
		
		
		// scoop connect
		$sci = Configuration::get($this->name.'sci');
		$scs = Configuration::get($this->name.'scs');
		
		if(Tools::strlen($sci)>0 && Tools::strlen($scs)>0){
			$smarty->assign($this->name.'sconf', 1);
		} else {
			$smarty->assign($this->name.'sconf', 0);
		}
		
		// scoop connect
		
		
		
		// wordpress connect
		$wci = Configuration::get($this->name.'wci');
		$wcs = Configuration::get($this->name.'wcs');
		
		if(Tools::strlen($wci)>0 && Tools::strlen($wcs)>0){
			$smarty->assign($this->name.'wconf', 1);
		} else {
			$smarty->assign($this->name.'wconf', 0);
		}
		
		// wordpress connect
		
		
		// tumblr connect
		$tuci = Configuration::get($this->name.'tuci');
		$tucs = Configuration::get($this->name.'tucs');
		
		if(Tools::strlen($tuci)>0 && Tools::strlen($tucs)>0){
			$smarty->assign($this->name.'tuconf', 1);
		} else {
			$smarty->assign($this->name.'tuconf', 0);
		}
		
		// tumblr connect
		
		
		// pinterest connect
		$pici = Configuration::get($this->name.'pici');
		$pics = Configuration::get($this->name.'pics');
		
		if(Tools::strlen($pici)>0 && Tools::strlen($pics)>0){
			$smarty->assign($this->name.'piconf', 1);
		} else {
			$smarty->assign($this->name.'piconf', 0);
		}
		
		if (Configuration::get('PS_SSL_ENABLED') == 0)
		{
			$smarty->assign($this->name.'ssltxtpi', $this->l('Note : SSL has not enabled on this server'));
			$smarty->assign($this->name.'is_sslpi',0);
		} else {
			$smarty->assign($this->name.'is_sslpi',1);
		}
		
		// pinterest connect
		
		
		// oklass connect
		$odci = Configuration::get($this->name.'odci');
		$odcs = Configuration::get($this->name.'odcs');
		$odpc = Configuration::get($this->name.'odpc');
		
		
		
		if(Tools::strlen($odci)>0 && Tools::strlen($odcs)>0 && Tools::strlen($odpc)>0){
			$smarty->assign($this->name.'oconf', 1);
		} else {
			$smarty->assign($this->name.'oconf', 0);
		}
		
		// oklass connect
		
		
		
		// mailru connect
		$maci = Configuration::get($this->name.'maci');
		$macs = Configuration::get($this->name.'macs');
		
		if(Tools::strlen($maci)>0 && Tools::strlen($macs)>0){
			$smarty->assign($this->name.'maconf', 1);
		} else {
			$smarty->assign($this->name.'maconf', 0);
		}
		
		// mailru connect
		
		
		// yandex connect
		$yaci = Configuration::get($this->name.'yaci');
		$yacs = Configuration::get($this->name.'yacs');
		
		if(Tools::strlen($yaci)>0 && Tools::strlen($yacs)>0){
			$smarty->assign($this->name.'yaconf', 1);
		} else {
			$smarty->assign($this->name.'yaconf', 0);
		}
		
		// yandex connect


        // vkontakte connect
        $vci = Configuration::get($this->name.'vci');
        $vcs = Configuration::get($this->name.'vcs');

        if(Tools::strlen($vci)>0 && Tools::strlen($vcs)>0){
            $smarty->assign($this->name.'vconf', 1);
        } else {
            $smarty->assign($this->name.'vconf', 0);
        }
        // vkontakte connect
		 
		$smarty->assign($this->name.'http_referer', $this->_http_referer);
		 
		$smarty->assign($this->name.'is16', $this->_is16);


		 
		$data_errors = $this->getConnetsArrayPrefix();

		$smarty->assign('gerror', $data_errors['g']['error']);
		$smarty->assign('ferror', $data_errors['f']['error']);
		$smarty->assign('terror', $data_errors['t']['error']);
		$smarty->assign('lerror', $data_errors['l']['error']);
		$smarty->assign('merror', $data_errors['m']['error']);
        if($this->_is_instagram)
		$smarty->assign('ierror', $data_errors['i']['error']);
		$smarty->assign('fserror', $data_errors['fs']['error']);
		$smarty->assign('gierror', $data_errors['gi']['error']);
		$smarty->assign('derror', $data_errors['d']['error']);
		$smarty->assign('aerror', $data_errors['a']['error']);
		$smarty->assign('dberror', $data_errors['db']['error']);
		//$smarty->assign('serror', $data_errors['s']['error']);
		$smarty->assign('werror', $data_errors['w']['error']);
		$smarty->assign('tuerror', $data_errors['tu']['error']);
		$smarty->assign('pierror', $data_errors['pi']['error']);
        if($this->_is_odnoklassniki)
		$smarty->assign('oerror', $data_errors['o']['error']);
        if($this->_is_mailru)
		$smarty->assign('maerror', $data_errors['ma']['error']);
        if($this->_is_yandex)
		$smarty->assign('yaerror', $data_errors['ya']['error']);
        $smarty->assign('perror', $data_errors['p']['error']);
        $smarty->assign('verror', $data_errors['v']['error']);
		
		
		### redirect URL ###
		foreach($this->getConnetsArrayPrefix() as $k_pref => $_data_item){

            $val_pref= $_data_item['prefix'];
		
			$redirect_url = $this->getRedirectURL(array('typelogin'=>$val_pref));
				
			$prefix_uri = '?';
			if(version_compare(_PS_VERSION_, '1.6', '>')){
				$prefix_uri = "&";
			}
				
			$is_order_page = $order_variables['order_page'];
			if($is_order_page == 1){
		
		
		
				if($val_pref == 'google' || $val_pref == 'yahoo'){
					$redirect_url = $redirect_url.$prefix_uri.'http_referer='.urlencode($order_variables['http_referrer']).'&p='.$val_pref;
				} else {
					$redirect_url = $redirect_url.$prefix_uri.'http_referer='.urlencode($order_variables['http_referrer']);
				}
			} else {
				if($val_pref == 'google' || $val_pref == 'yahoo'){
					$redirect_url = $redirect_url.$prefix_uri.'p='.$val_pref;
				}
			}
			$smarty->assign($this->name.'redurl'.$k_pref,$redirect_url);
		}
		
		### redirect URL ###

        $id_lang = (int)$cookie->id_lang;
        $data_seo_url = $this->getSEOURLs(array('id_lang'=>$id_lang));

        $account_url = $data_seo_url['account_url'];
        $my_account = $data_seo_url['my_account'];
        $delete_url = $data_seo_url['delete_url'];
        $update_social_api_email = $data_seo_url['update_social_api_email'];
        $amazon_url = $data_seo_url['amazon_url'];

        $smarty->assign($this->name.'account_url', $account_url);
        $smarty->assign($this->name.'my_account', $my_account);
        $smarty->assign($this->name.'delete_url', $delete_url);
        $smarty->assign($this->name.'update_email', $update_social_api_email);
        $smarty->assign($this->name.'amazon_url', $amazon_url);
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
			$locales[] = @current($locale);
		}
			
		return $locales;
	}
    
	
	
	
	
	
	
	public function getContent()
    {
    	$cookie = $this->context->cookie;
		$currentIndex = $this->context->currentindex;
    	
    	$this->_html = '';
    	
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$this->addBackOfficeMedia();
    	} else {
    		$this->_html .= $this->_headercssfiles();
    	}
    	
    	include_once(_PS_MODULE_DIR_.$this->name.'/classes/facebookhelp.class.php');
		$obj = new facebookhelp();

        $submitbasicsettings = Tools::getValue("submitbasicsettings");
        if (Tools::strlen($submitbasicsettings)>0) {
            $this->_html .= '<script>init_tabs(99);</script>';
        }

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

            Configuration::updateValue($this->name.'_rcblock', Tools::getValue('rcblock'));
            Configuration::updateValue($this->name.'_lcblock', Tools::getValue('lcblock'));
        	
        	Configuration::updateValue($this->name.'iauth', Tools::getValue('iauth'));

            Configuration::updateValue($this->name.'is_soc_link', Tools::getValue('is_soc_link'));
        	
        	Configuration::updateValue('redirpage', Tools::getValue('redir'));
        	
        	$languages = Language::getLanguages(false);
        	foreach ($languages as $language){
        		$i = $language['id_lang'];
        		Configuration::updateValue($this->name.'txtauthp_'.$i, Tools::getValue('txtauthp_'.$i));
        	}

            $url = $currentIndex.'&conf=6&tab=AdminModules&submitbasicsettings=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
            Tools::redirectAdmin($url);


		}


        $submitenabledisablesettings = Tools::getValue("submitenabledisablesettings");
        if (Tools::strlen($submitenabledisablesettings)>0) {
            $this->_html .= '<script>init_tabs(98);</script>';
        }
        if (Tools::isSubmit('submitenabledisable'))
        {
            Configuration::updateValue($this->name.'f_on', Tools::getValue('ff_on'));
            Configuration::updateValue($this->name.'t_on', Tools::getValue('tt_on'));
            Configuration::updateValue($this->name.'a_on', Tools::getValue('aa_on'));
            Configuration::updateValue($this->name.'g_on', Tools::getValue('gg_on'));
            Configuration::updateValue($this->name.'y_on', Tools::getValue('yy_on'));
            Configuration::updateValue($this->name.'p_on', Tools::getValue('pp_on'));
            Configuration::updateValue($this->name.'l_on', Tools::getValue('ll_on'));
            Configuration::updateValue($this->name.'m_on', Tools::getValue('mm_on'));
            if($this->_is_instagram) {
                Configuration::updateValue($this->name.'i_on', Tools::getValue('ii_on'));
            }
            Configuration::updateValue($this->name.'fs_on', Tools::getValue('fsfs_on'));
            Configuration::updateValue($this->name.'gi_on', Tools::getValue('gigi_on'));
            Configuration::updateValue($this->name.'d_on', Tools::getValue('dd_on'));
            Configuration::updateValue($this->name.'db_on', Tools::getValue('dbdb_on'));
            Configuration::updateValue($this->name.'w_on', Tools::getValue('ww_on'));
            Configuration::updateValue($this->name.'tu_on', Tools::getValue('tutu_on'));
            Configuration::updateValue($this->name.'pi_on', Tools::getValue('pipi_on'));
            Configuration::updateValue($this->name.'v_on', Tools::getValue('vv_on'));

            $url = $currentIndex.'&conf=6&tab=AdminModules&submitenabledisablesettings=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
            Tools::redirectAdmin($url);
        }


        $data_prefixes = $this->getConnetsArrayPrefix();

        //echo "<pre>"; var_dump($data_prefixes);exit;

        foreach($data_prefixes as $prefix_short => $data_prefix_item){

            $type_prefix_item = $data_prefix_item['type'];
            $prefix_long = $data_prefix_item['prefix'];

            $key_item_api = $data_prefix_item['key'];
            $secret_item_api = $data_prefix_item['secret'];
            $redirect_url_item_api = $data_prefix_item['redirect_url'];

            $submit_all_settings = Tools::getValue("submit".$prefix_short."settings");
            if (Tools::strlen($submit_all_settings)>0) {
                $this->_html .= '<script>init_tabs('.$type_prefix_item.');</script>';
            }


            if (Tools::isSubmit('submit'.$prefix_short))
            {


                if($key_item_api){
                    Configuration::updateValue($this->name.$key_item_api, Tools::getValue($key_item_api));
                }

                if($secret_item_api){
                    Configuration::updateValue($this->name.$secret_item_api, Tools::getValue($secret_item_api));
                }

                if($redirect_url_item_api){
                    Configuration::updateValue($this->name.$redirect_url_item_api, Tools::getValue($redirect_url_item_api));
                }



                //  connect
                Configuration::updateValue($this->name.$prefix_short.'_on', Tools::getValue($prefix_short.'_on'));

                Configuration::updateValue($this->name.'_top'.$prefix_short, Tools::getValue('top'.$prefix_short));
                Configuration::updateValue($this->name.'_rightcolumn'.$prefix_short, Tools::getValue('rightcolumn'.$prefix_short));
                Configuration::updateValue($this->name.'_leftcolumn'.$prefix_short, Tools::getValue('leftcolumn'.$prefix_short));
                Configuration::updateValue($this->name.'_footer'.$prefix_short, Tools::getValue('footer'.$prefix_short));
                Configuration::updateValue($this->name.'_beforeauthpage'.$prefix_short, Tools::getValue('beforeauthpage'.$prefix_short));
                Configuration::updateValue($this->name.'_authpage'.$prefix_short, Tools::getValue('authpage'.$prefix_short));
                Configuration::updateValue($this->name.'_welcome'.$prefix_short, Tools::getValue('welcome'.$prefix_short));
                Configuration::updateValue($this->name.'_chook'.$prefix_short, Tools::getValue('chook'.$prefix_short));

                Configuration::updateValue($this->name.'szchook'.$prefix_short, Tools::getValue('szchook'.$prefix_short));
                Configuration::updateValue($this->name.'sztop'.$prefix_short, Tools::getValue('sztop'.$prefix_short));
                Configuration::updateValue($this->name.'szrightcolumn'.$prefix_short, Tools::getValue('szrightcolumn'.$prefix_short));
                Configuration::updateValue($this->name.'szleftcolumn'.$prefix_short, Tools::getValue('szleftcolumn'.$prefix_short));
                Configuration::updateValue($this->name.'szfooter'.$prefix_short, Tools::getValue('szfooter'.$prefix_short));
                Configuration::updateValue($this->name.'szbeforeauthpage'.$prefix_short, Tools::getValue('szbeforeauthpage'.$prefix_short));
                Configuration::updateValue($this->name.'szauthpage'.$prefix_short, Tools::getValue('szauthpage'.$prefix_short));
                Configuration::updateValue($this->name.'szwelcome'.$prefix_short, Tools::getValue('szwelcome'.$prefix_short));

                // save connect image
                if(!empty($_FILES['post_image_'.$prefix_long]['name'])){
                    $obj->saveImage(array('type'=>$prefix_long));
                }

                // save connect small image
                if(!empty($_FILES['post_image_'.$prefix_long.'small']['name'])){
                    $obj->saveImage(array('type'=>$prefix_long.'small'));
                }

                // save  connect large_small image
                if(!empty($_FILES['post_image_'.$prefix_long.'large_small']['name'])){
                    $obj->saveImage(array('type'=>$prefix_long.'large_small'));
                }

                // save connect micro_small image
                if(!empty($_FILES['post_image_'.$prefix_long.'micro_small']['name'])){
                    $obj->saveImage(array('type'=>$prefix_long.'micro_small'));
                }

                $url = $currentIndex.'&conf=6&tab=AdminModules&submit'.$prefix_short.'settings=1&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
                Tools::redirectAdmin($url);
                //  connect
            }

        }





        if(Tools::isSubmit('cancel_search')){
        	$url = $currentIndex.'&tab=AdminModules&pageitems&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)(Tab::getIdFromClassName('AdminModules')).(int)($cookie->id_employee)).'';
        	Tools::redirectAdmin($url);
       
        }
        if (Tools::isSubmit('pageitems') || Tools::isSubmit('find') || Tools::isSubmit('search_query')) {
     		$this->_html .= '<script>init_tabs(10);</script>';
        }
    	
        if(version_compare(_PS_VERSION_, '1.6', '>')){
        	$this->_html .= $this->_displayForm16();
        } else {
        	$this->_html .= $this->_displayForm13_14_15();
        }
        
        
        
        return $this->_html;
    }
    
    
    /*
     * Add css and javascript
    */
    
    protected function addBackOfficeMedia()
    {
    	//CSS files
    	//$this->context->controller->addCSS($this->_path.'views/css/font-custom.min.css');

        $this->context->controller->addCSS(($this->_path).'views/css/font-awesome.min.css', 'all');
        $this->context->controller->addCSS(($this->_path).'views/css/'.$this->name.'.css', 'all');

    	$this->context->controller->addCSS($this->_path.'views/css/admin.css');
    	
    	// JS files
    	$this->context->controller->addJs($this->_path.'views/js/javascript.js');
    	$this->context->controller->addJs($this->_path.'views/js/menu16.js');
    	
    	
    }
    
    private function _displayForm16(){
    	$_html = '';
    	
    	$_html .= '<div class="row">
    				<div class="col-lg-12">
    					<div class="row">';
    	
    	$_html .= '<div class="productTabs col-lg-2 col-md-3">
			
			<div class="list-group">
				<ul class="nav nav-pills nav-stacked" id="navtabs16">
				    <li class="active"><a href="#welcome" data-toggle="tab" class="list-group-item"><i class="fa fa-home fa-lg"></i>&nbsp;'.$this->l('Welcome').'</a></li>
				    
				    <li><a href="#basicsettings" data-toggle="tab" class="list-group-item"><i class="fa fa-cogs fa-lg"></i>&nbsp;'.$this->l('Basic Settings').'</a></li>
				    <li><a href="#enabledisalbe" data-toggle="tab" class="list-group-item"><i class="fa fa-cogs fa-lg"></i>&nbsp;'.$this->l('Enable/Disable Connects').'</a></li>
				    <li><a href="#facebook" data-toggle="tab" class="list-group-item"><i class="fa fa-facebook fa-lg"></i>&nbsp;'.$this->l('Facebook').'</a></li>
				    <li><a href="#twitter" data-toggle="tab" class="list-group-item"><i class="fa fa-twitter fa-lg"></i>&nbsp;'.$this->l('Twitter').'</a></li>
				    <li><a href="#amazon" data-toggle="tab" class="list-group-item"><i class="fa fa-at fa-lg"></i>&nbsp;'.$this->l('Amazon').'</a></li>
				    <li><a href="#google" data-toggle="tab" class="list-group-item"><i class="fa fa-google fa-lg"></i>&nbsp;'.$this->l('Google').'</a></li>
				    <li><a href="#yahoo" data-toggle="tab" class="list-group-item"><i class="fa fa-yahoo fa-lg"></i>&nbsp;'.$this->l('Yahoo').'</a></li>

				    <li><a href="#paypal" data-toggle="tab" class="list-group-item"><i class="fa fa-paypal fa-lg"></i>&nbsp;'.$this->l('Paypal').'</a></li>

				    <li><a href="#linkedin" data-toggle="tab" class="list-group-item"><i class="fa fa-linkedin fa-lg"></i>&nbsp;'.$this->l('Linkedin').'</a></li>
				    <li><a href="#hotmail" data-toggle="tab" class="list-group-item"><i class="fa fa-windows fa-lg"></i>&nbsp;'.$this->l('Live/Hotmail').'</a></li>';

                if($this->_is_instagram) {
                    $_html .= '<li><a href="#instagram" data-toggle="tab" class="list-group-item"><i class="fa fa-instagram fa-lg"></i>&nbsp;' . $this->l('Instagram') . '</a></li>';
                }

				    $_html .= '<li><a href="#foursquare" data-toggle="tab" class="list-group-item"><i class="fa fa-foursquare fa-lg"></i>&nbsp;'.$this->l('Foursquare').'</a></li>
				    <li><a href="#github" data-toggle="tab" class="list-group-item"><i class="fa fa-github fa-lg"></i>&nbsp;'.$this->l('Github').'</a></li>
				    <li><a href="#disqus" data-toggle="tab" class="list-group-item custom-social-button-5"><i class="fa fa-disqus fa-lg"></i>&nbsp;'.$this->l('Disqus').'</a></li>
				    <li><a href="#dropbox" data-toggle="tab" class="list-group-item"><i class="fa fa-dropbox fa-lg"></i>&nbsp;'.$this->l('Dropbox').'</a></li>';
    	
				    //$_html .= '<li><a href="#scoop" data-toggle="tab" class="list-group-item"><img src="../modules/'.$this->name.'/views/img/settings_s.png"  />&nbsp;'.$this->l('Scoop.it').'</a></li>';
				    
				    $_html .= '<li><a href="#wordpress" data-toggle="tab" class="list-group-item"><i class="fa fa-wordpress fa-lg"></i>&nbsp;'.$this->l('Wordpress').'</a></li>
				    <li><a href="#tumblr" data-toggle="tab" class="list-group-item"><i class="fa fa-tumblr fa-lg"></i>&nbsp;'.$this->l('Tumblr').'</a></li>
				    <li><a href="#pinterest" data-toggle="tab" class="list-group-item"><i class="fa fa-pinterest fa-lg"></i>&nbsp;'.$this->l('Pinterest').'</a></li>';
        if($this->_is_odnoklassniki)
            $_html .= '<li><a href="#oklass" data-toggle="tab" class="list-group-item"><i class="fa fa-odnoklassniki fa-lg"></i>&nbsp;'.$this->l('Odnoklassniki').'</a></li>';

        if($this->_is_mailru)
            $_html .= '<li><a href="#mailru" data-toggle="tab" class="list-group-item"><i class="fa fa-mailru fa-lg"></i>&nbsp;'.$this->l('Mail.ru').'</a></li>';

        if($this->_is_yandex)
            $_html .= '<li><a href="#yandex" data-toggle="tab" class="list-group-item"><i class="fa fa-yandex fa-lg"></i>&nbsp;'.$this->l('Yandex').'</a></li>';


        $_html .= '<li><a href="#vkontakte" data-toggle="tab" class="list-group-item"><i class="fa fa-vk fa-lg"></i>&nbsp;'.$this->l('Vkontakte').'</a></li>';
				   
				   $_html .= '<li>&nbsp;</li>
					<li><a href="#faqspm" data-toggle="tab" class="list-group-item"><i class="fa fa-question-circle fa-lg"></i>&nbsp;'.$this->l('FAQ').'</a></li>
				    <li><a href="http://addons.prestashop.com/en/2_community-developer?contributor=61669" target="_blank"  class="list-group-item"><img src="../modules/'.$this->name.'/views/img/spm-logo.png"  />&nbsp;&nbsp;'.$this->l('Other SPM Modules').'</a></li>
			
				  </ul>
				  </div>
		</div>';
    	
    	$_html .= '<div class="tab-content col-lg-10 col-md-9">
			    	<div class="tab-pane active" id="welcome">'.$this->_welcome16().'</div>
			    	<div class="tab-pane" id="basicsettings">'.$this->_basicsettings16().'</div>
			    	<div class="tab-pane" id="enabledisalbe">'.
                    $this->_enabledisable16(
                        array(
                            array('prefix'=>'f','prefix_full'=>'facebook','title_item'=>$this->l('Facebook'),),
                            array('prefix'=>'t','prefix_full'=>'twitter','title_item'=>$this->l('Twitter'),),
                            array('prefix'=>'a','prefix_full'=>'amazon','title_item'=>$this->l('Amazon'),),
                            array('prefix'=>'g','prefix_full'=>'google','title_item'=>$this->l('Google'),),
                            array('prefix'=>'y','prefix_full'=>'yahoo','title_item'=>$this->l('Yahoo'),),
                            array('prefix'=>'p','prefix_full'=>'paypal','title_item'=>$this->l('Paypal'),),
                            array('prefix'=>'l','prefix_full'=>'linkedin','title_item'=>$this->l('Linkedin'),),
                            array('prefix'=>'m','prefix_full'=>'microsoft','title_item'=>$this->l('Microsoft'),),
                            //array('prefix' => 'i', 'prefix_full' => 'instagram', 'title_item' => $this->l('Instagram'),),
                            array('prefix'=>'fs','prefix_full'=>'foursquare','title_item'=>$this->l('Foursquare'),),
                            array('prefix'=>'gi','prefix_full'=>'github','title_item'=>$this->l('Github'),),
                            array('prefix'=>'d','prefix_full'=>'disqus','title_item'=>$this->l('Disqus'),),
                            array('prefix'=>'db','prefix_full'=>'dropbox','title_item'=>$this->l('Dropbox'),),
                            array('prefix'=>'w','prefix_full'=>'wordpress','title_item'=>$this->l('Wordpress'),),
                            array('prefix'=>'tu','prefix_full'=>'tumblr','title_item'=>$this->l('Tumblr'),),
                            array('prefix'=>'pi','prefix_full'=>'pinterest','title_item'=>$this->l('Pinterest'),),
                            array('prefix'=>'v','prefix_full'=>'vkontakte','title_item'=>$this->l('Vkontakte'),),

                        )
                    )
                    .'</div>
			    	<div class="tab-pane" id="facebook">'.
			    	$this->_connectSettings(array('prefix'=>'f',
			    								  'prefix_full'=>'facebook',
			    								  'title_item'=>$this->l('Facebook'),
			    								  'settings' => array('appid' => array( Tools::getValue($this->name.'appid', Configuration::get($this->name.'appid')), $this->l('Application Id') ),
			    								  					  'secret' => array(Tools::getValue($this->name.'secret', Configuration::get($this->name.'secret')),$this->l('Secret Key')  )
			    								  					 )
			    								 )
			    								 
			    							)
			    							.'
			    	</div>
			    	<div class="tab-pane" id="twitter">'.
			    	$this->_connectSettings(array('prefix'=>'t',
			    								  'prefix_full'=>'twitter',
			    								  'title_item'=>$this->l('Twitter'),
			    								  'settings' => array('twitterconskey' => array( Tools::getValue($this->name.'twitterconskey', Configuration::get($this->name.'twitterconskey')), $this->l('Consumer key') ),
			    								  					  'twitterconssecret' => array(Tools::getValue($this->name.'twitterconssecret', Configuration::get($this->name.'twitterconssecret')),$this->l('Consumer secret')  )
			    								  					 )
			    								 )
			    								 
			    							)
			    							.'
			    	</div>
			    	<div class="tab-pane" id="amazon">'.
			    	$this->_connectSettings(array('prefix'=>'a',
			    								  'prefix_full'=>'amazon',
			    								  'title_item'=>$this->l('Amazon'),
			    								  'settings' => array('aci' => array( Tools::getValue($this->name.'aci', Configuration::get($this->name.'aci')), $this->l('Client ID') ),
			    								  					  'aru' => array(Tools::getValue($this->name.'aru', Configuration::get($this->name.'aru')),$this->l('Allowed Return URL')  )
			    								  					 ),
			    								  'tip' => $this->l('Note: To enable Amazon Connect, Please make sure that "SSL" has enabled on your server'),
			    								  
			    								 )
			    								 
			    							)
			    							.'
			    	</div>
			    	<div class="tab-pane" id="google">'.
			    		$this->_connectSettings(array('prefix'=>'g','prefix_full'=>'google','title_item'=>$this->l('Google'),
			    										'settings' => array('oci' => array(Tools::getValue($this->name.'oci', Configuration::get($this->name.'oci')), $this->l('Client Id') ),
			    								  					  		'ocs' => array( Tools::getValue($this->name.'ocs', Configuration::get($this->name.'ocs')), $this->l('Client Secret') ),
			    															'oru' => array( Tools::getValue($this->name.'oru', Configuration::get($this->name.'oru')), $this->l('Callback URL') )
			    								  					 )
			    								 )
			    				)
			    		.'
			    	</div>
			    	<div class="tab-pane" id="yahoo">'.
			    		$this->_connectSettings(array('prefix'=>'y','prefix_full'=>'yahoo','title_item'=>$this->l('Yahoo'),
			    									 )
			    				)
			    		.'
			    	</div>
			    	<div class="tab-pane" id="paypal">'.
            $this->_connectSettings(array('prefix'=>'p','prefix_full'=>'paypal','title_item'=>$this->l('Paypal'),
                    'settings' => array('clientid' => array(Tools::getValue($this->name.'clientid', Configuration::get($this->name.'clientid')), $this->l('Client Id') ),
                        'psecret' => array( Tools::getValue($this->name.'psecret', Configuration::get($this->name.'psecret')), $this->l('Secret') ),
                        'pcallback' => array( Tools::getValue($this->name.'pcallback', Configuration::get($this->name.'pcallback')), $this->l('Callback URL') )
                    )
                )
            )
            .'
			    	</div>
			    	<div class="tab-pane" id="linkedin">'.
			    		$this->_connectSettings(array('prefix'=>'l','prefix_full'=>'linkedin','title_item'=>$this->l('Linkedin'),
			    										'settings' => array('lapikey' => array(Tools::getValue($this->name.'lapikey', Configuration::get($this->name.'lapikey')), $this->l('API Key') ),
			    								  					  		'lsecret' => array( Tools::getValue($this->name.'lsecret', Configuration::get($this->name.'lsecret')), $this->l('Secret Key') ),
			    								  					 )
			    								 )
			    				)
			    		.'
			    	</div>
			    	<div class="tab-pane" id="hotmail">'.
			    		$this->_connectSettings(array('prefix'=>'m','prefix_full'=>'microsoft','title_item'=>$this->l('Microsoft'),
			    										'settings' => array('mclientid' => array(Tools::getValue($this->name.'mclientid', Configuration::get($this->name.'mclientid')), $this->l('Client ID') ),
			    								  					  		'mclientsecret' => array( Tools::getValue($this->name.'mclientsecret', Configuration::get($this->name.'mclientsecret')), $this->l('Client Secret') ),
			    								  					 )
			    								 )
			    				)
			    		.'
			    	</div>';
        if($this->_is_instagram) {
            $_html .= '<div class="tab-pane" id="instagram">' .
                $this->_connectSettings(array('prefix' => 'i', 'prefix_full' => 'instagram', 'title_item' => $this->l('Instagram'),
                        'settings' => array('ici' => array(Tools::getValue($this->name . 'ici', Configuration::get($this->name . 'ici')), $this->l('Client Id')),
                            'ics' => array(Tools::getValue($this->name . 'ics', Configuration::get($this->name . 'ics')), $this->l('Client Secret')),
                            'iru' => array(Tools::getValue($this->name . 'iru', Configuration::get($this->name . 'iru')), $this->l('Callback URL'))
                        )
                    )
                )
                . '
			    	</div>';
        }
			    	$_html .= '<div class="tab-pane" id="foursquare">'.
			    		$this->_connectSettings(array('prefix'=>'fs','prefix_full'=>'foursquare','title_item'=>$this->l('Foursquare'),
			    										'settings' => array('fsci' => array(Tools::getValue($this->name.'fsci', Configuration::get($this->name.'fsci')), $this->l('Client Id') ),
			    								  					  		'fscs' => array( Tools::getValue($this->name.'fscs', Configuration::get($this->name.'fscs')), $this->l('Client Secret') ),
			    															'fsru' => array( Tools::getValue($this->name.'fsru', Configuration::get($this->name.'fsru')), $this->l('Callback URL') )
			    								  					 )
			    								 )
			    				)
			    		.'
			    	</div>
			    	<div class="tab-pane" id="github">'.
			    		$this->_connectSettings(array('prefix'=>'gi','prefix_full'=>'github','title_item'=>$this->l('Github'),
			    										'settings' => array('gici' => array(Tools::getValue($this->name.'gici', Configuration::get($this->name.'gici')), $this->l('Client Id') ),
			    								  					  		'gics' => array( Tools::getValue($this->name.'gics', Configuration::get($this->name.'gics')), $this->l('Client Secret') ),
			    															'giru' => array( Tools::getValue($this->name.'giru', Configuration::get($this->name.'giru')), $this->l('Callback URL') )
			    								  					 )
			    								 )
			    				)
			    		.'
			    	</div>
			    	
			    	<div class="tab-pane" id="disqus">'.
			    		$this->_connectSettings(array('prefix'=>'d','prefix_full'=>'disqus','title_item'=>$this->l('Disqus'),
			    										'settings' => array('dci' => array(Tools::getValue($this->name.'dci', Configuration::get($this->name.'dci')), $this->l('API Key') ),
			    								  					  		'dcs' => array( Tools::getValue($this->name.'dcs', Configuration::get($this->name.'dcs')), $this->l('API Secret') ),
			    															'dru' => array( Tools::getValue($this->name.'dru', Configuration::get($this->name.'dru')), $this->l('Callback URL') )
			    								  					 )
			    								 )
			    				)
			    		.'
			    	</div>
			    	
			    	
			    	<div class="tab-pane" id="dropbox">'.
			    		$this->_connectSettings(array('prefix'=>'db','prefix_full'=>'dropbox','title_item'=>$this->l('Dropbox'),
			    										'settings' => array('dbci' => array(Tools::getValue($this->name.'dbci', Configuration::get($this->name.'dbci')), $this->l('API Key') ),
			    								  					  		'dbcs' => array( Tools::getValue($this->name.'dbcs', Configuration::get($this->name.'dbcs')), $this->l('API Secret') )
			    															
			    								  					 ),
			    									'tip' => $this->l('Note: To enable Dropbox Connect, Please make sure that "SSL" has enabled on your server'),
			    								 )
			    				)
			    		.'
			    	</div>';
			    	
			    	/* $_html .= '<div class="tab-pane" id="scoop">'.
			    		$this->_connectSettings(array('prefix'=>'s','prefix_full'=>'scoop','title_item'=>$this->l('Scoop.it'),
			    										'settings' => array('sci' => array(Tools::getValue($this->name.'sci', Configuration::get($this->name.'sci')), $this->l('API Key') ),
			    								  					  		'scs' => array( Tools::getValue($this->name.'scs', Configuration::get($this->name.'scs')), $this->l('API Secret') )
			    															
			    								  					 )
			    									
			    								 )
			    				)
			    		.'
			    	</div>'; */
			    	
			    	$_html .= '<div class="tab-pane" id="wordpress">'.
			    		$this->_connectSettings(array('prefix'=>'w','prefix_full'=>'wordpress','title_item'=>$this->l('Wordpress'),
			    										'settings' => array('wci' => array(Tools::getValue($this->name.'wci', Configuration::get($this->name.'wci')), $this->l('Client ID') ),
			    								  					  		'wcs' => array( Tools::getValue($this->name.'wcs', Configuration::get($this->name.'wcs')), $this->l('Client Secret') )
			    															
			    								  					 )
			    									
			    								 )
			    				)
			    		.'
			    	</div>
			    	
			    	
			    	<div class="tab-pane" id="tumblr">'.
			    		$this->_connectSettings(array('prefix'=>'tu','prefix_full'=>'tumblr','title_item'=>$this->l('Tumblr'),
			    										'settings' => array('tuci' => array(Tools::getValue($this->name.'tuci', Configuration::get($this->name.'tuci')), $this->l('Consumer Key') ),
			    								  					  		'tucs' => array( Tools::getValue($this->name.'tucs', Configuration::get($this->name.'tucs')), $this->l('Secret Key') )
			    															
			    								  					 )
			    									
			    								 )
			    				)
			    		.'
			    	</div>
			    	
			    	<div class="tab-pane" id="pinterest">'.
			    		$this->_connectSettings(array('prefix'=>'pi','prefix_full'=>'pinterest','title_item'=>$this->l('Pinterest'),
			    										'settings' => array('pici' => array(Tools::getValue($this->name.'pici', Configuration::get($this->name.'pici')), $this->l('Consumer Key') ),
			    								  					  		'pics' => array( Tools::getValue($this->name.'pics', Configuration::get($this->name.'pics')), $this->l('Secret Key') )
			    															
			    								  					 ),
			    									'tip' => $this->l('Note: To enable Pinterest Connect, Please make sure that "SSL" has enabled on your server'),
			    									
			    								 )
			    				)
			    		.'
			    	</div>';
			    	
			    	if($this->_is_odnoklassniki) {
                        $_html .= '<div class="tab-pane" id="oklass">' .
                            $this->_connectSettings(array('prefix' => 'o', 'prefix_full' => 'oklass', 'title_item' => $this->l('Odnoklassniki'),
                                    'settings' => array('odci' => array(Tools::getValue($this->name . 'odci', Configuration::get($this->name . 'odci')), $this->l('Application ID')),
                                        'odpc' => array(Tools::getValue($this->name . 'odpc', Configuration::get($this->name . 'odpc')), $this->l('Application Public Key')),
                                        'odcs' => array(Tools::getValue($this->name . 'odcs', Configuration::get($this->name . 'odcs')), $this->l('Application Secret Key'))

                                    ),
                                    'tip' => $this->l('Note: To enable Odnoklassniki Connect, Please make sure that "SSL" has enabled on your server'),

                                )
                            )
                            . '
			    	</div>';
                    }
        if($this->_is_mailru) {
            $_html .= '<div class="tab-pane" id="mailru">' .
                $this->_connectSettings(array('prefix' => 'ma', 'prefix_full' => 'mailru', 'title_item' => $this->l('Mail.ru'),
                        'settings' => array('maci' => array(Tools::getValue($this->name . 'maci', Configuration::get($this->name . 'maci')), $this->l('ID')),
                            'macs' => array(Tools::getValue($this->name . 'macs', Configuration::get($this->name . 'macs')), $this->l('Secret Key'))

                        )

                    )
                )
                . '
			    	</div>';
        }

        if($this->_is_yandex) {
            $_html .= '<div class="tab-pane" id="yandex">' .
                $this->_connectSettings(array('prefix' => 'ya', 'prefix_full' => 'yandex', 'title_item' => $this->l('Yandex'),
                        'settings' => array('yaci' => array(Tools::getValue($this->name . 'yaci', Configuration::get($this->name . 'yaci')), $this->l('Client ID')),
                            'yacs' => array(Tools::getValue($this->name . 'yacs', Configuration::get($this->name . 'yacs')), $this->l('Client Secret'))

                        )

                    )
                )
                . '
			    	</div>';
        }

        $_html .= '<div class="tab-pane" id="vkontakte">'.
			    		$this->_connectSettings(array('prefix'=>'v','prefix_full'=>'vkontakte','title_item'=>$this->l('Vkontakte'),
			    										'settings' => array('vci' => array(Configuration::get($this->name.'vci'), $this->l('API Key') ),
			    								  					  		'vcs' => array( Configuration::get($this->name.'vcs'), $this->l('API Secret') )
			    													 )
			    								 )
			    				)
			    		.'
        </div>';

        $_html .= '<div class="tab-pane" id="faqspm">'.$this->_faq16().'</div>
			    	
			    	
    	</div>';
    	
    	
    	
    	$_html .= '</div></div></div>';
    	
    	return $_html;
    }

	

    
    
    
private function _displayForm13_14_15()
     {
     	
     	
     	$_html = '';
     	
     	
     	  
     	$_html .= '
		<fieldset class="display-form">
					<legend><img src="../modules/'.$this->name.'/logo.gif"  />
					'.$this->displayName.':</legend>';
					

     	
     	
     	
		$_html .= '<fieldset class="'.$this->name.'-menu">
			<legend>'.$this->l('Settings').':</legend>
		<ul class="leftMenu">
			<li><a href="javascript:void(0)" onclick="tabs_custom(90)" id="tab-menu-90" class="selected"><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Welcome').'</a></li>
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(99)" id="tab-menu-99"><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Basic Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(98)" id="tab-menu-98"><img src="../modules/'.$this->name.'/logo.gif" />'.$this->l('Enable/Disable Connects').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(1)" id="tab-menu-1"><img src="../modules/'.$this->name.'/views/img/settings_f.png" />'.$this->l('Facebook Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(2)" id="tab-menu-2"><img src="../modules/'.$this->name.'/views/img/settings_t.png"  />'.$this->l('Twitter Settings').'</a></li>
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(24)" id="tab-menu-24"><img src="../modules/'.$this->name.'/views/img/settings_a.png"  />'.$this->l('Amazon Settings').'</a></li>
			
			
			<li><a href="javascript:void(0)" onclick="tabs_custom(3)" id="tab-menu-3"><img src="../modules/'.$this->name.'/views/img/settings_g.png"  />'.$this->l('Google Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(6)" id="tab-menu-6"><img src="../modules/'.$this->name.'/views/img/settings_y.png"  />'.$this->l('Yahoo Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(8)" id="tab-menu-8"><img src="../modules/'.$this->name.'/views/img/settings_p.png"  />'.$this->l('Paypal Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(4)" id="tab-menu-4"><img src="../modules/'.$this->name.'/views/img/settings_l.png"  />'.$this->l('LinkedIn Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(5)" id="tab-menu-5"><img src="../modules/'.$this->name.'/views/img/settings_m.png"  />'.$this->l('Microsoft Settings').'</a></li>';
         if($this->_is_instagram)
		    $_html .= '<li><a href="javascript:void(0)" onclick="tabs_custom(7)" id="tab-menu-7"><img src="../modules/'.$this->name.'/views/img/settings_i.png"  />'.$this->l('Instagram Settings').'</a></li>';
			
			$_html .= '<li><a href="javascript:void(0)" onclick="tabs_custom(20)" id="tab-menu-20"><img src="../modules/'.$this->name.'/views/img/settings_fs.png"  />'.$this->l('Foursquare Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(21)" id="tab-menu-21"><img src="../modules/'.$this->name.'/views/img/settings_gi.png"  />'.$this->l('Github Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(22)" id="tab-menu-22"><img src="../modules/'.$this->name.'/views/img/settings_d.png"  />'.$this->l('Disqus Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(50)" id="tab-menu-50"><img src="../modules/'.$this->name.'/views/img/settings_db.png"  />'.$this->l('Dropbox Settings').'</a></li>';
			//$_html .= '<li><a href="javascript:void(0)" onclick="tabs_custom(51)" id="tab-menu-51"><img src="../modules/'.$this->name.'/views/img/settings_s.png"  />'.$this->l('Scoop.it Settings').'</a></li>';
			$_html .= '<li><a href="javascript:void(0)" onclick="tabs_custom(52)" id="tab-menu-52"><img src="../modules/'.$this->name.'/views/img/settings_w.png"  />'.$this->l('Wordpress Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(53)" id="tab-menu-53"><img src="../modules/'.$this->name.'/views/img/settings_tu.png"  />'.$this->l('Tumblr Settings').'</a></li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(54)" id="tab-menu-54"><img src="../modules/'.$this->name.'/views/img/settings_pi.png"  />'.$this->l('Pinterest Settings').'</a></li>';
         if($this->_is_odnoklassniki)
			$_html .= '<li><a href="javascript:void(0)" onclick="tabs_custom(55)" id="tab-menu-55"><img src="../modules/'.$this->name.'/views/img/settings_od.png"  />'.$this->l('Odnoklassniki Settings').'</a></li>';
         if($this->_is_mailru)
			$_html .= '<li><a href="javascript:void(0)" onclick="tabs_custom(56)" id="tab-menu-56"><img src="../modules/'.$this->name.'/views/img/settings_ma.png"  />'.$this->l('Mail.ru Settings').'</a></li>';
         if($this->_is_yandex)
            $_html .= '<li><a href="javascript:void(0)" onclick="tabs_custom(57)" id="tab-menu-57"><img src="../modules/'.$this->name.'/views/img/settings_ya.png"  />'.$this->l('Yandex Settings').'</a></li>';

         $_html .= '

         <li><a href="javascript:void(0)" onclick="tabs_custom(58)" id="tab-menu-58"><img src="../modules/'.$this->name.'/views/img/settings_v.png"  />'.$this->l('Vkontakte Settings').'</a></li>

        <li><a href="javascript:void(0)" onclick="tabs_custom(10)" id="tab-menu-10"><img src="../modules/'.$this->name.'/views/img/statistics.png"  />'.$this->l('Statistics').'</a></li>




			
			<li>&nbsp;</li>
			<li><a href="javascript:void(0)" onclick="tabs_custom(91)" id="tab-menu-91"><img src="../modules/'.$this->name.'/views/img/icon/ico_help.gif"  />'.$this->l('FAQ').'</a></li>
			<li><a href="http://addons.prestashop.com/en/2_community-developer?contributor=61669" target="_blank"><img src="../modules/'.$this->name.'/views/img/spm-logo.png"  />'.$this->l('Other SPM Modules').'</a></li>
			
			</ul>
		</fieldset>
			
			<div class="'.$this->name.'-content">';
				$_html .= '<div id="tabs-90">'.$this->_welcome13_14_15().'</div>';
				$_html .= '<div id="tabs-99">'.$this->_basicSettings().'</div>';
                $_html .= '<div id="tabs-98">'.$this->_enabledisable13_14(
                        array(
                            array('prefix'=>'f','prefix_full'=>'facebook','title_item'=>$this->l('Facebook'),),
                            array('prefix'=>'t','prefix_full'=>'twitter','title_item'=>$this->l('Twitter'),),
                            array('prefix'=>'a','prefix_full'=>'amazon','title_item'=>$this->l('Amazon'),),
                            array('prefix'=>'g','prefix_full'=>'google','title_item'=>$this->l('Google'),),
                            array('prefix'=>'y','prefix_full'=>'yahoo','title_item'=>$this->l('Yahoo'),),
                            array('prefix'=>'p','prefix_full'=>'paypal','title_item'=>$this->l('Paypal'),),
                            array('prefix'=>'l','prefix_full'=>'linkedin','title_item'=>$this->l('Linkedin'),),
                            array('prefix'=>'m','prefix_full'=>'microsoft','title_item'=>$this->l('Microsoft'),),
                            //array('prefix' => 'i', 'prefix_full' => 'instagram', 'title_item' => $this->l('Instagram'),),
                            array('prefix'=>'fs','prefix_full'=>'foursquare','title_item'=>$this->l('Foursquare'),),
                            array('prefix'=>'gi','prefix_full'=>'github','title_item'=>$this->l('Github'),),
                            array('prefix'=>'d','prefix_full'=>'disqus','title_item'=>$this->l('Disqus'),),
                            array('prefix'=>'db','prefix_full'=>'dropbox','title_item'=>$this->l('Dropbox'),),
                            array('prefix'=>'w','prefix_full'=>'wordpress','title_item'=>$this->l('Wordpress'),),
                            array('prefix'=>'tu','prefix_full'=>'tumblr','title_item'=>$this->l('Tumblr'),),
                            array('prefix'=>'pi','prefix_full'=>'pinterest','title_item'=>$this->l('Pinterest'),),
                            array('prefix'=>'v','prefix_full'=>'vkontakte','title_item'=>$this->l('Vkontakte'),),

                        )
                    ).'</div>';
				$_html .= '<div id="tabs-1">'.$this->_drawFacebookSettingsForm().'</div>';
				$_html .= '<div id="tabs-2">'.$this->_drawTwitterSettingsForm().'</div>';
				
				$_html .= '<div id="tabs-24">'.$this->_drawAmazonSettingsForm().'</div>';
				
				
				$_html .= '<div id="tabs-3">'.$this->_drawGoogleSettingsForm().'</div>';
				$_html .= '<div id="tabs-6">'.$this->_drawYahooSettingsForm().'</div>';


                $_html .= '<div id="tabs-8">'.$this->_drawPaypalSettingsForm().'</div>';

     			$_html .= '<div id="tabs-4">'.$this->_drawLinkedInSettingsForm().'</div>';
     			$_html .= '<div id="tabs-5">'.$this->_drawMicrosoftSettingsForm().'</div>';
         if($this->_is_instagram)
     			$_html .= '<div id="tabs-7">'.$this->_drawInstagramSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-20">'.$this->_drawFoursquareSettingsForm().'</div>';
     			$_html .= '<div id="tabs-21">'.$this->_drawGithubSettingsForm().'</div>';
     			$_html .= '<div id="tabs-22">'.$this->_drawDisqusSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-50">'.$this->_drawDropboxSettingsForm().'</div>';
     			
     			//$_html .= '<div id="tabs-51">'.$this->_drawScoopSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-52">'.$this->_drawWordpressSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-53">'.$this->_drawTumblrSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-54">'.$this->_drawPinterestSettingsForm().'</div>';
         if($this->_is_odnoklassniki)
     			$_html .= '<div id="tabs-55">'.$this->_drawOklassSettingsForm().'</div>';
         if($this->_is_mailru)
     			$_html .= '<div id="tabs-56">'.$this->_drawMailruSettingsForm().'</div>';
         if($this->_is_yandex)
     			$_html .= '<div id="tabs-57">'.$this->_drawYandexSettingsForm().'</div>';

         $_html .= '<div id="tabs-58">'.$this->_drawVkSettingsForm().'</div>';
     			
     			$_html .= '<div id="tabs-10">'.$this->_statistics().'</div>';
     			$_html .= '<div id="tabs-91">'.$this->_faq16().'</div>';
     			
     		
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

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_http_host = $shop_obj->getBaseURL($is_ssl);


                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_http_host = str_replace("http://","https://",$_http_host);
                }

            }

        }
    
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
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on').' <b>"+ Add New App"</b> '.$this->l('button').'.
    	</div>
    


    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Create a New App. Enter').' <b>"Display Name"</b> '.$this->l('and').' <b>"Contact Email"</b> '.$this->l('for app and press').' <b>"Create App ID"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"Settings"</b> '.$this->l('in the menu from left sidebar then Click on').' <b>"+Add Platform"</b>.
    	</div>
    
    	<div class="item-help-info">
    	<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on').' <b>"+ Add Platform"</b> '.$this->l('and select').' <b>"Website"</b> '.$this->l('platform').'".
    	</div>
    
    	<div class="item-help-info">
    	<b>6.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Site URL"</b>:
    	<input type="text" value="'.$_http_host.'" style="max-width:100%;min-width: 50%">
    	</div>
    
    	<div class="item-help-info">
    	<b>6.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your e-mail in').' <b>"Contact Email"</b> '.$this->l('to make app availble to all user').'.
    	</div>

    	<div class="item-help-info">
    	<b>6.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"Category"</b>.
    	</div>
    
    	<div class="item-help-info">
    	<b>6.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('After that click on').' <b>"Save Changes"</b> '.$this->l('button').'.
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

         if($this->_is15) {
             $current_shop_id = Shop::getContextShopID();

             if($current_shop_id) {

                 $is_ssl = false;
                 if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                     $is_ssl = true;

                 $shop_obj = new Shop($current_shop_id);

                 $_http_host = $shop_obj->getBaseURL($is_ssl);

                 if((bool)Configuration::get('PS_SSL_ENABLED')){
                     $_http_host = str_replace("http://","https://",$_http_host);
                 }

             }

         }

		$callback_url = $this->getRedirectURL(array('typelogin'=>'twitter','is_settings'=>1));

         if(version_compare(_PS_VERSION_, '1.7', '>')) {
             $delimeter_rewrite = "&";
              if(Configuration::get('PS_REWRITING_SETTINGS')){
                  $delimeter_rewrite = "?";
             }

             $html_help = ' ( Shop Parameters -> Traffic & SEO -> SEO & URLs -> Set up URLs )';

             $txt_url_rewrite = '<b style="color:green">'.$this->l('Twitter connect works only if Friendly URL is ENABLED in your SHOP.').'</b>'.

             (!Configuration::get('PS_REWRITING_SETTINGS')?'<br/><b style="color:red">'.$this->l('Friendly URL is DISABLED in your SHOP').$html_help.'</b>':'<br/><b style="color:green">'.$this->l('Friendly URL is ENABLED in your SHOP').'</b>');


         } else {
             $txt_url_rewrite = '';
             $delimeter_rewrite = "?";
         }



		$callback_url = $callback_url.$delimeter_rewrite.'action=callback';
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
				<input type="text" value="'.$_http_host.'" style="max-width:100%;min-width: 50%">
				</div>
				
				<div class="item-help-info">
				<b>3.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Callback URL"</b> '.$this->l('field').':
				<br/>
				'.$txt_url_rewrite.'
				<br/>
				<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%">
				</div>
				
				<div class="item-help-info">
				<b>3.5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Read and agree to rules, and then click').' <b>"Create your Twitter application"</b> '.$this->l('button').'.
				</div>
			
				<div class="item-help-info">
				<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').'  <b>"Permissions"</b> '.$this->l('tab, and set Access').' <b>"Read Only"</b>.
				</div>

				<div class="item-help-info">
				<b>4.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select checkbox').':  <b>"Request email addresses from users"</b>.
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

    private function _paypalhelp(){
        $_html = '';

        // callback_url
        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
        } else {
            $_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        }

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_http_host = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_http_host = str_replace("http://","https://",$_http_host);
                }
            }

        }



        $callback_url = $this->getRedirectURL(array('typelogin'=>'paypal','is_settings'=>1));

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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://developer.paypal.com/developer/applications">Paypal Developer</a> '.$this->l('link and login with your credentials').'.
    	</div>

    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create App"</b> '.$this->l('button').'.
    	</div>

    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your App name in').' <b>"App name"</b> '.$this->l('field').' '.$this->l('and press').' <b>"Create App"</b> '.$this->l('button').'.
    	</div>


    	<div class="item-help-info">
    	<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Switch to the').' <b>"Live"</b> '.$this->l('mode').':
    	</div>

    	<div class="item-help-info">
    	<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"LIVE APP SETTINGS"</b>:
    	</div>

    	<div class="item-help-info">
    	<b>5.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill').' <b>"Return URL"</b>: <input type="text" value="'.$callback_url.'" style="width:450px">.
    	</div>

    	<div class="item-help-info">
    	<b>6.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Open').' <b>"Advanced options"</b> '.$this->l('in').' <b>"Log In with PayPal"</b> '.$this->l('section').'
    	</div>

    	<div class="item-help-info">
    	<b>6.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select all checkboxes').' <b>"Personal Information"</b>, <b>"Address Information"</b>, <b>"Account Information"</b>.
    	</div>

    	<div class="item-help-info">
    	<b>6.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill').' <b>"Privacy Policy URL"</b>: <input type="text" value="'.$_http_host.'" style="width:450px">.
    	</div>

    	<div class="item-help-info">
    	<b>6.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill').' <b>"User agreement URL"</b>: <input type="text" value="'.$_http_host.'" style="width:450px">.
    	</div>

    	<div class="item-help-info">
    	<b>6.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save"</b> '.$this->l('button').'.
    	</div>


    	<div class="item-help-info">
    	<b>7.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"LIVE API CREDENTIALS"</b>:
    	</div>

    	<div class="item-help-info">
    	<b>7.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy').' <b>"Client ID"</b>' .$this->l('and').' <b>"Secret"</b>.
    	</div>
    	';

        $_html .= '<div class="item-help-info way-color">'
            .$this->l('Way').' 2:'.
            '</div>';

        $_html .= 	'<div class="item-help-info">'
            .$this->l('To configure the "Paypal API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
            '</div>';





        $_html .= '</fieldset>';
        return $_html;
    }

    private function _googlehelp(){
        $_html = '';

        // callback_url
        $_host_url = _PS_BASE_URL_SSL_;


        $callback_url = $this->getRedirectURL(array('typelogin'=>'google','is_settings'=>1));

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
    			    <b style="color:red">'.$this->l('If you in first time create project (punct 1,2):').'</b>
    			</div>
                <div class="item-help-info">
    			<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://console.developers.google.com/project">Google Developers console</a> '.$this->l('link and login with your credentials').'.
				</div>

                <div class="item-help-info">
				<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create a project"</b> '.$this->l('button').'.
				</div>

                <div class="item-help-info">
				<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Wait a few seconds until your project will be created').'
				</div>

				<div class="item-help-info">
				<b>3.1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill field').'  <b>"POJECT NAME"</b>
				</div>



				<br/><br/>

    			<div class="item-help-info">
    			    <b style="color:red">'.$this->l('If you already create some projects (punct 1,2):').'</b>
    			</div>

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
				<b>3.1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill field').'  <b>"POJECT NAME"</b>
				</div>



				<br/><br/>
				<div class="item-help-info">
				<b>4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <b>"Credentials"</b>.
				</div>

				<div class="item-help-info">
				<b>4.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Add credentials"</b> '.$this->l('and select').' <b>"OAuth Client ID"</b>.
				</div>

				<div class="item-help-info">
				<b>4.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Configure Consent screen"</b>.
				</div>

				<div class="item-help-info">
				<b>4.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your email address in').' <b>"Email address"</b> '.$this->l('field').'.
				</div>

				<div class="item-help-info">
				<b>4.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your product name in').' <b>"Product name"</b> '.$this->l('field').'.
				</div>

				<div class="item-help-info">
				<b>4.5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Save"</b> '.$this->l('button').'.
				</div>



				<div class="item-help-info">
				<b>5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Choose').' <b>"APPLICATION TYPE"</b> -> <b>Web Application</b>.
				</div>

				<div class="item-help-info">
				<b>5.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill').' <b>"Name"</b> '.$this->l('field').'.
				</div>

				<div class="item-help-info">
				<b>5.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Authorized JavaScript origins"</b> '.$this->l('field').':
				<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%">
				</div>

				<div class="item-help-info">
				<b>5.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Authorized redirect URIs"</b> '.$this->l('field').':
				<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%">
				</div>

				<div class="item-help-info">
				<b>5.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Create"</b> '.$this->l('button').'.
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

     if($this->_is15) {
         $current_shop_id = Shop::getContextShopID();

         if($current_shop_id) {

             $is_ssl = false;
             if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                 $is_ssl = true;

             $shop_obj = new Shop($current_shop_id);

             $_http_host = $shop_obj->getBaseURL($is_ssl);

             if((bool)Configuration::get('PS_SSL_ENABLED')){
                 $_http_host = str_replace("http://","https://",$_http_host);
             }

         }

     }
    
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
    	<input type="text" value="'.$_http_host.'" style="max-width:100%;min-width: 50%">
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

        $callback_url = $this->getRedirectURL(array('typelogin'=>'microsoft','is_settings'=>1));

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
				<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create Application"</b> '.$this->l('button').'.
				</div>



				<div class="item-help-info">
				<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').'  <b>"Web"</b>
				</div>

				<div class="item-help-info">
				<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Redirect URL"</b> '.$this->l('field').':
				<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%">
				</div>


				<div class="item-help-info">
				<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save"</b> '.$this->l('button').'.
				</div>

				<div class="item-help-info">
				<b>5.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').'  <b>"Properties"</b>
				</div>

				<div class="item-help-info">
				<b>6.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the').' <b>"Application Id"</b>' .$this->l('and').' <b>"Application Secret key"</b>.
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
    			
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_http_host = str_replace("http://","https://",$_http_host);
                }

            }

        }

    	$callback_url = $this->getRedirectURL(array('typelogin'=>'instagram','is_settings'=>1));
    	
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
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%">
    	</div>
    
    	<div class="item-help-info">
    	<b>3.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Redirect URI(s)"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%">
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
    			
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_host_url = str_replace("http://","https://",$_host_url);
                }

            }

        }
        $callback_url = $this->getRedirectURL(array('typelogin'=>'foursquare','is_settings'=>1));
    	
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
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Your privacy policy url"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Redirect URI(s)"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
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
    	
    		 
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_host_url = str_replace("http://","https://",$_host_url);
                }

            }

        }
    	$callback_url = $this->getRedirectURL(array('typelogin'=>'github','is_settings'=>1));

    	 
    	
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
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>

    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Application description in ').' <b>"Application description"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Authorization callback URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
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
    	 
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_host_url = str_replace("http://","https://",$_host_url);
                }

            }

        }
    	$callback_url = $this->getRedirectURL(array('typelogin'=>'disqus','is_settings'=>1));

    	 
    	
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
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
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
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
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
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
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
    		$_host_url = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;

    		$js_origins = Tools::getShopDomainSsl(true, true);

    		 
    	} else {
    		$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    	
    		$js_origins = _PS_BASE_URL_SSL_;

    	}

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_host_url = str_replace("http://","https://",$_host_url);
                }

            }

        }

        $callback_url = $this->getRedirectURL(array('typelogin'=>'amazon','is_settings'=>1));
    	
    
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
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
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
    	<input type="text" value="'.$js_origins.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	 
    	<div class="item-help-info">
    	<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Allowed Return URLs"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
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
    
    	
    	$_html .= '<p class="alert alert-danger">'.$this->l('Note: To enable Amazon Connect, Please make sure that "SSL" has enabled on your server').'</p>';
    
    
    
    
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
    
    private function _dropboxhelp(){
    	$_html = '';
    	 
    	// callback_url

    
    	$callback_url = $this->getRedirectURL(array('typelogin'=>'dropbox','is_settings'=>1));
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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://www.dropbox.com/developers/apps">DropBox Developers</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create app"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Choose an API').' -  <b>Dropbox API</b>  - '.$this->l('Access to a single folder created specifically for your app').'
    	</div>
    
    	<div class="item-help-info">
    	<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Choose the type of access you need').':  <b>"App folder"</b> '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter Name your app').'
    	</div>
    
    	
    
    	<div class="item-help-info">
    	<b>3.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Check').' <b>"I agree to Dropbox API Terms and Conditions"</b>
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Create app"</b> '.$this->l('button').'
    	</div>
    
    	
    	
    		
    	<div class="item-help-info">
    	<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Add').' <b>"Redirect URIs"</b> '.$this->l('with').' <b style="color:red">HTTPS</b>:
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%">
    	'.$this->l('and click').' <b>"Add"</b>  '.$this->l('button').'
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy').' <b>"App key"</b>' .$this->l('and').' <b>"App secret"</b>.
    	</div>
    	';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "DropBox API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    	$_html .= '<p class="alert alert-danger">'.$this->l('Note: To enable Dropbox Connect, Please make sure that "SSL" has enabled on your server').'</p>';
    	
    	 
    	 
    	 
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
    private function _wordpresshelp(){
    	$_html = '';
    	 
    	 
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_host_url = str_replace("http://","https://",$_host_url);
                }

            }

        }
    	$callback_url = $this->getRedirectURL(array('typelogin'=>'wordpress','is_settings'=>1));
    
    	 
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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://developer.wordpress.com/apps/">Developers resources</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>1.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the ').' <b>"Create New Appliaction"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Name of your application in ').' <b>"Name"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Description in ').' <b>"Description"</b> '.$this->l('field').'.
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Website URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    
    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Redirect URI"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    
    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Javascript Origins"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select ').' <b>"Type"</b> -> <b>"Web"</b>.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.6</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Create"</b> '.$this->l('button').'.
    	</div>
    	 
    	
    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Editing your name of application"</b>.
    	</div>
    
    	<div class="item-help-info">
    	<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"Client ID"</b>' .$this->l('and').' <b>"Client Secret"</b>.
    	</div>
    	';
    	 
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Wordpress API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    	 
    	 
    	 
    	 
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    private function _tumblrhelp(){
    	$_html = '';
    
    
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_host_url = str_replace("http://","https://",$_host_url);
                }
            }

        }
    
    	$callback_url = $this->getRedirectURL(array('typelogin'=>'tumblr','is_settings'=>1));
    
    
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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://www.tumblr.com/oauth/apps">Tumblr Developers</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>1.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the ').' <b>"+ Register Appliaction"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Application name in ').' <b>"Application name"</b> '.$this->l('field').'.
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Application website"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your Application description in ').' <b>"Application description"</b> '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Default callback URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    
    	
    	<div class="item-help-info">
    	<b>2.6</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save changes"</b> '.$this->l('button').'.
    	</div>
    
    	 
    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"Consumer Key"</b>' .$this->l('and').' <b>"Secret Key"</b>.
    	</div>
    	';
    
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Tumblr API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    
    
    
    
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    private function _pinteresthelp(){
    	$_html = '';
    
    	// callback_url
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    		 
    	} else {
    		$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    	}

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_http_host = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_http_host = str_replace("http://","https://",$_http_host);
                }

            }

        }

    	$callback_url = $this->getRedirectURL(array('typelogin'=>'pinterest','is_settings'=>1));
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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://developers.pinterest.com/apps/">Pinterest Developers</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Agree to the Pinterest Developer Terms and the API Policy. Click on the').' <b>"Create app"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill Name in ').' "<b>Name</b>"  '.$this->l('field').'
    	</div>
    
    	<div class="item-help-info">
    	<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill Description in ').' "<b>Description</b>"  '.$this->l('field').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>3.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Create"</b> '.$this->l('button').'
    	</div>
    
    	
    	<div class="item-help-info">
    	<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section Platforms -> Web').'.  <b>"Site URL"</b> '.$this->l('must be').':
    	<input type="text" value="'.$_http_host.'" style="max-width:100%;min-width: 50%">
    	</div>
    
    	<div class="item-help-info">
    	<b>4.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section Platforms -> Web').'.  <b>"Redirect URIs"</b> '.$this->l('must be with').' <b style="color:red">HTTPS</b>:
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%">
    	</div>
    	
    	<div class="item-help-info">
    	<b>4.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click on the').' <b>"Save"</b> '.$this->l('button').'
    	</div>
    	 
    	 
    	<div class="item-help-info">
    	<b>5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy').' <b>"App ID"</b>' .$this->l('and').' <b>"App secret"</b>.
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>6</b>&nbsp;&nbsp;&nbsp;'.$this->l('After testing you must change').' <b>"Status"</b> ' .$this->l('of your application').'.
    	'.$this->l('Click on the').' <b>"Submit for review"</b>.
    	</div>
    	
    	';
    	
    	
    	
    
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Pinterest API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    	$_html .= '<p class="alert alert-danger">'.$this->l('Note: To enable Pinterest Connect, Please make sure that "SSL" has enabled on your server').'</p>';
    	 
    
    
    
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
    private function _oklasshelp(){
    	$_html = '';
    
    	// callback_url
    
    	if(version_compare(_PS_VERSION_, '1.6', '>')){
    		$_host_url = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
    		 
    		 
    	} else {
    		$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    	
    	}

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_http_host = str_replace("http://","https://",$_http_host);
                }

            }

        }
    	 
    	$callback_url = $this->getRedirectURL(array('typelogin'=>'oklass','is_settings'=>1));
    	
    
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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="http://ok.ru/devaccess">http://ok.ru/devaccess</a> '.$this->l('link and login with your credentials').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>1.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Get Developer Rights').'
    	 </div>
    	 
    	<div class="item-help-info">
    	<b>1.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('After receiving Developer Rights you have the link to add applications.').'
    	'.$this->l('Open the section Games in the left menu and select ').'<b>"My uploaded Games"</b>
    	</div>
    	
    
    	<div class="item-help-info">
    	<b>2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Add App"</b>
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Title"</b> '.$this->l('field').'.
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Shortname"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Description"</b> '.$this->l('field').'.
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"Application type"</b> <b>Web (HTML)</b>, <b>HTML (Mobile)</b>, <b>External</b> (3 types!)
    	</div>
    
    	<div class="item-help-info">
    	<b>2.5</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Link to avatars and icons"</b> '.$this->l('fields').' (128x128, 50x50, 18x18)
    	</div>
    	
    
    	<div class="item-help-info">
    	<b>2.6</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"App link"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>

    	<div class="item-help-info">
    	<b>2.7</b>&nbsp;&nbsp;&nbsp;'.$this->l('Add').' <b>"List of permitted redirect_uri"</b> '.$this->l('with').' <b style="color:red">HTTPS</b>:
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.8</b>&nbsp;&nbsp;&nbsp;'.$this->l('Add').' <b>"Callback link"</b> '.$this->l('with').' <b style="color:red">HTTPS</b>:
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.9</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"E-mail for notifications"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.10</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select ').' <b>"App size"</b> '.$this->l('must be').' <b>Full screen</b>
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.11</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save"</b> '.$this->l('button').'.
    	</div>
    
    
    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('After you get ').' <b>"Application ID"</b>, <b>"Application Public Key"</b>, <b>"Application Secret Key"</b> 
    	<b style="color:red">'.$this->l('on your email at').' www.odnoklassniki.ru (www.ok.ru)</b>
    	</div>
    	 
    	 
    	 
    	';
    	 
    
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Odnoklassniki API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    	
    	$_html .= '<p class="alert alert-danger">'.$this->l('Note: To enable Odnoklassniki Connect, Please make sure that "SSL" has enabled on your server').'</p>';
    
    
    
    
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    
    private function _mailruhelp(){
    	$_html = '';
    
    
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_http_host = str_replace("http://","https://",$_http_host);
                }

            }

        }
    
    	//$callback_url = $this->getRedirectURL(array('typelogin'=>'mailru','is_settings'=>1));
    
    
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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="http://api.mail.ru/sites/my/add">http://api.mail.ru/sites/my/add</a> '.$this->l('link and login with your credentials').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>1.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Accept').' <b>"Terms and conditions"</b> '.$this->l('and click the').' <b>"Next"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your site Name in the').' <b>"Name"</b> '.$this->l('field').'.
    	</div>
    	 
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Your site URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    
    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Continue"</b> '.$this->l('button').'.
    	</div>
    
    	 
    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Download').' <b>"receiver.html"</b>. '.$this->l('Put').' <b>"receiver.html"</b> '.$this->l('in the root folder of your site').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Continue"</b> '.$this->l('button').'.
    	</div>
    
    	<div class="item-help-info">
    	<b>4.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"ID"</b>' .$this->l('and').' <b>"Secret Key"</b>.
    	</div>
    	';
    
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Mail.ru API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    
    
    
    
    
    	$_html .= '</fieldset>';
    	return $_html;
    }
    
    private function _yandexhelp(){
    	$_html = '';
    	
    	
    	$_host_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;


        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_host_url = $shop_obj->getBaseURL($is_ssl);

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_http_host = str_replace("http://","https://",$_http_host);
                }

            }

        }

    	
    	$callback_url = $this->getRedirectURL(array('typelogin'=>'yandex','is_settings'=>1));
    	
    	
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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://oauth.yandex.ru/client/new">https://oauth.yandex.ru/client/new</a> '.$this->l('link and login with your credentials').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter your site Name in the').' <b>"Name"</b> '.$this->l('field').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Link on the site App"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select').' <b>"API Yandex Passport"</b> '.$this->l('and select all checkboxes').'.
    	</div>
    	
    	 <div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Callback URL"</b> '.$this->l('field').':
    	<input type="text" value="'.$callback_url.'" style="max-width:100%;min-width: 50%" />
    	</div>
    	
    	
    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Save"</b> '.$this->l('button').'.
    	</div>
    	
    	<div class="item-help-info">
    	<b>3.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"ID"</b>' .$this->l('and').' <b>"Client Secret"</b>.
    	</div>
    	';
    	
    	$_html .= '<div class="item-help-info way-color">'
    	.$this->l('Way').' 2:'.
    	'</div>';
    	
    	$_html .= 	'<div class="item-help-info">'
    	.$this->l('To configure the "Yandex API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
    	style="text-decoration:underline;color:#0071bc">Installation_Guid.pdf</a> ,'.$this->l(' which is located in the folder  with the module.').
    	'</div>';
    	
    	
    	
    	
    	
    	$_html .= '</fieldset>';
    	return $_html;
    }

    private function _vkontaktehelp(){
        $_html = '';

        // callback_url

        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;

            $_host_url = $_http_host;

        } else {
            $_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;

            $_host_url = $_http_host;
        }

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {

                $is_ssl = false;
                if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                    $is_ssl = true;

                $shop_obj = new Shop($current_shop_id);

                $_http_host = $shop_obj->getBaseURL($is_ssl);
                $_host_url = $_http_host;

                if((bool)Configuration::get('PS_SSL_ENABLED')){
                    $_host_url = str_replace("http://","https://",$_host_url);
                }
            }

        }

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
    	<b>1.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to').' <a target="_blank" style="text-decoration:underline;color:#0071bc" href="https://vk.com/editapp?act=create">https://vk.com/editapp?act=create</a> '.$this->l('link and login with your credentials').'.
    	</div>

    	<div class="item-help-info">
    	<b>2.</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Title"</b> '.$this->l('field').'.
    	</div>

    	<div class="item-help-info">
    	<b>2.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Select  ').' <b>"Category"</b>. '.$this->l('Must be ').' <b>"Website"</b>!
    	</div>

    	<div class="item-help-info">
    	<b>2.2</b>&nbsp;&nbsp;&nbsp;'.$this->l('Enter this in').' <b>"Site address"</b> '.$this->l('field').':
    	<input type="text" value="'.$_host_url.'" style="width:450px" />
    	</div>

    	<div class="item-help-info">
    	<b>2.3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Fill the ').' <b>"Base domain"</b> '.$this->l('field').'. <span style="color:red;font-size:14px">'.$this->l('This is your domain name without WWW').'</span>.
    	</div>

    	<div class="item-help-info">
    	<b>2.4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Click').' <b>"Connect Site"</b> '.$this->l('button').'.
    	</div>


    	<div class="item-help-info">
    	<b>3</b>&nbsp;&nbsp;&nbsp;'.$this->l('Confirm Create Application').'.
    	</div>

    	<div class="item-help-info">
    	<b>4</b>&nbsp;&nbsp;&nbsp;'.$this->l('Go to section').' <b>"Settings"</b>
    	</div>


    	<div class="item-help-info">
    	<b>4.1</b>&nbsp;&nbsp;&nbsp;'.$this->l('Copy the generated').' <b>"Application ID"</b> ' .$this->l('and').'  <b>"Secure key"</b>.
    	</div>
    	';

        $_html .= '<div class="item-help-info way-color">'
            .$this->l('Way').' 2:'.
            '</div>';

        $_html .= 	'<div class="item-help-info">'
            .$this->l('To configure the "Vkontakte API" read').' <a target="_blank" href="../modules/'.$this->name.'/Installation_Guid.pdf"
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
        
    	include_once(_PS_MODULE_DIR_.$this->name.'/classes/statisticshelp.class.php');
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
    	foreach($this->getConnetsArrayPrefix() as $data_prefix){

                $text_type = $data_prefix['prefix'];
                $id_type = $data_prefix['type'];
    			
    		if($text_type=="foursquare")
	    		$key_prefix = "fs";
	    	elseif($text_type=="github")
	    		$key_prefix = "gi";
	    	elseif($text_type=="tumblr")
	    		$key_prefix = "tu";
	    	elseif($text_type=="pinterest")
	    		$key_prefix = "pi";
	    	elseif($text_type=="oklass"){
	    		//$text_type = "Odnoklassniki";
	    		$key_prefix = "od";
	    	}elseif($text_type=="mailru"){
	    		//$text_type = "Mail.ru";
	    		$key_prefix = "ma";
	    	}elseif($text_type=="yandex")
	    		$key_prefix = "ya";
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
    	
    	$data_avaiable_types = $this->getConnetsArrayPrefix();
    	
	    foreach($data_info as $_items){
    		$uid = $_items['id'];
    		$name_user = $_items['firstname']. ' '.$_items['lastname'];
    		$name_shop = $_items['name_shop'];
    		
    		$_html .= '<tr>';
    			$_html .= '<td>'.$uid.'</td>';


                $type = $_items['type'];

                foreach($data_avaiable_types as $data_prefix) {

                    $id_type = $data_prefix['type'];
                    if($type == $id_type){
                        $text_type = $data_prefix['prefix'];
                    }
                }



	    		if($text_type=="foursquare")
	    			$key_prefix = "fs";
	    		elseif($text_type=="github")
	    			$key_prefix = "gi";
	    		elseif($text_type=="tumblr")
	    			$key_prefix = "tu";
	    		elseif($text_type=="pinterest")
	    			$key_prefix = "pi";
	    		elseif($text_type=="oklass"){
	    			$text_type = "Odnoklassniki";
	    			$key_prefix = "od";
	    		}elseif($text_type=="mailru"){
	    			$text_type = "Mail.ru";
	    			$key_prefix = "ma";
	    		}elseif($text_type=="yandex")
	    			$key_prefix = "ya";
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
    


    

	private function _is_curl_installed() {
		if  (function_exists('curl_init')) {
			return true;
		}
		else {
			return false;
		}
	}
	
	private function _faq16(){
		$_html  = '';
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
		$_html .= '<div class="panel">
		
		<div class="panel-heading"><i class="fa fa-question-circle fa-lg"></i>&nbsp;'.$this->l('Frequently Asked Questions').'</div>';
		} else {	
		
		$_html .= '<fieldset>
		<legend><img src="../modules/'.$this->name.'/views/img/icon/ico_help.gif" />'.$this->l('Frequently Asked Questions').'</legend>
			
		';
		}
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			
		$_html .= '<div class="row ">
                      
                       <div class="span">
                          <p>
                             <span style="font-weight: bold; font-size: 15px;" class="question">
                             	-  '.$this->l('I have enabled buttons for left column on login page, but left column is not coming with these buttons').'.
                             </span>
                             <br/><br/>
                             <span style="color: black;" class="answer">
                             		'.$this->l('Kindly ensure that left column / right column is enabled in your theme for Login page.')
                             		.$this->l('To check go to, Preferences->Themes->Advance Settings->Enable left column / right column from on the login page.').'
                             		
                             		
                                     
                                    
                              </span>
                         </p>
                       </div>
                       <br/><br/>';
		
           $_html .= '<div class="span">
                          <p>
                             <span style="font-weight: bold; font-size: 15px;" class="question">
                             	- '.$this->l('How can I add the Social Login buttons to any location on the page?').'
                             </span>
                             <br/><br/>
                             <span style="color: black;" class="answer">
                             	   '.$this->l('You just need to add a line of code to the tpl file of the page where you want to add the login buttons.').'
                                   <pre>{hook h=\'socialConnectSpm\'}</pre>
                              </span>
                         </p>
                       </div><br/><br/>';
		}
		
		$_html .= '
		
						<div class="span">
							<p>
								<span style="font-weight: bold; font-size: 15px;" class="question">
									-  '.$this->l('I get 500 Internal Error').'.
								</span>
								<br/><br/>
								<span style="color: black;" class="answer">
									'.$this->l('Internal 500 error - this error related with setting of your server.').
									'<br/><br/>'.$this->l('- The problem may be in the access rights to the folder /modules/'.$this->name.'/ Must be 0777 or 0755').'
									<br/><br/>'.$this->l('- The problem may be in the .htaccess file in the folder /modules/ . Try delete/rename file .htaccess').'
									<br/><br/>'.$this->l('- The problem may be in the index.php file in the folder /modules/ . Try delete/rename file index.php').'
									 
								
								</span>
							</p>
						</div>
					<br/><br/>';
		
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){            
                  $_html .= '</div>';
		}
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
		$_html .= '</div>';
		} else {
			$_html .= '</fieldset>';
		}
			
		return $_html;
	}
	
	private function _welcome16(){
	
		$_html  = '';
		
		$_html .= '<div class="panel">
	
		<div class="panel-heading"><i class="fa fa-home fa-lg"></i>&nbsp;'.$this->l('Welcome').'</div>';
			
		
		
		$_html .=  '<p class="alert alert-info">'.$this->l('Welcome and thank you for purchasing our module.').'</p>'.
		'<br/>';
		 
		$_html .= '<h2>'.$this->l('Prerequisites Check').'</h2>';
		
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

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            if(!Configuration::get('PS_REWRITING_SETTINGS')) {
                $_html .= '<br/>';

                $html_help = ' ( Shop Parameters -> Traffic & SEO -> SEO & URLs -> Set up URLs )';

                $_html .= '<p class="alert alert-danger">';
                $_html .= '<b style="color:red">' . $this->l('Friendly URL is DISABLED in your SHOP') . $html_help . '</b>';
                $_html .= '</p>';
            }

        }
		
		$_html .= '</div>';

		 
		return $_html;
	}
	
	
	private function _welcome13_14_15(){
	
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





    private function _drawVkSettingsForm(){
        $_html = '';

        $_html .= $this->_vkontaktehelp ();
        $_html .= '<br/>';

        $_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';

        $_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_v.png" />' . $this->l ( 'Vkontakte Settings' ) . '</legend>

		';

        // enable or disable vouchers
        $_html .= '<label>' . $this->l ( 'Enable or Disable Vkontakte Connect' ) . ':</label>
		<div class="margin-form">

		<input type="radio" value="1" id="text_list_on" name="v_on" onclick="enableOrDisableVkontakte(1)"
		' . (Tools::getValue ( 'v_on', Configuration::get ( $this->name . 'v_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>

		<input type="radio" value="0" id="text_list_off" name="v_on" onclick="enableOrDisableVkontakte(0)"
		' . (! Tools::getValue ( 'v_on', Configuration::get ( $this->name . 'v_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>

		<p class="clear">' . $this->l ( 'Enable or Disable Vkontakte Connect' ) . '.</p>
		</div>';

        $_html .= '<script type="text/javascript">
		function enableOrDisableVkontakte(id)
		{
		if(id==0){
		$("#block-vkontakte-settings").hide(200);
	} else {
	$("#block-vkontakte-settings").show(200);
	}

	}
	</script>';

        $_html .= '<div id="block-vkontakte-settings" ' . (Configuration::get ( $this->name . 'v_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';

        // changes OAuth 2.0

        // Google Client Id
        $_html .= '<label>' . $this->l ( 'Vkontakte API Key' ) . ':</label>

		<div class="margin-form">
		<input type="text" name="vci"  style="width:400px"
		value="' . Tools::getValue ( 'vci', Configuration::get ( $this->name . 'vci' ) ) . '">

		</div>';

        // Google Client Secret
        $_html .= '<label>' . $this->l ( 'Vkontakte API Secret' ) . ':</label>

		<div class="margin-form">
		<input type="text" name="vcs"  style="width:400px"
		value="' . Tools::getValue ( 'vcs', Configuration::get ( $this->name . 'vcs' ) ) . '">


		</div>';


        // changes OAuth 2.0

        $_html .= '<br/><br/>';

        $_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Vkontakte Connect Button' ), 'prefix' => 'v' ) );

        $_html .= '<br/><br/>';

        $_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Vkontakte Connect Large Image' ), 'title_medium' => $this->l ( 'Vkontakte Connect Medium Image' ),
            'title_small' => $this->l ( 'Vkontakte Connect Small Image' ), 'title_very_small' => $this->l ( 'Vkontakte Connect Very Small Image' ),
            'prefix_short' => 'v', 'prefix' => 'vkontakte' ) );

        $_html .= '</div>';

        $_html .= $this->_updateButton ( array ('name' => 'vkontakte', 'prefix' => 'v' ) );

        $_html .= '</fieldset>';

        $_html .= '</form>';

        return $_html;
    }



    private function _drawPaypalSettingsForm(){
        $_html = '';


        $_html .= $this->_paypalhelp();
        $_html .='<br/>';

        $_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';

        $_html .= '<fieldset>
		<legend><img src="../modules/'.$this->name.'/views/img/settings_p.png" />'.$this->l('Paypal Settings').'</legend>

		';

        // enable or disable vouchers
        $_html .= '<label>'.$this->l('Enable or Disable Paypal Connect').':</label>
		<div class="margin-form">

		<input type="radio" value="1" id="text_list_on" name="p_on" onclick="enableOrDisablePaypal(1)"
		'.(Tools::getValue('p_on', Configuration::get($this->name.'p_on')) ? 'checked="checked" ' : '').'>
		<label for="dhtml_on" class="t">
		<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
		</label>

		<input type="radio" value="0" id="text_list_off" name="p_on" onclick="enableOrDisablePaypal(0)"
		'.(!Tools::getValue('p_on', Configuration::get($this->name.'p_on')) ? 'checked="checked" ' : '').'>
		<label for="dhtml_off" class="t">
		<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
		</label>

		<p class="clear">'.$this->l('Enable or Disable Paypal Connect').'.</p>
		</div>';

        $_html .= '<script type="text/javascript">
		function enableOrDisablePaypal(id)
		{
		if(id==0){
		$("#block-paypal-settings").hide(200);
	} else {
	$("#block-paypal-settings").show(200);
	}

	}
	</script>';

        $_html .= '<div id="block-paypal-settings" '.(Configuration::get($this->name.'p_on')==1?'style="display:block"':'style="display:none"').'>';


        // Paypal Client ID
        $_html .= '<label>'.$this->l('Paypal Client ID').':</label>

		<div class="margin-form">
		<input type="text" name="clientid"  style="width:374px"
		value="'.Tools::getValue('clientid', Configuration::get($this->name.'clientid')).'">

		</div>';

        // Secret
        $_html .= '<label>'.$this->l('Paypal Secret').':</label>

		<div class="margin-form">
		<input type="text" name="psecret"  style="width:374px"
		value="'.Tools::getValue('psecret', Configuration::get($this->name.'psecret')).'">


		</div>';

        // Secret
        $_html .= '<label>'.$this->l('Callback URL').':</label>

		<div class="margin-form">
		<input type="text" name="pcallback"  style="width:374px"
		value="'.Tools::getValue('pcallback', Configuration::get($this->name.'pcallback')).'">


		</div>';

        $_html .= '<br/><br/>';


        $_html .= $this->_positionConnect(
            array(
                'title'=>$this->l('Position Paypal Connect Button'),
                'prefix'=>'p'
            )
        );

        $_html .= '<br/><br/>';



        $_html .= $this->_imagesConnects(
            array(
                'title_large'=>$this->l('Paypal Connect Large Image'),
                'title_medium'=>$this->l('Paypal Connect Medium Image'),
                'title_small'=>$this->l('Paypal Connect Small Image'),
                'title_very_small'=>$this->l('Paypal Connect Very Small Image'),
                'prefix_short'=>'p',
                'prefix'=>'paypal',
            )
        );

        $_html .= '</div>';

        $_html .= $this->_updateButton(array('name'=>'Paypal','prefix'=>'p'));

        $_html .=	'</fieldset>';


        $_html .= '</form>';
        return $_html;
    }
	
	private function _drawYandexSettingsForm(){
		$_html = '';
		
		$_html .= $this->_yandexhelp ();
		$_html .= '<br/>';
		
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_ya.png" />' . $this->l ( 'Yandex Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Yandex Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="ya_on" onclick="enableOrDisableYandex(1)"
		' . (Tools::getValue ( 'ya_on', Configuration::get ( $this->name . 'ya_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="ya_on" onclick="enableOrDisableYandex(0)"
		' . (! Tools::getValue ( 'ya_on', Configuration::get ( $this->name . 'ya_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Yandex Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisableYandex(id)
		{
		if(id==0){
		$("#block-yandex-settings").hide(200);
		} else {
		$("#block-yandex-settings").show(200);
		}
			
		}
		</script>';
		
		$_html .= '<div id="block-yandex-settings" ' . (Configuration::get ( $this->name . 'ya_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Yandex Client ID' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="yaci"  style="width:400px"
		value="' . Tools::getValue ( 'yaci', Configuration::get ( $this->name . 'yaci' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Yandex Client Secret' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="yacs"  style="width:400px"
		value="' . Tools::getValue ( 'yacs', Configuration::get ( $this->name . 'yacs' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Yandex Connect Button' ), 'prefix' => 'ya' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Yandex Connect Large Image' ), 'title_medium' => $this->l ( 'Yandex Connect Medium Image' ),
				'title_small' => $this->l ( 'Yandex Connect Small Image' ), 'title_very_small' => $this->l ( 'Yandex Connect Very Small Image' ),
				'prefix_short' => 'ya', 'prefix' => 'yandex' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'yandex', 'prefix' => 'ya' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
	
	private function _drawMailruSettingsForm(){
		$_html = '';
		
		$_html .= $this->_mailruhelp ();
		$_html .= '<br/>';
		
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_ma.png" />' . $this->l ( 'Mail.ru Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Mail.ru Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="ma_on" onclick="enableOrDisableMailru(1)"
		' . (Tools::getValue ( 'ma_on', Configuration::get ( $this->name . 'ma_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="ma_on" onclick="enableOrDisableMailru(0)"
		' . (! Tools::getValue ( 'ma_on', Configuration::get ( $this->name . 'ma_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Mailru Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisableMailru(id)
		{
		if(id==0){
		$("#block-mailru-settings").hide(200);
		} else {
		$("#block-mailru-settings").show(200);
		}
			
		}
		</script>';
		
		$_html .= '<div id="block-mailru-settings" ' . (Configuration::get ( $this->name . 'ma_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Mail.ru Client ID' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="maci"  style="width:400px"
		value="' . Tools::getValue ( 'maci', Configuration::get ( $this->name . 'maci' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Mail.ru Client Secret' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="macs"  style="width:400px"
		value="' . Tools::getValue ( 'macs', Configuration::get ( $this->name . 'macs' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Mail.ru Connect Button' ), 'prefix' => 'ma' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Mail.ru Connect Large Image' ), 'title_medium' => $this->l ( 'Mail.ru Connect Medium Image' ),
				'title_small' => $this->l ( 'Mail.ru Connect Small Image' ), 'title_very_small' => $this->l ( 'Mail.ru Connect Very Small Image' ),
				'prefix_short' => 'ma', 'prefix' => 'mailru' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'mailru', 'prefix' => 'ma' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
	
	private function _drawOklassSettingsForm(){
		$_html = '';
		
		$_html .= $this->_oklasshelp ();
		$_html .= '<br/>';
		
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_od.png" />' . $this->l ( 'Odnoklassniki Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Odnoklassniki Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="o_on" onclick="enableOrDisableOdnoklassniki(1)"
		' . (Tools::getValue ( 'o_on', Configuration::get ( $this->name . 'o_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="o_on" onclick="enableOrDisableOdnoklassniki(0)"
		' . (! Tools::getValue ( 'o_on', Configuration::get ( $this->name . 'o_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Odnoklassniki Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisableOdnoklassniki(id)
		{
		if(id==0){
		$("#block-odnoklassniki-settings").hide(200);
		} else {
		$("#block-odnoklassniki-settings").show(200);
		}
			
		}
		</script>';
		
		$_html .= '<div id="block-odnoklassniki-settings" ' . (Configuration::get ( $this->name . 'o_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Odnoklassniki Application ID' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="odci"  style="width:400px"
		value="' . Tools::getValue ( 'odci', Configuration::get ( $this->name . 'odci' ) ) . '">
		
		</div>';
		
		$_html .= '<label>' . $this->l ( 'Odnoklassniki Application Public Key' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="odpc"  style="width:400px"
		value="' . Tools::getValue ( 'odpc', Configuration::get ( $this->name . 'odpc' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Odnoklassniki Application Secret Key' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="odcs"  style="width:400px"
		value="' . Tools::getValue ( 'odcs', Configuration::get ( $this->name . 'odcs' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Odnoklassniki Connect Button' ), 'prefix' => 'o' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Odnoklassniki Connect Large Image' ), 'title_medium' => $this->l ( 'Odnoklassniki Connect Medium Image' ),
				'title_small' => $this->l ( 'Odnoklassniki Connect Small Image' ), 'title_very_small' => $this->l ( 'Odnoklassniki Connect Very Small Image' ),
				'prefix_short' => 'o', 'prefix' => 'oklass' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'odnoklassniki', 'prefix' => 'o' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
	
	private function _drawPinterestSettingsForm(){
		$_html = '';
		
		$_html .= $this->_pinteresthelp ();
		$_html .= '<br/>';
		
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_pi.png" />' . $this->l ( 'Pinterest Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Pinterest Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="pi_on" onclick="enableOrDisablePinterest(1)"
		' . (Tools::getValue ( 'pi_on', Configuration::get ( $this->name . 'pi_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="pi_on" onclick="enableOrDisablePinterest(0)"
		' . (! Tools::getValue ( 'pi_on', Configuration::get ( $this->name . 'pi_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Pinterest Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisablePinterest(id)
		{
		if(id==0){
		$("#block-pinterest-settings").hide(200);
		} else {
		$("#block-pinterest-settings").show(200);
		}
			
		}
		</script>';
		
		$_html .= '<div id="block-pinterest-settings" ' . (Configuration::get ( $this->name . 'pi_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Pinterest Client ID' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="pici"  style="width:400px"
		value="' . Tools::getValue ( 'pici', Configuration::get ( $this->name . 'pici' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Pinterest Client Secret' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="pics"  style="width:400px"
		value="' . Tools::getValue ( 'pics', Configuration::get ( $this->name . 'pics' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Pinterest Connect Button' ), 'prefix' => 'pi' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Pinterest Connect Large Image' ), 'title_medium' => $this->l ( 'Pinterest Connect Medium Image' ),
				'title_small' => $this->l ( 'Pinterest Connect Small Image' ), 'title_very_small' => $this->l ( 'Pinterest Connect Very Small Image' ),
				'prefix_short' => 'pi', 'prefix' => 'pinterest' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'pinterest', 'prefix' => 'pi' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
	
	private function _drawTumblrSettingsForm(){
		$_html = '';
		
		$_html .= $this->_tumblrhelp ();
		$_html .= '<br/>';
		
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_tu.png" />' . $this->l ( 'Tumblr Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Tumblr Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="tu_on" onclick="enableOrDisableTumblr(1)"
		' . (Tools::getValue ( 'tu_on', Configuration::get ( $this->name . 'tu_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="tu_on" onclick="enableOrDisableTumblr(0)"
		' . (! Tools::getValue ( 'tu_on', Configuration::get ( $this->name . 'tu_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Tumblr Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisableTumblr(id)
		{
		if(id==0){
		$("#block-tumblr-settings").hide(200);
		} else {
		$("#block-tumblr-settings").show(200);
		}
			
		}
		</script>';
		
		$_html .= '<div id="block-tumblr-settings" ' . (Configuration::get ( $this->name . 'tu_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Tumblr Client ID' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="tuci"  style="width:400px"
		value="' . Tools::getValue ( 'tuci', Configuration::get ( $this->name . 'tuci' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Tumblr Client Secret' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="tucs"  style="width:400px"
		value="' . Tools::getValue ( 'tucs', Configuration::get ( $this->name . 'tucs' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Tumblr Connect Button' ), 'prefix' => 'tu' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Tumblr Connect Large Image' ), 'title_medium' => $this->l ( 'Tumblr Connect Medium Image' ),
				'title_small' => $this->l ( 'Tumblr Connect Small Image' ), 'title_very_small' => $this->l ( 'Tumblr Connect Very Small Image' ),
				'prefix_short' => 'tu', 'prefix' => 'tumblr' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'tumblr', 'prefix' => 'tu' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
	
	private function _drawWordpressSettingsForm(){
		$_html = '';
		
		$_html .= $this->_wordpresshelp ();
		$_html .= '<br/>';
		
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_w.png" />' . $this->l ( 'Wordpress Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Wordpress Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="w_on" onclick="enableOrDisableWordpress(1)"
		' . (Tools::getValue ( 'w_on', Configuration::get ( $this->name . 'w_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="w_on" onclick="enableOrDisableWordpress(0)"
		' . (! Tools::getValue ( 'w_on', Configuration::get ( $this->name . 'w_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Wordpress Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisableWordpress(id)
		{
		if(id==0){
		$("#block-wordpress-settings").hide(200);
		} else {
		$("#block-wordpress-settings").show(200);
		}
			
		}
		</script>';
		
		$_html .= '<div id="block-wordpress-settings" ' . (Configuration::get ( $this->name . 'w_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Wordpress Client ID' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="wci"  style="width:400px"
		value="' . Tools::getValue ( 'wci', Configuration::get ( $this->name . 'wci' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Wordpress Client Secret' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="wcs"  style="width:400px"
		value="' . Tools::getValue ( 'wcs', Configuration::get ( $this->name . 'wcs' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Wordpress Connect Button' ), 'prefix' => 'w' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Wordpress Connect Large Image' ), 'title_medium' => $this->l ( 'Wordpress Connect Medium Image' ),
				'title_small' => $this->l ( 'Wordpress Connect Small Image' ), 'title_very_small' => $this->l ( 'Wordpress Connect Very Small Image' ),
				'prefix_short' => 'w', 'prefix' => 'wordpress' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'wordpress', 'prefix' => 'w' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
	
	private function _drawScoopSettingsForm(){
		$_html = '';
		
		$_html .= $this->_dropboxhelp ();
		
		
		
		
		$_html .= "<div style='text-align:center;padding:5px;border:1px solid red;font-weight:bold;margin-bottom:10px'>";
		$_html .= "Add detailed installation info!!!!!!! ";
		$_html .= "</div>";
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_s.png" />' . $this->l ( 'Scoop.it Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Scoop.it Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="s_on" onclick="enableOrDisableScoop(1)"
		' . (Tools::getValue ( 's_on', Configuration::get ( $this->name . 's_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="s_on" onclick="enableOrDisableScoop(0)"
		' . (! Tools::getValue ( 's_on', Configuration::get ( $this->name . 's_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Scoop.it Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisableScoop(id)
		{
		if(id==0){
		$("#block-scoop-settings").hide(200);
		} else {
		$("#block-scoop-settings").show(200);
		}
			
		}
		</script>';
		
		$_html .= '<div id="block-scoop-settings" ' . (Configuration::get ( $this->name . 's_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Scoop.it API Key' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="sci"  style="width:400px"
		value="' . Tools::getValue ( 'sci', Configuration::get ( $this->name . 'sci' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Scoop.it API Secret' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="scs"  style="width:400px"
		value="' . Tools::getValue ( 'scs', Configuration::get ( $this->name . 'scs' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Scoop.it Connect Button' ), 'prefix' => 's' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Scoop.it Connect Large Image' ), 'title_medium' => $this->l ( 'Scoop.it Connect Medium Image' ),
				'title_small' => $this->l ( 'Scoop.it Connect Small Image' ), 'title_very_small' => $this->l ( 'Scoop.it Connect Very Small Image' ),
				'prefix_short' => 's', 'prefix' => 'scoop' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'scoop', 'prefix' => 's' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
		return $_html;
	}
	

	private function _drawDropboxSettingsForm(){
		$_html = '';
		
		$_html .= $this->_dropboxhelp ();
		
		
		
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$_html .= "<p class=\"alert alert-danger\">Note: To enable Dropbox Connect, Please make sure that \"SSL\" has enabled on your server </p>";
		} else {
			$_html .= "<div style='text-align:center;padding:5px;border:1px solid red;font-weight:bold;margin-bottom:10px'>";
			$_html .= "Note: To enable Dropbox Connect, Please make sure that \"SSL\" has enabled on your server ";
			$_html .= "</div>";
		}
		
		
		
		$_html .= '
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post" >';
		
		$_html .= '<fieldset>
		<legend><img src="../modules/' . $this->name . '/views/img/settings_db.png" />' . $this->l ( 'Dropbox Settings' ) . '</legend>
		
		';
		
		// enable or disable vouchers
		$_html .= '<label>' . $this->l ( 'Enable or Disable Dropbox Connect' ) . ':</label>
		<div class="margin-form">
		
		<input type="radio" value="1" id="text_list_on" name="db_on" onclick="enableOrDisableDropbox(1)"
		' . (Tools::getValue ( 'db_on', Configuration::get ( $this->name . 'db_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_on" class="t">
		<img alt="' . $this->l ( 'Enabled' ) . '" title="' . $this->l ( 'Enabled' ) . '" src="../img/admin/enabled.gif">
		</label>
		
		<input type="radio" value="0" id="text_list_off" name="db_on" onclick="enableOrDisableDropbox(0)"
		' . (! Tools::getValue ( 'db_on', Configuration::get ( $this->name . 'db_on' ) ) ? 'checked="checked" ' : '') . '>
		<label for="dhtml_off" class="t">
		<img alt="' . $this->l ( 'Disabled' ) . '" title="' . $this->l ( 'Disabled' ) . '" src="../img/admin/disabled.gif">
		</label>
		
		<p class="clear">' . $this->l ( 'Enable or Disable Dropbox Connect' ) . '.</p>
		</div>';
		
		$_html .= '<script type="text/javascript">
		function enableOrDisableDropbox(id)
		{
		if(id==0){
		$("#block-dropbox-settings").hide(200);
		} else {
		$("#block-dropbox-settings").show(200);
		}
		 
		}
		</script>';
		
		$_html .= '<div id="block-dropbox-settings" ' . (Configuration::get ( $this->name . 'db_on' ) == 1 ? 'style="display:block"' : 'style="display:none"') . '>';
		
		// changes OAuth 2.0
		
		$_html .= '<label>' . $this->l ( 'Dropbox API Key' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="dbci"  style="width:400px"
		value="' . Tools::getValue ( 'dbci', Configuration::get ( $this->name . 'dbci' ) ) . '">
		
		</div>';
		
		
		$_html .= '<label>' . $this->l ( 'Dropbox API Secret' ) . ':</label>
		
		<div class="margin-form">
		<input type="text" name="dbcs"  style="width:400px"
		value="' . Tools::getValue ( 'dbcs', Configuration::get ( $this->name . 'dbcs' ) ) . '">
		
		</div>';
		
		
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_positionConnect ( array ('title' => $this->l ( 'Position Dropbox Connect Button' ), 'prefix' => 'db' ) );
		
		$_html .= '<br/><br/>';
		
		$_html .= $this->_imagesConnects ( array ('title_large' => $this->l ( 'Dropbox Connect Large Image' ), 'title_medium' => $this->l ( 'Dropbox Connect Medium Image' ),
				'title_small' => $this->l ( 'Dropbox Connect Small Image' ), 'title_very_small' => $this->l ( 'Dropbox Connect Very Small Image' ),
				'prefix_short' => 'db', 'prefix' => 'dropbox' ) );
		
		$_html .= '</div>';
		
		$_html .= $this->_updateButton ( array ('name' => 'dropbox', 'prefix' => 'db' ) );
		
		$_html .= '</fieldset>';
		
		$_html .= '</form>';
		
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

        $data_prefixes = $this->getConnetsArrayPrefix();
        $prefix_full = $data_prefixes[$prefix]['prefix'];

    	
    	$_html = '';
    	
    	$_html .= '<label>'.$title.':</label>
    	 
    	';

        include_once(_PS_MODULE_DIR_.$this->name.'/classes/facebookhelp.class.php');
        $obj = new facebookhelp();
        $data_img = $obj->getImages(array('admin'=>1));

    	$hooks_array = array(
    						'top'=>$this->l('Top'),
    					    'rightcolumn'=>$this->l('Right Column'),
    						'leftcolumn'=>$this->l('Left Column'),
    			   			'footer'=>$this->l('Footer'),
                            'beforeauthpage'=>$this->l('Before Login Form on the Authentication page'),
    						'authpage'=>$this->l('Authentication page'),
    						'welcome'=>$this->l('Near with text Welcome'),
    						);
    	if(version_compare(_PS_VERSION_, '1.5', '>')){
    		$hooks_array['chook'] = $this->l('Custom Hook');
    	}
    			
    	$_html .= '<style>
    				.choose_hooks td{font-size:13px;padding:5px 0}
    				.choose_hooks td.title_hook{width:20%}
    			</style>
    	        		
    	        		<div class="margin-form choose_hooks">';
    		    			
    		
    				$_html .= '<table style="width:60%;">';


                    //$hooks_array_images  = $hooks_array;
    				foreach($hooks_array as $k=>$item){
    					
    					$_html .= '<tr>';
    						$_html .= '<td class="title_hook">'.$item.'</td>';
    						
    						$current_item = Configuration::get($this->name.'_'.$k.$prefix);
    						
    						$_html .= '<td class="title_hook">
    									<input type="checkbox" name="'.$k. $prefix.'" '. ($current_item == $k.$prefix ? 'checked="checked"' : '').' value="'.$k.$prefix.'"/>
    								  </td>';
    						
    						$si_img = Configuration::get($this->name.'sz'.$k.$prefix);
    						$_html .= '<td class="title_hook">
    									<select name="sz'.$k. $prefix.'" id="sz'.$k. $prefix.'">
	    									<option value="l'.$k.$prefix.'" '. ($si_img == 'l'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Large Image').'</option>
	    									<option value="ls'.$k.$prefix.'" '. ($si_img == 'ls'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Medium Image').'</option>
	    									<option value="s'.$k.$prefix.'" '. ($si_img == 's'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Small Image').'</option>
	    									<option value="sm'.$k.$prefix.'" '. ($si_img == 'sm'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Very Small Image').'</option>
	    									<option value="bl'.$k.$prefix.'" '. ($si_img == 'bl'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Bootstrap Large Icon').'</option>
	    									<option value="bls'.$k.$prefix.'" '. ($si_img == 'bls'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Bootstrap Medium Icon').'</option>
	    									<option value="bs'.$k.$prefix.'" '. ($si_img == 'bs'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Bootstrap Small Icon').'</option>
	    									<option value="bsm'.$k.$prefix.'" '. ($si_img == 'bsm'.$k.$prefix ? 'selected="selected"' : '').'>'.$this->l('Bootstrap Very Small Icon').'</option>
    									</select>

    									<script type="text/javascript">
                                            $(document).ready(function() {

                                            $(\'#sz'.$k. $prefix.'\').change(function() {
                                                //alert($(this).val());

                                                $(\'#preview-l'.$k.$prefix.'\').hide();
                                                $(\'#preview-ls'.$k.$prefix.'\').hide();
                                                $(\'#preview-s'.$k.$prefix.'\').hide();
                                                $(\'#preview-sm'.$k.$prefix.'\').hide();
                                                $(\'#preview-bl'.$k.$prefix.'\').hide();
                                                $(\'#preview-bls'.$k.$prefix.'\').hide();
                                                $(\'#preview-bs'.$k.$prefix.'\').hide();
                                                $(\'#preview-bsm'.$k.$prefix.'\').hide();


                                                $(\'#preview-\'+$(this).val()).show();
                                            });

                                            });

                                        </script>

    									</td>';


                            $_html .= '
                            <td><div class="fbloginblock-connects">';



                                $_k = $k;
                                $si_img = Configuration::get($this->name.'sz'.$_k.$prefix);

                                            $_html .= '<!-- large image -->

                                            <img src="'.$data_img[$prefix_full].'"
                                                 id="preview-l'.$_k.$prefix.'"
                                                 style="display: '.(($si_img == 'l'.$_k.$prefix)?'block':'none').'"
                                                  />
                                            <!-- large image -->
                                            ';

                                        $_html .= '
                                            <!-- large_small image -->

                                             <img src="'.$data_img[$prefix_full.'large_small'].'"
                                                 id="preview-ls'.$_k.$prefix.'"
                                                 style="display: '.(($si_img == 'ls'.$_k.$prefix)?'block':'none').'"
                                                  />
                                            <!-- large_small image -->';


                                        $_html .= '
                                            <!-- small image -->

                                             <img src="'.$data_img[$prefix_full.'small'].'"
                                                 id="preview-s'.$_k.$prefix.'"
                                                 style="display: '.(($si_img == 's'.$_k.$prefix)?'block':'none').'"
                                                  />
                                            <!-- small image -->';


                                        $_html .= '
                                            <!-- micro_small image -->

                                             <img src="'.$data_img[$prefix_full.'micro_small'].'"
                                                 id="preview-sm'.$_k.$prefix.'"
                                                 style="display: '.(($si_img == 'sm'.$_k.$prefix)?'block':'none').'"
                                                  />
                                            <!-- micro_small image -->';


                                    $_html .= '<!-- bootstrap large image -->
                                            <a href="javascript:void(0)"
                                               id="preview-bl'.$_k.$prefix.'"
                                               style="display: '.(($si_img == 'bl'.$_k.$prefix)?'block':'none').'"
                                               class="'.$prefix_full.' custom-social-button-all custom-social-button-1"
                                               title="'.Tools::ucfirst($prefix_full).'"
                                                    ><i class="fa fa-'.$prefix_full.'"
                                                        >&nbsp;'.Tools::ucfirst($prefix_full).'</i></a>
                                            <!-- bootstrap large image -->';


                                    $_html .= '<!-- bootstrap medium large image -->
                                            <a href="javascript:void(0)"
                                               id="preview-bls'.$_k.$prefix.'"
                                               style="display: '.(($si_img == 'bls'.$_k.$prefix)?'block':'none').'"
                                               class="'.$prefix_full.' custom-social-button-all custom-social-button-2"
                                               title="'.Tools::ucfirst($prefix_full).'"
                                                    ><i class="fa fa-'.$prefix_full.'"
                                                        ></i></a>
                                            <!-- bootstrap medium large image -->';


                                    $_html .= '<!-- bootstrap small image -->
                                            <a href="javascript:void(0)"
                                               id="preview-bs'.$_k.$prefix.'"
                                               style="display: '.(($si_img == 'bs'.$_k.$prefix)?'block':'none').'"
                                               class="'.$prefix_full.' custom-social-button-all custom-social-button-3"
                                               title="'.Tools::ucfirst($prefix_full).'"
                                                    ><i class="fa fa-'.$prefix_full.'"
                                                        >&nbsp;'.Tools::ucfirst($prefix_full).'</i></a>
                                            <!-- bootstrap small image -->';

                                 $_html .= '<!-- bootstrap very small image -->
                                            <a href="javascript:void(0)"
                                               id="preview-bsm'.$_k.$prefix.'"
                                               style="display: '.(($si_img == 'bsm'.$_k.$prefix)?'block':'none').'"
                                               class="'.$prefix_full.' custom-social-button-all custom-social-button-4"
                                               title="'.Tools::ucfirst($prefix_full).'"
                                                    ><i class="fa fa-'.$prefix_full.'"
                                                        ></i></a>
                                            <!-- bootstrap very small image -->';





                                        $_html .= '</div></td>';
    						
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
    
    include_once(_PS_MODULE_DIR_.$this->name.'/classes/facebookhelp.class.php');
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
     
    $_html .= '<p>'.$this->l('Allow formats').' *.jpg; *.jpeg; *.png; *.gif.</p>';
    $_html .= '</div>';
    
    return $_html;
    
    }
    
    
    private function _connectSettings($data_in){
    	$prefix = $data_in['prefix'];
    	$prefix_full = $data_in['prefix_full'];
    	$title_item = $data_in['title_item'];
    	
    	
    	$tip = isset($data_in['tip'])?$data_in['tip']:'';
    	//$tip1 = isset($data_in['tip1'])?$data_in['tip1']:'';
    	 
    	
    	$settings_connect = isset($data_in['settings'])?$data_in['settings']:array();
    	$prefix_full_icon = $prefix_full;
    	switch($prefix_full){
    		case 'amazon':
    			$prefix_full_icon = 'at';
    		break;
    	}
    	
    	############# positions #############
    	
    	$selected_data = array();
    	$hooks_array_pre = array(
    	 		'top'=>$this->l('Top'),
    	 		'rightcolumn'=>$this->l('Right Column'),
    	 		'leftcolumn'=>$this->l('Left Column'),
    	 		'footer'=>$this->l('Footer'),

                'beforeauthpage'=>$this->l('Before Login Form on the Authentication page'),
                'authpage'=>$this->l('After Login Form on the Authentication page'),

    	 		'welcome'=>$this->l('Near with text Welcome'),
    			'chook'=>$this->l('Custom Hook'),
    	 );
    	
    	 
    	foreach($hooks_array_pre as $k=>$val){
    	$selected_data['position'][$k.$prefix] = Configuration::get($this->name.'_'.$k.$prefix);
    	$selected_data['image'][$k.$prefix] = Configuration::get($this->name.'sz'.$k.$prefix);
    	}
    	############# positions #############
    	
    	//var_dump($selected_data);
    	
    	############# positions #############
    	include_once(_PS_MODULE_DIR_.$this->name.'/classes/facebookhelp.class.php');
    	$obj = new facebookhelp();
    	$data_img = $obj->getImages(array('admin'=>1));
    	############# positions #############


        $data_url = $this->getSEOURLs();
        $ajax_url = $data_url['ajax_url'];
    	   
    	
    	
    	$fields_form = array(
    			'form' => array(
    					'legend' => array(
    							'title' => $title_item.' '.$this->l('Settings'),
    							'icon' => 'fa fa-'.$prefix_full_icon.' fa-lg'
    					),
    					'input' => array(
    							
    							array(
    									'type' => 'switch',
    									'label' => $this->l('Enable or Disable').' '.$title_item.' '. $this->l('Connect'),
    									'name' => $prefix.'_on',
    									'values' => array(
    											array(
    													'id' => 'active_on',
    													'value' => 1,
    													'label' => $this->l('Yes')
    											),
    											array(
    													'id' => 'active_off',
    													'value' => 0,
    													'label' => $this->l('No')
    											)
    									),
    							),
    							
    							array(
    									'type' => 'cms_pages',
    									'label' => $title_item.' '.$this->l('Positions and Images'),
    									'name' => 'posimg',
    									'values'=>$this->getVarCustom(array('prefix'=>$prefix)),
    									'selected_data'=>$selected_data,

                                        'img_large' => array('' => array(
                                                                    $prefix => array($prefix_full=>array($data_img[$prefix_full],
                                                                                                            $data_img[$prefix_full.'_block']
                                                                                                        )
                                                                                    )
                                                                )
                                                        ),
                                        'img_large_small' => array('large_small' => array(
								    									$prefix => array($prefix_full=>array($data_img[$prefix_full.'large_small'],
								    																		 $data_img[$prefix_full.'_blocklarge_small']
								    																		))
								    									)),
                                        'img_small' => array('small' => array(
                                                                            $prefix => array($prefix_full=>array($data_img[$prefix_full.'small'],
                                                                                                                $data_img[$prefix_full.'_blocksmall']
                                                                                                            ))
                                                                             )),
                                        'img_micro_small' => array('micro_small' => array(
                                                                                            $prefix =>  array($prefix_full=>array($data_img[$prefix_full.'micro_small'],
                                                                                                                                $data_img[$prefix_full.'_blockmicro_small']
                                                                                                                                )
                                                                                                            )
                                                                                        )
                                                                    ),
                                        'prefix_full'=> $prefix_full,
    							),
    								
    							array(
    									'type' => 'file_img',
    									'label' => $title_item.' '.$this->l('Connect Large Image'),
    									'id'=> $prefix_full,
    									'name' => array('' => array(
    																$prefix => array($prefix_full=>array($data_img[$prefix_full],
    																									 $data_img[$prefix_full.'_block']
    																									))
    																)
    													),
    									'desc' => $title_item.' '.$this->l('Connect Large Image. Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                                        'ajax_url' => $ajax_url,
                                        'token_custom'=>Tools::getAdminTokenLite('AdminFbloginblockajax'),
    							),
    							 array(
    									'type' => 'file_img',
    									'label' => $title_item.' '.$this->l('Connect Medium Image'),
    							 		'id'=> $prefix_full,
    									'name' => array('large_small' => array(
								    									$prefix => array($prefix_full=>array($data_img[$prefix_full.'large_small'], 
								    																		 $data_img[$prefix_full.'_blocklarge_small']
								    																		))
								    									)),
    									'desc' => $title_item.' '.$this->l('Connect Medium Image. Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                                        'ajax_url' => $ajax_url,
                                        'token_custom'=>Tools::getAdminTokenLite('AdminFbloginblockajax'),
    							),
    							array(
    									'type' => 'file_img',
    									'label' => $title_item.' '.$this->l('Connect Small Image'),
    									'id'=> $prefix_full,
    									'name' => array('small' => array(
								    									$prefix => array($prefix_full=>array($data_img[$prefix_full.'small'],
								    																		 $data_img[$prefix_full.'_blocksmall']
								    																		))
								    									)),
    									'desc' => $title_item.' '.$this->l('Connect Small Image. Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                                        'ajax_url' => $ajax_url,
                                        'token_custom'=>Tools::getAdminTokenLite('AdminFbloginblockajax'),
    							),
    							array(
    									'type' => 'file_img',
    									'label' => $title_item.' '.$this->l('Connect Very Small Image'),
    									'id'=> $prefix_full,
    									'name' => array('micro_small' => array(
    														$prefix =>  array($prefix_full=>array($data_img[$prefix_full.'micro_small'],
    																							  $data_img[$prefix_full.'_blockmicro_small']))
    													)),
    									'desc' => $title_item.' '.$this->l('Connect Very Small Image. Allow formats *.jpg; *.jpeg; *.png; *.gif.'),
                                        'ajax_url' => $ajax_url,
                                        'token_custom'=>Tools::getAdminTokenLite('AdminFbloginblockajax'),
    							),
    							
    							
    							
    							
    							
    					),
    					
    					'submit' => array(
    							'title' => $this->l('Save'),
    					)
    			),
    	);
    	
    	

    	#### add configuration fields for each social connect ####
    	
    	$p=0;
    	$data = $fields_form['form']['input'];
    	$fields_form['form']['input'] = array();
    	foreach($data as $value){
    		if($p==0){
    			array_push($fields_form['form']['input'],$value);
    			
    			foreach($settings_connect as $k_set => $val_set){
    				
    				if(Tools::strlen($tip)>0){
    					$settings_data = array(
	    						'type' => 'text',
	    						'label' => $title_item.' '.$val_set[1],
	    						'name' => $k_set,
    							'desc' =>$tip,
	    				);
    					 
    				} else {
    				
	    				$settings_data = array(
	    						'type' => 'text',
	    						'label' => $title_item.' '.$val_set[1],
	    						'name' => $k_set,
	    				);
	    				
    				}
    				array_push($fields_form['form']['input'],$settings_data);
    			}
    		} else {
    			array_push($fields_form['form']['input'],$value);
    		}
    		
    		$p++;
    	}
    	#### add configuration fields for each social connect ####
    	   
    	
    	$helper = new HelperForm();
    	$helper->show_toolbar = false;
    	$helper->table = $this->table;
    	$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    	$helper->default_form_language = $lang->id;
    	$helper->module = $this;
    	$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    	$helper->identifier = $this->identifier;
    	$helper->submit_action = 'submit'.$prefix;
    	$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    	$helper->token = Tools::getAdminTokenLite('AdminModules');
    	$helper->tpl_vars = array(
    			'uri' => $this->getPathUri(),
    			'fields_value' => $this->getConfigFieldsValues(array('prefix'=>$prefix,'settings_connect'=>$settings_connect)),
    			'languages' => $this->context->controller->getLanguages(),
    			'id_language' => $this->context->language->id
    	);
    	
    
    	
    	
    	$_html = '';
    	$name_help_function = "_".$prefix_full."help";
    	 
    	if(method_exists($this->name,$name_help_function)){
	    	$_html .= '<div class="panel">';
	    	$_html .= '<div class="alert alert-info">'.$this->$name_help_function().'</div>';
	    	$_html .= '</div>';
    	}
    	
    	return $_html.$helper->generateForm(array($fields_form));
    }
    
    
    public function getConfigFieldsValues($data)
    {
    	$prefix = $data['prefix'];
    	$settings_connect = $data['settings_connect'];
    	
    	$data_config = array(
    			$prefix.'_on' => Tools::getValue($this->name.$prefix.'_on', Configuration::get($this->name.$prefix.'_on')),
    	);
    	 
    	
    	if(sizeof($settings_connect)>0){
	    	foreach($settings_connect as $k=>$val){
	    		$data_config[$k] = $val[0];
	    	}
    	}
    	
    	return $data_config;
    }
    
    public function getVarCustom($data){
    	$prefix = $data['prefix'];
    	$hooks_array_pre = array(
    			'top'=>$this->l('Top'),
    			'rightcolumn'=>$this->l('Right Column'),
    			'leftcolumn'=>$this->l('Left Column'),
    			'footer'=>$this->l('Footer'),
    			'beforeauthpage'=>$this->l('Before Login Form on the Authentication page'),
                'authpage'=>$this->l('After Login Form on the Authentication page'),
    			'welcome'=>$this->l('Near with text Welcome'),
    			'chook'=>$this->l('Custom Hook'),
    	);
    	 
    	$hooks_array = array();
    	$i=0;
    	foreach($hooks_array_pre as $k => $v){
    		$hooks_array['position'][$k.$prefix]=$v;
    	
    		$images_pos = array('l'=>$this->l('Large Image'),
                                'ls'=>$this->l('Medium Image'),
                                's'=>$this->l('Small Image'),
                                'sm'=>$this->l('Very Small Image'),
                                'bl'=>$this->l('Bootstrap Large Icon'),
                                'bls'=>$this->l('Bootstrap Medium Icon'),
                                'bs'=>$this->l('Bootstrap Small Icon'),
                                'bsm'=>$this->l('Bootstrap Very Small Icon'),
                                );

    		$tmp_img = array();
    		foreach($images_pos as $image_pos => $value_image_pos){
    			$tmp_img[$image_pos.$k.$prefix] = $value_image_pos;
    			 
    		}
    		$hooks_array['image'][$k.$prefix]=$tmp_img;
    		$i++;
    	}
    	return $hooks_array;
    }


    private function _enabledisable13_14($data_out){



            $_html = '';


            $_html .= '
        <form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" enctype="multipart/form-data" method="post" >';

            $_html .= '<fieldset>
					<legend><img src="../modules/' . $this->name . '/views/img/settings_f.png" />' . $this->l('Facebook Settings') . '</legend>

					';

        //$fields_form_array = array();
        foreach($data_out as $k=> $data_in) {

            $prefix = $data_in['prefix'];
            //$prefix_full = $data_in['prefix_full'];
            $title_item = $data_in['title_item'];

           /* $prefix_full_icon = $prefix_full;
            switch ($prefix_full) {
                case 'amazon':
                    $prefix_full_icon = 'at';
                    break;
                case 'microsoft':
                    $prefix_full_icon = 'windows';
                    break;
            }*/



            $_html .= '<label>' . $this->l('Enable or Disable') . ' ' . $title_item . ' ' . $this->l('Connect') . ':</label>
				<div class="margin-form">

					<input type="radio" value="1" id="text_list_on" name="' . $prefix . $prefix. '_on"
							' . (Configuration::get($this->name . $prefix .'_on') ? 'checked="checked" ' : '') . '>
					<label for="dhtml_on" class="t">
						<img alt="' . $this->l('Enabled') . '" title="' . $this->l('Enabled') . '" src="../img/admin/enabled.gif">
					</label>

					<input type="radio" value="0" id="text_list_off" name="' . $prefix . $prefix. '_on"
						   ' . (!Configuration::get($this->name . $prefix  . '_on') ? 'checked="checked" ' : '') . '>
					<label for="dhtml_off" class="t">
						<img alt="' . $this->l('Disabled') . '" title="' . $this->l('Disabled') . '" src="../img/admin/disabled.gif">
					</label>

					<p class="clear">' . $this->l('Enable or Disable') . ' ' . $title_item . ' ' . $this->l('Connect') . '.</p>
				</div>';




        }

        $_html .= $this->_updateButton(array('name' => '', 'prefix' => 'enabledisable'));

        $_html .=	'</fieldset>';

        $_html .= '</form>';

        return $_html;
    }

    private function _enabledisable16($data_out){

        $fields_form_array = array();
        foreach($data_out as $k=> $data_in) {

            $prefix = $data_in['prefix'];
            $prefix_full = $data_in['prefix_full'];
            $title_item = $data_in['title_item'];

            $prefix_full_icon = $prefix_full;
            switch($prefix_full){
                case 'amazon':
                    $prefix_full_icon = 'at';
                    break;
                case 'microsoft':
                    $prefix_full_icon = 'windows';
                    break;
            }



            $fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $title_item,
                        'icon' => 'fa fa-' . $prefix_full_icon . ' fa-lg'
                    ),
                    'input' => array(

                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable or Disable') . ' ' . $title_item . ' ' . $this->l('Connect'),
                            'name' => $prefix.$prefix . '_on',
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                )
                            ),
                        ),


                    ),

                ),
            );

            $fields_form_array[] = $fields_form;

        }


        $fields_form1 = array(
            'form' => array(


                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $fields_form_array[] = $fields_form1;


        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitenabledisable';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValuesEnableDisable(array('data_out'=>$data_out)),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );


        //echo "<pre>"; var_dump($fields_form_array);exit;

        return $helper->generateForm($fields_form_array);
    }


    public function getConfigFieldsValuesEnableDisable($data)
    {
        $data_out = $data['data_out'];
        $data_config = array();

        foreach($data_out as $k=> $data_in) {
            $prefix = $data_in['prefix'];

            $data_config[$prefix.$prefix . '_on'] = Configuration::get($this->name . $prefix . '_on');


        }

        return $data_config;
    }
    
    private function _basicsettings16(){

        $cookie = $this->context->cookie;
        $id_current_lang = $cookie->id_lang;
        $groups = Group::getGroups($id_current_lang, true);
        $total_groups = count($groups);

        $groups_customer = array();
        for ($i = 0; $i < $total_groups; $i++) {
            $group = new Group($groups[$i]['id_group']);

            $groups_customer[$i] = array();
            $groups_customer[$i]['id'] = $group->id;
            $groups_customer[$i]['name'] = $group->name[$id_current_lang];
        }


    	$fields_form = array(
						    	'form' => array(
						    				'legend' => array(
						    									'title' => $this->l('Basic Settings'),
						    									'icon' => 'fa fa-cogs fa-lg'
						    									),
						    	'input' => array(
						    			
						    			array(
						    					'type' => 'select',
						    					'label' => $this->l('Select your default customer group'),
						    					'name' => 'defaultgroup',
						    					'hint' => $this->l('This will use the default group once each customer is creating his own account through a social connector'),
						    					//'desc' => $this->l('This will use the default group once each customer is creating his own account through a social connector'),	
						    					'options' => array(
						    							'query' => /*array(
						    									array(
						    											'id' => '1',
						    											'name' => $this->l('Visitor')),
						    			
						    									array(
						    											'id' => '2',
						    											'name' => $this->l('Guest'),
						    									),
						    									array(
						    											'id' => '3',
						    											'name' => $this->l('Customer'),
						    									),
                                                        ),*/
                                                            $groups_customer,
						    							'id' => 'id',
						    							'name' => 'name'
						    					)
						    			),
						    				
						    			array(
						    					'type' => 'text',
						    					'label' => $this->l('Text before Social logins'),
						    					'name' => 'authp',
						    					'lang' => true,
						    					'hint' => $this->l('Text before Social logins'),
						    					'desc' => $this->l('Text before Social logins')
						    			),
						    			
						    			array(
						    					'type' => 'checkbox_custom',
						    					'label' => $this->l('Position Text before Social logins'),
						    					'name' => 'pos',
						    					//'desc' => $this->l('Position Text before Social logins'),
						    					'hint' => $this->l('Position Text before Social logins'),
						    					'values' => array(
						    							'query' => array(
						    									array(
						    											'id' => 'toptxt',
						    											'name' => $this->l('Top'),
						    											'val' => 'toptxt'
						    									),
						    									array(
						    											'id' => 'authpagetxt',
						    											'name' => $this->l('Authentication page'),
						    											'val' => 'authpagetxt'
						    									),
						    									array(
						    											'id' => 'footertxt',
						    											'name' => $this->l('Footer'),
						    											'val' => 'footertxt'
						    									),
						    							),
						    							'id' => 'id',
						    							'name' => 'name'
						    					),
						    					
						    			
						    			),
						    				
						    				
						    			array(
						    					'type' => 'switch',
						    					'label' => $this->l('Show information block on the account page?'),
						    					'name' => 'iauth',
						    					//'desc' => $this->l('Show information block on the account page'),
						    					'hint' => $this->l('Show information block on the account page'),
						    					'values' => array(
						    							array(
						    									'id' => 'active_on',
						    									'value' => 1,
						    									'label' => $this->l('Yes')
						    							),
						    							array(
						    									'id' => 'active_off',
						    									'value' => 0,
						    									'label' => $this->l('No')
						    							)
						    					),
						    			),

						    			array(
						    					'type' => 'text',
						    					'label' => $this->l('Text in the information block on the account page'),
						    					'name' => 'txtauthp',
						    					'lang' => true,
						    					'hint' => $this->l('Text in the information block on the account page'),
						    					//'desc' => $this->l('Text in the information block on the account page')
						    			),



                                        array(
                                            'type' => 'checkbox_custom',
                                            'label' => $this->l('Position Your account block'),
                                            'name' => 'pos',
                                            'hint' => $this->l('Position Your account block'),
                                            'values' => array(
                                                'query' => array(
                                                    array(
                                                        'id' => 'lcblock',
                                                        'name' => $this->l('Left Column'),
                                                        'val' => 'lcblock'
                                                    ),
                                                    array(
                                                        'id' => 'rcblock',
                                                        'name' => $this->l('Right Column'),
                                                        'val' => 'rcblock'
                                                    ),
                                                ),
                                                'id' => 'id',
                                                'name' => 'name'
                                            ),


                                        ),



                                    array(
						    					'type' => 'radio',
						    					'label' => $this->l('Redirect customer after registration'),
						    					'name' => 'redir',
                                                // 'desc' => $this->l('Reccomended select "Redirect to My Account page" if in the admin panel -> Preferences -> General:  "Enable SSL" = "YES" and "Enable SSL on all pages" = NO'),
						    					'hint' => $this->l('Redirect customer after registration'),
						    					'values' => array(
						    									array(
						    											'id' => 'mpage',
						    											'label' => $this->l('Redirect to My Account page'),
						    											'value' => '1'
						    									),
						    									array(
						    											'id' => 'rpage',
						    											'label' => $this->l('Just reload page'),
						    											'value' => '2'
						    									),
						    									
						    							),
						    					 
						    					'is_bool'   => true,
						    			),


                                    array(
                                        'type' => 'switch',
                                        'label' => $this->l('Enable or Disable Social Account Linking functional'),
                                        'name' => 'is_soc_link',
                                        //'desc' => $this->l('Enable or Disable Social Account Linking functional'),
                                        'hint' => $this->l('Enable or Disable Social Account Linking functional'),
                                        'values' => array(
                                            array(
                                                'id' => 'active_on',
                                                'value' => 1,
                                                'label' => $this->l('Yes')
                                            ),
                                            array(
                                                'id' => 'active_off',
                                                'value' => 0,
                                                'label' => $this->l('No')
                                            )
                                        ),
                                    ),
						    												
						  		),
						    										
    									'submit' => array(
    											'title' => $this->l('Save'),
    							)
    					),
    			);
    			 
    			 
    	
    				 
    			$helper = new HelperForm();
    			$helper->show_toolbar = false;
    			$helper->table = $this->table;
    			$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
    			$helper->default_form_language = $lang->id;
    			$helper->module = $this;
    			$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
    			$helper->identifier = $this->identifier;
    			$helper->submit_action = 'submitbasic';
    			$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
    			$helper->token = Tools::getAdminTokenLite('AdminModules');
    			$helper->tpl_vars = array(
    					'uri' => $this->getPathUri(),
    					'fields_value' => $this->getConfigFieldsValuesBasicSettings(),
    					'languages' => $this->context->controller->getLanguages(),
    					'id_language' => $this->context->language->id
    			);
    			 
    	
    			 
    			 
    	$_html = '';
    	
    	return $_html.$helper->generateForm(array($fields_form));
    }
    
    public function getConfigFieldsValuesBasicSettings(){
    	
    	
    
    	$languages = Language::getLanguages(false);
    	$fields_authp = array();
    	$fields_txtauthp = array();
    	
    	foreach ($languages as $lang)
    	{
    		$fields_authp[$lang['id_lang']] = Configuration::get($this->name.'authp_'.$lang['id_lang']);
    		
    		$fields_txtauthp[$lang['id_lang']] = Configuration::get($this->name.'txtauthp_'.$lang['id_lang']);
    	}
    	
    	$config_array = array(
    							'defaultgroup' => Configuration::get($this->name.'defaultgroup'),
    							
    							'toptxt' =>  Configuration::get($this->name.'_toptxt'),
    							'footertxt' =>  Configuration::get($this->name.'_footertxt'),
    							'authpagetxt' =>  Configuration::get($this->name.'_authpagetxt'),
    			
    							'redir' => Configuration::get('redirpage'),
    			
    							'iauth' => Configuration::get($this->name.'iauth'),
    			
    							'authp' => $fields_authp,
    			
    							'txtauthp' => $fields_txtauthp,

                                'lcblock' =>  Configuration::get($this->name.'_lcblock'),
                                'rcblock' =>  Configuration::get($this->name.'_rcblock'),

                                'is_soc_link' => (int)Configuration::get($this->name.'is_soc_link'),
    							
    						 );
    	
    	return $config_array;
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
							id="defaultgroup">';

        if(version_compare(_PS_VERSION_, '1.5', '>')){

            $cookie = $this->context->cookie;
            $id_current_lang = $cookie->id_lang;
            $groups = Group::getGroups($id_current_lang, true);
            $total_groups = count($groups);

            for ($i = 0; $i < $total_groups; $i++) {
                $group = new Group($groups[$i]['id_group']);

                $groups_customer_id = $group->id;
                $groups_customer_name = $group->name[$id_current_lang];

                $_html .= '<option '.((Configuration::get($this->name.'defaultgroup') == $groups_customer_id) ? 'selected="selected" ' : '').'
                               value="'.$groups_customer_id.'">'.$groups_customer_name.'</option>';
            }

            /*$_html .= '<option '.((Tools::getValue('defaultgroup', Configuration::get($this->name.'defaultgroup')) == '1') ? 'selected="selected" ' : '').' value="1">'.$this->l('Visitor').'</option>
                            <option '.((Tools::getValue('defaultgroup', Configuration::get($this->name.'defaultgroup')) == '2') ? 'selected="selected" ' : '').' value="2">'.$this->l('Guest').'</option>
                            <option '.((Tools::getValue('defaultgroup', Configuration::get($this->name.'defaultgroup')) == '3') ? 'selected="selected" ' : '').' value="3">'.$this->l('Customer').'</option>';*/
        } else {
    		$_html .= '<option '.((Configuration::get($this->name.'defaultgroup') == '1') ? 'selected="selected" ' : '').' value="1">'.$this->l('Default').'</option>
    		';
    		 
    	}
		$_html .= '</select>
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
    	
    	$_html .= '
		<style>
		.choose_hooks input {
			margin-bottom: 10px
		}
		</style>
		
		<div class="margin-form choose_hooks">
			<table style="width: 80%;">
				<tr>
					<td style="width: 33%; font-size: 12px">'.$this->l('Footer').'</td>
					<td style="width: 33%; font-size: 12px">'.$this->l('Authentication page').'</td>
					<td style="width: 33%; font-size: 12px">'.$this->l('Top').'</td>
				</tr>
				<tr>
					<td><input type="checkbox" name="footer'. $prefix.'" '. ($footer == 'footer'.$prefix ? 'checked="checked"' : '').'
							   value="footer'. $prefix.'" />
					</td>
					<td><input type="checkbox" name="authpage'. $prefix.'" '. ($authpage == 'authpage'.$prefix ? 'checked="checked"' : '').'
							   value="authpage'. $prefix.'" />
					</td>
					<td><input type="checkbox" name="top'. $prefix.'" '. ($top == 'top'.$prefix ? 'checked="checked"' : '').'
							   value="top'. $prefix.'" />
					</td>
				</tr>
		
			</table>
		</div>
		
		
		';
    	
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


        // Position Your account block
        $_html .= '<label>'.$this->l('Position Your account block').':</label>

            ';

        $lcblock = Configuration::get($this->name.'_lcblock');
        $rcblock = Configuration::get($this->name.'_rcblock');

        $_html .= '
            <div class="margin-form choose_hooks">
                <table style="width: 80%;">
                    <tr>
                        <td style="width: 33%; font-size: 12px">'.$this->l('Left Column').'</td>
                        <td style="width: 33%; font-size: 12px">'.$this->l('Right Column').'</td>

                    </tr>
                    <tr>
                        <td><input type="checkbox" name="lcblock" '. ($lcblock == 'lcblock' ? 'checked="checked"' : '').'
                                   value="lcblock" />
                        </td>
                        <td><input type="checkbox" name="rcblock" '. ($rcblock == 'rcblock' ? 'checked="checked"' : '').'
                                   value="rcblock" />
                        </td>

                    </tr>

                </table>
            </div>


            ';

        $_html .= '<br/><br/>';

    	
    		
    	$_html .= '<label>'.$this->l('Redirect customer after registration').'</label>
    			
    	<div class="margin-form">
    	
    	<input type="radio" '.(Configuration::get('redirpage') == 1 ? 'checked="checked" ' : '').' value="1" name="redir" />&nbsp;&nbsp;'.$this->l('Redirect to My Account page').'
    	&nbsp;&nbsp;&nbsp;
    	<input type="radio" '.(Configuration::get('redirpage') == 2 ? 'checked="checked" ' : '').' value="2" name="redir">&nbsp;&nbsp;'.$this->l('Just reload page').'


    	</div>';


    $_html .= '<br/><br/>';


    $_html .= '<label>'.$this->l('Enable or Disable Social Account Linking functional').'?</label>

		    	<div class="margin-form">

			    	<input type="radio" value="1" id="text_list_on" name="is_soc_link"
			    	'.(Configuration::get($this->name.'is_soc_link') ? 'checked="checked" ' : '').'>
			    	<label for="dhtml_on" class="t">
			    	<img alt="'.$this->l('Enabled').'" title="'.$this->l('Enabled').'" src="../img/admin/enabled.gif">
			    	</label>

			    	<input type="radio" value="0" id="text_list_off" name="is_soc_link"
			    	'.(!Configuration::get($this->name.'is_soc_link') ? 'checked="checked" ' : '').'>
			    	<label for="dhtml_off" class="t">
			    	<img alt="'.$this->l('Disabled').'" title="'.$this->l('Disabled').'" src="../img/admin/disabled.gif">
			    	</label>

		    	<p class="clear">'.$this->l('Enable or Disable Social Account Linking functional').'.</p>
    	</div>';
    	
    	
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


        $_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/views/css/font-awesome.min.css" type="text/css" />';
        $_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/views/css/'.$this->name.'.css" type="text/css" />';

		$_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/javascript.js"></script>';
    	
		$_html .= '<link rel="stylesheet" href="../modules/'.$this->name.'/views/css/custom_menu.css" type="text/css" />';
    	$_html .= '<script type="text/javascript" src="../modules/'.$this->name.'/views/js/custom_menu.js"></script>';
    
    	$_html .= '<style type="text/css">';
    	$_html .= '.update-button{border: 1px solid #EBEDF4;}';
        $_html .= '</style>';
    	
    	
    	
    	return $_html;
	}
	
	public function translateCustom(){
		return array(
                    'billing_address'=>$this->l('Delivery Address'),
                    'disabled'=>$this->l('Customer has been disabled by admin!'),

                     'meta_title_myaccount' => $this->l('Social account linking'),
                     'meta_description_myaccount' => $this->l('Social account linking'),
                     'meta_keywords_myaccount' => $this->l('Social account linking'),

                    'subject' => $this->l('Welcome!'),
                    );
	}		
	
	

	public function getConnetsArrayPrefix(){



	return array(
				 'f'=>array('prefix'=>'facebook','type'=>1,'link'=>'https://www.facebook.com/settings?tab=applications',
                            'error'=>$this->l('Error: Please fill Facebook App Id and Facebook Secret Key in the module settings'),
                            'key'=>'appid','secret'=>'secret','redirect_url'=>null),

				 't'=>array('prefix'=>'twitter','type'=>2,'link'=>'https://twitter.com/settings/applications',
                            'error'=>$this->l('Error: Please fill Consumer key and Consumer secret in the module settings'),
                            'key'=>'twitterconskey','secret'=>'twitterconssecret','redirect_url'=>null),

                 'a'=>array('prefix'=>'amazon','type'=>24,'link'=>'https://www.amazon.com/ap/adam',
                            'error'=>$this->l('Error: Please fill Amazon Client ID and Amazon Allowed Return URL in the module settings'),
                            'key'=>'aci','secret'=>null,'redirect_url'=>'aru'),

                 'g'=>array('prefix'=>'google','type'=>3,'link'=>'https://plus.google.com/apps',
                            'error'=>$this->l('Error: Please fill Google Client Id and Google Client Secret in the module settings'),
                            'key'=>'oci','secret'=>'ocs','redirect_url'=>'oru'),

                 'pi'=>array('prefix'=>'pinterest','type'=>54,'link'=>'https://es.pinterest.com/settings/',
                             'error'=>$this->l('Error: Please fill Pinterest App ID and Pinterest App secret in the module settings'),
                             'key'=>'pici','secret'=>'pics','redirect_url'=>null),
			
				 'y'=>array('prefix'=>'yahoo','type'=>6,'link'=>'https://api.login.yahoo.com/WSLogin/V1/unlink',
                            'error'=>null, 'key'=>null,'secret'=>null,'redirect_url'=>null),

                 'p'=>array('prefix'=>'paypal','type'=>8,'link'=>'https://www.paypal.com/webapps/auth/identity/myactivity?execution=e1s1',
                            'error'=>$this->l('Error: Please fill Paypal Client ID, Paypal Secret, Callback URL in the module settings!'),
                            'key'=>'clientid','secret'=>'psecret','redirect_url'=>'pcallback'),

			     'l'=>array('prefix'=>'linkedin','type'=>4,'link'=>'https://www.linkedin.com/secure/settings?userAgree',
                            'error'=>$this->l('Error: Please fill LinkedIn API Key and LinkedIn Secret Key in the module settings'),
                            'key'=>'lapikey','secret'=>'lsecret','redirect_url'=>null),

				 'm'=>array('prefix'=>'microsoft','type'=>5,'link'=>'https://account.live.com/consent/Manage',
                            'error'=>$this->l('Error: Please fill Microsoft Live Client ID and Microsoft Live Client Secret in the module settings'),
                            'key'=>'mclientid','secret'=>'mclientsecret','redirect_url'=>null),

				 'fs'=>array('prefix'=>'foursquare','type'=>20,'link'=>'https://foursquare.com/settings/connections',
                             'error'=>$this->l('Error: Please fill Foursquare Client Id and Foursquare Client Secret in the module settings'),
                             'key'=>'fsci','secret'=>'fscs','redirect_url'=>'fsru'),
			
				 'gi'=>array('prefix'=>'github','type'=>21,'link'=>'https://github.com/settings/applications',
                             'error'=>$this->l('Error: Please fill Github Client Id and Github Client Secret in the module settings'),
                             'key'=>'gici','secret'=>'gics','redirect_url'=>'giru'),

				 'd'=>array('prefix'=>'disqus','type'=>22,'link'=>'https://disqus.com/home/settings/apps/',
                            'error'=>$this->l('Error: Please fill Disqus API Key and Disqus API Secret in the module settings'),
                            'key'=>'dci','secret'=>'dcs','redirect_url'=>'dru'),

				 'db'=>array('prefix'=>'dropbox','type'=>50,'link'=>'https://www.dropbox.com/account#security',
                             'error'=>$this->l('Error: Please fill Dropbox API Key and Dropbox API Secret in the module settings'),
                             'key'=>'dbci','secret'=>'dbcs','redirect_url'=>null),

				 /*'s'=>array('prefix'=>'scoop','type'=>51,'link'=>'',
				              'error'=>$this->l('Error: Please fill Scoop.it API Key and Scoop.it API Secret in the module settings'),
				              'key'=>'sci','secret'=>'scs','redirect_url'=>null),
				 */

				 'w'=>array('prefix'=>'wordpress','type'=>52,'link'=>'https://wordpress.com/me/security/connected-applications',
                            'error'=>$this->l('Error: Please fill Wordpress Client ID and Wordpress Client Secret in the module settings'),
                            'key'=>'wci','secret'=>'wcs','redirect_url'=>null),

				 'tu'=>array('prefix'=>'tumblr','type'=>53,'link'=>'https://www.tumblr.com/settings/apps',
                             'error'=>$this->l('Error: Please fill Tumblr Consumer Key and Tumblr Secret Key in the module settings'),
                              'key'=>'tuci','secret'=>'tucs','redirect_url'=>null),
			
				 /*'i'=>array('prefix'=>'instagram','type'=>7,'link'=>'https://instagram.com/accounts/manage_access/',
				              'error'=>$this->l('Error: Please fill Instagram Client Id and Instagram Client Secret in the module settings'),
				              'key'=>'ici','secret'=>'ics','redirect_url'=>'iru'),*/


                 /*'o'=>array('prefix'=>'oklass','type'=>55,'link'=>'',
                              'error'=>$this->l('Error: Please fill Odnoklassniki Application ID, Odnoklassniki Application Public Key, Odnoklassniki Application Secret Key in the module settings'),
                              'key'=>'odci','secret'=>'odpc','redirect_url'=>'odcs'),
                 */

				 /*'ma'=>array('prefix'=>'mailru','type'=>56,'link'=>'',
				               'error'=>$this->l('Error: Please fill Mail.ru ID and Mail.ru Secret Key in the module settings'),
				               'key'=>'maci','secret'=>'macs','redirect_url'=>null),
				 */

				 /*'ya'=>array('prefix'=>'yandex','type'=>57,'link'=>'',
				               'error'=>$this->l('Error: Please fill Yandex Client ID and Yandex Client Secret in the module settings'),
				               'key'=>'yaci','secret'=>'yacs','redirect_url'=>null),
				 */

                 'v'=>array('prefix'=>'vkontakte','type'=>58,'link'=>'https://vk.com/settings?act=apps',
                            'error'=>$this->l('Error: Please fill Vkontakte ID Key and Vkontakte Secret Key in the module settings'),
                            'key'=>'vci','secret'=>'vcs','redirect_url'=>null),
				);	
	}
	
	public function getRedirectURL($data = null){
		
		$typelogin = $data['typelogin'];
		
		if($typelogin == 'google' || $typelogin == 'yahoo'){
			$typelogin = 'login';
		}
		if($typelogin == 'paypal'){
			$typelogin = 'paypalconnect';
		}

        if($typelogin == 'vkontakte'){
            $typelogin = 'vk';
        }
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$custom_ssl_var = 0;
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
				$custom_ssl_var = 1;
				
				
			if ($custom_ssl_var == 1)
				$_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
			else
				$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
				
		} else {
			$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
		}

        if($this->_is15) {
            $current_shop_id = Shop::getContextShopID();

            if($current_shop_id) {
                $shop_obj = new Shop($current_shop_id);

                $ssl_on = false;
                if ($custom_ssl_var == 1)
                    $ssl_on = true;

                $_http_host = $shop_obj->getBaseURL($ssl_on);


                // only for ps 1.5
                if(version_compare(_PS_VERSION_, '1.6', '<')){
                    $custom_ssl_var = 0;
                    if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                        $custom_ssl_var = 1;

                    if($custom_ssl_var)
                        $_http_host = str_replace("http://","https://",$_http_host);
                }
                // only for ps 1.5

            }

        }

        if(!Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Configuration::get('PS_SSL_ENABLED')){
            $_http_host = str_replace("https://","http://",$_http_host); // this is prevent bug if page have https
            $_http_host = str_replace("http://","https://",$_http_host);
        }


        $is_settings = isset($data['is_settings'])?$data['is_settings']:0;
		if(version_compare(_PS_VERSION_, '1.6', '>') && !$is_settings && $typelogin != 'amazon'){
				
			if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1)
				$redirect_uri = $_http_host.'index.php?fc=module&module='.$this->name.'&controller=spmlogin&typelogin='.$typelogin;
			else
				$redirect_uri = $_http_host.'index.php?fc=module&module='.$this->name.'&controller=spmlogin&typelogin='.$typelogin;
			
		}else{

            if(version_compare(_PS_VERSION_, '1.7', '<')) {


                $redirect_uri = $_http_host . 'modules/' . $this->name . '/' . $typelogin . '.php';
            } else {

                $cookie = $this->context->cookie;
                $id_lang = (int)$cookie->id_lang;
                $id_shop = $this->getIdShop();

                $link = new Link();
                $redirect_uri = $link->getModuleLink($this->name, $typelogin, array(), $custom_ssl_var, $id_lang, $id_shop);

                $lang_iso = $this->getLangISO();

                if(Tools::strlen($lang_iso)>0)
                    $redirect_uri = str_replace("/".$lang_iso."/","/",$redirect_uri);

                $lang_str = '&id_lang='.$this->context->language->id;
                $redirect_uri = str_replace($lang_str, '', $redirect_uri);


            }

            //$redirect_uri = $_http_host . 'modules/' . $this->name . '/' . $typelogin . '.php';
		}


        ### only for facebook conenct ###
        if($typelogin == 'facebook'){

            include_once(_PS_MODULE_DIR_.$this->name.'/classes/facebookSdkCustomhelper.class.php');

            $facebookSdkCustomhelper = new facebookSdkCustomhelper();
            $redirect_uri = $facebookSdkCustomhelper->loadSDKLibrary(array('redirect_uri'=>$redirect_uri));

        }
        ### only for facebook conenct ###


		return $redirect_uri;
		
	}

    public function getLangISO(){


        $cookie = $this->context->cookie;
        $id_lang = (int)$cookie->id_lang;

        if($this->getIdShop()) {
            $all_laguages = Language::getLanguages(true,$this->getIdShop());
        } else {
            $all_laguages = Language::getLanguages(true);
        }


        if(sizeof($all_laguages)>1)
            $iso_lang = Language::getIsoById((int)($id_lang));
        else
            $iso_lang = '';

        return $iso_lang;


    }


    public function getHttpost(){
        if(version_compare(_PS_VERSION_, '1.5', '>')){
            $custom_ssl_var = 0;
            if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
                $custom_ssl_var = 1;


            if ($custom_ssl_var == 1)
                $_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
            else
                $_http_host = _PS_BASE_URL_.__PS_BASE_URI__;

        } else {
            $_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
        }
        return $_http_host;
    }

    public function getIdShop(){
        $id_shop = 0;
        if(version_compare(_PS_VERSION_, '1.5', '>'))
            $id_shop = Context::getContext()->shop->id;
        return $id_shop;
    }


    public function getSEOURLs($data = null){
        $cookie = $this->context->cookie;
        $id_lang = isset($data['id_lang'])?(int)$data['id_lang']:(int)$cookie->id_lang;

        $is_ssl = false;
        if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (bool)Configuration::get('PS_SSL_ENABLED'))
            $is_ssl = true;


        $id_shop = $this->getIdShop();


        $link = new Link();
        if(version_compare(_PS_VERSION_, '1.5', '<')) {

            $my_account = "my-account.php";
            $account_url = $this->getHttpost()."modules/".$this->name."/account.php";
            $delete_url = $this->getHttpost()."modules/".$this->name."/delete.php";

            $url_unlink_account = $this->getHttpost()."modules/".$this->name."/account.php?is_linked=2";
            $url_link_account = $this->getHttpost()."modules/".$this->name."/account.php?is_linked=1";

        }else {



            $my_account = $link->getPageLink("my-account", true, $id_lang);
            $account_url = $link->getModuleLink($this->name, 'account',  array(), $is_ssl, $id_lang, $id_shop);
            $delete_url = $link->getModuleLink($this->name, 'delete',  array(), $is_ssl, $id_lang, $id_shop);

            $url_unlink_account = $this->context->link->getModuleLink($this->name, 'account', array('is_linked'=>2), $is_ssl,$id_lang, $id_shop);
            $url_link_account = $this->context->link->getModuleLink($this->name, 'account', array('is_linked'=>1), $is_ssl,$id_lang, $id_shop);
        }

        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            $ajax_url = '../modules/'.$this->name.'/ajax/admin_image.php';
            $update_social_api_email = $this->getHttpost()."modules/".$this->name."/update_social_api_email.php";
            $amazon_url = $this->getHttpost()."modules/".$this->name."/amazon.php";
        } else {
            $amazon_url = $this->getRedirectURL(array('typelogin'=>'amazon','is_settings'=>1));
            $ajax_url = $this->context->link->getAdminLink('AdminFbloginblockajax');
            $update_social_api_email = $link->getModuleLink($this->name, 'updateemail', array(), $is_ssl, $id_lang, $id_shop);
        }


        return array('my_account' => $my_account, 'account_url'=>$account_url,'delete_url'=>$delete_url,
                     'url_unlink_account'=>$url_unlink_account, 'url_link_account'=>$url_link_account,
                     'ajax_url'=>$ajax_url, 'update_social_api_email'=>$update_social_api_email,
                     'amazon_url'=>$amazon_url
                    );
    }
	


    public function setCookieForPrestashop14_13(){
        if(version_compare(_PS_VERSION_, '1.6', '<')){
            ### customer_linked_social_account only if customer is logged in ###
            $cookie_context = $this->context->cookie;
            $is_logged = isset($cookie_context->id_customer)?$cookie_context->id_customer:0;
            $linksocialaccount = Tools::getValue('linksocialaccount');
            if(version_compare(_PS_VERSION_, '1.6', '<') && $linksocialaccount == 1 && $is_logged){
                setcookie_fbloginblock(array('type'=>'linksocialaccount','value'=>$linksocialaccount));
            }
            ### customer_linked_social_account only if customer is logged in ###
        }

    }


    public function renderUserAccount(){
        return $this->display(__FILE__.'/fbloginblock.php', 'views/templates/front/account.tpl');
    }
	
	
	
	
	
	
	
}