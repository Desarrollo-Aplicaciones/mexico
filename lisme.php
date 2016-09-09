<?php
/*header( 'Content-type: text/html; charset=iso-8859-1' );*/

  header("Content-Type: application/json");
	header ("Expires: Mon, 31 Mar 2014 05:00:00 GMT"); // Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header ("Pragma: no-cache"); // HTTP/1.0
	

require(dirname(__FILE__).'/config/config.inc.php');
$search = (isset($_POST['input']) ) ? $_POST['input'] : $_GET['input'];

$url_post = explode(':', _DB_SERVER_);

$mysqli = null;

if ( count($url_post) > 1 ) {
  
  $mysqli = new mysqli($url_post[0], _DB_USER_, _DB_PASSWD_, _DB_NAME_, $url_post[1]);

} else {

  $mysqli = new mysqli(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);

}



  /* comprobar conexión */
  if ($mysqli->connect_errno) {
      printf("Conexión fallida: %s\n", $mysqli->connect_errno);
      return false;
      exit();
  }
   
   $par_busca = explode(" ", $search);

   /****** INICIO SOLO MOSTRAR VISITADORES MEDICOS  EWING   ******/
    
   $cupon_visitador = Configuration::get('PS_CUPONMED_ONLY_VISITADOR');

   if ( $cupon_visitador == 1 ) {
    $query_visitador = " AND a.id_visitador IS NOT NULL AND a.id_visitador != '' ";
   } else {
    $query_visitador = " ";
   }

   /****** FIN SOLO MOSTRAR VISITADORES MEDICOS   EWING  ******/

   //$query="SELECT id_medico, nombre FROM ps_medico WHERE nombre like '%" . $par_busca[0] . "%'"; 
   $query = "SELECT a.id_medico, a.nombre, CONCAT('[',GROUP_CONCAT(c.nombre SEPARATOR \", \"),']') especialidad
              FROM ps_medico a INNER JOIN ps_medic_especialidad b
              ON (a.id_medico = b.id_medico)
              INNER JOIN ps_especialidad_medica c 
              ON (c.id_especialidad = b.id_especialidad)
              WHERE a.nombre like '%" . $par_busca[0] . "%' ".$query_visitador;

   for ($i=1; $i < count($par_busca); $i++) {

    	if (strlen($par_busca[$i])>=3) {
   		$query.=" AND  a.nombre like '%" . $par_busca[$i] . "%' "; 
   		}
   }

   $query.=" GROUP BY a.id_medico ORDER BY a.nombre ASC LIMIT 0, 10";
      
      //echo "<br> query: ".$query;

if ($result = $mysqli->query($query)) {

		echo "{\"results\": [";
    $arr = array();
		
    /* obtener array asociativo */
    while ($row = $result->fetch_assoc()) {
       /*echo '<div class="suggest-element">
	    <a data="'.$row['nombre'].'" id="service'.$row['id_medico'].'">'.utf8_encode($row['nombre']).'</a>
	    </div>';*/
	    $arr[] = "{\"id\": \"".$row['id_medico']."\", \"value\": \"".utf8_encode($row['nombre'])."\", \"info\": \"".utf8_encode($row['especialidad'])."\"}";
    }
    echo implode(", ", $arr);
		echo "]}";
    /* liberar el resultset */
    $result->free();
}

/* cerrar la conexión */
$mysqli->close();
?>