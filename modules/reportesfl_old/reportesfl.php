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

class reportesfl extends Module
{
	private $_html = '';
	private $output = '';
	private $_postErrors = array();
        private $_msg='';

	function __construct()
	{
		$this->name = 'reportesfl';
		$this->tab = 'administration';
		$this->version = '0.1 Omega';
		$this->author = 'Farmalisto';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Reportes a la medida para Farmalisto');
		$this->description = $this->l('Listado de reportes a la medida para Farmalisto Colombia.');

		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);
        $this->context->smarty->assign('reportesLogo', '<img src="' . $this->_path .'logo_big.png" width="64px" height="64px" alt="medico" title="Reportes" />');
        $this->context->smarty->assign('reportesPath', $this->_path);
        $this->context->smarty->assign('pathModule', dirname(__FILE__));

	}

	public function install()
	{
		if (!$id_tab = Tab::getIdFromClassName('AdminReportesfl'))
		{
			$tab = new Tab();
			$tab->class_name = 'AdminReportesfl';
			$tab->module = 'reportesfl';
			$tab->id_parent = (int)Tab::getIdFromClassName('AdminParentStats'); //aparecerá al final del menú estadisticas

			foreach (Language::getLanguages(false) as $lang)
			$tab->name[(int)$lang['id_lang']] = 'Reportes Farmalisto Colombia.';
			if (!$tab->save())
				return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}

		$this->_clearCache('reportesfl.tpl');

		if (!parent::install()
			|| !$this->registerHook('displayHome')
			|| !$this->registerHook('header')
		)
			return false;
		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('reportesfl.tpl');
		return parent::uninstall();
	}

	// control de flujo del modulo      
    public function getContent() {

        if (Tools::getValue('opc_ini')) {

			$primeraopc = Tools::getValue('opc_ini');			

	    		switch ($primeraopc) {
	    			case 'consped': // $this->output.= "<div style='width:100%; float: left;'> Crear visitador médico </div>";

	    					if (Tools::getValue('executeconsul')) {
	    						if (Tools::getValue('executeconsul') == "Continuar") {	    							
	    							if(Tools::getValue('consped_f_ini') != '' && Tools::getValue('consped_f_fin') != ''  ) {

										$date_format = 'Y-m-d';
										$input1 = Tools::getValue('consped_f_ini');
										$input2 = Tools::getValue('consped_f_fin');

										$input1 = trim($input1);
										$input2 = trim($input2);

										$time1 = strtotime($input1);
										$time2 = strtotime($input2);

										if ( date($date_format, $time1) == $input1 && date($date_format, $time2) == $input2) {
											
		    								//$retorno = $this->crear_reporte(Tools::getValue('consped_f_ini'), Tools::getValue('consped_f_fin'));

		    								$this->displayConfirmation($this->l('Reporte generado correctamente.'));
		    								return $this->displayXlsDown($primeraopc."&f_ini=".Tools::getValue('consped_f_ini')."&f_fin=".Tools::getValue('consped_f_fin'));

										} else {
											$this->output .= $this->adminDisplayWarning($this->l('Formato de fecha erróneo o fecha incorrecta.'));
											return $this->output . $this->displayForm();
										}

	    							} else {
	    								$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios.'));
	    								return $this->output . $this->displayForm();
	    							}
	    							
	    						}
	    					}
	    					$this->output .= $this->adminDisplayWarning($this->l('Seleccione una opción'));
	    					return $this->output . $this->displayForm();
	    				break;
	    			case 'icrsumi': //$this->output.= "<br> Modificar cupón médico"; ord_consul

	    					if (Tools::getValue('executeconsul')) {
	    						if (Tools::getValue('executeconsul') == "Continuar") {	    							
	    							if(Tools::getValue('ord_consul') != '' ) {

		    								$this->displayConfirmation($this->l('Reporte generado correctamente.'));
		    								return $this->displayXlsDown($primeraopc."&orden=".Tools::getValue('ord_consul'));

	    							} else {
	    								$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios.'));
	    								return $this->output . $this->displayForm();
	    							}
	    							
	    						}
	    					}
	    					$this->output .= $this->adminDisplayWarning($this->l('Seleccione una opción'));
	    					return $this->output . $this->displayForm();
	    				break;

	    				break;

	    			default:	$this->output.= "<div style='width:100%; float: left;'> <br> Opción no disponible </div>";
	    				# code...
	    				break;
	    	}
	    	return $this->output . $this->displayForm();		
        } else {
            return $this->displayForm();
        }
    }

  


// muestra el formulario principal del modulo
    public function displayForm() {    	
        return $this->display(__FILE__, 'tpl/formulario.tpl');
    }

    public function displayXlsDown($opcion) {

    	header ("Location: ../modules/reportesfl/xlsdownload.php?opc_sel=".$opcion);
        //return $this->display(__FILE__, 'tpl/xlsdownload.tpl');
    }


}
