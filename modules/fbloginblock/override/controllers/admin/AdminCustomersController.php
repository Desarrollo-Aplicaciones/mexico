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

class AdminCustomersController extends AdminCustomersControllerCore
{

	public function renderList()
    {
    	
    	$name_module = 'fbloginblock';
    	### social icons ###
    	include_once(_PS_MODULE_DIR_.$name_module."/".$name_module.".php");
    	$obj = new $name_module();
    	 
    	$data_avaiable_types = $obj->getConnetsArrayPrefix();
    	
    	$statusIcon = array();
    	$status = array();
    	 
    	foreach($data_avaiable_types as $data_prefix){

            $k = $data_prefix['type'];
            $value = $data_prefix['prefix'];
    	
    		$statusIcon[$k] = array('src' => '../../modules/'.$name_module.'/views/img/'.$value.'-small.png', 'alt' => ucwords($value));
    		$status[$k] = ucwords($value);
    	
    	}
    	
    	$this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'customers_statistics_spm` spm ON (a.id_customer = spm.customer_id)';
    	### social icons ####
    	
    	
    	$filter = Tools::getValue('customerFilter_spm!type');
    	if($filter){
    		$this->_where .= ' AND spm.`type` = '.(int)$filter;
    	}
    	
    	 $this->array_insert($this->fields_list,"email",array(
												    			'social_connect' => array(
												    					'title' => $this->l('Social Connect'),
												    					'width' => 'auto',
												    					'type' => 'select', 'list' => $status  , 'icon' => $statusIcon,
												    					'filter_key' => 'spm!type',
												    					'orderby' => false,
												    					'align' => 'left',
												    			),
												    		)
    						);  
    	
    	
    	
    	
    	return parent::renderList();
    } 
    
    private function array_insert(&$array, $position, $insert)
    {
    	if (is_int($position)) {
    		array_splice($array, $position, 0, $insert);
    	} else {
    		$pos   = array_search($position, array_keys($array));
    		$array = array_merge(
    				array_slice($array, 0, $pos),
    				$insert,
    				array_slice($array, $pos)
    		);
    	}
    }
   
}
