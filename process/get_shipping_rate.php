<?php
session_start();
require "../include/config.php";

$_SESSION['zipcode']               = $_GET['shipzip'];
$_SESSION['shipping_services']     = $_GET['shipping_method'];
$_SESSION['tax_exempt']            = $_GET['tax_exempt_status'];
$_SESSION['shipping_charges']      = $_GET['shipping_rate'];
$_SESSION['shipping_carrier']      = $_GET['shipping_carrier'];
$_SESSION['shipping_arrival_date'] = $_GET['shipping_date'];

$ObjShoppingCart = Cart::getFromSession(FALSE);

// Echo out the new order total
echo Checkout::calculateTotal();