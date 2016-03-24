<?php

session_start();
// Allow the script 3 minutes of running time.
set_time_limit(180);
ini_set('memory_limit', '512M');

require "../include/config.php";

// Postdata and variables >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	// Pull all posted data
	$customerId = $_SESSION['CID'];
	$cartName   = ( !empty($_POST['cartName']) ? $_POST['cartName'] : NULL );
	$cartNotes   = ( !empty($_POST['cartNotes']) ? $_POST['cartNotes'] : NULL );
	$add        = ( $_POST['action'] == 'add'  ? TRUE : FALSE );
	$replace    = ( $_POST['action'] == 'replace' ? TRUE : FALSE );

	$currentCart = Cart::getFromSession(TRUE);

	if (!$cartName) {

		$success = FALSE;
	}

	// By defafult, there is no cart name conflict
	$nameConflict = FALSE;

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



// Action >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	// Check to see if we have a cart name conflict for this customer
	$conflictedCart = Cart::getSavedCart($cartName, $customerId);

	// If the return was a cart instance, there is a name conflict. Otherwise there was no cart by that name
	$nameConflict = ($conflictedCart instanceof Cart ? TRUE : FALSE);


	// If there was no name conflict, instantiate the new cart, set it to saved, and copy our products into it
	if (!$nameConflict) {

		// Create our new cart and set it to saved
		$savedCart = new Cart();
		$savedCart->setSaved();


		// Copy the products to the saved cart
		$success = $currentCart->copyProducts($savedCart, false, NULL);

		if($success){

			$savedCart->setName($cartName);

			(!empty($cartNotes) ? $savedCart->setNote($cartNotes) : NULL);
		}

	// If there was a name conflict and the user chose to merge, copy the products anyway
	} else if ($nameConflict && $add) {

		// Copy the products to the conflicted
		$success = $currentCart->copyProducts($conflictedCart, false, NULL);

		if($success){

			(!empty($cartNotes) ? $conflictedCart->setNote($cartNotes) : NULL);
		}


	// If there was a name conflict and the user chose to overwrite, empty the cart and then copy the products
	} else if ($nameConflict && $replace) {

		$conflictedCart->emptyProducts();
		$success = $currentCart->copyProducts($conflictedCart, false, null);

		if($success){
			(!empty($cartNotes) ? $conflictedCart->setNote($cartNotes) : NULL);
		}

	// Otherwise, we were unsuccessful
	} else {
		$success = FALSE;
	}

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



// Response >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	// Form our response array
	$response = array('success'      => $success,
					  'nameConflict' => $nameConflict);

	//Get account URL if save was successful
	if ($success) {

		$accountUrl = array("accountUrl" => Page::getPageUrlFromNickname('my-account'));
		$response = array_merge($response, $accountUrl);

	}

	// Output the JSON response
	header("Content-Type: application/json");
	echo json_encode($response);

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<