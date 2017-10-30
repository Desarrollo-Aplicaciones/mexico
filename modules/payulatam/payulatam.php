<?php

/*
 * 2007-2013 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
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
 *  @version  Release: $Revision: 14011 $
 *  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */


if (!defined('_PS_VERSION_'))
    exit;

class PayULatam extends PaymentModule {

    private $_postErrors = array();

    /**
     * @brief Constructor
     */
    public function __construct() {
        $this->name = 'payulatam';
        $this->tab = 'payments_gateways';
        $this->version = '9.1.1';
        $this->author = 'PrestaShop';

        parent::__construct();

        $this->displayName = $this->l('PayU Latam');
        $this->description = $this->l('Module for accepting payments in Latin American countries from local credit cards, local bank transfers and cash deposits.');

        $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');

        /* Backward compatibility */
        require_once(_PS_MODULE_DIR_ . 'payulatam/backward_compatibility/backward.php');
        $this->context->smarty->assign('base_dir', __PS_BASE_URI__);
        $this->context->smarty->assign('ruta', getcwd());
        $this->context->smarty->assign($this->name.'_dir',_MODULE_DIR_.$this->name);
        $this->context->smarty->assign('module_name', 'payulatam');
        // GetModelDatosPasarelas()
    }

    /**
     * @brief Install method
     *
     * @return Success or failure
     */
    public function install() {
        if (!parent::install() || !$this->registerHook('payment') ||
            !Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'payu_token` (
                                        `id_cart` int(10) NOT NULL,
                                        `token` varchar(32) DEFAULT NULL,
                                        `status` varchar(20) DEFAULT NULL,
                                        PRIMARY KEY  (`id_cart`)
		) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;')) // prod | test
            return false;

        if (!Configuration::get('PAYU_WAITING_PAYMENT'))
            Configuration::updateValue('PAYU_WAITING_PAYMENT', $this->addState('Payu Latam : Pending payment', '#DDEEFF'));

//PAYU_WAITING_PAYMENT
        return true;
    }

    private function addState($en, $color) {
        $orderState = new OrderState();
        $orderState->name = array();
        foreach (Language::getLanguages() AS $language) {
            /* if (strtolower($language['iso_code']) == 'en')
              $orderState->name[$language['id_lang']] = $fr;
              else */
                $orderState->name[$language['id_lang']] = $en;
        }
        $orderState->send_email = false;
        $orderState->color = $color;
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = false;
        if ($orderState->add())
            copy(dirname(__FILE__) . '/logo.gif', dirname(__FILE__) . '/../../img/os/' . (int) $orderState->id . '.gif');
        return $orderState->id;
    }

    /**
     * @brief Uninstall function
     *
     * @return Success or failure
     */
    public function uninstall() {
        // Uninstall parent and unregister Configuration
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'payu_token`');
        $orderState = new OrderState((int) Configuration::get('PAYU_WAITING_PAYMENT'));
        $orderState->delete();
        Configuration::deleteByName('PAYU_WAITING_PAYMENT');
        if (!parent::uninstall())
            return false;
        return true;
    }

    /**
     * @brief Main Form Method
     *
     * @return Rendered form
     */
    public function getContent() {
        if(Tools::getValue('option_ajax') && !empty(Tools::getValue('option_ajax')))
            $this->process_ajax();
        $html = '';
        if (isset($_POST) && isset($_POST['submitPayU'])) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
                $html .= $this->_displayValidation();
            } else
            $html .= $this->_displayErrors();
        } elseif (isset($_POST) && isset($_POST['SaveSelectedPasarela'])) {
            $medios_pago =  array();
            foreach ($_POST as $key => $value) {
               $element = explode("|", $key);
               if(isset($element[1]) && !empty($element[1]) && $element[1] == 'mediop'){
                $medios_pago[$element[0]] = $value;
            }
        }
        if (count($medios_pago) > 0 ) {
            PasarelaPagoCore::SetPasarelaMediosP($medios_pago);
            $html .= $this->_displayValidation();
        } else {
            $this->_postErrors[] = 'Su solicitud no se pudo completar';
            $html .= $this->_displayErrors();
        }
    }elseif(isset($_POST) && isset($_POST['datos_pasarela'])){
        if(PasarelaPagoCore::SaveDatosPasarela()){
            $html .= $this->_displayValidation();
        }else{
            $this->_postErrors[] = 'Su solicitud no se pudo completar';
            $html .= $this->_displayErrors();
        }
    }

    $this->context->smarty->assign('pasarelas', PasarelaPagoCore::GetList());
    $this->context->smarty->assign('mediosp_pasarelas',PasarelaPagoCore::GetPMediosPsarelas());
    $this->context->smarty->assign('model_Datos_pasarelas',PasarelaPagoCore::GetModelDatosPasarelas());

    return $html . $this->_displayAdminTpl();
}

    /**
     * @brief Method that will displayed all the tabs in the configurations forms
     *
     * @return Rendered form
     */
    private function _displayAdminTpl() {
       
        $this->context->smarty->assign(array(
                                       'tab' => array(
                                                      'intro' => array(
                                                                       'title' => $this->l('How to configure'),
                                                                       'content' => $this->_displayHelpTpl(),
                                                                       'icon' => '../modules/payulatam/img/info-icon.gif',
                                                                       'tab' => 1,
                                                                       'selected' => (Tools::isSubmit('submitPayU') ? false : true),
                                                                       ),
                                                      'credential' => array(
                                                                            'title' => $this->l('Credentials'),
                                                                            'content' => $this->_displayCredentialTpl(),
                                                                            'icon' => '../modules/payulatam/img/credential.png',
                                                                            'tab' => 2,
                                                                            'selected' => (Tools::isSubmit('submitPayU') ? true : false),
                                                                            ),
                                                      ),
'tracking' => 'http://www.prestashop.com/modules/payulatam.png?url_site=' . Tools::safeOutput($_SERVER['SERVER_NAME']) . '&id_lang=' . (int) $this->context->cookie->id_lang,
'logo' => '../modules/payulatam/img/logo.jpg',
'script' => array('../modules/payulatam/js/payu.js'),
'css' => '../modules/payulatam/css/payu.css',
'lang' => ($this->context->language->iso_code != 'en' || $this->context->language->iso_code != 'es' ? 'en' : $this->context->language->iso_code)
));

return $this->display(__FILE__, 'tpl/admin.tpl');
}

private function _displayHelpTpl() {
    return $this->display(__FILE__, 'tpl/help.tpl');
}

    /**
     * @brief Credentials Form Method
     *
     * @return Rendered form
     */
    private function _displayCredentialTpl() {
        $this->context->smarty->assign(array(
                                       'formCredential' => './index.php?tab=AdminModules&configure=payulatam&token=' . Tools::getAdminTokenLite('AdminModules') . '&tab_module=' . $this->tab . '&module_name=payulatam',
                                       'credentialTitle' => $this->l('Log in'),
                                       'credentialText' => $this->l('In order to use this module, please fill out the form with the logins provided to you by PayU Latam.'),
                                       'credentialInputVar' => array(
                                                                     'merchantId' => array(
                                                                                           'name' => 'merchantId',
                                                                                           'required' => true,
                                                                                           'value' => (Tools::getValue('merchantId') ? Tools::safeOutput(Tools::getValue('merchantId')) : Tools::safeOutput(Configuration::get('PAYU_MERCHANT_ID'))),
                                                                                           'type' => 'text',
                                                                                           'label' => $this->l('Merchant ID:'),
                                                                                           'desc' => $this->l('The Merchant ID given to you by PayU Latam at the creation of your account.'),
                                                                                           ),
                                                                     'apiKey' => array(
                                                                                       'name' => 'apiKey',
                                                                                       'required' => true,
                                                                                       'value' => (Tools::getValue('apiKey') ? Tools::safeOutput(Tools::getValue('apiKey')) : Tools::safeOutput(Configuration::get('PAYU_API_KEY'))),
                                                                                       'type' => 'text',
                                                                                       'label' => $this->l('Api Key:'),
                                                                                       'desc' => $this->l('The Api Key given to you by PayU Latam at the creation of your account.'),
                                                                                       ),
                                                                     'accountId' => array(
                                                                                          'name' => 'accountId',
                                                                                          'required' => false,
                                                                                          'value' => (Tools::getValue('accountId') ? (int) Tools::getValue('accountId') : (int) Configuration::get('PAYU_ACCOUNT_ID')),
                                                                                          'type' => 'text',
                                                                                          'label' => $this->l('Account ID:'),
                                                                                          'desc' => $this->l('The Account ID given to you by PayU Latam at the creation of your account.'),
                                                                                          ),
                                                                     'demo' => array(
                                                                                     'name' => 'demo',
                                                                                     'required' => false,
                                                                                     'value' => (Tools::getValue('demo') ? Tools::safeOutput(Tools::getValue('demo')) : Tools::safeOutput(Configuration::get('PAYU_DEMO'))),
                                                                                     'type' => 'radio',
                                                                                     'values' => array(1, 0),
                                                                                     'label' => $this->l('Mode Test:'),
                                                                                     'desc' => $this->l(''),
                                                                                     ))));
return $this->display(__FILE__, 'tpl/credential.tpl');
}

    /**
     * @brief Validate Method
     *
     * @return update the module depending
     */
    private function _postValidation() {
       if (Tools::isSubmit('submitPayU'))
        $this->_postValidationCredentials();
}

private function _postValidationCredentials() {
        // $merchantId = Tools::getValue('merchantId');
        // $apiKey = Tools::getValue('apiKey');
        // //$accountId = Tools::getValue('accountId');

        // if ($merchantId == '' || $apiKey == '')// || $accountId == '')
        //     $this->_postErrors[] = $this->l('Please fill out the entire form.');
}

private function _postProcess() { 
    if (Tools::isSubmit('submitPayU'))
        $this->_postProcessCredentials();
}

private function _postProcessCredentials() {
        //Configuration::updateValue('PAYU_MERCHANT_ID', Tools::safeOutput(Tools::getValue('merchantId')));
        //Configuration::updateValue('PAYU_API_KEY', pSQL(Tools::getValue('apiKey')));
        //Configuration::updateValue('PAYU_ACCOUNT_ID', (int) Tools::getValue('accountId'));
  Configuration::updateValue('PAYU_DEMO', pSQL(Tools::getValue('demo')));
        //echo'Dni Number: ' . Tools::getValue('dninumber');
}

private function _displayErrors() {
    $this->context->smarty->assign('postErrors', $this->_postErrors);
    return $this->display(__FILE__, 'tpl/error.tpl');
}

private function _displayValidation() {
    $this->context->smarty->assign('postValidation', array($this->l('Updated succesfully')));
    return $this->display(__FILE__, 'tpl/validation.tpl');
}

private function _displayWarning() {
    $this->context->smarty->assign('warnings', array($this->l('Please, activate Soap (PHP extension).')));
    return $this->display(__FILE__, 'tpl/warning.tpl');
}

    /**
     * @brief to display the payment option, so the customer will pay by merchant ware
     */
    public function hookPayment($params) {
        if (!$this->active || Configuration::get('PAYU_MERCHANT_ID') == ''){
            return false;
        } 
        //$medios_Psarelas = array();
        foreach (PasarelaPagoCore::GetPMediosPsarelas() as $key => $value) {
           // $medios_Psarelas[$value['nombre']] = $value['pasarela'];
            $this->context->smarty->assign($value['nombre'],$value['pasarela']);
            if($value['nombre'] == 'Tarjeta_credito' && $value['pasarela'] == 'openpay'){
                //$kesy = $this->get_keys_pasarela(); 
                $kesy = PasarelaPagoCore::GetDataConnect("Tarjeta_credito");

                $customer = new Customer($this->context->cart->id_customer);
                $credit_cards = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT * FROM "._DB_PREFIX_."openpay_cards where id_customer = ".$customer->id);

                require_once(_PS_MODULE_DIR_ . 'payulatam/payulatam.php');
                $openpay = Openpay::getInstance($kesy['apilogin_id'], $kesy['apikey_privatekey']);
                
                $op_customer = array_shift($openpay->customers->getList(array('external_id'=>$customer->secure_key.'-'.$customer->id)));

                /*$card = $op_customer->cards->get('k5nwybxlmxcdcaliv2ku');
                ddd($card);
                $sql =  "INSERT INTO `"._DB_PREFIX_."openpay_cards` (`id_customer`, `id_card`, `brand`, `bank`, `card_number`,`holder_name`,`expiration_year`, `expiration_month`)
            VALUES (".$customer->id.", '".$card->id."', '".$card->brand."', '".$card->bank."', '".$card->card_number."', '".$card->holder_name."', '".$card->expiration_year."', '".$card->expiration_month."');";

                ddd($sql);*/


                $this->context->smarty->assign('openpay_key', array('id'=>$kesy ['apilogin_id'],'public_key'=>$kesy ['pseco_publickey'],'mode'=>Configuration::get('PAYU_DEMO')));
                $this->context->smarty->assign('op_credit_cards',$credit_cards);
                
            // la variable "$pasarela_de_pago" se utiliza para determinar que formulario mostrar
                $year = date('Y-m-j');
                $year_select='<select id="año" onchange="cambiaFecha()" name="Year" name="year" data-openpay-card="expiration_year">
                <option value="">año</option>';
                for($i=0; $i<=15; $i++){
                    $str_year = strtotime ( '+'.$i.' year' , strtotime ( $year ) ); 
                    $new_year = date( 'y' , $str_year); 
                    $year_select.='<option value="'.$new_year.'">'.$new_year.'</option>';
                }
                $year_select.='</select>';

                $this->context->smarty->assign('year_select',$year_select);

            }
        }
        
        $this->context->smarty->assign(array('pathSsl' => (_PS_VERSION_ >= 1.4 ? Tools::getShopDomainSsl(true, true) : '' ) . __PS_BASE_URI__ . 'modules/payulatam/', 'modulePath' => $this->_path));

        return $this->display(__FILE__, 'tpl/payment.tpl');
    }

    /**
     * @brief Validate a payment, verify if everything is right
     */
    public function validationws() {
        require_once(_PS_MODULE_DIR_.'payulatam/config.php');
        
        $conf=new ConfPayu();
        
        $keysPayu= $conf->keys();
        
        $currency_iso_code='';
        if($conf->isTest()){
          $currency_iso_code='USD';
      }else{
         $currency_iso_code=$params[9]['currency'];
     }


     if (!isset($_POST['sign']) && !isset($_POST['signature']))
        Logger::AddLog('[Payulatam] the signature is missing.', 2, null, null, null, true);
    else
        $token = isset($_POST['sign']) ? $_POST['sign'] : $_POST['signature'];
    if (!isset($_POST['reference_sale']) && !isset($_POST['referenceCode']))
        Logger::AddLog('[Payulatam] the reference is missing.', 2, null, null, null, true);
    else
        $ref = isset($_POST['reference_sale']) ? $_POST['reference_sale'] : $_POST['referenceCode'];
    if (!isset($_POST['value']) && !isset($_POST['amount']))
        Logger::AddLog('[Payulatam] the amount is missing.', 2, null, null, null, true);
    else
        $amount = isset($_POST['value']) ? $_POST['value'] : $_POST['amount'];

    if (!isset($_POST['merchant_id']) && !isset($_POST['merchantId']))
        Logger::AddLog('[Payulatam] the merchantId is missing.', 2, null, null, null, true);
    else
        $merchantId = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : $_POST['merchantId'];

    if (!isset($_POST['lap_state']) && !isset($_POST['state_pol']))
        Logger::AddLog('[Payulatam] the lap_state is missing.', 2, null, null, null, true);
    else
        $statePol = isset($_POST['lap_state']) ? $_POST['lap_state'] : $_POST['state_pol'];

        //$idCart = substr($ref, 6 + strlen(Configuration::get('PS_SHOP_NAME')));
    $idCart = explode('_', $ref)[2];
    $this->context->cart = new Cart((int) $idCart);
    $total_order = $this->context->cart->getOrderTotals()['total_orden'];
    if (!$this->context->cart->OrderExists()) {
        Logger::AddLog('[Payulatam] The shopping card ' . (int) $idCart . ' doesn\'t have any order created', 2, null, null, null, true);
        return false;
    }
    if (Validate::isLoadedObject($this->context->cart)) {
        $id_orders = Db::getInstance()->ExecuteS('SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . (int) $this->context->cart->id . '');
        foreach ($id_orders as $val) {
            $order = new Order((int) $val['id_order']);
            if ($this->context->cart->getOrderTotalPaid() != $amount)
                Logger::AddLog('[Payulatam] The shopping card ' . (int) $idCart . ' doesn\'t have the correct amount expected during payment validation.' . $keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id . '~' . number_format((float) $this->context->cart->getOrderTotalPaid(), 2, '.', '') . '~' . $currency->iso_code . '~' . $statePol . "---" . $amount, 2, null, null, null, true);
            else {
                $currency = new Currency((int) $this->context->cart->id_currency);
                    if ($token == md5($keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id.'_'.$conf->get_intentos($this->context->cart->id) . '~' . number_format((float) $total_order, 2, '.', '') . '~' . $currency_iso_code . '~' . $statePol) || $token == md5($keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id .'_'.$conf->get_intentos($this->context->cart->id). '~' . number_format((float) $total_order, 1, '.', '') . '~' . $currency_iso_code . '~' . $statePol) || $token == md5($keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id .'_'.$conf->get_intentos($this->context->cart->id). '~' . number_format((float) $total_order, 0, '.', '') . '~' . $currency_iso_code . '~' . $statePol)) { // CUANDO SE ENVIAN # ENTEROS EN EL PAGO A PAYU, ESTE RETORNA 1 DECIMAL, CUANDO SE ENVIAN DECIMALES, PAYU RETORNA 2 DECIMALES. SE VALIDA TAMBIEN SIN DECIMALES EVG GPB
                        if ($statePol == 7){
                            if($order-> getCurrentState() != (int) Configuration::get('PAYU_WAITING_PAYMENT') )
                                $order->setCurrentState((int) Configuration::get('PAYU_WAITING_PAYMENT'));
                        }
                        else if ($statePol == 4){
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_PAYMENT') )
                                $order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));
                        }
                        else {
                            if($order-> getCurrentState() != (int) Configuration::get('PS_OS_ERROR') )
                                $order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
                            Logger::AddLog('[PayU] (payulatam) The shopping card ' . (int) $idCart . ' has been rejected by PayU state pol=' . (int) $statePol, 2, null, null, null, true);
                        }
                    } else
                    Logger::AddLog('[PayU] The shopping card ' . (int) $idCart . ' has an incorrect token given from payU during payment validation.' . $keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id . '~' . number_format((float) $total_order, 2, '.', '') . '~' . $currency->iso_code . '~' . $statePol . "--" . number_format((float) $total_order, 1, '.', '') . "--" . $token, 2, null, null, null, true);
                }
                if (_PS_VERSION_ >= 1.5) {
                    $payment = $order->getOrderPaymentCollection();
                    if (isset($payment[0])) {
                        $payment[0]->transaction_id = pSQL("payU_farmalisto_".$idCart);
                        $payment[0]->save();
                    }
                }
            }
        } else {
            Logger::AddLog('[PayU] The shopping card ' . (int) $idCart . ' was not found during the payment validation step', 2, null, null, null, true);
        }
    }

    public function validation() {
        
     
        if (!isset($_POST['sign']) && !isset($_POST['signature']))
            Logger::AddLog('[Payulatam] the signature is missing.', 2, null, null, null, true);
        else
            $token = isset($_POST['sign']) ? $_POST['sign'] : $_POST['signature'];
        if (!isset($_POST['reference_sale']) && !isset($_POST['referenceCode']))
            Logger::AddLog('[Payulatam] the reference is missing.', 2, null, null, null, true);
        else
            $ref = isset($_POST['reference_sale']) ? $_POST['reference_sale'] : $_POST['referenceCode'];
        if (!isset($_POST['value']) && !isset($_POST['amount']))
            Logger::AddLog('[Payulatam] the amount is missing.', 2, null, null, null, true);
        else
            $amount = isset($_POST['value']) ? $_POST['value'] : $_POST['amount'];

        if (!isset($_POST['merchant_id']) && !isset($_POST['merchantId']))
            Logger::AddLog('[Payulatam] the merchantId is missing.', 2, null, null, null, true);
        else
            $merchantId = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : $_POST['merchantId'];

        if (!isset($_POST['lap_state']) && !isset($_POST['state_pol']))
            Logger::AddLog('[Payulatam] the lap_state is missing.', 2, null, null, null, true);
        else
            $statePol = isset($_POST['lap_state']) ? $_POST['lap_state'] : $_POST['state_pol'];

       // $idCart = substr($ref, 6 + strlen(Configuration::get('PS_SHOP_NAME')));
        $idCart = explode('_', $ref)[2];
        $this->context->cart = new Cart((int) $idCart);

        if (!$this->context->cart->OrderExists()) {
            Logger::AddLog('[Payulatam] The shopping card ' . (int) $idCart . ' doesn\'t have any order created', 2, null, null, null, true);
            return false;
        }

        if (Validate::isLoadedObject($this->context->cart)) {
            $id_orders = Db::getInstance()->ExecuteS('SELECT `id_order` FROM `' . _DB_PREFIX_ . 'orders` WHERE `id_cart` = ' . (int) $this->context->cart->id . '');
            foreach ($id_orders as $val) {
                $order = new Order((int) $val['id_order']);

                // if ($this->context->cart->getOrderTotal() != $amount)
                 //   Logger::AddLog('[Payulatam] The shopping card ' . (int) $idCart . ' doesn\'t have the correct amount expected during payment validation.' . $this->context->cart->getOrderTotal() . " --- " . $amount, 2, null, null, null, true);
               // else {
                $currency = new Currency((int) $this->context->cart->id_currency);
                   // if ($token == md5($keysPayu['apiKey'] . '~' . Tools::safeOutput($keysPayu['merchantId']) . '~payU_' . Configuration::get('PS_SHOP_NAME') . '_' . (int) $this->context->cart->id . '~' . (float) $this->context->cart->getOrderTotal() . '~' . $currency->iso_code . '~' . $statePol)) {
                if ($statePol == 7)
                    $order->setCurrentState((int) Configuration::get('PAYU_WAITING_PAYMENT'));
                else if ($statePol == 4)
                    $order->setCurrentState((int) Configuration::get('PS_OS_PAYMENT'));
                else {
                    $order->setCurrentState((int) Configuration::get('PS_OS_ERROR'));
                    Logger::AddLog('[PayU] The shopping card ' . (int) $idCart . ' has been rejected by PayU state pol=' . (int) $statePol, 2, null, null, null, true);
                }
                  //  } else
                    //    Logger::AddLog('[PayU] The shopping card ' . (int) $idCart . ' has an incorrect token given from payU during payment validation', 2, null, null, null, true);
               // }
                if (_PS_VERSION_ >= 1.5) {
                    $payment = $order->getOrderPaymentCollection();
                    if (isset($payment[0])) {
                        $payment[0]->transaction_id = pSQL($ref);
                        $payment[0]->save();
                    }
                }
            }
        } else {
            Logger::AddLog('[PayU] The shopping card ' . (int) $idCart . ' was not found during the payment validation step', 2, null, null, null, true);
        }
    }
    
    public function addCartRuleBin($numero_tarjeta = NULL) {
        if (isset($numero_tarjeta) && $numero_tarjeta != '') {
            $numero_tarjeta= trim($numero_tarjeta);
            Logger::AddLog('[PayU] CC ' . (int) $numero_tarjeta . ' was not found during the payment validation step', 2, null, null, null, true);
            $bin = substr($numero_tarjeta, 0, 6);
            Logger::AddLog('Bin ' . (int) $bin . ' was not found during the payment validation step', 2, null, null, null, true);
            if (is_numeric($bin)) {

             $date = date('Y-m-d H:i:s');
             
             $query = "select bin.id_cart_rule,fran.nombre as franquicia, banco.nombre as banco, bin.bin

             FROM
             ps_bines bin
             INNER JOIN ps_franquicia fran ON(fran.id_franquicia=bin.id_franquicia )
             INNER JOIN ps_banco banco ON(banco.id_banco=bin.id_banco)
             INNER JOIN ps_cart_rule rules ON(bin.id_cart_rule=rules.id_cart_rule)
             WHERE bin.bin= " . (int) $bin . " AND rules.active=1 AND '".$date."' BETWEEN rules.date_from  AND rules.date_to
             GROUP BY bin.bin;";

             if ($results = Db::getInstance()->ExecuteS($query)) {

                if (count($results) > 0 && !$this->context->cart->validateProgressiveDiscountInCart()) {

// Eliminar todos los cupones del carrito

                    $this->context->cart->removeCartRules();

// Agregar el cupon de los Bines
                    $this->context->cart->addCartRule((int) $results[0]['id_cart_rule']);
                    $this->context->cart->update();

                        // se setea la variable bandera removeRulesGroup para que no retire los descuentos de categoria
                        //$this->context->cart->removeRulesGroup = false;

                    return $bin;
                }
            }
            return $bin;
        }
    }

    return FALSE;
}

    /*
     * 
     */
    
    public function addCartRulePse($codigo_banco = NULL) {
        if (isset($codigo_banco) && $codigo_banco != '') {

            if (is_numeric($codigo_banco)) {

                $date = date('Y-m-d H:i:s');

                $query = "select pse.id_cart_rule, banco.nombre, banco.img_beneficio,rule.reduction_percent
                from
                ps_banco banco INNER JOIN ps_pse_cart_rule pse ON(banco.id_banco=pse.id_banco)
                INNER JOIN ps_cart_rule rule ON(pse.id_cart_rule=rule.id_cart_rule)
                WHERE banco.codigo= " . (int) $codigo_banco . " AND rule.active=1 AND '" . $date . "' BETWEEN rule.date_from  AND rule.date_to;";

                //return $query;     
                if ($results = Db::getInstance()->ExecuteS($query)) {

                    if (count($results) > 0 && !$this->context->cart->validateProgressiveDiscountInCart()) {

                        // Eliminar todos los cupones del carrito

                        $this->context->cart->removeCartRules();

                        // Agregar el cupon de los Bines
                        $this->context->cart->addCartRule((int) $results[0]['id_cart_rule']);
                        $this->context->cart->update();

                        // se setea la variable bandera removeRulesGroup para que no retire los descuentos de categoria
                        //$this->context->cart->removeRulesGroup = false;

                        return TRUE;
                    }
                }
            }
        }
        
        return FALSE;
    }

    public function get_keys_pasarela(){

        $mode=1;

        if(Configuration::get('PAYU_DEMO') == 1){
         $mode=0;
     }

     $sql="SELECT datos.mercid,datos.apikey_privatekey ,datos.pseco_publickey, datos.apilogin_id, datos.accountid FROM "._DB_PREFIX_."datos_pasarelas_pago datos INNER JOIN "._DB_PREFIX_."medios_de_pago mediosp ON (datos.medios_pago = mediosp.id_medio_de_pago)
     INNER JOIN "._DB_PREFIX_."pasarelas_pago pasarela ON(datos.id_pasarela = pasarela.id_pasarela)
     WHERE pasarela.nombre ='openpay' AND datos.produccion = ".$mode.";";
     $row = Db::getInstance()->getRow($sql);

     if ( !empty($row) && count($row) > 0) {  
        return $row;
    }
    return FALSE;
}

public function process_ajax(){
    $case = Tools::getValue('option_ajax');

    switch ($case) {
        case 'nueva_pasarela':
        if(Tools::getValue('option_ajax') && !empty(Tools::getValue('option_ajax'))){
            if(PasarelaPagoCore::AddPasarela())
                exit(json_encode(array('message' => 'OK','code' => '200' )));
            exit(json_encode(array('message' => 'Error interno del servidor','code' => '500' )));
        }else{
            exit(json_encode(array('message' => 'Petición errónea','code' => '400' )));
        }          
        break;
        case 'datos_pasarela':
        $result = PasarelaPagoCore::GetDatosPasarelas();
        if( count($result)>0 && is_array($result)){
            exit(json_encode(array('message' => 'OK','code' => '200','results' => $result )));
        }else{ 
            exit(json_encode(array('message' => 'No hay contenido disponible','code' => '204' )));
        }
        break;    
        
        default:
            # code...
        break;
    }

    exit(json_encode(array('message' => 'Opción no encontrada','code' => '404' )));
}

}
