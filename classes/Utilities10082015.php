<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of utilFilesClass
 *
 * @author Ewing
 */

class UtilitiesCore extends ObjectModel {

 
  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
    'table' => 'alias',
    'primary' => 'id_alias',
  );
  
  /*
   * 
   */

    public static function get_order($id_cart) {
        try {
            $sql = 'select ord.id_order 
    from ps_orders ord INNER JOIN ps_cart car ON(ord.id_cart=car.id_cart) 
    WHERE  ord.id_cart=' .(int) $id_cart . ' Limit 1';

            if ($results = Db::getInstance()->ExecuteS($sql)) {
                foreach ($results as $row) {
                    return $row;
                }
            }
            return false;
        } catch (Exception $exc) {
            
            return false;
        }
    }

    /*
     * Insertar message Asociar orden con el empleado y cliente
     */
    
        public static function add_message($id_cart,$id_customer,$id_employee,$id_order,$message,$private,$date_add) {
        try {
            // si get_id_employee no es numerico se llama el metodo get_id_employee() 
            $id_employee = is_numeric($id_employee)? $id_employee : Utilities::get_id_employee($id_employee);
            
            $sql = "insert into ps_message ( `id_cart`,`id_customer` ,  `id_employee` ,`id_order` ,  `message` ,`private` , `date_add` ) 
                    VALUES(".(int)$id_cart.",".(int)$id_customer.",".(int)$id_employee.",".(int)$id_order.",'".$message."',".$private.", '".$date_add."');";
            

            if (Db::getInstance()->Execute($sql)) {
          return true;
            }
              } catch (Exception $exc) {
            
            return false;
        }
        return false;
    }
    
    /*
     * get_id_employee
     */
    
        public static function get_id_employee($id_employee_sugar) {
            $results=NULL;
        try {
            $sql = 'SELECT id_employee FROM
                    ps_sync_emp_user 
                    WHERE id_user="'.$id_employee_sugar.'" LIMIT 1;';

            if ($results = Db::getInstance()->ExecuteS($sql) && count($results)>0) {
                return $results[0]['id_employee'];
            }
                 } catch (Exception $exc) {
            
            return false;
        }
        return false;
    }
    
            public static function get_data_employee($id_employee_sugar) {
            $results=NULL;
        try {
            $sql = 'SELECT sync.id_employee,emp.email,emp.firstname,emp.lastname,emp.id_profile 
                    FROM ps_sync_emp_user sync INNER JOIN ps_employee emp ON(sync.id_employee = emp.id_employee)
                    WHERE sync.id_user="'.$id_employee_sugar.'" LIMIT 1;';

            if ($results = Db::getInstance()->ExecuteS($sql)) { 
                return $results[0];
            }
                 } catch (Exception $exc) {
            
            return false;
        }
        return false;
    }

    public static function available_property($property_name,$id_employee,$option)
 {
                    $results=NULL;
        try {
            $sql = "select acc.configure,acc.`view` ,prop.`name`,prop.description
                    from ps_employee emp
                    INNER JOIN ps_module_access_property acc  ON(emp.id_employee = acc.id_employee)
                    INNER JOIN ps_module_propertys  prop ON( acc.id_module_property = prop.id_module_property)
                    INNER JOIN ps_module module  ON (prop.id_module = module.id_module)
                    INNER JOIN ps_module_access modacc  ON (module.id_module = modacc.id_module)
                    INNER JOIN ps_profile prof ON (modacc.id_profile = prof.id_profile AND emp.id_profile = prof.id_profile)
                    WHERE modacc.configure = 1 AND prop.`name`='".$property_name."' AND emp.id_employee = ".$id_employee;
                    if($option === 'configure'){
                        $sql.=' AND acc.configure = 1;';
                      }
                    elseif($option === 'view'){
                       $sql.=' AND acc.`view` = 1;'; 
                    }  
            if ($results = Db::getInstance()->ExecuteS($sql) ) { 
             
                    if($option === 'configure' || $option === 'view'){
                        return TRUE;
                      }
                    else {
                        return $results[0];   
                    }
            }
                 } catch (Exception $exc) {
            
            return false;
        }
        return false;
    
 }
 
 public static function is_ssl() {
    if ( isset($_SERVER['HTTPS']) ) {
        if ( 'on' == strtolower($_SERVER['HTTPS']) )
            return true;
        if ( '1' == $_SERVER['HTTPS'] )
            return true;
    } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
        return true;
    }
    return false;
}

/**
 * Reglas de entrega nocturna que aplican en un momento determinado
 */
public static function is_rules_entrega_nocturna(){
//    throw new Exception(' || '.print_r("
//             SELECT regla.id_regla_entrga_nocturna
//             FROM
//            "._DB_PREFIX_."_regla_entrga_nocturna regla
//            WHERE (regla.".Utilities::get_day_str_es()." = 1 
//            AND '".Utilities::get_dateTime(2)."' BETWEEN regla.hora_inicio AND '23:59:59') OR
//            ('".Utilities::get_dateTime(2)."' BETWEEN '00:00:00' AND regla.hora_fin); ",TRUE).' || ');
   $result = Db::getInstance()->executeS("
             SELECT regla.id_regla_entrga_nocturna
             FROM
            "._DB_PREFIX_."_regla_entrga_nocturna regla
            WHERE (regla.".Utilities::get_day_str_es()." = 1 
            AND '".Utilities::get_dateTime(2)."' BETWEEN regla.hora_inicio AND '23:59:59') OR
            ('".Utilities::get_dateTime(2)."' BETWEEN '00:00:00' AND regla.hora_fin); ");

   if(isset($result) && count($result)>0){
       return TRUE;
   }
   
   return FALSE;
}

/**
 * Valida si la dirección actual tiene localidad y barrio asociados, retorna un arreglo con el id de la localiad y el barrio(id_localidad,id_barrio)
 * @param type $id_addres
 * @return null
 */
public static function is_localidad_barrio($id_addres){
    
    if(isset($id_addres) && is_int($id_addres)){
   $result = Db::getInstance()->executeS('
            SELECT addr.id_localidad, addr.id_barrio
             FROM   ps_address addr 
                WHERE addr.id_address = '.$id_addres.' AND!ISNULL(addr.id_colonia) AND !ISNULL(addr.id_barrio);  ');

        if(isset($result) && count($result)>0){
            return array('id_localidad'=>$result[0]['id_localidad'],'id_barrio'=>$result[0]['id_barrio']);
        }
    }
    
    return array();
}

/**
 * Si la dirección tiene barrio y localidad se valida si se debe mostrar el envio nocturno en el instante de ejecución
 * @param type (int)$id_addres
 * @param type (int)$id_barrio
 * @return boolean
 */
public static function show_select_localidad($id_localidad,$id_barrio){
    
    if(isset($id_localidad) && isset($id_barrio) && is_int($id_localidad) && is_int($id_barrio) ){
    $result = Db::getInstance()->executeS("
                 SELECT barrios.id_barrio
                 
                FROM ps_entrega_nocturna entrega INNER JOIN
                ps_regla_entrga_nocturna regla ON(entrega.id_id_regla_entrga_nocturna = regla.id_regla_entrga_nocturna)
                
                INNER JOIN ps_cities_col cities  ON (entrega.id_city = cities.id_city)
                INNER JOIN ps_localidad localidad ON(cities.id_city = localidad.id_city)
                INNER JOIN ps_barrios barrios ON(localidad.id_localidad = barrios.id_localidad)

                WHERE localidad.id_localidad = ".(int)$id_localidad." AND barrios.id_barrio = ".(int)$id_barrio." AND barrios.entrega_nocturna = 1 
                AND  ((regla.".Utilities::get_day_str_es()." = 1 
                AND '".Utilities::get_dateTime(2)."' BETWEEN regla.hora_inicio AND '23:59:59') 
                OR ('".Utilities::get_dateTime(2)."' BETWEEN '00:00:00' AND regla.hora_fin))); ");

   if(isset($result) && count($result)>0 && $result[0][id_barrio] === (int)$id_barrio){
       return TRUE;
   }
    }
    return FALSE;
}

/**
 * Retorna la lista de localidades disponibles para entrega nocturna en el momento de ejecución
 * @return type
 */
public static function get_list_localidades(){
    
        $result = Db::getInstance()->executeS("
             select localidad.id_localidad,localidad.nombre_localidad

                FROM ps_entrega_nocturna entrega INNER JOIN
                 ps_regla_entrga_nocturna regla ON(entrega.id_id_regla_entrga_nocturna = regla.id_regla_entrga_nocturna)

                    INNER JOIN ps_cities_col cities  ON (entrega.id_city = cities.id_city)
                    INNER JOIN ps_localidad localidad ON(cities.id_city = localidad.id_city)
                    INNER JOIN ps_barrios barrios ON(localidad.id_localidad = barrios.id_localidad)

                WHERE (regla.".Utilities::get_day_str_es()." = 1 
            AND '".Utilities::get_dateTime(2)."' BETWEEN regla.hora_inicio AND '23:59:59') OR
            ('".Utilities::get_dateTime(2)."' BETWEEN '00:00:00' AND regla.hora_fin)
               GROUP BY localidad.id_localidad;  ");

   if(isset($result) && count($result)>0 ){
       return $result[0];
   }
   return array();
}

/**
 * Retorna la lista de barrios disponibles en el mometo de ejecuación de una localiad.
 * @param type $id_localidad
 * @return type
 */
public static function get_list_barrios($id_localidad){
    
        $result = Db::getInstance()->executeS("
                    SELECT barrios.id_barrio,barrios.nombre_barrio

                        FROM ps_entrega_nocturna entrega INNER JOIN
                            ps_regla_entrga_nocturna regla ON(entrega.id_id_regla_entrga_nocturna = regla.id_regla_entrga_nocturna)

                        INNER JOIN ps_cities_col cities  ON (entrega.id_city = cities.id_city)
                        INNER JOIN ps_localidad localidad ON(cities.id_city = localidad.id_city)
                        INNER JOIN ps_barrios barrios ON(localidad.id_localidad = barrios.id_localidad)

                    WHERE barrios.entrega_nocturna = 1 AND ((regla.".Utilities::get_day_str_es()." = 1 
                    AND '".Utilities::get_dateTime(2)."' BETWEEN regla.hora_inicio AND '23:59:59') OR
                    ('".Utilities::get_dateTime(2)."' BETWEEN '00:00:00' AND regla.hora_fin))
                    AND localidad.id_localidad = '.(int)$id_localidad.';  ");

   if(isset($result) && count($result)>0 ){
       return $result;
   }
   return array();
}

/**
 *  Inserta la localidad y barrio a una dirección
 * @param type $id_address
 * @param type $id_localidad
 * @param type $id_barrio
 * @return boolean
 */
public static function set_localidad_barrio($id_address,$id_localidad,$id_barrio){
    
    if(isset($id_address) && isset($id_localidad) && isset($id_barrio) && is_int($id_address) && is_int($id_barrio) && is_int($id_localidad) ){
            $result = Db::getInstance()->execute('
                   update ps_address addr
                    SET addr.id_localidad ='.(int)$id_localidad.',  addr.id_barrio = '.(int)$id_barrio.'
                    WHERE addr.id_address = '.(int)$id_address.'; ');
            if(isset($result) && $result){
                return TRUE;
            }
    }
    return FALSE;
    }
    
    
public static function get_parameters(){
    
   
    $result = Db::getInstance()->executeS('
                 SELECT entrega.id_entrega_nocturna, entrega.valor,regla.hora_inicio,regla.hora_fin
                 
                    FROM
                        ps_regla_entrga_nocturna regla
                        
                    INNER JOIN ps_entrega_nocturna entrega on( regla.id_regla_entrga_nocturna = entrega.id_id_regla_entrga_nocturna)
                    
                WHERE regla.'.Utilities::get_day_str_es().' = 1 AND entrega.activa = 1;  ');

   if(isset($result) && count($result)>0){
       return $result[0];
   }
    
    return array();
}    

/**
 *  Retorna la fecha en diferentes formatos opcionalmente incrementa la fecha en un numero días.
 * Formatos de fecha 
 * 0 => Y-m-d H:i:s
 * 1 => Y-m-j
 * 2 => H:i:s
 * @param type $increment    
 * @return datetime
 */
public static function get_dateTime($format_date = 0,$increment = 0){

    $format=NULL;
    switch ($format_date) {
        case 0:

            $format='Y-m-d H:i:s';
            break;
        case 1:

             $format='Y-m-j';
            break;
        case 2:
            $format='H:i:s'; 

            break;        

        default:
            break;
    }
    
     if( $format_date <= 1 && $increment != 0){
        if(is_integer($increment)){
            return strtotime ( '+ '.$increment.' day' , strtotime (date($format) ) ) ;
        }    
    } else {
        return date($format);
    }
}

/**
 * Verifica que una fecha esté dentro del rango de fechas establecidas
 * @param $start_date fecha de inicio
 * @param $end_date fecha final
 * @param $evaluame fecha a comparar
 * @return true si esta en el rango, false si no lo está
 */
public static function check_in_range($start_date, $end_date, $evaluame) {
    $start_ts = strtotime($start_date);
    $end_ts = strtotime($end_date);
    $user_ts = strtotime($evaluame);
    return (($user_ts >= $start_ts) && ($user_ts <= $end_ts));
}

/**
 * Retorna el día de la semana en español
 * @return string|null
 */
public static function get_day_str_es(){
    
    $dia=(int) date("N");
    
    switch ($dia) {
        case 0:
            return 'domingo';

            break;
                case 1:
                   return 'lunes'; 

            break;
                case 2:
                    return 'martes';

            break;
                case 3:
                    return 'miercoles';

            break;
                case 4:
                    return 'jueves';

            break;
                case 5:
                    return 'viernes';

            break;
                case 6:
                    return 'sabado';

            break;

        default:
            return NULL;
            break;
    }
}
/**
*
Retorna el ID y el nombre del distribuidor de acuerdo al id de una orden de suministro
Requiere el id de una orden de suministro
*
**/
    public static function getSupplierById($id){
      if($id){
        $query = new DbQuery();
        $query->select('id_supplier, supplier_name');
        $query->from('supply_order');
        $query->where('id_supply_order ='.$id);
        $result = Db::getInstance()->ExecuteS($query);
        return $result[0];
      }
    }
/**
*
Retorna un Arreglo con la información para escribir el archivo de Nadro
Requiere el Id del detalle de la orden de suministros.
*
**/
    public static function armarArreglo($id, $path){
      $constante = "NNADRO";
      $codigo_cliente = "2780984";
      $propositos_generales = "000 ";
      $num_pedido = str_pad($id, 15, "0", STR_PAD_LEFT);
      $fecha = date('Ymd');
      $arreglo = array("cabecera"=>$constante.$codigo_cliente.$propositos_generales.$num_pedido.$fecha);
      $query = new DbQuery();
      $query->select('reference,quantity_expected');
      $query->from('supply_order_detail');
      $query->where('id_supply_order ='.$id);
      $result = Db::getInstance()->ExecuteS($query);
      foreach ($result as $key => $value) {
        $arreglo[$key] = str_pad($value['reference'], 14, "0", STR_PAD_LEFT).str_pad($value['quantity_expected'], 6, "0", STR_PAD_LEFT);
      }
      $string = "";
      foreach($arreglo as $key => $nombre_campo){
        $string .= preg_replace("[\n|\r|\n\r|\t]", "", $nombre_campo)."\n";
      }
      if ($ar=fopen($path,"w")){
        fputs($ar,$string);
        fclose($ar);
        return FALSE;
      }else{
        return "Error al escribir en $path";
      }
    }

/**
*
Función de conexión FTP con Nadro, realiza una acción de subir o bajar un archivo a servidor según sea necesario
cambiar acá en caso de modificar los datos de acceso FTP
Requiere Nombre de carpeta en servidor FTP, acción (PUT-GET), ruta del archivo en local, nombre final del archivo en servidor
*
**/
    public static function conectarFTPNadro($folder, $action, $path, $remote_file){
      #IMPORTANTE DATOS DE CONEXIÓN FTP ESTATICOS PARA NADRO, SE LLAMAN EN TIEMPO DE EJECUCION#
      $url = "ftp1.rednadro.com.mx";//<---------¡CUIDADO SOLO COLOCAR PARA PRODUCCIÓN
      $username = "farma_listo";
      $password = "Ost4-32re";
      #^----CAMBIAR AQUÍ-----^#
      $function = "ftp_".$action;
      $cid = ftp_connect($url);
      if ($cid){
        $resultado = ftp_login($cid, $username,$password);
        if ($resultado) {
          ftp_pasv ($cid, true) ;
          if(ftp_chdir($cid, $folder)){}else{
            $error .= "Hubo un problema durante el cambio a $folder <br>";
          }
          if ($function($cid, $remote_file, $path, FTP_ASCII)) {
          }
          else {
           $error .= "Hubo un problema durante la transferencia de $remote_file <br>";
          }
          ftp_close($cid);
        }
        else{
          $error = "Login o Password incorrectos.";  
        }
      }
      else{
        $error = "Falla en la conexión a $url";
      }
      if (isset($error))
        {
          return $error;
        }else{
          return FALSE;
        }


    }

    /**
     * [ChangeStateOrderIcr Cambia el estado actual de una orden a verificación manual]
     * @param [type] $IdOrder [id de la orden a modificar]
     */
    public static function ChangeStateOrderIcr( $IdOrder ) {

        if ( is_int( $IdOrder ) && $IdOrder != 0 ) {
            echo "<br> Good Id: ". $IdOrder;

            return Db::getInstance()->update('orders', array(
                'current_state' => (int)'19',
            ), 'id_order = '.(int)$IdOrder);
        } else {
            echo "<br> BAD Id: ". $IdOrder;
            return "Error";
        }


    }
 /**
     * 
     * Elimina todos los acentos de una cadena 
     */   
public static function sanear_string($string)
{

$string= utf8_encode($string);

$a = array('Ã€', 'Ã?', 'Ã‚', 'Ãƒ', 'Ã„', 'Ã…', 'Ã†', 'Ã‡', 'Ãˆ', 'Ã‰', 'ÃŠ', 'Ã‹', 'ÃŒ', 'Ã?', 'ÃŽ', 'Ã?', 'Ã?', 'Ã‘', 'Ã’', 'Ã“', 'Ã”', 'Ã•', 'Ã–', 'Ã˜', 'Ã™', 'Ãš', 'Ã›', 'Ãœ', 'Ã?', 'ÃŸ', 'Ã ', 'Ã¡', 'Ã¢', 'Ã£', 'Ã¤', 'Ã¥', 'Ã¦', 'Ã§', 'Ã¨', 'Ã©', 'Ãª', 'Ã«', 'Ã¬', 'Ã­', 'Ã®', 'Ã¯', 'Ã±', 'Ã²', 'Ã³', 'Ã´', 'Ãµ', 'Ã¶', 'Ã¸', 'Ã¹', 'Ãº', 'Ã»', 'Ã¼', 'Ã½', 'Ã¿', 'Ä€', 'Ä?', 'Ä‚', 'Äƒ', 'Ä„', 'Ä…', 'Ä†', 'Ä‡', 'Äˆ', 'Ä‰', 'ÄŠ', 'Ä‹', 'ÄŒ', 'Ä?', 'ÄŽ', 'Ä?', 'Ä?', 'Ä‘', 'Ä’', 'Ä“', 'Ä”', 'Ä•', 'Ä–', 'Ä—', 'Ä˜', 'Ä™', 'Äš', 'Ä›', 'Äœ', 'Ä?', 'Äž', 'ÄŸ', 'Ä ', 'Ä¡', 'Ä¢', 'Ä£', 'Ä¤', 'Ä¥', 'Ä¦', 'Ä§', 'Ä¨', 'Ä©', 'Äª', 'Ä«', 'Ä¬', 'Ä­', 'Ä®', 'Ä¯', 'Ä°', 'Ä±', 'Ä²', 'Ä³', 'Ä´', 'Äµ', 'Ä¶', 'Ä·', 'Ä¹', 'Äº', 'Ä»', 'Ä¼', 'Ä½', 'Ä¾', 'Ä¿', 'Å€', 'Å?', 'Å‚', 'Åƒ', 'Å„', 'Å…', 'Å†', 'Å‡', 'Åˆ', 'Å‰', 'ÅŒ', 'Å?', 'ÅŽ', 'Å?', 'Å?', 'Å‘', 'Å’', 'Å“', 'Å”', 'Å•', 'Å–', 'Å—', 'Å˜', 'Å™', 'Åš', 'Å›', 'Åœ', 'Å?', 'Åž', 'ÅŸ', 'Å ', 'Å¡', 'Å¢', 'Å£', 'Å¤', 'Å¥', 'Å¦', 'Å§', 'Å¨', 'Å©', 'Åª', 'Å«', 'Å¬', 'Å­', 'Å®', 'Å¯', 'Å°', 'Å±', 'Å²', 'Å³', 'Å´', 'Åµ', 'Å¶', 'Å·', 'Å¸', 'Å¹', 'Åº', 'Å»', 'Å¼', 'Å½', 'Å¾', 'Å¿', 'Æ’', 'Æ ', 'Æ¡', 'Æ¯', 'Æ°', 'Ç?', 'ÇŽ', 'Ç?', 'Ç?', 'Ç‘', 'Ç’', 'Ç“', 'Ç”', 'Ç•', 'Ç–', 'Ç—', 'Ç˜', 'Ç™', 'Çš', 'Ç›', 'Çœ', 'Çº', 'Ç»', 'Ç¼', 'Ç½', 'Ç¾', 'Ç¿', 'Î†', 'Î¬', 'Îˆ', 'Î­', 'ÎŒ', 'ÏŒ', 'Î?', 'ÏŽ', 'ÎŠ', 'Î¯', 'ÏŠ', 'Î?', 'ÎŽ', 'Ï?', 'Ï‹', 'Î°', 'Î‰', 'Î®');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Î‘', 'Î±', 'Î•', 'Îµ', 'ÎŸ', 'Î¿', 'Î©', 'Ï‰', 'Î™', 'Î¹', 'Î¹', 'Î¹', 'Î¥', 'Ï…', 'Ï…', 'Ï…', 'Î—', 'Î·');
 
  return Utilities::trim_all(str_replace($a, $b, $string));

}

/**
 * Elimina los caracteres especiales 
 */
public static function trim_all( $str , $what = NULL , $with = ' ' )
{
    if( $what === NULL )
    {
        //  Character      Decimal      Use
        //  "\0"            0           Null Character
        //  "\t"            9           Tab
        //  "\n"           10           New line
        //  "\x0B"         11           Vertical Tab
        //  "\r"           13           New Line in Mac
        //  " "            32           Space
       
        $what   = "\\x00-\\x20";    //all white-spaces and control chars
    }
   
    return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
}

public static function is_formula($cart,$context)
{
//Optener lista de productos del carrito    
 $pruducts = $cart->getProducts();
  // recorrer cada producto y validar si requiere formula medica    
  foreach ($pruducts as &$valor) {
     // crear un nuevo producto 
    $product = new Product($valor['id_product'], true, $context->language->id, $context->shop->id);
    // obtener las caracteristicas del producto
    $features = $product->getFrontFeatures($context->language->id);
    foreach($features as $value)
    {
    if($value['name'] === 'Requiere fórmula médica' && isset($value['value']))
      {
       
      if(strtoupper($value['value']) === 'SI') 
      {
         
      return 'SI';
      }
            
      }
    }
 } 
return 'NO';
}

public static function getImagenesFormula($id_order){
  $sql = "SELECT formula.id_formula_medica as id,formula.nombre_archivo_original as imagen,formula.nombre_archivo as fuente
          FROM "._DB_PREFIX_."orders orden 
          INNER JOIN "._DB_PREFIX_."cart carro ON(orden.id_cart = carro.id_cart)
          INNER JOIN "._DB_PREFIX_."formula_medica formula ON (carro.id_cart = formula.id_cart_fk)
          WHERE orden.id_order =  ".(int) $id_order." AND !ISNULL(formula.nombre_archivo) AND formula.nombre_archivo != 'NO' AND formula.nombre_archivo !='';";
  $result = Db::getInstance()->executeS($sql);
  if(isset($result) && !empty($result) && count($result)>0){
  return $result;
   }
  return array();
}

}

