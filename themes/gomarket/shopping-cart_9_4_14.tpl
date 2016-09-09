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
  width: 30%;
  height: auto;
  margin: 3px 3px;
  padding: 5px 3px;
  float: left;
  min-width: 120px;
} 


#boxmedisp
{
    
  width: 30%;
  height: auto;
  margin: 3px 3px;
  padding: 5px 3px;
  float: left;
  min-width: 200px;
} 

#boxnefi
{
    
  width: 30%;
  height: auto;
  margin: 3px 3px;
  padding: 5px 3px;
  float: left;
  min-width: 200px;
} 

#imgenvio
{
width:30px;
height: 15px;
border:none;
background:url({$img_dir}/mediosp/g644.png)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/g648.png)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/g652.png)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/amex.jpg)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/visa.jpg)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/master.jpg)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/diners.jpg)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/pse.jpg)no-repeat top center;
	
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
background:url({$img_dir}/mediosp/baloto.jpg)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;

}

#imgcod{
    width:51px;
height: auto;
border:none;
background:url({$img_dir}/mediosp/cod.jpg)no-repeat top center;
	
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
background:url({$img_dir}/formula-medica/btn-continuar.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 73px;
}

#processCarrier:hover
{
padding: 0 0;    
width:145px;
height:40px;
border:none;
border-style: none;

background:url({$img_dir}/formula-medica/btn-continuar-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 73px;
}



#atras1
{
padding: 0 0;  
width:145px;
height:40px;

animation: none !important;
border:none;
transition:none;
background:url({$img_dir}/formula-medica/btn-anterior.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 71px;
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
background:url({$img_dir}/formula-medica/btn-anterior-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;margin-top: 71px;
}



</style>{literal}<style type="text/css">

@media screen and (max-width:768px){
#atras1{margin-top: 77px;
}#atras1:hover{margin-top: 77px;
}
}
@media screen and (max-width:480px){
#atras1{margin-top: 73px;
}#atras1:hover{margin-top: 73px;
}
}
</style>{/literal}

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

<div id="order-detail-content" class="table_block" >
	
    <table id="cart_summary" class="std" style="margin-bottom: 20px;width: 100%;border-collapse: inherit;border-radius: 2px;-moz-border-radius: 2px;box-shadow: 0 0 0 transparent;margin-top: 181px;">
          
		<thead class="m_hide">
			<tr>
				<th class="cart_product first_item">{l s='Product'}</th>
				<th class="cart_description item">{l s='Description'}</th>
				<th class="cart_ref item">{l s='Ref.'}</th>
				<th class="cart_unit item">{l s='Unit price'}</th>
				<th class="cart_quantity item">{l s='Qty'}</th>
				<th class="cart_total item">{l s='Total'}</th>
				<th class="cart_delete last_item">&nbsp;</th>
			</tr>
		</thead>
                
                
                
                <tbody >
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
				<tr style="border: solid #D8000C;" id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" class="product_customization_for_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if} customization alternate_item {if $product@last && $customization@last && !count($gift_products)}last_item{/if}">
						<td></td>
						<td colspan="3" >
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
						</td>
						<td class="cart_quantity" colspan="2">
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
						</td>
						<td class="cart_delete">
							{if isset($cannotModify) AND $cannotModify == 1}
							{else}
								<div>
									<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")}">{l s='Delete'}</a>
								</div>
							{/if}
						</td>
					</tr>
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
		</tbody>
                
                <!-- ########################################### -->
                <tr class="cart_total_price">
                    <td colspan="3">
                    	<table class="std" style="border: 0px;" style="
margin-bottom: 20px;
width: 102%;
border-collapse: inherit;
border-radius: 2px;
-moz-border-radius: 2px;
box-shadow: 0 0 0 transparent;">
                    		<tr>
                    			<td style="border-radius: 6px; border: 1px solid #E7E7E7; xborder-collapse: collapse;">
                        
                       <!--
                        <table>
                            <tr>
                                <td><img src="{$img_dir}/mediosp/amex.png"/></td> <td><img src="{$img_dir}/mediosp/visa.png"/></td> <td><img src="{$img_dir}/mediosp/master.png"/></td> <td><img src="{$img_dir}/mediosp/diners.png"/></td>
                                
                            </tr>
                            <tr>
                               <td><img src="{$img_dir}/mediosp/pse.png"/></td> <td><img src="{$img_dir}/mediosp/baloto.png"/></td> <td><img src="{$img_dir}/mediosp/cod.png"/></td> 
                            </tr>
                            
                        </table> -->
                       
                       <div id="boxnefi">
                           <p  style="color: #399E98; font-size: 14px;" ><b>Beneficios</b></p>
                           <table>
                           	 <tr>
                           	 	<td style="border-bottom: 0px solid; padding :2px 2px" ><div style="float: left;" id="imgenvio"></div></td>
                           	 	<td style="border-bottom: 0px solid; padding :2px 2px" ><div style="font-size: 10px; float: left;">* Envío <b>gratis</b> por compras superiores a <span style="color:#b7689e"><b>$ 49.900<b/></span></div></td>
                           	 </tr>
                           	 <tr>
                           	 	<td style="border-bottom: 0px solid; padding :2px 2px" ><div style="float: left;" id="imgdiscr"></div></td>
                           	 	<td style="border-bottom: 0px solid; padding :2px 2px" ><div style="font-size: 10px">* <b>Absoluta</b> discreción</div></td>
                           	 </tr>
                           	 <tr>
                           	 	<td style="border-bottom: 0px solid; padding :2px 2px" ><div style="float: left;" id="imgprecio"></div></td>
                           	 	<td style="border-bottom: 0px solid; padding :2px 2px" ><div style="font-size: 10px">* Mejor precio <a href="content/6-garantia-del-mejor-precio"><span style="color:#b7689e; font-size:10px"><b>Garantizado*</span></b></a></div></td>
                           	 </tr>
                           </table>              
                           
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
                            <p ><label for="discount_name" style="color: #399E98; font-size: 14px;"><b>Apoyo Salud</b></label></p><br>
		             <p>
					<input style="width: 100%;" type="text" class="discount_name" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" /> 
				</p>
				<p class="submit"><input type="hidden" name="submitDiscount" /> </p>
                                 <input type="submit" style=" width:49px;" name="submitAddDiscount" value="{l s='OK'}" class="button" />
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
			                    </td>
			                </tr>
			            </table>    
                    </td>
                    
                    <td colspan="3">
                        <table>
                        <!-- total Productos -->
		{if $use_taxes}
			{if $priceDisplay}
				<tr class="cart_total_price">
					<td style="padding: 7px 5px; color:#676767; text-align: right;"  >{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</td>
					<td  style="padding: 7px 5px; color:#676767; text-align: right;" class="price" id="total_product">{displayPrice price=$total_products}</td>
				</tr>
			{else}
				<tr class="cart_total_price">
					<td  style="padding: 7px 5px; color:#676767; text-align: right;">{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</td>
					<td  style="padding: 7px 5px; color:#676767; text-align: right;" class="price" id="total_product">{displayPrice price=$total_products_wt}</td>
				</tr>
			{/if}
		{else}
			<tr class="cart_total_price">
				<td  style="padding: 7px 5px; color:#676767; text-align: right;">{l s='Total products:'}</td>
				<td  style="padding: 7px 5px; color:#676767; text-align: right;" class="price" id="total_product">{displayPrice price=$total_products}</td>
			</tr>
		{/if}
                
                <!-- Total Apoyo a la salud -->
			<tr class="cart_total_voucher" {if $total_discounts == 0}style="display:none"{/if}>
				<td  style="padding: 7px 10px; color:#676767; text-align: right;">
				{if $use_taxes && $display_tax_label}
					{l s='Total vouchers (tax excl.):'}
				{else}
					{l s='Total vouchers:'}
				{/if}
				</td>
				<td  style="padding: 7px 10px; color:#676767; text-align: right;" class="price-discount price" id="total_discount">
				{if $use_taxes && !$priceDisplay}
					{assign var='total_discounts_negative' value=$total_discounts * -1}
				{else}
					{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
				{/if}
				{displayPrice price=$total_discounts_negative}
				</td>
			</tr>
                       
			<tr class="cart_total_voucher" {if $total_wrapping == 0}style="display: none;"{/if}>
				<td  style="padding: 7px 10px; color:#676767; text-align: right;">
				{if $use_taxes}
					{if $display_tax_label}{l s='Total gift-wrapping (tax incl.):'}{else}{l s='Total gift-wrapping:'}{/if}
				{else}
					{l s='Total gift-wrapping:'}
				{/if}
				</td>
				<td  style="padding: 7px 10px; color:#676767; text-align: right;" class="price-discount price" id="total_wrapping">
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
				<tr class="cart_total_delivery" style="{if !isset($carrier->id) || is_null($carrier->id)}display:none;{/if} padding: 7px 10px; color:#676767;">
                                    <td  style="text-align: right;">{l s='Shipping'}</td>
					<td  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_shipping">{l s='Free Shipping!'}</td>
				</tr> 
			{else}
				{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
					{if $priceDisplay}
						<tr class="cart_total_delivery" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
							<td  style="padding: 7px 10px; color:#676767; text-align: right;">{if $display_tax_label}{l s='Total shipping (tax excl.):'}{else}{l s='Total shipping:'}{/if}</td>
							<td  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</td>
						</tr>
					{else}
						<tr class="cart_total_delivery"{if $total_shipping <= 0} style="display:none;"{/if}>
							<td  style="padding: 7px 10px; color:#676767; text-align: right;">{if $display_tax_label}{l s='Total shipping (tax incl.):'}{else}{l s='Total shipping:'}{/if}</td>
							<td  style="padding: 7px 10px; color:#676767;  text-align: right;" class="price" id="total_shipping" >{displayPrice price=$total_shipping}</td>
						</tr>
					{/if}
				{else}
					<tr class="cart_total_delivery"{if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
						<td  style="padding: 7px 10px; color:#676767; text-align: right;">{l s='Total shipping:'}</td>
						<td  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_shipping" >{displayPrice price=$total_shipping_tax_exc}</td>
					</tr>
				{/if}
			{/if}
                        
                        
                        <!-- total sin IVA -->
                        
			{if $use_taxes}
			<tr class="cart_total_price">
				<td  style="padding: 7px 10px; color:#676767; text-align: right;">{l s='Total (tax excl.):'}</td>
				<td  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</td>
			</tr>
			<tr class="cart_total_tax">
				<td  style="padding: 7px 10px; color:#676767; text-align: right;">{l s='Total tax:'}</td>
				<td  style="padding: 7px 10px; color:#676767; text-align: right;" class="price" id="total_tax">{displayPrice price=$total_tax}</td>
			</tr>
			{/if}
                        
                        <!-- Total compra  -->
			<tr class="cart_total_price total" >				
				{if $use_taxes}
				<td  style="padding: 7px 10px; color:#009207; width: 44%; text-align: right;">{l s='Total:'}</td>
				<td  style="padding: 7px 10px; color:#676767; width: 55%; text-align: right;" class="price total_price_container" id="total_price_container">
					<span id="total_price">{displayPrice price=$total_price}</span>
				</td>
				{else}
				<td  style="padding: 7px 10px; color:#009207; width: 44%; text-align: right;" >{l s='Total:'}xd</td>
				<td  style="padding: 7px 10px; color:#676767; width: 55%; text-align: right;" class="price total_price_container" id="total_price_container">
					
					<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
				</td>
				{/if}
			</tr>
                        <!-- fin Total -->
                        </table>
                    </td>
                </tr>
                
                
		
                        
		</tfoot>
		
                
         <!-- cupon apoyo a la salud  -->       
	{if sizeof($discounts)}
		<tbody>
		{foreach $discounts as $discount}
                    <tr  class="cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
				<td class="cart_discount_name" colspan="3">{$discount.name}</td>
				<td class="cart_discount_price"><span class="price-discount">
					{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}
				</span></td>
				<td class="cart_discount_delete">1</td>
				<td class="cart_discount_price">
					<span class="price-discount price">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
				</td>
				<td class="price_discount_del">
					{if strlen($discount.code)}<a href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}" class="price_discount_delete" title="{l s='Delete'}">{l s='Delete'}</a>{/if}
				</td>
			</tr>
		{/foreach}
		</tbody>
	{/if}
        
        
	</table>
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

