/*
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
*/

$(document).ready(function()
{
	if (typeof(formatedAddressFieldsValuesList) != 'undefined')
		updateAddressesDisplay(true);
	resizeAddressesBox();
});

//envío Nocturno
function envio_nocturno(id)
{  
    var id_dir="#"+id+'_nocturno';


	if($(id_dir).is(':checked'))
	{
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				id_address: id,
				valor_nocturno: 'get',
				step: 2,
				ajax: 'true',
				controller: 'order',
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
				else{
					if (jsonData.valor)
					{
						var valor=jsonData.valor;
						if(confirm("¿Desea recibir su pedido esta misma noche por solo $"+valor+" pesos adicionales?")){
							$("#"+id).click(function(){
								  $("#rb"+id).trigger("click");
								})
							updateDeliveryNocturno(id_dir);
						}
						else{
							$(id_dir).prop("checked", false);
						}
						
					}
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
	
	
				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
	else{
		updateDeliveryNocturno(id_dir);
	}
}

//envío express
function envioExpress(id)
{  
	if($("#"+id).is(':checked'))
	{ 
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				id_address: id,
				express: true,
				step: 2,
				ajax: 'true',
				controller: 'order',
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
				else{
					if (jsonData.valor)
					{
						var valor=jsonData.valor;
						confirmExpress(valor,id);
					}
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
	
	
				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
	else{
		updateDelivery(true);
	}
}
function confirmExpress(valor,id){
	var precio = valor.toString().split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1.');
	precio = precio.split('').reverse().join('').replace(/^[\.]/,'');
	$('#xpsValue').html("$ "+precio);
	$('#confirmExpress').fadeIn('slow');
	$('#sombreado').fadeIn('slow');
	$('#xpscancel').click(function() {
		$('#confirmExpress').fadeOut('slow');
		$('#sombreado').fadeOut('slow');
		$("#"+id).prop("checked", false);
	});
	$('#xpsaccept').click(function() {
		$('#confirmExpress').fadeOut('slow');
		$('#sombreado').fadeOut('slow');
		$("#"+id).click(function(){
			$("#rb"+id).trigger("click");
		});
		updateDelivery();
		$('#form_dir').submit();
 	});
}
function updateDelivery(valor)
{
	//if($("#"+id).is(':checked'))
	{
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				valorExpress: true,
				checked: valor,
				step: 2,
				ajax: 'true',
				controller: 'order',
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
	
	
				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
}



function updateDeliveryNocturno(id_dir)
{
    var status = 'disabled';


	if($(id_dir).is(':checked'))
	{
           status = 'enabled';  
          // enable(id_dir); 
        }

	{
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				entregaNocturna: status,
                                checked: true,
				step: 2,
				ajax: 'true',
				controller: 'order',
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
	
	
				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	}
}
function updateLocaliadBarrio(id_address){
    
	var localidad = $('#'+id_address+'localidades').val();
	var barrio = $('#'+id_address+'barrios').val();
        
        if(localidad =='' || barrio == '' || barrio == '-Barrio-' || localidad =='-Localidad-'){
            alert('Para obtener tu pedido esta misma noche, debes seleccionar tu localidad y barrio.');
            $('#'+id_address+'_nocturno_up').attr('checked', false);
            return false;
        }
       
	//alert ("delivery: "+ idAddress_delivery + " | invoice :" + idAddress_invoice + " | hide" +$('#id_address_delivery:hidden').length + " | token:" +static_token);
 	
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				step: 2,
				ajax: 'true',
				controller: 'order',
				'multi-shipping': $('#id_address_delivery:hidden').length,
				id_localiad: localidad,
				id_barrio: barrio,
                                id_address: id_address,
                                update_localidad_barrio: true,
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
                                if(jsonData.entrega_nocturna == true){
                                    
                                    var box_entrega_nocturna = '<input id="'+id_address+'_nocturno" type="checkbox" onchange="envio_nocturno('+id_address+')" value="'+id_address+'" name="envioNocturno" checked="checked" > Deseo mi orden esta misma noche.';
                                   $('#'+id_address+'_box_entrega_nocturna').html(box_entrega_nocturna);  
                                   envio_nocturno(id_address);
                                }

			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {


				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});

    
}

function displayBarrios(id_address){
    
	var localidad = $('#'+id_address+'localidades').val();
	    
	//alert ("delivery: "+ idAddress_delivery + " | invoice :" + idAddress_invoice + " | hide" +$('#id_address_delivery:hidden').length + " | token:" +static_token);
 	
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				step: 2,
				ajax: 'true',
				controller: 'order',
				'multi-shipping': $('#id_address_delivery:hidden').length,
				lid_localiad: localidad,
                                id_address: id_address,
                                display_barrios: true,
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
                                   $('#'+id_address+'barrios').html(jsonData.results);

			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {


				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});

    
}


//update the display of the addresses
function updateAddressesDisplay(first_view)
{
	// update content of delivery address
	updateAddressDisplay('delivery');
	var txtInvoiceTitle = "";
	try{
		var adrs_titles = getAddressesTitles();
		txtInvoiceTitle = adrs_titles.invoice;
	}
	catch (e)
	{}
	// update content of invoice address
	//if addresses have to be equals...
	if ($('input[type=checkbox]#addressesAreEquals:checked').length === 1 && ($('#multishipping_mode_checkbox:checked').length === 0))
	{
		if ($('#multishipping_mode_checkbox:checked').length === 0) {
			$('#address_invoice_form:visible').hide('fast');
		}
		$('ul#address_invoice').html($('ul#address_delivery').html());
		$('ul#address_invoice li.address_title').html(txtInvoiceTitle);
	}
	else
	{
		$('#address_invoice_form:hidden').show('fast');
		if ($('#id_address_invoice').val())
			updateAddressDisplay('invoice');
		else
		{
			$('ul#address_invoice').html($('ul#address_delivery').html());
			$('ul#address_invoice li.address_title').html(txtInvoiceTitle);
		}	
	}
	if(!first_view)
	{
		if (orderProcess === 'order')
			updateAddresses();
	}
	return true;
}

function updateAddressDisplay(addressType)
{
	//alert ("arr: "+formatedAddressFieldsValuesList);
	if (formatedAddressFieldsValuesList.length <= 0) 
		return false;
/*alert("addressType:" +$("input[name='id_address_delivery']:checked").val());
alert("a:" + $("#input[name='id_address_" + addressType + "']:checked").val());
*/
	var idAddress = parseInt($("input[name='id_address_" + addressType + "']:checked").val());
	//alert("idaddress:" +idAddress);
	buildAddressBlock(idAddress, addressType, $('#address_' + addressType));

	// change update link
	var link = $('ul#address_' + addressType + ' li.address_update a').attr('href');
	var expression = /id_address=\d+/;
	if (link)
	{
		link = link.replace(expression, 'id_address=' + idAddress);
		$('ul#address_' + addressType + ' li.address_update a').attr('href', link);
	}
	resizeAddressesBox();
}

function updateAddresses()
{
	var idAddress_delivery = parseInt($("input[name='id_address_delivery']:checked").val());
	var idAddress_invoice = parseInt($("input[name='id_address_delivery']:checked").val());
	//alert ("delivery: "+ idAddress_delivery + " | invoice :" + idAddress_invoice + " | hide" +$('#id_address_delivery:hidden').length + " | token:" +static_token);
   	if(isNaN(idAddress_delivery) == false && isNaN(idAddress_invoice) == false)	
		$.ajax({
			type: 'POST',
			headers: { "cache-control": "no-cache" },
			url: baseUri + '?rand=' + new Date().getTime(),
			async: false,
			cache: false,
			dataType : "json",
			data: {
				processAddress: true,
				step: 2,
				ajax: 'true',
				controller: 'order',
				'multi-shipping': $('#id_address_delivery:hidden').length,
				id_address_delivery: idAddress_delivery,
				id_address_invoice: idAddress_invoice,
				token: static_token
			},
			success: function(jsonData)
			{
				if (jsonData.hasError)
				{
					var errors = '';
					for(var error in jsonData.errors)
						//IE6 bug fix
						if(error !== 'indexOf')
							errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
					alert(errors);
				}
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {


				if (textStatus !== 'abort')
					alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
			}
		});
	resizeAddressesBox();
}
function enable(id)
{		
		updateDelivery(true);
		$(".express").removeAttr("style");
		$("#texto_"+id).attr("style", "color:#39CB98");
		$("#"+id+"_box_entrega_nocturna").attr("style", "color:#39CB98");
		$(".express input[type=checkbox]").attr("disabled", true);
		$(".express input[type=checkbox]").prop("checked", false);
                $(".express select").attr("disabled", true);
		//$(".express input[type=checkbox]").removeAttr("checked");
		$("#"+id+"_nocturno").removeAttr("disabled");
		$("#"+id).removeAttr("disabled");
}
