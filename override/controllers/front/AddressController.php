<?php

class AddressController extends AddressControllerCore
{
	/**
	 * redirección Usuario
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
	
		$this->assignCountries();
		$this->assignVatNumber();
		$this->assignAddressFormat();
	
		// Assign common vars
		$this->context->smarty->assign(array(
				'one_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'),
				'onr_phone_at_least' => (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'), //retro compat
				'ajaxurl' => _MODULE_DIR_,
				'errors' => $this->errors,
				'token' => Tools::getToken(false),
				'select_address' => (int)Tools::getValue('select_address'),
				'address' => $this->_address,
				'id_address' => (Validate::isLoadedObject($this->_address)) ? $this->_address->id : 0,
		));
	
		if ($back = Tools::getValue('back'))
			$this->context->smarty->assign('back', Tools::safeOutput($back));
		if ($mod = Tools::getValue('mod'))
			$this->context->smarty->assign('mod', Tools::safeOutput($mod));
		if (isset($this->context->cookie->account_created))
		{
			$this->context->smarty->assign('account_created', 1);
			unset($this->context->cookie->account_created);
		}
	
		/******* Codigo para Direcciones Ajax *******/
		$idcliente = $this->context->customer->id;
		$sql="SELECT ad.id_address, ad.id_state, st.name AS state, ad.id_customer, ad.alias, ad.city, ad.address1, ad.address2, ad.is_rfc 
		FROM "._DB_PREFIX_."address AS ad Inner Join "._DB_PREFIX_."state AS st ON ad.id_state = st.id_state WHERE ad.id_customer='".$idcliente."' AND ad.deleted=0";
		$result=Db::getInstance()->ExecuteS($sql,FALSE);
		$direcciones=array();
		$total=0;
		foreach($result as $row) {
			$direcciones[]=$row;
			$total+=1;
		}
		
		
		$pais = (int)Configuration::get('PS_COUNTRY_DEFAULT');
		$sqlpais="SELECT ps_state.id_state, ps_state.name AS state
                                            FROM ps_state
                                            WHERE ps_state.id_country =  ".$pais." ORDER BY state ASC ;";
		$rspais=Db::getInstance()->ExecuteS($sqlpais,FALSE);
		$estados=array();
		foreach($rspais as $estado) {
			$estados[]=$estado;
		}

		// datos tipos de documentos
		$this->context->smarty->assign('document_types', Utilities::data_type_documents() );
		// datos customer
		$this->context->smarty->assign('datacustomer', Utilities::data_customer_billing( $idcliente ) );

		// datos address RFC
		$this->context->smarty->assign('dataaddressrfc', Utilities::data_address_RFC( $idcliente ) );

		$this->context->smarty->assign('cliente',$idcliente);
		$this->context->smarty->assign('pais',$pais);
		$this->context->smarty->assign('estados',$estados);
		$this->context->smarty->assign('total',$total);
		$this->context->smarty->assign('direcciones',$direcciones);
		/******* Fin Codigo para Direcciones Ajax *******/
		$this->setTemplate(_PS_THEME_DIR_.'order-address.tpl');
	}
/**
	 * Process changes on an address
	 */
	protected function processSubmitAddress()
	{

						$val_cityid='';
						$val_address_up='';
						$val_savedir=0; //no guarda direccion

						if(isset($_POST['submitAddress'])){
									$val_savedir=1; //guarda direccion
									
									$val_address_up = $_POST['id_address'];
									$val_cityid = $_POST['city'];
									$_POST['city'] = $_POST['city_id'];
									$_POST['city_id'] = $val_cityid;
						}

		$address = new Address();
		$this->errors = $address->validateController();
		$address->id_customer = (int)$this->context->customer->id;

		// Check page token
		if ($this->context->customer->isLogged() && !$this->isTokenValid())
			$this->errors[] = Tools::displayError('Invalid token.');

		// Check phone
		if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && !Tools::getValue('phone') && !Tools::getValue('phone_mobile'))
			$this->errors[] = Tools::displayError('You must register at least one phone number.');
		if ($address->id_country)
		{
			// Check country
			if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country))
				throw new PrestaShopException('Country cannot be loaded with address->id_country');

			if ((int)$country->contains_states && !(int)$address->id_state)
				$this->errors[] = Tools::displayError('This country requires you to chose a State.');

			// US customer: normalize the address
			if ($address->id_country == Country::getByIso('US') && Configuration::get('PS_TAASC'))
			{
				include_once(_PS_TAASC_PATH_.'AddressStandardizationSolution.php');
				$normalize = new AddressStandardizationSolution;
				$address->address1 = $normalize->AddressLineStandardization($address->address1);
				$address->address2 = $normalize->AddressLineStandardization($address->address2);
			}

			$postcode = Tools::getValue('postcode');
			/* Check zip code format */
			if ($country->zip_code_format && !$country->checkZipCode($postcode))
				$this->errors[] = sprintf(Tools::displayError('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
			elseif(empty($postcode) && $country->need_zip_code)
				$this->errors[] = Tools::displayError('A Zip / Postal code is required.');
			elseif ($postcode && !Validate::isPostCode($postcode))
				$this->errors[] = Tools::displayError('The Zip / Postal code is invalid.');
			

			// Check country DNI
			/*if ($country->isNeedDni() && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni'))))
				$this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
			else if (!$country->isNeedDni())*/ //comentado para no requerir el numero de dni en la direccion del cliente
				$address->dni = null;
		}
		// Check if the alias exists
		if (!$this->context->customer->is_guest && !empty($_POST['alias']) && (int)$this->context->customer->id > 0)
		{
			$id_address = Tools::getValue('id_address');
			if(Configuration::get('PS_ORDER_PROCESS_TYPE') && (int)Tools::getValue('opc_id_address_'.Tools::getValue('type')) > 0)
				$id_address = Tools::getValue('opc_id_address_'.Tools::getValue('type'));

			if (Db::getInstance()->getValue('
				SELECT count(*)
				FROM '._DB_PREFIX_.'address
				WHERE `alias` = \''.pSql($_POST['alias']).'\'
				AND id_address != '.(int)$id_address.'
				AND id_customer = '.(int)$this->context->customer->id.'
				AND deleted = 0') > 0)
				$this->errors[] = sprintf(Tools::displayError('The alias "%s" has already been used. Please select another one.'), Tools::safeOutput($_POST['alias']));
		}

		// Check the requires fields which are settings in the BO
		$this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

		// Don't continue this process if we have errors !
		if ($this->errors && !$this->ajax)
			return;

		// If we edit this address, delete old address and create a new one
		if (Validate::isLoadedObject($this->_address))
		{
			if (Validate::isLoadedObject($country) && !$country->contains_states)
				$address->id_state = 0;
			$address_old = $this->_address;
			if (Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id))
			{
				if ($address_old->isUsed())
					$address_old->delete();
				else
				{
					$address->id = (int)($address_old->id);
					$address->date_add = $address_old->date_add;
				}
			}
		}

		if ($this->ajax && Tools::getValue('type') == 'invoice' && Configuration::get('PS_ORDER_PROCESS_TYPE'))
		{
			$this->errors = array_unique(array_merge($this->errors, $address->validateController()));
			if (count($this->errors))
			{
				$return = array(
					'hasError' => (bool)$this->errors,
					'errors' => $this->errors
				);
				die(Tools::jsonEncode($return));
			}
		}

		// Save address
		if ($result = $address->save())
		{

			if($val_savedir == 1) { // si estoy guardando direccion

				/*$Id_address=Db::getInstance()->Insert_ID(); 

				if($Id_address == 0) {
					$Id_address = $address->id;
					
					Db::getInstance()->update('address_city', array( 'id_city'=>(int)$val_cityid ), 'id_address = '.(int)$Id_address );

				} else {

					Db::getInstance()->insert('address_city', array( 'id_address'=>(int)$Id_address, 'id_city'=>(int)$val_cityid ));
				}
				*/

			}

			// Update id address of the current cart if necessary
			if (isset($address_old) && $address_old->isUsed())
				$this->context->cart->updateAddressId($address_old->id, $address->id);
			else // Update cart address
				$this->context->cart->autosetProductAddress();

            if ((bool)(Tools::getValue('select_address', false)) == true OR Tools::getValue('type') == 'invoice' && Configuration::get('PS_ORDER_PROCESS_TYPE'))
            {
                $this->context->cart->id_address_invoice = (int)$address->id;
                $this->context->cart->update();
            }

			if ($this->ajax)
			{
				$return = array(
					'hasError' => (bool)$this->errors,
					'errors' => $this->errors,
					'id_address_delivery' => $this->context->cart->id_address_delivery,
					'id_address_invoice' => $this->context->cart->id_address_invoice
				);
				die(Tools::jsonEncode($return));
			}

			// Redirect to old page or current page
			if ($back = Tools::getValue('back'))
			{
				$mod = Tools::getValue('mod');
				Tools::redirect('index.php?controller='.$back.($mod ? '&back='.$mod : ''));
			}
			else
				Tools::redirect('index.php?controller=addresses');
		}
		$this->errors[] = Tools::displayError('An error occurred while updating your address.');
	}
}