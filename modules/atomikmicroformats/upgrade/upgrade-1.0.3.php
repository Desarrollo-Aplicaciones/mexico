<?php
/**
* 2016-2017 Atomik Soft
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    Atomik Soft <info@atomiksoft.com>
*  @copyright 2016-2017 Atomik Soft
*  @license   http://addons.prestashop.com/en/content/12-terms-and-conditions-of-use
*/

function upgrade_module_1_0_3($module)
{
    $module->pingWebService();
    echo $module->name;
    return true;
}
