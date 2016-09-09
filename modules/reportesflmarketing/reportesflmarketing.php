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

class reportesflmarketing extends Module
{
	private $_html = '';
	private $output = '';
	private $_postErrors = array();
	private $_msg='';

	function __construct()
	{
		$this->name = 'reportesflmarketing';
		$this->tab = 'administration';
		$this->version = '1 Omega';
		$this->author = 'Farmalisto';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Reportes Marketing Farmalisto');
		$this->description = $this->l('Listado de reportes marketing Farmalisto.');

		$this->context->smarty->assign('base_dir', __PS_BASE_URI__);
        $this->context->smarty->assign('reportesLogo', '<img src="' . $this->_path .'logo_big.png" width="64px" height="64px" title="Reportes" />');
        $this->context->smarty->assign('reportesPath', $this->_path);
        $this->context->smarty->assign('pathModule', dirname(__FILE__));

	}

	public function install()
	{
		if (!parent::install() )
			return false;

		if (!$id_tab = Tab::getIdFromClassName('AdminReportesflmarketing'))
		{
			$tab = new Tab();
			$tab->class_name = 'AdminReportesflmarketing';
			$tab->module = 'reportesflmarketing';
			$tab->id_parent = (int)Tab::getIdFromClassName('AdminParentStats'); //aparecerá al final del menú estadisticas

			foreach (Language::getLanguages(false) as $lang)
			$tab->name[(int)$lang['id_lang']] = 'Reportes Marketing';
			if (!$tab->save())
				return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}

		$this->_clearCache('reportesflmarketing.tpl');

		return true;
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		// eliminar tab (pestaña) del menu del backoffice
		$tab = new Tab();
		$id_tab = $tab->getIdFromClassName("AdminReportesflmarketing");
		$tab->id = $id_tab;
		if ( !$tab->delete() ) {
			Tools::displayError('Imposible eliminar pestaña del menu');
		}

		return parent::uninstall();
	}

	// control de flujo del modulo      
	public function getContent() {

        if (Tools::getValue('opc_ini')) {

			$primeraopc = Tools::getValue('opc_ini');
    		switch ($primeraopc) {
    			case 'infencu':
					if (Tools::getValue('executeconsul')) {
						if (Tools::getValue('executeconsul') == "Continuar") {
							if(Tools::getValue('infencu_ini') != '' && Tools::getValue('infencu_fin') != ''  ) {

								$date_format = 'Y-m-d';
								$input1 = Tools::getValue('infencu_ini');
								$input2 = Tools::getValue('infencu_fin');
								$input1 = trim($input1);
								$input2 = trim($input2);
								$time1 = strtotime($input1);
								$time2 = strtotime($input2);

								if ( date($date_format, $time1) == $input1 && date($date_format, $time2) == $input2) {
    								$this->displayConfirmation($this->l('Reporte generado correctamente.'));
    								return $this->displayXlsDown($primeraopc."&f_ini=".Tools::getValue('infencu_ini')."&f_fin=".Tools::getValue('infencu_fin'));
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


    			case 'infseo':
					if (Tools::getValue('executeconsul')) {
						if (Tools::getValue('executeconsul') == "Continuar") {
							$this->displayConfirmation($this->l('Reporte generado correctamente.'));
    						return $this->displayXlsDown($primeraopc);
						}
					}
					$this->output .= $this->adminDisplayWarning($this->l('Seleccione una opción'));
					return $this->output . $this->displayForm();
    			break;


    			default:
    				$this->output.= "<div style='width:100%; float: left;'> <br> Opción no disponible </div>";
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
    	header ("Location: ../modules/reportesflmarketing/xlsdownload.php?opc_sel=".$opcion);
        //return $this->display(__FILE__, 'tpl/xlsdownload.tpl');
    }
}