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
	.container{width:100%;}
	th{background-color: #3A9B37;font-weight: 600;color:#FFF;padding:10px 2px;text-align:center;}
	td{color:#444; vertical-align: middle;}
	.info{width:100%;}
	.info td{font-size: 10pt;}
	span{color:#444;font-size: 14pt;margin:0;padding:0;}
	.content{font-size: 8pt;text-align:center;margin:0;padding:0;}
	.content table tr td{padding:5px 1px;}
	.darks{background-color: #c4c4c4;}
	.darkers{color:#FFF;background-color:#000; font-weight: bold}
	.taxes{width:100%;font-size:8pt;}
	.taxes table{width: 30%;float:right;}
	.sign{height:40px; text-align:center;border-top: 1px solid #646464}
</style>
{/literal}
<table class="container" style="margin-top:-200px;"><tr>
	<td><table class="info">
			<tr><td>{$shop_name}</td></tr>
			<tr><td>{$address_warehouse->address1}</td></tr>
			{* if the address has two parts *}
			{if !empty($address_warehouse->address2)}
			<tr><td>{$address_warehouse->address2}</td></tr>
			{/if}
			<tr><td>{$address_warehouse->city} {$address_warehouse->postcode}</td></tr>
		</table>
	</td>
	<td><table class="info" style="text-align: right;">
			<tr><td>{$supply_order->supplier_name}</td></tr>
			<tr><td>{$address_supplier->address1}</td></tr>
			{* if the address has two parts *}
			{if !empty($address_supplier->address2)}
			<tr><td>{$address_supplier->address2}</td></tr>
			{/if}
			<tr><td>{$address_supplier->city} {$address_supplier->postcode}</td></tr>
			<tr><td>{$address_supplier->country}</td></tr>
		</table>
	</td>
</tr></table>
<!-- / SUPPLIER ADDRESS -->
<span>{l s='Products ordered:' pdf='true'}</span>
<!-- PRODUCTS -->
<div class="content">
	<table class="container">
		<tr>
			<th style=" width: 13%;">EAN</th>
			<th style=" width: 7%;">{* l s='Reference' pdf='true' *}Ref</th>
			<th style="width: 22%;">{* l s='Designation' pdf='true' *}Nombre</th>
			<th style="width: 5%;">{* l s='Qty' pdf='true' *}Cant Esp</th>
			<th style="width: 5%;">{* l s='Qty' pdf='true' *}Cant Rec</th>
			<th style="width: 8%;">{* l s='Unit Price TE' pdf='true' *} Valor Und. </th>
			<th style="width: 10%;">{* l s='Total TE' pdf='true'}{l s='Before discount' pdf='true' *} Subtotal </th>
			<th style="width: 4%;">{* l s='Discount Rate' pdf='true' *}Des %</th>
			<th style="width: 10%;">{* l s='Total TE' pdf='true'}{l s='After discount' pdf='true' *} Total + Des</th>
			<th style="width: 4%;">{* l s='Tax rate' pdf='true' *} Imp % </th>
			<th style="width: 12%;">{* l s='Total TI' pdf='true' *} Total</th>
		</tr>
		{assign var="i" value="0"}
		{assign var="sumN" value="0"}
		{assign var="sumD" value="0"}
		{assign var="sumT" value="0"}
		{foreach $supply_order_details as $supply_order_detail}
		<tr {if $i%2}class="darks"{/if}>
			<td style="text-align: left;"><!--{$i++}-->{$supply_order_detail->reference}</td>
			<td style="text-align: left;">{$supply_order_detail->supplier_reference}</td>
			<td style="text-align: left;">{$supply_order_detail->name}</td>
			<td style="text-align: center;">{$supply_order_detail->quantity_expected}</td>
			<td style="text-align: center;">{$supply_order_detail->quantity_received}</td>
			<td style="text-align: center;">{$currency->prefix} {$supply_order_detail->unit_price_te} {$currency->suffix}
			{$neto = $supply_order_detail->unit_price_te * $supply_order_detail->quantity_received}
			{$sumN = $sumN + $neto}</td>
			<td style="text-align: right;">{$currency->prefix}{$neto}{$currency->suffix}</td>
			<td style="text-align: center;">{$supply_order_detail->discount_rate}
			{$ndis = $neto - ($neto * ($supply_order_detail->discount_rate/100))}
			{$sumD = $sumD + $ndis}
			</td>
			<td style="text-align: right;">{$currency->prefix}{$ndis}{$currency->suffix}</td>
			<td style="text-align: center;">{$supply_order_detail->tax_rate}
			{$ntax = $ndis + ($ndis * ($supply_order_detail->tax_rate/100))}
			{$sumT = $sumT + $ntax}
			</td>
			<td style="text-align: right;">{$currency->prefix}{$ntax|number_format:2:",":"."}{$currency->suffix}</td>
		</tr>
		{/foreach}
	</table>
</div>
<!-- / PRODUCTS -->

<table class="taxes"><tr>
	<td>
	<span>{l s='Taxes:' pdf='true'}</span><br>
	<!-- PRODUCTS TAXES -->
	<table>
		<tr>
			<th>{* l s='Base TE' pdf='true' *}Valor Neto</th>
			<th>{* l s='Tax Rate' pdf='true' *}Tasa Impuesto</th>
			<th>{* l s='Tax Value' pdf='true' *}Total Impuesto</th>
		</tr>
		{assign var="j" value="0"}
		{foreach $tax_order_summary as $entry}
		<tr {if $j%2}class="darks"{/if}>
			<td><!--{$j++}-->{$currency->prefix} {$entry['base_te']} {$currency->suffix}</td>
			<td>{$entry['tax_rate']} % </td>
			<td>{$currency->prefix} {$entry['total_tax_value']} {$currency->suffix}</td>
		</tr>
		{/foreach}
	</table><br>
<!-- / PRODUCTS TAXES -->
<span>{* l s='Summary:' pdf='true' *}Resumen</span><br>
<!-- TOTAL -->
	<table>
		<tr>
			<td class="darks">{* l s='Total TE' pdf='true'} <br /> {l s='(Discount excluded)' pdf='true' *}
			Valor Neto
			</td>
			<td style="text-align: right;">{$currency->prefix} {$sumN|number_format:2:",":"."} {$currency->suffix}</td>
		</tr>
		<tr>
			<td class="darks">{*l s='Order Discount' pdf='true' *}Valor de Descuento</td>
			<td style="text-align: right;">- {$currency->prefix} {($sumN - $sumD)|number_format:2:",":"."} {$currency->suffix}</td>
		</tr>
		<tr>
		<td class="darks">{* l s='Total TE' pdf='true'} <br /> {l s='(Discount included)' pdf='true' *}Total con descuentos</td>
			<td style="text-align: right;">{$currency->prefix} {$sumD|number_format:2:",":"."} {$currency->suffix}</td>
		</tr>
		<tr>
			<td class="darks">{* l s='Tax value' pdf='true' *}Valor impuestos</td>
			<td style="text-align: right;">{$currency->prefix} {($sumT - $sumD)|number_format:2:",":"."} {$currency->suffix}</td>
		</tr>
		<tr>
			<td class="darks">{* l s='Total TI' pdf='true' *}Total con impuestos</td>
			<td style="text-align: right;">{$currency->prefix} {$sumT|number_format:2:",":"."} {$currency->suffix}</td>
		</tr>
		{if $supply_order->shipping_base}
			<tr>
				<td class="darks">Costo base de Envío</td>
				<td style="text-align: right;">{$currency->prefix} {$supply_order->shipping_base|number_format:2:",":"."} {$currency->suffix}</td>
			</tr>
			{if $supply_order->shipping_tax}{assign var="shipping" value=($supply_order->shipping_base*(1+($supply_order->shipping_tax/100)))}{else}{assign var="shipping" value=$supply_order->shipping_base}{/if}
			<tr>
				<td class="darks">Costo total de Envío</td>
				<td style="text-align: right;">{$currency->prefix} {$shipping|number_format:2:",":"."} {$currency->suffix}</td>
			</tr>
		{/if}
		<tr>
			<td class="darkers">{l s='TOTAL TO PAY' pdf='true'}</td>
			<td class="darkers" style="text-align: right;">{$currency->prefix} {($shipping+$sumT)|number_format:2:",":"."} {$currency->suffix}</td>
		</tr>
		<tr>
			<td class="darks">{* l s='Total TI' pdf='true' *}Total Esperado</td>
			<td style="text-align: right;">{$currency->prefix} {($shipping+$supply_order->total_ti)|number_format:2:",":"."} {$currency->suffix}</td>
		</tr>
	</table>
</td>
<td><br><br><br><br><br>
	<table>
		<tr><td class="sign">Visto Bueno Compras</td></tr>
		<tr><td class="sign">Visto Bueno Logística</td></tr>
		<tr><td class="sign">Visto Bueno Administración</td></tr>
		<tr><td class="sign">Visto Bueno Técnico</td></tr>
	</table>
</td>
</tr></table>
<!-- / TOTAL -->
