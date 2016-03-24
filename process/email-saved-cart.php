<?php
session_start();
/**
 * This file processes requests to email saved carts from the account page
 */
// Require the good stuff
require "../include/config.php";



// Postdata and variables >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	// Pull all the request info
	$cartHash   = $_REQUEST['cartIdHash'];                                                 // The hash of the cart
	$recipient  = $_REQUEST['recipient'];                                                  // Whom to send the email to
	$greeting   = $_REQUEST['greeting'];						                           // An extra message about the cart
	$copySender = (mb_strtolower($_REQUEST['copySender']) == 'true' ? TRUE : FALSE);       // Whether or not to copy the sender on the message

	// Instantiate the saved cart
	$cart = new Cart($cartHash);
    $ObjEmail = new Email();

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



// Action >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	if (!empty($cartHash) && $cart instanceof Cart) {

		// Check to make sure the cart's customer id matches the customer's session CID (i.e.: they are not an evil hacking impersonator)
		if ($cart->customerId == $_SESSION['CID']) {

			// Validate the email address
			if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {

				// Get the customer's email address
				$from = User::getEmailAddressById($_SESSION['CID']);

				// Instantiate our terms and conditions page and get the URL
				$terms = new Page('terms-conditions');
				$termsUrl = $terms->getUrl();

				// Instantiate our customer service page and get the URL
				$customerService = new Page('customer-service');
				$customerServiceUrl = $customerService->getUrl();

				// Loop through each item in the order
				foreach ($cart->products AS $product) {

					$attributes = array();

					switch ($product->type) {

						case 'stock':
							$image = IMAGE_URL_PREFIX_FULL.'/images/catlog/product/small/'.$product->productImage;
							break;

						case 'builder':
							foreach($product->settings as $setting) {
								$label = $setting['builderLabel'];
								if ($setting['builderSettingDisplay'] == true) {
									if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {
										$attributes[$label] = $setting['builderValueText'];
									}
								}
							}

							$image = IMAGE_URL_PREFIX_FULL.$product->customImage['customImage'];

							break;


						case 'flash':
							foreach($product->upcharges as $upcharge) {
								$attributes[$upcharge['type']] = $upcharge['name'];
							}

							$image = IMAGE_URL_PREFIX_FULL.'/design/save/previews/small/'.$product->customImage['customImage'];

							break;


						case 'streetname':

							foreach ($product->getAdditionalDetails() as $key => $att_value) {
								$attributes[$key] = $att_value;
							}

							foreach ($product->upcharges AS $upcharge) {
								if (!empty($upcharge['name'])) {
									$attributes[$upcharge['type']] = $upcharge['name'];
								}
							}

							$image = IMAGE_URL_PREFIX_FULL.'/design/save/previews/small/'.$product->customImage['customImage'];

							break;

					}


					if ($product->type != 'stock') {
						if ($product->designService) {
							$attributes['Design Adjustment'] = "We will adjust your design for best appearance.";
						} else {
							$attributes['Design Adjustment'] = "We will print your design as shown.";
						}
					} else {
						$adjust = false;
					}
                    $cartArr[] = array (
                        'sku_code'           => isset($product->skuCode) ? $product->skuCode : NULL,
                        'size'               => isset($product->size) ? $product->size : NULL,
                        'material'           => isset($product->materialDescription) ? $product->materialDescription : NULL,
                        'attributes'         => isset($attributes) ? $attributes : NULL,
                        'builder_attributes' => isset($product->settings) ? $product->settings : NULL,
                        'stock_custom'       => (isset($product->isCustom) ? $product->isCustom : NULL ? 'C' : 'S'),
                        'design_service'     => isset($product->designService) ? $product->designService : NULL,
                        'product_type'       => isset($product->type) ? $product->type : NULL,
                        'comment'            => isset($product->comments) ? $product->comments : NULL,
                        'quantity'           => isset($product->quantity) ? $product->quantity : NULL,
                        'price'              => isset($product->unitPrice) ? $product->unitPrice : NULL,
                        'total'              => isset($product->totalPrice) ? $product->totalPrice : NULL,
                        'image'              => isset($image) ? $image : NULL,
                        'adjust'             => isset($adjust) ? $adjust : NULL,
                        'file_name'          => (!empty($product->uploads[0]['hash']) ? TRUE : FALSE)
                    );

				}

				// Natural business delay
				$delay = 1;

				// General production delay
				$delay +=  Settings::getSettingValue('productiondelay');

				// Add preset delay to product custom items
				if($cart->getCustomCount() > 0){

					$delay += Settings::getSettingValue('customproductdelay');
				}

				$shipping = new Page('shipping');
				$help 	  = new Page('help');
				$privacy  = new Page('privacy-policy');
				$ObjMenu  = new Menu();


				//get urls for header
				$custom = new Page('custom-products');
				$menu['Custom Signs'] = $custom->getUrl();

				// Grab a list of main menu categories
				$main_menu = $ObjMenu->MenuList();

				// As long as we have some categories, continue
				if (count($main_menu) > 0) {

					// Loop through the menu items
					foreach($main_menu as $key => $value) {

						//The name of the menu item
						$menu_name=$value['name'];

						//The id of the menu item
						$main_category_id = $value['primary_link_pageid'];

						//Instantiate a new link from the page class so we can get a constructed URL
						$link = new Page('category', $main_category_id);
						$menu[$menu_name] = $link->getUrl();

					}
				}

                $username = trim($_SESSION['Username']);

				$emailData = array('products'           => $cartArr,
								   'cartName'           => $cart->name,
								   'cartDate'           => date('F jS, Y', strtotime($cart->creationTime)),
								   'cartNote'           => $cart->note,
								   'todayDate'          => date('F jS, Y'),
								   'delay'              => $delay,
								   'subtotal'           => $cart->getSubtotal(),
								   'customerName'       => !empty($username) ? $_SESSION['Username'] : $from,
								   'customerEmail'      => $from,
								   'greeting'           => $greeting,
								   'termsUrl'           => $termsUrl,
								   'customerServiceUrl' => $customerServiceUrl,
								   'shipping'			=> $shipping->getUrl(),
								   'privacy'			=> $privacy->getUrl(),
								   'help'				=> $help->getUrl(),
								   'menu'				=> $menu );

				// Send the email
				if ( $ObjEmail->sendSavedCartEmail($recipient, $from, $greeting, $copySender, $emailData) ) {

					$success = TRUE;
					$error = NULL;

				} else {

					$success = FALSE;
					$error = "Our mail server is currently down. Please try again later.";

				}

			} else {

				$success = FALSE;
				$error = "Please ensure that your recipient's email address is valid and try again.";

			}

		} else {

			$success = FALSE;
			$error = NULL; // The customer's session does not match the cart. We are not going to give them a specific error message for this one

		}

	} else {

		$success = FALSE;
		$error = "The specified cart could not be found. Please try again.";

	}

// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<



// Response >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	// Form a response to send back to the JS
	$response = array('success'          => $success,
					  'recipientAddress' => $recipient,
					  'errorMessage'     => $error);

	// Output the JSON response
	header("Content-Type: application/json");
	echo json_encode($response);


// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<