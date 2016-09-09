<?php 
include_once(dirname(__FILE__)."/../config/config.inc.php");

$query_date = " select now()";
if ($results = Db::getInstance()->ExecuteS( $query_date)) {
	echo " <pre> ";
	print_r($results[0]);
	}
	echo "<br> http date: ".date("Y-m-d H:i:s");

?>