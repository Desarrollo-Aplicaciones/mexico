<?php

class IcrCore extends ObjectModel
{
	/** @var string identifier of the icr */
	public $id;
	
	/** @var  string  */
	public $cod_icr;
	
	public $id_historico;
	
	public $id_estado_icr;
	
	public $id_icr_location;
	
	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'icr',
		'primary' => 'id_icr',
		'fields' => array(
			'cod_icr' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 6),
			'id_historico' => array('type' => self::TYPE_INT, 'required' => true),
			'id_estado_icr' => array('type' => self::TYPE_INT, 'required' => true),
			'id_icr_location' => array('type' => self::TYPE_STRING),
		),
	);
	
	/**
	 * Obtiene todos los ICR, si no hay filtro de código
	 *
	 * @param string  ID ubicación
	 * @return array
	 */
	public static function getIcr($code = '')
	{
		$query = new DbQuery();
		$query->select('cod_icr');
		$query->from('icr');
		if( $code )
			$query->where('cod_icr = "' . pSQL($code) . '"');

		if( $results = Db::getInstance()->executeS($query) )
			return $results;

		return array();
	}
	
	/**
	 * Actualiza la ubicacion del ICR
	 *
	 * @param string  Código ICR
	 * @param string  Ubicación ICR
	 * @return bool
	 */
	public static function updateLocation($icr = '', $location = '')
	{
		//$getIcr = self::getIcr($icr);
		//if( empty($getIcr) )
		//	return false;

		$query = array(
			'table' => 'icr',
			'data' => array('id_icr_location' => pSQL($location)),
			'where' => 'cod_icr = "' . pSQL($icr) . '"',
		);

		return Db::getInstance()->update($query['table'], $query['data'], $query['where']);
	}

	/**
	 * Consulta todos los códigos ICR y su almacén actual
	 *
	 * @return array
	 */
	public static function getAllWarehouseICR()
	{
		// Consulta todos los códigos ICR y su almacén actual
		$query = new DbQuery();
		$query->select('w.reference, i.cod_icr')
				->from('supply_order_icr', 'soi')
				->innerJoin('icr', 'i', ' i.id_icr = soi.id_icr')
				->innerJoin('warehouse', 'w', ' w.id_warehouse = soi.id_warehouse');

		if( $results = Db::getInstance()->executeS($query) )
			return $results;

		return array();
	}

	/**
	 * Comprueba que el código ICR pertenezca al almacén.
	 * Crea la tabla temporal para la consulta masiva del
	 * almacén y código ICR, agrega la ubicación, 
	 * actualiza las ubicaciones de los ICR si pertenecen al almacén
	 *
	 * @param string  Referencia del almacén
	 * @param string  Ubicacíon del almacén
	 * @param string  Códigos ICR
	 * @return array  ICRs no válidos
	 */
	public static function checkWarehouseICR($reference = '', $location = '', $icrs = array())
	{
		$alenum = rand(10, 40);
		$table = 'tmp_warehouse_icr_' . $alenum;
		$sql = 'CREATE TEMPORARY TABLE IF NOT EXISTS ' . $table . ' ('
					. 'reference varchar(6) NOT NULL,'
					. 'cod_icr varchar(12) NULL,'
					. 'exist TINYINT(1) NULL DEFAULT 0,'
					. 'PRIMARY KEY (`cod_icr`),'
					. 'INDEX indx_ps_cod_icr_' . $alenum . ' (cod_icr) USING BTREE'
				. ')';

		if( !Db::getInstance()->execute($sql) )
			return array(
				'success' => false, 
				'message' => 'Error al crear la tabla temporal.'
			);

		$sql = 'INSERT INTO ' . $table . ' (reference, cod_icr) VALUES ';
		foreach ($icrs as $icr) {
			$sql .= "('" . $reference . "','" . $icr . "'),";
		}

		if( !Db::getInstance()->execute(rtrim($sql, ',')) )
			return array(
				'success' => false, 
				'message' => 'Error al insertar los datos en la tabla temporal.'
			);

		// Actualiza el campo de exist, si código ICR pertenece al almacén
		$sql = 'UPDATE '. $table.' twi '
				. 'INNER JOIN '._DB_PREFIX_.'icr i ON ( i.cod_icr = twi.cod_icr ) '
				. 'INNER JOIN '._DB_PREFIX_.'warehouse w ON ( w.reference = twi.reference ) '
				. 'INNER JOIN '._DB_PREFIX_.'supply_order_icr soi ON ( '
					. 'i.id_icr = soi.id_icr '
					. 'AND w.id_warehouse = soi.id_warehouse ) '
				. 'SET twi.exist = 1';

		if( !Db::getInstance()->execute($sql) )
			return array(
				'success' => false, 
				'message' => 'Error al actualizar los datos temporales.'
			);

		if( !IcrLocationCore::setLocation($location) )
			return array(
				'success' => false, 
				'message' => 'Error al crear la ubicación.'
			);

		// Actualiza la ubicación de los ICR válidos
		$sql = 'UPDATE '._DB_PREFIX_.'icr i '
				. 'INNER JOIN ' . $table . ' twi ON ( '
					. 'twi.cod_icr = i.cod_icr '
					. 'AND twi.exist = 1 ) '
				. 'SET i.id_icr_location = "' . $location . '"';

		if( !Db::getInstance()->execute($sql) )
			return array(
				'success' => false, 
				'message' => 'Error al actualizar la ubicación de los ICR.'
			);

		// Consulta los códigos ICR que no fueron válidos
		$sql = 'SELECT cod_icr FROM ' . $table . ' WHERE exist = 0';
		if( $results = Db::getInstance()->ExecuteS($sql) ) {
			$invalid = array();
			foreach ($results as $row)
				$invalid[] = $row['cod_icr'];

			return array(
				'success' => false, 
				'message' => 'No se pudo actualizar la ubicación de los siguientes ICR(s), por favor verifique que pertenezcan al almacén y/o existan:| ',
				'invalid' => $invalid
			);
		}

		return array(
			'success' => true,
			'message' => 'Se actualizaron todos los ICR(s) correctamente.'
		);

	}

}
