<?php

include(dirname(__FILE__) . '/../config/config.inc.php');
 //clase para serializar el contenido Json
class ArrayValue implements JsonSerializable {

    public function __construct(array $array) {
        $this->array = $array;
    }

    public function jsonSerialize() {
        return $this->array;
    }

}


class PaymentWs extends PaymentModule {
   
    
    	public function __construct()
	{
		$this->name = 'cashondelivery';
		$this->tab = 'payments_gateways';
		$this->version = '1.0';
		$this->author = 'PrestaShop';
		$this->need_instance = 1;
		$this->module_key = '1bc1eb8640f4234902725736f6bd45e9';

		$this->currencies = false;

		parent::__construct();

		$this->displayName = $this->l('Cash on delivery (COD)');
		$this->description = $this->l('Accept cash on delivery payments');

		/* For 1.4.3 and less compatibility */
		$updateConfig = array('PS_OS_CHEQUE', 'PS_OS_PAYMENT', 'PS_OS_PREPARATION', 'PS_OS_SHIPPING', 'PS_OS_CANCELED', 'PS_OS_REFUND', 'PS_OS_ERROR', 'PS_OS_OUTOFSTOCK', 'PS_OS_BANKWIRE', 'PS_OS_PAYPAL', 'PS_OS_WS_PAYMENT');
		if (!Configuration::get('PS_OS_PAYMENT'))
			foreach ($updateConfig as $u)
				if (!Configuration::get($u) && defined('_'.$u.'_'))
					Configuration::updateValue($u, constant('_'.$u.'_'));
	}


    
}

/*
 * Clase para gestionar las solicitudes de sugar
 */

class serverWsPs extends FrontController {

    public $array_obj = array();
    public $reponse = NULL;
    public $errors = array();

    public function __construct() {
        if (isset(Context::getContext()->controller))
            $controller = Context::getContext()->controller;
        else {
            $controller = new FrontController();
            $controller->init();
        }
    }

    /*
     * Procesa la solicitud en formato Json
     */

    function process_request($json) {

        $this->logtxt($json);
        try {

            if (isset($json)) {
                $array = json_decode($json, TRUE);


                if (isset($array['entity']) && isset($array['action']) && isset($array['content']) && isset($array['id_employee']) && !empty($array['entity']) && !empty($array['action']) && !empty($array['content']) && !empty($array['id_employee'])) {
                    $this->array_obj = $array;
                    $this->resolve();
                    return TRUE;
                }
            }
        } catch (Exception $e) {
            $this->errors[] = $e;
            return false;
        }
        $this->errors[] = 'Parametros no validos';
        return FALSE;
    }

    public function resolve() {
        switch ($this->array_obj['action']) {
            case 'add':
                $this->reponse = 'add';
                $this->add($this->array_obj);

                break;
            case 'delete':
                $this->reponse = 'delete';

                break;
                $this->reponse = 'edit';
            case 'edit':


                break;
            default:
                $this->errors[] = 'Acción no valida';
                break;
        }
    }

    public function add($array) {
        switch ($array['entity']) {
            case 'order':
                $id_vaucher = $array['content']['id_voucher'];
                $id_address = $array['content']['id_address'];
                $id_customer = $array['content']['id_customer'];

                /*
                 * Valida si la orden contiene todos elementos rqueridos
                 */
                if (isset($id_address) && !empty($id_address) && isset($id_customer) && !empty($id_customer) && count($array['content']['products']) > 0) {

                    $this->create_cart($array['content']['products'], $id_customer, $id_address);
                } else {
                    $this->errors[] = 'Los parametros de la orden no son validos';
                }
                break;

            default:
                $this->errors[] = 'Entidad invalida';
                break;
        }
    }

    public function create_cart($products, $id_customer, $id_address) {

        if (count($products) > 0) {

            $this->context = new StdClass(); // crear contexto
            //echo 'crear carrito';
            $this->context->cart = new Cart();
            $this->context = Context::getContext(); // actualizar contexto
            // Agrega el carrito a la base de datos
            $this->context->cart->add();
            // agrgar productos al carrito
            foreach ($products as $value) {
                $this->context->cart->updateQty($value['quantity'], $value['id_product_ps'], 0, 0, 'up', 0);
            }

            $this->context->cart->update();
            $this->add_vaucher();
            $this->create_order();

//            // actualizar cookie contexto cart
//            if ($this->context->cart->id) {
//                $this->context->cookie->id_cart = (int) $this->context->cart->id;
//            }
        } else {

            $this->errors[] = 'No se enviaron productos para crear la orden.';
        }
    }

    public function add_vaucher() {

        if (isset($this->array_obj['content']['id_voucher']) && !empty($this->array_obj['content']['id_voucher'])) {
            if (($this->array_obj['content']['id_voucher'])) {
                $this->context->cart->addCartRule((int) ($this->array_obj['content']['id_voucher']));
            }
        }
    }

    public function create_order() {

        $payment = new PaymentWs();
        $this->context = Context::getContext(); // actualizar contexto

        $this->context->cart->id_customer = (int) $this->array_obj['content']['id_customer'];
        $this->context->cart->id_address_delivery = (int) $this->array_obj['content']['id_address'];
        $this->context->cart->id_address_invoice =  (int) $this->array_obj['content']['id_address'];
        $this->context->cart->update();

        $total = (float) $this->context->cart->getOrderTotal(true, Cart::BOTH);
        $customer = new Customer($this->context->cart->id_customer);

        try {

            $payment->validateOrder((int) $this->context->cart->id, Configuration::get('PS_OS_PREPARATION'), $total, $payment->displayName.'-SugarCRM', NULL, array(), (int) $this->context->currency->id, false, $customer->secure_key);

            $this->context = Context::getContext(); // actualizar contexto

            $respuesta = array('id_cart' => $this->context->cart->id,
                'id_order' => $this->context->smarty->tpl_vars['order']->value->id,
                'reference' => $this->context->smarty->tpl_vars['order']->value->reference);
        } catch (Exception $exc) {

            Logger::AddLog('Soket-webservice [sugar_to_ps-php] error al crear la orden ' . $exc->getTraceAsString(), 2, null, null, null, true);
            $this->errors[] = 'Error creando la orden: ' . $exc;
        }

        if (!count($this->errors) > 0) {
            $obj = array('entity' => 'order', 'id_employee' => 1, 'action' => 'response', 'content' => $respuesta, 'error' => array());
        } else {
            $obj = array('entity' => 'order', 'id_employee' => 1, 'action' => 'response', 'content' => $respuesta, 'error' => $this->errors);
        }
        $this->reponse = $obj;
    }
    
    public function logtxt ($text="") {
        $fp=fopen(Configuration::get('PATH_UPLOAD')."log_socket_sugar.txt","a+");
        fwrite($fp,$text."\r\n"); fclose($fp);
}

}

/* socket_create=>Crea y devuelve un recurso socket */
$socket = socket_create(AF_INET, SOCK_STREAM, 0);

/* 0 - acepta cualquier conexion de cualquier ip */
$direccion = 0;

/* para el puerto no podemos utilizar numeros menores a 1025
  /debido a que ya estan reservados para aplicaciones del sistema como correo electronico etc. */
$puerto = 1721;

/* socket_bind=>Vincula el nombre dado en $direccion al socket descrito por $socket.
  Esto tiene que ser hecho antes de establecer una conexión
  usando socket_connect() o socket_listen(). */
socket_bind($socket, $direccion, $puerto);

/* socket_listen=>Después de que el socket socket haya sido creado usando socket_create()
  y vinculado a un nombre con socket_bind(), se le puede indicar
  que escuche conexiones entrantes sobre socket. */
socket_listen($socket);

/* Mientras sea verdadero se ejecuta, quiere decir que
  siempre estara a la espera de nuevos clientes */

$size = 2048;
$buffer = NULL;
while (1) {
    try {
        $cliente = socket_accept($socket);
        $buffer = socket_read($cliente, $size); //leemos mensaje del cliente

        $process_requets = new serverWsPs();
        $process_requets->process_request($buffer);

        //$buffer = json_encode(new ArrayValue($process_requets->reponse), JSON_PRETTY_PRINT);
        $buffer = json_encode($process_requets->reponse, JSON_PRETTY_PRINT);

        socket_write($cliente, $buffer); //escribimos el buffer
        socket_close($cliente); //cerramos cliente
    } catch (Exception $exc) {
        Logger::AddLog('Soket-webservice [sugar_to_ps-php] error al escribir en el socket ' . $exc->getTraceAsString(), 2, null, null, null, true);
    }
}
//socket_close=>cierra el recurso socket dado por $socket
socket_close($socket);

