<?php

session_start();

ini_set('memory_limit', '512M');
require "../include/config.php";

// Postdata and variables >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

//Get hash of cart to delete
$cartHash = (!empty($_POST['idHash']) ? $_POST['idHash'] : NULL);

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


//Action >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

if($cartHash){

	$cartToDelete = new Cart($cartHash);

	// Make sure cart being deleted is owned by the customer hoping to delete
	if($cartToDelete->customerId == $_SESSION['CID']){

		$cartName = $cartToDelete->name;
		$deleted = $cartToDelete->delete();

	} else {

		$deleted = FALSE;
	}
} else {
	$deleted = FALSE;
}

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


// Response >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$response = array("deleted" => $deleted,
				  "name" => $cartName);

//Output the JSON
header("Content-Type: application/json");
echo json_encode($response);

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<