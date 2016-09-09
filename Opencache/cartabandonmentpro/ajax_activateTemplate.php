<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token_cartabandonment');
$token_bdd = Configuration::get('CARTABAND_TOKEN');
if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token)
{
	include_once('controllers/TemplateController.class.php');
	$template_id = Tools::getValue('template_id');
	$active = Tools::getValue('active');

	echo TemplateController::activate($template_id, $active);
	die;
}
else{
	echo 'hack ...';die;
}