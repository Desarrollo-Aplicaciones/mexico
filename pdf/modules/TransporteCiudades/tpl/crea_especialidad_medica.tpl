{include file=$pathModule|cat:"/tpl/formulario.tpl"}

<style type="text/css" >
.creacuponini {
     background-color: #ebedf4;
    border: 1px solid #ccced7;
    color: #585a69;
    font-size: 1.1em;
    margin: 0;
    padding: 1em;
}    
.savedatadoc {
 float: left;
 margin: 3px;
 max-width: 30%;
 min-width: 235px;
 width: 30%;
 min-height: 42px;
 height: auto;
}

.savedatfull {
 float: left;
 margin: 3px;
 max-width: 90%;
 width: 90%;
 height: 100px;

}

.camposave {
 text-transform: capitalize;
 font-size: 12px;
 float: left;
 margin: 2px;
 width: 30%;
 height: 15px;
}

.camposavedata {
 text-transform: capitalize;
 font-size: 10px;
 float: left;
 margin: 2px;
 width: 60%;
 min-height: 15px;
 height: auto;
}

/*.camposavedatasel {
    background: url("../modules/adminmedicos/img/arrow_down.gif") no-repeat right;
    width: 50% !important;
}*/


label.error {
    background: none !important;
    border-bottom: 1px solid #99182c !important;
    border-right: 1px solid #99182c !important;
    border-top: 1px solid #99182c !important;
    color: red !important;
    display: block !important;
    font-size: 100% !important;
    line-height: 11px !important;
    padding: 1px 2px !important;
    width: auto !important;
   
}

#content .error {    
    background: none !important;
    min-height: 10px !important;
    height: 16px !important;
    padding: 1px !important;
    margin: 0px !important;
    font-size: 11px !important;
    line-height: 11px !important;
}

select .error {    
    background: none !important;
    min-height: 10px !important;
    height: 16px !important;
    padding: 1px !important;
    margin: 0px !important;
    font-size: 10px !important;
    padding-top: 0px !important;

}


label:after {
    display: none !important;
}

/*
.camposavedata select {
  
   background: transparent;
   width: 160px !important;
   font-size:8pt;
   color:grey;
   border: 0;
   border-radius: 0;
   height: 17px;
   -webkit-appearance: none;
   float: left;
   
   }
   .camposavedata select:focus {
    outline: none;

}*/

.camposavedatasel select {
width:160px !important;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    padding: 2px 2px 2px 2px;
    border: none;
    background: transparent url("../modules/adminmedicos/img/arrow_down.gif") 150px 5px no-repeat !important;
    text-indent: 9px;
    text-overflow: '';
}

#especialm {
   height: 100px !important;
   
}

</style>
{literal}
<script type="text/javascript">
$(document).ready(function() {
    

    $("#visidata").validate({


                  wrapper: 'span',
            errorPlacement: function (error, element) {
                error.css({'padding-left':'10px','margin-right':'20px','padding-bottom':'2px'});
                error.addClass("arrow")
                error.insertAfter(element);
            },

                    rules: {
{/literal}

{foreach from=$inputs key=campo item=arr_item}
    {if $arr_item['requerido'] == "si"} 
'{$campo}' : { required: true,  {if $arr_item['extra'] != '' } {$arr_item['extra']} {/if} },
    {elseif $arr_item['requerido'] == "depende" }
'{$campo}' : { required: {
                        depends: function(element) {
                            if ($("#{$arr_item['campo_dependencia']}").val() == "{$arr_item['valor_dependencia']}") {
                             return true;
                            } else {
                             return false;
                            }
                        }
                    }
            },
            {/if}
{/foreach}
            },
            messages: {
{foreach from=$inputs key=campo item=arr_item}
    {if $arr_item['requerido'] == "si"}
'{$campo}' : { required: "{$arr_item['requeridomensaje']}",
            {if $arr_item['extra'] != '' and $arr_item['extramensaje'] != '' } {$arr_item['extramensaje']} {/if}
},
{/if}
{/foreach}
            },
{literal}      
        });
    });
    </script>
    {/literal}
<br>

<fieldset class="creacuponini">
<p style="text-align: center; font-weight: bold; font-size: 2em;"> Crear Especialidad Médica </p> 

    <form name="visidata" id="visidata" action="" method="post">        
{foreach from=$inputs key=campo item=arr_item}
    {if $arr_item['tamano'] == "small"}
        <div class="savedatadoc"> 
            <div class="camposave"> {$arr_item['nombre']} : </div>        
        {if $arr_item['tipo'] == "select"}
                <div class="camposavedata camposavedatasel"> <select style="width: 95%" name="{$arr_item['campo']}" id="{$arr_item['campo']}"> {html_options options=${$arr_item['lista_select']}} </select> 
        {elseif $arr_item['tipo'] == "selectm"}
                <div class="camposavedata"> <select style="width: 95%" name="{$arr_item['campo']}[]" id="{$arr_item['campo']}" multiple> {html_options options=${$arr_item['lista_select']}} </select>
        {elseif $arr_item['tipo'] == "textarea"}
                <div class="camposavedata"> <textarea style="width: 95%" name="{$arr_item['campo']}" id="{$arr_item['campo']}" rows="5"> </textarea>
        {elseif $arr_item['tipo'] == "text"}
                <div class="camposavedata"> <input style="width: 95%"  name="{$arr_item['campo']}" id="{$arr_item['campo']}" type="text" value="">
        {/if}
            </div>
        </div>
    {elseif $arr_item['tamano'] == "big"}
        <div class="savedatfull">
            <div class="camposave"> {$arr_item['nombre']} : </div>
        {if $arr_item['tipo'] == "select"}
                <div class="camposavefull camposavedatasel"> <select style="width: 95%" name="{$arr_item['campo']}" id="{$arr_item['campo']}"> {html_options options=${$arr_item['lista_select']}} </select> 
        {elseif $arr_item['tipo'] == "selectm"}
                <div class="camposavefull"> <select style="width: 95%" name="{$arr_item['campo']}[]" id="{$arr_item['campo']}" multiple> {html_options options=${$arr_item['lista_select']}} </select>
        {elseif $arr_item['tipo'] == "textarea"}
                <div class="camposavefull"> <textarea style="width: 95%" name="{$arr_item['campo']}" id="{$arr_item['campo']}" cols="60" rows="5"> </textarea>
        {elseif $arr_item['tipo'] == "text"}
                <div class="camposavefull"> <input style="width: 95%"  name="{$arr_item['campo']}" id="{$arr_item['campo']}" type="text" value="">
        {/if}
            </div>
        </div>
    {/if}
{/foreach}
        <input type="hidden" name="opc_ini" value="creaesp">
        <div style="float: left; margin: 3px; margin-top: 60px; text-align: center; width: 100%;"><input type="submit" value="Guardar" name="savenewcreaesp"></div>
    </form>
</fieldset>