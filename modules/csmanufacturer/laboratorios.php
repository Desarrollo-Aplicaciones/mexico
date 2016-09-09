<?php
include_once(dirname(__FILE__)."/../../config/config.inc.php");

class laboratorios extends Manufacturer{

  var $laboratorios = null ;
  var $conexion;
  var $codigo;
  
  
  function __construct() {

    if(isset($_POST['codigos'])) {
      $this ->codigo = $_POST['codigos'];
    }

  }


 public function conectardb(){ //datos de conexion a la db



  $sql= "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."cs_manufacturer`(
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `listado` varchar(6) NOT NULL,
    PRIMARY KEY (`id`)
    )ENGINE=Aria AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1";



if(!$result=Db::getInstance()->Execute($sql))


  return false;
}
               

public function cargar(){ 


 $codigo= $_POST['codigos'];

 $a = explode(",", $codigo);

 $sql="SELECT COUNT(*) FROM "._DB_PREFIX_."cs_manufacturer";
 //$resultado=mysql_query($sql) or die (mysql_error()); 
 //$total=mysql_result($resultado,0); 
 $result=Db::getInstance()->ExecuteS($sql);

 if ($result ){ 
   $borrar = "TRUNCATE TABLE "._DB_PREFIX_."cs_manufacturer";
    $result_borrar=Db::getInstance()->ExecuteS($borrar);
 } 

 foreach ($a as $insertar) {

   $query ="INSERT INTO "._DB_PREFIX_."cs_manufacturer (listado)VALUES (".$insertar.")";
   $result_query=Db::getInstance()->ExecuteS($query);

 }

}

public function mostrar(){

  $query = "SELECT psm.listado, ps.`name` FROM ps_cs_manufacturer psm INNER JOIN ps_manufacturer ps
                        on psm.listado = ps.id_manufacturer";
  return Db::getInstance()->ExecuteS($query);
}


}

?>