<?php

$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_.'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_.'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');

class Oxxo extends PayUControllerWS{    
   
    
    public $ssl = true;
   

	public function setMedia()
	{
		parent::setMedia();
	}

	public function process()
	{
            
     if(empty( $this->context->cart->id)){
         Tools::redirect('/');  
     }       
		parent::process();               
                

 $params = $this->initParams();

 $messsage='';


if (isset($_POST['pagar']))
  {
    $conf=new ConfPayu();
   
    $id_cart = $this->context->cart->id;
    $id_address = $this->context->cart->id_address_delivery;
    $customer = new Customer((int)$this->context->cart->id_customer);
    $reference_code=$customer->id.'_'.$id_cart.'_0_'.$id_address;
    $address = $conf->get_address($this->context->cart->id_customer, $this->context->cart->id_address_delivery);
    $fecha = date('Y-m-j');
    $nuevafecha = strtotime ( '+5 day' , strtotime ( $fecha ) ) ;
    $fechaOXXO=date ( 'Y-m-d' , $nuevafecha ).'T'.date ( 'h:i:s' , $nuevafecha );          
    $keysPayu= $conf->keys();

// Script Json payuLatam (OXXO)   
    $intentos = $conf->count_pay_cart($id_cart);
    
$data='{
"language":"es",
"command":"SUBMIT_TRANSACTION",
"merchant":{
"apiLogin":"'.$keysPayu['apiLogin'].'",
"apiKey":"'.$keysPayu['apiKey'].'"
},
"transaction":{
"order":{
"accountId":"'.$keysPayu['accountId'] .'",
"referenceCode":"'.$params[2]['referenceCode']. '_'.$intentos.'",
"description":"'.$reference_code.'",
"language":"es",
"notifyUrl":"'.$conf->urlv().'",
"signature":"'.$conf->sing($params[2]['referenceCode']. '_'.$intentos.'~' .$params[4]['amount'].'~'.$params[9]['currency']).'",
"shippingAddress":{
"country":"'.$address['iso_code'].'"
},
"buyer":{
"fullName":"'.$this->context->customer->firstname.' '. $this->context->customer->lastname.'",
"emailAddress":"'.$params[5]['buyerEmail'].'",
"dniNumber":"'.$address['dni'].'",
"shippingAddress":{
"street1":"'.$address['address1'].'",
"city":"'.$address['city'].'",
"state":"'.$address['state'].'",
"country":"'.$address['iso_code'].'",
"phone":"'.$address['phone_mobile'].'"
}
},
"additionalValues":{
"TX_VALUE":{
"value":'.$params[4]['amount'].',
"currency":"'.$params[9]['currency'].'"
}
}
},
"type":"AUTHORIZATION_AND_CAPTURE",
"paymentMethod":"OXXO",
"expirationDate":"'.$fechaOXXO.'",
"paymentCountry": "' . $address['iso_code'] . '"    
},
"test":false
}
';


$response= $conf->sendJson($data);

$messsage='';
if($response['code'] === 'ERROR')
{

 $conf->failed_transaction(0.0, $customer->id, $data, $response, 'OXXO', $response['code'], $this->context->cart->id,$id_address);
 $messsage='<h1>¡Error!, Transaccion Rechazada</h1><br>' .$response['error'];    
}


        elseif ($response['code'] === 'SUCCESS' && $response['transactionResponse']['state'] === 'PENDING' && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS') {
   
                $extra_vars =  array('method'=>'OXXO',
                                     'cod_pago'=>$response['transactionResponse']['extraParameters']['REFERENCE'],
                                     'fechaex'=> date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)),
                                     'bar_code'=>$response['transactionResponse']['extraParameters']['BAR_CODE']);

                $this->createPendingOrder($extra_vars, 'OXXO', 'El sistema esta en espera de la confirmación de la pasarela de pago.');
                $order=$conf->get_order($id_cart);
                $extras=$response['transactionResponse']['extraParameters']['REFERENCE'].';'.date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)).';'.$response['transactionResponse']['extraParameters']['BAR_CODE'];
                $conf->pago_payu($order['id_order'], $customer->id, $data, $response, 'OXXO', $extras, $id_cart,$id_address);
                $orden_select = $order['id_order'];
                Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$id_cart.'&id_module=105&id_order='.(int)$orden_select);

}
else {
     $conf->failed_transaction(0.0, $customer->id, $data, $response, 'OXXO', $response['transactionResponse']['state'], $id_cart,$id_address);
      $messsage='Error, La entidad financiera del medio de pago seleccionado, no responde. Por favor intente con otro medio de pago o reintente mas tarde.';  
     }
     
  }

  self::$smarty->assign('messsage',$messsage );               

}

	public function displayContent()
	{
		parent::displayContent();
               
		self::$smarty->display(_PS_MODULE_DIR_.'payulatam/tpl/success.tpl');
	}

}


$farmaPayu=new Oxxo();


$farmaPayu->run();
