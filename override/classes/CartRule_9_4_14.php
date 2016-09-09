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

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'cart_rule` cr
        LEFT JOIN `'._DB_PREFIX_.'cart_rule_lang` crl ON (cr.`id_cart_rule` = crl.`id_cart_rule` AND crl.`id_lang` = '.(int)$id_lang.')
        WHERE (
            cr.`id_customer` = '.(int)$id_customer.' OR cr.group_restriction = 1
            '.($includeGeneric ? 'OR cr.`id_customer` = 0' : '').'
        )
        AND cr.date_from < "'.date('Y-m-d H:i:s').'"
        AND cr.date_to > "'.date('Y-m-d H:i:s').'"
        '.($active ? 'AND cr.`active` = 1' : '').'
        '.($inStock ? 'AND cr.`quantity` > 0 LIMIT 1' : ' LIMIT 1'));

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
        $arrayarray = array();  
       
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

        $array['selected'] = Db::getInstance()->executeS($rule_cupon_1);  

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

        $array['unselected'] = Db::getInstance()->executeS($rule_cupon_2);  
        exit;
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

  
}  