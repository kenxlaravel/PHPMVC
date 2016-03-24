<?php
require "../bs_common.php";

// Keep the coupon number in the session
$_SESSION['coupon_number'] = $_GET['couponcode'];

// Calculate the coupon savings and keep it in the session
Checkout::getCouponBySession();

// Update bs_sessions with form data
$total = Checkout::calculateTotal();

// Get the user's data from bs_sessions
$response['min'] = 0;
$response['coupon'] = number_format($_SESSION['coupon_value'], 2);
$response['sales_tax'] = number_format($_SESSION['sales_tax'], 2);
$response['services'] = number_format($_SESSION['shipping_charges'], 2);
$response['total'] = number_format($total, 2);
$response['status'] = $_SESSION['tax_exempt'];

// Print a pipe-separated list of data. TODO: Replace with JSON
print $response['min']."|".$response['coupon']."|$".$response['sales_tax']."|$".$response['services']."|$".$response['total']."|Y|".$total;