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

if (!defined('_PS_VERSION_'))
	exit;

class preciosapp extends Module
{
	private $_html = '';
	private $_postErrors = array();
        private $_msg='';

	function __construct() {
		$this->name = 'preciosapp';
		$this->tab = 'administration';
		$this->version = '1 Alfa';
		$this->author = 'Ewing Vasquez / Farmalisto';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Cague masivo Precios Proveedor App');
		$this->description = $this->l('Actualiza/Inserta los precios de los proveedores de la App Movil');

	}

	public function install() {
		if ( !parent::install() ) {
			return false;
		}

		if (!$id_tab = Tab::getIdFromClassName('AdminPriceApp')) {
			$tab = new Tab();
			$tab->class_name = 'AdminPriceApp';
			$tab->module = 'preciosapp';
			$tab->id_parent = (int)Tab::getIdFromClassName('AdminCatalog'); //aparecerá al final del menú catalogo

			foreach (Language::getLanguages(false) as $lang)
				$tab->name[(int)$lang['id_lang']] = 'Actualiza Precios App';

			if (!$tab->save())
				return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}
	

		return true;
	}
	
	public function uninstall() {
		$this->_clearCache('preciosapp.tpl');
		return parent::uninstall();
	}

	public function getContent() {

		$output = '<h2>'.$this->displayName.'</h2>';
			if (Tools::isSubmit('submitpreciosapp')) {

                    /* validar subida de archivo */

  				/* */                  
  				$allowedExts = array("txt", "csv" );
				  
				$temp = explode(".", $_FILES["filepreciosapp"]["name"]);


				$extension = end($temp);

				if ((($_FILES["filepreciosapp"]["type"] == "text/csv")
				|| ($_FILES["filepreciosapp"]["type"] == "text/plain")
				|| ($_FILES["filepreciosapp"]["type"] == "text/comma-separated-values")
				|| ($_FILES["filepreciosapp"]["type"] == "application/csv")
				|| ($_FILES["filepreciosapp"]["type"] == "application/excel")
				|| ($_FILES["filepreciosapp"]["type"] == "application/vnd.ms-excel")
				|| ($_FILES["filepreciosapp"]["type"] == "application/vnd.msexcel")
				|| ($_FILES["filepreciosapp"]["type"] == "application/octet-stream")
				|| ($_FILES["filepreciosapp"]["type"] == "text/anytext"))
				&& ($_FILES["filepreciosapp"]["size"] < (1024*5000))
				&& in_array($extension, $allowedExts)) {

				  	if ($_FILES["filepreciosapp"]["error"] > 0) {

  						$this->_msg="Error: " . $_FILES["filepreciosapp"]["error"];

    				} else {

      					$guardar_archivo = new Icrall();
      					$names = $guardar_archivo->saveFile($_FILES,'filepreciosapp',new Employee($this->context->cookie->id_employee),'preciosapp');     

					    if (is_array($names) && $names[0] != '' && $names[0] != false && $names[2] !=false ){
					    	
					    	$valida_precios = new AppMovil();					    						    	
					     	$retorno_cargue = $valida_precios->loadprodprovapp( $names[2] );

					     	if ($retorno_cargue == true) {

						     	if ( $valida_precios->ValidateFechaPrecioActua() ) { // validar formato fechas de forma correcta
						     		
                                                            if ( $valida_precios->TruncateProdsProvNew() ) { // Truncando la tabla proveedores_costo

                                                                if ($valida_precios->InsertProdsProvNew()) { // Insertando precios nuevos

                                                                    $output .= $this->displayConfirmation($this->l("Se actualizaron los precios de los productos y proveedores de la App Movil."));

                                                                } else {

                                                                    $output .= $this->displayError(implode("<br>", $guardar_archivo->errores_cargue));
                                                                }

                                                            } else {

                                                                $output .= $this->displayError(implode("<br>", $guardar_archivo->errores_cargue));
                                                            }

						     	} else {
						     		$output .= $this->displayError(implode("<br>", $valida_precios->errores_cargue));
						     	}

						    } else { //error

						     	$output .= $this->adminDisplayWarning(implode("<br>", $guardar_archivo->errores_cargue));
						    }

					    } else { //error

     	 					$output .= $this->adminDisplayWarning(implode("<br>", $guardar_archivo->errores_cargue));
     					}

    				}

  				} else {
 					$output .= $this->displayError( "Este tipo de archivo no es válido."); 
  				}
			}
 		return $output.$this->displayForm();
	}


	public function displayForm() {
		
		//Anulado
		$output = ' <p><b>'.$this->_msg.'</b></p>
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
	<p>Con este modulo usted podrá actualizar/insertar los precios de los proveedores para la App movil de Colombia, recuerde que el archivo CSV debe tener los siguientes campos <br><a download href="../modules/preciosapp/formato.csv">(Id_Producto;PVP;Id_Proveedor;Fecha)</a>, estos <b>deben estar</b> en la cabecera del archivo, <a download href="../modules/preciosapp/formato.csv"><b>Descargar formato</b> </a><BR> * Para la fecha, el formato debe ser <b> YYYY-MM-DD </b> </p>


<p><input name="filepreciosapp" type="file" />     </p>
</div>
				<center><input type="submit" name="submitpreciosapp" value="Actualizar Precios App" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}


}
