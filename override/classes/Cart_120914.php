<?php  
  
class Cart extends CartCore {  

  	public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null)
	{

		// calcular el precio de envio por tabla ps_carrier_city
		

		$sql = 'SELECT cac.precio_kilo, car.id_carrier, crp.delimiter2
			FROM '._DB_PREFIX_.'address adr 
			INNER JOIN '._DB_PREFIX_.'address_city adc ON (adc.id_address=adr.id_address) 
			INNER JOIN '._DB_PREFIX_.'carrier_city cac ON (cac.id_city_des = adc.id_city) 
			INNER JOIN '._DB_PREFIX_.'carrier car ON (car.id_reference = cac.id_carrier AND car.deleted = 0 AND car.active=1) 
			INNER JOIN '._DB_PREFIX_.'range_price crp ON (crp.id_carrier = car.id_carrier)
			WHERE adc.id_address='.Context::getContext()->cart->id_address_delivery.'
			ORDER BY cac.precio_kilo';


			$resultado=Db::getInstance()->executeS($sql);

		if (!$val = Db::getInstance()->getValue($sql))
		{
			$val="Ciudad sin costo de envio";
		}
		if(isset( $resultado[0]['id_carrier'])) {
			Context::getContext()->cart->id_carrier = $resultado[0]['id_carrier'];

			$val_total=0;
                        
                        if (Context::getContext()->cart->_products) {

                foreach (Context::getContext()->cart->_products as $key => $value) {
                    $val_total += $value['total_wt']; //valor total de la compra sin impuestos
                }
            }
            //echo "\n<br> val_total: ".$val_total." ? > delimiter2:".$resultado[0]['delimiter2'];

			$valtot_tax = (round( (round($val_total*1.16)/100) *2 , 0)/ 2 ) * 100; //valor con impuesto 16% y redondeado

			if ( $val_total > $resultado[0]['delimiter2']) { // si total de compra es mayor al valor para no cobrar envio
				$val=0;
			}
		}
		return  $val; //$total_shipping; 
	}




	public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null)
	{

		//echo "<br>\n 2: ".
		$sql = 'SELECT cac.precio_kilo, car.id_carrier, crp.delimiter2
			FROM '._DB_PREFIX_.'address adr 
			INNER JOIN '._DB_PREFIX_.'address_city adc ON (adc.id_address=adr.id_address) 
			INNER JOIN '._DB_PREFIX_.'carrier_city cac ON (cac.id_city_des = adc.id_city) 
			INNER JOIN '._DB_PREFIX_.'carrier car ON (car.id_reference = cac.id_carrier AND car.deleted = 0 AND car.active=1) 
			INNER JOIN '._DB_PREFIX_.'range_price crp ON (crp.id_carrier = car.id_carrier)
			WHERE adc.id_address='.Context::getContext()->cart->id_address_delivery. '
			ORDER BY cac.precio_kilo';
		
		$resultado=Db::getInstance()->executeS($sql);

		if (!$val = Db::getInstance()->getValue($sql))
		{
			$val="Ciudad sin costo de envio";
		}
		$shipping_cost = (float)Tools::ps_round((float)$val, 2);
			
			if(isset( $resultado[0]['id_carrier'])) {
				Context::getContext()->cart->id_carrier = $resultado[0]['id_carrier'];
			}

			$val_total=0;

			foreach (Context::getContext()->cart->_products as $key => $value) {
				$val_total += $value['total_wt']; //valor total de la compra sin impuestos
			}
			
			$valtot_tax = (round( (round($val_total*1.16)/100) *2 , 0)/ 2 ) * 100; //valor con impuesto 16% y redondeado

			if ( $val_total > $resultado[0]['delimiter2']) { // si total de compra es mayor al valor para no cobrar envio
				$shipping_cost=0;
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



}