<?php 
include_once(dirname(__FILE__)."/../config/config.inc.php");


$query_updt2 = 'UPDATE ps_supply_order_icr soi 
INNER JOIN ps_supply_order_detail sod ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
INNER JOIN ps_icr i ON ( soi.id_icr = i.id_icr )
LEFT JOIN ps_order_picking opp ON ( opp.id_order_icr = i.id_icr )
SET i.id_estado_icr = 2
WHERE ( i.id_estado_icr = 1 OR i.id_estado_icr = 3 ) AND opp.id_order_picking IS NULL';



if ($results_upd_gen = Db::getInstance()->Execute( $query_updt2 )) {
	echo "<br> - Actualización de ICR Estado 2";
}


$query_updt3 = ' UPDATE ps_supply_order_icr soi 
INNER JOIN ps_supply_order_detail sod ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
INNER JOIN ps_icr i ON ( soi.id_icr = i.id_icr )
LEFT JOIN ps_order_picking opp ON ( opp.id_order_icr = i.id_icr )
SET i.id_estado_icr = 3
WHERE ( i.id_estado_icr = 1 OR i.id_estado_icr = 2 ) AND opp.id_order_picking IS NOT NULL';


if ($results_upd_gen = Db::getInstance()->Execute( $query_updt3 )) {
	echo "<br> - Actualización de ICR Estado 3 ";
}


$query_select = "SELECT COUNT(1) FROM ps_stock_available_mv";

	if ($results = Db::getInstance()->ExecuteS( $query_select)) {
		echo " <pre> BEFORE: ";
		print_r($results[0]);
	}

 $desde_os_validar = 5600;

	if ( isset( $_GET['desde_os_validar'] ) && $_GET['desde_os_validar'] != "") {
            $desde_os_validar = $_GET['desde_os_validar'];    
    } 

    $query_updt = 'SELECT sod.id_supply_order, sod.id_supply_order_detail, sod.id_product, sod.reference, sod.quantity_expected, 
sod.quantity_received, COUNT(soi.id_icr) AS cant_real_rev 
, CONCAT( "UPDATE ps_supply_order_detail sod SET sod.quantity_received =", COUNT(soi.id_icr) , " WHERE sod.id_supply_order_detail  = ", sod.id_supply_order_detail, ";" ) AS querys
FROM ps_supply_order_detail sod
LEFT JOIN ps_supply_order_icr soi on ( sod.id_supply_order_detail = soi.id_supply_order_detail )
WHERE sod.id_supply_order >=  '.$desde_os_validar.'
GROUP BY sod.id_supply_order_detail
HAVING sod.quantity_received <> COUNT(soi.id_icr)';

        if ($results_upd = Db::getInstance()->ExecuteS( $query_updt)) {
            
		foreach ($results_upd as $row) {
                               // echo "<br>".$row['querys'];
                if ($results_upd_gen = Db::getInstance()->Execute( $row['querys'] )) {
                    echo "<br> OS ".$row['id_supply_order']. " - - actulizada cant: ". $row['cant_real_rev'];
                    
                }
                    
        	}
	} else {
	 echo "<br> No cantidades erroneas";
	}
       
 
$query_truncate = " TRUNCATE TABLE ps_stock_available_mv";
if ($results = Db::getInstance()->Execute( $query_truncate)) {

	echo " <br> tabla borrada";
	
	$query_insert = " INSERT INTO ps_stock_available_mv
    SELECT  `sa`.`id_stock_available` AS `id_stock_available`,
				`ps`.`id_product` AS `id_product`, 
				IF ( isnull( `sod`.`id_product_attribute` ),  0,  `sod`.`id_product_attribute` ) AS `id_product_attribute`,
				`ps`.`id_shop` AS `id_shop`,
				`sa`.`id_shop_group` AS `id_shop_group`,
				IF (  (   `ps`.`advanced_stock_management` = 1  ),  count(`i`.`id_icr`),  `sa`.`quantity`  ) AS `quantity`,
 				1 AS `depends_on_stock`,
 				2 AS `out_of_stock`
		FROM  `ps_product_shop` `ps`
		LEFT JOIN `ps_supply_order_detail` `sod` ON ( `sod`.`id_product` = `ps`.`id_product` )
		LEFT JOIN `ps_supply_order_icr` `soi` ON ( `sod`.`id_supply_order_detail` = `soi`.`id_supply_order_detail` )
		LEFT JOIN `ps_icr` `i` ON ( `soi`.`id_icr` = `i`.`id_icr` AND  `i`.`id_estado_icr` = 2 )
		LEFT JOIN `ps_stock_available` `sa` ON ( `ps`.`id_product` = `sa`.`id_product` )
		 GROUP BY ps.id_product";

	if ($results = Db::getInstance()->Execute( $query_insert)) {
		echo "<br> Stock actualizado";

		$query_select = "SELECT COUNT(1) FROM ps_stock_available_mv";

	if ($results = Db::getInstance()->ExecuteS( $query_select)) {
		echo " <pre> AFTER";
		print_r($results[0]);
	}

	}
}


?>


    

