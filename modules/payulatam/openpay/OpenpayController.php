<?php
require_once(dirname(__FILE__) . '/../../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/openpay/Openpay.php');

class OpenpayController extends FrontController {

	protected $open_pay;
	protected $customer;
	protected $card;
	protected $errors_op = array();
	protected $status;


	public function __construct($id = null,$api_key = null){
		if(empty($id) || empty($api_key)){
			$payulatam = new PayULatam();	
			//$keys = $payulatam->get_keys_pasarela();
			$keys = PasarelaPagoCore::GetDataConnect("Tarjeta_credito");
			//error_log('|'.print_r($keys,TRUE).'|',0);
			$this->open_pay = Openpay::getInstance($keys['apilogin_id'], $keys['apikey_privatekey']);
				Openpay::setProductionMode((boolean)$keys['produccion']);

		//$this->open_pay->setSandboxMode(TRUE);

		}else{
			$this->open_pay = Openpay::getInstance($id, $api_key);
			$this->open_pay->setSandboxMode(FALSE);
		}

		parent::__construct();

	}	


	protected   function randomNumber($length) {
		$result = '';

		for($i = 0; $i < $length; $i++) {
			$result .= mt_rand(0, 9);
		}

		return $result;
	}

/**
 * Agregar un comprador en nuestra cuenta OpenPay
 */
protected function add_customer()
{ 
	$this->context = Context::getContext();
	$address = new Address($this->context->cart->id_address_delivery);
	$customer = new Customer($this->context->cart->id_customer);

	$customerData = array(
	                      'external_id' => md5($customer->id).'-'.$this->randomNumber(7),
	                      'name' => $customer->firstname,
	                      'last_name' => $customer->lastname,
	                      'email' => $customer->email,
	                      'requires_account' => false,
	                      'phone_number' => substr($address->phone_mobile.' '.$address->phone,0,30),
	                      'address' => array(
	                                         'line1' => substr(utf8_encode($address->address1),0,199),
	                                         'line2' => substr(utf8_encode($address->address2),0,49),
	                                         'line3' => substr(utf8_encode($address->other),0,49),
	                                         'state' => State::getNameById($address->id_state),
	                                         'city' => utf8_encode($address->city),
	                                         'postal_code' => $address->postcode,
	                                         'country_code' => $this->context->country->iso_code
	                                         )
	                      );

// error_log('<$chargeRequest '.print_r($customerData,TRUE).' (customer)>',0); 

try {
	//$var = $customer = $openpay->customers->add($customerData);
	//echo('<pre>'.print_r($customerData,true).'</pre><br><br>'); exit();

	$ob_customer = $this->open_pay->customers->add($customerData);

/*
	

	$datetime_f = new DateTime($ob_customer->creation_date);
	$datetime_format = $datetime_f->format('Y-m-d H:i:s');

// 	$sql =	"INSERT INTO `"._DB_PREFIX_."openpay_customer` (`id_customer`, `id`, `creation_date`, `status`, `balance`, `clabe`)
	$sql =	"INSERT INTO `"._DB_PREFIX_."openpay_customer` (`id_customer`, `id`, `creation_date`, `clabe`)
		 	VALUES (".$customer->id.", '".$ob_customer->id."', '".$datetime_format."', ".$ob_customer->clabe." );";
//echo('<pre>'.print_r($sql,true).'</pre><br><br>'); exit();
	    if (Db::getInstance()->Execute($sql) ) {
	   		
	   		$this->customer=$ob_customer ;
	   		return TRUE;
	    } else {
	    	// Logger::AddLog('Openpay add_customer, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.' | ERROR en la Creación del Usuario PS-OpenPay, Validar Id_customer Duplicado -> ps_openpay_customer ' , 2, null, null, null, true);
	    }*/
	    $this->customer=$ob_customer ;  
	    return true;

	} catch (OpenpayApiRequestError $e) { error_log('<Error de petición Openpay (customer)>',0);
		$this->errors_op[] = $this->get_message_error($e->getErrorCode());
		Logger::AddLog('Openpay add_customer, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.'  ERROR en la petición: ' . $e->getMessage(), 2, null, null, null, true);
		return FALSE;
	} catch (OpenpayApiConnectionError $e) { error_log('<Error de conexión Openpay (customer)>',0);
		$this->errors_op[] = $this->get_message_error($e->getErrorCode());
		Logger::AddLog('Openpay add_customer, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.' ERROR en la conexión al API: ' . $e->getMessage(), 2, null, null, null, true);
		return FALSE;
	} catch (OpenpayApiAuthError $e) { error_log('<Error de autenticación Openpay. (customer)>',0);
		$this->errors_op[] = $this->get_message_error($e->getErrorCode());
		Logger::AddLog('Openpay add_customer, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.' ERROR en la autenticación: ' . $e->getMessage(), 2, null, null, null, true);	
		return FALSE;	
	} catch (OpenpayApiError $e) { error_log('<Error en el API Openpay (customer)>',0);
		$this->errors_op[] = $this->get_message_error($e->getErrorCode());
		Logger::AddLog('Openpay add_customer, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.' ERROR en el API: ' . $e->getMessage(), 2, null, null, null, true);		
		return FALSE;	
	} catch (Exception $e) { error_log('<Error en el script Openpay (customer)>',0);
		exit(print_r($e->getMessage(),true));
		$this->errors_op[] = $this->get_message_error($e->getMessage());
		Logger::AddLog('Openpay add_customer, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.'return FALSE; Error en el script: ' . $e->getMessage(), 2, null, null, null, true);	
		return FALSE;
	}
	
}

	/**
	 * Valida si el customer esta registrado en openpay, en caso de no estarlo se creara en openpay
	 */
	protected function load_customer() {
		$customer = new Customer($this->context->cart->id_customer);
		$sql="select id,`status` FROM ps_openpay_customer
		WHERE id_customer = ".$customer->id;
		$row = Db::getInstance()->getRow($sql);

		if ( !empty($row) && $row['id'] != '' && $row['status'] != '' ) {  //  

			$this->get_customer_ws($row['id']);
			return TRUE;

		} else {

			if ($this->add_customer()) {
				return TRUE;
			}

		}
		return FALSE;
	}

/**
 *  Solicita al ws el customer con id. Retorna un objeto customer openapay|
 */
public function get_customer_ws($id_customer){
	return $this->customer = $this->open_pay->customers->get($id_customer);
}
/**
 * Retorna el objeto customer openpay 
 */
public function get_customer(){
	return $this->customer;
}
/**
 * Agregar un pago con tarjeta de crédito
 */
public function add_charge($post,$contador){
	error_log('hola3');
$this->context = Context::getContext();
	// if($this->load_customer()){
	if($this->add_customer()){

		$customer = new Customer($this->context->cart->id_customer);
	//$currency = Tools::safeOutput( new Currency(self::$cart->id_currency)->iso_code);
		$address = new Address($this->context->cart->id_address_delivery);

$chargeRequest = array(
                       'method' => 'card',
                       'source_id' => $post["token_id"],
                       'amount' => $this->context->cart->getOrderTotal(),
                       'currency' => 'MXN',
                       'description' => 'Compra en '.Configuration::get('PS_SHOP_NAME').' '.$this->context->country->iso_code,
                       'order_id' => Configuration::get('PS_SHOP_NAME').'-'.$this->context->country->iso_code.'-'.$this->context->cart->id.'-'.$contador,
                       'device_session_id' => $post["openpay_device_session_id"],
                       'metadata' => array(
                                           'address1' 		=> substr(utf8_encode($address->address1),0,30),
                                           'address2' 		=> substr(utf8_encode($address->address2),0,30),
                                           'address3' 		=> (isset($address->other) && $address->other != '')?substr(utf8_encode($address->other),0,30):FALSE,
                                           'phone'			=> substr(utf8_encode($address->phone_mobile .' '.$address->phone),0,30),
                                           'fecha_compra' 	=> date("Y-m-d H:i:s"), 
                                           'total' 		=> $this->context->cart->getOrderTotal(),
                                           'descuento' 	=> $this->context->cart->getOrderTotal( Cart::ONLY_DISCOUNTS ),
                                           'iva' 			=> $this->context->cart->getOrderTotals()['total_iva'],
                                           'nombre' 		=> substr(utf8_encode($customer->firstname.' '.$customer->lastname),0,30),
                                           'email' 		=> substr($customer->email,0,30)
                                           )
);

//error_log('<$chargeRequest '.print_r($chargeRequest,TRUE).' (charge)>',0);  
try {

	if($this->add_transaction($this->customer->charges->create($chargeRequest))){
		return TRUE;	
	}

}  catch (OpenpayApiRequestError $e) { error_log('<Error en el script Openpay (charge)>',0); 
	$this->errors_op[] = $this->get_message_error($e->getErrorCode());
	Logger::AddLog('Openpay add_charge, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.'  ERROR en la petición: ' . $e->getMessage(), 2, null, null, null, true);
	error_log($e);
	return FALSE;
} 	catch (OpenpayApiConnectionError $e) { error_log('<Error en el script Openpay (charge)>',0);
	$this->errors_op[] = $this->get_message_error($e->getErrorCode());
	Logger::AddLog('Openpay add_charge, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.' ERROR en la conexión al API: ' . $e->getMessage(), 2, null, null, null, true);
	error_log('  ERROR en la conexión al API ');
	return FALSE;
} 	catch (OpenpayApiAuthError $e) { error_log('<Error en el script Openpay (charge)>',0);
	$this->errors_op[] = $this->get_message_error($e->getErrorCode());
	Logger::AddLog('Openpay add_charge, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.' ERROR en la autenticación: ' . $e->getMessage(), 2, null, null, null, true);	
	error_log(' ERROR en la autenticación:');
	return FALSE;	
} 	catch (OpenpayApiError $e) { error_log('<Error en el script Openpay (charge)>',0);
	$this->errors_op[] = $this->get_message_error($e->getErrorCode());
	Logger::AddLog('Openpay add_charge, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.' ERROR en el API: ' . $e->getMessage(), 2, null, null, null, true);		
	error_log('  ERROR en el API ');
	return FALSE;	
} 	catch (Exception $e) { error_log('<Error en el script Openpay (charge)>',0);
	$this->errors_op[] = $e->getMessage();
	Logger::AddLog('Openpay add_charge, customer:'.$customer->id.', id_cart:'.$this->context->cart->id.'return FALSE; Error en el script: ' . $e->getMessage(), 2, null, null, null, true);	
	error_log(' return FALSE; Error en el script:');
	return FALSE;
}
}

return FALSE;
}

/**
 * Almacenar una transacción en la base de datos
 */
protected function add_transaction($trans){
	error_log(print_r($trans,true));
	// echo ('<br><pre>add_transaction: '.print_r($trans,true).'<pre>');

	$this->status = $trans->status; 

	$datetime_f = new DateTime($trans->creation_date);
	$datetime_format = $datetime_f->format('Y-m-d H:i:s');

	//echo "<br> tx: ". 
	$sql="INSERT INTO `"._DB_PREFIX_."openpay_transaction` (`id`,
	                                                        `authorization`,
	                                                        `transaction_type`,
	                                                        `operation_type`,
	                                                        `method`,
	                                                        `creation_date`,
	                                                        `order_id`,
	                                                        `status`,
	                                                        `amount`,
	                                                        `description`,
	                                                        `error_message`,
	                                                        `op_customer_id`,
	                                                        `currency`,
	                                                        `bank_code`,
	                                                        `card_number`,
	                                                        `id_cart`)
VALUES ('".$trans->id."',
        '".$trans->authorization."',
        '".$trans->transaction_type."',
        '".$trans->operation_type."',
        '".$trans->method."',
        '".$datetime_format."',
        '".$trans->order_id."',
        '".$trans->status."',
        '".$trans->amount."',
        '".$trans->description."',
        '".$trans->error_message."',
        '".$trans->customer_id."',
        '".$trans->currency."',
        '".$trans->card->bank_code."',
        '".$trans->card->card_number."',
        ".$this->context->cart->id.");";

if (Db::getInstance()->Execute($sql) ) { 
	$this->customer=$customer_id ;
	return TRUE;
} 
return FALSE;

}
/**
 * Agregar carrito a la sonda 
 */
public function add_sonda($cart){

}
/**
 * Actualizar ordenes en estado pendiente de pago
 */
public function update_pendyng_orders (){

}

/**
 * Retorna las ordenes en estado pendiente de pago realizadas por OpenPay
 */
protected function get_pendyng_orders(){

}

public function get_open_pay(){
	return $this->open_pay;
}

public function set_open_pay($open_pay){
	$this->open_pay=$open_pay;
}

/**
 * Retorna código y mensaje de error
 */
protected function get_message_error($error_ceode){

	$errors = array(	
	                1000 => 'Ocurrió un error interno en el servidor de Openpay',
	                1001 => 'El formato de la petición no es JSON, los campos no tienen el formato correcto, o la petición no tiene campos que son requeridos.',
	                1002 => 'La llamada no esta autenticada o la autenticación es incorrecta.',
	                1003 => 'La operación no se pudo completar por que el valor de uno o más de los parámetros no es correcto.',
	                1004 => 'Un servicio necesario para el procesamiento de la transacción no se encuentra disponible.',
	                1005 => 'Uno de los recursos requeridos no existe.',
	                1006 => 'Ya existe una transacción con el mismo ID de orden.',
	                1007 => 'La transferencia de fondos entre una cuenta de banco o tarjeta y la cuenta de Openpay no fue aceptada.',
	                1008 => 'Una de las cuentas requeridas en la petición se encuentra desactivada.',
	                1009 => 'El cuerpo de la petición es demasiado grande.',
	                1010 => 'Se esta utilizando la llave pública para hacer una llamada que requiere la llave privada, o bien, se esta usando la llave privada desde JavaScript.',
	                2001 => 'La cuenta de banco con esta CLABE ya se encuentra registrada en el cliente.',
	                2002 => 'La tarjeta con este número ya se encuentra registrada en el cliente.',
	                2003 => 'El cliente con este identificador externo (External ID) ya existe.',
	                2004 => 'El dígito verificador del número de tarjeta es inválido de acuerdo al algoritmo Luhn.',
	                2005 => 'La fecha de expiración de la tarjeta es anterior a la fecha actual.',
	                2006 => 'El código de seguridad de la tarjeta (CVV2) no fue proporcionado.',
	                2007 => 'El número de tarjeta es de prueba, solamente puede usarse en Sandbox.',
	                4001 => 'Preconditon Failed',                
	                3001 => 'La tarjeta fue declinada.',
	                3002 => 'La tarjeta ha expirado.',
	                3003 => 'La tarjeta no tiene fondos suficientes.',
	                3004 => 'La tarjeta ha sido identificada como una tarjeta robada, Se ha dado aviso a la autoridad competente.',
	                3005 => 'No hemos podido completar su compra. La tarjeta o transacción no ha sido identificada.',
	                3008 => 'La tarjeta no es soportada en transacciones en linea.',
	                3009 => 'La tarjeta fue reportada como perdida, Se ha dado aviso a la autoridad competente.',
	                3010 => 'El banco ha restringido la tarjeta.',
	                3011 => 'El banco ha solicitado que la tarjeta sea retenida. Contacte al banco, Se ha dado aviso a la autoridad competente.',
	                3012 => 'Se requiere solicitar al banco autorización para realizar este pago.');

if (array_key_exists($error_ceode, $errors)) {
	return array($error_ceode,$errors[$error_ceode]);
}else{
	return array(9999,'Error inesperado.');
}

}

// retorna listado de errores
public function get_errors()
{
	return $this->errors_op;
}

/**
 * Retorna el estado de la transacción 
 */
public function get_status(){
	return $this->status;
}

public function add_log_error(){

	if(count($this->get_errors()) > 0){
		$sql =	"INSERT INTO `"._DB_PREFIX_."error_pay` ( `id_cart`, `errors`, `date`)
		VALUES (".$this->context->cart->id.", '".json_encode($this->get_errors())."', '".date("Y-m-d H:i:s")."');";
		if (Db::getInstance()->Execute($sql) ) {
			return true;
		}
		return true;
	}
	return false;
}


}