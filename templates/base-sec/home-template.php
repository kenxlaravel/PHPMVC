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
if ( $page->getNickname() == "404") { echo Template::generate("global/404_banner", array ('links' => $links)); } else { Template::generate("global/notices", array ()); }
echo Template::generate("global/home-banner", array ());
echo Template::generate("global/openwrap", array ('HTML_COMMENTS' => HTML_COMMENTS));
echo Template::generate("base-sec/home-grid",
    array (
        'ObjPageHomepage'   => $ObjPageHomepage,
        'HTML_COMMENTS'     => HTML_COMMENTS,
        'ENT_QUOTES'        => ENT_QUOTES,
        'IMAGE_URL_PREFIX'  => IMAGE_URL_PREFIX
    )
);
echo Template::generate("global/featured-pages",
    array (
        'ENT_QUOTES'        => ENT_QUOTES,
        'IMAGE_URL_PREFIX'  => IMAGE_URL_PREFIX
    )
);
echo Template::generate("global/home-bottombanner", array ());
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
        'URL_PREFIX'        => URL_PREFIX
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