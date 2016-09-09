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
<br><br><br>
<div style="font-size: 9pt; color: #444">

<table>
	<tr><td>&nbsp;</td></tr>
</table>


<table>
	<tr>
		<td colspan="3" style="width: 100%">
			Resumen Del Traslado
		</td>
	</tr>

	<tr>
		<td style="background-color: #1E817A; color: white; text-align: center; width: 20%;" > Referencia </td>
		<td style="background-color: #1E817A; color: white; text-align: center; width: 50%;" > Producto </td>
		<td style="background-color: #1E817A; color: white; text-align: center; width: 30%;" > ICR's </td>
	</tr>
	<tr>
		<td colspan="3" style="line-height: 1px">&nbsp;</td>
	</tr>

{foreach $info_body as $traslado_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:6px;">
					<td style="font-size: 18px; background-color: {$bgcolor};text-align: left; width: 20%;" > &nbsp;&nbsp;{$traslado_detail.reference} </td>
					<td style="font-size: 17px; background-color: {$bgcolor};text-align: left; width: 50%;" > {$traslado_detail.product} </td>
					<td style="font-size: 19px; background-color: {$bgcolor};text-align: left; width: 30%;" > {$traslado_detail.icrs} </td>
				</tr>
{/foreach}
</table>



<table>
	<tr><td style="line-height: 8px">&nbsp;</td></tr>
</table>


</div>

