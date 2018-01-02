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

class FbloginblockAccountModuleFrontController extends ModuleFrontController
{
    public $php_self;
	public function init()
	{

		parent::init();
	}
	
	public function setMedia()
	{

		parent::setMedia();

        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/fbloginblock/views/css/font-awesome.min.css', 'all');
        $this->context->controller->addCSS(__PS_BASE_URI__.'modules/fbloginblock/views/css/fbloginblock.css', 'all');


    }

	
	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
        $name_module = 'fbloginblock';

        $this->php_self = 'module-'.$name_module.'-account';
		parent::initContent();



        $cookie = Context::getContext()->cookie;



        $id_customer = isset($cookie->id_customer)?$cookie->id_customer:0;
        if (!$id_customer || !Configuration::get($name_module.'is_soc_link'))
            Tools::redirect('authentication.php');


        include_once(_PS_MODULE_DIR_.$name_module.'/fbloginblock.php');
        $obj_fbloginblock = new fbloginblock();

        $obj_fbloginblock->settings();


        include_once(_PS_MODULE_DIR_.$name_module.'/classes/userhelp.class.php');
        $userhelp = new userhelp();

        $data_linked_all = $userhelp->getLinkedAccountsForCustomer(array('id_customer'=>$id_customer));

        $data_linked = array();

        foreach($data_linked_all['data'] as $data_value){
            $data_linked[$data_value['type']]=$data_value;
        }


        $_data_translate = $obj_fbloginblock->translateCustom();



        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->context->smarty->tpl_vars['page']->value['meta']['title'] = $_data_translate['meta_title_myaccount'];
            $this->context->smarty->tpl_vars['page']->value['meta']['description'] = $_data_translate['meta_description_myaccount'];
            $this->context->smarty->tpl_vars['page']->value['meta']['keywords'] = $_data_translate['meta_keywords_myaccount'];
        }

        $this->context->smarty->assign('meta_title' , $_data_translate['meta_title_myaccount']);
        $this->context->smarty->assign('meta_description' , $_data_translate['meta_description_myaccount']);
        $this->context->smarty->assign('meta_keywords' , $_data_translate['meta_keywords_myaccount']);


        $is_linked = Tools::getValue('is_linked');
        if($is_linked == 2){
            $is_linked = 'del';
        } elseif($is_linked == 1) {
            $is_linked = 'link';
        }

        $this->context->smarty->assign(array(
            $name_module.'data_linked'=>$data_linked,
            $name_module.'is_linked'=>$is_linked,


        ));

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            $this->setTemplate('module:' . $name_module . '/views/templates/front/account17.tpl');
        }else {
            $this->setTemplate('account.tpl');
        }


    }
}