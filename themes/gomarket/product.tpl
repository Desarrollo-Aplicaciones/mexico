{include file="$tpl_dir./errors.tpl"}
<script type="text/javascript">
// <![CDATA[

// PrestaShop internal settings
var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
var currencyRate = '{$currencyRate|floatval}';
var currencyFormat = '{$currencyFormat|intval}';
var currencyBlank = '{$currencyBlank|intval}';
var taxRate = {$tax_rate|floatval};
var jqZoomEnabled = {if $jqZoomEnabled}true{else}false{/if};

//JS Hook
var oosHookJsCodeFunctions = new Array();

// Parameters
var id_product = '{$product->id|intval}';
var productHasAttributes = {if isset($groups)}true{else}false{/if};
var quantitiesDisplayAllowed = {if $display_qties == 1}true{else}false{/if};
var quantityAvailable = {if $display_qties == 1 && $product->quantity}{$product->quantity}{else}0{/if};
var allowBuyWhenOutOfStock = {if $allow_oosp == 1}true{else}false{/if};
var availableNowValue = '{$product->available_now|escape:'quotes':'UTF-8'}';
var availableLaterValue = '{$product->available_later|escape:'quotes':'UTF-8'}';
var productPriceTaxExcluded = {$product->getPriceWithoutReduct(true)|default:'null'} - {$product->ecotax};
var productBasePriceTaxExcluded = {$product->base_price} - {$product->ecotax};

var reduction_percent = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'percentage'}{$product->specificPrice.reduction*100}{else}0{/if};
var reduction_price = {if $product->specificPrice AND $product->specificPrice.reduction AND $product->specificPrice.reduction_type == 'amount'}{$product->specificPrice.reduction|floatval}{else}0{/if};
var specific_price = {if $product->specificPrice AND $product->specificPrice.price}{$product->specificPrice.price}{else}0{/if};
var product_specific_price = new Array();
{foreach from=$product->specificPrice key='key_specific_price' item='specific_price_value'}
product_specific_price['{$key_specific_price}'] = '{$specific_price_value}';
{/foreach}
var specific_currency = {if $product->specificPrice AND $product->specificPrice.id_currency}true{else}false{/if};
var group_reduction = '{$group_reduction}';
var default_eco_tax = {$product->ecotax};
var ecotaxTax_rate = {$ecotaxTax_rate};
var currentDate = '{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}';
var maxQuantityToAllowDisplayOfLastQuantityMessage = {$last_qties};
var noTaxForThisProduct = {if $no_tax == 1}true{else}false{/if};
var displayPrice = {$priceDisplay};
var productReference = '{$product->reference|escape:'htmlall':'UTF-8'}';
var productAvailableForOrder = {if (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}'0'{else}'{$product->available_for_order}'{/if};
var productShowPrice = '{if !$PS_CATALOG_MODE}{$product->show_price}{else}0{/if}';
var productUnitPriceRatio = '{$product->unit_price_ratio}';
var idDefaultImage = {if isset($cover.id_image_only)}{$cover.id_image_only}{else}0{/if};
var stock_management = {$stock_management|intval};
{if !isset($priceDisplayPrecision)}
{assign var='priceDisplayPrecision' value=2}
{/if}
{if !$priceDisplay || $priceDisplay == 2}
{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
{elseif $priceDisplay == 1}
{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
{/if}


var productPriceWithoutReduction = '{$productPriceWithoutReduction}';
var productPrice = '{$productPrice}';

// Customizable field
var img_ps_dir = '{$img_ps_dir}';
var customizationFields = new Array();
{assign var='imgIndex' value=0}
{assign var='textFieldIndex' value=0}
{foreach from=$customizationFields item='field' name='customizationFields'}
{assign var="key" value="pictures_`$product->id`_`$field.id_customization_field`"}
customizationFields[{$smarty.foreach.customizationFields.index|intval}] = new Array();
customizationFields[{$smarty.foreach.customizationFields.index|intval}][0] = '{if $field.type|intval == 0}img{$imgIndex++}{else}textField{$textFieldIndex++}{/if}';
customizationFields[{$smarty.foreach.customizationFields.index|intval}][1] = {if $field.type|intval == 0 && isset($pictures.$key) && $pictures.$key}2{else}{$field.required|intval}{/if};
{/foreach}

// Images
var img_prod_dir = '{$img_prod_dir}';
var combinationImages = new Array();

{if isset($combinationImages)}
{foreach from=$combinationImages item='combination' key='combinationId' name='f_combinationImages'}
combinationImages[{$combinationId}] = new Array();
{foreach from=$combination item='image' name='f_combinationImage'}
combinationImages[{$combinationId}][{$smarty.foreach.f_combinationImage.index}] = {$image.id_image|intval};
{/foreach}
{/foreach}
{/if}

combinationImages[0] = new Array();
{if isset($images)}
{foreach from=$images item='image' name='f_defaultImages'}
combinationImages[0][{$smarty.foreach.f_defaultImages.index}] = {$image.id_image};
{/foreach}
{/if}

// Translations
var doesntExist = '{l s='This combination does not exist for this product. Please select another combination.' js=1}';
var doesntExistNoMore = '{l s='This product is no longer in stock' js=1}';
var doesntExistNoMoreBut = '{l s='with those attributes but is available with others.' js=1}';
var uploading_in_progress = '{l s='Uploading in progress, please be patient.' js=1}';
var fieldRequired = '{l s='Please fill in all the required fields before saving your customization.' js=1}';

{if isset($groups)}
	// Combinations
	{foreach from=$combinations key=idCombination item=combination}
	var specific_price_combination = new Array();
	var available_date = new Array();
	specific_price_combination['reduction_percent'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'percentage'}{$combination.specific_price.reduction*100}{else}0{/if};
	specific_price_combination['reduction_price'] = {if $combination.specific_price AND $combination.specific_price.reduction AND $combination.specific_price.reduction_type == 'amount'}{$combination.specific_price.reduction}{else}0{/if};
	specific_price_combination['price'] = {if $combination.specific_price AND $combination.specific_price.price}{$combination.specific_price.price}{else}0{/if};
	specific_price_combination['reduction_type'] = '{if $combination.specific_price}{$combination.specific_price.reduction_type}{/if}';
	specific_price_combination['id_product_attribute'] = {if $combination.specific_price}{$combination.specific_price.id_product_attribute|intval}{else}0{/if};
	available_date['date'] = '{$combination.available_date}';
	available_date['date_formatted'] = '{dateFormat date=$combination.available_date full=false}';
	addCombination({$idCombination|intval}, new Array({$combination.list}), {$combination.quantity}, {$combination.price}, {$combination.ecotax}, {$combination.id_image}, '{$combination.reference|addslashes}', {$combination.unit_impact}, {$combination.minimal_quantity}, available_date, specific_price_combination);
	{/foreach}
	{/if}

	{if isset($attributesCombinations)}
	// Combinations attributes informations
	var attributesCombinations = new Array();
	{foreach from=$attributesCombinations key=id item=aC}
	tabInfos = new Array();
	tabInfos['id_attribute'] = '{$aC.id_attribute|intval}';
	tabInfos['attribute'] = '{$aC.attribute}';
	tabInfos['group'] = '{$aC.group}';
	tabInfos['id_attribute_group'] = '{$aC.id_attribute_group|intval}';
	attributesCombinations.push(tabInfos);
	{/foreach}
	{/if}
function cs_resize_tab()	{

	if(!isMobile())
	{
		$('.content_hide_show').removeAttr( 'style' );
	}
	if(getWidthBrowser() < 767){
		$('ul#thumbs_list_frame img').each(function() {
			$( this ).attr("src", $( this ).attr("src").split("medium").join("large") );
		});
	} 
}
	$(window).load(function(){
			//	Responsive layout, resizing the items
			$('#thumbs_list_frame').carouFredSel({
				responsive: true,
				width: '100%',
				height : 'variable',
				prev: '#prev-thumnail',
				next: '#next-thumnail',
				auto: false,
				swipe: {
					onTouch : true
				},
				items: {
					width: 90,
					visible: {
						min: 1,
						max: 1
					}
				},
				scroll: {
					
					items : 1 ,	 //The number of items scrolled.
					direction : 'left',	//The direction of the transition.
					duration : 1 //The duration of the transition.
				}
			});
		});
	$(document).ready(function() {
		cs_resize_tab();
		$('div.title_hide_show').first().addClass('selected');
		$('#more_info_sheets').on('click', '.title_hide_show', function() {
			$(this).next().toggle();
			if($(this).next().css('display') == 'block'){
				$(this).addClass('selected');
			}else{
				$(this).removeClass('selected');
			}
			return false;
		}).next().hide();
	});
	$(window).resize(function() {
		cs_resize_tab();
	});
	function isMobile() {
		if( navigator.userAgent.match(/Android/i) ||
			navigator.userAgent.match(/webOS/i) ||
			navigator.userAgent.match(/iPad/i) ||
			navigator.userAgent.match(/iPhone/i) ||
			navigator.userAgent.match(/iPod/i)
			){
			return true;
	}
	return false;
}

function update_shipping(){
	var cuenta = $("#quantity_wanted").val() * {$productPrice};
	var acum = {$valor_carrito};
	var subtotal= {$envio_gratis} - ( acum + cuenta );
	/*if (subtotal < 0) {
		$('.shipping_val').hide();
		$('.free_shipping').show('slow');
	}else{
		$('.free_shipping').hide();
		$('.shipping_val').show('slow');
	}*/
	$('#lightbox_qty').html($("#quantity_wanted").val());
	$('#lightbox_subtotal').html(cuenta);
}

$(window).load(function(){
	update_shipping();
});

$("#quantity_wanted").live('keyup', function(){
	update_shipping();
});

$('#contact-lap').unbind('click').live('click', function(){
	standard_lightbox('care-lines');
});

$('#btn-discount-purple').unbind('click').live('click', function(){
	standard_lightbox('product_voucher');
});

$('.continue_shopping').unbind('click').live('click', function(){
	lightbox_hide();
});

// $('.proceed').unbind('click').live('click', function(){
// });

$('.cart_quantity_up').unbind('click').live('click', function(){
	var qty_now=$("#quantity_wanted").val();
	var qty_new=parseInt(qty_now)+1;
	$("#quantity_wanted").val(qty_new);
	update_shipping();
});

$('.cart_quantity_down').unbind('click').live('click', function(){
	var qty_now=$("#quantity_wanted").val();
	if(parseInt(qty_now)>1)
	{
		var qty_new=parseInt(qty_now)-1;
		$("#quantity_wanted").val(qty_new);
		update_shipping();
	}
});
//]]>
</script>

{include file="$tpl_dir./breadcrumb.tpl"}

<!--Campo de fórmula médica-->
{if isset($isformula) && $isformula}
    <div id="formula_medica">
		<div class="arrow_formula"><img src="{$img_dir}pdp/Rx.png" alt="{l s='Subtract'}" width="100%" /></div>
		<div class="legend_formula">
			<span class="only1">Consultar al médico, </span>
			Producto de venta con fórmula médica 
			<span class="only2">, Sin fórmula médica no es posible la compra de este medicamento.</span>
		</div>
	</div>
{/if}
<!--fin campo formula medica-->

<link href='https://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>

<!-- Contenedor del producto con precio-->
<div class="contenido_producto">
	<!-- thumbnails -->
	<div class="ctn-product-imgs">
		{if isset($images) && count($images) >= 1}
			<div id="views_block">

				<!--Imagen labotratorio-->
				<div class="ctn-img-manufacturer">
					{if $url_manufacturer neq "" AND $url_manufacturer neq 0}
						<a href="{$base_dir}{$url_manufacturer}">
							<img class="img-manufacturer" src="{$base_dir}{$img_manufacturer}">
						</a>
					{/if}
				</div>
				<!--Imagen labotratorio-->

				<div class="thumb_navigate {if count($images) < 4}resp_nav{/if}">
					<a id="prev-thumnail"href="#"></a>
				</div>

				<div id="thumbs_list">
					<ul id="thumbs_list_frame">
						{foreach from=$images item=image name=thumbnails}
							{assign "legend_img" $product->name}
							{if !empty($image.legend)}
								{assign "legend_img" $image.legend}
							{/if}			
							{assign var=imageIds value="`$product->id`-`$image.id_image`"}
							<li id="thumbnail_{$image.id_image}">
								<a href="{$link->getImageLink($product->link_rewrite, $imageIds, thickbox_default)}" rel="other-views" class="thickbox {if $smarty.foreach.thumbnails.first}shown{/if}" title="{$image.legend|htmlspecialchars}">
									<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')}" alt="{$legend_img|htmlspecialchars}" />
								</a>
							</li>
						{/foreach}
					</ul>
					{if isset($isboton) && $isboton}
						<div class="btn-discount-purple" id="btn-discount-purple">
							<div class="btn-discount-purple-text">Obtén un -5%</div>
						</div>
					{/if}
				</div>

				<div class="thumb_navigate {if count($images) < 5}resp_nav{/if}">
					<a id="next-thumnail" href="#"></a>
				</div>
			</div>
		{/if}
		<!-- /thumbnails -->


		<!-- product img-->
		{assign "legend_img" $product->name}
		{if !empty($image.legend)}
			{assign "legend_img" $image.legend}
		{/if}	
		<div id="image-block{if !$have_image || count($images) < 2}-static{/if}">
			<span id="view_full_size" >
				{if $have_image}
					<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')}" {if $jqZoomEnabled}class="jqzoom" alt="{$legend_img|htmlspecialchars}"{else} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$legend_img|htmlspecialchars}" {/if} id="bigpic"/>
				{else}
					<img src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="{$legend_img|htmlspecialchars}" title="{$product->name|escape:'htmlall':'UTF-8'}"/>
				{/if}
			</span>
			{if $url_manufacturer neq "" AND $url_manufacturer neq 0}
				<a href="{$base_dir}{$url_manufacturer}">
					<img src="{$base_dir}{$img_manufacturer}">
				</a>
			{/if}
			{if isset($isboton) && $isboton}
				<div class="btn-discount-purple" id="btn-discount-purple">
					<div class="btn-discount-purple-text">Obtén un -5%</div>
				</div>
			{/if}
		</div>
		<!--/product img-->
	</div>
	{* /Contenedor de las imagenes del producto *}

	<!--product info-->
	<div class="buy_block">
		<div class="ctn-product-name" >
			<h1 id="name_unico_sin_estilo">{$product->name|lower|capitalize}</h1>
		</div>

		<div id="qty_content">
			
			<div class="ctn-price">
				<span class="word_price">Precio:</span>

				{* Precio tachado por descuento *}
				{if !$priceDisplay || $priceDisplay == 2}
					{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
					{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
				{elseif $priceDisplay == 1}
					{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
					{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
				{/if}
				{if $product->on_sale}
					<img src="{$img_dir}onsale_{$lang_iso}.gif" alt="{l s='On sale'}" class="on_sale_img"/>
					<span class="on_sale">{l s='On sale!'}</span>
				{elseif $product->specificPrice AND $product->specificPrice.reduction AND $productPriceWithoutReduction > $productPrice}
					<span class="discount">{l s='Reduced price!'}</span>
				{/if}
				{if $priceDisplay == 2}
					<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.'}</span>
				{/if}
				{* CUANTO FUE EL DESCUENTO--> <p id="reduction_percent" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}>
				<span id="reduction_percent_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}</span></p>
				<p id="reduction_amount" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'amount' && $product->specificPrice.reduction|intval ==0} style="display:none"{/if}><span id="reduction_amount_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'amount' && $product->specificPrice.reduction|intval !=0}-{convertPrice price=$product->specificPrice.reduction|floatval}{/if}</span></p> *}
				{if $product->specificPrice AND $product->specificPrice.reduction}
                                    <span id="old_price">
                                        {if $priceDisplay >= 0 && $priceDisplay <= 2}
                                            {if $productPriceWithoutReduction > $productPrice}
                                                <span id="old_price_display">{convertPrice price=$productPriceWithoutReduction}</span>
                                            {/if}
                                        {/if} 
                                    </span>
                                {/if}
				{if isset($packItems) && isset($productPrice) && isset($product) && $packItems|@count && $productPrice < $product->getNoPackPrice()}
					<p class="pack_price">{l s='instead of'} <span style="text-decoration: line-through;">{convertPrice price=$product->getNoPackPrice()}</span></p>
					<br class="clear" />
				{/if}
				{if $product->ecotax != 0}
					<p class="price-ecotax">{l s='include'} <span id="ecotax_price_display">{if $priceDisplay == 2}{$ecotax_tax_exc|convertAndFormatPrice}{else}{$ecotax_tax_inc|convertAndFormatPrice}{/if}</span> {l s='for green tax'}
						{if $product->specificPrice AND $product->specificPrice.reduction}
							<br />{l s='(not impacted by the discount)'}
						{/if}
					</p>
				{/if}
				{if !empty($product->unity) && $product->unit_price_ratio > 0.000000}
					{math equation="pprice / punit_price" pprice=$productPrice punit_price=$product->unit_price_ratio assign=unit_price}
					<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
				{/if}
				{* / Precio tachado por descuento *}

				{* Precio normalito o con el descuento aplicado (si lo tiene)*}
				<div class="our_price_display" id="our_price_display2">
					{if $priceDisplay >= 0 && $priceDisplay <= 2}{convertPrice price=$productPrice}{/if}
				</div>
				{* / Precio normalito o con el descuento aplicado (si lo tiene)*}
			</div>


			<div class="ctn-quantity">
				<div id="cantidad">{l s='Quantity2:'}</div>
				<div class="quantity_input">
					<a rel="nofollow" class="cart_quantity_down" id="" href="javascript:void(0)" title="{l s='Subtract'}">
						<div class="btn-cant-sumarrestar"> <span>-</span></div>
						{* <img src="{$img_dir}pdp/quantity_down.png" alt="{l s='Subtract'}"/> *}
					</a>
					<input type="text" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" maxlength="3" style="width: 28px;min-width:28px; text-align:center;" {if $product->minimal_quantity > 1}onkeyup="checkMinimalQuantity({$product->minimal_quantity});"{/if} />
					<a rel="nofollow" class="cart_quantity_up" href="javascript:void(0)" title="{l s='Add'}">
						<div class="btn-cant-sumarrestar"> <span>+</span></div>
						{* <img src="{$img_dir}pdp/quantity_up.png" alt="{l s='Add'}"/> *}
					</a>
				</div>
			</div>
		</div>

		{* <div class="beneficio">
			<span class="shipping_val">
				Envío por compras menores a <b>{convertPrice price=$envio_gratis}</b>: Bogotá <b>{convertPrice price=1000}</b> resto del país <b>{convertPrice price=5000}</b>
			</span>
			<span class="free_shipping">
				<b>¡Envío Gratuito!</b>
			</span>
		</div> *}

		{if ($productPrice > 0) AND !((!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE)}
			<div id="add_to_cart" class="buttons_bottom_block" >
				<input type="hidden" name="token" />
				<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
				<input type="hidden" name="add" value="1" />
				<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
				<div class="proceed">
					<input type="submit" id="btnComprar" name="Submit" value="{l s='Comprar'}" class="exclusive" />
				</div>
				<div class="continue_shopping">
					<button class="btn-default " id="btnAgregar1">
						<span id="txt-btn-agregar">Agregar</span>
						<img id="img-cart" src="{$img_dir}pdp/img-cart.png">
						<img id="img-check" src="{$img_dir}pdp/img-check.png" style=" display: none ">
						</button>
					<input type="hidden" id="btnAgregar" value="Agregar"/>
				</div>
			</div>
		{/if}

		<div class="ctn-contact-lap" id="contact-lap">
			<div class="ctn-img-call">
				<img src="{$img_dir}pdp/faceCall1.jpg" class="img-call">
			</div>
			<div class="borde-bajo">
				<span class="txt-gray">¿Tienes algúna duda?</span>
				<span class="txt-green">Contáctanos</span>
				<span class="txt-green"><strong>+</strong></span>
			</div>
		</div>
	</div>
	<!--/product info-->
	{* Contactanos *}
	<div class="ctn-contact-movile" style="display: none;">
		
		<div class="row-tittle">
			<span class="txt-orange">También puedes pedirlo por:</span>
		</div>
		
		<div class="row-desplegable" id="desplegador">
			<div class="ctn-img-call">
				<img src="{$img_dir}pdp/faceCall2.jpg" id="img2" class="img-call" style="display: none;">
				<img src="{$img_dir}pdp/faceCall1.jpg" id="img1" class="img-call">
			</div>
			<div class="ctn-txt-arrow borde-superior" id="call-now">
				<span id="txt-green">Llámanos ahora</span>
				<div class="ctn-img-desplegable">
					<img  id="img3" src="{$img_dir}pdp/flecha-desplegable2.jpg" style="display: none;">
					<img  id="img4" src="{$img_dir}pdp/flecha-desplegable1.jpg">
				</div>
			</div>
		</div>

		<div class="ctn-phone-number" id="desplegable" style=" display: none; ">
			{* <div class="row-call-now">
				<div class="txt-green">Algún texto como en Colombia</div>
			</div> *}
		
			<div class="row-phone-number">
				<div class="phone-number-city"><span class="txt-phone-number"><strong>{* Aquí el nombre de una ciudad especifica *}</strong>55 6732 1100</span></div>
				<div class="ctn-btn-call"><a href="tel:+5567321100"><div class="btn-call">Llamar</div></a></div>
			</div>
		</div>

		<div class="row-desplegable" id="active-whatsapp">
			<a href="intent://send/5567321100#Intent;scheme=smsto;package=com.whatsapp;action=android.intent.action.SENDTO;end" class="href-whatsapp"> 
				<div class="ctn-img-whatsapp"><img src="{$img_dir}pdp/whatsapp.jpg" class="img-whatsapp"></div>
				<div class="ctn-txt-arrow borde-superior">
					<span>Whatsapp</span>
					<div class="ctn-img-desplegable">
						<img src="{$img_dir}pdp/flecha-desplegable2.jpg" id="img6" style="display: none;">
						<img src="{$img_dir}pdp/flecha-desplegable1.jpg" id="img5">
					</div>
				</div>
			</a>
		</div>

	</div>

	<div class="ctn-times-movile" style="display: none;">
		<div class="row-desplegable" id="despl-times-delivery">
			<div class="ctn-img-truck">
				<img src="{$img_dir}pdp/truck2.jpg" class="img-truck" id="truck2" style="display: none">
				<img src="{$img_dir}pdp/truck1.jpg" class="img-truck" id="truck1">
			</div>
			<div class="ctn-txt-arrow">
				<span id="txt-green-times-delivery">Tiempos de entrega</span>
				<div class="ctn-img-desplegable" id="open-close">
					<span class="img-desplegable-plus">+</span>
				</div>
			</div>
		</div>

		<div class="ctn-times-delivery" id="times-delivery" style="display: none;">
			{* <div class="ctn-valor-min"><span class="txt-green-small"><strong>Valor mínimo</strong> de pedido: <strong>$25.000</strong></span></div> *}
			<div class="ctn-info-times-delivery">
				<span class="txt-gray">
					<strong>Costos de envío<br>
					Pedidos en D.F. y Área<br>
					Metropolitana:<br><br>
					Mayores a $350.00</strong>, el <span class="txt-green-small"><strong>¡ENVÍO ES GRATIS!</span><br>
					Menores a $350.00</strong>, el costo de envío es de <strong>$80.00  </strong><br>
					Pedidos al <strong>interior de la República</strong>, el costo de envío es de <strong>$189.00</strong>
				</span>
			</div>
			<div class="ctn-valor-min"><span class="txt-green-small"><strong>Ver más...</strong></span></div>
		</div>

	</div>
	{* Contactanos *}

</div>

<!--Descripcion producto-->
{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
	<div id="global" >
		<div id="conconImage">
			<div id="more_info_block" class="clear">
				<ul id="more_info_tabs" class="idTabs idTabsShort clearfix">
					{if $product->description_short}<li><a id="info_tab_info" href="#idTab0">{l s='Información'}</a></li>{/if}
					{if $product->description}<li><a id="more_info_tab_more_info" href="#idTab1">{l s='More info'}</a></li>{/if}
					{if $features}<li><a id="more_info_tab_data_sheet" href="#idTab2">{l s='Data sheet'}</a></li>{/if}
					{if $attachments}<li><a id="more_info_tab_attachments" href="#idTab9">{l s='Download'}</a></li>{/if}
					{if isset($accessories) AND $accessories}<li><a href="#idTab4">{l s='Accessories'}</a></li>{/if}
					{if isset($product) && $product->customizable}<li><a href="#idTab10">{l s='Product customization'}</a></li>{/if}
					<li><a href="#idTab99" id="tab-times-delivery">Tiempos de entrega</a></li>
					{$HOOK_PRODUCT_TAB}
				</ul>

				<div id="more_info_sheets" class="sheets align_justify">
					{if $product->description_short}
						{* <div class="title_hide_show">{l s='Información'}</div> *}
						<!--info-->
						<div id="idTab0" class="rte content_hide_show">
							<div id="scro">{$product->description_short}</div>
						</div>
					{/if}

					{if $features}
						{* <div class="title_hide_show">{l s='Data sheet'}</div> *}
					{/if}

					{if isset($features) && $features}
						<!-- product's features -->
						<ul id="idTab2" class="rte bullet content_hide_show">
							<div id="scro">
								{foreach from=$features item=feature}
									{if isset($feature.value)}
										<li>
											<span>{$feature.name|escape:'htmlall':'UTF-8'}</span>
											{$feature.value|escape:'htmlall':'UTF-8'}
										</li>
									{/if}
								{/foreach}
							</div>
						</ul>
					{/if}

					{* {if $attachments}<div class="title_hide_show" style="display:none">{l s='Download'}</div>{/if} *}
					
					{if isset($attachments) && $attachments}
						<ul id="idTab9" class="rte bullet content_hide_show"style="font-family: 'Open Sans', sans-serif;">
							{foreach from=$attachments item=attachment}
							<li><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}</li>
							{/foreach}
						</ul>
					{/if}
					{* {if isset($accessories) AND $accessories}<div class="title_hide_show" style="display:none">{l s='Accessories'}</div>{/if} *}
					{if isset($accessories) AND $accessories}
						<!-- accessories -->
						<ul id="idTab4" class="rte bullet content_hide_show">
							<div class="block products_block accessories_block clearfix">
								<div class="block_content">
									<ul id="product_list">
										{foreach from=$accessories item=accessory name=accessories_list}
										{if ($accessory.allow_oosp || $accessory.quantity > 0) AND $accessory.available_for_order AND !isset($restricted_country_mode)}
										{assign var='accessoryLink' value=$link->getProductLink($accessory.id_product, $accessory.link_rewrite, $accessory.category)}
										<li class="{if isset($grid_product)}{$grid_product}{else}grid_6{/if} ajax_block_product {if $smarty.foreach.accessories_list.first}first_item{elseif $smarty.foreach.accessories_list.last}last_item{else}item{/if} product_accessories_description clearfix">
											<div class="center_block">
												<div class="image"><a href="{$accessoryLink|escape:'htmlall':'UTF-8'}" title="{$accessory.name|escape:'htmlall':'UTF-8'}" class="product_img_link"><img src="{$link->getImageLink($accessory.link_rewrite, $accessory.id_image, 'home_default')}" alt="{$accessory.legend|escape:'htmlall':'UTF-8'}"/></a></div>
												<div class="name_product"><a href="{$accessoryLink|escape:'htmlall':'UTF-8'}">{$accessory.name|escape:'htmlall':'UTF-8'}</a></div>
												<div class="content_price">
													{if $accessory.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE} <span class="price">{if $priceDisplay != 1}{displayWtPrice p=$accessory.price}{else}{displayWtPrice p=$accessory.price_tax_exc}{/if}</span>{/if}
												</div>
												<div class="product_desc">
													{$accessory.description_short|strip_tags|truncate:90:'...'}
												</div>
												{if !$PS_CATALOG_MODE}
												<a rel="ajax_id_product_{$accessory.id_product|intval}" class="exclusive button ajax_add_to_cart_button" href="{$link->getPageLink('cart', true, NULL, "qty=1&amp;id_product={$accessory.id_product|intval}&amp;token={$static_token}&amp;add")}" rel="ajax_id_product_{$accessory.id_product|intval}" title="{l s='Agregar al carrito'}">{l s='Agregar al carrito'}</a>
												{/if}
											</div>
										</li>
										{/if}
										{/foreach}
									</ul>
								</div>
							</div>
						</ul>
					{/if}

					<ul id="idTab99" class="rte bullet content_hide_show">
						<div class="ctn-times-delivery-li">
							<span class="txt-gray">
								<strong>Costos de envío<br>
								Pedidos en D.F. y Área<br>
								Metropolitana:<br><br>
								Mayores a $350.00</strong>, el <span class="txt-green-small"><strong>¡ENVÍO ES GRATIS!</span><br>
								Menores a $350.00</strong>, el costo de envío es de <strong>$80.00 </strong><br>
								Pedidos al <strong>interior de la República</strong>, el costo de envío es de <strong>$189.00</strong>
							</span>
						</div>
					</ul>

					{if $product->description}
						{* <div class="title_hide_show">{l s='More info'}</div> *}
						<!-- full description -->
						<div id="idTab1" class="rte content_hide_show">
							{if isset($isrequired) && $isrequired}
								<div id="info_confirm">
									<p>La información aquí contenida está dirigida exclusivamente para profesionales de la salud, es necesario confirmar que pertenece a este gremio para tener acceso a la misma.<br /><br />
										<input type="checkbox" value="is_prof" id="is_prof" /><span onclick="$('#is_prof').trigger('click');"> Confirmo que soy un profesional de la salud.</span>
									</p>
									<a href="javascript:void(0)" onclick="if($('#is_prof').is(':checked'))$('#info_confirm').slideToggle();"><div>Mostrar información</div></a>
								</div>
							{/if}
							<div id="scro">
								{$product->description}
							</div>
						</div>
					{/if}
				</div>
			</div>
		</div>
		
		<div id="informac">
			{$HOOK_BANNER}
		</div>
		{$HOOK_PRPAMIDCEN}
		<!-- Customizable products -->
		{if isset($product) && $product->customizable}
			<div class="title_hide_show" style="display:none">{l s='Product customization'}</div>
			<div id="idTab10" class="rte bullet customization_block content_hide_show">
				<form method="post" action="{$customizationFormTarget}" enctype="multipart/form-data" id="customizationForm" class="clearfix">
					<p class="infoCustomizable">
						{l s='After saving your customized product, remember to add it to your cart.'}
						{if $product->uploadable_files}<br />{l s='Allowed file formats are: GIF, JPG, PNG'}{/if}
					</p>
					{if $product->uploadable_files|intval}
						<div class="customizableProductsFile">
							<h3>{l s='Pictures'}</h3>
							<ul id="uploadable_files" class="clearfix">
								{counter start=0 assign='customizationField'}
								{foreach from=$customizationFields item='field' name='customizationFields'}
									{if $field.type == 0}
										<li class="customizationUploadLine{if $field.required} required{/if}">{assign var='key' value='pictures_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field}
											{if isset($pictures.$key)}
												<div class="customizationUploadBrowse">
													<img src="{$pic_dir}{$pictures.$key}_small" alt="" />
													<a href="{$link->getProductDeletePictureLink($product, $field.id_customization_field)}" title="{l s='Delete'}" >
														<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" class="customization_delete_icon" width="12" height="12" />
													</a>
												</div>
											{/if}
											<div class="customizationUploadBrowse">
												<label class="customizationUploadBrowseDescription">
													{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your hard drive'}{/if}{if $field.required}<sup>*</sup>{/if}
												</label>
												<input type="file" name="file{$field.id_customization_field}" id="img{$customizationField}" class="customization_block_input {if isset($pictures.$key)}filled{/if}" />
											</div>				
										</li>
										{counter}
									{/if}
								{/foreach}
							</ul>
						</div>
					{/if}
					{if $product->text_fields|intval}
						<div class="customizableProductsText">
							<h3>{l s='Text'}</h3>
							<ul id="text_fields">
								{counter start=0 assign='customizationField'}
								{foreach from=$customizationFields item='field' name='customizationFields'}
									{if $field.type == 1}
										<li class="customizationUploadLine{if $field.required} required{/if}">
											<label for ="textField{$customizationField}">{assign var='key' value='textFields_'|cat:$product->id|cat:'_'|cat:$field.id_customization_field} {if !empty($field.name)}{$field.name}{/if}{if $field.required}<sup>*</sup>{/if}</label>
											<textarea type="text" name="textField{$field.id_customization_field}" id="textField{$customizationField}" rows="1" cols="40" class="customization_block_input" />{if isset($textFields.$key)}{$textFields.$key|stripslashes}{/if}</textarea>
										</li>
										{counter}
									{/if}
								{/foreach}
							</ul>
						</div>
					{/if}
					<p id="customizedDatas">
						<input type="hidden" name="quantityBackup" id="quantityBackup" value="" />
						<input type="hidden" name="submitCustomizedDatas" value="1" />
						<input type="button" class="button" value="{l s='Save'}" onclick="javascript:saveCustomization()" />
						<span id="ajax-loader" style="display:none"><img src="{$img_ps_dir}loader.gif" alt="loader" /></span>
					</p>
				</form>
				<p class="clear required"><sup>*</sup> {l s='required fields'}</p>
			</div>
			<!--/Customizable products -->
		{/if}
	</div>
	{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
{/if}
{include file="$tpl_dir/value-offer.tpl"}
<!--/Descripcion producto-->

<!--Footer Relacionados, SEO-->
{if (isset($packItems) && $packItems|@count eq 0) or !isset($packItems)}
	{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
{/if}
{$HOOK_PRPABOTCEN}
{if isset($packItems) && $packItems|@count > 0}
	<div id="blockpack">
		<h2>{l s='Pack content'}</h2>
		{include file="$tpl_dir./product-list.tpl" products=$packItems}
	</div>
{/if}

<!--/Footer Relacionados, SEO-->
<script type="text/javascript" src="{$js_dir}pdp/product-cart.js"></script>
<div id="buyConfirm">
	<div class="lightbox_close" onclick="lightbox_hide();"></div>
	<div class="lightbox_title">Este producto se agregó correctamente al carrito</div>
	<div class="lightbox_resume">
		<img class="lightbox_img" src={if $have_image}"{$link->getImageLink($product->link_rewrite, $cover.id_image, 'small_default')}"{else}"{$img_prod_dir}{$lang_iso}-default-small_default.jpg"{/if} alt="{$product->name|escape:'htmlall':'UTF-8'}" />
		<div class="lightbox_desc">
			<div class="lightbox_pname">{$product->name|lower|capitalize}</div>
			<div style="text-align:center">Cantidad: &nbsp;&nbsp; <span id="lightbox_qty"></span> &nbsp;Total: &nbsp;&nbsp; <span id="lightbox_subtotal"></span></div>
		</div>
	</div>
	<div class="lightbox_subtotal">
		<div><b>Hay en total <span class="ajax_cart_quantity"></span> productos en el carrito</b></div>
		<div>Subtotal: <b><span class="ajax_block_cart_total"></span></b></div>
	</div>
	<div class="shipping_val">
		Envío por compras menores a {*<b>{convertPrice price=$envio_gratis}</b>:<br />Bogotá <b>{convertPrice price=1000}</b> resto del país <b>{convertPrice price=5000}</b> *}
	</div>
	<div class="free_shipping">
		<b>¡Envío Gratuito!</b>
	</div>
	<div class="options">
		<div class="continue_shopping">Buscar más Productos</div>
		<div class="proceed">Realizar Pago</div>
	</div>
	<div class="lightbox_payment">
		Contamos con múltiples medios de pago<br />
		<img src="{$img_dir}footer/oxxo.jpg" alt="oxxo" />
		<img src="{$img_dir}footer/deposito.jpg" alt="deposito" />
		<img src="{$img_dir}footer/paypal.jpg" alt="Tarjeta Débito" />
		<img src="{$img_dir}footer/dinners.jpg" alt="Dinner Club" />
		<img src="{$img_dir}footer/master.jpg" alt="MasterCard" />
		<img src="{$img_dir}footer/visa.jpg" alt="Visa" />
		<img src="{$img_dir}footer/amex.jpg" alt="American Express" />
		<img src="{$img_dir}footer/cod.jpg" alt="Pago Contraentrega" />
	</div>
</div>
<div id="product_voucher">
	<div class="lightbox_close" onclick="lightbox_hide();"></div>
	<div class="lightbox_title">Cupón primera compra</div>
	<div class="lightbox_resume">
		<label>¡Bienvenido a farmalisto!</label><br /> ingresa este cupón al momento de realizar tu pago y obten un <span>5%</span> sobre el total de tu compra
	</div>
	<div class="code">BEN315</div>
	<div class="tos">*El cupón sólo se hará válido en la primera compra, no es acumulable con otras promociones. Para más información escríbir a contacto@farmalisto.com.mx</div>
</div>
<div id="favorite_lightbox">
	<div class="lightbox_close" onclick="lightbox_hide();"></div>
	<div class="lightbox_title">Agregar a Favoritos</div>
	<div class="lightbox_resume">
		Para agregar un producto a tu lista de favoritos ingresa a tu cuenta, si no tienes, puedes crearla.<br /><br />¿Deseas hacerlo ahora?<br /><br />
	</div>
	<div style="display:inline-block;">
		<a href="{$link->getPageLink('my-account', true)}">Ingresar</a>&nbsp;
		<a href="{$link->getPageLink('my-account', true)}">Crear Cuenta</a>&nbsp;
		<a href="javascript:void(0)" onclick="lightbox_hide();" class="cancelar">Cancelar</a>
	</div>
</div>
<div id="care-lines">
	<div class="lightbox_close" onclick="lightbox_hide();"></div>
	<div class="lightbox_title"></div>
	<div class="lightbox_resume">
		<img src="{$img_dir}pdp/LIGTHBOX-TELEFONOS-MX.jpg">

	</div>
</div>