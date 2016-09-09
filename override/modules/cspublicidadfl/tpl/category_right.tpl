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
		#catder { float: right; width: 303px; height: 254px; }
		#catderban { width: 100% !important; overflow: hidden;}
		
	}

	/* Tablet Portrait size to standard 960 (devices and browsers) */
	@media only screen and (min-width: 768px) and (max-width: 1000px) {
		#catder { float: right; padding-left: 20px; background-color: #fff;}
	}
	
	
	/* Small Tablet Portrait size to standard 600 (devices and browsers) */
	@media only screen and (min-width: 480px) and (max-width: 767px) {
		#catder { width: 100% !important; float: right; text-align: center; margin-top: 10px;}
		#catderban{ float: none; width: 303px; height: 254px;}
	}

	/* All Mobile Sizes (devices and browser) */
	@media only screen and (min-width: 200px) and (max-width: 479px) {
		#catder { display: none;}
	}
</style>


<!-- MODULE Block category right -->

<div id="catder">
	
	{if $publicidad !== false}	
		{if $tipopublicidad == 'adsense'}
			<div id="catderban" style="width: 303px; height: 254px; text-align: center;">	
				<div style="width: 303px; margin-top: -17px; position: absolute;">
					<img src="{$modules_dir}cspublicidadfl/img/BarraIzq.jpg" style="width: 110px; height: 8px;">
					<img src="{$modules_dir}cspublicidadfl/img/centro.jpg" style="width: 75px;">
					<img src="{$modules_dir}cspublicidadfl/img/BarraDer.jpg" style="width: 110px; height: 8px;">
				</div>
				<div style="width: 300px; height: 250px;">
					{$publicidad}
				</div>
			</div>
		{else}			
			<div id="catderban" style="width: 303px; height: 254px;">
				{if $linkpublicidad != ''}<a href="{$linkpublicidad}">{/if}
					<img id="catderimg" src="{$modules_dir}../img/imagen.php?imagen={$publicidad}" width="{*$anchopublicidad*}100%" height="{*$altopublicidad*}100%" style="margin-bottom: 2px;">
				{if $linkpublicidad != ''}</a>{/if}
			</div>
		{/if}

    {else}
		<p>&raquo; {l s='En este momento no hay imagenes' mod='cspublicidadfl'}</p>		
	{/if}
	
	</div>