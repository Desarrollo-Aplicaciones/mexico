<?php
include_once(dirname(__FILE__)."/../../config/config.inc.php");
 $codigo_icr = 0; 
############################
#metodo que conecta a la db#
############################
  $host="localhost";     $user="root";     $pass="";
    $dbname="test_colombia";
$host=_DB_SERVER_;
$user=_DB_USER_;
$pass=_DB_PASSWD_;
$dbname=_DB_NAME_;
//estableciendo conexion correspondiente
    $conexion=mysql_connect($host,$user,$pass);

    mysql_select_db($dbname,$conexion)or die("Error en la selección de la base de datos");

 $variableId = $_POST['generacion'];

#####################################




$result123 = mysql_query("SELECT i.id_icr,i.cod_icr,e.firstname,e.lastname,h.fecha,i.id_estado_icr from ps_icr i INNER JOIN ps_icr_history h on h.id_icr = i.id_historico INNER JOIN ps_employee e on e.id_employee = h.id_empleado where i.id_historico  = '".$variableId."'");

echo "<table  width='700' border='1' align='left' cellpadding='2' cellspacing='0' bordercolor='#CCCCCC'>
  <tr>
  <td>Identificador codigo</td>
  <td>Codigos</td>
  <td>Nombre empleado</td>
  <td>Apellido empleado</td>
  <td>Fecha en que se creo</td>
  <td>Estado</td>
  </tr>
  <tr>";
 while ($row = mysql_fetch_array($result123, MYSQL_ASSOC)) {

        
   
echo "<br>";    
   
    foreach ($row as $col_value) {


     echo "<td>$col_value</td>";

   }
   echo "</tr>";
 }
 echo "</table>";           





header("Content-type: application/force-download");
 header("Content-Disposition: attachment; filename=".basename("Archivo_Rangos_Icr.xls"));
 header("Content-Transfer-Encoding: binary");


?>