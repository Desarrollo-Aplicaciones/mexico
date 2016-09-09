<?php

if ( !defined('_PS_VERSION_') ) {
	exit;
}

class progressivediscounts extends Module
{
	public function __construct()
	{
		$this->name = 'progressivediscounts';
		$this->version = '0.1 Alfa';
		$this->author = 'Farmalisto - Jeisson Arturo Gomez Ayala';
		$this->displayName = $this->l('Descuentos Progresivos');
		$this->description = $this->l('Módulo para generar descuentos automaticos y escalables para las ordenes de compra');

		parent::__construct();

		$this->context->smarty->assign('pathModule', dirname(__FILE__));
	}

	public function install()
	{
		if ( !$id_tab = Tab::getIdFromClassName('AdminProgressiveDiscounts') ) {
			$tab = new Tab();
			$tab->class_name = 'AdminProgressiveDiscounts';
			$tab->module = 'progressivediscounts';
			$tab->id_parent = (int)Tab::getIdFromClassName('AdminPriceRule');

			foreach ( Language::getLanguages(false) as $lang ) {
				$tab->name[(int)$lang['id_lang']] = 'Descuentos Progresivos';
			}
			
			if ( !$tab->save() ) {
				return $this->_abortInstall('Imposible crear la pestaña');
			}
		}

		if ( !parent::install() ) {   
			return false;
		}

		$executecreateTables = $this->createTables();
		if ( !$executecreateTables ) {
			return $this->_abortInstall('Imposible crear las tablas');
		}

		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		// eliminar tab (pestaña) del menu del backoffice
		$tab = new Tab();
		$id_tab_progressive_iscounts = $tab->getIdFromClassName("AdminProgressiveDiscounts");
		$tab->id = $id_tab_progressive_iscounts;
		if ( !$tab->delete() ) {
			Tools::displayError('Imposible eliminar pestaña del menu');
		}

		return parent::uninstall();
	}

	public function displayForm()
	{
		return $this->display(__FILE__, '/tpl/progressivediscounts.tpl');
	}

	public function getContent()
	{
		$query = new DbQuery();
		$query->from('progressive_discounts');
		$query->orderBy('date_modify DESC');

		$items = Db::getInstance()->executeS($query);
		$buttons = "buttonsList_progressive_discounts";
		$form = "list_progressive_discounts";
		$legend_body = "Lista Descuentos Progresivos";

		if ( Tools::isSubmit('button_add') ) {
			$items = array();
			$buttons = "buttonsForm_progressive_discounts";
			$form = "form_progressive_discounts";
			$legend_body = "Nuevo Descuento Progresivo";
		}

		$this->context->smarty->assign(array(
			'items_progressive_discounts' => $items,
			'buttons' => $buttons,
			'form' => $form,
			'legend_body' => $legend_body
		));

		return $this->displayForm();
	}

	protected function createTables()
	{
		/* ps_progressive_discounts */
		$executeQuery = Db::getInstance()->execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."progressive_discounts` (
			`id_progressive_discount`  int NOT NULL AUTO_INCREMENT ,
			`name`  varchar(50) NOT NULL ,
			`description`  text NOT NULL ,
			`active`  tinyint NOT NULL ,
			`frequency`  int NOT NULL ,
			`periods`  int NOT NULL ,
			`limit_shopping_customer`  int NOT NULL ,
			`shopping_reset`  int NOT NULL ,
			`cycles`  int NOT NULL ,
			`states_orders` varchar(40) NOT NULL,
			`date_create` datetime NOT NULL,
			`date_modify` datetime NOT NULL,
			PRIMARY KEY (`id_progressive_discount`)
			)
			DEFAULT CHARSET=utf8
			ENGINE=Aria
			;");

		/* ps_product_progressive_discounts */
		$executeQuery &= Db::getInstance()->execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."product_progressive_discounts` (
			`id_product_progressive_discount`  int NOT NULL AUTO_INCREMENT ,
			`id_progressive_discount`  int NOT NULL ,
			`id_product`  int NOT NULL ,
			`reference_product`  varchar(50) NOT NULL ,
			PRIMARY KEY (`id_product_progressive_discount`)
			)
			DEFAULT CHARSET=utf8
			ENGINE=Aria
			;");

		/* ps_cart_rule_progressive_discounts */
		$executeQuery &= Db::getInstance()->Execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cart_rule_progressive_discounts` (
			`id_cart_rule_progressive_discount`  int NOT NULL AUTO_INCREMENT ,
			`id_progressive_discount`  int NOT NULL ,
			`id_cart_rule`  int NOT NULL ,
			`priority`  int(5) NOT NULL ,
			PRIMARY KEY (`id_cart_rule_progressive_discount`)
			)
			DEFAULT CHARSET=utf8
			ENGINE=Aria
			;");

		/* ps_order_history_progressive_discounts */
		$executeQuery &= Db::getInstance()->Execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."order_history_progressive_discounts` (
			`id_order_history_progressive_discount`  int NOT NULL AUTO_INCREMENT ,
			`id_progressive_discount`  int NOT NULL ,
			`id_order`  int NOT NULL ,
			`id_customer`  int NOT NULL ,
			`id_product`  int NOT NULL ,
			`id_cart_rule`  int NOT NULL ,
			`id_cart_rule_progressive_disscount` int NOT NULL,
			`date_order`  datetime NOT NULL ,
			`date_final_period`  datetime NOT NULL ,
			`date_final_progressive_disscount`  datetime NOT NULL ,
			`initial_shopping`  int(1) NOT NULL ,
			`counter_orders_period`  int NOT NULL ,
			`counter_period`  int NOT NULL ,
			`counter_reset`  int NOT NULL ,
			`counter_cycles`  int NOT NULL ,
			PRIMARY KEY (`id_order_history_progressive_discount`)
			)
			DEFAULT CHARSET=utf8
			ENGINE=Aria
			;");

		/* ps_cart_cartrule_progressive_discounts */
		$executeQuery &= Db::getInstance()->Execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cart_cartrule_progressive_discounts` (
			`id_cart`  int NOT NULL,
			`id_cart_rule`  int NOT NULL,
			`id_progressive_discount`  int NOT NULL
			)
			DEFAULT CHARSET=utf8
			ENGINE=Aria
			;");

		return $executeQuery;
	}
}
?>