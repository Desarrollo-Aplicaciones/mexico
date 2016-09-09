<?php

include(dirname(__FILE__) . '/../../config/config.inc.php');
include(dirname(__FILE__) . '/../../init.php');

class AjaxBines extends FrontController {
    
    
    /*
     * Ajax reglas de carrito para pagon con tarje de credito
     */

    public function ajaxBin($numero_tarjeta = NULL) {
        if (isset($numero_tarjeta) && $numero_tarjeta != '') {
            $bin = substr($numero_tarjeta, 0, 6);
            if (is_numeric($bin)) {

                $date = date('Y-m-d H:i:s');

                $query = "select bin.id_cart_rule,fran.nombre as franquicia, banco.nombre as banco, bin.bin,rules.reduction_percent,banco.img_beneficio

FROM
ps_bines bin
INNER JOIN ps_franquicia fran ON(fran.id_franquicia=bin.id_franquicia )
INNER JOIN ps_banco banco ON(banco.id_banco=bin.id_banco)
INNER JOIN ps_cart_rule rules ON(bin.id_cart_rule=rules.id_cart_rule)
WHERE bin.bin= " . (int) $bin . " AND rules.active=1 AND '" . $date . "' BETWEEN rules.date_from  AND rules.date_to
GROUP BY bin.bin;";

                //return $query;     
                if ($results = Db::getInstance()->ExecuteS($query)) {

                    if (count($results) > 0) {

                        return $results[0]['reduction_percent']. '|' . $results[0]['img_beneficio'];
                    }
                }
            }
        }
        // return $bin;
        return '0';
    }
/*
 * Ajax reglas de carrito para pago con PSE
 */
    public function ajaxBinPse($codigo_banco = NULL) {
        if (isset($codigo_banco) && $codigo_banco != '') {

            if (is_numeric($codigo_banco)) {

                $date = date('Y-m-d H:i:s');

                $query = "select pse.id_cart_rule, banco.nombre, banco.img_beneficio,rule.reduction_percent
from
ps_banco banco INNER JOIN ps_pse_cart_rule pse ON(banco.id_banco=pse.id_banco)
INNER JOIN ps_cart_rule rule ON(pse.id_cart_rule=rule.id_cart_rule)
WHERE banco.codigo= " . (int) $codigo_banco . " AND rule.active=1 AND '" . $date . "' BETWEEN rule.date_from  AND rule.date_to;";

                //return $query;     
                if ($results = Db::getInstance()->ExecuteS($query)) {

                    if (count($results) > 0) {

                        //echo '<pre>';
                        //print_r($query);
                        // exit();
                        return $results[0]['reduction_percent'] . '|' . $results[0]['img_beneficio'];
                    }
                }
            }
        }
        // return $bin;
        return '0';
    }

}

if (isset($_POST['numerot']) && $_POST['numerot'] != '' && isset($_POST['accion']) && $_POST['accion'] === 'ajax_bin') {
    $numero_tarjeta = trim($_POST['numerot']);
    $ajax_obj = new AjaxBines();
    echo $ajax_obj->ajaxBin($numero_tarjeta);
} 
elseif (isset($_POST['entidad']) && $_POST['entidad'] != '' && isset($_POST['accion']) && $_POST['accion'] === 'ajax_bin_pse') {

    $codigo_banco = trim($_POST['entidad']);
    $ajax_obj = new AjaxBines();
    echo $ajax_obj->ajaxBinPse($codigo_banco);
}else
{
echo '0';
}