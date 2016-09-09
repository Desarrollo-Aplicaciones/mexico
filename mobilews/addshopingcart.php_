<?php

include(dirname(__FILE__).'/../config/config.inc.php');
include(dirname(__FILE__).'/../init.php');

class Carrito extends FrontController {
    
    
    
public function crearCarrito($array){

      
  if (isset($this->context->cookie->id_cart) && !empty($this->context->cookie->id_cart))
    { 
        $context->cart = new Cart($this->context->cookie->id_cart);
        $this->emptybag();
    }else{
        $context->cart = new Cart(); 
        $this->context->cart->add(); 
    }
        
  $context = Context::getContext();
  // Agrega el carrito a la base de datos
  include(dirname(__FILE__) . '/../classes/Mobile_Detect.php');
  $detect = new Mobile_Detect;
  $context->cart->device = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
     /*
     * Si el arreglo contiene elementos 
     */
    
  if(count($array)>0){
      
    foreach ($array as $key => $value) {
       $context->cart->updateQty($value,$key,0,0,'up',0);
      //$update_quantity = $context->cart->updateQty(3,3485,0,0,'up',0);  
      }
    $context->cart->update(); 
    // actualizar cookie contexto cart
    if ($this->context->cart->id)
         $this->context->cookie->id_cart = (int)$this->context->cart->id;
        //  crear cookie para validacion, aplicacion -> pagina web 
        setcookie("validamobile", "true", time() + 3600, "/");
        Tools::redirect('index.php?controller=order&paso=inicial');
    }
 
  }

// limpiar carrito
  public function emptybag()
  {

    $products = $this->context->cart->getProducts();
    foreach ($products as $product) {
      $this->context->cart->deleteProduct($product["id_product"]);
    }
  }
    
    /*
     * Validar si el token es valido
     */
    
    function validarToken($token)
 { 
        $query="select token, time from ps_token_mobile where token='".$token."';";
        
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

    
}



if(isset($_POST['token_mobile']) and $_POST['token_mobile'] and isset($_POST['ids_producto']) and $_POST['ids_producto']){
    
   $obj=new Carrito();  
    
    if($obj->validarToken(remomeCharSql($_POST['token_mobile']))){    
    
    $array=$_POST['ids_producto'];

$obj->crearCarrito($array);
    
    }   
}
header("Location: ../");


function remomeCharSql($string, $length = NULL){
	$string = trim($string);
        
        $array=array("\"","#","$","%","&","'","(",")","*","+",",","-","/",":",";","<","=",">","?","@","[","]","^","_","`","{","|","}","~");
	$string = utf8_decode($string);
	$string = htmlentities($string, ENT_NOQUOTES);
	$string = str_replace($array, "", $string);        
        $string = ereg_replace( "([ ]+)", " ", $string );
	
	$length = intval($length);
	if ($length > 0){
		$string = substr($string, 0, $length);
	}
	return $string;
}