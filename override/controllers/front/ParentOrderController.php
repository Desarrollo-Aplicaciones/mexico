<?php


class ParentOrderController extends ParentOrderControllerCore  {
  
    protected function _assignSummaryInformations(){
        $summary = $this->context->cart->getSummaryDetails();
        $customizedDatas = Product::getAllCustomizedDatas($this->context->cart->id);

        // override customization tax rate with real tax (tax rules)
        if ($customizedDatas)
        {
            foreach ($summary['products'] as &$productUpdate)
            {
                $productId = (int)(isset($productUpdate['id_product']) ? $productUpdate['id_product'] : $productUpdate['product_id']);
                $productAttributeId = (int)(isset($productUpdate['id_product_attribute']) ? $productUpdate['id_product_attribute'] : $productUpdate['product_attribute_id']);

                if (isset($customizedDatas[$productId][$productAttributeId]))
                    $productUpdate['tax_rate'] = Tax::getProductTaxRate($productId, $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
            }

            Product::addCustomizationPrice($summary['products'], $customizedDatas);
        }

        $cart_product_context = Context::getContext()->cloneContext();
        foreach ($summary['products'] as $key => &$product)
        {
            $product['quantity'] = $product['cart_quantity'];// for compatibility with 1.2 themes

            if ($cart_product_context->shop->id != $product['id_shop'])
                $cart_product_context->shop = new Shop((int)$product['id_shop']);
            $product['price_without_specific_price'] = Product::getPriceStatic(
                $product['id_product'], 
                !Product::getTaxCalculationMethod(), 
                $product['id_product_attribute'], 
                2, 
                null, 
                false, 
                false,
                1,
                false,
                null,
                null,
                null,
                $null,
                true,
                true,
                $cart_product_context);

            if (Product::getTaxCalculationMethod())
                $product['is_discounted'] = $product['price_without_specific_price'] != $product['price'];
            else
                $product['is_discounted'] = $product['price_without_specific_price'] != $product['price_wt'];
        }

        // Get available cart rules and unset the cart rules already in the cart
        $available_cart_rules = CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ? $this->context->customer->id : 0), true, true, true, $this->context->cart);
        $cart_cart_rules = $this->context->cart->getCartRules();
        foreach ($available_cart_rules as $key => $available_cart_rule)
        {
            if (!$available_cart_rule['highlight'] || strpos($available_cart_rule['code'], 'BO_ORDER_') === 0)
            {
                unset($available_cart_rules[$key]);
                continue;
            }
            foreach ($cart_cart_rules as $cart_cart_rule)
                if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule'])
                {
                    unset($available_cart_rules[$key]);
                    continue 2;
                }
        }

        $show_option_allow_separate_package = (!$this->context->cart->isAllProductsInStock(true) && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));

        $rep_servier = $this->getIdAndNameFromAsociadoServier();
//        echo '<pre>';
//        var_dump($rep_servier);
//        exit(0);
        $this->context->smarty->assign($summary);
        $this->context->smarty->assign(array(
            'token_cart' => Tools::getToken(false),
            'isLogged' => $this->isLogged,
            'isVirtualCart' => $this->context->cart->isVirtualCart(),
            'productNumber' => $this->context->cart->nbProducts(),
            'voucherAllowed' => CartRule::isFeatureActive(),
            'shippingCost' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
            'shippingCostTaxExc' => $this->context->cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
            'customizedDatas' => $customizedDatas,
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'lastProductAdded' => $this->context->cart->getLastProduct(),
            'displayVouchers' => $available_cart_rules,
            'currencySign' => $this->context->currency->sign,
            'currencyRate' => $this->context->currency->conversion_rate,
            'currencyFormat' => $this->context->currency->format,
            'currencyBlank' => $this->context->currency->blank,
            'show_option_allow_separate_package' => $show_option_allow_separate_package,
            'smallSize' => Image::getSize(ImageType::getFormatedName('small')),
            'rep_servier'=> $rep_servier,
        ));

        $this->context->smarty->assign(array(
            'HOOK_SHOPPING_CART' => Hook::exec('displayShoppingCartFooter', $summary),
            'HOOK_SHOPPING_CART_EXTRA' => Hook::exec('displayShoppingCart', $summary)
        ));
    }
        
    public function getIdAndNameFromAsociadoServier() {
        $sql = "SELECT  ass.id_asociado, ass.nombre FROM ps_asociado_servier ass WHERE ass.estado = 1;";
        $result = array(DB::getInstance()->executeS($sql));
        return $result[0];
    }

    public function init()
    {
        if (Module::isEnabled('quantitydiscountpro')) {
            include_once(_PS_MODULE_DIR_.'quantitydiscountpro/quantitydiscountpro.php');
            $quantityDiscount = new QuantityDiscountRule();

            $this->isLogged = (bool)($this->context->customer->id && Customer::customerIdExistsStatic((int)$this->context->cookie->id_customer));

            parent::init();

            /* Disable some cache related bugs on the cart/order */
            header('Cache-Control: no-cache, must-revalidate');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

            $this->nbProducts = $this->context->cart->nbProducts();

            if (!$this->context->customer->isLogged(true) && $this->context->getMobileDevice() && Tools::getValue('step')) {
                Tools::redirect($this->context->link->getPageLink('authentication', true, (int)$this->context->language->id));
            }

            // Redirect to the good order process
            if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 0 && Dispatcher::getInstance()->getController() != 'order') {
                Tools::redirect('index.php?controller=order');
            }

            if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1 && Dispatcher::getInstance()->getController() != 'orderopc') {
                if (Tools::getIsset('step') && Tools::getIsset('step') == 3) {
                    Tools::redirect('index.php?controller=order-opc&isPaymentStep=true');
                }
                Tools::redirect('index.php?controller=order-opc');
            }

            if (Configuration::get('PS_CATALOG_MODE')) {
                $this->errors[] = Tools::displayError('This store has not accepted your new order.');
            }

            if (Tools::isSubmit('submitReorder') && $id_order = (int)Tools::getValue('id_order')) {
                $oldCart = new Cart(Order::getCartIdStatic($id_order, $this->context->customer->id));
                $duplication = $oldCart->duplicate();
                if (!$duplication || !Validate::isLoadedObject($duplication['cart'])) {
                    $this->errors[] = Tools::displayError('Sorry. We cannot renew your order.');
                } elseif (!$duplication['success']) {
                    $this->errors[] = Tools::displayError('Some items are no longer available, and we are unable to renew your order.');
                } else {
                    $this->context->cookie->id_cart = $duplication['cart']->id;
                    $this->context->cookie->write();

                    $quantityDiscount->createAndRemoveRules();

                    if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                        Tools::redirect('index.php?controller=order-opc');
                    }
                    Tools::redirect('index.php?controller=order');
                }
            }

            if ($this->nbProducts) {
                if (CartRule::isFeatureActive()) {
                    if (Tools::isSubmit('submitAddDiscount')) {
                        if (!($code = trim(Tools::getValue('discount_name')))) {
                            $this->errors[] = Tools::displayError('You must enter a voucher code.');
                        } elseif (!Validate::isCleanHtml($code)) {
                            $this->errors[] = Tools::displayError('The voucher code is invalid.');
                        } else {
                            if (($quantityDiscount = new quantityDiscountRule(QuantityDiscountRule::getQuantityDiscountRuleByCode($code))) && Validate::isLoadedObject($quantityDiscount)) {
                                // Add valid rule with discount code
                                if ($quantityDiscount->createAndRemoveRules($code)) {
                                    if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
                                        Tools::redirect('index.php?controller=order-opc&addingCartRule=1');
                                    }

                                    Tools::redirect('index.php?controller=order&addingCartRule=1');
                                } else {
                                    $this->errors[] = Tools::displayError('You cannot use this voucher');
                                }
                            } elseif (($cartRule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cartRule)) {
                                if ($quantityDiscount->cartRuleGeneratedByAQuantityDiscountRuleCode($code)) {
                                    //Check if user is trying to manually add a coupon generated automatically
                                    $this->errors[] = Tools::displayError('The voucher code is invalid.');
                                } elseif ($error = $cartRule->checkValidity($this->context, false, true)) {
                                    $this->errors[] = $error;
                                } else {
                                    $this->context->cart->addCartRule($cartRule->id);
                                    Tools::redirect('index.php?controller=order-opc');
                                }
                            } else {
                                $this->errors[] = Tools::displayError('This voucher does not exists.');
                            }
                        }
                        $this->context->smarty->assign(array(
                            'errors' => $this->errors,
                            'discount_name' => Tools::safeOutput($code)
                        ));
                    } elseif (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule)) {
                        $quantityDiscount->removeQuantityDiscountCartRule($id_cart_rule, (int)$this->context->cart->id);
                        $this->context->cart->removeCartRule($id_cart_rule);
                        Tools::redirect('index.php?controller=order-opc');
                    }
                }
                /* Is there only virtual product in cart */
                if ($isVirtualCart = $this->context->cart->isVirtualCart()) {
                    $this->setNoCarrier();
                }
            }

            $this->context->smarty->assign('back', Tools::safeOutput(Tools::getValue('back')));
        } else {
            parent::init();
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        if (Module::isEnabled('quantitydiscountpro') && ((int)(Configuration::get('PS_BLOCK_CART_AJAX')) || Configuration::get('PS_ORDER_PROCESS_TYPE') == 1 || Tools::getValue('step') == 2)) {
            $this->addJS(_MODULE_DIR_.'quantitydiscountpro/views/js/qdp.js');
        }
    }


}