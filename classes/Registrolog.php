<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of utilFilesClass
 *
 * @author Ewing
 */

class RegistrologCore extends ObjectModel {

   private $fp;
   private $log_file;
 
  /**
   * @see ObjectModel::$definition
   */
  public static $definition = array(
    'table' => 'alias',
    'primary' => 'id_alias',
  );

    public function lfile($path) {
        $this->log_file = $path;
    }

    // write message to the log file
    public function lwrite($module, $archivo, $message) {
        // if file pointer doesn't exist, then open log file
        if (!is_resource($this->fp)) {
            $this->lopen($module, $archivo);
        }
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time and suppress E_WARNING if using the system TZ settings
        // (don't forget to set the INI setting date.timezone)
        $time = @date('[d/M/Y:H:i:s]');
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message" . PHP_EOL);
    }

    // close log file (it's always a good idea to close a file when you're done with it)
    public function lclose() {
        fclose($this->fp);
    }

    // open log file (private method)
    private function lopen($sub_dir, $archivo) {

        $log_file_default=$this->pathFiles($sub_dir).$archivo; //module    

        // define log file from lfile method or use previously set default
        $lfile = $this->log_file ? $this->log_file : $log_file_default;
        // open log file for writing only and place file pointer at the end of the file
        // (if the file does not exist, try to create it)
        $this->fp = fopen($lfile, 'a') or exit("No se puede abrir el archivo $lfile!");
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
            
            echo "Error crear el archivo temporal, consulte con su administrador ". $e->getMessage();
            return false;
        }
            echo "Error crear el archivo temporal, consulte con su administrador ";
            return false;
    }

}