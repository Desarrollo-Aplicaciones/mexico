
<script type="text/javascript" >
   
    {literal}

/* mostrar formulario de crear cuanta o de compra rapida  con el evento click*/
$(document).ready(function(){
        
    $("#opinvitado").click(function(evento){
        
        $('#comprarapida').show(); 
        $("#create_acount").hide();
});    

       $("#opregistrado").click(function(evento){
        
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




{literal}
<style type="text/css">
    
    #comprarapida{height:  100%;width:  100%;}
     #guest_firstname{width: 92%;}
     #guest_lastnamei{ width: 95%;}
      #dni{width: 162%;}
    #estado{color: #555555;width: 97%;}


@media only screen and (min-width: 200px) and (max-width: 479px) {  
    #guest_firstname{width: 120px;}
    #guest_lastnamei{ width: 129px;}
    #dni{width:129%;}
    #estado{color: #555555;width: 120px;}
}
</style>
{/literal}

<div id="comprarapida">
                       
                        
               <form method="post" >
                                      
          <div id="ccorreo">
           <p style="float:left;width: 100%;font-size: 8pt; font-family: 'Open Sans', sans-serif;">Correo*:</p>    
            <input type="text"  id="guest_email" style="width: 98%;" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}"
          
          </div>
                   
                        <div style="width: 50%;float: left;"><p class="form-registro">Nombre*:</p>     
                        
                            <input type="text" class="text"  id="guest_firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
                           <input type="hidden" style="width:92%;" name="customer_firstname" id="customer_firstnamei"  value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}"> 
                        </div>
                        
                    <div  style="width: 50%;float: left;"><p class="form-registro">Apellido*:</p>          
                        <input type="text" name="lastname" id="guest_lastnamei" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}">
                         <input type="hidden" style="width:92%;" name="customer_lastname" id="guest_customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}"> 
                    </div>
                        
                 <div style="width: 50%;float: left;"><p class="form-registro">Ife*:</p>     
                  <input type="text" style="width:92%;" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}">
                  </div>
               
               <div style="float: left;width: 50%;" ><p class="form-registro">Telefono*: </p>
              <input type="text" name="phone" style="width:92%;" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}">
             </div>
               <div><p p class="form-registro">Dirección*:</p>
                 <input type="text" name="address1" style="width:98%;" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}" placeholder="Calle, # exterior, # interior"> 
               </div>

             <div style="float: left;width: 50%;">
             <p class="form-registro" id="label-estado" class="form-registro">Estado*:</p>
            <select class="seleccion" id="estado" name="id_state">
             <option value="" selected="selected" class="form-registro" >- Estado -</option>
                {foreach from=$estados item=dp}
                <option value="{$dp['id_state']}">{$dp['state']}</option>
                {/foreach}
            </select>    
             </div>
   
   
          <div style="float: left;width: 50%;">
            <p class="form-registro" id="label-ciudad" class="form-registro">Ciudad*:</p>
            <select class="seleccion" id="ciudad" name="city_id" style="width: 101%;color: #555555;">
                <option value=""  selected="selected" style="float:left;">- Ciudad -</option>
            </select>
                 <input type="hidden" class="hidden" name="city" id="nombre_ciudadi" value="" />
        </div>

        

        <div style="float: left;width: 70%;">
            <p class="etiqueta" id="label-id_colonia" style="float:left;font-size: 8pt;">Colonia*:</p>
            <select class="seleccion" id="id_colonia" name="id_colonia" style="width: 98%;color: #555555;">
                <option value=""  selected="selected" style="float:left;">- colonia -</option>
            </select>                
        </div>
        
        <div style="float: left;width: 20%;">
            <p class="etiqueta" id="label-postcode" style="float:left;font-size: 8pt;">Codigo Postal*:</p>
            <input type="text" id="postcode" maxlength="5" size="6" name="postcode" value="" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" /> {if isset($smarty.post.postcode)}{$smarty.post.postcode}{else}{if isset($address->postcode)}{$address->postcode|escape:'html'}{/if}{/if}
        </div>
                   
        
            


     
     <input type="hidden" name="days" value="31">
     <input type="hidden" name="months" value="12">
     <input type="hidden" name="years" value="1969"> 
     <input type="hidden" name="company" value="0"> 
     <input type="hidden" name="id_country" value="{$pais}">
    <input type="hidden" name="alias" value="Mi dirección">
    <input type="hidden" name="is_new_customer" value="0">
     <input type="hidden" name="display_guest_checkout" value="1">
    <input type="submit" name="submitGuestAccount" value="Continuar" style="border: 1px #FFA500 solid;-moz-border-radius: 3px;-webkit-border-radius: 3px;border-radius: 3px;background-color: transparent;background-image: url('{$img_dir}authentication/index_Button2_bkgrnd.png');background-repeat: repeat-x;background-position: left top;color: #FFFFFF;font-family: Verdana;font-weight: bold;font-size: 10pt;width: 122px;height: 43px;float: right;margin-top: 12px;">
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
                    $('#ciudad').html(json.results);
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
          url: "ajax_test.php?id_country="+$('#id_country').val()+"&postcode="+valpost,
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
      if ( $(this).val().length > 4) {
        reloadPostCode($(this).val());
      }

    });

    
    
</script>