<?php

require(dirname(__FILE__).'/../../config/config.inc.php');

$output_dir = "uploads/";

if (isset($_POST) && isset($_POST['empid'])) {

$employee = new Employee($_POST['empid']);

    //validar que se recibieron datos

    if ( isset($_POST['submitUpdatePrice']) && isset($_FILES["myfile"]) 
        && $_POST['submitUpdatePrice'] == 'Actualizar Precios Transportistas' && $_FILES["myfile"] != '' ) {
            //validar que se tenga el id de la publicidad
    
        $cargar_archivo = new CargaFiles();

        $cargaTR = new PrecioTransporte();

                    $names =  $cargar_archivo->saveFile($_FILES,'myfile',$employee,'TransporteCiudades/uploads'); 

                //print_r($names);

                    if (is_array($names) && $names[0] != '' && $names[0] != false && $names[2] !=false ){


                        if ( $cargaTR->loaduptranscp($names[2])  && $cargaTR->validarCodigop() 
                            && $cargaTR->actualizarCodigop() && $cargaTR->insertarCodigop() ) {

                            if ( $cargaTR->reporteCodigopMaloCount() ) {
                                if ( $cargaTR->cant_error_carga > 0) {
                                    echo "<font color='red'><a href='../modules/TransporteCiudades/erroresCargue.php' download> >> Ver ".
                                    $cargaTR->cant_error_carga." errores de cargue, de ".
                                    $cargaTR->cant_cargados." Registros <<  </a> &nbsp;&nbsp;&nbsp; ".
                                    ($cargaTR->cant_cargados - $cargaTR->cant_error_carga)." Códigos actualizados. </font>";
                                } else {
                                    echo "<font color='green'> <bold> Precios de transportistas actualizados correctamente. ".
                                    $cargaTR->cant_cargados." Registros. </bold> </font>";
                                }
                                
                            } else {
                                echo "<font color='red'>".implode("<br>", $cargaTR->errores_cargue)."</font>";
                            }

                        } else {
                            echo "<font color='red'>Error, No se pudo cargar el archivo enviado </font>";
                        }

                    } else {
                        
                        echo "<br> <font color='red'> Error en el archivo cargado.<br>";
                        if (count($names[3]>0)) { echo implode("<br>", $names[3]) ; } // Imprimir errores del cargue
                        echo "</font>";
                    }
               
    } elseif ( isset($_POST['submitUpdatePrice']) && isset($_FILES["myfile"]) 
        && $_POST['submitUpdatePrice'] == 'Actualizar Precios Transportistas Ciudades' && $_FILES["myfile"] != '' ) {
            //validar que se tenga el id de la publicidad
    
        $cargar_archivo = new CargaFiles();

        $cargaTR = new PrecioTransporte();

                    $names =  $cargar_archivo->saveFile($_FILES,'myfile',$employee,'TransporteCiudades/uploads'); 

                    if (is_array($names) && $names[0] != '' && $names[0] != false && $names[2] !=false ){


                        if ( $cargaTR->loaduptransciudad($names[2]) && $cargaTR->validarCiudadTransporteDuplicado() 
                            && $cargaTR->validarCiudad() && $cargaTR->actualizarCiudad() && $cargaTR->insertarCiudad() ) {

                            if ( $cargaTR->reporteCiudadMaloCount() ) {
                                if ( $cargaTR->cant_error_carga > 0) {
                                    echo "<font color='red'><a href='../modules/TransporteCiudades/erroresCargue.php?t=ciudades' download> >> Ver ".
                                    $cargaTR->cant_error_carga." errores de cargue, de ".
                                    $cargaTR->cant_cargados." Registros <<  </a> &nbsp;&nbsp;&nbsp; ".
                                    ($cargaTR->cant_cargados - $cargaTR->cant_error_carga)." Ciudades actualizadas. </font>";
                                } else {
                                    echo "<font color='green'> <bold> Precios de transportistas y ciudades actualizados correctamente. ".
                                    $cargaTR->cant_cargados." Registros. </bold> </font>";
                                }
                                
                            } else {
                                echo "<font color='red'>".implode("<br>", $cargaTR->errores_cargue)."</font>";
                            }

                        } else {
                            echo "<font color='red'>Error, No se pudo cargar el archivo enviado </font>";
                        }

                    } else {
                        
                        echo "<br> <font color='red'> Error en el archivo cargado.<br>";
                        if (count($names[3]>0)) { echo implode("<br>", $names[3]) ; } // Imprimir errores del cargue
                        echo "</font>";
                    }
               
    } 
    elseif (isset ($_POST['submit_mediosp_ciudades']) &&  !empty($_POST['submit_mediosp_ciudades']) && isset ($_POST['empid']) && !empty ($_POST['empid']) && isset($_FILES['file_mediosp_ciudades']) && !empty($_FILES['file_mediosp_ciudades']) ) {
  
          
        $cargar_archivo = new CargaFiles();
         $names =  $cargar_archivo->saveFile($_FILES,'file_mediosp_ciudades',$employee,'TransporteCiudades/uploads/mediosp_ciudades'); 
         
           $carga_ciudades_mediosp = new PrecioTransporte();
         
         if (is_array($names) && $names[0] != '' && $names[0] != false && $names[2] !=false ){
             
             if($carga_ciudades_mediosp->load_ciudades_mediosp($names[2]) && $carga_ciudades_mediosp->validar_ciudades_mediosp()){
        
                 $carga_ciudades_mediosp->update_ciudades_mediosp();

             } else
             {
                 
                echo "<font color='red'><a href='/modules/TransporteCiudades/errores_duplicados.php?duplicados=duplicados'>Descargar listado de duplicados</a></font>";        
                
             }
          
         }
 
}
    else  {
        echo "<br>NO SE ENVIARON TODOS LOS DATOS.";
    } 

 } else {
    echo "No se recibió ningún dato";
 }

?>