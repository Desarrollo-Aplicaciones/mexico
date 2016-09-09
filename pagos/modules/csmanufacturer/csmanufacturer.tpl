<!-- CS Manufacturer module -->
<br><br>
<div class="manufacturerContainer">
	<div class="list_manufacturer responsive">
		<ul id="scroller">
		{foreach from=$manufacs item=manufacturer name=manufacturer_list}
			{if file_exists($ps_manu_img_dir|cat:$manufacturer.id_manufacturer|cat:'.jpg')}
			<li class="{if $smarty.foreach.product_list.first}first_item{elseif $smarty.foreach.product_list.last}last_item{/if}">
				<a href="{$link->getmanufacturerLink($manufacturer.id_manufacturer, $manufacturer.link_rewrite)}" title="{$manufacturer.name|escape:'htmlall':'UTF-8'}">
				<img src="{$img_manu_dir}{$manufacturer.id_manufacturer|escape:'htmlall':'UTF-8'}.jpg" alt="{$manufacturer.name|escape:'htmlall':'UTF-8'}" /></a>
			</li>
			{/if}
		{/foreach}
		</ul>
			<a id="prev_cs_manu" class="prev btn" href="#">&lt;</a>
			<a id="next_cs_manu" class="next btn" href="#">&gt;</a>
	</div>
</div>
<script type="text/javascript">
	$(window).load(function(){
		$("#scroller").carouFredSel({
			auto: true,
			responsive: true,
				width: '100%',
				height : 'variable',
				prev: '#prev_cs_manu',
				next: '#next_cs_manu',
				swipe: {
					onTouch : true
				},
				items: {
					width: 200,
					height : 'auto',
					visible: {
						min: 1,
						max: 6
					}
				},
				scroll: {
					items : 2 ,       //  The number of items scrolled.
					direction : 'left'
				}

		});
	});
</script>
<!-- /CS Manufacturer module -->

