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

class FbloginblockdeleteModuleFrontController extends ModuleFrontController
{
	
	public function init()
	{

		parent::init();
	}
	
	public function setMedia()
	{

		parent::setMedia();


    }

	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();


        $cookie = Context::getContext()->cookie;

        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
        if (!$id_customer)
            Tools::redirect('authentication.php');

        $name_module = 'fbloginblock';

        include_once(_PS_MODULE_DIR_.$name_module.'/classes/userhelp.class.php');
        $userhelp = new userhelp();

        $type = Tools::getValue('type');
        $userhelp->deleteLinkedAccount(array('id_customer'=>$id_customer,'type'=>$type));


    }
}