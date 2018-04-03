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
$sqlC = 'SELECT cr.*, p.reference, rpl.id_product_registry, pl.`name`, pbl.motivo FROM ps_customer_registry AS cr 
INNER JOIN ps_registry_product_list AS rpl ON (cr.id_registry=rpl.id_registry)
INNER JOIN ps_product AS p ON (p.id_product=rpl.id_product)
INNER JOIN ps_product_lang AS pl ON (pl.id_product=p.id_product)
LEFT JOIN ps_product_black_list AS pbl ON (pbl.id_product=pl.id_product)
WHERE rpl.state = 1 AND p.active=1';
$resultsC = Db::getInstance()->ExecuteS($sqlC); 
echo "\r\nejecuta query"; 
$objPHPExcel = new PHPExcel();
$sheet_number = 0;
$sheet_reg = 0;
if(COUNT($resultsC) > 0) {
    echo "\r\nconsulta"; 
        
        $objPHPExcel->createSheet();
        
	foreach($resultsC AS $key) {
            if($key['motivo'] == 1 || $key['motivo'] == 10) {
			
                $objPHPExcel->setActiveSheetIndex($sheet_number);
                //$objPHPExcel->setActiveSheetIndex($sheet_number)->mergeCells('A1:G1');
                $style = array(
                    'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                echo "\r\nnombre: ".$key['name_registry']; 
            $objPHPExcel->getActiveSheet()->getCell("A1")->setValue(' NOMBRE ');
            $objPHPExcel->getActiveSheet()->getCell("B1")->setValue(' E-MAIL ');
            $objPHPExcel->getActiveSheet()->getCell("C1")->setValue(' TELEFONO ');
            $objPHPExcel->getActiveSheet()->getCell("D1")->setValue(' REFERENCIA PRODUCTO ');
            $objPHPExcel->getActiveSheet()->getCell("E1")->setValue(' NOMBRE PRODUCTO ');
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(80);
            
            $line = $sheet_reg + 2;
            
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $key['name_registry']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $key['email_registry']);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, $key['phone_registry']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $key['reference']);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, $key['name']);
            $objPHPExcel->getActiveSheet()
                        ->getStyle('A'.$line.':E'.$line)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('00FFDE00');
			$sqlUpdate = 'UPDATE ps_registry_product_list SET state= 0 WHERE id_product_registry = '.$key['id_product_registry'];
			Db::getInstance()->Execute($sqlUpdate);
                        
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
	$fileAttachment['name'] = "Reporte_clientes_interesados.xls";
	$fileAttachment['mime'] = "application/vnd.ms-excel";
	$emails = json_decode(Configuration::get('MAILS_BLACK_LIST'),true);
	Mail::Send(1, 'send_mail_black_list', 'Clientes interesados', '', $emails, '',
					null, null, $fileAttachment, null, _PS_MAIL_DIR_, false, 1);
        echo "\r\nEnvia"; 
}