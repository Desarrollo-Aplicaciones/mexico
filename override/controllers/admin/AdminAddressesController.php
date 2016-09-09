<?php
class AdminAddressesController extends AdminAddressesControllerCore
{
	public function renderForm(){

		$this->fields_form = array(
			'legend' => array(
				'title' => $this->l('Addresses'),
				'image' => '../img/admin/contact.gif'
			),
			'input' => array(
				array(
					'type' => 'text_customer',
					'label' => $this->l('Customer'),
					'name' => 'id_customer',
					'size' => 33,
					'required' => false,
				),
				array(
					'type' => 'hidden',
					'name'    => 'is_rfc',
					'required' => false,
				),
				array(
					'type' => 'text',
					'id' => 'rfc',
					'label' => 'RFC',
					'name' => 'dni',
					'size' => 30,
					'required' => true,
					'desc' => $this->l('formatos posibles XXX000000XXX o XXXX000000XXX')
				),
				array(
					'type' => 'hidden',
					'label' => $this->l('city_id'),
					'name' => 'city_id',
					'size' => 30,
					'required' => false
				),				
				array(
					'type' => 'text',
					'label' => $this->l('Address alias'),
					'name' => 'alias',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' <>;=#{}'
				),
				array(
					'type' => 'text',
					'label' => $this->l('Home phone'),
					'name' => 'phone',
					'size' => 33,
					'required' => false,
				),
				array(
					'type' => 'text',
					'label' => $this->l('Mobile phone'),
					'name' => 'phone_mobile',
					'size' => 33,
					'required' => false,
					'desc' => Configuration::get('PS_ONE_PHONE_AT_LEAST')? sprintf($this->l('You must register at least one phone number %s'), '<sup>*</sup>') : ''
				),
				array(
					'type' => 'textarea',
					'label' => $this->l('Other'),
					'name' => 'other',
					'cols' => 36,
					'rows' => 4,
					'required' => false,
					'hint' => $this->l('Forbidden characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span>'
				),
			),
			'submit' => array(
				'title' => $this->l('Save   '),
				'class' => 'button'
			)
		);
		$id_customer = (int)Tools::getValue('id_customer');
		if (!$id_customer && Validate::isLoadedObject($this->object))
			$id_customer = $this->object->id_customer;
		if ($id_customer)
		{
			$customer = new Customer((int)$id_customer);
			$token_customer = Tools::getAdminToken('AdminCustomers'.(int)(Tab::getIdFromClassName('AdminCustomers')).(int)$this->context->employee->id);
		}

		$this->tpl_form_vars = array(
			'customer' => isset($customer) ? $customer : null,
			'tokenCustomer' => isset ($token_customer) ? $token_customer : null
		);

		// Order address fields depending on country format
		$addresses_fields = $this->processAddressFormat();
		// we use  delivery address
		$addresses_fields = $addresses_fields['dlv_all_fields'];

		$temp_fields = array();

		foreach ($addresses_fields as $addr_field_item)
		{
			if ($addr_field_item == 'company')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Company'),
					'name' => 'company',
					'size' => 33,
					'required' => false,
					'hint' => $this->l('Invalid characters:').' <>;=#{}<span class="hint-pointer">&nbsp;</span>'
				);
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('VAT number'),
					'name' => 'vat_number',
					'size' => 33,
				);
			}
			else if ($addr_field_item == 'lastname')
			{
				if (isset($customer) &&
					!Tools::isSubmit('submit'.strtoupper($this->table)) &&
					Validate::isLoadedObject($customer) &&
					!Validate::isLoadedObject($this->object))
					$default_value = $customer->lastname;
				else
					$default_value = '';

				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Last Name'),
					'name' => 'lastname',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span>',
					'default_value' => $default_value,
				);
			}
			else if ($addr_field_item == 'firstname')
			{
				if (isset($customer) &&
					!Tools::isSubmit('submit'.strtoupper($this->table)) &&
					Validate::isLoadedObject($customer) &&
					!Validate::isLoadedObject($this->object))
					$default_value = $customer->firstname;
 	 	 	 	else
 	 	 	 		$default_value = '';

				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('First Name'),
					'name' => 'firstname',
					'size' => 33,
					'required' => true,
					'hint' => $this->l('Invalid characters:').' 0-9!<>,;?=+()@#"�{}_$%:<span class="hint-pointer">&nbsp;</span>',
					'default_value' => $default_value,
				);
			}
			else if ($addr_field_item == 'address1')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Address'),
					'name' => 'address1',
					'size' => 33,
					'required' => true,
				);
			}
			else if ($addr_field_item == 'address2')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Address').' (2)',
					'name' => 'address2',
					'size' => 33,
					'required' => false,
				);
			}
			else if ($addr_field_item == 'country' || $addr_field_item == 'Country:name')
			{
				$temp_fields[] = array(
					'type' => 'select',
					'label' => $this->l('Country'),
					'name' => 'id_country',
					'required' => false,
					'default_value' => (int)$this->context->country->id,
					'options' => array(
						'query' => Country::getCountries($this->context->language->id),
						'id' => 'id_country',
						'name' => 'name',
					)
				);
				$temp_fields[] = array(
					'type' => 'select',
					'label' => $this->l('State'),
					'name' => 'id_state',
					'required' => false,
					'options' => array(
						'query' => array(),
						'id' => 'id_state',
						'name' => 'name',
					)
				);
			}
			elseif ($addr_field_item == 'postcode')
			{
				$temp_fields[] = array(
					'type' => 'text',
					'label' => $this->l('Zip/Postal Code'),
					'name' => 'postcode',
					'size' => 33,
					'required' => true,
				);
			}
			else if ($addr_field_item == 'city')
			{

				$temp_fields[] = array(
					'type' => 'select',
					'label' => $this->l('City'),
					'name' => 'city',
					'required' => true,					
					'options' => array(
						'query' => City::getIdCityByIdAddress($this->id_object, true),
						'id' => 'id_city',
						'name' => 'city_name',
					)

				);

				if(isset($this->object->id_colonia)) {
					
					$temp_fields[] = array(
						'type' => 'select',
						'label' => $this->l('Colonia'),
						'name' => 'id_colonia',
						'required' => true,'options' => array(
							'query' => City::getColoniaByIdColonia($this->object->id_colonia),
							'id' => 'id_codigo_postal',
							'name' => 'nombrecolonia',
						)
					);
				} else {
					
					$temp_fields[] = array(
						'type' => 'select',
						'label' => $this->l('Colonia'),
						'name' => 'id_colonia',
						'required' => true,
					);
				}
			}
			else if ($addr_field_item == 'city_id')
			{
				$temp_fields[] = array(
					'type' => 'hidden',
					'label' => $this->l('city_id'),
					'name' => 'city_id',
					'required' => true,	
					'default_value' => City::getColoniaByIdColonia($this->id_object),				
				);
			}
		}

		// merge address format with the rest of the form
		array_splice($this->fields_form['input'], 3, 0, $temp_fields);

		return AdminController::renderForm();
	}
	public function processSave()
	{
		if (Validate::isEmail(Tools::getValue('email')))
		{
			$customer = new Customer();
			$customer->getByEmail(Tools::getValue('email'), null, false);
			if (Validate::isLoadedObject($customer))
				$_POST['id_customer'] = $customer->id;
			else
				$this->errors[] = Tools::displayError('This email address is not registered.');
		}
		else if ($id_customer = Tools::getValue('id_customer'))
		{
			$customer = new Customer((int)$id_customer);
			if (Validate::isLoadedObject($customer))
				$_POST['id_customer'] = $customer->id;
			else
				$this->errors[] = Tools::displayError('Unknown customer');
		}
		else{
			$this->errors[] = Tools::displayError('Unknown customer');
		}
		if (Country::isNeedDniByCountryId(Tools::getValue('id_country')) && !Tools::getValue('dni')) {
			//$this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
			//comentado para no tener obligatorio el DNI al momento de guardar la dirección
                    
                }

		/* If the selected country does not contain states */
		$id_state = (int)Tools::getValue('id_state');
		$id_country = (int)Tools::getValue('id_country');
		$country = new Country((int)$id_country);
		if ($country && !(int)$country->contains_states && $id_state)
			$this->errors[] = Tools::displayError('You\'ve selected a state for a country that does not contain states.');

		/* If the selected country contains states, then a state have to be selected */
		if ((int)$country->contains_states && !$id_state)
			$this->errors[] = Tools::displayError('An address located in a country containing states must have a state selected.');

		$postcode = Tools::getValue('postcode');		
		/* Check zip code format */
		if ($country->zip_code_format && !$country->checkZipCode($postcode) && $postcode != '')
			$this->errors[] = Tools::displayError('Your Postal / Zip Code is incorrect.').'<br />'.Tools::displayError('It must be entered as follows:').' '.str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format)));
		elseif(empty($postcode) && $country->need_zip_code)
			$this->errors[] = Tools::displayError('A Zip / Postal code is required.');
		elseif ($postcode && !Validate::isPostCode($postcode))
			$this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');

		if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && !Tools::getValue('phone') && !Tools::getValue('phone_mobile'))		
			$this->errors[] = Tools::displayError('You must register at least one phone number.');

		/* If this address come from order's edition and is the same as the other one (invoice or delivery one)
		** we delete its id_address to force the creation of a new one */
		if ((int)Tools::getValue('id_order'))
		{
			$this->_redirect = false;
			if (isset($_POST['address_type']))
				$_POST['id_address'] = '';
		}

		// Check the requires fields which are settings in the BO
		$address = new Address();
		$this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

		if(Tools::isSubmit('check_rfc')){
			if(Validate::isRFC(Tools::getValue('dni'))){
				Db::getInstance()->update('address', array( 'is_rfc'=>'0'), 'id_customer = '.$customer->id );
				$_POST['is_rfc'] = '1';
			}
			else{
				$this->errors[] = Tools::displayError('El formato de RFC es incorrecto');
			}
		}
		else{
			$_POST['is_rfc'] = '0';
		}
		if (empty($this->errors)){
			return AdminController::processSave();
		}
		else
			// if we have errors, we stay on the form instead of going back to the list
			$this->display = 'edit';

		/* Reassignation of the order's new (invoice or delivery) address */
		$address_type = ((int)Tools::getValue('address_type') == 2 ? 'invoice' : ((int)Tools::getValue('address_type') == 1 ? 'delivery' : ''));
		if ($this->action == 'save' && ($id_order = (int)Tools::getValue('id_order')) && !count($this->errors) && !empty($address_type))
		{
			if (!Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'orders SET `id_address_'.$address_type.'` = '.Db::getInstance()->Insert_ID().' WHERE `id_order` = '.$id_order))
				$this->errors[] = Tools::displayError('An error occurred while linking this address to its order.');
			else
				Tools::redirectAdmin(Tools::getValue('back').'&conf=4');
		}
	}
}
?>