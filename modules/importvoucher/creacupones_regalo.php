<?php

include_once(dirname(__FILE__)."/../../config/config.inc.php");

/**
 //**Script para creación automatica de cupones restringidos a producto
 */

if ( isset( $_REQUEST['creacupon'] ) && $_REQUEST['creacupon'] == 'ok' ) {


    if ( isset( $_REQUEST['codigo'] ) ) {

        $codigo = $_REQUEST['codigo'];

    } else {

        $codigo = 1;

    }

    $total_prods['3594450400093'] = 30;
    $total_prods['5391189200776'] = 30;
    $total_prods['5391189200851'] = 30;
    $total_prods['5391189220767'] = 30;
    $total_prods['5391189230797'] = 30;
    $total_prods['5391189230759'] = 30;
    $total_prods['3594452600958'] = 30;
    $total_prods['3594456400646'] = 30;
    $total_prods['3594456400653'] = 30;
    $total_prods['3594455800232'] = 30;
    $total_prods['3594455200162'] = 30;
    $total_prods['3594453800432'] = 30;
    $total_prods['3594454400648'] = 30;
    $total_prods['3594451300422'] = 30;
    $total_prods['3594451400412'] = 30;
    $total_prods['3594450700438'] = 30;
    $total_prods['3594450700421'] = 30;

    //3594456400103



                    foreach ( $total_prods as $key => $value ) {
                            $dto_regalo_applicar = 0;
                            $prod_regalar = 0;
                            $porc_dto = 0;
                            $mone_dto = 0;
                            $product_restriction = 0;
                            $prods = Product::searchByReference( $key, 'nadro', 1); //cambiar [para cualquiera]
                            //////--echo "<br> REF: ". $key ;
                            // si el producto está asociado al proveedor nadro, está activo y 

                            if ( isset( $prods['id_product'] ) && $prods['id_product'] != '' && $prods['id_product'] != 0 ) {
                                //////--echo "<br> si prod: ". $key ;

                                    //////--echo "<br> ** REf: ".$key." - - ".$prods_cart[ $key ]['name']." Iguales ";
                                    //////--echo "<br> -- Compradas cart = ".$prods_cart[ $key ]['PiezasPorComprar'];
                                    //echo "<br> -- Compradas webs = ".$prods_ret_array[ $key ]['PiezasPorComprar'];
                                /* if (  $prods_ret_array[ $key ]['PiezasGratis'] != 0 ) {
                                        //////--echo "<br> -- gratis == ".$prods_ret_array[ $key ]['PiezasGratis'];
                                        $prod_regalar = $prods['id_product'];
                                        $dto_regalo_applicar = 1;
                                        $product_restriction = 1;
                                    }
                                    if (  $prods_ret_array[ $key ]['PorcentajeDescuento'] != 0 ) {
                                */
                                        //////--echo "<br> -- PorcentajeDescuento = ".$prods_ret_array[ $key ]['PorcentajeDescuento'];
                                        $porc_dto =  $value ;
                                        $dto_regalo_applicar = 2;
                                    /*}
                                    elseif (  $prods_ret_array[ $key ]['MontoDescuento'] != 0 ) {
                                        //////--echo "<br> -- MontoDescuento = ".$prods_ret_array[ $key ]['MontoDescuento'];
                                        //$dto_monto_acum += $prods_ret_array[ $key ]['MontoDescuento'];
                                        $mone_dto = $prods_ret_array[ $key ]['MontoDescuento'];
                                        $dto_regalo_applicar = 3;
                                    }*/



                                /*elseif ( isset( $total_prods[ $key ]['cart'] ) && $total_prods[ $key ]['cart'] == 1 &&  !isset( $total_prods[ $key ]['ws'] )  ) {
                                }*/
                                /***************** CREACION DE CUPONES ********************/
                                /*echo "<pre> this object : ";
                                print_r( $this->context );
                                echo "</pre>";*/
                                /*
                                echo "<br> dto: ".$dto_regalo_applicar;
                                echo "<br> id_cliente: ".$customer_id;
                                echo "<br> id_cart: ".$this->context->cart->id;
                                */
                                if ( $dto_regalo_applicar != 0 ) {
                                    //////--echo "<br><hr> aplicar dto --".$this->context->cart->id;						
                                    /*					
                                    $cart_rule = new CartRule(25);
                                    $cart_rule->reduction_amount = 88;
                                    if (!$cart_rule->update())
                                                    echo "<br> Cupon NOOOOOO actualizado-".$cart_rule->name[1];
                                            else
                                            {
                                                    echo "<br> Cupon actualizado-".$cart_rule->name[1];
                                            }

                                            $cart_rule = new CartRule(25);
                                    echo "<hr><pre>  REGLAS CARRITO: ";
                                    print_r( $cart_rule );
                                    echo "<br></pre><hr> ";
                                    */
                                    $cart_rule = new CartRule();							
                                    $languages = Language::getLanguages(false);
                                    /*echo "<pre> orden: ";
                                    print_r($this);
                                    exit();*/
                                    foreach ($languages as $language)
                                        // Define a temporary name
                                        $cart_rule->name[$language['id_lang']] = sprintf( 'SERVIER%d', $prods['id_product'] );

                                    // Define a temporary code
                                    //$cart_rule->code = ''; //sprintf('CIRSA_C%1$d_O%2$d', $customer_id, $this->context->cart->id);
                                    $cart_rule->quantity = 999;
                                    $cart_rule->quantity_per_user = 999;
                                    //////echo "<br> paso1";

                                    // Specific to the customer
                                    //$cart_rule->id_customer = $customer_id;
                                    $now = time();
                                    $cart_rule->date_from = date('Y-m-d H:i:s', $now - (3600) );
                                    $cart_rule->date_to = date('Y-m-d H:i:s', $now + (3600 * 24 * 365.25)); // 3600 * 24 * 1: 3 hours  -   3600 * 24 * 1: 1 Day -  3600 * 24 * 365.25: 1 year  
                                    $cart_rule->partial_use = 0;
                                    $cart_rule->active = 1;
                                    $cart_rule->reduction_product = $prods['id_product'];
                                    //////echo "<br> paso2";
                                    $add_desc_name = '';
                                    switch ($dto_regalo_applicar) {
                                        case '1': //Producto
                                            $cart_rule->description = sprintf('Cupon circulo salud regalo #%d',$key);
                                            $cart_rule->reduction_product = 0;
                                            $cart_rule->product_restriction = $product_restriction;
                                            $cart_rule->gift_product = $prods['id_product']; //prod_regalar = 
                                            $add_desc_name = '_GFT'.$key;

                                            /*
                                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
                                            VALUES ('.(int)$currentObject->id.', '.(int)Tools::getValue('product_rule_group_'.$ruleGroupId.'_quantity').')');
                                            $id_product_rule_group = Db::getInstance()->Insert_ID();
                                            */
                                            break;

                                        case '2': //Descuento
                                            $cart_rule->description = 'Cupon SERVIER '.$porc_dto.' % - .IP '.$prods['id_product'];
                                            $cart_rule->product_restriction = 1;
                                            $cart_rule->reduction_percent = $porc_dto;
                                            $cart_rule->free_shipping = 1;
                                            $add_desc_name = '_POR'.$key;
                                            break;

                                        case '3': //Monto
                                            $cart_rule->description = 'Cupon circulo salud descuento $ '.$mone_dto;
                                            $cart_rule->reduction_amount = $mone_dto;
                                            $add_desc_name = '_MON'.$key;
                                            break;

                                        default:
                                            # code...
                                            break;
                                    }

                                    //////echo "<br> paso3";
                                    $cart_rule->reduction_tax = false;
                                    $cart_rule->minimum_amount_currency = $this->context->cart->id_currency;
                                    $cart_rule->reduction_currency = $this->context->cart->id_currency;

                                    //////echo "<br> paso4";
                                    try {
                                        if ( !$cart_rule->add() ) {
                                            //////echo "<br> paso5";
                                            //////--echo "<br> Cupon NOOOOOOOOOOO creado-";
                                            $this->errors[] = Tools::displayError('You cannot generate a voucher.');
                                        }
                                        else {
                                            //////echo "<br> paso6";



                                            /******************** INICIO PARA ATAR EL DESCUENDO X UN PRODUCTO PARA UN PRODUCTO EN ESPECIFICO ************************/

                                                //////echo "<br> ins 1: ".
                                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
                                                VALUES ('.(int)$cart_rule->id.', 1)');
                                                $id_product_rule_group = Db::getInstance()->Insert_ID();

                                                //////echo "<br> ins 2: ".
                                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
                                                VALUES ('.(int)$id_product_rule_group.', "products")');
                                                $id_product_rule = Db::getInstance()->Insert_ID();

                                                //////echo "<br> ins 3: ".
                                                Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ('.$id_product_rule.', '.$value_psend['id_product'].' ) ');

                                            /******************** FIN PARA ATAR EL DECUENDO X UN PRODUCTO PARA UN PRODUCTO EN ESPECIFICO ************************/



                                            //////echo "<br> Cupon creado id: ".$cart_rule->id;
                                            // Update the voucher code and name
                                            foreach ($languages as $language)
                                                    $cart_rule->name[$language['id_lang']] = sprintf('CIRSAN_C%1$dCI%2$dV%3$d', $customer_id, $this->context->cart->id, $cart_rule->id);
                                            if ($codigo == 1 ) { 
                                                $cart_rule->code =  str_replace(array(' ','$','#','%'), array('','','',''), $cart_rule->description).$add_desc_name; // sprintf('CIRSA_C%1$d_O%2$d', $cart_rule->id, $this->context->customer, $this->context->cart->id);
                                            } else {
                                                $cart_rule->code =  '';
                                            }

                                            if ( $add_carrito ) { 
                                                //////--echo "<br>Adicionando regla a carrito: ".
                                                $this->context->cart->addCartRule($cart_rule->id);
                                                //////--echo "<br> sin adicionar a carrito ";                                            
                                            }
                                            //////--echo "<br> update for : ".
                                            $this->context->cart->update();
                                            //////exit;
                                            if (!$cart_rule->update()) {
                                                $this->errors[] = Tools::displayError('You cannot update a voucher.');
                                            }
                                            else {
                                                //////--echo "<br> Cupon actualizado : ".implode(', ', $cart_rule->name );
                                            }
                                        }
                                    } 
                                    catch (Exception $e) {
                                        print_r($e);
                                    }
                                }
                            }
                            else {
                                //////echo "<br><br><br> no asociado a nadro";
                                //////echo "<br><br><br> NO prod: ". $key ;
                            }
                        }
  
                    print_r($this->errors);
}
?>

