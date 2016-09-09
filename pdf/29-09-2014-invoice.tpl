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
<div style="font-size: 8pt; color: #444">
	<table border="0">
        <tr>
            <td>&nbsp;</td>
        </tr>
    </table>
<!-- ADDRESSES -->
<table style="width: 100%;" >
	<tr>
		
	  <td width="50%" style="width: 83%">
			{if !empty($delivery_address)}
				<table width="50%" style="width: 100%">
					<tr>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Delivery Address' pdf='true'}</span><br />
							 {$delivery_address}
						</td>
						<td style="width: 50%">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Billing Address' pdf='true'}</span><br />
							 {$invoice_address}
						</td>
					</tr>
				</table>
			{else}
		<table style="width: 100%;">
					<tr>

						<td style="width: 90%;">
							<span style="font-weight: bold; font-size: 10pt; color: #9E9F9E">{l s='Billing & Delivery Address.' pdf='true'}</span><br />
							 
						</td>
					</tr>
					<tr>
						<td style="width: 98%;">
							{$invoice_address}
						</td>						
					</tr>
		</table>

        <p>{/if} </p>

        <p>&nbsp;</p></td>
      <td width="50%" style="width: 17%"><b>Factura de Venta:</b>			{$title|escape:'htmlall':'UTF-8'}<br />
<b>N&uacute;mero de Pedido:</b>			{$order->getUniqReference()}<br />
<b>Fecha de Pedido:</b>			{dateFormat date=$order->date_add full=0}<br />
			
 <div style="line-height: 1pt; height: auto; text-align: center;">&nbsp;
<img src="{$img_ps_dir}{$current_state_img}" style="width: 150px; height: auto; text-align: center;"/>
</div>
            <!-- / CUSTOMER INFORMATION -->
      </td>
	</tr>
</table>
<!-- / ADDRESSES -->
<table style="width: 100%; font-size: 8pt;">
				<tr style="line-height:4px; ">
					<td style="text-align: left; background-color: #3A9842; color: #FFF; padding-left: 10px; font-weight: bold; width: {if !$tax_excluded_display}35%{else}45%{/if}">{l s='Product / Reference' pdf='true'}</td>
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="background-color: #3A9842; color: #FFF; text-align: right; font-weight: bold; width: 20%">{l s='Unit Price' pdf='true'} <br />{*l s='(Tax Excl.)' pdf='true'*}</td>
					{/if}
					<td style="background-color: #3A9842; color: #FFF; text-align: right; font-weight: bold; width: 10%"> 
						{l s='tax' pdf='true'}
						{*if $tax_excluded_display}
							 {l s='(Tax Excl.)' pdf='true'}
						{else}
							 {l s='(Tax Incl.)' pdf='true'}
						{/if*}
					</td>
					<td style="background-color: #3A9842; color: #FFF; text-align: right; font-weight: bold; width: 10%; white-space: nowrap;">&nbsp;</td>
					<td style="background-color: #3A9842; color: #FFF; text-align: center; font-weight: bold; width: 10%">{l s='Qty' pdf='true'}</td>
					<td style="background-color: #3A9842; color: #FFF; text-align: right; font-weight: bold; width: {if !$tax_excluded_display}15%{else}25%{/if}">
						{l s='Total a Pagar' pdf='true'}
						
					</td>
				</tr>
				<!-- PRODUCTS -->
				{assign var="iva_calc_tot" value="0"} <!--total tax / product -->
				{foreach $order_details as $order_detail}
				{cycle values='#FFF,#DDD' assign=bgcolor}
				<tr style="line-height:6px;">
					<td style="text-align: left; width: {if !$tax_excluded_display}35%{else}45%{/if}">{$order_detail.product_name}</td>
					<!-- unit price tax excluded is mandatory -->
					{if !$tax_excluded_display}
						<td style="text-align: right; width: 20%">
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
						</td>
					{/if}
					<td style="text-align: right; width: 10%">					

					{*if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_detail.unit_price_tax_incl}
					{/if*}
					{if $order_detail.unit_price_tax_excl eq $order_detail.unit_price_tax_incl}
					0
					{else}
						{assign var="iva_calc" value=$order_detail.unit_price_tax_incl-$order_detail.unit_price_tax_excl}
						{displayPrice currency=$order->id_currency price=$iva_calc}

						{assign var="iva_calc_tot" value=$iva_calc_tot + ($iva_calc * $order_detail.product_quantity)}

					{/if}
					</td>
					<td style="text-align: right; width: 10%">&nbsp;</td>
					<td style="text-align: center; width: 10%">{$order_detail.product_quantity}</td>
					<td style="text-align: right;  width: {if !$tax_excluded_display}15%{else}25%{/if}">
					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_detail.total_price_tax_incl}
					{/if}
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
						<td style="line-height:3px;text-align:left;width:60%;vertical-align:top" colspan="{if !$tax_excluded_display}5{else}4{/if}">{$cart_rule.name}</td>
						<td>
							{if $tax_excluded_display}
								- {$cart_rule.value_tax_excl}
							{else}
								- {$cart_rule.value}
							{/if}
						</td>
					</tr>
				{/foreach}
				<!-- END CART RULES -->
			</table>
            <table style="width: 100%">
				{if (($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl) > 0)}
				<tr style="line-height:5px;">
					<td style="width: 83%; text-align: right; font-weight: bold">{l s='Product Total (Tax Excl.)' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_products}</td>
				</tr>
				
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total Tax' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$iva_calc_tot} {*($order_invoice->total_paid_tax_incl - $order_invoice->total_paid_tax_excl)*}</td>
				</tr>
			
				<tr style="line-height:5px;">
					<td style="width: 83%; text-align: right; font-weight: bold">{l s='Product Total (Tax Incl.)' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_products_wt}</td>
				</tr>
				{else}
				<tr style="line-height:5px;">
					<td style="width: 83%; text-align: right; font-weight: bold">{l s='Product Total' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_products}</td>
				</tr>
				{/if}

				{if $order_invoice->total_discount_tax_excl > 0} {*total_discount_tax_incl*}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold"> {if $apoyosalud!=NULL}{$apoyosalud}{else}Descuento{/if}{*l s='Total Vouchers' pdf='true'*}</td>
					<td style="width: 17%; text-align: right;">-{displayPrice currency=$order->id_currency price=($order_invoice->total_discount_tax_incl)}</td>
				</tr>
				{/if}

				{if $order_invoice->total_wrapping_tax_incl > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Wrapping Cost' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">

					{if $tax_excluded_display}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_wrapping_tax_excl}
					{else}
						{displayPrice currency=$order->id_currency price=$order_invoice->total_wrapping_tax_incl}
					{/if}
					</td>
				</tr>
				{/if}

				{if $order_invoice->total_shipping_tax_incl > 0}
				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Shipping Cost' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">
						{*if $tax_excluded_display}
							{displayPrice currency=$order->id_currency price=$order_invoice->total_shipping_tax_excl}
							{else}
							{displayPrice currency=$order->id_currency price=$order_invoice->total_shipping_tax_incl}
						{/if*}

						{if $facturaValida eq "BOGOTá, D.C."}

						{if $facturaValida2 eq "FARMALISTO"}

						{if $facturaValida3 eq 'CALLE 129A NO. 56B - 23' ||
											 $facturaValida3 eq 'CALLE 129A NUMERO 56B - 23' ||
											 $facturaValida3 eq 'CALLE 129A # 56B - 23' ||
											 $facturaValida3 eq 'CARRERA 56B NO. 129A - 23' ||
											 $facturaValida3 eq 'CARRERA 56B NUMERO 129A - 23' ||
											 $facturaValida3 eq 'CARRERA 56B # 129A - 23' ||
											 $facturaValida3 eq 'CALLE 129A NO. 56B 23' ||
											 $facturaValida3 eq 'CALLE 129A NUMERO 56B 23' ||
											 $facturaValida3 eq 'CALLE 129A # 56B 23' ||
											 $facturaValida3 eq 'CARRERA 56B NO. 129A 23' ||
											 $facturaValida3 eq 'CARRERA 56B NUMERO 129A 23' ||
											 $facturaValida3 eq 'CARRERA 56B # 129A 23' ||
											 $facturaValida3 eq 'CALLE 129A NO. 56B-23' ||
											 $facturaValida3 eq 'CALLE 129A NUMERO 56B-23' ||
											 $facturaValida3 eq 'CALLE 129A # 56B-23' ||
											 $facturaValida3 eq 'CARRERA 56B NO. 129A-23' ||
											 $facturaValida3 eq 'CARRERA 56B NUMERO 129A-23' ||
											 $facturaValida3 eq 'CARRERA 56B # 129A-23'}
							--

							{else}

						{assign var="shipping_discount_tax_excl" value=($order_invoice->total_shipping_tax_incl/1.16)}

						{assign var="shipping_discount_tax_value" value=($order_invoice->total_shipping_tax_incl - $shipping_discount_tax_excl)}
						
						{displayPrice currency=$order->id_currency price=$shipping_discount_tax_excl}

						{/if}
						
						{else}

						{assign var="shipping_discount_tax_excl" value=($order_invoice->total_shipping_tax_incl/1.16)}

						{assign var="shipping_discount_tax_value" value=($order_invoice->total_shipping_tax_incl - $shipping_discount_tax_excl)}
						
						{displayPrice currency=$order->id_currency price=$shipping_discount_tax_excl}
						
						{/if}

						{else}

						{assign var="shipping_discount_tax_excl" value=($order_invoice->total_shipping_tax_incl/1.16)}

						{assign var="shipping_discount_tax_value" value=($order_invoice->total_shipping_tax_incl - $shipping_discount_tax_excl)}
						
						{displayPrice currency=$order->id_currency price=$shipping_discount_tax_excl}
						{/if}

					</td>
				</tr>

				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Shipping Tax' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">
						<!--aca voy-->
						
					{if $facturaValida eq "BOGOTá, D.C."}
					

						{if $facturaValida2 eq "FARMALISTO"}
						
						{if $facturaValida3 eq 'CALLE 129A NO. 56B - 23' ||
											 $facturaValida3 eq 'CALLE 129A NUMERO 56B - 23' ||
											 $facturaValida3 eq 'CALLE 129A # 56B - 23' ||
											 $facturaValida3 eq 'CARRERA 56B NO. 129A - 23' ||
											 $facturaValida3 eq 'CARRERA 56B NUMERO 129A - 23' ||
											 $facturaValida3 eq 'CARRERA 56B # 129A - 23' ||
											 $facturaValida3 eq 'CALLE 129A NO. 56B 23' ||
											 $facturaValida3 eq 'CALLE 129A NUMERO 56B 23' ||
											 $facturaValida3 eq 'CALLE 129A # 56B 23' ||
											 $facturaValida3 eq 'CARRERA 56B NO. 129A 23' ||
											 $facturaValida3 eq 'CARRERA 56B NUMERO 129A 23' ||
											 $facturaValida3 eq 'CARRERA 56B # 129A 23' ||
											 $facturaValida3 eq 'CALLE 129A NO. 56B-23' ||
											 $facturaValida3 eq 'CALLE 129A NUMERO 56B-23' ||
											 $facturaValida3 eq 'CALLE 129A # 56B-23' ||
											 $facturaValida3 eq 'CARRERA 56B NO. 129A-23' ||
											 $facturaValida3 eq 'CARRERA 56B NUMERO 129A-23' ||
											 $facturaValida3 eq 'CARRERA 56B # 129A-23'}
							--
							{else}
						{displayPrice currency=$order->id_currency price=$shipping_discount_tax_value}	
						{/if}

							{else}
						{displayPrice currency=$order->id_currency price=$shipping_discount_tax_value}
						{/if}
							
							{else}
						{displayPrice currency=$order->id_currency price=$shipping_discount_tax_value}
						{/if}





						
					{*if $order_invoice->value_tax_excl > 0 || *}
						
						
					</td>
				</tr>
				{/if}

				
				

				<tr style="line-height:5px;">
					<td style="text-align: right; font-weight: bold">{l s='Total' pdf='true'}</td>
					<td style="width: 17%; text-align: right;">{displayPrice currency=$order->id_currency price=$order_invoice->total_paid_tax_incl}</td>
				</tr>

			</table>
                              
            <div style="vertical-align:bottom">
            {*$tax_tab*} <br />
            
	            <table width="100%">
                        {if $formu_medical}
                            <tr>
                                <td style="text-align:justify;">
                                    <p><sup>FM</sup>&nbsp;<em>Apreciado cliente, recuerde que la formula m&eacute;dica es requisito obligatorio para la venta y/o entrega de medicamentos que requieren prescripci&oacute;n m&eacute;dica seg&uacute;n el art&iacute;culo 19 decreto 2200 del a&ntilde;o 2005, sin copia de este documento nuestro transportador no entregar&aacute; el medicamento; recuerde las diferentes opciones con las que cuenta la compa&ntilde;&iacute;a para cumplir con este requisito, m&aacute;s informaci&oacute;n en <strong>www.farmalisto.com.co</strong></em></p>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;&nbsp;&nbsp;</td>
                            </tr>
                        {/if} 
	            	<tr>
	               	  <td width="48%" style="text-align:justify;"><p><br />Por medio de la presente Factura de Venta, el comprador como propietario, representante legal, su representante delegado o dependiente laboral acepta haber recibido real y materialmente las mercanc&iacute;as y/o servicios descritos en este t&iacute;tulo valor por:<br /><br /><b>
	                              {displayPrice currency=$order->id_currency price=$order_invoice->total_paid_tax_incl} ({$ValorEnLetras})</b><br /><div align="center"></div><br /><Br /><br />
	                      Lo anterior con fundamento en el art&iacute;culo 772 y siguientes del C.C. Modificados por la Ley 1231 del 17 de Julio de 2008<br>
	                     
	                      
	                      </p></td>
	                  <td width="4%">&nbsp;</td> 
	                    <td width="48%" style="text-align:justify;"><blockquote>
	                      <p>Nombre Cliente:  &nbsp;&nbsp;&nbsp;&nbsp;_________________________________<br /><br /> 
	                        Cedula:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_________________________________<br />
	                        <br />
	                        Fecha de Recibido:&nbsp;_________________________________<br /><br /><br /><br />
	                        _______________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;____________________<br />
	                        Firma Autorizada Farmalisto&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Firma Cliente</p>
	                    </blockquote></td>
	                </tr>
	            </table>
			</div>
<div style="line-height: 1pt">&nbsp;</div>

</div>