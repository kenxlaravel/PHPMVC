<?php

	$accessories = $ObjPageProduct->listAccessories();
	$accessoriescount = sizeof($accessories['accessories']);

	//Counts and iterators
	$i="20";
	$c=0;


	//If there are any accessories, continue
	if ($accessoriescount > 0) {
?>

		<div id="accessories" class="clearfix">
			<h4 class="divider">Accessories</h4>


<?php

		foreach($accessories['accessories'] as $key => $accessories_data) {

			$link = Page::create('product', $accessories_data['id'], $accessories_data);
			$TmpObjPageProduct = ProductPage::create($accessories_data['id']);

			//Iterate
			$c++;

			//Start a new row if we need to
			if ($c % 3 == 1) {
?>
				<section class="span-24 clearfix">
<?
			}
?>
			<div class="accesory-product-container <?php if($c % 3 == 0) { print "last"; } ?>">
				<div class="thumbnail">

					<a href="<?php echo $link->getUrl(); ?>">
						<img src="<?php isset($accessories_data['small_image'])? print website.$Path_Img_Small_product.$accessories_data['small_image'] : "";?>" />
					</a>

					<div class="product-highlight">
						<p class="h4">
							<?php print $accessories_data['nickname'];?>
						</p>
						<?php isset($accessories_data['material']) ? print '<p>' . $accessories_data['material'] . '</p>' : ""; ?>
					</div>

				</div>

				<div class="product-table">
					<table border="0">
<?php
					$apid=$accessories_data['product_number'];
					$productsattributes_first1=$ObjProductAttributes->ProductAttributesAccessoriesFirstList($apid);

					$productssubattributes_first=$ObjProductSubAttributes->streetsignProductGetSubAttributes($productsattributes_first1['sku_code'] , $apid);
					$count_colspan=count($productssubattributes_first);

?>
						<tr>
							<th>Size</th>
							<th class="price_header">Price</th>
							<th>Enter Qty</th>
						</tr>

<?php
						/*start product material code */
						$productsattributes=$ObjPageProduct->streetsignProductAttributesAccessoriesList($apid);

						/*start product material loop*/
						foreach($productsattributes as $material_key => $material_value) {

							$i++;
							$attributes_id=$material_value['material_id'];
							$sku_code = $material_value['sku_code'];
							$product_no = $material_value['product_number'];

							$productssubattributes=$ObjProductSubAttributes->streetsignProductGetSubAttributes($sku_code ,$product_no);

							$subattributes_count=count($productssubattributes);
							//$upcharges = $ObjPageProduct->getUpchargesByMaterialCode($attributes_id);
							//$upcharge_count = count($upcharges);

							if($material_value['streetsign_accessory_display']==TRUE) {
?>
								<tr class="quantity-break">
									<td></td>
									<td class="price">
										<table>
											<tr>
<?php
												$row=0;
												foreach($productssubattributes as $key => $value_qty) {
?>
													<td class="price" >
<?php
													if ($value_qty['quantity']!='0') {
														print $value_qty['quantity'];
													}
?>
													</td>
<?php
												}
?>
											</tr>
										</table>
									</td>
									<td class="add-to-cart"></td>
								</tr>
<?php
							}
?>
								<tr class="data-row product-addtocart-row pricing-row" title="<?php echo htmlspecialchars($material_value['sku_code'], ENT_QUOTES, 'UTF-8');?>">

									<td>
										<?php print $material_value['size_name'];?>
									</td>
									<td class="price">
										<table>
											<tr>
<?php

												foreach($productssubattributes as $key => $value) {
?>
													<td>
														<?php if($value['price']!='0.00'){print "$".$value['price'];}?>

													</td>
<?php
												}
?>
											</tr>
										</table>
									</td>
									<td class="add_to_cart_containter add-to-cart-form">
										<form accept-charset="utf-8" id="custom<?php print $i;?>" action="<?php print URL_PREFIX_HTTP;?>/add-to-cart" method="post" name="cartform" enctype="multipart/form-data" class="custom addtocart validate-me">
											<input type="hidden" name="type" value="stock">
											<input type="hidden" name="id" value="<?php print (int) $accessories_data['id'];?>">
                                            <input name="sku_id" type="hidden" value="<?php print htmlspecialchars((int)$material_value['sku_id'], ENT_QUOTES, 'UTF-8'); ?>">
											<input type="hidden" name="material" value="<?php print $value['material_code'];?>">
											<input type="text" class="add-to-cart-input text quantity" maxlength="10" id="quantity<?php print $i;?>" name="qty" value="">
                                            <input type="hidden" name="product_state_url" value="<?php print ProductStateParameter::encode($productStateParameters)?>">
                                            <?php if ($productStateParameters['sourceProduct']) : ?><input name="source_product_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                                            <?php if ($productStateParameters['sourceProductRecommendation']) : ?><input name="source_product_recommendation_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceProductRecommendation'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                                            <?php if ($productStateParameters['sourceAccessoryFamilyProduct']) : ?><input name="source_accessory_familyProduct_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceAccessoryFamilyProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                                            <?php if ($productStateParameters['sourceInstallationAccessory']) : ?><input name="source_installation_accessory_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceInstallationAccessory'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                                            <?php if ($productStateParameters['sourceLandingProduct']) : ?><input name="source_landing_product_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceLandingProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                                            <?php if ($productStateParameters['sourceSubcategoryProduct']) : ?><input name="source_subcategory_product_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceSubcategoryProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
<?php
											//If we have any upcharges
											if ($upcharge_count > 0) {
												$type = '';
												$count = 0;
												$typecount = -1;
?>
<!--											<div class="product-options">-->
<!--												<p class="h4">Options</p>-->
<?php
//												//Loop through the upcharges
//												foreach($upcharges as $key => $value) {
//
//													$count++;
//
//													//If this is a new option type, output a new fieldset with a legend
//													if ($value['type'] != $type) {
//
//														$typecount++;
//														$type = $value['type'];
//
//														//If this isn't the first row, close out the previous one
//														if ($count != 1) {
//?>
<!--															</fieldset>-->
<!--														</div>-->
<?php
//														}
//?>
<!--														<div>-->
<!--															<fieldset class="product-option">-->
<!--																<legend>--><?php //echo $value['type'] . ' Options';?><!--</legend>-->
<?php
//													}
//
//													//Output the input (despite that being an oxymoron)
//?>
<!--													<div><label><input type="radio" name="upcharges[--><?php //echo $typecount; ?><!--]" value="--><?php //echo $count; ?><!--"--><?php //if ($value['type'] != $type) { echo " checked"; } ?><!-->--><?php //echo $value['name']; ?><!--</label></div>-->
<?php
//													//If this is the last option, close everything out
//													if ($count == $upcharge_count) {
//?>
<!--															</fieldset>-->
<!--														</div>-->
<?php
//													}
//
//												}
//?>
<!--											</div>-->
<?php
											}
?>

											<button type="submit" class="add-to-cart-button" value="submit" title="add-to-cart">Add</button>
										</form>
									</td>
								</tr>
<?php
						}
?>
					</table>

					<div class="description"></div>
				</div>
			</div>
<?
			//If this is the end of a row, or the last product, end the section
			if ($c % 3 == 0 || $c == $accessoriescount) {
?>
				</section>
<?
			}

		}

?>
</div>
<?php
	}
?>