<?php

class Validate extends ValidateCore {

	/**
	 *  Validacion de registro sanitario mexico
	 */
	public static function isUpc($upc)
	{
		//error_log("\r\ncargando clase isUpc OVERRIDE:".preg_match('/^[0-9]{2,4}[a-zA-Z]{1,2}[0-9]{4}[a-zA-Z]{3,6}$/', $upc).'---:--'.$upc.'--',3,'/var/log/nginx/error.log');

		return !$upc || preg_match('/^.+$/', $upc); //preg_match('/^[0-9]{2,4}[a-zA-Z]{1,2}[0-9]{4}[a-zA-Z]{3,6}$/', $upc);
	}

} 