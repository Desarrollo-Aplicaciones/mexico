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
*}



<meta http-equiv="content-type" content="text/html; charset=utf-8" />

<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/flick/jquery-ui.css">
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>

<!-- formulario formula medica -->
<script src="{$js_dir}/formula-medica/formula.js"></script>
<link rel="stylesheet" href="{$css_dir}/formula-medica/formula.css">
<!-- formulario formula medica --> 

<style type="text/css">
    
    #processCarrier
{
width:145px;
height:40px;
border:none;
background:url({$img_dir}/formula-medica/btn-continuar.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}

#processCarrier:hover
{
width:145px;
height:40px;
border:none;
background:url({$img_dir}/formula-medica/btn-continuar-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}



#atras1
{
  
width:145px;
height:40px;
 animation: none !important;
border:none;
transition:none;
background:url({$img_dir}formula-medica/btn-anterior.png)no-repeat top center;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}
#atras1:hover
{
 animation: none !important;
transition:none;
width:150px;
height:45px;
border:none;
background:url({$img_dir}formula-medica/btn-anterior-hover.png)no-repeat top center !important;
	
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;
}

/*btn-adjuntar.png */

.ocultarobj
{
 opacity: 0;
 -moz-opacity: 0;
}

.archivoformula
{  
width:99px;
height:29px;
background:url({$img_dir}formula-medica/btn-adjuntar.png)no-repeat top center;
-webkit-background-size: 100% 100%;           /* Safari 3.0 */
-moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
-o-background-size: 100% 100%;           /* Opera 9.5 */
background-size: 100% 100%;
  
}
</style>


{* $link|@var_dump *}

{if !$opc}
	<script type="text/javascript">
	//<![CDATA[
	var orderProcess = 'order';
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='product' js=1}";
	var txtProducts = "{l s='products' js=1}";
	var orderUrl = '{$link->getPageLink("order", true)|addslashes}';

	var msg = "{l s='You must agree to the terms of service before continuing.' js=1}";
	{literal}        
           <!-- javascrip -- en formula.js>
   
	{/literal}
	//]]>
	</script>
{else}
	<script type="text/javascript">
		var txtFree = "{l s='Free!'}";
	</script>
{/if}


{if isset($virtual_cart) && !$virtual_cart && $giftAllowed && $cart->gift == 1}
<script type="text/javascript">
{literal}
// <![CDATA[
	$('document').ready( function(){
		if ($('input#gift').is(':checked'))
			$('p#gift_div').show();
	});
//]]>
{/literal}
</script>
{/if}

 <!-- inicio formulario -->
{if !$opc}
	
	
	<form enctype="multipart/form-data" id="form"  accept="application/pdf, image/*" action="{$link->getPageLink('order', true, NULL, "multi-shipping={$multi_shipping}")|escape:'html'}" method="post" onsubmit="return acceptCGV();">

{/if}
           
<!-- inicio formulario -->

<div style=" height: 45px; width: 95%;">
<div style="float: left; width: 48%; height: auto;">
{if !$opc}
	
        <h1 style="color:#979797; font-size:24px; font-family:verdana;width: 170%;">Fórmula Médica</h1>
{else}
	<h2><span>2</span> {l s='Delivery methods'}</h2>
{/if}
</div>



<div style="float: right; max-width: 310px; width: 48%; height:auto; ">
 <!-- btn navegación -->
 {if !$opc}

  
	{if !$is_guest}
            
            		
			{if $back}
				<a  href="{$link->getPageLink('order', true, NULL, "step=1&back={$back}&multi-shipping={$multi_shipping}")|escape:'html'}" title=" " class=""> </a>
			{else}
                        <a  id="atras1" style="float: left; overflow: visible;   position: relative;  z-index: 1;" href="{$link->getPageLink('order', true, NULL, "step=1&multi-shipping={$multi_shipping}")|escape:'html'}" title="Anterior" > </a>
			{/if}
		{else}
				<a id="atras2" style="float: left; overflow: visible;  position: relative;  z-index: 1;"  href="{$link->getPageLink('order', true, NULL, "multi-shipping={$multi_shipping}")|escape:'html'}" title="Anterior" > </a>
		{/if}
                
                {if isset($virtual_cart) && $virtual_cart || (isset($delivery_option_list) && !empty($delivery_option_list))}
                    <input type="submit" id="processCarrier" name="processCarrier" value=" " style="float: right; overflow: visible;  position: relative;  z-index: 1; " />
		{/if}
	
{/if}
 
 <!-- btn navegación -->   
</div>
</div>

{if !$opc}
	{assign var='current_step' value='shipping'}
	{include file="$tpl_dir./order-steps.tpl"}
	
	{include file="$tpl_dir./errors.tpl"}
{/if}


{if !$opc}
	<div id="carrier_area">
{else}
	<div id="carrier_area" class="opc-main-block">
{/if}





  



<div class="order_carrier_content" style="float:none; position: relative; width: 95%;">    
<h2 style="color:#979797; font-size:13px; font-family:verdana; float: none;">-Seleccione una opción para registrar su fórmula médica </h2>


    
<div id="formula" style=" height: 375px;">
        
<!-- opciones control de formula medica -->
<div id="optios" > 

<!-- opcion 1 digitar formual medica -->    
<div id="editar"  >
<div class="opcion" >

    
    <input type="radio" name="opcion" value="digitar" onclick="showdiv('div1');" id="opcion1" checked/>
          <div id="rmformula"><label class="rmformulabel1">Registrar los datos de su fórmula médica</label></div>
  
</div>

<div  class="imgformula">
    <img src="{$img_dir}formula-medica/farmalisto-registrar-datos.png" />
</div>

<div  class="textoayuda" >
Ingrese la información solicitada solo de los medicamentos que requieren formula medica
</div>
</div>
<!-- fin editar -->



<div id="div1" style="float: left; display: none;">
 
     <div class="contendfrom">
	    <div style=" width: auto; height: auto;">
	        <p   class="textapoyo"  style="text-justify: distribute;">Por favor ingrese la información en los campos solicitados, así podremos brindarle un servicio con mayor calidad y efectividad a la hora de tu entrega, si no comprendes los que dice tu prescripción, no te preocupes, tenemos otros métodos, para ingresar la formula médica.</p>
	    </div>


<fieldset id="formula-medica">

<div id="columna1" >

<p><label for="nombre" class="labelform">Nombre de su Médico*:</label>
  <input name="nombremedico" id="nombremedico" type="text" size="28" /></p>
  
 <div id="tarjeta"> 
 <div id="tarjetalabel"><p><label for="tarjeta" class="labelform">Tarjeta profesional*:</label></p></div>
  <input type="text" id="tarjeta" name="tarjeta"  size="28" onkeypress="return validar_texto(event)"/>
</div>

 <div id="posologia">
 	<div id="posologialabel"><p><label for="dosis" class="labelform">Posologia* (Dosis)</label></p></div>
  <input type="text" id="dosis" name ="dosis"  size="28"/>
</div>
  
  </div>
  
  
 <div id="columna2">
 	
 <div id="columnasalud">
     <div style="height: 26px"></div>
	 	<div id="particula">   
	  			<div id="radioParticular"><input name="medicoeps" id="particular" type="radio" value="Particular"  checked/></div>
	   			<p><label for="particular" class="labelform">Particular</label></p>
	   </div>
 
   <div id="ep">
   		<div id="epsradio">
   		<p>	<input name="medicoeps" id="eps" type="radio" value="eps" /></p>
   		</div>
   		<label for="eps" class="labelform">E.P.S.</label>
	</div>

    
  <div id="selectoreps">
  		<p> <label id="labeps" for="listeps" class="labelform">Seleccione su E.P.S.</label></p>
			<select name="listeps" size="1" id="listeps" >
					<option value="">Seleccione</option>
					<option value="2">Aliansalud</option>
					<option value="3">Ambuq</option>
					<option value="4">Asmet Salud</option>
					<option value="5">Café Salud</option>
					<option value="6">Cajacopi</option>
					<option value="7">Capital Salud</option>
					<option value="8">Caprecom</option>
					<option value="9">Capresoca</option>
					<option value="10">Colmedica</option>
					<option value="11">Colsubsidio</option>
					<option value="12">Comfaboy</option>
					<option value="13">Comfacor</option>
					<option value="14">Comfama</option>
					<option value="15">Comfamiliar</option>
					<option value="16">Comfandi</option>
					<option value="17">Comfenalco</option>
					<option value="18">Comparta</option>
					<option value="19">Compensar</option>
					<option value="20">Convida</option>
					<option value="21">Coomeva</option>
					<option value="22">Coosalud</option>
					<option value="23">Cruz Blanca</option>
					<option value="24">Ecoopsos</option>
					<option value="25">Emssanar</option>
					<option value="26">EPS Sura</option>
					<option value="27">Famisanar</option>
					<option value="28">Golden Group</option>
					<option value="29">Humanavivir</option>
					<option value="30">Mutual Ser </option>
					<option value="31">Nueva EPS</option>
					<option value="32">Pijaos salud</option>
					<option value="33">Salud Colpatria</option>
					<option value="34">Salud Total</option>
					<option value="35">SaludCoop</option>
					<option value="36">Saludvida</option>
					<option value="37">Sanitas</option>
					<option value="38">Solsalud</option>
					<option value="39">SOS(servicio occidental de salud)</option>
			</select>
  		</div>
  </div><!--fin columnasalud-->


				 <div id="fecha">
				 	 	<div id="fechalabel">
				 		<p class="labelform"><label for="eps" class="labelform">Fecha de prescripción*</label><br /></p></div>
				     <input type="text" id="datepicker" name="datepicker" size="28" placeholder="dd/mm/yyyy">
				 </div>
 			</div><!--fin column2-->
		   

  </fieldset>
<!--  -->

 </div>   <!-- fin contendfrom -->
 </div>    <!--fin div1 -->                   


<!-- opcion 2 entrgar con el pedido --> 
<div id="entrega"    >
<div  class="opcion">

    
          <input type="radio" name="opcion" onclick="showdiv('div2');" value="entrega" id="opcion2" />
         <div id="rmformula"><label class="rmformulabel"> Entregar al recibir tu pedido</label></div>
</div>

<div  class="imgformulay">
<img  src="{$img_dir}formula-medica/farmalisto-contra-entrega.png" />
</div>

<div  class="textoayuda4">
Puedes entregar una copia de tu fórmula al representante de nuestro servcio de entrgas.
</div>
</div>
<!-- fin entregar con el pedido -->


<div id="div2" style="display: none;">
                       
                       
<div class="contendfrom">
 <div style=" width: auto; height: auto; text-justify: auto;">
     <p  class="textapoyo">Saca una copia como soporte y entrégala al repartidor en el momento que recibas tu pedido, así podremos brindarte un servicio con mayor calidad y efectividad a la hora de tu entrega.</p>
 </div>

</div>
                       
                    
       </div>

<!-- opcion 3 llamada de farmalisto --> 
<div id="llamada" >
<div  class="opcion">

   
          <input type="radio" name="opcion" value="llamada" onclick="showdiv('div3');" id="opcion3" />
         <div id="rmformula"><label class="rmformulabel"> Recibe una llamada</label> </div>
</div>

<div class="imgformulaz">
<img src="{$img_dir}formula-medica/farmalisto-llamada.png" />
</div>

<div  class="textoayuda2">
Recibe una llamada de servicio al cliente en un numero fijo.
</div>
</div>
<!-- fin llamada -->


<div id="div3" style="display: none;">
     <div class="contendfrom">
    <div style=" width: auto; height: auto; text-justify: auto;">
        <p  class="textapoyo">Registra en el siguiente formulario tu numero telefónico en el cual podemos contactarte, y recibe una llamada de nuestro servicio al cliente, podrás entregar los datos de la formula medica vía telefónica, así podremos brindarte un servicio con mayor calidad y efectividad a la hora de tu entrega.</p>
    </div>


<fieldset id="formula-medica">
<div id="columna1" >

<p><label for="nombre" class="labelform">Tu numero de teléfono fijo o movil*:</label>
    <input id="telefono" name="telefono" type="text" size="28" placeholder="Ciudad-Télefono" />

</p>
</div>
  
  
 
  </fieldset>
<!--  -->
</div>
     
 </div>

<!-- enviar archivo adjunto -->
<div id="online"  >
<div  class="opcion">

    
          <input type="radio" name="opcion" onclick="showdiv('div4');" value="online" id="opcion4" />
        <label id="rmformulabel2">Enviar Online</label>
</div>

<div  class="imgformulax">
<img   src="{$img_dir}/formula-medica/farmalisto-adjuntar-archivo.png" />
</div>

<div  class="textoayuda3">
Toma una fotografiá con tu web cam o celular, escanea y adjunta la imagen de tu formula.
</div>
</div>


<!-- fin adjunto -->    

<div id="div4" style="display: none;">
     <div class="contendfrom">
    <div style=" width: auto; height: auto; text-justify: auto;">
        <p  class="textapoyo">Registra en el siguiente formulario tu numero telefónico en el cual podemos contactarte, y recibe una llamada de nuestro servicio al cliente, podrás entregar los datos de la formula medica vía telefónica, así podremos brindarte un servicio con mayor calidad y efectividad a la hora de tu entrega.</p>
    </div>


<fieldset id="formula-medica">
<div id="columna1" >

<p><label for="nombre" class="labelform">Tu numero de teléfono fijo o movil*:</label>
    <input id="telefono" name="telefono" type="text" size="28" placeholder="Ciudad-Télefono" />

</p>
</div>
  
  
 
  </fieldset>
<!--  -->
</div>
     
 </div>
    
    </div>
<!-- fin opciones -->


<!--- divs formularios -->




    
 <!-- formula medica -->   
 
 <!-- fin formula -->
 
 <!-- Contraentrega -->

 <!--Fin Contraentrega --> 
 


 





<div id="old-code" style="visibility:hidden;">
        {if isset($virtual_cart) && $virtual_cart}
	<input id="input_virtual_carrier" class="hidden" type="hidden" name="id_carrier" value="0" />
{else}
	<h3 class="carrier_title">{l s='Choose your delivery method'}</h3>
	
	<div id="HOOK_BEFORECARRIER">
		{if isset($carriers) && isset($HOOK_BEFORECARRIER)}
			{$HOOK_BEFORECARRIER}
		{/if}
	</div>
	{if isset($isVirtualCart) && $isVirtualCart}
		<p class="warning">{l s='No carrier is needed for this order.'}</p>
	{else}
		{if $recyclablePackAllowed}
			<p class="checkbox">
				<input type="checkbox" name="recyclable" id="recyclable" value="1" {if $recyclable == 1}checked="checked"{/if} autocomplete="off"/>
				<label for="recyclable">{l s='I would like to receive my order in recycled packaging.'}.</label>
			</p>
		{/if}
                
	<div class="delivery_options_address">
	{if isset($delivery_option_list)}
		{foreach $delivery_option_list as $id_address => $option_list}
			<h3>
				{if isset($address_collection[$id_address])}
					{l s='Choose a shipping option for this address:'} {$address_collection[$id_address]->alias}
				{else}
					{l s='Choose a shipping option'}
				{/if}
			</h3>
			<div class="delivery_options">
			{foreach $option_list as $key => $option}
				<div class="delivery_option {if ($option@index % 2)}alternate_{/if}item">
					<input class="delivery_option_radio" type="radio" name="delivery_option[{$id_address}]" onchange="{if $opc}updateCarrierSelectionAndGift();{else}updateExtraCarrier('{$key}', {$id_address});{/if}" id="delivery_option_{$id_address}_{$option@index}" value="{$key}" {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}checked="checked"{/if} />
					<label for="delivery_option_{$id_address}_{$option@index}">
						<table class="resume">
							<tr>
								<td class="delivery_option_logo">
									{foreach $option.carrier_list as $carrier}
										{if $carrier.logo}
											<img src="{$carrier.logo}" alt="{$carrier.instance->name}"/>
										{else if !$option.unique_carrier}
											{$carrier.instance->name}
											{if !$carrier@last} - {/if}
										{/if}
									{/foreach}
								</td>
								<td>
								{if $option.unique_carrier}
									{foreach $option.carrier_list as $carrier}
										<div class="delivery_option_title">{$carrier.instance->name}</div>
									{/foreach}
									{if isset($carrier.instance->delay[$cookie->id_lang])}
										<div class="delivery_option_delay">{$carrier.instance->delay[$cookie->id_lang]}</div>
									{/if}
								{/if}
								{if count($option_list) > 1}
									{if $option.is_best_grade}
										{if $option.is_best_price}
										<div class="delivery_option_best delivery_option_icon">{l s='The best price and speed'}</div>
										{else}
										<div class="delivery_option_fast delivery_option_icon">{l s='The fastest'}</div>
										{/if}
									{else}
										{if $option.is_best_price}
										<div class="delivery_option_best_price delivery_option_icon">{l s='The best price'}</div>
										{/if}
									{/if}
								{/if}
								</td>
								<td>
								<div class="delivery_option_price">
									{if $option.total_price_with_tax && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
										{if $use_taxes == 1}
											{convertPrice price=$option.total_price_with_tax} {l s='(tax incl.)'}
										{else}
											{convertPrice price=$option.total_price_without_tax} {l s='(tax excl.)'}
										{/if}
									{else}
										{l s='Free'}
									{/if}
								</div>
								</td>
							</tr>
						</table>
						<table class="delivery_option_carrier {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}selected{/if} {if $option.unique_carrier}not-displayable{/if}">
							{foreach $option.carrier_list as $carrier}
							<tr>
								{if !$option.unique_carrier}
								<td class="first_item">
								<input type="hidden" value="{$carrier.instance->id}" name="id_carrier" />
									{if $carrier.logo}
										<img src="{$carrier.logo}" alt="{$carrier.instance->name}"/>
									{/if}
								</td>
								<td>
									{$carrier.instance->name}
								</td>
								{/if}
								<td {if $option.unique_carrier}class="first_item" colspan="2"{/if}>
									<input type="hidden" value="{$carrier.instance->id}" name="id_carrier" />
									{if isset($carrier.instance->delay[$cookie->id_lang])}
										{$carrier.instance->delay[$cookie->id_lang]}<br />
										{if count($carrier.product_list) <= 1}
											({l s='product concerned:'}
										{else}
											({l s='products concerned:'}
										{/if}
										{* This foreach is on one line, to avoid tabulation in the title attribute of the acronym *}
										{foreach $carrier.product_list as $product}
										{if $product@index == 4}<acronym title="{/if}{if $product@index >= 4}{$product.name}{if !$product@last}, {else}">...</acronym>){/if}{else}{$product.name}{if !$product@last}, {else}){/if}{/if}{/foreach}
									{/if}
								</td>
							</tr>
						{/foreach}
						</table>
					</label>
				</div>
			{/foreach}
			</div>
			<div class="hook_extracarrier" id="HOOK_EXTRACARRIER_{$id_address}">{if isset($HOOK_EXTRACARRIER_ADDR) &&  isset($HOOK_EXTRACARRIER_ADDR.$id_address)}{$HOOK_EXTRACARRIER_ADDR.$id_address}{/if}</div>
			{foreachelse}
			<p class="warning" id="noCarrierWarning">
				{foreach $cart->getDeliveryAddressesWithoutCarriers(true) as $address}
					{if empty($address->alias)}
						{l s='No carriers available.'}
					{else}
						{l s='No carriers available for the address "%s".' sprintf=$address->alias}
					{/if}
					{if !$address@last}
					<br />
					{/if}
				{foreachelse}
					{l s='No carriers available.'}
				{/foreach}
			</p>
		{/foreach}
	{/if}
	
	</div>
	<div style="display: none;" id="extra_carrier"></div>
	
		{if $giftAllowed}
		<h3 class="gift_title">{l s='Gift'}</h3>
		<p class="checkbox">
			<input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if} autocomplete="off"/>
			<label for="gift">{l s='I would like my order to be gift wrapped.'}</label>
			<br />
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			{if $gift_wrapping_price > 0}
				({l s='Additional cost of'}
				<span class="price" id="gift-price">
					{if $priceDisplay == 1}{convertPrice price=$total_wrapping_tax_exc_cost}{else}{convertPrice price=$total_wrapping_cost}{/if}
				</span>
				{if $use_taxes}{if $priceDisplay == 1} {l s='(tax excl.)'}{else} {l s='(tax incl.)'}{/if}{/if})
			{/if}
		</p>
		<p id="gift_div" class="textarea">
			<label for="gift_message">{l s='If you\'d like, you can add a note to the gift:'}</label>
			<textarea rows="5" cols="35" id="gift_message" name="gift_message">{$cart->gift_message|escape:'htmlall':'UTF-8'}</textarea>
		</p>
		{/if}
	{/if}
{/if}
    </div>


 

<div style="visibility:hidden;">
{if $conditions AND $cms_id}
	<h3 class="condition_title">{l s='Terms of service'} </h3>
	<p class="checkbox">
            <input type="checkbox" checked="" name="cgv" id="cgv" value="1" {if $checkedTOS}checked="checked"{/if} />
		<label for="cgv">{l s='I agree to the Terms of Service and will adhere to them unconditionally.'}</label> <a href="{$link_conditions}" class="iframe">{l s='(Read Terms of Service)'}</a>
	</p>
	<script type="text/javascript">$('a.iframe').fancybox();</script>
{/if}
</div>


</div>




{if !$opc}
<p class="cart_navigation submit" style="width: 99%;">
  
		<input type="hidden" name="step" value="3" />
		<input type="hidden" name="back" value="{$back}" />
		{if !$is_guest}
			{if $back}
				<a  href="{$link->getPageLink('order', true, NULL, "step=1&back={$back}&multi-shipping={$multi_shipping}")|escape:'html'}" title=" " class=""> </a>
			{else}
                        <div id="segundo"> <a  id="atras1" style="float: left; overflow: visible;   position: relative;  z-index: 1;" href="{$link->getPageLink('order', true, NULL, "step=1&multi-shipping={$multi_shipping}")|escape:'html'}" title="Anterior" > </a></div>
			{/if}
		{else}
				<a id="atras2" href="{$link->getPageLink('order', true, NULL, "multi-shipping={$multi_shipping}")|escape:'html'}" title="Anterior" class="button"> </a>
		{/if}
		{if isset($virtual_cart) && $virtual_cart || (isset($delivery_option_list) && !empty($delivery_option_list))}
             <div id="segundoCarrier"><input type="submit" id="processCarrier" name="processCarrier" value=" " class="exclusive" /></div>
		{/if}
	</p>
</form>
{else}
	<h3>{l s='Leave a message'}</h3>
	<div>
		<p>{l s='If you would like to add a comment about your order, please write it in the field below.'}</p>
		<p><textarea cols="120" rows="3" name="message" id="message">{if isset($oldMessage)}{$oldMessage|escape:'htmlall':'UTF-8'}{/if}</textarea></p>
	</div>
</div>
{/if}

</div>
