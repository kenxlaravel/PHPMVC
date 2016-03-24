<?php

// Include our DB connection class
require_once ("../include/classes/connection.php");

// Include our PayPal functions
require_once("../include/classes/class_PayPalFunctions.inc.php");
require_once("../include/classes/class_Checkout.inc.php");

// Include the page class
require_once ("../include/classes/class_Page.inc.php");

// Include our validation class
require_once("../include/classes/class_Validate.inc.php");

// Include our shopping cart classes
require_once("../include/classes/class_Cart.inc.php");
require_once("../include/classes/class_CartProduct.inc.php");
require_once("../include/classes/class_CartProductStock.inc.php");
require_once("../include/classes/class_CartProductFlash.inc.php");
require_once("../include/classes/class_CartProductStreetname.inc.php");
require_once("../include/classes/class_CartProductBuilder.inc.php");

// Include our session class
require_once("../include/classes/class_Session.inc.php");

// Instantiate a shopping cart object so we can get the order total
$ObjShoppingCart = Cart::getFromSession(FALSE);

// Instantiate a user session
$objSession = new Session();

// Pass the form data into our checkout object for validation
$checkout = new Checkout($_REQUEST);

// The returnURL is the location where buyers return to when a payment has been succesfully authorized.
$returnURL = URL_PREFIX_HTTPS . "/checkout_confirm.php" . (mb_strtolower($_REQUEST['layout']) == 'paypal' ? '?layout=paypal' : '');

// The cancelURL is the location buyers are sent to when they hit the cancel button during authorization of payment during the PayPal flow
$cancelURL = URL_PREFIX_HTTPS . "/checkout_error.php" . (mb_strtolower($_REQUEST['layout']) == 'paypal' ? '?layout=paypal' : '');


// Form retention
if (!empty($_REQUEST)) {

	$_SESSION['checkout_form']['email'] = $_REQUEST['email'];
	$_SESSION['checkout_form']['company'] = $_REQUEST['company'];
	$_SESSION['checkout_form']['firstname'] = $_REQUEST['firstname'];
	$_SESSION['checkout_form']['lastname'] = $_REQUEST['lastname'];
	$_SESSION['checkout_form']['phonenumber'] = $_REQUEST['phonenumber'];
	$_SESSION['checkout_form']['billfaxnumber'] = $_REQUEST['billfaxnumber'];
	$_SESSION['checkout_form']['address1'] = $_REQUEST['address1'];
	$_SESSION['checkout_form']['address2'] = $_REQUEST['address2'];
	$_SESSION['checkout_form']['city2'] = $_REQUEST['city2'];
	$_SESSION['checkout_form']['state'] = $_REQUEST['state'];
	$_SESSION['checkout_form']['zipcode'] = $_REQUEST['zipcode'];
	$_SESSION['checkout_form']['country'] = $_REQUEST['country'];
	$_SESSION['checkout_form']['shipcompany'] = $_REQUEST['shipcompany'];
	$_SESSION['checkout_form']['shipfirstname'] = $_REQUEST['shipfirstname'];
	$_SESSION['checkout_form']['shiplastname'] = $_REQUEST['shiplastname'];
	$_SESSION['checkout_form']['shipphonenumber'] = $_REQUEST['shipphonenumber'];
	$_SESSION['checkout_form']['shipaddress1'] = $_REQUEST['shipaddress1'];
	$_SESSION['checkout_form']['shipaddress2'] = $_REQUEST['shipaddress2'];
	$_SESSION['checkout_form']['shipcity'] = $_REQUEST['shipcity'];
	$_SESSION['checkout_form']['sstate'] = $_REQUEST['sstate'];
	$_SESSION['checkout_form']['shipzip'] = $_REQUEST['shipzip'];
	$_SESSION['checkout_form']['shipcountry'] = $_REQUEST['shipcountry'];
	$_SESSION['checkout_form']['payment'] = $_REQUEST['payment'];
	$_SESSION['checkout_form']['shippingmethod'] = $_REQUEST['shippingmethod'];
	$_SESSION['checkout_form']['expediated-shipping'] = $_REQUEST['expediated-shipping'];
	$_SESSION['checkout_form']['shipping-account'] = $_REQUEST['shipping-account'];
	$_SESSION['checkout_form']['applied-shipping-account'] = $_REQUEST['applied-shipping-account'];
	$_SESSION['checkout_form']['couponcode'] = $_REQUEST['couponcode'];
	$_SESSION['checkout_form']['purchase_order'] = $_REQUEST['purchase_order'];
	$_SESSION['checkout_form']['tag_job'] = $_REQUEST['tag_job'];
	$_SESSION['checkout_form']['tax_exempt_status'] = $_REQUEST['tax_exempt_status'];
	$_SESSION['checkout_form']['special_comments'] = $_REQUEST['special_comments'];
	$_SESSION['checkout_form']['shipping_method'] = $_REQUEST['shipping_method'];
	$_SESSION['checkout_form']['shipping_carrier'] = $_REQUEST['shipping_carrier'];
	$_SESSION['checkout_form']['coupon_rate'] = $_REQUEST['coupon_rate'];
	$_SESSION['checkout_form']['sub_total'] = $_REQUEST['sub_total'];
	$_SESSION['checkout_form']['submit.x'] = $_REQUEST['submit.x'];
	$_SESSION['checkout_form']['submit.y'] = $_REQUEST['submit.y'];

}


// Grab an array of any errors that occur
$errors = $checkout->validateCheckoutForm();

// If we have no errors, continue
if (empty($errors['error_msg'])) {

	// We deal in USD
	$currencyCodeType = "USD";

	// Set to 'Authorization' for authorization only, or 'Sale' for authorize and capture.
	$paymentType = 'Authorization';

	// Shipping details
	$shipToName = $_REQUEST['shipfirstname'] . ' ' . $_REQUEST['shiplastname'];
	$shipToStreet = $_REQUEST['shipaddress1'];
	$shipToStreet2 = $_REQUEST['shipaddress2']; //Leave it blank if there is no value
	$shipToCity = $_REQUEST['shipcity'];
	$shipToState = $_REQUEST['sstate'];
	$shipToCountryCode = $_REQUEST['shipcountry']; // Please refer to the PayPal country codes in the API documentation
	$shipToZip = $_REQUEST['shipzip'];
	$phoneNum = $_REQUEST['shipphonenumber'];

	// Update the user session with the newest prices from the cart
	$objSession->updateDatabase();

	// Grab the user's session data from bs_sessions
	$session_data = $objSession->getSession();

	// Get an array of everything in the user's shopping cart
	$cart_items = $ObjShoppingCart->products;

	// Get the subtotal
	$subtotal = $ObjShoppingCart->getSubtotal();

	// If there's a coupon code, subtract it from the subtotal
	if (!empty($session_data['coupon_value'])) {
		$subtotal -= $session_data['coupon_value'];
	}

	// Set a session var to keep track of the order total (required for PayPal)
	$_SESSION['Payment_Amount'] = $session_data['invoice_total'];

	// Make our API call to PayPal
	$resArray = CallMarkExpressCheckout ($cart_items, $subtotal, $session_data['sales_tax'], $session_data['shipping_charges'], $session_data['invoice_total'], $session_data['coupon_number'], $session_data['coupon_value'],
										 $currencyCodeType, $paymentType, $returnURL,
										 $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState,
										 $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum
	);

	// Grab the acknowledgement from PayPal
	$ack = strtoupper($resArray["ACK"]);

	// Check if we got a success back
	if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING") {

		// Decode our authorization token from PayPal
		$token = urldecode($resArray["TOKEN"]);

		// Store the token in the user's session
		$_SESSION['reshash'] = $token;

		// Grab all the postdata from the form and keep it in the user's session
		$_SESSION['formdata'] = $_POST;

		// Redirect them to paypal (within the lightbox)
		RedirectToPayPal ( $token );

	// Failure case
	} else {

		//Display a user friendly Error on the page using any of the following error information returned by PayPal
		$ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
		$ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
		$ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
		$ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

		// Store the error in the user's session so we can display it on the error page
		$_SESSION['paypal_error'] = $ErrorCode;
		$_SESSION['paypal_error_short'] = $ErrorShortMsg;
		$_SESSION['paypal_error_long'] = $ErrorLongMsg;
		$_SESSION['paypal_error_severity_code'] = $ErrorSeverityCode;

		// Redirect the user to the checkout error page so we can display an error message
		header("Location: " . $cancelURL);
		die();

	}


} else {

	$_SESSION['errors'][] = $errors['error_msg'];

	// Redirect the user to the checkout error page so we can display an error message
	header("Location: " . $cancelURL);
	die();

}

?>