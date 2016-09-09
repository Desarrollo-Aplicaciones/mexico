{*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<style>

	
	@media only screen and (min-width:1001px)  {
		#catizq { width: 675px; height: 254px; float: left;}
		#catizqban { width: 100% !important; float: left;}
		
	}

	/* Tablet Portrait size to standard 960 (devices and browsers) */
	@media only screen and (min-width: 768px) and (max-width: 1000px) {
		#catizq { width: 424px; height: 254px; float: left;}
		#catizqban { width: 100% !important; float: none;}
		#catizqimg { width: 675px;}
	}
	
	
	/* Small Tablet Portrait size to standard 600 (devices and browsers) */
	@media only screen and (min-width: 480px) and (max-width: 767px) {
		#catizq { width: 100% !important; height: 160px; text-align: center;}
		#catizqban{ float: none; width: 100% !important; height: auto; }
		#catizqimg { width: 100% !important; height: auto; }
	}

	/* All Mobile Sizes (devices and browser) */
	@media only screen and (min-width: 200px) and (max-width: 479px) {
		#bc { margin-top: 0 !important;}
		#bann_category { display: none;}
		#catizq { width: 100% !important; height: 107px; text-align: center;}
		#catizqban{ float: none; width: 100% !important; height: auto; }
		#catizqimg { width: 100% !important; height: 107px; }
	}
</style>


<!-- MODULE Block category le -->

<div id="catizq">
	
	{if $publicidad !== false}	
		{if $tipopublicidad == 'adsense'}
			<div id="catizqban" style="text-align: center;">	
				<div style="width: 675px;">
					<img src="{$modules_dir}cspublicidadfl/img/BarraIzq.jpg" style="width: 290px; height: 8px;">
					<img src="{$modules_dir}cspublicidadfl/img/centro.jpg" style="width: 75px;">
					<img src="{$modules_dir}cspublicidadfl/img/BarraDer.jpg" style="width: 290px; height: 8px;">
				</div>
				<div style="width: 300px; height: 250px;">
					{$publicidad}
				</div>
			</div>
		{else}			
			<div id="catizqban">
				{if $linkpublicidad != ''}<a href="{$linkpublicidad}">{/if}
					<img id="catizqimg" src="{$modules_dir}../img/imagen.php?imagen={$publicidad}" width="{*$anchopublicidad*}100%" height="{*$altopublicidad*}100%" style="margin-bottom: 2px;">
				{if $linkpublicidad != ''}</a>{/if}
			</div>
		{/if}

    {else}
		<p>&raquo; {l s='En este momento no hay imagenes' mod='cspublicidadfl'}</p>		
	{/if}
	
	</div>