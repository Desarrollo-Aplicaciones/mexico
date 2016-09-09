<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
require_once(_PS_ROOT_DIR_.'/classes/seveFileClass.php'); //clase que se encarga de procesar el archivo cargado

if (!defined('_PS_VERSION_'))
	exit;

class UpdatePrice extends Module
{
	private $_html = '';
	private $_postErrors = array();
    private $_msg='';

	function __construct()  //informacion del modulo
	{
		$this->name = 'updateprice';
		$this->tab = 'front_office_features';
		$this->version = '0.1 Alfa';
		$this->author = 'Farmalisto';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Actualizar precios de productos');
		$this->description = $this->l('Actualiza los precios de los profuctos desde un archivo (CSV)');

	}

	public function install() // parametros de instalación del modulo
	{
		if (!$id_tab = Tab::getIdFromClassName('AdminUpPrice'))  // para crear acceso en menu back office / clase creada
		{
		$tab = new Tab();
		$tab->class_name = 'AdminUpPrice';		//la clase que redirecciona el link del menu a la configuracion
		$tab->module = 'updateprice';	// nombre del modulo creado
		$tab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog'); //aparecerá al final del menú catalogo
		foreach (Language::getLanguages(false) as $lang)
		$tab->name[(int)$lang['id_lang']] = 'Actualizar precios'; // texto a mostrar en el menu
		if (!$tab->save())
		return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}

		$this->_clearCache('updateprice.tpl');
		Configuration::updateValue('HOME_FEATURED_NBR', 8);

		if (!parent::install()) {	
			return false;
		}

		return true;
	}
	
	public function uninstall()  // desinstalacion
	{
		$this->_clearCache('updateprice.tpl');
		return parent::uninstall();
	}

	public function getContent()   // contenido a mostrar y cargar en la opción configuración de la clase
	{
		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitUpdatePrice'))
		{
			                    
			/* validar subida de archivo */
			                      
			$allowedExts = array("txt", "csv" );
			  
			$temp = explode(".", $_FILES["fileupdateprice"]["name"]);


			$extension = end($temp);

			if ((($_FILES["fileupdateprice"]["type"] == "text/csv")
			|| ($_FILES["fileupdateprice"]["type"] == "text/plain")
			|| ($_FILES["fileupdateprice"]["type"] == "text/comma-separated-values")
			|| ($_FILES["fileupdateprice"]["type"] == "application/csv")
			|| ($_FILES["fileupdateprice"]["type"] == "application/excel")
			|| ($_FILES["fileupdateprice"]["type"] == "application/vnd.ms-excel")
			|| ($_FILES["fileupdateprice"]["type"] == "application/vnd.msexcel")
			|| ($_FILES["fileupdateprice"]["type"] == "application/octet-stream")        
			|| ($_FILES["fileupdateprice"]["type"] == "text/anytext"))
			        
			&& ($_FILES["fileupdateprice"]["size"] < (1024*5000))
			&& in_array($extension, $allowedExts)) {

			  	if ($_FILES["fileupdateprice"]["error"] > 0) {
			      
			  		$this->_msg="Error: " . $_FILES["fileupdateprice"]["error"];
			    } else {
			       
			    	$obj = new seveFileClass();			      
			     	$dataUser='_'.$this->context->cookie->id_employee.'_'.current(array_slice(explode("@",$this->context->employee->email),0,1));
				    $names = $obj->saveFile($_FILES, "fileupdateprice",$dataUser);
			    
			     	if (is_array($names) && $names[0] != '' && $names[0] != false ) {
			     		$actualiza_price = $obj->updatePrice($obj->pathFiles().$names[0]);
			     	}

			     	if ($actualiza_price == true) {
			     		$output .= $this->displayConfirmation($this->l('Se actualizaron los precios de los productos..'));
			     	} else {
			     	 	$output .= $this->displayConfirmation($this->l('Error actualizando los precios de los productos.'));
			     	}
			    }
			} else {
			 	$output .= $this->displayError( "Este tipo de archivo no es valido."); 
			}
		}
		return $output.$this->displayForm();
	}

	public function displayForm() //mostrar como html en la opción de configuración del módulo
	{

		$impuestos = TaxRulesGroup::getTaxRulesGroups(true); //listado de impuestos actuales vigentes
		$imp_show = '';
		foreach ($impuestos as $key => $value) {	// string con listado de impuestos a productos
				$imp_show .= '
				<tr>
				  <td style="text-align:center; font-size: 0.8em;"> '.$value['id_tax_rules_group'].' </td><td style="text-align:center; font-size: 0.8em;"> '.$value['name'].' </td>
				</tr>';						
		}
		
		$output = ' <p><b>'.$this->_msg.'</b></p>
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post">
		<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
		<p>Con este modulo usted podrá actualizar los precios de los productos, recuerde que el archivo CSV debe tener los siguientes campos  <a href="../modules/updateprice/formato.csv">(reference; id_supplier; supplier_reference; precio_compra; precio_venta; id_impuesto)</a>, estos <b>deben estar</b> en la cabecera del archivo. LOS PRECIOS DE LOS PRODUCTOS DEBEN SER ENTEROS (12500) O CON DECIMALES CUYO SEPARADOR SEA PUNTO (12500.560000) DE NO MAS DE 6 NUMEROS EN SU PARTE FRACCIONAL.</p>
		<br /> Tabla de impuestos admitida: <br />
		<table border="1">
		 <tr>
		  <td style="text-align:center; " > Id Impuesto </td><td style="text-align:center;"> Descripción </td>
		 </tr>
		 <tr>
		  <td style="text-align:center; font-size: 0.8em;"> 0 </td><td style="text-align:center; font-size: 0.8em;"> Sin impuestos </td>
		 </tr>'.$imp_show.'</table>
		 <br /><a href="index.php?controller=AdminSuppliers&exportsupplier&token='.Tools::getAdminToken('AdminSuppliers'.(int)Tab::getIdFromClassName('AdminSuppliers').(int)$this->context->employee->id).'"> ** Descarga AQUI el listado de proveedores ** </a>
		 <br />
		<p><input name="fileupdateprice" type="file" />     </p>
		</div>
				<center><input type="submit" name="submitUpdatePrice" value="Actualizar precios" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}
}