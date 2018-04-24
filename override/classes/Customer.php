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

     public static $definition = array(
        'table' => 'customer',
        'primary' => 'id_customer',
        'fields' => array(
            'secure_key' =>                 array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
            'lastname' =>                   array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => false, 'size' => 32),
            'firstname' =>                  array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'email' =>                      array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'passwd' =>                     array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 32),
            'last_passwd_gen' =>            array('type' => self::TYPE_STRING, 'copy_post' => false),
            'id_gender' =>                  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false),
            'birthday' =>                   array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
            'newsletter' =>                 array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'newsletter_date_add' =>        array('type' => self::TYPE_DATE,'copy_post' => false),
            'ip_registration_newsletter' => array('type' => self::TYPE_STRING, 'copy_post' => false),
            'optin' =>                      array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'website' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'company' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'siret' =>                      array('type' => self::TYPE_STRING, 'validate' => 'isSiret'),
            'ape' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isApe'),
            'outstanding_allow_amount' =>   array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'show_public_prices' =>         array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_risk' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'max_payment_days' =>           array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'active' =>                     array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'deleted' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'note' =>                       array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
            'is_guest' =>                   array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_shop' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_shop_group' =>              array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_default_group' =>           array('type' => self::TYPE_INT, 'copy_post' => false),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'date_add' =>                   array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' =>                   array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_type' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'identification' =>             array('type' => self::TYPE_STRING, 'validate' => 'isFileName', 'copy_post' => false),
            'sms' =>                        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'img_profile' =>                array('type' => self::TYPE_STRING, 'copy_post' => false),            
        ),
    );
    
    
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

        /**
     * Light back office search for customers
     *
     * @param string $query Searched string
     * @return array Corresponding customers
     */
    public static function searchByName($query)
    {
        $sql = 'SELECT cus.*, (SELECT group_concat(" ",fm.type_message) FROM ps_fraud_message fm WHERE FIND_IN_SET(fm.id, cus.fraud)) AS "type_fraud"
                FROM `'._DB_PREFIX_.'customer` cus LEFT JOIN `'._DB_PREFIX_.'address` adr ON (cus.id_customer = adr.id_customer)
                WHERE (
                        cus.`email` LIKE \'%'.pSQL($query).'%\'
                        OR cus.`id_customer` LIKE \'%'.pSQL($query).'%\'
                        OR cus.`lastname` LIKE \'%'.pSQL($query).'%\'
                        OR cus.`firstname` LIKE \'%'.pSQL($query).'%\'
                        OR adr.dni LIKE \'%'.pSQL($query).'%\'
                        OR cus.identification LIKE \'%'.pSQL($query).'%\'
                        OR adr.phone LIKE \'%'.pSQL($query).'%\'
                        OR adr.phone_mobile LIKE \'%'.pSQL($query).'%\'
                    ) AND cus.id_shop IN (1)
                    GROUP BY cus.id_customer
                    LIMIT 40;';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }

       /**
     * Login social meadia
     * Return customer instance from its e-mail (optionnaly check password)
     *
     * @param string $email e-mail
     * @param string $passwd Password is also checked if specified
     * @return Customer instance
     */
        public function getByEmailSM($email, $ignore_guest = true)
        {
            if (!Validate::isEmail($email) )
                die (Tools::displayError());

            $sql = 'SELECT *
            FROM `'._DB_PREFIX_.'customer`
            WHERE `email` = \''.pSQL($email).'\'
            '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
            '.(isset($passwd) ? 'AND `passwd` = \''.Tools::encrypt($passwd).'\'' : '').'
            AND `deleted` = 0'.
            ($ignore_guest ? ' AND `is_guest` = 0' : '');

            $result = Db::getInstance()->getRow($sql);

            if (!$result)
                return false;
            $this->id = $result['id_customer'];
            foreach ($result as $key => $value)
                if (key_exists($key, $this))
                    $this->{$key} = $value;

                return $this;
        } 


    /**
     * Update discount for group
     *
     * @param type $identification
     */
    public function updateGroupDiscount($identification)
    {

        $sqlEmi = 'SELECT id_doc FROM ' . _DB_PREFIX_ . 'customer_emi WHERE id_doc = "' . $identification . '"';
        $default = 3;
        //      User EMI validation     //
        if ($row = Db::getInstance()->getRow($sqlEmi)) {
            //      Is the Customer is within the group 5 EMI     //
            if (empty(Db::getInstance()->executeS('SELECT id_group FROM ps_customer_group WHERE id_customer = ' . $this->id . ' AND id_group = 5'))) {
                $group = array(5);
                $this->addGroups($group);
                $default = 5;
            }
        } else {    //      When you are not registered as an EMI user      //
            $clone_group = $this->getGroups();
            $this->cleanGroups();
            foreach ($clone_group as $group) {
                $groups_update = array();
                if ($group != 5) {
                    array_push($groups_update, $group);
                    $this->addGroups($groups_update);
                }
            }
        }
        //      Update default group for Customer       //
        Db::getInstance()->update('customer', array(
            'id_default_group' => $default
        ), 'identification = "' . $identification . '"');
    }

    public function getValidPhones()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("select CONCAT(SUBSTRING(phone, 1, LENGTH(phone)-4),'****') phone,  REPLACE(phone,'+','') valid_phone, id_address as id_address_delivery from (
            select  
            distinct(IFNULL(
            IF(
             (LENGTH(REPLACE(`phone_mobile`, ' ', ''))=10 AND LEFT( REPLACE(`phone_mobile`, ' ', ''),3 )=553) OR
              (LENGTH(REPLACE(`phone_mobile`, ' ', ''))=8 AND LEFT( REPLACE(`phone_mobile`, ' ', ''),1 )='3') OR
              (LENGTH(REPLACE(`phone_mobile`, ' ', ''))=11 AND LEFT( REPLACE(`phone_mobile`, ' ', ''),4 )='+553') OR
                (LENGTH(REPLACE(`phone_mobile`, ' ', ''))=11 AND LEFT( REPLACE(`phone_mobile`, ' ', ''),3 )='346') OR
              (LENGTH(REPLACE(`phone_mobile`, ' ', ''))=13 AND LEFT( REPLACE(`phone_mobile`, ' ', ''),4 )='+521'), REPLACE(`phone_mobile`, ' ', ''), null),

            IF( 
             (LENGTH(REPLACE(`phone`, ' ', ''))=10 AND LEFT( REPLACE(`phone`, ' ', ''),3 )=553) OR
              (LENGTH(REPLACE(`phone`, ' ', ''))=8 AND LEFT( REPLACE(`phone`, ' ', ''),1 )='3') OR
              (LENGTH(REPLACE(`phone`, ' ', ''))=11 AND LEFT( REPLACE(`phone`, ' ', ''),4 )='+553') OR
                  (LENGTH(REPLACE(`phone`, ' ', ''))=11 AND LEFT( REPLACE(`phone`, ' ', ''),3 )='346') OR
              (LENGTH(REPLACE(`phone`, ' ', ''))=13 AND LEFT( REPLACE(`phone`, ' ', ''),4 )='+521'), REPLACE(`phone`, ' ', ''), null))) phone, a.id_address
            from ps_address a
            left join (SELECT max(id_order) max_order, id_address_delivery from ps_orders o group by id_address_delivery) o on o.id_address_delivery=a.id_address
            where a.id_customer = ".$this->id."
            order by max_order desc) x  where phone is not null");
    }
    
}
