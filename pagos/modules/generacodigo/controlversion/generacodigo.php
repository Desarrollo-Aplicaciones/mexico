<?php

require_once(_PS_ROOT_DIR_.'/modules/generacodigo/GenerandoIcr.php'); //clase que se encarga de procesar el archivo cargado


//comprobación de una constante PHP
if ( !defined( '_PS_VERSION_' ) )
  exit;

class generacodigo extends Module
{
  public function __construct()
  {
    $this->name = 'generacodigo';
    $this->tab = 'Test';
    $this->version = 1.0;
    $this->author = 'Farmalisto';
    $this->need_instance = 0;

    parent::__construct();

    $this->displayName = $this->l( 'Generar Codigo ICR' );
    $this->description = $this->l( 'Este codigo genera el cod de barras.' );
  }

  public function install()
  {

   if (!$id_tab = Tab::getIdFromClassName('generacod'))  // para crear acceso en menu back office / clase creada
   {
      $tab = new Tab();
    $tab->class_name = 'generacod';    //la clase que redirecciona el link del menu a la configuracion
    $tab->module = 'generacodigo'; // nombre del modulo creado
    $tab->id_parent = (int)Tab::getIdFromClassName('AdminStock'); //aparecerá al final del menú catalogo
    foreach (Language::getLanguages(false) as $lang)
    $tab->name[(int)$lang['id_lang']] = 'Creación codigos ICR'; // texto a mostrar en el menu
  if (!$tab->save())
    return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
}

$this->_clearCache('generacodigo.tpl');
Configuration::updateValue('HOME_FEATURED_NBR', 8);

if (!parent::install()) { 
  return false;
}

return true;

}

    public function displayForm() //mostrar como html en la opción de configuración del módulo
    {

    $impuestos = TaxRulesGroup::getTaxRulesGroups(true); //listado de impuestos actuales vigentes
    $imp_show = '';
    foreach ($impuestos as $key => $value) {  // string con listado de impuestos a productos
      $imp_show .= '
      <tr>
      <td style="text-align:center; font-size: 0.8em;"> '.$value['id_tax_rules_group'].' </td><td style="text-align:center; font-size: 0.8em;"> '.$value['name'].' </td>
      </tr>';           
    }

    //Funciónes del sistema
    echo "
    <script type='text/javascript' src='//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>
    <script type='text/javascript'>
    $(document).ready(function()
    {
      $('#botonValida').click(function () {  
       var variableValida = ($('input:radio[name=botonGrupo]:checked').val());
       $('#generar').submit();
     });


    $( '#cambiarEstado' ).click(function() {
      alert( 'Handler for .click() called.' );
    });

    });

function validaRadio(){

  opciones = document.getElementsByName('generacion');

  var seleccionado = false;
  for(var i=0; i<opciones.length; i++) {    
    if(opciones[i].checked) {
      seleccionado = true;    
      break;
    }
  }

  if(!seleccionado) {
    return false;
  }

}


function ValidaSoloNumeros(event) {
  var code =event.charCode || event.keyCode;
  if ((code< 48) || (code> 57)){
    if(window.event){
      event.returnValue = false;
    }else{
      event.preventDefault();
    }

  }
}



function ValidaLetrasNumeros(event) {
  var code =event.charCode || event.keyCode;
  if ((code< 48) || (code> 57) && (code< 65) || (code> 90) && (code< 97) || (code> 122)){
    if(window.event){
      event.returnValue = false;
    }else{
      event.preventDefault();
    }


  }
}

</script>";



$output = ' <p></p>
<form name="generar" action= "'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post">
<fieldset><legend><img src="'.$this->_path.'logo.gif" style="width: 50%;height: 6%;vertical-align: middle;" alt="codigo de barras" title="" />'.$this->l('Configuración').'</legend>


<p>Con este modulo usted podrá generar codigos ICR a los productos que existen fisicamente en bodega.</p>
<p><input type="radio" name="botonGrupo" id="botonesGrupo1" value="generarCodigo" onkeypress="ValidaSoloNumeros(event)" checked>Generar: Ingresar codigos ICR a los productos que existen en bodega.<p/>
<p>
<label>Cantidad</label>

<input name="cantidad" type="text" onkeypress="ValidaSoloNumeros(event)"/>     </p>

<p><input type="radio" name="botonGrupo" id="botonesGrupo2" value="buscarCodigo" >Buscar: Ingresar codigo ICR.<p/>

<p>
<label>Buscar</label>
<input name="buscar" type="text" onkeypress="ValidaLetrasNumeros(event)" id="buscar">     </p>


<p><input type="radio" name="botonGrupo" id="botonesGrupo3" value="buscarReporte" >Ir hacia el reporte de codigos ICR.<p/>
</div>

<center><input type="submit" name="submitUpdateCod" id="botonValida" value="Cargar Codigos" class="button"/></center>
</fieldset>
</form>';

return $output;
}

  public function getContent()   // contenido a mostrar y cargar en la opción configuración de la clase
  {

 //    echo "<pre>";
 // print_r($_POST);
 // echo "</pre>";

    $output = '<h2>'.$this->displayName.'</h2>';
    if (Tools::isSubmit('submitUpdateCod'))
    {  

     if( $_POST['botonGrupo'] == "generarCodigo"){          
      if ($_POST['cantidad'] == "") 
      { 

        echo '<script language="javascript">alert("Tiene que ingresar un dato en el campo de generación de codigo ICR");</script>';

      } 
      else {

        $obj = new GenerandoIcr();
        $obj -> conectardb();
        $obj -> campostabla();
        $obj -> generandocodigo();
        $obj -> generandoexcel();
        $obj -> fisicoExcel();

             // echo $obj ->codigo_final;

      }
    }
    if( $_POST['botonGrupo'] == "buscarCodigo"){   
     if ($_POST['buscar'] == "") 
     {  
       echo '<script language="javascript">alert("Tiene que ingresar un dato en el campo de busqueda");</script>'; 
     } 
     else {
      $obj = new GenerandoIcr();
      $obj -> conectardb();
      $obj ->traerParametro($obj ->encontro);
      $obj ->traerId($id_icr);
      $obj ->traerEstado($obj ->id_estado_icr);


      //echo $_POST['buscar']."<br> este es mi select ".$obj ->traerParametro($obj ->encontro);

      if( $_POST['buscar'] == $obj ->traerParametro($obj ->encontro) ){
         //echo "aca ando yo!!! ";
     // $campo = $obj ->traerParametro($obj ->estado);
       $id = $obj ->traerId($obj ->id_icr);
     //echo "<br>sera ".$id = $obj ->traerEstado($obj ->id_estado_icr);




       $formulariOculto =  "<form action='".Tools::safeOutput($_SERVER['REQUEST_URI'])."' enctype='multipart/form-data' method='post' name='cambiarEstado2'>
       <fieldset>
       <legend>
       <img src='".$this->_path."logo.gif' style='width: 50%;height: 6%;vertical-align: middle;' alt='codigo de barras' />".$this->l('Configuración')."</legend>
       <label>Este formulario sirve para cambiar el estado del ICR del codigo '".$obj ->traerParametro($obj ->encontro)."' de activo a anulado.</label><input type='hidden' name='cambiarEstado' id='cambiarEstado' value='".$id."'>&nbsp;Actualizar&nbsp;<a href=''>Volver</a><br><input type='submit' id='submita' name= 'submita' value='Anular'></label>
       </fieldset></form>";



       return $formulariOculto;


     }
     else{
      echo '<script language="javascript">alert("Resultado incorrecto");</script>'; 
    }

  } 
}

if( $_POST['botonGrupo'] == "buscarReporte"){  

 $obj = new GenerandoIcr();
 $obj ->conectardb();
 $Resultado=$obj ->reporteIcr($col_value);

  


 if($Resultado != null){

$report = "";


while ($line = mysql_fetch_array($Resultado, MYSQL_ASSOC)) {

    $variablePasar = $line['id_icr'];
    $report = $report." <tr><td><input type='radio' name='generacion' id='generacion' value = '".$variablePasar."'></td><td>".$line['id_icr']."</td><td>".$line['cod_inicio']."</td><td>".$line['cod_final']."</td><td>".$line['firstname']."</td><td>".$line['lastname']."</td><td>".$line['fecha']."</td><td>".$line['cantidad']."</td></tr>";
 }

   $resultadoFormulario = "<form action='../modules/generacodigo/prueba.php' enctype='multipart/form-data' method='post' name='formularioReporte'>
  <fieldset>
  <legend>
  <img src='".$this->_path."logo.gif' style='width: 50%;height: 6%;vertical-align: middle;' alt='codigo de barras' />".$this->l('Configuración')."</legend>
  <table  width='700' border='1' align='left' cellpadding='2' cellspacing='0' bordercolor='#CCCCCC'>
  <tr>
  <td>GR</td>
  <td>id</td>
  <td>Codigo inicial</td>
  <td>Codigo final</td>
  <td>Nombre Creador</td>
  <td>Apellido Creador</td>
  <td>Fecha en que se creo</td>
  <td>Cantidad</td>
  </tr>
   ".$report."</table>

 <input type='submit' name = 'generareporte' id = 'generareporte' value='codigo'>
 <a href=''>Volver</a><br>
 </fieldset>
 </form>";
 return $resultadoFormulario;

}else{
  echo "mal";
}
}

}



if(isset($_POST['submita']) || isset($_POST['cambiarEstado'])){
      //echo $_POST['cambiarEstado']."este es cambiar estado";
  $obj = new GenerandoIcr();
  $obj -> conectardb();
  $obj ->actualizarICR($obj->id);
}else{
          // echo '<br>No hay nada<br>';
}
return $output.$this->displayForm();
}

public function uninstall()
{
  if ( !parent::uninstall() )
    Db::getInstance()->Execute( 'DELETE FROM `' . _DB_PREFIX_ . 'mymodule`' );
  parent::uninstall();
}

public function hookLeftColumn( $params )
{
  global $smarty;
  return $this->display( __FILE__, 'generacodigo.tpl' );
}

public function hookRightColumn( $params )
{
  return $this->hookLeftColumn( $params );
} 

}

?>