<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
//require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

    //Id del carro para asociar el Medico
    $context = Context::getContext();
    $id_cart = $context->cart->id;
    
    //Consulta del autocomplete
//    $medico = Tools::getValue('term');
    $medico = $_POST["medico"];
    error_log("\n\n El medico: ".$medico,3,"/tmp/errorcito.log");
    
    //Medico seleccionado.
    $id_medico = $_POST["id_medico"];
 
    
    $servierMedico = new servierMedicos();
    
    if( $id_medico ){
        $result = $servierMedico->insertMedico( $id_medico, $id_cart );
        echo json_encode(
            $result
        );
    }
    
    else {
        //Consulta los medicos por nombre y apellido
        $result = $servierMedico->searchByNameMed( str_replace(" ", "%", $medico) );
        if ( isset($result) && $result != NULL ){
            //Los concatena para mostarlos en pantalla correctamente
            $ret = $servierMedico->concatNamesMedico($result);
            //los retorna.
            echo $ret;
        }
        else{
            echo json_encode(
                array(
                    'success'=>false, 
                    'mesage'=>'No hay resultados',
                    'resultado'=> $ret,
                ) 
            );
        }
    }
     
    
?>