<?php
include(dirname(__FILE__) . '/../../../config/config.inc.php');
include(dirname(__FILE__) . '/../../../init.php');

class classProgressiveDiscounts {

	public function search_coupon( $search ) {

		$query = "SELECT 
					cr.id_cart_rule,
					crl.name,
					cr.reduction_percent,
					cr.reduction_amount,
					IFNULL( CONCAT('(ID: ',pl1.id_product,') ',pl1.name), 0 )AS reduction_product,
					cr.free_shipping,
					IFNULL( CONCAT('(ID: ',pl2.id_product,') ',pl2.name), 0 ) AS gift_product
				FROM "._DB_PREFIX_."cart_rule cr
				LEFT JOIN "._DB_PREFIX_."cart_rule_lang crl ON ( cr.id_cart_rule = crl.id_cart_rule )
				LEFT JOIN "._DB_PREFIX_."product_lang pl1 ON ( cr.reduction_product = pl1.id_product )
				LEFT JOIN "._DB_PREFIX_."product_lang pl2 ON ( cr.gift_product = pl2.id_product )
				WHERE cr.active = 1
				AND cr.id_cart_rule = '".$search."'";

		if ($results = Db::getInstance()->ExecuteS($query)) {
			$item_coupon = $results[0];
			return $item_coupon;
		} else {
			return 0;
		}

	}

	public function search_product( $search ) {

		$query = "SELECT
						p.id_product,
						pl.name,
						p.reference,
						IFNULL( t.rate,0 ) AS tax,
						ROUND( p.price ) AS price,
						ROUND( p.price + ( (p.price * IFNULL(t.rate,0) ) / 100 ) ) AS price_tax,
						IF( pd.active=1, ppd.id_progressive_discount, '' ) AS id_progressive_discount
					FROM "._DB_PREFIX_."product p
					INNER JOIN "._DB_PREFIX_."product_lang pl ON ( p.id_product = pl.id_product )
					LEFT JOIN "._DB_PREFIX_."tax t ON ( p.id_tax_rules_group = t.id_tax )
					LEFT JOIN "._DB_PREFIX_."product_progressive_discounts ppd ON ( p.id_product = ppd.id_product )
					LEFT JOIN "._DB_PREFIX_."progressive_discounts pd ON ( ppd.id_progressive_discount = pd.id_progressive_discount )
					WHERE p.active = 1
					AND ( p.id_product = '".$search."' OR p.reference = '".$search."' )";

		if ($results = Db::getInstance()->ExecuteS($query)) {
			$item_product = $results[0];

			if ( $item_product['id_progressive_discount'] == "" ) {
				return $item_product;
			}
			
			return 1;
		} else {
			return 0;
		}

	}

	public function add_new_progressive_discount( $name, $description, $frequency, $periods, $limit_shopping_customer, $reset, $cycles, $state, $states_orders, $list_cart_rules, $list_products ) {

		$completed = false;

		// insertar datos principales del descuento progresivo
		$queryProgressiveDiscount = "INSERT INTO "._DB_PREFIX_."progressive_discounts (
										name,
										description,
										active,
										frequency,
										periods,
										limit_shopping_customer,
										shopping_reset,
										cycles,
										states_orders,
										date_create,
										date_modify
									) 
									VALUES (
										'".$name."',
										'".$description."',
										".$state.",
										".$frequency.",
										".$periods.",
										".$limit_shopping_customer.",
										".$reset.",
										".$cycles.",
										'".$states_orders."',
										NOW(),
										NOW()
									)";

		if ( $resultsProgressiveDiscount = Db::getInstance()->ExecuteS($queryProgressiveDiscount) ) {

				// tomar id del descuento progresivo insertado
				$lastIdProgressiveDiscount = "SELECT MAX(id_progressive_discount) 
												FROM "._DB_PREFIX_."progressive_discounts";

				$idprogressiveDiscount = Db::getInstance()->getValue($lastIdProgressiveDiscount);

				// insertar cupones del descuento progresivo
				$cart_rules = explode("///", $list_cart_rules);
				foreach ($cart_rules as $keyCartRules => $valueCartRules) {
					$detailcartrule = explode(",", $valueCartRules);

					$queryCartRules = "INSERT INTO "._DB_PREFIX_."cart_rule_progressive_discounts (
											id_progressive_discount,
											id_cart_rule,
											priority
										) 
										VALUES (
											".$idprogressiveDiscount.",
											".$detailcartrule[0].",
											".($keyCartRules+1)."
										)";
                                        error_log("\n\n\n\n\t\t\t QueryCartRules\n\t\t\t".print_r($queryCartRules,true), 3, "/tmp/progresivo.log");
					$resultsCartRules = Db::getInstance()->ExecuteS($queryCartRules);
				}

				// insertar productos del descuento progresivo
				$products = explode("///", $list_products);
				foreach ($products as $keyProducts => $valueProduct) {
					$detailProduct = explode(",", $valueProduct);

					$queryProducts = "INSERT INTO "._DB_PREFIX_."product_progressive_discounts (
											id_progressive_discount,
											id_product,
											reference_product
										)
										VALUES
										(
											".$idprogressiveDiscount.",
											".$detailProduct[0].",
											".$detailProduct[2]."
										)";
					$resultsProducts = Db::getInstance()->ExecuteS($queryProducts);
				}
			$completed = true;
		} else {
			$completed = false;
		}

		return $completed;

	}

	public function view_detail_progressive_discount( $idProgressiveDiscount ) {

		$queryProgressiveDiscount = "SELECT *
									FROM "._DB_PREFIX_."progressive_discounts
									WHERE id_progressive_discount = ".$idProgressiveDiscount;

		$queryProducts = "SELECT
								p.id_product,
								pl.name,
								p.reference,
								IFNULL( t.rate,0 ) as tax,
								ROUND( p.price ) as price,
								ROUND( p.price + ( (p.price * IFNULL(t.rate,0) ) / 100 ) ) as price_tax
							FROM "._DB_PREFIX_."product_progressive_discounts ppd
							INNER JOIN "._DB_PREFIX_."product p ON ( ppd.id_product = p.id_product )
							INNER JOIN "._DB_PREFIX_."product_lang pl ON ( p.id_product = pl.id_product )
							LEFT JOIN "._DB_PREFIX_."tax t ON ( p.id_tax_rules_group = t.id_tax )
							WHERE ppd.id_progressive_discount = ".$idProgressiveDiscount;

		$queryCartRules = "SELECT
								crpd.priority,
								cr.id_cart_rule,
								crl.name,
								cr.code,
								cr.reduction_percent,
								cr.reduction_amount,
								IFNULL( CONCAT('(ID: ',pl1.id_product,') ',pl1.name), 0 )AS reduction_product,
								cr.free_shipping,
								IFNULL( CONCAT('(ID: ',pl2.id_product,') ',pl2.name), 0 ) AS gift_product
							FROM "._DB_PREFIX_."cart_rule_progressive_discounts crpd
							INNER JOIN "._DB_PREFIX_."cart_rule cr ON ( crpd.id_cart_rule = cr.id_cart_rule )
							LEFT JOIN ps_cart_rule_lang crl ON ( cr.id_cart_rule = crl.id_cart_rule )
							LEFT JOIN "._DB_PREFIX_."product_lang pl1 ON ( cr.reduction_product = pl1.id_product )
							LEFT JOIN "._DB_PREFIX_."product_lang pl2 ON ( cr.gift_product = pl2.id_product )
							WHERE crpd.id_progressive_discount = ".$idProgressiveDiscount."
							ORDER BY crpd.priority";

		$resultsProgressiveDiscount = Db::getInstance()->ExecuteS($queryProgressiveDiscount);
		$resultsProgressiveDiscount = $resultsProgressiveDiscount[0];

		$resultsProducts = Db::getInstance()->ExecuteS($queryProducts);

		$resultsCartRules = Db::getInstance()->ExecuteS($queryCartRules);

		$statesOrders = $this->search_states_orders( $resultsProgressiveDiscount['states_orders'] );



		// informacion general
		if ( !empty($resultsProgressiveDiscount) ) {

			if ( $resultsProgressiveDiscount['active'] == 1 ) {
				$enable = '<img id="enabledPD" title="Activo" src="../modules/progressivediscounts/icon/enabled.gif" onclick="changeStatus('.$resultsProgressiveDiscount['id_progressive_discount'].',0);"/>';
			} else {
				$enable = '<img title="Inactivo" src="../modules/progressivediscounts/icon/disabled.gif" />';
			}

			$htmlStatesOrders = "<ul style='margin-left:30px;'>";
			foreach ($statesOrders as $key => $value) {
				$htmlStatesOrders .= "<li type='circle'>".$value['name']."</li>";
			}
			$htmlStatesOrders .= "</ul>";

			$htmlDetail = "<fieldset>
						<legend>Descuento Progresivo</legend>
						<ul style='margin-left:10px;'>
							<li type='square'> <strong>ID: </strong>".$resultsProgressiveDiscount['id_progressive_discount']." </li>
							<li type='square'> <strong>Nombre: </strong>".$resultsProgressiveDiscount['name']." </li>
							<li type='square'> <strong>Descripción: </strong>".$resultsProgressiveDiscount['description']." </li>
							<li type='square'> <strong>Activo: </strong>".$enable." </li>
							<li type='square'> <strong>Frecuencia : </strong>".$resultsProgressiveDiscount['frequency']." Día(s) </li>
							<li type='square'> <strong>Numero de Periodos: </strong>".$resultsProgressiveDiscount['periods']." </li>
							<li type='square'> <strong>Numero Limite Compras Por Cliente: </strong>".$resultsProgressiveDiscount['limit_shopping_customer']." Orden(es) </li>
							<li type='square'> <strong>Reinicio: </strong>".$resultsProgressiveDiscount['shopping_reset']." Orden(es) </li>
							<li type='square'> <strong>Ciclos: </strong>".$resultsProgressiveDiscount['cycles']." </li>
							<li type='square'> <strong>Estados Orden: </strong>".$htmlStatesOrders." </li>
							<li type='square'> <strong>Fecha Creación: </strong>".$resultsProgressiveDiscount['date_create']." </li>
							<li type='square'> <strong>Fecha Modificación: </strong>".$resultsProgressiveDiscount['date_modify']." </li>
						</ul>
						</fieldset>
						<br>";
		}


		// lista de cupones
		if ( !empty($resultsCartRules) ) {
			$htmlDetail .= "<div id='detailcartrules' class='titledetails'>Cupones</div>
							<div id='detailListCartRules' class='list'>
								<table>
									<tr>
										<th>Escala</th>
										<th>ID Cupón</th>
										<th>Nombre</th>
										<th>Descuento Porentaje</th>
										<th>Descuento Importe</th>
										<th>Producto Especifico</th>
										<th>Transporte Gratuito</th>
										<th>Regalo Producto</th>
									</tr>";

			foreach ($resultsCartRules as $keyCartRules => $valueCartRules) {
 
				if ( $valueCartRules['reduction_product'] != "0" ) {
					$reduction_product = $valueCartRules['reduction_product'];
				} else {
					$reduction_product = '<img title="Inactivo" src="../modules/progressivediscounts/icon/disabled.gif" />';
				}

				if ( $valueCartRules['free_shipping'] == 1 ) {
					$free_shipping = '<img title="Activo" src="../modules/progressivediscounts/icon/enabled.gif" />';
				} else {
					$free_shipping = '<img title="Inactivo" src="../modules/progressivediscounts/icon/disabled.gif" />';
				}

				if ( $valueCartRules['gift_product'] != "0" ) {
					$gift_product = $valueCartRules['gift_product'];
				} else {
					$gift_product = '<img title="Inactivo" src="../modules/progressivediscounts/icon/disabled.gif" />';
				}

				$htmlDetail .= "<tr>
									<td>".$valueCartRules['priority']."</td>
									<td>".$valueCartRules['id_cart_rule']."</td>
									<td>".$valueCartRules['name']."</td>
									<td>".(int)$valueCartRules['reduction_percent']."%</td>
									<td>$".(int)$valueCartRules['reduction_amount']."</td>
									<td>".$reduction_product."</td>
									<td>".$free_shipping."</td>
									<td>".$gift_product."</td>
								</tr>";
			}

			$htmlDetail .= "</table>
							</div>
							<br>";
		}


		// lista de productos
		if ( !empty($resultsProducts) ) {
			$htmlDetail .= "<div id='detailproducts' class='titledetails'>Productos</div>
							<div id='detailListProducts' class='list'>
								<table>
									<tr>
										<th>ID Producto</th>
										<th>Referencia</th>
										<th>Nombre</th>
										<th>IVA</th>
										<th>Precio Base</th>
										<th>Precio Final</th>
									</tr>";

			foreach ($resultsProducts as $keyProducts => $valueProducts) {
				$htmlDetail .= "<tr>
									<td>".$valueProducts['id_product']."</td>
									<td>".$valueProducts['reference']."</td>
									<td>".$valueProducts['name']."</td>
									<td>".(int)$valueProducts['tax']."%</td>
									<td>$".$valueProducts['price']."</td>
									<td>$".$valueProducts['price_tax']."</td>
								</tr>";
			}

			$htmlDetail .= "</table>
							</div>
							<br>";
		}

		return $htmlDetail;
	}

	public function changeStatus( $idprogressivediscount, $newstate ) {
		$completed = false;

		$queryupdatestate = "UPDATE "._DB_PREFIX_."progressive_discounts
								SET active = ".$newstate." , date_modify = NOW()
								WHERE id_progressive_discount = ".$idprogressivediscount;

		if ( $resultsupdatestate = Db::getInstance()->ExecuteS($queryupdatestate) ) {
			$completed = true;
		}

		return $completed;
	}

	public function search_states_orders( $states_progressive_discounts = "" ) {

		$queryStateOrders = "SELECT otl.id_order_state, otl.name
									FROM "._DB_PREFIX_."order_state ot
									INNER JOIN "._DB_PREFIX_."order_state_lang otl ON ( ot.id_order_state = otl.id_order_state )
									WHERE ot.paid = 1";

		if ( $states_progressive_discounts != "" ) {
			$queryStateOrders .= " AND otl.id_order_state IN ( ".$states_progressive_discounts." )";
		}

		$resultsStateOrders = Db::getInstance()->ExecuteS($queryStateOrders);

		if ( $states_progressive_discounts != "" ) {
			return $resultsStateOrders;
		}

		$list = "";
		foreach ($resultsStateOrders as $key => $value) {

			if ( $value['id_order_state'] == 5 ) {
				$list .= " <input type='checkbox' name='chk_state_order' value='".$value['id_order_state']."' disabled='disabled' checked> ".$value['name']." <br>";
			} else {
				$list .= " <input type='checkbox' name='chk_state_order' value='".$value['id_order_state']."' disabled='disabled'> ".$value['name']." <br>";
			}

		}

		return $list;
	}

}
?>