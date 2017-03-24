<?php
class AdminCustomersController extends AdminCustomersControllerCore
{
	public function renderForm()
	{

		if (!($obj = $this->loadObject(true)))
			return;

		$genders = Gender::getGenders();
		$list_genders = array();
		foreach ($genders as $key => $gender)
		{
			$list_genders[$key]['id'] = 'gender_'.$gender->id;
			$list_genders[$key]['value'] = $gender->id;
			$list_genders[$key]['label'] = $gender->name;
		}

		$years = Tools::dateYears();
		$months = Tools::dateMonths();
		$days = Tools::dateDays();

		$groups = Group::getGroups($this->default_form_language, true);

		$this->context->smarty->assign('validateEmailCustomer', 'validateEmailCustomer');

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Customer'),
				'image' => '../img/admin/tab-customers.gif'
			),
			'input' => array(
				array(
					'type' => 'radio',
					'label' => $this->l('Title:'),
					'name' => 'id_gender',
					'required' => false,
					'class' => 't',
					'values' => $list_genders
				),
				array(
					'type' => 'text',
					'label' => $this->l('Identificación:'),
					'name' => 'identification',
					'size' => 20,
					'required' => true,
					'hint' => $this->l('Forbidden characters:').' a-zA-Z!<>,;?=+()@#"�{}_$%:'
				),
				array(
					'type' => 'text',
					'label' => $this->l('First name:'),
					'name' => 'firstname',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Forbidden characters:').' 0-9!<>,;?=+()@#"�{}_$%:'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Last name:'),
					'name' => 'lastname',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Email address:'),
					'name' => 'email',
					'size' => 33,
					'required' => true
				),
				array(
					'type' => 'password',
					'label' => $this->l('Password:'),
					'name' => 'passwd',
					'size' => 33,
					'required' => ($obj->id ? false : true),
					'desc' => ($obj->id ? $this->l('Leave  this field blank if there\'s no change') : $this->l('Minimum of five characters (only letters and numbers).').' -_')
				),
				array(
					'type' => 'birthday',
					'label' => $this->l('Birthday:'),
					'name' => 'birthday',
					'options' => array(
						'days' => $days,
						'months' => $months,
						'years' => $years
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Status:'),
					'name' => 'active',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Enable or disable customer login')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Newsletter:'),
					'name' => 'newsletter',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'newsletter_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'newsletter_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Customers will receive your newsletter via email.')
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Opt in:'),
					'name' => 'optin',
					'required' => false,
					'class' => 't',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'optin_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id' => 'optin_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					),
					'desc' => $this->l('Customer will receive your ads via email.')
				),
			)
		);

		// if we add a customer via fancybox (ajax), it's a customer and he doesn't need to be added to the visitor and guest groups
		if (Tools::isSubmit('addcustomer') && Tools::isSubmit('submitFormAjax'))
		{
			$visitor_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
			$guest_group = Configuration::get('PS_GUEST_GROUP');
			foreach ($groups as $key => $g)
				if (in_array($g['id_group'], array($visitor_group, $guest_group)))
					unset($groups[$key]);
		}

		$this->fields_form['input'] = array_merge($this->fields_form['input'],
				array(
					array(
								'type' => 'group',
								'label' => $this->l('Group access:'),
								'name' => 'groupBox',
								'values' => $groups,
								'required' => true,
								'desc' => $this->l('Select all the groups that you would like to apply to this customer.')
							),
					array(
						'type' => 'select',
						'label' => $this->l('Default customer group:'),
						'name' => 'id_default_group',
						'options' => array(
							'query' => $groups,
							'id' => 'id_group',
							'name' => 'name'
						),
						'hint' => $this->l('The group will be as applied by default.'),
						'desc' => $this->l('Apply the discount\'s price of this group.')
						)
					)
				);

		// if customer is a guest customer, password hasn't to be there
		if ($obj->id && ($obj->is_guest && $obj->id_default_group == Configuration::get('PS_GUEST_GROUP')))
		{
			foreach ($this->fields_form['input'] as $k => $field)
				if ($field['type'] == 'password')
					array_splice($this->fields_form['input'], $k, 1);
		}

		if (Configuration::get('PS_B2B_ENABLE'))
		{
			$risks = Risk::getRisks();

			$list_risks = array();
			foreach ($risks as $key => $risk)
			{
				$list_risks[$key]['id_risk'] = (int)$risk->id;
				$list_risks[$key]['name'] = $risk->name;
			}

			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Company:'),
				'name' => 'company',
				'size' => 33
			);
			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('SIRET:'),
				'name' => 'siret',
				'size' => 14
			);
			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('APE:'),
				'name' => 'ape',
				'size' => 5
			);
			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Website:'),
				'name' => 'website',
				'size' => 33
			);
			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Outstanding allowed:'),
				'name' => 'outstanding_allow_amount',
				'size' => 10,
				'hint' => $this->l('Valid characters:').' 0-9',
				'suffix' => '¤'
			);
			$this->fields_form['input'][] = array(
				'type' => 'text',
				'label' => $this->l('Maximum number of payment days:'),
				'name' => 'max_payment_days',
				'size' => 10,
				'hint' => $this->l('Valid characters:').' 0-9'
			);
			$this->fields_form['input'][] = array(
				'type' => 'select',
				'label' => $this->l('Risk:'),
				'name' => 'id_risk',
				'required' => false,
				'class' => 't',
				'options' => array(
					'query' => $list_risks,
					'id' => 'id_risk',
					'name' => 'name'
				),
			);
		}

		$this->fields_form['submit'] = array(
			'title' => $this->l('Save   '),
			'class' => 'button'
		);

		$birthday = explode('-', $this->getFieldValue($obj, 'birthday'));

		$this->fields_value = array(
			'years' => $this->getFieldValue($obj, 'birthday') ? $birthday[0] : 0,
			'months' => $this->getFieldValue($obj, 'birthday') ? $birthday[1] : 0,
			'days' => $this->getFieldValue($obj, 'birthday') ? $birthday[2] : 0,
		);

		// Added values of object Group
		if (!Validate::isUnsignedId($obj->id))
			$customer_groups = array();
		else
			$customer_groups = $obj->getGroups();
		$customer_groups_ids = array();
		if (is_array($customer_groups))
			foreach ($customer_groups as $customer_group)
				$customer_groups_ids[] = $customer_group;

		// if empty $carrier_groups_ids : object creation : we set the default groups
		if (empty($customer_groups_ids))
		{
			$preselected = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
			$customer_groups_ids = array_merge($customer_groups_ids, $preselected);
		}

		foreach ($groups as $group)
			$this->fields_value['groupBox_'.$group['id_group']] =
				Tools::getValue('groupBox_'.$group['id_group'], in_array($group['id_group'], $customer_groups_ids));

		return parent::renderForm();
	}
        
        public function renderView() {
		if (!($customer = $this->loadObject()))
			return;

		$this->context->customer = $customer;
		$gender = new Gender($customer->id_gender);
		$gender_image = $gender->getImage();

		$customer_stats = $customer->getStats();
		$sql = 'SELECT SUM(total_paid_real) FROM '._DB_PREFIX_.'orders WHERE id_customer = %d AND valid = 1';
		if ($total_customer = Db::getInstance()->getValue(sprintf($sql, $customer->id)))
		{
			$sql = 'SELECT SQL_CALC_FOUND_ROWS COUNT(*) FROM '._DB_PREFIX_.'orders WHERE valid = 1 GROUP BY id_customer HAVING SUM(total_paid_real) > %d';
			Db::getInstance()->getValue(sprintf($sql, (int)$total_customer));
			$count_better_customers = (int)Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
		}
		else
			$count_better_customers = '-';

		$orders = Order::getCustomerOrders($customer->id, true);
		$total_orders = count($orders);
		for ($i = 0; $i < $total_orders; $i++)
		{
			$orders[$i]['date_add'] = Tools::displayDate($orders[$i]['date_add']);
			$orders[$i]['total_paid_real_not_formated'] = $orders[$i]['total_paid_real'];
			$orders[$i]['total_paid_real'] = Tools::displayPrice($orders[$i]['total_paid_real'], new Currency((int)$orders[$i]['id_currency']));
		}

		$messages = CustomerThread::getCustomerMessages((int)$customer->id);
		$total_messages = count($messages);
		for ($i = 0; $i < $total_messages; $i++)
		{
			$messages[$i]['message'] = substr(strip_tags(html_entity_decode($messages[$i]['message'], ENT_NOQUOTES, 'UTF-8')), 0, 75);
			$messages[$i]['date_add'] = Tools::displayDate($messages[$i]['date_add'], null, true);
		}

		$groups = $customer->getGroups();
		$total_groups = count($groups);
		for ($i = 0; $i < $total_groups; $i++)
		{
			$group = new Group($groups[$i]);
			$groups[$i] = array();
			$groups[$i]['id_group'] = $group->id;
			$groups[$i]['name'] = $group->name[$this->default_form_language];
		}

		$total_ok = 0;
		$orders_ok = array();
		$orders_ko = array();
		foreach ($orders as $order)
		{
			if (!isset($order['order_state']))
				$order['order_state'] = $this->l('The state isn\'t defined for this order');

			if ($order['valid'])
			{
				$orders_ok[] = $order;
				$total_ok += $order['total_paid_real_not_formated'];
			}
			else
				$orders_ko[] = $order;
		}

		$products = $customer->getBoughtProducts();
		$total_products = count($products);
		for ($i = 0; $i < $total_products; $i++)
			$products[$i]['date_add'] = Tools::displayDate($products[$i]['date_add'], null, true);

		$carts = Cart::getCustomerCarts($customer->id);
		$total_carts = count($carts);
		for ($i = 0; $i < $total_carts; $i++)
		{
			$cart = new Cart((int)$carts[$i]['id_cart']);
			$this->context->cart = $cart;
			$summary = $cart->getSummaryDetails();
			$currency = new Currency((int)$carts[$i]['id_currency']);
			$carrier = new Carrier((int)$carts[$i]['id_carrier']);
			$carts[$i]['id_cart'] = sprintf('%06d', $carts[$i]['id_cart']);
			$carts[$i]['date_add'] = Tools::displayDate($carts[$i]['date_add'], null, true);
			$carts[$i]['total_price'] = Tools::displayPrice($summary['total_price'], $currency);
			$carts[$i]['name'] = $carrier->name;
		}

		$sql = 'SELECT DISTINCT id_product, c.id_cart, c.id_shop, cp.id_shop AS cp_id_shop
				FROM '._DB_PREFIX_.'cart_product cp
				JOIN '._DB_PREFIX_.'cart c ON (c.id_cart = cp.id_cart)
				WHERE c.id_customer = '.(int)$customer->id.'
					AND cp.id_product NOT IN (
							SELECT product_id
							FROM '._DB_PREFIX_.'orders o
							JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
							WHERE o.valid = 1 AND o.id_customer = '.(int)$customer->id.'
						)';
		$interested = Db::getInstance()->executeS($sql);
		$total_interested = count($interested);
		for ($i = 0; $i < $total_interested; $i++)
		{
			$product = new Product($interested[$i]['id_product'], false, $this->default_form_language, $interested[$i]['id_shop']);
                        
                        if  (!Validate::isLoadedObject($product))
 				continue;                                
                        
			$interested[$i]['url'] = $this->context->link->getProductLink(
				$product->id,
				$product->link_rewrite,
				Category::getLinkRewrite($product->id_category_default, $this->default_form_language),
				null,
				null,
				$interested[$i]['cp_id_shop']
			);
			$interested[$i]['id'] = (int)$product->id;
			$interested[$i]['name'] = Tools::htmlentitiesUTF8($product->name);
		}

		$connections = $customer->getLastConnections();
		$total_connections = count($connections);
		for ($i = 0; $i < $total_connections; $i++)
		{
			$connections[$i]['date_add'] = Tools::displayDate($connections[$i]['date_add'],null , true);
			$connections[$i]['http_referer'] = $connections[$i]['http_referer'] ?
													preg_replace('/^www./', '', parse_url($connections[$i]['http_referer'], PHP_URL_HOST)) :
														$this->l('Direct link');
		}

		$referrers = Referrer::getReferrers($customer->id);
		$total_referrers = count($referrers);
		for ($i = 0; $i < $total_referrers; $i++)
			$referrers[$i]['date_add'] = Tools::displayDate($referrers[$i]['date_add'],null , true);

		$shop = new Shop($customer->id_shop);
		$this->tpl_view_vars = array(
			'customer' => $customer,
			'gender_image' => $gender_image,

			// General information of the customer
			'registration_date' => Tools::displayDate($customer->date_add,null , true),
			'customer_stats' => $customer_stats,
			'last_visit' => Tools::displayDate($customer_stats['last_visit'],null , true),
			'count_better_customers' => $count_better_customers,
			'shop_is_feature_active' => Shop::isFeatureActive(),
			'name_shop' => $shop->name,
			'customer_birthday' => Tools::displayDate($customer->birthday),
			'last_update' => Tools::displayDate($customer->date_upd,null , true),
			'customer_exists' => Customer::customerExists($customer->email),
			'id_lang' => $customer->id_lang,
			'customerLanguage' => (new Language($customer->id_lang)),

			// Add a Private note
			'customer_note' => Tools::htmlentitiesUTF8($customer->note),

			// Messages
			'messages' => $messages,

			// Groups
			'groups' => $groups,

			// Orders
			'orders' => $orders,
			'orders_ok' => $orders_ok,
			'orders_ko' => $orders_ko,
			'total_ok' => Tools::displayPrice($total_ok, $this->context->currency->id),

			// Products
			'products' => $products,

			// Addresses
			'addresses' => $customer->getAddresses($this->default_form_language),

			// Discounts
			'discounts' => CartRule::getCustomerCartRules($this->default_form_language, $customer->id, false, false),

			// Carts
			'carts' => $carts,

			// Interested
			'interested' => $interested,

			// Connections
			'connections' => $connections,

			// Referrers
			'referrers' => $referrers,
			'show_toolbar' => true
		);

		return AdminController::renderView();
	}
}
?>