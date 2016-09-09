<?php
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
    $extras=null;
    $contact=null;
    $sql =' select adr.phone_mobile, cus.identification, adr.dni, odr.current_state, odr.module ,payu.method,payu.extras 
            from ps_address adr INNER JOIN ps_customer cus ON (adr.id_customer = cus.id_customer) 
            INNER JOIN ps_orders odr ON(odr.id_customer =cus.id_customer and odr.id_address_delivery=adr.id_address)
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
    
           
            $current_state_img=null;
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
             $current_state_img=NULL;    
             break;
             case 7:
             $current_state_img=NULL;    
             break;
             case 8:
             $current_state_img=NULL;    
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
            $current_state_img=null; 
            }
            
           if(isset($row['method']) && $row['method']=='Baloto')
            {
              $extras=explode( ';', $row['extras'] );
            }
            
 $contact=(array('phone_mobile'=>$row['phone_mobile'],'dni'=>$dni,'current_state_img'=>$current_state_img));
         
            }

            
		$country = new Country((int)$this->order->id_address_invoice);
		$invoice_address = new Address((int)$this->order->id_address_invoice);
               
                
                $invoice_address->dni= $contact['dni'];
                

           $customer = new Customer((int)$this->order->id_customer);                

        if ( $invoice_address->lastname == '' || $invoice_address->firstname == '') { //validar si la dirección tiene nombre y apellido del cliente, si no, lo tomamos directamente del cliente

        	$invoice_address->lastname = $customer->lastname;
        	$invoice_address->firstname = $customer->firstname;
        }

		$formatted_invoice_address = AddressFormat::generateAddress($invoice_address, array(), '<br />', ' ');
                $formatted_invoice_address=$formatted_invoice_address.'<br />'.$contact['phone_mobile'];
                
                if(isset($row['method']) && $row['method']=='Baloto')
                {
                  $formatted_invoice_address=$formatted_invoice_address .'<br/>Baloto: '.$extras[0].'<br/>Fecha expiración: '.$extras[1].'<br/>Convenio: 950110';  
                }
                
                $formatted_delivery_address = '';

		if ($this->order->id_address_delivery != $this->order->id_address_invoice)
		{
			$delivery_address = new Address((int)$this->order->id_address_delivery);
			$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
		}
        
		$this->smarty->assign(array(
			'order' => $this->order,
			'order_details' => $this->order_invoice->getProducts(),
			'cart_rules' => $this->order->getCartRules($this->order_invoice->id),
			'delivery_address' => $formatted_delivery_address,
			'invoice_address' => $formatted_invoice_address,
			'tax_excluded_display' => Group::getPriceDisplayMethod($customer->id_default_group),
			'tax_tab' => $this->getTaxTabContent(),
			'customer' => $customer,
                        'current_state_img'=>$current_state_img
		
		));


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
			
			$this->smarty->assign(array(
				'tax_exempt' => $tax_exempt,
				'use_one_after_another_method' => $this->order_invoice->useOneAfterAnotherTaxComputationMethod(),
				'product_tax_breakdown' => $this->order_invoice->getProductTaxesBreakdown(),
				'shipping_tax_breakdown' => $this->order_invoice->getShippingTaxesBreakdown($this->order),
				'ecotax_tax_breakdown' => $this->order_invoice->getEcoTaxTaxesBreakdown(),
				'wrapping_tax_breakdown' => $this->order_invoice->getWrappingTaxesBreakdown(),
				'order' => $this->order,
				'order_invoice' => $this->order_invoice,
				'carrier' => $carrier
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

