		<?php

		require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
		require_once(_PS_MODULE_DIR_ . 'payulatam/openpay/OpenpayController.php');

		class PasarelaPagoCore extends ObjectModel {

			public $errores_cargue = array();

			public static $definition = array(
			                                  'table' => 'pasarelas_pago',
			                                  'primary' => 'id_pasarela',
			                                  'fields' => array(
			                                                    'nombre' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 20),
			                                                    'activa' => array('type' => self::TYPE_INT, 'required' => true),
			                                                    ),
			                                  );

			/**
			 * GetList
			 * @return void
			 */

			public static function GetList() {

				$query = new DbQuery();
				$query->select(' id_pasarela, nombre ');
				$query->from('pasarelas_pago', 'ol');
				$query->where('activa = 1');
		        //$query->groupBy(' od.id_supply_order_detail ');


				$items = Db::getInstance()->executeS($query);

				if ( $items ) {

					foreach ($items as $valores) {

						$pasarela[$valores['nombre']] = " ".$valores['nombre']." ";
					}


					return $pasarela;

				} else {
					//$this->errores_cargue[] = "Error obteniendo listado de pasarelas de pago.";
					return false;
				}

			}

			/**
			 * Retorna los medios de pago soportados por pasarelas de pago.
			 */
			public static function GetPMediosPsarelas(){

				$query = new DbQuery();
				$query->select('id_medio_de_pago,nombre,pasarela, type');
				$query->from('medios_de_pago');
				$query->where('Activo = 1');
				$items = Db::getInstance()->executeS($query);
				if ( $items ) {
					foreach ($items as $valores) {
						$pasarela[$valores['id_medio_de_pago']] = array('nombre' => $valores['nombre'],'pasarela' => $valores['pasarela'] );
					}
					return $pasarela;
				} else {
					//$this->errores_cargue[] = "Error obteniendo listado de medios de pago de pasarelas.";
					return false;
				}
			}

			/**
			 * Retorna estructura de la tabla ps_datos_pasarelas_pago
			 */
			public static function GetModelDatosPasarelas(){

				$result = Db::getInstance()->ExecuteS('SHOW COLUMNS FROM ps_datos_pasarelas_pago;');
				if ( $result ) {
					return $result;

				} else {
					$this->errores_cargue[] = "Error obteniendo la estructura de la tabla ps_datos_pasarelas_pago.";
					return false;
				}
			}

			/**
			 * Retorna los datos de conexión de una pasarela 
			 */
			public static function GetDatosPasarelas(){

				$result = Db::getInstance()->getRow("	SELECT datos.* 
				                                    FROM ". _DB_PREFIX_ ."datos_pasarelas_pago datos 
				                                    INNER JOIN ". _DB_PREFIX_ ."pasarelas_pago pasarela ON(pasarela.id_pasarela = datos.id_pasarela)
				                                    WHERE pasarela.nombre='".Tools::getValue('nombre')."' AND datos.produccion = '".Tools::getValue('tipo_credenciales')."';");
				if ( $result ) {
					return $result;
				} else {
					return false;
				}
			}

		/**
			 * Agrega un nuevo nombre de pasarela
			 */	
		public static function AddPasarela(){

			$state = false;
			if(!empty(Tools::getValue('estado')))
				$state = Tools::getValue('estado');
			$name =  Tools::getValue('nombre');
			if(!empty($state) && !empty($name))
				return Db::getInstance()->Execute("INSERT INTO `". _DB_PREFIX_ ."pasarelas_pago` (`nombre`, `activa`) VALUES ( '".$name."', ".$state.");");
			return false;
		}

		/**
		 * Define o actualiza, la pasarela que debe utilizar un determinado medio de pago
		 */
		public static function SetPasarelaMediosP($medios_de_pago){

			$processed_rows = 0;
			foreach ($medios_de_pago as $key => $value) {
				if(Db::getInstance()->Execute("UPDATE `". _DB_PREFIX_ ."medios_de_pago` SET  `pasarela`='".$value."' WHERE (`id_medio_de_pago`='".$key."');"))
				$processed_rows++;
			}
			if(count($medios_de_pago == $processed_rows))
				return true;
			return false;
		}

		/**
		 * Valida si existe una terminal de una pasarela de pago
		 */
		public static function IsTerminal($terminal){
			$sql = "SELECT terminal.terminal 
			FROM ps_count_terminal_transacctions terminal 
			INNER JOIN ps_datos_pasarelas_pago datos ON(terminal.id_datos_pasarela = datos.id_dato_pasarela)
			WHERE terminal.terminal = '".$terminal."';";
			if( !empty(Db::getInstance()->getValue($sql))){
				return true;
			}
			return false;
		}

		/**
		* Guarda o actualiza los datos de una pasarela de pago
		*/		
		public static function SaveDatosPasarela(){
			$flag = NULL;
			$array1 = PasarelaPagoCore::GetModelDatosPasarelas();
			$array2 = array();
			foreach ($array1 as $key => $value) {
				$array2[$value['Field']] = true;	
			}
			$array3 = array();
			foreach ($array2 as $key => $value) {
				$array3[$key] = Tools::getValue($key);
			}
			$id_datos = NULL;
			$return = true;
			$sql = '';
			if(isset($array3['id_dato_pasarela']) && !empty($array3['id_dato_pasarela'])){
				$sql ="UPDATE `". _DB_PREFIX_ ."datos_pasarelas_pago` SET ";
				foreach ($array3 as $key => $value) {
					$sql .="`".$key."` =";
					if(empty($value)){
						$sql .=" NULL, ";
					}else{
						$sql .="'".$value."', ";
					}				 
				}
				$sql = trim($sql,', ');
				$sql .=" WHERE (`id_dato_pasarela`='".$array3['id_dato_pasarela']."');";
				$return = Db::getInstance()->Execute(utf8_encode($sql));
				$id_datos = $array3['id_dato_pasarela'];
			}else{
				$array3['id_pasarela'] = PasarelaPagoCore::PasarelaByName(Tools::getValue('select_pasarela'))['id_pasarela'];
				$array3['produccion'] = Tools::getValue('tipo_credenciales');
				unset($array3['id_dato_pasarela']);
				/*			
					$sql = "INSERT INTO `ps_datos_pasarelas_pago` (";
				    foreach ($array3 as $key => $value) {
						$sql .="`".$key."`, ";
					}				 
					$sql = trim($sql,', ').') VALUES (';
					foreach ($array3 as $key => $value) {
						if(empty($value)){
							$sql .=" NULL, ";
						}else{
							$sql .="'".$value."', ";
						}
					}	
					$sql = trim($sql,', ').');'; 
				*/
		$return = Db::getInstance()->insert('datos_pasarelas_pago',$array3);
		$id_datos = Db::getInstance()->Insert_ID();
	}

	if($return){
		$terminales = Tools::getValue("terminals");
		if(!empty($terminales) && strlen($terminales)>3){
			if(strpos($terminales, ',')){
				$arrayt = explode(',', $terminales);
				$flag_terminales = true;
				foreach ($arrayt as $key => $value) {
					if(!PasarelaPagoCore::IsTerminal($value)){
						$flag_terminales += PasarelaPagoCore::AddTerminal($value, $id_datos);
					}
				}
				return $flag_terminales;
			}else{
				if(!PasarelaPagoCore::IsTerminal($terminales)){
					return PasarelaPagoCore::AddTerminal($terminales, $id_datos);
				}
			}								
		}
		return $return;
	}

}

		/**
		 * Agrega una terminal, asociada a los datos de conexión de una pasarela
		 */
		public static function AddTerminal($terminal, $id_datos_pasarela){

			return Db::getInstance()->insert('count_terminal_transacctions', array('terminal'=>$terminal,'id_datos_pasarela'=>$id_datos_pasarela));
		}

			/**
			 * buscar pasarela de pago por nombre
			 */
			public static function PasarelaByName($name){

				$result = Db::getInstance()->getRow("SELECT * FROM
				                                    ". _DB_PREFIX_ ."pasarelas_pago
				                                    WHERE nombre = '".$name."';");
				if ( $result ) {
					return $result;
				} else {
					return FALSE;
				}
			}

		/**
		 * Retorna los datos de conexión para un medio de pago
		 */

		public static function GetDataConnect($mediop){
			$sql = "SELECT datos.*, pasa.nombre as nombre_pasarela
			FROM 
			ps_pasarelas_pago pasa 
			INNER JOIN ". _DB_PREFIX_ ."datos_pasarelas_pago datos ON(pasa.id_pasarela = datos.id_pasarela)
			INNER JOIN ". _DB_PREFIX_ ."medios_de_pago mediosp ON(pasa.nombre = mediosp.pasarela)
			WHERE mediosp.nombre = '".$mediop."' AND datos.produccion != (SELECT `value` from ". _DB_PREFIX_ ."configuration WHERE `name` = 'PAYU_DEMO');";

			$result = Db::getInstance()->getRow($sql);

			if ( $result ) {
				return $result;
			} else {
				return FALSE;
			}		
		}


		/*
		 * doc
		 * http://eureka.ykyuen.info/2011/05/05/php-send-a-soap-request-by-curl/
		 * http://stackoverflow.com/questions/18572550/php-xml-how-to-generate-a-soap-request-in-php-from-this-xml
		 * http://stackoverflow.com/questions/7120586/soap-request-in-php-with-curl
		 * https://gist.github.com/johnkary/7782110
		 * http://www.bin-co.com/php/scripts/xml2array/
		 */

		/** 
		 * xml2array() will convert the given XML text to an array in the XML structure. 
		 * Link: http://www.bin-co.com/php/scripts/xml2array/ 
		 * Arguments : $contents - The XML text 
		 *                $get_attributes - 1 or 0. If this is 1 the function will get the attributes as well as the tag values - this results in a different array structure in the return value.
		 *                $priority - Can be 'tag' or 'attribute'. This will change the way the resulting array sturcture. For 'tag', the tags are given more importance. 
		 * Return: The parsed XML in an array form. Use print_r() to see the resulting array structure. 
		 * Examples: $array =  xml2array(file_get_contents('feed.xml')); 
		 *              $array =  xml2array(file_get_contents('feed.xml', 1, 'attribute')); 
		 */ 
		public static  function xml2array($contents, $get_attributes=1, $priority = 'tag') { 
			if(!$contents) return array(); 

			if(!function_exists('xml_parser_create')) { 
		        //print "'xml_parser_create()' function not found!"; 
				return array(); 
			} 

		    //Get the XML parser of PHP - PHP must have this module for the parser to work 
			$parser = xml_parser_create(''); 
		    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
		    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
		    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
		    xml_parse_into_struct($parser, trim($contents), $xml_values); 
		    xml_parser_free($parser); 

		    if(!$xml_values) return;//Hmm... 

		    //Initializations 
		    $xml_array = array(); 
		    $parents = array(); 
		    $opened_tags = array(); 
		    $arr = array(); 

		    $current = &$xml_array; //Refference 

		    //Go through the tags. 
		    $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
		    foreach($xml_values as $data) { 
		        unset($attributes,$value);//Remove existing values, or there will be trouble 

		        //This command will extract these variables into the foreach scope 
		        // tag(string), type(string), level(int), attributes(array). 
		        extract($data);//We could use the array by itself, but this cooler. 

		        $result = array(); 
		        $attributes_data = array(); 

		        if(isset($value)) { 
		        	if($priority == 'tag') $result = $value; 
		            else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
		        } 

		        //Set the attributes too. 
		        if(isset($attributes) and $get_attributes) { 
		        	foreach($attributes as $attr => $val) { 
		        		if($priority == 'tag') $attributes_data[$attr] = $val; 
		                else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
		            } 
		        } 

		        //See tag status and do the needed. 
		        if($type == "open") {//The starting of the tag '<tag>' 
		        $parent[$level-1] = &$current; 
		            if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
		            	$current[$tag] = $result; 
		            	if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
		            	$repeated_tag_index[$tag.'_'.$level] = 1; 

		            	$current = &$current[$tag]; 

		            } else { //There was another element with the same tag name 

		                if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
		                	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
		                	$repeated_tag_index[$tag.'_'.$level]++; 
		                } else {//This section will make the value an array if multiple tags with the same name appear together 
		                    $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array 
		                    $repeated_tag_index[$tag.'_'.$level] = 2; 

		                    if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
		                    	$current[$tag]['0_attr'] = $current[$tag.'_attr']; 
		                    	unset($current[$tag.'_attr']); 
		                    } 

		                } 
		                $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
		                $current = &$current[$tag][$last_item_index]; 
		            } 

		        } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
		            //See if the key is already taken. 
		            if(!isset($current[$tag])) { //New Key 
		            	$current[$tag] = $result; 
		            	$repeated_tag_index[$tag.'_'.$level] = 1; 
		            	if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 

		            } else { //If taken, put all things inside a list(array) 
		                if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 

		                    // ...push the new element into that array. 
		                	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 

		                	if($priority == 'tag' and $get_attributes and $attributes_data) { 
		                		$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
		                	} 
		                	$repeated_tag_index[$tag.'_'.$level]++; 

		                } else { //If it is not an array... 
		                    $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value 
		                    $repeated_tag_index[$tag.'_'.$level] = 1; 
		                    if($priority == 'tag' and $get_attributes) { 
		                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 

		                        	$current[$tag]['0_attr'] = $current[$tag.'_attr']; 
		                        	unset($current[$tag.'_attr']); 
		                        } 

		                        if($attributes_data) { 
		                        	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
		                        } 
		                    } 
		                    $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
		                } 
		            } 

		        } elseif($type == 'close') { //End of tag '</tag>' 
		        $current = &$parent[$level-1]; 
		    } 
		} 

		return($xml_array); 
	}

	/** genera un numero aleatorio **/

	public static function n_digit_random($digits){
		return rand(pow(10, $digits - 1) - 1, pow(10, $digits) - 1);
	}

		/**
		 *  Genera  y envía una transacción a Redeban, retorna true o false dependiendo la respuesta del servicio web de Redeban.  
		 */
		public static function EnviarPagoRedeBan($medio_de_pago,$parametros){

			$context = Context::getContext();
			$soap_request = PasarelaPagoCore::GetXmlPay($medio_de_pago,$parametros,$context);
			$str_tem = str_replace("<cmp:compraProcesarSolicitud>", "<com:compraReversarSolicitud>", $soap_request);
			$soap_request_reverse = str_replace("</cmp:compraProcesarSolicitud>", "</com:compraReversarSolicitud>", $str_tem);
			if(empty($soap_request))
				return FALSE;
			PasarelaPagoCore::AddTransaction($soap_request,$soap_request_reverse, $context); 
			$respuesta = PasarelaPagoCore::SendSoapRequest($soap_request,$medio_de_pago);
			$array_request = PasarelaPagoCore::xml2array($soap_request);
			$terminal = $array_request['soapenv:Envelope']['soapenv:Body']['cmp:compraProcesarSolicitud']['cmp:cabeceraSolicitud']['cmp:infoPuntoInteraccion']['cmr:idTransaccionTerminal'];;
			$id_transaccion_terminal =  $array_request['soapenv:Envelope']['soapenv:Body']['cmp:compraProcesarSolicitud']['cmp:cabeceraSolicitud']['cmp:infoPuntoInteraccion']['cmr:idTerminal'];
		 	$array_response = PasarelaPagoCore::xml2array($respuesta);  //exit('<pre>'.print_r($array_response,TRUE));
		 	PasarelaPagoCore::UpdateTransaction($respuesta,'response',$terminal, $id_transaccion_terminal); 
		 	$respuesta_cod = NULL; 
		 	if( isset($array_response['soapenv:Envelope']['soapenv:Body']['ns1:compraProcesarRespuesta']['ns1:infoRespuesta']['ns3:codRespuesta']))
		 		$respuesta_cod = $array_response['soapenv:Envelope']['soapenv:Body']['ns1:compraProcesarRespuesta']['ns1:infoRespuesta']['ns3:codRespuesta'];
		 	if(isset($array_response['soapenv:Envelope']['soapenv:Body']['com:compraProcesarRespuesta']['com:infoRespuesta']['rbm:codRespuesta']))
		 		$respuesta_cod = $array_response['soapenv:Envelope']['soapenv:Body']['com:compraProcesarRespuesta']['com:infoRespuesta']['rbm:codRespuesta'];
		 	if(isset($array_response['soapenv:Envelope']['soapenv:Body']['NS2:Fault']))
		 		$respuesta_cod = $array_response['soapenv:Envelope']['soapenv:Body']['NS2:Fault']['faultcode'];
		 	if($respuesta_cod != '00' && strlen($respuesta_cod) == 4){
		 		$respuesta_reversa = PasarelaPagoCore::SendSoapRequest($soap_request_reverse,$medio_de_pago);
		 		PasarelaPagoCore::UpdateTransaction($respuesta_reversa,'response_reverse',$terminal, $id_transaccion_terminal); 
		 		return FALSE;
		 	}
		 	if($respuesta_cod != '00')
		 		return FALSE;
		 	if($respuesta_cod == '00')
		 		return TRUE;
		 	return FALSE;
		 }

		/**
		 * Retorna un String con la estructura del XML, requerido por el servicio web de Redeban
		 */
		public static function GetXmlPay($medio_de_pago,$parametros,$context){

			$conn = PasarelaPagoCore::GetDataConnect($medio_de_pago);
			$monto_total = $context->cart->getOrderTotal();
			$Base_devolucion_iva = ($monto_total - $context->cart->total_tax);
			$id_transaccion_terminal = (int) PasarelaPagoCore::GetCountTransaction($medio_de_pago);
			$referencia =  $id_transaccion_terminal.'-'.$context->cart->id.'-'.$context->customer->id;

			$soap_request = '<?xml version="1.0" encoding="UTF-8"?>
			<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:cmp="http://www.rbm.com.co/esb/comercio/compra/" xmlns:cmr="http://www.rbm.com.co/esb/comercio/" xmlns:p="http://www.rbm.com.co/esb/comercio/compra" xmlns:rbm="http://www.rbm.com.co/esb/" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
			<soapenv:Header>
			<wsse:Security soapenv:mustUnderstand="1"> 
			<wsse:UsernameToken> 
			<wsse:Username>'.$conn['user'].'</wsse:Username> 
			<wsse:Password Type="PasswordText">'.$conn['password'].'</wsse:Password> 
			</wsse:UsernameToken>
			</wsse:Security>
			</soapenv:Header>
			<soapenv:Body>
			<cmp:compraProcesarSolicitud>
			<cmp:cabeceraSolicitud>
			<cmp:infoPuntoInteraccion>
			<cmr:tipoTerminal>WEB</cmr:tipoTerminal>
			<cmr:idTerminal>'.$conn['default_terminal'].'</cmr:idTerminal>
			<cmr:idAdquiriente>'.$conn['idAdquiriente'].'</cmr:idAdquiriente>
			<cmr:idTransaccionTerminal>'.$id_transaccion_terminal.'</cmr:idTransaccionTerminal>
			<cmr:modoCapturaPAN>Manual</cmr:modoCapturaPAN>
			<cmr:capacidadPIN>Virtual</cmr:capacidadPIN>
			</cmp:infoPuntoInteraccion>
			</cmp:cabeceraSolicitud>
			<cmp:idPersona>
			<rbm:tipoDocumento>'.$parametros['tipoDocumento'].'</rbm:tipoDocumento>
			<rbm:numDocumento>'.$parametros['numDocumento'].'</rbm:numDocumento>
			</cmp:idPersona>
			<cmp:infoMedioPago>
			';
			if($medio_de_pago == 'Tarjeta_credito'){
				$soap_request .='<cmp:idTarjetaCredito>
				<rbm:franquicia>'.$parametros['franquicia'].'</rbm:franquicia>
				<rbm:numTarjeta>'.$parametros['numTarjeta'].'</rbm:numTarjeta>
				<rbm:fechaExpiracion>'.$parametros['fechaExpiracion'].'</rbm:fechaExpiracion>
				<rbm:codVerificacion>'.$parametros['codVerificacion'].'</rbm:codVerificacion>
				</cmp:idTarjetaCredito>
				';
			}
			elseif ($medio_de_pago == 'Pse') {
				$soap_request .='<com:idCuenta>
				<esb:tipoCuenta>'.$parametros['tipoCuenta'].'</esb:tipoCuenta>
				<esb:numCuenta>'.$parametros['numCuenta'].'</esb:numCuenta>
				<esb:codBanco>'.$parametros['codBanco'].'</esb:codBanco>
				</com:idCuenta>
				';
			}
			$soap_request .='</cmp:infoMedioPago>
			<cmp:infoCompra>
			<cmp:montoTotal>'.number_format($monto_total, 2, '.', '').'</cmp:montoTotal>
			<cmp:infoImpuestos>
			<rbm:tipoImpuesto>IVA</rbm:tipoImpuesto>
			<rbm:monto>'.number_format($context->cart->total_tax, 2, '.', '').'</rbm:monto>
			</cmp:infoImpuestos>
			<cmp:montoDetallado>
			<rbm:tipoMontoDetallado>BaseDevolucionIVA</rbm:tipoMontoDetallado>
			<rbm:monto>'.number_format($Base_devolucion_iva, 2, '.', '').'</rbm:monto>
			</cmp:montoDetallado>
			<cmp:referencia>'.$referencia.'</cmp:referencia>
			<cmp:cantidadCuotas>'.trim($parametros['cantidadCuotas']).'</cmp:cantidadCuotas>
			</cmp:infoCompra>
			</cmp:compraProcesarSolicitud>
			</soapenv:Body>
			</soapenv:Envelope>';

			return	$soap_request;	
		}



		/**
		 * Envía una solicitud de pago a la pasarela de pago.
		 */
		public static function SendSoapRequest($soap_request,$medio_de_pago){

			$url = PasarelaPagoCore::GetDataConnect($medio_de_pago)['url_ws'];
			$header = array(
			                "Content-type: text/xml;charset=\"utf-8\"",
			                "Accept: text/xml",
			                "Cache-Control: no-cache",
			                "Pragma: no-cache",
			                "SOAPAction: \"run\"",
			                "Content-length: ".strlen($soap_request),
			                );

			$soap_do = curl_init();
		  //curl_setopt($soap_do, CURLOPT_URL, "https://www.pagosrbm.com:443/GlobalPayServicios/GlobalPayServicioDePago" );
			curl_setopt($soap_do, CURLOPT_URL, $url );
			curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 240);
			curl_setopt($soap_do, CURLOPT_TIMEOUT,        240);
			curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true );
			curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($soap_do, CURLOPT_POST,           true );
			curl_setopt($soap_do, CURLOPT_POSTFIELDS,     $soap_request);
			curl_setopt($soap_do, CURLOPT_HTTPHEADER,     $header);

			$response = curl_exec($soap_do);
		  //$info = curl_getinfo($soap_do);

			if($response === false) {
				$err = 'Curl error: ' . curl_error($soap_do);
				curl_close($soap_do);
				echo '<br><pre>'.print_r($err,true).'</pre><br>';
			} else {
				curl_close($soap_do);
				return $response;
			}
		}

		/**
		 * Retorna el numero de intentos de pago de un carrito
		 */
		public static function get_intentos($id_cart){
			$query=  "SELECT id_cart,contador
			FROM "._DB_PREFIX_."count_pay_cart
			WHERE id_cart = ".(int)$id_cart;

			$row = Db::getInstance()->getRow($query);
			if(isset($row['contador'])){
				return $id_cart.'_'.$row['contador'];
			}else{
				return false;
			}

		}

		/**
		     * Contador de intentos de pago 
		     */    

		public static function count_pay_cart($id_cart){

			$query= "SELECT id_cart,contador
			FROM "._DB_PREFIX_."count_pay_cart
			WHERE id_cart = ".(int)$id_cart;

			$row = Db::getInstance()->getRow($query);

			if ( isset($row) && count($row) > 1 && is_array($row)){
				$sql= "UPDATE "._DB_PREFIX_."count_pay_cart SET contador = ". ((int)$row['contador'] + 1) ." WHERE id_cart = ".$id_cart;

				if(Db::getInstance()->Execute($sql))
					return ($row['contador']+1);
			}
			else{
				$ini=1;
				$sql="INSERT INTO "._DB_PREFIX_."count_pay_cart (id_cart,contador)
				VALUES(".$id_cart.",".$ini.")";   
				if(Db::getInstance()->Execute($sql))
					return $ini;
			}
		}

		/**
		 * Retorna el contador de transacciones de la terminal activa 
		 */
		public static function    GetCountTransaction($medio_de_pago)
		{

			$query= "SELECT  countt.count,countt.id_datos_pasarela, countt.id_terminal
			FROM 
			"._DB_PREFIX_."pasarelas_pago pasa 
			INNER JOIN "._DB_PREFIX_."datos_pasarelas_pago datos ON(pasa.id_pasarela = datos.id_pasarela)
			INNER JOIN "._DB_PREFIX_."medios_de_pago mediosp ON(pasa.nombre = mediosp.pasarela)
			INNER JOIN "._DB_PREFIX_."count_terminal countt ON (countt.id_datos_pasarela = datos.id_dato_pasarela)
			WHERE mediosp.nombre = '".$medio_de_pago."' AND datos.default_terminal = countt.terminal AND datos.produccion != (SELECT `value` from "._DB_PREFIX_."configuration WHERE `name` = 'PAYU_DEMO');";
			$row = Db::getInstance()->getRow($query,false);

			if ( isset($row) && count($row) > 1 && is_array($row)){
				if( Db::getInstance()->update('count_terminal', array('count' => ($row['count'] + 1) ), 'id_terminal ='.(int)$row['id_terminal']) ){
					return (int)($row['count']+1);
				}

			}


			return FALSE;
		}

		/**
		  * Agrega una transacción en la base de datos. remplazar
		  */ 
		public static function AddTransaction($xml_str_request, $xml_str_reverse, $context){
			$array_request = PasarelaPagoCore::xml2array($xml_str_request);
			 	// Limpiar datos sensibles del cliente antes de almacenar en la base de datos
			$num_tarjeta = $array_request['soapenv:Envelope']['soapenv:Body']['cmp:compraProcesarSolicitud']['cmp:infoMedioPago']['cmp:idTarjetaCredito']['rbm:numTarjeta'];
			$buscar_str = '<rbm:numTarjeta>'.$num_tarjeta.'</rbm:numTarjeta>';
			$remplazar_str = '<rbm:numTarjeta>'.str_pad(substr($num_tarjeta, -4), strlen($num_tarjeta), "*", STR_PAD_LEFT).'</rbm:numTarjeta>';
			$xml_str_request = str_replace($buscar_str, $remplazar_str, $xml_str_request);
			$xml_str_reverse = str_replace($buscar_str, $remplazar_str, $xml_str_reverse);

			$cod_verificacion = $array_request['soapenv:Envelope']['soapenv:Body']['cmp:compraProcesarSolicitud']['cmp:infoMedioPago']['cmp:idTarjetaCredito']['rbm:codVerificacion'];
			$buscar_str = '<rbm:codVerificacion>'.$cod_verificacion.'</rbm:codVerificacion>';
			$remplazar_str = '<rbm:codVerificacion>'.str_pad('', strlen($cod_verificacion), "*", STR_PAD_LEFT).'</rbm:codVerificacion>';
			$xml_str_request = str_replace($buscar_str, $remplazar_str, $xml_str_request);
			$xml_str_reverse = str_replace($buscar_str, $remplazar_str, $xml_str_reverse);
			$date_add = date("Y-m-d H:i:s");
			$id_transaccion_terminal = $array_request['soapenv:Envelope']['soapenv:Body']['cmp:compraProcesarSolicitud']['cmp:cabeceraSolicitud']['cmp:infoPuntoInteraccion']['cmr:idTransaccionTerminal'];
			$id_terminal = $array_request['soapenv:Envelope']['soapenv:Body']['cmp:compraProcesarSolicitud']['cmp:cabeceraSolicitud']['cmp:infoPuntoInteraccion']['cmr:idTerminal'];
			$referencia = $array_request['soapenv:Envelope']['soapenv:Body']['cmp:compraProcesarSolicitud']['cmp:infoCompra']['cmp:referencia'];
			$id_cart = $context->cart->id;
			$amount = $context->cart->getOrderTotal();
			$array = array('id_transaccion_terminal'=>$id_transaccion_terminal,'terminal'=>$id_terminal,'id_cart'=>$id_cart ,'amount'=>$amount,'reference'=>$referencia,'request'=> utf8_encode($xml_str_request),'reverse'=> utf8_encode($xml_str_reverse),'date_add'=>$date_add);
			return Db::getInstance()->insert('transactions',$array);
		}

		/**
		 * Actualizar transacción
		 */
		public static function UpdateTransaction($xml_str, $option ='response', $id_transaccion_terminal, $terminal){
			
			$array_response = PasarelaPagoCore::xml2array($xml_str);
			$date_update = date("Y-m-d H:i:s");
			$cod_transaction = NULL; 
			$message = NULL;

			if(isset($array_response['soapenv:Envelope']['soapenv:Body']['ns1:compraProcesarRespuesta']))
			{
				$cod_transaction = $array_response['soapenv:Envelope']['soapenv:Body']['ns1:compraProcesarRespuesta']['ns1:infoRespuesta']['ns3:codRespuesta'];
				$message = $array_response['soapenv:Envelope']['soapenv:Body']['ns1:compraProcesarRespuesta']['ns1:infoRespuesta']['ns3:estado'];
			}elseif (isset($array_response['soapenv:Envelope']['soapenv:Body']['com:compraProcesarRespuesta'])) {
				$cod_transaction = $array_response['soapenv:Envelope']['soapenv:Body']['com:compraProcesarRespuesta']['com:infoRespuesta']['rbm:codRespuesta'];
				$message = $array_response['soapenv:Envelope']['soapenv:Body']['com:compraProcesarRespuesta']['com:infoRespuesta']['rbm:estado'];
			}elseif(isset($array_response['soapenv:Envelope']['soapenv:Body']['NS2:Fault'])){
				$cod_transaction = $array_response['soapenv:Envelope']['soapenv:Body']['NS2:Fault']['faultcode'];
				$message = $array_response['soapenv:Envelope']['soapenv:Body']['NS2:Fault']['faultstring'];
			}elseif(isset($array_response['soapenv:Envelope']['soapenv:Body']['soapenv:Fault'])){
				$cod_transaction = $array_response['soapenv:Envelope']['soapenv:Body']['soapenv:Fault']['faultcode'];
				$message = $array_response['soapenv:Envelope']['soapenv:Body']['soapenv:Fault']['faultstring'];
			}	


			$data = array();

			if($option == 'response_reverse'){
				$data = array($option => utf8_encode($xml_str),'date_update' => $date_update,'cod_reverse'=>$cod_transaction,'reverse_mesaage'=>$message);
			}else{	
				$data = array($option => utf8_encode($xml_str),'date_update' => $date_update,'cod_transaction' => $cod_transaction,'message' => $message);
			}

			return Db::getInstance()->update('transactions', $data, 'id_transaccion_terminal = '.$id_transaccion_terminal." AND terminal = '".$terminal."'");
		}

		public static function isPayCart(){

			$context = Context::getContext();
			$sql = "SELECT IF(ISNULL(id_cart),0,1) AS result 
			FROM ps_transactions 
			WHERE id_cart = ".(int)$context->cart->id." AND cod_transaction = '00';";
			return Db::getInstance()->getValue($sql);
		}
		/**
		 * validación campos formularios de pago
		 */
		public static function validateFiles($option){

			switch ($option) {
				case 'Tarjeta_credito':

				$post = array('nombre'  =>  (Tools::getValue('nombre')) ? Tools::getValue('nombre') : Tools::getValue('holder'),
				              'numerot' =>  (Tools::getValue('numerot')) ? Tools::getValue('numerot') : Tools::getValue('card'),
				              'codigot' =>  (Tools::getValue('codigot')) ? Tools::getValue('codigot') : Tools::getValue('cvv'),
				              'date'    =>  Tools::getValue('datepicker'),
				              'cuotas'  =>  Tools::getValue('cuotas'),
				              'Month'   =>  Tools::getValue('Month'),
				              'Year'    =>  Tools::getValue('Year'),
				              'option_pay' =>  Tools::getValue('option_pay')
				              );

				if((!empty($post['nombre']) && !empty($post['numerot']) && !empty($post['codigot']) && !empty($post['cuotas'])) && (!empty($post['date']) || (!empty($post['Month']) && !empty($post['Year']) ) )){
					return $post;
				}

				break;
				case 'Baloto':
				$post = array(
				              'pagar'    =>  Tools::getValue('pagar'),
				              'option_pay'  =>  Tools::getValue('option_pay')
				              );
				if(!empty($post['pagar']) && !empty($post['pagar']))  
					return $post;
				break;    
				case 'Efecty':
				$post = array(
				              'pagar'    =>  Tools::getValue('pagar'),
				              'option_pay'  =>  Tools::getValue('option_pay')
				              );
				if(!empty($post['pagar']) && !empty($post['pagar']))  
					return $post;         	
				break;    
				case 'Pse':
				$post = array(
				              'pse_bank'    =>  Tools::getValue('pse_bank'),
				              'name_bank'  =>  Tools::getValue('name_bank'),
				              'pse_tipoCliente'   =>  Tools::getValue('pse_tipoCliente'),
				              'pse_docType'    =>  Tools::getValue('pse_docType'),
				              'pse_docNumber'    =>  Tools::getValue('pse_docNumber'),
				              'option_pay' =>  Tools::getValue('option_pay')
				              );
				if (!empty($post['pse_bank']) && !empty($post['name_bank']) && !empty($post['pse_tipoCliente']) && !empty($post['pse_docType']) && !empty($post['pse_docNumber'])) {
					return $post;
				}
				break;    
				default:
				return NULL;
				break;
			}
			return FALSE;

		}

		 /**
		   * Retorna estructura Json 
		   */ 
		 public static function EnviarPagoPayu($args,$conn){
		 	// $conn = PasarelaPagoCore::GetDataConnect('Tarjeta_credito');
		 	$intentos = PasarelaPagoCore::count_pay_cart((int) $args['id_cart']);
		 	$conf = new ConfPayu();
		 	$context = Context::getContext();
		 	$referenceCode = md5('payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $args['id_cart'] .'_'.$intentos);
		 	$ref = $referenceCode . '~' . $args['total_paid'] . '~' . (($conn['produccion'] == 1) ? $context->currency->iso_code : 'USD');

		 	$description = (int)$args['id_customer']. '_' .(int)$args['id_cart']. '_' .(int)$args['id_order']. '_' .(int)$args['id_address_invoice'];
		 	$customer = new Customer((int) $args['id_customer']);
		 	$address = new Address((int)$args['id_address_invoice']);
		 	$dni = $conf->get_dni((int)$args['id_address_invoice']);
		 	$nuevafecha = strtotime ( '+5 day' , strtotime ( date('Y-m-j') ) ) ;
		 	$fecha_expiracion = date ( 'Y-m-d' , $nuevafecha ).'T'.date ( 'h:i:s' , $nuevafecha ); 

		 	$_deviceSessionId = NULL;

		 	if (isset($context->cookie->deviceSessionId) && !empty($context->cookie->deviceSessionId) && strlen($context->cookie->deviceSessionId) === 32) {
		 		$_deviceSessionId = $context->cookie->deviceSessionId;
		 	} elseif (isset($_POST['deviceSessionId']) && !empty($_POST['deviceSessionId']) && strlen($_POST['deviceSessionId']) === 32) {
		 		$_deviceSessionId = $_POST['deviceSessionId'];
		 	} else {
		 		$_deviceSessionId = md5($context->cookie->timestamp);
		 	}

		 	$data = '{

		 		"language":"'.$context->language->iso_code.'",
		 		"command":"SUBMIT_TRANSACTION",
		 		"merchant":{
		 			"apiKey":"'.$conn['apikey_privatekey'].'",
		 			"apiLogin":"'.$conn['apilogin_id'].'"
		 		},
		 		"transaction":{
		 			"order":{
		 				"accountId":"'.$conn['accountid'].'",
		 				"referenceCode":"'.$referenceCode.'",
		 				"description":"'.$description.'",
		 				"language":"'.$context->language->iso_code.'",
		 				"notifyUrl":"'.$conf->urlv().'",
		 				"signature":"'. $conf->sing($ref).'",
		 				"additionalValues":{
		 					"TX_VALUE":{
		 						"value":'.$args['total_paid'].',
		 						"currency":"'. $context->currency->iso_code.'"
		 					},
		 					"TX_TAX":{  
		 						"value":'.$total_tax.',
		 						"currency":"'. $context->currency->iso_code.'"
		 					},
		 					"TX_TAX_RETURN_BASE":{  
		 						"value":'. ($total_tax == 0.00 ? 0.00 : ($args['total_paid'] - $total_tax)).',
		 						"currency":"'. $context->currency->iso_code.'"
		 					}
		 				},
		 				"buyer":{
		 					"fullName":"'.$customer->firstname.' '. $customer->lastname.'",
		 					"contactPhone":"'.((empty($address->phone_mobile)) ? 'N/A' : $address->phone_mobile) .'",
		 					"emailAddress":"'.$customer->email.'",
		 					"dniNumber":"'.$dni.'",
		 					"shippingAddress":{
		 						"street1":"'.((empty($address->address1)) ? 'N/A' : $address->address1).'",
		 						"street2":"'.((empty($address->address2)) ? 'N/A' : $address->address2).'",
		 						"city":"'.$address->city.'",
		 						"state":"'.$conf->get_state($address->id_state).'",
		 						"country":"'. $context->country->iso_code.'",
		 						"postalCode":"' . ((empty($address->postcode)) ? '00000': $address->postcode) . '",
		 						"phone":"'.((empty($address->phone)) ? 'N/A': $address->phone ).'"
		 					}
		 				},
		 				"shippingAddress":{
		 					"street1":"'.((empty($address->address1)) ? 'N/A' : $address->address1).'",
		 					"street2":"'.((empty($address->address2)) ? 'N/A' : $address->address2).'",
		 					"city":"'.$address->city.'",
		 					"state":"'.$conf->get_state($address->id_state).'",
		 					"country":"'.$context->country->iso_code .'",
		 					"postalCode":"'. ((empty($address->postcode)) ? '00000': $address->postcode) .'",
		 					"phone":"'.((empty($address->phone)) ? 'N/A': $address->phone) .'"
		 				}
		 			},';
		 			if($args['option_pay'] == 'Tarjeta_credito' || $args['option_pay'] == 'Pse'){

		 				$data .='
		 				"payer":{
		 					"fullName":"'.$customer->firstname.' '. $customer->lastname.'",
		 					"emailAddress":"'.$customer->email.'",
		 					"contactPhone":"'.((empty($address->phone_mobile)) ? 'N/A' : $address->phone_mobile) .'",
		 					"dniNumber":"'.$dni.'",
		 					"billingAddress":{
		 						"street1":"'.((empty($address->address1)) ? 'N/A' : $address->address1).'",
		 						"street2":"'.((empty($address->address2)) ? 'N/A' : $address->address2).'",
		 						"city":"'.$address->city.'",
		 						"state":"'.$conf->get_state($address->id_state).'",
		 						"country":"'. $context->country->iso_code .'",
		 						"postalCode":"'.((empty($address->postcode)) ? '00000': $address->postcode).'",
		 						"phone":"'.((empty($address->phone)) ? 'N/A': $address->phone) .'"
		 					}
		 				},';
		 				if($args['option_pay'] == 'Tarjeta_credito'){        
		 					$data .='
		 					"creditCard":{
		 						"number":"'.$args['numerot'].'",
		 						"securityCode":"'.$args['codigot'].'",
		 						"expirationDate":"'.$args['date'].'",
		 						"name":"'.$args['nombre'] .'"
		 					},
		 					"extraParameters":{
		 						"INSTALLMENTS_NUMBER":'.$args['cuotas'].'
		 					},';
		 				}
		 			}



		 			$data .='        
		 			"type":"AUTHORIZATION_AND_CAPTURE",
		 			"paymentMethod":"'.(($args['option_pay'] == 'Tarjeta_credito') ? PasarelaPagoCore::getFranquicia($args['numerot'],$conn['nombre_pasarela']) : strtoupper($args['option_pay'])).'",
		 			"paymentCountry":"'. $context->country->iso_code .'",
		 			"deviceSessionId":"'.$_deviceSessionId.'",
		 			"ipAddress":"'.$_SERVER['REMOTE_ADDR'].'",
		 			"userAgent":"'.$_SERVER['HTTP_USER_AGENT'].'",
		 			"cookie":"'.md5($context->cookie->timestamp).'"';

		 			if($args['option_pay'] == 'Pse'){

		 				$data .=',
		 				"extraParameters":{
		 					"PSE_REFERENCE1":"' . $_SERVER['REMOTE_ADDR'] . '",
		 					"FINANCIAL_INSTITUTION_CODE":"' . $args['pse_bank'] . '",
		 					"FINANCIAL_INSTITUTION_NAME":"' . $args['name_bank'] . '",
		 					"USER_TYPE":"' . $args['pse_tipoCliente'] . '",
		 					"PSE_REFERENCE2":"' . $args['pse_docType'] . '",
		 					"PSE_REFERENCE3":"' . $args['pse_docNumber'] . '"
		 				}';
		 			}			

		 			$data .='},
		 			"test":'.(($conn['produccion'] == 1) ? 'false' : 'true').'

		 		}';
		 		//echo "<textarea>".$data.'</textarea>'; exit();
		 		$response_Payu = $conf->sendJson($data);

		// Eliminado datos sensibles

		 		$subs = substr($args['numerot'], 0, (strlen($args['numerot']) - 4));
		 		$nueva = '';
		 		for ($i = 0; $i <= strlen($subs); $i++) {
		 			$nueva = $nueva . '*';
		 		}

		 		$data = str_replace('"number":"' . $subs, '"number":"' . $nueva, $data);
		 		$data = str_replace('"securityCode":"' . $args['codigot'], '"securityCode":"' . '****', $data);

		 		return PasarelaPagoCore::validatePayu($response_Payu, $data,$args);
		 	}

		/**
		 * Valida la transacción enviada a Payu y retorna en estado para orden
		 */

		public static function validatePayu($response, $data, $args)
		{
			$conf = new ConfPayu();
			if (!empty($response['transactionResponse']['state']) && ($response['transactionResponse']['state'] === 'PENDING' || $response['transactionResponse']['state'] === 'APPROVED')){
				$conf->pago_payu($args['id_order'], $args['id_customer'], $data, $response, $args['option_pay'], $response['code'], $args['id_cart'], $args['id_address_invoice']);	
				if($response['transactionResponse']['state'] == 'PENDING')	
					return (int) Configuration::get('PAYU_WAITING_PAYMENT');

				if($response['transactionResponse']['state'] == 'APPROVED')
					return (int) Configuration::get('PS_OS_PAYMENT'); 
			}
			else {
				$conf->error_payu($args['id_order'], $args['id_customer'], $data, $response, $args['option_pay'], $response['code'], $args['id_cart'], $args['id_address_invoice']);
				return (int) Configuration::get('PS_OS_ERROR');
			}
		}

		/**
		 * Retorna el la franquicia a la que pertenece un numero de TC
		 */
		public static function getFranquicia($cart_number, $pasarela){
			require_once(_PS_MODULE_DIR_ . 'payulatam/creditcards.class.php');

			$arraypaymentMethod =  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AMEX','MASTERCARD'=>'MASTERCARD');
			$arraypaymentMethod2 =  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AmEx','MASTERCARD'=>'MasterCard', 'DinersClub'=>'DinersClub','UnionPay'=>'UnionPay');
			$CCV = new CreditCardValidator();
			$CCV->Validate($cart_number);
			$key = $CCV->GetCardName($CCV->GetCardInfo()['type']); 
			// print_r($key); exit();
			if($CCV->GetCardInfo()['status'] == 'invalid'){
				return json_encode(array('ERROR'=>'El numero de la tarjeta no es valido.'));
			}
			$str_out = '';
			switch ($pasarela) {
				case 'payulatam':
				return ((array_key_exists(strtoupper($key), $arraypaymentMethod)) ? $arraypaymentMethod[strtoupper($key)] : 'N/A'); 
				break;
				default:
				return ((array_key_exists(strtoupper($key), $arraypaymentMethod2[strtoupper($key)])) ? $arraypaymentMethod2[strtoupper($key)] : 'N/A'); 
				break;
			}
			return 'No encontrada';

		}

		/**
		 * Envía la solicitud de pago a la pasarela asociada al medio de pago
		 */
		public static function payOrder($args){
			
			$conn = PasarelaPagoCore::GetDataConnect($args['option_pay']);
			PasarelaPagoCore::add_relationship_mediosp_cart($args['option_pay']);

			switch ($conn['nombre_pasarela']) {
				case 'payulatam':
				return PasarelaPagoCore::EnviarPagoPayu($args,$conn); 
				break;
				case 'redeban':
				return	PasarelaPagoCore::EnviarPagoRedeBan($args['option_pay'],$parameters,$conn);
				break;
				case 'openpay':
				return	PasarelaPagoCore::EnviarPagoOpenPay($args,$conn);
				break;							
				
				default:
					# code...
				break;
			}
		}

		private static function add_relationship_mediosp_cart($option_pay){
			$sql = "SELECT mp.id_medio_de_pago as id_mp, pp.id_pasarela as id_pp
			FROM
			"._DB_PREFIX_."medios_de_pago mp INNER JOIN "._DB_PREFIX_."pasarelas_pago pp ON (mp.pasarela = pp.nombre)
			WHERE mp.nombre = '".$option_pay."';";
			if($rs = Db::getInstance()->Executes($sql)){ 
				$context = Context::getContext();
				$sql = "INSERT INTO "._DB_PREFIX_."mediosp_cart (id_pasarela, id_medio_de_pago, id_cart,date) VALUES (".$rs[0]['id_mp'].",".$rs[0]['id_pp'].",".$context->cart->id.",'".date( 'Y-m-d H:i:s')."');";
				                                                                 //exit(json_encode($sql));                                     
				Db::getInstance()->Execute($sql);
			}
		}

		/**
		 * obtiene información de la orden  
		 */

		public static function getDataOrder($link){
			$query =  "SELECT link.id_order, orden.id_cart, orden.id_customer, orden.id_address_delivery, orden.id_address_invoice, orden.total_paid, orden.current_state, pagos.extras, pagos.message, pagos.method, pagos.idps_pagos_payu,
			response.message AS mss_confirm, response.transactionId
			FROM "._DB_PREFIX_."order_link_pay link 
			INNER JOIN "._DB_PREFIX_."orders orden ON(link.id_order = orden.id_order)
			LEFT JOIN "._DB_PREFIX_."pagos_payu pagos ON(orden.id_cart = pagos.id_cart)
			LEFT JOIN "._DB_PREFIX_."log_payu_response response ON (pagos.orderIdPayu = response.orderIdPayu)
			WHERE order_hash = '".$link."';"; 
			return  $row = Db::getInstance()->getRow($query);
		}

		public static function get_bank_pse(){
			
			$conn = PasarelaPagoCore::GetDataConnect('Pse');
			$request='{
				"language":"es",
				"command":"GET_BANKS_LIST",
				"merchant":{
					"apiLogin":"'.$conn['apilogin_id'].'",
					"apiKey":"'.$conn['apikey_privatekey'].'"
				},
				"test":false,
				"bankListInformation":{
					"paymentMethod":"PSE",
					"paymentCountry":"CO"
				}
			}';


			$xml_send='
			<request>
			<language>es</language>
			<command>GET_BANKS_LIST</command>
			<merchant>
			<apiLogin>'.$conn['apilogin_id'].'</apiLogin>
			<apiKey>'.$conn['apikey_privatekey'].'</apiKey>
			</merchant>
			<isTest></isTest>
			<bankListInformation>
			<paymentMethod>PSE</paymentMethod>
			<paymentCountry>CO</paymentCountry>
			</bankListInformation>
			</request>';
			$bancos = array();

			$conf =  new ConfPayu();
			$PayuBanks = $conf->sendXml($xml_send)['bankListResponse']['banks'][0]['bank'];
			$array_baks = NULL;
			foreach ($PayuBanks as $row){
				$array_baks[] = array('value' => $row['pseCode'], 'name' => $row['description']);	
			}
			return $array_baks;
		}

		/**
		 * Enviar pago a la  Pasarela OpenPay
		 */

		private static function EnviarPagoOpenPay($args, $conn){
			           // OpenPay //

			$openPay = new OpenpayController();
			$conf = new ConfPayu();
			$intentos = $conf->count_pay_cart($this->context->cart->id);

			if($openPay->add_charge($args,$intentos) ) {                  	

				if ( $openPay->get_status() == 'completed' ) {

					return (int) Configuration::get('PS_OS_PAYMENT');

				} elseif ( $openPay->get_status() == 'in_progress' ) {

					return (int) Configuration::get('OPENPAY_WAITING_PAYMENT');

				} else {
					$openPay->add_log_error();
					return (int) Configuration::get('PS_OS_ERROR');
				}
			} else {
				$openPay->add_log_error();
				return (int) Configuration::get('PS_OS_ERROR');
			}

		}
		/**
		 * Retornar kesys de Open Pay requeridas para el formulario de TC
		 */
		public static function get_keys_open_pay($option_pay){
			$conn = PasarelaPagoCore::GetDataConnect($option_pay);
			return array('id'=>$conn ['apilogin_id'],'public_key'=>$conn ['pseco_publickey'],'production'=> (boolean)$conn ['produccion']);
		}

			/**
			 * Retorna la respuesta de la pasarela dependiendo del medio de pago
			 */
			public static function get_post_pay($id_cart){

			}
			/**
			 * Retorna variables extras payulatam
			 */
			public static function get_extra_vars_payu($id_cart,$method){

				$extra_vars =  array();
				$sql = "SELECT json_response 
				FROM "._DB_PREFIX_."pagos_payu 
				WHERE id_cart =".(int) $id_cart;

				if($rs = Db::getInstance()->getValue($sql)){
					$response = json_decode(stripslashes($rs),TRUE);
					

					if (isset($response['transactionResponse']['extraParameters']['BAR_CODE'])) {
						$extra_vars =  array('method'=>$method,
						                     'cod_pago'=>$response['transactionResponse']['extraParameters']['REFERENCE'],
						                     'fechaex'=> date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)),
						                     'bar_code'=>$response['transactionResponse']['extraParameters']['BAR_CODE']);

					}elseif (isset($response['transactionResponse']['extraParameters']['URL_PAYMENT_RECEIPT_HTML'])) {
						$extra_vars =  array('method'=>$method,
						                     'cod_pago'=>$response['transactionResponse']['extraParameters']['REFERENCE'],
						                     'fechaex'=> date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)));
					}

				}
				return $extra_vars;
			}

	/**
			 * 
			 */		
// función que genera una cadena aleatoria
	public static function randString ($length = 32)
	{  
		$string = "";
		$possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
		$i = 0;
		while ($i < $length)
		{    
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$string .= $char;    
			$i++;  
		}  
		return $string;
	}

	public static function get_total_tax($id_cart){
		$sql="SELECT total_tax
		FROM ps_cart
		WHERE id_cart =".(int) $id_cart;
		return (float)Db::getInstance()->getValue($sql);

	}	

}
