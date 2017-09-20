{*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*{$smarty.cookies.display}
*}

{if isset($products)}
<script type="text/javascript">
//<![CDATA[
$(document).ready(function()
{
	//_csJnit(IS,n);
	$('.continue_shopping').unbind('click').live('click', function(){
		lightbox_hide();
	});

	$('.proceed').unbind('click').live('click', function(){
		document.location.href = "{$__PS_BASE_URI__}index.php?controller=order&paso=inicial";
	});
});

function addcartlightbox( element ) {
	var linkrewrite = element.attr('linkrewrite');
	var pricepro = Math.round( element.attr('pricepro') );

	var namepro = element.attr('namepro');
	namepro = namepro.toLowerCase().replace(/\b[a-z]/g, function(letter) {
		return letter.toUpperCase();
	});

	var imgpro = element.attr('imgpro');
	var imgpro = imgpro.split('-');

	$(".lightbox_pname").html( namepro );
	$("#lightbox_subtotal").html( pricepro );

	if ( imgpro[1] == "default" ) {
		$(".lightbox_img").attr( "src", "{$base_dir}img/p/es-default-medium_default.jpg" );
	} else {
		$(".lightbox_img").attr( "src", "{$base_dir}"+imgpro[1]+"-medium_default/"+linkrewrite+".jpg" );
	}

	standard_lightbox('buyConfirm');
}
//]]>
</script>

<div id="buyConfirm">
	<div class="lightbox_close" onclick="lightbox_hide();"></div>
	<div class="lightbox_title">Este producto se agregó correctamente al carrito</div>
	<div class="lightbox_resume">
		<img class="lightbox_img" src="" alt="" />
		<div class="lightbox_desc">
			<div class="lightbox_pname"></div>
			<div style="text-align:center">Cantidad: &nbsp;&nbsp; <span id="lightbox_qty">1</span> &nbsp;Total: &nbsp;&nbsp; <span id="lightbox_subtotal"></span></div>
		</div>
	</div>
	<div class="lightbox_subtotal">
		<div><b>Hay en total <span class="ajax_cart_quantity">0</span> productos en el carrito</b></div>
		<div>Subtotal: <b><span class="ajax_block_cart_total">$ 0</span></b></div>
	</div>
	<div class="options">
		<div class="continue_shopping">Buscar más Productos</div>
		<div class="proceed">Realizar Pago</div>
	</div>
	<div class="lightbox_payment">
		Contamos con múltiples medios de pago<br />
		<img src="{$img_dir}footer/oxxo.jpg" alt="oxxo" />
		<img src="{$img_dir}footer/deposito.jpg" alt="deposito" />
		<img src="{$img_dir}footer/paypal.jpg" alt="Tarjeta Débito" />
		<img src="{$img_dir}footer/dinners.jpg" alt="Dinner Club" />
		<img src="{$img_dir}footer/master.jpg" alt="MasterCard" />
		<img src="{$img_dir}footer/visa.jpg" alt="Visa" />
		<img src="{$img_dir}footer/amex.jpg" alt="American Express" />
		<img src="{$img_dir}footer/cod.jpg" alt="Pago Contraentrega" />
	</div>
</div>

	<ul id="product_list" class="product_grid">
	{foreach from=$products item=product name=products}
		{assign "legend_img" $product.name}
		{if !empty($product.legend)}
			{assign "legend_img" $product.legend}
		{/if}	
		<li class="{if isset($grid_product)}{$grid_product}{elseif isset($smarty.cookies.grid_product)}{$smarty.cookies.grid_product}{else}grid_6{/if} ajax_block_product {if $smarty.foreach.products.first}first_item{elseif $smarty.foreach.products.last}last_item{/if} {if $smarty.foreach.products.index % 2}alternate_item{else}item{/if} clearfix omega alpha">
			<div class="center_block_search">				
				<div class="image_search"><a href="{$product.link|escape:'htmlall':'UTF-8'}" class="product_img_link" title="{$product.name|escape:'htmlall':'UTF-8'}">
					<img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')}" alt="{$legend_img|escape:'htmlall':'UTF-8'}" />
				</a>
				{if $product.specific_prices}
        			{assign var='specific_prices' value=$product.specific_prices}
        			{if $specific_prices.reduction_type == 'percentage' && ($specific_prices.from == $specific_prices.to OR ($smarty.now|date_format:'%Y-%m-%d %H:%M:%S' <= $specific_prices.to && $smarty.now|date_format:'%Y-%m-%d %H:%M:%S' >= $specific_prices.from))}
	        			<p class="reduction">{l s='Save '}<span>{$specific_prices.reduction*100|floatval}</span>%</p>
	            	{/if}
					{/if}
				</div>
                                <div class="line_catalog_search">
                                    <img src="img/line_catalogo.jpg"/>
                                </div>
				<div class="name_product_search"><h3 style="margin: 0;"><a href="{$product.link|escape:'htmlall':'UTF-8'}" title="{$product.name|escape:'htmlall':'UTF-8'}">{$product.name|truncate:55:'...'}</a></h3></div>
 				<p class="product_desc">{$product.description_short|strip_tags:'UTF-8'|truncate:200:'...'}</p>
				{if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
				<div class="content_price">
					{if $product.reduction}<span class="pricee-discount">{displayWtPrice p=$product.price_without_reduction}</span>{/if}
					{if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}<span class="pricee{if $product.reduction} old{/if}" style="display: inline;">{if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}</span>{/if}
				</div>
				{/if}
				
                                <div class="add_car_search">
						{if $product.active == 1 && ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE} 						
						{if ($product.allow_oosp || $product.quantity > 0)}
						{if isset($static_token)}
							<a imgpro="{$product.id_image}"linkrewrite="{$product.link_rewrite}"namepro="{$product.name}"pricepro="{$product.price}" class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)}" title="{l s='Comprar'}">{l s='Comprar'}</a>
						{else}
							<a imgpro="{$product.id_image}"linkrewrite="{$product.link_rewrite}"namepro="{$product.name}"pricepro="{$product.price}" class="button ajax_add_to_cart_button exclusive" rel="ajax_id_product_{$product.id_product|intval}" href="{$link->getPageLink('cart',false, NULL, "add&amp;id_product={$product.id_product|intval}", false)}" title="{l s='Comprar'}">{l s='Comprar'}</a>
						{/if}						
					{else}
						<span class="exclusive">{l s='Out of stock'}</span>
					{/if}
                                    {/if}
                                </div>
				{if isset($comparator_max_item) && $comparator_max_item}
					<p class="compare">
						<input type="checkbox" class="comparator" id="comparator_item_{$product.id_product}" value="comparator_item_{$product.id_product}" {if isset($compareProducts) && in_array($product.id_product, $compareProducts)}checked="checked"{/if} /> 
						<label for="comparator_item_{$product.id_product}">{l s='Select to compare'}</label>
					</p>
				{/if}
			</div>
		</li>
	{/foreach}
	</ul>
	<!-- /Products list -->
	 {* <div class="cclearfix" style="float:left;font-size: 7pt;padding: 0 0px 20px 0;text-align: justify;">
                            
Bienvenido a Farmalisto, tu droguería online, encontrarás productos de farmacia, salud, nutrición, cuidado personal y para la familia.  Acá te decimos para qué sirve<label id="tituloCategoryProd"></label> En Farmalisto Puedes comprar con varios medios de pago, tarjeta d&eacute;bito, tarjeta de cr&eacute;dito, OXXO, cuenta de ahorros, efectivo, ten seguridad en cada una de tus transacciones a trav&eacute;s de Symantec Powered by verisign un completo sistema de seguridad en tus compras. Nuestros beneficios: Mejor precio garantizado, No m&aacute;s filas, ya no tendr&aacute;s que hacer filas al salir de tu m&eacute;dico, Discreci&oacute;n en todas tus compras, total confidencialidad en todas tus transacciones. Tu f&oacute;rmula m&eacute;dica completa en un s&oacute;lo lugar. Contamos con profesionales certificados de la farmacolog&iacute;a ofreciendo as&iacute; garant&iacute;a total en tu experiencia de compra, somos una droguer&iacute;a 100% online en donde encontrar&aacute;s todo lo que necesitas. No te auto mediques, somos responsables por la salud de nuestros clientes, exigimos un soporte certificado (f&oacute;rmula m&eacute;dica) por t&uacute; m&eacute;dico en el que autorice la venta del medicamento, la dosis, presentaci&oacute;n del producto, fecha, advertencias, caracter&iacute;sticas, posolog&iacute;a, indicaciones y contraindicaciones es responsabilidad de t&uacute; m&eacute;dico y no nos responsabilizamos por ello.
</div>  *}

<div class="back-ctn-btn-cat">
    <div class="ctn-btn-cat">
        <div class="btn-cat" id="btn-cat">+</div>
    </div>
</div>

 <div class="cclearfix" id="toggle-category" style="float:left;font-size: 7pt;padding: 0 0px 20px 0;text-align: justify;display: none;">
                            
Bienvenido a Farmalisto, tu farmacia online, encontrarás medicamentos con receta y sin receta, productos dermatológicos, artículos para el cuidado personal, salud integral, pañales para adulto mayor, leches y pañales para bebés y salud sexual entre otros. Realizamos envíos urgentes a domicilio las 24 horas para Ciudad de México.<label id="tituloCategoryProd"></label><br> En Farmalisto puedes resolver preguntas acerca de tus medicamentos: ¿Para qué sirve? ¿Cuanto cuesta? Farmalisto es la farmacia Online más grande de México puedes comprar con varios medios de pago, tarjeta débito, tarjeta de crédito, OXXO, SevenEleven, PayPal cuenta de ahorros, efectivo, ten seguridad en cada una de tus transacciones a través de Symantec Powered by verisign un completo sistema de seguridad en tus compras. Nuestros beneficios: Mejor precio garantizado. Tu receta médica completa en un sólo lugar. Contamos con profesionales certificados de la farmacología ofreciendo así garantía total en tu experiencia de compra, somos una farmacia 100% online. No te automediques, no somos responsables por la salud de nuestros clientes, exigimos un soporte certificado (receta médica) por tú doctor en el que autorice la venta del medicamento, la dosis, presentación del producto, fecha, advertencias, características, posología, indicaciones y contraindicaciones es responsabilidad de tú doctor. <br> Los mejores precios entre Farmacias de Guadalajara, Farmacias del Ahorro, Farmacias San Pablo, Farmacias Similares, Farmacia Paris, Farmacias Especializadas y todas las farmacias de México, contáctanos las 24 horas, servicio a domicilio a todo México.
</div> 
	
 <script type="text/javascript">
    $(function(){
        $(".ctn-btn-cat").click(function(){
            if( $( '#toggle-category' ).is( ":hidden" ) ){
                $( "#btn-cat" ).css( "transform", "rotate(45deg)" );
            }
            else {

                $( "#btn-cat" ).css( "transform", "rotate(0deg)" );
            }
            $( "#toggle-category" ).slideToggle("slow");
        });
    });
</script>

{/if}
