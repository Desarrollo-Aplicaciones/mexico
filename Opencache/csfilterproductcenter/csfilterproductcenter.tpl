<!-- /CS Filter Product Module -->
{if count($tabs) > 0}
<div class="cs_home_filter_product grid_14">
	{assign var="index" value=1}
	{foreach from=$tabs item=tab name=tabs}
		<div class="cs_home_filter_row cat_block {if $index > 2} filter_row_{$hook}_1{else}filter_row_{$hook}_{$index}{/if}">
			<h4 class="title_{$tab->id}">{$tab->title[(int)$cookie->id_lang]}</h4>
			<div class="products_content_tab">
			{if $tab->product_list}
			<div class="slide_image grid_5">
					<ul id="ul_category_featured_{$tab->id}">
					{foreach from=$tab->product_list item=product name=filterProducts}
						<li class="ajax_block_product">
						<div class="cs_content_p">
						<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link product_image">
						<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" /></a>
						<div class="title_name_product">
						<h3 class="name_product"><a href="{$product.link}">{$product.name|truncate:25:'...'|escape:'htmlall':'UTF-8'}</a></h3></div>
						<p class="description">
							{$product.description|strip_tags:'UTF-8'|truncate:55:'...'}
						</p>
						<div class="shop_new_{$tab->id}"><a href="{$product.link}" title="{$product.name|escape:'htmlall':'UTF-8'}">{l s='comprar' mod='csfilterproductcenter'}</a></div>
						</div>
						</li>
					{/foreach}
					</ul>
					<a id="prev_featured_cat_{$tab->id}" class="prev btn" href="#">&lt;</a>
					<a id="next_featured_cat_{$tab->id}" class="next btn" href="#">&gt;</a>
			</div>
			<script type="text/javascript">
								/*$(window).resize(function(){
									if ($(window).width() > 767 && $(window).width() <= 960)
									{
										$(".pro_filter_5").remove();
										$(".pro_filter_6").remove();
									}
								});*/
								$(window).load(function(){
									$("#ul_category_featured_{$tab->id}").carouFredSel({
										auto: false,
										responsive: true,
											width: '100%',
											height : 'variable',
											prev: '#prev_featured_cat_{$tab->id}',
											next: '#next_featured_cat_{$tab->id}',
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
									$("#ul_category_featured_list_{$tab->id}").carouFredSel({
										auto: false,
										responsive: true,
											width: '100%',
											height : 'variable',
											prev: '#prev_featured_cat_list_{$tab->id}',
											next: '#next_featured_cat_list_{$tab->id}',
											swipe: {
												onTouch : true
											},
											items: {
												width: 130,
												height : 'auto',
												visible: {
													min: 2,
													max: 3
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
			<div class="products_home_filter_product">
				{if $tab->product_list}
					<div class="product_list_filter ">
					<ul id="ul_category_featured_list_{$tab->id}">
						{foreach from=$tab->product_list item=product name=product_list}
						{if $smarty.foreach.product_list.first || $smarty.foreach.product_list.iteration%2 == 1}
						<li style="float:left" class="ajax_block_product">
						{/if}
							<div class="pro_filter_{$smarty.foreach.product_list.iteration} cs_product {if $smarty.foreach.product_list.first}first_item{elseif $smarty.foreach.product_list.last}last_item{else}item{/if}">
							<div class="image">	
							<a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link product_image">
							<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'medium_default')}" alt="{$product.legend|escape:'htmlall':'UTF-8'}" /></a></div>
							<div class="name_product"><h3><a href="{$product.link}">{$product.name|truncate:20:'...'|escape:'htmlall':'UTF-8'}</a></h3></div>
							</div>
						{if $smarty.foreach.product_list.last || $smarty.foreach.product_list.iteration%2 == 0}
						</li>
						{/if}
						{/foreach}
					</ul>
					<a id="prev_featured_cat_list_{$tab->id}" class="prev btn" href="#">&lt;</a>
					<a id="next_featured_cat_list_{$tab->id}" class="next btn" href="#">&gt;</a>
					</div>
				{/if}
			</div>
		</div>
	</div>
	{math equation="temp + nb" temp=$index nb=1 assign=index}
	{/foreach}
</div>
{/if}
<!-- /CS Filter Product Module -->
