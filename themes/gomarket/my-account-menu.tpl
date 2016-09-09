<link rel="stylesheet" type="text/css" href="{$css_dir}my-account-menu.css">
<ul class="myaccount_lnk_list">

	<a href="{$link->getPageLink('identity', true)}" title="{l s='Information'}">
		<li id="menu_info">
			<div class="img_cont"><img src="{$img_dir}my-account/info.png" alt="{l s='Information'}" class="icon" /></div>
			<div class="title_cont">{*l s='My personal information'*}<span>Mis </span>datos<span> personales</span></div>
		</li>
	</a>

	<a href="{$link->getPageLink('addresses', true)}" title="{if $has_customer_an_address}{l s='Add my first address'}{else}{l s='Addresses'}{/if}">
		<li id="menu_addresses">
			<div class="img_cont"><img src="{$img_dir}my-account/addresses.png" alt="{if $has_customer_an_address}{l s='Add my first address'}{else}{l s='Addresses'}{/if}" class="icon" /></div>
			<div class="title_cont">{*l s='My addresses'*}<span>Mis </span>direcciones</div>
		</li>
	</a>

	<a href="{$link->getPageLink('history', true)}" title="{l s='Orders'}">
		<li id="menu_history">
			<div class="img_cont"><img src="{$img_dir}my-account/history.png" alt="{l s='Orders'}" class="icon" /></div>
			<div class="title_cont">{*l s='History and details of my orders'*}Historial<span> de pedidos</span></div>
		</li>
	</a>

	<a href="{$link->getPageLink('MyAccount', true)}?FAQ=1" title="{l s='FAQ'}">
		<li id="menu_FAQ">
			<div class="img_cont"><img src="{$img_dir}my-account/tos.png" alt="{l s='FAQ'}" class="icon" /></div>
			<div class="title_cont">{* l s='FAQ' *}Preguntas<span> frecuentes</span></div>
		</li>
	</a>
	
	{if !$in_footer}
	<a href="{$link->getModuleLink('favoriteproducts', 'account')|escape:'htmlall':'UTF-8'}" title="{l s='My favorite products.'}">
		<li id="menu_favorite">
			{if !$in_footer}
				<div class="img_cont"><img {if isset($mobile_hook)}src="{$module_template_dir}img/favorites.png" class="ui-li-icon ui-li-thumb"{else}src="{$img_dir}my-account/favorites.png" class="icon"{/if} alt="{l s='My favorite products.'}"/></div>
			{/if}
			<div class="title_cont">{l s='My favorite products.'}</div>
		</li>
	</a>
	{/if}
</ul>
{if $smarty.get.FAQ}
	<link rel="stylesheet" type="text/css" href="{$css_dir}FAQ.css">
	{$HOOK_CS_FAQ}
{/if}