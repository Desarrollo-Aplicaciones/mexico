{if $MENU != ''} 
    <link rel="stylesheet" type="text/css" href="{$css_dir}modules/blocktopmenu/css/opt-in.css" />

    <!-- Menu -->
	<div class="sf-contener clearfix" id="contenedor_menu_visual">
		<ul class="sf-menu clearfix" id="menu_parent">
			{$MENU}
			{if $MENU_SEARCH}
				<li class="sf-search noBack" style="float:right">
					<form id="searchbox" action="{$link->getPageLink('search')}" method="get">
						<p>
							<input type="hidden" name="controller" value="search" />
							<input type="hidden" value="position" name="orderby"/>
							<input type="hidden" value="desc" name="orderway"/>
							<input type="text" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|escape:'htmlall':'UTF-8'}{/if}" />
						</p>
					</form>
				</li>
			{/if}
		</ul>
	</div>
	<div class="sf-right">&nbsp;</div>

	<!--/ Menu -->
	<script>$('ul#menu_parent > li').last().addClass("last");$('ul#menu_parent > li').first().addClass("first");</script>       
{/if}