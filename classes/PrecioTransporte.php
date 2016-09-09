<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of utilFilesClass
 *
 * @author German
 */
 //extends ObjectModel
class PrecioTransporteCore extends ObjectModel {
    
   private  $nuevo_archivo;
 
  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
    'table' => 'precio_tr_codpos',
    'primary' => 'codigo_postal',
    'fields' => array(
      'id_carrier' => array('type' => self::TYPE_INT, 'required' => true,),
      'precio' => array('type' => self::TYPE_INT, 'required' => true),
    ),
  );

    // para registrar el objeto empleado
    private $empledado;

    
    // Listado de errores en el cargue
    public $errores_cargue = array();


    // error en la carga con datos no encontrados
    public $cant_error_carga = 0;

     // cantidad datos cargados
    public $cant_cargados = 0;


    public $resultados = array();


    public function validarCodigop() {

        /* si existe el transportador y el codigo postal */
        $sql_val1 = "UPDATE `"._DB_PREFIX_."tmp_precios_transportador` pt 
        INNER JOIN  
          (SELECT codp.codigo_postal FROM `"._DB_PREFIX_."cod_postal` codp 
            INNER JOIN `"._DB_PREFIX_."cities_col` ciu 
              ON ( ciu.id_city = codp.id_ciudad AND ciu.id_country = ".(int)Configuration::get('PS_COUNTRY_DEFAULT').") 
            GROUP BY codp.codigo_postal
          ) cp 
        ON ( pt.cod_postal = cp.codigo_postal )
        INNER JOIN `"._DB_PREFIX_."carrier` car ON 
          (car.id_reference = pt.id_transportador AND car.deleted = 0 AND car.active=1) 
        SET pt.flag = 'i' ";

        if ($resultado1 = Db::getInstance()->executeS($sql_val1) ) {


            /* si el transportador y codigo postal ya se encuentran registrados */
            $sql_val2 = "UPDATE `"._DB_PREFIX_."tmp_precios_transportador` pt 
            INNER JOIN `"._DB_PREFIX_."precio_tr_codpos` ptc ON 
                (pt.cod_postal = ptc.codigo_postal AND pt.id_transportador = ptc.id_carrier) 
                SET pt.flag = 'u' ";

            if ( $resultado2 = Db::getInstance()->executeS($sql_val2) ) {

                             /* si el precio de envio no existe o es inferior a 0 */
                $sql_val3 = "UPDATE `"._DB_PREFIX_."tmp_precios_transportador` pt 
                  SET pt.flag = 'n' 
                  WHERE pt.precio < 0 OR 
                      pt.precio IS NULL ";

                if ( $resultado3 = Db::getInstance()->executeS($sql_val3) ) {
                    return true;
                } else {
                    $this->errores_cargue[] = " No fue posible actualizar tabla temporal, validación valores negativos. ";
                    return false;
                }

            } else {
                $this->errores_cargue[] = " No fue posible actualizar tabla temporal, validando transportadores y códigos postales registrados. ";
                return false;
            }
        } else {
            $this->errores_cargue[] = " No fue posible actualizar tabla temporal, validando transportadores y códigos postales por separado. ";
            return false;
        }   

    }
  

    public function actualizarCodigop() {

        $sql_up_cod = "UPDATE `"._DB_PREFIX_."precio_tr_codpos` ptc 
        INNER JOIN `"._DB_PREFIX_."tmp_precios_transportador` pt 
        ON ( pt.flag = 'u' AND pt.cod_postal = ptc.codigo_postal AND pt.id_transportador = ptc.id_carrier )    
        SET ptc.precio = pt.precio ";

        if ( $resultado_sql_up_cod = Db::getInstance()->executeS($sql_up_cod) ) {
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible actualizar los precios de transporte. ";
            return false;
        }

    }


    public function insertarCodigop() {

        $sql_ins_cod = "INSERT INTO `"._DB_PREFIX_."precio_tr_codpos` (codigo_postal, id_carrier, precio)
        SELECT cod_postal, id_transportador, precio FROM `"._DB_PREFIX_."tmp_precios_transportador` 
        WHERE flag = 'i'";

        if ( $resultado_sql_ins_cod = Db::getInstance()->executeS($sql_ins_cod) ) {
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible insertar nuevos precios de transporte. ";
            return false;
        }
    }


    public function reporteCodigopMaloCount() {

        $sql_sel_bad = "SELECT a.errores, b.cantidad FROM 
            ( SELECT COUNT(*) AS errores FROM `"._DB_PREFIX_."tmp_precios_transportador` WHERE flag = 'n') AS a,
            ( SELECT COUNT(*) AS cantidad FROM `"._DB_PREFIX_."tmp_precios_transportador` ) AS b";

        if ( $resultado_sql_sel_bad = Db::getInstance()->executeS($sql_sel_bad) ) { 
            $this->cant_error_carga = $resultado_sql_sel_bad[0]['errores'];
            $this->cant_cargados = $resultado_sql_sel_bad[0]['cantidad'];
                    
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible obtener los registros mal cargados. ";
            return false;
        }
    }


    public function reporteCodigopMalo() {

        $sql_sel_bad = "SELECT tpt.cod_postal, tpt.id_transportador, tpt.precio,
            cp.codigo_postal, car.id_carrier, car.name AS namecarrier  FROM `"._DB_PREFIX_."tmp_precios_transportador` tpt 
            LEFT JOIN `"._DB_PREFIX_."cod_postal` cp ON ( tpt.cod_postal = cp.codigo_postal )
            LEFT JOIN `"._DB_PREFIX_."carrier` car ON (car.id_reference = tpt.id_transportador AND car.deleted = 0 AND car.active=1)
            WHERE tpt.flag = 'n'
            GROUP BY tpt.cod_postal, tpt.id_transportador, tpt.precio,
            cp.codigo_postal, car.id_carrier, car.name ";

        if ( $resultado_sql_sel_bad = Db::getInstance()->executeS($sql_sel_bad) ) { 
            $this->resultados = $resultado_sql_sel_bad;            
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible obtener los registros mal cargados. ";
            return false;
        }
    }
    

    
 /*
  * carga un archivo csv a la tabla ps_tmp_cargue_icr_salida
  * @path_file_load_db ruta del archivo csv
  */ 
    public function loaduptranscp($path_file_load_db) {


        $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        if (mysqli_connect_errno()) {

            $this->errores_cargue[] = "Conexión fallida: %s\n mensaje error: " . mysqli_connect_error();
            return false;
        }

        if (!mysqli_query($mysqli_1, "TRUNCATE TABLE ". _DB_PREFIX_ ."tmp_precios_transportador")) {
            $this->errores_cargue[] = "Error al truncar la tabla (". _DB_PREFIX_ ."tmp_precios_transportador). Mensaje error: " . mysqli_error($mysqli_1);
            return false;
        }

        $cargadat = "LOAD DATA LOCAL INFILE '" . $path_file_load_db . "'
        INTO TABLE ". _DB_PREFIX_ ."tmp_precios_transportador
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"' 
        LINES TERMINATED BY '\\r\\n'
        IGNORE 1 LINES 
        (cod_postal, id_transportador, precio )";

        if (!mysqli_query($mysqli_1, $cargadat)) {
            $this->errores_cargue[] = "Error al subir el archivo (estructura no valida). Mensaje error: " . mysqli_error($mysqli_1);
          
           return false;
        }  else {
            return true;
            
        }
        return false;
    }
/*
 * load reglas para medios de pago por ciudades
 */

    public function load_ciudades_mediosp($path_file_load_db){
        
        
        $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        if (mysqli_connect_errno()) {

            $this->errores_cargue[] = "Conexión fallida: %s\n mensaje error: " . mysqli_connect_error();
            return false;
        }

        if (!mysqli_query($mysqli_1, "TRUNCATE TABLE ". _DB_PREFIX_ ."tmp_ciudades_mediosp")) {
            $this->errores_cargue[] = "Error al truncar la tabla (". _DB_PREFIX_ ."ps_tmp_ciudades_mediosp). Mensaje error: " . mysqli_error($mysqli_1);
            return false;
        }

        $cargadat = "LOAD DATA LOCAL INFILE '" . $path_file_load_db . "'
        INTO TABLE ". _DB_PREFIX_ ."tmp_ciudades_mediosp
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"' 
        LINES TERMINATED BY '\\r\\n'
        IGNORE 1 LINES 
        (id_ciudad, id_mediosp, opcion )";

        if (!mysqli_query($mysqli_1, $cargadat)) {
            $this->errores_cargue[] = "Error al subir el archivo (estructura no valida). Mensaje error: " . mysqli_error($mysqli_1);
          
           return false;
        }  else {
            return true;
            
        }
        return false;
        
    }
    
public function validar_ciudades_mediosp(){
    
$query_reg_duplicado = "select COUNT(id_ciudad),  id_ciudad, id_mediosp
from ps_tmp_ciudades_mediosp
GROUP BY id_ciudad, id_mediosp
HAVING (COUNT(id_ciudad)) > 1; ";


        if ($results_reg = Db::getInstance()->ExecuteS($query_reg_duplicado)) {
            $this->errores_cargue[] = "Existen errores en cargue, ciudades mediosp.";
          
            return false;
        } else {
    
        return true;    
        }
    
    
}
/*
 * Retorna array de elementos duplicados
 */
public function get_duplicados() {

        $query_reg_duplicado = "select COUNT(id_ciudad) as id_ciudad,  id_ciudad, id_mediosp
from ps_tmp_ciudades_mediosp
GROUP BY id_ciudad, id_mediosp
HAVING (COUNT(id_ciudad)) > 1; ";


        if ($results_reg = Db::getInstance()->ExecuteS($query_reg_duplicado)) {
            return $results_reg;
        } else {
            return false;
        }
    }
    
    /*
     * Actualiza la tabla "rules_mediosp_ciudades", elimina o adiciona ciudades con un medio de pago
     */

    public function update_ciudades_mediosp()
{
 if($this->validar_ciudades_mediosp()){
     $this->delete_ciudades_mediosp();  
     $this->insert_ciudades_mediosp();
   
 } else{
     
 }   
    
}

/*
 * Iinserta relaciones entre medios de pago y ciudades
 */
public function  insert_ciudades_mediosp(){
    
$deletequery="INSERT INTO ps_rules_mediosp_ciudades (id_ciudad, id_medio_de_pago )
SELECT tem_mediosp.id_ciudad, tem_mediosp.id_mediosp as id_medio_de_pago
from 
ps_tmp_ciudades_mediosp tem_mediosp 
INNER JOIN ps_medios_de_pago mediosp ON (mediosp.id_medio_de_pago = tem_mediosp.id_mediosp) 
LEFT JOIN  ps_rules_mediosp_ciudades rules on ( rules.id_ciudad =  tem_mediosp.id_ciudad AND rules.id_medio_de_pago = tem_mediosp.id_mediosp)
WHERE ISNULL(rules.id_ciudad) AND ISNULL(rules.id_medio_de_pago) AND tem_mediosp.opcion='a';";
 
 if ($results_reg = Db::getInstance()->Execute($deletequery)) {
          

            return true;
        } else {

        return false;    
        }
    
}

/*
 * Elimina Relaciones entre ciudades y medios de pago
 */
public function delete_ciudades_mediosp(){
 $deletequery="DELETE reglas from
ps_rules_mediosp_ciudades reglas 
INNER JOIN ps_tmp_ciudades_mediosp temp
ON (reglas.id_ciudad=temp.id_ciudad AND reglas.id_medio_de_pago= temp.id_mediosp)
where temp.opcion='d';";
 
 if ($results_reg = Db::getInstance()->Execute($deletequery)) {

            return true;
        } else {

        return false;    
        }
    
}


/****************************** TRANSPORTE DE CIUDADES  *************************************/


    /**
     * [validarCiudadTransporteDuplicado description]
     * @return [type] [verdadero si no hay duplicados]
     */
    public function validarCiudadTransporteDuplicado() {
      //..echo "<br>1";

        $query_reg_duplicado = "SELECT COUNT(id_ciudad), id_ciudad, id_transportador from "._DB_PREFIX_."tmp_precios_transportador
            GROUP BY id_ciudad, id_transportador
            HAVING (COUNT(id_ciudad)) >1
            ORDER BY id_ciudad";


        if ($results_reg = Db::getInstance()->ExecuteS($query_reg_duplicado)) {
            $this->errores_cargue[] = "Existen errores en cargue, hay Ciudades con transportador duplicado.";
            return false;
        } else {
        return true;    
        }
    }

    public function validarCiudad() {

        /* si existe el transportador y el codigo postal */
        $sql_val1 = "UPDATE `"._DB_PREFIX_."tmp_precios_transportador` pt 
            INNER JOIN `ps_cities_col` ciu ON ( pt.id_ciudad = ciu.id_city AND ciu.id_country = ".(int)Configuration::get('PS_COUNTRY_DEFAULT')." )
            INNER JOIN `ps_carrier` car ON 
            (car.id_reference = pt.id_transportador AND car.deleted = 0 AND car.active=1)
            SET pt.flag = 'i' ";

        if ($resultado1 = Db::getInstance()->executeS($sql_val1) ) {


            /* si el transportador y codigo postal ya se encuentran registrados */
            $sql_val2 = "UPDATE `"._DB_PREFIX_."tmp_precios_transportador` pt 
                INNER JOIN `"._DB_PREFIX_."carrier_city` cc ON 
                (pt.id_ciudad = cc.id_city_des AND pt.id_transportador = cc.id_carrier) 
                SET pt.flag = 'u' ";

            if ( $resultado2 = Db::getInstance()->executeS($sql_val2) ) {

                             /* si el precio de envio no existe o es inferior a 0 */
                $sql_val3 = "UPDATE `"._DB_PREFIX_."tmp_precios_transportador` pt 
                  SET pt.flag = 'n' 
                  WHERE pt.precio < 0 OR 
                      pt.precio IS NULL ";

                if ( $resultado3 = Db::getInstance()->executeS($sql_val3) ) {
                    return true;
                } else {
                    $this->errores_cargue[] = " No fue posible actualizar tabla temporal, validación valores negativos para ciudades. ";
                    return false;
                }

            } else {
                $this->errores_cargue[] = " No fue posible actualizar tabla temporal, validando transportadores y ciudades registradas. ";
                return false;
            }
        } else {
            $this->errores_cargue[] = " No fue posible actualizar tabla temporal, validando transportadores y ciudades por separado. ";
            return false;
        }   

    }

    public function actualizarCiudad() {

        $sql_up_cod = "UPDATE `"._DB_PREFIX_."carrier_city` cc 
        INNER JOIN `"._DB_PREFIX_."tmp_precios_transportador` pt 
        ON ( pt.flag = 'u' AND pt.id_ciudad = cc.id_city_des AND pt.id_transportador = cc.id_carrier )    
        SET cc.precio_kilo = pt.precio ";

        if ( $resultado_sql_up_cod = Db::getInstance()->executeS($sql_up_cod) ) {
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible actualizar los precios de transporte para las ciudades. ";
            return false;
        }

    }

    public function insertarCiudad() {

        $sql_ins_cod = "INSERT INTO `"._DB_PREFIX_."carrier_city` (id_carrier, id_city_ori, id_city_des, precio_kilo, precio_k_add, nombre_city)
        SELECT tpt.id_transportador, 3390, tpt.id_ciudad, tpt.precio, 0, UPPER(cc.city_name) FROM `"._DB_PREFIX_."tmp_precios_transportador` tpt 
        INNER JOIN `"._DB_PREFIX_."cities_col` cc ON ( tpt.id_ciudad = cc.id_city )
        WHERE tpt.flag = 'i'";

        if ( $resultado_sql_ins_cod = Db::getInstance()->executeS($sql_ins_cod) ) {
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible insertar nuevos precios de transporte para las ciudades. ";
            return false;
        }
    }


    public function reporteCiudadMaloCount() {

        $sql_sel_bad = "SELECT a.errores, b.cantidad FROM 
            ( SELECT COUNT(*) AS errores FROM `"._DB_PREFIX_."tmp_precios_transportador` WHERE flag = 'n') AS a,
            ( SELECT COUNT(*) AS cantidad FROM `"._DB_PREFIX_."tmp_precios_transportador` ) AS b";

        if ( $resultado_sql_sel_bad = Db::getInstance()->executeS($sql_sel_bad) ) { 
            $this->cant_error_carga = $resultado_sql_sel_bad[0]['errores'];
            $this->cant_cargados = $resultado_sql_sel_bad[0]['cantidad'];
                    
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible obtener los registros mal cargados para las ciudades. ";
            return false;
        }
    }


    public function reporteCiudadMalo() {

        $sql_sel_bad = "SELECT tpt.id_ciudad, tpt.id_transportador, tpt.precio,
            cc.id_city, car.id_carrier, car.name AS namecarrier  FROM `"._DB_PREFIX_."tmp_precios_transportador` tpt 
            LEFT JOIN `"._DB_PREFIX_."cities_col` cc ON ( tpt.id_ciudad = cc.id_city )
            LEFT JOIN `"._DB_PREFIX_."carrier` car ON (car.id_reference = tpt.id_transportador AND car.deleted = 0 AND car.active=1)
            WHERE tpt.flag = 'n'
            GROUP BY tpt.id_ciudad, tpt.id_transportador, tpt.precio,
            cc.id_city, car.id_carrier, car.name ";

        if ( $resultado_sql_sel_bad = Db::getInstance()->executeS($sql_sel_bad) ) { 
            $this->resultados = $resultado_sql_sel_bad;            
            return true;
        } else {
            $this->errores_cargue[] = " No fue posible obtener los registros mal cargados para las ciudades. ";
            return false;
        }
    }
 /*
  * carga un archivo csv a la tabla ps_tmp_cargue_icr_salida
  * @path_file_load_db ruta del archivo csv
  */ 
    public function loaduptransciudad($path_file_load_db) {


        $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        if (mysqli_connect_errno()) {

            $this->errores_cargue[] = "Conexión fallida: %s\n mensaje error: " . mysqli_connect_error();
            return false;
        }

        if (!mysqli_query($mysqli_1, "TRUNCATE TABLE ". _DB_PREFIX_ ."tmp_precios_transportador")) {
            $this->errores_cargue[] = "Error al truncar la tabla (". _DB_PREFIX_ ."tmp_precios_transportador). Mensaje error: " . mysqli_error($mysqli_1);
            return false;
        }

        $cargadat = "LOAD DATA LOCAL INFILE '" . $path_file_load_db . "'
        INTO TABLE ". _DB_PREFIX_ ."tmp_precios_transportador
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"' 
        LINES TERMINATED BY '\\r\\n'
        IGNORE 1 LINES 
        (id_ciudad, id_transportador, precio )";

        if (!mysqli_query($mysqli_1, $cargadat)) {
            $this->errores_cargue[] = "Error al subir el archivo (estructura no valida). Mensaje error: " . mysqli_error($mysqli_1);
          
           return false;
        }  else {
            return true;
            
        }
        return false;
    }


}
