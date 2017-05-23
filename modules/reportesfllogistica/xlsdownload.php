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
                header("Content-type: text/csv; charset=utf-8");
                header("Content-Disposition: attachment; filename=".basename("Descarga_pedidos.csv"));
                header("Content-Transfer-Encoding: binary"); 
                $sql = 'SELECT
                        o.id_order,                        
                        CONCAT(c.firstname, " ", c.lastname) AS cliente,
                        a.address1,
                        a.address2,
                        o.date_add,
                        a.city,
                        od.product_reference,
                        od.product_name,
                        CONCAT( "$ ", od.product_price ) AS precio,                        
                        od.product_quantity,
                        i.cod_icr,                        
                        osl.name,                        
                        sod.id_supply_order,                        
                        ct.date_delivery as Fecha_entrga,
                        ct.time_windows as Hora_entrega,
                        o.payment,
                    CASE asoc.entity
                    WHEN  "employee" THEN CONCAT(emp.firstname," " , emp.lastname)
                    WHEN  "Carrier" THEN trans.`name`
                    ELSE "N/A"
                    END
                    AS Transportador,
                    a.postcode AS codpostal,
                    CONCAT(a.phone, ", ", a.phone_mobile) AS Tel_address,
                    o.total_paid_tax_incl AS total_pagado
                    FROM ps_order_detail od  -- 174411
                    INNER JOIN ps_orders o ON ( o.id_order = od.id_order )
                    INNER JOIN ps_customer c ON ( o.id_customer = c.id_customer )
                    INNER JOIN ps_address a ON ( o.id_address_delivery = a.id_address )
                    INNER JOIN ps_order_state_lang osl ON ( osl.id_order_state = o.current_state )
                    INNER JOIN ps_cart ct ON (o.id_cart = ct.id_cart)
                    LEFT JOIN ps_order_picking op ON ( op.id_order_detail = od.id_order_detail )
                    LEFT JOIN ps_icr i ON ( op.id_order_icr = i.id_icr )
                    LEFT JOIN ps_supply_order_icr soi ON ( i.id_icr = soi.id_icr      )
                    LEFT JOIN ps_supply_order_detail sod ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
                    LEFT JOIN ps_orders_transporte ot ON ( ot.id_order = o.id_order )
                    LEFT JOIN ps_transporte_opciones tro ON ( tro.id_transporte_opcion = ot.id_transporte_opcion ) 
                    LEFT JOIN ps_associate_carrier asoc ON (o.id_order = asoc.id_order)
                    LEFT JOIN ps_carrier trans ON(asoc.id_entity = trans.id_carrier)
                    LEFT JOIN ps_employee emp ON(asoc.id_entity = emp.id_employee)
                    WHERE o.date_add BETWEEN "'.$input1.' 00:00:00" AND "'.$input2.' 23:59:59"
                    ORDER BY ct.date_delivery ,ct.time_delivery,o.date_add ASC';
                    //echo $sql;
                    //echo"<hr>";
                    //exit();
                
                if ($results = Db::getInstance_slv()->ExecuteS($sql)) {
                    $output = fopen('php://output', 'w');
                    fputcsv($output, array('NUM PEDIDO', 'CLIENTE', 'DIRECCION 1', 'DIRECCION 2', 'CIUDAD DESTINO', 'FECHA', 'REFERENCIA', 'DESCRIPCION', 'PRECIO', 'CANTIDAD', 'ICR', 'ESTADO ORDEN', 'ORDEN SUMINISTRO', 'FECHA DE ENTREGA', 'HORA DE ENTREGA', 'METODO DE PAGO', 'TRANSPORTADOR', 'CODIGO POSTAL', 'TELEFONOS', 'TOTAL PAGADO'));
                    /*echo "<table border='1'>
                            <tr>
                                <td> NUM PEDIDO </td>
                                <td> NUM FACTURA </td>
                                <td> CLIENTE </td>
                                <td> DIRECCION 1 </td>
                                <td> DIRECCION 2 </td>
                                <td> CIUDAD DESTINO </td>
                                <td> FECHA </td>
                                <td> REFERENCIA </td>
                                <td> DESCRIPCION </td>
                                <td> PRECIO </td>
                                <td> PRECIO CON IMPUESTO</td>
                                <td> CANTIDAD </td>
                                <td> ICR </td>
                                <td> TRANSPORTADORA </td>
                                <td> ESTADO ORDEN </td>
                                <td> COSTO ICR </td>
                                <td> IVA PROVEEDOR </td>
                                <td> FECHA DE ENTREGA </td>
                                <td> HORA DE ENTREGA </td>
                                <td> TRANSPORTADOR </td>
                            </tr>";*/
                            foreach ($results as $dat_print) {
                                /*echo "
                                    <tr>
                                        <td> ".$dat_print['id_order']." </td>
                                        <td> ".$dat_print['invoice_number']." </td>
                                        <td> ".utf8_decode($dat_print['cliente'])." </td>
                                        <td> ".utf8_decode($dat_print['address1'])." </td>
                                        <td> ".utf8_decode($dat_print['address2'])." </td>
                                        <td> ".utf8_decode($dat_print['city'])." </td>
                                        <td> ".$dat_print['date_add']." </td>
                                        <td> ".$dat_print['product_reference']." </td>
                                        <td> ".utf8_decode($dat_print['product_name'])." </td>
                                        <td> ".$dat_print['precio']." </td>
                                        <td> ".$dat_print['precio_tax']." </td>
                                        <td> ".$dat_print['product_quantity']." </td>
                                        <td> ".$dat_print['cod_icr']." </td>
                                        <td> ".utf8_decode($dat_print['nombre'])." </td>
                                        <td> ".utf8_decode($dat_print['name'])." </td>
                                        <td> ".$dat_print['costo_icr']." </td>
                                        <td> ".$dat_print['iva_proveedor']." </td>
                                         <td> ".$dat_print['Fecha_entrga']." </td>
                                          <td> ".$dat_print['Hora_entrega']." </td>
                                        <td> ".$dat_print['Transportador']." </td>
                                    </tr>
                                ";*/
                                fputcsv( $output, array( $dat_print['id_order'], utf8_decode($dat_print['cliente']), utf8_decode($dat_print['address1']), utf8_decode($dat_print['address2']), utf8_decode($dat_print['city']), $dat_print['date_add'], $dat_print['product_reference'], utf8_decode($dat_print['product_name']), $dat_print['precio'], $dat_print['product_quantity'], $dat_print['cod_icr'], utf8_decode($dat_print['name']), $dat_print['id_supply_order'], $dat_print['Fecha_entrga'], $dat_print['Hora_entrega'], $dat_print['payment'], $dat_print['Transportador'], utf8_decode($dat_print['codpostal']), utf8_decode($dat_print['Tel_address']), utf8_decode($dat_print['total_pagado']) ));                    
                            }
                    //echo "</table>";
                } else {
                   header("Content-type: text/csv; charset=utf-8");
                   header("Content-Disposition: attachment; filename=".basename("No_se_pudo_generar_la_consulta.csv"));
                   header("Content-Transfer-Encoding: binary"); 
                }
            } else {
                header("Content-type: text/csv; charset=utf-8");
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
                $sql = 'SELECT so.id_supply_order, sod.reference, sod.`name`, i.cod_icr, REPLACE(sod.unit_price_te ,".",",") unit_price_te, REPLACE(sod.tax_rate ,".",",") tax_rate, w.`name` AS bodega, soi.lote, soi.fecha_vencimiento,
                        DATE_ADD( soi.fecha ,INTERVAL -5 HOUR ) AS fecha, p.upc
                        FROM ps_supply_order_icr soi
                        INNER JOIN ps_supply_order_detail sod ON ( soi.id_supply_order_detail = sod.id_supply_order_detail)
                        INNER JOIN ps_supply_order so ON ( so.id_supply_order = sod.id_supply_order)
                        INNER JOIN ps_icr i ON ( soi.id_icr = i.id_icr AND i.id_estado_icr = 2 )
                        INNER JOIN ps_warehouse w ON ( w.id_warehouse = soi.id_warehouse)
                        INNER JOIN ps_product p ON ( sod.id_product = p.id_product )
                        WHERE DATE_ADD( soi.fecha ,INTERVAL -5 HOUR ) BETWEEN "'.$input1.' 00:00:00" AND "'.$input2.' 23:59:59"
                        ORDER BY '.$orden_query.' ASC';
                //echo $sql;
                //exit();                
                
                if ($results = Db::getInstance_slv()->ExecuteS($sql)) {
                    $output = fopen('php://output', 'w');
                    fputcsv( $output, array( "ORDEN_SUMINISTRO", "REFERENCIA", "DESCRIPCION", "ICR", "PRECIO_UNITARIO", "IVA", "BODEGA", "LOTE", "FECHA_VENCIMIENTO", "REGISTRO_SANITARIO", "FECHA_INGRESO" ) ) ;
                    /*echo "<table>
                            <tr>     
                                <td> ORDEN_SUMINISTRO </td>
                                <td> REFERENCIA </td>
                                <td> DESCRIPCION </td>
                                <td> ICR </td>
                                <td> PRECIO_UNITARIO </td>
                                <td> IVA </td>
                                <td> BODEGA </td>
                                <td> LOTE </td>
                                <td> FECHA_VENCIMIENTO </td>
                                <td> REGISTRO_SANITARIO </td>
                                <td> FECHA_INGRESO </td>
                            </tr>";*/
                            foreach ($results as $dat_print) {
                        /*echo "
                            <tr>
                                <td> ".utf8_decode($dat_print['id_supply_order'])." </td>
                                <td> ".utf8_decode($dat_print['reference'])." </td>
                                <td> ".utf8_decode($dat_print['name'])." </td>
                                <td> ".utf8_decode($dat_print['cod_icr'])." </td>
                                <td> ".utf8_decode($dat_print['unit_price_te'])." </td>
                                <td> ".utf8_decode($dat_print['tax_rate'])." </td>
                                <td> ".utf8_decode($dat_print['bodega'])." </td>
                                <td> ".utf8_decode($dat_print['lote'])." </td>
                                <td> ".utf8_decode($dat_print['fecha_vencimiento'])." </td>
                                <td> ".utf8_decode($dat_print['upc'])." </td>
                                <td> ".utf8_decode($dat_print['fecha'])." </td>
                            </tr>";*/
                        fputcsv( $output, array( utf8_decode($dat_print['id_supply_order']), utf8_decode($dat_print['reference']), utf8_decode($dat_print['name']), utf8_decode($dat_print['cod_icr']), utf8_decode($dat_print['unit_price_te']), utf8_decode($dat_print['tax_rate']), utf8_decode($dat_print['bodega']), utf8_decode($dat_print['lote']), utf8_decode($dat_print['fecha_vencimiento']), utf8_decode($dat_print['upc']), utf8_decode($dat_print['fecha']) ) ) ;
                            }
                    /*echo "</table>";*/
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
                /*$sql = 'SELECT p.id_product, 
                p.reference AS referencia, 
                pll.name AS description,
                p.upc AS RegistroInvima, 
                REPLACE( pss.price,".",",") AS precio,  
                IF ( t.rate IS NULL , 0 , t.rate) AS id_iva_prod,
                p.active AS estado, 
                pss.active,
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
                ORDER BY sup.id_supplier ASC';*/
                $sql = 'SELECT p.id_product, p.reference AS referencia, pll.name AS description,
                    p.upc AS RegistroInvima, REPLACE( pss.price,".",",") AS precio,  IF ( t.rate IS NULL , 0 , t.rate) AS id_iva_prod,
                    p.active AS estado, pss.active, blmt.name AS Motivo_cancelacion,
                    GROUP_CONCAT( sup2.name SEPARATOR "|" ) AS Proveedores_Asociados,
                    GROUP_CONCAT( DISTINCT (sup.name) SEPARATOR "|" ) AS Proveedores_Comprados3M,
                    m.name AS Fabricante
                    FROM ps_product p
                    INNER JOIN ps_product_lang pll ON ( p.id_product = pll.id_product )
                    INNER JOIN ps_product_shop pss ON ( p.id_product = pss.id_product )
                    LEFT JOIN ps_tax_rule tr ON ( tr.id_tax_rules_group = pss.id_tax_rules_group AND tr.id_tax_rule NOT IN (3,4) )
                    LEFT JOIN ps_tax t ON ( t.id_tax = tr.id_tax AND t.active = 1 AND t.deleted = 0 )
                    LEFT JOIN ps_product_black_list pbl ON ( p.id_product = pbl.id_product )
                    LEFT JOIN ps_black_motivo blmt ON ( pbl.motivo = blmt.id_black_motivo )
                    LEFT JOIN ps_supply_order_detail sod ON ( p.id_product = sod.id_product AND sod.quantity_received != 0 )
                    LEFT JOIN ps_supply_order so ON ( so.id_supply_order = sod.id_supply_order AND so.id_supply_order_state IN (4, 5)
                                            AND so.date_add >= NOW() - INTERVAL 3 MONTH )
                    LEFT JOIN ps_supplier sup ON ( so.id_supplier = sup.id_supplier )
                    LEFT JOIN ps_product_supplier psup ON ( p.id_product = psup.id_product ) 
                    LEFT JOIN ps_supplier sup2 ON ( psup.id_supplier = sup2.id_supplier )  
                    LEFT JOIN ps_manufacturer m ON ( p.id_manufacturer = m.id_manufacturer )
                    GROUP BY p.id_product
                ORDER BY sup.id_supplier ASC';
                if ($results = Db::getInstance_slv()->ExecuteS($sql)) {
                    $output = fopen('php://output', 'w');
                    fputcsv( $output, array( "ID_PRODUCTO" , "REFERENCIA" , "DESCRIPCION" , "REGISTRO" , "PRECIO" , "IVA_PROD" , "ACTIVO_PRODUCT" , "ACTIVO_PRODUCT_SHOP" , "MOTIVO" , "PROVEEDORES ASOCIADOS", "PROVEEDORES COMPRA 3Meses", "FABRICANTE") ) ;
                    /*echo "<table border='1'>
                            <tr>
                                <td> ID_PRODUCTO </td>
                                <td> REFERENCIA </td>
                                <td> DESCRIPCION </td>
                                <td> REGISTRO INVIMA </td>
                                <td> PRECIO </td>
                                <td> IVA_PROD </td>
                                <td> ACTIVO_PRODUCT </td>
                                <td> ACTIVO_PRODUCT_SHOP </td>
                                <td> MOTIVO CANCELACION </td>
                                <td> PROVEEDORES </td>                                
                            </tr>";*/
                            foreach ($results as $dat_print) {
                               fputcsv( $output, array( $dat_print['id_product'], $dat_print['referencia'], utf8_decode($dat_print['description']), utf8_decode($dat_print['RegistroInvima']), $dat_print['precio'], $dat_print['id_iva_prod'], $dat_print['estado'], $dat_print['active'], utf8_decode($dat_print['Motivo_cancelacion']), utf8_decode($dat_print['Proveedores_Asociados']), utf8_decode($dat_print['Proveedores_Comprados3M']), utf8_decode($dat_print['Fabricante']) ) ) ;
                                
                                /*echo "
                                    <tr>
                                        <td> ".$dat_print['id_product']." </td>
                                        <td> ".$dat_print['referencia']." </td>
                                        <td> ".utf8_decode($dat_print['description'])." </td>
                                        <td> ".utf8_decode($dat_print['RegistroInvima'])." </td>
                                        <td> ".$dat_print['precio']." </td>
                                        <td> ".$dat_print['id_iva_prod']." </td>
                                        <td> ".$dat_print['estado']." </td>
                                        <td> ".$dat_print['active']." </td>
                                        <td> ".utf8_decode($dat_print['Motivo_cancelacion'])." </td>
                                        <td> ".utf8_decode($dat_print['Proveedores'])." </td>
                                    </tr>
                                ";*/
                            }
                    /*echo "</table>";*/
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
