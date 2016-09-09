<?php  
  
class CartRule extends CartRuleCore {  


    /**
     * @static
     * @param $id_lang
     * @param $id_customer
     * @param bool $active
     * @param bool $includeGeneric
     * @param bool $inStock
     * @param Cart|null $cart
     * @return array
     */
    public static function getCustomerCartRules($id_lang, $id_customer, $active = false, $includeGeneric = true, $inStock = false, Cart $cart = null)
    {
        if (!CartRule::isFeatureActive())
            return array();

        if (isset(Context::getContext()->cart)) {
             $context_cart=Context::getContext()->cart->getProducts();
        } else {
            return false;
        }
        
        $prods_rules_to_find = '';
        $prods_tables_join = '';
        $limit_search = " LIMIT 1 ";
        $group_search = "";
        if($context_cart) 
        {   
        $prods_tables_join = " INNER JOIN `"._DB_PREFIX_."cart_rule_product_rule_group` crprg ON (crprg.id_cart_rule = cr.id_cart_rule) 
        INNER JOIN `"._DB_PREFIX_."cart_rule_product_rule` crpr ON (crpr.id_product_rule_group = crprg.id_product_rule_group) 
        INNER JOIN `"._DB_PREFIX_."cart_rule_product_rule_value` crprv ON (crprv.id_product_rule = crpr.id_product_rule) ";

        $prods_rules_to_find .= " AND crprv.id_item IN ( ";

            foreach ($context_cart as $key => $value) {
                $prods_rules_to_find .= $value['id_product'].",";
            }

        $prods_rules_to_find = rtrim($prods_rules_to_find, ",");
        $prods_rules_to_find .= " ) ";
        $limit_search = "";
        $group_search = " GROUP BY cr.`id_cart_rule` ";
        }

        $query_voucher = '
        SELECT *
        FROM `'._DB_PREFIX_.'cart_rule` cr
        LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (cr.`id_cart_rule` = crl.`id_cart_rule` AND crl.`id_lang` = '.(int)$id_lang.')
        '.$prods_tables_join.'
        WHERE (
            cr.`id_customer` = '.(int)$id_customer.' OR cr.group_restriction = 1
            '.($includeGeneric ? 'OR cr.`id_customer` = 0' : '').'
        )
        AND cr.date_from < "'.date('Y-m-d H:i:s').'"
        AND cr.date_to > "'.date('Y-m-d H:i:s').'"
        '.$prods_rules_to_find .'
        '.($active ? 'AND cr.`active` = 1' : '').'
        '.($inStock ? 'AND cr.`quantity` > 0 ' : ' ').$group_search.$limit_search;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query_voucher);

        // Remove cart rule that does not match the customer groups
        $customerGroups = Customer::getGroupsStatic($id_customer);
        foreach ($result as $key => $cart_rule)
            if ($cart_rule['group_restriction'])
            {
                $cartRuleGroups = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'cart_rule_group WHERE id_cart_rule = '.(int)$cart_rule['id_cart_rule']);
                foreach ($cartRuleGroups as $cartRuleGroup)
                    if (in_array($cartRuleGroup['id_group'], $customerGroups))
                        continue 2;

                unset($result[$key]);
            }

        foreach ($result as &$cart_rule)
            if ($cart_rule['quantity_per_user'])
            {
                $quantity_used = Order::getDiscountsCustomer((int)$id_customer, (int)$cart_rule['id_cart_rule']);
                if (isset($cart) && isset($cart->id))
                    $quantity_used += $cart->getDiscountsCustomer((int)$cart_rule['id_cart_rule']);
                $cart_rule['quantity_for_user'] = $cart_rule['quantity_per_user'] - $quantity_used;
            }
            else
                $cart_rule['quantity_for_user'] = 0;

        // Retrocompatibility with 1.4 discounts
        foreach ($result as &$cart_rule)
        {
            $cart_rule['value'] = 0;
            $cart_rule['minimal'] = $cart_rule['minimum_amount'];
            $cart_rule['cumulable'] = !$cart_rule['cart_rule_restriction'];
            $cart_rule['id_discount_type'] = false;
            if ($cart_rule['free_shipping'])
                $cart_rule['id_discount_type'] = Discount::FREE_SHIPPING;
            elseif ($cart_rule['reduction_percent'] > 0)
            {
                $cart_rule['id_discount_type'] = Discount::PERCENT;
                $cart_rule['value'] = $cart_rule['reduction_percent'];
            }
            elseif ($cart_rule['reduction_amount'] > 0)
            {
                $cart_rule['id_discount_type'] = Discount::AMOUNT;
                $cart_rule['value'] = $cart_rule['reduction_amount'];
            }
        }

        return $result;
    }


  
    protected function getCartRuleCombinations()  
    {  
        $array = array();  
       
        $rule_cupon_1='  
        SELECT cr.*, crl.*, 1 as selected  
        FROM '._DB_PREFIX_.'cart_rule cr  
        LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')  
        WHERE cr.id_cart_rule != '.(int)$this->id.'  
        AND (  
            cr.cart_rule_restriction = 0  
            OR cr.id_cart_rule IN (  
            SELECT id_cart_rule_1 AS id_cart_rule FROM '._DB_PREFIX_.'cart_rule_combination WHERE '.(int)$this->id.' = id_cart_rule_2  
            UNION  
            SELECT id_cart_rule_2 AS id_cart_rule FROM '._DB_PREFIX_.'cart_rule_combination WHERE '.(int)$this->id.' = id_cart_rule_1  
            )  
        )';

        //$array['selected'] = Db::getInstance()->executeS($rule_cupon_1);  

        $rule_cupon_2='  
        SELECT cr.*, crl.*, 1 as selected  
        FROM '._DB_PREFIX_.'cart_rule cr  
        LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)Context::getContext()->language->id.')  
        WHERE cr.cart_rule_restriction = 1  
        AND cr.id_cart_rule != '.(int)$this->id.'  
        AND cr.id_cart_rule NOT IN (  
            SELECT id_cart_rule_1 AS id_cart_rule FROM '._DB_PREFIX_.'cart_rule_combination WHERE '.(int)$this->id.' = id_cart_rule_2  
            UNION  
            SELECT id_cart_rule_2 AS id_cart_rule FROM '._DB_PREFIX_.'cart_rule_combination WHERE '.(int)$this->id.' = id_cart_rule_1  
        )';

        //$array['unselected'] = Db::getInstance()->executeS($rule_cupon_2);  
        
        return $array;  
  
    }  


public function getAssociatedRestrictions($type, $active_only, $i18n)
    {
        $array = array('selected' => array(), 'unselected' => array());

        if (!in_array($type, array('country', 'carrier', 'group', 'cart_rule', 'shop')))
            return false;

        $shop_list = '';
        if ($type == 'shop')
        {
            $shops = Context::getContext()->employee->getAssociatedShops();
            if (count($shops))
                $shop_list = ' AND t.id_shop IN ('.implode(array_map('intval', $shops), ',').') ';
        }

        if (!Validate::isLoadedObject($this) OR $this->{$type.'_restriction'} == 0)
        {
            $_qr_cart_val='
            SELECT t.*'.($i18n ? ', tl.*' : '').', 1 as selected
            FROM `'._DB_PREFIX_.$type.'` t
            '.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
            WHERE 1
            '.($active_only ? 'AND t.active = 1' : '').'
            '.(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').'
            '.($type == 'cart_rule' ? 'AND t.id_cart_rule != '.(int)$this->id : '').
            $shop_list.
            ' ORDER BY name ASC '.($type == 'cart_rule' ? ' LIMIT 5 ' : '');

            $array['selected'] = Db::getInstance()->executeS($_qr_cart_val);
        }
        else
        {
            if ($type == 'cart_rule')
                $array = $this->getCartRuleCombinations();
            else
            {
                $resource = Db::getInstance()->query('
                SELECT t.*'.($i18n ? ', tl.*' : '').', IF(crt.id_'.$type.' IS NULL, 0, 1) as selected
                FROM `'._DB_PREFIX_.$type.'` t
                '.($i18n ? 'LEFT JOIN `'._DB_PREFIX_.$type.'_lang` tl ON (t.id_'.$type.' = tl.id_'.$type.' AND tl.id_lang = '.(int)Context::getContext()->language->id.')' : '').'
                LEFT JOIN (SELECT id_'.$type.' FROM `'._DB_PREFIX_.'cart_rule_'.$type.'` WHERE id_cart_rule = '.(int)$this->id.') crt ON t.id_'.($type == 'carrier' ? 'reference' : $type).' = crt.id_'.$type.'
                WHERE 1 '.($active_only ? ' AND t.active = 1' : '').
                $shop_list
                .(in_array($type, array('carrier', 'shop')) ? ' AND t.deleted = 0' : '').
                ' ORDER BY name ASC',
                false);
                while ($row = Db::getInstance()->nextRow($resource))
                    $array[($row['selected'] || $this->{$type.'_restriction'} == 0) ? 'selected' : 'unselected'][] = $row;
            }
        }
        return $array;
    }


    /**
     * Retrieves the id associated to the code to the given name
     *
     * @static
     * @param string $code
     * @return int|bool
     */
    public static function getIdByDoctor($iddoc)
    {
        if (!Validate::isCleanHtml($iddoc))
            return false;
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT cr.`id_cart_rule` FROM `'._DB_PREFIX_.'cart_rule` cr INNER JOIN `'._DB_PREFIX_.'medico_rule` mr ON (cr.`id_cart_rule` = mr.`id_cart_rule`) WHERE mr.`id_medico` = \''.pSQL($iddoc).'\'');
    }



    /**
     * @static
     * @param $name
     * @param $id_lang
     * @return array
     */
    public static function getCartsRuleByNameDoc($name, $id_lang, $visitador, $novalidar = 0)
    {

        $par_busca = explode(" ", $name);
        $addquery = '';
        if($visitador != '0') {
            $addquery = " INNER JOIN ps_visitador_medico vm ON (vm.id_visitador = med.id_visitador AND vm.id_empleado = ".$visitador.")";
        }

        /****** INICIO SOLO MOSTRAR VISITADORES MEDICOS  EWING   ******/

        $cupon_visitador = Configuration::get('PS_CUPONMED_ONLY_VISITADOR');

        if ( $cupon_visitador == 1 && $novalidar == 0 ) {
            $query_visitador = " AND med.id_visitador IS NOT NULL AND med.id_visitador != '' ";
        } else {
            $query_visitador = " ";
        }

        /****** FIN SOLO MOSTRAR VISITADORES MEDICOS  EWING   ******/
        
        //$query="SELECT id_medico, nombre FROM ps_medico WHERE nombre like '%" . $par_busca[0] . "%'"; 
        $query = "SELECT cr.id_cart_rule, cr.id_customer, cr.date_from, cr.date_to, cr.description, cr.quantity, cr.quantity_per_user, cr.priority, 
            cr.partial_use, CONCAT('[',GROUP_CONCAT(esm.nombre SEPARATOR \", \"),']') AS code, cr.minimum_amount, cr.minimum_amount_tax, 
            cr.minimum_amount_currency, cr.minimum_amount_shipping, cr.country_restriction, cr.carrier_restriction, cr.group_restriction, 
            cr.cart_rule_restriction, cr.product_restriction, cr.shop_restriction, cr.free_shipping, cr.reduction_percent, cr.reduction_amount, 
            cr.reduction_tax, cr.reduction_currency, cr.reduction_product, cr.gift_product, cr.gift_product_attribute, cr.highlight, cr.active, 
            cr.date_add, cr.date_upd, crl.id_cart_rule, crl.id_lang, med.nombre AS name
            FROM ps_cart_rule cr
            INNER JOIN ps_medico_rule mr ON (mr.id_cart_rule = cr.id_cart_rule)
            INNER JOIN ps_medico med ON (med.id_medico =  mr.id_medico)
            ".$addquery."
            INNER JOIN ps_medic_especialidad mes ON (med.id_medico = mes.id_medico) 
            INNER JOIN ps_especialidad_medica esm ON (esm.id_especialidad = mes.id_especialidad) 
            LEFT JOIN ps_cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ".(int)$id_lang.")
            WHERE med.nombre like '%" . $par_busca[0] . "%' ".$query_visitador;

        for ($i=1; $i < count($par_busca); $i++) {

            if (strlen($par_busca[$i])>=3) {
            $query.=" AND  med.nombre LIKE '%" . $par_busca[$i] . "%' "; 
            }
        }
        $query.=" GROUP BY med.id_medico ORDER BY med.nombre ASC LIMIT 0,10";


        return Db::getInstance()->executeS($query);
    }


    /**
     * @static
     * @param $name
     * @param $id_lang
     * @return array
     */
    public static function getCartsRuleByCedulaDoc($name, $id_lang, $visitador)
    {

        $par_busca = explode(" ", $name);
        
        $addquery = '';
        if($visitador != '0') {
            $addquery = " INNER JOIN ps_visitador_medico vm ON (vm.id_visitador = med.id_visitador AND vm.id_empleado = ".$visitador.")";
        }

        //$query="SELECT id_medico, nombre FROM ps_medico WHERE nombre like '%" . $par_busca[0] . "%'"; 
        $query = "SELECT cr.id_cart_rule, cr.id_customer, cr.date_from, cr.date_to, cr.description, cr.quantity, cr.quantity_per_user, cr.priority, 
            cr.partial_use, CONCAT('[',GROUP_CONCAT(esm.nombre SEPARATOR \", \"),']') AS code, cr.minimum_amount, cr.minimum_amount_tax, 
            cr.minimum_amount_currency, cr.minimum_amount_shipping, cr.country_restriction, cr.carrier_restriction, cr.group_restriction, 
            cr.cart_rule_restriction, cr.product_restriction, cr.shop_restriction, cr.free_shipping, cr.reduction_percent, cr.reduction_amount, 
            cr.reduction_tax, cr.reduction_currency, cr.reduction_product, cr.gift_product, cr.gift_product_attribute, cr.highlight, cr.active, 
            cr.date_add, cr.date_upd, crl.id_cart_rule, crl.id_lang, med.nombre AS name
            FROM ps_cart_rule cr
            INNER JOIN ps_medico_rule mr ON (mr.id_cart_rule = cr.id_cart_rule)
            INNER JOIN ps_medico med ON (med.id_medico =  mr.id_medico)
            ".$addquery."
            LEFT JOIN ps_medic_especialidad mes ON (med.id_medico = mes.id_medico) 
            LEFT JOIN ps_especialidad_medica esm ON (esm.id_especialidad = mes.id_especialidad) 
            LEFT JOIN ps_cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ".(int)$id_lang.")
            WHERE med.cedula like '%" . $par_busca[0] . "%'";

        $query.=" GROUP BY med.id_medico ORDER BY med.nombre ASC LIMIT 0,10";


        return Db::getInstance()->executeS($query);
    }

    /**
     * @static
     * @param $name
     * @param $id_lang
     * @return array
     */
    public static function getCartsRuleByCode($name, $id_lang)
    {

    /*
    SELECT mr.id_cart_rule
    FROM ps_cart_rule cr
    LEFT JOIN ps_cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = 1)
    INNER JOIN ps_cruzar_medcupon mc ON (mc.id_cart_rule = cr.id_cart_rule) 
    INNER JOIN ps_medico_rule mr ON (mr.id_medico = mc.id_medico)
    WHERE cr.code LIKE '%V33M888AAAC%' OR crl.name LIKE '%V33M888AAAC%'
    */
        $query_med_cupon = 'SELECT mr.id_cart_rule
            FROM '._DB_PREFIX_.'cart_rule cr
            LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
            INNER JOIN '._DB_PREFIX_.'cruzar_medcupon mc ON (mc.id_cart_rule = cr.id_cart_rule) 
            INNER JOIN '._DB_PREFIX_.'medico_rule mr ON (mr.id_medico = mc.id_medico)
            WHERE cr.code LIKE \'%'.pSQL($name).'%\' OR crl.name LIKE \'%'.pSQL($name).'%\'';

        $data = Db::getInstance()->executeS($query_med_cupon);

        if ($data) {

            return Db::getInstance()->executeS('
            SELECT cr.*, crl.*
            FROM '._DB_PREFIX_.'cart_rule cr
            LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
            WHERE cr.id_cart_rule = '.$data[0]['id_cart_rule'].'
            ');

        } else {

            return Db::getInstance()->executeS('
            SELECT cr.*, crl.*
            FROM '._DB_PREFIX_.'cart_rule cr
            LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = '.(int)$id_lang.')
            WHERE code LIKE \'%'.pSQL($name).'%\' OR name LIKE \'%'.pSQL($name).'%\'
            ');

        }
    }





     /**
     * Retrieves the id associated to the given code
     *
     * @static
     * @param string $code
     * @return int|bool
     */
    public static function getIdByCode($code)
    {
        if (!Validate::isCleanHtml($code))
            return false;
        
        $query_med_cupon = 'SELECT mr.id_cart_rule
            FROM '._DB_PREFIX_.'cart_rule cr
            INNER JOIN '._DB_PREFIX_.'cruzar_medcupon mc ON (mc.id_cart_rule = cr.id_cart_rule) 
            INNER JOIN '._DB_PREFIX_.'medico_rule mr ON (mr.id_medico = mc.id_medico)
            WHERE cr.code = \''.pSQL($code).'\'';
        
        $data = Db::getInstance()->executeS($query_med_cupon);

        if ($data) {

            return $data[0]['id_cart_rule'];

        } else {

            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `id_cart_rule` FROM `'._DB_PREFIX_.'cart_rule` WHERE `code` = \''.pSQL($code).'\'');

        }
    }            
  
}  