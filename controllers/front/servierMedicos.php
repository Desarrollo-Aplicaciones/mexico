<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class servierMedicosCore extends ObjectModel {
    public $id;   
    
    public static $definition = array(
		'table' => 'medic_servier',
		'primary' => 'id_servier',
		'fields' => array(),
	);
    
    
    
    
    
    public function searchByNameMed( $name_med ){
        $sql = "SELECT id_servier, nombres, apellidos
                FROM ps_medic_servier
                WHERE UPPER(CONCAT(`nombres`, ' ', `apellidos`)) LIKE UPPER('%".$name_med."%')
                LIMIT 5;";
        $result =  Db::getInstance()->executeS($sql);
        
        return $result;
    }
    
    public function concatNamesMedico( $result ) {
//        $ret = array();
//        foreach( $result as $key => $value ) {
//            $ret[] = array(
//                'id' => $value["id_servier"],
//                'label' => trim($value["nombres"]) . " " . trim($value["apellidos"]),
//                'value' => trim($value["nombres"]) . " " . trim($value["apellidos"]),
//            );
//        }
//        return $ret;
        
        $html = '<ul id="country-list">';
        foreach( $result as $key => $value ) {
            $nameComplete = trim($value["nombres"]) . " " . trim($value["apellidos"]);
            $html .= '<li onClick="selectOption(\''.$nameComplete.'\',\''.$value["id_servier"].'\');" value="'.$value["id_servier"].'">'.$nameComplete.'<li>';
        }
        $html .= '</ul>';
        //error_log("\n\n El html: ".$html,3,"/tmp/errorcito.log");
        return $html;
        
    }

    public function insertMedico( $id_medico, $id_cart ) {
        
        if ( (int)$id_cart != 0 ) {
            
            if( $this->consutarRegistroFromIdCart($id_cart) > 0 ){
                $result = Db::getInstance()->update('servier_medicos_cart', array(
                    'id_medico' => pSQL($id_medico),
                ), 'id_cart = '.(int)$id_cart);
                //echo "return 1: ".$result;
                error_log("\n insertMedico update id_medico:".pSQL($id_medico)."- id_cart:".(int)$id_cart." | ".$result,3,"/tmp/errorcito.log");
                return "Medico Servier actualizado en el carrito".(int)$id_cart." - ".$result; 
            }
            else {
                $result = Db::getInstance()->insert('servier_medicos_cart', array(
                    'id_medico' => pSQL($id_medico),
                    'id_cart'      => (int)$id_cart,
                ));
                error_log("\n insertMedico insert id_medico:".pSQL($id_medico)."- id_cart:".(int)$id_cart." | ".$result,3,"/tmp/errorcito.log");
                return "Medico Servier ingresado en el carrito".(int)$id_cart." - ".$result; 
            }
            
        } else {
            return "No fue posible asociar el médico en el carrito ".$id_cart.", para este cliente."; 
        }
               
    }
    
    public function consutarRegistroFromIdCart( $id_cart ){
        $sql = "SELECT COUNT(id_cart) FROM ps_servier_medicos_cart WHERE id_cart = ".$id_cart.";";
        $result =  Db::getInstance()->getValue($sql);
        return $result;        
    }
}