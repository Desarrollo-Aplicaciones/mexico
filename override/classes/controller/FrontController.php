<?php

class FrontController extends FrontControllerCore
{
public function init(){

		/*
		 * Globals are DEPRECATED as of version 1.5.
		 * Use the Context to access objects instead.
		 * Example: $this->context->cart
		 */
		global $useSSL, $cookie, $smarty, $cart, $iso, $defaultCountry, $protocol_link, $protocol_content, $link, $css_files, $js_files, $currency;

		if (self::$initialized)
			return;
		self::$initialized = true;

		Controller::init();

		// If current URL use SSL, set it true (used a lot for module redirect)
		if (Tools::usingSecureMode())
			$useSSL = true;

		// For compatibility with globals, DEPRECATED as of version 1.5
		$css_files = $this->css_files;
		$js_files = $this->js_files;

		// If we call a SSL controller without SSL or a non SSL controller with SSL, we redirect with the right protocol
		if (Configuration::get('PS_SSL_ENABLED') && ($_SERVER['REQUEST_METHOD'] != 'POST') && $this->ssl != Tools::usingSecureMode())
		{	
			header('HTTP/1.1 301 Moved Permanently');
			header('Cache-Control: no-cache');
			if ($this->ssl)					
				header('Location: '.Tools::getShopDomainSsl(true).$_SERVER['REQUEST_URI']);
			else						
				header('Location: '.Tools::getShopDomain(true).$_SERVER['REQUEST_URI']);
			exit();
		}
		
		if ($this->ajax)
		{
			$this->display_header = false;
			$this->display_footer = false;
		}

		// if account created with the 2 steps register process, remove 'accoun_created' from cookie
		if (isset($this->context->cookie->account_created))
		{
			$this->context->smarty->assign('account_created', 1);
			unset($this->context->cookie->account_created);
		}

		ob_start();

		// Init cookie language
		// @TODO This method must be moved into switchLanguage
		Tools::setCookieLanguage($this->context->cookie);

		$protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
		$useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
		$protocol_content = ($useSSL) ? 'https://' : 'http://';
		$link = new Link($protocol_link, $protocol_content);
		$this->context->link = $link;

		if ($id_cart = (int)$this->recoverCart()){
			$this->context->cookie->id_cart = (int)$id_cart;
			Tools::redirect('index.php?controller=order&paso=pagos&step=3');
			}

		if ($this->auth && !$this->context->customer->isLogged($this->guestAllowed))
			Tools::redirect('index.php?controller=authentication'.($this->authRedirection ? '&back='.$this->authRedirection : ''));

		/* Theme is missing */
		if (!is_dir(_PS_THEME_DIR_))
			die(sprintf(Tools::displayError('Current theme unavailable "%s". Please check your theme directory name and permissions.'), basename(rtrim(_PS_THEME_DIR_, '/\\'))));

		if (Configuration::get('PS_GEOLOCATION_ENABLED'))
			if (($newDefault = $this->geolocationManagement($this->context->country)) && Validate::isLoadedObject($newDefault))
				$this->context->country = $newDefault;

		$currency = Tools::setCurrency($this->context->cookie);

		if (isset($_GET['logout']) || ($this->context->customer->logged && Customer::isBanned($this->context->customer->id)))
		{
			$this->context->customer->logout();

			Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
		}
		elseif (isset($_GET['mylogout']))
		{
			$origin = explode('/',$_SERVER['HTTP_REFERER']);
			$this->context->customer->mylogout();
			if (preg_match('/^([_a-zA-Z0-9-]*)-[0-9]+.\.html.*$/', end($origin))){
				Tools::redirect(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null);
			}else{
				Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__);
			}
		}

		/* Cart already exists */
		if ((int)$this->context->cookie->id_cart)
		{
			$cart = new Cart($this->context->cookie->id_cart);
			if ($cart->OrderExists())
			{
				unset($this->context->cookie->id_cart, $cart, $this->context->cookie->checkedTOS);
				$this->context->cookie->check_cgv = false;
			}
			/* Delete product of cart, if user can't make an order from his country */
			elseif (intval(Configuration::get('PS_GEOLOCATION_ENABLED')) &&
					!in_array(strtoupper($this->context->cookie->iso_code_country), explode(';', Configuration::get('PS_ALLOWED_COUNTRIES'))) &&
					$cart->nbProducts() && intval(Configuration::get('PS_GEOLOCATION_NA_BEHAVIOR')) != -1 &&
					!FrontController::isInWhitelistForGeolocation() &&
					!in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1')))
				unset($this->context->cookie->id_cart, $cart);
			// update cart values
			elseif ($this->context->cookie->id_customer != $cart->id_customer || $this->context->cookie->id_lang != $cart->id_lang || $currency->id != $cart->id_currency)
			{
				if ($this->context->cookie->id_customer)
					$cart->id_customer = (int)($this->context->cookie->id_customer);
				$cart->id_lang = (int)($this->context->cookie->id_lang);
				$cart->id_currency = (int)$currency->id;
				$cart->update();
			}
			/* Select an address if not set */
			if (isset($cart) && (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0 ||
				!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0) && $this->context->cookie->id_customer)
			{
				$to_update = false;
				if (!isset($cart->id_address_delivery) || $cart->id_address_delivery == 0)
				{
					$to_update = true;
					$cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($cart->id_customer);
				}
				if (!isset($cart->id_address_invoice) || $cart->id_address_invoice == 0)
				{
					$to_update = true;
					$cart->id_address_invoice = (int)Address::getFirstCustomerAddressId($cart->id_customer);
				}
				if ($to_update)
					$cart->update();
			}
		}

		if (!isset($cart) || !$cart->id)
		{
			$cart = new Cart();
			$cart->id_lang = (int)($this->context->cookie->id_lang);
			$cart->id_currency = (int)($this->context->cookie->id_currency);
			$cart->id_guest = (int)($this->context->cookie->id_guest);
			$cart->id_shop_group = (int)$this->context->shop->id_shop_group;
			$cart->id_shop = $this->context->shop->id;
			if ($this->context->cookie->id_customer)
			{
				$cart->id_customer = (int)($this->context->cookie->id_customer);
				$cart->id_address_delivery = (int)(Address::getFirstCustomerAddressId($cart->id_customer));
				$cart->id_address_invoice = $cart->id_address_delivery;
			}
			else
			{
				$cart->id_address_delivery = 0;
				$cart->id_address_invoice = 0;
			}

			// Needed if the merchant want to give a free product to every visitors
			$this->context->cart = $cart;
			CartRule::autoAddToCart($this->context);
		}

		/* get page name to display it in body id */

		// Are we in a payment module
		$module_name = '';
		if (Validate::isModuleName(Tools::getValue('module')))
			$module_name = Tools::getValue('module');

		if (!empty($this->page_name))
			$page_name = $this->page_name;
		elseif (!empty($this->php_self))
			$page_name = $this->php_self;
		elseif (Tools::getValue('fc') == 'module' && $module_name != '' && (Module::getInstanceByName($module_name) instanceof PaymentModule))
			$page_name = 'module-payment-submit';
		// @retrocompatibility Are we in a module ?
		elseif (preg_match('#^'.preg_quote($this->context->shop->physical_uri, '#').'modules/([a-zA-Z0-9_-]+?)/(.*)$#', $_SERVER['REQUEST_URI'], $m))
			$page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
		else
		{
			$page_name = Dispatcher::getInstance()->getController();
			$page_name = (preg_match('/^[0-9]/', $page_name)) ? 'page_'.$page_name : $page_name;
		}

		$this->context->smarty->assign(Meta::getMetaTags($this->context->language->id, $page_name));
		$this->context->smarty->assign('request_uri', Tools::safeOutput(urldecode($_SERVER['REQUEST_URI'])));

		/* Breadcrumb */
		$navigationPipe = (Configuration::get('PS_NAVIGATION_PIPE') ? Configuration::get('PS_NAVIGATION_PIPE') : '>');
		$this->context->smarty->assign('navigationPipe', $navigationPipe);

		// Automatically redirect to the canonical URL if needed
		if (!empty($this->php_self) && !Tools::getValue('ajax'))
			$this->canonicalRedirection($this->context->link->getPageLink($this->php_self, $this->ssl, $this->context->language->id));

		Product::initPricesComputation();

		$display_tax_label = $this->context->country->display_tax_label;
		if (isset($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}) && $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})
		{
			$infos = Address::getCountryAndState((int)($cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));
			$country = new Country((int)$infos['id_country']);
			$this->context->country = $country;
			if (Validate::isLoadedObject($country))
				$display_tax_label = $country->display_tax_label;
		}

		$languages = Language::getLanguages(true, $this->context->shop->id);
		$meta_language = array();
		foreach ($languages as $lang)
			$meta_language[] = $lang['iso_code'];

		$this->context->smarty->assign(array(
			// Usefull for layout.tpl
			'mobile_device' => $this->context->getMobileDevice(),
			'link' => $link,
			'cart' => $cart,
			'currency' => $currency,
			'cookie' => $this->context->cookie,
			'page_name' => $page_name,
			'hide_left_column' => !$this->display_column_left,
			'hide_right_column' => !$this->display_column_right,
			'base_dir' => _PS_BASE_URL_.__PS_BASE_URI__,
			'base_dir_ssl' => $protocol_link.Tools::getShopDomainSsl().__PS_BASE_URI__,
			'content_dir' => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__,
			'base_uri' => $protocol_content.Tools::getHttpHost().__PS_BASE_URI__.(!Configuration::get('PS_REWRITING_SETTINGS') ? 'index.php' : ''),
			'tpl_dir' => _PS_THEME_DIR_,
			'modules_dir' => _MODULE_DIR_,
			'mail_dir' => _MAIL_DIR_,
			'lang_iso' => $this->context->language->iso_code,
			'come_from' => Tools::getHttpHost(true, true).Tools::htmlentitiesUTF8(str_replace(array('\'', '\\'), '', urldecode($_SERVER['REQUEST_URI']))),
			'cart_qties' => (int)$cart->nbProducts(),
			'currencies' => Currency::getCurrencies(),
			'languages' => $languages,
			'meta_language' => implode('-', $meta_language),
			'priceDisplay' => Product::getTaxCalculationMethod(),
			'add_prod_display' => (int)Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
			'shop_name' => Configuration::get('PS_SHOP_NAME'),
			'roundMode' => (int)Configuration::get('PS_PRICE_ROUND_MODE'),
			'use_taxes' => (int)Configuration::get('PS_TAX'),
			'show_taxes' => (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX')),
			'display_tax_label' => (bool)$display_tax_label,
			'vat_management' => (int)Configuration::get('VATNUMBER_MANAGEMENT'),
			'opc' => (bool)Configuration::get('PS_ORDER_PROCESS_TYPE'),
			'PS_CATALOG_MODE' => (bool)Configuration::get('PS_CATALOG_MODE') || !(bool)Group::getCurrent()->show_prices,
			'b2b_enable' => (bool)Configuration::get('PS_B2B_ENABLE'),
			'request' => $link->getPaginationLink(false, false, false, true),
			'PS_STOCK_MANAGEMENT' => Configuration::get('PS_STOCK_MANAGEMENT')
		));

		// Add the tpl files directory for mobile
		if ($this->context->getMobileDevice() != false)
			$this->context->smarty->assign(array(
				'tpl_mobile_uri' => _PS_THEME_MOBILE_DIR_,
			));

		// Deprecated
		$this->context->smarty->assign(array(
			'id_currency_cookie' => (int)$currency->id,
			'logged' => $this->context->customer->isLogged(),
			'customerName' => ($this->context->customer->logged ? $this->context->cookie->customer_firstname.' '.$this->context->cookie->customer_lastname : false)
		));

		$assign_array = array(
			'img_ps_dir' => _PS_IMG_,
			'img_cat_dir' => _THEME_CAT_DIR_,
			'img_lang_dir' => _THEME_LANG_DIR_,
			'img_prod_dir' => _THEME_PROD_DIR_,
			'img_manu_dir' => _THEME_MANU_DIR_,
			'img_sup_dir' => _THEME_SUP_DIR_,
			'img_ship_dir' => _THEME_SHIP_DIR_,
			'img_store_dir' => _THEME_STORE_DIR_,
			'img_col_dir' => _THEME_COL_DIR_,
			'img_dir' => _THEME_IMG_DIR_,
			'css_dir' => _THEME_CSS_DIR_,
			'js_dir' => _THEME_JS_DIR_,
			'pic_dir' => _THEME_PROD_PIC_DIR_
		);

		// Add the images directory for mobile
		if ($this->context->getMobileDevice() != false)
			$assign_array['img_mobile_dir'] = _THEME_MOBILE_IMG_DIR_;

		// Add the CSS directory for mobile
		if ($this->context->getMobileDevice() != false)
			$assign_array['css_mobile_dir'] = _THEME_MOBILE_CSS_DIR_;

		foreach ($assign_array as $assign_key => $assign_value)
			if (substr($assign_value, 0, 1) == '/' || $protocol_content == 'https://')
				$this->context->smarty->assign($assign_key, $protocol_content.Tools::getMediaServer($assign_value).$assign_value);
			else
				$this->context->smarty->assign($assign_key, $assign_value);

		/*
		 * These shortcuts are DEPRECATED as of version 1.5.
		 * Use the Context to access objects instead.
		 * Example: $this->context->cart
		 */
		self::$cookie = $this->context->cookie;
		self::$cart = $cart;
		self::$smarty = $this->context->smarty;
		self::$link = $link;
		$defaultCountry = $this->context->country;

		$this->displayMaintenancePage();
		if ($this->restrictedCountry)
			$this->displayRestrictedCountryPage();

		if (Tools::isSubmit('live_edit') && !$this->checkLiveEditAccess())
			Tools::redirect('index.php?controller=404');

		$this->iso = $iso;

		$this->context->cart = $cart;
		$this->context->currency = $currency;
	
}	
public function initContent() {
        $ubucacion = '';
        if (isset($this->context->controller->php_self)) {
            $ubucacion = $this->context->controller->php_self;
        }
        if ($ubucacion === 'authentication' || $ubucacion === 'order' || $ubucacion === 'identity') {
            self::$smarty->assign('lightboxshow', 'no');
        } else {
            self::$smarty->assign('lightboxshow', 'si');
        }
        //(order,authentication,)
        // validaciones para el control del ligthbox 
        /*self::$smarty->assign('iexplorerold', FALSE);
        self::$smarty->assign('lightbox1', FALSE);

        preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT'], $matches);
        if (count($matches) > 1) {
            $version = $matches[1];

            if ($version < 11) {
                if (!isset($_COOKIE['iexplorerOld'])) {
                    setcookie("iexplorerOld", 'iexplorerOld', time() + 3600 * 24 * 30);
                    self::$smarty->assign('iexplorerold', TRUE);
                    self::$smarty->assign('lightbox1', TRUE);
                }
            }
        }

        self::$smarty->assign('newsletter', FALSE);
        self::$smarty->assign('lightbox1', FALSE);

        include(dirname(__FILE__) . '/../../../classes/Mobile_Detect.php');
        $detect = new Mobile_Detect;
        $deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

        if ($deviceType != 'phone' && $deviceType != 'tablet') {
// si el cliente no es un bot
            if (!$this->is_bot()) {
                // si la cookie no existe   
                if (!isset($_COOKIE['newsletter'])) {
                    setcookie("newsletter", 'newsletter', time() + 3600 * 24 * 5);
                    self::$smarty->assign('newsletter', TRUE);
                    self::$smarty->assign('lightbox1', TRUE);
                }
            }
        } else {
        	// INICIO POP-UP DOWNLOAD APP FARMALISTO
        	
			// si la cookie CookApp no existe   
            if (!isset($_COOKIE['CookApp'])) {
            	$userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
	        	// android
				if(stripos($userAgent,'android') !== false) {
					$type = "android";
				}
				// ipad
				if(stripos($userAgent,'ipad') !== false){
					$type = "ipad";
				}
				// iphone/ipod
				if(stripos($userAgent,'iPhone') || stripos($userAgent,'iPod'))
				{
				 	$type = "ios";
				}
                setcookie("CookApp", 'CookApp', time() + 3600 * 24 * 5);
				//self::$smarty->assign('MobileDetected', $deviceType);
				//self::$smarty->assign('TypeMobileDetected', $type);
            }
			// FIN POP-UP DOWNLOAD APP FARMALISTO
        }*/

// Fin validaciones para el control del ligthbox      

		// si esta activa la redirección entre paises se envia una variable smarty como true para generar el pop-up para la redirección
		if ( Configuration::get('PS_REDIRECTION_COUNTRIES') == 1 ) {

			// se instancian los archivos necesarios para realizar la geolocalizacion por ip
			include_once(_PS_MODULE_DIR_ ."../tools/geoip/geoip.inc");
			include_once(_PS_MODULE_DIR_ ."../tools/geoip/geoipcity.inc");

			// se consulta el pais en el que se encuentra el usuario
			$geoip = geoip_open(_PS_MODULE_DIR_ .'../tools/geoip/GeoLiteCity.dat','1');

			//$_SERVER['REMOTE_ADDR'] = '186.180.255.255';

			$CountryAccessUserLocal = geoip_country_name_by_addr($geoip, $_SERVER['REMOTE_ADDR']);

			// se trae la extension a la cual se desea redireccionar
			$ExtPage = strtolower( geoip_country_code_by_addr($geoip, $_SERVER['REMOTE_ADDR']) );

			// arreglo con las extenciones de las paginas existentes
			$ExtValid = array('co','mx');

			$ShopCountry = Utilities::sanear_string( Configuration::get('PS_SHOP_COUNTRY') );
			
			// se valida si el pais de la pagina es igual a la ciudad del usuario, si no son iguales, se genera el pop-up
			if ( $CountryAccessUserLocal != $ShopCountry && $CountryAccessUserLocal != "" && in_array($ExtPage, $ExtValid) ) {
				if ( Configuration::get('IP_NOT_REDIRECTION_PAGES') != $_SERVER['REMOTE_ADDR'] ) {
					self::$smarty->assign('redirection_countries', false);
					self::$smarty->assign('country_page_local', Configuration::get('PS_SHOP_COUNTRY'));
					self::$smarty->assign('country_page_redirect', $CountryAccessUserLocal );
					self::$smarty->assign('url_page_redirection', 'http://www.farmalisto.com.'.$ExtPage);
					setcookie("CookRedirectionColombia", 'CookRedirectionColombia', time() + 3600 * 2);
				}
			}
		}

		$this->context->smarty->assign( 'lightbox_horario_call', Configuration::get('lightbox_horario_call') );
		setcookie("Cooklightbox_horario_call_mex", 'Cooklightbox_horario_call_mex', time() + 3600 * 1);

        $this->process();
		if (!isset($this->context->cart))
			$this->context->cart = new Cart();
		if ($this->context->getMobileDevice() == false) {
			// These hooks aren't used for the mobile theme.
			// Needed hooks are called in the tpl files.
			if (!isset($this->context->cart))
				$this->context->cart = new Cart();
			$this->context->smarty->assign(array(
				'HOOK_HEADER' => Hook::exec('displayHeader'),
				'HOOK_TOP' => Hook::exec('displayTop'),
				'HOOK_HOMETOPDER' => Hook::exec('hometopder'),
				'HOOK_HOMEBOTCEN' => Hook::exec('homebotcen'),
				'HOOK_PRPAMIDCEN' => Hook::exec('prpamidcen'),
				'HOOK_PRPABOTCEN' => Hook::exec('prpabotcen'),
				'HOOK_SEARBOTCEN' => Hook::exec('searbotcen'),
				'HOOK_CATETOPIZQ' => Hook::exec('catetopizq'),
				'HOOK_CATETOPDER' => Hook::exec('catetopder'),
				'HOOK_CATEBOTCEN' => Hook::exec('catebotcen'),
				'HOOK_IMACATEGORY' => Hook::exec('imacategory'),
				'HOOK_ADSENSES' => Hook::exec('adsenses'),
				'HOOK_LEFT_COLUMN' => ($this->display_column_left ? Hook::exec('displayLeftColumn') : ''),
				'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
			));
		} else {
			$this->context->smarty->assign(array(
				'HOOK_MOBILE_HEADER' => Hook::exec('displayMobileHeader'),
			));
		}
    }
    
            
function is_bot(){
     
        $bots = array(
            'Googlebot', 'Baiduspider', 'ia_archiver',
            'R6_FeedFetcher', 'NetcraftSurveyAgent', 'Sogou web spider',
            'bingbot', 'Yahoo! Slurp', 'facebookexternalhit', 'PrintfulBot',
            'msnbot', 'Twitterbot', 'UnwindFetchor',
            'urlresolver', 'Butterfly', 'TweetmemeBot' );
     
        foreach($bots as $b){
            if( stripos( $_SERVER['HTTP_USER_AGENT'], $b ) !== false ) return true;
     
        }
        return false;
    }

	/**
	 * @deprecated 1.5.0
	 */
	public function displayHeader($display = true)
	{
		// This method will be removed in 1.6
		Tools::displayAsDeprecated();
		$this->initHeader();
		$hook_header = Hook::exec('displayHeader');
		if ((Configuration::get('PS_CSS_THEME_CACHE') || Configuration::get('PS_JS_THEME_CACHE')) && is_writable(_PS_THEME_DIR_.'cache'))
		{
			// CSS compressor management
			if (Configuration::get('PS_CSS_THEME_CACHE'))
				$this->css_files = Media::cccCSS($this->css_files);
			//JS compressor management
			if (Configuration::get('PS_JS_THEME_CACHE'))
				$this->js_files = Media::cccJs($this->js_files);
		}

		// Call hook before assign of css_files and js_files in order to include correctly all css and javascript files
        $this->context->smarty->assign(array(
			'HOOK_HEADER' => $hook_header,
			'HOOK_TOP' => Hook::exec('displayTop'),
			'HOOK_HOMETOPDER' => Hook::exec('hometopder'),
			'HOOK_HOMEBOTCEN' => Hook::exec('homebotcen'),
			'HOOK_PRPAMIDCEN' => Hook::exec('prpamidcen'),
			'HOOK_PRPABOTCEN' => Hook::exec('prpabotcen'),
			'HOOK_SEARBOTCEN' => Hook::exec('searbotcen'),
			'HOOK_CATETOPIZQ' => Hook::exec('catetopizq'),
			'HOOK_CATETOPDER' => Hook::exec('catetopder'),
			'HOOK_CATEBOTCEN' => Hook::exec('catebotcen'),
			'HOOK_IMACATEGORY' => Hook::exec('imacategory'),
			'HOOK_ADSENSES' => Hook::exec('adsenses'),
			'HOOK_LEFT_COLUMN' => ($this->display_column_left ? Hook::exec('displayLeftColumn') : ''),
			'HOOK_RIGHT_COLUMN' => ($this->display_column_right ? Hook::exec('displayRightColumn', array('cart' => $this->context->cart)) : ''),
			'HOOK_FOOTER' => Hook::exec('displayFooter')
		));

		$this->context->smarty->assign('css_files', $this->css_files);
		$this->context->smarty->assign('js_files', array_unique($this->js_files));

		$this->display_header = $display;
		$this->smartyOutputContent(_PS_THEME_DIR_.'header.tpl');

	}

	public function setMedia()
	{
		// if website is accessed by mobile device
		// @see FrontControllerCore::setMobileMedia()
		if ($this->context->getMobileDevice() != false)
		{
			$this->setMobileMedia();
			return true;
		}

		if (Tools::file_exists_cache(_PS_ROOT_DIR_.Tools::str_replace_once(__PS_BASE_URI__, DIRECTORY_SEPARATOR, _THEME_CSS_DIR_.'grid_prestashop.css')))
			$this->addCSS(_THEME_CSS_DIR_.'grid_prestashop.css', 'all');
		$this->addCSS(_THEME_CSS_DIR_.'global.css', 'all');
		// $this->addjquery(); // in header.tpl
			
		$this->addjqueryPlugin('easing');
		$this->addJS(_PS_JS_DIR_.'tools.js');

		if (Tools::isSubmit('live_edit') && Tools::getValue('ad') && Tools::getAdminToken('AdminModulesPositions'.(int)Tab::getIdFromClassName('AdminModulesPositions').(int)Tools::getValue('id_employee')))
		{
			$this->addJqueryUI('ui.sortable');
			$this->addjqueryPlugin('fancybox');
			$this->addJS(_PS_JS_DIR_.'hookLiveEdit.js');
			$this->addCSS(_PS_CSS_DIR_.'jquery.fancybox-1.3.4.css', 'all'); // @TODO
		}
		if ($this->context->language->is_rtl)
			$this->addCSS(_THEME_CSS_DIR_.'rtl.css');

		// Execute Hook FrontController SetMedia
		Hook::exec('actionFrontControllerSetMedia', array());
	}

	

        
}
?>