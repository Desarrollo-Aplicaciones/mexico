<?php

class OrderDetail extends OrderDetailCore {

	protected function create(Order $order, Cart $cart, $product, $id_order_state, $id_order_invoice, $use_taxes = true, $id_warehouse = 0)
	{

		if ($use_taxes)
			$this->tax_calculator = new TaxCalculator();

		$this->id = null;

		$this->product_id = (int)($product['id_product']);
		$this->product_attribute_id = (int)($product['id_product_attribute'] ? (int)($product['id_product_attribute']) : null);
		$this->product_name = $product['name'].
			((isset($product['attributes']) && $product['attributes'] != null) ?
				' - '.$product['attributes'] : '');

		$this->product_quantity = (int)($product['cart_quantity']);
		$this->product_ean13 = empty($product['ean13']) ? null : pSQL($product['ean13']);
		$this->product_upc = empty($product['upc']) ? null : pSQL($product['upc']);
		$this->product_reference = empty($product['reference']) ? null : pSQL($product['reference']);
		$this->product_supplier_reference = empty($product['supplier_reference']) ? null : pSQL($product['supplier_reference']);
		$this->product_weight = (float)($product['id_product_attribute'] ? $product['weight_attribute'] : $product['weight']);
		$this->id_warehouse = $id_warehouse;

		$this->tax_rate = Tax::getProductTaxRate( (int)$this->product_id );

		$productQuantity = (int)(Product::getQuantity($this->product_id, $this->product_attribute_id));
		$this->product_quantity_in_stock = ($productQuantity - (int)($product['cart_quantity']) < 0) ?
			$productQuantity : (int)($product['cart_quantity']);

		$this->setVirtualProductInformation($product);
		$this->checkProductStock($product, $id_order_state);

		if ($use_taxes)
			$this->setProductTax($order, $product);
		$this->setShippingCost($order, $product);
		$this->setDetailProductPrice($order, $cart, $product);

		// Set order invoice id
		$this->id_order_invoice = (int)$id_order_invoice;
		
		// Set shop id
		$this->id_shop = (int)$product['id_shop'];

		// $cart = new cart($cart->id);
		$cartRule = $cart->getCartRules();
		if ( !empty($cartRule) && $cartRule[0]['reduction_product'] > 0 && $cartRule[0]['reduction_product'] == (int)($product['id_product']) ) {
			$this->reduction_percent = $cartRule[0]['reduction_percent'];
			$this->reduction_amount = $cartRule[0]['reduction_amount'];
		}

		// Add new entry to the table
		$this->save();

		if ($use_taxes)
			$this->saveTaxCalculator($order);
		unset($this->tax_calculator);
	}



	public function saveTaxCalculator(Order $order, $replace = false)
	{
		// Nothing to save
		if ($this->tax_calculator == null)
			return true;

		if (!($this->tax_calculator instanceOf TaxCalculator))
			return false;

		if (count($this->tax_calculator->taxes) == 0)
			return true;

		if ($order->total_products <= 0)
			return true;
		
		$values = '';

		// se deshabilita porque se realizan mal los calculos del iva del producto
		/*$ratio = $this->unit_price_tax_excl / $order->total_products;
		$order_reduction_amount = $order->total_discounts_tax_excl * $ratio;
		$discounted_price_tax_excl = $this->unit_price_tax_excl - $order_reduction_amount;

		foreach ($this->tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $amount)
		{
			$unit_amount = (float)Tools::ps_round($amount, 2);
			$total_amount = $unit_amount * $this->product_quantity;
			
			$values .= '('.(int)$this->id.','.(float)$id_tax.','.$unit_amount.','.(float)$total_amount.'),';
		}*/

		$tax = new Tax();
		$id_tax = $tax->getTaxIdByTaxPercent( $this->tax_rate );
		$id_tax = $id_tax['id_tax'];

		$unit_amount = (float)Tools::ps_round( $this->unit_price_tax_incl - $this->unit_price_tax_excl, 2 );
		$total_amount = $unit_amount * $this->product_quantity;

		$values .= '('.(int)$this->id.','.(float)$id_tax.','.$unit_amount.','.(float)$total_amount.'),';

		if ($replace)
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$this->id);

		$values = rtrim($values, ',');
		$sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
				VALUES '.$values;
		
		return Db::getInstance()->execute($sql);
	}

}
