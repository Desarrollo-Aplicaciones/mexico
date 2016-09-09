{*$base_dir}
{$prodblacklistLogo}
{$prodblacklistPath}
{$prodblacklistMsg*}

<style type="text/css" >
    
      .celdahead{
    padding: 0 0 0 5px;
    background-color:   #ffd573;  
    
    }
    .celda{
    padding: 0 0 0 5px;
    background-color:   #e9f1f6;  
    
    }
</style>

<script type="text/javascript">
    
    $(function(){
    $('#limit_page').change(function(){
    $( "#target" ).submit();
    });
    
    });
</script>


<div>		
<p><b>{$prodblacklistMsg}</b></p>
<form action="{$current}&token={$token}&configure=prodblacklist" id="target"  method="post">
<fieldset><legend> <a href="{$current}&token={$token}&configure=prodblacklist" title="Productos en lista negra"><img src="{$prodblacklistPath}logo.gif" alt="" title="" /></a>Lista Negra</legend>
		
    <table border="0">
        
        <tbody>
            
                 <tr>
                <td><label for="_">Seleccione una Opción de búsqueda: </label></td>
                <td> <select id="option_search" name="option_search"><option value="search_black_list">Productos en lista negra</option>
                        <option value="search_product">Buscar para agregar a la lista negra</option></select> </td>
                <td></td>
                <td></td> 
                 </tr>
                                    
            <tr>
                <td><label for="reference_prod">Referencia del producto: </label></td>
                <td> <input id="reference_prod" name="reference_prod"></td>
                <td><input type="submit" name="submitProdBlackList" id="submitProdBlackList" value="Buscar referencia" class="button" /></td>
                <td></td>
            </tr>

             <tr>
                <td><label >Numero de registros por pagina: </label></td>
                <td> {$select_limit} </td>
                <td></td>
                <td></td> 
                 </tr>


           
        </tbody>
    </table>
    
   {* http://www.smarty.net/docsv2/es/language.function.foreach.tpl *}                                        

   
</fieldset>
</form>
 
   <div style="background: #eaebec">
   <form action="" id="target"  method="post">
       
       <table border="0">
           <thead>
               <tr>
                   <th>Desactivar productos en lista negra</th>
               </tr>
           </thead>
           <tbody>
               <tr>
                   <td> <input name="searhs_and_disable" type="submit" value="Buscar y desactivar"> </td>
               </tr>

           </tbody>
       </table>

   </form>
       </div>

</div>
                
                