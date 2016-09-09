


<link rel="stylesheet" type="text/css" href="{$css_dir}order-address.css">
<script src="{$js_dir}order-address.js" type="text/javascript" charset="utf-8"></script>
<script src="{$js_dir}tools.js" type="text/javascript" charset="utf-8"></script>

{if $opc}
	{assign var="back_order_page" value="order-opc.php"}
{else}
	{assign var="back_order_page" value="order.php"}
{/if}


{* Will be deleted for 1.5 version and more *}
{if !isset($formatedAddressFieldsValuesList)}
	{$ignoreList.0 = "id_address"}
	{$ignoreList.1 = "id_country"}
	{$ignoreList.2 = "id_state"}
	{$ignoreList.3 = "id_customer"}
	{$ignoreList.4 = "id_manufacturer"}
	{$ignoreList.5 = "id_supplier"}
	{$ignoreList.6 = "date_add"}
	{$ignoreList.7 = "date_upd"}
	{$ignoreList.8 = "active"}
	{$ignoreList.9 = "deleted"}

	{* PrestaShop 1.4.0.17 compatibility *}
	{if isset($addresses)}
		{foreach from=$addresses key=k item=address}
			{counter start=0 skip=1 assign=address_key_number}
			{$id_address = $address.id_address}
			{foreach from=$address key=address_key item=address_content}
				{if !in_array($address_key, $ignoreList)}
					{$formatedAddressFieldsValuesList.$id_address.ordered_fields.$address_key_number = $address_key}
					{$formatedAddressFieldsValuesList.$id_address.formated_fields_values.$address_key = $address_content}
					{counter}
				{/if}
			{/foreach}
		{/foreach}
	{/if}
{/if}

<script type="text/javascript">
// <![CDATA[
	{if !$opc}
	var orderProcess = 'order';
	var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"}';
	var currencyRate = '{$currencyRate|floatval}';
	var currencyFormat = '{$currencyFormat|intval}';
	var currencyBlank = '{$currencyBlank|intval}';
	var txtProduct = "{l s='product' js=1}";
	var txtProducts = "{l s='products' js=1}";
	{/if}
	
	var addressMultishippingUrl = "{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{'&multi-shipping=1'|urlencode}{if $back}&mod={$back|urlencode}{/if}")}";
	var addressUrl = "{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")}";

	var formatedAddressFieldsValuesList = new Array();

	{foreach from=$formatedAddressFieldsValuesList key=id_address item=type}
		formatedAddressFieldsValuesList[{$id_address}] =
		{ldelim}
			'ordered_fields':[
				{foreach from=$type.ordered_fields key=num_field item=field_name name=inv_loop}
					{if !$smarty.foreach.inv_loop.first},{/if}"{$field_name}"
				{/foreach}
			],
			'formated_fields_values':{ldelim}
					{foreach from=$type.formated_fields_values key=pattern_name item=field_name name=inv_loop}
						{if !$smarty.foreach.inv_loop.first},{/if}"{$pattern_name}":"{$field_name}"
					{/foreach}
				{rdelim}
		{rdelim}
	{/foreach}

	function getAddressesTitles()
	{
		return {
						'invoice': "{l s='Your billing address' js=1}",
						'delivery': "{l s='Your delivery address' js=1}"
			};

	}


	function buildAddressBlock(id_address, address_type, dest_comp)
	{
		//console.log('id_address: '+id_address+'- address_type:'+ address_type+'- dest_comp:'+ dest_comp+'-');
		var adr_titles_vals = getAddressesTitles();
		var li_content = formatedAddressFieldsValuesList[id_address]['formated_fields_values'];
		var ordered_fields_name = ['title'];

		ordered_fields_name = ordered_fields_name.concat(formatedAddressFieldsValuesList[id_address]['ordered_fields']);
		ordered_fields_name = ordered_fields_name.concat(['update']);

		dest_comp.html('');

		li_content['title'] = adr_titles_vals[address_type];
		li_content['update'] = '<a href="{$link->getPageLink('address', true, NULL, "id_address")}'+id_address+'&amp;back={$back_order_page}?step=1{if $back}&mod={$back}{/if}" title="{l s='Update' js=1}">&raquo; {l s='Update' js=1}</a>';

		appendAddressList(dest_comp, li_content, ordered_fields_name);
	}

	function appendAddressList(dest_comp, values, fields_name)
	{
		for (var item in fields_name)
		{
			var name = fields_name[item];
			var value = getFieldValue(name, values);
			if (value != "")
			{
				var new_li = document.createElement('li');
				new_li.className = 'address_'+ name;
				new_li.innerHTML = getFieldValue(name, values);
				dest_comp.parent().append(new_li);
			}
		}
	}

	function getFieldValue(field_name, values)
	{
		var reg=new RegExp("[ ]+", "g");

		var items = field_name.split(reg);
		var vals = new Array();

		for (var field_item in items)
		{
			items[field_item] = items[field_item].replace(",", "");
			vals.push(values[items[field_item]]);
		}
		return vals.join(" ");
	}

//]]>
function hide_date_delivered(id_address){

			$.ajax({
				type: "post",
				url: "{$base_dir}ajaxs/ajax_address.php",
				data: {
					"ajax":true,
					"id_city_by_id_address":true,
					"id_address": id_address
				},
				success: function(response){
					var id_city = $.parseJSON(response);
		if(id_city != 1184 ){
			$("#date_delivered").prop( "disabled", true );
			$('#hour_delivered_h').prop( "disabled", true );
			$('#titulo-2').hide();
			$('#label-dia').hide();
			$('#label-hora').hide();
		}else{
			$("#date_delivered").prop( "disabled", false  );
			$('#hour_delivered_h').prop( "disabled", false  );
			$('#titulo-2').show();
			$('#label-dia').show();
			$('#label-hora').show();
		}

				}
			});	

}

	function toggleAddressForm(){
		$('.agregaNueva').toggleClass("titulo");
        $('#nueva-direccion').slideToggle();
        $('[name="id_address_delivery"]').prop("checked", "");
		//$('.navigation_block').slideToggle();
	}

	function changeAddress(id){
		if(!$("#rb"+id).is(':checked')){
			//console.log('1:'+id);
			$('#rb'+id).attr('checked', 'checked');
			$('#rb'+id).trigger("change");
	        $('.agregaNueva').removeClass("titulo");
	        $('#nueva-direccion').slideUp();
	        $('.navigation_block').slideDown();
       }
       //console.log('2:'+id);
	}

</script>
<form action="{$link->getPageLink($back_order_page, true)}{if $formula}&paso=pagos{else}&paso=formula{/if}" method="post" id="form_dir" name="form_dir">
{* if !$opc}
		<div class="titulo-pasos">{l s='Datos de Entrega'}</div>
		<div class="botones">
			<input type="button" id="processAddress" name="processAddress" value="{l s='Continue'} >>" class="enviar-form" />
				<a id="atras11"href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" >
				<< {l s='Previous'}</a>
		</div>
{/if *}
{assign var='current_step' value='address'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}

<div id="order-address">

{*if $datacustomer['firstname'] == "" || $datacustomer['identification'] == "" || $datacustomer['id_type'] == 0 }
	{include file="$tpl_dir./customer_data_billing.tpl"}
{/if*}
	<!-- ************************** PRIMERA COLUMNA ****************************-->
	<!-- <form action="{$link->getPageLink($back_order_page, true)}" method="post"> FORMULARIO COLUMNA 1-->
	<!-- <form action="{$link->getPageLink('address', true)|escape:'html'}" method="post" id="add_address"> FORMULARIO COLUMNA 2-->
	<div class="contenedor" id="primera_columna">
		<div class="titulo" id="titulo-1">
			¿A dónde llevamos tu pedido?
		</div>
		<div class="address_container">		
		<input type="checkbox" name="same" id="addressesAreEquals" value="1" checked="checked" style="display:none;"/>
		{assign var="numAddress" value="0"}
		{if $direcciones}			
			{foreach from=$direcciones item=nr}

				{if $nr['is_rfc'] neq 1}
				<div class="direccion" onclick="changeAddress({$nr['id_address']});">
						<div class="radio-direccion">
							<input type="radio" id="rb{$nr['id_address']}" name="id_address_delivery" value="{$nr['id_address']}" onchange="enable({$nr['id_address']});updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" {if $nr['id_address'] == $cart->id_address_delivery}checked="checked"{/if}"/>
						</div>
						<div class="nombre-direccion">{$nr['alias']}</div>
						<div class="detalle-direccion">{$nr['address1']|truncate:40:"...":true} <br />
						{if $nr['express'] && $expressEnabled && $express_productos}
						<div class="express" id="texto_{$nr['id_address']}" {if $nr['id_address'] == $cart->id_address_delivery}style="color:#39CB98;font-weight:600" {/if}>
							<input type="checkbox" id="{$nr['id_address']}" name="express" value="{$nr['id_address']}" onchange="envioExpress({$nr['id_address']})" {if $xps && $nr['id_address'] == $cart->id_address_delivery}checked{/if} {if $nr['id_address'] != $cart->id_address_delivery}disabled {/if}>
							Deseo mi orden con servicio express
						</div>
						{/if}
						{* Mostrar envio nocturno y actualizar dirección*}
						{if $entregaNocturnaEnabled eq 'enabled' && $localidadesBarriosEnabled eq 'enabled' && $paramEntregaNocturana['id_city'] == $nr['id_city']}
						<div id="{$nr['id_address']}_box_entrega_nocturna" class="express" id="texto_{$nr['id_address']}_nocturna" {if $nr['id_address'] == $cart->id_address_delivery}style="color:#39CB98" {/if}>
							<select style="height: 10px; font-size: 10px;" id="{$nr['id_address']}localidades" onchange="displayBarrios({$nr['id_address']})" {if $nr['id_address'] != $cart->id_address_delivery}disabled {/if}><option>-Localidad-</option>
							{$list_localidades}
							</select> - <select style="height: 10px; font-size: 10px;" id="{$nr['id_address']}barrios"><option {if $nr['id_address'] != $cart->id_address_delivery}disabled {/if}>-Barrio-</option></select>
							<br> <input type="checkbox" id="{$nr['id_address']}_nocturno_up" name="envioNocturno" value="{$nr['id_address']}" onchange="updateLocaliadBarrio({$nr['id_address']})" {if $entregaNocturna eq 'enabled' && $nr['id_address'] == $cart->id_address_delivery}checked="checked"{/if} {if $nr['id_address'] != $cart->id_address_delivery}disabled {/if}>
								Deseo mi orden esta misma noche.
						</div>
						{/if}
						{*Mostrar envio nocturno*}
						{if $entregaNocturnaEnabled eq 'enabled' && $localidadesBarriosEnabled eq 'disabled' && $paramEntregaNocturana['id_city'] == $nr['id_city'] && !$paramEntregaNocturana['auto_load']}
						<div id="{$nr['id_address']}_box_entrega_nocturna" class="express" id="texto_{$nr['id_address']}_nocturna" {if $nr['id_address'] == $cart->id_address_delivery}style="color:#39CB98" {/if}>
							
							<input type="checkbox" id="{$nr['id_address']}_nocturno" name="envioNocturno" value="{$nr['id_address']}" onchange="envio_nocturno({$nr['id_address']})" {if $entregaNocturna eq 'enabled' && $nr['id_address'] == $cart->id_address_delivery}checked="checked"{/if} {if $nr['id_address'] != $cart->id_address_delivery}disabled {/if}> 
							 Deseo mi orden esta misma noche
							
						</div>
						{/if}
							</div> 
						<div class="ciudad-direccion">{$nr['city']}</div>
						<div class="estado-direccion">{$nr['state']}</div>
				</div>
				{/if}
			{$numAddress = $numAddress + 1}
			{/foreach}
	{* Inicio fecha  y hora de entrega *}		
		<br /><br />
		<div class="titulo" id="titulo-2" style=" text-align: left;">
			Fecha y hora de entrega
		</div>	
		<br /><br />			
		<div class="etiqueta" id="label-dia"><label>Día<span class="purpura">*</span>:<br></label> 
			{if isset($day_delivered) && isset($js_json_delivered)}
			{$day_delivered}
			<br>	
			{$js_json_delivered}		
			{/if}
		</div>


		<div class="etiqueta" id="label-hora"><label>Hora<span class="purpura">*</span>:<br></label> 
			<select class="seleccion" id="hour_delivered" style="width: 150px !important;">
			</select><br>
		</div>
	{* Fin fecha  y hora de entrega *}			
        <br /><br /><br />
        <a href="javascript:void(0);" class="agregaNueva" onclick="toggleAddressForm();">Agregar nueva dirección</a>
		</div>
		{/if}
	</div>
	<!-- ************************** FIN PRIMERA COLUMNA ****************************-->			
	<!-- ************************** SEGUNDA COLUMNA ****************************-->
	<div class="contenedor" id="nueva-direccion">
		{*}
		<div class="campoCorto">
			<div class="etiqueta" id="label-estado"><label>Departamento<span class="purpura">*</span>:<br /></label>
				<select class="seleccion" id="estado" name="estado">
					<option value="" selected="selected" disabled>- Departamento* -</option>
					<option value="bog">Bogotá - Cundinamarca</option>
					<option value="cal">Cali - Valle del Cauca</option>
					<option value="med">Medellín - Antioquia</option>
					<option value="bar">Barranquilla - Atlántico</option>
					<option value="buc">Bucaramanga - Santander</option>
					<option disabled>──────────────</option>
					{foreach from=$estados item=dp}
					<option value="{$dp['id_state']}">{$dp['state']}</option>
					{/foreach}
				</select><br /> 
			</div>
        </div>
        <div class="campoCorto">
			<div class="etiqueta" id="label-postcode" sty><label>Código Postal<span class="purpura">*</span>:<br /></label>
				<input id="id_address" type="hidden" class="hidden"/>
				<input class="entrada larga" type="text" id="postcode" placeholder="Código Postal*" name="postcode" maxlength="5" value=""><br />
			</div>
		</div>
        <div class="campoCorto">
			<div class="etiqueta" id="label-ciudad"><label>Ciudad<span class="purpura">*</span>:<br /></label>
				<select class="seleccion" id="ciudad" name="ciudad">
					<option value="" selected="selected" disabled>- Ciudad* -</option>
				</select><br /> 
			</div>
				 <input type="hidden" class="hidden" name="nombre_ciudad" id="nombre_ciudad" value="" />
		</div>
		<div class="campoLargo">
			<div class="etiqueta" id="label-direccion"><label>Dirección<span class="purpura">*</span>:<br /></label>
			<input class="entrada larga" type="text" value="" placeholder="Dirección*" id="direccion" name="direccion"/><br />
			</div> 
		</div>
		<div class="campoLargo">
			<div class="etiqueta" id="label-complemento"><label>Barrio / Indicaciones<span class="purpura">*</span>:<br /></label>
			<input class="entrada larga" type="text" value="" placeholder="Barrio / Indicaciones*" id="complemento" name="complemento"/><br /> 
			</div>
		</div>
        <div class="campoCorto">
            <div class="etiqueta" id="label-fijo"><label>Teléfono 1<span class="purpura">*</span>:<br /></label>
            <input class="entrada" type="text" value="" placeholder="Número fijo o celular*" id="fijo" name="fijo"/><br /> 
            </div>
        </div>
        <div class="campoCorto">
            <div class="etiqueta" id="label-movil"><label>Teléfono 2:<br /></label>
            <input class="entrada" type="text" value="" placeholder="Teléfono 2, opcional" id="movil" name="movil"/><br /> 
            </div>
        </div>
*}

		<div class="campoCorto">
			<div class="etiqueta" id="label-postcode" ><label>Código Postal<span class="purpura">*</span>:<br /></label>
				<input id="id_address" type="hidden" class="hidden"/>
				<input class="entrada" type="text" id="postcode" name="postcode" maxlength="5" value="" placeholder="Código Postal*"><br />
			</div>
		</div>
		<br>
		<div class="campoCorto">
			<div class="etiqueta" id="label-colonia"><label>Colonia<span class="purpura">*</span>:<br /></label>
			<select class="seleccion" id="id_colonia" name="id_colonia">
				<option value="" selected="selected" disabled>- Colonia* -</option>			 
			</select><br />
			</div>
		</div>
		<br>
		<div class="campoCorto">
			<div class="etiqueta" id="label-estado"><label>Estado<span class="purpura">*</span>:<br /></label>
				<select class="seleccion" id="estado" name="estado">
				<option value="" selected="selected" disabled>- Estado* -</option>
					{foreach from=$estados item=dp}
					<option value="{$dp['id_state']}">{$dp['state']}</option>
					{/foreach}
				</select><br /> 
			</div>
		</div>
		<br>
		<div class="campoCorto">
			<div class="etiqueta" id="label-ciudad"><label>Ciudad<span class="purpura">*</span>:<br /></label>
				<select class="seleccion" id="ciudad" name="ciudad">
					<option value="" selected="selected" disabled>- Ciudad* -</option>
				</select><br /> 
			</div>
				 <input type="hidden" class="hidden" name="nombre_ciudad" id="nombre_ciudad" value="" />
		</div>
		<div class="campoLargo">
			<div class="etiqueta" id="label-direccion"><label>Dirección<span class="purpura">*</span>:<br /></label>
			<input class="entrada" type="text" value="" id="direccion" name="direccion" placeholder="Dirección*"/>
			</div> 
		</div>

		<div class="campoLargo">
			<div class="etiqueta" id="label-complemento"><label>Alguna indicación especial{* <span class="purpura">*</span> *}:<br /></label>
			<input class="entrada larga" type="text" value="" id="complemento" name="complemento" placeholder="Alguna indicación especial"/><br /> 
			</div>
		</div>

		<div class="campoCorto">
			<div class="etiqueta" id="label-fijo"><label>Teléfonos de contacto<span class="purpura">*</span>:<br /></label>
			<input class="entrada" type="text" value="" placeholder="Fijo o celular *" id="fijo" name="fijo" maxlength="10"/><br /> 
			</div>
		</div>

		<div class="campoCorto">
			<div class="etiqueta" id="label-movil"><label>Teléfono 2{*<span class="purpura">*</span>*}:<br /></label>
			<input class="entrada" type="text" value="" placeholder="Opcional, Fijo o celular" id="movil" name="movil" maxlength="10"/><br /> 
			</div>
		</div>

		{if $direcciones}
		<div style="display:inline-block;">
			{*<input type="button" value="Registrar dirección" id="new-address"/>*}
            <a href="javascript:void(0);" onclick="toggleAddressForm();" class="cancelar">Cancelar</a>
		</div>
		{/if}
	</div>

	{if $datacustomer['firstname'] == "" || $datacustomer['identification'] == "" || $datacustomer['id_type'] == 0 }
		{include file="$tpl_dir./customer_data_billing.tpl"}
	{/if}

	
	<!-- ************************** FIN SEGUNDA COLUMNA ****************************-->			
	
	<div class="navigation_block">
			<!-- si la fomula medica existe salto al paso 3 -->			
 {if $formula}
	 <input type="hidden" class="hidden" name="step" value="3" />
	{else}
	<input type="hidden" class="hidden" name="step" value="2" />
{/if}
		<input type="hidden" name="back" value="{$back}" />
		{*<input type="button" id="processAddress2" name="processAddress2" value="{if !($direcciones)}Guardar y {/if}{l s='Continue'} >>" class="enviar-form" />*}
		<input type="button" id="processAddress2" name="processAddress2" value="{l s='Continue'} >>" class="enviar-form" />
        <a id="atras12" href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" >
        << {l s='Previous'}</a>
	</div>
	</form>
</div>

<div id="standard_lightbox">
    <div class="fog"></div>
    <div id="lightbox_content"></div>
    <div class="recent"></div>
</div>
<script>
    function lightbox_hide(){
        $('#standard_lightbox').fadeOut('slow');
        $('#page').removeClass("blurred");
        $('#'+($('#lightbox_content div').attr("id"))).appendTo( '#standard_lightbox .recent' );
        $('#lightbox_content').empty();
        }
    function standard_lightbox(id){
        $('#lightbox_content').empty();
        $('#'+id).appendTo( "#lightbox_content" );
        $('#lightbox_content #'+id).show();
        $('#standard_lightbox').fadeIn('slow');
        $('#page').addClass("blurred");
    }
    $('#standard_lightbox .fog').click(function(){
        lightbox_hide();
    });
</script>

{if $numAddress eq 0}
$('#nueva-direccion').attr("style", "display:block !important;");
{/if}
<link href="{$base_dir}themes/gomarket/css/Lightbox_ConfirmExpress.css" rel="stylesheet" type="text/css">

<div class="contenedor container_24" id="pop-confirmExpress">
    <div class="close_express" onclick="lightbox_hide();"></div>
    <div class="block_title_express">
        Confirmación
    </div>

    <div class="block_information_express">
        <label>El pedido llegará en máximo 90 minutos, el costo del servicio express es de <span id="xpsValue"></span><br> adicionales. ¿Deseas activar este servicio?</label>
    </div>
    
    <div class="block_buttons_express">
    	<div id="xpscancel">No, Cancelar</div>
		<div id="xpsaccept">Si, Aceptar</div>
    </div>
</div>

<script>

	$('#estado').change(function(){
		var id_estado = $(this).val();
		var ciudad = "";
		switch (id_estado){
		case "bog":
			 $(this).val("326");
			 ciudad = "1184";
			break;
		case "cal":
			 $(this).val("342");
			 ciudad = "1976";
			break;
		case "med":
			 $(this).val("314");
			 ciudad = "1037";
			break;
		case "bar":
			 $(this).val("316");
			 ciudad = "1162";
			break;
		case "buc":
			 $(this).val("339");
			 ciudad = "1835";
			break;
		}
		id_estado = $(this).val();
		if (id_estado==""){
			$('#ciudad').html('<option value="" selected="selected" disabled>- Ciudad -</option>');
		}else{
			$.ajax({
				type: "post",
				url: "{$base_dir}ajax_formulario_cities.php",
				data: {
					"id_state":id_estado
				},
				success: function(response){
					var json = $.parseJSON(response);
					$('#ciudad').html('<option value="" selected="selected" disabled>- Ciudad -</option>'+json.results);
					$('#ciudad').val(ciudad);
					ciudad_s();
				}
			});
		}
	});
	
/*
	$('#ciudad').change(function(){
		ciudad_s();
		});
	
	function ciudad_s()
	{
		alert($("#ciudad :selected").text());	
		$("#nombre_ciudad").val($("#ciudad :selected").text()); 
		if(($('#ciudad').val()) != "")$('#direccion').focus();
	}

*/
$('#ciudad').change(function(){
	ciudad_s();
	updateColoni();
	});

function ciudad_s(){
	$("#nombre_ciudad").val($("#ciudad :selected").text()); 
	if(($('#ciudad').val()) != "")$('#direccion').focus();
}

function updateColoni()
{
	var city	= $('#ciudad').find('option:selected').val();
	var ruta_abs	= getAbsolutePath();
	//alert("Anadir Content Ciudades con provincia ID: " + $('#city').find('option:selected').val() + ' URL: ' + ruta_abs);
	
	$.ajax({
		type: "POST",
		url: "ajax_formulario_colonia_no_carry.php",
		dataType: 'json',
		data: 'city='+city,
		beforeSend: function(objeto){
			$('#errors_login').slideUp(200);
			//$('#loading_forms').fadeIn(500);
		},
		success: function(response) {
			//response.data[0].id
			$('select#id_colonia').html(response.results).fadeOut(700).fadeIn(700);
		},
		complete: function(objeto, exito){
			//$('#loading_forms').fadeOut(1000);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		
		}
	});
}

	
	$('#new-address').click(function(){
		$('.validacion').remove();
		var id_country={$pais};
		var id_state=$('#estado').val();
		var id_customer={$cliente};
		var alias="Direccion {($direcciones|@count)+1}";
		var address1=$('#direccion').val();
		var address2=$('#complemento').val();
		var city=$('#nombre_ciudad').val();
		var city_id=$('#ciudad').val();
		var phone=$('#fijo').val();
		var phone_mobile=$('#movil').val();
		var active = 1;
		$.ajax({
			type:"post",
			url:"{$base_dir}ajax_address_order.php",
			data:{
				"id_country":id_country,
				"id_state":id_state,
				"id_customer":id_customer,
				"alias":alias,
				"address1":address1,
				"address2":address2,
				"city":city,
				"city_id":city_id,
				"phone":phone,
				"phone_mobile":phone_mobile,
				"active":active
			},
			beforeSend: function(ev) {
					var result = Validate();
					if (result){
						$('#order').css('cursor','wait');
						$("#nueva-direccion").empty();
						$("#nueva-direccion").html('<img style="margin: auto;" src="{$img_ps_dir}ad/waiting.gif" />');
					}else{
						ev.abort();
					}
			},
			success: function(response){

				formatedAddressFieldsValuesList[response] =
				{
					'ordered_fields':[
						"dni"
						,"firstname lastname"
						,"address1"
						,"address2"
						,"Country:name"
						,"State:name"
						,"city"
						,"phone"
					],
					'formated_fields_values':{
						"dni":""
						,"firstname":""
						,"lastname":""
						,"address1":address1
						,"address2":address2
						,"Country:name":id_country
						,"State:name":id_state
						,"city":city
						,"phone":phone
					}
				}

				$('#form_dir').append('<input type="radio" id="rb'+response+'" name="id_address_delivery" value="'+response+'" onchange="enable('+response+');updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" checked="checked"/>');
				changeAddress(response);
				//$('#processAddress2').submit();
				$('#form_dir').submit();
				$('#order').css('cursor','default');
			}
		})
	})
	
{literal}

	function asignarPostcode(){
		var valor = $('#postcode').val();
		if ( valor.length == 5) {
			$.ajax({
				type: "GET",
				url: "ajaxs/postcode.php?postcode="+valor,
				success: function(isApplicable){
					if ( isApplicable != 0) {
						vals=isApplicable.split(";");
						//alert(vals[0] + '-' + vals[1] + '-' + vals[2]);

						$('#estado').val(vals[2]);

						/* $.when( updateCity() ).done(function(a1){
							$('#city').val(vals[1]);
						}); */

						var id_state	= vals[2];
						var ruta_abs	= getAbsolutePath();

						$.ajax({
							type: "POST",
							url: "ajax_formulario_cities.php",
							dataType: 'json',
							data: 'id_state='+id_state,
							beforeSend: function(objeto){
								$('#errors_login').slideUp(200);
								//$('#loading_forms').fadeIn(500);
							},
							success: function(response) {
								//response.data[0].id
								$('#ciudad').html(response.results);

									var city = vals[1];
									$('#ciudad').val(vals[1]);
									$('#id_city').val(vals[1]);

									$.ajax({
										type: "POST",
										url: "ajax_formulario_colonia_no_carry.php",
										dataType: 'json',
										data: 'city='+city,
										beforeSend: function(objeto){
											$('#errors_login').slideUp(200);
											//$('#loading_forms').fadeIn(500);
										},
										success: function(response) {
											//response.data[0].id
											$('#id_colonia').html(response.results);
											$('#ciudad').val(vals[1]);
											$('#id_colonia').val(vals[0]);												
										},
										complete: function(objeto, sexito){
											//$('#loading_forms').fadeOut(1000);
										},
										error: function(jqXHR, textStatus, errorThrown) {
										
										}
									});


							},
							complete: function(objeto, exito){
								//$('#loading_forms').fadeOut(1000);
							},
							error: function(jqXHR, textStatus, errorThrown) {
							
							}
						});

						$('#ciudad').val(vals[1]);
						$('#id_colonia').val(vals[0]);
						
					}
					else{
						$('#postcode').val("");
					}
				}
			});
		}
	}
	

	function Validate() {
		var id_state=$('#estado').val();
		//var alias=$('#alias').val();
		var address1=$('#direccion').val();
		var address2=$('#complemento').val();
		var city=$('#ciudad').val();
		var phone=$('#fijo').val();
		//var phone_mobile=$('#movil').val();
				
		if(id_state==""){
			$('#obliga-estado').remove();
			$('#label-estado').parent().append('<span class="validacion" id="obliga-estado">Campo Requerido</span>');
			$('#estado').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}else{
			$('#obliga-estado').remove();
			$('#estado').removeAttr("style");
		}
		
		if(city==""){
			$('#obliga-ciudad').remove();
			$('#label-ciudad').parent().append('<span class="validacion" id="obliga-ciudad">Campo Requerido</span>');
			$('#ciudad').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}else{
			$('#obliga-ciudad').remove();
			$('#ciudad').removeAttr("style");		 
		}
		
		if(phone==""){
			$('#obliga-fijo').remove();
			$('#label-fijo').parent().append('<span class="validacion" id="obliga-fijo">Campo Requerido</span>');
			$('#fijo').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}else{
			if (phone.match(/^[2-8]{1}\d{6}$/) || phone.match(/^[3]{1}([0-2]|[5]){1}\d{1}[2-9]{1}\d{6}$/)){
				$('#obliga-fijo').remove();
				$('#fijo').removeAttr("style"); 
			}else{
				$('#obliga-fijo').remove();
				$('#label-fijo').parent().append('<span class="validacion" id="obliga-fijo">Campo requerido</span>');
				$('#fijo').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			}
		}
		
		/*if(phone_mobile==""){
			$('#obliga-movil').remove();
			$('#label-movil').parent().append('<span class="validacion" id="obliga-movil">Campo Requerido</span>');
			$('#movil').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}else{
			if (phone_mobile.match(/^[3]{1}([0-2]|[5]){1}\d{1}[2-9]{1}\d{6}$/)){
				$('#obliga-movil').remove();
				$('#movil').removeAttr("style"); 
			}else{
				$('#obliga-movil').remove();
				$('#label-movil').parent().append('<span class="validacion" id="obliga-movil">Campo requerido</span>');
				$('#movil').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			}
		}*/
		
		if(address1==""){
			$('#obliga-direccion').remove();
			$('#label-direccion').parent().append('<span class="validacion" id="obliga-direccion">Campo Requerido</span>');
			$('#direccion').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}else{
			$('#obliga-direccion').remove();
			$('#direccion').removeAttr("style"); 
		}
		
		
		if(address2==""){
			$('#obliga-complemento').remove();
			$('#label-complemento').parent().append('<span class="validacion" id="obliga-complemento">Campo Requerido</span>');
			$('#complemento').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}else{
			$('#obliga-complemento').remove();
			$('#complemento').removeAttr("style"); 
		}
		
		
		/*if(alias==""){
			$('#label-alias').parent().append('<span class="validacion" id="obliga-alias">Campo Requerido</span>');
			$('#alias').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}else{
			$('#obliga-alias').remove();
			$('#alias').removeAttr("style"); 
		}*/

		var error=$('.validacion').length;
		
		if(error==0){
			return true;
		}else{
			return false;
		}
	}

	function validarSelect(nombre){
		valor = $("#"+nombre).val();
		if (valor){
			error = "";
			$('#e_'+nombre).remove();
			$('#error'+nombre).html(error);
			$('#'+nombre).attr("style", "border-color:#3A9B37");
			return true;
		}
		error = "Campo requerido.";
		$('#e_'+nombre).remove();
		$('#'+nombre).parent().parent().append("<label class='errorrequired' id='e_"+nombre+"'>"+error+"</label>");
		$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		$('#'+nombre).focus();
		return false;
	}

	function ValidaRfc(strCorrecta) {

		_rfc_pattern_pm = "^(([A-ZÑ&]{3})([0-9]{2})([0][13578]|[1][02])(([0][1-9]|[12][\\d])|[3][01])([A-Z0-9]{3}))|" +
							"(([A-ZÑ&]{3})([0-9]{2})([0][13456789]|[1][012])(([0][1-9]|[12][\\d])|[3][0])([A-Z0-9]{3}))|" +
							"(([A-ZÑ&]{3})([02468][048]|[13579][26])[0][2]([0][1-9]|[12][\\d])([A-Z0-9]{3}))|" +
							"(([A-ZÑ&]{3})([0-9]{2})[0][2]([0][1-9]|[1][0-9]|[2][0-8])([A-Z0-9]{3}))$";

		_rfc_pattern_pf = "^(([A-ZÑ&]{4})([0-9]{2})([0][13578]|[1][02])(([0][1-9]|[12][\\d])|[3][01])([A-Z0-9]{3}))|" +
							"(([A-ZÑ&]{4})([0-9]{2})([0][13456789]|[1][012])(([0][1-9]|[12][\\d])|[3][0])([A-Z0-9]{3}))|" +
							"(([A-ZÑ&]{4})([02468][048]|[13579][26])[0][2]([0][1-9]|[12][\\d])([A-Z0-9]{3}))|" +
							"(([A-ZÑ&]{4})([0-9]{2})[0][2]([0][1-9]|[1][0-9]|[2][0-8])([A-Z0-9]{3}))$";


		var _rfc_pattern_pm = new RegExp(_rfc_pattern_pm);
		var _rfc_pattern_pf = new RegExp(_rfc_pattern_pf);

		$('#e_rfc').remove();
		var error = "Campo requerido";

		if ( (strCorrecta.match(_rfc_pattern_pm) || strCorrecta.match(_rfc_pattern_pf)) && strCorrecta.length <= 13 ) {
			$('#rfc').attr("style", "border-color:#3A9B37");
			return true;
		} else {
			$('#rfc').parent().parent().append("<label class='errorrequired' id='e_rfc'>"+error+"</label>");
			$('#rfc').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			$('#rfc').focus();
			return false;
		}
	}

	function validarTelefono(nombre){
		valor = $("#"+nombre).val();

		if(valor) {

			if (isNaN(valor) || valor.length != 10 || !valor.match(/^[1-9]{1}\d{9}$/)) {
				error = "Campo requerido.";
			} else { 
				error="";
			}
		} else {
			error = "Campo requerido"
		}
		$('#e_'+nombre).remove();
		$('#'+nombre).parent().parent().append("<label class='errorrequired' id='e_"+nombre+"'>"+error+"</label>");

		if (error) {
			$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			$('#'+nombre).focus();
			return false;
		} else {
			$('#'+nombre).attr("style", "border-color:#3A9B37");
			return true;
		}
	}

	function validarDireccion(nombre){
		valor = $("#"+nombre).val();
		if (valor.length < 10) {
			error = "Campo requerido.";
		} else {
			error = "";
		}

		$('#e_'+nombre).remove();
		$('#'+nombre).parent().parent().append("<label class='errorrequired' id='e_"+nombre+"'>"+error+"</label>");
 		
 		if ( error != "" ) {
			$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			$('#'+nombre).focus();
			return false;
		} else {
			$('#'+nombre).attr("style", "border-color:#3A9B37");
			return true;
		}
	}

	function validarVacio(nombre){
		valor = $("#"+nombre).val();
		if (valor.length < 4) {
			error = "Campo requerido.";
		} else {
			error = "";
		}

		$('#e_'+nombre).remove();
		$('#'+nombre).parent().parent().append("<label class='errorrequired' id='e_"+nombre+"'>"+error+"</label>");

		if (error) {
			$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			$('#'+nombre).focus();
			return false;
		} else {
			$('#'+nombre).attr("style", "border-color:#3A9B37");
			return true;
		}
	}

	function validarPostCode(nombre){
		valor = $("#"+nombre).val();
		if (isNaN(valor) || valor.length < 5) {
			error = "Campo requerido.";
		} else {
			error = "";
		}

		$('#e_'+nombre).remove();
		$('#'+nombre).parent().parent().append("<label class='errorrequired' id='e_"+nombre+"'>"+error+"</label>");

		if (error) {
			$('#'+nombre).attr("style", "border-color:#A5689C;background-color:#FFFAFA");
			$('#'+nombre).focus();
			return false;
		} else {
			$('#'+nombre).attr("style", "border-color:#3A9B37");
			return true;
		}
	}

	$('#postcode').focusout(function(){
		validarPostCode('postcode');
		asignarPostcode();
	});

	$('#id_colonia').change(function(){
		validarSelect('id_colonia');
	});

	$('#estado').change(function(){
		validarSelect('estado');
	});

	$('#rfc_postcode').change(function(){
		validarSelect('ciudad');
	});

	$('#direccion').focusout(function(){
		validarDireccion('direccion');
	});

	/*$('#complemento').focusout(function(){
		validarVacio('complemento');
	});*/

	$('#fijo').focusout(function(){
		validarTelefono('fijo');
	});

	$('#movil').focusout(function(){
		if ( $('#movil').val() != "" ) {
			validarTelefono('movil');
		}
	});

{/literal}

	$('.enviar-form').click(function(){

		var active = 1;
		var id_country= {$pais};
		var id_customer = {$cliente};
		var num_Address = {$numAddress};

		// datos direccion
		var postcode = $('#postcode').val();
		var id_colonia = $('#id_colonia').val();
		var estado = $('#estado').val();
		var ciudad = $('#ciudad').val();
		var direccion = $('#direccion').val();
		var complemento = $('#complemento').val();
		var fijo = $('#fijo').val();
		var movil = $('#movil').val();

		// datos facturacion
		var rfc = $('#rfc').val();
		var rfc_name = $('#rfc_name').val();
		var rfc_address = $('#rfc_address').val();
		var rfc_postcode = $('#rfc_postcode').val();
		var rfc_phone = $('#rfc_phone').val();
		var rfc_ciudad = $('#rfc_ciudad').val();
		var rfc_estado = $('#rfc_estado').val();
		var rfc_id_colonia = $('#rfc_id_colonia').val();
		var rfc_id_city = $('#rfc_id_city').val();
		var rfc_id_address = $('#rfc_id_address').val();

		var data_rfc = false;
		if ( $('#rfc_register').attr('checked') ) {
			data_rfc = true;
		}

		var existdir = $('[name="id_address_delivery"]').val();
		if ( typeof($('[name="id_address_delivery"]').val()) === "undefined" ) {
			existdir = "";
		}

        if ( $('[name="id_address_delivery"]').is(':checked') && !data_rfc  && $('#postcode').val() == '') {
        	$('#order').css('cursor','wait');
            $('#form_dir').submit();
        } else {

			$.ajax({
				type : "post",
				url : "data_billing.php",
				data : {
					"action" : 'insertDataBillingCustomer',
					"existdir" : existdir,
					"data_rfc" : data_rfc,
					"active" : active,
					"id_country" : id_country,
					"id_customer" : id_customer,
					"postcode" : postcode,
					"id_colonia" : id_colonia,
					"estado" : estado,
					"ciudad" : ciudad,
					"direccion" : direccion,
					"complemento" : complemento,
					"fijo" : fijo,
					"movil" : movil,
					"rfc" : rfc,
					"rfc_name" : rfc_name,
					"rfc_address" : rfc_address,
					"rfc_postcode" : rfc_postcode,
					"rfc_phone" : rfc_phone,
					"rfc_ciudad" : rfc_ciudad,
					"rfc_estado" : rfc_estado,
					"rfc_id_colonia" : rfc_id_colonia,
					"rfc_id_city" : rfc_id_city,
					"rfc_id_address" : rfc_id_address,
					"num_Address" : num_Address
				},
				beforeSend: function(beforeresponse) {
					var resultdatabilling = validatedatabilling();
					$('#order').css('cursor','wait');
					if ( !resultdatabilling ){
						$('#order').css('cursor','default');
						beforeresponse.abort();
					}
					
				},
				success: function(response){
					if ( response != '' ) {

						var add_new = response.split('|')[1];
						var id_add_new0 = add_new.split(':')[0];
						var id_add_new = add_new.split(':')[1];

						if ( id_add_new0 == 'add' ) {
							$('#processAddress2').attr("disabled", true);

						formatedAddressFieldsValuesList[id_add_new] =
						{
							'ordered_fields':[
								"dni"
								,"firstname lastname"
								,"address1"
								,"address2"
								,"Country:name"
								,"State:name"
								,"city"
								,"phone"
							],
							'formated_fields_values':{
								"dni":""
								,"firstname":""
								,"lastname":""
								,"address1":direccion
								,"address2":complemento
								,"Country:name":id_country
								,"State:name":estado
								,"city":ciudad
								,"phone":fijo
							}
						}

							$('#form_dir').append('<div name="new_add_crea" id="new_add_crea" style="display:none"><input type="radio" id="rb'+id_add_new+'" name="id_address_delivery" value="'+id_add_new+'" onchange="enable('+id_add_new+');updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" checked="checked"/></div>');
							
							//$('#order').attr("cursor", "wait");
							  
							
							changeAddress(id_add_new);
							enable(id_add_new);updateAddressesDisplay();
							$('#form_dir').submit();
							$('#order').css('cursor','default');     
							//$('#processAddress2').attr("disabled", false);
						} else if ( $('[name="id_address_delivery"]').is(':checked') ) {
							
							//$('#order').attr("cursor", "wait");

							$('#processAddress2').attr("disabled", true);
            				$('#form_dir').submit();
            				//$('#processAddress2').attr("disabled", false);
							$('#order').css('cursor','default');
						}
					}
				}
			});
		}
	});

	function validatedatabilling() {
		var errorDataBilling = "";

		if ( $('#nueva-direccion').is(":visible") ) {
			if ( !validarPostCode('postcode') ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarSelect('id_colonia') ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarSelect('estado') ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarSelect('ciudad') ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarDireccion('direccion') ) {
				errorDataBilling += "errortrue";
			}

			/*if ( !validarVacio('complemento') ) {
				errorDataBilling += "errortrue";
			}*/

			if ( !validarTelefono('fijo') ) {
				errorDataBilling += "errortrue";
			}

			if ( $('#movil').val() != "" ) {
				if ( !validarTelefono('movil') ) {
					errorDataBilling += "errortrue";
				}
			}
		}


		if ( $('#rfc_register').attr('checked') ) {
			if ( !ValidaRfc( $('#rfc').val() ) ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarVacio('rfc_name') ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarDireccion('rfc_address') ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarPostCode('rfc_postcode') ) {
				errorDataBilling += "errortrue";
			}

			if ( !validarTelefono('rfc_phone') ) {
				errorDataBilling += "errortrue";
			}
		}

		if ( errorDataBilling != "" ) {
			return false;
		} else {
			return true;
		}
	}

    function getDocument(){
        var fieldname = 'Nombre';
        if($('#txt_type_document_customer').val() == 4){
            $('#label-lastname_customer').parent().slideUp();
            fieldname = 'Razón social';
            $('#txt_firstname_customer').val('');
        }else{
            $('#label-lastname_customer').parent().slideDown();

        }
        $('#txt_firstname_customer').attr('placeholder', fieldname+'*');
        fieldname += '<span class="purpura">*</span>:';
        $('#label-firstname_customer label').html(fieldname);
    }
		$(window).load(function() {
			       /* hide_date_delivered({$cart->id_address_delivery}); */
			{if !($direcciones)}
			$('#nueva-direccion').slideDown();
			{/if}
            $('#txt_type_document_customer').change(function(){
                getDocument();
            });
			});
</script>
