<?php
class ConfController{

	public function __construct(){
		
	}
	
	public static function setMaxDateReminder(){
		$max_reminder 	= Tools::getValue('max_reminder');
		$max_what		= Tools::getValue('max_what');
		Db::getInstance()->Execute("REPLACE INTO "._DB_PREFIX_."cartabandonment_conf VALUES (NULL, ".$max_reminder.", '".$max_what."')");
	}
	
	
	
	public static function getMaxDateReminder(){
	
	}
}