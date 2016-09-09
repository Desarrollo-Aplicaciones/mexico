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

{*} Capturar variables de control si existen errores {*}
	{assign var="control_vars" value = []}
	{assign var="var_duplicada" value = false} 
	  	{if isset($conditions_order) && !empty($conditions_order) && isset($conditions_order['ERRORS_THIS_STEP']) && !empty($conditions_order['ERRORS_THIS_STEP'])}
	  	{assign var="ERRORS_THIS_STEP" value = $conditions_order['ERRORS_THIS_STEP']}
						{*}Mostrar lista de errores {*}
					{foreach from=$ERRORS_THIS_STEP item=error} {*}Recorrer Errores {*}
						{foreach from=$error['control_vars'] item=control_var} {*} Agrega variables de control al arreglo control_vars {*}
							{foreach from=$control_vars item=item} {*} Recorre las variables en el array para evitar duplicidad de variables{*} 
								{if $control_var == $item}
									{$var_duplicada = true}
								{/if}
							{/foreach}
							{if !$var_duplicada} {*}Si la variable no esta duplicada se agrega al arreglo{*}
								{$control_vars[] = $control_var}
							{/if}
							{$var_duplicada = false}
						{/foreach}
  					{/foreach}
		{/if}
		{*} Mostrar Errores {*}
	  	{if isset($ERRORS_THIS_STEP) && !empty($ERRORS_THIS_STEP) && count($ERRORS_THIS_STEP) > 0}
						{*}Mostrar lista de errores {*}
			<span class="marcar_error" style="width:17px;" onclick="mostrar_errores(this)" messages="{foreach from=$ERRORS_THIS_STEP item=error}{$error['message']}|{/foreach}">
					<a href="#infoPayulatam" class="fancybox">
						<img style="width: 16px" src="../img/admin/Error3.png" alt="Ver Errores" />
					</a>
			</span>
			{elseif isset($conditions_order) && !empty($conditions_order) &&  $conditions_order['stop_step']}
			<spam class="marcar_ok" ><img style="width: 16px" src="../img/admin/ok-icono.jpg" alt="Ver Errores" /></spam>
						{*Botón de lista de icrs*}
			<span style="width:20px;">
			    <a href="#listaIcrs" class="fancybox" ><img src="../img/admin/list-icrs.jpg" alt="Listado de ICRS  de la orden." onclick="mostarListaIcrs({$order->id})"/></a>
			</span>
			 {if isset($conditions_order['status_name']) && $conditions_order['status_name'] != 'PS_OS_SHIPPING'}
			<span style="width:20px;">
			    <a orderout="{$order->id}|{$order->id_customer}|{$order->id_cart}|{$order->invoice_number}|{$order->delivery_number}" class="order_out" href="#ex5" ><img src="../img/admin/delivery.gif" alt="Registro de productos de salida" /></a>
			</span>		
			{/if}	
        {/if}

		{*} Si existe la variable "select_home" se muestra el acceso para cargar los ICRS, En caso contrario se muestra el acceso para descargar la factura {*}
        {if 'complete_order'|in_array:$control_vars || (isset($conditions_order['status_name']) && $conditions_order['status_name'] == 'VERIFICACION_MANUAL')}
        {* Generate HTML code for printing Delivery Icon with link *}

			<span style="width:20px;">
			    <a orderout="{$order->id}|{$order->id_customer}|{$order->id_cart}|{$order->invoice_number}|{$order->delivery_number}" class="order_out" href="#ex5" ><img src="../img/admin/delivery.gif" alt="Registro de productos de salida" /></a>
			</span>
        {elseif isset($conditions_order) && !empty($conditions_order) &&  $conditions_order['stop_step']}
        	<span style="width:20px; margin-right:5px;">

				<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id}"><img src="../img/admin/tab-invoice.gif" alt="invoice" /></a>

			</span>

        {/if}


<span style="width:17px;">
{if ($order_state->delivery || $order->delivery_number)}

	 <a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order={$order->id}&noInvoice=1"><img style="width: 16px" src="../img/admin/get_pdf.png" alt="delivery" /></a>
{/if}
</span> 
{*}

{* INICIO INFORMACION TRANSACCION PAYULATAM *}
<span style="width:17px;" onclick="consulInfoPayulatam({$order->id})">
	{if ($order_payu != 'empty')}
		<a href="#infoPayulatam" class="fancybox">
			<img style="width: 16px" src="../img/admin/Payu_featured.png" alt="Payulatam" />
		</a>
		
		{/if}
</span>

<script type="text/javascript">
	{*}console.log('id_order: {$order->id}, status: {$conditions_order['status_name']}');{*}
</script>

{* FIN INFORMACION TRANSACCION PAYULATAM *}