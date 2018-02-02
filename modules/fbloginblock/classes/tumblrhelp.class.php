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

class tumblrhelp {
	private $_http_host;
    private $_http_referer;
    private $_name;


    private $_social_type = 53;
    
	
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
			    	// insert record into customerXtumblr table
					$sql = 'INSERT into `'._DB_PREFIX_.'tumblr_spm` SET
								   user_id = '.(int)$result_dublicate['user_id'].', 
								   tumblr_id = "'.pSQL($id).'", 
								   id_shop = '.(int)$this->getIdShop().'';
					Db::getInstance()->Execute($sql);
					$auth = 1;
			
				}

                $http_referer = isset($data['http_referer_custom'])?$data['http_referer_custom']:'';


                ## add new functional for auth and create user ##
                $first_name = $this->deldigit(pSQL($this->translite($data['username'])));
                $last_name = $this->deldigit(pSQL($this->translite($data['username'])));


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
                        'tumblr_id'=>$id,
                        'type'=>$this->_social_type,
                        'auth'=>$auth,
                    )
                );


                ## add new functional for auth and create user ##
				

			       
			 }
	}
	
	 public function checkExist($id){
	 	
	 	$result = Db::getInstance()->ExecuteS('SELECT `user_id`
					FROM `'._DB_PREFIX_.'tumblr_spm`
					WHERE `tumblr_id` = "'.pSQL($id).'" AND id_shop = '.(int)$this->getIdShop().'
					LIMIT 1');
			$customer_id = isset($result[0]['user_id'])?(int)$result[0]['user_id']:0;
		return $customer_id;
	 }
	
	
public function checkForDublicate($data){
		//chek for dublicate
	
			if(version_compare(_PS_VERSION_, '1.5', '>')){
				$sql = '
		        	SELECT * FROM `'._DB_PREFIX_   .'customer` 
			        WHERE  `id_customer` = \''.(int)($data['id_customer']).'\'
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
    
    
    public function tumblrLogin($_data){
    	
    	
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/http.php';
    	include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/oauth_client.php';
    	
    	 
    	$name_module = $this->_name;
    	 
    	$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
    	 
        if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}    	 
    	 
    	 
    	$client = new oauth_client_class();
    	$client->server = 'Tumblr';
    	 
    	 
    	include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
    	$obj_module = new $name_module();
    	$redirect_uri = $obj_module->getRedirectURL(array('typelogin'=>'tumblr','is_settings'=>1));
    	$client->redirect_uri = $redirect_uri;
    	 
    	 
    	$tuci = Configuration::get($name_module.'tuci');
    	$tuci = trim($tuci);
    	$tucs = Configuration::get($name_module.'tucs');
    	$tucs = trim($tucs);
    	 
    	
    	 
    	$client->client_id = $tuci;
    	$application_line = __LINE__;
    	$client->client_secret = $tucs;
    	 
    	if(Tools::strlen($client->client_id) == 0
    			|| Tools::strlen($client->client_secret) == 0)
    		die('Please go to Dropox Connect Developer Center page '.
    				'https://www.tumblr.com/oauth/apps and create a new'.
    				'application, and in the line '.$application_line.
    				' set the client_id to API Key and client_secret with API Secret. '.
    				'The callback URL must be '.$client->redirect_uri.' but make sure '.
    				'the domain is valid and can be resolved by a public DNS.');
    	 
    	/* API permissions
    	 */
    	$client->scope = 'email';
    	if(($success = $client->Initialize()))
    	{
    		if(($success = $client->Process()))
    		{
    			if(Tools::strlen($client->authorization_error))
    			{
    				$client->error = $client->authorization_error;
    				$success = false;
    			}
    			elseif(Tools::strlen($client->access_token))
    			{
    				$success = $client->CallAPI(
    						'https://api.tumblr.com/v2/user/info',
    						'GET', array(), array('FailOnAccessError'=>true), $user);
    			}
    		}
    		$success = $client->Finalize($success);
    	}
    	if($client->exit)
    		exit;
    	if($success)
    	{
    	
    		$last_name = $user->response->user->name;
    		//$first_name = $user->response->user->name;
    		
    		
    		$data_profile = array('username'=>$last_name,
    							  'id'=>md5($last_name),
    							   'http_referer_custom'=>$http_referer
    							  );
    		
    		$this->login($data_profile);
    		
    		 
    	}
    	else
    	{
    		echo 'Error:'.HtmlSpecialChars($client->error);
    	}
    	
    }


    public function insertCustomerXTumblr($data){

        $tumblr_id = $data['tumblr_id'];
        $insert_id = $data['insert_id'];
        $id_shop = $data['id_shop'];

        $sql_exists= 'SELECT `user_id`
					FROM `'._DB_PREFIX_.'tumblr_spm`
					WHERE `tumblr_id` = "'.pSQL($tumblr_id).'" AND id_shop = '.(int)$id_shop.'
					LIMIT 1';
        $result_exists = Db::getInstance()->ExecuteS($sql_exists);
        $user_id = isset($result_exists[0]['user_id'])?$result_exists[0]['user_id']:0;
        if($user_id){
            $sql_del = 'DELETE FROM `'._DB_PREFIX_.'tumblr_spm` WHERE `user_id` = '.(int)($user_id).'
							AND id_shop = '.(int)$id_shop.'';
            Db::getInstance()->Execute($sql_del);
        }

        $sql = 'INSERT into `'._DB_PREFIX_.'tumblr_spm` SET
						   user_id = '.(int)$insert_id.',
						   tumblr_id = "'.pSQL($tumblr_id).'" ,
						   id_shop = '.(int)$id_shop.'
							';
        Db::getInstance()->Execute($sql);
    }
}