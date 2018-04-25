<?php

class SignatureCFDICore extends ObjectModel
{

    public static $definition = array(
        'table' => 'alias',
        'primary' => 'id_alias'
    );

    private $id_order = null;

    private $id_order_transfer = null;

    private $order = null;

    private $orderInvoice = null;

    private $cupon_xml_calc = null;

    /**
     *
     * @param unknown $order
     * @return NULL
     */
    public function __construct($order = null)
    {
        $this->id_order_transfer = (int) Configuration::get('TRANSFERENCIA_BANCARIA_FACT');
        $this->order = $order;
        
        if (! Validate::isLoadedObject($this->order) || ! isset($this->id_order_transfer))
            return NULL;
        
        $this->orderInvoice = $this->getOrderInvoice();
        
        parent::__construct();
    }

    /**
     */
    private function getOderDetail()
    {
        if (! isset($this->order) || ! isset($this->id_order_transfer))
            return NULL;
        
        $sql = 'SELECT
	adr.phone_mobile,
	cus.identification,
	adr.dni,
	odr.current_state,
	odr.module,
	payu.method,
	payu.extras,

IF (
	ohh.id_order_state = 10,
	\'Transferencia electrónica (03)\',
	mp.medio_de_pago
) AS medio_de_pago,
 payu.json_request,
 GROUP_CONCAT(
	CONCAT(
		UPPER(LEFT(mes.message, 1)),
		LOWER(SUBSTRING(mes.message, 2))
	)
) AS note,
 t1.*,
t2.*
FROM
	' . _DB_PREFIX_ . 'orders odr
LEFT JOIN ' . _DB_PREFIX_ . 'customer cus ON (
	odr.id_customer = cus.id_customer
)
LEFT JOIN ' . _DB_PREFIX_ . 'address adr ON (
	adr.id_address = odr.id_address_delivery
)
LEFT JOIN ' . _DB_PREFIX_ . 'pagos_payu payu ON (
	odr.id_order = payu.id_order
	AND odr.id_customer = payu.id_customer
)
LEFT JOIN ' . _DB_PREFIX_ . 'medios_de_pago mp ON (
	odr.payment = mp.nombre
	OR odr.payment = mp.nombre_alterno
)
LEFT JOIN ' . _DB_PREFIX_ . 'message mes ON (
	odr.id_order = mes.id_order
	AND mes.id_employee = 0
	AND mes.id_customer != 0
)
LEFT JOIN ' . _DB_PREFIX_ . 'order_history ohh ON (
	odr.id_order = ohh.id_order
	AND ohh.id_order_state = 10
	AND ohh.id_order >= ' . $this->id_order_transfer . '
)
LEFT JOIN (
	SELECT
		cupon.description,
		cupon.reduction_percent,
		cupon.reduction_amount,
		cupon.reduction_product,
		cupon.gift_product,
		orden.id_order
	FROM
		' . _DB_PREFIX_ . 'orders orden
	INNER JOIN ' . _DB_PREFIX_ . 'order_cart_rule cartcup ON (
		orden.id_order = cartcup.id_order
	)
	INNER JOIN ' . _DB_PREFIX_ . 'cart_rule cupon ON (
		cartcup.id_cart_rule = cupon.id_cart_rule
	)
) t1 ON (odr.id_order = t1.id_order)
LEFT JOIN (
SELECT is_rfc,
 dni,
 alias,
 address1,
 cpp.nombre AS colonia_name,
 address2,
 postcode,
 firstname, 
lastname,
 a.city,
 a.id_state,
a.id_customer
FROM ' . _DB_PREFIX_ . 'address a LEFT JOIN ' . _DB_PREFIX_ . 'cod_postal cpp ON (cpp.id_codigo_postal = a.id_colonia)
WHERE a.is_rfc = 1
) t2 ON (adr.id_customer = t2.id_customer)
WHERE
	odr.id_order = ' . (int) $this->order->id . ' LIMIT 1;';
        
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     */
    public function sigCDFI($orderDetail = null)
    {
        $factura = new Facturaxion();
        
        $sello_SAT = $factura->RegistroTimbrado($this->order->id, 1);
        
        if ($sello_SAT == 0) {
            
            if (! isset($orderDetail))
                $orderDetail = $this->getOderDetail();
            
            $orderDetail = $orderDetail[0];
            
            $paymentMethod = 'No Identificado';
            $lastDigits = null;
            
            if (isset($orderDetail['medio_de_pago']) && ! empty($orderDetail['medio_de_pago'])) {
                $paymentMethod = $orderDetail['medio_de_pago'];
            }
            
            if (isset($orderDetail['json_request']) && ! empty($orderDetail['json_request'])) {
                $lastDigits = $orderDetail['json_request'];
            }
            
            $this->cupon_xml_calc['description'] = $orderDetail['description'];
            $this->cupon_xml_calc['reduction_product'] = $orderDetail['reduction_product'];
            $this->cupon_xml_calc['gift_product'] = $orderDetail['gift_product'];
            
            if ($orderDetail['reduction_percent'] != '0.00' && $orderDetail['reduction_percent'] != '0') {
                
                $this->cupon_xml_calc['tipo'] = 'porcentaje';
                $this->cupon_xml_calc['reduction'] = $orderDetail['reduction_percent'];
            } else {
                
                $this->cupon_xml_calc['tipo'] = 'valor';
                $this->cupon_xml_calc['reduction'] = $orderDetail['reduction_amount'];
            }
            
            $list_products = $this->orderInvoice->getProducts();
            
            $list_products = $this->addClaveProdServ($list_products);
            
            $country = new Country((int) $this->order->id_address_invoice);
            $invoice_address = new Address((int) $this->order->id_address_invoice);
            
            $dni = NULL;
            
            if (isset($orderDetail['identification']) && $orderDetail['identification'] != '0') {
                $dni = $orderDetail['identification'];
            } else if ($orderDetail['dni'] != '1111' && $orderDetail['dni'] != '') {
                $dni = $orderDetail['dni'];
            } else {
                $dni = 'N/A';
            }
            
            $contact = (array(
                'phone_mobile' => $orderDetail['phone_mobile'],
                'dni' => $dni
            ));
            
            $invoice_address->dni = $contact['dni'];
            
            $customer = new Customer((int) $this->order->id_customer);
            
            if ($invoice_address->lastname == '' || $invoice_address->firstname == '') { // validar si la dirección tiene nombre y apellido del cliente, si no, lo tomamos directamente del cliente
                $invoice_address->lastname = $customer->lastname;
                $invoice_address->firstname = $customer->firstname;
            }
            
            $obligar_timbrado = (int) Configuration::get('CFDI_OBLIGAR_TIMBRADO');
            $hacer_debug = (int) Configuration::get('CFDI_HACER_DEBUG');
            
            $totals = $this->getTotals();
            $sello_SAT = $factura->solicitud2($paymentMethod, $this->cupon_xml_calc, $list_products, $invoice_address, $this->order, $totals['array_ivas'], $totals['val_total_de_ivas'], $this->order->current_state, $obligar_timbrado, $hacer_debug);
            $count = 0;
           while ( !is_array($sello_SAT ) ){
                usleep(800000);
                $sello_SAT = $factura->RegistroTimbrado($this->order->id, 1);
               $count ++;
               if($count >= 8)
                   break;
            }
        } else {
            error_log("<La orden: " . $this->order->id . " ya esta firmada.>");
        }
        
        return $sello_SAT;
    }

    /**
     *
     * @return boolean|OrderInvoice
     */
    private function getOrderInvoice()
    {
        $sql = '
		SELECT oip.id_order_invoice
		FROM `' . _DB_PREFIX_ . 'order_invoice_payment` oip
		WHERE oip.id_order = ' . $this->order->id . '
        ORDER BY oip.id_order_invoice DESC
        LIMIT 1';
        
        $res = Db::getInstance()->ExecuteS($sql);
        
        if (! isset($res))
            return false;
        
        return new OrderInvoice((int) $res[0]['id_order_invoice']);
    }

    /**
     *
     * @return string[]|number[]|unknown[]
     */
    private function getTotals()
    {
        $array_ivas['0'] = '0.00';
        $cant_prods = 0;
        $totals = array();
        $iva_prod_actual = 0;
        $val_total_de_ivas = array();
        $val_total_de_iva = 0;
        $val_total_min_dto_mas_iva = 0;
        $list_products = $this->orderInvoice->getProducts();
        
        foreach ($list_products as $key => $value) {
            
            $val_iva_prod_actual = 0; // total del iva calculado del producto actual
            
            if (isset($this->cupon_xml_calc) && $this->cupon_xml_calc != null && $this->cupon_xml_calc['reduction'] != '') {
                if ($this->cupon_xml_calc['tipo'] == 'porcentaje' && $this->cupon_xml_calc['reduction_product'] != '0' && $this->cupon_xml_calc['reduction_product'] == $list_products[$key]['product_id']) {
                    $iva_prod_actual = Tools::ps_round(Cart::StaticUnitPriceDiscountPercent(Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round($this->cupon_xml_calc['reduction'], 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true), 2);
                } elseif ($this->cupon_xml_calc['tipo'] == 'porcentaje' && $this->cupon_xml_calc['reduction_product'] == '0') {
                    $iva_prod_actual = Tools::ps_round(Cart::StaticUnitPriceDiscountPercent(Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round($this->cupon_xml_calc['reduction'], 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true), 2);
                } elseif ($this->cupon_xml_calc['tipo'] == 'valor' && $this->cupon_xml_calc['reduction_product'] != '0' && ($this->cupon_xml_calc['reduction_product'] == $list_products[$key]['product_id'])) {
                    $iva_prod_actual = Tools::ps_round((Tools::ps_round((Tools::ps_round((Tools::ps_round(($list_products[$key]['unit_price_tax_excl'] - $this->cupon_xml_calc['reduction']), 2) * $list_products[$key]['product_quantity']), 2) * $list_products[$key]['tax_rate']), 2) / 100), 2);
                } else {
                    $iva_prod_actual = Tools::ps_round(Cart::StaticUnitPriceDiscountPercent(Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round('0.00', 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true), 2);
                }
            } else {
                $iva_prod_actual = Tools::ps_round(Cart::StaticUnitPriceDiscountPercent(Tools::ps_round($list_products[$key]['unit_price_tax_excl'], 2), Tools::ps_round($list_products[$key]['tax_rate'], 2), Tools::ps_round('0.00', 2), false, Tools::ps_round($list_products[$key]['product_quantity'], 2), false, true), 2);
            }
            
            if ($cant_prods == 0) {
                $subTotal_calculado = $list_products[$key]['total_price_tax_excl'];
            } else {
                $subTotal_calculado += $list_products[$key]['total_price_tax_excl'];
            }
            
            if (Tools::ps_round($list_products[$key]['tax_rate'], 2) != '0.00') {
                
                if (! isset($array_ivas[Tools::ps_round(Tools::ps_round($list_products[$key]['tax_rate'], 0), 0)])) {
                    
                    $array_ivas[Tools::ps_round($list_products[$key]['tax_rate'], 0)] = 0;
                }
                
                $array_ivas[Tools::ps_round($list_products[$key]['tax_rate'], 0)] += $iva_prod_actual;
            }
            
            $cant_prods ++;
        }
        
        if ($this->order->total_shipping != '0.00' || $this->order->total_shipping_tax_incl != '0.00') {
            
            if (! isset($array_ivas['16'])) {
                $array_ivas['16'] = 0;
            }
            
            $val_no_iva_envio = number_format($this->order->total_shipping / 1.16, 3, '.', '');
            $val_iva_envio_act = $this->order->total_shipping - $val_no_iva_envio;
            $array_ivas['16'] += number_format($val_iva_envio_act, 2, '.', '');
            $val_total_de_iva += $val_iva_envio_act;
            $val_total_min_dto_mas_iva += $val_iva_envio_act + $val_no_iva_envio;
            $subTotal_calculado += number_format($val_no_iva_envio, 2, '.', '');
        }
        
        $val_total_de_ivas = 0;
        foreach ($array_ivas as $key => $value) {
            $val_total_de_ivas += $value;
            $array_ivas[$key] = number_format($array_ivas[$key], 2, '.', '');
        }
        
        ksort($array_ivas);
        
        $totals['array_ivas'] = $array_ivas;
        $totals['val_total_de_ivas'] = $val_total_de_ivas;
        
        return $totals;
    }

    private function addClaveProdServ($list_products)
    {
        $query = 'SELECT o.product_id AS producto, fea.id_product AS formed, tx.rate, odt.unit_amount, feav.`value`, fea.id_feature AS fvalue
FROM ' . _DB_PREFIX_ . 'order_detail o
LEFT JOIN ' . _DB_PREFIX_ . 'order_detail_tax odt ON ( o.id_order_detail = odt.id_order_detail )
LEFT JOIN ' . _DB_PREFIX_ . 'tax tx ON ( tx.id_tax = odt.id_tax )
LEFT JOIN ' . _DB_PREFIX_ . 'feature_product fea ON ( o.product_id = fea.id_product AND fea.id_feature in  (' . Configuration::get('CARACTERISTICAS_FACTURA') . ') )
LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang feav ON ( fea.id_feature_value = feav.id_feature_value )
WHERE o.id_order = ' . (int) $this->order->id;
        
        if ($results = Db::getInstance()->ExecuteS($query)) {
            $formu_medical = false;
            
            foreach ($results as $row) {
                
                foreach ($list_products as $row2 => $value) {
                    
                    if ($value['product_id'] == $row['formed']) {
                        
                        $list_products[$row2]['product_name'] = '<sup>FM</sup> ' . $list_products[$row2]['product_name'];
                        $formu_medical = true;
                    }
                    
                    if (! isset($list_products[$row2]['ClaveProdServ']) || $list_products[$row2]['ClaveProdServ'] == '01010101') {
                        if ($value['product_id'] == $row['producto'] && $row['fvalue'] == '4137') {
                            $list_products[$row2]['ClaveProdServ'] = $row['value'];
                        } else {
                            $list_products[$row2]['ClaveProdServ'] = '01010101';
                        }
                    }
                    
                    if (! isset($list_products[$row2]['DescProdServ']) || $list_products[$row2]['DescProdServ'] == 'No existe en el catálogo') {
                        if ($value['product_id'] == $row['producto'] && $row['fvalue'] == '4136') {
                            $list_products[$row2]['DescProdServ'] = $row['value'];
                        } else {
                            $list_products[$row2]['DescProdServ'] = 'No existe en el catálogo';
                        }
                    }
                    
                    if ($value['product_id'] == $row['producto'] && $row['rate'] != '' && $row['rate'] != null) {
                        
                        $list_products[$row2]['tax_rate'] = $row['rate'];
                    }
                }
            }
        }
        
        return $list_products;
    }
}


