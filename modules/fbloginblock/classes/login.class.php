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

class login {
	
	private $_action;
	private $_http_host;
	private $_http_referer;
	private $_name;
    private $_social_type = 6; // this is yahoo connect
    private $_social_type_google = 3;
	
	public function __construct($data){
		
		$this->_name =  'fbloginblock'; 
		
		$this->_action = $data['p'];
		$this->_http_referer = isset($data['http_referer'])?$data['http_referer']:'';
		
		
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
	
 	public function mainAction(){
 		
 		$this->loginYahoo();
 		
 		
  	}
  	

	public function loginYahoo(){		
		try {
			
		$openid = new LightOpenID($this->_http_host);
			
            if(!$openid->mode) {
             		$_SESSION['openid_identifier'] = 'me.yahoo.com/';
					
                    $openid->required = array('contact/email');
           			$openid->optional = array('namePerson', 'namePerson/friendly');
                                        
                      
                    $openid->identity = 'me.yahoo.com/';
                    
                    redirect_custom_fbloginblock($openid->authUrl());
                    
                   
            } elseif($openid->mode == 'cancel') {
                echo 'User has canceled authentication!';
            } else {


                $_data = $openid->getAttributes();
                $_email = ($_data['contact/email']) ? $_data['contact/email'] : '';

                $openid_identifier = isset($_SESSION['openid_identifier'])?$_SESSION['openid_identifier']:null;

                if ($openid_identifier && $_email == '')
                    $_email = $openid_identifier;

                $gender = ((isset($_data['person/gender']) && $_data['person/gender'] == 'M') || !isset($_data['person/gender']))?1:2;




                if(isset($_data['namePerson/first']) && isset($_data['namePerson/last'])){
                    $firstname = pSQL($_data['namePerson/first']);
                    $lastname = pSQL($_data['namePerson/last']);
                }elseif(isset($_data['namePerson'])){
                    $pattern = ("|^(.+?) (.*)$|su");
                    preg_match($pattern, $_data['namePerson'], $arg);

                    if(sizeof($arg)>0){
                        $firstname = pSQL($arg[1]);
                        $lastname = pSQL($arg[2]);
                    } else {
                        $firstname = pSQL($_data['namePerson']);
                        $lastname = pSQL($_data['namePerson']);
                    }
                }else{
                    $firstname = pSQL($_data['namePerson/friendly']);
                    $lastname = pSQL($_data['namePerson/friendly']);
                }




                ## add new functional for auth and create user ##
                $data_profile = array(
                    'email'=>$_email,
                    'first_name'=>$firstname,
                    'last_name'=>$lastname,
                    'gender'=>$gender,

                );

                include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
                $userhelp = new userhelp();
                $userhelp->userLog(
                    array(
                        'data_profile'=>$data_profile,
                        'http_referer_custom'=>$this->_http_referer,
                        'type'=>$this->_social_type,
                    )
                );
                ## add new functional for auth and create user ##


                //$this->userLog($openid->getAttributes());
            }
        } catch(ErrorException $e) {
            echo $e->getMessage();
        }
  }
  
  
  public function GoogleAndYahooLoginLogin(){
  	 
  	$action = $this->_action;
  	$http_referer = $this->_http_referer;

      if (version_compare(_PS_VERSION_, '1.5', '>')){
          $cookie = new Cookie('ref');
          $cookie->http_referer_custom = $http_referer;
      }
  	
  	$name_module = $this->_name;
  	
  	switch($action){
  		case 'yahoo':
  		case 'login':

            include_once(_PS_MODULE_DIR_.$this->_name.'/lib/openId/openid.php');
            include_once(_PS_MODULE_DIR_.$this->_name.'/lib/openId/provider/provider.php');
  			$this->loginYahoo();
  		break;
  		default:
  			$oci = Configuration::get($name_module.'oci');
  			$oci = trim($oci);
  			$ocs = Configuration::get($name_module.'ocs');
  			$ocs = trim($ocs);
  			$oru = Configuration::get($name_module.'oru');
  			$oru = trim($oru);



  	
  			if(Tools::strlen($oci)==0 || Tools::strlen($ocs)==0
                || Tools::strlen($oru)==0
            ){
  				echo "Error: Please fill Google Client Id, Google Client Secret, Google Callback URL in the module settings!";
  				exit;
  			}
  	
  			require(_PS_MODULE_DIR_.$this->_name.'/lib/google/Google_Client.php');
  			require(_PS_MODULE_DIR_.$this->_name.'/lib/google/contrib/Google_Oauth2Service.php');
  	
  	
  			$client = new Google_Client();



  			$client->setClientId($oci);
  			$client->setClientSecret($ocs);
  			$client->setRedirectUri($oru);
            $client->setApprovalPrompt ("auto");
  	
  			$client->setApplicationName(
  					array(
  							'application_name'=>Configuration::get('PS_SHOP_NAME'),
  					)
  	
  			);
  	
  			$oauth2 = new Google_Oauth2Service($client);
  	
  			
  	
  			if(!Tools::getValue('code')) {
  					
  				$url = $client->createAuthUrl();
  				redirect_custom_fbloginblock($url);
  	
  				exit();
  	
  			} else {
  				$client->authenticate(Tools::getValue('code'));
  				$_SESSION['token'] = $client->getAccessToken();
  			}
  	
  			if (isset($_SESSION['token'])) {
  			 $client->setAccessToken($_SESSION['token']);
  			}
  	
  			if (isset($_REQUEST['logout'])) {
  				unset($_SESSION['token']);
  				$client->revokeToken();
  			}
  	
  			if ($client->getAccessToken()) {
  				$user = $oauth2->userinfo->get();
  	
  	
  				$first_name= $user['given_name'];
  				$last_name = $user['family_name'];
  				$email = $user['email'];
  				$gender = isset($user['gender'])?$user['gender']:''; //female or mail


                ## add new functional for auth and create user ##
                $data_profile = array(
                    'email'=>$email,
                    'first_name'=>$first_name,
                    'last_name'=>$last_name,
                    'gender'=>$gender,


                );

                include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
                $userhelp = new userhelp();
                $userhelp->userLog(
                    array(
                        'data_profile'=>$data_profile,
                        'http_referer_custom'=>$http_referer,
                        'type'=>$this->_social_type_google,
                    )
                );
                ## add new functional for auth and create user ##


  	
  				$_SESSION['token'] = $client->getAccessToken();
  			} else {
  				//$authUrl = $client->createAuthUrl();
  			}
  	
  	
  			break;
  	}
  	
  	
  }
    
    
	
    
}

?>