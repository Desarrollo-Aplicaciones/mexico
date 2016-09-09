<?php
	require( "../config/config.inc.php" );

	$resultValidateData = Db::getInstance()->executeS("
			SELECT c.id_customer AS data_asoc, qs.id_order AS exist_survey
			FROM "._DB_PREFIX_."customer c
			INNER JOIN "._DB_PREFIX_."orders o ON ( c.id_customer = o.id_customer )
			LEFT JOIN "._DB_PREFIX_."quality_score qs ON ( o.id_order = qs.id_order AND c.id_customer = qs.id_customer )
			WHERE c.id_customer = ".$_POST['id_customer']."
			AND c.email = '".$_POST['mail_customer']."'
			AND o.id_order = ".$_POST['id_order']
	);

	if ( isset($resultValidateData) && $resultValidateData[0]['data_asoc'] != "" && $resultValidateData[0]['exist_survey'] == "" ) {

		$resultUpdateOrderQuality = Db::getInstance()->execute("
				DELETE FROM "._DB_PREFIX_."order_quality
				WHERE id_order = ".$_POST['id_order']
		);

		$resultqualify = Db::getInstance()->execute("
				INSERT INTO "._DB_PREFIX_."quality_score (
					id_order,
					id_customer,
					qualification,
					comments,
					date_qualification
				) VALUES (
					".$_POST['id_order'].",
					".$_POST['id_customer'].",
					".$_POST['qualification'].",
					'".$_POST['comments']."',
					NOW()
				)
		");
	}

	if ( Configuration::get('PS_LOCALE_COUNTRY') == "co" ) {
		$landing_gratefulness = "http://www.farmalisto.com.co/content/78-gracias-encuesta";
	} elseif ( Configuration::get('PS_LOCALE_COUNTRY') == "mx" ) {
		$landing_gratefulness = "http://www.farmalisto.com.mx/content/67-gracias-encuesta";
	}

	die( $landing_gratefulness );

?>