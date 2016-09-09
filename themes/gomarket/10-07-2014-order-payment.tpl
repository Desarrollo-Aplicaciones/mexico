{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the waorld-wide-web at this URL:
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

{if isset($disableBaloto) and $disableBaloto OR (isset($isblockmpb) and $isblockmpb)} 
    {literal}
    <script type="text/javascript">

   </script>  
    {/literal}

 <style type="text/css">
  #imgbaloto,#mediopagob {
  opacity: 0.6;
}

#textradiobaloto
{
color: #979797;

}
 
 </style>

{/if}

{if isset($show_contra_entrega)}
{if !$show_contra_entrega }
    
    <style type="text/css">
  #imgcontrae,#mediopagoce {
  opacity: 0.6;
}

#textradiocontrae
{
color: #979797;

}
 
 </style>
    
 {/if}
{/if}
<script>

function mouse_overd( objeto ){

	    var $divs = $('#divs > div');
        $divs.hide();

        if(objeto == "div1") {
		    document.getElementById("botoncitosubmit").onclick = submitforms1;
		    document.getElementById("botoncitosubmit2").onclick = submitforms1;
		}
		else if(objeto == "div2") {
		    document.getElementById("botoncitosubmit").onclick = submitforms2;
		    document.getElementById("botoncitosubmit2").onclick = submitforms2;
		} else if(objeto == "div3") {
		    document.getElementById("botoncitosubmit").onclick = submitforms3;
		    document.getElementById("botoncitosubmit2").onclick = submitforms3;
		}
		 else if(objeto == "div4") {
		    document.getElementById("botoncitosubmit").onclick = false;
		    document.getElementById("botoncitosubmit2").onclick = false;
		}
       
	document.getElementById(objeto).style.display = 'block';

}

function submitforms1() {
		$( "#formBaloto" ).submit();	
}
function submitforms2() {
		$( "#formPayU" ).submit();	
}
function submitforms3() {
		$( "#formPayUPse" ).submit();	
}

</script>





<script>
    function validar_texto(e){
    tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
    if ((tecla==8)||(tecla==0)){
        return true;
    }
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
    }
</script>
<script>
  function pulsar(e) {
  tecla = (document.all) ? e.keyCode :e.which;
  return (tecla!=13);
  }
  
  
</script>


<style type="text/css">

#order-detail-content5{
	margin: -158px 0;

}
#productoTamano{
	width: 40%;
float: left;height: 111px;

}
#textoseguridad{
float: left;
margin-left: 48px;
clear: both;
}
#imagenseguridad{
	margin-left: 29px;margin-top: 21px;
}
.botoncitosubmit1{
padding: 0 0; 
margin: 7px 0px 0px 0px;
width:145px;
height:40px;
border:none;
float: right;
background:url({$img_dir}pagar-normal.png)no-repeat top center !important;
z-index: 1;


}
.botoncitosubmit1:hover{
padding: 0 0; 
margin: 7px 0px 0px 0px;
width:145px;
height:40px;
border:none;
float: right;
background:url({$img_dir}pagar-hover.png)no-repeat top center !important;
z-index: 1;


}.botoncitosubmit2{
padding: 0 0; 
margin: 7px 0px 0px 0px;
width:145px;
height:40px;
border:none;
float: right;
background:url({$img_dir}pagar-normal.png)no-repeat top center !important;position: relative;

}
.botoncitosubmit2:hover{
padding: 0 0; 
margin: 7px 0px 0px 0px;
width:145px;
height:40px;
border:none;
float: right;
background:url({$img_dir}pagar-hover.png)no-repeat top center !important;position: relative;



}
#botonesuperiores{
position: relative;
top: -90px;

}
#botonesInferiores{
float: left;
margin-top: -81px;
width: 100%;
}


#navegarAtras{
padding: 0 0; 
margin: 7px 7px 7px 7px;
width:149px;
height:42px;
border:none;
float: left;
background: url({$img_dir}formula-medica/btn-anterior.png)no-repeat top center !important;

}
#navegarAtras:hover{
padding: 0 0; 
margin: 7px 7px 7px 7px;
width:149px;
height:42px;
border:none;
float: left;
background: url({$img_dir}formula-medica/btn-anterior.png)no-repeat top center !important;
}

#navegarAtras2{
padding: 0 0; 
margin: 7px 0px 0px 0px;
width:149px;
height:42px;
border:none;
float: left;
background: url({$img_dir}formula-medica/btn-anterior.png)no-repeat top center !important;
z-index: 1;
}

#navegarAtras2:hover{
padding: 0 0; 
margin: 7px 0px 0px 0px;
width:149px;
height:42px;
border:none;
float: left;
background: url({$img_dir}formula-medica/btn-anterior.png)no-repeat top center !important;
z-index: 1;

}
</style>
{literal}
<style type="text/css">
    
select,input { 

	border-top:1px solid #acbd5a;
	border-left:1px solid #acbd5a;
	border-bottom:1px solid #6e7f3d;
	border-right:1px solid #6e7f3d;
}

input#submit{
	width:115px;
	height:25px;
	background:url(./img/flecha3.png) #a6bf52 no-repeat 3px 9px;
	padding:2px 0px 3px 24px;
	color:#fff;
	text-align:left;
	margin-left:237px;
	border:none;

	}
	input#submit:focus{
		background:url(./img/flecha3.png) #03b0f4 no-repeat 3px 9px;
	}

        
.contend-form{
 margin: 7px 7px 7px 7px;   
 display: inline-block;
 width: 97%;        
        }
        
  .ui-datepicker-calendar {
    display: none;
    }  
    
#payuSubmit:hover
{
padding: 0 0; 
margin: 7px 7px 7px 7px;
width:145px;
height:40px;
border:none;
background:url({/literal}{$img_dir}{literal}pagar-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}
#sele{margin-left: 22px;position: absolute;top: 63px;}
#seleccionPago{
font-weight: bold;
font-size: 14pt;
font-family: open sans;
color:	#646464 ;
background:url(http://127.0.0.1/test.farmalisto.com.co/themes/gomarket/img/mediosp/mediop.jpg) scroll left top rgba(0, 0, 0, 0); background-repeat: no-repeat;
 height: 46px;width: 76%;margin-left: 0px;
}
.resumen{
background: url(http://test.farmalisto.com.co/themes/gomarket/img/mediosp/resumen.jpg) repeat scroll left top rgba(0, 0, 0, 0);
background-repeat: no-repeat !important;
margin-left: 759px;
height: 70px;
margin-top: -46px;
}
#resumen{
width: 85%;

}
    
#mediospago{
	width: 71%;
position: relative;
top: 148px;
}

.contenedorGris{
	float:left;
border-style: solid;
border-width: 1px;
border-radius: 3px;
border-color: #D8D8D8;
color: #999595;
height: auto;
width: 747px;
}
#pepe{
	float: left;
}

	#botonArriba{
		position: absolute;
top: 0;
	}

	#botonAbajo{
		position: absolute;
top: 0;}

#ResumenLabel{
color: #1E807A ;
font-weight: bold;
font-size: 14pt;
font-family: open sans;
margin-left: 85px;
top: 63px;
position: absolute;
}
#uls{
	position: relative;
top: -77px;
clear: both;
}
#tablaResumen2{
	position: relative;
left: -4px;	
}
#contenedor_medios{float: left;  vertical-align: top; width:67%;}

</style>{/literal}

{if !$opc}
	<script type="text/javascript">
	// <![CDATA[
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='product' js=1}";
	var txtProducts = "{l s='products' js=1}";
	// ]]>
	</script>

	
{/if}

{if !$opc}<h1 style="color:#979797; font-size:24px; font-family:verdana;">Modos de pago</h1>{/if}
<div id="botonesuperiores">
<p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=1")}" id="navegarAtras2" title="{l s='Previous'}" ></a></p>
<input type="button"  name="botoncitosubmit" id="botoncitosubmit" class="botoncitosubmit1"></div>
<div id="uls">{if !$opc}
	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	{include file="$tpl_dir./errors.tpl"}
{else}
</div>
	<div id="opc_payment_methods" class="opc-main-block">
		<h2><span>3</span> {l s='Choose your payment method'}</h2>
<div id="opc_payment_methods-overlay" class="opc-overlay" style="display: none;"></div>
{/if}

<!-- contenedor  -->
<div class="paiement_block">

    <div id="HOOK_TOP_PAYMENT" >{$HOOK_TOP_PAYMENT}</div>

{if $HOOK_PAYMENT}
	{if !$opc}
            
            
            
       <!-- tabla resumen producto -->
       <div id="order-detail-content5" class="table_block"  >
           
           <!-- style="border: solid #0000CC !important;" -->

        





	<div id="cart_summary"  class="std" >
	
		<div >
			<div>
				   <div class="cart_product first_item"></div>
                               
				<div class="cart_description item"> </div>
				<div class="cart_availability item"></div>
				<div class="cart_unit item"></div>
				<div class="cart_quantity item"></div>
                       
				<div class="cart_total last_item"></div>
			</div>
		</div>
             

                <!-- ------------------------------ -->
               
                <!-- ---------------------------------------- -->
               
                
              <!-- productos --> 
		<div id="contenedor_total" style="margin-top:176px;">
                   
                    
                        {foreach from=$products item=product name=productLoop}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{assign var='cannotModify' value=1}
			{assign var='odd' value=$product@iteration%2}
			{assign var='noDeleteButton' value=1}
			{* Display the product line *}
                        
                        <!-- Imprime productos -->
			{*include file="$tpl_dir./shopping-cart-product-line-formula.tpl"*}
                        
			{* Then the customized datas ones*}
			{if isset($customizedDatas.$productId.$productAttributeId)}
				{foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization'}
					
					
                                     <div id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" class="alternate_item cart_item">
					<!-- NPI -->	
                                         <div>
                                             
							{foreach from=$customization.datas key='type' item='datas'}
								{if $type == $CUSTOMIZE_FILE}
                                                                    <div class="customizationUploaded"  >
										<ul class="customizationUploaded">
											{foreach from=$datas item='picture'}
												<li>
											<img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" />
												</li>
											{/foreach}
										</ul>
									</div>
								{elseif $type == $CUSTOMIZE_TEXTFIELD}
									<ul class="typedText">
										{foreach from=$datas item='textField' name='typedText'}
											<li>
												{if $textField.name}
													{l s='%s:' sprintf=$textField.name}
												{else}
													{l s='Text #%s:' sprintf=$smarty.foreach.typedText.index+1}
												{/if}
												{$textField.value}
											</li>
										{/foreach}
									</ul>
								{/if}
							{/foreach}
						</div>
						
                                               <!-- eliminar producto --> 
                                             
                                               <!-- NPI -->
                                               
                                                <div class="cart_quantity" >
							{if isset($cannotModify) AND $cannotModify == 1}
								<span style="float:left">{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
							{else}
								<div style="float:right">
									<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}"><img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" title="{l s='Delete this customization'}" width="11" height="13" class="icon" /></a>
								</div>
								<div id="cart_quantity_button" style="float:left">
								<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="14" height="9" /></a><br />
								{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
								<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract'}">
								 <img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
								</a>
								{else}
								<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="#" title="{l s='Subtract'}">
									<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
								</a>
								{/if}
								</div>
								<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_hidden"/>
								<input size="2" type="text" value="{$customization.quantity}" class="cart_quantity_input" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}"/>
							{/if}
						</div>
                                                
                                                
                                                
                                                
						<div class="cart_total" ></div>
                                                
					</div>
                                        
					{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
				{/foreach}
				{* If it exists also some uncustomized products *}
				{if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl"}{/if}
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
                     
			{include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
		{/foreach}

			<div id="seleccionPago" >
				<label id="sele" >Selecciona un Medio de pago</label>
			</div>
	<div  class="resumen">
				    <label id="ResumenLabel">Resúmen</label>
	</div>
               
	<div id="contenedor_medios" >
			<!-- medios de pago -->        
			<div id="cart_voucher" class="cart_voucher" style="float: top; display: none;">
			{if $voucherAllowed}
				{if isset($errors_discount) && $errors_discount}
					<ul class="error">
					{foreach from=$errors_discount key=k item=error}
						<li>{$error|escape:'htmlall':'UTF-8'}</li>
					{/foreach}
					</ul>
				{/if}
			{/if}
	</div>



{/if}
<div class="contenedorGris">
		{if $opc}<div id="opc_payment_methods-content" >{/if}
		<div id="HOOK_PAYMENT" style="padding: 0 0 0 14px;float: left;">{literal}<style type="text/css">
			#HOOK_PAYMENT div div{float: left;}
		</style>{/literal}{$HOOK_PAYMENT}</div>
	{if $opc}</div>{/if}
{else}
	<p class="warning">{l s='No payment modules have been installed.'}</p>
{/if}
<!-- fin medios de pago -->
</div>


</div>


<div id="resumen_total">
<div id="contenedorProducto" >



        <div id="pepe" >

        	
 {foreach from=$products item=product name=productLoop}
                    {assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{assign var='cannotModify' value=1}
			{assign var='odd' value=$product@iteration%2}
			{assign var='noDeleteButton' value=1}
			{* Display the product line *}
                        
                        <!-- Imprime productos -->
			{include file="$tpl_dir./shopping-cart-product-line-formula.tpl"}
       {/foreach}
        </div>

        <div  id="contenedorPrecio">

             <!-- total Productos -->
		{if $use_taxes}
			{if $priceDisplay}
				<div class="cart_total_price" style="display: table-cell;float: left;width: 250px;text-align: right;">
					<div style="display: table-cell;float: left;width: 140px;text-align: right;font-size: 8pt;">{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</div>
					<div  class="price" id="total_product" style="text-align: right;font-size: 8pt;margin-right: 10px;">{displayPrice price=$total_products}</div>
				</div>
			{else}
				<div class="cart_total_price"  style="display: table-cell;float: left;width: 250px;text-align: right;">
					<div style="display: table-cell;float: left;width: 140px;text-align: right;font-size: 8pt;">{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</div>
					<div  class="price" id="total_product" style="text-align: right;font-size: 8pt;margin-right: 10px;">{displayPrice price=$total_products_wt}</div>
				</div>
			{/if}
		{else}
			<div class="cart_total_price"  style="display: table-cell;float: left;width: 250px;text-align: right;">
				<div style="display: table-cell;float: left;width: 140px;text-align: right;font-size: 8pt;">{l s='Total products:'}</div>
				<div  class="price" id="total_product" style="text-align: right;margin-right: -6px;font-size: 8pt;margin-right: 10px;">{displayPrice price=$total_products}</div>
			</div>
		{/if}
               
               <!-- descuentos -->
	{if count($discounts)}
		<div style="width: 240px;float: left;">
		{foreach from=$discounts item=discount name=discountLoop}
			<div class="cart_discount {if $smarty.foreach.discountLoop.last}last_item{elseif $smarty.foreach.discountLoop.first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
				<div class="cart_discount_description" style="float: left;width: 140px;text-align: right;font-size: 8pt;">{$discount.description}</div>
				<div class="cart_discount_price" style="text-align: right;margin-right: -6px;">
					<span class="price-discount" style="color:#009207!important;font-size: 8pt;">
						{if $discount.value_real > 0}
							{if !$priceDisplay}
								{displayPrice price=$discount.value_real*-1}
							{else}
								{displayPrice price=$discount.value_tax_exc*-1}
							{/if}
						{/if}
					</span>
				</div>
			</div>
		{/foreach}
		</div>
	{/if}
        <!-- fin descuentos --><br>

                <!-- Total Apoyo a la salud -->
			<div class="cart_total_voucher" style="float: left;width: 240px;"{if $total_discounts == 0}style="display:none"{/if}>
				<div style="width: 140px;float: left;text-align: right;font-size: 8pt;">
				{if $use_taxes && $display_tax_label}
					{l s='Total vouchers (tax excl.):'}
				{else}
					{l s='Total vouchers:'}
				{/if}
				</div>
				<div  class="price-discount price" id="total_discount" style="text-align: right;margin-right: 0px;font-size: 8pt;">
				{if $use_taxes && !$priceDisplay}
					{assign var='total_discounts_negative' value=$total_discounts * -1}
				{else}
					{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
				{/if}
				{displayPrice price=$total_discounts_negative}
				</div>
			</div>
                       
			<div class="cart_total_voucher" style="float: left;width: 240px;font-size: 8pt;"{if $total_wrapping == 0}style="display: none;"{/if}>
				<div style="width: 140px;float: left;text-align: right;font-size: 8pt;">
				{if $use_taxes}
					{if $display_tax_label}{l s='Total gift-wrapping (tax incl.):'}{else}{l s='Total gift-wrapping:'}{/if}
				{else}
					{l s='Total gift-wrapping:'}
				{/if}
				</div>
				<div  class="price-discount price" id="total_wrapping" style="text-align: right;font-size: 8pt;">
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
				<div class="cart_total_delivery" style="float: left;width: 240px;font-size: 8pt;{if !isset($carrier->id) || is_null($carrier->id)}display:none;{/if}">
					<div style="float: left;width: 140px;text-align: right;font-size: 8pt;" >{l s='Shipping'}</div>
					<div  style="text-align: right;margin-right: 0px;width: 47px;font-size: 8pt;" class="price" id="total_shipping">{l s='Free Shipping!'}</div>
				</div> 
			{else}
				{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
					{if $priceDisplay}
						<div class="cart_total_delivery" style="float: left;width: 240px;"{if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
							<div style="float: left;width: 140px;text-align: right;font-size: 8pt;">{if $display_tax_label}{l s='Total shipping (tax excl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div  style="text-align: right;margin-right: 0px;width: 47px;font-size: 8pt;"class="price" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</div>
						</div>
					{else}
						<div class="cart_total_delivery" style="float: left;width: 240px;"{if $total_shipping <= 0} style="display:none;"{/if}>
							<div style="float: left;width: 140px;text-align: right;font-size: 8pt;">{if $display_tax_label}{l s='Total shipping (tax incl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div  style="text-align: right;margin-right: 0px;width: 47px;font-size: 8pt;" class="price" id="total_shipping" >{displayPrice price=$total_shipping}</div>
						</div>
					{/if}
				{else}
					<div class="cart_total_delivery" style="float: left;width: 240px;" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
						<div style="float: left;width: 140px;text-align: right;font-size: 8pt;" >{l s='Total shipping:'}</div>
						<div  style="text-align:right;font-size: 8pt;" class="price" id="total_shipping" >{displayPrice price=$total_shipping_tax_exc}</div>
					</div>
				{/if}
			{/if}
                        
                        
                        <!-- total sin IVA -->
                        
			{if $use_taxes}
			<div class="cart_total_price" style="float: left;width: 240px;" >
				<div style="float: left;width: 140px;text-align: right;font-size: 8pt;" >{l s='Total (tax excl.):'}</div>
				<div   style="text-align:right;font-size: 8pt;"  class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</div>
			</div>
			<div class="cart_total_tax" style="float: left;width: 240px;">
				<div style="float: left;width: 140px;text-align: right;font-size: 8pt;" >{l s='Total tax:'}</div>
				<div  style="text-align:right;font-size: 8pt;" class="price" id="total_tax">{displayPrice price=$total_tax}</div>
			</div>
			{/if}
                        
                        <!-- Total compra  -->
			<div class="cart_total_price total" style="float: left;width: 240px;height: 57px;background-color:#fafafa" >				
				{if $use_taxes}
				<div style="float: left;width: 140px;text-align: right;font-size: 13pt;font-weight: 700;margin-top: 21px;" >{l s='Total:'}</div>
				<div  style="text-align:right;font-size: 13pt;margin-top: 21px;" class="price total_price_container" id="total_price_container">
					<span id="total_price">{displayPrice price=$total_price}</span>
				</div>
				{else}
				<div  style="float: left;width: 140px;text-align: right;font-size: 13pt;margin-top: 21px;" >{l s='Total:'}</div>
				<div   style="text-align:right;font-size: 13pt;margin-top: 21px;" class="price total_price_container" id="total_price_container">
					
					<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
				</div>
				{/if}
				<hr style="width: 236px;margin-left: 0px;margin-top: 20px;">
			</div>
         </div>
 </div>


      </div>
</div>
</div>
</div>
</div>
<!-- fin tabla producto -->        
        
                <!--texto seguridad+imagen-->
 <div id="textoseguridad">
 	 					<ul>
 	                     <li style="height: 54px;"><div id="imagenseguridad"><img src="{$img_dir}authentication/g644.png" /></div></li>
                        </ul>
                            <div ALIGN=center style="display:block; font-size:11px; width: 224px; height: 35px;margin-top: 38px;margin-left: -35px;">Realiza tu compra con tranquilidad, contamos con certificación de seguridad.</div>
                            <div style="display:block; font-size:7pt; width: 120px; height: 27px;display:none;">* <b>Absoluta</b> discreción</div>
                            <div style="display:block; font-size:7pt; width: 120px; height: 35px;display:none;">* Mejor precio <span style="color:#b7689e">Garantizado*</span></div>
 </div>
<!--fin texto seguridad+imagen-->


{if !$opc}
    <!-- <p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=2")}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a></p> -->

	<div id="botonesInferiores">
		<p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=1")}" id="navegarAtras" title="{l s='Previous'}" ></a></p>
		<input type="button"  name="botoncitosubmit2" id="botoncitosubmit2" class="botoncitosubmit2">
	</div>

{else}
	</div>
{/if}
</div>