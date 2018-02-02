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

$_GET['controller'] = 'all';
$_GET['fc'] = 'module';
$_GET['module'] = 'fbloginblock';
include_once(dirname(__FILE__).'/../../config/config.inc.php');
include_once(_PS_ROOT_DIR_.'/init.php');

		$name_module = 'fbloginblock';
		
		if (version_compare(_PS_VERSION_, '1.5', '<')){
			require_once(_PS_MODULE_DIR_.$name_module.'/backward_compatibility/backward.php');
		} else{
			$smarty = Context::getContext()->smarty;
            $cookie = Context::getContext()->cookie;
		}


include_once(_PS_MODULE_DIR_.$name_module.'/fbloginblock.php');
$obj_fbloginblock = new fbloginblock();
$_data_translate = $obj_fbloginblock->translateCustom();


$smarty->assign('meta_title' , $_data_translate['meta_title_myaccount']);
$smarty->assign('meta_description' , $_data_translate['meta_description_myaccount']);
$smarty->assign('meta_keywords' , $_data_translate['meta_keywords_myaccount']);


        if (version_compare(_PS_VERSION_, '1.5', '>') && version_compare(_PS_VERSION_, '1.6', '<')) {
            if (isset(Context::getContext()->controller)) {
                $oController = Context::getContext()->controller;
            }
            else {
                $oController = new FrontController();
                $oController->init();
            }
            // header
            $oController->setMedia();
            @$oController->displayHeader();
        } else {
            if(version_compare(_PS_VERSION_, '1.5', '<'))
                include(dirname(__FILE__).'/../../header.php');
        }





$id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
if (!$id_customer)
    Tools::redirect('authentication.php');


$obj_fbloginblock->settings();


include_once(_PS_MODULE_DIR_.$name_module.'/classes/userhelp.class.php');
$userhelp = new userhelp();

$data_linked_all = $userhelp->getLinkedAccountsForCustomer(array('id_customer'=>$id_customer));

$data_linked = array();

foreach($data_linked_all['data'] as $data_value){
    $data_linked[$data_value['type']]=$data_value;
}
//echo "<pre>"; var_dump($data_linked_all['data']); var_dump($data_linked);exit;


$is_linked = Tools::getValue('is_linked');
if($is_linked == 2){
    $is_linked = 'del';
} elseif($is_linked == 1) {
    $is_linked = 'link';
}

$smarty->assign(array(
    $name_module.'data_linked'=>$data_linked,
    $name_module.'is_linked'=>$is_linked,


));





    if(version_compare(_PS_VERSION_, '1.5', '>')){

        if(version_compare(_PS_VERSION_, '1.6', '>')){

            $obj_front_c = new ModuleFrontController();
            $obj_front_c->module->name = "fbloginblock";
            $obj_front_c->setTemplate('account.tpl');

            $obj_front_c->setMedia();

            $obj_front_c->initHeader();

            $obj_front_c->initContent();

            $obj_front_c->initFooter();


            $obj_front_c->display();

        } else {
            echo $obj_fbloginblock->renderUserAccount();
        }
    } else {
        echo Module::display(dirname(__FILE__).'/fbloginblock.php', 'views/templates/front/account.tpl');
    }



if (version_compare(_PS_VERSION_, '1.5', '>') && version_compare(_PS_VERSION_, '1.6', '<')) {
    if (isset(Context::getContext()->controller)) {
        $oController = Context::getContext()->controller;
    }
    else {
        $oController = new FrontController();
        $oController->init();
    }
    // header
    @$oController->displayFooter();
} else {
    if(version_compare(_PS_VERSION_, '1.5', '<'))
        include(dirname(__FILE__).'/../../footer.php');
}
