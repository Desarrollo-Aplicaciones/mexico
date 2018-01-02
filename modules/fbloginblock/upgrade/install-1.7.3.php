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

function upgrade_module_1_7_3($module)
{
    $name_module = 'fbloginblock';


    if (version_compare(_PS_VERSION_, '1.7', '>')) {

        $tab_id = Tab::getIdFromClassName("AdminStat");
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminStatistics");
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        $tab_id = Tab::getIdFromClassName("AdminFbloginblockajax");
        if($tab_id){
            $tab = new Tab($tab_id);
            $tab->delete();
        }


        $module->createAdminTabs();


        $all_shops = Shop::getShops();

        foreach ($all_shops as $_shop) {

            $id_shop_group = (int)$_shop['id_shop_group'];
            $id_shop = (int)$_shop['id_shop'];


            // google connect
            Configuration::updateValue($name_module . 'oru', $module->getRedirectURL(array('typelogin' => 'google', 'is_settings' => 1)), false, $id_shop_group, $id_shop);
            // google connect

            // foursquare connect
            Configuration::updateValue($name_module . 'fsru', $module->getRedirectURL(array('typelogin' => 'foursquare', 'is_settings' => 1)), false, $id_shop_group, $id_shop);
            // foursquare connect

            // github connect
            Configuration::updateValue($name_module . 'giru', $module->getRedirectURL(array('typelogin' => 'github', 'is_settings' => 1)), false, $id_shop_group, $id_shop);

            // disqus connect
            Configuration::updateValue($name_module . 'dru', $module->getRedirectURL(array('typelogin' => 'disqus', 'is_settings' => 1)), false, $id_shop_group, $id_shop);

            // amazon connect
            Configuration::updateValue($name_module . 'aru', $module->getRedirectURL(array('typelogin' => 'amazon', 'is_settings' => 1)), false, $id_shop_group, $id_shop);

            //paypal connect
            Configuration::updateValue($name_module . 'pcallback', $module->getRedirectURL(array('typelogin' => 'paypal', 'is_settings' => 1)), false, $id_shop_group, $id_shop);
        }


        /// clear smarty cache ///

        Tools::clearSmartyCache();

        /// clear smarty cache ///

    }

    return true;
}
?>