{*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<!-- MODULE Home Featured Products -->
<div id="featured-products_block_center" class="block products_block grid_19 omega">
	<div class="featured-products-content">
	<h4 class="title_block">{l s='Featured products' mod='homefeatured'}</h4>
	{if isset($products) AND $products}
		<div class="block_content" >
			<ul class="ul_home_fearture">
			{foreach from=$products item=product name=homeFeaturedProducts}
				<li class="ajax_block_product {if $smarty.foreach.homeFeaturedProducts.first}first_item{elseif $smarty.foreach.homeFeaturedProducts.last}last_item{else}item{/if}">
					<a href="{$product.link}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}"  /></a>
					<h3 class="name_product"><a href="{$product.link}">{$product.name|truncate:35:'...'|escape:'htmlall':'UTF-8'}</a></h3>
					{if $product.show_price AND !isset($restricted_country_mode) AND !$PS_CATALOG_MODE}<p class="price_container">{if $priceDisplay && $product.reduction}<span class="price-discount">{displayWtPrice p=$product.price_without_reduction}</span>{/if}<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span></p>{else}<div style="height:21px;"></div>{/if}
				</li>
			{/foreach}
			</ul>
			<a id="prev_home_featured" class="prev btn" href="#">&lt;</a>
			<a id="next_home_featured" class="next btn" href="#">&gt;</a>
		</div>
	{else}
		<p>{l s='No featured products' mod='homefeatured'}</p>
	{/if}
	</div>
</div>
<script type="text/javascript">
	$(window).load(function(){
		$("ul.ul_home_fearture").carouFredSel({
			auto: false,
			responsive: true,
				width: '100%',
				prev: '#prev_home_featured',
				next: '#next_home_featured',
				swipe: {
					onTouch : true
				},
				items: {
					width: 350,
					height:'variable',
					visible: {
						min: 1,
						max: 5
					}
				},
				scroll: {
					items : 2 ,       //  The number of items scrolled.
					direction : 'left',    //  The direction of the transition.
					duration  : 500   //  The duration of the transition.
				}

		});
	});
</script>
<!-- /MODULE Home Featured Products -->
