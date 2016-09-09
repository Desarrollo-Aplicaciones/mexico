<?php  
  
class Carrier extends CarrierCore {
	/**
	 * Get all carriers in a given language
	 *
	 * @param integer $id_lang Language id
	 * @param $modules_filters, possible values:
			PS_CARRIERS_ONLY
			CARRIERS_MODULE
			CARRIERS_MODULE_NEED_RANGE
			PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
			ALL_CARRIERS
	 * @param boolean $active Returns only active carriers when true
	 * @return array Carriers
	 */
	public static function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = null, $modules_filters = self::PS_CARRIERS_ONLY)
	{
		if (!Validate::isBool($active))
			die(Tools::displayError());
		if ($ids_group)
		{
			$ids = '';
			foreach ($ids_group as $id)
				$ids .= (int)$id.', ';
			$ids = rtrim($ids, ', ');
			if ($ids == '')
				return array();
		}

$contextOri = Context::getContext();
$contextClone = Context::getContext()->cloneContext();

		if(isset($contextClone->cart->id_address_delivery)) { 

			$add_delivery = $contextClone->cart->id_address_delivery;

		} elseif(isset($contextOri->cart->id_address_delivery)) { 

			$add_delivery = $contextOri->cart->id_address_delivery;

		} else {
			$carrier_sel = '';
			$add_delivery = '';
		}


		if($add_delivery != '') { //Seleccionar el transportador con menor costo de envío

			//echo "<br><br>carrier: ".
			$sql_car = 'SELECT cac.precio_kilo, car.id_carrier, crp.delimiter2, car2.id_carrier AS carrier_cp, 
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
			WHERE adc.id_address='.$add_delivery.'
			AND ( ( car.id_carrier IS NOT NULL AND crp.delimiter2 IS NOT NULL ) 
						OR ( car2.id_carrier IS NOT NULL AND crp2.delimiter2 IS NOT NULL ) 
					)
			GROUP BY car.id_carrier, car2.id_carrier
			ORDER BY adtrcp.precio ASC, cac.precio_kilo ASC, crp.delimiter2 DESC, crp2.delimiter2 DESC';
			//asignar al contexto el id_carrier con el menor costo de envio al destino seleccionado
			
			if ( $resultado=Db::getInstance()->executeS($sql_car) ) {
				if ( $resultado[0]['carrier_cp'] != null ) {
					//echo "<br>3666";
					Context::getContext()->cart->id_carrier = $carrier_sel = $resultado[0]['carrier_cp'];
					Context::getContext()->cloneContext()->cart->id_carrier = $resultado[0]['carrier_cp'];

				} elseif ( $resultado[0]['id_carrier'] != null ) {
					//echo "<br>2555";
					Context::getContext()->cart->id_carrier = $carrier_sel = $resultado[0]['id_carrier'];
					Context::getContext()->cloneContext()->cart->id_carrier = $resultado[0]['id_carrier'];

				} else {
				$carriers = array();
				return $carriers;
			}
			} else {
				$carriers = array();
				return $carriers;
			}	
		}
		//echo "<br> sel: ".$carrier_sel;

				$sql = 'SELECT c.*, cl.delay
				FROM `'._DB_PREFIX_.'carrier` c
				LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz ON (cz.`id_carrier` = c.`id_carrier`)'.
				($id_zone ? 'LEFT JOIN `'._DB_PREFIX_.'zone` z ON (z.`id_zone` = '.(int)$id_zone.')' : '').'
				'.Shop::addSqlAssociation('carrier', 'c').'
				WHERE c.`deleted` = '.($delete ? '1' : '0').
					($active ? ' AND c.`active` = 1' : '').					
					($carrier_sel ? ' AND c.id_carrier = '.$carrier_sel : '').
					($id_zone ? ' AND cz.`id_zone` = '.(int)$id_zone.'
					AND z.`active` = 1 ' : ' ');
		switch ($modules_filters)
		{
			case 1 :
				$sql .= 'AND c.is_module = 0 ';
			break;
			case 2 :
				$sql .= 'AND c.is_module = 1 ';
			break;
			case 3 :
				$sql .= 'AND c.is_module = 1 AND c.need_range = 1 ';
			break;
			case 4 :
				$sql .= 'AND (c.is_module = 0 OR c.need_range = 1) ';
			break;
			case 5 :
				$sql .= '';
			break;

		}
		//echo "<br>sql: ".
		$sql .= ($ids_group ? ' AND c.id_carrier IN (SELECT id_carrier FROM '._DB_PREFIX_.'carrier_group WHERE id_group IN ('.$ids.')) ' : '').'
			GROUP BY c.`id_carrier`
			ORDER BY c.`position` ASC';

		$carriers = Db::getInstance()->executeS($sql);

		if (is_array($carriers) && count($carriers))
		{
			foreach ($carriers as $key => $carrier)
				if ($carrier['name'] == '0')
					$carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
		}
		else
			$carriers = array();

		return $carriers;
	}

	public static function getCarrierList(){
		$query="select id_reference,`name` 
				from ps_carrier 
				where shipping_handling = 0
				GROUP BY id_reference;";
		return Db::getInstance()->executeS($query);
	}	
}