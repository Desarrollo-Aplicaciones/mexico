<?php

require(dirname(__FILE__).'/../../config/config.inc.php');

$output_dir = "uploads/";

if (isset($_POST)) {

    //validar que se recibieron datos

    if ( isset($_POST['id_publicidad']) && isset($_POST['submit']) && isset($_POST['tipo']) 
        && $_POST['id_publicidad'] != '' && $_POST['submit'] == 'Cambiar publicidad' &&  $_POST['tipo'] != '' ) {
            //validar que se tenga el id de la publicidad

        switch ( $_POST['tipo'] ) {
            case 'banner':
                if ( isset($_FILES["myfile"]) && $_POST['altoimg'] !='' && $_POST['anchoimg'] != '' && isset($_POST["activo"])) {

                    $names =  saveFile($_FILES,'myfile',$_POST['id_publicidad'],'cspublicidadfl/uploads', $_POST['altoimg'],$_POST['anchoimg']); 

                    if (is_array($names) && $names[0] != '' && $names[0] != false && $names[2] !=false ){

                        $cambio_publicidad = "UPDATE ". _DB_PREFIX_ ."publicidad SET 
                                tipo = '".$_POST['tipo']."',
                                link= '".$_POST['link']."',
                                imagen= '".$names[0]."',
                                adsense= '".$_POST['adsense']."',
                                activo= '".$_POST['activo']."' 
                                WHERE id_publicidad = '".$_POST['id_publicidad']."'";

                        if ($result_datos_up = Db::getInstance()->ExecuteS($cambio_publicidad)) {

                           /* $query_config = "SELECT * FROM ". _DB_PREFIX_ ."publicidad WHERE id_publicidad = '".$_POST['id_publicidad']."'";

                   
                            if ($load_config = Db::getInstance()->ExecuteS($query_config)) {
                                foreach ($load_config as $value) {
                                    echo "<br>id_publicidad: ".$value['id_publicidad'];            
                                    echo "<br>pagina: ".$value['pagina'];
                                    echo "<br>ubicacion: ".$value['ubicacion'];
                                    echo "<br>tipo: ".$value['tipo'];
                                    echo "<br>link: ".$value['link'];
                                    echo "<br>imagen: ".$value['imagen'];
                                    echo "<br>adsense: ".$value['adsense'];
                                    echo "<br>activo: ".$value['activo'];
                                }
                            }*/
                            echo "<br> Se ha modificado la publicidad # ".$_POST['id_publicidad']." Correctamente";
                        } else {
                            echo "<br> No se pudo actualizar la publicidad # ".$_POST['id_publicidad'];
                        }

                    } else {
                        echo "<br> Error en el archivo cargado";
                    }


                } else {
                     echo "<br> <font color='red'>Error, no se envió imagen a actualizar.</font>";
                     $cambio_publicidad = "UPDATE ". _DB_PREFIX_ ."publicidad SET 
                                link= '".$_POST['link']."',
                                adsense= '',
                                activo= '".$_POST['activo']."' 
                                WHERE id_publicidad = '".$_POST['id_publicidad']."'";

                        if ($result_datos_up = Db::getInstance()->ExecuteS($cambio_publicidad)) {
                            echo "<br> Se ha modificado la publicidad # ".$_POST['id_publicidad']." Sin imagen, Correctamente";
                        } else {
                            echo "<br><font color='red'>Error, no se modificó la publicidad # ".$_POST['id_publicidad'].".</font>";
                        }
                }
                
                break;

            case 'adsense':

                 if ( isset($_POST["adsense"]) && $_POST['adsense'] !='' && isset($_POST["activo"]) ) {                   

                        $cambio_publicidad = "UPDATE ". _DB_PREFIX_ ."publicidad SET 
                                tipo = '".$_POST['tipo']."',
                                link= '".$_POST['link']."',
                                adsense= '".$_POST['adsense']."',
                                activo= '".$_POST['activo']."' 
                                WHERE id_publicidad = '".$_POST['id_publicidad']."'";

                        if ($result_datos_up = Db::getInstance()->ExecuteS($cambio_publicidad)) {

                            echo "<br> Se ha modificado la publicidad # ".$_POST['id_publicidad']." Correctamente";
                        } else {
                            echo "<br> No se pudo actualizar la publicidad # ".$_POST['id_publicidad'];
                        }

                   

                } else {
                    echo "<br>No se han enviado los datos necesarios.";
                }
                break;

            default:
                echo "<br> Opción no disponible.";
                break;
        }

    } else {
        echo "<br>NO SE ENVIARON TODOS LOS DATOS.";
    }





 } else {
    echo "yucas";
 }


/**
     * Guarda un archivo proveniente de una solicitud http
     *
     * @array_file  arreglo de archivos html
     * @name_file varible html del archivo
     * @$employee objeto empledado prestashop
     * @$module nombre del modulo
     * @return Array [0] =>'Nuevo nombre', [1] =>'Nombre original', [2]=>'Ruta completa'
     */
    function saveFile($array_file, $name_file, $id_publicidad, $module, $alto, $ancho) {

        
        $full_path = null;
        // Sustituir especios por guion
        //echo "<br>nombre: ".
        $nombre_archivo = str_replace(' ', '-', $array_file[$name_file]['name']);

        $tamano = list($ancho_orig, $alto_orig) = getimagesize($array_file[$name_file]['tmp_name']);
       
        /*echo "<br>tamaño: <pre>";
        print_r($tamano);
        echo "<br>: tamaño</pre>";        

        echo "<br>ancho_orig: ".$ancho_orig;
        echo "<br>alto_orig: ".$alto_orig;
        */
       
        //echo "<br>ext: ".
        $extencion = strrchr($array_file[$name_file]['name'], '.');

        $_exts = array("image/jpg", "image/jpeg", "image/png", "image/gif"); // Tipos de archivos soportados

        if (in_array(strtolower($array_file[$name_file]['type']), $_exts)) {
            //echo "<br>Tipo de archivo soportado.";
        } else {
            echo "<br>Tipo de archivo NO soportado.";
            return false;
        }

        if ($ancho_orig != $ancho || $alto_orig != $alto) {
            echo "<br>Dimensiones de archivo no permitidas.<br>Se requiere Alto X Ancho ".$alto." X ".$ancho.", se recibió: ".$alto_orig." X ".$ancho_orig;
            return false;
        } 
        //echo "<br> dimensiones OK";

        // Rutina que asegura que no se sobre-escriban documentos
        $nuevo_archivo = '';
        $flag = true;
        while ($flag) {
            $nuevo_archivo = $id_publicidad. $extencion; //.$extencion;

            //echo "<br>path: ".
            $full_path = pathFiles($module) . $nuevo_archivo;
            if (!file_exists(pathFiles($module) . $nuevo_archivo)) { // no existe
                echo "<br>Imagen Banner cargada.";
                $flag = false;
            } else { // existe
                echo "<br>Imagen Banner actualizada.";
            }
            break;
        }
        
        //Validar caracteristicas del archivo
        try {

            if (move_uploaded_file($array_file[$name_file]['tmp_name'], pathFiles($module) . $nuevo_archivo)) {
                chmod(pathFiles($module) . $nuevo_archivo, 0755);
                //echo "<br> subio archivo: ".pathFiles($module) . $nuevo_archivo;
                //retorna array en [0]=>'nombre original', [1]=>'nuevo nombre', [2]=>'ruta del archivo'
                return $vector = array($nuevo_archivo, $nombre_archivo, $full_path);
            } else {
                //echo "<br> NO subio archivo";
                // retorna un arrar con elementos en false
                return $vector = array(false, false, false);
            }
        } catch (Exception $e) {
            echo 'Error en la Función seveFile  ', $e->getMessage(), "\n";
            exit;
        }
    }


    /*
     * verifica si un archivo existe
     * @name_file nombre del archivo
     * @sub_dir para-metro opcional en caso de utilizar subcarpetas
     */

    function exist_file($name_file, $sub_dir) {
        if (file_exists(pathFiles($sub_dir) . $name_file)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * retorna la ruta de los archivos
     * @sub_dir para-metro opcional en caso de utilizar subcarpetas
     */

    function pathFiles($sub_dir) {
        // Definir directorio donde almacenar los archivos, debe terminar en "/" 
       // $directorio = "C:/wamp/www/prod.farmalisto.com.co/filesX/";
        $directorio = Configuration::get('PATH_UP_LOAD');
                

        if (isset($sub_dir) && $sub_dir != '') {
            $directorio.=$sub_dir . '/';
        }

        try {
            $path = "" . $directorio;

            if (!file_exists($path)) {
                mkdir($path, 0755, TRUE);
            }
            return $path;
        } catch (Exception $e) {
            
            echo "<br>Error crear el archivo temporal, consulte con su administrador ". $e->getMessage();
            return false;
        }
           echo "<br>Error crear el archivo temporal, consulte con su administrador ";
            return false;
    }

?>