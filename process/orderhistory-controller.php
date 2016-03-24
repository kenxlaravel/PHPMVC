<?php

// Allow the script 3 minutes of running time.
set_time_limit(180);

//Set the customer id
$CID = $_SESSION['CID'];
$ObjCustomProduct = new CustomProduct();
$ObjOrder = new Orders();
$ObjAddresses = new Addresses($CID);
$ObjProduct = ProductPage::create($page->getId());

//Check if the customer is reordering
if( isset($_GET['orderno']) ) {

    $cartPID = (isset($_GET['pid']) && $_GET['pid'] > 0 ? $_GET['pid'] : NULL);
    $cart = Cart::getCartFromOrderNumber($_GET['orderno']);

    // Create a current cart if there wasn't any before reordering
    $ObjShoppingCart = Cart::getFromSession(TRUE);

    // Copy the products to
    $reordered = $cart->copyProducts($ObjShoppingCart, FALSE, $cartPID);

    // Based on whether we were successful or not, grab the correct message and redirect the user
    if( $reordered ) {

        $_SESSION['notices'][] = 'reordered';
        $cart = new Page('cart');

        header("Location: ".$cart->getUrl());
        die();

    }else{

        $_SESSION['notices'][] = 'reorder_fail';

        header("Location: ".$page->getUrl());
        die();
    }
}

//Get all the customer's information
$customer_data = $objUser->GetCustomerById($CID);
$orders = $objUser->getOrdersByCid($CID);
$order_total = count($orders);

//Instantiate pages for links
$tracking_page = new Page('tracking');
$contact_page = new Page('contact-us');
$invoice_page = new Page('invoice');

$tracking = $tracking_page->getUrl();
$contact = $contact_page->getUrl();
$invoice = $invoice_page->getUrl();

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    'CID'                           => $CID,
    'objCountry'                    => $objCountry,
    'page'                          => $page,
    'Path_Templates_Tracking'       => $Path_Templates_Tracking,
    'ObjOrder'                      => $ObjOrder,
    'invoice'                       => $invoice,
    'tracking'                      => $tracking,
    'orders'                        => $orders,
    'order_total'                   => $order_total,
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
    'RECAPTCHA_PUBLIC_KEY'          => RECAPTCHA_PUBLIC_KEY,
    'APP_ROOT'                      => APP_ROOT,
    'EMAIL_SALES'                   => EMAIL_SALES,
    'website'                       => website,
    'links'                         => $links,
    'ObjShoppingCart'               => $ObjShoppingCart,
    'ObjMenu'                       => $ObjMenu,
    'ObjPageProduct'                => $ObjPageProduct,
    'objContactUs'                  => $objContactUs,
    'FrontEndTemplateIncluder'      => $FrontEndTemplateIncluder
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('myaccount-sec/orderhistory-template', $templateInfo);