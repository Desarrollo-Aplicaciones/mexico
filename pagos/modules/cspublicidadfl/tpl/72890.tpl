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
		#banner_728 { display: block; float: right; width: 1000px; height: auto; max-height: 90px; text-align: center;}
		#banner_728_in { width: 100%; height: 100%; max-height: 90px; max-width: 728px;}
		.imagen_ban_728LR { width: 321px; height: 8px;}
		#informac { float: none !important; height: 0 !important; position: unset !important; top: 0 !important; width: auto !important; }
		#textosseo { margin: 0px !important;}
	}

	/* Tablet Portrait size to standard 960 (devices and browsers) */
	@media only screen and (min-width: 768px) and (max-width: 1000px) {
		#banner_728 { display: block; float: right; width: 100%; height: auto; margin: 10px 0; overflow: hidden; max-height: 90px; text-align: center;}
		#banner_728_in { width: 100%; height: 100%; max-height: 70px; max-width: 728px;}
		.imagen_ban_728LR { width: 330px; height: 8px;}
	}
	
	
	/* Small Tablet Portrait size to standard 600 (devices and browsers) */
	@media only screen and (min-width: 480px) and (max-width: 767px) {
		#banner_728 { display: none;}
	}

	/* All Mobile Sizes (devices and browser) */
	@media only screen and (min-width: 200px) and (max-width: 479px) {
		#banner_728 { display: none;}
	}
</style>

<!-- MODULE Block new products -->

	<div align="center" id="banner_728" style="background-color: #fff; float: left; width: 100%; height: auto; padding-bottom: 16px; ">
	
	{if $publicidad !== false}
		{if $tipopublicidad == 'adsense'}
			<div id="banner_728_in" style="text-align: center;">	
				<div style="width: 100%;">
					<img class="imagen_ban_728LR" src="{$modules_dir}cspublicidadfl/img/BarraIzq.jpg" >
					<img src="{$modules_dir}cspublicidadfl/img/centro.jpg" style="width: 75px;">
					<img class="imagen_ban_728LR" src="{$modules_dir}cspublicidadfl/img/BarraDer.jpg" >
				</div>
				<div style="width: 100% height: auto;">
					{$publicidad}
				</div>
			</div>
		{else}			
			<div id="banner_728_in" >
				{if $linkpublicidad != ''}<a href="{$linkpublicidad}">{/if}
					<img id="banner_728_img" src="{$modules_dir}../img/imagen.php?imagen={$publicidad}" width="{*$anchopublicidad*}100%" height="{*$altopublicidad*}100%" style="margin-bottom: 2px;">
				{if $linkpublicidad != ''}</a>{/if}
			</div>
		{/if}

	{else}
		<p>&raquo; {l s='En este momento no hay imagenes' mod='cspublicidadfl'}</p>		
	{/if}
	
	</div>