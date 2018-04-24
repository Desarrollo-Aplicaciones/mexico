<?php
class AuthController extends AuthControllerCore
{
	public function initContent()
	{
		// FrontController::initContent();

		$this->context->smarty->assign('genders', Gender::getGenders());

		$this->assignDate();

		$this->assignCountries();

		$query = new DbQuery();
		$query->select('*');
		$query->from('document');
		$query->orderBy('active DESC');
		$document = Db::getInstance()->executeS($query);
		$this->context->smarty->assign('document_types', $document);

		$this->context->smarty->assign('newsletter', 1);

		$back = Tools::getValue('back');
		$key = Tools::safeOutput(Tools::getValue('key'));
		if (!empty($key))
			$back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
		if (!empty($back))
			$this->context->smarty->assign('back', Tools::safeOutput($back));
			
			//mostrar formulario compra rapida
     $this->context->smarty->assign('GUEST_FORM_ENABLED' , Configuration::get('PS_GUEST_CHECKOUT_ENABLED'));
			
	
		if (Tools::getValue('display_guest_checkout'))
		{
			if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
				$countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
			else
				$countries = Country::getCountries($this->context->language->id, true);
			
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
			{
				// get all countries as language (xy) or language-country (wz-XY)
				$array = array();
				preg_match("#(?<=-)\w\w|\w\w(?!-)#",$_SERVER['HTTP_ACCEPT_LANGUAGE'],$array);
				if (!Validate::isLanguageIsoCode($array[0]) || !($sl_country = Country::getByIso($array[0])))
					$sl_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
			}
			else
				$sl_country = (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));
			
			$this->context->smarty->assign(array(
					'inOrderProcess' => true,
					'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
					'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
					'sl_country' => (int)$sl_country,
					'countries' => $countries
				));
		}

		if (Tools::getValue('create_account'))
			$this->context->smarty->assign('email_create', 1);

		if (Tools::getValue('multi-shipping') == 1)
			$this->context->smarty->assign('multi_shipping', true);
		else
			$this->context->smarty->assign('multi_shipping', false);
		
		$this->assignAddressFormat();

		// Call a hook to display more information on form
		$this->context->smarty->assign(array(
				'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
				'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop'),
				'HOOK_LOGINIZQ' => Hook::exec('loginizq')
			));

		// Login con Facebook
		$this->context->smarty->assign(array(
				'HOOK_HEADER' => Hook::exec('displayHeader'),
		));
		
		// Just set $this->template value here in case it's used by Ajax
		$this->setTemplate(_PS_THEME_DIR_.'authentication.tpl');

		if ($this->ajax)
		{
			// Call a hook to display more information on form
			$this->context->smarty->assign(array(
					'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
					'genders' => Gender::getGenders()
				));

			$return = array(
				'hasError' => !empty($this->errors),
				'errors' => $this->errors,
				'page' => $this->context->smarty->fetch($this->template),
				'token' => Tools::getToken(false)
			);
			die(Tools::jsonEncode($return));
		}
                
                
        
                                        $sqlpais="SELECT ps_state.id_state, ps_state.name AS state 
                                            FROM ps_state 
                                            WHERE ps_state.id_country =  69 ORDER BY state ASC ;";
                                        $rspais=Db::getInstance()->ExecuteS($sqlpais,FALSE);
                                        $estados=array();
                                        foreach($rspais as $estado) {
                                            $estados[]=$estado;                                           
                                        }
                                      
                                        $this->context->smarty->assign('estados',$estados);
                                 
                                        /******* Fin Codigo para Direcciones Ajax *******/    
                
	
	}

	public function initFooter(){
		$this->display_footer = false;
	}

	/**
	 * Process login
	 */
	protected function processSubmitLogin()
	{
		Hook::exec('actionBeforeAuthentication');
		$passwd = trim(Tools::getValue('passwd'));
		$email = trim(Tools::getValue('email'));
		if (empty($email))
			$this->errors[] = Tools::displayError('An email address required.');
		elseif (!Validate::isEmail($email))
			$this->errors[] = Tools::displayError('Invalid email address.');
		elseif (empty($passwd))
			$this->errors[] = Tools::displayError('Password is required.');
		elseif (!Validate::isPasswd($passwd))
			$this->errors[] = Tools::displayError('Invalid password.');
		else
		{
			$customer = new Customer();
			$authentication = $customer->getByEmail(trim($email), trim($passwd));
			if (!$authentication || !$customer->id)
				$this->errors[] = Tools::displayError('Authentication failed.');
			else
			{
				$this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare: CompareProduct::getIdCompareByIdCustomer($customer->id);
				$this->context->cookie->id_customer = (int)($customer->id);
				$this->context->cookie->customer_lastname = $customer->lastname;
				$this->context->cookie->customer_firstname = $customer->firstname;
				$this->context->cookie->logged = 1;
				$customer->logged = 1;
				$this->context->cookie->is_guest = $customer->isGuest();
				$this->context->cookie->passwd = $customer->passwd;
				$this->context->cookie->email = $customer->email;
				
				// Add customer to the context
				$this->context->customer = $customer;
				
				if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id))
					$this->context->cart = new Cart($id_cart);
				else
				{
					$this->context->cart->id_carrier = 0;
					$this->context->cart->setDeliveryOption(null);
					$this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
					$this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
				}
				$this->context->cart->id_customer = (int)$customer->id;
				$this->context->cart->secure_key = $customer->secure_key;
				$this->context->cart->save();
				$this->context->cookie->id_cart = (int)$this->context->cart->id;
				$this->context->cookie->write();
				$this->context->cart->autosetProductAddress();

				Hook::exec('actionAuthentication');

				// Login information have changed, so we check if the cart rules still apply
				CartRule::autoRemoveFromCart($this->context);
				CartRule::autoAddToCart($this->context);

				if (!$this->ajax)
				{
					if ($back = Tools::getValue('back')) {
						if ($back != "my-account") {
							Tools::redirect(html_entity_decode($back));
							//Tools::redirect(_PS_BASE_URL_.__PS_BASE_URI__.$back);
						} else {
							Tools::redirect(html_entity_decode($back));
						}
					}
					Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
				}
			}
		}
		if ($this->ajax)
		{
			$return = array(
				'hasError' => !empty($this->errors),
				'errors' => $this->errors,
				'token' => Tools::getToken(false)
			);
			die(Tools::jsonEncode($return));
		}
		else
			$this->context->smarty->assign('authentification_error', $this->errors);
	}

	/**
	 * Initialize auth controller
	 * @see FrontController::init()
	 */
	public function init()
	{ 
		FrontController::init();

		if (!Tools::getIsset('step') && $this->context->customer->isLogged() && !$this->ajax) { 
			if (Tools::getValue('back') && $this->authRedirection === false) {
				Tools::redirect(Tools::getValue('back'));
			}
			Tools::redirect('index.php?controller='.(($this->authRedirection !== false) ? urlencode($this->authRedirection) : 'my-account'));
		
}
		if (Tools::getValue('create_account'))
			$this->create_account = true;
	}	
	   public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_.'min-login.css', 'all');
    }

    public function postProcess(){
        if (Tools::isSubmit('ajax') && Tools::isSubmit('checkPassword'))
            $this->checkPassword();
        else if (Tools::isSubmit('ajax') && Tools::isSubmit('checkMailNotExist'))
            $this->checkMailNotExist();

        return parent::postProcess();

    }

    private function checkPassword(){
        $password = Tools::getValue('password');
        $email = Tools::getValue('email');

        $customer = new Customer();
        $authentication = $customer->getByEmail(trim($email), trim($password));

        if (!$authentication || !$customer->id)
            die(json_encode("KO"));
        else
            die(json_encode("OK"));
    }

    private function checkMailNotExist(){
        $email = Tools::getValue('email');
        if (Customer::customerExists($email))
            die(json_encode("KO"));
        else
            die(json_encode("OK"));
    }
}
?>