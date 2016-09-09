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

class loadicrsalida extends Module
{
	private $_html = '';
	private $_postErrors = array();
        private $_msg='';

	function __construct()
	{
		$this->name = 'loadicrsalida';
		$this->tab = 'administration';
		$this->version = '0.1 Beta';
		$this->author = 'Farmalisto';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Cague masivo salida ICR ');
		$this->description = $this->l('Actualiza las ordenes de salida asociando los ICR desde un archivo (CSV)');

	}

	public function install()
	{
		if (!$id_tab = Tab::getIdFromClassName('AdminIcrSalida'))
		{
		$tab = new Tab();
		$tab->class_name = 'AdminIcrSalida';
		$tab->module = 'loadicrsalida';
		$tab->id_parent = (int)Tab::getIdFromClassName('AdminParentOrders'); //aparecerá al final del menú catalogo

		$query1= "CREATE TABLE `" . _DB_PREFIX_ . "tmp_cargue_icr_salida` (
			  `id_orden` int(11) DEFAULT NULL,
			  `id_product` int(11) DEFAULT NULL,
			  `reference` varchar(32) DEFAULT NULL,
			  `cod_icr` varchar(6) DEFAULT NULL,
			  `id_icr` int(11) DEFAULT NULL,
			  `flag` enum('n','i','c') DEFAULT 'n'
			) ENGINE=Aria DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1;";
		
			 if (!$results = Db::getInstance()->ExecuteS($query1))
			 {
			 echo '<br><b>Error creando tablas .</b><br>';
			 }

		foreach (Language::getLanguages(false) as $lang)
		$tab->name[(int)$lang['id_lang']] = 'Actualizar ordenes de salida';
		if (!$tab->save())
		return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}

		$this->_clearCache('loadicrsalida.tpl');
		Configuration::updateValue('HOME_FEATURED_NBR', 8);

		if (!parent::install()
			|| !$this->registerHook('displayHome')
			|| !$this->registerHook('header')
			|| !$this->registerHook('addproduct')
			|| !$this->registerHook('updateproduct')
			|| !$this->registerHook('deleteproduct')
		)
			return false;
		return true;
	}
	
	public function uninstall()
	{
		$this->_clearCache('loadicrsalida.tpl');
		return parent::uninstall();
	}

	public function getContent()
	{
		$icr_all = new Icrall();
		//$icr_all->pepe();

$output = '<h2>'.$this->displayName.'</h2>';
if (Tools::isSubmit('submitloadicrsalida'))
{
                    
                    /* validar subida de archivo */
                     
  /* */                  
  $allowedExts = array("txt", "csv" );
  
$temp = explode(".", $_FILES["fileloadicrsalida"]["name"]);


$extension = end($temp);

if ((($_FILES["fileloadicrsalida"]["type"] == "text/csv")
|| ($_FILES["fileloadicrsalida"]["type"] == "text/plain")
|| ($_FILES["fileloadicrsalida"]["type"] == "text/comma-separated-values")
|| ($_FILES["fileloadicrsalida"]["type"] == "application/csv")
|| ($_FILES["fileloadicrsalida"]["type"] == "application/excel")
|| ($_FILES["fileloadicrsalida"]["type"] == "application/vnd.ms-excel")
|| ($_FILES["fileloadicrsalida"]["type"] == "application/vnd.msexcel")
|| ($_FILES["fileloadicrsalida"]["type"] == "application/octet-stream")        
|| ($_FILES["fileloadicrsalida"]["type"] == "text/anytext"))
        
&& ($_FILES["fileloadicrsalida"]["size"] < (1024*5000))
&& in_array($extension, $allowedExts))
  {
  if ($_FILES["fileloadicrsalida"]["error"] > 0)
    {
      
  $this->_msg="Error: " . $_FILES["fileloadicrsalida"]["error"];
    }
  else
    {
     
      $guardar_archivo = new Icrall();
      $names=  $guardar_archivo->saveFile($_FILES,'fileloadicrsalida',new Employee($this->context->cookie->id_employee),'loadicrsalida');          
    
     
     
     
     if (is_array($names) && $names[0] != '' && $names[0] != false && $names[2] !=false ){
     	$retorno_cargue = $guardar_archivo->loadicrsalida($names[2]);

     	if ($retorno_cargue == true) {     	
	     	
	     	if ( $guardar_archivo->validarIcrDuplicados() && $guardar_archivo->validarIcrCargadoVsIngresado() && $guardar_archivo->validarIcrCargadoVsPicking() && $guardar_archivo->validarEstadoRegistrosCargados() && $guardar_archivo->OrdenesProductos()  && $guardar_archivo->IcrCargados()  && $guardar_archivo->validarProductosOrden()) { // validar icr duplicados  // actualizar registros con respecto a ordenes, productos e icr 

	     		if ( $guardar_archivo->insertarPicking() ) { // si inserta en picking
	     			if ($guardar_archivo->cambiarIcrEstado() ) { // si cambia estado de icr
	     				if ($guardar_archivo->reducirStock() ) { //si reduce stock
	     					$output .= $this->displayConfirmation($this->l('Se actualizaron los ICR de las Ordenes de salida.'));
	     				} else {
	     					$output .= $this->displayError(implode("<br>", $guardar_archivo->errores_cargue));
	     				}
	     			} else {
	     				$output .= $this->displayError(implode("<br>", $guardar_archivo->errores_cargue));
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
  }
else
  {
 $output .= $this->displayError( "Este tipo de archivo no es valido."); 
  }
}
 return $output.$this->displayForm();
	}

	public function displayForm()
	{

		$impuestos = TaxRulesGroup::getTaxRulesGroups(true);
		$imp_show = '';

		
		$output = ' <p><b>'.$this->_msg.'</b></p>
		<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" enctype="multipart/form-data" method="post">
			<fieldset><legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>
	<p>Con este modulo usted podrá actualizar las ordenes de salida, recuerde que el archivo CSV debe tener los siguientes campos  <a href="../modules/loadicrsalida/formato.csv">(PEDIDO, FAC, ESTADO, FECHA, EAN, DESCRIPCION, CANTIDAD, PVP, TRANSPORTADORA, IRC)</a>, estos <b>deben estar</b> en la cabecera del archivo.</p>


<p><input name="fileloadicrsalida" type="file" />     </p>
</div>
				<center><input type="submit" name="submitloadicrsalida" value="Actualizar ordenes de salida" class="button" /></center>
			</fieldset>
		</form>';
		return $output;
	}

	public function hookDisplayHeader($params)
	{
		$this->hookHeader($params);
	}

	public function hookHeader($params)
	{
		$this->context->controller->addCSS(($this->_path).'loadicrsalida.css', 'all');
	}

	public function hookDisplayHome($params)
	{
		if (!$this->isCached('loadicrsalida.tpl', $this->getCacheId('loadicrsalida')))
		{
			$category = new Category(Context::getContext()->shop->getCategory(), (int)Context::getContext()->language->id);
			$nb = (int)Configuration::get('HOME_FEATURED_NBR');
			$products = $category->getProducts((int)Context::getContext()->language->id, 1, ($nb ? $nb : 8));

			$this->smarty->assign(array(
				'products' => $products,
				'add_prod_display' => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
				'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
			));
		}
		return $this->display(__FILE__, 'loadicrsalida.tpl', $this->getCacheId('loadicrsalida'));
	}

	public function hookAddProduct($params)
	{
		$this->_clearCache('loadicrsalida.tpl');
	}

	public function hookUpdateProduct($params)
	{
		$this->_clearCache('loadicrsalida.tpl');
	}

	public function hookDeleteProduct($params)
	{
		$this->_clearCache('loadicrsalida.tpl');
	}
}
