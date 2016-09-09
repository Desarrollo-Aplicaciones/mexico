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
		updateColoni('0','0');
	});

/***** para las colonia mx u otro pais con codigo postal zonal ****/

	$('select#city').change(function(){
		updateColoni();
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

function updateCity(sel, sendstate)
{
	if (sendstate === undefined || sendstate == '') {
		var id_state = $('#id_state').find('option:selected').val();
	} else {
		var id_state = sendstate;
	}

	var ruta_abs 	= getAbsolutePath();
	//alert("Anadir Content Ciudades con provincia ID: " + $('#id_state').find('option:selected').val() + ' URL: ' + ruta_abs);
	if (sel === undefined || sel == '' ) {
          sel = '';
    }

	$.ajax({
		type: "POST",
		url: ruta_abs + "ajax_formulario_cities.php",
		dataType: 'json',
		data: 'id_state='+id_state+'&selected='+sel,
		beforeSend: function(objeto){
			$('#errors_login').slideUp(200);
			//$('#loading_forms').fadeIn(500);
		},
		success: function(response) {
			//response.data[0].id
			$('select#city').html(response.results).fadeOut(700).fadeIn(700);
			ciudad_s();
		},
		complete: function(objeto, exito){
			//$('#loading_forms').fadeOut(1000);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		
		}
	});
}

function updateColoni(sel, sendcity)
{
	if (sendcity === undefined || sendcity == '') {
		var city = $('#city').find('option:selected').val();	
	} else {
		var city = sendcity;
	}
	
	var ruta_abs 	= getAbsolutePath();
	//alert("Anadir Content Ciudades con provincia ID: " + $('#city').find('option:selected').val() + ' URL: ' + ruta_abs);
	
	if (sel === undefined || sendcity == '') {
          sel = '';
    }
    
	$.ajax({
		type: "POST",
		url: ruta_abs + "ajax_formulario_colonia_no_carry.php",
		dataType: 'json',
		data: 'city='+city+'&selected='+sel,
		beforeSend: function(objeto){
			$('#errors_login').slideUp(200);
			//$('#loading_forms').fadeIn(500);
		},
		success: function(response) {
			//response.data[0].id
			$('select#id_colonia').html(response.results).fadeOut(700).fadeIn(700);
			ciudad_s();
		},
		complete: function(objeto, exito){
			//$('#loading_forms').fadeOut(1000);
		},
		error: function(jqXHR, textStatus, errorThrown) {
		
		}
	});
}

  function ciudad_s()
  {
   //alert($("#city :selected").text());    
   $("#city_id").val($("#city :selected").text()); 
  }
