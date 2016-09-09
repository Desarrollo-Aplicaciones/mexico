<?php
class SupplyOrder extends SupplyOrderCore
{
	/**
	 * @var Factura Proveedor
	 */
	public $supplier_invoice;
	/**
	 * @var costos de envío Proveedor
	 */
	public $shipping_base;
	public $shipping_tax;
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'supply_order',
		'primary' => 'id_supply_order',
		'fields' => array(
			'id_supplier' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'supplier_name' => 			array('type' => self::TYPE_STRING, 'validate' => 'isCatalogName', 'required' => false),
			'supplier_invoice' => 		array('type' => self::TYPE_STRING, 'required' => false),
			'id_lang' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_warehouse' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_supply_order_state' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_currency' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_ref_currency' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'reference' => 				array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'date_delivery_expected' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
			'total_te' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_with_discount_te' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_ti' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_tax' =>				array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'discount_rate' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => false),
			'discount_value_te' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'is_template' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'shipping_base' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'shipping_tax' => 			array('type' => self::TYPE_FLOAT),
		),
	);
}
?>