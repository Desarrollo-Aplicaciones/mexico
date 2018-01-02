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

class StatisticsCustomers extends ObjectModel
{
	/** @var string Name */
	public $customer_id;
	public $lastname;
	public $id_shop;
	public $type;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
			'table' => 'customers_statistics_spm',
			'primary' => 'id',
			'fields' => array(
					'customer_id' => array('type' => self::TYPE_INT,'validate' => 'isUnsignedInt','required' => true,),
					'lastname' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),
					'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
					'type' => array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isGenericName', 'size' => 128),
					
			),
	);
	
	
	 public function __construct($id = null) {
		//parent::__construct($id, $id_lang, $id_shop);
		
	 	$context = Context::getContext();
	 	$admin_url_to_customer = 'index.php?controller=AdminCustomers&id_customer='.$id.'&updatecustomer&token='.Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)($context->employee->id)).'';
	 	Tools::redirectAdmin($admin_url_to_customer);
	 	
	 } 
}
?>
