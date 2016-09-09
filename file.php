<?php
/*
 *Code By Abhishek R. Kaushik
 * Downloaded from http://devzone.co.in
 */
require_once(dirname(__FILE__).'/config/config.inc.php');

$upload_dir = "log/ordensuministro/";


if (isset($_FILES["file"])) {
    if ($_FILES["file"]["error"] > 0) {
        echo "Error: " . $_FILES["file"]["error"] . "<br>";
    } else {



        $dataUser='_';//.Context::getContext()->cookie->id_employee.'_'.current(array_slice(explode("@",Context::getContext()->employee->email),0,1));
        $names = saveFile($_FILES, "file",$dataUser);        
        if (is_array($names) && $names[0] != '' && $names[0] != false ) {
            $actualiza_price = loadProducts(pathFiles().$names[0],$_POST['id_supplier_order_copy'],$_POST['id_supplier_copy'],$names[2]);
        }

        if ($actualiza_price == false) {
            header("HTTP/1.0 404 Error cargando el listado de productos.");
        } elseif ($actualiza_price == 406) {
            header("HTTP/1.0 406 Error cargando el listado de productos, algunos productos no corresponden con el proveedor seleccionado.");
        } elseif ($actualiza_price == 407) {
            header("HTTP/1.0 407 Error cargando el listado de productos, algunos productos se encuentran duplicados.");
        } elseif ($actualiza_price == 204) {
            header("HTTP/1.0 204 No se cargo ningún producto.");
        } else {
            echo $actualiza_price;
        }

    }
}


    // funciones para la gestion de subida de archivos 
        
   // función para guardar documentos
 function saveFile($arrayDoc,$documento,$dataUser)
{  
  
  // Sustituir especios por guion
  $archivo_usuario = str_replace(' ','-',$arrayDoc[$documento]['name']); 

  $tipo_archivo = $arrayDoc[$documento]['type']; 
  $tamano_archivo = $arrayDoc[$documento]['size'];
  $extencion = strrchr($arrayDoc[$documento]['name'],'.');

  // Rutina que asegura que no se sobre-escriban documentos
  $nuevo_archivo;
  $flag = true;
  while ($flag)
  {
    $nuevo_archivo=randString().$dataUser.$extencion;//.$extencion;
    if (!file_exists(pathFiles().$nuevo_archivo)) {
      $flag= false;
    }
  }
  //compruebo si las características del archivo son las que deseo 
  try {

     if (move_uploaded_file($arrayDoc[$documento]['tmp_name'],pathFiles().$nuevo_archivo))
     { 
      chmod(pathFiles().$nuevo_archivo, 0755);
     
       //return $nuevo_archivo;       
       //print_r($vector);
       return $vector = array ( $nuevo_archivo, $archivo_usuario, $tamano_archivo );
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
 function randString ($length = 4)
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

 function exist_file($name_file)
{
  if (file_exists(pathFiles().$name_file)) {
    return true; 
  } else {
    return false; 
  }
}

// Retorna la ruta donde se encuentran los archivos de los usuarios
 function pathFiles()
{
  // Definir directorio donde almacenar los archivos, debe terminar en "/" 
    $directorio="/home/ubuntu/ordensuministro/";
    //$directorio="log/ordensuministro/";

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


   function loadProducts($upFile,$order,$supplier,$tama_file)
  {

$mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        
        $url_post = explode(':', _DB_SERVER_);


        if ( count($url_post) > 1 ) {

          mysqli_real_connect($mysqli_1, $url_post[0], _DB_USER_, _DB_PASSWD_, _DB_NAME_, $url_post[1]);

        } else {

          mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        }

 /* 
$mysqli_1 = mysqli_init();
mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
mysqli_real_connect($mysqli_1,_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
*/
  if (mysqli_connect_error()) {
      printf("Conexión fallida: %s\n", mysqli_connect_error());
      return false;
      exit();
  }


//$mysqli_1 = mysqli_connect(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

$query_exec=0;

    $query_1 = mysqli_query($mysqli_1, "TRUNCATE TABLE "._DB_PREFIX_."tmp_product_order");
    
    $query_2 = mysqli_query($mysqli_1, " LOAD DATA LOCAL INFILE '".$upFile."'
    INTO TABLE "._DB_PREFIX_."tmp_product_order
    FIELDS TERMINATED BY ';'
    OPTIONALLY ENCLOSED BY '\"' 
    LINES TERMINATED BY '\\r\\n'
    IGNORE 1 LINES 
    (product_reference, supplier_reference, precio_compra, cantidad, tasa_descuento, tasa_impuesto)");

    
    $query_3 = mysqli_query($mysqli_1, " UPDATE "._DB_PREFIX_."tmp_product_order SET id_order = ".$order.",
    id_supplier = ".$supplier."
    WHERE id_order IS NULL");

      $query_4 = mysqli_query($mysqli_1, "UPDATE "._DB_PREFIX_."tmp_product_order ptp INNER JOIN "._DB_PREFIX_."product pp ON (pp.reference = ptp.product_reference)
    LEFT JOIN "._DB_PREFIX_."tax tax ON (pp.id_tax_rules_group = tax.id_tax)
    SET ptp.id_product=pp.id_product, ptp.tasa_impuesto=IFNULL(tax.rate,0)
    WHERE ptp.id_order = ".$order."");

      $query_5 = mysqli_query($mysqli_1, "UPDATE "._DB_PREFIX_."tmp_product_order ptp INNER JOIN "._DB_PREFIX_."product_lang pl ON (pl.id_product = ptp.id_product)
    SET ptp.nombre = pl.name
    WHERE ptp.id_order = ".$order."");
      
      $query_6 = mysqli_query($mysqli_1, "UPDATE "._DB_PREFIX_."tmp_product_order ptp 
    INNER JOIN "._DB_PREFIX_."product_supplier ps ON (ptp.id_product = ps.id_product AND ptp.id_supplier = ps.id_supplier)
    SET ptp.precio_compra = CASE 
    WHEN ptp.precio_compra = 0 THEN ps.product_supplier_price_te
    ELSE ptp.precio_compra
    END,
    ptp.flag='u'
    WHERE ptp.id_order = ".$order."");
//usleep(250000);
$query_exec=1;
  /*$mysqli = new mysqli(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);  

 */

/*
  $query_exec=1;


    if ($mysqli->multi_query($sql)) {
        do {
   
            if ($result = $mysqli->store_result()) {
                while ($row = $result->fetch_row()) {
                   if (!$mysqli->more_results()) break;
                }
                 $result->commit();
                $result->free();
            }

           
        } while ($mysqli->next_result());
    $query_exec=1;
    }

*/

/*



if (mysqli_multi_query($mysqli_1, $sql)) {
    do {
      
    } while (mysqli_next_result($mysqli_1));
    $query_exec = 1;
}
*/



  
    /* cerrar conexión */
   //$mysqli->close();

     //echo "</pre><br />FIN Actualizando precios"; 
    if($query_exec == 1) {
     

    
      // adicionar resumen de la tabla temporal en el archivo subido.
      $query_resumen = "SELECT id_product, id_supplier, id_order, product_reference, nombre, supplier_reference, precio_compra, cantidad, tasa_descuento, tasa_impuesto, flag FROM ps_tmp_product_order WHERE id_order=".$order;
      $resultado = mysqli_query($mysqli_1, $query_resumen);
      
      $headers = array('id_product', 'id_supplier', 'id_order', 'product_reference', 'nombre', 'supplier_reference', 'precio_compra', 'cantidad', 'tasa_descuento', 'tasa_impuesto', 'flag');    
     
      $handle = fopen($upFile, 'a+');
      fwrite($handle,"\r\n");
      fputcsv($handle, $headers, ';');
       
      while($results = mysqli_fetch_assoc($resultado)) {        
          $row = array(
              $results['id_product'],
              $results['id_supplier'],
              $results['id_order'],
              $results['product_reference'],
              $results['nombre'],
              $results['supplier_reference'],
              $results['precio_compra'],
              $results['cantidad'],
              $results['tasa_descuento'],
              $results['tasa_impuesto'],
              $results['flag']
          );
          fputcsv($handle, $row, ';');
      }
       
      fclose($handle);

      //mysqli_query($mysqli_1, "DROP TABLE "._DB_PREFIX_."temp_product");


      // si hay algun producto con error
      //echo "<br>". 
      $query_error = "SELECT COUNT(id_product_order) AS errf FROM "._DB_PREFIX_."tmp_product_order WHERE id_order = ".$order." AND flag = 'n'";
      $cant_error = mysqli_query($mysqli_1, $query_error);
      $cant_error_r = mysqli_fetch_assoc($cant_error);
      // echo "<br>
      // flag: ".$cant_error_r['errf'];


      // si hay algun producto repetido
      //echo "<br>". 
      $query_error_dup = "SELECT COUNT(id_product_order) AS errc FROM ps_tmp_product_order WHERE id_order=".$order."
                        GROUP BY id_product
                        HAVING COUNT(id_product)>1
                        ORDER BY COUNT(id_product) DESC";
      $cant_error_dup = mysqli_query($mysqli_1, $query_error_dup);
      $cant_error_dup_r = mysqli_fetch_assoc($cant_error_dup);

      // echo "<br>
      // cant dup: ".$cant_error_dup_r['errc'];

      if ($cant_error_r['errf'] > 0 ) {
        return 406;
      }

      if ( $cant_error_dup_r['errc'] > 0) {
        return 407;
      }

      //si no se cargan productos
      //echo "<br>". 
      $query_total = "SELECT COUNT(id_product_order) AS total FROM "._DB_PREFIX_."tmp_product_order WHERE id_order='".$order."' AND flag != 'n'";

      $cant_total = mysqli_query($mysqli_1, $query_total);
      $cant_total_r = mysqli_fetch_assoc($cant_total);

      // echo "<br>
      // tot_prods: ".$cant_total_r['total'];
      

      if ($cant_total_r['total'] <= 0) {
        return 204;
      }


      $prods_cargados = mysqli_query($mysqli_1, "SELECT 
      CONCAT(p.id_product, '_', IFNULL(pa.id_product_attribute, '0')) as id,
      tpo.supplier_reference as supplier_reference,
      tpo.product_reference as reference,
      IFNULL(pa.ean13, IFNULL(p.ean13, '')) as ean13,
      IFNULL(pa.upc, IFNULL(p.upc, '')) as upc,
      md5(CONCAT('"._COOKIE_KEY_."', p.id_product, '_', IFNULL(pa.id_product_attribute, '0'))) as checksum,
      tpo.nombre as name,
      tpo.precio_compra as unit_price_te,
      tpo.cantidad as quantity,
      tpo.tasa_descuento as tasa_des,
      tpo.tasa_impuesto as tasa_imp
      FROM `"._DB_PREFIX_."product` p 
      INNER JOIN `"._DB_PREFIX_."tmp_product_order`       tpo ON (tpo.id_product = p.id_product)
      LEFT JOIN `"._DB_PREFIX_."product_attribute`        pa ON  (pa.id_product = p.id_product)
      LEFT JOIN `"._DB_PREFIX_."product_supplier`         ps ON  (ps.id_product = p.id_product AND ps.id_product_attribute = IFNULL(pa.id_product_attribute, 0))
      WHERE tpo.id_order = '".$order."'
      AND p.id_product NOT IN (SELECT pd.id_product FROM `"._DB_PREFIX_."product_download` pd WHERE (pd.id_product = p.id_product))
      AND p.is_virtual = 0 AND p.cache_is_pack = 0
      AND (ps.id_supplier = ".$supplier." OR p.id_supplier = ".$supplier.")
      GROUP BY p.id_product, pa.id_product_attribute;");
      
      $list_producst_val = '<thead>
<tr class="nodrag nodrop">
<th style="width: 150px">Referencia</th>
<th style="width: 50px">EAN13</th>
<th style="width: 50px">UPC</th>
<th style="width: 150px">Referencia del proveedor</th>
<th>Nombre</th>
<th style="width: 100px">Precio unitario (sin IVA)</th>
<th style="width: 100px">Cantidad</th>
<th style="width: 100px">Tasa de descuento (sin IVA)</th>
<th style="width: 100px">Tasa de los impuestos</th>
<th style="width: 40px">Eliminar</th>
</tr>
</thead>
<tbody>';
$arr_prods = "";
      while($results = mysqli_fetch_assoc($prods_cargados)) {
        $arr_prods .= $results['id']."|";
          
         $list_producst_val .='
<tr style="height:50px;"> 
<td>'.$results['reference'].'<input type="hidden" name="input_check_'.$results['id'].'" value="'.$results['checksum'].'" /><input type="hidden" name="input_reference_'.$results['id'].'" value="'.$results['reference'].'" /></td>
<td>'.$results['ean13'].'<input type="hidden" name="input_ean13_'.$results['id'].'" value="'.$results['ean13'].'" /></td>
<td>'.$results['upc'].'<input type="hidden" name="input_upc_'.$results['id'].'" value="'.$results['upc'].'" /></td>
<td>'.$results['supplier_reference'].'<input type="hidden" name="input_supplier_reference_'.$results['id'].'" value="'.$results['supplier_reference'].'" /></td>
<td>'.$results['name'].'<input type="hidden" name="input_name_displayed_'.$results['id'].'" value="'.$results['name'].'" /></td>
<td class="center">$&nbsp;<input type="text" name="input_unit_price_te_'.$results['id'].'" value="'.$results['unit_price_te'].'" size="8" />&nbsp;</td>
<td class="center"><input type="text" name="input_quantity_expected_'.$results['id'].'" value="'.$results['quantity'].'" size="5" /></td>
<td class="center"><input type="text" name="input_discount_rate_'.$results['id'].'" value="'.$results['tasa_des'].'" size="5" />%</td>
<td class="center"><input type="text" name="input_tax_rate_'.$results['id'].'" value="'.$results['tasa_imp'].'" size="5" readonly />%</td>
<td class="center"><a href="#" class="removeProductFromSupplyOrderLink" id="deletelink|'.$results['id'].'">
<img src="../img/admin/delete.gif" alt="Remover este producto del pedido" title="Remover este producto del pedido" />
</a></td>
</tr>';


      }


$list_producst_val .='</tbody><input type="hidden" id="arr_prods" name="arr_prods" value="'.substr($arr_prods, 0, - 1).'">';

     


      return $list_producst_val;
    } else {
      return false;
    }

  }

   

?>



   