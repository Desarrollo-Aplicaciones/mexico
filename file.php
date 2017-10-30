<?php
/*
 *Code By Abhishek R. Kaushik
 * Downloaded from http://devzone.co.in
 *Modificado Por: Esteban Rincon
 *Para: Farmalisto
 */
require_once(dirname(__FILE__).'/config/config.inc.php');

$upload_dir = "log/ordensuministro/";


if (isset($_FILES["file"])) {
    if ($_FILES["file"]["error"] > 0) {
        echo "Error: " . $_FILES["file"]["error"] . "<br>";
    } else {

        $actualiza_price = "4926363";
        $dataUser='_';
        $names = saveFile($_FILES, "file",$dataUser);        
        if (is_array($names) && $names[0] != '' && $names[0] != false ) {
            $actualiza_price = loadProducts(pathFiles().$names[0],Tools::getValue('id_supplier_order_copy'),Tools::getValue('id_supplier_copy'),$names[2]);
        }

        if ($actualiza_price == false) {
            //header("HTTP/1.0 404 Error cargando el listado de productos.");
            $msg_send = "Error cargando el listado de productos.";
        } elseif ($actualiza_price == 406) {
            //header("HTTP/1.0 406 Error cargando el listado de productos, algunos productos no corresponden con el proveedor seleccionado.");
            $msg_send = "Error cargando el listado de productos, algunos productos no corresponden con el proveedor seleccionado.";
        } elseif ($actualiza_price == 407) {
            //header("HTTP/1.0 407 Error cargando el listado de productos, algunos productos están duplicados.");
            $msg_send = "Error cargando el listado de productos, algunos productos están duplicados.";
        } elseif ($actualiza_price == 204) {
            //header("HTTP/1.0 204 No se cargo ningún producto.");
            $msg_send = "No se cargo ningún producto.";
        } else {
          $err_paso = 0;
            switch ($actualiza_price) {
              case '001':
                $err_paso = 1;
                $msg_send = " No se ha podido borrar la tabla.";
                break;
              case '002':
                $err_paso = 1;
                $msg_send = " No se ha podido cargar los registros a la tabla.";
                break;
              case '003':
                $err_paso = 1;
                $msg_send = " No se ha podido actualizar el proveedor.";
                break;
              case '004':
                $err_paso = 1;
                $msg_send = " No se ha podido aplicar la taza de impuesto.";
                break;
              case '005':
                $err_paso = 1;
                $msg_send = " No se ha podido colocar el nombre del producto.";
                break;
              case '006':
                $err_paso = 1;
                $msg_send = " No se ha podido asignar el proveedor al producto.";
                break;         
             
            }
            if ( $err_paso == 1 ) {
              header("HTTP/1.0 404 ".$msg_send.', -'.$actualiza_price.'-');
            } else {
              echo $actualiza_price;
            }
        }
        echo '<div style="color: #f00; border:solid 1px #f00;padding:20px;background-color:#fdd">'.$msg_send.'</div>';
    }
}
else{
  echo '<div style="color: #f00; border:solid 1px #f00;padding:20px;background-color:#fdd">
    Error: No se ha cargado ningún archivo, por favor intentar nuevamente
    </div>';
}


    # funciones para la gestion de subida de archivos 
        
   # función para guardar documentos
 function saveFile($arrayDoc,$documento,$dataUser)
{  
  
  # Sustituir especios por guion
  $archivo_usuario = str_replace(' ','-',$arrayDoc[$documento]['name']); 

  $tipo_archivo = $arrayDoc[$documento]['type']; 
  $tamano_archivo = $arrayDoc[$documento]['size'];
  $extencion = strrchr($arrayDoc[$documento]['name'],'.');

  # Rutina que asegura que no se sobre-escriban documentos
  $nuevo_archivo;
  $flag = true;
  while ($flag)
  {
    $nuevo_archivo=randString().$dataUser.$extencion;
    if (!file_exists(pathFiles().$nuevo_archivo)) {
      $flag= false;
    }
  }
  #compruebo si las características del archivo son las que deseo 
  try {

     if (move_uploaded_file($arrayDoc[$documento]['tmp_name'],pathFiles().$nuevo_archivo))
     { 
      chmod(pathFiles().$nuevo_archivo, 0777);
     
       return $vector = array ( $nuevo_archivo, $archivo_usuario, $tamano_archivo );
     } else { 
      
       return $vector = array (false, false );
     } 
  }
  catch(Exception $e)
  {
  echo 'Error en la Función sefeFile --> lib.php ', $e->getMessage(), "\n";
  exit;
  }
}
# función que genera una cadena aleatoria
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

# Retorna la ruta donde se encuentran los archivos de los usuarios
 function pathFiles()
{
  # Definir directorio donde almacenar los archivos, debe terminar en "/" 
    $directorio=Configuration::get('PATH_UPLOAD')."ordensuministro/";

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
      error_reporting("E_ALL");
      ini_set("log_errors", 1);

    $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        
        $url_post = explode(':', _DB_SERVER_);


        if ( count($url_post) > 1 ) {

          mysqli_real_connect($mysqli_1, $url_post[0], _DB_USER_, _DB_PASSWD_, _DB_NAME_, $url_post[1]);

        } else {

          mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        }

        if (mysqli_connect_errno()) {            
            return "Conexión fallida: %s\n mensaje error: " . mysqli_connect_error();
        }


    #ELIMINAR REGISTROS ANTERIORES

    $sql = "TRUNCATE TABLE "._DB_PREFIX_."tmp_product_order";
    
    #ASIGNACIÓN DE NUEVA LINEA PARA EL SERVIDOR
    
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
      $nl = '\\r\\n';
    } else {
      $nl = '\\n';
    }
    
    #CREAR LISTADO DE PRODUCTOS EN TABLA TEMPORAL
    
    $sql2 = " LOAD DATA LOCAL INFILE '".$upFile."'
      INTO TABLE "._DB_PREFIX_."tmp_product_order
      FIELDS TERMINATED BY ';'
      OPTIONALLY ENCLOSED BY '\"' 
      LINES TERMINATED BY '".$nl."'
      IGNORE 1 LINES 
      (product_reference, supplier_reference, precio_compra, cantidad, tasa_descuento, tasa_impuesto)";

     
      /*error_log( $sql2 );*/


    $sql3 = " UPDATE "._DB_PREFIX_."tmp_product_order SET id_order = ".$order.",
    id_supplier = ".$supplier."
    WHERE id_order IS NULL";

    $sql4 = "UPDATE "._DB_PREFIX_."tmp_product_order ptp INNER JOIN "._DB_PREFIX_."product pp ON (pp.reference = ptp.product_reference)
    LEFT JOIN "._DB_PREFIX_."tax tax ON (pp.id_tax_rules_group = tax.id_tax)
    SET ptp.id_product=pp.id_product, ptp.tasa_impuesto=IFNULL(tax.rate,0)
    WHERE ptp.id_order = ".$order;

    $sql5 = "UPDATE "._DB_PREFIX_."tmp_product_order ptp INNER JOIN "._DB_PREFIX_."product_lang pl ON (pl.id_product = ptp.id_product)
    SET ptp.nombre = pl.name
    WHERE ptp.id_order = ".$order;

      
    $sql6 = "UPDATE "._DB_PREFIX_."tmp_product_order ptp 
    INNER JOIN "._DB_PREFIX_."product_supplier ps ON (ptp.id_product = ps.id_product AND ptp.id_supplier = ps.id_supplier)
    SET ptp.precio_compra = CASE 
    WHEN ptp.precio_compra = 0 THEN SUBSTRING_INDEX(ps.product_supplier_price_te, '.', 1)
    ELSE SUBSTRING_INDEX(ptp.precio_compra, '.', 1)
    END,  
    ptp.flag='u'
    WHERE ptp.id_order = ".$order;


    if ( !Db::getInstance()->execute($sql) ) {
    return "001";
    }  
    if ( !mysqli_query($mysqli_1, $sql2) ) { 
    return "002";
    }  
    if ( !Db::getInstance()->execute($sql3) ) {
    return "003";
    }  
    if ( !Db::getInstance()->execute($sql4) ) {
    return "004";
    }  
    if ( !Db::getInstance()->execute($sql5) ) {
    return "005";
    }  
    if ( !Db::getInstance()->execute($sql6) ) {
      return "006";
    }
    
    #ADICIONAR RESUMEN DE LA TABLA TEMPORAL EN EL ARCHIVO SUBIDO.
    
    $query_resumen = new DbQuery();
    $query_resumen->select('id_product');
    $query_resumen->select('id_supplier');
    $query_resumen->select('id_order');
    $query_resumen->select('product_reference');
    $query_resumen->select('nombre');
    $query_resumen->select('supplier_reference');
    $query_resumen->select('precio_compra');
    $query_resumen->select('cantidad');
    $query_resumen->select('tasa_descuento');
    $query_resumen->select('tasa_impuesto');
    $query_resumen->select('flag');
    $query_resumen->from('tmp_product_order');
    $query_resumen->where('id_order='.$order);
    $result = Db::getInstance()->executeS($query_resumen);
    if(isset($result[0]) && is_array($result[0])){
      $headers = array_keys($result[0]);
    }
    else{
      return FALSE;
    }

    $handle = fopen($upFile, 'a+');
    fwrite($handle, $nl);
    fputcsv($handle, $headers, ';');

    foreach ($result as $row) {
      fputcsv($handle, $row, ';');
    }
    fclose($handle);
    
    #VALIDACION DE ERRORES
    
    #ERROR REFERENCIA NO EXISTENTE
    $query_error = new DbQuery();
    $query_error->select('COUNT(id_product_order) AS errf');
    $query_error->from('tmp_product_order');
    $query_error->where('id_order = "'.$order.'" AND flag = "n"');
    $cant_error = Db::getInstance()->executeS($query_error);
    $cant_error_r = $cant_error[0];

    #ERROR PRODUCTO DUPLICADO
    $query_error_dup = new DbQuery();
    $query_error_dup->select('COUNT(id_product_order) AS errc');
    $query_error_dup->from('tmp_product_order');
    $query_error_dup->where('id_order="'.$order.'"');
    $query_error_dup->groupBy('id_product');
    $query_error_dup->having('COUNT(id_product)>1');
    $query_error_dup->orderBy('COUNT(id_product) DESC');
    $cant_error_dup = Db::getInstance()->executeS($query_error_dup);
    $cant_error_dup_r = $cant_error_dup[0];
    if ((isset($cant_error_r['errf']) && $cant_error_r['errf'] > 0) ||
        (isset($cant_error_dup_r['errc']) && $cant_error_dup_r['errc'] > 0)) {
      return 406;
    }

    #ERROR NO SE CARGARON PRODUCTOS
    $query_total = new DbQuery();
    $query_total->select('COUNT(id_product_order) AS total');
    $query_total->from('tmp_product_order');
    $query_total->where('id_order="'.$order.'" AND flag != "n"');
    $cant_total = Db::getInstance()->executeS($query_total);
    $cant_total_r = $cant_total[0];
    if ($cant_total_r['total'] <= 0) {
      return 204;
    }
    
    #OBTENER REGISTROS TABLA
    $prods_cargados = new DbQuery();
    $prods_cargados->select('CONCAT(p.id_product, "_", IFNULL(pa.id_product_attribute, "0")) as id');
    $prods_cargados->select('tpo.supplier_reference as supplier_reference');
    $prods_cargados->select('tpo.product_reference as reference');
    $prods_cargados->select('IFNULL(pa.ean13, IFNULL(p.ean13, "")) as ean13');
    $prods_cargados->select('IFNULL(pa.upc, IFNULL(p.upc, "")) as upc');
    $prods_cargados->select('md5(CONCAT("'._COOKIE_KEY_.'", p.id_product, "_", IFNULL(pa.id_product_attribute, "0"))) as checksum');
    $prods_cargados->select('tpo.nombre as name');
    $prods_cargados->select('tpo.precio_compra as unit_price_te');
    $prods_cargados->select('tpo.cantidad as quantity');
    $prods_cargados->select('tpo.tasa_descuento as tasa_des');
    $prods_cargados->select('tpo.tasa_impuesto as tasa_imp');
    $prods_cargados->from('product', 'p');
    $prods_cargados->innerJoin('tmp_product_order', 'tpo', 'tpo.id_product = p.id_product');
    $prods_cargados->leftJoin('product_attribute', 'pa', 'pa.id_product = p.id_product');
    $prods_cargados->leftJoin('product_supplier', 'ps', 'ps.id_product = p.id_product AND ps.id_product_attribute = IFNULL(pa.id_product_attribute, 0)');

    $subquery = new DbQuery();
    $subquery->select('pd.id_product');
    $subquery->from('product_download', 'pd');
    $subquery->where('pd.id_product = p.id_product');
    
    $prods_cargados->where('tpo.id_order = "'.$order.'"
              AND p.id_product NOT IN ('.$subquery->__toString().')
              AND p.is_virtual = 0 AND p.cache_is_pack = 0
              AND (ps.id_supplier = "'.$supplier.'" OR p.id_supplier = "'.$supplier.'")');
    $prods_cargados->groupBy('p.id_product, pa.id_product_attribute');
    $cargados = Db::getInstance()->executeS($prods_cargados);

    #DIBUJAR TABLA
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
    foreach ($cargados as $results) {
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
      <td class="center"><input type="text" name="input_tax_rate_'.$results['id'].'" value="'.$results['tasa_imp'].'" size="5" readonly/>%</td>
      <td class="center"><a href="#" class="removeProductFromSupplyOrderLink" id="deletelink|'.$results['id'].'">
      <img src="../img/admin/delete.gif" alt="Remover este producto del pedido" title="Remover este producto del pedido" />
      </a></td>
      </tr>';
    }
    $list_producst_val .='</tbody><input type="hidden" id="arr_prods" name="arr_prods" value="'.substr($arr_prods, 0, - 1).'">';
    return $list_producst_val;
  }

?>



   