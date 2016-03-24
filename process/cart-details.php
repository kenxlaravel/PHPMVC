<?php
	session_start();

/**

	 */
	require "../include/config.php";

	ini_set('memory_limit', '512M');


	// Postdata and variables >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	//Pull all posted data
	$cartHash = (!empty($_POST['idHash']) ? $_POST['idHash'] : NULL) ;

	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


	//Action >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>

	$viewedCart = new Cart($cartHash);

	$authenticCustomer = ($viewedCart->customerId == $_SESSION['CID'] ? TRUE : FALSE);

	// Make sure customer is the owner of the cart they are tying to view
	if( $authenticCustomer ){

		$i = 0;

		foreach($viewedCart->products AS $product){

			// Form the image URL and other additional details based on the product type
			if ($product->type == 'stock') {

				//$imageUrl = IMAGE_URL_PREFIX.'/images/catlog/product/small/'.$product->productImage;
				$imageUrl = $product->productImage;

            } else if ($product->type == 'streetname') {

				// Retrieve Mounting option
				foreach($product->upcharges as $upcharge) {

					if (!empty($upcharge['name'])) {

						$details[$upcharge['type']] = $upcharge['name'];
					}
				}

				// Retrieve the attributes
				foreach($product->getAdditionalDetails() as $key => $attValue){

					$details[$key] = $attValue;
				}

				// Was a custom file uploaded or not?
				$details['Custom Image Uploaded'] = ($product->fileUpload['id'] > 0 ? 'Yes' : 'No');

				// Does the product need adjusting?
				$details['Design Adjustment'] = ($product->designService ? ' We will adjust your design for best appearance.' :
					' We will print your design as shown.');

				// Include the comments if there are any
				if(!empty($product->comments)){

					$details['Instructions'] = $product->comments;
				}

				$imageUrl = CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$product->customImage['customImage'];

				// File name of image for sample streetname product
				$accuracyImage = $product->accuracyImage;

			} else if ($product->type == 'flash') {

				// Retrieve Mounting option
				foreach($product->upcharges as $upcharge) {

					if (!empty($upcharge['name'])) {

						$details[$upcharge['type']] = $upcharge['name'];
					}
				}

				// Does the product need adjusting?
				$details['Design Adjustment'] = ($product->designService ? ' We will adjust your design for best appearance.' :
					' We will print your design as shown.');

				// Include the comments if there are any
				if( !empty($product->comments) ){

					$details['Instructions'] = $product->comments;
				}

				$imageUrl = CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$product->customImage['customImage'];

			} else if ($product->type == 'builder') {

				// Output the attributes
				foreach($product->settings as $setting) {

					$label = $setting['builderLabel'];

                    if ($setting['builderSettingDisplay'] == true) {

						if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {

							$details[$label] = $setting['builderValueText'];
						}
					}
				}

				// Does the product need adjusting?
				$details['Design Adjustment'] = ($product->designService ? ' We will adjust your design for best appearance.' :
					' We will print your design as shown.');

				// Include the comments if there are any
				if(!empty($product->comments)){

					$details['Instructions'] = $product->comments;
				}

				$imageUrl = $product->customImage['customImage'];
			}

            $description = array (

                "title"         => isset($product->nickname) ? $product->nickname : NULL,
                "subtitle"      => isset($product->subtitle) ? $product->subtitle : NULL,
                "image"         => isset($imageUrl) ? $imageUrl : NULL,
                "url"           => isset($product->productLink) ? $product->productLink : NULL,
                "item_number"   => isset($product->skuCode) ? $product->skuCode : NULL,
                "size"          => isset($product->size) ? $product->size : NULL,
                "material"      => isset($product->materialDescription) ? $product->materialDescription : NULL,
                "accuracyImage" => isset($accuracyImage) ? $accuracyImage : NULL
            );

			$cartItems[$i]['description'] = isset($description) ? $description : NULL;
			$cartItems[$i]['quantity']    = isset($product->quantity) ? $product->quantity : NULL;
			$cartItems[$i]['unit_price']  = isset($product->unitPrice) ? $product->unitPrice : NULL;
			$cartItems[$i]['total_price'] = isset($product->totalPrice) ? $product->totalPrice : NULL;
			$cartItems[$i]['details']     = isset($details) ? $details : NULL;

			$i++;

			unset($details);
		}

		$cartName = $viewedCart->name;
		$cartNote = $viewedCart->note;
		$cartSubtotal = $viewedCart->getSubtotal();

	}

	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<


	// Response >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
	if( $authenticCustomer ){

        $response = array (
            "name"       => (string) $cartName,
            "note"       => (string) $cartNote,
            "cartHash"   => (string) $cartHash,
            "subtotal"   => $cartSubtotal,
            "savedCarts" => $cartItems
        );
		// If customer is not the owner of the cart they are tying to view.
	} else {

		$response = FALSE;
	}

	// Output the JSON response
	header("Content-Type: application/json");
	echo json_encode($response);
    exit;

	// <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
