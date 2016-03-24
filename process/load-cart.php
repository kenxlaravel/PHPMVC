<?php

    session_start();

	// Allow the script 3 minutes of running time.
	set_time_limit(180);

	ini_set('memory_limit', '512M');
    require "../include/config.php";

// Postdata and variables >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	//Pull all posted data
	$mergeItems   = ($_POST['action'] == 'add' ? TRUE : FALSE);
	$replaceItems = ($_POST['action'] == 'replace' ? TRUE : FALSE);
	$cartHash     = $_POST['idHash'];

	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


	// Action >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	$currentCart = Cart::getFromSession(TRUE);

	//Check if there are items in customer's current cart
	$itemsInCart = ($currentCart->getLineItemCount() > 0 ? TRUE : FALSE);

	// Give customer benefit of the doubt
	$authenticCustomer = TRUE;

	// If there are no items in the current cart, just copy the saved cart into it
	if (!$itemsInCart) {

		$cartToLoad = new Cart($cartHash);

		// Check if customer is authentic in his/her intentions
		$authenticCustomer = ($cartToLoad->customerId == (int) $_SESSION['CID'] ? TRUE : FALSE);

		if ( $authenticCustomer ) {

			$success = $cartToLoad->copyProducts($currentCart, false, NULL);
		}

		// If there are items in the current cart but the user chose to merge, run our copy function
	} else if($itemsInCart && $mergeItems) {

		$cartToLoad = new Cart($cartHash);

		// Check if customer is authentic in his/her intentions
		$authenticCustomer = ($cartToLoad->customerId == $_SESSION['CID'] ? TRUE : FALSE);

		if ( $authenticCustomer ) {

			$success = $cartToLoad->copyProducts($currentCart, false, null);

		}

		// If there are items in the current cart but the user chose to replace,
		// empty the current cart and then copy into it
	} else if ($itemsInCart && $replaceItems) {

		if($currentCart->emptyProducts()){

			$cartToLoad = new Cart($cartHash);

			// Check if customer is authentic in his/her intentions
			$authenticCustomer = ($cartToLoad->customerId == $_SESSION['CID'] ? TRUE : FALSE);

			if ( $authenticCustomer ) {

				$success = $cartToLoad->copyProducts($currentCart, false, null);

			}

		} else {

			$success = FALSE;
		}

		// Otherwise, we are unsuccessful
	} else {

		$success = FALSE;
	}

	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



	// Response >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	if( $authenticCustomer ){
		$response = array('loaded'     => $success,
						  'itemsInCart' => $itemsInCart,
						  'cartUrl' => Page::getPageUrlFromNickname('cart'));
	} else {
		$response = FALSE;
	}

	//Output the JSON
	header("Content-Type: application/json");
	echo json_encode($response);

	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<