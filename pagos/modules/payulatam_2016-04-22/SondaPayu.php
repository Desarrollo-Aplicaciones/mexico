<?php

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/config.php');
require_once(_PS_MODULE_DIR_ . 'payulatam/paymentws.php');

class SondaPayu extends PayUControllerWS {

    private $url_reports = "";
  
    public function __construct() {
  
      if(Configuration::get('PAYU_DEMO') === 'si'){
      $this->url_reports = "https://stg.api.payulatam.com/reports-api/4.0/service.cgi";
    }else{
    $this->url_reports =  "https://api.payulatam.com/reports-api/4.0/service.cgi";
    }
 }


public function updatePendyngOrdes(){

$contar= 1;
	$orders_pendyng = $this->getPendyngOrders();

	foreach ($orders_pendyng as $key ) {

		$order = new Order((int) $key['id_order']);

    $response= $this->getByOrderId((int) $key['orderIdPayu']);


    if($response['code'] === 'SUCCESS'){
      // actualizar sonda con la fecha actual, así esta orden se consultar en función de la ultima fecha de nacionalización y el intervalo definido
      $this->update_sonda($order->id_cart);

      $update = false;

        $statePol = $this->getStatePolBymessagePol($response['result']['payload']['transactions'][0]['transactionResponse']['responseCode']);

         						           if ($statePol == 7){
                                    if($order-> getCurrentState() != (int) Configuration::get('PAYU_WAITING_PAYMENT') ){
                                    $order->setCurrentState((int) Configuration::get('PAYU_WAITING_PAYMENT'));
                                    $update = true;
                                  }
                                }
                                else if ($statePol == 4){
                                    if($order-> getCurrentState() != (int) Configuration::get('PS_OS_PAYMENT') ){
                                    $order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));
                                    $update = true;
                                  }
                                }else {
                                    if($order-> getCurrentState() != (int) Configuration::get('PS_OS_ERROR') ){
                                      $order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
                                      $update = true; 
                                    }
                               }                                

                              if (_PS_VERSION_ >= 1.5) {
                                  $payment = $order->getOrderPaymentCollection();
                                    if (isset($payment[0])) {
                                        $payment[0]->transaction_id = pSQL("payU_farmalisto_".$idCart);
                                        $payment[0]->save();
                                    }
                               }

                               if($update){ // si se actualizo la orden se crea un log da la consulta generada a payu
                                  $this->response_sonda_payu($order, $response);
                               }
  }

}
}

public function getByOrderId($order_id){

			$conf = new ConfPayu($this->url_reports);
			$keysPayu = $conf->keys();
			$data = '{
   					"test":';
        if($conf->isTest()){
          $data.='true';
          }else{
           $data.='false';
          }
      $data.=',
   					  "language":"es",
   					  "command":"ORDER_DETAIL",
   					  "merchant":{
      							       "apiLogin":"'.$keysPayu['apiLogin'].'",
      							       "apiKey":"'.$keysPayu['apiKey'].'"
   								       },
   					  "details":{
      							     "orderId":'.(int)$order_id.'
   							        }
				    }';
  
			$response= $conf->sendJson($data);
    
      return $response;

}

public function getPendyngOrders()
{ 
  $results = false;
	$sql="SELECT orders.id_cart,orders.id_order, payu.orderIdPayu,payu.transactionId,CONCAT('payU_farmalisto_', contador.id_cart,'_', contador.contador) AS reference
        	FROM "._DB_PREFIX_."orders orders 
					INNER JOIN "._DB_PREFIX_."pagos_payu payu ON(orders.id_cart = payu.id_cart)
					INNER JOIN "._DB_PREFIX_."sonda_payu sonda ON(orders.id_cart = sonda.id_cart) 
					INNER JOIN "._DB_PREFIX_."count_pay_cart contador ON (orders.id_cart = contador.id_cart)
					LEFT JOIN "._DB_PREFIX_."log_payu_response response ON(payu.orderIdPayu = response.orderIdPayu)
					WHERE orders.current_state = 18 
						AND payu.orderIdPayu !=0 
						AND ( sonda.last_update + INTERVAL sonda.`interval` MINUTE) < '".date("Y-m-d H:i:s")."' 
						AND payu.message like 'PENDING_TRANSACTION_%'
						-- AND payu.json_request LIKE CONCAT('%',CONCAT('payU_farmalisto_', contador.id_cart,'_', contador.contador),'%')
						AND  ISNULL(response.orderIdPayu)
						GROUP BY payu.id"._DB_PREFIX_."pagos_payu
						ORDER BY orders.id_order;";
   if ($results = Db::getInstance()->ExecuteS($sql) ) {   
    return $results;
  }else{
    return array();  
  }
}

public function pingPayu(){

        $conf = new ConfPayu();
        $keysPayu = $conf->keys();
  
        $data='{
                "test": ';
        if($conf->isTest()){
          $data.='true';
          }else{
           $data.='false';
          }
        $data.=',
                "language": "es",
                "command": "PING",
                "merchant":
                            {
                              "apiLogin": "'.$keysPayu['apiLogin'].'",
                              "apiKey": "'.$keysPayu['apiKey'].'"
                            }
              }';
        echo '<pre>'.print_r($data,true).'</pre><br>';
        $response= $conf->sendJson($data);
        echo('<pre>'.print_r(json_encode($response),true).'</pre>');

}

public function getByTransactionId($transaction_id){
 
  $conf = new ConfPayu($this->url_reports);
  $keysPayu = $conf->keys();
  $data='{
          "test":';
          if($conf->isTest()){
          $data.='true';
          }else{
           $data.='false';
          }
          $data.=',
          "language":"es",
          "command":"TRANSACTION_RESPONSE_DETAIL",
          "merchant":{
          "apiLogin":"'.$keysPayu['apiLogin'].'",
          "apiKey":"'.$keysPayu['apiKey'].'"
            },
          "details":{
          "transactionId":"'.$transaction_id.'"
            }
        }';

   $response= $conf->sendJson($data);
 return $response;
}

public function getByReference($reference){

  $conf = new ConfPayu($this->url_reports);
  $keysPayu = $conf->keys();
 
  $data='{
          "test":';
          if($conf->isTest()){
          $data.='true';
          }else{
           $data.='false';
          }
          $data.=',
          "language":"es",
          "command":"ORDER_DETAIL_BY_REFERENCE_CODE",
          "merchant":{
                      "apiLogin":"'.$keysPayu['apiLogin'].'",
                      "apiKey":"'.$keysPayu['apiKey'].'"
        },
          "details":{
          "referenceCode":"'.$reference.'"
          }
        }';

  $response= $conf->sendJson($data);
 return $response;
}

public function getPendyngOrdesConfirmation(){ 
  $sql="SELECT orders.id_cart,orders.id_order, payu.orderIdPayu,payu.transactionId,CONCAT('payU_farmalisto_', contador.id_cart,'_', contador.contador) AS reference,confirmacion.message
      	FROM "._DB_PREFIX_."orders orders 
			INNER JOIN "._DB_PREFIX_."pagos_payu payu ON(orders.id_cart = payu.id_cart)
      		INNER JOIN "._DB_PREFIX_."sonda_payu sonda ON(orders.id_cart = sonda.id_cart) 
			INNER JOIN "._DB_PREFIX_."count_pay_cart contador ON (orders.id_cart = contador.id_cart)
      		INNER JOIN "._DB_PREFIX_."log_payu_response confirmacion ON(contador.id_cart = confirmacion.id_cart)
			WHERE orders.current_state = 18 AND payu.orderIdPayu !=0
			AND payu.orderIdPayu = confirmacion.orderIdPayu  
			-- AND confirmacion.reponse LIKE CONCAT('%',CONCAT('payU_farmalisto_', contador.id_cart,'_', contador.contador),'%')
			GROUP BY payu.id"._DB_PREFIX_."pagos_payu ORDER BY orders.id_order;
      ";
     if ($results = Db::getInstance()->ExecuteS($sql) ) {   
    return $results;
  }else{
    return array();  
  }    
}


public function updatePendyngOrdesConfirmation(){

  $orders_pendyng = $this->getPendyngOrdesConfirmation();

  foreach ($orders_pendyng as $key ) {


          $order = new Order((int) $key['id_order']);
          $statePol = $this->getStatePolBymessagePol( $key['message']);
            
                        if ($statePol == 7){
                            if($order-> getCurrentState() != (int) Configuration::get('PAYU_WAITING_PAYMENT') )
                            $order->setCurrentState((int) Configuration::get('PAYU_WAITING_PAYMENT'));
                        }
                        else if ($statePol == 4){
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_PAYMENT') )
                            $order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));
                        }
                        else {
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_ERROR') ){
                            $order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
                            }  
                          }

        if (_PS_VERSION_ >= 1.5) {
            $payment = $order->getOrderPaymentCollection();
            if (isset($payment[0])) {
               $payment[0]->transaction_id = pSQL("payU_farmalisto_".$key['id_cart']);
               $payment[0]->save();
            }
        }                          
echo '<br>id_order: '.$key['id_order'];
  }
}

public function getStatePolBymessagePol($message_pol){
  switch ($message_pol) {
    case 'APPROVED':
      return 4;
      break;

    case 'ANTIFRAUD_REJECTED':
      return 6;
      break;
      case 'REJECTED':
      return 6;
      break;  

    case 'BANK_UNREACHABLE':
      return 6;
      break;

      case 'ERROR_CONVERTING_TRANSACTION_AMOUNTS':
      return 6;
      break;
      
    case 'EXPIRED_CARD':
      return 6;
      break;

    case 'EXPIRED_TRANSACTION':
      return 5;
      break;
    case 'INTERNAL_PAYMENT_PROVIDER_ERROR':
      return 6;
      break; 
    case 'INVALID_CARD':
      return 6;
      break; 
    case 'NOT_ACCEPTED_TRANSACTION':
      return 6;
      break; 
      case 'PAYMENT_NETWORK_REJECTED':
      return 6;
      break; 
      case 'RESTRICTED_CARD':
      return 6;
      break;                                               
      case 'PENDING_TRANSACTION_CONFIRMATION':
      return 7;
      break;  
      case 'PENDING':
      return 7;
      break;
      case 'PENDING_TRANSACTION_REVIEW':
      return 7;
      break;
   
    default:
      return 6;
      break;
  }
}

public function update_sonda($id_cart){
  $date = date("Y-m-d H:i:s");
  $sql="update "._DB_PREFIX_."sonda_payu
        set last_update = '".$date."'
        WHERE id_cart = ".(int)$id_cart.";";

        if (Db::getInstance()->Execute($sql) ) {   
            return true;
          }
          return false;
}

public function response_sonda_payu($order, $response_ws){

        $date = date("Y-m-d H:i:s");
        $sql="INSERT INTO `"._DB_PREFIX_."response_sonda_payu` (`id_order`, `id_cart`, `response_ws`, `date_add`, `responseCode`, `id_transaction`, `id_payload`)
              VALUES (".(int)$order->id.", ".(int)$order->id_cart.", '".print_r($response_ws,true)."', '".$date."', '".$response_ws['result']['payload']['transactions'][0]['transactionResponse']['responseCode']."', '".$response_ws['result']['payload']['transactions'][0]['id']."', ".$response_ws['result']['payload']['id'].");";
        
           if (Db::getInstance()->Execute($sql) ) {   
            return true;
          }
          return false;
}

public function getPendyngOrdersDaysAgo($days = 12)
{ 
  $results = false;
  $sql="SELECT orders.id_cart,orders.id_order, payu.orderIdPayu,payu.transactionId,CONCAT('payU_farmalisto_', contador.id_cart,'_', contador.contador) AS reference
          FROM "._DB_PREFIX_."orders orders 
          INNER JOIN "._DB_PREFIX_."pagos_payu payu ON(orders.id_cart = payu.id_cart)
          INNER JOIN "._DB_PREFIX_."sonda_payu sonda ON(orders.id_cart = sonda.id_cart) 
          INNER JOIN "._DB_PREFIX_."count_pay_cart contador ON (orders.id_cart = contador.id_cart)
          LEFT JOIN "._DB_PREFIX_."log_payu_response response ON(payu.orderIdPayu = response.orderIdPayu)
          WHERE orders.current_state = ".(int)Configuration::get('PAYU_WAITING_PAYMENT')." 
            AND payu.orderIdPayu !=0 
            AND payu.message like 'PENDING_TRANSACTION_%'
            AND orders.date_add < date_add(NOW(), INTERVAL -".(int)$days." DAY)
            AND  ISNULL(response.orderIdPayu)
            GROUP BY payu.idps_pagos_payu
            ORDER BY orders.id_order;";
            
   if ($results = Db::getInstance()->ExecuteS($sql) ) {   
    return $results;
  }else{
    return array();  
  }
}


public function updatePendyngOrdesDaysAgo($days = 12, $estate){

  $orders_pendyng = $this->getPendyngOrdersDaysAgo($days);
  foreach ($orders_pendyng as $key ) {
    $order = new Order((int) $key['id_order']);
    if($order-> getCurrentState() != (int) Configuration::get($estate) ){
       $order->setCurrentState((int) Configuration::get($estate));    
  }
}
}

}