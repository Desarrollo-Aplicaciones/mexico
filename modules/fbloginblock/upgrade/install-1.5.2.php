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

function upgrade_module_1_5_2($module)
{

    if(version_compare(_PS_VERSION_, '1.6', '>')){

        $name_module = 'fbloginblock';
        $array_need = array('p');
        foreach($array_need as $prefix){
            Configuration::updateValue($name_module.'sztop'.$prefix, 'ltop'.$prefix);
            Configuration::updateValue($name_module.'szrightcolumn'.$prefix, 'srightcolumn'.$prefix);
            Configuration::updateValue($name_module.'szleftcolumn'.$prefix, 'sleftcolumn'.$prefix);

            Configuration::updateValue($name_module.'szfooter'.$prefix, 'lfooter'.$prefix);

            Configuration::updateValue($name_module.'szauthpage'.$prefix, 'lsauthpage'.$prefix);
            Configuration::updateValue($name_module.'szwelcome'.$prefix, 'smwelcome'.$prefix);
            Configuration::updateValue($name_module.'szchook'.$prefix, 'lschook'.$prefix);


            Configuration::updateValue($name_module.'_top'.$prefix, 'top'.$prefix);
            Configuration::updateValue($name_module.'_rightcolumn'.$prefix, 'rightcolumn'.$prefix);
            Configuration::updateValue($name_module.'_leftcolumn'.$prefix, 'leftcolumn'.$prefix);

            Configuration::updateValue($name_module.'_authpage'.$prefix, 'authpage'.$prefix);
            Configuration::updateValue($name_module.'_welcome'.$prefix, 'welcome'.$prefix);

            Configuration::updateValue($name_module.'_chook'.$prefix, 'chook'.$prefix);

            Configuration::updateValue($name_module.$prefix.'_on', 1);
        }


        $array_need = array('o','ma','ya','i');
        foreach($array_need as $prefix){
            Configuration::deleteByName($name_module.'sztop'.$prefix);
            Configuration::deleteByName($name_module.'szrightcolumn'.$prefix);
            Configuration::deleteByName($name_module.'szleftcolumn'.$prefix);

            Configuration::deleteByName($name_module.'szfooter'.$prefix);

            Configuration::deleteByName($name_module.'szauthpage'.$prefix);
            Configuration::deleteByName($name_module.'szwelcome'.$prefix);
            Configuration::deleteByName($name_module.'szchook'.$prefix);

            Configuration::deleteByName($name_module.'_chook'.$prefix);
            Configuration::deleteByName($name_module.'_top'.$prefix);
            Configuration::deleteByName($name_module.'_footer'.$prefix);
            Configuration::deleteByName($name_module.'_rightcolumn'.$prefix);
            Configuration::deleteByName($name_module.'_leftcolumn'.$prefix);

            Configuration::deleteByName($name_module.'_authpage'.$prefix);
            Configuration::deleteByName($name_module.'_welcome'.$prefix);


            Configuration::deleteByName($name_module.$prefix.'_on');
        }



    }

    return true;
}
?>