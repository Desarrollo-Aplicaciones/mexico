<?php
/*
**Autor: Esteban Rincón
**Para: Farmalisto
*/
class Icrall extends IcrallCore {


	    public function icrDel($order) {

        $query_prods_restar_stock = "SELECT so.id_warehouse, sod.id_product, count(sod.id_product) AS cantprods FROM ps_supply_order so 
            INNER JOIN ps_supply_order_detail sod ON ( so.id_supply_order = sod.id_supply_order )
            INNER JOIN ps_supply_order_icr soi ON ( soi.id_supply_order_detail = sod.id_supply_order_detail )
            INNER JOIN ps_icr i ON ( i.id_icr = soi.id_icr)
            WHERE  so.id_supply_order=".$order."
            GROUP BY so.id_warehouse, sod.id_product"; 
            $id_stock_mvt_reason = Configuration::get('PS_STOCK_MVT_REASON_DEFAULT');
            $usable = 1;
            if ($res_query_prods_restar_stock = Db::getInstance()->ExecuteS($query_prods_restar_stock))
                foreach ($res_query_prods_restar_stock as $row_res_stock) {
                

                    $warehouse = new Warehouse($row_res_stock['id_warehouse']);

                    $id_product = $row_res_stock['id_product'];
                    $id_product_attribute = null;
                    $quantity = $row_res_stock['cantprods'];

                    $stock_manager = StockManagerFactory::getManager();
                    
                    $removed_products = $stock_manager->removeProduct($row_res_stock['id_product'], null, $warehouse, $row_res_stock['cantprods'], $id_stock_mvt_reason, $usable); 

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

        $sql="UPDATE ps_icr AS icr
              INNER JOIN ps_supply_order_icr AS soi
              ON icr.id_icr = soi.id_icr
              INNER JOIN ps_supply_order_detail AS sod
              ON sod.id_supply_order_detail = soi.id_supply_order_detail
              SET icr.id_estado_icr = 1
              WHERE sod.id_supply_order = ".$order;
        $sql2="DELETE soi.*
              FROM ps_supply_order_icr soi
              INNER JOIN ps_supply_order_detail sod
              ON sod.id_supply_order_detail = soi.id_supply_order_detail
              WHERE sod.id_supply_order = ".$order;
        if ((DB::getInstance()->execute($sql)) && (DB::getInstance()->execute($sql2)))
            {
                return false;
            }
        else {
            return true;
        }
    }
}