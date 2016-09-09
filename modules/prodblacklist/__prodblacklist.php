<?php

if (!defined('_PS_VERSION_'))
    exit;

class ProdBlackList extends Module {

    //private $_html = '';
    //private $_postErrors = array();
    //private $_msg='';
    private $select_limit = 100;
    private $offset = null;
    private $pag = null;
    private $total__rows = 0;
    private $output = '';
    private $prodblacklistMsg = '';

    function __construct() {  //informacion del modulo
        $this->name = 'prodblacklist';
        $this->tab = 'Back_office_features';
        $this->version = '1.0 Betha';
        $this->author = 'Farmalisto';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Productos en lista negra');
        $this->description = $this->l('Envía productos a la lista negra.');

        $this->context->smarty->assign('base_dir', __PS_BASE_URI__);
        $this->context->smarty->assign('prodblacklistLogo', '<img src="' . $this->_path . 'logo.gif" alt="Lista negra de productos" title="Lista negra de productos" />');
        $this->context->smarty->assign('prodblacklistPath', $this->_path);
        $this->context->smarty->assign('pathModule', dirname(__FILE__));
        $this->context->smarty->assign('prodblacklistMsg', $this->prodblacklistMsg);
        $this->context->smarty->assign('employee', $this->context->employee);

        $this->init_page();
        $this->rows_page();
    }

    public function install() { // parametros de instalación del modulo
        if (!$id_tab = Tab::getIdFromClassName('Adminprodblacklist')) {  // para crear acceso en menu back office / clase creada
            $tab = new Tab(); 
            $tab->class_name = 'Adminprodblacklist';  //la clase que redirecciona el link del menu a la configuracion
            $tab->module = 'prodblacklist'; // nombre del modulo creado
            $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog'); //aparecerá al final del menú catalogo
            
            $query1="CREATE TABLE IF NOT EXISTS  `ps_black_motivo` (
  `id_black_motivo` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_black_motivo`)
) ENGINE=Aria AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1;

CREATE TABLE IF NOT EXISTS  `ps_log_prod_blac_list` (
  `id_log` int(11) NOT NULL AUTO_INCREMENT,
  `id_employee` int(11) DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `descripcion` text,
  PRIMARY KEY (`id_log`)
) ENGINE=Aria AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1;

CREATE TABLE IF NOT EXISTS  `ps_product_black_list` (
  `id_prod_black` int(11) NOT NULL AUTO_INCREMENT,
  `id_emp` int(11) DEFAULT NULL,
  `id_product` int(11) DEFAULT NULL,
  `status` bit(1) DEFAULT NULL,
  `motivo` int(255) DEFAULT NULL,
  `descripcion` text CHARACTER SET latin1 COLLATE latin1_spanish_ci,
  `date` datetime DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_prod_black`)
) ENGINE=Aria AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1;

INSERT INTO `ps_black_motivo` VALUES ('1', 'Producto sale del mercado', ' Producto sale del mercado', '1');
INSERT INTO `ps_black_motivo` VALUES ('2', 'Producto controlado','Producto controlado', '2');
INSERT INTO `ps_black_motivo` VALUES ('3', 'Por componente activo no se puede vender', 'Por componente activo no se puede vender', '3');
";
 
 if (!$results = Db::getInstance()->ExecuteS($query1))
 {
 echo '<br><b>Error creando tablas .</b><br>';
 }
            
            foreach (Language::getLanguages(false) as $lang)
                $tab->name[(int) $lang['id_lang']] = 'Productos en lista negra'; // texto a mostrar en el menu
            if (!$tab->save())
                return $this->_abortInstall($this->l('Imposible crear la pestaña de nuevo'));
        }

        $this->_clearCache('prodblacklist.tpl');
        Configuration::updateValue('HOME_FEATURED_NBR', 8);

        if (!parent::install()) {
            return false;
        }

        return true;
    }

    public function uninstall() {  // desinstalacion
        $this->_clearCache('prodblacklist.tpl');
        return parent::uninstall();
    }

    // control de flujo del modulo      
    public function getContent() {

        if (Tools::isSubmit('submitProdBlackList')) {
            if (isset($_POST['reference_prod']) && $_POST['reference_prod'] && isset($_POST['option_search']) && isset($_POST['option_search'])) {
                if ($_POST['option_search'] == 'search_black_list') {
                    return $this->search_black_list_products();
                } elseif ($_POST['option_search'] == 'search_product') {
                    return $this->search_products();
                }
            } else {
                $this->output .= $this->adminDisplayWarning($this->l('Debes introducir una referencia para buscar.'));
                return $this->output . $this->displayForm();
            }
            // captura de parametros get (habilitar producto)
        } elseif (isset($_GET['option']) && $_GET['option'] == 'enabled' && isset($_GET['reference_prod']) && $_GET['reference_prod'] != '') {
            $reference_prod = addslashes($this->filtro($_GET['reference_prod']));
            $this->enabledProd($reference_prod);
            return $this->black_list_products();
        } elseif (isset($_POST['submit_black_list']) && $_POST['submit_black_list']) {
            $this->sendBlackList();
            return $this->output . $this->displayForm();
        } else {
            return $this->black_list_products();
        }
    }
    
    // hbilitar una referencia (sacar de la lista negra un producto')  
    function sendBlackList() {
        if (isset($_POST['id_emp']) && isset($_POST['motivo']) && isset($_POST['status']) && isset($_POST['id_product']) && isset($_POST['ref_product']) && isset($_POST['descripcion'])) {

            $flag1 = false;
            $flag2 = false;
            try {
                $id_emp = addslashes($this->filtro($_POST['id_emp']));
                $motivo = addslashes($this->filtro($_POST['motivo']));
                //$status=addslashes(filtro ($_POST['status']));
                $id_product = addslashes($this->filtro($_POST['id_product']));
                $ref_product = addslashes($this->filtro($_POST['ref_product']));
                $descripcion = addslashes($this->filtro($_POST['descripcion']));

                $query = 'update ps_product prod INNER JOIN ps_product_shop prods ON(prod.id_product=prods.id_product)
SET prod.active=0, prods.active=0
WHERE prod.reference="'.$ref_product .'"';

                if (DB::getInstance()->execute($query)) {
                    $flag1 = true;
                }
            } catch (Exception $exc) {

            $this->output .= $this->displayError($exc->getTraceAsString());
            }

            if ($flag1) {
                $query = 'INSERT INTO `' . _DB_PREFIX_ . 'product_black_list` (`id_emp`, `id_product`, `status`, `motivo`,`date`,`descripcion`,`reference` ) VALUES ';
                $query .= '(' . (int) $id_emp . ', ' . (int) $id_product . ',1, ' . $motivo . ',CURRENT_TIMESTAMP(),"' . $descripcion . '","' . $ref_product . '" ); ';

                if (DB::getInstance()->execute($query))
                    $flag2 = true;

                $query = 'INSERT INTO `' . _DB_PREFIX_ . 'log_prod_blac_list` (`id_employee`, `action`, `date`, `reference`,`descripcion` ) VALUES ';
                $query .= '(' . (int) $this->context->employee->id . ',"Bloquear" ,CURRENT_TIMESTAMP(),"' . $ref_product . '","' . $descripcion . '" ); ';
                DB::getInstance()->execute($query);
            }

            if ($flag1 && $flag2) {
                $this->output .= $this->displayConfirmation($this->l('El producto con la referencia (' . $ref_product . '). se envio a la lista negra.'));
                return true;
            } else {
                $this->output .= $this->displayError($this->l('Ocurrió un error inesperado,  es probable que los productos con la referencia (' . $ref_product . '). no esten bloqueados.'));
                return false;
            }
        }
    }
// habilita los prodcutos con una referencia
    function enabledProd($reference_prod) {
        $flag1 = false;
        try {
            $query = 'update ps_product prod INNER JOIN ps_product_shop prods ON(prod.id_product=prods.id_product)
                SET prod.active=1, prods.active=1
                WHERE prod.reference=' . $reference_prod;
            if (DB::getInstance()->execute($query))
                $flag1 = 1;
        } catch (Exception $exc) {
            $this->output .= $this->displayError($exc->getTraceAsString());
        }

        if ($flag1) {
            $query = 'DELETE FROM ps_product_black_list  WHERE  reference=' . $reference_prod;
            if (DB::getInstance()->execute($query)) {

                $query = 'INSERT INTO `' . _DB_PREFIX_ . 'log_prod_blac_list` (`id_employee`, `action`, `date`, `reference`,`descripcion` ) VALUES ';
                $query .= '(' . (int) $this->context->employee->id . ',"Desbloquear" ,CURRENT_TIMESTAMP(),"' . $reference_prod . '","Desbloquear" ); ';
                DB::getInstance()->execute($query);

                $this->output .= $this->displayConfirmation($this->l('Los productos con la referencia (' . $reference_prod . '). se actualizaron correctamente.'));
                return true;
            } else {

                $this->output .= $this->adminDisplayWarning($this->l('Los productos se activaron correctamente, pero no se removieron de la lista negra (' . $reference_prod . ').'));
                return false;
            }
        } else {
            $this->output .= $this->adminDisplayWarning($this->l('Los productos no se actualizaron correctamente (' . $reference_prod . ').'));
        }
    }

// muestra el formulario principal del modulo
    public function displayForm() {
        return $this->display(__FILE__, 'tpl/formulario.tpl');
    }

// Muestra el listado de los productos en lista negra
    public function displayBlackList() {
        return $this->display(__FILE__, 'tpl/black_list_products.tpl');
    }

// Muestra los productos que tienen la referencia de busqueda 
    public function displaySearchBlackList() {
        return $this->display(__FILE__, 'tpl/search_black_list_products.tpl');
    }

// Muestra los productos que tienen la referencia de busqueda, para agregar a la lista negra.
    public function displaySearchProducts() {
        return $this->display(__FILE__, 'tpl/search_products.tpl');
    }

// Conexión DB, buscar referencia para agregar a la lista negra
    function search_products() {

        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'black_motivo';
        if ($results = Db::getInstance()->ExecuteS($sql))
            $this->context->smarty->assign('select_motivo', $results);

        $reference_prod = addslashes($this->filtro($_POST['reference_prod']));

        $sql1 = 'select count(1) as total from ' . _DB_PREFIX_ . 'product prod INNER JOIN ' . _DB_PREFIX_ .
                'product_lang prodl ON(prod.id_product=prodl.id_product)'
                 . 'LEFT JOIN ps_product_black_list black ON(prod.reference=black.reference ) WHERE prod.reference= \'' . $reference_prod . '\' AND black.reference is NULL;';
        $sql2 = 'select prod.id_product, prod.reference, prodl.`name` from ' . _DB_PREFIX_ . 'product prod INNER JOIN ' . _DB_PREFIX_ .
                'product_lang prodl ON(prod.id_product=prodl.id_product)'
                . 'LEFT JOIN ps_product_black_list black ON(prod.reference=black.reference ) WHERE prod.reference= \'' . $reference_prod . '\' AND black.reference is NULL;';

        $this->paginar($sql1, $sql2, 0);

        $this->context->smarty->assign('reference_prod', $reference_prod);

        if ($this->total__rows > 0) {
            $this->output .= $this->displayConfirmation($this->l('Se encontraron (' . $this->total__rows . ') registros con el parámetro de búsqueda. (' . $_POST['reference_prod'] . ')'));
            return $this->output . $this->displaySearchProducts();
        } else {
            $this->output .= $this->adminDisplayWarning($this->l('No se encontraron coincidencias con el parámetro de búsqueda (' . $_POST['reference_prod'] . ').'));
            return $this->output . $this->displayForm();
        }
    }

//Conexión DB, buscar referencia en la lista negra 
    function search_black_list_products() {
        $reference_prod = addslashes($this->filtro($_POST['reference_prod']));

        $sql1 = "select count(1) as total 
from ps_product_black_list black INNER JOIN ps_product prod on (black.id_product=prod.id_product)
INNER JOIN ps_employee emp on(black.id_emp=emp.id_employee)
INNER JOIN ps_product_lang plang ON(prod.id_product=plang.id_product)
INNER JOIN ps_black_motivo mot ON(mot.id_black_motivo=black.motivo)
WHERE prod.reference ='" . $reference_prod . "'";

        $sq2 = "select black.id_prod_black, black.`status`,black.motivo, black.descripcion, black.date,
prod.reference, plang.`name`, prod.id_product, emp.email, emp.firstname, emp.lastname, emp.id_employee, mot.`name` as namem
from ps_product_black_list black INNER JOIN ps_product prod on (black.id_product=prod.id_product)
INNER JOIN ps_employee emp on(black.id_emp=emp.id_employee)
INNER JOIN ps_product_lang plang ON(prod.id_product=plang.id_product)
INNER JOIN ps_black_motivo mot ON(mot.id_black_motivo=black.motivo)
WHERE prod.reference ='" . $reference_prod . "'";

        $this->paginar($sql1, $sq2, 0);

        $this->context->smarty->assign('reference_prod', $reference_prod);

        if ($this->total__rows > 0) {
            $this->output .= $this->displayConfirmation($this->l('Se encontraron (' . $this->total__rows . ') registros con el parámetro de búsqueda (' . $_POST['reference_prod'] . ')'));
            return $this->output . $this->displaySearchBlackList();
        } else {
            $this->output .= $this->adminDisplayWarning($this->l('No se encontraron coincidencias con el parametro de búsqueda (' . $_POST['reference_prod'] . ').'));
            return $this->output . $this->displayForm();
        }
    }

//Conexión DB, listar productos de la lista negra
    function black_list_products() {
        $sql1 = 'SELECT COUNT(1) as total FROM ' . _DB_PREFIX_ . 'product_black_list ';
        $sq2 = "select black.id_prod_black, black.`status`,black.motivo, black.descripcion, black.date,
prod.reference, plang.`name`, prod.id_product, emp.email, emp.firstname, emp.lastname, emp.id_employee, mot.`name` as namem
from ps_product_black_list black INNER JOIN ps_product prod on (black.id_product=prod.id_product)
INNER JOIN ps_employee emp on(black.id_emp=emp.id_employee)
INNER JOIN ps_product_lang plang ON(prod.id_product=plang.id_product)
INNER JOIN ps_black_motivo mot ON(mot.id_black_motivo=black.motivo)
ORDER BY black.date ASC LIMIT $this->offset, $this->select_limit";
        $this->paginar($sql1, $sq2, 0);

        if ($this->total__rows > 0) {
            $this->output .= $this->displayConfirmation($this->l('Se encontraron (' . $this->total__rows . ') elementos en la lista negra'));
            return $this->output . $this->displayBlackList();
        } else {
            $this->output .= $this->displayError($this->l('No se encontraron elementos en la lista negra.'));
            return $this->output . $this->displayBlackList();
        }
    }

    /* genera la numeración y paginación  para construir una tabla html 
     * recive 2 consultas , la primera es la que indica el numero de registros 
     * la segunda consulta debuelve un array de registros para ser mostrados dentro de la la tabla 
     */
    function paginar($query1, $query2) {

        if ($results = Db::getInstance()->ExecuteS($query1))
            foreach ($results as $row) {
                $this->total__rows = $row['total'];
            }

        if ($results = Db::getInstance()->ExecuteS($query2)) {
            $this->context->smarty->assign('productsblock', $results);
        }

        $this->context->smarty->assign('totalrows', $this->total__rows);

        $totalPag = ceil($this->total__rows / $this->select_limit);
        $this->context->smarty->assign('totalpages', $totalPag);

        $links = array();
        for ($i = 1; $i <= $totalPag; $i++) {
            $links[] = $i;
        }

        $this->context->smarty->assign('links', $links);
    }

    // filtrar etuquetas  html 
    function filtro($texto) {
        $html = array("<", ">");
        $filtrado = array("&lt;", "&gt;");
        $final = str_replace($html, $filtrado, $texto);

        return $final;
    }

    // numero de registros por pagína y pagína actual  
    function rows_page() {


        if ((isset($_POST['limit_page']) && $_POST['limit_page']) || (isset($_GET['limit_page']) && $_GET['limit_page'])) {
            if (isset($_POST['limit_page'])) {
                $this->select_limit = $_POST['limit_page'];
            } else {
                $this->select_limit = $_GET['limit_page'];
            }
        } else {
            $this->select_limit = 100;
        }
        $select = '<select name="limit_page" id="limit_page">';
        for ($i = 100; $i <= 500; $i+=100) {
            if ($i == $this->select_limit) {
                $select.='<option value="' . $i . '" selected >' . $i . '</option>';
            } else {
                $select.= '<option value="' . $i . '" >' . $i . '</option>';
            }
        }
        $select.='</select>';

        $this->context->smarty->assign('select_limit', $select);
        $this->context->smarty->assign('select_rows', $this->select_limit);
    }

    // valores iniciales de paginación
    function init_page() {
        if (isset($_GET["pag"])) {
            $this->pag = (int) $_GET["pag"];
        }

        if ($this->pag < 1) {
            $this->pag = 1;
        }
        $this->offset = ($this->pag - 1) * $this->select_limit;

        $this->context->smarty->assign('pageprod', $this->pag);
    }

       
}

?>
