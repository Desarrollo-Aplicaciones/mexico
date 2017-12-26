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

class statisticshelp extends Module{
	
	private $_name = 'fbloginblock';
	private $_id_shop;
	
	public function __construct()	
 	{
 		
 		if(version_compare(_PS_VERSION_, '1.5', '>')){
		       	$this->_id_shop = Context::getContext()->shop->id;
		       	
		} else {
		    	$this->_id_shop = 0;
		}
		    
 		$name = "fbloginblock";
	
		if (version_compare(_PS_VERSION_, '1.5', '<')){
			require_once(_PS_MODULE_DIR_.$name.'/backward_compatibility/backward.php');
		}
	
	
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}
	
public function getCustomers($data){
    	
    	$start = $data['start'];
		$step = $data['step'];
		
		$id_shop = 0;
		$name_shop = '';
		
		
		$sql = 'SELECT customer_id as user_id , id_shop , type       
       				   FROM `'. _DB_PREFIX_ . 'customers_statistics_spm`
       				   ORDER BY `customer_id` DESC LIMIT '.(int)$start.' ,'.(int)$step.'';
		$_data_ids = Db::getInstance()->ExecuteS($sql);
		
		$user_data = array();
		foreach($_data_ids as $_item_id){
			$uid = $_item_id['user_id'];
			$id_shop = $_item_id['id_shop'];
				
			$type = $_item_id['type'];
			
			// get info about user //
			$sql = 'SELECT c.id_customer as id,
						   c.firstname,
						   c.lastname
						   FROM  `'. _DB_PREFIX_ . 'customer` c
						   WHERE c.id_customer = '.(int)$uid.'';
			$info_user = Db::getInstance()->ExecuteS($sql);
			$info_user[0]['id_shop']= $id_shop;
			$info_user[0]['type']= $type;
			$user_data[] = $info_user[0];
		}
		
		$_data_tmp = $user_data;
		
		$_data = array();
		
		foreach($_data_tmp as $_item){
			
			$_id_customer = isset($_item['id'])?$_item['id']:0;
			if($_id_customer == 0)continue;
			
			$types_avaiable = $this->getAvaiableTypesStat();
			
			foreach($types_avaiable as $data_prefix){

                $text_type = $data_prefix['prefix'];
                $id_type = $data_prefix['type'];

				$sql_is_exist = 'select COUNT(*) as count from `'. _DB_PREFIX_ . 'customers_statistics_spm` 
        					where customer_id = '.(int)$_id_customer.'  
        					 AND type = '.(int)$id_type;
			
				$data_exist_user = Db::getInstance()
				->getRow($sql_is_exist);
			
				$_item[$text_type]= $data_exist_user['count'];
				
				if(version_compare(_PS_VERSION_, '1.5', '>')){
					$id_shop = $_item['id_shop'];
					$data_shop = Shop::getShop($id_shop);
					
					$name_shop = $data_shop['name'];
				} else {
					$name_shop = '';
				}
				
				$_item['name_shop'] = $name_shop;
			
			}
			
			$_data[] = $_item;
			
			
			
			
			
		}
		
		$sql_count = 'SELECT distinct c.id_customer,
					   c.firstname,
					   c.lastname
					   FROM  `'. _DB_PREFIX_ . 'customer` c';
		$_data_tmp = Db::getInstance()->ExecuteS($sql_count);
		$count_all = 0;
		foreach($_data_tmp as $_item){
			$_id_customer = $_item['id_customer'];
			
			$data_exist_user = Db::getInstance()
			->getRow('select COUNT(*) as count from `'. _DB_PREFIX_ . 'customers_statistics_spm` 
        					where customer_id = '.(int)$_id_customer.'');
			
			if($data_exist_user['count']>0){
				$count_all++;
			}
			
		}
		return array('data' => $_data, 'count_all' => $count_all );
		
		
    }
    
	public function getCustomersSearch($data){
    	
     	$search_query = trim(htmlspecialchars(strip_tags($data['search_query'])));
     	
     	if(version_compare(_PS_VERSION_, '1.5', '>')){
		       	$id_shop = Context::getContext()->shop->id;
		       	$name_shop = Context::getContext()->shop->name;
		    } else {
		      	$id_shop = 0;
		      	$name_shop = '';
		    }
	    
		    
     	// get info about user //
     	
     	
		$sql = 'SELECT c.id_customer as id 
					   FROM  `'. _DB_PREFIX_ . 'customer` c
					   WHERE c.active = 1 AND c.deleted = 0  AND
					   (
					   	LOWER(c.lastname) LIKE BINARY LOWER(\'%'.$search_query.'%\')
					   OR
	     			    LOWER(c.firstname) LIKE BINARY LOWER(\'%'.$search_query.'%\')
	     			    )';
		
		$info_ids = Db::getInstance()->ExecuteS($sql);
		$ids_exists = array();
		foreach($info_ids as $_v_ids)
		$ids_exists[] = $_v_ids['id'];
		$ids_exists = implode(",",$ids_exists);
		if(Tools::strlen($ids_exists)==0)
			$ids_exists = 0;
		
		$sql = 'SELECT customer_id as user_id, id_shop , type    
       				   FROM `'. _DB_PREFIX_ . 'customers_statistics_spm`
       				   WHERE `customer_id` IN('.pSQL($ids_exists).') 
       				   ORDER BY `customer_id` DESC';
		$_data_ids = Db::getInstance()->ExecuteS($sql);
		
		$user_data = array();
		foreach($_data_ids as $_item_id){
			
			$id_shop = $_item_id['id_shop'];
				
			$type = $_item_id['type'];
			$uid = $_item_id['user_id'];
				
			// get info about user //
			$sql = 'SELECT c.id_customer as id,
						   c.firstname,
						   c.lastname
						   FROM  `'. _DB_PREFIX_ . 'customer` c
						   WHERE c.id_customer = '.(int)$uid;
			$info_user = Db::getInstance()->ExecuteS($sql);
			$info_user[0]['id_shop']= $id_shop;
			$info_user[0]['type']= $type;
			$user_data[] = $info_user[0];
		}
		
		$_data_tmp = $user_data;
		
		$_data = array();
		
		foreach($_data_tmp as $_item){
			
			
			$_id_customer = $_item['id'];
			
			$types_avaiable = $this->getAvaiableTypesStat();
			
			foreach($types_avaiable as $data_prefix){

                    $text_type = $data_prefix['prefix'];
                    $id_type = $data_prefix['type'];
			
				$sql_is_exist = 'select COUNT(*) as count from `'. _DB_PREFIX_ . 'customers_statistics_spm` 
        					where customer_id = '.(int)$_id_customer.'  
        					 AND type = '.(int)$id_type;
			
				$data_exist_user = Db::getInstance()
				->getRow($sql_is_exist);
			
				$_item[$text_type]= $data_exist_user['count'];
				
				if(version_compare(_PS_VERSION_, '1.5', '>')){
					$id_shop = $_item['id_shop'];
					$data_shop = Shop::getShop($id_shop);
					
					$name_shop = $data_shop['name'];
				} else {
					$name_shop = '';
				}
				
				$_item['name_shop'] = $name_shop;
			
			}
			
			$_data[] = $_item;
			
		}
		
		$sql_count = 'SELECT distinct c.id_customer,
					   c.firstname,
					   c.lastname
					   FROM  `'. _DB_PREFIX_ . 'customer` c 
					   WHERE c.id_customer IN('.pSQL($ids_exists).')';
		$_data_tmp = Db::getInstance()->ExecuteS($sql_count);
		$count_all = 0;
		foreach($_data_tmp as $_item){
			$_id_customer = $_item['id_customer'];
			
			$data_exist_user = Db::getInstance()
			->getRow('select COUNT(*) as count from `'. _DB_PREFIX_ . 'customers_statistics_spm` 
        					where customer_id = '.(int)$_id_customer.' ');
			
			if($data_exist_user['count']>0){
				$count_all++;
			}
			
		}
		return array('data' => $_data, 'count_all' => $count_all );
		
		
    }
	
	public function PageNav($start,$count,$step, $_data =null )
	{
		
		$res = '';
		$currentIndex = $_data['currentIndex'];
		$item = $_data['item'];
		$token = $_data['token'];
		$text_page = $_data['text_page'];
		$start1 = $start;
		
		
		$res .= '<span>';
			 if($start > 0){		
			   $res .= '<input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.$token.'\'" src="'._PS_ADMIN_IMG_.'list-prev2.gif">
						&nbsp;';
				  
			   $res .= '<input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.'='.((int)$start - (int)$step).$token.'\'" src="'._PS_ADMIN_IMG_.'list-prev.gif">&nbsp;&nbsp;';
			  }
			   
				$res .=	''.$text_page.' <b>'.((int)($start1 / $step) + 1).'</b> / '.ceil($count/$step).'';

			   if($start + $step < $count) {
						$res .= '&nbsp;&nbsp;<input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.'='.((int)$start + (int)$step).$token.'\'" src="'._PS_ADMIN_IMG_.'list-next.gif">
						&nbsp;
						<input type="image" onclick="window.location.href=\''.$currentIndex.'&page'.$item.'='.((ceil($count/$step)*$step)-$step).$token.'\'" src="'._PS_ADMIN_IMG_.'list-next2.gif">';
			   }
		$res .= '</span>';
		
		
		return $res;
	}
	
	
	public function totalCustomers(){
    	$data_all = Db::getInstance()
    	->getRow('select COUNT(*) as count from `'. _DB_PREFIX_ . 'customers_statistics_spm` css left join `'. _DB_PREFIX_ . 'customer`  c
    			on (c.id_customer = css.customer_id) where c.deleted = 0
    			');
    		
    	$count_types_array = array();

        foreach($this->getAvaiableTypesStat() as $data_prefix){

            $key_text = $data_prefix['prefix'];
            $value_id = $data_prefix['type'];


            $data_type = Db::getInstance()
    		->getRow('select COUNT(*) as count from `'. _DB_PREFIX_ . 'customers_statistics_spm` css left join `'. _DB_PREFIX_ . 'customer`  c
    				on (c.id_customer = css.customer_id) where c.deleted = 0 and
    				css.type = '.(int)$value_id);
    		$count_types_array[$key_text] = $data_type['count'];
    	}
    		
    	return array('count_all' => $data_all['count'],'count_types'=>$count_types_array);
    }
	
	public function getAvaiableTypesStat(){
		include_once(_PS_MODULE_DIR_.$this->_name.'/fbloginblock.php');
		$obj = new fbloginblock();
			
		return $obj->getConnetsArrayPrefix();
	}
	
	public function addCustomerToStatistics($data){
		
			$customer_id = $data['customer_id'];
			$email = $data['email'];
			$id_shop = $data['id_shop'];
			$type = $data['type'];
			
			
			$sql_exists = 'select count(*) as count from `'._DB_PREFIX_.'customers_statistics_spm` where 
								  customer_id = \''.(int)$customer_id.'\' and id_shop = \''.(int)$id_shop.'\'';
			$data_exists = Db::getInstance()->getRow($sql_exists);
			if($data_exists['count']==0){

                ## fixed bug, when admin delete user from admin panel, and user again try register on the site ##
                $sql_delete = 'delete from `'._DB_PREFIX_.'customers_statistics_spm` where
							    '.(version_compare(_PS_VERSION_, '1.5', '>')?'email_stat':'email').' = \''.pSQL($email).'\' AND
							   id_shop = \''.(int)$id_shop.'\' AND type = \''.(int)$type.'\' ';
                Db::getInstance()->Execute($sql_delete);
                ## fixed bug, when admin delete user from admin panel, and user again try register on the site ##

		
				$sql = 'insert into `'._DB_PREFIX_.'customers_statistics_spm` SET 
							   customer_id = \''.(int)$customer_id.'\', '.(version_compare(_PS_VERSION_, '1.5', '>')?'email_stat':'email').' = \''.pSQL($email).'\',
							   id_shop = \''.(int)$id_shop.'\', type = \''.(int)$type.'\' ';
				Db::getInstance()->Execute($sql);
				
			}
	}

    public function updateEmail($data){


        $email = $data['email'];
        $id_shop = $data['id_shop'];
        $id_customer = $data['id_customer'];


        $sql = 'UPDATE `'._DB_PREFIX_.'customers_statistics_spm`
						SET
						email = \''.pSQL($email).'\'
						WHERE id_customer = '.(int)$id_customer.' and id_shop = '.(int)($id_shop).'
						';
        Db::getInstance()->Execute($sql);

    }


}