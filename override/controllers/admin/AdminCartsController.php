<?php
class AdminCartsController extends AdminCartsControllerCore
{
	protected $parameters = NULl;

	public function __construct()
	{
		$this->table = 'cart';
		$this->className = 'Cart';
		$this->lang = false;
		$this->explicitSelect = true;

		$this->addRowAction('view');
		$this->addRowAction('delete');
		$this->allow_export = true;

		$this->_select = 'CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) `customer`, a.id_cart total, ca.name carrier, o.id_order, IF(co.id_guest, 1, 0) id_guest';
		$this->_join = 'LEFT JOIN '._DB_PREFIX_.'customer c ON (c.id_customer = a.id_customer)
		LEFT JOIN '._DB_PREFIX_.'currency cu ON (cu.id_currency = a.id_currency)
		LEFT JOIN '._DB_PREFIX_.'carrier ca ON (ca.id_carrier = a.id_carrier)
		LEFT JOIN '._DB_PREFIX_.'orders o ON (o.id_cart = a.id_cart)
		LEFT JOIN `'._DB_PREFIX_.'connections` co ON (a.id_guest = co.id_guest AND TIME_TO_SEC(TIMEDIFF(NOW(), co.`date_add`)) < 1800)';

		$this->fields_list = array(
		                           'id_cart' => array(
		                                              'title' => $this->l('ID'),
		                                              'align' => 'center',
		                                              'width' => 25
		                                              ),
		                           'id_order' => array(
		                                               'title' => $this->l('Order ID'),
		                                               'align' => 'center', 'width' => 25
		                                               ),
		                           'customer' => array(
		                                               'title' => $this->l('Customer'),
		                                               'width' => 'auto',
		                                               'filter_key' => 'c!lastname'
		                                               ),
		                           'total' => array(
		                                            'title' => $this->l('Total'),
		                                            'callback' => 'getOrderTotalUsingTaxCalculationMethod',
		                                            'orderby' => false,
		                                            'search' => false,
		                                            'width' => 80,
		                                            'align' => 'right',
		                                            'prefix' => '<b>',
		                                            'suffix' => '</b>',
		                                            ),
		                           'carrier' => array(
		                                              'title' => $this->l('Carrier'),
		                                              'width' => 50,
		                                              'align' => 'center',
		                                              'callback' => 'replaceZeroByShopName',
		                                              'filter_key' => 'ca!name'
		                                              ),
		                           'date_add' => array(
		                                               'title' => $this->l('Date'),
		                                               'width' => 150,
		                                               'align' => 'right',
		                                               'type' => 'datetime',
		                                               'filter_key' => 'a!date_add'
		                                               ),
		                           'id_guest' => array(
		                                               'title' => $this->l('Online'),
		                                               'width' => 40,
		                                               'align' => 'center',
		                                               'type' => 'bool',
		                                               'havingFilter' => true,
		                                               'icon' => array(0 => 'blank.gif', 1 => 'tab-customers.gif')
		                                               )
		                           );
$this->shopLinkType = 'shop';

AdminController::__construct();

}

public function ajaxProcessAddDoctor(){
	Cart::addCartMedico(Tools::getValue('id_cart'),Tools::getValue('id_medico'));
}

protected function afterAdd($currentObject)
{
		// Add restrictions for generic entities like country, carrier and group
	foreach (array('country', 'carrier', 'group', 'shop') as $type)
		if (Tools::getValue($type.'_restriction') && is_array($array = Tools::getValue($type.'_select')) && count($array))
		{
			$values = array();
			foreach ($array as $id)
				$values[] = '('.(int)$currentObject->id.','.(int)$id.')';
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_'.$type.'` (`id_cart_rule`, `id_'.$type.'`) VALUES '.implode(',', $values));
		}
		// Add cart rule restrictions
		if (Tools::getValue('cart_rule_restriction') && is_array($array = Tools::getValue('cart_rule_select')) && count($array))
		{
			$values = array();
			foreach ($array as $id)
				$values[] = '('.(int)$currentObject->id.','.(int)$id.')';
			Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) VALUES '.implode(',', $values));
		}
		// Add product rule restrictions
		if (Tools::getValue('product_restriction') && is_array($ruleGroupArray = Tools::getValue('product_rule_group')) && count($ruleGroupArray))
		{
			foreach ($ruleGroupArray as $ruleGroupId)
			{
				Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
				                           VALUES ('.(int)$currentObject->id.', '.(int)Tools::getValue('product_rule_group_'.$ruleGroupId.'_quantity').')');
				$id_product_rule_group = Db::getInstance()->Insert_ID();

				if (is_array($ruleArray = Tools::getValue('product_rule_'.$ruleGroupId)) && count($ruleArray))
					foreach ($ruleArray as $ruleId)
					{
						Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
						                           VALUES ('.(int)$id_product_rule_group.', "'.pSQL(Tools::getValue('product_rule_'.$ruleGroupId.'_'.$ruleId.'_type')).'")');
						$id_product_rule = Db::getInstance()->Insert_ID();

						$values = array();
						foreach (Tools::getValue('product_rule_select_'.$ruleGroupId.'_'.$ruleId) as $id)
							$values[] = '('.(int)$id_product_rule.','.(int)$id.')';
						$values = array_unique($values);
						if (count($values))
							Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES '.implode(',', $values));
					}
				}
			}

		// If the new rule has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
			if (!Tools::getValue('cart_rule_restriction'))
			{
				Db::getInstance()->execute('
				                           INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
				                                                                                                                    SELECT id_cart_rule, '.(int)$currentObject->id.' FROM `'._DB_PREFIX_.'cart_rule` WHERE cart_rule_restriction = 1
				                                                                                                                    )');
			}
		// And if the new cart rule has restrictions, previously unrestricted cart rules may now be restricted (a mug of coffee is strongly advised to understand this sentence)
			else
			{
				$ruleCombinations = Db::getInstance()->executeS('
				                                                SELECT cr.id_cart_rule
				                                                FROM '._DB_PREFIX_.'cart_rule cr
				                                                WHERE cr.id_cart_rule != '.(int)$currentObject->id.'
				                                                AND cr.cart_rule_restriction = 0
				                                                AND cr.id_cart_rule NOT IN (
				                                                                            SELECT IF(id_cart_rule_1 = '.(int)$currentObject->id.', id_cart_rule_2, id_cart_rule_1)
				                                                                            FROM '._DB_PREFIX_.'cart_rule_combination
				                                                                            WHERE '.(int)$currentObject->id.' = id_cart_rule_1
				                                                                            OR '.(int)$currentObject->id.' = id_cart_rule_2
				                                                                            )');
foreach ($ruleCombinations as $incompatibleRule)
{
	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'cart_rule` SET cart_rule_restriction = 1 WHERE id_cart_rule = '.(int)$incompatibleRule['id_cart_rule'].' LIMIT 1');
	Db::getInstance()->execute('
	                           INSERT IGNORE INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
	                                                                                                                           SELECT id_cart_rule, '.(int)$incompatibleRule['id_cart_rule'].' FROM `'._DB_PREFIX_.'cart_rule`
	                                                                                                                           WHERE active = 1
	                                                                                                                           AND id_cart_rule != '.(int)$currentObject->id.'
	                                                                                                                           AND id_cart_rule != '.(int)$incompatibleRule['id_cart_rule'].'
	                                                                                                                           )');
}
}
}

public function displayAjaxSearchCarts()
{	

	$id_customer = (int)Tools::getValue('id_customer');
	$carts = Cart::getCustomerCarts((int)$id_customer);
	$orders = Order::getCustomerOrders((int)$id_customer);
	$customer = new Customer((int)$id_customer);

	if (count($carts))
		foreach ($carts as $key => &$cart)
		{
			$cart_obj = new Cart((int)$cart['id_cart']);
			if ($cart['id_cart'] == $this->context->cart->id || !Validate::isLoadedObject($cart_obj) || $cart_obj->OrderExists())
				unset($carts[$key]);
			$currency = new Currency((int)$cart['id_currency']);
			$cart['total_price'] = Tools::displayPrice($cart_obj->getOrderTotal(), $currency);
		}
		if (count($orders))
			foreach ($orders as &$order)
				$order['total_paid_real'] = Tools::displayPrice($order['total_paid_real'], $currency);
			if ($orders || $carts)
				$to_return = array_merge($this->ajaxReturnVars(),
				                         array('carts' => $carts,
				                               'orders' => $orders,
				                               'found' => true));
			else
				$to_return = array_merge($this->ajaxReturnVars(), array('found' => false));

			echo Tools::jsonEncode($to_return);
		}

		public function ajaxProcessUpdateDeliveryOption()
		{
			if ($this->tabAccess['edit'] === '1')
			{	
				$delivery_option = Tools::getValue('delivery_option');			
				if ($delivery_option !== false)
					$this->context->cart->setDeliveryOption(array($this->context->cart->id_address_delivery => $delivery_option));
				if (Validate::isBool(($recyclable = (int)Tools::getValue('recyclable'))))
					$this->context->cart->recyclable = $recyclable;
				if (Validate::isBool(($gift = (int)Tools::getValue('gift'))))
					$this->context->cart->gift = $gift;
				if (Validate::isMessage(($gift_message = pSQL(Tools::getValue('gift_message')))))
					$this->context->cart->gift_message = $gift_message;
				$this->entrega_nocturna();
				if (Tools::getValue('express')){

					Context::getContext()->cookie->check_xps = true;
				}else{
					Context::getContext()->cookie->check_xps = false;
				}			
				$this->context->cart->save();

// 			if (Tools::getValue('express'))
// 				//echo "<pre>";;echo "</pre>";
				echo Tools::jsonEncode($this->ajaxReturnVars());
			}
		}

		public function ajaxProcessUpdateAddresses()
		{

			if ($this->tabAccess['edit'] === '1')
			{
				if (($id_address_delivery = (int)Tools::getValue('id_address_delivery')) &&
				    ($address_delivery = new Address((int)$id_address_delivery)) &&
				    $address_delivery->id_customer == $this->context->cart->id_customer)
					$this->context->cart->id_address_delivery = (int)$address_delivery->id;

				if (($id_address_invoice = (int)Tools::getValue('id_address_invoice')) &&
				    ($address_invoice = new Address((int)$id_address_invoice)) &&
				    $address_invoice->id_customer = $this->context->cart->id_customer)
					$this->context->cart->id_address_invoice = (int)$address_invoice->id;
				$this->entrega_nocturna();
				if (Tools::getValue('express')){

					Context::getContext()->cookie->check_xps = true;
				}else{
					Context::getContext()->cookie->check_xps = false;
				}			
				$this->context->cart->save();

				echo Tools::jsonEncode($this->ajaxReturnVars());
			}
		}

		public function ajaxReturnVars()
		{
			if($this->parameters === NULL)
			{
				$this->parameters = Utilities::get_parameters();
			}

			$id_cart = (int)$this->context->cart->id;
			$message_content = '';
			if ($message = Message::getMessageByCartId((int)$this->context->cart->id))
				$message_content = $message['message'];
			$cart_rules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_SHIPPING);

			$free_shipping = false;
			if (count($cart_rules))
				foreach ($cart_rules as $cart_rule)
					if ($cart_rule['id_cart_rule'] == CartRule::getIdByCode('BO_ORDER_'.(int)$this->context->cart->id))
					{
						$free_shipping = true;
						break;
					}

					$address=$this->context->customer->getAddresses((int)$this->context->cart->id_lang);

					for($i=0; $i <count($address); $i++){
						if(Utilities::is_rules_entrega_nocturna($this->parameters['id_regla_entrga_nocturna']) ){

							if($result=Utilities::is_localidad_barrio($address[$i]['id_address']) && count($result)>0 && Utilities::show_select_localidad($result['id_localidad'],$result['id_barrio'])){
								$address[$i]['entrega_nocturna']= $this->parameters['valor'] ;
							}  else {                 
								$address[$i]['entrega_nocturna']= $this->parameters['valor'] ;
								$address[$i]['update_address_nocturno']= TRUE;

								$localidades= Utilities::get_list_localidades(); 
                                    //trigger_error(' || #Express: '.print_r((int)Tools::getValue('express'),TRUE).' || ', E_USER_NOTICE);
								$str_localidades='<option>Localidades</option>';
								if ( count($localidades) > 0 &&  !empty ($localidades)) {
									foreach ($localidades as $row) {
										$str_localidades .= '<option value="' . $row['id_localidad'] .'">' . $row['nombre_localidad'] . '</option>';
									}
									$array_result = array('results' => $str_localidades);
									$address[$i]['list_localidades']= utf8_encode($str_localidades);

								}         

							}                        
						}                


					}                

					return array(
					             'summary' => $this->getCartSummary(),
					             'delivery_option_list' => $this->getDeliveryOptionList(),
					             'cart' => $this->context->cart,
					             'currency' => new Currency($this->context->cart->id_currency),
					             'addresses' => $this->context->customer->getAddresses((int)$this->context->cart->id_lang),
					             'id_cart' => $id_cart,
					             'order_message' => $message_content,
					             'link_order' => $this->context->link->getPageLink(
					                                                               'order', false,
					                                                               (int)$this->context->cart->id_lang,
					                                                               'step=3&recover_cart='.$id_cart.'&token_cart='.md5(_COOKIE_KEY_.'recover_cart_'.$id_cart)
					                                                               ),
					             'free_shipping' => (int)$free_shipping,
					             'productExpress' => $this->getProductxps(),
					             'is_entrega_nocturna' => Utilities::is_rules_entrega_nocturna()
					             );
}

public function entrega_nocturna() {

	if($this->parameters === NULL)
	{
		$this->parameters = Utilities::get_parameters();
	}
	
	$this->context->smarty->assign('paramEntregaNocturana',$this->parameters);
	$this->valor_entrega_nocturna = isset($this->parameters['valor']) ? $this->parameters['valor'] : 0 ;
              // validación para control de inventario  
	$opcional = true;
              // si se requiere mostrar el envió de nocturno a partir de la disponibilidad de inventario
	if(isset($this->parameters['existencias']) && $this->parameters['existencias'] === '1'){
              	if($this->context->cart->expressProduct()){ // si existen productos en inventario
              		$opcional = true;
              	}else{
              		$opcional = false;
              	}
              } 


            // valida si se debe mostrar la opción de entrega nocturna
              if(Utilities::is_rules_entrega_nocturna($this->parameters['id_regla_entrga_nocturna']) && $opcional ){
                // valida si la dirección actual tiene localidad y barrio, y si se debe mostrar la entrega nocturna
              	$result = Utilities::is_localidad_barrio((int)$this->context->cart->id_address_delivery);

              	if(Utilities::show_select_localidad($result)){
              		$this->context->smarty->assign('entregaNocturnaEnabled','enabled');
              		$this->context->smarty->assign('localidadesBarriosEnabled','disabled');
              		$this->context->smarty->assign('entregaNocturna',Context::getContext()->cookie->entrega_nocturna);
              		if($this->parameters['auto_load']){
              			Context::getContext()->cookie->entrega_nocturna = 'enabled';
              		}
                   // Context::getContext()->cookie->check_xps = true;
                    //    trigger_error(' || Mostrar envio nocturno || ', E_USER_NOTICE);
              		return TRUE;
              	}else{
              		$this->context->smarty->assign('entregaNocturnaEnabled','enabled');
              		$this->context->smarty->assign('localidadesBarriosEnabled','enabled');


              		$localidades= Utilities::get_list_localidades(); 
              		$str_localidades='';
              		if ( count($localidades) > 0 &&  !empty ($localidades)) {
              			foreach ($localidades as $row) {
              				$str_localidades .= '<option value="' . $row['id_localidad'] .'">' . $row['nombre_localidad'] . '</option>';
              			}
              		}
              		$this->context->smarty->assign('list_localidades',$str_localidades);

                     //trigger_error(' || Mostrar envio nocturno y actualizar dirección || ', E_USER_NOTICE);


              		$this->context->smarty->assign('entregaNocturna',Context::getContext()->cookie->entrega_nocturna);

                     //Context::getContext()->cookie->check_xps = true;
              		if($this->parameters['auto_load']){
              			Context::getContext()->cookie->entrega_nocturna = 'enabled';
              		}
              		return TRUE;
              	}

              }
              $this->context->smarty->assign('localidadesBarriosEnabled','disabled');
              $this->context->smarty->assign('entregaNocturnaEnabled','disabled');

              Context::getContext()->cookie->entrega_nocturna = 'disabled';
              $this->context->smarty->assign('entregaNocturna',Context::getContext()->cookie->entrega_nocturna);
              return FALSE;
          }


          public function ajaxPreProcess()
          {

          	if ($this->tabAccess['edit'] === '1')
          	{
          		$id_customer = (int)Tools::getValue('id_customer');
          		$customer = new Customer((int)$id_customer);
          		$this->context->customer = $customer;
          		$id_cart = (int)Tools::getValue('id_cart');
          		if (!$id_cart)
          			$id_cart = $customer->getLastCart(false);
          		$this->context->cart = new Cart((int)$id_cart);

          		if (!$this->context->cart->id)
          		{
          			$this->context->cart->recyclable = 0;
          			$this->context->cart->gift = 0;
          		}

          		if (!$this->context->cart->id_customer)
          			$this->context->cart->id_customer = $id_customer;
          		if (Validate::isLoadedObject($this->context->cart) && $this->context->cart->OrderExists())
          			return;

		// Verificación productos en lista negra, opciones (Usar este carro/orden)
          		if(isset($this->context->cart->id) && !empty($this->context->cart->id))
          		{
			// Asociar empleado al carro de compra
          			if(isset($this->context->employee->id) && !empty($this->context->employee->id)  && !$this->is_cart_employee() ) {

          				Db::getInstance()->update('cart',
          				                          array(
          				                                'id_employee' => (int) $this->context->employee->id,
          				                                ),
          				                          'id_cart = '.(int) $this->context->cart->id);
          			} 
          			$prod_to_valid = array();
          			$products = $this->context->cart->getProducts();
          			foreach ($products as $product) { 
          				$prod_to_valid[] = $product['id_product'];
          			}

          			$sql = "SELECT id_product
          			FROM "._DB_PREFIX_."product_black_list
          			WHERE id_product IN(".implode(',', $prod_to_valid).")";
          			$blank_list = Db::getInstance()->executeS($sql);
          			if(!empty($blank_list) && count($blank_list) > 0 ){
          				foreach ($blank_list as $key => $value) {
          					$this->context->cart->deleteProduct((int) $value['id_product']);
          				}
          				$this->context->cart->update();
          			}
          		}

          		if (!$this->context->cart->secure_key)
          			$this->context->cart->secure_key = $this->context->customer->secure_key;
          		if (!$this->context->cart->id_shop)
          			$this->context->cart->id_shop = (int)$this->context->shop->id;
          		if (!$this->context->cart->id_lang)
          			$this->context->cart->id_lang = (($id_lang = (int)Tools::getValue('id_lang')) ? $id_lang : Configuration::get('PS_LANG_DEFAULT'));
          		if (!$this->context->cart->id_currency)
          			$this->context->cart->id_currency = (($id_currency = (int)Tools::getValue('id_currency')) ? $id_currency : Configuration::get('PS_CURRENCY_DEFAULT'));

          		$addresses = $customer->getAddresses((int)$this->context->cart->id_lang);
          		$id_address_delivery = (int)Tools::getValue('id_address_delivery');
          		$id_address_invoice = (int)Tools::getValue('id_address_delivery');

          		if (!$this->context->cart->id_address_invoice && isset($addresses[0]))
          			$this->context->cart->id_address_invoice = (int)$addresses[0]['id_address'];
          		elseif ($id_address_invoice)
          			$this->context->cart->id_address_invoice = (int)$id_address_invoice;
          		if (!$this->context->cart->id_address_delivery && isset($addresses[0]))
          			$this->context->cart->id_address_delivery = $addresses[0]['id_address'];
          		elseif ($id_address_delivery)
          			$this->context->cart->id_address_delivery = (int)$id_address_delivery;
          		$this->context->cart->setNoMultishipping();
          		$this->context->cart->save();
          		$currency = new Currency((int)$this->context->cart->id_currency);
          		$this->context->currency = $currency;
          	}
          }

          public function ajaxProcessDuplicateOrder()
          {
          	if ($this->tabAccess['edit'] === '1')
          	{
          		$errors = array();
          		if (!$id_order = Tools::getValue('id_order'))
          			$errors[] = Tools::displayError('Invalid order');
          		$cart = Cart::getCartByOrderId($id_order);
          		$new_cart = $cart->duplicate();
          		if (!$new_cart || !Validate::isLoadedObject($new_cart['cart']))
          			$errors[] = Tools::displayError('The order cannot be renewed.');
          		else if (!$new_cart['success'])
          			$errors[] = Tools::displayError('The order cannot be renewed.');
          		else
          		{
          			$this->context->cart = $new_cart['cart'];
							// Asociar empleado al carro de compra
          			if(isset($this->context->employee->id) && !empty($this->context->employee->id) && !$this->is_cart_employee() ) {

          				Db::getInstance()->update('cart',
          				                          array(
          				                                'id_employee' => (int) $this->context->employee->id,
          				                                ),
          				                          'id_cart = '.(int) $this->context->cart->id);
          			}
          			echo Tools::jsonEncode($this->ajaxReturnVars());
          		}
          	}
          }                

          public function is_cart_employee(){
          	$sql = "SELECT IF( id_employee IS NULL,0, id_employee) as id_employee
          	FROM "._DB_PREFIX_."cart
          	WHERE id_cart = ".(int) $this->context->cart->id;
          	if(Db::getInstance()->getValue($sql) == 0)
          		return false;
          	return true;
          } 

      }


      ?>
