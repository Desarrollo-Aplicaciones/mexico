<style>
	@media only screen and (min-width:1001px)  {
		#loginizq { width: 666px; height: auto; float: left;}
	}

	/* Tablet Portrait size to standard 960 (devices and browsers) */
	@media only screen and (min-width: 768px) and (max-width: 1000px) {
		#loginizq { width: 376px; height: 252px; float: left;}
	}
	/* Small Tablet Portrait size to standard 600 (devices and browsers) */
	@media only screen and (min-width: 480px) and (max-width: 767px) {
		#loginizq { display:none;}
	}
	/* All Mobile Sizes (devices and browser) */
	@media only screen and (min-width: 200px) and (max-width: 479px) {
		#loginizq { display:none;}
	}
</style>


<!-- MODULE Block category le -->

<div id="loginizq">
	
	{if $publicidad !== false}	
		{if $tipopublicidad == 'banner'}		
			{if $linkpublicidad != ''}<a href="{$linkpublicidad}">{/if}
				<img id="loginizqimg" src="{$modules_dir}../img/imagen.php?imagen={$publicidad}" width="100%" height="100%">
			{if $linkpublicidad != ''}</a>{/if}
		{/if}
	{else}
		<p>&raquo; {l s='En este momento no hay imagenes' mod='cspublicidadfl'}</p>		
	{/if}
</div>