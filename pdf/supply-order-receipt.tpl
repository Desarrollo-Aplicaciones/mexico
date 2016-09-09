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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!-- SHOP ADDRESS -->
{literal}
<style>
	table{font-size: 8pt;}
	th{background-color: #3A9B37;font-weight: 600;color:#FFF;padding:10px 2px;text-align:center;}
	td{color:#444; vertical-align: middle;}
	span{color:#444;font-size: 14pt;margin:0;padding:0;}
	.darks{background-color: #c4c4c4;}
	.darkers{color:#FFF;background-color:#000; font-weight: bold}
	.sign{margin-top:40px; width:200px; text-align:center;border-top: 1px solid #646464;float:left;}
</style>
{/literal}
<!--Datos de la Orden-->
<table>
	<tr>
		<td>Proveedor: {$supply_order->supplier_name}</td>
		<td>Fecha de Recepción: {$date_delivery}</td>
	</tr>
	<tr>
		<td>Factura de Proveedor: {$supplier_invoice}</td>
		<td>Recibido por: {$receiver}</td>
	</tr>
</table>

<!-- Resumen General -->
<span>Resumen de la Orden.</span>
<br />
<!-- / PRODUCTS -->
<table>
	<tr>
		<th style="width:10%;">ICR</th>
		<th style="width:25%;">Producto</th>
		<th style="width:15%;">Presentación</th>
		<th style="width:10%;">Fabricante</th>
		<th style="width:10%;">Código de Barras</th>
		<th style="width:10%;">Registro Sanitario</th>
		<th style="width:10%;">Lote</th>
		<th style="width:10%;">Fecha de vencimiento</th>
	</tr>
	{assign var="i" value="0"}
	{foreach from=$data item=item key=key}
		<tr {if $i%2}class="darks"{/if}>
			<td>{$info[$key]['cod_icr']}</td>
			<td>{$item->name|lower|capitalize}</td>
			<td>{$features[$item->id_product]['presentacion']}</td>
			<td>{$features[$item->id_product]['name']}</td>
			<td>{$item->reference}</td>
			<td>{$item->upc}</td>
			<td>{$info[$key]['lote']}</td>
			<td>{$info[$key]['fecha_vencimiento']}</td>
		</tr>
		<!--{$i++}-->
	{/foreach}
</table>
<!-- / PRODUCTS -->
<!-- FIRMA -->
<br />
<br />
<br />
<br />
<table>
	<tr>
		<td><div class="sign">Verificado 1.</div></td>
		<td><div class="sign">Verificado 2.</div></td>
	</tr>
</table>
<!-- / FIRMA -->
