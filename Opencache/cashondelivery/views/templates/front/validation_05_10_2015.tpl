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
	<script type="text/javascript" >
function validarCodPago(obj){
			var error = false;
			if(obj.id == 'cod_pagar'){
				if(obj.value == ''){
					obj.className = "error";
					error = true;
				}else{
					obj.className = "";
				}
			}
			if(obj.id == 'cod_submit'){
				if($("#cod_pagar").val() == ""){
					$("#cod_pagar").addClass('error');
					error = true;
					$("#cod_form").submit(function(e){
    					e.preventDefault();
  					});
				}else{
					if ( $.browser.msie ) {
  					$('#cod_form').unbind('submit');
  					$("#cod_form").bind("submit");
					}else{
					 $('#cod_form').unbind('submit').submit();
					}
				}
			}

		if(error){
			if($('#cod_error').length){
				$('#cod_error').remove();
			}
			$('#cod_content_pagar').append('<div id="cod_error" class="error">Selecciona el modo de pago.</div>');
		}else{
				if($('#cod_error').length){
				$('#cod_error').remove();
				//$('#cod_form').submit();
			}
		}
}
	</script>
<link rel="stylesheet" href="{$css_dir}confirmation.css" type="text/css" media="screen" charset="utf-8" />
{capture name=path}{l s='Shipping' mod='cashondelivery'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<div class="titulo-pasos">{l s='Order summation' mod='cashondelivery'}</div>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="titulos">{l s='Cash on delivery (COD) payment' mod='cashondelivery'}</div>

<form action="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}" method="post" id="cod_form">
	<input type="hidden" name="confirm" value="1" />
	<div class="imagen"><img src="{$this_path_cod}cashondelivery.jpg" alt="{l s='Cash on delivery (COD) payment' mod='cashondelivery'}" /></div>
<div style="display:table-cell; vertical-align: middle">
	<p class="parrafo">{l s='You have chosen the cash on delivery method.' mod='cashondelivery'}</p>
	<p class="parrafo">{l s='The total amount of your order is' mod='cashondelivery'}
		<span id="amount_{$currencies.0.id_currency}" class="price">{convertPrice price=$total}</span>
		{if $use_taxes == 1}
		    {l s='(tax incl.)' mod='cashondelivery'}
		{/if}
	</p>
</div>
<div id="cod_content_pagar" >
		<p class="parrafo">

		<select id="cod_pagar" name="cod_pagar" onchange="validarCodPago(this);">
			<option value="">-Seleccionar modo de pago-</option>
			<option value="COD-Efectivo">Pagar en efectivo</option>
			<option value="COD-Tarjeta">Pagar con tarjeta débito/crédito</option>
		</select>
	</p>
</div>
	<div class="titulos">
		{l s='Please confirm your order by clicking \'I confirm my order\'' mod='cashondelivery'}.
	</div>

	<div class="botones">
		<a href="{$link->getPageLink('order', true)}?step=3" class="botonotras"> {l s='Other payment methods' mod='cashondelivery'}</a>
		<input type="submit" value="{l s='I confirm my order' mod='cashondelivery'}" class="botonconfirmar" onclick="validarCodPago(this);" id="cod_submit" />
	</div>
</form>
