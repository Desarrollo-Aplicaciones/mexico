<style type="text/css">
<!--
.textosss {
	color: #000;
}
-->
</style>
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


<table>

	<tr>
		<td colspan="2" style="text-align: justify; font-weight:bold; font-size: 9pt; color: #000000;">
         &nbsp;www.farmalisto.com.mx<br />

			{if !empty($shop_phone) OR !empty($shop_fax)}
				{if !empty($shop_phone)}
					&nbsp;&nbsp;&nbsp;México: {$shop_phone|escape:'htmlall':'UTF-8'}
				{/if}

				{if !empty($shop_fax)}
					&nbsp;&nbsp;&nbsp;Fax: {$shop_fax|escape:'htmlall':'UTF-8'}
				{/if}
				<br />
			{/if}
            &nbsp;&nbsp;&nbsp;México<br />
            &nbsp;&nbsp;&nbsp;contacto@farmalisto.com.mx<br />
		</td>
	</tr>
</table>
*}

<table>
	<tr>
		<td style="text-align: justify; font-weight:bold; font-size: 19px; color: #646464;">
         &nbsp;México - &nbsp; www.farmalisto.com.mx - &nbsp; contacto@farmalisto.com.mx
		</td>
		<td style="text-align: right; font-weight:bold; font-size: 19px; color: #646464; font-style:  italic;">
			Número de pedido {$id_order_footer}
		