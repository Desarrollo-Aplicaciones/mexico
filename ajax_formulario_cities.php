<?php
require(dirname(__FILE__).'/config/config.inc.php');

$id_state = $_POST['id_state'];		//4200

$selected = isset($_POST['selected']) ? $_POST['selected'] : '';

$str_cities = '<option value="">- Ciudad -</option>';
$cities = City::getCitiesByStateAvailableCP($id_state);
foreach ($cities as $row){
	//$str_cities .= '<option value="'. $row['id_city'] .'">'. $row['city_name'] . '</option>';
	if ( $row['id_city'] == $selected ) { 
		$str_cities .= '<option value="'. $row['id_city'] .'" selected="selected">'. $row['city_name'] . '</option>';
	} else {
		$str_cities .= '<option value="'. $row['id_city'] .'" >'. $row['city_name'] . '</option>';
	}
}

$array_result = array('results' => $str_cities);
echo json_encode($array_result);

?>