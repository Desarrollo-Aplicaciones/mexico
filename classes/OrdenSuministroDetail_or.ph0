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

        $query = 'INSERT INTO `'._DB_PREFIX_.'supply_order_icr` (`id_supply_order_detail`, `id_icr`, `id_employee`, `fecha`) ';
        $query .= ' SELECT sod.id_supply_order_detail, soli.id_icr, '.$this->getId_employee().', now() FROM `'._DB_PREFIX_.'supply_order_detail` sod
                    INNER JOIN `'._DB_PREFIX_.'supply_order_load_icr` soli 
                    ON (sod.id_supply_order = soli.id_supply_order AND sod.id_product = soli.id_product)
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

            $query = 'UPDATE `'._DB_PREFIX_.'supply_order_detail` od INNER JOIN ( SELECT id_supply_order, id_product, COUNT(id_product) AS cant FROM `'._DB_PREFIX_.'supply_order_load_icr` 
                WHERE id_supply_order = '.(int)$this->supply_order.'
                GROUP BY id_product ) AS ol
                ON (od.id_supply_order = ol.id_supply_order AND od.id_product = ol.id_product)
                SET od.quantity_received = od.quantity_received '.$way.' ol.cant';
                          
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

        DB::getInstance()->execute($ins_history);


    }


    public function eliminarIcrProducto() {

        $query = 'DELETE oi1 FROM `'._DB_PREFIX_.'supply_order_icr` oi1 ';
        $query .= 'INNER JOIN ( SELECT oi.id_supply_order_icr FROM ps_supply_order_icr oi
                    INNER JOIN ps_supply_order_load_icr li ON (oi.id_icr = li.id_icr)
                    INNER JOIN ps_supply_order_detail od ON (oi.id_supply_order_detail = od.id_supply_order_detail)
) oi2
WHERE oi1.id_supply_order_icr = oi2.id_supply_order_icr';

        DB::getInstance()->execute($query);
        
        //DB::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'supply_order_load_icr` ');
    }



    // buscar el producto enviado, asociado a la orden y retornar los detalles del mismo en la orden
    public function ajaxProductoOrden($orden, $reference) {
        //echo $orden .' - '. $reference;

        $query = new DbQuery();

        $query->select('id_product, name, reference, quantity_expected, quantity_received ');

        $query->from('supply_order_detail', 'sod');

        $query->where('sod.id_supply_order = '.$orden);

        if ($reference) {
            $query->where('sod.reference = '.$reference);
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



        if ($orden && $reference) {
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


}
