<?php

class OrderDetail extends OrderDetailCore {

	public $iva_producto_recalculado;
	public $cantiad_producto_recalculo;
	public $orden_con_descuento = false;

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
		        

		///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
		$cartobj = new cart($cart->id);
		$cartRules = $cartobj->getCartRules();
		$descuento = $cartRules[0]['reduction_percent'];

		if ($descuento != "" && $descuento != 0){
			
			// calcular valores de cada producto
			$precio = $product['price'];
			$iva = $product['rate'];
			$this->cantiad_producto_recalculo = $product['cart_quantity'];
			

			$descuento_sin_iva = ($precio * $descuento) / 100;
			$this->iva_producto_recalculado = ( $precio - (( $precio * $descuento) / 100 )) * ($iva / 100);

			
			/*$this->unit_price_tax_excl = $precio - $descuento_sin_iva;
			$this->unit_price_tax_incl = $this->unit_price_tax_excl + $this->iva_producto_recalculado;*/

			$unit_price_tax_excl = $precio - $descuento_sin_iva;
			$unit_price_tax_incl = $unit_price_tax_excl + $this->iva_producto_recalculado;

			$this->total_price_tax_excl = $unit_price_tax_excl * $this->cantiad_producto_recalculo;
			$this->total_price_tax_incl = $unit_price_tax_incl * $this->cantiad_producto_recalculo;

			$this->reduction_percent = $descuento;

			$this->orden_con_descuento = true;
			
			/* 
				echo "precio: ".$precio."<br>";
				echo "descuento_sin_iva: ".$descuento_sin_iva."<br>";
				echo "this->iva_producto_recalculado: ".$this->iva_producto_recalculado."<br>";
				echo "this->unit_price_tax_excl: ".$unit_price_tax_excl."<br>";
				echo "this->unit_price_tax_incl: ".$unit_price_tax_incl."<br>";
				echo "this->total_price_tax_excl: ".$this->total_price_tax_excl."<br>";
				echo "this->total_price_tax_incl: ".$this->total_price_tax_incl."<br>";
				echo "this->reduction_percent: ".$this->reduction_percent."<br>"; 
				die(); 
			*/
		
		}
		///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///



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

		$ratio = $this->unit_price_tax_excl / $order->total_products;
		$order_reduction_amount = $order->total_discounts_tax_excl * $ratio;
		$discounted_price_tax_excl = $this->unit_price_tax_excl - $order_reduction_amount;

		$values = '';
		foreach ($this->tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $amount)
		{
			$unit_amount = (float)Tools::ps_round($amount, 2);
			$total_amount = $unit_amount * $this->product_quantity;

			///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
			if ($this->orden_con_descuento){				
				$unit_amount = $this->iva_producto_recalculado;
				$total_amount = $unit_amount * $this->cantiad_producto_recalculo;
			}
			///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
			
			$values .= '('.(int)$this->id.','.(float)$id_tax.','.$unit_amount.','.(float)$total_amount.'),';
		}

		if ($replace)
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$this->id);

		$values = rtrim($values, ',');
		$sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax` (id_order_detail, id_tax, unit_amount, total_amount)
				VALUES '.$values;
		
		return Db::getInstance()->execute($sql);
	}

}
