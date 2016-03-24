<?php
	session_start();

	require_once '../include/config.php';

	$ObjShoppingCart = Cart::getFromSession(FALSE);

	$ObjShippingCharges = new ShippingCharges();

	$itemToBeDeleted = array();


	if ( $_POST['remove'] ) { // Remove All from cart

		$response = $ObjShoppingCart->emptyProducts();

	} else {

		if ($_REQUEST['id'] > 0 && !empty($_REQUEST['qty'])) { // Update cart

			// Loop through deleted products and keep their id's in an array
			foreach($_REQUEST['qty'] AS $key => $itemQty) {

				if ($itemQty == 0) {

					$itemToBeDeleted[] = $_REQUEST['id'][$key];
				}
			}

			$cnt_id  = count($_REQUEST['id']);
			$cnt_qty = count($_REQUEST['qty']);

			if ($cnt_id == $cnt_qty) {

				$response = array();

				//loop through each combination to create an array for update
				for ($i = 0; $i < $cnt_id; $i++) {

					$ObjShoppingCart->updateProductQuantity($_REQUEST['id'][$i], $_REQUEST['qty'][$i]);
				}
			}
		}

		// Loop through the products in the cart and check inventory levels and expirations
		foreach ($ObjShoppingCart->products as $product) {

			$expired = ($product->expirationDate > 0 && $product->expirationDate < date('Y-m-d') ? TRUE : FALSE);

			$a = array("id"        => (int) $product->id,
					   "qty"       => (int) ($product->quantity),
					   "unitprice" => (float) $product->unitPrice);

			if ($product->limitedInventory || $expired || $product->active != 1) {

				$a['inventory'] = (int) ($product->inventory <= 0 || $expired || $product->active != 1 ? 0 : $product->inventory);
			}

			$response[] = $a;
		}

		// Loop through any deleted items and add them to the array
		foreach($itemToBeDeleted as $item) {

			$response[] = array("id"        => (int) $item,
							    "qty"       => (int) 0,
							    "unitprice" => (float) 0);
		}

	}

	// If this was requested via AJAX, calculate shipping and return JSON...
	if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && mb_strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

		//freight shipment
		$freight = $ObjShoppingCart->requiresFreight();

		//get estimated shipdate
		$dates = $ObjShoppingCart->getEstimatedDate();

		if( isset($ups_rates_time) ) {

			foreach ($ups_rates_time as $key => $value) {

				$shipdate_utc = gmdate('Y-m-d', strtotime($value['PickupDate']));
				$shipdate = strtotime($shipdate_utc) * 1000;
			}
		}
		// Serve the JSON with update response
		header("Content-Type: application/json");

		if (!$response) {

			echo json_encode(array(
								 "products"        => $response,
								 "freightshipment" => $freight,
								 "error"           => TRUE
							 )
			);
		} else {

			echo json_encode(array(
								 "products"        => $response,
								 "freightshipment" => $freight,
								 "shipdate"        => $dates['shipdate']
							 )
			);
		}

		exit;

	// If this was requested via a regular form submission, redirect to the cart and let that file present the results...
	} else {

		// Instantiate the cart page.
		$cart = new Page('cart');

		// Redirect to the cart.
		header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', TRUE, 302);

		if (!$response) {

			header('Location: ' . $cart->getUrl() . "?error");

		} else {

			header('Location: ' . $cart->getUrl());
		}

		exit;
	}
