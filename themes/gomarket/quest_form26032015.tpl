
<script type="text/javascript" >
  
    {literal}

/* mostrar formulario de crear cuanta o de compra rapida  con el evento click*/
$(document).ready(function(){
        
	//$("#opinvitado").click(function(evento){
	$("#titinvitado").click(function(evento){

		$("#opinvitado").attr("checked", true);
		$("#opregistrado").removeAttr("checked");
        $('#comprarapida').show(); 
        $("#create_acount").hide();
});    

	//$("#opregistrado").click(function(evento){
	$("#titregistrado").click(function(evento){
		
		$("#opinvitado").removeAttr("checked");
		$("#opregistrado").attr("checked", true);
        $('#comprarapida').hide();
        $("#create_acount").show();
});
   
});
{/literal}

 
  
  $(document).ready(function () {
            $("#guest_lastnamei").keyup(function () {
        var value2 = $(this).val();
        $("#guest_customer_lastname").val(value2);
    });
    
      
    $("#guest_firstname").keyup(function () {
        var value1 = $(this).val();
        $("#customer_firstnamei").val(value1);
    });
    
        
});


/* mostrar formulario de crear cuanta o de compra rapida al cargar la pagina */
  $(function(){  
    var checkeado = $("#opinvitado").attr("checked");
    if(checkeado) {
        $('#create_acount').hide(); 
         $('#comprarapida').show();
    } else {
       $('#comprarapida').hide();
        $('#create_acount').show(); 
    }
});

    
  
</script>



<div id="comprarapida">
	<form method="post" >
	<div id="ccorreo" class="regformu">
		<label class="etiqueta">E-mail<span class="purpura">*</span>:</label>
		<input type="text"  id="guest_email" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}">
		<div id="errorguest_email" class="rterror"></div>
	</div>

	<div class="regform">
		<label class="etiqueta">Nombre<span class="purpura">*</span>:</label>
		<input type="text" class="text"  id="guest_firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
		<input type="hidden" name="customer_firstname" id="customer_firstnamei"  value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}"> 
		<div id="errorguest_firstname" class="rterror"></div>
	</div>
                        
	<div class="regform">
		<label class="etiqueta">Apellido<span class="purpura">*</span>:</label>
		<input type="text" name="lastname" id="guest_lastnamei" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}">
		<input type="hidden" name="customer_lastname" id="guest_customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}"> 
		<div id="errorguest_lastnamei" class="rterror"></div>
	</div>
{*
	<div class="regform">
		<label class="etiqueta">Ife:</label>
		<input type="text" name="dni" id="dni2" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}">
		<div id="errordni2" class="rterror"></div>
	</div>
*}

  <div class="regform">
    <label class="etiqueta">Codigo Postal<span class="purpura">*</span>:</label>
      <input type="text" id="postcode" maxlength="5" size="6" name="postcode" value="" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" /> {if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html'}{/if}{/if}
    <div id="errorpostcode" class="rterror"></div>
  </div>

  <div class="regform">
    <label class="etiqueta">Colonia<span class="purpura">*</span>:</label>
      <select class="seleccion" id="id_colonia" name="id_colonia">
        <option value=""  selected="selected" disabled>- colonia -</option>
      </select>
    <div id="errorid_colonia" class="rterror"></div>
  </div>
  
	<div class="regform">
		<label class="etiqueta">Estado<span class="purpura">*</span>:</label>
		<select class="seleccion" id="estado" name="id_state">
			<option value="" selected="selected" class="form-registro" disabled>-Seleccionar-</option>
			{foreach from=$estados item=dp}
				<option value="{$dp['id_state']}">{$dp['state']}</option>
			{/foreach}
		</select>
		<div id="errorestado" class="rterror"></div>
	</div>
   
	<div class="regform">
		<label class="etiqueta">Ciudad<span class="purpura">*</span>:</label>
			<select class="seleccion" id="ciudad" name="city_id">
				<option value=""  selected="selected">- Ciudad -</option>
			</select>
		<div id="errorestado" class="rterror"></div>
		<input type="hidden" class="hidden" name="city" id="nombre_ciudadi" value="" />
	</div>

  <div class="regformu">
    <label class="etiqueta">Dirección<span class="purpura">*</span>:</label>
    <input type="text" name="address1" id="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" placeholder="Calle, # exterior, # interior"> 
    <div class="rterror" id="erroraddress1"></div>
  </div>

  <div class="regform">
    <label class="etiqueta">Telefono<span class="purpura">*</span>:</label>
    <input type="text" name="phone" id="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}">
    <div id="errorphone" class="rterror"></div>
  </div>

	<div class="TOS2">
			<input type="checkbox" id="TOSquest"/>
				Acepto <a href="{$base_uri}?id_cms=3&controller=cms" target="blank">términos y condiciones</a> legales.<span class="purpura">*</span>
			<div id="errorTOSquest" class="rterror"></div>
	</div>

     <input type="hidden" name="days" value="31">
     <input type="hidden" name="months" value="12">
     <input type="hidden" name="years" value="1969"> 
     <input type="hidden" name="company" value="0"> 
     <input type="hidden" name="id_country" value="{$pais}">
    <input type="hidden" name="alias" value="Mi dirección">
    <input type="hidden" name="is_new_customer" value="0">
     <input type="hidden" name="display_guest_checkout" value="1">
    <input type="submit" name="submitGuestAccount" value="Continuar" id="submitGuest">
    <input type="hidden"  name="id_gender"  value="0" />
                       
                        
                        </form>
                    </div>
   
   <script type="text/javascript">
	
    $('#estado').change(function(){
       updateColoni('0','0');
       //alert($().jquery);
        
        var id_estado = $(this).val();
        
         console.log("id_state:  "+ id_estado);
         
         if (id_estado===""){
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
                         $('#ciudad').html('<option value="" selected="selected" disabled>- Ciudad -</option>'+json.results);
                     }
                 });
             }
    });
    

    
  function ciudad_s()
  {
   //alert($("#ciudad :selected").text());    
   $("#nombre_ciudadi").val($("#ciudad :selected").text()); 
  }
  
  $(document).ready(function () {
      
      $("#guest_lastnamei").keyup(function () {
        var value2 = $(this).val();
         console.log("test 2: "+value2);
        $("#guest_customer_lastname").val(value2);
    });
    
      
    $("#guest_firstname").keyup(function () {
        var value1 = $(this).val();
        console.log("test 1:  "+value1);
        $("#customer_firstnamei").val(value1);
    });
    
       

    $("#guest_lastnamei").change(function () {
        var value2 = $(this).val();
         console.log("test 2: "+value2);
        $("#guest_customer_lastname").val(value2);
    });
    
      
    $("#guest_firstname").change(function () {
        var value1 = $(this).val();
        console.log("test 1:  "+value1);
        $("#customer_firstnamei").val(value1);
    }); 
});

$('#ciudad').change(function(){
    ciudad_s();
    updateColoni();
});


function updateCity(sel, sendstate)
{
  if (sendstate === undefined || sendstate == '' ) {
    var id_state = $('#id_state').find('option:selected').val();
  } else {
    var id_state = sendstate;
  }

  var ruta_abs  = getAbsolutePath();
  //alert("Anadir Content Ciudades con provincia ID: " + $('#id_state').find('option:selected').val() + ' URL: ' + ruta_abs);
  if (sel === undefined || sel == '') {
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
      $('select#ciudad').html(response.results).fadeOut(700).fadeIn(700);
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
    var city = $('#ciudad').find('option:selected').val();  
  } else {
    var city = sendcity;
  }
  var ruta_abs  = getAbsolutePath();
  //alert("Anadir Content Ciudades con provincia ID: " + $('#city').find('option:selected').val() + ' URL: ' + ruta_abs);
  
  if (sel === undefined || sel == '') {
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


if ( $('#postcode').val() != '') {
      reloadPostCode($('#postcode').val());
    }

    function reloadPostCode(valpost) {
      $.ajax({
          type: "POST",
          url: "ajaxs/postcode.php?id_country="+$('#id_country').val()+"&postcode="+valpost,
          success: function(isApplicable){
            if ( isApplicable != 0) {             
              vals=isApplicable.split(";");
              $('#estado').val(vals[2]);
              updateCity(vals[1],vals[2]);
              updateColoni(vals[0],vals[1]);
            }
          }
        });
    }

    $('#postcode').focusout(function() {
      //alert('Validando información del código postal.');
      if ( $(this).val().length == 5) {
        reloadPostCode($(this).val());
      }

    });

    $('#postcode').keyup(function() {
      //alert('Validando información del código postal.');
      if ( $(this).val().length == 5) {
        reloadPostCode($(this).val());
      }

    });
    
</script>