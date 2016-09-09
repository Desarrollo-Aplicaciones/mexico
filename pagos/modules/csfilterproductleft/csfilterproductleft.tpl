<!-- CS Home Tab module -->
<div class="home_top_tab grid_5 alpha">
{if count($tabs) > 0}
<div class="cs_home_none_tab">
{assign var="index" value=1}
	{foreach from=$tabs item=ftab name=tabs}
	{assign var="tab" value=$ftab.info}
		<div class="cs_hometab_row cat_block {if $index > 2} none_tab_row_{$hook}_1{else}none_tab_row_{$hook}_{$index}{/if}">
			<h4>{$tab->title[(int)$cookie->id_lang]}</h4>
			<div class="products_none_tab">
				<span class="icon">{$tab->title[(int)$cookie->id_lang]}</span>
				{if $tab->product_list}
					<div class="list_call_carousel">
						<ul class="call_carousel" id="call_carousel_{$tab->id}">
						{foreach from=$tab->product_list item=product name=product_list}
							<li class="ajax_block_product">
							<a href="{$product.link}" class="product_image"><img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}"/></a>
							<h3 class="name_product"><a href="{$product.link}" >{$product.name|truncate:22:'...'|escape:'htmlall':'UTF-8'}</a></h3>
							<p class="description">
								{$product.description|strip_tags:'UTF-8'|truncate:50:'...'}
							</p>
						</li>
						{/foreach}
						</ul>
						<a id="prev_cs_home_none_{$tab->id}" class="prev btn" href="#">&lt;</a>
						<a id="next_cs_home_none_{$tab->id}" class="next btn" href="#">&gt;</a>
					</div>
				{/if}
				<a class="more_view" href="{$ftab.link}">{l s='ver m&aacute;s' mod='csfilterproductleft'}</a>
			</div>
		</div>
		<script type="text/javascript">
		$(window).load(function(){
		$("#call_carousel_{$tab->id}").carouFredSel({
			auto: false,
			responsive: true,
				width: '100%',
				prev: '#prev_cs_home_none_{$tab->id}',
				next: '#next_cs_home_none_{$tab->id}',
				swipe: {
					onTouch : true
				},
				items: {
					width: 198,
					visible: {
						min: 1,
						max: 1
					}
				},
				scroll: {
					items : 1 ,       //  The number of items scrolled.
					direction : 'left',
					duration  : 500   //  The duration of the transition.
				}

		});
	});
</script>
{math equation="temp + nb" temp=$index nb=1 assign=index}
	{/foreach}
</div>
{/if}
</div>
<!-- /CS Home Tab module -->
