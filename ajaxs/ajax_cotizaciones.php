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
date_default_timezone_set('America/Bogota');

$name_contributor = Tools::getValue("nombre");
$company_contributor = Tools::getValue("empresa");
$email_contributor = Tools::getValue("email");
$telefono_contributor = Tools::getValue("telefono");
$codpostal_contributor = Tools::getValue("codigpostal");
$products = Tools::getValue("products");
  
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

$objPHPExcel = new PHPExcel();
$sheet_number = 0;
$sheet_name = '';
$sheet_reg = 0;

$prodcl = array();

foreach ($products as $key => $product) {
  $prodcl[ $product['cod'] ]['qty'] = $product['qty'];
  $prodcl[ $product['cod'] ]['Disponibilidad'] = 'No Disponible';

  }
$prodcot = implode(", ", array_keys($prodcl));

  $sql = 'SELECT prod.id_product, prod.reference, prol.`name` 
          FROM '._DB_PREFIX_.'product AS prod
          INNER JOIN '._DB_PREFIX_.'product_lang AS prol 
          ON ( prod.id_product = prol.id_product)
          WHERE  prod.id_product  IN ('.$prodcot.')';
          $results = Db::getInstance()->ExecuteS($sql);         
 
  foreach ($results as $dataprod){  

    if (isset($dataprod['id_product']) && !empty($dataprod['id_product'])) {
      $prodcl[ $dataprod['id_product']] = 
        [
          "reference" => $dataprod['reference'],
          "name" => $dataprod['name'],
          "qty" => $prodcl[ $dataprod['id_product']]['qty'],
          "Disponibilidad" => 'Disponible'
        ];
    } 
  }
  
  $objPHPExcel->createSheet();
  $objPHPExcel->setActiveSheetIndex($sheet_number);
  $objPHPExcel->getActiveSheet()->setTitle("Cotizacion al por mayor");
  
  $style = array(
          'alignment' => array(
              'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
              'bold' => true
          )
  );
 
  $objPHPExcel->getActiveSheet()->getStyle('A1:A6')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('B6:E6')->getFont()->setBold(true);
  $objPHPExcel->getActiveSheet()->getStyle('A6:E6')->applyFromArray($style);
  $objPHPExcel->getActiveSheet()->getCell("A1")->setValue(' Nombre ');
  $objPHPExcel->getActiveSheet()->getCell("A2")->setValue(' Empresa ');
  $objPHPExcel->getActiveSheet()->getCell("A3")->setValue(' E-mail ');
  $objPHPExcel->getActiveSheet()->getCell("A4")->setValue(' Teléfono ');
  $objPHPExcel->getActiveSheet()->getCell("A5")->setValue('Código postal ');
  
  $objPHPExcel->getActiveSheet()->getCell("A6")->setValue('Producto ');
  $objPHPExcel->getActiveSheet()->getCell("B6")->setValue('Id producto ');
  $objPHPExcel->getActiveSheet()->getCell("C6")->setValue('Referencia ');
  $objPHPExcel->getActiveSheet()->getCell("D6")->setValue('Cantidad ');
  $objPHPExcel->getActiveSheet()->getCell("E6")->setValue('Disponibilidad ');  
  
  $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
  $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);
  $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
  $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
  $objPHPExcel->getActiveSheet()->setTitle($sheet_name);  

  $objPHPExcel->getActiveSheet()->setCellValue('B1', $name_contributor);
  $objPHPExcel->getActiveSheet()->setCellValue('B2', $company_contributor);
  $objPHPExcel->getActiveSheet()->setCellValue('B3', $email_contributor);
  $objPHPExcel->getActiveSheet()->setCellValue('B4', $telefono_contributor);
  $objPHPExcel->getActiveSheet()->setCellValue('B5', $codpostal_contributor);
  $line = 7;
  
  foreach ($prodcl as $key => $product) {  
    $objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $product['name']);
    $objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $key);
    $objPHPExcel->getActiveSheet()->setCellValue('C'.$line, $product['reference']);
    $objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $product['qty']);
    $objPHPExcel->getActiveSheet()->setCellValue('E'.$line, $product['Disponibilidad']);
    $line ++;
  }
  
$sheet_reg ++;
$objPHPExcel->setActiveSheetIndex(0);
@ob_start();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
$writer->save('ventas_por_mayoreo.xls');    

$data = @ob_get_contents();
@ob_end_clean();  
$fileAttachment['content'] = $data;
$fileAttachment['name'] = "ventas_por_mayoreo.xls";
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

$mail->AddReplyTo("socialmedia@farmalisto.com.co");
$mail->AddCC("leidy.castiblanco@farmalisto.com.co");

$mail->From       = "socialmedia@farmalisto.com.co";
$mail->FromName   = "Farmalisto Mexico";

$to = "ventasmayoreo@farmalisto.com.mx";

$mail->AddAddress($to);

$mail->Subject  = "Cotizaciones por mayoreo";

$mail->AltBody    = ""; // optional, comment out and test
$mail->AddAttachment("ventas_por_mayoreo.xls", "ventas_por_mayoreo.xls");
   $mail->WordWrap   = 80; // set word wrap

$mail->MsgHTML($body);

$mail->IsHTML(false); // send as HTML

$mail->Send();

echo 'Su cotización ha sido enviada.';

} catch (phpmailerException $e) {
echo $e->errorMessage();
}  




/*
$sendMail = Mail::Send(
    1,
    'cotizaciones',
    'cotizaciones por mayoreo',
    array(),
    ['ventasmayoreo@farmalisto.com.mx','leidy.castiblanco@farmalisto.com.co'],
    null,
    null,
    'socialmedia@farmalisto.com.co',
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
}*/