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

function upgrade_module_1_6_1($module)
{

    #### update for override AdminCustomersController #####
    $module->uninstallOverrides();

    try {
        $module->installOverrides();
    } catch (Exception $e) {
        $module->uninstallOverrides();
        return false;
    }

    $path_to_delete_class_index = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."cache".DIRECTORY_SEPARATOR."class_index.php";
    if(file_exists($path_to_delete_class_index)){
        unlink($path_to_delete_class_index);
    }
    #### update for override AdminCustomersController #####

    return true;
}
?>