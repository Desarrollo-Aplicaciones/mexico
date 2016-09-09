<?php
include(dirname(__FILE__).'/../config/config.inc.php');
include(dirname(__FILE__).'/../init.php');


$dia=(int) date("N");

try {
    


if($dia===4)
{
  $fecha=strtotime ( '+1 day' , strtotime (date('Y-m-j') ) ) ;
  $inicio = date ( 'Y-m-j ' , $fecha ).'00:00:00';
  $fin = date ( 'Y-m-j ' , $fecha ).'23:59:59';
  
  $query="UPDATE ps_cart_rule
set date_from ='".$inicio."', date_to = '".$fin."'
WHERE id_cart_rule = 169085";
  
  if(Db::getInstance()->ExecuteS($query))
 
  {
    echo 'Rango de fechas '.$inicio.' - '.$fin;
    echo '<br>';
   }
}else{
    echo $dia;
}

} catch (Exception $exc) {
    // echo $exc->getTraceAsString();
}
?>