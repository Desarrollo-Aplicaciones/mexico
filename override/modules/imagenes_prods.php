<?php

set_time_limit ( 3602 );


include_once(dirname(__FILE__)."/../config/config.inc.php");


$dir_server = '/var/www/';

/*********************************** ICR YA VENCIDOS QUE ESTÁN EN BODEGA ************************************/

$query_imagenes =  "SELECT i.id_image, p.id_product, p.reference FROM ps_product p
LEFT JOIN ps_image i ON ( i.id_product = p.id_product )
WHERE p.active = 0
-- AND p.id_product = 454
ORDER BY p.id_product ASC ";


                    include_once(dirname(__FILE__)."/../tools/phpexcel/PHPExcel.php");
                    require_once dirname(__FILE__)."/../tools/phpexcel/PHPExcel/IOFactory.php"; 

                    $objPHPExcel = new PHPExcel();
                    $sheet_number = 0;


$tiposimg[] = '';
$tiposimg[] = 'home_default';
$tiposimg[] = 'large_default';
$tiposimg[] = 'medium_default';
$tiposimg[] = 'small_default';
$tiposimg[] = 'thickbox_default';


         //echo("<br>".$iteracion);

                if ($results = Db::getInstance_slv()->ExecuteS($query_imagenes)) {



                    
                    $sheet_name = '';
                    $sheet_reg = 0;
                    $regshow = 0;

                    


                            $objPHPExcel->createSheet();

                            
                            $sheet_name = "IMAGENES PRODUCTOS INACTIVOS";


                            // Create a first sheet, representing sales data
                            $objPHPExcel->setActiveSheetIndex($sheet_number);
                            $objPHPExcel->setActiveSheetIndex($sheet_number)->mergeCells('A1:F1');
                            
                            $style = array(
                                    'alignment' => array(
                                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                                    )
                                );

                            $objPHPExcel->getActiveSheet()->getCell("A1")->setValue('IMAGENES DE PRODUCTOS');
                            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($style);

                            $objPHPExcel->getActiveSheet()->getRowDimension()->setRowHeight(-1);
                            $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getAlignment()->setWrapText(true);
                            $objPHPExcel->getActiveSheet()->getStyle('C')->getNumberFormat()->setFormatCode('#');
                            $objPHPExcel->getActiveSheet()->getCell("A3")->setValue(' ');
                            $objPHPExcel->getActiveSheet()->getCell("B3")->setValue(' PRODUCT_ID ');
                            $objPHPExcel->getActiveSheet()->getCell("C3")->setValue(' REFERENCIA ');
                            $objPHPExcel->getActiveSheet()->getCell("D3")->setValue(' ID_IMAGEN ');
                            $objPHPExcel->getActiveSheet()->getCell("E3")->setValue(' ENCONTRADA ');
                            $objPHPExcel->getActiveSheet()->getCell("F3")->setValue(' TIPO IMAGEN ');/*
                            $objPHPExcel->getActiveSheet()->getCell("G3")->setValue(' LABORATORIO/FABRICANTE ');
                            $objPHPExcel->getActiveSheet()->getCell("H3")->setValue(' PROVEEDORES ');
                            $objPHPExcel->getActiveSheet()->getCell("I3")->setValue(' BODEGA ');
                            $objPHPExcel->getActiveSheet()->getCell("J3")->setValue(' FECHA_REGISTRO_ICR ');
                            $objPHPExcel->getActiveSheet()->getCell("K3")->setValue(' RE ');
                            $objPHPExcel->getActiveSheet()->getCell("L3")->setValue(' OBSERVACIONES ');*/

                            $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($style);
                            $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THICK);

                            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(12);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(50);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);/*
                            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(3.5);
                            $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(17);*/

                            $objPHPExcel->getActiveSheet()->setTitle($sheet_name);                       


                    foreach ($results as $dat_print) {                        
                        //echo "<br> dat_print id_product :".$dat_print['id_product'];
                        foreach ($tiposimg as $key_t => $value_t) {
                            
                            $imagen_encontrada = 0;                            
                            

                            //echo "<br> dat_print id_image :".$dat_print['id_image'];
                            
                            $caracteres = preg_split('//', $dat_print['id_image'], -1, PREG_SPLIT_NO_EMPTY);
                            //print_r($caracteres);
                            $compemento_ruta = implode("/", $caracteres);

                            if ( $value_t != '' ) {
                                $ruta_imagen_existe = $dir_server."img/p/".$compemento_ruta.'/'.$dat_print['id_image'].'-'.$value_t.".jpg";
                            } else {
                                $ruta_imagen_existe = $dir_server."img/p/".$compemento_ruta.'/'.$dat_print['id_image'].$value_t.".jpg";
                            }
                            

                            if ( !file_exists($ruta_imagen_existe) ) {
                                //echo "<br>".$ruta_imagen_existe;

                                //$query_del = "DELETE FROM ps_image WHERE id_image = ".$dat_print['id_image'];
                                //echo "<br> borrando: ".$dat_print['id_image']." - estado: ".$retornado = Db::getInstance()->Execute($query_del);
                                $line = $sheet_reg + 4;
                                $regshow++;
                                $objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $regshow );
                                $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $dat_print['id_product']);
                                $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, $dat_print['reference']);
                                $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $dat_print['id_image']);
                                $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, $imagen_encontrada);
                                $objPHPExcel->getActiveSheet()->setCellValue('F'.$line, $value_t);/*
                                $objPHPExcel->getActiveSheet()->setCellValue('G'.$line, $dat_print['laboratorio']);
                                $objPHPExcel->getActiveSheet()->setCellValue('H'.$line, $dat_print['proveedores']);
                                $objPHPExcel->getActiveSheet()->setCellValue('I'.$line, $dat_print['Bodega']);
                                $objPHPExcel->getActiveSheet()->setCellValue('J'.$line, $dat_print['fecha']);
                                $objPHPExcel->getActiveSheet()->setCellValue('K'.$line, ' ');
                                $objPHPExcel->getActiveSheet()->setCellValue('L'.$line, ' ');*/


                                //if ( $imagen_encontrada == 0 ) {
                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('A'.$line.':F'.$line)
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setARGB('00FF6666');
                                $sheet_reg ++;      
                            } /*else {

                                $line = $sheet_reg + 4;
                                $regshow++;
                                $objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $regshow );
                                $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $dat_print['id_product']);
                                $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, $dat_print['reference']);
                                $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $dat_print['id_image']);
                                $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, "SI");
                                $objPHPExcel->getActiveSheet()->setCellValue('F'.$line, $value_t);

                                $objPHPExcel->getActiveSheet()
                                    ->getStyle('A'.$line.':F'.$line)
                                    ->getFill()
                                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                    ->getStartColor()
                                    ->setARGB('0024984E');
                                $sheet_reg ++;

                            }*/

                            
                        }
                    }



                    // COMENTARIO PARA LA ULTIMA HOJA
                    //$row = $objPHPExcel->getActiveSheet()->getHighestRow()+2;
                    //$objPHPExcel->getActiveSheet()->getCell('A'.$row)->setValue('Convenciones AP Aprobado, RE Rechazado');
                    //$objPHPExcel->getActiveSheet()->getStyle('A'.$row)->getFont()->setSize(8);

                    // Redirect output to a client’s web browser (Excel5)
                    

                } 

            
                    //header('Content-Type: application/vnd.ms-excel');
                    //header('Content-Disposition: attachment;filename="productos_imagenes.xls"');
                    //header('Cache-Control: max-age=0');
                    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    //$objWriter->save('php://output');
                    
                    
                  
       
@ob_start();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
$writer->save("php://output");
$data = @ob_get_contents();
@ob_end_clean(); 

$fileAttachment['content'] = $data;
$fileAttachment['name'] = "Reporte_imagenes_Mexico.xls";
$fileAttachment['mime'] = "application/vnd.ms-excel";

 $correos = array(/*"eiver.gomez@farmalisto.com.co",*/ "ewing.vasquez@farmalisto.com.co"/*, "ricardo.ruiz@farmalisto.com.co"*/, "valentina.murillo@farmalisto.com.co");
/*
echo "<pre>mails: ";
print_r($correos);
print_r($nombres);
exit;
*/

if ( Mail::Send(1, 'log_alert', 'Reporte general de productos sin imágenes', array(), $correos, $nombres, "info@farmalisto.com.co", "Farmalisto imagenes", $fileAttachment) ) {

    echo "\r\n".date("Y-m-d")." ".date("H:i:s")." REPORTE ENVIADO CORRECTAMENTE A: ". implode(", ", $correos);
} else {
    echo "\r\n".date("Y-m-d")." ".date("H:i:s")." REPORTE NOOOOOOOOOOOOOOOOOOO ENVIADO";
}

?>