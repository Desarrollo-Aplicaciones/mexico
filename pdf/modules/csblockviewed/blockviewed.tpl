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

<!-- CS Block Viewed products -->
<div id="viewed-products_block_left" class="block products_block">
	<h4 class="title_block">{l s='Lastest views' mod='csblockviewed'}</h4>
	<div class="block_content">
		<ul class="products clearfix">
			{foreach from=$productsViewedObj item=viewedProduct name=myLoop}
				<li class="clearfix{if $smarty.foreach.myLoop.last} last_item{elseif $smarty.foreach.myLoop.first} first_item{else} item{/if}">
					<a href="{$viewedProduct.link}" title="{l s='About' mod='csblockviewed'} {$viewedProduct.name|escape:html:'UTF-8'}" class="content_img">
						<img src="{if isset($viewedProduct.id_image) && $viewedProduct.id_image}{$link->getImageLink($viewedProduct.link_rewrite, $viewedProduct.id_image, 'small_default')}{else}{$img_prod_dir}{$lang_iso}-default-small_default.jpg{/if}" alt="{$viewedProduct.legend|escape:html:'UTF-8'}" />
					</a>		
					<h3 class="name_product"><a href="{$viewedProduct.link}" title="{l s='About' mod='csblockviewed'} {$viewedProduct.name|escape:html:'UTF-8'}">{$viewedProduct.name|truncate:35:'...'|escape:html:'UTF-8'}</a></h3>
					<div class="products_list_price">
					<span class="price">{if !$priceDisplay}{displayWtPrice p=$viewedProduct.price}{else}{displayWtPrice p=$viewedProduct.price_tax_exc}{/if}</span>
					
				</div>
						
				</li>
			{/foreach}
		</ul>
	</div>
</div>
<!-- CS Block Viewed products -->
