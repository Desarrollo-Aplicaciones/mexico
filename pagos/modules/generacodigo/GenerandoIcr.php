<?php
include_once(dirname(__FILE__)."/../../config/config.inc.php");
class GenerandoIcr extends AdminController  {

################################################
# metodo que busca campos dentro de las tablas #
################################################
  
  public static function campostabla(){
    $resultado = array();
    $query = new DbQuery();
    $query->select('MAX(cod_icr) AS ultimo');
    $query->from('icr');
    if(Db::getInstance()->numRows($query) == 0){
      $entradaFinal = 0;
    }
    else{
      $resultado = Db::getInstance()->executeS($query);
      $entradaFinal = $resultado[0]['ultimo'];
    }
    return $entradaFinal;
  }

###################################
# metodo que genera el codigo icr #
###################################
  
  public static function generandocodigo($cantidad, $ultimo){
    if (strlen($ultimo) != 6){
      $result['error'] = "error en el formato del código";
      return $result;
    }
    $arreglo = str_split($ultimo);
    $numeros = "";
    $letras = "";
    if( $ultimo == "0"){
      $cod_icr = 'AAA000';
    }else{
      for ($i = 0; $i < count($arreglo); $i++){
        if ($i < (count($arreglo)/2)){
          $letras .= $arreglo[$i];
        }
        else{
          $numeros .= $arreglo[$i];
        }
      }
      if ($numeros == "999"){
        $numeros = "000";
        $letras = GenerandoIcr::get_next_in_sequence($letras);
      }
      else{
        $numeros++;
        $numeros = sprintf('%03d',$numeros);
      }
      $cod_icr = $letras.$numeros;
    }
    $arreglo = GenerandoIcr::generandoexcel($cantidad, $cod_icr);
    if (isset($arreglo['error'])){
      return $arreglo;
    }
    else{
      GenerandoIcr::fisicoExcel($arreglo);
    }
  }

###########################################
# metodo que genera el arreglo de codigos #
###########################################

  protected static function generandoexcel($cantidad, $primer_cod){
    if (strlen($primer_cod) != 6){
      $result['error'] = "error en el formato del código";
      return $result;
    }
    $codigos = array();
    $arreglo = str_split($primer_cod);
    $numeros = "";
    $letras = "";
    for ($i = 0; $i < count($arreglo); $i++){
        if ($i < (count($arreglo)/2)){
          $letras .= $arreglo[$i];
        }
        else{
          $numeros .= $arreglo[$i];
        }
      }
      $codigos[0] = $letras.$numeros;
    for ($i = 0; $i < $cantidad; $i++){
      $codigos[$i] = $letras.$numeros;
      if ($codigos[$i] == "ZZZ999"){
        $result['error'] = "No es posible crear tantos ICR";
        return $result;
      }
      if ($numeros == "999"){
        $numeros = "000";
        $letras = GenerandoIcr::get_next_in_sequence($letras);
      }
      else{
        $numeros++;
        $numeros = sprintf('%03d',$numeros);
      }

    }
    return $codigos;
 }

##############################################
# metodo que genera el archivo digital excel #
##############################################

public static function fisicoExcel($codigos){
  $arreglo = array( "cod_final" => end($codigos),
                    "cod_inicio" => reset($codigos),
                    "id_empleado" => Context::getContext()->cookie->id_employee,
                    "fecha" => date("Y-m-d H:i:s"),
                    "cantidad" => count($codigos)
                  );
  if(!(Db::getInstance()->insert('icr_history', $arreglo))){
    $result['error'] = "Error en el acceso a la base de datos";
    return $result;
  }
  $new_icr = array("id_historico" => Db::getInstance()->Insert_ID(),
                   "id_estado_icr" => 1
                  );
  foreach ($codigos as $value) {
    $new_icr['cod_icr'] = $value;
    if(!(Db::getInstance()->insert('icr', $new_icr))){
      $result['error'] = "Error en el acceso a la base de datos";
      return $result;
    }
  }
      header('Content-type: application/ms-excel');
      header('Content-Disposition: attachment; filename=codigos_generados'.date("Y-m-d").'.xls');
      header('Content-Type: application/force-download; charset=UTF-8');
      header('Cache-Control: no-store, no-cache');
  echo implode("\n", $codigos);
  exit;
 }

#################################################
# metodo que genera el cambio de letra y numero #
#################################################

protected static function get_next_in_sequence($str) {

  $letters = range('A', 'Z');
  $arr = str_split($str);
  foreach ($arr as $key => $char) {
    $arr[$key] = array_search($char, $letters); 
  }
 $digits = count($arr)-1; 
 for ($i=$digits; $i > -1; $i--) { 
  if ($i == $digits) 
   $arr[$i]++; 
  if ($arr[$i] == 26) { 
   $arr[$i] = 0; 
   if ($i != 0) 
    $arr[$i-1]++;
  } 
}
foreach ($arr as $key => $char) { 
  $arr[$key] = $letters[$char];
}
$str = implode($arr);
return $str;
}

##################################################
# metodo para buscar el codigo ICR #
##################################################

public static function traerParametro($codigo){
  $query = new DbQuery();
  $query->select('id_icr, id_estado_icr, cod_icr');
  $query->from('icr');
  $query->where('cod_icr ="'. $codigo.'"');
  $items = Db::getInstance()->executeS($query);
  if(count($items) > 0){
    if($items[0]['id_estado_icr'] == 1){
      $result = $items[0];
    }
    else{
      $result['error'] = "El código no se encuentra activo";
    }
  }
  else{
    $result['error'] = "Código no encontrado";
  }
  return $result;
}

##################################################
# metodo para cambiar de estado en el codigo ICR #
##################################################
public static function actualizarICR($id){
  if(!Db::getInstance()->update('icr', array('id_estado_icr' => 7),"id_icr='".$id."'")){
    $result['error'] = "error en la actualización del código";
  }else{
    $result['result'] = "Código Anulado";
  }
  return $result;
}
################################################
# metodo que genera el reporte de interaciones #
################################################
  public static function reporteICR(){
    $idprofile = Context::getContext()->employee->id_profile; 
    $idempleado = Context::getContext()->cookie->id_employee;
      $query = new DbQuery();
      $query->select('h.id_icr,h.cod_inicio,h.cod_final,e.firstname,e.lastname,h.fecha,h.cantidad');
      $query->from('icr_history', 'h');
      $query->innerJoin('employee', 'e', ' e.id_employee = h.id_empleado ');
      if(!($idprofile == 1 || $idprofile == 2)){
        $query->where('h.id_empleado ='. $idempleado);
      }
      $query->orderBy('h.fecha desc');
      $items = Db::getInstance()->executeS($query);
    return $items;
  }
################################################
# metodo que genera el reporte de ICR libres #
################################################
  public static function reporteLibres(){
    $reporte = "";
    $query = new DbQuery();
    $query->select('cod_icr');
    $query->from('icr');
    $query->where('id_estado_icr = 1');
    $query->orderBy('id_icr asc');
    $items = Db::getInstance()->executeS($query);
    foreach ($items as $value) {
      $reporte .= $value['cod_icr']."\n";
    }
    header('Content-type: application/ms-excel');
    header('Content-Disposition: attachment; filename=codigos_libres'.date("Y-m-d").'.xls');
    header('Content-Type: application/force-download; charset=UTF-8');
    header('Cache-Control: no-store, no-cache');
    echo $reporte;
    exit;
  }
}
?>


