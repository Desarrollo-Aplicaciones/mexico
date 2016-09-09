<?php

include_once('hubspot_sync.php');

class Hubspot extends Module
{

    public function __construct()
    {
        $this->name = 'hubspot';
        $this->tab = 'advertising_and_marketing';
        $this->version = '1.1';
        $this->author = 'SergeyH';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->l('Hubspot integration');
        $this->description = $this->l('Create customers and deals on Hubspot side.');
        // $this->ps_versions_compliancy = array( 'min' => '1.5.0.0','max' => _PS_VERSION_);
    }

    public function install()
    {
        return (
            parent::install() && $this->registerHook('actionValidateOrder')
        );

    }

    public function uninstall()
    {
        return (
            parent::uninstall() && Configuration::deleteByName('PS_HUBSPOT_KEY')
        );
    }

    public function hookActionValidateOrder($params)
    {
        try {
            $address = new Address($params['cart']->id_address_delivery);
            $state = State::getNameById($address->id_state);
            $hub = new HubspotSync(Configuration::get('PS_HUBSPOT_KEY'));
            $hub->createContactAndDeals($address, $params['customer'], $params['cart']->getProducts(), $state);
        } catch (Exception $e) {
//            echo 'Error to sync with Hubspot: ' . $e->getMessage();
//            file_put_contents(dirname(__FILE__) . '/log/cartid-' . $params['cart']->id . '.log', print_r($e, true));
        }
    }


    public function getContent()
    {
        $output = '';
        $errors = array();

        if (Tools::isSubmit('submitCredentials')) {
            $key = Tools::getValue('PS_HUBSPOT_KEY');

            if (!$key OR !Validate::isString($key))
                $errors[] = $this->l('Invalid API key');

            if (!count($errors)) {
                Configuration::updateValue('PS_HUBSPOT_KEY', $key);

                $output .= $this->displayConfirmation($this->l('Credentials updated'));
            } else
                $output .= $this->displayError(implode('<br />', $errors));
        }
        return $output . $this->renderForm();
    }

    public function renderForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Credentials'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'password',
                        'label' => $this->l('API key'),
                        'name' => 'PS_HUBSPOT_KEY',
                        'class' => 'fixed-width-xl',
                        'desc' => $this->l('Hubspot API key'),
                    ),

                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCredentials';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    /**
     * @return array
     */
    public function getConfigFieldsValues()
    {
        return array(
            'PS_HUBSPOT_KEY' => Tools::getValue('PS_HUBSPOT_KEY', Configuration::get('PS_HUBSPOT_KEY')),
        );
    }
}