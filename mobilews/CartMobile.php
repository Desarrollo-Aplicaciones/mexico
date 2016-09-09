<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

class PayMobile extends PaymentModule {
   
    
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


class CartWSJSON extends FrontController {

    public $products = array();
    public $email;
    public $id_customer;
    public $id_address; 
    public $id_voucher;
    public $message;
    public $reponse = NULL;
    public $errors = array();    

    public function __construct($args) {
            foreach ($args as $key => $value) {
              $this->{$key} = $value;
            }
        if (isset(Context::getContext()->controller))
            $controller = Context::getContext()->controller;
        else {
            $controller = new FrontController();
            $controller->init();
        }
    }


    public function create_cart() {

        if (count($products) > 0) {
            
           if (!$this->valid_products($products)){
            $this->errors[] = 'Error actualizando cantidades de producto';  
            Logger::AddLog('[CartMobile] Error actualizando cantidades de producto' . $exc->getTraceAsString(), 2, null, null, null, true);
           }
            $this->context = new StdClass(); // crear contexto
            //echo 'crear carrito';
            $this->context->cart = new Cart();
            $this->context = Context::getContext(); // actualizar contexto
            // Agrega el carrito a la base de datos
             $this->context->cart->id_customer = (int) $id_customer;
             $this->context->cart->id_address_delivery = (int) $id_address;
             $this->context->cart->id_address_invoice =  (int) $id_address;
             $this->context->cart->update();
             $contextClone = Context::getContext()->cloneContext();
			       if(isset($contextClone->cart->id_address_delivery)) { 
				      $add_delivery = $contextClone->cart->id_address_delivery;
			       } elseif(isset($this->cart->id_address_delivery)) { 
				      $add_delivery = $this->cart->id_address_delivery;
			       }
            $this->context->cart->add();
            // agrgar productos al carrito
            foreach ($products as $key => $value) {
              $cart->updateQty($value, $key, 0, 0, 'up', 0);
            }
            $cart->update();
        } else {
            $this->errors[] = 'No se enviaron productos para crear la orden.';
        }
    }

    public function add_order()
    {

    }

    public function add_vaucher() {

        if (isset($id_voucher) && !empty($id_voucher)) {
                $this->context->cart->addCartRule((int) $id_voucher);
        }
    }

    public function create_order() {
        $payment = new PayMobile();
        $this->context = Context::getContext(); // actualizar contexto
        $this->context->cart->update();
        $total = (float) $this->context->cart->getOrderTotal(true, Cart::BOTH);
        $customer = new Customer();
		    $customer = $customer->getByEmail($this->email, null, false);
		    $employee = new Employee();
		    $id_employee = $employee->employeeExists($this->email);

        try {
            $payment->validateOrder((int) $this->context->cart->id, Configuration::get('PS_OS_PAYMENT'), $total, $payment->displayName.'TOGO-MX', NULL, array(), (int) $this->context->currency->id, false, $customer->secure_key);
            $this->context = Context::getContext(); // actualizar contexto
            $respuesta = array( 'id_order' => $this->context->smarty->tpl_vars['order']->value->id,
                                'reference' => $this->context->smarty->tpl_vars['order']->value->reference);
            // se crea la relación entre el empleado y la orden
            $emp_data=Utilities::get_data_employee($id_employee);
            Utilities::add_message($this->context->cart->id,$customer->id, $id_employee , $this->context->smarty->tpl_vars['order']->value->id ,$emp_data['firstname'].'-'.$emp_data['lastname'].': '.$this->message,1,$this->context->smarty->tpl_vars['order']->value->date_add);
        } catch (Exception $exc) {
            Logger::AddLog('Error [CartMobile] error al crear la orden ' . $exc->getTraceAsString(), 2, null, null, null, true);
            $this->errors[] = 'Error creando la orden: ' . $exc;
        }
        if (!count($this->errors) > 0) {
            $obj = array('entity' => 'order', 'action' => 'response', 'content' => $respuesta, 'error' => array());
        } else {
            $obj = array('entity' => 'order', 'action' => 'response', 'content' => $respuesta, 'error' => $this->errors);
        }
        $this->reponse = $obj;
    }


 public function valid_products($products){
     
      $ids_products="";
      $i=0;
      foreach ($products as $value) {
        if ($i < (count($products) - 1)) {
          $ids_products.=$value['id_product_ps'] . ',';
        } else {
          $ids_products.=$value['id_product_ps'];
        }
        $i++;
      }

      $query="  select prod.id_product,stock.quantity FROM
                ps_product prod 
                INNER JOIN ps_product_shop prods ON (prod.id_product=prods.id_product)
                INNER JOIN ps_stock_available stock on( prod.id_product=stock.id_product)
                LEFT JOIN ps_product_black_list black ON(prod.id_product=black.id_product)
                WHERE ISNULL(black.id_product) and prod.is_virtual !=1 AND prod.active=1 AND prods.active=1 and prod.id_product in(".$ids_products.");";

     
      $products_update=array();
      if ($results = Db::getInstance()->ExecuteS($query)) {
        foreach ($results as $value) {
          foreach ($products as $value2) {
            if($value['id_product']===$value2['id_product_ps'] && ( (int)$value['quantity'] < (int)$value2['quantity']) ){
              $products_update[]=array('id_product'=>$value2['id_product_ps'],'quantity'=> ((int)$value2['quantity']+1) );
            }
                  
          }
        }
      }
   
       
    if(count($products_update)>0){
      $query="";
      foreach ($products_update as $value) {
        $query.=" update  ps_product prod 
                  INNER JOIN ps_product_shop prods ON (prod.id_product=prods.id_product)
                  INNER JOIN ps_stock_available stock on( prod.id_product=stock.id_product)
                  LEFT JOIN ps_product_black_list black ON(prod.id_product=black.id_product)
                  SET stock.quantity = ".$value['quantity']."
                  WHERE ISNULL(black.id_product) and prod.is_virtual !=1 AND prod.active=1 AND prods.active=1 and prod.id_product = ".$value['id_product']."; "
                   . "";

        if ($results = Db::getInstance()->ExecuteS($query)) {
          return TRUE;   
        }else{
          return false;
        }
      }
    }
  return TRUE;
}


    
}