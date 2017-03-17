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
//require_once(_PS_ROOT_DIR_.'/classes/seveFileClass.php');

if (!defined('_PS_VERSION_'))
	exit;

class loadicrdevoanula extends Module
{
	private $_html = '';
	private $_postErrors = array();
        private $_msg='';

	function __construct()
	{
		$this->name = 'loadicrdevoanula';
		$this->tab = 'administration';
		$this->version = '1 Alfa';
		$this->author = 'Ewing Vasquez';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Cague masivo ICR Devolver/Anular');
		$this->description = $this->l('Actualiza los ICR desde un archivo (CSV) para marcarlos como anulados o devueltos.');

	}

	public function install()
	{
		if ( !parent::install() ) {
			return false;
		}

		if (!$id_tab = Tab::getIdFromClassName('AdminIcrDevuelto')) {
			$tab = new Tab();
			$tab->class_name = 'AdminIcrDevuelto';
			$tab->module = 'loadicrdevoanula';
			$tab->id_parent = (int)Tab::getIdFromClassName('AdminStock'); //aparecerá al final del menú catalogo

			foreach (Language::getLanguages(false) as $lang)
				$tab->name[(int)$lang['id_lang']] = 'Devolver-Anular ICRs.';

			if (!$tab->save())
				return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}
	

		$query1= "CREATE TABLE `" . _DB_PREFIX_ . "tmp_cargue_icr_devolucion` (
		  `cod_icr` varchar(6) DEFAULT NULL,
		  `estado_icr` varchar(32) DEFAULT NULL
		) ENGINE=Aria DEFAULT CHARSET=utf8;";
		
		 if (!$results = Db::getInstance()->ExecuteS($query1))
		 {
		 	echo '<br><b>Error creando tablas .</b><br>';
		 	return false;
		 }

		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('loadicrdevoanula.tpl');
		return parent::uninstall();
	}

	public function getContent()
	{

		$output = '<h2>'.$this->displayName.'</h2>';
			if (Tools::isSubmit('submitloadicrdevoanula')) {

                    /* validar subida de archivo */

  				/* */                  
  				$allowedExts = array("txt", "csv" );
				  
				$temp = explode(".", $_FILES["fileloadicrdevoanula"]["name"]);


				$extension = end($temp);

				if ((($_FILES["fileloadicrdevoanula"]["type"] == "text/csv")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "text/plain")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "text/comma-separated-values")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "application/csv")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "application/excel")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "application/vnd.ms-excel")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "application/vnd.msexcel")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "application/octet-stream")
				|| ($_FILES["fileloadicrdevoanula"]["type"] == "text/anytext"))
				&& ($_FILES["fileloadicrdevoanula"]["size"] < (1024*5000))
				&& in_array($extension, $allowedExts)) {

				  	if ($_FILES["fileloadicrdevoanula"]["error"] > 0) {

  						$this->_msg="Error: " . $_FILES["fileloadicrdevoanula"]["error"];

    				} else {

      					$guardar_archivo = new Icrall();
      					$names = $guardar_archivo->saveFile($_FILES,'fileloadicrdevoanula',new Employee($this->context->cookie->id_employee),'loadicrdevoanula');     

					    if (is_array($names) && $names[0] != '' && $names[0] != false && $names[2] !=false ){
					     	$retorno_cargue = $guardar_archivo->loadicrdevoanula($names[2]);

					     	if ($retorno_cargue == true) {

						     	if ( $guardar_archivo->ValidateIcrCambioEstado() ) { // validar icr duplicados  // estados validos etc
						     		
						     		if ( $guardar_archivo->CambioEstadosIcr() ) { // si actualiza estado de los ICR
						     			
						     			if ($guardar_archivo->FullStockIcr()) {

						     				$output .= $this->displayConfirmation($this->l("Se actualizaron los estados de (". $guardar_archivo->response_extra .") ICR's y se actualizó el stock."));

						     			} else {

						     				$output .= $this->adminDisplayWarning($this->l("Se actualizaron los estados de (". $guardar_archivo->response_extra .") ICR's, pero no se pudo re-generar el stock completo, <b>Contacte a su Administrador</b>."));
						     			}
						     			
						     		} else {

						     			$output .= $this->displayError(implode("<br>", $guardar_archivo->errores_cargue));
						     		}	

						     	} else {
						     		$output .= $this->displayError(implode("<br>", $guardar_archivo->errores_cargue));
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


	public function displayForm()
	{
		//Anulado
		$output = ' <p><b>'.$this->_msg.'</b></p>
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
	<p>Con este modulo usted podrá actualizar los estados de los ICR a devuelto o a anulado, recuerde que el archivo CSV debe tener los siguientes campos <br><a download href="../modules/loadicrdevoanula/formato.csv">(cod_icr,estado_icr) </a>, estos <b>deben estar</b> en la cabecera del archivo, <a download href="../modules/loadicrdevoanula/formato.csv"><small>Descargar formato</small> </a><BR> * los estados se deben ingresar como texto, los cuales pueden ser: Devolucion . </p>


<p><input name="fileloadicrdevoanula" type="file" />     </p>
</div>
				<center><input type="submit" name="submitloadicrdevoanula" value="Actualizar estado de ICR\'s" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}


}
