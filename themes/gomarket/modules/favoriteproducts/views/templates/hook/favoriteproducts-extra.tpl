{if $isLogged}
<ul>
	{if !$isCustomerFavoriteProduct}
	<li id="favoriteproducts_block_extra_add" class="add">
		<img src="{$img_dir}pdp/mark.png" alt="{l s='Añadir producto a favoritos'}" width="100%" />
		Favoritos
	</li>
	{else}
	<li id="favoriteproducts_block_extra_remove">
		<img src="{$img_dir}pdp/marked.png" alt="{l s='Remover producto de favoritos'}" width="100%" />
		Favoritos
	</li>
	{/if}

	<li id="favoriteproducts_block_extra_added">
		<img src="{$img_dir}pdp/marked.png" alt="{l s='Añadir producto a favoritos'}" width="100%" />
		Favoritos
	</li>
	<li id="favoriteproducts_block_extra_removed">
		<img src="{$img_dir}pdp/mark.png" alt="{l s='Remover producto de favoritos'}" width="100%" />
		Favoritos
	</li>
</ul>
{else}
	<li id="favoriteproducts_block_extra_unlogged">
		<img src="{$img_dir}pdp/mark.png" alt="{l s='Añadir producto a favoritos'}" width="100%" />
		Favoritos
		{* <a href="{$link->getPageLink('my-account', true)}">{l s='Add this product to my list of favorites.' mod='favoriteproducts'}{l s='Remove this product from my favorite\'s list. ' mod='favoriteproducts'}</a> *}
	</li>
{/if}