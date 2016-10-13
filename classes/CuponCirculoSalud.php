<?php


class CuponCirculoSalud  {
    public $context = '';

    public function objectToArray($data){
        
        if ( ( !is_array($data) ) and ( !is_object($data) ) ) return 'xxx'; //$data;
        
        $result = array();
        $data = (array) $data;

        foreach ($data as $key => $value) {
            if (is_object($value)) $value = (array) $value;

            if (is_array($value)) 
                $result[$key] = $this->objectToArray($value);
            else
                $result[$key] = $value;
        }
        return $result;
    }

    /*
    jquery-....min.js (línea 8240)
    POST http://192.168.10.83/prod.farmalisto.com.mx/admin...dminCarts&token=19225e33468dc97d7f8f3ca8e79b6f74
    200 OK
                    5,14s	
    jquery-....min.js (línea 8240)
    ParámetrosEncabezadosEnvíoRespuestaHTMLCookies
    Parámetrosapplication/x-www-form-urlencodedDo not sort
    action	addVoucher
    ajax	1
    id_cart	3741
    id_cart_rule	105
    id_customer	20
    tab	AdminCarts
    token	19225e33468dc97d7f8f3ca8e79b6f74
    Fuente
    ajax=1&token=19225e33468dc97d7f8f3ca8e79b6f74&tab=AdminCarts&action=addVoucher&id_cart_rule=105&id_cart=3741&id_customer=20
    */

    public function circuloSalud( $contexto, $tajetacirculo , $add_carrito = false ) {

        $debug = '<br>';
        //ini_set("log_errors", 1);
        //ini_set("error_reporting", E_ALL);
        /*array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";'));
        exit;*/
        //error_log( array_walk(debug_backtrace(),create_function('$a,$b','print "{$a[\'function\']}()(".basename($a[\'file\']).":{$a[\'line\']}); ";')) , 3, "c:/tmp/cirsalog.log");
        // back|
        // circuloSalud()(Cart.php:993); updateQty()(AdminCartsController.php:372); ajaxProcessUpdateQty()(AdminController.php:562); postProcess()(Controller.php:158); run()(Dispatcher.php:348); dispatch()(index.php:53);
        // front
        // 1ra vez agrega
        // circuloSalud()(Cart.php:995); updateQty()(CartController.php:257); processChangeProductInCart()(CartController.php:72); postProcess()(Controller.php:158); run()(Dispatcher.php:348); dispatch()(index.php:26); 
        // cambia cantidad
        // circuloSalud()(Cart.php:995); updateQty()(CartController.php:257); processChangeProductInCart()(CartController.php:72); postProcess()(Controller.php:158); run()(Dispatcher.php:348); dispatch()(index.php:26); 
		
        $this->context = $contexto;
        /**
        * si cliente esta logueado y tiene tarjeta vinculada
        */

        if ( isset( $this->context->customer->id ) && $tajetacirculo != '' && $tajetacirculo != null ) {
            $customer_id = $this->context->customer->id;
            $prods_a_circulo = $this->context->cart->getSummaryDetails()['products'];
            $debug .= "<br> update initial: ".$this->context->cart->update();
            $debug .= "<br> prods enviar :<pre>";
            $debug .= print_r($prods_a_circulo, true);
            $debug .="<br> : prods enviar </pre>";
            $obj_cirsalud = new CirculoSalud();
            $obj_cirsalud->debug_mode = false;
            $flagLoginApego = 0;
            $debug .= '<br>Tengo session en cart y es:  '.$this->context->cart->sessionApego;
            if( isset($this->context->cart->sessionApego) && $this->context->cart->sessionApego && $this->context->cart->sessionApego != Null ){
                
                $obj_cirsalud->session_load = ($this->context->cart->sessionApego);
                $flagLoginApego = 1;
                $debug .= '<br>Tengo session en obj_salud y es:  '.$obj_cirsalud->session_load ;
            }
            elseif($obj_cirsalud->Login()){
                $flagLoginApego = 1;
                $debug .= '<br>Entre al if.';
                $this->context->cart->sessionApego = $obj_cirsalud->session_load;
                $sql = 'UPDATE ps_cart
                        SET ps_cart.sessionApego = "'.$obj_cirsalud->session_load.'"
                        WHERE ps_cart.id_cart = '.$this->context->cart->id.';';
                $debug .= '<br>El sql es: '.$sql;
                DB::getInstance()->execute($sql);            
            }
            else {
                $debug .= '<br><h1>No canciona.</h1>';
            }
            
            if ( $flagLoginApego == 1 ) {
                $debug .= '<br>estoy dentro';
                // para borrar carritos: $cart_rule->delete();
                $rules_prev_created = CartRule::getCartRulesByNameLang( 'CIRSAN_C'.$customer_id.'CI'.$this->context->cart->id, 1 );
                $debug .= '<br>'.print_r($rules_prev_created,true);
                $debug .= "<br> cant reglas anteriores: ".
                $rules_in_cart_org = count($rules_prev_created);

                $debug .= "<pre> reglas return2: ";
                               
                // remover reglas asociadas al carrito				
                foreach ($rules_prev_created as $key => $value) {

                    $debug .= "<br> remover reglas: ".
                    $this->context->cart->removeCartRule($value['id_cart_rule'])."  --  ".$value['id_cart_rule'];
                    $debug .= "<br> update for removed: ".
                    $this->context->cart->update();
                    $debug .= "<br> -- ".$value['id_cart_rule'] ." -- removida : ".
                    $this->context->cart->removeCartRule($value['id_cart_rule']);
                    $newdel = new CartRule($value['id_cart_rule']);
                    $debug .= "<br> objeto cartrule  borrar regla ---- borrada : ".
                    $newdel->delete();
                }
                
                $debug .= "<br>remover reglas: ".
                $this->context->cart->removeCartRules();
                $debug .= "</pre>";
                $debug .= "<br> update for removed: ".
                $this->context->cart->update();
	        $rules_prev_created_customer = CartRule::deleteByIdCustomer(  $customer_id );
                $debug .= "<pre> reglas return rules_prev_created_customer: ";
                $debug .= $rules_prev_created_customer;
                $debug .= "</pre>";
                $flagcart = 0;
        
                foreach ($prods_a_circulo as $key => $value_psend) {
                    $productos = array();
                    $productos[$key]['referencia'] = $value_psend['reference'];
                    $productos[$key]['cantidad'] = $value_psend['cart_quantity'];
                    $debug .= "<hr><pre> productos: ";
                    $debug .= print_r($productos,true);
                    $debug .= "<br></pre> ";
                
                    /*******************************LISTADO EN ARREGLO DE PRODUCTOS ENVIADOS Y DEVUELVE CUAL CON PROMOCION ********************************/

                    $obj_cirsalud->GetBonusProductList($tajetacirculo, $productos);
                    $debug .= "<hr><pre> GetBonusProductList rta: ";
                    $debug .= $obj_cirsalud->_GetBonusProductList;
                    $debug .= "<br></pre><hr> ";
                    $prods_ret = 0;
                    $prods_ret_array = array();
                    $total_prods = array();
                    $debug .= "<hr><pre> total_prods 1: ";
                    $debug .= print_r($total_prods,true);
                    $debug .= "<br></pre><hr> ";

                    /****************  ASOCIAR RESPUESTA WEB SERVICE DE PRODUCTOS A UN ARRAY ****************************/
                    $array_ws_ret  = $this->objectToArray( $obj_cirsalud->_GetBonusProductList ); //->ResponseBonusList
                    $debug .= "<hr><pre> new rta: ";
                    $debug .= print_r($array_ws_ret,true);
                    $debug .= "<br></pre><hr> ";

                    // para validar el primer producto del carrito
                    if ( !isset( $array_ws_ret['ResponseBonusList'][0]) ) {
                            $arr_noid['ResponseBonusList']['0'] = $array_ws_ret['ResponseBonusList'];
                            $array_ws_ret = $arr_noid;
                    }

                    foreach ( $array_ws_ret['ResponseBonusList'] as $key => $value ) {
                        $debug .= "<br> llave: -".$key.'-';

                        if ( $key ==  $prods_ret)  {
                            $debug .= "<br> SI id en respuesta: ".$prods_ret . " - ".$key.' -ref : '.$value['Sku'];
                            $prods_ret_array[ strval($value['Sku']) ][ 'PiezasPorComprar' ] = strval($value['PiezasPorComprar']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'PiezasGratis' ] = strval($value['PiezasGratis']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'PiezasAcumuladas' ] = strval($value['PiezasAcumuladas']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'PiezasPendientes' ] = strval($value['PiezasPendientes']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'PorcentajeDescuento' ] = strval($value['PorcentajeDescuento']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'MontoDescuento' ] = strval($value['MontoDescuento']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'TipoPrecioDescuento' ] = strval($value['TipoPrecioDescuento']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'PrecioFijo' ] = strval($value['PrecioFijo']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'PorcentajePuntos' ] = strval($value['PorcentajePuntos']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'EfectivoxPunto' ] = strval($value['EfectivoxPunto']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'HuboError' ] = strval($value['HuboError']);
                            $prods_ret_array[ strval($value['Sku']) ][ 'MensajeError' ] = ( count($value['MensajeError']) != 0 ? implode(", ", $value['MensajeError']) : '' );
                            $total_prods[ strval($value['Sku']) ]['ws'] = 1;
                        } 
                        else {
                            $debug .= "<br> NO id en respuesta: ".$prods_ret . " - ".$key;
                        }
                        $prods_ret++;
                    }

                    $debug .= "<hr><pre> total_prods 2: ";
                    $debug .= print_r($total_prods,true);
                    $debug .= "<br></pre><hr> ";

                    /****************  ASOCIAR RESPUESTA WEB SERVIDE DE PRODUCTOS A UN ARRAY ****************************/
                    $debug .= "<hr><pre> RESPUESTA PRODS ASOCIADO: ";
                    $debug .= print_r($prods_ret_array,true);
                    $debug .= "<br></pre><hr> ";
                    $debug .= print_r($this->isLogged,true);
                    //$this->context->cart->getProducts()[0]['reduction_applies'] = '1';
                    //$this->context->cart->getProducts()[0]['quantity_discount_applies'] ='10';
                    /*

                    llave: -0-
                    SI id en respuesta: 0 - 0 -ref : 353885008211  x 3 =+ 1		One Touch Select 50 Tiras Reactivas
                    llave: -1-
                    SI id en respuesta: 1 - 1 -ref : 7501098611329   x   2 		Nexium-Mups 40 Mg Tab 14 0
                    llave: -2-
                    SI id en respuesta: 2 - 2 -ref : 7501098611312  --  		Nexium-Mups 40 Mg Tab 7 3
                    llave: -3-
                    SI id en respuesta: 3 - 3 -ref : 5702191008104    x   1 	Fucidin Ungüento 2% Tubo Con 15 G – Antibiótico
                    llave: -4-
                    SI id en respuesta: 4 - 4 -ref : 699073710925    x   1 		Free Style Lite Tiras De Prueba De Glucosa En Sangre
                    */

                    $debug .= "<br> ---------- Productos dos ----------". $customer_id."<hr><pre>";
                    $debug .= print_r($this->context->cart->getProducts(),true);
                    $debug .= "</pre>";

                    $prods_cart = array();
                    /****************  ASOCIAR PRODUCTOS  DE CARRITO A UN ARRAY ****************************/
                    //foreach ( $prods_a_circulo as $key => $value ) {
						
                    $prods_cart[ $value_psend['reference'] ][ 'PiezasPorComprar' ] =  $value_psend['cart_quantity'];
                    $prods_cart[ $value_psend['reference'] ][ 'quantity' ] =  $value_psend['quantity'];
                    $prods_cart[ $value_psend['reference'] ][ 'id_product' ] =  $value_psend['id_product'];
                    $prods_cart[ $value_psend['reference'] ][ 'name' ] =  $value_psend['name'];
                    $prods_cart[ $value_psend['reference'] ][ 'price' ] =  $value_psend['price'];
                    $prods_cart[ $value_psend['reference'] ][ 'rate' ] =  $value_psend['rate'];
                    $prods_cart[ $value_psend['reference'] ][ 'price_wt' ] =  $value_psend['price_wt'];
                    $prods_cart[ $value_psend['reference'] ][ 'total' ] =  $value_psend['total'];
                    $prods_cart[ $value_psend['reference'] ][ 'total_wt' ] =  $value_psend['total_wt'];
                    $total_prods[ $value_psend['reference'] ]['cart'] = 1;

                    //}

                    $debug .= "<hr><pre> total_prods 3: ";
                    $debug .= print_r($total_prods,true);
                    $debug .= "<br></pre><hr> ";
			
                    $debug .= "<hr><pre>  PRODS CARRITO: ";
                    $debug .= print_r($prods_cart,true);
                    $debug .= "<br></pre><hr> ";
                    
                    foreach ( $total_prods as $key => $value ) {
                        $dto_regalo_applicar = 0;
                        $prod_regalar = 0;
                        $porc_dto = 0;
                        $mone_dto = 0;
                        $product_restriction = 0;
                        $prods = Product::searchByReference( $key, 'nadro', 1);
                        $debug .= "<br> REF: ". $key ;
                        // si el producto está asociado al proveedor nadro, está activo y 

                        if ( isset( $prods['id_product'] ) && $prods['id_product'] != '' && $prods['id_product'] != 0 ) {
                            $debug .= "<br> si prod: ". $key ;
                            if ( isset( $total_prods[ $key ]['cart'] ) && $total_prods[ $key ]['cart'] == 1 && isset( $total_prods[ $key ]['ws'] ) && $total_prods[ $key ]['ws'] == 1 ) {
                                $debug .= "<br> ** REf: ".$key." - - ".$prods_cart[ $key ]['name']." Iguales ";
                                $debug .= "<br> -- Compradas cart = ".$prods_cart[ $key ]['PiezasPorComprar'];
                                $debug .= "<br> -- Compradas webs = ".$prods_ret_array[ $key ]['PiezasPorComprar'];
                                if (  $prods_ret_array[ $key ]['PiezasGratis'] != 0 ) {
                                    $debug .= "<br> -- gratis == ".$prods_ret_array[ $key ]['PiezasGratis'];
                                    $prod_regalar = $prods['id_product'];
                                    $dto_regalo_applicar = 1;
                                    $product_restriction = 1;
                                }
                                if (  $prods_ret_array[ $key ]['PorcentajeDescuento'] != 0 ) {
                                    $debug .= "<br> -- PorcentajeDescuento = ".$prods_ret_array[ $key ]['PorcentajeDescuento'];
                                    $porc_dto = $prods_ret_array[ $key ]['PorcentajeDescuento'];
                                    $dto_regalo_applicar = 2;
                                }
                                elseif (  $prods_ret_array[ $key ]['MontoDescuento'] != 0 ) {
                                    $debug .= "<br> -- MontoDescuento = ".$prods_ret_array[ $key ]['MontoDescuento'];
                                    //$dto_monto_acum += $prods_ret_array[ $key ]['MontoDescuento'];
                                    $mone_dto = $prods_ret_array[ $key ]['MontoDescuento'];
                                    $dto_regalo_applicar = 3;
                                }
                            }
                            elseif ( !isset( $total_prods[ $key ]['cart'] )  && isset( $total_prods[ $key ]['ws'] ) && $total_prods[ $key ]['ws'] == 1   ) {
                                $debug .= "<hr> ** NO EN CARRITO ------------ ";
                                $debug .= "<br> ** REf: ".$key." - ";
                                $debug .= "<br> ".$prods_cart[ $key ]['name']." Diferente ";
                                $debug .= "<br> -- Compradas cart = ".$prods_cart[ $key ]['PiezasPorComprar'];
                                $debug .= "<br> -- Compradas webs = ".$prods_ret_array[ $key ]['PiezasPorComprar'];

                                $found = '';
                                if ( array_key_exists($key, $prods_cart) && array_key_exists($key, $prods_ret_array) ){
                                    $found = 2;
                                    $debug .= "<br> en ambos";
                                }
                                elseif (array_key_exists($key, $prods_cart)){
                                    $found = 1;
                                    $debug .= "<br> en cart";
                                }
                                elseif (array_key_exists($key, $prods_ret_array)){
                                    $found = 0;
                                    $debug .= "<br> en wsre";
                                }

                                /******************************* INICIO SI CANTIDAD COMPRADA DIFERENTE A RETORNADA **********************************************************
                                if ( $prods_ret_array[ $key ]['PiezasPorComprar'] > $prods_cart[ $key ]['PiezasPorComprar'] ) {
                                    $this->context->cart->updateQty( ( $prods_ret_array[ $key ]['PiezasPorComprar'] - $prods_cart[ $key ]['PiezasPorComprar'] ) , $prods_cart[ $key ]['id_product'],0,0,'up',0);
                                }
                                elseif ( $prods_ret_array[ $key ]['PiezasPorComprar'] < $prods_cart[ $key ]['PiezasPorComprar'] ) {
                                    $this->context->cart->updateQty( ( $prods_cart[ $key ]['PiezasPorComprar'] - $prods_ret_array[ $key ]['PiezasPorComprar'] ) , $prods_cart[ $key ]['id_product'],0,0,'down',0);
                                }
                                /******************************* FIN SI CANTIDAD COMPRADA DIFERENTE A RETORNADA ************************************************************/

                                if (  $prods_ret_array[ $key ]['PiezasGratis'] != 0 ) {
                                    $debug .= "<br> -- gratis != ".$prods_ret_array[ $key ]['PiezasGratis'];
                                    $prod_regalar = $prods['id_product'];
                                    $dto_regalo_applicar = 1;
                                    $product_restriction = 1;

                                    $prods = Product::searchByReference( $key, 'nadro', 1);
                                    $debug .= "<br>pr: <pre>";
                                    $debug .= $prods;
                                    $debug .= "</pre>";
                                }
                                if (  $prods_ret_array[ $key ]['PorcentajeDescuento'] != 0 ) {
                                    $debug .= "<br> IO Error -- PorcentajeDescuento = ".$prods_ret_array[ $key ]['PorcentajeDescuento'];
                                    $porc_dto = $prods_ret_array[ $key ]['PorcentajeDescuento'];
                                }
                                elseif (  $prods_ret_array[ $key ]['MontoDescuento'] != 0 ) {
                                    $debug .= "<br> IO Error -- MontoDescuento = ".$prods_ret_array[ $key ]['MontoDescuento'];
                                    //$mone_dto = $prods_ret_array[ $key ]['MontoDescuento'];
                                    //$dto_monto_acum += $prods_ret_array[ $key ]['MontoDescuento'];
                                }
                                //$this->context->cart->updateQty($value,$key,0,0,'up',0);
                                //updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', $id_address_delivery = 0, Shop $shop = null, $auto_add_cart_rule = true)
                            }

                            /*elseif ( isset( $total_prods[ $key ]['cart'] ) && $total_prods[ $key ]['cart'] == 1 &&  !isset( $total_prods[ $key ]['ws'] )  ) {
                            }*/
                            /***************** CREACION DE CUPONES ********************/
                            $debug .= "<pre> this object : ";
                            $debug .= print_r($this->context,true);
                            $debug .= "</pre>";
                            $debug .= "<br> dto: ".$dto_regalo_applicar;
                            $debug .= "<br> id_cliente: ".$customer_id;
                            $debug .= "<br> id_cart: ".$this->context->cart->id;
                            
                            if ( $dto_regalo_applicar != 0 && isset( $customer_id )  && $flagcart == 0 ) {
                                $debug .= "<br><hr> aplicar dto --".$this->context->cart->id;						
                                /*					
                                $cart_rule = new CartRule(25);
                                $cart_rule->reduction_amount = 88;
                                if (!$cart_rule->update())
                                                $debug .= "<br> Cupon NOOOOOO actualizado-".$cart_rule->name[1];
                                        else
                                        {
                                                $debug .= "<br> Cupon actualizado-".$cart_rule->name[1];
                                        }

                                        $cart_rule = new CartRule(25);
                                $debug .= "<hr><pre>  REGLAS CARRITO: ";
                                $debug .= print_r( $cart_rule );
                                $debug .= "<br></pre><hr> ";
                                */
                                $cart_rule = new CartRule();							
                                $languages = Language::getLanguages(false);
                                $debug .= "<pre> orden: ";
                                $debug .= print_r($this, true);
                                
                                foreach ($languages as $language)
                                    // Define a temporary name
                                    $cart_rule->name[$language['id_lang']] = sprintf('CIRSAN_CI%d', $this->context->cart->id);

                                // Define a temporary code
                                //$cart_rule->code = ''; //sprintf('CIRSA_C%1$d_O%2$d', $customer_id, $this->context->cart->id);
                                $cart_rule->quantity = 1;
                                $cart_rule->quantity_per_user = 1;
                                $debug .= "<br> paso1";

                                // Specific to the customer
                                $cart_rule->id_customer = $customer_id;
                                $now = time();
                                $cart_rule->date_from = date('Y-m-d H:i:s', $now - (3600) );
                                $cart_rule->date_to = date('Y-m-d H:i:s', $now + (3600 * 3 * 1)); // 3600 * 24 * 1: 3 hours  -   3600 * 24 * 1: 1 Day -  3600 * 24 * 365.25: 1 year  
                                $cart_rule->partial_use = 0;
                                $cart_rule->active = 1;
                                $cart_rule->reduction_product = $prods['id_product'];
                                $debug .= "<br> paso2";
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
                                        $cart_rule->description = 'Cupon circulo salud descuento '.$porc_dto.' %';
                                        $cart_rule->reduction_percent = $porc_dto;
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
                                
                                $debug .= "<br> paso3";
                                $cart_rule->reduction_tax = false;
                                $cart_rule->minimum_amount_currency = $this->context->cart->id_currency;
                                $cart_rule->reduction_currency = $this->context->cart->id_currency;

                                $debug .= "<br> paso4";
                                try {
                                    if ( !$cart_rule->add() ) {
                                        $debug .= "<br> paso5";
                                        $debug .= "<br> Cupon NOOOOOOOOOOO creado-";
                                        $this->errors[] = Tools::displayError('You cannot generate a voucher.');
                                    }
                                    else {
                                        $debug .= "<br> paso6";
                                        if( $dto_regalo_applicar == 1 ) {
                                            $debug .= "<br> ins 1: ".
                                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
                                            VALUES ('.(int)$cart_rule->id.', 1)');
                                            $id_product_rule_group = Db::getInstance()->Insert_ID();
								
                                            $debug .= "<br> ins 2: ".
                                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
                                            VALUES ('.(int)$id_product_rule_group.', "products")');
                                            $id_product_rule = Db::getInstance()->Insert_ID();

                                            $debug .= "<br> ins 3: ".
                                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ('.$id_product_rule.', '.$value_psend['id_product'].' ) ');
                                        }

                                        $debug .= "<br> Cupon creado id: ".$cart_rule->id;
                                        // Update the voucher code and name
                                        foreach ($languages as $language)
                                            $cart_rule->name[$language['id_lang']] = sprintf('CIRSAN_C%1$dCI%2$dV%3$d', $customer_id, $this->context->cart->id, $cart_rule->id);
                                        $cart_rule->code =  str_replace(array(' ','$','#','%'), array('','','',''), $cart_rule->description).$add_desc_name; // sprintf('CIRSA_C%1$d_O%2$d', $cart_rule->id, $this->context->customer, $this->context->cart->id);
                                        
                                        if ( $add_carrito ) { 
                                            $debug .= "<br>Adicionando regla a carrito: ".
                                            $this->context->cart->addCartRule($cart_rule->id);
                                            $debug .= "<br> sin adicionar a carrito ";
                                            $flagcart = 1;
                                        }
                                        $debug .= "<br> update for : ".
                                        $this->context->cart->update();
                                        if (!$cart_rule->update()) {
                                            $this->errors[] = Tools::displayError('You cannot generate a voucher.');
                                        }
                                        else {
                                            $debug .= "<br> Cupon actualizado : ".implode(', ', $cart_rule->name );
                                        }
                                    }
                                } 
                                catch (Exception $e) {
                                    print_r($e);
                                }
                            }
                        }
                        else {
                            $debug .= "<br><br><br> no asociado a nadro";
                            $debug .= "<br><br><br> NO prod: ". $key ;
                        }
                    }

                    //echo "<br>-.-.-.-.-.-.-->>>>  Adicionando regla a carrito: ".$this->context->cart->addCartRule(105);
                    /*
                    echo "<br> Antes Sleep".date("H:i:s");

                    echo "<br> update fin : ".$this->context->cart->update();

                    $rules_in_cart = $this->context->cart->getCartRules();
                    $repetir = 0; 
                    echo "<br> cant_rules: ".count( $rules_in_cart )." - Repetir: ".$repetir;

                    while ( count( $rules_in_cart ) ==  $rules_in_cart_org && $repetir < 3 ) {
                        sleep(1);
                        echo "<br> Despues Sleep".date("H:i:s");
                        $repetir ++;
                        $rules_in_cart = $this->context->cart->getCartRules();
                        echo "<br> innn   - cant_rules: ".count( $rules_in_cart )." - Repetir: ".$repetir;
                    }

                    $rules_in_cart = $this->context->cart->getCartRules();

                    echo "<BR><BR>..................................<BR><pre> REGLAS EN CARRITO : ";
                    foreach ($rules_in_cart as $key => $value) {
                            echo "<br> cart: ".$value['id_cart']." - id_Rule: ".$value['id_cart_rule'];
                    }
                    echo "</pre>";
                    echo("<BR>");
                    */
                }
            }
            else{
                $debug .= "<br> No logueado";
            }
        }
        if( Configuration::get('PS_DEBUG_CIRCULO_DE_LA_SALUD') == 1 && $this->context->customer->id == 8910 ){
//            echo "<br><h1><b>Usted esta en modo Debug de Circulo de salud</b></h1><br><br>".$debug;
        }
    }
}



?>