<?php
//The stuff we all know and love
session_start();

require_once('../include/config.php');

//Buffering
ob_start();
echo "asdfsdf";

$objUser = new User();
$ObjSession  = new Session();
$ObjShoppingCart = Cart::getFromSession(TRUE);

//Unset all session variables
$ObjSession->unsetSession();

// Set customer_id to NULL in cart row upon sign-out if applicable
if($ObjShoppingCart instanceof Cart){

	$ObjShoppingCart->setCustomerId(NULL);
}

//Destroy their cookie
setcookie("credentials", '', 1);
$objUser->cookieDestroy();

//Throw a flag so they know what happened
$_SESSION['notices'][] = 'loggedout';

//Redirect the user
$link = new Page('sign-in');

header("Location: " . $link->getUrl() . "?loggedout=1");
ob_end_flush();
die();