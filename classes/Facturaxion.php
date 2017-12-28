
<?php

if (!class_exists('Facturaxion')) {

	class FacturaxionCore {

		public $xmlPreCFDI = '';
		public $debug_mode = 0;
		public $test_char = '';
		public $test_mode = true;
		
		public $rfc_emisor = '';
		public $numero_certificado = '';
		public $numero_certificadoS = '';
		
		public $dir_server = '/var/www/farmalisto.com.mx/htdocs/';
		public $sello_emisor = '';
		public $certificado_emisor = '';
		public $array_xml_a_timbrar = '';
		public $xml_a_timbrar = '';

		public $archivo_cer = ""; // Prueba de timbrado
  		public $archivo_pem = ""; // Prueba de timbrado
  		public $usuario ="";
		public $proveedor ="";
		public $sucursal ="";
		public $RFCEmisor  = '';
		public $RFCEmisor_nombre = '';
		public $regimenfiscal = "";
		public $cadena_original = "";



		public function __construct( $test_mode = false ) {
                        
			$test_mode = Configuration::get('PRODUCTION_FACTURAXION');
                        $this->dir_server = (Configuration::get('PS_DIR_SERVER_FACTURAXION')) ? (Configuration::get('PS_DIR_SERVER_FACTURAXION')) : $dir_server ;
//                        echo $this->dir_server;
//                        die();
			if ( $test_mode == '1') {

				$this->numero_certificado = "00001000000304972067";
				$this->archivo_cer = "xml_timbrado/certi_valido/00001000000304972067.cer"; // old 00001000000304972067.cer"; // Produccion
				$this->archivo_pem = "xml_timbrado/certi_valido/publica.key.pem"; // Produccion
				$this->usuario = "1130A7374C35496EACF575158BA20DEEF49494A2";
				$this->usuarioxml = "FME140730J95";
				$this->contrasenia = "1130A7374C35496EACF575158BA20DEEF49494A2";
				$this->proveedor = "FME140730J95";
				$this->sucursal = "3204C082-CE9E-E411-93F6-005056B8554D"; //961549
				$this->RFCEmisor  = 'FME140730J95';
				$this->RFCEmisor_nombre = 'FARMATALAM DE MEXICO S DE R.L DE C.V';
				//Código Proveedor (CP):D4C3825A19DB08FD73C025F9F8A54302F7F7A379
				$this->test_mode = $test_mode = false;
				$this->regimenfiscal = "REGIMEN GENERAL DE LEY DE LAS PERSONAS MORALES";

			} else {

				$this->test_char = 't_';
				$this->test_mode = true;	

				$this->numero_certificadoS = "30001000000300023708";
				$this->numero_certificado = "30001000000300023708";
				$this->archivo_cer = "xml_timbrado/certi_prueba/CSD01_AAA010101AAA.cer"; // Prueba de timbrado
				$this->archivo_pem = "xml_timbrado/certi_prueba/certificate1.pem"; // Prueba de timbrado 

				$this->usuario = "1763AAB0593430490B3B3EE5457A9A2580F9D7DE";
				$this->usuarioxml = "demo";
				$this->contrasenia = "123456";
				$this->proveedor = "N#@Mo!)#oh&amp;gt;)BYOdX=q_ZUCsLxqpv?";
				$this->sucursal = ""; //151048
				$this->RFCEmisor  = 'AAA010101AAA';
				$this->RFCEmisor_nombre = 'Emisor de prueba';
				$this->regimenfiscal = "REGIMEN GENERAL DE LEY DE LAS PERSONAS MORALES";

			}

		}





/*********************** PRUEBA SI FUNCIONA ***************************/

public function simplexml_to_array ($xml, &$array) {

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


/*********************** PRUEBA SI FUNCIONA ***************************/



public function array_to_xml($template_info, &$xml_template_info) {
			foreach($template_info as $key => $value) {
				if(is_array($value)) {
					if(!is_numeric($key)){
 
						$subnode = $xml_template_info->addChild("$key");
 
						if(count($value) >1 && is_array($value)){
							$jump = false;
							$count = 1;
							foreach($value as $k => $v) {
								if(is_array($v)){
									if($count++ > 1)
										$subnode = $xml_template_info->addChild("$key");
 
									$this->array_to_xml($v, $subnode);
									$jump = true;
								}
							}
							if($jump) {
								goto LE;
							}
							$this->array_to_xml($value, $subnode);
						}
						else
							$this->array_to_xml($value, $subnode);
					}
					else{
						$this->array_to_xml($value, $xml_template_info);
					}
				}
				else {
					$xml_template_info->addChild("$key","$value");
				}
 
				LE: ;
			}
		}
























public function LlavesRegistroTimbrado( $xml_sellar = false, $depurar = 0 ) {


  $rfc_emisor = $this->RFCEmisor; // Prueba de timbrado
  
  //Archivos del CSD de prueba proporcionados por el SAT.
  //ver http://developers.facturacionmoderna.com/webroot/CertificadosDemo-FacturacionModerna.zip
  $numero_certificado = $this->numero_certificadoS; // Prueba de timbrado
  $archivo_cer = $this->archivo_cer; // Prueba de timbrado
  $archivo_pem = $this->archivo_pem; // Prueba de timbrado


	
  //Datos de acceso al ambiente de pruebas

$dir = $this->dir_server;
//echo "<br>dir_actual".$this->dir_server.$archivo_pem."<hr>";

//echo "<hr> Llave pem: ||".
$received = openssl_pkey_get_private("file://".$dir.$archivo_pem);
//echo "<pre><br>";
//var_dump($received);
//echo "</pre><br>";

  //generar y sellar un XML con los CSD de pruebas
	if ( $xml_sellar !== false ) {

		$cfdi = $xml_sellar;

	} else {

		$cfdi = $this->generarXML($rfc_emisor);

	}

  //echo "<br> Luego generarXML"; 

  $cfdi = $this->sellarXML($cfdi, $numero_certificado, $archivo_cer, $archivo_pem, $depurar);
  //echo "<br> Luego sellarXML";
  
}



function sellarXML($cfdi, $numero_certificado, $archivo_cer, $archivo_pem, $depurar = 0){

  
  if ( $depurar == 1 ) {
  	echo "<br> entra funcion sellarXML";
  }
  
  $private = openssl_pkey_get_private(file_get_contents($this->dir_server.$archivo_pem));
  $this->certificado_emisor = $certificado = str_replace(array('\n', '\r'), '', base64_encode(file_get_contents($this->dir_server.$archivo_cer)));
  
  
  if ( $depurar == 1 ) {
  	echo "<br> Alistando para sellar:<br><textarea style='height: 404px; width: 1339px;' >"; echo $cfdi; echo "</textarea><br><hr>";
  }

  $xdoc = new DomDocument();

        //$fp_cfdi=fopen("/home/desarrollo/modules/facturaxion/re_".date("Y-m-d")."T".date("H:i:s",  strtotime ( '-10 minute' , strtotime ( date("H:i:s") ) )).$this->test_char."_cfdi.xml","a+"); //"_".date("Y-m-d H:i:s").
        //$fp_cfdi=fopen("/home/ubuntu/modules/facturaxion/re_".date("Y-m-d")."T".date("H:i:s",  strtotime ( '-10 minute' , strtotime ( date("H:i:s") ) )).$this->test_char."_cfdi.xml","a+"); //"_".date("Y-m-d H:i:s").
        $fp_cfdi=fopen( (Configuration::get('PS_RUTA_FACTURAXION') ).date("Y-m-d")."T".date("H:i:s",  strtotime ( '-10 minute' , strtotime ( date("H:i:s") ) )).$this->test_char."_cfdi.xml","a+"); //"_".date("Y-m-d H:i:s").
  
        fwrite($fp_cfdi,$cfdi);
        fclose($fp_cfdi);

  if ( $depurar == 1 ) {
  	@ini_set('display_errors', 'on');
			@error_reporting(E_ALL | E_STRICT);
  	$xdoc->loadXML($cfdi) or die("<br>XML invalido");
  	echo "<br> luego funcion loadXML";
  }

  $xdoc->loadXML($cfdi);// or die("<br>XML invalido");
 
  $XSL = new DOMDocument();
  $XSL->load($this->dir_server.'xml_timbrado/utilerias/xslt32/cadenaoriginal_3_3.xslt');
  
  if ( $depurar == 1 ) {
  	echo "<br> luego funcion load";  
  }
  
  $proc = new XSLTProcessor;
  $proc->importStyleSheet($XSL);
  
  if ( $depurar == 1 ) {
  	echo "<br> luego funcion importStyleSheet";
  }
  
  $cadena_original = $proc->transformToXML($xdoc);
  
  if ( $depurar == 1 ) {
  	echo "<br> luego funcion transformToXML";
  }

  openssl_sign($this->cadena_original, $sig, $private, OPENSSL_ALGO_SHA256);
  
  if ( $depurar == 1 ) {
  	echo "<br> luego funcion openssl_sign";
  }
 
  $this->sello_emisor = $sello = base64_encode($sig);


  $this->array_xml_a_timbrar['@attributes']['Certificado'] = $this->certificado_emisor;
  $this->array_xml_a_timbrar['@attributes']['Sello'] = $this->sello_emisor;


/*
  $c = $xdoc->getElementsByTagNameNS('http://www.sat.gob.mx/cfd/3', 'Comprobante')->item(0); 
  $c->setAttribute('sello', $sello);
  $c->setAttribute('certificado', $certificado);
  $c->setAttribute('noCertificado', $numero_certificado);
*/


 

  if ( $depurar == 1 ) {
  	echo "<pre> sello c:<br>";
	  print_r( $sello );
	  echo "<br> certificado: ";
	  print_r( $certificado );
	  echo "<br> numero_certificado: ";
	  print_r( $numero_certificado );

	  echo "</pre> <br>";
	}

  //return $xdoc->saveXML();

}


function generarXML($rfc_emisor){

  //echo "<br> Fecha: ".$fecha_actual = substr( date('c',strtotime('-1 day')), 0, 19);
  /*
	Puedes encontrar más ejemplos y documentación sobre estos archivos aquí. (Factura, Nota de Crédito, Recibo de Nómina y más...)
	Link: https://github.com/facturacionmoderna/Comprobantes
	Nota: Si deseas información adicional contactanos en www.facturacionmoderna.com
 */

/********************* CON IVA ***************************/
/*
$cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante  xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" descuento="0" subTotal="192.36" fecha="2014-12-03T15:40:30" certificado="MIIEkzCCA3ugAwIBAgIUMDAwMDEwMDAwMDAzMDQ5NzIwNjcwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA4MjUyMzIyMzJaFw0xODA4MjUyMzIyMzJaMIHfMSswKQYDVQQDEyJGQVJNQVRBTEFNIERFIE1FWElDTyBTIERFIFJMIERFIENWMSswKQYDVQQpEyJGQVJNQVRBTEFNIERFIE1FWElDTyBTIERFIFJMIERFIENWMSswKQYDVQQKEyJGQVJNQVRBTEFNIERFIE1FWElDTyBTIERFIFJMIERFIENWMSUwIwYDVQQtExxGTUUxNDA3MzBKOTUgLyBDRUJBNzEwNjE0TUUzMR4wHAYDVQQFExUgLyBDRUJBNzEwNjE0SERGUlJOMDkxDzANBgNVBAsTBk1BVFJJWjCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAh/bxZWJVHG4u2Qs2ntuU5Ok3SMgJNEVXvwnn96ZoEN8s2hBYKI0/BUeOlY3t2GWYFVdEyO80ciKpGBUy0vHlwVUIojd6dUbNoNa9LWIOEvqmZZFq+zb6pid/wWWN2qvEPUL8xNHLYZF5AdkfITOuAZCCgcgzeYAlZqVQreUYcpECAwEAAaMdMBswDAYDVR0TAQH/BAIwADALBgNVHQ8EBAMCBsAwDQYJKoZIhvcNAQEFBQADggEBAM7R4JTGhvCpb0TuDNEn8nHsXk56cBunLnqpWW+DUatuUEnbSbyfxGGQoMC77YJ2iUeiH7mGq0M1vzrpox6eu7JxUv66VZOt4rq6lfM7kDyBTWWcEhXfkVhnGWnN5dngFHTBu6Xip7Y2Pf55MgbiVv1lmDY524QZJKLHpHx+ZPv/2jZ8FgC+gMGE40qJgtrI3R87JM2qu27KGZV6DUC8OtPMG/SZlKI9TP5B03WfOVdDemSlMajrwj42fmY/3D5IqJS991zcch0sWe9z5HGaSsW+WGZ0Yl3rmthijNLk7qw2Uiv5ZIUifgZ0RncptlF35Ju3xipwag5kD8z47JGRNWM=" total="223.14" folio="4551" metodoDePago="Efectivo" noCertificado="00001000000304972067" tipoDeComprobante="ingreso" LugarExpedicion="MEXICO., DISTRITO FEDERAL." version="3.2" formaDePago="PAGO EN UNA SOLA EXHIBICION" sello="eTUd33rr3ZmbbGCHii066uNzuK4ImSGwDqJMExyBU7L7RCD9hxiV5lPdsgta1RHvs5qLRFE4RIEVQ0pB1H9b6/2AZD6TUdqqd1oAeiZEupCljB9GCGVmLtX+U3LW3pfvwypHjYy16qQmmr2/tzvqFFMT0lhCFXv3+pZY2Ap6Ors=">

  <cfdi:Emisor rfc="FME140730J95" nombre="FARMATALAM DE MEXICO S DE R.L DE C.V">
	<cfdi:DomicilioFiscal calle="MEXICALI" codigoPostal="06170" estado="DISTRITO FEDERAL" municipio="CUAUHTEMOC" pais="México" noInterior="8" noExterior="4" colonia="HIPODROMO CONDESA" />
	<cfdi:RegimenFiscal Regimen="Régimen General de Ley Personas Morales" />
  </cfdi:Emisor>

  <cfdi:Receptor rfc="CGL941003AU8" nombre="CAFE LA GLORIA S.A. DE C.V.">
	<cfdi:Domicilio calle="VICENTE SUAREZ." localidad="MEXICO" codigoPostal="06140" municipio="CUAUHTEMOC" pais="México" noInterior="D" noExterior="41" colonia="CONDESA" />
  </cfdi:Receptor>

  <cfdi:Conceptos>
	<cfdi:Concepto valorUnitario="192.36" unidad="NA" cantidad="1" importe="192.36" descripcion="DERMOTIN A CRA 100 G" />
  </cfdi:Conceptos>

  <cfdi:Impuestos totalImpuestosRetenidos="0" totalImpuestosTrasladados="30.78">
		<cfdi:Traslados>
			<cfdi:Traslado impuesto="IVA" importe="30.78" tasa="16.00" />
		</cfdi:Traslados>
	</cfdi:Impuestos>
</cfdi:Comprobante>
XML;
*/

/********************************  SIN IVA  **************************/
/*
 $cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" descuento="0" subTotal="1822" fecha="2015-01-14T06:36:40" certificado="MIIEkzCCA3ugAwIBAgIUMDAwMDEwMDAwMDAzMDQ5NzIwNjcwDQYJKoZIhvcNAQEFBQAwggGKMTgwNgYDVQQDDC9BLkMuIGRlbCBTZXJ2aWNpbyBkZSBBZG1pbmlzdHJhY2nDs24gVHJpYnV0YXJpYTEvMC0GA1UECgwmU2VydmljaW8gZGUgQWRtaW5pc3RyYWNpw7NuIFRyaWJ1dGFyaWExODA2BgNVBAsML0FkbWluaXN0cmFjacOzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNpw7NuMR8wHQYJKoZIhvcNAQkBFhBhY29kc0BzYXQuZ29iLm14MSYwJAYDVQQJDB1Bdi4gSGlkYWxnbyA3NywgQ29sLiBHdWVycmVybzEOMAwGA1UEEQwFMDYzMDAxCzAJBgNVBAYTAk1YMRkwFwYDVQQIDBBEaXN0cml0byBGZWRlcmFsMRQwEgYDVQQHDAtDdWF1aHTDqW1vYzEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTUwMwYJKoZIhvcNAQkCDCZSZXNwb25zYWJsZTogQ2xhdWRpYSBDb3ZhcnJ1YmlhcyBPY2hvYTAeFw0xNDA4MjUyMzIyMzJaFw0xODA4MjUyMzIyMzJaMIHfMSswKQYDVQQDEyJGQVJNQVRBTEFNIERFIE1FWElDTyBTIERFIFJMIERFIENWMSswKQYDVQQpEyJGQVJNQVRBTEFNIERFIE1FWElDTyBTIERFIFJMIERFIENWMSswKQYDVQQKEyJGQVJNQVRBTEFNIERFIE1FWElDTyBTIERFIFJMIERFIENWMSUwIwYDVQQtExxGTUUxNDA3MzBKOTUgLyBDRUJBNzEwNjE0TUUzMR4wHAYDVQQFExUgLyBDRUJBNzEwNjE0SERGUlJOMDkxDzANBgNVBAsTBk1BVFJJWjCBnzANBgkqhkiG9w0BAQEFAAOBjQAwgYkCgYEAh/bxZWJVHG4u2Qs2ntuU5Ok3SMgJNEVXvwnn96ZoEN8s2hBYKI0/BUeOlY3t2GWYFVdEyO80ciKpGBUy0vHlwVUIojd6dUbNoNa9LWIOEvqmZZFq+zb6pid/wWWN2qvEPUL8xNHLYZF5AdkfITOuAZCCgcgzeYAlZqVQreUYcpECAwEAAaMdMBswDAYDVR0TAQH/BAIwADALBgNVHQ8EBAMCBsAwDQYJKoZIhvcNAQEFBQADggEBAM7R4JTGhvCpb0TuDNEn8nHsXk56cBunLnqpWW+DUatuUEnbSbyfxGGQoMC77YJ2iUeiH7mGq0M1vzrpox6eu7JxUv66VZOt4rq6lfM7kDyBTWWcEhXfkVhnGWnN5dngFHTBu6Xip7Y2Pf55MgbiVv1lmDY524QZJKLHpHx+ZPv/2jZ8FgC+gMGE40qJgtrI3R87JM2qu27KGZV6DUC8OtPMG/SZlKI9TP5B03WfOVdDemSlMajrwj42fmY/3D5IqJS991zcch0sWe9z5HGaSsW+WGZ0Yl3rmthijNLk7qw2Uiv5ZIUifgZ0RncptlF35Ju3xipwag5kD8z47JGRNWM=" total="1822" folio="4551" metodoDePago="Efectivo" noCertificado="00001000000304972067" tipoDeComprobante="ingreso" LugarExpedicion="MEXICO., DISTRITO FEDERAL." version="3.2" formaDePago="PAGO EN UNA SOLA EXHIBICION" sello="H2M6bT61CKGlV6G8ReXrNEXPrz26oN0i38RCiqTvJn0xGrAydCtlD8ssaikx+A36+Iuj3fbaVOgL5kszZdos+C5mPu0kfR2z4jnnHXIqaWTyf9Rw7Yh2LYRX1hgLIh9Eu6+PA7+tGvU/JBKAH7djcl5idSgklMTtmjN6Nv2gh3o=">

  <cfdi:Emisor rfc="FME140730J95" nombre="FARMATALAM DE MEXICO S DE R.L DE C.V">
	<cfdi:DomicilioFiscal calle="MEXICALI" codigoPostal="06170" estado="DISTRITO FEDERAL" municipio="CUAUHTEMOC" pais="México" noInterior="8" noExterior="4" colonia="HIPODROMO CONDESA" />
	<cfdi:RegimenFiscal Regimen="Régimen General de Ley Personas Morales" />
  </cfdi:Emisor>

  <cfdi:Receptor rfc="CGL941003AU8" nombre="CAFE LA GLORIA S.A. DE C.V.">
	<cfdi:Domicilio calle="VICENTE SUAREZ." localidad="MEXICO" codigoPostal="06140" municipio="CUAUHTEMOC" pais="México" noInterior="D" noExterior="41" colonia="CONDESA" />
  </cfdi:Receptor>


  <cfdi:Conceptos>
	<cfdi:Concepto valorUnitario="644" unidad="NA" cantidad="1" importe="644" descripcion="ALMETEC 40 MG TAB 28" />
	<cfdi:Concepto valorUnitario="915" unidad="NA" cantidad="1" importe="915" descripcion="CETOLAN 973 MG GRAG 100" />
	<cfdi:Concepto valorUnitario="263" unidad="NA" cantidad="1" importe="263" descripcion="CONCOR 1.25MG GRAG 30" />
  </cfdi:Conceptos>

  <cfdi:Impuestos totalImpuestosRetenidos="0" totalImpuestosTrasladados="0">
	<cfdi:Traslados>
	  <cfdi:Traslado impuesto="IVA" importe="0" tasa="0" />
	</cfdi:Traslados>
  </cfdi:Impuestos>
</cfdi:Comprobante>
XML;

*/
/*****************************************************/



/********************* inicio prueba timbrado ************************/
/*
 $cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" version="3.2" serie="FX" folio="505" fecha="2015-01-13T16:29:31" sello="qsdei1QXgAazWFTo6ecXWPhKxxRqC5vwW2bU2hXjdfrBt9f7TRLzF77A+WusvSWT5jk8kPsDBtt2DsF6ZEW2OkgoJBA6yPfzNY5zoMTY6FamndawbPK+g19MfSKcmz70u5HP444YVBIXZcdc6Lrp6sCSc8DLbMNPXLxkVP2s/L4=" formaDePago="PAGO EN UNA SOLA EXHIBICION" noCertificado="aaa010101aaa__csd_01" certificado="MIIEdDCCA1ygAwIBAgIUMjAwMDEwMDAwMDAxMDAwMDU4NjcwDQYJKoZIhvcNAQEFBQAwggFvMRgwFgYDVQQDDA9BLkMuIGRlIHBydWViYXMxLzAtBgNVBAoMJlNlcnZpY2lvIGRlIEFkbWluaXN0cmFjacOzbiBUcmlidXRhcmlhMTgwNgYDVQQLDC9BZG1pbmlzdHJhY2nDs24gZGUgU2VndXJpZGFkIGRlIGxhIEluZm9ybWFjacOzbjEpMCcGCSqGSIb3DQEJARYaYXNpc25ldEBwcnVlYmFzLnNhdC5nb2IubXgxJjAkBgNVBAkMHUF2LiBIaWRhbGdvIDc3LCBDb2wuIEd1ZXJyZXJvMQ4wDAYDVQQRDAUwNjMwMDELMAkGA1UEBhMCTVgxGTAXBgNVBAgMEERpc3RyaXRvIEZlZGVyYWwxEjAQBgNVBAcMCUNveW9hY8OhbjEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTIwMAYJKoZIhvcNAQkCDCNSZXNwb25zYWJsZTogSMOpY3RvciBPcm5lbGFzIEFyY2lnYTAeFw0xMjA3MjcxNzAyMDBaFw0xNjA3MjcxNzAyMDBaMIHbMSkwJwYDVQQDEyBBQ0NFTSBTRVJWSUNJT1MgRU1QUkVTQVJJQUxFUyBTQzEpMCcGA1UEKRMgQUNDRU0gU0VSVklDSU9TIEVNUFJFU0FSSUFMRVMgU0MxKTAnBgNVBAoTIEFDQ0VNIFNFUlZJQ0lPUyBFTVBSRVNBUklBTEVTIFNDMSUwIwYDVQQtExxBQUEwMTAxMDFBQUEgLyBIRUdUNzYxMDAzNFMyMR4wHAYDVQQFExUgLyBIRUdUNzYxMDAzTURGUk5OMDkxETAPBgNVBAsTCFVuaWRhZCAxMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC2TTQSPONBOVxpXv9wLYo8jezBrb34i/tLx8jGdtyy27BcesOav2c1NS/Gdv10u9SkWtwdy34uRAVe7H0a3VMRLHAkvp2qMCHaZc4T8k47Jtb9wrOEh/XFS8LgT4y5OQYo6civfXXdlvxWU/gdM/e6I2lg6FGorP8H4GPAJ/qCNwIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQUFAAOCAQEATxMecTpMbdhSHo6KVUg4QVF4Op2IBhiMaOrtrXBdJgzGotUFcJgdBCMjtTZXSlq1S4DG1jr8p4NzQlzxsdTxaB8nSKJ4KEMgIT7E62xRUj15jI49qFz7f2uMttZLNThipunsN/NF1XtvESMTDwQFvas/Ugig6qwEfSZc0MDxMpKLEkEePmQwtZD+zXFSMVa6hmOu4M+FzGiRXbj4YJXn9Myjd8xbL/c+9UIcrYoZskxDvMxc6/6M3rNNDY3OFhBK+V/sPMzWWGt8S1yjmtPfXgFs1t65AZ2hcTwTAuHrKwDatJ1ZPfa482ZBROAAX1waz7WwXp0gso7sDCm2/yUVww==" subTotal="1" TipoCambio="1" Moneda="Pesos" total="1.16" tipoDeComprobante="ingreso" metodoDePago="NO IDENTIFICADO" LugarExpedicion="MIGUEL HIDALGO, REFORMA">
  <cfdi:Emisor rfc="AAA010101AAA" nombre="EMPRESA DE PRUEBA S.A">
	<cfdi:DomicilioFiscal calle="PRUEBA" noExterior="156" colonia="REFORMA" localidad="DF" municipio="MIGUEL HIDALGO" estado="MEXICO" pais="MEXICO" codigoPostal="01234" />
	<cfdi:ExpedidoEn calle="PRUEBA EXPEDIDO" noExterior="156" colonia="REFORMA" localidad="DF" municipio="MIGUEL HIDALGO" estado="MEXICO" pais="MEXICO" codigoPostal="01234" />
	<cfdi:RegimenFiscal Regimen="REGIMEN GENERAL DE LEY DE LAS PERSONAS FISICAS CON ACTIVIDADES EMPRESARIALES" />
  </cfdi:Emisor>
  <cfdi:Receptor rfc="AAA010101AAA" nombre="EMPRESA DE PRUEBAaaa">
	<cfdi:Domicilio calle="PRUEBAS" noExterior="789" colonia="REFORMA" localidad="RECEPTOR" municipio="MIGUEL HIDALGO" estado="MEXICO" pais="MEXICO" codigoPostal="01234" />
  </cfdi:Receptor>
  <cfdi:Conceptos>
	<cfdi:Concepto cantidad="1" unidad="C/U" noIdentificacion="0000000001" descripcion="CONCEPTO DE PRUEBA AÑO" valorUnitario="1" importe="1" />
  </cfdi:Conceptos>
  <cfdi:Impuestos totalImpuestosRetenidos="0" totalImpuestosTrasladados="0.16">
	<cfdi:Traslados>
	  <cfdi:Traslado impuesto="IVA" tasa="16" importe="16" />
	</cfdi:Traslados>
  </cfdi:Impuestos>
</cfdi:Comprobante>
XML;
*/



/********************* fin prueba timbrado ************************/

/*    
 $cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante LugarExpedicion="PARQUE INDUSTRIAL XALOSTOC 55348 XALOSTOC MEX."
				  certificado="MIIDWjCCAkKgAwIBAgIUMDAwMDExMDAwMDAyMDAwMDAxNTQwDQYJKoZIhvcNAQEFBQAwgcMxGTAXBgNVBAcTEENpdWRhZCBkZSBNZXhpY28xFTATBgNVBAgTDE1leGljbywgRC5GLjELMAkGA1UEBhMCTVgxGjAYBgNVBAMTEUFDIGRlIFBydWViYXMgU0FUMTYwNAYDVQQLFC1BZG1pbmlzdHJhY2nzbiBkZSBTZWd1cmlkYWQgZGUgbGEgSW5mb3JtYWNp824xLjAsBgNVBAoUJVNlcnZpY2lvIGRlIEFkbWluaXN0cmFjafNuIFRyaWJ1dGFyaWEwHhcNMDYwODA0MjIwNDI3WhcNMDgwODAzMjIwNDI3WjBvMSUwIwYDVQQtExxBQUEwMTAxMDFBQUEgLyBBQUFBMDEwMTAxQUFBMR4wHAYDVQQFExUgLyBBQUFBMDEwMTAxSERGUlhYMDExFTATBgNVBAoTDEFBQTAxMDEwMUFBQTEPMA0GA1UECxMGTWF0cml6MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDKRVP186zuDWHP9BDOGPAOfJaqBlKKaNN6FV0mkO6iyG7TlpWrO3IBRBX4lw5k5MEDBwLxFmRQJ68ZHkaPDdBfGi3SO6VA+rkt50tlH5bLcSycWDAkCJ7U72TWDypx69TcafQwpr2vrfXPRmEz/kie5vF0H3tVkVxn5WQ6YUAMeQIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwID6DANBgkqhkiG9w0BAQUFAAOCAQEARQmA3gt+y5vZiFgHQV4XZbdyfHdTlmHV8VwIvHou/ghIfgp3CsazBvputFQlWEKkpYCbbYErFqH8/G4i5ZzxKqr68XT/otEU2YlkMqJA8I/KGVQkMMBQMtpYsI+txw4f1Y72q2x8OdcO7nTtMOpAlwtEIPppLadLE405K9bFMwbyX+NH/w8ZcEF4T3h9T7/5mrqjrssURxR6I8tWI/lXsqg7xMJ5mdnHC9gd89GrEU/BPM5HCVRomQ0FWpik5uMNW5PNUxpYbIAbOfM1sioCJpMhBFMLIhu4Q47C8WM8VqZzzwawDwQG2iOS0rL6d7D5F8SB7Li3zt9vbbMzBc5xGg=="
				  descuento="0"
				  fecha="2013-12-22T10:28:15"
				  folio="000001"
				  formaDePago="EL PAGO DE ESTA FACTURA (CONTRAPRESTACION) SE EFECTUARA EN UNA SOLA EXHIBICION, SI POR ALGUNA RAZON NO FUERA ASI, LO PAGARAN EN LAS PARCIALIDADES RESPECTIVAS"
				  metodoDePago="NO APLICA"
				  noCertificado="00001100000200000154"
				  sello="E0q9sxQ6IfmhONw+BuQPdyoYsMKZ7tqOSYObCg5vCU6IidFNgPnXSswHv34w9ZnWAbMiL687T+B3LvIsj+XYGjR70gtfkSD7buBGlVpgSildMgreVBydMLI/+SjbVlSiR21SYB52HWOcsc4A/43lIVhWcE5cpvIAgGVosjMvy58="
				  serie="FDXA"
				  subTotal="1"
				  tipoDeComprobante="ingreso"
				  total="1.16"
				  version="3.2"
				  xmlns:cfdi="http://www.sat.gob.mx/cfd/3"
				  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
				  xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd">
	<cfdi:Emisor nombre="FABRICA DE JABON LA CORONA SA DE CV"
				 rfc="FJC780315E91">
		<cfdi:DomicilioFiscal calle="CARLOS B. ZETINA"
							  codigoPostal="55348"
							  colonia="PARQUE INDUSTRIAL XALOSTOC"
							  estado="MEXICO"
							  municipio="ECATEPEC DE MORELOS"
							  noExterior="80"
							  pais="MEXICO" />
		<cfdi:RegimenFiscal Regimen="REGIMEN GENERAL DE LEY DE PERSONAS MORALES" />
	</cfdi:Emisor>
	<cfdi:Receptor nombre="FABRICA DE JABON LA CORONA,S.A.C.V"
				   rfc="XAXX010101000">
		<cfdi:Domicilio calle="CARLOS B. ZETINA"
						codigoPostal="55340"
						colonia="FRACC.INDUSTRIAL XALOSTOC"
						estado="ESTADO DE MEXICO"
						localidad="ECATEPEC DE MORELOS"
						municipio="ECATEPEC"
						noExterior="80"
						pais="MEXICO" />
	</cfdi:Receptor>
	<cfdi:Conceptos>
		<cfdi:Concepto cantidad="1"
					   descripcion="VARIOS"
					   importe="1.00"
					   unidad="Pieza"
					   valorUnitario="1" />
	</cfdi:Conceptos>
	<cfdi:Impuestos totalImpuestosTrasladados="0.16">
		<cfdi:Traslados>
			<cfdi:Traslado importe="0.16"
						   impuesto="IVA"
						   tasa="16.00" />
		</cfdi:Traslados>
	</cfdi:Impuestos>
</cfdi:Comprobante>
XML;*/


/*
  $cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xs="http://www.w3.org/2001/XMLSchema" version="3.2" fecha="$fecha_actual" tipoDeComprobante="ingreso" noCertificado="" certificado="" sello="" formaDePago="Pago en una sola exhibición" metodoDePago="Transferencia Electrónica" NumCtaPago="No identificado" LugarExpedicion="San Pedro Garza García, Mty." subTotal="10.00" total="11.60">
<cfdi:Emisor nombre="EMPRESA DEMO" rfc="$rfc_emisor">
  <cfdi:RegimenFiscal Regimen="No aplica"/>
</cfdi:Emisor>
<cfdi:Receptor nombre="PUBLICO EN GENERAL" rfc="XAXX010101000"></cfdi:Receptor>
<cfdi:Conceptos>
  <cfdi:Concepto cantidad="10" unidad="No aplica" noIdentificacion="00001" descripcion="Servicio de Timbrado" valorUnitario="1.00" importe="10.00">
  </cfdi:Concepto>  
</cfdi:Conceptos>
<cfdi:Impuestos totalImpuestosTrasladados="1.60">
  <cfdi:Traslados>
	<cfdi:Traslado impuesto="IVA" tasa="16.00" importe="1.6"></cfdi:Traslado>
  </cfdi:Traslados>
</cfdi:Impuestos>
</cfdi:Comprobante>
XML;*/




/**********************para sello de pruebas 737373737373737373******************************/

/*
$cfdi = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<cfdi:Comprobante xmlns:cfdi="http://www.sat.gob.mx/cfd/3" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv32.xsd" version="3.2" fecha="2015-01-20T10:57:16" formaDePago="PAGO EN UNA SOLA EXHIBICION" condicionesDePago="Parcialidades" metodoDePago="Efectivo" noCertificado="20001000000100005867" certificado="MIIEdDCCA1ygAwIBAgIUMjAwMDEwMDAwMDAxMDAwMDU4NjcwDQYJKoZIhvcNAQEFBQAwggFvMRgwFgYDVQQDDA9BLkMuIGRlIHBydWViYXMxLzAtBgNVBAoMJlNlcnZpY2lvIGRlIEFkbWluaXN0cmFjacOzbiBUcmlidXRhcmlhMTgwNgYDVQQLDC9BZG1pbmlzdHJhY2nDs24gZGUgU2VndXJpZGFkIGRlIGxhIEluZm9ybWFjacOzbjEpMCcGCSqGSIb3DQEJARYaYXNpc25ldEBwcnVlYmFzLnNhdC5nb2IubXgxJjAkBgNVBAkMHUF2LiBIaWRhbGdvIDc3LCBDb2wuIEd1ZXJyZXJvMQ4wDAYDVQQRDAUwNjMwMDELMAkGA1UEBhMCTVgxGTAXBgNVBAgMEERpc3RyaXRvIEZlZGVyYWwxEjAQBgNVBAcMCUNveW9hY8OhbjEVMBMGA1UELRMMU0FUOTcwNzAxTk4zMTIwMAYJKoZIhvcNAQkCDCNSZXNwb25zYWJsZTogSMOpY3RvciBPcm5lbGFzIEFyY2lnYTAeFw0xMjA3MjcxNzAyMDBaFw0xNjA3MjcxNzAyMDBaMIHbMSkwJwYDVQQDEyBBQ0NFTSBTRVJWSUNJT1MgRU1QUkVTQVJJQUxFUyBTQzEpMCcGA1UEKRMgQUNDRU0gU0VSVklDSU9TIEVNUFJFU0FSSUFMRVMgU0MxKTAnBgNVBAoTIEFDQ0VNIFNFUlZJQ0lPUyBFTVBSRVNBUklBTEVTIFNDMSUwIwYDVQQtExxBQUEwMTAxMDFBQUEgLyBIRUdUNzYxMDAzNFMyMR4wHAYDVQQFExUgLyBIRUdUNzYxMDAzTURGUk5OMDkxETAPBgNVBAsTCFVuaWRhZCAxMIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC2TTQSPONBOVxpXv9wLYo8jezBrb34i/tLx8jGdtyy27BcesOav2c1NS/Gdv10u9SkWtwdy34uRAVe7H0a3VMRLHAkvp2qMCHaZc4T8k47Jtb9wrOEh/XFS8LgT4y5OQYo6civfXXdlvxWU/gdM/e6I2lg6FGorP8H4GPAJ/qCNwIDAQABox0wGzAMBgNVHRMBAf8EAjAAMAsGA1UdDwQEAwIGwDANBgkqhkiG9w0BAQUFAAOCAQEATxMecTpMbdhSHo6KVUg4QVF4Op2IBhiMaOrtrXBdJgzGotUFcJgdBCMjtTZXSlq1S4DG1jr8p4NzQlzxsdTxaB8nSKJ4KEMgIT7E62xRUj15jI49qFz7f2uMttZLNThipunsN/NF1XtvESMTDwQFvas/Ugig6qwEfSZc0MDxMpKLEkEePmQwtZD+zXFSMVa6hmOu4M+FzGiRXbj4YJXn9Myjd8xbL/c+9UIcrYoZskxDvMxc6/6M3rNNDY3OFhBK+V/sPMzWWGt8S1yjmtPfXgFs1t65AZ2hcTwTAuHrKwDatJ1ZPfa482ZBROAAX1waz7WwXp0gso7sDCm2/yUVww==" sello="J6og0eZ7+trCMC/SxeUPZzDEJqraegjtCAFBUMqeeRs/y0ZgknUV0iSz4pqJwTZbE0dM69gE1bgSQSKp5+bNC3KKYkMi5cxbS2qCx22FKr33fstovqF6XEi6VRHtMseF3iAOntpU16iSGbN7wXzUBwsnBM6R7t6cYNONsuK328s=" subTotal="7200.00" total="7934.40" descuento="360.00" motivoDescuento="5% de descuento por pago en efectivo" tipoDeComprobante="ingreso" LugarExpedicion="MEXICO DISTRITO FEDERAL"> 
  <cfdi:Emisor rfc="AAA010101AAA" nombre="Emisor de prueba"> 
	<cfdi:DomicilioFiscal calle="Tamaulipas" noExterior="125" noInterior="1" colonia="Roma" localidad="México" referencia="Entre León y Manzanillo" municipio="Cuauhtémoc" estado="Distrito Federal" pais="México" codigoPostal="35143" /> 
	<cfdi:ExpedidoEn calle="Acapulco" noExterior="651" noInterior="1" colonia="Roma" localidad="México" municipio="Cuauhtémoc" estado="Distrito Federal" pais="México" codigoPostal="35135" /> 
	<cfdi:RegimenFiscal Regimen="REGIMEN GENERAL DE LEY DE LAS PERSONAS MORALES" /> 
  </cfdi:Emisor> 
  <cfdi:Receptor rfc="XAXX010101000" nombre="MOSTRADOR"> 
	<cfdi:Domicilio calle="Heriberto Frias" noExterior="513" colonia="Narvarte" localidad="MEXICO" referencia="Entre Morena y Esperanza" municipio="Benito Juárez" estado="DISTRITO FEDERAL" pais="MEXICO" codigoPostal="03600" /> 
  </cfdi:Receptor> 
  <cfdi:Conceptos> 
	<cfdi:Concepto cantidad="1.00" unidad="Pieza" descripcion="Computadora armada" valorUnitario="3000.00" importe="3000.00"> 
	  <cfdi:Parte cantidad="1.00" noIdentificacion="SAD665874DCSD6CX" descripcion="Disco duro de 500 GB" importe="1000.00"> 
		<cfdi:InformacionAduanera numero="ASDRFSF345FDF" fecha="2012-05-09" aduana="Baja California" /> 
	  </cfdi:Parte> 
	  <cfdi:Parte cantidad="1.00" noIdentificacion="SDFSDFC321641ERW2E3R315XV" descripcion="Procesador AMD" importe="2000.00" /> 
	</cfdi:Concepto> 
	<cfdi:Concepto cantidad="10.00" unidad="Caja" descripcion="Caja de DVD" valorUnitario="120.00" importe="1200.00"> 
	  <cfdi:InformacionAduanera numero="AABBCC" fecha="2012-05-09" aduana="Tijuana" /> 
	  <cfdi:InformacionAduanera numero="ZZZYYYWWW" fecha="2012-05-09" aduana="Baja California" /> 
	</cfdi:Concepto> 
	<cfdi:Concepto cantidad="1.00" unidad="Pieza" noIdentificacion="AX546461XASASD" descripcion="Monitor de 19 marca AOC" valorUnitario="3000.00" importe="3000.00" /> 
  </cfdi:Conceptos> 
  <cfdi:Impuestos totalImpuestosTrasladados="1094.40"> 
	<cfdi:Traslados> 
	  <cfdi:Traslado impuesto="IVA" tasa="16.00" importe="1094.40" /> 
	</cfdi:Traslados> 
  </cfdi:Impuestos> 
</cfdi:Comprobante>
XML;

*/


/************************************* GENERADOS A PARTIR DEL ARRAY ******************************************/

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















	public function stripAccents($string) {
    $string = trim($string);

    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );

    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );

    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );

    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );

    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );

    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç', '&'),
        array('n', 'N', 'c', 'C', 'y'),
        $string
    );

    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array( "<br>", "<sup>FM</sup>", "\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "< ", ";", ",", ":",
             ".", "♥", "–" ),
        '',
        $string
    );


    return $string;
}





public function trim_all( $str , $what = NULL , $with = ' ' )
{
    if( $what === NULL )
    {
        //  Character      Decimal      Use
        //  "\0"            0           Null Character
        //  "\t"            9           Tab
        //  "\n"           10           New line
        //  "\x0B"         11           Vertical Tab
        //  "\r"           13           New Line in Mac
        //  " "            32           Space
       
        $what   = "\\x00-\\x20";    //all white-spaces and control chars
    }
   
    return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
}






	/**
	 * [solicitud2 Método usado para timbrar la factura o para recuperar los datos de timbrado]
	 * @param  [String] $metodo_pago     [El metodo de pago con el que se realizó la compra]
	 * @param  [Array] $cupon           [El cupón usado en la compra, si lo hay]
	 * @param  [Array] $list_products   [Listado de productos comprados]
	 * @param  [Array] $invoice_address [Listado de la dirección de facturación]
	 * @param  [Objeto] $order_tot       [Listado detallado de la orden]
	 * @return [Array]                  [Retorna la respuesta del timbrado de la factura]
	 */
	
		public function solicitud2( $metodo_pago, $cupon, $list_products, $invoice_address, $order_tot, $array_ivas, $val_total_de_iva, $estado_orden = 0, $obligar_timbrado = 0, $hacer_debug = 0) {

			$orden_validar = Configuration::get('FACTURAXION_ORDER_VALIDATE');
                        
                        $order_tot->total_discounts = ($order_tot->total_products_wt+$order_tot->total_shipping_tax_incl+$order_tot->total_wrapping_tax_incl-$order_tot->total_paid_tax_incl);			
			$valor_timbrado = $this->RegistroTimbrado( $order_tot->id , 1);

			//echo "<br> <pre> orden timbrar datos : ";
			//print_r($valor_timbrado);
			//echo "<br></pre> ".substr( $order_tot->invoice_date, 0, 10);
			//exit;

			if ( isset($order_tot->invoice_date) && strlen($order_tot->invoice_date) >= 10 ) { 
				$invoice_date_t = substr( $order_tot->invoice_date, 0, 10)."/";
			} else {
				$invoice_date_t = "";
			}
			

			$xml_previamente_generado = $this->dir_server."xml_timbrado/response/".$invoice_date_t.$this->test_char.$order_tot->id."_response.xml";
			//echo "<br>xml ruta: ".$xml_previamente_generado;
			//exit;
			if ( ( ( !is_array($valor_timbrado) && $valor_timbrado == 0 ) && !file_exists($xml_previamente_generado) && $estado_orden == 5 ) || $obligar_timbrado == 1 || $hacer_debug == 1)  {
				
				if ( $orden_validar == $order_tot->id ) {
					echo "<br> or: ".$order_tot->id;
					echo "<br> ot: ".$obligar_timbrado;
					echo "<br> order: <pre>";
					print_r($order_tot);
					echo "</pre>";

					echo "<hr><div style='font-size:13px'> list_products: <pre style='font-size:13px'><font size='3'>"; //current_state
					print_r($list_products);
					echo " </pre>";

					echo "<hr><div style='font-size:13px'> invoice_address: <pre style='font-size:13px'><font size='3'>"; //current_state
					print_r($invoice_address);
					echo "</hr> </pre>";

					echo "<hr><div style='font-size:13px'> cupon: <pre style='font-size:13px'><font size='3'>"; //current_state
					print_r($cupon);
					echo "</hr> </pre>";
				}

				$cant_prods = 0;
				$order_tot->total_products = 0;
				$total_productos = 0;
				$hjys='';
				$totalDescuentoConceptos = 0;
				$totalImportes = 0;
				$infoConceptos = '';

				foreach ($list_products as $key_prod => $value) {
					if($list_products[$key_prod]['unit_price_tax_excl'] != 0){
						$total_productos += 1;
					}
				}

				foreach ($list_products as $key_prod => $value) {
					if($list_products[$key_prod]['unit_price_tax_excl'] != 0){
						$base = number_format( $list_products[$key_prod]['total_price_tax_excl'], 2, '.', '');
						//$hjys .= "<br>".$list_products[$key_prod]['product_name'];
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveProdServ'] = $list_products[$key_prod]['ClaveProdServ'];
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['NoIdentificacion'] = $this->trim_all( $list_products[$key_prod]['product_reference'] );
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Cantidad'] = number_format( $list_products[$key_prod]['product_quantity'], 6, '.', '');
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveUnidad'] = 'H87';
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Unidad'] = 'Pieza';
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Descripcion'] = $this->trim_all( trim( $this->stripAccents( $list_products[$key_prod]['product_name'] ) ) );
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ValorUnitario'] = number_format( $list_products[$key_prod]['unit_price_tax_excl'], 2, '.', '');
						$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Importe'] = $base;
						if($list_products[$key_prod]['tax_rate'] != '0.000'){
							$importe = number_format(($list_products[$key_prod]['total_price_tax_excl']*$list_products[$key_prod]['tax_rate'])/100, 2, '.', '');
							$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Base'] = $base;
							$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Impuesto'] = "002";
							$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TipoFactor'] = "Tasa";
							$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TasaOCuota'] = number_format($list_products[$key_prod]['tax_rate']/100, 6, '.', '');
							$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Importe'] = $importe;
							$totalImportes += $importe;
						}
						$order_tot->total_products += $base;
						$cant_prods++;
					}
				}
				
				/******************  ENVIO DE PRODUCTOS... DOMICILIO ******************/
				
				if ( $order_tot->total_shipping != '0.00' || $order_tot->total_shipping_tax_incl != '0.00' ) {
					$val_no_iva_envio =  Tools::ps_round( $order_tot->total_shipping / 1.16 ,3);
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveProdServ'] = '01010101';
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['NoIdentificacion'] = '1';
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Cantidad'] = number_format( 1, 6, '.', '');
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ClaveUnidad'] = 'H87';
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Unidad'] = 'Pieza';
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Descripcion'] = "FLETE ENVIO";
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['ValorUnitario'] = number_format( $val_no_iva_envio, 2, '.', '');
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Importe'] = Tools::ps_round( $val_no_iva_envio ,2);
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Base'] = number_format( $val_no_iva_envio, 2, '.', '');
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Impuesto'] = "002";
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TipoFactor'] = "Tasa";
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['TasaOCuota'] = "0.160000";
					$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][0]['@attributes']['Importe'] = number_format(($val_no_iva_envio*16)/100, 2, '.', '');
					$totalImportes += number_format(($val_no_iva_envio*16)/100, 2, '.', '');
					$order_tot->total_products += Tools::ps_round( $val_no_iva_envio ,2);
				}

				/**
				 *  si no existe impuesto, crea una regla con valor 0 cero
				 */

				if ( $order_tot->id == $orden_validar  ) { 
					foreach ($array_ivas as $key => $value) {
						echo "<br> 1. tax: ".$key."  - - - Value : ".$value;
					}
				}
				//$val_total_de_iva = 0;
				ksort($array_ivas);

				if ( $order_tot->id == $orden_validar  ) { 
					foreach ($array_ivas as $key => $value) {
						echo "<br> 2. tax: ".$key."  - - - Value : ".$value;
					}
				}

				//foreach ($array_ivas as $key => $value) {
					$order_tot->total_paid = $order_tot->total_products+$totalImportes;
				//}

				$arr_xml_cargar['@attributes']['xmlns_cfdi'] = 'http://www.sat.gob.mx/cfd/3';
				$arr_xml_cargar['@attributes']['xmlns_xsi'] = 'http://www.w3.org/2001/XMLSchema-instance';
				$arr_xml_cargar['@attributes']['xsi_schemaLocation'] = 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd';
				$arr_xml_cargar['@attributes']['Version'] = '3.3';
				$arr_xml_cargar['@attributes']['Fecha'] = date("Y-m-d")."T".date("H:i:s",  strtotime ( '-10 minute' , strtotime ( date("H:i:s") ) ));//'2015-01-14T15:57:16';
				$arr_xml_cargar['@attributes']['TipoDeComprobante'] = 'I'; //ingreso
				$metodo_pago_numero = explode('(',explode(')',explode('-', $metodo_pago)[0])[0]);
				$arr_xml_cargar['@attributes']['FormaPago'] = (isset($metodo_pago_numero[1]))?$metodo_pago_numero[1]:$metodo_pago_numero[0];
				//$arr_xml_cargar['@attributes']['serie'] = 'C';
				//$arr_xml_cargar['@attributes']['folio'] = '2000';
				$arr_xml_cargar['@attributes']['MetodoPago'] = 'PUE';//PAGO EN UNA SOLA EXHIBICION
				//$arr_xml_cargar['@attributes']['condicionesDePago'] = 'Parcialidades';
				$arr_xml_cargar['@attributes']['Sello'] = '';
				$arr_xml_cargar['@attributes']['NoCertificado'] = $this->numero_certificado;
				$arr_xml_cargar['@attributes']['Certificado'] = str_replace(array('\n', '\r'), '', base64_encode(file_get_contents($this->dir_server.$this->archivo_cer)));
                
				if ($order_tot->total_discounts != null || $order_tot->total_discounts != 0) {
					$arr_xml_cargar['@attributes']['Descuento'] = number_format( $order_tot->total_discounts , 2, '.', '');
					$arr_xml_cargar['@attributes']['SubTotal'] = $order_tot->total_products;
					$arr_xml_cargar['@attributes']['Total'] = number_format( $order_tot->total_paid-$order_tot->total_discounts , 2, '.', '');
				}else{
					$arr_xml_cargar['@attributes']['Descuento'] = '0.00';
					$arr_xml_cargar['@attributes']['SubTotal'] = $order_tot->total_products;
					$arr_xml_cargar['@attributes']['Total'] = number_format( $order_tot->total_paid , 2, '.', '');
				}
				
				$arr_xml_cargar['@attributes']['Moneda'] = 'MXN'; //codigo postal
				$arr_xml_cargar['@attributes']['LugarExpedicion'] = '11870'; //codigo postal
				
				//se insertan los descuentos según el porcentaje del valor de cada producto por aparte
				$cant_prods = 0;
				foreach ( $list_products as $key_prod => $value ) {
					if( $list_products[$key_prod]['unit_price_tax_excl'] != 0 ){
						$base = number_format( $list_products[$key_prod]['total_price_tax_excl'], 2, '.', '' );
						$conceptoTemporal = $arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods];
						$infoConceptos .= $conceptoTemporal['@attributes']['ClaveProdServ']."|".$conceptoTemporal['@attributes']['NoIdentificacion']."|".$conceptoTemporal['@attributes']['Cantidad']."|H87|Pieza|".$conceptoTemporal['@attributes']['Descripcion']."|".$conceptoTemporal['@attributes']['ValorUnitario']."|".$conceptoTemporal['@attributes']['Importe']."|";
						if ( $order_tot->total_discounts != null || $order_tot->total_discounts != 0 ) {
							$descuentoConcepto = number_format( $order_tot->total_discounts*( $base/$arr_xml_cargar['@attributes']['SubTotal'] ), 2, '.', '' );
							$totalDescuentoConceptos += $descuentoConcepto;
							if ( $cant_prods == ( $total_productos-1 ) ) {
								$descuentoConcepto = number_format( $descuentoConcepto, 2, '.', '' )-( $totalDescuentoConceptos-$order_tot->total_discounts );
							}
							$arr_xml_cargar_p['ar6to67be_Conceptos']['ar6to67be_Concepto'][$cant_prods]['@attributes']['Descuento'] = number_format( $descuentoConcepto, 2, '.', '' );
							$infoConceptos .= number_format( $descuentoConcepto, 2, '.', '' )."|";
						}
						$infoConceptos .= $conceptoTemporal['@attributes']['Base']."|002|Tasa|".$conceptoTemporal['@attributes']['TasaOCuota']."|".['@attributes']['Importe']."|";
						$cant_prods++;
					}
				}
				
				$arr_xml_cargar['ar6to67be_Emisor']['@attributes']['Rfc'] = $this->RFCEmisor;
				$arr_xml_cargar['ar6to67be_Emisor']['@attributes']['Nombre'] = $this->RFCEmisor_nombre;
				$arr_xml_cargar['ar6to67be_Emisor']['@attributes']['RegimenFiscal'] = '601';
				
				
				
				/********     ASIGNAR    RFC    DE    COMPRA    CON    FACTURA   is_rfc  ********/
				
				$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['UsoCFDI'] = 'G01';       
				$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Rfc'] = 'XAXX010101000';
				$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = 'MOSTRADOR';
				
				
				if ( $this->stripAccents( $invoice_address->firstname ) != '' && $this->stripAccents( $invoice_address->firstname ) != null ) {
					
					$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] =  $this->trim_all($this->stripAccents( $invoice_address->firstname )); //'Nombre del Receptor';
					
				}
				
				if (  $this->stripAccents( $invoice_address->lastname ) != '' &&  $this->stripAccents( $invoice_address->lastname ) != null ) {
					
					if ( $arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] != '' ) {
						
						$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] .= " ". $this->trim_all( $this->stripAccents( $invoice_address->lastname )); //'Nombre del Receptor';
						
					} else {
						
						$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] =  $this->trim_all( $this->stripAccents( $invoice_address->lastname )); //'Nombre del Receptor';
						
					}
				}
				
				/***************************************************** CAMBIO PARA RFC ******************************************************************/
				
				
				if ( isset( $invoice_address->id_customer ) && $invoice_address->id_customer != '' && $invoice_address->id_customer != null  ) {						
					
					$query = new DbQuery();
					$query->select(' is_rfc, dni, alias, address1, cpp.nombre AS colonia_name, address2, postcode, firstname, lastname ');
					$query->from('address', 'a');
					$query->leftJoin('cod_postal', 'cpp', 'cpp.id_codigo_postal = a.id_colonia' );
					$query->where(' a.id_customer = '.$invoice_address->id_customer. ' AND a.is_rfc = 1' );
					$query->limit('1');
					
					if ( $dir_factura = Db::getInstance()->executeS($query) ) {
						
						/********     ASIGNAR    RFC    DE    COMPRA    CON    FACTURA   is_rfc  ********/
						
						if ( isset( $dir_factura[0]['is_rfc'] ) && $dir_factura[0]['is_rfc'] == 1 ) {
							
							$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Rfc'] = strtoupper( $dir_factura[0]['dni'] );
							
							$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = 'MOSTRADOR';
							
							
							if (  $this->stripAccents( $dir_factura[0]['alias'] ) != '' &&  $this->stripAccents( $dir_factura[0]['alias'] ) != null ) {
								$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre'] = $this->trim_all( $this->stripAccents( $dir_factura[0]['alias'] ));
							}
							
						}							
					}
				}
				
				/********     ASIGNAR    RFC    DE    COMPRA    CON    FACTURA     ********/
				
				$arr_xml_cargar =  array_merge($arr_xml_cargar,$arr_xml_cargar_p); // UNIMOS ARRAY DE PRODUCTOS Y EL INICIAL DEL XML
				$arr_xml_cargar['ar6to67be_Impuestos']['@attributes']['TotalImpuestosRetenidos'] = '0.00';
				$arr_xml_cargar['ar6to67be_Impuestos']['@attributes']['TotalImpuestosTrasladados'] = $totalImportes;
				
				$cant_taxs = 0;
				$infoTaxes = "";
				
				foreach ($array_ivas as $key => $value) {
					if($value > 0){
						$arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['Impuesto'] = '002';
						$arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['TipoFactor'] = 'Tasa';
						$arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['TasaOCuota'] = number_format($key/100, 6, '.', '');
						$arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['Importe'] = $totalImportes;
						$infoTaxes .= "002|Tasa|".$arr_xml_cargar['ar6to67be_Impuestos']['ar6to67be_Traslados']['ar6to67be_Traslado'][$cant_taxs]['@attributes']['TasaOCuota']."|".$totalImportes."|";
						$cant_taxs++;
					}
					
				}
				$this->cadena_original = "||3.3|".$arr_xml_cargar['@attributes']['Fecha']."|".$arr_xml_cargar['@attributes']['FormaPago']."|".$this->numero_certificado."|".$arr_xml_cargar['@attributes']['SubTotal']."|".$arr_xml_cargar['@attributes']['Descuento']."|MXN|".$arr_xml_cargar['@attributes']['Total']."|I|PUE|11870|".$this->RFCEmisor."|".$this->RFCEmisor_nombre."|601|".$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Rfc']."|".$arr_xml_cargar['ar6to67be_Receptor']['@attributes']['Nombre']."|G01|".$infoConceptos."0.00|".$infoTaxes.$totalImportes."||";

				$serie='C';
				$folio='2000';

				$this->array_xml_a_timbrar = $arr_xml_cargar;

				/*********************** INICIO GENERAR EL XML DEL ARRAY *******************************/


							$xml1 = Array2XML::createXML('ar6to67be_Comprobante', $arr_xml_cargar );

			  $buscar = array("&amp;aacute;","&amp;eacute;","&amp;iacute;","&amp;oacute;","&amp;uacute;","&amp;Aacute;","&amp;Eacute;","&amp;Iacute;","&amp;Oacute;","&amp;Uacute;","ar6to67be_", "xmlns_", "xsi_", "a23r4e3r4eee_");

			  $cambiar = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","cfdi:", "xmlns:", "xsi:", "tfd:");

			  $xml214_1 = html_entity_decode( str_replace( $buscar, $cambiar, $xml1->saveXML() ) );

			  $contenido_fichero_1 = $xml214_1;

			  if ( $order_tot->id == $orden_validar  ) {
			  	  echo "<br> antes Sellado emisor:<br><textarea style='height: 574px;  width: 1549px;' style='font-size:13px'>"; echo html_entity_decode( str_replace( array("ar6to67be_", "xmlns_", "xsi_"), array("cfdi:", "xmlns:", "xsi:"), $xml1->saveXML() ) ); echo "</textarea><br><hr>";

			  	  $this->LlavesRegistroTimbrado( $contenido_fichero_1, 1 );
			  }

							$this->LlavesRegistroTimbrado( $contenido_fichero_1 );

							$xml2 = Array2XML::createXML('ar6to67be_Comprobante', $this->array_xml_a_timbrar );

				if ( $order_tot->id == $orden_validar  ) {
					echo "<br> Sellado emisor:<br><textarea style='height: 574px;  width: 1549px;' style='font-size:13px'>"; echo html_entity_decode( str_replace( array("ar6to67be_", "xmlns_", "xsi_"), array("cfdi:", "xmlns:", "xsi:"), $xml2->saveXML() ) ); echo "</textarea><br><hr>";

					exit(" sin timbrar ");
				}
				/*********************** FINAL GENERAR EL XML DEL ARRAY *******************************/

				/******************* INICIO PARA IMPRIMIR EL ARREGLO EN UNA TABLA *****************************/
				/*
				include("show_array.php");
				html_show_array($arr_xml_cargar);
				*/
				/******************* FIN PARA IMPRIMIR EL ARREGLO EN UNA TABLA *****************************/


				/*
				$xml = simplexml_load_string($contenido_fichero);
				$this->simplexml_to_array($xml, $arr);
				var_dump($arr);  
				*/
				//exit();
				

				$buscar = array("&amp;aacute;","&amp;eacute;","&amp;iacute;","&amp;oacute;","&amp;uacute;","&amp;Aacute;","&amp;Eacute;","&amp;Iacute;","&amp;Oacute;","&amp;Uacute;","ar6to67be_", "xmlns_", "xsi_", "a23r4e3r4eee_");

				$cambiar = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","cfdi:", "xmlns:", "xsi:", "tfd:");


				$xml214 =html_entity_decode( str_replace( $buscar, $cambiar, $xml2->saveXML() ) );

				$contenido_fichero = $xml214;

				//$usuario="0000023492";
				//$usuario="FME140730J95";
				$usuario = $this->usuario;
				$proveedor = $this->proveedor;
				$contrasenia = $this->contrasenia;
				$sucursal = $this->sucursal;


				if ( $this->test_mode == true ) {

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
					"MailCCO" => "",
				);
				//d($xml_params);

				//echo "<br><textarea cols=150 rows=20>".str_replace(array("&amp;lt;","&amp;gt;",'&amp;quot;',"&lt;","&gt;",'&quot;'),array("<",">",'"',"<",">",'"') ,$xml_params)."</textarea><br>";
				//exit;
				if ( is_array($valor_timbrado) && $valor_timbrado != 0 && $obligar_timbrado == 0 ) {
					$this->TimbradoNoOk( $order_tot->id, "Timbrado previamente generado ", 1, 0, $contenido_fichero );
					//echo "<br> SIIIIIIIIIIII existe registro en la bd ";	
					return ($valor_timbrado);

				}

				try {

					$result = $soapClient->$metodo($xml_params);
					$resultado = $result->XMLCFDI;
					usleep(800000);
					if ($resultado) {
						$responseXML1= $result->XMLCFDI;
						
						if (!file_exists( $this->dir_server."xml_timbrado/response/".$invoice_date_t )) {
			                mkdir( $this->dir_server."xml_timbrado/response/".$invoice_date_t , 0755, TRUE);
			            }


            			if (!file_exists( $this->dir_server."xml_timbrado/response/".$invoice_date_t.$this->test_char.$order_tot->id."_response.xml" )) {

							$fp1=fopen($this->dir_server."xml_timbrado/response/".$invoice_date_t.$this->test_char.$order_tot->id."_response.xml","x"); //"_".date("Y-m-d H:i:s").
							fwrite($fp1,$responseXML1);
							fclose($fp1);

						}
						//$fp1=fopen($this->dir_server."xml_timbrado/response/".$invoice_date_t.$this->test_char.$order_tot->id."_result.xml","x");
						//fwrite($fp1,print_r($result,true));
						//fclose($fp1);		   
					
						/*
						$nombre_fichero = "../xml/response/C-2000-66_response.xml";
						$fichero_texto = fopen ($nombre_fichero, "r");
						$contenido_fichero = fread($fichero_texto, filesize($nombre_fichero));
						*/

						//echo "<hr>Respuesta:<textarea cols=150 rows=20>".$responseXML1."</textarea><br>";
						$p = xml_parser_create();
						$xml = xml_parse_into_struct($p, $responseXML1, $vals, $index);
						xml_parser_free($p);
						//d($index['TFD:TIMBREFISCALDIGITAL'][0]);
						$arr = $vals[$index['TFD:TIMBREFISCALDIGITAL'][0]];
						//$xml = simplexml_load_string($responseXML1);
						//$json = json_encode($xml);
//d($arr);
						// $this->simplexml_to_array($xml, $arr);
						//echo "<br>array rta: <pre>";
						//var_dump($arr['Comprobante']["@attributes"]); 
						//echo "</pre>";
						//exit;

						$arr_xml_cargar['ar6to67be_Complemento']['a23r4e3r4eee_TimbreFiscalDigital']['@attributes']['xmlns_tfd'] = "http://www.sat.gob.mx/TimbreFiscalDigital";
						$arr_xml_cargar['ar6to67be_Complemento']['a23r4e3r4eee_TimbreFiscalDigital']['@attributes']['xsi_schemaLocation'] = "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd";

						foreach ($arr["attributes"] as $key => $value) {

							$arr_xml_cargar['ar6to67be_Complemento']['a23r4e3r4eee_TimbreFiscalDigital']['@attributes'][$key] = $value;

						}


						/************************   INICIO GENERACION XML FULL       **********************************/

						$xml2 = Array2XML::createXML('ar6to67be_Comprobante', $arr_xml_cargar );

						//echo "xml timbrado:<br><textarea style='height: 404px; width: 1339px;' >"; echo html_entity_decode( str_replace( $buscar, $cambiar, $xml2->saveXML() ) ); echo "</textarea><br><hr>";
						/*
						$xml214 =html_entity_decode( str_replace( $buscar, $cambiar, $xml2->saveXML() ) );

						$contenido_fichero = $xml214;
									$contenido_fichero = str_replace("<","&amp;lt;",$contenido_fichero);
									$contenido_fichero = str_replace(">","&amp;gt;",$contenido_fichero);
									$contenido_fichero = str_replace('"','&amp;quot;',$contenido_fichero);
*/

/*

<Resultado><Informacion>
	<Documento Serie="FX" 
			   Folio="7025" 
			   Fecha="2015-01-23T12:57:16" 
			   RFCEmisor="AAA010101AAA" 
			   RFCReceptor="XAXX010101000" 
			   RutaXml="https://fx.facturaxion.com/bovedaCFDI/cfdi/Pruebas/2015/1/23/AAA010101AAA-FX7025-ceb47b61-a456-4d19-8bce-f8e89895c876.xml" 
			   idCfdi="35587244" />
	<Timbre version="1.0" 
			UUID="ceb47b61-a456-4d19-8bce-f8e89895c876" 
			FechaTimbrado="2015-01-23T14:57:18" 
			selloCFD="QKJ4porjBgJvkalKBE4Ym8vISbltWesL9Ra4vQUxAEsQyDqF5+2No5dioIpulBB+NsHt3e6BW2ZjblaTnZQEZGXXVL+jw7P9eXriSWu49hbNyeW0hYhDz/jyxtyXE0h2l1s0H/dkFamlk0flUkA8ub+S5HfOIfFKkfH1sROgrjk=" 
			noCertificadoSAT="00001000000203092957" 
			selloSAT="h5rp7wfu+rgv9QKsCUj5VqRRhX+glAs3X7YoibhyGl27hKYTRYZrCcIlZNH5FJkpMuvoRWhybjn2w/5RfB7JyE8jESvLMVnwK6ozvWLxjJeZXl15J8v8SpBTu23FBXbMqxFKIdHePMyzvtld9D/iVI0yYx/fOEgqvucxjCQ8PxU=" />


 

array(6) {
  ["version"]=>
  string(3) "1.0"
  ["UUID"]=>
  string(36) "ceb47b61-a456-4d19-8bce-f8e89895c876"
  ["FechaTimbrado"]=>
  string(19) "2015-01-23T14:57:18"
  ["selloCFD"]=>
  string(172) "QKJ4porjBgJvkalKBE4Ym8vISbltWesL9Ra4vQUxAEsQyDqF5+2No5dioIpulBB+NsHt3e6BW2ZjblaTnZQEZGXXVL+jw7P9eXriSWu49hbNyeW0hYhDz/jyxtyXE0h2l1s0H/dkFamlk0flUkA8ub+S5HfOIfFKkfH1sROgrjk="
  ["noCertificadoSAT"]=>
  string(20) "00001000000203092957"
  ["selloSAT"]=>
  string(172) "h5rp7wfu+rgv9QKsCUj5VqRRhX+glAs3X7YoibhyGl27hKYTRYZrCcIlZNH5FJkpMuvoRWhybjn2w/5RfB7JyE8jESvLMVnwK6ozvWLxjJeZXl15J8v8SpBTu23FBXbMqxFKIdHePMyzvtld9D/iVI0yYx/fOEgqvucxjCQ8PxU="
}

						*/

						//d($arr["Comprobante"]["@attributes"]);
						$this->TimbradoOk( $order_tot->id, $arr["attributes"], $result->RutaPDF);
						
						return ($arr["attributes"]);
				   
					} else {

						//echo "Ocurrio el siguiente error al momento de timbrar la factura, contacte a su administrador: ";
				   
						$responseXML= $result->MensajeValidacion;
						// $pieces = explode("<Errores><", $responseXML);
						// $sedunda= $pieces[1];
						// $sedunda= str_replace("/></Errores></Informacion></Resultado></Timbrado>","&lt;",$sedunda);
						//echo $sedunda;
						//$this->TimbradoNoOk( $order_tot->id, $arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"], $arr['Timbrado']['Resultado']["Informacion"]["Documento"]["@attributes"] );
						$this->TimbradoNoOk( $order_tot->id, $responseXML, 1, 0, $contenido_fichero );
						//return $sedunda;

					}

				} catch(Exception $e) {
					$message = $e->getMessage(); echo $message; 
				}
				
			} else {	

				if ( is_array($valor_timbrado) && $valor_timbrado != 0 ) {

					//echo "<br> SIIIIIIIIIIII existe registro en la bd ";	
					return ($valor_timbrado);

				} 


				if ( file_exists($xml_previamente_generado) ) {

		  			//echo "<br> SIIIIIIIIIIII existe archivo XML de timbrado ";			
					
					$fichero_texto = fopen ($xml_previamente_generado, "r");
					$contenido_fichero = fread($fichero_texto, filesize($xml_previamente_generado));

					//echo "<hr>Respuesta:<textarea cols=150 rows=20>".$responseXML1."</textarea><br>";
					$xml = simplexml_load_string($contenido_fichero);
					//$json = json_encode($xml);

					$this->simplexml_to_array($xml, $arr);
					$arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"]["RutaXml"] = $arr['Timbrado']['Resultado']["Informacion"]["Documento"]["@attributes"]["RutaXml"];

					return ($arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"]);
				}
			}
			

		}











/**
 * [cancelacion Método usado para cancelar el timbrado de la factura]
 * @param  [Object] $order_tot [Listado de todas las características de la orden]
 * @return [type]            [description]
 */
public function cancelacion( $order_tot_id, $order_invoice_date ) {

			//echo "<textarea cols=150 rows=20>"./*str_replace(array("&amp;lt;","&amp;gt;",'&amp;quot;',"&lt;","&gt;",'&quot;'),array("<",">",'"',"<",">",'"') ,*/$xml_params/*)*/."</textarea><br>";
			//echo "<textarea cols=150 rows=50>".utf8_decode($xml_params)."</textarea><br>";

	//$usuario="0000023492";
				//$usuario="FME140730J95";
				$usuario = $this->usuario;
				$proveedor = $this->proveedor;
				//$sucursal="961549";
				$sucursal = $this->sucursal;
				$RFCEmisor  = $this->RFCEmisor;

			if ( isset($order_invoice_date) && strlen($order_invoice_date) >= 10 ) { 
				$invoice_date_t = substr( $order_invoice_date, 0, 10)."/";
			} else {
				$invoice_date_t = "";
			}

			$xml_previamente_timbrado = $this->dir_server."xml_timbrado/response/".$invoice_date_t.$this->test_char.$order_tot_id."_response.xml";

			$valor_timbrado = $this->RegistroTimbrado( $order_tot_id , 1);

			$xml_previamente_cancelado = $this->dir_server."xml_timbrado/cancelacion/".$invoice_date_t.$this->test_char.$order_tot_id."_response.xml";

			$valor_cancelado = $this->RegistroTimbrado( $order_tot_id , 1, 1);

			//echo "<br> <pre> orden cancelar datos : ";
			//print_r($valor_timbrado);
			//echo "<br></pre> ";
			

			if ( ( is_array($valor_timbrado) || file_exists($xml_previamente_timbrado) ) &&  $valor_cancelado == 0 && !file_exists($xml_previamente_cancelado) ) {

				/****************  envio $this->order->total_shipping_tax_incl   *//////////

				$arr_xml_cargar['@attributes']['Version'] = '1.0';
				$arr_xml_cargar['@attributes']['CodigoUsuarioProveedor'] = $proveedor;//'N#@Mo!)#oh&gt;)BYOdX=q_ZUCsLxqpv?';
				$arr_xml_cargar['@attributes']['CodigoUsuario'] = $usuario;//'1763AAB0593430490B3B3EE5457A9A2580F9D7DE';				
				$arr_xml_cargar['@attributes']['RFCEmisor'] = $RFCEmisor;
				//$arr_xml_cargar['@attributes']['serie'] = 'C';
				//$arr_xml_cargar['@attributes']['folio'] = '2000';
				
				$databd = 0; //datos existentes en la base de datos
				if ( is_array($valor_timbrado)  ) {

					$arr_xml_cargar['Folios']['UUID'] = $valor_timbrado['uuid']; //date("Y-m-d")."T".date("H:i:s",strtotime("-10 minute"));//'2015-01-14T15:57:16';
					$databd = 1;

				} elseif (file_exists($xml_previamente_timbrado)) {

					$fichero_texto = fopen ($xml_previamente_timbrado, "r");
					$contenido_fichero = fread($fichero_texto, filesize($xml_previamente_timbrado));                

					//echo "<hr>Respuesta:<textarea cols=150 rows=20>".$responseXML1."</textarea><br>";
					$xml = simplexml_load_string($contenido_fichero);
					//$json = json_encode($xml);

					$this->simplexml_to_array($xml, $arr);
					$arrkeylower = array_change_key_case($arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"], CASE_LOWER);

					$arr_xml_cargar['Folios']['UUID'] = $arrkeylower['uuid']; //date("Y-m-d")."T".date("H:i:s",strtotime("-10 minute"));//'2015-01-14T15:57:16';
				}
				

				$serie='C';
				$folio='2000';

				/*********************** INICIO GENERAR EL XML DEL ARRAY *******************************/


							$xml2 = Array2XML::createXML('Parametros', $arr_xml_cargar );

				//echo "<textarea style='height: 150px; width: 1339px;' >"; echo html_entity_decode( str_replace( array("ar6to67be_", "xmlns_", "xsi_"), array("cfdi:", "xmlns:", "xsi:"), $xml2->saveXML() ) ); echo "</textarea><br><hr>";


				/*********************** FINAL GENERAR EL XML DEL ARRAY *******************************/

				/******************* INICIO PARA IMPRIMIR EL ARREGLO EN UNA TABLA *****************************/
				/*
				include("show_array.php");
				html_show_array($arr_xml_cargar);
				*/
				/******************* FIN PARA IMPRIMIR EL ARREGLO EN UNA TABLA *****************************/


				/*
				$xml = simplexml_load_string($contenido_fichero);
				$this->simplexml_to_array($xml, $arr);
				var_dump($arr);  
				*/
				//exit();



				$buscar = array("&amp;aacute;","&amp;eacute;","&amp;iacute;","&amp;oacute;","&amp;uacute;","&amp;Aacute;","&amp;Eacute;","&amp;Iacute;","&amp;Oacute;","&amp;Uacute;","ar6to67be_", "xmlns_", "xsi_", "a23r4e3r4eee_");

				$cambiar = array("&aacute;","&eacute;","&iacute;","&oacute;","&uacute;","&Aacute;","&Eacute;","&Iacute;","&Oacute;","&Uacute;","cfdi:", "xmlns:", "xsi:", "tfd:");
				//$cambiar = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","cfdi:", "xmlns:", "xsi:");


				$xml214 =html_entity_decode( str_replace( $buscar, $cambiar, $xml2->saveXML() ) );
				
				$contenido_fichero = str_replace("<","&lt;",$xml214);
				$contenido_fichero = str_replace(">","&gt;",$contenido_fichero);
				$contenido_fichero = str_replace('"','&quot;',$contenido_fichero);


				$soapClient = new SoapClient("https://timbre02.facturaxion.net/CFDI.asmx?WSDL");

				if ( $this->test_mode == true ) {

					$metodo = "ReportarCancelacionPrueba";
					$TextoXml='"'.$contenido_fichero.'"&lt;/Parametros&gt;';

					$xml_params = '<ReportarCancelacionPrueba xmlns="http://www.facturaxion.com/">
					<parametros>'.$contenido_fichero.'</parametros>
					</ReportarCancelacionPrueba>';
				
				} else {

					$metodo = "ReportarCancelacion";
					$TextoXml='"'.$contenido_fichero.'"&lt;/Parametros&gt;';

					$xml_params = '<ReportarCancelacion xmlns="http://www.facturaxion.com/">
					<parametros>'.$contenido_fichero.'</parametros>
					</ReportarCancelacion>';

				}

				

				//echo "<textarea style='height: 200px; width: 1339px;' >"; echo  $xml_params ; echo "</textarea><br><hr>";

				try {

					$soapVar = new SoapVar($xml_params, XSD_ANYXML, null, null, null); 

				} catch(Exception $e){

					$message = $e->getMessage();//echo $message;
					$fp_cfdi=fopen("/home/desarrollo/modules/facturaxion/cancelacion_".date("Y-m-d")."T".date("H:i:s",  strtotime ( '-10 minute' , strtotime ( date("H:i:s") ) )).$this->test_char."_cfdi.xml","a+"); //"_".date("Y-m-d H:i:s").
						fwrite($fp_cfdi,$message);
						fclose($fp_cfdi);
				} 

				try {

					$result = $soapClient->$metodo(new SoapParam($soapVar, $metodo));
					if ( $this->test_mode == true ) {
						$resultado = $result->ReportarCancelacionPruebaResult;
					} else {
						$resultado = $result->ReportarCancelacionResult;
					}
					
					if ($resultado == true) {
						$responseXML1= $result->resultado;
						//echo "<hr>Result: <br><textarea cols=150 rows=20>".print_r($result,true)."</textarea><br>";
						//$alea=rand(1,100);
						
						if (!file_exists( $this->dir_server."xml_timbrado/cancelacion/".$invoice_date_t )) {
			                mkdir( $this->dir_server."xml_timbrado/cancelacion/".$invoice_date_t , 0755, TRUE);
			            }

						$fp1=fopen($this->dir_server."xml_timbrado/cancelacion/".$invoice_date_t.$this->test_char.$order_tot_id."_response.xml","x"); //"_".date("Y-m-d H:i:s").
						fwrite($fp1,$responseXML1);
						fclose($fp1);
						//$fp1=fopen($this->dir_server."xml_timbrado/cancelacion/".$invoice_date_t.$this->test_char.$order_tot_id."_result.xml","x");
						//fwrite($fp1,print_r($result,true));
						//fclose($fp1);
						
						if ( $databd == 0) {
							$this->TimbradoOk( $order_tot_id, $arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"], $arr['Timbrado']['Resultado']["Informacion"]["Documento"]["@attributes"]);
							$valor_timbrado = $this->RegistroTimbrado( $order_tot_id , 1);
							$this->CanceladoOk( $valor_timbrado['id_timbrado'] );

						} else {

							$this->CanceladoOk( $valor_timbrado['id_timbrado'] );
						}

						return true;
				   
					} else {

						//echo "Ocurrio el siguiente error al momento de cancelar la factura, contacte a su administrador: ";
				   
						$responseXML= $result->resultado;
						$pieces = explode("<Errores><", $responseXML);
						$sedunda= $pieces[1];
						$sedunda= str_replace("/></Errores></Informacion></Resultado></Timbrado>","&lt;",$sedunda);
						$sedunda= str_replace("</Errores></Resultado>","",$sedunda);
						
						//echo $sedunda;

						$this->CanceladoNoOk( $order_tot_id, $sedunda, 0, 1 );


					}

				} catch(Exception $e) {
					$message = $e->getMessage();  echo $message; 
				}
			
			} else {

				if ( is_array($valor_timbrado) && $valor_timbrado != 0 ) {

					//echo "<br> SIIIIIIIIIIII existe registro en la bd ";	
					return ($valor_timbrado);

				} 

				if ( file_exists($xml_previamente_cancelado) ) {	
					
					$fichero_texto = fopen ($xml_previamente_cancelado, "r");
					$contenido_fichero = fread($fichero_texto, filesize($xml_previamente_cancelado));                

					//echo "<hr>Respuesta:<textarea cols=150 rows=20>".$responseXML1."</textarea><br>";
					$xml = simplexml_load_string($contenido_fichero);
					$json = json_encode($xml);

					$this->simplexml_to_array($xml, $arr);
					
					return ($arr['Timbrado']['Resultado']["Informacion"]["Timbre"]["@attributes"]);
				}
			}
	
		}















		public function TimbradoOk( $id_order, array $arr_timbre, $arr_documento ) {

			//echo "<hr> timbradook<br>";
			$query = new DbQuery();
			$query->select('count(t.id_order) AS cant');
			$query->from('timbrado', 't');
			//$query->innerJoin('order_invoice_payment', 'oip2',
			//	'oip2.id_order_payment = oip1.id_order_payment AND oip2.id_order_invoice <> oip1.id_order_invoice');
			$and = '';
			if ( $this->test_mode == true ) {
				$and = ' AND t.prueba = 1 ';
			} else {
				$and = ' AND t.prueba = 0 ';
			}

			$query->where('t.id_order = '.$id_order.$and);
			
			$timbres = Db::getInstance()->executeS($query);
			//echo "<pre>timbre: ";
			//if ($timbres[0]['cant'] == 0 ){ echo "0 <br>"; }else{ echo "1<br>"; }
			//print_r($timbres);
			//echo "<pre>";
			

			if ($timbres[0]['cant'] == 0) {
				//echo "<br> para registrar."	;

				$is_correct = true;

				if ( $this->test_mode == true ) {

					$sql = 'INSERT INTO `'._DB_PREFIX_.'timbrado` (`id_order`, `version`, `uuid`, `fechatimbrado`, `sellocfd`, `nocertificadosat`, `sellosat`, `timbrado`, `cancelado`, `rutaxml`, `fecha`, `prueba`)
						VALUES ("'.$id_order.'", "'.$arr_timbre['VERSION'].'", "'.$arr_timbre['UUID'].'", "'.$arr_timbre['FECHATIMBRADO'].'", "'.$arr_timbre['SELLOCFD'].'", "'.$arr_timbre['NOCERTIFICADOSAT'].'", "'.$arr_timbre['SELLOSAT'].'", 1, 0, "'.$arr_documento.'", NOW(), 1 )';

				} else {

					$sql = 'INSERT INTO `'._DB_PREFIX_.'timbrado` (`id_order`, `version`, `uuid`, `fechatimbrado`, `sellocfd`, `nocertificadosat`, `sellosat`, `timbrado`, `cancelado`, `rutaxml`, `fecha`)
						VALUES ("'.$id_order.'", "'.$arr_timbre['VERSION'].'", "'.$arr_timbre['UUID'].'", "'.$arr_timbre['FECHATIMBRADO'].'", "'.$arr_timbre['SELLOCFD'].'", "'.$arr_timbre['NOCERTIFICADOSAT'].'", "'.$arr_timbre['SELLOSAT'].'", 1, 0, "'.$arr_documento.'", NOW() )';

				}

				//echo $sql;

				$is_correct = Db::getInstance()->execute($sql);
				//print_r($is_correct);

				//echo "<br> ya registrado." ;

			} else {
				$is_correct = false;
				echo "<br> no se registro." ;
			}
			//exit();
			return $is_correct;
		}





		public function TimbradoNoOk( $id_order, $mensaje, $timbrado, $cancelado, $xml='' ) {

			//echo "<hr> timbradoNOok<br>";
			

				$is_correct = true;

				if ( $this->test_mode == true ) {

					$sql = 'INSERT INTO `'._DB_PREFIX_.'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`, `prueba`, `xml` )
						VALUES ("'.$id_order.'", \''.htmlentities($mensaje, ENT_QUOTES).'\', '.$timbrado.', '.$cancelado.', NOW(), 1, \''.$xml.'\' )';

				} else {

					$sql = 'INSERT INTO `'._DB_PREFIX_.'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`, `xml`)
						VALUES ("'.$id_order.'", \''.htmlentities($mensaje, ENT_QUOTES).'\', '.$timbrado.', '.$cancelado.', NOW(), \''.$xml.'\'  )';

				}

				//echo $sql;

				$is_correct = Db::getInstance()->execute($sql);
				//echo "<br>is_correct:--".$is_correct."--";
				//print_r($is_correct);

				//echo "<br> registrado." ;

					
			return $is_correct;
		}



		public function RegistroTimbrado( $id_order, $timb = 1, $canc = 0 ) {

			//echo "<hr> RegistroTimbrado<br>";
			$orden_validar_no_cancelada = Configuration::get('FACTURAXION_VALIDAR_NO_CANCELADA');

			$query = new DbQuery();
			$query->select('*');
			$query->from('timbrado', 't');
			//$query->innerJoin('order_invoice_payment', 'oip2',
			//	'oip2.id_order_payment = oip1.id_order_payment AND oip2.id_order_invoice <> oip1.id_order_invoice');
			$and = '';

			if ( $this->test_mode == true ) {
				$and = ' AND t.prueba = 1 ';
			} else {
				$and = ' AND t.prueba = 0 ';
			}

			if ( $orden_validar_no_cancelada == 1 && $canc == 0 ) {
				$canc = 1;
			}

			$and .= " AND t.timbrado = ".$timb." AND t.cancelado = ".$canc;

			$query->where('t.id_order = '.$id_order.$and);
			$timbres = Db::getInstance()->executeS($query);
			//print_r($query->__ToString());
			//echo "<hr>";
			if (isset($timbres[0]) && count($timbres[0] ) > 0 ) {
				return $timbres[0];
			} else { 
				return (0);
			}					
			
		}



		public function CanceladoOk( $id_timbrado ) {

			//echo "<hr> CanceladoOk<br>";
			

			if ( $id_timbrado != '' && $id_timbrado != null && $id_timbrado != 0) {
				//echo "<br> para registro cancelar."	;

				$is_correct = true;

				if ( $this->test_mode == true ) {

					$is_correct = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'timbrado` SET `cancelado` = 1 WHERE id_timbrado = '.$id_timbrado.' AND prueba = 1');

				} else {

					$is_correct = Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'timbrado` SET `cancelado` = 1 WHERE id_timbrado = '.$id_timbrado);

				}

				//echo "<br> ya registrado." ;

			} else {
				$is_correct = false;
				//echo "<br> no se registro." ;
			}
		
			return $is_correct;
		}





		public function CanceladoNoOk( $id_order, $mensaje, $timbrado = 0, $cancelado = 1 ) {

			//echo "<hr> CanceladoNoOk<br>";
			

				$is_correct = true;

				if ( $this->test_mode == true ) {

					$sql = 'INSERT INTO `'._DB_PREFIX_.'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`, `prueba`)
						VALUES ("'.$id_order.'", \''.htmlentities($mensaje, ENT_QUOTES).'\', '.$timbrado.', '.$cancelado.', NOW(), 1 )';

				} else {

					$sql = 'INSERT INTO `'._DB_PREFIX_.'timbrado_error` (`id_order`, `mensaje`, `timbrado`, `cancelado`, `fecha`)
						VALUES ("'.$id_order.'", \''.htmlentities($mensaje, ENT_QUOTES).'\', '.$timbrado.', '.$cancelado.', NOW() )';

				}

				//echo $sql;
				$is_correct = Db::getInstance()->execute($sql);
				/*echo "<br>is_correct:--".$is_correct."--";
				print_r($is_correct);

				echo "<br> registrado." ;*/

			return $is_correct;
		}




	}



}

?>




