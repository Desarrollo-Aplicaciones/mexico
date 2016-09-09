<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token_cartabandonment');
$token_bdd = Configuration::get('CARTABAND_TOKEN');
if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token)
{
	include_once('classes/Template.class.php');
	include_once('controllers/TemplateController.class.php');
	include_once('classes/Model.class.php');
	$template_id = Tools::getValue('template_id');
	$iso = Language::getIsoById(Tools::getValue('language'));
	
	$content = Tools::file_get_contents(realpath('./') . '/mails/' . $iso . '/' . $template_id . '.html');
	
	$template = new Template($template_id, new Model(TemplateController::getModelByTemplate($template_id)));
	echo $template->editTemplate($content);
	die;
}
else{
	echo 'hack ...';die;
}