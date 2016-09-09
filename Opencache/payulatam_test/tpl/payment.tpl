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
*  @version  Release: $Revision: 14011 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA

* 1 <a href="{$pathSsl|escape:'htmlall':'UTF-8'}modules/payulatam/payment.php" name="ref1"  id="ref1">
* 3 <a href="{$pathSsl|escape:'htmlall':'UTF-8'}modules/payulatam/payment.php">
*}
<div id="divs">
 <div id="div4" style="display: none;">
		{if isset($medios_de_pago['cashondelivery']) && $medios_de_pago['cashondelivery'] === '1'}
		<p>
			Paga tu pedido al recibirlo en la direcci&oacute;n que seleccionaste
			<a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow" class="paymentSubmit">Pagar &raquo;</a>
		</p>
		{else}
        {include file="$tpl_dir../../modules/payulatam/tpl/no_disponible.tpl"}
        {/if}
 </div>
 </div>


 {assign var=contador value=0}  
             {foreach key=key item=item from=$medios_de_pago_efectivo}
                {if $item eq '1'}
                {assign var=contador value=$contador+1}    
                {/if}
             {/foreach}

{if $contador > 0} 
<div>
<div name="opcion9" id="opciones" onclick="mouse_overd('div9');">
	<div class="invisible">
		<div id="div9rb" style="display:none">
		</div>
		<div class="visible"></div>
	</div>
<div class="payment_module" id="textradiodeposit">
    <input type="radio" value="div9" name="mediopago" id="mediopagop">
    <div class="image"><img src="{$img_dir}mediosp/efectivo.png" alt="Deposito en efectivo"/> </div>
    Deposito en efectivo
</div> 
</div>
    
<div id="divs">


    <div id="div9" style="display: none; "> 
        <select id="depostito_efectivo" name="depostito_efectivo" onchange="change_mp_efectivo(this);">
            <option value="">Medio de pago efectivo </option> 
            {foreach key=key item=item from=$medios_de_pago_efectivo}
                {if $item eq '1'}
                    <option value="{$key}"> {$key} </option> 
                {/if}



            {/foreach}
        </select>
        <div id="text_mediop"></div>
		<input type="button" onclick="$('#botoncitosubmit').click();" class="paymentSubmit" value="Pagar &raquo;" id="deposito_efectivo">
   </div>

    {assign var="mediop" value=""}

    {foreach key=key item=item from=$medios_de_pago_efectivo}

        {if $item eq '1'}
            {$mediop = $key|lower}
            {include file="$tpl_dir../../modules/payulatam/tpl/$mediop.tpl"}
        {/if}
    {/foreach}
</div>

 </div>
{/if}


<!-- Scotiabank /-->

<!-- Tarjeta Credito -->
<div>
<div name="opcion2" id="opciones" onclick="mouse_overd('div2');">
    <div class="invisible">
        <div id="div2rb" style="display:none">
        </div>
        <div class="visible"></div>
    </div>
<div class="payment_module"  id="textradiocredit">
    <input type="radio" value="div2" name="mediopago" id="mediopagot">
    <div class="image">
        <img src="{$img_dir}mediosp/credito.jpg" alt="Tarjetas Farmalisto"/>
    </div>            
    Tarjeta de crédito
</div> 
</div>
<div id="divs">
 {if isset($medios_de_pago['Tarjeta_credito']) && $medios_de_pago['Tarjeta_credito'] ==='1'}
     
<div id="div2" style="display: none; ">              
    <div class="contendfrom">
     <div style=" width: auto; height: auto; text-justify: auto;">
        {include file="$tpl_dir../../modules/payulatam/tpl/credit_card.tpl"}
     </div>

    </div>
</div>
         {else}
        <div id="div2" style="display: none;">
 {include file="$tpl_dir../../modules/payulatam/tpl/no_disponible.tpl"}
  
                       
</div>             
 {/if}         
</div>
</div>
<!-- Tarjeta Credito /-->


{*
<!-- payu -->
    <p style="background:url(https://maf.pagosonline.net/ws/fp?id={$deviceSessionId}80200);display:none"></p> 
  <img src="https://maf.pagosonline.net/ws/fp/clear.png?id={$deviceSessionId}80200" style="display:none"> 
  <script src="https://maf.pagosonline.net/ws/fp/check.js?id={$deviceSessionId}80200"></script>
<object type="application/x-shockwave-flash" 
        data="https://maf.pagosonline.net/ws/fp/fp.swf?id={$deviceSessionId}80200" width="1" height="1" id="thm_fp" style="display:none">
  <param name="movie" value="https://maf.pagosonline.net/ws/fp/fp.swf?id={$deviceSessionId}80200"/>
</object>
<!-- payu /-->
*}