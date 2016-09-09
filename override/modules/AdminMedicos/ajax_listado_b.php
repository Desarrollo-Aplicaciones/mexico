<?php

//header('Content-Type: application/xhtml+xml; charset=utf-8');

require_once('../modules/payulatam/config.php');
                
 $conf=  new ConfPayu();
 
 $keysPayu= $conf->keys();
 
$js_send='{
"language":"es",
"command":"GET_BANKS_LIST",
"merchant":{
"apiLogin":"'.$keysPayu['apiLogin'].'",
"apiKey":"'.$keysPayu['apiKey'].'"
},
"test":false,
"bankListInformation":{
"paymentMethod":"PSE",
"paymentCountry":"CO"
}
}';


$xml_send='
<request>
<language>es</language>
<command>GET_BANKS_LIST</command>
<merchant>
<apiLogin>'.$keysPayu['apiLogin'].'</apiLogin>
<apiKey>'.$keysPayu['apiKey'].'</apiKey>
</merchant>
<isTest></isTest>
<bankListInformation>
<paymentMethod>PSE</paymentMethod>
<paymentCountry>CO</paymentCountry>
</bankListInformation>
</request>';


//print_r($js_send);
// $arrayBanck=$conf->sendJson($js_send);
$arrayBanck=$conf->sendXml($xml_send);

/*echo "<pre>";
print_r($arrayBanck);
*/
 //[bankListResponse] => Array ( [code] => SUCCESS [banks] => Array ( [0] => Array ( [bank] => Array ( 0 .. n

$bancos=$arrayBanck['bankListResponse']['banks'][0]['bank'];

$str_nombancos['']='- Seleccione Entidad -';

foreach ($bancos as $row){
	$str_nombancos[ $row['description'] ]= $row['description'] ;
}


?>