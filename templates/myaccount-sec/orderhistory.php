<?php
	$link = new Page('orderhistory');
	$count = count($orders);
    $reorder_all = FALSE;
?>

	<!-- Order History-->
	<div class="myaccount-section container" id="order-history">
		<p class="h4 left-side append-bottom">Order History</p>
	<?php
		//If the user has more than 5 orders, give them a total and a view all button
		if ($count > 5 && $page->getNickname() != 'orderhistory') {

			$orders = array_slice($orders, 0, 5); // Take only the first 5 orders for the account page
?>
			<p class="left-side first-margin prepend-top-5">(Showing 5 most recent orders. <a href="<?php echo htmlspecialchars($link->getUrl(), ENT_QUOTES, 'UTF-8') ?>" class="underline">View All</a>)</p>
<?php	}


		if ($count>0) {
?>
			<table id="orderhistory">
				<thead>
					<tr>
						<th class="oh-table-col-1">Order Date</th>
						<th class="oh-table-col-1">Order Number</th>
						<th class="oh-table-col-1">Total</th>
						<th class="oh-table-col-2">Total Qty.</th>
						<th class="oh-table-col-1">Status</th>
						<th class="oh-table-col-3">Shipment Tracking</th>
						<th class="oh-table-col-4">Details</th>
					</tr>
				</thead>


<?php
			foreach ($orders as $key => $value) {
				$date_pickup=$ObjOrder->GetDatePickup($value['order_no']);
?>
			<!-- TO DO: the tbody needs to wrap every two rows -->
				<tbody class="order-row">
					<tr class="data-row order-details-open-head">
						<td class="oh-table-col-1"><?php print	$date_purchased=$ObjOrder->OrderDate($value['date_purchased']);?></td>
						<td class="oh-table-col-1"><?php print $value['order_no'];?></td>
						<td class="oh-table-col-1">$<?php print number_format($value['total_amount'], 2);?></td>
						<td class="oh-table-col-2"><?php echo $value['item_count']; ?></td>
						<td class="oh-table-col-1"><?php $status=$value['orders_status'];print $orderstatus=$ObjOrder->OrderStatus($status);?></td>
						<td class="oh-table-col-3">
<?php
							//Figure out the order status
							if ($value['tracking_number']=='' && $value['shipping_services']!='Customer Pickup' && $value['orders_status']!='3') {

								print "Shipping Pending";

							} else if($value['tracking_number']=='' && $value['shipping_services']==='Customer Pickup' ) {

								if ($value[orders_status]=='6')
									print "Ready for Pickup";
								else if($value[orders_status]=='7')
									print "Picked Up on ".date("m/d/Y",strtotime($date_pickup));
								else print "Not yet ready for pickup";

							} else if($value['tracking_number']=='' && $value['shipping_services']=='LTL / Freight Carrier' && $value['orders_status']==='3') {

								print "Shipped";

							} else if($value['tracking_number']!='' && $value['shipping_services']!='Customer Pickup') {
?>
								<a href="<?php print $tracking."?orderno=". $value['order_no'];?>" title="Click here for order tracking detail."><?php print  $value['tracking_number'];?></a>
<?php
							}
?>
						</td>
						<td class="oh-table-col-4">
							<a href="<?php print $link->getUrl();?>?orderno=<?php print $value['order_no'];?>" class="reorder_button" title="Click here to reorder." >View/Reorder</a>
						</td>
					</tr>
					<tr class="order-details-open no-js-hidden">
						<td colspan="7">
							<div class="container">
								<div class="span-5 last text-left">
									<div class="order-details-basic-info">
										<p class="bold">Shipping:</p>
										<p><?php echo $value['shipping_carrier']." ".$value['shipping_services']; ?></p>
<?php
									if (!empty($value['shipping_account'])) {
?>
										<p class="bold">Shipping Account:</p>
										<p><?php echo $value['shipping_account']; ?></p>
<?php
									}
?>
									</div>
									<div class="order-details-basic-info">
										<p class="bold">Shipping Address:</p>
										<?php echo "<p>" . $value['shipping_first_name'] . " " . $value['shipping_last_name'] . "</p>"; ?>
										<?php echo (!empty($value['shipping_company']) ? "<p>" . $value['shipping_company'] . "</p>" : ''); ?>
										<?php echo "<p>" . $value['shipping_street_address'] . "</p>"; ?>
										<?php echo (!empty($value['shipping_suburb']) ? "<p>" . $value['shipping_suburb'] . "</p>" : ''); ?>
										<?php echo "<p>" . $value['shipping_city'] . ", " . $value['shipping_state'] . " ". $value['shipping_postcode'] . "</p>" ?>

										<?php
											$address_country=$objCountry->CountryCodeList($value['shipping_country']);
											if(!empty($address_country)) { echo "<p>" . $address_country['countries_name'] . "</p>"; }?>
									</div>
									<div class="order-details-basic-info">
										<p class="bold">Billing:</p>
										<?php echo (!empty($value['ccType']) ? "<p>" . $value['ccType'] . "</p>" : ''); ?>
										<?php
										if (mb_strtolower($value['ccType']) == 'brimar') {
											echo '<p>**** **** ' . $value['lastFourCcNum'] . '</p>';
										} else if (mb_strtolower($value['ccType']) == 'paypal') {
											echo '';
										} else {
											echo '<p>**** **** **** ' . $value['lastFourCcNum'] . '</p>';
										}
										?>
									</div>
<?php
								// Only show billing address if this is not paypal
								if (mb_strtolower($value['ccType']) != 'paypal') {
?>
									<div class="order-details-basic-info">
										<p class="bold">Billing Address:</p>
										<?php echo "<p>" . $value['billing_first_name'] . " " . $value['billing_last_name'] . "</p>"; ?>
										<?php echo (!empty($value['billing_company']) ? "<p>" . $value['billing_company'] . "</p>" : ''); ?>
										<?php echo "<p>" . $value['billing_street_address'] . "</p>"; ?>
										<?php echo (!empty($value['billing_suburb']) ? "<p>" . $value['billing_suburb'] . "</p>" : ''); ?>
										<?php echo "<p>" . $value['billing_city'] . ", " . $value['billing_state'] ." ". $value['billing_postcode'] . "</p>"; ?>
										<?php
											$address_bill_country=$objCountry->CountryCodeList($value['billing_country']);
											if(!empty($address_bill_country)) { echo "<p>" . $address_bill_country['countries_name'] . "</p>"; }?>
									</div>
<?php
								}
?>
									<p><a href="<?php echo $invoice . '?orderno=' . $value['order_no']; ?>" class="button blue" target="_blank">Print Invoice</a></p>
								</div>
								<div class="order-details-table-wrap">
<?php

							//Grab the products from this order
							$orderedCart = new Cart($value['hash'], null, false, true);
							$orderProducts = $orderedCart->products;

								if (count($orderProducts) > 0) {
?>
									<table>
										<thead>
											<tr class="items-list">
												<th class="text-center span-5">Item Image</th>
												<th class="span-3">Item Description</th>
												<th>Quantity</th>
												<th>Each</th>
												<th>Price</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
<?php
										$reorder_all = true;
                                        $i = 0;

										//Loop through all the products
										foreach($orderProducts as $orderProduct) {

											// Get the product info
											$product = new Page('product', $orderProduct->productId);
											$product_details = ProductPage::create($orderProduct->productId);

											// Get the price based on the ordered sku code, but the NEW product number. This way if the product
											// Number has changed, we will not receive prices and can invalidate reordering the product.
											// Note that the prices displayed will still use the historic numbers, this new price is only for validation.
											$current_price_array = $product_details->getProductFromPrice($orderProduct->skuCode, $product_details->getProductNumber());

											$inventory = $orderProduct->inventory;
											$limited_inventory = $orderProduct->limitedInventory;
											$inStock = (!$limited_inventory || $orderProduct->quantity <= $inventory ? TRUE : FALSE);

                                            echo "<pre>".print_r($product_details->getProductNumber(), 1)."</pre>";

											// Check if the product is valid, AND if we can get a price.
											// Sometimes a custom product will reference a material that no longer exists, so checking for pricing is imperative
											if (!$product->getValidity() || !(isset($current_price_array[0]['price']) && $current_price_array[0]['price'] > 0) || !$inStock) {
												$reorder_all = false;
												$reorder_this = false;
											} else {
												$reorder_this = true;
											}

											// Iterate counter
											$i++;
											$design_adjust="Design Adjustment:";
?>
											<tr class="items-list" data-cart-product-id=<?php echo json_encode($orderProduct->cartProductId); ?> data-cart-product-qty=<?php echo json_encode($orderProduct->quantity); ?>>

<?php
///////////
// FLASH //
///////////
												//Display Item Description & Image based on tool/product type
												if ($orderProduct->type == "flash") {
?>
												<?php if (HTML_COMMENTS) {?>	<!--- Display Image for product --> <?php } ?>
													<td class="sign-thumb span-5">
<?php
														if ($reorder_this) {
?>
														<a href="<?php print htmlspecialchars($orderProduct->productLink, ENT_QUOTES, 'UTF-8');?>">
<?php
														}
?>
															<img src="<?php print CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$orderProduct->customImage['customImage'];?>" alt="<?php print htmlspecialchars($orderProduct->skuCode,ENT_QUOTES,"UTF-8");?>">
<?php
														if ($reorder_this) {
?>
														</a>
<?php
														}

?>
													</td>
													<td class="item-descriptions span-3">
													<?php if (HTML_COMMENTS) {?>	<!--- Display Skucode, size and Material for product --> <?php } ?>
														<div<?php if ($reorder_this) { echo ' class="product-name"'; }?>>
<?php
															if ($reorder_this) {
?>
															<a href="<?php print htmlspecialchars($orderProduct->productLink, ENT_QUOTES, 'UTF-8');?>">
<?php
															}
?>
																<?php print htmlspecialchars((!empty($orderProduct->nickname) ? $orderProduct->nickname : 'Product: ' . $orderProduct->skuCode),ENT_QUOTES,"UTF-8"); ?>
<?php
															if ($reorder_this) {
?>
															</a>
<?php
															}
?>
														</div>
<?php
														if (!empty($orderProduct->subtitle)) {
?>
															<div class="product-mutcd"><?php print htmlspecialchars($orderProduct->subtitle,ENT_QUOTES,"UTF-8"); ?></div>
<?php
														}
?>
														<ul class="product-details-list">
															<li><span class="bold">Item &#35;: </span><?php print htmlspecialchars($orderProduct->skuCode,ENT_QUOTES,"UTF-8"); ?></li>
															<li><span class="bold">Size: </span><?php print htmlspecialchars($orderProduct->size,ENT_QUOTES,"UTF-8");?> </li>
															<li><span class="bold">Material: </span><?php print htmlspecialchars($orderProduct->materialDescription,ENT_QUOTES,"UTF-8");?></li>
														</ul>
														<div>
															<div class="custom-attributes collapsed">
																<p class="additional-details-header">Additional Details</p>
																<ul>
<?php
																	//Loop through each attribute
																	foreach($orderProduct->upcharges as $upcharge) {
																		print  "<li><span class='bold'>" . $upcharge['type'] . ": </span>".htmlspecialchars($upcharge['name'], ENT_QUOTES, 'UTF-8')."</li>";
																	}

																	if ($orderProduct->designService) {
																		print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
																	} else {
																		print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>";
																	}

																	if ($orderProduct->comments != '') {
																		print "<li><span class='bold'>Instructions: </span>".htmlspecialchars($orderProduct->comments, ENT_QUOTES, 'UTF-8')."</li>";
																	}

?>																</ul>
															</div>
														</div>
													</td>
<?php
/////////////
// BUILDER //
/////////////
												} else if ($orderProduct->type == "builder" ) {
?>
												<?php if (HTML_COMMENTS) {?>	<!--- Display Image for product --> <?php } ?>
													<td class="sign-thumb span-5">
<?php
														if ($reorder_this) {
?>
														<a href="<?php print htmlspecialchars($orderProduct->productLink, ENT_QUOTES, 'UTF-8');?>">
<?php
														}
?>
															<img src="<?php print htmlspecialchars($orderProduct->customImage['customImage'],ENT_QUOTES,"UTF-8");?>" alt="<?php print htmlspecialchars($orderProduct->skuCode,ENT_QUOTES,"UTF-8");?>">
<?php
														if ($reorder_this) {
?>
														</a>
<?php
														}

?>
													</td>

												<?php if (HTML_COMMENTS) {?>	<!--- Display Skucode, size and Material for product --> <?php } ?>

													<td class="item-descriptions">
														<div<?php if ($reorder_this) { echo ' class="product-name"'; }?>>
<?php
															if ($reorder_this) {
?>
															<a href="<?php print htmlspecialchars($orderProduct->productLink, ENT_QUOTES, 'UTF-8');?>">
<?php
															}
?>
																<?php print htmlspecialchars((!empty($orderProduct->nickname) ? $orderProduct->nickname : 'Product: ' . $orderProduct->skuCode),ENT_QUOTES,"UTF-8"); ?>
<?php
															if ($reorder_this) {
?>
															</a>
<?php
															}
?>
														</div>
<?php
														if (!empty($orderProduct->subtitle)){ ?>
															<div class="product-mutcd"><?php print htmlspecialchars($orderProduct->subtitle,ENT_QUOTES,"UTF-8"); ?></div>
<?php
														}?>
														<ul class="product-details-list">
															<li><span class="bold">Item &#35;: </span> <?php print htmlspecialchars($orderProduct->skuCode, ENT_QUOTES, 'UTF-8');?></li>
															<li><span class="bold">Size: </span><?php print htmlspecialchars($orderProduct->size, ENT_QUOTES, 'UTF-8');?> </li>
															<li><span class="bold">Material: </span><?php print htmlspecialchars($orderProduct->materialDescription, ENT_QUOTES, 'UTF-8');?></li>
														</ul>
															<div class="append-bottom">
																<div class="custom-attributes collapsed">
																	<p class="additional-details-header">Additional Details</p>

																	<ul>
<?php
																		//Loop through each attribute for product
																		foreach($orderProduct->settings as $setting) {
																			$label="<li><span class='bold'>".htmlspecialchars($setting['builderLabel'],ENT_QUOTES,"UTF-8").": </span>";
																			if ($setting['builderSettingDisplay'] == true) {
																				if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {
																					print $label.htmlspecialchars($setting['builderValueText'],ENT_QUOTES,'UTF-8') . "</li>";
																				}

																			}
																		}

																		if ($orderProduct->designService) {
																			print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
																		} else {
																			print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>" ;
																		}

																		if($orderProduct->comments != '') print "<li><span class='bold'>Instructions: </span>". $orderProduct->comments."</li>";
?>
																	</ul>
																</div>
															</div>
													</td>
<?php
/////////////////////////
// STREETNAME OR STOCK //
/////////////////////////
												} else {
?>
												<?php if (HTML_COMMENTS) {?>	<!--- Display Image for product --> <?php } ?>
													<td class="sign-thumb span-5">
<?php
														if ($reorder_this || $inventory > 0) {
?>
														<a href="<?php print htmlspecialchars($orderProduct->productLink, ENT_QUOTES, 'UTF-8');?>">
<?php
														}

														if ($orderProduct->type=='stock') {
?>													<img src="<?php print IMAGE_URL_PREFIX.'/images/catlog/product/small/'.$orderProduct->productImage;?>" alt="<?php print htmlspecialchars($orderProduct->skuCode,ENT_QUOTES,"UTF-8");?>">
<?php
														} elseif($orderProduct->type=='streetname') {
?>
														 <img src="<?php print CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$orderProduct->customImage['customImage'];?>" alt="<?php print htmlspecialchars($orderProduct->skuCode,ENT_QUOTES,"UTF-8");?>">
<?php
														}

														if ($reorder_this || $inventory > 0) {
?>
														</a>
<?php
														}
?>
													</td>
												<?php if (HTML_COMMENTS) {?>	<!--- Display Skucode, size and Material for product --> <?php } ?>
													<td class="item-descriptions">
														<div<?php if ($reorder_this || $inventory > 0) { echo ' class="product-name"'; }?>>
<?php
															if ($reorder_this || $inventory > 0) {
?>
															<a href="<?php print htmlspecialchars($orderProduct->productLink);?>">
<?php
															}
?>
																<?php print htmlspecialchars((!empty($orderProduct->nickname) ? $orderProduct->nickname : 'Product: ' . $orderProduct->skuCode),ENT_QUOTES,"UTF-8"); ?>
<?php
															if ($reorder_this || $inventory > 0) {
?>
															</a>
<?php
															}
?>
														</div>
<?php
														if(!empty($orderProduct->subtitle)){ ?>
															<div class="product-mutcd"><?php print htmlspecialchars($orderProduct->subtitle,ENT_QUOTES,"UTF-8"); ?></div>
<?php
														}?>
														<ul class="product-details-list">
															<li><span class="bold">Item &#35;: </span> <?php print htmlspecialchars($orderProduct->skuCode,ENT_QUOTES,"UTF-8");?></li>
															<li><span class="bold">Size: </span><?php print htmlspecialchars($orderProduct->size,ENT_QUOTES,"UTF-8");?> </li>
															<li><span class="bold">Material: </span><?php print htmlspecialchars($orderProduct->materialDescription,ENT_QUOTES,"UTF-8");?></li>
															<? if( $orderProduct->type=='streetname' ) {?>
																<li><span class="bold">Preview: </span><span class="cartproduct-preview">Image to the left is for text confirmation purposes only. <a href="<? echo "/images/help-elements/street-name-popups/" . $orderProduct->accuracyImage ?>" class="zoom underline no-wrap">Click here</a> to see a sample of our print quality.</span></li>
															<? } ?>
														</ul>
<?php
////////////////
// STREETNAME //
////////////////
													if ($orderProduct->type == 'streetname') {
?>
														<div>
															<div class="custom-attributes collapsed">
																<p class="additional-details-header">Additional Details</p>
																<ul>
<?php

																	// Display mounting option
																	foreach($orderProduct->upcharges as $upcharge) {
																		print  "<li><span class='bold'>" . $upcharge['type'] . ": </span>".htmlspecialchars( $upcharge['name'],ENT_QUOTES,"UTF-8"). "</li>";
																	}

																	//Loop through each attribute
																	foreach ($orderProduct->getAdditionalDetails() as $key => $att_value) {
																		print  "<li><span class='bold'>".$key.": </span>".htmlspecialchars($att_value,ENT_QUOTES,"UTF-8"). "</li>";
																	}
																	//Custom image
																	if (!empty($orderProduct->fileUpload['name']))
																		print "<li><span class='bold'>Custom Image Uploaded:</span> Yes</li>";

																	//Design adjustment
																	if ($orderProduct->designService){
																		print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
																	} else {
																		print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>" ;
																	}

																	if($orderProduct->comments!='') print "<li><span class='bold'>Instructions: </span>".htmlspecialchars($orderProduct->comments,ENT_QUOTES,"UTF-8")."</li>";

?>
																</ul>
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
													<td class="qty-forms-in-shopping-cart">
													<?php print htmlspecialchars($orderProduct->quantity, ENT_QUOTES, "UTF-8");?>
													</td>
												<?php if (HTML_COMMENTS) {?>	<!--- Display Price for product --> <?php } ?>
													<td class="price-per">
														$<span class="unitprice"><?php print number_format($orderProduct->unitPrice, 2);?></span>
													</td>
												<?php if (HTML_COMMENTS) {?>	<!--- Display Quantity for product --> <?php } ?>
													<td class="price-total last">
														$<span class="totalprice"><?php print number_format($orderProduct->totalPrice, 2);?></span>
													</td>
													<td>
<?php
														if ($reorder_this) {
?>
															<a href='<?php echo '?action=reorder&orderno=' . $value['order_no'] . '&pid=' . $orderProduct->id; ?>' class='green button'>Reorder Item</a>
<?php
														} else if( $inventory > 0 && $product->getValidity() ) {
?>
															<p class="oh-outofstock"><span class="bold">ONLY <?=$inventory?> <br>IN STOCK</span> </br><span class="small-text">Please order a lower quantity or similar product.</span></p>
<?php
														} else {
?>
															<p class="oh-outofstock"><span class="bold">This product is no longer available.</span> <br><span class="small-text">Please order a similar product.</span></p>
<?php														}
?>
													</td>
											</tr>
<?php

										}
?>
										</tbody>
									</table>

<?php
							}
?>
								</div>
									<div class="right-side text-right">

<?php
										$total = $value['total_amount'];
										$tax = $value['sales_tax'];
										$shipping = $value['shipping_charges'];
										$coupon = $value['coupon_value'];
										$subtotal = $total - $tax - $shipping + $coupon;
?>

										<p class="right-side append-bottom">
<?php
											if ($reorder_all) {
?>
											<a href='<?php echo '?action=reorder&orderno=' . $value['order_no']; ?>' class='small green button'>
												Reorder All Items
											</a>
<?php
											}
?>
										</p>
										<p class="clear"><span class="bold">Subtotal:</span> $<?php echo number_format($subtotal, 2); ?></p>
										<p><?php if ($tax > 0) { ?><span class="bold">Tax:</span> $<?php echo number_format($tax, 2); } ?></p>
										<p><?php if ($coupon > 0) { ?><span class="bold">Discount:</span> $<?php echo number_format($coupon, 2); } ?></p>
										<p><?php if ($shipping > 0) { ?><span class="bold">Shipping:</span> $<?php echo number_format($shipping, 2); } ?></p>
										<p class="font-18 prepend-top-5"><span class="bold">Order Total:</span> $<?php echo number_format($total, 2); ?></p>

									</div>

							</div>
						</td>
					</tr>
				</tbody>
				<?php
						}
?>
			</table>

<?php
				//If the user has more than 5 orders, give them a total and a view all button
				if ($order_total > 5 && $page->getNickname() != 'orderhistory') {
					echo "<a href='" . $link->getUrl() . "' class='right-side'>View Full Order History >></a>";
				}

			//The user has no order history
			} else {
				print "<p class='clear'>You have no orders in your order history.</p>";
			}
?>
	</div>
