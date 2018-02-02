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
            
            
            if(type=="dropbox")
            	$('#imagedb').css('opacity',0.5);
            if(type=="dropboxsmall")
            	$('#imagedbsmall').css('opacity',0.5);
            if(type=="dropboxlarge_small")
            	$('#imagedblarge_small').css('opacity',0.5);
            if(type=="dropboxmicro_small")
            	$('#imagedbmicro_small').css('opacity',0.5);
            
            
            if(type=="scoop")
            	$('#images').css('opacity',0.5);
            if(type=="scoopsmall")
            	$('#imagessmall').css('opacity',0.5);
            if(type=="scooplarge_small")
            	$('#imageslarge_small').css('opacity',0.5);
            if(type=="scoopmicro_small")
            	$('#imagesmicro_small').css('opacity',0.5);
            
            
            if(type=="wordpress")
            	$('#imagew').css('opacity',0.5);
            if(type=="wordpresssmall")
            	$('#imagewsmall').css('opacity',0.5);
            if(type=="wordpresslarge_small")
            	$('#imagewlarge_small').css('opacity',0.5);
            if(type=="wordpressmicro_small")
            	$('#imagewmicro_small').css('opacity',0.5);
            
            
            if(type=="tumblr")
            	$('#imagetu').css('opacity',0.5);
            if(type=="tumblrsmall")
            	$('#imagetusmall').css('opacity',0.5);
            if(type=="tumblrlarge_small")
            	$('#imagetularge_small').css('opacity',0.5);
            if(type=="tumblrmicro_small")
            	$('#imagetumicro_small').css('opacity',0.5);
            
            
            if(type=="pinterest")
            	$('#imagepi').css('opacity',0.5);
            if(type=="pinterestsmall")
            	$('#imagepismall').css('opacity',0.5);
            if(type=="pinterestlarge_small")
            	$('#imagepilarge_small').css('opacity',0.5);
            if(type=="pinterestmicro_small")
            	$('#imagepimicro_small').css('opacity',0.5);
            
            
            if(type=="oklass")
            	$('#imageo').css('opacity',0.5);
            if(type=="oklasssmall")
            	$('#imageosmall').css('opacity',0.5);
            if(type=="oklasslarge_small")
            	$('#imageolarge_small').css('opacity',0.5);
            if(type=="oklassmicro_small")
            	$('#imageomicro_small').css('opacity',0.5);
            
            
            if(type=="mailru")
            	$('#imagema').css('opacity',0.5);
            if(type=="mailrusmall")
            	$('#imagemasmall').css('opacity',0.5);
            if(type=="mailrularge_small")
            	$('#imagemalarge_small').css('opacity',0.5);
            if(type=="mailrumicro_small")
            	$('#imagemamicro_small').css('opacity',0.5);
            
            
            if(type=="yandex")
            	$('#imageya').css('opacity',0.5);
            if(type=="yandexsmall")
            	$('#imageyasmall').css('opacity',0.5);
            if(type=="yandexlarge_small")
            	$('#imageyalarge_small').css('opacity',0.5);
            if(type=="yandexmicro_small")
            	$('#imageyamicro_small').css('opacity',0.5);

                if(type=="paypal")
                    $('#imagep').css('opacity',0.5);
                if(type=="paypalsmall")
                    $('#imagepsmall').css('opacity',0.5);
                if(type=="paypallarge_small")
                    $('#imageplarge_small').css('opacity',0.5);
                if(type=="paypalmicro_small")
                    $('#imagepmicro_small').css('opacity',0.5);


                if(type=="vkontakte")
                    $('#imagev').css('opacity',0.5);
                if(type=="vkontaktesmall")
                    $('#imagevsmall').css('opacity',0.5);
                if(type=="vkontaktelarge_small")
                    $('#imagevlarge_small').css('opacity',0.5);
                if(type=="vkontaktemicro_small")
                    $('#imagevmicro_small').css('opacity',0.5);


                $.post(ajax_link_fbloginblock, {
                        action_custom:'returnimage',
                        type : type,
                        token: token_fbloginblock,
                        ajax : true,
                        controller : 'AdminFbloginblockajax',
                        action : 'FbloginblockAjax',
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
        			
        			
        			
        			if(type=="dropbox"){
                		$('#imagedb').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedb').src = "";
            			document.getElementById('imagedb').src = "../modules/fbloginblock/views/img/dropbox.png?re=" + count;
            			$('#imagedb-click').remove();
        			}
        			
        			if(type=="dropboxsmall"){
                		$('#imagedbsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedbsmall').src = "";
            			document.getElementById('imagedbsmall').src = "../modules/fbloginblock/views/img/dropbox-small.png?re=" + count;
            			$('#imagedb-clicksmall').remove();
        			}
        			if(type=="dropboxlarge_small"){
                		$('#imagedblarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedblarge_small').src = "";
            			document.getElementById('imagedblarge_small').src = "../modules/fbloginblock/views/img/dropbox-large-small.png?re=" + count;
            			$('#imagedb-clicklarge_small').remove();
        			}
        			if(type=="dropboxmicro_small"){
                		$('#imagedbmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedbmicro_small').src = "";
            			document.getElementById('imagedbmicro_small').src = "../modules/fbloginblock/views/img/dropbox-small-micro.png?re=" + count;
            			$('#imagedb-clickmicro_small').remove();
        			}
        			if(type=="dropbox"){
                		$('#imagedb').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagedb').src = "";
            			document.getElementById('imagedb').src = "../modules/fbloginblock/views/img/dropbox.png?re=" + count;
            			$('#imagedb-click').remove();
        			}
        			
        			
        			
        			if(type=="scoopsmall"){
                		$('#imagessmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagessmall').src = "";
            			document.getElementById('imagessmall').src = "../modules/fbloginblock/views/img/scoop-small.png?re=" + count;
            			$('#images-clicksmall').remove();
        			}
        			if(type=="scooplarge_small"){
                		$('#imageslarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageslarge_small').src = "";
            			document.getElementById('imageslarge_small').src = "../modules/fbloginblock/views/img/scoop-large-small.png?re=" + count;
            			$('#images-clicklarge_small').remove();
        			}
        			if(type=="scoopmicro_small"){
                		$('#imagesmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagesmicro_small').src = "";
            			document.getElementById('imagesmicro_small').src = "../modules/fbloginblock/views/img/scoop-small-micro.png?re=" + count;
            			$('#images-clickmicro_small').remove();
        			}
        			if(type=="scoop"){
                		$('#images').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('images').src = "";
            			document.getElementById('images').src = "../modules/fbloginblock/views/img/scoop.png?re=" + count;
            			$('#images-click').remove();
        			}
        			
        			
        			
        			if(type=="wordpresssmall"){
                		$('#imagewsmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagewsmall').src = "";
            			document.getElementById('imagewsmall').src = "../modules/fbloginblock/views/img/wordpress-small.png?re=" + count;
            			$('#imagew-clicksmall').remove();
        			}
        			if(type=="wordpresslarge_small"){
                		$('#imagewlarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagewlarge_small').src = "";
            			document.getElementById('imagewlarge_small').src = "../modules/fbloginblock/views/img/wordpress-large-small.png?re=" + count;
            			$('#imagew-clicklarge_small').remove();
        			}
        			if(type=="wordpressmicro_small"){
                		$('#imagewmicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagewmicro_small').src = "";
            			document.getElementById('imagewmicro_small').src = "../modules/fbloginblock/views/img/wordpress-small-micro.png?re=" + count;
            			$('#imagew-clickmicro_small').remove();
        			}
        			if(type=="wordpress"){
                		$('#imagew').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagew').src = "";
            			document.getElementById('imagew').src = "../modules/fbloginblock/views/img/wordpress.png?re=" + count;
            			$('#imagew-click').remove();
        			}
        			
        			
        			
        			if(type=="tumblrsmall"){
                		$('#imagetusmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagetusmall').src = "";
            			document.getElementById('imagetusmall').src = "../modules/fbloginblock/views/img/tumblr-small.png?re=" + count;
            			$('#imagetu-clicksmall').remove();
        			}
        			if(type=="tumblrlarge_small"){
                		$('#imagetularge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagetularge_small').src = "";
            			document.getElementById('imagetularge_small').src = "../modules/fbloginblock/views/img/tumblr-large-small.png?re=" + count;
            			$('#imagetu-clicklarge_small').remove();
        			}
        			if(type=="tumblrmicro_small"){
                		$('#imagetumicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagetumicro_small').src = "";
            			document.getElementById('imagetumicro_small').src = "../modules/fbloginblock/views/img/tumblr-small-micro.png?re=" + count;
            			$('#imagetu-clickmicro_small').remove();
        			}
        			if(type=="tumblr"){
                		$('#imagetu').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagetu').src = "";
            			document.getElementById('imagetu').src = "../modules/fbloginblock/views/img/tumblr.png?re=" + count;
            			$('#imagetu-click').remove();
        			}
        			
        			
        			
        			if(type=="pinterestsmall"){
                		$('#imagepismall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagepismall').src = "";
            			document.getElementById('imagepismall').src = "../modules/fbloginblock/views/img/pinterest-small.png?re=" + count;
            			$('#imagepi-clicksmall').remove();
        			}
        			if(type=="pinterestlarge_small"){
                		$('#imagepilarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagepilarge_small').src = "";
            			document.getElementById('imagepilarge_small').src = "../modules/fbloginblock/views/img/pinterest-large-small.png?re=" + count;
            			$('#imagepi-clicklarge_small').remove();
        			}
        			if(type=="pinterestmicro_small"){
                		$('#imagepimicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagepimicro_small').src = "";
            			document.getElementById('imagepimicro_small').src = "../modules/fbloginblock/views/img/pinterest-small-micro.png?re=" + count;
            			$('#imagepi-clickmicro_small').remove();
        			}
        			if(type=="pinterest"){
                		$('#imagepi').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagepi').src = "";
            			document.getElementById('imagepi').src = "../modules/fbloginblock/views/img/pinterest.png?re=" + count;
            			$('#imagepi-click').remove();
        			}
        			
        			
        			
        			if(type=="oklasssmall"){
                		$('#imageosmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageosmall').src = "";
            			document.getElementById('imageosmall').src = "../modules/fbloginblock/views/img/oklass-small.png?re=" + count;
            			$('#imageo-clicksmall').remove();
        			}
        			if(type=="oklasslarge_small"){
                		$('#imageolarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageolarge_small').src = "";
            			document.getElementById('imageolarge_small').src = "../modules/fbloginblock/views/img/oklass-large-small.png?re=" + count;
            			$('#imageo-clicklarge_small').remove();
        			}
        			if(type=="oklassmicro_small"){
                		$('#imageomicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageomicro_small').src = "";
            			document.getElementById('imageomicro_small').src = "../modules/fbloginblock/views/img/oklass-small-micro.png?re=" + count;
            			$('#imageo-clickmicro_small').remove();
        			}
        			if(type=="oklass"){
                		$('#imageo').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageo').src = "";
            			document.getElementById('imageo').src = "../modules/fbloginblock/views/img/oklass.png?re=" + count;
            			$('#imageo-click').remove();
        			}
        			
        			
        			
        			if(type=="mailrusmall"){
                		$('#imagemasmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagemasmall').src = "";
            			document.getElementById('imagemasmall').src = "../modules/fbloginblock/views/img/mailru-small.png?re=" + count;
            			$('#imagema-clicksmall').remove();
        			}
        			if(type=="mailrularge_small"){
                		$('#imagemalarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagemalarge_small').src = "";
            			document.getElementById('imagemalarge_small').src = "../modules/fbloginblock/views/img/mailru-large-small.png?re=" + count;
            			$('#imagema-clicklarge_small').remove();
        			}
        			if(type=="mailrumicro_small"){
                		$('#imagemamicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagemamicro_small').src = "";
            			document.getElementById('imagemamicro_small').src = "../modules/fbloginblock/views/img/mailru-small-micro.png?re=" + count;
            			$('#imagema-clickmicro_small').remove();
        			}
        			if(type=="mailru"){
                		$('#imagema').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imagema').src = "";
            			document.getElementById('imagema').src = "../modules/fbloginblock/views/img/mailru.png?re=" + count;
            			$('#imagema-click').remove();
        			}
        			
        			
        			
        			if(type=="yandexsmall"){
                		$('#imageyasmall').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageyasmall').src = "";
            			document.getElementById('imageyasmall').src = "../modules/fbloginblock/views/img/yandex-small.png?re=" + count;
            			$('#imageya-clicksmall').remove();
        			}
        			if(type=="yandexlarge_small"){
                		$('#imageyalarge_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageyalarge_small').src = "";
            			document.getElementById('imageyalarge_small').src = "../modules/fbloginblock/views/img/yandex-large-small.png?re=" + count;
            			$('#imageya-clicklarge_small').remove();
        			}
        			if(type=="yandexmicro_small"){
                		$('#imageyamicro_small').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageyamicro_small').src = "";
            			document.getElementById('imageyamicro_small').src = "../modules/fbloginblock/views/img/yandex-small-micro.png?re=" + count;
            			$('#imageya-clickmicro_small').remove();
        			}
        			if(type=="yandex"){
                		$('#imageya').css('opacity',1);
                		var count = Math.random();
            			document.getElementById('imageya').src = "";
            			document.getElementById('imageya').src = "../modules/fbloginblock/views/img/yandex.png?re=" + count;
            			$('#imageya-click').remove();
        			}


                    if(type=="paypal"){
                        $('#imagep').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imagep').src = "";
                        document.getElementById('imagep').src = "../modules/fbloginblock/views/img/paypal.png?re=" + count;
                        $('#imagep-click').remove();
                    }

                    if(type=="paypalsmall"){
                        $('#imagepsmall').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imagepsmall').src = "";
                        document.getElementById('imagepsmall').src = "../modules/fbloginblock/views/img/paypal-small.png?re=" + count;
                        $('#imagep-clicksmall').remove();
                    }
                    if(type=="paypallarge_small"){
                        $('#imageplarge_small').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imageplarge_small').src = "";
                        document.getElementById('imageplarge_small').src = "../modules/fbloginblock/views/img/paypal-large-small.png?re=" + count;
                        $('#imagep-clicklarge_small').remove();
                    }
                    if(type=="paypalmicro_small"){
                        $('#imagepmicro_small').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imagepmicro_small').src = "";
                        document.getElementById('imagepmicro_small').src = "../modules/fbloginblock/views/img/paypal-small-micro.png?re=" + count;
                        $('#imagep-clickmicro_small').remove();
                    }

                    if(type=="vkontakte"){
                        $('#imagev').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imagev').src = "";
                        document.getElementById('imagev').src = "../modules/fbloginblock/views/img/vkontakte.png?re=" + count;
                        $('#imagev-click').remove();
                    }
                    if(type=="vkontaktesmall"){
                        $('#imagevsmall').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imagevsmall').src = "";
                        document.getElementById('imagevsmall').src = "../modules/fbloginblock/views/img/vkontakte-small.png?re=" + count;
                        $('#imagev-clicksmall').remove();
                    }
                    if(type=="vkontaktelarge_small"){
                        $('#imagevlarge_small').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imagevlarge_small').src = "";
                        document.getElementById('imagevlarge_small').src = "../modules/fbloginblock/views/img/vkontakte-large-small.png?re=" + count;
                        $('#imagev-clicklarge_small').remove();
                    }
                    if(type=="vkontaktemicro_small"){
                        $('#imagevmicro_small').css('opacity',1);
                        var count = Math.random();
                        document.getElementById('imagevmicro_small').src = "";
                        document.getElementById('imagevmicro_small').src = "../modules/fbloginblock/views/img/vkontakte-small-micro.png?re=" + count;
                        $('#imagev-clickmicro_small').remove();
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
        			
        			if(type=="dropbox")
                		$('#imagedb').css('opacity',1);	
        			if(type=="dropboxsmall")
                		$('#imagedbsmall').css('opacity',1);
        			if(type=="dropboxlarge_small")
                		$('#imagedblarge_small').css('opacity',1);
        			if(type=="dropboxmicro_small")
                		$('#imagedbmicro_small').css('opacity',1);
        			
        			
        			if(type=="scoop")
                		$('#images').css('opacity',1);	
        			if(type=="scoopsmall")
                		$('#imagessmall').css('opacity',1);
        			if(type=="scooplarge_small")
                		$('#imageslarge_small').css('opacity',1);
        			if(type=="scoopmicro_small")
                		$('#imagesmicro_small').css('opacity',1);
        			
        			
        			if(type=="wordpress")
                		$('#imagew').css('opacity',1);	
        			if(type=="wordpresssmall")
                		$('#imagewsmall').css('opacity',1);
        			if(type=="wordpresslarge_small")
                		$('#imagewlarge_small').css('opacity',1);
        			if(type=="wordpressmicro_small")
                		$('#imagewmicro_small').css('opacity',1);
        			
        			
        			if(type=="tumblr")
                		$('#imagetu').css('opacity',1);	
        			if(type=="tumblrsmall")
                		$('#imagetusmall').css('opacity',1);
        			if(type=="tumblrlarge_small")
                		$('#imagetularge_small').css('opacity',1);
        			if(type=="tumblrmicro_small")
                		$('#imagetumicro_small').css('opacity',1);
        			
        			
        			if(type=="pinterest")
                		$('#imagepi').css('opacity',1);	
        			if(type=="pinterestsmall")
                		$('#imagepismall').css('opacity',1);
        			if(type=="pinterestlarge_small")
                		$('#imagepilarge_small').css('opacity',1);
        			if(type=="pinterestmicro_small")
                		$('#imagepimicro_small').css('opacity',1);
        			
        			
        			if(type=="oklass")
                		$('#imageo').css('opacity',1);	
        			if(type=="oklasssmall")
                		$('#imageosmall').css('opacity',1);
        			if(type=="oklasslarge_small")
                		$('#imageolarge_small').css('opacity',1);
        			if(type=="oklassmicro_small")
                		$('#imageomicro_small').css('opacity',1);
        			
        			
        			if(type=="mailru")
                		$('#imagema').css('opacity',1);	
        			if(type=="mailrusmall")
                		$('#imagemasmall').css('opacity',1);
        			if(type=="mailrularge_small")
                		$('#imagemalarge_small').css('opacity',1);
        			if(type=="mailrumicro_small")
                		$('#imagemamicro_small').css('opacity',1);
        			
        			
        			if(type=="yandex")
                		$('#imageya').css('opacity',1);	
        			if(type=="yandexsmall")
                		$('#imageyasmall').css('opacity',1);
        			if(type=="yandexlarge_small")
                		$('#imageyalarge_small').css('opacity',1);
        			if(type=="yandexmicro_small")
                		$('#imageyamicro_small').css('opacity',1);

                    if(type=="paypal")
                        $('#imagep').css('opacity',1);
                    if(type=="paypalsmall")
                        $('#imagepsmall').css('opacity',1);
                    if(type=="paypallarge_small")
                        $('#imageplarge_small').css('opacity',1);
                    if(type=="paypalmicro_small")
                        $('#imagepmicro_small').css('opacity',1);


                    if(type=="vkontakte")
                        $('#imagev').css('opacity',1);
                    if(type=="vkontaktesmall")
                        $('#imagevsmall').css('opacity',1);
                    if(type=="vkontaktelarge_small")
                        $('#imagevlarge_small').css('opacity',1);
                    if(type=="vkontaktemicro_small")
                        $('#imagevmicro_small').css('opacity',1);
        			
        			alert(data.message);
        		}
        		
        	}, 'json');
        	}

        }


