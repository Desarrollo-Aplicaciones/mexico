{include file=$pathModule|cat:"/tpl/formulario.tpl"}


<p> <h3>Resultado de búsqueda, productos en lista negra: <b>{$totalrows}</b>, con referencia: <b>{$reference_prod}</b></h3> </p>

<table border="0">
                                      <thead>
                                          <tr>
                                              
                                              <th class="celdahead">Referencia</th>
                                              <th class="celdahead">Nombre producto</th>
                                              <th class="celdahead">Nombre emp</th>
                                              <th class="celdahead">Apellido</th>
                                              <th class="celdahead">Fecha</th>
                                              <th class="celdahead">Habilitar</th>
                                              <th class="celdahead">Ver descripcíon</th>
                                              <!-- <th class="celdahead">Editar</th> -->
                                             
                                     
                                          </tr>
                                      </thead>
                                      <tbody>
                                          
                                                                                   
    {foreach name=outer item=product from=$productsblock}
  <tr>
      <td class="celda">{$product.reference}</td><td class="celda">{$product.name}</td> <td class="celda">{$product.firstname}</td><td class="celda">{$product.lastname}</td> <td class="celda">{$product.date}</td>
      <td class="celda"><a href="{$current}&token={$token}&configure=prodblacklist&limit_page={$select_rows}&reference_prod={$product.reference}&option=enabled" title="Pagina">Habilitar</a></td>
      <td class="celda"><a href="#" title="Pagina" onclick="fancyMsgBox('{$product.descripcion}');">Ver</a></td>
     <!-- <td class="celda"><a href="{$current}&token={$token}&configure=prodblacklist&limit_page={$select_rows}&reference_prod={$product.reference}&option=edit" title="Pagina">Editar</a></td>  -->

    </tr>
{/foreach}
       </tbody>
       </table>