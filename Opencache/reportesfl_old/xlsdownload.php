<?php

include_once(dirname(__FILE__)."/../../config/config.inc.php");

if (isset($_GET['opc_sel']) ) {

    switch ($_GET['opc_sel']) {
        case 'consped':
                
                $date_format = 'Y-m-d';
                // echo "<br> 1: ".
                $input1 = $_GET['f_ini'];
                // echo "<br> 2: ".
                $input2 = $_GET['f_fin'];

                $input1 = trim($input1);
                $input2 = trim($input2);

                $time1 = strtotime($input1);
                $time2 = strtotime($input2);


                if ( date($date_format, $time1) == $input1 && date($date_format, $time2) == $input2 && isset($_GET['f_ini']) && $_GET['f_ini'] != '' 
                    && isset($_GET['f_fin']) && $_GET['f_fin'] != '' ) {

                        header("Content-type: application/force-download");
                        header("Content-Disposition: attachment; filename=".basename("Descarga_pedidos.xls"));
                        header("Content-Transfer-Encoding: binary"); 

                        $sql = 'SELECT o.id_order, o.invoice_number, CONCAT(c.firstname, " ", c.lastname) AS cliente,
                            a.address1, a.address2, o.date_add, od.product_reference, od.product_name, CONCAT( "$ ", REPLACE( (od.product_price ),".",",")) AS precio,
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

                        if ($results = Db::getInstance()->ExecuteS($sql)) {

                            echo "<table>
                                    <tr>
                                        <td> NUM PEDIDO </td>
                                        <td> NUM FACTURA </td>
                                        <td> CLIENTE </td>
                                        <td> DIRECCION 1 </td>
                                        <td> DIRECCION 2 </td>
                                        <td> FECHA </td>
                                        <td> REFERENCIA </td>
                                        <td> DESCRIPCION </td>
                                        <td> PRECIO </td>
                                        <td> CANTIDAD </td>
                                        <td> ICR </td>
                                        <td> TRANSPORTADORA </td>
                                        <td> ESTADO ORDEN </td>
                                    </tr>";

                                    foreach ($results as $dat_print) {
                                         echo "
                                    <tr>
                                        <td> ".$dat_print['id_order']." </td>
                                        <td> ".$dat_print['invoice_number']." </td>
                                        <td> ".utf8_decode($dat_print['cliente'])." </td>
                                        <td> ".utf8_decode($dat_print['address1'])." </td>
                                        <td> ".utf8_decode($dat_print['address2'])." </td>
                                        <td> ".$dat_print['date_add']." </td>
                                        <td> ".$dat_print['product_reference']." </td>
                                        <td> ".utf8_decode($dat_print['product_name'])." </td>
                                        <td> ".$dat_print['precio']." </td>
                                        <td> ".$dat_print['product_quantity']." </td>
                                        <td> ".$dat_print['cod_icr']." </td>
                                        <td> ".utf8_decode($dat_print['nombre'])." </td>
                                        <td> ".utf8_decode($dat_print['name'])." </td>
                                    </tr>";
                                    }

                                    echo "</table>";

                           
                        } else {
                        header("Content-type: application/force-download");
                        header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.xls"));
                        header("Content-Transfer-Encoding: binary"); 
                        }


                } else {
                        header("Content-type: application/force-download");
                        header("Content-Disposition: attachment; filename=".basename("Formato_de_fecha_erroneo_o_fecha_incorrecta.xls"));
                        header("Content-Transfer-Encoding: binary"); 
                }

            break;

        case 'icrsumi':

                if (  isset($_GET['orden']) && $_GET['orden'] != '' ) {

                        header("Content-type: application/force-download");
                        header("Content-Disposition: attachment; filename=".basename("Descarga_icr_asignados.xls"));
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



                        $sql = 'SELECT so.id_supply_order, sod.reference, sod.`name`, i.cod_icr, REPLACE(sod.unit_price_te ,".",",") unit_price_te, REPLACE(sod.tax_rate ,".",",") tax_rate, w.`name` AS bodega FROM ps_supply_order_icr soi
                                INNER JOIN ps_supply_order_detail sod ON ( soi.id_supply_order_detail = sod.id_supply_order_detail)
                                INNER JOIN ps_supply_order so ON ( so.id_supply_order = sod.id_supply_order)
                                INNER JOIN ps_icr i ON ( soi.id_icr = i.id_icr AND i.id_estado_icr = 2 )
                                INNER JOIN ps_warehouse w ON ( w.id_warehouse = soi.id_warehouse)
                                ORDER BY '.$orden_query.' ASC';

                        if ($results = Db::getInstance()->ExecuteS($sql)) {

                            echo "<table>
                                    <tr>     
                                        <td> ORDEN_SUMINISTRO </td>
                                        <td> REFERENCIA </td>
                                        <td> DESCRIPCION </td>
                                        <td> ICR </td>
                                        <td> PRECIO_UNITARIO </td>
                                        <td> IVA </td>
                                        <td> BODEGA </td>
                                    </tr>";

                                    foreach ($results as $dat_print) {
                                         echo "
                                    <tr>
                                        <td> ".utf8_decode($dat_print['id_supply_order'])." </td>
                                        <td> ".utf8_decode($dat_print['reference'])." </td>
                                        <td> ".utf8_decode($dat_print['name'])." </td>
                                        <td> ".utf8_decode($dat_print['cod_icr'])." </td>
                                        <td> ".utf8_decode($dat_print['unit_price_te'])." </td>
                                        <td> ".utf8_decode($dat_print['tax_rate'])." </td>
                                        <td> ".utf8_decode($dat_print['bodega'])." </td>
                                    </tr>";
                                    }

                                    echo "</table>";

                           
                        } else {
                        header("Content-type: application/force-download");
                        header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.xls"));
                        header("Content-Transfer-Encoding: binary"); 
                        }


                } else {
                        header("Content-type: application/force-download");
                        header("Content-Disposition: attachment; filename=".basename("Opcion_de_orden_incorrecta.xls"));
                        header("Content-Transfer-Encoding: binary"); 
                }
            break;
        
        default:
                header("Content-type: application/force-download");
                header("Content-Disposition: attachment; filename=".basename("Ninguna_opcion_seleccionada.xls"));
                header("Content-Transfer-Encoding: binary"); 
            break;
    }
    
}

?>