<?php
class GithubApi {

	public function curlRequest($data)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if($data['header'] and is_array($data['header']))
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, $data['header']);
	}
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	if($data['method'] == 'POST' and !empty($data['data']))
	{
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data['data']);
	}
	else
	{
		curl_setopt($ch, CURLOPT_HTTPGET, true);
	}
	curl_setopt($ch, CURLOPT_URL, $data['url']);
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT ,3);
	curl_setopt($ch,CURLOPT_TIMEOUT, 20);

	$data = curl_exec($ch);
	return $data;
} 
	
}
