<?php


$cart     = Page::create('cart');
$checkout = Page::create('checkout');
$admin    = Page::create('admin-checkout');

$CCGateWay      = new CcGateway();
$ObjUserAddress = new Addresses();
$objOrders      = new Orders($_POST);
$a              = new Authorizenet;

$templateInfo = array (
    'a'               => $a,
    'page'            => $page,
    'cart'            => $cart,
    'links'           => $links,
    'admin'           => $admin,
    'websitedir'      => $websitedir,
    'ObjMenu'         => $ObjMenu,
    'objUser'         => $objUser,
    'website'         => website,
    'checkout'        => $checkout,
    'CCGateWay'       => $CCGateWay,
    'ObjUserAddress'  => $ObjUserAddress,
    'objOrders'       => $objOrders,
    'ObjShoppingCart' => $ObjShoppingCart,
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('checkout-sec/checkout-template', $templateInfo);