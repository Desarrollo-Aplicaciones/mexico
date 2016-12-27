<?php

include_once(dirname(__FILE__)."/../../config/config.inc.php");

if (isset($_GET['opc_sel']) ) {

    switch ($_GET['opc_sel']) {
        case 'consped':
            $date_format = 'Y-m-d';
            $input1 = $_GET['f_ini'];
            $input2 = $_GET['f_fin'];
            $input1 = trim($input1);
            $input2 = trim($input2);
            $time1 = strtotime($input1);
            $time2 = strtotime($input2);

            if ( date($date_format, $time1) == $input1 && date($date_format, $time2) == $input2 && isset($_GET['f_ini']) && $_GET['f_ini'] != '' 
                && isset($_GET['f_fin']) && $_GET['f_fin'] != '' ) {

                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Descarga_pedidos.csv"));
                header("Content-Transfer-Encoding: binary"); 

                $sql = 'SELECT o.id_order, o.invoice_number, CONCAT(c.firstname, " ", c.lastname) AS cliente,
                    a.address1, a.address2, o.date_add, od.product_reference, od.product_name, CONCAT( "$ ", REPLACE( (od.product_price ),",",",")) AS precio,
                     CONCAT( "$ ", REPLACE( (od.unit_price_tax_incl ),",",",")) AS precio_tax,
                    od.product_quantity, i.cod_icr, tro.nombre, osl.`name` FROM ps_order_detail od 
                    INNER JOIN ps_orders o ON ( o.id_order = od.id_order )
                    INNER JOIN ps_customer c ON ( o.id_customer = c.id_customer )
                    INNER JOIN ps_address a ON ( o.id_address_delivery = a.id_address )
                    LEFT JOIN ps_order_picking op ON ( op.id_order_detail = od.id_order_detail )
                    LEFT JOIN ps_icr i ON ( op.id_order_icr = i.id_icr )
                    LEFT JOIN ps_orders_transporte ot ON ( ot.id_order = o.id_order )
                    LEFT JOIN ps_transporte_opciones tro ON ( tro.id_transporte_opcion = ot.id_transporte_opcion) 
                    INNER JOIN ps_order_state_lang osl ON ( osl.id_order_state = o.current_state )
                    WHERE o.date_add BETWEEN "'.$input1.' 00:00:00" AND "'.$input2.' 23:59:59"
                    ORDER BY o.id_order, od.product_reference ASC';

                //if ($results = Db::getInstance_slv()->ExecuteS($sql)) {
                 if ($results = Db::getInstance()->ExecuteS($sql)) {
                    //var_dump($results);

                    $output = fopen('php://output', 'w');
                    fputcsv( $output, array( "NUM PEDIDO", "NUM FACTURA", "CLIENTE", "DIRECCION 1", "DIRECCION 2", "FECHA", "REFERENCIA", "DESCRIPCION", "PRECIO", "PRECIO CON IMPUEST", "CANTIDAD", "ICR", "TRANSPORTADORA", "ESTADO ORDEN" ) ) ;

                            foreach ($results as $dat_print) {

                                fputcsv( $output, array( $dat_print['id_order'], $dat_print['invoice_number'], utf8_decode($dat_print['cliente']), utf8_decode($dat_print['address1']), utf8_decode($dat_print['address2']), $dat_print['date_add'], $dat_print['product_reference'], utf8_decode($dat_print['product_name']), $dat_print['precio'], $dat_print['precio_tax'], $dat_print['product_quantity'], $dat_print['cod_icr'], utf8_decode($dat_print['nombre']), utf8_decode($dat_print['name']) ) );

                            }
      
                } else {
                    header("Content-type: application/force-download");
                    header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.csv"));
                    header("Content-Transfer-Encoding: binary"); 
                }
            } else {
                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Formato_de_fecha_erroneo_o_fecha_incorrecta.csv"));
                header("Content-Transfer-Encoding: binary"); 
            }
        break;


        case 'icrsumi':
            $date_format = 'Y-m-d';
            $input1 = $_GET['f_ini'];
            $input2 = $_GET['f_fin'];
            $input1 = trim($input1);
            $input2 = trim($input2);
            $time1 = strtotime($input1);
            $time2 = strtotime($input2);

            if (  isset($_GET['orden']) && $_GET['orden'] != '' && date($date_format, $time1) == $input1 && date($date_format, $time2) == $input2 && isset($_GET['f_ini']) && $_GET['f_ini'] != '' && isset($_GET['f_fin']) && $_GET['f_fin'] != '' ) {

                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Descarga_icr_asignados.csv"));
                header("Content-Transfer-Encoding: binary"); 
                $orden_query = 'sod.reference';

                switch ($_GET['orden']) {
                    case 'referencia':
                            $orden_query = 'sod.reference';
                        break;
                    case 'nombre':
                            $orden_query = 'sod.`name`';
                        break;

                    case 'icr':
                            $orden_query = 'i.cod_icr';
                        break;

                    case 'bodega':
                            $orden_query = 'w.`name`';
                        break;
                    default : 
                            $orden_query = "so.id_supply_order, sod.id_supply_order_detail";
                        break;

                }



                $sql = 'SELECT so.id_supply_order, sod.reference, sod.`name`, i.cod_icr, REPLACE(sod.unit_price_te ,",",",") unit_price_te, REPLACE(sod.tax_rate ,",",",") tax_rate, w.`name` AS bodega, soi.lote, soi.fecha_vencimiento,
                        soi.fecha, p.upc
                        FROM ps_supply_order_icr soi
                        INNER JOIN ps_supply_order_detail sod ON ( soi.id_supply_order_detail = sod.id_supply_order_detail)
                        INNER JOIN ps_supply_order so ON ( so.id_supply_order = sod.id_supply_order)
                        INNER JOIN ps_icr i ON ( soi.id_icr = i.id_icr AND i.id_estado_icr = 2 )
                        INNER JOIN ps_warehouse w ON ( w.id_warehouse = soi.id_warehouse)
                        INNER JOIN ps_product p ON ( sod.id_product = p.id_product )
                        WHERE soi.fecha BETWEEN "'.$input1.' 00:00:00" AND "'.$input2.' 23:59:59"                               
                        ORDER BY '.$orden_query.' ASC';

                if ($results = Db::getInstance_slv()->ExecuteS($sql)) {

                    $output = fopen('php://output', 'w');
                    fputcsv( $output, array( "ORDEN_SUMINISTRO", "REFERENCIA", "DESCRIPCION", "ICR", "PRECIO_UNITARIO", "IVA", "BODEGA", "LOTE", "FECHA_VENCIMIENTO", "REGISTRO_SANITARIO", "FECHA_INGRESO" ) );


                            foreach ($results as $dat_print) {
                                fputcsv( $output, array( utf8_decode($dat_print['id_supply_order']), utf8_decode($dat_print['reference']), utf8_decode($dat_print['name']), utf8_decode($dat_print['cod_icr']), utf8_decode($dat_print['unit_price_te']), utf8_decode($dat_print['tax_rate']), utf8_decode($dat_print['bodega']), utf8_decode($dat_print['lote']), utf8_decode($dat_print['fecha_vencimiento']), utf8_decode($dat_print['upc']), utf8_decode($dat_print['fecha']) ) );

                            }

                } else {
                    header("Content-type: application/force-download");
                    header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.csv"));
                    header("Content-Transfer-Encoding: binary"); 
                }
            } else {
                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Opcion_de_orden_incorrecta.csv"));
                header("Content-Transfer-Encoding: binary"); 
            }
        break;


        case 'repcata':           

                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Reporte_catalogo.csv"));
                header("Content-Transfer-Encoding: binary"); 

                $sql = 'SELECT p.id_product, 
                p.reference AS referencia, 
                pll.name AS description,
                p.upc AS RegistroInvima, 
                pss.price AS precio,  
                IF ( t.rate IS NULL , "0.0" , CONVERT(t.rate, UNSIGNED INTEGER) ) AS id_iva_prod,
                p.active AS estado, 
                pss.active,
                pss.available_for_order,
                blmt.name AS Motivo_cancelacion, 
                GROUP_CONCAT( sup.name SEPARATOR "|" ) AS Proveedores  
                FROM ps_product p
                INNER JOIN ps_product_lang pll ON ( p.id_product = pll.id_product )
                INNER JOIN ps_product_shop pss ON ( p.id_product = pss.id_product )
                LEFT JOIN ps_tax_rule tr ON ( tr.id_tax_rules_group = pss.id_tax_rules_group AND tr.id_tax_rule NOT IN (3,4) )
                LEFT JOIN ps_tax t ON ( t.id_tax = tr.id_tax AND t.active = 1 AND t.deleted = 0 )
                LEFT JOIN ps_product_black_list pbl ON ( p.id_product = pbl.id_product )
                LEFT JOIN ps_black_motivo blmt ON ( pbl.motivo = blmt.id_black_motivo )
                LEFT JOIN ps_product_supplier psup ON ( p.id_product = psup.id_product ) 
                LEFT JOIN ps_supplier sup ON ( psup.id_supplier = sup.id_supplier )
                GROUP BY p.id_product
                ORDER BY sup.id_supplier ASC';

                if ($results = Db::getInstance_slv()->ExecuteS($sql)) {
                    $output = fopen('php://output', 'w');
                    fputcsv( $output, array( "ID_PRODUCTO" , "REFERENCIA" , "DESCRIPCION" , "REGISTRO" , "PRECIO" , "IVA_PROD" , "ACTIVO_PRODUCT" , "ACTIVO_PRODUCT_SHOP" , "DISPONIBLE_ORDENAR" , "MOTIVO" , "PROVEEDORES" ) ) ;

                            foreach ($results as $dat_print) {
                               fputcsv( $output, array( $dat_print['id_product'], $dat_print['referencia'], utf8_decode($dat_print['description']), utf8_decode($dat_print['RegistroInvima']), $dat_print['precio'], $dat_print['id_iva_prod'], $dat_print['estado'], $dat_print['active'], $dat_print['available_for_order'], utf8_decode($dat_print['Motivo_cancelacion']), utf8_decode($dat_print['Proveedores']) ) ) ;                                
                            }

                } else {
                    header("Content-type: application/force-download");
                    header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.csv"));
                    header("Content-Transfer-Encoding: binary"); 
                }

        break;
        
        default:
            header("Content-type: application/force-download");
            header("Content-Disposition: attachment; filename=".basename("Ninguna_opcion_seleccionada.csv"));
            header("Content-Transfer-Encoding: binary"); 
        break;
    }
}

?>