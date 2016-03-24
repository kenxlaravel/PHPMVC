<?php
session_start();
require "../include/config.php";

$_SESSION['zipcode']               = $_GET['shipzip'];
$_SESSION['shipping_services']     = $_GET['shipping_method'];
$_SESSION['tax_exempt']            = $_GET['tax_exempt_status'];
$_SESSION['shipping_charges']      = $_GET['shipping_rate'];
$_SESSION['shipping_carrier']      = $_GET['shipping_carrier'];
$_SESSION['shipping_arrival_date'] = $_GET['shipping_date'];

$_SESSION['shipping_services_pre'] = $_GET['shipping_method'];
$_SESSION['shipping_carrier_pre']  = $_GET['shipping_carrier'];
$_SESSION['shipping_charges_pre']  = $_GET['shipping_rate'];

$ObjShoppingCart = Cart::getFromSession(FALSE);

// Get new order total
$total = Checkout::calculateTotal($ObjShoppingCart);

// Update the database with all the new session variables
Session::updateDatabase();


	// Echo out the new order total
echo $total;