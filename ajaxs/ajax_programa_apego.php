<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

//    echo json_encode(array(
//        'success'=>true,
//        'mesage'=>'todo fine hasta aqui',
//        'nombre_apego'=>Tools::getValue('name_apego'),
//        'access_value'=>Tools::getValue('access_value'),
//    ));
    $nombre_apego = Tools::getValue('nombre_apego');
    $access_value = Tools::getValue('access_value');
    $ProgramaApego = new ProgramaApego();
    $result = $ProgramaApego->subscribeCirculoSalud($nombre_apego, $access_value);
//    var_dump($result);
    
    if ( isset($result) && $result ){
        echo json_encode(
            array(
                'success'=>true, 
                'mesage'=>'Todo Ok', 
                'resultado'=> $result,
            ) 
        );        
    }
    else{
        echo json_encode(
            array(
                'success'=>false, 
                'mesage'=>'Todo Paila',
                'resultado'=> $result,
            ) 
        );
    }

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

