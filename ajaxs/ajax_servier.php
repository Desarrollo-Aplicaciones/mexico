<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
//require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

    $context = Context::getContext();
    $id_cart = $context->cart->id;
    
    
    $buscar = Tools::getValue('term');
    
    $value = Tools::getValue('value');
    
    $servier = new Servier();
    
    if ( $value ){
        $result = $servier->validateIdCartOnServier( $id_cart, $value );
        echo json_encode(
            $result
        );
    }
    else {
        $registros = $servier->searchRep( $buscar );
        $result = $servier->setResultFront( $registros );
        if ( isset($result) && $result ){
            echo json_encode(
                $result
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
    }
    
    
    
?>