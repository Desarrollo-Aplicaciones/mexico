<link media="all" type="text/css" rel="stylesheet" href="../js/jquery/ui/themes/base/jquery.ui.theme.css"></link>
<link media="all" type="text/css" rel="stylesheet" href="../js/jquery/ui/themes/base/jquery.ui.datepicker.css"></link>
<link media="all" type="text/css" rel="stylesheet" href="../js/jquery/ui/themes/base/jquery.ui.core.css"></link>
<script src="../js/jquery/ui/jquery.ui.core.min.js" type="text/javascript"></script>
<script src="../js/jquery/ui/jquery.ui.datepicker.min.js" type="text/javascript"></script>
<script src="../js/jquery/ui/i18n/jquery.ui.datepicker-es.js" type="text/javascript"></script>


<style type="text/css">
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
        $(function() {
            $(".datepicker").datepicker({
                prevText: '',
                nextText: '',
                dateFormat: 'yy-mm-dd'
            });

            $('"input[name=opc_ini]:radio"').change(function() {
                if(this.value != "infencu") {
                    $( "#forminfencu" ).hide();
                } else { 
                    $('#forminfencu').show( "slow" );
                }
            });
        });
    </script>
{/literal}


<div>
    <form action="{$current}&token={$token}&configure=reportesflmarketing" id="target"  method="post">
        <fieldset>
            <legend>{$reportesLogo}</legend>


            <p>Seleccione la opción deseada:</p>
            <div class="opciones"> <input type="radio" name="opc_ini" value="infencu"> Consultar Calificaciones del Servicio</div>
            <div class="opciones"> <input type="radio" name="opc_ini" value="infseo"> Consultar Reporte SEO</div>


            {* FORM infencu *}
            <div id="forminfencu" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione el rango de fechas &nbsp;&nbsp;&nbsp; 
                Inicio: <input id="infencu_ini" class="datepicker" type="text" style="width:70px" value="" name="infencu_ini">
                Fin: <input id="infencu_fin" class="datepicker" type="text" style="width:70px" value="" name="infencu_fin">
                <div id="vouchers_err" class="warn" style="display: none;"></div>
            </div>


            <div style="width: 100%; float: left;"> 
                <input type="hidden" id="step_opc" name="step_opc" value="1">
                <center><input type="submit" name="executeconsul" id="executeconsul" value="Continuar" class="button" /></center>
            </div>
        </fieldset>
    </form>
</div>