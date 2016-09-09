<?php
exit("no se ejecuto el archivo, borre o comente esta linea y luego de ejecutarlo, vuelva a colocarla");
require_once('../../classes/OrdenSuministroDetail.php');

$empledado = new Employee(28);
//echo $empleado = Context::getContext()->employee->id;
Context::getContext()->employee->id = $empledado->id;

            Context::getContext()->employee->firstname = $empledado->firstname;
            Context::getContext()->employee->lastname = $empledado->lastname;
            Context::getContext()->employee->id_profile = $empledado->id_profile;
            echo "---".$empledado->id_profile."---";

    echo "<br>qr: ".
            $query_prods_restar_stock = " SELECT so.id_warehouse, sod.id_product, sod.reference, count(sod.id_product) AS cantprods FROM ps_supply_order so 
            INNER JOIN ps_supply_order_detail sod ON ( so.id_supply_order = sod.id_supply_order )
            INNER JOIN ps_supply_order_icr soi ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )            
            INNER JOIN ps_icr i ON ( i.id_icr = soi.id_icr )
            INNER JOIN tmp_devover_icr ote ON ( ote.ICR = i.cod_icr AND  soi.id_supply_order_icr )
            WHERE  i.id_estado_icr = 2
            GROUP BY so.id_warehouse, sod.id_product
            ORDER BY so.id_warehouse, sod.id_product "; 

            $id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT');
            $usable = 1;
            if ($res_query_prods_restar_stock = Db::getInstance()->ExecuteS($query_prods_restar_stock)) {
                foreach ($res_query_prods_restar_stock as $row_res_stock) {

                    //for ($sbd = 1; $sbd <= $row_res_stock['cantprods'];$sbd ++) {

                    echo "<br>Bodega: ".$row_res_stock['id_warehouse']." - Producto: ".$row_res_stock['id_product']." - Ref: ".$row_res_stock['reference'];

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
                                echo " --> ".sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d out of %d.'), (int)$quantity, (int)$usable_quantity_in_stock);
                            else if ($not_usable_quantity < $quantity)
                                echo " --> ".sprintf(Tools::displayError('You don\'t have enough usable quantity. Cannot remove %d out of %d.'), (int)$quantity, (int)$not_usable_quantity);
                            else
                                echo 'It is not possible to remove the specified quantity. Therefore no stock was removed.';
                        }

                    //}
         
                   
                }

                 echo "<br>". $query_2 = "UPDATE  ps_stock_mvt sm INNER JOIN ps_employee e ON (e.id_employee = sm.id_employee)
                    SET sm.employee_firstname = e.firstname,
                        sm.employee_lastname = e.lastname
                    WHERE sm.employee_firstname = '' AND sm.employee_lastname = ''
                    AND sm.id_employee = ".$empledado->id;


                    if ( $results = Db::getInstance()->ExecuteS($query_2) ) {
                        echo "<br>Si actualizo empleado";
                        //return true;
                    } else {
                         "No se pudo reducir actualizar el empleado en el stock. Contacte a su administrador del sistema.";
                        //return false;
                    }
            }



        echo "<BR>FINALNo se pudo reducir el stock. Contacte a su administrador del sistema";
        
       
?>
