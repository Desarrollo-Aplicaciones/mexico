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

{capture name=path}{l s='My account'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

{* <h1>{l s='My account'}</h1> *}
{if isset($account_created)}
	<p class="success">
		{l s='Your account has been created.'}
	</p>
{/if}
{if !$smarty.get.FAQ}
<p class="title_block">
	{l s='Welcome to my account.'}
</p>
{/if}
{include file="$tpl_dir./my-account-menu.tpl"}
{if !$smarty.get.FAQ}
	<script type="text/javascript">
		window.location.assign("{$link->getPageLink('identity', true)}")
	</script>
{/if}
{* <ul class="myaccount_lnk_list">
	<li>
		<a href="{$link->getPageLink('addresses', true)}" title="{if $has_customer_an_address}{l s='Add my first address'}{else}{l s='Addresses'}{/if}">
			<div class="img_cont"><img src="{$img_dir}my-account/addresses.png" alt="{if $has_customer_an_address}{l s='Add my first address'}{else}{l s='Addresses'}{/if}" class="icon" /></div>
			{l s='My addresses'}
		</a>
	</li>

	<li>
		<a href="{$link->getPageLink('identity', true)}" title="{l s='Information'}">
			<div class="img_cont"><img src="{$img_dir}my-account/info.png" alt="{l s='Information'}" class="icon" /></div>
			{l s='My personal information'}
		</a>
	</li>

	<li>
		<a href="{$link->getPageLink('history', true)}" title="{l s='Orders'}">
			<div class="img_cont"><img src="{$img_dir}my-account/history.png" alt="{l s='Orders'}" class="icon" /></div>
			{l s='History and details of my orders'}
		</a>
	</li>

	{if $returnAllowed}
		<li><a href="{$link->getPageLink('order-follow', true)}" title="{l s='Merchandise returns'}"><img src="{$img_dir}icon/return.gif" alt="{l s='Merchandise returns'}" class="icon" /> {l s='My merchandise returns'}</a></li>
	{/if}
	<li><a href="{$link->getPageLink('order-slip', true)}" title="{l s='Credit slips'}"><img src="{$img_dir}icon/slip.gif" alt="{l s='Credit slips'}" class="icon" /> {l s='My credit slips'}</a></li>
	{if $voucherAllowed}
		<li><a href="{$link->getPageLink('discount', true)}" title="{l s='Vouchers'}"><img src="{$img_dir}icon/voucher.gif" alt="{l s='Vouchers'}" class="icon" /> {l s='My vouchers'}</a></li>
	{/if}
	{$HOOK_CUSTOMER_ACCOUNT}
</ul>
<p><a class="home" href="{$base_dir}" title="{l s='Home'}">{l s='Home'}</a></p> *}