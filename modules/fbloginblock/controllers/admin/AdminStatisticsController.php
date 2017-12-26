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

require_once(_PS_MODULE_DIR_ . 'fbloginblock/classes/StatisticsCustomers.php');

class AdminStatisticsController extends ModuleAdminController {

    private $_name_module = 'fbloginblock';

public function __construct()
    {
    	 $name_module = 'fbloginblock';
    	 
    	 $this->bootstrap = true;
    	 
    	 $this->module = $name_module;
    	 
         $this->table = 'customers_statistics_spm';
         $this->className = 'StatisticsCustomers';
         $this->identifier = 'id';
  
         $this->lang = false;
         
         $this->_orderBy = 'id';
         $this->_orderWay = 'DESC';
         
        
         $this->allow_export = false;
         

        
          
         $this->_select .= 'c.id_customer as id, c.firstname, c.lastname, a.`type` as social_connect, s.`name` AS `shop_name`  ';
         $this->_join .= ' JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.id_customer = a.customer_id and c.deleted = 0)';
         $this->_join .= ' LEFT JOIN ' . _DB_PREFIX_ . 'shop s on s.id_shop = a.id_shop ';

         
         //$this->addRowAction('edit');
         //$this->addRowAction('delete');
         $this->addRowAction('view');
         //$this->addRowAction('&nbsp;');
         
         //$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));

         
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
          ### social icons ####
         
         ### shops ###
          
         $shops = Shop::getShops();
         $data_shops = array();
         foreach($shops as $_shop){
         	$data_shops[$_shop['id_shop']]= $_shop['name'];
         }
         ### shops ###



        //$translations = $this->_customTranslation();
  
         $this->fields_list = array(
            'id' => array(
                'title' => $this->l('ID'),
                //'title' => $translations['id'],
                'align' => 'center',
                'width' => 25,
            	'search' => false,
            	'orderby' => true, 
            	
            ),
            'firstname' => array(
                'title' => $this->l('First Name'),
                //'title' => $translations['firstname'],
                'width' => 'auto',
                //'hint' => $translations['firstname_hint'],
            	'hint' => $this->l('You can search customer by First Name'),
            	'filter_key' => 'c!firstname',
            	
            ),
         	'lastname' => array(
         				'title' => $this->l('Last Name'),
                        //'title' => $translations['lastname'],
         				'width' => 'auto',
         				'hint' => $this->l('You can search customer by Last Name'),
                        //'hint' => $translations['lastname_hint'],
         				'filter_key' => 'c!lastname',
         				 
         	),
        	'shop_name' => array(
        		'title' => $this->l('Shop'),
                //'title' => $translations['shop_name'],
        		'width' => 'auto',
        		'orderby' => false,
        		'filter_key' => 'a!id_shop',
        		'type' => 'select', 'list' => $data_shops 
        	),
        	'social_connect' => array(
        		'title' => $this->l('Social Connect'),
                //'title' => $translations['social_connect'],
        		'width' => 'auto',
        		'type' => 'select', 'list' => $status  , 'icon' => $statusIcon,
        		'filter_key' => 'a!type',
        		'orderby' => false,
        		'align' => 'left',
        	)
        );  
  
        $this->list_no_link = true;
        
          
        parent::__construct();
    }
    
    public function initToolbar() {
    	parent::initToolbar();
    	$this->toolbar_btn['new'] = array('desc'=>'');
    	
    }

    /* override function $this->l() for compatible with Prestashop 1.7.x.x */

    /*public function l($string , $class = NULL, $addslashes = false, $htmlentities = true){
        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::l($string);
        } else {
            return Translate::getModuleTranslation($this->_name_module, $string, $this->_name_module);
        }
    }*/

    public function l($string , $class = NULL, $addslashes = false, $htmlentities = true){
        if(version_compare(_PS_VERSION_, '1.7', '<')) {
            return parent::l($string);
        } else {
            //$class = array();
            //return Context::getContext()->getTranslator()->trans($string, $class, $addslashes, $htmlentities);
            return Translate::getModuleTranslation($this->_name_module, $string, $this->_name_module);
        }
    }


    
    
    
    
}

?>
