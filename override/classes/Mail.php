<?php

class Mail extends MailCore
{
	public static function Send($id_lang, $template, $subject, $template_vars, $to,
		$to_name = null, $from = null, $from_name = null, $file_attachment = null, $mode_smtp = null,
		$template_path = _PS_MAIL_DIR_, $die = false, $id_shop = null, $bcc = null, $validamsjcontact = false, $msjcontact = '')
	{
		$addressee = $to;

		$configuration = Configuration::getMultiple(array(
			'PS_SHOP_EMAIL',
			'PS_MAIL_METHOD',
			'PS_MAIL_SERVER',
			'PS_MAIL_USER',
			'PS_MAIL_PASSWD',
			'PS_SHOP_NAME',
			'PS_MAIL_SMTP_ENCRYPTION',
			'PS_MAIL_SMTP_PORT',
			'PS_MAIL_TYPE'
		), null, null, $id_shop);
		
		// Returns immediatly if emails are deactivated
		if ($configuration['PS_MAIL_METHOD'] == 3)
			return true;
		
		$theme_path = _PS_THEME_DIR_;

		// Get the path of theme by id_shop if exist
		if (is_numeric($id_shop) && $id_shop)
		{
			$shop = new Shop((int)$id_shop);
			$theme_name = $shop->getTheme();

			if (_THEME_NAME_ != $theme_name)
				$theme_path = _PS_ROOT_DIR_.'/themes/'.$theme_name.'/';
		}

		if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION']))
			$configuration['PS_MAIL_SMTP_ENCRYPTION'] = 'off';
		if (!isset($configuration['PS_MAIL_SMTP_PORT']))
			$configuration['PS_MAIL_SMTP_PORT'] = 'default';

		// Sending an e-mail can be of vital importance for the merchant, when his password is lost for example, so we must not die but do our best to send the e-mail
		if (!isset($from) || !Validate::isEmail($from))
			$from = $configuration['PS_SHOP_EMAIL'];
		if (!Validate::isEmail($from))
			$from = null;

		// $from_name is not that important, no need to die if it is not valid
		if (!isset($from_name) || !Validate::isMailName($from_name))
			$from_name = $configuration['PS_SHOP_NAME'];
		if (!Validate::isMailName($from_name))
			$from_name = null;


		// It would be difficult to send an e-mail if the e-mail is not valid, so this time we can die if there is a problem
		if (!is_array($to) && !Validate::isEmail($to))
		{
			Tools::dieOrLog(Tools::displayError('Error: parameter "to" is corrupted'), $die);
			return false;
		}
		
		$email = $to;
		$without_email = explode(",",Configuration::get('PS_WITHOUT_EMAIL'));
		if(preg_match_all ("/^(.+)@([a-zA-Z0-9_]*)/", $email, $e1)){
			if (in_array($e1[2][0], $without_email)){
				//error_log("\n\n\n\n\nEste es el to 2: ".print_r($to,true)."\n\n\n\n");
				Tools::dieOrLog(Tools::displayError('Error: parameter  is corrupted'), $die);
				return false;
			}
		}
		
//		if (self::validateSinEmail($to))
//		{
//			Tools::dieOrLog(Tools::displayError('Error: parameter  is corrupted'), $die);
//			return false;
//		}

		if (!is_array($template_vars))
			$template_vars = array();

		// Do not crash for this error, that may be a complicated customer name
		if (is_string($to_name) && !empty($to_name) && !Validate::isMailName($to_name))
			$to_name = null;

		if (!Validate::isTplName($template))
		{
			Tools::dieOrLog(Tools::displayError('Error: invalid e-mail template'), $die);
			return false;
		}

		if (!Validate::isMailSubject($subject))
		{
			Tools::dieOrLog(Tools::displayError('Error: invalid e-mail subject'), $die);
			return false;
		}

		/* Construct multiple recipients list if needed */
		$to_list = new Swift_RecipientList();
		if (is_array($to) && isset($to))
		{
			foreach ($to as $key => $addr)
			{
				$to_name = null;
				$addr = trim($addr);
				if (!Validate::isEmail($addr))
				{
					Tools::dieOrLog(Tools::displayError('Error: invalid e-mail address'), $die);
					return false;
				}
				if (is_array($to_name))
				{
					if ($to_name && is_array($to_name) && Validate::isGenericName($to_name[$key]))
						$to_name = $to_name[$key];
				}
				if ($to_name == null)
					$to_name = $addr;
				/* Encode accentuated chars */
				if (function_exists('mb_encode_mimeheader'))
					$to_list->addTo($addr, mb_encode_mimeheader($to_name, 'utf-8'));
				else
					$to_list->addTo($addr, self::mimeEncode($to_name));
			}
			$to_plugin = $to[0];
		} else {
			/* Simple recipient, one address */
			$to_plugin = $to;
			if ($to_name == null)
				$to_name = $to;
			if (function_exists('mb_encode_mimeheader'))
				$to_list->addTo($to, mb_encode_mimeheader($to_name, 'utf-8'));
			else
				$to_list->addTo($to, self::mimeEncode($to_name));
		}
		if(isset($bcc)) {
			$to_list->addBcc($bcc);
		}
		$to = $to_list;
		try {
			/* Connect with the appropriate configuration */
			if ($configuration['PS_MAIL_METHOD'] == 2)
			{
				if (empty($configuration['PS_MAIL_SERVER']) || empty($configuration['PS_MAIL_SMTP_PORT']))
				{
					Tools::dieOrLog(Tools::displayError('Error: invalid SMTP server or SMTP port'), $die);
					return false;
				}
				$connection = new Swift_Connection_SMTP($configuration['PS_MAIL_SERVER'], $configuration['PS_MAIL_SMTP_PORT'],
					($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl') ? Swift_Connection_SMTP::ENC_SSL :
					(($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls') ? Swift_Connection_SMTP::ENC_TLS : Swift_Connection_SMTP::ENC_OFF));
				$connection->setTimeout(4);
				if (!$connection)
					return false;
				if (!empty($configuration['PS_MAIL_USER']))
					$connection->setUsername($configuration['PS_MAIL_USER']);
				if (!empty($configuration['PS_MAIL_PASSWD']))
					$connection->setPassword($configuration['PS_MAIL_PASSWD']);
			}
			else
				$connection = new Swift_Connection_NativeMail();

			if (!$connection)
				return false;
			$swift = new Swift($connection, Configuration::get('PS_MAIL_DOMAIN', null, null, $id_shop));
			/* Get templates content */
			$iso = Language::getIsoById((int)$id_lang);
			if (!$iso)
			{
				Tools::dieOrLog(Tools::displayError('Error - No ISO code for email'), $die);
				return false;
			}

			$template = $iso.'/'.$template;

			$module_name = false;
			$override_mail = false;

			// get templatePath
			if (preg_match('#'.__PS_BASE_URI__.'modules/#', str_replace(DIRECTORY_SEPARATOR, '/', $template_path)) && preg_match('#modules/([a-z0-9_-]+)/#ui', str_replace(DIRECTORY_SEPARATOR, '/',$template_path), $res))
				$module_name = $res[1];

			if ($module_name !== false && (file_exists($theme_path.'modules/'.$module_name.'/mails/'.$template.'.txt') ||
				file_exists($theme_path.'modules/'.$module_name.'/mails/'.$template.'.html')))
				$template_path = $theme_path.'modules/'.$module_name.'/mails/';
			elseif (file_exists($theme_path.'mails/'.$template.'.txt') || file_exists($theme_path.'mails/'.$template.'.html'))
			{
				$template_path = $theme_path.'mails/';
				$override_mail  = true;
			}

			if (!file_exists($template_path.$template.'.txt') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT))
			{
				Tools::dieOrLog(Tools::displayError('Error - The following e-mail template is missing:').' '.$template_path.$template.'.txt', $die);
				return false;
			}
			else if (!file_exists($template_path.$template.'.html') && ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML))
			{
				Tools::dieOrLog(Tools::displayError('Error - The following e-mail template is missing:').' '.$template_path.$template.'.html', $die);
				return false;
			}
			$template_html = file_get_contents($template_path.$template.'.html');
			$template_txt = strip_tags(html_entity_decode(file_get_contents($template_path.$template.'.txt'), null, 'utf-8'));

			if ($override_mail && file_exists($template_path.$iso.'/lang.php'))
					include_once($template_path.$iso.'/lang.php');
			else if ($module_name && file_exists($theme_path.'mails/'.$iso.'/lang.php'))
				include_once($theme_path.'mails/'.$iso.'/lang.php');
			else
				include_once(_PS_MAIL_DIR_.$iso.'/lang.php');

			/* Create mail and attach differents parts */
			if ($validamsjcontact){
				$message = new Swift_Message($subject. ' [Asunto: '.$msjcontact.']');
			} else {
				$message = new Swift_Message('['.Configuration::get('PS_SHOP_NAME', null, null, $id_shop).'] '.$subject);
			}

			$message->setCharset('utf-8');

			/* Set Message-ID - getmypid() is blocked on some hosting */
			$message->setId(Mail::generateId());

			$message->headers->setEncoding('Q');

			if (Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $id_shop)))
				$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $id_shop);
			else
			{
				if (file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop)))
					$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop);
				else
					$template_vars['{shop_logo}'] = '';
			}
			ShopUrl::cacheMainDomainForShop((int)$id_shop);
			/* don't attach the logo as */
			if (isset($logo))
				//$template_vars['{shop_logo}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File($logo), null, ImageManager::getMimeTypeByExtension($logo)));

			if ((Context::getContext()->link instanceof Link) === false)
				Context::getContext()->link = new Link();

			$template_vars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
			$template_vars['{shop_url}'] = Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id);
			$template_vars['{my_account_url}'] = Context::getContext()->link->getPageLink('my-account', true, Context::getContext()->language->id);
			$template_vars['{guest_tracking_url}'] = Context::getContext()->link->getPageLink('guest-tracking', true, Context::getContext()->language->id);
			$template_vars['{history_url}'] = Context::getContext()->link->getPageLink('history', true, Context::getContext()->language->id);
			$template_vars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $id_shop));

			/* start new template mail */
			if ( $template == 'es/order_conf' ) {
				//$template_vars['{logo_recuerda}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( _PS_IMG_DIR_."/ordermail/Icono-mail.jpg" ), null));
			}

			if ( $template == 'es/delivered' || $template == 'es/canceled' ) {

				$template_vars['{logo_header}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( _PS_IMG_DIR_."/ordermail/logoheadermail.png" ), null));
				
				$template_vars['{star}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( _PS_IMG_DIR_."/ordermail/estrella.png" ), null));
				
				$template_vars['{logo_playstore}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( _PS_IMG_DIR_."/ordermail/play-store.png" ), null));
				
				$template_vars['{logo_appstore}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( _PS_IMG_DIR_."/ordermail/app-store.png" ), null));
				
				$template_vars['{logo_facebook}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( _PS_IMG_DIR_."/ordermail/facebook.png" ), null));
				
				$template_vars['{logo_twitter}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( _PS_IMG_DIR_."/ordermail/twitter.png" ), null));

				$resultBanner = Db::getInstance()->executeS("SELECT link, imagen
															FROM "._DB_PREFIX_."publicidad
															WHERE pagina = 'maildelivered'
															AND ubicacion = 'inferior'
															AND activo = 'si'
															ORDER BY id_publicidad DESC"
														);
				$template_vars['{url_banner_publicidad}'] = $resultBanner[0]['link'];
				$template_vars['{img_banner_publicidad}'] = $message->attach(new Swift_Message_EmbeddedFile(new Swift_File( Configuration::get('PATH_UP_LOAD')."cspublicidadfl/uploads/".$resultBanner[0]['imagen'] ), null));

				$template_vars['{ext}'] = Configuration::get('PS_LOCALE_COUNTRY');

				$id_customer = Context::getContext()->cart->id_customer;
				$id_order = Order::getOrderByCartId(Context::getContext()->cart->id);
				if ( $id_customer == "" ) { $id_customer = $template_vars['id_customer']; }
				if ( $id_order == "" ) { $id_order = $template_vars['id_order']; }

				for ( $star = 1; $star <= 5; $star++ ) {
					$tokendata = base64_encode( $star.",".$id_customer.",".$id_order.",".$addressee );
					$template_vars['{url_star_'.$star.'}'] = "quality_score/quality_score.php?".$tokendata;
				}

				if ( Configuration::get('PS_LOCALE_COUNTRY') == "co" ) {
					// category 1
					$template_vars['{name_category_1}'] = "Salud sexual y reproductiva";
					$template_vars['{url_category_1}'] = "http://www.farmalisto.com.co/636-salud-sexual-y-reproductiva?utm_source=Newsletter_Col&utm_medium=Newsletter_Col_Categoria_01&utm_term=Lp_636&utm_content=ID_00440&utm_campaign=09102015_CmpStatusEntregado";
					// category 2
					$template_vars['{name_category_2}'] = "Mamá y bebé";
					$template_vars['{url_category_2}'] = "http://www.farmalisto.com.co/555-mama-y-bebe?utm_source=Newsletter_Col&utm_medium=Newsletter_Col_Categoria_02&utm_term=Lp_555&utm_content=ID_00441&utm_campaign=09102015_CmpStatusEntregado";
					// category 3
					$template_vars['{name_category_3}'] = "Cuidado y aseo personal";
					$template_vars['{url_category_3}'] = "http://www.farmalisto.com.co/447-cuidado-y-aseo-personal?utm_source=Newsletter_Col&utm_medium=Newsletter_Col_Categoria_03&utm_term=Lp_447&utm_content=ID_00442&utm_campaign=09102015_CmpStatusEntregado";
					// apps
					$template_vars['{play_store}'] = "http://play.google.com/store/apps/details?id=com.kubo.farmalisto&hl=es";
					$template_vars['{app_store}'] = "http://itunes.apple.com/co/app/farmalisto/id899599402?mt=8";
					// networks
					$template_vars['{facebook}'] = "http://www.facebook.com/farmalistocolombia?ref=ts&fref=ts";
					$template_vars['{twitter}'] = "http://twitter.com/farmalistocol";
					// contact
					$template_vars['{contact}'] = Configuration::get('blockcontact_email')." <br>
													Bogotá: (+571) 4926363 - Medellín: (+574) 2040695 <br>
													Cali: (+572) 8912562 - Barranquilla: (+575) 753851691 <br>
													Domicilios a Nivel Nacional: 0180009133830";
					
				} elseif ( Configuration::get('PS_LOCALE_COUNTRY') == "mx" ) {
					// category 1
					$template_vars['{name_category_1}'] = "Salud sexual y reproductiva";
					$template_vars['{url_category_1}'] = "http://www.farmalisto.com.mx/636-salud-sexual-y-reproductiva?utm_source=Newsletter_Mx&utm_medium=Newsletter_Mx_Categoria_01&utm_term=Lp_636&utm_content=ID_00451&utm_campaign=22102015_CmpCalificacion";
					// category 2
					$template_vars['{name_category_2}'] = "Mamá y bebé";
					$template_vars['{url_category_2}'] = "http://www.farmalisto.com.mx/555-mama-y-bebe?utm_source=Newsletter_Mx&utm_medium=Newsletter_Mx_Categoria_02&utm_term=Lp_555&utm_content=ID_00452&utm_campaign=22102015_CmpCalificacion";
					// category 3
					$template_vars['{name_category_3}'] = "Cuidado y aseo personal";
					$template_vars['{url_category_3}'] = "http://www.farmalisto.com.mx/447-cuidado-y-aseo-personal?utm_source=Newsletter_Mx&utm_medium=Newsletter_Mx_Categoria_03&utm_term=Lp_447&utm_content=ID_00453&utm_campaign=22102015_CmpCalificacion";
					// apps
					$template_vars['{play_store}'] = "http://play.google.com/store/apps/details?id=com.kubo.farmalistomx&hl=fr_CA";
					$template_vars['{app_store}'] = "http://itunes.apple.com/mx/app/farmalisto-mexico/id1029945733?mt=8";
					// networks
					$template_vars['{facebook}'] = "http://www.facebook.com/farmalistomexico";
					$template_vars['{twitter}'] = "http://twitter.com/farmalistomex";
					// contact
					$template_vars['{contact}'] = Configuration::get('blockcontact_email')." <br>
													Línea de atención y ventas nacional sin costo: (55) 6732.1100";
				}
			}
			/* finish template mail */

			$swift->attachPlugin(new Swift_Plugin_Decorator(array($to_plugin => $template_vars)), 'decorator');
			if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_TEXT)
				$message->attach(new Swift_Message_Part($template_txt, 'text/plain', '8bit', 'utf-8'));
			if ($configuration['PS_MAIL_TYPE'] == Mail::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == Mail::TYPE_HTML)
				$message->attach(new Swift_Message_Part($template_html, 'text/html', '8bit', 'utf-8'));
			if ($file_attachment && !empty($file_attachment))
			{
				// Multiple attachments?
				if (!is_array(current($file_attachment)))
					$file_attachment = array($file_attachment);

				foreach ($file_attachment as $attachment)
					if ( isset($attachment['content']) && isset($attachment['name']) && isset($attachment['mime']) ) {
						$message->attach(new Swift_Message_Attachment($attachment['content'], $attachment['name'], $attachment['mime']));
					}
			}
			/* Send mail */
			$send = $swift->send($message, $to, new Swift_Address($from, $from_name));
			$swift->disconnect();

			ShopUrl::resetMainDomainCache();			

			return $send;
		}
		catch (Swift_Exception $e) {
			return false;
		}
	}
	
//	public function validateSinEmail($email){
//		$without_email = explode(",",Configuration::get('PS_WITHOUT_EMAIL'));
//		if(preg_match_all ("/^(.+)@([a-zA-Z0-9_]*)/", $email, $e1)){
//			if (!in_array($e1[2][0], $without_email)){
//				return false;
//			}
//		}
//		return true;
//	}
}
