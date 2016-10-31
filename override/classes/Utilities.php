<?php

class Utilities extends UtilitiesCore {

	/**
	 * [sanear_string limpia un string de tal forma que solo queden letras y numeros en su contenido]
	 * @param  [string] $string [cadena de caracteres a limpiar]
	 * @return [string]         [cadena de caracteres limpiada]
	 */
	public static function sanear_string ( $string )
	{

	    $string = trim($string);

	    $string = str_replace(
	        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
	        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
	        $string
	    );

	    $string = str_replace(
	        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
	        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
	        $string
	    );

	    $string = str_replace(
	        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
	        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
	        $string
	    );

	    $string = str_replace(
	        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
	        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
	        $string
	    );

	    $string = str_replace(
	        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
	        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
	        $string
	    );

	    $string = str_replace(
	        array('ñ', 'Ñ', 'ç', 'Ç'),
	        array('n', 'N', 'c', 'C',),
	        $string
	    );

	    //Esta parte se encarga de eliminar cualquier caracter extraño
	    $string = str_replace(
	        array("\\", "¨", "º", "-", "~",
	             "#", "@", "|", "!", "\"",
	             "·", "$", "%", "&", "/",
	             "(", ")", "?", "'", "¡",
	             "¿", "[", "^", "`", "]",
	             "+", "}", "{", "¨", "´",
	             ">", "< ", ";", ",", ":",
	             ".", " "),
	        '',
	        $string
	    );


	    return $string;
	}

	// datos tipos de documentos
	public static function data_type_documents ()
	{
		$query = new DbQuery();
		$query->select('*');
		$query->from('document');
		$query->where('active = 1');
		$query->orderBy('document ASC');
		return Db::getInstance()->executeS($query);
	}

	// datos customer
	public static function data_customer_billing ( $idcliente )
	{
		$query = new DbQuery();
		$query->select('identification, firstname, lastname, id_type');
		$query->from('customer');
		$query->where('id_customer = '.$idcliente);
		$datacustomer = Db::getInstance()->executeS($query);
		return $datacustomer[0];
	}

	// datos address RFC
	public static function data_address_RFC ( $idcliente ){
            
            if(isset( $idcliente ) && $idcliente != null && $idcliente != ""){
		$sql = "SELECT
					a.id_address, a.dni, a.firstname, a.address1, a.postcode, a.phone, ac.id_city, a.id_state, a.id_colonia, a.alias
				FROM "._DB_PREFIX_."address a
				INNER JOIN "._DB_PREFIX_."address_city ac ON ( a.id_address = ac.id_address )
				WHERE a.is_rfc = 1
				AND a.id_customer = ".$idcliente;
		$dataaddressrfc = Db::getInstance()->ExecuteS($sql);

		return $dataaddressrfc[0];
            }
            return array();
	}
}