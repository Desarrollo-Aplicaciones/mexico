<!-- CS Filter Product Right Module -->
{if count($tabs) > 0}
<div class="cs_home_filter_product_right grid_5 omega">
	{foreach from=$tabs item=tab name=tabs}
		<div class="cs_home_filter_right_row cat_block filter_right_row_{$tab->id}">
			<h4 class="title_right_{$tab->id}">{$tab->title[(int)$cookie->id_lang]}</h4>
			{if $tab->product_list}
				<div class="filter_right">
					<div class="product_list_filter_right">
					<ul id="ul_filter_right_{$tab->id}">
						{foreach from=$tab->product_list item=product name=product_list}
						{if $smarty.foreach.product_list.first || $smarty.foreach.product_list.iteration%3 == 1}
						<li class="ajax_block_product">
						{/if}
							<div class="item_product">							
							<div class="image">	
							<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link product_image" >
							<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')}" /></a></div>
							<h3 class="name_product"><a href="{$product.link}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|truncate:20:'...'|escape:'htmlall':'UTF-8'}</a></h3>
							<div class="products_list_price">
								{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
									{if $priceDisplay && $product.reduction}<span class="price-discount">{displayWtPrice p=$product.price_without_reduction}</span>{/if}
									<span class="price">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>
								{/if}
							</div>						
							
							</div>
						{if $smarty.foreach.product_list.last || $smarty.foreach.product_list.iteration%3 == 0}
						</li>
						{/if}
						{/foreach}
					</ul>
					<a id="prev_filter_right_{$tab->id}" class="prev btn" href="#">&lt;</a>
					<a id="next_filter_right_{$tab->id}" class="next btn" href="#">&gt;</a>
				</div>
			</div>
			<script type="text/javascript">
								$(window).load(function(){
									$("#ul_filter_right_{$tab->id}").carouFredSel({
										auto: false,
										responsive: true,
											width: '100%',
											height: 'variable',
											prev: '#prev_filter_right_{$tab->id}',
											next: '#next_filter_right_{$tab->id}',
											swipe: {
												onTouch : true
											},
											items: {
												width: 130,
												height : 'auto',
												visible: {
													min: 1,
													max: 1
												}
											},
											scroll: {
												items : 1 ,       //  The number of items scrolled.
												direction : 'left',    //  The direction of the transition.
												duration  : 500   //  The duration of the transition.
											}

									});
								});
			</script>
		 {/if}
	 </div>
	{/foreach}
</div>
{/if}
<!-- CS Filter Product Right Module -->
