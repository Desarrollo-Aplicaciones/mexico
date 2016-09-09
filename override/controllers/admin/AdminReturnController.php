<?php

class AdminReturnController extends AdminReturnControllerCore
{
	public function __construct()
	{
		$this->context = Context::getContext();
	 	$this->table = 'order_return';
	 	$this->className = 'OrderReturn';
		$this->colorOnBackground = true;
		$this->_select = 'orsl.`name`';
		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'order_return_state_lang orsl ON (orsl.`id_order_return_state` = a.`state` AND orsl.`id_lang` = '.(int)$this->context->language->id.')';

 		$this->fields_list = array(
			'id_order_return' => array('title' => $this->l('ID'), 'align' => 'center', 'width' => 25),
			'id_order' => array('title' => $this->l('Order ID'), 'width' => 100, 'align' => 'center'),
			'name' => array('title' => $this->l('Status'), 'width' => 'auto', 'align' => 'left'),
			'date_add' => array('title' => $this->l('Date issued'), 'width' => 150, 'type' => 'date', 'align' => 'right'),
 		);

		$this->fields_options = array(
			'general' => array(
				'title' =>	$this->l('Merchandise return (RMA) options'),
				'fields' =>	array(
					'PS_ORDER_RETURN' => array('title' => $this->l('Enable returns:'), 'desc' => $this->l('Would you like to allow merchandise returns in your shop?'), 'cast' => 'intval', 'type' => 'bool'),
					'PS_ORDER_RETURN_NB_DAYS' => array('title' => $this->l('Time limit of validity:'), 'desc' => $this->l('How many days after the delivery date does the customer have to return a product?'), 'cast' => 'intval', 'type' => 'text', 'size' => '2'),
					'PS_ORDER_EDIT' => array('title' => $this->l('¿Activar la edición de ordenes?'), 'desc' => $this->l('Seleccione "sí;" para activar la edición de ordenes.'), 'cast' => 'intval', 'type' => 'bool'),
				),
				'submit' => array()
			),
		);

		AdminController::__construct();
	}
}