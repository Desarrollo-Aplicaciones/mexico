<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
//require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

    $context = Context::getContext();
    $servier = new Servier();
    echo "<br>rep:".
    $id_rep = Tools::getValue('id_rep');
    
    $id_cart = $context->cart->id;
//    echo json_encode(array('id_rep'=>$id_rep, 'id_cart'=>$id_cart));
    
    $validateReg = $servier->validateReg($id_rep);
    
    if( $validateReg != 0 ){
        $result = $servier->validateIdCartOnServier( $id_cart, $id_rep );
    }
    
    
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
?>