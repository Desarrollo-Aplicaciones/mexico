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

<!-- Block user information module HEADER -->
<div id="header_user" {if $PS_CATALOG_MODE}class="header_user_catalog"{/if}>
	{*<ul id="header_nav">
		<li id="your_account"><a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow">{l s='Mi Cuenta' mod='blockuserinfo'}</a></li>
	</ul>*}
	<a id="header_logo" href="{$base_dir}" title="{$shop_name|escape:'htmlall':'UTF-8'}">
							<img class="logo" src="{$logo_url}" alt="{$shop_name|escape:'htmlall':'UTF-8'}" />
						</a>

		<div style="float: left; position: relative;"><p id="header_user_info">
			{if $logged}
			{assign var="myStrname" value="`$cookie->customer_firstname` `$cookie->customer_lastname`"}		
				<a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span style="font-weight: 500; font-size: 100%;">{$myStrname|truncate:26},</span></a>
				<a href="{$link->getPageLink('index', true, NULL, "mylogout")}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" rel="nofollow" style="font-weight: 500; font-size: 100%;">{l s='Log out' mod='blockuserinfo'}</a>
			{else}
				<a href="{$link->getPageLink('my-account', true)}" title="{l s='Login to your customer account' mod='blockuserinfo'}" class="login" rel="nofollow">{l s='Login' mod='blockuserinfo'}</a> 
			{/if}
		</p></div>

		<!-- Block permanent links module HEADER -->
		<div style="float: left; position: relative;">
			<ul id="header_links">
				<li id="header_link_contact"><a href="{$link->getPageLink('contact', true)}" title="Contacto" style="font-weight: 500; font-size: 100%;">Contacto</a></li>
				{*<li id="header_link_sitemap"><a href="{$link->getPageLink('sitemap')}" title="Mapa Sitio" style="font-weight: 500; font-size: 100%;">Mapa Sitio</a></li>
				<li id="header_link_bookmark">
					<script type="text/javascript">writeBookmarkLink('{$come_from}', '{$meta_title|addslashes|addslashes}', '{l s='bookmark' mod='blockpermanentlinks' js=1}');</script>
				</li>*}
			</ul>
		</div>
		<!-- /Block permanent links module HEADER -->


</div>       
<!-- /Block user information module HEADER -->
<div id="linea_atencion">
	<span style="font-weight: 500; font-size: 80%; color: #FFFFFF; text-align: center;">Línea de Atención y Televentas: Bogotá 220 5249<br>Gratuita Nacional: 01800 913 3830</span>
</div>
        {if !$PS_CATALOG_MODE}
		<div id="shopping_cart">
			<a href="{$link->getPageLink($order_process, true)}&paso=inicial" title="{l s='View my shopping cart' mod='blockuserinfo'}" rel="nofollow">{l s='Cart' mod='blockuserinfo'}
			<span class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">({$cart_qties})</span>
			<span class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='blockuserinfo'}</span>
			</a>
		</div>
        {/if}