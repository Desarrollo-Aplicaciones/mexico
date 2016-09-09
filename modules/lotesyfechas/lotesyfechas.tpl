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

<div>
	<p><b>{l s='Cambio fecha de Vencimiento' mod='lotesyfechas'}</b></p>
	<form enctype="multipart/form-data" method="post">
		<fieldset><legend><img src="{$module_template_dir}logo.gif" alt="" title="" />{l s='Ingreso de Fecha' mod='lotesyfechas'}</legend>
			<p>Con este modulo usted podrá asignar lotes y fechas de vencimiento por ICR de producto.</p>
			<table>
				<tr><td><p>ICR:</p></td><td><input type="text" name="icr" placeholder="ICR"></td></tr>
				<tr><td><p>Lote:</p></td><td><input type="text" name="batch" placeholder="lote"></td></tr>
				<tr>
					<td><p>Fecha de vencimiento:</p></td>
					<td>
						{html_select_date prefix=NULL end_year="+15"
							month_format="%m" 
							year_empty="año"
							month_empty="mes"
							day_empty="dia"
							day_format="%02d"
							field_order="YMD" time=NULL} | No Aplica : <input type="checkbox" name="fechanoaplica" id="fechanoaplica">
					</td>
				</tr>
				{*<tr><td><p>Registro Sanitario :</p></td><td><input type="text" name="regsani" placeholder="Registro Sanitario Invima"></td></tr>*}
				
				<tr><td colspan="2"><center>
					<small>Los datos que no ingrese no se actualizarán. ( Si no Ingresa la fecha de vencimiento, no se actualizará). </small>
				</center></td></tr>

				<tr><td colspan="2"><center>
					<input type="submit" name="submitUpdateDate" value="Asignar" class="button" />
				</center></td></tr>
			</table>
		</fieldset>
	</form>
</div>