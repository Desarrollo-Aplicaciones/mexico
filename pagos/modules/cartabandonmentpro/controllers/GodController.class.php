<?php
class GodController extends FrontController{

	public function __construct (){
		if(Tools::getValue('edit') == 1){
			$reminderController = new ReminderController();
			$reminderController->edit();
			$this->redirect();
		}
		if(Tools::getValue('conf') == 1){
			ConfController::setMaxDateReminder();
			$this->redirect();
		}
	}
	
	public static function getTemplate(){
		if(self::isDebug())
			return 'views/templates/admin/debug.tpl';
		if(self::isFirstTime())
			return 'views/templates/admin/first_time.tpl';
		else
			return 'views/templates/admin/configuration.tpl';
	}
	private static function isFirstTime(){
		return false;
	}
	
	private static function isDebug(){
		return false;
	}
}