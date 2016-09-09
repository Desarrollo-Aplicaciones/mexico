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
<link rel="stylesheet" href="{$css_dir}order-payment.css" type="text/css" media="screen" charset="utf-8" />

{if isset($disableBaloto) and $disableBaloto OR (isset($isblockmpb) and $isblockmpb)} 


 <style type="text/css">
  #imgbaloto,#mediopagob, #imgEfecty, #mediopagoe {
  opacity: 0.6;
}

#textradiobaloto,#textradioefecty
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
  var es_div = objeto.substring(0, 3); 
  var $divs = $('#divs > div');
      $divs.hide();
      

        if(objeto === "OXXO") {
		    document.getElementById("botoncitosubmit").onclick = submitforms1;
		    document.getElementById("botoncitosubmit2").onclick = submitforms1;
		}
		else if(objeto === "div2") {
		    document.getElementById("botoncitosubmit").onclick = submitforms2;
		    document.getElementById("botoncitosubmit2").onclick = submitforms2;
		} else if(objeto === "IXE") {
		    document.getElementById("botoncitosubmit").onclick = submitforms3;
		    document.getElementById("botoncitosubmit2").onclick = submitforms3;
		}
		 else if(objeto === "div4") {
		    document.getElementById("botoncitosubmit").onclick = false;
		    document.getElementById("botoncitosubmit2").onclick = false;
		}
                 else if(objeto === "BANCOMER") {
		    document.getElementById("botoncitosubmit").onclick = submitforms5;
		    document.getElementById("botoncitosubmit2").onclick = submitforms5;
		}
             else if(objeto === "SANTANDER") {
		    document.getElementById("botoncitosubmit").onclick = submitforms6;
		    document.getElementById("botoncitosubmit2").onclick = submitforms6;
		}
                else if(objeto === "7ELEVEN") {
		    document.getElementById("botoncitosubmit").onclick = submitforms7;
		    document.getElementById("botoncitosubmit2").onclick = submitforms7;
		}
                else if(objeto === "SCOTIABANK") {
		    document.getElementById("botoncitosubmit").onclick = submitforms8;
            document.getElementById("botoncitosubmit2").onclick = submitforms8;
    }

  
            if (es_div === 'div') {
    document.getElementById(objeto).style.display = 'block';
    }
    else
    {
        document.getElementById("div9").style.display = 'block';
    }


    }

function submitforms1() {
		$( "#formOxxo" ).submit();	
}
function submitforms2() {
		$( "#formPayU" ).submit();	
}
function submitforms3() {
		$( "#formIxe" ).submit();	
}

function submitforms5() {
		$( "#formBancomer" ).submit();	
}

function submitforms6() {
		$( "#formSantander" ).submit();	
}
function submitforms7() {
		$( "#formSEleven" ).submit();	
}
function submitforms8() {
		$( "#formScotiabanck" ).submit();	
}

function change_mp_efectivo( obj )
{
    if($(obj).val()!==""){        
    
    $("#text_mediop").html(' <p style="text-justify: distribute;">Finaliza tu compra para recibir los datos con los que podras acercarte a un punto <b>'+$(obj).val()+' </b>  y realizar tu pago.</p>');
       
            mouse_overd($(obj).val());
        }else{
  		    document.getElementById("botoncitosubmit").onclick = false;
		    document.getElementById("botoncitosubmit2").onclick = false;          
            $("#text_mediop").html('');
        }
}

function mouse_overd_efectivo( objeto ){

   
      if (objeto === "div9") {        


        if ($("#depostito_efectivo").val() === "" )
     
    {
		    document.getElementById("botoncitosubmit").onclick = false;
		    document.getElementById("botoncitosubmit2").onclick = false;
                    mouse_overd("div9");

    }
    else
    {
      mouse_overd($("#depostito_efectivo").val());
          
     }
    
      }
  
   
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
    
      function pulsar(e) {
  tecla = (document.all) ? e.keyCode :e.which;
  return (tecla!=13);
  }
</script>



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

{if !$opc}<div class="titulo-pasos">Modos de pago</div>{/if}
<div class="botones">
	<a href="{$link->getPageLink('order', true, NULL, "step=1")}" id="navegarAtras2" title="{l s='Previous'}" >
	<< Anterior</a>
	<input type="button"  name="botoncitosubmit" id="botoncitosubmit" class="botoncitosubmit1" value="Pago >>">
</div>
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
	

             

                <!-- ------------------------------ -->
               
                <!-- ---------------------------------------- -->
               
                
              <!-- productos --> 
		<div id="contenedor_total">
                   
                    
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
                
             
                
                



<!-- medios de pago -->
<div class="columna1" style="float:left">
	<div class="seleccionPago" >
		<label id="sele" >Selecciona un Medio de pago</label>
	</div>

	<div id="contenedor_medios" >

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
				<div id="HOOK_PAYMENT">{literal}<style type="text/css">
					#HOOK_PAYMENT div div{float: left;}
				</style>{/literal}{$HOOK_PAYMENT}</div>
				{if $opc}</div>{/if}
				{else}
				<p class="warning">{l s='No payment modules have been installed.'}</p>
		{/if}
			</div>

	</div>
</div>
<!-- fin medios de pago -->
<!-- Resumen -->
<style type="text/css">
{if ($products|count+$gift_products|count)<5} 
.detalle
{

width:42%;
height:80px;
padding:10px;
min-width: 162px;
}
.price
{
font-size: 14px;
}

{else}
.detalle
{

width: 108px;
height: 134px;
padding: 3px;
}
.nombre
{
clear:left;
}
.labels
{
font-size:11px !important;
font-weight: 600;
}
.subtotal
{
width:100%;
text-align: left
}

.nombre a
{
font-size:11px !important;
}
{/if}
</style>
<div class="columna2" style="float:right">
<div  class="resumen">Resumen</div>

<div id="contenedorProducto" >

        <div id="productos" >




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

<!-------------------------- productos de regalo ------------------------------>
		{assign var='last_was_odd' value=$product@iteration%2}
		{foreach $gift_products as $product}
			{assign var='productId' value=$product.id_product}
			{assign var='productAttributeId' value=$product.id_product_attribute}
			{assign var='quantityDisplayed' value=0}
			{assign var='odd' value=($product@iteration+$last_was_odd)%2}
			{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
			{assign var='cannotModify' value=1}
			{* Display the gift product line *}
                     
			{include file="$tpl_dir./shopping-cart-product-line-formula.tpl" productLast=$product@last productFirst=$product@first}
		{/foreach}


        </div>
<div style="display:table-row">
<!--texto seguridad+imagen-->
<div id="textoseguridad">
	<div id="imagenseguridad"><img src="{$img_dir}authentication/g644.png" /></div>
	<div>Realiza tu compra con tranquilidad, contamos con certificación de seguridad.</div>
	<div style="display:block; font-size:7pt;display:none;">* <b>Absoluta</b> discreción</div>
	<div style="display:block; font-size:7pt;display:none;">* Mejor precio <span style="color:#b7689e">Garantizado*</span></div>
</div>
<!--fin texto seguridad+imagen-->



        <div  id="contenedorPrecio">

             <!-- total Productos -->
		{if $use_taxes}
			{if $priceDisplay}
				<div class="cart_total_price">
					<div class="descripcion">{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</div>
					<div class="price2" id="total_product">{displayPrice price=$total_products}</div>
				</div>
			{else}
				<div class="cart_total_price">
					<div class="descripcion">{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</div>
					<div class="price2" id="total_product">{displayPrice price=$total_products_wt}</div>
				</div>
			{/if}
		{else}
			<div class="cart_total_price">
				<div class="descripcion">{l s='Total products:'}</div>
				<div class="price2" id="total_product">{displayPrice price=$total_products}</div>
			</div>
		{/if}
               
               <!-- descuentos -->
	{if count($discounts)}
		<div class="cart_total_price">
		{foreach from=$discounts item=discount name=discountLoop}
			<div class="cart_discount {if $smarty.foreach.discountLoop.last}last_item{elseif $smarty.foreach.discountLoop.first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
				<div class="descripcion">{$discount.description}:</div>
					<div class="price2" id="descuentos">
						{if $discount.value_real > 0}
							{if !$priceDisplay}
								{displayPrice price=$discount.value_real*-1}
							{else}
								{displayPrice price=$discount.value_tax_exc*-1}
							{/if}
						{/if}
					</div>
			</div>
		{/foreach}
		</div>
	{/if}
        <!-- fin descuentos -->

            
                        
                        <!-- total envio -->
                        
			{if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
				<div class="cart_total_price" style="{if !isset($carrier->id) || is_null($carrier->id)}display:none;{/if}">
					<div class="descripcion">{l s='Shipping:'}</div>
					<div class="price2" id="total_shipping">{l s='Free Shipping!'}</div>
				</div> 
			{else}
				{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
					{if $priceDisplay}
						<<div class="cart_total_price" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
							<div class="descripcion">{if $display_tax_label}{l s='Total shipping (tax excl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div class="price2" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</div>
						</div>
					{else}
						<div class="cart_total_price" {if $total_shipping <= 0} style="display:none;"{/if}>
							<div class="descripcion">{if $display_tax_label}{l s='Total shipping (tax incl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div class="price2" id="total_shipping" >{displayPrice price=$total_shipping}</div>
						</div>
					{/if}
				{else}
					<div class="cart_total_price" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
						<div class="descripcion">{l s='Total shipping:'}</div>
						<div class="price2" id="total_shipping" >{displayPrice price=$total_shipping_tax_exc}</div>
					</div>
				{/if}
			{/if}
                        
                        
                        <!-- total sin IVA -->
                        
			{if $use_taxes}
			<div class="cart_total_price">
				<div class="descripcion">{l s='Total (tax excl.):'}</div>
				<div class="price2" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</div>
			</div>
			<div class="cart_total_price">
				<div class="descripcion">{l s='Total tax:'}</div>
				<div class="price2" id="total_tax">{displayPrice price=$total_tax}</div>
			</div>
			{/if}
                        
        <!-- Total compra  -->
		<div class="total_span">				
			{if $use_taxes}
			<div class="total_compra">{l s='Total:'}</div>
			<div class="price3" id="total_price_container">
				<span id="total_price">{displayPrice price=$total_price}</span>
			</div>
			{else}
			<div class="total_compra">{l s='Total:'}</div>
			<div class="price3" id="total_price_container">
				<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
			</div>
			{/if}
		</div>
	</div>
<!-- fin contenedor precio -->
</div>
</div>
</div>


</div>
</div>
</div>
</div>
<!-- fin tabla producto -->        
        



{if !$opc}
    <!-- <p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=2")}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a></p> -->

	<div id="botonesInferiores">
		<a href="{$link->getPageLink('order', true, NULL, "step=1")}" id="navegarAtras" title="{l s='Previous'}" >
		<< Anterior</a>
		<input type="button"  name="botoncitosubmit2" id="botoncitosubmit2" class="botoncitosubmit2" value="Pago >>">
	</div>

{else}
	</div>
{/if}
</div>