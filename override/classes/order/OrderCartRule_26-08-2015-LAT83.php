<?php

class OrderCartRule extends OrderCartRuleCore
{
	/** @var int porcentaje de reduccion del cupon aplicado en el momento de la orden */
	public $reduction_percent;
	
	/** @var int descuento monetario del cupon aplicado en el momento de la orden */
	public $reduction_amount;


	public static $definition = array(
		'table' => 'order_cart_rule',
		'primary' => 'id_order_cart_rule',
		'fields' => array(
			'id_order' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_cart_rule' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_order_invoice' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'name' => 				array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true),
			'value' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'value_tax_excl' => 	array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'free_shipping' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'reduction_percent' => 	array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'reduction_amount' => 	array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat')
		)
	);
}

