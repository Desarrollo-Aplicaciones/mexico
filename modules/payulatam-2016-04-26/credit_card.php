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
require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/openpay/OpenpayController.php');

class farmapayu extends PayUControllerWS {

  public $ssl = true;

  public function setMedia() {
    parent::setMedia();
  }

  public function process() {

    if (empty($this->context->cart->id)) {
      Tools::redirect('/');
      exit();
    }

    parent::process();

        // url para re intentos de pago
    $url_reintento=$_SERVER['HTTP_REFERER'];
    if(!strpos($_SERVER['HTTP_REFERER'], '&step=')){
      $url_reintento.='&step=3';
    }
          // vaciar errores en el intento de pago anterior  
    if(isset($this->context->cookie->{'error_pay'})){
      unset($this->context->cookie->{'error_pay'});
    }

    $arraypaymentMethod=  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AMEX','MASTERCARD'=>'MASTERCARD');

    $messsage = '';
    if ((isset($_POST['numerot']) && !empty($_POST['numerot']) && strlen($_POST['numerot']) > 13 && strlen((int) $_POST['numerot']) < 17
        && isset($_POST['nombre']) && !empty($_POST['nombre']) && isset($_POST['codigot']) && !empty($_POST['codigot']) && 
        isset($_POST['datepicker']) && !empty($_POST['datepicker']) && isset($_POST['cuotas']) && !empty($_POST['cuotas'])) 
      || (isset($_POST['token_id']) && !empty($_POST['token_id']) && isset($_POST['openpay_device_session_id']) && !empty($_POST['openpay_device_session_id']) ) ) {
            // reglas de carrito para bines
            $payulatam = new PayULatam(); //($haceFrio) ? "Hace frío" : "No hace frío";
          $bin = $payulatam->addCartRuleBin((Tools::getValue('numerot')) ? Tools::getValue('numerot') : Tools::getValue('card'));
          $paymentMethod='';
          $key=json_decode(file_get_contents('http://www.binlist.net/json/'.(int)$bin), TRUE)['brand'];

          if (array_key_exists($key, $arraypaymentMethod)) {
            $paymentMethod = $arraypaymentMethod[$key];
          }

          $params = $this->initParams();
            // se optinen los datos del formulario de pago farmalisto    
          $post = array('nombre'  =>  (Tools::getValue('nombre')) ? Tools::getValue('nombre') : Tools::getValue('holder'),
                        'numerot' =>  (Tools::getValue('numerot')) ? Tools::getValue('numerot') : Tools::getValue('card'),
                        'codigot' =>  (Tools::getValue('codigot')) ? Tools::getValue('codigot') : Tools::getValue('cvv'),
                        'date'    =>  Tools::getValue('datepicker'),
                        'cuotas'  =>  Tools::getValue('cuotas'),
                        'Month'   =>  Tools::getValue('Month'),
                        'Year'    =>  Tools::getValue('Year'),
                        'openpay_device_session_id' => Tools::getValue('openpay_device_session_id'),
                        'token_id' => Tools::getValue('token_id')
                        ); 

          $conf = new ConfPayu();
          $keysPayu = $conf->keys();
          $id_cart = $this->context->cart->id;
          if($conf->existe_transaccion($id_cart)){
            if(isset($this->context->cookie->{'page_confirmation'})){
              $redirect = json_decode($this->context->cookie->{'page_confirmation'});
              unset($this->context->cookie->{'page_confirmation'});
              Tools::redirectLink($redirect);
              exit();
            }else{
              Tools::redirect('/');
              exit();
            }
          }
          $address = new Address($this->context->cart->id_address_delivery); 
          $id_order = 0;
          $customer = new Customer((int) $this->context->cart->id_customer);
          $id_address = $this->context->cart->id_address_delivery;
          $dni=$conf->get_dni($this->context->cart->id_address_delivery);
          $reference_code = $customer->id . '_' . $id_cart . '_' . $id_order . '_' . $id_address;
          $_deviceSessionId = NULL;
          if (isset($this->context->cookie->deviceSessionId) && !empty($this->context->cookie->deviceSessionId) && strlen($this->context->cookie->deviceSessionId) === 32) {
            $_deviceSessionId = $this->context->cookie->deviceSessionId;
          } elseif (isset($_POST['deviceSessionId']) && !empty($_POST['deviceSessionId']) && strlen($_POST['deviceSessionId']) === 32) {
            $_deviceSessionId = $_POST['deviceSessionId'];
          } else {
            $_deviceSessionId = md5($this->context->cookie->timestamp);
          }

          $intentos = $conf->count_pay_cart($id_cart);

           $conn = PasarelaPagoCore::GetDataConnect("Tarjeta_credito");


            if ( $conn['nombre_pasarela'] == 'openpay' ) {

            // OpenPay //

              $openPay = new OpenpayController();

              if($openPay->add_charge($post,$intentos) ) {                  	

                    //$conf->pago_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);


               if ( $openPay->get_status() == 'completed' ) {

                 $this->createPendingOrder(array(), 'Tarjeta_credito', 'El sistema está en espera de la confirmación de la pasarela de pago OpenPay.', 'PS_OS_PAYMENT');
                 $order = $conf->get_order($id_cart);

                    	//	$obj_order = new Order((int) $order['id_order']);
                    	//	$obj_order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));

               } elseif ( $openPay->get_status() == 'in_progress' ) {

                 $this->createPendingOrder(array(), 'Tarjeta_credito', 'El sistema está en espera de la confirmación de la pasarela de pago OpenPay.', 'OPENPAY_WAITING_PAYMENT');
                 $order = $conf->get_order($id_cart);

               } else {
                 $openPay->add_log_error();
						 // asignar array al objeto cookie en formato texto
                 $this->context->cookie->{'error_pay'} =  json_encode($openPay->get_errors());
                 Tools::redirectLink($url_reintento);
                 exit();
               }
               $page_confirmation = __PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $this->context->cart->id . '&id_module=105&id_order=' . (int) $order['id_order']; 
               $this->context->cookie->{'page_confirmation'} = json_encode($page_confirmation);
               Tools::redirectLink($page_confirmation);
               exit();
             } else {
              $openPay->add_log_error();
                    // asignar array al objeto cookie en formato texto
              $this->context->cookie->{'error_pay'} =  json_encode($openPay->get_errors());

              Tools::redirectLink($url_reintento);
              exit();
            }

          } elseif ( $conn['nombre_pasarela'] == 'payulatam' ) {

            $currency='';

            if($conf->isTest()){

              $currency='USD';
            } else {

              $currency=$params[9]['currency'];
            }

            $subtotal = 0;

            echo '<br> '.$this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
            echo "<br>". $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
            echo "<br>".$this->context->cart->getOrderTotal();
            echo "<br>";
            if ( $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) != $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING) ) {
              $subtotal = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS_WITHOUT_SHIPPING);
            } else {
              $subtotal = $this->context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
            } 
            $tx_tax =  $params[4]['amount'] - $subtotal;     

            $data = '{
             "language":"es",
             "command":"SUBMIT_TRANSACTION",
             "merchant":{
              "apiKey":"' . $keysPayu['apiKey'] . '",
              "apiLogin":"' . $keysPayu['apiLogin'] . '"
            },
            "transaction":{

              "order":{
               "accountId":"' . $keysPayu['accountId'] . '",
               "referenceCode":"' . $params[2]['referenceCode'] . '_'.$intentos.'",
               "description":"' . $reference_code . '",
               "language":"' . $params[10]['lng'] . '",
               "notifyUrl":"' . $conf->urlv() . '",
               "signature":"' . $conf->sing($params[2]['referenceCode'] . '_'.$intentos.'~' . $params[4]['amount'] . '~'.$currency).'",
               "additionalValues":{
                "TX_VALUE":{
                 "value":' . $params[4]['amount'] . ',
                 "currency":"'.$currency.'"
               },
               "TX_TAX":{  
                "value":'.$tx_tax.',
                "currency":"'.$currency.'"
              },
              "TX_TAX_RETURN_BASE":{  
               "value":'.($tx_tax != 0 ? $subtotal : 0).',
               "currency":"'.$currency.'"
             } 
           },

           "buyer": {
            "fullName": "'.$customer->firstname.' '.$customer->lastname.'",
            "contactPhone": "'.$address->phone_mobile.'",
            "emailAddress":"'. $params[5]['buyerEmail'].'",
            "dniNumber":"'.$dni.'",   
            "shippingAddress": {
             "street1": "'.$address->address1.'",
             "street2":"N/A",    
             "city": "'.$address->city.'",
             "state": "'.$conf->get_state($address->id_state).'",
             "country": "';
             if($conf->isTest()){
              $data.='PA';
            }else{
             $data.=$this->context->country->iso_code;
           }
           $data.='",
           "postalCode": "'.$address->postcode.'",
           "phone": "'.$address->phone.'"
         }
       },      

       "shippingAddress":{
        "street1":"'.$address->address1.'",
        "street2":"N/A",
        "city":"'.$address->city.'",
        "state":"'.$conf->get_state($address->id_state).'",
        "country":"';
        if($conf->isTest()){
          $data.='PA';
        }else{
         $data.=$this->context->country->iso_code;
       }
       $data.='",
       "postalCode":"'.$address->postcode.'",
       "phone":"'.$address->phone.'"
     }  
   },
   "payer":{

    "fullName":"'.$customer->firstname.' '.$customer->lastname.'",
    "emailAddress":"'. $params[5]['buyerEmail'].'",
    "contactPhone":"'.$address->phone_mobile.'",
    "dniNumber":"'.$dni.'",
    "billingAddress":{
      "street1":"'.$address->address1.'",
      "street2":"N/A",
      "city":"'.$address->city.'",
      "state":"'.$conf->get_state($address->id_state).'",
      "country":"';
      if($conf->isTest()){
        $data.='PA';
      }else{
       $data.=$this->context->country->iso_code;
     }
     $data.='",
     "postalCode":"'.$address->postcode.'",
     "phone":"'.$address->phone.'"
   }      
 },
 "creditCard":{
   "number":"' . $post['numerot'] . '",
   "securityCode":"' . $post['codigot'] . '",
   "expirationDate":"' . $post['date'] . '",
   "name":"';
   if($conf->isTest()){
    $data.='APPROVED';
  }else{
   $data.=$post['nombre'];
 }
 $data.='"
},

"extraParameters":{
  "INSTALLMENTS_NUMBER":1
},
"type":"AUTHORIZATION_AND_CAPTURE",
"paymentMethod":"' . $paymentMethod . '",
"paymentCountry":"';
if($conf->isTest()){
  $data.='PA';
}else{
 $data.=$this->context->country->iso_code;
}
$data.='",
"deviceSessionId": "'.$_deviceSessionId.'",
"ipAddress": "'.$_SERVER['REMOTE_ADDR'].'",
"userAgent": "'.$_SERVER['HTTP_USER_AGENT'].'",
"cookie": "'.md5($this->context->cookie->timestamp).'"  
},
"test":';
if($conf->isTest()){
  $data.='true';
}else{
 $data.='false';
}
$data.='          
}
';

 exit('<pre>'.print_r($data,true).'</pre>');

$response = $conf->sendJson($data);
$subs = substr($post['numerot'], 0, (strlen($post['numerot']) - 4));
$nueva = '';

for ($i = 0; $i <= strlen($subs); $i++) {
  $nueva = $nueva . '*';
}

$data = str_replace('"number":"' . $subs, '"number":"' . $nueva, $data);
$data = str_replace('"securityCode":"' . $post['codigot'], '"securityCode":"' . '****', $data);
                // colector Errores Payu
$error_pay = array();

if ($response['code'] === 'ERROR') {
 $conf->error_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
 $error_pay[]=$response;
} elseif ($response['code'] === 'SUCCESS' && ( $response['transactionResponse']['state'] === 'PENDING' || $response['transactionResponse']['state'] === 'APPROVED' ) && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS') {
  $conf->pago_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);                       
                          if($response['transactionResponse']['state'] === 'APPROVED'){ //
                            $this->createPendingOrder(array(), 'Tarjeta_credito', 'El sistema esta en espera de la confirmación de la pasarela de pago.', 'PS_OS_PAYMENT');
                          } else{
                            $this->createPendingOrder(array(), 'Tarjeta_credito', 'El sistema esta en espera de la confirmación de la pasarela de pago.', 'PAYU_WAITING_PAYMENT');  
                          }

                          $order = $conf->get_order($id_cart);
                          $id_order = $order['id_order'];
                          $page_confirmation = __PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $this->context->cart->id . '&id_module=105&id_order=' . (int) $order['id_order']; 
                          $this->context->cookie->{'page_confirmation'} = json_encode($page_confirmation);
                          Tools::redirectLink($page_confirmation);

                        } else {
                          $conf->error_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address); 
                          $error_pay[]=array('ERROR'=>'La entidad financiera del medio de pago seleccionado, no responde.');
                        }
                        $this->context->cookie->{'error_pay'} = json_encode($error_pay);
                        Tools::redirectLink($url_reintento);
                        exit();
                      }

                    }  else {
                      $this->context->cookie->{'error_pay'} = json_encode(array('ERROR'=>'Valida tus datos he intenta de nuevo.'));
                      Tools::redirectLink($url_reintento); 
                      exit();   
                    }

                  }

                  public function displayContent() {
                    parent::displayContent();
                    self::$smarty->display(_PS_MODULE_DIR_ . 'payulatam/tpl/success.tpl');
                  }


                }

                $farmaPayu = new farmapayu();
                $farmaPayu->run();
