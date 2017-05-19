<?php  
 
class Cart extends CartCore {

	public $discountOrder = 0;
	public $removeRulesGroup = false;
	public $CartRuleProgressiveDiscount = 0;
	public $device = NULL;
        public $sessionApego;
    
    /** 
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'cart',
		'primary' => 'id_cart',
		'fields' => array(
			'id_shop_group' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_shop' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_address_delivery' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_address_invoice' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_carrier' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_currency' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_customer' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_guest' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_lang' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'recyclable' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'gift' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'gift_message' => 		array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
			'mobile_theme' => 		array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'delivery_option' => 		array('type' => self::TYPE_STRING),
			'secure_key' => 		array('type' => self::TYPE_STRING, 'size' => 32),
			'allow_seperated_package' =>    array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'date_upd' => 			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'device' =>			array('type' => self::TYPE_STRING),
                        'sessionApego'=>                array('type' => self::TYPE_STRING),
		),
	);

    /**
     * [validationaddressfarmalisto Para validar si la direccion de entrega, es la oficina de farmalisto]
     * @return [bool] $validateaddress [true si la direccion de entrega es la oficina de farmalisto, si no se retorna false]
     */
    public function validationaddressfarmalisto(){
        // se crea objeto address para tomar la dirección seleccionada de entrega
        $address = new Address($this->id_address_delivery);
        $cityact = strtoupper($address->city);
        $addressact = strtoupper($address->address1);

        // se inicializa en false la variable a retornar
        $validateaddress = false;

        // se crea arreglo con las direcciones de entrega validas para aplicar envio 0
        $addressesoficina = array(
            'FARMALISTO DOCTORA #39',
            'FARMALISTO DOCTORA#39',
            'FARMALISTO DOCTORA 39'
        );

        // valida que la ciudad de entrega sea bogota
        if (trim(strtoupper($cityact)) == trim(strtoupper('CIUDAD DE MéXICO - MIGUEL HIDALGO'))) {
            // se recorre arreglo de direcciones validas
            foreach ($addressesoficina as $addresofi) {
                // se valida que la direccion de entrega, sea igual a la direccion del arreglo de direcciones validas
                if (trim($addresofi) == trim(strtoupper($addressact))){
                    // si son iguales, se toma como true la variable a retornar
                    $validateaddress = true;
                }
            }
        }
        return $validateaddress;
    }

	public static function addCartMedico($id_cart, $id_medico){
		if ($id_medico != 0){
			$sql = 'DELETE FROM '._DB_PREFIX_.'doctor_cart WHERE id_cart = '.$id_cart;
			$data = array('id_cart' => $id_cart, 'id_doctor' => $id_medico);
			$error = array();
			if (!Db::getInstance()->execute($sql) || !Db::getInstance()->insert('doctor_cart', $data)){
				die($error['error'] = 'no se pudo agregar el médico');
			}
		}
		return $error;
	}

	public function getTotalShippingCost($delivery_option = null, $use_tax = true, Country $default_country = null, $express = false){
		if ((isset(Context::getContext()->cookie->check_xps) && Context::getContext()->cookie->check_xps)
			|| (isset(Context::getContext()->cart->check_xps) && Context::getContext()->cart->check_xps)
			|| Tools::getValue('express')){

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

        $parameters = NULL;

        /* Valida si es entrega nocturna */
        if( isset(Context::getContext()->cookie->entrega_nocturna) && Context::getContext()->cookie->entrega_nocturna === 'enabled' ){
            $parameters = Utilities::get_parameters();
        }

        /* validaciones para envio nocturno */
        if ((!isset(Context::getContext()->cookie->check_xps) || !Context::getContext()->cookie->check_xps)
            && isset(Context::getContext()->cookie->entrega_nocturna) && Context::getContext()->cookie->entrega_nocturna === 'enabled' ){
            // trigger_error(' -| envio nocturno solo |- ', E_USER_NOTICE);
            return   (int)($parameters['valor']);
		}

        /* validaciones envio Express y envio nocturno */
        if (Context::getContext()->cookie->check_xps
            && isset(Context::getContext()->cookie->entrega_nocturna)
            && Context::getContext()->cookie->entrega_nocturna === 'enabled'){
            $a = Context::getContext()->cart->id_address_delivery;
            //echo "<pre>";print_r(Context::getContext()->cart->check_xps); echo "</pre>"; exit();
            $val_total=0;
            if (Context::getContext()->cart->_products) {
                foreach (Context::getContext()->cart->_products as $key => $value) {
                    $val_total += $value['total_wt']; //valor total de la compra sin impuestos
                }
            }

            $subtotal = (round( (round($val_total)/100) *2 , 0)/ 2 ) * 100;

            if( isset($parameters['add_value_express']) && !$parameters['add_value_express'] ){
                return (float) $this->valorExpress( $a, $subtotal );
            }
            return  (float)($this->valorExpress( $a, $subtotal ) + (float)( $parameters['valor'] ) );
		}

		// calcular el precio de envio por tabla ps_carrier_city y codigo postal
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

			if ( !isset($resultado[0]['precio_kilo']) && !isset($resultado[0]['precio_cp']) ){
				$val="Ciudad sin costo de envio";
			}
            else {
				$val = !empty($resultado[0]['precio_cp'])? $resultado[0]['precio_cp'] : $resultado[0]['precio_kilo'];
			}

			$cobroenvio = 0; // 0 -> cobrar envio
			$found_carrier = 0;
			$delimitador_envio = 0;

			if ( isset( $resultado[0]['carrier_cp']) && $resultado[0]['carrier_cp'] !=  NULL ) {
				Context::getContext()->cart->id_carrier = $resultado[0]['carrier_cp'];
				$delimitador_envio = $resultado[0]['delimiter2_cp'];
				$found_carrier = 1;
				$cobroenvio = $resultado[0]['tarifa']? $resultado[0]['tarifa'] : 0;
			}
            elseif ( isset( $resultado[0]['id_carrier']) && $resultado[0]['id_carrier'] !=  NULL ) {
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
		}
        else {
			$val="Ciudad sin costo de envio";
		}

		// si la direccion de envio es la oficina de farmalisto, se toma como 0 el costo de transporte
		$validateaddress = $this->ValidationAddressFarmalisto();
		if ($validateaddress){
			$val = 0;
		}


		//echo $val;
		return  $val; //$total_shipping;
	}



	public function getTotalShippingCostWS($cod_postal = NULL, $ids_products = array()){
        /* validaciones envio Express */
		if ((isset(Context::getContext()->cookie->check_xps) && Context::getContext()->cookie->check_xps)
			 && (!isset(Context::getContext()->cookie->entrega_nocturna) || Context::getContext()->cookie->entrega_nocturna==='disabled')) {

			$a=Context::getContext()->cart->id_address_delivery;
			//echo "<pre>";print_r(Context::getContext()->cart->check_xps); echo "</pre>"; exit();
			$val_total=0;
			if (Context::getContext()->cart->_products) {
				$val_total=0;
				foreach (Context::getContext()->cart->_products as $key => $value) {
					$val_total += $value['total_wt']; //valor total de la compra sin impuestos
				}
			}
			$subtotal = (round( (round($val_total)/100) *2 , 0)/ 2 ) * 100;
            // trigger_error(' -| envio expres solo |- ', E_USER_NOTICE);
            return $this->valorExpress($a,$subtotal);
		}

        $parameters = NULL;

        if (isset(Context::getContext()->cart->id_address_delivery)) {
            //Validación vacia.
        }

        if( isset(Context::getContext()->cookie->entrega_nocturna) && Context::getContext()->cookie->entrega_nocturna === 'enabled' ){
            $parameters = Utilities::get_parameters();
        }

        /* validaciones para envio nocturno */
        if ((!isset(Context::getContext()->cookie->check_xps) || !Context::getContext()->cookie->check_xps)
            && isset(Context::getContext()->cookie->entrega_nocturna) && Context::getContext()->cookie->entrega_nocturna === 'enabled' ) {
            //  trigger_error(' -| envio nocturno solo |- ', E_USER_NOTICE);
            return (int)($parameters['valor']);
		}

        /* validaciones envio Express y envio nocturno */
        if (Context::getContext()->cookie->check_xps && isset(Context::getContext()->cookie->entrega_nocturna)  && Context::getContext()->cookie->entrega_nocturna === 'enabled') {
			$a=Context::getContext()->cart->id_address_delivery;
			//echo "<pre>";print_r(Context::getContext()->cart->check_xps); echo "</pre>"; exit();
			$val_total=0;
			if (Context::getContext()->cart->_products){
				foreach (Context::getContext()->cart->_products as $key => $value) {
					$val_total += $value['total_wt']; //valor total de la compra sin impuestos
				}
			}
			$subtotal = (round( (round($val_total)/100) *2 , 0)/ 2 ) * 100;

            // trigger_error(' -| envio nocturno y express |- ', E_USER_NOTICE);
			if( isset($parameters['add_value_express']) && !$parameters['add_value_express'] ){
				return (float) $this->valorExpress($a,$subtotal);
			}
			return  (int)($this->valorExpress($a,$subtotal) + (int)($parameters['valor']));
		}

		if(empty($cod_postal) || empty($ids_products))
			return false;

        // verifica si existe el código postal
		$sql = "SELECT COUNT(codigo_postal) total
				FROM ps_cod_postal
				WHERE codigo_postal = ".(int) $cod_postal;
		$total = (int)Db::getInstance()->getValue($sql);
		if($total==0)
            return array('STATUS'=>'ERROR', 'Message' => 'El código postal no existe.');

        // calcular el precio de envió por tabla ps_carrier_city y código postal
		$val = 0;

		$sql = 'SELECT cac.precio_kilo, car.id_carrier, crp.delimiter2, cac.tarifa, car2.id_carrier AS carrier_cp, 
				adtrcp.precio AS precio_cp, crp2.delimiter2 AS delimiter2_cp
                FROM '._DB_PREFIX_.'cod_postal codpos 
                LEFT JOIN '._DB_PREFIX_.'carrier_city cac ON (codpos.id_ciudad = cac.id_city_des ) 
                LEFT JOIN '._DB_PREFIX_.'carrier car ON ( car.id_reference = cac.id_carrier AND car.deleted = 0 AND car.active=1 ) 
                LEFT JOIN '._DB_PREFIX_.'range_price crp ON ( crp.id_carrier = car.id_carrier )
                LEFT JOIN '._DB_PREFIX_.'cities_col cc ON ( cc.id_city = cac.id_city_des )
                LEFT JOIN '._DB_PREFIX_.'state s ON ( s.id_state = cc.id_state AND s.id_country = '.(int)Configuration::get('PS_COUNTRY_DEFAULT').')
                LEFT JOIN '._DB_PREFIX_.'precio_tr_codpos adtrcp ON ( codpos.codigo_postal = adtrcp.codigo_postal )
                LEFT JOIN '._DB_PREFIX_.'carrier car2 ON (car2.id_reference = adtrcp.id_carrier AND car2.deleted = 0 AND car2.active=1)
                LEFT JOIN '._DB_PREFIX_.'range_price crp2 ON ( crp2.id_carrier = car2.id_carrier )
                WHERE codpos.codigo_postal = '.(int)$cod_postal.'
                AND ( ( car.id_carrier IS NOT NULL AND crp.delimiter2 IS NOT NULL ) 
                            OR ( car2.id_carrier IS NOT NULL AND crp2.delimiter2 IS NOT NULL ) 
                        )
                GROUP BY car.id_carrier, car2.id_carrier
                ORDER BY adtrcp.precio ASC, cac.precio_kilo ASC, crp.delimiter2 DESC, crp2.delimiter2 DESC';

        $resultado=Db::getInstance()->executeS($sql);

        if( count($resultado) > 0 ) {
			if ( !isset($resultado[0]['precio_kilo']) && !isset($resultado[0]['precio_cp']) ){
				$val=0;
			}
            else {
				$val = $resultado[0]['precio_cp']? $resultado[0]['precio_cp'] : $resultado[0]['precio_kilo'];
			}

            $cobroenvio = 0; // 0 -> cobrar envió
			$found_carrier = 0;
			$delimitador_envio = 0;

			if ( isset( $resultado[0]['carrier_cp']) && $resultado[0]['carrier_cp'] !=  NULL ) {
				$delimitador_envio = $resultado[0]['delimiter2_cp'];
				$found_carrier = 1;
				$cobroenvio = $resultado[0]['tarifa']? $resultado[0]['tarifa'] : 0;
			}
            elseif ( isset( $resultado[0]['id_carrier']) && $resultado[0]['id_carrier'] !=  NULL ) {
				$delimitador_envio = $resultado[0]['delimiter2'];
				$found_carrier = 1;
				$cobroenvio = $resultado[0]['tarifa']? $resultado[0]['tarifa'] : 0;
			}

			if ( $found_carrier == 1 ) {
				$val_total=0;

                if (!empty($ids_products) && is_array($ids_products)) {
					$ids = array();
					foreach ($ids_products as $key => $value) {
                        $ids[] = $key;
					}

					$sql = "SELECT id_product,price
							FROM "._DB_PREFIX_."product
							WHERE id_product IN(".implode(",", $ids).")
							AND active = 1
							ORDER BY id_product ASC;";
					$resultado2 = Db::getInstance()->executeS($sql);
					if( count($resultado2) > 0 ) {
    					foreach ($resultado2 as $key => $value) {
        					$val_total += ((float) $value['price'] * (int) $ids_products[$value['id_product']]); //valor total de la compra sin impuestos
            			}
                	}
				}
				$valtot_tax = (round( (round($val_total*1.16)/100) *2 , 0)/ 2 ) * 100; //valor con impuesto 16% y redondeado
				if ( $val_total > $delimitador_envio && $cobroenvio == 1) { // si total de compra es mayor al valor para no cobrar envió
					$val=0;
				}
			}
		}
        else {
			$val=0;
		}

		//echo $val;
		return  $val; //$total_shipping;
	}

	public function getPackageShippingCost($id_carrier = null, $use_tax = true, Country $default_country = null, $product_list = null, $id_zone = null, $express = false){
        /* validaciones envio Express */
		if ((isset(Context::getContext()->cookie->check_xps) && Context::getContext()->cookie->check_xps)
			 && (!isset(Context::getContext()->cookie->entrega_nocturna) || Context::getContext()->cookie->entrega_nocturna==='disabled')) {

			$a=Context::getContext()->cart->id_address_delivery;
			//echo "<pre>";print_r(Context::getContext()->cart->check_xps); echo "</pre>"; exit();
			$val_total=0;
			if (Context::getContext()->cart->_products) {
				$val_total=0;
				foreach (Context::getContext()->cart->_products as $key => $value) {
					$val_total += $value['total_wt']; //valor total de la compra sin impuestos
				}
			}
			$subtotal = (round( (round($val_total)/100) *2 , 0)/ 2 ) * 100;
                // trigger_error(' -| envio expres solo |- ', E_USER_NOTICE);
                return $this->valorExpress($a,$subtotal);
		}

        $parameters = NULL;

        if( isset(Context::getContext()->cookie->entrega_nocturna) && Context::getContext()->cookie->entrega_nocturna === 'enabled' ){
            $parameters = Utilities::get_parameters();
        }

        /* validaciones para envio nocturno */
        if ((!isset(Context::getContext()->cookie->check_xps) || !Context::getContext()->cookie->check_xps)
            && isset(Context::getContext()->cookie->entrega_nocturna) && Context::getContext()->cookie->entrega_nocturna === 'enabled' ) {
            //  trigger_error(' -| envio nocturno solo |- ', E_USER_NOTICE);
            return (int)($parameters['valor']);
		}

        /* validaciones envio Express y envio nocturno */
        if (Context::getContext()->cookie->check_xps && isset(Context::getContext()->cookie->entrega_nocturna)  && Context::getContext()->cookie->entrega_nocturna === 'enabled') {
			$a=Context::getContext()->cart->id_address_delivery;
			//echo "<pre>";print_r(Context::getContext()->cart->check_xps); echo "</pre>"; exit();
			$val_total=0;
			if (Context::getContext()->cart->_products) {
				foreach (Context::getContext()->cart->_products as $key => $value) {
					$val_total += $value['total_wt']; //valor total de la compra sin impuestos
				}
			}
			$subtotal = (round( (round($val_total)/100) *2 , 0)/ 2 ) * 100;
            // trigger_error(' -| envio nocturno y express |- ', E_USER_NOTICE);
			if( isset($parameters['add_value_express']) && !$parameters['add_value_express'] ){
				return (float) $this->valorExpress($a,$subtotal);
			}
			return  (int)($this->valorExpress($a,$subtotal) + (int)($parameters['valor']));
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

			if ( !$resultado[0]['precio_kilo'] && !$resultado[0]['precio_cp'] ){
				$val="Ciudad sin costo de envio";
			}
            else {
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

            }
            elseif ( isset( $resultado[0]['id_carrier']) && $resultado[0]['id_carrier'] !=  NULL ) {
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
		}
        else {
			$shipping_cost="Ciudad sin costo de envio";
		}

		// si la direccion de envio es la oficina de farmalisto, se toma como 0 el costo de transporte
		$validateaddress = $this->ValidationAddressFarmalisto();
		if ($validateaddress){
			$shipping_cost = 0;
		}

		// si existe cupon con envio gratuito, se toma como 0 el costo de transporte
		$cartRules = $this->getCartRules();
		if ( isset($cartRules) && !empty($cartRules) && $cartRules[0]['free_shipping'] == 1 ) {
			$shipping_cost = 0;
		}

		//echo $shipping_cost;
		return  $shipping_cost;
	}

	public function removeCartRules(){
		$cart_rules = $this->getCartRules();
		foreach ($cart_rules as $value) {
			$this->removeCartRule((int) $value['id_cart_rule']);
		}

		// se coloca la bandera removeRulesGroup en true, para que retire los descuentos de categoria de los productos de la orden en el metodo getProducts;
		$this->removeRulesGroup = true;
    }

	public function valorExpress($id,$subtotal)	{
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
		if($subtotal>$resultado['umbral']){
			return $express['arriba'];
		}
		else{
			return $express['abajo'];
		}
	}
	
    public function expressProduct(){
		$compare = array();
		$compare2 = array();
		$lista = "(";
		foreach($this->_products as $productos){
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
		foreach($resultado as $res){
			$compare2[$res["id"]]=$res["cantidad"];
		}
		unset($res);
		if(array_diff_key($compare, $compare2)){
			return false;
		}
		else{
			foreach($compare as $a => $valor){
				$res[$a] = $compare2[$a]-$compare[$a];
				if ($res[$a]<0){
					return false;
				}
			}
			return true;
		}
	}

	public function getSummaryDetails($id_lang = null, $refresh = false){
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
		$total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
		$total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
		$total_discounts = $this->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
		$total_discounts_tax_exc = $this->getOrderTotal(false, Cart::ONLY_DISCOUNTS);
		
                
                
                
            /*echo "<pre>";
        print_r($cart_rules);
        //print_r($products);
        echo "</pre>";
        echo "<hr>";*/
		// The cart content is altered for display
		foreach ($cart_rules as &$cart_rule){
                    foreach ($products as $key => &$product){
                        if( $cart_rule['product_restriction'] == 1 && $cart_rule['reduction_amount'] > 0 && ($product['id_product'] == $cart_rule['reduction_product'] ) ){
                            $cart_rule['value_real'] = Tools::ps_round($cart_rule['value_real'] * $product['cart_quantity'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
//                            echo "<pre>";
//                            print_r($cart_rule['value_real']);
//                            echo "<pre>";
//                            print_r($product['id_product'] );
////                            echo "<pre>";                            
////                            print_r($product );
//                            echo "</pre>";
//                            echo "<hr>";
//                        
                        }
                    }
                    
			// If the cart rule is automatic (wihtout any code) and include free shipping, it should not be displayed as a cart rule but only set the shipping cost to 0
			if ($cart_rule['free_shipping'] && (empty($cart_rule['code']) || preg_match('/^'.CartRule::BO_ORDER_CODE_PREFIX.'[0-9]+/', $cart_rule['code']))){
                                
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
			if ($cart_rule['gift_product']){
				foreach ($products as $key => &$product){
					if (empty($product['gift']) && $product['id_product'] == $cart_rule['gift_product'] && $product['id_product_attribute'] == $cart_rule['gift_product_attribute']){
						// Update total products
						$total_products_wt = Tools::ps_round($total_products_wt - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$total_products = Tools::ps_round($total_products - $product['price'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						
						// Update total discounts
						$total_discounts = Tools::ps_round($total_discounts - $product['price_wt'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
						$total_discounts_tax_exc = Tools::ps_round($total_discounts_tax_exc - $product['price'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
					
						// Update cart rule value
						$cart_rule['value_real'] = Tools::ps_round(($cart_rule['value_real'] - $product['price_wt'])*$product['cart_quantity'], (int)$context->currency->decimals * _PS_PRICE_DISPLAY_PRECISION_);
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
		}
		/********** DESHABILITADO PARA MOSTRAR CUPONES CON VALOR EN CERO - Ewing 
		foreach ($cart_rules as $key => &$cart_rule)
			if ($cart_rule['value_real'] == 0)
				unset($cart_rules[$key]);
		**************/

        
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
			'carrier' => new Carrier($this->id_carrier, $id_lang)
		);
	}

	public function getProducts($refresh = false, $id_product = false, $id_country = null){
		if (!$this->id)
			return array();
		// Product cache must be strictly compared to NULL, or else an empty cart will add dozens of queries
		if ($this->_products !== null && !$refresh){
			// Return product row with specified ID if it exists
			if (is_int($id_product)){
				foreach ($this->_products as $product){
					if ($product['id_product'] == $id_product){
						return array($product);
                    }
                    return array();
                }
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
						product_shop.`wholesale_price`, product_shop.advanced_stock_management, ps.product_supplier_reference supplier_reference, IFNULL(fvl.`value`, "") AS rx');

		// Build FROM
		$sql->from('cart_product', 'cp');

		// Build JOIN
		$sql->leftJoin('product', 'p', 'p.`id_product` = cp.`id_product`');
		$sql->innerJoin('product_shop', 'product_shop', '(product_shop.id_shop=cp.id_shop AND product_shop.id_product = p.id_product)');
		$sql->leftJoin('product_lang', 'pl', 'p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('pl', 'cp.id_shop') );

		// Product RX
		$sql->leftJoin('feature_product', 'fp', '( p.`id_product` = fp.`id_product` AND fp.`id_feature` = 4121 )');
		$sql->leftJoin('feature_value_lang', 'fvl', 'fp.`id_feature_value` = fvl.`id_feature_value`');

		$sql->leftJoin('category_lang', 'cl', 'product_shop.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)$this->id_lang.Shop::addSqlRestrictionOnLang('cl', 'cp.id_shop')	);

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

		if (Customization::isFeatureActive()){
			$sql->select('cu.`id_customization`, cu.`quantity` AS customization_quantity');
			$sql->leftJoin('customization', 'cu',
				'p.`id_product` = cu.`id_product` AND cp.`id_product_attribute` = cu.id_product_attribute AND cu.id_cart='.(int)$this->id);
		}
		else
			$sql->select('NULL AS customization_quantity, NULL AS id_customization');

		if (Combination::isFeatureActive()){
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
		else{
			$sql->select(
				'p.`reference` AS reference, p.`ean13`,
				p.`upc` AS upc, product_shop.`minimal_quantity` AS minimal_quantity'
			);
        }
		$result = Db::getInstance()->executeS($sql);

		// Reset the cache before the following return, or else an empty cart will add dozens of queries
		$products_ids = array();
		$pa_ids = array();
		if ($result){
			foreach ($result as $row){
				$products_ids[] = $row['id_product'];
				$pa_ids[] = $row['id_product_attribute'];
			}
        }
		// Thus you can avoid one query per product, because there will be only one query for all the products of the cart
		Product::cacheProductsFeatures($products_ids);
		Cart::cacheSomeAttributesLists($pa_ids, $this->id_lang);

		$this->_products = array();
		if (empty($result))
			return array();

		$cart_shop_context = Context::getContext()->cloneContext();
                //error_log("\r\n cart_shop_context: ".print_r($cart_shop_context['price_wt'],true), 3, "/tmp/ordererror.log");
		foreach ($result as &$row){
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

			if ($this->_taxCalculationMethod == PS_TAX_EXC){
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
			else {
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

			// si removeRulesGroup es true, se remueven los descuentos de categoria, seteando sus precios a los valores originales
			if ( $this->removeRulesGroup ) {
				$tax = Tax::getProductTaxRate((int)$row['id_product'], (int)$address_id);

				$row['price'] = (float)$row['wholesale_price'];

				$row['price_wt'] = $row['price'] + ( ( $row['price'] * $tax ) / 100 );

				$row['total_wt'] = $row['price_wt'] * (int)$row['cart_quantity'];
				$row['total'] = $row['price'] * (int)$row['cart_quantity'];
			}

			$CartRules = $this->getCartRules();

			if ( !empty($CartRules) && $CartRules[0]['reduction_percent'] != 0 && $CartRules[0]['reduction_product'] > 0 && $CartRules[0]['reduction_product'] == $row['id_product'] ) {
					// se toma el iva a aplicar del producto
					$tax = Tax::getProductTaxRate((int)$row['id_product'], (int)$address_id);
					$priceDiscount = $this->UnitPriceDiscountPercent( $row['price'], $tax, $CartRules[0]['reduction_percent'], true, $row['cart_quantity']);
					$row['price_wt'] = Tools::ps_round( $priceDiscount, 2);
					$row['total_wt'] = $row['price_wt'] * (int)$row['cart_quantity'];

			}
            elseif ( !empty($CartRules) && $CartRules[0]['reduction_percent'] != 0  && $CartRules[0]['reduction_product'] == 0 ) {
				// si existe un cupon de descuento por porcentaje, se recalculan los valores price_wt y total_wt aplicando el descuento a cada producto
                // se toma el iva a aplicar del producto
				$tax = Tax::getProductTaxRate((int)$row['id_product'], (int)$address_id);
				$priceDiscount = $this->UnitPriceDiscountPercent( $row['price'], $tax, $CartRules[0]['reduction_percent'], true, $row['cart_quantity']);
				$row['price_wt'] = Tools::ps_round( $priceDiscount, 2);
				$row['total_wt'] = $row['price_wt'] * (int)$row['cart_quantity'];
			}

			if (!isset($row['pai_id_image']) || $row['pai_id_image'] == 0){
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
			else {
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

		return $this->_products;
	}

        public function add_total_tax($totalTax){
        $rs = Db::getInstance()->update('cart',
            array(
                'total_tax' => $totalTax,
            ),
            'id_cart = '.(int)$this->id);
	}

    /**
    * [getOrderTotalPaid Para devolver el valor pagado de la orden creada]
    * @return [float] [retorna el valor de la tabla order]
    */
    public function getOrderTotalPaid() {
		if (!$this->id)
			return 0;

		$row2 = Db::getInstance()->getRow('SELECT total_paid FROM `'._DB_PREFIX_.'orders` WHERE id_cart = '.(int)$this->id  );

		if (!$row2) {
			return 0;
		}
        else {
			return $row2['total_paid'];
		}
	}

    /**
    * Lista de productos re-calculados
    */

    public function get_products_rec(){
        $productos = $this->getProducts();
        $total_descuento =0;
        $total_productos=0;
        $total_iva=0;

        ///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE Y LISTADO DE IMPUESTOS DE PRODUCTOS ***///
		$cart_rules = $this->getCartRules();

		if (!empty($cart_rules)) {
			$detailcartrule = $this->cartRuleDetail($cart_rules[0]['id_cart_rule']);
			if ($cart_rules[0]['reduction_percent'] != 0 && $cart_rules[0]['reduction_amount'] == 0) {
				// descuento por de porcentaje
				foreach ($productos as $key => $product) {
					$precio = $product['price'];
					$iva_c = $product['rate'];

					$porcentajedescuento = $cart_rules[0]['reduction_percent'];

					// calcular descuento por unidad producto
					$descuento_producto = ( $precio * $porcentajedescuento) / 100;
					// Calcular descuento total de productos del mismo tipo
					$descuento_subtotal = ( ( $precio * $product['cart_quantity'] )  * $porcentajedescuento) / 100;
					// Calcular iva total de productos del mismo tipo
					$iva_subtotal = ( ( $precio * $product['cart_quantity'] ) - $descuento_subtotal) * ($iva_c / 100);
					// Aplicar descuento a cada producto
					$producto_con_descuento =  $precio - $descuento_producto;
					// Calcular iva por unidad de producto
					$iva_producto = (($producto_con_descuento * $iva_c) / 100);
					// calcular recio de venta
					//$precio_venta = $producto_con_descuento + $iva_producto + $descuento_producto;
					$precio_venta = $producto_con_descuento + $iva_producto;

					$productos[$key]['precio_venta'] = $precio_venta;
					$productos[$key]['iva_subtotal'] = $iva_subtotal;
					$productos[$key]['descuento_subtotal']=$descuento_subtotal;
					$productos[$key]['iva_prod']=$iva_producto;
					$productos[$key]['descuento_prod']=$descuento_producto;

                    $recalculadoivaproducto = true;
					if ( !isset($array_ivas[$iva_c]) ) {
						$array_ivas[$iva_c] = 0;
					}

					$array_ivas[$iva_c] += $iva_subtotal;
				}
			}
            elseif ($cart_rules[0]['reduction_percent'] == 0  && $cart_rules[0]['reduction_amount'] != 0) {
				// descuento monetario
                foreach ($productos as $key => $product) {
					$precio = $product['price'];
					$iva_c = $product['rate'];

					$iva_subtotal = (( $precio * $product['cart_quantity'] )) * ($iva_c / 100);
					$iva_producto = (($precio * $iva_c) / 100);
					$precio_venta = $precio + $iva_producto;
					$productos[$key]['precio_venta'] = $precio_venta;

                    $recalculadoivaproducto = true;
					if ( !isset($array_ivas[$iva_c]) ) {
						$array_ivas[$iva_c] = 0;
					}

                    $array_ivas[$iva_c] += $iva_subtotal;
				}

			}
            else {}
		}
        else {
            foreach ($productos as $key => $product) {
                $precio = $product['price'];
                $iva_c = $product['rate'];

                $iva_subtotal = ( $precio * $product['cart_quantity'] ) * ($iva_c / 100);
                $iva_producto = (($precio * $iva_c) / 100);
                $precio_venta = $precio + $iva_producto;

                $productos[$key]['precio_venta'] = $precio_venta;
                $productos[$key]['iva_subtotal'] = $iva_subtotal;
                $productos[$key]['iva_prod']=$iva_producto;

                if ( !isset($array_ivas[$iva_c]) ) {
                    $array_ivas[$iva_c] = 0;
                }

				//echo "<br> iva si: ".$iva_c." - ".
				$array_ivas[$iva_c] += number_format( $iva_subtotal ,2, '.', '');

                $total_iva+=($iva_subtotal * $product['cart_quantity']);
                $total_productos+= ($precio_venta*$product['cart_quantity']);
            }
		}

        // $productos['total_productos'] = $total_productos;
        // $productos['total_iva'] = $total_iva;
        // $productos['total_descuento'] = $total_descuento;
        return $productos;
    }

    /**
    * totales orden
    */
    public function getOrderTotals(){
        $productos = $this->getProducts();
        $total_descuento =0;
        $total_productos=0;
        $total_iva=0;
        ///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE Y LISTADO DE IMPUESTOS DE PRODUCTOS ***///
		$cart_rules = $this->getCartRules();

        //	exit('<pre>'.print_r($cart_rules,true));

		if (!empty($cart_rules)) {
			$detailcartrule = $this->cartRuleDetail($cart_rules[0]['id_cart_rule']);
			$porcentajedescuento = $detailcartrule[0]['reduction_percent'];
			if ($cart_rules[0]['reduction_percent'] != 0 && $cart_rules[0]['reduction_amount'] == 0) {
				foreach ($productos as $key => $product) {
					$precio = $product['price'];
					$iva_c = $product['rate'];

					// calcular descuento por unidad producto
					$descuento_producto = ( $precio * $porcentajedescuento) / 100;
					// Calcular descuento total de productos del mismo tipo
					$descuento_subtotal = ( ( $precio * $product['cart_quantity'] )  * $porcentajedescuento) / 100;
					// Calcular iva total de productos del mismo tipo
					$iva_subtotal = ( ( $precio * $product['cart_quantity'] ) - $descuento_subtotal) * ($iva_c / 100);
					// Aplicar descuento a cada producto
					$producto_con_descuento =  $precio - $descuento_producto;
					// Calcular iva por unidad de producto
					$iva_producto = (($producto_con_descuento * $iva_c) / 100);
					// calcular recio de venta
					//$precio_venta = $producto_con_descuento + $iva_producto + $descuento_producto;
					$precio_venta = $producto_con_descuento + $iva_producto;

					$productos[$key]['precio_venta'] = $precio_venta;
					$productos[$key]['iva_subtotal'] = $iva_subtotal;
					$productos[$key]['descuento_subtotal']=$descuento_subtotal;
					$productos[$key]['iva_prod']=$iva_producto;
					$productos[$key]['descuento_prod']=$descuento_producto;

                    $total_descuento+=($descuento_subtotal * $product['cart_quantity']);
                    $total_iva+=($iva_subtotal * $product['cart_quantity']);
                    $total_productos+= ($precio_venta*$product['cart_quantity']);

                    // echo "<br>Precio: ".$precio." || Antes ".$productos[$key]['price_wt']." || Calculo: ".(float) ($precio + (($precio * $iva_c) / 100)).' || Iva: '.$iva_c;

                    $recalculadoivaproducto = true;
					if ( !isset($array_ivas[$iva_c]) ) {
						$array_ivas[$iva_c] = 0;
					}

					$array_ivas[$iva_c] += $iva_subtotal;
				}
			}
            elseif ($cart_rules[0]['reduction_percent'] == 0  && $cart_rules[0]['reduction_amount'] != 0) {
				// descuento monetario
                foreach ($productos as $key => $product) {
					$precio = $product['price'];
					$iva_c = $product['rate'];

					$iva_subtotal = (( $precio * $product['cart_quantity'] )) * ($iva_c / 100);
					$iva_producto = (($precio * $iva_c) / 100);
					$precio_venta = $precio + $iva_producto;
					$productos[$key]['precio_venta'] = $precio_venta;

                    $recalculadoivaproducto = true;
					if ( !isset($array_ivas[$iva_c]) ) {
						$array_ivas[$iva_c] = 0;
					}

					$array_ivas[$iva_c] += $iva_subtotal;

					$array_ivas[$iva_c] += number_format( $iva_subtotal ,2, '.', '');
					$total_iva+=($iva_subtotal * $product['cart_quantity']);
					$total_productos+= ($precio_venta*$product['cart_quantity']);
				}
			}
		}
        else {
            foreach ($productos as $key => $product) {
                $precio = $product['price'];
                $iva_c = $product['rate'];


                $iva_subtotal = ( $precio * $product['cart_quantity'] ) * ($iva_c / 100);
                $iva_producto = (($precio * $iva_c) / 100);
                $precio_venta = $precio + $iva_producto;

                $productos[$key]['precio_venta'] = $precio_venta;
                $productos[$key]['iva_subtotal'] = $iva_subtotal;
                $productos[$key]['iva_prod']=$iva_producto;

                if ( !isset($array_ivas[$iva_c]) ) {
                    $array_ivas[$iva_c] = 0;
                }

                $array_ivas[$iva_c] += number_format( $iva_subtotal ,2, '.', '');

                $total_iva+=($iva_subtotal * $product['cart_quantity']);
                $total_productos+= ($precio_venta*$product['cart_quantity']);
            }
		}
        return array('total_productos'=>number_format($total_productos, 2, '.', ''),'total_iva'=>number_format($total_iva, 2, '.', ''),'total_descuento'=>number_format($total_descuento, 2, '.', ''),'shipping'=>number_format($this->getTotalShippingCost(), 2, '.', ''),'productos'=>$productos,'total_orden'=>number_format( (float) ($total_productos+$this->getTotalShippingCost()), 2, '.', ''));
    }

	/*consulta para conocer los detalles del cupon agregado*/
	public function cartRuleDetail($id_cart_rule){
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                                                            SELECT *
                                                            FROM `'._DB_PREFIX_.'cart_rule` cr
                                                            WHERE cr.`id_cart_rule` = '.$id_cart_rule);
	}




    /**
    * [UnitPriceDiscountPercent Para retornar el valor unitario del producto aplicando el respectivo descuento para facturaxion]
    * @param [int] $price		   [Valor inicial del producto]
    * @param [int] $tax			 [% IVA del producto]
    * @param [int] $discountPercent [% Descuento a aplicar]
    * @param [bool] $priceShow	  [flag para retornar precio a mostrar]
    * @param [bool] $quantity	   [cantidad de productos en carrito]
    * @param [bool] $showDiscount   [flag para retornar unicarmente el desucuento aplicado por producto]
    * @return [int] $unitPrice	  [valor final unitario del producto]
    */

	public static function StaticUnitPriceDiscountPercent( $price, $tax, $discountPercent, $priceShow, $quantity, $showDiscount = false, $showTaxDiscount = false) {

		//echo "<br> ----- cart price: ".$price;
		//echo "<br> ----- cart tax: ".$tax;
		//echo "<br> ----- cart discountPercent: ".$discountPercent;
		//echo "<br> ----- cart priceShow: ".$priceShow;
		//echo "<br> ----- cart quantity: ".$quantity;

		// discount almacena descuento aplicado al precio inicial del producto
		$discount = ($price * $discountPercent) / 100;

		//echo "<br> ----- cart discount: ".$discount;

		// si showDiscount es true, se retorna solamente el descuento de cada producto
		if ( $showDiscount ) {
			return ( $discount * $quantity );
		}

		// priceDiscount almacena el precio inicial del producto con el descuento aplicado
		$priceDiscount = $price - $discount;
		//echo "<br> ----- cart priceDiscount: ".$priceDiscount;
		// taxDiscount almacena el iva del producto con el descuento aplicado
		$taxDiscount = ( ( $priceDiscount * $tax ) / 100 );

		// retorna unicamente el iva del producto con el descuento aplicado
		if ( $showTaxDiscount ) {
			//echo "<br> ----- cart taxDiscount: ".$taxDiscount;
			return $taxDiscount * $quantity;
		}

		if ( $priceShow ) {
			// se suma el precio inicial del producto para ser mostrado correctamente en la vista
			$unitPrice = $price + $taxDiscount;
		} else {
			// se suma el precio con descuento para generar el total de la orden correctamente
			$unitPrice = $priceDiscount + $taxDiscount;
		}

		return $unitPrice;
	}

	/**
    * [UnitPriceDiscountPercent Para retornar el valor unitario del producto aplicando el respectivo descuento]
    * @param [int] $price		   [Valor inicial del producto]
    * @param [int] $tax			 [% IVA del producto]
    * @param [int] $discountPercent [% Descuento a aplicar]
    * @param [bool] $priceShow	  [flag para retornar precio a mostrar]
    * @param [bool] $quantity	   [cantidad de productos en carrito]
    * @param [bool] $showDiscount   [flag para retornar unicarmente el desucuento aplicado por producto]
    * @return [int] $unitPrice	  [valor final unitario del producto]
    */
	public function UnitPriceDiscountPercent( $price, $tax, $discountPercent, $priceShow, $quantity, $showDiscount = false, $showTaxDiscount = false) {

       // discount almacena descuento aplicado al precio inicial del producto
       $discount = ($price * $discountPercent) / 100;

       // si showDiscount es true, se retorna solamente el descuento de cada producto
       if ( $showDiscount ) {
           return ( $discount * $quantity );
       }

       // priceDiscount almacena el precio inicial del producto con el descuento aplicado
       $priceDiscount = $price - $discount;

       // taxDiscount almacena el iva del producto con el descuento aplicado
       $taxDiscount = ( ( $priceDiscount * $tax ) / 100 );

       // retorna unicamente el iva del producto con el descuento aplicado
       if ( $showTaxDiscount ) {
           return $taxDiscount * $quantity;
       }

       if ( $priceShow ) {
           // se suma el precio inicial del producto para ser mostrado correctamente en la vista
           $unitPrice = $price + $taxDiscount;
       } else {
           // se suma el precio con descuento para generar el total de la orden correctamente
           $unitPrice = $priceDiscount + $taxDiscount;
       }

       return $unitPrice;
    }

	/**
    * [GetProductsCartReductionCategory Para retornar true si en el carrito se encuentra algun producto con cupon de descuento por categoria]
    * @return [bool] $validationReductionCategory [true si se encuentra un producto del carrito con cupon de descuento]
    */
	public function GetProductsCartReductionCategory( $ruleAdding ) {
		$validationReductionCategory = false;

		// se valida si el cupon a agregar es 0, si esto se cumple, se retorna como falso para que permita agregar el cupon en 0
		$carRule = new CartRule();
		$ruleIs0 = $carRule->getCartRuleDetail( $ruleAdding );
		if ( $ruleIs0 ) {
			return $validationReductionCategory;
		}

		// se toman los productos que se encuentran en el carrito
		$products = $this->getProducts();

		// se crea objeto specificPrice que se encarga de los descuentos de categoria
		$specificPrice = new SpecificPrice();

		// se recorren los productos que se encuentran en el carrito
		foreach ($products as $product) {
			// se llama el metodo getByProductId para validar si el producto posee descuento por categoria
			$productCategoryDiscount = $specificPrice->getByProductId( $product['id_product'], false, false, true );

			// si el arreglo es diferente a vacio es porque el producto posee un descuento con categoria
			if ( !empty($productCategoryDiscount) ) {
				$validationReductionCategory = true;
				break;
			}
		}
		return $validationReductionCategory;
	}

    /**
    * [validateProgressiveDiscount Funcion para validar si se encuentra un cupon de descuento progresivo en el carrito]
    */
	public function validateProgressiveDiscountInCart() {

		$queryProgressiveDiscountInCart = "
			SELECT COUNT(*) as ProgressiveDiscountInCart
			FROM "._DB_PREFIX_."cart_cart_rule ccr
			INNER JOIN "._DB_PREFIX_."cart_cartrule_progressive_discounts ccpd
			ON ( ccr.id_cart = ccpd.id_cart )
			INNER JOIN "._DB_PREFIX_."progressive_discounts pd
			ON ( ccpd.id_progressive_discount = pd.id_progressive_discount )
			WHERE ccr.id_cart = ".(int)$this->id;

		$ProgressiveDiscountInCart = Db::getInstance()->ExecuteS($queryProgressiveDiscountInCart);

		if ( $ProgressiveDiscountInCart[0]['ProgressiveDiscountInCart'] > 0 ) {
			return true;
		}
        else {
			return false;
		}
	}

	/**
    * [DetailsFacturaxion consulta los detalles de la orden y genera los datos para el desglose de iva]
    * @param int $idOrder [id de la orden a consultar]
    * @param int $idCart  [id del carrito a consultar]
    * @return [array] [arreglo con los detalles de la orden]
    */
	public function DetailsFacturaxion( $idOrder = "", $idCart = "" ) {

		$arrayDetailsFacturaxion = array();

		// se consulta por la entidad que sea diferente a vacia, de ser ambas vacias, se retorna el array vacio
		if ( $idOrder != "" ) {
		   $searchFor = 'o.id_order = '.$idOrder;
		}
        elseif ( $idCart != "" ) {
		   $searchFor = 'o.id_cart = '.$idCart;
		}
        else {
			return $arrayDetailsFacturaxion;
		}

		// query para consultar los detalles de la orden
		$sql = new DbQuery();
		$sql->select('o.total_paid, o.total_discounts, crl.name, cr.description, ocr.free_shipping, ocr.reduction_amount, ocr.reduction_percent');
		$sql->from('orders', 'o');
		$sql->innerJoin('order_cart_rule', 'ocr', 'o.id_order = ocr.id_order');
		$sql->innerJoin('cart_rule', 'cr', 'ocr.id_cart_rule = cr.id_cart_rule');
		$sql->innerJoin('cart_rule_lang', 'crl', 'cr.id_cart_rule = crl.id_cart_rule');
		$sql->where($searchFor);
		// se almacena los resultados en la posicion DetailsOrder del arreglo a retornar
		$arrayDetailsFacturaxion['DetailsOrder'] = Db::getInstance()->executeS($sql);

		// query para consultar los detalles de la orden
		$sql = new DbQuery();
		$sql->select('ROUND( od.tax_rate, 0 ) as tax_rate, ROUND( SUM(odt.total_amount), 2 ) as total_taxt');
		$sql->from('orders', 'o');
		$sql->innerJoin('order_detail', 'od', 'o.id_order = od.id_order');
		$sql->leftJoin('order_detail_tax', 'odt', 'od.id_order_detail = odt.id_order_detail');
		$sql->where($searchFor);
		$sql->groupBy('od.tax_rate');
		$sql->orderBy('od.tax_rate');
		// se almacena los resultados en la posicion BreakdownTax del arreglo a retornar
		$arrayDetailsFacturaxion['BreakdownTax'] = Db::getInstance()->executeS($sql);

		// se retorna el arreglo con detalles de la orden y desglose de IVA
		return $arrayDetailsFacturaxion;
	}

	///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
	/*public function recalculartotalconcupon($descuento){
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



	///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO MONETARIO ***///
	/*public function RecalcularCuponMonetario(){
		// arreglo de los productos en el carrito
		$products = $this->getProducts();

		// arreglo con las reglas de compra
		$cartRules = $this->getCartRules();

		// costo de envio
		$validateaddressOficina = $this->validationaddressfarmalisto();
		if ( $validateaddressOficina ){
			$total_envio = 0;
			$address_oficina = 1;
		} else {
			$total_envio = $this->getTotalShippingCost();
			$address_oficina = 0;
		}


		// captura el descuento monetario aplicado y el envio gratuito
		$descuento = $cartRules[0]['reduction_amount'];
		$free_shipping = $cartRules[0]['free_shipping'];

		// declaracion de variables
		$detail_products = "";
		$total_iva_pesos_individual = "";
		$total_iva_pesos_grupal = "";
		$total_precio_iva_individual = "";
		$total_precio_iva_grupal = "";
		$total_precio_cantidad = "";
		$i = 0;

		// se recorren los productos del carrito
		foreach ( $products as $value ) {

			// se toman los datos principales del producto
			$precio = $value['price'];
			$iva = $value['rate'];
			$cantidad = $value['cart_quantity'];

			// inicio calculos individuales
			$iva_pesos_individual = ( $precio * $iva ) / 100;
			$iva_pesos_grupal = $iva_pesos_individual * $cantidad;

			$precio_iva_individual = $precio + $iva_pesos_individual;
			$precio_iva_grupal = $precio_iva_individual * $cantidad;

			$precio_cantidad = $precio * $cantidad;
			// fin calculos individuales

			// inicio calculo totales
			$total_iva_pesos_individual += $iva_pesos_individual;
			$total_iva_pesos_grupal += $iva_pesos_grupal;



			$total_precio_iva_individual += $precio_iva_individual;
			$total_precio_iva_grupal += $precio_iva_grupal;

			$total_precio_cantidad += $precio_cantidad;
			// fin calculo totales


			// crea arreglo con detalles del producto
			$detail_products[$i] = array(
						'id_product' => $value['id_product'],
						// 'reference' => $value['reference'],
						// 'name' => $value['name'],
						// 'price' => $value['price'],
						// 'rate' => $value['rate'],
						// 'cart_quantity' => $value['cart_quantity'],
						// 'price_wt' => $value['price_wt'],
						// 'total_wt' => $value['total_wt'],
						// 'total' => $value['total'],
						'iva_pesos_individual' => $iva_pesos_individual,
						'iva_pesos_grupal' => $iva_pesos_grupal,
						'precio_iva_individual' => $precio_iva_individual,
						'precio_iva_grupal' => $precio_iva_grupal,
						'precio_cantidad' => $precio_cantidad
			);

			$i++;
		}

		// se aplica descuento y se suma al final el iva de los productos
		$precioTotal_descuento_aplicado = $total_precio_cantidad - $descuento;

		if ( $precioTotal_descuento_aplicado < 0 ) {
			$precioTotal_descuento_aplicado = 0;
		}
		$precioTotal_descuento_aplicado += $total_iva_pesos_grupal;


		// si el precio con descuento aplicado es 0, se toma la suma de ivas de los productos, si no se toma el valor calculado
		if ( $precioTotal_descuento_aplicado == 0 ) {
			$total_orden = $total_iva_pesos_grupal;
		} else {
			$total_orden = $precioTotal_descuento_aplicado;
		}


		// se suma el costo del envio solo si la regla del carrito no contiene envio gratuito
		if ( $free_shipping == 0 ) {
			$total_orden += $total_envio;
		}


		// calculo del descuento aplicado
		if ( $total_precio_cantidad >= $descuento){
			$descuento_aplicado = $descuento;
		} else {
			$descuento_aplicado = $total_precio_cantidad;
		}


		// calculo de porcentaje de descuento individual en $ y %
		foreach ( $detail_products as $key => $value ) {

			// se calcula el % de descuento individual
			$porcentaje_descuento_individual = ( 100 * ( $value['precio_iva_grupal'] ) / $total_precio_iva_grupal);
			$porcentaje_descuento_individual = number_format($porcentaje_descuento_individual, 4);

			// se calcula los $ de descuento individual
			$pesos_descuento_individual = ( $total_precio_cantidad * $porcentaje_descuento_individual ) / 100;

			// se asigna los valores de descuento al arreglo de detalles
			$detail_products[$key]['porcentaje_descuento_individual'] = $porcentaje_descuento_individual;
			$detail_products[$key]['pesos_descuento_individual'] = $pesos_descuento_individual;
		}

		// crea arreglo con detalles de los totales
		$detail_products['totales'] = array(
					'total_iva_pesos_individual' => $total_iva_pesos_individual,
					'total_iva_pesos_grupal' => $total_iva_pesos_grupal,
					'total_precio_iva_individual' => $total_precio_iva_individual,
					'total_precio_iva_grupal' => $total_precio_iva_grupal,
					'total_precio_cantidad' => $total_precio_cantidad,
					'free_shipping' => $free_shipping,
					'address_oficina' => $address_oficina,
					'descuento_aplicado' => $descuento_aplicado,
					'total_orden' => $total_orden
		);

		//echo "<pre>"; print_r($detail_products); die();

		return $detail_products;
	}
	///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO MONETARIO***///

	public function getPackageList($flush = false){
		static $cache = array();
		if (isset($cache[(int)$this->id]) && $cache[(int)$this->id] !== false && !$flush)
			return $cache[(int)$this->id];

		$product_list = $this->getProducts();
		// Step 1 : Get product informations (warehouse_list and carrier_list), count warehouse
		// Determine the best warehouse to determine the packages
		// For that we count the number of time we can use a warehouse for a specific delivery address
		$warehouse_count_by_address = array();
		$warehouse_carrier_list = array();

		$stock_management_active = Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT');

		foreach ($product_list as &$product){
			if ((int)$product['id_address_delivery'] == 0)
				$product['id_address_delivery'] = (int)$this->id_address_delivery;

			if (!isset($warehouse_count_by_address[$product['id_address_delivery']]))
				$warehouse_count_by_address[$product['id_address_delivery']] = array();

			$product['warehouse_list'] = array();

			if ($stock_management_active &&
				((int)$product['advanced_stock_management'] == 1 || Pack::usesAdvancedStockManagement((int)$product['id_product'])) && Configuration::get('PS_SHIP_WHEN_AVAILABLE'))	{
				$warehouse_list = Warehouse::getProductWarehouseList($product['id_product'], $product['id_product_attribute'], $this->id_shop);
				if (count($warehouse_list) == 0)
					$warehouse_list = Warehouse::getProductWarehouseList($product['id_product'], $product['id_product_attribute']);

                // Does the product is in stock ?
				// If yes, get only warehouse where the product is in stock

				$warehouse_in_stock = array();
				$manager = StockManagerFactory::getManager();

				foreach ($warehouse_list as $key => $warehouse){
					$product_real_quantities = $manager->getProductRealQuantities(
						$product['id_product'],
						$product['id_product_attribute'],
						array($warehouse['id_warehouse']),
						true
					);

					if ($product_real_quantities > 0 || Pack::isPack((int)$product['id_product']))
						$warehouse_in_stock[] = $warehouse;
				}

				if (!empty($warehouse_in_stock)){
					$warehouse_list = $warehouse_in_stock;
					$product['in_stock'] = true;
				}
				else
					$product['in_stock'] = false;
			}
			else{
				//simulate default warehouse
				$warehouse_list = array(0);
				$product['in_stock'] = StockAvailable::getQuantityAvailableByProduct($product['id_product'], $product['id_product_attribute']) > 0;
			}

			foreach ($warehouse_list as $warehouse)	{
				if (!isset($warehouse_carrier_list[$warehouse['id_warehouse']])){
					$warehouse_object = new Warehouse($warehouse['id_warehouse']);
					$warehouse_carrier_list[$warehouse['id_warehouse']] = $warehouse_object->getCarriers();
				}

				$product['warehouse_list'][] = $warehouse['id_warehouse'];
				if (!isset($warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']]))
					$warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']] = 0;

				$warehouse_count_by_address[$product['id_address_delivery']][$warehouse['id_warehouse']]++;
			}
		}
		unset($product);

		arsort($warehouse_count_by_address);

		// Step 2 : Group product by warehouse
		$grouped_by_warehouse = array();
		foreach ($product_list as &$product){
			if (!isset($grouped_by_warehouse[$product['id_address_delivery']]))
				$grouped_by_warehouse[$product['id_address_delivery']] = array(
					'in_stock' => array(),
					'out_of_stock' => array(),
				);

			$product['carrier_list'] = array();
			$id_warehouse = 0;
			foreach ($warehouse_count_by_address[$product['id_address_delivery']] as $id_war => $val){
				if (in_array((int)$id_war, $product['warehouse_list'])){
					$product['carrier_list'] = array_merge($product['carrier_list'], Carrier::getAvailableCarrierList(new Product($product['id_product']), $id_war, $product['id_address_delivery'], null, $this));
					if (!$id_warehouse)
						$id_warehouse = (int)$id_war;
				}
			}

			if (!isset($grouped_by_warehouse[$product['id_address_delivery']]['in_stock'][$id_warehouse])){
				$grouped_by_warehouse[$product['id_address_delivery']]['in_stock'][$id_warehouse] = array();
				$grouped_by_warehouse[$product['id_address_delivery']]['out_of_stock'][$id_warehouse] = array();
			}

			if (!$this->allow_seperated_package)
				$key = 'in_stock';
			else
				$key = $product['in_stock'] ? 'in_stock' : 'out_of_stock';

			if (empty($product['carrier_list']))
				$product['carrier_list'] = array(0);

			$grouped_by_warehouse[$product['id_address_delivery']][$key][$id_warehouse][] = $product;
		}
		unset($product);

		// Step 3 : grouped product from grouped_by_warehouse by available carriers
		$grouped_by_carriers = array();
		foreach ($grouped_by_warehouse as $id_address_delivery => $products_in_stock_list){
			if (!isset($grouped_by_carriers[$id_address_delivery]))
				$grouped_by_carriers[$id_address_delivery] = array(
					'in_stock' => array(),
					'out_of_stock' => array(),
				);
			foreach ($products_in_stock_list as $key => $warehouse_list){
				if (!isset($grouped_by_carriers[$id_address_delivery][$key]))
					$grouped_by_carriers[$id_address_delivery][$key] = array();
				foreach ($warehouse_list as $id_warehouse => $product_list){
					if (!isset($grouped_by_carriers[$id_address_delivery][$key][$id_warehouse]))
						$grouped_by_carriers[$id_address_delivery][$key][$id_warehouse] = array();
					foreach ($product_list as $product){
						$package_carriers_key = implode(',', $product['carrier_list']);

						if (!isset($grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key]))
							$grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key] = array(
								'product_list' => array(),
								'carrier_list' => $product['carrier_list'],
								'warehouse_list' => $product['warehouse_list']
							);

						$grouped_by_carriers[$id_address_delivery][$key][$id_warehouse][$package_carriers_key]['product_list'][] = $product;
					}
				}
			}
		}

		$package_list = array();
		// Step 4 : merge product from grouped_by_carriers into $package to minimize the number of package
		foreach ($grouped_by_carriers as $id_address_delivery => $products_in_stock_list){
			if (!isset($package_list[$id_address_delivery]))
				$package_list[$id_address_delivery] = array(
					'in_stock' => array(),
					'out_of_stock' => array(),
				);

			foreach ($products_in_stock_list as $key => $warehouse_list){
				if (!isset($package_list[$id_address_delivery][$key]))
					$package_list[$id_address_delivery][$key] = array();
				// Count occurance of each carriers to minimize the number of packages
				$carrier_count = array();
				foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers){
					foreach ($products_grouped_by_carriers as $data){
						foreach ($data['carrier_list'] as $id_carrier){
							if (!isset($carrier_count[$id_carrier]))
								$carrier_count[$id_carrier] = 0;
							$carrier_count[$id_carrier]++;
						}
					}
				}
				arsort($carrier_count);
				foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers){
					if (!isset($package_list[$id_address_delivery][$key][$id_warehouse]))
						$package_list[$id_address_delivery][$key][$id_warehouse] = array();
					foreach ($products_grouped_by_carriers as $data){
						foreach ($carrier_count as $id_carrier => $rate){
							if (in_array($id_carrier, $data['carrier_list'])){
								if (!isset($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]))
									$package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier] = array(
										'carrier_list' => $data['carrier_list'],
										'warehouse_list' => $data['warehouse_list'],
										'product_list' => array(),
									);
								$package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['carrier_list'] =
									array_intersect($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['carrier_list'], $data['carrier_list']);
								$package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['product_list'] =
									array_merge($package_list[$id_address_delivery][$key][$id_warehouse][$id_carrier]['product_list'], $data['product_list']);

								break;
							}
						}
					}
				}
			}
		}

		// Step 5 : Reduce depth of $package_list
		$final_package_list = array();
		foreach ($package_list as $id_address_delivery => $products_in_stock_list){
			if (!isset($final_package_list[$id_address_delivery]))
				$final_package_list[$id_address_delivery] = array();

			foreach ($products_in_stock_list as $key => $warehouse_list)
				foreach ($warehouse_list as $id_warehouse => $products_grouped_by_carriers)
					foreach ($products_grouped_by_carriers as $data){
						$final_package_list[$id_address_delivery][] = array(
							'product_list' => $data['product_list'],
							'carrier_list' => $data['carrier_list'],
							'warehouse_list' => $data['warehouse_list'],
							'id_warehouse' => $id_warehouse,
						);
					}
		}
		$cache[(int)$this->id] = $final_package_list;
		return $final_package_list;
	}

    public function is_formula(){
        //Optener lista de productos del carrito
        $pruducts = $this->getProducts();
        // recorrer cada producto y validar si requiere formula medica
        foreach ($pruducts as &$valor) {
            // crear un nuevo producto
            $product = new Product($valor['id_product'], true, $this->context->language->id, $this->context->shop->id);
            // obtener las caracteristicas del producto
            $features = $product->getFrontFeatures($this->context->language->id);
            foreach($features as $value){
                if($value['name'] === 'Requiere fórmula médica'&&isset($value['value'])){
                    if( strtoupper($value['value']) === 'SI'){
                        return true;
                    }
                }
            }
        }
        return false;
    }

	public static function prodsHasFormula ($array_prods) {
		$queryProdsFormula = " SELECT count(1) AS hasformula FROM 
			ps_feature_product fpp 
			INNER JOIN ps_feature_lang fll ON ( fpp.id_feature = fll.id_feature ) 
			INNER JOIN ps_feature_value_lang fvl ON ( fvl.id_feature_value = fpp.id_feature_value )
			WHERE fpp.id_product IN ( ". implode(',', $array_prods)." ) AND fll.`name` LIKE '%requiere%' AND fvl.`value` LIKE '%si%' ";

			$resultQueryProdsFormula = Db::getInstance()->ExecuteS($queryProdsFormula);

		if ( $resultQueryProdsFormula && $resultQueryProdsFormula[0]['hasformula'] > 0 ) {
			return true;
		}
        else {
			return false;
		}
	}


	public function getCartRules($filter = CartRule::FILTER_ACTION_ALL)
	{
            //--//error_log("\r\n  method getCartRules this->id : ".$this->id, 3, "/tmp/progresivo.log");
		// If the cart has not been saved, then there can't be any cart rule applied
		if (!CartRule::isFeatureActive() || !$this->id)
			return array();

		$cache_key = 'Cart::getCartRules'.$this->id.'-'.$filter;

		//--//error_log("\r\n  method getCartRules: ".$cache_key, 3, "/tmp/progresivo.log");

		if (!Cache::isStored($cache_key))
		{

			$result = Db::getInstance()->executeS('
				SELECT *
				FROM `'._DB_PREFIX_.'cart_cart_rule` cd
				LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON cd.`id_cart_rule` = cr.`id_cart_rule`
				LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (
					cd.`id_cart_rule` = crl.`id_cart_rule`
					AND crl.id_lang = '.(int)$this->id_lang.'
				)
				WHERE `id_cart` = '.(int)$this->id.'
				'.($filter == CartRule::FILTER_ACTION_SHIPPING ? 'AND free_shipping = 1' : '').'
				'.($filter == CartRule::FILTER_ACTION_GIFT ? 'AND gift_product != 0' : '').'
				'.($filter == CartRule::FILTER_ACTION_REDUCTION ? 'AND (reduction_percent != 0 OR reduction_amount != 0)' : '')
				.' ORDER by cr.priority ASC'
			);

			//--//error_log("\r\n  NO CARGO CACHE, EJECUTO QUERY", 3, "/tmp/progresivo.log");

			Cache::store($cache_key, $result);
		}
		$result = Cache::retrieve($cache_key);

		// Define virtual context to prevent case where the cart is not the in the global context
		$virtual_context = Context::getContext()->cloneContext();
		$virtual_context->cart = $this;

		foreach ($result as &$row)
		{
			$row['obj'] = new CartRule($row['id_cart_rule'], (int)$this->id_lang);
			$row['value_real'] = $row['obj']->getContextualValue(true, $virtual_context, $filter);
			$row['value_tax_exc'] = $row['obj']->getContextualValue(false, $virtual_context, $filter);

			/////--//error_log("\r\n  id_cart_rule: ".$row['id_cart_rule'], 3, "/tmp/progresivo.log");
			/////--//error_log(" - value_real: ".$row['value_real'], 3, "/tmp/progresivo.log");
			/////--//error_log(" - value_tax_exc: ".$row['value_tax_exc'], 3, "/tmp/progresivo.log");

			// Retro compatibility < 1.5.0.2
			$row['id_discount'] = $row['id_cart_rule'];
			$row['description'] = $row['name'];
		}

                //--//error_log("\r\n  Cart::getCartRules result: ", 3, "/tmp/ordererror.log");
		return $result;
	}


    public function addCartRule($id_cart_rule)
    {
        $result = parent::addCartRule($id_cart_rule);

        if (Tools::isSubmit('submitAddDiscount') && $result) {
            if (Module::isEnabled('quantitydiscountpro')) {
                include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
                $quantityDiscount = new QuantityDiscountRule();
                $quantityDiscountRulesAtCart = $quantityDiscount->getQuantityDiscountRulesAtCart((int)Context::getContext()->cart->id);

                if (is_array($quantityDiscountRulesAtCart) && count($quantityDiscountRulesAtCart)) {
                    foreach ($quantityDiscountRulesAtCart as $quantityDiscountRuleAtCart) {
                        $quantityDiscountRuleAtCartObj = new QuantityDiscountRule((int)$quantityDiscountRuleAtCart['id_quantity_discount_rule']);
                        if (!$quantityDiscount->compatibleCartRules($quantityDiscountRuleAtCartObj)) {
                            $quantityDiscount->removeQuantityDiscountCartRule($quantityDiscountRuleAtCart['id_cart_rule'], (int)Context::getContext()->cart->id);
                        }
                    }
                }
            }
        }

        return $result;
    }
}
