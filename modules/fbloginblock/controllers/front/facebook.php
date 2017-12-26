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

class FbloginblockFacebookModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {

        parent::initContent();

        $http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';

        $name_module = 'fbloginblock';


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/facebookhelp.class.php');
        $obj = new facebookhelp();
        $obj->facebookLogin(array('http_referer_custom'=>$http_referer));

        exit;
    }

}

?>