<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token');
$token_bdd = Configuration::get('CARTABAND_TOKEN');

if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token)
{
	require_once dirname(__FILE__).'/controllers/ReminderController.class.php';
	require_once dirname(__FILE__).'/controllers/TemplateController.class.php';
	require_once dirname(__FILE__).'/classes/Template.class.php';
	$id_shop = Tools::getValue('id_shop');
	$wich_remind = Tools::getValue('wich_remind');

	if(!$id_shop){
		echo 'No shop ...';die;
	}
	if(!$wich_remind){
		echo 'No remind number ...';die;
	}
	$carts = ReminderController::getAbandonedCart($wich_remind, $id_shop);
	$templates = TemplateController::getActiveTemplate($id_shop);
	if(!$templates)
		die('No active template ...');
	$x = 0;

	foreach($carts as $cart){
		
		$iso = Language::getIsoById($cart['id_lang']);
		$id_lang = $cart['id_lang'];

		if(!file_exists('mails/' . $iso . '/' . $templates[$cart['id_shop']][$cart['id_lang']][1]['id'] . '.html')){
			$id_lang = Configuration::get('PS_LANG_DEFAULT');
			$iso = Language::getIsoById($id_lang);
		}

		$content = Tools::file_get_contents('mails/' . $iso . '/' . $templates[$cart['id_shop']][$id_lang][1]['id'] . '.html');
		$content = Template::editBeforeSending($content, $cart['id_cart'], $id_lang, $wich_remind);

		if(!$content) continue;

		$title	 = Template::editTitleBeforeSending($templates[$cart['id_shop']][$id_lang][1]['name'], $cart['id_cart'], $id_lang);
		
		$fp = fopen('mails/' . $iso . '/send.html', 'w+');
		fwrite($fp, $content);
		fclose($fp);
		$fp = fopen('mails/' . $iso . '/send.txt', 'w+');
		fwrite($fp, $content);
		fclose($fp);

		$mail = Mail::Send($id_lang, 'send', $title, array(), $cart['email'], null, null, null, null, null, dirname(__FILE__) . '/mails/');

		if($mail){
			Db::getInstance()->Execute("INSERT INTO "._DB_PREFIX_."cartabandonment_remind VALUES (NULL, " . $wich_remind . ", " . $cart['id_cart'] . ", NOW(), 0, 0, 0)");
			$x++;
		}
	}
	echo $x . ' mails have been sent.';
}
else{
	echo 'hack ...';die;
}