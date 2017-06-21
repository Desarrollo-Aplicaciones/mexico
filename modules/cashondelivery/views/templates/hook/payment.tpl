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

{* if isset($medios_de_pago['cashondelivery']) && $medios_de_pago['cashondelivery'] === '1' }
<div id="opciones">
	<p><a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow">
                <div class="medios_boton">
                    <div class="medios_radio"></div>
                    <div class="medios_img">  <img src="{$img_dir}mediosp/cod.png" alt="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" style="float:left;" /> </div>
                    <div class="medios_text"> <br />{l s='Pay with cash on delivery (COD)' mod='cashondelivery'} </div>
		<!--<br />{l s='You pay for the merchandise upon delivery' mod='cashondelivery'} -->
		<br style="clear:both;" />
	</a> </div> </p>
</div>
{else}
{/if *}


<div id="ctn-contra-entrega" class="cont-opc-pago">
  <div name="opcion4" id="opciones" onclick="mouse_overd('div4', '#ctn-contra-entrega');">
    <div class="invisible">
      <div id="div4rb" >
      </div>
      <div class="visible2"></div>
      <div class="visible"></div>
    </div>
    <div class="payment_module" id="textradiocontrae">
      <input type="radio" value="div4" name="mediopago" id="mediopagoce">
      <div class="image">
        <img src="{$img_dir}mediosp/pagocontentrega.png" id="imgcontrae" alt="Pago contra entrega" id="img-Pago-contra-entrega"/>
      </div>
      Pago contra entrega <img id="ayuda_contra_entrega" class="icono_ayuda" src="{$img_dir}mediosp/Icono-de-ayuda.png"/>
      <div class="cont-mas-menos">
        <img id="div4im" src="{$img_dir}mediosp/mas_menos.png">
      </div>
    </div>
  </div>
  <div id="divs">
    <div id="div4" class="ctn-toggle-payment" style="display: none;">

    {assign var="flag1" value=false}
    
		  {if isset($medios_de_pago['cashondelivery']) && $medios_de_pago['cashondelivery'] === '1'}
          <div class="ctn-vlr-total-pedido">
            El valor total de tu pedido es de <strong class="ctn-vlr-total-pedido-semibold">{displayPrice price=$total_price} (Impuestos incl.)</strong>
          </div>

    {if $total_price > $medios_de_pago['COD-Efectivo_max_amount'] && $medios_de_pago['COD-Efectivo_max_amount'] != 0.00}
      {assign "flag1" true}
        <div class="div_max_amount"> <span class="span_max_amount">Medio de pago en EFECTIVO no esta disponible</span> para compras superiores a<br>
            <span class="max_amount">&#36; {$medios_de_pago['COD-Efectivo_max_amount']|number_format:2:".":","} </span> <br>
            por favor intente con otro medio de pago.
        </div>
    {/if}

    {if $total_price > $medios_de_pago['COD-Tarjeta_max_amount'] && $medios_de_pago['COD-Tarjeta_max_amount'] != 0.00}
      {assign "flag1" true}  
        <div class="div_max_amount"> <span class="span_max_amount">Medio de pago con TERMINAL no esta disponible</span> para compras superiores a<br>
            <span class="max_amount">&#36; {$medios_de_pago['COD-Tarjeta_max_amount']|number_format:2:".":","} </span> <br>
            por favor intente con otro medio de pago.
        </div>
    {/if}
        {if !$flag1}
          <div class="ctn-vlr-total-pedido">¿Deseas pagar en efectivo o con terminal bancaria?</div>
        {/if}


           {* Si _max_amount es igual a 0.00 o si el total de la orden es inferior al máximo permitido para el medio de pago*}
        {if $medios_de_pago['COD-Efectivo_max_amount'] == 0.00 || $total_price < $medios_de_pago['COD-Efectivo_max_amount']}
          <a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}?confirm=1&cod_pagar=COD-Efectivo" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow">
            <input type="button" class="boton_pagos paymentSubmit" value="EFECTIVO" >
          </a>
          {else}
          <a href="#" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow">
            <input type="button" class="boton_pagos  paymentSubmitDisabled" value="EFECTIVO" disabled="disabled">
          </a>          
       {/if}

           {* Si _max_amount es igual a 0.00 o si el total de la orden es inferior al máximo permitido para el medio de pago*}
        {if $medios_de_pago['COD-Tarjeta_max_amount'] == 0.00 || $total_price < $medios_de_pago['COD-Tarjeta_max_amount']}
          <a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}?confirm=1&cod_pagar=COD-Tarjeta" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow">
            <input type="button" class="boton_pagos paymentSubmit" value="TERMINAL">
          </a>
                {else}
          <a href="#" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow">
            <input type="button" class="boton_pagos paymentSubmit" value="TERMINAL">
          </a>

        {/if}       
          
          {*<div>  
            <a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}?confirm=1&cod_pagar=COD-Tarjeta" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow" class="paymentSubmit">TERMINAL</a>
  			    <a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}?confirm=1&cod_pagar=COD-Efectivo" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow" class="paymentSubmit">EFECTIVO</a>
          </div>*}
		{else}
        {include file="$tpl_dir../../modules/payulatam/tpl/no_disponible.tpl"}
        {/if}
  </div>
 </div>
</div>
<div class="separador-medios-pago"></div>
<div class="row cuadro_ayuda" id="cuadro_ayuda_contra_entrega">
  <div class="col-lg-10 titulo">Pago Contra Entrega</div>
  <div class="col-lg-12 texto">Disponible sólo para la <b>Ciudad de México</b> y en montos <b>inferiores</b> a <b>$3000 MXN</b></div>
</div>
{*  <!-- Datáfono -->
<div>
  <div name="opcion5" id="opciones" onclick="mouse_overd('div5');">
    <div class="invisible">
      <div id="div5rb" style="display:none">
      </div>
      <div class="visible">
      </div>
    </div>
    <div class="payment_module" id="textradiodatafano">
      <input type="radio" value="div5" name="mediopago" id="mediopagodt">
        <div class="image">
          <img src="{$img_dir}mediosp/Datafono.jpg" id="imgdatafono" alt="Datáfono"/>
        </div>
        Datáfono contra entrega
    </div>
  </div>
  <div id="divs">
    <div id="div5" style="display: none;">
      {if isset($medios_de_pago['cashondelivery']) && $medios_de_pago['cashondelivery'] === '1'}
        <p>
          <div class="textCod">Paga tu pedido al recibirlo en la direcci&oacute;n que seleccionaste, con tus tarjetas de crédito y débito, Visa, MasterCard, Diners y American Express.</div>
          <a href="{$link->getModuleLink('cashondelivery', 'validation', [], true)|escape:'html'}?confirm=1&cod_pagar=COD-Tarjeta" title="{l s='Pay with cash on delivery (COD)' mod='cashondelivery'}" rel="nofollow" class="paymentSubmit">TERMINAL</a>
        </p>
      {else}
        {include file="$tpl_dir../../modules/payulatam/tpl/no_disponible.tpl"}
      {/if}
    </div>        
  </div>
</div>
<!-- Datáfono /-->
 *}
{* 

<!-- Tarjeta Credito -->
<div>
  <div name="opcion2" id="opciones" onclick="mouse_overd('div2');">
    <div class="invisible">
      <div id="div2rb" style="display:none">
      </div>
      <div class="visible">
      </div>
    </div>
    <div class="payment_module"  id="textradiocredit">
      <input type="radio" value="div2" name="mediopago" id="mediopagot">
      <div class="image">
        <img src="{$img_dir}mediosp/credito.jpg" alt="Tarjetas Farmalisto"/>
      </div>            
      Tarjeta de Crédito o Débito
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
<!-- Tarjeta Credito /--> *}
