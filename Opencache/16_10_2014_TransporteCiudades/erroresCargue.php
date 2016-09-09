<?php

require(dirname(__FILE__).'/../../config/config.inc.php');

$cargaTR = new PrecioTransporte();
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=reporte_error.csv");
header("Pragma: no-cache");
header("Expires: 0");
if( isset($_GET['t'] ) && $_GET['t'] == 'ciudades') {

	if ( $repcod = $cargaTR->reporteCiudadMalo() ) {
	    echo "id_ciudad_cargue;id_transportador_cargue;precio_cargue;id_ciudad_BD;id_transportador_BD;nombre_transportador_BD
	";
	    foreach ($cargaTR->resultados as $row) {
	        echo $row['cod_postal'].";".$row['id_transportador'].";".$row['precio'].";".$row['codigo_postal'].";".$row['id_carrier'].";".$row['namecarrier']."
	";
	    }
	} else {
	    echo "<font color='red'>".implode("<br>", $cargaTR->errores_cargue)."</font>";
	}

} else {
	if ( $repcod = $cargaTR->reporteCodigopMalo() ) {
	    echo "cod_postal_cargue;id_transportador_cargue;precio_cargue;codigo_postal_BD;id_transportador_BD;nombre_transportador_BD
	";
	    foreach ($cargaTR->resultados as $row) {
	        echo $row['cod_postal'].";".$row['id_transportador'].";".$row['precio'].";".$row['codigo_postal'].";".$row['id_carrier'].";".$row['namecarrier']."
	";
	    }
	} else {
	    echo "<font color='red'>".implode("<br>", $cargaTR->errores_cargue)."</font>";
	}
}

?>