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


	{include file="$tpl_dir./errors.tpl"}
	{if $errors|@count == 0}
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
	$(window).load(function(){
			//	Responsive layout, resizing the items
			$('#thumbs_list_frame').carouFredSel({
				responsive: true,
				width: '70%',
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
						min: 2,
						max: 3
					}
				},
				scroll: {
					
					items : 3 ,       //  The number of items scrolled.
					direction : 'left',    //  The direction of the transition.
					duration  : 500   //  The duration of the transition.
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
function cs_resize_tab()	{
	if(!isMobile())
	{
		$('.content_hide_show').removeAttr( 'style' );
	}
	if(getWidthBrowser() < 767){
		$('ul#more_info_tabs').hide();
		$('div.title_hide_show').show();
	} else {
		$('div.title_hide_show').hide();
		$('ul#more_info_tabs').show();
	}
}
$('.cart_quantity_up').unbind('click').live('click', function(){
	var qty_now=$("#quantity_wanted").val();
	var qty_new=parseInt(qty_now)+1;
	$("#quantity_wanted").val(qty_new);
});
$('.cart_quantity_down').unbind('click').live('click', function(){
	var qty_now=$("#quantity_wanted").val();
	if(parseInt(qty_now)>1)
	{
		var qty_new=parseInt(qty_now)-1;
		$("#quantity_wanted").val(qty_new);
	}
});
//]]>
</script>

{literal}<script>
$(document).ready(function(){
var nuevosElementos = $({/literal}"<div >{$product->name|escape:'htmlall':'UTF-8'}.</div>"{literal});
nuevosElementos.appendTo("#tituloCategoryProd");
});
</script>{/literal}

{include file="$tpl_dir./breadcrumb.tpl"}

<div id="primary_block" class="clearfix">

	{if isset($adminActionDisplay) && $adminActionDisplay}
	<div id="admin-action">
		<p>{l s='This product is not visible to your customers.'}
			<input type="hidden" id="admin-action-product-id" value="{$product->id}" />
			<input type="submit" value="{l s='Publish'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 0, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
			<input type="submit" value="{l s='Back'}" class="exclusive" onclick="submitPublishProduct('{$base_dir}{$smarty.get.ad|escape:'htmlall':'UTF-8'}', 1, '{$smarty.get.adtoken|escape:'htmlall':'UTF-8'}')"/>
		</p>
		<p id="admin-action-result"></p>
	</p>
</div>
{/if}

{if isset($confirmation) && $confirmation}
<p class="confirmation">
	{$confirmation}
</p>
{/if}
<!-- right infos-->
<div id="pb-right-column">
	<!-- product img-->
	<div id="image-block"   style="background: url('{$img_dir}Recuadro-principal-producto.png');background-repeat: no-repeat;-webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;">
	{if $have_image}
	<span id="view_full_size" >
		<img src="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'large_default')}" {if $jqZoomEnabled}class="jqzoom" alt="{$link->getImageLink($product->link_rewrite, $cover.id_image, 'thickbox_default')}"{else} title="{$product->name|escape:'htmlall':'UTF-8'}" alt="{$product->name|escape:'htmlall':'UTF-8'}" {/if} id="bigpic" style="width: 68%;"/>
		<span class="span_link">{l s='View full size'}</span>
	</span>

	{else}
	<span id="view_full_size">
		<img src="{$img_prod_dir}{$lang_iso}-default-large_default.jpg" id="bigpic" alt="" title="{$product->name|escape:'htmlall':'UTF-8'}"/>
		<span class="span_link">{l s='View full size'}</span>
	</span>
	{/if}

</div>

<div id="logoPrecio">
	<img src="{$img_dir}icon/producto/ProductPage Aprobada Codigo Barras Oculto copia-10.png" alt="{l s='Subtract'}" width="100%" height="100%" />
</div>
{if isset($isformula) && $isformula}



	<div id="formula_medica">
	<img src="{$img_dir}icon/producto/ProductPage Aprobada Codigo Barras Oculto copia-08.png" alt="{l s='Subtract'}" width="100%" height="100%" />
	</div>
	{else}
	<div id="formula_medica">
	<img src="{$img_dir}icon/producto/formulablanco.png" alt="{l s='Subtract'}" width="100%" height="100%" />
	</div>
    {/if}
{if isset($images) && count($images) > 0}
<!-- thumbnails -->
<!-- thumbnails -->
{if isset($images) && count($images) < 2}
    <div id="views_block" class="clearfix ">

	<div id="thumbs_list">
		<ul id="thumbs_list_frame">
			{if isset($images)}
			{foreach from=$images item=image name=thumbnails}
			{assign var=imageIds value="`$product->id`-`$image.id_image`"}
			<li id="thumbnail_{$image.id_image}" style="margin: 27px 7px 0px;width: 70px;height: 59px;
														
														">
			<a href="{$link->getImageLink($product->link_rewrite, $imageIds, thickbox_default)}" rel="other-views" class="thickbox {if $smarty.foreach.thumbnails.first}shown{/if}" title="{$image.legend|htmlspecialchars}">
				<img id="thumb_{$image.id_image}" src="{$img_dir}mini_prod.jpg" alt="{$image.legend|htmlspecialchars}" style="border-radius: 3px;height: 38px;margin-top: 0px;width: 50%;"/>
			</a>
		</li>
		{/foreach}
		{/if}
	</ul>
	<a id="prev-thumnail" class="btn prev" href="#">&lt;</a>
	<a id="next-thumnail" class="btn next" href="#">&gt;</a>
</div>

</div> 

        {else}
            
            <div id="views_block" class="clearfix {if isset($images) && count($images) < 2}hidden{/if}">

	<div id="thumbs_list">
		<ul id="thumbs_list_frame">
			{if isset($images)}
			{foreach from=$images item=image name=thumbnails}
			{assign var=imageIds value="`$product->id`-`$image.id_image`"}
			<li id="thumbnail_{$image.id_image}" style="width: 125px!important;">
			<a href="{$link->getImageLink($product->link_rewrite, $imageIds, thickbox_default)}" rel="other-views" class="thickbox {if $smarty.foreach.thumbnails.first}shown{/if}" title="{$image.legend|htmlspecialchars}" style="height:68px;">
				
	<div style="background: url('{$img_dir}escritorii.png');  background-repeat: no-repeat top center;
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;padding: 0px 24px;height: 58px;width: 60px;">
		<img id="thumb_{$image.id_image}" src="{$link->getImageLink($product->link_rewrite, $imageIds, 'medium_default')}" alt="{$image.legend|htmlspecialchars}" style="border-radius: 3px;height: 56px;margin-top: 0px;width: 98%;"/></div>
			</a>
		</li>
		{/foreach}
		{/if}
	</ul>
	<a id="prev-thumnail" class="btn prev" href="#">&lt;</a>
	<a id="next-thumnail" class="btn next" href="#">&gt;</a>
</div>

</div>
      <!-- cfffff -->      
           
            
            
        {/if}

{/if}
{if isset($images) && count($images) > 1}<p class="resetimg clear"><span id="wrapResetImages" style="display: none;"><img src="{$img_dir}icon/i_display.png" alt="{l s='Cancel'}" width="24" height="18"/> <a id="resetImages" href="{$link->getProductLink($product)}" onclick="$('span#wrapResetImages').hide('slow');return (false);">{l s='Display all pictures'}</a></span></p>{/if}

</div>
<div id="pb-left-column">

    <div id="cabeceraTitulo">

	<!--contenedor logo Fabricante-->
	<div id="fabricante" >
		{if $url_manufacturer neq "" AND $url_manufacturer neq 0}
		<a href="{$base_dir}{$url_manufacturer}">
		{/if}	

		<img style="width: 100px;height: 45px;" src="{$img_manufacturer}">

		{if $url_manufacturer neq "" AND $url_manufacturer neq 0}
		</a>
		{/if}	
	</div>

	<!--fin contenedor logo Fabricante-->
		<img src="{$img_dir}icon/producto/linea-lab-product.png" alt="{l s='Subtract'}" style=""/>
	<!---titulo producto-->
	<div id="tituloProducto">
		<h1 id="titulo_producto">{$product->name|escape:'htmlall':'UTF-8'|lower|capitalize}</h1>
	</div>
	<!--fin titulo producto-->



</div>
	<!--contenedor gris de referencia y cantidad-->
								<div id="product_reference" {if isset($groups) OR !$product->reference}style="display: none;"{/if}>

									<label id="cantidad">{l s='Quantity:'}</label>
									<input type="text" name="qty" id="quantity_wanted" class="text" value="{if isset($quantityBackup)}{$quantityBackup|intval}{else}{if $product->minimal_quantity > 1}{$product->minimal_quantity}{else}1{/if}{/if}" size="2" maxlength="3" {if $product->minimal_quantity > 1}onkeyup="checkMinimalQuantity({$product->minimal_quantity});"{/if} />

									<span class="cs_cart_quantity">
										<a rel="nofollow" class="cart_quantity_up" id="" href="javascript:void(0)" title="{l s='Add'}">
											<img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="10" height="10" /></a>
											<a rel="nofollow" class="cart_quantity_down" id="" href="javascript:void(0)" title="{l s='Subtract'}">
												<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="10" height="10" />
											</a>
										</p>
									</div>



										<div id="disponibilidad">
											{if $product->quantity <= 0}<span class="outofstock" style="display:none;">{l s='Agotado'}</span>{else}{/if}<!-- number of item in stock -->
											{if ($display_qties == 1 && !$PS_CATALOG_MODE && $product->available_for_order)}
											{/if}<p class="category_name" id="dispo3">
											{$category->name|escape:'htmlall':'UTF-8'}
										</p>
										{if $product->description_short}
										<div id="short_description_content" class="rte align_justify">
										

{if isset($isboton) && $isboton}
<a href="{$base_dir}themes/gomarket/Boton_productos/bot_dto.php?idproducto={$product->id}">
<iframe src="{$base_dir}themes/gomarket/Boton_productos/bot_dto.php?idproducto={$product->id}" style="border: none;
height: 60px;"></iframe></a>

{$product->description_short}
{else}
{$product->description_short}	
{/if}								</div>
										{/if}

{*
										<div style="display:none;" class="itemPinterestButton">
											<a href="//pinterest.com/pin/create/button/" data-pin-do="buttonBookmark" >
												<img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>
												<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
											</div>
										</div>
*}


									</div>
									{if isset($isformula) && $isformula}
										<div id="formulaMedica1"><img id="formulaMedica1" src="{$img_dir}icon/producto/ProductPage Aprobada Codigo Barras Oculto copia-09.png" alt="{l s='Subtract'}" /><span id="labelFormula">Este medicamento sólo se vende con prescripción médica. NO SE AUTOMEDIQUE.</span></div>
										{else}
										<div id="formulaMedica1"><img id="formulaMedica1" src="{$img_dir}icon/producto/formulablanco.png" alt="{l s='Subtract'}" style="height: 68px;width: 69px;"/><span id="labelFormula">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>
                            {/if}
											<div id="compraSegura"><img src="{$img_dir}icon/producto/linea2.png" id="linea2">Compra Segura<img src="{$img_dir}icon/producto/linea.png" id="linea"></div>

										<img src="{$img_dir}icon/medios-pago.png" alt="{l s='Subtract'}" id="mediosPagos"/>



										<div class="cs_price price" id="price">
											{if !$priceDisplay || $priceDisplay == 2}
											{assign var='productPrice' value=$product->getPrice(true, $smarty.const.NULL, $priceDisplayPrecision)}
											{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(false, $smarty.const.NULL)}
											{elseif $priceDisplay == 1}
											{assign var='productPrice' value=$product->getPrice(false, $smarty.const.NULL, $priceDisplayPrecision)}
											{assign var='productPriceWithoutReduction' value=$product->getPriceWithoutReduct(true, $smarty.const.NULL)}
											{/if}

											<div id="centrarPrecio">

											<div class="our_price_display" style="margin-left: 19px;">
												<label id="labelPrecio">Precio</label>

												{if $priceDisplay >= 0 && $priceDisplay <= 2}
												<span id="our_price_display" style="word-spacing: -9px;">{convertPrice price=$productPrice}</span>
					<!--{if $tax_enabled  && ((isset($display_tax_label) && $display_tax_label == 1) OR !isset($display_tax_label))}
						{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
						{/if}-->
						{/if}
					</div>
					</div>

					{if $product->on_sale}
					<img src="{$img_dir}onsale_{$lang_iso}.gif" alt="{l s='On sale'}" class="on_sale_img"/>
					<span class="on_sale">{l s='On sale!'}</span>
					{elseif $product->specificPrice AND $product->specificPrice.reduction AND $productPriceWithoutReduction > $productPrice}
					<span class="discount">{l s='Reduced price!'}</span>
					{/if}
					{if $priceDisplay == 2}
					<br />
					<span id="pretaxe_price"><span id="pretaxe_price_display">{convertPrice price=$product->getPrice(false, $smarty.const.NULL)}</span>&nbsp;{l s='tax excl.'}</span>
					{/if}
					<p id="reduction_percent" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'percentage'} style="display:none;"{/if}><span id="reduction_percent_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'percentage'}-{$product->specificPrice.reduction*100}%{/if}</span></p>
					<p id="reduction_amount" {if !$product->specificPrice OR $product->specificPrice.reduction_type != 'amount' && $product->specificPrice.reduction|intval ==0} style="display:none"{/if}><span id="reduction_amount_display">{if $product->specificPrice AND $product->specificPrice.reduction_type == 'amount' && $product->specificPrice.reduction|intval !=0}-{convertPrice price=$product->specificPrice.reduction|floatval}{/if}</span></p>
					{if $product->specificPrice AND $product->specificPrice.reduction}
					<p id="old_price"><span class="bold">
						{if $priceDisplay >= 0 && $priceDisplay <= 2}
						{if $productPriceWithoutReduction > $productPrice}
						<span id="old_price_display">{convertPrice price=$productPriceWithoutReduction}</span>
						<!-- {if $tax_enabled && $display_tax_label == 1}
							{if $priceDisplay == 1}{l s='tax excl.'}{else}{l s='tax incl.'}{/if}
							{/if} -->
							{/if}
							{/if}
						</span>
					</p>
					{/if}
					{if $packItems|@count && $productPrice < $product->getNoPackPrice()}
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
					{math equation="pprice / punit_price"  pprice=$productPrice  punit_price=$product->unit_price_ratio assign=unit_price}
					<p class="unit-price"><span id="unit_price_display">{convertPrice price=$unit_price}</span> {l s='per'} {$product->unity|escape:'htmlall':'UTF-8'}</p>
					{/if}
					{*close if for show price*}
					{/if}
					{if (!$allow_oosp && $product->quantity <= 0) OR !$product->available_for_order OR (isset($restricted_country_mode) AND $restricted_country_mode) OR $PS_CATALOG_MODE}
				<span class="exclusive" style="display:none;">
					<span></span>
					{l s='Agregar al Carrito'}
				</span>
				{else}
				<p id="add_to_cart" class="buttons_bottom_block" >
					<input type="submit" id="btnComprar" name="Submit" value="{l s='COMPRAR'}" class="exclusive" />
				</p>
				{/if}
				<div id="conteDescuento">
					<span style="color: #8D2F2B;font-weight: 600!important;font-size: 11px!important;font-family: 'Open Sans', sans-serif!important;"><!-- Valor restante para envío gratuito -->
					{if ($cart_qties > 0 and $valor_restante neq 0) OR ( $cart_qties eq 0 and $valor_restante neq 0)}
					Tu env&#237;o gratis por compras superiores a <br>{convertPrice price=$valor_restante} en toda Colombia.
					{elseif $cart_qties > 0 and $valor_restante eq 0}
					Tu env&iacute;o es gratuito!.
					{/if}</span>
				</div>

										<div id="elementos_redes">
										<img src="{$img_dir}barra-arriba.png" alt="{l s='Subtract'}" id="mediosPagos" style="width: 213px;margin-top: 10px;"/>
										<div class="itemGooglePlusOneButton">
											<div class="g-plusone" data-size="medium"></div>

											<!-- Place this tag after the last +1 button tag. -->
											<script type="text/javascript">
											(function() {
												var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
												po.src = 'https://apis.google.com/js/plusone.js';
												var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
											})();
											</script>
										</div>

										<div class="itemFacebookButton" id="caraLibro">
											<div id="fb-root"></div>
											<script type="text/javascript">
											(function(d, s, id) {
												var js, fjs = d.getElementsByTagName(s)[0];
												if (d.getElementById(id)) return;
												js = d.createElement(s); js.id = id;
												js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
												fjs.parentNode.insertBefore(js, fjs);
											}(document, 'script', 'facebook-jssdk'));
											</script>
											<div class="fb-like" data-send="false" data-layout="button_count" data-width="200" data-show-faces="true"></div>
										</div>

											<script type="text/javascript" src="//platform.twitter.com/widgets.js"></script>
											<img style="width: 213px;margin-top: -16px;" src="{$img_dir}barra-abajo.png" alt="{l s='Subtract'}" id="mediosPagos"/>
										</div>	
				</div>	

									<!--<div id="redes_sociales" > 
										<a href="http://www.linkedin.com/company/farmalisto" target="_blank" title="LinkedIn"><div alt="{l s='Subtract'}" style="border:0px !important;" id="in"/></div></a>	
										
										<a href="https://plus.google.com/+FarmalistoColombia/posts" target="_blank" title="Google +"><div  alt="{l s='Subtract'}" id="goog"/></div></a>

										<a href="https://twitter.com/farmalistocol" target="_blank" title="Twitter"><div alt="{l s='Subtract'}" id="twit"/></div></a>

										<a href="https://www.facebook.com/farmalistocolombia" target="_blank" title="Facebook Oficial"><div alt="{l s='Subtract'}"  id="face"/></div></a>

									</div>-->

										

										

								

										
									</div>
									<!--fin contenedor gris referencia y cantidad-->
																			{if $product->description_short OR $packItems|@count > 0}
										<div id="short_description_block">

											{if $product->description}
											<p class="buttons_bottom_block" style="display:none;" ><a href="javascript:{ldelim}{rdelim}" class="button">{l s='More details'}</a></p>
											{/if}
											{if $packItems|@count > 0}
											<div class="short_description_pack">
												<h3>{l s='Pack content'}</h3>
												{foreach from=$packItems item=packItem}
												<div class="pack_content">
													{$packItem.pack_quantity} x <a href="{$link->getProductLink($packItem.id_product, $packItem.link_rewrite, $packItem.category)}">{$packItem.name|escape:'htmlall':'UTF-8'}</a>
													<p>{$packItem.description_short}</p>
												</div>
												{/foreach}
											</div>
											{/if}
										</div>
										{/if}
						</div>

	<!-- Out of stock hook -->
		<p style="display:none;" id="oosHook"{if $product->quantity > 0} style="display: none;"{/if}>
			{$HOOK_PRODUCT_OOS}
		</p>
		{*{if isset($colors) && $colors}
		<!-- colors -->
		<div id="color_picker">
			<p>{l s='Pick a color:' js=1}</p>
			<div class="clear"></div>
			<ul id="color_to_pick_list" class="clearfix">
				{foreach from=$colors key='id_attribute' item='color'}
				<li><a id="color_{$id_attribute|intval}" class="color_pick" style="background: {$color.value};" onclick="updateColorSelect({$id_attribute|intval});$('#wrapResetImages').show('slow');" title="{$color.name}">{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$color.name}" width="20" height="20" />{/if}</a></li>
				{/foreach}
			</ul>
			<div class="clear"></div>
		</div>
		{/if}*}

		{if ($product->show_price AND !isset($restricted_country_mode)) OR isset($groups) OR $product->reference OR (isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS)}
		<!-- Agregar al carrito form-->
		<form id="buy_block" {if $PS_CATALOG_MODE AND !isset($groups) AND $product->quantity > 0}class="hidden"{/if} action="{$link->getPageLink('cart')}" method="post">

			<!-- hidden datas -->
			<p class="hidden">
				<input type="hidden" name="token" />
				<input type="hidden" name="id_product" value="{$product->id|intval}" id="product_page_product_id" />
				<input type="hidden" name="add" value="1" />
				<input type="hidden" name="id_product_attribute" id="idCombination" value="" />
			</p>

			<div class="product_attributes">
				{if isset($groups)}
				<!-- attributes -->
				<div id="attributes">
					{foreach from=$groups key=id_attribute_group item=group}
					{if $group.attributes|@count}
					<fieldset class="attribute_fieldset">
						<label class="attribute_label" for="group_{$id_attribute_group|intval}">{$group.name|escape:'htmlall':'UTF-8'} :</label>
						{assign var="groupName" value="group_$id_attribute_group"}
						<div class="attribute_list">
							{if ($group.group_type == 'select')}
							<select name="{$groupName}" id="group_{$id_attribute_group|intval}" class="attribute_select" onchange="findCombination();getProductAttribute();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if};">
								{foreach from=$group.attributes key=id_attribute item=group_attribute}
								<option value="{$id_attribute|intval}"{if (isset($smarty.get.$groupName) && $smarty.get.$groupName|intval == $id_attribute) || $group.default == $id_attribute} selected="selected"{/if} title="{$group_attribute|escape:'htmlall':'UTF-8'}">{$group_attribute|escape:'htmlall':'UTF-8'}</option>
								{/foreach}
							</select>
							{elseif ($group.group_type == 'color')}
							<ul id="color_to_pick_list" class="clearfix">
								{assign var="default_colorpicker" value=""}
								{foreach from=$group.attributes key=id_attribute item=group_attribute}
								<li{if $group.default == $id_attribute} class="selected"{/if}>
								<a id="color_{$id_attribute|intval}" class="color_pick{if ($group.default == $id_attribute)} selected{/if}" style="background: {$colors.$id_attribute.value};" title="{$colors.$id_attribute.name}" onclick="colorPickerClick(this);getProductAttribute();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if}">
									{if file_exists($col_img_dir|cat:$id_attribute|cat:'.jpg')}
									<img src="{$img_col_dir}{$id_attribute}.jpg" alt="{$colors.$id_attribute.name}" width="20" height="20" /><br>
									{/if}
								</a>
							</li>
							{if ($group.default == $id_attribute)}
							{$default_colorpicker = $id_attribute}
							{/if}
							{/foreach}
						</ul>
						<input type="hidden" class="color_pick_hidden" name="{$groupName}" value="{$default_colorpicker}" />
						{elseif ($group.group_type == 'radio')}
						{foreach from=$group.attributes key=id_attribute item=group_attribute}
						<input type="radio" class="attribute_radio" name="{$groupName}" value="{$id_attribute}" {if ($group.default == $id_attribute)} checked="checked"{/if} onclick="findCombination();getProductAttribute();{if $colors|@count > 0}$('#wrapResetImages').show('slow');{/if}">
						{$group_attribute|escape:'htmlall':'UTF-8'}<br/>
						{/foreach}
						{/if}
					</div>
				</fieldset>
				{/if}
				{/foreach}
			</div>
			{/if}
			

			<!-- quantity wanted -->
			

			<!-- minimal quantity wanted -->
			<p id="minimal_quantity_wanted_p" as="as"{if $product->minimal_quantity <= 1 OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				{l s='This product is not sold individually. You must select at least'} <b id="minimal_quantity_label">{$product->minimal_quantity}</b> {l s='quantity for this product.'}
			</p>
			{if $product->minimal_quantity > 1}
			<script type="text/javascript">
			checkMinimalQuantity();
			</script>
			{/if}

			<!-- availability -->
			<!--<p id="availability_statut"{if ($product->quantity <= 0 && !$product->available_later && $allow_oosp) OR ($product->quantity > 0 && !$product->available_now) OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none;"{/if}>
				<span id="availability_label">{l s='Availability:'}</span>
				<span id="availability_value"{if $product->quantity <= 0} class="warning_inline"{/if}>
				{if $product->quantity <= 0}{if $allow_oosp}{$product->available_later}{else}{l s='This product is no longer in stock'}{/if}{else}{$product->available_now}{/if}
			</span>-->
		</p>





		<p class="warning_inline" id="last_quantities" style="display:none;"{if ($product->quantity > $last_qties OR $product->quantity <= 0) OR $allow_oosp OR !$product->available_for_order OR $PS_CATALOG_MODE} style="display: none"{/if} >{l s='Warning: Last items in stock!'}</p>
	</div>

	<div class="content_prices clearfix">
		<!-- prices -->
		{if $product->show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}

		{if $product->online_only}
		<p class="online_only">{l s='Online only'}</p>
		{/if}



		{if isset($HOOK_PRODUCT_ACTIONS) && $HOOK_PRODUCT_ACTIONS}{$HOOK_PRODUCT_ACTIONS}{/if}

		<div class="clear"></div>
	</div>
</form>
{/if}
{if isset($HOOK_EXTRA_RIGHT) && $HOOK_EXTRA_RIGHT}{$HOOK_EXTRA_RIGHT}{/if}
</div>
</div>

{if (isset($quantity_discounts) && count($quantity_discounts) > 0)}
<!-- quantity discount -->
<ul class="idTabs clearfix">
	<li><a href="#discount" style="cursor: pointer" class="selected">{l s='Quantity discount'}</a></li>
</ul>
<div id="quantityDiscount">
	<table class="std">
		<thead>
			<tr>
				<th>{l s='product'}</th>
				<th>{l s='from (qty)'}</th>
				<th>{l s='discount'}</th>
			</tr>
		</thead>
		<tbody>
			{foreach from=$quantity_discounts item='quantity_discount' name='quantity_discounts'}
			<tr id="quantityDiscount_{$quantity_discount.id_product_attribute}">
				<td>
					{if (isset($quantity_discount.attributes) && ($quantity_discount.attributes))}
					{$product->getProductName($quantity_discount.id_product, $quantity_discount.id_product_attribute)}
					{else}
					{$product->getProductName($quantity_discount.id_product)}
					{/if}
				</td>
				<td>{$quantity_discount.quantity|intval}</td>
				<td>
					{if $quantity_discount.price >= 0 OR $quantity_discount.reduction_type == 'amount'}
					-{convertPrice price=$quantity_discount.real_value|floatval}
					{else}
					-{$quantity_discount.real_value|floatval}%
					{/if}
				</td>
			</tr>
			{/foreach}
		</tbody>
	</table>
</div>
{/if}

<!-- description and features -->
{if (isset($product) && $product->description) || (isset($features) && $features) || (isset($accessories) && $accessories) || (isset($HOOK_PRODUCT_TAB) && $HOOK_PRODUCT_TAB) || (isset($attachments) && $attachments) || isset($product) && $product->customizable}
<div id="global" >
<div id="conconImage">
	<div id="more_info_block" class="clear">
		<ul id="more_info_tabs" class="idTabs idTabsShort clearfix">
		{if $product->description}<li><a id="more_info_tab_more_info" href="#idTab1" style="text-transform: capitalize;">{l s='More info'}</a></li>{/if}
		{if $features}<li><a id="more_info_tab_data_sheet" href="#idTab2" style="text-transform: capitalize;">{l s='Data sheet'}</a></li>{/if}
		{if $attachments}<li><a id="more_info_tab_attachments" href="#idTab9">{l s='Download'}</a></li>{/if}
		{if isset($accessories) AND $accessories}<li><a href="#idTab4">{l s='Accessories'}</a></li>{/if}
		{if isset($product) && $product->customizable}<li><a href="#idTab10">{l s='Product customization'}</a></li>{/if}
		{$HOOK_PRODUCT_TAB}
		</ul>

		<div id="more_info_sheets" class="sheets align_justify">

			{if $product->description}<div class="title_hide_show" style="text-transform: capitalize;">{l s='More info'}</div>{/if}
			{if isset($product) && $product->description}
			<!-- full description -->
			<div id="idTab1" class="rte content_hide_show"><div id="scro">{$product->description}</div></div>
			{/if}
			{if $features}<div class="title_hide_show" style="text-transform: capitalize;">{l s='Data sheet'}</div>{/if}
			{if isset($features) && $features}
			<!-- product's features -->
			<ul id="idTab2" class="bullet content_hide_show" style="text-transform: capitalize;font-family: 'Open Sans', sans-serif;"><div id="scro">
				{foreach from=$features item=feature}
				{if isset($feature.value)}
				<li><span>{$feature.name|escape:'htmlall':'UTF-8'}</span> {$feature.value|escape:'htmlall':'UTF-8'}</li>
				{/if}
				{/foreach}</div>
			</ul>
			{/if}

			{if $attachments}<div class="title_hide_show" style="display:none">{l s='Download'}</div>{/if}
			{if isset($attachments) && $attachments}
			<ul id="idTab9" class="bullet content_hide_show"style="font-family: 'Open Sans', sans-serif;">
				{foreach from=$attachments item=attachment}
				<li><a href="{$link->getPageLink('attachment', true, NULL, "id_attachment={$attachment.id_attachment}")}">{$attachment.name|escape:'htmlall':'UTF-8'}</a><br />{$attachment.description|escape:'htmlall':'UTF-8'}</li>
				{/foreach}
			</ul>
			{/if}
			{if isset($accessories) AND $accessories}<div class="title_hide_show" style="display:none">{l s='Accessories'}</div>{/if}
			{if isset($accessories) AND $accessories}
			<!-- accessories -->
			<ul id="idTab4" class="bullet content_hide_show">
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
		</div>
		
	</div>


		<div id="imagenMarco">
			<div id="marco"></div>
		</div>
		<div id="imagenMarco2">
			<div id="marco"></div>
		</div>
	</div>

	<div id="informac">
		<a href="http://www.farmalisto.com.co/content/28-quienes-somos?utm_source=PDP&utm_medium=BannerPDP&utm_term=ProductDetail&utm_content=Cyberlunes&utm_campaign=0026_16052014" target="_self"><img id="imgge" src="{$base_dir}img/cms/landing/pdp/pdp-farmalisto.jpg" style="width: 240px;height: 232px;" alt="Los mejores productos para mamá "/></a>
		{*<div style="float: left;">
			<div id="imagenes">
			<img id="imgge" src="{$img_dir}icon/producto/ProductPage Aprobada Codigo Barras Oculto copia-07.png" alt="{l s='Subtract'}" />
			</div>
			<div id="labeles">
				<label id="TextoGrileta">Comunícate <label id="colorTexto">gratis</label> con nosotros en el <label id="colorTexto">01800 913 3830</label> y en Bogotá al <label id="colorTexto">220 5249</label>.</label>
			</div>
		</div>

		<div style="float: left;">
			<div id="imagenes">
				<img id="imgge"  src="{$img_dir}icon/producto/ProductPage Aprobada Codigo Barras Oculto copia-07.png" alt="{l s='Subtract'}"/>
			</div>
			<div id="labeles">
				<label id="TextoGrileta2">Contamos con <label id="colorTexto">certificación Symantec</label><label id="textoGrilletaOscuro"> Powered by VeriSign Secure Site Pro with EV</label>.</label>
			</div>
		</div>

		<div style="float: left;">
			<div id="imagenes">
				<img id="imgge" src="{$img_dir}icon/producto/ProductPage Aprobada Codigo Barras Oculto copia-07.png" alt="{l s='Subtract'}"/>
			</div>
			<div id="labeles">
				<label id="TextoGrileta3">Envíos <label id="colorTexto">gratis</label> por compras superiores a <label id="textoGrilletaOscuro">$49.900</label>, entregamos en <label id="colorTexto">cualquier</label> parte del <label id="colorTexto">país</label>.</label>
			</div>
		</div>
	</div>*}

		
</div>



		<!-- Customizable products -->
		{if isset($product) && $product->customizable}<div class="title_hide_show" style="display:none">{l s='Product customization'}</div>{/if}
		{if isset($product) && $product->customizable}
		<div id="idTab10" class="bullet customization_block content_hide_show">
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
								<label class="customizationUploadBrowseDescription">{if !empty($field.name)}{$field.name}{else}{l s='Please select an image file from your hard drive'}{/if}{if $field.required}<sup>*</sup>{/if}</label>
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
		{/if}

		{if isset($HOOK_PRODUCT_TAB_CONTENT) && $HOOK_PRODUCT_TAB_CONTENT}{$HOOK_PRODUCT_TAB_CONTENT}{/if}
		


{/if}

{if (isset($packItems) && $packItems|@count eq 0) or !isset($packItems)}
{if isset($HOOK_PRODUCT_FOOTER) && $HOOK_PRODUCT_FOOTER}{$HOOK_PRODUCT_FOOTER}{/if}
{/if}

	{if isset($packItems) && $packItems|@count > 0}
	<div id="blockpack">
		<h2>{l s='Pack content'}</h2>
		{include file="$tpl_dir./product-list.tpl" products=$packItems}


	{/if}
{/if}

