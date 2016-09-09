{*
* 2007-2015 PrestaShop
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
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-5 text-right">
			<img src="{$module_dir|escape:'html':'UTF-8'}views/img/oneandone.png" />
		</div>
		<div class="col-xs-7 text-left">
			<br /><br /><h4>{l s='PrestaShop Official hosting partner' mod='oneandonehosting'}</h4>
		</div>
	</div>

	<hr />

	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-7 text-left">
				<p>
					<h4><strong>{l s='To install PrestaShop click on "Create an account on 1&1", choose Linux Unlimited , Unlimited Plus or Unlimited Pro. Then go on the 1&1 App Center, select "Click & Build" and look for PrestaShop' mod='oneandonehosting'}</strong></h4>
					<ul class="ul-spaced">
						<li>{l s='1&1 Expert App Support available 24 hours a day, 7 days a week – with advice and practical help specific to the application you’re using' mod='oneandonehosting'}</li>
						<li>{l s='Maximum availability thanks to the geo-redundant hosting of your website in two geographically separate data centres' mod='oneandonehosting'}</li>
						<li>{l s='1&1 SiteLock included, keeping your website virus-free' mod='oneandonehosting'}</li>
						<li>{l s='Guaranteed performance from 2 GB RAM (with the 1&1 Unlimited Plus package)' mod='oneandonehosting'}</li>
						<li>{l s='Domain included free with select packages (.co.uk, .com, .net, .org, .me.uk or .org.uk)' mod='oneandonehosting'}</li>
						<li>{l s='Unlimited Webspace' mod='oneandonehosting'}</li>
						<li>{l s='Unlimited Websites' mod='oneandonehosting'}</li>
					</ul>
				</p>
			</div>
			<div class="col-xs-5 text-right">
			<img class="img-responsive" src="{$module_dir|escape:'htmlall':'UTF-8'}views/img/{$screenshot|escape:'htmlall':'UTF-8'}.png" />
			</div>
		</div>
				<br />
				<p class="text-center">
					<strong>
						<a href="{$landing_page|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-primary" id="create-account-btn">{l s='Create an account on 1&1' mod='oneandonehosting' }</a>
					</strong>
				</p>
			</div>
	</div>
</div>
