<?php

    require_once "../include/config.php";

	//Get the PID from the URL and instantiate a new product

    $pid = isset($_GET['products_id']) ? $_GET['products_id'] : NULL;

	$ObjPageProduct = ProductPage::create($pid);
	$link_product   = Page::create('product', $pid);
	$link_ul        = Page::create('ul-recognized');

	$links          = array('product' => $link_product->getUrl(),
					    '   ul_recognized' => $link_ul->getUrl());

	//Grab all the info we need about the product
	$productsattributes = $ObjPageProduct->ProductAttributes();
	$description_type   = $ObjPageProduct->ProductDescriptionType($ObjPageProduct->getHeaderId());
	$fully_ulrecognized = $ObjPageProduct->fully_ulrecognized;
	$ulrecognized       = $ObjPageProduct->flags['ulrecognized'];
?>

<div class="quick-view-wrapper last">
<h2 class="subhead section-headline" id="item-category"><?php print $ObjPageProduct->getNickname();?></h2>
<div class="love" id="item-image-and-description">
	<div class="span-8" id="item-thumbnail-container">
		<dl id="item-image-reviews-availibility">
			<dt id="item-thumbnail">
            <a href="<?php echo $links['product'];?>" ><img alt="<?php print $ObjPageProduct->getPageTitle();?>" src="<?php print $ObjPageProduct->getImagePath("medium") . $ObjPageProduct->image2;?>"></a>
			</dt>
		</dl>

	<?php if ($ulrecognized){ ?>

        <div class="ul-recognized-wrapper left-side">
				<img src="/images/icons/ul-recognized-red.png" class="left-side" />
				<div class="ul-note small-text left-side first-margin"><?php if(!$fully_ulrecognized){ echo 'Some';}else{ echo 'The';}?> products below are UL&reg;&nbsp;Recognized. <span class="ul-toolip-link fake-link underline">Learn more.</span>
					<div class="ul-tooltip">
						<p class="h1" style="color:#90B557;">UL&reg; Recognized Components</p>
						<p><?php if(!$fully_ulrecognized){ echo 'Some of the';}else{ echo 'The';}?> products on this page are recognized under the Component Recognition Program of UL.</span> The Machine Safety Labels sold by SafetySign.com have been evaluated by UL in the PGDQ2 category in accordance with ANSI/UL 969 and CSA C22.2 No. <a href="<?php echo $links['ul_recognized']; ?>" class="underline">Learn more about UL&reg; Recognition</a>.</p>
					</div>
				</div>

			</div>
	<?php } ?>
	</div>

    <div class="span-10 last" id="item-name-and-details">
		<dl class="clearfix">
        <?php if($description_type['desc_heading1']!='' && $ObjPageProduct->desc_line1!='')
		{
		?>
			<dt id="item-name"><?php print $description_type['desc_heading1'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line1;?></dd>
        	<?php
		}
	    ?>
        <?php if($description_type['desc_heading2']!='' && $ObjPageProduct->desc_line2!='')
		{
		?>
			<hr />
			<dt id="item-compliance"><?php print $description_type['desc_heading2'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line2;?></dd>
       <?php
	    }
		?>
			<?php if($description_type['desc_heading3']!='' && $ObjPageProduct->desc_line3!='')
		{
		?>
			<hr />
			<dt id="item-number"><?php print $description_type['desc_heading3'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line3;?></dd>
	<?php
	    }
		?>
       	<?php if($description_type['desc_heading4']!='' && $ObjPageProduct->desc_line4!='')
		{
		?>
			<hr />
			<dt id="sizes"><?php print $description_type['desc_heading4'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line4;?></dd>
        	<?php
	    }
		?>
        <?php if($description_type['desc_heading5']!='' && $ObjPageProduct->desc_line5!='')
		{
		?>
			<hr />
			<dt id="sizes"><?php print $description_type['desc_heading5'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line5;?></dd>
        	<?php
	    }
		?>

        <?php if($description_type['desc_heading6']!='' && $ObjPageProduct->desc_line6!='')
		{
		?>
			<hr />
			<dt id="sizes"><?php print $description_type['desc_heading6'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line6;?></dd>
        	<?php
	    }
		?>

        <?php if($description_type['desc_heading7']!='' && $ObjPageProduct->desc_line7!='')
		{
		?>
			<hr />
			<dt id="sizes"><?php print $description_type['desc_heading7'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line7;?></dd>
        	<?php
	    }
		?>

        <?php if($description_type['desc_heading8']!='' && $ObjPageProduct->desc_line8!='')
		{
		?>
			<hr />
			<dt id="sizes"><?php print $description_type['desc_heading8'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line8;?></dd>
        	<?php
	    }
		?>

		 <?php if($description_type['desc_heading9']!='' && $ObjPageProduct->desc_line9!='')
		{
		?>
			<hr />
			<dt id="sizes"><?php print $description_type['desc_heading9'];?></dt>
			<dd><?php print $ObjPageProduct->desc_line9;?></dd>
        	<?php
	    }
		?>

        <?php if (HTML_COMMENTS) {?><!-- Availability --><?php } ?>
<?php
				if ($_GET['type'] != 'c' && $ObjPageProduct->tool_type === 'stock') {
					$in_stock_msg = ($ObjPageProduct->limitedAvailability ? 'Limited Availability <span class="small-text note-text">(See Below)</span>' : 'In Stock');
?>
					<dt class="sizes">Availability:</dt>
						<dd><?php echo ($ObjPageProduct->in_stock ? $in_stock_msg : '<span class="inventory-alert">Out of Stock</span>'); ?></dd>
<?php
				}
?>
		</dl>
<a href="<?php echo $links['product']?>" class="button green first-margin"><span class="left-side">Continue To Product Page</span><i class="sprite sprite-right-white"></i></a>
	</div>
</div>
<?echo (!empty($ObjPageProduct->sale_percentage) ? "<p class='prepend-top'><span class='product-percentage-saved'>ON SALE NOW</span> <span class='bold'>Save " . $ObjPageProduct->sale_percentage . "&#37;</span> <span class='note-text'>Sale prices shown below. While supplies last.</span></p>" : "");?>
	<table id="size-set-1" class="item-options quick-view-pricing" cellspacing="0">
		<tbody>
			<tr class="pricing-table-header">
				<th class="item-size">Size</th>
				<th class="sign-material-type">Materials</th>
				<th class="size-mat-code">SKU</th>
<?php
				$productsattributes_first = $ObjProductAttributes->ProductAttributesFirstList($pid);

				$first_attributes_id = $productsattributes_first[0]['material_code'];
				$first_sku_code = $productsattributes_first[0]['sku_code'];
				$first_product_number = $productsattributes_first[0]['product_number'];

		        $productssubattributes_first = $ObjProductSubAttributes->ProductGetSubAttributes($first_sku_code , $first_product_number);


				$count_colspan = count($productssubattributes_first);
 ?>
				<th colspan="<?php echo $count_colspan; ?>">Quantity / Price </th>
			</tr>
		</tbody>

<?php
		//Counter
		$tables = 0;

		//Attributes loop
		foreach($productsattributes as $key => $value) {
			$attributes_id=$value['material_code'];
			$sku_code=$value['sku_code'];
			$product_number = $value['product_number'];
			$ul_recognized = $value['ul_recognized'];

			$productssubattributes=$ObjProductSubAttributes->ProductGetSubAttributes($sku_code, $product_number);
			$subattributes_count=count($productssubattributes);

			//If this is the quantity row, output the quantities as headers
			if($value['quantity_show']=="Y") {
?>
				</table>
				<table class="quick-view-pricing-table scrollable-price-grid">
					<thead>
						<tr class="quantity-break">
							<td colspan="3"><span><?php print $value['material'];?></span></td>
							<td class="price">
								<table>
									<tr>
<?php
										foreach($productssubattributes as $key => $value_qty) {
?>
											<td><?php if($value_qty['quantity_show']=="Y"){ print $value_qty['quantity']; }?></td>
<?php
										}
?>
									</tr>
								</table>
							</td>
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
					<tr class="data-row">
						<td class="item-size"><?php print $value['size'];?></td>
						<td class="sign-material-type"><?php print $value['material_description'];?>
						<?php if($ul_recognized) { print '<br><span class="note-text"> &bull; UL&reg; Recognized</span>';}?>
						<br><span class="<?=$inventory_class?>"><?echo ($ObjPageProduct->limitedAvailability ? $in_stock : '')?></span>
					</td>
						<td class="size-mat-code"><?php print $value['sku_code'];?></td>
						<td class="price">
							<table>
								<tr>
<?php
									/*start of price foreach loop*/
									foreach($productssubattributes as $key => $value) {
?>
										<td><?php if($value['price']!='0'){print "$".$value['price'];}?></td>
<?php
									}
?>
								</tr>
							</table>
						</td>
					</tr>
<?php
		}
?>
		</tbody>
	</table>
	</div>