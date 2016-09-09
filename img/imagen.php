<?php

$ts = gmdate("D, d M Y H:i:s") . " GMT";
header("Expires: $ts");
header("Last-Modified: $ts");
header("Pragma: no-cache");
header("Cache-Control: no-cache, must-revalidate");

require(dirname(__FILE__).'/../config/config.inc.php');
// Capturamos el nombre de la imagen
$image = $_GET['imagen'];
// un try catch en caso de que la imagen no exista podamos controlar la excepción
	try {
		// Definimos la ruta donde se almacenan las imágenes y añadimos el nombre imagen que nos interesa 
		$directorio = Configuration::get('PATH_UP_LOAD');

		$ruta=$directorio."cspublicidadfl/uploads/".$image;
		//le informamos al navegador el tipo de documento
		$ext_file = explode('.', strtolower($image));

		//print_r($ext_file);
		switch ($ext_file[1]) {
			case 'jpg':
					$headerimg = 'image/jpg';
				break;
			case 'jpeg':
					$headerimg = 'image/jpeg';
				break;
			case 'png':
					$headerimg = 'image/png';
				break;
			case 'gif':
					$headerimg = 'image/gif';
				break;
			
			default:
					$headerimg = 'image/jpg';
				break;
		}
		
		header("Content-Type:".$headerimg); 
		// leemos el archivo
		readfile($ruta);
	} catch(Exception $e) {
	    // En caso de que la imagen no exista o se presente algún tipo de Excepción, mostramos una imagen por defecto.
		//header("Content-Type: image/jpeg"); 
		//readfile("nodisponible.jpg");   
	}
?>
