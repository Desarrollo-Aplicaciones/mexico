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

class cspublicidadfl extends Module
{
	public $tipo_publicar= array();

	public function __construct()
	{
		$this->name = 'cspublicidadfl';
		$this->tab = 'advertising_marketing';
		$this->version = '1 Theta';
		$this->author = 'Ewing Vásquez';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Cs Publicidad Farmalisto');
		$this->description = $this->l('Colocar publicidad de Farmalisto o Google en las páginas de Producto, Home, Category y Search.');
		$this->context->smarty->assign('pathModule', dirname(__FILE__));
		//$this->context->smarty->assign('tokenn', md5(pSQL(_COOKIE_KEY_.'AdminCartRules'.(int)Tab::getIdFromClassName('AdminCartRules').(int)$this->context->employee->id)) );
	}


	public function install()
	{
		// REGISTRAR HOOKS DONDE SE DEBEN MOSTRAR LA PUBLICIDAD
			if (   parent::install() == false 
				|| $this->registerHook('rightColumn') == false 
				|| $this->registerHook('imacategory') == false 
				|| $this->registerHook('csslideshow') == false 
				|| $this->registerHook('hometopder') == false
				|| $this->registerHook('homebotcen') == false
				|| $this->registerHook('prpamidcen') == false
				|| $this->registerHook('prpabotcen') == false
				|| $this->registerHook('searbotcen') == false
				|| $this->registerHook('catetopizq') == false
				|| $this->registerHook('catetopder') == false
				|| $this->registerHook('catebotcen') == false
				) {
					return false;
			}
			$this->createTables();
			return true;
	}


	/**
	 * @see Module::uninstall()
	 */
	public function uninstall()
	{
		/* Deletes Module */
		if (parent::uninstall())
		{
			/* Deletes tables */
			$res = $this->deleteTables();		
			
			return $res;
		}
		return false;
	}



/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* publicidad_hook */
		$res = (bool)Db::getInstance()->execute("
		CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."publicidad_hook` (
			  `id_hook_publicidad` int(11) NOT NULL,
			  `nombre` enum('index','product','category','search') DEFAULT NULL COMMENT 'nombre de la pagina a mostrarlo',
			  `ubicacion` enum('inferior','superior','derecha','media','izquierda') DEFAULT NULL,
			  `tipo` enum('ambos','banner','adsense') DEFAULT 'adsense' COMMENT 'que tipo de publicidad mostrar',
			  `activo` enum('si','no') DEFAULT 'si' COMMENT 'mostrar o no la publicidad',
			  `mostrar` varchar(30) DEFAULT NULL COMMENT '1,2,3,4,5,random',
			  `alto` varchar(10) DEFAULT NULL COMMENT 'alto de la imagen',
			  `ancho` varchar(10) DEFAULT NULL COMMENT 'ancho de la imagen',
			  PRIMARY KEY (`id_hook_publicidad`)
		) ENGINE=Aria DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1;
		");

		/* publicidad */
		$res &= Db::getInstance()->execute("
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."publicidad` (
			  `id_publicidad` int(11) NOT NULL AUTO_INCREMENT,
			  `pagina` enum('search','category','product','index') DEFAULT NULL,
			  `ubicacion` enum('izquierda','derecha','inferior','media','superior') DEFAULT NULL,
			  `tipo` enum('banner','adsense') DEFAULT NULL,
			  `link` varchar(255) DEFAULT NULL,
			  `imagen` varchar(100) DEFAULT NULL,
			  `adsense` text,
			  `activo` enum('no','si') DEFAULT 'no',
			  `category` varchar(255) DEFAULT NULL,
			  PRIMARY KEY (`id_publicidad`)
			) ENGINE=Aria DEFAULT CHARSET=utf8;
		");

		/* publicidad_hook configuration */
		$res &= Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."publicidad_hook` 
			(`id_hook_publicidad`, `nombre`, `ubicacion`, `tipo`, `activo`, `mostrar`, `alto`, `ancho`) 
			VALUES 	('1', 'product', 'media', 'ambos', 'si', null, '90', '970'),
					('2', 'product', 'inferior', 'ambos', 'si', null, '90', '728'),
					('3', 'index', 'superior', 'ambos', 'si', null, '265', '230'),
					('4', 'index', 'inferior', 'ambos', 'si', null, '90', '970'),
					('5', 'search', 'superior', 'ambos', 'si', null, '90', '970'),
					('6', 'category', 'izquierda', 'ambos', 'si', null, '254', '675'),
					('7', 'category', 'derecha', 'ambos', 'si', null, '254', '303'),
					('8', 'category', 'inferior', 'ambos', 'si', null, '90', '970');");

		/* publicidad configuration */
		$res &= Db::getInstance()->Execute("INSERT INTO `"._DB_PREFIX_."publicidad` 
			(`id_publicidad`, `pagina`, `ubicacion`, `tipo`, `link`, `imagen`, `adsense`, `activo`, `category`) 
			VALUES 	('1', 'product', 'media', 'banner', '', '', '', 'si', null),
					('2', 'product', 'media', 'banner', '', '', '', 'si', null),
					('3', 'product', 'media', 'banner', '', '', '', 'si', null),
					('4', 'product', 'inferior', 'banner', '', '', '', 'si', null),
					('5', 'product', 'inferior', 'banner', '', '', '', 'si', null),
					('6', 'product', 'inferior', 'banner', '', '', '', 'si', null),
					('7', 'search', 'superior', 'banner', '', '', '', 'si', null),
					('8', 'search', 'superior', 'banner', '', '', '', 'si', null),
					('9', 'search', 'superior', 'banner', '', '', '', 'si', null),
					('10', 'index', 'superior', 'banner', '', '', '', 'si', null),
					('11', 'index', 'superior', 'banner', '', '', '', 'si', null),
					('12', 'index', 'superior', 'banner', '', '', '', 'si', null),
					('13', 'index', 'inferior', 'banner', '', '', '', 'si', null),
					('14', 'index', 'inferior', 'banner', '', '', '', 'si', null),
					('15', 'index', 'inferior', 'banner', '', '', '', 'si', null),
					('16', 'category', 'inferior', 'banner', '', '', '', 'si', null),
					('17', 'category', 'inferior', 'banner', '', '', '', 'si', null),
					('18', 'category', 'inferior', 'banner', '', '', '', 'si', null),
					('19', 'category', 'derecha', 'banner', '', '', '', 'si', null),
					('20', 'category', 'derecha', 'banner', '', '', '', 'si', null),
					('21', 'category', 'derecha', 'banner', '', '', '', 'si', null);");
		
		return $res;
	}

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{
		
		return Db::getInstance()->execute('
			DROP TABLE IF EXISTS `'._DB_PREFIX_.'publicidad_hook`, `'._DB_PREFIX_.'publicidad`;
		');
	}

	public function getContent() {

		/*echo "<pre>datos: <br>";
		print_r($_POST);
		echo "</pre>";*/
		
		$error = '';
		if (isset($_POST['opc_ini']) && $_POST['opc_ini'] != '' && isset($_POST[$_POST['opc_ini'].'sel']) && $_POST[$_POST['opc_ini'].'sel'] != '' 
			|| ( isset($_POST['opc_ini']) && isset($_POST['val_seleccionado']) && $_POST['val_seleccionado'] == 'izquierda' && 
				$_POST['opc_ini'] == 'category' && isset($_POST['categorysellist']) && $_POST['categorysellist'] != '' )  ) {
			
			//echo "<br>seleccionado: ".$_POST['opc_ini'];

			$this->context->smarty->assign('pagesel', $_POST[$_POST['opc_ini'].'sel'] );


			//echo "<br>hook: ". 
			$query_hook = "SELECT * FROM ". _DB_PREFIX_ ."publicidad_hook WHERE nombre = '".$_POST['opc_ini']."' AND ubicacion = '".
			$_POST['val_seleccionado']."' ";

			if ($load_hook = Db::getInstance()->ExecuteS($query_hook)) {

				$arr_hook_pautas = array();

				foreach ($load_hook as $valores) {

					$arr_hook_pautas[ $valores['id_hook_publicidad'] ]['nombre'] = $valores['nombre'];
					$arr_hook_pautas[ $valores['id_hook_publicidad'] ]['ubicacion'] = $valores['ubicacion'];
					$arr_hook_pautas[ $valores['id_hook_publicidad'] ]['tipo'] = $valores['tipo'];
					$arr_hook_pautas[ $valores['id_hook_publicidad'] ]['activo'] = $valores['activo'];
					$arr_hook_pautas[ $valores['id_hook_publicidad'] ]['mostrar'] = $valores['mostrar'];
					$arr_hook_pautas[ $valores['id_hook_publicidad'] ]['alto'] = $valores['alto'];
					$arr_hook_pautas[ $valores['id_hook_publicidad'] ]['ancho'] = $valores['ancho'];
				}
				
				$this->context->smarty->assign(array('info_hook'=> $arr_hook_pautas ));
			}

			$extra_query = '';
			// SI HA SELECCIONADO LA PAGINA DE CATEGORIA, BANNER IZQUIERDO 
			if ($_POST['opc_ini'] == 'category' && isset($_POST['categorysellist']) && $_POST['categorysellist'] != '' && $_POST['val_seleccionado'] == 'izquierda' ) {

				// QUERY PARA SABER SI HAY REGISTROS PARA LA CATEGORIA SELECCIONADA
				//echo "<br>".
				$query_val_category = "SELECT COUNT(p.id_publicidad) AS total  FROM ". _DB_PREFIX_ ."publicidad p WHERE p.pagina = '".$_POST['opc_ini']."' AND 
						p.ubicacion = '".$_POST['val_seleccionado']."' AND p.category = '".$_POST['categorysellist']."' ";

				if ($load_val_category = Db::getInstance()->ExecuteS($query_val_category)) { 
					//SI YA ESTA CREADA ESA CATEGORIA CON BANNER EN LA BD					
					$rowsca =  $load_val_category[0];
					//echo "<br>total: ".$rowsca['total'];
					if ($rowsca['total'] == '0' ) { 
						// SI NO SE HA REGISTRADO LA CATEGORIA EN LA BD, SE PROCEDE A INSERTAR EL REGISTRO

						$query_insert_publicidad = "INSERT INTO ". _DB_PREFIX_ ."publicidad ( pagina, ubicacion, tipo, link, imagen, adsense, activo, category )
						VALUES ( '".$_POST['opc_ini']."', '".$_POST['val_seleccionado']."', 'banner', '', '', '', 'si', '".$_POST['categorysellist']."' )";
						
						if ($load_insert_publicidad = Db::getInstance()->ExecuteS($query_insert_publicidad) ) {
							echo "<br> publicidad para la categoria ".$_POST['categorysellist']." insertada";
						} else {
							$output = $this->adminDisplayWarning('No se pudo crear el registro para la categoría seleccionada.');
							return $output.$this->displayForm();
						}

					}

				} else {
					$output = $this->adminDisplayWarning('Error en el cargue de la información de publicidad de la categoría.');
					return $output.$this->displayForm();
				}

				$extra_query = " AND category = '".$_POST['categorysellist']."' ";

				$query_cat_select = "SELECT name FROM ". _DB_PREFIX_. "category_lang WHERE id_category = ".$_POST['categorysellist'];
				if ($load_cat_select = Db::getInstance()->ExecuteS($query_cat_select) ) {
					/*echo "<pre>catca: ";
					print_r($load_cat_select);
					echo "</pre>c";*/
					$valorcategoria = $load_cat_select[0];
					//echo "<br>nom: ".$valorcategoria['name'];
					$this->context->smarty->assign('categoria_cargada',$valorcategoria['name'] );

				} else { 
					$output = $this->adminDisplayWarning('No se pudo cargar el nombre de la categoría seleccionada.');
					return $output.$this->displayForm();
				}

			}

			//echo "<br>". 
			$query_config = "SELECT * FROM ". _DB_PREFIX_ ."publicidad WHERE pagina = '".$_POST['opc_ini']."' AND 
						ubicacion = '".$_POST['val_seleccionado']."' ".$extra_query;
	   
			if ($load_config = Db::getInstance()->ExecuteS($query_config)) {

				$arr_enviar_pautas = array();

				foreach ($load_config as $valores) {

					$arr_enviar_pautas[ $valores['id_publicidad'] ]['pagina'] = $valores['pagina'];
					$arr_enviar_pautas[ $valores['id_publicidad'] ]['ubicacion'] = $valores['ubicacion'];
					$arr_enviar_pautas[ $valores['id_publicidad'] ]['tipo'] = $valores['tipo'];
					$arr_enviar_pautas[ $valores['id_publicidad'] ]['link'] = $valores['link'];
					$arr_enviar_pautas[ $valores['id_publicidad'] ]['imagen'] = $valores['imagen'];
					$arr_enviar_pautas[ $valores['id_publicidad'] ]['adsense'] = $valores['adsense'];
					$arr_enviar_pautas[ $valores['id_publicidad'] ]['activo'] = $valores['activo'];
					$arr_enviar_pautas[ $valores['id_publicidad'] ]['category'] = $valores['category'];
				}

				$this->context->smarty->assign(array('info_pautas'=> $arr_enviar_pautas ));

				$this->context->smarty->assign(array('nombre_list' => array('' =>' -- Seleccione -- ', 'index','product','category','search')));
				$this->context->smarty->assign(array('ubicacion_list' => array(' -- Seleccione -- ', 'media','inferior','superior','derecha','izquierda')));
				$this->context->smarty->assign(array('tipo_list' => array(''=> ' -- Seleccione -- ', 'ambos'=>'ambos','banner'=>'banner','adsense'=>'adsense')));
				$this->context->smarty->assign(array('tipo_listp' => array(''=> ' -- Seleccione -- ', 'banner'=>'banner','adsense'=>'adsense')));
				$this->context->smarty->assign(array('activo_list' => array('' => ' -- Seleccione -- ','si'=> 'si','no' =>'no')));


				return $this->displayFormStep2();
			} else {

				$output = $this->adminDisplayWarning('No se ha encontrado publicidad para esta configuracion.');
				//return false;
			}
		} elseif ( isset($_POST['submit']) && isset($_POST['hook'])  && isset($_POST['hook']) && isset($_POST['activo'])  
			&& $_POST['submit'] == 'Cambiar tipo de publicidad' && $_POST['hook'] != '' && $_POST['tipo'] != ''  && $_POST['activo'] != '') {

			$update_hook = "UPDATE ". _DB_PREFIX_ ."publicidad_hook SET tipo = '".$_POST['tipo']."', activo = '".$_POST['activo']."' WHERE id_hook_publicidad = '".
			$_POST['hook']."' ";

			if ($load_hook = Db::getInstance()->ExecuteS($update_hook)) {
				$output = $this->displayConfirmation($this->l('Publicidad modificada correctamente.'));
			} else {
				$output = $this->adminDisplayWarning('No se pudo modificar el tipo de publicidad');
			}

		} else {
			//$output = $this->adminDisplayWarning("error interno primero");
			//echo "opc: ".$_POST['opc_ini']." ---- sel: ". $_POST[$_POST['opc_ini'].'sel'];
		}

		//$output = '<h2>'.$this->displayName.'</h2>';
		//include_once("class_imgUpldr.php"); 
		//$obj = new imgUpldr;
		//$obj -> conectardb();
		
		return $output.$this->displayForm();
	}


	public function displayForm()
	{
		//echo "|".__FILE__."|";
		$this->loadConfigPublicidadFull();
		// LISTADO (enum) DEL CAMPO PAGINA DE LA TABLA PS_PUBLICIDAD
		
		$list_paginas_banner = array('search' => 'search', 'category' => 'category', 'product' => 'product', 'index' => 'index'); 		

		$this->context->smarty->assign('paginas_sel',$list_paginas_banner);

		return $this->display(__FILE__, 'tpl/formulario.tpl');
	}

	public function displayFormStep2()
	{	
		$this->loadConfigPublicidadFull();
		// LISTADO (enum) DEL CAMPO PAGINA DE LA TABLA PS_PUBLICIDAD
		
		$list_paginas_banner = array('search' => 'search', 'category' => 'category', 'product' => 'product', 'index' => 'index'); 		

		$this->context->smarty->assign('paginas_sel',$list_paginas_banner);

		return $this->display(__FILE__, 'tpl/formulario2.tpl');
	}


	public function loadConfigPublicidadFull() {

		//echo "<br>".
		$query_config = "SELECT nombre, ubicacion, id_hook_publicidad  FROM ". _DB_PREFIX_ ."publicidad_hook ";
				
		$query_categorias = " SELECT name, id_category FROM ". _DB_PREFIX_ ."category_lang WHERE meta_keywords NOT LIKE	'Doctores%' OR meta_title LIKE 'MÉDICOS%' 
							AND name NOT IN ('ROOT','INICIO') ORDER BY name ASC";

   		$configfull = array();
   		$categoryfull = array();
		if ($load_config = Db::getInstance()->ExecuteS($query_config)) {
			
			foreach ($load_config as $valores) {
		
				$configfull[$valores['nombre']][$valores['id_hook_publicidad']] = $valores['ubicacion'];
           	}

           	if ($load_categorias = Db::getInstance()->ExecuteS($query_categorias)) {
           		foreach ($load_categorias as $valores2) {
           			$categoryfull[$valores2['id_category']] = $valores2['name'];		
           		}
           	}

           	$seleccion_vacia = array('' => ' -- Seleccione -- ');
           	$this->context->smarty->assign(array('search'=> array_merge((array)$seleccion_vacia, (array)$configfull['search']) ));
           	$this->context->smarty->assign(array('category'=> array_merge((array)$seleccion_vacia, (array)$configfull['category']) ));
           	$this->context->smarty->assign(array('product'=> array_merge((array)$seleccion_vacia, (array)$configfull['product']) ));
           	$this->context->smarty->assign(array('index'=> array_merge((array)$seleccion_vacia, (array)$configfull['index']) ));

           	$this->context->smarty->assign(array('categoryfull'=>$categoryfull));

		} else {			
			return false;
		}
    }


    public function loadConfigPublicidad($ubicacion) {
    	//print_r($this->context->controller->php_self);


		//echo "<br>".
		$query_config = "SELECT tipo, mostrar FROM ". _DB_PREFIX_ ."publicidad_hook WHERE nombre = '".$this->context->controller->php_self."' AND 
						ubicacion = '".$ubicacion."' AND activo = 'si' LIMIT 1";

   
		if ($load_config = Db::getInstance()->ExecuteS($query_config)) {
			
			$datos = $load_config[0];

			$this->tipo_publicar['tipo'] = $datos['tipo'];
			$this->tipo_publicar['mostrar'] = $datos['mostrar'];

			return $datos['tipo'];

		} else {			
			return false;
		}
    }



    public function cargapublicidad($pagcarga, $config_show, $ubicacion, $categoria = false) {

    	//echo "<h1>hay configuracion</h1><br>config: ".$config_show;
        	$tipo_publicidad = '';
        	if($config_show == 'ambos') { 
        		//mostrar los dos tipos de publicidad de forma aleatoria
        		$tipo_publicidad = " AND ( p.tipo = 'banner' OR p.tipo = 'adsense' )"; 
        	} elseif ($config_show == 'adsense') { 
        		//mostrar tipo de publicid adsense
        		$tipo_publicidad = " AND ( p.tipo = 'adsense' )"; 
        	} else { 
        		//mostrar tipo de publicid banner
        		$tipo_publicidad = " AND ( p.tipo = 'banner' )"; 
        	}

        	if ($categoria) {
        		$tipo_publicidad .= " AND category = '".$categoria."' ";
        	}

        	//echo "<br>".
        	$query_colocar = "SELECT p.tipo, p.imagen, p.link, p.adsense, ph.alto, ph.ancho FROM ". _DB_PREFIX_ ."publicidad p INNER JOIN ". _DB_PREFIX_ ."publicidad_hook ph
        	ON (ph.nombre = p.pagina AND ph.ubicacion = p.ubicacion)
        	 WHERE p.pagina = '".$pagcarga."' ".$tipo_publicidad." AND p.activo = 'si' AND p.ubicacion = '".$ubicacion."'";

			if ($this->tipo_publicar['mostrar'] == 'random' || $this->tipo_publicar['mostrar'] == '' ) {
				$query_colocar .= " ORDER BY RAND() LIMIT 1 ";
			} elseif ($this->tipo_publicar['mostrar'] != '') {
				$query_colocar .= " AND id_publicidad IN (".$this->tipo_publicar['mostrar'].") ORDER BY RAND() LIMIT 1 ";
			}

			//echo "<br>hook: ".$query_colocar;

        	if ($result_banner = Db::getInstance()->ExecuteS($query_colocar)) {

        		$datos = $result_banner[0];       		        		
        		
        		$this->smarty->assign(array( 'tipopublicidad' => $datos['tipo']));
        		//echo "<br>publicidad tipo ".$datos['tipo']."<br>img: ".$datos['imagen'];

        		if ( $datos['tipo'] == 'banner' ) {

        			$this->smarty->assign(array( 'publicidad' => $datos['imagen']));
        			$this->smarty->assign(array( 'linkpublicidad' => $datos['link']));
        			$this->smarty->assign(array( 'anchopublicidad' => $datos['ancho']));
        			$this->smarty->assign(array( 'altopublicidad' => $datos['alto']));

        		} elseif ( $datos['tipo'] == 'adsense') {

        			$this->smarty->assign(array( 'publicidad' => $datos['adsense']));

        		}
        		
        		if ( $datos['imagen'] != '' && (file_exists(Configuration::get('PATH_UP_LOAD')."cspublicidadfl/uploads/".$datos['imagen']) &&  $datos['tipo'] == 'banner' ) || $datos['tipo'] == 'adsense' ) {
        			//echo "<br>si file";
        			return true;
        		} else {
        			//echo "<br>NO file";
        			return false;
        		}

        		
        	} else {
        		//echo "<h1>sin registros publicitarios</h1>";
        		return false;
        	}
    }


    /**
     * [hookhomebotcen publicidad de parte superior derecha del home - index]
     * @param  [type] $params [description]
     * @return [type]         [tpl o false]
     */

    public function hookCsSlideshow($params)
	{
		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {

	     	$ubicacion = "superior";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {
				
				if ( $this->cargapublicidad('index', $config_show, $ubicacion) ) {
	        			return $this->display(__FILE__, '/tpl/homesup.tpl');
	        		} else {
	        			return false;
	        		}

	        } else {
	        	//echo "<h1>no hay nada</h1>";
	        	return false;
	        }
	    }		
	}


    /**
     * [hookhomebotcen publicidad de parte inferior del home - index]
     * @param  [type] $params [description]
     * @return [type]         [tpl o false]
     */
	public function hookhomebotcen($params)
	{
		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'index') {
	     	$ubicacion = "inferior";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {

	        	if ( $this->cargapublicidad('index', $config_show, $ubicacion) ) {
        			return $this->display(__FILE__, 'cspublicidadfl.tpl');
        		} else {
        			return false;
        		}

	        } else {
	        	//echo "<h1>no hay nada</h1>";
	        	return false;
	        }
	    }
	}



	/**
	 * [hookimacategory publicidad de parte media de la página de productos - index]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function hookprpamidcen($params)
	{
		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'product') {
	     	$ubicacion = "media";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {
	        	
        		if ( $this->cargapublicidad('product', $config_show, $ubicacion) ) {
        			return $this->display(__FILE__, 'cspublicidadfl.tpl');
        		} else {
        			return false;
        		}

	        } else {
	        	//echo "<h1>no hay nada</h1>";
	        	return false;
	        }
	    }
	}


	/**
	 * [hookimacategory publicidad de parte inferior de la página de productos - index]
	 * @param  [type] $params [description]
	 * @return [type]         [description]
	 */
	public function hookprpabotcen($params)
	{
		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'product') {
	     	$ubicacion = "inferior";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {
	        	
        		if ( $this->cargapublicidad('product', $config_show, $ubicacion) ) {
        			return $this->display(__FILE__, '/tpl/72890.tpl');
        		} else {
        			return false;
        		}

	        } else {
	        	//echo "<h1>no hay nada</h1>";
	        	return false;
	        }
	    }
	}	

    /**
     * [hookhomebotcen publicidad de parte superior derecha de pagina de producto - index]
     * @param  [type] $params [description]
     * @return [type]         [tpl o false]
     */

    public function hookprodmain($params)
	{
		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'product') {

	     	$ubicacion = "superior";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {
				
				if ( $this->cargapublicidad('product', $config_show, $ubicacion) ) {
	        			return $this->display(__FILE__, '/tpl/prodmain.tpl');
	        		} else {
	        			return false;
	        		}

	        } else {
	        	//echo "<h1>no hay nada</h1>";
	        	return false;
	        }
	    }		
	}

	 /**
     * [hookhomebotcen publicidad de parte inferior de los resultados de búsqueda]
     * @param  [type] $params [description]
     * @return [type]         [tpl o false]
     */
	public function hooksearbotcen($params)
	{
		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'search') {
	     	$ubicacion = "superior";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {

	        	if ( $this->cargapublicidad('search', $config_show, $ubicacion) ) {
        			return $this->display(__FILE__, 'cspublicidadfl.tpl');
        		} else {
        			return false;
        		}

	        } else {
	        	//echo "<h1>no hay nada</h1>";
	        	return false;
	        }
	    }
	}


	 /**
     * [hookhomebotcen publicidad de parte inferior de las categorias]
     * @param  [type] $params [description]
     * @return [type]         [tpl o false]
     */
	public function hookcatebotcen($params)
	{
		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'category') {
	     	$ubicacion = "inferior";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {

	        	if ( $this->cargapublicidad('category', $config_show, $ubicacion) ) {
        			return $this->display(__FILE__, 'cspublicidadfl.tpl');
        		} else {
        			return false;
        		}

	        } else {
	        	//echo "<h1>no hay nada</h1>";
	        	return false;
	        }
	    }
	}


	 /**
     * [hookhomebotcen publicidad de parte izquierda de las categorias]
     * @param  [type] $params [description]
     * @return [type]         [tpl o false]
     */
	public function hookcatetopizq($params)
	{		
		//print_r($this->context->controller->getCategory()->id_parent);
	
		//print_r($this->context->controller->getCategory()->id);

		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'category') {
	     	$ubicacion = "izquierda";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {

	        	if ( $this->cargapublicidad('category', $config_show, $ubicacion, $this->context->controller->getCategory()->id) ) {
	        		//echo "<h1>si banner cat prin</h1>";
        			return $this->display(__FILE__, '/tpl/category_left.tpl');
        		} else {

        			if ( $this->cargapublicidad('category', $config_show, $ubicacion, $this->context->controller->getCategory()->id_parent) ) {
	        		//echo "<h1>si cat padre</h1>";
        				return $this->display(__FILE__, '/tpl/category_left.tpl');
	        		} else {

	        			//echo "<h1>no hay nada  cat padre </h1>";
	        			return false;
	        		}

        			//echo "<h1>no hay nada 1 cat prin</h1>";
        			return false;
        		}

	        } else {
	        	//echo "<h1>no hay nada 2</h1>";
	        	return false;
	        }
	    }
	}



	 /**
     * [hookhomebotcen publicidad de parte izquierda de las categorias]
     * @param  [type] $params [description]
     * @return [type]         [tpl o false]
     */
	public function hookcatetopder($params)
	{

		//print_r($this->context->controller->getCategory()->id);

		if ( isset($this->context->controller->php_self) && $this->context->controller->php_self == 'category') {
	     	$ubicacion = "derecha";
	        $config_show = $this->loadConfigPublicidad($ubicacion);

	        if($config_show != false) {

	        	if ( $this->cargapublicidad('category', $config_show, $ubicacion) ) {
	        		//echo "<h1>si banner_right</h1>";
        			return $this->display(__FILE__, '/tpl/category_right.tpl');
        		} else {
        			//echo "<h1>no hay nada 1_right</h1>";
        			return false;
        		}

	        } else {
	        	//echo "<h1>no hay nada 2_right</h1>";
	        	return false;
	        }
	    }
	}




	public function hookHeader()
	{
		
		$this->context->controller->addCSS(($this->_path).'cspublicidadfl.css', 'all');

	}

}


