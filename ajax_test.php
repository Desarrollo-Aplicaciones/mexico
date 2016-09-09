<?php
require(dirname(__FILE__).'/config/config.inc.php');

$postcode = $_REQUEST['postcode']; 	

		if ($result = City::getIdCodPosIdCityIdStateByPostcode($postcode) ) {
			//print_r($result);
			$valores = $result[0];
			if ( $valores['id_codigo_postal'] != '' && $valores['id_city'] != '' && $valores['id_state'] != '') {
				echo $valores['id_codigo_postal'].";".$valores['id_city'].";".$valores['id_state'];
			} else {
				echo "0";
			}
			
		} else {
			echo "0";
		}


?>