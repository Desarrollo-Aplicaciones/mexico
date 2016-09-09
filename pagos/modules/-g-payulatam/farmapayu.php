<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_.'payulatam/payulatam.php');
require_once('./config.php');

class farmapayu extends FrontController {
    
    public $ssl = true;
   

	public function setMedia()
	{
		parent::setMedia();
	}

	public function process()
	{
            //error_reporting(0);
		parent::process();               
                

  $params = $this->initParams();
               // var_dump($params);
 $post;
 $messsage='';
 if (isset($_POST['numerot']))
    {
 
    
 
 // se optinen los datos del formulario de pago farmalisto    
     $post=array('nombre'=> $nombre=$_POST['nombre'],
                 'numerot'=>$numerot=$_POST['numerot'],
                 'codigot'=>$codigot=$_POST['codigot'],
                 'date'=>$year=$_POST['datepicker'],
                 'cuotas'=>$cuotas=$_POST['cuotas'],
                 'mediop'=>$mediop=$_POST['mediop']//,
                // 'dninumber'=>$dninumber=$_POST['dninumber']
             );
      

  
  $conf=new ConfPayu();
  
 $keysPayu= $conf->keys();

              
    $data='{
   "language":"es",
   "command":"SUBMIT_TRANSACTION",
   "merchant":{
      "apiLogin":"'.$keysPayu['apiLogin'].'",
      "apiKey":"'.$keysPayu['apiKey'].'"
   },
   "transaction":{
      "order":{
         "accountId":"'.$keysPayu['accountId'].'",
         "referenceCode":"'.$params[2]['referenceCode'].'",
         "description":"'.$params[3]['description'].'",
         "language":"'.$params[10]['lng'].'",
         "notifyUrl":"'.$conf->urlv().'",
         "signature":"'.$conf->sing($params[2]['referenceCode'].'~'.$params[4]['amount'].'~'.$params[9]['currency']).'",
         "additionalValues":{
            "TX_VALUE":{
               "value":'.$params[4]['amount'].',
               "currency":"'.$params[9]['currency'].'"
            }
         }
      },
      "creditCard":{
         "number":"'.$post['numerot'].'",
         "securityCode":"'.$post['codigot'].'",
         "expirationDate":"'.$post['date'].'",
         "name":"'.$post['nombre'].'"
      },
      "type":"AUTHORIZATION_AND_CAPTURE",
      "paymentMethod":"'.$post['mediop'].'",
      "paymentCountry":"CO",
      "extraParameters":{
         "INSTALLMENTS_NUMBER":1
      }
   },
   "test":false
}
';

$response= $conf->sendJson($data);

// Validacion de daqtos de respuesta del web service 

if($response['code']=='ERROR')
{
 $messsage='<h1>¡Error!, Transaccion Rechazada</h1><br>'.$response['error'];    
}
elseif ($response['code']=='SUCCESS') {

    $customer = new Customer((int)$this->context->cart->id_customer);
    
    $id_cart = $this->context->cart->id;
                $this->createPendingOrder();
                $order = $conf->get_order($id_cart);

$orden_select = $order['id_order'];

                $subs = substr($numerot, 0, (strlen($numerot) - 4));

                $nueva = '';
                for ($i = 0; $i <= strlen($subs); $i++) {
                    $nueva = $nueva . '*';
                }
                $data = str_replace('"number":"'.$subs, '"number":"'.$nueva, $data);
                $data = str_replace('"securityCode":"'.$codigot, '"securityCode":"'.'****', $data);


                $conf->pago_payu($order['id_order'], $order['id_customer'], $data, $response, 'Tarjeta_credito', null);

  
Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?key='.$customer->secure_key.'&id_cart='.(int)$this->context->cart->id.'&id_module=105&id_order='.(int)$orden_select);

}
elseif ($response['code']=='PAYU_WAITING_PAYMENT') {

}
 else {
       $messsage='Error indeterminado';  
      }
self::$smarty->assign('data',$data );    
 
                  
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
		$payu->validateOrder((int)self::$cart->id, (int)Configuration::get('PAYU_WAITING_PAYMENT'), (float)self::$cart->getOrderTotal(), 'Payu_tarjeta_credito', 'El sistema esta en espera de la confirmación de la pasarela de pago.', array(), NULL, false,self::$cart->secure_key);
            } catch (Exception $e)
            {
               echo 'Error: '.$e->getMessage();
               print_r($e);
               exit();
            }       
        }


}

$farmaPayu=new farmapayu();
$farmaPayu->run();