<?php 
require_once('classes/Rest.inc.php');
require_once('classes/Model.php');

class API extends REST {

	public $id_lang_default = 0;

	public function __construct() 
	{
		parent::__construct(); // Init parent contructor
		$this->id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
	}

	/**
	 * Método público para el acceso a la API.
	 * Este método llama dinámicamente el método basado en la cadena de consulta
	 *
	 */
	public function processApi()
	{
		$func = strtolower(trim(str_replace("/","",$_REQUEST['rquest'])));
		if((int)method_exists($this,$func) > 0)
			$this->$func();
		else
			$this->response('',404); // If the method not exist with in this class, response would be "Page not found".
	}

	/**
	 * Codifica el array en un JSON
	 */
	private function json($data)
	{
		if(is_array($data)){
			return json_encode($data);
		}
	}

	/** 
	 * Productos API
	 * Consulta de los productos debe ser por método GET
	 * expr : <Nombre del producto o referencia>
	 * page_number : <Número de página>
	 * page_size : <Filas por página>
	 * order_by : <Ordenar por ascendente ó descendente>
	 * order_way : <Ordenar por campo>
	 */
	private function search()
	{
		// Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
		if ($this->get_request_method() != "GET") {
			$this->response('', 406);
		}

		$expr        = $this->_request['expr'];
		$page_number = $this->_request['page_number'];
		$page_size   = $this->_request['page_size'];
		$order_by    = $this->_request['order_by'];
		$order_way   = $this->_request['order_way'];

		$model = new Model();
		$result = $model->productSearch($this->id_lang_default, $expr, $page_number, $page_size, $order_by,	$order_way);

		if (empty($result)) {
			// Si no hay registros, estado "Sin contenido"
			$this->response('', 204);
		} else {
			// Si todo sale bien, enviará cabecera de "OK" y la lista de la búsqueda en formato JSON
			$this->response($this->json($result), 200);
		}
	}

	/** 
	 * Inicio de sesión
	 * Válida credenciales de usuario, si todo sale bien agrega el usuario al contexto
	 * email : <Correo eléctronico>
	 * pwd : <Contraseña>
	 */
	private function login()
	{
		// Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
		if($this->get_request_method() != "POST") {
			$this->response('',406);
		}

		$email = $this->_request['email'];
		$password = $this->_request['pwd'];

		// Validaciones de entrada
		if(!empty($email) and !empty($password)) {
			if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$customer = new Customer();
				$authentication = $customer->getByEmail(trim($email), trim($password));
				
				if (!$authentication || !$customer->id) {
					// Error de autenticación
					$this->response('', 204);	// Si no hay registros, estado "No Content"
				} else {
					$context = Context::getContext();
					$context->cookie->id_compare = isset($context->cookie->id_compare) 
					? $context->cookie->id_compare
					: CompareProduct::getIdCompareByIdCustomer($customer->id);
					$context->cookie->id_customer = (int)($customer->id);
					$context->cookie->customer_lastname = $customer->lastname;
					$context->cookie->customer_firstname = $customer->firstname;
					$context->cookie->logged = 1;
					$customer->logged = 1;
					$context->cookie->is_guest = $customer->isGuest();
					$context->cookie->passwd = $customer->passwd;
					$context->cookie->email = $customer->email;

					// Agrega el cliente a el contexto
					$context->customer = $customer;

					// Si todo sale bien, enviará cabecera de "OK" y los detalles del usuario en formato JSON
					unset($customer->passwd, $customer->last_passwd_gen);
					$gender = $customer->id_gender  == 1 ? 'M' : ($customer->id_gender  == 2 ? 'F' : '');
					$this->response($this->json(array('id' => (int) $customer->id,'lastname' => $customer->lastname, 'firstname' => $customer->firstname, 'email' => $customer->email,'newsletter' => (bool)$customer->newsletter,'identification' => $customer->identification,'gender' => $gender,'id_type' => (int)$customer->id_type)), 200);
				}
			}
		}

		// Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
		$this->response($this->json(array(
		                "success" => false, 
		                "message" => "Dirección de correo electrónico o contraseña no válidos"
		                )), 400);
	}

	private function logout()
	{
		$context = Context::getContext();
		$context->customer->mylogout();
		$this->response('', 200);
	}

	private function test()
	{
		$context = Context::getContext();
		$this->response($this->json((array) $context->customer), 200);
	}

	private function isLogin()
	{
		$context = Context::getContext();
		$this->response(json_encode($context->customer->isLogged()), 200);
	}

	private function categories($params)
	{
		$model = new Model();
		$this->response(json_encode($model->get_category(2,3)),200);
	}


	public function prodCategories() {

		// Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
		if ($this->get_request_method() != "GET") {
			$this->response('', 406);
		}    

		$ids         = $this->_request['ids'];
		$page_number = $this->_request['page_number'];
		$page_size   = $this->_request['page_size'];
		$order_by    = $this->_request['order_by'];
		$order_way   = $this->_request['order_way'];

		$ids_cats = explode(",", $ids);
		if(!is_array($ids_cats))
			$ids_cats[] = array((int)$ids_cats);

		$model = new Model();

		$result = $model->getProdCategories($ids_cats, $page_number,$page_size, $order_way,$order_by);

		if (empty($result)) {
			// Si no hay registros, estado "Sin contenido"
			$this->response('La categoría no existe o no tiene productos asociados.', 204);
		} else {
			// Si todo sale bien, enviará cabecera de "OK" y la lista de la búsqueda en formato JSON
			$this->response($this->json($result), 200);
		}


		//return $this->response($this->json($mugre), 200);
		//return $this->response(json_encode($model->getProdCategories($ids_cats, $page_number,$page_size, $order_way,$order_by)),200);

	}  


	private function header()
	{

	}

	private function myAccount()
	{

	}	
	private function orderHistory()
	{

	}

	private function footer()
	{

	}

	private function product() {

		if ($this->get_request_method() != "GET") {
			$this->response('', 406);
		}

		$id_prod        = $this->_request['id'];

		$model = new Model();
		//return $this->response(json_encode("XD"),200);
		return $this->response(json_encode($model->getProduct($id_prod)),200);
	}

	private function manufacturers(){
		$model = new Model();
		
		return $this->response(json_encode($model->manufacturers()),200);
	}

	private function createAccount(){

		// Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
		if ($this->get_request_method() != "POST") {
			$this->response('', 406);
		} 

		$arguments = array();
		$arguments['name']	= $this->_request['name'];
		$arguments['lastname']		= $this->_request['lastname'];
		$arguments['gender'] 			= $this->_request['gender'];
		$arguments['email']			= $this->_request['email'];
		$arguments['passwd']		= $this->_request['passwd'];
		$arguments['signon']		= $this->_request['signon'];			
		$arguments['news']			= $this->_request['news'];
		$arguments['dni']			= $this->_request['dni'];	


		if (Validate::isEmail($arguments['email']) && !empty($arguments['email'])){
			if(Customer::customerExists($arguments['email'])){
		// Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
				$this->response($this->json(array(
				                "success" => false, 
				                "message" => "No se pudo crear la cuenta, el (".$arguments['email']." ) email ya esta registrado"
				                )), 400);
			}
		}else{
			$this->response($this->json(array(
			                "success" => false, 
			                "message" => "se requiere un correo valido (".$arguments['email'].' )' 
			                )), 400);
		}
		if (!Validate::isPasswd($arguments['passwd']))
			$this->response($this->json(array(
			                "success" => false, 
			                "message" => "La contraseña no es valida, utiliza una contraseña con una longitud mínima de 5 caracteres." 
			                )), 400);	

		$model = new Model();
		if($customer = $model->createAccount($arguments)) {
			$this->response($this->json( $customer ),200);
		}

		$this->response($this->json(array(
		                "success" => false, 
		                "message" => "Error creando la cuenta."
		                )), 400);
		
	}

	private function addresses(){

			// Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
		if ($this->get_request_method() != "GET") {
			$this->response('', 406);
		} 

		$id_customer	= $this->_request['id_customer'];
		$model = new Model();		
		return $this->response(json_encode($model->get_address($id_customer)),200);	
		
	} 			

	private function addAddress(){


			// Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
		if ($this->get_request_method() != "POST") {
			$this->response('', 406);
		} 
		
		$arg = array();
		
		$arg['id_customer'] = $this->_request['id_customer'];
		$arg['id_country'] = $this->_request['id_country'];
		$arg['id_state'] = $this->_request['id_state'];
		$arg['alias'] = $this->_request['alias'];
		$arg['lastname'] = $this->_request['lastname'];
		$arg['firstname'] = $this->_request['firstname'];
		$arg['address1'] = $this->_request['address1'];
		$arg['address2'] = $this->_request['address2'];
		$arg['city'] = $this->_request['city'];
		$arg['phone'] = $this->_request['phone'];
		$arg['mobile'] = $this->_request['mobile'];
		$arg['dni'] = $this->_request['dni'];
		$arg['postcode'] = $this->_request['postcode'];	
		$arg['id_colonia'] = $this->_request['id_colonia'];
		$arg['is_rfc'] = $this->_request['is_rfc'];
		$arg['id_city'] = $this->_request['id_city'];

		$model = new Model();		
		return $this->response(json_encode($model->add_address($arg)),200);	
		
	}

	private function getPostCodeInfo() {

		if ($this->get_request_method() != "GET") {
			$this->response('', 406);
		} 
		
		$postcode	= $this->_request['postcode'];
		$model = new Model();

		return $this->response(json_encode($model->get_fromPostcode($postcode)),200);	

	}	


	private function getColoniaByIdCity() {

		if ($this->get_request_method() != "GET") {
			$this->response('', 406);
		} 
		
		$id_city	= $this->_request['id_city'];
		$model = new Model();

		return $this->response(json_encode($model->get_colonia_fromid_city($id_city)),200);	

	}

	private function countries() {

		if ($this->get_request_method() != "GET") {
			$this->response('', 406);
		} 
		
		$model = new Model();

		return $this->response(json_encode($model->get_countries()),200);	

	}
/**
 * 
 */
private function states(){
	if ($this->get_request_method() != "GET") {
		$this->response('', 406);
	} 
	$id_country = 	$this->_request['id_country'];

	$model = new Model();

	return $this->response(json_encode($model->get_states($id_country)),200);	
	
}
/**
 * 
 */
private function cities(){
	if ($this->get_request_method() != "GET") {
		$this->response('', 406);
	} 
	$id_state = 	$this->_request['id_state'];

	$model = new Model();

	return $this->response(json_encode($model->get_cities($id_state)),200);	
	
}

private function costoEnvio(){
	if ($this->get_request_method() != "GET") {
		$this->response('', 406);
	} 
	$id_city = 	$this->_request['id_city'];

	$model = new Model();

	return $this->response(json_encode($model->get_costo_envio($id_city)),200);		
}
/**
 * AddVoucher 
 */
private function cart(){

	if ($this->get_request_method() != "POST") {
		$this->response('', 406);
	}

	$param['products'] = 		$this->_request['products'];
	$param['id_customer'] = 	$this->_request['id_customer'];
	$param['discounts'] = 		$this->_request['discounts'];
	$param['deleteDiscount'] = 	$this->_request['deleteDiscount'];
	$param['id_address'] = 		$this->_request['id_address'];
	$param['msg'] = 			$this->_request['msg'];
	$param['id_cart'] = 		($this->_request['id_cart'] > 0 ? $this->_request['id_cart'] : NULL);		

	$model = new Model();
	return $this->response($this->json($model->cart($param['products'],$param['id_customer'],$param['id_address'],$param['discounts'],$param['deleteDiscount'],$param['msg'],$param['id_cart']),200));
}
/**
 * 
 */

public function pay(){
	$param['payment'] = 	$this->_request['payment'];
	$param['products'] = 	$this->_request['products'];
	$param['id_customer'] = 	$this->_request['id_customer'];
	$param['id_address'] = 	$this->_request['id_address'];
	$param['discounts'] = 		$this->_request['discounts'];
	$param['msg'] = 			$this->_request['msg'];
	$param['id_cart'] = 		($this->_request['id_cart'] > 0 ? $this->_request['id_cart'] : NULL);			

	$model = new Model();
	return $this->response($this->json($model->pay($param)),200);	
}

public function bankPse(){
	return $this->response($this->json(PasarelaPagoCore::get_bank_pse()),200);	
}

public function KeysOpenPay(){
	return $this->response($this->json(PasarelaPagoCore::get_keys_open_pay('Tarjeta_credito')),200);	
}

public function franquicia(){
	$cart_number = 	$this->_request['cart_number'];
	$this->response(json_encode( PasarelaPagoCore::getFranquicia($cart_number, 'payulatam')),200);
}

public function addImg(){
			// Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
	if($this->get_request_method() != "POST") {
		$this->response('',406);
	}

	//$str_img = 	$this->_request['str_img'];
	$option = 	$_REQUEST['option']; //$this->_request['option'];

	//error_log('|'.print_r($_REQUEST,true).'|', 0);
	$model = new Model();

	$flag = true;
	foreach ($_FILES as $key) {
		if(!$model->add_image($key,$option)){
			$flag = false;
			break;
		}
	}
	$this->response(json_encode(array('success'=>$flag)),200);
}

public function password(){
	if ($this->get_request_method() != "POST") {
		$this->response('', 406);
	}
	$model = new Model();
	$email = $this->_request['email'];
//exit(json_encode($email));
	return $this->response($this->json($model->password($email)),200);	
}


}


// Access-Control-Allow-Origin | CORS
//header("Access-Control-Allow-Origin: *");
//header("Access-Control-Allow-Credentials: true");
//header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Key, Authorization");
//header("Access-Control-Allow-Headers: Content-Type,x-prototype-version,x-requested-with");
//header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, PATCH");
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

// Iniciar
$api = new API;
$api->processApi();

?>