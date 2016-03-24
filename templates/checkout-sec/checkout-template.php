<?php

$paypal = TRUE;
$sids   = isset($sid) ? $sid : NULL;

set_time_limit(90);

// Clear out any coupon stuff lingering in the session if this is not the checkout page submitting to itself
if( isset($_POST['sub_total']) && !($_POST['sub_total'] > 0) ) {

    $ObjSession->unsetSessionCouponData();
}

// Check to make sure there is a cart with at least one item in it. Otherwise, redirect back to the cart
// So the user sees the empty cart message
if( !($ObjShoppingCart instanceof Cart) || ($ObjShoppingCart->getTotalQuantity() <= 0) ) {

    // Intantiate a cart page
    // Redirect to the cart
    header($_SERVER['SERVER_PROTOCOL'].' 302 Found', TRUE, 302);
    header('Location: '.$cart->getUrl());

    exit;
}

//Clear out notices we don't want shown on this page
$clear_notices = array ('passwordreset', 'guestlogin', 'accountcreated');

//clear session shipping values if not submitting order
if( isset($_REQUEST['submit']) && $_REQUEST['submit'] != "Place Order" ) {

    $ObjShippingCharges->emptySessionShippingDetails();
}

$shipping_rate = $ObjShippingCharges->getShippingChargesBySession();

//Get special messages for freight
$message = $ObjShoppingCart->getMessage();

if( ($objUser->checkAdmin() && $_SESSION['adminID'] != $_SESSION['CID']) || (!empty($message['freight_item'])) ) {

    $show_paypal = FALSE;

}else{

    $show_paypal = TRUE;
}

foreach ($clear_notices as $notice) {

    $key = array_search($notice, $_SESSION['notices']);

    $_SESSION['notices'][$key] = NULL;
}

//date calculation for freight shipment
$dates          = $ObjShoppingCart->getEstimatedDate();
$esitmated_date = strtotime("+5 days", strtotime($dates['estimated_date']));
$frieght_date   = date("F jS, Y", $esitmated_date);

if( $objUser->checkAdmin() && $_POST['submit'] != "Place Order" &&
        mb_strpos($_SERVER['HTTP_REFERER'], '/admin-checkout') === FALSE &&
            mb_strpos($_SERVER['HTTP_REFERER'], 'checkout_error.php') === FALSE ) {

    $ref_url        = $_SERVER['REQUEST_URI'];
    $parsed_ref_url = parse_url($ref_url);
    $query          = $parsed_ref_url['query'];

    if( !empty($query) ) {

        $query = '?'.$query;
    }

    header("Location: ".$admin->getUrl().$query);
    die();
}

$cart_url     = $cart->getUrl();
$checkout_url = $checkout->getUrl();
$item_count   = $ObjShoppingCart->getTotalQuantity();

if( $item_count == 0 ) {

    $ObjShoppingCart->RedirectEmptyCard();
}

$CCGateWay->CreditCard();


if( isset($_SESSION['admin']) && $_SESSION['admin'] === TRUE && $_SESSION['adminID'] != $_SESSION['CID'] ) {

    $address_array = $ObjUserAddress->listAddresses($_SESSION['adminID']);
    $brimar_net = $objUser->getLinkedNet30($_SESSION['adminID']);

}else{

    $address_array = $ObjUserAddress->listAddresses();
    $brimar_net = $objUser->getLinkedNet30($_SESSION['CID']);
}


// Determine the payment method to default to on page load.
if( isset($_SESSION['checkout_form']['payment']) ) {

    $default_payment_method = $_SESSION['checkout_form']['payment'];

}elseif( !empty($_REQUEST['payment']) ){

    $default_payment_method = $_REQUEST['payment'];

}else{

    $default_payment_method = (empty($_REQUEST['credit_card_number']) && (!empty($brimar_net) ||
                                    !empty($_REQUEST['brimar_card_number']))) ? 'Brimar' : 'CreditCard';
}

// If the payment method is set to a type of credit card, default it to 'CreditCard'
if( $default_payment_method == 'Visa' || $default_payment_method == 'Amex' ||
    $default_payment_method == 'Mastercard' || $default_payment_method == 'Discover' ) {

    $default_payment_method = 'CreditCard';
}

//loop through each address for customer if more than one address
if( count($address_array) > 1 ) {

    foreach ($address_array as $key => $address_value) {

        if( $address_value['default_billing'] ) {

            //check if billing address has already been set via default
            if( empty($billing_array) ) {

                $billing_array['id']             = $address_value['public_id'];
                $billing_array['company']        = $address_value['company'];
                $billing_array['first_name']     = $address_value['first_name'];
                $billing_array['last_name']      = $address_value['last_name'];
                $billing_array['street_address'] = $address_value['street_address'];
                $billing_array['suburb']         = $address_value['suburb'];
                $billing_array['city']           = $address_value['city'];
                $billing_array['state']          = $address_value['state'];
                $billing_array['postcode']       = $address_value['postcode'];
                $billing_array['country']        = $address_value['country'];
                $billing_array['phone']          = $address_value['phone'];
                $billing_array['fax']            = $address_value['fax'];
            }
        }

        if( $address_value['default_shipping'] ) {

            //check if shipping address has already been set via default
            if( empty($shipping_array) ) {

                $shipping_array['id'] = $address_value['public_id'];
                $shipping_array['company'] = $address_value['company'];
                $shipping_array['first_name'] = $address_value['first_name'];
                $shipping_array['last_name'] = $address_value['last_name'];
                $shipping_array['street_address'] = $address_value['street_address'];
                $shipping_array['suburb'] = $address_value['suburb'];
                $shipping_array['city'] = $address_value['city'];
                $shipping_array['state'] = $address_value['state'];
                $shipping_array['postcode'] = $address_value['postcode'];
                $shipping_array['country'] = $address_value['country'];
                $shipping_array['phone'] = $address_value['phone'];

            }

        }

    }

}elseif( count($address_array) == 1 ){
    //make an array for billing & shipping address
    foreach ($address_array as $key => $address_value) {
        if( empty($billing_array) ) {
            $billing_array['id'] = $address_value['public_id'];
            $billing_array['company'] = $address_value['company'];
            $billing_array['first_name'] = $address_value['first_name'];
            $billing_array['last_name'] = $address_value['last_name'];
            $billing_array['street_address'] = $address_value['street_address'];
            $billing_array['suburb'] = $address_value['suburb'];
            $billing_array['city'] = $address_value['city'];
            $billing_array['state'] = $address_value['state'];
            $billing_array['postcode'] = $address_value['postcode'];
            $billing_array['country'] = $address_value['country'];
            $billing_array['phone'] = $address_value['phone'];
            $billing_array['fax'] = $address_value['fax'];
        }
        if( empty($shipping_array) ) {
            $shipping_array['id'] = $address_value['public_id'];
            $shipping_array['company'] = $address_value['company'];
            $shipping_array['first_name'] = $address_value['first_name'];
            $shipping_array['last_name'] = $address_value['last_name'];
            $shipping_array['street_address'] = $address_value['street_address'];
            $shipping_array['suburb'] = $address_value['suburb'];
            $shipping_array['city'] = $address_value['city'];
            $shipping_array['state'] = $address_value['state'];
            $shipping_array['postcode'] = $address_value['postcode'];
            $shipping_array['country'] = $address_value['country'];
            $shipping_array['phone'] = $address_value['phone'];
            $shipping_array['fax'] = $address_value['fax'];

        }

    }

}


if( !empty($shipping_array) ) {
    // Set our class variables now that we know where we need to ship to
    $ObjShippingCharges->setAddress1($shipping_array['street_address']);
    $ObjShippingCharges->setAddress2($shipping_array['suburb']);
    $ObjShippingCharges->setCity($shipping_array['city']);
    $ObjShippingCharges->setZipcode($shipping_array['postcode']);
    $ObjShippingCharges->setState($shipping_array['state']);
    $ObjShippingCharges->setCountry($shipping_array['country']);
}

$customer_sid = (isset($_SESSION['admin']) && $_SESSION['admin'] === TRUE && $_SESSION['adminID'] > 0) ? $_SESSION['adminID'] : $sids;



// The following logic is for form prefilling

	// email
	if( isset($_SESSION['checkout_form']['email']) ) {
        $prefill['email'] = $_SESSION['checkout_form']['email'];
    }elseif( isset($_SESSION['adminAccount']) ){
        $prefill['email'] = $_SESSION['adminAccount'];
    }elseif( isset($customer_info_data['customers_email']) ){
        $prefill['email'] = $customer_info_data['customers_email'];
    }else{
        $prefill['email'] = $_SESSION['Useremail'];
    }


	// Billing Company
	if( isset($_SESSION['checkout_form']['company']) ) {
        $prefill['billing']['company'] = $_SESSION['checkout_form']['company'];
    }elseif( $billing_array['company'] && (!isset($_REQUEST['company'])) ){
        $prefill['billing']['company'] = $billing_array['company'];
    }elseif( $_REQUEST['company'] ){
        $prefill['billing']['company'] = $_REQUEST['company'];
    }


	// Billing First name
	if( isset($_SESSION['checkout_form']['firstname']) ) {
        $prefill['billing']['firstname'] = $_SESSION['checkout_form']['firstname'];
    }elseif( $billing_array['first_name'] && (!isset($_REQUEST['firstname'])) ){
        $prefill['billing']['firstname'] = $billing_array['first_name'];
    }elseif( $_REQUEST['firstname'] ){
        $prefill['billing']['firstname'] = $_REQUEST['firstname'];
    }


	// Billing Last name
	if( isset($_SESSION['checkout_form']['lastname']) ) {
        $prefill['billing']['lastname'] = $_SESSION['checkout_form']['lastname'];
    }elseif( $billing_array['last_name'] && (!isset($_REQUEST['lastname'])) ){
        $prefill['billing']['lastname'] = $billing_array['last_name'];
    }elseif( $_REQUEST['lastname'] ){
        $prefill['billing']['lastname'] = $_REQUEST['lastname'];
    }


	// Billing phone
	if( isset($_SESSION['checkout_form']['phonenumber']) ) {
        $prefill['billing']['phone'] = $_SESSION['checkout_form']['phonenumber'];
    }elseif( $billing_array['phone'] && (!isset($_REQUEST['phonenumber'])) ){
        $prefill['billing']['phone'] = $billing_array['phone'];
    }elseif( $_REQUEST['phonenumber'] ){
        $prefill['billing']['phone'] = $_REQUEST['phonenumber'];
    }


	// Billing fax
	if( isset($_SESSION['checkout_form']['billfaxnumber']) ) {
        $prefill['billing']['fax'] = $_SESSION['checkout_form']['billfaxnumber'];
    }elseif( $billing_array['fax'] && (!isset($_REQUEST['billfaxnumber'])) ){
        $prefill['billing']['fax'] = $billing_array['fax'];
    }elseif( $_REQUEST['billfaxnumber'] ){
        $prefill['billing']['fax'] = $_REQUEST['billfaxnumber'];
    }


	// Billing address1
	if( isset($_SESSION['checkout_form']['address1']) ) {
        $prefill['billing']['address1'] = $_SESSION['checkout_form']['address1'];
    }elseif( $billing_array['street_address'] && (!isset($_REQUEST['address1'])) ){
        $prefill['billing']['address1'] = $billing_array['street_address'];
    }elseif( $_REQUEST['address1'] ){
        $prefill['billing']['address1'] = $_REQUEST['address1'];
    }


	// Billing address2
	if( isset($_SESSION['checkout_form']['address2']) ) {
        $prefill['billing']['address2'] = $_SESSION['checkout_form']['address2'];
    }elseif( $billing_array['suburb'] && (!isset($_REQUEST['address2'])) ){
        $prefill['billing']['address2'] = $billing_array['suburb'];
    }elseif( $_REQUEST['address2'] ){
        $prefill['billing']['address2'] = $_REQUEST['address2'];
    }


	// Billing city
	if( isset($_SESSION['checkout_form']['city2']) ) {
        $prefill['billing']['city'] = $_SESSION['checkout_form']['city2'];
    }elseif( $billing_array['city'] && (!isset($_REQUEST['city2'])) ){
        $prefill['billing']['city'] = $billing_array['city'];
    }elseif( $_REQUEST['city2'] ){
        $prefill['billing']['city'] = $_REQUEST['city2'];
    }


	// Billing zipcode
	if( isset($_SESSION['checkout_form']['zipcode']) ) {
        $prefill['billing']['zipcode'] = $_SESSION['checkout_form']['zipcode'];
    }elseif( $billing_array['postcode'] && (!isset($_REQUEST['zipcode'])) ){
        $prefill['billing']['zipcode'] = $billing_array['postcode'];
    }elseif( $_REQUEST['zipcode'] ){
        $prefill['billing']['zipcode'] = $_REQUEST['zipcode'];
    }


	// Shipping company
	if( isset($_SESSION['checkout_form']['shipcompany']) ) {
        $prefill['shipping']['company'] = $_SESSION['checkout_form']['shipcompany'];
    }elseif( $shipping_array['company'] && (!isset($_REQUEST['shipcompany'])) ){
        $prefill['shipping']['company'] = $shipping_array['company'];
    }elseif( $_REQUEST['shipcompany'] ){
        $prefill['shipping']['company'] = $_REQUEST['shipcompany'];
    }


	// Shipping first name
	if( isset($_SESSION['checkout_form']['shipfirstname']) ) {
        $prefill['shipping']['firstname'] = $_SESSION['checkout_form']['shipfirstname'];
    }elseif( $shipping_array['first_name'] && (!isset($_REQUEST['shipfirstname'])) ){
        $prefill['shipping']['firstname'] = $shipping_array['first_name'];
    }elseif( $_REQUEST['shipfirstname'] ){
        $prefill['shipping']['firstname'] = $_REQUEST['shipfirstname'];
    }


	// Shipping last name
	if( isset($_SESSION['checkout_form']['shiplastname']) ) {
        $prefill['shipping']['lastname'] = $_SESSION['checkout_form']['shiplastname'];
    }elseif( $shipping_array['last_name'] && (!isset($_REQUEST['shiplastname'])) ){
        $prefill['shipping']['lastname'] = $shipping_array['last_name'];
    }elseif( $_REQUEST['shiplastname'] ){
        $prefill['shipping']['lastname'] = $_REQUEST['shiplastname'];
    }


	// Shipping phone number
	if( isset($_SESSION['checkout_form']['shipphonenumber']) ) {
        $prefill['shipping']['phone'] = $_SESSION['checkout_form']['shipphonenumber'];
    }elseif( $shipping_array['phone'] && (!isset($_REQUEST['shipphonenumber'])) ){
        $prefill['shipping']['phone'] = $shipping_array['phone'];
    }elseif( $_REQUEST['shipphonenumber'] ){
        $prefill['shipping']['phone'] = $_REQUEST['shipphonenumber'];
    }


	// Shipping address1
	if( isset($_SESSION['checkout_form']['shipaddress1']) ) {
        $prefill['shipping']['address1'] = $_SESSION['checkout_form']['shipaddress1'];
    }elseif( $shipping_array['street_address'] && (!isset($_REQUEST['shipaddress1'])) ){
        $prefill['shipping']['address1'] = $shipping_array['street_address'];
    }elseif( $_REQUEST['shipaddress1'] ){
        $prefill['shipping']['address1'] = $_REQUEST['shipaddress1'];
    }


	// Shipping address2
	if( isset($_SESSION['checkout_form']['shipaddress2']) ) {
        $prefill['shipping']['address2'] = $_SESSION['checkout_form']['shipaddress2'];
    }elseif( $shipping_array['suburb'] && (!isset($_REQUEST['shipaddress2'])) ){
        $prefill['shipping']['address2'] = $shipping_array['suburb'];
    }elseif( $_REQUEST['shipaddress2'] ){
        $prefill['shipping']['address2'] = $_REQUEST['shipaddress2'];
    }


	// Shipping city
	if( isset($_SESSION['checkout_form']['shipcity']) ) {
        $prefill['shipping']['city'] = $_SESSION['checkout_form']['shipcity'];
    }elseif( $shipping_array['city'] && (!isset($_REQUEST['shipcity'])) ){
        $prefill['shipping']['city'] = $shipping_array['city'];
    }elseif( $_REQUEST['shipcity'] ){
        $prefill['shipping']['city'] = $_REQUEST['shipcity'];
    }


	// Shipping state
	if( !empty($_SESSION['checkout_form']['sstate']) ) {
        $prefill['shipping']['state'] = $_SESSION['checkout_form']['sstate'];
    }else if( !empty($_REQUEST['sstate']) ) {
        $prefill['shipping']['state'] = $_REQUEST['sstate'];
    }else if( !empty($shipping_array['state']) && isset($_REQUEST['sstate']) && $_REQUEST['sstate'] == '' ) {
        $prefill['shipping']['state'] = $shipping_array['state'];
    }


	// Shipping zipcode
	if( isset($_SESSION['checkout_form']['shipzip']) ) {
        $prefill['shipping']['zipcode'] = $_SESSION['checkout_form']['shipzip'];
    }elseif( $shipping_array['postcode'] && (!isset($_REQUEST['shipzip'])) ){
        $prefill['shipping']['zipcode'] = $shipping_array['postcode'];
    }elseif( $_REQUEST['shipzip'] ){
        $prefill['shipping']['zipcode'] = $_REQUEST['shipzip'];
    }


	// Expediated shipping comment
	if( isset($_SESSION['checkout_form']['expediated-shipping']) ) {

        $prefill['expediated-shipping'] = $_SESSION['checkout_form']['expediated-shipping'];

    }elseif( isset($_REQUEST['expediated-shipping']) && $_REQUEST['expediated-shipping'] ){

        $prefill['expediated-shipping'] = $_REQUEST['expediated-shipping'];
    }


	// Special comments
	if( isset($_SESSION['checkout_form']['special_comments']) ) {

        $prefill['special_comments'] = $_SESSION['checkout_form']['special_comments'];

    }else if( isset($_REQUEST['special_comments']) && $_REQUEST['special_comments'] != '' ) {

        $prefill['special_comments'] = $_REQUEST['special_comments'];
    }


	// Shipping account
	if( isset($_SESSION['checkout_form']['applied-shipping-account']) ) {

        $prefill['shipping-account'] = trim($_SESSION['checkout_form']['applied-shipping-account']);

    }else if( isset($_REQUEST['applied-shipping-account']) && mb_strlen(trim($_REQUEST['applied-shipping-account'])) > 0 || (isset($error_array['user_ship']) && $error_array['user_ship'] == TRUE) ) {

        $prefill['shipping-account'] = trim($_REQUEST['applied-shipping-account']);
    }


//Call the function for admins
$objUser->isAdmin();

if( isset($_POST['submit']) && $_POST['submit'] == "Place Order" ) {

    $objCheckout = new Checkout($_POST);
    $objOrder = new Orders();
    $objSession = new Session();
    // Validate the checkout form and grab any errors that were encountered
    $error_array = $objCheckout->validateCheckoutForm();
    // Grab the payment method the customer chose
    $payment_method = $objCheckout->payment_method;
    //If it's an admin, grab the user data for the user they are logging in as. Otherwise grab their own data
    if( $_SESSION['admin'] === TRUE && $_SESSION['adminID'] > 0 ) {
        $customer_info = $objUser->UserBillingInformation($_SESSION['adminID']);
    }else{
        $customer_info = $objUser->UserBillingInformation();
    }
    if( count($customer_info) > 0 ) {
        foreach ($customer_info as $key => $customer_info_data) {
        }
    }
    // Generate a unique order number
    $order_no = $objOrder->generateOrderNumber();
    // Keep the order number in the user's session so we can reference it again
    $_SESSION['order_no'] = $order_no;
    // Pass our form fields into the authorize.net class
    $a->add_field('x_login', x_login);
    $a->add_field('x_tran_key', x_tran_key);
    $a->add_field('x_version', '3.1');
    $a->add_field('x_type', 'AUTH_ONLY');
    $a->add_field('x_test_request', 'FALSE');
    $a->add_field('x_relay_response', 'FALSE');
    $a->add_field('x_delim_data', 'TRUE');
    $a->add_field('x_delim_char', '|');
    $a->add_field('x_encap_char', '');
    $a->add_field('x_description', $CCGateWay->orderDesc);
    $a->add_field('x_invoice_num', $order_no);
    $a->add_field('x_invoice_num', $order_no);
    $a->add_field('x_company', $_REQUEST['company']);
    $a->add_field('x_first_name', $_REQUEST['firstname']);
    $a->add_field('x_last_name', $_REQUEST['lastname']);
    $a->add_field('x_address', $_REQUEST['address1'].' '.$_REQUEST['address2']);
    $a->add_field('x_city', $_REQUEST['city2']);
    $a->add_field('x_state', $_REQUEST['state']);
    $a->add_field('x_zip', $_REQUEST['zipcode']);
    $a->add_field('x_country', $_REQUEST['country']);
    $a->add_field('x_email', $_REQUEST['email']);
    $a->add_field('x_phone', $_REQUEST['phonenumber']);
    $a->add_field('x_customer_ip', $CCGateWay->getCustomerIP());
    $a->add_field('x_ship_to_company', $_REQUEST['shipcompany']);
    $a->add_field('x_ship_to_first_name', $_REQUEST['shipfirstname']);
    $a->add_field('x_ship_to_last_name', $_REQUEST['shiplastname']);
    $a->add_field('x_ship_to_address', $_REQUEST['shipaddress1'].' '.$_REQUEST['shipaddress2']);
    $a->add_field('x_ship_to_city', $_REQUEST['shipcity']);
    $a->add_field('x_ship_to_state', $_REQUEST['sstate']);
    $a->add_field('x_ship_to_zip', $_REQUEST['shipzip']);
    $a->add_field('x_ship_to_country', $_REQUEST['shipcountry']);
    // Update the user session with the newest prices from the cart
    Session::updateDatabase();
    // Grab the user's session data from bs_sessions
    $session_data = $objSession->getSession();
    // Add our cost fields to Authorize.net
    $a->add_field('x_tax', $salestax = $session_data['sales_tax']);
    $a->add_field('x_freight', $services = $session_data['shipping_charges']);
    $a->add_field('x_tax_exempt', $shipping_tax_exempt_pre = $session_data['tax_exempt']);
    $a->add_field('x_amount', $order_total = $session_data['invoice_total']);
    //  Setup fields for payment information
    if( $CCGateWay->ccType != 'Brimar' ) {
        $a->add_field('x_method', 'CC');
        $a->add_field('x_card_num', $CCGateWay->ccNum);
        $a->add_field('x_exp_date', $CCGateWay->ccExpMonth.$CCGateWay->ccExpYear);
        $a->add_field('x_card_code', $CCGateWay->secureCode);
    }
    // If there are no errors, continue
    if( $error_array['error_msg'] == '' ) {
        if( $CCGateWay->ccType == 'Brimar' ) {
            if( count($brimar_net) > 0 ) {
                if( !empty($brimar_net['account_no']) && ($brimar_net['account_no'] == $_REQUEST['brimar_card_number']) ) {
                    $account_no = $brimar_net['account_no'];
                }else{
                    $account_no = base64_encode($_REQUEST['brimar_card_number']);
                }
                if( !empty($brimar_net['security_code']) && ($brimar_net['security_code'] == $_REQUEST['brimar_security_number']) ) {
                    $security_code = $brimar_net['security_code'];
                }else{
                    $security_code = base64_encode($_REQUEST['brimar_security_number']);
                }
                $CCGateWay->GetBrimarNet($account_no, $security_code, $order_total);
                if( $error_array['error_msg'] == '' && $error_brimar == '' ) {
                    // Place the order
                    $order_number = $objOrders->placeOrder($error_array['level'], 'net30', 'Brimar', $order_no);
                    // Redirect to the order confirmation
                    header("Location: ".URL_PREFIX_HTTPS."/order-confirmation?orderno=".$order_number);
                    die();
                }
            }
        }else{
            switch ($a->process()) {
                case 1: // Success
                    // Get the response data from Authorize.net.
                    $results = $a->dump_response(); // outputs the response from the payment gateway
                    $card_type = $a->card_type;
                    // Store transaction data.
                    $CCGateWay->setAuthorizerTransactionData(
                        $results['transactionId'], $results['reasonCode'], $results['status'], $results['subCode']
                    );
                    $CCGateWay->SaveCustomerCreditCardResponse($CID);
                    // Create an array for the auth trans note and id
                    $authorization = array (
                        'id' => $results['transactionId'],
                        'note' => $results['reasonCode']
                    );
                    // Place the order
                    $order_number = $objOrders->placeOrder(
                        $error_array['level'], 'creditcard', $card_type, $order_no, NULL, $authorization
                    );
                    // Check for success
                    if( $order_number != FALSE ) {
                        // Redirect to the order confirmation
                        header("Location: ".URL_PREFIX_HTTPS."/order-confirmation?orderno=".$order_number);
                        die();

                    }else{
                        $_SESSION['errors'] .= 'An error occurred while attempting to place your order. Please try again.';

                    }
                    break;
                case 2: // Declined
                    $error_array['error_msg'] = $a->get_response_reason_text();
                    $credit_card_error_flag = TRUE;
                    // outputs the response from the payment gateway
                    $results = $a->dump_response();
                    // Store transaction data.
                    $CCGateWay->setAuthorizerTransactionData(
                        $results['transactionId'], $results['reasonCode'], $results['status'], $results['subCode']
                    );
                    $CCGateWay->SaveCustomerCreditCardResponse($CID);
                    break;
                case 3: // Error
                    $error_array['error_msg'] = $a->get_response_reason_text();
                    $credit_card_error_flag = TRUE;
                    // outputs the response from the payment gateway
                    $results = $a->dump_response();
                    // Store transaction data.
                    $CCGateWay->setAuthorizerTransactionData(
                        $results['transactionId'], $results['reasonCode'], $results['status'], $results['subCode']
                    );
                    $CCGateWay->SaveCustomerCreditCardResponse($CID);
                    break;
            }
        }
    }

}//end of post array
echo Template::generate("global/head_checkout", array ("page" => $page, "websitedir" => $websitedir));
?>

<body id="checkout-page" class="checkout <?php print $shipping_array['state']; ?>">




<div id="expand-click-area">
    <!-- Click Body -->
    <div id="wrapper" class="container clearfix">
        <script language="javascript" type="text/javascript" src="ajax/checkout.20130905.js"></script>
        <div id="content" class="container prepend-top half">
            <!--[if !IE]> Process Status <![endif]-->
            <div class="span-24">
                <a href="<?php print $website; ?>"
                   title="Safety Signs, Parking Signs, Traffic Signs, Street Signs - SafetySign.com"
                   id="logo-wrapper"><img id="logo" src="/new_images/ss-logo-2014.png" alt="SafetySign.com"/></a>

                <!-- Support Links -->
                <div id="checkout-support-links" class="span-6 last prepend-11">
                    <p class="h3 text-center">Need Help?</p>
                    <a href="" class="support-links">
                        <div id="phone_open"><p id="phone_num">800-274-6271</p>

                            <p id="phone_hrs">M - F 9am to 5pm</p></div>
                    </a>
                    <?php
                    if( $_SERVER["HTTPS"] == "on" ) {
                        ?>
                        <div id="ciDA2w" style="sz-index:100;position:absolute"></div>
                        <div id="scDA2w" style="display:inline"></div>
                        <div id="sdDA2w" style="display:none"></div>
                        <script type="text/javascript">var seDA2w = document.createElement("script");
                            seDA2w.type = "text/javascript";
                            var seDA2ws = (location.protocol.indexOf("https") == 0 ? "https" : "http") + "://image.providesupport.com/js/brimar/safe-standard.js?ps_h=DA2w&ps_t=" + new Date().getTime() + "&online-image=https://www.safetysign.com/new_images/live_chat_online.png&offline-image=https://www.safetysign.com/new_images/live_chat_offline.png";
                            setTimeout("seDA2w.src=seDA2ws;document.getElementById('sdDA2w').appendChild(seDA2w)", 1)</script>
                        <noscript>
                            <div style="display:inline"><a href="http://www.providesupport.com?messenger=brimar">Online
                                    Customer Support</a></div>
                        </noscript>
                    <?php
                    }
                    else
                    {
                    ?>
                        <div id="ciDA2w" style="sz-index:100;position:absolute"></div>
                        <div id="scDA2w" style="display:inline"></div>
                        <div id="sdDA2w" style="display:none"></div>
                        <script type="text/javascript">var seDA2w = document.createElement("script");
                            seDA2w.type = "text/javascript";
                            var seDA2ws = (location.protocol.indexOf("https") == 0 ? "https" : "http") + "://image.providesupport.com/js/brimar/safe-standard.js?ps_h=DA2w&ps_t=" + new Date().getTime() + "&online-image=http://safetysign.com/new_images/live_chat_online.png&offline-image=http://safetysign.com/new_images/live_chat_offline.png";
                            setTimeout("seDA2w.src=seDA2ws;document.getElementById('sdDA2w').appendChild(seDA2w)", 1)</script>
                        <noscript>
                            <div style="display:inline"><a href="http://www.providesupport.com?messenger=brimar">Online
                                    Customer Support</a></div>
                        </noscript>
                    <?php
                    }
                    ?>
                </div>
            </div>

            <!-- /Support Links -->

            <!--[if !IE]> /Process Status <![endif]-->

            <!-- Left Column -->
            <div id="column-1" class="column span-24">
                <?php
                $checkout = new Page('checkout');
                $url = $checkout->getUrl();

                ?>
                <form accept-charset="utf-8" name="formcheckout"
                      class="checkout-form contacting<?php if( isset($_REQUEST['layout']) && $_REQUEST['layout'] == 'paypal' ) {
                          echo ' paypal-form';
                      } ?>" id="checkout-form" method="post" action="<?php if( isset($_REQUEST['layout']) && $_REQUEST['layout'] == 'paypal' ) {
                    echo 'process/expresscheckout.php';
                }else{
                    echo $url;
                } ?>">
                    <!-- Not Signed In -->
                    <?php
                    include($Path_Templates_Checkout."checkout.php");
                    ?>
                    <!-- /Not Signed In -->

            </div>
            <!-- /Left Column -->

        </div>
        <!--End Content -->

    </div>
</div>
<!--End Wrapper -->
<?php include_once($PathTemplates."footer_checkout.php");

// Clear out the user's session of the checkout form data now that we've loaded the page.
$_SESSION['checkout_form'] = NULL;
