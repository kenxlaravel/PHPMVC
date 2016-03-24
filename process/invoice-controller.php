<?php


$orderno = isset($_GET['orderno']) ? $_GET['orderno'] : NULL;

//Grab the order information and count it
$ObjOrder = new Orders();

$home   = Page::create ('home');
$faq    = Page::create ('faqs');
$help   = Page::create ('help');
$return = Page::create ('return-policy');
$term   = Page::create ('terms-conditions');

$order     = $ObjOrder->GetOrderInvoice($orderno, $objUser);
$sub_total = $ObjShoppingCart->getSubtotal();

foreach( $order AS $orderIds ) {

    $cart = Cart::getCartFromOrderId($orderIds['orders_id']);
}

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$templateInfo = array (
    'page'            => $page,
    "home"            => $home,
    "faq"             => $faq,
    "cart"            => $cart,
    "help"            => $help,
    "return"          => $return,
    "term"            => $term,
    "order"           => $order,
    "orderno"         => $orderno,
    'sub_total'       => $sub_total,
    "ObjOrder"        => $ObjOrder,
    "objCountry"      => $objCountry,
    'ObjShoppingCart' => $ObjShoppingCart
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('global/invoice-template', $templateInfo);