<?php
	require_once('../../config/config.inc.php');

	//se captura el id de orden
	$id_order = $_POST['id_order'];

	//consulta los datos de respuesta de payulatam
	$sqlPayuRespu = "SELECT id_cart, orderidpayu, transactionid, message
					FROM ps_pagos_payu
					WHERE id_cart = (SELECT id_cart FROM ps_orders WHERE id_order = ".$id_order.")";
	$resultsPayuRespu = Db::getInstance()->ExecuteS($sqlPayuRespu);

	//se valida que exista respuesta de payulatam, si no, se retorna sin respuesta
	if (empty($resultsPayuRespu)){
		$resultsPayuRespu['id_cart'] = "Sin Respuesta";
		$resultsPayuRespu['orderidpayu'] = "Sin Respuesta";
		$resultsPayuRespu['transactionid'] = "Sin Respuesta";
		$resultsPayuRespu['message'] = "Sin Respuesta";
	} else {
		$resultsPayuRespu = $resultsPayuRespu[0];
	}

	//consulta los datos de confirmacion de payulatam
	$sqlPayuConfi = "SELECT message, date
					FROM ps_log_payu_response
					WHERE id_cart = (SELECT id_cart FROM ps_orders WHERE id_order = ".$id_order.")";
	$resultsPayuConfi = Db::getInstance()->ExecuteS($sqlPayuConfi);

	//se valida que exista confirmacion de payulatam, si no, se retorna sin confirmacion
	if (empty($resultsPayuConfi)){
		$resultsPayuConfi['date'] = "Sin Confirmaci&oacute;n";
		$resultsPayuConfi['message'] = "Sin Confirmaci&oacute;n";
	} else {
		$resultsPayuConfi = $resultsPayuConfi[0];
	}

	//genera html con la informacion retornada de las consultas
	$html = "<div id='dialog-modal' style='font-size: 13px;'>
			    <p>
		            <fieldset>
		            	<legend>Informaci&oacute;n Transacci&oacute;n PayuLatam</legend>
		            	<b>N&uacute;mero de Pedido: ".$id_order."</b>
		            	<br>
		            	<br>
		            	<fieldset>
		            		<legend>Respuesta PayuLatam</legend>
		            			<b>N&uacute;mero de Carrito:</b> ".$resultsPayuRespu['id_cart']."<br>
		            			<b>N&uacute;mero de Orden PayuLatam:</b> ".$resultsPayuRespu['orderidpayu']."<br>
		            			<b>Codigo de Transacci&oacute;n:</b> ".$resultsPayuRespu['transactionid']."<br>
		            			<b>Mensaje de Respuesta:</b> ".$resultsPayuRespu['message']."
		            	</fieldset>
		            	<br>
		            	<fieldset>
		            		<legend>Confirmaci&oacute;n PayuLatam</legend>
		            			<b>Fecha de Confirmaci&oacute;n:</b> ".$resultsPayuConfi['date']."<br>
		            			<b>Mensaje de Confirmaci&oacute;n:</b> ".$resultsPayuConfi['message']."
		            	</fieldset>
		            </fieldset>
		        </p>
		    </div>";

	//retorna el html generado
	echo $html;
?>