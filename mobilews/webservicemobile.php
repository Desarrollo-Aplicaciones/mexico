<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');
require_once 'lib.php';

class JSON_WebService {
    private $methods, $args, $strcall, $data_array;
    public function __construct($rawData) {
        
        $this->data_array= json_decode($rawData,TRUE);
        $this->strcall = $this->data_array['method']; //str_replace($_SERVER["SCRIPT_NAME"]."/", "", $_SERVER["REQUEST_URI"]);
        $this->args =  json_encode( $this->data_array['args']);

        $this->methods = array();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods', 'PUT, GET, POST, DELETE, OPTIONS');
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json; charset=utf-8');
        
        
    }
    
    public function Register($name) {
        $this->methods[$name] = true;
    }
    
    public function Remove($name) {
        $this->methods[$name] = false;
    }
    
    private function call($name, $args) {
      
     
        if ($this->methods[$name] == true) {
            $result = call_user_func($name, $args);
           //$result = call_user_func_array($name, $args);
            return json_encode($result);
        } else {
           header("HTTP/1.0 403 Not Found ");
       }
   }
   
   function start() {
    try{
        if(!function_exists($this->strcall))
            throw new Exception("Function '".$this->strcall."' does not exist.");
        if (!$this->methods[$this->strcall])
            throw new Exception("Access denied for function '".$this->strcall."'.");
        
        header("HTTP/1.0 200 OK");
        print $this->call($this->strcall, json_decode($this->args,true));
    }
    catch(Exception $e){
        header("HTTP/1.0 500 Internal server error");
        print json_encode(
                          array(
                                "message" => $e->getMessage(),
                                "code" => $e->getCode(),
                                "file" => $e->getFile(),
                                "line" => $e->getLine(),
                                "stackTrace" => $e->getTrace(),
                                "status" => array("message" => "Internal server error", "code" => "500")
                                )
                          );
    }
}


}




//Obtiene el contenido de la solicitud POST
$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : ''; 

//Instancia de la clase JSON_WebService
$server = new JSON_WebService($HTTP_RAW_POST_DATA);


/*
 * Registar metodos del web service
 *
 * Métodos para clientes externos (comercios asociados)
 */
// retorna un token, requiere los parámetros usuario y contraseña
$server->register("getToken"); 
//Retorna los productos requiere validación de token
$server->register("getProducts");
// Retorna el costo de envió del pedido, requiere el código postal, el token y los id's de los productos y cantidades
$server->register("getShippingCost"); 
// retorna los productos de las categorías que se pasen como parámetros, requiere token 
$server->register("getProdCategories");
// Registra un comprador, el comprador debe ser asociado al cliente externo
$server->register("setCustomer");
// Registra una dirección
$server->register("setAddress");
// Retorna un comprador, si el comprador esta asociado al cliente externo que lo solicita 
$server->register("getCustomer");
// retorna la direcciones asociadas al un comprador, si el comprador esta asociado al cliente externo que lo solicita  
$server->register("getAddresses");
//Registra una orden en el sistema 
$server->register("setOrder");
// Retorna las ordenes de un cliente 
$server->register("getOrderses");

/**
 * Métodos APP Kubo
 */
// Retorna token para aplicación movil kubo
$server->register("getTokenStr");
//Retorna los productos
$server->register("getProductos");
//Retorna los productos de una categorías
$server->register("getCategoria");
//Retorna los categorías
$server->register("getCategorias");
//Retorna los categorías
$server->register("getBanners");
//Inicializa el servicio
$server->start();




/*
 * Define los metodos del servicio web
 */
function getServerTime($format){ 

    return date($format);
}
/**
 * Definición de compradores 
 */
function setCustomer(){
    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');  

}
/**
 * Consulta de comprador
 */
function getCustomer(){
    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');  
}
/**
 * Definición de direcciones 
 */
function setAddress($args){
    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');    
}
/**
 * Consulta de direcciones 
 */
function getAddresses($args){
    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');  
}
/**
 * Definición de orden
 */
function setOrder(){
    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');  
}
/**
 * Consulta de ordenes
 */
function getOrders(){
    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');  
}
/**
 * Retorna token para APP
 */
function getTokenStr($args)
{
   $obj = new Lib();
   return $obj->getToken();
   
}

// retorna productos
function getProductos($args)
{ 
    if(count($args)==5 || count($args)==3)
    {  
        
        $obj = new Lib();
        
        
        
        $args['Buscar'] = utf8_decode($args['Buscar']);

        if (is_numeric($obj->remomeCharSql($args['Pagina']))) {
            $args['Pagina'] = $obj->remomeCharSql($args['Pagina']);
        } else {
            $args['Pagina'] = 1;
        }
        if (is_numeric($obj->remomeCharSql($args['Filas']))) {
            $args['Filas'] = $obj->remomeCharSql($args['Filas']);
        } else {
            $args['Filas'] = 10;
        }
        
        if(isset($args['Ordenar'])) {     
            $args['Ordenar']=$obj->remomeCharSql($args['Ordenar']);
            $args['Campo']=$obj->remomeCharSql($args['Campo']);
        }
        
        if ($args['Buscar'] != NULL && $args['Buscar'] != "" ) {
            if(isset($args['Ordenar'])&&($args['Ordenar']==='A'||$args['Ordenar']==='Z')&& is_string($args['Campo'])){
                
                return $obj->buscarProductos($args['Buscar'], $args['Pagina'], $args['Filas'],$args['Ordenar'],$args['Campo']);      
            }
            
            return $obj->buscarProductos($args['Buscar'], $args['Pagina'], $args['Filas']);  
        }
    }  
    return false;   
    
}

/**
 * Aplicaciones externas
 */

function getShippingCost($args){
  
    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');
    $cart = new Cart();
    $obj = new Lib();
    return $cart->getTotalShippingCostWS($obj->remomeCharSql($args['cod_postal']) , $args['products']);
}
/**
 * 
 */
 // aplicación externa
function getProducts($args){

    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');

    if(count($args)==5 || count($args)==4)
    {  
        
        $obj = new Lib();
        
        
        
        $args['Buscar'] = utf8_decode($args['Buscar']);

        if (is_numeric($obj->remomeCharSql($args['Pagina']))) {
            $args['Pagina'] = $obj->remomeCharSql($args['Pagina']);
        } else {
            $args['Pagina'] = 1;
        }
        if (is_numeric($obj->remomeCharSql($args['Filas']))) {
            $args['Filas'] = $obj->remomeCharSql($args['Filas']);
        } else {
            $args['Filas'] = 20;
        }
        
        if(isset($args['Ordenar'])) {     
            $args['Ordenar']=$obj->remomeCharSql($args['Ordenar']);
            $args['Campo']=$obj->remomeCharSql($args['Campo']);
        }
        
        if ($args['Buscar'] != NULL && $args['Buscar'] != "" ) {
            if(isset($args['Ordenar']) && ($args['Ordenar']=='A'||$args['Ordenar']=='Z') && ($args['Ordenar']=='ASC'||$args['Ordenar']=='DESC') && is_string($args['Campo'])){
                
                return $obj->buscarProductos($args['Buscar'], $args['Pagina'], $args['Filas'],$args['Ordenar'],$args['Campo']);      
            }
            
            return $obj->buscarProductos($args['Buscar'], $args['Pagina'], $args['Filas']);  
        }
    }  
    return false;

}

/**
 * Aplicaciones externas
 */

function getProdCategories($args)
{

    if(empty($args['token']) || !validarToken($args['token']))
        return  array('STATUS'=>'ERROR', 'Message' => 'El Token caduco o es invalido.');
    
    if(count($args)==6 || count($args)==4)
    { 
       
        $obj = new Lib();

        if (is_numeric($obj->remomeCharSql($args['Pagina']))) {
            $args['Pagina'] = $obj->remomeCharSql($args['Pagina']);
        } else {
            $args['Pagina'] = 1;
        }
        if (is_numeric($obj->remomeCharSql($args['Filas']))) {
            $args['Filas'] = $obj->remomeCharSql($args['Filas']);
        } else {
            $args['Filas'] = 20;
        }
        if ($args['categorias'] != NULL && $args['categorias'] != "" && !empty($args['categorias']) ) {

            $allow_categories = $obj->getAllowcategories($args['token']);  
            $ids_categoria = explode(",", $args['categorias']);
            if(!is_array($ids_categoria))
                $ids_categoria[] = array((int)$ids_categoria);


            $result = array_intersect($allow_categories, $ids_categoria); 
            if(isset($args['Ordenar']) && ($args['Ordenar']==='A'||$args['Ordenar']==='Z') && ($args['Ordenar']==='ASC'||$args['Ordenar']==='DESC') && is_string($args['Campo'])){
                return $obj->buscarProductosCategoria($result, $args['Pagina'], $args['Filas'],$args['Ordenar'],$args['Campo']);      
            }
            return $obj->buscarProductosCategoria($result, $args['Pagina'], $args['Filas']);
        }  
        
    }
    return false;
}

    /*
     * Validar si el token es valido
     */
    
    function validarToken($token)
    {  
        $obj = new Lib();
        $query="select token, time from ps_token_mobile where token='".$obj->remomeCharSql($token)."';";
        
        if ($results = Db::getInstance()->ExecuteS($query)) {
            if($results[0]['token']===$token )
            { 
                $time=time();

                if( $time <= strtotime($results[0]['time'] ))
                {  
                  return true;  
              }
              
          }
          
      }
      return false;   
  }
/**
 * 
 */

function getToken($args){
    $errors =  array();

    if(isset($args) && is_array($args) && count($args) == 2){
        $obj = new Lib();
        if(crypt('PX4HQ9K6SH', $args['passhash']) == $args['passhash'] &&  $obj->get_external_client($args['user'])['user_hash'] == $args['user']) {
            $obj = new Lib();
            return $obj->getToken($args['user']);
        }else{
            $respose =  array('STATUS'=>'ERROR', 'Message' => 'La contraseña o el usuario no son validos.');
        }
    }else{
        $respose = array('STATUS'=> 'ERROR', 'Message' => 'El tipo de parámetros o el numero de parámetros no es correcto.');
    }
    return $respose;
}


function getCategoria($args)
{

    
    if(count($args)==5 || count($args)==3)
    { 
       
        $obj = new Lib();

        if (is_numeric($obj->remomeCharSql($args['Pagina']))) {
            $args['Pagina'] = $obj->remomeCharSql($args['Pagina']);
        } else {
            $args['Pagina'] = 1;
        }
        if (is_numeric($obj->remomeCharSql($args['Filas']))) {
            $args['Filas'] = $obj->remomeCharSql($args['Filas']);
        } else {
            $args['Filas'] = 10;
        }
        
        if ($args['categoria'] != NULL && $args['categoria'] != "" ) {
            
         $categorias=array(
                           'sin_formula_medica'=>array(587),
                           'con_formula_medica'=>array(477),
                           'cuidado_personal'=>array(766),
                           'bellza'=>array(434,433),
                           'sexualidad'=>array(636,640),
                           'mama_y_babe'=>array(555));
         
         
         
         foreach ($categorias as $key => $value) {
           
            if($key==$args['categoria']){
                
        // if(($args['Ordenar']==='A'||$args['Ordenar']==='Z')&& is_string($args['Campo'])){
             if(isset($args['Ordenar'])&&($args['Ordenar']==='A'||$args['Ordenar']==='Z')&& is_string($args['Campo'])){
               
                return $obj->buscarProductosCategoria($value, $args['Pagina'], $args['Filas'],$args['Ordenar'],$args['Campo']);      
            }
            
            return $obj->buscarProductosCategoria($value, $args['Pagina'], $args['Filas']);
        }
        
    }
}  

}
return false;
}



function getCategorias($args)
{

    if ($args == 'farmalisto_colombia') {
        $categorias[] = array('color' => '#FF0000',
                              'nombre' => '-Sin Fórmula Médica-',
                              'url_img' => 'https://www.farmalisto.com.mx/10611-home_default/img.jpg',
                              'value' => 'sin_formula_medica',
                              'ids_categorias'=>'587',
                              'orden'=>'1');

        $categorias[] = array('color' => '#00FF80',
                              'nombre' => '- Con Fórmula Médica -',
                              'url_img' => 'https://www.farmalisto.com.mx/10611-home_default/img.jpg',
                              'value' => 'con_formula_medica',
                              'ids_categorias'=>'477',
                              'orden'=>'2');

        $categorias[] = array('color' => '#FF0000',
                              'nombre' => '- Cuidado Personal -',
                              'url_img' => 'https://www.farmalisto.com.mx/img/demos/demo1.jpg',
                              'value' => 'cuidado_personal',
                              'ids_categorias'=>'766',
                              'orden'=>'3');

        $categorias[] = array('color' => '#FF0000',
                              'nombre' => '- Belleza -',
                              'url_img' => 'https://www.farmalisto.com.mx/img/demos/demo1.jpg',
                              'value' => 'bellza',
                              'ids_categorias'=>'434,433',
                              'orden'=>'4');

        $categorias[] = array('color' => '#FF0000',
                              'nombre' => '- Sexualidad -',
                              'url_img' => 'https://www.farmalisto.com.mx/img/demos/demo1.jpg',
                              'value' => 'sexualidad',
                              'ids_categorias'=>'636,640',
                              'orden'=>'5');

        $categorias[] = array('color' => '#FF0000',
                              'nombre' => '- Mamá y bebé -',
                              'url_img' => 'https://www.farmalisto.com.mx/img/demos/demo1.jpg',
                              'value' => 'mama_y_babe',
                              'ids_categorias'=>'555',
                              'orden'=>'6');

        return $categorias;
    }
    return false;
}

function getBanners($args)
{
   
 if ($args == 'banners_colombia') {
     
     
   $banners[]=array('titulo'=>'Farmalisto México',
                    'url_img'=>'https://www.farmalisto.com.mx/img/demos/Banner1.jpg',
                    'descripcion'=>'Farmalisto Esencial Diabetes Farmalisto Esencial Hipertensión Farmalisto Esencial Depresión Farmalisto Esencial Epilepsia Farmalisto Esencial Salud Sexual',
                    'enlace'=>'https://www.farmalisto.com.mx/');
   
   
   $banners[]=array('titulo'=>'Servicio al cliente - Contáctenos',
                    'url_img'=>'https://www.farmalisto.com.mx/img/demos/Banner2.jpg',
                    'descripcion'=>'Servicio al cliente - Contáctenos',
                    'enlace'=>'https://www.farmalisto.com.mx/contactenos');
   
   $banners[]=array('titulo'=>'Receta Medica',
                    'url_img'=>'https://www.farmalisto.com.mx/img/demos/Banner3.jpg',
                    'descripcion'=>'Receta Medica',
                    'enlace'=>'http://info.farmalisto.com/farmalisto-esencial-mx/?utm_source=barramegamenuhorizontal_mx&utm_medium=categoriafarmalistoesencial_m');
   
   
   $banners[]=array('titulo'=>'Entregas y pedidos domicilios',
                    'url_img'=>'https://www.farmalisto.com.mx/img/demos/Banner4.jpg',
                    'descripcion'=>'Entregas y pedidos domicilios',
                    'enlace'=>'https://www.farmalisto.com.mx/content/1-entregas-y-pedidos-domicilios');

   
   return $banners;
   
}

}




