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

function return_default_img(type,text){
        	if(confirm(text))
        	{
        	
        	if(type=="facebook")
            	$('#imagef').css('opacity',0.5);	
            if(type=="facebooksmall")
            	$('#imagefsmall').css('opacity',0.5);
            if(type=="facebooklarge_small")
            	$('#imageflarge_small').css('opacity',0.5);
            if(type=="facebookmicro_small")
            	$('#imagefmicro_small').css('opacity',0.5);
            	
            	
            if(type=="twitter")
            	$('#imaget').css('opacity',0.5);
            if(type=="twittersmall")
            	$('#imagetsmall').css('opacity',0.5);
            if(type=="twitterlarge_small")
            	$('#imagetlarge_small').css('opacity',0.5);
            if(type=="twittermicro_small")
            	$('#imagetmicro_small').css('opacity',0.5);
            	
            if(type=="google")
        		$('#imageg').css('opacity',0.5);
        	if(type=="googlesmall")
        		$('#imagegsmall').css('opacity',0.5);
        	if(type=="googlelarge_small")
            	$('#imageglarge_small').css('opacity',0.5);
            if(type=="googlemicro_small")
            	$('#imagegmicro_small').css('opacity',0.5);
            
            if(type=="yahoo")
        		$('#imagey').css('opacity',0.5);
        	if(type=="yahoosmall")
        		$('#imageysmall').css('opacity',0.5);
        	if(type=="yahoolarge_small")
            	$('#imageylarge_small').css('opacity',0.5);
            if(type=="yahoomicro_small")
            	$('#imageymicro_small').css('opacity',0.5);
            
            
            if(type=="linkedin")
        		$('#imagel').css('opacity',0.5);
        	if(type=="linkedinsmall")
        		$('#imagelsmall').css('opacity',0.5);
        	if(type=="linkedinlarge_small")
            	$('#imagellarge_small').css('opacity',0.5);
            if(type=="linkedinmicro_small")
            	$('#imagelmicro_small').css('opacity',0.5);
        	
            
            if(type=="microsoft")
        		$('#imagem').css('opacity',0.5);
        	if(type=="microsoftsmall")
        		$('#imagemsmall').css('opacity',0.5);
        	if(type=="microsoftlarge_small")
            	$('#imagemlarge_small').css('opacity',0.5);
            if(type=="microsoftmicro_small")
            	$('#imagemmicro_small').css('opacity',0.5);
        	
            
        	if(type=="instagram")
            	$('#imagei').css('opacity',0.5);
            if(type=="instagramsmall")
            	$('#imageismall').css('opacity',0.5);
            if(type=="instagramlarge_small")
            	$('#imageilarge_small').css('opacity',0.5);
            if(type=="instagrammicro_small")
            	$('#imageimicro_small').css('opacity',0.5);	
            	
        	
            if(type=="foursquare")
            	$('#imagefs').css('opacity',0.5);
            if(type=="foursquaresmall")
            	$('#imagefssmall').css('opacity',0.5);
            if(type=="foursquarelarge_small")
            	$('#imagefslarge_small').css('opacity',0.5);
            if(type=="foursquaremicro_small")
            	$('#imagefsmicro_small').css('opacity',0.5);
            
            
            if(type=="github")
            	$('#imagegi').css('opacity',0.5);
            if(type=="githubsmall")
            	$('#imagegismall').css('opacity',0.5);
            if(type=="githublarge_small")
            	$('#imagegilarge_small').css('opacity',0.5);
            if(type=="githubmicro_small")
            	$('#imagegimicro_small').css('opacity',0.5);
            
            
            if(type=="disqus")
            	$('#imaged').css('opacity',0.5);
            if(type=="disqussmall")
            	$('#imagedsmall').css('opacity',0.5);
            if(type=="disquslarge_small")
            	$('#imagedlarge_small').css('opacity',0.5);
            if(type=="disqusmicro_small")
            	$('#imagedmicro_small').css('opacity',0.5);
            
            
            if(type=="amazon")
            	$('#imagea').css('opacity',0.5);
            if(type=="amazonsmall")
            	$('#imageasmall').css('opacity',0.5);
            if(type=="amazonlarge_small")
            	$('#imagealarge_small').css('opacity',0.5);
            if(type=="amazonmicro_small")
            	$('#imageamicro_small').css('opacity',0.5); 
            
        	
        	$.post('../modules/fbloginblock/ajax/admin_image.php', {
        		action:'returnimage',
        		type : type
        	}, 
        	function (data) {
        		if (data.status == 'success') {
        			
        			if(type=="amazon"){
                		$('#imagea').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagea').src = "";
            			document.getElementById('imagea').src = "../modules/fbloginblock/views/img/amazon.png?re=" + count;
            			$('#imagea-click').remove();
        			}
        			
        			if(type=="amazonsmall"){
                		$('#imageasmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageasmall').src = "";
            			document.getElementById('imageasmall').src = "../modules/fbloginblock/views/img/amazon-small.png?re=" + count;
            			$('#imagea-clicksmall').remove();
        			}
        			if(type=="amazonlarge_small"){
                		$('#imagealarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagealarge_small').src = "";
            			document.getElementById('imagealarge_small').src = "../modules/fbloginblock/views/img/amazon-large-small.png?re=" + count;
            			$('#imagea-clicklarge_small').remove();
        			}
        			if(type=="amazonmicro_small"){
                		$('#imageamicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageamicro_small').src = "";
            			document.getElementById('imageamicro_small').src = "../modules/fbloginblock/views/img/amazon-small-micro.png?re=" + count;
            			$('#imagea-clickmicro_small').remove();
        			}
        			
        			
        			if(type=="facebook"){
                		$('#imagef').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagef').src = "";
            			document.getElementById('imagef').src = "../modules/fbloginblock/views/img/facebook.png?re=" + count;
            			$('#imagef-click').remove();
        			}
        			if(type=="facebooksmall"){
                		$('#imagefsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagefsmall').src = "";
            			document.getElementById('imagefsmall').src = "../modules/fbloginblock/views/img/facebook-small.png?re=" + count;
            			$('#imagef-clicksmall').remove();
        			}
        			if(type=="facebooklarge_small"){
                		$('#imageflarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageflarge_small').src = "";
            			document.getElementById('imageflarge_small').src = "../modules/fbloginblock/views/img/facebook-large-small.png?re=" + count;
            			$('#imagef-clicklarge_small').remove();
        			}
        			if(type=="facebookmicro_small"){
                		$('#imagefmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagefmicro_small').src = "";
            			document.getElementById('imagefmicro_small').src = "../modules/fbloginblock/views/img/facebook-small-micro.png?re=" + count;
            			$('#imagef-clickmicro_small').remove();
        			}
        		
        			
        			if(type=="twitter"){
                		$('#imaget').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imaget').src = "";
            			document.getElementById('imaget').src = "../modules/fbloginblock/views/img/twitter.png?re=" + count;
            			$('#imaget-click').remove();
        			}
        			if(type=="twittersmall"){
                		$('#imagetsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagetsmall').src = "";
            			document.getElementById('imagetsmall').src = "../modules/fbloginblock/views/img/twitter-small.png?re=" + count;
            			$('#imaget-clicksmall').remove();
        			}
        			if(type=="twitterlarge_small"){
                		$('#imagetlarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagetlarge_small').src = "";
            			document.getElementById('imagetlarge_small').src = "../modules/fbloginblock/views/img/twitter-large-small.png?re=" + count;
            			$('#imaget-clicklarge_small').remove();
        			}
        			if(type=="twittermicro_small"){
                		$('#imagetmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagetmicro_small').src = "";
            			document.getElementById('imagetmicro_small').src = "../modules/fbloginblock/views/img/twitter-small-micro.png?re=" + count;
            			$('#imaget-clickmicro_small').remove();
        			}
        			
        			
        			
        			if(type=="google"){
                		$('#imageg').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageg').src = "";
            			document.getElementById('imageg').src = "../modules/fbloginblock/views/img/google.png?re=" + count;
            			$('#imageg-click').remove();
        			}
        			if(type=="googlesmall"){
                		$('#imagegsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagegsmall').src = "";
            			document.getElementById('imagegsmall').src = "../modules/fbloginblock/views/img/google-small.png?re=" + count;
            			$('#imageg-clicksmall').remove();
        			}
        			if(type=="googlelarge_small"){
                		$('#imageglarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageglarge_small').src = "";
            			document.getElementById('imageglarge_small').src = "../modules/fbloginblock/views/img/google-large-small.png?re=" + count;
            			$('#imageg-clicklarge_small').remove();
        			}
        			if(type=="googlemicro_small"){
                		$('#imagegmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagegmicro_small').src = "";
            			document.getElementById('imagegmicro_small').src = "../modules/fbloginblock/views/img/google-small-micro.png?re=" + count;
            			$('#imageg-clickmicro_small').remove();
        			}
        			
        			
        			if(type=="yahoo"){
                		$('#imagey').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagey').src = "";
            			document.getElementById('imagey').src = "../modules/fbloginblock/views/img/yahoo.png?re=" + count;
            			$('#imagey-click').remove();
        			}
        			if(type=="yahoosmall"){
                		$('#imageysmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageysmall').src = "";
            			document.getElementById('imageysmall').src = "../modules/fbloginblock/views/img/yahoo-small.png?re=" + count;
            			$('#imagey-clicksmall').remove();
        			}
        			if(type=="yahoolarge_small"){
                		$('#imageylarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageylarge_small').src = "";
            			document.getElementById('imageylarge_small').src = "../modules/fbloginblock/views/img/yahoo-large-small.png?re=" + count;
            			$('#imagey-clicklarge_small').remove();
        			}
        			if(type=="yahoomicro_small"){
                		$('#imageymicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageymicro_small').src = "";
            			document.getElementById('imageymicro_small').src = "../modules/fbloginblock/views/img/yahoo-small-micro.png?re=" + count;
            			$('#imagey-clickmicro_small').remove();
        			}
        			
        			
        			
        			if(type=="linkedin"){
                		$('#imagel').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagel').src = "";
            			document.getElementById('imagel').src = "../modules/fbloginblock/views/img/linkedin.png?re=" + count;
            			$('#imagel-click').remove();
        			}
        			
        			if(type=="linkedinsmall"){
                		$('#imagelsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagelsmall').src = "";
            			document.getElementById('imagelsmall').src = "../modules/fbloginblock/views/img/linkedin-small.png?re=" + count;
            			$('#imagel-clicksmall').remove();
        			}
        			if(type=="linkedinlarge_small"){
                		$('#imagellarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagellarge_small').src = "";
            			document.getElementById('imagellarge_small').src = "../modules/fbloginblock/views/img/linkedin-large-small.png?re=" + count;
            			$('#imagel-clicklarge_small').remove();
        			}
        			if(type=="linkedinmicro_small"){
                		$('#imagelmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagelmicro_small').src = "";
            			document.getElementById('imagelmicro_small').src = "../modules/fbloginblock/views/img/linkedin-small-micro.png?re=" + count;
            			$('#imagel-clickmicro_small').remove();
        			}
        			
        			
        			
        			
        			
        			if(type=="microsoft"){
                		$('#imagem').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagem').src = "";
            			document.getElementById('imagem').src = "../modules/fbloginblock/views/img/microsoft.png?re=" + count;
            			$('#imagem-click').remove();
        			}
        			
        			if(type=="microsoftsmall"){
                		$('#imagemsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagemsmall').src = "";
            			document.getElementById('imagemsmall').src = "../modules/fbloginblock/views/img/microsoft-small.png?re=" + count;
            			$('#imagem-clicksmall').remove();
        			}
        			if(type=="microsoftlarge_small"){
                		$('#imagemlarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagemlarge_small').src = "";
            			document.getElementById('imagemlarge_small').src = "../modules/fbloginblock/views/img/microsoft-large-small.png?re=" + count;
            			$('#imagem-clicklarge_small').remove();
        			}
        			if(type=="microsoftmicro_small"){
                		$('#imagemmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagemmicro_small').src = "";
            			document.getElementById('imagemmicro_small').src = "../modules/fbloginblock/views/img/microsoft-small-micro.png?re=" + count;
            			$('#imagem-clickmicro_small').remove();
        			}
        			
        			
        			
        			
        			if(type=="instagram"){
                		$('#imagei').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagei').src = "";
            			document.getElementById('imagei').src = "../modules/fbloginblock/views/img/instagram.png?re=" + count;
            			$('#imagei-click').remove();
        			}
        			
        			if(type=="instagramsmall"){
                		$('#imageismall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageismall').src = "";
            			document.getElementById('imageismall').src = "../modules/fbloginblock/views/img/instagram-small.png?re=" + count;
            			$('#imagei-clicksmall').remove();
        			}
        			if(type=="instagramlarge_small"){
                		$('#imageilarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageilarge_small').src = "";
            			document.getElementById('imageilarge_small').src = "../modules/fbloginblock/views/img/instagram-large-small.png?re=" + count;
            			$('#imagei-clicklarge_small').remove();
        			}
        			if(type=="instagrammicro_small"){
                		$('#imageimicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageimicro_small').src = "";
            			document.getElementById('imageimicro_small').src = "../modules/fbloginblock/views/img/instagram-small-micro.png?re=" + count;
            			$('#imagei-clickmicro_small').remove();
        			}
        			
        			
        			
        			if(type=="foursquare"){
                		$('#imagefs').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagefs').src = "";
            			document.getElementById('imagefs').src = "../modules/fbloginblock/views/img/foursquare.png?re=" + count;
            			$('#imagefs-click').remove();
        			}
        			
        			if(type=="foursquaresmall"){
                		$('#imagefssmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagefssmall').src = "";
            			document.getElementById('imagefssmall').src = "../modules/fbloginblock/views/img/foursquare-small.png?re=" + count;
            			$('#imagefs-clicksmall').remove();
        			}
        			if(type=="foursquarelarge_small"){
                		$('#imagefslarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagefslarge_small').src = "";
            			document.getElementById('imagefslarge_small').src = "../modules/fbloginblock/views/img/foursquare-large-small.png?re=" + count;
            			$('#imagefs-clicklarge_small').remove();
        			}
        			if(type=="foursquaremicro_small"){
                		$('#imagefsmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagefsmicro_small').src = "";
            			document.getElementById('imagefsmicro_small').src = "../modules/fbloginblock/views/img/foursquare-small-micro.png?re=" + count;
            			$('#imagefs-clickmicro_small').remove();
        			}
        			
        			
        			if(type=="github"){
                		$('#imagegi').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagegi').src = "";
            			document.getElementById('imagegi').src = "../modules/fbloginblock/views/img/github.png?re=" + count;
            			$('#imagegi-click').remove();
        			}
        			if(type=="githubsmall"){
                		$('#imagegismall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagegismall').src = "";
            			document.getElementById('imagegismall').src = "../modules/fbloginblock/views/img/github-small.png?re=" + count;
            			$('#imagegi-clicksmall').remove();
        			}
        			if(type=="githublarge_small"){
                		$('#imagegilarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagegilarge_small').src = "";
            			document.getElementById('imagegilarge_small').src = "../modules/fbloginblock/views/img/github-large-small.png?re=" + count;
            			$('#imagegi-clicklarge_small').remove();
        			}
        			if(type=="githubmicro_small"){
                		$('#imagegimicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagegimicro_small').src = "";
            			document.getElementById('imagegimicro_small').src = "../modules/fbloginblock/views/img/github-small-micro.png?re=" + count;
            			$('#imagegi-clickmicro_small').remove();
        			}
        			
        			
        			
        			if(type=="disqus"){
                		$('#imaged').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imaged').src = "";
            			document.getElementById('imaged').src = "../modules/fbloginblock/views/img/disqus.png?re=" + count;
            			$('#imaged-click').remove();
        			}
        			if(type=="disqussmall"){
                		$('#imagedsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedsmall').src = "";
            			document.getElementById('imagedsmall').src = "../modules/fbloginblock/views/img/disqus-small.png?re=" + count;
            			$('#imaged-clicksmall').remove();
        			}
        			if(type=="disquslarge_small"){
                		$('#imagedlarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedlarge_small').src = "";
            			document.getElementById('imagedlarge_small').src = "../modules/fbloginblock/views/img/disqus-large-small.png?re=" + count;
            			$('#imaged-clicklarge_small').remove();
        			}
        			if(type=="disqusmicro_small"){
                		$('#imagedmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedmicro_small').src = "";
            			document.getElementById('imagedmicro_small').src = "../modules/fbloginblock/views/img/disqus-small-micro.png?re=" + count;
            			$('#imaged-clickmicro_small').remove();
        			}
                	
        		} else {
        			
        			if(type=="facebook")
                		$('#imagef').css('opacity',1);	
        			if(type=="facebooksmall")
                		$('#imagefsmall').css('opacity',1);
        			if(type=="facebooklarge_small")
                		$('#imageflarge_small').css('opacity',1);
        			if(type=="facebookmicro_small")
                		$('#imagefmicro_small').css('opacity',1);
        			
        			
        			if(type=="twitter")
                		$('#imaget').css('opacity',1);
        			if(type=="twittersmall")
                		$('#imagetsmall').css('opacity',1);
        			if(type=="twitterlarge_small")
                		$('#imagetlarge_small').css('opacity',1);
        			if(type=="twittermicro_small")
                		$('#imagetmicro_small').css('opacity',1);
        			
        			
        			if(type=="google")
                		$('#imageg').css('opacity',1);
        			if(type=="googlesmall")
                		$('#imagegsmall').css('opacity',1);
        			if(type=="googlelarge_small")
                		$('#imageglarge_small').css('opacity',1);
        			if(type=="googlemicro_small")
                		$('#imagegmicro_small').css('opacity',1);
        			
        			
        			
        			if(type=="yahoo")
                		$('#imagey').css('opacity',1);
        			if(type=="yahoosmall")
                		$('#imageysmall').css('opacity',1);
        			if(type=="yahoolarge_small")
                		$('#imageylarge_small').css('opacity',1);
        			if(type=="yahoomicro_small")
                		$('#imageymicro_small').css('opacity',1);
        			
        			
        			
        			if(type=="linkedin")
                		$('#imagel').css('opacity',1);	
        			if(type=="linkedinsmall")
                		$('#imagelsmall').css('opacity',1);
        			if(type=="linkedinlarge_small")
                		$('#imagellarge_small').css('opacity',1);
        			if(type=="linkedinmicro_small")
                		$('#imagelmicro_small').css('opacity',1);
        			
        			
        			if(type=="microsoft")
                		$('#imagem').css('opacity',1);	
        			if(type=="microsoftsmall")
                		$('#imagemsmall').css('opacity',1);
        			if(type=="microsoftlarge_small")
                		$('#imagemlarge_small').css('opacity',1);
        			if(type=="microsoftmicro_small")
                		$('#imagemmicro_small').css('opacity',1);
        			
        			
        			
        			if(type=="instagram")
                		$('#imagei').css('opacity',1);	
        			if(type=="instagramsmall")
                		$('#imageismall').css('opacity',1);
        			if(type=="instagramlarge_small")
                		$('#imageilarge_small').css('opacity',1);
        			if(type=="instagrammicro_small")
                		$('#imageimicro_small').css('opacity',1);
        			
        			
        			if(type=="foursquare")
                		$('#imagefs').css('opacity',1);	
        			if(type=="foursquaresmall")
                		$('#imagefssmall').css('opacity',1);
        			if(type=="foursquarelarge_small")
                		$('#imagefslarge_small').css('opacity',1);
        			if(type=="foursquaremicro_small")
                		$('#imagefsmicro_small').css('opacity',1);
        			
        			
        			if(type=="github")
                		$('#imagegi').css('opacity',1);	
        			if(type=="githubsmall")
                		$('#imagegismall').css('opacity',1);
        			if(type=="githublarge_small")
                		$('#imagegilarge_small').css('opacity',1);
        			if(type=="githubmicro_small")
                		$('#imagegimicro_small').css('opacity',1);
        			
        			
        			if(type=="disqus")
                		$('#imaged').css('opacity',1);	
        			if(type=="disqussmall")
                		$('#imagedsmall').css('opacity',1);
        			if(type=="disquslarge_small")
                		$('#imagedlarge_small').css('opacity',1);
        			if(type=="disqusmicro_small")
                		$('#imagedmicro_small').css('opacity',1);
        			
        			if(type=="amazon")
                		$('#imagea').css('opacity',1);	
        			if(type=="amazonsmall")
                		$('#imageasmall').css('opacity',1);
        			if(type=="amazonlarge_small")
                		$('#imagealarge_small').css('opacity',1);
        			if(type=="amazonmicro_small")
                		$('#imageamicro_small').css('opacity',1);
        			
        			
        			
        			alert(data.message);
        		}
        		
        	}, 'json');
        	}

        }