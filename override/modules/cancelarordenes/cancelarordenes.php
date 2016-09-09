<?php
/*
* 20014-2015 Farmalisto
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
*  @author Ewing Vásquez <ewing.vasquez@farmalisto.com.co>
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of Farmalisto 
*/


if (!defined('_PS_VERSION_'))
	exit;

class cancelarordenes extends Module
{
	private $_html = '';
	private $_postErrors = array();
        private $_msg='';

	function __construct()
	{
		$this->name = 'cancelarordenes';
		$this->tab = 'administration';
		$this->version = '1 Alfa';
		$this->author = 'Farmalisto';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Cambiar estado de ordenes');
		$this->description = $this->l('Actualiza las ordenes colocando el estado de Preparación en Curso.');

	}

	public function install()
	{

		if (!$id_tab = Tab::getIdFromClassName('AdminCancelarOrdenes'))
		{
		$tab = new Tab();
		$tab->class_name = 'AdminCancelarOrdenes';
		$tab->module = 'cancelarordenes';
		$tab->id_parent = (int)Tab::getIdFromClassName('AdminParentOrders'); //aparecerá al final del menú ordenes

		foreach (Language::getLanguages(false) as $lang)
		$tab->name[(int)$lang['id_lang']] = 'Cambiar Estado Ordenes';
		if (!$tab->save())
		return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}

		$this->_clearCache('cancelarordenes.tpl');		

		if ( !parent::install() ) {
			return false;
		}

		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('cancelarordenes.tpl');
		return parent::uninstall();
	}


	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* order_cambio_state */
		$res = (bool)Db::getInstance()->execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."order_cambio_state` (
			  `id_cambio` int(11) NOT NULL AUTO_INCREMENT,
			  `id_order` int(11) DEFAULT NULL,
			  `id_motivo` int(11) DEFAULT NULL,
			  `id_state` int(11) DEFAULT NULL,
			  `id_empleado` int(11) DEFAULT NULL,
			  `fecha` datetime DEFAULT NULL,
			  PRIMARY KEY (`id_cambio`)
			) ENGINE=Aria DEFAULT CHARSET=utf8;");

		/* publicidad */
		$res &= Db::getInstance()->execute("
			CREATE TABLE `"._DB_PREFIX_."order_motivo_cambio` (
			  `id_motivo` int(11) NOT NULL AUTO_INCREMENT,
			  `nombre` varchar(100) DEFAULT NULL,
			  PRIMARY KEY (`id_motivo`)
			) ENGINE=Aria DEFAULT CHARSET=utf8;
		");

		/* publicidad_hook configuration */
		$res &= Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."order_motivo_cambio` (`id_motivo`, `nombre`) 
			VALUES ('1', 'Cancelación de Pedido'),
				   ('2', 'Cambio de Productos'),
				   ('3', 'Error en la digitación'),
				   ('4', 'Error en la generación del Pedido'),
				   ('5', 'Nueva Facturación');");
		
		return $res;
	}



	public function getContent()
	{


		$output = '<h2>'.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitcancelarordenes') && isset($_POST['orden_cambio']) && $_POST['orden_cambio'] != '' && isset($_POST['motivo_cambio']) && $_POST['motivo_cambio'] != '') {

			if( is_numeric( $_POST['orden_cambio'] ) ) {
				$sql = 'SELECT COUNT(1) AS cant 
						FROM `'._DB_PREFIX_.'orders` o
						INNER JOIN `'._DB_PREFIX_.'order_detail` od ON ( o.id_order = od.id_order)
						WHERE  o.current_state IN ('.(int) Configuration::get('PS_OS_SHIPPING').', '.(int) Configuration::get('PS_OS_DELIVERED').', '.(int) Configuration::get('PICKING').', '.(int) Configuration::get('PS_PACKING').' ) 
						AND o.id_order = '.(int)$_POST['orden_cambio'];
						//echo "<pre>".print_r($sql,true).'</pre>'; exit();
				$result = Db::getInstance()->getRow($sql); 

				if ( isset($result['cant']) && $result['cant'] > 0 ) {					

					$history_order = new OrderHistory();
					$history_order->id_order = $_POST['orden_cambio'];
					$history_order->id_order_state = (int) Configuration::get('VERIFICACION_MANUAL'); // Estado preparación en curso
					$history_order->id_employee = Context::getContext()->employee->id;

					if ( $history_order->add() ) {

						$insertOrderCambioEstado = array(
						'id_order' => (int)($history_order->id_order),
						'id_motivo' => (int)($_POST['motivo_cambio']),
						'id_state' => (int)($history_order->id_order_state),
						'id_empleado' => (int)($history_order->id_employee),
						'fecha' => date('Y-m-d H:i:s')
						);

						if ( Db::getInstance()->insert('order_cambio_state', $insertOrderCambioEstado ) ) {

							$output .= $this->displayConfirmation($this->l('Se ha actualizado la orden << '.$_POST['orden_cambio'].' >> al estado Verificación manual.'));

						} else {

							$output .= $this->adminDisplayWarning($this->l('Se ha actualizado la orden << '.$_POST['orden_cambio'].' >> al estado Verificación manual, <br>pero no se insertó el registro de cambios.'));
						}

					} else {
						$output .= $this->displayError( "No se pudo actualizar el estado de la orden.");
					}

				} else {
					$output .= $this->displayError( "La orden no esta en estado Enviado o Entregado o Pickin o Packing, No es posible cambiar la orden a Verificación manual." );
				}

			} else {

				$output .= $this->adminDisplayWarning( "El Id de la orden debe ser numérico." );

			}

		} elseif ( Tools::isSubmit('submitcancelarordenes') ) {

			$output .= $this->displayError( "No se enviaron los datos necesarios para cambiar el estado de la orden.");
		}

		 return $output.$this->displayForm();
	}


	public function displayForm() {

		$output = ' <p><b>'.$this->_msg.'</b></p>
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
	<p>Con este modulo usted podrá actualizar las ordenes de entrada al estado <b>Verificación manual</b>, tenga en cuenta que este cambio no puede ser reversible.</p>


<p> <div style="width:150px; float:left;"> Id de la orden: </div> <div style="width:150px; float:left;"><input type="text" name="orden_cambio" id="orden_cambio" />   </div>  </p>

<p> <div style="width:150px; float:left;"> Motivo del Cambio: </div> <div style="width:150px; float:left;"><select name="motivo_cambio" id="motivo_cambio" />
						<option value=""></option>';

$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT * FROM `'._DB_PREFIX_.'order_motivo_cambio`		
		ORDER BY `nombre` ASC');

		$motivos = array();
		foreach ($result as $motiv) {
			$output .= '<option value="'.$motiv['id_motivo'].'">'.$motiv['nombre'].'</option>';
		}

		$output .= '</select>
</div>
</p>

				<center><input type="submit" name="submitcancelarordenes" value="Actualizar orden de entrada" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

}
