<?php
require(dirname(__FILE__).'/config/config.inc.php');

$id_ciudad = $_POST['city'];		//4200

$selected = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : '';

$str_cities = '<option value="">- Colonia-</option>';
$cities = City::getColoniaByIdCity($id_ciudad);
if ( !is_array($cities) ) {
	$cities = array();
}
foreach ($cities as $row){
	//$str_cities .= '<option value="'. $row['id_codigo_postal'] .'">'. $row['nombrecolonia'] . '</option>';
	if ( $row['id_codigo_postal'] == $selected ) { 
		$str_cities .= '<option value="'. $row['id_codigo_postal'] .'" selected="selected">'. $row['nombrecolonia'] . '</option>';
	} else {
		$str_cities .= '<option value="'. $row['id_codigo_postal'] .'" >'. $row['nombrecolonia'] . '</option>';
	}
}

$array_result = array('results' => $str_cities);
echo json_encode($array_result);

?>