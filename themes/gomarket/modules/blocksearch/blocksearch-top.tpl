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
<div id="static_header">
	<div class="flecha_logo"></div>
	<div class="container_24">
	<div class="main_logo">
		<a id="header_logo" href="{$base_dir}" title="{$shop_name|escape:'htmlall':'UTF-8'}">
			<img class="logo_small" src="{$base_dir}img/logo_small.jpg" alt="{$shop_name|escape:'htmlall':'UTF-8'}" />
			<img class="logo" src="{if isset($logo_url)}{$logo_url}{else}{$base_dir}img/logo.jpg{/if}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" />
		</a>
	</div>
	<!-- block seach mobile -->
	{if isset($hook_mobile)}
	<div class="input_search" data-role="fieldcontain">
		<form method="get" action="{$link->getPageLink('search')}" id="searchbox">
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="orderway" value="desc" />
			<input class="search_query" type="search" id="search_query_top" name="search_query" placeholder="{l s='Search' mod='blocksearch'}" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
		</form>
	</div>
	{else}
	<div id="search_block_top">

		<form method="get" action="{$link->getPageLink('search')}" id="searchbox">
				<input type="hidden" name="controller" value="search" />
				<input type="hidden" name="orderby" value="position" />
				<input type="hidden" name="orderway" value="desc" />
				<input class="search_query" type="text" id="search_query_top" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{else}{l s='Buscar en toda la tienda...' mod='blocksearch'} {/if}"  onfocus="this.value=''; this.placeholder='';" onblur="if (this.value =='') this.value='{l s='Buscar en toda la tienda...' mod='blocksearch'}'" />
				<input type="submit" id="submit_search_instant" name="submit_search" value="{l s='Search' mod='blocksearch'}" class="button" />
		</form>
	</div>
	{include file="$self/blocksearch-instantsearch.tpl"}
	{/if}
	<!-- /Block search module TOP -->
