<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ServierCore extends ObjectModel {
    public $id;
    public $id_cart;
    public $id_asociado_servier;
    
    
    public static $definition = array(
		'table' => 'cart_asociado_servier',
		'primary' => 'id_cart',
		'fields' => array(
			'id_asociado_servier'   => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
		),
	);
    
    public function add($autodate = true, $nullValues = null){
        echo "<br>Voy a agregar esto: ".$this->id." - ".$this->id_asociado_servier;
        $r = parent::add($autodate);
        echo "<br>Se supone que ya agregó".$r."<br><br>";
        return $r;
    }
        
    public function update($null_values = false){
            Cache::clean('getContextualValue_'.$this->id.'_*');
            return parent::update($null_values);	
    }
        
        
        
    
    public function validateIdCartOnServier(){
        if( isset($this->id) && $this->id != Null ){
            $sql = 'SELECT COUNT(id_cart) 
                    FROM ps_cart_asociado_servier
                    WHERE id_cart = '.$this->id.';';
            $result = DB::getInstance()->getValue($sql);
            if( isset($result) && $result == 0 ){
                $return = $this->add();
            }
            elseif( $result >= 1 ){
                $return = $this->update();
            }
            else {}
        }
        else{
            $return = 'yucas';
        }
        return " ".$return;
    }
//
//    public function insertOnServier($id_rep, $id_cart){
//        $sql = 'INSERT INTO ps_cart_asociado_servier (id_cart, id_asociado_servier)
//                VALUES ('.$id_cart.', "'.$id_rep.'");';
//        $result =  Db::getInstance()->execute($sql);
//        return $result;
//    }
//    
//    public function updateOnServier($id_rep, $id_cart){
//        $sql = 'UPDATE ps_cart_asociado_servier 
//                SET id_asociado_servier = "'.$id_rep.'"
//                WHERE id_cart = '.$id_cart.';';
//        $result =  Db::getInstance()->execute($sql);
//        return $result;
//    }
    
    
}