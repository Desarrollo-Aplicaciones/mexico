<?php
include_once(dirname(__FILE__)."/../../config/config.inc.php");
class GenerandoIcr extends AdminController  {



  var $generar = null ;
  var $consecutivo = null ;
  var $codinicio;
  var $codfinal;
  var $pput = 000;
  var $resultado = null;
  var $desglocel = null;
  var $desglocen = null;
  var $put;
  var $text = "AAA";
  var $totg;
  var $empleado;
  var $cod_icr;
  var $codigo_final = 0;
  var $estado = 1;
  var $cant = 0;
  var $result = 0;
  var $arr = null;
  var $letters =  null;
  var $digits = null;
  var $str = null;
  var $mysqldate = 0;
  var $codigo_inicio = 0;
  var $codigo_icr = 0;
  var $char = 0;
  var $key = 0;
  var $pepe = null;

  var $conexion;
  
  function __construct() {
    $this ->put = sprintf('%03d',$this ->pput);
    $this ->totg = $_POST['cantidad'];
    $this ->empleado = Context::getContext();

  }

############################
#metodo que conecta a la db#
############################
  
  public function conectardb(){ //datos de conexion a la db
   // $host="localhost";     $user="root";     $pass="";
   // $dbname="test_colombia";
    
$host=_DB_SERVER_;
$user=_DB_USER_;
$pass=_DB_PASSWD_;
$dbname=_DB_NAME_;

//estableciendo conexion correspondiente
    $this->conexion=mysql_connect($host,$user,$pass);

    mysql_select_db($dbname,$this->conexion)or die("Error en la selección de la base de datos");


$sql= "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."icr`(
      `id_icr` int(11) NOT NULL AUTO_INCREMENT,
  `cod_icr` varchar(6) NOT NULL,
  `id_historico` int(11) NOT NULL,
  `id_estado_icr` int(11) NOT NULL,
  PRIMARY KEY (`id_icr`)
)ENGINE=Aria AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1";


$sql2= "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."icr_history`(
      `id_icr` int(11) NOT NULL AUTO_INCREMENT,
  `cod_inicio` varchar(6) CHARACTER SET latin1 COLLATE latin1_spanish_ci DEFAULT NULL,
  `cod_final` varchar(6) DEFAULT NULL,
  `id_empleado` int(11) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_icr`)
) ENGINE=Aria AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 PAGE_CHECKSUM=1";
     
      if(!$result=Db::getInstance()->Execute($sql) && !$result=Db::getInstance()->Execute($sql2))
      

      return false;
  }




################################################
# metodo que busca campos dentro de las tablas #
################################################
  
  public function campostabla(){
    $query = mysql_query('SELECT id_icr,cod_inicio,cod_final FROM ps_icr_history WHERE id_icr =(SELECT MAX(id_icr)FROM ps_icr_history)');

    $this ->resultado=mysql_num_rows(mysql_query("select * from ps_icr_history"));


    if($this ->resultado == 0){
// se inicializan variables
      $this ->entradaInicial=$this ->text.$this ->put;
      $this ->entradaFinal = 0;


    }else{
// trae el resultado del registro inicio y final
      $row = mysql_fetch_array($query);

      $this ->entradaInicial = $row['cod_inicio'];
      $this ->entradaFinal = $row['cod_final'];
      $this ->cod_icr = $row['id_icr'];


      $this ->desglocen = substr ($this ->entradaFinal , -3 );
      $this ->desglocel = substr ($this ->entradaFinal , -6,3 );
      //echo $this ->desglocen.'--'.$this ->desglocel;


    } 

  }

###################################
# metodo que genera el codigo icr #
###################################
  
  public function generandocodigo(){
//contador

    if( $this ->resultado == 0){

      $this ->desglocen= 0;
      $this ->desglocel = "AAA";
      $this->cod_icr = 1;
    }else{
      if( $this ->desglocen == 999){
        $this ->put=0;  
        //echo "letrasss".$this ->desglocel."<br>";    
        $this ->text = $this ->get_next_in_sequence();
        

      }
      else{

        $this ->put = $this ->desglocen+1;
        $this ->text = $this ->desglocel;

      }

    }


    $this ->codigo_inicio = $this ->text.sprintf('%03d',$this ->put);

    if( $this ->put == 999 && $this ->text == "ZZZ"){
  //echo "Fin de la generación de la secuencia permitida";
  //exit;
    }
  }


###########################################
# metodo que genera el codigo consecutivo #
###########################################

  public function generandoexcel(){
// señal para los ciclos
    $generar=1;
    $this ->text = $this  ->desglocel;
    $this ->codigo_icr = array();


    if ($generar == 1 ) {
     for ($let=$this ->text; $let !='AAAA';$let = $this ->get_next_in_sequence())  {

   // echo "<br>contador ".$this ->cant."<br>este es mi let ".$let."<br>putttt".$this ->put;
      if(strlen($let) == 3) {
        for ($num = $this ->put; $num<=999; $num++) {


          if ($this ->cant < $this ->totg) {
            $this ->cant++;
            $this ->result = $let.sprintf('%03d',$num);

            $this ->codigo_icr[$this ->cant] = $this ->result;
            $this ->codigo_final = $this ->result;
          } else {
//echo "ROMPEEE".$this ->cant;
            break;

          }
        } 


        $this ->put = sprintf('%03d',$this ->pput);
        

      } else {

       // echo "<br>este es mi atributo text ".$this ->text."<br>aca rompe y el error es notable en el let ". $let;
        break;
      }


      if ($this ->cant >= $this ->totg) {
         // echo "ROMPE en letra".$this ->cant;
       break;
     }

   }

 }
}



##############################################
# metodo que genera el archivo digital excel #
##############################################

public function fisicoExcel(){


  $this ->mysqldate = date("Y-m-d H:i:s");

  if ($this ->cant >= $this ->totg) {



 //echo "<br>este es mi codigo final ".$codigo_final."<br>";

   $query =  mysql_query("INSERT INTO ps_icr_history (cod_inicio, cod_final, id_empleado, fecha, cantidad )values('".$this ->codigo_inicio."','".$this ->codigo_final."','".(int)Context::getContext()->cookie->id_employee."','".$this ->mysqldate."', '".$this ->totg."')");

   $this ->cod_icr = mysql_insert_id(); 

//echo $this ->cod_icr;
           //echo $query;


     header("Content-type: application/force-download");
    header("Content-Disposition: attachment; filename=".basename("Archivo_Rangos_Icr.xls"));
   header("Content-Transfer-Encoding: binary");

   echo "<table>";
   for($i=1;$i<=count ($this ->codigo_icr);$i++)
   {

     $query =  mysql_query("INSERT INTO ps_icr (cod_icr,id_historico,id_estado_icr) values('".$this ->codigo_icr[$i]."','".$this ->cod_icr."','".$this ->estado."')");
                // echo "<br>".$query;
     ?>

     <tr><td><?php  echo $this ->codigo_icr[$i]; ?></td></tr>
     <?php
   }
   echo "</table>";
   exit;
 }

}

#################################################
# metodo que genera el cambio de letra y numero #
#################################################


function get_next_in_sequence() {

  $str= $this ->desglocel;
  //echo "<br>funcion secuencia: ".$str;
  $letters = range('A', 'Z');
  $arr = str_split($str);
 // Replace each character with numeric equivalent
  foreach ($arr as $key => $char) {
    $arr[$key] = array_search($char, $letters); 
  }
 $digits = count($arr)-1; // Count digits
 for ($i=$digits; $i > -1; $i--) { // Starting at the right-most spot, move left
  if ($i == $digits) // If this is the right most spot
   $arr[$i]++; // Increment it
  if ($arr[$i] == 26) { // If this spot has moved past "z"
   $arr[$i] = 0; // Set it to "a"
   if ($i != 0) // Unless it is the left most spot
    $arr[$i-1]++;  // Carry the one to the next spot
  } 
}
 // Rebuild characters from numeric equivalent
foreach ($arr as $key => $char) { 
  $arr[$key] = $letters[$char];
}
$str = implode($arr);
return $str;
}

##################################################
# metodo para cambiar de estado en el codigo ICR #
##################################################
function traerParametro($encontro){
 $buscar = $_POST['buscar'];
 $query = mysql_query("SELECT cod_icr from ps_icr where cod_icr = '".$buscar."'");

 $row = mysql_fetch_array($query);
 $encontro = $row['cod_icr'];
 return $encontro;
 

}
##################################################
# metodo para cambiar de estado en el codigo ICR #
##################################################
function traerId($id_icr){
 $buscar = $_POST['buscar'];
 $query =  mysql_query("SELECT id_icr from ps_icr where cod_icr = '".$buscar."'");
 $row = mysql_fetch_array($query);
 $id_icr = $row['id_icr'];

 return $id_icr;
}
##################################################
# metodo para cambiar de estado en el codigo ICR #
##################################################
function traerEstado($id_estado_icr){
 $query =  mysql_query("SELECT id_estado_icr from ps_icr where id_estado_icr = '1'");
 $row = mysql_fetch_array($query);
 $id_estado_icr = $row['id_estado_icr'];

 return $id_estado_icr;
}

##################################################
# metodo para cambiar de estado en el codigo ICR #
##################################################
function actualizarICR($id){
  $id = $_POST['cambiarEstado'];
  try{

    if(isset($id)){
      $query =  mysql_query("SELECT id_estado_icr from ps_icr where id_icr = '".$id."'");
      $row = mysql_fetch_array($query);
      $id_estado_icr = $row['id_estado_icr'];

      if( $id_estado_icr == 1){
       
       $query = mysql_query("UPDATE ps_icr SET id_estado_icr='7' WHERE id_icr = '".$id."'");
      echo '<script language="javascript">alert("El codigo ICR ha cambiado de estado nulo");</script>';  

     }else{

      echo '<script language="javascript">alert("El codigo ICR esta en un estado actualmente que no se puede cambiar");</script>';
    }
  }else{
    echo "<br>No existe";
  }

}catch(Exeption $a){
  print_r($a);
}

}

################################################
# metodo que genera el reporte de interaciones #
################################################

function reporteICR($col_value){

 $idprofile = Context::getContext()->employee->id_profile; 


//valida si es super administrador o administrador
 if($idprofile == 1 || $idprofile == 2){
  $result123 = mysql_query("SELECT h.id_icr,h.cod_inicio,h.cod_final,e.firstname,e.lastname,h.fecha,h.cantidad from ps_icr_history h 
    INNER JOIN ps_employee e on e.id_employee = h.id_empleado"); 
}else{

  $result123 = mysql_query("SELECT h.id_icr,h.cod_inicio,h.cod_final,e.firstname,e.lastname,h.fecha,h.cantidad
    FROM ps_icr_history h INNER JOIN ps_employee e on  e.id_employee = h.id_empleado 
    where h.id_empleado = '".(int)Context::getContext()->cookie->id_employee."'");
}

return $result123;


}


}
?>


