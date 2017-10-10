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
    case !(Validate::isName($name_contributor)) || (empty($name_contributor)):
        $result['form'][] = [
            'input' => 'nombre',
            'message' => 'Ingrese su nombre'
        ];
    case !(Validate::isName($company_contributor)) || (empty($company_contributor)):
        $result ['form'][] = [
            'input' => 'empresa',
            'message' => 'Ingrese el nombre de la empresa'
        ];
    case !(Validate::isEmail($email_contributor)) || (empty($email_contributor)):
        $result['form'][] = [
            'input' => 'email',
            'message' => 'Ingrese su e-mail'
        ];
    case !(Validate::isPhoneNumber($telefono_contributor)) || (empty($telefono_contributor)):
        $result['form'][] = [
            'input' => 'telefono',
            'message' => 'Ingrese su número teléfonico'
        ];
    case (Validate::isPostCode($codpostal_contributor)) || (empty($codpostal_contributor)):
        $result['form'][] = [
            'input' => 'codigpostal',
            'message' => 'Ingrese su código postal'
        ];
        break;
}

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

if (count($result['form']) > 0) {
  exit(json_encode($result));
}

$sheet = array();

foreach ($products as $key => $product) {
  $product["exist"] = "No Disponible";  

  $sql = 'SELECT prod.id_product, prod.reference, prol.`name` 
          FROM '._DB_PREFIX_.'product AS prod
          INNER JOIN '._DB_PREFIX_.'product_lang AS prol 
          ON ( prod.id_product = prol.id_product)
          WHERE  prod.id_product  = '.(int)$product["cod"];

  if ($row = Db::getInstance()->getRow($sql)) {
    $product["exist"] = "Disponible";
    $products[$key] = array_merge($product, $row);
  } 
  
  $sheet[] = array(    
    'Nombre' => $name_contributor,
    'Empresa' => $company_contributor,
    'Email' => $email_contributor,
    'Teléfono' => $telefono_contributor,
    'Código postal' => $codpostal_contributor,
    'Id producto' => $products[$key]['cod'],
    'Referencia' => $products[$key]['reference'],
    'Producto' => $products[$key]['name'],
    'Cantidad' => $products[$key]['qty'],
    'Disponibilidad' => $products[$key]['exist']
  );

}
$objPHPExcel = new PHPExcel();
$sheet_number = 0;
$sheet_name = '';
$sheet_reg = 0;

$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex($sheet_number);
$objPHPExcel->getActiveSheet()->setTitle("Cotizacion al por mayor");

$objPHPExcel->getActiveSheet()->fromArray(array_keys($sheet[0]),NULL,'A1'); //Cabecera de la hoja
$objPHPExcel->getActiveSheet()->fromArray($sheet, null, 'A2'); // Datos recibidos

$style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(34);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(45);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(20);

$sheet_reg ++;


@ob_start();
$writer = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
$writer->save("php://output");    
$data = @ob_get_contents();
@ob_end_clean();  
$fileAttachment['content'] = $data;
$fileAttachment['name'] = "ventas_por_mayoreo.xls";
$fileAttachment['mime'] = "application/vnd.ms-excel";

//Parametros para enviar el e-mail
$sendMail = Mail::Send(
  1, 
  'cotizaciones', 
  'cotizaciones por mayoreo', 
  array(), 
  ['leidy.castiblanco@farmalisto.com.co'],
  null, 
  null, 
  null, 
  $fileAttachment, 
  null, 
  _PS_MAIL_DIR_, 
  false, 
  null, 
  null,
  false,
  ''
);
  
if ($sendMail) {
  exit(json_encode(array(
    'success' => true, 
    'message' => 'Su cotización ha sido enviada'
  ))); 
}

exit(json_encode(array(
  'success' => false, 
  'message' => 'Lo sentimos, ocurrio un error, no se pudo envíar el correo.'
)));
