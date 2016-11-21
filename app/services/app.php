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
    if ((int)method_exists($this,$func) > 0)
      $this->$func();
    else
      $this->response('',404); // If the method not exist with in this class, response would be "Page not found".
  }

  /**
   * Codifica el array en un JSON
   */
  private function json($data)
  {
    if (is_array($data)) {
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
    $result = $model->productSearch($this->id_lang_default, $expr, $page_number, $page_size, $order_by,  $order_way);

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
    if ($this->get_request_method() != "POST") {
      $this->response('',406);
    }

    $email = $this->_request['email'];
    $password = $this->_request['pwd'];

    // Validaciones de entrada
    if (!empty($email) 
        && !empty($password)) {
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $customer = new Customer();
        $authentication = $customer->getByEmail(trim($email), trim($password));
        
        if (!$authentication 
            || !$customer->id) {
          // Error de autenticación
          $this->response('', 204);  // Si no hay registros, estado "No Content"
        } else {
          $context = Context::getContext();
          $context->cookie->id_compare = isset($context->cookie->id_compare) 
                                       ? $context->cookie->id_compare
                                       : CompareProduct::getIdCompareByIdCustomer($customer->id);
          $context->cookie->id_customer = (int)($customer->id);
          $context->cookie->customer_lastname = $customer->lastname;
          $context->cookie->customer_firstname = $customer->firstname;
          $context->cookie->is_guest = $customer->isGuest();
          $context->cookie->passwd = $customer->passwd;
          $context->cookie->email = $customer->email;
          $context->cookie->logged = 1;
          $customer->logged = 1;

          // Agrega el cliente a el contexto
          $context->customer = $customer;

          // Si todo sale bien, enviará cabecera de "OK" y los detalles del usuario en formato JSON
          unset($customer->passwd, $customer->last_passwd_gen);
          $gender = $customer->id_gender  == 1 ? 'M' : ($customer->id_gender  == 2 ? 'F' : '');
          $this->response($this->json(array(
            'id' => (int) $customer->id,
            'lastname' => $customer->lastname, 
            'firstname' => $customer->firstname, 
            'email' => $customer->email,
            'newsletter' => (bool)$customer->newsletter,
            'identification' => $customer->identification,
            'gender' => $gender,
            'id_type' => (int)$customer->id_type
          )), 200);
        }
      }
    }

    // Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
    $this->response($this->json(array(
      "success" => false, 
      "message" => "Dirección de correo electrónico o contraseña no válidos"
    )), 400);
  }

  /**
   * Cierra sesión
   */
  private function logout()
  {
    $context = Context::getContext();
    $context->customer->mylogout();
    $this->response('', 200);
  }

  /**
   * Comprueba si el usuario esta autenticado
   */
  private function isLogin()
  {
    $context = Context::getContext();
    $this->response(json_encode($context->customer->isLogged()), 200);
  }

  /**
   * Devuelve la lista de categorías
   */
  private function categories()
  {
    $model = new Model();
    $this->response(json_encode($model->get_category(2,3)),200);
  }

  /** 
   * Productos por Categoria(s)
   * Consulta de los productos por categoria(s)
   * ids : <Id categoria(s)>
   * page_number : <Número de página>
   * page_size : <Filas por página>
   * order_by : <Ordenar por ascendente ó descendente>
   * order_way : <Ordenar por campo>
   */
  public function prodCategories()
  {
    // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $requestData = array('ids', 'page_number', 'page_size', 'order_by', 'order_way');
    foreach ($requestData as $rqd) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : "";
    }

    $ids_cats = explode(",", $ids);
    if (!is_array($ids_cats))
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
  }

  /**
   * Crea/Procesa el carrito de compras
   * products : <>
   * id_customer : <>
   * id_address : <>
   * discounts : <>
   * deleteDiscount : <>
   * id_cart : <>
   * msg : <>
   * clear : <>
   */
  private function cart()
  {
    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }

    $requestData = array('products', 'id_customer', 'id_address', 'discounts', 'deleteDiscount', 'msg', 'id_cart', 'clear');
    foreach ($requestData as $rqd) {
      ${$rqd} = isset($this->_request[$rqd]) ? $this->_request[$rqd] : "";
    }
    $id_cart = $id_cart > 0 ? $id_cart : NULL;
    $clear = !empty($clear) ? (boolean) $clear : FALSE;

    $model = new Model();
    $this->response($this->json($model->cart(
      $products,
      $id_customer,
      $id_address,
      $discounts,
      $deleteDiscount,
      $msg,
      $id_cart,
      $clear
    ),200));
  }

  /**
   * Obtiene toda la información de un producto
   * id : <Identificador del producto>
   */
  private function product()
  {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if (!isset($this->_request['id']) 
        || empty($this->_request['id'])) {
      $this->response('', 204);
    }

    $id_prod = $this->_request['id'];
    $model = new Model();
    $this->response(json_encode($model->getProduct($id_prod)), 200);
  }

  /**
   * Obtiene todos los fabricantes
   */
  private function manufacturers()
  {
    $model = new Model();
    $this->response(json_encode($model->manufacturers()),200);
  }

  private function createAccount($update = false)
  {
    // Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }

    $arguments = array();
    $requestData = array(
      'firstname', 
      'lastname', 
      'gender', 
      'email', 
      'passwd', 
      'signon', 
      'news', 
      'dni', 
      'birthday', 
      'website', 
      'company',
      'id_type'
    );
    foreach ($requestData as $rqd) {
      $arguments[$rqd] = isset($this->_request[$rqd]) ? $this->_request[$rqd] : "";
    }

    if (Validate::isEmail($arguments['email']) 
        && !empty($arguments['email'])) {
      if (!$update) {
        if(Customer::customerExists($arguments['email'])){
          // Si las entradas son inválidas, mensaje de estado "Bad Request" y la razón
          $this->response($this->json(array(
            "success" => false, 
            "message" => "No se pudo crear la cuenta, el (".$arguments['email']." ) email ya esta registrado"
          )), 400);
        }
      }
    } else {
      $this->response($this->json(array(
        "success" => false, 
        "message" => "se requiere un correo valido (".$arguments['email'].' )' 
      )), 400);
    }

    /*if ($arguments['d']) {
      # code...
    }*/

    if (!Validate::isPasswd($arguments['passwd']) 
        && isset($arguments['update']) 
        && empty($arguments['update'])) {
      $this->response($this->json(array(
        "success" => false, 
        "message" => "La contraseña no es valida, utiliza una contraseña con una longitud mínima de 5 caracteres." 
      )), 400);
    }

    $model = new Model();
    if ($customer = $model->setAccount($arguments)) {
      $this->response($this->json($customer), 200);
    }

    $this->response($this->json(array(
      "success" => false, 
      "message" => "Error creando la cuenta."
    )), 400);
  }

  private function updateAccount()
  {
    // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }

    $this->createAccount(true);
  }

  private function addressRFC()
  {
    // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if (!isset($this->_request['id_customer']) 
        || empty($this->_request['id_customer'])) {
      $this->response('', 204);
    }

    $model = new Model();
    $this->response(json_encode($model->getAddressRFC($this->_request['id_customer'])), 200);
  }

  private function addresses()
  {
    // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if (!isset($this->_request['id_customer']) 
        || empty($this->_request['id_customer'])) {
      $this->response('', 204);
    }

    if (!isset($this->_request['id_address']) 
        || empty($this->_request['id_address'])) {
      $this->_request['id_address'] = null;
    }

    $model = new Model();
    $this->response(json_encode($model->getAddress($this->_request['id_customer'], $this->_request['id_address'])), 200);
  }       

  private function address()
  {
    // Validación Cross si el método de la petición es GET de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }

    $arguments = array();
    $requestData = array(
      'id', 
      'id_customer', 
      'id_country',
      'id_state',
      'id_city',
      'alias', 
      'address1', 
      'address2', 
      'city',
      'phone',
      'mobile',
      'postcode',
      'id_colonia',
      'dni',
      'is_rfc'

    );
    foreach ($requestData as $rqd) {
      $arguments[$rqd] = isset($this->_request[$rqd]) ? $this->_request[$rqd] : "";
    }

    $model = new Model();   
    $this->response(json_encode($model->setAddress($arguments)), 200); 
  }

  /**
   * Paises disponibles para app,
   * identificación y nombre
   */
  private function countries() 
  {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }
    
    $model = new Model();
    $this->response(json_encode($model->get_countries()), 200);
  }

  /**
   * Consulta el estado, la ciudad y la colonia
   * dependiendo del código postal
   * postcode : <Número código postal>
   */
  private function postcode()
  {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if (!isset($this->_request['postcode']) 
        || empty($this->_request['postcode'])) {
      $this->response('', 204);
    }

    $postcode = $this->_request['postcode'];
    $model = new Model();
    $this->response(json_encode($model->get_location_by_postcode($postcode)), 200);
  }

  /**
   * Consulta los estados por país
   * id_country : <Identificador del país>
   */
  private function states()
  {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if (!isset($this->_request['id_country']) 
        || empty($this->_request['id_country'])) {
      $this->response('', 204);
    }

    $id_country = $this->_request['id_country'];
    $model = new Model();
    return $this->response(json_encode($model->get_states($id_country)),200);
  }

  /**
   * Consulta las ciudades por estado
   * id_state : <Identificador del estado>
   */
  private function cities()
  {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if (!isset($this->_request['id_state']) 
        || empty($this->_request['id_state'])) {
      $this->response('', 204);
    }

    $id_state = $this->_request['id_state'];
    $model = new Model();
    return $this->response(json_encode($model->get_cities($id_state)),200);
  }

  /**
   * Consulta las colonias por ciudad
   * id_state : <Identificador de la ciudad>
   */
  private function colonies()
  {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    if (!isset($this->_request['id_city']) 
        || empty($this->_request['id_city'])) {
      $this->response('', 204);
    }

    $id_city = $this->_request['id_city'];
    $model = new Model();
    return $this->response(json_encode($model->get_colonies($id_city)),200);
  }

  private function costoEnvio()
  {
    if ($this->get_request_method() != "GET") {
      $this->response('', 406);
    }

    $id_city = $this->_request['id_city'];
    $model = new Model();
    return $this->response(json_encode($model->get_costo_envio($id_city)),200);    
  }

  /**
   * 
   */
  public function pay()
  {
    $param['payment'] = $this->_request['payment'];
    $param['products'] = $this->_request['products'];
    $param['id_customer'] = $this->_request['id_customer'];
    $param['id_address'] = $this->_request['id_address'];
    $param['discounts'] = $this->_request['discounts'];
    $param['msg'] = $this->_request['msg'];
    $param['id_cart'] = ($this->_request['id_cart'] > 0 ? $this->_request['id_cart'] : NULL);      

    $model = new Model();
    return $this->response($this->json($model->pay($param)),200);  
  }

  public function bankPse()
  {
    return $this->response($this->json(PasarelaPagoCore::get_bank_pse()),200);  
  }

  public function KeysOpenPay()
  {
    return $this->response($this->json(PasarelaPagoCore::get_keys_open_pay('Tarjeta_credito')),200);  
  }

  public function franquicia()
  {
    $cart_number = $this->_request['cart_number'];
    $this->response(json_encode( PasarelaPagoCore::getFranquicia($cart_number, 'payulatam')),200);
  }

  public function addImg()
  {
    // Validación Cross si el método de la petición es POST de lo contrario volverá estado de "no aceptable"
    if ($this->get_request_method() != "POST") {
      $this->response('',406);
    }

    //$str_img =   $this->_request['str_img'];
    $option = $_REQUEST['option']; //$this->_request['option'];

    //error_log('|'.print_r($_REQUEST,true).'|', 0);
    $model = new Model();

    $flag = true;
    foreach ($_FILES as $key) {
      if (!$model->add_image($key,$option)){
        $flag = false;
        break;
      }
    }
    $this->response(json_encode(array('success'=>$flag)),200);
  }

  public function password()
  {
    if ($this->get_request_method() != "POST") {
      $this->response('', 406);
    }

    $model = new Model();
    $email = $this->_request['email'];
    return $this->response($this->json($model->password($email)),200);  
  }

  /**
   * Retorna las ordenes generadas por un usuario
   */
  public function getHistory()
  {
    $id_customer =  $this->_request['id'];
    $orders_out = array();
    if ($orders = Order::getCustomerOrders($id_customer))
      $contador = 0;

    foreach ($orders as &$order) {
      $contador ++;
      $myOrder = new Order((int)$order['id_order']);
      if (Validate::isLoadedObject($myOrder))
      $order['virtual'] = $myOrder->isVirtual(false);

      $order_state = Db::getInstance()->getValue("SELECT  `name` FROM ps_order_state_lang WHERE id_order_state = ". (int) $order['current_state']);

      $date = new DateTime($order['date_add']); 
      $address = new Address((int) $order['id_address_invoice']);
      $address_str =  $address->address1.' '.$address->address2.' '.$address->city.'. C.P. '.$address->postcode;  
      $orders_out[] = array(
        'id' => (int) $order['id_order'],
        'state' =>  $order_state,
        'ref' => $order['reference'],
        'id_customer' => (int) $order['id_customer'],
        'id_cart' => (int) $order['id_cart'],
        'id_address_delivery' => (int) $order['id_address_delivery'],
        'id_address_invoice' => (int) $order['id_address_invoice'],
        'address' => $address_str,
        'payment' => $order['payment'],
        'gift_message' => $order['gift_message'],
        'total' => (float) $order['total_paid'],
        'total_shipping' => (float) $order['total_shipping'],
        'total_products' => (float) $order['total_products'] ,
        'total_discounts' => (float) $order['total_discounts'],
        'invoice_number' => (int) $order['invoice_number'],
        'date_add' => $date->format("d/m/Y"),
        'order_detail' => $this->orderDetail((int) $order['id_order'])
      );
      if($contador == 20)
        break;
    }

    return $this->response($this->json($orders_out), 200);
  }

  private function orderDetail($id_order = NULL)
  {
    $id = $this->_request['id'];
    $model = new Model();
    if ($id_order != NULL)
      return $model->get_order_datail($id_order);

    $this->response($this->json($model->get_order_datail($id)), 200);
  }

  private function tracker()
  {
    $id_order =   $this->_request['id'];
    $model = new Model();
    $this->response($this->json($model->get_traker_order($id_order)),200);
  }

  private function callback()
  {
    if ($this->get_request_method() != "GET" && $this->get_request_method() != "POST") {
      $this->response('', 406);
    }

    $model = new Model();
    $accountObj = $model->call_api($_REQUEST['accessToken'],"https://www.googleapis.com/plus/v1/people/me");

    return $this->response(json_encode($accountObj),200);
  }

  private function test()
  {
    //$context = Context::getContext();
    //$this->response($this->json((array) $context->customer), 200);
    $model = new Model();
    $this->response(json_encode($model->list_medios_de_pago()));
    //echo "<pre>";
    //var_dump($model->list_medios_de_pago());
    //echo "</pre>";
  }

}

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
header('Access-Control-Allow-Credentials: true');

// Iniciar
$api = new API;
$api->processApi();

?>
