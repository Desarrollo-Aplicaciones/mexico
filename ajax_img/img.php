<?php
require_once(dirname(__FILE__) . '/../config/config.inc.php');
require_once(dirname(__FILE__) . '/../config/defines.inc.php');
require_once(dirname(__FILE__) . '/../init.php');

if(isset($_GET['image']) && !empty($_GET['image'])){
	$array_img = explode('.', trim($_GET['image']));
	$id_formula_medica = (int) $array_img[1];
	$extensions = array(
                      "gif" => IMAGETYPE_GIF,
                      "jpg" => IMAGETYPE_JPEG,
                      "png" => IMAGETYPE_PNG,
                      "swf" => IMAGETYPE_SWF,
                      "psd" => IMAGETYPE_PSD,
                      "bmp" => IMAGETYPE_BMP,
                      "tiff" => IMAGETYPE_TIFF_II,
                      "tiff" => IMAGETYPE_TIFF_MM,
                      "jpc" => IMAGETYPE_JPC,
                      "jp2" => IMAGETYPE_JP2,
                      "jpx" => IMAGETYPE_JPX,
                      "jb2" => IMAGETYPE_JB2,
                      "swc" =>  IMAGETYPE_SWC,
                      "iff" =>  IMAGETYPE_IFF,
                      "wbmp" => IMAGETYPE_WBMP,
                      "xbm" => IMAGETYPE_XBM,
                      "ico" => IMAGETYPE_ICO
                      );
  $ruta_base = _PS_ROOT_DIR_.'/KWE54O31MDORBOJRFRPLMM8C7H24LQQR/';

  $sql = "SELECT formula.nombre_archivo_original as imagen,formula.nombre_archivo as fuente
  FROM "._DB_PREFIX_."formula_medica formula 
  WHERE formula.id_formula_medica = ".(int) $id_formula_medica .";";

  if($array_img[0] == 'customer_photo'){

   $sql = "SELECT img_profile AS fuente 
   FROM
   "._DB_PREFIX_."customer 
   WHERE id_customer = ".(int) $id_formula_medica .";";
   $ruta_base = _PS_ROOT_DIR_.'/img/customers/profile/';

 }


 $full_name = '';
 $ruta_img = '';

 if($row = Db::getInstance()->getRow($sql)){
  if(isset($row['fuente']) && !empty($row['fuente'])){
    $ruta_img = $row['fuente'];
    if(!file_exists($ruta_base.$ruta_img)){
      $ruta_img = 'img_default'; 
    }
  }else{
   $ruta_img = 'img_default'; 
 }  
}else{
  $ruta_img = 'img_default'; 
}

//supplier_icon
if($array_img[0] == 'supplier_icon'){
 $ruta_base = _PS_ROOT_DIR_.'/img/app/supplier/';
 $ruta_img = $array_img[1].'.jpg';
 if(!file_exists($ruta_base.$ruta_img)){
  $ruta_img = '0.jpg'; 
}
}
$full_name = $ruta_base.$ruta_img;
// var_dump($full_name);
// exit;
header("Content-Type: ".image_type_to_mime_type($extensions[strtolower($array_img[2])])); 
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".filesize($full_name));
readfile($full_name);
exit();
}