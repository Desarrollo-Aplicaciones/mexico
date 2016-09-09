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
<div class="block" id="lineas-contacto">
<!-- MODULE Block contact infos -->
	<div id="block_contact_infos">
		<h4 class="title_block">{l s='Customer service' mod='blockcontactinfos'}</h4>
		<a class="show_hide_footer" href="javascript:void(0)">icon</a>
		<ul class="f_block_content">
			<li>
			{* if $blockcontactinfos_address != ''}
				<a href="tel:018002694408">{l s='Tel' mod='blockcontactinfos'}<br /><b>01 800 269.4408</b></a><br />
			{/if *}
			{if $blockcontactinfos_phone != ''}
				{* <a href="tel:018002694408">{l s='Tel' mod='blockcontactinfos'}<b>01 800 269.4408</b></a><br /> *}
				<a href="tel:+5541708434">{l s='Línea de Atención y Ventas Nacional sin costo:' mod='blockcontactinfos'}<br /><b>{$blockcontactinfos_phone|escape:'htmlall':'UTF-8'}</b></a><br />
			{/if}
			</li>
	                {if $blockcontactinfos_email != ''}<li><b>{mailto address=$blockcontactinfos_email|escape:'htmlall':'UTF-8' }</b></li>{/if}
		</ul>
	</div>
</div>
	<!-- /MODULE Block contact infos -->
<div class="block">
	<div id="block_mobile_app">
		<h4 class="title_block">Compra con seguridad</h4>
			<a class="show_hide_footer" href="javascript:void(0)">icon</a>
			<ul class="f_block_content">
				<a target="_blank" href="http://ssl.comodo.com/ev-ssl-certificates.php">
					<img src="{$img_dir}footer/comodo_secure_100x85.png" alt="EV SSL Certificate"/>
				</a><br />
				<img src="{$img_dir}footer/oxxo.jpg" alt="depósito en Oxxo"/>
				<img src="{$img_dir}footer/deposito.jpg" alt="depósito en efectivo"/>
				<img src="{$img_dir}footer/dinners.jpg" alt="tarjeta de crédito Diners Club"/>
				<img src="{$img_dir}footer/master.jpg" alt="tarjeta de crédito Master Card"/><br />
				<img src="{$img_dir}footer/visa.jpg" alt="tarjeta de crédito VISA"/>
				<img src="{$img_dir}footer/amex.jpg" alt="tarjeta de crédito American Express"/>
				<img src="{$img_dir}footer/cod.jpg" alt="pago ContraEntrega al recibir el pedido"/>
				<img src="{$img_dir}footer/paypal.jpg" alt="pago con PayPal"/>
			</ul>
	</div>
</div>