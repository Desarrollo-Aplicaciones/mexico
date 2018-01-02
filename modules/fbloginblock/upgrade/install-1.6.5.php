<?php
/**
 * 2011 - 2017 StorePrestaModules SPM LLC.
 *
 * MODULE fbloginblock
 *
 * @author    SPM <kykyryzopresto@gmail.com>
 * @copyright Copyright (c) permanent, SPM
 * @license   Addons PrestaShop license limitation
 * @version   1.7.7
 * @link      http://addons.prestashop.com/en/2_community-developer?contributor=61669
 *
 * NOTICE OF LICENSE
 *
 * Don't use this module on several shops. The license provided by PrestaShop Addons
 * for all its modules is valid only once for a single shop.
 */

function upgrade_module_1_6_5($module)
{
    $list_index = Db::getInstance()->getRow('SELECT COUNT(1) indexexists FROM INFORMATION_SCHEMA.STATISTICS
                                                WHERE table_name=\'' . _DB_PREFIX_ . 'customers_statistics_spm\' AND index_name=\'customer_id\'');
    if (!$list_index['indexexists']) {
        $sql_add_index = 'ALTER TABLE ' . _DB_PREFIX_ . 'customers_statistics_spm ADD INDEX  customer_id (customer_id)';
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_add_index))
            return false;

    }




    $list_index = Db::getInstance()->getRow('SELECT COUNT(1) indexexists FROM INFORMATION_SCHEMA.STATISTICS
                                                WHERE table_name=\'' . _DB_PREFIX_ . 'customers_statistics_spm\' AND index_name=\'id_shop\'');
    if (!$list_index['indexexists']) {
        $sql_add_index = 'ALTER TABLE ' . _DB_PREFIX_ . 'customers_statistics_spm ADD INDEX  id_shop (id_shop)';
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_add_index))
            return false;
    }


    $list_index = Db::getInstance()->getRow('SELECT COUNT(1) indexexists FROM INFORMATION_SCHEMA.STATISTICS
                                                WHERE table_name=\'' . _DB_PREFIX_ . 'customers_statistics_spm\' AND index_name=\'type\'');
    if (!$list_index['indexexists']) {
        $sql_add_index = 'ALTER TABLE ' . _DB_PREFIX_ . 'customers_statistics_spm ADD INDEX  `type` (`type`)';
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_add_index))
            return false;
    }





    $list_index = Db::getInstance()->getRow('SELECT COUNT(1) indexexists FROM INFORMATION_SCHEMA.STATISTICS
                                                WHERE table_name=\'' . _DB_PREFIX_ . 'customer_linked_social_account\' AND index_name=\'type\'');
    if (!$list_index['indexexists']) {
        $sql_add_index = 'ALTER TABLE ' . _DB_PREFIX_ . 'customer_linked_social_account ADD INDEX  `type` (`type`)';
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_add_index))
            return false;
    }




    $list_index = Db::getInstance()->getRow('SELECT COUNT(1) indexexists FROM INFORMATION_SCHEMA.STATISTICS
                                                WHERE table_name=\'' . _DB_PREFIX_ . 'customer_linked_social_account\' AND index_name=\'id_shop\'');
    if (!$list_index['indexexists']) {
        $sql_add_index = 'ALTER TABLE ' . _DB_PREFIX_ . 'customer_linked_social_account ADD INDEX  id_shop (id_shop)';
        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute($sql_add_index))
            return false;
    }

    return true;

    return true;
}
?>