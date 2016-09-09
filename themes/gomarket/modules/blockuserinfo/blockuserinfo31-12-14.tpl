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
	
	
        {if !$PS_CATALOG_MODE}
		<div id="shopping_cart">
			<a href="{$link->getPageLink($order_process, true)}&paso=inicial" title="{l s='View my shopping cart' mod='blockuserinfo'}" style="width: 100%;" rel="nofollow"><span id="nombreCarrito">{l s='Cart' mod='blockuserinfo'}</span>
			<span id="numero" class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">({$cart_qties})</span>
			<span id="numero" class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='blockuserinfo'}</span>
			</a>
		</div>
        {/if}
	<p id="header_user_info">
		{if $logged}
			<a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow"><span style="color:#ffffff!important;">Hola {$cookie->customer_firstname} {$cookie->customer_lastname}</span></a>
			<ul id="header_nav">
				<li id="your_account"><a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="your_account" rel="nofollow">{l s='Ver mi Cuenta' mod='blockuserinfo'}</a></li>
			</ul>
			<a href="{$link->getPageLink('index', true, NULL, "mylogout")}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" id="login" rel="nofollow">{l s='Log out' mod='blockuserinfo'}</a>
		{else}
		<ul id="header_nav">
		<li id="your_account"><a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow" id="login">{l s='Ingresar a mi Cuenta' mod='blockuserinfo'}</a></li>
	</ul>
			<a href="{$link->getPageLink('my-account', true)}" title="{l s='Login to your customer account' mod='blockuserinfo'}" class="login" id="login" rel="nofollow">{l s='Registrarme' mod='blockuserinfo'}</a> 
		{/if}
	</p>
</div>       
<!-- /Block user information module HEADER -->
