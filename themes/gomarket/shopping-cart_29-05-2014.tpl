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



{* include file="$tpl_dir./breadcrumb.tpl" *}

<style type="text/css"> 
#cupon{
  width: 32%;
  height: auto;
  margin: 3px 3px;
  padding: 5px 3px;
  float: left;
  min-width: 120px;
} 


#boxmedisp
{
    
  width: 27%;
  height: auto;
  margin: 3px 3px;
  padding: 5px 1px;
  float: left;
  min-width: 200px;
} 

#boxnefi
{
    
  width: 28%;
  height: auto;
  margin: 3px 3px;
  padding: 5px 1px;
  float: left;
  min-width: 200px;
} 

#imgenvio
{
width:30px;
height: 15px;
border:none;
background:url({$img_dir}mediosp/g644.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}


#imgdiscr
{
width:20px;
height: 26px;
border:none;
background:url({$img_dir}mediosp/g648.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}


#imgprecio
{
width:26px;
height: 15px;
border:none;
background:url({$img_dir}mediosp/g652.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}


#imgamex
{
width:51px;
height: 32px;
margin: 5px 5px;
border:none;
background:url({$img_dir}mediosp/amex.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}


#imgvisa
{
width:51px;
height: 32px;
margin: 5px 5px;
border:none;
background:url({$img_dir}mediosp/visa.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}

#imgmaster

{
    
width:51px;
height: 32px;
margin: 5px 5px;
border:none;
background:url({$img_dir}mediosp/master.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}

#imgdiners{
width:51px;
height: 32px;
margin: 5px 5px;
border:none;
background:url({$img_dir}mediosp/diners.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;

}

#imgpse{
 width:33px;
height: 33px;
margin: 5px 5px;
border:none;
background:url({$img_dir}mediosp/pse.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;

}


#imgbaloto{
    width:22px;
height: 33px;
margin: 5px 5px;
border:none;
background:url({$img_dir}mediosp/baloto.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;

}

#imgcod{
    width:51px;
height: auto;
border:none;
background:url({$img_dir}mediosp/cod.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;

}   




    
    #processCarrier
{
 padding: 0 0;    
width:145px;
height:40px;
border:none;
background:url({$img_dir}formula-medica/btn-continuar.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 137px;
}

#processCarrier:hover
{
padding: 0 0;    
width:145px;
height:40px;
border:none;
border-style: none;

background:url({$img_dir}formula-medica/btn-continuar-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 137px;
}



#atras1
{
padding: 0 0;  
width:145px;
height:40px;

animation: none !important;
border:none;
transition:none;
background:url({$img_dir}formula-medica/btn-anterior.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 137px;
}
#atras1:hover
{
    
padding: 0 0;

animation: none !important;
transition:none;
width:145px;
height:40px;
border:none;
border-style: none;
background:url({$img_dir}formula-medica/btn-anterior-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 137px;
}



</style>{literal}<style type="text/css">
#productoLabel{width: 125px;float: left;font-weight: 700;margin-top: 7px;color: #969696 !important;background: #666666;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#E6E6E6), to(#fff));background: -moz-linear-gradient(#E6E6E6, #fff);background: linear-gradient(#E6E6E6, #fff);-pie-background: linear-gradient(#E6E6E6, #646464);box-shadow: 0px 0px 3px #333;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;-moz-border-radius: 3px 3px 3px 3px;-webkit-border-radius: 3px 3px 3px 3px;font-weight: 100;font-size: 13px;text-transform: capitalize;text-align: center;margin: 7px 41px 0 0;}
#descripcionLabel{width: 125px;float: left;font-weight: 700;margin-top: 7px;color: #969696 !important;background: #666666;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#E6E6E6), to(#fff));background: -moz-linear-gradient(#E6E6E6, #fff);background: linear-gradient(#E6E6E6, #fff);-pie-background: linear-gradient(#E6E6E6, #646464);box-shadow: 0px 0px 3px #333;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;-moz-border-radius: 3px 3px 3px 3px;-webkit-border-radius: 3px 3px 3px 3px;font-weight: 100;font-size: 13px;text-transform: capitalize;text-align: center;margin: 7px 41px 0 0;}
#referenciaLabel{width: 125px;float: left;font-weight: 700;margin-top: 7px;color: #969696 !important;background: #666666;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#E6E6E6), to(#fff));background: -moz-linear-gradient(#E6E6E6, #fff);background: linear-gradient(#E6E6E6, #fff);-pie-background: linear-gradient(#E6E6E6, #646464);box-shadow: 0px 0px 3px #333;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;-moz-border-radius: 3px 3px 3px 3px;-webkit-border-radius: 3px 3px 3px 3px;font-weight: 100;font-size: 13px;text-transform: capitalize;text-align: center;margin: 7px 41px 0 0;}
#precioLabel{width: 125px;float: left;font-weight: 700;margin-top: 7px;color: #969696 !important;background: #666666;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#E6E6E6), to(#fff));background: -moz-linear-gradient(#E6E6E6, #fff);background: linear-gradient(#E6E6E6, #fff);-pie-background: linear-gradient(#E6E6E6, #646464);box-shadow: 0px 0px 3px #333;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;-moz-border-radius: 3px 3px 3px 3px;-webkit-border-radius: 3px 3px 3px 3px;font-weight: 100;font-size: 13px;text-transform: capitalize;text-align: center;margin: 7px 41px 0 0;}
#cantidadLabel{width: 125px;float: left;font-weight: 700;margin-top: 7px;color: #969696 !important;background: #666666;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#E6E6E6), to(#fff));background: -moz-linear-gradient(#E6E6E6, #fff);background: linear-gradient(#E6E6E6, #fff);-pie-background: linear-gradient(#E6E6E6, #646464);box-shadow: 0px 0px 3px #333;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;-moz-border-radius: 3px 3px 3px 3px;-webkit-border-radius: 3px 3px 3px 3px;font-weight: 100;font-size: 13px;text-transform: capitalize;text-align: center;margin: 7px 41px 0 0;}
#totaLabel{width: 125px;font-weight: 700;margin-top: 7px;color: #969696 !important;background: #666666;background: -webkit-gradient(linear, 0 0, 0 bottom, from(#E6E6E6), to(#fff));background: -moz-linear-gradient(#E6E6E6, #fff);background: linear-gradient(#E6E6E6, #fff);-pie-background: linear-gradient(#E6E6E6, #646464);box-shadow: 0px 0px 3px #333;-webkit-box-shadow: #666 0px 2px 3px;-moz-box-shadow: #666 0px 2px 3px;-moz-border-radius: 3px 3px 3px 3px;-webkit-border-radius: 3px 3px 3px 3px;font-weight: 100;font-size: 13px;text-transform: capitalize;text-align: center;margin: 7px 41px 0 0;}
#imagenProductoCompra{float:left;width: 175px;}
#contenedor3contenidos{border-radius: 6px; border: 1px solid #E7E7E7; xborder-collapse: collapse; width:715px; height:154px; float:left;margin: 0 10px 0 0;}
#primerLabel{padding: 14px 70px; color:#676767; text-align: right;}
#segundoLabel{text-align: left;margin-top: 102px;}
#tercerLabel{margin-top: -17px;margin-left: 827px;}
#cuartoLabel{margin-top: -17px;margin-left: 888px;}
#quintoLabel{margin-top: -36px;margin-left: 938px; width: 50px;}
#sextoLabel{margin-top: -13px;margin-right: -10px;}
#precioContenedor{float: right;margin-top: -30px;}
#total_tax{margin-top:-28px; color:#676767; text-align: right;}
#total_product{margin-top: -30px;color: #676767;text-align: right;float: right;}
#total_price_without_tax{margin-top:-28px; color:#676767; text-align: right;}
#descuentoValor{text-align: right;margin-right: 59px;color:#676767; }
#descripcioNombre {margin-left: 0px!important;}


@media only screen and (min-width: 768px) and (max-width: 1000px) {
	
		#productoLabel {width: 113px;margin-top: 38px;margin: 38px 7px 0 0;}
		#referenciaLabel {width: 125px;margin-top: 38px;margin: 38px 7px 0 0;}
		#precioLabel {width: 125px;margin-top: 38px;margin: 38px 7px 0 0;}
		#descripcionLabel {width: 125px!important;margin-top: 38px;margin: 38px 7px 0 0;}
		#cantidadLabel{margin-top: 38px;width: 125px;margin: 38px 7px 0 0;}
		#totaLabel{margin-top: 38px;width: 113px}
		.cart_description  {width: 61px!important;margin: 0px 23px 0px 0px;}
		#descripcioNombre{margin-left: -15px!important;float: left;}
		#cantidades{margin-left: -27px!important;margin:38px 7px 0px 0px!important;text-align: left;}
		.cart_ref {width: 111px!important;margin:38px 7px 0px 0px!important;}
		.cart_unit {margin-left: -30px;margin:38px 7px 0px 0px!important;text-align: center;}
		.cart_total .price {margin-left: 25px!important;margin: 38px 7px 0px 95px!important;text-align: center;}
		.cart_delete a.cart_quantity_delete, a.price_discount_delete{margin-right: 253px!important;}
		#atras1{margin: 48px 0 26px 0;margin-top: 132px!important;}
		#processCarrier{margin: 48px 0 26px 0;margin-top: 132px!important;}
		#atras1:hover{margin: 48px 0 26px 0;margin-top: 132px!important;}
		#processCarrier:hover{margin: 48px 0 26px 0;margin-top: 132px!important;}
		#primerLabel{margin-left: 440px;padding: 10px;float: left;}
		#contenedor3contenidos{width:746px;}	
        #precioContenedor{margin-top:19px;}
        #total_tax{margin-top: 10px;color: #676767;text-align: left;margin-left: 179px;float: left;}        
		#total_product{margin-top: 14px;color: #676767;text-align: left;float: left;margin-left: 12px;}
		#total_price_without_tax{margin-top: 11px;text-align: left;float: left;margin-left: 136px;}
		#descuentoValor{text-align: right;margin-right: 136px;}
		#segundoLabel {margin-top: -52px;}
		#tercerLabel {margin-left: 452px;}
		#cuartoLabel {margin-left: 548px;}
		#quintoLabel {margin-left: 697px;}
		#sextoLabel {margin-right: 369px;}
		#descuentoLabel{margin-top: 200px;}
		#cajon{display: flex;}
		#imagenProductoCompra {float: left;width: 157px;}

}

@media only screen and (min-width: 480px) and (max-width: 767px) {

		#tercerLabel {margin-left: 240px;}
		#cuartoLabel {margin-left: 299px;}
		#quintoLabel {margin-left: 352px;}
		#sextoLabel {margin-right: 293px;}
		ul#order_step {width: 100%;}
		#productoLabel {width: 125px;}
		#referenciaLabel {width: 125px;margin-top: 70px;}
		#precioLabel{margin-top: 20px;margin-left: 0px;}
		#cantidadLabel{margin-top: 20px;margin-left: 0px;}
		#totaLabel{margin-top: 20px;}
		#imagenProductoCompra {width: 82px;margin-left: -140px;margin-top: -17px;}
		.cart_description p.s_title_block{margin-top: -12px;margin-left: -61px;width: 89px!important;}
		#descripcioNombre {margin-left: 47px!important;float:left;font-size: 11px!important;margin-top: 13px!important;}
		.cart_description p.s_title_block a{width: 74px!important;}
		.cart_description {width: 74px!important;}
		#descripcionLabel{width:125px!important;margin-top: 88px!important;margin-left: 2px!important;}
		.cart_ref{font-size: 11px;}
		.cart_product{margin-left: 149px;}
		.totaLabel{margin-left: -34px;width: 125px;}
		#cajon{display: table-caption;width: 134px;height: 330px;float: left;position: relative;}
		#contenedorProductos{width: 305px;height: 354px;display: inline-flex;margin: 0 0 20px 0;overflow-x: scroll;}

}
@media screen and (max-width:480px){

		#tercerLabel {margin-left: 240px;}
		#cuartoLabel {margin-left: 299px;}
		#quintoLabel {margin-left: 352px;}
		#sextoLabel {margin-right: 293px;}
		ul#order_step {width: 100%;}
		#productoLabel {width: 125px;}
		#referenciaLabel {width: 125px;margin-top: 70px;}
		#precioLabel{margin-top: 20px;margin-left: 0px;}
		#cantidadLabel{margin-top: 20px;margin-left: 0px;}
		#totaLabel{margin-top: 20px;}
		#imagenProductoCompra {width: 82px;margin-left: -140px;margin-top: -17px;}
		.cart_description p.s_title_block{margin-top: -12px;margin-left: -61px;width: 89px!important;}
		#descripcioNombre {margin-left: 47px!important;float:left;font-size: 11px!important;margin-top: 13px!important;}
		.cart_description p.s_title_block a{width: 74px!important;}
		.cart_description {width: 74px!important;}
		#descripcionLabel{width:125px!important;margin-top: 88px!important;margin-left: 0px!important;}
		.cart_ref{font-size: 11px;}
		.cart_product{margin-left: 149px;}
		.totaLabel{margin-left: -34px;width: 125px;}
		#cajon{display: table-caption;width: 134px;height: 330px;float: left;position: relative;}
		#contenedorProductos{width: 305px;height: 354px;display: inline-flex;margin: 0 0 20px 0;overflow-x: scroll;}

}

</style>{/literal}


<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
<link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />


<h1 id="cart_title" style="color:#9C9C9C; font-size: 24px; font-family:Verdana; ">Mi Carrito</h1> 

{if isset($account_created)}
	<p class="success">
		{l s='Your account has been created.'}
	</p>
{/if}
{assign var='current_step' value='summary'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}

{if isset($empty)}
	<p class="warning">{l s='Your shopping cart is empty.'}</p>
{elseif $PS_CATALOG_MODE}
	<p class="warning">{l s='This store has not accepted your new order.'}</p>
{else}
	<script type="text/javascript">
	// <![CDATA[
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='product' js=1}";
	var txtProducts = "{l s='products' js=1}";
	var deliveryAddress = {$cart->id_address_delivery|intval};
	// ]]>
	</script>
	<p style="display:none" id="emptyCartWarning" class="warning">{l s='Your shopping cart is empty.'}</p>
<!--
        {if isset($lastProductAdded) AND $lastProductAdded}
	<div class="cart_last_product">
		<div class="cart_last_product_header">
			<div class="left">{l s='Last product added'}</div>
		</div>
		<a  class="cart_last_product_img" href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, $lastProductAdded.id_shop)|escape:'htmlall':'UTF-8'}"><img src="{$link->getImageLink($lastProductAdded.link_rewrite, $lastProductAdded.id_image, 'small_default')|escape:'html'}" alt="{$lastProductAdded.name|escape:'htmlall':'UTF-8'}"/></a>
		<div class="cart_last_product_content">
			<p class="s_title_block"><a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$lastProductAdded.name|escape:'htmlall':'UTF-8'}</a></p>
			{if isset($lastProductAdded.attributes) && $lastProductAdded.attributes}<a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$lastProductAdded.attributes|escape:'htmlall':'UTF-8'}</a>{/if}
		</div>
		<br class="clear" />
	</div> 
{/if}
-->
<!-- <p>{l s='Your shopping cart contains:'} <span id="summary_products_quantity">{$productNumber} {if $productNumber == 1}{l s='product'}{else}{l s='products'}{/if}</span></p> 
<p>su carrito contiene: <span id="summary_products_quantity">{$productNumber} {if $productNumber == 1}{l s='product'}{else}{l s='products'}{/if}</span></p>
-->

<div id="order-detail-content" class="table_block">
	
    <div id="cart_summary" class="std" style="margin-top: 113px;margin-bottom: 20px;width: 100%;border-collapse: inherit;border-radius: 2px;-moz-border-radius: 2px;box-shadow: 0 0 0 transparent;margin-top: 181px;overflow: hidden;">
          
		<div class="m_hide" style="width: 1000px;height: auto;">
			<div id="cajon">
				<div class="cart_product first_item" id="productoLabel">{l s='Product'}</div>
				<div class="cart_description item" id="descripcionLabel">{l s='Description'}</div>
				<div class="cart_ref item" id="referenciaLabel">{l s='Ref.'}</div>
				<div class="cart_unit item" id="precioLabel">{l s='Unit price'}</div>
				<div class="cart_quantity item" id="cantidadLabel">{l s='Qty'}</div>
				<div class="cart_total item" id="totaLabel">{l s='Total'}</div>
				<div class="cart_delete last_item">&nbsp;</div>
			</div>
		
                
                
                <div id="contenedorProductos" >
		{assign var='odd' value=0}
		{foreach $products as $product}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{assign var='odd' value=($odd+1)%2}
			{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) || count($gift_products)}
			{* Display the product line *}
			{include file="$tpl_dir./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
                        
                        
			{* Then the customized datas ones*}
			{if isset($customizedDatas.$productId.$productAttributeId)}
				{foreach $customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] as $id_customization=>$customization}
				<div style="border: solid #D8000C;" id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" class="product_customization_for_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if} customization alternate_item {if $product@last && $customization@last && !count($gift_products)}last_item{/if}">
						
						<div >
							{foreach $customization.datas as $type => $custom_data}
								{if $type == $CUSTOMIZE_FILE}
									<div class="customizationUploaded">
										<ul class="customizationUploaded">
											{foreach $custom_data as $picture}
												<li><img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" /></li>
											{/foreach}
										</ul>
									</div>
								{elseif $type == $CUSTOMIZE_TEXTFIELD}
									<ul class="typedText">
										{foreach $custom_data as $textField}
											<li>
												{if $textField.name}
													{$textField.name}
												{else}
													{l s='Text #'}{$textField@index+1}
												{/if}
												{l s=':'} {$textField.value}
											</li>
										{/foreach}
										
									</ul>
								{/if}

							{/foreach}
						</div>
						<div class="cart_quantity">
							{if isset($cannotModify) AND $cannotModify == 1}
								<span style="float:left">{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
							{else}
								<div class="cart_quantity_button">
								<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="11" height="11" /></a><br />
								{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
								<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract'}">
									<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="11" height="11" />
								</a>
								{else}
								<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="#" title="{l s='Subtract'}">
									<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="11" height="11" />
								</a>
								{/if}
								<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}_hidden"/>
								<input size="2" type="text" value="{$customization.quantity}" class="cart_quantity_input" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"/>
								</div>
								
							{/if}
						</div>
						<div class="cart_delete">
							{if isset($cannotModify) AND $cannotModify == 1}
							{else}
								<div>
									<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")}">{l s='Delete'}</a>
								</div>
							{/if}
						</div>
					</div>
					{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
				{/foreach}
				{* If it exists also some uncustomized products *}                           
                                
				{if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}{/if}
			{/if}
		{/foreach}
                
                
		{assign var='last_was_odd' value=$product@iteration%2}
		{foreach $gift_products as $product}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{assign var='odd' value=($product@iteration+$last_was_odd)%2}
			{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
			{assign var='cannotModify' value=1}
			{* Display the gift product line *}
			{include file="$tpl_dir./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
		{/foreach} 
		</div>
	</div><!--m_hide cerrar-->
                
                <!-- ########################################### -->
                <div id="contenedorVenta">
                <div class="cart_total_price">
                    	<div class="std" style="border: 0px;" style="margin-bottom: 20px;width: 102%;border-collapse: inherit;border-radius: 2px;-moz-border-radius: 2px;box-shadow: 0 0 0 transparent;">

             <div id="contenedor3contenidos">
                        
                                    
                       <div id="boxnefi">
                           <p  style="color: #399E98; font-size: 14px;" ><b>Beneficios</b></p>
                           
                           	 	<div style="border-bottom: 0px solid; padding :2px 2px" >
                           	 		<div style="float: left;" id="imgenvio"></div>
                           	 	</div>

                           	 	<div style="border-bottom: 0px solid; padding :2px 2px" >
                           	 		<div style="font-size: 10px; float: left;width: 194px;">*Envío <b>gratis</b> por compras superiores a <span style="color:#b7689e"><b><br>$ 49.900</b></span>
                           	 		</div>
                           	 	</div>

                           	 <div style="border-bottom: 0px solid; padding :2px 2px;width: 229px;height:54px;" ><div style="float: left;" id="imgdiscr"></div>
                           	 </div>
                           	 	<div style="border-bottom: 0px solid; padding :2px 2px;width: 227px;" >
                           	 		<div style="font-size: 10px;margin-top: -28px;margin-left: 27px;">* <b>Absoluta</b> discreción</div>
                           	 	</div>
                           	 
                           	 	<div style="border-bottom: 0px solid; padding :2px 2px" >
                           	 		<div style="float: left;" id="imgprecio"></div>
                           	 </div>
                           	 	<div style="border-bottom: 0px solid; padding :2px 2px" >
									<div style="font-size: 10px">* Mejor precio <a href="content/6-garantia-del-mejor-precio"><span style="color:#b7689e; font-size:10px"><b>Garantizado*</b></span></a></div>
                           	 	</div>
                           	 </div>
                             

                       
                        <div id="boxmedisp">
                            
                            <div id="fila1mp"  style="float: none; height: 100%; width: 100%;"> 
                              <p  style="color: #399E98; font-size: 14px;" ><b>Nuestros medios de pago</b></p>
                                <div style="float: left;" id="imgamex"></div> 
                                <div style="float: left;" id="imgvisa"></div> 
                                <div style="float: left;" id="imgmaster"></div> 
                                <div style="float: left;" id="imgdiners"></div> 
                           
                            
                             
                                <div style="float:left;" id="imgpse"></div>
                                <div style="float: left;" id="imgbaloto"></div>
                                <div style="float: left;" id="imgcod" ></div>   
                            </div>
                            
                        </div>

          <!-- Cupon apoyo a la salud -->              
          <div id="cupon">
	{if $voucherAllowed}
		{if isset($errors_discount) && $errors_discount}
			<ul class="error">
			{foreach $errors_discount as $k=>$error}
				<li>{$error|escape:'htmlall':'UTF-8'}</li>
			{/foreach}
			</ul>
		{/if}
		<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
			<fieldset>
                            <p ><label for="discount_name" style="color: #399E98; font-size: 14px;"><b>Apoyo Salud</b></label></p>
                            <input type="radio" name="type_voucher" value="md" > <span style="font-size: 13px; font:500 13px/14px 'Open Sans',Helvetica,arial;">Médico &nbsp;| &nbsp;	
                            <input type="radio" name="type_voucher" value="cupon" checked="checked"> Cupón	            </span> <p>
					<input style="width: 95%;" type="text" class="discount_name" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" /> 
					<input type="hidden" name="doc_fnd" id="doc_fnd" value="">
				</p>
				<div id="suggestions"></div>
				<p class="submit"><input type="hidden" name="submitDiscount" /> </p>
                                 <input type="submit" style="" name="submitAddDiscount" id="submitAddDiscount" value="{l s='OK'}" class="button" />
			</fieldset>
		</form>
		{if $displayVouchers}
			<p id="title" class="title_offers">{l s='Take advantage of our offers:'}</p>
			<div id="display_cart_vouchers">
			{foreach $displayVouchers as $voucher}
				{if $voucher.code != ''}<span onclick="$('#discount_name').val('{$voucher.code}');return false;" class="voucher_name">{$voucher.code}</span> - {/if}{$voucher.name}<br />
			{/foreach}
			</div>
		{/if}
	{/if}
	       </div> 
	       </div> 
	     </div>
	 </div>

	 <!-- total Productos -->
		{if $use_taxes}
			{if $priceDisplay}
				<div class="cart_total_price">
					<div  id="primerLabel">{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</div>
					<div class="price" id="total_product">{displayPrice price=$total_products}</div>
				</div>
			{else}
				<div class="cart_total_price">
					<div  id="primerLabel">{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</div>
					<div  class="price" id="total_product">{displayPrice price=$total_products_wt}</div>
				</div>
			{/if}
		{else}
			<div class="cart_total_price">
				<div  id="primerLabel">{l s='Total products:'}</div>
				<div  class="price" id="total_product">{displayPrice price=$total_products}</div>
			</div>
		{/if}
                
                <!-- Total Apoyo a la salud -->
			<div class="cart_total_voucher" {if $total_discounts == 0}style="display:none"{/if}>
				<div  id="descuentoValor">
				{if $use_taxes && $display_tax_label}
					{l s='Total vouchers (tax excl.):'}
				{else}
					{l s='Total vouchers:'}
				{/if}
				</div>
				<div  style="margin-top: -16px; color:#676767; text-align: right;" class="price-discount price" id="total_discount">
				{if $use_taxes && !$priceDisplay}
					{assign var='total_discounts_negative' value=$total_discounts * -1}
				{else}
					{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
				{/if}
				{displayPrice price=$total_discounts_negative}
				</div>
			</div>
                       
			<div class="cart_total_voucher" {if $total_wrapping == 0}style="display: none;"{/if}>
				<div  style="margin-top: -16px; color:#676767; text-align: right;">
				{if $use_taxes}
					{if $display_tax_label}{l s='Total gift-wrapping (tax incl.):'}{else}{l s='Total gift-wrapping:'}{/if}
				{else}
					{l s='Total gift-wrapping:'}
				{/if}
				</div>
				<div  style="margin-top: -16px; color:#676767; text-align: right;" class="price-discount price" id="total_wrapping">
				{if $use_taxes}
					{if $priceDisplay}
						{displayPrice price=$total_wrapping_tax_exc}
					{else}
						{displayPrice price=$total_wrapping}
					{/if}
				{else}
					{displayPrice price=$total_wrapping_tax_exc}
				{/if}
				</div>
			</div>
                        
                        <!-- total envio -->
                        
			{if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
				<div class="cart_total_delivery" style="{if !isset($carrier->id) || is_null($carrier->id)}display:none;{/if} padding: 7px 10px; color:#676767;">
                                    <div  style="text-align: right;">{l s='Shipping'}</div>
					<div  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_shipping">{l s='Free Shipping!'}</div>
				</div> 
			{else}
				{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
					{if $priceDisplay}
						<div class="cart_total_delivery" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
							<div  style="padding: 7px 10px; color:#676767; text-align: right;">{if $display_tax_label}{l s='Total shipping (tax excl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</div>
						</div>
					{else}
						<div class="cart_total_delivery"{if $total_shipping <= 0} style="display:none;"{/if}>
							<div  style="padding: 7px 10px; color:#676767; text-align: right;">{if $display_tax_label}{l s='Total shipping (tax incl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div  style="padding: 7px 10px; color:#676767;  text-align: right;" class="price" id="total_shipping" >{displayPrice price=$total_shipping}</div>
						</div>
					{/if}
				{else}
					<div class="cart_total_delivery"{if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
						<div  style="padding: 7px 10px; color:#676767; text-align: right;">{l s='Total shipping:'}</div>
						<div  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_shipping" >{displayPrice price=$total_shipping_tax_exc}</div>
					</div>
				{/if}
			{/if}
                        
                        
                        <!-- total sin IVA -->
                        
			{if $use_taxes}
			<div class="cart_total_price">
				<div  id="primerLabel">{l s='Total (tax excl.):'}</div>
				<div  class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</div>
			</div>
			<div class="cart_total_tax">
				<div  id="primerLabel">{l s='Total tax:'}</div>
				<div  class="price" id="total_tax">{displayPrice price=$total_tax}</div>
			</div>
			{/if}
                        
                        <!-- Total compra  -->
			<div class="cart_total_price total" >				
				{if $use_taxes}
				<div  id="primerLabel">{l s='Total:'}</div>
				<div  id="precioContenedor" class="price total_price_container" id="total_price_container">
					<span id="total_price">{displayPrice price=$total_price}</span>
				</div>
				{else}
				<div  style="padding: 7px 10px; color:#009207; width: 44%; text-align: right;" >{l s='Total:'}xd</div>
				<div  style="padding: 7px 10px; color:#676767; width: 55%; text-align: right;" class="price total_price_container" id="total_price_container">
					
					<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
				</div>
				{/if}
			 </div>
            <!-- fin Total -->
           </div> 
           </div>                        
		</div>
		
                
         <!-- cupon apoyo a la salud  -->       
	{if sizeof($discounts)}
		<div>
		{foreach $discounts as $discount}
                <div  class="cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}" style="float:left;margin-top:50px;">
                	<div id="descuentoLabel">
				<div class="cart_discount_name" id="segundoLabel">{$discount.name}</div>
				<div class="cart_discount_price" id="tercerLabel"><span class="price-discount">
					{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
				</span></div>
				<div class="cart_discount_delete" id="cuartoLabel">1</div>
				<div class="cart_discount_price" id="quintoLabel">
					<span class="price-discount price" id="quintoLabel">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
				</div>
				<div class="price_discount_del" id="sextoLabel">
					{if strlen($discount.code)}<a href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}" class="price_discount_delete" title="{l s='Delete'}">{l s='Delete'}</a>{/if}
				</div>
			</div>
		{/foreach}
			</div>
		</div>
	{/if}
        
        
	</div>
</div>
        
        
{if $show_option_allow_separate_package}
<p>
	<input type="checkbox" name="allow_seperated_package" id="allow_seperated_package" {if $cart->allow_seperated_package}checked="checked"{/if} autocomplete="off"/>
	<label for="allow_seperated_package">{l s='Send available products first'}</label>
</p>
{/if}
{if !$opc}
	{if Configuration::get('PS_ALLOW_MULTISHIPPING')}
		<p>
			<input type="checkbox" {if $multi_shipping}checked="checked"{/if} id="enable-multishipping" />
			<label for="enable-multishipping">{l s='I want to specify a delivery address for each individual product.'}</label>
		</p>
	{/if}
{/if}

<div id="HOOK_SHOPPING_CART">{$HOOK_SHOPPING_CART}</div>

{* Define the style if it doesn't exist in the PrestaShop version*}
{* Will be deleted for 1.5 version and more *}
{if !isset($addresses_style)}
	{$addresses_style.company = 'address_company'}
	{$addresses_style.vat_number = 'address_company'}
	{$addresses_style.firstname = 'address_name'}
	{$addresses_style.lastname = 'address_name'}
	{$addresses_style.address1 = 'address_address1'}
	{$addresses_style.address2 = 'address_address2'}
	{$addresses_style.city = 'address_city'}
	{$addresses_style.country = 'address_country'}
	{$addresses_style.phone = 'address_phone'}
	{$addresses_style.phone_mobile = 'address_phone_mobile'}
	{$addresses_style.alias = 'address_title'}
{/if}


<p class="cart_navigation">
	{if !$opc}
		<a id="processCarrier" href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}" class="exclusive standard-checkout" title="{l s='Next'}"> </a>
		{if Configuration::get('PS_ALLOW_MULTISHIPPING')}
			<a href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}&amp;multi-shipping=1" class="multishipping-button multishipping-checkout exclusive" title="{l s='Next'}">{l s='Next'} &raquo;</a>
		{/if}
	{/if}
        <a  id="atras1" href="{if (isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order.php')) || isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order-opc') || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index')}{else}{$smarty.server.HTTP_REFERER|escape:'htmlall':'UTF-8'|secureReferrer}{/if}" class="button_large" title="{l s='Continue shopping'}"> </a>
</p>
	{if !empty($HOOK_SHOPPING_CART_EXTRA)}
		<div class="clear"></div>
		<div class="cart_navigation_extra">
			<div id="HOOK_SHOPPING_CART_EXTRA">{$HOOK_SHOPPING_CART_EXTRA}</div>
		</div>
	{/if}
{/if}



{literal}
<script type="text/javascript">


	$(document).ready(function() {

			function setUserID(myValue) {
			     $('#doc_fnd').val(myValue).trigger('change');
			}

		$('#discount_name').change(function(){
			$('#doc_fnd').val('');

			if ($('input:radio[name=type_voucher]:checked').val() == 'cupon') {
				$('#submitAddDiscount').prop( "disabled", false );
			} else {
				$('#submitAddDiscount').prop( "disabled", true );
			}
		});

		$('#doc_fnd').change(function(){
			//alert("cambio id doc");
			if ($('#doc_fnd').val().length === 0) {
				//alert("medico vacio");
				$('#submitAddDiscount').prop( "disabled", true );
			}
			else {
				//alert("medico si");
				$('#submitAddDiscount').prop( "disabled", false );
				
			}
		});

	    $('input[type=radio][name=type_voucher]').change(function() {
	    	
			$('#discount_name').val('');
			$('#doc_fnd').val('');

	        if (this.value == 'md') {
	        		$('#submitAddDiscount').prop( "disabled", true );
	        		var options = {
					script:"lisme.php?",
					varname:"input",
					json:true,
					shownoresults:true,
					maxresults:10,
					timeout:7500, 
					delay:0,
					callback: function (obj) { setUserID(obj.id); /*document.getElementById('doc_fnd').value = obj.id; */ }
				};

	            var as_json = new bsn.AutoSuggest('discount_name', options);	
	        }
	        else if (this.value == 'cupon') {
	        		$('#submitAddDiscount').prop( "disabled", false );
	        		var options = {
	        		minchars:555, 
	        		meth:"post", 
					script:"lisme.php?",
					varname:"service",
					json:true,
					shownoresults:true,
					maxresults:0,
					timeout:0, 
					delay:0,
					maxheight: 0, 
					cache: false, 
					maxentries: 0,
				};			
				$("#discount_name").css({"autocomplete":"on"});
	           var as_json = new bsn.AutoSuggest('discount_name', options);	
	        }
	    });
});

	
		

</script>
{/literal}