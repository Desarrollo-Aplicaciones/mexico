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

<!-- MODULE Block new products -->
<div id="new-products_block_right" class="block products_block grid_5 omega">
	<div class="new_content">
	<h4 class="title_block"><a href="{$link->getPageLink('new-products')}" title="{l s='Nuevos Productos' mod='csblocknewproducts'}">{l s='Nuevos Productos' mod='csblocknewproducts'}</a></h4>
	<div class="block_content">
	{if $new_products !== false}
		<ul class="cs_new_product">
		{foreach from=$new_products item='product' name='newProducts'}
				<li class="ajax_block_product item">
				<h3 class="name_product"><a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}">{$product.name|truncate:50:'...'|strip_tags|escape:html:'UTF-8'}</a></h3>
				<div class="products_list_price">
				{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
					<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
				{/if}
				</div>
                                <a class="product_image" href="{$product.link}" title="{$product.legend|escape:html:'UTF-8'}"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.legend|escape:html:'UTF-8'}"/></a></li>
		{/foreach}
		</ul>
		<a id="prev_cs_new_product" class="prev" href="#">&lt;</a>
		<a id="next_cs_new_product" class="next" href="#">&gt;</a>
	{else}
		<p>&raquo; {l s='En este momento no hay nuevos productos disponibles' mod='csblocknewproducts'}</p>
	{/if}
	</div>
	</div>
</div>
<script type="text/javascript">
var item = 1;

						$(window).resize(function(){
						if ($.browser.msie  && parseInt($.browser.version, 10) === 7) {
							return;
						}
						else
						{
							if(getWidthBrowser() < 1023)
									var item = 3;
								else 
									var item = 1;
									//alert(item);
								$("ul.cs_new_product").carouFredSel({
									auto: false,
									responsive: true,
										width: '100%',
										prev: '#prev_cs_new_product',
										next: '#next_cs_new_product',
										swipe: {
											onTouch : true
										},
										items: {
											width: 130,
											visible: {
												min: 1,
												max: item
											}
										},
										scroll: {
											items : item ,       //  The number of items scrolled.
											direction : 'left',    //  The direction of the transition.
											duration  : 500   //  The duration of the transition.
										}

								});
						}
						});
							$(window).load(function(){
								if(getWidthBrowser() < 1023)
									var item = 3;
								else 
									var item = 1;
								$("ul.cs_new_product").carouFredSel({
									auto: false,
									responsive: true,
										width: '100%',
										prev: '#prev_cs_new_product',
										next: '#next_cs_new_product',
										swipe: {
											onTouch : true
										},
										items: {
											width: 130,
											visible: {
												min: 1,
												max: item
											}
										},
										scroll: {
											items : item ,       //  The number of items scrolled.
											direction : 'left',    //  The direction of the transition.
											duration  : 500   //  The duration of the transition.
										}

								});
							});
		</script>
<!-- /MODULE Block new products -->
