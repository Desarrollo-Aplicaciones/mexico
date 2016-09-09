<?php
require_once 'EnLetras_class.php';
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5
 */
class HTMLTemplateInvoiceCore extends HTMLTemplate
{
	public $order;
	public $available_in_your_account = false;

	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
	

	
		$this->order_invoice = $order_invoice;
		$this->order = new Order((int)$this->order_invoice->id_order);
		$this->smarty = $smarty;
		


		// header informations
		$this->date = Tools::displayDate($order_invoice->date_add);

		$id_lang = Context::getContext()->language->id;
		$this->title = HTMLTemplateInvoice::l('Invoice ').' #'.Configuration::get('PS_INVOICE_PREFIX', $id_lang, null, (int)$this->order->id_shop).sprintf('%06d', $order_invoice->number);
		// footer informations
		$this->shop = new Shop((int)$this->order->id_shop);
	}

	/**
	 * Returns the template's HTML content
	 * @return string HTML content
	 */
	public function getContent()
	{
            //'.(int)$this->order->id.'
    $current_state_img='blanco-estado.jpg';         
    $extras=null;
    $contact=null;
    $sql =' select adr.phone_mobile, cus.identification, adr.dni, odr.current_state, odr.module ,payu.method,payu.extras 
            from ps_address adr INNER JOIN ps_customer cus ON (adr.id_customer = cus.id_customer) 
            INNER JOIN ps_orders odr ON(odr.id_customer =cus.id_customer )
            LEFT JOIN ps_pagos_payu payu ON (odr.id_order=payu.id_order and odr.id_customer=payu.id_customer)
            WHERE odr.id_order='.(int)$this->order->id;
    

if ($results = Db::getInstance()->ExecuteS($sql))
    foreach ($results as $row)
            {
              $dni=NULL;
             
            
           if($row['identification']!=NULL&&$row['identification']!='0')
              {
              $dni=$row['identification'];             
              }
       else if($row['dni']!='1111'&&$row['dni']!='')
             {
             $dni=$row['dni'];  
             }
         else 
            {
            $dni='N/A'; 
            }
    
 
   
          
            switch ($row['current_state']) 
            {
            case 1:
             $current_state_img='payment-pending.jpg';    
             break;
             case 2:
             $current_state_img='cancelado.jpg';    
             break;
             case 3:
                 if($row['module']=='cashondelivery')
                 {
             $current_state_img='payment-pending.jpg';
                 }
                 else
                 {
             $current_state_img='cancelado.jpg';       
                 }
            break;
             case 4:
              if($row['module']=='cashondelivery')
                 {
             $current_state_img='payment-pending.jpg';
                 }
                 else
                 {
             $current_state_img='cancelado.jpg';       
                 }
             break;
             case 5:
             $current_state_img='cancelado.jpg';    
             break;
             case 6:
             $current_state_img='blanco-estado.jpg';    
             break;
             case 7:
             $current_state_img='blanco-estado.jpg';   
             break;
             case 8:
             $current_state_img='blanco-estado.jpg';   
             break;
             case 9:
             $current_state_img='cancelado.jpg';    
             break;
             case 10:
             $current_state_img='payment-pending.jpg';    
             break;
             case 11:
             $current_state_img='payment-pending.jpg';    
             break;
             case 12:
             $current_state_img='cancelado.jpg';    
             break;
             case 15:
             $current_state_img='payment-pending.jpg';    
             break;
            default:             
            $current_state_img='blanco-estado.jpg';
            }
            
           if(isset($row['method']) && $row['method']=='Baloto')
            {
              $extras=explode( ';', $row['extras'] );
            }
            
 $contact=(array('phone_mobile'=>$row['phone_mobile'],'dni'=>$dni,'current_state_img'=>$current_state_img));
 break;
            }


        $query = 'select cupon.* 
from ps_orders orden
INNER JOIN ps_cart cart ON(orden.id_cart = cart.id_cart)
INNER JOIN ps_cart_cart_rule cartcup ON(cart.id_cart=cartcup .id_cart)
INNER JOIN ps_cart_rule cupon ON(cartcup.id_cart_rule = cupon.id_cart_rule)
where orden.id_order =' . (int) $this->order->id.' LIMIT 1';

    
$cupon=null;
try {
    


        if ($results = Db::getInstance()->ExecuteS($query))
        {
            foreach ($results as $row) {
           
               $cupon = $row['description'];  
            }
         }

} catch (Exception $exc) {
    
Logger::AddLog('Apoyo Salud [HTMLTempleteInvoice.php] getContent() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
 $cupon=null;    
}

            
		$country = new Country((int)$this->order->id_address_invoice);
		$invoice_address = new Address((int)$this->order->id_address_invoice);
               
                
                $invoice_address->dni= $contact['dni'];
                

           $customer = new Customer((int)$this->order->id_customer);                

        if ( $invoice_address->lastname == '' || $invoice_address->firstname == '') { //validar si la dirección tiene nombre y apellido del cliente, si no, lo tomamos directamente del cliente

        	$invoice_address->lastname = $customer->lastname;
        	$invoice_address->firstname = $customer->firstname;
        }

        /************ FORMATEO FACTURA ***********/
        
        /*** Motrar opcion de transporte ***/
        
        $query="SELECT trans_op.cod_ref FROM
ps_orders_transporte order_t INNER JOIN ps_orders orde ON(order_t.id_order=orde.id_order )
INNER JOIN ps_transporte_opciones trans_op ON (order_t.id_transporte_opcion=trans_op.id_transporte_opcion)
WHERE orde.id_order=".$this->order->id;
   
        $transporte='';
  if ($results = Db::getInstance()->ExecuteS($query)){
       
       $transporte="<tr><td width=\"70px\" >COD:         </td><td>".$results[0]['cod_ref']."</td></tr>";   
   }
        
        $direccion2='';
        
        if($invoice_address->address2!=null && $invoice_address->address2!='')
            {
            $direccion2="<tr><td width=\"70px\" >Dirección2:         </td><td>".$invoice_address->address2."</td></tr>";
            }
        
        $formatted_invoice_address =   "<table >
        <tr><td width=\"70px\" >Identificación:     </td><td>".$invoice_address->dni."</td></tr>".
        "<tr><td width=\"70px\" >Nombre y apellido: </td><td>".$invoice_address->firstname." ".$invoice_address->lastname."</td></tr>".
        "<tr><td width=\"70px\" >Dirección:         </td><td>".$invoice_address->address1."</td></tr>".
                $direccion2.
        "<tr><td width=\"70px\" >País:              </td><td>".$invoice_address->country."</td></tr>".
        "<tr><td width=\"70px\" >Departamento:      </td><td>".State::getNameById($invoice_address->id_state)."</td></tr>".
        "<tr><td width=\"70px\" >Ciudad:            </td><td>".$invoice_address->city."</td></tr>".
        "<tr><td width=\"70px\" >Teléfono:          </td><td>".$invoice_address->phone."</td></tr>"
                .$transporte;

        $fa1 = $invoice_address->city;
        $fa2 = $invoice_address->alias;
        $fa3 = $invoice_address->address1;

        // echo '<pre>';
        // print_r($invoice_address);
        // exit();

        $facturaValida = strtoupper($fa1);
        $facturaValida2 = strtoupper($fa2);
        $facturaValida3 = strtoupper($fa3);

		//$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
        if(isset($contact['phone_mobile']) && $contact['phone_mobile'] != '') {
                $formatted_invoice_address.="<tr><td width=\"70px\" >Móvil: </td><td>".$contact['phone_mobile']."</td></tr>";
            }
                
                if(isset($row['method']) && $row['method']=='Baloto')
                {
                  $formatted_invoice_address.='<tr><td width=\"70px\" >Baloto: </td><td>'.$extras[0].'</td></tr>
                  <tr><td width=\"70px\" >Fecha expiración: </td><td>'.$extras[1].'</td></tr> <tr><td width=\"70px\" >Convenio: </td><td>950110</td></tr>';  
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
$nombre_archivo=explode('/', $nombre_archivo);
$var= array_pop($nombre_archivo);
$nombre_archivo=implode('/', $nombre_archivo); 
$urlValidation='http://'.$_SERVER['HTTP_HOST'].$nombre_archivo;          
                
$query='select prod.*
FROM
 ps_order_detail orderd
INNER JOIN ps_product prod ON (orderd.product_id= prod.id_product)
INNER JOIN ps_feature_product fea ON (prod.id_product = fea.id_product )
where 
fea.id_feature_value =4121
and orderd.id_order='. (int) $this->order->id;


$list_products = $this->order_invoice->getProducts();

        try {
            if ($results = Db::getInstance()->ExecuteS($query)) {


                foreach ($results as $row) {

                    foreach ($list_products as $row2 => $value) {


                        if ($value['product_id'] == $row['id_product']) {

	//<img style="height: 10px;" src="' . $urlValidation . '/../img/formulita.png"> 
                            $list_products[$row2]['product_name'] = '<sup>FM</sup> ' . $list_products[$row2]['product_name'];
                        }
                    }
                }
            }
        } catch (Exception $exc) {

            Logger::AddLog('Formula Medica [HTMLTempleteInvoice.php] getContent() error: ' . $exc->getTraceAsString(), 2, null, null, null, true);
        }




        $this->smarty->assign(array(
			'order' => $this->order,
			'order_details' => $list_products,
			'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
            'facturaValida' => $facturaValida,
            'facturaValida2' => $facturaValida2,
            'facturaValida3' => $facturaValida3,
			'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
			'tax_tab' => $this->getTaxTabContent(),
			'customer' => $customer,
            'current_state_img'=>$current_state_img,
            'apoyosalud'=> $cupon
		
		));

///////////////////////////////////////////////////////////////////////
//  sirve para mostrar la factura sin imprimir.
//    echo '<pre>array<br>';

// print_r($facturaValida3);
//    echo '<br>factura<br>';

// print_r($this->smarty->fetch($this->getTemplateByCountry($country->iso_code)));
// exit();
  /////////////////////////////////////////////////////////////////

		return $this->smarty->fetch($this->getTemplateByCountry($country->iso_code));
	}

  
	/**
	 * Returns the tax tab content
	 */
	public function getTaxTabContent()
	{
			$address = new Address((int)$this->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
			$tax_exempt = Configuration::get('VATNUMBER_MANAGEMENT')
								&& !empty($address->vat_number)
								&& $address->id_country != Configuration::get('VATNUMBER_COUNTRY');
			$carrier = new Carrier($this->order->id_carrier);
                       
                        $numLetras=new EnLetras();

			$val_en_letras=explode(".",round( (round($this->order_invoice->total_paid_tax_incl*100)/100) *2 , 0)/ 2);
                        
                        $letras= utf8_encode ($numLetras->ValorEnLetras((int)$val_en_letras[0], 'Pesos'));

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

	/**
	 * Returns the invoice template associated to the country iso_code
	 * @param string $iso_country
	 */
	protected function getTemplateByCountry($iso_country)
	{
		$file = Configuration::get('PS_INVOICE_MODEL');

		// try to fetch the iso template
		$template = $this->getTemplate($file.'.'.$iso_country);

		// else use the default one
		if (!$template)
			$template = $this->getTemplate($file);

		return $template;
	}

	/**
	 * Returns the template filename when using bulk rendering
	 * @return string filename
	 */
	public function getBulkFilename()
	{
		return 'invoices.pdf';
	}

	/**
	 * Returns the template filename
	 * @return string filename
	 */
	public function getFilename()
	{
		return Configuration::get('PS_INVOICE_PREFIX', Context::getContext()->language->id, null, $this->order->id_shop).sprintf('%06d', $this->order_invoice->number).'.pdf';
	}
}

