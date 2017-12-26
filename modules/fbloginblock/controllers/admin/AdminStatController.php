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

ob_start();
class AdminStatController extends AdminController
{

    private $_name_controller = 'AdminStatistics';
    public function __construct()

    {
        $red_url = 'index.php?controller='.$this->_name_controller.'&token='.Tools::getAdminTokenLite($this->_name_controller);
        Tools::redirectAdmin($red_url);
    }


}