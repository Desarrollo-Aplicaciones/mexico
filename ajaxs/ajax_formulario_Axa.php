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
  
$sheet_reg ++;
$objPHPExcel->setActiveSheetIndex(0);

@ob_start();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");

$writer->save('cotizaciones_axa.xls');    

$data = @ob_get_contents();
@ob_end_clean();  
$fileAttachment['content'] = $data;
$fileAttachment['name'] = "cotizaciones_axa.xls";
$fileAttachment['mime'] = "application/vnd.ms-excel";

include_once ($path."/../phpmailer/class.phpmailer.php");
include_once ($path."/../phpmailer/class.smtp.php");

try {
    $mail = new PHPMailer(true); //New instance, with exceptions enabled

    //$body             = file_get_contents('contents.html');
    $body             = '<b>¡Nueva Cotización Mayorista!'
                          . '<br> Revisar adjunto</b>'; 

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->Port       = 587;                    // set the SMTP server port
    $mail->Host       = "smtp.gmail.com"; // SMTP server
    $mail->Username   = "socialmedia@farmalisto.com.co";     // SMTP server username
    $mail->Password   = "f4rm4l1st0";            // SMTP server password


    $mail->IsSendmail(); 

    $mail->AddReplyTo("contacto@farmalisto.com.mx");
    $mail->AddCC("leidy.castiblanco@farmalisto.com.co");

    $mail->From       = "socialmedia@farmalisto.com.co";
    $mail->FromName   = "Farmalisto Mexico";

    $to = "ventasmayoreo@farmalisto.com.mx";

    $mail->AddAddress($to);

    $mail->Subject  = 'Formulario AXA'.$email_prov;

    $mail->AltBody    = ""; // optional, comment out and test
    $mail->AddAttachment("ventas_por_mayoreo.xls", "ventas_por_mayoreo.xls");
       $mail->WordWrap   = 80; // set word wrap

    $mail->MsgHTML($body);

    $mail->IsHTML(false); // send as HTML

    $mail->Send();

    echo json_encode('Su cotización ha sido enviada.');

    } catch (phpmailerException $e) {
    echo $e->errorMessage();
}  
