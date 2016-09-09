<?php
/**
 * StorePrestaModules SPM LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 *
 /*
 * 
 * @author    StorePrestaModules SPM
 * @category social_networks
 * @package fbloginblock
 * @copyright Copyright StorePrestaModules SPM
 * @license   StorePrestaModules SPM
 */

include(dirname(__FILE__).'/../../config/config.inc.php');
	include(dirname(__FILE__).'/../../init.php');
	
require_once(dirname(__FILE__).'/backward_compatibility/backward.php');

    include_once dirname(__FILE__).'/lib/microsoft/http.php';
    include_once dirname(__FILE__).'/lib/microsoft/oauth_client.php';
    include_once dirname(__FILE__).'/classes/microsofthelp.class.php';
    $microsofthelp = new microsofthelp();		
   
    
    $name_module = "fbloginblock";
	$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';
	
	
	$cookie = new Cookie('ref');
	$cookie->http_referer_custom = $http_referer;
	
	
	
    $client = new oauth_client_class();
    $client->server = 'Microsoft';
    if(version_compare(_PS_VERSION_, '1.6', '>')){
		$_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__; 
	} else {
		$_http_host = _PS_BASE_URL_.__PS_BASE_URI__;
	}
    $client->redirect_uri = $_http_host.'modules/'.$name_module.'/microsoft.php';
    
    $mclientid = Configuration::get($name_module.'mclientid');
    $mclientid = trim($mclientid);
    
    $mclientsecret = Configuration::get($name_module.'mclientsecret');
    $mclientsecret = trim($mclientsecret);
    
    $client->client_id = $mclientid; 
    $application_line = __LINE__;
    $client->client_secret = $mclientsecret; 

    if(Tools::strlen($client->client_id) == 0
    || Tools::strlen($client->client_secret) == 0)
        die('Please go to Microsoft Live Connect Developer Center page '.
            'https://manage.dev.live.com/AddApplication.aspx and create a new'.
            'application, and in the line '.$application_line.
            ' set the client_id to Client ID and client_secret with Client secret. '.
            'The callback URL must be '.$client->redirect_uri.' but make sure '.
            'the domain is valid and can be resolved by a public DNS.');

    /* API permissions
     */
    $client->scope = 'wl.basic wl.emails wl.birthday';
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
                    'https://apis.live.net/v5.0/me',
                    'GET', array(), array('FailOnAccessError'=>true), $user);
            }
        }
        $success = $client->Finalize($success);
    }
    if($client->exit)
        exit;
    if($success)
    {
    	
    	$last_name = $user->last_name;
    	$first_name = $user->first_name;
    	$email_address = isset($user->emails->preferred)?$user->emails->preferred:$user->emails->account;
    	
    	$data_profile = array('first_name'=>$first_name,
    					  'last_name'=>$last_name,
    					  'email'=>$email_address
    					 );
    
    	$microsofthelp->userLog(
    					 array('data'=>$data_profile, 
    						   'http_referer_custom'=>$http_referer 
							  )
					     );
        
    }
    else
    {
      echo 'Error:'.HtmlSpecialChars($client->error); 
    }