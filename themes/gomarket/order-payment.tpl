{*
* 2007-2012 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the waorld-wide-web at this URL:
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<link rel="stylesheet" href="{$css_dir}order-payment.css" type="text/css" media="screen" charset="utf-8" />
{*}<script	src="js/jquery/jquery-1.7.2.min.js" type="text/javascript"></script>{*}
<script src="/themes/gomarket/js/jquery.validate.js"></script>
{*}
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
{*}
<script src="js/jquery.form.js"></script>

{* if isset($disableBaloto) and $disableBaloto OR (isset($isblockmpb) and $isblockmpb)} 


<style type="text/css">
#imgbaloto,#mediopagob, #imgEfecty, #mediopagoe {
opacity: 0.6;
}

#textradiobaloto,#textradioefecty
{
color: #979797;

}
 
</style>
{/if}
{if isset($show_contra_entrega) and !$show_contra_entrega }
    
<style type="text/css">
#imgcontrae,#mediopagoce {
opacity: 0.6;
}

#textradiocontrae
{
color: #979797;

}
 
</style>
   
{/if *}
{literal}


	<script>

	$(function() {
		/*if (screen.width <= 425 ) {
			$( '.total_span' ).on('click',function(){
					if($(this).attr('data-click-state') == 1) {
						$(this).attr('data-click-state', 0);
						$('.ctn-desplegable-resumen-total').stop().slideUp();
					} else {
						$(this).attr('data-click-state', 1);
						$('.ctn-desplegable-resumen-total').stop().slideDown();
					}
				});
		}*/

		$( window ).resize(function() {
			// $( window ).width();
			if ($( window ).width() > 425 ) {
				$('.ctn-desplegable-resumen-total').stop().slideDown();
				$( '.total_span, .ctn-desplegable-resumen2' ).off();
				$(".ctn-flecha-desplegable2").find(".img-flecha-desplegable").css( "transform",  "rotateX(0deg)" );
			}
			else {
				$('.ctn-desplegable-resumen-total').stop().slideUp();
				$('.total_span').children(".price3").css( "border-top",  "none" );
				$(".ctn-flecha-desplegable2").find(".img-flecha-desplegable").css( "transform",  "rotateX(0deg)" );
				/*$( ".total_span" ).toggle(function() {
						$('.ctn-desplegable-resumen-total').stop().slideDown();
				}, function() {					
						$('.ctn-desplegable-resumen-total').stop().slideUp();
				});*/
				$( '.ctn-desplegable-resumen2' ).off();
				$( '.ctn-desplegable-resumen2' ).on('click',function(){
					$( '.total_span' ).attr('data-click-state', 0);
					$('.ctn-desplegable-resumen-total').stop().slideUp();
					$('.total_span').children(".price3").css( "border-top",  "none" );
					$(".ctn-flecha-desplegable2").find(".img-flecha-desplegable").css( "transform",  "rotateX(0deg)" );
				});	
				$( '.total_span' ).off();
				$( '.total_span' ).on('click',function(){
					if($(this).attr('data-click-state') == 1) {
						$(this).attr('data-click-state', 0);
						$('.ctn-desplegable-resumen-total').stop().slideUp();
						$(this).children(".price3").css( "border-top",  "none" );
						$(".ctn-flecha-desplegable2").find(".img-flecha-desplegable").css( "transform",  "rotateX(0deg)" );
					} else {
						$(this).attr('data-click-state', 1);
						$('.ctn-desplegable-resumen-total').stop().slideDown();
						$(this).children(".price3").css( "border-top",  "1px solid #c8c8c8" );
						$(".ctn-flecha-desplegable2").find(".img-flecha-desplegable").css( "transform",  "rotateX(180deg)" );
					}
				});
			}
		});
		$( window ).resize();
	});

		/*$( ".total_span" ).toggle(function() {
			if (screen.width <= 425 ) {
				$('.ctn-desplegable-resumen-total').stop().slideDown();
			}
		}, function() {
			if (screen.width <= 425 ) {
				$('.ctn-desplegable-resumen-total').stop().slideUp();
			}
		});
	});*/
	/*
	// Acordeón pagos
$( ".btn-toggle-payment" ).click(function() {
	var $this = $(this);
	if ( $this.hasClass("collapse") ) {
		$this.removeClass("collapse").addClass("expand");
		$this.parent(".payment-button").children(".content").stop().slideUp();
		return false;
	}

	$(".btn-toggle-payment").removeClass("collapse");
	$(".payment-button").children(".content").stop().slideUp();

	$this.addClass("collapse");
	$this.parent(".payment-button").children(".content").stop().slideDown();
});

// Muestra/Oculta Detalles del pedido
$( ".btn-toggle-order-detail" ).toggle(function() {
	$(this).removeClass("collapse").addClass("expand");
	$("#cart_summary").stop().fadeOut();
}, function() {
	$(this).removeClass("expand").addClass("collapse");
	$("#cart_summary").stop().fadeIn();
});*/


	/*$( ".btn-toggle-payment" ).click(function() {
		var $this = $(this);
		if ( $this.hasClass("collapse") ) {
			$this.removeClass("collapse").addClass("expand");
			$this.parent(".payment-button").children(".content").stop().slideUp();
			return false;
		}

		$(".btn-toggle-payment").removeClass("collapse");
		$(".payment-button").children(".content").stop().slideUp();

		$this.addClass("collapse");
		$this.parent(".payment-button").children(".content").stop().slideDown();
	});*/

		
		

	</script>


	<script>
		function mouse_overd(objeto, idCotainer) {
				console.log(objeto, idCotainer);
				//$('.ctn-toggle-payment').slideUp();
				//$('#' + objeto).slideDown();
				//$(idCotainer).addClass("cont-opc-pago-border");
				
				$(".cont-opc-pago").find(".invisible").children('[id^=div], .visible2').css( "background-color", "#F0F0F0" );
				$(".cont-opc-pago").find(".invisible").children('[id^=div]').stop().animate({width: '1px'});
				$(".cont-opc-pago").find(".invisible").children(".visible").css( "border-left-color", "#F0F0F0" );
				$(".cont-opc-pago").find(".cont-mas-menos").children('[id^=div]').css( "transform",  "rotate(0deg)" );

				if ( $(idCotainer).hasClass("cont-opc-pago-border") ) {
					//$('#' + objeto).slideDown();
					//$('.ctn-toggle-payment').slideUp();
					$('#' + objeto).stop().slideUp();
					$(idCotainer).removeClass("cont-opc-pago-border");
				}
				else {
					$('#' + objeto + "rb").stop().animate({width: '10px', display: 'block'});
					$('.ctn-toggle-payment').stop().slideUp();

					$('#' + objeto).stop().slideDown();
					$(".cont-opc-pago").removeClass("cont-opc-pago-border");
					$(idCotainer).addClass("cont-opc-pago-border");	

					$(".cont-opc-pago").find(".cont-mas-menos").children("#" + objeto + "im").css( "transform",  "rotate(45deg)" );

					var $ctnInvisible = $(idCotainer).find(".invisible");
					$ctnInvisible.children("#" + objeto + "rb, .visible2").css( "background-color", "#FF922E" );
					$ctnInvisible.children(".visible").css( "border-left-color", "#FF922E" );
					
					//$('#' + objeto + "rb").animate({width: 'slideUp'});
					//$('#' + objeto).slideUp();
				}


				
				//$(idCotainer).addClass("cont-opc-pago-border");
				var es_div = objeto.substring(0, 3);
				var $divs = $('#divs > div');

				if (objeto === "OXXO") {
					document.getElementById("botoncitosubmit").onclick = submitforms1;
				} else if (objeto === "div2") {
					clearDivs();
					document.getElementById("botoncitosubmit").onclick = submitforms2;
					document.getElementById("textradiocredit").style.color = '#646464';
					document.getElementById("textradiocredit").style.fontWeight = '600';
					$('[value=div2]').attr("checked", true);
				} 
				// else if (objeto === "IXE") {
				// 	document.getElementById("botoncitosubmit").onclick = submitforms3;
				// } 
				else if (objeto === "div4") {
					clearDivs();
					document.getElementById("botoncitosubmit").onclick = false;
					document.getElementById("textradiocontrae").style.color = '#646464';
					document.getElementById("textradiocontrae").style.fontWeight = '600';
					$('[value=div4]').attr("checked", true);
				} 
				else if (objeto === "div5") {
					clearDivs();
					document.getElementById("botoncitosubmit").onclick = false;
					document.getElementById("textradiodatafano").style.color = '#646464';
					document.getElementById("textradiodatafano").style.fontWeight = '600';
					$('[value=div5]').attr("checked", true);
				} 
				// else if (objeto === "BANCOMER") {
				// 	document.getElementById("botoncitosubmit").onclick = submitforms5;
				// } else if (objeto === "SANTANDER") {
				// 	document.getElementById("botoncitosubmit").onclick = submitforms6;
				// } 
				
				else if (objeto === "7ELEVEN") {
					document.getElementById("botoncitosubmit").onclick = submitforms7;
				}
				// else if (objeto === "SCOTIABANK") {
				// 	document.getElementById("botoncitosubmit").onclick = submitforms8;
				// } 
				else if (objeto === "div9") {
					clearDivs();
					document.getElementById("textradiodeposit").style.color = '#646464';
					document.getElementById("textradiodeposit").style.fontWeight = '600';
					$('[value=div9]').attr("checked", true);
				} 
				else if (objeto == "divPay") {
					clearDivs();
					document.getElementById("botoncitosubmit").onclick = false;
					document.getElementById("textPay").style.color = '#646464';
					document.getElementById("textPay").style.fontWeight = '600';
					$('[value=divPay]').attr("checked", true);
				} 
				else if (objeto == "divPayC") {
					clearDivs();
					document.getElementById("botoncitosubmit").onclick = false;
					document.getElementById("textPayC").style.color = '#646464';
					document.getElementById("textPayC").style.fontWeight = '600';
					$('[value=divPayC]').attr("checked", true);
				}
					// $('#' + objeto).slideToggle();
					// $('#' + objeto + "rb").animate({width: 'toggle'});
				
				

			            /*        if (es_div === 'div') {
			             document.getElementById(objeto).style.display = 'block';
			             }
			             else
			             {
			             document.getElementById("div9").style.display = 'block';
			         }*/
		
		}

		function clearDivs() {
		     	pest = Array();
		     	rb = Array();
		     	pest[1] = "#textradiocredit";
		     	pest[2] = "#textradiocontrae";
		     	pest[3] = "#textradiodeposit";
		     	rb[1] = "div2";
		     	rb[2] = "div4";
		     	rb[5] = "div5";
		     	pest[5] = "#textradiodatafano";
		     	rb[3] = "div9";
		     	pest[4] = "#textPayC";
		     	for (var i = 1; i < 6; i++) {
		     		$(pest[i]).removeAttr("style");
		     		$('[value=' + rb[i] + ']').removeAttr("checked");
		     		// $('#' + rb[i] + "rb").hide();
		     		// $('#' + rb[i]).hide();
		     		// $('#divPayrb').hide();
		     		// $('#divPayCrb').hide();
		     		// $('#divPay').hide();
		     		// $('#divPayC').hide();
		     		// $('#textPay').removeAttr("style");
		     	}
		}

		function submitforms1() {
		  	$("#formOxxo").submit();
		}

		function submitforms2() {
		     	{/literal}
		     	{if  $pasarela_de_pago === 'payulatam'}
		     	$("#formPayU").submit();
		     	{/if}
		     	{literal}
		}

		// function submitforms3() {
		//    	$("#formIxe").submit();
		// }

		// function submitforms5() {
		//    	$("#formBancomer").submit();
		// }

		// function submitforms6() {
		//    	$("#formSantander").submit();
		// }

		function submitforms7() {
		    $("#formSEleven").submit();
		}

		// function submitforms8() {
		// 	$("#formScotiabanck").submit();
		// }

		function change_mp_efectivo(obj) {
			if ($(obj).val() !== "") {
				$("#text_mediop").html(' <p style="text-justify: distribute;">Pulsa Pagar&raquo; para recibir los datos con los que podrás acercarte a un punto ' + $(obj).val() + ' y realizar tu pago.</p>');
				$("#deposito_efectivo").show();
				mouse_overd($(obj).val());
			} 
			else {
				document.getElementById("botoncitosubmit").onclick = false;
		 		$("#deposito_efectivo").hide();
		 		$("#text_mediop").html('');
		 	}
		}

		function mouse_overd_efectivo(objeto) {
		 	if (objeto === "div9") {
		 		if ($("#depostito_efectivo").val() === "") {
		 			document.getElementById("botoncitosubmit").onclick = false;
		 			mouse_overd("div9");
				} 
				else {
		     		mouse_overd($("#depostito_efectivo").val());
		     	}
		    }
		}
	</script>
{/literal}




<script>
    function validar_texto(e) {
    	tecla = (document.all) ? e.keyCode : e.which;
        //Tecla de retroceso para borrar, siempre la permite
        if ((tecla == 8) || (tecla == 0)) {
        	return true;
        }
        // Patron de entrada, en este caso solo acepta numeros
        patron = /[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function pulsar(e) {
    	tecla = (document.all) ? e.keyCode : e.which;
    	return (tecla != 13);
    }
</script>

{if !$opc}
    <script type="text/javascript">
        // <![CDATA[
        var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
        var currencyRate = '{$currencyRate|floatval}';
        var currencyFormat = '{$currencyFormat|intval}';
        var currencyBlank = '{$currencyBlank|intval}';
        var txtProduct = "{l s='product' js=1}";
        var txtProducts = "{l s='products' js=1}";
        // ]]>
    </script>
{/if}

{* if !$opc}<div class="titulo-pasos">Modos de pago</div>{/if *}
{* <a href="{$link->getPageLink('order', true, NULL, "step=1")}" id="navegarAtras2" title="{l s='Previous'}" > 
<< Anterior</a> *}

<div id="uls">
	{if !$opc}
		{assign var='current_step' value='payment'}
		{include file="$tpl_dir./order-steps.tpl"}
		{include file="$tpl_dir./errors.tpl"}
	{else}
		</div>
		<div id="opc_payment_methods" class="opc-main-block">
			<h2><span>3</span> {l s='Choose your payment method'}</h2>
			<div id="opc_payment_methods-overlay" class="opc-overlay" style="display: none;"></div>
	{/if}

	<!-- contenedor  -->
	<div class="paiement_block">
		<div id="HOOK_TOP_PAYMENT" >{$HOOK_TOP_PAYMENT}</div>
		{if $HOOK_PAYMENT}
			{if !$opc}
				<!-- tabla resumen producto -->
				<div id="order-detail-content5" class="table_block"  >
					<!-- style="border: solid #0000CC !important;" -->
					<div id="cart_summary"  class="std" >
						<!-- productos --> 
						<div id="contenedor_total">
							{if $errors_pay === 'true'}
								<div id="errors_pay" class="error">
									{foreach name=outer item=error from=$errors_msgs}
										{foreach key=key item=item from=$error}
											{$item}&nbsp;&nbsp; 
										{/foreach} <br />
									{/foreach}
									Verifica tus datos e intenta de nuevo o utiliza otro medio de pago.
								</div>
							{/if}
							{foreach from=$products item=product name=productLoop}
								{assign var='productId' value=$product.id_product}
								{assign var='productAttributeId' value=$product.id_product_attribute}
								{assign var='quantityDisplayed' value=0}
								{assign var='cannotModify' value=1}
								{assign var='odd' value=$product@iteration%2}
								{assign var='noDeleteButton' value=1}
								{* Display the product line *}
								<!-- Imprime productos -->
								{*include file="$tpl_dir./shopping-cart-product-line-formula.tpl"*}
								{* Then the customized datas ones*}
								{if isset($customizedDatas.$productId.$productAttributeId)}
									{foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization'}
										<div id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" class="alternate_item cart_item">
											<!-- NPI -->	
											<div>
												{foreach from=$customization.datas key='type' item='datas'}
													{if $type == $CUSTOMIZE_FILE}
														<div class="customizationUploaded"  >
															<ul class="customizationUploaded">
																{foreach from=$datas item='picture'}
																	<li>
																		<img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" />
																	</li>
																{/foreach}
															</ul>
														</div>
													{elseif $type == $CUSTOMIZE_TEXTFIELD}
														<ul class="typedText">
															{foreach from=$datas item='textField' name='typedText'}
																<li>
																	{if $textField.name}
																		{l s='%s:' sprintf=$textField.name}
																	{else}
																		{l s='Text #%s:' sprintf=$smarty.foreach.typedText.index+1}
																	{/if}
																	{$textField.value}
																</li>
															{/foreach}
														</ul>
													{/if}
												{/foreach}
											</div>

											<!-- eliminar producto -->
											<div class="cart_quantity" >
												{if isset($cannotModify) AND $cannotModify == 1}
													<span style="float:left">
														{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}
														{else}{$product.cart_quantity-$quantityDisplayed}
														{/if}
													</span>
												{else}
													<div style="float:right">
														<a rel="nofollow" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "delete&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}">
															<img src="{$img_dir}icon/delete.gif" alt="{l s='Delete'}" title="{l s='Delete this customization'}" width="11" height="13" class="icon" />
														</a>
													</div>
													<div id="cart_quantity_button" style="float:left">
														<a rel="nofollow" class="cart_quantity_up" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;token={$token_cart}")}" title="{l s='Add'}">
															<img src="{$img_dir}icon/quantity_up.gif" alt="{l s='Add'}" width="14" height="9" />
														</a><br />
														{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
															<a rel="nofollow" class="cart_quantity_down" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="{$link->getPageLink('cart', true, NULL, "add&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")}" title="{l s='Subtract'}">
																<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
															</a>
														{else}
															<a class="cart_quantity_down" style="opacity: 0.3;" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" href="#" title="{l s='Subtract'}">
																<img src="{$img_dir}icon/quantity_down.gif" alt="{l s='Subtract'}" width="14" height="9" />
															</a>
														{/if}
													</div>
													<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_hidden"/>
													<input size="2" type="text" value="{$customization.quantity}" class="cart_quantity_input" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}"/>
												{/if}
											</div>
											<div class="cart_total" ></div>
										</div>
										{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
									{/foreach}
									{* If it exists also some uncustomized products *}
									{if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl"}
									{/if}
								{/if}
							{/foreach}

							<!-- Contenedor izquierdo -->
							<div class="cont_izquierdo">
								<!-- Resumen -->
								<div  class="resumen">Resumen:</div>
									<div class="columna1">
										<div class="columna_resumen">
											<div class="ctn-desplegable-resumen-total" >
												<div class="ctn-desplegable-resumen2">
													<div  class="resumen2">
														Resumen:
													</div>
													<div class="ctn-flecha-desplegable">
														<img src="{$img_dir}mediosp/flecha-desplegable.png">
													</div>
												</div>
												<div id="contenedorProducto" >
													<div id="productos" >
														{foreach from=$products item=product name=productLoop}
															{assign var='productId' value=$product.id_product}
															{assign var='productAttributeId' value=$product.id_product_attribute}
															{assign var='quantityDisplayed' value=0}
															{assign var='cannotModify' value=1}
															{assign var='odd' value=$product@iteration%2}
															{assign var='noDeleteButton' value=1}
															{* Display the product line *}
															
															<!-- Imprime productos -->
															{include file="$tpl_dir./shopping-cart-product-line-formula.tpl"}
														{/foreach}
														<!-- productos de regalo -->
														{assign var='last_was_odd' value=$product@iteration%2}
														{foreach $gift_products as $product}
															{assign var='productId' value=$product.id_product}
															{assign var='productAttributeId' value=$product.id_product_attribute}
															{assign var='quantityDisplayed' value=0}
															{assign var='odd' value=($product@iteration+$last_was_odd)%2}
															{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
															{assign var='cannotModify' value=1}

															{* Display the gift product line *}
															{include file="$tpl_dir./shopping-cart-product-line-formula.tpl" productLast=$product@last productFirst=$product@first}
														{/foreach}
													</div>
												</div>
											
												<!--Inicio contenedor precio-->
												<div  id="contenedorPrecio">
													<!-- total Productos -->
													<div class="cart_total_price">
														<div class="descripcion">
															Sub total:
														</div>
														{if $use_taxes}
															{if $priceDisplay}
																<div class="price2" id="total_product">
																	{displayPrice price=$total_products}
																</div>
															{else}
																<div class="price2" id="total_product">
																	{displayPrice price=$total_products_wt}
																</div>
															{/if}
														{else}
															<div class="price2" id="total_product">
																{displayPrice price=$total_products}
															</div>
														{/if}
													</div>
													<!-- total envio -->
													{if $total_shipping_tax_exc <= 0 && !isset($virtualCart)}
														<div class="cart_total_price" style="{if !isset($carrier->id) || is_null($carrier->id)}display:none;{/if}">
															<div class="descripcion">
																Envío:
															</div>
															<div class="price2" id="total_shipping">
																{l s='Free Shipping!'}
															</div>
														</div>
													{else}
														{if $use_taxes && $total_shipping_tax_exc != $total_shipping}
															{if $priceDisplay}
																<div class="cart_total_price" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
																	<div class="descripcion">
																		Envío:
																	</div>
																	<div class="price2" id="total_shipping">
																		{displayPrice price=$total_shipping_tax_exc}
																	</div>
																</div>
															{else}
																<div class="cart_total_price" {if $total_shipping <= 0} style="display:none;"{/if}>
																	<div class="descripcion">
																		Envío:
																	</div>
																	<div class="price2" id="total_shipping" >
																		{displayPrice price=$total_shipping}
																	</div>
																</div>
															{/if}
														{else}
															<div class="cart_total_price" {if $total_shipping_tax_exc <= 0} style="display:none;"{/if}>
																<div class="descripcion">
																	Envío:
																</div>
																<div class="price2" id="total_shipping" >
																	{displayPrice price=$total_shipping_tax_exc}
																</div>
															</div>
														{/if}
													{/if}
													<!-- descuentos -->
													{if count($discounts)}
														<div class="cart_total_price">
															{foreach from=$discounts item=discount name=discountLoop}
																<div class="cart_discount {if $smarty.foreach.discountLoop.last}last_item{elseif $smarty.foreach.discountLoop.first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
																	<div class="descripcion">
																		<span class="celeste">
																			Cupón: {* $discount.name *} 
																		</span>
																	</div>
																	<div class="price2" id="descuentos">
																		<span>
																			{if $discount.value_real > 0}
																				{if !$priceDisplay}
																					{displayPrice price=$discount.value_real*-1}
																				{else}
																					{displayPrice price=$discount.value_tax_exc*-1}
																				{/if}
																			{/if}
																		</span>
					                                        		</div>
					                                    		</div>
					                                		{/foreach}
				                                		</div>
				                        			{/if}
				                        			<!-- fin descuentos -->
				                    			</div>
				                    			<!-- fin contenedor precio -->


					                    		{* <!-- inicio cupon -->
				                        		<div class="cont_cupon_txt">
													<div class="txt_green">
														Cupón:
													</div>
													<div class="total_cupon" >
														-{$discounts|@print_r}
													</div>
												</div> *}
												<!-- fin cupon -->

				                    		</div>
			                    			<!-- Total compra  -->


		                        			<div class="total_span">				
		                           				{if $use_taxes}
		                              				<div class="total_compra">
		                              					{l s='Total:'}
		                              				</div>
		                              				<div class="price3" id="total_price_container">
		                                				<span id="total_price">{displayPrice price=$total_price}</span>
		                                				<div class="ctn-flecha-desplegable2">
															<img class="img-flecha-desplegable" src="{$img_dir}mediosp/flecha-desplegable2.png">
														</div>
		                                			</div>
		                                			
		                            			{else}
		                                			<div class="total_compra">
		                                				{l s='Total:'}
		                                			</div>
		                                			<div class="price3" id="total_price_container">
		                                				<span id="total_price">{displayPrice price=$total_price_without_tax}</span>
			                                			<div class="ctn-flecha-desplegable2">
															<img class="img-flecha-desplegable" src="{$img_dir}mediosp/flecha-desplegable2.png">
														</div>
		                                			</div>
		                            			{/if}
		                        			</div>
			                       			<!-- FIN Total compra  -->
			                       		</div>
										<!--Fin resumen-->								

										<!-- inicio cupones -->
										<div class="cont_cupones">
											{if sizeof($discounts)}
												<div class="suc_dis">Código registrado con éxito
												</div>
											{else}
												{if $voucherAllowed}
													{if isset($errors_discount) && $errors_discount}
														<ul class="error">
															{foreach $errors_discount as $k=>$error}
																<li>
																	{$error|escape:'htmlall':'UTF-8'}
																</li>
															{/foreach}
														</ul>
													{/if}
													<form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
														<fieldset id="addDiscount">
															<p class="label_cupon">
																¿Tienes algún cupón de descuento?
																</p>
															<p class="label_cupon" style="display: none;">
																<br/>¡Opcional! Ingresa:
																<input type="radio" name="type_voucher" value="md" > <b>Nombre del médico &nbsp; &nbsp;-&nbsp; 
																<input type="radio" name="type_voucher" value="cupon" checked="checked"> Cupón Promocional</b>
															</p>
															<div class="cont_input_cupon">
																<input type="text" class="discount_name" placeholder="Ingrésalo aquí" id="discount_name" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
																<input type="hidden" name="doc_fnd" id="doc_fnd" value="">
																<input type="hidden" name="submitDiscount" />
																<input type="submit" name="submitAddDiscount" id="submitAddDiscount" value="{l s='Aplicar'}" class="button"/>
															</div>
														</fieldset>
													</form>
													{if $displayVouchers}
														<p id="title" class="title_offers">{l s='Take advantage of our offers:'}</p>
														<div id="display_cart_vouchers">
															{foreach $displayVouchers as $voucher}
																{if $voucher.code != ''}
																	<span onclick="$('#discount_name').val('{$voucher.code}');return false;" class="voucher_name">
																		{$voucher.code}
																	</span> - 
																{/if}
																{$voucher.name}
																<br/>
															{/foreach}
														</div>
													{/if}
												{/if}
											{/if}
										</div>
										<!--fin cupones-->
                                                                                
                                                                                <div class="ctn-medicos">
                                                                                    <br><span class="span-medicos">Ingrese un médico:</span>
                                                                                    <input type="text" id="input-medicos">
                                                                                    <input type="hidden" id="input-medicos-val">
                                                                                </div>
                                                                                
                                                                                <script>
                                                                                    {*var availableTags;
                                                                                    $('#input-medicos').keyup(function(){
                                                                                        if( this.value.length >= 3 ){
                                                                                            var medico = $(this).val();
                                                                                            $.post( "{$base_dir}ajaxs/ajax_servier_medicos.php", { medico: medico })
                                                                                                .done(function( data ) {
                                                                                                    console.log("Respuesta del ajax: "+data);
                                                                                                }, "json");
                                                                                        }
                                                                                    });*}
                                                                                </script>
                                                                                

    
                                                                                <script type="text/javascript">
                                                                                // <![CDATA[
                                                                                $('document').ready( function() {
                                                                                    
                                                                                    function log( message ) {
                                                                                        $( "<div>" ).text( message ).prependTo( "#input-medicos-val" );
                                                                                       
                                                                                      }
                                                                                    $("#input-medicos").autocomplete({
                                                                                        source: function( request, response ) {
                                                                                          $.ajax( {
                                                                                            url: '{$base_dir}ajaxs/ajax_servier_medicos.php',
                                                                                            dataType: "jsonp",
                                                                                            data: {
                                                                                              term: request.term
                                                                                            },
                                                                                            success: function( data ) {
                                                                                              response( data[0] );
                                                                                            }
                                                                                          } );
                                                                                        },
                                                                                        minLength: 3,
                                                                                        select: function( event, ui ) {
                                                                                          log( ui.item.id );
                                                                                        }
                                                                                  } );
                                                                                            {*'{$base_dir}ajaxs/ajax_servier_medicos.php', {
                                                                                                minChars: 3,
                                                                                                max: 10,
                                                                                                width: 500,
                                                                                                selectFirst: false,
                                                                                                scroll: false,
                                                                                                dataType: "json",
                                                                                                formatItem: function(data, i, max, value, term) {
                                                                                                    return value;
                                                                                                },
                                                                                                parse: function(data) {
                                                                                                    console.log(data[0]);
                                                                                                    var mytab = new Array();
                                                                                                    if(data == "") {
                                                                                                        var vacio = new Array();
                                                                                                        
                                                                                                        mytab[mytab.length] = { data: vacio,value: ''};
                                                                                                    } 
                                                                                                    else {
                                                                                                        for (var i = 0; i < data[0].length; i++) {
                                                                                                            mytab[mytab.length] = { data: data[0][i], value: '<div>'+data[0][i].nombres+' '+data[0][i].apellidos+'</div>'};
                                                                                                        }
                                                                                                    }
                                                                                                    return mytab;
                                                                                                },
                                                                                                
                                                                                                [[{"id_servier":"326","nombres":"ROBERTO","apellidos":"BARBA PADILLA"},{"id_servier":"327","nombres"
:"ALFONSO CARLOS","apellidos":"CASTILLO ALARCON"},{"id_servier":"334","nombres":"FRANCISCO DANIEL","apellidos"
:"GIL SANCHEZ"},{"id_servier":"335","nombres":"FERNANDO ARTURO","apellidos":"GO\u00d1I FLORES"},{"id_servier"
:"339","nombres":"CARLOS","apellidos":"MEDINA MARIN"},{"id_servier":"346","nombres":"JOSE","apellidos"
:"ESQUIVEL PINEDA"},{"id_servier":"347","nombres":"LILIA","apellidos":"AGUILERA RIOS"},{"id_servier"
:"350","nombres":"JES\u00daS","apellidos":"ALC\u00c1NTAR RAM\u00cdREZ"},{"id_servier":"353","nombres"
:"CECILIO","apellidos":"ENRIQUEZ CERVANTES"},{"id_servier":"356","nombres":"JOSE FERMIN","apellidos"
:"PEREZ CORONEL"}]]

                                                                                                }
                                                                                            }
                                                                                        )
                                                                                        .result(function(event, data, formatted) {
                                                                                            $('#input-medicos').val( data[0].nombres, data[0].apellidos );
                                                                                        })*}
                                                                                });
                                                                                // ]]>
                                                                                </script>
                                                                                
                                                                                

									</div>
	                        	</div>
							</div>
							<!--fin cont_izquierdo-->

							<!--inicio contenedor derecho-->
							<!-- medios de pago -->
							<div class="cont_derecho">
								<div class="columna2" style="float:right">
					        	   	<div class="seleccionPago" >
					           			<label id="sele" >Selecciona un medio de pago</label>
					                </div>
									<div id="contenedor_medios" >
					                	<div id="cart_voucher" class="cart_voucher" style="float: top; display: none;">
					                		{if $voucherAllowed}
					                			{if isset($errors_discount) && $errors_discount}
					                				<ul class="error">
					                					{foreach from=$errors_discount key=k item=error}
					                						<li>{$error|escape:'htmlall':'UTF-8'}</li>
					                					{/foreach}
					                				</ul>
					                			{/if}
					                		{/if}
					                	</div>
					                	{/if}
					                	<div>
					                		{if $opc}
					                			<div id="opc_payment_methods-content" >
					                		{/if}
						                			<div id="HOOK_PAYMENT">
						                				<div style="position:relative;float:right;">
						                					<img src="{$img_dir}mediosp/seguridad.png" id="imagenayuda" style="position:absolute;top:49px;right:18px;z-index:100;display:none;"/>
						                				</div>
						                				{$HOOK_PAYMENT} 
						                			</div>
						                	{if $opc}
						                		</div>
						                	{/if}
					                	</div>    
					                		{else}
					                			<p class="warning">{l s='No payment modules have been installed.'}</p>
					                </div>
				            	</div>
				            <!-- fin medios de pago -->
							</div>				            
                    	</div>
                	</div>
                </div>
            </div>
        {/if}
    </div>
</div>
</div>
</div>
{if !$opc}
    <!-- <p class="cart_navigation"><a href="{$link->getPageLink('order', true, NULL, "step=2")}" title="{l s='Previous'}" class="button">&laquo; {l s='Previous'}</a></p> -->

        <div id="botonesInferiores">
        	<input type="button"  name="botoncitosubmit" id="botoncitosubmit" class="botoncitosubmit1" value="&raquo;">
        	<a href="{$link->getPageLink('order', true, NULL, "step=1")}" id="navegarAtras" title="{l s='Previous'}" >
        		Anterior</a>
       </div>

{/if}
        	{literal}
        	<script type="text/javascript">


        	$(document).ready(function () {

        		function setUserID(myValue) {
        			$('#doc_fnd').val(myValue).trigger('change');
        		}

        		$('#discount_name').change(function () {
        			$('#doc_fnd').val('');

        			if ($('input:radio[name=type_voucher]:checked').val() == 'cupon') {
        				$('#submitAddDiscount').prop("disabled", false);
        			} else {
        				$('#submitAddDiscount').prop("disabled", true);
        			}
        		});

        		$('#doc_fnd').change(function () {
                //alert("cambio id doc");
                if ($('#doc_fnd').val().length === 0) {
                    //alert("medico vacio");
                    $('#submitAddDiscount').prop("disabled", true);
                } else {
                    //alert("medico si");
                    $('#submitAddDiscount').prop("disabled", false);

                }
            });

        		$('input[type=radio][name=type_voucher]').change(function () {

        			$('#discount_name').val('');
        			$('#doc_fnd').val('');

        			if (this.value == 'md') {
        				$('#submitAddDiscount').prop("disabled", true);
        				var options = {
        					script: "lisme.php?",
        					varname: "input",
        					json: true,
        					shownoresults: true,
        					maxresults: 10,
        					timeout: 7500,
        					delay: 0,
        					callback: function (obj) {
        						setUserID(obj.id); /*document.getElementById('doc_fnd').value = obj.id; */
        					}
        				};

        				var as_json = new bsn.AutoSuggest('discount_name', options);
        			} else if (this.value == 'cupon') {
        				$('#submitAddDiscount').prop("disabled", false);
        				var options = {
        					minchars: 555,
        					meth: "post",
        					script: "lisme.php?",
        					varname: "service",
        					json: true,
        					shownoresults: true,
        					maxresults: 0,
        					timeout: 0,
        					delay: 0,
        					maxheight: 0,
        					cache: false,
        					maxentries: 0,
        				};
        				$("#discount_name").css({"autocomplete": "on"});
        				var as_json = new bsn.AutoSuggest('discount_name', options);
        			}
        		});
});

</script>
{/literal}
<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>
<link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />
{literal}
<script type="text/javascript">
$(document).ready(function () {
	var options = {
		beforeSend: function ()
		{
                    //alert("antes de enviaro");
                },
                uploadProgress: function (event, position, total, percentComplete)
                {
                    //alert("CArgandooooooooo");

                },
                success: function ()
                {
                    //alert("cupon cargado satisfactorio");
                    location.reload();

                },
                complete: function (response)
                {
                    //alert("cupon cargado completo");
                    //$("#message").html("<font color='green'>"+response.responseText+"</font>");
                },
                error: function ()
                {
                    //alert("cupon NOOOOOOOOOOOOO cargado");
                    //$("#message").html("<font color='red'> ERROR: unable to upload files</font>");
                }

            };



            $("#voucher").ajaxForm(options);


        });
</script>
{/literal}
