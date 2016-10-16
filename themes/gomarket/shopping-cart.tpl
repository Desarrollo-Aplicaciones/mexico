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
<link href="{$css_dir}shopping-cart.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
<link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
{literal}<style type="text/css">
@media only screen and (min-width: 1001px) {
.listado_carrito {width: 100%; float: left; height: auto;}
#contenedorGrande {width: 100%; height: auto; display:table; vertical-align: middle; text-align: center; vertical-align: middle;}
}
</style>{/literal}
{*
<div class="titulo-pasos">Mi Carrito</div>

<p class="cart_navigation1">
	<a  id="atras6" href="{if (isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order.php')) || isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order-opc') || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index')}{else}{$smarty.server.HTTP_REFERER|escape:'htmlall':'UTF-8'|secureReferrer}{/if}" class="button_large" title="{l s='Continue shopping'}">
	{l s='Return to shop'} </a>
	{if !$opc}
		<a id="processCarrier2" href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}" class="exclusive standard-checkout" title="{l s='Next'}">
		Continuar >>
		</a>
		{if Configuration::get('PS_ALLOW_MULTISHIPPING')}
			<a href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}&amp;multi-shipping=1" class="multishipping-button multishipping-checkout exclusive" title="{l s='Next'}">{l s='Next'} &raquo;</a>
		{/if}
	{/if}
</p> 
*}
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
	
    <div id="cart_summary" class="std">
          
		<div class="m_hide" style="width: 100%;height: auto;">
			{* <div id="cajon">
				<div class="product_titles first_item" id="productoLabel"><p>{l s='Product'}</p></div>
				<div class="product_titles item" id="descripcionLabel"><p>{l s='Description'}</p></div>
				<div class="product_titles item" id="cantidadLabel"><p>{l s='Qty'}</p></div>
				<div class="product_titles item" id="precioLabel"><p>{l s='Unit price'}</p></div>
				<div class="product_titles item" id="totaLabel"><p>{l s='Total'}</p></div>
				<div class="product_titles item" id="deleteLabel">&nbsp;&nbsp; </div>
			</div> *}
		
                
                
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
														<li>
															<img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" />
														</li>
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
										<span style="float:left">
											{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}
											{else}{$product.cart_quantity-$quantityDisplayed}
											{/if}
										</span>
									{else}
										<div class="cart_quantity_button">
											<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="11" height="11" />
											</a>
											<br/>
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
	                    {if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
	                    {/if}
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
	    {* <div id="contenedor3contenidos">
                        
                                    
<div id="boxnefi">
  <p  class="titulo" >Beneficios</p>

  <div class="boxnefi2">
  <div id="imgdiscr"><img src="{$img_dir}mediosp/g644.png"/></div>
  <div class="beneficios">*Envío <b>gratis</b> al día siguiente, por compras superiores a <span style="color:#b7689e"><b><br>$300</b></span> en el DF y Área Metropolitana.</div>
  </div>

  <div class="boxnefi2">
  <div id="imgdiscr"><img src="{$img_dir}mediosp/g648.png"/></div>
  <div class="beneficios">*<b>Absoluta</b> discreción</div>
  </div>

  <div class="boxnefi2">
  <div id="imgdiscr"><img src="{$img_dir}mediosp/g652.png"/></div>
    <div class="beneficios">
      * Mejor precio <a href="content/6-garantia-del-mejor-precio"> <span style="color:#b7689e;font-size:10px"><b>Garantizado*</b></span></a>
    </div>
  </div>
</div>
                             

                       
                       
                            
<div class="medios_pago2">
 <a  href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}" class="exclusive standard-checkout" id="boxmedisp">
<div class="titulo">Nuestros Medios de pago</div>
<div>
<div class="imagen_medios"><img src="{$img_dir}authentication/amex.png" width="100%"/></div>
<div class="imagen_medios"><img src="{$img_dir}authentication/visa.png" width="100%"/></div>
<div class="imagen_medios"><img src="{$img_dir}authentication/master.png" width="100%"/></div>
<!-->div class="imagen_medios"><img src="{$img_dir}authentication/diners.png" width="100%"/></div>
<div class="imagen_medios"><img src="{$img_dir}authentication/pse.png" width="100%"/></div>
<div class="imagen_medios"><img src="{$img_dir}authentication/baloto.png" width="100%"/></div-->
<div class="imagen_medios"><img src="{$img_dir}authentication/cod.png" width="100%"/></div>
<!-->div class="imagen_medios"><img src="{$img_dir}authentication/efecty.png" width="100%"/></div-->
</div>
</a>
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
                            <p ><label for="discount_name" class="titulo">Apoyo Salud</label></p>
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
	       </div> *}

	    {*  <div class="en_cuenta">
	     	<p class="mind_title">{l s='Costos de Envío'}:</p>
			<table border="1" style="display:inline-table;width:100%;text-align:center;font-family:'Open Sans', sans-serif;font-size:12px;border-color:#c8c8c8;border-style:solid;">
				<tr style="background-color:#999;font-weight:600;color:#fff;height:20px;">
					<td style="vertical-align:middle;">Zona</td>
					<td style="vertical-align:middle;">Monto</td>
					<td style="vertical-align:middle;">Día Siguiente</td>
					<td style="vertical-align:middle;">Mismo Día</td>
				</tr>
				<tr style="height:20px;">
					<td style="vertical-align:middle;" rowspan="2">D.F./ Área metropolitana</td>
					<td style="vertical-align:middle;"><b>$ 300.00</b> o <b>Más</b></td>
					<td style="vertical-align:middle;">Gratis</td>
					<td style="vertical-align:middle;"><b>$ 29.00</b></td>
				</tr>
				<tr style="height:20px;">
					<td style="vertical-align:middle;"><b>$ 0.00</b> a <b>$ 300.00</b></td>
					<td style="vertical-align:middle;"><b>$ 29.00</b></td>
					<td style="vertical-align:middle;"><b>$ 59.00</b></td>
				</tr>
				<tr style="background-color:#999;font-weight:600;color:#fff;height:20px;">
					<td style="vertical-align:middle;">Zona</td>
					<td style="vertical-align:middle;">Monto</td>
					<td style="vertical-align:middle;" colspan="2">48 Horas</td>
				</tr>
				<tr style="height:20px;">
					<td style="vertical-align:middle;">Interior de la República</td>
					<td style="vertical-align:middle;">Cualquier monto</td>
					<td style="vertical-align:middle;" colspan="2">$ 99.00</td>
				</tr>
			</table>
			<p class="mind_content">- Medios de Pago: <b>Efectivo</b> contra entrega (Aplica sólo en Distrito Federal y Área Metropolitana*) <b>Oxxo, 7 Eleven, depósito o transferencia bancaria, tarjeta de crédito y débito.<br>
	     	*Efectivo contra entrega por su seguridad de limita a pedidos iguales o menores a $2,000.00</b><br />
		** Al agregar este producto es posible que el plazo de nuestra promesa de entrega se extienda. Agrega este producto y al finalizar tu pedido ponte en contacto con nuestro servicio al cliente para determinar la hora de entrega.</p>
	     </div> *}

{if $activado_nocturno_express}
	<div id="lightbox_horario_call" style="display:none;">
		<div class="close_lightbox_horario_call" onclick="lightbox_hide(); ">
		</div>
			<a href="http://www.farmalisto.com.co/content/55-Horarios-Diciembre">
				<img id="img_lightbox_horario_call_1" src="{$base_dir}img/24horas_MX_ligthbox.jpg" style="width:90%; margin-top:10px;"/>
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

		</style>
	{/literal}
{/if}
	     
<div class="contenedortotal">

	 <!-- total Productos -->
	{*	{if $use_taxes}
			{if $priceDisplay}
				<div class="cart_total_price" >
					<div  id="primerLabel"  style="padding-bottom: 0px;">{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</div>
					<div class="totales" id="total_product" >{displayPrice price=$total_products}</div>
				</div>
			{else}
				<div class="cart_total_price">
					<div  id="primerLabel"  style="padding-bottom: 0px;">{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</div>
					<div  class="totales" id="total_product">{displayPrice price=$total_products_wt}</div>
				</div>
			{/if}
		{else}
			<div class="cart_total_price">
				<div  id="primerLabel"  style="padding-bottom: 0px;">{l s='Total products:'}</div>
				<div  class="totales" id="total_product">{displayPrice price=$total_products}</div>
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
				<div  class="price-discount price" id="total_discount">
				{if $use_taxes && !$priceDisplay}
					{assign var='total_discounts_negative' value=$total_discounts * -1}
				{else}
					{assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
				{/if}
				{displayPrice price=$total_discounts_negative}
				</div>
			</div>
                       
			<div class="cart_total_voucher" {if $total_wrapping == 0}style="display: none;"{/if}>
				<div style="float:left">
				{if $use_taxes}
					{if $display_tax_label}{l s='Total gift-wrapping (tax incl.):'}{else}{l s='Total gift-wrapping:'}{/if}
				{else}
					{l s='Total gift-wrapping:'}
				{/if}
				</div>
				<div  class="price-discount price" id="total_wrapping">
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
				<div class="cart_total_delivery" style="{if !isset($carrier->id) || is_null($carrier->id)}display:none;{/if} ">
                                    <div id="envioGratu" style="float:left">{l s='Shipping'}</div>
					<div class="totales" id="total_shipping">{l s='Free Shipping!'}</div>
				</div> 
			{else}
				{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
					{if $priceDisplay}
						<div class="cart_total_delivery" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
							<div  id="primerLabel" >{if $display_tax_label}{l s='Total shipping (tax excl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div  style="text-align: right;" class="totales" id="total_shipping">{displayPrice price=$total_shipping_tax_exc}</div>
						</div>
					{else}
						<div class="cart_total_delivery"{if $total_shipping <= 0} style="display:none;"{/if}>
							<div  id="primerLabel" >{if $display_tax_label}{l s='Total shipping (tax incl.):'}{else}{l s='Total shipping:'}{/if}</div>
							<div  style="color:#676767;  text-align: right;" class="totales" id="total_shipping" >{displayPrice price=$total_shipping}</div>
						</div>
					{/if}
				{else}
					<div class="cart_total_delivery"{if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
						<div  id="primerLabel" >{l s='Shipping'}</div>
						<div  style="text-align: right;" class="totales" id="total_shipping" >{displayPrice price=$total_shipping_tax_exc}</div>
					</div>
				{/if}
			{/if}
                        
                        
                        <!-- total sin IVA -->
                        
			{if $use_taxes}
			<div class="cart_total_price">
				<div  id="primerLabel" >{l s='Total (tax excl.):'}</div>
				<div  class="totales" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</div>
			</div>
			<div class="cart_total_tax">
				<div  id="primerLabel">{l s='Total tax:'}</div>
				<div  class="totales" id="total_tax">{displayPrice price=$total_tax}</div>
			</div>
			{/if} *}
			<!-- cupon apoyo a la salud  -->       
	<!--{if sizeof($discounts)}
		<div id="cupon">
			<div class="suc_dis">
				{l s='Successful code'}
			</div>
		</div>
		{foreach $discounts as $discount}
			<div class="cart_total_voucher">
				<span class="discount_name">{* $discount.name *}Beneficio*: &nbsp;</span>
				<span  class="price-discount">
					{if !$priceDisplay}
						&nbsp;{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}
					{/if}
					{if strlen($discount.code)}
						 <a href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}" title="{l s='Delete'}">&nbsp;&nbsp;X</a> 
					{/if}
				</span>
			</div>
		{/foreach}
	{else}
    	<div id="cupon">
			{if $voucherAllowed}
				{if isset($errors_discount) && $errors_discount}
					<ul class="error">
						{foreach $errors_discount as $k=>$error}
							<li>
								{$error|escape:'htmlall':'UTF-8'}
							</li>
						{/foreach}
					</ul>
				{/if}
				<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
					<fieldset>
	                    <p class="label_cupon">
	                    	Ingresa el nombre de tu 
	                    	<br/>
		                    <input type="radio" name="type_voucher" value="md" > 
		                    <b>
		                    	Médico &nbsp; &nbsp;&nbsp; 
		                    	<input type="radio" name="type_voucher" value="cupon" checked="checked"> 
		                    		Cupón Promocional
		                	</b>
		                </p>
						<input type="text" class="discount_name" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" /> 
						<input type="hidden" name="doc_fnd" id="doc_fnd" value="">
						<input type="hidden" name="submitDiscount" />
	                    <input type="submit" name="submitAddDiscount" id="submitAddDiscount" value="{l s='Aplicar'}" class="button" />
					</fieldset>
				</form>
				{if $displayVouchers}
					<p id="title" class="title_offers">
						{l s='Take advantage of our offers:'}
					</p>
					<div id="display_cart_vouchers">
						{foreach $displayVouchers as $voucher}
							{if $voucher.code != ''}
								<span onclick="$('#discount_name').val('{$voucher.code}');return false;" class="voucher_name">
									{$voucher.code}
								</span>
								 - 
							{/if}
							{$voucher.name}
							<br/>
						{/foreach}
					</div>
				{/if}
			{/if}
		</div> 
	{/if} --> 
                        
    <!-- Total compra  -->
	<div class="cart_total_price total" >				
		<div  id="primerLabel">
			{l s='Total:'}
		</div>
		{if $use_taxes}
			<div  id="precioContenedor" class="price total_price_container" id="total_price_container">
				{$total_price = $total_price - $total_shipping}
				{if $total_price < 0}
					{$total_price = 0}
				{/if}
				<span id="total_price">{displayPrice price=$total_price}</span>
			</div>
		{else}
			<div class="price total_price_container" id="total_price_container">
				<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
			</div>
		{/if}
	</div>
    <!-- fin Total -->
    
    <br />
    {if sizeof($discounts)}
		<div class="no_taxes">*{l s='No taxes discount'}</div>
	{/if}
</div> 
</div>                        
</div>
</div>       
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

<div class="ctn-servier">
    <span>Ingrese un representante:</span>
    {*<select name="servier" id="servier">
        {foreach item=item from=$rep_servier}
            <option value="{$item['id_asociado']}">{$item['nombre']}</option>
        {/foreach}
    </select>*}
    <input type="text" id="servier">
    <span id="error-label" style="display: none;"><br><b>Error: Representante no valido</b></span>
</div>
    
    <script>
        {*$( "#servier" ).change(function() {
            var id_rep = $(this).val();
            $.post( "{$base_dir}ajaxs/ajax_servier.php", { id_rep: id_rep })
                .done(function( data ) {
                    console.log("Respuesta del ajax:   "+data);
                }, "json");
        
        });*}
        
        $( "#servier" ).focusout(function() {
            var id_rep = $(this).val();
            {*console.log(id_rep);
            var reg = /^[a-zA-Z]{3}\s[0-9]{2}/;
            if ( !(id_rep.match(reg)) ){ // reg.test(id_rep) ) { 
                console.log("si:" + id_rep);
                $("#error-label").hide();*}
                        
                        
                        
                $.post( "{$base_dir}ajaxs/ajax_servier.php", { id_rep: id_rep })
                    .done(function( data ) {
                        console.log("Respuesta del ajax:   "+data);
                    }, "json");
                    
                    
                    
            {*}
            else {
                console.log("No: "+id_rep);
                $("#error-label").show();
            }*}
        });
        
    </script>

<div class="cart_navigation">
   {* <a  id="atras1" href="{if (isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order.php')) || isset($smarty.server.HTTP_REFERER) && strstr($smarty.server.HTTP_REFERER, 'order-opc') || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index')}{else}{$smarty.server.HTTP_REFERER|escape:'htmlall':'UTF-8'|secureReferrer}{/if}" class="button_large" title="{l s='Continue shopping'}">
   {l s='Return to shop'}</a> *}
{if !$opc}
		<a id="processCarrier" href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}" class="exclusive standard-checkout" title="{l s='Next'}">
		Pagar >></a>
		{if Configuration::get('PS_ALLOW_MULTISHIPPING')}
			<a href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')}{else}{$link->getPageLink('order', true, NULL, 'step=1')}{/if}&amp;multi-shipping=1" class="multishipping-button multishipping-checkout exclusive" title="{l s='Next'}">{l s='Next'} &raquo;</a>
		{/if}
	{/if}

</div>

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