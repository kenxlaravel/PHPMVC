	<!-- Product tabs
	================================================================================================
	-->

<?php
	$accessories = $ObjPageProduct->getProductAccessoriesList();
	$accessories_count = count($accessories);


	//If we have content from any of the three tabs, we need to render tabs
	if($ObjPageProduct->getDetailsTabContent() || $ObjPageProduct->getMaterialsTabContent() || $accessories_count > 0) {
		$val = "0";
	}
?>


	<!-- Draw the tabs -->
	<div class="tabs prepend-top clear" id="item-detail-slide-navigation">

<?php
		//If we have accessories, display the tab
		if ($accessories_count > 0) {
?>
				<a href="#sign_accessories" class="selected tab-control">Sign<br />Accessories</a>
<?php
		}

		//If we have details, display the tab
		if ($ObjPageProduct->getDetailsTabContent()) {
?>
				<a href="#sign_details_regulations" class="tab-control">Sign Details<br />&amp; Regulations</a>
<?php
		}

		//If we have materials, display the tab
		if ($ObjPageProduct->getMaterialsTabContent()) {
?>
				<a href="#material_descriptions" class="tab-control">Material Descriptions<br />&amp; Sign Application</a>
<?php
		}
?>

	</div>




	<!-- Tab content -->
	<div id="additional-details-slider" class="span-24 custom-builder-details-accessories">

<?php
		//If we have accessories, display the contents
		if ($accessories_count > 0) {
?>
			<div class="panel bottom-rounded" id="sign_accessories">
<?php


				//Start accessories loop
				$i="20";


				foreach ($accessories as $key => $accessories_data) {


				$accessories_id = $accessories_data['accessory_product_id'];
				$product_accessories = $ObjPageProduct->getAccessories($accessories_id);

				//Start product accessories data loop
				foreach ($product_accessories as $key => $accessories_data) {
				$last_material_code = "";

				$apid = $accessories_data['product_number'];

				$productsattributes = $ObjProductAttributes->ProductAttributesAccessoriesList($apid);

				if (!empty($productsattributes)) {
?>

				<div class="accessory_listing prepend-top append-bottom">

					<!--<p>There are no accessories availiable for this item </p>-->
					<div class="span-5 accesory-thumbnail">
						<div class="thumbnail-container">
							<img src="<?php print website.$pathImgSmallProduct . $accessories_data['image1_thumbnail']; ?>"
								 alt="composite_post" swidth="100" sheight="100"/>
						</div>

					</div>

					<div class="span-18 last append-bottom">
						<div class="accesory-title">
							<p><strong><?php print $accessories_data['by_legend']; ?></strong><br/>
								<?php print $accessories_data['product_nickname']; ?> </p>
						</div>
						<table class="accesory-pricing-table" cellspacing="0">
							<tr>
								<th>Size</th>
								<th class="description">Materials</th>

								<?php

								/*(
                                $apid=$accessories_data['product_number'];
                                $productsattributes_first1=$ObjProductAttributes->ProductAttributesAccessoriesFirstList($apid);
                                $productssubattributes_first=$ObjProductSubAttributes->ProductGetSubAttributes($productsattributes_first1['sku_code'] , $apid);
                                $count_colspan = count($productssubattributes_first);
                                */

								$productsattributes_first1 = $ObjProductAttributes->ProductAttributesAccessoriesList($apid);



								foreach ($productsattributes_first1 as $material_key => $material_value) {

									$i++;
									$attributes_id = $material_value['material_code'];
									$sku_code = $material_value['name'];
									$product_number = $material_value['product_number'];

									$productsattributes_first1 = $ObjProductSubAttributes->ProductGetSubAttributes($sku_code, $product_number);

									$count_colspan = count($productsattributes_first1);
								}

								?>
								<th colspan="<?php print $count_colspan; ?>">Price</th>
								<th class="add-to-cart-th">Enter Quantity</th>
							</tr>

							<?php
							//Start product material code


							if (!empty($productsattributes)) {
							//Start product material loop
							foreach ($productsattributes as $material_key => $material_value) {

							$i++;
							$attributes_id = $material_value['material_code'];
							$sku_code = $material_value['name'];
							$product_number = $material_value['product_number'];

							$productssubattributes = $ObjProductSubAttributes->ProductGetSubAttributes($sku_code, $product_number);
							$subattributes_count = count($productssubattributes);


							if ($material_value['streetsign_accessory_display'] == TRUE && $last_material_code != $material_value['accessory_material_header']) {

								?>
								<tr class="quantity-break">
									<td colspan="2">
										<span><?php print $material_value['accessory_material_header']; ?></span></td>
									<?php
									$row = 0;

									//Quantity
									foreach ($productssubattributes as $key => $value_qty) {
										?>
										<td class="price">
											<?php
											if ($value_qty['minimum_quantity'] != '0') {
												print $value_qty['minimum_quantity'];
											}
											?>
										</td>
									<?php
									} //End quantity foreach
									?>
									<td class="price"></td>
								</tr>
							<?php
							} //end if condition

							$last_material_code = $material_value['accessory_material_header'];
							?>

							<tr class="data-row product-addtocart-row"
								title="<?php echo htmlspecialchars($material_value['name'], ENT_QUOTES, 'UTF-8'); ?>">
								<td class="stock"><?php print $material_value['size']; ?></td>
								<td class="description"><?php print $material_value['material']; ?></td>
								<?php

								foreach ($productssubattributes as $key => $value) {
									?>
									<td class="price"><?php if ($value['price'] != '0.00') {
											print "$" . $value['price'];
										} ?></td>
								<?php
								}
								?>
								<td class="add-to-cart-form">
									<form accept-charset="utf-8" id="custom<?php print $i; ?>"
										  action="<?php print URL_PREFIX_HTTP; ?>/add-to-cart" method="post"
										  name="cartform" enctype="multipart/form-data" class="custom addtocart">
										<input type="hidden" name="type" value="stock">
										<input type="hidden" name="id" value="<?php print (int) $accessories_data['products_id']; ?>">
                                        <input type="hidden" name="sku_id" value="<?php print (int) $material_value['sku_id']; ?>">
										<input type="hidden" name="material" value="<?php print $value['material_code']; ?>">
										<input type="text" class="add-to-cart-input text quantity" maxlength="10" id="quantity<?php print $i; ?>" name="qty" value="">
										<?php
										//If we have any upcharges
										if (isset($upcharges) && is_array($upcharges)){
										if ($upcharge_count > 0) {
										$type = '';
										$count = 0;
										$typecount = -1;
										?>
										<div class="product-options">
											<p class="h4">Options</p>
											<?php
											//Loop through the upcharges
											foreach ($upcharges as $key => $value) {

											$count++;

											//If this is a new option type, output a new fieldset with a legend
											if ($value['type'] != $type) {

											$typecount++;
											$type = $value['type'];

											//If this isn't the first row, close out the previous one
											if ($count != 1) {
											?>
											</fieldset>
										</div>
									<?php
									}
									?>
															<div>
																<fieldset class="product-option">
																	<legend><?php echo $value['type'] . ' Options'; ?></legend>
<?php
									}

									//Output the input (despite that being an oxymoron)
									?>
														<div><label><input type="radio" name="upcharges[<?php echo $typecount; ?>]" value="<?php echo $count; ?>"<?php if ($value['type'] != $type) {
										echo " checked";
									} ?>><?php echo $value['name']; ?></label></div>
<?php
									//If this is the last option, close everything out
									if ($count == $upcharge_count) {
										?>
										</fieldset>
										</div>
									<?php
									}

									}
									?>
					</div>
					<?php
					}
					}
					?>

					<button type="submit" class="add-to-cart-button js" value="submit" title="add-to-cart">Add To
						Cart
					</button>
					</form>
					</td>
					</tr>
					<?php
					}//end product material loop
					}
					?>
					</table>
				</div>

			</div>
	<hr/>


	<?php
	}//end product accessories data loop
	}
	}//end accessories loop


				if ($accessories_count > 0) {
?>
					</div>
					<!-- End accessories panel-->
<?php
				}
		}
?>


<?php
		//If we have deatails info, output the tab contents
		if($ObjPageProduct->getDetailsTabContent()) {
			print "<div class='panel bottom-rounded' id='sign_details_regulations'>";
			include $PathContentDetailTab.$ObjPageProduct->getDetailsTabContent();
			print "</div>";
		}
?>



<?php
		//If we have materials info, output the tab contents
		if($ObjPageProduct->getMaterialsTabContent()) {
			print "<div class='panel bottom-rounded' id='material_descriptions'>";
			include $PathContentMaterialsTab.$ObjPageProduct->getMaterialsTabContent();
			print "</div>";
		}
?>
	</div>