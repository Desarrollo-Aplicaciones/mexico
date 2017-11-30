<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$path = dirname(__FILE__);
require($path.'/../config/config.inc.php');
include($path.'/../init.php');
include_once($path."/../tools/phpexcel/PHPExcel.php");
require_once $path."/../tools/phpexcel/PHPExcel/IOFactory.php";

$num_poliza = Tools::getValue("num_poliza");
$num_sini = Tools::getValue("num_sini");
$num_auto = Tools::getValue("num_auto");
$nombre_prov = Tools::getValue("nombre");
$direccion_prov = Tools::getValue("direccion");
$telefono_prov = Tools::getValue("telefono");
$email_prov = Tools::getValue("email");
$products = Tools::getValue("products");
 

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$objPHPExcel = new PHPExcel();
$sheet_number = 0;
$sheet_name = '';
$sheet_reg = 0;

$results = array();
/*$results = array(
 'num_poliza' => $num_poliza,
 'num_sini' => $num_sini,
 'num_auto'=> $num_auto,
 'nombre_prov' => $nombre_prov,
 'direccion'=> $direccion_prov,
 'telefono' => $telefono_prov,
 'email' => $email_prov  
);*/

  $objPHPExcel->createSheet();
  $objPHPExcel->setActiveSheetIndex($sheet_number);
  $objPHPExcel->getActiveSheet()->setTitle("Cotización axa");
  
  $style = array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'bold' => true
          )
  );
 
  $objPHPExcel->getActiveSheet()->getStyle('A1:A6')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->applyFromArray($style);
  $objPHPExcel->getActiveSheet()->getCell("A1")->setValue(' Número Póliza ');
  $objPHPExcel->getActiveSheet()->getCell("A2")->setValue(' Número Siniestro ');
  $objPHPExcel->getActiveSheet()->getCell("A3")->setValue(' Número Autorización ');
  $objPHPExcel->getActiveSheet()->getCell("A4")->setValue(' Nombre ');
  $objPHPExcel->getActiveSheet()->getCell("A5")->setValue('Dirección ');
  $objPHPExcel->getActiveSheet()->getCell("A6")->setValue('Email ');
  $objPHPExcel->getActiveSheet()->getCell("A7")->setValue('Teléfono ');
  
  $objPHPExcel->getActiveSheet()->getCell("A8")->setValue('Producto ');
  $objPHPExcel->getActiveSheet()->getCell("B8")->setValue('Id_producto ');
  $objPHPExcel->getActiveSheet()->getCell("c8")->setValue('Cantidad ');  
  
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(33);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
  $objPHPExcel->getActiveSheet()->setTitle($sheet_name);  

  $objPHPExcel->getActiveSheet()->setCellValue('B1', $num_poliza);
  $objPHPExcel->getActiveSheet()->setCellValue('B2', $num_sini);
  $objPHPExcel->getActiveSheet()->setCellValue('B3', $num_auto);
  $objPHPExcel->getActiveSheet()->setCellValue('B4', $nombre_prov);
  $objPHPExcel->getActiveSheet()->setCellValue('B5', $direccion_prov);
  $objPHPExcel->getActiveSheet()->setCellValue('B6', $email_prov);
  $objPHPExcel->getActiveSheet()->setCellValue('B7', $telefono_prov);
  $line = 7;
  
 
 foreach ( $product as $product_pr) {  
 //print_r($dataprov);
  $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $product_pr['cod']);
  $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $product_pr['qty']);
  $line ++;
 }  
  
  
  echo 'hola';
$sheet_reg ++;
$objPHPExcel->setActiveSheetIndex(0);
  echo 'hola1';
@ob_start();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
 echo 'hola2';
$writer->save('cotizaciones_axa.xls');    

$data = @ob_get_contents();
@ob_end_clean();  
$fileAttachment['content'] = $data;
$fileAttachment['name'] = "cotizaciones_axa.xls";
$fileAttachment['mime'] = "application/vnd.ms-excel";


$sendMail = Mail::Send(
    1,
    'cotizaciones',
    'cotizaciones axa',
    array(),
    ['leidy.castiblanco@farmalisto.com.co'],
    null,
    null,
    '',
    $fileAttachment,
    null,
    _PS_MAIL_DIR_,
    false,
    null,
    null,
    false,
    '');



if ($sendMail) {
    echo json_encode(array(
        'success' => true, 
        'message' => 'Su cotización ha sido enviada'
    ));
    exit(); 
}  else {
    echo json_encode(array(
        'success' => false, 
        'message' => 'Lo sentimos, no se pudo envíar el correo.'
    ));
    exit();
}