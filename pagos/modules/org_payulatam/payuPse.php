<?php



$useSSL = true;
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
require_once(_PS_MODULE_DIR_.'payulatam/payulatam.php');
require_once(_PS_MODULE_DIR_.'payulatam/config.php');

class PayuPse extends FrontController {
    
    
    
   
    
    public $ssl = true;
   

	public function setMedia()
	{
		parent::setMedia();
	}

	public function process() {
            
     if(empty( $this->context->cart->id)){
         Tools::redirect('/');  
     }            

        parent::process();





        $messsage = '';
        if (isset($_POST['pse_bank']) && isset($_POST['name_bank'])) {

            // reglas de carrito para bines
            $payulatam = new PayULatam();
            $payulatam->addCartRulePse($_POST['pse_bank']);

            $params = $this->initParams();

            $conf = new ConfPayu();
            $keysPayu = $conf->keys();


            $customer = new Customer((int) $this->context->cart->id_customer);
            $id_cart = $this->context->cart->id;
            $id_address = $this->context->cart->id_address_delivery;
            $this->createPendingOrder();
            $order = $conf->get_order($id_cart);
            $id_order = $order['id_order'];
            $reference_code = $customer->id . '_' . $id_cart . '_' . $id_order . '_' . $id_address;


            $varRandn = $conf->randString();
            $varRandc = $conf->randString();
            setcookie($varRandn, $varRandc, time() + 900);


            $browser = array('ipAddress' => $_SERVER['SERVER_ADDR'],
                'userAgent' => $_SERVER['HTTP_USER_AGENT']);


            $address = null;
            $sql = 'select ad.address1,city,phone_mobile,phone,dni, st.`name` as state, co.iso_code   
        from ps_address ad, ps_state st, ps_country co  where ad.id_customer=' . $this->context->cart->id_customer . ''
                    . ' and ad.id_address=' . $this->context->cart->id_address_delivery . ' and ad.id_state= st.id_state and
       co.id_country =ad.id_country';



            if ($results = Db::getInstance()->ExecuteS($sql))
                foreach ($results as $row) {
                    $address = $row;
                }
            $phone = null;
            if ($address['phone_mobile'] == '') {
                $phone = $address['phone'];
            } else {
                $phone = $address['phone_mobile'];
            }



            $data = '{
"test":false,
"language":"es",
"command":"SUBMIT_TRANSACTION",
"merchant":{
"apiLogin":"' . $keysPayu['apiLogin'] . '",
"apiKey":"' . $keysPayu['apiKey'] . '"
},
"transaction":{
"order":{
"accountId":"' . $keysPayu['pse-CO'] . '",
"referenceCode":"' . $params[2]['referenceCode'] . '",
"description":"' . $reference_code . '",
"language":"es",
"notifyUrl":"' . $conf->urlv() . '",
"signature":"' . $conf->sing($params[2]['referenceCode'] . '~' . $params[4]['amount'] . '~' . $params[9]['currency']) . '",
"buyer":{
"fullName":"' . $this->context->customer->firstname . ' ' . $this->context->customer->lastname . '",
"emailAddress":"' . $params[5]['buyerEmail'] . '",
"dniNumber":"' . $address['dni'] . '",
"shippingAddress":{
"street1":"' . $address['address1'] . '",
"city":"' . $address['city'] . '",
"state":"' . $address['state'] . '",
"country":"' . $address['iso_code'] . '",
"phone":"' . $phone . '"
}
},
"additionalValues":{
"TX_VALUE":{
"value":' . $params[4]['amount'] . ',
"currency":"' . $params[9]['currency'] . '"
}
}
},
"payer":{
"fullName":"' . $this->context->customer->firstname . ' ' . $this->context->customer->lastname . '",
"emailAddress":"' . $params[5]['buyerEmail'] . '",
"dniNumber":"' . $address['dni'] . '",
"contactPhone":"' . $phone . '"
},
"ipAddress":"' . $browser['ipAddress'] . '",
"cookie":"' . $varRandn . '",
"userAgent":"' . $browser['userAgent'] . '",
"type":"AUTHORIZATION_AND_CAPTURE",
"paymentMethod":"PSE",
"extraParameters":{
"PSE_REFERENCE1":"' . $browser['ipAddress'] . '",
"FINANCIAL_INSTITUTION_CODE":"' . $_POST['pse_bank'] . '",
"FINANCIAL_INSTITUTION_NAME":"' . $_POST['name_bank'] . '",
"USER_TYPE":"' . $_POST['pse_tipoCliente'] . '",
"PSE_REFERENCE2":"' . $_POST['pse_docType'] . '",
"PSE_REFERENCE3":"' . $_POST['pse_docNumber'] . '"
}
}
}
';


            $response = $conf->sendJson($data);


            $messsage = '';
            if ($response['code'] == 'ERROR') {
                $conf->failed_transaction($id_order, $customer->id, $data, $response, 'Pse', 'code=ERROR', $id_cart, $id_address);

                $messsage = '<h1>¡Error!, Transaccion Rechazada</h1><br>' . $response['error'];
            } elseif ($response['code'] == 'SUCCESS') {


                $conf->pago_payu($id_order, $customer->id, $data, $response, 'Pse', 'code=SUCCESS', $id_cart, $id_address);


                $url_base64 = strtr(base64_encode($response['transactionResponse']['extraParameters']['BANK_URL']), '+/=', '-_,');


                $string_send = __PS_BASE_URI__ . 'order-confirmation.php?key=' . $customer->secure_key . '&id_cart=' . (int) $id_cart . '&id_module=105&id_order=' . (int) $order['id_order'] . '&bankdest2=' . $url_base64;

                Tools::redirectLink($string_send);
            } elseif ($response['code'] == 'PAYU_WAITING_PAYMENT') {
                $conf->failed_transaction($id_order, $customer->id, $data, $response, 'Pse', 'code=PAYU_WAITING_PAYMENT', $id_cart, $id_address);
            } else {
                $messsage = 'Error indeterminado';
                $conf->failed_transaction($id_order, $customer->id, $data, $response, 'Pse', 'code=Error indeterminado', $id_cart, $id_address);
            }
        }

        $messsage.=' <div id="messsage_payu">  <a href="' . $this->context->smarty->tpl_vars['content_dir']->value . 'index.php?controller=order&submitReorder=&id_order=' . $id_order . '">  Intenta con otro medio de pago </a>  </div> ';

        // Error de pago
        $obj_order = new Order((int) $id_order);
        $obj_order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
        
        self::$smarty->assign('messsage', $messsage);
    }

    public function displayContent() {
        parent::displayContent();

        self::$smarty->display(_PS_MODULE_DIR_ . 'payulatam/tpl/success.tpl');
    }

    public function initParams()
	{

		$tax = (float)self::$cart->getOrderTotal() - (float)self::$cart->getOrderTotal(false);
		$base = (float)self::$cart->getOrderTotal(true, Cart::ONLY_PRODUCTS) + (float)self::$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS) - (float)$tax;
		if($tax == 0)
			$base = 0;

		$currency = new Currency(self::$cart->id_currency);

		$language = new Language(self::$cart->id_lang);

		$customer = new Customer(self::$cart->id_customer);

		$ref = 'payU_'.Configuration::get('PS_SHOP_NAME').'_'.(int)self::$cart->id;

		$token = md5(Tools::safeOutput(Configuration::get('PAYU_API_KEY')).'~'.Tools::safeOutput(Configuration::get('PAYU_MERCHANT_ID')).'~'.$ref.'~'.(float)self::$cart->getOrderTotal().'~'.Tools::safeOutput($currency->iso_code));

		$params = array(
			array('test' => (Configuration::get('PAYU_DEMO') == 'yes' ? 1 : 0), 'name' => 'test'),
			array('merchantId' => Tools::safeOutput(Configuration::get('PAYU_MERCHANT_ID')), 'name' => 'merchantId'),
			array('referenceCode' => $ref, 'name' => 'referenceCode'),
			array('description' => substr(Configuration::get('PS_SHOP_NAME').' Order', 0, 255), 'name' => 'description'),
			array('amount' => (float)self::$cart->getOrderTotal(), 'name' => 'amount'),
			array('buyerEmail' => Tools::safeOutput($customer->email), 'name' => 'buyerEmail'),
			array('tax' => (float)$tax, 'name' => 'tax'),
			array('extra1' => 'PRESTASHOP', 'name' => 'extra1'),
			array('taxReturnBase' => (float)$base, 'name' => 'taxReturnBase'),
			array('currency' => Tools::safeOutput($currency->iso_code), 'name' => 'currency'),
			array('lng' => Tools::safeOutput($language->iso_code), 'name' => 'lng'),
			array('signature' => Tools::safeOutput($token), 'name' => 'signature'),
			array('value' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'history.php', 'name' => 'responseUrl'),
			array('value' => 'http://'.htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/payulatam/validation.php', 'name' => 'confirmationUrl'),
		);

		if (Configuration::get('PAYU_ACCOUNT_ID') != 0)
			$params[] = array('accountId' => (int)Configuration::get('PAYU_ACCOUNT_ID'), 'name' => 'accountId');

		if (Db::getInstance()->getValue('SELECT `token` FROM `'._DB_PREFIX_.'payu_token` WHERE `id_cart` = '.(int)self::$cart->id))
			Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'payu_token` SET `token` = "'.pSQL($token).'" WHERE `id_cart` = '.(int)self::$cart->id);
		else
			Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'payu_token` (`id_cart`, `token`) VALUES ('.(int)self::$cart->id.', \''.pSQL($token).'\')');

		return $params;
	}

	public function createPendingOrder()
	{
            try{
		$payu = new PayULatam();
		$payu->validateOrder((int)self::$cart->id, (int)Configuration::get('PAYU_WAITING_PAYMENT'), (float)self::$cart->getOrderTotal(), 'Payu_pse', 'El sistema esta en espera de la confirmación de la pasarela de pago.', array(), NULL, false,	self::$cart->secure_key);
            } catch (Exception $e)
            {
                //echo 'Error: '.$e->getMessage();
            }       
        }
        
        



}


$payuPse=new PayuPse();

$payuPse->run();
