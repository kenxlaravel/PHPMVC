<?php
session_start();
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// This file handles form submissions from the my-account page as a PRG pattern
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//The stuff we all know and love
	require_once '../include/config.php';

	//Instantiate needed classes
	$ObjOrder = new Orders();
	//$ObjProduct = new ProductPage($page);
    $objUser = new User();
	$ObjCustomProduct=new CustomProduct();

	$objShoppingcartinfo = Cart::getFromSession(TRUE);

	//Set the customer's information
	$CID = $_SESSION['CID'];
	$customer_data = $objUser->GetCustomerById($CID);
	$order = $ObjOrder->GetCustomerOrderList($CID, 5);
	$order_total = $ObjOrder->getOrderCount($CID);
	$ObjAddresses = new Addresses($CID);

	//Check if the customer was saving a design
	if (isset($_SESSION['cpi']) && $_GET['action']=="save") {
		$ObjCustomProduct->saveCustomDesign();
	}

	//Check if the customer was deleting a saved design
	if (isset($_REQUEST['custom_product_id']) && $_REQUEST['custom_product_id'] && $_REQUEST['custom_product']=="Delete Design") {
		$ObjCustomProduct->deleteCustomDesign();
	}

	//Check if the customer changed their username
	if (isset($_POST['change_email']) && $_POST['change_email'] == 'Update Email Address') {
		$objUser->changeUsername($_POST);
	}

	//Check if the customer changed their password
	if (isset($_POST['change_password']) && $_POST['change_password'] == 'Update Password') {
		$objUser->changePassword($_POST);
	}

	//Check if the customer is updating user type & passwords
	if (isset($_POST['update']) && $_POST['update'] == 'Register') {
		$objUser->updateUser($_POST);
	}

	//Check if the customer is adding/updating an address
	if (isset($_POST['address']) && $_POST['address'] == 'modify') {
		$ObjAddresses->modifyAddress($_POST);
	}

	//Check if the customer is deleting an address
	if (isset($_REQUEST['delete-address'])) {
		$ObjAddresses->deleteAddress($_REQUEST['delete-address']);
	}

	//Check if the customer is setting a default address
	if (isset($_POST['quick-change']) && $_POST['quick-change'] == 1) {

		$success = $ObjAddresses->setDefaultAddress($_POST);

		//Check if the update was successful, and set our error message accordingly
		if ($success) {
			$error = null;
		} else {
			$error = "Your address could not be set as a default; an unknown error was encountered.";
		}

		//If this is NOT an AJAX request, set a session notice and redirect
		if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {

			if ($error == NULL) {
				$_SESSION['successes'][] = "Your address has been set as a default successfully.";
			} else {
				$_SESSION['errors'][] = $error;
			}

			//Redirect them
			header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
			header("Location: ".$_SERVER['REQUEST_URI']);
			exit;

		//Otherwise, this IS an AJAX request and we'll just return some JSON
		} else {

			//Form a JSON response
			$response = array('defaultshippingid' => $_POST['default_shipping'],
							  'defaultbillingid' => $_POST['default_billing']);

			if ($error != NULL) {
				$response['errors'] = array($error);
			}

			//Output the JSON
			header("Content-Type: application/json");
			echo json_encode($response);
			die();
		}
	}

	//Check if the customer is linking a net30 account
	if (isset($_POST['link_net30']) && $_POST['link_net30'] == 'Add Account') {
		$objUser->linkNet30($_POST);
	}

	//Check if the customer is unlinking a net30 account
	if (isset($_POST['unlink_net30']) && $_POST['unlink_net30'] == 'Unlink Net30') {
		$objUser->unlinkNet30();
	}