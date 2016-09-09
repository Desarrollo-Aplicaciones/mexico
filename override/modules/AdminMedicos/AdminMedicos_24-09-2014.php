<?php
/**
 * 	Clase encargada de la administración de médicos y especialidades médicas.
 */

if (!defined('_PS_VERSION_'))
	exit;

class AdminMedicos extends Module
{

    //private $_html = '';
    //private $_postErrors = array();
    //private $_msg='';
    private $select_limit = 100;
    private $offset = null;
    private $pag = null;
    private $total__rows = 0;
    private $output = '';
    private $adminmedicMsg = '';
    private $perfil_usuario = '';
    private $json_empleado = '';
    /**
     * [$val_random para asignar el id del visitador si es este el del perfil usado]
     * @var integer
     */
    private $val_random = 0;


    private $error = 0;
    private $errores = '';
    private $lista_campos_input1 = array();
    private $bd_fielsd_save = array();
    //private $bd_data_save = array();

    /**
     * [$permisos_basicos 0 = superadmin, 2 = supervisor visitadores, 3 = visitador medico]
     * @var integer
     */
    private $permisos_basicos = 3;

    function __construct() {  //informacion del modulo
		$this->name = 'AdminMedicos';
		$this->tab = 'administration';
		$this->version = '0.5 Theta';
		$this->author = 'Farmalisto - Ewing Vásquez';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Administración de Médicos y Visitadores');
		$this->description = $this->l('Actualiza las características de los médicos y visitadores de Farmalisto SAS');

		$nombs[]="stock_doctor_female_med.jpg";
		$nombs[]="stock_doctor_med.jpg";
		$nombs[]="businesspeople_med.jpg";			

		$rand_keys = array_rand($nombs, 1);

        $this->context->smarty->assign('base_dir', __PS_BASE_URI__);
        $this->context->smarty->assign('adminmedicLogo', '<img src="' . $this->_path ."".$nombs[$rand_keys].'" width="64px" height="64px" alt="medico" title="medico" />');
        $this->context->smarty->assign('adminmedicPath', $this->_path);
        $this->context->smarty->assign('pathModule', dirname(__FILE__));
        $this->context->smarty->assign('adminmedicMsg', $this->adminmedicMsg);
        $this->context->smarty->assign('tokenn', md5(pSQL(_COOKIE_KEY_.'AdminCartRules'.(int)Tab::getIdFromClassName('AdminCartRules').(int)$this->context->employee->id)) );
        
        $this->perfil_usuario = Profile::getProfile($this->context->employee->id_profile);        
        
        if ( strtolower($this->perfil_usuario['name']) == 'superadmin' ) { // PARA SUPER ADMIN
        	$this->permisos_basicos = 0;

        } elseif (strtolower($this->perfil_usuario['name']) == 'jefe visitador') { // PARA SUPERVISOR VISITADORES
        	$this->permisos_basicos = 2;
        } else { 						// PARA VISITADORES
        	$this->permisos_basicos = 3;
        	$this->val_random = 0; //$this->context->employee->id; // valor en 0 para poder modificar cualquier medico
        }

        $this->context->smarty->assign('random_visitador', $this->val_random);
        $this->context->smarty->assign('perfil_usuario', $this->permisos_basicos);

    }

    public function install() // parametros de instalación del modulo
	{
		if (!$id_tab = Tab::getIdFromClassName('AdminMedicosMenu'))  // para crear acceso en menu back office / clase creada
		{
		$tab = new Tab();
		$tab->class_name = 'AdminMedicosMenu';		//la clase que redirecciona el link del menu a la configuracion
		$tab->module = 'AdminMedicos';	// nombre del modulo creado
		$tab->id_parent = (int)Tab::getIdFromClassName('AdminPriceRule'); //aparecerá al final del menú catalogo
		foreach (Language::getLanguages(false) as $lang)
			$tab->name[(int)$lang['id_lang']] = 'Actualizar Médicos'; // texto a mostrar en el menu
		if (!$tab->save())
			return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
		}

		$this->_clearCache('AdminMedicos.tpl');
		Configuration::updateValue('HOME_FEATURED_NBR', 8);

		if (!parent::install()) {	
			return false;
		}

		return true;
	}
	
	public function uninstall()  // desinstalacion
	{
		$this->_clearCache('AdminMedicos.tpl');
		return parent::uninstall();
	}

    // control de flujo del modulo      
    public function getContent() {
    	if ($this->json_empleado == '') {
    		$this->json_empleado = ' "Empleado": {"id_employee" : "'.$this->context->employee->id.'", "nombres" : "'.$this->context->employee->firstname.'", "apellidos" : "'.$this->context->employee->lastname.'"}';
    	}

    	$this->listaEspecialidadesMedicas();
    	// $this->listaVisitadorMedico();
    	// $this->listaVisitadorMedicoFull();
    	
        if (Tools::getValue('opc_ini')) {

			$primeraopc = Tools::getValue('opc_ini');

			if (substr($primeraopc,0,4) == "crea"){
				$this->cargaDatosBd($this->permisos_basicos, "medicos", $primeraopc, "crear"); //CARGAR CAMPOS Y PERMISOS DE LA OPCION A CREAR
			} else {
				$this->cargaDatosBd($this->permisos_basicos, "medicos", $primeraopc, "modificar"); //CARGAR CAMPOS Y PERMISOS DE LA OPCION A MODIFICAR
			}

	    	$validado = 0 ;
	    	$guardar = 0 ;
		if (Tools::getValue('savenew'.$primeraopc)) {
			$guardar = 1;
			if (Tools::getValue('savenew'.$primeraopc) == "Guardar") {
				$validado = $this->validarCampos();
			}
		} elseif (Tools::getValue('saveold'.$primeraopc)) {
			$guardar = 1;
			if (Tools::getValue('saveold'.$primeraopc) == "Guardar") {
				$validado = $this->validarCampos();
			}
		}

		switch ($primeraopc) {
			case 'creamed':
				if($guardar == 1 && $validado == 1) {
					if ( $retorno = $this->registernewmed() ) { //REGISTRAR EL MEDICO EN LA BD	
						$this->output .= $this->displayConfirmation($this->l('El cupón médico se registró correctamente.'));	    									
					} else {
						$this->output .= $this->adminDisplayWarning($this->l('El cupón médico NO se registró correctamente.'.$this->errores));
					}
					return $this->output . $this->displayForm();
				} else {

					return $this->output . $this->displayCreaCupon();
				}
				break;

			case 'actumed': //$this->output.= "<br> Modificar cupón médico";

				switch (Tools::getValue('step_opc')) {
	    			case 1 :
						if ( Tools::getValue('doc_fnd') != '' ) {
	    					$this->cargaMedicoPorIdCupon( Tools::getValue('doc_fnd') );
		    				return $this->output . $this->displayModiCupon();
						} else {
		    				$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios, Seleccione un médico a modificar.'.$this->errores));
		    				return $this->output . $this->displayForm();
		    			}

	    				break;
	    			case 2 : 
	    				if ( $validado ) {
		    				if ($retorno = $this->registeroldmed()) {
		    					$this->output .= $this->displayConfirmation($this->l('El cupón médico se modificó correctamente.'));
		    				} else {
		    					$this->output .= $this->adminDisplayWarning($this->l('Ocurrio un error al modificar el cupón médico.'.$this->errores));
		    				}
		    				return $this->output . $this->displayForm();
		    			} else {
		    				$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios.'.$this->errores));
		    				return $this->output . $this->displayModiCupon();
		    			}
	    				break;
	    		}

				return $this->output . $this->displayModiCupon();
				break;

			/*case 'creavis': //$this->output.= "<br> Crear visitador médico";
				
				if($guardar == 1 && $validado == 1) {
					if ( $retorno = $this->registernewvis() ) { //REGISTRAR EL MEDICO EN LA BD	
						$this->output .= $this->displayConfirmation($this->l('El visitador médico se registró correctamente.'));
					} else {
						$this->output .= $this->adminDisplayWarning($this->l('El visitador médico NO se registró correctamente.'.$this->errores));
					}
					return $this->output . $this->displayForm();
				} else {

					return $this->output . $this->displayCreaVisitador();
				}				
				break;

			case 'actuvis': //$this->output.= "<br> Modificar visitador médico";

				switch (Tools::getValue('step_opc')) {
	    			case 1 :
						if ( Tools::getValue('visitadormod') != '' ) {
	    					$this->cargaVisitadorPorIdVisitador( Tools::getValue('visitadormod') );
		    				return $this->output . $this->displayModiVisitador();
						} else {
		    				$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios, Seleccione un visitador a modificar.'.$this->errores));
		    				return $this->output . $this->displayForm();
		    			}
	    				break;

	    			case 2 : 
	    				if ( $validado ) {
		    				if ($retorno = $this->registeroldvis()) {
		    					$this->output .= $this->displayConfirmation($this->l('El visitador médico se modificó correctamente.'));
		    				} else {
		    					$this->output .= $this->adminDisplayWarning($this->l('Ocurrio un error al modificar el visitador médico.'.$this->errores));
		    				}
		    				return $this->output . $this->displayForm();
		    			} else {
		    				$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios.'.$this->errores));
		    				return $this->output . $this->displayModiVisitador();
		    			}
	    				break;
	    		}

				return $this->output . $this->displayModiVisitador();
				break;
			*/
			case 'creaesp': //$this->output.= "<br> Crear visitador médico";
				
				if($guardar == 1 && $validado == 1) {
					if ( $retorno = $this->registernewesp() ) { //REGISTRAR EL MEDICO EN LA BD	
						$this->output .= $this->displayConfirmation($this->l('La especialidad médica se registró correctamente.'));
					} else {
						$this->output .= $this->adminDisplayWarning($this->l('La especialidad médica NO se registró correctamente.'.$this->errores));
					}
					return $this->output . $this->displayForm();
				} else {
					return $this->output . $this->displayCreaEspecialidad();
				}
				
				break; 

			case 'actuesp': 
				switch (Tools::getValue('step_opc')) {
	    			case 1 :
						if ( Tools::getValue('especialidadmod') != '' ) {

							//require(dirname(__FILE__).'\..\..\classes\Logging.php');							

	    					$this->cargaEspecialidadPorIdEspecialidad( Tools::getValue('especialidadmod') );
		    				return $this->output . $this->displayModiEspecialidad();
						} else {
		    				$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios, Seleccione la especialidad a modificar.'.$this->errores));
		    				return $this->output . $this->displayForm();
		    			}
	    				break;

	    			case 2 : 
	    				if ( $validado ) {
		    				if ($retorno = $this->registeroldesp()) {
		    					$this->output .= $this->displayConfirmation($this->l('La especialidad médica se modificó correctamente.'));
		    				} else {
		    					$this->output .= $this->adminDisplayWarning($this->l('Ocurrio un error al modificar la especialidad médica.'.$this->errores));
		    				}
		    				return $this->output . $this->displayForm();
		    			} else {
		    				$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios.'.$this->errores));
		    				return $this->output . $this->displayModiEspecialidad();
		    			}
	    				break;
	    		}
				return $this->output . $this->displayModiEspecialidad();
			break;

			default:	$this->output.= "<div style='width:100%; float: left;'> <br> Opción no disponible </div>";

			break;
		}

	    	return $this->output . $this->displayForm();
		} elseif(Tools::isSubmit('submitAdminMedicos')) {
			$this->output .= $this->displayConfirmation($this->l('No seleccionó ninguna opción.'));
			return $this->output . $this->displayForm();
        } else {
            return $this->displayForm();
        }
    }

	public function validarCampos() {

		$this->error=0;

		foreach ($this->lista_campos_input1 as $key => $value) {
			// RECORRO TODOS LOS CAMPOS DE MI FORMULARIO DINAMICO
				
			if( $value['tipo'] == "selectm") { 
				// PARA CAMPOS DE SELECCION MULTIPLE
				$key = trim(trim($key, ']'), '[');
				// echo "<br>".$key." req: ".$value['requerido']." val: -".Tools::getValue($key)."- size: -".is_array(Tools::getValue($key))."-dep: -".Tools::getValue($value['campo_dependencia'])."- acti: -".$value['valor_dependencia']."-";
				if (is_array(Tools::getValue($key))  && count(Tools::getValue($key)) > 0 ) {
					$campo_valida = 1;
				}
			} elseif ( trim(Tools::getValue($key)) == "" || trim(Tools::getValue($key)) == null ){ 
				// PARA OTROS CAMPO SI NO TIENEN VALORES
				$campo_valida = 0;
			} else { 
				// PARA OTROS CAMPOS SI TIENEN VALOR
				$campo_valida = 1;
			}

			if ($value['requerido'] == 'si' &&  $campo_valida == 0) { 
				// SI EL CAMPO ES REQUERIDO Y NO TIENE VALOR 

				$this->error++;
				$this->errores .= "<br> Campo [ ".$key." ]  sin valor requerido.";

			} elseif ($value['requerido'] == 'depende' && (Tools::getValue($value['campo_dependencia']) == $value['valor_dependencia']) && $campo_valida == 0 ) {	    									
				// SI EL CAMPO DEPENDE DE OTRO Y NO TIENE VALOR
				$this->error++;
				$this->errores .= "<br> Campo dependiente [ ".$key." ] sin valor requerido.";
			}

			if ( is_array(Tools::getValue($key)) ) {  // PARA COLOCAR EN ARRAY CON VALORES Y CAMPOS EL ARRAY ORIGINAL O LOS CAMPOS LIMPIOS
				$this->bd_fielsd_save[$key] = Tools::getValue($key);				
			} else {
				$this->bd_fielsd_save[$key] = $this->remomeCharSql(trim(Tools::getValue($key)));
			}
		}

		if ($this->error == 0) { 
			// SI NO EXISTE ALGUN ERROR EN CUANTO LAS VALIDACIONES
			return true;	

		} else {
			//$this->output .= $this->adminDisplayWarning($this->l('No se enviaron todos los datos necesarios.'.$this->errores));
			return false;
		}
			
	}


	public function remomeCharSql($string, $length = NULL){
		$string = trim($string);
	        
	        $array=array("\"","$","&","'","(",")","*","+",",","-","/",":",";","<","=",">","?","[","]","^","`","{","|","}","~");
		$string = utf8_decode($string);
		$string = htmlentities($string, ENT_NOQUOTES| ENT_IGNORE, "UTF-8");
		$string = str_replace($array, "", $string);        
	        $string = preg_replace( "/([ ]+)/", " ", $string );
		
		$length = intval($length);
		if ($length > 0){
			$string = substr($string, 0, $length);
		}
		return $string;
	}


    public function listaEspecialidadesMedicas() {

    	$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'especialidad_medica ORDER BY nombre ASC';
        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $valores) {

               $especialidades_lista[$valores['id_especialidad']]=$valores['nombre'];
            }
        }

        $this->context->smarty->assign(array('especialidades'=> $especialidades_lista));
        
    }


    public function listaEmpleadosVisitadores() { // por definir perfil visitadores medicos

    	$sql = 'SELECT id_employee, firstname, lastname  FROM ' . _DB_PREFIX_ . 'employee WHERE id_profile = 9 AND active = 1 ORDER BY firstname ASC';
    	
    	$especialidades_lista[''] = ' -- Seleccione -- ';
        
        if ($results = Db::getInstance()->ExecuteS($sql)) {
        	
            foreach ($results as $valores) {
            
               $especialidades_lista[$valores['id_employee']]=$valores['firstname']." ".$valores['lastname'];
            }
        }

        $this->context->smarty->assign(array('empleados'=> $especialidades_lista));
        
    }

/*
    public function listaVisitadorMedico($id_visitador = null) {

    	$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'visitador_medico WHERE activo = "si" ';

    	if($id_visitador != '' && $id_visitador != null) {
    		$sql .= ' AND id_visitador = '.$id_visitador;
    	}

    	$sql.=' ORDER BY nombre_visitador ASC';
    	$visitadores_lista[''] = ' -- Seleccione -- ';

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $valores) {            
               $visitadores_lista[$valores['id_visitador']]=$valores['nombre_visitador'];
            }
        }

        $this->context->smarty->assign(array('visitadores'=> $visitadores_lista));        
    } 

    public function listaVisitadorMedicoFull($id_visitador = null) {

    	$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'visitador_medico';

    	if($id_visitador != '' && $id_visitador != null) {
    		$sql .= ' WHERE id_visitador = '.$id_visitador;
    	}

    	$sql.=' ORDER BY nombre_visitador ASC';
    	$visitadores_lista[''] = ' -- Seleccione -- ';

        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $valores) {            
               $visitadores_lista[$valores['id_visitador']]=$valores['nombre_visitador'];
            }
        }

        $this->context->smarty->assign(array('visitadoresall'=> $visitadores_lista));        
    } 


    public function cargaVisitadorPorIdVisitador($id_visitador) {

    	$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'visitador_medico';

    	if($id_visitador != '' && $id_visitador != null) {
    		$sql .= ' WHERE id_visitador = '.$id_visitador;
    	}

        if ($results = Db::getInstance()->ExecuteS($sql)) {

	        $valores = $results[0];

	        	$this->context->smarty->assign('id_visitador' , $valores['id_visitador']);
	        	$this->context->smarty->assign('nombre_visitador' , $valores['nombre_visitador']);
				$this->context->smarty->assign('activo' , $valores['activo']);
				$this->context->smarty->assign('id_empleado' , $valores['id_empleado']);
				
		}        
    }


*/



    public function cargaMedicoPorIdCupon($id_cupon) {

    	//echo "<br>".
    	$sql = 'SELECT m.*, GROUP_CONCAT( me.id_especialidad ) especialidad FROM ps_medico m
			LEFT JOIN ps_medico_rule mr ON ( mr.id_medico = m.id_medico )
			LEFT JOIN ps_medic_especialidad me ON ( me.id_medico = m.id_medico )
			WHERE mr.id_cart_rule = "'.$id_cupon.'"
			GROUP BY me.id_medico';

        if ($results = Db::getInstance()->ExecuteS($sql)) {      

	        $valores = $results[0];

				$this->context->smarty->assign('especialm' , explode(',', $valores['especialidad']));
				$this->context->smarty->assign('id_medico' , $valores['id_medico']);
				$this->context->smarty->assign('nombre' , $valores['nombre']);
				$this->context->smarty->assign('domicilio' , $valores['domicilio']);
				$this->context->smarty->assign('telefono' , $valores['telefono']);
				$this->context->smarty->assign('localidad' , $valores['localidad']);
				$this->context->smarty->assign('id_ciudad' , $valores['id_ciudad']);
				$this->context->smarty->assign('region' , $valores['region']);
				$this->context->smarty->assign('contrato' , $valores['contrato']);
				$this->context->smarty->assign('activo' , $valores['activo']);
				$this->context->smarty->assign('banco' , $valores['banco']);
				$this->context->smarty->assign('num_cuenta' , $valores['num_cuenta']);
				$this->context->smarty->assign('beneficio' , $valores['beneficio']);
				$this->context->smarty->assign('cedula' , $valores['cedula']);
				$this->context->smarty->assign('email' , $valores['email']);
				$this->context->smarty->assign('valor_comision' , $valores['valor_comision']);
				$this->context->smarty->assign('porc_rango_menor' , $valores['porc_rango_menor']);
				$this->context->smarty->assign('porc_rango_mayor' , $valores['porc_rango_mayor']);
				$this->context->smarty->assign('observacion' , $valores['observacion']);
				$this->context->smarty->assign('beneficiario' , $valores['beneficiario']);
				$this->context->smarty->assign('id_visitador' , $valores['id_visitador']);
		}        
        
    }


    public function cargaEspecialidadPorIdEspecialidad($id_especialidad) {

    	$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'especialidad_medica ';

        if($id_especialidad != '' && $id_especialidad != null) {
    		$sql .= ' WHERE id_especialidad = '.$id_especialidad;
    	}

        if ($results = Db::getInstance()->ExecuteS($sql)) {      

	        $valores = $results[0];				

	        	$this->context->smarty->assign('id_especialidad' , $valores['id_especialidad']);
	        	$this->context->smarty->assign('nombre' , $valores['nombre']);
		}        
    }


    public function cargaDatosBd($id_perfil, $modulo, $formulario, $accion) {

    	$sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'campos_formulario ';

        if(isset($id_perfil) && 
        	isset($modulo)  && 
        	isset($formulario)  &&
        	isset($accion) ) {
    		$sql .= ' WHERE id_perfil = "'.$id_perfil.'"';
	    	$sql .= ' AND formulario = "'.$formulario.'"';
	    	$sql .= ' AND modulo = "'.$modulo.'"';
	    	$sql .= ' AND accion = "'.$accion.'"';
    	} else {
    		return false;
    	}
//echo $sql;
        if ($results = Db::getInstance()->ExecuteS($sql)) {      

 			foreach ($results as $valores) {
				/*       
				id_campo, campo, tipo, lista_select, predeterminado, tamano, requerido, requeridomensaje, extra, extramensaje, 
				dependencia, campo_dependencia, valor_dependencia, id_perfil, nombre, formulario, modulo, accion 
				*/

				$this->lista_campos_input1[$valores['campo']]['id_campo'] = $valores['id_campo'];
				$this->lista_campos_input1[$valores['campo']]['campo'] = $valores['campo'];
				$this->lista_campos_input1[$valores['campo']]['tipo'] = $valores['tipo'];
				$this->lista_campos_input1[$valores['campo']]['lista_select'] = $valores['lista_select'];
				$this->lista_campos_input1[$valores['campo']]['mostrar'] = $valores['mostrar'];
				$this->lista_campos_input1[$valores['campo']]['tamano'] = $valores['tamano'];
				$this->lista_campos_input1[$valores['campo']]['requerido'] = $valores['requerido'];
				$this->lista_campos_input1[$valores['campo']]['requeridomensaje'] = $valores['requeridomensaje'];
				$this->lista_campos_input1[$valores['campo']]['campo_dependencia'] = $valores['campo_dependencia'];
				$this->lista_campos_input1[$valores['campo']]['valor_dependencia'] = $valores['valor_dependencia'];
				$this->lista_campos_input1[$valores['campo']]['extra'] = $valores['extra'];
				$this->lista_campos_input1[$valores['campo']]['extramensaje'] = $valores['extramensaje'];
				$this->lista_campos_input1[$valores['campo']]['id_perfil'] = $valores['id_perfil'];
				$this->lista_campos_input1[$valores['campo']]['nombre'] = $valores['nombre'];
				$this->lista_campos_input1[$valores['campo']]['formulario'] = $valores['formulario'];
				$this->lista_campos_input1[$valores['campo']]['modulo'] = $valores['modulo'];
				$this->lista_campos_input1[$valores['campo']]['accion'] = $valores['accion'];

           	}

           	$this->context->smarty->assign(array('inputs'=> $this->lista_campos_input1));
		}

    }


    public function registernewmed() {

    	$campos_a_guardar = array("id_medico", "nombre", "domicilio", "telefono", "localidad", "region", "contrato", "activo", "banco", "num_cuenta",
    	 "beneficio", "cedula", "email", "valor_comision", "porc_rango_menor", "porc_rango_mayor", "observacion", "beneficiario", "id_visitador");
    	// INSERTAR REGLAS NUEVAS , REGLAS POR MÉDICO
    	$campos_s = '';
    	$datos_s = '';

    	foreach ($campos_a_guardar as $key => $value) {
    		$campos_s .= ''.$value.',';
    		
    		/*if ( $value == "id_visitador" && $this->permisos_basicos == 3 ) { //PARA REGISTRAR EL MEDICO CON EL VISITADOR ACTUALMENTE LOGUEADO
    			$this->bd_fielsd_save[$value] = $this->context->employee->id;    			
    		}*/
    		if($this->bd_fielsd_save[$value] == '') {
    			$datos_s .= "null,";
    		} else {
				$datos_s .= '"'.$this->bd_fielsd_save[$value].'",';
			}

    		
    		
    	}

		$campos_s = trim($campos_s, ','); // ELIMINAR ULTIMO CARACTER CADENA
		$datos_s = trim($datos_s, ','); // ELIMINAR ULTIMO CARACTER CADENA

    	$ins_medico = "INSERT INTO ". _DB_PREFIX_ ."medico (".$campos_s.") VALUES (".strtoupper($datos_s).")";

    	if ($results_ins_medico = Db::getInstance()->Execute($ins_medico)) {

    		/**
			 * [$save_log Guardar log de medico Luego de insertar]
			 * @var string
			 */
			$save_log = '{ "RegistrarMedico" : {';			
				$save_log .= '"Id_medico" : "'.$this->bd_fielsd_save['id_medico'].'" }, '.$this->json_empleado.'}';

			$loggin = new Registrolog();
			$loggin->lwrite("AdminMedicos", "log_medicos.txt", $save_log);

			/*****FIN LOG******/


    		$ins_regla = "INSERT INTO ". _DB_PREFIX_ ."cart_rule (`date_from`, `date_to`, `description`, `quantity`, `quantity_per_user`, `priority`, 
			 `code`, `minimum_amount_currency`, `reduction_percent`, `reduction_currency`, `active`, `date_add`, `date_upd`)
			SELECT '".date("Y-m-d 00:00:00")."','".date("Y-m-d 00:00:00", strtotime('+3 year'))."', 'Apoyo Salud', 500, 500, 1, med.nombre, 1, 5, 1, 1, 
			now(), now() FROM ". _DB_PREFIX_ ."medico med WHERE med.id_medico = ".$this->bd_fielsd_save['id_medico'];

			$medico_closeup = $this->bd_fielsd_save['id_medico'];

			if ( $results_ins_regla = Db::getInstance()->Execute($ins_regla) ) {

				$id_rule = Db::getInstance()->Insert_ID();

				$ins_rule_lang = "INSERT INTO ". _DB_PREFIX_ ."cart_rule_lang (`id_cart_rule`, `id_lang`,`name`)
					SELECT id_cart_rule, 1, 'Apoyo Salud' FROM ". _DB_PREFIX_ ."cart_rule WHERE id_cart_rule = ".$id_rule;

				if ( $results_ins_rule_lang = Db::getInstance()->Execute($ins_rule_lang) ) {
					
					$ins_espe_med = "INSERT INTO ". _DB_PREFIX_ ."medic_especialidad (id_medico, id_especialidad ) VALUES ";

					foreach ($this->bd_fielsd_save["especialm"] as $key => $value) {

						$ins_espe_med .= "(".$medico_closeup.",".$value."),";
					}
					$ins_espe_med = trim($ins_espe_med, ',');

					if ( $result_ins_espe_med = Db::getInstance()->ExecuteS($ins_espe_med)) {

						$ins_regla_medico = "INSERT INTO ". _DB_PREFIX_ ."medico_rule (`id_cart_rule`, `id_medico`)
						VALUES ( ".$id_rule.",".$medico_closeup.")";

						if ( $results_ins_regla_medico = Db::getInstance()->Execute($ins_regla_medico) ) {
							return true;
						} else {
							$this->errores .= "<br> No se pudo relacionar la regla del carrito con el médico. Id_cart_rule: ".$id_rule.", Id_médico: ".$medico_closeup."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
							return false;
						}
					} else {
						$this->errores .= "<br> No se pudo relacionar las especialidades con el médico. Especialidades: ".$ins_espe_med.", Id_médico: ".$medico_closeup."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
						return false;
					}
				} else {
					$this->errores .= "<br> No se pudo insertar el lenguaje de la regla del carrito, Id_cart_rule: ".$id_rule."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
					return false;
				}

			} else {
				$this->errores .= "<br> No se pudo insertar la regla del carrito, Código close-up: ".$medico_closeup."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
				return false;
			}

    	} else {    		
    		$this->errores .= "<br> No se pudo registrar el cliente, posible código close-up duplicado: ".$medico_closeup."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
    		return false;
    	}

        return false;
    }


    public function registeroldmed() { // MODIFICAR MEDICOS 

    	$campos_a_modificar = array("id_medico", "nombre", "domicilio", "telefono", "localidad", "region", "contrato", "activo", "banco", "num_cuenta",
    	 "beneficio", "cedula", "email", "valor_comision", "porc_rango_menor", "porc_rango_mayor", "observacion", "beneficiario", "id_visitador");
    	// INSERTAR REGLAS NUEVAS , REGLAS POR MÉDICO
    	$campos_s = '';
    	$datos_s = '';

    	foreach ($campos_a_modificar as $key => $value) {

    		$campos_s .= ' med.'.$value.',';
    	}

		$campos_s = trim($campos_s, ','); // ELIMINAR ULTIMO CARACTER CADENA

    	$query_old_datos = "SELECT ".$campos_s.", GROUP_CONCAT( espme.id_especialidad ) especialidad FROM ". _DB_PREFIX_ ."medico med
    	LEFT JOIN ". _DB_PREFIX_ ."medic_especialidad espme ON ( espme.id_medico = med.id_medico ) WHERE med.id_medico = ".$this->bd_fielsd_save['id_medico'];

		if ($result_old_datos = Db::getInstance()->ExecuteS($query_old_datos)) {
			// SELECCIONAR DATOS ACTUALES DEL MEDICO
			
			/**
			 * [$save_log Guardar los de medico antes de modificar]
			 * @var string
			 */
			$save_log = '{ "ModificarMedico" : {';
			foreach ($campos_a_modificar as $key => $value) {
				$save_log .= '"'.$value.'" : "'.$result_old_datos[0][$value].'", ';
			}
			$save_log .= '"especialidad" : "'.$result_old_datos[0]['especialidad'].'"}, '.$this->json_empleado.'}';

			$loggin = new Registrolog();
			$loggin->lwrite("AdminMedicos", "log_medicos.txt", $save_log);

			/*****FIN LOG******/

			$query_datos_up = "UPDATE ". _DB_PREFIX_ ."medico SET ";

			foreach ($campos_a_modificar as $key => $value) {
				if ($value != "id_medico") {
				
					/*if ( $value == "id_visitador" && $this->permisos_basicos == 3 ) { //PARA REGISTRAR EL MEDICO CON EL VISITADOR ACTUALMENTE LOGUEADO
		    			$this->bd_fielsd_save[$value] = $this->context->employee->id;    			
		    		}*/
		    		if($this->bd_fielsd_save[$value] == '') {
		    			$query_datos_up .= " ".$value." = null,";
		    		} else {
						$query_datos_up .= " ".$value." = '".strtoupper($this->bd_fielsd_save[$value])."',";
					}
				}
			}

			$query_datos_up = trim($query_datos_up, ','); // ELIMINAR ULTIMO CARACTER CADENA
			$query_datos_up .= " WHERE id_medico = ".$this->bd_fielsd_save['id_medico'];

			if ($result_datos_up = Db::getInstance()->ExecuteS($query_datos_up)) {
				// ACTUALIZAR INFORMACIÓN DEL MEDICO

				$query_del_especialidad = "DELETE FROM ". _DB_PREFIX_ ."medic_especialidad WHERE id_medico = ".$this->bd_fielsd_save['id_medico'];

				if ($result_del_especialidad = Db::getInstance()->ExecuteS($query_del_especialidad)) {
					// ELIMINAR ESPECIALIDADES DEL MEDICO

					$ins_espe_med = "INSERT INTO ". _DB_PREFIX_ ."medic_especialidad (id_medico, id_especialidad ) VALUES ";

					foreach ($this->bd_fielsd_save["especialm"] as $key => $value) {

						$ins_espe_med .= "(".$this->bd_fielsd_save["id_medico"].",".$value."),";
					}
					$ins_espe_med = trim($ins_espe_med, ',');

					if ( $result_ins_espe_med = Db::getInstance()->ExecuteS($ins_espe_med)) {
						//INSERTAR NUEVAS ESPECIALIDADES DEL MEDICO
						
						return true;
					} else {
						$this->errores .= "<br> No se pudieron ingresar las especialidades del médico: ".$this->bd_fielsd_save['id_medico']."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
						return false;
					}
				} else {
					$this->errores .= "<br> No se pudieron eliminar las especialidades del médico: ".$this->bd_fielsd_save['id_medico']."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
					return false;
				}
			} else {
				$this->errores .= "<br> No se pudieron actualizar los datos del médico: ".$this->bd_fielsd_save['id_medico']."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
				return false;
			}
		} else {
			$this->errores .= "<br> No se pudieron obtener los datos del médico: ".$this->bd_fielsd_save['id_medico']."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
			return false;
		}
		
        return false;

    }


    /**
     * REGRISTRAR NUEVO VISITADOR MEDICO
     */
    
    public function registernewvis() {
    	

    	$campos_a_guardar = array("nombre_visitador", "activo", "id_empleado");//);
    	// INSERTAR VISITADOR MÉDICO
 
    	$campos_s = '';
    	$datos_s = '';

    	foreach ($campos_a_guardar as $key => $value) {
    		$campos_s .= ''.$value.',';
    		$datos_s .= '"'.$this->bd_fielsd_save[$value].'",';
    	}

		$campos_s = trim($campos_s, ','); // ELIMINAR ULTIMO CARACTER CADENA
		$datos_s = trim($datos_s, ','); // ELIMINAR ULTIMO CARACTER CADENA

    	$ins_visitador_medico = "INSERT INTO ". _DB_PREFIX_ ."visitador_medico (".$campos_s.") VALUES (".$datos_s.")";

    	if ($results_ins_visitador_medico = Db::getInstance()->Execute($ins_visitador_medico)) {
    		
    		/**
			 * [$save_log Guardar los de medico antes de modificar]
			 * @var string
			 */
			$save_log = '{ "RegistrarVisitador" : {';			
				$save_log .= '"nombre_visitador" : "'.$this->bd_fielsd_save['nombre_visitador'].'" }, '.$this->json_empleado.'}';

			$loggin = new Registrolog();
			$loggin->lwrite("AdminMedicos", "log_visitador.txt", $save_log);

			/*****FIN LOG******/

    		return true;
    	} else {
    		$this->errores .= "<br> No se pudo registrar el visitador, contacte a su administrador."."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
    		return false;
    	}

//        $perfil = Profile::getProfile($this->context->employee->id_profile);

        return false;
    
    }

    
    public function registeroldvis() {

    	$campos_a_modificar = array("nombre_visitador", "activo", "id_empleado");//, "id_empleado");
    	// MODIFICAR VISITADOR MÉDICO
    	//  
		
		$query_old_datos = "SELECT * FROM ". _DB_PREFIX_ ."visitador_medico
		WHERE id_visitador = ".Tools::getValue('id_visitador');

		if ($result_old_datos = Db::getInstance()->ExecuteS($query_old_datos)) {

			/**
			 * [$save_log Guardar los de medico antes de modificar]
			 * @var string
			 */
			$save_log = '{ "ModificarVisitador" : {';

			$save_log .= '"id_visitador" : "'.Tools::getValue('id_visitador').'", ';

			foreach ($campos_a_modificar as $key => $value) {
				$save_log .= '"'.$value.'" : "'.$result_old_datos[0][$value].'", ';
			}
			$save_log .= '}, '.$this->json_empleado.'}';

			$loggin = new Registrolog();
			$loggin->lwrite("AdminMedicos", "log_visitador.txt", $save_log);

			/*****FIN LOG******/


	    	$query_datos_up = "UPDATE ". _DB_PREFIX_ ."visitador_medico SET ";

				foreach ($campos_a_modificar as $key => $value) {				
					$query_datos_up .= " ".$value." = '".$this->bd_fielsd_save[$value]."',";
				}

				$query_datos_up = trim($query_datos_up, ','); // ELIMINAR ULTIMO CARACTER CADENA
				$query_datos_up .= " WHERE id_visitador = ".Tools::getValue('id_visitador');

echo $query_datos_up;

				if ($result_datos_up = Db::getInstance()->ExecuteS($query_datos_up)) {			

					return true;
			} else {
				$this->errores .= "<br> No se pudo modificar el visitador, contacte a su administrador."."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
				return false;
			}
		} else {
			$this->errores .= "<br> No se pudieron obtener los datos del visitador: ".$this->bd_fielsd_save['id_visitador']."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
			return false;
		}

        return false;
    }

    public function registernewesp() {

    	$campos_a_guardar = array("nombre");//, "id_empleado");
    	// INSERTAR ESPECIALIDAD MÉDICA
 
    	$campos_s = '';
    	$datos_s = '';

    	foreach ($campos_a_guardar as $key => $value) {
    		$campos_s .= ''.$value.',';
    		$datos_s .= '"'.$this->bd_fielsd_save[$value].'",';
    	}

		$campos_s = trim($campos_s, ','); // ELIMINAR ULTIMO CARACTER CADENA
		$datos_s = trim($datos_s, ','); // ELIMINAR ULTIMO CARACTER CADENA

    	$ins_visitador_medico = "INSERT INTO ". _DB_PREFIX_ ."especialidad_medica (".$campos_s.") VALUES (".strtoupper($datos_s).")";

    	if ($results_ins_visitador_medico = Db::getInstance()->Execute($ins_visitador_medico)) {
    		/**
			 * [$save_log Guardar los de medico antes de modificar]
			 * @var string
			 */
			$save_log = '{ "RegistrarEspecialidad" : {';			
			$save_log .= '"nombre" : "'.$this->bd_fielsd_save['nombre'].'" }, '.$this->json_empleado.'}';

			$loggin = new Registrolog();
			$loggin->lwrite("AdminMedicos", "log_especialidad.txt", $save_log);

			/*****FIN LOG******/

    		return true;
    	} else {
    		$this->errores .= "<br> No se pudo registrar la especialidad, contacte a su administrador."."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
    		return false;
    	}

        return false;
    }

    public function registeroldesp() {

    	$campos_a_modificar = array("nombre");//, "id_empleado");
    	// MODIFICAR VISITADOR MÉDICO
		
		$query_old_datos = "SELECT * FROM ". _DB_PREFIX_ ."especialidad_medica
		WHERE id_especialidad = ".Tools::getValue('id_especialidad');

		if ($result_old_datos = Db::getInstance()->ExecuteS($query_old_datos)) {

			/**
			 * [$save_log Guardar los de medico antes de modificar]
			 * @var string
			 */
			$save_log = '{ "ModificarVisitador" : {';

			$save_log .= '"id_especialidad" : "'.Tools::getValue('id_especialidad').'", ';

			foreach ($campos_a_modificar as $key => $value) {
				$save_log .= '"'.$value.'" : "'.$result_old_datos[0][$value].'", ';
			}
			$save_log .= '}, '.$this->json_empleado.'}';

			$loggin = new Registrolog();
			$loggin->lwrite("AdminMedicos", "log_especialidad.txt", $save_log);

			/*****FIN LOG******/

	    	$query_datos_up = "UPDATE ". _DB_PREFIX_ ."especialidad_medica SET ";

				foreach ($campos_a_modificar as $key => $value) {				
					$query_datos_up .= " ".$value." = '".strtoupper($this->bd_fielsd_save[$value])."',";
				}

				$query_datos_up = trim($query_datos_up, ','); // ELIMINAR ULTIMO CARACTER CADENA
				$query_datos_up .= " WHERE id_especialidad = ".Tools::getValue('id_especialidad');

				if ($result_datos_up = Db::getInstance()->ExecuteS($query_datos_up)) {
					return true;
				} else {
					$this->errores .= "<br> No se pudo modificar la especialidad, contacte a su administrador."."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
					return false;
				}

		} else {
			$this->errores .= "<br> No se pudieron obtener los datos de la especialidad: ".$this->bd_fielsd_save['id_visitador']."<br> Mensaje Error: ".Db::getInstance()->getMsgError();
			return false;
		}

        return false;
    }


// muestra el formulario principal del modulo
    public function displayForm() {
    	$this->listaEspecialidadesMedicas();
    	// $this->listaVisitadorMedico();
    	// $this->listaVisitadorMedicoFull();
        return $this->display(__FILE__, 'tpl/formulario.tpl');
    }



// Muestra el formulario de creación de cupones médicos
    public function displayCreaCuponPru() {
    	$this->listaEmpleadosVisitadores();
    	$this->context->smarty->assign('si_no' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO'));
    	$this->context->smarty->assign('si_no_cede' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO', 'CEDIDO' => 'CEDIDO'));
        return $this->display(__FILE__, 'tpl/crea_cupon_pru.tpl');
    }

// Muestra el formulario de creación de cupones médicos
    public function displayCreaCupon() {
    	require_once('ajax_listado_b.php');
    	$this->context->smarty->assign('listbancos', $str_nombancos);
    	$this->listaEmpleadosVisitadores();
    	$this->context->smarty->assign('si_no' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO'));
    	$this->context->smarty->assign('si_no_cede' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO', 'CEDIDO' => 'CEDIDO'));
        return $this->display(__FILE__, 'tpl/crea_cupon_medico.tpl');
    }

// Muestra el formulario de actualización de cupones médicos
    public function displayModiCupon() {
    	require_once('ajax_listado_b.php');
    	$this->context->smarty->assign('listbancos', $str_nombancos);
    	$this->listaEmpleadosVisitadores();
    	$this->context->smarty->assign('si_no' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO'));
    	$this->context->smarty->assign('si_no_cede' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO', 'CEDIDO' => 'CEDIDO'));
        return $this->display(__FILE__, 'tpl/modi_cupon_medico.tpl');
    }
/*
// Muestra el formulario de creación de visitadores médicos
    public function displayCreaVisitador() {
    	$this->listaEmpleadosVisitadores();
    	$this->context->smarty->assign('si_no' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO'));
        return $this->display(__FILE__, 'tpl/crea_visitador_medico.tpl');
    }

// Muestra el formulario de actualización de cupones médicos
    public function displayModiVisitador() {
    	$this->listaEmpleadosVisitadores();
    	$this->context->smarty->assign('si_no' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO'));
        return $this->display(__FILE__, 'tpl/modi_visitador_medico.tpl');
    }
*/
    // Muestra el formulario de creación de visitadores médicos
    public function displayCreaEspecialidad() {
    	$this->context->smarty->assign('si_no' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO'));
        return $this->display(__FILE__, 'tpl/crea_especialidad_medica.tpl');
    }

// Muestra el formulario de actualización de cupones médicos
    public function displayModiEspecialidad() {
    	$this->context->smarty->assign('si_no' , array('' => ' -- Seleccione -- ','SI' => 'SI', 'NO' => 'NO'));
        return $this->display(__FILE__, 'tpl/modi_especialidad_medica.tpl');
    }

       
}

?>
