
{if isset($error)}
<p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else}
    
{literal}
 



<script>
    function validar_texto(e){
    tecla = (document.all) ? e.keyCode : e.which;
    //Tecla de retroceso para borrar, siempre la permite
    if ((tecla==8)||(tecla==0)){
        return true;
    }
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
    }
</script>
<script>
  function pulsar(e) {
  tecla = (document.all) ? e.keyCode :e.which;
  return (tecla!=13);
  }
  
  
</script>

{/literal}
 
<style type="text/css">
      



select,input { 

	border-top:1px solid #acbd5a;
	border-left:1px solid #acbd5a;
	border-bottom:1px solid #6e7f3d;
	border-right:1px solid #6e7f3d;
}

   
 </style>
 


<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.1/jquery-ui.min.js"></script>
<link rel="stylesheet" type="text/css" media="screen" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/base/jquery-ui.css">
<script type="text/javascript">
$(function(){  



    $("input[name='mediopago']").change(function() {
	
        if ($("input[name='mediopago']:checked").val() === "div3") {       

            var id_sel_b = $('#pse_bank').val();
            if (id_sel_b==""){  
                $('#pse_bank').html('<option value="" selected="selected">Cargando Listado de Bancos</option>');          
                $.ajax({
                    type: "post",
                    url: "{$base_dir}modules/payulatam/ajax_listado_b.php",
                    data: {
                        "id_state":id_sel_b
                    },
                    success: function(response){
                        var json = $.parseJSON(response);                        
                        $('#pse_bank').html('<option value="" selected="selected">Seleccione una entidad</option>'+json.results);
                    }
                });
            }

        }

    });




        $('#formPayUPse').validate({
{literal}
                  wrapper: 'span',
            errorPlacement: function (error, element) {
                error.css({'padding-left':'10px','margin-right':'20px','padding-bottom':'2px'});
                error.addClass("arrow")
                error.insertAfter(element);
            },
{/literal}            
            rules :{                
                pse_bank : {
                    required : true                                                
                },                
                pse_tipoCliente : {
                    required : true                     
                },
                pse_docType : {
                  required : true
                },
                pse_docNumber : {
                    required : true,                    
                    minlength : 8 , //para validar campo con minimo 3 caracteres
                    maxlength : 16 //para validar campo con maximo 9 caracteres      
                }
            },
            messages: {
                        pse_bank: { 
                            required: "El campo Banco es requerido."
                        },
                        pse_tipoCliente: { 
                            required: "El campo Tipo de cliente es requerido."
                        },
                        pse_docType : {
                            required: "El campo Tipo de documento es requerido."
                        },
                        pse_docNumber : {
                            required : "El campo Número de Documento es requerido.",
                            minlength: "Por favor ingrese m&iacute;nimo 8 caracteres.",
                            maxlength: "Por favor ingrese m&aacute;ximo 16 caracteres.",
                        }
                    },
        });    
    });
    
    
    


  function bank()
  {
   //alert($("#pse_bank :selected").text());    
   $("#name_bank").val($("#pse_bank :selected").text()); 
  }

  


</script>
  
 <div class="pagocont">
     
     
     <form  method="POST" action="./modules/payulatam/payuPse.php" id="formPayUPse" name="formPayUPse" autocomplete="off" >
         
         
         <div>


    <div class="contend-form" >

        <div style="display: inline-block; width:100%">
            <div style=" min-width: 38%; max-width: 45%; width: 39%; text-align: left; float: left; padding: 3px;">Banco *
            </div>

            <div  style="float: left;  min-width: 45%; max-width: 55%; width: 55%; ">
                <select id="pse_bank" name="pse_bank" style="width: 100%" onchange="bank()">
                   <option value="">Seleccione una entidad</option>
                </select> 
                <input type="hidden" value="" name="name_bank" id="name_bank"/>
            </div>
            <label class="error" for="pse_bank" style="display: none; width:80%; padding: 5px 10px 0 15px; text-align: right;"></label>
        </div>

        <div style="display: inline-block; width:100%">
            <div style=" min-width: 38%; max-width: 45%; width: 39%; text-align: left; float: left; padding: 3px;">Tipo de cliente *</div>

            <div  style="float: left;  min-width: 45%; max-width: 55%; width: 55%; ">
                <input type="radio" id="pse_tipoCliente" name="pse_tipoCliente" checked="checked" value="N">Natural <input type="radio" id="pse_tipoCliente" name="pse_tipoCliente" value="J" >Juridico
            </div>
            <label class="error" for="pse_tipoCliente" style="display: none; width:80%; padding: 5px 10px 0 15px; text-align: right;"></label>
        </div>


            <div style="display: inline-block; width:100%">
                <div style=" min-width: 38%; max-width: 45%; width: 39%; text-align: left; float: left; padding: 3px;">Tipo de documento *</div>

                <div  style="float: left;  min-width: 45%; max-width: 55%; width: 55%; ">
                    <select id="pse_docType" name="pse_docType" style="width: 100%">
    <option value="">Seleccione un tipo de documento</option>
    <option value="CC">Cédula de ciudadanía.</option>
    <option value="CE">Cédula de extranjería.</option>
    <option value="NIT">NIT, en caso de ser una empresa.</option>
    <option value="TI">Tarjeta de Identidad.</option>
    <option value="PP">Pasaporte.</option>
    <option value="IDC">Identificador único de cliente, para el caso de ID’s únicos de clientes/usuarios de servicios públicos.</option>
    <option value="CEL">Número Móvil, en caso de identificar a través de la línea del móvil.</option>
    <option value="RC">Registro civil de nacimiento.</option>
    <option value="DE">Documento de identificación Extranjero.</option>
    </select>   
                </div>
                <label class="error" for="pse_docType" style="display: none; width:80%; padding: 5px 10px 0 15px; text-align: right;"></label>
            </div>


            <div style="display: inline-block; width:100%">
                <div style=" min-width: 38%; max-width: 45%; width: 39%; text-align: left; float: left; padding: 3px;">Número de documento *</div>
                <div  style="float: left;  min-width: 45%; max-width: 55%; width: 55%; ">
                    <input type="text" id="pse_docNumber" name="pse_docNumber" value="" style="width: 100%">
                </div>
                <label class="error" for="pse_docNumber" style="display: none; width:80%; padding: 5px 10px 0 15px; text-align: right;"></label>
            </div>

                <div style="display: inline-block; width:100%; font-size:smaller">
                Recuerda tener habilitada tu cuenta corriente/ahorros para realizar compras  vía  internet. <br> No  olvides desbloquear las ventanas emergentes de tu navegador para evitar inconvenientes a la hora de realizar el pago.            </div>

                <input type="hidden" id="PaymentMethodForm_parameter_PagosOnlinePayment_Pse_pse_userAgent" name="PaymentMethodForm[parameter][PagosOnlinePayment_Pse][pse_userAgent]" value="Mozilla/5.0 (Windows NT 6.1; rv:26.0) Gecko/20100101 Firefox/26.0">
                <input type="hidden" id="PaymentMethodForm_parameter_PagosOnlinePayment_Pse_pse_sessionId" name="PaymentMethodForm[parameter][PagosOnlinePayment_Pse][pse_sessionId]" value="ldtp5nkml2ive4a9745hjt59k0">            
    </div>

    <!--<div style="margin:0 0 0 1%; ">
        <input type="submit" id="payuSubmit" value=" " style="
padding: 0 0; 
margin: 7px 7px 7px 7px;
width:145px;
height:40px;
border:none;
background:url({$img_dir}pagar-normal.png)no-repeat top center !important;
    
 -webkit-background-size: 100% 100%;           /* Safari 3.0 */
     -moz-background-size: 100% 100%;           /* Gecko 1.9.2 (Firefox 3.6) */
       -o-background-size: 100% 100%;           /* Opera 9.5 */
          background-size: 100% 100%;" />
         </div>-->
</div>

       

</form>
</div>






{/if}

