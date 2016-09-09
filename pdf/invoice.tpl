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
*}
<div style="font-size: 8pt; color: #444;"><!-- ADDRESSES -->
<table style="width: 100%;" cellpadding="0px">
	<tr>		
	  <td style="width: 49%; vertical-align:top; text-align: left; border: none;">{if !empty($invoice_address)}
				<table width="50%" >
					<tr>
						<td style=" width:133px; background-color: #3A9842; color: #FFF; height:8px; line-height:4px; font-size: 8pt;">&nbsp;&nbsp;{l s='Delivery Address' pdf='true'}							
						</td>
						<td style=" width:134px; background-color: #3A9842; color: #FFF; height:8px; line-height:4px; font-size: 8pt;">&nbsp;&nbsp;{l s='Billing Address' pdf='true'}							
						</td>
					</tr>

					<tr>
						<td style="width:134px;" cellpadding="0px">
							 {$delivery_address}
						</td>
						<td style="width:134px;" cellpadding="0px">
							 {$invoice_address}
						</td>
					</tr>
				</table>
{else}<table style=" width:270px; text-align: left;" >
			<tr>
				<td style=" width:270px; background-color: #3A9842; color: #FFF; height:8px; line-height:4px; font-size: 8pt;">&nbsp;&nbsp;{l s='Billing & Delivery Address.' pdf='true'}
				</td>
			</tr>
			<tr>
				<td style="width:270px;" cellpadding="0px">{$delivery_address}</td>
			</tr>
		</table>
        	{/if}
        </td>

	  <td style="width: 2%; text-align: right;">&nbsp;</td>

      <td style="width: 49%; " cellpadding="0px"><table style="width: 100%; text-align: left; border-collapse:collapse;" cellpadding="0px">
			<tr>
			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; width:87px; height:8px; line-height:4px;" > Número de Pedido 
			  </td>

			  	<td style="background-color: #FFF; width:2px; padding:0px; margin:0px; line-height:3px;" ></td>

			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; width:87px; height:8px; line-height:4px;" > Referencia de Pedido 
			  </td>

			  	<td style="background-color: #FFF; width:2px; padding:0px; margin:0px; line-height:3px;" ></td>

			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; height:8px; line-height:4px; width:86px;" > Fecha de Pedido 
			  </td>
			</tr>
			<tr>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:87px; text-align: center;">{$order->id}</td>
			  	<td style=" background-color: #FFF;  padding:0px; margin:0px; width:2px; " > </td>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:87px; text-align: center;">{$order->getUniqReference()}</td>
			  	<td style=" background-color: #FFF;  width:2px;" > </td>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:87px; text-align: center;">{dateFormat date=$order->date_add full=1}</td>
			</tr>
			<tr>
				<td colspan="5" style="width: 100%; line-height: 1px;"></td>
			</tr>
			{*<tr>
			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; height:8px; line-height:4px; width:131px;"> Forma de Pago </td>
			  	<td style=" background-color: #FFF;  padding:0px; margin:0px; width:2px; " > </td>
			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; height:8px; line-height:4px; width:130px;"> Método de Pago </td>
			</tr>
			<tr>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:131px; text-align: center;">En una sola exhibici&oacute;n</td>
			  	<td style=" background-color: #FFF;  padding:0px; margin:0px; width:2px; " > </td>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:130px; text-align: center;">{$metodo_pago}</td>
			</tr>
			*}

			<tr>
			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; width:87px; height:8px; line-height:4px;" > Forma de Pago 
			  </td>

			  	<td style="background-color: #FFF; width:2px; padding:0px; margin:0px; line-height:3px;" ></td>

			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; width:87px; height:8px; line-height:4px;" > Método de Pago 
			  </td>

			  	<td style="background-color: #FFF; width:2px; padding:0px; margin:0px; line-height:3px;" ></td>

			  <td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 7pt; height:8px; line-height:4px; width:86px;" > Fecha de Emisión 
			  </td>
			</tr>
			<tr>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:87px; text-align: center;">En una sola exhibici&oacute;n</td>
			  	<td style=" background-color: #FFF;  padding:0px; margin:0px; width:2px; " > </td>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:87px; text-align: center;">{$metodo_pago}</td>
			  	<td style=" background-color: #FFF;  width:2px;" > </td>
			  <td style=" background-color: #EAEAEA;  padding:0px; margin:0px; font-size: 6pt; color: #444; width:87px; text-align: center;">{dateFormat date=$order->invoice_date full=1}</td>
			</tr>
			<tr>
				<td colspan="5" style="width: 100%; line-height: 1px;"></td>
			</tr>


			<tr>
				<td colspan="5" style="width: 100%; line-height: 3px;"></td>
			</tr>

			{if $current_state_txt != '' }
			<tr style="border-bottom: 1pt solid black;">
			  <td style="vertical-align: middle; text-align: center; width:263px;"><table style="width: 100%; border: 0.5px solid black; padding:0px; margin:0px;" cellpadding="0px"><tr><td style="vertical-align: middle; text-align: center; width:87px;"><br style="line-height:5px;"><img src="{$img_physical_uri}factura/{$current_state_img}" style="width:40px; height:40px;" /></td>
				<td style="width:3px; vertical-align: middle; text-align: center;"><br style="line-height:6px;"><img src="{$img_physical_uri}factura/vert_line.jpg" style="height:40px;" /></td>
			  <td style="color: #646464; vertical-align: middle; text-align: center; font-size: 15pt; font-weight: bold; width:174px;"><br style="line-height:9px;">{$current_state_txt}</td>
				</tr>
			   </table>
			  </td>		
			</tr>
			{/if}

		</table>
 <!-- / CUSTOMER INFORMATION -->
      </td>
	</tr>
	<tr>
		<td colspan="3" style="width: 100%; line-height: 1px;"></td>
	</tr>
</table>
<!-- / ADDRESSES -->

<br style="line-height:1px;">
<table style="width: 100%; font-size: 19px;">
				<tr style="line-height:4px; ">
					<td style="text-align: left; background-color: #3A9842; color: #FFF; padding-left: 10px; font-weight: bold; width: {if !$tax_excluded_display}48%{else}48%{/if}"><br style="line-height:7px;">{l s='Product / Reference' pdf='true'}</td>

					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="background-color: #3A9842; color: #FFF; vertical-align: middle; text-align: center; font-weight: bold; width: 10%"><br style="line-height:7px;">{l s='Unit Price' pdf='true'} {*l s='(Tax Excl.)' pdf='true'*}</td>
					{/if}
					{*<td valign="middle" style="background-color: #3A9842; color: #FFF; text-align: center; font-weight: bold; width: 8%;"><br style="line-height:7px;">
						{l s='tax' pdf='true'}
						{*if $tax_excluded_display}
							 {l s='(Tax Excl.)' pdf='true'}
						{else}
							 {l s='(Tax Incl.)' pdf='true'}
						{/if}
					</td>*}
					<td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 17px; font-weight: bold; width: 18%; white-space: nowrap;"><br style="line-height:7px;">	Código EAN </td>
					<td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 17px; font-weight: bold; width: 7%; white-space: nowrap;"> Unidad de Medida </td>
					<td style="background-color: #3A9842; color: #FFF; text-align: center; font-size: 17px; font-weight: bold; width: 4%"><br style="line-height:7px;">{l s='Qty' pdf='true'}</td>
					<td style="background-color: #3A9842; color: #FFF; text-align: right; font-weight: bold; width: {if !$tax_excluded_display}13%{else}13%{/if}"><br style="line-height:7px;">{l s='SubTotal' pdf='true'}
						
					</td>
				</tr>
				<!-- PRODUCTS -->
				{assign var="iva_calc_tot" value="0"} <!--total tax / product -->
				{assign var="sub_total_prod" value="0"} <!--sub total product  no tax-->
				{foreach $order_details as $order_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:6px;">
					<td style="background-color:{$bgcolor};text-align: left; width: {if !$tax_excluded_display}48%{else}48%{/if}">{$order_detail.product_name}</td>
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="background-color:{$bgcolor};text-align: right; width: 10%">
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
						</td>
					{/if}
					{*<td style="background-color:{$bgcolor};font-size: 16px; text-align: right; width: 8%; padding:0; margin:0; border-collapse: collapse; border-spacing: 0;">					

					{*if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_incl}
					{/if}
					{if $order_detail.unit_price_tax_excl eq $order_detail.unit_price_tax_incl}
					0
					{else}
						{assign var="iva_calc" value=$order_detail.unit_price_tax_incl-$order_detail.unit_price_tax_excl}
						{displayPrice currency=$order->id_currency price=$iva_calc}

						{assign var="iva_calc_tot" value=$iva_calc_tot + ($iva_calc * $order_detail.product_quantity)}

					{/if}
					</td>*}
					<td style="background-color:{$bgcolor};text-align: center; width: 18%"> {$order_detail.reference} </td>
					<td style="background-color:{$bgcolor};text-align: center; width: 7%"> PIEZA </td>
					<td style="background-color:{$bgcolor};text-align: center; width: 4%">{$order_detail.product_quantity}</td>
					<td style="background-color:{$bgcolor};text-align: right;  width: {if !$tax_excluded_display}13%{else}13%{/if}">
					{*if $tax_excluded_display*}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}&nbsp;&nbsp;						
						{assign var="sub_total_prod" value=$sub_total_prod + $order_detail.total_price_tax_excl}
					{*else}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_incl}&nbsp;&nbsp;
					{/if*}
					</td>
				</tr>
					{foreach $order_detail.customizedDatas as $customizationPerAddress}
						{foreach $customizationPerAddress as $customizationId => $customization}
							<tr style="line-height:6px;background-color:{$bgcolor};">
								<td style="line-height:3px; text-align: left; width: 45%; vertical-align: top">

										<blockquote>
											{if isset($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) && count($customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_]) > 0}
												{foreach $customization.datas[$smarty.const._CUSTOMIZE_TEXTFIELD_] as $customization_infos}
													{$customization_infos.name}: {$customization_infos.value}
													{if !$smarty.foreach.custo_foreach.last}<br />
													{else}
													<div style="line-height:0.4pt">&nbsp;</div>
													{/if}
												{/foreach}
											{/if}

											{if isset($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) && count($customization.datas[$smarty.const._CUSTOMIZE_FILE_]) > 0}
												{count($customization.datas[$smarty.const._CUSTOMIZE_FILE_])} {l s='image(s)' pdf='true'}
											{/if}
										</blockquote>
								</td>
								{if !$tax_excluded_display}
									<td style="text-align: right;"></td>
								{/if}
								<td style="text-align: right; width: 10%"><span style="text-align: center; width: 10%; vertical-align: top">({$customization.quantity})</span></td>
								<td style="text-align: center; width: 10%; vertical-align: top">&nbsp;</td>
								<td style="width: 15%; text-align: right;"></td>
							</tr>
						{/foreach}
					{/foreach}
				{/foreach}
				<!-- END PRODUCTS -->

				<!-- CART RULES -->
				{assign var="shipping_discount_tax_excl" value="0"}
				{assign var="shipping_discount_tax_value" value="0"}

				{foreach $cart_rules as $cart_rule}
					{cycle values='#FFF,#DDD' assign=bgcolor}
					<tr style="line-height:6px;background-color:{$bgcolor};text-align:left;">
						<td style="text-align:left;width:48%;vertical-align:top" colspan="{if !$tax_excluded_display}5{else}4{/if}">{$cart_rule.name}</td>
						<td width="10%" style="text-align:right;" >{if $tax_excluded_display}$ - {$cart_rule.value_tax_excl}{else}$ - {$cart_rule.value}{/if}&nbsp;</td>
					</tr>
				{/foreach}
				<!-- END CART RULES -->
</table>
<br style="line-height:1px;"><br style="line-height:1px;">
<table width="100%">
	<tr>
		<td width="30%">&nbsp;</td><td width="19%" align="right" style="font-size: 17px; ">
		</td>
		<td width="1%"></td>
		<td width="50%" style="text-align: right; align:right; background-color: #EAEAEA;"><br style="line-height:1px;"><br style="line-height:1px;">

			<table style="width: 50%; font-size: 19px; line-height:1px; ">
				{if (($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl) > 0)}
				<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px; text-align: right; font-weight: bold">SubTotal</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">{*displayPrice currency=$order->id_currency price=$order_invoice->total_products*}{displayPrice currency=$order->id_currency price=$sub_total_prod}&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
				
				<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px; text-align: right; font-weight: bold">{l s='Total Tax' pdf='true'}</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">{displayPrice currency=$order->id_currency price=$iva_calc_tot}&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
			
				<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px; text-align: right; font-weight: bold">{l s='Product Total (Tax Incl.)' pdf='true'}</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_products_wt}&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
				{else}
				<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px; text-align: right; font-weight: bold">{l s='Product Total' pdf='true'}</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">{*displayPrice currency=$order->id_currency price=$order_invoice->total_products_wt*}{displayPrice currency=$order->id_currency price=$sub_total_prod}&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
				{/if}

				{if $order_invoice->total_discount_tax_excl > 0} {*total_discount_tax_incl*}
				<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px; text-align: right; font-weight: bold"> {if $apoyosalud!=NULL}{$apoyosalud}{else}Descuento{/if}{*l s='Total Vouchers' pdf='true'*}</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">-{displayPrice currency=$order->id_currency price=($order_invoice->total_discount_tax_incl)}&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
				{/if}

				{if $order_invoice->total_wrapping_tax_incl > 0}
				<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px; text-align: right; font-weight: bold">{l s='Wrapping Cost' pdf='true'}</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_wrapping_tax_excl}&nbsp;&nbsp;&nbsp;
					{else}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_wrapping_tax_incl}&nbsp;&nbsp;&nbsp;
					{/if}
					</td>
				</tr>
				{/if}

				{foreach key=key item=item from=$ivas}

    <tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px;text-align: right; font-weight: bold"> IVA {$key} % </td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">
						

							
							
							{displayPrice currency=$order->id_currency price=$item}
	

					&nbsp;&nbsp;&nbsp;</td>
				</tr>

  {/foreach}

				{if $order_invoice->total_shipping_tax_incl > 0}
				<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px;text-align: right; font-weight: bold">{l s='Shipping Cost' pdf='true'}</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right;">
						

							{assign var="shipping_discount_tax_excl" value=($order_invoice->total_shipping_tax_incl/1.16)}

							{assign var="shipping_discount_tax_value" value=($order_invoice->total_shipping_tax_incl - $shipping_discount_tax_excl)}
							
							{displayPrice currency=$order->id_currency price=$shipping_discount_tax_excl}
	

					&nbsp;&nbsp;&nbsp;</td>
				</tr>

				{*<tr style="line-height:3.5px;">
					<td style="background-color: #EAEAEA; width: 186px; text-align: right; font-weight: bold">{l s='Shipping Tax' pdf='true'}</td>
					<td style="background-color: #EAEAEA; width: 82px; text-align: right; ">
						<!--aca voy-->
						
							{displayPrice currency=$order->id_currency price=$shipping_discount_tax_value}

						
					{*if $order_invoice->value_tax_excl > 0 || }
						
						
					&nbsp;&nbsp;&nbsp;</td>
				</tr>*}
				{else}
					<tr style="line-height:3.5px;">
						<td style="background-color: #EAEAEA; width: 186px;text-align: right; font-weight: bold">{l s='Shipping Cost' pdf='true'}</td>
						<td style="background-color: #EAEAEA; width: 82px; text-align: right; font-weight: bold">Gratuito&nbsp;&nbsp;&nbsp;&nbsp;</td>
					</tr>
				{/if}
				<tr style="line-height:3.5px;">
					<td style="width: 186px; font-size: 20px; text-align: right; font-weight: bold;">{l s='Total' pdf='true'}</td>
					<td style="width: 82px; font-size: 20px; text-align: right; font-weight: bold; height:13px;">{displayPrice currency=$order->id_currency price=$order_invoice->total_paid_tax_incl}&nbsp;&nbsp;&nbsp;&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>                              
    <div style="vertical-align:bottom">
	    <table width="100%" style="" cellpadding="0px" cellspacing="0px">
        	<tr>
           	  <td width="100%" style="text-align:justify; font-size: 21px; font-weight: bold">&nbsp;{$ValorEnLetras}<br></td>
           	</tr>
           	<tr>
           	  <td width="100%" style="text-align:justify; font-size: 21px; font-weight: bold">{if $formu_medical}<p><sup>FM</sup>&nbsp;<em>Apreciado cliente, recuerde que la receta m&eacute;dica es requisito obligatorio para la venta y/o entrega de medicamentos que requieren prescripci&oacute;n m&eacute;dica, sin copia de este documento nuestro transportador no entregar&aacute; el medicamento; recuerde las diferentes opciones con las que cuenta la compa&ntilde;&iacute;a para cumplir con este requisito, m&aacute;s informaci&oacute;n en <strong>www.farmalisto.com.mx</strong></em></p>{/if}</td>
           	</tr>

           	{if $note != ""}
	        	<tr><td><br style="line-height:3px;"><br style="line-height:3px;"><strong>Nota:</strong> <em>{$note}</em><br style="line-height:2px;"><br style="line-height:2px;"></td></tr>
	        {/if}
	               	
           	<tr>           	
           		<td width="285px" style="">{if isset($sellosat.uuid) && $sellosat.uuid != ''}<table width="100%" style="text-align:left; font-size: 19px; border-collapse:collapse; " cellpadding="0px" cellspacing="0px">
	           	  		<tr>
	           	  			<td width="88px" height="88px;">{assign var="qr_code" value="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chld=Q|0&chl=%3fre%3d{$rfcemisor}%26rr%3d{$rfcreceptor}%26tt%3d{$order_invoice->total_paid_tax_incl}%26id%3d{$sellosat.uuid}"}<img src="{$qr_code|escape:'url'}" width="88px" height="88px" style="display:block;"></td>
	           	  			<td width="197px" style="color: black; font-size: 18px; "><table style="width: 100%; text-align: left; border-collapse:collapse;" cellpadding="0px" cellspacing="0px">
	           	  					<tr><td style="" width="63px"><br style="line-height:8px;"><br style="line-height:8px;">{*<b>Fecha de Emisión:</b>*} </td><td style="" width="128px"><br style="line-height:8px;"><br style="line-height:8px;"> {*dateFormat date=$order->invoice_date full=1*}</td></tr>
	               	  				<tr><td style="" width="63px"><br style="line-height:7px;"><b>Folio Fiscal: </b></td><td style="" width="134px"><br style="line-height:7px;"> {$sellosat.uuid}</td></tr>
	               	  				<tr><td style="" width="63px"><br style="line-height:7px;"><b>Certificado SAT: </b></td><td style="" width="134px"><br style="line-height:7px;"> {$sellosat.nocertificadosat}</td></tr>
	               	  				<tr><td style="" width="63px"><br style="line-height:7px;"><b>Fecha Certificación: </b></td><td style="" width="134px"><br style="line-height:7px;"> {$sellosat.fechatimbrado}</td></tr>	               	  			
	           	  				</table>
	           	  			</td>
	           	  		</tr>
	           	  	</table>
	           	  	{/if}
           	  	</td>
           	
              	<td width="4px;" ></td>

                <td width="221px" style="text-align:justify; font-size: 19px;"><br /><br />Nombre de Quien Recibe:_____________________________________<br /><br /> 
                    	Fecha de Recibido:__________________________________________<br style="line-height:8px;"><br style="line-height:8px;">
                    	<hr>Firma de Quien Recibe
               	</td>
            </tr>
        </table>
	</div>
{if isset($sellosat.uuid) && $sellosat.uuid != ''}
	<table>
		<tr>
			<td width="100%" style="color: grey; font-size: 6pt;"><hr>Ruta XML		
			</td>
		</tr>
		<tr>
			<td width="100%" style="font-size: 6pt;">{$sellosat.rutaxml}
			<br></td>
		</tr>
		<tr>
			<td width="100%" style="color: grey; font-size: 6pt;"><hr>Cadena Original Del Complemento De Certificación Digital Del SAT			
			</td>
		</tr>
		<tr>
			<td width="100%" style="font-size: 6pt;">||{$sellosat.version}|{$sellosat.uuid}|{$sellosat.fechatimbrado}|{$sellosat.sellocfd}|{$sellosat.nocertificadosat}||
			<br></td>
		</tr>
		<tr>
			<td width="100%" style="color: grey; font-size: 6pt;"><hr>Sello Digital Del Emisor			
			</td>
		</tr>
		<tr>
			<td width="100%" style="font-size: 6pt;">{$sellosat.sellocfd}
			<br></td>
		</tr>
		<tr>
			<td width="100%" style="color: grey; font-size: 6pt;"><hr>Sello Digital SAT			
			</td>
		</tr>
		<tr>
			<td width="100%" style="font-size: 6pt;">{$sellosat.sellosat}
			<br></td>
		</tr>
	</table>
{/if}
<br>
<table width="100%">
	<tr>
        <td colspan="2" width="100%" style="text-align:center; font-size: 8pt; color: #444; font-weight: bold; font-style:italic;">
        	<em>"Debo y pagaré incondicionalmente a la orden de Farmatalam de México, S. de R.L. de C.V. la cantidad que ampara este documento, proveniente de las mercancías que se detallan en el cuerpo del mismo y y que fueron entregadas en esta fecha a petición y entera satisfacción mía."</em>
        </td>
    </tr>
    <tr>
        <td colspan="2" width="100%" style="text-align:center; font-size: 8pt; color: #444;">
        	<br>
        </td>
    </tr>
	<tr>
        <td width="50%" style="text-align:center; font-size: 7pt; color: #444;">
        	{if isset($sellosat.uuid) && $sellosat.uuid != ''}<br><br><br><em>ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI</em>{/if}
        </td>
        <td width="50%" style="text-align:center; font-size: 6pt; color: #444;">{if $bar_code != '' }{$bar_code}{/if}</td>
    </tr>
</table>
</div>