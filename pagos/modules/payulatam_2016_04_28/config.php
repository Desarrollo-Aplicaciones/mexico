<?php
require_once('XmlToArray.class.php');
class ConfPayu {

 private $testing=  array('apiKey'=>'6u39nqhq8ftd0hlvnjfs66eh8c',
                          'apiLogin'=>'11959c415b33d0c',
                          'merchantId'=>'500238',
                          'accountId'=>'500537',
                          'pse-CO'=>'500538');
 
 private $production=  array('apiKey'=>'1frsu8h7400repsub01sujbpum',
                             'apiLogin'=>'05c78842c916bfc',
                             'merchantId'=>'503351',
                             'accountId'=>'515914',
                             'pse-CO'=>'504266');

 private $test= false;

 private $url_service = NULL;

 public function __construct($url_service = NULL,$test=TRUE) {
  if(Configuration::get('PAYU_DEMO') == 1){
    $this->test = TRUE;
  }else{
    $this->test = FALSE;
  }
  $this->url_service = $url_service;

}


public function keys()
{
  if ($this->test)
  {
    return $this->testing;    
  }
  else {
    return $this->production;    
  }
}



public function sendJson($data)
{
  $responseData ='';

  try {
    $ch =NULL;

    if($this->url_service != NULL ){
      $ch = curl_init($this->url_service);
    }
    else if($this->test)
    {  
     $ch = curl_init('https://stg.api.payulatam.com/payments-api/4.0/service.cgi');
   }
   else {
     $ch = curl_init('https://api.payulatam.com/payments-api/4.0/service.cgi');  
   }

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // deshabilitar la validacion SSl (false)
curl_setopt_array($ch, array(
                  CURLOPT_POST => TRUE,
                  CURLOPT_RETURNTRANSFER => TRUE,
                  CURLOPT_HTTPHEADER => array(
                                              "Content-Type: application/json; charset=utf-8",
                                              "Accept: application/json"),
CURLOPT_POSTFIELDS =>$data)); //json_encode($postData) 

$response = curl_exec($ch); // enviando datos al servidor de payuLatam

$info = curl_getinfo($ch);
// echo '<br><b>Info Solicitud: </br><pre>'.print_r($info,true).'</pre><br>';
curl_close($ch);

if($response === FALSE) // si hay errores
{
   //die(curl_error($ch));
  return false;
}

return $responseData = json_decode($response, TRUE); // decodificando el formato Json

} catch (Exception $ex) {
 return false;
}

}


public function sendXml($data)
{
  $responseData ='';
  try {
    $ch = NULL;

    if($this->test)
    {
     $ch = curl_init('https://stg.api.payulatam.com/payments-api/4.0/service.cgi');
   }

   else {
     $ch = curl_init('https://api.payulatam.com/payments-api/4.0/service.cgi');  
   }


curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // deshabilitar la validacion SSl (false)
curl_setopt_array($ch, array(
                  CURLOPT_POST => TRUE,
                  CURLOPT_RETURNTRANSFER => TRUE,
                  CURLOPT_HTTPHEADER => array("Accept:application/xml","Content-Type:application/xml"),
CURLOPT_POSTFIELDS =>$data)); //json_encode($postData) 

$response = curl_exec($ch); // enviando datos al servidor de payuLatam

if($response === FALSE) // si hay errores
{

   //die(curl_error($ch));
  return false;
}

//Creating Instance of the Class
$xmlObj    = new XmlToArray($response);

//Creating Array
return $arrayData = $xmlObj->createArray();


//return ($response);//json_decode($response, TRUE); // decodificando el formato Json

} catch (Exception $ex) {

 return false;
}

}

public function randString ($length = 32)
{  
 $string = "";
 $possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
 $i = 0;
 while ($i < $length)
 {    
  $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
  $string .= $char;    
  $i++;  
}  
return $string;
}


public function sing($str)

{
  $keys=$this->keys();
  return md5($signature=$keys['apiKey'].'~'.$keys['merchantId'].'~'.$str); 
}

public function urlv() {

  $protocolo=NULL;
  if(Utilities::is_ssl())
  {
    $protocolo='https://';
  }else{
    $protocolo='http://';
  }

// Url archivo de verificaciÃƒÂ³n webservice   
  $nombre_archivo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  $nombre_archivo = explode('/', $nombre_archivo);
  $var = array_pop($nombre_archivo);
  $nombre_archivo = implode('/', $nombre_archivo);
  return $urlValidation = $protocolo.$_SERVER['HTTP_HOST'] . $nombre_archivo . '/validationws.php';
}

public function pago_payu($id_order, $id_customer, $json_request, $json_response, $method, $extras, $id_cart, $id_address) {
  $array_rq = json_decode($json_request, TRUE);
  try {

    $mysqldate = date("Y-m-d H:i:s");

    $log = 'Fecha de transacción-WS: ' . $mysqldate . '\r\nRequest: \r\n' . $json_request . '\r\nResponse: \r\n' . json_encode($json_response);
    $this->logtxt($log);
    

    if (isset($json_response['transactionResponse']['extraParameters']['BAR_CODE'])) {
      $extras = $json_response['transactionResponse']['extraParameters']['REFERENCE'].';'.date('d/m/Y', substr($json_response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3)).';'.$json_response['transactionResponse']['extraParameters']['BAR_CODE'];

    }elseif (isset($json_response['transactionResponse']['extraParameters']['URL_PAYMENT_RECEIPT_HTML'])) {
      $extras = $json_response['transactionResponse']['extraParameters']['REFERENCE'].';'.date('d/m/Y', substr($json_response['transactionResponse']['extraParameters']['EXPIRATION_DATE'], 0, -3));

    }

    Db::getInstance()->autoExecute('ps_pagos_payu', array(
                                   'idps_pagos_payu' => (int) 0,
                                   'fecha' => pSQL($mysqldate),
                                   'id_order' => (int) $id_order,
                                   'id_customer' => (int) $id_customer,
                                   'json_request' => pSQL(addslashes($json_request)),
                                   'json_response' => pSQL(addslashes(json_encode($json_response))),
                                   'method' => pSQL($method),
                                   'extras' => pSQL($extras),
                                   'id_cart' => (int) $id_cart,
                                   'id_address' => (int) $id_address,
                                   'transactionId' => PSQL($json_response['transactionResponse']['transactionId']),
                                   'valor' => (int) $array_rq['transaction']['order']['additionalValues']['TX_VALUE']['value'],
                                   'orderIdPayu' => (int) $json_response['transactionResponse']['orderId'],
                                   'message'=>PSQL($json_response['transactionResponse']['responseCode']),                
                                   ), 'INSERT');
} catch (Exception $exc) {
  Logger::AddLog('payulatam [config.php] pago_payu() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
}
}

public function error_payu($id_order, $id_customer, $json_request, $json_response, $method, $extras, $id_cart, $id_address) {
  $array_rq = json_decode($json_request, TRUE);
  try {

    $mysqldate = date("Y-m-d H:i:s");

    $log = 'Fecha de transacción-WS: ' . $mysqldate . '\r\nRequest: \r\n' . $json_request . '\r\nResponse: \r\n' . json_encode($json_response);
    $this->logtxt($log);

    Db::getInstance()->autoExecute('ps_error_payu', array(
                                   'idps_pagos_payu' => (int) 0,
                                   'fecha' => pSQL($mysqldate),
                                   'id_order' => (int) $id_order,
                                   'id_customer' => (int) $id_customer,
                                   'json_request' => pSQL(addslashes($json_request)),
                                   'json_response' => pSQL(addslashes(json_encode($json_response))),
                                   'method' => pSQL($method),
                                   'extras' => pSQL($extras),
                                   'id_cart' => (int) $id_cart,
                                   'id_address' => (int) $id_address,
                                   'transactionId' => PSQL($json_response['transactionResponse']['transactionId']),
                                   'valor' => (int) $array_rq['transaction']['order']['additionalValues']['TX_VALUE']['value'],
                                   'orderIdPayu' => (int) $json_response['transactionResponse']['orderId'],
                                   'message'=>PSQL($json_response['transactionResponse']['responseCode']),                
                                   ), 'INSERT');
} catch (Exception $exc) {
  Logger::AddLog('payulatam [config.php] pago_payu() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
}
}

public function log_response_ws($array_rs) {


  $reference_code = explode("_", $array_rs['description']);

  try {
    $mysqldate = date("Y-m-d H:i:s");

    Db::getInstance()->autoExecute('ps_log_payu_response', array(
                                   'id_log_payu_response' => (int) 0,
                                   'date' => pSQL($mysqldate),
                                   'reponse' => pSQL(var_export($array_rs,TRUE)),
                                   'id_order' => (int) $reference_code[2],
                                   'id_customer' => (int) $reference_code[0],
                                   'id_cart' => (int) $reference_code[1],
                                   'id_address' => (int) $reference_code[3],
                                   'transactionId' => PSQL($array_rs['transaction_id']),
                                   'valor' => (int) $array_rs['value'],
                                   'orderIdPayu' => (int) $array_rs['reference_pol'],
                                   'message'=>PSQL($array_rs['response_message_pol']),
                                   ), 'INSERT');
  } catch (Exception $exc) {
    Logger::AddLog('payulatam [config.php] pago_payu() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
  }
}

public function failed_transaction($id_order, $id_customer, $json_request, $json_response, $method, $extras, $id_cart, $id_address) {
  $array_rq = json_decode($json_request, TRUE);

  try {

    if (!isset($json_response['transactionResponse']['responseCode'])){
      $errortransaction = explode(",", $json_response['error']);
      $json_response['transactionResponse']['responseCode'] = $errortransaction[0];
    }

    $mysqldate = date("Y-m-d H:i:s");

    Db::getInstance()->autoExecute('ps_pagos_payu', array(
                                   'idps_pagos_payu' => (int) 0,
                                   'fecha' => pSQL($mysqldate),
                                   'id_order' => (int) $id_order,
                                   'id_customer' => (int) $id_customer,
                                   'json_request' => pSQL(addslashes($json_request)),
                                   'json_response' => pSQL(addslashes(json_encode($json_response))),
                                   'method' => pSQL($method),
                                   'extras' => pSQL($extras),
                                   'status' => pSQL('0'),
                                   'id_cart' => (int) $id_cart,
                                   'id_address' => (int) $id_address,
                                   'transactionId' => PSQL($json_response['transactionResponse']['transactionId']),
                                   'valor' => (int) $array_rq['transaction']['order']['additionalValues']['TX_VALUE']['value'],
                                   'orderIdPayu' => (int) $json_response['transactionResponse']['orderId'],
                                   'message'=>PSQL($json_response['transactionResponse']['responseCode']),                 
                                   ), 'INSERT');
} catch (Exception $exc) {
  Logger::AddLog('payulatam [config.php] pago_payu() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
}
}

public function get_order($id_cart) {
  try {
    $sql = 'select ord.* 
    from ps_orders ord INNER JOIN ps_cart car ON(ord.id_cart=car.id_cart) 
    WHERE  ord.id_cart=' . $id_cart . ' Limit 1';

    if ($results = Db::getInstance()->ExecuteS($sql)) {
      foreach ($results as $row) {
        return $row;
      }
    }
    return null;
  } catch (Exception $exc) {
    Logger::AddLog('payulatam [config.php] get_order() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
    return null;
  }
}

public function logtxt ($text="")
{

/*$fp=fopen("/home/ubuntu/log_payu/log_payu.txt","a+");
fwrite($fp,$text."\r\n");
fclose($fp) ;
            */
}

public function get_state($id_state)
{
  $query="select state.`name` FROM 
  ps_state state
  WHERE state.id_state=".(int)$id_state.' limit 1;';


  if ($results = Db::getInstance()->ExecuteS($query)) {

   if(count($results)>0){

     return $results[0]['name'];
   }   
 }

 return null; 
}

public function get_address($id_customer, $id_address_delivery) {


  $sql = 'select ad.address1,city,phone_mobile,phone,dni, st.`name` as state, co.iso_code   
  from ps_address ad, ps_state st, ps_country co  where ad.id_customer=' . $id_customer . ''
  . ' and ad.id_address=' . $id_address_delivery . ' and ad.id_state= st.id_state and
  co.id_country =ad.id_country';


  if ($results = Db::getInstance()->ExecuteS($sql)) {
    if (count($results) > 0) {
      return $results[0];
    }
  }
  return FALSE;
}

public function get_dni($id_address) {

  $sql = 'select cus.identification, adr.dni
  from ps_address adr INNER JOIN ps_customer cus ON (adr.id_customer = cus.id_customer) 
  WHERE adr.id_address=' . (int) $id_address . ';';

  $dni =  'N/A';

  if ($results = Db::getInstance()->ExecuteS($sql)) {

    foreach ($results as $row) {



      if ($row['identification'] != NULL && $row['identification'] != '0') {
        $dni = $row['identification'];
      } else if ($row['dni'] != '1111' && $row['dni'] != '') {
        $dni = $row['dni'];
      } else {
        $dni = 'N/A';
      }
    }
  }
  return $dni;
}

public function isTest() {
  return $this->test;
}

public function count_pay_cart($id_cart){

  $query= "SELECT id_cart,contador
  FROM "._DB_PREFIX_."count_pay_cart
  WHERE id_cart = ".(int)$id_cart;

  $row = Db::getInstance()->getRow($query);

  if ( isset($row) && count($row) > 1 && is_array($row)){
    $sql= "UPDATE "._DB_PREFIX_."count_pay_cart SET contador = ". ((int)$row['contador'] + 1) ." WHERE id_cart = ".$id_cart;

    if(Db::getInstance()->Execute($sql))
      return ($row['contador']+1);
  }
  else{
    $ini=1;
    $sql="INSERT INTO "._DB_PREFIX_."count_pay_cart (id_cart,contador)
    VALUES(".$id_cart.",".$ini.")";   
    if(Db::getInstance()->Execute($sql))
     return $ini;
 }
}

public function get_intentos($id_cart){
  $query=  "SELECT id_cart,contador
  FROM "._DB_PREFIX_."count_pay_cart
  WHERE id_cart = ".(int)$id_cart;

  $row = Db::getInstance()->getRow($query);
  if(isset($row['contador'])){
    return $row['contador'];
  }else{
    return false;
  }

}

public function existe_transaccion($id_cart){
  $sql ="SELECT id_cart
  FROM "._DB_PREFIX_."openpay_transaction
  WHERE id_cart = ".(int)$id_cart;
  $row = Db::getInstance()->getRow($sql);
  if(isset($row['id_cart']) && !empty($row['id_cart'])){
    return TRUE;
  }else{
    return FALSE;
  }
}

}



