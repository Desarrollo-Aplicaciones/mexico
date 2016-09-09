<?php  
  
class Order extends OrderCore {  
	
	public function getHistory($id_lang, $id_order_state = false, $no_hidden = false, $filters = 0)
	{
		if (!$id_order_state)
			$id_order_state = 0;
	
		$logable = false;
		$delivery = false;
		$paid = false;
		$shipped = false;
		if ($filters > 0)
		{
			if ($filters & OrderState::FLAG_NO_HIDDEN)
				$no_hidden = true;
			if ($filters & OrderState::FLAG_DELIVERY)
				$delivery = true;
			if ($filters & OrderState::FLAG_LOGABLE)
				$logable = true;
			if ($filters & OrderState::FLAG_PAID)
				$paid = true;
			if ($filters & OrderState::FLAG_SHIPPED)
				$shipped = true;
		}
	
		if (!isset(self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters]) || $no_hidden)
		{
			$id_lang = $id_lang ? (int)($id_lang) : 'o.`id_lang`';
			$result = Db::getInstance()->executeS('
			SELECT oh.*, e.`firstname` AS employee_firstname, e.`lastname` AS employee_lastname, osl.`name` AS ostate_name, ot.`extras` AS `extras`
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON o.`id_order` = oh.`id_order`
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = oh.`id_order_state`
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)($id_lang).')
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = oh.`id_employee`
			LEFT JOIN `'._DB_PREFIX_.'orders_transporte` ot ON (o.`id_order` = ot.`id_order`)
			WHERE oh.id_order = '.(int)($this->id).'
			'.($no_hidden ? ' AND os.hidden = 0' : '').'
			'.($logable ? ' AND os.logable = 1' : '').'
			'.($delivery ? ' AND os.delivery = 1' : '').'
			'.($paid ? ' AND os.paid = 1' : '').'
			'.($shipped ? ' AND os.shipped = 1' : '').'
			'.((int)($id_order_state) ? ' AND oh.`id_order_state` = '.(int)($id_order_state) : '').'
			ORDER BY oh.date_add DESC, oh.id_order_history DESC');
			if ($no_hidden)
				return $result;
			self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters] = $result;
		}
		return self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters];
	}
	
}