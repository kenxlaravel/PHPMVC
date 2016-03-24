<?php
session_start();

require_once '../include/config.php';

// If this was requested via AJAX, calculate shipping and return JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

		//Determine address type
		if(mb_strtolower($_REQUEST['addressType']) != 'residential'){
			$address_type ='commercial';
		}else{
			$address_type ='residential';
		}

 		//Create an instance of the shipping charges class, and pass in the zipcode
		$ObjShippingCharges = new ShippingCharges($_REQUEST['zip'],$address_type);

		// Calculate the shipping rates and times.
		$shipping_info = $ObjShippingCharges->shippingCalc(false);

		$state_search= $ObjShippingCharges->zipcodeSearch($_REQUEST['zip']);
		if($state_search['state'] == 'NJ'){
			$pickup = true;
		}else{
			$pickup = false;
		}
		 	// If results were found, build the return array.
		if (!empty($shipping_info)) {

			foreach ($shipping_info['shipping_rates'] as $key => $shipping_value) {

					$rates[] = array(
						"carrier" => (string) isset($shipping_value['carrier']) ? $shipping_value['carrier'] : NULL,
						"name" => (string) isset($shipping_value['name']) ? $shipping_value['name'] : NULL,
						"price" => (float) isset($shipping_value['price']) ? $shipping_value['price'] : NULL,
						"arrivalDate" => (int) (isset($shipping_value['arrivalDate'] ) ? $shipping_value['arrivalDate'] * 1000 : NULL),
						"hint" => (string) isset($shipping_value['hint']) ? $shipping_value['hint'] : NULL
					);

			}
			$response = array(
				"addressType"=> (string) $address_type,
	    		"pickupAvailable" => (bool) $pickup,
				"rates" => $rates,
				"errors" => $shipping_info['errors']
			);
		}

		// Serve the JSON.
		header("Content-Type: application/json");
		echo json_encode($response);
		exit;

	// If this was requested via a regular form submission, redirect to the cart and let that file present the results

} else {


	// Instantiate the cart page.
	$cart = new Page('cart');
	// Redirect to the cart.
	header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
	header('Location:' . $cart->getUrl() . '?' . http_build_query(array('zipcode' => $_REQUEST['zip'],'type'=>$_REQUEST['address-type'])));
	exit;

}
