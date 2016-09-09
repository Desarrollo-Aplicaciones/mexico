<?php

// clase para serializar el contenido Json
class ArrayValue implements JsonSerializable {
    public function __construct(array $array) {
        $this->array = $array;
    }

    public function jsonSerialize() {
        return $this->array;
    }
}

class clientWsPs
{
    
    public $errors=array();
    public $reponse= array();
    // inicializacion del objeto
    public function __construct() {
        
    }
    
    /*
     * convertir un array a json
     */
    public function arrayToJson($array) {
        try {
            return json_encode(new ArrayValue($array), JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            $this->errors[]=array('msg'=>'Error al convertir array a Json','list'=>$e);
            return false;
        }
        return false;
    }

    public function send_orer($array) {

        try {
            $host = "127.0.0.1";

            $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

            $puerto = 1721;
            $conexion = socket_connect($socket, $host, $puerto);

            $tamano = 2048;
            if ($conexion) {

                $salida = '';
                //buffer->trabaja con almacenamiento de memoria
                socket_write($socket, $this->arrayToJson($array));

                while ($salida = socket_read($socket, $tamano)) {

                    if ($this->process_response($salida) == TRUE) {
                        break;
                    }
                }
            } else {
                //echo "\n la conexion TCP no se a podido realizar, puerto: ".$puerto;
                $this->errors[]=array('msg'=>'la conexion TCP no se a podido realizar,verifique los datos de conxión','list'=>NULL);
                return false;
            }

            socket_close($socket); //cierra el recurso socket dado por $socket
            return true;
        } catch (Exception $error) {
            $this->errors[]=array('msg'=>'Error de conexión','list'=>$error);
            return false;
        }
    }

    /*
     * Procesar salida
     */

    public function process_response($json) {
        if (isset($json)&& !empty($json)) {
         $this->reponse=json_decode($json, TRUE);
         
            echo '<pre>';
            echo '<pre><br> <br> <br><br><br> <b>Json respuesta del servidor <br> </b>';
            print_r($this->reponse);
            echo '<br><br>';
            print_r($this->errors);
           return TRUE;
        }
    }

}

$cliente=new clientWsPs();

// structura para el envio de productos
$productos=array(array('id_product_sugar' => '1242a78a-9666-a6a7-da69-751875e3ed3c',
            'quantity' => 3,
            'id_product_ps' => 7120),
    array('id_product_sugar' => 'c5f1012c-0aa4-d642-410b-c40da74d6392',
            'quantity' => 4,
            'id_product_ps' => 991)
    );
$_voucher=169085;
$id_customer=249;
$id_address=240;
$id_employee=14;
// estructura orden
$order=array('products'=>$productos,'id_customer'=>$id_customer,'id_voucher'=>$_voucher,'id_address'=>$id_address);

// estructura del objeto Json
$obj=array('entity'=>'order','id_employee'=>$id_employee,'action'=>'add','content'=>$order);

$cliente->send_orer($obj);
