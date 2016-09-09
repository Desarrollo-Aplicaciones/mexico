<?php



$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_.'payulatam/payulatam.php');
require_once('./config.php');

class PayuPse extends FrontController {
    
    
    
   
    
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
if(isset($_POST['pse_bank'])&&isset($_POST['name_bank']))
  {
          
$conf=new ConfPayu();
$signature=$conf->sing($params[2]['referenceCode'].'~'.$params[4]['amount'].'~'.$params[9]['currency']);

$varRandn=$conf->randString();
$varRandc=$conf->randString();
setcookie($varRandn, $varRandc, time()+900);  


$browser =array('ipAddress'=>$_SERVER['SERVER_ADDR'],
    'userAgent'=>$_SERVER['HTTP_USER_AGENT']);

  
$address=null;
$sql = 'select ad.address1,city,phone_mobile,phone,dni, st.`name` as state, co.iso_code   
        from ps_address ad, ps_state st, ps_country co  where ad.id_customer='.$this->context->cart->id_customer.''
        . ' and ad.id_address='.$this->context->cart->id_address_delivery.' and ad.id_state= st.id_state and
       co.id_country =ad.id_country';



if ($results = Db::getInstance()->ExecuteS($sql))
     foreach ($results as $row)
           {
             $address=$row;
           }
  $phone=null;         
 if($address['phone_mobile']=='')
 {
 $phone=$address['phone'];    
 }  else {
  $phone=$address['phone_mobile'];    
 }
           
$keysPayu= $conf->keys();

// Script Json payuLatam (PSE)
              
$data='{
"test":false,
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
"description":"'.$params[3]['description'].' PSE",
"language":"es",
"notifyUrl":"'.$conf->urlv().'",
"signature":"'.$conf->sing($params[2]['referenceCode'].'~'.$params[4]['amount'].'~'.$params[9]['currency']).'",
"buyer":{
"fullName":"'.$this->context->customer->firstname.' '.$this->context->customer->lastname.'",
"emailAddress":"'.$params[5]['buyerEmail'].'",
"dniNumber":"'.$address['dni'].'",
"shippingAddress":{
"street1":"'.$address['address1'].'",
"city":"'.$address['city'].'",
"state":"'.$address['state'].'",
"country":"'.$address['iso_code'].'",
"phone":"'.$phone.'"
}
},
"additionalValues":{
"TX_VALUE":{
"value":'.$params[4]['amount'].',
"currency":"'.$params[9]['currency'].'"
}
}
},
"payer":{
"fullName":"'.$this->context->customer->firstname.' '. $this->context->customer->lastname.'",
"emailAddress":"'.$params[5]['buyerEmail'].'",
"dniNumber":"'.$address['dni'].'",
"contactPhone":"'.$phone.'"
},
"ipAddress":"'.$browser['ipAddress'].'",
"cookie":"'.$varRandn.'",
"userAgent":"'.$browser['userAgent'].'",
"type":"AUTHORIZATION_AND_CAPTURE",
"paymentMethod":"PSE",
"extraParameters":{
"PSE_REFERENCE1":"'.$browser['ipAddress'].'",
"FINANCIAL_INSTITUTION_CODE":"'.$_POST['pse_bank'].'",
"FINANCIAL_INSTITUTION_NAME":"'.$_POST['name_bank'].'",
"USER_TYPE":"'.$_POST['pse_tipoCliente'].'",
"PSE_REFERENCE2":"'.$_POST['pse_docType'].'",
"PSE_REFERENCE3":"'.$_POST['pse_docNumber'].'"
}
}
}
';


$response= $conf->sendJson($data);

//echo "<pre>";
//print_r($response);

$messsage='';
if($response['code']=='ERROR')
{
 $messsage='<h1>¡Error!, Transaccion Rechazada</h1><br>'.$response['error'];    
}
elseif ($response['code']=='SUCCESS') {
    
 $id_cart=$this->context->cart->id;
 $customer = new Customer((int)$this->context->cart->id_customer);
 $this->createPendingOrder();
 $order=$conf->get_order($id_cart);
 $orden_select = $order['id_order'];
 $conf->pago_payu($order['id_order'], $order['id_customer'], $data, $response, 'Pse', null);
  
// Pruebas carrito
$url_base64 = strtr(base64_encode($response['transactionResponse']['extraParameters']['BANK_URL']), '+/=', '-_,');


$string_send=__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$id_cart.'&id_module=105&id_order='.(int)$orden_select.'&bankdest2='.$url_base64;

Tools::redirectLink($string_send);

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

	public function createPendingOrder()
	{
            try{
		$payu = new PayULatam();
		$payu->validateOrder((int)self::$cart->id, (int)Configuration::get('PAYU_WAITING_PAYMENT'), (float)self::$cart->getOrderTotal(), 'Payu_pse', 'El sistema esta en espera de la confirmación de la pasarela de pago.', array(), NULL, false,	self::$cart->secure_key);
            } catch (Exception $e)
            {
                //echo 'Error: '.$e->getMessage();
            }       
        }
        
        



}


$payuPse=new PayuPse();

$payuPse->run();
