<?php

/**
 * Description of ProgressiveDiscountsCore
 *
 * @author Jeisson Gomez
 */

class ProgressivediscountsCore
{
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


	/**
	 * [addProgressiveDiscount Funcion con el que se aplicara el descuento progresivo]
	 */
	public function addProgressiveDiscount( $Cart = null ) {

		// cargar objeto con las caracteristicas del carrito
		if ( !empty($Cart) && !is_null($Cart) ) {
			$this->ObjectCart = $Cart;
		} else {
			$this->ObjectCart = Context::getContext()->cart;
		}

		// tomar la fecha actual
		$date = getdate();
		$this->currentDate = $date[0];
		// $this->currentDate = strtotime( '2015-08-13 21:28:53' );

		// valida si se encuentra un usuario logeado en el sistema
		$isLogged = $this->validateLoggedUser();

		// valida si se encuentra un producto con descuento progresivo en el carrito
		$productIn = $this->validateProductInProgressiveDiscount();

		if ( $isLogged && $productIn) {

			// valida si existe historial del descuento progresivo a aplicar
			$inHistoryProgressiveDiscount = $this->validateHistoryProgressiveDiscount();
			if ( $inHistoryProgressiveDiscount ) {

				// si la orden anterior no se encuentra en el estado valido para aplicar el descuento progresivo, no aplicara ningun descuento ni registrara en historial de descuento progresivo
				$states_orders = explode(",", $this->currentProgressiveDiscount['states_orders']);
				if ( !in_array($this->beforeProgressiveDiscount['current_state'], $states_orders) ) {
					return false;
				}

				// valida si el contador de ciclos es mayor a la cantidd de cilos del descuento progresivo
				$validateCounterCycles = $this->validateCounterCyclesProgressiveDiscount();
				if ( !$validateCounterCycles ) {
					return false;
				} else {
					// valida si la fecha actual es mayor a la fecha final del descuento progresivo
					$validateRangeDateFinish = $this->validateRangeDateFinishProgressiveDiscount();

					// valida si el contador de reinicio es mayor al reinicio del descuento progresivo
					$validateCounterReset = $this->validateCounterResetProgressiveDiscount();

					if ( $validateRangeDateFinish || $validateCounterReset ) {
						$this->addHistoryProgressiveDiscount();
						return true;
					} else {

						// valida si la fecha actual es mayor a la fecha final del periodo vigente
						$validateRangeDateFinishPeriod = $this->validateRangeDateFinishPeriod();
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
								} else {

									// toma y aplica el cupon a aplicar del listado de descuento progresivo
									$this->addCartRuleFromCart();
									$this->data = "2";
									$this->dataHistoryProgressiveDiscount();
									$this->addHistoryProgressiveDiscount();
									return true;
								}
							}
						} else {

							// valida si la ultima compra con descuento progresivo es una compra inicial
							if ( $this->beforeProgressiveDiscount['initial_shopping'] == 1 ) {
								$this->addCartRuleFromCart();
								$this->data = "3";
								$this->dataHistoryProgressiveDiscount();
								$this->addHistoryProgressiveDiscount();
								return true;
							} else {

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
				}
			} else {
				$this->addHistoryProgressiveDiscount();
				return true;
			}
		} else {
			return false;
		}
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

		if ( $resultsSearchProduct = Db::getInstance()->ExecuteS($querySearchProduct) ) {
			$this->idProduct = $resultsSearchProduct[0]['id_product'];
			$this->currentProgressiveDiscount = $resultsSearchProduct[0];
			$this->idProgressiveDiscount = $resultsSearchProduct[0]['id_progressive_discount'];
			return true;
		}

		return false;
	}


	/**
	 * [validateHistoryProgressiveDiscount Funcion para validar si existe un historial del descuento progresivo a aplicar]
	 */
	public function validateHistoryProgressiveDiscount() {

		$querySearchHistory = "SELECT ohpd.*, o.current_state
								FROM "._DB_PREFIX_."order_history_progressive_discounts ohpd
								INNER JOIN "._DB_PREFIX_."orders o ON ( ohpd.id_order = o.id_order )
								WHERE ohpd.id_customer = ".$this->idCustomer."
								AND ohpd.id_product = ".$this->idProduct."
								AND ohpd.id_progressive_discount = ".$this->idProgressiveDiscount."
								ORDER BY ohpd.date_order DESC , ohpd.id_order_history_progressive_discount DESC";

		if ( $resultsSearchHistory = Db::getInstance()->ExecuteS($querySearchHistory) ) {
			$this->beforeProgressiveDiscount = $resultsSearchHistory[0];
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

		if ( empty($this->idCartRule) ) {
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

			if ( $this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] == 0 ) {
				$queryCartRuleAdd .= " AND crpd1.priority = 1";
			} else {
				// para tomar el siguiente cupon de la escala del descuento progresivo
				$queryCartRuleAdd .= " AND crpd1.id_cart_rule_progressive_discount = ".$this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'];
			}

			$resultsCartRuleAdd = Db::getInstance()->ExecuteS($queryCartRuleAdd);

			$resultsCartRuleAdd = $resultsCartRuleAdd[0];
			
			if ( ($resultsCartRuleAdd['Nextkeycartrule'] == "" && $resultsCartRuleAdd['Nextidcartrule'] == "") || $this->beforeProgressiveDiscount['id_cart_rule_progressive_disscount'] == 0 ) {
				$this->KeyCartRule = $resultsCartRuleAdd['keycartrule'];
				$this->idCartRule = $resultsCartRuleAdd['idcartrule'];
			} else {
				$this->KeyCartRule = $resultsCartRuleAdd['Nextkeycartrule'];
				$this->idCartRule = $resultsCartRuleAdd['Nextidcartrule'];
			}

			$queryValidateExistCouponInCart = "SELECT COUNT(*) AS QuantityCartRules
												FROM "._DB_PREFIX_."cart_cart_rule cr
												INNER JOIN "._DB_PREFIX_."cart_rule_progressive_discounts crpd
												ON cr.id_cart_rule = crpd.id_cart_rule
												INNER JOIN "._DB_PREFIX_."progressive_discounts pd
												ON crpd.id_progressive_discount = pd.id_progressive_discount
												WHERE cr.id_cart = ".(int)$this->ObjectCart->id."
												AND pd.id_progressive_discount = ".$this->idProgressiveDiscount;

			$resultsValidateExistCouponInCart = Db::getInstance()->ExecuteS($queryValidateExistCouponInCart);
			$resultsValidateExistCouponInCart = $resultsValidateExistCouponInCart[0];

			$this->removeCartRuleFromCart();

			if ( $resultsValidateExistCouponInCart['QuantityCartRules'] <= 0 ) {
				
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
				$resultsInsertCartCartRulePD = Db::getInstance()->ExecuteS($queryInsertCartCartRulePD);
				
				// valida si el cupon a agregar tiene restriccion a producto especifico
				// En caso de contar con la restriccion, se agregara el cupon al carrito solo si el producto del descuento progresivo es el mismo de la restriccion del cupon
				$queryCouponReductionProduct ="SELECT reduction_product
												FROM "._DB_PREFIX_."cart_rule
												WHERE id_cart_rule = ".$this->idCartRule;
				$resultsCouponReductionProduct = Db::getInstance()->ExecuteS($queryCouponReductionProduct);

				if ( $resultsCouponReductionProduct[0]['reduction_product'] == 0 || ($resultsCouponReductionProduct[0]['reduction_product'] == $this->idProduct) )
				{
					$this->ObjectCart->addCartRule($this->idCartRule);
				}
			}
		}

		return true;

	}


	/**
	 * [removeCartRuleFromCart Funcion para remover las reglas del carrito ]
	 */
	public function removeCartRuleFromCart() {

		// remover cupones
		$queryRemoveCartRules = '
			DELETE FROM '._DB_PREFIX_.'cart_cart_rule
			WHERE id_cart = '.(int)$this->ObjectCart->id;

		// remover productos regalados
		$queryRemoveGiftProduct = '
			DELETE cp.*
			FROM '._DB_PREFIX_.'cart_product cp
			INNER JOIN '._DB_PREFIX_.'cart_rule cr
			ON ( cp.id_product = cr.gift_product )
			WHERE cp.id_cart = '.(int)$this->ObjectCart->id;

		if ( !empty($this->idCartRule) ) {
			$queryRemoveCartRules .= ' AND id_cart_rule != '.$this->idCartRule;
			$queryRemoveGiftProduct .= ' AND cr.id_cart_rule != '.$this->idCartRule;
		}

		Db::getInstance()->execute($queryRemoveCartRules);
		Db::getInstance()->execute($queryRemoveGiftProduct);

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
		} else {
			return false;
		}
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
	 * [validateCounterResetProgressiveDiscount Funcion para validar si el contador de reinicio es menor al reinicio del descuento progresivo]
	 */
	public function validateCounterResetProgressiveDiscount() {
		$counterResetProgressiveDiscount = $this->beforeProgressiveDiscount['counter_reset'];
		$reset = $this->currentProgressiveDiscount['shopping_reset'];
		if ( $counterResetProgressiveDiscount >= $reset ) {
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

		if ( $this->currentDate > $endDateProgressivediscount ) {
			return true;
		} else {
			return false;
		}
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

		return true;
	}


	/**
	 * [addHistoryProgressiveDiscount Funcion para agregar la orden al historial de descuentos progresivos ]
	 */
	public function addHistoryProgressiveDiscount() {

		if ( isset($this->idOrder) && !empty($this->idOrder) ) {

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

			$resultsProgressiveDiscount = Db::getInstance()->ExecuteS($queryInsertHistoryProgressiveDiscount);

			$queryDeleteRelationCartCoupon = "DELETE FROM "._DB_PREFIX_."cart_cartrule_progressive_discounts WHERE id_cart = ".(int)$this->ObjectCart->id;
			$resultsDeleteRelationCartCoupon = Db::getInstance()->ExecuteS($queryDeleteRelationCartCoupon);

			return true;
		}
	}
}

?>