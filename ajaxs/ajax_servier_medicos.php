<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
//require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

    $context = Context::getContext();
    $medico = Tools::getValue('medico');
    $id_cart = $context->cart->id;
    $servierMedico = new servierMedicos();
//    $result = $servierMedico->explodeMedico( $medico );
    $result = $servierMedico->insertMedico( $medico, $id_cart );
    
    
    if ( isset($result) && $result != NULL ){
//        foreach ($result as $value) {
//            echo '<div class="suggest-element"><a data="'.$value['nombres'].' '.$value['apellidos'].'" id="'.$value['id_servier'].'">'.utf8_encode($value['nombres']).' '.utf8_encode($value['apellidos']).'</a></div>';
//        }
    
        echo json_encode(
            array(
                'success'=>true, 
                'mesage'=>'Todo Ok', 
                'resultado'=> $result,
//                $result,
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
    
?>