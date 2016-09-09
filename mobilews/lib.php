<?php
require_once(dirname(__FILE__).'/../config/config.inc.php');
require_once(dirname(__FILE__).'/../init.php');
require_once(dirname(__FILE__).'/CartMobile.php');

class Lib extends FrontController {

  private $base_url;

  function __construct() {
    $this->base_url = _PS_BASE_URL_.__PS_BASE_URI__;
    if (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')){
      $this->base_url = _PS_BASE_URL_SSL_.__PS_BASE_URI__;
    }
  }

  private function imgs_product($id_product=null)
  {



    if($id_product!=null)
      {   $array_img=null;
        $query="SELECT i.id_image
        FROM "._DB_PREFIX_."image i
        LEFT JOIN "._DB_PREFIX_."image_lang il ON (i.id_image = il.id_image)
        WHERE i.id_product = ".$id_product." AND il.id_lang = 1
        ORDER BY i.position  ASC";

        if ($results = Db::getInstance()->ExecuteS($query)){
          foreach ($results as $value) {

            $array_img[] = $this->base_url.$value['id_image']."-thickbox_default/img.jpg";
          }
          if($array_img!=NULL)

           return $array_img;
       }     

     }

     $array_img[]=  $this->base_url.'img/p/es-default-thickbox_default.jpg';
     return $array_img;
   }


   function buscarProductos($buscar, $pagina = 1, $filas = 20, $ordenar = null, $campo = null) {
    $array_productos = array();


    $buscar = utf8_encode($this->remomeCharSql($this->sanear_string($buscar)));


    if ($buscar != NULL && strlen(trim($buscar)) >= 4) {
      if ($filas > 300) {
        $filas = 300;
      }

            /*
             * Validacioner ordenar
             */
            if ($ordenar != NULL && $campo != NULL) {

              if ($ordenar == 'A' OR $ordenar == "ASC") {
                $ordenar = "ASC";
              }
              if ($ordenar == 'Z' OR $ordenar == 'DESC') {
                $ordenar = "DESC";
              }
            }
            if ($ordenar==NULL) {
             $ordenar = "DESC";  
             $campo="position"; 
           }

            // cambiar orden de campo id_product a position
           if(isset($campo) && strtolower($campo) == 'id_product'){
             $campo="position"; 
             $ordenar = "DESC";  
           }
 // return 'campo -> '.$campo.' ordener -> '.$ordenar;
           $search = new Search();

           $results = $search->findWsMobile(1, $buscar, $pagina, $filas, $campo, $ordenar, FALSE, FALSE);

           if ((int) $results['total'] > 0) {

            $total_filas = (int) $results['total'];
            $total_paginas = ceil($total_filas / $filas);

            $inicio = 0;

            if ($pagina > $total_paginas | $pagina == 1) {
              $pagina = 1;
              $inicio = 0;
            } else {
              $inicio = ($pagina - 1) * $filas;
            }

            $array_productos['total_paginas'] = $total_paginas;
            $array_productos['total_filas'] = $total_filas;
            $array_productos['pagina'] = $pagina;
            $array_productos['filas'] = $filas;

            $array_prod = NULL;
            if ($results['result'] != NULL) {
              foreach ($results['result'] as $value) {

                $value['description'] = strip_tags($value['description']);
                $value['description_short'] = strip_tags($value['description_short']);
                $value['price'] = Tools::ps_round($value['price'], 2);
                $value['price'] = number_format( $value['price'] ,2, '.', '');

                $value['position'] = 1;

                $array_prod[] = array($value, 'imgs' => $this->imgs_product($value['id_product']), 'img_laboratorio' => $this->base_url.'img/tmp/manufacturer_mini_74_1.jpg');
              }
              if ($array_prod != NULL) {
                $array_productos['productos'] = $array_prod;
                return $array_productos;
              }
            }
          }
        }
        return false;
      }

      public function buscarProductosCategoria($ids_categoria, $pagina=1,$filas=10, $ordenar=null,$campo=null)
      {  
       $array_productos= array();  

       $buscar = $ids_categoria;

       $busqueda = NULL;
       if ($filas > 300) {
        $filas = 300;
      }

      foreach ($buscar as $value) {
       $var=str_replace(" ", "", $this->remomeCharSql($value)); 
       if(is_numeric($var))
         $busqueda[]=$var;        
     }
     if($busqueda!=NULL)       
     {  

      $total_filas = 0;
      $query = "SELECT COUNT(prod.id_product) total
      FROM "._DB_PREFIX_."product prod 
      INNER JOIN "._DB_PREFIX_."product_lang prodl on(prod.id_product=prodl.id_product) 
      INNER JOIN "._DB_PREFIX_."category_product cat_prod ON (cat_prod.id_product=prod.id_product) 
      INNER JOIN "._DB_PREFIX_."product_shop prods ON (prod.id_product=prods.id_product AND prod.active = prods.active) 
      INNER JOIN "._DB_PREFIX_."category_lang cat_prodl ON (cat_prod.id_category=cat_prodl.id_category) 
      LEFT JOIN "._DB_PREFIX_."tax_rule taxr ON(prod.id_tax_rules_group = taxr.id_tax_rules_group) 
      LEFT JOIN "._DB_PREFIX_."tax tax ON(taxr.id_tax = tax.id_tax) 
      WHERE cat_prodl.id_category in ('".implode("','",$busqueda)."') AND prod.active=1 AND prods.active=1 AND prod.active=1 AND prod.is_virtual=0 AND prods.visibility='both' AND (taxr.id_tax != 0 OR ISNULL(taxr.id_tax)); ";


      if ($results = Db::getInstance()->ExecuteS($query)) {
        foreach ($results as $value) {
          $total_filas = (int) $value['total'];
        }
      }

      $total_paginas = ceil($total_filas / $filas);

      $inicio = 0;

      if ( $pagina > $total_paginas | $pagina == 1) {
        $pagina = 1;
        $inicio = 0;
      } else {
        $inicio = ($pagina - 1) * $filas;
      }

      $array_productos['total_paginas']=$total_paginas;
      $array_productos['total_filas']=$total_filas;
      $array_productos['pagina']=$pagina;
      $array_productos['filas']=$filas;


      $query = " SELECT prod.id_product , prod.reference, prodl.`name`,prodl.description, prodl.description_short , cat_prod.id_category, 
      CASE prod.id_tax_rules_group
      WHEN  0 THEN prod.price
      ELSE IF(taxr.id_tax != 0,prod.price + (prod.price * tax.rate/100),prod.price)
      END
      AS `price`, prod.price as precio
      FROM "._DB_PREFIX_."product prod
      INNER JOIN "._DB_PREFIX_."product_lang prodl on(prod.id_product=prodl.id_product)
      INNER JOIN "._DB_PREFIX_."category_product cat_prod ON (cat_prod.id_product=prod.id_product)
      INNER JOIN "._DB_PREFIX_."product_shop prods ON (prod.id_product=prods.id_product AND prod.active = prods.active)
      INNER JOIN "._DB_PREFIX_."category_lang cat_prodl ON (cat_prod.id_category=cat_prodl.id_category)
      LEFT JOIN "._DB_PREFIX_."tax_rule taxr ON(prod.id_tax_rules_group = taxr.id_tax_rules_group)
      LEFT JOIN "._DB_PREFIX_."tax tax ON(taxr.id_tax = tax.id_tax) 
      WHERE cat_prodl.id_category in ('".implode("','",$busqueda)."') "
      . " AND prod.active=1 AND prods.active=1 AND prod.active=1 
      AND prod.is_virtual=0 AND prods.visibility='both' AND (taxr.id_tax != 0 OR ISNULL(taxr.id_tax)) ";

   /*
 * Validacioner ordenar
 */
   if($ordenar!=NULL && $campo!=NULL)
   {

     if($ordenar==='A')
     {
      $ordenar="ASC";
    }
    if($ordenar==='Z'){
      $ordenar="DESC";
    }
    
    $query.=" ORDER BY `".$campo."` ".$ordenar;

  }else{   

    $query.=" ORDER BY prodl.id_product ASC";
  }

  $query.=" LIMIT ".$inicio.", ".$filas.";";

  $array_prod = array();
  
  if ($results = Db::getInstance()->ExecuteS($query)) {
    foreach ($results as $value) {

      $value['description']=strip_tags($value['description']);
      $value['description_short']=strip_tags($value['description_short']);
      $value['price'] = Tools::ps_round($value['price'], 2);
      $value['price'] = number_format( $value['price'] ,2, '.', '');
      $array_prod[] = array($value,'imgs'=>$this->imgs_product($value['id_product']),'img_laboratorio'=>$this->base_url.'img/tmp/manufacturer_mini_74_1.jpg');

    }
    if($array_prod!=NULL)
    {
     $array_productos['productos'] = $array_prod;  
     return $array_productos;
   }
 }
 
}


return false;
}

function remomeCharSql($string, $length = NULL){
	$string = trim($string);

  $array=array("\"","#","$","%","&","'","(",")","*","+",",","-","/",":",";","<","=",">","?","@","[","]","^","`","{","|","}","~");
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

function getToken($user)
{
 $salt = $this->randString();
   // genera token de forma aleatoria
 $token = md5(sha1($salt).md5(uniqid(microtime(), true)));
   // genera fecha de generación del token
   //$tokenTime = time();
   // escribe la información del token en sesión para poder
   //$_SESSION['AntiCsrf']['webservicemobile_token'] = array('token'=>$token, 'time'=>$tokenTime); 
 $time_token= $this->sumarMinutos(time(),15);

 $query = "insert into "._DB_PREFIX_."token_mobile (token, time";
                                                    if(isset($user) && !empty($user))
                                                      $query.=',user_hash';
                                                    $query.=") VALUES('".$token."','".date('Y-m-d H:i:s',$time_token)."'";
                                                    if(isset($user) && !empty($user))
                                                      $query.= ",'".$user."'";
                                                    $query .=" ); ";
if ($results = Db::getInstance()->ExecuteS($query)) {

 return $token;
}
return false;
}

/**
 * Retorna las categor�as permitidas al usuario relacionado con el token actual
 */
function getAllowcategories($token){

  $sql="SELECT cat.id_category
  FROM "._DB_PREFIX_."system_clients_categories scc 
  INNER JOIN "._DB_PREFIX_."employee emp ON (scc.id_employee = emp.id_employee)
  INNER JOIN "._DB_PREFIX_."category cat ON (scc.id_category = cat.id_category)
  INNER JOIN "._DB_PREFIX_."token_mobile tom ON (emp.user_hash = tom.user_hash)
  WHERE tom.token = '".$this->remomeCharSql($token)."'
  GROUP BY cat.id_category";
  $results = Db::getInstance()->ExecuteS($sql);    
  if (!empty($results) && is_array($results)) {
    $resultado = array();
    foreach ($results as $key => $value) {
      $resultado[] = $value['id_category'];
    }
    return $resultado;
  }
  return NULL;      
}

function randString ($length = 256)
{  
  $string = "";
  $possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
  $i = 0;
  while ($i < $length)
  {    
    $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
    $string .= $char;    
    $i++;  
  }  
  return $string;
}

private function sumarMinutos($segundos,$minutos)
{
  $retorno = NULL;

  $mes    = date("m",$segundos);
  $dia    = date("d",$segundos);
  $anyo   = date("Y",$segundos);
  $hora   = date("H",$segundos);
  $minuto = date("i",$segundos)+ $minutos;

  $sumadeMeses = mktime($hora,$minuto,0,$mes,$dia,$anyo);

  $retorno = $sumadeMeses;

  return $retorno;
}



//public function logtxt ($text="") { 
// $fp=fopen("C:/wamp/www/archivo.txt","a+"); fwrite($fp,$text."\r\n"); fclose($fp);
//
//}


function sanear_string($string)
{

  $string= utf8_encode($string);

  $a = array('À', '�?', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', '�?', 'Î', '�?', '�?', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', '�?', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', '�?', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', '�?', 'Ď', '�?', '�?', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', '�?', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', '�?', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', '�?', 'Ŏ', '�?', '�?', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', '�?', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', '�?', 'ǎ', '�?', '�?', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ', 'Ά', 'ά', 'Έ', 'έ', 'Ό', 'ό', '�?', 'ώ', 'Ί', 'ί', 'ϊ', '�?', 'Ύ', '�?', 'ϋ', 'ΰ', 'Ή', 'ή');
  $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Α', 'α', 'Ε', 'ε', 'Ο', 'ο', 'Ω', 'ω', 'Ι', 'ι', 'ι', 'ι', 'Υ', 'υ', 'υ', 'υ', 'Η', 'η');

  return str_replace($a, $b, $string);

}


/**
 * Retorna el id del cliente Externo
 */
function get_external_client($user = NULL, $profile = 'systems_clients'){
  $sql ="SELECT emp.id_employee,emp.user_hash,emp.email  
  FROM
  "._DB_PREFIX_ ."employee emp INNER JOIN "._DB_PREFIX_ ."profile_lang prof ON(emp.id_profile = prof.id_profile)
  WHERE emp.user_hash = '".$this->remomeCharSql($user)."' AND prof.`name` = '".$this->remomeCharSql($profile)."' 
  AND emp.active = 1 ";
          //trigger_error(' ||'.$sql.'|| ', E_USER_NOTICE);  
  $result = Db::getInstance()->ExecuteS($sql);
  if($result && !empty($result[0]['id_employee']) AND !empty($result[0]['user_hash'])){
    return $result[0];
  }else{
    return FALSE;
  }          
}


/**
 * Crear Customer cliente Externo
 */

function add_customer($args){

 if(empty($this->getCustomersByEmail($email))){
  $customer = new Customer();
  foreach ($args as $key => $value) {
    $customer->{$key} = $value;  
  } 
  $customer->add(); 
}


  /*$customer->firstname = 'name';
  $customer->lastname = 'lastname';
  $customer->email = 'mail@mail.com';
  $customer->passwd = md5(time());
  $customer->is_guest = 0;*/

  // $validateFiles = $address->validateFieldsRequiredDatabase();

}


/**
 * Crear Address cliente Externo
 */

function add_address(){

  $address = new Address();
  $this->errors = $address->validateController();
  $address->id_customer = (int)$this->context->customer->id;
  $address->id_country;
  $address->id_state = 0;
  $normalize = new AddressStandardizationSolution;
  $address->address1 = $normalize->AddressLineStandardization($address->address1);
  $address->address2 = $normalize->AddressLineStandardization($address->address2);
  $address->dni = null;
  $address->id_colonia;
  $address->colonia_name;
  $address->city_name;
  $address->is_rfc;
  $address->alias;
  $address->postcode;
  $address->phone;
  $address->phone_mobile;
  $result = $address->save();

  Db::getInstance()->insert('address_city', array(
                            'id_address'=>(int)$Id_address,
                            'id_city'=>(int)$city_id
                            ));

}

  /**
   * Retrieve customers by email address
   *
   * @static
   * @param $email
   * @return array
   */
  public function getCustomersByEmail($email)
  {
    $sql = 'SELECT *
    FROM `'._DB_PREFIX_.'customer`
    WHERE `email` = \''.pSQL($email).'\'
    '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

    return Db::getInstance()->ExecuteS($sql);
  }


/**
 * Crear Order cliente Externo
 */

function add_order(){
  $cart_ws_json = new CartWSJSON();
}

function getEmployeeByToken($token){
  $sql="SELECT emp.id_employee, emp.email, emp.firstname, emp.lastname, emp.user_hash
  FROM "._DB_PREFIX_."employee emp INNER JOIN "._DB_PREFIX_."token_mobile tkm ON(emp.user_hash = tkm.user_hash)
  WHERE tkm.token = '".$this->remomeCharSql($token)."'
  GROUP BY emp.id_employee";
  return Db::getInstance()->getRow($sql);
}


} 
