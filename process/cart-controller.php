<?php


$FrontEndTemplateIncluder->addHandlebarsTemplate('cartCommunicationError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoadingMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('freightShipmentNotice');
$FrontEndTemplateIncluder->addHandlebarsTemplate('inventoryAlert');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderLoading');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderResults');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartConflict');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartLoading');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartSuccess');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDeleteConfirmation');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetails');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetailsErrorMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetailsLoadingMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartLoading');
$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartMergeConflict');
$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartNameError');

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    'page'                      => $page,
    'objUser'                   => $objUser,
    'order'                     => $order,
    'objOrder'                  => $objOrder,
    'chatStatus'                => $chatStatus,
    'breadcrumbs'               => $page->getBreadCrumbs(),
    'result'                    => $result,
    'URL_PREFIX'                => URL_PREFIX,
    'URL_PREFIX_HTTP'           => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'          => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                => ENT_QUOTES,
    'PAGE_TYPE'                 => PAGE_TYPE,
    'PAGE_ID'                   => PAGE_ID,
    'HTML_COMMENTS'             => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'        => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID'  => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'          => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'   => CUSTOM_IMAGE_URL_PREFIX,
    'website'                   => website,
    'links'                     => $links,
    'ObjShoppingCart'           => $ObjShoppingCart,
    'ObjMenu'                   => $ObjMenu,
    'ObjPageProduct'            => $ObjPageProduct,
    'FrontEndTemplateIncluder'  => $FrontEndTemplateIncluder
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('shoppingcart-sec/cart-template', $templateInfo);