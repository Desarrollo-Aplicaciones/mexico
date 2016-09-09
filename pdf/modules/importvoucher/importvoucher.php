<?php

class importvoucher extends Module
{
	function __construct()
	{
		$this->name = 'importvoucher';
		$this->tab = 'Blocks';
		$this->version = '1.5.2';
        $this->author = 'MyPresta.eu';
        $this->tab = 'advertising_marketing';
        $this->dir = '/modules/importvoucher/';
		$this->psver=$this->psversion();
	    @ini_set("display_errors", 'off'); 
		parent::__construct();
        $this->mkey="nlc";
        if (@file_exists('../modules/'.$this->name.'/key.php'))
            @require_once ('../modules/'.$this->name.'/key.php');
        else if (@file_exists(dirname(__FILE__) . $this->name.'/key.php'))
            @require_once (dirname(__FILE__) . $this->name.'/key.php');
        else if (@file_exists('modules/'.$this->name.'/key.php'))
            @require_once ('modules/'.$this->name.'/key.php');                        
        $this->checkforupdates();

		$this->displayName = $this->l('Import Vouchers from CSV');
		$this->description = $this->l('This module imports voucher codes from CSV file. Supports PrestaShop in version 1.4.x - 1.5.x');
	}
    
    function checkforupdates(){
        if (isset($_GET['controller']) OR isset($_GET['tab'])){
            if (Configuration::get('update_'.$this->name) < (date("U")>86400)){
                $actual_version = importvoucherUpdate::verify($this->name,$this->mkey,$this->version);
            }
            if (importvoucherUpdate::version($this->version)<importvoucherUpdate::version(Configuration::get('updatev_'.$this->name))){
                $this->warning=$this->l('New version available, check www.MyPresta.eu for more informations');
            }
        }
    }


	/**
	 * Build a categories tree
	 *
	 * @param array $indexedCategories Array with categories where product is indexed (in order to check checkbox)
	 * @param array $categories Categories to list
	 * @param array $current Current category
	 * @param integer $id_category Current category id
	 */

    
	function install(){
        if (!(parent::install())
            OR !Configuration::updateValue('update_'.$this->name,'0')
            OR !Configuration::updateValue('IV_ROW_DELIMITER','\r')
            OR !Configuration::updateValue('IV_COL_DELIMITER',',')
            OR !Configuration::updateValue('IV_VOUCHER_TYPE','1')
            OR !Configuration::updateValue('IV_VOUCHER_CURRENCY','1')
            OR !Configuration::updateValue('IV_VOUCHER_MINCURRENCY','1')
            OR !Configuration::updateValue('IV_VOUCHER_VALUE','5')
            OR !Configuration::updateValue('IV_VOUCHER_TAX','1')
            OR !Configuration::updateValue('IV_VOUCHER_DESC','my default voucher')
            OR !Configuration::updateValue('IV_VOUCHER_Q','1')
            OR !Configuration::updateValue('IV_VOUCHER_QPU','1')
            OR !Configuration::updateValue('IV_VOUCHER_C','0')
            OR !Configuration::updateValue('IV_VOUCHER_CPR','0')
            OR !Configuration::updateValue('IV_VOUCHER_MINB','70')
            OR !Configuration::updateValue('IV_VOUCHER_DATETO','')
            OR !Configuration::updateValue('IV_VOUCHER_DATEFROM','')
            OR !Configuration::updateValue('IV_VOUCHER_CAT','')
            OR !Configuration::updateValue('IV_VOUCHER_ATTR','')
            OR !Configuration::updateValue('IV_CONDITION_ATTR','0')
            OR !Configuration::updateValue('IV_CONDITION','0')
            OR !Configuration::updateValue('IV_PARTIAL_USE','0')
            OR !Configuration::updateValue('IV_PRODUCTS','')
            ){
            return false;
        }
		return true;
	}
	
	public function psversion() {
		$version=_PS_VERSION_;
		$exp=$explode=explode(".",$version);
		return $exp[1];
	}   
    
    public function attrchecks($array,$id){
        if (isset($array[$id])){
            return 'checked="yes"';
        } else {
            return '';
        }
    }
	 
    public function catchecks($array,$id){
        if (isset($array[$id])){
            return 'checked="yes"';
        } else {
            return '';
        }
    }
    
    public function cattree(){
    	$var=$this->getconf();
        $exp=explode(",",$var['IV_VOUCHER_CAT']);
        foreach($exp as $key=>$value){
           $catchecks[$value]=true;    
        }
            
        $id_lang=1;//_USER_ID_LANG_;
          
        $id_lang=Configuration::get('PS_LANG_DEFAULT');
		$result = Category::getCategories($id_lang);
			            
	  	$cat_tree="";
	   		foreach ($result as $key=>$value){
	   			foreach ($value as $key1=>$value1){
	           	$cat_tree.="<tr>
					<td><input name='categoryBox[]' ".$this->catchecks($catchecks,$value1['infos']['id_category'])." type='checkbox' id='categoryBox".$value1['infos']['id_category']."' value='".$value1['infos']['id_category']."'/></td>
					<td>".$value1['infos']['name']."</td></tr>";
				}
	         }
	    return $cat_tree;
    }
    
    public function attributes_tree(){
        $var=$this->getconf();
        $exp=explode(",",$var['IV_VOUCHER_ATTR']);
        foreach($exp as $key=>$value){
           $attr[$value]=true;    
        }
        
        $id_lang=Configuration::get('PS_LANG_DEFAULT');
        $attribs="";
        foreach (Attribute::getAttributes($id_lang) AS $akey=>$avalue){
            $attribs.="<tr>
					<td><input name='attributesBox[]' ".$this->attrchecks($attr,$avalue['id_attribute'])." type='checkbox' value='".$avalue['id_attribute']."'></td>
					<td>".$avalue['id_attribute']."</td><td><strong>[".$avalue['group_type']."]</strong> ".$avalue['name']."</td></tr>";
        }
        return $attribs;
    }


    public function getconf(){
        $array['IV_COL_DELIMITER']=Configuration::get('IV_COL_DELIMITER');
        $array['IV_ROW_DELIMITER']=Configuration::get('IV_ROW_DELIMITER');
        $array['IV_VOUCHER_TYPE']=Configuration::get('IV_VOUCHER_TYPE');
        $array['IV_VOUCHER_CURRENCY']=Configuration::get('IV_VOUCHER_CURRENCY');
        $array['IV_VOUCHER_VALUE']=Configuration::get('IV_VOUCHER_VALUE');
        $array['IV_VOUCHER_TAX']=Configuration::get('IV_VOUCHER_TAX');
        $array['IV_VOUCHER_DESC']=Configuration::get('IV_VOUCHER_DESC');
        $array['IV_VOUCHER_Q']=Configuration::get('IV_VOUCHER_Q');
        $array['IV_VOUCHER_QPU']=Configuration::get('IV_VOUCHER_QPU');
        $array['IV_VOUCHER_C']=Configuration::get('IV_VOUCHER_C');
        $array['IV_VOUCHER_CPR']=Configuration::get('IV_VOUCHER_CPR');
        $array['IV_VOUCHER_DATETO']=Configuration::get('IV_VOUCHER_DATETO');
        $array['IV_VOUCHER_DATEFROM']=Configuration::get('IV_VOUCHER_DATEFROM');
        $array['IV_PARTIAL_USE']=Configuration::get('IV_PARTIAL_USE');
        $array['IV_VOUCHER_MINB']=Configuration::get('IV_VOUCHER_MINB');
        $array['IV_VOUCHER_CAT']=Configuration::get('IV_VOUCHER_CAT');
        $array['IV_VOUCHER_ATTR']=Configuration::get('IV_VOUCHER_ATTR');
        $array['IV_CONDITION']=Configuration::get('IV_CONDITION');
        $array['IV_CONDITION_ATTR']=Configuration::get('IV_CONDITION_ATTR');
        $array['IV_PRODUCTS']=Configuration::get('IV_PRODUCTS');
		$array['IV_VOUCHER_MINCURRENCY']=Configuration::get('IV_VOUCHER_MINCURRENCY');               
        return $array;
    }
    
    public function generateselect($colid){
        $form='
        <SELECT name="col'.$colid.'" style="font-size:12px; max-width:100px;">
        <option value="skip">skip column</option>
        <option value="voucher_code">voucher code</option>
        <option value="voucher_type">voucher type</option>
        <option value="description">description</option>
        <option value="value">value</option>
        <option value="quantity">quantity</option>
        <option value="quantity_per_user">quantity per user</option>
        <option value="cumulable">cumulable</option>
        <option value="cumulable_reduction">cumulable reduction</option>
        <option value="date_from">date from</option>
        <option value="date_to">date to</option>
        <option value="minimal_basket">minimal basket</option>
        <option value="currency">currency id</option>
        <option value="partial_use">partial use</option>
        </SELECT>';
        return $form;
    }


    public function insert_voucher($array_values){
        Configuration::updateValue('PS_CART_RULE_FEATURE_ACTIVE',"1");
    	if ($this->psver==5){
   			$var=$this->getconf();
            if ($var['IV_VOUCHER_C']==1){$var['IV_VOUCHER_C']=0;}else{$var['IV_VOUCHER_C']=1;}
   			$field['voucher_type']=$var['IV_VOUCHER_TYPE'];
	        $field['voucher_code']="";
	        $field['description']=$var['IV_VOUCHER_DESC'];
	        $field['value']=$var['IV_VOUCHER_VALUE'];
            $field['tax']=$var['IV_VOUCHER_TAX'];
	        $field['quantity']=$var['IV_VOUCHER_Q'];
	        $field['quantity_per_user']=$var['IV_VOUCHER_QPU'];
	        $field['cumulable']=$var['IV_VOUCHER_C'];
	        $field['cumulable_reduction']=$var['IV_VOUCHER_CPR'];
	        $field['date_to']=$var['IV_VOUCHER_DATETO'];
	        $field['date_from']=$var['IV_VOUCHER_DATEFROM'];
	        $field['minimal_basket']=$var['IV_VOUCHER_MINB'];
	        $field['currency']=$var['IV_VOUCHER_CURRENCY'];
	        $field['minimal_amount_currency']=$var['IV_VOUCHER_MINCURRENCY'];
            $field['partial_use']=$var['IV_PARTIAL_USE'];
	        
			foreach ($field as $k => $v){
	            if (isset($array_values[$k])){
	                ${$k}=trim($array_values[$k]);
	            } else {
	                ${$k}=trim($v);
	            }
	        }
			
			
			if ($var['IV_VOUCHER_TYPE']==3){$freeshp=1;}else{$freeshp=0;}
  			if ($var['IV_VOUCHER_TYPE']==2){$value2=$value;}else{$value2=0;}
  			if ($var['IV_VOUCHER_TYPE']==1){$value1=$value;}else{$value1=0;}
            
	           	$id_customer="0";
	           	$now=date("Y-m-d H:i:s");
	           	$active="1";
				//$date_from from array
				//$date_to from array
				//description from array
				//$quantity from array
				//$quantity_per_user from array
				$priority="1";
				//$partial_use="1";
				//$voucher_code from array
				//$minimum_amount from array as $minimal_basket
				$minimum_amount_tax="0";
				//$minimum_amount_currency from array as $minimal_amount_currency
				$minimum_amount_shipping="0";
				$country_restriction="0";
				$carrier_restriction="0";
				$group_restriction="0";
				$cart_rule_restriction=$field['cumulable'];
				$product_restriction="1";
				$shop_restriction="0";
				$free_shipping=$freeshp;
				$reduction_percent=$value1;
				$reduction_amount=$value2;
				$reduction_tax=$tax;
				//$reduction_currency from array as $currency
				$reduction_product="0";
				$gift_product="0";
				$gift_product_attribute="0";

	            $db = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule 
					(
						id_customer,
						date_from,
						date_to,
						description,
						quantity,
						quantity_per_user,
						priority,
						partial_use,
						code,
						minimum_amount,
						minimum_amount_tax,
						minimum_amount_currency,
						minimum_amount_shipping,
						country_restriction,
						carrier_restriction,
						group_restriction,
						cart_rule_restriction,
						product_restriction,
						shop_restriction,
						free_shipping,
						reduction_percent,
						reduction_amount,
						reduction_tax,
						reduction_currency,
						reduction_product,
						gift_product,
						gift_product_attribute,
						active,
						date_add,
						date_upd
					) VALUES ('$id_customer','$date_from','$date_to','$description','$quantity','$quantity_per_user','$priority','$partial_use','$voucher_code','$minimal_basket','$minimum_amount_tax','$minimal_amount_currency','$minimum_amount_shipping','$country_restriction','$carrier_restriction','$group_restriction','$cart_rule_restriction','$product_restriction','$shop_restriction','$free_shipping','$reduction_percent','$reduction_amount','$reduction_tax','$currency','$reduction_product','$gift_product','$gift_product_attribute','$active','$now','$now');
					");
					$lang=Configuration::get('PS_LANG_DEFAULT');
					$dbb = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_cart_rule FROM "._DB_PREFIX_."cart_rule WHERE code='$voucher_code'");
					$dbbb = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_lang (id_cart_rule,id_lang,name) VALUES ('{$dbb[0]['id_cart_rule']}','$lang','$description')");
                    
					
                    if ($var['IV_CONDITION']==1){
                        $dprg = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule_group (id_cart_rule,quantity) VALUES ('{$dbb[0]['id_cart_rule']}','1')");
    					$dprgid = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_product_rule_group FROM "._DB_PREFIX_."cart_rule_product_rule_group WHERE id_cart_rule='{$dbb[0]['id_cart_rule']}'");
    					$dprinsert = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule (id_product_rule_group,type) VALUES ('{$dprgid[0]['id_product_rule_group']}','categories')");
    					$dprid = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_product_rule FROM "._DB_PREFIX_."cart_rule_product_rule WHERE id_product_rule_group='{$dprgid[0]['id_product_rule_group']}'");
    					$exp=explode(",",Configuration::get('IV_VOUCHER_CAT'));															
    					foreach ($exp as $key=>$value){$catchecks[$value]=true;}
    					foreach ($catchecks as $idcat=>$categ){
    						$dprinsert = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule_value (id_product_rule,id_item) VALUES ('{$dprid[0]['id_product_rule']}','$idcat')");
    					}
                    }
                    if ($var['IV_CONDITION']==2){
                        $dprg = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule_group (id_cart_rule,quantity) VALUES ('{$dbb[0]['id_cart_rule']}','1')");
    					$dprgid = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_product_rule_group FROM "._DB_PREFIX_."cart_rule_product_rule_group WHERE id_cart_rule='{$dbb[0]['id_cart_rule']}'");
    					$dprinsert = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule (id_product_rule_group,type) VALUES ('{$dprgid[0]['id_product_rule_group']}','products')");
    					$dprid = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_product_rule FROM "._DB_PREFIX_."cart_rule_product_rule WHERE id_product_rule_group='{$dprgid[0]['id_product_rule_group']}'");
    					$impid=Configuration::get('IV_PRODUCTS');
                        $exp=explode(",",$impid);
                        foreach ($exp as $idcat=>$categ){
        				    $dprinsert = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule_value (id_product_rule,id_item) VALUES ('{$dprid[0]['id_product_rule']}','$categ')");
        				}
                    }
                    if ($var['IV_CONDITION_ATTR']==1){
                        $dprg = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule_group (id_cart_rule,quantity) VALUES ('{$dbb[0]['id_cart_rule']}','1')");
    					$dprgid = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_product_rule_group FROM "._DB_PREFIX_."cart_rule_product_rule_group WHERE id_cart_rule='{$dbb[0]['id_cart_rule']}'");
    					$dprinsert = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule (id_product_rule_group,type) VALUES ('{$dprgid[0]['id_product_rule_group']}','attributes')");
    					$dprid = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_product_rule FROM "._DB_PREFIX_."cart_rule_product_rule WHERE id_product_rule_group='{$dprgid[0]['id_product_rule_group']}'");
    					$exp=explode(",",Configuration::get('IV_VOUCHER_ATTR'));															
    					foreach ($exp as $key=>$value){$catchecks[$value]=true;}
    					foreach ($catchecks as $idcat=>$categ){
    						$dprinsert = Db::getInstance(_PS_USE_SQL_SLAVE_)->Execute("INSERT INTO "._DB_PREFIX_."cart_rule_product_rule_value (id_product_rule,id_item) VALUES ('{$dprid[0]['id_product_rule']}','$idcat')");
    					}
                    }
                                     
	   	}

    		
    	if ($this->psver==4){
	        $var=$this->getconf();
	        $field['voucher_type']=$var['IV_VOUCHER_TYPE'];
	        $field['voucher_code']="";
	        $field['description']=$var['IV_VOUCHER_DESC'];
	        $field['value']=$var['IV_VOUCHER_VALUE'];
	        $field['quantity']=$var['IV_VOUCHER_Q'];
	        $field['quantity_per_user']=$var['IV_VOUCHER_QPU'];
	        $field['cumulable']=$var['IV_VOUCHER_C'];
	        $field['cumulable_reduction']=$var['IV_VOUCHER_CPR'];
	        $field['date_to']=$var['IV_VOUCHER_DATETO'];
	        $field['date_from']=$var['IV_VOUCHER_DATEFROM'];
	        $field['minimal_basket']=$var['IV_VOUCHER_MINB'];
	        $field['currency']=$var['IV_VOUCHER_CURRENCY'];
	        
	        
	        foreach ($field as $k => $v){
	            if (isset($array_values[$k])){
	                ${$k}=trim($array_values[$k]);
	            } else {
	                ${$k}=trim($v);
	            }
	        }
	        
	        $now=date("Y-m-d H:i:s");
	        $cats = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT c.id_category FROM  "._DB_PREFIX_."category as c GROUP BY c.id_category");            
	        
	        $db = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("INSERT INTO "._DB_PREFIX_."discount 
	        (id_discount_type, id_currency, name, value, date_from, date_to, date_add, date_upd,minimal,quantity,quantity_per_user,active,cumulable,cumulable_reduction) 
	 		VALUES ('$voucher_type','$currency','$voucher_code','$value','$date_from','$date_to','$now','$now','$minimal_basket','$quantity','$quantity_per_user','1','$cumulable','$cumulable_reduction')");
	 
	        $lang=Configuration::get('PS_LANG_DEFAULT');
	        $dbb = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT id_discount FROM "._DB_PREFIX_."discount WHERE name='$voucher_code'");
	        $dbbb = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("INSERT INTO "._DB_PREFIX_."discount_lang 
	        (id_discount,id_lang,description) 
			VALUES ('{$dbb[0]['id_discount']}','$lang','$description')");
	       
	            $exp=explode(",",$var['IV_VOUCHER_CAT']);
	            foreach($exp as $key=>$value){
	                $catchecks[$value]=true;    
	            }
                
    	        foreach ($catchecks as $idcat=>$categ){
    	            $dbbb = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("INSERT INTO "._DB_PREFIX_."discount_category 
    	            (id_category, id_discount) 
    	            VALUES ('$idcat','{$dbb[0]['id_discount']}')");    
    	        }        
		}
    }

	public function getContent(){
        $var=$this->getconf();
        $output="";

        if (Tools::isSubmit('submit_vouchers')){
      
            $file=file_get_contents("..".$this->dir."{$_POST['filename']}");
            if ($var['IV_ROW_DELIMITER']=='\n'){$exp=explode("\n",$file);}
            if ($var['IV_ROW_DELIMITER']=='\r'){$exp=explode("\r",$file);}
            if ($var['IV_ROW_DELIMITER']=='\r\n'){$exp=explode("\r\n",$file);}
            if ($var['IV_ROW_DELIMITER']=='\n\r'){$exp=explode("\r\n",$file);}
            
            $columns="";
            foreach ($exp as $key=>$value){
                $first=1;
                $exprow=explode("{$var['IV_COL_DELIMITER']}","$exp[$key]");
                foreach ($exprow as $id=>$val){
                    ${"col".$id}=$_POST["col"."$id"];                    
                    if (!(${"col".$id}=="skip")){                    
                        $columns.=${"col".$id}.",";
                    }
                }
                if ($first==1){break;}    
            }
            $columns=substr($columns,0,-1);
            
            foreach ($exp as $key=>$value){
                if (isset($_POST["add"."$key"])){
                    $exprow=explode("{$var['IV_COL_DELIMITER']}","$exp[$key]");
                    $values="";
                    foreach ($exprow as $id=>$val){
                        ${"col".$id}=$_POST["col"."$id"];                    
                        if (!(${"col".$id}=="skip")){      
                            $values.="'$val',";
                            $array_values[${"col"."$id"}]=$val;
                        }
                    }
                    
                    $values=substr($values,0,-1);
                    
                    $this->insert_voucher($array_values);                    
                }           
            }
            $output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Vouchers added to database').'" />'.$this->l('Vouchers added to database').'</div>';            
        }
        
        
        if (Tools::isSubmit('delete_csv_file')){
            if (file_exists("..".$this->dir.$_POST['fcsv'])){
                unlink("..".$this->dir.$_POST['fcsv']);
            }
            $output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('CSV FILE DELETED').'" />'.$this->l('CSV FILE DELETED').'</div>';
        }
        
        if (Tools::isSubmit('importcsv')){
            $file=file_get_contents("..".$this->dir."{$_POST['importfile']}");
            $exp=NULL;
            if ($var['IV_ROW_DELIMITER']=='\n'){$exp=explode("\n",$file);}
            if ($var['IV_ROW_DELIMITER']=='\r'){$exp=explode("\r",$file);}
            if ($var['IV_ROW_DELIMITER']=='\r\n'){$exp=explode("\r\n",$file);}
            if ($var['IV_ROW_DELIMITER']=='\n\r'){$exp=explode("\r\n",$file);}
            
            $rows='<table border="1" style="margin-bottom:15px; border: 1px solid #c0c0c0;"><form action="'.$_SERVER['REQUEST_URI'].'" method="post"><input type="hidden" name="filename" value="'.$_POST['importfile'].'"/>';
            
            if (count($exp)>0){
	            foreach ($exp as $key=>$value){
	                $first="1";
	                $exprow=explode("{$var['IV_COL_DELIMITER']}","$exp[$key]");
	                $rows.="<tr><td>add</td>";
	                foreach ($exprow as $id=>$val){
	                    $rows.="<td>".$this->generateselect($id)."</td>";
	                }
	                $rows.="</tr>";
	                if ($first==1){break;}
	            }
            } 
            
            if (count($exp)>0){
	            foreach ($exp as $key=>$value){
	                $exprow=explode("{$var['IV_COL_DELIMITER']}","$exp[$key]");
	                $rows.="<tr>";
	                $rows.="<td><input type=\"checkbox\" checked=\"yes\" value=\"1\" name=\"add$key\"></td>";
	                foreach ($exprow as $id=>$val){
	                    $rows.="<td>$val</td>";
	                }
	                $rows.="</tr>";
	            }
            }
            $rows.="</table><input type='submit' name='submit_vouchers' value='".$this->l('Add vouchers')."' class='button'/></form>";
               
            $output .='<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Loaded to import').'" />'.$this->l('Loaded to import').'</div>';                
            $output .='<div style="margin-bottom:120px;">
                <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            	<fieldset style="">
                <legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('import file: ').$_POST['importfile'].'</legend>
                '.$rows.'
                </fieldset>
                </form>
            </div>';
        }        
        
		if (Tools::isSubmit('upload_csv')){
            $plik_tmp = $_FILES['upload_csv']['tmp_name']; 
            $plik_nazwa = $_FILES['upload_csv']['name']; 
            $plik_rozmiar = $_FILES['upload_csv']['size'];
            if(is_uploaded_file($plik_tmp)){
                $date=date("Y-m-d-h-i-s");
                if (move_uploaded_file($plik_tmp, '..'.$this->dir."$date.csv")){
                } 
            }
    		$output.='<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('CSV file uploaded').'" />'.$this->l('CSV file uploaded').'</div>';
		}   
        
        if (Tools::isSubmit('submit_settings')){
            $cats="";
            $count=count($_POST['categoryBox']);
            $l=0;
            foreach ($_POST['categoryBox'] AS $key=>$value){
                $l++;
                if ($l==$count){
                    $cats.=$value;
                } else {
                    $cats.="$value".",";
                }   
            }
            
            $atr="";
            $count=count($_POST['attributesBox']);
            $l=0;
            foreach ($_POST['attributesBox'] AS $key=>$value){
                $l++;
                if ($l==$count){
                    $atr.=$value;
                } else {
                    $atr.="$value".",";
                }   
            }
            
            Configuration::updateValue('IV_ROW_DELIMITER',"{$_POST['iv_row_delimiter']}");
            Configuration::updateValue('IV_COL_DELIMITER',"{$_POST['iv_col_delimiter']}");
            Configuration::updateValue('IV_VOUCHER_TYPE',"{$_POST['IV_VOUCHER_TYPE']}");
            Configuration::updateValue('IV_VOUCHER_CURRENCY',"{$_POST['IV_VOUCHER_CURRENCY']}");
            Configuration::updateValue('IV_VOUCHER_VALUE',"{$_POST['IV_VOUCHER_VALUE']}");
            Configuration::updateValue('IV_VOUCHER_TAX',"{$_POST['IV_VOUCHER_TAX']}");
            Configuration::updateValue('IV_VOUCHER_DESC',"{$_POST['IV_VOUCHER_DESC']}");
            Configuration::updateValue('IV_VOUCHER_Q',"{$_POST['IV_VOUCHER_Q']}");
            Configuration::updateValue('IV_VOUCHER_QPU',"{$_POST['IV_VOUCHER_QPU']}");
            Configuration::updateValue('IV_VOUCHER_C',"{$_POST['IV_VOUCHER_C']}");
            Configuration::updateValue('IV_VOUCHER_CPR',"{$_POST['IV_VOUCHER_CPR']}");
            Configuration::updateValue('IV_VOUCHER_MINB',"{$_POST['IV_VOUCHER_MINB']}");
            Configuration::updateValue('IV_VOUCHER_DATETO',"{$_POST['IV_VOUCHER_DATETO']}");
            Configuration::updateValue('IV_VOUCHER_DATEFROM',"{$_POST['IV_VOUCHER_DATEFROM']}");
            Configuration::updateValue('IV_PARTIAL_USE',"{$_POST['IV_PARTIAL_USE']}");
            Configuration::updateValue('IV_VOUCHER_MINCURRENCY',"{$_POST['IV_VOUCHER_MINCURRENCY']}");
            Configuration::updateValue('IV_CONDITION',"{$_POST['IV_CONDITION']}");
            Configuration::updateValue('IV_CONDITION_ATTR',"{$_POST['IV_CONDITION_ATTR']}");
            Configuration::updateValue('IV_PRODUCTS',"{$_POST['IV_PRODUCTS']}");
            Configuration::updateValue('IV_VOUCHER_CAT',"{$cats}");
            Configuration::updateValue('IV_VOUCHER_ATTR',"{$atr}");
            
            $output.='<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Settings Saved').'" />'.$this->l('Settings Saved').'</div>';
        }
        
        return "<div id='cssmenu'>
            <ul>
               <li class='active'><a><span>".$this->l('version:')." ".$this->version."</span></a></li>
               <li class=''><a href='http://mypresta.eu/contact.html' target='_blank'><span>".$this->l('Support')."</span></a></li>
               <li class=''><a href='http://mypresta.eu/modules/advertising-and-marketing/import-voucher-from-csv.html' target='_blank'><span>".$this->l('Updates')."</span></a></li>
               <li style='position:relative; display:inline-block; float:right; '><a href='http://mypresta.eu' target='_blank' title='prestashop modules'><img src='../modules/".$this->name."/logo-white.png' alt='prestashop modules' style=\"position:absolute; top:17px; right:16px;\"/></a></li>
            </ul>
            </div>".'
            <link href="../modules/'.$this->name.'/css.css" rel="stylesheet" type="text/css" />'.$output.$this->displayForm();
	}

    
    public function getcurrencies(){
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_); 
        $query = "SELECT * FROM `"._DB_PREFIX_."currency`";
        $array = $db->ExecuteS($query);
        return $array;
    }    

	public function displayForm(){
	   
       
	   
         $dir = opendir('..'.$this->dir);
         $count=0;
         while(false !== ($file = readdir($dir))){
             if (($file==".")||($file=="..")){} else {
                if(preg_match('@(.*)\.(csv)@i',$file)){
                     $filesarray[$count]['name']=$file;
                     $count++;   
                } 
            }
        } 
        $csvfiles="";
        
        
        if (isset($filesarray)){
            if (count($filesarray)>0){
                    foreach ($filesarray as $key=>$value){
                        $csvfiles=$csvfiles.'<div style="text-align:center; display:inline-block; padding:5px 10px; background:#FFF; border:1px solid #c0c0c0; margin-right:10px;"><a href="'._PS_BASE_URL_._MODULE_DIR_.'importvoucher/'.$value['name'].'" target="_blank" style="margin-bottom:10px; display:block; "><strong>'.$value['name'].'</strong></a><form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data"><input type="hidden" name="importfile" value="'.$value['name'].'"><input type="submit" value="'.$this->l('Import to database').'" name="importcsv" class="button"/></form><form action="'.$_SERVER['REQUEST_URI'].'" style="margin-top:10px" method="post" enctype="multipart/form-data"><input type="hidden" name="fcsv" value="'.$value['name'].'"><input type="submit" value="'.$this->l('Delete').'" name="delete_csv_file" class="button"/></form></div>';
                    }
                } else {
                    $csvfiles=$this->l('No Files');
                }
        } else {
            $csvfiles=$this->l('No Files');
        }
            	   

        $date_start=date("Y-m-d H:i:s");
        $date_u=date("U");
        $date_u=$date_u+12960000;
        $date_end=date("Y-m-d H:i:s",$date_u);
        
        $var=$this->getconf();
        $currencies=$this->getcurrencies();
        $vtp1=""; $vtp2=""; $vtp3="";
        if ($var['IV_VOUCHER_TYPE']==1){$vtp1="Selected=\"yes\"";}
        if ($var['IV_VOUCHER_TYPE']==2){$vtp2="Selected=\"yes\"";}
        if ($var['IV_VOUCHER_TYPE']==3){$vtp3="Selected=\"yes\"";}
        
        $vtypeslist="";
        $vtypeslist.='<option value="0" >'.$this->l('-- Check --').'</option>
                        <option value="1" '.$vtp1.'>'.$this->l('Discount on order (%)').'</option>
                        <option value="2" '.$vtp2.'>'.$this->l('Discount on order (amount)').'</option>
                        <option value="3" '.$vtp3.'>'.$this->l('Free shipping').'</option>';
                        

        
        $currencylist="";
        if (count($currencies)>0){
            foreach ($currencies as $key=>$value){
            if ($var['IV_VOUCHER_CURRENCY']==$value['id_currency']){$selected="selected='yes'";}else{$selected="";}
            $currencylist.="<option value='{$value['id_currency']}' $selected>{$value['sign']} {$value['name']}</option>";
            }            
        }
        
        $condition_0="";
        $condition_1="";
        $condition_2="";
        $attributes_checked="";
        
        $css_condition_1="display:none;";
        $css_condition_2="display:none;";
        $css_attributes_checked="display:none;";
        
        if ($var['IV_CONDITION']==0){$condition_0='checked="yes"';}
        if ($var['IV_CONDITION']==1){$condition_1='checked="yes"'; $css_condition_1="display:block;";}
        if ($var['IV_CONDITION']==2){$condition_2='checked="yes"'; $css_condition_2="display:block;";}
        if ($var['IV_CONDITION_ATTR']==1){$attributes_checked='checked="yes"'; $css_attributes_checked="display:block;";}
        
        $partial_use=Configuration::get('IV_PARTIAL_USE');
        
        $date_start=date("Y-m-d H:i:s");
        $date_u=date("U");
        $date_u=$date_u+12960000;
        $date_end=$var['IV_VOUCHER_DATETO'];

        $cat_tree="";
        $cat_tree=$this->cattree();		
        $attribs="";
        $attribs=$this->attributes_tree();
       	
		$curr="";
		$lengths="";
	   	for($in=5; $in<=32; $in++){$lengths=$lengths."<option value='$in'>$in</option>";}
       	foreach (Currency::getCurrencies() as $row=>$value){
       		if ($value['id_currency']==$var['IV_VOUCHER_MINCURRENCY']){$minselected="SELECTED=\"yes\"";}else{$minselected="";}
        	$curr=$curr."<option value='$value[id_currency]' $minselected>$value[sign]</option>";
       	}            
        
        includeDatepicker(array('IV_VOUCHER_DATETO'), true);
        includeDatepicker(array('IV_VOUCHER_DATEFROM'), true);
		return'
        <script type="text/javascript">
            function conditions(id){
                $(\'.condition\').hide();
                if (!id==0){
                    $(\'#condition_\'+id).show();
                }
            }
        </script>
        <div style="clear:both; display:block;">
        <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
    	<fieldset style="display:inline-block; width:430px; float:left; ">
            <legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('CSV file Settings').'</legend>
                <h3 style="margin-bottom:0px; padding-bottom:0px;">'.$this->l('General CSV File Settings').'</h3>
                <hr style="margin-top:5px;">
 			        <label>'.$this->l('ROW delimiter').'</label>
    					<div class="margin-form">
    						<input type="text" name="iv_row_delimiter" value="'.$var['IV_ROW_DELIMITER'].'">
                            <p class="clear">'.$this->l('setup the char for row delimiter (line)').'<br/>\n '.$this->l('new line in windows').'<br/>\r '.$this->l('new line in unix').'</p>
    					</div>
    				<label>'.$this->l('COLUMN delimiter').'</label>
    					<div class="margin-form">
    						<input type="text" name="iv_col_delimiter" value="'.$var['IV_COL_DELIMITER'].'">
                            <p class="clear">'.$this->l('setup the char for column delimiter (variables in line)').'</p>
    					</div>
                <h3 style="margin-bottom:0px; padding-bottom:0px;">'.$this->l('Default Voucher Settings').'</h3>
                <hr style="margin-top:5px;">
                <div style="clear:both; display:block; margin-bottom:10px; "><p class="warning" style="max-width:390px;">
                '.$this->l('If some collumns in CSV file doesn\'n exists, module will generate vouchers with this specification').':
                </p></div>                                        
       		    <label>'.$this->l('Voucher type').'</label>
    			<div class="margin-form">
                    <select type="text" name="IV_VOUCHER_TYPE" style="max-width:200px;">'.$vtypeslist.'</select>
                    <p class="clear">'.$this->l('Select the voucher type').'</p>
   				</div>
       		    <label>'.$this->l('Voucher currency').'</label>
    			<div class="margin-form">
                    <select type="text" name="IV_VOUCHER_CURRENCY" style="max-width:200px;">'.$currencylist.'</select>
                    <p class="clear">'.$this->l('Select the default currency for voucher value').'</p>
   				</div>
                <label>'.$this->l('Voucher value').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_VALUE" value="'.$var['IV_VOUCHER_VALUE'].'">
                        <p class="clear">type the default voucher value (in % or in currency - but without signs) - decimal separator: . (dot)</p>
                </div>
                <label>'.$this->l('Voucher value tax').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_TAX" value="'.$var['IV_VOUCHER_TAX'].'">
                        <p class="clear">Voucher value - tax indluded / excluded. 1: included, 0: excluded</p>
                </div>
                
                <label>'.$this->l('Voucher description').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_DESC" value="'.$var['IV_VOUCHER_DESC'].'">
                        <p class="clear">Type the default voucher description here</p>
                </div>                
                <label>'.$this->l('Voucher quantity').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_Q" value="'.$var['IV_VOUCHER_Q'].'">
                        <p class="clear">Type the default voucher quantity. Default: 1 means: one voucher for use</p>
                </div>
                
                <label>'.$this->l('Voucher quantity per user').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_QPU" value="'.$var['IV_VOUCHER_QPU'].'">
                        <p class="clear">Type the default voucher quantity per user. Default: 1 means: one voucher code for one user</p>
                </div>   
                             
                <label>'.$this->l('Voucher cumulable with other codes').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_C" value="'.$var['IV_VOUCHER_C'].'">
                        <p class="clear">1 if you want cumulable mode,<br/>0 if you dont want</p>
                </div> 
                               
                <label>'.$this->l('Voucher cumulable with price reductions').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_CPR" value="'.$var['IV_VOUCHER_CPR'].'">
                        <p class="clear">1 if you want cumulable with price reduction,<br/>0 if you dont want</p>
                </div>

                <label>'.$this->l('Voucher minimal baset value').'</label>
                <div class="margin-form">
                    <input type="text" name="IV_VOUCHER_MINB" value="'.$var['IV_VOUCHER_MINB'].'" style="width:80px;">
             			<select id="IV_VOUCHER_MINCURRENCY" name="IV_VOUCHER_MINCURRENCY" style="float:left; margin-left:10px;width:50px;">
                       		<option value="0">--</option>
                                '.$curr.'
                        </select>
                        <p class="clear">Setup the minimum basket value, from which voucher code will be active. Decimal separator: . (dot)</p>
                </div>


				<label>'.$this->l('Date From:').' </label>
				<div class="margin-form">
					<input type="text" size="20" id="IV_VOUCHER_DATEFROM" name="IV_VOUCHER_DATEFROM" value="'.$date_start.'"/>
					<p class="clear">'.$this->l('Set the start date of voucher code').'<br />'.$this->l('Format: YYYY-MM-DD HH:MM:SS').'</p>
				</div>
				
				
				<label>'.$this->l('Date To:').' </label>
				<div class="margin-form">
					<input type="text" size="20" id="IV_VOUCHER_DATETO" name="IV_VOUCHER_DATETO" value="'.$date_end.'"/>
					<p class="clear">'.$this->l('End date/time at which voucher is no longer valid').'<br />'.$this->l('Format: YYYY-MM-DD HH:MM:SS').'</p>
				</div>
                
                <label>'.$this->l('Partial use:').' </label>
				<div class="margin-form">
					<input type="text" size="20" id="IV_PARTIAL_USE" name="IV_PARTIAL_USE" value="'.$partial_use.'"/>
					<p class="clear">'.$this->l('0 - off | 1 - on | PS 1.5+ only').'</p>
				</div>

                <label>'.$this->l('Advanced Conditions').'</label>                
                <div class="margin-form">
                    <input type="radio" name="IV_CONDITION" value="0" onchange="conditions(0);" '.$condition_0.'/> '.$this->l('Turn Off').'<br/>
                    <input type="radio" name="IV_CONDITION" value="1" onchange="conditions(1);" '.$condition_1.'/> '.$this->l('Select categories').'<br/>
                    <input type="radio" name="IV_CONDITION" value="2" onchange="conditions(2);" '.$condition_2.'/> '.$this->l('Select products (only PS 1.5+)').' <br/>
                    <input type="checkbox" name="IV_CONDITION_ATTR" value="1" onchange="conditions(10);" '.$attributes_checked.'/> '.$this->l('Select attributes (only PS 1.5+)').' <br/>
                </div>
                
                
                <div class="clear condition" id="condition_10" style="'.$css_attributes_checked.'">
				<label>'.$this->l('Attributes:').' </label>
					<div class="margin-form">
							<table cellspacing="0" cellpadding="0" class="table" style="width: 220px; max-width:220px;">
					    		<tr>
				 					<th colspan="1" style="max-width:10px; width:10px;"><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'attributesBox[]\', this.checked)" /></th>
                                    <th>'.$this->l('ID').'</th>
			    					<th>'.$this->l('Attribute').'</th>
	        					</tr>
                            '.$attribs.'
						    </table>
						    <p style="padding:0px; margin:0px 0px 10px 0px;">'.$this->l('Select attributes').'</p>
				    </div>
                </div>
                
 				<div class="clear condition" id="condition_1" style="'.$css_condition_1.'">
				<label>'.$this->l('Categories:').' </label>
					<div class="margin-form">
							<table cellspacing="0" cellpadding="0" class="table" style="width: 220px; max-width:220px;">
					    		<tr>
				 					<th style="max-width:10px; width:10px;"><input type="checkbox" name="checkme" class="noborder" onclick="checkDelBoxes(this.form, \'categoryBox[]\', this.checked)" /></th>
			    					<th>'.$this->l('Name').'</th>
	        					</tr>
                            '.$cat_tree.'
						    </table>
						    <p style="padding:0px; margin:0px 0px 10px 0px;">'.$this->l('Mark all checkbox(es) of categories to which the discount is to be applied').'</p>
				    </div>
                </div>
                
                
                <div class="clear condition" id="condition_2" style="'.$css_condition_2.'">
                    <label>'.$this->l('Products:').' </label>
                    <div class="margin-form">
                        <input type="text" name="IV_PRODUCTS" value="'.configuration::get('IV_PRODUCTS').'" />
                        <p style="padding:0px; margin:0px 0px 10px 0px;">'.$this->l('Add products by ID, separate by commas').'</p>
                    </div>
                </div>
                        
                                                                           
                                                                                 
                <center><input type="submit" name="submit_settings" value="'.$this->l('Save Settings').'" class="button" /></center>                                                   
        </fieldset>
        </form>        

        <form action="'.$_SERVER['REQUEST_URI'].'" method="post" enctype="multipart/form-data">
            <fieldset style="display:inline-block; width:430px; float:right;">        
                <legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Upload CSV File').'</legend>
                <h3 style="margin-bottom:0px; padding-bottom:0px;">'.$this->l('Upload form').'</h3>
                <hr style="margin-top:5px;">
                    <input type="file" name="upload_csv" style="margin-left:100px;">
                <center><input style="margin-top:10px;" type="submit" name="upload_csv" value="'.$this->l('Upload File').'" class="button" /></center>               
            </fieldset>   
        </form>
        
        
        <form action="'.$_SERVER['REQUEST_URI'].'" method="post">
            <fieldset style="display:inline-block; width:430px; float:right; margin-top:15px;">
    		<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Uploaded CSV files').'</legend>
            '.$csvfiles.'
            </fieldset>
        </form>
         
        </div>

        ';
	}  
}

class importvoucherUpdate extends importvoucher {  
    public static function version($version){
        $version=(int)str_replace(".","",$version);
        if (strlen($version)==3){$version=(int)$version."0";}
        if (strlen($version)==2){$version=(int)$version."00";}
        if (strlen($version)==1){$version=(int)$version."000";}
        if (strlen($version)==0){$version=(int)$version."0000";}
        return (int)$version;
    }
    
    public static function encrypt($string){
        return base64_encode($string);
    }
    
    public static function verify($module,$key,$version){
        if (ini_get("allow_url_fopen")) {
             if (function_exists("file_get_contents")){
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module='.$module."&version=".self::encrypt($version)."&lic=$key&u=".self::encrypt(_PS_BASE_URL_.__PS_BASE_URI__));
             }
        }
        Configuration::updateValue("update_".$module,date("U"));
        Configuration::updateValue("updatev_".$module,$actual_version); 
        return $actual_version;
    }
}
?>
