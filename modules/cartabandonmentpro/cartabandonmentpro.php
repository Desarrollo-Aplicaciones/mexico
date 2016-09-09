<?php
if (!defined('_PS_VERSION_'))
	exit;

class CartAbandonmentPro extends Module
{
	protected static $lang_cache;

	public function __construct()
	{
		$this->name 					= 'cartabandonmentpro';
		$this->tab 						= 'advertising_marketing';
		$this->version 					= '1.2.2';
		$this->author 					= 'PrestaShop';
		$this->module_key 				= '011df651e7ac1913166469984d0cf519';
		$this->need_instance 			= 0;
		$this->ps_version_compliancy 	= array('min' => '1.5.0.0', 'max' => '1.6.7');
		$this->dependencies 			= array();
		$this->bootstrap				= true;
		parent::__construct();

		$this->displayName				= $this->l('Cart Abandonment Pro');
		$this->description 				= $this->l('Send an automatic mail to customers that abandoned their shopping cart.');

		$this->confirmUninstall 		= $this->l('Are you sure you want to uninstall?');

		$this->css_path = $this->_path.'views/css/';
		$this->js_path = $this->_path.'views/js/';
		if (version_compare(_PS_VERSION_, '1.6', '<'))
		$this->getLang();
	}

	public function install()
	{
		$token = uniqid(rand(), true);

		Configuration::updateValue('CART_MAXREMINDER', 7);
		Configuration::updateValue('CART_MAXREMINDER_WHAT', 'days');
		$token = uniqid(rand(), true);
		Configuration::updateValue('CARTABAND_TOKEN', $token);

		if(!parent::install() || !$this->installDB() || !$this->installTab())
			return false;
		return true;
	}

	private function installDB()
	{
		$query = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cartabandonment_template` (
				  `id_template` int(11) NOT NULL AUTO_INCREMENT,
				  `id_model` int(11) NOT NULL,
				  `name` varchar(100) NOT NULL,
				  `id_lang` int(11),
				  `id_shop` int(11),
				  `active` int(11),
				  `order` int(11),
				  PRIMARY KEY (`id_template`)
				  );CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cartabandonment_template_field` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `id_template` int(11) NOT NULL,
					  `id_field` int(11) NOT NULL,
					  `value` longtext NOT NULL,
					  `column` varchar(10) NOT NULL,
					  PRIMARY KEY (`id`)
					);CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cartabandonment_template_color` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `id_template` int(11) NOT NULL,
				  `id_color` int(11) NOT NULL,
				  `value` varchar(15) NOT NULL,
				  PRIMARY KEY (`id`)
				);
				CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cartabandonment_remind_config` (
				  `wich_remind` int(11) NOT NULL,
				  `days` int(11) NOT NULL,
				  `hours` int(11) NOT NULL,
				  `active` int(11) NOT NULL,
				  `id_shop` int(11) NOT NULL,
				  PRIMARY KEY (`wich_remind`)
				);
				INSERT INTO `"._DB_PREFIX_."cartabandonment_remind_config` VALUES (1, 0, 2, 1, 0);
				INSERT INTO `"._DB_PREFIX_."cartabandonment_remind_config` VALUES (2, 2, 0, 0, 0);
				INSERT INTO `"._DB_PREFIX_."cartabandonment_remind_config` VALUES (3, 5, 0, 0, 0);";

				$query2 = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cartabandonment_remind` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `wich_remind` int(11) NOT NULL,
				  `id_cart` int(11) NOT NULL,
				  `send_date` date NOT NULL,
				  PRIMARY KEY (`id`)
				);
				CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cartabandonment_remind_lang` (
				  `wich_remind` int(11) NOT NULL,
				  `id_lang` int(11) NOT NULL,
				  `id_template` int(11) NOT NULL,
				  `tpl_same` int(11) NOT NULL,
				  `id_shop` int(11) NOT NULL,
				  PRIMARY KEY (`wich_remind`,`id_lang`,`id_template`)
				);
				ALTER TABLE `"._DB_PREFIX_."cartabandonment_remind` ADD `visualize` INT NOT NULL DEFAULT '0',
				ADD `click` INT NOT NULL DEFAULT '0';
				ALTER TABLE `"._DB_PREFIX_."cartabandonment_remind` ADD `click_cart` INT NOT NULL DEFAULT '0';
				CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cartabandonment_unsubscribe` (
				`id_customer` int(11) NOT NULL,
				PRIMARY KEY (`id_customer`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		return Db::getInstance()->Execute($query) && Db::getInstance()->Execute($query2);
	}

	public function uninstall()
	{
		$query  = "DROP TABLE `"._DB_PREFIX_."cartabandonment_template`;";
		$query2 = "DROP TABLE `"._DB_PREFIX_."cartabandonment_template_field`;";
		$query3 = "DROP TABLE `"._DB_PREFIX_."cartabandonment_template_color`;";
		$query4 = "DROP TABLE `"._DB_PREFIX_."cartabandonment_conf`;";
		$query4 = "DROP TABLE `"._DB_PREFIX_."cartabandonment_remind`;";
		$query5 = "DROP TABLE `"._DB_PREFIX_."cartabandonment_remind_lang`;";
		$query6 = "DROP TABLE `"._DB_PREFIX_."cartabandonment_remind_config`;";
		$query6 = "DROP TABLE `"._DB_PREFIX_."cartabandonment_unsubscribe`;";

		return  parent::uninstall()
			&& Db::getInstance()->Execute($query)
			&& Db::getInstance()->Execute($query2)
			&& Db::getInstance()->Execute($query3)
			&& Db::getInstance()->Execute($query4)
			&& Db::getInstance()->Execute($query5)
			&& Db::getInstance()->Execute($query6)
			&& $this->uninstallTab();
	}

	 /**
	* Loads asset resources
	*/
	public function loadAsset()
	{
		$css_compatibility = $js_compatibility = array();

		// Load CSS
		$css = array(
			$this->css_path.'bootstrap-select.min.css',
			$this->css_path.'DT_bootstrap.css',
			$this->css_path.'fix.css',
			$this->css_path.'views/css/reset.css'
		);
		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			$css_compatibility = array(
				$this->css_path.'bootstrap.min.css',
				$this->css_path.'bootstrap.extend.css',
				$this->css_path.'bootstrap-responsive.min.css',
				$this->css_path.'font-awesome.min.css',
			);
			$css = array_merge($css_compatibility, $css);
		}
		$this->context->controller->addCSS($css, 'all');

		// Load JS
		$js = array(
			$this->js_path.$this->name.'.js',
			$this->js_path.'bootstrap-select.min.js',
			$this->js_path.'bootstrap-dialog.js',
			$this->js_path.'jquery.autosize.min.js',
			$this->js_path.'jquery.dataTables.js',
			$this->js_path.'DT_bootstrap.js',
			$this->js_path.'dynamic_table_init.js',
			$this->js_path.'jscolor.js',
			$this->js_path.'tinymce/tinymce.min.js',
		);
		if (version_compare(_PS_VERSION_, '1.6', '<'))
		{
			$js_compatibility = array(
				$this->js_path.'bootstrap.min.js'
			);
			$js = array_merge($js_compatibility, $js);
		}
		$this->context->controller->addJS($js);

		// Clean memory
		unset($js, $css, $js_compatibility, $css_compatibility);
	}

	public function getContent()
	{
		require_once dirname(__FILE__).'/controllers/GodController.class.php';
		require_once dirname(__FILE__).'/classes/Model.class.php';
		require_once dirname(__FILE__).'/classes/Template.class.php';
		require_once dirname(__FILE__).'/controllers/TemplateController.class.php';
		require_once dirname(__FILE__).'/controllers/ConfController.class.php';
		require_once dirname(__FILE__).'/controllers/ReminderController.class.php';
		require_once dirname(__FILE__).'/controllers/StatsController.class.php';

		$god = new GodController();
		unset($god);
		$this->loadAsset();

		$this->viewEdit();
		$this->initVars();
		$this->initStats();

		return $this->display(__FILE__, GodController::getTemplate());
	}

	private function initStats(){
		$this->context->smarty->assign('carts1', ReminderController::getAbandonedCart(1, $this->context->shop->id));
		$this->context->smarty->assign('carts2', ReminderController::getAbandonedCart(2, $this->context->shop->id));
		$this->context->smarty->assign('carts3', ReminderController::getAbandonedCart(3, $this->context->shop->id));
		$this->context->smarty->assign('stats', StatsController::getStatsForReminder());
		$this->context->smarty->assign('unsubscribe', StatsController::getUnsubscribe());
		// d(StatsController::getTransformedCarts());
	}

	//
	private function initVars(){
		$this->context->smarty->assign('token', Configuration::get('CARTABAND_TOKEN'));

		$languages = Language::getLanguages();

		$id_lang = Tools::getValue('id_lang');

		if(!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');

		$this->context->smarty->assign('languages', $languages);
		$logo = Configuration::get('PS_LOGO');
		$this->context->smarty->assign('logo', $this->context->shop->domain.__PS_BASE_URI__.'img/'.$logo);
		$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
		$this->context->smarty->assign('uri', $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$this->context->smarty->assign('url', $protocol . $_SERVER['HTTP_HOST'] . __PS_BASE_URI__);

		$this->context->smarty->assign('dirname', dirname(__FILE__));

		$this->initReminders();

		$this->context->smarty->assign('templates', TemplateController::getAllTemplates($this->context->shop->id, $id_lang));
		$this->context->smarty->assign('id_shop', $this->context->shop->id);
		$this->context->smarty->assign('id_lang', $id_lang);
		$this->context->smarty->assign('language', $id_lang);
		$this->context->smarty->assign('iso_lang', Language::getIsoById($id_lang));
		$this->context->smarty->assign('lang_select', self::$lang_cache);
		$this->context->smarty->assign('token_send', Configuration::get('CARTABAND_TOKEN'));

		$conf = Tools::getValue('cartabandonment_conf');
		if(!isset($conf))
			$conf = 0;
		$this->context->smarty->assign('conf', $conf);

		$this->initEdit($id_lang);

		if(Tools::getValue('justEdited') == 1) $edit = 1;
		else							 	   $edit = 0;

		$this->context->smarty->assign(array(
			'var_ajax' => $this->setVarAjax(),
			'base_url' => $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
			'ps_version' => (bool)version_compare(_PS_VERSION_, '1.6', '>'),
			'edit' => $edit
		));
	}

	// Get reminders
	private function initReminders(){
		$reminder = ReminderController::getReminders(1);
		$this->context->smarty->assign('first_reminder_days', $reminder[0]['days']);
		$this->context->smarty->assign('first_reminder_hours', $reminder[0]['hours']);
		$this->context->smarty->assign('first_reminder_active', $reminder[0]['active']);

		$reminder = ReminderController::getReminders(2);
		$this->context->smarty->assign('second_reminder_days', $reminder[0]['days']);
		$this->context->smarty->assign('second_reminder_hours', $reminder[0]['hours']);
		$this->context->smarty->assign('second_reminder_active', $reminder[0]['active']);

		$reminder = ReminderController::getReminders(3);
		$this->context->smarty->assign('third_reminder_days', $reminder[0]['days']);
		$this->context->smarty->assign('third_reminder_hours', $reminder[0]['hours']);
		$this->context->smarty->assign('third_reminder_active', $reminder[0]['active']);

		$this->context->smarty->assign('max_reminder', Configuration::get('CART_MAXREMINDER'));
		$this->context->smarty->assign('max_reminder_what', Configuration::get('CART_MAXREMINDER_WHAT'));
	}

	// Edit a template
	private function viewEdit(){
		if(Tools::getValue('viewedit') == 1){
			$this->context->smarty->assign('viewedit', 1);
			$this->context->smarty->assign('edittpl', Tools::getValue('tpl'));
			$this->context->smarty->assign('viewedit', 1);

			$editor = TemplateController::getEditor(Tools::getValue('tpl'));
			$this->context->smarty->assign('modelFile', '../../../model/' . $editor[0]['id_model'] . '_form_edit.tpl');

			$this->context->smarty->assign('tplDetails', $editor);
			$this->context->smarty->assign('tplColors', TemplateController::getEditorColors(Tools::getValue('tpl')));
			$this->context->smarty->assign('tplFields', TemplateController::getEditorFields(Tools::getValue('tpl')));
		}
		else
			$this->context->smarty->assign('viewedit', 0);
	}

	private function initEdit($id_lang){
		$reminders 	= ReminderController::getRemindersByLanguage($id_lang, $this->context->shop->id);
		if(!$reminders){
			$this->context->smarty->assign('editor', 0);
			return false;
		}
		$this->context->smarty->assign('editor', 1);

		if($reminders[0]['tpl_same'])
			$this->context->smarty->assign('id_tpl_same', $reminders[0]['id_template']);

		$x = 1;
		foreach($reminders as $reminder){
			$template_id 	= $reminder['id_template'];
			$model_id 		= TemplateController::getModelByTemplate($template_id);
			$this->context->smarty->assign('template_file_' . $x, $template_id . '.html');
			$this->context->smarty->assign('template_file_' . $x, $template_id . '.html');
			$this->context->smarty->assign('template_file_' . $x, $template_id . '.html');
			$this->context->smarty->assign('edit_template_id' . $x, $template_id);
			$this->context->smarty->assign('edit_template_id' . $x, $template_id);
			$this->context->smarty->assign('edit_template_id' . $x, $template_id);
			$this->context->smarty->assign('edit_model_id' . $x, $model_id);
			$this->context->smarty->assign('edit_model_id' . $x, $model_id);
			$this->context->smarty->assign('edit_model_id' . $x, $model_id);
			$x++;
		}
	}

	private function getLang()
	{
		if (self::$lang_cache == null && !is_array(self::$lang_cache))
		{
			self::$lang_cache = array();
			if ($languages = Language::getLanguages())
			{
				foreach ($languages as $row)
				{
						$exprow = explode(' (', $row['name']);
						$subtitle = (isset($exprow[1]) ? trim(Tools::substr($exprow[1], 0, -1)) : '');
						self::$lang_cache[$row['iso_code']] = array (
								'title' => trim($exprow[0]),
								'subtitle' => $subtitle
						);
				}
				// Clean memory
				unset($row, $exprow, $result, $subtitle, $languages);
			}
		}
	}


	/**
	* Set some JS vars for Ajax
	* @return string
	*/
	private function setVarAjax()
	{
		return ('<script>
		var admin_module_controller = \'AdminCartAbandonmentHelpingController\';
		var admin_module_ajax_url = \''.$this->context->link->getAdminLink('AdminCartAbandonmentHelpingController').'\';
		var current_id_tab = '.(int)$this->context->controller->id.';
		</script>');
	}

	/**
	* Install Tab
	* @return boolean
	*/
	private function installTab()
	{
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = 'AdminCartAbandonmentHelpingController';
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
		$tab->name[$lang['id_lang']] = 'Cart Abandonment';
		unset($lang);
		$tab->id_parent = -1;
		$tab->module = $this->name;
		return $tab->add();
	}

	/**
	* Uninstall Tab
	* @return boolean
	*/
	private function uninstallTab()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminCartAbandonmentHelpingController');
		if ($id_tab)
		{
		$tab = new Tab($id_tab);
		return $tab->delete();
		}
		else
		return false;
	}
}

