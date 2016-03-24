<?php


$cartHash = $_GET['cart'];

$home            = new Page('home');
$faq             = new Page('faqs');
$help            = new Page('help');
$return          = new Page('return-policy');
$term            = new Page('terms-conditions');
$sub_total       = 0;
$objShoppingCart = new Cart($cartHash);

$value = array ();


$templateInfo = array (
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
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}
echo Template::generate('global/printsavedcart-template', $templateInfo);