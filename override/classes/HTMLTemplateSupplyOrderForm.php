<?php

class HTMLTemplateSupplyOrderForm extends HTMLTemplateSupplyOrderFormCore
{
	protected function getTaxOrderSummary()
	{
		$query = new DbQuery();
		$query->select('
			SUM(unit_price_te*quantity_received) as base_te,
			tax_rate,
			SUM((unit_price_te*quantity_received) * tax_rate/100) as total_tax_value
		');
		$query->from('supply_order_detail');
		$query->where('id_supply_order = '.(int)$this->supply_order->id);
		$query->groupBy('tax_rate');

		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

		foreach ($results as &$result)
		{
			$result['base_te'] = Tools::ps_round($result['base_te'], 2);
			$result['tax_rate'] = Tools::ps_round($result['tax_rate'], 2);
			$result['total_tax_value'] = Tools::ps_round($result['total_tax_value'], 2);
		}
		unset($result); // remove reference

		return $results;
	}
}