


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
{if !$opc}
        <div class="titulo-pasos">{l s='Datos de Entrega'}</div>
        <div class="botones">
            <input type="button" id="processAddress" name="processAddress" value="{l s='Next'} &raquo;" class="enviar-form" />
            <div id="atras11">
                <a href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" ></a></div>
        </div>
{/if}
{assign var='current_step' value='address'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}

<div style="diplay:block; width: 100%; height: auto;">
    <!-- ************************** PRIMERA COLUMNA ****************************-->
    <!-- <form action="{$link->getPageLink($back_order_page, true)}" method="post"> FORMULARIO COLUMNA 1-->
    <!-- <form action="{$link->getPageLink('address', true)|escape:'html'}" method="post" id="add_address"> FORMULARIO COLUMNA 2-->
    <div class="contenedor" id="primera_columna">
        <div class="titulo" id="titulo-1">
            Tu dirección de entrega:
        </div>
        <br>       
        <input type="checkbox" name="same" id="addressesAreEquals" value="1" checked="checked" style="visibility:hidden;"/>
            {foreach from=$direcciones item=nr}
            {if $nr@iteration + 1 is even}<div>{/if}
            <div class="direccion">
                    <div style="display: table-cell;vertical-align:middle;">
                        <input style="margin-right: 15px;" type="radio" name="id_address_delivery" value="{$nr['id_address']}" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" {if $nr['id_address'] == $cart->id_address_delivery}checked="checked"{/if}"/>
                    </div>
                    <div class="nombre-direccion">{$nr['alias']}</div>
                    <div class="detalle-direccion">{$nr['address1']}</div>
                    <div class="detalle-direccion">{$nr['city']}, {$nr['state']}</div>
            </div>           
            {if $nr@iteration + 1 is odd}</div><br>{/if}
            {/foreach}
            {if $total is odd}</div><br>{/if}
    </div>
    <!-- ************************** FIN PRIMERA COLUMNA ****************************-->            
    <!-- ************************** SEGUNDA COLUMNA ****************************-->         
    <div class="contenedor" id="nueva-direccion">
        <div style="display: inline-block; width:100%;">
            <span class="titulo">Registrar nueva Dirección:</span>
            <span class="obliga">(*) Campos Obligatorios</span>
        </div>
        <br>
        <br>
        <div>
        <div class="datos">
            <p class="etiqueta" id="label-estado">Estado*:</p>
            <select class="seleccion" id="estado" name="estado">
                <option value="" selected="selected">- Estado -</option>
                {foreach from=$estados item=dp}
                <option value="{$dp['id_state']}">{$dp['state']}</option>
                {/foreach}
            </select>    
        </div>
        <div class="datos">
            <p class="etiqueta" id="label-ciudad">Ciudad*:</p>
            <select class="seleccion" id="ciudad" name="ciudad">
                <option value="" selected="selected">- Ciudad -</option>
            </select>
                 <input type="hidden" class="hidden" name="nombre_ciudad" id="nombre_ciudad" value="" />
        </div>
        </div>
        <div>
        <div class="datos">
            <p class="etiqueta" id="label-colonia">Colonia*:</p>
            <select class="seleccion" id="id_colonia" name="id_colonia">
                <option value="" selected="selected">- Colonia -</option>                
            </select>    
        </div>
        <div class="datos">
            <p class="etiqueta" id="label-postcode">Postcode*:</p>
            <input type="text" id="postcode" name="postcode" maxlength="5" value="">             
            
        </div>
        </div>

        <div>
        <div style="min-width: 50%; max-width: 95%; display: inline-block;">
            <p class="etiqueta" id="label-fijo">Teléfono Fijo*:</p>
            <input class="entrada" type="text" value="" placeholder="ingrese su número fijo" id="fijo" name="fijo"/>
        </div>
        <div style="min-width: 50%; max-width: 95%; display: inline-block;">
            <p class="etiqueta" id="label-movil">Teléfono Móvil*:</p>
            <input class="entrada" type="text" value="" placeholder="ingrese su número móvil" id="movil" name="movil"/>
        </div>
        </div>
        <div>
            <div style="width: 100%; display: inline-block;">
                <p class="etiqueta" id="label-direccion">Dirección*:</p>
                <input class="entrada larga" type="text" value="" placeholder="Calle, # enterior, # interior" id="direccion" name="direccion"/>
            </div>
        </div>
        <div>
            <div style="width: 100%; display: inline-block;">
                <p class="etiqueta" id="label-complemento">Complemento dirección*:</p>
                <input class="entrada larga" type="text" value="" placeholder="Indicaciones adicionales para la dirección" id="complemento" name="complemento"/>
            </div>
        </div>
        <div>
            <div style="width: 100%; display: inline-block;">
                <p class="etiqueta" id="label-alias">Nombre de dirección*:</p>
                <input class="entrada larga" type="text" value="" placeholder="Por favor asigne un nombre a esta dirección para futuras referencias" id="alias" name="alias"/>
            </div>
        </div>
        <div>
            <input style="float:right;margin-top:15px;" type="button" value="Registrar dirección" id="new-address"/>
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
            <div id="atras12" ><a id="atras12" href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" ></a></div>
            <input type="button" id="processAddress2" name="processAddress2" value="{l s='Next'} &raquo;" class="enviar-form" />
        </p>
    </form>
</div>



<script>

    $('#estado').change(function(){
        var id_estado = $(this).val();
        if (id_estado==""){
            $('#ciudad').html('<option value="" selected="selected">- Ciudad -</option>');
        }else{
            $.ajax({
                type: "post",
                url: "{$base_dir}ajax_formulario_cities.php",
                data: {
                    "id_state":id_estado
                },
                success: function(response){
                    var json = $.parseJSON(response);
                    $('#ciudad').html(json.results);
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
        url: ruta_abs + "ajax_formulario_colonia_no_carry.php",
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

$('#postcode').focusout(function() {
            if ( $(this).val().length > 4) {
                $.ajax({
                    type: "GET",
                    url: "ajax_test.php?postcode="+$(this).val(),
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
                                url: ruta_abs + "ajax_formulario_cities.php",
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
                                            url: ruta_abs + "ajax_formulario_colonia_no_carry.php",
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

                            /*$.when( updateColoni() ).done(function(a1){
                                $('#colonia').val(vals[0]);
                            });*/
                            
                            
                        }
                    }
                });
            }           
        });
    
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
                window.location= self.location
            }
        })
    })
    
{literal}
    function Validate(){
        var id_state=$('#estado').val();
        var alias=$('#alias').val();
        var address1=$('#direccion').val();
        var address2=$('#complemento').val();
        var city=$('#ciudad').val();
        var phone=$('#fijo').val();
        var phone_mobile=$('#movil').val();
                
        if(id_state==""){
            $('#label-estado').append('<span class="validacion" id="obliga-estado">Campo Requerido</span>');
        }else{
            $('#obliga-estado').remove();
        }
        
        if(city==""){
            $('#label-ciudad').append('<span class="validacion" id="obliga-ciudad">Campo Requerido</span>');
        }else{
            $('#obliga-ciudad').remove();           
        }
        
        if(phone==""){
            $('#label-fijo').append('<span class="validacion" id="obliga-fijo">Campo Requerido</span>');
        }else{
            if (phone.match(/^[2-8]{1}\d{7}$/)){
                $('#obliga-fijo').remove();
            }else{
                $('#label-fijo').append('<span class="validacion" id="obliga-fijo">Número no Válido</span>');
            }
        }
        
        if(phone_mobile==""){
            $('#label-movil').append('<span class="validacion" id="obliga-movil">Campo Requerido</span>');
        }else{
            if (phone_mobile.match(/^[2-9][0-9]\d{8}$/)){
                $('#obliga-movil').remove();
            }else{
                $('#label-movil').append('<span class="validacion" id="obliga-movil">Número no Válido</span>');
            }
        }
        
        if(address1==""){
            $('#label-direccion').append('<span class="validacion" id="obliga-direccion">Campo Requerido</span>');
        }else{
            $('#obliga-direccion').remove();
        }
        
        
        if(address2==""){
            $('#label-complemento').append('<span class="validacion" id="obliga-complemento">Campo Requerido</span>');
        }else{
            $('#obliga-complemento').remove();           
        }
        
        
        if(alias==""){
            $('#label-alias').append('<span class="validacion" id="obliga-alias">Campo Requerido</span>');
        }else{
            $('#obliga-alias').remove();
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
            $('#titulo-1').append('<span class="validacion" id="obliga-eleccion">Elija una direccion</span>')
        }    
    })
    
{/literal}    
</script>