<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

class SubmitImg{

	public function saveFile ($arrayDoc,$documento)
{
   
$archivo_usuario = str_replace(' ','-',$arrayDoc[$documento]['name']); 

$tipo_archivo = $arrayDoc[$documento]['type']; 
$tamano_archivo = $arrayDoc[$documento]['size'];
$extencion = strrchr($arrayDoc[$documento]['name'],'.');

// Rutina que asegura que no se sobre-escriban documentos
$nuevo_archivo;
$flag= true;
while ($flag)
 {
$nuevo_archivo=$this->randString();
if (!file_exists($this->pathFiles().$nuevo_archivo))
{
$flag= false;
}
 }
//compruebo si las características del archivo son las que deseo 
try {

   if (move_uploaded_file($arrayDoc[$documento]['tmp_name'],$this-> pathFiles().$nuevo_archivo))
   { 
     //return $nuevo_archivo;
	return $vector = array ( $nuevo_archivo, $archivo_usuario );
   }
    else
     { 
     // return 'NO';
	 return $vector = array ( "NO", "NO" );
     } 
}
catch(Exception $e)
{
echo 'Error en la Función saveFile --> lib.php ', $e->getMessage(), "\n";

exit;
}
}


// función que genera una cadena aleatoria
	public function randString ($length = 32){  
		$string = "";
		$possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
		$i = 0;
		while ($i < $length){    
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$string .= $char;    
			$i++;  
		}  
		return $string;
	}




	public function exist_file($name_file){
		if (file_exists(pathFiles().$name_file)){
			return true; 
		}
		else {
			return false; 
		}
	}

	// Retorna la ruta donde se encuentran los archivos de los usuarios
	public function pathFiles(){
		// Definir directorio donde almacenar los archivos, debe terminar en "/" 
		$directorio="KWE54O31MDORBOJRFRPLMM8C7H24LQQR/";
		try { 
			$path=_PS_ROOT_DIR_.'/'.$directorio;	
			if (!file_exists($path)) {
				mkdir($path, 0755);
			}
			$this->writeHtaccess($path);
			return $path;
  		} 

		catch (Exception $e) {
	 		echo $e;
  			return false;
 		}
}

	function writeHtaccess($path){
	// htaccess documentos
	if(!file_exists($path.'.htaccess'))	{
		$htaccess_content="Order allow,deny
							Deny from all";
		$file = fopen($path.'.htaccess' , "w+");
		fwrite($file, $htaccess_content);
	}
	// htaccess Raiz
	if(!file_exists('./.htaccess')){
		$htaccess_content="Options -Indexes
							Options +FollowSymlinks
							RewriteEngine on
							#RewriteBase /SefeDocuments/
							RewriteRule ^([a-zA-Z]+).html$ index.php?req=$1";
		$file = fopen('./.htaccess' , "w+");
		fwrite($file, $htaccess_content);
	}

}

}


if(isset($_FILES) && !empty($_FILES) && isset($_GET) && !empty($_GET['id_order']))
{	
	$obj =	new SubmitImg(); 
	$order = new Order((int) $_GET['id_order']);
	$archivo_formula = $obj->saveFile($_FILES, 'file');
	if(isset($order) && !empty($order) && $archivo_formula[1] && $archivo_formula[1] != 'NO' && $archivo_formula[0] != 'NO'){
  		if(Db::getInstance()->autoExecute('ps_formula_medica', array(
    		'medio_formula' =>    (int)4,
    		'nombre_archivo_original' =>    pSQL($archivo_formula[1]),   
    		'nombre_archivo' =>    pSQL($archivo_formula[0]), 
     		'fecha' =>    pSQL(date("Y-m-d")),
    		'id_cart_fk' =>    (int)$order->id_cart,
     		'id_cunstomer_fk' =>    (int) $order->id_customer,
     		'id_orders_fk' => (int) $order->id,
		 ), 'INSERT'))
exit(); 
 }
exit(json_encode('error'));
}




?>