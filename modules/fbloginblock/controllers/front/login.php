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

class FbloginblockLoginModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {

        parent::initContent();
        $name_module = 'fbloginblock';


        $action = Tools::getValue('p');
        $http_referer = Tools::getValue('http_referer');

        if (version_compare(_PS_VERSION_, '1.5', '>')){
            $cookie = new Cookie('ref');
            $cookie->http_referer_custom = $http_referer;
        }

        include_once(_PS_MODULE_DIR_.$name_module.'/classes/login.class.php');
        $obj = new login(array('p'=>$action,'http_referer'=>$http_referer));
        $obj->GoogleAndYahooLoginLogin();
        exit;


    }

}

?>