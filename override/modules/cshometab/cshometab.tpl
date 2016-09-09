<!-- CS Home Tab module -->
<div class="home_top_tab_category">
{if count($tabs) > 0}
<div id="tabs">
	<ul id="ul_cs_tab">
		{foreach from=$tabs item=ftab name=tabs}
		{assign var="tab" value=$ftab.info}
			<li class="{if $smarty.foreach.tabs.last}last{/if} refreshCarousel">
				<a href="#tabs-{$smarty.foreach.tabs.iteration}">{$tab->title[(int)$cookie->id_lang]}</a>
			</li>
		{/foreach}
	</ul>
	{foreach from=$tabs item=ftab name=tabs}
	{assign var="tab" value=$ftab.info}
	{assign var="category" value=$ftab.category}
		<div class="title_tab_hide_show" style="display:none">
			{$tab->title[(int)$cookie->id_lang]}
			<input type='hidden' value='{$smarty.foreach.tabs.iteration}' />
		</div>
		<div id="tabs-{$smarty.foreach.tabs.iteration}">
		{if $category->id_image}
			<div class="cat_banner">
			<img src="{$link->getCatImageLink($category->link_rewrite[(int)$cookie->id_lang], $category->id_image, 'category_default')}" alt="{$category->name[(int)$cookie->id_lang]|escape:'htmlall':'UTF-8'}" title="{$category->name[(int)$cookie->id_lang]|escape:'htmlall':'UTF-8'}"/>
			</div>
		{/if}
		<div class="cs_right_cat_tab">
			<div class="products">
				{if $tab->product_list}
				<div class="list_carousel responsive">
					<ul class="product-list">
					{foreach from=$tab->product_list item=product name=product_list}
						<li class="ajax_block_product {if $smarty.foreach.product_list.first}first_item{elseif $smarty.foreach.product_list.last}last_item{/if}">
						<a href="{$product.link}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.name|escape:html:'UTF-8'}" /></a>
						<h3 class="name_product"><a href="{$product.link}">{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a></h3>
					
						<div class="products_list_price">
							{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
								<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
							{/if}
						</div>
					</li>
					{/foreach}
					</ul>
					<div class="clearfix"></div>
				</div>
				{/if}
			</div>
				<h1>
				<a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'htmlall':'UTF-8'}" title="{$category->name[(int)$cookie->id_lang]|escape:'htmlall':'UTF-8'}" class="img">
				{$category->name[(int)$cookie->id_lang]|escape:'htmlall':'UTF-8'}</a></h1>
			{if $category->description}
				<div class="info_cat">
				{if strlen($category->description[(int)$cookie->id_lang]) > 120}
					<p>{$category->description[(int)$cookie->id_lang]|truncate:120}</p>
				{else}
					<p>{$category->description[(int)$cookie->id_lang]}</p>
				{/if}
				</div>
			{/if}
		</div>
	</div>
	{/foreach}
</div>
<script type="text/javascript">
	$('div.title_tab_hide_show').first().addClass('selected');
	$(document).ready(function() {
		cs_resize();
		$('#tabs').on('click', '.title_tab_hide_show', function() {
			if($(this).hasClass('selected')) {
				$(this).removeClass('selected');
				var id = $(this).find('input').val();
				$('#tabs-'+id).hide();
			} else {
				$(this).addClass('selected');
				var id = $(this).find('input').val();
				$('#tabs-'+id).show();
			}
		});
	});

	$(window).resize(function() {
		cs_resize();
	});
	function cs_resize()	{
		if(getWidthBrowser() < 767){ //767
			$('#tabs').tabs('destroy');
			$('.title_tab').hide();
			$('#ul_cs_tab').hide();
			$('#tabs div.title_tab_hide_show').show();
			$('#tabs div.title_tab_hide_show').addClass('selected');
		} else {
			$('#ul_cs_tab').show();
			$('#tabs div.title_tab_hide_show').hide();
			$('#tabs').tabs();
		}
	}
</script>
{/if}
</div>
<!-- /CS Home Tab module -->
