<?php
$CID = isset($_SESSION['CID']) ? $_SESSION['CID'] : NULL;
$stateParameters = (!empty($_GET['s'])? $_GET['s'] : NULL);
$ObjCustomProduct = new CustomProduct();
$ObjOrder         = new Orders();
$ObjAddresses     = new Addresses($CID);
$ObjProduct       = Product::create($page->getId(),NULL,$stateParameters);
$invoice_page     = new Page('invoice');
$tracking_page    = new Page('tracking');
$contact_page     = new Page('contact-us');

$customer_data = $objUser->GetCustomerById($CID);
$savedCarts    = $objUser->getSavedCarts($CID);
$contact       = $contact_page->getUrl();
$tracking      = $tracking_page->getUrl();
$invoice       = $invoice_page->getUrl();

$saved_carts = count($savedCarts);
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

$savedLink = new Page('savedcarts');

if( $ObjShoppingCart instanceof Cart ) {

    $totalQuantity = (int) $ObjShoppingCart->getTotalQuantity();

}else{

    $totalQuantity = 0;
}

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    'page'                          => $page,
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
    'RECAPTCHA_PUBLIC_KEY'          => RECAPTCHA_PUBLIC_KEY,
    'website'                       => website,
    'ObjShoppingCart'               => $ObjShoppingCart,
    'ObjMenu'                       => $ObjMenu,
    'ObjPageProduct'                => $ObjPageProduct,
    'FrontEndTemplateIncluder'      => $FrontEndTemplateIncluder,
    'links'                         => $links,
    'Path_Templates_MyAccount'      => $Path_Templates_MyAccount,
    'ObjCustomProduct'              => $ObjCustomProduct,
    'ObjOrder'                      => $ObjOrder,
    'ObjAddresses'                  => $ObjAddresses,
    'ObjProduct'                    => $ObjProduct,
    'customer_data'                 => $customer_data,
    'savedCarts'                    => $savedCarts,
    'saved_carts'                   => $saved_carts,
    'invoice_page'                  => $invoice_page,
    'tracking_page'                 => $tracking_page,
    'contact_page'                  => $contact_page,
    'contact'                       => $contact,
    'tracking'                      => $tracking,
    'invoice'                       => $invoice,
    'ObjShoppingCart'               => $ObjShoppingCart,
    'link'                          => $savedLink,
    'totalQuantity'                 => $totalQuantity
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('myaccount-sec/savedcarts-template', $templateInfo);