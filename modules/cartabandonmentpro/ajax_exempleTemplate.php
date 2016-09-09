<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token_cartabandonment');
$token_bdd = Configuration::get('CARTABAND_TOKEN');
if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token)
{
	$model_id = Tools::getValue('model_id');
	$lg = Tools::getValue('lg');
	if(file_exists(realpath('./') . '/model/' . $model_id . '_exemple_' . $lg . '.tpl'))
		echo Tools::file_get_contents(realpath('./') . '/model/' . $model_id . '_exemple_' . $lg . '.tpl');
	else
		echo Tools::file_get_contents(realpath('./') . '/model/' . $model_id . '_exemple_en.tpl');
	die;
}
else{
	echo 'hack ...';die;
}