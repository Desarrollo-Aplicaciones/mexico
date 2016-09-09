<?php

	if ( isset($_POST) && !empty($_POST) && isset($_POST['action']) && !empty($_POST['action']) ) {

		include_once dirname(__FILE__).'/classes/classProgressiveDiscounts.php';

		$classProgressiveDiscounts = new classProgressiveDiscounts();

		switch ( $_POST['action'] ) {
			case 'search_coupon':
				$itemCoupon = $classProgressiveDiscounts->search_coupon($_POST['search']);
				die( json_encode($itemCoupon) );
				break;

			case 'search_product':
				$itemProduct = $classProgressiveDiscounts->search_product($_POST['search']);
				die( json_encode($itemProduct) );
				break;

			case 'add_new_progressive_discount':
				$add_new = $classProgressiveDiscounts->add_new_progressive_discount($_POST['name'], $_POST['description'], $_POST['frequency'], $_POST['periods'], $_POST['limit_shopping_customer'], $_POST['reset'], $_POST['cycles'], $_POST['state'], $_POST['states_orders'], $_POST['list_cart_rules'], $_POST['list_products']);
				die( json_encode($add_new) );
				break;

			case 'view_detail_progressive_discount':
				$detail = $classProgressiveDiscounts->view_detail_progressive_discount($_POST['idProgressiveDiscount']);
				die( $detail );
				break;

			case 'changeStatus':
				$state = $classProgressiveDiscounts->changeStatus($_POST['idprogressivediscount'], $_POST['newstate']);
				die( json_encode($state) );
				break;

			case 'search_states_orders':
				$listStates = $classProgressiveDiscounts->search_states_orders();
				die( $listStates );
				break;
			
			default:
				die(0);
				break;
		}
	} else {
		die(0);
	}
?>