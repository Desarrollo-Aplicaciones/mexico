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


{*   <div name="opcion4" id="opciones" onclick="mouse_overd('div4');">
    <div class="invisible">
      <div id="div4rb" style="display:none">
      </div>
      <div class="visible"></div>
    </div>
    <div class="payment_module" id="textradiocontrae">
      <input type="radio" value="div4" name="mediopago" id="mediopagoce">
      <div class="image">
        <img src="{$img_dir}mediosp/cod.jpg" id="imgcontrae" alt="Pago contra entrega"/>
      </div>
      Pago contra entrega
    </div>
  </div>
  <div id="divs">
    <div id="div4" style="display: none;">
		  {if isset($medios_de_pago['cashondelivery']) && $medios_de_pago['cashondelivery'] === '1'}
		  <p>
        <div class="textCod">Paga tu pedido al recibirlo en la direcci&oacute;n que seleccionaste</div>
			   <a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}?confirm=1&cod_pagar=COD-Efectivo" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow" class="paymentSubmit">Pagar &raquo;</a>
		</p>
		{else}
        {include file="$tpl_dir../../modules/payulatam/tpl/no_disponible.tpl"}
        {/if}
 </div>
 </div>
 </div>

 <!-- Datáfono -->
<div>
<div name="opcion5" id="opciones" onclick="mouse_overd('div5');">
  <div class="invisible">
    <div id="div5rb" style="display:none">
    </div>
    <div class="visible"></div>
  </div>
    <div class="payment_module" id="textradiodatafano">
      <input type="radio" value="div5" name="mediopago" id="mediopagodt">
      <div class="image"><img src="{$img_dir}mediosp/Datafono.jpg" id="imgdatafono" alt="Datáfono"/></div>
    Datáfono contra entrega
  </div>
</div>
<div id="divs">
 <div id="div5" style="display: none;">
    {if isset($medios_de_pago['cashondelivery']) && $medios_de_pago['cashondelivery'] === '1'}
    <p>
      <div class="textCod">Paga tu pedido al recibirlo en la direcci&oacute;n que seleccionaste, con tus tarjetas de crédito y débito, Visa, MasterCard, Diners y American Express.</div>
      <a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}?confirm=1&cod_pagar=COD-Tarjeta" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow" class="paymentSubmit">Pagar &raquo;</a>
    </p>
    {else}
        {include file="$tpl_dir../../modules/payulatam/tpl/no_disponible.tpl"}
        {/if}
 </div>        
</div>
</div>
<!-- Datáfono /--> *}

<!-- Tarjeta Credito -->
<div id="ctn-tarjetas" class="cont-opc-pago">
  <div name="opcion2" id="opciones">

    <div class="invisible">
      <div id="div2rb">
      </div>
      <div class="visible2"></div>
      <div class="visible">
      </div>
    </div>
    <div class="payment_module" id="textradiocredit">
      <input type="radio" value="div2" name="mediopago" id="mediopagot">
      <div class="image">
        <img src="{$img_dir}mediosp/tarjetas.png" alt="Tarjetas Farmalisto" id="img-tarjetas-farmalisto"/>
      </div>
      <div class="ctn-title-medio-pago">Tarjeta de Crédito o Débito <img id="ayuda_debito" class="icono_ayuda" src="{$img_dir}mediosp/Icono-de-ayuda.png"/></div>
      <div class="cont-mas-menos" onclick="mouse_overd('div2', '#ctn-tarjetas');">
        <img id="div2im" src="{$img_dir}mediosp/mas_menos.png">
      </div>
    </div> 
  </div>
  <div class="row cuadro_ayuda" id="cuadro_ayuda_debito">
    <div class="col-lg-10 titulo">Tarjeta de crédito o Débito: <img style="float:right; transform: rotate(45deg);" src="{$img_dir}mediosp/mas_menos.png"></div>
    <div class="col-lg-12 texto">Sujeto a un proceso de autenticación de seguridad. Para más información consulta nuestro aviso de privacidad.</div>
  </div>
  <div id="divs">
   {if isset($medios_de_pago['Tarjeta_credito']) && $medios_de_pago['Tarjeta_credito'] ==='1'}
    <div id="div2"  class="ctn-toggle-payment"  style="display: none; ">
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
<div class="separador-medios-pago"></div>
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
