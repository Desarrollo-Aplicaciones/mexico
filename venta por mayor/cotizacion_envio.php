<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

echo "\r\nentro\r\n";
$path = dirname(__FILE__);
echo "\r\nruta:".$path;
require($path.'/../config/config.inc.php');
include_once($path."/../tools/phpexcel/PHPExcel.php");
require_once $path."/../tools/phpexcel/PHPExcel/IOFactory.php"; 
echo "\r\nlibrerias"; 
date_default_timezone_set('America/Mexico_City');

$objPHPExcel = new PHPExcel();
$sheet_number = 0;
$sheet_reg = 0;
if(COUNT($resultsC) > 0) {
    echo "\r\nconsulta"; 
        
        $objPHPExcel->createSheet();
        
	foreach($resultsC AS $key) {
            if($key[''] == 1 || $key[''] == 10) {
			
                $objPHPExcel->setActiveSheetIndex($sheet_number);
                //$objPHPExcel->setActiveSheetIndex($sheet_number)->mergeCells('A1:G1');
                $style = array(
                    'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                echo "\r\nnombre: ".$key['name_registry']; 
            $objPHPExcel->getActiveSheet()->getCell("A1")->setValue(' NOMBRE ');
            $objPHPExcel->getActiveSheet()->getCell("B1")->setValue(' EMPRESA ');
            $objPHPExcel->getActiveSheet()->getCell("C1")->setValue(' E-MAIL ');
            $objPHPExcel->getActiveSheet()->getCell("D1")->setValue(' TELEFONO ');
            $objPHPExcel->getActiveSheet()->getCell("E1")->setValue('CODIGO POSTAL ');
            $objPHPExcel->getActiveSheet()->getCell("F1")->setValue('ID PRODUCTO ');
            $objPHPExcel->getActiveSheet()->getCell("G1")->setValue('CANTIDAD ');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            
            $line = $sheet_reg + 2;
            
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $key['nombre']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $key['empresa']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, $key['email']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $key['telefono']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, $key['codigpostal']);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$line, $key['products']);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$line, $key['number']);
            $objPHPExcel->getActiveSheet()
                        ->getStyle('A'.$line.':G'.$line)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('00FFDE00');
			
                        
                $sheet_reg ++;
            }
	}
	$objPHPExcel->setActiveSheetIndex(0);
	@ob_start();
	$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$writer->save("php://output");
	$data = @ob_get_contents();
	@ob_end_clean(); 
	$fileAttachment['content'] = $data;
	$fileAttachment['name'] = "ventas_por_mayoreo.xls";
	$fileAttachment['mime'] = "application/vnd.ms-excel";
	Mail::Send(1, 'cotizacion_envio', 'Clientes interesados', '', ['leidy.castiblanco@farmalisto.com.co'], '',
					null, null, $fileAttachment, null, _PS_MAIL_DIR_, false, 1);
        echo "\r\nEnvia"; 
}
    


