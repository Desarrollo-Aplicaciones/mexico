
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




<style type="text/css">
    
    #comprarapida{
    height:  100%;
    width:  100%;
  }
    
</style>

<div id="comprarapida">
                       
                        
               <form method="post" >
                                      
          <div id="ccorreo">
           <p style="float:left;width: 100%;font-size: 8pt;">Correo*:</p>    
            <input type="text"  id="guest_email" style="width: 98%;" name="email" value="{if isset($smarty.post.email)}{$smarty.post.email|stripslashes}{/if}"
          
          </div>
                   
                        <div style="width: 50%;float: left;"><p style="float:left;width: 100%;font-size: 8pt;">Nombre*:</p>     
                        
                            <input type="text" class="text"  id="guest_firstname" name="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname}{/if}" />
                           <input type="hidden" name="customer_firstname" id="customer_firstnamei"  value="{if isset($smarty.post.customer_firstname)}{$smarty.post.customer_firstname}{/if}"> 
                        </div>
                        
                    <div  style="width: 50%;float: left;"><p style="float:left;width: 100%;font-size: 8pt;">Apellido*:</p>          
                        <input type="text" name="lastname" id="guest_lastnamei" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname}{/if}">
                         <input type="hidden" name="customer_lastname" id="guest_customer_lastname" value="{if isset($smarty.post.customer_lastname)}{$smarty.post.customer_lastname}{/if}"> 
                    </div>
                        
                 <div style="float: left;"><p style="width: 100%;float:left;font-size: 8pt;">Cédula*:</p>     
                  <input type="text" name="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni}{/if}">
                  </div>
               
               <div><p style="float:left;width:98%;font-size: 8pt;">Dirección*:</p>
                 <input type="text" name="address1" style="width:98%;" value="{if isset($smarty.post.address1)}{$smarty.post.address1}{/if}"> 
               </div>

             <div style="float: left;width: 50%;">
             <p class="etiqueta" id="label-estado" style="float:left;font-size: 8pt;">Departamento*:</p>
            <select class="seleccion" id="estado" name="id_state" style="color: #555555;">
             <option value="" selected="selected" style="float:left;" >- Departamento -</option>
                {foreach from=$estados item=dp}
                <option value="{$dp['id_state']}">{$dp['state']}</option>
                {/foreach}
            </select>    
             </div>
   
   
          <div style="float: left;width: 50%;">
            <p class="etiqueta" id="label-ciudad" style="float:left;font-size: 8pt;">Ciudad*:</p>
            <select class="seleccion" id="ciudad" name="city_id" style="width: 100%;color: #555555;">
                <option value=""  selected="selected" style="float:left;">- Ciudad -</option>
            </select>
                 <input type="hidden" class="hidden" name="city" id="nombre_ciudadi" value="" />
        </div>
                   
        
            
      <div><p style="float:left;width: 100%;font-size: 8pt;">Telefono*: </p>
    <input type="text" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone}{/if}">
   </div>

     
     <input type="hidden" name="days" value="31">
     <input type="hidden" name="months" value="12">
     <input type="hidden" name="years" value="1969"> 
     <input type="hidden" name="company" value="0"> 
     <input type="hidden" name="id_country" value="69">
    <input type="hidden" name="postcode" value="0000">
    <input type="hidden" name="alias" value="Mi dirección">
    <input type="hidden" name="is_new_customer" value="0">
     <input type="hidden" name="display_guest_checkout" value="1">
    <input type="submit" name="submitGuestAccount" value="Continuar" style="border: 1px #FFA500 solid;-moz-border-radius: 3px;-webkit-border-radius: 3px;border-radius: 3px;background-color: transparent;background-image: url('{$img_dir}authentication/index_Button2_bkgrnd.png');background-repeat: repeat-x;background-position: left top;color: #FFFFFF;font-family: Verdana;font-weight: bold;font-size: 10pt;width: 122px;height: 43px;float: right;margin-top: -26px;">
    <input type="hidden"  name="id_gender"  value="0" />                    
                       
                        
                        </form>
                    </div>
   
   <script type="text/javascript">

    $('#estado').change(function(){
        
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
    
        
});

    
    
</script>