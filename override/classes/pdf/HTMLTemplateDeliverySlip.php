<?php

class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
	public function __construct($order_invoice, $smarty)
	{
			$this->order_invoice = $order_invoice;
		if (Tools::isSubmit('noInvoice')) {
			$this->order = new Order($order_invoice->id);
		}
		 else {
			
			$this->order = new Order($this->order_invoice->id_order);
		}
		$this->smarty = $smarty;

		// header informations
		$this->date = Tools::displayDate($this->order->invoice_date);
		$this->title = HTMLTemplateDeliverySlip::l('Delivery').' #'.Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id).sprintf('%06d', $this->order_invoice->delivery_number);

		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}
	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
		$delivery_address = new Address((int)$this->order->id_address_delivery);
		$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		$formatted_invoice_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$invoice_address = new Address((int)$this->order->id_address_invoice);
			$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
		}

		// consulta de las nostas o mensajes agregadas a la orden
		$sql ='SELECT GROUP_CONCAT(CONCAT(UPPER(LEFT(mes.message, 1)), LOWER(SUBSTRING(mes.message, 2)))) AS note, cart.date_delivery AS fecha, cart.time_windows AS hora
				FROM ps_orders odr
				LEFT JOIN ps_message mes ON ( odr.id_order = mes.id_order AND mes.id_employee = 0 AND mes.id_customer != 0 )
				LEFT JOIN ps_cart cart ON ( odr.id_cart = cart.id_cart )
				WHERE odr.id_order = '.(int)$this->order->id." ;";

		$message = Db::getInstance()->ExecuteS( $sql );
		
		$date_delivery = $message[0]['fecha'];
		$time_delivery = $message[0]['hora'];
		$message = $message[0]['note'];
		
		$carrier = new Carrier($this->order->id_carrier);
		$carrier->name = ($carrier->name == '0' ? Configuration::get('PS_SHOP_NAME') : $carrier->name);
		$this->smarty->assign(array(
			'order' => $this->order,
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'order_invoice' => $this->order_invoice,
			'carrier' => $carrier,
			'note' => $message,
			'date_delivery' => $date_delivery,
			'time_delivery' => $time_delivery
		));
		if ($this->order_invoice){
			$this->smarty->assign(array(
				'order_details' => $this->order_invoice->getProducts()
			));
		}else{
			$this->smarty->assign(array(
				'order_details' => $this->order->getProducts()
			));
		}
		return $this->smarty->fetch($this->getTemplate('delivery-slip'));
	}
	public function getFilename()
	{
		return Configuration::get('PS_DELIVERY_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop)."id_".sprintf('%06d', $this->order->id).'.pdf';
	}

}
