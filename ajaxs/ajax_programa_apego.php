<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

    $nombre_apego = Tools::getValue('nombre_apego');
    $access_value = Tools::getValue('access_value');
    $ProgramaApego = new ProgramaApego();
    $result = $ProgramaApego->subscribeCirculoSalud($nombre_apego, $access_value);
    
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

