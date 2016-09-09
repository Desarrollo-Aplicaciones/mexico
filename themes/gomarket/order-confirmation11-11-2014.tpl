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

{capture name=path}{l s='Order confirmation'}{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}

<h1>{l s='Order confirmation'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

{include file="$tpl_dir./errors.tpl"}

{$HOOK_ORDER_CONFIRMATION}
{$HOOK_PAYMENT_RETURN}

<br />
{if $is_guest}
	<p>{l s='Your order ID is:'} <span class="bold">{$id_order_formatted}</span> . {l s='Your order ID has been sent via email.'}</p>
	<a href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html'}" title="{l s='Follow my order'}"><img src="{$img_dir}icon/order.gif" alt="{l s='Follow my order'}" class="icon" /></a>
	<a href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order}&email={$email}")|escape:'html'}" title="{l s='Follow my order'}">{l s='Follow my order'}</a>
{else}
	<a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='Back to orders'}"><img src="{$img_dir}icon/order.gif" alt="{l s='Back to orders'}" class="icon" /></a>
	<a href="{$link->getPageLink('history', true)|escape:'html'}" title="{l s='Back to orders'}">{l s='Back to orders'}</a>
{/if}


{literal}

<p style="text-align: center;"><span style="font-size: 24pt;"> ¡Tu pedido en Farmalisto.com.mx</span></p>
<p style="text-align: center;"><span style="font-size: 24pt;">se ha registrado con éxito!</span></p>
<p style="text-align: center;"><span><img title="Transacción exitosa!" src="http://www.farmalisto.com.mx/img/cms/banners/transaccion-exitosa-farmalisto.jpg" alt="Transacción exitosa!" height="456" width="400" /></span></p>
<p style="text-align: center;"><span style="font-size: 14pt;">Enviaremos tu pedido en breve plazo.</span></p>
<p style="text-align: center;"><span style="font-size: 14pt;">Consulta tus pedidos <a href="http://www.farmalisto.com.mx/historial-de-pedidos"><strong>acá</strong></a></span></p>
<p style="text-align: center;"><span style="font-size: 14pt;">Para cualquier pregunta comunícate con nosotros en la línea de atención y televentas:  </span></p>
<p style="text-align: center;"><span style="font-size: 14pt;">01800 913 3830 o en el correo: contacto@farmalisto.com.mx</span></p>
<p style="text-align: center;"><span><br /></span></p>
<p style="text-align: center;"><span><br /></span></p>
<p style="text-align: center;"><span><img title="Tiene preguntas?" src="http://www.farmalisto.com.mx/img/cms/banners/preguntas.jpg" alt="Tiene preguntas?" height="251" width="250" /></span></p>
<p style="text-align: center;"><span><br /></span></p>
<p style="text-align: center;"><span style="font-size: 14pt;">¿Tienes más preguntas?</span></p>
<p style="text-align: center;"><span style="font-size: 14pt;">¡Quizá en la sección de preguntas frecuentes encuentres una respuesta inmediata!</span></p>
<p style="text-align: center;"><span style="font-size: 14pt;"><strong><a href="http://www.farmalisto.com.mx/content/1-entregas" target="_blank">Clic acá</a></strong></span></p>
<p style="text-align: center;"><span><br /></span></p>
<p style="text-align: center;"><a href="http://www.farmalisto.com.mx/"><span><img title="Volver a la tienda" src="http://www.farmalisto.com.mx/img/cms/banners/volver%20a%20la%20tienda%20farmalisto.jpg" alt="Volver a la tienda" height="177" width="150" /></span></a></p>
<p style="text-align: center;"><span style="font-size: 12pt;"><a href="http://www.farmalisto.com.mx/">Volver a la tienda</a></span></p>

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

