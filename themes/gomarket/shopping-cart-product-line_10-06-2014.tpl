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
</style>{literal}<style type="text/css">



.cart_description{float: left;width: 125px;margin: 0px 43px 0px 0px;}
.cart_ref{float: left;width: 144px;margin: 0px 50px 0 0;}
.cart_unit{float: left;width: 141px;margin: 0 20px 0 0;}
#totaLabel{float: left;}
#cantidades{float: left;width: 35px; margin: 0 59px 0 0;}
#contenedorCantidades{margin-left: 17px;margin-top: -15px;}
#contenedorGrande{width: 1000px;height: 119px;margin: 14px 0;}
.cart_total .price{margin-left: 81px;float:left;}
#descripcioNombre{margin-left: 0px;}

@media only screen and (min-width: 480px) and (max-width: 767px) {

		#contenedorGrande{float: left;position:relative;height: auto;width: 144px;}
		#ProductoDescription{margin-left: 37px!important;height: 84px;margin-top: 22px;}
		#categoriaReferencia{margin-top: 8px!important;margin-left: 20px!important;float: left;}
		#valorUnidad{margin-left: 34px!important;margin-top: 23px!important;}
		#cantidades{margin: 165px 27px 0px 118px;}
		#totalCart{float: left;margin-left: -47px!important;margin-top: 13px!important;width: 168px!important;}
		.cart_quantity{margin-left: -91px;float: left;margin-top: -155px;}
		.cart_delete{float: left;margin-left: 109px;margin-top: -321px;}

}

@media screen and (max-width:480px){

			#contenedorGrande{float: left;position:relative;height: auto;width: 144px;}
		#ProductoDescription{margin-left: 37px!important;height: 84px;margin-top: 22px;}
		#categoriaReferencia{margin-top: 8px!important;margin-left: 20px!important;float: left;}
		#valorUnidad{margin-left: 34px!important;margin-top: 23px!important;}
		#cantidades{margin: 165px 27px 0px 118px;}
		#totalCart{float: left;margin-left: -47px!important;margin-top: 13px!important;width: 168px!important;}
		.cart_quantity{margin-left: -91px;float: left;margin-top: -155px;}
		.cart_delete{float: left;margin-left: 109px;margin-top: -321px;}
}

</style>{/literal}


               
<div id="product_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" class="{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)}last_item{elseif isset($productFirst) && $productFirst}first_item{/if} {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}alternate_item{/if} cart_item address_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if}" style="margin-left: 0px;margin-right: 0px;position: relative;padding: 0 147px 0 0;">

	<div id="contenedorGrande">
	<div class="cart_product">
		<a id="imagenProductoCompra" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_default')}" alt="{$product.name|escape:'htmlall':'UTF-8'}"/></a>
	</div>
	<div class="cart_description" id="ProductoDescription">
		<p class="s_title_block"><a id="descripcioNombre" href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$product.name|escape:'htmlall':'UTF-8'}</a></p>
		{if isset($product.attributes) && $product.attributes}<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}">{$product.attributes|escape:'htmlall':'UTF-8'}</a>{/if}
	</div>
	<div class="cart_ref" id="categoriaReferencia">{if $product.reference}{$product.reference|escape:'htmlall':'UTF-8'}{else}--{/if}</div>
	<div class="cart_unit" id="valorUnidad">
		<span class="price" id="product_price_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!'}</span>
			{else}
				{if isset($product.is_discounted) && $product.is_discounted}
					<span style="text-decoration:line-through;">{convertPrice price=$product.price_without_specific_price}</span><br />
				{/if}
				{if !$priceDisplay}
					{convertPrice price=$product.price_wt}
				{else}
					{convertPrice price=$product.price}
				{/if}
			{/if}
		</span>
	</div>
	<div class="cart_quantity"{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} style="text-align: center;"{/if}>
	<div id="cantidades">
		{if isset($cannotModify) AND $cannotModify == 1}
			<span style="float:left">
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}
				{else}
					{$product.cart_quantity-$quantityDisplayed}
				{/if}
			</span>
		{else}
			{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
				<span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}" >{$product.customizationQuantityTotal}</span>
			{/if}
			{if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}
				<div class="cart_quantity_button">
				<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")}" title="{l s='Add'}"><img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="8" height="8" /></a><br />
				{if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
				<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract'}">
					<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="8" height="8" />
				</a>
				{else}
				<a class="cart_quantity_down" style="opacity: 0.3;" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity}">
					<img src="{$img_dir}icon/quantity_down.gif" width="8" height="8" alt="{l s='Subtract'}" />
				</a>
				{/if}
					<input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}_hidden" />
				<input id="contenedorCantidades" size="2" type="text" autocomplete="off" class="cart_quantity_input" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  name="quantity_{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" />
				</div>
				
				
			{/if}
		{/if}
		</div>
	</div>
	<div class="cart_total" id="totalCart">
		<span class="price" id="total_product_price_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!'}</span>
			{else}
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{if !$priceDisplay}{displayPrice price=$product.total_customization_wt}{else}{displayPrice price=$product.total_customization}{/if}
				{else}
					{if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
				{/if}
			{/if}
		</span>
	</div>
	{if !isset($noDeleteButton) || !$noDeleteButton}
		<div class="cart_delete">
		{if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed) > 0 && empty($product.gift)}
			<div>
				<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_0_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")}">{l s='Delete'}</a>
			</div>
		{/if}
		</div>
	{/if}
</div>
</div>
