<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token');
$token_bdd = Configuration::get('CARTABAND_TOKEN');
$wichRemind = Tools::getValue('wichRemind');
$id_cart = Tools::getValue('id_cart');
$link = Tools::getValue('link');
if($token != $token_bdd) Tools::redirect();
include_once('controllers/RedirectController.class.php');
$controller = new RedirectController();
$controller->redirect();