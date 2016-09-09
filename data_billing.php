<?php

require(dirname(__FILE__).'/config/config.inc.php');

$action = $_POST['action'];

switch ( $action ) {
	case 'insertDataBillingCustomer':

$firstname = '';
$lastname = '';

if ( isset(Context::getContext()->customer->firstname) ) {
	$firstname = Context::getContext()->customer->firstname;
}

if ( isset(Context::getContext()->customer->lastname) ) {
	$lastname = Context::getContext()->customer->lastname;	
} else {
		$lastname = ' ';
	}
$pasa = '' ;
		if ( $_POST['postcode'] != "" ) {
			// // echo " paso 1";
			$address = new Address();
			$address->id_customer = $_POST['id_customer'];
			$address->id_country = $_POST['id_country'];
			$address->id_state = $_POST['estado'];
			$address->lastname = $lastname;
			$address->firstname = $firstname;
			$address->address1 = $_POST['direccion'];
			$address->address2 = $_POST['complemento'];
			$address->city = $_POST['ciudad'];
			$address->id_colonia = $_POST['id_colonia'];
			$address->phone = $_POST['fijo'];
			$address->phone_mobile = $_POST['movil'];
			$address->postcode = $_POST['postcode'];
			//$address->dni = $_POST['number_document'];
			$address->alias = "Dirección ".($_POST['num_Address']+1);
			if ( !$address->add() ) {
				echo false;
			} else {

				$query = new DbQuery();
				$query->select('MAX(id_address) AS last_id');
				$query->from('address');
				$query->where('id_customer = '.$_POST['id_customer'].' AND is_rfc = 0 ');
				
				$response = Db::getInstance()->executeS($query);
				
				$pasa .= '|add:'.$response[0]['last_id'];
			}
		}

		if ( $_POST['data_rfc'] == "true" ) {
			// // echo " paso 2";

			$arr_nom_city = City::getCityByIdCity($_POST['rfc_id_city']);


			if ( $_POST['rfc_id_address'] != "" ) {
			
				$address_rfc = new Address( $_POST['rfc_id_address'] );
				$address_rfc->id_customer = $_POST['id_customer'];
				$address_rfc->id_country = $_POST['id_country'];
				$address_rfc->id_state = $_POST['rfc_estado'];
				$address_rfc->lastname = $lastname;
				$address_rfc->firstname = $firstname;
				$address_rfc->address1 = $_POST['rfc_address'];
				$address_rfc->address2 = '';
				//$address_rfc->city = $arr_nom_city[0]['city_name'];
				$address_rfc->city = $_POST['rfc_id_city'];
				$address_rfc->id_colonia = $_POST['rfc_id_colonia'];
				$address_rfc->phone = $_POST['rfc_phone'];
				//$address_rfc->phone_mobile = $_POST['phone_mobileRFC'];
				$address_rfc->postcode = $_POST['rfc_postcode'];
				$address_rfc->dni = $_POST['rfc'];
				$address_rfc->alias = $_POST["rfc_name"];
				$address_rfc->is_rfc = "1";
				
				if ( !$address_rfc->update() ) {
					echo false;
				} else {
					
					$pasa .= '|rfc:'.$_POST['rfc_id_address'];
				}

			} else {
				// // echo " paso 3";
				$address_rfc = new Address();
				$address_rfc->id_customer = $_POST['id_customer'];
				$address_rfc->id_country = $_POST['id_country'];
				$address_rfc->id_state = $_POST['rfc_estado'];
				$address_rfc->lastname = $lastname;
				$address_rfc->firstname = $firstname;
				$address_rfc->address1 = $_POST['rfc_address'];
				$address_rfc->address2 = '';
				//$address_rfc->city = $arr_nom_city[0]['city_name'];
				$address_rfc->city = $_POST['rfc_id_city'];
				$address_rfc->id_colonia = $_POST['rfc_id_colonia'];
				$address_rfc->phone = $_POST['rfc_phone'];
				//$address_rfc->phone_mobile = $_POST['phone_mobileRFC'];
				$address_rfc->postcode = $_POST['rfc_postcode'];
				$address_rfc->dni = $_POST['rfc'];
				$address_rfc->alias = $_POST["rfc_name"];
				$address_rfc->is_rfc = "1";

				if ( !$address_rfc->add() ) {
					echo false;
				} else {
					$Id_address=Db::getInstance()->Insert_ID(); 
					$pasa .= '|rfc:'.$Id_address;
				}
			}			
		}
		// // echo " paso 4";
		// // exit;

			echo $pasa;		

		break;
	
	default:
		echo '';
		break;
}
