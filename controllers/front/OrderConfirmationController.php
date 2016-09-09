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

class OrderConfirmationControllerCore extends FrontController
{
	public $php_self = 'order-confirmation';

	public $id_cart;
	public $id_module;
	public $id_order;
	public $reference;
	public $secure_key;
	public $url_banco;

	/**
	 * Initialize order confirmation controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		parent::init();

		$this->id_cart = (int)(Tools::getValue('id_cart', 0));
		$is_guest = false;

		/* check if the cart has been made by a Guest customer, for redirect link */
		if (Cart::isGuestCartByCartId($this->id_cart))
		{
			$is_guest = true;
			$redirectLink = 'index.php?controller=guest-tracking';
		}
		else
			$redirectLink = 'index.php?controller=history';

		$this->id_module = (int)(Tools::getValue('id_module', 0));
		$this->id_order = Order::getOrderByCartId((int)($this->id_cart));
		$this->secure_key = Tools::getValue('key', false);
		$order = new Order((int)($this->id_order));
		if ($is_guest)
		{
			$customer = new Customer((int)$order->id_customer);
			$redirectLink .= '&id_order='.$order->reference.'&email='.urlencode($customer->email);
		}
		if (!$this->id_order || !$this->id_module || !$this->secure_key || empty($this->secure_key))
			Tools::redirect($redirectLink.(Tools::isSubmit('slowvalidation') ? '&slowvalidation' : ''));
		$this->reference = $order->reference;
		if (!Validate::isLoadedObject($order) || $order->id_customer != $this->context->customer->id || $this->secure_key != $order->secure_key)
			Tools::redirect($redirectLink);
		$module = Module::getInstanceById((int)($this->id_module));
		if (isset($order->payment) && isset($module->displayName) && $order->payment != $module->displayName && ( !in_array($order->payment, $this->get_mediosp()) ))
			Tools::redirect($redirectLink);


		$url_banco2 = Tools::getValue('bankdest2', 0);
		$url_dec_64 = base64_decode(strtr($url_banco2, '-_,', '+/='));

		if ($url_banco2 != false) {	
			$this->url_banco = $url_dec_64;			
		}

	}
        
        /*
         * Lista medios de pago 
         */
        public  function get_mediosp(){
          //in_array("Irix", $this->get_mediosp())
            $mediosp=array();
               
                   $query="select nombre from ps_medios_de_pago limit 100;";
            $results = Db::getInstance()->ExecuteS($query);
            if (count($results)>0 ) {
               
                foreach ($results as $value) {
                    $mediosp[]=$value["nombre"];
                    
                }
                              
            }
            return $mediosp;  
        }

        /**
	 * Assign template vars related to page content
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();

		$this->context->smarty->assign(array(
			'is_guest' => $this->context->customer->is_guest,
			'HOOK_ORDER_CONFIRMATION' => $this->displayOrderConfirmation(),
			'HOOK_PAYMENT_RETURN' => $this->displayPaymentReturn()
		));

		if ($this->context->customer->is_guest)
		{
			$this->context->smarty->assign(array(
				'id_order' => $this->id_order,
				'reference_order' => $this->reference,
				'id_order_formatted' => sprintf('#%06d', $this->id_order),
				'email' => $this->context->customer->email
			));
			/* If guest we clear the cookie for security reason */
			$this->context->customer->mylogout();
		}

		if($this->url_banco != null) {
			$this->context->smarty->assign(array(
			'pse' => true,
			'bankdest2' => $this->url_banco
		));
		}

		$this->setTemplate(_PS_THEME_DIR_.'order-confirmation.tpl');
	}

	/**
	 * Execute the hook displayPaymentReturn
	 */
	public function displayPaymentReturn()
	{
		if (Validate::isUnsignedId($this->id_order) && Validate::isUnsignedId($this->id_module))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayPaymentReturn', $params, $this->id_module);
			}
		}
		return false;
	}

	/**
	 * Execute the hook displayOrderConfirmation
	 */
	public function displayOrderConfirmation()
	{
		if (Validate::isUnsignedId($this->id_order))
		{
			$params = array();
			$order = new Order($this->id_order);
			$currency = new Currency($order->id_currency);

			if (Validate::isLoadedObject($order))
			{
				$params['total_to_pay'] = $order->getOrdersTotalPaid();
				$params['currency'] = $currency->sign;
				$params['objOrder'] = $order;
				$params['currencyObj'] = $currency;

				return Hook::exec('displayOrderConfirmation', $params);
			}
		}
		return false;
	}
}

