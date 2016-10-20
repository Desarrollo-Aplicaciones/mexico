<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ServierCore extends ObjectModel {
    public $id;
    public $id_asociado_servier;
    
    
    public static $definition = array(
		'table' => 'cart_asociado_servier',
		'primary' => 'id_cart',
		'fields' => array(
                    'id_cart'               => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
                    'id_asociado_servier'   => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
		),
	);
    
//    public function add($autodate = true, $nullValues = null){
//        echo "<br>Voy a agregar esto: ".(int)$this->id." - ".$this->id_asociado_servier;
//        $r = parent::add($autodate);
//        echo "<br>Se supone que ya agregó".$r."<br><br>";
//        return $r;
//    }
//        
//    public function update($null_values = false){
//            Cache::clean('getContextualValue_'.(int)$this->id.'_*');
//            return parent::update($null_values);	
//    }
//        
        
        
    
    public function validateIdCartOnServier( $id_cart, $id_rep ){
        $sql = "SELECT COUNT(id_cart) 
                FROM ps_cart_asociado_servier
                WHERE id_cart = '".$id_cart."'";
        $result = DB::getInstance()->getValue($sql);
        
        if( isset($result) && $result == 0 ){
            $return = $this->insertOnServier( $id_rep, (int)$id_cart );
        }
        elseif( $result >= 1 ){
            $return = $this->updateOnServier( $id_rep, (int)$id_cart );
        }
        return $return;
    }

    public function insertOnServier($id_rep, $id_cart){
        $sql = "INSERT INTO ps_cart_asociado_servier (id_cart, id_asociado_servier)
                VALUES (".(int)$id_cart.", '".$id_rep."')";
        $result =  Db::getInstance()->execute($sql);
        return $result;
    }
    
    public function updateOnServier($id_rep, $id_cart){
        $sql = "UPDATE ps_cart_asociado_servier 
                SET id_asociado_servier = '".$id_rep."'
                WHERE id_cart = ".(int)$id_cart;
        $result =  Db::getInstance()->execute($sql);
        return $result;
    }
    
    public function searchRep( $id ){
        $sql = "SELECT id_asociado
                FROM ps_asociado_servier
                WHERE id_asociado LIKE '".(String)$id."%' && estado = 1
                LIMIT 10;";
        $result = DB::getInstance()->executeS($sql);
        return $result;
    }
    
    public function setResultFront( $result ) {
        $ret = array();
        foreach( $result as $key => $value ) {
            $ret[] = array(
                'id' => $value["id_asociado"],
                'label' => trim($value["id_asociado"]),
                'value' => trim($value["id_asociado"]),
            );
        }
        return $ret;
    }
    
}