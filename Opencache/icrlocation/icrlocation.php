<?php
if (!defined('_PS_VERSION_'))
	exit;

require_once(_PS_MODULE_DIR_ . 'icrlocation/override/classes/stock/Warehouse.php');
require_once(_PS_MODULE_DIR_ . 'icrlocation/classes/Icr.php');
require_once(_PS_MODULE_DIR_ . 'icrlocation/classes/IcrLocation.php');

/**
 * Ubicaciones de ICR
 * Asignación de ICR a una ubicación física dentro de la Bodega
 *
 * @author Andres Valencia
 */
class IcrLocation extends Module
{
	private $_html = '';
	protected $_errors = array();
	protected $_msg = '';
	protected $_icrUpdate = array();

	public function __construct()
	{
		$this->name = 'icrlocation';
		$this->tab = 'administration';
		$this->version = '1.0.0';
		$this->author = 'Farmalisto';
		$this->need_instance = 0; // No se instancia al momento de cargar el modulo, con eso se ahorra memoria y tiempo

		parent::__construct();

		$this->displayName = $this->l('Asignación de ICR a una ubicación física');
		$this->description = $this->l('Asignación de ICR a una ubicación física dentro de la Bodega');
		
		$this->context->controller->addCSS($this->_path . 'views/css/icrlocation.css');
		$this->context->controller->addJS(__PS_BASE_URI__ . 'js/jquery/jquery.validate.js');
		$this->context->controller->addJS($this->_path . 'views/js/icrlocation.js');
	}
	
	/**
	 * @see Module::install()
	 * @return bool 
	 */
	public function install()
	{
		if( !parent::install() 
			|| !$this->_createTab() )
			return false;
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "icr_location` ( "
				. "`id_icr_location` varchar(30) NOT NULL, "
				. "`active` enum('Y','N') NOT NULL DEFAULT 'Y' "
					. "COMMENT 'Ubicación activa Yes - No', "
				. "`creation_date` datetime DEFAULT NULL "
					. "COMMENT 'Fecha de creación', "
				. "PRIMARY KEY (`id_icr_location`) "
				. ") ENGINE=Aria DEFAULT CHARSET=utf8;";
		
		if( !Db::getInstance()->execute($sql) )
			return false;

		try {
			$sql = "ALTER TABLE `". _DB_PREFIX_ ."icr` "
				. "ADD COLUMN `id_icr_location` varchar(30) NULL "
					. "COMMENT 'Ubicacion del ICR' AFTER `id_estado_icr`,"
				. "ADD INDEX `indx_icr_id_icr_location` (`id_icr_location`) USING BTREE ,"
				. "ROW_FORMAT=DEFAULT;";
			Db::getInstance()->execute($sql);
		} catch (Exception $e) {
			$this->displayError("La columna id_icr_location ya existe en la tabla ". _DB_PREFIX_ ."icr");
		}

		return true;
	}

	/**
	 * @see Module::uninstall()
	 * @return bool 
	 */
	public function uninstall()
	{
		// Uninstall Module
        if ( !parent::uninstall() 
			|| !$this->_deleteTab() )
            return false;

        return true;
	}

	/**
	 * Crea un tab personalizado para el módulo durante la instalación
	 *
	 * @return bool 
	 */
	private function _createTab()
	{
		$tab = new Tab();
		$tab->class_name = 'AdminIcrLocation';
		$tab->module = 'icrlocation';
		$tab->id_parent = (int) Tab::getIdFromClassName('AdminStock'); // Aparecerá al final del menú existencias
		//$tab->position = 11;

		// Need a foreach for the language
		foreach (Language::getLanguages(false) as $lang)
			$tab->name[(int)$lang['id_lang']] = 'Ubicaciones de ICR';
		if (!$tab->add())
			return false;		

		return true;
	}
	
	/**
	 * Elimina el tab personalizado del módulo
	 *
	 * @return bool 
	 */
	private function _deleteTab()
	{
		$tab = new Tab((int)Tab::getIdFromClassName('AdminIcrLocation'));
        if(!$tab->delete())
				return false;
			
		return true;
	}

	/**
	 * Se encarga de la configuración del módulo. 
	 * Crea y gestiona el funcionamiento del formulario 
	 * de configuración en el back-office.
	 *
	 * @return string  contenido html
	 */
	public function getContent() 
	{
		 // Si se ha pulsado el botón Guardar del formulario
		 if ( Tools::isSubmit('btn-submit') ) {
			
			// Comprobar datos del formulario
			if( $this->_postValidation() ) {
				// Si no hay errores, se procesan los datos
				// enviados por el formulario
				if( $this->_postProcess() ) {
					//Informar que se ha agregado los ICRs
					$this->_html .= $this->displayConfirmation($this->_msg);
				}
			}

			// Muestra los errores
			foreach ($this->_errors as $error)
				$this->_html .= $this->displayError($error);

		 }

		return $this->_html . $this->_displayForm();
	}

	/**
	 * Se encarga de generar el formulario de configuración mostrando
	 * los campos necesarios.
	 *
	 * @return string  contenido html
	 */
	private function _displayForm()
	{
		// Definir campos del formulario
		// Se definen los input text, submit, textarea y select		
		
		// Asigna los valores a Smarty
		$this->smarty->assign('warehouses', Warehouse::getAllWarehouses()); 
		$this->smarty->assign('displayName', $this->displayName);

		return $this->display(__FILE__, 'views/templates/admin/icrlocation.tpl');
	}
	
	/**
	 * Comprueba que la información
	 * introducida en el formulario es correcta
	 *
	 * @return bool
	 */
	private function _postValidation()
	{
		// Válida si se ha pulsado el botón Guardar del formulario
		switch( Tools::isSubmit('btn-submit') ) {
			case !Tools::getValue('warehouse'):
				$this->_errors[] = $this->l('El almacén es obligatorio.');
			case !Tools::getValue('location'):
				$this->_errors[] = $this->l('La ubicación es obligatoria.');
			case !Tools::getValue('icr'):
				$this->_errors[] = $this->l('Mínimo debe haber un ICR.');
		}
		
		return !count($this->_errors);
	}
	
	/**
	 * Se encarga de almacenar en la base de datos
	 * la información introducida en el formulario
	 *
	 * @return bool
	 */
	private function _postProcess()
	{
		// Válida si se ha pulsado el botón Guardar del formulario
		if( !Tools::isSubmit('btn-submit') )
			return false;

		// Elimina elementos vacíos de el array icr
		$icrs = array_filter(
			explode("\n", 
			str_replace("\r", "", Tools::getValue('icr'))
		));
		$location = Tools::getValue('location');

		// Agrega a cada ICR su ubicación
		$check = IcrCore::checkWarehouseICR(
					Tools::getValue('warehouse'), 
					Tools::getValue('location'),
					$icrs
				);

		if( !$check['success'] ) {
			$invalid = isset($check['invalid']) ? implode(",", $check['invalid']) : '';
			$this->_errors[] = $check['message'] . $invalid;
		} else {
			$this->_msg = $check['message'];
		}

		return empty($this->_errors);
	}

}
