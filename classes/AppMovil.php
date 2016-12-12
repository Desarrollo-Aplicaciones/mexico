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
 //extends ObjectModel
class AppMovilCore extends ObjectModel {
    
   private  $nuevo_archivo;
 
  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
    'table' => 'proveedores_costo',
    'primary' => 'id_icr',
    'fields' => array(
      'id_product'  => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
      'id_supplier' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
      'id_estado'   => array('type' => self::TYPE_INT, 'required' => true),
      'price'       => array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'required' => true),
      'flag'        => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
      'date'        => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'copy_post' => false),
    ),
  );


    // Listado de errores en el cargue
    public $errores_cargue = array();    


    /**
     * ValidateFechaPrecioActua    Validación del ingreso de fecha de modificacion de precio de producto
    */
   
    public function ValidateFechaPrecioActua() {


        /*********    VALIDAR TAMAÑO DE LA FECHA **************/

        $query = "SELECT COUNT(*) AS tamanofec, 
                GROUP_CONCAT( DISTINCT CONCAT( id_producto,':',id_proveedor )  ORDER BY id_producto  SEPARATOR ' | ' ) AS prods_prov 
            FROM tmp_precios_proveed_app
            WHERE CHAR_LENGTH(fecha) != 10 AND TRIM(fecha) != ''";

        if ($retorno = DB::getInstance()->executeS($query) ) {

            if( isset($retorno[0]['tamanofec']) && $retorno[0]['tamanofec'] == 0 ) {

                /*********    VALIDAR FORMATO CON EXPRESION REGULAR **************/

                $query_regexfec = "SELECT COUNT(*) AS regexfec,  GROUP_CONCAT( DISTINCT CONCAT( id_producto,':',id_proveedor )  ORDER BY id_producto  SEPARATOR ' | ' ) AS prods_prov 
                        /*REGEXP '^[0-9]{4}-([0]{1}[1-9]{1}|[1]{1}[0-2]{1})-([0]{1}[1-9]{1}|[1-3]{1}[0-9]{1})$' AS valido*/
                    FROM tmp_precios_proveed_app
                    WHERE fecha 
                        REGEXP '^[0-9]{4}-([0]{1}[0-9]{1}|[1]{1}[0-2]{1})-([0]{1}[0-9]{1}|[1-3]{1}[0-9]{1})$' = 0
                    AND TRIM(fecha) != '' ";

                if ($retorno_regexfec = DB::getInstance()->executeS($query_regexfec) ) {

                    if( isset($retorno_regexfec[0]['regexfec']) && $retorno_regexfec[0]['regexfec'] == 0 ) {

                        /*********    VALIDAR QUE LA FECHA SEA VÁLIDA **************/

                        $query_diainvalid = "SELECT 
                            COUNT( IF( day( fecha ) IS NULL , 99, day( fecha ) ) ) AS diainvalid,
                            GROUP_CONCAT( DISTINCT CONCAT( id_producto,':',id_proveedor )  ORDER BY id_producto  SEPARATOR ' | ' ) AS prods_prov  
                            FROM tmp_precios_proveed_app
                            WHERE day( fecha ) IS NULL AND TRIM(fecha) != '' ";

                        if ($retorno_diainvalid = DB::getInstance()->executeS($query_diainvalid) ) {

                            if( isset($retorno_diainvalid[0]['diainvalid']) && $retorno_diainvalid[0]['diainvalid'] == 0 ) {

                                /*********    VALIDAR QUE LA FECHA SEA MAYOR A ACTUAL **************/

                                $query_fecinvalid = "SELECT COUNT(fecha) AS fecinvalid, 
                                    GROUP_CONCAT( DISTINCT CONCAT( id_producto,':',id_proveedor )  ORDER BY id_producto  SEPARATOR ' | ' ) AS prods_prov
                                    FROM tmp_precios_proveed_app
                                    WHERE IF(STR_TO_DATE(fecha,'%Y-%m-%d') < NOW(), 0 , 1) = 1 AND TRIM(fecha) != ''
                                    AND fecha != '0000-00-00'; ";

                                if ($retorno_fecinvalid = DB::getInstance()->executeS($query_fecinvalid) ) {

                                    if( isset($retorno_fecinvalid[0]['fecinvalid']) && $retorno_fecinvalid[0]['fecinvalid'] == 0 ) {
                                        return true;

                                    } else {

                                        $this->errores_cargue[] = "Algunas fechas de algunos Productos/Proveedores son mayores a la actual. Cantidad de fechas erróneas ( ". $retorno_fecinvalid[0]['fecinvalid']." ). Producto:Proveedor: ".$retorno_fecinvalid[0]['prods_prov'];

                                        return false;
                                    }
                                    
                                } else {
                                    $this->errores_cargue[] = "No se puede comprobar que fecha de vencimiento de los ICR sea mayor a la actual.";
                                    return false;
                                }


                            } else {

                                $this->errores_cargue[] = "Algunas fechas de productos/proveedores son erroneas. Cantidad de fechas erróneas ( ". $retorno_diainvalid[0]['diainvalid']." ). Producto:Proveedor -> ".$retorno_diainvalid[0]['prods_prov'];

                                return false;
                            }
                            
                        } else {
                            $this->errores_cargue[] = "No se puede comprobar la validez de la fecha de vencimiento de los ICR.";
                            return false;
                        }

                    } else {

                        $this->errores_cargue[] = "El formato de fecha de algunos productos/proveedores es incorrecto, debe ser YYYY-MM-DD. Cantidad de fechas erróneas ( ". $retorno_regexfec[0]['regexfec']." ). Proveedor/Producto: ".$retorno_regexfec[0]['prods_prov'];
                        return false;
                    }

                } else {
                    $this->errores_cargue[] = "No se puede validar formato de fecha, de la fecha de vencimiento de los ICR.";
                    return false;
                }

            } else {

                $this->errores_cargue[] = "Se ha presentado error en el tamaño de fecha de vencimiento de algunos productos/proveedores (10 caracteres). Cantidad de fechas erróneas ( ". $retorno[0]['tamanofec']." ). Producto:Proveedor -> ".$retorno[0]['prods_prov'];
                return false;
            }

        } else {

            $this->errores_cargue[] = "No se puede validar el tamaño de la fecha de vencimiento de los ICR.";
            return false;
        }
        
    }

    /**
     *  [TruncateProdsProvNew Trunca la tabla de proveedores_costo]
     *  @return [bool] [dependiendo del resultado/ejecucion del query]
     */
    public function TruncateProdsProvNew() {
                    
            $query_insert = "TRUNCATE TABLE ps_proveedores_costo;";
                    
            if ( $results = Db::getInstance()->Execute( $query_insert) ) {
                return true;
            } else {
                $this->errores_cargue[] = "No se pudo truncar la tabla productos/proveedores.";
                return false;
            }

    }
    
    
    /**
     *  [InsertProdsProvNew Inserta los productos proveedores nuevos en la tabla  de proveedores_costo]
     *  @return [bool] [dependiendo del resultado/ejecucion del query]
     */
    public function InsertProdsProvNew() {

            $query_insert = "INSERT INTO ps_proveedores_costo ( id_product, id_supplier, price, date, flag )
                SELECT tpa.`id_producto`, 
                tpa.id_proveedor, tpa.PVP, STR_TO_DATE( tpa.fecha , '%Y-%m-%d' ) , 1 
                FROM tmp_precios_proveed_app tpa
                LEFT JOIN ps_proveedores_costo ppc ON ( tpa.`id_producto` = ppc.id_product AND tpa.id_proveedor = ppc.id_supplier )
                WHERE ppc.id_product is NULL;";
            //error_log("\n\n Este es el query: ".$query_insert,3,"/tmp/errorcito.log");

            if ( $results = Db::getInstance()->Execute( $query_insert) ) {

                return true;

            } else {
                $this->errores_cargue[] = "No se pudieron ingresar los productos/proveedores nuevos.";
                return false;
            }

    }



    /**
     * [UpdateProdsProvOld Cambia los valores de las fechas de vencimiento cargadas cuando son vacias o nulas]
     * @return [bool] [dependiendo del resultado/ejecucion del query]
     */
    public function UpdateProdsProvOld() {
      //..-echo "<br>2.5";

        $query_prod_prov_old = "UPDATE tmp_precios_proveed_app tpa
            INNER JOIN ps_proveedores_costo ppc ON ( tpa.`id_producto` = ppc.id_product AND tpa.id_proveedor = ppc.id_supplier )
            SET ppc.price = tpa.PVP,
            ppc.date =  STR_TO_DATE( tpa.Fecha , '%Y-%m-%d' ),
            ppc.flag = 2";

        if ($results_prodprov = Db::getInstance()->Execute($query_prod_prov_old)) {
            return true;
        } else {
            $this->errores_cargue[] = "Error en la actualización de los productos/proveedores.";
            return false;    
        }
    }



 /*
  * carga un archivo csv a la tabla ps_tmp_cargue_entrada_icr
  * @path_file_load_db ruta del archivo csv
  */ 
 public function loadprodprovapp($path_file_load_db) {

        $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        
        $url_post = explode(':', _DB_SERVER_);


        if ( count($url_post) > 1 ) {

          mysqli_real_connect($mysqli_1, $url_post[0], _DB_USER_, _DB_PASSWD_, _DB_NAME_, $url_post[1]);

        } else {

          mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        }
        

        if (mysqli_connect_errno()) {

            $this->errores_cargue[] = "Conexión fallida: %s\n mensaje error: " . mysqli_connect_error();
            return false;
            
        }

        if (!mysqli_query($mysqli_1, "TRUNCATE TABLE tmp_precios_proveed_app")) {
            $this->errores_cargue[] = "Error al truncar la tabla (tmp_precios_proveed_app). Mensaje error: " .  mysqli_error($mysqli_1);
            return false;
        }

        $cargadat = "LOAD DATA LOCAL INFILE '" . $path_file_load_db . "'
        INTO TABLE tmp_precios_proveed_app
        FIELDS TERMINATED BY ','
        OPTIONALLY ENCLOSED BY '\"' 
        LINES TERMINATED BY '\\n'
        IGNORE 1 LINES 
        (id_producto, pvp, id_proveedor, fecha)";

        //error_log("\n\n\n\n query 1111: ".$cargadat,3,"/tmp/errorcito.log");

        if (!mysqli_query($mysqli_1, $cargadat)) {
            $this->errores_cargue[] = "Error al subir el archivo (estructura no valida). Mensaje error: " . mysqli_error($mysqli_1);
          
           return false;
        }  else {
            return true;
            
        }
        return false;
    }

}
