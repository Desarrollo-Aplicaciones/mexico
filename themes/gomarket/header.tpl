{* forces client-side cache auto refresh{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7 " lang="{$lang_iso}"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7" lang="{$lang_iso}"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8" lang="{$lang_iso}"> <![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9" lang="{$lang_iso}"> <![endif]-->

<!-- validacion cookie para estilos, aplicacion -> pagina web -->
{if isset($smarty.cookies.validamobile)}
	
	<!-- Paso 5 -> Pedido Generado -->
	{if $smarty.get.controller == 'orderconfirmation'}
		<style type="text/css">
			#order_step {
				display: none!important;
			}
		</style>
	{/if}

{/if}
<!-- fin validacion cookie para estilos, aplicacion -> pagina web -->


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}">
<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
{/if}
		{* forces client-side cache auto refresh *}
		<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate" />
		<meta http-equiv="Last-Modified" content="0">
		<meta http-equiv="Pragma" content="no-cache" />
		<meta http-equiv="Expires" content="0" />
		{* /forces client-side cache auto refresh *}
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta http-equiv="content-language" content="{$meta_language}" />
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport"/>
                <meta name="alexaVerifyID" content="rTrWSM7GJbZLefBY3yrb80s3P3M"/>
		{*<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>*}
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,600' rel='stylesheet' type='text/css'>
		<link href='https://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
		<script	src="{$base_dir_ssl}js/jquery/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			var baseDir = '{$content_dir|addslashes}';
			var baseUri = '{$base_uri|addslashes}';
			var static_token = '{$static_token|addslashes}';
			var token = '{$token|addslashes}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
                        var ruta = "{$img_ps_dir}../ajax_newsletter.php"; 
		</script>

{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}?v=1.01" rel="stylesheet" type="text/css" media="{$media}" />
	{/foreach}
{/if}

{if isset($js_files)}
	{foreach from=$js_files item=js_uri}	
		{if isset($settings->column) && $settings->column == '1_column'}
			{if !strpos($js_uri,"blocklayered.js")}
				<script type="text/javascript" src="{$js_uri}"></script>
			{/if}
		{else}
			<script type="text/javascript" src="{$js_uri}"></script>
		{/if}
	{/foreach}
{/if}
<!--[if IE 7]><link href="{$css_dir}global-ie.css" rel="stylesheet" type="text/css" media="{$media}" /><![endif]-->
<script src="{$base_dir_ssl}js/jquery/plugins/jquery.scrolltofixed.js" type="text/javascript"></script>
<script>
	$(document).ready(function() {
	  $('#static_header').scrollToFixed();
	});
</script>
{$HOOK_HEADER}
<!-- Apple Touch Icons-->
<link rel="apple-touch-icon" href="{$base_dir_ssl}img/touch-icon-iphone.png" />
<link rel="apple-touch-icon" sizes="72x72" href="{$base_dir_ssl}img/touch-icon-ipad.png" />
<link rel="apple-touch-icon" sizes="114x114" href="{$base_dir_ssl}img/touch-icon-iphone-retina.png" />
<link rel="apple-touch-icon" sizes="144x144" href="{$base_dir_ssl}img/touch-icon-ipad-retina.png" />
<link rel="apple-touch-icon" sizes="180x180" href="{$base_dir_ssl}img/touch-icon-iphone6P.png" />
<link rel="icon" sizes="192x192" href="{$base_dir_ssl}img/touch-icon-hq.png"/>
<link rel="icon" sizes="128x128" href="{$base_dir_ssl}img/touch-icon-lq.png" />
<link rel="icon" sizes="40x40" href="{$base_dir_ssl}img/touch-icon-40x40.png"/>

<script type="application/ld+json">
{
	"@context" : "http://schema.org",
	"@type" : "Organization",
	"name" : "Farmalisto",
	"url" : "https://www.farmalisto.com.mx/",
	"sameAs" : [
		"https://www.facebook.com/farmalistomexico",
		"https://twitter.com/farmalistomex",
		"https://www.linkedin.com/company/farmalisto"
	]
}
</script>

	</head>
	
	<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if} class="{if $hide_left_column}hide-left-column{/if} {if $hide_right_column}hide-right-column{/if} {if $content_only} content_only {/if}">

    {if isset($customerName) && $customerName && isset($customerEmail) && $customerEmail }
		<div id="FAR-33" class="hidden">
			<input type="hidden" id="nombre_usuario_logueado_tm" name="nombre_usuario_logueado_tm" value="{$customerName}">
			<input type="hidden" id="email_usuario_logueado_tm" name="email_usuario_logueado_tm" value="{$customerEmail}">
		</div>
    {/if}

	{if $lightbox_horario_call == 1 && !isset($smarty.cookies.Cooklightbox_horario_call_mex) }
		<div id="lightbox_horario_call" style="display:none;">
			<div class="close_lightbox_horario_call" onclick="lightbox_hide(); "></div>

			<a href="https://www.farmalisto.com.mx/content/61-Horarios-Diciembre">
				<img id="img_lightbox_horario_call_1" src="{$base_dir_ssl}img/Licghtbox-horario-diciembre-desktop-Mx.jpg" style="width:90%;"/>
				<img id="img_lightbox_horario_call_2" src="{$base_dir_ssl}img/Lightbox-horario-diciembre-movil-Mx.jpg" style="width:90%; display:none;"/>
			</a>
		</div>
		<script type="text/javascript">
			$('document').ready( function(){
				standard_lightbox('lightbox_horario_call');
			});
		</script>
		{literal}
			<style type="text/css">
				.close_lightbox_horario_call {
					content: "asdf";
					font-size: 19.3px;
					width: 26px;
					height: 26px;
					-webkit-border-radius: 50%;
					border-radius: 50%;
					border:7px solid white;
					background-color: #4d4d4d;
					position:absolute;
					top: -1px;
					right: 10px;
					color: white;
					font-weight: 600;
					cursor: pointer;
				}

				.close_lightbox_horario_call:after {
					content: "\00D7";
				}

				@media only screen and (min-width: 200px) and (max-width: 479px) {
					#img_lightbox_horario_call_1{display:none!important;}
					#img_lightbox_horario_call_2{display:block!important;}
				}
				@media only screen and (min-width: 480px) and (max-width: 767px) {
					#img_lightbox_horario_call_1{display:none!important;}
					#img_lightbox_horario_call_2{display:block!important;}
				}
			</style>
		{/literal}
	{/if}

	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
		<div id="restricted-country">
			<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
		</div>
		{/if}
		<div id="page">
			<!-- Header -->

			<!-- validacion cookie para estilos, aplicacion -> pagina web -->
			{if isset($smarty.cookies.validamobile)}
				<br>
			{/if}
			
			<!-- validacion cookie para estilos, aplicacion -> pagina web -->
			{if !isset($smarty.cookies.validamobile)}
				<div class="mode_header" id="mode_header">
					<div id="header" class="grid_24 clearfix omega alpha">						
						<div class="globalheader">
							{if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
						</div>
					</div>
				</div>
			{/if}			
	
		<div class="container_24">
			{if $page_name == 'index'}
			{* if isset($CS_MEGA_MENU)}{$CS_MEGA_MENU}{/if *}
			<div class="hook_csslide_new">
			{if isset($HOOK_CS_SLIDESHOW)}{$HOOK_CS_SLIDESHOW}{/if}
			</div>
			{/if}
		</div>	
			<div class="mode_container">

				<div id="columns" class="{if isset($grid_column)}{$grid_column}{/if} grid_24 omega alpha">
				{if $page_name != 'index'}
					{if isset($settings)}
						{if (($settings->column == '2_column_left' || $settings->column == '3_column'))}
							<!-- Left -->
							<div id="left_column" class="{$settings->left_class} alpha">				
								{$HOOK_LEFT_COLUMN}
							</div>
						{/if}
					{else}
						<!-- Left -->
							<div id="left_column" class="grid_5 alpha">
								
								{$HOOK_LEFT_COLUMN}
							</div>
					{/if}
				{/if}
				<div class="container_24">
					<!-- Center -->
					<div id="center_column" class="{if $page_name == 'index'}grid_24 omega alpha{else}{if isset($settings)}{$settings->center_class} {else}grid_19 omega{/if}{/if}">
		{/if}
	<div class="container_24">
 
{*if isset($iexplorerold) && $iexplorerold eq true and isset($lightboxshow) and $lightboxshow eq 'si'}
   

{literal}
    
<style>
#popup {
	left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 1001;
}

.content-popup {
	margin:0px auto;
	margin-top:120px;
	position:relative;
	padding:10px;
	width:500px;
	min-height:250px;
	border-radius:4px;
	background-color:#FFFFFF;
	box-shadow: 0 2px 5px #666666;
}

.content-popup h2 {
	color:#48484B;
	border-bottom: 1px solid #48484B;
    margin-top: 0;
    padding-bottom: 4px;
}

.popup-overlay {
	left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 999;
	display:none;
	background-color: #777777;
    cursor: pointer;
    opacity: 0.7;
}

.close {
	position: absolute;
    right: 15px;
}
</style>
                        
                        
 <script type="text/javascript">
     
           
     function closePopaUp()
     {
	$('#popup').fadeOut('slow');
        $('.popup-overlay').fadeOut('slow');
		 
     {/literal}
      {if isset($newsletter) && isset($iexplorerold) && $iexplorerold eq true && $newsletter eq true and isset($lightboxshow) and $lightboxshow eq 'si'}      
          {literal}
                
		if($.cookie('lightboxsTime') != undefined){ 

			var obJson = $.parseJSON($.cookie('lightboxsTime'));
			var date_1 = new Date(obJson.timeIni); console.log('feccha1: '+date_1);
  			var date_2 = new Date(obJson.timeAfater); console.log('feccha1: '+date_2);
  			var diferencia = (date_1-date_2) * 1000;
  			console.log('diferencia1: '+diferencia);
  			if(diferencia < 0){
  				diferencia = 0;
  			}
  			console.log('diferencia2: '+diferencia);
			setTimeout(function(){
				$('#news').fadeIn('slow');
				$('.news-overlay').fadeIn('slow');
				$('.news-overlay').height($(window).height());
			}, diferencia );
	}
          {/literal}
      }
      {/if}
          {literal}    
      return false;       
     }
     
     
$(document).ready(function(){
    

		$('#popup').fadeIn('slow');
		$('.popup-overlay').fadeIn('slow');
		$('.popup-overlay').height($(window).height());
	
	      
});


</script>                        
                        
{/literal}
    
{/if}


{if isset($newsletter) && $newsletter eq true and isset($lightboxshow) and $lightboxshow eq 'si'}
    
   
     
<style type="text/css">
#news {
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 1001;
   max-width: initial;
    
    
}

.content-news {
	margin:0px auto;
	margin-top:120px;
	position:relative;
	 padding: 13px;
	width:720px;
        height: 445px;
	min-height:411px;
	border-radius:4px;
	background-color:#FFFFFF;
	box-shadow: 0 2px 5px #666666;
        background: url('{$base_dir_ssl}img/cms/landing/newsletter.jpg') no-repeat;
}

.content-news h2 {
   color:#48484B;
   border-bottom: 1px solid #48484B;
   margin-top: 0;
   padding-bottom: 4px;
}

.news-overlay {
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
   /* height: 100%; */
    z-index: 999;
    display:none;
    background-color: #777777;
    cursor: pointer;
    opacity: 0.7;
    /*max-width: initial;*/
 
   
}

.close-news {
    position: absolute;
    right: 15px;
}



#hombre1 {
   //    background:url('{$base_dir_ssl}img/bt1.jpg') no-repeat;
width:80px;
height:32px;
margin: 6px 0 0 10px;
border:none;	
//     -webkit-background-size: 100% 100%;           /* Safari 3.0 */
//    -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
//   -o-background-size: 100% 100%;           /* Opera 9.5 */
//   background-size: 100% 100%;
    opacity: 0.0;
    filter: alpha(opacity=0); /* For IE8 and earlier */
}

#hombre1:hover
{
  
width:80px;
height:32px;
 margin: 6px 0 0 10px;
border:none;
   // background:url('{$base_dir_ssl}img/bt1.jpg') no-repeat;
  //  -webkit-background-size: 100% 100%;           /* Safari 3.0 */
  //  -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
  //  -o-background-size: 100% 100%;           /* Opera 9.5 */
  //  background-size: 100% 100%;
      opacity: 0.0;
    filter: alpha(opacity=0); /* For IE8 and earlier */
    
}



#mujer1{
    width:80px;
height:32px;
 margin: 6px 0 0 10px;
 border:none;
   // background:url('{$base_dir_ssl}img/bt2.jpg') no-repeat;
  //  -webkit-background-size: 100% 100%;           /* Safari 3.0 */
   // -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
   // -o-background-size: 100% 100%;           /* Opera 9.5 */
   // background-size: 100% 100%;
       opacity: 0.0;
    filter: alpha(opacity=0); /* For IE8 and earlier */
}

#mujer1:hover
{
  
width:80px;
height:32px;
 margin: 6px 0 0 10px;
border:none;
  //  background:url('{$base_dir_ssl}img/bt2.jpg') no-repeat;
  //  -webkit-background-size: 100% 100%;           /* Safari 3.0 */
  //  -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
  //  -o-background-size: 100% 100%;           /* Opera 9.5 */
   // background-size: 100% 100%;
       opacity: 0.0;
    filter: alpha(opacity=0); /* For IE8 and earlier */
    
}


.divTable
    {
        display:  table;
        width:auto;
       // background-color:#eee;
       // border:1px solid  #666666;
        // border-spacing:5px;/*cellspacing:poor IE support for  this*/
        margin: 1px 0 -9px -47px;
        }

    .divRow
    {
       display:table-row;
       width:auto;
       width: 259px;
       margin: 0 0 0 30px;
      
    }

    .divCell
    {
        float:left;/*fix for  buggy browsers*/
        display:table-column;
       text-align: right;
        width: 294px;
        
    height: 44px;
        // background-color:#ccc;
    }


</style>
 

 {/if}   


{if isset($newsletter)  && isset($iexplorerold) && $newsletter eq true && $iexplorerold eq false and isset($lightboxshow) and $lightboxshow eq 'si'}
    {literal}            
  <script type="text/javascript">          
   
	$(document).ready(function(){

		if($.cookie('lightboxsTime') != undefined){ 

			var obJson = $.parseJSON($.cookie('lightboxsTime'));
			var date_1 = new Date(obJson.timeIni); console.log('feccha1: '+date_1);
  			var date_2 = new Date(obJson.timeAfater); console.log('feccha1: '+date_2);
  			var diferencia = (date_1-date_2)* 1000;
  			console.log('diferencia1: '+diferencia);
  			if(diferencia < 0){
  				diferencia = 0;
  			}
  			console.log('diferencia2: '+diferencia);
			setTimeout(function(){
				$('#news').fadeIn('slow');
				$('.news-overlay').fadeIn('slow');
				$('.news-overlay').height($(window).height());
			}, diferencia );
	}
	
	$('#close-news').click(function(){
		$('#news').fadeOut('slow');
		$('.news-overlay').fadeOut('slow');
		$.cookie('newsletter', 'newsletter', { expires: 30, path: '/' });
	});
});
</script>
{/literal}

{elseif isset($newsletter) && isset($iexplorerold) && $newsletter eq true && $iexplorerold eq true and isset($lightboxshow) and $lightboxshow eq 'si'}
    
{literal}
    
 <script type="text/javascript">
 $(document).ready(function(){
   
        // cerrar ligthbox
        $('#close-news').click(function(){
		$('#news').fadeOut('slow');
		$('.news-overlay').fadeOut('slow');
		return false;
	});
});
        
 </script> 
    
{/literal}
    
{/if*}