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
include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');

class farmapayu extends PayUControllerWS {

    public $ssl = true;

    public function setMedia() {
        parent::setMedia();
    }

    public function process() {

        if (empty($this->context->cart->id)) {
            Tools::redirect('/');
        }

        parent::process();

        $arraypaymentMethod=  array("VISA"=>'VISA','DISCOVER'=>'DINERS','AMERICAN EXPRESS'=>'AMEX','MASTERCARD'=>'MASTERCARD');

        $messsage = '';
        if (isset($_POST['numerot']) && !empty($_POST['numerot']) && strlen($_POST['numerot']) > 13 && strlen((int) $_POST['numerot']) < 17
                    && isset($_POST['nombre']) && !empty($_POST['nombre']) && isset($_POST['codigot']) && !empty($_POST['codigot']) && 
                    isset($_POST['datepicker']) && !empty($_POST['datepicker']) && isset($_POST['cuotas']) && !empty($_POST['cuotas']) ) {
            // reglas de carrito para bines
            $payulatam = new PayULatam();
            $bin = $payulatam->addCartRuleBin($_POST['numerot']);
            $paymentMethod='';
            $key=json_decode(file_get_contents('http://www.binlist.net/json/'.(int)$bin), TRUE)['brand'];
            if (array_key_exists($key, $arraypaymentMethod)) {
                $paymentMethod = $arraypaymentMethod[$key];
            }
            $params = $this->initParams();
            // se optinen los datos del formulario de pago farmalisto    
            $post = array('nombre' => $nombre = $_POST['nombre'],
                          'numerot' => $numerot = $_POST['numerot'],
                          'codigot' => $codigot = $_POST['codigot'],
                          'date' => $year = $_POST['datepicker'],
                          'cuotas' => $cuotas = $_POST['cuotas']//,
                          //'mediop' => $mediop = $_POST['mediop']
                          // 'dninumber'=>$dninumber=$_POST['dninumber']
                        );

            $conf = new ConfPayu();
            $keysPayu = $conf->keys();
            $address = new Address($this->context->cart->id_address_delivery);

            $customer = new Customer((int) $this->context->cart->id_customer);
            $id_cart = $this->context->cart->id;
            $id_address = $this->context->cart->id_address_delivery;

            //$this->createPendingOrder();
            //$order = $conf->get_order($id_cart);
            //$id_order = $order['id_order'];
            $id_order= 0;
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

            $currency='';
            if($conf->isTest()){
                $currency='USD';
              }else{
                $currency=$params[9]['currency'];
              }
  //echo '<pre>'.print_r( $this->context->cart->getOrderTotals(),true).'</pre>';

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
// exit('<pre>'.print_r($data,true).'</pre>');
            $response = $conf->sendJson($data);

            $subs = substr($numerot, 0, (strlen($numerot) - 4));
            $nueva = '';
            for ($i = 0; $i <= strlen($subs); $i++) {
                $nueva = $nueva . '*';
            }
            $data = str_replace('"number":"' . $subs, '"number":"' . $nueva, $data);
            $data = str_replace('"securityCode":"' . $codigot, '"securityCode":"' . '****', $data);

            if ($response['code'] === 'ERROR') {

                $conf->failed_transaction($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['code'], $this->context->cart->id, $id_address);
                $messsage = '<h1>¡Error!, Transaccion Rechazada</h1><br>' . $response['error'];
            }
            elseif ($response['code'] === 'SUCCESS' && ( $response['transactionResponse']['state'] === 'PENDING' || $response['transactionResponse']['state'] === 'APPROVED' ) && $response['transactionResponse']['responseMessage'] != 'ERROR_CONVERTING_TRANSACTION_AMOUNTS') {

                    $conf->pago_payu($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);
                    $this->createPendingOrder(array(), 'Tarjeta_credito', 'El sistema esta en espera de la confirmación de la pasarela de pago.');
                    $order = $conf->get_order($id_cart);
                    $id_order = $order['id_order'];
                    Tools::redirectLink(__PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $this->context->cart->id . '&id_module=105&id_order=' . (int) $order['id_order']);
            } 
            else {
                  $conf->failed_transaction($id_order, $customer->id, $data, $response, 'Tarjeta_credito', $response['transactionResponse']['state'], $this->context->cart->id, $id_address);
                  $messsage='Error, La entidad financiera del medio de pago seleccionado, no responde. Por favor intente con otro medio de pago o reintente mas tarde.'; 
            }
            Tools::redirectLink(__PS_BASE_URI__ . 'index.php?controller=order?step=3&paso=pagos&error_pay=tc');
            exit();
/*
        $obj_order = new Order((int) $id_order);
        $obj_order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
        $messsage.=' <div id="messsage_payu">  <a href="' . $this->context->smarty->tpl_vars['content_dir']->value . 'index.php?controller=order&submitReorder=&id_order=' . $id_order . '">  Intenta con otro medio de pago </a>  </div> ';

        self::$smarty->assign('messsage', $messsage);
        */
            
        }  else {
           Tools::redirect(__PS_BASE_URI__ . 'index.php?controller=order?step=3&paso=pagos');    
        }

    }

    public function displayContent() {
        parent::displayContent();

        self::$smarty->display(_PS_MODULE_DIR_ . 'payulatam/tpl/success.tpl');
    }
   

}

$farmaPayu = new farmapayu();
$farmaPayu->run();
