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

{* Generate HTML code for printing Invoice Icon with link *}


<span style="width:20px; margin-right:5px;">
{if ($order_state->invoice || $order->invoice_number) and $complete_order}
	<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id}"><img src="../img/admin/tab-invoice.gif" alt="invoice" /></a>
{else}
	-
{/if}
</span>


{* Generate HTML code for printing Delivery Icon with link *}
<span style="width:20px;">
{if ($order_state->delivery || $order->delivery_number)and !$complete_order}
    <a orderout="{$order->id}|{$order->id_customer}|{$order->id_cart}|{$order->invoice_number}|{$order->delivery_number}" class="order_out" href="#ex5" ><img src="../img/admin/delivery.gif" alt="Registro de productos de salida" /></a>
{else}
	-
{/if}
</span>

<span style="width:17px;">
{if ($order_state->delivery || $order->delivery_number)}
    <a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order={$order->id}"><img style="width: 16px" src="../img/admin/get_pdf.png" alt="delivery" /></a>
{else}
	-
{/if}
</span>

