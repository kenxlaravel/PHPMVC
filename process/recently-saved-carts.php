<?php
 
session_start();

ini_set('memory_limit', '512M');
require "../include/config.php";

// Postdata and variables >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$customerId = $_SESSION['CID'];

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


// Action >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

// Get all carts that belong to that customer (probably should be a function in Customer class if it exists)
$sql = Connection::getHandle()->prepare(
                "SELECT name, c.creation_time, count(cp.id) AS product_count, ch.hash AS idHash,
                 SUM(cp.quantity) AS quantity_sum FROM bs_carts c
                 LEFT JOIN bs_cart_skus cp ON cp.cart_id = c.id LEFT JOIN bs_cart_hashes ch ON ch.id = c.hash_id
                 WHERE customer_id = ? AND saved = 1 AND ordered = 0  GROUP BY c.id ORDER BY c.creation_time DESC");

$sql->execute(array($customerId));

$minDisplay = 5;

// loop through carts and retrieve only the information necessary
while($row = $sql->fetch(PDO::FETCH_ASSOC)){
	$savedCarts[] = array("name" => $row['name'],
						  "quantity" => $row['quantity_sum'],
						  "date" => strtotime($row['creation_time']) * 1000,
						  "idHash" => $row['idHash']);
}

$totalCount = count($savedCarts);
$savedCarts = array_slice($savedCarts, 0, $minDisplay);

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


// Response >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

$response = array("savedCarts" => $savedCarts,
				  "savedCartsCount" => $totalCount,
				  "minDisplay" => $minDisplay,
				  "savedCartsUrl" => Page::getPageUrlFromNickname('savedcarts'));

//Output the JSON
header("Content-Type: application/json");

echo json_encode($response);
exit;
// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
