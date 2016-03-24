<?php

// Check if the cart is instantiated. If not, we will simply pretend there is a cart that is empty
if ($ObjShoppingCart instanceof Cart) {
	$totalQuantity = (int) $ObjShoppingCart->getTotalQuantity();
} else {
	$totalQuantity = 0;
}

// Prepare the response array.
$response = array();

if ( $totalQuantity > 0 ) {

	// Get the cart subtotal
	$subTotal = $ObjShoppingCart->getSubTotal();

	// Instantiate the page class for each link we need.
	$cartPage = new Page('cart');
	$priceGuaranteePage = new Page('lowprice');

	// Update the response array.
	$response['totalPrice'] = $subTotal;
	$response['totalQuantity'] = $totalQuantity;
	$response['cartLink'] = $cartPage->getUrl();
	$response['guaranteeLink'] = $priceGuaranteePage->getUrl();
	$response['items'] = array();

	foreach ( $ObjShoppingCart->products as $product ) {

		// Check for tool type to pull images from location
		if ( $product->type == 'streetname' || $product->type == 'flash' ) {
			$image = CUSTOM_IMAGE_URL_PREFIX . '/design/save/previews/small/' . $product->customImage['customImage'];
		} elseif ( $product->type == 'builder' ) {
			$image = $product->customImage['customImage'];
		} else {
			$image = $product->productImage;
		}

		// Update the response array.
		$response['items'][] = array(
			'image' => $image,
			'name' => $product->nickname,
			'quantity' => (int) $product->quantity
		);

	}


}

// Return json array
header('Content-Type: application/json');
echo json_encode($response);
