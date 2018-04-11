<?php

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
    public function getContent() {
        $current_state_img = 'blanco-estado.jpg';
        $current_state_txt = '';
        $id_order_transfer = 32540;
        if ( Configuration::get('TRANSFERENCIA_BANCARIA_FACT') != NULL && Configuration::get('TRANSFERENCIA_BANCARIA_FACT') != 0) {
           $id_order_transfer = Configuration::get('TRANSFERENCIA_BANCARIA_FACT');
        }

        $extras = null;
        $contact = null;
        $sql = 'SELECT adr.phone_mobile, cus.identification, adr.dni, odr.current_state, odr.module ,payu.method, payu.extras, 
                    IF ( ohh.id_order_state = 10, "Transferencia electrónica (03)", mp.medio_de_pago ) AS medio_de_pago, payu.json_request,  GROUP_CONCAT(CONCAT(UPPER(LEFT(mes.message, 1)), LOWER(SUBSTRING(mes.message, 2)))) as note
                FROM ps_orders odr 
                LEFT JOIN ps_customer cus ON ( odr.id_customer = cus.id_customer )
                LEFT JOIN ps_address adr ON ( adr.id_address = odr.id_address_delivery)
                LEFT JOIN ps_pagos_payu payu ON (odr.id_order=payu.id_order AND odr.id_customer=payu.id_customer)
                LEFT JOIN ps_medios_de_pago mp ON ( odr.payment = mp.nombre OR odr.payment = mp.nombre_alterno )
                LEFT JOIN ps_message mes ON (odr.id_order = mes.id_order AND mes.id_employee = 0 AND mes.id_customer != 0)
                LEFT JOIN ps_order_history ohh ON ( odr.id_order = ohh.id_order AND ohh.id_order_state = 10 AND ohh.id_order >='.$id_order_transfer.')
                WHERE odr.id_order=' . (int) $this->order->id . " ;";
               // print_r($sql);exit();
        if ($results = Db::getInstance()->ExecuteS($sql))
            foreach ($results as $row) {
                $dni = NULL; // se toma la nota o mensaje registrada para la orden
                $note = $row['note'];  // print_r($row['medio_de_pago']);  

                if ($row['identification'] != NULL && $row['identification'] != '0') {
                    $dni = $row['identification'];
                } else if ($row['dni'] != '1111' && $row['dni'] != '') {
                    $dni = $row['dni'];
                } else {
                    $dni = 'N/A';
                }
          
                switch ($row['current_state']) {
                    case 1:
                        $current_state_img = 'pago_pendiente.png';
                        $current_state_txt = 'Pago Pendiente';
                        break;
                    case 2:
                            $current_state_img = 'pago_aprobado.png';
                            $current_state_txt = 'Pagado';
                        break;
                    case 3:
                        if ($row['module'] == 'cashondelivery') {

                            $current_state_img = 'pago_pendiente.png';
                            $current_state_txt = 'Pago Pendiente';

                        } else {

                            $current_state_img = 'pago_aprobado.png';
                            $current_state_txt = 'Pagado';

                        }
                        break;
                    case 4:
                        if ($row['module'] == 'cashondelivery') {
                            $current_state_img = 'pago_pendiente.png';
                            $current_state_txt = 'Pago Pendiente';                            
                        } else {
                            $current_state_img = 'pago_aprobado.png';
                            $current_state_txt = 'Pagado';
                        }
                        break;
                    case 5:
                            $current_state_img = 'pago_aprobado.png';
                            $current_state_txt = 'Pagado';                        
                        break;
                    case 6:
                        $current_state_img = 'blanco-estado.jpg';
                        break;
                    case 7:
                        $current_state_img = 'blanco-estado.jpg';
                        break;
                    case 8:
                        $current_state_img = 'blanco-estado.jpg';
                        break;
                    case 9:
                        $current_state_img = 'pago_pendiente.png';
                        $current_state_txt = 'Pago Pendiente';
                        break;
                    case 10:
                        $current_state_img = 'pago_pendiente.png';
                        $current_state_txt = 'Pago Pendiente';
                        break;
                    case 11:
                        $current_state_img = 'pago_pendiente.png';
                        $current_state_txt = 'Pago Pendiente';
                        break;
                    case 12:
                        $current_state_img = 'pago_aprobado.png';
                        $current_state_txt = 'Pagado';
                        break;
                    case 15:
                        $current_state_img = 'pago_aprobado.png';
                        $current_state_txt = 'Pagado';
                        break;
                    default:
                        $current_state_img = 'blanco-estado.jpg';
                }

                if (isset($row['method']) && (in_array( strtolower($row['method']), $this->get_mediosp()) )) {
                    $extras = explode(';', $row['extras']);
                    //echo '<br> Explode';
                }


                $contact = (array('phone_mobile' => $row['phone_mobile'], 'dni' => $dni, 'current_state_img' => $current_state_img));
                break;
            }


        $query = 'SELECT cupon.description, cupon.reduction_percent, cupon.reduction_amount, cupon.reduction_product, cupon.gift_product
                    FROM ps_orders orden
                    INNER JOIN ps_order_cart_rule cartcup ON( orden.id_order = cartcup.id_order )
                    INNER JOIN ps_cart_rule cupon ON(cartcup.id_cart_rule = cupon.id_cart_rule)
                    WHERE orden.id_order =' . (int) $this->order->id . ' LIMIT 1';


        $cupon = null;
        $cupon_xml_calc = array();
        
        try {

            if ($results = Db::getInstance()->ExecuteS($query)) {
                foreach ($results as $row2) {

                    $cupon = $row2['description'];
                    $cupon_xml_calc['description'] = $row2['description'];
                    $cupon_xml_calc['reduction_product'] = $row2['reduction_product'];
                    $cupon_xml_calc['gift_product'] = $row2['gift_product'];

                    if ( $row2['reduction_percent'] != '0.00' && $row2['reduction_percent'] != '0' ) {

                        $cupon_xml_calc['tipo'] = 'porcentaje';
                        $cupon_xml_calc['reduction'] = $row2['reduction_percent'];

                    } else {

                        $cupon_xml_calc['tipo'] = 'valor';
                        $cupon_xml_calc['reduction'] = $row2['reduction_amount'];


                    }

                }
            }

        } catch (Exception $exc) {

            Logger::AddLog('Apoyo Salud [HTMLTempleteInvoice.php] getContent() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
            $cupon = null;

        }

        $country = new Country((int) $this->order->id_address_invoice);
        $invoice_address = new Address((int) $this->order->id_address_invoice);


        $invoice_address->dni = $contact['dni'];


        $customer = new Customer((int) $this->order->id_customer);

        if ($invoice_address->lastname == '' || $invoice_address->firstname == '') { //validar si la dirección tiene nombre y apellido del cliente, si no, lo tomamos directamente del cliente
            $invoice_address->lastname = $customer->lastname;
            $invoice_address->firstname = $customer->firstname;
        }

        /************ FORMATEO FACTURA ***********/
        $direccion2='';
        $original_address = 0;
        $ancho_dir1= 80;
        $ancho_dir2= 189;
        
        

            
            $rfcReceptor = 'XAXX010101000';
            $rfcEmisor = 'FME140730J95';

            $query = new DbQuery();
            $query->select(' is_rfc, dni, alias, address1, cpp.nombre AS colonia_name, address2, postcode, firstname, lastname, a.city, a.id_state ');
            $query->from('address', 'a');
            $query->leftJoin('cod_postal', 'cpp', 'cpp.id_codigo_postal = a.id_colonia' );
            $query->where(' a.id_customer = '.$this->order->id_customer. ' AND a.is_rfc = 1' );
            $query->limit('1');
            $formatted_invoice_address = '';

            if ( $dir_factura = Db::getInstance()->executeS($query) ) {

                /********     ASIGNAR    RFC    DE    COMPRA    CON    FACTURA   is_rfc  ********/
            
                if ( isset( $dir_factura[0]['is_rfc'] ) && $dir_factura[0]['is_rfc'] == 1 ) {
                    $rfcReceptor = $dir_factura[0]['dni'];

                    /* style=\"border: 1px solid black;\" */
                    $formatted_invoice_address =   "<br style=\"line-height:2px;\"><br style=\"line-height:1px;\"><table style=\"width: 100%; \" cellpadding=\"0px\">
                    <tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > RFC Receptor: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$dir_factura[0]['dni']." </td></tr>"./*
                    <tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > Identificación: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$dir_factura[0]['dni']."</td></tr>".*/
                    "<tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > Nombre: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$dir_factura[0]['alias']."</td></tr>"/*.
                    "<tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > Dirección: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$dir_factura[0]['address1']."</td></tr>".*//*
                    "<tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >                  </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$invoice_address->address2."</td></tr>".   */     
                    /*"<tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > Colonia: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >". $dir_factura[0]['colonia_name']."</td></tr>".
                    "<tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > Código Postal: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$dir_factura[0]['postcode']."</td></tr>".*/
                    //"<tr><td width=\"70px\" >País: </td><td>".$invoice_address->country."</td></tr>".
                    //"<tr><td width=\"70px\" >Departamento: </td><td>".State::getNameById($invoice_address->id_state)."</td></tr>".
                    /*"<tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > Ciudad/Estado: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$dir_factura[0]['city']."/".State::getNameById($dir_factura[0]['id_state'])."</td></tr>"*//*.
                    "<tr><td width=\"50px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" > Teléfono: </td><td colspan='2' style=\"width: 74px; font-size: 19px;\" >".$invoice_address->phone."</td></tr>"*/;

                    $original_address = 1;


                    $formatted_invoice_address.="</table>";

                    $ancho_dir1= 60;
                    $ancho_dir2= 74;

                }
            } 

            

            /* style=\"border: 1px solid black;\" */
            $formatted_delivery_address = "<br style=\"line-height:2px;\"><br style=\"line-height:1px;\"><table style=\"width: 100%; \" cellpadding=\"0px\">";
            
            if ( $original_address == 0 ) {
                $formatted_delivery_address .= "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >RFC Receptor: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$rfcReceptor." </td></tr>";
            }

            $formatted_delivery_address .= "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Identificación: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->dni."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Nombre y apellido: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->firstname." ".$invoice_address->lastname."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Dirección: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->address1." ".$invoice_address->address2."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >                 </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->address2."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Colonia: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->colonia_name."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Código Postal: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->postcode."</td></tr>".
            //"<tr><td width=\"70px\" >País: </td><td>".$invoice_address->country."</td></tr>".
            //"<tr><td width=\"70px\" >Departamento: </td><td>".State::getNameById($invoice_address->id_state)."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Ciudad/Estado: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->city."/".State::getNameById($invoice_address->id_state)."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Teléfono: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->phone."</td></tr>".
            "<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\" >Celular: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\" >".$invoice_address->phone_mobile."</td></tr>";
        
        $fa1 = $invoice_address->city;
        $fa2 = $invoice_address->alias;
        $fa3 = $invoice_address->address1;

        // echo '<pre>';
        // print_r($invoice_address);
        // exit();

        $facturaValida = strtoupper($fa1);
        $facturaValida2 = strtoupper($fa2);
        $facturaValida3 = strtoupper($fa3);

        $bar_code = '';

        //$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
        // if (isset($contact['phone_mobile']) && $contact['phone_mobile'] != '') {
        //     $formatted_delivery_address.="<tr><td width=\"".$ancho_dir1."px;\" style=\"text-align: left; font-weight: bold; font-size: 17px;\">Móvil: </td><td colspan='2' style=\"width: ".$ancho_dir2."px; font-size: 19px;\">" . $contact['phone_mobile'] . "</td></tr>";
        // }

        $metodo_pago = 'No Identificado';
            $ultimos_digitos = null;

            if ( isset( $row['medio_de_pago'] ) && $row['medio_de_pago'] != '' && $row['medio_de_pago'] != null ) {
                $metodo_pago = $row['medio_de_pago'];                
            }

            if ( isset( $row['json_request'] ) && $row['json_request'] != '' && $row['medio_de_pago'] != null ) {
                $ultimos_digitos = $row['json_request'];
            }
            //echo "<br> Medio de pago: ".$row['medio_de_pago'];
        $ultimos_digitos = json_decode(str_replace("\\", "", $ultimos_digitos));

        if ( isset( $ultimos_digitos->transaction->creditCard->number ) && $ultimos_digitos->transaction->creditCard->number != null && $ultimos_digitos->transaction->creditCard->number != '') {            
            $ultimos4_digitos = "***".substr($ultimos_digitos->transaction->creditCard->number, -4, 4);
        } else {
            $ultimos4_digitos = '';
        }


/************** PRUEBAS PAGOS ***************
        $row['method'] = "OXXO";
        $extras = array();
        $extras['0'] = '029347505230';
        $extras['1'] = date('Y-M-D H:i:s');
        $extras['2'] = '9261491498612094';
        $metodo_pago = "Depósito en cuenta bancaria";
/************** PRUEBAS  PAGOS***************/
            if (Utilities::is_ssl() == true ) {
                $http_protocolo = 'https';
            } else {
                $http_protocolo = 'http';
            }

        if (isset($row['method']) && (in_array( strtolower($row['method']), $this->get_mediosp()))) {

            if ($row['method'] == 'Baloto') {
                $formatted_delivery_address.='<tr><td width=\"70px\" >Convenio: </td><td>950110</td></tr>';
            } elseif ($row['method'] == 'Efecty') {
                $formatted_delivery_address.='<tr><td width=\"70px\" >Convenio: </td><td>110528</td></tr>';
            } else {

       

            $bar_code = '<table><tr><td style=" width:270px; background-color: #3A9842; color: #FFF; height:8px; line-height:4px; font-size: 8pt; border: none; text-align: left;" >&nbsp;&nbsp;Para Realizar Tu Pago </td>
            </tr><tr><td style=" width:270px; height:4px; line-height:2px; font-size: 8pt; border: none; text-align: left;"> </td></tr><tr><td style="width:270px; color: #000; height:8px; line-height:4px; font-size: 8pt; border: none;" ><img alt="Bar Code" src="' . str_replace("http", $http_protocolo, $this->smarty->tpl_vars['img_ps_dir']->value) . 'barcode.php?barcode=' . $extras[2] . '" height="40px"/></td></tr></table>';
            }



            $formatted_delivery_address.='
            <tr>
               <td colspan="3" style=" width:134px; font-size: 5pt; border: none; height:0.5px; line-height: 2px;" ></td>
            </tr>
            <tr>
               <td colspan="3" style=" width:134px; background-color: #3A9842; color: #FFF; height:8px; line-height:4px; font-size: 8pt; border: none;" >&nbsp;&nbsp;Para Realizar Tu Pago </td>
            </tr>
            <tr>
               <td colspan="3" style=" width:134px; font-size: 5pt; border: none; height:0.5px; line-height: 1px;" ></td>
            </tr>
            <tr><td width="'.$ancho_dir1.'px;" style="text-align: left; font-weight: bold; font-size: 7pt; " >' . $row['method'] . ': </td><td colspan="2" style="width: '.$ancho_dir2.'px; font-size: 7pt;" > &nbsp;' . $extras[0] . '</td>
            </tr>';
            if ( $metodo_pago != "Depósito en cuenta bancaria") {

                $formatted_delivery_address.='<tr>
                   <td width="'.$ancho_dir1.'px;" style="text-align: left; font-weight: bold; font-size: 7pt;" >Fecha expiración: </td><td colspan="2" style="width: '.$ancho_dir2.'px; font-size: 7pt;" >&nbsp;' . $extras[1] . '</td>
                </tr>';
  
            }
            if(isset($extras[3])){
                $covenio = json_decode($extras[3],true);
                $formatted_delivery_address.='<tr>
                                             <td width="'.$ancho_dir1.'px;" style="text-align: left; font-weight: bold; font-size: 7pt;" >'.$covenio['label'].' &nbsp;</td><td colspan="2" style="width: '.$ancho_dir2.'px; font-size: 7pt;" >&nbsp;'.$covenio['value'].'</td>
                                             </tr>';
             } 
            
        }
        $formatted_delivery_address.="</table>";
        //$formatted_delivery_address = '';
        

        if ($this->order->id_address_delivery != $this->order->id_address_invoice) {
            $delivery_address = new Address((int) $this->order->id_address_delivery);
            $formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
        }

        $customer = new Customer((int) $this->order->id_customer);




        // Url archivo de verificaciÃ³n webservice   
        $nombre_archivo = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $nombre_archivo = explode('/', $nombre_archivo);
        $var = array_pop($nombre_archivo);
        $nombre_archivo = implode('/', $nombre_archivo);
        $urlValidation = 'http://' . $_SERVER['HTTP_HOST'] . $nombre_archivo;

 /*       $query = 'SELECT prod.* FROM ps_order_detail orderd
INNER JOIN ps_product prod ON (orderd.product_id= prod.id_product)
LEFT JOIN ps_feature_product fea ON (prod.id_product = fea.id_product )
WHERE fea.id_feature_value =4121
AND orderd.id_order=' . (int) $this->order->id;
*/
        $query = 'SELECT o.product_id AS producto, fea.id_product AS formed, tx.rate, odt.unit_amount, feav.`value`, fea.id_feature AS fvalue
FROM '._DB_PREFIX_.'order_detail o
LEFT JOIN '._DB_PREFIX_.'order_detail_tax odt ON ( o.id_order_detail = odt.id_order_detail )
LEFT JOIN '._DB_PREFIX_.'tax tx ON ( tx.id_tax = odt.id_tax )
LEFT JOIN '._DB_PREFIX_.'feature_product fea ON ( o.product_id = fea.id_product AND fea.id_feature in  ('.Configuration::get('CARACTERISTICAS_FACTURA').') )
LEFT JOIN '._DB_PREFIX_.'feature_value_lang feav ON ( fea.id_feature_value = feav.id_feature_value )
WHERE o.id_order = ' . (int) $this->order->id;



        $list_products = $this->order_invoice->getProducts();
       /* echo "<pre>";var_dump(debug_backtrace()); echo "</pre>"; 
                exit();*/
        $formu_medical = false;
        try {
            if ($results = Db::getInstance()->ExecuteS($query)) {


                foreach ($results as $row) {

                    foreach ($list_products as $row2 => $value) {


                        if ($value['product_id'] == $row['formed']) {

                            //<img style="height: 10px;" src="' . $urlValidation . '/../img/formulita.png"> 
                            $list_products[$row2]['product_name'] = '<sup>FM</sup> ' . $list_products[$row2]['product_name'];
                            $formu_medical = true;
                        }

                        if(!isset($list_products[$row2]['ClaveProdServ']) || $list_products[$row2]['ClaveProdServ'] == '01010101'){
                            if($value['product_id'] == $row['producto'] && $row['fvalue'] == '4137'){
                                $list_products[$row2]['ClaveProdServ'] = $row['value'];
                            }else{
                                $list_products[$row2]['ClaveProdServ'] = '01010101';
                            }
                        }

                        if(!isset($list_products[$row2]['DescProdServ']) || $list_products[$row2]['DescProdServ'] == 'No existe en el catálogo'){
                            if($value['product_id'] == $row['producto'] && $row['fvalue'] == '4136'){
                                $list_products[$row2]['DescProdServ'] = $row['value'];
                            }else{
                                $list_products[$row2]['DescProdServ'] = 'No existe en el catálogo';
                            }
                        }

                        if ( $value['product_id'] == $row['producto'] && $row['rate'] != '' && $row['rate'] != null ) {

                            $list_products[$row2]['tax_rate'] = $row['rate'];

                        }
                    }
                }
            }
        } catch (Exception $exc) {

            Logger::AddLog('Formula Medica [HTMLTempleteInvoice.php] getContent() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
        }
        
        $recalculadoivaproducto = false;
        $array_ivas['0'] = '0.00';

        ///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE Y LISTADO DE IMPUESTOS DE PRODUCTOS ***///
        $cart_rules = $this->order->getCartRules($this->order_invoice->id);

                $cant_prods = 0;
                $subTotal_calculado = floatval(0); // tendrá la suma de cada ( precio producto X cantidad ) antes de iva
                $val_total_min_dto_mas_iva = 0; // Total de la venta actual
                $val_total_de_iva = 0; // total del iva calculado
                $iva_prod_actual = 0;
                //$val_iva_X_tax = array();
                
//                error_log("\n\n 11111.... list_products:".print_r($list_products,true),3,"/tmp/progresivo.log");
//                error_log("\n\n 22222.... cupon_xml_calc:".print_r($cupon_xml_calc,true),3,"/tmp/progresivo.log");
                foreach ($list_products as $key => $value) {

                    $val_iva_prod_actual = 0; // total del iva calculado del producto actual
                    
                    
                    if ( $cant_prods == 0 ) {
                        $subTotal_calculado = $list_products[$key]['total_price_tax_excl'];
                    } else {
                        $subTotal_calculado += $list_products[$key]['total_price_tax_excl'];
                    }


                    if ( isset( $cupon_xml_calc ) && $cupon_xml_calc != null && $cupon_xml_calc['reduction'] != '' ) {
                        if ( $cupon_xml_calc['tipo'] == 'porcentaje' && $cupon_xml_calc['reduction_product'] != '0' && $cupon_xml_calc['reduction_product'] == $list_products[$key]['product_id']) {
                            $iva_prod_actual = Tools::ps_round( Cart::StaticUnitPriceDiscountPercent( Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round($cupon_xml_calc['reduction'], 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true ), 2);
//                            error_log("\n\n 1.... iva_prod_actual:".print_r($iva_prod_actual,true),3,"/tmp/progresivo.log");
                        } 
                        elseif ( $cupon_xml_calc['tipo'] == 'porcentaje' && $cupon_xml_calc['reduction_product'] == '0' ) {
                            $iva_prod_actual = Tools::ps_round( Cart::StaticUnitPriceDiscountPercent( Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round($cupon_xml_calc['reduction'], 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true ), 2);
//                            error_log("\n\n 2.1... iva_prod_actual:".print_r($iva_prod_actual,true),3,"/tmp/progresivo.log");
                        } 
                        elseif ( $cupon_xml_calc['tipo'] == 'valor' && $cupon_xml_calc['reduction_product'] != '0'  && ( $cupon_xml_calc['reduction_product'] == $list_products[$key]['product_id']) ){
                            //$iva_prod_actual = Tools::ps_round( Cart::StaticUnitPriceDiscountPercent( Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round($cupon_xml_calc['reduction'], 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true ), 2);
//                            error_log("\n\n 2.2... list_products[key]['unit_price_tax_excl']:".print_r($list_products[$key]['unit_price_tax_excl'],true),3,"/tmp/progresivo.log");
//                            error_log("\n\n 2.2... list_products[key]['product_quantity']:".print_r($list_products[$key]['product_quantity'],true),3,"/tmp/progresivo.log");
//                            error_log("\n\n 2.2... cupon_xml_calc['reduction']:".print_r($cupon_xml_calc['reduction'],true),3,"/tmp/progresivo.log");
//                            error_log("\n\n 2.2... list_products[key]['tax_rate']:".print_r($list_products[$key]['tax_rate'],true),3,"/tmp/progresivo.log");
                            $iva_prod_actual = Tools::ps_round(( Tools::ps_round((Tools::ps_round( ( Tools::ps_round(($list_products[$key]['unit_price_tax_excl'] - $cupon_xml_calc['reduction']),2) * $list_products[$key]['product_quantity']),2) * $list_products[$key]['tax_rate']),2)/100),2) ;
//                            error_log("\n\n 2.2... iva_prod_actual:".print_r($iva_prod_actual,true),3,"/tmp/progresivo.log");
                        }
                        //$cupon_xml_calc['reduction_product'] == '0'
                        else {
                            $iva_prod_actual = Tools::ps_round( Cart::StaticUnitPriceDiscountPercent( Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round('0.00', 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true ), 2);
//                            error_log("\n\n 3.... iva_prod_actual:".print_r($iva_prod_actual,true),3,"/tmp/progresivo.log");
                        }
                    } 
                    else {
                        $iva_prod_actual = Tools::ps_round( Cart::StaticUnitPriceDiscountPercent( Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round('0.00', 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true ), 2);
//                        error_log("\n\n 4.... iva_prod_actual:".print_r($iva_prod_actual,true),3,"/tmp/progresivo.log");
                    }

                    //$iva_prod_actual = Tools::ps_round( Cart::StaticUnitPriceDiscountPercent( Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round('0.00', 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true ), 2);
//                    error_log("\n\n\n\n\n Prueba de iva con esa maricada: ".$iva_prod_actual,3,"/tmp/ordererror.log");
                    if ( Tools::ps_round( $list_products[$key]['tax_rate'] , 2) != '0.00') {


                            ///echo "<br> si tax del ". /*Tools::ps_round( */Tools::ps_round($list_products[$key]['tax_rate'],0) /*, 2)*/;

                        if ( !isset( $array_ivas[ Tools::ps_round(Tools::ps_round($list_products[$key]['tax_rate'],0),0) ] ) ) {
                            ///echo "<br> no creado tax del %  ".Tools::ps_round($list_products[$key]['tax_rate'],0);

                            $array_ivas[Tools::ps_round($list_products[$key]['tax_rate'],0)] = 0;

                        }
                            ///echo "<br> tax del  ".Tools::ps_round($list_products[$key]['tax_rate'],0)." % antes con ".$array_ivas[Tools::ps_round($list_products[$key]['tax_rate'],0)];



                        $array_ivas[Tools::ps_round($list_products[$key]['tax_rate'],0)] += $iva_prod_actual;

                            ///echo "<br> tax del  ".Tools::ps_round($list_products[$key]['tax_rate'],0)." % despues con ".$array_ivas[Tools::ps_round($list_products[$key]['tax_rate'],0)];


                    } 

                    $cant_prods++;

                }


                
                $descuentop_aplicado = 0;

                if ( isset( $cupon_xml_calc ) && $cupon_xml_calc != null && $cupon_xml_calc['reduction'] != '' ) {

                    if ( $cupon_xml_calc['tipo'] == 'porcentaje' ) {
                        $descuentop_aplicado = ( $subTotal_calculado * $cupon_xml_calc['reduction'] ) / 100;

                    } else {

                        $descuentop_aplicado = $subTotal_calculado - $cupon_xml_calc['reduction'];
                    }

                }
                //echo "<br> descuento aplicado: ".$descuentop_aplicado;

                if (  $this->order->total_shipping != '0.00' ||  $this->order->total_shipping_tax_incl != '0.00' ) {

                    if ( !isset( $array_ivas['16'] ) ) {
                        $array_ivas['16'] = 0;
                    }

                    //echo "<br> valor final envio: ".  $this->order->total_shipping;
                    //echo "<br> - valor sin iva envio: ". 
                    $val_no_iva_envio =  number_format(  $this->order->total_shipping / 1.16 ,3, '.', '');

                    //echo "<br> - iva envio: ". 
                    $val_iva_envio_act = /* number_format( ( */ $this->order->total_shipping  - $val_no_iva_envio/*) ,2, '.', '')*/;
//                    error_log("\n\n val_iva_envio_act:".print_r($val_iva_envio_act,true),3,"/tmp/progresivo.log");
                    
                    
                    $array_ivas['16'] += number_format( $val_iva_envio_act ,2, '.', '');

                    //echo "<br> val anterior de iva :". /*number_format( */$val_total_de_iva /*,2, '.', '')*/;

                    $val_total_de_iva += /*number_format( */$val_iva_envio_act /*,2, '.', '')*/;
                    //echo "<br> val acumulado total de iva :".  /*number_format( */$val_total_de_iva /*,2, '.', '')*/;

                    
                    //echo "<br> val acumulado total :".
                    $val_total_min_dto_mas_iva += $val_iva_envio_act + $val_no_iva_envio;
                    
                    $subTotal_calculado += number_format( $val_no_iva_envio ,2, '.', '');
                }

                //echo "<br> Valor verificador: ".
                $valor_calculado_verificador = number_format( $val_total_min_dto_mas_iva - $val_total_de_iva + $descuentop_aplicado, 2, '.', '');
                $val_total_de_ivas = 0;
                foreach ($array_ivas as $key => $value) {
                    $val_total_de_ivas += $value;
                    $array_ivas[$key] = number_format( $array_ivas[$key] , 2, '.', '');

                }

        // arsort() ordenar valores mayor a menor
        
        ksort($array_ivas);

        ///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE Y LISTADO DE IMPUESTOS DE PRODUCTOS ***///


 //   echo '<pre>array<br>...';
 //print_r( $array_ivas );
 //   echo '<br>factura<br>';
 ////print_r($this->smarty->fetch($this->getTemplateByCountry($country->iso_code)));
//exit();
//echo "<pre>";var_dump(debug_backtrace()); echo "</pre>"; exit();

    $sello_SAT = '';

//echo "<br>this->order->current_state: " . $this->order->current_state;
        $desdeordervalidar = 918;
       if ( Configuration::get('INICIO_ORDER_TIMBRADO') ) {
            $desdeordervalidar = Configuration::get('INICIO_ORDER_TIMBRADO');
        }

    if ( $this->order->id > $desdeordervalidar ) {

        $factura = new Facturaxion();

        if ( isset( $_GET['obligar_timbrado'] ) && $_GET['obligar_timbrado'] == "true") {
            $obligar_timbrado = 1;    
        } else {
            $obligar_timbrado = 0;
        }
        if ( isset( $_GET['hacer_debug'] ) && $_GET['hacer_debug'] == "true") {
            $hacer_debug = 1;    
        } else {
            $hacer_debug = 0;
        }
        $cant_rep = 0;

       // while ( ( $sello_SAT == '' && $cant_rep < 5 ) ) {
                                            //( $metodo_pago, $cupon,          $list_products, $invoice_address, $order_tot ) {
        $sello_SAT = $factura->solicitud2( $metodo_pago, $cupon_xml_calc, $list_products, $invoice_address, $this->order, $array_ivas, $val_total_de_ivas, $this->order->current_state, $obligar_timbrado, $hacer_debug);
        $cant_rep++;
        $obligar_timbrado = 0;
        usleep(800000);
        $sello_SAT = $factura->RegistroTimbrado( $this->order->id , 1);

    }


   // }

    //$sello_SAT = $factura->cancelacion( $this->order );
    //echo "<br>cant_rep: ".$cant_rep."<br>";
   // print_r( $sello_SAT );
    
//}
//exit();

//if ( $this->order->current_state == 4 ) {
    
    
    //$sello_SAT = $factura->solicitud2(  $metodo_pago, $cupon_xml_calc, $list_products, $invoice_address, $this->order, $this->order->current_state );

//}

        $CirculoSalud = new CirculoSalud();
        $data = $CirculoSalud->ProductsForXml( $this->order->id );
        $sql = 'SELECT c.sessionApego
                FROM ps_orders o
                inner JOIN ps_cart c
                ON o.id_cart = c.id_cart
                WHERE o.id_order = '.$this->order->id.';';
        $sessionApego = DB::getInstance()->getValue($sql);
	if ( $sessionApego ){
		$xml = $CirculoSalud->Create_Sales_Folio_Receta( $this->order->id, $sessionApego );
	}
			
        //error_log("El resultado es: ".$xml,3,"/var/www/errors.log");
//        
//        $c_rule = new CartRule();
//        $sqlCartRule = $c_rule->getCartRulesByNameLang( 'CIRSAN_C8910CI27', 1 );
//        echo 'Este es el fucking $sqlCartRule:  <br><br><pre>';
//        var_dump( $sqlCartRule );
//        
//        echo 'Este es el fucking DATA:  <br><br><pre>';
//        var_dump($data);
//        echo '<br><br>Este es el sessionApego <br><br><pre>';
//        var_dump($sessionApego);
//        echo '<br><br>este es el xml<br><br><pre>';
//        var_dump($xml);
//        exit(0);

        $this->smarty->assign(array(
            'apoyosalud' => $cupon,
            'bar_code' => $bar_code,
            'cart_rules' => $cart_rules,
            'current_state_img' => $current_state_img,
            'current_state_txt' => $current_state_txt,
            'customer' => $customer,
            'delivery_address' => $formatted_delivery_address,
            'facturaValida' => $facturaValida,
            'facturaValida2' => $facturaValida2,
            'facturaValida3' => $facturaValida3,
            'formu_medical' => $formu_medical,
            'invoice_address' => $formatted_invoice_address,
            'ivas' => $array_ivas,
            'metodo_pago' => $metodo_pago,
            'note' => $note,
            'order_details' => $list_products,
            'order' => $this->order,
            'recalculadoivaproducto' => $recalculadoivaproducto,
            'rfcemisor' => $rfcEmisor,
            'rfcreceptor' => $rfcReceptor,
            'sellosat' => $sello_SAT,
            'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
            'tax_tab' => $this->getTaxTabContent(),
            'ultimos_numeros' => $ultimos4_digitos
        ));
          
         /* $enviartimbradofactura = array(
            'rfcemisor' => $rfcEmisor,
            'rfcreceptor' => $rfcReceptor,
            'sellosat' => $sello_SAT            
            );

          echo "<hr> agua: <pre>";
          print_r($enviartimbradofactura);
          exit;*/
        //&modo_debug=md_col_09374&obligar_timbrado=true&hacer_debug=true
        //error_log("\n\nIvas: ".print_r($array_ivas,true),3,"/tmp/progresivo.log");
///////////////////////////////////////////////////////////////////////
  //sirve para mostrar la factura sin imprimir.
 //   echo '<pre>array<br>...';
 //print_r(1200 * ($list_products[780]['tax_rate'] / 100) );
 //   echo '<br>factura<br>';
 //print_r($this->smarty->fetch($this->getTemplateByCountry($country->iso_code)));
 //exit();
        /////////////////////////////////////////////////////////////////

        return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
    }

        /**
     * Returns the tax tab content
     */
    public function getTaxTabContent() {

        $address = new Address((int) $this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT') && !empty($address->vat_number) && $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
        $carrier = new Carrier($this->order->id_carrier);

        $numLetras = new EnLetras();

        $val_en_letras = explode(".", round((round($this->order_invoice->total_paid_tax_incl * 100) / 100) * 2, 0) / 2);

        $centavos = explode(".", $this->order_invoice->total_paid_tax_incl);

        $letras = utf8_encode($numLetras->ValorEnLetras((int) $val_en_letras[0], 'Pesos ('.$centavos[1].'/100) M.N.'));
        $this->order->total_discounts_tax_incl = ($this->order->total_products_wt+$this->order->total_shipping_tax_incl+$this->order->total_wrapping_tax_incl-$this->order->total_paid_tax_incl);
//  ENvia datos a invoice.tpl
        $this->smarty->assign(array(
            'carrier' => $carrier,
            'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
            'order_invoice' => $this->order_invoice,
            'order' => $this->order,
            'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown(),
            'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
            'tax_exempt' => $tax_exempt,
            'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
            'ValorEnLetras' => $letras,
            'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
        ));

        return $this->smarty->fetch($this->getTemplate('invoice.tax-tab'));
    }

    /*consulta para conocer los detalles del cupon agregado*/
    public function cartRuleDetail($id_cart_rule)
    {   
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT *
        FROM `'._DB_PREFIX_.'cart_rule` cr
        WHERE cr.`id_cart_rule` = '.$id_cart_rule);
    }

    /*consulta para conocer el iva del producto*/
    public function ivaProduct($id_product)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT t.rate
        FROM '._DB_PREFIX_.'product p
        INNER JOIN '._DB_PREFIX_.'tax_rule tr
        ON p.id_tax_rules_group = tr.id_tax_rule
        INNER JOIN '._DB_PREFIX_.'tax t
        ON tr.id_tax_rule = t.id_tax
        WHERE p.id_product ='.$id_product);
    }

    public function getTextCFDI()
    { 
        $textCFDI = NULL;
        $cart = new Cart($this->order->id_cart);
        $pruducts = $cart->getProducts();
        $context = Context::getContext();
        
        foreach ($pruducts as &$valor) {
            $product = new Product($valor['id_product'], true, $context->language->id, $context->shop->id);
            $features = $product->getFrontFeatures($context->language->id);
            foreach ($features as $value) {
                if (strtoupper($value['name']) == 'CFDI' && isset($value['value'])) {
                    $condition = (int) $value['value'];
                    $textCFDI = Configuration::get('PS_CFDI_' . $condition);
                    if (isset($textCFDI) && !empty($textCFDI))
                        break;
                }
            }
        }
        
        if ( empty($textCFDI)){
            $textCFDI = Configuration::get('PS_CFDI_102886');
        }
        if ( empty($textCFDI)){
            $textCFDI = 'G03 Gastos en general';
        }
        
        
        return $textCFDI; 
    }

    /**
     *
     * @see HTMLTemplate::getHeader()
     */
    public function getHeader()
    {
        $this->smarty->assign('textCFDI', $this->getTextCFDI());
        return parent::getHeader();
    }

}
