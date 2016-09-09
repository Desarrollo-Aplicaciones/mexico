<?php

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
	public function getContent()
	{
       $current_state_img='blanco-estado.jpg';   
         
    $extras=null;
    $contact=null;
    $sql =' select adr.phone_mobile, cus.identification, adr.dni, odr.current_state, odr.module ,payu.method,payu.extras 
            from ps_address adr INNER JOIN ps_customer cus ON (adr.id_customer = cus.id_customer) 
            INNER JOIN ps_orders odr ON(odr.id_customer =cus.id_customer )
            LEFT JOIN ps_pagos_payu payu ON (odr.id_order=payu.id_order and odr.id_customer=payu.id_customer)
            WHERE odr.id_order='.(int)$this->order->id." GROUP BY cus.identification;";
    


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
                        $current_state_img = 'payment-pending.jpg';
                        break;
                    case 2:
                        $current_state_img = 'cancelado.jpg';
                        break;
                    case 3:
                        if ($row['module'] == 'cashondelivery') {
                            $current_state_img = 'payment-pending.jpg';
                        } else {
                            $current_state_img = 'cancelado.jpg';
                        }
                        break;
                    case 4:
                        if ($row['module'] == 'cashondelivery') {
                            $current_state_img = 'payment-pending.jpg';
                        } else {
                            $current_state_img = 'cancelado.jpg';
                        }
                        break;
                    case 5:
                        $current_state_img = 'cancelado.jpg';
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
                        $current_state_img = 'cancelado.jpg';
                        break;
                    case 10:
                        $current_state_img = 'payment-pending.jpg';
                        break;
                    case 11:
                        $current_state_img = 'payment-pending.jpg';
                        break;
                    case 12:
                        $current_state_img = 'cancelado.jpg';
                        break;
                    case 15:
                        $current_state_img = 'payment-pending.jpg';
                        break;
                    default:
                        $current_state_img = 'blanco-estado.jpg';
                }

                if (isset($row['method']) && (in_array( strtolower($row['method']), $this->get_mediosp()) )) {
                    $extras = explode(';', $row['extras']);
                    echo '<br> Explode';
                }


                $contact = (array('phone_mobile' => $row['phone_mobile'], 'dni' => $dni, 'current_state_img' => $current_state_img));
                break;
            }


        $query = 'select cupon.description 
from ps_orders orden
INNER JOIN ps_cart cart ON(orden.id_cart = cart.id_cart)
INNER JOIN ps_cart_cart_rule cartcup ON(cart.id_cart=cartcup .id_cart)
INNER JOIN ps_cart_rule cupon ON(cartcup.id_cart_rule = cupon.id_cart_rule)
where orden.id_order =' . (int) $this->order->id.' LIMIT 1';

    
$cupon=null;
try {
    
        if ($results = Db::getInstance()->ExecuteS($query))
        {
            foreach ($results as $row2) {
           
               $cupon = $row2['description'];  
            }
         }

} catch (Exception $exc) {
    
Logger::AddLog('Apoyo Salud [HTMLTempleteInvoice.php] getContent() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
 $cupon=null;    
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
            $direccion2="<tr><td width=\"80px\" >Dirección2:         </td><td>".$invoice_address->address2."</td></tr>";
            }
        
        $formatted_invoice_address =   "<table >
        <tr><td width=\"80px\" >Identificación:     </td><td>".$invoice_address->dni."</td></tr>".
        "<tr><td width=\"80px\" >Nombre y apellido: </td><td>".$invoice_address->firstname." ".$invoice_address->lastname."</td></tr>".
        "<tr><td width=\"80px\" >Dirección:         </td><td>".$invoice_address->address1."</td></tr>".
                $direccion2.
        "<tr><td width=\"80px\" >Colonia:           </td><td>".$invoice_address->colonia_name."</td></tr>".
        "<tr><td width=\"80px\" >Código Postal:     </td><td>".$invoice_address->postcode."</td></tr>".
        //"<tr><td width=\"70px\" >País:              </td><td>".$invoice_address->country."</td></tr>".
        //"<tr><td width=\"70px\" >Departamento:      </td><td>".State::getNameById($invoice_address->id_state)."</td></tr>".
        "<tr><td width=\"80px\" >Ciudad/Estado:     </td><td>".$invoice_address->city."/".State::getNameById($invoice_address->id_state)."</td></tr>".
        "<tr><td width=\"80px\" >Teléfono:          </td><td>".$invoice_address->phone."</td></tr>";

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
            $formatted_invoice_address.="<tr><td width=\"70px\" >Móvil: </td><td>" . $contact['phone_mobile'] . "</td></tr>";
        }

        if (isset($row['method']) && (in_array( strtolower($row['method']), $this->get_mediosp()))) {
            $formatted_invoice_address.='<tr><td width=\"70px\" >' . $row['method'] . ': </td><td>' . $extras[0] . '</td></tr>
                  <tr><td width=\"70px\" >Fecha expiración: </td><td>' . $extras[1] . '</td></tr>';
            if ($row['method'] == 'Baloto') {
                $formatted_invoice_address.='<tr><td width=\"70px\" >Convenio: </td><td>950110</td></tr>';
            } elseif ($row['method'] == 'Efecty') {
                $formatted_invoice_address.='<tr><td width=\"70px\" >Convenio: </td><td>110528</td></tr>';
            } else {
                $bar_code = '<img alt="Bar Code" src="' . $this->smarty->tpl_vars['img_ps_dir']->value . 'barcode.php?barcode=' . $extras[2] . '" />';
            }
        }
        $formatted_invoice_address.="</table>";
        $formatted_delivery_address = '';
        
        

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$delivery_address = new Address((int)$this->order->id_address_delivery);
			$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}

		$customer = new Customer((int)$this->order->id_customer); 

                    
               
           
// Url archivo de verificaciÃ³n webservice   
$nombre_archivo= parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
$nombre_archivo = explode('/', $nombre_archivo);
$var= array_pop($nombre_archivo);
$nombre_archivo = implode('/', $nombre_archivo);
$urlValidation = 'http://' .$_SERVER['HTTP_HOST'] .$nombre_archivo;          
                
$query = 'select prod.*
FROM
 ps_order_detail orderd
INNER JOIN ps_product prod ON (orderd.product_id= prod.id_product)
INNER JOIN ps_feature_product fea ON (prod.id_product = fea.id_product )
where 
fea.id_feature_value =4121
and orderd.id_order='. (int) $this->order->id;


$list_products = $this->order_invoice->getProducts();
$formu_medical = false;
        try {
            if ($results = Db::getInstance()->ExecuteS($query)) {


                foreach ($results as $row) {

                    foreach ($list_products as $row2 => $value) {


                        if ($value['product_id'] == $row['id_product']) {

	//<img style="height: 10px;" src="' . $urlValidation . '/../img/formulita.png"> 
                            $list_products[$row2]['product_name'] = '<sup>FM</sup> ' . $list_products[$row2]['product_name'];
                            $formu_medical = true;
                        }
                    }
                }
            }
        } catch (Exception $exc) {

            Logger::AddLog('Formula Medica [HTMLTempleteInvoice.php] getContent() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
        }
        
        $recalculadoivaproducto = false;

        ///*** INICIO CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///
        $cart_rules = $this->order->getCartRules($this->order_invoice->id);

        if (!empty($cart_rules)){
            $detailcartrule = $this->cartRuleDetail($cart_rules[0]['id_cart_rule']);
            $porcentajedescuento = $detailcartrule[0]['reduction_percent'];

            if ($porcentajedescuento != "" && $porcentajedescuento != 0){
                foreach ($list_products as $key => $product) {
                    $precio = $product['product_price'];

                    $iva = $this->ivaProduct($product['product_id']);
                    $iva = $iva[0]['rate'];

                    $descuento = ($precio * $porcentajedescuento) / 100;
                    $ivaproducto = ($precio - $descuento) * ($iva / 100);

                    $list_products[$key]['iva_recalculado'] = $ivaproducto;
                    $recalculadoivaproducto = true;
                }
            }
        }
        ///*** FIN CALCULO COSTO TOTAL, CON CUPON DE DESCUENTO PORCENTAJE ***///


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
            'apoyosalud' => $cupon,
		'formu_medical' => $formu_medical,
            'recalculadoivaproducto' => $recalculadoivaproducto,
		'bar_code' => $bar_code
		));

		return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
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

