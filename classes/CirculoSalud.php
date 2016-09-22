<?php

/**
* 
*/
class CirculoSalud{

	public $debug_mode;
	private $Usuario = "FarmaListo";
	private $Contraseña = "1WOC1DZA";
	private $Id_Cadena = "0000000238";
	private $Id_Sucursal = "0000000001";
	private $URL = "http://189.202.202.107/nadro_pruebas/WSCirculodelaSalud.asmx";
	private $session_load = '';
	public $error = array();

	public $_ActivateCard;                       /****-----*****/
	public $_ActivateCardUser;                   /***RAPIDO*****/
	public $_Create_Sales;                       /**************/
	public $_Create_Sales_Folio_Receta;          /**CON RECETA**/
	public $_GetBonusProduct;
	public $_GetBonusProductList;                /**************/
	public $_GetInfoCard;                        /**************/
	public $_GetInfoDevolucionProductos;
	public $_GetInfoPatient;
	public $_GetInfoProduct;
	public $_GetInfoProductCard;
	public $_GetPatientPoints;
	public $_GetPromocionesEspecialesVigentes;
	public $_GetReposicionesCadena;
	public $_GetReposicionesSucursal;
	public $_GetStatementPatientPoints;
	public $_Login;                              /**************/
	public $_Logout;
	public $_TestConnect;
	public $_UpdatePatient;
	public $_getVersion;

	function __construct() {
		$this->debug_mode = true;
	}

	private function WebService($metodo, $parametros) {

		$soapUrl = $this->URL."?WSDL"; // asmx URL of WSDL

        // xml post structure

        echo ($this->debug_mode == true ) ? "<br><pre>head: ":'';
		$headers = array(
                "Content-type: text/xml;charset=\"utf-8\"",
                "SOAPAction: http://tempuri.org/".$metodo, 
                "Content-length: ".strlen($parametros),
            );
		
		($this->debug_mode == true ) ? print_r($headers): print_r('');		
		echo ($this->debug_mode == true ) ? "<br></pre> ":'';


        $xml_post_string = $parametros;/*'<?xml version="1.0" encoding="utf-8"?>
                            <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                              <soap:Body>
                                <GetItemPrice xmlns="http://connecting.website.com/WSDL_Service"> 
                                  <PRICE>'.$dataFromTheForm.'</PRICE> 
                                </GetItemPrice >
                              </soap:Body>
                            </soap:Envelope>';   // data from the form, e.g. some ID number
*/
            //SOAPAction: your op URL

            $url = $soapUrl;

            // PHP cURL  for https connection with auth
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // converting
            $response = curl_exec($ch); 
            curl_close($ch);

            // converting
            $response1 = str_replace("<soap:Body>","",$response);
            $response2 = str_replace("</soap:Body>","",$response1);

            // convertingc to XML
            return $parser = simplexml_load_string($response2);
	}


		public function LoadMethod($metodo) {
			$xml_params = $this->$metodo();
			print_r($this->WebService($metodo, $xml_params));
		}

		public function Login() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <Login xmlns="http://tempuri.org/">
      <Usuario>'.$this->Usuario.'</Usuario>
      <Password>'.$this->Contraseña.'</Password>
      <CodigoCadena>'.$this->Id_Cadena.'</CodigoCadena>
      <CodigoSucursal>'.$this->Id_Sucursal.'</CodigoSucursal>
    </Login>
  </soap:Body>
</soap:Envelope>';


			$parser = $this->WebService(__FUNCTION__, $xml_method);
			echo ($this->debug_mode == true ) ?  "<br><pre> Function: ".__FUNCTION__: '';
			($this->debug_mode == true ) ? print_r($parser): print_r('');
			echo ($this->debug_mode == true ) ?  "<br></pre> Function " : '';


			//print_r ($this->debug_mode == true ) ?  print_r($parser) : print_r('');
            // user $parser to get your data out of XML response and to display it.
            
            if ( $parser->LoginResponse->LoginResult->HuboError == 'true' ) {
            	
            	$this->error[] = 'Ocurrio un error al momento de Loguearse en la plaraforma. '.$parser->LoginResponse->LoginResult->MensajeError;
            	($this->debug_mode == true ) ?  print_r($this->error) : print_r('');
            	return false;

            } else {
            	
            	$this->session_load = $parser->LoginResponse->LoginResult->Sesion;
            	return true;

            }

		}

		
		public function ActivateCard( $NoTarjeta, $Nombre, $ApellidoPaterno, $ApellidoMaterno, $Telefono, $Email ) {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ActivateCard xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>'.$NoTarjeta.'</NoTarjeta>
      <Nombre>'.$Nombre.'</Nombre>
      <ApellidoPaterno>'.$ApellidoPaterno.'</ApellidoPaterno>
      <ApellidoMaterno>'.$ApellidoMaterno.'</ApellidoMaterno>
      <Telefono>'.$Telefono.'</Telefono>
      <Email>'.$Email.'</Email>
    </ActivateCard>
  </soap:Body>
</soap:Envelope>';

			     $parser = $this->WebService(__FUNCTION__, $xml_method);

      echo ($this->debug_mode == true ) ?  "<br><pre> Function: ".__FUNCTION__: '';
      ($this->debug_mode == true ) ? print_r($parser): print_r('');
      echo ($this->debug_mode == true ) ?  "<br></pre> Function " : '';


      ($this->debug_mode == true ) ?  print_r($parser) : print_r('');
            // user $parser to get your data out of XML response and to display it.
            
            if ( $parser->ActivateCardResponse->ActivateCardResult->HuboError == 'true' ) {
              
              $this->error[] = 'Ocurrio un error al momento de activar la tarjeta. '.$parser->ActivateCardResponse->ActivateCardResult->MensajeError;
              ($this->debug_mode == true ) ?  print_r($this->error) : print_r('');
              return false;

            } else {
              
              $this->_ActivateCard = $parser->ActivateCardResponse->ActivateCardResult;
              return true;

            }
		}


		public function ActivateCardUser() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <ActivateCardUser xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <Usuario>string</Usuario>
      <NoTarjeta>string</NoTarjeta>
      <Nombre>string</Nombre>
      <ApellidoPaterno>string</ApellidoPaterno>
      <ApellidoMaterno>string</ApellidoMaterno>
      <Telefono>string</Telefono>
      <Email>string</Email>
      <Sexo>string</Sexo>
      <FechaNacimiento>dateTime</FechaNacimiento>
      <CP>string</CP>
    </ActivateCardUser>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function Create_Sales() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <Create_Sales xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <Pedido>
        <NoTarjeta>string</NoTarjeta>
        <CedulaProfesionalMedico>string</CedulaProfesionalMedico>
        <Usuario>string</Usuario>
        <Total>decimal</Total>
        <NoTicket>string</NoTicket>
      </Pedido>
      <Articulos>
        <PedidoArticulos>
          <Sku>string</Sku>
          <Precio>decimal</Precio>
          <PrecioPOS>decimal</PrecioPOS>
          <PrecioFijo>decimal</PrecioFijo>
          <PorcentajeDescuento>decimal</PorcentajeDescuento>
          <MontoDescuento>decimal</MontoDescuento>
          <PiezasPagadas>long</PiezasPagadas>
          <PiezasGratis>long</PiezasGratis>
          <IVA>decimal</IVA>
          <PorcentajeIVA>double</PorcentajeIVA>
        </PedidoArticulos>
        <PedidoArticulos>
          <Sku>string</Sku>
          <Precio>decimal</Precio>
          <PrecioPOS>decimal</PrecioPOS>
          <PrecioFijo>decimal</PrecioFijo>
          <PorcentajeDescuento>decimal</PorcentajeDescuento>
          <MontoDescuento>decimal</MontoDescuento>
          <PiezasPagadas>long</PiezasPagadas>
          <PiezasGratis>long</PiezasGratis>
          <IVA>decimal</IVA>
          <PorcentajeIVA>double</PorcentajeIVA>
        </PedidoArticulos>
      </Articulos>
    </Create_Sales>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function Create_Sales_Folio_Receta() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <Create_Sales_Folio_Receta xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <Pedido>
        <NoTarjeta>string</NoTarjeta>
        <CedulaProfesionalMedico>string</CedulaProfesionalMedico>
        <Usuario>string</Usuario>
        <Total>decimal</Total>
        <NoTicket>string</NoTicket>
      </Pedido>
      <Articulos>
        <PedidoArticulos>
          <Sku>string</Sku>
          <Precio>decimal</Precio>
          <PrecioPOS>decimal</PrecioPOS>
          <PrecioFijo>decimal</PrecioFijo>
          <PorcentajeDescuento>decimal</PorcentajeDescuento>
          <MontoDescuento>decimal</MontoDescuento>
          <PiezasPagadas>long</PiezasPagadas>
          <PiezasGratis>long</PiezasGratis>
          <IVA>decimal</IVA>
          <PorcentajeIVA>double</PorcentajeIVA>
        </PedidoArticulos>
        <PedidoArticulos>
          <Sku>string</Sku>
          <Precio>decimal</Precio>
          <PrecioPOS>decimal</PrecioPOS>
          <PrecioFijo>decimal</PrecioFijo>
          <PorcentajeDescuento>decimal</PorcentajeDescuento>
          <MontoDescuento>decimal</MontoDescuento>
          <PiezasPagadas>long</PiezasPagadas>
          <PiezasGratis>long</PiezasGratis>
          <IVA>decimal</IVA>
          <PorcentajeIVA>double</PorcentajeIVA>
        </PedidoArticulos>
      </Articulos>
      <FolioReceta>string</FolioReceta>
    </Create_Sales_Folio_Receta>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function GetBonusProduct( $NoTarjeta, $Sku, $Piezas ) {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetBonusProduct xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>'.$NoTarjeta.'</NoTarjeta>
      <Sku>'.$Sku.'</Sku>
      <Piezas>'.$Piezas.'</Piezas>
    </GetBonusProduct>
  </soap:Body>
</soap:Envelope>';

			$parser = $this->WebService(__FUNCTION__, $xml_method);

      echo ($this->debug_mode == true ) ?  "<br><pre> Function: ".__FUNCTION__: '';
      ($this->debug_mode == true ) ? print_r($parser): print_r('');
      echo ($this->debug_mode == true ) ?  "<br></pre> Function " : '';


      ($this->debug_mode == true ) ?  print_r($parser) : print_r('');
            // user $parser to get your data out of XML response and to display it.
            
            if ( $parser->GetBonusProductResponse->GetBonusProductResult->HuboError == 'true' ) {
              
              $this->error[] = 'Ocurrio un error al momento de activar la tarjeta. '.$parser->GetBonusProductResponse->GetBonusProductResult->MensajeError;
              ($this->debug_mode == true ) ?  print_r($this->error) : print_r('');
              return false;

            } else {
              
              $this->_GetBonusProduct = $parser->GetBonusProductResponse->GetBonusProductResult;
              return true;

            }
		}


		public function GetBonusProductList($tarjeta, $productos = array() ) {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetBonusProductList xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>'.$tarjeta.'</NoTarjeta>
      <Productos>';
      
	      	foreach ($productos as $numprod => $producto) {
	      		$xml_method .= '
	    <BonusProductList>
          <Sku>'.$producto['referencia'].'</Sku>
          <Piezas>'.$producto['cantidad'].'</Piezas>
        </BonusProductList>';
	      	}
        
        $xml_method .='
      </Productos>
    </GetBonusProductList>
  </soap:Body>
</soap:Envelope>';

			$parser = $this->WebService(__FUNCTION__, $xml_method);

			echo ($this->debug_mode == true ) ?  "<br><pre> Function: ".__FUNCTION__: '';
			($this->debug_mode == true ) ? print_r($parser): print_r('');
			echo ($this->debug_mode == true ) ?  "<br></pre> Function " : '';


			($this->debug_mode == true ) ?  print_r($parser) : print_r('');
            // user $parser to get your data out of XML response and to display it.
            
            if ( $parser->GetBonusProductListResponse->GetBonusProductListResult->HuboError == 'true' ) {
            	
            	$this->error[] = 'Ocurrio un error al momento de obtener el listado de productos. '.$parser->GetBonusProductListResponse->GetBonusProductListResult->MensajeError;
            	($this->debug_mode == true ) ?  print_r($this->error) : print_r('');
            	return false;

            } else {
            	
            	$this->_GetBonusProductList = $parser->GetBonusProductListResponse->GetBonusProductListResult;
            	return true;

            }

		}


		public function GetInfoCard($num_tarjeta) {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetInfoCard xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>'.$num_tarjeta.'</NoTarjeta>
    </GetInfoCard>
  </soap:Body>
</soap:Envelope>';

			
			$parser = $this->WebService(__FUNCTION__, $xml_method);

			echo ($this->debug_mode == true ) ?  "<br><pre> Function: ".__FUNCTION__: '';
			($this->debug_mode == true ) ? print_r($parser): print_r('');
			echo ($this->debug_mode == true ) ?  "<br></pre> Function " : '';


			($this->debug_mode == true ) ?  print_r($parser) : print_r('');
            // user $parser to get your data out of XML response and to display it.
            
            if ( $parser->GetInfoCardResponse->GetInfoCardResult->HuboError == 'true' ) {
            	
            	$this->error[] = 'Ocurrio un error al momento de obtener información de la tarjeta. '.$parser->GetInfoCardResponse->GetInfoCardResult->MensajeError;
            	($this->debug_mode == true ) ?  print_r($this->error) : print_r('');
            	return false;

            } else {
            	
            	$this->_GetInfoCard = $parser->GetInfoCardResponse->GetInfoCardResult;
            	return true;

            }
		}


		public function GetInfoDevolucionProductos() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetInfoDevolucionProductos xmlns="http://tempuri.org/">
      <sesion>'.$this->session_load.'</sesion>
      <skuProductos>
        <string>string</string>
        <string>string</string>
      </skuProductos>
    </GetInfoDevolucionProductos>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function GetInfoPatient() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetInfoPatient xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>string</NoTarjeta>
    </GetInfoPatient>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function GetInfoProduct() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetInfoProduct xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <Sku>string</Sku>
      <PiezasCompradas>long</PiezasCompradas>
    </GetInfoProduct>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function GetInfoProductCard() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetInfoProductCard xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>string</NoTarjeta>
    </GetInfoProductCard>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function GetPatientPoints($NoTarjeta) {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetPatientPoints xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>'.$NoTarjeta.'</NoTarjeta>
    </GetPatientPoints>
  </soap:Body>
</soap:Envelope>';

			     $parser = $this->WebService(__FUNCTION__, $xml_method);

      echo ($this->debug_mode == true ) ?  "<br><pre> Function: ".__FUNCTION__: '';
      ($this->debug_mode == true ) ? print_r($parser): print_r('');
      echo ($this->debug_mode == true ) ?  "<br></pre> Function " : '';


      ($this->debug_mode == true ) ?  print_r($parser) : print_r('');
            // user $parser to get your data out of XML response and to display it.
            
            if ( $parser->GetPatientPointsResponse->GetPatientPointsResult->HuboError == 'true' ) {
              
              $this->error[] = 'Ocurrio un error al momento de obtener los puntos asociados a la tarjeta. '.$parser->GetPatientPointsResponse->GetPatientPointsResult->MensajeError;
              ($this->debug_mode == true ) ?  print_r($this->error) : print_r('');
              return false;

            } else {
              
              $this->_GetPatientPoints = $parser->GetPatientPointsResponse->GetPatientPointsResult;
              return true;

            }
		}


		public function GetPromocionesEspecialesVigentes() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetPromocionesEspecialesVigentes xmlns="http://tempuri.org/">
      <request>
        <Usuario>'.$this->Usuario.'</Usuario>
        <Password>'.$this->Contraseña.'</Password>
        <CodigoCadena>'.$this->Id_Cadena.'</CodigoCadena>
      </request>
    </GetPromocionesEspecialesVigentes>
  </soap:Body>
</soap:Envelope>';

			 $parser = $this->WebService(__FUNCTION__, $xml_method);

      echo ($this->debug_mode == true ) ?  "<br><pre> Function: ".__FUNCTION__: '';
      ($this->debug_mode == true ) ? print_r($parser): print_r('');
      echo ($this->debug_mode == true ) ?  "<br></pre> Function " : '';


      ($this->debug_mode == true ) ?  print_r($parser) : print_r('');
            // user $parser to get your data out of XML response and to display it.
            
            if ( $parser->GetPromocionesEspecialesVigentesResponse->GetPromocionesEspecialesVigentesResult->HuboError == 'true' ) {
              
              $this->error[] = 'Ocurrio un error al momento de obtener información de la tarjeta. '.$parser->GetPromocionesEspecialesVigentesResponse->GetPromocionesEspecialesVigentesResult->MensajeError;
              ($this->debug_mode == true ) ?  print_r($this->error) : print_r('');
              return false;

            } else {
              
              $this->_GetPromocionesEspecialesVigentes = $parser->GetPromocionesEspecialesVigentesResponse->GetPromocionesEspecialesVigentesResult;
              return true;

            }
		}


		public function GetReposicionesCadena() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetReposicionesCadena xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
    </GetReposicionesCadena>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function GetReposicionesSucursal() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetReposicionesSucursal xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
    </GetReposicionesSucursal>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function GetStatementPatientPoints() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <GetStatementPatientPoints xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
      <NoTarjeta>string</NoTarjeta>
    </GetStatementPatientPoints>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function Logout() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <Logout xmlns="http://tempuri.org/">
      <Sesion>'.$this->session_load.'</Sesion>
    </Logout>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function TestConnect() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <TestConnect xmlns="http://tempuri.org/" />
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function UpdatePatient() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <UpdatePatient xmlns="http://tempuri.org/">
      <sesion>'.$this->session_load.'</sesion>
      <paciente>
        <Membresia>string</Membresia>
        <ApellidoPaterno>string</ApellidoPaterno>
        <ApellidoMaterno>string</ApellidoMaterno>
        <Nombre>string</Nombre>
        <Direccion>string</Direccion>
        <Colonia>string</Colonia>
        <CodigoPostal>string</CodigoPostal>
        <Ciudad>string</Ciudad>
        <Estado>string</Estado>
        <Rfc>string</Rfc>
        <Curp>string</Curp>
        <Sexo>string</Sexo>
        <EstadoCivil>string</EstadoCivil>
        <FechaNacimiento>string</FechaNacimiento>
        <Telefono>string</Telefono>
        <Celular>string</Celular>
        <Email>string</Email>
      </paciente>
    </UpdatePatient>
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}


		public function getVersion() {
			
			$xml_method = '<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <getVersion xmlns="http://tempuri.org/" />
  </soap:Body>
</soap:Envelope>';

			$xmlobj=/*new SimpleXMLElement(*/$xml_method;
			return $xmlobj;
		}

	



}


		










