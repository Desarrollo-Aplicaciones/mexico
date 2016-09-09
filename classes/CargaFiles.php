<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of utilFilesClass
 *
 * @author German
 */
 //extends ObjectModel
class CargaFilesCore extends ObjectModel {
    
   private  $nuevo_archivo;
 
  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
    'table' => 'customized_data',
    'primary' => 'id_customization',
    'fields' => array(
      'index' => array('type' => self::TYPE_INT, 'required' => true,),
    ),
  );   

    // Listado de errores en el cargue
    public $errores_cargue = array();

/**
     * Guarda un archivo proveniente de una solicitud http
     *
     * @array_file arreglo de archivos html
     * @name_file varible html del archivo
     * @$employee objeto empledado prestashop
     * @$module nombre del modulo
     * @return Array [0] =>'Nuevo nombre', [1] =>'Nombre original', [2]=>'Ruta completa' , [3]=>'array de errores'
     */
    public function saveFile($array_file, $name_file, $employee, $module) {

        $this->empledado = $employee;
        $full_path = null;
        // Sustituir especios por guion
        $nombre_archivo = str_replace(' ', '-', $array_file[$name_file]['name']);

        $extencion = strrchr($array_file[$name_file]['name'], '.');

        // Rutina que asegura que no se sobre-escriban documentos        
        $flag = true;
        while ($flag) {
            $nuevo_archivo = $this->randString() . '_' . $employee->id . '_' . '_' . $this->sanear_string($employee->firstname) . '_' . $this->sanear_string($employee->lastname) . $extencion; //.$extencion;

            $full_path = $this->pathFiles($module) . $nuevo_archivo;
            if (!file_exists($this->pathFiles($module) . $nuevo_archivo)) {
                $flag = false;
            }
        }
        //Validar caracteristicas del archivo
        try {

            if (move_uploaded_file($array_file[$name_file]['tmp_name'], $this->pathFiles($module) . $nuevo_archivo)) {
                chmod($this->pathFiles($module) . $nuevo_archivo, 0755);

                //retorna array en [0]=>'nombre original', [1]=>'nuevo nombre', [2]=>'ruta del archivo'
                return $vector = array($nuevo_archivo, $nombre_archivo, $full_path);
            } else {

                // retorna un arrar con elementos en false
                return $vector = array(false, false, false,$this->errores_cargue);
            }
        } catch (Exception $e) {
            echo 'Error en la Función saveFile --> CargaFiles ', $e->getMessage(), "\n";
            exit;
        }
    }


    /**
     * [Limpiar cadenas con tildes y/o caracteres especiales]
     * @param  [type] $string [cadena con caracteres especiales]
     * @return [type]         [cadena limpia]
     */
    public function sanear_string($string){

        $string= utf8_encode($string);

        $a = array('Ã€', 'Ã?', 'Ã‚', 'Ãƒ', 'Ã„', 'Ã…', 'Ã†', 'Ã‡', 'Ãˆ', 'Ã‰', 'ÃŠ', 'Ã‹', 'ÃŒ', 'Ã?', 'ÃŽ', 'Ã?', 'Ã?', 'Ã‘', 'Ã’', 'Ã“', 'Ã”', 'Ã•', 'Ã–', 'Ã˜', 'Ã™', 'Ãš', 'Ã›', 'Ãœ', 'Ã?', 'ÃŸ', 'Ã ', 'Ã¡', 'Ã¢', 'Ã£', 'Ã¤', 'Ã¥', 'Ã¦', 'Ã§', 'Ã¨', 'Ã©', 'Ãª', 'Ã«', 'Ã¬', 'Ã­', 'Ã®', 'Ã¯', 'Ã±', 'Ã²', 'Ã³', 'Ã´', 'Ãµ', 'Ã¶', 'Ã¸', 'Ã¹', 'Ãº', 'Ã»', 'Ã¼', 'Ã½', 'Ã¿', 'Ä€', 'Ä?', 'Ä‚', 'Äƒ', 'Ä„', 'Ä…', 'Ä†', 'Ä‡', 'Äˆ', 'Ä‰', 'ÄŠ', 'Ä‹', 'ÄŒ', 'Ä?', 'ÄŽ', 'Ä?', 'Ä?', 'Ä‘', 'Ä’', 'Ä“', 'Ä”', 'Ä•', 'Ä–', 'Ä—', 'Ä˜', 'Ä™', 'Äš', 'Ä›', 'Äœ', 'Ä?', 'Äž', 'ÄŸ', 'Ä ', 'Ä¡', 'Ä¢', 'Ä£', 'Ä¤', 'Ä¥', 'Ä¦', 'Ä§', 'Ä¨', 'Ä©', 'Äª', 'Ä«', 'Ä¬', 'Ä­', 'Ä®', 'Ä¯', 'Ä°', 'Ä±', 'Ä²', 'Ä³', 'Ä´', 'Äµ', 'Ä¶', 'Ä·', 'Ä¹', 'Äº', 'Ä»', 'Ä¼', 'Ä½', 'Ä¾', 'Ä¿', 'Å€', 'Å?', 'Å‚', 'Åƒ', 'Å„', 'Å…', 'Å†', 'Å‡', 'Åˆ', 'Å‰', 'ÅŒ', 'Å?', 'ÅŽ', 'Å?', 'Å?', 'Å‘', 'Å’', 'Å“', 'Å”', 'Å•', 'Å–', 'Å—', 'Å˜', 'Å™', 'Åš', 'Å›', 'Åœ', 'Å?', 'Åž', 'ÅŸ', 'Å ', 'Å¡', 'Å¢', 'Å£', 'Å¤', 'Å¥', 'Å¦', 'Å§', 'Å¨', 'Å©', 'Åª', 'Å«', 'Å¬', 'Å­', 'Å®', 'Å¯', 'Å°', 'Å±', 'Å²', 'Å³', 'Å´', 'Åµ', 'Å¶', 'Å·', 'Å¸', 'Å¹', 'Åº', 'Å»', 'Å¼', 'Å½', 'Å¾', 'Å¿', 'Æ’', 'Æ ', 'Æ¡', 'Æ¯', 'Æ°', 'Ç?', 'ÇŽ', 'Ç?', 'Ç?', 'Ç‘', 'Ç’', 'Ç“', 'Ç”', 'Ç•', 'Ç–', 'Ç—', 'Ç˜', 'Ç™', 'Çš', 'Ç›', 'Çœ', 'Çº', 'Ç»', 'Ç¼', 'Ç½', 'Ç¾', 'Ç¿', 'Î†', 'Î¬', 'Îˆ', 'Î­', 'ÎŒ', 'ÏŒ', 'Î?', 'ÏŽ', 'ÎŠ', 'Î¯', 'ÏŠ', 'Î?', 'ÎŽ', 'Ï?', 'Ï‹', 'Î°', 'Î‰', 'Î®');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o', 'Î‘', 'Î±', 'Î•', 'Îµ', 'ÎŸ', 'Î¿', 'Î©', 'Ï‰', 'Î™', 'Î¹', 'Î¹', 'Î¹', 'Î¥', 'Ï…', 'Ï…', 'Ï…', 'Î—', 'Î·');
         
        return str_replace($a, $b, $string);

    }


    /*
     * Genera una cadena aleatoria
     * @length longitud de la cadena, por defecto es 4 
     * 
     */

    public function randString($length = 4) {
        $string = "";
        $possible = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXY";
        $i = 0;
        while ($i < $length) {
            $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
            $string .= $char;
            $i++;
        }
        return date("dmY_Hi_") . $string;
    }


    /*
     * verifica si un archivo existe
     * @name_file nombre del archivo
     * @sub_dir para-metro opcional en caso de utilizar subcarpetas
     */

    public function exist_file($name_file, $sub_dir) {
        if (file_exists($this->pathFiles($sub_dir) . $name_file)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * retorna la ruta de los archivos
     * @sub_dir para-metro opcional en caso de utilizar subcarpetas
     */

    public function pathFiles($sub_dir) {
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
            
            $this->errores_cargue[] = "Error al crear el archivo temporal, consulte con su administrador ". $e->getMessage();
            return false;
        }
           $this->errores_cargue[] = "Error al crear el archivo temporal, consulte con su administrador ";
            return false;
    }

  


}
