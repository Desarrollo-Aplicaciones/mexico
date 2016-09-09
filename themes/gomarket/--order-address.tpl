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
<style type="text/css">

#atras11:hover {
padding: 0 0;
animation: none !important;
transition: none;
width: 149px;
height: 43px;
border: none;
border-style: none;
background: url(http://127.0.0.1/test.farmalisto.com.co/themes/gomarket/img//formula-medica/btn-anterior-hover.png)no-repeat top center !important;
-webkit-background-size: 100% 100%;
-moz-background-size: 100% 100%;
-o-background-size: 100% 100%;
background-size: 100% 100%;position: relative;
top: -1px;
left: -13px;
}
#atras11{
    background: url(http://127.0.0.1/test.farmalisto.com.co/themes/gomarket/img//formula-medica/btn-anterior.png)no-repeat top center !important;
margin:auto;width: 150px;height: 43px;animation: none !important;border: none;transition: none;
-webkit-background-size: 100% 100%;
-moz-background-size: 100% 100%;
-o-background-size: 100% 100%;
background-size: 100% 100%;position: relative;
top: -1px;
left: -13px;
}
#atras12{
    background: url(http://127.0.0.1/test.farmalisto.com.co/themes/gomarket/img//formula-medica/btn-anterior.png)no-repeat top center !important;
    margin:auto;width: 149px;height: 43px;animation: none !important;border: none;transition: none;
-webkit-background-size: 100% 100%;
-moz-background-size: 100% 100%;
-o-background-size: 100% 100%;
background-size: 100% 100%;position: relative;
top: 41px;
left: -427px;

}
#atras12:hover{
      background: url(http://127.0.0.1/test.farmalisto.com.co/themes/gomarket/img//formula-medica/btn-anterior-hover.png)no-repeat top center !important;
    margin:auto;width: 149px;height: 43px;animation: none !important;border: none;transition: none;
-webkit-background-size: 100% 100%;
-moz-background-size: 100% 100%;
-o-background-size: 100% 100%;
background-size: 100% 100%;position: relative;
top: 41px;
left: -427px;

}

@media screen and (max-width:1000px){
#atras11{
left: -23px;
top: 0px;
}#atras11:hover{
left: -23px;
top: 0px;
}#atras12{
 top: 38px;
left: -297px;


}#atras12:hover{ 
top: 38px;
left: -297px;


}
}
@media screen and (max-width:768px){
#atras11{
left: -43px;
top: 6px;
}#atras11:hover{
left: -43px;
top: 6px;
}#atras12{
   top: 2px;
left: -136px;

}#atras12:hover{ 
    top: 2px;
left: -136px;

}
}
@media screen and (max-width:480px){
#atras11{
    left: -44px;
    top: 6px;
}#atras11:hover{
    left: -44px;
    top: 6px;
}#atras12{
   top: 5px;
left: -78px;

}#atras12:hover{
    top: 5px;
left: -78px;

}
}
</style>

<form action="{$link->getPageLink($back_order_page, true)}" method="post">
{if !$opc}
    <p style="width: 100%; margin-top: 15px;">
        <h1 style=" display: inline; color:#979797; font-weight: normal; font-size: 17pt;">{l s='Datos de Entrega'}</h1>
        <input type="button" id="processAddress" name="processAddress" value="{l s='Continuar'} &raquo;" class="enviar-form1"/>
        <div id="atras11" style="float:right">
            <a  id="atras11" href="{$link->getPageLink($back_order_page, true, NULL, "step=0{if $back}&back={$back}{/if}")}" title="{l s='Previous'}" ></a></div>
    </p>
{/if}
{assign var='current_step' value='address'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}

<div style="diplay:block; width: 98%; height: auto;">
    <!-- ************************** PRIMERA COLUMNA ****************************-->
    <!-- <form action="{$link->getPageLink($back_order_page, true)}" method="post"> FORMULARIO COLUMNA 1-->
    <!-- <form action="{$link->getPageLink('address', true)|escape:'html'}" method="post" id="add_address"> FORMULARIO COLUMNA 2-->
    <div style="display:inline-block; min-width: 48%; max-width: 100%; vertical-align: top;">
        <div style="display: inline-block; width:100%; margin-top: 5px; margin-left: 5px;" id="titulo-1">
            <span class="titulo">Tú dirección de entrega:</span>
        </div>
        <br>       
        <input type="checkbox" name="same" id="addressesAreEquals" value="1" checked="checked" style="visibility:hidden;"/>
            {foreach from=$direcciones item=nr}
            {if $nr@iteration + 1 is even}<div>{/if}
            <div style="min-width: 48%; max-width: 100%; display: inline-block;">
                <table cellspacing="0">
                    <tr><td rowspan="4"><input style="margin-right: 15px;" type="radio" name="id_address_delivery" value="{$nr['id_address']}" onchange="updateAddressesDisplay();{if $opc}updateAddressSelection();{/if}" {if $nr['id_address'] == $cart->id_address_delivery}checked="checked"{/if}"/></td><td><b class="nombre-direccion">{$nr['alias']}</b></td></tr>
                    <tr><td><div class="detalle-direccion">{$nr['address1']}</div></td></tr>
                    <tr><td><div class="detalle-direccion">{$nr['city']}</div></td></tr>
                    <tr><td><div class="detalle-direccion">{$nr['state']}</div></td></tr>
                </table>
            </div>           
            {if $nr@iteration + 1 is odd}</div><br>{/if}
            {/foreach}
            {if $total is odd}</div><br>{/if}
    </div>
    <!-- ************************** FIN PRIMERA COLUMNA ****************************-->            
    <!-- ************************** SEGUNDA COLUMNA ****************************-->         
    <div id="nueva-direccion">
        <div style="display: inline-block; width:100%;">
            <span class="titulo">Registrar nueva Dirección:</span>
            <span class="obliga">(*) Campos Obligatorios</span>
        </div>
        <br>
        <br>
        <div>
        <div style="min-width: 50%; max-width: 100%; display: inline-block;">
            <p class="etiqueta" id="label-estado">Departamento*:</p>
            <select class="seleccion" id="estado" name="estado">
                <option value="" selected="selected">- Departamento -</option>
                {foreach from=$estados item=dp}
                <option value="{$dp['id_state']}">{$dp['state']}</option>
                {/foreach}
            </select>    
        </div>
        <div style="min-width: 50%; max-width: 100%; display: inline-block;">
            <p class="etiqueta" id="label-ciudad">Ciudad*:</p>
            <select class="seleccion" id="ciudad" name="ciudad">
                <option value="" selected="selected">- Ciudad -</option>
            </select>
                 <input type="hidden" class="hidden" name="nombre_ciudad" id="nombre_ciudad" value="" />
        </div>
        </div>
        <div>
        <div style="min-width: 50%; max-width: 95%; display: inline-block;">
            <p class="etiqueta" id="label-fijo">Telefono Fijo*:</p>
            <input class="entrada" type="text" value="" placeholder="ingrese su número fijo" id="fijo" name="fijo"/>
        </div>
        <div style="min-width: 50%; max-width: 95%; display: inline-block;">
            <p class="etiqueta" id="label-movil">Telefono Móvil*:</p>
            <input class="entrada" type="text" value="" placeholder="ingrese su número móvil" id="movil" name="movil"/>
        </div>
        </div>
        <div>
            <div style="width: 100%; display: inline-block;">
                <p class="etiqueta" id="label-direccion">Dirección*:</p>
                <input class="entrada larga" type="text" value="" placeholder="Ingrese su Dirección de entrega" id="direccion" name="direccion"/>
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
            <input type="button" id="processAddress" name="processAddress" value="{l s='Next'} &raquo;" class="enviar-form" />
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
                    $('#ciudad').html('<option value="" selected="selected">- Ciudad -</option>'+json.results);
                }
            });
        }
    });
    

    $('#ciudad').change(function(){
        ciudad_s();
        });
    
  function ciudad_s()
  {
   //alert($("#ciudad :selected").text());    
   $("#nombre_ciudad").val($("#ciudad :selected").text()); 
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
            if (phone.match(/^[2-8]{1}\d{6}$/)){
                $('#obliga-fijo').remove();
            }else{
                $('#label-fijo').append('<span class="validacion" id="obliga-fijo">Número no Válido</span>');
            }
        }
        
        if(phone_mobile==""){
            $('#label-movil').append('<span class="validacion" id="obliga-movil">Campo Requerido</span>');
        }else{
            if (phone_mobile.match(/^[3]{1}([0-2]|[5]){1}\d{1}[2-9]{1}\d{6}$/)){
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
            $('form').submit();
        }else{
            $('#titulo-1').append('<span class="validacion" id="obliga-eleccion">Elija una direccion</span>')
        }    
    })
    
{/literal}    
</script>