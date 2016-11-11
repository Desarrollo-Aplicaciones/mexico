<?php

/**
 * Description of ProgressiveDiscountsCore
 *
 * @author Jeisson Gomez
 */

class ProgressivediscountsCore
{
        // Objetos para multiples productos
    
        // Almacena todos los ids de productos
        public $IdProducts;
        
        //  informacion del descuento progresivo a aplicar de todos los productos
        public $currentProgressiveDiscounts;
        
        // id del descuento progresivo a aplicar de todos los productos respectivamente.
        public $idProgressiveDiscounts;
        
        
        
        
        
        
        
        // id del cliente al que se le aplicara el descuento progresivo
	public $idCustomer;

	// id del producto en el carrito al que se aplicara descuento progresivo
	public $idProduct;

	// id del descuento progresivo a aplicar
	public $idProgressiveDiscount;

	// informacion del ultimo descuento aplicado de acuerdo al historico
	public $idOrder;

	// id del cupon a aplicar
	public $idCartRule;

	// llave del cupon a aplicar
	public $KeyCartRule;

	// informacion del ultimo descuento aplicado de acuerdo al historico
	public $beforeProgressiveDiscount;

	// informacion del descuento progresivo a aplicar
	public $currentProgressiveDiscount;
	
	// flag para indentificar si la orden es orden inicial
	public $initialShopping;
	
	// contador de ordenes por perido
	public $counterOrdersPeriod;
	
	// contador de periodos
	public $counterPeriod;
	
	// contador del reinicio
	public $counterReset;

	// contador del ciclos
	public $counterCycles;

	// objeto carrito
	public $ObjectCart;

	// fecha actual
	public $currentDate;

	// tipo de datos a insertar en el descuento progresivo
	public $data;

	// fecha final del proximo periodo del descuento progresivo
	public $dateEndNextPeriod;

	// fecha final del descuento progresivo
	public $dateEndProgressiveDiscount;
        
        
        
        public function getProductsFromCartWithProgressiveDiscount( $Cart = null ){
            
		// cargar objeto con las caracteristicas del carrito
		if ( !empty($Cart) && !is_null($Cart) ) {
                    $this->ObjectCart = $Cart;
		} else {
                    $this->ObjectCart = Context::getContext()->cart;
		}
                
                // valida si se encuentra un usuario logeado en el sistema
		$isLogged = $this->validateLoggedUser();
                
                // valida si se encuentra como minimo un producto con descuento progresivo en el carrito
		$productIn = $this->validateProductInProgressiveDiscount();
                
                if ( $isLogged && $productIn ) {
                    $result = true;
                    foreach ( $this->idProducts as $key => $value ) {
                        $this->idProduct = $this->idProducts[$key];
                        $this->currentProgressiveDiscount = $this->currentProgressiveDiscounts[$key];
                        $this->idProgressiveDiscount = $this->idProgressiveDiscounts[$key];
                        $result = $this->addProgressiveDiscount() ? $result : false;
                    }
//                    error_log("\n\n\n\n\n\n\n\n\n\n\n\n\n\n en get order total: ".$result."\n\n\n\n\n\n\n\n\n\n" , 3, "/tmp/progresivo.log");
                    return $result;
                }
                else {
                    return false;
                }
                
        }
        
        /**
	 * [addProgressiveDiscount Funcion con la que se aplicara el descuento progresivo]
	 */
	public function addProgressiveDiscount() {
//		error_log(print_r(debug_backtrace(2), TRUE), 3, "/tmp/progresivo.log");
//		error_log("\n\n\n\n\n\n\n\n\n\n\n\t   Este es el carruito que entra a add de dcts prog:  ".print_r($Cart, TRUE)."\n\n\n\n\n\n\n\n\n\n\n", 3, "/tmp/progresivo.log");
//              echo '<pre>';
//              var_dump(Context::getContext()->cart);
//              exit(0);
		// tomar la fecha actual
		$date = getdate();
		$this->currentDate = $date[0];
		// $this->currentDate = strtotime( '2015-08-13 21:28:53' );

//                    echo '<pre><br>aqsdfasdkasgdklhasjdgaskldjhasjdgh';
//                    var_dump($this->idProduct);
//                    echo '<pre><br>';
//                    var_dump($this->currentProgressiveDiscounts);
//                    echo '<pre><br>';
//                    var_dump($this->idProgressiveDiscounts);
                
//                if ( $isLogged && $productIn ) {
                    	
                        // valida si existe historial del descuento progresivo a aplicar
			$inHistoryProgressiveDiscount = $this->validateHistoryProgressiveDiscount();
			if ( $inHistoryProgressiveDiscount ) {

                            // si la orden anterior no se encuentra en el estado valido para aplicar el descuento progresivo, no aplicara ningun descuento ni registrara en historial de descuento progresivo
                            $states_orders = explode(",", $this->currentProgressiveDiscount['states_orders']);
//                                    error_log("\n\n\tcurrentProgressiveDiscount: ".print_r($this->currentProgressiveDiscount,true),3,"/tmp/progresivo.log");
//                                    error_log("\n\n\tcurrentProgressiveDiscount: ".print_r($states_orders,true),3,"/tmp/progresivo.log");
                            if ( !in_array($this->beforeProgressiveDiscount['current_state'], $states_orders) ) {
//                                    error_log("\n\n\t\tPaila 1\n\n".print_r($this->beforeProgressiveDiscount['current_state'],true),3,"/tmp/progresivo.log");
                                    return false;
                            }
//                                    error_log("\n\n\t\tContinuo...\n\n",3,"/tmp/progresivo.log");


                            // valida si el contador de ciclos es mayor a la cantidd de cilos del descuento progresivo
                            $validateCounterCycles = $this->validateCounterCyclesProgressiveDiscount();
                            if ( !$validateCounterCycles ) {
//                                    error_log("\n\n\t\tPaila 2\n\n",3,"/tmp/progresivo.log");
                                    return false;
                            }
                            
                            
                            
                            
                            
                            
                            
                            // valida si la fecha actual es mayor a la fecha final del descuento progresivo
                            $validateRangeDateFinish = $this->validateRangeDateFinishProgressiveDiscount();

                            // valida si el contador de reinicio es mayor al reinicio del descuento progresivo
                            $validateCounterReset = $this->validateCounterResetProgressiveDiscount();

//                                        error_log("\n\n\t\t DEBEN ESTAR EN TRUE AMBAS    ".print_r($validateRangeDateFinish)." - ".print_r($validateCounterReset),3,"/tmp/progresivo.log");
//                                        echo '<pre>';
//                                        var_dump($validateRangeDateFinish);
//                                        echo '<br>';
//                                        var_dump($validateCounterReset);
//                                        
                            if ( $validateRangeDateFinish || $validateCounterReset ) {
                                    $this->addHistoryProgressiveDiscount();
                                    return true;
                            } 

                            // valida si la fecha actual es mayor a la fecha final del periodo vigente
                            $validateRangeDateFinishPeriod = $this->validateRangeDateFinishPeriod();
//                            var_dump($validateRangeDateFinishPeriod);
//                             error_log("\n\n\t\tContinuo...\n\n",3,"/tmp/progresivo.log");
                            if ( $validateRangeDateFinishPeriod ) {
                                // valida si el contador de periodos es mayor al numero de periodos del descuento progresivo
                                $validateCounterPeriod = $this->validateCounterPeriodProgressiveDiscount();
                                if ( $validateCounterPeriod ) {
                                    $this->addHistoryProgressiveDiscount();
                                    return true;
                                } else {

                                    // valida si la fecha actual no sobrepasa la fecha final del proximo periodo
                                    $validateEndDateNextPeriod = $this->validateEndDateNextPeriod();
                                    if ( $validateEndDateNextPeriod ) {
                                        $this->addHistoryProgressiveDiscount();
                                        return true;
                                    }
                                    else {

                                        // toma y aplica el cupon a aplicar del listado de descuento progresivo
                                        $this->addCartRuleFromCart();
                                        $this->data = "2";
                                        $this->dataHistoryProgressiveDiscount();
                                        $this->addHistoryProgressiveDiscount();
                                        return true;
                                    }
                                }
                            } 
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            
                            else {
                                 
                                // valida si la ultima compra con descuento progresivo es una compra inicial
                                if ( $this->beforeProgressiveDiscount['initial_shopping'] == 1 ) {
//                                    error_log("\n\n\t\tContinuo...\n\n",3,"/tmp/progresivo.log");
                                    $this->addCartRuleFromCart();
                                    $this->data = "3";
                                    $this->dataHistoryProgressiveDiscount();
                                    $this->addHistoryProgressiveDiscount();
                                    return true;
                                } else {
error_log("\n\n\t\tContinuo...\n\n",3,"/tmp/progresivo.log");
                                    // valida si el contador de ordenes por periodo es mayor al numero de limite de compras por cliente
                                    $validateCounterOrdersPeriod = $this->validateCounterOrdersPeriodProgressiveDiscount();
                                    if ( $validateCounterOrdersPeriod ) {
                                        $this->addHistoryProgressiveDiscount();
                                        return true;
                                    } else {
                                        $this->addCartRuleFromCart();
                                        $this->data = "5";
                                        $this->dataHistoryProgressiveDiscount();
                                        $this->addHistoryProgressiveDiscount();
                                        return true;
                                    }
                                }
                            }
			}
                        else {
                            $this->addHistoryProgressiveDiscount();
                            return true;
                        }
//		} else {
//			return false;
//		}
	}


	/**
	 * [validateLoggedUser Funcion para validar si se encuentra un cliente logeado en el sistema]
	 */
	public function validateLoggedUser() {

		$queryCustomer = "SELECT COUNT(*) as validCustomer
                                    FROM "._DB_PREFIX_."customer
                                    WHERE id_default_group = 3
                                    AND id_customer = ".(int)$this->ObjectCart->id_customer;


		$resultsCustomer = Db::getInstance()->ExecuteS($queryCustomer);

		if ( $this->ObjectCart->id_customer != "" && $this->ObjectCart->id_customer != 0 && $resultsCustomer[0]['validCustomer'] != 0 /*&& (int)$this->ObjectCart->id_customer == 2691*/ ) {
			$this->idCustomer = $this->ObjectCart->id_customer;
			return true;
		} else {
			return false;
		}
	}


	/**
	 * [validateProductInProgressiveDiscount Funcion para validar si se encuentra un producto asociado a un descuento progresivo]
	 */
	public function validateProductInProgressiveDiscount() {

		$querySearchProduct = "
			SELECT cp.id_product, pd.*
			FROM "._DB_PREFIX_."cart_product cp
			INNER JOIN "._DB_PREFIX_."product_shop ps
			ON ( cp.id_product = ps.id_product )
			LEFT JOIN "._DB_PREFIX_."tax t
			ON ( ps.id_tax_rules_group = t.id_tax )
			INNER JOIN "._DB_PREFIX_."product_progressive_discounts ppd
			ON ( cp.id_product = ppd.id_product )
			INNER JOIN "._DB_PREFIX_."progressive_discounts pd
			ON ( ppd.id_progressive_discount = pd.id_progressive_discount )
			WHERE cp.id_cart = ".(int)$this->ObjectCart->id."
			AND pd.active = 1
			ORDER BY IF(ps.id_tax_rules_group=0, ps.price, ps.price + ( ( ps.price * t.rate ) / 100 )) DESC";
                //::FABER:: Para varios productos del carrito.
		if ( $resultsSearchProduct = Db::getInstance()->ExecuteS($querySearchProduct) ) {
                    foreach ($resultsSearchProduct as $product) {
                        $this->idProducts[] = $product['id_product'];
                        $this->currentProgressiveDiscounts[] = $product;
                        $this->idProgressiveDiscounts[] = $product['id_progressive_discount'];
                    }

                    return true;
		}
                
                
//		if ( $resultsSearchProduct = Db::getInstance()->ExecuteS($querySearchProduct) ) {
//			$this->idProduct = $resultsSearchProduct[0]['id_product'];
//			$this->currentProgressiveDiscount = $resultsSearchProduct[0];
//			$this->idProgressiveDiscount = $resultsSearchProduct[0]['id_progressive_discount'];
//			return true;
//		}

		return false;
	}


	/**
	 * [validateHistoryProgressiveDiscount Funcion para validar si existe un historial del descuento progresivo a aplicar]
	 */
	public function validateHistoryProgressiveDiscount() {
//            echo '<pre><br>'.$this->idProduct."<br>".$this->idProgressiveDiscount;
                $querySearchHistory = " SELECT ohpd.*, o.current_state
                                        FROM "._DB_PREFIX_."order_history_progressive_discounts ohpd
                                        INNER JOIN "._DB_PREFIX_."orders o ON ( ohpd.id_order = o.id_order )
                                        WHERE ohpd.id_customer = ".$this->idCustomer."
                                        AND ohpd.id_product = ".$this->idProduct."
                                        AND ohpd.id_progressive_discount = ".$this->idProgressiveDiscount."
                                        ORDER BY ohpd.date_order DESC , ohpd.id_order_history_progressive_discount DESC";
                
//                error_log("\n\n\n\n\n\n\n\n SQL consulta History progresivos:".$querySearchHistory." \n\n\n\n\n\n\n\n",3, "/tmp/progresivo.log");
//                $resultsSearchHistory = Db::getInstance()->ExecuteS($querySearchHistory);
//                error_log("\n\n\n\n\n\n\n\n respuesta sql resultsSearchHistory:".print_r($resultsSearchHistory, true)." \n\n\n\n\n\n\n\n",3, "/tmp/progresivo.log");
		if ( $resultsSearchHistory = Db::getInstance()->ExecuteS($querySearchHistory) ) {
                    $this->beforeProgressiveDiscount = $resultsSearchHistory[0];
//                    error_log("\n\n\n\n\n\n\n\n respuesta sql this->beforeProgressiveDiscount:".print_r($this->beforeProgressiveDiscount, true)." \n\n\n\n\n\n\n\n",3, "/tmp/progresivo.log");
                    return true;
		} else {
			$this->data = "1";
			$this->dataHistoryProgressiveDiscount();
			return false;
		}
	}


	/**
	 * [addCartRuleFromCart Funcion para agregar el cupon a la orden ]
	 */
	public function addCartRuleFromCart() {
//		error_log("\r\n addCartRuleFromCart", 3, "/tmp/progresivo.log");
//                error_log("\n\n\n\n\n\nEste es this->idProgressiveDiscount\n\n\t".print_r($this->idProgressiveDiscount,true)."\n\n\n\n\n\n\n\n\n\n", 3, "/tmp/progresivo.log");
//                error_log("\n\n\n\n\n\nEste es this->beforeProgressiveDiscount\n\n\t".print_r($this->beforeProgressiveDiscount,true)."\n\n\n\n\n\n\n\n\n\n", 3, "/tmp/progresivo.log");
                
//		if ( empty($this->idCartRule) ) {
//                error_log("\n\n\n\n\n\n\n\n\n\n\n\n\n\n idxcvxcvxcvxcvxcvnt: ".$this->idProgressiveDiscount."\n\n\n\n\n\n\n\n\n\n" , 3, "/tmp/progresivo.log");
//			error_log(" - - 1 addCartRuleFromCart empty(this->idCartRule) ", 3, "/tmp/progresivo.log");
                    // si el anterior cupon agregado es 0 (compra inicial) toma el primer cupon de la escala, si no es 0, toma el cupon agregado y calcula el siguiente a agregar
                    $queryCartRuleAdd = "SELECT
                                            crpd1.id_cart_rule_progressive_discount AS keycartrule,
                                            crpd1.id_cart_rule AS idcartrule,
                                            crpd2.id_cart_rule_progressive_discount AS Nextkeycartrule,
                                            crpd2.id_cart_rule AS Nextidcartrule
                                        FROM "._DB_PREFIX_."cart_rule_progressive_discounts crpd1
                                        LEFT JOIN "._DB_PREFIX_."cart_rule_progressive_discounts crpd2
                                        ON ( crpd2.priority = crpd1.priority+1 )
                                        WHERE crpd1.id_progressive_discount = ".$this->idProgressiveDiscount;
                    
//                    error_log(" Query de addCartRuleFromCart ".$queryCartRuleAdd, 3, "/tmp/progresivo.log");

                    if ( $this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] == 0 ) {
//				error_log(" - - 2 beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] == 0 ", 3, "/tmp/progresivo.log");
                            $queryCartRuleAdd .= " AND crpd1.priority = 1";
                    } else {
//				error_log(" - - 3 beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] != 0 ", 3, "/tmp/progresivo.log");
                            // para tomar el siguiente cupon de la escala del descuento progresivo
                            $queryCartRuleAdd .= " AND crpd1.id_cart_rule_progressive_discount = ".$this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'];
                    }
//                    error_log("\n\n\n\n\n\nEste es el query loco de addcartrulefromcart: \n\n\t".$queryCartRuleAdd."\n\n\n\n\n\n\n\n\n\n", 3, "/tmp/progresivo.log");

                    $resultsCartRuleAdd = Db::getInstance()->ExecuteS($queryCartRuleAdd);
//                    error_log("\n\n\n\n\n\n resultsCartRuleAdd: \n\n\t".print_r($resultsCartRuleAdd, true)."\n\n\n\n\n\n\n\n\n\n", 3, "/tmp/progresivo.log");

                    $resultsCartRuleAdd = $resultsCartRuleAdd[0];
                    
//                    error_log("\n\n\n\n\n\nEste es resultsCartRuleAdd\n\n\t".print_r($resultsCartRuleAdd,true)."\n\n\n\n\n\n\n\n\n\n", 3, "/tmp/progresivo.log");
//                    error_log("\n\n\n Este es this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount']\n\n\t".print_r($this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'],true)."\n", 3, "/tmp/progresivo.log");
//                    error_log("\n\n\n Este es resultsCartRuleAdd['Nextkeycartrule']\n\n\t".print_r($resultsCartRuleAdd['Nextkeycartrule'],true)."\n", 3, "/tmp/progresivo.log");
//                    error_log("\n\n\n Este es resultsCartRuleAdd['Nextidcartrule']\n\n\t".print_r($resultsCartRuleAdd['Nextidcartrule'],true)."\n", 3, "/tmp/progresivo.log");

                    if ( ($resultsCartRuleAdd['Nextkeycartrule'] == "" && $resultsCartRuleAdd['Nextidcartrule'] == "") || $this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] == 0 ) {
//				error_log(" - - 4 Nextkeycartrule beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] == 0 ", 3, "/tmp/progresivo.log");
                            $this->KeyCartRule = $resultsCartRuleAdd['keycartrule'];
                            $this->idCartRule = $resultsCartRuleAdd['idcartrule'];
                            
                    } else {
//				error_log(" - - 5 Nextkeycartrule beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] != 0 ", 3, "/tmp/progresivo.log");
                            $this->KeyCartRule = $resultsCartRuleAdd['Nextkeycartrule'];
                            $this->idCartRule = $resultsCartRuleAdd['Nextidcartrule'];
                    }
//                    error_log("\n\n\n\n idCartRule: ".$this->idCartRule,3,"/tmp/progresivo.log");
//                    error_log("\n\n\n\n 379 - this->idCartRule \n\n\n\n\n".$this->idCartRule , 3, "/tmp/progresivo.log");
                       
                    $queryValidateExistCouponInCart = "SELECT COUNT(*) AS QuantityCartRules
                                                        FROM "._DB_PREFIX_."cart_cart_rule cr
                                                        INNER JOIN "._DB_PREFIX_."cart_rule_progressive_discounts crpd
                                                        ON cr.id_cart_rule = crpd.id_cart_rule
                                                        INNER JOIN "._DB_PREFIX_."progressive_discounts pd
                                                        ON crpd.id_progressive_discount = pd.id_progressive_discount
                                                        WHERE cr.id_cart = ".(int)$this->ObjectCart->id."
                                                        AND pd.id_progressive_discount = ".$this->idProgressiveDiscount;
                    
//                    error_log("\n\n\n\n\t\t\tthis->idProgressiveDiscount\n\t\t\t".print_r($this->idProgressiveDiscount,true), 3, "/tmp/progresivo.log");
//                    error_log("\n\n\n\n\t\t\tqueryValidateExistCouponInCart\n\t\t\t".print_r($queryValidateExistCouponInCart,true), 3, "/tmp/progresivo.log");

                    $resultsValidateExistCouponInCart = Db::getInstance()->ExecuteS($queryValidateExistCouponInCart);
//                    error_log("\n\n\n\n\t\t\tresultsValidateExistCouponInCart\n\t\t\t".print_r($resultsValidateExistCouponInCart,true), 3, "/tmp/progresivo.log");

                    $resultsValidateExistCouponInCart = $resultsValidateExistCouponInCart[0];
//                    error_log("\n\n\n\n\t\t\tresultsValidateExistCouponInCart\n\t\t\t".print_r($resultsValidateExistCouponInCart,true), 3, "/tmp/progresivo.log");
                    $this->removeCartRuleFromCart();

                    if ( $resultsValidateExistCouponInCart['QuantityCartRules'] <= 0 ) {
//                	error_log(" - - 6 resultsValidateExistCouponInCart['QuantityCartRules'] <= 0 ", 3, "/tmp/progresivo.log");
                        // remover relaciones antiguas carrito<->cupones<->descuento progresivo
                        Db::getInstance()->execute('
                            DELETE FROM '._DB_PREFIX_.'cart_cartrule_progressive_discounts
                            WHERE id_cart = '.(int)$this->ObjectCart->id);


                        $queryInsertCartCartRulePD = "INSERT INTO "._DB_PREFIX_."cart_cartrule_progressive_discounts (
                                                            id_cart,
                                                            id_cart_rule,
                                                            id_progressive_discount
                                                        ) VALUES (
                                                            ".(int)$this->ObjectCart->id.",
                                                            ".$this->idCartRule.",
                                                            ".$this->idProgressiveDiscount."
                                                        )";
                        
//                        error_log("\n\n\n\n\n\n\nInserción de cart_cartrule_progressive_discounts:".$queryInsertCartCartRulePD, 3, "/tmp/progresivo.log");
                        
                        $resultsInsertCartCartRulePD = Db::getInstance()->ExecuteS($queryInsertCartCartRulePD);
//                        error_log("\n\n\n\ resultsInsertCartCartRulePD:".$resultsInsertCartCartRulePD, 3, "/tmp/progresivo.log");
				
                        // valida si el cupon a agregar tiene restriccion a producto especifico
                        // En caso de contar con la restriccion, se agregara el cupon al carrito solo si el producto del descuento progresivo es el mismo de la restriccion del cupon
                        $queryCouponReductionProduct ="SELECT reduction_product
                                                                                        FROM "._DB_PREFIX_."cart_rule
                                                                                        WHERE id_cart_rule = ".$this->idCartRule;
//                                error_log(" - - 7 ".$queryCouponReductionProduct, 3, "/tmp/progresivo.log");
                        $resultsCouponReductionProduct = Db::getInstance()->ExecuteS($queryCouponReductionProduct);

                        if ( $resultsCouponReductionProduct[0]['reduction_product'] == 0 || ($resultsCouponReductionProduct[0]['reduction_product'] == $this->idProduct) )
                        {
//                                        error_log(" - - 8 addCartRuleFromCart if add cupon".$resultsCouponReductionProduct[0]['reduction_product'].' - '.$this->idProduct, 3, "/tmp/progresivo.log");
                                $this->ObjectCart->addCartRule($this->idCartRule);
                        }
                        else {
//                                    error_log(" - - 9 addCartRuleFromCart no if resultsCouponReductionProduct", 3, "/tmp/progresivo.log");
                        }
                    }
//		} else {
////                    error_log(" - -10 addCartRuleFromCart no if idCartRule", 3, "/tmp/progresivo.log");
//                }

		return true;

	}


	/**
	 * [removeCartRuleFromCart Funcion para remover las reglas del carrito ]
	 */
	public function removeCartRuleFromCart() {
//		error_log("\r\n  removeCartRuleFromCart: ".$this->ObjectCart->id, 3, "/tmp/progresivo.log");

		$QueryReglasCarritoEliminar = " SELECT /*'a' AS d1, */cr.id_cart_rule AS reglasdesassoc FROM ps_cart_cart_rule ccr
					INNER JOIN ps_cart_rule cr ON ( ccr.id_cart_rule = cr.id_cart_rule )
					LEFT JOIN ps_cart_rule_progressive_discounts crpd ON ( crpd.id_cart_rule = ccr.id_cart_rule )
					WHERE ccr.id_cart = ".$this->ObjectCart->id."
					AND ( cr.reduction_product = 0 
					OR cr.code != '' )
					AND crpd.id_cart_rule IS NULL 

					UNION


					SELECT /*'b' AS d1, */cr.id_cart_rule AS reglasdesassoc FROM ps_cart_cart_rule ccr
					INNER JOIN ps_cart_rule cr ON ( ccr.id_cart_rule = cr.id_cart_rule )
					LEFT JOIN ps_cart_rule_progressive_discounts crpd ON ( crpd.id_cart_rule = ccr.id_cart_rule )
					INNER JOIN ( 
							SELECT cr.reduction_product AS product_del, MAX( crpd.priority) AS prioridad 
								FROM ps_cart_cart_rule ccr
								INNER JOIN ps_cart_rule cr ON ( ccr.id_cart_rule = cr.id_cart_rule )
								LEFT JOIN ps_cart_rule_progressive_discounts crpd ON ( crpd.id_cart_rule = ccr.id_cart_rule )
								WHERE ccr.id_cart = ".$this->ObjectCart->id." -- AND cr.reduction_product = 0
								GROUP BY cr.reduction_product
								HAVING COUNT(cr.reduction_product) > 1 
										) crcd ON ( crcd.product_del = cr.reduction_product AND ( crcd.prioridad < crpd.priority OR crpd.priority IS NULL ) )
					WHERE ccr.id_cart = ".$this->ObjectCart->id;
                
//                error_log("SQL RARO: ".$QueryReglasCarritoEliminar, 3, "/tmp/progresivo.log" );
                        

		$result_QueryReglasCarritoEliminar = Db::getInstance()->ExecuteS($QueryReglasCarritoEliminar);
		
		$ReglasCarritoEliminar = array();

		foreach ($result_QueryReglasCarritoEliminar[0] as $key => $value) {
			$ReglasCarritoEliminar[] = $value;
//			error_log("\r\n  ReglasCarritoEliminar: ".$value." - ".count($ReglasCarritoEliminar), 3, "/tmp/progresivo.log");
		}

//		error_log("\r\n  removeCartRuleFromCart query \r\n : ".$reglasCarritoElminar, 3, "/tmp/progresivo.log");
		if ( count($ReglasCarritoEliminar) == 0 ) {
//			error_log("\r\n  no reglas a desasociar ", 3, "/tmp/progresivo.log");
			return true;
		} else {
//			error_log("\r\n  SI reglas a desasociar ", 3, "/tmp/progresivo.log");
			// remover cupones
			$queryRemoveCartRules = '
				DELETE FROM '._DB_PREFIX_.'cart_cart_rule
				WHERE id_cart = '.(int)$this->ObjectCart->id.' AND id_cart_rule IN ( '.implode(',', $ReglasCarritoEliminar ).' ) ' ;

//			error_log("\r\n  queryRemoveCartRules: ".$queryRemoveCartRules, 3, "/tmp/progresivo.log");
			//return true;
			// remover productos regalados
			$queryRemoveGiftProduct = '
				DELETE cp.*
				FROM '._DB_PREFIX_.'cart_product cp
				INNER JOIN '._DB_PREFIX_.'cart_rule cr
				ON ( cp.id_product = cr.gift_product )
				WHERE cp.id_cart = '.(int)$this->ObjectCart->id;

			if ( !empty($this->idCartRule) ) {
				// $queryRemoveCartRules .= ' AND id_cart_rule != '.$this->idCartRule;
				$queryRemoveGiftProduct .= ' AND cr.id_cart_rule != '.$this->idCartRule;
			}
			
//			error_log("\r\n  queryRemoveCartRules: ".$queryRemoveCartRules, 3, "/tmp/progresivo.log");
//			error_log("\r\n  queryRemoveGiftProduct: ".$queryRemoveGiftProduct, 3, "/tmp/progresivo.log");

			$res_query1 = Db::getInstance()->execute($queryRemoveCartRules);
			$res_query2 = Db::getInstance()->execute($queryRemoveGiftProduct);

//			error_log(" borrar y desasociar reglas q1: ".$res_query1." -  q2: ".$res_query2, 3, "/tmp/progresivo.log");
		}

		return true;
	}


	/**
	 * [removeResidueProgressiveDiscount Funcion para remover los cupones del carrito cuando se retira el descuento progresivo del carrito ]
	 */
	public function removeResidueProgressiveDiscount() {

		$queryCouponremovePG = "SELECT id_cart_rule
                                        FROM "._DB_PREFIX_."cart_cartrule_progressive_discounts
                                        WHERE id_cart = ".(int)$this->ObjectCart->id;

		$resultCouponremovePG = Db::getInstance()->ExecuteS($queryCouponremovePG);

		if ( !empty($resultCouponremovePG) ) {

			// remover cupones
			Db::getInstance()->execute('
				DELETE FROM '._DB_PREFIX_.'cart_cart_rule
				WHERE id_cart = '.(int)$this->ObjectCart->id.'
				AND id_cart_rule = '.$resultCouponremovePG[0]['id_cart_rule']
			);

			// remover productos regalados
			Db::getInstance()->execute('
				DELETE cp.*
				FROM '._DB_PREFIX_.'cart_product cp
				INNER JOIN '._DB_PREFIX_.'cart_rule cr
				ON ( cp.id_product = cr.gift_product )
				WHERE cp.id_cart = '.(int)$this->ObjectCart->id.'
				AND cr.id_cart_rule = '.$resultCouponremovePG[0]['id_cart_rule']
			);

			foreach ($resultCouponremovePG as $key => $value) {
				// remover relacion de carrito con cupon del descuento progresivo
				Db::getInstance()->execute('
					DELETE FROM '._DB_PREFIX_.'cart_cartrule_progressive_discounts
					WHERE id_cart = '.(int)$this->ObjectCart->id.'
					AND id_cart_rule = '.$value['id_cart_rule']
				);
			}

		}

		return true;
	}


	/**
	 * [validateRangeDateFinishProgressiveDiscount Funcion para validar si la fecha actual es menor a la fecha final del descuento progresivo]
	 */
	public function validateRangeDateFinishProgressiveDiscount() {
            $endDateProgressivediscount = strtotime( $this->beforeProgressiveDiscount['date_final_progressive_disscount'] );

            if ( $this->currentDate > $endDateProgressivediscount ) {
                $this->data = "1";
                $this->dataHistoryProgressiveDiscount();
                return true;
            }
            return false;		
	}

	/**
	 * [validateCounterResetProgressiveDiscount Funcion para validar si el contador de reinicio es menor al reinicio del descuento progresivo]
	 */
	public function validateCounterResetProgressiveDiscount() {
            $counterResetProgressiveDiscount = $this->beforeProgressiveDiscount['counter_reset'];
            $reset = $this->currentProgressiveDiscount['shopping_reset'];
            
            if ( $counterResetProgressiveDiscount >= $reset ) {
                $this->data = "1";
                $this->dataHistoryProgressiveDiscount();
                return true;
            }
            return false;
	}

	/**
	 * [validateEndDateNextPeriod Funcion para validar si la fecha actual no sobrepasa la fecha final del proximo periodo]
	 */
	public function validateEndDateNextPeriod() {
		$this->dateEndNextPeriod = strtotime ( '+'.$this->currentProgressiveDiscount['frequency'].' day' , strtotime ( $this->beforeProgressiveDiscount['date_order'] ) );

		if ( $this->currentDate > $this->dateEndNextPeriod ) {
			$this->data = "1";
			$this->dataHistoryProgressiveDiscount();
			return true;
		} else {
			return false;
		}
	}




	/**
	 * [validateCounterCyclesProgressiveDiscount Funcion para validar si el contador de ciclos es menor a la cantidad de ciclos del descuento progresivo]
	 */
	public function validateCounterCyclesProgressiveDiscount() {

		$counterCyclesProgressiveDiscount = $this->beforeProgressiveDiscount['counter_cycles'];
		$cycles = $this->currentProgressiveDiscount['cycles'];

		if ( $counterCyclesProgressiveDiscount > $cycles ) {
			return false;
		} else {
			return true;
		}
	}


	/**
	 * [validateCounterPeriodProgressiveDiscount Funcion para validar si el contador de periodos es menor al numero de periodos del descuento progresivo]
	 */
	public function validateCounterPeriodProgressiveDiscount() {
		$counterPeriodProgressiveDiscount = $this->beforeProgressiveDiscount['counter_period'];
		$periods = $this->currentProgressiveDiscount['periods'];
		if ( $counterPeriodProgressiveDiscount >= $periods ) {
			$this->data = "1";
			$this->dataHistoryProgressiveDiscount();
			return true;
		} else {
			return false;
		}
	}


	/**
	 * [validateCounterOrdersPeriodProgressiveDiscount Funcion para validar si el contador de ordenes por periodo es mayor al numero de limite de compras por cliente]
	 */
	public function validateCounterOrdersPeriodProgressiveDiscount() {
		$counterOrdersPeriodProgressiveDiscount = $this->beforeProgressiveDiscount['counter_orders_period'];
		$limitShoppingCustomer = $this->currentProgressiveDiscount['limit_shopping_customer'];
		if ( $counterOrdersPeriodProgressiveDiscount >= $limitShoppingCustomer ) {
			$this->data = "4";
			$this->dataHistoryProgressiveDiscount();
			return true;
		} else {
			return false;
		}
	}

	/**
	 * [validateRangeDateFinishPeriod Funcion para validar si la fecha actual es menor a la fecha final del periodo vigente]
	 */
	public function validateRangeDateFinishPeriod() {
            $endDateProgressivediscount = strtotime( $this->beforeProgressiveDiscount['date_final_period'] );
            return $this->currentDate > $endDateProgressivediscount ? true : false;
	}


	/**
	 * [dataHistoryProgressiveDiscount Funcion para tomar los datos del historial del descuento progresivo]
	 */
	public function dataHistoryProgressiveDiscount() {
		switch ( $this->data ) {
			case "1":
				$this->idCartRule = 0;
				$this->KeyCartRule = 0;
				$this->initialShopping = 1;
				$this->counterOrdersPeriod = 0;
				$this->counterPeriod = 0;
				$this->counterReset = 0;

				if ( empty($this->beforeProgressiveDiscount) ) {
					$this->counterCycles = 1;
				} else {
					$this->counterCycles = $this->beforeProgressiveDiscount['counter_cycles'] + 1;
				}

				$this->dateEndNextPeriod = "DATE_ADD(NOW(), INTERVAL ".$this->currentProgressiveDiscount['frequency']." DAY)";
				$this->dateEndProgressiveDiscount = "DATE_ADD(NOW(), INTERVAL (".$this->currentProgressiveDiscount['frequency']." * ".$this->currentProgressiveDiscount['periods'].") DAY)";
				break;

			case "2":
				$this->initialShopping = 0;
				$this->counterOrdersPeriod = 1;
				$this->counterPeriod = $this->beforeProgressiveDiscount['counter_period'] + 1;
				$this->counterReset = $this->beforeProgressiveDiscount['counter_reset'] + 1;
				$this->counterCycles = $this->beforeProgressiveDiscount['counter_cycles'];
				$this->dateEndNextPeriod = "'".date('Y-m-d H:i:s', $this->dateEndNextPeriod)."'";
				$this->dateEndProgressiveDiscount = "'".$this->beforeProgressiveDiscount['date_final_progressive_disscount']."'";
				break;

			case "3":
				$this->initialShopping = 0;
				$this->counterOrdersPeriod = $this->beforeProgressiveDiscount['counter_orders_period'] + 1;
				$this->counterPeriod = $this->beforeProgressiveDiscount['counter_period'] + 1;
				$this->counterReset = $this->beforeProgressiveDiscount['counter_reset'] + 1;
				$this->counterCycles = $this->beforeProgressiveDiscount['counter_cycles'];
				$this->dateEndNextPeriod = "'".$this->beforeProgressiveDiscount['date_final_period']."'";
				$this->dateEndProgressiveDiscount = "'".$this->beforeProgressiveDiscount['date_final_progressive_disscount']."'";
				break;

			case "4":
				$this->idCartRule = $this->beforeProgressiveDiscount['id_cart_rule'];
				$this->KeyCartRule = $this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'];
				$this->initialShopping = 0;
				$this->counterOrdersPeriod = $this->beforeProgressiveDiscount['counter_orders_period'];
				$this->counterPeriod = $this->beforeProgressiveDiscount['counter_period'];
				$this->counterReset = $this->beforeProgressiveDiscount['counter_reset'];
				$this->counterCycles = $this->beforeProgressiveDiscount['counter_cycles'];
				$this->dateEndNextPeriod = "'".$this->beforeProgressiveDiscount['date_final_period']."'";
				$this->dateEndProgressiveDiscount = "'".$this->beforeProgressiveDiscount['date_final_progressive_disscount']."'";
				break;

			case "5":
				$this->initialShopping = 0;
				$this->counterOrdersPeriod = $this->beforeProgressiveDiscount['counter_orders_period'] + 1;
				$this->counterPeriod = $this->beforeProgressiveDiscount['counter_period'];
				$this->counterReset = $this->beforeProgressiveDiscount['counter_reset'] + 1;
				$this->counterCycles = $this->beforeProgressiveDiscount['counter_cycles'];
				$this->dateEndNextPeriod = "'".$this->beforeProgressiveDiscount['date_final_period']."'";
				$this->dateEndProgressiveDiscount = "'".$this->beforeProgressiveDiscount['date_final_progressive_disscount']."'";
				break;
		}
//                error_log("\n\n 751 - this->data ".$this->data, 3 , "/tmp/progresivo.log");
//                error_log("\n\n 751 - this->idCartRule ".$this->idCartRule , 3 , "/tmp/progresivo.log");

		return true;
	}


	/**
	 * [addHistoryProgressiveDiscount Funcion para agregar la orden al historial de descuentos progresivos ]
	 */
	public function addHistoryProgressiveDiscount() {
                        
            if ( isset($this->idOrder) && !empty($this->idOrder) ) {

//                    error_log("\r\n idCartRule: ".print_r($this->idCartRule,true), 3, "/tmp/progresivo.log");
//                    error_log("\r\n KeyCartRule: ".print_r($this->KeyCartRule,true), 3, "/tmp/progresivo.log");
                    
                    $queryInsertHistoryProgressiveDiscount = "INSERT INTO "._DB_PREFIX_."order_history_progressive_discounts (
                                                    id_progressive_discount,
                                                    id_order,
                                                    id_customer,
                                                    id_product,
                                                    id_cart_rule,
                                                    id_cart_rule_progressive_disscount,
                                                    date_order,
                                                    date_final_period,
                                                    date_final_progressive_disscount,
                                                    initial_shopping,
                                                    counter_orders_period,
                                                    counter_period,
                                                    counter_reset,
                                                    counter_cycles
                                            )
                                            VALUES (
                                                    ".$this->idProgressiveDiscount.",
                                                    ".$this->idOrder.",
                                                    ".$this->idCustomer.",
                                                    ".$this->idProduct.",
                                                    ".$this->idCartRule.",
                                                    ".$this->KeyCartRule.",
                                                    NOW(),
                                                    ".$this->dateEndNextPeriod.",
                                                    ".$this->dateEndProgressiveDiscount.",
                                                    ".$this->initialShopping.",
                                                    ".$this->counterOrdersPeriod.",
                                                    ".$this->counterPeriod.",
                                                    ".$this->counterReset.",
                                                    ".$this->counterCycles."
                                            )";
                    
//                    error_log("\r\n queryInsertHistoryProgressiveDiscount: ".print_r($queryInsertHistoryProgressiveDiscount,true), 3, "/tmp/progresivo.log");
                    $resultsProgressiveDiscount = Db::getInstance()->Execute($queryInsertHistoryProgressiveDiscount);

                    $queryDeleteRelationCartCoupon = "DELETE FROM "._DB_PREFIX_."cart_cartrule_progressive_discounts WHERE id_cart = ".(int)$this->ObjectCart->id;
                    $resultsDeleteRelationCartCoupon = Db::getInstance()->Execute($queryDeleteRelationCartCoupon);

                    return true;
		}
//            }
	}
        
}

?>