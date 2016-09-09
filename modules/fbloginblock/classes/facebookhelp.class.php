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

class facebookhelp extends Module{
	
	private $_width = 400;
	private $_height = 400;
	private $_name = 'fbloginblock';
	
	public function __construct(){
	
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
    
    
	private function _getConnectImages($data){
		
		
		$_http_host = $data['http_host'];
		$id_shop = $data['id_shop'];
		$type_large = $data['type_large']; // example 1, 2, 19
		$type_medium = $data['type_medium'];
		$type_small = $data['type_small'];
		$type_very_small = $data['type_very_small'];
		
		$prefix = $data['prefix']; // example facebook, twitter
		
		
		// Facebook connect image
			
		$sql = 'SELECT * FROM `'._DB_PREFIX_   .$this->_name.'_img`
		WHERE `type` = '.$type_large.' AND `id_shop` = '.(int)($id_shop).'';
		$data_facebook = Db::getInstance()->GetRow($sql);
		$img_blockfacebook = (isset($data_facebook['img'])?$data_facebook['img']:'');
		$img_facebook = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebook;
			
			
		$uploaded_img = 0;
		if(Tools::strlen($img_blockfacebook)>0){
			if(@filesize($img_facebook)>0){
				$uploaded_img = 1;
			}
		}
		if($uploaded_img){
			$facebook = $_http_host."upload/".$this->_name."/".$img_blockfacebook;
		} else {
			$facebook = $_http_host.'modules/'.$this->_name.'/views/img/'.$prefix.'.png';
		}
			
		// Facebook connect small image
			
		$sql = 'SELECT * FROM `'._DB_PREFIX_   .$this->_name.'_img`
		WHERE `type` = '.$type_medium.' AND `id_shop` = '.(int)($id_shop).'';
		$data_facebooksmall = Db::getInstance()->GetRow($sql);
		$img_blockfacebooksmall = (isset($data_facebooksmall['img'])?$data_facebooksmall['img']:'');
		$img_facebooksmall = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebooksmall;
			
			
		$uploaded_imgsmall = 0;
		if(Tools::strlen($img_blockfacebooksmall)>0){
			if(@filesize($img_facebooksmall)>0){
				$uploaded_imgsmall = 1;
			}
		}
		if($uploaded_imgsmall){
			$facebooksmall = $_http_host."upload/".$this->_name."/".$img_blockfacebooksmall;
		} else {
			$facebooksmall = $_http_host.'modules/'.$this->_name.'/views/img/'.$prefix.'-small.png';
		}
			
			
			
		// Facebook connect large_small image
			
		$sql = 'SELECT * FROM `'._DB_PREFIX_   .$this->_name.'_img`
		WHERE `type` = '.$type_small.' AND `id_shop` = '.(int)($id_shop).'';
		$data_facebooklarge_small = Db::getInstance()->GetRow($sql);
		$img_blockfacebooklarge_small = (isset($data_facebooklarge_small['img'])?$data_facebooklarge_small['img']:'');
		$img_facebooklarge_small = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebooklarge_small;
			
			
		$uploaded_imglarge_small = 0;
		if(Tools::strlen($img_blockfacebooklarge_small)>0){
			if(@filesize($img_facebooklarge_small)>0){
				$uploaded_imglarge_small = 1;
			}
		}
		if($uploaded_imglarge_small){
			$facebooklarge_small = $_http_host."upload/".$this->_name."/".$img_blockfacebooklarge_small;
		} else {
			$facebooklarge_small = $_http_host.'modules/'.$this->_name.'/views/img/'.$prefix.'-large-small.png';
		}
		
			
		// Facebook connect micro_small image
		
		$sql = 'SELECT * FROM `'._DB_PREFIX_   .$this->_name.'_img`
		WHERE `type` = '.$type_very_small.' AND `id_shop` = '.(int)($id_shop).'';
		$data_facebookmicro_small = Db::getInstance()->GetRow($sql);
		$img_blockfacebookmicro_small = (isset($data_facebookmicro_small['img'])?$data_facebookmicro_small['img']:'');
		$img_facebookmicro_small = dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebookmicro_small;
		
		
		$uploaded_imgmicro_small = 0;
		if(Tools::strlen($img_blockfacebookmicro_small)>0){
			if(@filesize($img_facebookmicro_small)>0){
				$uploaded_imgmicro_small = 1;
			}
		}
		if($uploaded_imgmicro_small){
			$facebookmicro_small = $_http_host."upload/".$this->_name."/".$img_blockfacebookmicro_small;
		} else {
			$facebookmicro_small = $_http_host.'modules/'.$this->_name.'/views/img/'.$prefix.'-small-micro.png';
		}
		
		
		return array(
					'large'=>$facebook,'blocklarge'=> $img_blockfacebook,
					'small'=>$facebooksmall, 'blocksmall' => $img_blockfacebooksmall,
					'large_small'=>$facebooklarge_small, 'blocklarge_small' => $img_blockfacebooklarge_small,
					'micro_small'=>$facebookmicro_small, 'blockmicro_small' => $img_blockfacebookmicro_small,
					
					);
		
	}
	
    
	public function getImages($data = null){
			$admin = isset($data['admin'])?$data['admin']:0;
			$smarty = $this->context->smarty;
			
			 if(version_compare(_PS_VERSION_, '1.5', '>')){
	        	$id_shop = Context::getContext()->shop->id;
	         } else {
	        	$id_shop = 0;
	         }
	         
	         if(!$admin){
	         
	         $_http_host = '';
	         if(defined('_MYSQL_ENGINE_')){
				$_http_host = isset($smarty->tpl_vars['base_dir_ssl']->value)?$smarty->tpl_vars['base_dir_ssl']->value:$smarty->tpl_vars['base_dir']->value;
			 } else {
			    $_http_host = isset($smarty->_tpl_vars['base_dir_ssl'])?$smarty->_tpl_vars['base_dir_ssl']:$smarty->_tpl_vars['base_dir'];
			 }
		
			if($_http_host == 'http://' || $_http_host == 'http:///'
	    	   || $_http_host == 'https://' || $_http_host == 'https:///'){
	    	   	if (Configuration::get('PS_SSL_ENABLED') == 1)
					$type_url = "https://";
				else
					$type_url = "http://";
	    	   $_http_host = $type_url.$_SERVER['HTTP_HOST']."/";
	    	   }
        
	         } else {
	         	$_http_host = "../";
	         }
	         
	         
	         $data_facebook = $this->_getConnectImages(
					         							array(
					         									'http_host'=>$_http_host,
					         									'id_shop'=>$id_shop,
					         									'type_large'=>1,
					         									'type_medium'=>4,
					         									'type_small'=>17,
					         									'type_very_small'=>18,
					         									'prefix'=>'facebook',
					         								  )
					         						  );
	         $facebook = $data_facebook['large'];
	         $img_blockfacebook = $data_facebook['blocklarge'];
	         $facebooksmall = $data_facebook['small'];
	         $img_blockfacebooksmall = $data_facebook['blocksmall'];
	         $facebooklarge_small = $data_facebook['large_small'];
	         $img_blockfacebooklarge_small = $data_facebook['blocklarge_small'];
	         $facebookmicro_small = $data_facebook['micro_small'];
	         $img_blockfacebookmicro_small = $data_facebook['blockmicro_small'];
	         
	         
	      
				
	         
	         // Twitter connect image
	         	
	         $data_twitter = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>7,
	         				'type_medium'=>8,
	         				'type_small'=>19,
	         				'type_very_small'=>20,
	         				'prefix'=>'twitter',
	         		)
	         );
	         $twitter = $data_twitter['large'];
	         $img_blocktwitter = $data_twitter['blocklarge'];
	         $twittersmall = $data_twitter['small'];
	         $img_blocktwittersmall = $data_twitter['blocksmall'];
	         $twitterlarge_small = $data_twitter['large_small'];
	         $img_blocktwitterlarge_small = $data_twitter['blocklarge_small'];
	         $twittermicro_small = $data_twitter['micro_small'];
	         $img_blocktwittermicro_small = $data_twitter['blockmicro_small'];
	         	
			
			
	         // Paypal connect image
	          
	         $data_paypal = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>3,
	         				'type_medium'=>6,
	         				'type_small'=>21,
	         				'type_very_small'=>22,
	         				'prefix'=>'paypal',
	         		)
	         );
	         $paypal = $data_paypal['large'];
	         $img_blockpaypal = $data_paypal['blocklarge'];
	         $paypalsmall = $data_paypal['small'];
	         $img_blockpaypalsmall = $data_paypal['blocksmall'];
	         $paypallarge_small = $data_paypal['large_small'];
	         $img_blockpaypallarge_small = $data_paypal['blocklarge_small'];
	         $paypalmicro_small = $data_paypal['micro_small'];
	         $img_blockpaypalmicro_small = $data_paypal['blockmicro_small'];
	          
	         
			
	         // Google connect image
	          
	         $data_google = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>2,
	         				'type_medium'=>5,
	         				'type_small'=>23,
	         				'type_very_small'=>24,
	         				'prefix'=>'google',
	         		)
	         );
	         $google = $data_google['large'];
	         $img_blockgoogle = $data_google['blocklarge'];
	         $googlesmall = $data_google['small'];
	         $img_blockgooglesmall = $data_google['blocksmall'];
	         $googlelarge_small = $data_google['large_small'];
	         $img_blockgooglelarge_small = $data_google['blocklarge_small'];
	         $googlemicro_small = $data_google['micro_small'];
	         $img_blockgooglemicro_small = $data_google['blockmicro_small'];
			
			
			
			
	         // Yahoo connect image
	          
	         $data_yahoo = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>9,
	         				'type_medium'=>10,
	         				'type_small'=>25,
	         				'type_very_small'=>26,
	         				'prefix'=>'yahoo',
	         		)
	         );
	         $yahoo = $data_yahoo['large'];
	         $img_blockyahoo = $data_yahoo['blocklarge'];
	         $yahoosmall = $data_yahoo['small'];
	         $img_blockyahoosmall = $data_yahoo['blocksmall'];
	         $yahoolarge_small = $data_yahoo['large_small'];
	         $img_blockyahoolarge_small = $data_yahoo['blocklarge_small'];
	         $yahoomicro_small = $data_yahoo['micro_small'];
	         $img_blockyahoomicro_small = $data_yahoo['blockmicro_small'];
	         	
			
			
	         // Linkedin connect image
	          
	         $data_linkedin = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>11,
	         				'type_medium'=>12,
	         				'type_small'=>27,
	         				'type_very_small'=>28,
	         				'prefix'=>'linkedin',
	         		)
	         );
	         $linkedin = $data_linkedin['large'];
	         $img_blocklinkedin = $data_linkedin['blocklarge'];
	         $linkedinsmall = $data_linkedin['small'];
	         $img_blocklinkedinsmall = $data_linkedin['blocksmall'];
	         $linkedinlarge_small = $data_linkedin['large_small'];
	         $img_blocklinkedinlarge_small = $data_linkedin['blocklarge_small'];
	         $linkedinmicro_small = $data_linkedin['micro_small'];
	         $img_blocklinkedinmicro_small = $data_linkedin['blockmicro_small'];
	          
			
			
	         // Microsoft connect image
	          
	         $data_microsoft = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>13,
	         				'type_medium'=>14,
	         				'type_small'=>29,
	         				'type_very_small'=>30,
	         				'prefix'=>'microsoft',
	         		)
	         );
	         $microsoft = $data_microsoft['large'];
	         $img_blockmicrosoft = $data_microsoft['blocklarge'];
	         $microsoftsmall = $data_microsoft['small'];
	         $img_blockmicrosoftsmall = $data_microsoft['blocksmall'];
	         $microsoftlarge_small = $data_microsoft['large_small'];
	         $img_blockmicrosoftlarge_small = $data_microsoft['blocklarge_small'];
	         $microsoftmicro_small = $data_microsoft['micro_small'];
	         $img_blockmicrosoftmicro_small = $data_microsoft['blockmicro_small'];
	          
			
			
	         // Instagram connect image
	          
	         $data_instagram = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>15,
	         				'type_medium'=>16,
	         				'type_small'=>31,
	         				'type_very_small'=>32,
	         				'prefix'=>'instagram',
	         		)
	         );
	         $instagram = $data_instagram['large'];
	         $img_blockinstagram = $data_instagram['blocklarge'];
	         $instagramsmall = $data_instagram['small'];
	         $img_blockinstagramsmall = $data_instagram['blocksmall'];
	         $instagramlarge_small = $data_instagram['large_small'];
	         $img_blockinstagramlarge_small = $data_instagram['blocklarge_small'];
	         $instagrammicro_small = $data_instagram['micro_small'];
	         $img_blockinstagrammicro_small = $data_instagram['blockmicro_small'];
	         
	         // Foursquare connect image
	          
	         $data_foursquare = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>33,
	         				'type_medium'=>34,
	         				'type_small'=>35,
	         				'type_very_small'=>36,
	         				'prefix'=>'foursquare',
	         		)
	         );
	         $foursquare = $data_foursquare['large'];
	         $img_blockfoursquare = $data_foursquare['blocklarge'];
	         $foursquaresmall = $data_foursquare['small'];
	         $img_blockfoursquaresmall = $data_foursquare['blocksmall'];
	         $foursquarelarge_small = $data_foursquare['large_small'];
	         $img_blockfoursquarelarge_small = $data_foursquare['blocklarge_small'];
	         $foursquaremicro_small = $data_foursquare['micro_small'];
	         $img_blockfoursquaremicro_small = $data_foursquare['blockmicro_small'];
	         
	         
	         // Github connect image
	          
	         $data_github = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>37,
	         				'type_medium'=>38,
	         				'type_small'=>39,
	         				'type_very_small'=>40,
	         				'prefix'=>'github',
	         		)
	         );
	         $github = $data_github['large'];
	         $img_blockgithub = $data_github['blocklarge'];
	         $githubsmall = $data_github['small'];
	         $img_blockgithubsmall = $data_github['blocksmall'];
	         $githublarge_small = $data_github['large_small'];
	         $img_blockgithublarge_small = $data_github['blocklarge_small'];
	         $githubmicro_small = $data_github['micro_small'];
	         $img_blockgithubmicro_small = $data_github['blockmicro_small'];
	         
	         
	         // disqus connect image
	          
	         $data_disqus = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>41,
	         				'type_medium'=>42,
	         				'type_small'=>43,
	         				'type_very_small'=>44,
	         				'prefix'=>'disqus',
	         		)
	         );
	         $disqus = $data_disqus['large'];
	         $img_blockdisqus = $data_disqus['blocklarge'];
	         $disqussmall = $data_disqus['small'];
	         $img_blockdisqussmall = $data_disqus['blocksmall'];
	         $disquslarge_small = $data_disqus['large_small'];
	         $img_blockdisquslarge_small = $data_disqus['blocklarge_small'];
	         $disqusmicro_small = $data_disqus['micro_small'];
	         $img_blockdisqusmicro_small = $data_disqus['blockmicro_small'];
			
	         
	         // amazon connect image
	         	
	         $data_amazon = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>49,
	         				'type_medium'=>50,
	         				'type_small'=>51,
	         				'type_very_small'=>52,
	         				'prefix'=>'amazon',
	         		)
	         );
	         $amazon = $data_amazon['large'];
	         $img_blockamazon = $data_amazon['blocklarge'];
	         $amazonsmall = $data_amazon['small'];
	         $img_blockamazonsmall = $data_amazon['blocksmall'];
	         $amazonlarge_small = $data_amazon['large_small'];
	         $img_blockamazonlarge_small = $data_amazon['blocklarge_small'];
	         $amazonmicro_small = $data_amazon['micro_small'];
	         $img_blockamazonmicro_small = $data_amazon['blockmicro_small'];
	         	
			
				
			return array('facebook'=>$facebook,'facebook_block'=> $img_blockfacebook, 
						 'facebooksmall'=>$facebooksmall, 'facebook_blocksmall' => $img_blockfacebooksmall,
						 'facebooklarge_small'=>$facebooklarge_small, 'facebook_blocklarge_small' => $img_blockfacebooklarge_small,
						 'facebookmicro_small'=>$facebookmicro_small, 'facebook_blockmicro_small' => $img_blockfacebookmicro_small,
					
						 'twitter' => $twitter, 'twitter_block' => $img_blocktwitter,
						 'twittersmall' => $twittersmall, 'twitter_blocksmall' => $img_blocktwittersmall,
						 'twitterlarge_small'=>$twitterlarge_small, 'twitter_blocklarge_small' => $img_blocktwitterlarge_small,
						 'twittermicro_small'=>$twittermicro_small, 'twitter_blockmicro_small' => $img_blocktwittermicro_small,
						
						 'paypal'=>$paypal, 'paypal_block' => $img_blockpaypal,
						 'paypalsmall'=>$paypalsmall, 'paypal_blocksmall' => $img_blockpaypalsmall,
						 'paypallarge_small'=>$paypallarge_small, 'paypal_blocklarge_small' => $img_blockpaypallarge_small,
						 'paypalmicro_small'=>$paypalmicro_small, 'paypal_blockmicro_small' => $img_blockpaypalmicro_small,
						
					
						 'google' => $google, 'google_block' => $img_blockgoogle,
						 'googlesmall' => $googlesmall, 'google_blocksmall' => $img_blockgooglesmall ,
						 'googlelarge_small'=>$googlelarge_small, 'google_blocklarge_small' => $img_blockgooglelarge_small,
						 'googlemicro_small'=>$googlemicro_small, 'google_blockmicro_small' => $img_blockgooglemicro_small,
					
						
						 'yahoo' => $yahoo, 'yahoo_block' => $img_blockyahoo,
						 'yahoosmall' => $yahoosmall, 'yahoo_blocksmall' => $img_blockyahoosmall,
						 'yahoolarge_small'=>$yahoolarge_small, 'yahoo_blocklarge_small' => $img_blockyahoolarge_small,
						 'yahoomicro_small'=>$yahoomicro_small, 'yahoo_blockmicro_small' => $img_blockyahoomicro_small,
						
					
						 'linkedin' => $linkedin, 'linkedin_block' => $img_blocklinkedin,
						 'linkedinsmall' => $linkedinsmall, 'linkedin_blocksmall' => $img_blocklinkedinsmall,
						 'linkedinlarge_small'=>$linkedinlarge_small, 'linkedin_blocklarge_small' => $img_blocklinkedinlarge_small,
						 'linkedinmicro_small'=>$linkedinmicro_small, 'linkedin_blockmicro_small' => $img_blocklinkedinmicro_small,
					
					
						 'microsoft' => $microsoft, 'microsoft_block' => $img_blockmicrosoft,
						 'microsoftsmall' => $microsoftsmall, 'microsoft_blocksmall' => $img_blockmicrosoftsmall,
						 'microsoftlarge_small'=>$microsoftlarge_small, 'microsoft_blocklarge_small' => $img_blockmicrosoftlarge_small,
						 'microsoftmicro_small'=>$microsoftmicro_small, 'microsoft_blockmicro_small' => $img_blockmicrosoftmicro_small,
						
						 'instagram' => $instagram, 'instagram_block' => $img_blockinstagram,
						 'instagramsmall' => $instagramsmall, 'instagram_blocksmall' => $img_blockinstagramsmall,
						 'instagramlarge_small'=>$instagramlarge_small, 'instagram_blocklarge_small' => $img_blockinstagramlarge_small,
						 'instagrammicro_small'=>$instagrammicro_small, 'instagram_blockmicro_small' => $img_blockinstagrammicro_small,
						    
						 'foursquare' => $foursquare, 'foursquare_block' => $img_blockfoursquare,
						 'foursquaresmall' => $foursquaresmall, 'foursquare_blocksmall' => $img_blockfoursquaresmall,
						 'foursquarelarge_small'=>$foursquarelarge_small, 'foursquare_blocklarge_small' => $img_blockfoursquarelarge_small,
						 'foursquaremicro_small'=>$foursquaremicro_small, 'foursquare_blockmicro_small' => $img_blockfoursquaremicro_small,
					
						 'github' => $github, 'github_block' => $img_blockgithub,
						 'githubsmall' => $githubsmall, 'github_blocksmall' => $img_blockgithubsmall,
						 'githublarge_small'=>$githublarge_small, 'github_blocklarge_small' => $img_blockgithublarge_small,
						 'githubmicro_small'=>$githubmicro_small, 'github_blockmicro_small' => $img_blockgithubmicro_small,
					
						 'disqus' => $disqus, 'disqus_block' => $img_blockdisqus,
						 'disqussmall' => $disqussmall, 'disqus_blocksmall' => $img_blockdisqussmall,
						 'disquslarge_small'=>$disquslarge_small, 'disqus_blocklarge_small' => $img_blockdisquslarge_small,
						 'disqusmicro_small'=>$disqusmicro_small, 'disqus_blockmicro_small' => $img_blockdisqusmicro_small,
					
					'amazon' => $amazon, 'amazon_block' => $img_blockamazon,
					'amazonsmall' => $amazonsmall, 'amazon_blocksmall' => $img_blockamazonsmall,
					'amazonlarge_small'=>$amazonlarge_small, 'amazon_blocklarge_small' => $img_blockamazonlarge_small,
					'amazonmicro_small'=>$amazonmicro_small, 'amazon_blockmicro_small' => $img_blockamazonmicro_small,
					
						 );
			}
	
	
	public function saveImage($data = null){
		
		$error = 0;
		$error_text = '';
		$custom_type_img = $data['type'];
		
		$files = $_FILES['post_image_'.$custom_type_img];
		
		############### files ###############################
		if(!empty($files['name']))
			{
		      if(!$files['error'])
		      {
				  $type_one = $files['type'];
				  $ext = explode("/",$type_one);
				  
				  if(strpos('_'.$type_one,'image')<1)
				  {
				  	$error_text = $this->l('Invalid file type, please try again!');
				  	$error = 1;

				  }elseif(!in_array($ext[1],array('png','x-png','gif','jpg','jpeg','pjpeg'))){
				  	$error_text = $this->l('Wrong file format, please try again!');
				  	$error = 1;
				  	
				  } else {
				  	
				  		
				  		
				  		$data_img = $this->getImages(array('admin'=>1));
				  		if($custom_type_img == "facebook"){
				  			$type_page = 1;
				  			$img_old_del = $data_img['facebook_block']; 
				  		} elseif($custom_type_img == "facebooksmall"){
				  			$type_page = 4;
				  			$img_old_del = $data_img['facebook_blocksmall']; 		
				  		} elseif($custom_type_img == "facebooklarge_small"){
				  			$type_page = 17;
				  			$img_old_del = $data_img['facebook_blocklarge_small']; 
				  		} elseif($custom_type_img == "facebookmicro_small"){
				  			$type_page = 18;
				  			$img_old_del = $data_img['facebook_blockmicro_small']; 
				  					
				  		} elseif($custom_type_img == "twitter"){
				  			$type_page = 7;
				  			$img_old_del = $data_img['twitter_block']; 		
				  		} elseif($custom_type_img == "twittersmall"){
				  			$type_page = 8;
				  			$img_old_del = $data_img['twitter_blocksmall']; 		
				  		} elseif($custom_type_img == "twitterlarge_small"){
				  			$type_page = 19;
				  			$img_old_del = $data_img['twitter_blocklarge_small']; 
				  		} elseif($custom_type_img == "twittermicro_small"){
				  			$type_page = 20;
				  			$img_old_del = $data_img['twitter_blockmicro_small']; 
				  					
				  		} elseif($custom_type_img == "paypal"){
				  			$type_page = 3;
				  			$img_old_del = $data_img['paypal_block']; 		
				  		} elseif($custom_type_img == "paypalsmall"){
				  			$type_page = 6;
				  			$img_old_del = $data_img['paypal_blocksmall']; 		
				  		} elseif($custom_type_img == "paypallarge_small"){
				  			$type_page = 21;
				  			$img_old_del = $data_img['paypal_blocklarge_small']; 
				  		} elseif($custom_type_img == "paypalmicro_small"){
				  			$type_page = 22;
				  			$img_old_del = $data_img['paypal_blockmicro_small']; 
				  					
				  		}elseif($custom_type_img == "google"){
				  			$type_page = 2;
				  			$img_old_del = $data_img['google_block']; 		
				  		} elseif($custom_type_img == "googlesmall"){
				  			$type_page = 5;
				  			$img_old_del = $data_img['google_blocksmall']; 		
				  		} elseif($custom_type_img == "googlelarge_small"){
				  			$type_page = 23;
				  			$img_old_del = $data_img['google_blocklarge_small']; 
				  		} elseif($custom_type_img == "googlemicro_small"){
				  			$type_page = 24;
				  			$img_old_del = $data_img['google_blockmicro_small']; 
				  					
				  		} elseif($custom_type_img == "yahoo"){
				  			$type_page = 9;
				  			$img_old_del = $data_img['yahoo_block']; 		
				  		} elseif($custom_type_img == "yahoosmall"){
				  			$type_page = 10;
				  			$img_old_del = $data_img['yahoo_blocksmall']; 		
				  		} elseif($custom_type_img == "yahoolarge_small"){
				  			$type_page = 25;
				  			$img_old_del = $data_img['yahoo_blocklarge_small']; 
				  		} elseif($custom_type_img == "yahoomicro_small"){
				  			$type_page = 26;
				  			$img_old_del = $data_img['yahoo_blockmicro_small']; 
				  					
				  		}  elseif($custom_type_img == "linkedin"){
				  			$type_page = 11;
				  			$img_old_del = $data_img['linkedin_block']; 		
				  		}  elseif($custom_type_img == "linkedinsmall"){
				  			$type_page = 12;
				  			$img_old_del = $data_img['linkedin_blocksmall']; 		
				  		} elseif($custom_type_img == "linkedinlarge_small"){
				  			$type_page = 27;
				  			$img_old_del = $data_img['linkedin_blocklarge_small']; 
				  		} elseif($custom_type_img == "linkedinmicro_small"){
				  			$type_page = 28;
				  			$img_old_del = $data_img['linkedin_blockmicro_small']; 
				  					
				  		} elseif($custom_type_img == "microsoft"){
				  			$type_page = 13;
				  			$img_old_del = $data_img['microsoft_block']; 		
				  		} elseif($custom_type_img == "microsoftsmall"){
				  			$type_page = 14;
				  			$img_old_del = $data_img['microsoft_blocksmall']; 		
				  		} elseif($custom_type_img == "microsoftlarge_small"){
				  			$type_page = 29;
				  			$img_old_del = $data_img['microsoft_blocklarge_small']; 
				  		} elseif($custom_type_img == "microsoftmicro_small"){
				  			$type_page = 30;
				  			$img_old_del = $data_img['microsoft_blockmicro_small']; 
				  					
				  		}elseif($custom_type_img == "instagram"){
				  			$type_page = 15;
				  			$img_old_del = $data_img['instagram_block']; 		
				  		} elseif($custom_type_img == "instagramsmall"){
				  			$type_page = 16;
				  			$img_old_del = $data_img['instagram_blocksmall']; 		
				  		} elseif($custom_type_img == "instagramlarge_small"){
				  			$type_page = 31;
				  			$img_old_del = $data_img['instagram_blocklarge_small']; 
				  		} elseif($custom_type_img == "instagrammicro_small"){
				  			$type_page = 32;
				  			$img_old_del = $data_img['instagram_blockmicro_small']; 
				  					
				  		}elseif($custom_type_img == "foursquare"){
				  			$type_page = 33;
				  			$img_old_del = $data_img['foursquare_block']; 		
				  		} elseif($custom_type_img == "foursquaresmall"){
				  			$type_page = 34;
				  			$img_old_del = $data_img['foursquare_blocksmall']; 		
				  		} elseif($custom_type_img == "foursquarelarge_small"){
				  			$type_page = 35;
				  			$img_old_del = $data_img['foursquare_blocklarge_small']; 
				  		} elseif($custom_type_img == "foursquaremicro_small"){
				  			$type_page = 36;
				  			$img_old_del = $data_img['foursquare_blockmicro_small']; 
				  					
				  		}elseif($custom_type_img == "github"){
				  			$type_page = 37;
				  			$img_old_del = $data_img['github_block']; 		
				  		} elseif($custom_type_img == "githubsmall"){
				  			$type_page = 38;
				  			$img_old_del = $data_img['github_blocksmall']; 		
				  		} elseif($custom_type_img == "githublarge_small"){
				  			$type_page = 39;
				  			$img_old_del = $data_img['github_blocklarge_small']; 
				  		} elseif($custom_type_img == "githubmicro_small"){
				  			$type_page = 40;
				  			$img_old_del = $data_img['github_blockmicro_small']; 
				  					
				  		}elseif($custom_type_img == "disqus"){
				  			$type_page = 41;
				  			$img_old_del = $data_img['disqus_block']; 		
				  		} elseif($custom_type_img == "disqussmall"){
				  			$type_page = 42;
				  			$img_old_del = $data_img['disqus_blocksmall']; 		
				  		} elseif($custom_type_img == "disquslarge_small"){
				  			$type_page = 43;
				  			$img_old_del = $data_img['disqus_blocklarge_small']; 
				  		} elseif($custom_type_img == "disqusmicro_small"){
				  			$type_page = 44;
				  			$img_old_del = $data_img['disqus_blockmicro_small']; 
				  					
				  		} elseif ($custom_type_img == "amazon") {
							$type_page = 49;
							$img_old_del = $data_img ['amazon_block'];
						} elseif ($custom_type_img == "amazonsmall") {
							$type_page = 50;
							$img_old_del = $data_img ['amazon_blocksmall'];
						} elseif ($custom_type_img == "amazonlarge_small") {
							$type_page = 51;
							$img_old_del = $data_img ['amazon_blocklarge_small'];
						} elseif ($custom_type_img == "amazonmicro_small") {
							$type_page = 52;
							$img_old_del = $data_img ['amazon_blockmicro_small'];
						
						}
    					
    					
				  		if(Tools::strlen($img_old_del)>0){
				  			// delete old img
				  			unlink(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_old_del);
				  		} 
					
				  			
					  	srand((double)microtime()*1000000);
					 	$uniq_name_image = uniqid(rand());
					 	$type_one = Tools::substr($type_one,6,Tools::strlen($type_one)-6);
					 	$filename = $uniq_name_image.'.'.$type_one; 
					 	
						move_uploaded_file($files['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$filename);
						
						
						$img_return = $uniq_name_image.'.jpg';
						$img_return = $filename;
			  		
				  		$this->_updateImgDB(array('type_page' => $type_page,
				  								  'img' => $img_return
				  							     )
				  							);

				  }
				}
				
			}  
			
		return array('error' => $error,
					 'error_text' => $error_text);
	
	
	}
	
	private function _updateImgDB($data = null){
		
		$type_page = $data['type_page'];
		$img = $data['img'];
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
	       	$id_shop = Context::getContext()->shop->id;
	    } else {
	      	$id_shop = 0;
	    }
	         
		
		$sql = 'SELECT count(*) as count FROM `'._DB_PREFIX_   .$this->_name.'_img` 
		        	WHERE `type` = '.(int)($type_page).' AND `id_shop` = '.(int)($id_shop).'';
		$data_exists = Db::getInstance()->GetRow($sql);
		
		if($data_exists['count']){
			// delete and insert
			$sql = 'DELETE FROM `'._DB_PREFIX_.$this->_name.'_img` 
						   WHERE `type` = '.(int)$type_page.' 
						   AND `id_shop` = '.(int)$id_shop.'';
			Db::getInstance()->Execute($sql);
			
		} else {
			// only insert new
		}
		// insert
		$sql = 'INSERT INTO `'._DB_PREFIX_.$this->_name.'_img` 
						   SET `type` = '.(int)$type_page.', 
						       `id_shop` = '.(int)$id_shop.',
						       `img` = \''.pSQL($img).'\'
						       ';
		
			Db::getInstance()->Execute($sql);
	}
	
	public function deleteImage($data){
		$type = $data['type'];
		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
		       	$id_shop = Context::getContext()->shop->id;
		    } else {
		      	$id_shop = 0;
		    }
	    
		$sql = 'SELECT * FROM `'._DB_PREFIX_   .$this->_name.'_img` 
		        	WHERE `type` = '.(int)($type).' AND `id_shop` = '.(int)($id_shop).'';
		$data = Db::getInstance()->GetRow($sql);
		$img_delete = (isset($data['img'])?$data['img']:'');
			
		    
		   $sql = 'DELETE FROM `'._DB_PREFIX_.$this->_name.'_img` 
						   WHERE `type` = '.(int)$type.' 
						   AND `id_shop` = '.(int)$id_shop.'';
				Db::getInstance()->Execute($sql);
			
		
		@unlink(dirname(__FILE__).DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_delete);
				  		
	}
	
	
	
	
}