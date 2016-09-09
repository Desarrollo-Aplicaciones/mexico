<?php

class imgUpldr 
{


	# Variables #
	public $_exts = array("image/jpg", "image/jpeg", "image/png", "image/gif"); // Tipos de archivos soportados
	public $_width = 228; // Ancho máximo por defecto
	public $_height = 264; // Alto máximo por defecto
	public $_size = 200000; // Peso máximo. MAX_FILE_SIZE sobrescribe este valor
	public $_name = "imagen"; // Nombre por defecto 	
	public $_dest ="";//C:/wamp/www/test.farmalisto.com.co/modules/cspublicidad/img/";// _PS_ROOT_DIR_.'\modules\modules\cspublicidad\img';
	public $_img;
	public $_ext;
	public $_r = "";
	# Métodos mágicos #


	public function __set($var, $value) {
		$this->$var = $value; 
	}
	public function __get($var) {
		return $this->$var;
	}
	public function conectardb(){ //datos de conexion a la db
    $host="localhost";     $user="root";     $pass="";
    $dbname="test_colombia";

//estableciendo conexion correspondiente
    $this->conexion=mysql_connect($host,$user,$pass);

    mysql_select_db($dbname,$this->conexion)or die("Error en la selección de la base de datos");


$sql= "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."archivopublicidad`(
      `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `tipo` varchar(25) NOT NULL,
  `publica` char(1) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=Aria AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1";

$sql2= "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."codigoPublicidad`(
      `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(4000) NOT NULL,
  `publicadsense` char(1) NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=Aria AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1";

      if(!$result=Db::getInstance()->Execute($sql) && !$result=Db::getInstance()->Execute($sql2))
      

      return false;
  }



	# Métodos propios #
	public function init($img) {
	 $this->_dest = _PS_ROOT_DIR_.'\modules\cspublicidad\img\\';


		$cadenaDestino = str_replace('\\','/',$this->_dest);
		$this->_dest =$cadenaDestino;


// echo '<pre>';
// print_r($img);
// exit();
	
		$this->_img = $img;		


		// Vemos si no pesa más que el máximo definido en $_size
		if ($this->_img['size'] <= $this->_size) {
			// Vemos si hay error
			$error = $this->_img['error'];
			switch($error) {
				case 0:
					// Verificamos que el tipo de archivo sea válido, de ser así, subimos
					if ($this->validaTipo()) {
						// Vemos si el usuario no cambió el nombre por defecto
						// Si $_name == imagen, asignamos el nombre con formato f
						if ($this->_name == "imagen") $this->asignaNombre();
						// Vemos si es mayor al tamaño por defecto
						$tamano = list($ancho_orig, $alto_orig) = getimagesize($this->_img['tmp_name']);
						$origen = $this->_img['tmp_name'];
						// Verificamos que exista el destino, si no, lo creamos
						if ($this->_dest != "" and !is_dir($this->_dest)) {
							mkdir($this->_dest, 0775);
						}
						$destino = $this->_dest.$this->_name;
						$ancho_max = $this->_width;
						$alto_max = $this->_height;

						if ($ancho_orig < $ancho_max or $alto_orig < $alto_max) {
							
							$ratio_orig = $ancho_orig/$alto_orig;
							if ($ancho_max/$alto_max > $ratio_orig) {
							   
							} else {

							}
							// Redimensionar
							$canvas = imagecreatetruecolor($ancho_max, $alto_max);
							switch($this->_img['type']) {
								case "image/jpg":
								case "image/jpeg":
									$image = imagecreatefromjpeg($origen);
									imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
									imagejpeg($canvas, $destino, 100);
								break; 
								case "image/gif":
									$image = imagecreatefromgif($origen);
									imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
									imagegif($canvas, $destino);
								break; 
								case "image/png":
									$image = imagecreatefrompng($origen);
									imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
									imagepng($canvas, $destino, 0);
								break; 
							}
							// echo '<pre>';
							// print_r($this->_name);
							// exit();
							$obj = new imgUpldr;
							$obj -> conectardb();
							$publicaFarmalisto = 1;
							$query = mysql_query("INSERT INTO ps_archivopublicidad (name,tipo,publica)values('".$this ->_name."', '".$this ->_img['type']."','".$publicaFarmalisto."')");

							 $query2 = mysql_query("INSERT INTO ps_codigoPublicidad (codigo,publicadsense)values('0','0')");

							// echo '<pre>';
							// print_r($query);
							// exit();

						} else {
							
							$ratio_orig = $ancho_orig/$alto_orig;
							if ($ancho_max/$alto_max > $ratio_orig) {
							   
							} else {

							}
							// Redimensionar
							$canvas = imagecreatetruecolor($ancho_max, $alto_max);
							switch($this->_img['type']) {
								case "image/jpg":
								case "image/jpeg":
									$image = imagecreatefromjpeg($origen);
									imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
									imagejpeg($canvas, $destino, 100);
								break; 
								case "image/gif":
									$image = imagecreatefromgif($origen);
									imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
									imagegif($canvas, $destino);
								break; 
								case "image/png":
									$image = imagecreatefrompng($origen);
									imagecopyresampled($canvas, $image, 0, 0, 0, 0, $ancho_max, $alto_max, $ancho_orig, $alto_orig);
									imagepng($canvas, $destino, 0);
								break; 
							}
						$obj = new imgUpldr;
							$obj -> conectardb();
							$publicaFarmalisto = 1;							
							$query = mysql_query("INSERT INTO ps_archivopublicidad (name,tipo,publica)values('".$this ->_name."', '".$this ->_img['type']."','".$publicaFarmalisto."')");

							 $query2 = mysql_query("INSERT INTO ps_codigoPublicidad (codigo,publicadsense)values('0','0')");
						}
					} else {
						$this->_r = "Tipo de archivo no válido.";	
					}
				break;
				case 1:
				case 2:
				$this->_r = "[".$error."] La imagen excede el tamaño máximo soportado.";
				break;
				case 3:
				$this->_r = "[".$error."] La imagen no se subió correctamente.";
				break;	
				case 4:
				$this->_r = "[".$error."] Se debe seleccionar un archivo.";
				break;	
			}
		} else {
				$this->_r = "La imagen es muy pesada.";
		}
		return $this->_r;
	}
	public function asignaNombre() { 
		// Asignamos la extensión según el tipo de archivo
		switch($this->_img['type']) {
			case "image/jpg":
			case "image/jpeg":
			$this->_ext = "jpg";
			break; 
			case "image/gif":
			$this->_ext = "gif";
			break; 
			case "image/png":
			$this->_ext = "png";
			break; 
		}
		// Asignamos el nombre a la imagen según la fecha en formato aaaammddhhiiss y la extensión
		$this->_name = date("Ymdhis").".".$this->_ext;


	}
	public function validaTipo() {
		// Verifica que la extensión sea permitida, según el arreglo $_exts
		if (in_array(strtolower($this->_img['type']), $this->_exts))
		 return true;
	}


}
?>