<?php
	require( "../config/config.inc.php" );

	$deleteOrderQuality = Db::getInstance()->execute("
		DELETE FROM "._DB_PREFIX_."order_quality
		WHERE DATEDIFF( NOW() , date_change_state ) >= 5"
	);

	$listRememberScore = Db::getInstance()->executeS("
			SELECT oq.id_order, o.reference, c.firstname, c.lastname, c.email, c.id_customer
			FROM "._DB_PREFIX_."order_quality oq
			INNER JOIN "._DB_PREFIX_."orders o ON ( oq.id_order = o.id_order )
			INNER JOIN "._DB_PREFIX_."customer c ON ( o.id_customer = c.id_customer )
			WHERE oq.remember = 1
			AND ( NOW() - oq.date_change_state ) >= 86400");

	if ( isset($listRememberScore) && !empty($listRememberScore) ) {

		foreach ($listRememberScore as $key => $value) {
			$template_vars['{firstname}'] = $value['firstname'];
			$template_vars['{lastname}'] = $value['lastname'];
			$template_vars['{order_name}'] = $value['reference'];
			$template_vars['id_customer'] = $value['id_customer'];
			$template_vars['id_order'] = $value['id_order'];

			Mail::Send(
				1,
				'delivered',
				'Recordatorio: Califica nuestro servicio',
				$template_vars,
				$value['email'],
				( $value['firstname']." ".$value['lastname'] ),
				Configuration::get('PS_SHOP_EMAIL'),
				Configuration::get('PS_SHOP_NAME')
			);

			$resultUpdateOrderQuality = Db::getInstance()->execute("
				DELETE FROM "._DB_PREFIX_."order_quality
				WHERE id_order = ".(int)$value['id_order']
			);
		}
		
		echo "\r\n Recordatorio: ".date("Y-m-d H:i:s");
	} else {
		echo "\r\n NO Recordatorio: ".date("Y-m-d H:i:s");
	}

?>