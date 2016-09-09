<?php

class Warehouse extends WarehouseCore
{
	/**
	 * Gets available warehouses
	 * It is possible via ignore_shop and id_shop to filter the list with shop id
	 *
	 * @param bool $ignore_shop Optional, false by default - Allows to get only the warehouses that are associated to one/some shops (@see $id_shop)
	 * @param int $id_shop Optional, Context::shop::Id by default - Allows to define a specific shop to filter.
	 * @return array Warehouses (ID, reference, name)
	 */
	public static function getAllWarehouses($ignore_shop = false, $id_shop = null)
	{
		if (!$ignore_shop)
			if (is_null($id_shop))
				$id_shop = Context::getContext()->shop->id;

		$query = new DbQuery();
		$query->select('w.id_warehouse, w.reference, w.name');
		$query->from('warehouse', 'w');
		$query->where('deleted = 0');
		$query->orderBy('reference ASC');
		if (!$ignore_shop)
			$query->innerJoin('warehouse_shop', 'ws', 'ws.id_warehouse = w.id_warehouse AND ws.id_shop = '.(int)$id_shop);
		
		if( $results = Db::getInstance()->executeS($query) )
			return $results;

		return array();
	}
}
