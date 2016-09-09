<?php 
include_once(dirname(__FILE__)."/../config/config.inc.php");


$query_select = "SELECT COUNT(1) FROM ps_stock_available_mv";

	if ($results = Db::getInstance()->ExecuteS( $query_select)) {
		echo " <pre> BEFORE: ";
		print_r($results[0]);
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


    

