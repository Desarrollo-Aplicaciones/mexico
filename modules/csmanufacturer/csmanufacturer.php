<?php
if (!defined('_PS_VERSION_'))
  exit;

require_once(_PS_ROOT_DIR_.'/modules/csmanufacturer/laboratorios.php'); //clase que se encarga de procesar el archivo cargado



class csmanufacturer extends Module
{

  var $codigo;

  function __construct()
  {
    $this->name = 'csmanufacturer';
    $this->tab = 'Test';
    $this->version = '1.0';
    $this->author = 'Farmalisto';

    parent::__construct();

    $this->displayName = $this->l('CS Slider of Manufacturer Logo');
    $this->description = $this->l('Adds Slider of Manufacturer Logo.');
  }

  function install()
  {
    

        $this->_clearCache('csmanufacturer.tpl');
        Configuration::updateValue('HOME_FEATURED_NBR', 8);

        if (!parent::install()) {   
            return false;
        }

        return true;
  }

  public function displayForm() //mostrar como html en la opción de configuración del módulo
  {



echo "<script>

function numbersonly(myfield, e, dec)
{
var key;
var keychar;

if (window.event)
key = window.event.keyCode;
else if (e)
key = e.which;
else
return true;
keychar = String.fromCharCode(key);

// control keys
if ((key==null) || (key==0) || (key==8) ||
(key==9) || (key==13) || (key==27) )
return true;

// numbers
else if ((('0123456789,').indexOf(keychar) > -1))
return true;

// decimal point jump
else if (dec && (keychar == ','))
{
myfield.form.elements[dec].focus();
return false;
}
else
return false;
}

</script>";

    $output= '
    <form name="generar" action= "'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post">
    <fieldset style="with:48%;float:left;"><legend><img src="'.$this->_path.'logo.gif" style="width: 50%;height: 6%;vertical-align: middle;" alt="codigo de barras" title="" />'.$this->l('Configuración').'</legend>


    <p>Con este modulo usted podrá escoger el logo de los diferentes laboratorios que tenemos.</p>


    <p>Estos son los ids que se encuentran en el instante</p>


    <p><input type="text" placeholder="1,3,5,6" name="codigos" onKeyPress="return numbersonly(this, event)"></p>


    <center><input type="submit" name="submitUpdateCod" id="botonValida" value="Cargar laboratorios" class="button"/></center>
    </fieldset>
    </form>';

    

    $obj = new laboratorios();
    $obj ->conectardb();
    $Resultado=$obj ->mostrar();

    if($Resultado){

      $report = "";


       foreach ($Resultado as $line) {

        $variablePasar = $line['listado'];
        $report = $report." <tr><td style='color: #585A69;'>".$line['listado']."</td><td style='color: #585A69;'>".$line['name']."</td></tr>";
      }



      $resultadoFormulario = $output.'<fieldset style="margin-top: 40px;">
      <table border="1" align="left" cellpadding="2" cellspacing="0" bordercolor="#CCCCCC">
      <tr>
      <td colspan="2" style="color: #585A69;">Este es el listado de laboratorios que se encuentran habilitados en el momento.</td>
      </tr>
      <tr>
      <td style="color: #585A69;">ID</td>
      <td style="color: #585A69;">Nombre laboratorios</td>

      </tr>
      '.$report.'</table><p></p></fieldset>
      <center style="float:left;color: red;width:100%;font-size: 19px;font:700 12pt/1.25 "Open Sans",Arial, Verdana, sans-serif;"><a href="javascript:history.go(-1);" style="color: #585A69;">Volver</a></center>';
      return $resultadoFormulario;

    }
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
  
 if ($_POST['codigos'] == "") 
     {  
       echo '<script language="javascript">alert("Tiene que ingresar un dato en el campo de busqueda");</script>'; 
     } 
     else {
        

        $obj = new laboratorios();
        $obj->conectardb();
      $obj->cargar();

     
  }
}
  return $output.$this->displayForm();

  }



  function hookFooter($params)
  {

$manufacturers_sel =  array();
     $query="SELECT listado FROM ps_cs_manufacturer LIMIT 10";




     if ($results = Db::getInstance()->ExecuteS($query)){
      

      foreach ($results as $total) {
        //print_r($total);# code...

        $manufacturers_sel[] = $total['listado'];  
       }

       } 




    global $smarty;
    $manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, $manufacturers_sel);

    $smarty->assign(array(
      'manufacs' => $manufacturers,
      'ps_manu_img_dir' => _PS_MANU_IMG_DIR_
    ));
    return $this->display(__FILE__, 'csmanufacturer.tpl');
  }
  
  function hookHeader($params)
  {
    $this->context->controller->addCss($this->_path.'css/csmanufacturer.css', 'all');
  }

  function hookDisplayHome($params) {

     $manufacturers_sel =  array();
     $query="SELECT listado FROM ps_cs_manufacturer LIMIT 10";


     if ($results = Db::getInstance()->ExecuteS($query)){
      

      foreach ($results as $total) {
        //print_r($total);# code...

        $manufacturers_sel[] = $total['listado'];  
       }

       } 

    global $smarty;
    $manufacturers = Manufacturer::getManufacturers(false, 0, true, false, false, false, $manufacturers_sel);

    $smarty->assign(array(
      'manufacs' => $manufacturers,
      'ps_manu_img_dir' => _PS_MANU_IMG_DIR_
    ));
    return $this->display(__FILE__, 'csmanufacturer.tpl');
  }

}


