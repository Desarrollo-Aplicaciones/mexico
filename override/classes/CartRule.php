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
     * @static
     * @param $name
     * @param $id_lang
     * @return array
     */
    public static function getIdDocByNameDoc($name, $id_lang, $visitador)
    {

        $par_busca = explode(" ", $name);

        //$query="SELECT id_medico, nombre FROM ps_medico WHERE nombre like '%" . $par_busca[0] . "%'"; 
        $query = "SELECT med.id_medico, CONCAT('[',GROUP_CONCAT(esm.nombre SEPARATOR \", \"),']') AS code, med.nombre AS name
            FROM ps_cart_rule cr
            INNER JOIN ps_medico_rule mr ON (mr.id_cart_rule = cr.id_cart_rule)
            INNER JOIN ps_medico med ON (med.id_medico =  mr.id_medico)
            INNER JOIN ps_medic_especialidad mes ON (med.id_medico = mes.id_medico) 
            INNER JOIN ps_especialidad_medica esm ON (esm.id_especialidad = mes.id_especialidad) 
            LEFT JOIN ps_cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ".(int)$id_lang.")
            WHERE med.nombre like '%" . $par_busca[0] . "%'";

        for ($i=1; $i < count($par_busca); $i++) {

            if (strlen($par_busca[$i])>=3) {
            $query.=" AND  med.nombre LIKE '%" . $par_busca[$i] . "%' "; 
            }
        }
        $query.=" GROUP BY med.id_medico ORDER BY med.nombre ASC LIMIT 0,10";


        return Db::getInstance()->executeS($query);
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
            cr.date_add, cr.date_upd, crl.id_cart_rule, crl.id_lang, med.nombre AS name, med.id_medico
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


    public function getContextualValue($use_tax, Context $context = null, $filter = null, $package = null, $use_cache = true)
    {
        if (!CartRule::isFeatureActive())
            
            return 0;
        if (!$context)
            $context = Context::getContext();
        
            
        if (!$filter)
            $filter = CartRule::FILTER_ACTION_ALL;

            
        
        $all_products = $context->cart->getProducts();
        $package_products = (is_null($package) ? $all_products : $package['products']);

        $reduction_value = 0;

        $cache_id = 'getContextualValue_'.(int)$this->id.'_'.(int)$use_tax.'_'.(int)$context->cart->id.'_'.(int)$filter;
        foreach ($package_products as $product)
            $cache_id .= '_'.(int)$product['id_product'].'_'.(int)$product['id_product_attribute'];

        if (Cache::isStored($cache_id))
            return Cache::retrieve($cache_id);

        /* SE INHABILITA PARA QUE NO SUME EL COSTO DEL ENVIO COMO DESCUENTO, CUANDO EL CUPON CONTIENE FREE SHIPPING
        // Free shipping on selected carriers
        if ($this->free_shipping && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_SHIPPING)))
        {
            if (!$this->carrier_restriction) {
                $reduction_value += $context->cart->getOrderTotal($use_tax, Cart::ONLY_SHIPPING, is_null($package) ? null : $package['products'], is_null($package) ? null : $package['id_carrier']);
            }
            else
            {
                $data = Db::getInstance()->executeS('
                    SELECT crc.id_cart_rule, c.id_carrier
                    FROM '._DB_PREFIX_.'cart_rule_carrier crc
                    INNER JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crc.id_carrier AND c.deleted = 0)                  
                    WHERE crc.id_cart_rule = '.(int)$this->id.'
                    AND c.id_carrier = '.(int)$context->cart->id_carrier);

                if ($data) {
                    foreach ($data as $cart_rule) {
                        $reduction_value += $context->cart->getCarrierCost((int)$cart_rule['id_carrier'], $use_tax, $context->country);
                    }
                }
            }
        }*/

        if (in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_REDUCTION))) {  

            // DESCUENTO (%) EN TODA LA ORDEN
            // Discount (%) on the whole order
            if ($this->reduction_percent && $this->reduction_product == 0)
            {
                // SE INHABILITA PORQUE SE REALIZAN MAL LOS CALCULOS TOTALES CUANDO LA ORDEN CONTIENE UN CUPON DE DESCUENTO POR PORCENTAJE
                // Do not give a reduction on free products!
                /*$order_total = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package_products);

                foreach ($context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $cart_rule)
                    $order_total -= Tools::ps_round($cart_rule['obj']->getContextualValue($use_tax, $context, CartRule::FILTER_ACTION_GIFT, $package), 2);

                $reduction_value += $order_total * $this->reduction_percent / 100;*/

                foreach ($package_products as $product){
                    $reduction_value += $context->cart->UnitPriceDiscountPercent( $product['price'],  $product['rate'], $this->reduction_percent, false, $product['cart_quantity'], true);
                }
            }

            // DESCUENTO (%) EN PRODUCTO ESPECIFICO
            // Discount (%) on a specific product
            if ($this->reduction_percent && $this->reduction_percent != '0.00' && $this->reduction_product > 0)
            {
                foreach ($package_products as $product)
                    if ($product['id_product'] == $this->reduction_product)
                        //$reduction_value += ($use_tax ? $product['total_wt'] : $product['total']) * $this->reduction_percent / 100;
                        $reduction_value += $context->cart->UnitPriceDiscountPercent( $product['price'],  $product['rate'], $this->reduction_percent, false, (int)$product['cart_quantity'], true);
            }

            // DESCUENTO (%) EN EL PRODUCTO MAS BARATO
            // Discount (%) on the cheapest product
            if ($this->reduction_percent && $this->reduction_product == -1)
            {
                $minPrice = false;
                $cheapest_product = null;
                foreach ($all_products as $product)
                {
                    $price = ($use_tax ? $product['price_wt'] : $product['price']);
                    if ($price > 0 && ($minPrice === false || $minPrice > $price))
                    {
                        $minPrice = $price;
                        $cheapest_product = $product['id_product'].'-'.$product['id_product_attribute'];
                    }
                }
                
                // Check if the cheapest product is in the package
                $in_package = false;
                foreach ($package_products as $product)
                    if ($product['id_product'].'-'.$product['id_product_attribute'] == $cheapest_product || $product['id_product'].'-0' == $cheapest_product)
                        $in_package = true;
                if ($in_package)
                    $reduction_value += $minPrice * $this->reduction_percent / 100;
            }

            // DESCUENTO (%) EN PRODUCTO SELECCIONADOS
            // Discount (%) on the selection of products
            if ($this->reduction_percent && $this->reduction_product == -2)
            {
                $selected_products_reduction = 0;
                $selected_products = $this->checkProductRestrictions($context, true);
                if (is_array($selected_products))
                    foreach ($package_products as $product)
                        if (in_array($product['id_product'].'-'.$product['id_product_attribute'], $selected_products)
                            || in_array($product['id_product'].'-0', $selected_products))
                        {
                            $price = ($use_tax ? $product['price_wt'] : $product['price']);
                            $selected_products_reduction += $price * $product['cart_quantity'];
                        }
                $reduction_value += $selected_products_reduction * $this->reduction_percent / 100;
            }

            // DESCUENTO MONETARIO
            // Discount (¤)
            if ($this->reduction_amount)
            {
                $prorata = 1;
                if (!is_null($package) && count($all_products))
                {
                    $total_products = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                    if ($total_products){
                        $prorata = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS, $package['products']) / $total_products;
                    }
                }

                $reduction_amount = $this->reduction_amount;
                // If we need to convert the voucher value to the cart currency
                if ($this->reduction_currency != $context->currency->id)
                {
                    $voucherCurrency = new Currency($this->reduction_currency);

                    // First we convert the voucher value to the default currency
                    if ($reduction_amount == 0 || $voucherCurrency->conversion_rate == 0)
                        $reduction_amount = 0;
                    else
                        $reduction_amount /= $voucherCurrency->conversion_rate;

                    // Then we convert the voucher value in the default currency into the cart currency
                    $reduction_amount *= $context->currency->conversion_rate;
                    $reduction_amount = Tools::ps_round($reduction_amount);
                }

                // If it has the same tax application that you need, then it's the right value, whatever the product!
                if ($this->reduction_tax == $use_tax)
                {
                    // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                    if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP)
                    {
                        $cart_amount = $context->cart->getOrderTotal($use_tax, Cart::ONLY_PRODUCTS);
                        $reduction_amount = min($reduction_amount, $cart_amount);
                    }
                    $reduction_value += $prorata * $reduction_amount;
                }
                else
                {
                    if ($this->reduction_product > 0)
                    {
                        foreach ($context->cart->getProducts() as $product)
                            if ($product['id_product'] == $this->reduction_product)
                            {
                                $product_price_ti = $product['price_wt'];
                                $product_price_te = $product['price'];
                                $product_vat_amount = $product_price_ti - $product_price_te;

                                if ($product_vat_amount == 0 || $product_price_te == 0) {
                                    $product_vat_rate = 0;
                                } else {
                                    $product_vat_rate = $product_vat_amount / $product_price_te;
                                }

                                if ($this->reduction_tax && !$use_tax)
                                    $reduction_value += $prorata * $reduction_amount / (1 + $product_vat_rate);
                                elseif (!$this->reduction_tax && $use_tax)
                                    $reduction_value += $prorata * $reduction_amount /* (1 + $product_vat_rate)*/;
                            }
                    }
                    // DESCUENTO A TODA LA ORDEN MONETARIO
                    // Discount (¤) on the whole order
                    elseif ($this->reduction_product == 0)
                    {
                        $cart_amount_ti = $context->cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
                        $cart_amount_te = $context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
                        
                        // The reduction cannot exceed the products total, except when we do not want it to be limited (for the partial use calculation)
                        if ($filter != CartRule::FILTER_ACTION_ALL_NOCAP)
                            $reduction_amount = min($reduction_amount, $this->reduction_tax ? $cart_amount_ti : $cart_amount_te);

                        $cart_vat_amount = $cart_amount_ti - $cart_amount_te;

                        if ($cart_vat_amount == 0 || $cart_amount_te == 0)
                            $cart_average_vat_rate = 0;
                        else
                            $cart_average_vat_rate = Tools::ps_round($cart_vat_amount / $cart_amount_te, 3);

                        if ($this->reduction_tax && !$use_tax)
                            $reduction_value += $prorata * $reduction_amount / (1 + $cart_average_vat_rate);
                        elseif (!$this->reduction_tax && $use_tax)
                            $reduction_value += $prorata * $reduction_amount * (1 + $cart_average_vat_rate);

                        $totalPriceIniProducts = 0;
                        foreach ($context->cart->getProducts() as $product) {
                            $totalPriceIniProducts += $product['price'] * (int)$product['cart_quantity'];
                        }

                        // si el descuento del cupon monetario es mayor al descuento posible, se toma como descuento el valor del descuento posible y no el descuento del cupon
                        // el descuento posible, es el acumulado de los precios de los productos (iva excl.) del carrito
                        if ( $totalPriceIniProducts < $reduction_amount ) {
                            $reduction_value = $totalPriceIniProducts;
                        }
                    }
                    /*
                     * Reduction on the cheapest or on the selection is not really meaningful and has been disabled in the backend
                     * Please keep this code, so it won't be considered as a bug
                     * elseif ($this->reduction_product == -1)
                     * elseif ($this->reduction_product == -2)
                    */
                }
            }
        }

        // DESCUENTO PRODUCTO GRATIS
        // Free gift
        if ((int)$this->gift_product && in_array($filter, array(CartRule::FILTER_ACTION_ALL, CartRule::FILTER_ACTION_ALL_NOCAP, CartRule::FILTER_ACTION_GIFT)))
        {
            $id_address = (is_null($package) ? 0 : $package['id_address']);
            foreach ($package_products as $product)
                if ($product['id_product'] == $this->gift_product && ($product['id_product_attribute'] == $this->gift_product_attribute || !(int)$this->gift_product_attribute))
                {
                    // The free gift coupon must be applied to one product only (needed for multi-shipping which manage multiple product lists)
                    if (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product])
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == $id_address
                        || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0
                        || $id_address == 0
                        || !$use_cache)
                    {
                        $reduction_value += ($use_tax ? $product['price_wt'] : $product['price']);
                        if ($use_cache && (!isset(CartRule::$only_one_gift[$this->id.'-'.$this->gift_product]) || CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] == 0))
                            CartRule::$only_one_gift[$this->id.'-'.$this->gift_product] = $id_address;
                        break;
                    }
                }
        }

        Cache::store($cache_id, $reduction_value);
        return $reduction_value;
    }

    /**
     * [getCartRuleDetail Para consultar los detalles de la regla de carrito que se esta agregando]
     * @param  [string] $codeRule [codigo del cupon a agregar]
     * @return [bool] $dataDetail [true si la regla de carrito es 0]
     */
    public static function getCartRuleDetail( $codeRule )
    {
        $ruleIs0 = false;

        $queryDetailRule = "SELECT cr.id_cart_rule, cr.reduction_percent, cr.reduction_amount, cr.free_shipping
                            FROM "._DB_PREFIX_."cart_rule cr
                            LEFT JOIN "._DB_PREFIX_."cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND cr.active = 1 AND crl.id_lang = 1)
                            WHERE code = '". $codeRule."'";

        $dataDetail = Db::getInstance()->executeS( $queryDetailRule );

        if ( $dataDetail[0]['reduction_percent'] == 0 && $dataDetail[0]['reduction_amount'] == 0 ) {
            $ruleIs0 = true;
        }

        return $ruleIs0;
    }

    public static function copyConditions($id_cart_rule_source, $id_cart_rule_destination)
    {
        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'cart_rule_shop` (`id_cart_rule`, `id_shop`)
        (SELECT '.(int)$id_cart_rule_destination.', id_shop FROM `'._DB_PREFIX_.'cart_rule_shop` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'cart_rule_carrier` (`id_cart_rule`, `id_carrier`)
        (SELECT '.(int)$id_cart_rule_destination.', id_carrier FROM `'._DB_PREFIX_.'cart_rule_carrier` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'cart_rule_group` (`id_cart_rule`, `id_group`)
        (SELECT '.(int)$id_cart_rule_destination.', id_group FROM `'._DB_PREFIX_.'cart_rule_group` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'cart_rule_country` (`id_cart_rule`, `id_country`)
        (SELECT '.(int)$id_cart_rule_destination.', id_country FROM `'._DB_PREFIX_.'cart_rule_country` WHERE `id_cart_rule` = '.(int)$id_cart_rule_source.')');
        /*Db::getInstance()->execute('
        INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`)
        (SELECT '.(int)$id_cart_rule_destination.', IF(id_cart_rule_1 != '.(int)$id_cart_rule_source.', id_cart_rule_1, id_cart_rule_2) FROM `'._DB_PREFIX_.'cart_rule_combination`
        WHERE `id_cart_rule_1` = '.(int)$id_cart_rule_source.' OR `id_cart_rule_2` = '.(int)$id_cart_rule_source.')');*/

        // Todo : should be changed soon, be must be copied too
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule` WHERE `id_cart_rule` = '.(int)$this->id);
        // Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'cart_rule_product_rule_value` WHERE `id_product_rule` NOT IN (SELECT `id_product_rule` FROM `'._DB_PREFIX_.'cart_rule_product_rule`)');
    }

    
    
     /**
     * [getCartRulesByNameLang devueve informacion de las reglas respecto al texto hallado en el lang del cupon y id_cliente si se ingresa ]
     * @param  [type] $lang_cart_rule [description]
     * @param  string $id_customer    [description]
     * @return [type]                 [description]
     */
//    Deberia traer las reglas de carrito que tine e el carrito actualmente..
    public static function getCartRulesByNameLang( $lang_cart_rule, $id_lang, $id_customer = '') {

        $query = "SELECT cr.id_customer, cr.date_from, cr.date_to, cr.description, cr.quantity, cr.quantity_per_user, cr.priority, 
            cr.partial_use, cr.minimum_amount, cr.minimum_amount_tax, 
            cr.minimum_amount_currency, cr.minimum_amount_shipping, cr.country_restriction, cr.carrier_restriction, cr.group_restriction, 
            cr.cart_rule_restriction, cr.product_restriction, cr.shop_restriction, cr.free_shipping, cr.reduction_percent, cr.reduction_amount, 
            cr.reduction_tax, cr.reduction_currency, cr.reduction_product, cr.gift_product, cr.active, 
            cr.date_add, cr.date_upd, crl.id_cart_rule, crl.id_lang
            FROM "._DB_PREFIX_."cart_rule cr
            LEFT JOIN "._DB_PREFIX_."cart_rule_lang crl ON (cr.id_cart_rule = crl.id_cart_rule AND crl.id_lang = ".(int)$id_lang.")
            LEFT JOIN "._DB_PREFIX_."order_cart_rule ocr ON ( cr.id_cart_rule = ocr.id_cart_rule ) ";
            $customer_add = '';
            
        error_log("\n\n\n\nPrimer Query: \n\n".$query, 3, "/var/www/errors.log");

        if ( $id_customer != '' ) {
            $customer_add = " cr.id_customer = ".$id_customer." AND " ;
        }
            $query .= " WHERE ".$customer_add." ocr.id_order IS NULL AND crl.name like '" . $lang_cart_rule . "%'";

            
        if ( $id_customer != '' ) {
            $query.=" AND  cr.id_customer = ". $id_customer ; 
        }
        
        error_log("\n\n\nSegundo query: \n\n".$query, 3, "/var/www/errors.log");
        //$query.=" GROUP BY med.id_medico ORDER BY med.nombre ASC LIMIT 0,10";

        //echo "<br> query: ".$query;
        return Db::getInstance()->executeS($query);
    }
    
}  
