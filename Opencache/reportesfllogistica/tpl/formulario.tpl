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
                if(this.value != "repcata") { 
                    if(this.value != "consped") {
                        $( "#pedid_date" ).hide();
                    } else { 
                        $('#pedid_date').show( "slow" );
                    }

                    if(this.value != "icrsumi") {       
                        $( "#orden_sumi" ).hide();
                        $( "#pedid_date2" ).hide();
                    } else { 
                        $('#orden_sumi').show( "slow" );
                        $( "#pedid_date2" ).show( "slow" );
                    }
                }
            });
        });
    </script>
{/literal}


<div>
    <form action="{$current}&token={$token}&configure=reportesfllogistica" id="target"  method="post">
        <fieldset>
            <legend>{$reportesLogo}</legend>


            <p>Seleccione la opción deseada:</p>
            <div class="opciones"> <input type="radio" name="opc_ini" value="consped"> Consultar pedidos </div>
            <div class="opciones"> <input type="radio" name="opc_ini" value="icrsumi"> Consultar ICR's de Entrada (Ordenes Suministro)</div>
            <div class="opciones"> <input type="radio" name="opc_ini" value="repcata"> Reporte Catálogo </div>
            

            <div id="pedid_date" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione el rango de fechas &nbsp;&nbsp;&nbsp; 
                Inicio: <input id="consped_f_ini" class="datepicker" type="text" style="width:70px" value="" name="consped_f_ini">
                Fin: <input id="consped_f_fin" class="datepicker" type="text" style="width:70px" value="" name="consped_f_fin">
                <div id="vouchers_err" class="warn" style="display: none;"></div>
            </div>


            <div id="orden_sumi" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione el orden del reporte: &nbsp;&nbsp;&nbsp; 
                 <select name="ord_consul">
                    <option value="orden"> -- Seleccione -- </option>
                    <option value="referencia"> Referencia </option>
                    <option value="nombre"> Nombre Producto </option>
                    <option value="icr"> ICR </option>
                    <option value="bodega"> Bodega </option>
                </select>

                <div id="vouchers_err" class="warn" style="display: none;"></div>
            </div>


            <div id="pedid_date2" style="display: none; float: left; width: 100%; margin: 20px;">  Seleccione el rango de fechas &nbsp;&nbsp;&nbsp; 
                Inicio: <input id="consped_f_ini2" class="datepicker" type="text" style="width:70px" value="" name="consped_f_ini2">
                Fin: <input id="consped_f_fin2" class="datepicker" type="text" style="width:70px" value="" name="consped_f_fin2">
                <div id="vouchers_err" class="warn" style="display: none;"></div>
            </div>


            <div style="width: 100%; float: left;"> 
                <input type="hidden" id="step_opc" name="step_opc" value="1">
                <center><input type="submit" name="executeconsul" id="executeconsul" value="Continuar" class="button" /></center>
            </div>
        </fieldset>
    </form>
</div>