<?php

// Template the page
echo Template::generate(
    "global/head", array (
        'page'              => $page,
        'ObjPageProduct'    => $ObjPageProduct,
        'chatStatus'        => $chatStatus,
        'URL_PREFIX'        => URL_PREFIX,
        'ENT_QUOTES'        => ENT_QUOTES,
        'PAGE_TYPE'         => PAGE_TYPE,
        'HTML_COMMENTS'     => HTML_COMMENTS
    )
);
echo Template::generate("global/header", array ('page' => $page, 'HTML_COMMENTS' => HTML_COMMENTS, 'ENT_QUOTES' => ENT_QUOTES));
echo Template::generate("global/notices", array ());
echo Template::generate("global/breadcrumbs",
    array (
        'links'             => $links,
        'breadcrumbs'       => $page->getBreadCrumbs(),
        'result'            => $result,
        'URL_PREFIX_HTTP'   => URL_PREFIX_HTTP,
        'ENT_QUOTES'        => ENT_QUOTES,
        'PAGE_TYPE'         => PAGE_TYPE
    )
);
echo Template::generate("global/openwrap", array ('HTML_COMMENTS' => HTML_COMMENTS));

echo Template::generate(
    "myaccount-sec/myaccount", array (
         "CID"                          => $CID,
         "links"                        => $links,
         "ObjSession"                   => $ObjSession,
         "objUser"                      => $objUser,
         'Path_Templates_Base'          => $Path_Templates_Base,
         'FrontEndTemplateIncluder'     => $FrontEndTemplateIncluder,
         "page"                         => $page,
         "tracking"                     => $tracking,
         "orders"                       => $orders,
         "order_total"                  => $orderTotal,
         "ObjCustomProduct"             => $ObjCustomProduct,
         "ObjShoppingCart"              => $ObjShoppingCart,
         "cartItems"                    => $cartItems,
         "savedCarts"                   => $savedCarts,
         "saved_carts"                  => $saved_carts,
         "customerData"                 => $customerData,
         "ObjAddresses"                 => $ObjAddresses,
         "ObjProduct"                   => $ObjProduct,
         "ObjOrder"                     => $ObjOrder,
         'invoice'                      => $invoice,
         "link"                         => $savedLink,
         "objCountry"                   => $objCountry,
         "Path_Templates_MyAccount"     => $PathTemplatesMyAccount,
         'URL_PREFIX_HTTP'              => URL_PREFIX_HTTPS,
         'ENT_QUOTES'                   => ENT_QUOTES
    )
);

echo Template::generate("global/closewrap", array ('HTML_COMMENTS' => HTML_COMMENTS));

echo Template::generate(
    "global/header-content", array (
        'links'                        => $links,
        'FrontEndTemplateIncluder'     => $FrontEndTemplateIncluder,
        'ObjShoppingCart'              => $ObjShoppingCart,
        'ObjMenu'                      => $ObjMenu,
        'ENT_QUOTES'                   => ENT_QUOTES,
        'IMAGE_URL_PREFIX'             => IMAGE_URL_PREFIX,
        'URL_PREFIX_HTTPS'             => URL_PREFIX_HTTPS,
        'website'                      => website
    )
);

echo Template::generate("global/footer", array (
        'page'              => $page,
        'links'             => $links,
        'URL_PREFIX_HTTP'   => URL_PREFIX_HTTP,
        'HTML_COMMENTS'     => HTML_COMMENTS,
        'URL_PREFIX'        => URL_PREFIX,
        'URL_PREFIX'        => URL_PREFIX,
        'showOrderHistory'  => (isset($_SESSION['cid']) && User::getOrderCount($_SESSION['cid']) && $_SESSION['UserType']=='U') ? TRUE : FALSE
    )
);

echo Template::generate(
    "global/foot", array (
        'page'                         => $page,
        'ObjPageProduct'               => $ObjPageProduct,
        'URL_PREFIX'                   => URL_PREFIX,
        'NEXTOPIA_PUBLIC_ID'           => NEXTOPIA_PUBLIC_ID,
        'SHOPPER_APPROVED_SITE_ID'     => SHOPPER_APPROVED_SITE_ID,
        'order'                        => $order,
        'objOrder'                     => $objOrder,
        'FrontEndTemplateIncluder'     => $FrontEndTemplateIncluder
    )
);