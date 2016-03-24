<?php
ini_set('memory_limit', '512M');
require("../include/config.php");
require ("global-controller.php");

//Pull all posted data
$sku_code = (!empty($_REQUEST['sku_code']) ? $_REQUEST['sku_code'] : NULL);

$type = (!empty($_REQUEST['type']) ? $_REQUEST['type'] : $_REQUEST['producttype']);
$skuId =(!empty($_REQUEST['sku_id']) ? $_REQUEST['sku_id'] : NULL);
$pid = (!empty($_REQUEST['id']) ? $_REQUEST['id'] : $_REQUEST['productid']);
$qty = (!empty($_REQUEST['qtyField']) ? $_REQUEST['qtyField'] : $_REQUEST['qty']);
$stid = (!empty($_REQUEST['stid']) ? $_REQUEST['stid'] : 1);

// Get product state parameter from js if the product is not streetname or builder
if ($type == 'stock' || $type == 'flash') {
    $stateParameters = ProductStateParameter::decode($_REQUEST['product_state_url']);

} elseif ($type == 'streetname') {

    $stateParameters = array('sourceProduct' => $_POST['source_product_id'],
        'sourceProductRecommendation' => $_POST['source_product_recommendation_id'],
        'sourceAccessoryFamilyProduct' => $_POST['source_accessory_familyProduct_id'],
        'sourceInstallationAccessory' => $_POST['source_installation_accessory_id'],
        'sourceLandingProduct' => $_POST['source_landing_product_id'],
        'sourceSubcategoryProduct' => $_POST['source_subcategory_product_id']);
} else {
    $stateParameters = array('sourceProduct' => $_POST['sourceproductid'],
        'sourceProductRecommendation' => $_POST['sourceproductrecommendationid'],
        'sourceAccessoryFamilyProduct' => $_POST['sourceaccessoryfamilyproductid'],
        'sourceInstallationAccessory' => $_POST['sourceinstallationaccessoryid'],
        'sourceLandingProduct' => $_POST['sourcelandingproductid'],
        'sourceSubcategoryProduct' => $_POST['sourcesubcategoryproductid']);
}

$ObjShoppingCart = $resultsOfGlobalController['ObjShoppingCart'];

if (isset($sku_code) && !$skuId ) { $skuId = (int) $ObjShoppingCart->getSkuIdBySkuCode($sku_code); }

if ($type == 'streetname') {
	$editdata = isset($_REQUEST['editdata']) ? json_decode($_POST['editdata'], true) : NULL;
} else if ($type == 'flash') {
	$design = $_REQUEST['designapproval'];
	$cpi = $_REQUEST['cpi'];
} else if ($type == 'builder' || $type == 'custom-builder') {
	$designid = $_REQUEST['designid'];
	$builder_ref = $_REQUEST['builderref'];
	$action = $_REQUEST['action'];
	$data = $_REQUEST['editdata'];
	$render=$_REQUEST['renderdata'];
}

//Check to see if this is a custom builder
if ($type == 'builder' || $type == 'custom-builder') {

	if ($action == "add" || $action == "edit") {

		$editdata=json_decode($data,true);
		$renderdata=json_decode($render,true);

		//Get data needed to render 'added to cart' window
		$ShoppingCartNotification = $ObjShoppingCart->addBuilder($qty, $stateParameters, $editdata, $renderdata, $action, $designid, $builder_ref);

		//Encode this data as JSON, based off success or failure
		if ($ShoppingCartNotification['success'] == true) { //Success
			$response = array('designid' => $ShoppingCartNotification['designid'],
							  'success' => $ShoppingCartNotification['success'],
							  'notices' => $ShoppingCartNotification['notices'],
							  'image' => $ShoppingCartNotification['image'],
							  'count' => $ShoppingCartNotification['count'],
							  'subtotal' => $ShoppingCartNotification['subtotal']);
		} else { //Failure
			$response = array('designid' => $ShoppingCartNotification['designid'],
							  'success' => $ShoppingCartNotification['success'],
							  'errors' => $ShoppingCartNotification['errors'],
							  'count' => $ShoppingCartNotification['count'],
							  'subtotal' => $ShoppingCartNotification['subtotal']);
		}

	} else if ($action == "remove") {

		//Try to delete the item from cart. Deleted will return true if successful, or false if item could not be deleted.
		$cartProductId = $ObjShoppingCart->getProductIdByDesign($designid);
        $deleted = $ObjShoppingCart->removeProduct( $cartProductId );


		//Get cart count and subtotal so we can update the cart
		$total = $ObjShoppingCart->getSubTotal();
		$count = $ObjShoppingCart->getLineItemCount();

		//Output the JSON
		$response = array('success' => $deleted, 'count' => $count, 'subtotal' => $total);

	}

} else if($type == 'stock'){ $response = $ObjShoppingCart->addStock($pid, $skuId, $qty, $stateParameters); }
  else if($type == 'flash'){ $response = $ObjShoppingCart->addFlash($pid, $skuId, $qty, $stateParameters, $upcharges, $design, $cpi); }
  else if($type == 'streetname'){

	  $response = $ObjShoppingCart->addStreetname($pid, $skuId, $qty, $stateParameters, $stid, 'adjust', $editdata);

	  // Check if an custom image was uploaded
	  $response['imageUploaded'] = ($editdata['uploadfileid'] > 0 ? TRUE : FALSE);
  }

	//If this was not an AJAX request
	if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {

		//If there is a quantity, take them to the cart
		if ($qty > 0) {

			//Redirect to the cart.
			$cart = new Page('cart');
			header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
			header('Location:' . $cart->getUrl());
			exit;

		//If there is no quantity, take them back to the page they were on and throw a flag
		} else {

			//If we have a product id, build a URL to send them back to
			if ($pid > 0) {

				//Parse the referer URL so we can keep the query intact if there is one
				$referer = parse_url($_SERVER['HTTP_REFERER']);

				// Throw a flag so the user knows what happened
				$_SESSION['notices'][] = 'addtocartfail';

				// Instantiate the product, and form a url with a query appended if there was one
				$url = new Page('product', $pid);
				$redirect_to = $url->getUrl() . (!empty($referer['query']) ? '?' . $referer['query'] : '');

				// Redirect
				header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
				header('Location:' . $redirect_to);
				die();

			//Otherwise we have to trust the referral var is where they came from
			} else {

				// Throw a flag so the user knows what happened
				$_SESSION['notices'][] = 'addtocartfail';

				// Form a URL and redirect them
				header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
				header('Location:' . $_SERVER['HTTP_REFERER']);
				die();

			}

		}

	}

ini_restore('memory_limit');

//Output the JSON
header("Content-Type: application/json");
echo json_encode($response);
die();
