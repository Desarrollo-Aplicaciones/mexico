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
		'fields' => array(
 		),
	);
    
    public function searchByNameMed( $name_med ){
        $sql = "SELECT id_servier, nombres, apellidos
                FROM ps_medic_servier
                WHERE nombres LIKE '%".$name_med."%' OR apellidos LIKE '%".$name_med."%'
                LIMIT 10;";
        $result =  Db::getInstance()->executeS($sql);
        
        return $result;
    }
    
    public function explodeMedico( $medico ) {
        $result = null;
        $name_med = explode(" ", $medico);
        
        foreach ( $name_med as $buscar ) {
            if( isset($result) && $result != null ){
                $result += $this->searchByNameMed( $buscar );
            }
            else {
                $result = $this->searchByNameMed( $buscar );
            }
        }
        return $result;
    }
    
    public function insertMedico( $medico, $id_cart ){
        $sql = "INSERT INTO ps_servier_medicos_new (`nombre_medico`, `id_cart`) VALUES ('".$medico."', ".$id_cart.")" ;
        
        $result =  Db::getInstance()->execute($sql);
        
        return $result;
    }
}