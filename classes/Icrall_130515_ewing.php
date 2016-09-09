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
class IcrallCore extends ObjectModel {
    
   private  $nuevo_archivo;
 
  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
    'table' => 'icr',
    'primary' => 'id_icr',
    'fields' => array(
      'cod_icr' => array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'required' => true, 'size' => 6),
      'id_estado' => array('type' => self::TYPE_INT, 'required' => true),
    ),
  );

    private $empledado;
      // Listado de productos para la orden de salida
    private $productos_orsa = array();

      // Listado de productos para la orden de entrada
    private $productos_oren = array();

        // Listado de productos para la orden de salida y entrada
    private $productos_arca = array();

        // Listado de productos para la orden de salida y entrada
    private $productos_arca_icr = array();  

        // Listado de icr para la orden de salida y entrada
    private $icr_actualizar= array();  

        // Listado de icr para la orden de salida y entrada
    private $cod_icr_actualizar= array(); 

        // Listado de icr para la orden de salida y entrada
    private $id_orders_actualizar= array();

    // Listado de errores en el cargue
    public $errores_cargue = array();    


    /**
     * [validarIcrDuplicados Valida que los ICr cargados no se repitan en el mismo archivo]
     * @return [type] [bool]
     */
    public function validarIcrDuplicados() {
      //..echo "<br>1";

        $query_icr_duplicado = "SELECT cod_icr , COUNT(cod_icr) as cant FROM ps_tmp_cargue_icr_salida
          GROUP BY cod_icr 
          HAVING COUNT(cod_icr) > 1";

        if ($results_icr = Db::getInstance()->ExecuteS($query_icr_duplicado)) {
            $this->errores_cargue[] = "Existen errores en cargue, hay ICR duplicados.";
            return false;
        } else {
        return true;    
        }
    }

    /**
     * [validarIcrCargadoVsIngresado Validar ICR cargados vr los ingresados en las ordenes de suministros y el detalle de la orden de ps_tmp_cargue_icr_salida]
     * @return [type] [bool]
     */
    public function validarIcrCargadoVsIngresado() {
      //..echo "<br>2";

        $query_icr_compara = "UPDATE ps_tmp_cargue_icr_salida tcis
            INNER JOIN ps_order_detail od ON (tcis.id_orden = od.id_order AND tcis.reference = od.product_reference)
            INNER JOIN ps_supply_order_detail sod ON (sod.id_product = od.product_id)
            INNER JOIN ps_supply_order_icr soi ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
            INNER JOIN ps_icr i ON (i.cod_icr = tcis.cod_icr AND i.id_icr = soi.id_icr)
            SET 
            tcis.id_product = od.product_id,
            tcis.id_icr = i.id_icr,
            tcis.flag = 'i'
            WHERE i.id_estado_icr = 2";

        if ($results_icr = Db::getInstance()->ExecuteS($query_icr_compara)) {
            return true;
        } else {
            $this->errores_cargue[] = "Error en la actualización de los ICR cargados.";
        return false;    
        }
    }


    public function validarIcrCargadoVsPicking() {
      //..echo "<br>2.5";

        $query_icr_compara = "UPDATE ps_tmp_cargue_icr_salida tcis
            INNER JOIN ps_order_picking op ON (tcis.id_icr = op.id_order_icr)
            SET             
            tcis.flag = 'n'";

        if ($results_icr = Db::getInstance()->ExecuteS($query_icr_compara)) {
            return true;
        } else {
            $this->errores_cargue[] = "Error en la actualización del cargue Vs Picking.";
        return false;    
        }
    }


    public function validarEstadoRegistrosCargados() {
      //..echo "<br>3";
        $errores = 0;
        $query_icr_flag = "SELECT id_orden, reference, cod_icr FROM ps_tmp_cargue_icr_salida 
            WHERE flag = 'n'";

        if ($results_icr_flag = Db::getInstance()->ExecuteS($query_icr_flag)) {           

            $this->errores_cargue[] = "Error de datos en el archivo cargado.";

            foreach ($results_icr_flag as $row) {

                $this->errores_cargue[] = "Existen errores en los registros, id_orden: ".$row['id_orden'].", Referencia: ".$row['reference'].", Icr: ".$row['cod_icr'];
           }

            return false;

        } else {
           
        return true;    
        }        
    }

    
    public function OrdenesProductos() {
      //..echo "<br>4";
        
        //echo "<br>1: ".
        $query_prods = "SELECT od.id_order, od.product_id, sal.saliente AS cantidad FROM ps_order_detail od
            INNER JOIN 
            ( 
                SELECT tcis.id_orden, tcis.id_product FROM ps_tmp_cargue_icr_salida tcis
                GROUP BY tcis.id_orden, tcis.id_product 
                ORDER BY tcis.id_orden, tcis.id_product 
            ) ics
            ON ( ics.id_orden = od.id_order AND ics.id_product = od.product_id)
            INNER JOIN 
            (
                SELECT odd.product_quantity, COUNT(op.id_order_detail), 
                (cast(odd.product_quantity AS SIGNED) - COUNT(CAST(op.id_order_detail AS SIGNED))) AS saliente, 
                o.id_order, odd.product_id 
                FROM ps_order_detail odd 
                LEFT JOIN ps_order_picking op ON (op.id_order_detail = odd.id_order_detail) 
                INNER JOIN ps_orders o ON (o.id_order = odd.id_order) 
                WHERE o.id_order IN 
                  (SELECT los.id_orden FROM ps_tmp_cargue_icr_salida los GROUP BY los.id_orden) 
                GROUP BY o.id_order, odd.product_id 
                HAVING (CAST(odd.product_quantity AS SIGNED) - COUNT(CAST(op.id_order_detail AS SIGNED))) > 0
                ORDER BY o.id_order, odd.product_id                
            ) sal
            ON ( sal.id_order = od.id_order AND sal.product_id = od.product_id )
            GROUP BY od.id_order, od.product_id
            ORDER BY od.id_order, od.product_id";


        if ($results = Db::getInstance()->ExecuteS($query_prods)) {

            foreach ($results as $row) {
                $this->productos_orsa[ $row['id_order'] ][ $row['product_id'] ] = $row['cantidad'];
            }
            return true;
        } else {
            $this->errores_cargue[] = "No se pudieron obtener los productos de las ordenes cargadas, o algunos productos ingresados ya poseen todos los ICR asociados.
            <br>Contacte a su administrador.";
            return false;    
       }

    }


    public function IcrCargados() {
      //..echo "<br>5";
        
        //echo "<br>2: ".
        $query_icr_table = " SELECT id_orden, id_product, id_icr, cod_icr FROM ps_tmp_cargue_icr_salida

        GROUP BY id_orden, id_product, id_icr
        ORDER BY id_orden, id_product, id_icr";


        if ($results_icr = Db::getInstance()->ExecuteS($query_icr_table)) {

           foreach ($results_icr as $row) {

            $this->productos_arca_icr[ $row['id_orden'] ][ $row['id_product'] ][ $row['id_icr'] ] = $row['cod_icr'];

            if (!in_array($row['id_orden'], $this->id_orders_actualizar)) {
                $this->id_orders_actualizar[] = $row['id_orden'];
            }

            $this->icr_actualizar[] = $row['id_icr'];
            $this->cod_icr_actualizar[] = $row['cod_icr'];

            if (isset($this->productos_arca[ $row['id_orden'] ][ $row['id_product'] ])) {
                $this->productos_arca[ $row['id_orden'] ][ $row['id_product'] ] ++;
            } else {
                $this->productos_arca[ $row['id_orden'] ][ $row['id_product'] ] = 1;
            }

           }
           return true;
        } else {
          $this->errores_cargue[] = "No se pudieron obtener las Ordenes, productos y/o ICR del archivo cargado. Contacte a su administrador.";
       return false;    
       }

    }

   
    /**
     * validarProductosOrden valida que los productos enviados correspondan a la orden seleccionada
     * @param  string $accion define la accion a realizad con los productos
     * @return bool         verdadero o falso según la validación realizada
     */
    public function validarProductosOrden() {  
    //..echo "<br>6";     
        $error = 0;


        foreach ($this->productos_orsa as $id_orden ) { //SI ARRAYS TIENEN MISMOS PRODUCTOS Y MISMAS CANTIDADES
            if (in_array($id_orden, $this->productos_arca)){ 
                //echo '<br>Existe'.$id_orden;
            } else {
                $error ++;
            }

        }
        if($error > 0) {

            foreach ($this->productos_arca as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if( !$this->productos_orsa[$key][$key2] ){ // si no tiene ese producto en la orden de salida
                        $this->errores_cargue[] = "No existe el producto , id_producto: ".$key2." o la orden: ".$key.", de los ICR cargados.";                
                    } elseif( $value2 > $this->productos_orsa[$key][$key2]) { // si la cantidad de productos es mayor a la solicitada en la orden de salida
                        $this->errores_cargue[] = "Hay mas ICR para cargar en la orden: ".$key." , id_producto: ".$key2." de los permitidos.
                        <br> A cargar: ".$value2.", Requeridos:".$this->productos_orsa[$key][$key2];
                    } 
                }
               
            }

            if(count($this->errores_cargue) > 0) {

                return false;  

            } else {

                return true;

            }
            
        } else {

            return true;
        }

        
    }
     

     public function insertarPicking() {
    //..echo "<br>7";
        $insertar_query = "INSERT INTO `"._DB_PREFIX_."order_picking` (`id_order_icr`, `id_order_supply_icr`, `id_order_detail`, `date`, `id_employee`)                      
              SELECT t1.id_order_icr,t1.id_order_supply_icr, t1.id_order_detail, t1.date,t1.id_employee

              FROM
              ( SELECT COUNT(icr.id_icr) AS prod, 
                orders_d.product_id, 
                icr.id_icr AS id_order_icr, 
                s_order_i.id_supply_order_icr AS id_order_supply_icr,
                orders_d.id_order_detail, NOW() AS date ,  ".$this->empledado->id."  as id_employee  
              from ps_orders orders
              INNER JOIN ps_order_detail orders_d ON(orders.id_order=orders_d.id_order)
              INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
              INNER JOIN ps_supply_order_icr s_order_i ON(s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
              INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
              INNER JOIN ps_tmp_cargue_icr_salida tis 
                ON ( tis.id_orden = orders.id_order AND tis.id_product = orders_d.product_id AND tis.id_icr = icr.id_icr)
              WHERE icr.id_estado_icr=2 
              GROUP BY tis.id_orden, tis.id_product, icr.id_icr 
              ORDER BY orders.id_order, orders_d.product_id ) as t1 ";

           if ($result = DB::getInstance()->execute($insertar_query)) {

                $validar_insert_picking = "SELECT COUNT(`id_order_icr`) AS cant FROM `"._DB_PREFIX_."order_picking` WHERE id_order_icr IN (".implode(",", $this->icr_actualizar).")";

                if ( $result2 = DB::getInstance()->executeS($validar_insert_picking) ) { // validar insertar picking

                    if ( $result2[0]['cant'] == count($this->icr_actualizar) ) {
                        return true;
                    } else {
                        $this->errores_cargue[] = "La cantidad insertada para picking no correponde con la cantidad cargada. 
                        <br> Insertada: ".$result2[0]['cant'].", Cargada: ".count($this->icr_actualizar)."
                        <br>cod_icr (".implode(",", $this->icr_actualizar).") - orders  (".implode(",", $this->id_orders_actualizar).").";
                        return false;
                    }

                } else {
                    $this->errores_cargue[] = "No se pudo insertar en la tabla de picking, no se encuentran registros. Contacte a su administrador del sistema. 
                    <br>cod_icr (".implode(",", $this->icr_actualizar).") - orders  (".implode(",", $this->id_orders_actualizar).").";
                    return false;
                }
           }        
     $this->errores_cargue[] = "No se pudo insertar en la tabla de picking. Contacte a su administrador del sistema. 
     <br>cod_icr (".implode(",", $this->icr_actualizar).") - orders  (".implode(",", $this->id_orders_actualizar).").";      
     return false;
     }


     public function cambiarIcrEstado() {
        //..echo "<br>8"; 
              $query = "UPDATE ps_icr icrU 
                  INNER JOIN
                  (
                  SELECT  icr.cod_icr
                  FROM ps_orders orders 
                  INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
                  INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
                  INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
                  INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
                  INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
                  WHERE icr.id_estado_icr=2 AND orders.id_order IN (".implode(",", $this->id_orders_actualizar).")
                  ) as actualizar
                  ON(icrU.cod_icr=actualizar.cod_icr)

                  SET icrU.id_estado_icr=3";

          if (DB::getInstance()->execute($query)) {
                return true;
          }   

      $this->errores_cargue[] = "No se pudo cambiar el estado de los ICR. Contacte a su administrador del sistema. 
     <br> orders  (".implode(",", $this->id_orders_actualizar).").";      
     return false;
     }


    public function reducirStock() {
    //..echo "<br>9"; 


        $query_prods_restar_stock = "SELECT so.id_warehouse, sod.id_product, count(sod.id_product) AS cantprods FROM ps_supply_order so 
            INNER JOIN ps_supply_order_detail sod ON ( so.id_supply_order = sod.id_supply_order )
            INNER JOIN ps_supply_order_icr soi ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
            INNER JOIN ps_order_picking op ON ( op.id_order_supply_icr = soi.id_supply_order_icr )
            INNER JOIN ps_icr i ON ( i.id_icr = soi.id_icr AND i.id_icr = op.id_order_icr )
            WHERE  i.id_icr IN (".implode(",",$this->icr_actualizar).")
            AND i.id_estado_icr = 3 
            GROUP BY so.id_warehouse, sod.id_product"; 

            $id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT');
            $usable = 1;
            if ($res_query_prods_restar_stock = Db::getInstance()->ExecuteS($query_prods_restar_stock)) {
                foreach ($res_query_prods_restar_stock as $row_res_stock) {
                

                    $warehouse = new Warehouse($row_res_stock['id_warehouse']);

                    $id_product = $row_res_stock['id_product'];
                    $id_product_attribute = null;
                    $quantity = $row_res_stock['cantprods'];

                    // remove stock
                    $stock_manager = StockManagerFactory::getManager();
                    
                    $removed_products = $stock_manager->removeProduct($row_res_stock['id_product'], null, $warehouse, $row_res_stock['cantprods'], $id_stock_mvt_reason, $usable); 
                     /*echo "<pre><br>remprods: ";
                     print_r($removed_products);*/


                    if (count($removed_products) > 0)
                    {
                        StockAvailable::synchronize($id_product);
                        // Tools::redirectAdmin($redirect.'&conf=2');
                    }
                    else
                    {
                        $physical_quantity_in_stock = (int)$stock_manager->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), false);
                        $usable_quantity_in_stock = (int)$stock_manager->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), true);
                        $not_usable_quantity = ($physical_quantity_in_stock - $usable_quantity_in_stock);
                        if ($usable_quantity_in_stock < $quantity)
                            $this->errores_cargue[] = sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d out of %d.'), (int)$quantity, (int)$usable_quantity_in_stock);
                        else if ($not_usable_quantity < $quantity)
                            $this->errores_cargue[] = sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d out of %d.'), (int)$quantity, (int)$not_usable_quantity);
                        else
                            $this->errores_cargue[] = Tools::displayError('It is not possible to remove the specified quantity. Therefore no stock was removed.');
                    }

                }
     
                $query_2 = "UPDATE  ps_stock_mvt sm INNER JOIN ps_employee e ON (e.id_employee = sm.id_employee)
                SET sm.employee_firstname = e.firstname,
                    sm.employee_lastname = e.lastname
                WHERE sm.employee_firstname = '' AND sm.employee_lastname = ''
                AND sm.id_employee = ".$this->empledado->id;


                if ( $results = Db::getInstance()->ExecuteS($query_2) ) {
                    return true;
                } else {
                    $this->errores_cargue[] = "No se pudo reducir actualizar el empleado en el stock. Contacte a su administrador del sistema.";      
                    return false;
                }
            }

        $this->errores_cargue[] = "No se pudo reducir el stock. Contacte a su administrador del sistema. 
        <br> orders ('".implode("','",$this->id_orders_actualizar)."').";      
        return false;

     }





/**   ENTRADA DE ICRS A LAS ORDENES DE SUMINISTROS   **/




    /**
     * [validarIcrDuplicados Valida que los ICr cargados no se repitan en el mismo archivo ( ps_tmp_cargue_entrada_icr )]
     * @return [type] [bool]
     */
    public function validarIcrDuplicadosEntrada() {
      //..-echo "<br>1";

        $query_icr_duplicado = "SELECT cod_icr , COUNT(cod_icr) as cant FROM ps_tmp_cargue_entrada_icr
          GROUP BY cod_icr 
          HAVING COUNT(cod_icr) > 1";

        if ($results_icr = Db::getInstance()->ExecuteS($query_icr_duplicado)) {
            $this->errores_cargue[] = "Existen errores en cargue, hay ICR duplicados.";
            return false;
        } else {
        return true;    
        }
    }


    /**
     * [validarIcrCargadoVsIngresado Validar ICR cargados vr los ingresados en las ordenes de suministros y el detalle de la orden de ps_tmp_cargue_icr_salida]
     * @return [type] [bool]
     */
    public function validarIcrCargadoVsIngresadoEntrada() {
      //..-echo "<br>2: ".

        $query_icr_compara = "UPDATE ps_tmp_cargue_entrada_icr tcei
            INNER JOIN 
            ( 
                SELECT tcei2.id_orden_suministro, tcei2.reference, sod.id_product, i.id_icr, tcei2.cod_icr, 
                sod.quantity_expected, sod.quantity_received,
                ( sod.quantity_expected - sod.quantity_received ) AS faltantes 
                FROM ps_tmp_cargue_entrada_icr tcei2
                INNER JOIN ps_supply_order so ON ( so.id_supply_order = tcei2.id_orden_suministro AND so.id_supplier = tcei2.id_proveedor AND so.id_supply_order_state = 3)
                INNER JOIN ps_supply_order_detail sod ON ( so.id_supply_order = sod.id_supply_order AND tcei2.reference = sod.reference )
                INNER JOIN ps_product p ON ( p.id_product = sod.id_product AND p.active = 1 AND p.is_virtual = 0 )
                INNER JOIN ps_icr i ON ( i.cod_icr = tcei2.cod_icr AND i.id_estado_icr = 1 )
                HAVING ( sod.quantity_expected - sod.quantity_received ) > 0
                ORDER BY sod.id_supply_order, sod.id_product 
            ) list ON ( tcei.id_orden_suministro = list.id_orden_suministro AND tcei.reference = list.reference
            AND tcei.cod_icr = list.cod_icr )
            SET tcei.id_product = list.id_product,
            tcei.id_icr = list.id_icr,
            tcei.flag = 'i';";

        if ($results_icr = Db::getInstance()->Execute($query_icr_compara)) {
            return true;
        } else {
            $this->errores_cargue[] = "Error en la actualización de los ICR cargados.";
        return false;    
        }
    }


    public function validarIcrCargadoVsSupplyOrderIcr() {
      //..-echo "<br>2.5";

        $query_icr_compara = "UPDATE ps_tmp_cargue_entrada_icr tcei
            INNER JOIN ps_supply_order_icr soi ON (tcei.id_icr = soi.id_icr)
            SET             
            tcei.flag = 'n'";

        if ($results_icr = Db::getInstance()->ExecuteS($query_icr_compara)) {
            return true;
        } else {
            $this->errores_cargue[] = "Error en la actualización del cargue Vs Supply Order Icr.";
        return false;    
        }
    }


    public function validarIcrCargadoVsSupplyOrderIcrCantidades() {
      //..-echo "<br>3: ";

        $query_icr_comparacant = "UPDATE ps_tmp_cargue_entrada_icr tcei2
            INNER JOIN ps_supply_order_detail sod ON ( sod.id_supply_order = tcei2.id_orden_suministro AND tcei2.reference = sod.reference)
            INNER JOIN ps_product p ON ( p.id_product = sod.id_product AND p.active = 1 AND p.is_virtual = 0)
            INNER JOIN ps_icr i ON ( i.cod_icr = tcei2.cod_icr AND i.id_estado_icr = 1 )
            INNER JOIN 
          (
          SELECT toten.id_orden_suministro, toten.id_product, COUNT(toten.id_product) AS cantcarg FROM ps_tmp_cargue_entrada_icr toten
          GROUP BY toten.id_orden_suministro, toten.id_product
          ) datcar ON ( sod.id_supply_order = datcar.id_orden_suministro AND datcar.id_product = sod.id_product)
            SET tcei2.flag = 'd'
            WHERE ( 
            (CAST( sod.quantity_expected AS SIGNED) - CAST( sod.quantity_received AS SIGNED) )  
            - CAST( datcar.cantcarg AS SIGNED)
          ) < 0";

        if ($results_icr_c = Db::getInstance()->ExecuteS($query_icr_comparacant)) {
            return true;
        } else {
            $this->errores_cargue[] = "Error en la actualización del cargue Vs Supply Order Icr verificando cantidades.";
        return false;    
        }
    }


    public function validarEstadoRegistrosCargadosEntrada() {
      //..-echo "<br>4";
        $errores = 0;
        $query_icr_flag = "SELECT id_orden_suministro, reference, cod_icr, flag FROM ps_tmp_cargue_entrada_icr 
            WHERE flag = 'n' OR flag = 'd'";

        if ($results_icr_flag = Db::getInstance()->ExecuteS($query_icr_flag)) {           

            $this->errores_cargue[] = "Error de datos en el archivo de Entrada cargado. Posibles causas: <br>Ordenes de suministro en estado diferende a pendiente recepción,
            <br>Icr aún no creados, <br>Productos asociados incorrectamente a una orden, <br>Proveedor asociado incorrectamente a una orden.";

            foreach ($results_icr_flag as $row) {
                if ( $row['flag'] == 'd') {
                  $this->errores_cargue[] = "Cantidad a insertar, superior a la esperada, id_orden_suministro: ".$row['id_orden_suministro'].", Referencia: ".$row['reference'].", Icr: ".$row['cod_icr'];           
                } else {
                  $this->errores_cargue[] = "Existen errores en los registros, id_orden_suministro: ".$row['id_orden_suministro'].", Referencia: ".$row['reference'].", Icr: ".$row['cod_icr'];           
                }
            }

            return false;

        } else {
           
        return true;    
        }        
    }


    public function OrdenesProductosEntrada() {
      //..-echo "<br>5: ";
        
        //..-//echo "<br>1: ".
        $query_prods = " SELECT sod.id_supply_order, sod.id_product, sod.quantity_expected, sod.quantity_received,
               (sod.quantity_expected - sod.quantity_received) AS disponible 
               FROM ps_supply_order_detail sod
               INNER JOIN ps_supply_order so ON ( 
                  so.id_supply_order = sod.id_supply_order )
                  INNER JOIN ps_tmp_cargue_entrada_icr tcei2 ON ( tcei2.id_orden_suministro = so.id_supply_order 
                  AND tcei2.id_proveedor = so.id_supplier AND tcei2.id_product = sod.id_product 
                )
               GROUP BY sod.id_supply_order, sod.id_product, sod.quantity_expected, sod.quantity_received";


        if ($results = Db::getInstance()->ExecuteS($query_prods)) {

            foreach ($results as $row) {
                $this->productos_oren[ $row['id_supply_order'] ][ $row['id_product'] ] = $row['disponible'];
            }
            return true;
        } else {
            $this->errores_cargue[] = "No se pudieron obtener los productos de las ordenes cargadas, o los productos ingresados ya poseen todos los ICR asociados.
            <br>Contacte a su administrador.";
            return false;    
       }

    }


    public function IcrCargadosEntrada() {
      //..-echo "<br>6";
        
        //..-//echo "<br>2: ".
        $query_icr_table = " SELECT tcei2.id_orden_suministro, tcei2.id_product, tcei2.id_icr, tcei2.cod_icr FROM
            ps_tmp_cargue_entrada_icr tcei2
            GROUP BY tcei2.id_orden_suministro, tcei2.id_product";


        if ($results_icr = Db::getInstance()->ExecuteS($query_icr_table)) {

           foreach ($results_icr as $row) {

            $this->productos_arca_icr[ $row['id_orden_suministro'] ][ $row['id_product'] ][ $row['id_icr'] ] = $row['cod_icr'];

            if (!in_array($row['id_orden_suministro'], $this->id_orders_actualizar)) {
                $this->id_orders_actualizar[] = $row['id_orden_suministro'];
            }

            $this->icr_actualizar[] = $row['id_icr'];
            $this->cod_icr_actualizar[] = $row['cod_icr'];

            if (isset($this->productos_arca[ $row['id_orden_suministro'] ][ $row['id_product'] ])) {
                $this->productos_arca[ $row['id_orden_suministro'] ][ $row['id_product'] ] ++;
            } else {
                $this->productos_arca[ $row['id_orden_suministro'] ][ $row['id_product'] ] = 1;
            }

           }
           return true;
        } else {
          $this->errores_cargue[] = "No se pudieron obtener las Ordenes, productos y/o ICR del archivo cargado. Contacte a su administrador.";
       return false;    
       }

    }




 /**
     * validarProductosOrden valida que los productos enviados correspondan a la orden seleccionada
     * @param  string $accion define la accion a realizad con los productos
     * @return bool         verdadero o falso según la validación realizada
     */
    public function validarProductosOrdenEntrada() {  
    //..-echo "<br>7";     
        $error = 0;


        foreach ($this->productos_oren as $id_orden ) { //SI ARRAYS TIENEN MISMOS PRODUCTOS Y MISMAS CANTIDADES
            if (in_array($id_orden, $this->productos_arca)){ 
                //echo '<br>Existe'.$id_orden;
            } else {
                $error ++;
            }

        }
        if($error > 0) {

            foreach ($this->productos_arca as $key => $value) {
                foreach ($value as $key2 => $value2) {
                    if( !$this->productos_oren[$key][$key2] ){ // si no tiene ese producto en la orden de salida
                        $this->errores_cargue[] = "No existe el producto , id_producto: ".$key2." o la orden: ".$key.", de los ICR cargados.";
                    } elseif( $value2 > $this->productos_oren[$key][$key2]) { // si la cantidad de productos es mayor a la solicitada en la orden de salida
                        $this->errores_cargue[] = "Hay mas ICR para cargar en la orden: ".$key." , id_producto: ".$key2." de los permitidos.
                        <br> A cargar: ".$value2.", Requeridos:".$this->productos_oren[$key][$key2];
                    } 
                }
               
            }

            if(count($this->errores_cargue) > 0) {

                return false;  

            } else {

                return true;

            }
            
        } else {

            return true;
        }

        
    }


    /**
     * InsertarProductosIcrOrden    inserta los icr por cada producto almacenado en el formulario
     */
    public function InsertarProductosIcrOrdenEntrada() {
        //..-echo "<br>8";
        $query = 'INSERT INTO `'._DB_PREFIX_.'supply_order_icr` (`id_supply_order_detail`, `id_icr`, `id_employee`, `fecha`, `lote`, `fecha_vencimiento`) ';
        $query .= ' SELECT sod.id_supply_order_detail, cei.id_icr, "'.$this->empledado->id.'", now(), cei.lote, cei.fecha_vencimiento FROM `ps_supply_order_detail` sod
            INNER JOIN `ps_tmp_cargue_entrada_icr` cei 
            ON (cei.id_orden_suministro = sod.id_supply_order AND cei.id_product = sod.id_product )
            ORDER BY sod.id_supply_order_detail, cei.id_icr';

        if ($retorno = DB::getInstance()->execute($query) ) {
            return true;
        } else {
            $this->errores_cargue[] = "No se pudieron ingresar los registros del archivo a la tabla de asociación de ICR.";
            return false;
        }
    }



    /**
     * updateIcrProductoOrder    actualiza la cantidad de productos dependiendo del parámetro accion_icr 
     * @return string si la actualización es correcta retorna '1' si no retorna '0'
     */
    public function updateIcrProductoOrderEntrada() {
        //..-echo "<br>9";
        $way = '+';
        $this->status_icr = 2;

            $query = new DbQuery();
            $query->select(' od.id_supply_order, od.id_supply_order_detail AS suordetail, COUNT(ol.id_product) AS cant ');
            $query->from('tmp_cargue_entrada_icr', 'ol');
            $query->innerJoin('supply_order_detail', 'od', ' od.id_product = ol.id_product AND od.id_supply_order = ol.id_orden_suministro ');
            //$query->where('ol.id_supply_order = '.(int)$this->supply_order);
            $query->groupBy(' od.id_supply_order_detail ');


            $items = Db::getInstance()->executeS($query);
        
            if ($items) {
                $supply_order_detailBoxI = array();
                $quantity_received_todayI = array();

                foreach ($items as $item){
                    //echo "<br>item:".$item['suordetail']." | cantidad:".$item['cant'];
                    $supply_order_detailBoxI[] = $item['suordetail'];                    
                    $quantity_received_todayI[$item['suordetail']] = $item['cant'];
                    $supply_order_arr[$item['suordetail']] = $item['id_supply_order'];
                    
                }

                //$supply_order->postProcessUpdateReceipt($supply_order_detailBoxI, $quantity_received_todayI, $this->supply_order, " (".$this->accion_icr.")");                       
            



                Context::getContext()->employee->id = $this->empledado->id;
                Context::getContext()->employee->firstname = $this->empledado->firstname;
                Context::getContext()->employee->lastname = $this->empledado->lastname;

                $to_update = $quantity_received_todayI;
                foreach ($to_update as $id_supply_order_detail => $quantity) {
                    $last_inserted = '';
                    $supply_order_detail = new SupplyOrderDetail($id_supply_order_detail);
                    
                    $supply_order = new SupplyOrder((int)$supply_order_arr[$id_supply_order_detail]);
                    

                    if (Validate::isLoadedObject($supply_order_detail) && Validate::isLoadedObject($supply_order))
                    {
                        //echo "<br>paso la validacion";
                        // checks if quantity is valid
                        // It's possible to receive more quantity than expected in case of a shipping error from the supplier
                        if (!Validate::isInt($quantity) || $quantity <= 0) {
                            //printf('Quantity (%d) for product #%d is not valid', (int)$quantity, (int)$id_supply_order_detail);
                            $this->errores_cargue[] = "Error 0. Cantidad (".$quantity.") para el producto ".(int)$id_supply_order_detail." no es valida.";
                            return false;
                        } else // everything is valid :  updates
                        {
                            //$this->employee->id.", '".$this->employee->lastname."', '".$this->employee->firstname." (".$this->accion_icr.")
                            //echo "<br> creates the history";
                            $supplier_receipt_history = new SupplyOrderReceiptHistory();
                            $supplier_receipt_history->id_supply_order_detail = (int)$id_supply_order_detail;
                            $supplier_receipt_history->id_employee = (int)$this->empledado->id;
                            $supplier_receipt_history->employee_firstname = pSQL($this->empledado->firstname);
                            $supplier_receipt_history->employee_lastname = pSQL($this->empledado->lastname);
                            $supplier_receipt_history->id_supply_order_state = (int)$supply_order->id_supply_order_state;
                            $supplier_receipt_history->quantity = (int)$quantity;

                            // updates quantity received
                            if ($way == '+') {
                                $supply_order_detail->quantity_received += (int)$quantity;
                            } else {
                                $supply_order_detail->quantity_received -= (int)$quantity;
                            } 

                            // if current state is "Pending receipt", then we sets it to "Order received in part"
                            if (3 == $supply_order->id_supply_order_state)
                                $supply_order->id_supply_order_state = 4;

                            //echo "<br>  Adds to stock";
                            $warehouse = new Warehouse($supply_order->id_warehouse);
                            if (!Validate::isLoadedObject($warehouse))
                            {
                                //echo "<br> error warehouse";
                                $this->errores_cargue[] = "Error 1. ".Tools::displayError($this->l('The warehouse could not be loaded.'));
                                return false;
                            }
                            //echo "<br> precio";
                            $price = $supply_order_detail->unit_price_te;
                            // converts the unit price to the warehouse currency if needed
                            if ($supply_order->id_currency != $warehouse->id_currency)
                            {
                                //echo "<br> convertir precios";
                                // first, converts the price to the default currency
                                $price_converted_to_default_currency = Tools::convertPrice($supply_order_detail->unit_price_te, $supply_order->id_currency, false);

                                // then, converts the newly calculated pri-ce from the default currency to the needed currency
                                $price = Tools::ps_round(Tools::convertPrice($price_converted_to_default_currency,
                                                                             $warehouse->id_currency,
                                                                             true),
                                                         6);
                            }
                            //echo "<br> StockManagerFactory_getManager";
                            $manager = StockManagerFactory::getManager();
                            //echo "<br> Manager";

                            $res = $manager->addProduct($supply_order_detail->id_product,
                                                        $supply_order_detail->id_product_attribute,
                                                        $warehouse,
                                                        (int)$quantity,
                                                        Configuration::get('PS_STOCK_MVT_SUPPLY_ORDER'),
                                                        $price,
                                                        true,
                                                        $supply_order->id);
                            //echo "<br>res";
                            if (!$res){
                                //echo "<br> error res";
                                $this->errores_cargue[] = "Error 2. ".Tools::displayError($this->l('Something went wrong when adding products to the warehouse.'));
                                return false;
                            }

                            $location = Warehouse::getProductLocation($supply_order_detail->id_product,
                                                                      $supply_order_detail->id_product_attribute,
                                                                      $warehouse->id);

                            $res = Warehouse::setProductlocation($supply_order_detail->id_product,
                                                                 $supply_order_detail->id_product_attribute,
                                                                 $warehouse->id,
                                                                 $location ? $location : '');

                            if ($res)
                            {
                                //echo "<br> entro a adicionar";
                                $supplier_receipt_history->add();

                                $last_inserted = Db::getInstance()->Insert_ID(); 

                                if ($last_inserted != '') {
                                $ins_history = "
                                UPDATE ps_supply_order_receipt_history SET employee_firstname = '".$this->empledado->firstname." (add)'
                                WHERE id_supply_order_detail = ".(int)$id_supply_order_detail."
                                AND id_supply_order_receipt_history = ".(int)$last_inserted."";

                                DB::getInstance()->execute($ins_history);
                                }
                                //echo "<br>Antes de guardar detalle orden";
                                $supply_order_detail->save();
                                //echo "<br>Antes de guardar orden";
                                $supply_order->save();
                                //echo "<br>Luego de guardar orden.";
                            }
                            else {
                                //echo "<br> error final";
                                $this->errores_cargue[] = "Error 3. ".Tools::displayError($this->l('Something went wrong when setting warehouse on product record'));
                                return false;
                            }
                        }
                    } else {
                        $this->errores_cargue[] = "Error validando el objeto en el stock.";
                        return false;
                    }
                }

            } else {
              $this->errores_cargue[] = "Error en el query reduciendo el stock.";
              return false;
            }

        return true;
    }



    public function updateIcrStatusEntrada() {
        //..echo "<br>10";
        $query = 'UPDATE `'._DB_PREFIX_.'icr` i INNER JOIN `'._DB_PREFIX_.'tmp_cargue_entrada_icr` ol
            ON ( i.id_icr = ol.id_icr ) 
            SET i.id_estado_icr = 2'; // cambiar estado del icr a asignado
                      
        if ($upicr = DB::getInstance()->execute($query) ) {

            return true;
        } else {
            $this->errores_cargue[] = "Error cambiando el estado de los ICR.";
            return false;
        }


    }


  /**
     * Guarda un archivo proveniente de una solicitud http
     *
     * @array_file  arreglo de archivos html
     * @name_file varible html del archivo
     * @$employee objeto empledado prestashop
     * @$module nombre del modulo
     * @return Array [0] =>'Nuevo nombre', [1] =>'Nombre original', [2]=>'Ruta completa'
     */
    public function saveFile($array_file, $name_file, $employee, $module) {

        $this->empledado = $employee;
        $full_path = null;
        // Sustituir especios por guion
        $nombre_archivo = str_replace(' ', '-', $array_file[$name_file]['name']);

        $extencion = strrchr($array_file[$name_file]['name'], '.');

        // Rutina que asegura que no se sobre-escriban documentos
        $this->nuevo_archivo;
        $flag = true;
        while ($flag) {
            $this->nuevo_archivo = $this->randString() . '_' . $employee->id . '_' . '_' . $employee->firstname . '_' . $employee->lastname . $extencion; //.$extencion;

            $full_path = $this->pathFiles($module) . $this->nuevo_archivo;
            if (!file_exists($this->pathFiles($module) . $this->nuevo_archivo)) {
                $flag = false;
            }
        }
        //Validar caracteristicas del archivo
        try {

            if (move_uploaded_file($array_file[$name_file]['tmp_name'], $this->pathFiles($module) . $this->nuevo_archivo)) {
                chmod($this->pathFiles($module) . $this->nuevo_archivo, 0755);

                //retorna array en [0]=>'nombre original', [1]=>'nuevo nombre', [2]=>'ruta del archivo'
                return $vector = array($this->nuevo_archivo, $nombre_archivo, $full_path);
            } else {

                // retorna un arrar con elementos en false
                return $vector = array(false, false, false);
            }
        } catch (Exception $e) {
            echo 'Error en la Función seveFile --> class Icrall ', $e->getMessage(), "\n";
            exit;
        }
    }

    /*
     * Genera una cadena aleatoria
     * @length longitud de la cadena, por defecto es 4 
     * 
     */

    public function randString($length = 4) {
        $string = "";
        $possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $string .= $char;
            $i++;
        }
        return date("dmY_Hi_") . $string;
    }

    /*
     * verifica si un archivo existe
     * @name_file nombre del archivo
     * @sub_dir para-metro opcional en caso de utilizar subcarpetas
     */

    public function exist_file($name_file, $sub_dir) {
        if (file_exists(pathFiles($sub_dir) . $name_file)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * retorna la ruta de los archivos
     * @sub_dir para-metro opcional en caso de utilizar subcarpetas
     */

    public function pathFiles($sub_dir) {
        // Definir directorio donde almacenar los archivos, debe terminar en "/" 
       // $directorio = "C:/wamp/www/prod.farmalisto.com.co/filesX/";
        $directorio = Configuration::get('PATH_UP_LOAD');

        if (isset($sub_dir) && $sub_dir != '') {
            $directorio.=$sub_dir . '/';
        }

        try {
            $path = "" . $directorio;

            if (!file_exists($path)) {
                mkdir($path, 0755, TRUE);
            }
            return $path;
        } catch (Exception $e) {
            
            $this->errores_cargue[] = "Error crear el archivo tempral, consulte con su administrador ". $e->getMessage();
            return false;
        }
           $this->errores_cargue[] = "Error crear el archivo tempral, consulte con su administrador ";
            return false;
    }

  
 /*
  * carga un archivo csv a la tabla ps_tmp_cargue_icr_salida
  * @path_file_load_db ruta del archivo csv
  */ 
 public function loadicrsalida($path_file_load_db) {


        $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        if (mysqli_connect_errno()) {

            $this->errores_cargue[] = "Conexión fallida: %s\n mensaje error: " . mysqli_connect_error();
            return false;
        }

        if (!mysqli_query($mysqli_1, "TRUNCATE TABLE ps_tmp_cargue_icr_salida")) {
                        $this->errores_cargue[] = "Error al truncar la tabla (ps_tmp_cargue_icr_salida). Mensaje error: " . mysqli_error($mysqli_1);
            return false;
        }

        $cargadat = "LOAD DATA LOCAL INFILE '" . $path_file_load_db . "'
        INTO TABLE ps_tmp_cargue_icr_salida
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"' 
        LINES TERMINATED BY '\\r\\n'
        IGNORE 1 LINES 
        (id_orden, @dummy,@dummy,@dummy, reference, @dummy,@dummy,@dummy,@dummy, cod_icr)";

        if (!mysqli_query($mysqli_1, $cargadat)) {
            $this->errores_cargue[] = "Error al subir el archivo (estructura no valida). Mensaje error: " . mysqli_error($mysqli_1);
          
           return false;
        }  else {
            return true;
            
        }
        return false;
    }



 /*
  * carga un archivo csv a la tabla ps_tmp_cargue_entrada_icr
  * @path_file_load_db ruta del archivo csv
  */ 
 public function loadicrentrada($path_file_load_db) {


        $mysqli_1 = mysqli_init();
        mysqli_options($mysqli_1, MYSQLI_OPT_LOCAL_INFILE, true);
        mysqli_real_connect($mysqli_1, _DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

        if (mysqli_connect_errno()) {

            $this->errores_cargue[] = "Conexión fallida: %s\n mensaje error: " . mysqli_connect_error();
            return false;
        }

        if (!mysqli_query($mysqli_1, "TRUNCATE TABLE ps_tmp_cargue_entrada_icr")) {
                        $this->errores_cargue[] = "Error al truncar la tabla (ps_tmp_cargue_entrada_icr). Mensaje error: " .  mysqli_error($mysqli_1);
            return false;
        }

        $cargadat = "LOAD DATA LOCAL INFILE '" . $path_file_load_db . "'
        INTO TABLE ps_tmp_cargue_entrada_icr
        FIELDS TERMINATED BY ';'
        OPTIONALLY ENCLOSED BY '\"' 
        LINES TERMINATED BY '\\r\\n'
        IGNORE 1 LINES 
        (id_orden_suministro, id_proveedor, reference, @dummy, cod_icr, @dummy, @dummy, @dummy, @dummy, lote, fecha_vencimiento)";


        if (!mysqli_query($mysqli_1, $cargadat)) {
            $this->errores_cargue[] = "Error al subir el archivo (estructura no valida). Mensaje error: " . mysqli_error($mysqli_1);
          
           return false;
        }  else {
            return true;
            
        }
        return false;
    }

}
