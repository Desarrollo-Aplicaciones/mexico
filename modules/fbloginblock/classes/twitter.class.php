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

include_once(dirname(__FILE__).'/../lib/twitteroauth/twitteroauth.php');
 
class twitter {
	private $consumer_key;
    private $consumer_secret;
    private $oauth_callback;
    private $_http_host;
    private $_http_referer;
    private $_name;
    private $_redirect_url;

    private $_social_type = 2;
    
    
	
	public function __construct($data = null){
		$this->consumer_key = isset($data['key'])?$data['key']:'';
		$this->consumer_secret = isset($data['secret'])?$data['secret']:'';
		$this->oauth_callback = isset($data['callback'])?$data['callback']:'';
		$this->_http_referer = isset($data['http_referer'])?$data['http_referer']:'';

		$this->_name = "fbloginblock";
		
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
		} else {
			$this->_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}
		
		
		include_once _PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php';
		$obj_module = new $this->_name();
		$this->_redirect_url = $obj_module->getRedirectURL(array('typelogin'=>'twitter'));

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
	

    
    $str  = str_replace(array('@','"','№','\\','%',';',"®","'",'"','`','?','!','.','=',':','&','+',',','’', ')', '(', '$', '{', '}','/', "\\",'#','\'','#174;','#39;','#160;','#246;','™','&amp;','amp;'), array(''), $str );
		
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
    
	


	
	public function connect(){
		
        if ($this->consumer_key === '' || $this->consumer_secret === '') {
          echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
          exit;
        }
        
        $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret);

        /* Get temporary credentials. */
        $request_token = $connection->getRequestToken($this->oauth_callback);        
        /* Save temporary credentials to session. */
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
          case 200:
            /* Build authorize URL and redirect user to Twitter. */
            $url = $connection->getAuthorizeURL($token);
            //var_dump($url);
             $this->_redirect($url);
            break;
          default:
            /* Show notification if something went wrong. */
            echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
	}
	
	public function callback(){

        $prefix_uri = '?';
        if(version_compare(_PS_VERSION_, '1.6', '>')){
            $prefix_uri = "&";
        }

	 	if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
          $_SESSION['oauth_status'] = 'oldtoken';
          
          
          $this->_redirect($this->_redirect_url.$prefix_uri.'action=connect');
	 	  
          
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret,
        							 $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $_SESSION['access_token'] = $access_token;

        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
          /* The user has been verified and the access tokens can be saved for future use */
          $_SESSION['status'] = 'verified';
         
          
          $this->_redirect($this->_redirect_url.$prefix_uri.'action=login');
         
        } else {
          /* Save HTTP status for error dialog on connnect page.*/

            $this->_redirect($this->_redirect_url.$prefix_uri.'action=connect');
          
        }
		
	}
	
	
	
	
	public function login(){
		
		if (empty($_SESSION['access_token']) 
				|| empty($_SESSION['access_token']['oauth_token'])
				|| empty($_SESSION['access_token']['oauth_token_secret'])
				) 
				{
			     	$this->connect();
			    }
			    
			 /* Get user access tokens out of the session. */
			 $access_token = $_SESSION['access_token'];
			
			 /* Create a TwitterOauth object with consumer/user tokens. */
			 $connection = new TwitterOAuth($this->consumer_key,$this->consumer_secret, 
			   								$access_token['oauth_token'], 
			   								$access_token['oauth_token_secret']);
			      								
			  /* If method is set change API call made. Test is called by default. */
			  $content = $connection->get('account/verify_credentials',array('include_email' => 'true'));



			  
	  if ($content->id){
			  		
			  	$result = $this->checkExist($content->id);

                $email_current = isset($content->email)?$content->email:null;
			         
			  	$result_dublicate = $this->checkForDublicate(
			    	array(//'email'=>Tools::strtolower($this->translite($content->name))."@twitter.com"
			    		 'id_customer'=>$result)
			    );
			    $exists_mail = $result_dublicate['exists_mail'];


			        
			    $auth = 0;
				if($result && $exists_mail){
					$auth = 1;
				}
				
			    if(!$result && $exists_mail){
			    	// insert record into customerXtwitter table
					$sql = 'INSERT into `'._DB_PREFIX_.'tw_customer` SET
								   user_id = '.(int)$result_dublicate['user_id'].', 
								   twitter_id = '.(int)$content->id.', 
								   id_shop = '.(int)$this->getIdShop().'';
					Db::getInstance()->Execute($sql);
					$auth = 1;
			
				}

                  ## add new functional for auth and create user ##
                  $first_name = $this->deldigit(pSQL($this->translite($content->name)));
                  $last_name = $this->deldigit(pSQL($this->translite($content->name)));
                  if($auth == 1){
                      $email = $result_dublicate['email'];
                  } else {
                      $email = null;
                  }


                  $data_profile = array(
                      'email'=>isset($email_current)?$email_current:$email,
                      'first_name'=>$first_name,
                      'last_name'=>$last_name,


                  );

                    include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
                  $userhelp = new userhelp();
                  $userhelp->userLog(
                      array(
                          'data_profile'=>$data_profile,
                          'http_referer_custom'=>$this->_http_referer,
                          'twitter_id'=>$content->id,
                          'type'=>$this->_social_type,
                          'auth'=>$auth,

                      )
                  );


                  ## add new functional for auth and create user ##


			      
			       
			 }
	}
	
	 public function checkExist($id){
	 	
	 	$result = Db::getInstance()->ExecuteS('SELECT `user_id`
					FROM `'._DB_PREFIX_.'tw_customer`
					WHERE `twitter_id` = '.(int)($id).' AND id_shop = '.(int)$this->getIdShop().'
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
			        WHERE `id_customer` = \''.(int)($data['id_customer']).'\'
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
    
    
    
    public function twitterLogin($_data){
    	
    	$action = $_data['action'];


        if(version_compare(_PS_VERSION_, '1.7', '>')) {
            session_start_fbloginblock();
        }
    	
    	switch($action){
    		case 'callback':
    			$this->callback();
    			break;
    		case 'connect':
    			$this->connect();
    			break;
    		case 'login':
    			$this->login();
    			break;
    		default:
    			$this->login();
    	
    			
    		break;
    	}
    }


    public function insertCustomerXTwitter($data){

        $twitter_id = $data['twitter_id'];
        $insert_id = $data['insert_id'];
        $id_shop = $data['id_shop'];

        $sql_exists= 'SELECT `user_id`
					FROM `'._DB_PREFIX_.'tw_customer`
					WHERE `twitter_id` = '.(int)($twitter_id).' AND id_shop = '.(int)$id_shop.'
					LIMIT 1';
        $result_exists = Db::getInstance()->ExecuteS($sql_exists);
        $user_id = isset($result_exists[0]['user_id'])?$result_exists[0]['user_id']:0;
        if($user_id){
            $sql_del = 'DELETE FROM `'._DB_PREFIX_.'tw_customer` WHERE `user_id` = '.(int)($user_id).'
							AND id_shop = '.(int)$id_shop.'';
            Db::getInstance()->Execute($sql_del);
        }

        $sql = 'INSERT into `'._DB_PREFIX_.'tw_customer` SET
						   user_id = '.(int)$insert_id.', twitter_id = '.(int)$twitter_id.' , id_shop = '.(int)$id_shop.'';
        Db::getInstance()->Execute($sql);
    }
}