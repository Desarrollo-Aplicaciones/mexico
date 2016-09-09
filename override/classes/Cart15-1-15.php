<?php  
  
class Cart extends CartCore {  

  	public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)
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




	public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null)
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

}