<?php


//Instantiate needed classes
$objCategoryPage = CategoryPage::create(PAGE_ID);
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
    'CUSTOM_IMAGE_URL_PREFIX'   => CUSTOM_IMAGE_URL_PREFIX,
    'website'                   => website,
    'links'                     => $links,
    'ObjShoppingCart'           => $ObjShoppingCart,
    'ObjMenu'                   => $ObjMenu,
    'ObjPageProduct'            => $ObjPageProduct,
    'FrontEndTemplateIncluder'  => $FrontEndTemplateIncluder,
    'objCategoryPage'           => $objCategoryPage,
    'objSubcategoryPage'        => $objSubcategoryPage,
    'Path_Templates_Base'       => $Path_Templates_Base
);

if (!empty($resultsOfGlobalController)) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string)$name] = $value;
    }
}

echo Template::generate('base-sec/category-template', $templateInfo);