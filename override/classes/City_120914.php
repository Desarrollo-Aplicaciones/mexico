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
		WHERE cit.id_state = '. $id_state .'
		GROUP BY cit.id_city, cit.city_name
		ORDER BY cit.city_name ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($q_city_unique);
	}

}

