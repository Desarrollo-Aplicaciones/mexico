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

class amazonhelp extends Module{
	
	private $_http_host;
	private $_name;
    private $_social_type = 24;
	
	public function __construct(){
		
		$this->_name = 'fbloginblock';
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$this->_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}
		
		if (version_compare(_PS_VERSION_, '1.5', '<')){
			require_once(_PS_MODULE_DIR_.$this->_name.'/backward_compatibility/backward.php');
		}
	
	
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}
	

	
	
	public function userLog($_data){

        ## add new functional for auth and create user ##


        include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
        $userhelp = new userhelp();
        return $userhelp->userLog(
            array(
                'data_profile'=>$_data['data'],
                'http_referer_custom'=>$_data['http_referer_custom'],
                'type'=>$this->_social_type,
            )
        );
        ## add new functional for auth and create user ##

    }



}