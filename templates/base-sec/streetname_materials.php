<?php
	//Check if we have a page object. If we do not, this page is not being included in the streetname
	//tool but instead called dynamically with an AJAX request. In that case we have to include/
	//instantiate some things.

	//TODO: change all default to actual values after url implemented
	if (!$ObjPageProduct instanceof ProductPage) {
		include_once ('../../include/config.php');
        $productStateParameters = !empty($_GET['s'])? $_GET['s'] : $productStateParameters;
        $stateParameters = ProductStateParameter::decode($productStateParameters);
		$pid = isset($_POST['pid'])? $_POST['pid'] : $stateParameters['sourceProduct'];

		$ObjProduct = Product::create($pid,NULL,$stateParameters);
		if(!empty($stateParameters)) { $streetId = (int) $ObjProduct->getStreetsignToolId(); } else { $streetId = $ObjProduct->getStreetsignToolId(); }
		$productno = trim($ObjProduct->getProductNumber());
		$size = $_POST['size'];
		$size = str_replace('×', 'x', $size);
		$size_array = explode('x',$size);
		$size_array[0] = trim($size_array[0]);
		$size_array[1] = trim($size_array[1]);
		$new_size = $size_array[0] . " × " . $size_array[1];

		$stid = isset($_REQUEST['stid']) ? $_REQUEST['stid'] : $streetId; // KEN: VARIABLE CONFLICT!
		$product_material_by_size = StreetNameTool::create($stid)->getCustomMaterialList($productno,$new_size);

		if ($editdata['uploadfileid'] > 0) {
			$final_editdata = $editdata;
		} else {
			$final_editdata = json_decode($_REQUEST['editdata']);
		}

	} else {

		$pid = $pid;

		if (isset($editdata)) {
			$final_editdata = $editdata;
		} else {
			# Build the editdata array
			$final_editdata = array(
				'line_1' => (string) '',
				'line_2' => (string) '',
				'position' => (string) $defaultposition,
				'prefix' => (string)$defaultprefix,
				'sign_background' => (string) $defaultbackground,
				'sign_color' => (string) $defaultcolor,
				'sign_font' => (string)(isset($defaultfont)? $defaultfont : ""),
				'special_comment' => '',
				'suffix' => (string)$defaultsuffix,
				'textupper' => (string) 'N',
				'uploadfileid' => (string) $file_id,
				'sidetext' => (string) isset($defaultsidetext)?$defaultsidetext : NULL
			);


		}
	}

	if (!empty($_REQUEST['mounting'])) { $mounting = (int) $_REQUEST['mounting']; }
	if (!empty($defaultmounting)) { $mounting = (int) $defaultmounting; }
	if (!empty($_REQUEST['currentmountingoption'])) { $mounting = (int) $_REQUEST['currentmountingoption']; }
	if (!empty($_REQUEST['mounting_option'])) { $mounting = (int)$_REQUEST['mounting_option']; }
    $ObjProductSubAttributes = new ProductSubAttributes();
	$productssubattributes_first = $ObjProductSubAttributes->ProductGetSubAttributes($product_material_by_size[0]['sku_code'] , $productno);

	$count_colspan = count($productssubattributes_first);

?>

<div class="viewmaterial quantity-pricing-table append-bottom street-name-pricing">


	<table id="size-set-1" class="item-options">
		<tr>
			<th>Size</th>
			<th class="sign-material-type">Materials</th>
			<th>Quantity / Price </th>
			<th class="add-to-cart-th">Enter Quantity</th>
		</tr>
<?php
		$row=0;
?>
		<tr class="quantity-break">
			<td colspan="2"><span id="custom_material"><?php print htmlspecialchars($product_material_by_size[0]['material'], ENT_QUOTES, 'UTF-8');?></span></td>
			<td class="price">
				<table>
					<tr>
<?php

						$row=0;
						foreach($productssubattributes_first as $key => $value_qty) {
?>
							<td><?php if($value_qty['minimum_quantity']!='0'){ print htmlspecialchars($value_qty['minimum_quantity'], ENT_QUOTES, 'UTF-8'); }?></td>
<?php
						}
?>
					</tr>
				</table>
			</td>
			<td></td>
		</tr>

<?php

	 	for ($i = 0; $i < count($product_material_by_size); $i++) {
			$row++;
			$p_id= $pid;
			$attributes_id=$product_material_by_size[$i]['material_code'];
			$sku_code = $product_material_by_size[$i]['sku_code'];
			$sku_id = (int) $product_material_by_size[$i]['sku_id'];

			$productssubattributes=$ObjProductSubAttributes->ProductGetSubAttributes($sku_code ,$productno);
?>
				<tr class="data-row product product-addtocart-row" title="<?php echo htmlspecialchars($product_material_by_size[$i]['sku_code'], ENT_QUOTES, 'UTF-8');?>">
					<td class="item-size"><?php print htmlspecialchars($product_material_by_size[$i]['size'], ENT_QUOTES, 'UTF-8');?></td>
					<td class="sign-material-type"><?php print htmlspecialchars($product_material_by_size[$i]['material'], ENT_QUOTES, 'UTF-8');?></td>
					<td class="price">
						<table>
							<tr>
<?php
								$subattributes_count = count($productssubattributes);
								$row=0;


								foreach($productssubattributes as $key => $price_value) {
?>
									<td><?php if($price_value['price']!='0.00'){ print "$".htmlspecialchars($price_value['price'], ENT_QUOTES, 'UTF-8'); }?></td>
<?php
								}
?>
							</tr>
						</table>
					</td>
					<td class="add-to-cart-form">

						<form accept-charset="utf-8" id="custom<?php print $i; ?>" action="<?php print URL_PREFIX_HTTP; ?>/add-to-cart" method="post" name="cartform" enctype="multipart/form-data" class="custom addtocart validate-me htmlcustom">
							<input name="type" type="hidden" value="streetname">
							<input name="id" type="hidden" class="p_id" value="<?php print htmlspecialchars($pid, ENT_QUOTES, 'UTF-8'); ?>">
							<input name="sku_id" type="hidden" value="<?php print htmlspecialchars($sku_id, ENT_QUOTES, 'UTF-8'); ?>">
							<input name="stid" type="hidden" value="<?php print htmlspecialchars($stid, ENT_QUOTES, 'UTF-8'); ?>">

							<?php if ($productStateParameters['sourceProduct']) : ?><input name="source_product_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                            <?php if ($productStateParameters['sourceProductRecommendation']) : ?><input name="source_product_recommendation_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceProductRecommendation'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                            <?php if ($productStateParameters['sourceAccessoryFamilyProduct']) : ?><input name="source_accessory_familyProduct_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceAccessoryFamilyProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                            <?php if ($productStateParameters['sourceInstallationAccessory']) : ?><input name="source_installation_accessory_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceInstallationAccessory'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                            <?php if ($productStateParameters['sourceLandingProduct']) : ?><input name="source_landing_product_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceLandingProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>
                            <?php if ($productStateParameters['sourceSubcategoryProduct']) : ?><input name="source_subcategory_product_id" type="hidden" value="<?php print htmlspecialchars($productStateParameters['sourceSubcategoryProduct'], ENT_QUOTES, 'UTF-8'); ?>"> <?php  endif; ?>

							<input name="material" type="hidden" value="<?php print htmlspecialchars($product_material_by_size[$i]['material_code'], ENT_QUOTES, 'UTF-8'); ?>">
							<input name="upcharges[]" type="hidden" class="vmounting" value="<? print htmlspecialchars((int) $mounting, ENT_QUOTES, 'UTF-8'); ?>">
							<input name="editdata" type="hidden" value="<?php echo htmlspecialchars(json_encode($final_editdata), ENT_QUOTES, 'UTF-8'); ?>">
							<input name="qty" type="text" class="add-to-cart-input text quantity" maxlength="10" id="quantity<?php print $i; ?>" value="">
							<div class="add-to-cart-requiretext noshow">Please enter a Street Name to continue adding this item to your shopping cart.</div>
							<button type="submit" name="submit" class="add-to-cart-button" value="submit" >Add To Cart </button>
						</form>
					</td>
				</tr>
<?php
		}
?>
				</table>
			</div>