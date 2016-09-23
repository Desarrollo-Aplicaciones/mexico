<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class ProgApego {
    
    public $id_customer;
    public $id_prog_apego;
    public $name_apego = 'Prueba';
    public $priority = 1;
    public $status_apego = true;
    
    public static $definition = array(
		'table' => 'ps_prog_apego',
		'primary' => 'id_prog_apego',
		'fields' => array(
			'name_apego'   => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'priority'     => array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
			'status_apego' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'required' => true),
		),
	);
    
    public function add($autodate = true, $nullValues = false)
	{
		$result = parent::add($autodate, $nullValues);
		return $result;
	}
}