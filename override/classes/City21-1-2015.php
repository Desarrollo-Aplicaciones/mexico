<?php


class City extends CityCore
{

    /**
     * @static
     * @param $id_lang
     * @param $id_customer
     * @param bool $active
     * @param bool $includeGeneric
     * @param bool $inStock
     * @param Cart|null $cart
     * @return array
     */
    
	public static function getCitiesByStateAvailable($id_state)
	{
		$q_city_unique='
		SELECT cit.id_city, cit.city_name
		FROM `'._DB_PREFIX_.'cities_col` cit
		INNER JOIN `'._DB_PREFIX_.'carrier_city` car
		ON (car.id_city_des = cit.id_city)
		INNER JOIN ps_state s  ON ( s.id_state = cit.id_state AND s.id_country = '.(int)Configuration::get('PS_COUNTRY_DEFAULT').' )
		WHERE cit.id_state = '. $id_state .'
		GROUP BY cit.id_city, cit.city_name
		ORDER BY cit.city_name ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_unique);
	}

	/**
	 * Listado de Ciudades con código postal y/o ciudad con precio de envío
	 * @param  [type] $id_state [id_del estado]
	 * @return [type]           [array con la respuesta del query]
	 */
	public static function getCitiesByStateAvailableCP($id_state)
	{
		$q_city_unique='
		SELECT cit.id_city, cit.city_name, car.precio_kilo, ptcp.precio, cit.id_state
		FROM `'._DB_PREFIX_.'cities_col` cit
		LEFT JOIN `'._DB_PREFIX_.'carrier_city` car
		ON (car.id_city_des = cit.id_city)		
		INNER JOIN `'._DB_PREFIX_.'state` s  ON ( s.id_state = cit.id_state AND s.id_country = '.(int)Configuration::get('PS_COUNTRY_DEFAULT').' )
		LEFT JOIN `'._DB_PREFIX_.'cod_postal` cp ON ( cp.id_ciudad = cit.id_city)
		LEFT JOIN `'._DB_PREFIX_.'precio_tr_codpos` ptcp ON ( ptcp.codigo_postal = cp.codigo_postal)
		WHERE cit.id_state = '. $id_state .'
		AND ( car.id_city_des IS NOT NULL OR ptcp.codigo_postal IS NOT NULL )
		GROUP BY cit.id_city, cit.city_name
		ORDER BY cit.city_name ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_unique);
	}


	public static function getCitiesByStateAvailableNoCarrier($id_state)
	{
		$q_city_unique='
		SELECT cit.id_city, cit.city_name
		FROM `'._DB_PREFIX_.'cities_col` cit
		WHERE cit.id_state = '. $id_state .'
		GROUP BY cit.id_city, cit.city_name
		ORDER BY cit.city_name ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_unique);
	}

	public static function getColoniaByIdCity($id_city)
	{
		/*$q_colonia_unique="
		SELECT cop.id_codigo_postal, CONCAT(nombre, ' - ', tipo) AS nombrecolonia
		FROM `"._DB_PREFIX_."cod_postal` cop
		WHERE cop.id_ciudad = ". $id_city ."
		GROUP BY cop.id_codigo_postal, cop.nombre
		ORDER BY cop.nombre ASC";

		$q_colonia_unique=" SELECT cop.id_codigo_postal, CONCAT(nombre, ' - ', tipo) AS nombrecolonia
		FROM `"._DB_PREFIX_."precio_tr_codpos` ptcp 
		LEFT JOIN `"._DB_PREFIX_."cod_postal` cop ON ( cop.id_ciudad = ". $id_city ." AND ptcp.codigo_postal = cop.codigo_postal )
		WHERE ( cop.id_ciudad IN ( SELECT pcc.id_city_des FROM ps_carrier_city pcc WHERE pcc.id_city_des = ". $id_city ." ) ) OR
		( cop.id_codigo_postal IS NOT NULL )
		GROUP BY cop.id_codigo_postal, cop.nombre
		ORDER BY cop.nombre ASC";
*/

		$q_colonia_unique="SELECT IF(cop.id_codigo_postal IS NOT NULL,cop.id_codigo_postal,cop2.id_codigo_postal) AS id_codigo_postal , 
		IF ( cop.nombre IS NOT NULL ,CONCAT(cop.nombre, ' - ', cop.tipo), CONCAT(cop2.nombre, ' - ', cop2.tipo)) AS nombrecolonia, 
		cop.codigo_postal, ptcp.codigo_postal, ptcp.id_carrier
		FROM `"._DB_PREFIX_."precio_tr_codpos` ptcp 
		LEFT JOIN `"._DB_PREFIX_."cod_postal` cop ON ( cop.id_ciudad = ". $id_city ." AND ptcp.codigo_postal = cop.codigo_postal )
		LEFT JOIN `"._DB_PREFIX_."carrier_city`cc ON ( cc.id_city_des = ". $id_city ." )
		LEFT JOIN `"._DB_PREFIX_."cod_postal` cop2 ON ( cop2.id_ciudad = cc.id_city_des )
		WHERE ( cc.precio_kilo IS NOT NULL
		) OR
		( cop.id_codigo_postal IS NOT NULL )
		GROUP BY cop.id_codigo_postal, cop.nombre,cop2.id_codigo_postal, cop2.nombre
		ORDER BY cop.nombre,cop2.nombre ASC";

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_colonia_unique);
	}


	public static function getCityByIdCity($id_city)
	{
		$q_city_name=" SELECT city_name FROM `"._DB_PREFIX_."cities_col` WHERE id_city =".$id_city;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_name);
	}

	public static function getIdCodPosIdCityIdStateByPostcode($postcode)
	{
		$sql_location_postcode='
		SELECT cp.id_codigo_postal, cp.codigo_postal, cc.id_city, s.id_state  FROM ps_cod_postal cp 
		INNER JOIN ps_precio_tr_codpos ptcp ON ( ptcp.codigo_postal = cp.codigo_postal )
		INNER JOIN ps_cities_col cc ON (cc.id_city = cp.id_ciudad)
		INNER JOIN ps_state s ON (s.id_state = cc.id_state)
		WHERE s.id_country = '.Configuration::get('PS_COUNTRY_DEFAULT').' AND cp.codigo_postal = '.$postcode.'
		GROUP BY cp.codigo_postal, cc.id_city, s.id_state';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_location_postcode);
	}
	
	public static function getIdCityByIdAddress($id_address, $arr = true)
	{
		$q_city_address='
		SELECT cit.id_city, cit.city_name
		FROM `'._DB_PREFIX_.'cities_col` cit
		INNER JOIN `'._DB_PREFIX_.'address_city` ac ON (cit.id_city = ac.id_city) 
		WHERE ac.id_address = '.$id_address.'
		GROUP BY cit.id_city, cit.city_name
		ORDER BY cit.city_name ASC';
		if ($arr  == true ) {
			$cities = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_address);

			foreach ($result as $row) {
				$cities[$row['id_city']] = $row;
			}
			return $cities;

		} elseif ( $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_address) ) {
			return $result[0]['id_city'];
			
		}
	}

	public static function getCityNameByIdAddress($id_address)
	{
		$q_city_address='
		SELECT cit.city_name
		FROM `'._DB_PREFIX_.'cities_col` cit
		INNER JOIN `'._DB_PREFIX_.'address_city` ac ON (cit.id_city = ac.id_city) 
		WHERE ac.id_address = '.$id_address.'
		GROUP BY cit.id_city, cit.city_name
		ORDER BY cit.city_name ASC';
		if ( $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_address) ) {
			echo $result[0]['city_name'];
			exit;
			return $result[0]['city_name'];			
		}
	}

	public static function getCitiesByIdState($id_state)
	{
		$cities = array();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT cit.id_city, cit.city_name
		FROM `'._DB_PREFIX_.'cities_col` cit
		WHERE cit.id_state = '. $id_state .'
		GROUP BY cit.id_city, cit.city_name
		ORDER BY cit.city_name ASC');
		foreach ($result as $row)
			$cities[$row['id_city']] = $row;
		return $cities;
	}

	public static function getColoniaByIdColonia($id_colonia)
	{
		$colonia = array();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("
			SELECT cop.id_codigo_postal, cop.id_codigo_postal, CONCAT(nombre, ' - ', tipo) AS nombrecolonia
			FROM `"._DB_PREFIX_."cod_postal` cop
			WHERE cop.id_codigo_postal = ". $id_colonia);
		foreach ($result as $row) {
			$colonia[$row['id_codigo_postal']] = $row;
		}		
		return $colonia;
	}
}

