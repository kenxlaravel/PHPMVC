<?php

session_start();
require "../include/config.php";

//Variables
$errors = array();
$objUser = new User();
$ObjShoppingCart = Cart::getFromSession(FALSE);

unset($_SESSION['errors']['admin_error']);

//Clear out notices we don't want shown on this page
$clear_notices = array('passwordreset', 'guestlogin', 'accountcreated');

foreach ($clear_notices as $notice) {
	$key = array_search($notice, $_SESSION['notices']);
	$_SESSION['notices'][$key] = NULL;
}

$admin_checkout = new Page('admin-checkout');

// If the user should not be on this page, throw them out
if ( $_SESSION['CID'] == '' ) {
	header("Location: /");
	exit;
}

if ( $_SESSION['UserType'] == 'G' || $_SESSION['UserType'] == '' ) {
	header("Location:/");
	exit;
}

$checkout = new Page('checkout');

if ( $objUser->checkAdmin() ) {

	//Flush out old admin session data, and grab admin data again
	$objUser->clearAdmin();
	$objUser->isAdmin();

	$ref_url = $_SERVER['REQUEST_URI'];
	$parsed_ref_url = parse_url($ref_url);
	$query = $parsed_ref_url['query'];

	if (!empty($query)) {
		$query = '?' . $query;
	}


	if ( isset($_POST['submit']) ) {

		// Listing of possible error flags:
			// $errors[0] = No customer email entered
			// $errors[1] = No new email entered
			// $errors[2] =
			// $errors[3] = Nothing selected
			// $errors[4] = No customer exists with those credentials
			// $errors[5] = The guest account could not be created
			// $errors[6] = That customer account already exists

		switch ($_POST['admincheckouttype']) {

			//If they would like to check out as an existing customer
			case "customer":
				//Validate the email field
				if (isset($_POST['admincheckout-customer-email']) AND $_POST['admincheckout-customer-email'] != "") {
					//If we find a user with that account name
					 if ($objUser->getIdByCustomer($_POST['admincheckout-customer-email']) !== NULL) {

					 	//Authorize the admin with the user's information
						$_SESSION['adminID'] = $objUser->getIdByCustomer($_POST['admincheckout-customer-email']);

						// Assign the appropriate customer id to the cart
						 if($ObjShoppingCart instanceof Cart){
							 $ObjShoppingCart->setCustomerId($_SESSION['adminID']);
						 }

						if (!$objUser->loginAs()) {
							$_SESSION['errors']['admin_error'] = array('4' => true);
						}
						//Send them to checkout as the authenticated existing user
						header("Location:".$checkout->getUrl().$query);
						exit;

					} else {
						$_SESSION['errors']['admin_error'] = array('4' => true);
					}
				} else {
					$_SESSION['errors']['admin_error'] = array( '0' => true);
				}

			break;

			//If they would like to check out as a new user
			case "new":
				//Validate the email field
				if (isset($_POST['admincheckout-new-email']) AND $_POST['admincheckout-new-email'] != "") {

					//If they want a new account made
					if (isset($_POST['admincheckout-new-create'])) {
						if (!$objUser->adminUserRegister($_POST['admincheckout-new-email'])) {
							$_SESSION['errors']['admin_error'] = array('6' => true);
						}
					//If they want to use a guest account
					} else {
						if (!$objUser->adminGuest()) {
							$_SESSION['errors']['admin_error'] = array('5' => true);
						}
					}
				} else {
					$_SESSION['errors']['admin_error'] = array('1' => true);
				}
			break;

			//If they would like to check out as themselves
			case "regular":

				$_SESSION['adminID'] = $_SESSION['CID'];
				$_SESSION['adminAccount'] = $_SESSION['Useremail'];

				// Assign admin's customer id to the cart
				if($ObjShoppingCart instanceof Cart){

					$ObjShoppingCart->setCustomerId($_SESSION['adminID']);
				}

				header("Location:".$checkout->getUrl().$query);
				exit;

			break;

			//Anything else
			default:
				$_SESSION['errors']['admin_error'] = array('3' => true);

			break;
		}
	}

 	header("Location: ".$admin_checkout->getUrl().$query);
 	exit;

}
