<?php
class ReminderController extends FrontController{

	public function __construct (){
			
	}

	public function edit(){
		if(!Tools::getValue('tpl')) return false;

		$this->generateTemplate();
		header('Location: ' . Tools::getValue('uri') . '&justEdited=1&cartabandonment_conf=1');die;
	}
	
	private function getReminds(){
		return Db::getInstance()->ExecuteS("SELECT wich_remind FROM "._DB_PREFIX_."cartabandonment_remind_config WHERE active = 1 ORDER BY wich_remind;");
	}
	
	private function getEditedTemplate($id_tpl){
		if(!isset($id_tpl) || is_null($id_tpl) || $id_tpl == 0)
			$id_tpl = null;
		return $id_tpl;
	}
	
	private function sameTemplate(){
		$id_tpl = (int)Tools::getValue('edittpl1');

		$id_template = $this->saveTemplate($this->getEditedTemplate($id_tpl), 1, Tools::getValue('name_1'));
		if(!$id_template)
			d('Erreur lors de l\'enregistrement du template');
		$this->save(1, $id_template, Tools::getValue('tpl_same'));
		$this->save(2, $id_template, Tools::getValue('tpl_same'));
		$this->save(3, $id_template, Tools::getValue('tpl_same'));
	}
	
	private function generateTemplate(){
		for($x = 1;$x <= 3;$x++){
			$id_tpl = (int)Tools::getValue('edittpl' . $x);

			$id_template = $this->saveTemplate($this->getEditedTemplate($id_tpl), $x, Tools::getValue('name_' . $x));
			if(!$id_template)
				d('Erreur lors de l\'enregistrement du template');	
			$this->save($x, $id_template, Tools::getValue('tpl_same'));
		}
	}

	private function save($remind, $id_template, $tpl_same){
		$query = "REPLACE INTO " . _DB_PREFIX_ . "cartabandonment_remind_lang VALUE(".$remind.", ".Tools::getValue('language').", ".(int)$id_template.", ".(int)$tpl_same.", ".pSQL(Tools::getValue('id_shop')).")";
		if(!Db::getInstance()->Execute($query))
			d('Erreur lors de l\'enregistrement du template');
	}
	
	private function saveTemplate($id_tpl, $wich_template, $name){
		$model_id = Tools::getValue('model' . $wich_template);
		$template = new Template($id_tpl, new Model($model_id), $wich_template);
		$template->setName($name);
		return $template->save();
	}
	
	public static function setDays($wichRemind, $val){
		$query = "UPDATE " . _DB_PREFIX_ . "cartabandonment_remind_config SET days = " . $val . " WHERE wich_remind = " . $wichRemind;

		return DB::getInstance()->Execute($query);
	}
	public static function setHours($wichRemind, $val){
		$query = "UPDATE " . _DB_PREFIX_ . "cartabandonment_remind_config SET hours = " . $val . " WHERE wich_remind = " . $wichRemind;
		return DB::getInstance()->Execute($query);
	}
	public static function setActive($wichRemind, $val){
		$query = "UPDATE " . _DB_PREFIX_ . "cartabandonment_remind_config SET active = " . $val . " WHERE wich_remind = " . $wichRemind;
		return DB::getInstance()->Execute($query);
	}
	public static function setMaxReminder($val){
		return Configuration::updateValue('CART_MAXREMINDER', $val);
	}
	public static function setMaxReminderWhat($val){
		return Configuration::updateValue('CART_MAXREMINDER_WHAT', $val);
	}
	
	// public static function getAbandonedCart($wichReminder, $idShop){
		// $query	=  'SELECT `ca`.*, `cu`.`firstname`, `cu`.`lastname`, `cu`.`id_customer`, `cu`.`email`
					// FROM `'._DB_PREFIX_.'cart` `ca`
					// LEFT JOIN `'._DB_PREFIX_.'orders` `ord` ON `ord`.`id_cart` = `ca`.`id_cart`
					// INNER JOIN `'._DB_PREFIX_.'customer` `cu` ON `cu`.`id_customer` = `ca`.`id_customer`
					// WHERE `ord`.`id_order` IS NULL';
		// $reminder = ReminderController::getReminder($wichReminder);
		// $maxReminder = ReminderController::getMaxReminder();
		// $query .= ' AND ca.date_upd <= DATE_SUB(NOW(),INTERVAL ' . pSql($reminder) . ')';
		
		// $query .= ' AND ca.date_upd >= DATE_SUB(NOW(),INTERVAL ' . pSql($maxReminder) . ') AND (';
		
		// if($wichReminder > 1){
			// $remind = $wichReminder-1;
			// $reminder2 = ReminderController::getReminder($remind);
			// $query .= 'ca.id_cart IN (SELECT cr.id_cart FROM '._DB_PREFIX_.'cartabandonment_remind cr WHERE wich_remind = ' . (int)$remind . ')
					   // AND ';
		// }
		// $query .= ' ca.id_cart NOT IN (SELECT cr.id_cart FROM '._DB_PREFIX_.'cartabandonment_remind cr WHERE wich_remind = ' . (int)$wichReminder. '))';

		// $results = DB::getInstance()->ExecuteS($query);
		// return $results;
	// }
	
	public static function getAbandonedCart($wichReminder){
		$query	=  'SELECT `ca`.*, `cu`.`firstname`, `cu`.`lastname`, `cu`.`id_customer`, `cu`.`email`
					FROM `'._DB_PREFIX_.'cart` `ca`
					LEFT JOIN `'._DB_PREFIX_.'orders` `ord` ON `ord`.`id_cart` = `ca`.`id_cart`
					INNER JOIN `'._DB_PREFIX_.'customer` `cu` ON `cu`.`id_customer` = `ca`.`id_customer`
					JOIN '._DB_PREFIX_.'cart_product cp ON ca.id_cart = cp.id_cart
					WHERE `ord`.`id_order` IS NULL';
		$reminder = ReminderController::getReminder($wichReminder);
		$maxReminder = ReminderController::getMaxReminder();
		$query .= ' AND ca.date_upd <= "' . $reminder . '"';
		
		$query .= ' AND ca.date_upd >= "' . pSql($maxReminder) . '" '; 
		$query .= 'AND (';
		if($wichReminder > 1){
			$remind = $wichReminder-1;
			// $reminder2 = ReminderController::getReminder($remind);
			$query .= 'ca.id_cart IN (SELECT cr.id_cart FROM '._DB_PREFIX_.'cartabandonment_remind cr WHERE wich_remind = ' . (int)$remind . ')
					   AND ';
		}
		$query .= ' ca.id_cart NOT IN (SELECT cr.id_cart FROM '._DB_PREFIX_.'cartabandonment_remind cr WHERE wich_remind = ' . (int)$wichReminder. '))
					AND ca.id_customer NOT IN (SELECT id_customer FROM '._DB_PREFIX_.'cartabandonment_unsubscribe)
					GROUP BY cu.email';

		$results = DB::getInstance()->ExecuteS($query);

		return $results;
	}
	
	public static function getRemindersByLanguage($id_lang = 1, $id_shop = 1){
		$query = "SELECT id_template, tpl_same FROM " . _DB_PREFIX_ . "cartabandonment_remind_lang WHERE id_lang = " . (int)$id_lang . " AND id_shop = " . (int)$id_shop;
		return DB::getInstance()->ExecuteS($query);
	}
	
	public static function getReminders($wichRemind){
		return DB::getInstance()->ExecuteS("SELECT * FROM " . _DB_PREFIX_ . "cartabandonment_remind_config WHERE wich_remind = " . (int)$wichRemind);
	}
	
	private static function getReminder($wichRemind){
		$remind = ReminderController::getReminders($wichRemind);
		$startDate = time();

		$date = date('Y-m-d H:i:s', strtotime('-'. $remind[0]['days'] . ' day', $startDate));
		$date = date('Y-m-d H:i:s', strtotime('-'. $remind[0]['hours'] . ' hours', $startDate));

		return $date;
	}
	
	// private static function getReminder($wichReminder){
		
		// $reminder = Configuration::get('CART_' . $wichReminder . 'REMINDER_WHAT');
		// $time = Configuration::get('CART_' . $wichReminder . 'REMINDER');
		
		// switch($reminder){
			// case 'minutes':
				// return $time . ' MINUTE';
			// break;
			// case 'hours':
				// return $time . ' HOUR';
			// break;
			// default:
			// case 'day':
				// return $time . ' DAY';
			// break;
		// }
	// }
	
	private static function getMaxReminder(){
		$reminder = Configuration::get('CART_MAXREMINDER_WHAT');
		$time = Configuration::get('CART_MAXREMINDER');

		$startDate = time();

		switch($reminder){
			case 'minutes':
				return date('Y-m-d H:i:s', strtotime('-'. $time . ' minutes', $startDate));
				// return $time . ' MINUTE';
			break;
			case 'hours':
				return date('Y-m-d H:i:s', strtotime('-'. $time . ' hours', $startDate));
				// return $time . ' HOUR';
			break;
			default:
			case 'days':
				return date('Y-m-d H:i:s', strtotime('-'. $time . ' day', $startDate));
				// return $time . ' DAY';
			break;
		}
	}
}