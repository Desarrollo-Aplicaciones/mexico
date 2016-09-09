{*
* 2007-2014 PrestaShop
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
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="page-head">
	<h2 class="page-title">
		{l s='Configure' mod='cartabandonmentpro'} {l s='Abandonned Cart Reminder' mod='cartabandonmentpro'}
	</h2>
	<ul class="breadcrumb page-breadcrumb">
		<li>
			<i class="icon-puzzle-piece"></i>Modules
		</li>
		<li>{l s='Abandonned Cart Reminder' mod='cartabandonmentpro'}</li>
		<li>
			<i class="icon-wrench"></i>
			{l s='Configure' mod='cartabandonmentpro'}
		</li>
	</ul>
	<div class="page-bar toolbarBox">
		<div class="btn-toolbar">
			<ul class="cc_button nav nav-pills pull-right">
				<li>
					<a id="desc-module-hook" class="toolbar_btn" href="{$module_form|escape:'htmlall':'UTF-8'}&amp;uninstall={$module_name|escape:'htmlall':'UTF-8'}" title="{l s='Uninstall' mod='cartabandonmentpro'}">
						<i class="process-icon-uninstall"></i>
						<div>{l s='Uninstall' mod='cartabandonmentpro'}</div>
					</a>
				</li>
				<li>
					<a id="desc-module-hook" class="toolbar_btn" href="{$module_reset|escape:'htmlall':'UTF-8'}" title="{l s='Reset' mod='cartabandonmentpro'}">
						<i class="process-icon-reset"></i>
						<div>{l s='Reset' mod='cartabandonmentpro'}</div>
					</a>
				</li>
				<li>
					<a id="desc-module-back" class="toolbar_btn" href="{$module_back|escape:'htmlall':'UTF-8'}" title="{l s='Back' mod='cartabandonmentpro'}">
						<i class="process-icon-back"></i>
						<div>{l s='Back' mod='cartabandonmentpro'}</div>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>