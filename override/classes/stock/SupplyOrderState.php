<?php
class SupplyOrderState extends SupplyOrderStateCore
{
	public static function getSupplyOrderStates($id_state_referrer = null, $id_lang = null)
	{
		if ($id_lang == null)
			$id_lang = Context::getContext()->language->id;

		$query = new DbQuery();
		$query->select('sl.name, s.id_supply_order_state');
		$query->from('supply_order_state', 's');
		$query->leftjoin('supply_order_state_lang', 'sl', 's.id_supply_order_state = sl.id_supply_order_state AND sl.id_lang='.(int)$id_lang);

		if (!is_null($id_state_referrer))
		{
			$is_receipt_state = false;
			$is_editable = false;
			$is_delivery_note = false;
			$is_pending_receipt = false;

			//check current state to see what state is available
			$state = new SupplyOrderState((int)$id_state_referrer);
			if (Validate::isLoadedObject($state))
			{
				$is_receipt_state = $state->receipt_state;
				$is_editable = $state->editable;
				$is_delivery_note = $state->delivery_note;
				$is_pending_receipt = $state->pending_receipt;
			}
			 $profile = new Profile(Context::getContext()->employee->id_profile);
			if( !strpos($profile->name[1], '(edit)')) // si el nombre del perfil actual contiene la etiqueta "(edit)", entonces se permite al usuario actual habilitar la edición de la orden
			$query->where('s.editable = 0');

			// if( Context::getContext()->employee->id_profile != 1 && Context::getContext()->employee->id_profile !=2  && Context::getContext()->employee->id_profile != 23 ){
			// 	$query->where('s.editable = 0');
			// }
			$query->where('s.id_supply_order_state <> '.$id_state_referrer);

			//check first if the order is editable
			if ($is_editable)
				$query->where('s.editable = 1 OR s.delivery_note = 1 OR s.enclosed = 1');
			//check if the delivery note is available or if the state correspond to a pending receipt state
			else if ($is_delivery_note || $is_pending_receipt)
				$query->where('(s.delivery_note = 0) OR s.enclosed = 1');
			//check if the state correspond to a receipt state
			else if ($is_receipt_state)
				$query->where('s.receipt_state = 1 OR s.enclosed = 1');
		}

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}
	public static function getSupplyOrderCauses($id_state_referrer = null, $id_lang = null)
	{
		$query = new DbQuery();
		$query->select('*');
		$query->from('edit_supply_order');
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}
	public static function editSupplyOrder($id_supply_order, $cause){
		$data['edit_cause'] = $cause;
		Db::getInstance()->update('supply_order', $data, 'id_supply_order = '.$id_supply_order);
	}
	public static function getOrderCause($id_supply_order){
		$query = new DbQuery();
		$query->select('edit_cause');
		$query->from('supply_order');
		$query->where('id_supply_order = '.$id_supply_order);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		return $result[0]['edit_cause'];
	}
}