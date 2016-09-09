<?php
include_once(dirname(__FILE__)."/../../config/config.inc.php");
 $codigo_icr = 0; 
############################
#metodo que conecta a la db#
############################

 $variableId = Tools::getValue('generacion');

#####################################

$query = new DbQuery();
      $query->select('i.id_icr,i.cod_icr,e.firstname,e.lastname,h.fecha,i.id_estado_icr');
      $query->from('icr', 'i');
      $query->innerJoin('icr_history', 'h', ' h.id_icr = i.id_historico ');
      $query->innerJoin('employee', 'e', ' e.id_employee = h.id_empleado ');
      $query->where('i.id_historico  = '. $variableId);
      $items = Db::getInstance()->executeS($query);
 
 header("Content-type: application/force-download");
 header("Content-Disposition: attachment; filename=".basename("Archivo_Rangos_Icr.xls"));
 header("Content-Transfer-Encoding: binary");


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
 
   
    foreach ($items as $value) {
      echo "<tr>";
      if (is_array($value)){
        foreach ($value as $valor){
          echo "<td>$valor</td>";
        }
      }
      echo "</tr>";
    }
  echo "</table>";           
  exit;
?>