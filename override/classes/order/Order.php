<?php  
  
class Order extends OrderCore {  
    
    public $id_employee_close_order;
    public $payment_method_sat;
    
    
    public static $definition = array(
		'table' => 'orders',
		'primary' => 'id_order',
		'fields' => array(
			'id_address_delivery' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_address_invoice' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_cart' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_currency' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_shop_group' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_shop' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_lang' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_customer' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_carrier' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'current_state' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'secure_key' => 				array('type' => self::TYPE_STRING, 'validate' => 'isMd5'),
			'payment' => 					array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
		    'payment_method_sat' => 		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'module' => 					array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true),
			'recyclable' => 				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'gift' => 						array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'gift_message' => 				array('type' => self::TYPE_STRING, 'validate' => 'isMessage'),
			'mobile_theme' => 				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'total_discounts' =>			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_discounts_tax_incl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_discounts_tax_excl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_paid' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_paid_tax_incl' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_paid_tax_excl' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_paid_real' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_products' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_products_wt' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_shipping' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_shipping_tax_incl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_shipping_tax_excl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'carrier_tax_rate' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
			'total_wrapping' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_wrapping_tax_incl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'total_wrapping_tax_excl' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice'),
			'shipping_number' => 			array('type' => self::TYPE_STRING, 'validate' => 'isTrackingNumber'),
			'conversion_rate' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'invoice_number' => 			array('type' => self::TYPE_INT),
			'delivery_number' => 			array('type' => self::TYPE_INT),
			'invoice_date' => 				array('type' => self::TYPE_DATE),
			'delivery_date' => 				array('type' => self::TYPE_DATE),
			'valid' => 						array('type' => self::TYPE_BOOL),
			'reference' => 					array('type' => self::TYPE_STRING),
			'date_add' => 					array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' => 					array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'private_message' => 			array('type' => self::TYPE_STRING),
                        'id_employee_close_order' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
		),
	);
	
	public function getHistory($id_lang, $id_order_state = false, $no_hidden = false, $filters = 0)
	{
		if (!$id_order_state)
			$id_order_state = 0;
	
		$logable = false;
		$delivery = false;
		$paid = false;
		$shipped = false;
		if ($filters > 0)
		{
			if ($filters & OrderState::FLAG_NO_HIDDEN)
				$no_hidden = true;
			if ($filters & OrderState::FLAG_DELIVERY)
				$delivery = true;
			if ($filters & OrderState::FLAG_LOGABLE)
				$logable = true;
			if ($filters & OrderState::FLAG_PAID)
				$paid = true;
			if ($filters & OrderState::FLAG_SHIPPED)
				$shipped = true;
		}
	
		if (!isset(self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters]) || $no_hidden)
		{
			$id_lang = $id_lang ? (int)($id_lang) : 'o.`id_lang`';
			$result = Db::getInstance()->executeS('
			SELECT oh.*, e.`firstname` AS employee_firstname, e.`lastname` AS employee_lastname, osl.`name` AS ostate_name, ot.`extras` AS `extras`
			FROM `'._DB_PREFIX_.'orders` o
			LEFT JOIN `'._DB_PREFIX_.'order_history` oh ON o.`id_order` = oh.`id_order`
			LEFT JOIN `'._DB_PREFIX_.'order_state` os ON os.`id_order_state` = oh.`id_order_state`
			LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)($id_lang).')
			LEFT JOIN `'._DB_PREFIX_.'employee` e ON e.`id_employee` = oh.`id_employee`
			LEFT JOIN `'._DB_PREFIX_.'orders_transporte` ot ON (o.`id_order` = ot.`id_order`)
			WHERE oh.id_order = '.(int)($this->id).'
			'.($no_hidden ? ' AND os.hidden = 0' : '').'
			'.($logable ? ' AND os.logable = 1' : '').'
			'.($delivery ? ' AND os.delivery = 1' : '').'
			'.($paid ? ' AND os.paid = 1' : '').'
			'.($shipped ? ' AND os.shipped = 1' : '').'
			'.((int)($id_order_state) ? ' AND oh.`id_order_state` = '.(int)($id_order_state) : '').'
			ORDER BY oh.date_add DESC, oh.id_order_history DESC');
			if ($no_hidden)
				return $result;
			self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters] = $result;
		}
		return self::$_historyCache[$this->id.'_'.$id_order_state.'_'.$filters];
	}
	

	public function addCartRule($id_cart_rule, $name, $values, $id_order_invoice = 0, $free_shipping = null)
	{
//		error_log("la orden ".$this->id." adiciono la regla: ".$id_cart_rule,0);
//		error_log(print_r(debug_backtrace(2), TRUE),0);
		
		$order_cart_rule = new OrderCartRule();
		$cart_rule = new CartRule($id_cart_rule);

		$order_cart_rule->id_order = $this->id;
		$order_cart_rule->id_cart_rule = $id_cart_rule;
		$order_cart_rule->id_order_invoice = $id_order_invoice;
		$order_cart_rule->name = $name;
		$order_cart_rule->value = $values['tax_incl'];
		$order_cart_rule->value_tax_excl = $values['tax_incl'];

		$order_cart_rule->reduction_percent = $cart_rule->reduction_percent;
		$order_cart_rule->reduction_amount = $cart_rule->reduction_amount;
                $order_cart_rule->reduction_product = $cart_rule->reduction_product;
                $order_cart_rule->gift_product = $cart_rule->reduction_product;
                
                

		if ($free_shipping === null)
		{
			$free_shipping = $cart_rule->free_shipping;
		}

		$order_cart_rule->free_shipping = (int)$free_shipping;

		$order_cart_rule->add();
	}


	public function addOrderPayment($amount_paid, $payment_method = null, $payment_transaction_id = null, $currency = null, $date = null, $order_invoice = null)
	{
		$order_payment = new OrderPayment();
		$order_payment->order_reference = $this->reference;
		$order_payment->id_currency = ($currency ? $currency->id : $this->id_currency);
		// we kept the currency rate for historization reasons
		$order_payment->conversion_rate = ($currency ? $currency->conversion_rate : 1);
		// if payment_method is define, we used this
		$order_payment->payment_method = ($payment_method ? $payment_method : $this->payment);
		$order_payment->transaction_id = $payment_transaction_id;
		$this->total_paid_real = $order_payment->amount = $amount_paid;
		$order_payment->date_add = ($date ? $date : null);

		// SE INHABILITA PORQUE SE DUPLICA EL COSTO DEL TOTAL PAID REAL, AL CUMPLIRSE LA VALIDACION DEL MISMO TIPO DE MONEDA
		// Update total_paid_real value for backward compatibility reasons
		/*if ($order_payment->id_currency == $this->id_currency)
			$this->total_paid_real += $order_payment->amount;
		else
			$this->total_paid_real += Tools::ps_round(Tools::convertPrice($order_payment->amount, $order_payment->id_currency, false), 2);
		*/

		// We put autodate parameter of add method to true if date_add field is null
		
		//echo "<br>order<br>";
		//echo "<pre>"; print_r($order_payment); echo "<br><hr>";
		$res = $order_payment->add(is_null($order_payment->date_add)) && $this->update();
		
		if (!$res)
			return false;
	
		if (!is_null($order_invoice))
		{
			$res = Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
			VALUES('.(int)$order_invoice->id.', '.(int)$order_payment->id.', '.(int)$this->id.')');

			// Clear cache
			Cache::clean('order_invoice_paid_*');
		}
		
		return $res;
	}
	/*-Habilita la posibilidad de generar ordenes de envío sin generar factura, creando un objeto tipo order cuando no hay factura generada-*/
	public function getInvoicesCollection()
	{
		if (Tools::isSubmit('noInvoice')) {
			$order_invoices = new Collection('Order');		
		}
		 else {
			$order_invoices = new Collection('OrderInvoice');
		}
		$order_invoices->where('id_order', '=', $this->id);
                if($order_invoices != null && !empty($order_invoices)) {
                    
                    if(isset($order_invoices[0])){
                        $order_invoices[0]->total_discount_tax_incl = ($order_invoices[0]->total_products_wt+$order_invoices[0]->total_shipping_tax_incl+$order_invoices[0]->total_wrapping_tax_incl-$order_invoices[0]->total_paid_tax_incl);
                    }else{
                        $order_invoices->total_discount_tax_incl = ($order_invoices->total_products_wt+$order_invoices->total_shipping_tax_incl+$order_invoices->total_wrapping_tax_incl-$order_invoices->total_paid_tax_incl);
                    }
                }

		return $order_invoices;
	}
        
        /**
	 * This method allows to generate first invoice of the current order
	 */
	public function setInvoice($use_existing_payment = false)
	{
		if (!$this->hasInvoice())
		{
			$order_invoice = new OrderInvoice();
			$order_invoice->id_order = $this->id;
			$order_invoice->number = Configuration::get('PS_INVOICE_START_NUMBER', null, null, $this->id_shop);
			// If invoice start number has been set, you clean the value of this configuration
			if ($order_invoice->number)
				Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $this->id_shop);
			else
				$order_invoice->number = Order::getLastInvoiceNumber() + 1;

			$invoice_address = new Address((int)$this->id_address_invoice);
			$carrier = new Carrier((int)$this->id_carrier);
			$tax_calculator = $carrier->getTaxCalculator($invoice_address);

			$order_invoice->total_discount_tax_excl = $this->total_discounts_tax_excl;
			$order_invoice->total_discount_tax_incl = $this->total_discounts_tax_incl;
			$order_invoice->total_paid_tax_excl = $this->total_paid_tax_excl;
			$order_invoice->total_paid_tax_incl = $this->total_paid_tax_incl;
			$order_invoice->total_products = $this->total_products;
			$order_invoice->total_products_wt = $this->total_products_wt;
			$order_invoice->total_shipping_tax_excl = $this->total_shipping_tax_excl;
			$order_invoice->total_shipping_tax_incl = $this->total_shipping_tax_incl;
			$order_invoice->shipping_tax_computation_method = $tax_calculator->computation_method;
			$order_invoice->total_wrapping_tax_excl = $this->total_wrapping_tax_excl;
			$order_invoice->total_wrapping_tax_incl = $this->total_wrapping_tax_incl;

			$contTime = 0;
                        while(Configuration::get('SEMAFORO_FACTURAS') == 1 && $contTime < 2){
                            time_nanosleep(0, 50000000);
                            $contTime++;
                        }
                        Configuration::updateValue('SEMAFORO_FACTURAS',1);            
                        if ($order_invoice->number)
                           Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $this->id_shop);
                        else
                          $order_invoice->number = Order::getLastInvoiceNumber($this->id_shop) + 1;

			// Save Order invoice
			$order_invoice->add();
                        
                        Configuration::updateValue('SEMAFORO_FACTURAS',0);

			$order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

			// Update order_carrier
			$id_order_carrier = Db::getInstance()->getValue('
				SELECT `id_order_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order` = '.(int)$order_invoice->id_order.'
				AND (`id_order_invoice` IS NULL OR `id_order_invoice` = 0)');
			
			if ($id_order_carrier)
			{
				$order_carrier = new OrderCarrier($id_order_carrier);
				$order_carrier->id_order_invoice = (int)$order_invoice->id;
				$order_carrier->update();
			}

			// Update order detail
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_detail`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

			// Update order payment
			if ($use_existing_payment)
			{
				$id_order_payments = Db::getInstance()->executeS('
					SELECT DISTINCT op.id_order_payment 
					FROM `'._DB_PREFIX_.'order_payment` op
					INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.reference = op.order_reference)
					LEFT JOIN `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment)					
					WHERE (oip.id_order != '.(int)$order_invoice->id_order.' OR oip.id_order IS NULL) AND o.id_order = '.(int)$order_invoice->id_order);

				if (count($id_order_payments))
				{
					foreach ($id_order_payments as $order_payment)
						Db::getInstance()->execute('
							INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
							SET
								`id_order_invoice` = '.(int)$order_invoice->id.',
								`id_order_payment` = '.(int)$order_payment['id_order_payment'].',
								`id_order` = '.(int)$order_invoice->id_order);
					// Clear cache
					Cache::clean('order_invoice_paid_*');
				}
			}

			// Update order cart rule
			Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_cart_rule`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

			// Keep it for backward compatibility, to remove on 1.6 version
			$this->invoice_date = $order_invoice->date_add;
			$this->invoice_number = $order_invoice->number;
			$this->update();
		}
	}
}