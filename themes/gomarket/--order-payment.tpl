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

<script>

function mouse_overd( objeto ){
	document.getElementById(objeto).style.display = 'block';

}

function mouse_outd( objeto ){
	document.getElementById(objeto).style.display = 'none';

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
background:url({$img_dir}pagar-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}
    
 </style>


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

{if !$opc}
	{assign var='current_step' value='payment'}
	{include file="$tpl_dir./order-steps.tpl"}

	{include file="$tpl_dir./errors.tpl"}
{else}
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
       <div id="order-detail-content" class="table_block"  >
           
           <!-- style="border: solid #0000CC !important;" -->
           
	<table id="cart_summary"  class="std" >
		<thead class="m_hide">
			<tr>
				<th class="cart_product first_item">Selecciona un medio de pago</th>
                               
				<th class="cart_description item"> </th>
				<th class="cart_availability item"></th>
				<th class="cart_unit item"></th>
				<th class="cart_quantity item"></th>
                       
				<th class="cart_total last_item">Resumen</th>
			</tr>
		</thead>
             
                
                <!-- ------------------------------ -->
               
                <!-- ---------------------------------------- -->
               
                
              <!-- productos -->  
		<tbody>
                   
                    
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
					
                                     <tr id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" class="alternate_item cart_item">
					<!-- NPI -->	
                                         <td colspan="4" >
                                             
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
						</td>
						
                                               <!-- eliminar producto --> 
                                               
                                               <!-- NPI -->
                                               
                                                <td class="cart_quantity" >
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
						</td>
                                                
                                                
                                                
                                                
						<td class="cart_total" ></td>
                                                
					</tr>
                                        
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
               
                
<tr>
    <td style="width: 25%;  vertical-align: top;">
<!-- medios de pago -->        
<div id="cart_voucher" class="cart_voucher" style="float: top; display: inline-block;">
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
	{if $opc}<div id="opc_payment_methods-content" >{/if}
		<div id="HOOK_PAYMENT">{$HOOK_PAYMENT}</div>
	{if $opc}</div>{/if}
{else}
	<p class="warning">{l s='No payment modules have been installed.'}</p>
{/if}
<!-- fin medios de pago -->
</td>

<td style="width: 48%;  vertical-align: top;">











<!--- divs formularios -->



<div id="divs" name="divs" >
    
 <!-- formula medica -->   
 <div id="div1" style="float: left;" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
 
     <div class="contendfrom">
	    <div style=" width: auto; height: auto;">
	        <p   class="textapoyo"  style="text-justify: distribute;">Finaliza tu compra para recibir los datos con los que podras acercarte a un punto Baloto y realizar tu pago.</p>
	    </div>

<!--  -->
	</div>
                       
</div>
 <!-- fin formula -->
 

 <!-- Contraentrega -->
<div id="div2" style="display: none; ">
                       
                       
<div class="contendfrom">
 <div style=" width: auto; height: auto; text-justify: auto;">
    {include file="$tpl_dir../../modules/payulatam/tpl/process.tpl"}
 </div>

</div>
                       
                    
       </div>
 <!--Fin Contraentrega --> 

 <div id="div3" style="display: none; background-color:#F0F0F0;">
     <div class="contendfrom">
	    <div style=" width: auto; height: auto; text-justify: auto;">
	        <p  class="textapoyo"></p>
	    </div>

		<!--  -->
		</div>
     
 </div>

 <div id="div4" style="display: none; background-color:#F0F0F0; padding: 10px 10px 10px 10px">
     
     <div class="contendfrom">
	    <div style=" width: auto; height: auto; text-justify: auto;">
	        <p class="textapoyo">Recuerda tener a mano copia de la fórmula médica, en caso de ser necesaria, y tener la cantidad exacta de efectivo para nuestro repartidor.</p>
	    </div>


	<!--  -->
	</div>
     
 </div>
 

</div>




 <!-- Cupon apoyo a la salud -->              
 <div id="cupon" style="margin: 0 auto 0 auto; " style="display: none;">
	{if $voucherAllowed}
		{if isset($errors_discount) && $errors_discount}
			<ul class="error">
			{foreach $errors_discount as $k=>$error}
				<li>{$error|escape:'htmlall':'UTF-8'}</li>
			{/foreach}
			</ul>
		{/if}
		<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
                    
                    <input class="hidden" type="hidden" value="3" name="step" id="step">
			<fieldset>
                            <p ><label for="discount_name" style="color: #399E98; font-size: 16px;"><b>Apoyo Salud</b></label></p><br>
		             <p>
                                 <input style=" width:90%; " type="text" class="discount_name" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
				</p>
				<p class="submit"><input type="hidden" name="submitDiscount" /> </p>
                                 <input type="submit" style="" name="submitAddDiscount" value="{l s='OK'}" class="button" />
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
<!-- fin Cupon apoyo a la salud --> 

</td>
<td colspan="4" style="width: 25%;">
    
    <table>  
        <tbody >
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
        </tbody>
    </table>
    
</td>

</tr>

<tr>
    <td style="width: 25%;">

</td>

<td style="width: 50%;">


</td>
<td colspan="4" style="width: 25%;">
  <table>
             <!-- total Productos -->
		{if $use_taxes}
			{if $priceDisplay}
				<tr class="cart_total_price">
					<td >{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</td>
					<td  class="price" id="total_product">{displayPrice price=$total_products}</td>
				</tr>
			{else}
				<tr class="cart_total_price">
					<td >{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</td>
					<td  class="price" id="total_product">{displayPrice price=$total_products_wt}</td>
				</tr>
			{/if}
		{else}
			<tr class="cart_total_price">
				<td >{l s='Total products:'}</td>
				<td  class="price" id="total_product">{displayPrice price=$total_products}</td>
			</tr>
		{/if}
                
                <!-- Total Apoyo a la salud -->
			<tr class="cart_total_voucher" {if $total_discounts == 0}style="display:none"{/if}>
				<td >
				{if $use_taxes && $display_tax_label}
					{l s='Total vouchers (tax excl.):'}
				{else}
					{l s='Total vouchers:'}
				{/if}
				</td>
				<td  class="price-discount price" id="total_discount">
				{if $use_taxes && !$priceDisplay}
					{assign var='total_discounts_negative' value=$total_discounts * -1}
				{else}
					{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
				{/if}
				{displayPrice price=$total_discounts_negative}
				</td>
			</tr>
                       
			<tr class="cart_total_voucher" {if $total_wrapping == 0}style="display: none;"{/if}>
				<td >
				{if $use_taxes}
					{if $display_tax_label}{l s='Total gift-wrapping (tax incl.):'}{else}{l s='Total gift-wrapping:'}{/if}
				{else}
					{l s='Total gift-wrapping:'}
				{/if}
				</td>
				<td  class="price-discount price" id="total_wrapping">
				{if $use_taxes}
					{if $priceDisplay}
						{displayPrice price=$total_wrapping_tax_exc}
					{else}
						{displayPrice price=$total_wrapping}
					{/if}
				{else}
					{displayPrice price=$total_wrapping_tax_exc}
				{/if}
				</td>
			</tr>
                        
                        <!-- total envio -->
                        
			{if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
				<tr class="cart_total_delivery" style="{if !isset($carrier->id) || is_null($carrier->id)}display:none;{/if}">
					<td >{l s='Shipping'}</td>
					<td  class="price" id="total_shipping">{l s='Free Shipping!'}</td>
				</tr> 
			{else}
				{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
					{if $priceDisplay}
						<tr class="cart_total_delivery" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
							<td >{if $display_tax_label}{l s='Total shipping (tax excl.):'}{else}{l s='Total shipping:'}{/if}</td>
							<td  class="price" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</td>
						</tr>
					{else}
						<tr class="cart_total_delivery"{if $total_shipping <= 0} style="display:none;"{/if}>
							<td >{if $display_tax_label}{l s='Total shipping (tax incl.):'}{else}{l s='Total shipping:'}{/if}</td>
							<td  class="price" id="total_shipping" >{displayPrice price=$total_shipping}</td>
						</tr>
					{/if}
				{else}
					<tr class="cart_total_delivery"{if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
						<td >{l s='Total shipping:'}</td>
						<td  class="price" id="total_shipping" >{displayPrice price=$total_shipping_tax_exc}</td>
					</tr>
				{/if}
			{/if}
                        
                        
                        <!-- total sin IVA -->
                        
			{if $use_taxes}
			<tr class="cart_total_price">
				<td >{l s='Total (tax excl.):'}</td>
				<td  class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</td>
			</tr>
			<tr class="cart_total_tax">
				<td >{l s='Total tax:'}</td>
				<td  class="price" id="total_tax">{displayPrice price=$total_tax}</td>
			</tr>
			{/if}
                        
                        <!-- Total compra  -->
			<tr class="cart_total_price total" >				
				{if $use_taxes}
				<td >{l s='Total:'}</td>
				<td  class="price total_price_container" id="total_price_container">
					<span id="total_price">{displayPrice price=$total_price}</span>
				</td>
				{else}
				<td >{l s='Total:'}</td>
				<td  class="price total_price_container" id="total_price_container">
					
					<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
				</td>
				{/if}
			</tr>
                        <!-- fin Total -->
                        </table>
    
</td>

</tr>



</tbody>

<!-- descuentos -->
	{if count($discounts)}
		<tbody>
		{foreach from=$discounts item=discount name=discountLoop}
			<tr class="cart_discount {if $smarty.foreach.discountLoop.last}last_item{elseif $smarty.foreach.discountLoop.first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
				<td class="cart_discount_name" colspan="2">{$discount.name}  </td>
				<td class="cart_discount_description" colspan="3">{$discount.description}</td>
				<td class="cart_discount_price" >
					<span class="price-discount">
						{if $discount.value_real > 0}
							{if !$priceDisplay}
								{displayPrice price=$discount.value_real*-1}
							{else}
								{displayPrice price=$discount.value_tax_exc*-1}
							{/if}
						{/if}
					</span>
				</td>
			</tr>
		{/foreach}
		</tbody>
	{/if}
        <!-- fin descuentos -->
	</table> 
        
     
</div>
<!-- fin tabla producto -->        
        



{if !$opc}
    <!-- <p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=2")}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a></p> -->
	<p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=1")}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a></p>
{else}
	</div>
{/if}
</div>
