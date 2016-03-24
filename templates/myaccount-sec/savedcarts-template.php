<?php
/**
 * @author     Daniel <danielm@brimar.com>
 * @author     Ken <ken@brimar.com>
 * @author     Ali <ali@brimar.com>
 * @author     Abu <abu@brimar.com>
 * @copyright  Brimar Industries
 * @since      Class available since Nov 17, 2014
 * @version    ?? :: PHP 5.2.4
 **/

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
    "myaccount-sec/savedcarts",
        array (
            "page"                          => $page,
            "FrontEndTemplateIncluder"      => $FrontEndTemplateIncluder,
            "Path_Templates_MyAccount"      => $Path_Templates_MyAccount,
            "ObjCustomProduct"              => $ObjCustomProduct,
            "ObjOrder"                      => $ObjOrder,
            "ObjAddresses"                  => $ObjAddresses,
            "ObjProduct"                    => $ObjProduct,
            "customer_data"                 => $customer_data,
            "savedCarts"                    => $savedCarts,
            "saved_carts"                   => $saved_carts,
            "invoice_page"                  => $invoice_page,
            "tracking_page"                 => $tracking_page,
            "contact_page"                  => $contact_page,
            "contact"                       => $contact,
            "tracking"                      => $tracking,
            "invoice"                       => $invoice,
            "ObjShoppingCart"               => $ObjShoppingCart,
            "link"                          => $savedLink,
            "totalQuantity"                 => $totalQuantity,
            'ENT_QUOTES'                    => ENT_QUOTES
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
