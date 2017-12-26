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

class pinteresthelp {
	private $_http_host;
    private $_http_referer;
    private $_name;

    private $_social_type = 54;
    
	
	public function __construct(){
		
		$this->_name = "fbloginblock";
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$this->_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}


        if (version_compare(_PS_VERSION_, '1.7', '<')){
            require_once(_PS_MODULE_DIR_.$this->_name.'/backward_compatibility/backward.php');
        }

        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            require_once(_PS_MODULE_DIR_ . $this->_name . '/backward_compatibility/backward_functions.php');
        }
		
	
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}
	
	public function translite($str){
	
		$str  = str_replace(array('"','№','\\','%',';',"®","'",'"','`','?','!','.','=',':','&','+',',','’', ')', '(', '$', '{', '}','/', "\\",'#','\'','#174;','#39;','#160;','#246;','™','&amp;','amp;'), array(''), $str );
		
	$arrru = array ("А","а","Б","б","В","в","Г","г","Д","д","Е","е","Ё","ё","Ж","ж","З","з","И","и","Й","й","К","к","Л","л","М","м","Н","н", "О","о","П","п","Р","р","С","с","Т","т","У","у","Ф","ф","Х","х","Ц","ц","Ч","ч","Ш","ш","Щ","щ","Ъ","ъ","Ы","ы","Ь", "ь","Э","э","Ю","ю","Я","я",
    " ","-",",","«","»","+","/","(",")",".");
		
    	$arren = array ("a","a","b","b","v","v","g","g","d","d","e","e","e","e","zh","zh","z","z","i","i","y","y","k","k","l","l","m","m","n","n", "o","o","p","p","r","r","s","s","t","t","u","u","ph","f","h","h","c","c","ch","ch","sh","sh","sh","sh","","","i","i","","","e", "e","yu","yu","ya","ya",
    			"-","-","","","","","","","","","");
    
    	$textout = '';
    	$textout = str_replace($arrru,$arren,$str);

        $textout = str_replace(array('--','-','_'),array(''),$textout);
    	return Tools::strtolower($textout);
	}
	
	 private function deldigit($str){
    	$arr_out = array('');
		$arr_in = array(0,1,2,3,4,5,6,7,8,9);

		$textout = str_replace($arr_in,$arr_out,$str);
		
		return $textout;
    
    }
    


	
	
	
	public function login($data){
		
		$id = isset($data['id'])?$data['id']:0;
			  
	  	if ($id){
			  		
			  	$result = $this->checkExist($id);
			         
			  	$result_dublicate = $this->checkForDublicate(array('id_customer'=>$result));
			    $exists_mail = $result_dublicate['exists_mail'];
			        
			    $auth = 0;
				if($result && $exists_mail){
					$auth = 1;
				}
				
			    if(!$result && $exists_mail){
			    	// insert record into customerXpinterest table
					$sql = 'INSERT into `'._DB_PREFIX_.'pinterest_spm` SET
								   user_id = '.(int)$result_dublicate['user_id'].', 
								   pinterest_id = '.(int)$id.', 
								   id_shop = '.(int)$this->getIdShop().'';
					Db::getInstance()->Execute($sql);
					$auth = 1;
			
				}
				

			    $http_referer = isset($data['http_referer_custom'])?$data['http_referer_custom']:'';

                ## add new functional for auth and create user ##


                $first_name = $this->deldigit(pSQL($this->translite($data['first_name'])));
                $last_name = $this->deldigit(pSQL($this->translite($data['last_name'])));
                if($auth == 1){
                    $email = $result_dublicate['email'];
                } else {
                    $email = null;
                }


                $data_profile = array(
                    'email'=>$email,
                    'first_name'=>$first_name,
                    'last_name'=>$last_name,


                );

                include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
                $userhelp = new userhelp();
                $userhelp->userLog(
                    array(
                        'data_profile'=>$data_profile,
                        'http_referer_custom'=>$http_referer,
                        'pinterest_id'=>$id,
                        'type'=>$this->_social_type,
                        'auth'=>$auth,
                    )
                );


                ## add new functional for auth and create user ##



			       
			 }
	}
	
	 public function checkExist($id){
	 	
	 	$result = Db::getInstance()->ExecuteS('SELECT `user_id`
					FROM `'._DB_PREFIX_.'pinterest_spm`
					WHERE `pinterest_id` = '.(int)($id).' AND id_shop = '.(int)$this->getIdShop().'
					LIMIT 1');
			$customer_id = isset($result[0]['user_id'])?(int)$result[0]['user_id']:0;
		return $customer_id;
	 }
	
	
public function checkForDublicate($data){
		//chek for dublicate
	
			if(version_compare(_PS_VERSION_, '1.5', '>')){
				$sql = '
		        	SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        WHERE `id_customer` = \''.(int)($data['id_customer']).'\'
			        AND id_shop = '.(int)$this->getIdShop().' 
			        AND `deleted` = 0 '.(@defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
			        ';
			} else {
				$sql = '
		        	SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        WHERE  `id_customer` = \''.(int)($data['id_customer']).'\'
			        AND `deleted` = 0 '.(@defined(_MYSQL_ENGINE_)?"AND `is_guest` = 0":"").'
			        ';
			}
			$result_exists_mail = Db::getInstance()->GetRow($sql);

        ## if customer disabled ##
        if(!empty($result_exists_mail) && $result_exists_mail['active'] == 0){
            include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
            $obj = new $this->_name();
            $data_tr = $obj->translateCustom();
            echo $data_tr['disabled'];exit;
        }
        ## if customer disabled ##


        $email = isset($result_exists_mail['email'])?$result_exists_mail['email']:null;

        if($result_exists_mail)
            return array('exists_mail' => 1, 'email'=>$email, 'user_id' => $result_exists_mail['id_customer']);
        else
            return array('exists_mail' => 0, 'email'=>$email, 'user_id' =>0);

	}
	
private function getIdShop(){
    	if(version_compare(_PS_VERSION_, '1.5', '>')){
        	$id_shop = Context::getContext()->shop->id;
        } else {
        	$id_shop = 0;
        }
        return $id_shop;
    }
    
    
    private function _redirect($url){
    
          redirect_custom_fbloginblock($url);
          
    }
    
    
    public function pinterestLogin($_data){
    	
    	
    	$name_module = $this->_name;
    	 
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	 
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}    	 
    	 
    	 
    	include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
    	$obj_module = new $name_module();
    	$redirect_uri = $obj_module->getRedirectURL(array('typelogin'=>'pinterest','is_settings'=>1));
    	//$redirect_uri = "https://demostoreprestashop.com/demo/prestashop1611/modules/fbloginblock/pinterest.php";

        //$redirect_uri = "https://demostoreprestashop.com/demo/prestashop1700/modules/fbloginblock/pinterest.php";
    	
    	//var_dump($redirect_uri);exit;
    	 
    	$pici = Configuration::get($name_module.'pici');
    	$pici = trim($pici);
    	$pics = Configuration::get($name_module.'pics');
    	$pics = trim($pics);
    	 
    	
    	$code = Tools::getValue('code');
    	
    	if($code)
    	{
    		$curl = curl_init( 'https://api.pinterest.com/v1/oauth/token' );
    		curl_setopt( $curl, CURLOPT_POST, true );
    		curl_setopt( $curl, CURLOPT_POSTFIELDS, array(
    				'client_id' => $pici,
    				'client_secret' => $pics,
    				'code' => $code, // The code from the previous request
    				'grant_type' => 'authorization_code'
    		) );
    		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1);
    		$auth = curl_exec( $curl );
    	
    		
    		//var_dump($auth);
    		
    		$secret =  Tools::jsonDecode($auth);
    		 
    		$access_key = $secret->access_token;
    	
    		$user_url = "https://api.pinterest.com/v1/me/?access_token=".$access_key;
    		
    		$response = Tools::file_get_contents($user_url);
    		
    		$user = Tools::jsonDecode( $response );
    		
    		//var_dump($user);
    		
    	
    		$last_name = isset($user->data->last_name)?$user->data->last_name:'';
    		$first_name = isset($user->data->first_name)?$user->data->first_name:'';
    		
    		if(Tools::strlen($last_name)==0)
    			$last_name = $first_name;
    		if(Tools::strlen($first_name)==0)
    			$first_name = $last_name;
    		
    		
    		$id = $user->data->id;


           $data_profile = array('last_name'=>$last_name,
    							  'first_name'=>$first_name,
    							  'id'=>$id,
    							   'http_referer_custom'=>$http_referer
    							  );

    		$this->login($data_profile);
    	
    	} else {
    		$redirect_uri = "https://api.pinterest.com/oauth/?response_type=code&redirect_uri=".$redirect_uri."&client_id=".$pici."&scope=read_public,write_public&state=768uyFys";
    		redirect_custom_fbloginblock($redirect_uri);
    		exit;
    	}
    	
    	
    	
    	
    }


    public function insertCustomerXPinterest($data){

        $pinterest_id = $data['pinterest_id'];
        $insert_id = $data['insert_id'];
        $id_shop = $data['id_shop'];

        // insert record into customerXPinterest table
        $sql_exists= 'SELECT `user_id`
					FROM `'._DB_PREFIX_.'pinterest_spm`
					WHERE `pinterest_id` = '.(int)($pinterest_id).' AND id_shop = '.(int)$id_shop.'
					LIMIT 1';
        $result_exists = Db::getInstance()->ExecuteS($sql_exists);
        $user_id = isset($result_exists[0]['user_id'])?$result_exists[0]['user_id']:0;
        if($user_id){
            $sql_del = 'DELETE FROM `'._DB_PREFIX_.'pinterest_spm` WHERE `user_id` = '.(int)($user_id).'
							AND id_shop = '.(int)$id_shop.'';
            Db::getInstance()->Execute($sql_del);
        }

        $sql = 'INSERT into `'._DB_PREFIX_.'pinterest_spm` SET
						   user_id = '.(int)$insert_id.',
						   pinterest_id = '.(int)$pinterest_id.' ,
						   id_shop = '.(int)$id_shop.'
							';
        Db::getInstance()->Execute($sql);


    }
    
}