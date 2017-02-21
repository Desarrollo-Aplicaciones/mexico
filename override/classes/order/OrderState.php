<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class OrderState extends OrderStateCore
{
 	/**
	* Get all available order states
	*
	* @param integer $id_lang Language id for state name
	* @return array Order states
	*/
	public static function getOrderStates($id_lang, $id_profile = NULL, $flag_order_back = false)
	{ 
		$sql = '';
		if(!is_null($id_profile) && is_int($id_profile)){
			$sql = 'SELECT * 
					FROM
						`'._DB_PREFIX_.'order_state` os INNER JOIN `'._DB_PREFIX_.'order_states_profile` osp ON(os.`id_order_state` = osp.`id_order_state` )
					INNER JOIN `'._DB_PREFIX_.'profile` p ON(osp.`id_profile` = p.`id_profile`)
					INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$id_lang.')
					WHERE p.`id_profile` = '.(int)$id_profile.' AND os.`deleted` = 0
					ORDER BY osl.`name` ASC;';
		} else{
			$sql = 'SELECT *
					FROM `'._DB_PREFIX_.'order_state` os
					LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$id_lang.')
					WHERE deleted = 0
					ORDER BY `name` ASC';	
		}  	
		
                $result = Db::getInstance()->executeS($sql);
                // echo '<b> data-    -2xd: <pre>'.var_dump($result).'</pre>';
                        
                if($flag_order_back){
                    /*error_log("Este es el arreglo de order_state_back: ".print_r($result, true),3, "/tmp/orderstates.log" );*/
                    // Ewstados validos para nuevas ordenes por back.
                    $valid_states = explode(',',Configuration::get('PS_CREATE_ORDER_STATE_BACK'));
                    /*error_log("Este es el arreglo de estados validos: ".print_r($valid_states, true),3, "/tmp/orderstates.log" );*/
                    
                    $states = array();
                    
                    foreach ($result as $array){
                        foreach ($valid_states as $key => $value) {
                            if ($array['id_order_state'] == $value) {
                                $states[] = $array;
                            }
                        }
                    }
                    return $states;
                    /*error_log("Este es el arreglo de estados validos FINAL: ".print_r($states, true),3, "/tmp/orderstates.log" );*/
                    
                }
                        
		return $result;
	}

}


