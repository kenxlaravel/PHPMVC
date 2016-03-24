<?php

$new      = "";
$invoice  = new Page('invoice');
$tracking = new Page('tracking');
$return   = new Page('return-policy');
$account  = new Page('my-account');
$history  = new Page('orderhistory');
$home     = new Page('home');
$signin   = new Page('sign-in');
$shipping = new Page('shipping');
$term     = new Page('terms-conditions');
$contact  = new Page('contact-us');

//Customer Pickup
$objOrder           = new Orders();
$objUPS             = new UpsRateService('07026', 'NJ', 'US');
$ObjShippingCharges = new ShippingCharges();

// Get the order id from the orderno
$orders_id = $objOrder->getOrdersId($_REQUEST['orderno']);
$orderno   = $_REQUEST['orderno'];

// Instantiate the cart for this order
$Cart      = Cart::getCartFromOrderId($orders_id);
$CCGateway = new CcGateway();
$objUser   = new User();

if( $_SESSION['UserType'] == 'G' ) {

    $new = $objOrder->getNewUser();
}

//@ReminderToSelf: NEED TO FIND ORDER THAT BELONGS TO CUSTOMER IF TESTING
$adminID= isset($_SESSION['adminID']) ? $_SESSION['adminID'] : NULL;

// Grab the order info
$order = $objOrder->getCustomerOrder($orderno);

if( count($order) > 0 ) {

    if( !Orders::checkIfCustomerOwnsOrder($adminID ? $adminID : $_SESSION['CID'], $orderno) ) {

        header($_SERVER['SERVER_PROTOCOL'].' 302 Found', TRUE, 302);
        header('Location: '.($_SESSION['UserType'] == 'G' ? $signin->getUrl() : $home->getUrl()));

        exit;
    }

}else{

    header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found', TRUE, 404);
    include($_SERVER['DOCUMENT_ROOT'].'/404.php');

    exit;
}

// Clear out the user's session of the checkout form data now that checkout is complete.
$ObjSession->unsetSessionCheckoutData();

$templateInfo = array (
    'page'                          => $page,
    'objUser'                       => $objUser,
    'order'                         => $order,
    'objOrder'                      => $objOrder,
    'chatStatus'                    => $chatStatus,
    'breadcrumbs'                   => $page->getBreadCrumbs(),
    'result'                        => $result,
    'URL_PREFIX'                    => URL_PREFIX,
    'URL_PREFIX_HTTP'               => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'              => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                    => ENT_QUOTES,
    'PAGE_TYPE'                     => PAGE_TYPE,
    'PAGE_ID'                       => PAGE_ID,
    'HTML_COMMENTS'                 => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'            => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID'      => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'              => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'       => CUSTOM_IMAGE_URL_PREFIX,
    'website'                       => website,
    'links'                         => $links,
    'ObjShoppingCart'               => $ObjShoppingCart,
    'ObjMenu'                       => $ObjMenu,
    'ObjPageProduct'                => $ObjPageProduct,
    'FrontEndTemplateIncluder'      => $FrontEndTemplateIncluder,
    'new'                           => $new,
    "invoice"                       => $invoice,
    "tracking"                      => $tracking,
    "return"                        => $return,
    "account"                       => $account,
    "history"                       => $history,
    "home"                          => $home,
    "signin"                        => $signin,
    "shipping"                      => $shipping,
    "term"                          => $term,
    "contact"                       => $contact,
    'ObjSession'                    => $ObjSession,
    "objUPS"                        => $objUPS,
    "ObjShippingCharges"            => $ObjShippingCharges
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('checkout-sec/order-confirmation-template', $templateInfo);