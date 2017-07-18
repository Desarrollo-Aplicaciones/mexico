<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
    include(dirname(__FILE__) . '/../config/config.inc.php');
    include(dirname(__FILE__) . '/../init.php');
    header('Content-Type: application/json');
    $name_registry = Tools::getValue('name');
    $email_registry = Tools::getValue('email');
    $telefono_registry = Tools::getValue('telefono');
    $id_product_registry = Tools::getValue('product');
    $sql = "SELECT cr.*, rgl.state, rgl.id_product FROM ps_customer_registry cr "
        . "LEFT JOIN ps_registry_product_list rgl ON (rgl.id_registry = cr.id_registry AND rgl.state = 1 AND rgl.id_product = ".$id_product_registry.") "
        . "WHERE cr.email_registry = '".$email_registry."';";
    $result = Db::getInstance()->ExecuteS($sql);
    /*print_r($result);
    print_r($sql); 
    */
    if ( count($result) == 0 ) {
        $resultInsert = Db::getInstance()->Execute("INSERT INTO `". _DB_PREFIX_ ."customer_registry` (`name_registry`, `email_registry`, `phone_registry`) VALUES ( '".$name_registry."', '".$email_registry."', '".$telefono_registry."')");
        if($resultInsert) {
            $id_registry = Db::getInstance()->Insert_ID();   
        } else {
            echo json_encode(['success' => false]);
            exit;
        }
    } else {
        $id_registry = $result[0]['id_registry'];
    }
    
    if($result[0]['id_product'] != $id_product_registry) {
        $product_list = Db::getInstance()->Execute("INSERT INTO `". _DB_PREFIX_ ."registry_product_list` (`id_product`,`id_registry`,`date_registry`) VALUES ( '".$id_product_registry."','".$id_registry."','".date("Y-m-d")."')");
        if($product_list) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => true]);
    }