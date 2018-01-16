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
 
 include_once(dirname(__FILE__)."/../config/config.inc.php");

class OrdenSuministroDetail {   
    
    private $id_employee=null;

    // nombre del empleado
    public $firstnameEmployee;

    // apellido del empleado
    public $lastnameEmployee;

    // Listado de productos en formulario
    public $productos = array();

    // Listado de icr en formulario
    public $icr = array();

    // Listado de productosIcrs en formulario
    public $productoicr = array();

    // Id de la orden a modificar
    public $supply_order;

    // Accion a realizar en el proceso: adicionar/eliminar asociacion icr
    public $accion_icr;

    // Estado que deben tomar los icr en el proceso
    public $status_icr;

    //obtener id del empleado
    public function getId_employee() {
        return $this->id_employee;
    }

    // asignar id del empleado
    public function setId_employee($id_employee) {
        $this->id_employee = $id_employee;
    }

    /*
    Set Nombre del empleado
     */
    public function setNomemployee($nombreemployee) {
        $this->firstnameEmployee = $nombreemployee;
    }

    /*
    Set Apellido del empleado
     */
    public function setApeemployee($apellidoemployee) {
        $this->lastnameEmployee = $apellidoemployee;
    }

    /**
     * [setProductos description]
     * @param array $arr_prods [arreglo con los productos a modificar]
     */
    public function setProductos($arr_prods) {
        $this->productos = $arr_prods;
    }

    /**
     * [setIcr para asigan el arreglo de icr]
     * @param array $arr_icrs [arreglo con los icr a asociar/desasociar]
     */
    public function setIcr($arr_icrs) {
        $this->icr = $arr_icrs;
    }

    /**
     * setProductosIcr para asignar los productos y sus icrs asociados
     * @param array $arr_prodsIcr arreglo con los produsctos y sus icrs asociados
     */
    public function setProductosIcr($arr_prodsIcr) {
        $this->productoicr = $arr_prodsIcr;
    }

    /**
     * validarProductosOrden valida que los productos enviados correspondan a la orden seleccionada
     * @param  string $accion define la accion a realizad con los productos
     * @return bool         verdadero o falso según la validación realizada
     */
    public function validarProductosOrden($accion = null) {       

        $this->accion_icr = $accion;

        if ($this->supply_order) {

            switch ($this->accion_icr) {

                case 'add':
                    
                    $query = new DbQuery();

                    $query->select(' count(id_product) AS cant ');
                    $query->from('product_supplier', 'p');
                    $query->innerJoin('supply_order', 'sp', 'p.id_supplier = sp.id_supplier');
                    $query->where('sp.id_supply_order = '.$this->supply_order);
                    $query->where('p.id_product IN ('.implode(",", $this->productos).')' );

                    $items = Db::getInstance()->executeS($query);

                        if ($items) {
                            if($items[0]['cant'] == count($this->productos) ) {

                                $resultado_validacion = 1;
                            } else {
                                $resultado_validacion = 0;
                            }

                        } else {
                            $resultado_validacion = 0;
                        }

                    break;


                case 'del': //-------------------------------------------------------------------------
                    
                    $query = new DbQuery();

/*
SELECT od.id_product FROM ps_ supply_order_detail od
INNER JOIN ps_ supply_order_icr oi ON (oi.id_supply_order_detail = od.id_supply_order_detail)
INNER JOIN ps_ icr i ON (i.id_icr = oi.id_icr )
WHERE od.id_supply_order = 2
AND i.id_estado_icr = 2 
AND od.id_product IN (76,54)
GROUP BY od.id_product;
*/

                    $query->select(' od.id_product ');
                    $query->from('supply_order_detail', 'od');
                    $query->innerJoin('supply_order_icr', 'oi', ' oi.id_supply_order_detail = od.id_supply_order_detail ');
                    $query->innerJoin('icr', 'i', ' i.id_icr = oi.id_icr ');
                    $query->where('od.id_supply_order = '.$this->supply_order);
                    $query->where('i.id_estado_icr = 2 ');
                    $query->where('od.id_product IN ('.implode(",", $this->productos).')' );
                    $query->groupBy('od.id_product' );

                    $items = Db::getInstance()->executeS($query);

                        if ($items) {
                            if(count($items) == count($this->productos) ) {

                                $resultado_validacion = 1;
                            } else {
                                $resultado_validacion = 0;
                            }

                        } else {
                            $resultado_validacion = 0;
                        }
                
                    break;
                
                default:

                    $resultado_validacion = 0;

                    break;
            }
            
            

        } else {

            $resultado_validacion = 0;
        }

        return ($resultado_validacion);
    }


    /**
     * validarIcrOrden valida que los icr asociados sean correctos segun la opción almacenada en el parámetro accion_icr
     * @return bool verdadero o falso segun la validación realizada.
     */
    public function validarIcrOrden() {

        if ($this->supply_order) {

             switch ($this->accion_icr) {

                case 'add':
            
                    $query = new DbQuery();

                    $query->select(' count(id_icr) AS cant ');
                    $query->from('icr', 'i');
                    $query->where('i.id_icr IN ('.implode(",",  $this->icr).')' );
                    $query->where('i.id_estado_icr = 1');

                    $items = Db::getInstance()->executeS($query);

                        if ($items) {
                            if($items[0]['cant'] == count($this->icr) ) {

                                $resultado_validacion = 1;
                            } else {
                                $resultado_validacion = 0;
                            }

                        } else {
                            $resultado_validacion = 0;
                        }

                         break;


                case 'del': //-------------------------------------------------------------------------
                    
                    $query = new DbQuery();

/*
SELECT COUNT(i.id_icr) AS cant FROM ps_ supply_order_detail od
INNER JOIN ps_ supply_order_icr oi ON (oi.id_supply_order_detail = od.id_supply_order_detail)
INNER JOIN ps_ icr i ON (i.id_icr = oi.id_icr )
WHERE od.id_supply_order = 2
AND i.id_estado_icr = 2 
AND i.id_icr IN (9,11,7)
AND od.id_product IN (212);
*/

                    $query->select(' COUNT(i.id_icr) AS cant ');
                    $query->from('supply_order_detail', 'od');
                    $query->innerJoin('supply_order_icr', 'oi', ' oi.id_supply_order_detail = od.id_supply_order_detail ');
                    $query->innerJoin('icr', 'i', ' i.id_icr = oi.id_icr ');
                    $query->where('od.id_supply_order = '.$this->supply_order);
                    $query->where('i.id_estado_icr = 2 ');
                    $query->where('i.id_icr IN ('.implode(",", $this->icr).')' );
                    $query->where('od.id_product IN ('.implode(",", $this->productos).')' );

                    $items = Db::getInstance()->executeS($query);

                        if ($items) {
                            if($items[0]['cant'] == count($this->icr) ) {

                                $resultado_validacion = 1;
                            } else {
                                $resultado_validacion = 0;
                            }

                        } else {
                            $resultado_validacion = 0;
                        }
                
                    break;
                
                default:

                    $resultado_validacion = 0;

                    break;
            }                 

        } else {

            $resultado_validacion = 0;
        }

        return ($resultado_validacion);       
    }

    
    /**
     * InsertarProductosIcrOrdenLoad    limpia la tabla de carga de los icr y almacena los nuevos valores enviados desde el formulario
     */
    public function InsertarProductosIcrOrdenLoad() {

        DB::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supply_order_load_icr` ');

        foreach ($this->productoicr as $prod => $idIcrCod) {
            foreach ($idIcrCod as $idicr => $codicr) {

                $query = 'INSERT INTO `'._DB_PREFIX_.'supply_order_load_icr` (`id_supply_order`, `id_product`, `id_icr`, `cod_icr`) VALUES ';
                $query .= '('.(int)$this->supply_order.', '.(int)$prod.', '.(int)$idicr.', "'.strtoupper($codicr).'" ) ';
                DB::getInstance()->execute($query);
                $query='';
            }
        }
    }


    /**
     * InsertarProductosIcrOrden    inserta los icr por cada producto almacenado en el formulario
     */
    public function InsertarProductosIcrOrden() {

        $query = 'INSERT INTO `'._DB_PREFIX_.'supply_order_icr` (`id_supply_order_detail`, `id_icr`, `id_employee`, `fecha`, id_warehouse) ';
        $query .= ' SELECT sod.id_supply_order_detail, soli.id_icr, '.$this->getId_employee().', now(), so.id_warehouse FROM `'._DB_PREFIX_.'supply_order_detail` sod
                    INNER JOIN `'._DB_PREFIX_.'supply_order_load_icr` soli 
                    ON (sod.id_supply_order = soli.id_supply_order AND sod.id_product = soli.id_product)
                    INNER JOIN `'._DB_PREFIX_.'supply_order` so
                    ON so.id_supply_order = sod.id_supply_order
                    WHERE soli.id_supply_order = '.(int)$this->supply_order; 
        DB::getInstance()->execute($query);
    }


    /**
     * updateIcrProductoOrder    actualiza la cantidad de productos dependiendo del parámetro accion_icr 
     * @return string si la actualización es correcta retorna '1' si no retorna '0'
     */
    public function updateIcrProductoOrder() {

        if(isset($this->accion_icr) && $this->accion_icr != '') {

            switch ($this->accion_icr) {
                case 'add':
                    $way = '+';
                    $this->status_icr = 2;
                    break;

                case 'del':
                    $way = '-';
                    $this->status_icr = 1;
                break;
                
                default:
                   return ('0');
                break;
            }

/*select  od.id_supply_order_detail AS suordetail, COUNT(ol.id_product) AS cant 
from ps_supply_order_load_icr ol 
inner Join ps_supply_order_detail od ON (od.id_product = ol.id_product AND od.id_supply_order = ol.id_supply_order )
where ol.id_supply_order = 2
group By od.id_supply_order_detail;
*/
            $query = new DbQuery();
            $query->select(' od.id_supply_order_detail AS suordetail, COUNT(ol.id_product) AS cant ');
            $query->from('supply_order_load_icr', 'ol');
            $query->innerJoin('supply_order_detail', 'od', ' od.id_product = ol.id_product AND od.id_supply_order = ol.id_supply_order ');
            $query->where('ol.id_supply_order = '.(int)$this->supply_order);
            $query->groupBy(' od.id_supply_order_detail ');


            $items = Db::getInstance()->executeS($query);
        
            if ($items) {
                $supply_order_detailBoxI = array();
                $quantity_received_todayI = array();

                foreach ($items as $item){
                    //echo "<br>item:".$item['suordetail']." | cantidad:".$item['cant'];
                    $supply_order_detailBoxI[] = $item['suordetail'];

                    if ($way == '+') {
                        $quantity_received_todayI[$item['suordetail']] = $item['cant'];
                    } else {
                        $quantity_received_todayI[$item['suordetail']] = 0;
                    }
                }

                //$supply_order->postProcessUpdateReceipt($supply_order_detailBoxI, $quantity_received_todayI, $this->supply_order, " (".$this->accion_icr.")");                       
            }










            
            Context::getContext()->employee->id = $this->getId_employee();
            Context::getContext()->employee->firstname = $this->firstnameEmployee;
            Context::getContext()->employee->lastname = $this->lastnameEmployee;

            $to_update = $quantity_received_todayI;
            foreach ($to_update as $id_supply_order_detail => $quantity) {
            $last_inserted = '';
            $supply_order_detail = new SupplyOrderDetail($id_supply_order_detail);
            
            $supply_order = new SupplyOrder((int)$this->supply_order);
            

            if (Validate::isLoadedObject($supply_order_detail) && Validate::isLoadedObject($supply_order))
            {
                //echo "<br>paso la validacion";
                // checks if quantity is valid
                // It's possible to receive more quantity than expected in case of a shipping error from the supplier
                if (!Validate::isInt($quantity) || $quantity <= 0) {
                    //printf('Quantity (%d) for product #%d is not valid', (int)$quantity, (int)$id_supply_order_detail);
                }else // everything is valid :  updates
                {
                    //$this->getId_employee().", '".$this->lastnameEmployee."', '".$this->firstnameEmployee." (".$this->accion_icr.")
                    //echo "<br> creates the history";
                    $supplier_receipt_history = new SupplyOrderReceiptHistory();
                    $supplier_receipt_history->id_supply_order_detail = (int)$id_supply_order_detail;
                    $supplier_receipt_history->id_employee = (int)$this->getId_employee();
                    $supplier_receipt_history->employee_firstname = pSQL($this->firstnameEmployee);
                    $supplier_receipt_history->employee_lastname = pSQL($this->lastnameEmployee);
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
                        $this->errors[] = Tools::displayError($this->l('The warehouse could not be loaded.'));
                        return;
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
                        $this->errors[] = Tools::displayError($this->l('Something went wrong when adding products to the warehouse.'));
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
                        UPDATE ps_supply_order_receipt_history SET employee_firstname = '".$this->firstnameEmployee." (".$this->accion_icr.")'
                        WHERE id_supply_order_detail = ".(int)$id_supply_order_detail."
                        AND id_supply_order_receipt_history = ".(int)$last_inserted."";

                        DB::getInstance()->execute($ins_history);
                        }
        
                        $supply_order_detail->save();
                        $supply_order->save();
                    }
                    else {
                        //echo "<br> error final";
                        $this->errors[] = Tools::displayError($this->l('Something went wrong when setting warehouse on product record'));
                    }
                }
            }
        }










            
            if ($way == '-') {
                $query = 'UPDATE `'._DB_PREFIX_.'supply_order_detail` od INNER JOIN ( SELECT id_supply_order, id_product, COUNT(id_product) AS cant FROM `'._DB_PREFIX_.'supply_order_load_icr` 
                WHERE id_supply_order = '.(int)$this->supply_order.'
                GROUP BY id_product ) AS ol
                ON (od.id_supply_order = ol.id_supply_order AND od.id_product = ol.id_product)
                SET od.quantity_received = od.quantity_received '.$way.' ol.cant';
            }

            $status_query = DB::getInstance()->execute($query);

            if( $status_query ) {
                return ('1');
            } else {
                return ('0');
            }

        } else {
                return ('0');
        }

    }


    public function updateIcrStatus() {

        $query = 'UPDATE `'._DB_PREFIX_.'icr` i INNER JOIN `'._DB_PREFIX_.'supply_order_load_icr` ol
            ON ( i.id_icr = ol.id_icr ) 
            SET i.id_estado_icr = '.$this->status_icr;
                      
        DB::getInstance()->execute($query);
        
        //self::debug_to_console($query, " query ");
        $update_ok = false;
        if (DB::getInstance()->execute($query)) {
          $query2 = "SELECT i.id_icr AS id_icr, i.id_estado_icr AS id_estado_icr, i.cod_icr AS cod_icr 
            FROM ps_icr i 
            INNER JOIN `ps_supply_order_load_icr` ol
            ON ( i.id_icr = ol.id_icr )";
          //self::debug_to_console($query2, " query 2");
          if ($result = DB::getInstance()->executeS($query2)) {
            foreach ($result as $res) {
              //self::debug_to_console($res['id_icr'], " res[id_icr]");
              if (isset($res['id_icr'])) {
                $query3 = "SELECT samv.reserve_on_stock  AS reserve_on_stock, samv.id_product
                  FROM `ps_stock_available_mv` samv 
                  INNER JOIN ps_supply_order_detail sod ON (samv.id_product = sod.id_product)		
                  INNER JOIN ps_supply_order_icr soi ON (soi.id_supply_order_detail = sod.id_supply_order_detail)
                  WHERE soi.id_icr = " . $res['id_icr'];
                //self::debug_to_console($query3, " Query 3");
                $result3 = DB::getInstance()->executeS($query3);

                $productStockMv = $this->getProductStockMv($result3[0]['id_product']);

                //      return var_dump("result3 t", var_dump($result3));
    //              var_dump("ID ICR: ",$res['id_icr'],"reserve_on_stock: ",$result3[0]['reserve_on_stock']);
    //              if (isset($result3)) {
                //self::debug_to_console($result3[0]['reserve_on_stock'], " result3[0][reserve_on_stock]  ");
                if ($res['id_estado_icr'] == 2 && $result3[0]['reserve_on_stock'] != NULL) {
                    $this->updateReserveProduct();
                    $reserve_on_stock = $result3[0]['reserve_on_stock'] - 1;
                } else {
                    $this->getOrdersDiscountIcr();
                    $reserve_on_stock = $result3[0]['reserve_on_stock'] + 1;
                }

                //self::debug_to_console(json_encode($productStockMv), "    Add stock MV ");
                setcookie("add_stock_mv", json_encode($productStockMv), time()+3600);

                $query5 = "CALL update_stock_available_mv(" . $res['id_icr'] . "," . $reserve_on_stock . ",'".Configuration::get('PS_NOT_IN_WAREHOUSE_ICR')."')";

                //self::debug_to_console($query5, " Query5 ");
                //            return var_dump("RESULT2 : id_icr: ", $result2[0]['id_icr'], "id_estado_icr: ", $result2[0]['id_estado_icr'], $id_order, $result3[0]['reserve_on_stock'], "QUERY:", $query4);
                if (DB::getInstance()->execute($query5)) {
    //                  return true;
                    //self::debug_to_console($update_ok, " Update_ok ");              
                    $update_ok = true;
    //                 var_dump("RESULT2 SI", $res['id_icr'], $result3[0]['reserve_on_stock'], "QUERY:", $query5);
                } else {
                    $this->errores_cargue[] = "Error Actualizando el Stock disponible y los reservados, en el ingreso del ICR: " . $res['cod_icr'];
                    return false;
                }

    //              }
    //            }
              }
            }
          }
        }
        if ($update_ok) {
            $ins_history = "
            INSERT INTO ps_supply_order_receipt_history (id_supply_order_detail, id_employee, employee_lastname, 
            employee_firstname, id_supply_order_state, quantity, date_add)

            SELECT od.id_supply_order_detail, ".$this->getId_employee().", '".$this->lastnameEmployee."', '".$this->firstnameEmployee." (".$this->accion_icr.")', IF (od.quantity_expected = od.quantity_received, 5, 
            IF(od.quantity_expected > od.quantity_received, 4, 
            IF(od.quantity_expected < od.quantity_received, 4, 3) ) ) AS state,
            COUNT(oli.id_product) AS canti, NOW()
            FROM ps_supply_order_detail od
            INNER JOIN ps_supply_order_load_icr oli 
            ON (oli.id_supply_order = od.id_supply_order AND oli.id_product = od.id_product) 
            WHERE oli.id_supply_order = ".(int)$this->supply_order."
            GROUP BY (od.id_supply_order_detail)";
            //self::debug_to_console($ins_history, " ins_history ");
          DB::getInstance()->execute($ins_history);
        } else {
          $this->errors[] = Tools::displayError($this->l("Error Actualizando el Stock disponible y los reservados, No se pudo ingresar registro en Histórico orden de suministro recibida"));
        }
       // DB::getInstance()->execute($ins_history);


    }
    
    /**
     * Obtengo la información del producto en la tabla stock_avilable_mv
     * @param int $id_product
     * @return array
     */
    public function getProductStockMv($id_product) {
        
        $validayteStockAvailableMv = new DbQuery();
        $validayteStockAvailableMv->select('*');
        $validayteStockAvailableMv->from('stock_available_mv', 'sa');
        $validayteStockAvailableMv->where(' sa.id_product = ' . pSQL($id_product));
        $resultValidayteStockAvailableMv = Db::getInstance()->executeS($validayteStockAvailableMv);
        return $resultValidayteStockAvailableMv;
    }
    
    /**
     * Trae las ordenes desde la mas nueva a la mas vieja para realizar el descuento del ICR
     * @return array Retorna las ordenes con sus productos para descontar los ICR
     */
    public function getOrdersDiscountIcr() {
        $ordersDiscount = "SELECT rp.id_order, rp.id_product, rp.id_reserve, od.product_quantity AS solicitados, rp.quantity_reserve AS warehouse_quantity, 
                            rp.missing_quantity AS faltantes, hr.quantity_icrs 
                            FROM " . _DB_PREFIX_ . "orders AS o 
                            INNER JOIN " . _DB_PREFIX_ . "address AS a ON ( o.id_address_delivery = a.id_address ) 
                            INNER JOIN " . _DB_PREFIX_ . "order_detail AS od ON ( o.id_order = od.id_order) 
                            INNER JOIN " . _DB_PREFIX_ . "product AS p ON ( p.id_product = od.product_id) 
                            LEFT JOIN " . _DB_PREFIX_ . "manufacturer AS m ON ( m.id_manufacturer = p.id_manufacturer) 
                            INNER JOIN " . _DB_PREFIX_ . "stock_available_mv AS sam ON ( sam.id_product = p.id_product ) 
                            INNER JOIN " . _DB_PREFIX_ . "reserve_product AS rp ON (rp.id_order = o.id_order AND rp.id_product = p.id_product) 
                            INNER JOIN " . _DB_PREFIX_ . "history_reserve AS hr ON (hr.id_order = o.id_order)
                            WHERE current_state IN (".Configuration::get('PS_STATES_RESERVE_HISTORY').") 
                            AND hr.quantity_icrs > 0
                            ORDER BY o.id_order DESC";
        $orders = Db::getInstance()->ExecuteS($ordersDiscount);
        
        $this->validateOrderDiscount($orders);
    }
    
    public function validateOrderDiscount($orders) {
        if(count($orders) > 0) {
            $updateQuantityOrder = "UPDATE " . _DB_PREFIX_ . "history_reserve SET "
                    . "quantity_icrs = quantity_icrs - 1 "
                    . "WHERE id_order = '".$orders[0]['id_order']."' AND id_product = '".$orders[0]['id_product']."'";
            
            DB::getInstance()->execute($updateQuantityOrder);
            
            $updateReserve = "UPDATE " . _DB_PREFIX_ . "reserve_product"
                  . " SET missing_quantity = missing_quantity + 1, quantity_reserve = quantity_reserve - 1 "
                  . "WHERE id_reserve = '".$orders[0]['id_reserve']."'";
            
            DB::getInstance()->execute($updateReserve);
            
            $this->changeCurrentStateOrder($orders[0]['id_order'], 9);
            $this->addHistoryOrder($orders[0]['id_order'], 9);
        }
    }
  
    /**
    * Se obtiene la información de todas las ordenes y sus productos faltantes por compra.
    */
    public function getOrdersToProducts() {
        $ordersSql = 'SELECT  rp.id_reserve, rp.id_order, rp.id_product
	        FROM ' . _DB_PREFIX_ . 'orders AS  o 
	        INNER JOIN ' . _DB_PREFIX_ . 'address AS a ON ( o.id_address_delivery = a.id_address )
	        INNER JOIN ' . _DB_PREFIX_ . 'order_detail AS od  ON ( o.id_order = od.id_order)
	        INNER JOIN ' . _DB_PREFIX_ . 'product AS p ON ( p.id_product = od.product_id)
	        LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer AS m ON ( m.id_manufacturer = p.id_manufacturer)
	        INNER JOIN ' . _DB_PREFIX_ . 'stock_available_mv AS sam ON ( sam.id_product = p.id_product )
                INNER JOIN ' . _DB_PREFIX_ . 'reserve_product AS rp ON (rp.id_order = o.id_order AND rp.id_product = p.id_product)
	        WHERE  current_state = 9 AND rp.missing_quantity > 0 AND rp.id_product IN (' . implode(",", $this->productos) . ')
	        ORDER BY o.id_order ASC';
        return $orders = Db::getInstance()->ExecuteS($ordersSql);
    }
    
    /**
    * Inserto el producto en la tabla stock_avialable_mv
    * @param array $valueStock
    */
    public function addProductStockMv($valueStock) {
       $sqlStock = "INSERT INTO ps_stock_available_mv (id_stock_available,id_product,id_product_attribute,id_shop,id_shop_group,quantity,depends_on_stock,out_of_stock,reserve_on_stock) "
                       . "VALUES ('".$valueStock[0]->id_product."',"
                       . "'".$valueStock[0]->id_product."',"
                       . "'".$valueStock[0]->id_product_attribute."',"
                       . "'".$valueStock[0]->id_shop."',"
                       . "'".$valueStock[0]->id_shop_group."',"
                       . "'".($valueStock[0]->quantity - 1)."',"
                       . "'".$valueStock[0]->depends_on_stock."',"
                       . "'".$valueStock[0]->out_of_stock."',"
                       . "'".$valueStock[0]->reserve_on_stock."')";
       Db::getInstance()->Execute($sqlStock);
    }
    
    /**
    * Se obtiene los productos de una orden para revisar si ya se cumplieron las cantidades y realizar la actualización 
    * del estado.
    * @param int $id_order
    */
    public function getProductsReserve($id_order) {
        
        $ordersSql = 'SELECT  rp.id_reserve, rp.id_order, rp.missing_quantity FROM ' . _DB_PREFIX_ . 'reserve_product AS rp '
                . 'WHERE  rp.id_order = '.$id_order;
        return $orders = Db::getInstance()->ExecuteS($ordersSql);
    }
    
    /**
     * Realizo el update a los productos para ir restando por cada ICR agregado
     */
    public function updateReserveProduct() {
        if(isset($_COOKIE['add_stock_mv'])) {
            $dataProductStock = json_decode($_COOKIE['add_stock_mv']);
            $validateProductStock = $this->getProductStockMv($dataProductStock[0]->id_product);
            if(count($validateProductStock) == 0) {
                $this->addProductStockMv($dataProductStock);
            }
        }
        $ordersProduct = $this->getOrdersToProducts();
      
        if(count($ordersProduct) > 0) {
            $updateReserve = "UPDATE " . _DB_PREFIX_ . "reserve_product"
                  . " SET missing_quantity = missing_quantity - 1, quantity_reserve = quantity_reserve + 1 "
                  . "WHERE id_reserve = '".$ordersProduct[0]['id_reserve']."' AND missing_quantity > 0";
            DB::getInstance()->execute($updateReserve);
            
            $historyReserve = "SELECT * FROM " . _DB_PREFIX_ . "history_reserve"
                    . " WHERE id_order = ".$ordersProduct[0]['id_order']." AND id_product = ".$ordersProduct[0]['id_product']."";
            $resultHistory = DB::getInstance()->executeS($historyReserve);
            
            if(count($resultHistory) > 0) {
                $updateQuantityOrder = "UPDATE " . _DB_PREFIX_ . "history_reserve SET "
                    . "quantity_icrs = quantity_icrs + 1 "
                    . "WHERE id_order = '".$ordersProduct[0]['id_order']."' AND id_product = '".$ordersProduct[0]['id_product']."'";
            
                DB::getInstance()->execute($updateQuantityOrder);
            } else {
                $addQuantityOrder = "INSERT INTO " . _DB_PREFIX_ . "history_reserve(id_order, id_product, quantity_icrs) "
                    . " VALUES(".$ordersProduct[0]['id_order'].", ".$ordersProduct[0]['id_product'].", 1)";
            
                DB::getInstance()->execute($addQuantityOrder);
            }
            
            $this->validateMissingProducts($ordersProduct);
        } 
    }
    
    /**
     * Valido si se cumplen las cantidades de los productos en una orden
     * @param Obj $resultOrdersBuy Objeto con la información de la orden y sus productos a validar
     */
    public function validateMissingProducts($resultOrdersBuy) {
        $resultOrdersProducts = $this->getProductsReserve($resultOrdersBuy[0]['id_order']);
        if(count($resultOrdersProducts) > 0) {
            $i = 0;
            foreach($resultOrdersProducts as $key => $value){
                if($value['missing_quantity'] == 0) {
                    $i++;
                }
            }
            
            if($i == count($resultOrdersProducts)) {
                $this->changeCurrentStateOrder($resultOrdersBuy[0]['id_order'], 3);
                $this->addHistoryOrder($resultOrdersBuy[0]['id_order'], 3);
            }
        }
    }
    
    /**
     * Verifico si la orden ya cumplio con todos los productos para cambiar el estado
     * @param Int id_order
     */
    public function changeCurrentStateOrder($id_order, $current_state) {
        $changeCurrent = "UPDATE " . _DB_PREFIX_ . "orders SET current_state = '".$current_state."' WHERE id_order = '".$id_order."'";
        DB::getInstance()->execute($changeCurrent);
    }
    
    /**
     * Agregado el historial de la orden con su estado correspondiente
     * @param type $id_order
     * @param type $id_state
     */
    public function addHistoryOrder($id_order, $id_state) {
        $addHistory = "INSERT INTO " . _DB_PREFIX_ . "order_history(id_employee, id_order, id_order_state, date_add) "
                . "VALUES(0, '".$id_order."','".$id_state."', '".date("Y-m-d H:i:s")."')";
        
        DB::getInstance()->execute($addHistory);
    }


    public function eliminarIcrProducto() {
        $id_emp = Tools::getValue(id_emp);
           $employee = new Employee($id_emp);
            Context::getContext();
            Context::getContext()->employee->id = $id_emp; 
            Context::getContext()->employee->firstname = $employee->firstname;
            Context::getContext()->employee->lastname = $employee->lastname;
            Context::getContext()->employee->id_profile = $employee->id_profile;

        $query = 'DELETE oi1 FROM `'._DB_PREFIX_.'supply_order_icr` oi1 ';
        $query .= 'INNER JOIN ( SELECT oi.id_supply_order_icr FROM ps_supply_order_icr oi
                    INNER JOIN ps_supply_order_load_icr li ON (oi.id_icr = li.id_icr)
                    INNER JOIN ps_supply_order_detail od ON (oi.id_supply_order_detail = od.id_supply_order_detail)
) oi2
WHERE oi1.id_supply_order_icr = oi2.id_supply_order_icr';

        DB::getInstance()->execute($query);
        $query2 = 'SELECT id_warehouse FROM '._DB_PREFIX_.'supply_order WHERE id_supply_order = '.Tools::getValue('id_supply_order');
        $id_warehouse = DB::getInstance()->executeS($query2);
        $id_warehouse = $id_warehouse[0]['id_warehouse'];
        $id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT');
        $usable = 1;
                            foreach ($this->productoicr as $key => $value) {
                                $warehouse = new Warehouse($id_warehouse);
                                $id_product = $key;
                                $id_product_attribute = null;
                                $quantity = count($value);
                                $stock_manager = StockManagerFactory::getManager();
                                $removed_products = $stock_manager->removeProduct($id_product, null, $warehouse, $quantity, $id_stock_mvt_reason, $usable); 
                            }
                    StockAvailable::synchronize($id_product);
    }



    // buscar el producto enviado, asociado a la orden y retornar los detalles del mismo en la orden
    public function ajaxProductoOrden($orden, $reference) {
        //echo $orden .' - '. $reference;

        $query = new DbQuery();

        $query->select('id_product, name, reference, quantity_expected, quantity_received ');

        $query->from('supply_order_detail', 'sod');

        $query->where('sod.id_supply_order = '.$orden);

        if ($reference) {
            $query->where('sod.reference = "'.$reference.'"');
        }

        if ($orden && $reference) {
            $items = Db::getInstance()->executeS($query);
        }

        if ($items) {
            $retorno = '
            <h3>Producto Seleccionado</h3>
        <table id="tablaprod"> 
                <thead>
                    <tr>
                    <th>Nombre</th>
                    <th>Referencia</th>
                    <th>Cantidad solicitada</th>
                    <th>Cantidad Recibida</th>
                    </tr>
                </thead>';

            foreach ($items as $item){
                $retorno .='<tbody> 
                    <tr>
                        <td><input type="hidden" id="prodsel" name="prodsel" value="'.$item['id_product'].'"> '. $item['name'].'</td>
                        <td>'. $item['reference'].'<input type="hidden" value="'.$item['reference'].'" id="referencepr"></td> 
                        <td>'. $item['quantity_expected'].'</td>
                        <td>'. $item['quantity_received'].'</td>
                    <tr> 
                    </tbody>';
              }

        $retorno .='</table>';
            die(($retorno));
        } else {
            die("0");
        }

    }

    
    // buscar el icr enviado y retornar true si existe o false si no esta disponible
    public function ajaxIcrAdd($icr) {

        $query = new DbQuery();
        $query->select('id_icr, cod_icr');
        $query->from('icr', 'i');
        $query->where('i.cod_icr = "'.$icr.'"');
        $query->where('i.id_estado_icr = 1 ');

        if ($icr) {
            $items = Db::getInstance()->executeS($query);
            //print_r($items);
        }

        if (isset($items) && isset($items[0]['id_icr']) &&  $items[0]['id_icr']!= '') {
            $resp=$items[0]['id_icr']."|".$items[0]['cod_icr'];
            die("$resp");
        } else {
            die('No');
        }
    }


    // buscar el producto enviado, asociado a la orden y retornar los detalles del mismo en la orden
    public function ajaxProductoOrdenDel($orden, $reference) {

        $query = new DbQuery();

        
/*

            $query = 'SELECT od.id_product, od.name, od.reference, od.quantity_expected, od.quantity_received
FROM `'._DB_PREFIX_.'supply_order_detail` od
INNER JOIN  `'._DB_PREFIX_.'supply_order_icr` oi ON (oi.id_supply_order_detail = od.id_supply_order_detail)
WHERE od.id_supply_order = '.$orden.'
AND od.reference = '.$reference.'
GROUP BY od.id_product, od.name, od.reference, od.quantity_expected, od.quantity_received';
                          
           */

                    $query->select(' od.id_product, od.name, od.reference, od.quantity_expected, od.quantity_received ');
                    $query->from('supply_order_detail', 'od');
                    $query->innerJoin('supply_order_icr', 'oi', ' oi.id_supply_order_detail = od.id_supply_order_detail ');
                    $query->innerJoin('icr', 'i', ' i.id_icr = oi.id_icr ');
                    $query->where('od.id_supply_order = '.$orden);
                    $query->where('i.id_estado_icr = 2 ');
                    $query->where(' od.reference = "'.$reference.'"' );
                    $query->groupBy(' od.id_product, od.name, od.reference, od.quantity_expected, od.quantity_received ' );


//echo $orden .' - '. $reference;
        if ($orden && $reference) {

           // echo $query->__toString();
            //$status_query = DB::getInstance()->execute($query);
            $items = Db::getInstance()->executeS($query);
            //print_r($items);
        }       

        if ($items) {
            $retorno = '
            <h3>Producto Seleccionado</h3>
        <table id="tablaprod"> 
                <thead>
                    <tr>
                    <th>Nombre</th>
                    <th>Referencia</th>
                    <th>Cantidad solicitada</th>
                    <th>Cantidad Recibida</th>
                    </tr>
                </thead>';

            foreach ($items as $item){
                $retorno .='<tbody> 
                    <tr>
                        <td><input type="hidden" id="prodsel" name="prodsel" value="'.$item['id_product'].'"> '. $item['name'].'</td>
                        <td>'. $item['reference'].'<input type="hidden" value="'.$item['reference'].'" id="referencepr"></td> 
                        <td>'. $item['quantity_expected'].'</td>
                        <td>'. $item['quantity_received'].'</td>
                    <tr> 
                    </tbody>';
              }

        $retorno .='</table>';
            die(($retorno));
        } else {
            die("0");
        }

    }


        // buscar el icr enviado y retornar true si existe o false si no esta disponible
    public function ajaxIcrDel($icr, $orden, $product) {

/*
SELECT i.id_icr, i.cod_icr FROM ps_supply_order_detail od
INNER JOIN ps_supply_order_icr oi ON (oi.id_supply_order_detail = od.id_supply_order_detail)
INNER JOIN ps_icr i ON (i.id_icr = oi.id_icr )
WHERE od.id_supply_order = 2
AND i.id_estado_icr = 2 
AND i.cod_icr = 'aaa001'
AND od.id_product = 76;
*/
        $query = new DbQuery();
        $query->select('i.id_icr, i.cod_icr');
        $query->from('supply_order_detail', 'od');
        $query->innerJoin('supply_order_icr', 'oi', 'oi.id_supply_order_detail = od.id_supply_order_detail');
        $query->innerJoin('icr', 'i', 'i.id_icr = oi.id_icr');
        $query->where('i.id_estado_icr = 2 ');
        $query->where('od.id_supply_order = '.$orden);
        $query->where('i.cod_icr = "'.$icr.'"');
        $query->where('od.id_product = '.$product);

        if ($icr && $orden && $product) {
            $items = Db::getInstance()->executeS($query);
            //print_r($items);
        }

        if (isset($items) && isset($items[0]['id_icr']) &&  $items[0]['id_icr']!= '') {
            $resp=$items[0]['id_icr']."|".$items[0]['cod_icr'];
            die("$resp");
        } else {
            die('No');
        }
    }
// proceso de salida de producto    
// Paso 1 validar cantidades  
/**
*  Retori¿na las respectivas cantidades de los productos,  que se envian en el array como parametro.
*
* @array array(ids_productos)
* @return boolean
*/
   
   public function cantidadDisponibleProductos($id_order){
 
   
 $query="select s_order_d.id_product, COUNT(s_order_d.id_product) as total from ps_supply_order_detail s_order_d 
INNER JOIN ps_supply_order_icr s_order_i ON(s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
where s_order_d.id_product IN( select order_d.product_id  from ps_orders orders
INNER JOIN ps_order_detail  order_d ON(orders.id_order=order_d.id_order)
WHERE order_d.id_order =".$id_order." ) and icr.id_estado_icr=2
GROUP BY s_order_d.id_product";
 
           if ($results = Db::getInstance()->ExecuteS($query)){
           return $results;
           }
       else {
       return false;    
       }
       
   }
   // paso 2 agrgando icrs( productos a la orden de salida) 
 /**
* Valida si un icr esta asociado a un producto y si el estado del icr es disponible (2) 
* respuesta ajax formulario aosciaci�n productos orden de salida 
* 
* @icr String(6)
* @return array
*/
    public function icrDisponible($icr)
    {
// valida si es un cod�go icr es valido
 if(strlen($icr)==6 && !preg_match('/[^A-Za-z]/', substr($icr, 0,3))  && is_numeric(substr($icr, 3,3)))
 {
   
$query="select COUNT(1) as total, icr.id_icr, icr.cod_icr,s_order_d.name,s_order_d.reference,s_order_d.id_product from ps_supply_order_detail  orderdtl 
INNER JOIN ps_supply_order_icr ordericr ON(orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail)
INNER JOIN ps_icr icr ON (ordericr.id_icr= icr.id_icr) 
INNER JOIN ps_supply_order_detail s_order_d ON (ordericr.id_supply_order_detail=s_order_d.id_supply_order_detail)
WHERE icr.cod_icr='".$icr."' AND icr.id_estado_icr=2";
        
         if ($results = Db::getInstance()->ExecuteS($query))
            foreach ($results as $row) {
             return $row;   
            }
       
 }  else {
 return null;    
 }
        
    }


    /**
     * [icrFechaVencida valida que la fecha del icr que sale no este vencida]
     * @param  [type] $icr [XXX###]
     * @return [type] mixed     [array / true]
     */
    public function icrFechaVencida($icr)
    {
        // valida si es un cod�go icr es valido
         if(strlen($icr)==6 && !preg_match('/[^A-Za-z]/', substr($icr, 0,3))  && is_numeric(substr($icr, 3,3)))
         {
           
        $query="select COUNT(1) as total, ordericr.fecha_vencimiento, icr.id_icr, icr.cod_icr,s_order_d.name,s_order_d.reference,s_order_d.id_product 
        FROM "._DB_PREFIX_."supply_order_detail  orderdtl 
        INNER JOIN "._DB_PREFIX_."supply_order_icr ordericr ON(orderdtl.id_supply_order_detail= ordericr.id_supply_order_detail)
        INNER JOIN "._DB_PREFIX_."icr icr ON (ordericr.id_icr= icr.id_icr) 
        INNER JOIN "._DB_PREFIX_."supply_order_detail s_order_d ON (ordericr.id_supply_order_detail=s_order_d.id_supply_order_detail)
        WHERE icr.cod_icr='".$icr."' AND icr.id_estado_icr=2
                AND ordericr.fecha_vencimiento <> '0000-00-00' 
                AND ordericr.fecha_vencimiento <> '1969-12-31' 
                AND STR_TO_DATE(ordericr.fecha_vencimiento, '%Y-%m-%d') < NOW()";
                
            if ($results = Db::getInstance()->ExecuteS($query)) {
                foreach ($results as $row) {
                    return $row;
                }
            }
               
         }  else {
         return null;
         }

    }


// paso 3 almcenar relacion de icr's y prodcutos relacionados a la orden de salida 
/**
* Guarda el listado de icr's relacionados a la orden de salida 
*
* @array array(icr_order_detail)
* @return boolean
*/

    public function saveIcrOrderPicking($array_icr =0,$id_order=0,$id_emp=0,$id_cart=0)
    {
        if( isset($array_icr) && !empty($array_icr) ) {
   
    Context::getContext()->employee = $employee = new Employee((int)$id_emp);

            Context::getContext()->employee->id = $id_emp; 
            Context::getContext()->employee->firstname = $employee->firstname;
            Context::getContext()->employee->lastname = $employee->lastname;
            Context::getContext()->employee->id_profile = $employee->id_profile;

 $query = 'INSERT INTO `' . _DB_PREFIX_ . 'order_picking` (`id_order_icr`, `id_order_supply_icr`, `id_order_detail`, `date`, `id_employee`)
        
';
 
 $query.="SELECT t2.id_order_icr,t2.id_order_supply_icr, t2.id_order_detail, t2.date,t2.id_employee

FROM
(
select  if(tablita.completado='no' AND tablita.disponible='si' AND tablita.permitido='si',tablita.ord_product_id,NULL) as id_product 
FROM
(SELECT  *,IF(orden.ord_total<=cargado.car_cantidad, 'si', 'no') as completado,
IF(pedido.ped_total<=bodega.bod_total, 'si', 'no') as disponible,
IF(( IF(ISNULL(cargado.car_cantidad),0,cargado.car_cantidad)+pedido.ped_total)<=orden.ord_total, 'si', 'no') as permitido

FROM
-- prudctos requeridos en la orden 
(select order_d.product_id ord_product_id,order_d.product_quantity ord_total  from ps_orders orders
INNER JOIN ps_order_detail  order_d ON(orders.id_order=order_d.id_order)
WHERE order_d.id_order =".$id_order.") as orden
LEFT JOIN
-- Productos disponibles
(
select s_order_d.id_product bod_id_product, COUNT(s_order_d.id_product) as bod_total from ps_supply_order_detail s_order_d 
INNER JOIN ps_supply_order_icr s_order_i ON(s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
where s_order_d.id_product IN( select order_d.product_id  from ps_orders orders
INNER JOIN ps_order_detail  order_d ON(orders.id_order=order_d.id_order)
WHERE order_d.id_order =".$id_order." ) and icr.id_estado_icr=2
GROUP BY s_order_d.id_product) as bodega
ON(orden.ord_product_id=bodega.bod_id_product)
-- total de productos a incertar
INNER JOIN
(select  orders_d.product_id ped_product_id, COUNT(orders_d.product_id) ped_total 
from ps_orders orders
INNER JOIN ps_order_detail orders_d ON(orders.id_order=orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON(s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
WHERE icr.id_estado_icr=2 and cod_icr in ('".implode("','",$array_icr)."')  AND orders.id_order=".$id_order."
GROUP BY orders_d.product_id) as pedido
on(bodega.bod_id_product=pedido.ped_product_id)


-- pructos en la orden
LEFT JOIN 
(SELECT  s_order_d.id_product car_id_product, COUNT(s_order_d.id_product) as car_cantidad
from ps_orders orders 
INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
WHERE icr.id_estado_icr=3 and orders.id_order=".$id_order."
GROUP BY s_order_d.id_product) as cargado
ON (orden.ord_product_id=cargado.car_id_product)) tablita
) as t1

INNER JOIN
(
select COUNT(icr.id_icr) as prod, orders_d.product_id, icr.id_icr as id_order_icr, s_order_i.id_supply_order_icr as id_order_supply_icr,orders_d.id_order_detail,NOW() as date ,".$id_emp." as id_employee  
from ps_orders orders
INNER JOIN ps_order_detail orders_d ON(orders.id_order=orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON(s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
WHERE icr.id_estado_icr=2 and cod_icr in ('".implode("','",$array_icr)."')  AND orders.id_order=".$id_order."
GROUP BY icr.id_icr
) as t2

ON(t1.id_product =t2.product_id);";
 

 if( DB::getInstance()->execute($query))
      {
        if($this->updateStausIcrsOrder($id_order))
         {
    /*
    $employee = new Employee((int)$id_emp);
    echo "<br> empleado: ";
    print_r($employee);
                Context::getContext()->employee->id = $id_emp;


    echo "<br> empleado contexto: ";
    print_r(Context::getContext()->employee);

      */    $query_prods_restar_stock = "SELECT so.id_warehouse, sod.id_product, count(sod.id_product) AS cantprods FROM ps_supply_order so 
            INNER JOIN ps_supply_order_detail sod ON ( so.id_supply_order = sod.id_supply_order )
            INNER JOIN ps_supply_order_icr soi ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
            INNER JOIN ps_order_picking op ON ( op.id_order_supply_icr = soi.id_supply_order_icr )
            INNER JOIN ps_icr i ON ( i.id_icr = soi.id_icr AND i.id_icr = op.id_order_icr )
            WHERE  i.cod_icr IN ('".implode("','",$array_icr)."')
            AND i.id_estado_icr = 3 
            GROUP BY so.id_warehouse, sod.id_product"; 
            $id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT');
            $usable = 1;
            if ($res_query_prods_restar_stock = Db::getInstance()->ExecuteS($query_prods_restar_stock))
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
                            $this->errors[] = sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d out of %d.'), (int)$quantity, (int)$usable_quantity_in_stock);
                        else if ($not_usable_quantity < $quantity)
                            $this->errors[] = sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d out of %d.'), (int)$quantity, (int)$not_usable_quantity);
                        else
                            $this->errors[] = Tools::displayError('It is not possible to remove the specified quantity. Therefore no stock was removed.');
                    }

                }
     
                $query_2 = "UPDATE  ps_stock_mvt sm INNER JOIN ps_employee e ON (e.id_employee = sm.id_employee)
                SET sm.employee_firstname = e.firstname,
                    sm.employee_lastname = e.lastname
                WHERE sm.employee_firstname = '' AND sm.employee_lastname = ''
                AND sm.id_employee = ".$id_emp;

                $results = Db::getInstance()->ExecuteS($query_2);


          return true; 
         }
      } 
       return false;  
    }
    }
        /**
* actualiza el estado de los icr asociados a la orden de salida
*
* @array array_icr
* @return boolean
*/
    public function updateStausIcrsOrder($id_order)
    {
     $query = "UPDATE ps_icr icrU 
INNER JOIN
(
SELECT  icr.cod_icr
from ps_orders orders 
INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
WHERE icr.id_estado_icr=2 and orders.id_order=".$id_order."
) as actualizar
ON(icrU.cod_icr=actualizar.cod_icr)

SET icrU.id_estado_icr=3";

   if (DB::getInstance()->execute($query)) {

           return true;
         }        
       return false; 
    }
    
// Orden de Salida   
 /**
* Lista los productos por id, nombre, subtotal, cantidad
*
*|2979,	GSK - DOLEX - 200 TABLETAS,	657727.300000,	2| 
* @number 
* @return Array
*/
   public function totalOrderDetail($id_orders)
   {
$query="SELECT  s_order_d.id_product, orders_d.product_name,SUM(s_order_d.price_te) as total, COUNT(s_order_d.id_product) as cantidad
from ps_orders orders 
INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
WHERE icr.id_estado_icr=3 and orders.id_order=".$id_orders." 
GROUP BY s_order_d.id_product;";
       
            if ($results = Db::getInstance()->ExecuteS($query)){
           return $results;
        }  else {
  return false;    
  }    
   }
   
   // Orden de Salida   
 /**
* Lista los productos por id y cantiad, de la orden actual
*
*|id,cantidad| 
* @number 
* @return Array
*/
   public function contarProductosOrdenSalida($id_orders)
   {
     $query="SELECT  s_order_d.id_product, COUNT(s_order_d.id_product) as cantidad
from ps_orders orders 
INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
WHERE icr.id_estado_icr=3 and orders.id_order=".$id_orders." 
GROUP BY s_order_d.id_product;";  
   
     if ($results = Db::getInstance()->ExecuteS($query)){
           return $results;
        }  else {
  return false;    
  }    
  
   }
   
   
   // Comprobante de salida   
 /**
* Lista de prooductos con icrs asociados
*
* @id_oderders  id de la orden de salida
* @return Array
*/
   
   public  function comprobanteSalida($id_oderders)
   {
    $query="SELECT s_order_d.id_product,s_order_d.reference, orders_d.product_name, icr.cod_icr
from ps_orders orders 
INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_icr)
WHERE icr.id_estado_icr=3 and orders.id_order=".$id_oderders;
    
    if ($results = Db::getInstance()->ExecuteS($query))
    {
        return $results;
    }
 else {
    return false;    
    }
    
    
   }
   
   
  /**
* Retorna el numero de productos cargados en una orden
*
* @id_order  id de la orden de salida
* @return Array que contiene id_product y cantiad por producto
*/  
   
 public function numProducts($id_order)
 {
     $query="select order_d.product_id,order_d.product_quantity  from ps_orders orders
INNER JOIN ps_order_detail  order_d ON(orders.id_order=order_d.id_order)
WHERE order_d.id_order =".$id_order;
     
      if ($results = Db::getInstance()->ExecuteS($query)){
           return $results;
        }  else {
  return false;    
  }    
     
 }
 
 
   /**
* Retorna el listado de los productos cargados en una orden de salida 
*
* @id_order  id de la orden de salida
* @return Array que contiene: nombre del producto, referencia y codígo icr
*/ 
 
 public function cargarProductosEnOrden($id_order)
 {
   $query="SELECT orders_d.product_name,product.reference,icr.cod_icr,product.id_product
from ps_orders orders 
INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
INNER JOIN ps_product product ON (s_order_d.id_product=product.id_product)
WHERE icr.id_estado_icr=3 and orders.id_order=".$id_order." 
GROUP BY icr.cod_icr";  
     
       if ($results = Db::getInstance()->ExecuteS($query)){
           return $results;
        }  else {
  return false;    
  }
 }
 /*
  * 
  */
 
 public function eliminarProductoOrdenSalida($cod_icr)
 {
  if(strlen($cod_icr)==6 && !preg_match('/[^A-Za-z]/', substr($cod_icr, 0,3))  && is_numeric(substr($cod_icr, 3,3)))
 {
     $query="SELECT @id_order_picking:=picking.id_order_picking, @id_icr:=id_icr 
from 
ps_icr icr 
INNER JOIN ps_order_picking picking ON(icr.id_icr=picking.id_order_icr) 
WHERE  icr.cod_icr='".$cod_icr."';

DELETE from ps_order_picking
WHERE id_order_picking=@id_order_picking;

update ps_icr SET id_estado_icr=2
WHERE id_icr=@id_icr;";
     
        if ($results = Db::getInstance()->ExecuteS($query)){
          
         return true;
        }  else {
        
  return false;    
  }
 }

 return false;
 }
 /*
  * Verifica si la orden esta completa
  * 
  */

public  function ordenCompleta($id_order){
    
    $query="select 'NO' in

(SELECT if(t1.car_cantidad=t2.ord_total,'SI','NO') as completo
FROM

(select order_d.product_id ord_product_id,order_d.product_quantity ord_total  from ps_orders orders
INNER JOIN ps_order_detail  order_d ON(orders.id_order=order_d.id_order)
WHERE order_d.id_order =".$id_order." and order_d.id_order> 56) as t2

LEFT JOIN
(SELECT  s_order_d.id_product car_id_product, COUNT(s_order_d.id_product) as car_cantidad
from ps_orders orders 
INNER JOIN ps_order_detail orders_d ON( orders.id_order= orders_d.id_order)
INNER JOIN ps_supply_order_detail s_order_d ON(orders_d.product_id=s_order_d.id_product)
INNER JOIN ps_supply_order_icr s_order_i ON (s_order_d.id_supply_order_detail=s_order_i.id_supply_order_detail)
INNER JOIN ps_icr icr ON (s_order_i.id_icr=icr.id_icr)
INNER JOIN ps_order_picking o_picking ON (orders_d.id_order_detail= o_picking.id_order_detail AND s_order_i.id_supply_order_icr =o_picking.id_order_supply_icr)
WHERE icr.id_estado_icr=3 and orders.id_order=".$id_order." and orders.id_order>56
GROUP BY s_order_d.id_product) as t1

ON(t1.car_id_product=t2.ord_product_id)) as incompleta";
    
          if ($results = Db::getInstance()->ExecuteS($query)){
          if($results[0]['incompleta']==0)
         return true;
        }  else {
        
  return false;    
  }
}
 //$this->context->smarty->register_function('date_now', 'print_current_date');
//$smarty->register_function('date_now', 'print_current_date');



 
}
