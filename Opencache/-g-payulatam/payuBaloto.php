<?php

$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_.'payulatam/payulatam.php');
require_once('./config.php');

class PayuBaloto extends FrontController{    
   
    
    public $ssl = true;
   

	public function setMedia()
	{
		parent::setMedia();
	}

	public function process()
	{
       
		parent::process();               
                

 $params = $this->initParams();

 $messsage='';


if (isset($_POST['pagar']))
  {
    $conf=new ConfPayu();
  
    
    $address=null;
    $sql = 'select ad.address1,city,phone_mobile,phone,dni, st.`name` as state, co.iso_code   
            from ps_address ad, ps_state st, ps_country co  where ad.id_customer='.$this->context->cart->id_customer.''
          .' and ad.id_address='.$this->context->cart->id_address_delivery.' and ad.id_state= st.id_state and
           co.id_country =ad.id_country';
    
 
if ($results = Db::getInstance()->ExecuteS($sql))
    foreach ($results as $row)
            {
             $address=$row;
            }
           
       
           
$fecha = date('Y-m-j');
$nuevafecha = strtotime ( '+3 day' , strtotime ( $fecha ) ) ;
$fechaBaloto=date ( 'Y-m-d' , $nuevafecha ).'T'.date ( 'h:i:s' , $nuevafecha );          
     
$keysPayu= $conf->keys();

// Script Json payuLatam (Baloto)              
$data='{
"language":"es",
"command":"SUBMIT_TRANSACTION",
"merchant":{
"apiLogin":"'.$keysPayu['apiLogin'].'",
"apiKey":"'.$keysPayu['apiKey'].'"
},
"transaction":{
"order":{
"accountId":"'.$keysPayu['pse-CO'].'",
"referenceCode":"'.$params[2]['referenceCode'].'",
"description":"'.$params[3]['description'].' Baloto",
"language":"es",
"notifyUrl":"'.$conf->urlv().'",
"signature":"'.$conf->sing($params[2]['referenceCode'].'~'.$params[4]['amount'].'~'.$params[9]['currency']).'",
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
"paymentMethod":"BALOTO",
"expirationDate":"'.$fechaBaloto.'"
},
"test":false
}
';


$response= $conf->sendJson($data);


$messsage='';
if($response['code']=='ERROR')
{
 $messsage='<h1>¡Error!, Transaccion Rechazada</h1><br>'.$response['error'];    
}


elseif ($response['code']=='SUCCESS') {
   
    $customer = new Customer((int)$this->context->cart->id_customer);
    
 $id_cart=$this->context->cart->id;

$extra_vars =  array('method'=>'baloto',
                     'cod_pago'=>$response['transactionResponse']['extraParameters']['REFERENCE'],
                     'fechaex'=> date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)));

$this->createPendingOrder($extra_vars);

$order=$conf->get_order($id_cart);
$extras=$response['transactionResponse']['extraParameters']['REFERENCE'].';'.date('d/m/Y', substr($response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3));
$conf->pago_payu($order['id_order'], $order['id_customer'], $data, $response, 'Baloto', $extras);
 
  $orden_select = $order['id_order'];
// Pruebas carrito

Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$id_cart.'&id_module=105&id_order='.(int)$orden_select);

}
elseif ($response['code']=='PAYU_WAITING_PAYMENT')
{

}
else {
      $messsage='Error indeterminado';  
     }
     
  }
  self::$smarty->assign('messsage',$messsage );               

}

	public function displayContent()
	{
		parent::displayContent();
               
		self::$smarty->display(_PS_MODULE_DIR_.'payulatam/tpl/success.tpl');
	}

	public function initParams()
	{

		$tax = (float)self::$cart->getOrderTotal() - (float)self::$cart->getOrderTotal(false);
		$base = (float)self::$cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) + (float)self::$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS) - (float)$tax;
		if($tax == 0)
			$base = 0;

		$currency = new Currency(self::$cart->id_currency);

		$language = new Language(self::$cart->id_lang);

		$customer = new Customer(self::$cart->id_customer);

		$ref = 'payU_'.Configuration::get('PS_SHOP_NAME').'_'.(int)self::$cart->id;

		$token = md5(Tools::safeOutput(Configuration::get('PAYU_API_KEY')).'~'.Tools::safeOutput(Configuration::get('PAYU_MERCHANT_ID')).'~'.$ref.'~'.(float)self::$cart->getOrderTotal().'~'.Tools::safeOutput($currency->iso_code));

		$params = array(
			array('test' => (Configuration::get('PAYU_DEMO') == 'yes' ? 1 : 0), 'name' => 'test'),
			array('merchantId' => Tools::safeOutput(Configuration::get('PAYU_MERCHANT_ID')), 'name' => 'merchantId'),
			array('referenceCode' => $ref, 'name' => 'referenceCode'),
			array('description' => substr(Configuration::get('PS_SHOP_NAME').' Order', 0, 255), 'name' => 'description'),
			array('amount' => (float)self::$cart->getOrderTotal(), 'name' => 'amount'),
			array('buyerEmail' => Tools::safeOutput($customer->email), 'name' => 'buyerEmail'),
			array('tax' => (float)$tax, 'name' => 'tax'),
			array('extra1' => 'PRESTASHOP', 'name' => 'extra1'),
			array('taxReturnBase' => (float)$base, 'name' => 'taxReturnBase'),
			array('currency' => Tools::safeOutput($currency->iso_code), 'name' => 'currency'),
			array('lng' => Tools::safeOutput($language->iso_code), 'name' => 'lng'),
			array('signature' => Tools::safeOutput($token), 'name' => 'signature'),
			array('value' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'history.php', 'name' => 'responseUrl'),
			array('value' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/payulatam/validation.php', 'name' => 'confirmationUrl'),
		);

		if (Configuration::get('PAYU_ACCOUNT_ID') != 0)
			$params[] = array('accountId' => (int)Configuration::get('PAYU_ACCOUNT_ID'), 'name' => 'accountId');

		if (Db::getInstance()->getValue('SELECT `token` FROM `'._DB_PREFIX_.'payu_token` WHERE `id_cart` = '.(int)self::$cart->id))
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'payu_token` SET `token` = "'.pSQL($token).'" WHERE `id_cart` = '.(int)self::$cart->id);
		else
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'payu_token` (`id_cart`, `token`) VALUES ('.(int)self::$cart->id.', \''.pSQL($token).'\')');

		return $params;
	}

	public function createPendingOrder($extra_vars = array())
	{
            try{
		$payu = new PayULatam();
		$payu->validateOrder((int)self::$cart->id, (int)Configuration::get('PAYU_WAITING_PAYMENT'), (float)self::$cart->getOrderTotal(), 'Payu_baloto', 'El sistema esta en espera de la confirmación de la pasarela de pago.', $extra_vars, NULL, false,	self::$cart->secure_key);
            } catch (Exception $e)
            {
                //echo 'Error: '.$e->getMessage();
            }       
        }
        
        


}


$farmaPayu=new PayuBaloto();


$farmaPayu->run();
