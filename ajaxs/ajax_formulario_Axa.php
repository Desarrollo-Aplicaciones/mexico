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

$nombreasegurado = Tools::getValue("nombre");
$email_prov = Tools::getValue("email");
$telefono_prov = Tools::getValue("telefono");
$num_auto = Tools::getValue("num_auto");
$coaseg = Tools::getValue("coaseg");
$products = Tools::getValue("products");
/* 
products[0][cod]
products[0][qty]
*/
$otros = Tools::getValue("otros");

/*
$num_poliza = Tools::getValue("num_poliza");
$num_sini = Tools::getValue("num_sini");
$num_auto = Tools::getValue("num_auto");
$nombre_prov = Tools::getValue("nombre");
$direccion_prov = Tools::getValue("direccion");
$telefono_prov = Tools::getValue("telefono");
$email_prov = Tools::getValue("email");
$products = Tools::getValue("products");
*/
/*
echo "POST\r\n<hr>";
print_r($_POST);
echo "FILES\r\n<hr>";
print_r($_FILES);
echo "Attachment<hr>";
print_r($_FILES["_attachments"]);
echo "\r\n<hr>";

echo " ---".$check = getimagesize($_FILES["_attachments"]["tmp_name"]);
*/

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$fileok = 0;

if ( isset($_FILES) && isset($_FILES["_attachments"]) && $_FILES["_attachments"]["error"] == 0 ) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $_FILES['_attachments']['tmp_name']);

    if ( $mime != "application/pdf" && $mime != "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" &&  $mime != "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ) {
        echo json_encode('El archivo adjunto no tiene un formato Valido <br>( .pdf, .doc, .xlsx, .docx) ');
        exit;
    } else { 
      $fileok = 1;
    }
}




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
  $objPHPExcel->getActiveSheet()->getCell("A1")->setValue(' Nombre del Asegurado ');
  $objPHPExcel->getActiveSheet()->getCell("A2")->setValue(' Email ');
  $objPHPExcel->getActiveSheet()->getCell("A3")->setValue(' Teléfono ');
  $objPHPExcel->getActiveSheet()->getCell("A4")->setValue(' Número de Autorización ');
  $objPHPExcel->getActiveSheet()->getCell("A5")->setValue(' Coaseguro ');
  $objPHPExcel->getActiveSheet()->getCell("A6")->setValue(' Otros productos autorizados ');
 /* $objPHPExcel->getActiveSheet()->getCell("A5")->setValue('Dirección ');
  $objPHPExcel->getActiveSheet()->getCell("A6")->setValue('Email ');
  $objPHPExcel->getActiveSheet()->getCell("A7")->setValue('Teléfono ');
  */
  $objPHPExcel->getActiveSheet()->getCell("A8")->setValue('cod Producto ');
  $objPHPExcel->getActiveSheet()->getCell("B8")->setValue('Cantidad ');

  //$objPHPExcel->getActiveSheet()->getCell("c8")->setValue('Cantidad ');  
  
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(33);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);
  //$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
  $objPHPExcel->getActiveSheet()->setTitle($sheet_name);  

  $objPHPExcel->getActiveSheet()->setCellValue('B1', $nombreasegurado);
  $objPHPExcel->getActiveSheet()->setCellValue('B2', $email_prov);
  $objPHPExcel->getActiveSheet()->setCellValue('B3', $telefono_prov);
  $objPHPExcel->getActiveSheet()->setCellValueExplicit('B4', $num_auto, PHPExcel_Cell_DataType::TYPE_STRING);

  $objPHPExcel->getActiveSheet()->setCellValue('B5', $coaseg);
  $objPHPExcel->getActiveSheet()->setCellValue('B6', $otros);
  /*
  $objPHPExcel->getActiveSheet()->setCellValue('B5', $direccion_prov);
  $objPHPExcel->getActiveSheet()->setCellValue('B6', $email_prov);
  $objPHPExcel->getActiveSheet()->setCellValue('B7', $telefono_prov);
*/


 /*
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
  */


  $line = 9;
   
 foreach ( $products as $product_pr) {  
 //print_r($dataprov);
  //$objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $product_pr['cod'] );
  $objPHPExcel->getActiveSheet()->setCellValueExplicit('A'.$line, $product_pr['cod'], PHPExcel_Cell_DataType::TYPE_STRING);
  $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $product_pr['qty']);
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
    $body             = '<b>¡Nueva Solicitud Medicamentos AXA!'
                          . '<br> Revisar adjunto</b>'; 

    $mail->IsSMTP();                           // tell the class to use SMTP
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->Port       = 587;                    // set the SMTP server port
    $mail->Host       = "smtp.gmail.com"; // SMTP server
    $mail->Username   = "socialmedia@farmalisto.com.co";     // SMTP server username
    $mail->Password   = "Soporte2017";//f4rm4l1st0";            // SMTP server password


    $mail->IsSendmail(); 

    $mail->AddReplyTo("socialmedia@farmalisto.com.co");
    $mail->AddCC("jessica.radilla@farmalisto.com.mx");

    $mail->From       = "socialmedia@farmalisto.com.co";
    $mail->FromName   = "Farmalisto Mexico";

    $to = "ventasmayoreo@farmalisto.com.mx";
    //$to = "ewing.vasquez@farmalisto.com.co";

    $mail->AddAddress($to);

    $mail->Subject  = 'Formulario AXA - '.$email_prov;

    $mail->AltBody    = ""; // optional, comment out and test
    $mail->AddAttachment("cotizaciones_axa.xls", "solicitd_ventas_por_mayoreo_axa.xls");
       $mail->WordWrap   = 80; // set word wrap
    if( $fileok == 1 ) {
      $mail->AddAttachment($_FILES["_attachments"]["tmp_name"] , $_FILES["_attachments"]["name"]);
    }
    $mail->MsgHTML($body);

    $mail->IsHTML(false); // send as HTML

    $mail->Send();

    echo json_encode('Su Solicitud ha sido enviada.');

    } catch (phpmailerException $e) {
    echo json_encode( $e->errorMessage() );
}  
