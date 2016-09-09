<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token_cartabandonment');
$token_bdd = Configuration::get('CARTABAND_TOKEN');
if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token)
{
	require_once dirname(__FILE__).'/controllers/TemplateController.class.php';
	require_once dirname(__FILE__).'/controllers/ReminderController.class.php';
	$id_lang	 	= Tools::getValue('id_lang');
	$reminders 		= ReminderController::getRemindersByLanguage($id_lang);
	
	if(!$reminders){
		echo 0;die;
	}
	
	if($reminders[0]['tpl_same'] == 1){
		$template_id 	= $reminders[0]['id_template'];
		$model_id 		= TemplateController::getModelByTemplate($template_id);
		$tpl1 			= Tools::file_get_contents(realpath('./') . '/tpls/' . $template_id . '_1.html');
		$tpl2 			= Tools::file_get_contents(realpath('./') . '/tpls/' . $template_id . '_2.html');
		$tpl3 			= Tools::file_get_contents(realpath('./') . '/tpls/' . $template_id . '_3.html');
		
		$array = array();
		$array['content'] = '<div style="width: 1024px; height: auto; margin: auto;" id="model_' . $model_id . '_1" class="models model_1">' . $tpl1 . '</div>
			  <div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_' . $model_id . '_2" class="models model_1">' . $tpl2 . '</div>
			  <div style="width: 1024px; height: auto; margin: auto; display: none;" id="model_' . $model_id . '_3" class="models model_1">' . $tpl3 . '</div>
			  <input type="hidden" name="test" value="ok">
			  ';
		$array['template_id'] 	= $template_id;
		$array['model_id'] 		= $model_id;
		echo Tools::jsonEncode($array);die;
	}
	else{
		foreach($reminders as $reminder){
			$template_id 	= $reminder['id_template'];
			$model_id 		= TemplateController::getModelByTemplate($template_id);
			$html 			= Tools::file_get_contents(realpath('./') . '/tpls/' . $template_id . '.html');
		}
	}
}
else{
	echo 'hack ...';die;
}