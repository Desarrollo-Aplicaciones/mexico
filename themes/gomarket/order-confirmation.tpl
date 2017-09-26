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
<link rel="stylesheet" href="{$css_dir}order-confirmation.css" type="text/css" media="screen" charset="utf-8" />
{capture name=path}{l s='Order confirmation'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

{literal}
<!-- Google Code for Farmalisto Conversion Page -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 959868865;
var google_conversion_language = "en";
var google_conversion_format = "2";
var google_conversion_color = "ffffff";
var google_conversion_label = "v1u2CILBnFcQwd_ZyQM";
var google_conversion_value = 1.00;
var google_conversion_currency = "MXN";
var google_remarketing_only = false;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/959868865/?value=1.00&amp;currency_code=MXN&amp;label=v1u2CILBnFcQwd_ZyQM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>
{/literal}

<h1>{l s='Order confirmation'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

{$HOOK_ORDER_CONFIRMATION}
{$HOOK_PAYMENT_RETURN}
{*if $logged}
<p class="invoice_link"> ¿Necesitas factura? <a href="{$link->getPageLink('identity', true)}" title="Facturación">Click Aquí</a></p>
{/if*}
{literal}
<p class="titulo">
		¡Excelente, tu pedido en Farmalisto.com.mx se ha registrado con éxito!
</p>
<p class="importante">
	NOTA IMPORTANTE:
</p>
<p class="parrafo">
	<span class="strong">D.F. ó Área Metropolitana:</span> 
Sí realiza su pedido entre las 09:00 a.m. y las 17:00 hrs lo recibirá en el transcurso de la tarde del mismo día, si lo realiza después de las 17:00 hrs lo recibirá al siguiente día en el transcurso del día, este tiempo dependerá de la existencia del producto solicitado. Los pedidos serán entregados de lunes a viernes a partir de la confirmación de pago con excepción de COD (pago en la entrega) en un horario de 9:00 a.m. y hasta las 20:00 hrs y los sábados de 09:00 a.m. a 18:00 hrs.
</p>
<p class="parrafo">
	<span class="strong">Interior de la República:</span> 
En un máximo de 48 horas hábiles a partir de la confirmación del pago, aplica para transferencia bancaria, depósitos bancarios, depósitos en efectivo en OXXO, 7 Eleven, Banorte, IXE, Bancomer, PayPal y tarjeta de crédito.
</p>

<div class="recuerda_div">
	<img title="Recuerda"
	 src="img/cms/Landing-Page/Icono_Gracias.jpg"
	 alt="Recuerda"
	 class="recuerda_img"/>
	<span class="recuerda_txt1">Recuerda:</span><br><br>
	<span class="recuerda_txt2">En caso de que tu producto requiera receta médica debes presentarla en el momento de recibir tu pedido.</span>
</div>
<p class="importante2">
	Los domingos y días festivos no hacemos entregas.
</p>
<img title="Confirmación pedido"
	 src="img/cms/Landing-Page/confirmacion_mex.jpg"
	 alt="Confirmación pedido"
	 class="imagen"/>
<p class="text">
	<span class="follow">Síguenos:</span>
	<a href="https://www.facebook.com/farmalistomexico">
		<img title="facebook"
			 src="img/cms/socialmedia/REDES_SOCIALES/FB.png"
			 alt="facebook"
			 width="62"
			 height="40" />
	</a>
	<a href="https://twitter.com/farmalistomex">
		<img title="twitter"
			 src="img/cms/socialmedia/REDES_SOCIALES/TW.png"
			 alt="twitter"
			 width="62"
			 height="40" />
	</a>
	<a href="http://www.linkedin.com/company/farmalisto">
		<img title="LinkedIn"
			 src="img/cms/socialmedia/REDES_SOCIALES/IN.png"
			 alt="LinkedIn"
			 width="62"
			 height="40" />
	</a>
</p>
<p class="text">
	Consulte el estado de sus pedidos 
	<span class="strong">
{/literal}
{if $is_guest}
	<a href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html'}" title="{l s='Follow my order'}" class="text">aquí</a>
{else}
	<a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='Back to orders'}" class="text">aquí</a>
{/if}
{literal}
	</span>
</p>
<p class="text">
	<span class="strong">
		¿Tiene más preguntas? 
	</span>
	¡Visite nuesta sección de preguntas frecuentes y encontrará una respuesta inmediata!
</p>
<p class="text">
	<span class="strong">
		<a href="content/1-entregas" target="_blank" class="text">
			Clic aquí
		</a>
	</span>
</p>
<p class="text">
	<a href="/">
		<img title="Volver a la tienda"
			 src="img/cms/socialmedia/REDES_SOCIALES/perfil%20horarios-03.png"
			 alt="Volver a la tienda"
			 width="100"
			 height="101" />
	</a>
</p>
<p class="text">
	<a href="/" class="text">
		Volver a la tienda
	</a>
</p>
{/literal}





{if isset($pse) && $pse!=false }

<script type="text/javascript">

function redireccionar(){
  window.location="{$bankdest2}";
} 


$(document).ready(function(){
setTimeout ("redireccionar()", 1000); 
});
 
</script>

{/if}

