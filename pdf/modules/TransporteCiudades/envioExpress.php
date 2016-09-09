<?php
require(dirname(__FILE__) . '/../../config/config.inc.php');
if($_POST['express'] == "update")
{
	$status = 0;
	$desc = "deshabilitado";
	if (isset($_POST['status']) && $_POST['status'])
	{
		$status = 1;
		$desc = "habilitado";
	}
	$query="update ps_configuration
            SET Value = ".$status." WHERE Name = 'ENVIO_EXPRESS';";
	if(Db::getInstance()->execute($query))
	{
		exit();
	}
	else{
		echo "Error en ".$query;
	}
	exit();
}
if ($_POST['express'] == "checkCiudad") {
	//echo 'valor='.$_POST['value'];
	$query="SELECT express_abajo, express_arriba 
			FROM ps_carrier_city
			WHERE id_city_des=".$_POST['value'];
	if($row = Db::getInstance()->getRow($query))
	{
		echo $row['express_abajo'].'::'.$row['express_arriba'];
		exit();
	}
}
if ($_POST['express'] == "updateCiudad") {
	//echo 'valor='.$_POST['value'];
	if($_POST['xpsabajo']){
		$abajo = $_POST['xpsabajo'];
	}
	else{
		$abajo = "NULL";
	}
	if($_POST['xpsarriba']){
		$arriba = $_POST['xpsarriba'];
	}
	else{
		$arriba = "NULL";
	}
	$query="UPDATE ps_carrier_city
			SET express_abajo=$abajo,
				express_arriba=$arriba
			WHERE id_city_des=".$_POST['ciudad'];
	if (!Db::getInstance()->execute($query))
		echo "Error";
}
?>