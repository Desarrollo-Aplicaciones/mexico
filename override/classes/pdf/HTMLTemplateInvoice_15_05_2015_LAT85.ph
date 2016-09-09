<?php

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
	public function getContent() {
        $current_state_img = 'blanco-estado.jpg';
        $current_state_txt = '';


        $extras = null;
        $contact = null;
        $sql = 'SELECT adr.phone_mobile, cus.identification, adr.dni, odr.current_state, odr.module ,payu.method, payu.extras, 
                        mp.medio_de_pago, payu.json_request 
            FROM ps_orders odr 
            LEFT JOIN ps_customer cus ON ( odr.id_customer = cus.id_customer )
            LEFT JOIN ps_address adr ON ( adr.id_customer = cus.id_customer) 
            LEFT JOIN ps_pagos_payu payu ON (odr.id_order=payu.id_order AND odr.id_customer=payu.id_customer)
            LEFT JOIN ps_medios_de_pago mp ON ( odr.payment = mp.nombre OR odr.payment = mp.nombre_alterno )
            WHERE odr.id_order=' . (int) $this->order->id . " GROUP BY cus.identification;";


        if ($results = Db::getInstance()->ExecuteS($sql))
            foreach ($results as $row) {
                $dni = NULL;


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


        $query = 'SELECT cupon.description, cupon.reduction_percent, cupon.reduction_amount
FROM ps_orders orden
INNER JOIN ps_cart cart ON(orden.id_cart = cart.id_cart)
INNER JOIN ps_cart_cart_rule cartcup ON(cart.id_cart=cartcup .id_cart)
INNER JOIN ps_cart_rule cupon ON(cartcup.id_cart_rule = cupon.id_cart_rule)
WHERE orden.id_order =' . (int) $this->order->id . ' LIMIT 1';


        $cupon = null;
        $cupon_xml_calc = array();
        
        try {

            if ($results = Db::getInstance()->ExecuteS($query)) {
                foreach ($results as $row2) {

                    $cupon = $row2['description'];
                    $cupon_xml_calc['description'] = $row2['description'];

                    if ( $row2['reduction_percent'] != '0.00'  ) {

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
        
        if($invoice_address->address2!=null && $invoice_address->address2!='')
            {
            $direccion2="<tr><td width=\"80px\" >Dirección2:         </td><td colspan='2'>".$invoice_address->address2."</td></tr>";
            }

            /* style=\"border: 1px solid black;\" */
            $formatted_invoice_address =   "<br style=\"line-height:2px;\"><br style=\"line-height:1px;\"><table style=\"width: 100%; \" cellpadding=\"0px\">
            <tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >RFC Receptor: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;XAXX010101000 </td></tr>
            <tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >Identificación: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->dni."</td></tr>".
            "<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >Nombre y apellido: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->firstname." ".$invoice_address->lastname."</td></tr>".
            "<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >Dirección: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->address1." ".$invoice_address->address2."</td></tr>"./*
            "<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >                 </td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->address2."</td></tr>".   */     
            "<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >Colonia: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->colonia_name."</td></tr>".
            "<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >Código Postal: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->postcode."</td></tr>".
            //"<tr><td width=\"70px\" >País: &nbsp;</td><td>".$invoice_address->country."</td></tr>".
            //"<tr><td width=\"70px\" >Departamento: &nbsp;</td><td>".State::getNameById($invoice_address->id_state)."</td></tr>".
            "<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >Ciudad/Estado: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->city."/".State::getNameById($invoice_address->id_state)."</td></tr>".
            "<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\" >Teléfono: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\" > &nbsp;".$invoice_address->phone."</td></tr>";

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
        if (isset($contact['phone_mobile']) && $contact['phone_mobile'] != '') {
            $formatted_invoice_address.="<tr><td width=\"80px;\" style=\"text-align: right; font-weight: bold; font-size: 19px;\">Móvil: &nbsp;</td><td colspan='2' style=\"width: 189px; font-size: 19px;\">&nbsp;" . $contact['phone_mobile'] . "</td></tr>";
        }

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
                $formatted_invoice_address.='<tr><td width=\"70px\" >Convenio: </td><td>950110</td></tr>';
            } elseif ($row['method'] == 'Efecty') {
                $formatted_invoice_address.='<tr><td width=\"70px\" >Convenio: </td><td>110528</td></tr>';
            } else {

                

                $bar_code = '<table><tr><td style=" width:270px; background-color: #3A9842; color: #FFF; height:8px; line-height:4px; font-size: 8pt; border: none; text-align: left;" >&nbsp;&nbsp;Para Realizar Tu Pago </td>
            </tr><tr><td style=" width:270px; height:4px; line-height:2px; font-size: 8pt; border: none; text-align: left;"> </td></tr><tr><td style="width:270px; color: #000; height:8px; line-height:4px; font-size: 8pt; border: none;" ><img alt="Bar Code" src="' . str_replace("http", $http_protocolo, $this->smarty->tpl_vars['img_ps_dir']->value) . 'barcode.php?barcode=' . $extras[2] . '" height="40px"/></td></tr></table>';
            }



            $formatted_invoice_address.='
            <tr>
               <td colspan="3" style=" width:270px; font-size: 5pt; border: none; height:0.5px; line-height: 2px;" ></td>
            </tr>
            <tr>
               <td colspan="3" style=" width:270px; background-color: #3A9842; color: #FFF; height:8px; line-height:4px; font-size: 8pt; border: none;" >&nbsp;&nbsp;Para Realizar Tu Pago </td>
            </tr>
            <tr>
               <td colspan="3" style=" width:270px; font-size: 5pt; border: none; height:0.5px; line-height: 1px;" ></td>
            </tr>
            <tr><td width="80px;" style="text-align: right; font-weight: bold; font-size: 7pt; " >' . $row['method'] . ': &nbsp;</td><td colspan="2" style="width: 189px; font-size: 7pt;" > &nbsp;' . $extras[0] . '</td>
            </tr>';
            if ( $metodo_pago != "Depósito en cuenta bancaria") {

                $formatted_invoice_address.='<tr>
                   <td width="80px;" style="text-align: right; font-weight: bold; font-size: 7pt;" >Fecha expiración: &nbsp;</td><td colspan="2" style="width: 189px; font-size: 7pt;" >&nbsp;' . $extras[1] . '</td>
                </tr>';
            }
            
            
        }
        $formatted_invoice_address.="</table>";
        $formatted_delivery_address = '';
        
        

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
        $query = 'SELECT o.product_id AS producto, fea.id_product AS formed, tx.rate, odt.unit_amount
FROM '._DB_PREFIX_.'order_detail o
LEFT JOIN '._DB_PREFIX_.'order_detail_tax odt ON ( o.id_order_detail = odt.id_order_detail )
LEFT JOIN '._DB_PREFIX_.'tax tx ON ( tx.id_tax = odt.id_tax )
LEFT JOIN '._DB_PREFIX_.'feature_product fea ON ( o.product_id = fea.id_product AND fea.id_feature = 4121 AND ( fea.id_feature_value = 1 OR fea.id_feature_value = 112 ) )
WHERE o.id_order = ' . (int) $this->order->id;



        $list_products = $this->order_invoice->getProducts();
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

        if (!empty($cart_rules)) {

            $detailcartrule = $this->cartRuleDetail($cart_rules[0]['id_cart_rule']);
            $porcentajedescuento = $detailcartrule[0]['reduction_percent'];

            if ( $porcentajedescuento != "" && $porcentajedescuento != 0 ) {
                foreach ($list_products as $key => $product) {
                    $precio = $product['product_price'];

                    $iva_c = (int)$product['tax_rate'];

                    $descuento = ( ( $precio * $product['product_quantity'] )  * $porcentajedescuento) / 100;                    
                    $ivaproducto = ( ( $precio * $product['product_quantity'] ) - $descuento) * ($iva_c / 100);

                    $list_products[$key]['iva_recalculado'] = $ivaproducto;
                    $recalculadoivaproducto = true;
                    
                    if ( !isset($array_ivas[$iva_c]) ) {
                        $array_ivas[$iva_c] = 0;
                    }

                    $array_ivas[$iva_c] += $ivaproducto;
                }
            }

        } else { 

             foreach ($list_products as $key => $product) {

                $precio = $product['product_price'];

                $iva_c = (int)$product['tax_rate'];
                
                $ivaproducto = ( $precio * $product['product_quantity'] ) * ($iva_c / 100);
                
                    if ( !isset($array_ivas[$iva_c]) ) {
                        $array_ivas[$iva_c] = 0;
                    }

                //echo "<br> iva si: ".$iva_c." - ".
                $array_ivas[$iva_c] += number_format( $ivaproducto ,2, '.', '');

                }

        }
        ///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///

        /*if ( $this->order->total_shipping != '0.00' || $this->order->total_shipping_tax_incl != '0.00' ) {

                    if ( !isset( $array_ivas['16'] ) ) {
                        $array_ivas['16'] = 0;
                    }

                    // envio valor sin iva
                    $val_no_iva_envio =  number_format( $this->order->total_shipping / 1.16 ,2, '.', '');
                    
                    //envio valor del iva
                    $val_iva_envio_act = $this->order->total_shipping  - $val_no_iva_envio;

                    $array_ivas['16'] += $val_iva_envio_act;
        }*/




                







                $cant_prods = 0;
                $subTotal_calculado = floatval(0); // tendrá la suma de cada ( precio producto X cantidad ) antes de iva
                $val_total_min_dto_mas_iva = 0; // Total de la venta actual
                $val_total_de_iva = 0; // total del iva calculado
                $val_iva_X_tax = array();
                
                foreach ($list_products as $key => $value) {

                    $val_iva_prod_actual = 0; // total del iva calculado del producto actual
                    
                    
                    if ( $cant_prods == 0 ) {
                        $subTotal_calculado = $list_products[$key]['total_price_tax_excl'];
                    } else {
                        $subTotal_calculado += $list_products[$key]['total_price_tax_excl'];
                    }

                    if ( number_format( $list_products[$key]['tax_rate'] , 2, '.', '') != '0.00') {

                        //echo "<br> si tax del ". /*number_format( */$list_products[$key]['tax_rate'] /*, 2, '.', '')*/;

                        if ( isset( $cupon_xml_calc ) && $cupon_xml_calc != null && $cupon_xml_calc['reduction'] != '' ) {

                            //echo "<br> si cupon_xml_calc del ". $cupon_xml_calc['reduction'] ;

                            if ( $cupon_xml_calc['tipo'] == 'porcentaje' ) {

                                //echo "<br> si cupon_xml_calc de %  ";
                                //echo "<br> val acumulado total de iva :".
                                $val_total_de_iva += $val_iva_prod_actual = //number_format( 
                                    ( $list_products[$key]['total_price_tax_excl'] - ( $list_products[$key]['total_price_tax_excl'] * $cupon_xml_calc['reduction'] ) / 100 ) * ( ( $list_products[$key]['tax_rate'] ) / 100 ) 
                                    /*, 2, '.', '')*/;

                                //echo "<br> val total acumulado :".
                                $val_total_min_dto_mas_iva += //number_format( 
                                    ( $list_products[$key]['total_price_tax_excl'] - ( $list_products[$key]['total_price_tax_excl'] * $cupon_xml_calc['reduction'] ) / 100 ) + $val_iva_prod_actual 
                                    /*, 2, '.', '')*/;

                            }

                        } else {

                            //echo "<br> val acumulado total de iva :".
                            $val_total_de_iva += $val_iva_prod_actual = //number_format( 
                                $list_products[$key]['total_price_tax_excl'] * ( ( $list_products[$key]['tax_rate'] ) / 100 )
                                /*, 2, '.', '')*/;

                            //echo "<br> Iva actual : ". $val_iva_prod_actual;

                            //echo "<br> val total acumulado :".
                            $val_total_min_dto_mas_iva += 
                                 $list_products[$key]['total_price_tax_incl'] 
                                ;

                            //echo "<br> precio con iva: ".$list_products[$key]['total_price_tax_incl'];

                        }


                        if ( !isset( $val_iva_X_tax[$list_products[$key]['tax_rate']] ) ) {
                            //echo "<br> no creado tax del %  ".$list_products[$key]['tax_rate'];

                            $val_iva_X_tax[$list_products[$key]['tax_rate']] = 0;

                        }

                        //echo "<br> tax del  ".$list_products[$key]['tax_rate']." % antes con ".$val_iva_X_tax[$list_products[$key]['tax_rate']];

                        $val_iva_X_tax[$list_products[$key]['tax_rate']] += $val_iva_prod_actual;

                        //echo "<br> tax del  ".$list_products[$key]['tax_rate']." % despues con ".$val_iva_X_tax[$list_products[$key]['tax_rate']];


                    } else {

                        if ( isset( $cupon_xml_calc ) && $cupon_xml_calc != null && $cupon_xml_calc['reduction'] != '' ) {

                            if ( $cupon_xml_calc['tipo'] == 'porcentaje' ) {

                                
                                /*$val_total_de_iva += $val_iva_prod_actual = number_format( 
                                    ( $list_products[$key]['total_price_tax_excl'] - ( $list_products[$key]['total_price_tax_excl'] * $cupon_xml_calc['reduction'] ) / 100 ) 
                                    , 2, '.', '');*/
                                //echo "<br> val total acumulado :".
                                $val_total_min_dto_mas_iva += //number_format( 
                                    ( $list_products[$key]['total_price_tax_excl'] - ( $list_products[$key]['total_price_tax_excl'] * $cupon_xml_calc['reduction'] ) / 100 ) 
                                    /*, 2, '.', '')*/;

                            }

                        } else {

                            //echo "<br> val total acumulado :".
                            $val_total_min_dto_mas_iva += /*number_format(*/ $list_products[$key]['total_price_tax_excl'] 
                                        /*, 2, '.', '')*/;

                        }

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

                if (  $valor_calculado_verificador !=  number_format($subTotal_calculado , 2, '.', '') ) {

                    //echo "<br> VALORES DIFERENTES !!!!!!!!!!!! ".$valor_calculado_verificador." <> ".number_format($subTotal_calculado , 2, '.', '');
                    $diferenciaValoresCalculados = number_format($subTotal_calculado , 2, '.', '') - $valor_calculado_verificador;

                    if ( count($array_ivas) != 0 ) {

                        arsort($array_ivas);
                        
                        foreach ($array_ivas as $key => $value) {
                            
                            if ( $key != '0' && $value >= $diferenciaValoresCalculados ) {
                                $array_ivas[$key] = number_format( $value - $diferenciaValoresCalculados , 2, '.', '');
                                $val_total_de_iva = $val_total_de_iva - $diferenciaValoresCalculados;
                                break;
                            }
                            //!isset( $array_ivas['16.000']
                        }

                    }

                }

                foreach ($array_ivas as $key => $value) {

                    $array_ivas[$key] = number_format( $array_ivas[$key] , 2, '.', '');

                }



        // arsort() ordenar valores mayor a menor
        
        ksort($array_ivas);

        ///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE Y LISTADO DE IMPUESTOS DE PRODUCTOS ***///


//    echo '<pre>array<br>...';
// print_r( $array_ivas );
//    echo '<br>factura<br>';
// //print_r($this->smarty->fetch($this->getTemplateByCountry($country->iso_code)));
//exit();


//echo "<pre>";var_dump(debug_backtrace()); echo "</pre>"; exit();

 $sello_SAT = '';

//echo "<br>this->order->current_state: " . $this->order->current_state;
$factura = new Facturaxion();


 if ( isset( $_GET['obligar_timbrado'] ) && $_GET['obligar_timbrado'] == "true") {
    $obligar_timbrado = 1;    
 } else {
    $obligar_timbrado = 0;
 }
    
    $cant_rep = 0;
    

   // while ( ( $sello_SAT == '' && $cant_rep < 5 ) ) {
                                        //( $metodo_pago, $cupon,          $list_products, $invoice_address, $order_tot ) {
        $sello_SAT = $factura->solicitud2(  $metodo_pago, $cupon_xml_calc, $list_products, $invoice_address, $this->order, $this->order->current_state, $obligar_timbrado );        
        $cant_rep++;
        $obligar_timbrado = 0;

   // }

    //$sello_SAT = $factura->cancelacion( $this->order );
    //echo "<br>cant_rep: ".$cant_rep."<br>";
    //print_r( $sello_SAT );
    
//}
//exit();

//if ( $this->order->current_state == 4 ) {
    
    
    //$sello_SAT = $factura->solicitud2(  $metodo_pago, $cupon_xml_calc, $list_products, $invoice_address, $this->order, $this->order->current_state );

//}


         $this->smarty->assign(array(
			'order' => $this->order,
			'order_details' => $list_products,
			'cart_rules' => $cart_rules,
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
            'facturaValida' => $facturaValida,
            'facturaValida2' => $facturaValida2,
            'facturaValida3' => $facturaValida3,
            'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
            'tax_tab' => $this->getTaxTabContent(),
            'customer' => $customer,
            'current_state_img' => $current_state_img,
            'current_state_txt' => $current_state_txt,
            'apoyosalud' => $cupon,
			'formu_medical' => $formu_medical,
			'recalculadoivaproducto' => $recalculadoivaproducto,
			'bar_code' => $bar_code,
            'metodo_pago' => $metodo_pago,
            'ultimos_numeros' => $ultimos4_digitos,
            'sellosat' => $sello_SAT,
            'ivas' => $array_ivas,
		));
       
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

        $this->smarty->assign(array(
            'tax_exempt' => $tax_exempt,
            'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
            'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown(),
            'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
            'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
            'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
            'order' => $this->order,
            'order_invoice' => $this->order_invoice,
            'carrier' => $carrier,
            'ValorEnLetras' => $letras
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

}

