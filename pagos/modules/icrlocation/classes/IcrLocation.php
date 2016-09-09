<?php

class IcrLocationCore extends ObjectModel
{
	/** @var string identifier of the icr location */
	public $id;
	
	/** @var boolean string Status */
	public $active = 'Y';
	
	public $creation_date;
	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'icr_location',
		'primary' => 'id_icr_location',
		'fields' => array(
			'active' => 				array('type' => self::TYPE_STRING),
			'creation_date' => 	array('type' => self::TYPE_DATE),
		),
	);

	/**
	 * Obtiene todas las ubicaciones activas, si no hay filtro de ubicacion
	 *
	 * @param string  ID ubicación
	 * @return array (id, active, creation_date)
	 */
	public static function getLocation($location = '')
	{
		$query = new DbQuery();
		$query->select('icrl.id_icr_location, icrl.active');
		$query->from('icr_location', 'icrl');
		$query->where('active = "Y"');
		if( $location )
			$query->where('id_icr_location = "' . pSQL($location) . '"');

		if( $results = Db::getInstance()->executeS($query) )
			return $results;

		return array();
	}

	/**
	 * Agrega una nueva ubicacion
	 *
	 * @param string  ID ubicación
	 * @return bool
	 */
	public static function setLocation($location = '')
	{
		$getLocation = self::getLocation($location);
		if( !empty( $getLocation ) ) 
			return true;

		$query = array(
			'table' => 'icr_location',
			'data' => array(
				'id_icr_location' => pSQL($location),
				'creation_date' => date("Y-m-d H:i:s")
			)
		);

		return Db::getInstance()->insert($query['table'], $query['data']);
	}

}