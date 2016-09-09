<?php

include_once(dirname(__FILE__)."/../config/config.inc.php");


/*********************************** ICR YA VENCIDOS QUE ESTÁN EN BODEGA ************************************/

$reporte_icr['vencidos']['query'] =  "SELECT 
        '0' AS dias_alertar , '0' AS dias_para_vencer, ordericr.fecha_vencimiento, icr.cod_icr, 
        orderdtl.name, orderdtl.reference, ml.`name` AS laboratorio, GROUP_CONCAT( DISTINCT sl.`name` ) AS proveedores,
        IF ( w2.name IS NOT NULL, w2.`name`, w.`name` )  AS Bodega, ordericr.fecha
            FROM ps_supply_order_icr ordericr 
                INNER JOIN ps_supply_order_detail orderdtl ON ( orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail )
                INNER JOIN ps_icr icr ON ( icr.id_estado_icr = 2 AND ordericr.id_icr= icr.id_icr )
                INNER JOIN ps_product p ON ( orderdtl.id_product = p.id_product )
                LEFT JOIN ps_manufacturer ml ON ( p.id_manufacturer = ml.id_manufacturer)
                LEFT JOIN ps_product_supplier ps ON ( p.id_product = ps.id_product )
                LEFT JOIN ps_supplier sl ON ( sl.id_supplier = ps.id_supplier )
                LEFT JOIN ps_supply_order so ON ( so.id_supply_order = orderdtl.id_supply_order )
                LEFT JOIN ps_warehouse w ON ( w.id_warehouse = so.id_warehouse )
                LEFT JOIN ps_warehouse w2 ON ( w2.id_warehouse = ordericr.id_warehouse )
                WHERE  ordericr.fecha_vencimiento <> '0000-00-00'
                AND ordericr.fecha_vencimiento <> '1969-12-31'
                AND ordericr.fecha_vencimiento <> ''
                AND STR_TO_DATE(ordericr.fecha_vencimiento, '%Y-%m-%d') <  CURDATE()               
         GROUP BY icr.id_icr
        ORDER BY ordericr.fecha /*afvi.dias_alertar*/ ASC";

$reporte_icr['vencidos']['sheet'] = 'ICR VENCIDOS';
$reporte_icr['vencidos']['title'] = ' ICR\'S VENCIDOS QUE SE ENCUENTRAN EN BODEGA ';



/*********************************** SIN FECHA DE VENCIMIENTO INGRESADA ************************************/

$reporte_icr['sinfecha']['query'] =  "SELECT 
        'N' AS dias_alertar , 'N' AS dias_para_vencer, ordericr.fecha_vencimiento, icr.cod_icr, 
        orderdtl.name, orderdtl.reference, ml.`name` AS laboratorio, GROUP_CONCAT( DISTINCT sl.`name` ) AS proveedores,
        IF ( w2.name IS NOT NULL, w2.`name`, w.`name` )  AS Bodega, ordericr.fecha
            FROM ps_supply_order_icr ordericr 
                INNER JOIN ps_supply_order_detail orderdtl ON ( orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail )
                INNER JOIN ps_icr icr ON ( icr.id_estado_icr = 2 AND ordericr.id_icr= icr.id_icr )
                INNER JOIN ps_product p ON ( orderdtl.id_product = p.id_product )
                LEFT JOIN ps_manufacturer ml ON ( p.id_manufacturer = ml.id_manufacturer)
                LEFT JOIN ps_product_supplier ps ON ( p.id_product = ps.id_product )
                LEFT JOIN ps_supplier sl ON ( sl.id_supplier = ps.id_supplier )
                LEFT JOIN ps_supply_order so ON ( so.id_supply_order = orderdtl.id_supply_order )
                LEFT JOIN ps_warehouse w ON ( w.id_warehouse = so.id_warehouse )
                LEFT JOIN ps_warehouse w2 ON ( w2.id_warehouse = ordericr.id_warehouse )
            WHERE  ordericr.fecha_vencimiento = '0000-00-00'
         GROUP BY icr.id_icr
        ORDER BY ordericr.fecha /*afvi.dias_alertar*/ ASC";

$reporte_icr['sinfecha']['sheet'] = 'SIN FECHA VENCIMIENTO';
$reporte_icr['sinfecha']['title'] = ' ICR\'S CON FECHA DE VENCIMIENTO NO ESPECIFICADA ';


/*********************************** SIN FECHA DE VENCIMIENTO INGRESADA QUE NO ESTAN EN BODEGA POR QUE SE ENVIARON AL CLIENTE ************************************/
/*
$reporte_icr['sinfechabod']['query'] =  "SELECT 
        'N' AS dias_alertar , 'N' AS dias_para_vencer, ordericr.fecha_vencimiento, icr.cod_icr, 
        orderdtl.name, orderdtl.reference, ml.`name` AS laboratorio, GROUP_CONCAT( DISTINCT sl.`name` ) AS proveedores,
        IF ( w2.name IS NOT NULL, w2.`name`, w.`name` )  AS Bodega, ordericr.fecha
            FROM ps_supply_order_icr ordericr 
                INNER JOIN ps_supply_order_detail orderdtl ON ( orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail )
                INNER JOIN ps_icr icr ON ( icr.id_estado_icr = 3 AND ordericr.id_icr= icr.id_icr )
                INNER JOIN ps_product p ON ( orderdtl.id_product = p.id_product )
                LEFT JOIN ps_manufacturer ml ON ( p.id_manufacturer = ml.id_manufacturer)
                LEFT JOIN ps_product_supplier ps ON ( p.id_product = ps.id_product )
                INNER JOIN ps_supplier sl ON ( sl.id_supplier = ps.id_supplier )
                INNER JOIN ps_supply_order so ON ( so.id_supply_order = orderdtl.id_supply_order )
                INNER JOIN ps_warehouse w ON ( w.id_warehouse = so.id_warehouse )
                LEFT JOIN ps_warehouse w2 ON ( w2.id_warehouse = ordericr.id_warehouse )
            WHERE  ordericr.fecha_vencimiento = '0000-00-00'
         GROUP BY icr.id_icr
        ORDER BY ordericr.fecha ASC";

$reporte_icr['sinfechabod']['sheet'] = 'SIN FECHA VENCIMIENTO-SALIENTES';
$reporte_icr['sinfechabod']['title'] = ' ICR\'S CON FECHA DE VENCIMIENTO NO ESPECIFICADA QUE SE ENTREGARON AL CLIENTE';

*/


/***********************************  FECHA DE VENCIMIENTO VALIDACIÓN GENÉRICA ************************************/

$reporte_icr['generico']['query'] =  "SELECT afvi.dias_alertar , 
        TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) AS dias_para_vencer, ordericr.fecha_vencimiento, icr.cod_icr, 
        orderdtl.name, orderdtl.reference, ml.`name` AS laboratorio, GROUP_CONCAT( DISTINCT sl.`name` ) AS proveedores,
        IF ( w2.name IS NOT NULL, w2.`name`, w.`name` )  AS Bodega, ordericr.fecha
            FROM ps_supply_order_icr ordericr 
                INNER JOIN ps_supply_order_detail orderdtl ON ( orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail )
                INNER JOIN ps_icr icr ON ( icr.id_estado_icr = 2 AND ordericr.id_icr= icr.id_icr )
                INNER JOIN ps_product p ON ( orderdtl.id_product = p.id_product )
                LEFT JOIN ps_manufacturer ml ON ( p.id_manufacturer = ml.id_manufacturer)
                LEFT JOIN ps_product_supplier ps ON ( p.id_product = ps.id_product )
                INNER JOIN ps_supplier sl ON ( sl.id_supplier = ps.id_supplier )
                INNER JOIN ps_alerta_fecha_venci_icr afvi ON ( afvi.id_alerta_icr = 1 AND afvi.activo = 1 AND 
                                                    ( afvi.id_proveedor IS NULL AND afvi.id_laboratorio IS NULL ) )
                INNER JOIN ps_supply_order so ON ( so.id_supply_order = orderdtl.id_supply_order )
                INNER JOIN ps_warehouse w ON ( w.id_warehouse = so.id_warehouse )      
                LEFT JOIN ps_warehouse w2 ON ( w2.id_warehouse = ordericr.id_warehouse )
                WHERE  ordericr.fecha_vencimiento <> '0000-00-00' 
                        AND ordericr.fecha_vencimiento <> '1969-12-31' 
                        AND STR_TO_DATE(ordericr.fecha_vencimiento, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL afvi.dias_alertar DAY) 
                GROUP BY icr.id_icr
                ORDER BY TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) /*afvi.dias_alertar*/ ASC";

$reporte_icr['generico']['sheet'] = 'MENOR A ';
$reporte_icr['generico']['title'] = ' ICR\'S PRÓXIMOS A VENCER DETERMINADO POR UN TIEMPO GENÉRICO DE ';



/***********************************  FECHA DE VENCIMIENTO VALIDACIÓN LABORATORIO ************************************/

$reporte_icr['laboratorio']['query'] =  "SELECT afvi.dias_alertar , 
        TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) AS dias_para_vencer, ordericr.fecha_vencimiento, icr.cod_icr, 
        orderdtl.name, orderdtl.reference, ml.`name` AS laboratorio, GROUP_CONCAT( DISTINCT sl.`name` ) AS proveedores,
        IF ( w2.name IS NOT NULL, w2.`name`, w.`name` )  AS Bodega, ordericr.fecha
            FROM ps_supply_order_icr ordericr 
            INNER JOIN ps_supply_order_detail orderdtl ON ( orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail )
            INNER JOIN ps_icr icr ON ( icr.id_estado_icr = 2 AND ordericr.id_icr= icr.id_icr )
            INNER JOIN ps_product p ON ( orderdtl.id_product = p.id_product )
            LEFT JOIN ps_manufacturer ml ON ( p.id_manufacturer = ml.id_manufacturer)
            LEFT JOIN ps_product_supplier ps ON ( p.id_product = ps.id_product )
            INNER JOIN ps_supplier sl ON ( sl.id_supplier = ps.id_supplier )
            INNER JOIN ps_supply_order so ON ( so.id_supply_order = orderdtl.id_supply_order )
            INNER JOIN ps_warehouse w ON ( w.id_warehouse = so.id_warehouse )
            LEFT JOIN ps_warehouse w2 ON ( w2.id_warehouse = ordericr.id_warehouse )
            INNER JOIN ps_alerta_fecha_venci_icr afvi ON ( afvi.activo = 1 AND 
                                            ( afvi.id_laboratorio = ml.id_manufacturer AND afvi.id_proveedor IS NULL ) )        
            WHERE  ordericr.fecha_vencimiento <> '0000-00-00' 
                AND ordericr.fecha_vencimiento <> '1969-12-31' 
                AND STR_TO_DATE(ordericr.fecha_vencimiento, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL afvi.dias_alertar DAY) 
            GROUP BY icr.id_icr
                ORDER BY TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) ASC";

$reporte_icr['laboratorio']['sheet'] = 'X LABORATORIO < A ';
$reporte_icr['laboratorio']['title'] = ' ICR\'S PRÓXIMOS A VENCER POR REGLA DE LABORATORIO INICIANDO CON ';



/***********************************  FECHA DE VENCIMIENTO VALIDACIÓN PROVEEDOR ************************************/

$reporte_icr['proveedor']['query'] =  "SELECT afvi.dias_alertar , 
        TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) AS dias_para_vencer, ordericr.fecha_vencimiento, icr.cod_icr, 
        orderdtl.name, orderdtl.reference, ml.`name` AS laboratorio, GROUP_CONCAT( DISTINCT sl.`name` ) AS proveedores,
        IF ( w2.name IS NOT NULL, w2.`name`, w.`name` )  AS Bodega, ordericr.fecha
            FROM ps_supply_order_icr ordericr 
                INNER JOIN ps_supply_order_detail orderdtl ON ( orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail )
                INNER JOIN ps_icr icr ON ( icr.id_estado_icr = 2 AND ordericr.id_icr= icr.id_icr )
                INNER JOIN ps_product p ON ( orderdtl.id_product = p.id_product )
                LEFT JOIN ps_manufacturer ml ON ( p.id_manufacturer = ml.id_manufacturer)
                LEFT JOIN ps_product_supplier ps ON ( p.id_product = ps.id_product )
                LEFT JOIN ps_supplier sl ON ( sl.id_supplier = ps.id_supplier )
                LEFT JOIN ps_supply_order so ON ( so.id_supply_order = orderdtl.id_supply_order )
                LEFT JOIN ps_warehouse w ON ( w.id_warehouse = so.id_warehouse )
                LEFT JOIN ps_warehouse w2 ON ( w2.id_warehouse = ordericr.id_warehouse )
                INNER JOIN ps_alerta_fecha_venci_icr afvi ON ( afvi.activo = 1 AND 
                                            ( afvi.id_proveedor = sl.id_supplier AND afvi.id_laboratorio IS NULL ) )
        WHERE  ordericr.fecha_vencimiento <> '0000-00-00' 
                AND ordericr.fecha_vencimiento <> '1969-12-31' 
                AND STR_TO_DATE(ordericr.fecha_vencimiento, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL afvi.dias_alertar DAY) 
        GROUP BY icr.id_icr
                ORDER BY TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) ASC";

$reporte_icr['proveedor']['sheet'] = 'X PROVEEDOR < A ';
$reporte_icr['proveedor']['title'] = ' ICR\'S PRÓXIMOS A VENCER POR REGLA DE PROVEEDOR INICIANDO CON ';



/***********************************  FECHA DE VENCIMIENTO VALIDACIÓN LABORATORIO/PROVEEDOR ************************************/

$reporte_icr['lab_proveedor']['query'] =  "SELECT afvi.dias_alertar , 
        TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) AS dias_para_vencer, ordericr.fecha_vencimiento, icr.cod_icr, 
        orderdtl.name, orderdtl.reference, ml.`name` AS laboratorio, GROUP_CONCAT( DISTINCT sl.`name` ) AS proveedores,
        IF ( w2.name IS NOT NULL, w2.`name`, w.`name` )  AS Bodega, ordericr.fecha
            FROM ps_supply_order_icr ordericr 
                INNER JOIN ps_supply_order_detail orderdtl ON ( orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail )
                INNER JOIN ps_icr icr ON ( icr.id_estado_icr = 2 AND ordericr.id_icr= icr.id_icr )
                INNER JOIN ps_product p ON ( orderdtl.id_product = p.id_product )
                LEFT JOIN ps_manufacturer ml ON ( p.id_manufacturer = ml.id_manufacturer)
                LEFT JOIN ps_product_supplier ps ON ( p.id_product = ps.id_product )
                LEFT JOIN ps_supplier sl ON ( sl.id_supplier = ps.id_supplier )
                LEFT JOIN ps_supply_order so ON ( so.id_supply_order = orderdtl.id_supply_order )
                LEFT JOIN ps_warehouse w ON ( w.id_warehouse = so.id_warehouse )
                LEFT JOIN ps_warehouse w2 ON ( w2.id_warehouse = ordericr.id_warehouse )
                INNER JOIN ps_alerta_fecha_venci_icr afvi ON ( afvi.activo = 1 AND 
                                            ( afvi.id_proveedor = sl.id_supplier AND afvi.id_laboratorio = ml.id_manufacturer ) )
        WHERE  ordericr.fecha_vencimiento <> '0000-00-00' 
                AND ordericr.fecha_vencimiento <> '1969-12-31' 
                AND STR_TO_DATE(ordericr.fecha_vencimiento, '%Y-%m-%d') BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL afvi.dias_alertar DAY) 
        GROUP BY icr.id_icr
                ORDER BY TIMESTAMPDIFF(DAY, CURDATE(), ordericr.fecha_vencimiento) ASC";

$reporte_icr['lab_proveedor']['sheet'] = 'X LAB_PROVEEDOR < A ';
$reporte_icr['lab_proveedor']['title'] = ' ICR\'S PRÓXIMOS A VENCER POR REGLA DE LABORATORIO Y PROVEEDOR INICIANDO CON ';


                    include_once(dirname(__FILE__)."/../tools/phpexcel/PHPExcel.php");
                    require_once dirname(__FILE__)."/../tools/phpexcel/PHPExcel/IOFactory.php"; 

                    $objPHPExcel = new PHPExcel();
                    $sheet_number = 0;

        foreach ( $reporte_icr as $iteracion => $query_text  ) {

         //echo("<br>".$iteracion);

                if ($results = Db::getInstance()->ExecuteS($query_text['query'])) {



                    
                    $sheet_name = '';
                    $sheet_reg = 0;
                    $regshow = 0;
                    foreach ($results as $dat_print) {

                                                // Create a new worksheet, after the default sheet
                        //if ( $sheet_name != $dat_print['proveedor'] && $sheet_reg != 0 ) {

                            // Rename sheet
                            
                            //echo "<br>ns: ".$sheet_name." - - - ".$dat_print['dias_alertar'];
                            //echo "<br>regshow: ".$regshow;
                            //echo "<br>sheet_number: ".$sheet_number;
                            //echo "<br>sheet_reg: ".$sheet_reg;

                            //$sheet_name = $dat_print['proveedor'];
                            
                            //$regshow = 0;
                            //$sheet_number ++;
                            //$sheet_reg = 0;
                            
                            //COMENTARIO LEYENDA AL FINAL DE LA HOJA
                            //$row = $objPHPExcel->getActiveSheet()->getHighestRow()+2;
                            //$objPHPExcel->getActiveSheet()->getCell('A'.$row)->setValue('Convenciones AP Aprobado, RE Rechazado');
                            //$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(8);
                            
                            
                        //}


                        if ( $sheet_reg == 0 ) {
                            $objPHPExcel->createSheet();

                            if ( $iteracion == 'vencidos' || $iteracion == 'sinfecha' || $iteracion == 'sinfechabod') {

                                $sheet_name = $query_text['sheet'];

                            } else {

                                $sheet_name = $query_text['sheet'].$dat_print['dias_alertar'].' DIAS';
                            }

                            // Create a first sheet, representing sales data
                            $objPHPExcel->setActiveSheetIndex($sheet_number);
                            $objPHPExcel->setActiveSheetIndex($sheet_number)->mergeCells('A1:L1');
                            
                            $style = array(
                                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    )
                                );

                            $objPHPExcel->getActiveSheet()->getCell("A1")->setValue($query_text['title'].$dat_print['dias_alertar'].' DIAS ');
                            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style);

                            $objPHPExcel->getActiveSheet()->getRowDimension()->setRowHeight(-1);
                            $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->getStyle('F')->getNumberFormat()->setFormatCode('#');
                            $objPHPExcel->getActiveSheet()->getCell("A3")->setValue(' ');
                            $objPHPExcel->getActiveSheet()->getCell("B3")->setValue(' DIAS RESTANTES ');
                            $objPHPExcel->getActiveSheet()->getCell("C3")->setValue(' FECHA VENCIMIENTO ');
                            $objPHPExcel->getActiveSheet()->getCell("D3")->setValue(' CODIGO ICR ');
                            $objPHPExcel->getActiveSheet()->getCell("E3")->setValue(' PRODUCTO ');
                            $objPHPExcel->getActiveSheet()->getCell("F3")->setValue(' REFERENCIA ');
                            $objPHPExcel->getActiveSheet()->getCell("G3")->setValue(' LABORATORIO/FABRICANTE ');
                            $objPHPExcel->getActiveSheet()->getCell("H3")->setValue(' PROVEEDORES ');
                            $objPHPExcel->getActiveSheet()->getCell("I3")->setValue(' BODEGA ');
                            $objPHPExcel->getActiveSheet()->getCell("J3")->setValue(' FECHA_REGISTRO_ICR ');
                            $objPHPExcel->getActiveSheet()->getCell("K3")->setValue(' RE ');
                            $objPHPExcel->getActiveSheet()->getCell("L3")->setValue(' OBSERVACIONES ');

                            $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->applyFromArray($style);
                            $objPHPExcel->getActiveSheet()->getStyle('A3:L3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

                            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(3.5);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(17);

                            $objPHPExcel->getActiveSheet()->setTitle($sheet_name);

                        }

                        $line = $sheet_reg + 4;
                        $regshow++;
                        $objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $regshow );
                        $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $dat_print['dias_para_vencer']);
                        $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, $dat_print['fecha_vencimiento']);
                        $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $dat_print['cod_icr']);
                        $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, $dat_print['name']);
                        $objPHPExcel->getActiveSheet()->setCellValue('F'.$line, $dat_print['reference']);
                        $objPHPExcel->getActiveSheet()->setCellValue('G'.$line, $dat_print['laboratorio']);
                        $objPHPExcel->getActiveSheet()->setCellValue('H'.$line, $dat_print['proveedores']);
                        $objPHPExcel->getActiveSheet()->setCellValue('I'.$line, $dat_print['Bodega']);
                        $objPHPExcel->getActiveSheet()->setCellValue('J'.$line, $dat_print['fecha']);
                        $objPHPExcel->getActiveSheet()->setCellValue('K'.$line, ' ');
                        $objPHPExcel->getActiveSheet()->setCellValue('L'.$line, ' ');

                        if ( $dat_print['dias_para_vencer'] < 8 ) {
                            $objPHPExcel->getActiveSheet()
                                ->getStyle('A'.$line.':L'.$line)
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB('00FF4444');
                        } elseif ( $dat_print['dias_para_vencer'] >= 8 && $dat_print['dias_para_vencer'] <= 16 ) {
                            $objPHPExcel->getActiveSheet()
                                ->getStyle('A'.$line.':L'.$line)
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB('00FFDE00');
                        }

                        $sheet_reg ++;

                    }
                    $sheet_number++;

                    // COMENTARIO PARA LA ULTIMA HOJA
                    //$row = $objPHPExcel->getActiveSheet()->getHighestRow()+2;
                    //$objPHPExcel->getActiveSheet()->getCell('A'.$row)->setValue('Convenciones AP Aprobado, RE Rechazado');
                    //$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(8);

                    // Redirect output to a client’s web browser (Excel5)
                    

                } 

            }
            
            $objPHPExcel->setActiveSheetIndex(0);

                    //header('Content-Type: application/vnd.ms-excel');
                    //header('Content-Disposition: attachment;filename="Fecha_Vencimiento_ICR.xls"');
                    //header('Cache-Control: max-age=0');
                    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                    //$objWriter->save('php://output');
                    //
                    //
                    //
                    
@ob_start();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
$writer->save("php://output");
$data = @ob_get_contents();
@ob_end_clean(); 

$fileAttachment['content'] = $data;
$fileAttachment['name'] = "Reporte_ICR_Mexico.xls";
$fileAttachment['mime'] = "application/vnd.ms-excel";

$results = Db::getInstance()->ExecuteS("SELECT * FROM ps_alerta_icr_correo WHERE activo = 1");

foreach ($results as $key => $value) {
    $correos[] = $value['mail'];
    $nombres[] = utf8_encode( $value['nombre'] );
}

//$correos = array("sandy.mesa@farmalisto.com.co", "guillermo.rueda@farmalisto.com.co", "sebastian.mora@farmalisto.com.co", "ewing.vasquez@farmalisto.com.co", "luis.quinones@farmalisto.com");
/*
echo "<pre>mails: ";
print_r($correos);
print_r($nombres);
exit;
*/

if ( Mail::Send(1, 'icr', 'Reporte automático generado de ICRs - Fechas de Vencimiento México', array(), $correos, $nombres, "info@farmalisto.com.co", "Farmalisto icrs's México", $fileAttachment) ) {

    echo "\r\n".date("Y-m-d")." ".date("H:i:s")." REPORTE ENVIADO CORRECTAMENTE A: ". implode(", ", $correos);
} else {
    echo "\r\n".date("Y-m-d")." ".date("H:i:s")." REPORTE NOOOOOOOOOOOOOOOOOOO ENVIADO";
}

?>