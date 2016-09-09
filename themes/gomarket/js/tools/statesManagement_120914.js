$(document).ready(function()
{	
	bindStateInputAndUpdate();
});

function bindStateInputAndUpdate()
{
	$('.id_state, .dni, .postcode').css({'display':'none'});
	updateState();
	updateNeedIDNumber();
	updateZipCode();

	$('select#id_country').change(function(){
		updateState();
		updateNeedIDNumber();
		updateZipCode();
	});
	
	$('select#id_state').change(function(){
		updateCity();
	});

	if ($('select#id_country_invoice').length !== 0)
	{
		$('select#id_country_invoice').change(function(){   
			updateState('invoice');
			updateNeedIDNumber('invoice');
			updateZipCode('invoice');
		});
		updateState('invoice');
		updateNeedIDNumber('invoice');
		updateZipCode('invoice');
	}
}

function updateState(suffix)
{
	$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')+' option:not(:first-child)').remove();
	var states = countries[$('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val()];
	if(typeof(states) !== 'undefined')
	{
		$(states).each(function (key, item){
			$('select#id_state'+(suffix !== undefined ? '_'+suffix : '')).append('<option value="'+item.id+'"'+ (idSelectedCountry === item.id ? ' selected="selected"' : '') + '>'+item.name+'</option>');
		});
		$('.id_state'+(suffix !== undefined ? '_'+suffix : '')+':hidden').fadeIn('slow');;
	}
	else
		$('.id_state'+(suffix !== undefined ? '_'+suffix : '')).fadeOut('fast');
}

function updateNeedIDNumber(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());
	if ($.inArray(idCountry, countriesNeedIDNumber) >= 0)
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')+':hidden').fadeIn('slow');
	else
		$('.dni'+(suffix !== undefined ? '_'+suffix : '')).fadeOut('fast');
}

function updateZipCode(suffix)
{
	var idCountry = parseInt($('select#id_country'+(suffix !== undefined ? '_'+suffix : '')).val());
	if (countriesNeedZipCode[idCountry] !== 0)
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')+':hidden').fadeIn('slow');
	else
		$('.postcode'+(suffix !== undefined ? '_'+suffix : '')).fadeOut('fast');
}

function updateCity()
{
	var id_state 	= $('#id_state').find('option:selected').val();
	var ruta_abs 	= getAbsolutePath();
	//alert("Anadir Content Ciudades con provincia ID: " + $('#id_state').find('option:selected').val() + ' URL: ' + ruta_abs);
	
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
			$('select#city').html(response.results).fadeOut(700).fadeIn(700);
		},
		complete: function(objeto, exito){
			//$('#loading_forms').fadeOut(1000);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		
		}
	});
}
