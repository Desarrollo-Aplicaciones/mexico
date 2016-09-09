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
			<td>Url</td>
			<td></td>
			<td><input type="text" name="url" value="<?php if(isset($_POST['url'])) echo $_POST['url']; ?>"> </td>
			<td></td>
			<td></td>
		</tr>
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

if(isset($_POST)){

	$array = array(); 
	eval('$array = '.$_POST['data'].';');
	echo '<b> Respuesta</br> ||<pre>'.print_r(httpPost($_POST['url'],$array),true).'</pre>||';
}


function httpPost($url,$params)
{
  $postData = '';
   //create name value pairs seperated by &
   foreach($params as $k => $v)
   {
      $postData .= $k . '='.$v.'&';
   }
   rtrim($postData, '&');
 
    $ch = curl_init(); 
 
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POST, count($postData));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);   
 
    $output=curl_exec($ch);
 	$info = curl_getinfo($ch);
 	echo '<b>Info Solicitud: </br><pre>'.print_r($info,true).'</pre><br>';
    curl_close($ch);
    return $output;
 }


?>