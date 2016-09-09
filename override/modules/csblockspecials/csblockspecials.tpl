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

<!-- MODULE Block specials -->
<div id="cs_special_block_right" class="block products_block csblockspecials grid_5 alpha">
	<h4 class="title_block"><a href="{$link->getPageLink('prices-drop')}" title="{l s='Producto de la Semana' mod='csblockspecials'}">{l s='Producto de la Semana' mod='csblockspecials'}</a></h4>
	<div class="block_content">

{if $special}
		<ul class="products clearfix">
			<li class="ajax_block_product">
				<a href="{$special.link}" class="product_image"><img src="{$link->getImageLink($special.link_rewrite, $special.id_image, 'home_default')}" /></a>
				<h3 class="s_title_block"><a href="{$special.link}">{$special.name|truncate:35:'...'|escape:html:'UTF-8'}</a></h3>
				{if !$PS_CATALOG_MODE}
					<span class="price">{if !$priceDisplay}{displayWtPrice p=$special.price}{else}{displayWtPrice p=$special.price_tax_exc}{/if}</span>
				{/if}
			</li>
		</ul>
{else}
		<p>{l s='En este momento no hay productos en esta categoria' mod='csblockspecials'}</p>
{/if}
	</div>
</div>
<!-- /MODULE Block specials -->