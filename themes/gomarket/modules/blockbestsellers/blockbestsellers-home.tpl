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

<!-- MODULE Home Block best sellers -->
<div id="best-sellers_block_center" class="block products_block">
	<h4 class="title_block">{l s='Top sellers' mod='blockbestsellers'}</h4>
	{if isset($best_sellers) AND $best_sellers}
		<div class="block_content">
			<ul class="ul_best_seller">
			{foreach from=$best_sellers item=product name=myLoop}
				<li style="border-bottom:0" class="ajax_block_product {if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if}">
					<a href="{$product.link}" title="{$product.name|escape:html:'UTF-8'}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
					<h5 class="s_title_block"><a href="{$product.link}" title="{$product.name|truncate:32:'...'|escape:'htmlall':'UTF-8'}">{$product.name|truncate:27:'...'|escape:'htmlall':'UTF-8'}</a></h5>
					<div class="product_desc"><a href="{$product.link}" title="{l s='More' mod='blockbestsellers'}">{$product.description_short|strip_tags|truncate:130:'...'}</a></div>
				</li>
			{/foreach}
			</ul>
			<a id="prev_best_seller" class="prev btn" href="#">&lt;</a>
			<a id="next_best_seller" class="next btn" href="#">&gt;</a>
			<p class="clearfix" style="padding: 5px;"><a style="float:right;" href="{$link->getPageLink('best-sales')}" title="{l s='All best sellers' mod='blockbestsellers'}" class="button_large">{l s='View more' mod='blockbestsellers'}</a></p>
		</div>
	{else}
		<p>{l s='No best sellers' mod='blockbestsellers'}</p>
	{/if}
	<br class="clear"/>
</div>
<script type="text/javascript">
	$(window).load(function(){
		$("ul.ul_best_seller").carouFredSel({
			auto: false,
			responsive: true,
				width: '100%',
				prev: '#prev_best_seller',
				next: '#next_best_seller',
				swipe: {
					onTouch : true
				},
				items: {
					width: 200,
					visible: {
						min: 1,
						max: 1
					}
				},
				scroll: {
					items : 1,       //  The number of items scrolled.
					direction : 'left',    //  The direction of the transition.
					duration  : 500   //  The duration of the transition.
				}

		});
	});
</script>
<!-- /MODULE Home Block best sellers -->
