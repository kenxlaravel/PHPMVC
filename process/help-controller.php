<?php
$ObjHelp = new Help(PAGE_ID);
// Define a var to tell us we are including from the base level help page.
// This way if help page content is accessed from somewhere other than through
// this file we will be able to tell

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    'page'                      => $page,
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
    'website'                   => website,
    'links'                     => $links,
    'ObjShoppingCart'           => $ObjShoppingCart,
    'ObjMenu'                   => $ObjMenu,
    'ObjPageProduct'            => $ObjPageProduct,
    'FrontEndTemplateIncluder'  => $FrontEndTemplateIncluder,
    'ObjHelp'                   => $ObjHelp,
    'Path_Templates_Help'       => $Path_Templates_Help,
    'Path_Templates_Base'       => $Path_Templates_Base
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('help-sec/help-template', $templateInfo);