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
  

   if(this.value != "actumed") {       
        $( "#medicmodi" ).hide();
        $( "#medccmodi" ).hide();        
    } else { 
        $('#medicmodi').show( "slow" );
        $('#medccmodi').show( "slow" );
    }

    if(this.value != "actuvis") {       
        $( "#visimodi" ).hide();
    } else { 
        $('#visimodi').show( "slow" );
    }

    if(this.value != "actuesp") {       
        $( "#espemodi" ).hide();
    } else { 
        $('#espemodi').show( "slow" );
    } 

    if(this.value != "consulvisit") {       
        $( "#divvisitadores" ).hide();
    } else { 
        $('#divvisitadores').show( "slow" );
    }

   });


 $("#voucher").autocomplete('index.php?controller=AdminCartRules&token={/literal}{$tokenn}{literal}', {
                    minChars: 3,
                    max: 15,
                    width: 250,
                    selectFirst: false,
                    scroll: false,
                    dataType: "json",
                    formatItem: function(data, i, max, value, term) {
                        return value;
                    },
                    parse: function(data) {
                        if (!data.found)
                            $('#vouchers_err').html('No se encontraron cupones').show();
                        else
                            $('#vouchers_err').hide();
                        var mytab = new Array();
                        for (var i = 0; i < data.vouchers.length; i++)
                            mytab[mytab.length] = { data: data.vouchers[i], value: data.vouchers[i].name + (data.vouchers[i].code.length > 0 ? ' - ' + data.vouchers[i].code : '')};
                        return mytab;
                    },
                    extraParams: {
                        selopcvoucher: function opcvouchersel() {
                                            return $('input:radio[name=opcselv]:checked').val();
                                    },
                        selorandom: function opcrandom() {
                                            return $('#valrandom').val();
                                    },  
                        modulovisita: function opcrandom() {
                                            return $('#modulovisita').val();
                                    },
                        ajax: "1",
                        token: "{/literal}{$tokenn}{literal}",
                        tab: "AdminCartRules",
                        action: "searchCartRuleVouchers"
                    }
                }
            )
            .result(function(event, data, formatted) {
                $('#voucher').val(data.name);
                setUserID(data.id_cart_rule);

            });


function setUserID(myValue){
  $('#doc_fnd').val(myValue).trigger('change');

    if($('#doc_fnd').val().length===0) { 
        
        $( "#datcupon" ).hide();
    } else { 
        
        $('#datcupon').show( "slow" );
    }}

});
</script>
{/literal}


<div>		

<form action="{$current}&token={$token}&configure=AdminMedicos" id="target"  method="post">
    <fieldset><legend>{$adminmedicLogo}</legend>
        <p>Seleccione la opción deseada:</p>
        {if $perfil_usuario eq 0 || $perfil_usuario eq 2 || $perfil_usuario eq 3}
        <div class="opciones"> <input type="radio" name="opc_ini" value="creamed"> Crear Cupón Médico </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="actumed"> Modificar Cupón Médico </div>
            {*if $perfil_usuario eq 0 || $perfil_usuario eq 2 }
        <div class="opciones"> <input type="radio" name="opc_ini" value="creavis"> Crear Visitador Médico </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="actuvis"> Modificar Visitador Médico </div>
            {/if*}
        <div class="opciones"> <input type="radio" name="opc_ini" value="creaesp"> Crear Especialidad Médica </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="actuesp"> Modificar Especialidad Médica </div>
        <div class="opciones"> <input type="radio" name="opc_ini" value="consulvisit"> Consultar Médicos </div>
        {/if}
        
        <div id="medicmodi" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione el médico a modificar (Introduzca nombres o apellidos) o Cédula: 
            <br /> Médico <input type="radio" value="medico" name="opcselv" id="opcselv2" checked="checked">
             Cédula <input type="radio" value="cedula" name="opcselv" id="opcselv1">
            
            <input id="valrandom" type="hidden" value="{$random_visitador}" name="valrandom">
            <input id="modulovisita" type="hidden" value="modulovisita" name="modulovisita">
            <input id="voucher" class="ac_input" type="text" value="" autocomplete="off" style="width: 250px;">
            <input id="doc_fnd" type="hidden" value="" name="doc_fnd">
            <div id="vouchers_err" class="warn" style="display: none;"></div>
        </div>

      <div id="espemodi" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione la especialidad a modificar : 
            <select style="width: auto; " name="especialidadmod" id="especialidadmod">    {html_options options=$especialidades} </select>
        </div>

        <div id="divvisitadores" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione el visitador a consultar : 
            <select style="width: auto; " name="visitadoroption" id="visitadoroption">    {html_options options=$visitadoresmedicos} </select>
        </div>

      <div style="width: 100%; float: left;"> 
            <input type="hidden" id="step_opc" name="step_opc" value="1">
            <center><input type="submit" name="submitadmin_medicos" id="submitadmin_medicos" value="Continuar" class="button" /></center>
      </div>



    </fieldset>
</form>


</div>
                
                