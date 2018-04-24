<?php

/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class PasswordController extends PasswordControllerCore
{


    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('ajax')) {

            if (Tools::isSubmit('checkEmail')) {

                if (!($email = trim(Tools::getValue('email'))) || !Validate::isEmail($email)) {
                    $this->errors[] = Tools::displayError('Invalid email address.');
                } else {
                    $email = trim(Tools::getValue('email'));
                    $customer = new Customer();

                    $customer->getByemail($email);

                    if (!Validate::isLoadedObject($customer))
                        $this->errors[] = Tools::displayError('There is no account registered for this email address.');
                    elseif (!$customer->active)
                        $this->errors[] = Tools::displayError('You cannot regenerate the password for this account.');

                }

                if (empty($this->errors)) {
                    $phones = $customer->getValidPhones();

                    die(json_encode(array('email' => $this->obfuscate_email($email), 'phones' => $phones, 'id_customer' => $customer->id)));
                } else {
                    die(json_encode(array('errors' => $this->errors)));
                }

            }

            if (Tools::isSubmit('rememberPassword')) {
                if (trim(Tools::getValue('via')) == 'mail') {
                    $this->sendMail();
                }
                if (trim(Tools::getValue('via')) == 'tel') {
                    $this->sendSms();
                }
            }

            if (Tools::isSubmit('checkCode')) {
                $id_customer = Tools::getValue('id');
                $code = Tools::getValue('code');


                $existsCode = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM ps_forgot_password where id_customer = '.$id_customer.' AND code = '.$code);

                if($existsCode){
                    $customer = new Customer($id_customer);
                    $url = $this->context->link->getPageLink('password', true, null, 'token=' . $customer->secure_key . '&id_customer=' . (int)$customer->id);
                    die(json_encode(array('url'=>$url)));
                }
                else{
                    die(json_encode(array('error'=>'Código no existe')));
                }
            }

            if (Tools::isSubmit('changePassword')) {
                if (($token = Tools::getValue('c_token')) && ($id_customer = (int)Tools::getValue('id_customer'))) {
                    $email = Db::getInstance()->getValue('SELECT `email` FROM ' . _DB_PREFIX_ . 'customer c WHERE c.`secure_key` = \'' . pSQL($token) . '\' AND c.id_customer = ' . (int)$id_customer);
                    if($email){
                        $customer = new Customer($id_customer);
                        $newPassword = Tools::getValue('password');

                        $customer->passwd = Tools::encrypt($newPassword);
                        $customer->last_passwd_gen = date('Y-m-d H:i:s', time());

                        if ($customer->update()){
                            die(json_encode(array('email'=>$email, 'name'=>$customer->firstname)));
                        }
                    }
                }


                die(json_encode(array('error'=>'Problema al cambiar passsword')));
            }
        }
    }

    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->display_footer = false;
        FrontController::initContent();

        if (($token = Tools::getValue('token')) && ($id_customer = (int)Tools::getValue('id_customer'))) {
            $email = Db::getInstance()->getValue('SELECT `email` FROM ' . _DB_PREFIX_ . 'customer c WHERE c.`secure_key` = \'' . pSQL($token) . '\' AND c.id_customer = ' . (int)$id_customer);
            if ($email) {
                $this->context->smarty->assign(array('token' => $token, 'id_customer' => $id_customer));
                $this->setTemplate(_PS_THEME_DIR_ . 'new-password.tpl');
            }
        } else {
            $this->setTemplate(_PS_THEME_DIR_ . 'password.tpl');
        }
    }

    private function obfuscate_email($email)
    {
        $em = explode("@", $email);
        $name = implode(array_slice($em, 0, count($em) - 1), '@');
        $len = floor(strlen($name) / 2);

        return substr($name, 0, $len) . str_repeat('*', $len) . "@" . end($em);
    }

    private function sendMail()
    {
        $id_customer = trim(Tools::getValue('id'));
        $customer = new Customer($id_customer);

        if (!Validate::isLoadedObject($customer))
            $this->errors[] = Tools::displayError('There is no account registered for this email address.');
        elseif (!$customer->active)
            $this->errors[] = Tools::displayError('You cannot regenerate the password for this account.');
        else {
            $mail_params = array(
                '{email}' => $customer->email,
                '{lastname}' => $customer->lastname,
                '{firstname}' => $customer->firstname,
                '{url}' => $this->context->link->getPageLink('password', true, null, 'token=' . $customer->secure_key . '&id_customer=' . (int)$customer->id),
                '{url_route}' => $content_dir
            );
            if (Mail::Send($this->context->language->id, 'password_query', Mail::l('Password query confirmation'), $mail_params, $customer->email, $customer->firstname . ' ' . $customer->lastname))
                $this->context->smarty->assign(array('confirmation' => 2, 'customer_email' => $customer->email));
            else
                $this->errors[] = Tools::displayError('An error occurred while sending the email.');
        }

        if (empty($this->errors)) {
            die(json_encode($customer));
        } else {
            die(json_encode(array('errors' => $this->errors)));
        }


    }

    private function sendSms()
    {
        $id_addr = Tools::getValue('id');

        $id_customer = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue("SELECT id_customer FROM ps_address where id_address=" . $id_addr);
        $customer = new Customer($id_customer);

        $phones = $customer->getValidPhones();

        $valid_phone = null;
        foreach ($phones as $phone) {
            if ($phone['id_address_delivery'] == $id_addr) {
                $valid_phone = $phone['valid_phone'];
            }
        }

        if ($valid_phone) {
            if(strlen($valid_phone)==10)
                $valid_phone = str_pad($valid_phone, 12, "57", STR_PAD_LEFT);


            $code = rand(100000, 999999);

            DB::getInstance()->execute("REPLACE INTO ps_forgot_password (id_customer, code, date_add) VALUES ($id_customer,$code,NOW())");

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://api.infobip.com/sms/1/text/single",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{ \"from\":\"FarmalistoColombia\", \"to\":\"$valid_phone\", \"text\":\"Código de recuperación de contraseña: " . $code . "\" }",
                CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    "authorization: Basic TUVSQ0FERU8uRkFSTUFMSVNUTzpGYXJtYWxpc3RvMTIz",
                    "content-type: application/json"
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);


            die(json_encode(array('response' => $response,'id_customer'=>$id_customer)));

        } else {
            die(json_enccode(array('error' => 'No se ha podido enviar SMS')));
        }
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_ . 'min-login.css', 'all');
    }

}

