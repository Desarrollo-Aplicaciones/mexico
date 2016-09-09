{*
** @author PrestaShop SA <contact@prestashop.com>
** @copyright  2007-2014 PrestaShop SA
**
** International Registered Trademark & Property of PrestaShop SA
**
** Description: PayPal "Express Checkout" buttons template (Product page, Shopping cart content page, Payment page/step)
**
** This template is displayed to the customer to allow him/her to pay with PayPal Express Checkout
** It can be either displayed on the Product pages, the Shopping cart content page depending on your preferences (Back-office addon's configuration)
** It will also always be displayed on the payment page/step to confirm the payment
**
** Step 1: The customer is clicking on the PayPal Express Checkout button from a product page or the shopping cart content page
** Step 2: The customer is redirected to PayPal and selecting a funding source (PayPal account, credit card, etc.)
** Step 3: PayPal redirects the customer to your store ("Shipping" checkout process page/step)
** Step 4: PayPal is also sending you the customer details (delivery address, e-mail address, etc.)
** If we do not have these info yet, we update your store database and create the related customer
** Step 5: The customer is selected his/her shipping preference and is redirected to the payment page/step (still on your store)
** Step 6: The customer is clicking on the second PayPal Express Checkout button to confirm his/her payment
** Step 7: The transaction success or failure is sent to you by PayPal at the following URL: http://www.mystore.com/modules/paypalusa/controllers/front/expresscheckout.php?pp_exp_payment=1
** Step 8: The customer is redirected to the Order confirmation page
**
*}

<!-- Tarjeta crédito -->
{if isset($smarty.get.paso) && $smarty.get.paso === 'pagos'}
<div id="ctn-paypal" class="cont-opc-pago">
<div id="opciones" name="payPal" onclick="mouse_overd('divPayC', '#ctn-paypal');">
    <div class="invisible">
        <div id="divPayCrb" >
        </div>
        <div class="visible2"></div>
        <div class="visible"></div>
    </div>
    <div class="payment_module" id="textPayC">
        <input type="radio" value="divPayC" name="mediopago" id="mediopagop" >
        <div class="image"><img src="{$img_dir}mediosp/pagopaypal.png" alt="{l s='Pay with PayPal' mod='paypalusa'}" id="img-Pay-with-PayPal"/></div>
        {l s='Pagar con Paypal' mod='paypalusa'}
        <div class="cont-mas-menos">
        <img id="divPayCim" src="{$img_dir}mediosp/mas_menos.png">
      </div>
    </div>
</div>
{/if}
<div id="divPayC"   class="ctn-toggle-payment"  style="display: none; ">

     {* Si _max_amount es igual a 0.00 o si el total de la orden es inferior al máximo permitido para el medio de pago*}
    {if $medios_de_pago['PayPal_max_amount'] == 0.00 || $total_price < $medios_de_pago['PayPal_max_amount']}              
    <div class="contendfrom">
        <div class="ctn-vlr-total-pedido">
                    El valor total de tu pedido es de <strong class="ctn-vlr-total-pedido-semibold">{displayPrice price=$total_price} (Impuestos incl.)</strong>
                </div>
        {if ($page_name == 'order' && (!isset($paypal_usa_express_checkout_no_token) || !$paypal_usa_express_checkout_no_token) && ((isset($smarty.get.step) && $smarty.get.step > 1) || (isset($smarty.post.step) && $smarty.post.step > 1))) || ($page_name == 'order-opc' && isset($smarty.get.isPaymentStep) && $smarty.get.isPaymentStep == true && isset($paypal_usa_express_checkout_hook_payment))}
            <p class="payment_module">

                <div id="paypal-express-checkout">
                    <form id="paypal-express-checkout-form" action="{$paypal_usa_action_payment}" method="post">
                        {if $paypal_usa_merchant_country_is_mx}
                            <input id="paypal-express-checkout-btn" type="image" name="submit" src="{$module_dir}img/boton_terminar_compra.png" alt="" style="vertical-align: middle; margin-right: 10px;float: left;" />
                            <input id="paypal-express-checkout-btn" type="image" name="submit" src="{$module_dir}img/boton_terminar_compra.png" alt="" style="vertical-align: middle; margin-right: 10px;float: left;" />
                            <p style="line-height: 50px; float: left;">{l s='Da clic para confirmar tu compra con PayPal' mod='paypalusa'}</p>
                            <div style="clear: both;"></div>
                        {else}
                            <input id="paypal-express-checkout-btn" type="image" name="submit" src="https://www.paypalobjects.com/{if $lang_iso == 'en'}en_US{else}{if $lang_iso == 'fr'}fr_CA{else}{if $lang_iso == 'es'}es_ES{else}en_US{/if}{/if}{/if}/i/bnr/horizontal_solution_PPeCheck.gif" alt="" style="vertical-align: middle; margin-right: 10px;" />
                            <input id="paypal-express-checkout-btn" type="image" name="submit" src="https://www.paypalobjects.com/{if $lang_iso == 'en'}en_US{else}{if $lang_iso == 'fr'}fr_CA{else}{if $lang_iso == 'es'}es_ES{else}en_US{/if}{/if}{/if}/i/bnr/horizontal_solution_PPeCheck.gif" alt="" style="vertical-align: middle; margin-right: 10px;" />
                            {l s='Complete your order with PayPal Express Checkout' mod='paypalusa'}
                        {/if}
                    </form>
                </div>
            </p>
            {else}
                {if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}
                    <p class="payment_module">
                {/if}
                {if isset($smarty.get.paso) && $smarty.get.paso === 'pagos'}
                <div id="paypal-express-checkout" >
                    <form id="paypal-express-checkout-form" action="{$paypal_usa_action}" method="post" onsubmit="$('#paypal_express_checkout_id_product_attribute').val($('#idCombination').val());                        $('#paypal_express_checkout_quantity').val($('#quantity_wanted').val());">
                        {if $page_name == 'product' && isset($smarty.get.id_product)}
                            <input type="hidden" id="paypal_express_checkout_id_product" name="paypal_express_checkout_id_product" value="{$smarty.get.id_product|intval}" />
                            <input type="hidden" id="paypal_express_checkout_id_product_attribute" name="paypal_express_checkout_id_product_attribute" value="0" />
                            <input type="hidden" id="paypal_express_checkout_quantity" name="paypal_express_checkout_quantity" value="0" />
                        {/if}
                        <input onclick="$('#paypal-express-checkout-btn-product').click();" type="button" class="boton_pagos paymentSubmit " name="submit" value="TARJETA CRÉDITO"/>
                        <input onclick="$('#paypal-express-checkout-btn-product').click();" type="button" class="boton_pagos paymentSubmit " name="submit" value="TARJETA DÉBITO"/>
                        <input id="paypal-express-checkout-btn-product" type="image" name="submit" src="{if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}{$module_dir}/img/submit.png{else}{$module_dir}/img/Terminar-compra.jpg{/if}" alt="" style="float: left;border:none; display:none;"/>

                        {* <input onclick="$('#paypal-express-checkout-btn-product').click();" type="image" name="submit" src="{if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}{$module_dir}/img/submit.png{else}{$module_dir}/img/Terminar-compra.jpg{/if}" alt="" style="float: left;border:none;" class="boton_pagos paymentSubmit"/>
 *}
                        {* &nbsp;&nbsp;&nbsp;
                        {if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}
                            Iniciar Sesión en Paypal
                        {else}
                            Terminar la compra
                        {/if} *}

                    </form>
                </div> {/if}
                <div style="clear: both;"></div>
                {if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}
            </p>
            {/if}

        {/if}
    </div>
      {else}

        <div class="div_max_amount"> <span class="span_max_amount">Medio de pago no disponible</span> para compras superiores a<br>
            <span class="max_amount">&#36; {$medios_de_pago['PayPal_max_amount']|number_format:2:".":","} </span> <br>
            por favor intente con otro medio de pago.
        </div>
  {/if}
</div>
</div>
<div class="separador-medios-pago"></div>
<!-- Fin tarjeta crédito --> 


{* 
<!-- Tarjeta débito -->

{if isset($smarty.get.paso) && $smarty.get.paso === 'pagos'}
<div id="opciones" name="payPal" onclick="mouse_overd('divPay');">
    <div class="invisible">
        <div id="divPayrb" style="display:none">
        </div>
        <div class="visible2"></div>
        <div class="visible"></div>
    </div>
    <div class="payment_module" id="textPay">
        <input type="radio" value="divPay" name="mediopago" id="mediopagop" >
        <div class="image"><img src="{$base_dir}modules/paypalusa/img/icon.jpg" alt="{l s='Pay with PayPal' mod='paypalusa'}"/></div>
        {l s='Pagar con Tarjeta Débito' mod='paypalusa'}
    </div>
</div>
{/if}  
<div id="divPay" style="display: none; ">              
    <div class="contendfrom">
		{if ($page_name == 'order' && (!isset($paypal_usa_express_checkout_no_token) || !$paypal_usa_express_checkout_no_token) && ((isset($smarty.get.step) && $smarty.get.step > 1) || (isset($smarty.post.step) && $smarty.post.step > 1))) || ($page_name == 'order-opc' && isset($smarty.get.isPaymentStep) && $smarty.get.isPaymentStep == true && isset($paypal_usa_express_checkout_hook_payment))}
			<p class="payment_module">
				<div id="paypal-express-checkout">
				    <form id="paypal-express-checkout-form" action="{$paypal_usa_action_payment}" method="post">
				        {if $paypal_usa_merchant_country_is_mx}
				            <input id="paypal-express-checkout-btn" type="image" name="submit" src="{$module_dir}img/boton_terminar_compra.png" alt="" style="vertical-align: middle; margin-right: 10px;float: left;" />
				            <p style="line-height: 50px; float: left;">{l s='Da clic para confirmar tu compra con PayPal' mod='paypalusa'}</p>
				            <div style="clear: both;"></div>
				        {else}
				            <input id="paypal-express-checkout-btn" type="image" name="submit" src="https://www.paypalobjects.com/{if $lang_iso == 'en'}en_US{else}{if $lang_iso == 'fr'}fr_CA{else}{if $lang_iso == 'es'}es_ES{else}en_US{/if}{/if}{/if}/i/bnr/horizontal_solution_PPeCheck.gif" alt="" style="vertical-align: middle; margin-right: 10px;" /> {l s='Complete your order with PayPal Express Checkout' mod='paypalusa'}
				        {/if}
				    </form>
				</div>
			</p>
			{else}
				{if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}
				    <p class="payment_module">
				{/if}
                                {if isset($smarty.get.paso) && $smarty.get.paso === 'pagos'}
				<div id="paypal-express-checkout" >
				    <form id="paypal-express-checkout-form" action="{$paypal_usa_action}" method="post" onsubmit="$('#paypal_express_checkout_id_product_attribute').val($('#idCombination').val());						$('#paypal_express_checkout_quantity').val($('#quantity_wanted').val());">
				        {if $page_name == 'product' && isset($smarty.get.id_product)}
				            <input type="hidden" id="paypal_express_checkout_id_product" name="paypal_express_checkout_id_product" value="{$smarty.get.id_product|intval}" />
				            <input type="hidden" id="paypal_express_checkout_id_product_attribute" name="paypal_express_checkout_id_product_attribute" value="0" />
				            <input type="hidden" id="paypal_express_checkout_quantity" name="paypal_express_checkout_quantity" value="0" />
				        {/if}
                        <input id="paypal-express-checkout-btn-product" type="image" name="submit" src="{if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}{$module_dir}/img/submit.png{else}{$module_dir}/img/Terminar-compra.jpg{/if}" alt="" style="float: left;border:none;"/>&nbsp;&nbsp;&nbsp;
                        {if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}
                            Iniciar Sesión en Paypal
                        {else}
                            Terminar la compra
                        {/if}

                    <!--

				        if $paypal_usa_merchant_country_is_mx}
				            <input id="paypal-express-checkout-btn-product" type="image" name="submit" src="{if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}{$module_dir}img/submit.png{else}{$module_dir}/img/express_checkout_mx.png{/if}" alt="" style="float: left;border: 0;"/>
				        {else}
				            <input id="paypal-express-checkout-btn-product" type="image" name="submit" src="{if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}https://www.paypalobjects.com/{if $lang_iso == 'en'}en_US{else}{if $lang_iso == 'fr'}fr_CA{else}{if $lang_iso == 'es'}es_ES{else}en_US{/if}{/if}{/if}/i/bnr/horizontal_solution_PPeCheck.gif{else}https://www.paypal.com/{if $lang_iso == 'en'}en_US{else}{if $lang_iso == 'fr'}fr_CA{else}{if $lang_iso == 'es'}es_ES{else}en_US{/if}{/if}{/if}/i/btn/btn_xpressCheckout.gif{/if}" alt="" />
				        {/if} -->
				    </form>
				</div>  {/if}
				<div style="clear: both;"></div>
				{if isset($paypal_usa_express_checkout_no_token) && $paypal_usa_express_checkout_no_token}
			</p>
			{/if}
			{if !isset($paypal_usa_from_error)}
				<script type="text/javascript">
				{literal}
				$(document).ready(function()
				{
				    {/literal}
				    {if $page_name == 'product'}
				        {literal}
				        $('#paypal-express-checkout-form').insertAfter('#buy_block');
				        $('#paypal-express-checkout-btn-product').css('float', 'left');
				        $('#paypal-express-checkout-btn-product').css('margin-top', '-30px');
				        {/literal}
				    {else}
				        {if !isset($paypal_usa_express_checkout_no_token) || !$paypal_usa_express_checkout_no_token}
				            {literal}
				            $('#paypal-express-checkout-btn-product').hide();
				            $('#paypal-express-checkout').insertBefore('.cart_navigation .button_large');
				            $('#paypal-express-checkout-btn-product').css('float', 'right');
				            $('#paypal-express-checkout-btn-product').css('margin-right', '5px');
				            $('.cart_navigation .button_large').css('margin-left', '5px');
				            $('#paypal-express-checkout-btn-product').show();
				            {/literal}
				        {/if}
				    {/if}
				    {literal}
				});
				{/literal}
			</script>
			{/if}
		{/if}
    </div>
</div>
<!-- Fin tarjeta débito --> *}

<!-- Deposito bancario -->

{* {assign var=contador value=0}  
{foreach key=key item=item from=$medios_de_pago_efectivo}
    {if $item eq '1'}
        {assign var=contador value=$contador+1}    
    {/if}
{/foreach}

{if $contador > 0}  *}
    <div id="ctn-deposito-efectivo" class="cont-opc-pago">
        <div name="opcion9" id="opciones" onclick="mouse_overd('div9', '#ctn-deposito-efectivo');">
            <div class="invisible">
                <div id="div9rb" >
                </div>
                <div class="visible2"></div>
                <div class="visible"></div>
            </div>
            <div class="payment_module" id="textradiodeposit">
                <input type="radio" value="div9" name="mediopago" id="mediopagop">
                <div class="image">
                    <img src="{$img_dir}mediosp/pagodepsefec.png" alt="Deposito en efectivo" id="img-Deposito-en-efectivo"/> 
                </div>
                Deposito en efectivo
                <div class="cont-mas-menos">
                    <img id="div9im" src="{$img_dir}mediosp/mas_menos.png">
                </div>
            </div> 
        </div>
            
        <div id="divs">
            <div id="div9" class="ctn-toggle-payment" style="display: none;" > 
                     {* Si _max_amount es igual a 0.00 o si el total de la orden es inferior al máximo permitido para el medio de pago*}
                    {if $medios_de_pago['OXXO_max_amount'] == 0.00 || $total_price < $medios_de_pago['OXXO_max_amount']}  
                <div class="ctn-vlr-total-pedido"  id="ultimo-ctn-vlr-total-pedido">
            El valor total de tu pedido es de <strong class="ctn-vlr-total-pedido-semibold">{displayPrice price=$total_price} (Impuestos incl.)</strong>
          </div>
                <div class="cont-form-btn" id="cont-form-btn-1">
                    <form  method="POST" action="./modules/payulatam/oxxo.php" id="formOxxo" name="formOxxo" autocomplete="off" >
                        <div class="contend-form2" >
                            <input type="image" value="payuOxxo"  name="pagar" class="paymentSubmitim" src="{$img_dir}mediosp/btnOxxo.png"/>
                            <input type="hidden" value="payuOxxo"  name="pagar" />
                        </div>
                    </form>
                </div>
                <div  class="cont-form-btn">
                    <form  method="POST" action="./modules/payulatam/sevenEleven.php" id="formSEleven" name="formSEleven" autocomplete="off" >
                        <div class="contend-form2" >
                            <input type="image" value="formSEleven" id="pagar" name="pagar" class="paymentSubmitim" src="{$img_dir}mediosp/sevenEleven.png"/>
                            <input type="hidden" value="payuSEleven"  name="pagar" />

                        </div>
                    </form>
                </div>
                {* <select id="depostito_efectivo" name="depostito_efectivo" onchange="change_mp_efectivo(this);">
                    <option value="">Medio de pago efectivo </option> 
                    {foreach key=key item=item from=$medios_de_pago_efectivo}
                        {if $item eq '1'}
                            <option value="{$key}"> {$key} </option> 
                        {/if}



                    {/foreach}
                </select> *}
                  {else}
                    <div class="div_max_amount"> <span class="span_max_amount">Medio de pago no disponible</span> para compras superiores a<br>
                        <span class="max_amount">&#36; {$medios_de_pago['OXXO_max_amount']|number_format:2:".":","} </span> <br>
                        por favor intente con otro medio de pago.
                    </div>
            {/if}
            </div>


            {* {assign var="mediop" value=""}
            
            {foreach key=key item=item from=$medios_de_pago_efectivo}
               
                {if $item eq '1'}
                    {$mediop = $key|lower}
                    {include file="$tpl_dir../../modules/payulatam/tpl/$mediop.tpl"}
            
                {/if}
            {/foreach} *}
        </div>
    </div>
    <div class="separador-medios-pago"></div>
{* {/if} *}

<!-- Fin deposito bancario -->
