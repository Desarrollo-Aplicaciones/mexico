<?php

include_once(dirname(__FILE__)."/../../config/config.inc.php");

/*$arr_ord[1636]='2015-03-30 14:22:10';
$arr_ord[1699]='2015-04-02 10:53:15';
$arr_ord[1708]='2015-04-02 11:04:25';
$arr_ord[1857]='2015-04-13 13:29:11';
$arr_ord[1903]='2015-04-16 13:57:48';
$arr_ord[1980]='2015-04-23 10:53:17';
$arr_ord[2011]='2015-04-24 10:39:21';
$arr_ord[2253]='2015-05-15 14:10:34';
$arr_ord[2255]='2015-05-15 14:24:19';
$arr_ord[2398]='2015-05-29 13:23:54';*/
$arr_ord[2446]='2015-06-02 13:24:47';


foreach ($arr_ord as $key => $value) {
	$factura = new Facturaxion(0);
	$Cancelacion = $factura->cancelacion( $key, $value );
	echo "<br> Estado de orden ".$key." Cancelada: ";
	print_r( $Cancelacion );
}



?>
