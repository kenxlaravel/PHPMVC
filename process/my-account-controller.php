<?php
// Allow the script 3 minutes of running time.
set_time_limit(180);

$CID = isset($_SESSION['CID']) ? $_SESSION['CID'] : NULL;

//Instantiate needed classes
$ObjOrder         = new Orders();
$ObjCustomProduct = new CustomProduct();
$ObjAddresses     = new Addresses($CID);
$ObjProduct       = ProductPage::create(PAGE_ID);
$cartItems        = $ObjShoppingCart instanceof Cart ? $ObjShoppingCart->getTotalQuantity() : 0;
$customerData     = $objUser->GetCustomerById($CID);
$savedCarts       = $objUser->getSavedCarts($CID);
$orders           = $objUser->getOrdersByCid($CID);
$orderTotal       = count($orders);
$saved_carts      = count($savedCarts);
$savedLink = new Page('savedcarts');
$customerData = $objUser->GetCustomerById($CID);

// Handlebars templates
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartCommunicationError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoadingMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('freightShipmentNotice');
$FrontEndTemplateIncluder->addHandlebarsTemplate('inventoryAlert');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartCommunicationError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderLoading');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderResults');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoadingMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartConflict');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartLoading');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartSuccess');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDeleteConfirmation');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDeleting');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetails');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetailsErrorMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetailsLoadingMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartLoading');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartMergeConflict');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartEmailer');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartEmailerSuccess');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartOwnerEmailAddress');

//Check if the customer is reordering
if( isset($_GET['orderno']) ) {

    $cartPID = ($_GET['pid'] > 0 ? $_GET['pid'] : NULL);
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
//Instantiate pages for links
$tracking = Page::create('tracking')->getUrl();
$contact_page = new Page('contact-us');
$contact = $contact_page->getUrl();
$invoice_page = new Page('invoice');
$invoice = $invoice_page->getUrl();

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    "CID"                           => $CID,
    'page'                          => $page,
    'links'                         => $links,
    'savedLink'                     => $savedLink,
    'invoice'                       => $invoice,
    'objUser'                       => $objUser,
    'ObjSession'                    => $ObjSession,
    "ObjOrder"                      => $ObjOrder,
    "ObjProduct"                    => $ObjProduct,
    "ObjCustomProduct"              => $ObjCustomProduct,
    "cartItems"                     => $cartItems,
    "customerData"                  => $customerData,
    "ObjShoppingCart"               => $ObjShoppingCart,
    "savedCarts"                    => $savedCarts,
    "orders"                        => $orders,
    "tracking"                      => $tracking,
    "PathTemplatesMyAccount"        => $Path_Templates_MyAccount,
    "orderTotal"                    => $orderTotal,
    "saved_carts"                   => $saved_carts,
    "ObjAddresses"                  => $ObjAddresses,
    'Path_Templates_Base'           => $Path_Templates_Base,
    'FrontEndTemplateIncluder'      => $FrontEndTemplateIncluder,
    "objCountry"                    => $objCountry,
    'chatStatus'                    => $chatStatus,
    'breadcrumbs'                   => $page->getBreadCrumbs(),
    'ObjMenu'                       => $ObjMenu,
    'ObjPageProduct'                => $ObjPageProduct,
    'customerData'                  => $customerData,
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
    'URL_PREFIX_HTTP'               => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                    => ENT_QUOTES
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}
echo Template::generate('myaccount-sec/myaccount-template', $templateInfo);