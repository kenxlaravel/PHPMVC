<?php
echo Template::generate(
    "global/printsavedcart",
        array (
            'page'                     => $page,
            'links'                    => $links,
            'FrontEndTemplateIncluder' => $FrontEndTemplateIncluder,
            "home"                     => $home,
            "faq"                      => $faq,
            "help"                     => $help,
            "return"                   => $return,
            "term"                     => $term,
            "sub_total"                => $sub_total,
            "objShoppingCart"          => $objShoppingCart,
            "value"                    => $value,
            "cartHash"                 => $cartHash,
        )
);