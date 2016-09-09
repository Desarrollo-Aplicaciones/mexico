{include file=$pathModule|cat:"/tpl/formulario.tpl"}
<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
<script src="../js/jquery/jquery.validate.js" type="text/javascript"></script>
<script src="../js/jquery/jquery.form.js"></script>

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

    #mitabla th,#mitabla td{
font:13px Arial, Helvetica, sans-serif;
color:#333333;
border:none;
padding:1px 1px;
border-bottom:1px solid #e1e1e1;
text-align: left; width:40%;
 
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

.prodintform {
    float: left; margin: 5px; padding: 6px; background-color: #f0f0f0;
    width: auto;
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
        $("#bar").width('0%');
        $("#message").html("");
        $("#percent").html("0%");
    },
    uploadProgress: function(event, position, total, percentComplete) 
    {
        $("#bar").width(percentComplete+'%');
        $("#percent").html(percentComplete+'%');
 
    },
    success: function() 
    {
        $("#bar").width('100%');
        $("#percent").html('100%');
 
    },
    complete: function(response) 
    {
        $("#message").html("<font color='green'>"+response.responseText+"</font>");
    },
    error: function()
    {
        $("#message").html("<font color='red'> ERROR: unable to upload files</font>");
 
    }
 
}; 

{/literal} 

 {foreach from=$info_pautas key=campo item=arr_item}
  $("#myForm{$campo}").ajaxForm(options);
 {/foreach}

{literal}
    });
</script>
{/literal}


<div>		

<form name="formpublicidad" action="" id="formpublicidad"  method="post">
    <fieldset><legend>{$pagesel}</legend>

        <table id="mitabla">
        {foreach from=$info_hook key=campo item=arr_item}
            
                <tr><td>Página: </td><td style="font-weight: bold; ">{$arr_item['nombre']}</td></tr>
                <tr><td>Ubicación: </td><td style="font-weight: bold; ">{$arr_item['ubicacion']}</td></tr>
                <tr><td>Tipo de publicidad a mostrar: </td><td><select name="tipo" id="tipo"> {html_options options=$tipo_list selected=$arr_item['tipo']} </select>  </td></tr>
                <tr><td>Activo: </td><td><select name="activo" id="activo"> {html_options options=$activo_list selected=$arr_item['activo']} </select> </td></tr>
                <tr><td>Alto: </td><td>{$arr_item['alto']} px</td></tr>
                <tr><td>Ancho: </td><td>{$arr_item['ancho']} px</td></tr>

            {assign var=altoimg value=$arr_item['alto']}
            {assign var=anchoimg value=$arr_item['ancho']}
            {assign var=idhook value=$campo}
        {/foreach}
        <tr><td style="text-align:center" colspan="2">
        <input type="hidden" name="hook" id="hook" value="{$idhook}">
        <input type="submit" name="submit" value="Cambiar tipo de publicidad">
        </td></tr></table>
    </fieldset>
</form>

<div class="toolbar">
    <div id="progress">
        <div id="bar"></div>
        <div id="percent">0%</div >
    </div>
    <div id="message"></div>
</div>

<div id="listadopublicidad">
    {foreach from=$info_pautas key=campo item=arr_item}
        <form id="myForm{$campo}" action="../modules/cspublicidadfl/upload.php" method="post" enctype="multipart/form-data">
            <div id="publicidad{$campo}" class="prodintform">
            <table id="mitabla" style="border: solid 1px #bdbdbd; width: 550px;">                
                    <tr><td>Id_publicidad : </td><td style="font-weight: bold; "><input type="hidden" name="id_publicidad" id="id_publicidad" value="{$campo}"> # {$campo}</td><td rowspan="5" style="text-align:center">{if $arr_item['tipo'] == 'banner'} <img src="{$modules_dir}../img/imagen.php?imagen={$arr_item['imagen']}" width="190px" height="150px" style="margin-bottom: 2px;"> {/if}<td></tr>
                    <tr><td>Pagina : </td><td>{$arr_item['pagina']}</td></tr>
                    <tr><td>Ubicacion : </td><td>{$arr_item['ubicacion']}</td></tr>

                    {if isset($categoria_cargada)}
                        <tr><td>Categoría : </td><td>{$categoria_cargada}</td></tr>
                        <tr><td>Tipo : </td><td><select name="tipo" id="tipo"> <option value="{$arr_item['tipo']}">{$arr_item['tipo']}</option> </select> </td></tr>
                        <tr><td>Imagen : </td><td colspan="2">{$arr_item['imagen']}</td></tr><input type="hidden" name="link" id="link" value="{$arr_item['link']}">
                        <tr><td>Archivo : </td><td colspan="2"><input type="file" size="60" name="myfile"></td></tr>
                    {else}
                        <tr><td>Tipo : </td><td><select name="tipo" id="tipo"> {html_options options=$tipo_listp selected=$arr_item['tipo']} </select> </td></tr>
                        <tr><td>Link : </td><td><input type="text" name="link" id="link" value="{$arr_item['link']}"></td></tr>
                        <tr><td>Imagen : </td><td colspan="2">{$arr_item['imagen']}</td></tr>
                        <tr><td>Archivo : </td><td colspan="2"><input type="file" size="60" name="myfile"></td></tr>
                        <tr><td>Adsense : </td><td colspan="2"><textarea name="adsense" id="adsense" cols="80" rows="3">{$arr_item['adsense']}</textarea></td></tr>
                    {/if}

                    
                    <tr><td>Activo : </td><td colspan="2"><select name="activo" id="activo"> {html_options options=$activo_list selected=$arr_item['activo']} </select></td></tr>
                    
                    

            <tr><td style="text-align:center" colspan="3">
<input type="hidden" name="altoimg" id="altoimg" value="{$altoimg}">
<input type="hidden" name="anchoimg" id="anchoimg" value="{$anchoimg}">
            <input type="submit" name="submit" value="Cambiar publicidad">
        </td></tr></table>
            </div>
        </form>  
    {/foreach}

</div>



</div>
                
                