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
  
                 
  // ConfPayu($test =true) en modo test
  //ConfPayu($test =false) en modo producción
  public function ConfPayu($test =false)
  {
      
      $this->test=$test;
      
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
CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json; charset=utf-8",
                            "Accept: application/json"),
CURLOPT_POSTFIELDS =>$data)); //json_encode($postData) 

$response = curl_exec($ch); // enviando datos al servidor de payuLatam




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

// Url archivo de verificaciÃ³n webservice   
        $nombre_archivo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $nombre_archivo = explode('/', $nombre_archivo);
        $var = array_pop($nombre_archivo);
        $nombre_archivo = implode('/', $nombre_archivo);
        return $urlValidation = 'http://' . $_SERVER['HTTP_HOST'] . $nombre_archivo . '/validationws.php';
    }

    public function pago_payu($id_order, $id_customer, $json_request, $json_response, $method, $extras, $id_cart, $id_address) {
        $array_rq = json_decode($json_request, TRUE);
        try {

            $mysqldate = date("Y-m-d H:i:s");

            $log = 'Fecha de transacci�n-WS: ' . $mysqldate . '\r\nRequest:\r\n' . $json_request . '\r\nResponse: \r\n' . json_encode($json_response);
            $this->logtxt($log);

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
            //$contenido="-- lo que quieras escribir en el archivo -- \r\n";
$fp=fopen("/var/www/farmalisto.com.mx/uploads/log_payu/log_payu.txt","a+");
fwrite($fp,$text."\r\n");
fclose($fp) ;
            
        }
    
 }
     


