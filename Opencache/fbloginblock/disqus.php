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




$http_referer = isset($_REQUEST['http_referer'])?$_REQUEST['http_referer']:'';



$name_module = 'fbloginblock';

$cookie = new Cookie('ref');
$cookie->http_referer_custom = $http_referer;


include(dirname(__FILE__).'/lib/disqus/DisqusAPI.class.php');
include(dirname(__FILE__).'/classes/disqushelp.class.php');



$PUBLIC_KEY = Configuration::get($name_module.'dci');
$PUBLIC_KEY = trim($PUBLIC_KEY);
$SECRET_KEY = Configuration::get($name_module.'dcs');
$SECRET_KEY = trim($SECRET_KEY);
$redirect = Configuration::get($name_module.'dru');
$redirect = trim($redirect);

if(Tools::strlen($PUBLIC_KEY)==0 || Tools::strlen($SECRET_KEY)==0 || Tools::strlen($redirect)==0){
	echo "Error: Please fill Disqus Client Id, Disqus Client Secret, Disqus Callback URL in the module settings!";
	exit;
}


//This is a all-in-one example of API authentication and making API calls using OAuth
//More information on using OAuth with Disqus can be found here: http://disqus.com/api/docs/auth/


$endpoint = 'https://disqus.com/api/oauth/2.0/authorize?';
$client_id = $PUBLIC_KEY;
$scope = 'read,write,email';
$response_type = 'code';


// Get the code to request access

$CODE = Tools::getValue('code');

if($CODE){

	// Build the URL and request the authentication token
	extract($_POST);
	
	$authorize = "authorization_code";
	
	$url = 'https://disqus.com/api/oauth/2.0/access_token/?';
	$fields = array(
	  'grant_type'=>urlencode($authorize),
	  'client_id'=>urlencode($PUBLIC_KEY),
	  'client_secret'=>urlencode($SECRET_KEY),
	  'redirect_uri'=>urlencode($redirect),
	  'code'=>urlencode($CODE)
	);
	
	$fields_string = '';
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	rtrim($fields_string, "&");
	
	//open connection
	$ch = curl_init();
	
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	
	//execute post
	$data = curl_exec($ch);
	
	//close connection
	curl_close($ch);
	
	//turn the string into a object
	$auth_results = Tools::jsonDecode($data);


	$access_token = $auth_results->access_token;

     //Setting the correct endpoint
    $cases_endpoint = 'https://disqus.com/api/3.0/users/details.json?';

    //Calling the function to getData
    
    $disqusapi = new DisqusAPI();
    
    $user_details = $disqusapi->getData($cases_endpoint, $SECRET_KEY, $access_token);
   /*  echo "<p><h3>Getting user details:</h3>";
    echo "<pre>"; var_dump($user_details);
    echo "</p>"; */
    
    $email_address = $user_details->response->email;
    $first_name = $user_details->response->username;
    $last_name = $user_details->response->username;
    
    
    $data_profile = array(
    		'first_name'=>$first_name,
    		'last_name'=>$last_name,
    		'email'=>$email_address,
    
    );
    
   // var_dump($data_profile);exit;
    
    
    
    $disqushelp = new disqushelp();
    $disqushelp->userLog(
    		array(
    				'data'=>$data_profile,
    				'http_referer_custom'=>$http_referer
    		)
    );
    
    
    } else {
    	$auth_url = $endpoint.'&client_id='.$client_id.'&scope='.$scope.'&response_type='.$response_type.'&redirect_uri='.$redirect;
    	Tools::redirect($auth_url);
    	exit;
    }

    
    
   
    
?>




