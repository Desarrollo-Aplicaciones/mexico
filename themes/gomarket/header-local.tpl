{*
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
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}">
<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
{if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:html:'UTF-8'}" />
{/if}
{if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:html:'UTF-8'}" />
{/if}
		<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
		<meta http-equiv="content-language" content="{$meta_language}" />
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta content="width=device-width, initial-scale=1, maximum-scale=1" name="viewport"/>
				
		<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>
		
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
		<script type="text/javascript">
			var baseDir = '{$content_dir|addslashes}';
			var baseUri = '{$base_uri|addslashes}';
			var static_token = '{$static_token|addslashes}';
			var token = '{$token|addslashes}';
			var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals};
			var priceDisplayMethod = {$priceDisplay};
			var roundMode = {$roundMode};
		</script>

{if isset($css_files)}
	{foreach from=$css_files key=css_uri item=media}
	<link href="{$css_uri}" rel="stylesheet" type="text/css" media="{$media}" />
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

{$HOOK_HEADER}
	</head>
	
	<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if} class="{if $hide_left_column}hide-left-column{/if} {if $hide_right_column}hide-right-column{/if} {if $content_only} content_only {/if}">
	{if !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
		<div id="restricted-country">
			<p>{l s='You cannot place a new order from your country.'} <span class="bold">{$geolocation_country}</span></p>
		</div>
		{/if}
		<div id="page">
			<!-- Header -->
			<div class="mode_header" id="mode_header">
				<div class="container_24">
					<div id="header" class="grid_24 clearfix omega alpha">						
						<div id="header_right">
							{$HOOK_TOP}							
						</div>
						<a id="header_logo" href="{$base_dir}" title="{$shop_name|escape:'htmlall':'UTF-8'}">
							<img class="logo" src="{$logo_url}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" />
						</a>
						{if $page_name != 'index'}
						{if isset($CS_MEGA_MENU)}{$CS_MEGA_MENU}{/if}
						{/if}
					</div>
				</div>
			</div>					
			<script>
//				$(document).ready(function(){
//				  var acc = prompt("Digite la clave de acceso")
//				  if (acc !='farma13'){
//					window.location='http://www.google.com';
//				  }
//				})
			</script>
	
			{if $page_name == 'index'}
			<div class="container_24">
				<div class="hook_csslide_new">
				{if isset($HOOK_CS_SLIDESHOW)}{$HOOK_CS_SLIDESHOW}{/if}
				</div>
				{if isset($CS_MEGA_MENU)}{$CS_MEGA_MENU}{/if}
			</div>
			{/if}
			
			<div class="mode_container">
				<div class="container_24">
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
					<!-- Center -->
					<div id="center_column" class="{if $page_name == 'index'}grid_24 omega alpha{else}{if isset($settings)}{$settings->center_class} {else}grid_19 omega{/if}{/if}">
		{/if}

              
                

                                
                
{if isset($iexplorerold)}

{literal}
    
<style>
#popup {
	left: 0;
    position: absolute;
    top: 0;
  
    z-index: 1001;
    width: 100%;
 
  
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
    
    z-index: 999;
	display:none;
	background-color: #777777;
    cursor: pointer;
    opacity: 0.7;
   max-width: 50000px;

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
      {if isset($newsletter) and isset($iexplorerold)}      
          {literal}
                
         $('#news').fadeIn('slow');
		$('.news-overlay').fadeIn('slow');
		$('.news-overlay').height($(window).height());
          {/literal}
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



{if isset($newsletter)}
     
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
	padding:10px;
	width:720px;
	min-height:411px;
	border-radius:4px;
	background-color:#FFFFFF;
	box-shadow: 0 2px 5px #666666;
        background: url('img/newsletter.png') no-repeat;
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



#hombre {
       background:url('img/news-hombre.png') no-repeat;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}

#hombre:hover
{
  
width:110px;
height:43px;
border:none;
    background:url('img/news-hombre-hover.png') no-repeat;
    -webkit-background-size: 100% 100%;           /* Safari 3.0 */
    -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
    -o-background-size: 100% 100%;           /* Opera 9.5 */
    background-size: 100% 100%;
    
}



#mujer{
    background:url('img/news-mujer.png') no-repeat;
    -webkit-background-size: 100% 100%;           /* Safari 3.0 */
    -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
    -o-background-size: 100% 100%;           /* Opera 9.5 */
    background-size: 100% 100%;
}

#mujer:hover
{
  
width:110px;
height:43px;
border:none;
    background:url('img/news-mujer-hover.png') no-repeat;
    -webkit-background-size: 100% 100%;           /* Safari 3.0 */
    -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
    -o-background-size: 100% 100%;           /* Opera 9.5 */
    background-size: 100% 100%;
    
}


</style>

    
{/if}

{if isset($newsletter) and !isset($iexplorerold)}
    {literal}            
  <script type="text/javascript">          
   
   
$(document).ready(function(){
  

		$('#news').fadeIn('slow');
		$('.news-overlay').fadeIn('slow');
		$('.news-overlay').height($(window).height());
		
	
	$('#close-news').click(function(){
		$('#news').fadeOut('slow');
		$('.news-overlay').fadeOut('slow');
		return false;
	});
});
</script>
{/literal}

{elseif isset($newsletter) and isset($iexplorerold)}
    
{literal}
    
 <script type="text/javascript">
 $(document).ready(function(){
        
        $('#close-news').click(function(){
		$('#news').fadeOut('slow');
		$('.news-overlay').fadeOut('slow');
		return false;
	});
});
        
 </script> 
    
{/literal}
    
{/if}