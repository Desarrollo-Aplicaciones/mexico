<?php
include_once('../../config/config.inc.php');
$token = Tools::getValue('token');
$token_bdd = Configuration::get('CARTABAND_TOKEN');

if(strlen($token_bdd) > 0 && isset($token_bdd) && isset($token) && $token_bdd == $token)
{
	require_once dirname(__FILE__).'/controllers/ReminderController.class.php';
	require_once dirname(__FILE__).'/controllers/TemplateController.class.php';
	require_once dirname(__FILE__).'/classes/Template.class.php';
	
	include_once('classes/Template.class.php');
	include_once('controllers/TemplateController.class.php');
	include_once('classes/Model.class.php');
	
	$id_lang = Tools::getValue('id_lang');
	$iso = Language::getIsoById($id_lang);
	$id_shop = Tools::getValue('id_shop');
	$mail = Tools::getValue('mail');
	
	$templates = TemplateController::getAllTemplates($id_shop, $id_lang);
	
	$x = 0;
	foreach($templates as $template){
		$content = Tools::file_get_contents(realpath('./') . '/mails/' . $iso . '/' . $template['id_template'] . '.html');
		$templateObj = new Template($template['id_template'], new Model(TemplateController::getModelByTemplate($template['id_template'])));
		$content = $templateObj->editTemplate($content);
		
		if(!$content) continue;
		
		$fp = fopen('mails/' . $iso . '/send.html', 'w+');
		fwrite($fp, $content);
		fclose($fp);
		$fp = fopen('mails/' . $iso . '/send.txt', 'w+');
		fwrite($fp, $content);
		fclose($fp);

		$title = Template::editTitleBeforeSending($template['template_name'], NULL, $id_lang);

		$mail = Mail::Send($id_lang, 'send', $title, array(), trim($mail), null, null, null, null, null, dirname(__FILE__) . '/mails/');
		if($mail)
			$x++;
	}
	echo $x . ' mails have been sent.';
}
else{
	echo 'hack ...';die;
}