<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Convert Array-str to POST or GET</title>
  <link rel="stylesheet" href="">
</head>
<body>

<form action="" method="POST" accept-charset="utf-8">


  <table border="0">

    <tr>
      <td></td>
      <td></td>
      <td><textarea id="data" name="data" cols="180" rows="13" ><?php 
if(isset($_POST['data']))
  echo $_POST['data'];
        ?></textarea> </td>
      <td></td>
      <td></td>
    </tr>

    <tr>
      <td></td>
      <td></td>
      <td><input type="submit" value="Enviar Solicitud" id="submit" name="submit"> </td>
      <td></td>
      <td></td>
    </tr>
  </table>
  </form> 
</body>
</html>

<?php

if(isset($_POST["data"])){
  
sendJson($_POST["data"]);

}


 function sendJson($data)
 {

$ch = curl_init('https://api.mercadolibre.com/users/test_user?access_token=zFbTcDB69nRoa4j5vQyfRzIWc%2Fc%3D');  


curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // deshabilitar la validacion SSl (false)
curl_setopt_array($ch, array(
CURLOPT_POST => TRUE,
CURLOPT_RETURNTRANSFER => TRUE,
CURLOPT_HTTPHEADER => array(
                            "Content-Type: application/json; charset=utf-8",
                            "Accept: application/json"),
CURLOPT_POSTFIELDS =>$data)); //json_encode($postData) 

$response = curl_exec($ch); // enviando datos al servidor de payuLatam

echo '<br> <b>Respuesta</b><pre>'.print_r($response,true).'</pre>';



 
}