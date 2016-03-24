<?php

	if (!$objShoppingCart instanceof Cart || $objShoppingCart->customerId != $_SESSION['CID']) {
?>


		<div style="margin-top:20px;color: #666; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; ">
			<div style="width: 640px; border: 1px solid #dddddd; margin: 0 auto; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; font-size: 12px; padding: 10px; color:#666; background-color:#FFF;">
				<div>
					<h3 style="text-align:center; padding: .5em; background: #CDDDED; color: #175892; margin-top:0; margin-bottom: .5em;">
						The Information you requested is unavailable.
					</h3>
				</div>

				<table cellspacing="0">
					<tr>
						<td>
							<a href="/">
								<img src="/new_images/SScom_newlogo.png" alt="logo" style="border:0;margin-right:10px;"/>
							</a>
						</td>

						<td>
							<table cellspacing="0">
								<tr>
									<td>
										<h2 style="padding: 0; margin: 0; color:#333;font-size:24px;">
											<!-- TODO: SAVED CART TITLE GOES HERE -->
										</h2>
									</td>
								</tr>
								<tr>
									<td style="font-size:9px; color: #666; padding: 5px;">
										<p style="margin:0;">
											Phone: 800.274.6271 &nbsp;|&nbsp;
											<span>Fax: 800.279.6897&nbsp;|&nbsp;</span>
											<span>P. O. Box 467 / 64 Outwater Ln / Garfield, NJ 07026</span>
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>

				<div style="width: 100%; margin-top:30px; border-top:1px solid #ddd;">
					<div style="width: 100%;">
						<h2>
							<!-- TODO: SAVED CART TITLE GOES HERE --> Is Unavailable
						</h2>

						<p style="margin-top:0; padding-top:0;font-size:14px;">
							The saved cart you requested is not available at this time. Please contact customer service if you require further assistance.
						</p>
						<div style="margin: 0 auto;width:100%;text-align:center;height:50px;"><a href="javascript:window.close();" class="button">Close Window</a></div>
					</div>
					<hr style="background-color:#dddddd;border:0;;height:1px;"/>
					<div style="color:#999999;">
						<p style="margin-top:10px;color: #666; font-size:10px; border-top:1px solid #EBEBEB;padding-top:10px;">
							For details on how to save carts and more, please visit our <a href="<?php print $help->getUrl();?>" style="color: #00559b; text-decoration:underline">Online Help</a>. Or call us at 800-274-6271, Monday thru Friday from 9:00 am thru 5:00 pm Eastern.<br />
						</p>

						<p style="margin-top:10px; color: #666; font-size:10px;">
							This cart is subject to SafetySign.com <a href="<?php print $term->getUrl();?>" style="color: #00559b; text-decoration:underline">Terms and Conditions</a>.</p>
					</div>

				</div>


			</div>
		</div>

		<?php

		die();
	}

	$cartProducts = $objShoppingCart->products;
?>

<div style="color: #666; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; font-size: 12px;">
	<div style="width: 640px; border: 1px solid #ebebeb; margin: 0 auto; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; font-size: 12px; padding: 10px; color:#666; background-color:#FFF;">
		<table cellspacing="0" style="height:80px;">
			<tr>
				<td>
					<a href="<?php print $home->getUrl();?>">
						<img src="<?php print URL_PREFIX;?>new_images/SScom_newlogo.png" alt="logo" style="border:0;margin-right:10px;"/>
					</a>
				</td>

				<td>
					<table cellspacing="0">
						<tr>
							<td>
								<h2 style="padding: 0; margin: 0; color:#333;">
									<? echo $objShoppingCart->name; ?>
								</h2>
							</td>
						</tr>
						<tr>
							<td style="font-size:9px; color: #333; padding: 5px;">
								<p>
									Phone: 800.274.6271 &nbsp;|&nbsp;
									<span>Fax: 800.279.6897&nbsp;|&nbsp;</span>
									<span>P. O. Box 467 / 64 Outwater Ln / Garfield, NJ 07026</span>
								</p>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>


		<table id="customer-info" cellspacing="0" style="width: 640px; font-size: 12px; border: 1px solid #ebebeb;  color:#666;">
			<tr style="color:#175892; padding-top:5px; background: #E6EEF6;">
				<th style="padding-top: .5em; padding-bottom: .5em; text-align:left; padding-left:5px;">
					Account Email
				</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">
					Date Saved
				</th>
			</tr>

			<tr>
				<td style="padding:5px;">

					<?php

				if(mb_strlen($_SESSION['Useremail']) > 50 && mb_strlen($_SESSION['Useremail']) < 55){
					print mb_substr($_SESSION['Useremail'], 0, 49). "&hellip;";
				}elseif(mb_strlen($_SESSION['Useremail']) >= 55){
					print wordwrap($_SESSION['Useremail'], 50, "<br />", true);
				}else{
					print$_SESSION['Useremail'];
				}
				?>
				</td>
				<td style="padding:5px; border-left: 1px solid #ebebeb;">
					<?php print	date("m/d/Y", strtotime($objShoppingCart->creationTime));?>
				</td>

			</tr>
		</table>

		<table cellspacing="0" style="margin-top:10px; width: 640px; font-size: 12px; border: 1px solid #ebebeb; color:#666;">
			<tr style="background: #E6EEF6; color:#175892;">
				<th style="padding-top: .5em; padding-bottom: .5em; text-align:left; padding-left:5px;">Item Image</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Description &amp; Size</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Qty</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Each</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Price</th>
			</tr>

<?php

			$customItemCount = $objShoppingCart->getCustomCount();
			$stockItemCount = $objShoppingCart->getStockCount();

			// Loop through the cart products
			foreach($cartProducts as $product) {

				$cartid=$product->id;
				$sub_total = $sub_total+$product->totalPrice;
				$sku_code=$product->skuCode;
?>
			<tr>

				<td style="border-left: 1px solid #ebebeb; border-bottom: 1px solid #ebebeb; padding: 10px; padding: 10px; text-align:center;">
<?php
					if($product->type=='flash' || $product->type=='streetname') {

?>

						<img src="<?php print CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$product->customImage['customImage'];?>" border="0" >

<?php
					} else if($product->type=='builder') {

?>
						<style>
							.invoice-image{width:auto; height:auto;max-width:100px;max-height:150px;}
							.invoice-image{width:150px;}
						</style>

						<img src="<?php print $product->customImage['customImage']; ?>" class="invoice-image" >
<?php
					// Stock
					} else {

?>
					 	<img src="<?php print IMAGE_URL_PREFIX.'/images/catlog/product/small/'.$product->productImage;?>" alt="<?php print htmlspecialchars($product->skuCode,ENT_QUOTES,"UTF-8");?>">

<?php
					}
?>
				</td>

				<td style="border-left: 1px solid #ebebeb; border-bottom: 1px solid #ebebeb; padding: 10px;">
<?php
					print 'Item #: ' . $product->skuCode . "<br />";
					print 'Size: '.$product->size."<br />";
					print 'Material: '.stripslashes($product->materialDescription)."<br/>";
					print (!empty($product->savingsPercentage) ? '<strong>YOU SAVED '.$product->savingsPercentage.'&#37;</strong>' : "");

					$s_id=$product->skuCode;
					$design_adjust="Design Adjustment:";
					//Output builder attributes
					if($product->type=='builder') {

						//Loop through each attribute for product
						foreach($product->settings as $setting) {
							$label="<p style=\"overflow:hidden;width:250px;\">".htmlspecialchars($setting['builderLabel'],ENT_QUOTES,"UTF-8").": ";
							if ($setting['builderSettingDisplay'] == true) {
								if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {
									print $label.htmlspecialchars($setting['builderValueText'],ENT_QUOTES,'UTF-8') . "</p>";
								}

							}
						}

						if ($product->designService) {
							print "<p style=\"overflow:hidden;width:250px;\">Design Adjustment: </span> We will adjust your design for best appearance.</p>";
						} else {
							print "<p style=\"overflow:hidden;width:250px;\">Design Adjustment: </span> We will print your design as shown.</p>";
						}

						if($product->comments != '') print "<p style=\"overflow:hidden;width:250px;\">Instructions: ". $product->comments."</p>";


					}//builder ends
					//Output builder attributes
					if($product->type=='flash') {

						//Loop through each attribute
						foreach($product->upcharges as $upcharge) {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>" . $upcharge['type'] . ": </span>" . htmlspecialchars($upcharge['name'], ENT_QUOTES, 'UTF-8')."</p>";
						}

						if ($product->designService) {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</p>";
						} else {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will print your design as shown.</p>";
						}

						if ($product->comments != '') {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Instructions: </span>".htmlspecialchars($product->comments, ENT_QUOTES, 'UTF-8')."</p>";
						}


					}//flash ends

					if($product->type == 'streetname') {


						// Loop through the upcharges
						foreach($product->upcharges AS $upcharge) {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>" . $upcharge['type'] . ": </span>".htmlspecialchars($upcharge['name'],ENT_QUOTES,"UTF-8"). "</p>";
						}

						//Loop through each attribute
						foreach ($product->getAdditionalDetails() as $key => $att_value) {
							print  "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>".$key.": </span>".htmlspecialchars($att_value,ENT_QUOTES,"UTF-8"). "</p>";
						}

						//Custom image
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Custom Image Uploaded:</span> ".($product->fileUpload['id'] > 0 ? 'Yes' : 'No' )." </p>";

						//Design adjustment
						if ($product->designService){
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</p>";
						} else {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will print your design as shown.</p>";
						}

						if($product->comments!='') print "<p style=\"overflow:hidden;width:250px;\">Instructions: ".htmlspecialchars($product->comments,ENT_QUOTES,"UTF-8")."</p>";

					}//streetname ends


?>
				</td>

				<td style="border-left: 1px solid #ebebeb; border-bottom: 1px solid #ebebeb; padding: 10px;">
					<?php print $product->quantity;?>
				</td>
				<td style="border-left: 1px solid #ebebeb; border-bottom: 1px solid #ebebeb; padding: 10px;">
					$<?php print $product->unitPrice;?>
				</td>
				<td style="border-left: 1px solid #ebebeb; border-bottom: 1px solid #ebebeb; padding: 10px;">
					$<?php print number_format($product->totalPrice,2);?>
				</td>

			</tr>
<?php
			} //End foreach*/
?>
		</table>

<div style="float:left;width:350px;">
<?

		// Natural business delay
		$delay = 1;

		// General production delay
		$delay +=  Settings::getSettingValue('productiondelay');

		// Add preset delay to product custom items
		if( $customItemCount > 0 ){

			$delay += Settings::getSettingValue('customproductdelay');
		}

		$pri = $delay ." business ".( $delay > 1 ? "days" : "day" );


		if($objShoppingCart->note) {
?>
			<p style="margin-bottom:2px; padding-bottom:2px;font-weight:bold;font-size:14px;">Notes:</p>

			<p style="margin-bottom:20px;margin-top:0; padding-top:0;"><?php print htmlspecialchars($objShoppingCart->note, ENT_QUOTES, 'UTF-8');?></p>
<?php
		}
?>



			<?php if(isset($value['shipping_services']) && $value['shipping_services'] !== 'Customer Pickup' && (!$value['freight_shipment'])) { ?>
				<p style="margin-bottom:2px; padding-bottom:2px;font-weight:bold;font-size:14px;">Production Time Table</p>
				<!-- Stock and Custom message -->
				<?php print "<p style='margin-top:0; padding-top:0;'>This order can ship from our warehouse in ". $pri . "</p>";
				 ?>


				<? }?>


			</div>

		<table style="float:right; font-size:12px; margin-top:10px; color: #175892;margin-bottom:20px;background: #E6EEF6;
padding: 5px 10px 15px;">
			<tr>
				<td style="padding:10px 5px 0 5px;font-size:14px;">
					<strong>Subtotal</strong>
				</td>

				<td style="padding:10px 5px 0 5px; text-align: right;">
					$<?php print number_format($sub_total,2);?>
				</td>
			</tr>
		</table>

		<div style="clear:both;">
			<div>
				<p style="margin-top:10px;color: #666; font-size:10px; border-top:1px solid #EBEBEB;padding-top:10px;">
					Prices and quantities on this shopping cart are valid as of <?php print date("m/d/Y"); ?>. All pricing subject to change. Quantities are subject to availability.
				</p>

				<p style="margin-top:10px; color: #666; font-size:10px;">
					This shopping cart is subject to SafetySign.com <a href="<?php print $term->getUrl();?>" style="color: #00559b; text-decoration:underline">Terms and Conditions</a>.</p>
			</div>

		</div>

		<div style="margin-top:10px; background-color:#175892; color: #fff; text-align:right; padding: 10px; text-align: center;">
			&copy; 1988-<?php print date('Y');?> Brimar Industries, Inc. | All Rights Reserved
		</div>

	</div>
</div>
<script>window.print();</script>
