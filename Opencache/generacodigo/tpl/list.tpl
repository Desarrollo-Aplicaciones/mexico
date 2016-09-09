<div>
  <p><b>{l s='Gestión de códigos ICR' mod='generacodigo'}</b></p>
  <fieldset style="display:inline;">
    <legend>
      <img src="{$module_template_dir}icon/list.gif" alt="" title="" />{l s='Reporte de Códigos' mod='generacodigo'}
    </legend>
    <div>Seleccione un movimiento para obtener mas informacion &nbsp; <a href='' class="button">Cancelar</a></div>
    <form action="{$module_dir}export.php" enctype="multipart/form-data" method="post" name='formularioReporte'>
      <div class="list">
        <table>
          <tr><th>&nbsp;</th><th>Fecha del movimiento</th><th>Responsable</th><th>Rango de códigos</th><th>Cantidad creada</th></tr>
          {foreach key=key item=item from=$listado}
          <tr>
            <td><input type='radio' name='generacion' id='generacion' value = "{$item['id_icr']}"></td>
            <td>{$item['fecha']}</td>
            <td>{$item['firstname']} {$item['lastname']}</td>
            <td>{$item['cod_inicio']} - {$item['cod_final']}</td>
            <td>{$item['cantidad']}</td>
          </tr>
          {/foreach}
        </table>
      </div>
      <center><input type='submit' name = 'generareporte' id = 'generareporte' value='Generar Reporte' class="button"></center>
    </form>
  </fieldset>
</div>
<style type="text/css" >
  .list{
    margin:10px;
    height:330px;
    overflow-y:scroll;
  }
  th{
    font-weight: 600;
    padding:5px 15px;
    text-align: center;
  }
  td{
    padding:5px 15px;
    text-align: center;
  }
  tr:nth-child(even){
    background: #FFFFFF;
  }
</style>