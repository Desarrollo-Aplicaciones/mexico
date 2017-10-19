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
class seveFileClass {
    
    public $nuevo_archivo;
    // funciones para la gestion de subida de archivos 
        
   // función para guardar documentos
public function saveFile($arrayDoc,$documento,$dataUser)
{  
  
  // Sustituir especios por guion
  $archivo_usuario = str_replace(' ','-',$arrayDoc[$documento]['name']); 

  $tipo_archivo = $arrayDoc[$documento]['type']; 
  $tamano_archivo = $arrayDoc[$documento]['size'];
  $extencion = strrchr($arrayDoc[$documento]['name'],'.');

  // Rutina que asegura que no se sobre-escriban documentos
  $this->nuevo_archivo;
  $flag = true;
  while ($flag)
  {
    $this->nuevo_archivo=$this->randString().$dataUser.$extencion;//.$extencion;
    if (!file_exists($this->pathFiles().$this->nuevo_archivo)) {
      $flag= false;
    }
  }
  //compruebo si las características del archivo son las que deseo 
  try {

     if (move_uploaded_file($arrayDoc[$documento]['tmp_name'],$this->pathFiles().$this->nuevo_archivo))
     { 
      chmod($this->pathFiles().$this->nuevo_archivo, 0755);
     
       //return $this->nuevo_archivo;
       return $vector = array ( $this->nuevo_archivo, $archivo_usuario );
     } else { 
      
       // return 'NO';
       return $vector = array (false, false );
     } 
  }
  catch(Exception $e)
  {
  echo 'Error en la Función sefeFile --> lib.php ', $e->getMessage(), "\n";
  exit;
  }
}
// función que genera una cadena aleatoria
public function randString ($length = 4)
{  
  $string = "";
  $possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
  $i = 0;
    while ($i < $length)
    {    
      $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
      $string .= $char;    
      $i++;  
    }  
  return date("dmY_Hi_").$string;
}

public function exist_file($name_file)
{
  if (file_exists(pathFiles().$name_file)) {
    return true; 
  } else {
    return false; 
  }
}

// Retorna la ruta donde se encuentran los archivos de los usuarios
public function pathFiles()
{
  // Definir directorio donde almacenar los archivos, debe terminar en "/" 
    $directorio=Configuration::get('PATH_UPLOAD')."updateprice/";

    try { 
    $path="".$directorio; 

    if (!file_exists($path)) {
    mkdir($path, 0755);
    }
    return $path;
    } catch (Exception $e) {
     echo $e;
    return false;
   }
}

  public function updatePrice($upFile)
  {
  //echo "<br />Actualizando precios<pre>".
  /*
  
   -- eliminar datos tabla temporal
     -- TRUNCATE TABLE ps_temp_product; 
        
     CREATE TEMPORARY TABLE IF NOT EXISTS `ps_temp_product` (
    `reference` varchar(32) DEFAULT NULL,
    `price` decimal(20,0) DEFAULT NULL,
    `id_supplier` int(10) DEFAULT NULL,
    `precio_venta` int(11) NOT NULL,
    `id_impuesto` int(11) NOT NULL DEFAULT '0',
    `id_product` int(10) DEFAULT NULL,
    `flag` enum('n','i','u') DEFAULT 'n' COMMENT 'u: update, i: insert, n: no action'
  ) ENGINE=MEMORY DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1;


   */
  
  //$mysqli_1 = mysqli_connect(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
/*
$mysqli_1 = mysqli_init();
mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
mysqli_real_connect($mysqli_1,_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
*/
        $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        
        $url_post = explode(':', _DB_SERVER_);


        if ( count($url_post) > 1 ) {

          mysqli_real_connect($mysqli_1, $url_post[0], _DB_USER_, _DB_PASSWD_, _DB_NAME_, $url_post[1]);

        } else {

          mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        }

      if (mysqli_connect_errno()) {
          printf("Conexión fallida: %s\n", mysqli_connect_error());
          return false;
          exit();
      }

  $query_1 = mysqli_query($mysqli_1, "TRUNCATE TABLE ps_temp_product");

  $query_2 = mysqli_query($mysqli_1, "LOAD DATA LOCAL INFILE '".$upFile."'
      INTO TABLE ps_temp_product
      FIELDS TERMINATED BY ';'
      OPTIONALLY ENCLOSED BY '\"' 
      LINES TERMINATED BY '\\r\\n'
      IGNORE 1 LINES 
      (reference, id_supplier, product_supplier_reference, price, precio_venta, id_impuesto)");

  $query_3 = mysqli_query($mysqli_1, "update ps_temp_product ptp INNER JOIN ps_product pp ON (pp.reference=ptp.reference)
      SET ptp.id_product=pp.id_product, ptp.flag='u'");

  $query_4 = mysqli_query($mysqli_1, "UPDATE ps_temp_product ptp 
      LEFT JOIN ps_product_supplier pps ON (ptp.id_product=pps.id_product AND ptp.id_supplier=pps.id_supplier)
      INNER JOIN ps_supplier pss ON (pss.id_supplier=ptp.id_supplier)
      SET ptp.flag='i' 
      WHERE pps.id_supplier is null AND ptp.flag != 'n'");

  $query_4_1 = mysqli_query($mysqli_1, "UPDATE ps_product_supplier pps 
      INNER JOIN ps_temp_product ptp ON ( ptp.id_product = pps.id_product AND ptp.id_supplier = pps.id_supplier )
      SET pps.product_supplier_price_te = ptp.price,
      pps.product_supplier_reference = ptp.product_supplier_reference
      WHERE ptp.flag = 'u';
      ");

  $query_5 = mysqli_query($mysqli_1, "UPDATE ps_temp_product ptp LEFT JOIN (
      SELECT 0 AS `id_tax_rules_group`
      UNION SELECT `id_tax_rules_group`
      FROM `ps_tax_rules_group`
      WHERE `active` = 1 ) ptx ON (ptp.id_impuesto = ptx.id_tax_rules_group)  
      SET ptp.flag = 'n' 
      WHERE ptx.id_tax_rules_group IS NULL OR 
      ptp.price <= 0 OR
      ptp.precio_venta <= 0 OR 
      ptp.price IS NULL OR
      ptp.precio_venta IS NULL");

  $query_6 = mysqli_query($mysqli_1, "DELETE pps 
      FROM ps_product_supplier pps 
      INNER JOIN ps_temp_product ptp ON (pps.id_product = ptp.id_product AND ptp.flag != 'n') 
      WHERE ( pps.id_product, pps.id_supplier ) NOT IN ( SELECT tp.id_product, tp.id_supplier FROM ps_temp_product tp 
      INNER JOIN ps_supply_order_detail sod ON ( tp.id_product = sod.id_product AND ( tp.flag != 'n') )
      INNER JOIN ps_supply_order_icr soi ON ( sod.id_supply_order_detail = soi.id_supply_order_detail )
      INNER JOIN ps_icr i ON ( soi.id_icr = i.id_icr )
      WHERE i.id_estado_icr = 2 
      GROUP BY tp.id_product, tp.id_supplier )
      GROUP BY pps.id_product, pps.id_supplier");
  //$query_6 = mysqli_query($mysqli_1, " DELETE pps.* FROM ps_product_supplier pps INNER JOIN ps_temp_product ptp ON (pps.id_product = ptp.id_product) WHERE ptp.flag = 'u' OR ptp.flag = 'i'");

  $query_7 = mysqli_query($mysqli_1, "INSERT INTO ps_product_supplier (id_product, id_supplier, product_supplier_reference, product_supplier_price_te, id_currency)
      (SELECT ptp.id_product, ptp.id_supplier, ptp.product_supplier_reference, ptp.price, 1 FROM ps_temp_product ptp 
      -- INNER JOIN ps_product pp ON (ptp.id_product=pp.id_product) 
      WHERE ptp.flag = 'i')");

  /*$query_7 = mysqli_query($mysqli_1, " INSERT INTO ps_product_supplier (id_product, id_supplier, product_supplier_reference, product_supplier_price_te, id_currency)
      (SELECT ptp.id_product, ptp.id_supplier, ptp.product_supplier_reference, ptp.price, 1 FROM ps_temp_product ptp 
      -- INNER JOIN ps_product pp ON (ptp.id_product=pp.id_product) 
      WHERE ptp.flag = 'i' OR ptp.flag = 'u')");*/

  /* $query_7 = mysqli_query($mysqli_1, "update ps_product_supplier pps INNER JOIN ps_temp_product ptp on (pps.id_product=ptp.id_product and pps.id_supplier= ptp.id_supplier)
      SET pps.product_supplier_price_te = ptp.price,
      pps.product_supplier_reference = ptp.product_supplier_reference
      WHERE ptp.flag='u'");
      */

  $query_8 = mysqli_query($mysqli_1, "update ps_product pp INNER JOIN 
      (SELECT a1.`id_product` , a1.`id_supplier`, a1.`product_supplier_price_te` AS price 
        FROM `ps_product_supplier` a1
        INNER JOIN 
          ( 
          SELECT MIN(`product_supplier_price_te` ) AS pmin, `id_product` , `id_supplier`
          FROM `ps_product_supplier` WHERE `id_product`
            IN ( SELECT id_product
            FROM ps_temp_product
            WHERE flag != 'n'  )
          AND `product_supplier_price_te` > 0
          GROUP BY `id_product`
          ) b2 ON  
          ( a1.`id_product` =  b2.`id_product` AND
        a1.`product_supplier_price_te` =  b2.pmin) 
      ) ptp ON 

      (pp.id_product = ptp.id_product )
      INNER JOIN ps_product_shop pps ON (pps.id_product = ptp.id_product )
      INNER JOIN ps_temp_product pstpo ON (pstpo.id_product = pp.id_product AND pstpo.id_supplier = ptp.id_supplier)
      SET pp.price = pstpo.precio_venta,
      pps.price = pstpo.precio_venta,
      pps.wholesale_price = pstpo.precio_venta,
      pp.id_supplier = ptp.id_supplier,
      pp.id_tax_rules_group = pstpo.id_impuesto,
      pps.id_tax_rules_group = pstpo.id_impuesto,
      pp.supplier_reference = pstpo.product_supplier_reference");
$query_exec = 1;

/*
  $sql = "
      CREATE TABLE IF NOT EXISTS `ps_temp_product` (
      `reference` varchar(32) DEFAULT NULL,
      `price` decimal(20,0) DEFAULT NULL,
      `id_supplier` int(10) DEFAULT NULL,
      `product_supplier_reference` varchar(32) DEFAULT NULL,
      `precio_venta` int(11) NOT NULL,
      `id_impuesto` int(11) NOT NULL DEFAULT '0',
      `id_product` int(10) DEFAULT NULL,
      `flag` enum('n','i','u') DEFAULT 'n' COMMENT 'u: update, i: insert, n: no action'
      ) ENGINE=MEMORY DEFAULT CHARSET=utf8;

      TRUNCATE TABLE ps_temp_product;

      -- cargar archivo csv con los datos a actualizar
      LOAD DATA lOCAL INFILE '".$upFile."'
      INTO TABLE ps_temp_product
      FIELDS TERMINATED BY ';'
      OPTIONALLY ENCLOSED BY '\"' 
      LINES TERMINATED BY '\\r\\n'
      IGNORE 1 LINES 
      (reference, id_supplier, product_supplier_reference, price, precio_venta, id_impuesto); 
            
      -- actualizar registos de la tabla temporal que deben ser actualizados     
      update ps_temp_product ptp INNER JOIN ps_product pp ON (pp.reference=ptp.reference)
      SET ptp.id_product=pp.id_product, ptp.flag='u';

      -- actualizar registos de la tabla temporal que deben ser insertados   
      UPDATE ps_temp_product ptp 
      LEFT JOIN ps_product_supplier pps ON (ptp.id_product=pps.id_product AND ptp.id_supplier=pps.id_supplier)
      INNER JOIN ps_supplier pss ON (pss.id_supplier=ptp.id_supplier)
      SET ptp.flag='i' 
      WHERE pps.id_supplier is null AND ptp.flag != 'n';

      -- update flag si precio o impuesto es erróneo     
      UPDATE ps_temp_product ptp LEFT JOIN (
      SELECT 0 AS `id_tax_rules_group`
      UNION SELECT `id_tax_rules_group`
      FROM `ps_tax_rules_group`
      WHERE `active` = 1 ) ptx ON (ptp.id_impuesto = ptx.id_tax_rules_group)  
      SET ptp.flag = 'n' 
      WHERE ptx.id_tax_rules_group IS NULL OR 
      ptp.price < 0 OR
      ptp.precio_venta < 0;
    
      -- inserta los supplier que no estan en product
      INSERT INTO ps_product_supplier (id_product, id_supplier, product_supplier_reference, product_supplier_price_te, id_currency)
      (SELECT ptp.id_product, ptp.id_supplier, ptp.product_supplier_reference, CONCAT(ptp.price, '.000000'), 1 FROM ps_temp_product ptp 
      -- INNER JOIN ps_product pp ON (ptp.id_product=pp.id_product) 
      WHERE ptp.flag = 'i');

      -- actualizar precio en la tabla ps_product_supplier 
      update ps_product_supplier pps INNER JOIN ps_temp_product ptp on (pps.id_product=ptp.id_product and pps.id_supplier= ptp.id_supplier)
      SET pps.product_supplier_price_te = CONCAT(ptp.price, '.000000'),
      pps.product_supplier_reference = ptp.product_supplier_reference
      WHERE ptp.flag='u';

      -- actualizar precio en la tabla ps_product
      update ps_product pp INNER JOIN 
      (SELECT a1.`id_product` , a1.`id_supplier`, a1.`product_supplier_price_te` AS price 
        FROM `ps_product_supplier` a1
        INNER JOIN 
          ( 
          SELECT MIN(`product_supplier_price_te` ) AS pmin, `id_product` , `id_supplier`
          FROM `ps_product_supplier` WHERE `id_product`
            IN ( SELECT id_product
            FROM ps_temp_product
            WHERE flag != 'n'  )
          GROUP BY `id_product`
          ) b2 ON  
          ( a1.`id_product` =  b2.`id_product` AND
        a1.`product_supplier_price_te` =  b2.pmin) 
      ) ptp ON 

      (pp.id_product = ptp.id_product )
      INNER JOIN ps_product_shop pps ON (pps.id_product = ptp.id_product )
      INNER JOIN ps_temp_product pstpo ON (pstpo.id_product = pp.id_product AND pstpo.id_supplier = ptp.id_supplier)
      SET pp.price = CONCAT(pstpo.precio_venta, '.000000'),
      pps.price = CONCAT(pstpo.precio_venta, '.000000'),
      pp.id_supplier = ptp.id_supplier,
      pp.id_tax_rules_group = pstpo.id_impuesto,
      pps.id_tax_rules_group = pstpo.id_impuesto,
      pp.supplier_reference = pstpo.product_supplier_reference;
  
      ";


  $mysqli = new mysqli(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

  // comprobar conexión 
  if (mysqli_connect_errno()) {
      printf("Conexión fallida: %s\n", mysqli_connect_error());
      return false;
      exit();
  }

  $query_exec=0;

    // ejecutar multi consulta
    if ($mysqli->multi_query($sql)) {
        do {
            // almacenar primer juego de resultados 
            if ($result = $mysqli->store_result()) {
                while ($row = $result->fetch_row()) {
                    printf("%s\n", $row[0]);
                }
                $result->free();
            }
            //mostrar divisor 
            if ($mysqli->more_results()) {
               
            }
        } while ($mysqli->next_result());
    $query_exec=1;
    }

usleep(20000000);
*/
     //echo "</pre><br />FIN Actualizando precios"; 
    if($query_exec == 1) {
     
      // adicionar resumen de la tabla temporal en el archivo subido.
      
      $resultado = mysqli_query($mysqli_1, "SELECT reference,price,id_supplier,product_supplier_reference,precio_venta,id_impuesto,id_product,flag FROM ps_temp_product");
      
      $headers = array('reference','price','id_supplier','product_supplier_reference','precio_venta','id_impuesto','id_product','flag');    
      $handle = fopen($this->pathFiles().$this->nuevo_archivo, 'a+');
      fwrite($handle,"\r\n");
      fputcsv($handle, $headers, ';');
       
      while($results = mysqli_fetch_assoc($resultado)) {
          $row = array(
              $results['reference'],
              $results['price'],
              $results['id_supplier'],
              $results['product_supplier_reference'],
              $results['precio_venta'],
              $results['id_impuesto'],
              $results['id_product'],
              $results['flag']
          );
          fputcsv($handle, $row, ';');
      }
       
      fclose($handle);

      //mysqli_query($mysqli, "DROP TABLE ps_temp_product");

      /* cerrar conexión */
      //mysqli_close($mysqli_1);


      return true;
    } else {
      return false;
    }

  }

    
}
