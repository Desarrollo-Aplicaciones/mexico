{*
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
*}

{if !$fbloginblockislogged}

{literal}
<style type="text/css">
.padding-left-logins{padding-left:5px!important;margin:0px!important}
.header_user_info_ps16{float:right;padding:9px;border-left: 1px solid #515151;}

.auth-page-txt-before-logins{font-weight:bold;color:#555454}
.padding-top-10{padding-top:10px}
.auth-page-txt-info-block{text-align:center;margin-top:20px;font-weight:bold;color:#555454}


.wrap a{text-decoration:none;opacity:1}
.wrap a:hover{text-decoration:none;opacity:0.5}
.width_fbloginblock{margin-top:12px}

.wrap1 a{text-decoration:none;opacity:1}
.wrap1 a:hover{text-decoration:none;opacity:0.5}
.width_fbloginblock1{width:40px}

.fbtwgblock-columns15{margin-top:10px;margin-left:10px}
.fbtwgblock-columns{margin-top:10px}

.fbtwgblock-columns15 a{float:left;margin-top:10px;margin-right:5px;opacity:1}
.fbtwgblock-columns15 a:hover{float:left;margin-top:10px;margin-right:5px;opacity:0.5}
.fbtwgblock-columns15 a.fbloginblock-last{margin-right:0px!important;}

.fbtwgblock-columns a{float:left;margin-top:10px;margin-right:5px;opacity:1}
.fbtwgblock-columns a:hover{float:left;margin-top:10px;margin-right:5px;opacity:0.5}
.fbtwgblock-columns a.fbloginblock-last{margin-right:0px!important;}

a.fbloginblock-log-in:hover{opacity:0.5}
a.fbloginblock-log-in{opacity:1;} 
{/literal}
{if $fbloginblock_topf == "topf" || $fbloginblock_topg == "topg" 
|| $fbloginblock_topt == "topt" || $fbloginblock_topl == "topl"
|| $fbloginblock_topm == "topm" || $fbloginblock_topy == "topy" || $fbloginblock_topi == "topi"
|| $fbloginblock_topfs == "topfs" || $fbloginblock_topgi == "topgi" || $fbloginblock_topd == "topd"
|| $fbloginblock_topa == "topa"}
{literal}
#follow-teaser  {
	background-color:#F3F3F3;
	border-bottom:none;
}
#follow-teaser .wrap {
    margin: auto;
    position: relative;
    width: auto;
	text-align:center;
	padding-bottom:10px;
}

{/literal}
{/if}

{if $fbloginblock_footerf == "footerf" || $fbloginblock_footerg == "footerg" 
    || $fbloginblock_footert == "footert" || $fbloginblock_footerl == "footerl"
	|| $fbloginblock_footerm == "footerm" || $fbloginblock_footery == "footery" || $fbloginblock_footeri == "footeri"
	|| $fbloginblock_footerfs == "footerfs" || $fbloginblock_footergi == "footergi" || $fbloginblock_footerd == "footerd"
	|| $fbloginblock_footera == "footera"}
{literal}
#follow-teaser-footer  {
	background-color:#F3F3F3;
	border-bottom:none;
	font-weight:bold;
	padding:10px 0;
	width:100%;
	margin-top:0px;
}
#follow-teaser-footer .wrap {
    margin: auto;
    position: relative;
   text-align:center
}

{/literal}
{/if}
{literal}
</style>
{/literal}
{/if}



{if !$fbloginblockislogged}

{literal}
<script type="text/javascript">
{/literal}
{if $fbloginblockis16 != 1}	
{literal}
<!--
//<![CDATA[
{/literal}
{/if}
{if $fbloginblock_footerf == "footerf" || $fbloginblock_footerg == "footerg" 
     || $fbloginblock_footert == "footert" || $fbloginblock_footerl == "footerl"
    || $fbloginblock_footerm == "footerm" || $fbloginblock_footery == "footery" || $fbloginblock_footeri == "footeri"
    || $fbloginblock_footerfs == "footerfs" || $fbloginblock_footergi == "footergi" || $fbloginblock_footerd == "footerd"
    || $fbloginblock_footera == "footera"}
{literal}


$(document).ready(function() {
	var bottom_teaser = '<div id="follow-teaser-footer">'+
	'<div class="wrap">'+
	
	{/literal}
		{if $fbloginblock_footertxt == "footertxt"}
	{literal}
	 '<div class="auth-page-txt-before-logins padding-top-10">{/literal}{$fbloginblockauthp|escape:'quotes':'UTF-8'}{literal}</div>'+
	 {/literal}
		{/if}
	{literal}
	
	{/literal}
	{if $fbloginblock_footerf == "footerf" && $fbloginblockf_on == 1}{literal}
	'<a href="javascript:void(0)" onclick="return fblogin();" title="Facebook">'+
		'<img src="{/literal}{$fbloginblockffooterimg|escape:'htmlall':'UTF-8'}{literal}" class="width_fbloginblock" alt="Facebook"  />'+
	'</a>&nbsp;'+
	{/literal}
	{/if}
	{if $fbloginblock_footert == "footert" && $fbloginblockt_on == 1}{literal}
		'<a href="javascript:void(0)"'+
		{/literal}{if $fbloginblocktconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/twitter.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'login\', \'location,width=600,height=600,top=0\'); popupWin.focus();"'+
		 {/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$terror|escape:'htmlall':'UTF-8'}{literal}\')"'+
		 {/literal}{/if}{literal}
			'title="Twitter">'+
			'<img src="{/literal}{$fbloginblocktfooterimg|escape:'htmlall':'UTF-8'}{literal}" style="margin-top:12px" alt="Twitter" />'+
		'</a>&nbsp;'+
	{/literal}
	{/if}
	
	{if $fbloginblock_footera == "footera" && $fbloginblocka_on == 1}{literal}
	'<a href="javascript:void(0)" onclick="return amazonlogin();" title="Amazon">'+
		'<img src="{/literal}{$fbloginblockafooterimg|escape:'htmlall':'UTF-8'}{literal}" class="width_fbloginblock" alt="Amazon"  />'+
	'</a>&nbsp;'+
	{/literal}
	{/if}
	
	{if $fbloginblock_footerg == "footerg" && $fbloginblockg_on == 1}{literal}
	'<a href="javascript:void(0)" title="Google"'+
	{/literal}{if $fbloginblockgconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/login.php?p=google{/literal}{if $fbloginblockorder_page == 1}{literal}&http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();"'+
	  {/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$gerror|escape:'htmlall':'UTF-8'}{literal}\')"'+
	{/literal}{/if}{literal}
	   '>'+
			'<img src="{/literal}{$fbloginblockgfooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="Google" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	{if $fbloginblock_footery == "footery" && $fbloginblocky_on == 1}{literal}
	'<a href="javascript:void(0)" title="Yahoo"'+
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/login.php?p=yahoo{/literal}{if $fbloginblockorder_page == 1}{literal}&http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=400,height=300,top=0\');popupWin.focus();"'+
		'>'+
		'<img src="{/literal}{$fbloginblockyfooterimg|escape:'htmlall':'UTF-8'}{literal}" style="margin-top:12px" alt="Yahoo"  />'+
	'</a>&nbsp;'+
	{/literal}{/if}
	{if $fbloginblock_footerp == "footerp" && $fbloginblockp_on == 1}{literal}
	'<a href="javascript:void(0)" title="Paypal"'+
	{/literal}{if $fbloginblockpconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/paypalconnect.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
    {/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$perror|escape:'htmlall':'UTF-8'}{literal}\')">'+
	 {/literal}{/if}{literal}
			'<img src="{/literal}{$fbloginblockpfooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="Paypal" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	{if $fbloginblock_footerl == "footerl" && $fbloginblockl_on == 1}{literal}
	'<a href="javascript:void(0)" title="LinkedIn"'+
	{/literal}{if $fbloginblocklconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/linkedin.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
	{/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$lerror|escape:'htmlall':'UTF-8'}{literal}\')">'+
	{/literal}{/if}{literal}   
			'<img src="{/literal}{$fbloginblocklfooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="LinkedIn" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	{if $fbloginblock_footerm == "footerm" && $fbloginblockm_on == 1}{literal}
	'<a href="javascript:void(0)" title="Microsoft Live"'+
	{/literal}{if $fbloginblockmconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/microsoft.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
	{/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$merror|escape:'htmlall':'UTF-8'}{literal}\')">'+
	{/literal}{/if}{literal}   
		
			'<img src="{/literal}{$fbloginblockmfooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="Microsoft Live" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	
	{if $fbloginblock_footeri == "footeri" && $fbloginblocki_on == 1}{literal}
	'<a href="javascript:void(0)" title="Instagram"'+
	{/literal}{if $fbloginblockiconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/instagram.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
	{/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$ierror|escape:'htmlall':'UTF-8'}{literal}\')">'+
	{/literal}{/if}{literal}   
		
			'<img src="{/literal}{$fbloginblockifooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="Instagram" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	
	
	{if $fbloginblock_footerfs == "footerfs" && $fbloginblockfs_on == 1}{literal}
	'<a href="javascript:void(0)" title="Foursquare"'+
	{/literal}{if $fbloginblockfsconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/foursquare.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
	{/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$fserror|escape:'htmlall':'UTF-8'}{literal}\')">'+
	{/literal}{/if}{literal}   
		
			'<img src="{/literal}{$fbloginblockfsfooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="Foursquare" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	
	{if $fbloginblock_footergi == "footergi" && $fbloginblockgi_on == 1}{literal}
	'<a href="javascript:void(0)" title="Github"'+
	{/literal}{if $fbloginblockgiconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/github.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
	{/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$gierror|escape:'htmlall':'UTF-8'}{literal}\')">'+
	{/literal}{/if}{literal}   
		
			'<img src="{/literal}{$fbloginblockgifooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="Github" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	
	
	{if $fbloginblock_footerd == "footerd" && $fbloginblockd_on == 1}{literal}
	'<a href="javascript:void(0)" title="Disqus"'+
	{/literal}{if $fbloginblockdconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/disqus.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
	{/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$derror|escape:'htmlall':'UTF-8'}{literal}\')">'+
	{/literal}{/if}{literal}   
		
			'<img src="{/literal}{$fbloginblockdfooterimg|escape:'htmlall':'UTF-8'}{literal}"  style="margin-top:12px" alt="Disqus" />'+
		'</a>&nbsp;'+
	{/literal}{/if}
	
	{literal}
	
	'</div>'+ 
'</div>';

$('body').append(bottom_teaser);

    });
{/literal}   
    	
{/if}


{if $fbloginblock_topf == "topf" || $fbloginblock_topg == "topg" 
	|| $fbloginblock_topt == "topt" || $fbloginblock_topl == "topl"
	|| $fbloginblock_topm == "topm" || $fbloginblock_topy == "topy" || $fbloginblock_topi == "topi"
	|| $fbloginblock_topfs == "topfs" || $fbloginblock_topgi == "topgi" || $fbloginblock_topd == "topd"
	|| $fbloginblock_topa == "topa"}
{literal}



    	
{/literal}
{/if}

{if $blockfacebookappid != '' && $blockfacebooksecret != ''}
{literal}

$(document).ready(function(){

	//add div fb-root
	if ($('div#fb-root').length == 0)
	{
	    FBRootDom = $('<div>', {'id':'fb-root'});
	    $('body').prepend(FBRootDom);
	}

	(function(d){
        var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
        js = d.createElement('script'); js.id = id; js.async = true;
        js.src = "//connect.facebook.net/{/literal}{$fbloginblocklang|escape:'htmlall':'UTF-8'}{literal}/all.js";
        d.getElementsByTagName('head')[0].appendChild(js);
      }(document));
});	

	function login(){
		$.post(baseDir+'modules/fbloginblock/ajax.php', 
					{action:'login',
					 secret:'{/literal}{$blockfacebooksecret|escape:'htmlall':'UTF-8'}{literal}',
					 appid:'{/literal}{$blockfacebookappid|escape:'htmlall':'UTF-8'}{literal}'
					 }, 
		function (data) {
			if (data.status == 'success') {
						
				{/literal}{if $fbloginblockorder_page == 1}{literal}
					var url = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$fbloginblockuri|escape:'htmlall':'UTF-8'}{literal}";
					window.location.href= url;
				{/literal}{else}{literal}		
					var url = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{$fbloginblockuri|escape:'htmlall':'UTF-8'}{literal}";
					window.location.href= url;
				{/literal}{/if}{literal}		
				
				
						
			} else {
				alert(data.message);
			}
		}, 'json');
	}
	/*function logout(){
				var url = "{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}index.php?mylogout{literal}";
				$('#fb-log-out').html('');
				$('#fb-log-out').html('Log in');
				$('#fb-fname-lname').remove();
				window.location.href= url;
	}*/
	function greet(){
	   FB.api('/me', function(response) {
		   
		var src = 'https://graph.facebook.com/'+response.id+'/picture';
		$('#header_user_info span').append('<img style="margin-left:5px" height="20" src="'+src+'"/>');
			
		{/literal}{if !$fbloginblockislogged}{literal}
			login();
		{/literal}{/if}{literal}
		 });
	}


	   function fblogin(){
		   
			FB.init({appId: '{/literal}{$blockfacebookappid|escape:'htmlall':'UTF-8'}{literal}', 
					status: true, 
					cookie: true, 
					xfbml: true,
		         	oauth: true});
         	
				FB.login(function(response) {
		            if (response.status == 'connected') {
			            login();
		            } else {
		                // user is not logged in
		                logout();
		            }
		        }, {scope:'email'});
		       
		        return false;
			}
	   {/literal}
{else}
{literal}
function fblogin(){
	  alert("{/literal}{$ferror|escape:'htmlall':'UTF-8'}{literal}");
	return;	
}
{/literal}
{/if}
		   

		 
		 
		 
{if $fbloginblockamazonci != '' && $fbloginblockis_ssl == 1}		   
{literal}

// amazon connect

$(document).ready(function(){

	//add div amazon-root
	if ($('div#amazon-root').length == 0)
	{
	    FBRootDomAmazon = $('<div>', {'id':'amazon-root'});
	    $('body').prepend(FBRootDomAmazon);
	}

	window.onAmazonLoginReady = function() {
   	 amazon.Login.setClientId('{/literal}{$fbloginblockamazonci|escape:'htmlall':'UTF-8'}{literal}');
  	};
  (function(d) {
    var a = d.createElement('script'); a.type = 'text/javascript';
    a.async = true; a.id = 'amazon-login-sdk';
    a.src = 'https://api-cdn.amazon.com/sdk/login1.js';
    d.getElementById('amazon-root').appendChild(a);
  })(document);
  
   
});

 function amazonlogin(){
 	    options = { scope : 'profile' };
	    amazon.Login.authorize(options, '{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/amazon.php');
	    return false;
 }


// amazon connect	

{/literal}
{else}
{literal}

function amazonlogin(){
		  
		  {/literal}{if $fbloginblockis_ssl == 0}{literal}
		  alert("{/literal}{$fbloginblockssltxt|escape:'htmlall':'UTF-8'}{literal}");
		  {/literal}{else}{literal}
		  	alert("{/literal}{$aerror|escape:'htmlall':'UTF-8'}{literal}");
		  {/literal}{/if}{literal}	
		 	return;
		 	
 }
 
{/literal}
{/if}


{literal}
$(document).ready(function() {

{/literal}
	{if $fbloginblock_authpagef == "authpagef" || $fbloginblock_authpaget == "authpaget"
		|| $fbloginblock_authpageg == "authpageg" || $fbloginblock_authpagey == "authpagey" 
		|| $fbloginblock_authpagel == "authpagel" || $fbloginblock_authpagem == "authpagem"
		|| $fbloginblock_authpagei == "authpagei" || $fbloginblock_authpagefs == "authpagefs"
		|| $fbloginblock_authpagegi == "authpagegi" || $fbloginblock_authpaged == "authpaged"
		|| $fbloginblock_authpagea == "authpagea"}
{literal}

	 var ph = '<div class="wrap" style="display:none">'+
	 
{/literal}
		{if $fbloginblock_authpagef == "authpagef" && $fbloginblockf_on == 1}
	{literal}
	 '<a href="javascript:void(0)" onclick="return fblogin();" title="Facebook">'+
	   'Iniciar sesión con Facebook'+
	 '<\/a>&nbsp;'+
	 {/literal}
	 	{/if}
	 {if $fbloginblock_authpaget == "authpaget" && $fbloginblockt_on == 1}{literal}
		'<a href="javascript:void(0)"'+ 
		{/literal}{if $fbloginblocktconf == 1}{literal} 
			   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/twitter.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'login\', \'location,width=600,height=600,top=0\'); popupWin.focus();"'+
		 {/literal}{else}{literal}
					  'onclick="alert(\'{/literal}{$terror|escape:'htmlall':'UTF-8'}{literal}\')"'+
		 {/literal}{/if}{literal}
		 'title="Twitter">'+
			'<img src="{/literal}{$fbloginblocktauthpageimg|escape:'htmlall':'UTF-8'}{literal}" style="margin-top:12px" alt="Twitter" />'+
		'</a>&nbsp;'+
	 {/literal}
	 {/if}
	 
	 
	 {if $fbloginblock_authpagea == "authpagea" && $fbloginblocka_on == 1}
	{literal}
	 '<a href="javascript:void(0)" onclick="return amazonlogin();" title="Amazon">'+
	   '<img src="{/literal}{$fbloginblockaauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="Amazon" style="margin-top:12px"  />'+
	 '<\/a>&nbsp;'+
	 {/literal}
	 	{/if}
		 
	 {if $fbloginblock_authpageg == "authpageg" && $fbloginblockg_on == 1}
	 {literal}
	 '<a href="javascript:void(0)" title="Google"'+
	 {/literal}{if $fbloginblockgconf == 1}{literal} 
	   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/login.php?p=google{/literal}{if $fbloginblockorder_page == 1}{literal}&http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();"'+
	 {/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$gerror|escape:'htmlall':'UTF-8'}{literal}\')"'+
	{/literal}{/if}{literal} 
	   '>'+
			'<img src="{/literal}{$fbloginblockgauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="Google" style="margin-top:12px" />'+
		'</a>&nbsp;'+
	 {/literal}
	 {/if}
	 {if $fbloginblock_authpagey == "authpagey" && $fbloginblocky_on == 1}{literal}
		'<a href="javascript:void(0)" title="Yahoo"'+
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/login.php?p=yahoo{/literal}{if $fbloginblockorder_page == 1}{literal}&http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=400,height=300,top=0\');popupWin.focus();"'+
			'>'+
			'<img src="{/literal}{$fbloginblockyauthpageimg|escape:'htmlall':'UTF-8'}{literal}" style="margin-top:12px" alt="Yahoo"  />'+
		'</a>&nbsp;'+
	 {/literal}{/if}
	
	 {if $fbloginblock_authpagel == "authpagel" && $fbloginblockl_on == 1}
	 {literal}
		 '<a href="javascript:void(0)" title="LinkedIn"'+
		 {/literal}{if $fbloginblocklconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/linkedin.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
		 {/literal}{else}{literal}
				  'onclick="alert(\'{/literal}{$lerror|escape:'htmlall':'UTF-8'}{literal}\')">'+
		{/literal}{/if}{literal}  
				'<img src="{/literal}{$fbloginblocklauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="LinkedIn" style="margin-top:12px" />'+
		 '</a>&nbsp;'+
	 {/literal}
	 {/if}
	 {if $fbloginblock_authpagem == "authpagem" && $fbloginblockm_on == 1}
		 {literal}
			 '<a href="javascript:void(0)" title="Microsoft Live"'+
			 
			    {/literal}{if $fbloginblockmconf == 1}{literal} 
				   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/microsoft.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
				{/literal}{else}{literal}
						  'onclick="alert(\'{/literal}{$merror|escape:'htmlall':'UTF-8'}{literal}\')">'+
				{/literal}{/if}{literal}

				'<img src="{/literal}{$fbloginblockmauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="Microsoft Live" style="margin-top:12px" />'+
			 '</a>&nbsp;'+
		 {/literal}
	 {/if}
	 
	 {if $fbloginblock_authpagei == "authpagei" && $fbloginblocki_on == 1}
		 {literal}
			 '<a href="javascript:void(0)" title="Instagram"'+
			 
			    {/literal}{if $fbloginblockiconf == 1}{literal} 
				   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/instagram.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
				{/literal}{else}{literal}
						  'onclick="alert(\'{/literal}{$ierror|escape:'htmlall':'UTF-8'}{literal}\')">'+
				{/literal}{/if}{literal}

				'<img src="{/literal}{$fbloginblockiauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="Instagram" style="margin-top:12px" />'+
			 '</a>&nbsp;'+
		 {/literal}
	 {/if}
	 
	 
	  {if $fbloginblock_authpagefs == "authpagefs" && $fbloginblockfs_on == 1}
		 {literal}
			 '<a href="javascript:void(0)" title="Foursquare"'+
			 
			    {/literal}{if $fbloginblockfsconf == 1}{literal} 
				   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/foursquare.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
				{/literal}{else}{literal}
						  'onclick="alert(\'{/literal}{$fserror|escape:'htmlall':'UTF-8'}{literal}\')">'+
				{/literal}{/if}{literal}

				'<img src="{/literal}{$fbloginblockfsauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="Foursquare" style="margin-top:12px" />'+
			 '</a>&nbsp;'+
		 {/literal}
	 {/if}
	 
	  {if $fbloginblock_authpagegi == "authpagegi" && $fbloginblockgi_on == 1}
		 {literal}
			 '<a href="javascript:void(0)" title="Github"'+
			 
			    {/literal}{if $fbloginblockgiconf == 1}{literal} 
				   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/github.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
				{/literal}{else}{literal}
						  'onclick="alert(\'{/literal}{$gierror|escape:'htmlall':'UTF-8'}{literal}\')">'+
				{/literal}{/if}{literal}

				'<img src="{/literal}{$fbloginblockgiauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="Github" style="margin-top:12px" />'+
			 '</a>&nbsp;'+
		 {/literal}
	 {/if}
	 
	  {if $fbloginblock_authpaged == "authpaged" && $fbloginblockd_on == 1}
		 {literal}
			 '<a href="javascript:void(0)" title="Disqus"'+
			 
			    {/literal}{if $fbloginblockdconf == 1}{literal} 
				   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/disqus.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
				{/literal}{else}{literal}
						  'onclick="alert(\'{/literal}{$derror|escape:'htmlall':'UTF-8'}{literal}\')">'+
				{/literal}{/if}{literal}

				'<img src="{/literal}{$fbloginblockdauthpageimg|escape:'htmlall':'UTF-8'}{literal}" alt="Disqus" style="margin-top:12px" />'+
			 '</a>'+
		 {/literal}
	 {/if}
	
	 {literal}
	 
	 
	 
	 
	'<\/div>';
	
	{/literal}{if $fbloginblockis16 == 1}{literal}
    	$('#login_form').parent('div').after(ph);
    {/literal}{else}{literal}
    	$('#login_form').after(ph);
	{/literal}{/if}{literal}
    

{/literal}{/if}{literal}



{/literal}
	{if $fbloginblock_welcomef == "welcomef" || $fbloginblock_welcomet == "welcomet"
		|| $fbloginblock_welcomeg == "welcomeg" || $fbloginblock_welcomey == "welcomey" 
		|| $fbloginblock_welcomel == "welcomel" || $fbloginblock_welcomem == "welcomem"
		|| $fbloginblock_welcomei == "welcomei" || $fbloginblock_welcomefs == "welcomefs"
		|| $fbloginblock_welcomegi == "welcomegi" || $fbloginblock_welcomed == "welcomed"
		|| $fbloginblock_welcomea == "welcomea"}
{literal}

    var ph_top = '&nbsp;'+
    {/literal}
	 {if $fbloginblock_welcomet == "welcomet" && $fbloginblockt_on == 1}{literal}
		'<a href="javascript:void(0)" title="Twitter" '+ 
		{/literal}{if $fbloginblocktconf == 1}{literal} 
			   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/twitter.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'login\', \'location,width=600,height=600,top=0\'); popupWin.focus();"'+
		 {/literal}{else}{literal}
					  'onclick="alert(\'{/literal}{$terror|escape:'htmlall':'UTF-8'}{literal}\')"'+
		 {/literal}{/if}{literal}
		'title="Twitter" class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}">'+
	 			'<img src="{/literal}{$fbloginblocktwelcomeimg|escape:'htmlall':'UTF-8'}{literal}"  alt="Twitter"/>'+
		'</a>&nbsp;'+
	 {/literal}
	 {/if}
	 
	  {if $fbloginblock_welcomea == "welcomea" && $fbloginblocka_on == 1}
	{literal}
       '<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)"  onclick="return amazonlogin();" title="Amazon">'+
	   '<img src="{/literal}{$fbloginblockawelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="Amazon"  />'+
	 '<\/a>&nbsp;'+
	 {/literal}
	 	{/if}
	 

	 {if $fbloginblock_welcomey == "welcomey" && $fbloginblocky_on == 1}{literal}
		'<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)" title="Yahoo" '+
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/login.php?p=yahoo{/literal}{if $fbloginblockorder_page == 1}{literal}&http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=400,height=300,top=0\');popupWin.focus();"'+
			'>'+
			'<img src="{/literal}{$fbloginblockywelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="Yahoo"  />'+
		'</a>&nbsp;'+
	 {/literal}{/if}
		 
	
	{if $fbloginblock_welcomel == "welcomel" && $fbloginblockl_on == 1}
	 {literal}
	 '<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)" title="LinkedIn" '+
		 {/literal}{if $fbloginblocklconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/linkedin.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
		{/literal}{else}{literal}
			  'onclick="alert(\'{/literal}{$lerror|escape:'htmlall':'UTF-8'}{literal}\')">'+
		{/literal}{/if}{literal}  
				'<img src="{/literal}{$fbloginblocklwelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="LinkedIn" />'+
	 '</a>&nbsp;'+
	 {/literal}
		 {/if}

	{if $fbloginblock_welcomem == "welcomem" && $fbloginblockm_on == 1}
	 {literal}
	 '<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)" title="Microsoft Live" '+

	 	{/literal}{if $fbloginblockmconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/microsoft.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
		{/literal}{else}{literal}
				  'onclick="alert(\'{/literal}{$merror|escape:'htmlall':'UTF-8'}{literal}\')">'+
		{/literal}{/if}{literal}
		
	   	'<img src="{/literal}{$fbloginblockmwelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="Microsoft Live" />'+
	 '</a>&nbsp;'+
	 {/literal}
	 {/if}
	 
	 
	 {if $fbloginblock_welcomei == "welcomei" && $fbloginblocki_on == 1}
	 {literal}
	 '<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)" title="Instagram" '+

	 	{/literal}{if $fbloginblockiconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/instagram.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
		{/literal}{else}{literal}
				  'onclick="alert(\'{/literal}{$ierror|escape:'htmlall':'UTF-8'}{literal}\')">'+
		{/literal}{/if}{literal}
		
	   	'<img src="{/literal}{$fbloginblockiwelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="Instagram" />'+
	 '</a>&nbsp;'+
	 {/literal}
	 {/if}
	 
	 
	 {if $fbloginblock_welcomefs == "welcomefs" && $fbloginblockfs_on == 1}
	 {literal}
	 '<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)" title="Foursquare" '+

	 	{/literal}{if $fbloginblockfsconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/foursquare.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
		{/literal}{else}{literal}
				  'onclick="alert(\'{/literal}{$fserror|escape:'htmlall':'UTF-8'}{literal}\')">'+
		{/literal}{/if}{literal}
		
	   	'<img src="{/literal}{$fbloginblockfswelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="Foursquare" />'+
	 '</a>&nbsp;'+
	 {/literal}
	 {/if}
	 
	 
	  {if $fbloginblock_welcomegi == "welcomegi" && $fbloginblockgi_on == 1}
	 {literal}
	 '<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)" title="Github" '+

	 	{/literal}{if $fbloginblockgiconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/github.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
		{/literal}{else}{literal}
				  'onclick="alert(\'{/literal}{$gierror|escape:'htmlall':'UTF-8'}{literal}\')">'+
		{/literal}{/if}{literal}
		
	   	'<img src="{/literal}{$fbloginblockgiwelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="Github" />'+
	 '</a>&nbsp;'+
	 {/literal}
	 {/if}
	 
	 
	  {if $fbloginblock_welcomed == "welcomed" && $fbloginblockd_on == 1}
	 {literal}
	 '<a class="fbloginblock-log-in {/literal}{if $fbloginblockis_ps5 == 1}padding-left-logins{/if}{literal}" href="javascript:void(0)" title="Disqus" '+

	 	{/literal}{if $fbloginblockdconf == 1}{literal} 
		   'onclick="javascript:popupWin = window.open(\'{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/disqus.php{/literal}{if $fbloginblockorder_page == 1}{literal}?http_referer={/literal}{$fbloginblockhttp_referer|urlencode}{/if}{literal}\', \'openId\', \'location,width=512,height=512,top=0\');popupWin.focus();">'+
		{/literal}{else}{literal}
				  'onclick="alert(\'{/literal}{$derror|escape:'htmlall':'UTF-8'}{literal}\')">'+
		{/literal}{/if}{literal}
		
	   	'<img src="{/literal}{$fbloginblockdwelcomeimg|escape:'htmlall':'UTF-8'}{literal}" alt="Disqus" />'+
	 '</a>&nbsp;'+
	 {/literal}
	 {/if}
	 
	 {literal}
	 '';

    if($('#header_user_info a'))
    	$('#header_user_info a:last').after(ph_top);

    // for PS 1.6 >
    if($('.header_user_info'))
		$('.header_user_info:last').after('<div class="header_user_info_ps16">'+ph_top+'<\/div>');


{/literal}{/if}{literal}
    
	    
	
	
    });
{/literal}



		

	
{if $fbloginblockis16 != 1}
{literal}
	// ]]>
-->
{/literal}
{/if}
{literal}
</script>
{/literal}

{else}


{if $fbloginblocktwpopup == 1 || $fbloginblockinpopup == 1}

<!--  show popup for twitter or instagram customer which not changed email address  -->

{literal}
<style type="text/css">
div#fb-con-wrapper {
	width: 500px;
	padding: 20px 25px;
	position: fixed;
	bottom: 50%;
	left: 50%;
	margin-left: -250px;
	z-index: 9999;
	background-color: #EEE;
	color: #444;
	border-radius: 5px;
	font-size: 14px;
	font-weight: bold;
	display: none;
	box-shadow: 0 0 27px 0 #111;
	text-align: center;
	line-height: 1em;
}

div#fb-con {
	filter: alpha(opacity=70);
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=70)";	
	opacity: 0.7;
	background-color: #444;	
	width: 100%;
	height: 100%;	
	cursor: pointer;
	z-index: 9998;
	position: fixed;
	bottom: 0;
	top: 0;
	left:0;
	display: none;	
}

#button-close-twitter{
    background: url("{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/views/img/fancybox.png") repeat scroll -40px 0 transparent;
    cursor: pointer;
    height: 30px;
    position: absolute;
    right: -15px;
    top: -15px;
    width: 30px;
    z-index: 1103;
}
</style>
{/literal}


{literal}
<script type="text/javascript"><!--
{/literal}
{if $fbloginblockis16 != 1}	
{literal}

//<![CDATA[
{/literal}
{/if}


{literal}
$(document).ready(function() {

	{/literal}{if $fbloginblocktwpopup == 1}{literal}
	var data = '<h4><img src="{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/views/img/settings_t.png"/>&nbsp;{/literal}{$fbloginblocktw_one|escape:'htmlall':'UTF-8'}{literal}</h4>'+
			   '<br/>'+
			   '<p>{/literal}{$fbloginblocktw_two|escape:'htmlall':'UTF-8'}{literal} </p>'+
			   '<br/>'+
			   '<label for="twitter-email">{/literal}{l s='Your e-mail' mod='fbloginblock'}{literal}:</label>&nbsp;<input type="text" value="" id="twitter-email" name="twitter-email">'+
			   '<br/>'+
			   '<br/>'+
			   '<a class="button" style="margin:0 auto" onclick="update_twitter_email();return false;" value="{/literal}{l s='Send' mod='fbloginblock'}{literal}"><b>{/literal}{l s='Send' mod='fbloginblock'}{literal}</b></a>'+
			   '';
	{/literal}{else}{literal}
	var data = '<h4><img src="{/literal}{$base_dir_ssl|escape:'htmlall':'UTF-8'}{literal}modules/fbloginblock/views/img/settings_i.png"/>&nbsp;{/literal}{$fbloginblockin_one|escape:'htmlall':'UTF-8'}{literal}</h4>'+
			   '<br/>'+
			   '<p>{/literal}{$fbloginblockin_two|escape:'htmlall':'UTF-8'}{literal} </p>'+
			   '<br/>'+
			   '<label for="instagram-email">{/literal}{l s='Your e-mail' mod='fbloginblock'}{literal}:</label>&nbsp;<input type="text" value="" id="instagram-email" name="instagram-email">'+
			   '<br/>'+
			   '<br/>'+
			   '<a class="button" style="margin:0 auto" onclick="update_instagram_email();return false;" value="{/literal}{l s='Send' mod='fbloginblock'}{literal}"><b>{/literal}{l s='Send' mod='fbloginblock'}{literal}</b></a>'+
			   '';
			   
	{/literal}{/if}{literal}
	
    if ($('div#fb-con-wrapper').length == 0)				
	{					
		conwrapper = '<div id="fb-con-wrapper"><\/div>';		
		$('body').append(conwrapper);				
	}
	
	if ($('div#fb-con').length == 0)				
	{					
		condom = '<div id="fb-con"><\/div>';					
		$('body').append(condom);				
	}				

	$('div#fb-con').fadeIn(function(){	
				
		$(this).css('filter', 'alpha(opacity=70)');					
		$(this).bind('click dblclick', function(){						
		$('div#fb-con-wrapper').hide();						
		$(this).fadeOut();	
		});				
	});				

	
	$('div#fb-con-wrapper').html('<a id="button-close-twitter" style="display: inline;"><\/a>'+data).fadeIn();

	$("a#button-close-twitter").click(function() {
		$('div#fb-con-wrapper').hide();
		$('div#fb-con').fadeOut();	
	});
});

	function update_twitter_email(){
   	 $('#fb-con-wrapper').css('opacity',0.8);

	var twemail = $('#twitter-email').val();
    $.post(baseDir+'modules/fbloginblock/twupdate.php', 
    			{cid:'{/literal}{$fbloginblockcid|escape:'htmlall':'UTF-8'}{literal}',
				 email:twemail 
    			 }, 
    function (data) {
    	if (data.status == 'success') {

    		$('#fb-con-wrapper').html('');
    		$('#fb-con-wrapper').html('<br/><p>'+data.params.content+'</p><br/>');
    		$('#fb-con-wrapper').css('opacity',1);
    	} else {
    		$('#fb-con-wrapper').css('opacity',1);
    		alert(data.message);
    		
    	}
    }, 'json');
    
    }
    
    function update_instagram_email(){
   	 $('#fb-con-wrapper').css('opacity',0.8);

	var inemail = $('#instagram-email').val();
    $.post(baseDir+'modules/fbloginblock/inupdate.php', 
    			{cid:'{/literal}{$fbloginblockcid|escape:'htmlall':'UTF-8'}{literal}',
				 email:inemail 
    			 }, 
    function (data) {
    	if (data.status == 'success') {

    		$('#fb-con-wrapper').html('');
    		$('#fb-con-wrapper').html('<br/><p>'+data.params.content+'</p><br/>');
    		$('#fb-con-wrapper').css('opacity',1);
    	} else {
    		$('#fb-con-wrapper').css('opacity',1);
    		alert(data.message);
    		
    	}
    }, 'json');
    
    }
    
	{/literal}
	
{if $fbloginblockis16 != 1}
{literal}
	// ]]>

{/literal}
{/if}
{literal}
--></script>
{/literal}

<!--  show popup for twitter customer which not changed email address  -->
{/if}

{/if}
