<?php


class OrderHistory extends OrderHistoryCore
{
	/**
	 * @param bool $autodate Optional
	 * @param array $template_vars Optional
	 * @param Context $context Optional
	 * @return bool
	 */
	public function addWithemail($autodate = true, $template_vars = false, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		$order = new Order($this->id_order);
		
		if (!$this->add($autodate))
			return false;

		$result = Db::getInstance()->getRow('
			SELECT osl.`template`, c.`lastname`, c.`firstname`, osl.`name` AS osname, c.`email`, os.`module_name`, os.`id_order_state`
			FROM `'._DB_PREFIX_.'order_history` oh
				LEFT JOIN `'._DB_PREFIX_.'orders` o ON oh.`id_order` = o.`id_order`
				LEFT JOIN `'._DB_PREFIX_.'customer` c ON o.`id_customer` = c.`id_customer`
				LEFT JOIN `'._DB_PREFIX_.'order_state` os ON oh.`id_order_state` = os.`id_order_state`
				LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = o.`id_lang`)
			WHERE oh.`id_order_history` = '.(int)$this->id.' AND os.`send_email` = 1');
		if (isset($result['template']) && Validate::isEmail($result['email']))
		{
			if ($order->id) {
				$id_order_state = (int)$result['id_order_state'];
				if ( $id_order_state !== 8 && $id_order_state !== 18 && $id_order_state !== 6 && $id_order_state !== 4 
						&& $id_order_state !== 22 && $id_order_state !== 20 && $id_order_state !== 5 && $id_order_state !== 10) {

					// Genrenando el descuento de los reservados en el stock
					$sql = new DbQuery();
					$sql->select('pp.id_product, pp.quantity');
					$sql->from('orders', 'po');
					$sql->leftJoin('cart_product', 'pp', 'po.id_cart = pp.id_cart');
					$sql->where('po.id_cart = ' . pSQL($order->id_cart).' AND pp.id_product NOT IN (
					SELECT rp.id_product FROM ps_reserve_product rp
					WHERE rp.id_product = pp.id_product AND rp.id_order = po.id_order
					)');
					$result = Db::getInstance()->executeS($sql);
					$sin_stock = false;
					if(count($result) > 0) {
						foreach ($result as $row) {
							$sql = new DbQuery();
							$sql->select('s.quantity, s.reserve_on_stock');
							$sql->from('stock_available_mv', 's');
							$sql->where('s.id_product = ' . pSQL($row['id_product']));
							$result2 = Db::getInstance()->executeS($sql);
							if($result2[0]['quantity'] < ($result2[0]['reserve_on_stock'] + $row['quantity'])) {
								$sin_stock = true;
							}
							$sqlReserve = new DbQuery();
							$sqlReserve->select('SUM(quantity_reserve) AS quantity_reserve');
							$sqlReserve->from('reserve_product');
							$sqlReserve->where('id_product = ' . pSQL($row['id_product']));
							$sqlReserve->groupBy('id_product');
							$resultReserve = Db::getInstance()->executeS($sqlReserve);
							if(count($resultReserve) > 0) {
								$quantity_reserve = $resultReserve[0]['quantity_reserve'];
							} else {
								$quantity_reserve = 0;
							}
							$stock = $result2[0]['quantity'] - $result2[0]['reserve_on_stock'];
							$totalStock = ($stock < 0 ? 0 : $stock);
							if($quantity_reserve >= $result2[0]['quantity']) {
								$reserve = 0;
								$missing = $row['quantity'];
							} else if($row['quantity'] >= $totalStock) {
								$reserve = $totalStock;
								$missing = $row['quantity'] - ($totalStock < 0 ? 0 : $totalStock);
							} else {
								$reserve = $row['quantity'];
								$missing = 0;
							}
							if (isset($result2[0])) {
								$newReserveOnStock = $result2[0]['reserve_on_stock'] + $row['quantity'];
								$sql_new_reserve = 'UPDATE ' . _DB_PREFIX_ . 'stock_available_mv
																SET reserve_on_stock = ' . $newReserveOnStock . '
																WHERE id_product = ' . pSQL($row['id_product']);
								Db::getInstance()->executeS($sql_new_reserve);
							}
							if($missing > 0) {
								$reserve_products = 'INSERT INTO ' . _DB_PREFIX_ . 'reserve_product(id_order, id_product, quantity_reserve, missing_quantity)
													VALUES('.$order->id.','.pSQL($row['id_product']).',' . $reserve . ',' . $missing . ')';
								Db::getInstance()->executeS($reserve_products);
							}
						}
						//Revisa que no haya stock y que el estado no se encuentre en un estado especifico
						if($sin_stock && !in_array($id_order_state, array(Configuration::get('PS_OS_ARRAY_IN_STOCK')))) {
							$this->changeIdOrderState(Configuration::get('PS_OS_OUTOFSTOCK'), $order, true);
							parent::addWithemail();
						}
					}
				}
			}
			ShopUrl::cacheMainDomainForShop($order->id_shop);
			
			$topic = $result['osname'];
			$data = array(
				'{lastname}' => $result['lastname'],
				'{firstname}' => $result['firstname'],
				'{id_order}' => (int)$this->id_order,
				'{order_name}' => $order->getUniqReference()
			);
			if ($template_vars)
				$data = array_merge($data, $template_vars);

			if ($result['module_name'])
			{
				$module = Module::getInstanceByName($result['module_name']);
				if (Validate::isLoadedObject($module) && isset($module->extra_mail_vars) && is_array($module->extra_mail_vars))
					$data = array_merge($data, $module->extra_mail_vars);
			}
			
			$data['{total_paid}'] = Tools::displayPrice((float)$order->total_paid, new Currency((int)$order->id_currency), false);
			$data['{order_name}'] = $order->getUniqReference();

			if (Validate::isLoadedObject($order))
			{
				// Join PDF invoice if order state is "payment accepted"
				if ( ( (int)$result['id_order_state'] === 5 || (int)$result['id_order_state'] === 5 ) && (int)Configuration::get('PS_INVOICE') && $order->invoice_number)
				{
					$context = Context::getContext();
					$pdf = new PDF($order->getInvoicesCollection(), PDF::TEMPLATE_INVOICE, $context->smarty);
					$file_attachement[0]['content'] = $pdf->render(false);
					$file_attachement[0]['name'] = Configuration::get('PS_INVOICE_PREFIX', (int)$order->id_lang, null, $order->id_shop).sprintf('%06d', $order->invoice_number).'.pdf';
					$file_attachement[0]['mime'] = 'application/pdf';

					$facturaxion = new Facturaxion();
					$retorno_timbrado = $facturaxion->RegistroTimbrado( $order->id );

					if ( is_array($retorno_timbrado) && $retorno_timbrado != 0 ) {

						$url = $retorno_timbrado['rutaxml'];
						$xml = file_get_contents($url);
							//echo "<br><textarea cols='150' rows='10'>";
							//echo $xml."</textarea>";
							//exit();
						$file_attachement[1]['content'] = $xml;
						$file_attachement[1]['name'] = 'xml_timbrado_'.$order->id.'_.xml';
						$file_attachement[1]['mime'] = 'application/xml';
					}

				}
				else
					$file_attachement = null;

				Mail::Send((int)$order->id_lang, $result['template'], $topic, $data, $result['email'], $result['firstname'].' '.$result['lastname'],
					null, null, $file_attachement, null, _PS_MAIL_DIR_, false, (int)$order->id_shop);
			}

			ShopUrl::resetMainDomainCache();
		}

		return true;
	}


	public function changeIdOrderState($new_order_state, $id_order, $use_existing_payment = false)
	{
		if (!$new_order_state || !$id_order)
			return;

		if (!is_object($id_order) && is_numeric($id_order))
			$order = new Order((int)$id_order);
		elseif (is_object($id_order))
			$order = $id_order;
		else
			return;

		ShopUrl::cacheMainDomainForShop($order->id_shop);

		$new_os = new OrderState((int)$new_order_state, $order->id_lang);
		$old_os = $order->getCurrentOrderState();
		$is_validated = $this->isValidated();
		

		// executes hook
		if (in_array($new_os->id, array(Configuration::get('PS_OS_PAYMENT'), Configuration::get('PS_OS_WS_PAYMENT'))))
			Hook::exec('actionPaymentConfirmation', array('id_order' => (int)$order->id));

		// executes hook
		Hook::exec('actionOrderStatusUpdate', array(
			'newOrderStatus' => $new_os,
			'id_order' => (int)$order->id
		));

		if (Validate::isLoadedObject($order) && ($new_os instanceof OrderState))
		{
			// An email is sent the first time a virtual item is validated
			$virtual_products = $order->getVirtualProducts();
			if ($virtual_products && (!$old_os || !$old_os->logable) && $new_os && $new_os->logable)
			{
				$context = Context::getContext();
				$assign = array();
				foreach ($virtual_products as $key => $virtual_product)
				{
					$id_product_download = ProductDownload::getIdFromIdProduct($virtual_product['product_id']);
					$product_download = new ProductDownload($id_product_download);
					// If this virtual item has an associated file, we'll provide the link to download the file in the email
					if ($product_download->display_filename != '')
					{
						$assign[$key]['name'] = $product_download->display_filename;
						$dl_link = $product_download->getTextLink(false, $virtual_product['download_hash'])
							.'&id_order='.(int)$order->id
							.'&secure_key='.$order->secure_key;
						$assign[$key]['link'] = $dl_link;
						if (isset($virtual_product['download_deadline']) && $virtual_product['download_deadline'] != '0000-00-00 00:00:00')
							$assign[$key]['deadline'] = Tools::displayDate($virtual_product['download_deadline']);
						if ($product_download->nb_downloadable != 0)
							$assign[$key]['downloadable'] = (int)$product_download->nb_downloadable;
					}
				}
								
				$customer = new Customer((int)$order->id_customer);
				
				$links = '<ul>';
				foreach($assign as $product)
				{
					$links .= '<li>';
					$links .= '<a href="'.$product['link'].'">'.Tools::htmlentitiesUTF8($product['name']).'</a>';
					if (isset($product['deadline']))
						$links .= '&nbsp;'.Tools::htmlentitiesUTF8(Tools::displayError('expires on')).'&nbsp;'.$product['deadline'];
					if (isset($product['downloadable']))
						$links .= '&nbsp;'.Tools::htmlentitiesUTF8(sprintf(Tools::displayError('downloadable %d time(s)'), (int)$product['downloadable']));	
					$links .= '</li>';
				}
				$links .= '</ul>';
				$data = array(
						'{lastname}' => $customer->lastname,
						'{firstname}' => $customer->firstname,
						'{id_order}' => (int)$order->id,
						'{order_name}' => $order->getUniqReference(),
						'{nbProducts}' => count($virtual_products),
						'{virtualProducts}' => $links
					);
				// If there's at least one downloadable file
				if (!empty($assign))
					Mail::Send((int)$order->id_lang, 'download_product', Mail::l('Virtual product to download', $order->id_lang), $data, $customer->email, $customer->firstname.' '.$customer->lastname,
						null, null, null, null, _PS_MAIL_DIR_, false, (int)$order->id_shop);
			}

			// @since 1.5.0 : gets the stock manager
			$manager = null;
			if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
				$manager = StockManagerFactory::getManager();
				
			$errorOrCanceledStatuses = array(Configuration::get('PS_OS_ERROR'), Configuration::get('PS_OS_CANCELED'));
			
			// foreach products of the order
			if (Validate::isLoadedObject($old_os))			
				foreach ($order->getProductsDetail() as $product)
				{
					// if becoming logable => adds sale
					if ($new_os->logable && !$old_os->logable)
					{
						ProductSale::addProductSale($product['product_id'], $product['product_quantity']);
						// @since 1.5.0 - Stock Management
						if (!Pack::isPack($product['product_id']) &&
							in_array($old_os->id, $errorOrCanceledStatuses) &&
							!StockAvailable::dependsOnStock($product['id_product'], (int)$order->id_shop))
							StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], -(int)$product['product_quantity'], $order->id_shop);
					}
					// if becoming unlogable => removes sale
					elseif (!$new_os->logable && $old_os->logable)
					{
						ProductSale::removeProductSale($product['product_id'], $product['product_quantity']);
	
						// @since 1.5.0 - Stock Management
						if (!Pack::isPack($product['product_id']) &&
							in_array($new_os->id, $errorOrCanceledStatuses) &&
							!StockAvailable::dependsOnStock($product['id_product']))
							StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
					}
					// if waiting for payment => payment error/canceled
					elseif (!$new_os->logable && !$old_os->logable &&
							 in_array($new_os->id, $errorOrCanceledStatuses) &&
							 !in_array($old_os->id, $errorOrCanceledStatuses) &&
							 !StockAvailable::dependsOnStock($product['id_product']))
							 StockAvailable::updateQuantity($product['product_id'], $product['product_attribute_id'], (int)$product['product_quantity'], $order->id_shop);
					// @since 1.5.0 : if the order is being shipped and this products uses the advanced stock management :
					// decrements the physical stock using $id_warehouse
					if ($new_os->shipped == 1 && $old_os->shipped == 0 &&
						Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
						Warehouse::exists($product['id_warehouse']) &&
						$manager != null &&
						((int)$product['advanced_stock_management'] == 1 || Pack::usesAdvancedStockManagement($product['product_id'])))
					{
						// gets the warehouse
						$warehouse = new Warehouse($product['id_warehouse']);
	
						// decrements the stock (if it's a pack, the StockManager does what is needed)
						$manager->removeProduct(
							$product['product_id'],
							$product['product_attribute_id'],
							$warehouse,
							$product['product_quantity'],
							Configuration::get('PS_STOCK_CUSTOMER_ORDER_REASON'),
							true,
							(int)$order->id
						);
					}
					// @since.1.5.0 : if the order was shipped, and is not anymore, we need to restock products
					elseif ($new_os->shipped == 0 && $old_os->shipped == 1 &&
							 Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') &&
							 Warehouse::exists($product['id_warehouse']) &&
							 $manager != null &&
							 ((int)$product['advanced_stock_management'] == 1 || Pack::usesAdvancedStockManagement($product['product_id'])))
					{
						// if the product is a pack, we restock every products in the pack using the last negative stock mvts
						if (Pack::isPack($product['product_id']))
						{
							$pack_products = Pack::getItems($product['product_id'], Configuration::get('PS_LANG_DEFAULT', null, null, $order->id_shop));
							foreach ($pack_products as $pack_product)
							{
								if ($pack_product->advanced_stock_management == 1)
								{
									$mvts = StockMvt::getNegativeStockMvts($order->id, $pack_product->id, 0, $pack_product->pack_quantity * $product['product_quantity']);
									foreach ($mvts as $mvt)
									{
										$manager->addProduct(
											$pack_product->id,
											0,
											new Warehouse($mvt['id_warehouse']),
											$mvt['physical_quantity'],
											null,
											$mvt['price_te'],
											true
										);
									}
									if (!StockAvailable::dependsOnStock($product['id_product']))
										StockAvailable::updateQuantity($pack_product->id, 0, (int)$pack_product->pack_quantity * $product['product_quantity'], $order->id_shop);
								}
							}
						}
						// else, it's not a pack, re-stock using the last negative stock mvts
						else
						{
							$mvts = StockMvt::getNegativeStockMvts($order->id, $product['product_id'], $product['product_attribute_id'], $product['product_quantity']);
							foreach ($mvts as $mvt)
							{
								$manager->addProduct(
									$product['product_id'],
									$product['product_attribute_id'],
									new Warehouse($mvt['id_warehouse']),
									$mvt['physical_quantity'],
									null,
									$mvt['price_te'],
									true
								);
							}
						}
					}
				}
		}

		$this->id_order_state = (int)$new_order_state;
		
		// changes invoice number of order ?
		if (!Validate::isLoadedObject($new_os) || !Validate::isLoadedObject($order))
			die(Tools::displayError('Invalid new order state'));

		// the order is valid if and only if the invoice is available and the order is not cancelled
		$order->current_state = $this->id_order_state;
		$order->valid = $new_os->logable;
		$order->update();

		if ( $this->id_order_state == 5 ) {
			$resultqualify = Db::getInstance()->execute("
					INSERT INTO "._DB_PREFIX_."order_quality (
						id_order,
						date_change_state,
						remember
					) VALUES (
						".$order->id.",
						NOW(),
						1
					) ON DUPLICATE KEY UPDATE date_change_state = NOW()
			");
		}
		
		$orderStateSig = (int)Configuration::get(Configuration::get('SIGNATURE_CFDI'));
		
		if(isset($orderStateSig) && $orderStateSig !=0 && $orderStateSig == (int)$order->current_state){
		    $sig = new SignatureCFDI($order);
		    $sig->sigCDFI();		    
		}

		if ($new_os->invoice && !$order->invoice_number)
			$order->setInvoice($use_existing_payment);

		// set orders as paid
		if ($new_os->paid == 1)
		{
			$invoices = $order->getInvoicesCollection();
			if ($order->total_paid != 0)
				$payment_method = Module::getInstanceByName($order->module);

			foreach ($invoices as $invoice)
			{
				$rest_paid = $invoice->getRestPaid();
				if ($rest_paid > 0)
				{
					$payment = new OrderPayment();
					$payment->order_reference = $order->reference;
					$payment->id_currency = $order->id_currency;
					$payment->amount = $rest_paid;

					if ($order->total_paid != 0)
						$payment->payment_method = $payment_method->displayName;
					else 
						$payment->payment_method = null;
					
					// Update total_paid_real value for backward compatibility reasons
					if ($payment->id_currency == $order->id_currency)
						$order->total_paid_real += $payment->amount;
					else
						$order->total_paid_real += Tools::ps_round(Tools::convertPrice($payment->amount, $payment->id_currency, false), 2);
					$order->save();
						
					$payment->conversion_rate = 1;
					$payment->save();
					Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
					VALUES('.(int)$invoice->id.', '.(int)$payment->id.', '.(int)$order->id.')');
				}
			}
		}

		// updates delivery date even if it was already set by another state change
		if ($new_os->delivery)
			$order->setDelivery();

		// executes hook
		Hook::exec('actionOrderStatusPostUpdate', array(
			'newOrderStatus' => $new_os,
			'id_order' => (int)$order->id,
		));

		ShopUrl::resetMainDomainCache();
	}

}
