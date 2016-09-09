<?php
class StatsController{

	public function __construct(){
	}
	
	public static function getStatsForReminder($wichReminder = false){
		$query = "SELECT COUNT(cr.id_cart) AS count, SUM(cr.visualize) AS view, SUM(click) AS click, SUM(click_cart) AS click_cart
				  FROM "._DB_PREFIX_."cartabandonment_remind cr";

		return Db::getInstance()->ExecuteS($query);
	}
	
	public static function getUnsubscribe($wichReminder = false){
		$query = "SELECT COUNT(id_customer) AS nb
				  FROM "._DB_PREFIX_."cartabandonment_unsubscribe";

		return Db::getInstance()->getValue($query);
	}
	
	public static function getTransformedCarts(){
		$query	=  'SELECT `ca`.id_cart, ord.total_paid
					FROM `'._DB_PREFIX_.'cart` `ca`
					INNER JOIN `'._DB_PREFIX_.'orders` `ord` ON `ord`.`id_cart` = `ca`.`id_cart`
					JOIN '._DB_PREFIX_.'cart_product cp ON ca.id_cart = cp.id_cart
					JOIN '._DB_PREFIX_.'cartabandonment_remind cr ON ord.id_cart = cr.id_cart
					WHERE `ord`.`id_order` IS NOT NULL';
					
		return Db::getInstance()->ExecuteS($query);
	}

}