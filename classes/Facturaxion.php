
<?php
if (! class_exists('Facturaxion')) {

    class FacturaxionCore
    {

        public $xmlPreCFDI = '';

        public $debug_mode = 0;

        public $test_char = '';

        public $test_mode = true;

        public $rfc_emisor = '';

        public $numero_certificado = '';

        public $numero_certificadoS = '';

        public $dir_server = _PS_ROOT_DIR_;

        public $sello_emisor = '';

        public $certificado_emisor = '';

        public $array_xml_a_timbrar = '';

        public $xml_a_timbrar = '';

        public $archivo_cer = "";

        // Prueba de timbrado
        public $archivo_pem = "";

        // Prueba de timbrado
        public $usuario = "";

        public $proveedor = "";

        public $sucursal = "";

        public $RFCEmisor = '';

        public $RFCEmisor_nombre = '';

        public $regimenfiscal = "";

        public $cadena_original = "";

        /**
         */
        public function __construct($test_mode = false)
        {
            $test_mode = Configuration::get('PRODUCTION_FACTURAXION');
            
            if ($test_mode == '1') {
                
                $this->numero_certificado = "00001000000304972067";
                $this->archivo_cer = "xml_timbrado" . DIRECTORY_SEPARATOR . "certi_valido" . DIRECTORY_SEPARATOR . "00001000000304972067.cer"; // old 00001000000304972067.cer"; // Produccion
                $this->archivo_pem = "xml_timbrado" . DIRECTORY_SEPARATOR . "certi_valido" . DIRECTORY_SEPARATOR . "publica.key.pem"; // Produccion
                $this->usuario = "1130A7374C35496EACF575158BA20DEEF49494A2";
                $this->usuarioxml = "FME140730J95";
                $this->contrasenia = "1130A7374C35496EACF575158BA20DEEF49494A2";
                $this->proveedor = "FME140730J95";
                $this->sucursal = "3204C082-CE9E-E411-93F6-005056B8554D"; // 961549
                $this->RFCEmisor = 'FME140730J95';
                $this->RFCEmisor_nombre = 'FARMATALAM DE MEXICO S DE R.L DE C.V';
                // Código Proveedor (CP):D4C3825A19DB08FD73C025F9F8A54302F7F7A379
                $this->test_mode = $test_mode = false;
                $this->regimenfiscal = "REGIMEN GENERAL DE LEY DE LAS PERSONAS MORALES";
            } else {
                
                $this->test_char = 't_';
                $this->test_mode = true;
                
                $this->numero_certificadoS = "30001000000300023708";
                $this->numero_certificado = "30001000000300023708";
                $this->archivo_cer = "xml_timbrado" . DIRECTORY_SEPARATOR . "certi_prueba" . DIRECTORY_SEPARATOR . "CSD01_AAA010101AAA.cer"; // Prueba de timbrado
                $this->archivo_pem = "xml_timbrado" . DIRECTORY_SEPARATOR . "certi_prueba" . DIRECTORY_SEPARATOR . "certificate1.pem"; // Prueba de timbrado
                
                $this->usuario = "1763AAB0593430490B3B3EE5457A9A2580F9D7DE";
                $this->usuarioxml = "demo";
                $this->contrasenia = "123456";
                $this->proveedor = "N#@Mo!)#oh&amp;gt;)BYOdX=q_ZUCsLxqpv?";
                $this->sucursal = ""; // 151048
                $this->RFCEmisor = 'AAA010101AAA';
                $this->RFCEmisor_nombre = 'Emisor de prueba';
                $this->regimenfiscal = "REGIMEN GENERAL DE LEY DE LAS PERSONAS MORALES";
            }
        }

        /**
         * ********************* PRUEBA SI FUNCIONA **************************
         */
        public function simplexml_to_array($xml, &$array)
        {
            
            // Empty node : <node></node>
            $array[$xml->getName()] = '';
            
            // Nodes with children
            foreach ($xml->children() as $child) {
                $this->simplexml_to_array($child, $array[$xml->getName()]);
            }
            
            // Node attributes
            foreach ($xml->attributes() as $key => $att) {
                $array[$xml->getName()]['@attributes'][$key] = (string) $att;
            }
            
            // Node with value
            if (trim((string) $xml) != '') {
                $array[$xml->getName()][] = (string) $xml;
            }
        }

        /**
         * ********************* PRUEBA SI FUNCIONA **************************
         */
        
        /**
         *
         * @param unknown $template_info
         * @param unknown $xml_template_info
         */
        public function array_to_xml($template_info, &$xml_template_info)
        {
            foreach ($template_info as $key => $value) {
                if (is_array($value)) {
                    if (! is_numeric($key)) {
                        
                        $subnode = $xml_template_info->addChild("$key");
                        
                        if (count($value) > 1 && is_array($value)) {
                            $jump = false;
                            $count = 1;
                            foreach ($value as $k => $v) {
                                if (is_array($v)) {
                                    if ($count ++ > 1)
                                        $subnode = $xml_template_info->addChild("$key");
                                    
                                    $this->array_to_xml($v, $subnode);
                                    $jump = true;
                                }
                            }
                            if ($jump) {
                                goto LE;
                            }
                            $this->array_to_xml($value, $subnode);
                        } else
                            $this->array_to_xml($value, $subnode);
                    } else {
                        $this->array_to_xml($value, $xml_template_info);
                    }
                } else {
                    $xml_template_info->addChild("$key", "$value");
                }
                
                LE:
                ;
            }
        }

        /**
         *
         * @param boolean $xml_sellar
         * @param number $depurar
         */
        public function LlavesRegistroTimbrado($xml_sellar = false, $depurar = 0)
        {
            $rfc_emisor = $this->RFCEmisor; // Prueba de timbrado
                                            
            // Archivos del CSD de prueba proporcionados por el SAT.
                                            // ver http://developers.facturacionmoderna.com/webroot/CertificadosDemo-FacturacionModerna.zip
            $numero_certificado = $this->numero_certificadoS; // Prueba de timbrado
            $archivo_cer = $this->archivo_cer; // Prueba de timbrado
            $archivo_pem = $this->archivo_pem; // Prueba de timbrado
                                               
            // Datos de acceso al ambiente de pruebas
            
            $dir = $this->dir_server;
            
            // generar y sellar un XML con los CSD de pruebas
            if ($xml_sellar !== false) {
                
                $cfdi = $xml_sellar;
            } else {
                
                $cfdi = $this->generarXML($rfc_emisor);
            }
            
            $cfdi = $this->sellarXML($cfdi, $numero_certificado, $archivo_cer, $archivo_pem, $depurar);
        }

        /**
         *
         * @param unknown $cfdi
         * @param unknown $numero_certificado
         * @param unknown $archivo_cer
         * @param unknown $archivo_pem
         * @param number $depurar
         */
        function sellarXML($cfdi, $numero_certificado, $archivo_cer, $archivo_pem, $depurar = 0)
        {
            if ($depurar == 1) {
                echo "<br> entra funcion sellarXML";
            }
            
            $private = openssl_pkey_get_private(file_get_contents($this->dir_server . DIRECTORY_SEPARATOR . $archivo_pem));
            $this->certificado_emisor = $certificado = str_replace(array(
                '\n',
                '\r'
            ), '', base64_encode(file_get_contents($this->dir_server . DIRECTORY_SEPARATOR . $archivo_cer)));
            
            if ($depurar == 1) {
                echo "<br> Alistando para sellar:<br><textarea style='height: 404px; width: 1339px;' >";
                echo $cfdi;
                echo "</textarea><br><hr>";
            }
            
            $xdoc = new DomDocument();
            $foDir = $this->dir_server . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'facturaxion' . DIRECTORY_SEPARATOR;
            if (! is_dir($foDir)) {
                mkdir($foDir, 0755, TRUE);
            }
            
            $foPath = ( $foDir. 're_') . date("Y-m-d") . "T" . date("H-i-s", strtotime('-10 minute', strtotime(date("H:i:s")))) . $this->test_char . "_cfdi.xml";
            $fp_cfdi = fopen($foPath, "a+");
            
            fwrite($fp_cfdi, $cfdi);
            fclose($fp_cfdi);
            
            if ($depurar == 1) {
                @ini_set('display_errors', 'on');
                @error_reporting(E_ALL | E_STRICT);
                $xdoc->loadXML($cfdi) or die("<br>XML invalido");
                echo "<br> luego funcion loadXML";
            }
            
            $xdoc->loadXML($cfdi);
            
            $XSL = new DOMDocument();
            $XSL->load($this->dir_server . DIRECTORY_SEPARATOR . 'xml_timbrado' . DIRECTORY_SEPARATOR . 'utilerias' . DIRECTORY_SEPARATOR . 'xslt32' . DIRECTORY_SEPARATOR . 'cadenaoriginal_3_3.xslt');
            
            if ($depurar == 1) {
                echo "<br> luego funcion load";
            }
            
            $proc = new XSLTProcessor();
            $proc->importStyleSheet($XSL);
            
            if ($depurar == 1) {
                echo "<br> luego funcion importStyleSheet";
            }
            
            $cadena_original = $proc->transformToXML($xdoc);
            
            if ($depurar == 1) {
                echo "<br> luego funcion transformToXML";
            }
            
            openssl_sign($this->cadena_original, $sig, $private, OPENSSL_ALGO_SHA256);
            
            if ($depurar == 1) {
                echo "<br> luego funcion openssl_sign";
            }
            
            $this->sello_emisor = $sello = base64_encode($sig);
            
            $this->array_xml_a_timbrar['@attributes']['Certificado'] = $this->certificado_emisor;
            $this->array_xml_a_timbrar['@attributes']['Sello'] = $this->sello_emisor;
            
            if ($depurar == 1) {
                echo "<pre> sello c:<br>";
                print_r($sello);
                echo "<br> certificado: ";
                print_r($certificado);
                echo "<br> numero_certificado: ";
                print_r($numero_certificado);
                
                echo "</pre> <br>";
            }
            
            // return $xdoc->saveXML();
        }

        /**
         *
         * @param unknown $rfc_emisor
         * @return string
         */
        function generarXML($rfc_emisor)
        {
            $cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" version="3.2" fecha="2015-01-23T12:57:16" formaDePago="PAGO EN UNA SOLA EXHIBICION" metodoDePago="Efectivo" noCertificado="20001000000100005867" certificado="MIIEdDCCA1ygAwIBAgIUMjAwMDEwMDAwMDAxMDAwMDU4NjcwDQYJKoZIhvcNAQEFBQAwggFvMRgwFgYDVQQDDA9BLkMuIGRlIHBydWViYXMxLzAtBgNVBAoMJlNlcnZpY2lvIGRlIEFkbWluaXN0cmFjacOzbiBUcmlidXRhcmlhMTgwNgYDVQQLDC9BZG1pbmlzdHJhY2nDs24gZGUgU2VndXJpZGFkIGRlIGxhIEluZm9ybWFjacOzbjEpMCcGCSqGSIb3DQEJARYaYXNpc25ldEBwcnVlYmFzLnNhdC5nb2IubXgxJjAkBgNVBAkMHUF2LiBIaWRhbGdvIDc3LCBDb2wuIEd1ZXJyZXJvMQ4wDAYDVQQRDAUwNjMwMDELMAkGA1UEBhMCTVgxGTAXBgNVBAgMEERpc3RyaXRvIEZlZGVyYWwxEjAQBgNVBAcMCUNveW9hY8OhbjEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTIwMAYJKoZIhvcNAQkCDCNSZXNwb25zYWJsZTogSMOpY3RvciBPcm5lbGFzIEFyY2lnYTAeFw0xMjA3MjcxNzAyMDBaFw0xNjA3MjcxNzAyMDBaMIHbMSkwJwYDVQQDEyBBQ0NFTSBTRVJWSUNJT1MgRU1QUkVTQVJJQUxFUyBTQzEpMCcGA1UEKRMgQUNDRU0gU0VSVklDSU9TIEVNUFJFU0FSSUFMRVMgU0MxKTAnBgNVBAoTIEFDQ0VNIFNFUlZJQ0lPUyBFTVBSRVNBUklBTEVTIFNDMSUwIwYDVQQtExxBQUEwMTAxMDFBQUEgLyBIRUdUNzYxMDAzNFMyMR4wHAYDVQQFExUgLyBIRUdUNzYxMDAzTURGUk5OMDkxETAPBgNVBAsTCFVuaWRhZCAxMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC2TTQSPONBOVxpXv9wLYo8jezBrb34i/tLx8jGdtyy27BcesOav2c1NS/Gdv10u9SkWtwdy34uRAVe7H0a3VMRLHAkvp2qMCHaZc4T8k47Jtb9wrOEh/XFS8LgT4y5OQYo6civfXXdlvxWU/gdM/e6I2lg6FGorP8H4GPAJ/qCNwIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQUFAAOCAQEATxMecTpMbdhSHo6KVUg4QVF4Op2IBhiMaOrtrXBdJgzGotUFcJgdBCMjtTZXSlq1S4DG1jr8p4NzQlzxsdTxaB8nSKJ4KEMgIT7E62xRUj15jI49qFz7f2uMttZLNThipunsN/NF1XtvESMTDwQFvas/Ugig6qwEfSZc0MDxMpKLEkEePmQwtZD+zXFSMVa6hmOu4M+FzGiRXbj4YJXn9Myjd8xbL/c+9UIcrYoZskxDvMxc6/6M3rNNDY3OFhBK+V/sPMzWWGt8S1yjmtPfXgFs1t65AZ2hcTwTAuHrKwDatJ1ZPfa482ZBROAAX1waz7WwXp0gso7sDCm2/yUVww==" sello="hiKzVLXyMPzi6OIr2IQRMH0K/YJqZWQZDO1jAaoDDo6IULX9It7VKutuo/J7vhdh0Jbm/SzhE1wqk75nTEet8QXfKJXiSGd4v9znGOt5X5gupojVuNY+kaX0wHaRXXdUqPfPgV/nZAZF8GPu5YWnEDEw1p7ba+YGRN7FZ7EVUso=" subTotal="7200.00" total="7934.40" descuento="360.00" motivoDescuento="Descuento en el primer pedido por internet." tipoDeComprobante="ingreso" LugarExpedicion="MEXICO DISTRITO FEDERAL">
  <cfdi:Emisor rfc="AAA010101AAA" nombre="Emisor de prueba">
	<cfdi:DomicilioFiscal calle="Tamaulipas" noExterior="125" noInterior="1" colonia="Roma" localidad="México" referencia="Entre León y Manzanillo" municipio="Cuauhtémoc" estado="Distrito Federal" pais="México" codigoPostal="35143"/>
	<cfdi:ExpedidoEn calle="Acapulco" noExterior="651" noInterior="1" colonia="Roma" localidad="México" municipio="Cuauhtémoc" estado="Distrito Federal" pais="México" codigoPostal="35135"/>
	<cfdi:RegimenFiscal Regimen="REGIMEN GENERAL DE LEY DE LAS PERSONAS MORALES"/>
  </cfdi:Emisor>
  <cfdi:Receptor rfc="XAXX010101000" nombre="MOSTRADOR">
	<cfdi:Domicilio calle="Heriberto Frias" noExterior="513" colonia="Narvarte" localidad="MEXICO" referencia="Entre Morena y Esperanza" municipio="Benito Juárez" estado="DISTRITO FEDERAL" pais="MEXICO" codigoPostal="03600"/>
  </cfdi:Receptor>
  <cfdi:Conceptos>
	<cfdi:Concepto descripcion="Computadora armada" cantidad="1.00" unidad="Pieza" valorUnitario="3000.00" importe="3000.00"/>
	<cfdi:Concepto descripcion="Caja de DVD" cantidad="10.00" unidad="Pieza" valorUnitario="120.00" importe="1200.00"/>
	<cfdi:Concepto descripcion="Monitor de 19 marca AOC" cantidad="1.00" unidad="Pieza" valorUnitario="3000.00" importe="3000.00"/>
  </cfdi:Conceptos>
  <cfdi:Impuestos totalImpuestosTrasladados="1094.40">
	<cfdi:Traslados>
	  <cfdi:Traslado impuesto="IVA" tasa="16.00" importe="1094.40"/>
	</cfdi:Traslados>
  </cfdi:Impuestos>
</cfdi:Comprobante>
XML;
            return $cfdi;
        }

        /**
         *
         * @param unknown $string
         * @return mixed
         */
        public function stripAccents($string)
        {
            $string = trim($string);
            
            $string = str_replace(array(
                'á',
                'à',
                'ä',
                'â',
                'ª',
                'Á',
                'À',
                'Â',
                'Ä'
            ), array(
                'a',
                'a',
                'a',
                'a',
                'a',
                'A',
                'A',
                'A',
                'A'
            ), $string);
            
            $string = str_replace(array(
                'é',
                'è',
                'ë',
                'ê',
                'É',
                'È',
                'Ê',
                'Ë'
            ), array(
                'e',
                'e',
                'e',
                'e',
                'E',
                'E',
                'E',
                'E'
            ), $string);
            
            $string = str_replace(array(
                'í',
                'ì',
                'ï',
                'î',
                'Í',
                'Ì',
                'Ï',
                'Î'
            ), array(
                'i',
                'i',
                'i',
                'i',
                'I',
                'I',
                'I',
                'I'
            ), $string);
            
            $string = str_replace(array(
                'ó',
                'ò',
                'ö',
                'ô',
                'Ó',
                'Ò',
                'Ö',
                'Ô'
            ), array(
                'o',
                'o',
                'o',
                'o',
                'O',
                'O',
                'O',
                'O'
            ), $string);
            
            $string = str_replace(array(
                'ú',
                'ù',
                'ü',
                'û',
                'Ú',
                'Ù',
                'Û',
                'Ü'
            ), array(
                'u',
                'u',
                'u',
                'u',
                'U',
                'U',
                'U',
                'U'
            ), $string);
            
            $string = str_replace(array(
                'ñ',
                'Ñ',
                'ç',
                'Ç',
                '&'
            ), array(
                'n',
                'N',
                'c',
                'C',
                'y'
            ), $string);
            
            // Esta parte se encarga de eliminar cualquier caracter extraño
            $string = str_replace(array(
                "<br>",
                "<sup>FM</sup>",
                "\\",
                "¨",
                "º",
                "-",
                "~",
                "#",
                "@",
                "|",
                "!",
                "\"",
                "·",
                "$",
                "%",
                "&",
                "/",
                "(",
                ")",
                "?",
                "'",
                "¡",
                "¿",
                "[",
                "^",
                "`",
                "]",
                "+",
                "}",
                "{",
                "¨",
                "´",
                ">",
                "< ",
                ";",
                ",",
                ":",
                ".",
                "♥",
                "–"
            ), '', $string);
            
            return $string;
        }

        /**
         *
         * @param unknown $str
         * @param unknown $what
         * @param string $with
         * @return string
         */
        public function trim_all($str, $what = NULL, $with = ' ')
        {
            if ($what === NULL) {
                // Character Decimal Use
                // "\0" 0 Null Character
                // "\t" 9 Tab
                // "\n" 10 New line
                // "\x0B" 11 Vertical Tab
                // "\r" 13 New Line in Mac
                // " " 32 Space
                
                $what = "\\x00-\\x20"; // all white-spaces and control chars
            }
            
            return trim(preg_replace("/[" . $what . "]+/", $with, $str), $what);
        }

        /**
         * [solicitud2 Método usado para timbrar la factura o para recuperar los datos de timbrado]
         *
         * @param [String] $metodo_pago
         *            [El metodo de pago con el que se realizó la compra]
         * @param [Array] $cupon
         *            [El cupón usado en la compra, si lo hay]
         * @param [Array] $list_products
         *            [Listado de productos comprados]
         * @param [Array] $invoice_address
         *            [Listado de la dirección de facturación]
         * @param [Objeto] $order_tot
         *            [Listado detallado de la orden]
         * @return [Array] [Retorna la respuesta del timbrado de la factura]
         */
        public function solicitud2($metodo_pago, $cupon, $list_products, $invoice_address, $order_tot, $array_ivas, $val_total_de_iva, $estado_orden = 0, $obligar_timbrado = 0, $hacer_debug = 0)
        {
            $orden_validar = Configuration::get('FACTURAXION_ORDER_VALIDATE');
            
            $invoice_date_t = "";
            
            $order_tot->total_discounts = ($order_tot->total_products_wt + $order_tot->total_shipping_tax_incl + $order_tot->total_wrapping_tax_incl - $order_tot->total_paid_tax_incl);
            $valor_timbrado = $this->RegistroTimbrado($order_tot->id, 1);
            
            if (isset($order_tot->invoice_date) && strlen($order_tot->invoice_date) >= 10) {
                $invoice_date_t = substr($order_tot->invoice_date, 0, 10) . DIRECTORY_SEPARATOR;
            }
            
            $xml_previamente_generado = $this->dir_server . DIRECTORY_SEPARATOR . "xml_timbrado" . DIRECTORY_SEPARATOR . "response" . DIRECTORY_SEPARATOR . $invoice_date_t . $this->test_char . $order_tot->id . "_response.xml";
            $orderStateSig = (int) Configuration::get(Configuration::get('SIGNATURE_CFDI'));
            if (((! is_array($valor_timbrado) && $valor_timbrado == 0) && ! file_exists($xml_previamente_generado) && $estado_orden == $orderStateSig) || $obligar_timbrado == 1 || $hacer_debug == 1) {
                
                if ($orden_validar == $order_tot->id) {
                    echo "<br> or: " . $order_tot->id;
                    echo "<br> ot: " . $obligar_timbrado;
                    echo "<br> order: <pre>";
                    print_r($order_tot);
                    echo "</pre>";
                    
                    echo "<hr><div style='font-size:13px'> list_products: <pre style='font-size:13px'><font size='3'>"; // current_state
                    print_r($list_products);
                    echo " </pre>";
                    
                    echo "<hr><div style='font-size:13px'> invoice_address: <pre style='font-size:13px'><font size='3'>"; // current_state
                    print_r($invoice_address);
                    echo "</hr> </pre>";
                    
                    echo "<hr><div style='font-size:13px'> cupon: <pre style='font-size:13px'><font size='3'>"; // current_state
                    print_r($cupon);
                    echo "</hr> </pre>";
                }
                
                $cant_prods = 0;
                $order_tot->total_products = 0;
                $total_productos = 0;
                $hjys = '';
                $totalDescuentoConceptos = 0;
                $totalImportes = 0;
                $infoConceptos = '';
                
                foreach ($list_products as $key_prod => $value) {
                    if ($list_products[$key_prod]['unit_price_tax_excl'] != 0) {
                        $total_productos += 1;
                    }
                }
                
                foreach ($list_products as $key_prod => $value) {
                    if ($list_products[$key_prod]['unit_price_tax_excl'] != 0) {
                        $base = number_format($list_products[$key_prod]['total_price_tax_excl'], 2, '.', '');
                        // $hjys .= "<br>".$list_products[$key_prod]['product_name'];
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveProdServ'] = $list_products[$key_prod]['ClaveProdServ'];
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['NoIdentificacion'] = $this->trim_all($list_products[$key_prod]['product_reference']);
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Cantidad'] = number_format($list_products[$key_prod]['product_quantity'], 6, '.', '');
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveUnidad'] = 'H87';
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Unidad'] = 'Pieza';
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Descripcion'] = $this->trim_all(trim($this->stripAccents($list_products[$key_prod]['product_name'])));
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ValorUnitario'] = number_format($list_products[$key_prod]['unit_price_tax_excl'], 2, '.', '');
                        $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Importe'] = $base;
                        if ($list_products[$key_prod]['tax_rate'] != '0.000') {
                            $importe = number_format(($list_products[$key_prod]['total_price_tax_excl'] * $list_products[$key_prod]['tax_rate']) / 100, 2, '.', '');
                            $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Base'] = $base;
                            $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Impuesto'] = "002";
                            $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TipoFactor'] = "Tasa";
                            $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TasaOCuota'] = number_format($list_products[$key_prod]['tax_rate'] / 100, 6, '.', '');
                            $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Importe'] = $importe;
                            $totalImportes += $importe;
                        }
                        $order_tot->total_products += $base;
                        $cant_prods ++;
                    }
                }
                
                /**
                 * **************** ENVIO DE PRODUCTOS...
                 * DOMICILIO *****************
                 */
                $infoFlete = "";
                if ($order_tot->total_shipping != '0.00' || $order_tot->total_shipping_tax_incl != '0.00') {
                    $val_no_iva_envio = Tools::ps_round($order_tot->total_shipping / 1.16, 3);
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveProdServ'] = '01010101';
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['NoIdentificacion'] = '1';
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Cantidad'] = number_format(1, 6, '.', '');
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveUnidad'] = 'H87';
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Unidad'] = 'Pieza';
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Descripcion'] = "FLETE ENVIO";
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ValorUnitario'] = number_format($val_no_iva_envio, 2, '.', '');
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Importe'] = Tools::ps_round($val_no_iva_envio, 2);
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Base'] = number_format($val_no_iva_envio, 2, '.', '');
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Impuesto'] = "002";
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TipoFactor'] = "Tasa";
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TasaOCuota'] = "0.160000";
                    $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Importe'] = number_format(($val_no_iva_envio * 16) / 100, 2, '.', '');
                    $totalImportes += number_format(($val_no_iva_envio * 16) / 100, 2, '.', '');
                    $order_tot->total_products += Tools::ps_round($val_no_iva_envio, 2);
                    $fleteTemporal = $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods];
                    $infoFlete = "01010101|1|" . $fleteTemporal['@attributes']['Cantidad'] . "|H87|Pieza|FLETE ENVIO|" . $fleteTemporal['@attributes']['ValorUnitario'] . "|" . $fleteTemporal['@attributes']['Importe'] . "|" . $fleteTemporal['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Base'] . "|002|Tasa|0.160000|" . $fleteTemporal['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Importe'] . "|";
                }
                
                /**
                 * si no existe impuesto, crea una regla con valor 0 cero
                 */
                
                if ($order_tot->id == $orden_validar) {
                    foreach ($array_ivas as $key => $value) {
                        echo "<br> 1. tax: " . $key . "  - - - Value : " . $value;
                    }
                }
                // $val_total_de_iva = 0;
                ksort($array_ivas);
                
                if ($order_tot->id == $orden_validar) {
                    foreach ($array_ivas as $key => $value) {
                        echo "<br> 2. tax: " . $key . "  - - - Value : " . $value;
                    }
                }
                
                // foreach ($array_ivas as $key => $value) {
                $order_tot->total_paid = $order_tot->total_products + $totalImportes;
                // }
                
                $arr_xml_cargar['@attributes']['xmlns_cfdi'] = 'http://www.sat.gob.mx/cfd/3';
                $arr_xml_cargar['@attributes']['xmlns_xsi'] = 'http://www.w3.org/2001/XMLSchema-instance';
                $arr_xml_cargar['@attributes']['xsi_schemaLocation'] = 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd';
                $arr_xml_cargar['@attributes']['Version'] = '3.3';
                $arr_xml_cargar['@attributes']['Fecha'] = date("Y-m-d") . "T" . date("H:i:s", strtotime('-10 minute', strtotime(date("H:i:s")))); // '2015-01-14T15:57:16';
                $arr_xml_cargar['@attributes']['TipoDeComprobante'] = 'I'; // ingreso
                $metodo_pago_numero = explode('(', explode(')', explode('-', $metodo_pago)[0])[0]);
                $arr_xml_cargar['@attributes']['FormaPago'] = (isset($metodo_pago_numero[1])) ? $metodo_pago_numero[1] : $metodo_pago_numero[0];
                // $arr_xml_cargar['@attributes']['serie'] = 'C';
                // $arr_xml_cargar['@attributes']['folio'] = '2000';
                $arr_xml_cargar['@attributes']['MetodoPago'] = 'PUE'; // PAGO EN UNA SOLA EXHIBICION
                                                                      // $arr_xml_cargar['@attributes']['condicionesDePago'] = 'Parcialidades';
                $arr_xml_cargar['@attributes']['Sello'] = '';
                $arr_xml_cargar['@attributes']['NoCertificado'] = $this->numero_certificado;
                $arr_xml_cargar['@attributes']['Certificado'] = str_replace(array(
                    '\n',
                    '\r'
                ), '', base64_encode(file_get_contents($this->dir_server . DIRECTORY_SEPARATOR . $this->archivo_cer)));
                
                if ($order_tot->total_discounts != null || $order_tot->total_discounts != 0) {
                    $arr_xml_cargar['@attributes']['Descuento'] = number_format($order_tot->total_discounts, 2, '.', '');
                    $arr_xml_cargar['@attributes']['SubTotal'] = $order_tot->total_products;
                    $arr_xml_cargar['@attributes']['Total'] = number_format($order_tot->total_paid - $order_tot->total_discounts, 2, '.', '');
                } else {
                    $arr_xml_cargar['@attributes']['Descuento'] = '0.00';
                    $arr_xml_cargar['@attributes']['SubTotal'] = $order_tot->total_products;
                    $arr_xml_cargar['@attributes']['Total'] = number_format($order_tot->total_paid, 2, '.', '');
                }
                
                $arr_xml_cargar['@attributes']['Moneda'] = 'MXN'; // codigo postal
                $arr_xml_cargar['@attributes']['LugarExpedicion'] = '11870'; // codigo postal
                                                                             
                // se insertan los descuentos según el porcentaje del valor de cada producto por aparte
                $cant_prods = 0;
                foreach ($list_products as $key_prod => $value) {
                    if ($list_products[$key_prod]['unit_price_tax_excl'] != 0) {
                        $base = number_format($list_products[$key_prod]['total_price_tax_excl'], 2, '.', '');
                        $conceptoTemporal = $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods];
                        $infoConceptos .= $conceptoTemporal['@attributes']['ClaveProdServ'] . "|" . $conceptoTemporal['@attributes']['NoIdentificacion'] . "|" . $conceptoTemporal['@attributes']['Cantidad'] . "|H87|Pieza|" . trim($conceptoTemporal['@attributes']['Descripcion']) . "|" . $conceptoTemporal['@attributes']['ValorUnitario'] . "|" . $conceptoTemporal['@attributes']['Importe'] . "|";
                        if ($order_tot->total_discounts != null || $order_tot->total_discounts != 0) {
                            $descuentoConcepto = number_format($order_tot->total_discounts * ($base / $arr_xml_cargar['@attributes']['SubTotal']), 2, '.', '');
                            $totalDescuentoConceptos += $descuentoConcepto;
                            if ($cant_prods == ($total_productos - 1)) {
                                $descuentoConcepto = number_format($descuentoConcepto, 2, '.', '') - ($totalDescuentoConceptos - $order_tot->total_discounts);
                            }
                            $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Descuento'] = number_format($descuentoConcepto, 2, '.', '');
                            $infoConceptos .= number_format($descuentoConcepto, 2, '.', '') . "|";
                        }
                        if ($list_products[$key_prod]['tax_rate'] != '0.000') {
                            $infoConceptos .= $conceptoTemporal['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Base'] . "|002|Tasa|" . $conceptoTemporal['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TasaOCuota'] . "|" . $conceptoTemporal['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Importe'] . "|";
                        }
                        $cant_prods ++;
                    }
                }
                
                $arr_xml_cargar['ar6to67be_Emisor']['@attributes']['Rfc'] = $this->RFCEmisor;
                $arr_xml_cargar['ar6to67be_Emisor']['@attributes']['Nombre'] = $this->RFCEmisor_nombre;
                $arr_xml_cargar['ar6to67be_Emisor']['@attributes']['RegimenFiscal'] = '601';
                
                /**
                 * ****** ASIGNAR RFC DE COMPRA CON FACTURA is_rfc *******
                 */
                
                $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['UsoCFDI'] = 'G01';
                $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Rfc'] = 'XAXX010101000';
                $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = 'MOSTRADOR';
                
                if ($this->stripAccents($invoice_address->firstname) != '' && $this->stripAccents($invoice_address->firstname) != null) {
                    
                    $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = $this->trim_all($this->stripAccents($invoice_address->firstname)); // 'Nombre del Receptor';
                }
                
                if ($this->stripAccents($invoice_address->lastname) != '' && $this->stripAccents($invoice_address->lastname) != null) {
                    
                    if ($arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] != '') {
                        
                        $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] .= " " . $this->trim_all($this->stripAccents($invoice_address->lastname)); // 'Nombre del Receptor';
                    } else {
                        
                        $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = $this->trim_all($this->stripAccents($invoice_address->lastname)); // 'Nombre del Receptor';
                    }
                }
                
                /**
                 * *************************************************** CAMBIO PARA RFC *****************************************************************
                 */
                
                if (isset($invoice_address->id_customer) && $invoice_address->id_customer != '' && $invoice_address->id_customer != null) {
                    
                    $query = new DbQuery();
                    $query->select(' is_rfc, dni, alias, address1, cpp.nombre AS colonia_name, address2, postcode, firstname, lastname ');
                    $query->from('address', 'a');
                    $query->leftJoin('cod_postal', 'cpp', 'cpp.id_codigo_postal = a.id_colonia');
                    $query->where(' a.id_customer = ' . $invoice_address->id_customer . ' AND a.is_rfc = 1');
                    $query->limit('1');
                    
                    if ($dir_factura = Db::getInstance()->executeS($query)) {
                        
                        /**
                         * ****** ASIGNAR RFC DE COMPRA CON FACTURA is_rfc *******
                         */
                        
                        if (isset($dir_factura[0]['is_rfc']) && $dir_factura[0]['is_rfc'] == 1) {
                            
                            $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Rfc'] = strtoupper($dir_factura[0]['dni']);
                            
                            $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = 'MOSTRADOR';
                            
                            if ($this->stripAccents($dir_factura[0]['alias']) != '' && $this->stripAccents($dir_factura[0]['alias']) != null) {
                                $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = $this->trim_all($this->stripAccents($dir_factura[0]['alias']));
                            }
                        }
                    }
                }
                
                /**
                 * ****** ASIGNAR RFC DE COMPRA CON FACTURA *******
                 */
                
                $arr_xml_cargar = array_merge($arr_xml_cargar, $arr_xml_cargar_p); // UNIMOS ARRAY DE PRODUCTOS Y EL INICIAL DEL XML
                $arr_xml_cargar['ar6to67be_Impuestos']['@attributes']['TotalImpuestosRetenidos'] = '0.00';
                $arr_xml_cargar['ar6to67be_Impuestos']['@attributes']['TotalImpuestosTrasladados'] = $totalImportes;
                
                $cant_taxs = 0;
                $infoTaxes = "";
                
                foreach ($array_ivas as $key => $value) {
                    if ($value > 0) {
                        $arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['Impuesto'] = '002';
                        $arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['TipoFactor'] = 'Tasa';
                        $arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['TasaOCuota'] = number_format($key / 100, 6, '.', '');
                        $arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['Importe'] = $totalImportes;
                        $infoTaxes = "002|Tasa|" . $arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['TasaOCuota'] . "|" . $totalImportes . "|";
                        $cant_taxs ++;
                    }
                }
                
                $this->cadena_original = "||3.3|" . $arr_xml_cargar['@attributes']['Fecha'] . "|" . $arr_xml_cargar['@attributes']['FormaPago'] . "|" . $this->numero_certificado . "|" . $arr_xml_cargar['@attributes']['SubTotal'] . "|" . $arr_xml_cargar['@attributes']['Descuento'] . "|MXN|" . $arr_xml_cargar['@attributes']['Total'] . "|I|PUE|11870|" . $this->RFCEmisor . "|" . $this->RFCEmisor_nombre . "|601|" . $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Rfc'] . "|" . $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] . "|G01|" . $infoConceptos . $infoFlete . "0.00|" . $infoTaxes . $totalImportes . "||";
                
                $serie = 'C';
                $folio = '2000';
                
                $this->array_xml_a_timbrar = $arr_xml_cargar;
                
                /**
                 * ********************* INICIO GENERAR EL XML DEL ARRAY ******************************
                 */
                
                $xml1 = Array2XML::createXML('ar6to67be_Comprobante', $arr_xml_cargar);
                
                $buscar = array(
                    "&amp;aacute;",
                    "&amp;eacute;",
                    "&amp;iacute;",
                    "&amp;oacute;",
                    "&amp;uacute;",
                    "&amp;Aacute;",
                    "&amp;Eacute;",
                    "&amp;Iacute;",
                    "&amp;Oacute;",
                    "&amp;Uacute;",
                    "ar6to67be_",
                    "xmlns_",
                    "xsi_",
                    "a23r4e3r4eee_"
                );
                
                $cambiar = array(
                    "&aacute;",
                    "&eacute;",
                    "&iacute;",
                    "&oacute;",
                    "&uacute;",
                    "&Aacute;",
                    "&Eacute;",
                    "&Iacute;",
                    "&Oacute;",
                    "&Uacute;",
                    "cfdi:",
                    "xmlns:",
                    "xsi:",
                    "tfd:"
                );
                
                $xml214_1 = html_entity_decode(str_replace($buscar, $cambiar, $xml1->saveXML()));
                
                $contenido_fichero_1 = $xml214_1;
                
                if ($order_tot->id == $orden_validar) {
                    echo "<br> antes Sellado emisor:<br><textarea style='height: 574px;  width: 1549px;' style='font-size:13px'>";
                    echo html_entity_decode(str_replace(array(
                        "ar6to67be_",
                        "xmlns_",
                        "xsi_"
                    ), array(
                        "cfdi:",
                        "xmlns:",
                        "xsi:"
                    ), $xml1->saveXML()));
                    echo "</textarea><br><hr>";
                    
                    $this->LlavesRegistroTimbrado($contenido_fichero_1, 1);
                }
                
                $this->LlavesRegistroTimbrado($contenido_fichero_1);
                
                $xml2 = Array2XML::createXML('ar6to67be_Comprobante', $this->array_xml_a_timbrar);
                
                if ($order_tot->id == $orden_validar) {
                    echo "<br> Sellado emisor:<br><textarea style='height: 574px;  width: 1549px;' style='font-size:13px'>";
                    echo html_entity_decode(str_replace(array(
                        "ar6to67be_",
                        "xmlns_",
                        "xsi_"
                    ), array(
                        "cfdi:",
                        "xmlns:",
                        "xsi:"
                    ), $xml2->saveXML()));
                    echo "</textarea><br><hr>";
                    
                    exit(" sin timbrar ");
                }
                /**
                 * ********************* FINAL GENERAR EL XML DEL ARRAY ******************************
                 */
                
                /**
                 * ***************** INICIO PARA IMPRIMIR EL ARREGLO EN UNA TABLA ****************************
                 */
                /*
                 * include("show_array.php");
                 * html_show_array($arr_xml_cargar);
                 */
                /**
                 * ***************** FIN PARA IMPRIMIR EL ARREGLO EN UNA TABLA ****************************
                 */
                
                /*
                 * $xml = simplexml_load_string($contenido_fichero);
                 * $this->simplexml_to_array($xml, $arr);
                 * var_dump($arr);
                 */
                // exit();
                
                $buscar = array(
                    "&amp;aacute;",
                    "&amp;eacute;",
                    "&amp;iacute;",
                    "&amp;oacute;",
                    "&amp;uacute;",
                    "&amp;Aacute;",
                    "&amp;Eacute;",
                    "&amp;Iacute;",
                    "&amp;Oacute;",
                    "&amp;Uacute;",
                    "ar6to67be_",
                    "xmlns_",
                    "xsi_",
                    "a23r4e3r4eee_"
                );
                
                $cambiar = array(
                    "&aacute;",
                    "&eacute;",
                    "&iacute;",
                    "&oacute;",
                    "&uacute;",
                    "&Aacute;",
                    "&Eacute;",
                    "&Iacute;",
                    "&Oacute;",
                    "&Uacute;",
                    "cfdi:",
                    "xmlns:",
                    "xsi:",
                    "tfd:"
                );
                
                $xml214 = html_entity_decode(str_replace($buscar, $cambiar, $xml2->saveXML()));
                
                $contenido_fichero = $xml214;
                
                // $usuario="0000023492";
                // $usuario="FME140730J95";
                $usuario = $this->usuario;
                $proveedor = $this->proveedor;
                $contrasenia = $this->contrasenia;
                $sucursal = $this->sucursal;
                
                if ($this->test_mode == true) {
                    
                    $soapClient = new SoapClient("https://wstimbradopruebas.facturaxion.com/WSTimbrado.svc?WSDL");
                } else {
                    $soapClient = new SoapClient("https://wstimbrado.facturaxion.com/WSTimbrado.svc?WSDL");
                }
                
                $metodo = "TimbrarParametros";
                
                $xml_params = array(
                    "Usuario" => $this->usuarioxml,
                    "Contrasenia" => $contrasenia,
                    "XMLPreCFDI" => $contenido_fichero,
                    "XMLOpcionales" => "",
                    "IdAddenda" => "",
                    "NodoAddenda" => "",
                    "IdLlaveUnico" => "",
                    "UUIDSucursal" => $sucursal,
                    "Mail" => "",
                    "MailCC" => "",
                    "MailCCO" => ""
                );
                // d($xml_params);
                
                // echo "<br><textarea cols=150 rows=20>".str_replace(array("&amp;lt;","&amp;gt;",'&amp;quot;',"&lt;","&gt;",'&quot;'),array("<",">",'"',"<",">",'"') ,$xml_params)."</textarea><br>";
                // exit;
                if (is_array($valor_timbrado) && $valor_timbrado != 0 && $obligar_timbrado == 0) {
                    $this->TimbradoNoOk($order_tot->id, "Timbrado previamente generado ", 1, 0, $contenido_fichero);
                    // echo "<br> SIIIIIIIIIIII existe registro en la bd ";
                    return ($valor_timbrado);
                }
                
                try {
                    
                    $result = $soapClient->$metodo($xml_params);
                    $resultado = $result->XMLCFDI;
                    usleep(800000);
                    if ($resultado) {
                        $responseXML1 = $result->XMLCFDI;
                        $responsePath = $this->dir_server . DIRECTORY_SEPARATOR . "xml_timbrado" . DIRECTORY_SEPARATOR . "response" . DIRECTORY_SEPARATOR . $invoice_date_t;
                        if (! is_dir($responsePath)) {
                            mkdir($responsePath, 0755, TRUE);
                        }
                        
                        $responseFilePath = $responsePath . $this->test_char . $order_tot->id . "_response.xml";
                        
                        if (! file_exists($responseFilePath)) {
                            
                            $fp1 = fopen($responseFilePath, "x");
                            fwrite($fp1, $responseXML1);
                            fclose($fp1);
                        }
                        $p = xml_parser_create();
                        $xml = xml_parse_into_struct($p, $responseXML1, $vals, $index);
                        xml_parser_free($p);
                        $arr = $vals[$index['TFD:TIMBREFISCALDIGITAL'][0]];
                        $arr_xml_cargar['ar6to67be_Complemento']['a23r4e3r4eee_TimbreFiscalDigital']['@attributes']['xmlns_tfd'] = "http://www.sat.gob.mx/TimbreFiscalDigital";
                        $arr_xml_cargar['ar6to67be_Complemento']['a23r4e3r4eee_TimbreFiscalDigital']['@attributes']['xsi_schemaLocation'] = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd";
                        
                        foreach ($arr["attributes"] as $key => $value) {
                            
                            $arr_xml_cargar['ar6to67be_Complemento']['a23r4e3r4eee_TimbreFiscalDigital']['@attributes'][$key] = $value;
                        }
                        
                        /**
                         * ********************** INICIO GENERACION XML FULL *********************************
                         */
                        
                        $xml2 = Array2XML::createXML('ar6to67be_Comprobante', $arr_xml_cargar);
                        
                        $this->TimbradoOk($order_tot->id, $arr["attributes"], $result->RutaPDF);
                        
                        return ($arr["attributes"]);
                    } else {
                        
                        $responseXML = $result->MensajeValidacion;
                        $this->TimbradoNoOk($order_tot->id, $responseXML, 1, 0, $contenido_fichero);
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    echo $message;
                }
            } else {
                
                if (is_array($valor_timbrado) && $valor_timbrado != 0) {
                    
                    return ($valor_timbrado);
                }
                
                if (file_exists($xml_previamente_generado)) {
                    
                    $fichero_texto = fopen($xml_previamente_generado, "r");
                    $contenido_fichero = fread($fichero_texto, filesize($xml_previamente_generado));
                    
                    $xml = simplexml_load_string($contenido_fichero);
                    
                    $this->simplexml_to_array($xml, $arr);
                    $arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"]["RutaXml"] = $arr['Timbrado']['Resultado']["Informacion"]["Documento"]["@attributes"]["RutaXml"];
                    
                    return ($arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"]);
                }
            }
        }

        /**
         * [cancelacion Método usado para cancelar el timbrado de la factura]
         *
         * @param [Object] $order_tot
         *            [Listado de todas las características de la orden]
         * @return [type] [description]
         */
        public function cancelacion($order_tot_id, $order_invoice_date)
        {
            $usuario = $this->usuario;
            $proveedor = $this->proveedor;
            $sucursal = $this->sucursal;
            $RFCEmisor = $this->RFCEmisor;
            
            if (isset($order_invoice_date) && strlen($order_invoice_date) >= 10) {
                $invoice_date_t = substr($order_invoice_date, 0, 10) . "/";
            } else {
                $invoice_date_t = "";
            }
            
            $xml_previamente_timbrado = $this->dir_server . DIRECTORY_SEPARATOR . "xml_timbrado" . DIRECTORY_SEPARATOR . "response" . DIRECTORY_SEPARATOR . $invoice_date_t . $this->test_char . $order_tot_id . "_response.xml";
            
            $valor_timbrado = $this->RegistroTimbrado($order_tot_id, 1);
            
            $xml_previamente_cancelado = $this->dir_server . DIRECTORY_SEPARATOR . "xml_timbrado" . DIRECTORY_SEPARATOR . "cancelacion" . DIRECTORY_SEPARATOR . $invoice_date_t . $this->test_char . $order_tot_id . "_response.xml";
            
            $valor_cancelado = $this->RegistroTimbrado($order_tot_id, 1, 1);
            
            if ((is_array($valor_timbrado) || file_exists($xml_previamente_timbrado)) && $valor_cancelado == 0 && ! file_exists($xml_previamente_cancelado)) {
                
                /**
                 * ************** envio $this->order->total_shipping_tax_incl
                 */
                // ///////
                
                $arr_xml_cargar['@attributes']['Version'] = '1.0';
                $arr_xml_cargar['@attributes']['CodigoUsuarioProveedor'] = $proveedor; // 'N#@Mo!)#oh&gt;)BYOdX=q_ZUCsLxqpv?';
                $arr_xml_cargar['@attributes']['CodigoUsuario'] = $usuario; // '1763AAB0593430490B3B3EE5457A9A2580F9D7DE';
                $arr_xml_cargar['@attributes']['RFCEmisor'] = $RFCEmisor;
                
                $databd = 0; // datos existentes en la base de datos
                if (is_array($valor_timbrado)) {
                    
                    $arr_xml_cargar['Folios']['UUID'] = $valor_timbrado['uuid'];
                    $databd = 1;
                } elseif (file_exists($xml_previamente_timbrado)) {
                    
                    $fichero_texto = fopen($xml_previamente_timbrado, "r");
                    $contenido_fichero = fread($fichero_texto, filesize($xml_previamente_timbrado));
                    $xml = simplexml_load_string($contenido_fichero);
                    $this->simplexml_to_array($xml, $arr);
                    $arrkeylower = array_change_key_case($arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"], CASE_LOWER);
                    $arr_xml_cargar['Folios']['UUID'] = $arrkeylower['uuid']; // date("Y-m-d")."T".date("H:i:s",strtotime("-10 minute"));//'2015-01-14T15:57:16';
                }
                
                $serie = 'C';
                $folio = '2000';
                
                /**
                 * ********************* INICIO GENERAR EL XML DEL ARRAY ******************************
                 */
                
                $xml2 = Array2XML::createXML('Parametros', $arr_xml_cargar);
                
                /**
                 * ********************* FINAL GENERAR EL XML DEL ARRAY ******************************
                 */
                
                $buscar = array(
                    "&amp;aacute;",
                    "&amp;eacute;",
                    "&amp;iacute;",
                    "&amp;oacute;",
                    "&amp;uacute;",
                    "&amp;Aacute;",
                    "&amp;Eacute;",
                    "&amp;Iacute;",
                    "&amp;Oacute;",
                    "&amp;Uacute;",
                    "ar6to67be_",
                    "xmlns_",
                    "xsi_",
                    "a23r4e3r4eee_"
                );
                
                $cambiar = array(
                    "&aacute;",
                    "&eacute;",
                    "&iacute;",
                    "&oacute;",
                    "&uacute;",
                    "&Aacute;",
                    "&Eacute;",
                    "&Iacute;",
                    "&Oacute;",
                    "&Uacute;",
                    "cfdi:",
                    "xmlns:",
                    "xsi:",
                    "tfd:"
                );
                
                $xml214 = html_entity_decode(str_replace($buscar, $cambiar, $xml2->saveXML()));
                
                $contenido_fichero = str_replace("<", "&lt;", $xml214);
                $contenido_fichero = str_replace(">", "&gt;", $contenido_fichero);
                $contenido_fichero = str_replace('"', '&quot;', $contenido_fichero);
                
                $soapClient = new SoapClient("https://timbre02.facturaxion.net/CFDI.asmx?WSDL");
                
                if ($this->test_mode == true) {
                    
                    $metodo = "ReportarCancelacionPrueba";
                    $TextoXml = '"' . $contenido_fichero . '"&lt;/Parametros&gt;';
                    
                    $xml_params = '<ReportarCancelacionPrueba xmlns="http://www.facturaxion.com/">
					<parametros>' . $contenido_fichero . '</parametros>
					</ReportarCancelacionPrueba>';
                } else {
                    
                    $metodo = "ReportarCancelacion";
                    $TextoXml = '"' . $contenido_fichero . '"&lt;/Parametros&gt;';
                    
                    $xml_params = '<ReportarCancelacion xmlns="http://www.facturaxion.com/">
					<parametros>' . $contenido_fichero . '</parametros>
					</ReportarCancelacion>';
                }
                
                try {
                    
                    $soapVar = new SoapVar($xml_params, XSD_ANYXML, null, null, null);
                } catch (Exception $e) {
                    
                    $message = $e->getMessage();
                    $fp_cfdi = fopen($this->dir_server . DIRECTORY_SEPARATOR .'..'. DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'facturaxion' . DIRECTORY_SEPARATOR . "cancelacion_" . date("Y-m-d") . "T" . date("H:i:s", strtotime('-10 minute', strtotime(date("H:i:s")))) . $this->test_char . "_cfdi.xml", "a+"); // "_".date("Y-m-d H:i:s").
                    fwrite($fp_cfdi, $message);
                    fclose($fp_cfdi);
                }
                
                try {
                    
                    $result = $soapClient->$metodo(new SoapParam($soapVar, $metodo));
                    if ($this->test_mode == true) {
                        $resultado = $result->ReportarCancelacionPruebaResult;
                    } else {
                        $resultado = $result->ReportarCancelacionResult;
                    }
                    
                    if ($resultado == true) {
                        
                        $responseXML1 = $result->resultado;
                        $cancelacionPath = $this->dir_server . DIRECTORY_SEPARATOR . "xml_timbrado" . DIRECTORY_SEPARATOR . "cancelacion" . DIRECTORY_SEPARATOR . $invoice_date_t;
                        if (! is_dir($cancelacionPath)) {
                            mkdir($cancelacionPath, 0755, TRUE);
                        }
                        
                        $fp1 = fopen($this->dir_server . DIRECTORY_SEPARATOR . "xml_timbrado" . DIRECTORY_SEPARATOR . "cancelacion" . DIRECTORY_SEPARATOR . $invoice_date_t . $this->test_char . $order_tot_id . "_response.xml", "x"); // "_".date("Y-m-d H:i:s").
                        fwrite($fp1, $responseXML1);
                        fclose($fp1);
                        
                        if ($databd == 0) {
                            $this->TimbradoOk($order_tot_id, $arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"], $arr['Timbrado']['Resultado']["Informacion"]["Documento"]["@attributes"]);
                            $valor_timbrado = $this->RegistroTimbrado($order_tot_id, 1);
                            $this->CanceladoOk($valor_timbrado['id_timbrado']);
                        } else {
                            
                            $this->CanceladoOk($valor_timbrado['id_timbrado']);
                        }
                        
                        return true;
                    } else {
                        
                        $responseXML = $result->resultado;
                        $pieces = explode("<Errores><", $responseXML);
                        $sedunda = $pieces[1];
                        $sedunda = str_replace("/></Errores></Informacion></Resultado></Timbrado>", "&lt;", $sedunda);
                        $sedunda = str_replace("</Errores></Resultado>", "", $sedunda);
                        
                        $this->CanceladoNoOk($order_tot_id, $sedunda, 0, 1);
                    }
                } catch (Exception $e) {
                    $message = $e->getMessage();
                    echo $message;
                }
            } else {
                
                if (is_array($valor_timbrado) && $valor_timbrado != 0) {
                    
                    return ($valor_timbrado);
                }
                
                if (file_exists($xml_previamente_cancelado)) {
                    
                    $fichero_texto = fopen($xml_previamente_cancelado, "r");
                    $contenido_fichero = fread($fichero_texto, filesize($xml_previamente_cancelado));
                    $xml = simplexml_load_string($contenido_fichero);
                    $json = json_encode($xml);
                    $this->simplexml_to_array($xml, $arr);
                    
                    return ($arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"]);
                }
            }
        }

        /**
         *
         * @param unknown $id_order
         * @param array $arr_timbre
         * @param unknown $arr_documento
         * @return boolean
         */
        public function TimbradoOk($id_order, array $arr_timbre, $arr_documento)
        {
            $query = new DbQuery();
            $query->select('count(t.id_order) AS cant');
            $query->from('timbrado', 't');
            $and = '';
            if ($this->test_mode == true) {
                $and = ' AND t.prueba = 1 ';
            } else {
                $and = ' AND t.prueba = 0 ';
            }
            
            $query->where('t.id_order = ' . $id_order . $and);
            
            $timbres = Db::getInstance()->executeS($query);
            
            if ($timbres[0]['cant'] == 0) {
                $is_correct = true;
                
                if ($this->test_mode == true) {
                    
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'timbrado` (`id_order`, `version`, `uuid`, `fechatimbrado`, `sellocfd`, `nocertificadosat`, `sellosat`, `timbrado`, `cancelado`, `rutaxml`, `fecha`, `prueba`)
						VALUES ("' . $id_order . '", "' . $arr_timbre['VERSION'] . '", "' . $arr_timbre['UUID'] . '", "' . $arr_timbre['FECHATIMBRADO'] . '", "' . $arr_timbre['SELLOCFD'] . '", "' . $arr_timbre['NOCERTIFICADOSAT'] . '", "' . $arr_timbre['SELLOSAT'] . '", 1, 0, "' . $arr_documento . '", NOW(), 1 )';
                } else {
                    
                    $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'timbrado` (`id_order`, `version`, `uuid`, `fechatimbrado`, `sellocfd`, `nocertificadosat`, `sellosat`, `timbrado`, `cancelado`, `rutaxml`, `fecha`)
						VALUES ("' . $id_order . '", "' . $arr_timbre['VERSION'] . '", "' . $arr_timbre['UUID'] . '", "' . $arr_timbre['FECHATIMBRADO'] . '", "' . $arr_timbre['SELLOCFD'] . '", "' . $arr_timbre['NOCERTIFICADOSAT'] . '", "' . $arr_timbre['SELLOSAT'] . '", 1, 0, "' . $arr_documento . '", NOW() )';
                }
                
                $is_correct = Db::getInstance()->execute($sql);
            } else {
                $is_correct = false;
                echo "<br> no se registro.";
            }
            return $is_correct;
        }

        /**
         *
         * @param unknown $id_order
         * @param unknown $mensaje
         * @param unknown $timbrado
         * @param unknown $cancelado
         * @param string $xml
         * @return boolean
         */
        public function TimbradoNoOk($id_order, $mensaje, $timbrado, $cancelado, $xml = '')
        {
            $is_correct = true;
            
            if ($this->test_mode == true) {
                
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`, `prueba`, `xml` )
						VALUES ("' . $id_order . '", \'' . htmlentities($mensaje, ENT_QUOTES) . '\', ' . $timbrado . ', ' . $cancelado . ', NOW(), 1, \'' . $xml . '\' )';
            } else {
                
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`, `xml`)
						VALUES ("' . $id_order . '", \'' . htmlentities($mensaje, ENT_QUOTES) . '\', ' . $timbrado . ', ' . $cancelado . ', NOW(), \'' . $xml . '\'  )';
            }
            
            $is_correct = Db::getInstance()->execute($sql);
            
            return $is_correct;
        }

        /**
         *
         * @param unknown $id_order
         * @param number $timb
         * @param number $canc
         * @return unknown|number
         */
        public function RegistroTimbrado($id_order, $timb = 1, $canc = 0)
        {
            
            // echo "<hr> RegistroTimbrado<br>";
            $orden_validar_no_cancelada = Configuration::get('FACTURAXION_VALIDAR_NO_CANCELADA');
            
            $query = new DbQuery();
            $query->select('*');
            $query->from('timbrado', 't');
            $and = '';
            
            if ($this->test_mode == true) {
                $and = ' AND t.prueba = 1 ';
            } else {
                $and = ' AND t.prueba = 0 ';
            }
            
            if ($orden_validar_no_cancelada == 1 && $canc == 0) {
                $canc = 1;
            }
            
            $and .= " AND t.timbrado = " . $timb . " AND t.cancelado = " . $canc;
            
            $query->where('t.id_order = ' . $id_order . $and);
            $timbres = Db::getInstance()->executeS($query);
            if (isset($timbres[0]) && count($timbres[0]) > 0) {
                return $timbres[0];
            } else {
                return (0);
            }
        }

        /**
         *
         * @param unknown $id_timbrado
         * @return boolean
         */
        public function CanceladoOk($id_timbrado)
        {
            if ($id_timbrado != '' && $id_timbrado != null && $id_timbrado != 0) {
                
                $is_correct = true;
                
                if ($this->test_mode == true) {
                    
                    $is_correct = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'timbrado` SET `cancelado` = 1 WHERE id_timbrado = ' . $id_timbrado . ' AND prueba = 1');
                } else {
                    
                    $is_correct = Db::getInstance()->execute('UPDATE `' . _DB_PREFIX_ . 'timbrado` SET `cancelado` = 1 WHERE id_timbrado = ' . $id_timbrado);
                }
            } else {
                $is_correct = false;
            }
            
            return $is_correct;
        }

        /**
         *
         * @param unknown $id_order
         * @param unknown $mensaje
         * @param number $timbrado
         * @param number $cancelado
         * @return boolean
         */
        public function CanceladoNoOk($id_order, $mensaje, $timbrado = 0, $cancelado = 1)
        {
            $is_correct = true;
            
            if ($this->test_mode == true) {
                
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`, `prueba`)
						VALUES ("' . $id_order . '", \'' . htmlentities($mensaje, ENT_QUOTES) . '\', ' . $timbrado . ', ' . $cancelado . ', NOW(), 1 )';
            } else {
                
                $sql = 'INSERT INTO `' . _DB_PREFIX_ . 'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`)
						VALUES ("' . $id_order . '", \'' . htmlentities($mensaje, ENT_QUOTES) . '\', ' . $timbrado . ', ' . $cancelado . ', NOW() )';
            }
            
            $is_correct = Db::getInstance()->execute($sql);
            
            return $is_correct;
        }
    }
}

?>