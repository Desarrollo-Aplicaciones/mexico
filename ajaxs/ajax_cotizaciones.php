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

$result = array(
    'success' => false, 
    'message' => 'Error al validar formulario',
    'form' => array()
);

// obteniendo  valores result 
switch (true) {
    case !(Validate::isName($name_contributor)) || (empty(Tools::getValue("nombre"))):
        $result['form'][] = [
            'input' => 'nombre',
            'message' => 'Ingrese su nombre'
        ];
    case !(Validate::isName($company_contributor)) || (empty(Tools::getValue("empresa"))):
        $result ['form'][] = [
            'input' => 'empresa',
            'message' => 'Ingrese el nombre de la empresa'
        ];
    case !(Validate::isEmail($email_contributor)) || (empty(Tools::getValue("email"))):
        $result['form'][] = [
            'input' => 'email',
            'message' => 'Ingrese su e-mail'
        ];
    case !(Validate::isPhoneNumber($telefono_contributor)) || (empty(Tools::getValue("telefono"))):
        $result['form'][] = [
            'input' => 'telefono',
            'message' => 'Ingrese su número teléfonico'
        ];
    case (Validate::isPostCode($codpostal_contributor)) || (empty(Tools::getValue("codigpostal"))):
        $result['form'][] = [
            'input' => 'codigpostal',
            'message' => 'Ingrese su código postal'
        ];
        break;
}

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');


if (count($result['form']) > 0) {
    echo json_encode($result);
    exit(); 
}
  
$objPHPExcel = new PHPExcel();
$sheet_number = 0;
$sheet_name = '';
$sheet_reg = 0;

$html_products = "";
$prodNotAvailable = array();
foreach ($products as $product) {
    $prod = new Product((int)$product['cod']);
    if (!isset($prod->id) || empty($prod->id)) {
        $prodNotAvailable[] = (int)$product['cod'];
    }
    $html_products .= "<tr><td>" . $product["cod"] . "</td><td>" . $product["qty"] . "</td></tr>";  
}
    $html_products .= "Productos no disponibles (ids):" . implode (", ", $prodNotAvailable);

$mail_params = array(
    '{nombre}' => $name_contributor,
    '{empresa}' => $company_contributor,
    '{email}' => $email_contributor,
    '{telefono}' => $telefono_contributor,
    '{codigpostal}' => $codpostal_contributor,
    '{productos}' => $html_products,
);

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex($sheet_number);
$objPHPExcel->getActiveSheet()->setTitle("Cotizacion al por mayor");
                         
$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
);
$objPHPExcel->getActiveSheet()->getCell("A1")->setValue(' NOMBRE ');
$objPHPExcel->getActiveSheet()->getCell("B1")->setValue(' EMPRESA ');
$objPHPExcel->getActiveSheet()->getCell("C1")->setValue(' E-MAIL ');
$objPHPExcel->getActiveSheet()->getCell("D1")->setValue(' TELÉFONO ');
$objPHPExcel->getActiveSheet()->getCell("E1")->setValue('CÓDIGO POSTAL ');
$objPHPExcel->getActiveSheet()->getCell("F1")->setValue('ID PRODUCTO ');
$objPHPExcel->getActiveSheet()->getCell("G1")->setValue('CANTIDAD ');
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

$objPHPExcel->getActiveSheet()->setTitle($sheet_name);
$line = $sheet_reg + 2;

$objPHPExcel->getActiveSheet()->setCellValue('A'.$line, $name_contributor);
$objPHPExcel->getActiveSheet()->setCellValue('B'.$line, $company_contributor);
$objPHPExcel->getActiveSheet()->setCellValue('C'.$line, $email_contributor);
$objPHPExcel->getActiveSheet()->setCellValue('D'.$line, $telefono_contributor);
$objPHPExcel->getActiveSheet()->setCellValue('E'.$line, $codpostal_contributor);
$line2 = 2;

foreach ($products as $value) {                          
    $objPHPExcel->getActiveSheet()->setCellValue('F'.$line2, $value['cod']);
    $objPHPExcel->getActiveSheet()->setCellValue('G'.$line2, $value['qty']);
    $line2 = $line + 1;
}
$sheet_reg ++;
$objPHPExcel->setActiveSheetIndex(0);

@ob_start();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
$writer->save("php://output");    
$data = @ob_get_contents();
@ob_end_clean();  
$fileAttachment['content'] = $data;
$fileAttachment['name'] = "ventas_por_mayoreo.xls";
$fileAttachment['mime'] = "application/vnd.ms-excel";
   
$sendMail = Mail::Send(1, 'cotizaciones', 'cotizaciones por mayoreo', $mail_params, ['leidy.castiblanco@farmalisto.com.co'],
					null, null, null, $fileAttachment, null, _PS_MAIL_DIR_, false, null,  null, false, '');
  
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
//var_dump($sendMail);


