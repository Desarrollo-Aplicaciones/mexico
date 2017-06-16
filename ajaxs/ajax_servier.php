<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
//require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');


     if ( $_POST["id_cart_ini"] != 0 ) {
        
        $id_cart = $_POST["id_cart_ini"];
        
    } else {       

        $context = Context::getContext();       

        if ( $_GET["printpantalla"] == '1' )
        {
            echo "<pre>REQUEST: <br>";
            print_r($_REQUEST);
            echo "<hr><br><br><hr>Context: <br>";
            print_r($context);
            echo '<hr><br><br><hr>CloneContext:<br>';
            print_r($contextClone);
            echo "</pre>";
        }

        $id_cart = $context->cart->id;
    }
    
    $servier = new Servier();
    //echo "<br>rep:".
    $id_rep = Tools::getValue('id_rep');
    
//    echo json_encode(array('id_rep'=>$id_rep, 'id_cart'=>$id_cart));
    
    $validateReg = $servier->validateReg($id_rep);
    
    if( $validateReg != 0 ){
        $result = $servier->validateIdCartOnServier( $id_cart, $id_rep );
    }
    
    
    if ( isset($result) && $result ){
        echo json_encode(
            array(
                'success'=>true, 
                'mesage'=>'Los resultados son', 
                'resultado'=> $result,
            ) 
        );        
    }
    else{
        echo json_encode(
            array(
                'success'=>false, 
                'mesage'=>'No hay resultados',
                'resultado'=> $result,
            ) 
        );
    }
?>