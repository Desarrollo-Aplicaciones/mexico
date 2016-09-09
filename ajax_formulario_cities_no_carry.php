<?php
require(dirname(__FILE__).'/config/config.inc.php');

$id_state = $_POST['id_state'];		//4200
$str_cities = '<option value="">- Ciudad -</option>';
$cities = City::getCitiesByStateAvailableNoCarrier($id_state);
foreach ($cities as $row){
	//$str_cities .= '<option value="'. $row['id_city'] .'">'. $row['city_name'] . '</option>';
	$str_cities .= '<option value="'. $row['id_city'] .'">'. $row['city_name'] . '</option>';
}

$array_result = array('results' => $str_cities);
echo json_encode($array_result);

?>