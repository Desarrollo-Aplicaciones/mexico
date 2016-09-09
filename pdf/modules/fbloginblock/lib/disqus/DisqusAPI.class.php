<?php
class DisqusApi {

	public function getData($url, $SECRET_KEY, $access_token){
    
    	//Setting OAuth parameters
    	$oauth_params = (object) array(
    			'access_token' => $access_token,
    			'api_secret' => $SECRET_KEY
    	);
    
    	$param_string = '';
    
    
    	//Build the endpiont from the fields selected and put add it to the string.
    	 
    	//foreach($params as $key=>$value) { $param_string .= $key.'='.$value.'&'; }
    	foreach($oauth_params as $key=>$value) {
    		$param_string .= $key.'='.$value.'&';
    	}
    	$param_string = rtrim($param_string, "&");
    
    	// setup curl to make a call to the endpoint
    	$url .= $param_string;
    
    	//echo $url;
    	$session = curl_init($url);
    
    	// indicates that we want the response back rather than just returning a "TRUE" string
    	curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($session,CURLOPT_FOLLOWLOCATION,true);
    
    	// execute GET and get the session backs
    	$results = curl_exec($session);
    	// close connection
    	curl_close($session);
    	// show the response in the browser
    	return  Tools::jsonDecode($results);
    }
	
}
