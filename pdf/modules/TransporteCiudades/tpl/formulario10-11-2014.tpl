<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<script src="../js/jquery/jquery.validate.js" type="text/javascript"></script>
<script src="../js/jquery.form.js"></script>

<style type="text/css" >
    
      .celdahead{
    padding: 0 0 0 5px;
    background-color:   #ffd573;  
    
    }
    .celda{
    padding: 0 0 0 5px;
    background-color:   #e9f1f6;  
    
    }
    .opciones {
      float: left;
      margin: 15px;
    }

    .toolbar { 
bottom: -2px; 
position: fixed; 
right: 50%; 
z-index: 10000; 
float: right; 
margin-top: 0; 
margin-right: -550px; 
margin-bottom: 5px; 
margin-left: 0px; 
}


</style>
{literal}
<script type="text/javascript">
$(document).ready(function() {
 


     
    var options = { 
        beforeSend: function() 
        {
            $("#progress").show();
            //clear everything
            // $("#bar").width('0%');
            $("#message").html("");
            $("#percent").html("<small>0%</small>");
        },
        uploadProgress: function(event, position, total, percentComplete) 
        {
            //$("#bar").width(percentComplete+'%');
            $("#percent").html(percentComplete+'%');
     
        },
        success: function() 
        {
            //$("#bar").width('100%');
            $("#percent").html('<small>100%</small>');
     
        },
        complete: function(response) 
        {
            $("#message").html("<font color='green'><small>"+response.responseText+"</small></font>");
        },
        error: function()
        {
            $("#message").html("<font color='red'> ERROR: No se pudo cargar el archivo.</font>");
     
        }
 
    }; 

$("#formcarga").ajaxForm(options);


var options = { 
        beforeSend: function() 
        {
            $("#progress2").show();
            //clear everything
            // $("#bar2").width('0%');
            $("#message2").html("");
            $("#percent2").html("<small>0%</small>");
        },
        uploadProgress: function(event, position, total, percentComplete) 
        {
            //$("#bar2").width(percentComplete+'%');
            $("#percent2").html(percentComplete+'%');
     
        },
        success: function() 
        {
            //$("#bar2").width('100%');
            $("#percent2").html('<small>100%</small>');
     
        },
        complete: function(response) 
        {
            $("#message2").html("<font color='green'><small>"+response.responseText+"</small></font>");
        },
        error: function()
        {
            $("#message2").html("<font color='red'> ERROR: No se pudo cargar el archivo.</font>");
     
        }
 
    }; 

$("#formcargacy").ajaxForm(options);

    $('#estadoc').change(function(){
        var ruta = "../ajax_formulario_cities_no_carry.php";                                   
        var estado = $(this).val();
        $('#ciudadc').empty();
        $.ajax({
            type:"post",
            url:ruta,
            data:{
                "id_state":estado
            },
            success:function(response){
                var json = $.parseJSON(response);
                $('#ciudadc').html('<option value="" selected="selected">- Ciudad -</option>'+json.results);
            },
            error: function(err){
                alert(err.responseText)
            }
        });
    });

    $('#ciudadc').change(function(){
        var ruta = "../ajax_formulario_colonia_no_carry.php";                                   
        var ciudad = $(this).val();
        $('#coloniac').empty();
        $.ajax({
            type:"post",
            url:ruta,
            data:{
                "id_ciudad":ciudad
            },
            success:function(response){
                var json = $.parseJSON(response);
                $('#coloniac').html('<option value="" selected="selected">- Colonia -</option>'+json.results);
            },
            error: function(err){
                alert(err.responseText)
            }
        });
    });


 $('"input[name=opc_ini]:radio"').change(function() {
  

   if(this.value != "cargatranscp") {
        $( "#codp_tr" ).hide();
    } else { 
        $('#codp_tr').show( "slow" );
    }

   if(this.value != "cargatransciudad") {
        $( "#ciudad_tr" ).hide();
    } else { 
        $('#ciudad_tr').show( "slow" );
    }
    
    //mediosp_ciudades
       if(this.value != "mediosp_ciudades") {
        $( "#mediosp_ciudades" ).hide();
    } else { 
        $('#mediosp_ciudades').show( "slow" );
    }
    

    if(this.value != "actuvis") {
        $( "#visimodi" ).hide();
    } else { 
        $('#visimodi').show( "slow" );
    }
    
    if(this.value != "mediosp_ciudades"){
      $("#mediosp_ciudades").hide();  
    }else{
      $("#mediosp_ciudades").show();       
    }

    if(this.value != "actuesp") {

        $( "#estadodiv" ).hide();
        $( "#ciudaddiv" ).hide();
        $( "#coloniadiv" ).hide();

    } else {

        $('#estadodiv').show( "slow" );
        $('#ciudaddiv').show( "slow" );
        $('#coloniadiv').show( "slow" );

    }

   });
   
//ajax 
console.log("Ajax mediosp");
   
    $.ajax({
        type: "post",
        url: "../modules/TransporteCiudades/ajax_list_mediosp.php",
        data: {
            "get_mediosp": 'mediosp'
        },
        success: function(response) {
            if (response != '0') {
console.log(response);
              
                $('#mediosp').html(response);
            } else
            {
                alert('¡Ups!, error ajax lista medios de pago ');
            }
        }
    });
   
   
});

function changemediop(obj)
{

array =  $(obj).val().split("_");
    $.ajax({
        type: "post",
        url: "../modules/TransporteCiudades/ajax_list_mediosp.php",
        data: {
            "mediosp": 'update', "status": array[0], "id_mediop": array[1]
        },
        success: function(response) {
            if (response != '0') {
console.log(response);
              array2 =  response.split("_");
                $(obj).html(array2[0]);
                $(obj).val(array2[1]+"_"+array[1]);
            } else
            {
                alert('¡Ups!, error ajax lista medios de pago ');
                }
            }
        });

    }
    
    
    function comprueba_extension(formulario, archivo) {
   extensiones_permitidas = new Array(".gif", ".jpg", ".doc", ".pdf");
   mierror = "";
   if (!archivo) {
      //Si no tengo archivo, es que no se ha seleccionado un archivo en el formulario
       mierror = "No has seleccionado ningún archivo";
   }else{
      //recupero la extensión de este nombre de archivo
      extension = (archivo.substring(archivo.lastIndexOf("."))).toLowerCase();
      //alert (extension);
      //compruebo si la extensión está entre las permitidas
      permitida = false;
      for (var i = 0; i < extensiones_permitidas.length; i++) {
         if (extensiones_permitidas[i] == extension) {
         permitida = true;
         break;
         }
      }
      if (!permitida) {
         mierror = "Comprueba la extensión de los archivos a subir. \nSólo se pueden subir archivos con extensiones: " + extensiones_permitidas.join();
       }else{
          //submito!
         alert ("Todo correcto. Voy a submitir el formulario.");
         formulario.submit();
         return 1;
       }
   }
   //si estoy aqui es que no se ha podido submitir
   alert (mierror);
   return 0;
}
</script>



{/literal}


<div>		

{* <form action="{$current}&token={$token}&configure=TransporteCiudades" id="target"  method="post"> *}
    <fieldset><legend>{$transciuLogo}</legend>
        <p>Seleccione la opciÃ³n deseada:</p>
        
        <div class="opciones"> <input type="radio" name="opc_ini" value="cargatranscp"> Cargar precios codigo postal - transportador </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="cargatransciudad"> Cargar precios ciudad - transportador </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="addtranscp"> Adicionar ciudades / colonias no registradas </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="actuesp"> Ajax ciudades </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="mediosp_ciudades"> Medios de pago por ciudades </div>
       
        <div id="codp_tr" style="display: none; float: left; width: 100%; margin: 20px;"> 

            <form id="formcarga" action="../modules/TransporteCiudades/upload.php" method="post" enctype="multipart/form-data">
                <fieldset style ="width:80%"> 
                    <small>
                    <p>Recuerde que el archivo CSV debe tener los siguientes campos  
                        <a href="../modules/updateprice/formato.csv">(cod_postal; id_transportador; precio; )</a>, estos <b>deben estar</b> en la cabecera del archivo. LOS PRECIOS DE LOS PRODUCTOS DEBEN SER ENTEROS (12500) O CON DECIMALES CUYO SEPARADOR SEA PUNTO (12500.560000) DE NO MAS DE 6 NUMEROS EN SU PARTE FRACCIONAL.</p>
                    
                     <br /><a href="listado_ciudades_colonias.php"> ** Descarga AQUI el listado de ciudades y colonias ** </a><br />
                     <br />
                    <p><input type="file" size="60" name="myfile"></p>
                    <input type="hidden" name="empid" value="{$empid}">                
                        <br><center><input type="submit" name="submitUpdatePrice" value="Actualizar Precios Transportistas" /></center>
                    </small>
                </fieldset>
            </form>

            <div class="toolbar2"> 
                <fieldset style ="width:80%">
                    <div id="progress">            
                        <div id="percent"><small>0%</small></div >
                    </div>
                    <div id="message"></div>
                </fieldset>
            </div>

        </div>


        <div id="ciudad_tr" style="display: none; float: left; width: 100%; margin: 20px;"> 

            <form id="formcargacy" action="../modules/TransporteCiudades/upload.php" method="post" enctype="multipart/form-data">
                <fieldset style ="width:80%"> 
                    <small>
                    <p>Recuerde que el archivo CSV debe tener los siguientes campos  
                        <a href="../modules/updateprice/formato.csv">(id_ciudad; id_transportador; precio; )</a>, estos <b>deben estar</b> en la cabecera del archivo. LOS PRECIOS DE LOS PRODUCTOS DEBEN SER ENTEROS (12500) O CON DECIMALES CUYO SEPARADOR SEA PUNTO (12500.560000) DE NO MAS DE 6 NUMEROS EN SU PARTE FRACCIONAL.</p>
                    
                     <br /><a href="listado_ciudades_colonias.php"> ** Descarga AQUI el listado de ciudades y colonias ** </a><br />
                     <br />
                    <p><input type="file" size="60" name="myfile"></p>
                    <input type="hidden" name="empid" value="{$empid}">                
                        <br><center><input type="submit" name="submitUpdatePrice" value="Actualizar Precios Transportistas Ciudades" /></center>
                    </small>
                </fieldset>
            </form >

            <div class="toolbar2"> 
                <fieldset style ="width:80%">
                    <div id="progress2">            
                        <div id="percent2"><small>0%</small></div >
                    </div>
                    <div id="message2"></div>
                </fieldset>
            </div>
            
        </div>
        
                        <div id="mediosp_ciudades" style="display: none; float: left; width: 100%; margin: 20px;">
                            <p></p>
                            
                            <div id="mediosp" style=" margin: 20px;"></div>
                            
                            <FORM action="../modules/TransporteCiudades/upload.php" method="post" enctype="multipart/form-data" >
                                <INPUT type="file" value="Seleccionar CSV" name="file_mediosp_ciudades">
                                <input type="hidden" name="empid" value="{$empid}">   
                                <INPUT type="submit" value="Enviar Archivo" name="submit_mediosp_ciudades" onclick="comprueba_extension(this.form, this.form.file_mediosp_ciudades.value); "> 
                                
                            </FORM>
                        </div>                 

        <div id="estadodiv" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione el estado a modificar : 
            <select style="width: auto; " name="estadoc" id="estadoc">    {html_options options=$EstadoDepto} </select>
        </div>

        <div id="ciudaddiv" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione la ciudad a modificar : 
            <select style="width: auto; " name="ciudadc" id="ciudadc"> <option value=""> -- Seleccione -- </option> </select>
        </div>

        <div id="coloniadiv" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione la Colonia : 
            <select style="width: auto; " name="coloniac" id="coloniac"> <option value=""> -- Seleccione -- </option> </select>
        </div>

      <div style="width: 100%; float: left;"> 
            <input type="hidden" id="step_opc" name="step_opc" value="1">
            {* <center><input type="submit" name="submitadmin_medicos" id="submitadmin_medicos" value="Continuar" class="button" /></center> *}
      </div>



    </fieldset>
{* </form> *}







</div>
                
                