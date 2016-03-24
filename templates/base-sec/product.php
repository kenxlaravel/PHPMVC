<?php
	/**
	 * The product page displays all information about each product, including:
	 * name, image, video, description, specs, materials, accessories, and details.
	 *
	 * It also allows adding products and accessories to cart.
	 *
	 * @author  Daniel Hennion <daniel@brimar.com>
	 * @since   09.28.2012
	 */
/*
    echo __FILE__;
    exit;*/

	//Check to see if this is a custom product
	if (!empty($_REQUEST['cpi'])) {
		$custom_product = $CustomProductObj->GetCustomProduct($_REQUEST['cpi']);
		$custom_count = count($custom_product);
	} else {
		$custom_count = 0;
	}

	//Check to see if this is a tweakable product
	$tweakable = $ObjPageProduct->isTweakAble();
//	$fully_ulrecognized = $ObjPageProduct->fully_ulrecognized;
//	$ulrecognized = $ObjPageProduct->flags['ulrecognized'];

	//Check to see if this is a multilingual product
	if ($ObjPageProduct->isMultiLingual() ) {
		$multilingual = TRUE;
		$languages = $ObjPageProduct->getLanguages();
	}

	//Check to see if we have any accessories
	$accessories = $Product->getProductAccessories();
	$accessories_count = count($accessories);
	$related_products_count = count($Product->getProductRecommendations());

?>

<?php if (HTML_COMMENTS) {?><!-- Main content column
====================================================================================================
--><?php } ?>
<div id="column-2" class="span-18 last">


	<?php if (HTML_COMMENTS) {?><!-- Header (product name)
	================================================================================================
	--><?php } 
	?>
	<h2 class="h4 h4-rev pad-left-15"><?php echo htmlspecialchars($breadcrumbs[4]['text'], ENT_QUOTES, 'UTF-8') ?></h2>



	<?php if (HTML_COMMENTS) {?><!-- Product image and description
	================================================================================================
	--><?php } ?>
	<div id="item-image-and-description" class="love">



		<?php if (HTML_COMMENTS) {?><!-- Product image
		============================================================================================
		--><?php } ?>
		<div id="item-thumbnail-container" class="span-8">

			<?php if (HTML_COMMENTS) {?><!-- Image --><?php } ?>
			<div id="item-image-reviews-availibility">
				<div id="item-thumbnail">
<?php
					//If we have a custom product (eg: someone built a sign with the flash tool and may now add it to the cart)
					if ($custom_count > 0) {

						$account_link = new Page('my-account');
						foreach($custom_product as $key => $custom_product_value) {
?>
							<a class="zoom" id="zoom-image" href="<?php echo URL_PREFIX_HTTP . "/design/save/previews/" . $custom_product_value['custom_image']; ?>">

							<img src="<?php echo URL_PREFIX_HTTP . "/design/save/previews/medium/" . $custom_product_value['custom_image']; ?>" /></a>
<?php
						}
?>
						<a class="zoom" id="zoom-link" href="<?php echo URL_PREFIX_HTTP . "/design/save/previews/" . $custom_product_value['custom_image']; ?>"><i class="sprite sprite-zoom-in"></i><span>Zoom Image</span></a>

						<div id="custom-sign-edit-preview" class="append-bottom prepend-top">

							<a href="<?php echo $page->getUrl(); ?>?status=edit&amp;design_id=<?php echo $custom_product_value['design_id']; ?>" class="button green small-text">Edit Design</a>

						</div>
				</div>
			</div>
<?php

					} else {
?>
						<a class="zoom" id="zoom-image" href="<?php echo $ObjPageProduct->getImagePath("zoom") . htmlspecialchars($ObjPageProduct->image_zoom, ENT_QUOTES, 'UTF-8'); ?>">
							<img src="<?php echo $ObjPageProduct->getImagePath("medium") . htmlspecialchars($ObjPageProduct->image2, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($ObjPageProduct->image2_title, ENT_QUOTES, 'UTF-8'); ?>" />
						</a>

				</div>
			</div>

			<?php if (HTML_COMMENTS) {?><!-- Zoom --><?php } ?>
			<a class="zoom" id="zoom-link" href="<?php echo $ObjPageProduct->getImagePath("zoom") . htmlspecialchars($ObjPageProduct->image_zoom, ENT_QUOTES, 'UTF-8'); ?>">
				<i class="sprite sprite-zoom-in"></i><span>Zoom Image</span>
			</a>
<?php
					}
			//If there is ul_recognized in the current product display it
			if($ulrecognized) {
			$link_ul_recognized = new Page('ul-recognized');
?>

			<div class="ul-recognized-wrapper left-side">
				<img src="/images/icons/ul-recognized-red.png" class="left-side" />
				<div class="ul-note small-text left-side first-margin"><?php if(!$fully_ulrecognized){ echo 'Some';}else{ echo 'The';}?> products below are UL&reg;&nbsp;Recognized. <span class="ul-toolip-link fake-link underline">Learn more.</span>
					<div class="ul-tooltip">
						<p class="h1" style="color:#90B557;">UL&reg; Recognized Components</p>
						<p><?php if(!$fully_ulrecognized){ echo 'Some of the';}else{ echo 'The';}?> products on this page are recognized under the Component Recognition Program of UL.</span> The Machine Safety Labels sold by SafetySign.com have been evaluated by UL in the PGDQ2 category in accordance with ANSI/UL 969 and CSA C22.2 No. <a href="<?php echo $link_ul_recognized->getUrl(); ?>" class="underline">Learn more about UL&reg; Recognition</a>.</p>
					</div>
				</div>

			</div>
<?php
			}
?>
		</div>



		<?php if (HTML_COMMENTS) {?><!-- Product details
		============================================================================================
		--><?php } ?>
<?php
		//Get the description types as labels for the product descriptions
		//$heading = $ObjPageProduct->ProductDescriptionType();
?>
		<div id="item-name-and-details" class="last">

			<?php if (HTML_COMMENTS) {?><!-- List --><?php } ?>
			<dl class="clearfix">


				<?php if (HTML_COMMENTS) {?><!-- Header 1 --><?php } ?>

				<?php if (HTML_COMMENTS) {?><!-- Availability --><?php } ?>
<?php

				if ( isset($_GET['type']) && $_GET['type'] != 'c' && $ObjPageProduct->getToolTypeName() === 'stock') {
					$in_stock_msg = ($ObjPageProduct->limitedAvailability ? 'Limited Availability <span class="small-text note-text">(See Below)</span>' : 'In Stock');
?>
					<dt class="sizes">Availability:</dt>
						<dd><?php echo ($ObjPageProduct->in_stock ? $in_stock_msg : '<span class="inventory-alert">Out of Stock</span>'); ?></dd>
<?php
				}
?>
			</dl>

<?php if ($ObjPageProduct->getNote() != '') {
?>
				<p class="pad-left-10 note append-bottom prepend-top-5">Note: <?php echo htmlspecialchars($ObjPageProduct->getNote(), ENT_QUOTES, 'UTF-8'); ?></p>
<?php
				}
?>

			<?php if (HTML_COMMENTS) {?><!-- More information --><?php } ?>
			<?php
			if($ObjPageProduct->getDetailsTabContent() || $ObjPageProduct->getMaterialsTabContent() || $accessories_count > 0) { ?>
			<div id="colors" class="clearfix">
				<p>
					<a id="item-detail-slide-link" href="#item-detail-slide-navigation">
<?php
							if ($accessories_count > 0 && !($ObjPageProduct->getDetailsTabContent()) && !($ObjPageProduct->getMaterialsTabContent())) {
?>
								View Accessories
<?php
							} else {
								if ($accessories_count > 0 ) {
?>
									Accessories &amp;
<?php
								}
?>
								More Information
<?php
							}
?>
					</a>
				</p>
			</div>
			<?php } ?>

		</div>
	</div>
	<?php if (HTML_COMMENTS) {?><!-- End product image and description --><?php } ?>


<?php
	//THREE BANNER CASES: TWEAKABLE, MULTILINGUAL, TWEAKABLE AND MULTILINGUAL ==========================

		//Case multilingual and tweakable
		if ($multilingual && $tweakable) {
?>
			<div class="tweaklang-options span-18 last append-bottom">
				<div class="span-11 last">
					<p class="h4">Want to make a change to this <?php echo (isset($ObjPageProduct->product_type) ? htmlspecialchars(mb_strtolower($ObjPageProduct->product_type), ENT_QUOTES, 'UTF-8') : 'sign'); ?>?</p>
					<p>Translate it or edit to suit your needs. Different pricing may apply.</p>
				</div>
				<div class="span-7 last tweaklang-inputs">
					<a href="#language-popup" class="tweakable-button fancybox" id="choose-language">Choose Language</a>
					<a href="<?php echo $page->getUrl() . '?mode=tweak'; ?>" class="tweakable-button">Tweak <?php echo (isset($ObjPageProduct->product_type) ? htmlspecialchars(ucwords($ObjPageProduct->product_type), ENT_QUOTES, 'UTF-8') : 'Sign'); ?></a>
				</div>
			</div>
<?php
		}


		//Case multilingual
		else if ($multilingual && !$tweakable) {
?>
		<?php if (HTML_COMMENTS) {?><!-- Multi-Lingual --><?php } ?>
		<div class="tweaklang-options span-18 last append-bottom">
			<div class="span-13">
				<p class="h4"><?php echo (isset($ObjPageProduct->product_type) ? htmlspecialchars(ucwords($ObjPageProduct->product_type), ENT_QUOTES, 'UTF-8') : 'Sign'); ?> Translation (optional)</p>
				<p>Click the button to the right to choose a language or <a href="mailto:sales@safetysign.com?subject=Language%20request%20for%20item%20<?php echo htmlspecialchars($ObjPageProduct->number, ENT_QUOTES, 'UTF-8'); ?>">email us</a> for custom options.</p>

			</div>
			<div class="span-4 last tweaklang-inputs">
				<a href="#language-popup" class="tweakable-button fancybox" id="choose-language">Choose Language</a>
			</div>
		</div>
<?php
		}

		//Case tweakable
		else if (!$multilingual && $tweakable) {
?>
			<div class="tweaklang-options span-18 last append-bottom">
				<div class="span-13">
					<p class="h4">Want to make a change to this <?php echo (isset($ObjPageProduct->product_type) ? htmlspecialchars(mb_strtolower($ObjPageProduct->product_type), ENT_QUOTES, 'UTF-8') : 'sign'); ?>?</p>
					<p>Edit to suit your needs using our custom tool. Custom pricing will apply.</p>
				</div>
				<div class="span-4 last tweaklang-inputs">
					<a href="<?php echo $page->getUrl() . '?mode=tweak'; ?>" class="tweakable-button">Tweak <?php echo (isset($ObjPageProduct->product_type) ? htmlspecialchars(ucwords($ObjPageProduct->product_type), ENT_QUOTES, 'UTF-8') : 'Sign'); ?></a>
				</div>
			</div>
<?php
		}
		//END BANNER CASES =================================================================================
?>

	<?php if (HTML_COMMENTS) {?><!-- Price table
	================================================================================================
	--><?php }?>
	<?echo (!empty($ObjPageProduct->sale_percentage) && $ObjPageProduct->in_stock ? "<p class='prepend-top'><span class='product-percentage-saved'>ON SALE NOW</span> <span class='bold'>Save " . $ObjPageProduct->sale_percentage . "&#37;</span> <span class='note-text'>Sale prices shown below. While supplies last.</span></p>" : "");?>
	<?php if (!$ObjPageProduct->in_stock){
		$ObjPage = new Page('custom');
	?>
		<p class='prepend-top'><span class='product-percentage-saved'>THIS ITEM IS OUT OF STOCK</span>
		<span class='note-text'>Please choose a similar item or <a href="<?=$ObjPage->getUrl()?>" class="underline">create a custom item</a>. </span></p>
	<?php } ?>
	<div class="span-18 last quantity-pricing-table append-bottom">

		<table class="item-options">
			<thead>
					<?php if (HTML_COMMENTS) {?><!-- Table headings --><?php } ?>
				<?if($ObjPageProduct->in_stock){?>
					<tr class="table-header">
						<th>Size</th>
						<th class="sign-material-type">Materials</th>
						<th>Quantity/Price</th>
						<th class="add-to-cart-th">Enter Quantity</th>
					</tr>
				<?}?>
			</thead>
		</table>



<?php

						$pid = $ObjPageProduct->getProductNumber();
						$productsattributes=$ObjPageProduct->getProductsSkuDescription();
						$first_attributes_id = $productsattributes[0]['material_code'];
						$first_sku_code = $productsattributes[0]['sku_code'];
						$productssubattributes_first = $ObjPageProduct->getProductFromPrice($first_sku_code , $pid);

						//Iterations
						$i = -1;

						//Start productsattributes loop
						foreach ($productsattributes as $key => $value) {

							$attributes_id = $value['material_code'];
							$sku_code = $value['sku_code'];
							$product_number = $value['product_number'];
							$ul_recognized = $value['ul_recognized'];

							$productssubattributes = $ObjPageProduct->getProductFromPrice($sku_code ,$product_number);
							$upcharges = $ObjPageProduct->getUpchargesByMaterialCode($attributes_id);
							$upcharge_count = count($upcharges);

							//Increment iterations
							$i++;

							//Show quantity row if set to show,
							if ($value['quantity_show'] == 'Y' || $i == 0) {

								//Keep track of the previous type of material so we can automatically start new quantity rows if it changes.
								if (!empty($value['material'])) {
									$previous_material = $value['material'];
								}
?>
						<table class="item-options scrollable-price-grid">
							<thead>
								<tr class="quantity-break">

									<?php if (HTML_COMMENTS) {?><!-- Material --><?php } ?>
									<td colspan="2" class="material-head">
										<span><?php echo htmlspecialchars($value['material'], ENT_QUOTES, 'UTF-8'); ?></span>
									</td>

									<?php if (HTML_COMMENTS) {?><!-- Price --><?php } ?>
									<td class="price">
										<table>
											<tr>
<?php
												//Loop through the sub attributes, and output a quantity number in the price column if
												//different quantities have price discounts.
												foreach ($productssubattributes as $key2 => $value2) {
?>
													<td><?php if($value2['quantity'] != '0') { echo htmlspecialchars($value2['quantity'], ENT_QUOTES, 'UTF-8'); }?></td>
<?php
												}
?>
											</tr>
										</table>
									</td>

									<?php if (HTML_COMMENTS) {?><!-- Quantity (sadly, nothing ever goes in this one)--><?php } ?>
									<td class="quantity-break-placeholder"></td>

								</tr>
							</thead>
							<tbody>
<?php
							}

					$in_stock = '';

					//Show in-stock note if there exists only a limited amount of that product sku
					if($value['limited_inventory']){
						$in_stock = htmlspecialchars($value['inventory'], ENT_QUOTES, 'UTF-8').' left in stock';
						$inventory_class = "showInventoryLevel";
					} else {
						$in_stock = 'In Stock';
						$inventory_class = "bold";
					}
?>
						<tr class="data-row product-addtocart-row" title="<?php echo htmlspecialchars($value['sku_code'], ENT_QUOTES, 'UTF-8');?>">

							<?php if (HTML_COMMENTS) {?><!-- Size --><?php } ?>
							<td class="item-size"><?php echo htmlspecialchars($value['size'], ENT_QUOTES, 'UTF-8'); ?></td>

							<?php if (HTML_COMMENTS) {?><!-- Material --><?php } ?>
							<td class="sign-material-type"><?php echo htmlspecialchars($value['material_description'], ENT_QUOTES, 'UTF-8').'<br/>';?>
							<?php if($ul_recognized) {?><span class="note-text"> &bull;&nbsp;UL&reg;&nbsp;Recognized</span><br/><span class="bold"></span><?php }?>
							<span class="<?=$inventory_class?>"><?echo ($ObjPageProduct->limitedAvailability ? $in_stock : '')?></span>
							</td>

							<?php if (HTML_COMMENTS) {?><!-- Price --><?php } ?>
							<td class="price">
								<table>
									<tr>
<?php
										//Loop through and output prices
										foreach ($productssubattributes as $key3 => $price_value) {
?>
											<td>
												<?php if ($price_value['price'] != '0.00') { echo "$" . htmlspecialchars($price_value['price'], ENT_QUOTES, 'UTF-8');} ?>
											</td>
<?php
										}
?>
									</tr>
								</table>
							</td>

<?php 					if (HTML_COMMENTS) { ?><!-- Add to cart form --><?php } ?>
							<td class="add-to-cart-form">
								<form accept-charset="utf-8" action="<?php echo URL_PREFIX_HTTP; ?>/add-to-cart" method="post" name="cartform" enctype="multipart/form-data" class="custom addtocart validate-me ">
									<input type="hidden" name="type" value="<?php echo htmlspecialchars($ObjPageProduct->getToolTypeName(), ENT_QUOTES, 'UTF-8'); ?>">
									<input type="hidden" name="id" value="<?php echo PAGE_ID; ?>">
									<input type="hidden" name="material" value="<?php echo htmlspecialchars($value['material_code'], ENT_QUOTES, 'UTF-8');?>">
									<input type="hidden" name="cpi" value="<?php echo htmlspecialchars($custom_product_value[custom_product_id], ENT_QUOTES, 'UTF-8');?>">
									<input type="text" class="add-to-cart-input text quantity" maxlength="10" name="qty" value="" />


									<?php
										//Show design adjustment options if this is a custom product
									if ($upcharge_count <= 0) {
										if ($custom_count > 0) {
?>
											<div class="product-options">
												<p class="h4">Options</p>
												<p class="legend-style">Design Options</p>

												<label><input id="best-appearance" name="designapproval" type="radio" value="adjust" checked />Adjust My Design For Best Appearance</label>
												<p class="special-note">Our experts will review your sign, correct obvious design errors, and make adjustments to ensure optimum printing.</p>

												<label><input id="approved-design" name="designapproval" type="radio" value="approved" />Print My Design As Shown</label>
												<p class="special-note">Your sign will be printed exactly as it appears above. Please make any required adjustments before ordering.</p>
											</div>
<?php
										}
									}
									//If we have any upcharges
									else {
										$type = '';
										$count = 0;
										$typecount = -1;
?>
									<div class="product-options">
										<p class="h4">Options</p>

<?php
										//Show design adjustment options if this is a custom product
										if ($custom_count > 0) {
?>
											<div>
												<p class="legend-style">Design Options</p>

												<label><input id="best-appearance" name="designapproval" type="radio" value="adjust" checked />Adjust My Design For Best Appearance</label>
												<p class="special-note">Our experts will review your sign, correct obvious design errors, and make adjustments to ensure optimum printing.</p>

												<label><input id="approved-design" name="designapproval" type="radio" value="approved" />Print My Design As Shown</label>
												<p class="special-note">Your sign will be printed exactly as it appears above. Please make any required adjustments before ordering.</p>
											</div>
<?php
										}
  										//Loop through the upcharges
										foreach($upcharges as $key => $value) {

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
														<legend><?php echo $value['type'] . ' Options';?></legend>
<?php
											}

											//Output the input (despite that being an oxymoron)
?>
											<div><label><input type="radio" name="upcharges[<?php echo $typecount; ?>]" value="<?php echo $count; ?>"<?php if ($value['checked'] == 1) { echo " checked"; } ?>><?php echo $value['name']; ?></label></div>
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
?>
									<button type="submit" class="add-to-cart-button" value="submit" title="add-to-cart" >Add To Cart </button>
								</form>
							</td>
						</tr>

<?php
						}//End productsattributes loop

?>
			</tbody>
		</table>
	</div>
	<?php if (HTML_COMMENTS) { ?><!-- End price table --><?php } ?>


	<?php if (HTML_COMMENTS) { ?><!-- Product tabs
	================================================================================================
	--><?php } ?>

<?php



	//If we have content from any of the three tabs, we need to render tabs
	if($ObjPageProduct->details_tab_content || $ObjPageProduct->materials_tab_content || $accessories_count > 0) {
		$val = "0";
	}


?>

	<?php if (HTML_COMMENTS) { ?><!-- Draw the tabs --><?php } ?>
	<div class="tabs prepend-top clear" id="item-detail-slide-navigation">

<?php
		//If we have accessories or related products, display so in the tab
		if ($accessories_count > 0 || $related_products_count > 0) {

			$hasRelatedPrdctsAndAcsries = FALSE;

			if($accessories_count > 0 && $related_products_count > 0){
				$tab_title = 'Related Products<br /> &amp; Accessories';
				$hasRelatedPrdctsAndAcsries = TRUE;
			} else if($accessories_count > 0){
				$tab_title = 'Accessories';
			} else {
				$tab_title = 'Related Products';
			}
?>
			<a href="#sign_accessories" class="selected tab-control"><?=$tab_title?></a>
<?php
		}

		//If we have details, display the tab
		if ($ObjPageProduct->details_tab_content) {
?>
				<a href="#sign_details_regulations" class="tab-control">Sign Details<br />&amp; Regulations</a>
<?php
		}

		//If we have materials, display the tab
		if ($ObjPageProduct->materials_tab_content) {
?>
				<a href="#material_descriptions" class="tab-control">Material Descriptions<br />&amp; Sign Application</a>
<?php
		}
?>

	</div>


	<?php if (HTML_COMMENTS) { ?><!-- Tab content --><?php } ?>

<?php
	//If there are any tabs, render the tab wrapper
	$render_tabs = ($accessories_count > 0 || $related_products_count > 0 || $ObjPageProduct->details_tab_content || $ObjPageProduct->materials_tab_content);

	if ($render_tabs) {
?>
	<div id="additional-details-slider" class="span-18">
<?php
	}


		//If we have accessories, display the contents
		if ($accessories_count > 0 || $related_products_count > 0) {
?>
			<div id="sign_accessories">
<?php
				//Start accessories loop
				$i="20";
				$displayRelatedTitle = TRUE;
				$displayAccessoriesTitle = TRUE;
				foreach ($accessories as $groupKey => $group) {
					foreach($group as $key => $accessories_data){

						//Check if item is a Related Product or an Accessory
						if($groupKey === 'related_products'){
							$product_id = $accessories_data['canonical'];
							$TmpObjPageProduct = new BSPageProduct($accessories_data['canonical']);
							$TmpObjPage = new Page('product', $accessories_data['canonical']);
							if($displayRelatedTitle && $hasRelatedPrdctsAndAcsries){
	?>
								<p class="h3 product-related-header">Related Products</p>
	<?
							}

							//Display 'Related Products' only once
							$displayRelatedTitle = FALSE;
						} else {
							$product_id = $accessories_data['id'];
							$TmpObjPageProduct = new BSPageProduct($accessories_data['id'], $accessories_data);
							if($displayAccessoriesTitle && $hasRelatedPrdctsAndAcsries){
	?>
								<p class="h3 product-accessories-header">Accessories</p>
	<?
							}

							//Display 'Accessories' only once
							$displayAccessoriesTitle = FALSE;
						}

						$materials = $TmpObjPageProduct->getMaterials();

	?>
						<div class="accessory_listing prepend-top append-bottom">
							<div class="table-display">
								<div class="table-row-display">
								<div class="accesory-thumbnail table-cell-display">
									<div class="thumbnail-container">
										<img src="<?php echo $TmpObjPageProduct->imagePath['grid'].htmlspecialchars($accessories_data['image1_thumbnail'], ENT_QUOTES, 'UTF-8');?>" alt="composite_post" />
									</div>
								</div>
								<div class="accesory-title table-cell-display">
									<p>
										<strong><?php echo htmlspecialchars($accessories_data['by_legend'], ENT_QUOTES, 'UTF-8');?></strong>
										<?php echo htmlspecialchars($accessories_data['product_nickname'], ENT_QUOTES, 'UTF-8');?>
										<? if ( $groupKey == 'related_products' ) : ?>
											&#183; <a href="<?=htmlspecialchars($TmpObjPage->getUrl(), ENT_QUOTES, 'UTF-8')?>">Product Information »</a>
										<? endif; ?>
									</p>
									<?echo (!empty($accessories_data['sale_percentage']) ? '<p><span class="product-percentage-saved">ON SALE NOW</span> <span class="bold">SAVE '.htmlspecialchars($accessories_data['sale_percentage'], ENT_QUOTES, 'UTF-8').'&#37;</span> <span class="note-text">Sale prices shown below. While supplies last.</span></p>': '');?>
								</div>
								</div>
							</div>
							<div class="clear">
								<table class="accesory-pricing-table">
									<thead>
										<tr>
											<th>Size</th>
											<th class="description">Materials</th>
											<th>Price</th>
											<th class="add-to-cart-th">Enter Quantity</th>
										</tr>
									</thead>
								</table>

	<?php
									$i = -1;
									$ObjProductSubAttributes=new ProductSubAttributes();
									//Start product material loop
									foreach($materials as $material_key => $material_value) {

										$upcharges = $ObjPageProduct->getUpchargesByMaterialCode($material_value['material_code']);
										$upcharge_count = count($upcharges);

										$i++;
										$product_prices = $ObjProductSubAttributes->ProductGetSubAttributes($material_value['sku_code'],$material_value['product_number']);

										if($material_value['quantity_show'] == "Y" || $i == 0 || (!empty($value['material']) && $previous_material != $material_value['material'])) {

											//Keep track of the previous type of material so we can automatically start new quantity rows if it changes.
											if (!empty($material_value['material'])) {
												$previous_material = $value['material'];
											}
	?>
									<table class="accesory-pricing-table scrollable-price-grid">
										<thead>
											<tr class="quantity-break" >
												<td colspan="2"><span><?php echo htmlspecialchars($material_value['material'], ENT_QUOTES, 'UTF-8');?></span></td>

	<?php
												$row=0;
	?>
												<td class="price">
													<table>
														<tr>
	<?php
															//Loop through and output prices
															foreach($product_prices as $key => $value_qty) {
	?>
																<td>
																	<?php if($value_qty['quantity']!='0') { echo htmlspecialchars($value_qty['quantity'], ENT_QUOTES, 'UTF-8');} ?>
																</td>
	<?php
															}
	?>
														</tr>
													</table>
												</td>

								<?php if (HTML_COMMENTS) { ?><!-- Quantity (sadly, nothing ever goes in this one)--><?php } ?>
												<td></td>
											</tr>
										</thead>
										<tbody>
	<?php
										} //end if condition

											$in_stock = '';

											//Show in-stock note if there exists only a limited amount of that product sku
											if($material_value['limited_inventory']){
												$in_stock = '<br><span class="showInventoryLevel">'.htmlspecialchars($material_value['inventory'], ENT_QUOTES, 'UTF-8').' left in stock</span>';
											}
	?>
										<tr class="data-row product-addtocart-row" title="<?php echo htmlspecialchars($material_value['sku_code'], ENT_QUOTES, 'UTF-8');?>" >
											<td class="stock"><?php echo htmlspecialchars($material_value['size'], ENT_QUOTES, 'UTF-8');?></td>
											<td class="description"><?php echo htmlspecialchars($material_value['material_description'], ENT_QUOTES, 'UTF-8').$in_stock;?>
											<?php if($material_value['ul_recognized']) {?><br><span class="note-text"> &bull;&nbsp;UL&reg;&nbsp;Recognized</span><br/><span class="bold"></span><?php }?>
											</td>

											<td class="price">
													<table>
														<tr>
	<?php
															//Loop through and output prices
															foreach ($product_prices as $key => $value) {
	?>
																<td>
																	<?php if($value['price']!='0.00') { echo "$".htmlspecialchars($value['price'], ENT_QUOTES, 'UTF-8'); }?>
																</td>
	<?php
															}
	?>
														</tr>
													</table>
												</td>

											<td class="add-to-cart-form" >
												<form accept-charset="utf-8" action="<?php echo URL_PREFIX_HTTP; ?>/add-to-cart" method="post" name="cartform" enctype="multipart/form-data" class="custom addtocart" >
													<input type="hidden" name="type" value="<?php echo $TmpObjPageProduct->tool_type; ?>">
													<input type="hidden" name="id" value="<?php echo $product_id;?>">
													<input type="hidden" name="material" value="<?php echo $value['material_code'];?>">
													<input type="text" class="add-to-cart-input text quantity" maxlength="10" name="qty" value="" />
	<?php
													//If we have any upcharges
													if ($upcharge_count > 0) {
														$type = '';
														$count = 0;
														$typecount = -1;
	?>
													<div class="product-options">
														<p class="h4">Options</p>
	<?php
														//Loop through the upcharges
														foreach($upcharges as $key => $value) {

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
																		<legend><?php echo $value['type'] . ' Options';?></legend>
	<?php
															}

															//Output the input (despite that being an oxymoron)
	?>
															<div><label><input type="radio" name="upcharges[<?php echo $typecount; ?>]" value="<?php echo $count; ?>"<?php if ($value['checked'] == 1) { echo " checked"; } ?>><?php echo $value['name']; ?></label></div>
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
	?>
													<button type="submit" class="add-to-cart-button js" value="submit" title="add-to-cart" >Add To Cart </button>
												</form>
											</td>
										</tr>
	<?php
									}//end product material loop
	?>
								</tbody>
									</table>
								</div>

							</div>

	<?php
						} //end of group array loop
					}//end accessories-tab loop

					if ($accessories_count > 0 || $related_products_count > 0) {
	?>
						</div>
						<?php if (HTML_COMMENTS) { ?><!-- End accessories panel--><?php } ?>
	<?php
					}
			}

?>

<?php
		//If we have details info, output the tab contents
		if($ObjPageProduct->details_tab_content) {
			echo "<div class='panel' id='sign_details_regulations'>";
			include $Path_Content_Detail_Tab.$ObjPageProduct->details_tab_content;
			echo "</div>";
		}
?>

<?php
		//If we have materials info, output the tab contents
		if($ObjPageProduct->materials_tab_content) {
			echo "<div class='panel' id='material_descriptions'>";
			include $Path_Content_Materials_Tab.$ObjPageProduct->materials_tab_content;
			echo "</div>";
		}

	if ($render_tabs) {
?>
	</div>
<?php
	}
?>


<?php if (HTML_COMMENTS) { ?><!-- End tabs --><?php } ?>

</div>


<?php if (HTML_COMMENTS) { ?><!-- End main content column --><?php } ?>

<?php
//Only render the language popup if the product is tweakable and multilingual
if ($tweakable || $multilingual) {
?>

	<div class="language-popup" id="language-popup">
		<div class="translation-dialogue">
			<p class="h3"><?php echo (isset($ObjPageProduct->product_type) ? ucwords($ObjPageProduct->product_type) : 'Sign'); ?> Translation</p>
			<form method="post" accept-charset="utf-8">
<?php
				//Loop through each language
				foreach($product_langauge as $key => $language) {

					$product_number_language = $language['product_number_language'];
					$product_language = $ObjPageProduct->getSearchProductNumberList($product_number_language);
?>
					<label><input type="radio" name="language"
					<?php if($ObjPageProduct->language == $language['language']) echo "checked='checked'";?>
					value="<?php echo htmlspecialchars($language['language'], ENT_QUOTES, 'UTF-8');?>">&nbsp;<?php echo htmlspecialchars($language['language'], ENT_QUOTES, 'UTF-8');?></label>
<?php
				}
?>
				<div>
					<a href="#" class="cancel-button">Cancel</a>
					<input type="submit" value="Translate" class="translate-button blue button">
				</div>
			</form>
		</div>
		<?php if ($tweakable) { ?>
		<div class="popup-tweak">
			<p class="h5">Need another language?</p>
			<p>Edit to suit your needs. Custom pricing will apply.</p>
			<a href="<?php echo $page->getUrl() . '?mode=tweak'; ?>" class="tweakable-button">Tweak <?php echo (isset($ObjPageProduct->product_type) ? htmlspecialchars(ucwords($ObjPageProduct->product_type), ENT_QUOTES, 'UTF-8') : 'Sign'); ?></a>
		</div>
		<? } ?>
	</div>

<?php
}
?>