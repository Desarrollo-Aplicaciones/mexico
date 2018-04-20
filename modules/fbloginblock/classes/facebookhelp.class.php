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

class facebookhelp extends Module{
	
	private $_width = 400;
	private $_height = 400;
	private $_name = 'fbloginblock';
	private $_http_host;
    private $_social_type = 1;
	
	public function __construct(){
	
		$name = "fbloginblock";
	
		if(version_compare(_PS_VERSION_, '1.6', '>')){
			$this->_http_host = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__;
		} else {
			$this->_http_host = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
		}
		

        if (version_compare(_PS_VERSION_, '1.7', '<')){
            require_once(_PS_MODULE_DIR_.$name.'/backward_compatibility/backward.php');
        }
	
		$this->initContext();
	}
	
	private function initContext()
	{
		$this->context = Context::getContext();
	}
    
    
	private function _getConnectImages($data){
		return false;
		
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
		$img_facebook = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebook;
			
			
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
		$img_facebooksmall = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebooksmall;
			
			
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
		$img_facebooklarge_small = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebooklarge_small;
			
			
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
		$img_facebookmicro_small = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_blockfacebookmicro_small;
		
		
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
	         	
	         
	         
	         // dropbox connect image
	          
	         $data_dropbox = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>53,
	         				'type_medium'=>54,
	         				'type_small'=>55,
	         				'type_very_small'=>56,
	         				'prefix'=>'dropbox',
	         		)
	         );
	         $dropbox = $data_dropbox['large'];
	         $img_blockdropbox = $data_dropbox['blocklarge'];
	         $dropboxsmall = $data_dropbox['small'];
	         $img_blockdropboxsmall = $data_dropbox['blocksmall'];
	         $dropboxlarge_small = $data_dropbox['large_small'];
	         $img_blockdropboxlarge_small = $data_dropbox['blocklarge_small'];
	         $dropboxmicro_small = $data_dropbox['micro_small'];
	         $img_blockdropboxmicro_small = $data_dropbox['blockmicro_small'];
			
	         
	         // scoop connect image
	          
	         $data_scoop = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>57,
	         				'type_medium'=>58,
	         				'type_small'=>59,
	         				'type_very_small'=>60,
	         				'prefix'=>'scoop',
	         		)
	         );
	         $scoop = $data_scoop['large'];
	         $img_blockscoop = $data_scoop['blocklarge'];
	         $scoopsmall = $data_scoop['small'];
	         $img_blockscoopsmall = $data_scoop['blocksmall'];
	         $scooplarge_small = $data_scoop['large_small'];
	         $img_blockscooplarge_small = $data_scoop['blocklarge_small'];
	         $scoopmicro_small = $data_scoop['micro_small'];
	         $img_blockscoopmicro_small = $data_scoop['blockmicro_small'];
	         
	         
	         // wordpress connect image
	          
	         $data_wordpress = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>61,
	         				'type_medium'=>62,
	         				'type_small'=>63,
	         				'type_very_small'=>64,
	         				'prefix'=>'wordpress',
	         		)
	         );
	         $wordpress = $data_wordpress['large'];
	         $img_blockwordpress = $data_wordpress['blocklarge'];
	         $wordpresssmall = $data_wordpress['small'];
	         $img_blockwordpresssmall = $data_wordpress['blocksmall'];
	         $wordpresslarge_small = $data_wordpress['large_small'];
	         $img_blockwordpresslarge_small = $data_wordpress['blocklarge_small'];
	         $wordpressmicro_small = $data_wordpress['micro_small'];
	         $img_blockwordpressmicro_small = $data_wordpress['blockmicro_small'];
	         
	         // tumblr connect image
	          
	         $data_tumblr = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>65,
	         				'type_medium'=>66,
	         				'type_small'=>67,
	         				'type_very_small'=>68,
	         				'prefix'=>'tumblr',
	         		)
	         );
	         $tumblr = $data_tumblr['large'];
	         $img_blocktumblr = $data_tumblr['blocklarge'];
	         $tumblrsmall = $data_tumblr['small'];
	         $img_blocktumblrsmall = $data_tumblr['blocksmall'];
	         $tumblrlarge_small = $data_tumblr['large_small'];
	         $img_blocktumblrlarge_small = $data_tumblr['blocklarge_small'];
	         $tumblrmicro_small = $data_tumblr['micro_small'];
	         $img_blocktumblrmicro_small = $data_tumblr['blockmicro_small'];
	         
	         
	         // pinterest connect image
	          
	         $data_pinterest = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>69,
	         				'type_medium'=>70,
	         				'type_small'=>71,
	         				'type_very_small'=>72,
	         				'prefix'=>'pinterest',
	         		)
	         );
	         $pinterest = $data_pinterest['large'];
	         $img_blockpinterest = $data_pinterest['blocklarge'];
	         $pinterestsmall = $data_pinterest['small'];
	         $img_blockpinterestsmall = $data_pinterest['blocksmall'];
	         $pinterestlarge_small = $data_pinterest['large_small'];
	         $img_blockpinterestlarge_small = $data_pinterest['blocklarge_small'];
	         $pinterestmicro_small = $data_pinterest['micro_small'];
	         $img_blockpinterestmicro_small = $data_pinterest['blockmicro_small'];
	         
	         
	         // oklass connect image
	          
	         $data_oklass = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>73,
	         				'type_medium'=>74,
	         				'type_small'=>75,
	         				'type_very_small'=>76,
	         				'prefix'=>'oklass',
	         		)
	         );
	         $oklass = $data_oklass['large'];
	         $img_blockoklass = $data_oklass['blocklarge'];
	         $oklasssmall = $data_oklass['small'];
	         $img_blockoklasssmall = $data_oklass['blocksmall'];
	         $oklasslarge_small = $data_oklass['large_small'];
	         $img_blockoklasslarge_small = $data_oklass['blocklarge_small'];
	         $oklassmicro_small = $data_oklass['micro_small'];
	         $img_blockoklassmicro_small = $data_oklass['blockmicro_small'];
	         
	         
	         // mailru connect image
	          
	         $data_mailru = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>77,
	         				'type_medium'=>78,
	         				'type_small'=>79,
	         				'type_very_small'=>80,
	         				'prefix'=>'mailru',
	         		)
	         );
	         $mailru = $data_mailru['large'];
	         $img_blockmailru = $data_mailru['blocklarge'];
	         $mailrusmall = $data_mailru['small'];
	         $img_blockmailrusmall = $data_mailru['blocksmall'];
	         $mailrularge_small = $data_mailru['large_small'];
	         $img_blockmailrularge_small = $data_mailru['blocklarge_small'];
	         $mailrumicro_small = $data_mailru['micro_small'];
	         $img_blockmailrumicro_small = $data_mailru['blockmicro_small'];
	         
	         
	         
	         // yandex connect image
	          
	         $data_yandex = $this->_getConnectImages(
	         		array(
	         				'http_host'=>$_http_host,
	         				'id_shop'=>$id_shop,
	         				'type_large'=>81,
	         				'type_medium'=>82,
	         				'type_small'=>83,
	         				'type_very_small'=>84,
	         				'prefix'=>'yandex',
	         		)
	         );
	         $yandex = $data_yandex['large'];
	         $img_blockyandex = $data_yandex['blocklarge'];
	         $yandexsmall = $data_yandex['small'];
	         $img_blockyandexsmall = $data_yandex['blocksmall'];
	         $yandexlarge_small = $data_yandex['large_small'];
	         $img_blockyandexlarge_small = $data_yandex['blocklarge_small'];
	         $yandexmicro_small = $data_yandex['micro_small'];
	         $img_blockyandexmicro_small = $data_yandex['blockmicro_small'];


        // vkontakte connect image

        $data_vkontakte = $this->_getConnectImages(
            array(
                'http_host'=>$_http_host,
                'id_shop'=>$id_shop,
                'type_large'=>45,
                'type_medium'=>46,
                'type_small'=>47,
                'type_very_small'=>48,
                'prefix'=>'vkontakte',
            )
        );
        $vkontakte = $data_vkontakte['large'];
        $img_blockvkontakte = $data_vkontakte['blocklarge'];
        $vkontaktesmall = $data_vkontakte['small'];
        $img_blockvkontaktesmall = $data_vkontakte['blocksmall'];
        $vkontaktelarge_small = $data_vkontakte['large_small'];
        $img_blockvkontaktelarge_small = $data_vkontakte['blocklarge_small'];
        $vkontaktemicro_small = $data_vkontakte['micro_small'];
        $img_blockvkontaktemicro_small = $data_vkontakte['blockmicro_small'];
	         	
				
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
					
					
						 'dropbox' => $dropbox, 'dropbox_block' => $img_blockdropbox,
						 'dropboxsmall' => $dropboxsmall, 'dropbox_blocksmall' => $img_blockdropboxsmall,
						 'dropboxlarge_small'=>$dropboxlarge_small, 'dropbox_blocklarge_small' => $img_blockdropboxlarge_small,
						 'dropboxmicro_small'=>$dropboxmicro_small, 'dropbox_blockmicro_small' => $img_blockdropboxmicro_small,
					
						 'scoop' => $scoop, 'scoop_block' => $img_blockscoop,
						 'scoopsmall' => $scoopsmall, 'scoop_blocksmall' => $img_blockscoopsmall,
						 'scooplarge_small'=>$scooplarge_small, 'scoop_blocklarge_small' => $img_blockscooplarge_small,
						 'scoopmicro_small'=>$scoopmicro_small, 'scoop_blockmicro_small' => $img_blockscoopmicro_small,
					
						 'wordpress' => $wordpress, 'wordpress_block' => $img_blockwordpress,
						 'wordpresssmall' => $wordpresssmall, 'wordpress_blocksmall' => $img_blockwordpresssmall,
						 'wordpresslarge_small'=>$wordpresslarge_small, 'wordpress_blocklarge_small' => $img_blockwordpresslarge_small,
						 'wordpressmicro_small'=>$wordpressmicro_small, 'wordpress_blockmicro_small' => $img_blockwordpressmicro_small,
					
						 'tumblr' => $tumblr, 'tumblr_block' => $img_blocktumblr,
						 'tumblrsmall' => $tumblrsmall, 'tumblr_blocksmall' => $img_blocktumblrsmall,
						 'tumblrlarge_small'=>$tumblrlarge_small, 'tumblr_blocklarge_small' => $img_blocktumblrlarge_small,
						 'tumblrmicro_small'=>$tumblrmicro_small, 'tumblr_blockmicro_small' => $img_blocktumblrmicro_small,
					
						 'pinterest' => $pinterest, 'pinterest_block' => $img_blockpinterest,
						 'pinterestsmall' => $pinterestsmall, 'pinterest_blocksmall' => $img_blockpinterestsmall,
						 'pinterestlarge_small'=>$pinterestlarge_small, 'pinterest_blocklarge_small' => $img_blockpinterestlarge_small,
						 'pinterestmicro_small'=>$pinterestmicro_small, 'pinterest_blockmicro_small' => $img_blockpinterestmicro_small,
						
						 'oklass' => $oklass, 'oklass_block' => $img_blockoklass,
						 'oklasssmall' => $oklasssmall, 'oklass_blocksmall' => $img_blockoklasssmall,
						 'oklasslarge_small'=>$oklasslarge_small, 'oklass_blocklarge_small' => $img_blockoklasslarge_small,
						 'oklassmicro_small'=>$oklassmicro_small, 'oklass_blockmicro_small' => $img_blockoklassmicro_small,
					
						 'mailru' => $mailru, 'mailru_block' => $img_blockmailru,
						 'mailrusmall' => $mailrusmall, 'mailru_blocksmall' => $img_blockmailrusmall,
						 'mailrularge_small'=>$mailrularge_small, 'mailru_blocklarge_small' => $img_blockmailrularge_small,
						 'mailrumicro_small'=>$mailrumicro_small, 'mailru_blockmicro_small' => $img_blockmailrumicro_small,
					
						 'yandex' => $yandex, 'yandex_block' => $img_blockyandex,
						 'yandexsmall' => $yandexsmall, 'yandex_blocksmall' => $img_blockyandexsmall,
						 'yandexlarge_small'=>$yandexlarge_small, 'yandex_blocklarge_small' => $img_blockyandexlarge_small,
						 'yandexmicro_small'=>$yandexmicro_small, 'yandex_blockmicro_small' => $img_blockyandexmicro_small,

                'vkontakte' => $vkontakte, 'vkontakte_block' => $img_blockvkontakte,
                'vkontaktesmall' => $vkontaktesmall, 'vkontakte_blocksmall' => $img_blockvkontaktesmall,
                'vkontaktelarge_small'=>$vkontaktelarge_small, 'vkontakte_blocklarge_small' => $img_blockvkontaktelarge_small,
                'vkontaktemicro_small'=>$vkontaktemicro_small, 'vkontakte_blockmicro_small' => $img_blockvkontaktemicro_small,
					
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
						
						} elseif ($custom_type_img == "dropbox") {
							$type_page = 53;
							$img_old_del = $data_img ['dropbox_block'];
						} elseif ($custom_type_img == "dropboxsmall") {
							$type_page = 54;
							$img_old_del = $data_img ['dropbox_blocksmall'];
						} elseif ($custom_type_img == "dropboxlarge_small") {
							$type_page = 55;
							$img_old_del = $data_img ['dropbox_blocklarge_small'];
						} elseif ($custom_type_img == "dropboxmicro_small") {
							$type_page = 56;
							$img_old_del = $data_img ['dropbox_blockmicro_small'];
						
						} elseif ($custom_type_img == "scoop") {
							$type_page = 57;
							$img_old_del = $data_img ['scoop_block'];
						} elseif ($custom_type_img == "scoopsmall") {
							$type_page = 58;
							$img_old_del = $data_img ['scoop_blocksmall'];
						} elseif ($custom_type_img == "scooplarge_small") {
							$type_page = 59;
							$img_old_del = $data_img ['scoop_blocklarge_small'];
						} elseif ($custom_type_img == "scoopmicro_small") {
							$type_page = 60;
							$img_old_del = $data_img ['scoop_blockmicro_small'];
						
						} elseif ($custom_type_img == "wordpress") {
							$type_page = 61;
							$img_old_del = $data_img ['wordpress_block'];
						} elseif ($custom_type_img == "wordpresssmall") {
							$type_page = 62;
							$img_old_del = $data_img ['wordpress_blocksmall'];
						} elseif ($custom_type_img == "wordpresslarge_small") {
							$type_page = 63;
							$img_old_del = $data_img ['wordpress_blocklarge_small'];
						} elseif ($custom_type_img == "wordpressmicro_small") {
							$type_page = 64;
							$img_old_del = $data_img ['wordpress_blockmicro_small'];
						
						} elseif ($custom_type_img == "tumblr") {
							$type_page = 65;
							$img_old_del = $data_img ['tumblr_block'];
						} elseif ($custom_type_img == "tumblrsmall") {
							$type_page = 66;
							$img_old_del = $data_img ['tumblr_blocksmall'];
						} elseif ($custom_type_img == "tumblrlarge_small") {
							$type_page = 67;
							$img_old_del = $data_img ['tumblr_blocklarge_small'];
						} elseif ($custom_type_img == "tumblrmicro_small") {
							$type_page = 68;
							$img_old_del = $data_img ['tumblr_blockmicro_small'];
						
						} elseif ($custom_type_img == "pinterest") {
							$type_page = 69;
							$img_old_del = $data_img ['pinterest_block'];
						} elseif ($custom_type_img == "pinterestsmall") {
							$type_page = 70;
							$img_old_del = $data_img ['pinterest_blocksmall'];
						} elseif ($custom_type_img == "pinterestlarge_small") {
							$type_page = 71;
							$img_old_del = $data_img ['pinterest_blocklarge_small'];
						} elseif ($custom_type_img == "pinterestmicro_small") {
							$type_page = 72;
							$img_old_del = $data_img ['pinterest_blockmicro_small'];
						
						} elseif ($custom_type_img == "oklass") {
							$type_page = 73;
							$img_old_del = $data_img ['oklass_block'];
						} elseif ($custom_type_img == "oklasssmall") {
							$type_page = 74;
							$img_old_del = $data_img ['oklass_blocksmall'];
						} elseif ($custom_type_img == "oklasslarge_small") {
							$type_page = 75;
							$img_old_del = $data_img ['oklass_blocklarge_small'];
						} elseif ($custom_type_img == "oklassmicro_small") {
							$type_page = 76;
							$img_old_del = $data_img ['oklass_blockmicro_small'];
						
						} elseif ($custom_type_img == "mailru") {
							$type_page = 77;
							$img_old_del = $data_img ['mailru_block'];
						} elseif ($custom_type_img == "mailrusmall") {
							$type_page = 78;
							$img_old_del = $data_img ['mailru_blocksmall'];
						} elseif ($custom_type_img == "mailrularge_small") {
							$type_page = 79;
							$img_old_del = $data_img ['mailru_blocklarge_small'];
						} elseif ($custom_type_img == "mailrumicro_small") {
							$type_page = 80;
							$img_old_del = $data_img ['mailru_blockmicro_small'];
						
						} elseif ($custom_type_img == "yandex") {
							$type_page = 81;
							$img_old_del = $data_img ['yandex_block'];
						} elseif ($custom_type_img == "yandexsmall") {
							$type_page = 82;
							$img_old_del = $data_img ['yandex_blocksmall'];
						} elseif ($custom_type_img == "yandexlarge_small") {
							$type_page = 83;
							$img_old_del = $data_img ['yandex_blocklarge_small'];
						} elseif ($custom_type_img == "yandexmicro_small") {
							$type_page = 84;
							$img_old_del = $data_img ['yandex_blockmicro_small'];
						
						}elseif ($custom_type_img == "vkontakte") {
                            $type_page = 45;
                            $img_old_del = $data_img ['vkontakte_block'];
                        } elseif ($custom_type_img == "vkontaktesmall") {
                            $type_page = 46;
                            $img_old_del = $data_img ['vkontakte_blocksmall'];
                        } elseif ($custom_type_img == "vkontaktelarge_small") {
                            $type_page = 47;
                            $img_old_del = $data_img ['vkontakte_blocklarge_small'];
                        } elseif ($custom_type_img == "vkontaktemicro_small") {
                            $type_page = 48;
                            $img_old_del = $data_img ['vkontakte_blockmicro_small'];

                        }
    					
    					
    					
				  		if(Tools::strlen($img_old_del)>0){
				  			// delete old img
				  			unlink(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_old_del);
				  		} 
					
				  			
					  	srand((double)microtime()*1000000);
					 	$uniq_name_image = uniqid(rand());
					 	$type_one = Tools::substr($type_one,6,Tools::strlen($type_one)-6);
					 	$filename = $uniq_name_image.'.'.$type_one; 
					 	
						move_uploaded_file($files['tmp_name'], _PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$filename);
						
						
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
			
		
		@unlink(_PS_ROOT_DIR_.DIRECTORY_SEPARATOR."upload".DIRECTORY_SEPARATOR.$this->_name.DIRECTORY_SEPARATOR.$img_delete);
				  		
	}
	
	
	
	public function facebookLogin($_data){
		
		
		$http_referer = isset($_data['http_referer_custom'])?$_data['http_referer_custom']:'';
			
		
	    if (version_compare(_PS_VERSION_, '1.5', '>')){
			$cookie = new Cookie('ref');
			$cookie->http_referer_custom = $http_referer;
		}
		
		
		$name_module = $this->_name;
		//$cookie = Context::getContext()->cookie;
		
		
		//$me = array();
		
		
		$appid = Configuration::get($name_module.'appid');
        $appid = trim($appid);
		$secret = Configuration::get($name_module.'secret');
		$secret = trim($secret);


        if(Tools::strlen($appid)==0 || Tools::strlen($secret)==0){
            include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
			$obj = new $this->_name();
			$data_tr = $obj->translateCustom();
			echo $data_tr['facebook'];exit;
		}









        ### fix for oauth 18.10.2017 ###
        include_once(_PS_MODULE_DIR_.$this->_name.'/classes/facebookSdkCustomhelper.class.php');
        $facebookSdkCustomhelper = new facebookSdkCustomhelper();
        $data_facebookSdkCustomhelper = $facebookSdkCustomhelper->connectionSDKCustom(array('appid'=>$appid,'secret'=>$secret));

        $me = $data_facebookSdkCustomhelper['me'];
        $access_token = $data_facebookSdkCustomhelper['access_token'];
        ### fix for oauth 18.10.2017 ###





		/*


		#### new facebook api ###
		include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/http.php';
		include_once _PS_MODULE_DIR_.$this->_name.'/lib/microsoft/oauth_client.php';


        ## fixed bug, when customer declined Facebook email permissions and try login again ###
        if(!empty($_SESSION['OAUTH_ACCESS_TOKEN']))
            unset($_SESSION['OAUTH_ACCESS_TOKEN']);
        ## fixed bug, when customer declined Facebook email permissions and try login again ###


		 $client = new oauth_client_class();
		$client->server = 'Facebook';
		

		include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
		$obj_module = new $name_module();
		$redirect_uri = $obj_module->getRedirectURL(array('typelogin'=>'facebook','is_settings'=>1));
        $client->redirect_uri = $redirect_uri;
			
		$client->client_id = $appid;
		$client->client_secret = $secret;
		
		//API permissions

		$client->scope = 'public_profile,email,user_birthday';
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
					$access_token = $client->access_token;
					$success = $client->CallAPI(
							'https://graph.facebook.com/me', 'GET', array(), array('FailOnAccessError' => true), $user);
				}
			}
			$success = $client->Finalize($success);
		}
		if($client->exit)
			exit;
		if($success)
		{
			 
			$me = (array)$user;
			
		
		}
		else
		{
			echo 'Error:'.HtmlSpecialChars($client->error);exit;
		}*/
		



        /*## upadte api 16/10/2017 ##
        $code = Tools::getValue('code');
        $permissions = "email";

        include_once _PS_MODULE_DIR_.$this->_name.'/'.$name_module.'.php';
        $obj_module = new $name_module();
        $redirect_uri = $obj_module->getRedirectURL(array('typelogin'=>'facebook','is_settings'=>1));


        if(!$code){
            $start_url = "https://www.facebook.com/v2.10/dialog/oauth";

            $redirect_uri = urlencode($redirect_uri);
            $permissions = $permissions;

            $state = md5(uniqid(rand(), true));

            $start_url .= '?client_id=' . $appid  . '&redirect_uri=' . $redirect_uri . '&scope=' . $permissions.'&state=' . $state;


            header('location:' . $start_url);
            exit;

        } else {

            $graph_url = 'https://graph.facebook.com/oauth/access_token';
            $graph_url .= '?client_id=' . $appid  . '&redirect_uri=' . urlencode($redirect_uri) . '&client_secret=' . $secret;

            $access_token_data = Tools::file_get_contents($graph_url . '&code=' . $code);

            $access_token_data = json_decode($access_token_data);


            $access_token = $access_token_data->access_token;

            $permissions_me = "email,id,first_name,last_name,name,birthday,gender";

            $user_data = Tools::file_get_contents('https://graph.facebook.com/v2.10/me?access_token=' . $access_token . '&fields=' . $permissions_me);

            $user_data = json_decode($user_data);
            $me = (array)$user_data;
        }

        ## upadte api 16/10/2017 ##*/



		
		if(version_compare(_PS_VERSION_, '1.5', '>')){
			$id_shop = Context::getContext()->shop->id;
		} else {
			$id_shop = 0;
		}

		### fix for updated API ###
		if(empty($me['email'])){
			
				$url_fix = 'https://graph.facebook.com/me?access_token='.$access_token.'&fields=email,id,first_name,last_name,name,birthday,gender';
			
				if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
					$data = Tools::file_get_contents($url_fix);
				} else {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url_fix);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					$data = curl_exec($ch);
				}
				$me = json_decode($data);
                //echo "<pre>"; var_dump($me);
				$me = (array)$me;
		}

		### fix for updated API ###

        $is_false_generated = 0;
        if(empty($me['email'])){
            srand((double)microtime()*1000000);
            $em = Tools::substr(uniqid(rand()),0,12);
            $me['email'] = $em.'@api-not-provide-email-'.$this->_social_type.'.com';
            $is_false_generated = 1;
        }

		
		
		if (is_array($me)) {
			
			$sql= 'SELECT `customer_id`
							FROM `'._DB_PREFIX_.'fb_customer`
							WHERE `fb_id` = '.pSQL($me['id']).' AND `id_shop` = '.(int)($id_shop).'
							LIMIT 1';
			$result = Db::getInstance()->ExecuteS($sql);
			
			if(sizeof($result)>0)
				$customer_id = $result[0]['customer_id'];
			else
				$customer_id = 0;
		
		}
		
		$exists_mail = 0;
		//chek for dublicate
		if(!empty($me['email'])){
            if(version_compare(_PS_VERSION_, '1.5', '>')){
                $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
                                WHERE `email` = \''.pSQL($me['email']).'\'
                                AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").' AND `id_shop` = '.(int)($id_shop).'';
            } else {
                $sql = 'SELECT * FROM `'._DB_PREFIX_   .'customer`
                                WHERE  `email` = \''.pSQL($me['email']).'\'
                                AND `deleted` = 0 '.(defined('_MYSQL_ENGINE_')?"AND `is_guest` = 0":"").'';
            }
			$result_exists_mail = Db::getInstance()->GetRow($sql);
			if($result_exists_mail)
			    $exists_mail = 1;

            ## if customer already registered with facebook, but deleted in admin panel -> Customers ##
            if($customer_id) {
                if (version_compare(_PS_VERSION_, '1.5', '>')) {
                    $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'customer`
                                WHERE `id_customer` = ' . (int)($customer_id) . '
                                AND `deleted` = 0 ' . (defined('_MYSQL_ENGINE_') ? "AND `is_guest` = 0" : "") . ' AND `id_shop` = ' . (int)($id_shop) . '';
                } else {
                    $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'customer`
                                WHERE `id_customer` = ' . (int)($customer_id) . '
                                AND `deleted` = 0 ' . (defined('_MYSQL_ENGINE_') ? "AND `is_guest` = 0" : "") . '';
                }
                $result_mail = Db::getInstance()->GetRow($sql);
                $email_for_exists_customer = isset($result_mail['email'])?$result_mail['email']:null;

                if(!empty($email_for_exists_customer)){
                    $me['email']= $email_for_exists_customer;
                    $exists_mail = 1;
                }
            }
            ## if customer already registered with facebook, but deleted in admin panel -> Customers ##

		}


		
		$auth = 0;
		if($customer_id && $exists_mail){

            ## if customer disabled ##
            if(!empty($result_exists_mail) && $result_exists_mail['active'] == 0){
                include_once(_PS_MODULE_DIR_.$this->_name.'/'.$this->_name.'.php');
                $obj = new $this->_name();
                $data_tr = $obj->translateCustom();
                echo $data_tr['disabled'];exit;
            }
            ## if customer disabled ##

        	$auth = 1;
		}
		
		if(empty($customer_id) &&  $exists_mail){
			// insert record into customerXfacebook table
			$sql = 'INSERT into `'._DB_PREFIX_.'fb_customer` SET
							customer_id = '.(int)$result_exists_mail['id_customer'].',
							fb_id = '.pSQL($me['id']).',
							id_shop = '.(int)$id_shop.' ';
			Db::getInstance()->Execute($sql);
			
			$auth = 1;
		}


        ## add new functional for auth and create user ##
        $gender = ($me['gender'] == 'male')?1:2;
        $first_name = isset($me['first_name'])?pSQL($me['first_name']):'';
        $last_name = isset($me['first_name'])?pSQL($me['last_name']):'';
        $email = isset($me['email'])?$me['email']:'';
        $birthday = isset($me['birthday'])?$me['birthday']:'';

        $data_profile = array(
                                'email'=>$email,
                                'first_name'=>$first_name,
                                'last_name'=>$last_name,
                                'gender'=>$gender,
                                'birthday'=>$birthday,


                              );

        include_once _PS_MODULE_DIR_.$this->_name.'/classes/userhelp.class.php';
        $userhelp = new userhelp();
        $userhelp->userLog(
            array(
                'data_profile'=>$data_profile,
                'http_referer_custom'=>$http_referer,
                'is_false_generated'=>$is_false_generated,
                'me_facebook_id'=>$me['id'],
                'auth'=>$auth,
                'type'=>$this->_social_type,
            )
        );


        ## add new functional for auth and create user ##



		
		
	}


    public  function insertCustomerXFacebook($data){


        $me_facebook_id = $data['me_facebook_id'];
        $insert_id = $data['insert_id'];


        $id_shop = $data['id_shop'];

        // insert record into customerXfacebook table
        $sql_exists= 'SELECT `customer_id`
								FROM `'._DB_PREFIX_.'fb_customer`
								WHERE `fb_id` = '.(int)($me_facebook_id).' AND `id_shop` = '.(int)($id_shop).'
								LIMIT 1';
        $result_exists = Db::getInstance()->ExecuteS($sql_exists);
        if(sizeof($result_exists)>0)
            $customer_id = $result_exists[0]['customer_id'];
        else
            $customer_id = 0;

        if($customer_id){
            $sql_del = 'DELETE FROM `'._DB_PREFIX_.'fb_customer` WHERE `customer_id` = '.(int)$customer_id.' AND `id_shop` = '.(int)$id_shop.'';
            Db::getInstance()->Execute($sql_del);

        }

        $sql = 'INSERT into `'._DB_PREFIX_.'fb_customer` SET
							customer_id = '.(int)$insert_id.', fb_id = '.(int)$me_facebook_id.', id_shop = '.(int)$id_shop.' ';
        Db::getInstance()->Execute($sql);

        //// end create new user ///
    }







	
}


