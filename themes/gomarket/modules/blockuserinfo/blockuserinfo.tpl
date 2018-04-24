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
	<div class="header_account_info">
		<p id="header_user_info">
			{if $logged}
				<a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
					<span>Hola {if $cookie->customer_firstname|strstr:" ":true}{$cookie->customer_firstname|strstr:" ":true}{else}{$cookie->customer_firstname}{/if}</span>
				</a>
				<br>
				<a href="{$link->getPageLink('index', true, NULL, "mylogout")}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" id="login" rel="nofollow">
					{l s='Log out' mod='blockuserinfo'}
				</a>
			{else}
				<a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow" id="login">
					{l s='Ingresar' mod='blockuserinfo'}
				</a>
				<br>
				<a href="{$link->getPageLink('authentication', true)}?reg=5" title="{l s='Login to your customer account' mod='blockuserinfo'}" class="login" id="login" rel="nofollow">
					{l s='Registrarme' mod='blockuserinfo'}
				</a> 
			{/if}
		</p>
	</div>
	<div class="header_account_avatar">
		<a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
			<img src="{$img_dir}{if $logged}logged_{/if}avatar.png" />
		</a>
	</div>
	{if !$PS_CATALOG_MODE}
		<div class="shopping_cart" id="shopping_cart">
			<a href="{$link->getPageLink($order_process, true)}&paso=inicial" title="{l s='View my shopping cart' mod='blockuserinfo'}" rel="nofollow">
				<div style="height:30px;">
					<img src="{$img_dir}{if $cart_qties == 0}empty{else}full{/if}_cart.png" class="shopping_cart3"/>
					<img src="{$img_dir}{if $cart_qties == 0}empty{else}full{/if}_cart4.png" class="shopping_cart4"/>
				</div>
				<span id="numero" class="ajax_cart_quantity{if $cart_qties == 0} hidden{/if}">{$cart_qties}</span>
				{* <span id="numero" class="ajax_cart_no_product{if $cart_qties > 0} hidden{/if}">{l s='(empty)' mod='blockuserinfo'}</span> *}
			</a>
		</div>
	{/if}
	<!-- /Block user information module HEADER -->
	</div>
</div>
<!--Small elements, solo se activan en responsive-->
<!--MEGA MENU-->
<div class="sf-contener_small">
	<div class="container_24" id="header_account_small">
		<div class="header_account_info_small">
			<div class="header_account_avatar_small">
				<a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
					<img src="{$img_dir}{if $logged}logged_{/if}avatar.png" />
				</a>
			</div>
			<div class="header_account_label_small">
			{if $logged}
				<a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" class="account" rel="nofollow">
					<span>Hola {if $cookie->customer_firstname|strstr:" ":true}{$cookie->customer_firstname|strstr:" ":true}{else}{$cookie->customer_firstname}{/if}</span>
				</a><!-- / 
				<a href="{$link->getPageLink('index', true, NULL, "mylogout")}" title="{l s='Log me out' mod='blockuserinfo'}" class="logout" rel="nofollow">
					{l s='Log out' mod='blockuserinfo'}
				</a-->
			{else}
				<!--a href="{$link->getPageLink('my-account', true)}" title="{l s='View my customer account' mod='blockuserinfo'}" rel="nofollow" >
					{l s='Ingresar' mod='blockuserinfo'}
				</a> / 
				<a href="{$link->getPageLink('my-account', true)}" title="{l s='Login to your customer account' mod='blockuserinfo'}" class="login" rel="nofollow">
					{l s='Registrarme' mod='blockuserinfo'}
				</a--> 
			{/if}
			</div>
		</div>
		{if isset($CS_MEGA_MENU)}{$CS_MEGA_MENU}{/if}
	</div>
</div>
<!--/MEGA MENU-->
<!--/Small elements, solo se activan en responsive-->