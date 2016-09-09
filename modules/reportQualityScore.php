<?php 
	include_once(dirname(__FILE__)."/../config/config.inc.php");

	$query = "SELECT COUNT(*) numero FROM ps_order_quality";
	if ( $results = Db::getInstance()->ExecuteS( $query ) ) {
		echo "
			<label style='font-family: Open Sans, sans-serif; font-size: 15px;'># Ordenes sin respuesta a la encuesta:</label>
			<label style='font-family: Open Sans, sans-serif; font-size: 25px; color: #E7A937;'>".$results[0]['numero']."</label>
			<hr>";
	}


	$query2 = "SELECT qs.*, osl.name state_order
				FROM ps_quality_score qs
				INNER JOIN ps_orders o ON ( qs.id_order = o.id_order )
				INNER JOIN ps_order_state_lang osl ON ( o.current_state = osl.id_order_state )
				ORDER BY qs.date_qualification DESC";
	if ( $results2 = Db::getInstance()->ExecuteS( $query2 ) ) {
		echo "
			<table cellpadding='7' border='1' style='font-family: Open Sans, sans-serif; font-size: 15px; text-align: center; border-collapse: separate; border-spacing: 3px 10px;'>
				<thead style='background: #E7A937;'>
					<tr>
						<th>ORDEN</th>
						<th>CLIENTE</th>
						<th>ESTADO ORDEN</th>
						<th>CALIFICACION</th>
						<th>COMENTARIO</th>
						<th>FECHA ENCUESTA</th>
					</tr>
				</thead>
		";

		$color = 1;
		foreach ($results2 as $key => $value) {

			if ( $color == 1 ) {
				$color = $color + 1;
				$codcolor = "#f0f0f0";
			} else {
				$color = $color - 1;
				$codcolor = "#DEDAE7";
			}

			if ( $value['qualification'] >= 4 ) {
				$codcolorqual = "#ffde00";
			} else {
				$codcolorqual = "#EA4335";
			}

			if ( $value['comments'] == "" ) {
				$value['comments'] = "---";
			}

			echo "
				<tr style='background: ".$codcolor."'>
					<td>".$value['id_order']."</td>
					<td>".$value['id_customer']."</td>
					<td>".$value['state_order']."</td>
					<td style='font-size: 25px;'>".$value['qualification']." <label style='color: ".$codcolorqual."; font-size: 30px;'>&#9733;</label>  </td>
					<td>".$value['comments']."</td>
					<td>".$value['date_qualification']."</td>
				</tr>
			";
		}
		
		echo "</table>";
	}
?>