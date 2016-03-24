<?php

// Template the page
echo Template::generate("global/notices", array ());
echo Template::generate("global/openwrap", array ('HTML_COMMENTS' => HTML_COMMENTS));
echo Template::generate(
    "global/invoice",
    array (
        "page"       => $page,
        "orderno"    => $orderno,
        "home"       => $home,
        "faq"        => $faq,
        "help"       => $help,
        "return"     => $return,
        "term"       => $term,
        "cart"       => $cart,
        "order"      => $order,
        'objUser'    => $objUser,
        "ObjOrder"   => $ObjOrder,
        "sub_total"  => $sub_total,
        "objCountry" => $objCountry,
    )
);

