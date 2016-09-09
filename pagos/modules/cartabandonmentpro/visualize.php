<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token');
$token_bdd = Configuration::get('CARTABAND_TOKEN');
if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token){
	$wichRemind = Tools::getValue('wichRemind');
	$id_cart = Tools::getValue('id_cart');
	$query = "UPDATE "._DB_PREFIX_."cartabandonment_remind SET visualize = 1 WHERE wich_remind = ".(int)$wichRemind." AND id_cart = ".(int)$id_cart;
	Db::getInstance()->Execute($query);
}