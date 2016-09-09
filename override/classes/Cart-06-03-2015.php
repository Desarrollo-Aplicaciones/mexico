<?php  
 
class Cart extends CartCore {

  	public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null, $express = false)
	{
		
		if ((isset(Context::getContext()->cookie->check_xps) && Context::getContext()->cookie->check_xps)
			|| (isset(Context::getContext()->cart->check_xps) && Context::getContext()->cart->check_xps)
			|| Tools::getValue('express'))
		{
			$a=Context::getContext()->cart->id_address_delivery;
			//echo "<pre>";print_r(Context::getContext()->cart->check_xps); echo "</pre>"; exit();
			$val_total=0;
			if (Context::getContext()->cart->_products) {
				foreach (Context::getContext()->cart->_products as $key => $value) {
					$val_total += $value['total_wt']; //valor total de la compra sin impuestos
				}
			}
			$subtotal = $val_total;
			return $this->valorExpress($a,$subtotal);
		}

		// calcular el precio de envio por tabla ps_carrier_city y codigo postal
		
		//echo "<br><br>CART: ".
		$sql = 'SELECT cac.precio_kilo, car.id_carrier, crp.delimiter2, cac.tarifa, car2.id_carrier AS carrier_cp, 
				adtrcp.precio AS precio_cp, crp2.delimiter2 AS delimiter2_cp
			FROM '._DB_PREFIX_.'address adr 
			LEFT JOIN '._DB_PREFIX_.'address_city adc ON ( adc.id_address = adr.id_address ) 
			LEFT JOIN '._DB_PREFIX_.'carrier_city cac ON ( cac.id_city_des = adc.id_city ) 
			LEFT JOIN '._DB_PREFIX_.'carrier car ON ( car.id_reference = cac.id_carrier AND car.deleted = 0 AND car.active=1 ) 
			LEFT JOIN '._DB_PREFIX_.'range_price crp ON ( crp.id_carrier = car.id_carrier )
			LEFT JOIN '._DB_PREFIX_.'cities_col cc ON ( cc.id_city = adc.id_city )
			LEFT JOIN '._DB_PREFIX_.'state s ON ( s.id_state = cc.id_state AND s.id_country = '.(int)Configuration::get('PS_COUNTRY_DEFAULT').')
			LEFT JOIN '._DB_PREFIX_.'precio_tr_codpos adtrcp ON ( adr.postcode = adtrcp.codigo_postal )
			LEFT JOIN '._DB_PREFIX_.'carrier car2 ON (car2.id_reference = adtrcp.id_carrier AND car2.deleted = 0 AND car2.active=1)
			LEFT JOIN '._DB_PREFIX_.'range_price crp2 ON ( crp2.id_carrier = car2.id_carrier )
			WHERE adc.id_address='.Context::getContext()->cart->id_address_delivery.'
			AND ( ( car.id_carrier IS NOT NULL AND crp.delimiter2 IS NOT NULL ) 
						OR ( car2.id_carrier IS NOT NULL AND crp2.delimiter2 IS NOT NULL ) 
					)
			GROUP BY car.id_carrier, car2.id_carrier
			ORDER BY adtrcp.precio ASC, cac.precio_kilo ASC, crp.delimiter2 DESC, crp2.delimiter2 DESC';

			$resultado=Db::getInstance()->executeS($sql);
		if( count($resultado) > 0 ) {

			if ( !isset($resultado[0]['precio_kilo']) && !isset($resultado[0]['precio_cp']) )
			{
				//echo "<br>1:".
				$val="Ciudad sin costo de envio";
			} else {
				//echo "<br>2:".
				$val = $resultado[0]['precio_cp']? $resultado[0]['precio_cp'] : $resultado[0]['precio_kilo'];
			}

			$cobroenvio = 0; // 0 -> cobrar envio
			$found_carrier = 0;
			$delimitador_envio = 0;

			if ( isset( $resultado[0]['carrier_cp']) && $resultado[0]['carrier_cp'] !=  NULL ) {

				Context::getContext()->cart->id_carrier = $resultado[0]['carrier_cp'];
				$delimitador_envio = $resultado[0]['delimiter2_cp'];
				$found_carrier = 1;
				$cobroenvio = $resultado[0]['tarifa']? $resultado[0]['tarifa'] : 0;

			} elseif ( isset( $resultado[0]['id_carrier']) && $resultado[0]['id_carrier'] !=  NULL ) {

				Context::getContext()->cart->id_carrier = $resultado[0]['id_carrier'];
				$delimitador_envio = $resultado[0]['delimiter2'];
				$found_carrier = 1;
				$cobroenvio = $resultado[0]['tarifa']? $resultado[0]['tarifa'] : 0;
			}

			if ( $found_carrier == 1 ) {

				$val_total=0;
	                        
	            if (Context::getContext()->cart->_products) {

	                foreach (Context::getContext()->cart->_products as $key => $value) {
	                    $val_total += $value['total_wt']; //valor total de la compra sin impuestos
	                }
	            }
	            //echo "\n<br> val_total: ".$val_total." ? > delimiter2:".$delimitador_envio." | cobroenvio: ".$cobroenvio;

				$valtot_tax = (round( (round($val_total*1.16)/100) *2 , 0)/ 2 ) * 100; //valor con impuesto 16% y redondeado

				if ( $val_total > $delimitador_envio && $cobroenvio == 1) { // si total de compra es mayor al valor para no cobrar envio
					$val=0;
				}
			}
		} else {
			$val="Ciudad sin costo de envio";
		}
		//echo "<br>tot:".$val;
		return  $val; //$total_shipping; 
	}




	public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null, $express = false)
	{	
		if ((isset(Context::getContext()->cookie->check_xps) && Context::getContext()->cookie->check_xps)
			|| (isset(Context::getContext()->cart->check_xps) && Context::getContext()->cart->check_xps)
			|| Tools::getValue('express'))
		{
			$a = Context::getContext()->cart->id_address_delivery;
			if (Context::getContext()->cart->_products) {
				$val_total=0;
				foreach (Context::getContext()->cart->_products as $key => $value) {
					$val_total += $value['total_wt']; //valor total de la compra sin impuestos
				}
			}
			$subtotal = $val_total;
			return $this->valorExpress($a,$subtotal);
		}

		//echo "<br>\n 2: ".
		$sql = 'SELECT cac.precio_kilo, car.id_carrier, crp.delimiter2, cac.tarifa, car2.id_carrier AS carrier_cp, 
				adtrcp.precio AS precio_cp, crp2.delimiter2 AS delimiter2_cp
			FROM '._DB_PREFIX_.'address adr 
			LEFT JOIN '._DB_PREFIX_.'address_city adc ON ( adc.id_address = adr.id_address ) 
			LEFT JOIN '._DB_PREFIX_.'carrier_city cac ON ( cac.id_city_des = adc.id_city ) 
			LEFT JOIN '._DB_PREFIX_.'carrier car ON ( car.id_reference = cac.id_carrier AND car.deleted = 0 AND car.active=1 ) 
			LEFT JOIN '._DB_PREFIX_.'range_price crp ON ( crp.id_carrier = car.id_carrier )
			LEFT JOIN '._DB_PREFIX_.'cities_col cc ON ( cc.id_city = adc.id_city )
			LEFT JOIN '._DB_PREFIX_.'state s ON ( s.id_state = cc.id_state AND s.id_country = '.(int)Configuration::get('PS_COUNTRY_DEFAULT').')
			LEFT JOIN '._DB_PREFIX_.'precio_tr_codpos adtrcp ON ( adr.postcode = adtrcp.codigo_postal )
			LEFT JOIN '._DB_PREFIX_.'carrier car2 ON (car2.id_reference = adtrcp.id_carrier AND car2.deleted = 0 AND car2.active=1)
			LEFT JOIN '._DB_PREFIX_.'range_price crp2 ON ( crp2.id_carrier = car2.id_carrier )
			WHERE adc.id_address='.Context::getContext()->cart->id_address_delivery.'
			AND ( ( car.id_carrier IS NOT NULL AND crp.delimiter2 IS NOT NULL ) 
						OR ( car2.id_carrier IS NOT NULL AND crp2.delimiter2 IS NOT NULL ) 
					)
			GROUP BY car.id_carrier, car2.id_carrier
			ORDER BY adtrcp.precio ASC, cac.precio_kilo ASC, crp.delimiter2 DESC, crp2.delimiter2 DESC';
		
		$resultado=Db::getInstance()->executeS($sql);

		if( count($resultado) > 0 ) {

			if ( !$resultado[0]['precio_kilo'] && !$resultado[0]['precio_cp'] )
			{
				$val="Ciudad sin costo de envio";
			} else {
				$val = $resultado[0]['precio_cp']? $resultado[0]['precio_cp'] : $resultado[0]['precio_kilo'];
			} 

			$cobroenvio = 0; // 0 -> cobrar envio
			$found_carrier = 0;
			$delimitador_envio = 0;

			$shipping_cost = (float)Tools::ps_round((float)$val, 2);
				
				if ( isset( $resultado[0]['carrier_cp']) && $resultado[0]['carrier_cp'] !=  NULL ) {

					Context::getContext()->cart->id_carrier = $resultado[0]['carrier_cp'];
					$delimitador_envio = $resultado[0]['delimiter2_cp'];
					$found_carrier = 1;
					$cobroenvio = $resultado[0]['tarifa']? $resultado[0]['tarifa'] : 0;

				} elseif ( isset( $resultado[0]['id_carrier']) && $resultado[0]['id_carrier'] !=  NULL ) {

					Context::getContext()->cart->id_carrier = $resultado[0]['id_carrier'];
					$delimitador_envio = $resultado[0]['delimiter2'];
					$found_carrier = 1;
					$cobroenvio = $resultado[0]['tarifa']? $resultado[0]['tarifa'] : 0;
				}
				
				$val_total=0;

				if ( $found_carrier == 1 ) {				
		                        
		            if (Context::getContext()->cart->_products) {

		                foreach (Context::getContext()->cart->_products as $key => $value) {
		                    $val_total += $value['total_wt']; //valor total de la compra sin impuestos
		                }
		            }
		            //echo "\n<br> val_total: ".$val_total." ? > delimiter2:".$delimitador_envio;

					$valtot_tax = (round( (round($val_total*1.16)/100) *2 , 0)/ 2 ) * 100; //valor con impuesto 16% y redondeado

					if ( $val_total > $delimitador_envio && $cobroenvio == 1) { // si total de compra es mayor al valor para no cobrar envio
						$shipping_cost=0;
					}
				}
		} else {
			$shipping_cost="Ciudad sin costo de envio";
		}

		return  $shipping_cost;
	}
        
        	public function removeCartRules()
	{
                $cart_rules=$this->getCartRules();     
		Cache::clean('Cart::getCartRules'.$this->id.'-'.CartRule::FILTER_ACTION_ALL);
		Cache::clean('Cart::getCartRules'.$this->id.'-'.CartRule::FILTER_ACTION_SHIPPING);
		Cache::clean('Cart::getCartRules'.$this->id.'-'.CartRule::FILTER_ACTION_REDUCTION);
		Cache::clean('Cart::getCartRules'.$this->id.'-'.CartRule::FILTER_ACTION_GIFT);

		$result = Db::getInstance()->execute('
		DELETE FROM `'._DB_PREFIX_.'cart_cart_rule`
		WHERE  `id_cart` = '.(int)$this->id.';');
                
               
                
              foreach ($cart_rules as $value) {
                  $cart_rule = new CartRule($value['id_cart_rule'], Configuration::get('PS_LANG_DEFAULT'));
		if ((int)$cart_rule->gift_product)
			$this->updateQty(1, $cart_rule->gift_product, $cart_rule->gift_product_attribute, null, 'down', 0, null, false);
                }
		
		
		
		return $result;
	}
	public function valorExpress($id,$subtotal)
	{
		$sql="SELECT ac.id_address AS id ,
					 express_abajo as abajo,
					 express_arriba as arriba
			FROM ps_carrier_city AS cc
			Inner Join ps_address_city AS ac
			ON ac.id_city=cc.id_city_des
			WHERE id_address=".$id;
		$express=Db::getInstance()->getRow($sql);
		$sql2 = 'SELECT cac.precio_kilo,
						car.id_carrier,
						crp.delimiter2 as umbral
			FROM '._DB_PREFIX_.'address adr
			INNER JOIN '._DB_PREFIX_.'address_city adc ON (adc.id_address=adr.id_address)
			INNER JOIN '._DB_PREFIX_.'carrier_city cac ON (cac.id_city_des = adc.id_city)
			INNER JOIN '._DB_PREFIX_.'carrier car ON (car.id_reference = cac.id_carrier AND car.deleted = 0 AND car.active=1)
			INNER JOIN '._DB_PREFIX_.'range_price crp ON (crp.id_carrier = car.id_carrier)
			WHERE adc.id_address='.$id.'
			ORDER BY cac.precio_kilo';
		$resultado=Db::getInstance()->getRow($sql2);
		if($subtotal>$resultado['umbral'])
		{
			return $express['arriba'];
		}
		else
		{
			return $express['abajo'];
		}
	}
	public function expressProduct(){
		$compare = array();
		$compare2 = array();
		$lista = "(";
		foreach($this->_products as $productos)
		{
			$compare[$productos["id_product"]]=$productos["cart_quantity"];
			$lista = $lista.$productos["id_product"].",";
		}
		$lista = substr($lista, 0, -1).")";
		$sql= 'SELECT sod.id_product AS id, COUNT(sod.id_product)AS cantidad
			FROM `'._DB_PREFIX_.'supply_order_detail` AS sod
			INNER JOIN `'._DB_PREFIX_.'supply_order_icr` AS soi
			ON sod.id_supply_order_detail = soi.id_supply_order_detail
			INNER JOIN `'._DB_PREFIX_.'icr` AS icr
			ON soi.id_icr = icr.id_icr
			WHERE icr.id_estado_icr = 2
			AND sod.id_product IN '.$lista.' GROUP BY sod.id_product;';
		$resultado=Db::getInstance()->executeS($sql);
		foreach($resultado as $res)
		{
			$compare2[$res["id"]]=$res["cantidad"];
		}
		unset($res);
		if(array_diff_key($compare, $compare2))
		{
			return false;
		}
		else
		{
			foreach($compare as $a => $valor)
			{
				$res[$a] = $compare2[$a]-$compare[$a];
				if ($res[$a]<0)
				{
					return false;
				}
			}
			return true;
		}
	}
	
	///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
	public function recalculartotalconcupon($descuento){
		$products = $this->getProducts();
		
		$total_todo_iva_producto = 0;
		$total_todo_descuento_sin_iva = 0;
		$total_todo_sin_iva = 0;

		foreach ($products as $value) {
			$precio = $value['price'];
			$iva = $value['rate'];
			$cantidad = $value['cart_quantity'];
			
			$total_sin_iva = $precio * $cantidad;
			$total_todo_sin_iva += $total_sin_iva;

			$descuento_sin_iva = ($total_sin_iva * $descuento) / 100;
			$total_todo_descuento_sin_iva += $descuento_sin_iva;

			$iva_producto = ( $precio - (( $precio * $descuento) / 100 )) * ($iva / 100) * $cantidad;
			$total_todo_iva_producto += $iva_producto;
		}

		$total = $total_todo_sin_iva + $total_todo_iva_producto - $total_todo_descuento_sin_iva;

		return array(
			'total' => $total,
			'total_todo_descuento_sin_iva' => $total_todo_descuento_sin_iva,
			'total_todo_sin_iva' => $total_todo_sin_iva,
			'total_todo_iva_producto' => $total_todo_iva_producto
		);
	}
	///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO  PORCENTAJE***///

	public function getSummaryDetails($id_lang = null, $refresh = false)
	{
		$context = Context::getContext();
		if (!$id_lang)
			$id_lang = $context->language->id;

		$delivery = new Address((int)$this->id_address_delivery);
		$invoice = new Address((int)$this->id_address_invoice);

		// New layout system with personalization fields
		$formatted_addresses['delivery'] = AddressFormat::getFormattedLayoutData($delivery);		
		$formatted_addresses['invoice'] = AddressFormat::getFormattedLayoutData($invoice);

		$base_total_tax_inc = $this->getOrderTotal(true);
		$base_total_tax_exc = $this->getOrderTotal(false);
		
		$total_tax = $base_total_tax_inc - $base_total_tax_exc;

		if ($total_tax < 0)
			$total_tax = 0;
		
		$currency = new Currency($this->id_currency);
		
		$products = $this->getProducts($refresh);
		$gift_products = array();
		$cart_rules = $this->getCartRules();
		$total_shipping = $this->getTotalShippingCost();
		$total_shipping_tax_exc = $this->getTotalShippingCost(null, false);
		$total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
		$total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);
		$total_discounts = $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
		$total_discounts_tax_exc = $this->getOrderTotal(false, Cart::ONLY_DISCOUNTS);
		
		// The cart content is altered for display
		foreach ($cart_rules as &$cart_rule)
		{
			// If the cart rule is automatic (wihtout any code) and include free shipping, it should not be displayed as a cart rule but only set the shipping cost to 0
			if ($cart_rule['free_shipping'] && (empty($cart_rule['code']) || preg_match('/^'.CartRule::BO_ORDER_CODE_PREFIX.'[0-9]+/', $cart_rule['code'])))
			{
				$cart_rule['value_real'] -= $total_shipping;
				$cart_rule['value_tax_exc'] -= $total_shipping_tax_exc;
				$cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
				$cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
				if ($total_discounts > $cart_rule['value_real'])
					$total_discounts -= $total_shipping;
				if ($total_discounts_tax_exc > $cart_rule['value_tax_exc'])
					$total_discounts_tax_exc -= $total_shipping_tax_exc;

				// Update total shipping
				$total_shipping = 0;
				$total_shipping_tax_exc = 0;
			}
			if ($cart_rule['gift_product'])
			{
				foreach ($products as $key => &$product)
					if (empty($product['gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute'])
					{
						// Update total products
						$total_products_wt = Tools::ps_round($total_products_wt - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$total_products = Tools::ps_round($total_products - $product['price'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						
						// Update total discounts
						$total_discounts = Tools::ps_round($total_discounts - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$total_discounts_tax_exc = Tools::ps_round($total_discounts_tax_exc - $product['price'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
					
						// Update cart rule value
						$cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$cart_rule['value_tax_exc'] = Tools::ps_round($cart_rule['value_tax_exc'] - $product['price'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						
						// Update product quantity
						$product['total_wt'] = Tools::ps_round($product['total_wt'] - $product['price_wt'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$product['total'] = Tools::ps_round($product['total'] - $product['price'], (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$product['cart_quantity']--;
						
						if (!$product['cart_quantity'])
							unset($products[$key]);
						
						// Add a new product line
						$gift_product = $product;
						$gift_product['cart_quantity'] = 1;
						$gift_product['price'] = 0;
						$gift_product['price_wt'] = 0;
						$gift_product['total_wt'] = 0;
						$gift_product['total'] = 0;
						$gift_product['gift'] = true;
						$gift_products[] = $gift_product;
						
						break; // One gift product per cart rule
					}
			}
		}

		foreach ($cart_rules as $key => &$cart_rule)
			if ($cart_rule['value_real'] == 0)
				unset($cart_rules[$key]);


        


		///*** INICIO VALIDACION DIRECCION FARMALISTO ***///
		$validateaddress = $this->validationaddressfarmalisto();
        if ($validateaddress){
            $total_shipping_tax_exc = 0;
        }
        ///*** FIN VALIDACION DIRECCION FARMALISTO ***///


		///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
		$cartRules = $this->getCartRules();
		$descuento = $cartRules[0]['reduction_percent'];
		$validacartrule = "false";
		if ($descuento != "" && $descuento != 0){
			$totalorderdescuento = $this->recalculartotalconcupon($descuento);

			$base_total_tax_inc = $totalorderdescuento['total'] + $total_shipping_tax_exc;
			$total_products_wt =  $totalorderdescuento['total_todo_sin_iva'] + $totalorderdescuento['total_todo_iva_producto'];
			$total_discounts = $totalorderdescuento['total_todo_descuento_sin_iva'];
			$base_total_tax_exc = $totalorderdescuento['total_todo_sin_iva'];
			$total_tax = $totalorderdescuento['total_todo_iva_producto'];
			$cart_rules[0]['value_real'] = $totalorderdescuento['total_todo_descuento_sin_iva'];
			$total_discounts_tax_exc = $totalorderdescuento['total_todo_descuento_sin_iva'];
			$validacartrule = "true";
			$total_products_recalculado = $totalorderdescuento['total_todo_sin_iva'] + $totalorderdescuento['total_todo_iva_producto'];
		}
		///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///

		return array(
			'delivery' => $delivery,
			'delivery_state' => State::getNameById($delivery->id_state),
			'invoice' => $invoice,
			'invoice_state' => State::getNameById($invoice->id_state),
			'formattedAddresses' => $formatted_addresses,
			'products' => array_values($products),
			'gift_products' => $gift_products,
			'discounts' => $cart_rules,
			'is_virtual_cart' => (int)$this->isVirtualCart(),
			'total_discounts' => $total_discounts,
			'total_discounts_tax_exc' => $total_discounts_tax_exc,
			'total_wrapping' => $this->getOrderTotal(true, Cart::ONLY_WRAPPING),
			'total_wrapping_tax_exc' => $this->getOrderTotal(false, Cart::ONLY_WRAPPING),
			'total_shipping' => $total_shipping,
			'total_shipping_tax_exc' => $total_shipping_tax_exc,
			'total_products_wt' => $total_products_wt,
			'total_products' => $total_products,
			'total_price' => $base_total_tax_inc,
			'total_tax' => $total_tax,
			'total_price_without_tax' => $base_total_tax_exc,
			'is_multi_address_delivery' => $this->isMultiAddressDelivery() || ((int)Tools::getValue('multi-shipping') == 1),
			'free_ship' => $total_shipping ? 0 : 1,
			'carrier' => new Carrier($this->id_carrier, $id_lang),
			'validacartrule' => $validacartrule,
			'total_products_recalculado' => round($total_products_recalculado),
		);
	}

	public function getProducts($refresh = false, $id_product = false, $id_country = null)
	{
		if (!$this->id)
			return array();
		// Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
		if ($this->_products !== null && !$refresh)
		{
			// Return product row with specified ID if it exists
			if (is_int($id_product))
			{
				foreach ($this->_products as $product)
					if ($product['id_product'] == $id_product)
						return array($product);
				return array();
			}
			return $this->_products;
		}

		// Build query
		$sql = new DbQuery();

		// Build SELECT
		$sql->select('cp.`id_product_attribute`, cp.`id_product`, cp.`quantity` AS cart_quantity, cp.id_shop, pl.`name`, p.`is_virtual`,
						pl.`description_short`, pl.`available_now`, pl.`available_later`, p.`id_product`, product_shop.`id_category_default`, p.`id_supplier`,
						p.`id_manufacturer`, product_shop.`on_sale`, product_shop.`ecotax`, product_shop.`additional_shipping_cost`,
						product_shop.`available_for_order`, product_shop.`price`, product_shop.`active`, product_shop.`unity`, product_shop.`unit_price_ratio`, 
						stock.`quantity` AS quantity_available, p.`width`, p.`height`, p.`depth`, stock.`out_of_stock`, p.`weight`,
						p.`date_add`, p.`date_upd`, IFNULL(stock.quantity, 0) as quantity, pl.`link_rewrite`, cl.`link_rewrite` AS category,
						CONCAT(LPAD(cp.`id_product`, 10, 0), LPAD(IFNULL(cp.`id_product_attribute`, 0), 10, 0), IFNULL(cp.`id_address_delivery`, 0)) AS unique_id, cp.id_address_delivery,
						product_shop.`wholesale_price`, product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference');

		// Build FROM
		$sql->from('cart_product', 'cp');

		// Build JOIN
		$sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
		$sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_shop=cp.id_shop AND product_shop.id_product = p.id_product)');
		$sql->leftJoin('product_lang', 'pl', '
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop')
		);

		$sql->leftJoin('category_lang', 'cl', '
			product_shop.`id_category_default` = cl.`id_category`
			AND cl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop')
		);

		$sql->leftJoin('product_supplier', 'ps', 'ps.id_product=cp.id_product AND ps.id_product_attribute=cp.id_product_attribute AND ps.id_supplier=p.id_supplier');

		// @todo test if everything is ok, then refactorise call of this method
		$sql->join(Product::sqlStock('cp', 'cp'));

		// Build WHERE clauses
		$sql->where('cp.`id_cart` = '.(int)$this->id);
		if ($id_product)
			$sql->where('cp.`id_product` = '.(int)$id_product);
		$sql->where('p.`id_product` IS NOT NULL');

		// Build GROUP BY
		$sql->groupBy('unique_id');

		// Build ORDER BY
		$sql->orderBy('p.id_product, cp.id_product_attribute, cp.date_add ASC');

		if (Customization::isFeatureActive())
		{
			$sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
			$sql->leftJoin('customization', 'cu',
				'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.id_product_attribute AND cu.id_cart='.(int)$this->id);
		}
		else
			$sql->select('NULL AS customization_quantity, NULL AS id_customization');

		if (Combination::isFeatureActive())
		{
			$sql->select('
				product_attribute_shop.`price` AS price_attribute, product_attribute_shop.`ecotax` AS ecotax_attr,
				IF (IFNULL(pa.`reference`, \'\') = \'\', p.`reference`, pa.`reference`) AS reference,
				(p.`weight`+ pa.`weight`) weight_attribute,
				IF (IFNULL(pa.`ean13`, \'\') = \'\', p.`ean13`, pa.`ean13`) AS ean13,
				IF (IFNULL(pa.`upc`, \'\') = \'\', p.`upc`, pa.`upc`) AS upc,
				pai.`id_image` as pai_id_image, il.`legend` as pai_legend,
				IFNULL(product_attribute_shop.`minimal_quantity`, product_shop.`minimal_quantity`) as minimal_quantity
			');

			$sql->leftJoin('product_attribute', 'pa', 'pa.`id_product_attribute` = cp.`id_product_attribute`');
			$sql->leftJoin('product_attribute_shop', 'product_attribute_shop', '(product_attribute_shop.id_shop=cp.id_shop AND product_attribute_shop.id_product_attribute = pa.id_product_attribute)');
			$sql->leftJoin('product_attribute_image', 'pai', 'pai.`id_product_attribute` = pa.`id_product_attribute`');
			$sql->leftJoin('image_lang', 'il', 'il.id_image = pai.id_image AND il.id_lang = '.(int)$this->id_lang);
		}
		else
			$sql->select(
				'p.`reference` AS reference, p.`ean13`,
				p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity'
			);
		$result = Db::getInstance()->executeS($sql);

		// Reset the cache before the following return, or else an empty cart will add dozens of queries
		$products_ids = array();
		$pa_ids = array();
		if ($result)
			foreach ($result as $row)
			{
				$products_ids[] = $row['id_product'];
				$pa_ids[] = $row['id_product_attribute'];
			}
		// Thus you can avoid one query per product, because there will be only one query for all the products of the cart
		Product::cacheProductsFeatures($products_ids);
		Cart::cacheSomeAttributesLists($pa_ids, $this->id_lang);

		$this->_products = array();
		if (empty($result))
			return array();

		$cart_shop_context = Context::getContext()->cloneContext();
		foreach ($result as &$row)
		{
			if (isset($row['ecotax_attr']) && $row['ecotax_attr'] > 0)
				$row['ecotax'] = (float)$row['ecotax_attr'];

			$row['stock_quantity'] = (int)$row['quantity'];
			// for compatibility with 1.2 themes
			$row['quantity'] = (int)$row['cart_quantity'];

			if (isset($row['id_product_attribute']) && (int)$row['id_product_attribute'] && isset($row['weight_attribute']))
				$row['weight'] = (float)$row['weight_attribute'];

			if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice')
				$address_id = (int)$this->id_address_invoice;
			else
				$address_id = (int)$row['id_address_delivery'];
			if (!Address::addressExists($address_id))
				$address_id = null;

			if ($cart_shop_context->shop->id != $row['id_shop'])
				$cart_shop_context->shop = new Shop((int)$row['id_shop']);

			if ($this->_taxCalculationMethod == PS_TAX_EXC)
			{
				$row['price'] = Product::getPriceStatic(
					(int)$row['id_product'],
					false,
					isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
					2,
					null,
					false,
					true,
					(int)$row['cart_quantity'],
					false,
					((int)$this->id_customer ? (int)$this->id_customer : null),
					(int)$this->id,
					((int)$address_id ? (int)$address_id : null),
					$specific_price_output,
					true,
					true,
					$cart_shop_context
				); // Here taxes are computed only once the quantity has been applied to the product price

				$row['price_wt'] = Product::getPriceStatic(
					(int)$row['id_product'],
					true,
					isset($row['id_product_attribute']) ? (int)$row['id_product_attribute'] : null,
					2,
					null,
					false,
					true,
					(int)$row['cart_quantity'],
					false,
					((int)$this->id_customer ? (int)$this->id_customer : null),
					(int)$this->id,
					((int)$address_id ? (int)$address_id : null),
					$null,
					true,
					true,
					$cart_shop_context
				);

				$tax_rate = Tax::getProductTaxRate((int)$row['id_product'], (int)$address_id);

				$row['total_wt'] = Tools::ps_round($row['price'] * (float)$row['cart_quantity'] * (1 + (float)$tax_rate / 100), 2);
				$row['total'] = $row['price'] * (int)$row['cart_quantity'];
			}
			else
			{
				$row['price'] = Product::getPriceStatic(
					(int)$row['id_product'],
					false,
					(int)$row['id_product_attribute'],
					2,
					null,
					false,
					true,
					$row['cart_quantity'],
					false,
					((int)$this->id_customer ? (int)$this->id_customer : null),
					(int)$this->id,
					((int)$address_id ? (int)$address_id : null),
					$specific_price_output,
					true,
					true,
					$cart_shop_context
				);

				$row['price_wt'] = Product::getPriceStatic(
					(int)$row['id_product'],
					true,
					(int)$row['id_product_attribute'],
					2,
					null,
					false,
					true,
					$row['cart_quantity'],
					false,
					((int)$this->id_customer ? (int)$this->id_customer : null),
					(int)$this->id,
					((int)$address_id ? (int)$address_id : null),
					$null,
					true,
					true,
					$cart_shop_context
				);
				
				// In case when you use QuantityDiscount, getPriceStatic() can be return more of 2 decimals
				$row['price_wt'] = Tools::ps_round($row['price_wt'], 2);
				$row['total_wt'] = $row['price_wt'] * (int)$row['cart_quantity'];
				$row['total'] = Tools::ps_round($row['price'] * (int)$row['cart_quantity'], 2);
			}

			if (!isset($row['pai_id_image']) || $row['pai_id_image'] == 0)
			{
				$row2 = Db::getInstance()->getRow('
					SELECT image_shop.`id_image` id_image, il.`legend`
					FROM `'._DB_PREFIX_.'image` i
					JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (i.id_image = image_shop.id_image AND image_shop.cover=1 AND image_shop.id_shop='.(int)$row['id_shop'].')
					LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$this->id_lang.')
					WHERE i.`id_product` = '.(int)$row['id_product'].' AND image_shop.`cover` = 1'
				);

				if (!$row2)
					$row2 = array('id_image' => false, 'legend' => false);
				else
					$row = array_merge($row, $row2);
			}
			else
			{
				$row['id_image'] = $row['pai_id_image'];
				$row['legend'] = $row['pai_legend'];
			}

			$row['reduction_applies'] = ($specific_price_output && (float)$specific_price_output['reduction']);
			$row['quantity_discount_applies'] = ($specific_price_output && $row['cart_quantity'] >= (int)$specific_price_output['from_quantity']);
			$row['id_image'] = Product::defineProductImage($row, $this->id_lang);
			$row['allow_oosp'] = Product::isAvailableWhenOutOfStock($row['out_of_stock']);
			$row['features'] = Product::getFeaturesStatic((int)$row['id_product']);

			if (array_key_exists($row['id_product_attribute'].'-'.$this->id_lang, self::$_attributesLists))
				$row = array_merge($row, self::$_attributesLists[$row['id_product_attribute'].'-'.$this->id_lang]);

			$row = Product::getTaxesInformations($row, $cart_shop_context);

			$this->_products[] = $row;
		}

		///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
		$cartRules = $this->getCartRules();
		$descuento = $cartRules[0]['reduction_percent'];

		if ($descuento != "" && $descuento != 0) {

			foreach ($this->_products as $key => $value) {

				$precio = $value['price'];
				$iva = $value['rate'];
				$cantidad = $value['cart_quantity'];
				
				$total_sin_iva = $precio * $cantidad;
				$iva_producto = ( $precio - (( $precio * $descuento) / 100 )) * ($iva / 100) * $cantidad;
				$total_producto_show = $total_sin_iva + $iva_producto ;

				$this->_products[$key]['price_wt'] = $total_producto_show / $cantidad;
				$this->_products[$key]['total_wt'] = $total_producto_show;
			}
		}
		///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***//

		return $this->_products;
	}


	public function getOrderTotal($with_taxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = true)
	{

		if (!$this->id)
			return 0;

		$type = (int)$type;
		$array_type = array(
			Cart::ONLY_PRODUCTS,
			Cart::ONLY_DISCOUNTS,
			Cart::BOTH,
			Cart::BOTH_WITHOUT_SHIPPING,
			Cart::ONLY_SHIPPING,
			Cart::ONLY_WRAPPING,
			Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING,
			Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING,
		);
		
		// Define virtual context to prevent case where the cart is not the in the global context
		$virtual_context = Context::getContext()->cloneContext();
		$virtual_context->cart = $this;

		


		if (!in_array($type, $array_type))
			die(Tools::displayError());

		$with_shipping = in_array($type, array(Cart::BOTH, Cart::ONLY_SHIPPING));

		
		
		// if cart rules are not used
		if ($type == Cart::ONLY_DISCOUNTS && !CartRule::isFeatureActive())
			return 0;


		// no shipping cost if is a cart with only virtuals products
		$virtual = $this->isVirtualCart();
		if ($virtual && $type == Cart::ONLY_SHIPPING)
			return 0;




		if ($virtual && $type == Cart::BOTH)
			$type = Cart::BOTH_WITHOUT_SHIPPING;

		if ($with_shipping)
		{
			
			if (is_null($products) && is_null($id_carrier)){

				$shipping_fees = $this->getTotalShippingCost(null, (boolean)$with_taxes);
			
		
			}else
				$shipping_fees = $this->getPackageShippingCost($id_carrier, (int)$with_taxes, null, $products);
				
		}

		else{
			$shipping_fees = 0;
			}
	

		if ($type == Cart::ONLY_SHIPPING){
			return $shipping_fees;}

		if ($type == Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING){
			$type = Cart::ONLY_PRODUCTS;}

		$param_product = true;
		if (is_null($products))
		{
			$param_product = false;
			$products = $this->getProducts();
		}


		if ($type == Cart::ONLY_PHYSICAL_PRODUCTS_WITHOUT_SHIPPING)
		{
			foreach ($products as $key => $product)
				if ($product['is_virtual'])
					unset($products[$key]);
			$type = Cart::ONLY_PRODUCTS;
		}



		$order_total = 0;
		if (Tax::excludeTaxeOption())
			$with_taxes = false;

		foreach ($products as $product) // products refer to the cart details
		{

			if ($virtual_context->shop->id != $product['id_shop'])
				$virtual_context->shop = new Shop((int)$product['id_shop']);



			if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_invoice')
				$address_id = (int)$this->id_address_invoice;
			else
				$address_id = (int)$product['id_address_delivery']; // Get delivery address of the product from the cart
			if (!Address::addressExists($address_id))
				$address_id = null;
			

			if ($this->_taxCalculationMethod == PS_TAX_EXC)
			{
				// Here taxes are computed only once the quantity has been applied to the product price
				$price = Product::getPriceStatic(
					(int)$product['id_product'],
					false,
					(int)$product['id_product_attribute'],
					2,
					null,
					false,
					true,
					$product['cart_quantity'],
					false,
					(int)$this->id_customer ? (int)$this->id_customer : null,
					(int)$this->id,
					$address_id,
					$null,
					true,
					true,
					$virtual_context
				);

				$total_ecotax = $product['ecotax'] * (int)$product['cart_quantity'];
				$total_price = $price * (int)$product['cart_quantity'];

				if ($with_taxes)
				{
					$product_tax_rate = (float)Tax::getProductTaxRate((int)$product['id_product'], (int)$address_id, $virtual_context);
					$product_eco_tax_rate = Tax::getProductEcotaxRate((int)$address_id);

					$total_price = ($total_price - $total_ecotax) * (1 + $product_tax_rate / 100);
					$total_ecotax = $total_ecotax * (1 + $product_eco_tax_rate / 100);
					$total_price = Tools::ps_round($total_price + $total_ecotax, 2);
				}

			}
			else
			{
				if ($with_taxes)
					$price = Product::getPriceStatic(
						(int)$product['id_product'],
						true,
						(int)$product['id_product_attribute'],
						2,
						null,
						false,
						true,
						$product['cart_quantity'],
						false,
						((int)$this->id_customer ? (int)$this->id_customer : null),
						(int)$this->id,
						((int)$address_id ? (int)$address_id : null),
						$null,
						true,
						true,
						$virtual_context
					);
				else
					$price = Product::getPriceStatic(
						(int)$product['id_product'],
						true,
						(int)$product['id_product_attribute'],
						2,
						null,
						false,
						true,
						$product['cart_quantity'],
						false,
						((int)$this->id_customer ? (int)$this->id_customer : null),
						(int)$this->id,
						((int)$address_id ? (int)$address_id : null),
						$null,
						true,
						true,
						$virtual_context
					);

				$total_price = Tools::ps_round($price * (int)$product['cart_quantity'], 2);

			}
			$order_total += $total_price;
		}
		

	// echo '<pre>3.';
	// print_r($total_price);
	// echo '<pre>4.';
	// print_r($with_taxes);

		$order_total_products = $order_total;


		if ($type == Cart::ONLY_DISCOUNTS)
			$order_total = 0;

		// Wrapping Fees
		$wrapping_fees = 0;
		if ($this->gift)
			$wrapping_fees = Tools::convertPrice(Tools::ps_round($this->getGiftWrappingPrice($with_taxes), 2), Currency::getCurrencyInstance((int)$this->id_currency));
		if ($type == Cart::ONLY_WRAPPING)
			return $wrapping_fees;

		$order_total_discount = 0;

		
				

		if (!in_array($type, array(Cart::ONLY_SHIPPING, Cart::ONLY_PRODUCTS)) && CartRule::isFeatureActive())
		{
			

			// First, retrieve the cart rules associated to this "getOrderTotal"
			if ($with_shipping || $type == Cart::ONLY_DISCOUNTS)
				$cart_rules = $this->getCartRules(CartRule::FILTER_ACTION_ALL);
			

			else
			{
				$cart_rules = $this->getCartRules(CartRule::FILTER_ACTION_REDUCTION);
				// Cart Rules array are merged manually in order to avoid doubles
				foreach ($this->getCartRules(CartRule::FILTER_ACTION_GIFT) as $tmp_cart_rule)
				{
					$flag = false;
					foreach ($cart_rules as $cart_rule)
						if ($tmp_cart_rule['id_cart_rule'] == $cart_rule['id_cart_rule'])
							$flag = true;
					if (!$flag)
						$cart_rules[] = $tmp_cart_rule;
				}
			}
			
			$id_address_delivery = 0;
			if (isset($products[0]))
				$id_address_delivery = (is_null($products) ? $this->id_address_delivery : $products[0]['id_address_delivery']);
			$package = array('id_carrier' => $id_carrier, 'id_address' => $id_address_delivery, 'products' => $products);
			

			// Then, calculate the contextual value for each one
			foreach ($cart_rules as $cart_rule)
			{
				// If the cart rule offers free shipping, add the shipping cost
				if (($with_shipping || $type == Cart::ONLY_DISCOUNTS) && $cart_rule['obj']->free_shipping)
					$order_total_discount += Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_SHIPPING, ($param_product ? $package : null), $use_cache), 2);
				
				
			
				// If the cart rule is a free gift, then add the free gift value only if the gift is in this package
				if ((int)$cart_rule['obj']->gift_product)
				{
					$in_order = false;
					if (is_null($products))
						$in_order = true;
					else
						foreach ($products as $product)
							if ($cart_rule['obj']->gift_product == $product['id_product'] && $cart_rule['obj']->gift_product_attribute == $product['id_product_attribute'])
								$in_order = true;

					if ($in_order)
						$order_total_discount += $cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_GIFT, $package, $use_cache);
				}

				// If the cart rule offers a reduction, the amount is prorated (with the products in the package)
				if ($cart_rule['obj']->reduction_percent > 0 || $cart_rule['obj']->reduction_amount > 0)
					$order_total_discount += Tools::ps_round($cart_rule['obj']->getContextualValue($with_taxes, $virtual_context, CartRule::FILTER_ACTION_REDUCTION, $package, $use_cache), 2);

				
			}


                        //$order_total_discount = min(Tools::ps_round($order_total_discount, 2), $wrapping_fees + $order_total_products + $shipping_fees);
			$order_total_discount = min(Tools::ps_round($order_total_discount, 1), $wrapping_fees + $order_total_products + $shipping_fees);
			$order_total -= $order_total_discount;
		}

				
		///*** INICIO VALIDACION DIRECCION FARMALISTO ***///
        $validateaddress = $this->validationaddressfarmalisto();
        if ($validateaddress){
            $shipping_fees = 0;
        }
        ///*** FIN VALIDACION DIRECCION FARMALISTO ***///		
		

		if ($type == Cart::BOTH)
			$order_total += $shipping_fees + $wrapping_fees;

		if ($order_total < 0 && $type != Cart::ONLY_DISCOUNTS)
			return 0;

		if ($type == Cart::ONLY_DISCOUNTS)
			return $order_total_discount;


		return Tools::ps_round((float)$order_total, 2);
	}

	///*** INICIO VALIDACION DIRECCION FARMALISTO ***///
	public function validationaddressfarmalisto(){

		$address = new Address($this->id_address_delivery);
		$cityact = strtoupper($address->city);
        $addressact = strtoupper($address->address1);

        $validateaddress = false;

        $addressesoficina = array(
            'CALLE 129A NO. 56B - 23',
            'CALLE 129A NUMERO 56B - 23',
            'CALLE 129A # 56B - 23',
            'CALLE 129A NO. 56B 23',
            'CALLE 129A NUMERO 56B 23',
            'CALLE 129A # 56B 23',
            'CALLE 129A NO. 56B-23',
            'CALLE 129A NUMERO 56B-23',
            'CALLE 129A # 56B-23'
        );

        if (trim(strtoupper($cityact)) == trim(strtoupper('BOGOTá, D.C.'))){
            foreach ($addressesoficina as $addresofi) {
                if (trim($addresofi) == trim(strtoupper($addressact))){
                    $validateaddress = true;
                }
            }
        }
        return $validateaddress;
    }
    ///*** FIN VALIDACION DIRECCION FARMALISTO ***///
    
        
    /**
     * [getOrderTotalPaid Para devolver el valor pagado de la orden creada]
     * @return [float] [retorna el valor de la tabla order]
     */
     public function getOrderTotalPaid() {

		if (!$this->id)
			return 0;
		
		$row2 = Db::getInstance()->getRow('SELECT total_paid FROM `'._DB_PREFIX_.'orders` WHERE id_cart = '.(int)$this->id	);

		if (!$row2) {
			return 0;
		} else {
			return $row2['total_paid'];
		}

	}
}