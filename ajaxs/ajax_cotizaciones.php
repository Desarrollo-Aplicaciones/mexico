<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

//echo "<pre>";
//print_r($_POST);
//echo "<hr>";

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
    echo json_encode($result); 

if (!count($result['form'])) {
    $result = array(
        'success' => true, 
        'message' => 'Su cotización ha sido enviada'
    );
} else {
    exit(); 
}
$html_products = "";
foreach ($products as $product) {
    echo $product["cod"] . " - " . $product["qty"] . "<br>";
    $html_products .= "<tr><td>" . $product["cod"] . "</td><td>" . $product["qty"] . "</td></tr>";
}

$mail_params = array(
    '{nombre}' => $name_contributor,
    '{empresa}' => $company_contributor,
    '{email}' => $email_contributor,
    '{telefono}' => $telefono_contributor,
    '{codigpostal}' => $codpostal_contributor,
    '{productos}' => $html_products,
);

$sendMail = Mail::Send(
                Context::getContext()->language->id, 'cotizaciones', // this is file name here the file name is test.html
                "Cotización Ventas por Mayoreo", $mail_params, array("leidy.castiblanco@farmalisto.com.co")
);

//var_dump($sendMail);
