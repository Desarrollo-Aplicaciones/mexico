<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Customer
 *
 * @author German.peralta
 */
class Customer extends CustomerCore{
    
    
    public function get_id_custumer($id_address) {
        $query = "select customer.id_customer FROM
ps_address adre INNER JOIN ps_customer customer ON (adre.id_customer=customer.id_customer)
WHERE adre.id_address=" . (int) $id_address . " LIMIT 1;";

        if ($results = Db::getInstance()->ExecuteS($query)) {
            $sid_cusomer = '';
            if (count($results) > 0) {
                $sid_cusomer = $results[0]['id_customer'];

                return json_encode(array('results' => $sid_cusomer));
            }
        }
        return '!';
    } 
    
    
    public function get_id_custumer_account($billing_account_id, $json = false) {
        $query = "select custumer.id_customer
                  from ps_customer custumer INNER JOIN ps_sync_tracker track on(custumer.id_customer=track.key2)
                  WHERE track.key1='" . $billing_account_id . "' AND track.sync_module_cd='PS_CUSTOMER'
                  GROUP BY track.key1;";

        if ($results = Db::getInstance()->ExecuteS($query)) {
            $id_customer = '';
            if (count($results) > 0) {
                /* @var $json type  boolean */
                if ($json) {
                    return json_encode(array('id_customer' => $id_customer = $results[0]['id_customer']));
                } else {
                    return $id_customer = $results[0]['id_customer'];
                }
            }
        }
        return '!';
    }
    
}
