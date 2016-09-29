<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProgramaApegoCore extends ObjectModel {
    
    public $id_customer;
    public $name_apego;
    public $priority = 1;
    public $status_apego = true;
    
    public static $definition = array(
		'table' => 'prog_apego',
		'primary' => 'id_prog_apego',
		'fields' => array(
			'name_apego'   => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'priority'     => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
			'status_apego' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
		),
	);
    
    public function add($autodate = true, $nullValues = null)
	{
        echo "<br>Voy a agregar esto: ".$this->name_apego." - ".$this->priority." - ".$this->status_apego;
        $r = parent::add($autodate);
        echo "<br>Se supone que ya agregó".$r."<br><br>";
		return $r;
	}
    
    public function subscribeCirculoSalud($name_apego, $access_value){
        $this->context = Context::getContext();
        $this->id_customer = $this->context->customer->id;
        $this->name_apego = $name_apego;
        if(isset($this->id_customer)){
            /* @var $id_prod_apego INT */
            $id_prod_apego = $this->getIdProgApegoFromName( $name_apego );
            $result = $this->setProgApegoWithCustomer((int)$id_prod_apego, $this->id_customer, $access_value);

        }
        
    }
    
    public function getIdProgApegoFromName( $name_apego ) {
        $sql = "SELECT id_prog_apego FROM ps_prog_apego WHERE name_apego = '".$name_apego."'";

        $result = DB::getInstance()->getValue($sql);
        return $result;
    }
    
    public function setProgApegoWithCustomer($id_apego, $id_customer, $access_value) {
        $sql = "INSERT INTO ps_apego_customer (`id_prog_apego`, `id_customer`, `access_value` ) VALUES (".(int)$id_apego.", ".$id_customer.", '".$access_value."');";
        $result =  Db::getInstance()->execute($sql);
        return $result;
    }
    
    public function getAccesValueFromApegoCustomer($id_prog_apego, $id_customer) {
        $sql = "SELECT ps_apego_customer.access_value FROM ps_apego_customer WHERE ps_apego_customer.id_prog_apego=".$id_prog_apego." and ps_apego_customer.id_customer=".$id_customer.";";
        $result = DB::getInstance()->getValue($sql);
        return $result;
    }
    
    public function getNameProgApegoActive() {
        $sql = "SELECT name_apego FROM ps_prog_apego WHERE status_apego = 1 ORDER BY priority ASC;";
        $result = array(DB::getInstance()->executeS($sql));
        return $result[0];
    }
    
    public function getAccessValueFromNameApegoAndIdCustomer( $id_customer ){
        $sql = "SELECT ps_prog_apego.name_apego, ps_apego_customer.access_value
                FROM ps_prog_apego
                LEFT JOIN ps_apego_customer
                ON ps_prog_apego.id_prog_apego = ps_apego_customer.id_prog_apego and ps_apego_customer.id_customer = ".$id_customer."
                WHERE ps_prog_apego.status_apego = 1;";
        $result = array(DB::getInstance()->executeS($sql));
        return $result[0];
    }
}
