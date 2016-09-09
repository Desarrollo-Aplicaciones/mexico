<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<script src="../js/jquery/jquery.validate.js" type="text/javascript"></script>

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
</style>
{literal}
<script type="text/javascript">
$(document).ready(function() {
 
    $('"input[name=opc_ini]:radio"').change(function() {
      

        if(this.value != "index") {       
            $( "#indexlist" ).hide();
        } else { 
            $('#indexlist').show( "slow" );
        }

        if(this.value != "search") {       
            $( "#searchlist" ).hide();
        } else { 
            $('#searchlist').show( "slow" );
        }

        if(this.value != "category") {       
            $( "#categorylist" ).hide();
        } else { 
            $('#categorylist').show( "slow" );
        }    

        if(this.value != "product") {       
            $( "#productlist" ).hide();
        } else { 
            $('#productlist').show( "slow" );
        }

    });

    $("#categorysel").change(function() {  
//alert(this.options[this.selectedIndex].text);
        if(this.options[this.selectedIndex].text != "izquierda") {       
            $( "#categorylistarr" ).hide();
        } else { 
            $('#categorylistarr').show( "slow" );
        }

    });





  $("#formpublicidad").validate({


                      wrapper: 'span',
                errorPlacement: function (error, element) {
                    error.css({'float': 'left', 'padding-left':'10px','margin-right':'20px','padding-bottom':'2px'});
                    error.addClass("arrow")
                    error.insertAfter(element);
                },

            rules: {
                opc_ini : {required: true},

                searchsel : {
                    required: { 
                        depends: function(element) {
                        return $('input[name=opc_ini]:checked').val() == 'search';
                    }
                }}, 
   
                categorysel : {
                    required: { 
                        depends: function(element) {
                         return $('input[name=opc_ini]:checked').val() == 'category';
                    }
                }},

                productsel : {
                    required: {  
                        depends: function(element) {
                         return $('input[name=opc_ini]:checked').val() == 'product';
                    }
                }},

                indexsel : {
                    required: { 
                        depends: function(element) {
                         return $('input[name=opc_ini]:checked').val() == 'index';
                    }
                }},
            },
            messages: {
                opc_ini : { required:" Seleccione un opción " },
                searchsel : { required:" Seleccione una ubicación de búsqueda " },
                categorysel : { required:" Seleccione una ubicación de categoría " },
                productsel : { required:" Seleccione una ubicación de producto " },
                indexsel : { required:" Seleccione una ubicación de index " },

            },
      
        });



});

function sendvalue(myValue){
    //alert(myValue);
    //alert(myValue.options[myValue.selectedIndex].text);
    
    $('#val_seleccionado').val(myValue.options[myValue.selectedIndex].text);
}

</script>
{/literal}


<div>		

<form name="formpublicidad" action="" id="formpublicidad"  method="post">
    <fieldset><legend></legend>
        <p>Seleccione la opción deseada:</p>
        
        <div class="opciones"> <input type="radio" name="opc_ini" value="index"> Página inicial </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="category"> Página de Categoria </div>            
        <div class="opciones"> <input type="radio" name="opc_ini" value="product"> Página de Producto </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="search"> Página de Resultados de Búsqueda</div>
        
        <span class="arrow" style="width: 100%; float: right; padding-left: 10px; margin-right: 20px; padding-bottom: 2px;">
<label id="opc_ini-error" class="error" style="display: none;" for="opc_ini"> </label>
</span>

        <div id="searchlist" style="display: none; float: left; width: 100%; margin: 20px;">  Ubicación : 
            <select style="width: auto; " name="searchsel" id="searchsel" OnChange="sendvalue(this);">    {html_options options=$search} </select>
        </div>

        <div id="categorylist" style="display: none; float: left; width: 100%; margin: 20px;">  Ubicación : 
            <select style="width: auto; " name="categorysel" id="categorysel" OnChange="sendvalue(this);">    {html_options options=$category} </select>
        </div>

        <div id="categorylistarr" style="display: none; float: left; width: 100%; margin: 20px;">  Categoría : 
            <select style="width: auto; " name="categorysellist" id="categorysellist">    {html_options options=$categoryfull} </select>
        </div>

        <div id="productlist" style="display: none; float: left; width: 100%; margin: 20px;">  Ubicación : 
            <select style="width: auto; " name="productsel" id="productsel" OnChange="sendvalue(this);">    {html_options options=$product} </select>
        </div>

        <div id="indexlist" style="display: none; float: left; width: 100%; margin: 20px;">  Ubicación : 
            <select style="width: auto; " name="indexsel" id="indexsel" OnChange="sendvalue(this);">    {html_options options=$index} </select>
        </div>


      <div style="width: 100%; float: left;"> 
            <input type="hidden" id="val_seleccionado" name="val_seleccionado" value="">
            <input type="hidden" id="step_opc" name="step_opc" value="1">
            <center><input type="submit" name="submitapublicidad" id="submitapublicidad" value="Continuar" class="button" /></center>
      </div>



    </fieldset>
</form>


</div>
                
                