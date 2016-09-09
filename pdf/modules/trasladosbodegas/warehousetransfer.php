<?php
require_once(dirname(__FILE__).'../../../config/config.inc.php');
require_once(dirname(__FILE__).'../../../init.php');
$respuesta = array();
if (Tools::getValue('validar') && Tools::getValue('validar') == 1 ){
	if(Tools::getValue('origin_warehouse') && Tools::getValue('icr')){
		$origen = Tools::getValue('origin_warehouse');
		$icr = Tools::getValue('icr');
		$query = new DbQuery();
		$query->select('w.name,i.cod_icr,soi.id_warehouse,soi.id_icr');
		$query->from('supply_order_icr', 'soi');
		$query->innerJoin('icr', 'i', ' i.id_icr = soi.id_icr ');
		$query->innerJoin('warehouse', 'w', ' w.id_warehouse = soi.id_warehouse ');
		$query->where('soi.id_warehouse = '.$origen);
		$query->where('i.cod_icr = "'.$icr.'"');
		$items = Db::getInstance()->executeS($query);
		if ($items){
			$respuesta = $items[0];
		}
		else{
			$respuesta['error'] = "El ICR no existe en la bodega seleccionada";
	}
	}
	else{
		$respuesta['error'] = "Datos Incorrectos, por favor verificar la información";
	}
	echo Tools::jsonEncode( $respuesta);
}
exit;
?>