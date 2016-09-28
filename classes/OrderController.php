<?php

class OrderController extends OrderControllerCore
{


	public function circuloSaludFullProducts(){

echo "<br> INICIANDO CIRCULO DE LA SALUD 1";

//echo "<pre>";var_dump(debug_backtrace()); echo "</pre>"; exit();

ini_set("log_errors", 1);
ini_set("error_reporting", E_ALL);

		$prods_a_circulo = $this->context->cart->getSummaryDetails()['products'];

		//echo "<br> update initial: ".$this->context->cart->update();

		echo "<br> prods enviar :<pre>";
		print_r($prods_a_circulo);
		echo "<br> : prods enviar </pre>";
		


			$obj_cirsalud = new CirculoSalud();
			$obj_cirsalud->debug_mode = false;

			if ( $obj_cirsalud -> Login() ) {


				foreach ($prods_a_circulo as $key => $value) {
					$productos[$key]['referencia'] = $value['reference'];
					$productos[$key]['cantidad'] = $value['cart_quantity'];
				}


				echo "<hr><pre> productos: ";
				print_r( $productos );
				echo "<br></pre> ";


				/*******************************LISTADO EN ARREGLO DE PRODUCTOS ENVIADOS Y DEVUELVE CUAL CON PROMOCION ********************************/
				
				/*$tarjes = array(
'9981417498158',
'9981417498141',
'9981417498134',
'9981417498004',
'9981417497991',
'9981417497861',
'9981417497854',
'9981417497847',
'9981417497830',
'9981417497823',
'9981417497816',
'9981417497809',
'9981417497793',
'9981417497786',
'9981417497779',
'9981417497878',
'9981417497885',
'9981417497984',
'9981417497977',
'9981417497960',
);

				foreach ($tarjes as $key => $value) {
					# code...
				$obj_cirsalud->GetBonusProductList($value, $productos);

				echo "<hr><pre> GetBonusProductList rta ".$value." :";
				print_r( $obj_cirsalud->_GetBonusProductList );
				echo "<br></pre><hr> ";

				}

				exit;*/

				

				$obj_cirsalud->GetBonusProductList('9981417498158', $productos);

				echo "<hr><pre> GetBonusProductList rta ".$value." :";
				print_r( $obj_cirsalud->_GetBonusProductList );
				echo "<br></pre><hr> ";

				$prods_ret = 0;
				$prods_ret_array = array();
				$total_prods = array();

				/*echo "<hr><pre> total_prods 1: ";
				print_r( $total_prods );
				echo "<br></pre><hr> ";*/
				
				/****************  ASOCIAR RESPUESTA WEB SERVICE DE PRODUCTOS A UN ARRAY ****************************/

				$array_ws_ret  = $this->objectToArray( $obj_cirsalud->_GetBonusProductList ); //->ResponseBonusList


				echo "<hr><pre> new rta: ";
				print_r( $array_ws_ret );
				echo "<br></pre><hr> ";

				// para validar el primer producto del carrito

				if ( !isset( $array_ws_ret['ResponseBonusList'][0]) ) {
					$arr_noid['ResponseBonusList']['0'] = $array_ws_ret['ResponseBonusList'];
					$array_ws_ret = $arr_noid;
				}

				foreach ( $array_ws_ret['ResponseBonusList'] as $key => $value ) {
					

					echo "<br> llave: -".$key.'-';

					if ( $key ==  $prods_ret)  {

							echo "<br> SI id en respuesta: ".$prods_ret . " - ".$key.' -ref : '.$value['Sku'];
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
							

						} else {

							echo "<br> NO id en respuesta: ".$prods_ret . " - ".$key;

						}
					
					$prods_ret++;
				}

				echo "<hr><pre> total_prods 2: ";
				print_r( $total_prods );
				echo "<br></pre><hr> ";

				/****************  ASOCIAR RESPUESTA WEB SERVIDE DE PRODUCTOS A UN ARRAY ****************************/



				//echo "<hr><pre> RESPUESTA PRODS ASOCIADO: ";
				//print_r( $prods_ret_array );
				//echo "<br></pre><hr> ";				
			} else {
				echo "<br> No logueado";
			}
		//print_r($this->isLogged);
		

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

        //echo "<br> ---------- Productos dos ----------". $this->context->customer->id."<hr><pre>";
		//print_r($this->context->cart->getProducts());
		//echo "</pre>";

		$prods_cart = array();

				
				/****************  ASOCIAR PRODUCTOS  DE CARRITO A UN ARRAY ****************************/

				foreach ( $prods_a_circulo as $key => $value ) {
					
					$prods_cart[ $value['reference'] ][ 'PiezasPorComprar' ] =  $value['cart_quantity'];
					$prods_cart[ $value['reference'] ][ 'quantity' ] =  $value['quantity'];
					$prods_cart[ $value['reference'] ][ 'id_product' ] =  $value['id_product'];
					$prods_cart[ $value['reference'] ][ 'name' ] =  $value['name'];
					$prods_cart[ $value['reference'] ][ 'price' ] =  $value['price'];
					$prods_cart[ $value['reference'] ][ 'rate' ] =  $value['rate'];
					$prods_cart[ $value['reference'] ][ 'price_wt' ] =  $value['price_wt'];
					$prods_cart[ $value['reference'] ][ 'total' ] =  $value['total'];
					$prods_cart[ $value['reference'] ][ 'total_wt' ] =  $value['total_wt'];
					$total_prods[ $value['reference'] ]['cart'] = 1;

				}

				/*echo "<hr><pre> total_prods 3: ";
				print_r( $total_prods );
				echo "<br></pre><hr> ";*/
		
		
		echo "<hr><pre>  PRODS CARRITO: ";
		print_r( $prods_cart );
		echo "<br></pre><hr> ";


// para borrar carritos: $cart_rule->delete();

$rules_prev_created = CartRule::getCartRulesByNameLang( 'CIRSAN_C'.$this->context->customer->id.'CI'.$this->context->cart->id, 1 );

echo "<br> cant reglas anteriores: ".$rules_in_cart_org = count($rules_prev_created);

echo "<pre> reglas return2: ";

foreach ($rules_prev_created as $key => $value) {
	echo "<br> customer: ".$value['id_customer']." - id_Rule: ".$value['id_cart_rule'];
}



/*
foreach ($rules_prev_created as $key => $value) {

	echo "<br> remover reglas: ".$this->context->cart->removeCartRule($value['id_cart_rule'])."  --  ".$value['id_cart_rule'];
	echo "<br> update for removed: ".$this->context->cart->update();

	//echo "<br> -- ".$value['id_cart_rule'] ." -- removida : ".$this->context->cart->removeCartRule($value['id_cart_rule']);
	$newdel = new CartRule($value['id_cart_rule']);
	echo "<br> objeto cartrule  borrar regla ---- borrada : ".$newdel->delete();


}

echo "<br>remover reglas: ".$this->context->cart->removeCartRules();

echo "</pre>";
echo "<br> update for removed: ".$this->context->cart->update();
*/


/*
$rules_prev_created_customer = CartRule::deleteByIdCustomer(  $this->context->customer->id );

echo "<pre> reglas return rules_prev_created_customer: ";
print_r( $rules_prev_created_customer );
echo "</pre>";
	*/

			foreach ( $total_prods as $key => $value ) {
				$dto_regalo_applicar = 0;
				$prod_regalar = 0;
				$porc_dto = 0;
				$mone_dto = 0;
				$product_restriction = 0;

					$prods = Product::searchByReference( $key, 'nadro', 1);

					echo "<br> REF: ". $key ;
					// si el producto está asociado al proveedor nadro, está activo y 
					
					if ( isset( $prods['id_product'] ) && $prods['id_product'] != '' && $prods['id_product'] != 0 ) {

						echo "<br> si prod: ". $key ;

						if ( isset( $total_prods[ $key ]['cart'] ) && $total_prods[ $key ]['cart'] == 1 && isset( $total_prods[ $key ]['ws'] ) && $total_prods[ $key ]['ws'] == 1 ) {

							echo "<br> ** REf: ".$key." - - ".$prods_cart[ $key ]['name']." Iguales ";
							echo "<br> -- Compradas cart = ".$prods_cart[ $key ]['PiezasPorComprar'];

							//echo "<br> -- Compradas webs = ".$prods_ret_array[ $key ]['PiezasPorComprar'];
							if (  $prods_ret_array[ $key ]['PiezasGratis'] != 0 ) {
								echo "<br> -- gratis == ".$prods_ret_array[ $key ]['PiezasGratis'];
								$prod_regalar = $prods['id_product'];
								$dto_regalo_applicar = 1;
								$product_restriction = 1;

							}

							if (  $prods_ret_array[ $key ]['PorcentajeDescuento'] != 0 ) {
								echo "<br> -- PorcentajeDescuento = ".$prods_ret_array[ $key ]['PorcentajeDescuento'];
								$porc_dto = $prods_ret_array[ $key ]['PorcentajeDescuento'];
								$dto_regalo_applicar = 2;
							} elseif (  $prods_ret_array[ $key ]['MontoDescuento'] != 0 ) {
								echo "<br> -- MontoDescuento = ".$prods_ret_array[ $key ]['MontoDescuento'];
								//$dto_monto_acum += $prods_ret_array[ $key ]['MontoDescuento'];
								$mone_dto = $prods_ret_array[ $key ]['MontoDescuento'];
								$dto_regalo_applicar = 3;
							}

						} elseif ( !isset( $total_prods[ $key ]['cart'] )  && isset( $total_prods[ $key ]['ws'] ) && $total_prods[ $key ]['ws'] == 1   ) {

							echo "<hr> ** NO EN CARRITO ------------ ";
							echo "<br> ** REf: ".$key." - ";
							echo "<br> ".$prods_cart[ $key ]['name']." Diferente ";
							echo "<br> -- Compradas cart = ".$prods_cart[ $key ]['PiezasPorComprar'];
							echo "<br> -- Compradas webs = ".$prods_ret_array[ $key ]['PiezasPorComprar'];

							$found = '';
							if ( array_key_exists($key, $prods_cart) && array_key_exists($key, $prods_ret_array) ){
								$found = 2;
								echo "<br> en ambos";
							} elseif (array_key_exists($key, $prods_cart)){
								$found = 1;
								echo "<br> en cart";
							} elseif (array_key_exists($key, $prods_ret_array)){
								$found = 0;
								echo "<br> en wsre";
							}

							/******************************* INICIO SI CANTIDAD COMPRADA DIFERENTE A RETORNADA **********************************************************

							if ( $prods_ret_array[ $key ]['PiezasPorComprar'] > $prods_cart[ $key ]['PiezasPorComprar'] ) {

								$this->context->cart->updateQty( ( $prods_ret_array[ $key ]['PiezasPorComprar'] - $prods_cart[ $key ]['PiezasPorComprar'] ) , $prods_cart[ $key ]['id_product'],0,0,'up',0);

							} elseif ( $prods_ret_array[ $key ]['PiezasPorComprar'] < $prods_cart[ $key ]['PiezasPorComprar'] ) {

								$this->context->cart->updateQty( ( $prods_cart[ $key ]['PiezasPorComprar'] - $prods_ret_array[ $key ]['PiezasPorComprar'] ) , $prods_cart[ $key ]['id_product'],0,0,'down',0);

							}

							/******************************* FIN SI CANTIDAD COMPRADA DIFERENTE A RETORNADA ************************************************************/

							if (  $prods_ret_array[ $key ]['PiezasGratis'] != 0 ) {
								echo "<br> -- gratis != ".$prods_ret_array[ $key ]['PiezasGratis'];
								$prod_regalar = $prods['id_product'];
								$dto_regalo_applicar = 1;

								/*$prods = Product::searchByReference( $key, 'nadro', 1);
								echo "<br>pr: <pre>";
								print_r($prods);
								echo "</pre>";*/
							}
							if (  $prods_ret_array[ $key ]['PorcentajeDescuento'] != 0 ) {
								echo "<br> IO Error -- PorcentajeDescuento = ".$prods_ret_array[ $key ]['PorcentajeDescuento'];
								//$porc_dto = $prods_ret_array[ $key ]['PorcentajeDescuento'];

							} elseif (  $prods_ret_array[ $key ]['MontoDescuento'] != 0 ) {
								echo "<br> IO Error -- MontoDescuento = ".$prods_ret_array[ $key ]['MontoDescuento'];
								//$mone_dto = $prods_ret_array[ $key ]['MontoDescuento'];
								//$dto_monto_acum += $prods_ret_array[ $key ]['MontoDescuento'];
							}
							//$this->context->cart->updateQty($value,$key,0,0,'up',0);
							//updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', $id_address_delivery = 0, Shop $shop = null, $auto_add_cart_rule = true)
						}

						/*elseif ( isset( $total_prods[ $key ]['cart'] ) && $total_prods[ $key ]['cart'] == 1 &&  !isset( $total_prods[ $key ]['ws'] )  ) {

						}*/


	/***************** CREACION DE CUPONES ********************/

/*echo "<pre> this object : ";
print_r( $this->context );
echo "</pre>";*/

echo "<br> dto: ".$dto_regalo_applicar;
echo "<br> id_cliente: ".$this->context->customer->id;
echo "<br> id_cart: ".$this->context->cart->id;










						if ( $dto_regalo_applicar != 0 && isset( $this->context->customer->id ) ) {

							echo "<br><hr> aplicar dto --".$this->context->cart->id;						



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
								$cart_rule->name[$language['id_lang']] = sprintf('CIRSAN_CI%d', $this->context->cart->id);

							// Define a temporary code
							//$cart_rule->code = ''; //sprintf('CIRSA_C%1$d_O%2$d', $this->context->customer->id, $this->context->cart->id);
							$cart_rule->quantity = 1;
							$cart_rule->quantity_per_user = 1;
							echo "<br> paso1";

							// Specific to the customer
							$cart_rule->id_customer = $this->context->customer->id;
							$now = time();
							$cart_rule->date_from = date('Y-m-d H:i:s', $now - (3600) );
							$cart_rule->date_to = date('Y-m-d H:i:s', $now + (3600 * 3 * 1)); // 3600 * 24 * 1: 3 hours  -   3600 * 24 * 1: 1 Day -  3600 * 24 * 365.25: 1 year  
							$cart_rule->partial_use = 0;
							$cart_rule->active = 1;
							$cart_rule->reduction_product = $prods['id_product'];
echo "<br> paso2";
							switch ($dto_regalo_applicar) {
								case '1': //Producto
									$cart_rule->description = sprintf('Cupon circulo salud regalo #%d',$key);
									$cart_rule->reduction_product = 0;
									$cart_rule->product_restriction = $product_restriction;
									$cart_rule->gift_product = $prods['id_product']; //prod_regalar = 
									break;

								case '2': //Descuento
									$cart_rule->description = sprintf('Cupon circulo salud descuento #%d \%',$porc_dto);
									$cart_rule->reduction_percent = $porc_dto;
									break;
									
								case '3': //Monto
									$cart_rule->description = sprintf('Cupon circulo salud descuento $ #%d',$mone_dto);
									$cart_rule->reduction_amount = $mone_dto;
									break;
								
								default:
									# code...
									break;
							}
echo "<br> paso3";
							$cart_rule->reduction_tax = false;
							
							$cart_rule->minimum_amount_currency = $this->context->cart->id_currency;
							$cart_rule->reduction_currency = $this->context->cart->id_currency;
echo "<br> paso4";
							try {

									if ( !$cart_rule->add() ) {
		echo "<br> paso5";
										echo "<br> Cupon NOOOOOOOOOOO creado-";
										$this->errors[] = Tools::displayError('You cannot generate a voucher.');
									} else {
		echo "<br> paso6";
										echo "<br> Cupon creado id: ".$cart_rule->id;
										// Update the voucher code and name
										foreach ($languages as $language)
											$cart_rule->name[$language['id_lang']] = sprintf('CIRSAN_C%1$dCI%2$dV%3$d', $this->context->customer->id, $this->context->cart->id, $cart_rule->id);
										$cart_rule->code = '' ;// sprintf('CIRSA_C%1$d_O%2$d', $cart_rule->id, $this->context->customer, $this->context->cart->id);
										
										echo "<br>Adicionando regla a carrito: ".$this->context->cart->addCartRule($cart_rule->id);
										echo "<br> update for : ".$this->context->cart->update();
										
										if (!$cart_rule->update()) {

											$this->errors[] = Tools::displayError('You cannot generate a voucher.');

										} else {

											echo "<br> Cupon actualizado : ".implode(', ', $cart_rule->name );

										}
									}

							} catch (Exception $e) {
								print_r($e);
							}
							
						
					
						}
					} else {
						echo "<br><br><br> no asociado a nadro";
						echo "<br><br><br> NO prod: ". $key ;
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








	public function objectToArray($data) 
	{
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










	public function circuloSaludxxxxxx(){

echo "<br> INICIANDO CIRCULO DE LA SALUD 1";

//echo "<pre>";var_dump(debug_backtrace()); echo "</pre>"; exit();

ini_set("log_errors", 1);
ini_set("error_reporting", E_ALL);

		$prods_a_circulo = $this->context->cart->getSummaryDetails()['products'];

		//echo "<br> update initial: ".$this->context->cart->update();
/*
		echo "<br> prods enviar :<pre>";
		print_r($prods_a_circulo);
		echo "<br> : prods enviar </pre>";
		exit();*/



			$obj_cirsalud = new CirculoSalud();
			$obj_cirsalud->debug_mode = false;

			if ( $obj_cirsalud -> Login() ) {



				// para borrar carritos: $cart_rule->delete();

				$rules_prev_created = CartRule::getCartRulesByNameLang( 'CIRSAN_C'.$this->context->customer->id.'CI'.$this->context->cart->id, 1 );

				echo "<br> cant reglas anteriores: ".$rules_in_cart_org = count($rules_prev_created);

				echo "<pre> reglas return2: ";

				foreach ($rules_prev_created as $key => $value) {
					echo "<br> customer: ".$value['id_customer']." - id_Rule: ".$value['id_cart_rule'];
				}



				// remover reglas asociadas al carrito				
				foreach ($rules_prev_created as $key => $value) {

					echo "<br> remover reglas: ".$this->context->cart->removeCartRule($value['id_cart_rule'])."  --  ".$value['id_cart_rule'];
					echo "<br> update for removed: ".$this->context->cart->update();

					//echo "<br> -- ".$value['id_cart_rule'] ." -- removida : ".$this->context->cart->removeCartRule($value['id_cart_rule']);
					$newdel = new CartRule($value['id_cart_rule']);
					echo "<br> objeto cartrule  borrar regla ---- borrada : ".$newdel->delete();

				}

				echo "<br>remover reglas: ".$this->context->cart->removeCartRules();
				echo "</pre>";
				echo "<br> update for removed: ".$this->context->cart->update();
				


				/*
				$rules_prev_created_customer = CartRule::deleteByIdCustomer(  $this->context->customer->id );

				echo "<pre> reglas return rules_prev_created_customer: ";
				print_r( $rules_prev_created_customer );
				echo "</pre>";
				*/


				foreach ($prods_a_circulo as $key => $value_psend) {
					$productos = array();

					$productos[$key]['referencia'] = $value_psend['reference'];
					$productos[$key]['cantidad'] = $value_psend['cart_quantity'];
				


				echo "<hr><pre> productos: ";
				print_r( $productos );
				echo "<br></pre> ";


				/*******************************LISTADO EN ARREGLO DE PRODUCTOS ENVIADOS Y DEVUELVE CUAL CON PROMOCION ********************************/

				$obj_cirsalud->GetBonusProductList('9981417497861', $productos);

				/*echo "<hr><pre> GetBonusProductList rta: ";
				print_r( $obj_cirsalud->_GetBonusProductList );
				echo "<br></pre><hr> ";*/

				$prods_ret = 0;
				$prods_ret_array = array();
				$total_prods = array();

				/*echo "<hr><pre> total_prods 1: ";
				print_r( $total_prods );
				echo "<br></pre><hr> ";*/
				
				/****************  ASOCIAR RESPUESTA WEB SERVICE DE PRODUCTOS A UN ARRAY ****************************/

				$array_ws_ret  = $this->objectToArray( $obj_cirsalud->_GetBonusProductList ); //->ResponseBonusList


				echo "<hr><pre> new rta: ";
				print_r( $array_ws_ret );
				echo "<br></pre><hr> ";

				// para validar el primer producto del carrito

				if ( !isset( $array_ws_ret['ResponseBonusList'][0]) ) {
					$arr_noid['ResponseBonusList']['0'] = $array_ws_ret['ResponseBonusList'];
					$array_ws_ret = $arr_noid;
				}

				foreach ( $array_ws_ret['ResponseBonusList'] as $key => $value ) {
					

					echo "<br> llave: -".$key.'-';

					if ( $key ==  $prods_ret)  {

							echo "<br> SI id en respuesta: ".$prods_ret . " - ".$key.' -ref : '.$value['Sku'];
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
							

						} else {

							echo "<br> NO id en respuesta: ".$prods_ret . " - ".$key;

						}
					
					$prods_ret++;
				}
/*
				echo "<hr><pre> total_prods 2: ";
				print_r( $total_prods );
				echo "<br></pre><hr> ";*/

				/****************  ASOCIAR RESPUESTA WEB SERVIDE DE PRODUCTOS A UN ARRAY ****************************/



				//echo "<hr><pre> RESPUESTA PRODS ASOCIADO: ";
				//print_r( $prods_ret_array );
				//echo "<br></pre><hr> ";	
				



				//print_r($this->isLogged);
		

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

        //echo "<br> ---------- Productos dos ----------". $this->context->customer->id."<hr><pre>";
		//print_r($this->context->cart->getProducts());
		//echo "</pre>";

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

				echo "<hr><pre> total_prods 3: ";
				print_r( $total_prods );
				echo "<br></pre><hr> ";
		
		
		echo "<hr><pre>  PRODS CARRITO: ";
		print_r( $prods_cart );
		echo "<br></pre><hr> ";


			foreach ( $total_prods as $key => $value ) {
				$dto_regalo_applicar = 0;
				$prod_regalar = 0;
				$porc_dto = 0;
				$mone_dto = 0;
				$product_restriction = 0;

					$prods = Product::searchByReference( $key, 'nadro', 1);

					echo "<br> REF: ". $key ;
					// si el producto está asociado al proveedor nadro, está activo y 
					
					if ( isset( $prods['id_product'] ) && $prods['id_product'] != '' && $prods['id_product'] != 0 ) {

						echo "<br> si prod: ". $key ;

						if ( isset( $total_prods[ $key ]['cart'] ) && $total_prods[ $key ]['cart'] == 1 && isset( $total_prods[ $key ]['ws'] ) && $total_prods[ $key ]['ws'] == 1 ) {

							echo "<br> ** REf: ".$key." - - ".$prods_cart[ $key ]['name']." Iguales ";
							echo "<br> -- Compradas cart = ".$prods_cart[ $key ]['PiezasPorComprar'];

							//echo "<br> -- Compradas webs = ".$prods_ret_array[ $key ]['PiezasPorComprar'];
							if (  $prods_ret_array[ $key ]['PiezasGratis'] != 0 ) {
								echo "<br> -- gratis == ".$prods_ret_array[ $key ]['PiezasGratis'];
								$prod_regalar = $prods['id_product'];
								$dto_regalo_applicar = 1;
								$product_restriction = 1;

							}

							if (  $prods_ret_array[ $key ]['PorcentajeDescuento'] != 0 ) {
								echo "<br> -- PorcentajeDescuento = ".$prods_ret_array[ $key ]['PorcentajeDescuento'];
								$porc_dto = $prods_ret_array[ $key ]['PorcentajeDescuento'];
								$dto_regalo_applicar = 2;
							} elseif (  $prods_ret_array[ $key ]['MontoDescuento'] != 0 ) {
								echo "<br> -- MontoDescuento = ".$prods_ret_array[ $key ]['MontoDescuento'];
								//$dto_monto_acum += $prods_ret_array[ $key ]['MontoDescuento'];
								$mone_dto = $prods_ret_array[ $key ]['MontoDescuento'];
								$dto_regalo_applicar = 3;
							}

						} elseif ( !isset( $total_prods[ $key ]['cart'] )  && isset( $total_prods[ $key ]['ws'] ) && $total_prods[ $key ]['ws'] == 1   ) {

							echo "<hr> ** NO EN CARRITO ------------ ";
							echo "<br> ** REf: ".$key." - ";
							echo "<br> ".$prods_cart[ $key ]['name']." Diferente ";
							echo "<br> -- Compradas cart = ".$prods_cart[ $key ]['PiezasPorComprar'];
							echo "<br> -- Compradas webs = ".$prods_ret_array[ $key ]['PiezasPorComprar'];

							$found = '';
							if ( array_key_exists($key, $prods_cart) && array_key_exists($key, $prods_ret_array) ){
								$found = 2;
								echo "<br> en ambos";
							} elseif (array_key_exists($key, $prods_cart)){
								$found = 1;
								echo "<br> en cart";
							} elseif (array_key_exists($key, $prods_ret_array)){
								$found = 0;
								echo "<br> en wsre";
							}

							/******************************* INICIO SI CANTIDAD COMPRADA DIFERENTE A RETORNADA **********************************************************

							if ( $prods_ret_array[ $key ]['PiezasPorComprar'] > $prods_cart[ $key ]['PiezasPorComprar'] ) {

								$this->context->cart->updateQty( ( $prods_ret_array[ $key ]['PiezasPorComprar'] - $prods_cart[ $key ]['PiezasPorComprar'] ) , $prods_cart[ $key ]['id_product'],0,0,'up',0);

							} elseif ( $prods_ret_array[ $key ]['PiezasPorComprar'] < $prods_cart[ $key ]['PiezasPorComprar'] ) {

								$this->context->cart->updateQty( ( $prods_cart[ $key ]['PiezasPorComprar'] - $prods_ret_array[ $key ]['PiezasPorComprar'] ) , $prods_cart[ $key ]['id_product'],0,0,'down',0);

							}

							/******************************* FIN SI CANTIDAD COMPRADA DIFERENTE A RETORNADA ************************************************************/

							if (  $prods_ret_array[ $key ]['PiezasGratis'] != 0 ) {
								echo "<br> -- gratis != ".$prods_ret_array[ $key ]['PiezasGratis'];
								$prod_regalar = $prods['id_product'];
								$dto_regalo_applicar = 1;
								$product_restriction = 1;

								/*$prods = Product::searchByReference( $key, 'nadro', 1);
								echo "<br>pr: <pre>";
								print_r($prods);
								echo "</pre>";*/
							}
							if (  $prods_ret_array[ $key ]['PorcentajeDescuento'] != 0 ) {
								echo "<br> IO Error -- PorcentajeDescuento = ".$prods_ret_array[ $key ]['PorcentajeDescuento'];
								//$porc_dto = $prods_ret_array[ $key ]['PorcentajeDescuento'];

							} elseif (  $prods_ret_array[ $key ]['MontoDescuento'] != 0 ) {
								echo "<br> IO Error -- MontoDescuento = ".$prods_ret_array[ $key ]['MontoDescuento'];
								//$mone_dto = $prods_ret_array[ $key ]['MontoDescuento'];
								//$dto_monto_acum += $prods_ret_array[ $key ]['MontoDescuento'];
							}
							//$this->context->cart->updateQty($value,$key,0,0,'up',0);
							//updateQty($quantity, $id_product, $id_product_attribute = null, $id_customization = false, $operator = 'up', $id_address_delivery = 0, Shop $shop = null, $auto_add_cart_rule = true)
						}

						/*elseif ( isset( $total_prods[ $key ]['cart'] ) && $total_prods[ $key ]['cart'] == 1 &&  !isset( $total_prods[ $key ]['ws'] )  ) {

						}*/


	/***************** CREACION DE CUPONES ********************/

/*echo "<pre> this object : ";
print_r( $this->context );
echo "</pre>";*/
/*
echo "<br> dto: ".$dto_regalo_applicar;
echo "<br> id_cliente: ".$this->context->customer->id;
echo "<br> id_cart: ".$this->context->cart->id;
*/



						if ( $dto_regalo_applicar != 0 && isset( $this->context->customer->id ) ) {

							echo "<br><hr> aplicar dto --".$this->context->cart->id;						



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
								$cart_rule->name[$language['id_lang']] = sprintf('CIRSAN_CI%d', $this->context->cart->id);

							// Define a temporary code
							//$cart_rule->code = ''; //sprintf('CIRSA_C%1$d_O%2$d', $this->context->customer->id, $this->context->cart->id);
							$cart_rule->quantity = 1;
							$cart_rule->quantity_per_user = 1;
echo "<br> paso1";

							// Specific to the customer
							$cart_rule->id_customer = $this->context->customer->id;
							$now = time();
							$cart_rule->date_from = date('Y-m-d H:i:s', $now - (3600) );
							$cart_rule->date_to = date('Y-m-d H:i:s', $now + (3600 * 3 * 1)); // 3600 * 24 * 1: 3 hours  -   3600 * 24 * 1: 1 Day -  3600 * 24 * 365.25: 1 year  
							$cart_rule->partial_use = 0;
							$cart_rule->active = 1;
							$cart_rule->reduction_product = $prods['id_product'];
echo "<br> paso2";
							switch ($dto_regalo_applicar) {
								case '1': //Producto
									$cart_rule->description = sprintf('Cupon circulo salud regalo #%d',$key);
									$cart_rule->reduction_product = 0;
									$cart_rule->product_restriction = $product_restriction;
									$cart_rule->gift_product = $prods['id_product']; //prod_regalar = 


									/*
									
									Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
				VALUES ('.(int)$currentObject->id.', '.(int)Tools::getValue('product_rule_group_'.$ruleGroupId.'_quantity').')');
				$id_product_rule_group = Db::getInstance()->Insert_ID();
				
									 */
									
									break;

								case '2': //Descuento %
									$cart_rule->description = sprintf('Cupon circulo salud descuento #%d \%',$porc_dto);
									$cart_rule->reduction_percent = $porc_dto;
									break;
									
								case '3': //Monto $
									$cart_rule->description = sprintf('Cupon circulo salud descuento $ #%d',$mone_dto);
									$cart_rule->reduction_amount = $mone_dto;
									break;
								
								default:
									# code...
									break;
							}
echo "<br> paso3";
							$cart_rule->reduction_tax = false;
							
							$cart_rule->minimum_amount_currency = $this->context->cart->id_currency;
							$cart_rule->reduction_currency = $this->context->cart->id_currency;
echo "<br> paso4";
							try {

									if ( !$cart_rule->add() ) {
		echo "<br> paso5";
										echo "<br> Cupon NOOOOOOOOOOO creado-";
										$this->errors[] = Tools::displayError('You cannot generate a voucher.');
									} else {
		echo "<br> paso6";
											if( $dto_regalo_applicar == 1 ) {

												echo "<br> ins 1: ".Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_group` (`id_cart_rule`, `quantity`)
												VALUES ('.(int)$cart_rule->id.', 1)');
												$id_product_rule_group = Db::getInstance()->Insert_ID();
												
												echo "<br> ins 2: ".Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule` (`id_product_rule_group`, `type`)
												VALUES ('.(int)$id_product_rule_group.', "products")');
												$id_product_rule = Db::getInstance()->Insert_ID();

												echo "<br> ins 3: ".Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'cart_rule_product_rule_value` (`id_product_rule`, `id_item`) VALUES ('.$id_product_rule.', '.$value_psend['id_product'].' ) ');

											}

										echo "<br> Cupon creado id: ".$cart_rule->id;
										// Update the voucher code and name
										foreach ($languages as $language)
											$cart_rule->name[$language['id_lang']] = sprintf('CIRSAN_C%1$dCI%2$dV%3$d', $this->context->customer->id, $this->context->cart->id, $cart_rule->id);
										$cart_rule->code =  str_replace(' ', '', $cart_rule->description); // sprintf('CIRSA_C%1$d_O%2$d', $cart_rule->id, $this->context->customer, $this->context->cart->id);
										
										echo "<br>Adicionando regla a carrito: ".$this->context->cart->addCartRule($cart_rule->id);
										echo "<br> update for : ".$this->context->cart->update();
										
										if (!$cart_rule->update()) {

											$this->errors[] = Tools::displayError('You cannot generate a voucher.');

										} else {

											echo "<br> Cupon actualizado : ".implode(', ', $cart_rule->name );

										}
									}

							} catch (Exception $e) {
								print_r($e);
							}
							
						
					
						}

					} else {
						echo "<br><br><br> no asociado a nadro";
						echo "<br><br><br> NO prod: ". $key ;
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

		
			} else {
				echo "<br> No logueado";
			}


	}

		/**
	 * Initialize order controller
	 * @see FrontController::init()
	 */
	public function init()
	{
		global $orderTotal;

		parent::init();

		$this->step = (int)(Tools::getValue('step'));
		if (!$this->nbProducts)
			$this->step = -1;		

		// If some products have disappear
		if (!$this->context->cart->checkQuantities())
		{
			$this->step = 0;
			$this->errors[] = Tools::displayError('An item in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.');
		}

		// Check minimal amount
		$currency = Currency::getCurrency((int)$this->context->cart->id_currency);

		$orderTotal = $this->context->cart->getOrderTotal();
		$minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
		if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase && $this->step > 0)
		{
			$this->step = 0;
			$this->errors[] = sprintf(
				Tools::displayError('A minimum purchase total of %s is required in order to validate your order.'),
				Tools::displayPrice($minimal_purchase, $currency)
			);
		}
		if (!$this->context->customer->isLogged(true) && in_array($this->step, array(1, 2, 3)))
		{
			$back_url = $this->context->link->getPageLink('order', true, (int)$this->context->language->id, array('step' => $this->step, 'multi-shipping' => (int)Tools::getValue('multi-shipping')));
			$params = array('multi-shipping' => (int)Tools::getValue('multi-shipping'), 'display_guest_checkout' => (int)Configuration::get('PS_GUEST_CHECKOUT_ENABLED'), 'back' => $back_url);
			Tools::redirect($this->context->link->getPageLink('authentication', true, (int)$this->context->language->id, $params));
		}

		if (Tools::getValue('multi-shipping') == 1)
			$this->context->smarty->assign('multi_shipping', true);
		else
			$this->context->smarty->assign('multi_shipping', false);

		if ($this->context->customer->id)
			$this->context->smarty->assign('address_list', $this->context->customer->getAddresses($this->context->language->id));
		else
			$this->context->smarty->assign('address_list', array());

		$this->circuloSaludFullProducts();
/*
		$cuponcirculo = new CuponCirculoSalud();
		$cuponcirculo->circuloSalud( $this->context, true );
*/
	}

	public function initContent()
	{
		//echo "<br> ----". $this->context->customer->id."<hr><pre>";
		//print_r($this->context->cart->getProducts());
		//echo "</pre>";

		parent::initContent();
		$this->context->smarty->assign('expressEnabled',Configuration::get('ENVIO_EXPRESS'));
		if (Tools::isSubmit('ajax') && Tools::getValue('method') == 'updateExtraCarrier')
		{
			// Change virtualy the currents delivery options
			$delivery_option = $this->context->cart->getDeliveryOption();
			$delivery_option[(int)Tools::getValue('id_address')] = Tools::getValue('id_delivery_option');
			$this->context->cart->setDeliveryOption($delivery_option);
			$this->context->cart->save();
			$return = array(
				'content' => Hook::exec(
					'displayCarrierList',
					array(
						'address' => new Address((int)Tools::getValue('id_address'))
					)
				)
			);
			die(Tools::jsonEncode($return));
		}

		if ($this->nbProducts)
			$this->context->smarty->assign('virtual_cart', $this->context->cart->isVirtualCart());

		// 4 steps to the order
		switch ((int)$this->step)
		{
			case -1;
				$this->context->smarty->assign('empty', 1);
				$this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
			break;

			case 1:
//###############################################    
 if(!isset($_SESSION)) 
    { 
        session_start(); 
    }                            
// si la varible sesión (formulamedica) se creo                            
if (isset($_SESSION['formulamedica'])){
   // si la varible de seción (formulamedica) es igual a true
   
if($_SESSION['formulamedica']==true) {
self::$smarty->assign('formula',true);
}
 else {
    self::$smarty->assign('formula',false);
}
}
 else {
    self::$smarty->assign('formula',false);
} 


if(!$this->is_formula())
{
self::$smarty->assign('formula',true);    
}
                           
                            
                            
				$this->_assignAddress();
				$this->processAddressFormat();
				if (Tools::getValue('multi-shipping') == 1)
				{
					$this->_assignSummaryInformations();
					$this->context->smarty->assign('product_list', $this->context->cart->getProducts());
					$this->setTemplate(_PS_THEME_DIR_.'order-address-multishipping.tpl');
				}
				else
                                   /******* Codigo para Direcciones Ajax *******/
                                        $idcliente = $this->context->customer->id; 
                                  $sql="SELECT ad.id_address, 
                              				   ad.id_state,
                                  			   st.name AS state,
                                  			   ad.id_customer,
                                  			   ad.alias,
                                  			   ad.city,
                                  			   ad.address1,
                                  			   ad.address2,
 												ac.id_city,
												cc.express_abajo AS express
                                  		FROM "._DB_PREFIX_."address AS ad
                                  				Inner Join "._DB_PREFIX_."state AS st
                                  				ON ad.id_state = st.id_state
                                  				INNER JOIN "._DB_PREFIX_."address_city AS ac
												ON ad.id_address=ac.id_address
												INNER JOIN "._DB_PREFIX_."carrier_city AS cc
												ON ac.id_city=cc.id_city_des
                                  		WHERE ad.id_customer='".$idcliente."' AND ad.deleted=0";
                                  $result=Db::getInstance()->ExecuteS($sql,FALSE);
                                        $direcciones=array();
                                        $total=0;
                                        foreach($result as $row) {
                                            $direcciones[]=$row;                                           
                                            $total+=1;
                                        }
                                        
                                        
                                        $pais = $this->context->country->id;
                                        $sqlpais="SELECT ps_state.id_state, ps_state.name AS state 
                                            FROM ps_state 
                                            WHERE ps_state.id_country =  ".$pais." ORDER BY state ASC ;";
				$rspais=Db::getInstance()->ExecuteS($sqlpais,FALSE);
				$estados=array();
				foreach($rspais as $estado) {
					$estados[]=$estado;
				}
				$this->context->smarty->assign('cliente',$idcliente);
				$this->context->smarty->assign('pais',$pais);
				$this->context->smarty->assign('estados',$estados);
				$this->context->smarty->assign('total',$total);
				$this->context->smarty->assign('direcciones',$direcciones);
				$this->context->smarty->assign('xps',Context::getContext()->cookie->check_xps);
				/******* Fin Codigo para Direcciones Ajax *******/
				$this->setTemplate(_PS_THEME_DIR_.'order-address.tpl');
				break;
	
				//			case 2:
				//				if (Tools::isSubmit('processAddress'))
					//					$this->processAddress();
				//				$this->autoStep();
				//				$this->_assignCarrier();
				//				$this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
				//			break;
	
	
			case 2:
				// varible utilizada para validar si se requiere formula medica
				$formula=false;
	
	
				$formula=$this->is_formula();
	
	
				// Si $formula es verdadera se muestra la pantalla para que el cliente registre su formula medica.
				if($formula)
				{
					if (Tools::isSubmit('processAddress'))
						$this->processAddress();
					$this->autoStep();
					$this->_assignCarrier();
					$this->setTemplate(_PS_THEME_DIR_.'order-carrier.tpl');
					//$this->setTemplate(_PS_THEME_DIR_.'order-carrier-test.tpl');
					//$this->setTemplate(_PS_THEME_DIR_.'order-carrier-org.tpl');
	
				}
				else{
	
					$this->disableMediosP();
					$this->show_contra_entrega();
					$this->block_medioP_sobre_costo();
					$this->list_medios_de_pago();
	
					if (Tools::isSubmit('processAddress'))
						$this->processAddress();
					$this->autoStep();
					$this->_assignCarrier();
					$this->_assignPayment();
					//assign some informations to display cart
					$this->_assignSummaryInformations();
					$this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
				}
	
				break;
	
			case 3:
				$this->block_medioP_sobre_costo();
				$this->disableMediosP();
				$this->show_contra_entrega();
				// Check that the conditions (so active) were accepted by the customer
				$cgv = Tools::getValue('cgv') || $this->context->cookie->check_cgv;
				if (Configuration::get('PS_CONDITIONS') && (!Validate::isBool($cgv) || $cgv == false))
					Tools::redirect('index.php?controller=order&step=2&paso=pagos');
				Context::getContext()->cookie->check_cgv = true;
				// Check the delivery option is set
				if (!$this->context->cart->isVirtualCart())
				{
					if (!Tools::getValue('delivery_option') && !Tools::getValue('id_carrier') && !$this->context->cart->delivery_option && !$this->context->cart->id_carrier)
						Tools::redirect('index.php?controller=order&step=2&paso=pagos');
					elseif (!Tools::getValue('id_carrier') && !$this->context->cart->id_carrier)
					{
						$deliveries_options = Tools::getValue('delivery_option');
						if (!$deliveries_options) {
							$deliveries_options = $this->context->cart->delivery_option;
						}
						foreach ($deliveries_options as $delivery_option)
						if (empty($delivery_option))
							Tools::redirect('index.php?controller=order&step=2&paso=pagos');
					}
				}
				$this->autoStep();
				// Bypass payment step if total is 0
				if (($id_order = $this->_checkFreeOrder()) && $id_order)
				{
					if ($this->context->customer->is_guest)
					{
						$order = new Order((int)$id_order);
						$email = $this->context->customer->email;
						$this->context->customer->mylogout(); // If guest we clear the cookie for security reason
						Tools::redirect('index.php?controller=guest-tracking&id_order='.urlencode($order->reference).'&email='.urlencode($email));
					}
					else
						Tools::redirect('index.php?controller=history');
				}
				$this->_assignPayment();
				// assign some informations to display cart
				$this->_assignSummaryInformations();
				$this->setTemplate(_PS_THEME_DIR_.'order-payment.tpl');
                                
           
			break;

			default:
				$this->_assignSummaryInformations();
				$this->setTemplate(_PS_THEME_DIR_.'shopping-cart.tpl');
			break;
		}

		$this->context->smarty->assign(array(
			'currencySign' => $this->context->currency->sign,
			'currencyRate' => $this->context->currency->conversion_rate,
			'currencyFormat' => $this->context->currency->format,
			'currencyBlank' => $this->context->currency->blank,
		));
              
	}
	/**
	 * Envío Express
	 */
	private function valorEnvioExpress($idAddress)
	{
		$valorCompra=$this->context->cart->getOrderTotal(true,Cart::ONLY_PRODUCTS);//obtiene el total de los productos
		$valor = $this->context->cart->valorExpress($idAddress, $valorCompra);
		return $valor;
	
	}
	private function express()
	{
		$this->context->cart->express = true;
		$this->context->cart->costoExpress = $this->valorEnvioExpress($this->context->cart->id_address_delivery);
	
		/*if($a)
		 {
		die('{"express" : true, "valor" : '.$a.'}');
		}
		else
		{
		$this->errors[] = "Envío express no disponible en este momento";
		}
		if ($this->errors)
		{
		if (Tools::getValue('ajax'))
			die('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
		$this->step = 1;
		}*/
	}
	
	public function processAddress()
	{
		if (Tools::getValue('valorExpress')){
			Context::getContext()->cookie->check_xps = true;
			if (Tools::getValue('checked'))
			{
				Context::getContext()->cookie->check_xps = false;
			}
			die('{"express" : true, "valor" : 1}');
		}
		if (Tools::getValue('express')){
			$id= Tools::getValue('id_address');
			$a=$this->valorEnvioExpress($id);
			die('{"express" : true, "valor" : '.$a.'}');
		}
		if (!Tools::getValue('multi-shipping'))
			$this->context->cart->setNoMultishipping();
		
		$same = Tools::isSubmit('same');
		if(!Tools::getValue('id_address_invoice', false) && !$same)
			$same = true;

		if (!Customer::customerHasAddress($this->context->customer->id, (int)Tools::getValue('id_address_delivery'))
			|| (!$same && Tools::getValue('id_address_delivery') != Tools::getValue('id_address_invoice')
				&& !Customer::customerHasAddress($this->context->customer->id, (int)Tools::getValue('id_address_invoice'))))
			$this->errors[] = Tools::displayError('Invalid address', !Tools::getValue('ajax'));
		else
		{
			$this->context->cart->id_address_delivery = (int)Tools::getValue('id_address_delivery');
			$this->context->cart->id_address_invoice = $same ? $this->context->cart->id_address_delivery : (int)Tools::getValue('id_address_invoice');
			
			CartRule::autoRemoveFromCart($this->context);
			CartRule::autoAddToCart($this->context);
			
			if (!$this->context->cart->update())
				$this->errors[] = Tools::displayError('An error occurred while updating your cart.', !Tools::getValue('ajax'));

			if (!$this->context->cart->isMultiAddressDelivery())
				$this->context->cart->setNoMultishipping(); // If there is only one delivery address, set each delivery address lines with the main delivery address

			if (Tools::isSubmit('message'))
				$this->_updateMessage(Tools::getValue('message'));
	
			// Add checking for all addresses
			$address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers();
			if (count($address_without_carriers) && !$this->context->cart->isVirtualCart())
			{
				if (count($address_without_carriers) > 1)
					$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to some addresses you selected.', !Tools::getValue('ajax')));
				elseif ($this->context->cart->isMultiAddressDelivery())
					$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to one of the address you selected.', !Tools::getValue('ajax')));
				else
					$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to the address you selected.', !Tools::getValue('ajax')));
			}
		}
		if ($this->errors)
		{
			if (Tools::getValue('ajax'))
				die('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
			$this->step = 1;
		}

		if ($this->ajax)
			die(true);
	}

}
