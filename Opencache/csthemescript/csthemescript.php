<?php
if (!defined('_PS_VERSION_'))
	exit;
class CsThemeScript extends Module
{
	function __construct()
	{
		$this->name = 'csthemescript';
		$this->tab = 'Other Module';
		$this->version = 1.0;
		$this->author = 'Codespot';
		parent::__construct();
		$this->displayName = $this->l('Cs Theme Script');
		$this->description = $this->l('Load javascript and css script for theme.');
	}

	function install()
	{
		return (parent::install() AND $this->registerHook('header'));
	}
	
	public function uninstall()
	{
		return (parent::uninstall());
	}
	
	function hookHeader($params)
	{
		global $smarty;
		
		  /*Load custom CSS files*/
		$this->context->controller->addCSS(_THEME_CSS_DIR_."reponsive.css");
		$this->context->controller->addCSS(_THEME_CSS_DIR_."ui.totop.css");
		
		$this->context->controller->addJs($this->_path.'js/jquery.cookie-plugin.js');
		$this->context->controller->addJs($this->_path.'js/jquery.carouFredSel-6.1.0.js');
		$this->context->controller->addJs($this->_path.'js/jquery.touchSwipe.min.js');
		$this->context->controller->addJs($this->_path.'js/easing.js');
		$this->context->controller->addJs($this->_path.'js/jquery.ba-throttle-debounce.min.js');
		$this->context->controller->addJs($this->_path.'js/jquery.mousewheel.min.js');
		$this->context->controller->addJs($this->_path.'js/jquery.ui.totop.js');
		$this->context->controller->addJs($this->_path.'js/getwidthbrowser.js');
		$this->context->controller->addJs($this->_path.'js/codespot.js');
		
		
		/* Load custom js files*/
		if ($smarty->tpl_vars['page_name']->value != 'index' && $smarty->tpl_vars['page_name']->value != 'product')
		{
			$this->context->controller->addJs($this->_path.'js/list.gird.js');
		}
		if ($smarty->tpl_vars['page_name']->value == 'products-comparison')
		{
			$this->context->controller->addJs($this->_path.'js/jquery.nicescroll.min.js');
		}
	}
}


