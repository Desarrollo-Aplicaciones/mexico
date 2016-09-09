<?php
if (!defined('_PS_VERSION_'))
	exit;

class CsAddToCartExtend extends Module
{
	private $_html;
	function __construct()
	{
		$this->name = 'csaddtocartextend';
		$this->tab = 'My Blocks';
		$this->version = 1.0;
		$this->author = 'Codespot';

		parent::__construct();

		$this->displayName = $this->l('CS add to cart extend');
		$this->description = $this->l('To create notifications on button add to cart.');
	}

	function install()
	{
		return (Configuration::updateValue('CS_ADD_TO_CART_CLASS',"a.ajax_add_to_cart_button") AND parent::install() AND $this->registerHook('header'));
	}
	
	public function uninstall()
	{
		Configuration::deleteByName('CS_ADD_TO_CART_CLASS');
		return parent::uninstall();
	}

	
	function hookHeader($params)
	{
		global $smarty;
		$this->context->controller->addCss($this->_path.'css/jquery.ambiance.css', 'all');
		$this->context->controller->addJs($this->_path.'js/jquery.ambiance.js');
		$add_to_cart_class =Configuration::get('CS_ADD_TO_CART_CLASS');
		$smarty->assign('add_to_cart_class',$add_to_cart_class);
		$smarty->assign(array(
			'add_to_cart_class' => $add_to_cart_class,
			'__PS_BASE_URI__' => __PS_BASE_URI__
		));
		return $this->display(__FILE__, 'csaddtocartextend.tpl');
	}
}


