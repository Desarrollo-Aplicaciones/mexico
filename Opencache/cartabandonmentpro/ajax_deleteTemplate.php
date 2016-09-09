<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token_cartabandonment');
$token_bdd = Configuration::get('CARTABAND_TOKEN');
if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token)
{
	require_once dirname(__FILE__).'/controllers/TemplateController.class.php';
	$id_template = Tools::getValue('template_id');
	$iso = Language::getIsoById(Tools::getValue('ig_lang'));
	echo TemplateController::deleteTemplate($id_template, $iso);
}
else{
	echo 'hack ...';die;
}
