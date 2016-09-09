
<!-- Banner Superior -->
{if $publicidad !== false}	
	<div id="prodsupban">
		{if $linkpublicidad != ''}<a href="{$linkpublicidad}">{/if}
			<img id="homesupimg" src="{$modules_dir}../img/imagen.php?imagen={$publicidad}" width="100%" height="100%" >
		{if $linkpublicidad != ''}</a>{/if}
	</div>
{else}
	<p>&raquo; {l s='En este momento no hay imagenes' mod='cspublicidadfl'}</p>
{/if}
