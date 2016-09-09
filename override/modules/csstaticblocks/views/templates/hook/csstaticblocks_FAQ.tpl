<!-- Static Block FAQ module -->
{foreach from=$block_list item=block}
	{if isset($block->content[(int)$cookie->id_lang])}
		{$block->content[(int)$cookie->id_lang]}
	{/if}
{/foreach}
<!-- /Static block FAQ module -->