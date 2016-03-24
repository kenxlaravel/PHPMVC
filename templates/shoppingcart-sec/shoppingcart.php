<?php // Include the necessary front-end templates.

$session_id = isset($_SESSION['CID']) ? $_SESSION['CID']: NULL;

$saved_carts_count = $objUser->getSavedCartsCount($session_id);

$signedIn = ($session_id > 0 ? TRUE : FALSE);
$savedCarts = ($saved_carts_count > 0 ? TRUE : FALSE);
$shipping_rate = array();
$error_count = 0;

//Instantiate the order class
$url_array=array('checkout','net30','home','gcart');

foreach($url_array as $url_value){
	$cart_page=new Page($url_value);
	$page_url[$url_value]=$cart_page->getUrl();
}


// Check if we have a cart instance. If there is no cart, we will just pretend we have an empty cart
if ($ObjShoppingCart instanceof Cart) {

	//Is cart empty?
	$cartIsEmpty = (!count($ObjShoppingCart->products) > 0 ? TRUE : FALSE);

	// Get list of products for shopping cart
	$cartProducts = $ObjShoppingCart->products;

	$ObjShippingCharges = new ShippingCharges();

	$subtotal=$ObjShoppingCart->getSubtotal();
	$subtotal= number_format($subtotal,2);

	// Get special messages for tips & freight
	$message=$ObjShoppingCart->getMessage();

	// get estimated ship date
	$dates=$ObjShoppingCart->getEstimatedDate();

	// get count of items in cart
	$item_count=$ObjShoppingCart->getTotalQuantity();


	if ( isset($_REQUEST['zip-code']) ) {
		$_SESSION['zip-code'] = $_REQUEST['zip-code'];
	}

	$i = -1;
	$freight = FALSE;
	$shipping_count = 0;

// Treat as though there is an empty cart
} else {

	$cartProducts = null;
	$subtotal = 0;
	$item_count = 0;
	$cartIsEmpty = TRUE;

}


if((isset($_REQUEST['zip-code'])) && (empty($message['freight_item']))) {

	$ObjShippingCharges = new ShippingCharges($_REQUEST['zip-code'],$_REQUEST['type']);

	$shipping_rate=$ObjShippingCharges->shippingCalc(false);
	$shipping_count=count($shipping_rate['shipping_rates']);
	$error_count=count($shipping_rate['errors']);

}

?>

<div id="shopping-cart-wrapper" data-cart-ship-date="<?php echo json_encode($dates['shipdate']);?>" >
	<div class="span-24">

<?php
		//check for non empty cart
		if ($item_count > 0) {
?>
			<div class="cart-messages">

				<?php if(!empty($message['tip'])) { ?><div id="holiday-message" class="append-bottom notice"><p class="h4">Holiday message</p><p><?php print $message['tip'];?></p></div><?php } ?>
				<?php if(!empty($message['freight_item'])){ $freight=(bool)true; ?><div class="freight-message"><p><span class="h5">Freight Shipping Required </span> Please see <a href="#shipping-estimator-link">shipping details below.</a></p></div><?php }?>
				<?php if(isset($_REQUEST['error'])) { ?> <div id="update-error" class="append-bottom"><p class="h5 left-side">There was an error updating the cart.&nbsp;</p><ul><li>Please contact our customer service department for assistance.</li></ul></div><?php }?>
			</div>


			<div class="span-24 append-bottom">
				<? if(!$signedIn){ ?>
				<div class="save-cart-tool left-side">
					<p class="top-space-20"><a href="<?=Page::getPageUrlFromNickname('sign-in')?>" class="underline">Sign in or create an account</a> to save this cart or load a saved cart.</p>
				</div> <? } else{ ?>
				<div class="save-cart-tool left-side">
					<div class="save-cart-wrapper left-side">
						<a href="#" class="button green save-cart">Save This Cart</a>
						<form method="POST" action="/save-cart" class="save-cart-dialog">
							<p class="h3">Save Cart</p>
							<p>Give your cart a name and click the Save button below to save it to your account.</p>
							<input type="text" class="text cart-name" name="keywords" placeholder="Enter Cart Name" value="" size="100">
							<div class="prepend-top note-wrapper">
								<span>Notes</span> <span class="note-text">(optional)</span>
								<textarea type="text" class="clear save-notes" name="keywords" value="" size="255"></textarea>
							</div>
							<div class="save-cart-controls prepend-top">
								<p class="left-side show-notes"><a href="#" class="underline">Add a note</a> <span class="note-text">(optional)</span></p>

								<div class="right-side"><span class="button save-cancel">Cancel</span> <button type="submit" class="button green save-cart" >Save Cart</button></div>
							</div>
						</form>
					</div>
					<?if($savedCarts){?>
					<div class="load-cart-wrapper left-side">
						<a href="#" class="button green load-cart">Load A Saved Cart</a>
						<div class="load-cart-dialog">

						</div>
					</div>
					<?}?>
				</div>
				<? } ?>
				<div class="span-6 prepend-top shopping-cart-item-count text-center">
					<p class="h3 pad-left-10"><span class="count">
						<?php
			//Output total items in cart
						if ($item_count > 1){
							print $item_count." Items";
						}else{
							print "1 Item";
						}
						?></span> in Your Cart
					</p>
					<p class="pad-left-10 special-note"><span class="bold">Estimated Ship Date: </span><span class="cart-shipdate"><?php print $dates['estimated_date'];?></span></p>
				</div>
				<?php if (HTML_COMMENTS) {?>	<!--Display Sub total for TOP banner --><?php }?>
				<div id="top-shopping-buttons" class="span-6 prepend-top last text-center">
					<div id="cart-summary" class="pad-right-10">
						<p class="h3">Subtotal: <span class="super-text">$</span><span class="subtotal"><?php print $subtotal; ?></span></p>
						<p class="special-note">Subtotal does not include tax or shipping.</p>
					</div>
				</div>
			</div>
			<?php if (HTML_COMMENTS) {?>	<!--Create table for Items in cart--> <?php }?>
			<table id="shopping-cart-table">
				<thead>
					<tr id="shopping-cart-header" class="span-24 prepend-top">
						<th class="text-center">Item Image</th>
						<th>Item Description</th>
						<th>Quantity</th>
						<th>Each</th>
						<th>Price</th>
					</tr>
				</thead>
				<tbody>

<?php
		//Loop through each item in the cart
		foreach ($cartProducts as $cartProduct) {

			$price=number_format(htmlspecialchars($cartProduct->unitPrice,ENT_QUOTES,"UTF-8"),2);
			$total=number_format(htmlspecialchars($cartProduct->totalPrice,ENT_QUOTES,"UTF-8"),2);
			$i++;
			$design_adjust="Design Adjustment:";
			$sku_total_quantity = $ObjShoppingCart->getQuantityBySku($cartProduct->skuCode);

			$isExpired = FALSE;
			if($cartProduct->expirationDate <= date("Y-m-d") && $cartProduct->expirationDate !== NULL && $cartProduct->expirationDate != '0000-00-00'){
				$isExpired = TRUE;
			}

			if($isExpired){
				$data_cart_inventory = 'data-cart-inventory="0"';

			} else if($cartProduct->limitedInventory){
				$data_cart_inventory = 'data-cart-inventory="'.$cartProduct->inventory.'"';

			} else {
				$data_cart_inventory = '';

			}


			if($isExpired || ($cartProduct->inventory <= 0 && $cartProduct->limitedInventory)) {
				$cartProductClass = 'cartproduct-unavailable';

			} else if($sku_total_quantity > $cartProduct->inventory && $cartProduct->limitedInventory) {
				$cartProductClass = 'cartproduct-limited';

			} else {
				$cartProductClass = 'cartproduct-available';

			}
?>
		<tr class="items-list <?=$cartProductClass?>" data-cart-product-id=<?php echo json_encode($cartProduct->id); ?> data-cart-product-qty="<?php echo json_encode($cartProduct->quantity);?>" <?php echo $data_cart_inventory; ?> >

<?php
			//Display Item Description & image based on tool/product type
			if($cartProduct->type == "flash") {

?>
				<?php if (HTML_COMMENTS) {?>	<!--- Display Image for product --> <?php } ?>
				<td class="sign-thumb span-5">
					<a href="<?php echo $cartProduct->editUrl.'?id='.$cartProduct->id; ?>">
					 <img src="<?php print CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$cartProduct->customImage['customImage'];?>" alt="<?php print htmlspecialchars($cartProduct->skuCode,ENT_QUOTES,"UTF-8");?>">
					</a>
				</td>
				<td class="item-descriptions span-9">
					<?php if (HTML_COMMENTS) {?>	<!--- Display Skucode, size and Material for product --> <?php } ?>
					<div class="product-name"><a href="<?php echo $cartProduct->editUrl; ?>"><?php print htmlspecialchars($cartProduct->nickname,ENT_QUOTES,"UTF-8"); ?></a></div>
<?php
					if(!empty($cartProduct->subtitle)){ ?>
						<div class="product-mutcd"><?php print htmlspecialchars($cartProduct->subtitle,ENT_QUOTES,"UTF-8"); ?></div>
<?php
					}?>
					<ul class="product-details-list">
						<li><span class="bold">Item &#35;: </span><span class="cartproduct-sku"><?php print htmlspecialchars($cartProduct->skuCode,ENT_QUOTES,"UTF-8"); ?></span></li>
						<?php if(!empty($cartProduct->size)) { ?><li><span class="bold">Size: </span><span class="cartproduct-size"><?php print htmlspecialchars($cartProduct->size,ENT_QUOTES,"UTF-8");?></span></li><?php } ?>
						<li><span class="bold">Material: </span><span class="cartproduct-material"><?php print htmlspecialchars($cartProduct->materialDescription,ENT_QUOTES,"UTF-8");?></span></li>
				<?if(!empty($cartProduct->savingsPercentage)){?>
						<li><span class="cart-percentage">YOU SAVED <?php print htmlspecialchars($cartProduct->savingsPercentage,ENT_QUOTES,"UTF-8").'&#37;';?></span></li>
				<?}?>
					</ul>
						<div class="span-8">
							<div class="custom-attributes collapsed">
								<p class="additional-details-header">Additional Details</p>
								<ul>
<?php
									//Loop through upcharges
									foreach($cartProduct->upcharges as $upcharge) {
										print  "<li><span class='bold'>" . $upcharge['type'] . "</span>: ".htmlspecialchars($upcharge['name'], ENT_QUOTES, 'UTF-8')."</li>";
									}

									if ($cartProduct->designService) {
										print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
									} else {
										print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>";
									}

									if ($cartProduct->comments != '') {
										print "<li><span class='bold'>Instructions: </span>".htmlspecialchars($cartProduct->comments, ENT_QUOTES, 'UTF-8')."</li>";
									}
?>
								</ul>
							</div>
						</div>
				<?php if (HTML_COMMENTS) {?>	<!-- Edit Your design--> <?php } ?>
					<p class="append-bottom prepend-top"><a class="item-number green button small-text" href="<?php echo $cartProduct->editUrl.'?id='. $cartProduct->id; ?>">Edit Your Design </a></p>
				</td>
<?php
			} else if ($cartProduct->type == "builder" ) {
?>
				<?php if (HTML_COMMENTS) {?>	<!--- Display product image --> <?php } ?>
				<td class="sign-thumb span-5">
					<a href="<?php print htmlspecialchars($cartProduct->editUrl, ENT_QUOTES, 'UTF-8');?>">
						<img src="<?php print htmlspecialchars($cartProduct->customImage['customImage'],ENT_QUOTES,"UTF-8");?>" alt="<?php print htmlspecialchars($cartProduct->materialCode,ENT_QUOTES,"UTF-8");?>">
					</a>
				</td>
				<?php if (HTML_COMMENTS) {?>	<!--- Display product skucode, size and material --> <?php } ?>

				<td class="item-descriptions span-9">
					<div class="product-name"><a href="<?php print htmlspecialchars($cartProduct->editUrl, ENT_QUOTES, 'UTF-8');?>"><?php print htmlspecialchars($cartProduct->nickname,ENT_QUOTES,"UTF-8"); ?></a></div>
<?php
					if(!empty($cartProduct->subtitle)){ ?>
						<div class="product-mutcd"><?php print htmlspecialchars($cartProduct->subtitle,ENT_QUOTES,"UTF-8"); ?></div>
<?php
					}?>
					<ul class="product-details-list">
						<li><span class="bold">Item &#35;: </span> <span class="cartproduct-sku"><?php print htmlspecialchars($cartProduct->skuCode, ENT_QUOTES, 'UTF-8');?></span></li>
						<li><span class="bold">Size: </span><span class="cartproduct-size"><?php print htmlspecialchars($cartProduct->size, ENT_QUOTES, 'UTF-8');?></span></li>
						<li><span class="bold">Material: </span><span class="cartproduct-material"><?php print htmlspecialchars($cartProduct->materialDescription, ENT_QUOTES, 'UTF-8');?></span></li>
					<?if(!empty($cartProduct->savingsPercentage)){?>
						<li><span class="cart-percentage">YOU SAVED <?php print htmlspecialchars($cartProduct->savingsPercentage,ENT_QUOTES,"UTF-8").'&#37;';?></span></li>
				<?}?>
					</ul>
						<div class="span-8 append-bottom">
							<div class="custom-attributes collapsed">
								<p class="additional-details-header">Additional Details</p>
								<ul>
<?php
									//Loop through each attribute for product
									foreach($cartProduct->settings as $setting) {
										$label="<li><span class='bold'>".htmlspecialchars($setting['builderLabel'],ENT_QUOTES,"UTF-8").": </span>";
										if ($setting['builderSettingDisplay'] == true) {
											if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {
												print $label.htmlspecialchars($setting['builderValueText'],ENT_QUOTES,'UTF-8') . "</li>";
											}

										}
									}

									if ($cartProduct->designService) {
										print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
									} else {
										print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>" ;
									}

									if($cartProduct->comments != '') print "<li><span class='bold'>Instructions: </span>". $cartProduct->comments."</li>";
?>
								</ul>
							</div>
						</div>
				<?php if (HTML_COMMENTS) {?>		<!-- Edit Your design--><?php }?>
					<p class="append-bottom"><a class="item-number green button small-text" href="<?php print htmlspecialchars($cartProduct->editUrl, ENT_QUOTES, 'UTF-8');?>">Edit Your Design</a></p>
				</td>IMAGE_URL_PREFIX.'/images/catlog/product/small/
<?php
			// Stock and streetname
			} else {

				?>
				<?php if (HTML_COMMENTS) {?>	<!--- Display Image for product --> <?php } ?>
				<td class="sign-thumb span-5">

					<a href="<?php print htmlspecialchars($cartProduct->productLink, ENT_QUOTES, 'UTF-8');?>">
<?php
					if ($cartProduct->type=='stock') {
?>
						<img src="<?php print IMAGE_URL_PREFIX.'/images/catlog/product/small/'.$cartProduct->productImage;?>" alt="<?php print htmlspecialchars($cartProduct->skuCode,ENT_QUOTES,"UTF-8");?>">
<?php
					} elseif($cartProduct->type=='streetname') {
?>
						<img src="<?php print CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$cartProduct->customImage['customImage'];?>" alt="<?php print htmlspecialchars($cartProduct->skuCode,ENT_QUOTES,"UTF-8");?>">
<?php
					}
?>
					</a>
				</td>
				<?php if (HTML_COMMENTS) {?>	<!--- Display Skucode, size and Material for product --> <?php } ?>
				<td class="item-descriptions span-9">
					<div class="product-name"><a href="<?php print htmlspecialchars($cartProduct->productLink);?>"><?php print htmlspecialchars($cartProduct->nickname,ENT_QUOTES,"UTF-8"); ?></a></div>
 <?php
					if(!empty($cartProduct->subtitle)){ ?>
						<div class="product-mutcd"><?php print htmlspecialchars($cartProduct->subtitle,ENT_QUOTES,"UTF-8"); ?></div>
<?php
					}?>
					<ul class="product-details-list">
						<li><span class="bold">Item &#35;: </span> <span class="cartproduct-sku"><?php print htmlspecialchars($cartProduct->skuCode,ENT_QUOTES,"UTF-8");?></span></li>
                <?php if(!empty($cartProduct->size)) { ?><li><span class="bold">Size: </span><span class="cartproduct-size"><?php print htmlspecialchars($cartProduct->size,ENT_QUOTES,"UTF-8");?></span></li><?php }?>
						<li><span class="bold">Material: </span><span class="cartproduct-material"><?php print htmlspecialchars($cartProduct->materialDescription,ENT_QUOTES,"UTF-8");?></span></li>
						<? if ($cartProduct->type == 'streetname') {?>
							<li><span class="bold">Preview: </span><span class="cartproduct-preview">Image to the left is for text confirmation purposes only. <a href="<? echo "/images/help-elements/street-name-popups/" . $cartProduct->accuracyImage ?>" class="zoom underline">Click&nbsp;here</a> to see a sample of our print quality.</span></li>
						<? } ?>
					<?if(!empty($cartProduct->savingsPercentage)){?>
						<li><span class="cart-percentage">YOU SAVED <?php print htmlspecialchars($cartProduct->savingsPercentage,ENT_QUOTES,"UTF-8").'&#37;';?></span></li>
				<?}
						//Comment
						if ($cartProduct->comments!=''){ ?>
							<li><span class="bold">Instructions: </span><?php print htmlspecialchars($cartProduct->comments,ENT_QUOTES,'UTF-8');?> </li>
						<?php
						}
						?>
					</ul>
<?php
					if($cartProduct->type == 'streetname') {
?>
						<div class="span-8">
							<div class="custom-attributes collapsed">
								<p class="additional-details-header">Additional Details</p>
								<ul>
<?php

									// Loop through the upcharges
									foreach($cartProduct->upcharges AS $upcharge) {
										print "<li><span class='bold'>" . $upcharge['type'] . ": </span>".htmlspecialchars($upcharge['name'],ENT_QUOTES,"UTF-8"). "</li>";
									}

									// Loop through each attribute
									foreach ($cartProduct->getAdditionalDetails() as $key => $att_value) {
										print  "<li><span class='bold'>".$key.": </span>".htmlspecialchars($att_value,ENT_QUOTES,"UTF-8"). "</li>";
									}

									//Custom image
									if (!empty($cartProduct->fileUpload['name'])) {
										print "<li><span class='bold'>Custom Image Uploaded:</span> Yes</li>";
									}

									//Design adjustment
									if ($cartProduct->designService) {
										print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
									} else {
										print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>";
									}

									if($cartProduct->comments!='') print "<li><span class='bold'>Instructions: </span>".htmlspecialchars($cartProduct->comments,ENT_QUOTES,"UTF-8")."</li>";

?>								</ul>
							</div>
						</div>
<?php



					}
?>
				</td>
<?php
			}
?>
				<?php if (HTML_COMMENTS) {?>	<!--- Update / Remove Item from Cart --> <?php } ?>

<?php
				if(($cartProduct->inventory <= 0 && $cartProduct->limitedInventory) || $isExpired) {
					$available_msg = 'No longer available';

				} else if($sku_total_quantity > $cartProduct->inventory && $cartProduct->inventory > 0 && $cartProduct->limitedInventory ) {
					$available_msg = 'Only '.$cartProduct->inventory.' in stock';

				} else {
					$available_msg = '';
				}
?>

				<td class="qty-forms-in-shopping-cart span-4">
					<form action="/update-cart" method="post" accept-charset="utf-8" name="cartupdate" >
				 		<input type="number" class="add-to-cart-input text" name="qty[]" value="<?php print htmlspecialchars($cartProduct->quantity, ENT_QUOTES, "UTF-8");?>" />
				 		<input type="hidden" name="id[]" value="<?php echo  htmlspecialchars($cartProduct->id, ENT_QUOTES, "UTF-8");?>"/>
				 		<input type="submit" value="Update" class="update-items-btn" />

					</form>
					<form action="/update-cart" method="post" accept-charset="utf-8" name="cartremove" >
						<input type="hidden" name="id[]" value="<?=htmlspecialchars($cartProduct->id, ENT_QUOTES, "UTF-8");?>" />
						<input type="hidden" name="qty[]" value="0" />
						<input type="submit" value="Remove" class="remove-items-btn left-side first-margin button" />
					</form>
					<p class="not-available inventory-alert clear top-space-10 bold"><?echo $available_msg;?></p>
				</td>
				<?php if (HTML_COMMENTS) {?>	<!--- Display Price for product --> <?php } ?>
				<td class="price-per span-3">
					$<span class="unitprice"><?php print number_format($cartProduct->unitPrice, 2);?></span>
				</td>
				<?php if (HTML_COMMENTS) {?>	<!--- Display Quantity for product --> <?php } ?>
				<td class="price-total span-3 last">
					$<span class="totalprice"><?php print number_format($cartProduct->totalPrice, 2);?></span>
				</td>
			</tr>
<?php

		}
?>
	  </tbody>
	</table>
	<div id="order-processing-footer" class="span-24">
		<div class="span-4">
			<form action="/update-cart" method="post" accept-charset="utf-8" name="removecart" >
				<input type="hidden" name="remove" value="true" />
				<button type="submit" class="remove-all-btn" >Remove All</button>
			</form>
		</div>
	</div>
</div>
<?php if (HTML_COMMENTS) {?>	<!---Shipping Calculator --> <?php } ?>

<!--New shipping estimator -->

<div id="shipping-estimator-link" class="shipping-estimator <?php if($freight) { echo 'freight-disable';} else if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'residential' || isset($_REQUEST['type']) && $_REQUEST['type'] == 'commercial') echo 'loaded';?>" data-freight-shipment=<?php echo json_encode($freight); ?> data-pickup-available=<?php echo json_encode(isset($shipping_rate['pickupAvailable']) ? $shipping_rate['pickupAvailable'] : NULL); ?> >

	<div class="shipping-estimator-instructions">
		<p class="shipping-estimator-headline"> <?php if($freight){ print "Freight Shipping Required"; } else {print "Shipping Estimator"; }?></p>
		<p class="shipping-estimator-subhead"><?php if($freight){ print $message['freight_item'];
					}else { print "Enter a US ZIP code to preview your shipping options. Need to ship outside the US? No problem; proceed to checkout to calculate shipping."; } ?></p>

	</div>
	<form accept-charset="utf-8" action="/calculate-shipping" method="post">
		<div class="shipping-estimator-zip-input-wrap"><label><p class="left-side last-margin prepend-top">ZIP Code</p> <input class="shipping-estimator-zip-input left-side" type="text" name="zip" <?php if (!empty($_REQUEST['zip-code'])) { ?>value="<?php echo $_REQUEST['zip-code']; ?>"<?php } ?>></label></div>
		<div class="shipping-estimator-address-type-input-wrap">
			<label><input class="shipping-estimator-commercial-input" type="radio" name="address-type" value="commercial" <?php if(isset($_REQUEST['type']) && $_REQUEST['type'] != 'residential') echo 'checked';?> > Business Delivery</label>
			<label><input class="shipping-estimator-residential-input" type="radio" name="address-type" value="residential" <?php if(isset($_REQUEST['type']) && $_REQUEST['type'] == 'residential') echo 'checked';?> > Home Delivery</label>
		</div>
		<div class="shipping-estimator-submit-wrap"><button class="button green" type="submit">Calculate Shipping</button></div>
	</form>

<?php
		if ($error_count > 0) {
?>
			<ul class="error clear">
<?php
			foreach($shipping_rate['errors'] as $error) {
				print '<li>'.$error.'</li>';
			}
?>
			</ul>
<?php
		}
?>


	<ul class="shipping-estimator-errors clear"></ul>
	<div class="shipping-estimator-viewer">
		<?php
		if(empty($_REQUEST['zip-code'])){
		?>
	 	<div class="shipping-estimator-tab-wrapper">
			<a class="shipping-estimator-tab shipping-estimator-tab-commercial activetab">
				<p class="h6">Business Delivery</p>
				<p>(for commercial addresses)</p>
			</a>
			<a class="shipping-estimator-tab shipping-estimator-tab-residential">
				<p class="h6">Home Delivery</p>
				<p>(for residential addresses)</p>
			</a>
			<a class="shipping-estimator-tab shipping-estimator-tab-pickup">
				<p class="h6">Customer Pickup</p>
				<p>(at our Garfield, NJ offices)</p>
			</a>

		</div>

		<div class="shipping-estimator-viewer-results-wrapper">
			<div class="shipping-estimator-viewer-commercial activetab">
				<div class="shipping-estimator-results"></div>
			</div>


			<div class="shipping-estimator-viewer-residential">
				<div class="shipping-estimator-results"></div>
			</div>

			<div class="shipping-estimator-viewer-pickup">
				<div class="shipping-estimator-results">
					<table>
						<thead>
							<tr><th>Shipping Method</th><th>Price</th><th>Estimated Availability</th></tr>
						</thead>
						<tbody>
							<tr class="data-row"><td>Customer Pickup</td><td>$0.00</td><td><?php
									$shipdate = $ObjShoppingCart->getEstimatedDate(1);
									print date("F jS, Y",strtotime($shipdate['shipdate_formatted'])); ?></td></tr>
						</tbody>
					</table>
					<p class="prepend-top pad-left-10 h6">Save time and money when you pick up your order at our Garfield, NJ facility!</p>
					<p class="pad-left-10 pad-right-10">After placing your order, you will be notified via email when your order is ready and waiting at our facility.</p>
				</div>
			</div>
		</div>

	<?php

		}
		if($shipping_count>0){
		?>
		<div class="shipping-estimator-tab-wrapper">
			<?php if($_REQUEST['type'] == 'commercial') { ?><a class="shipping-estimator-tab shipping-estimator-tab-commercial activetab">
					<p class="h6">Business Delivery</p>
					<p>(for commercial addresses)</p>
			</a>
		<?php }
			else if($_REQUEST['type'] == 'residential') { ?>
			<a class="shipping-estimator-tab shipping-estimator-tab-residential activetab">
					<p class="h6">Home Delivery</p>
					<p>(for commercial addresses)</p>
			</a>
			<?php } ?>

		</div>
			<div class="shipping-estimator-viewer-results-wrapper">
				<div
	<?php
						if($_REQUEST['type'] == 'commercial') { echo "class='shipping-estimator-viewer-commercial activetab'"; }
						else if($_REQUEST['type'] == 'residential') { echo "class='shipping-estimator-viewer-residential activetab'";  }
	?>
				>

					<div class="shipping-estimator-results">
<?php

						if($shipping_count>0){?>
							<table>
								<thead>
									<tr>
										<th>Shipping Method</th>
										<th>Price</th>
										<th>Estimated Arrival</th>
									</tr>
								</thead>
								<tbody>
<?php
								//Loop through each shipping eastimate rate
								foreach ($shipping_rate as $key => $ups_estimate) {
?>
<?php
									foreach($ups_estimate as $keys =>$shipvalue){

?>
										<tr class="data-row">
<?php
											if(empty($shipvalue['hint'])) {
?>
												<td><?php print htmlspecialchars($shipvalue['carrier']." ". $shipvalue['name'],ENT_QUOTES,"UTF-8"); ?></td>
<?php
											}else{
?>
												<td>
													<?php print htmlspecialchars($shipvalue['carrier']." ". $shipvalue['name'],ENT_QUOTES,"UTF-8"); ?>
													<div class="shipping-method-hint"> <?php print htmlspecialchars($shipvalue['hint'],ENT_QUOTES,"UTF-8"); ?></div>
												</td>
<?php
											}
?>
												<td>$<?php print htmlspecialchars(number_format($shipvalue['price'], 2),ENT_QUOTES,"UTF-8"); ?></td>
												<td><?php print htmlspecialchars(date("F jS, Y",($shipvalue['arrivalDate'])),ENT_QUOTES,"UTF-8"); ?></td>
										</tr>
<?php
									}
								}
?>
								</tbody>
							</table>
<?php
						}else{
							print $ups_estimate['errors'];
						}
?>
					</div>
				</div>
			</div>
<?php
	} ?>


	</div>
	<?php
	if( isset($shipping_rate['pickupAvailable']) ){ ?>

		<div class="shipping-estimator-viewer-pickup activetab">
				<p class="h6 pad-left-10 prepend-top append-bottom">Customer Pickup (at our Garfield, NJ offices)</p>
			<div class="shipping-estimator-results">
				<table class="ups-rates">
					<thead>
						<tr>
							<th>Shipping Method</th>
							<th>Price</th>
							<th>Estimated Arrival</th>
						</tr>
					</thead>
					<tbody>
						<tr class="data-row">
							<td>Customer Pickup</td>
							<td>$0.00</td>
							<td><?php
								$shipdate = $ObjShippingCharges->getEstimatedDate(1);
							 	print date("F jS, Y", strtotime($shipdate['shipdate_formatted'])); ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<p class="prepend-top pad-left-10 h6">Save time and money when you pick up your order at our Garfield, NJ facility!</p> <p class="pad-left-10 pad-right-10">After placing your order, you will be notified via email when your order is ready and waiting at our facility.</p>
		</div>
<?php
	}
?>

<p class="clear"><span class='bold'>Estimated Ship Date:</span> <span class='shipest-shipdate'><?php print $dates['estimated_date'];?></span></p>

</div>




<?php if (HTML_COMMENTS) { ?>	<!---Subtotal for Footer --> <?php } ?>
<div id="shopping-cart-subtotal" class="span-10 last">
	<div id="invoice-subtotals" class="span-10 last">
		<p class="total-price h2">Subtotal: <span class="super-text">$</span><span class="subtotal"><?php print $subtotal;?></span></p>
		<p class="special-note">Subtotal does not include tax or shipping.</p>
		<a class="paypal-checkout <?php if(!empty($message['freight_item'])){ $freight=(bool)true; ?>hidden<?php } ?>" href="<?php print $page_url['checkout'] . '?layout=paypal'; ?>"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif" align="left" style="margin-right:7px;"></a>
	<a href="<?php print $page_url['checkout']; ?>" class="button large orange checkout preview-checkout"><i class="sprite sprite-lock-large-white"></i><span class="left-side"> Checkout </span><i class="sprite sprite-right-white-large"></i></a>
	</div>
</div>




<!-- ends here-->

</div>
<?php if (HTML_COMMENTS) {?>	<!--- End #shopping-cart-wrapper --> <?php } ?>
<div id="security-footer" class="span-24 prepend-top">
	<div id="payment-info" class="prepend-top">
		<p class="h4 span-9">Payment Information</p>
		<p>We accept all major credit cards and PayPal, plus Brimar Net30 accounts.</p>


		<p class="span-7 h6">Apply for Brimar Net30</p>
		<a href="<?php print $page_url["net30"];?>" class="brimar">Brimar’s Net 30</a>
		<p class="span-8 left-side top-space-10"><a href="<?php print $page_url["net30"];?>" class="underline">Learn how</a> to open a Brimar Net30 account.</p>



	</div>

	<div class="span-13 prepend-top append-bottom last">

		<div class="span-6 last pad-right-5">
			<p id="security-blurb-header" class="top-rounded h4">Shop With Confidence</p>
			<p>Safetysign.com’s top priority is your security. We use the strongest security measures available to protect you and your personal information.</p>
		</div>
		<div id="security-blurb" class="span-7 last prepend-top">
			<div class="left-side">
						<a id="bbb-checkout" title="Click for the Business Review of Brimar Industries, Inc, a Safety Equipment &amp; Clothing in Garfield NJ" href="http://www.bbb.org/new-jersey/business-reviews/safety-equipment-and-clothing/brimar-industries-inc-in-garfield-nj-90048124#sealclick"><img alt="Click for the BBB Business Review of this Safety Equipment &amp; Clothing in Garfield NJ" style="border: 0;" src="/new_images/bbbsealh1US.png" /></a>
					</div>
			<div class="left-side">
						<a href="https://www.mcafeesecure.com/RatingVerify?ref=www.safetysign.com" target="_blank" id="mcafee-checkout"><img style="border:0;width:115px;height:32px" oncontextmenu="alert('Copying Prohibited by Law - McAfee Secure is a Trademark of McAfee, Inc.');" alt="McAfee Secure" src="//images.scanalert.com/meter/www.safetysign.com/22.gif"></a>
					</div>
		</div>
	</div>
</div>
<?php
}else{
?>
<div class="span-24">
	<div id="empty-cart" class="load-cart-wrapper"><p class="append-bottom h3 h3-rev">Shopping Cart</p>
				<p class="h4 pad-left-10 append-bottom">There are 0 items in your cart.</p>
				<p>To add items to your cart, use the navigation or search bar above.</p>
				<? if($signedIn && $cartIsEmpty && $savedCarts){ ?>
				<p>Would you like to load a saved cart? <span class="button small-text green load-cart">Load A Saved Cart</span></p>
						<div class="load-cart-dialog"></div>
			<? } ?>
			</div>
		</div>
	</div>
		</div><?php if (HTML_COMMENTS) {?>	<!--- End #shopping-cart-wrapper --> <?php } ?>
<div id="security-footer" class="span-24 prepend-top">
	<div id="payment-info" class="prepend-top">
		<p class="h4 span-9">Payment Information</p>
		<p>We accept all major credit cards and PayPal, plus Brimar Net30 accounts.</p>


		<p class="span-7 h6">Apply for Brimar Net30</p>
		<a href="<?php print $page_url["net30"];?>" class="brimar">Brimar’s Net 30</a>
		<p class="span-8 left-side top-space-10"><a href="<?php print $page_url["net30"];?>" class="underline">Learn how</a> to open a Brimar Net30 account.</p>



	</div>

	<div class="span-13 prepend-top append-bottom last">

		<div class="span-6 last pad-right-5">
			<p id="security-blurb-header" class="top-rounded h4">Shop With Confidence</p>
			<p>Safetysign.com’s top priority is your security. We use the strongest security measures available to protect you and your personal information.</p>
		</div>
		<div id="security-blurb" class="span-7 last prepend-top">
			<div class="left-side">
						<a id="bbb-checkout" title="Click for the Business Review of Brimar Industries, Inc, a Safety Equipment &amp; Clothing in Garfield NJ" href="http://www.bbb.org/new-jersey/business-reviews/safety-equipment-and-clothing/brimar-industries-inc-in-garfield-nj-90048124#sealclick"><img alt="Click for the BBB Business Review of this Safety Equipment &amp; Clothing in Garfield NJ" style="border: 0;" src="/new_images/bbbsealh1US.png" /></a>
					</div>
			<div class="left-side">
				<a href="https://www.mcafeesecure.com/RatingVerify?ref=www.safetysign.com" target="_blank" id="mcafee-checkout"><img style="border:0;width:115px;height:32px" oncontextmenu="alert('Copying Prohibited by Law - McAfee Secure is a Trademark of McAfee, Inc.');" alt="McAfee Secure" src="//images.scanalert.com/meter/www.safetysign.com/22.gif"></a>
			</div>
		</div>
	</div>
</div>
<?php
}