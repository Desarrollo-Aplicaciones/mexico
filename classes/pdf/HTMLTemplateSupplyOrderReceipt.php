<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license	http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class HTMLTemplateSupplyOrderReceiptCore extends HTMLTemplate
{
	public $supply_order;
	public $warehouse;
	public $address_warehouse;
	public $address_supplier;
	public $context;

	public function __construct(SupplyOrder $supply_order, $smarty)
	{
		$this->supply_order = $supply_order;
		$this->smarty = $smarty;
		$this->context = Context::getContext();
		$this->warehouse = new Warehouse((int)$supply_order->id_warehouse);
		$this->address_warehouse = new Address((int)$this->warehouse->id_address);
		$this->address_supplier = new Address(Address::getAddressIdBySupplierId((int)$supply_order->id_supplier));

		// header informations
		$this->date = Tools::displayDate($supply_order->date_add);
		$this->title = HTMLTemplateSupplyOrderReceipt::l('Formato de Recepción Técnica');
	}

	/**
	 * @see HTMLTemplate::getContent()
	 */
	public function getContent()
	{
		$receiver = $this->getReceiver();
		$supply_order_details = $this->supply_order->getEntriesCollection((int)$this->supply_order->id_lang);
		$array = array();
		$data = array();
		foreach ($supply_order_details->getResults() as $value) {
			$array[$value->id_product] = $value;
		}
		$info = $this->getBatch();
		$features = $this->getFeatures();
		foreach ($info as $key => $icr) {
			$data[$key] = $array[$icr['id_product']];
		}
		$this->smarty->assign(array(
			'date_delivery' => Tools::displayDate($this->supply_order->date_delivery_expected),
			'supplier_invoice' => $this->supply_order->supplier_invoice,
			'warehouse' => $this->warehouse,
			'address_warehouse' => $this->address_warehouse,
			'address_supplier' => $this->address_supplier,
			'supply_order' => $this->supply_order,
			'data' => $data,
			'info' => $info,
			'features' => $features,
			'receiver' => $receiver,
		));

		return $this->smarty->fetch($this->getTemplate('supply-order-receipt'));
	}

	/**
	 * Consulta caracteristicas adicionales
	 */
	public function getFeatures(){
		$details = $this->supply_order->getEntriesCollection((int)$this->supply_order->id_lang)->getResults();
		$prodIds = array_map(function($o) { return $o->id_product; }, $details);
		$array = array();
		$query = new DbQuery();
		$query->select('p.id_product, GROUP_CONCAT(DISTINCT fvl.value ORDER BY fvl.value DESC SEPARATOR \' x \') AS presentacion, man.name');
		$query->from('product', 'p');
		$query->leftJoin('feature_product', 'f', 'p.id_product = f.id_product');
		$query->leftJoin('feature_value_lang', 'fvl', 'f.id_feature_value = fvl.id_feature_value');
		$query->leftJoin('manufacturer', 'man', 'p.id_manufacturer = man.id_manufacturer');
		$query->where('fvl.id_lang = '.$this->supply_order->id_lang);
		$query->where('(f.id_feature = 6 OR f.id_feature = 7)');
		$query->where('p.id_product IN ('.implode($prodIds, ',').')');
		$query->groupBy('p.id_product');
		$query->orderBy('p.id_product DESC');
		$result = Db::getInstance()->executeS($query);
		foreach ($result as $value) {
			$array[$value['id_product']] = $value;
		}
		return $array;
	}
	public function getReceiver(){
		$query = new DbQuery();
		$query->select('CONCAT (employee_firstname, \' \', employee_lastname) AS employee');
		$query->from('supply_order_history');
		$query->orderBy('id_supply_order_history DESC');
		$query->where('id_state = 5');
		$query->where('id_supply_order = '.$this->supply_order->id);
		$query->limit(1);
		$items = Db::getInstance()->executeS($query);
		return $items[0]['employee'];
	}
	public function getBatch(){
		$query = new DbQuery();
		$query->select('cod.cod_icr, det.id_product, icr.lote, icr.fecha_vencimiento');
		$query->from('supply_order_icr', 'icr');
		$query->innerJoin('icr', 'cod', 'icr.id_icr = cod.id_icr');
		$query->leftJoin('supply_order_detail', 'det', 'icr.id_supply_order_detail = det.id_supply_order_detail');
		$query->where('det.id_supply_order = '.$this->supply_order->id);
		$query->orderBy('det.id_product DESC');
		$result = Db::getInstance()->executeS($query);
		return $result;
	}

	/**
	 * @see HTMLTemplate::getBulkFilename()
	 */
	public function getBulkFilename()
	{
		return 'supply_order.pdf';
	}

	/**
	 * @see HTMLTemplate::getFileName()
	 */
	public function getFilename()
	{
		return self::l('SupplyOrderReceipt').sprintf('_%s', $this->supply_order->reference).'.pdf';
	}


	/**
	 * @see HTMLTemplate::getHeader()
	 */
	public function getHeader()
	{
		$shop_name = Configuration::get('PS_SHOP_NAME');
		$path_logo = $this->getLogo();
		$width = $height = 0;
		
		if (!empty($path_logo))
			list($width, $height) = getimagesize($path_logo);
		
		$this->smarty->assign(array(
			'logo_path' => $path_logo,
			'img_ps_dir' => 'http://'.Tools::getMediaServer(_PS_IMG_)._PS_IMG_,
			'img_update_time' => Configuration::get('PS_IMG_UPDATE_TIME'),
			'title' => $this->title,
			'reference' => $this->supply_order->reference,
			'date' => $this->date,
			'shop_name' => $shop_name,
			'width_logo' => $width,
			'height_logo' => $height
		));

		return $this->smarty->fetch($this->getTemplate('supply-order-header'));
	}

	/**
	 * @see HTMLTemplate::getFooter()
	 */
	public function getFooter()
	{
		$this->address = $this->address_warehouse;

		$this->smarty->assign(array(
			'shop_address' => $this->getShopAddress(),
			'shop_fax' => Configuration::get('PS_SHOP_FAX'),
			'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
			'shop_details' => Configuration::get('PS_SHOP_DETAILS'),
		));
		return $this->smarty->fetch($this->getTemplate('supply-order-footer'));
	}
}

