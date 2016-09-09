<?php

/*
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2014 PrestaShop SA
 *
 *  International Registered Trademark & Property of PrestaShop SA
 *
 * Description: PayPal "Express Checkout" controller (Product page, Shopping cart content page, Payment page/step)
 *
 * PayPal Express Checkout can be either offered on the Product pages, the Shopping cart content page depending on your preferences (Back-office addon's configuration)
 * It will also always be offered on the payment page/step to confirm the payment
 *
 * Step 1: The customer is clicking on the PayPal Express Checkout button from a product page or the shopping cart content page
 * Step 2: The customer is redirected to PayPal and selecting a funding source (PayPal account, credit card, etc.)
 * Step 3: PayPal redirects the customer to your store ("Shipping" checkout process page/step)
 * Step 4: PayPal is also sending you the customer details (delivery address, e-mail address, etc.)
 * If we do not have these info yet, we update your store database and create the related customer
 * Step 5: The customer is selected his/her shipping preference and is redirected to the payment page/step (still on your store)
 * Step 6: The customer is clicking on the second PayPal Express Checkout button to confirm his/her payment
 * Step 7: The transaction success or failure is sent to you by PayPal at the following URL: http://www.mystore.com/modules/paypalusa/controllers/front/expresscheckout.php?pp_exp_payment=1
 * Step 8: The customer is redirected to the Order confirmation page
 */

class PayPalusaExpressCheckoutModuleFrontController extends ModuleFrontController
{

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->paypal_usa = new PayPalUSA();
		if ($this->paypal_usa->active && Configuration::get('PAYPAL_USA_EXPRESS_CHECKOUT') == 1)
		{
			$pp_exp = 1 * (int)Tools::getValue('pp_exp_initial') + 2 * (int)Tools::getValue('pp_exp_checkout') + 3 * (int)Tools::getValue('pp_exp_payment');
			switch ($pp_exp)
			{
				/* Step 1 - Called the 1st time customer is clicking on the PayPal Express Checkout button */
				case 1:
				$this->_expressCheckoutInitial();
				break;
				/* Step 2 - Called by PayPal when the customer is redirected back from PayPal to the store (to retrieve the customer address and details) */
				case 2:
				$this->_expressCheckout();
				break;
				/* Step 3 - Called when the customer is placing his/her order / making his payment */
				case 3:
				$this->_expressCheckoutPayment();
				break;
				default :
				$this->_expressCheckoutInitial();
			}
		}
	}

	/**
	 * Upon a click on the "PayPal Express Checkout" button, this function creates a PayPal payment request
	 * If the customer was coming from a product page, it will also add the product to his/her shopping cart.
	 * Eventually it will redirect the customer to PayPal (to log-in or to fill his/her credit card info)
	 */
	private function _expressCheckoutInitial()
	{  
		/* If the customer has no cart yet, we need to create an empty one */
		if (!$this->context->cart->id)
		{
			if ($this->context->cookie->id_guest)
			{
				$guest = new Guest((int)$this->context->cookie->id_guest);
				$this->context->cart->mobile_theme = $guest->mobile_theme;
			}
			$this->context->cart->add();
			if ($this->context->cart->id)
				$this->context->cookie->id_cart = (int)$this->context->cart->id;
		}
			// validaciones para reglas de carrito
		$this->context->cart->get_products_rec();
		$this->auto_cart_rules('PayPal');
		 	$cart_rules = $this->context->cart->getCartRules(); // $totalToPay

		 	/* If the customer is coming from a product page, we need to add his/her product to the cart */
		 	if (Tools::getValue('paypal_express_checkout_id_product') != '')
		 		$this->context->cart->updateQty((int)Tools::getValue('paypal_express_checkout_quantity'), (int)Tools::getValue('paypal_express_checkout_id_product'), (int)Tools::getValue('paypal_express_checkout_id_product_attribute'));



		 	$nvp_request = '';
		 	$i = 0;
		 	$totalToPay = (float)$this->context->cart->getOrderTotal(true); 
//
		 	$totalToPayWithoutTaxes = (float)$this->context->cart->getOrderTotal(false); 

		 	$total_price = 0;

		 	$total_products=0;

		 	$total = 0;

		//foreach ($this->context->cart->getProducts() as $product) //   	$this->context->cart->get_products_rec();

		 	$list_products = $this->context->cart->get_products_rec();

		//exit('<pre>'.print_r($list_products,true));
		 	foreach ($list_products as $product)

		 	{
			//$price = (float)Tools::ps_round($product['price_wt'],2);
		 		$price = (float)Tools::ps_round((isset($product['precio_venta']) ? $product['precio_venta'] : $product['price_wt']),2);
		 		$nvp_request .= '&L_PAYMENTREQUEST_0_NAME'.$i.'='.urlencode($product['name']).
		 		'&L_PAYMENTREQUEST_0_NUMBER'.$i.'='.urlencode((int)$product['id_product']).
		 		'&L_PAYMENTREQUEST_0_DESC'.$i.'='.urlencode(strip_tags(Tools::truncate($product['description_short'], 80))).
		 		'&L_PAYMENTREQUEST_0_AMT'.$i.'='.urlencode($price).
		 		'&L_PAYMENTREQUEST_0_QTY'.$i.'='.urlencode((int)$product['cart_quantity']);
		 		$total += (float) $price * (int)$product['cart_quantity'];

		 		$i++;
		 	}

		 	$shipping = (float)$this->context->cart->getTotalShippingCost();
		 	// validación para cupones por monto
		 	if (isset($cart_rules) && $cart_rules[0]['reduction_percent'] == 0  && $cart_rules[0]['reduction_amount'] != 0  && ($total+$shipping) >= $cart_rules[0]['minimum_amount'])
		 	{ 
		 		if($cart_rules[0]['free_shipping']){
		 			$shipping = 0;
		 		}
		 		$discount = (float)Tools::ps_round($cart_rules[0]['value_real'],2); 

		 		$nvp_request .= '&L_PAYMENTREQUEST_0_NAME'.$i.'='.urlencode($cart_rules[0]['name']).
		 		'&L_PAYMENTREQUEST_0_AMT'.$i.'='.urlencode(-$discount).
		 		'&L_PAYMENTREQUEST_0_QTY'.$i.'=1';

		 		$total -=$discount;	

/*		 		if((float)$cart_rules[0]['value_real'] != (float) $cart_rules[0]['value_tax_exc']) {

		 			$value_tax_exc = (float) Tools::ps_round($cart_rules[0]['value_tax_exc'],2);
		 			$inpuesto = ($totalToPay - $shipping - $total);
		 			$nvp_request .= '&L_PAYMENTREQUEST_0_NAME'.($i+1).'='.urlencode('Impuestos').
		 							'&L_PAYMENTREQUEST_0_AMT'.($i+1).'='.urlencode($inpuesto).
		 							'&L_PAYMENTREQUEST_0_QTY'.($i+1).'=1';
		 			$total +=$inpuesto;	 
		 		}*/	
		 	}


		 	$nvp_request .= "&PAYMENTREQUEST_0_SHIPPINGAMT=".$shipping.
		 	"&PAYMENTREQUEST_0_ITEMAMT=".(float)$total;

		 	$total_total=(float) $total + $shipping; 

		 	$customer = new Customer((int)$this->context->cart->id_customer);
                //$addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
		 	$address = new Address((int)$this->context->cart->id_address_delivery);     

		 	$nvp_request .='&PAYMENTREQUEST_0_SHIPTONAME='.$address->firstname.' '.$address->lastname;
		 	$nvp_request .='&PAYMENTREQUEST_0_SHIPTOSTREET='.$address->address1;
		 	$nvp_request .='&PAYMENTREQUEST_0_SHIPTOSTREET2='.$address->address2;
		 	$nvp_request .='&PAYMENTREQUEST_0_SHIPTOCITY='. substr($address->city, 0, 39);
		 	$nvp_request .='&PAYMENTREQUEST_0_SHIPTOSTATE='.$address->country;
		 	if(empty($address->postcode)){
		 		$nvp_request .='&PAYMENTREQUEST_0_SHIPTOZIP=00000';
		 	} else{
		 		$nvp_request .='&PAYMENTREQUEST_0_SHIPTOZIP='.$address->postcode;
		 	}
		 	$nvp_request .='&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE='.$this->context->country->iso_code;
		 	$nvp_request .='&EMAIL='.$customer->email;
		 	$nvp_request .='&ADDROVERRIDE=1&NOSHIPPING=0';
		 	$nvp_request .='&COUNTRYCODE='.$this->context->country->iso_code;

		 	$Phone=NULL;
		 	if(empty($address->phone)){
		 		$Phone=$address->phone_mobile;
		 	}  else {
		 		$Phone=$address->phone;
		 	}
		 	$nvp_request .='&PAYMENTREQUEST_0_SHIPTOPHONENUM='.$Phone;

		 	/* Create a PayPal payment request and redirect the customer to PayPal (to log-in or to fill his/her credit card info) */
		 	$currency = new Currency((int)$this->context->cart->id_currency);
//exit((Configuration::get('PAYPAL_USA_EXP_CHK_BORDER_COLOR') != '' ? '&CARTBORDERCOLOR='.Tools::substr(str_replace('#', '', Configuration::get('PAYPAL_USA_EXP_CHK_BORDER_COLOR')), 0, 6) : '').'&PAYMENTREQUEST_0_AMT='.$totalToPay.'&PAYMENTREQUEST_0_PAYMENTACTION=Sale&RETURNURL='.urlencode($this->paypal_usa->getModuleLink('paypalusa', 'expresscheckout', array('pp_exp_checkout' => 1,))).'&CANCELURL='.urlencode($this->context->link->getPageLink('order.php')).'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($currency->iso_code).$nvp_request);
		 	$result = $this->paypal_usa->postToPayPal('SetExpressCheckout', (Configuration::get('PAYPAL_USA_EXP_CHK_BORDER_COLOR') != '' ? '&CARTBORDERCOLOR='.Tools::substr(str_replace('#', '', Configuration::get('PAYPAL_USA_EXP_CHK_BORDER_COLOR')), 0, 6) : '').'&PAYMENTREQUEST_0_AMT='.$total_total.'&PAYMENTREQUEST_0_PAYMENTACTION=Sale&RETURNURL='.urlencode($this->paypal_usa->getModuleLink('paypalusa', 'expresscheckout', array('pp_exp_checkout' => 1,))).'&CANCELURL='.urlencode($this->context->link->getPageLink('order.php')).'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($currency->iso_code).$nvp_request);

		 	if (Tools::strtoupper($result['ACK']) == 'SUCCESS' || Tools::strtoupper($result['ACK']) == 'SUCCESSWITHWARNING')
		 	{
		 		Tools::redirect('https://www.'.(Configuration::get('PAYPAL_USA_SANDBOX') ? 'sandbox.' : '').'paypal.com/'.(Configuration::get('PAYPAL_USA_SANDBOX') ? '' : 'cgi-bin/').'webscr?cmd=_express-checkout&token='.urldecode($result['TOKEN']));
		 		exit;
		 	}
		 	else
		 	{ 
		 		foreach ($result as $key => $val)
		 			$result[$key] = urldecode($val);

			//exit('<pre>'.print_r($result,TRUE));

		 		$this->context->smarty->assign('paypal_usa_errors', $result);
		 		$this->setTemplate('express-checkout-messages.tpl');
		 	}
		 }

	/**
	 * When the customer is back from PayPal after filling his/her credit card info or credentials, this function is preparing the order
	 * PayPal is providing us with the customer info (E-mail address, billing address) and we are trying to find a matching customer in the Shop database.
	 * If no customer is found, we create a new one and we simulate a logged customer session.
	 * Eventually it will redirect the customer to the "Shipping" step/page of the order process
	 */
	private function _expressCheckout()
	{        
		/* We need to double-check that the token provided by PayPal is the one expected */
		$result = $this->paypal_usa->postToPayPal('GetExpressCheckoutDetails', '&TOKEN='.urlencode(Tools::getValue('token')));
		$this->saveLog($result);  
             // exit('Resultado Paypal: <pre>'.  print_r($result ,TRUE).'</pre>');
		if ((Tools::strtoupper($result['ACK']) == 'SUCCESS' || Tools::strtoupper($result['ACK']) == 'SUCCESSWITHWARNING') && $result['TOKEN'] == Tools::getValue('token') && $result['PAYERID'] == Tools::getValue('PayerID') && $result['COUNTRYCODE'] == $this->context->country->iso_code)
		{
			/* Checks if a customer already exists for this e-mail address */
//			if (Validate::isEmail($result['EMAIL']))
//			{
			$customer = new Customer((int)$this->context->cart->id_customer);
				//$customer->getByEmail($result['EMAIL']);
//			}

			/* If the customer does not exist yet, create a new one */
			if (!Validate::isLoadedObject($customer))
			{
				$customer = new Customer();
				$customer->email = $result['EMAIL'];
				$customer->firstname = $result['FIRSTNAME'];
				$customer->lastname = $result['LASTNAME'];
				$customer->passwd = Tools::encrypt(Tools::passwdGen());
				$customer->add();

			}

//			/* Look for an existing PayPal address for this customer */
//			$addresses = $customer->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
//			foreach ($addresses as $address)
//				if ($address['alias'] == 'PayPal')
//				{
//					$id_address = (int)$address['id_address'];
//					break;
//				}
////exit();
//			/* Create or update a PayPal address for this customer */
//			$address = new Address(isset($id_address) ? (int)$id_address : 0);
//			$address->id_customer = (int)$customer->id;
//			$address->id_country = (int)Country::getByIso($result['PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE']);
//			$address->id_state = (int)State::getIdByIso($result['PAYMENTREQUEST_0_SHIPTOSTATE'], (int)$address->id_country);
//			$address->alias = 'PayPal';
//			$address->lastname = Tools::substr($result['PAYMENTREQUEST_0_SHIPTONAME'], 0, strpos($result['PAYMENTREQUEST_0_SHIPTONAME'], ' '));
//			$address->firstname = Tools::substr($result['PAYMENTREQUEST_0_SHIPTONAME'], strpos($result['PAYMENTREQUEST_0_SHIPTONAME'], ' '), Tools::strlen($result['PAYMENTREQUEST_0_SHIPTONAME']) - Tools::strlen($address->lastname));
//			$address->address1 = $result['PAYMENTREQUEST_0_SHIPTOSTREET'];
//			if ($result['PAYMENTREQUEST_0_SHIPTOSTREET2'] != '')
//				$address->address2 = $result['PAYMENTREQUEST_0_SHIPTOSTREET2'];
//			$address->city = $result['PAYMENTREQUEST_0_SHIPTOCITY'];
//			$address->postcode = $result['PAYMENTREQUEST_0_SHIPTOZIP'];
//			$address->save();

			/* Update the customer cookie to simulate a logged-in session */
			$this->context->cookie->id_customer = (int)$customer->id;
			$this->context->cookie->customer_lastname = $customer->lastname;
			$this->context->cookie->customer_firstname = $customer->firstname;
			$this->context->cookie->passwd = $customer->passwd;
			$this->context->cookie->email = $customer->email;
			$this->context->cookie->is_guest = $customer->isGuest();
			$this->context->cookie->logged = 1;
			
			/* Update the cart billing and delivery addresses */
//			$this->context->cart->id_address_delivery = (int)$address->id;
//			$this->context->cart->id_address_invoice = (int)$address->id;
			$this->context->cart->secure_key = $customer->secure_key;
			$this->context->cart->update();
			
			/* Save the Payer ID and Checkout token for later use (during the payment step/page) */
			$this->context->cookie->paypal_express_checkout_token = $result['TOKEN'];
			$this->context->cookie->paypal_express_checkout_payer_id = $result['PAYERID'];

			if (version_compare(_PS_VERSION_, '1.5', '<'))
				Module::hookExec('authentication');
			else
				Hook::exec('authentication');
                      // Modificación para redireccionar a la confirmación de pago  
                      // exit('<pre>'.  print_r($this->context->smarty->tpl_vars['base_uri']->value,TRUE).'</pre>');
			Tools::redirect($this->context->smarty->tpl_vars['base_uri']->value.'module/paypalusa/expresscheckout?pp_exp_payment=1');  
			/* Redirect the use to the "Shipping" step/page of the order process */
			//Tools::redirectLink($this->context->link->getPageLink('order.php', false, null, array('step' => '3')));
			exit;
		}
		else
		{ //exit('<pre>'.print_r($result,TRUE));
	foreach ($result as $key => $val)
		$result[$key] = urldecode($val);

	if($request['COUNTRYCODE'] != $this->context->country->iso_code){
		$result['ACK'] = 'Failure';
		$result['L_SHORTMESSAGE0'] = urldecode(utf8_encode('El pago con PayPal solo esta disponible para cuentas PayPal de México.'));
		$result['L_LONGMESSAGE0'] = urldecode(utf8_encode('Para finalizar tu pedido, realiza tu pago con otro medio de pago.'));
		$result['L_ERRORCODE0'] = urldecode(utf8_encode('998'));
	}

	$this->context->smarty->assign('paypal_usa_errors', $result);
	$this->setTemplate('express-checkout-messages.tpl');
}
}

	/**
	 * When the customer has clicked on the PayPal Express Checkout button (on the payment step/page) to complete his/her payment,
	 * this function is verifying the Payer ID and Checkout tokens and confirming the payment to PayPal
	 * Eventually it will create the order and redirect the customer to the Order confirmatione page
	 */
	private function _expressCheckoutPayment()
	{   

		if($this->context->cart->nbProducts() == 0){
			$result['ACK'] = 'Failure';
			$result['L_SHORTMESSAGE0'] = urldecode(utf8_encode('Asegúrate de no eliminar los productos del carrito, para poder confirmar tu orden correctamente.'));
			$result['L_LONGMESSAGE0'] = urldecode(utf8_encode('Agrega los productos a tu carrito y sigue le flujo normal de compra.'));
			$result['L_ERRORCODE0'] = urldecode(utf8_encode('997'));

			$this->context->smarty->assign('paypal_usa_errors', $result);
			$this->setTemplate('express-checkout-messages.tpl');

		}else{
			/* Verifying the Payer ID and Checkout tokens (stored in the customer cookie during step 2) */
			if (isset($this->context->cookie->paypal_express_checkout_token) && !empty($this->context->cookie->paypal_express_checkout_token))
			{
				/* Confirm the payment to PayPal */
				$currency = new Currency((int)$this->context->cart->id_currency);
				$result = $this->paypal_usa->postToPayPal('DoExpressCheckoutPayment', '&TOKEN='.urlencode($this->context->cookie->paypal_express_checkout_token).'&PAYERID='.urlencode($this->context->cookie->paypal_express_checkout_payer_id).'&PAYMENTREQUEST_0_PAYMENTACTION=Sale&PAYMENTREQUEST_0_AMT='.$this->context->cart->getOrderTotal(true).'&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($currency->iso_code).'&IPADDRESS='.urlencode($_SERVER['SERVER_NAME']));
				if (Tools::strtoupper($result['ACK']) == 'SUCCESS' || Tools::strtoupper($result['ACK']) == 'SUCCESSWITHWARNING')
				{
					/* Prepare the order status, in accordance with the response from PayPal */
					if (Tools::strtoupper($result['PAYMENTINFO_0_PAYMENTSTATUS']) == 'COMPLETED')
						$order_status = (int)Configuration::get('PS_OS_PAYMENT');
					elseif (Tools::strtoupper($result['PAYMENTINFO_0_PAYMENTSTATUS']) == 'PENDING')
						$order_status = (int)Configuration::get('PS_OS_PAYPAL');
					else
						$order_status = (int)Configuration::get('PS_OS_ERROR');

					/* Prepare the transaction details that will appear in the Back-office on the order details page */
					$message =
					'Transaction ID: '.$result['PAYMENTINFO_0_TRANSACTIONID'].'
					Transaction type: '.$result['PAYMENTINFO_0_TRANSACTIONTYPE'].'
					Payment type: '.$result['PAYMENTINFO_0_PAYMENTTYPE'].'
					Order time: '.$result['PAYMENTINFO_0_ORDERTIME'].'
					Final amount charged: '.$result['PAYMENTINFO_0_AMT'].'
					Currency code: '.$result['PAYMENTINFO_0_CURRENCYCODE'].'
					PayPal fees:  '.$result['PAYMENTINFO_0_FEEAMT'];

					if (isset($result['PAYMENTINFO_0_EXCHANGERATE']) && !empty($result['PAYMENTINFO_0_EXCHANGERATE']))
						$message .= 'Exchange rate: '.$result['PAYMENTINFO_0_EXCHANGERATE'].'
					Settled amount (after conversion): '.$result['PAYMENTINFO_0_SETTLEAMT'];

					$pending_reasons = array(
					                         'address' => 'Customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments.',
					                         'echeck' => 'The payment is pending because it was made by an eCheck that has not yet cleared.',
					                         'intl' => 'You hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.',
					                         'multi-currency' => 'You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.',
					                         'verify' => 'You are not yet verified, you have to verify your account before you can accept this payment.',
					                         'other' => 'Unknown, for more information, please contact PayPal customer service.');

if (isset($result['PAYMENTINFO_0_PENDINGREASON']) && !empty($result['PAYMENTINFO_0_PENDINGREASON']) && isset($pending_reasons[$result['PAYMENTINFO_0_PENDINGREASON']]))
	$message .= "\n".'Pending reason: '.$pending_reasons[$result['PAYMENTINFO_0_PENDINGREASON']];

/* Creating the order */
$customer = new Customer((int)$this->context->cart->id_customer);

if ($this->paypal_usa->validateOrder((int)$this->context->cart->id, (int)$order_status, (float)$result['PAYMENTINFO_0_AMT'], $this->paypal_usa->displayName, $message, array(), null, false, $customer->secure_key))
{
	/* Store transaction ID and details */
	$this->paypal_usa->addTransactionId((int)$this->paypal_usa->currentOrder, $result['PAYMENTINFO_0_TRANSACTIONID']);
	$this->paypal_usa->addTransaction('payment', array('source' => 'express', 'id_shop' => (int)$this->context->cart->id_shop, 'id_customer' => (int)$this->context->cart->id_customer, 'id_cart' => (int)$this->context->cart->id,
	                                  'id_order' => (int)$this->paypal_usa->currentOrder, 'id_transaction' => $result['PAYMENTINFO_0_TRANSACTIONID'], 'amount' => $result['PAYMENTINFO_0_AMT'],
	                                  'currency' => $result['PAYMENTINFO_0_CURRENCYCODE'], 'cc_type' => '', 'cc_exp' => '', 'cc_last_digits' => '', 'cvc_check' => 0,
	                                  'fee' => $result['PAYMENTINFO_0_FEEAMT']));

	/* Reset the PayPal's token so the customer will be able to place a new order in the future */
	unset($this->context->cookie->paypal_express_checkout_token, $this->context->cookie->paypal_express_checkout_payer_id);
}
else
	throw new Exception();

/* Redirect the customer to the Order confirmation page */
if (version_compare(_PS_VERSION_, '1.4', '<'))
	Tools::redirect('order-confirmation.php?id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->paypal_usa->id.'&id_order='.(int)$this->paypal_usa->currentOrder.'&key='.$customer->secure_key);
else
	Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$this->context->cart->id.'&id_module='.(int)$this->paypal_usa->id.'&id_order='.(int)$this->paypal_usa->currentOrder.'&key='.$customer->secure_key);
exit;
}
else
{
	foreach ($result as $key => $val)
		$result[$key] = urldecode($val);

				/* If PayPal is returning an error code equal to 10486, it means either that:
				 *
				 * - Billing address could not be confirmed
				 * - Transaction exceeds the card limit
				 * - Transaction denied by the card issuer
				 * - The funding source has no funds remaining
				 *
				 * Therefore, we are displaying a new PayPal Express Checkout button and a warning message to the customer
				 * He/she will have to go back to PayPal to select another funding source or resolve the payment error
				 */
				if (isset($result['L_ERRORCODE0']) && (int)$result['L_ERRORCODE0'] == 10486)
				{
					unset($this->context->cookie->paypal_express_checkout_token, $this->context->cookie->paypal_express_checkout_payer_id);
					$this->context->smarty->assign('paypal_usa_action', $this->paypal_usa->getModuleLink('paypalusa', 'expresscheckout', array('pp_exp_initial' => 1)));
				}

				$this->context->smarty->assign('paypal_usa_errors', $result);
				$this->setTemplate('express-checkout-messages.tpl');
			}
		}
	}
}

protected function auto_cart_rules($medio_de_pago){

	$sql="SELECT rules.id_cart_rule, mediosp.nombre_alterno,mediosp_rule.unique_rule,rules.reduction_percent,rules.reduction_amount 
	FROM "._DB_PREFIX_."cart_rule rules 
	INNER JOIN "._DB_PREFIX_."medios_de_pago_rule mediosp_rule ON(rules.id_cart_rule = mediosp_rule.id_cart_rule )
	INNER JOIN "._DB_PREFIX_."medios_de_pago_cart_rule mediosp_cart ON(mediosp_rule.id_medios_de_pago_rule = mediosp_cart.id_medios_de_pago_rule)
	INNER JOIN "._DB_PREFIX_."medios_de_pago mediosp ON(mediosp_cart.id_medio_de_pago = mediosp.id_medio_de_pago)
	WHERE '".date('Y-m-d H:i:s')."' BETWEEN rules.date_from  AND rules.date_to AND mediosp.nombre = '".$medio_de_pago."';";

	$row = Db::getInstance()->getRow($sql);

	if ( !empty($row) && count($row) > 0 && $row['id_cart_rule'] != '' && $row['id_cart_rule'] != '' ) {

        $cart_rule = new CartRule((int) $row['id_cart_rule']); //exit('<pre>'.print_r($cart_rule,TRUE));
        $shippingtmmp = (float)$this->context->cart->getTotalShippingCost();
        $totaltmp = (float)$this->context->cart->getOrderTotal(true); 
        $cart_rules = $this->context->cart->getCartRules();
		//echo '<br>Total: '. ($totaltmp + $shippingtmmp);
		//echo '<br>minimum_amount: '.$cart_rule->minimum_amount;
        if((float) ($totaltmp + $shippingtmmp) >= (float) $cart_rule->minimum_amount && $this->is_pay_rule((int) $row['id_cart_rule'])){
			                        // Eliminar todos los cupones del carrito
            $this->context->cart->removeCartRules(); //echo '<br><b>Agregar reglas</b>';
            // Agregar el cupón
            $this->context->cart->addCartRule((int) $row['id_cart_rule']);
            $this->context->cart->update(); 

        } elseif((float) ($totaltmp + $shippingtmmp) < (float) $cart_rule->minimum_amount && $this->is_pay_rule((int) $cart_rules[0]->id)){
        	$this->context->cart->removeCartRules();
			// echo '<br><b>Eliminar reglas</b>';
        }

    } // exit();
}
protected function is_pay_rule($id_rule){
	$sql="SELECT rules.id_cart_rule 
	FROM "._DB_PREFIX_."cart_rule rules 
	INNER JOIN "._DB_PREFIX_."medios_de_pago_rule mediosp_rule ON(rules.id_cart_rule = mediosp_rule.id_cart_rule )
	INNER JOIN "._DB_PREFIX_."medios_de_pago_cart_rule mediosp_cart ON(mediosp_rule.id_medios_de_pago_rule = mediosp_cart.id_medios_de_pago_rule)
	INNER JOIN "._DB_PREFIX_."medios_de_pago mediosp ON(mediosp_cart.id_medio_de_pago = mediosp.id_medio_de_pago)
	WHERE '".date('Y-m-d H:i:s')."' BETWEEN rules.date_from  AND rules.date_to AND rules.id_cart_rule = '".(int)$id_rule."';";

	$row = Db::getInstance()->getRow($sql);
	if(!empty($row) && !empty($row['id_cart_rule']) && $row['id_cart_rule'] == $id_rule ){
		return TRUE;

	}
	return FALSE;
}

protected function saveLog($data){
	$sql = "INSERT INTO `"._DB_PREFIX_."log_paypal` (`date`, `id_cart`, `id_customer`, `log`) VALUES ('".date('Y-m-d H:i:s')."', '".(int)$this->context->cart->id."', '".(int)$this->context->cart->id_customer."', '".print_r($data,TRUE)."');";
	if ($results = Db::getInstance()->Execute($sql)){
		return TRUE;
	}
	return FALSE;
}

}
