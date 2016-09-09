


<link rel="stylesheet" type="text/css" href="{$css_dir}order-address.css">

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
		//alert(id_address);
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
				dest_comp.append(new_li);
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

<div style="diplay:block; width: 98%; height: auto;">
    <!-- ************************** PRIMERA COLUMNA ****************************-->
    <!-- <form action="{$link->getPageLink($back_order_page, true)}" method="post"> FORMULARIO COLUMNA 1-->
    <!-- <form action="{$link->getPageLink('address', true)|escape:'html'}" method="post" id="add_address"> FORMULARIO COLUMNA 2-->
    <div class="contenedor" id="primera_columna">
        <div class="titulo" id="titulo-1">
            Tus direcciones de entrega:
        </div>
        <br>       
        <input type="checkbox" name="same" id="addressesAreEquals" value="1" checked="checked" style="display:none;"/>
        {if $direcciones}
            {foreach from=$direcciones item=nr}
            {if $nr@iteration + 1 is even}<div>{/if}
            <div class="direccion">
                    <div class="radio-direccion">
                        <input type="radio" id="rb{$nr['id_address']}" name="id_address_delivery" value="{$nr['id_address']}" onchange="enable({$nr['id_address']});updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" {if $nr['id_address'] == $cart->id_address_delivery}checked="checked"{/if}"/>
                    </div>
                    <div class="nombre-direccion">{$nr['alias']}</div>
                    <div class="detalle-direccion">{$nr['address1']} <br />
                    {if $nr['express'] && $expressEnabled && $express_productos}
                    <div class="express" id="texto_{$nr['id_address']}" {if $nr['id_address'] == $cart->id_address_delivery}style="color:#39CB98;font-weight:600" {/if}>
	                    <input type="checkbox" id="{$nr['id_address']}" name="express" value="{$nr['id_address']}" onchange="envioExpress({$nr['id_address']})" {if $xps && $nr['id_address'] == $cart->id_address_delivery}checked{/if} {if $nr['id_address'] != $cart->id_address_delivery}disabled {/if}>
	                    Deseo mi orden con servicio express
                    </div>
                    {/if}
	      </div>
                    <div class="ciudad-direccion">{$nr['city']}</div>
					<div class="estado-direccion">{$nr['state']}</div>

            </div>
            <img src="{$img_dir}hor_sep.jpg" width="100%" height="1px"/>
            {if $nr@iteration + 1 is odd}</div>{/if}
            {/foreach}
        {else}
        <div class="noAddress">Es necesario añadir datos de entrega del pedido, para hacerlo pulse Agregar nueva dirección a continuación.</div>    
        {/if}
            {if $total is odd}</div>{/if}
            <div class="agregaNueva" onclick="muestra();"><span>+ </span> &nbsp; &nbsp; Agregar nueva dirección</div>
    </div>
    <!-- ************************** FIN PRIMERA COLUMNA ****************************-->            
    <!-- ************************** SEGUNDA COLUMNA ****************************-->
    <div id="sombreado"></div>
    <div class="contenedor" id="nueva-direccion">
        <div>
        <div class="campoCorto">
            <p class="etiqueta" id="label-postcode">Código Postal<span class="purpura">*</span>:<br />
                <input class="entrada" type="text" id="postcode" name="postcode" maxlength="5" value=""><br />
            </p>
        </div>
        <div class="campoCorto">
            <p class="etiqueta" id="label-estado">Estado<span class="purpura">*</span>:<br />
	            <select class="seleccion" id="estado" name="estado">
                <option value="" selected="selected" disabled>- Estado -</option>
	                {foreach from=$estados item=dp}
	                <option value="{$dp['id_state']}">{$dp['state']}</option>
	                {/foreach}
	            </select><br /> 
            </p>
        </div>
        <div class="campoCorto">
            <p class="etiqueta" id="label-ciudad">Ciudad<span class="purpura">*</span>:<br />
	            <select class="seleccion" id="ciudad" name="ciudad">
	                <option value="" selected="selected" disabled>- Ciudad -</option>
	            </select><br /> 
            </p>
                 <input type="hidden" class="hidden" name="nombre_ciudad" id="nombre_ciudad" value="" />
        </div>
        <div class="campoCorto">
            <p class="etiqueta" id="label-colonia">Colonia<span class="purpura">*</span>:<br />
            <select class="seleccion" id="id_colonia" name="id_colonia">
                <option value="" selected="selected" disabled>- Colonia -</option>             
	        </select><br />
			</p>
        </div>
        <div class="campoCorto">
            <p class="etiqueta" id="label-fijo">Teléfono Fijo<span class="purpura">*</span>:<br />
            <input class="entrada" type="text" value="" placeholder="Número fijo o celular" id="fijo" name="fijo"/><br /> 
        	</p>
        </div>
        <div class="campoCorto">
            <p class="etiqueta" id="label-movil">Teléfono Móvil<span class="purpura">*</span>:<br />
            <input class="entrada" type="text" value="" placeholder="Número de celular" id="movil" name="movil"/><br /> 
            </p>
        </div>
            <div class="campoCorto">
                <p class="etiqueta" id="label-direccion">Dirección<span class="purpura">*</span>:<br />
                <input class="entrada" type="text" value="" placeholder="Calle, # exterior, # interior" id="direccion" name="direccion"/>
                </p> 
            </div>
        <div class="campoCorto">
                <p class="etiqueta" id="label-alias">Nombre de dirección<span class="purpura">*</span>:<br />
                <input class="entrada" type="text" value="" placeholder="Ej: Mi casa, Mi oficina, Mi mamá" id="alias" name="alias"/><br /> 
                </p>
            </div>
            <div class="campoLargo">
                <p class="etiqueta" id="label-complemento">Barrio / Indicaciones<span class="purpura">*</span>:<br />
                <input class="entrada larga" type="text" value="" placeholder="Cómo llegar" id="complemento" name="complemento"/><br /> 
                </p>
            </div>
        </div>
        <span class="obliga">(<span class="purpura">*</span>) Campos Obligatorios</span>
        <div style="display:inline-block; margin-top:20px">
        	<a href="#" onclick="oculta();" class="cancelar">Cancelar</a>
            <input type="button" value="Registrar dirección" id="new-address"/>
			
        </div>
    </div>
    <!-- ************************** FIN SEGUNDA COLUMNA ****************************-->              
   
   <p style="width: 100%; margin-top: 15px;">
           <!-- si la fomula medica existe salto al paso 3 -->              
 {if $formula}
     <input type="hidden" class="hidden" name="step" value="3" />
   {else}
    <input type="hidden" class="hidden" name="step" value="2" />
{/if}
            <input type="hidden" name="back" value="{$back}" />
            <a id="atras12" href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" >
            << {l s='Previous'}</a>
            <input type="button" id="processAddress2" name="processAddress2" value="{l s='Continue'} >>" class="enviar-form" />
        </p>
    </form>
</div>



<script>
	function muestra(){
		$('#nueva-direccion').fadeIn('slow');
        $('#sombreado').fadeIn('slow');
        $('.agregaNueva').hide();
		}
	function oculta(){
		$('#nueva-direccion').fadeOut('slow');
        $('#sombreado').fadeOut('slow');
        $('.agregaNueva').show();
		}

    $('#estado').change(function(){
        var id_estado = $(this).val();
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
                }
            });
        }
    });
    

    $('#ciudad').change(function(){
        ciudad_s();
        updateColoni();
        });
    
  function ciudad_s()
  {
   //alert($("#ciudad :selected").text());    
   $("#nombre_ciudad").val($("#ciudad :selected").text()); 
  }

function updateColoni()
{
    var city    = $('#ciudad').find('option:selected').val();
    var ruta_abs    = getAbsolutePath();
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
function asignarPostcode() {
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

                            var id_state    = vals[2];
                            var ruta_abs    = getAbsolutePath();

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
                                    $('#ciudad').html(response.results).fadeOut(700).fadeIn(700);

                                        var city = vals[1];
                                        $('#ciudad').val(vals[1]);
                                        

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
                                                $('#id_colonia').html(response.results).fadeOut(700).fadeIn(700);
                                                $('#ciudad').val(vals[1]);
                                                $('#id_colonia').val(vals[0]);                                                
                                                ciudad_s();
                                                $("#fijo").focus();
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

                            $.when( updateColoni() ).done(function(a1){
                                $('#colonia').val(vals[0]);
                            });
                            
                            
                        }
                    }
                });
            }          
        }
    
    $('#new-address').click(function(){
        $('.validacion').remove();
        var id_country={$pais};
        var id_state=$('#estado').val();
        var id_customer={$cliente};
        var alias=$('#alias').val();
        var address1=$('#direccion').val();
        var address2=$('#complemento').val();
        var city=$('#nombre_ciudad').val();
        var postcode=$('#postcode').val();
        var id_colonia=$('#id_colonia').val();
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
                "active":active,
                "postcode":postcode,
                "id_colonia":id_colonia
            },
            beforeSend: function(ev) {
                    var result = Validate();                   
                    if (result){
                        $("#nueva-direccion").empty();
                        $("#nueva-direccion").html('<img style="margin: auto;" src="{$img_ps_dir}ad/waiting.gif" />');
                    }else{
                        ev.abort();
                    }
            },
            success: function(response){               
                oculta();
                //window.location= self.location;
                location.reload();
            }
        })
    })
{literal}
$('#postcode').focusout(function() {asignarPostcode();});
$('#postcode').keyup(function() {asignarPostcode();});
    function Validate(){
        var id_state=$('#estado').val();
        var alias=$('#alias').val();
        var address1=$('#direccion').val();
        var address2=$('#complemento').val();
        var city=$('#ciudad').val();
        var phone=$('#fijo').val();
        var phone_mobile=$('#movil').val();
        var postcode=$('#postcode').val();
        var id_colonia=$('#id_colonia').val();
                
        if(id_state==""){
            $('#label-estado').append('<span class="validacion" id="obliga-estado">Campo Requerido</span>');
            $('#estado').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
        }else{
            $('#obliga-estado').remove();
            $('#estado').removeAttr("style");
        }
        
        if(city==""){
            $('#label-ciudad').append('<span class="validacion" id="obliga-ciudad">Campo Requerido</span>');
            $('#ciudad').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
        }else{
            $('#obliga-ciudad').remove();  
            $('#ciudad').removeAttr("style");         
        }
        
        if(phone==""){
            $('#label-fijo').append('<span class="validacion" id="obliga-fijo">Campo Requerido</span>');
            $('#fijo').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
        }else{
            if (phone.match(/^[2-8]{1}\d{9}$/)){
                $('#obliga-fijo').remove();
                $('#fijo').removeAttr("style"); 
            }else{
                $('#label-fijo').append('<span class="validacion" id="obliga-fijo">Campo requerido</span>');
                $('#fijo').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
            }
        }
        
        if(phone_mobile==""){
            $('#label-movil').append('<span class="validacion" id="obliga-movil">Campo Requerido</span>');
            $('#movil').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
        }else{
            if (phone_mobile.match(/^[2-9][0-9]\d{8}$/)){
                $('#obliga-movil').remove();
                $('#movil').removeAttr("style"); 
            }else{
                $('#label-movil').append('<span class="validacion" id="obliga-movil">Campo requerido</span>');
                $('#movil').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
            }
        }
        
        if(address1==""){
            $('#label-direccion').append('<span class="validacion" id="obliga-direccion">Campo Requerido</span>');
            $('#direccion').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
        }else{
            $('#obliga-direccion').remove();
            $('#direccion').removeAttr("style"); 
        }
        
        
        if(address2==""){
            $('#label-complemento').append('<span class="validacion" id="obliga-complemento">Campo Requerido</span>');
            $('#complemento').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
        }else{
            $('#obliga-complemento').remove();
            $('#complemento').removeAttr("style"); 
        }
        
        
        if(alias==""){
            $('#label-alias').append('<span class="validacion" id="obliga-alias">Campo Requerido</span>');
            $('#alias').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
        }else{
            $('#obliga-alias').remove();
            $('#alias').removeAttr("style"); 
        }


		if(postcode==""){
            $('#label-postcode').append('<span class="validacion" id="obliga-alias">Campo Requerido</span>');
            $('#postcode').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}
		if(id_colonia==""){
			$('#label-colonia').append('<span class="validacion" id="obliga-alias">Campo Requerido</span>');
            $('#id_colonia').attr("style", "border-color:#A5689C;background-color:#FFFAFA");
		}
        var error=$('.validacion').length;
        
        if(error==0){
            return true;
        }else{
            return false;
        }
    }
    
    $('.enviar-form').click(function(){
        if($('[name="id_address_delivery"]').is(':checked')){
            $('#obliga-eleccion').remove();
            $('#form_dir').submit();
        }else{
            $('#titulo-1').append('<span class="validacion" id="obliga-eleccion">Campo requerido</span>')
        }    
    });
{/literal}
	{if !($direcciones)}
    $(window).load(function() {
   	 muestra();
   	});
    {/if}
</script>