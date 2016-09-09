<?php

class SpecificPrice extends SpecificPriceCore
{
	public static function getByProductId($id_product, $id_product_attribute = false, $id_cart = false, $getavailable = false)
	{
		$querygetByProductId = '
			SELECT *
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE `id_product` = '.(int)$id_product.
			($id_product_attribute ? ' AND id_product_attribute = '.(int)$id_product_attribute : '').'
			AND id_cart = '.(int)$id_cart;

		// se valida si la variable getavailable es true, para validar si esta vigente el descuento por categoría
		if ( $getavailable ) {
			$querygetByProductId .= ' AND ( NOW() BETWEEN `from` AND `to` )';
		}

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($querygetByProductId);
	}
}

