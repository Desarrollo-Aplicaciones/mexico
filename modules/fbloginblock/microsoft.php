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

if (version_compare(_PS_VERSION_, '1.7', '<')){
    require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
}

$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';


include_once(_PS_MODULE_DIR_.$name_module.'/fbloginblock.php');
$obj_fbloginblock_ps14_13 = new fbloginblock();
$obj_fbloginblock_ps14_13->setCookieForPrestashop14_13();






include_once(_PS_MODULE_DIR_.$name_module.'/classes/microsofthelp.class.php');
$obj = new microsofthelp();
$obj->microsoftLogin(array('http_referer_custom'=>$http_referer));
	
	
  