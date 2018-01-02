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

function upgrade_module_1_6_0($module)
{

        $name_module = 'fbloginblock';

        $module->registerHook('customerAccount');
        $module->registerHook('myAccountBlock');

        $array_need = array('v');
        foreach($array_need as $prefix){

            Configuration::updateValue($name_module.'sztop'.$prefix, 'bltop'.$prefix);
            Configuration::updateValue($name_module.'szrightcolumn'.$prefix, 'bsrightcolumn'.$prefix);
            Configuration::updateValue($name_module.'szleftcolumn'.$prefix, 'bsleftcolumn'.$prefix);

            Configuration::updateValue($name_module.'szfooter'.$prefix, 'blfooter'.$prefix);

            Configuration::updateValue($name_module.'szauthpage'.$prefix, 'blsauthpage'.$prefix);
            Configuration::updateValue($name_module.'szwelcome'.$prefix, 'bsmwelcome'.$prefix);
            Configuration::updateValue($name_module.'szchook'.$prefix, 'blschook'.$prefix);




            Configuration::updateValue($name_module.'_top'.$prefix, 'top'.$prefix);
            Configuration::updateValue($name_module.'_rightcolumn'.$prefix, 'rightcolumn'.$prefix);
            Configuration::updateValue($name_module.'_leftcolumn'.$prefix, 'leftcolumn'.$prefix);


            Configuration::updateValue($name_module.'_authpage'.$prefix, 'authpage'.$prefix);
            Configuration::updateValue($name_module.'_welcome'.$prefix, 'welcome'.$prefix);

            Configuration::updateValue($name_module.'_chook'.$prefix, 'chook'.$prefix);

            Configuration::updateValue($name_module.$prefix.'_on', 1);
        }


    $data_prefixes = $module->getConnetsArrayPrefix();

    foreach($data_prefixes as $prefix_short => $data_prefix_item){

        Configuration::updateValue($name_module.'szbeforeauthpage'.$prefix_short, 'blsbeforeauthpage'.$prefix_short);
        Configuration::updateValue($name_module.'_beforeauthpage'.$prefix_short, 'beforeauthpage'.$prefix_short);
    }

    Configuration::updateValue($name_module.'_rcblock', 'rcblock');
    Configuration::updateValue($name_module.'_lcblock', 'lcblock');

    $module->createLinkedSocialConnectTable();

    return true;
}
?>