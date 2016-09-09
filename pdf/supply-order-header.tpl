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
<br><br><br>
<table style="width: 100%;">
	<tr>
		<td rowspan="4" style="width: 50%">
	        {if $logo_path}
	            <img src="{$logo_path}" width="150px"/>
	        {/if}
		</td>
		<td style="font-size: 8pt; color: #444; text-align:right">{$shop_name|escape:'htmlall':'UTF-8'}</td>
	</tr>
	<tr>
		<td style="font-size: 8pt; color: #444; text-align:right">{$date|escape:'htmlall':'UTF-8'}</td>
	</tr>
	<tr>
		<td style="font-size: 8pt; color: #444; text-align:right">{$title|escape:'htmlall':'UTF-8'}</td>
	</tr>
	<tr>
		<td style="font-size: 8pt; color: #444; text-align:right">{$reference|escape:'htmlall':'UTF-8'}  -  {$supply_order_id|escape:'htmlall':'UTF-8'}</td>
	</tr>
</table>