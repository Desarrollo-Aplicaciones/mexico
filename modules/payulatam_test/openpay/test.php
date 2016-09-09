<?php

require_once("./Openpay.php"); 
//Por default se usa el ambiente de sandbox
//

$openpay = Openpay::getInstance('mzdtln0bmtms6o3kck8f', 'sk_e568c42a6c384b7ab02cd47d2e407cab');

// Estructura OpenpayCustomer
$customerData = array(
     'external_id' => '12345678',
     'name' => 'customer name',
     'last_name' => '',
     'email' => 'customer_email8@farmalisto.com.mx',
     'requires_account' => true,
     'phone_number' => '51209087654',
     'address' => array(
         'line1' => 'Calle 10',
         'line2' => 'col. san pablo',
         'line3' => 'entre la calle 1 y la 2',
         'state' => 'Queretaro',
         'city' => 'Queretaro',
         'postal_code' => '76000',
         'country_code' => 'MX'
      )
   );


$card = array(
    'card_number' => '4111111111111111',
    'holder_name' => 'Juan Perez Ramirez',
    'expiration_year' => '20',
    'expiration_month' => '12',
    'cvv2' => '110');

$chargeRequest = array(
    'method' => 'card',
    'card' => $card,
    'amount' => 100,
    'currency' => 'MXN',
    'description' => 'Cargo inicial a mi cuenta',
    'order_id' => 'MX-'.randomNumber(5).'-1',
    'metadata' => array(
      'destino' => 'Mexico-Queretaro Corrida 1', 
      'no_autobus' => '42123', 
      'no_asiento' => '25',
      'fecha_compra' => '2014-11-26 19:11:12', 
      'iva' => '123.32'
    )
  );



try {
// GET customer
$customer = $openpay->customers->get('atvh89rptzykaqtdkm8x');
//$address=$customer->address;

//echo '<br> line1 => '.$address->line1.' postal_code => '.$address->postal_code;
//echo '<br>id =>'.$customer->id.'<br>';
//echo'<b>Customer </b><br> <pre>'.print_r($customer,true).'</pre>';
// add cargo CC	
//$charge = $customer->charges->create($chargeRequest);	

//Consultar OpenpayCustomer
//$customer = $openpay->customers->get('atvh89rptzykaqtdkm8x');
// crear OpenpayCustomer
//$var = $customer = $openpay->customers->add($customerData);


// get cargo
$customer = $openpay->customers->get('atvh89rptzykaqtdkm8x');
$charge = $customer->charges->get('trs3xfbgy8i06lgo14k2');

echo '<pre>'.print_r($charge->card->bank_code,true).'</pre>';
echo '<pre>'.print_r($charge->status,true).'</pre>';
echo '<pre>'.print_r($charge->error_message,true).'</pre>';
echo '<pre>'.print_r($charge,true).'</pre>';
	
} catch (Exception $e) {
	echo 'Error: <pre>'.print_r($e,true).'</pre>';
}

    function randomNumber($length) {
   $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
    }


?>