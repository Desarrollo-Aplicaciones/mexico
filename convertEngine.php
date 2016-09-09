<?php  
$tiempo_inicio = microtime(true);
/* Conectar a una base de datos de ODBC invocando al controlador */
$host='10.0.1.240';
$usuario = 'farmalisto';
$contraseña = 'f4rm4l1st02013**';
$dbName='farmalisto_colombia';
$engine = 'Aria';

$dsn = 'mysql:dbname='.$dbName.';host='.$host;


try {

    $gbd = new PDO($dsn, $usuario, $contraseña);
    $i=0;
     foreach($gbd->query('show tables') as $result) {
     
     if ($result) {
    
     echo $result[0].'<br>';
    
    $gbd->query('ALTER TABLE '.$result[0].' ENGINE='.$engine);
    $i++;
}
else{
die('query failed: ');
}

}

echo '<b>Se convirtieron ('.$i.') tablas al motor '.$engine.'</b>';

} catch (PDOException $e) {
    echo 'Falló la conexión: ' . $e->getMessage();
     die();
}
$tiempo_fin = microtime(true);
echo "<br>Tiempo empleado: " . ($tiempo_fin - $tiempo_inicio);
?>