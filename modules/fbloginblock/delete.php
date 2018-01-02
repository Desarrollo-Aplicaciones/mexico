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

include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(_PS_ROOT_DIR_.'/init.php');

$name_module = 'fbloginblock';


if (version_compare(_PS_VERSION_, '1.5', '<')){
    require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
} else{
    $cookie = Context::getContext()->cookie;
}

$id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
if (!$id_customer)
    Tools::redirect('authentication.php');

include_once(_PS_MODULE_DIR_.$name_module.'/classes/userhelp.class.php');
$userhelp = new userhelp();

$type = Tools::getValue('type');
$userhelp->deleteLinkedAccount(array('id_customer'=>$id_customer,'type'=>$type));

