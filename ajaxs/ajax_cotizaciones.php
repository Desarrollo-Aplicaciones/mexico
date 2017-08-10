<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
include(dirname(__FILE__) . '/../config/config.inc.php');
include(dirname(__FILE__) . '/../init.php');

echo "<pre>";
print_r($_POST); 
echo "<hr>";


$name_contributor =  Tools::getValue("nombre");
$company_contributor =  Tools::getValue("empresa");
$email_contributor =  Tools::getValue("email");
$telefono_contributor =  Tools::getValue("telefono");
$codpostal_contributor =  Tools::getValue("codigpostal");
$products = Tools::getValue("products");

echo "<hr>";

$html_products = "";
foreach ($products as $product) {
    echo $product["cod"] . " - " . $product["qty"] . "<br>";
    $html_products .= "<tr><td>".$product["cod"]."</td><td>".$product["qty"]."</td></tr>";
}

echo "<hr>";

$mail_params = array(
    '{nombre}' => $name_contributor,
    '{empresa}'=> $company_contributor,
    '{email}' => $email_contributor,
    '{telefono}' => $telefono_contributor,
    '{codigpostal}' => $codpostal_contributor,  
    '{productos}' => $html_products,  
);

$sendMail = Mail::Send(
    Context::getContext()->language->id,
    'cotizaciones', // this is file name here the file name is test.html
    "Cotización Ventas por Mayoreo",
    $mail_params,
    array("leidy.castiblanco@farmalisto.com.co")
);

var_dump($sendMail);

/*
if ($sendMail) {
    echo 'entro';
    return array(
      'success' => TRUE,
      'message' => 'Enviamos un mensaje al correo a ' .$email . ', de confirmación de devolución de pago.'
    );
} else {
     echo 'emtro2';
    return array(
      'success' => FALSE,
      'message' => 'Ocurrió un error al enviar el correo confirmación.'
    );
}
*/



