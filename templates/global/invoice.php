<?php


	if ($order == 'mismatch') {
?>

<style>
.button{
	-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	background: #175892;
	border-color: #0f3d65;
	border-radius: 3px;
	border-width: 1px;
	border-style:solid;
	cursor: pointer;
	display: inline-block;
	font-size: 12px;
	font-weight: bold;
	height:24px;
	line-height: 24px;
	padding: 0 10px;
	text-decoration: none;
	text-shadow:none;
	text-align:center;
	margin: 0;
	color:#fff;
}
.button:hover{
	color: #fff;
	border-color: #3d7fa1 #13648c #095d87 #2f769a;
	background: #1885bf; /* Old browsers */
}
@media print{.hide-for-print{display:none;}}
</style>

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
									Order Invoice: <span>Unavailable</span>
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
					Order Invoice <?php print $orderno;?> Is Unavailable
				</h2>
				<p style="margin-top:0; padding-top:0;font-size:14px;">
					To view your order invoice, please sign in to the account you used to place the order.<br>If you checked out without registering, please contact customer service for assistance.
				</p>
				<div style="margin: 0 auto;width:100%;text-align:center;height:50px;"><a href="javascript:window.close();" id="close-window">Close Window</a></div>
			</div>
 <hr style="background-color:#dddddd;border:0;;height:1px;"/>
			<div style="color:#999999;">
				<p style="margin-top:10px;color: #666; font-size:10px; border-top:1px solid #EBEBEB;padding-top:10px;">
					For details on your order status, to view tracking information, learn about returns and cancellations, and more, please visit our <a href="<?php print $help->getUrl();?>" style="color: #00559b; text-decoration:underline">Online Help</a>. Or call us at 800-274-6271, Monday thru Friday from 9:00 am thru 5:00 pm Eastern.<br />
				</p>

				<p style="margin-top:10px; color: #666; font-size:10px;">
					This order is subject to SafetySign.com <a href="<?php print $term->getUrl();?>" style="color: #00559b; text-decoration:underline">Terms and Conditions</a>.</p>


			</div>

		</div>


	</div>
</div>

<?php

		die();
	}

	$count = count($order);

	//If there is order information, get the ids
	if ($count > 0 && $order != FALSE) {

		foreach($order as $key => $value){

				$orderid = $value['orders_id'];
		}

	} else {
?>

<style>
.button{
-moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	background: #175892;
	border-color: #0f3d65;
	border-radius: 3px;
	border-width: 1px;
	border-style:solid;
	cursor: pointer;
	display: inline-block;
	font-size: 12px;
	font-weight: bold;
	height:24px;
	line-height: 24px;
	padding: 0 10px;
	text-decoration: none;
	text-shadow:none;
	text-align:center;
	margin: 0;
	color:#fff;
}
.button:hover{
	color: #fff;
	border-color: #3d7fa1 #13648c #095d87 #2f769a;
	background: #1885bf; /* Old browsers */
}
@media print{.hide-for-print{display:none;}}

</style>
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
									Order Invoice: <span>Unavailable</span>
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
					Order Invoice <?php print $orderno;?> Is Unavailable
				</h2>

				<p style="margin-top:0; padding-top:0;font-size:14px;">
					The invoice number you requested is not available at this time. Please contact customer service if you require further assistance.
				</p>
				<div style="margin: 0 auto;width:100%;text-align:center;height:50px;"><a href="javascript:window.close();" class="button">Close Window</a></div>
			</div>
 <hr style="background-color:#dddddd;border:0;;height:1px;"/>
			<div style="color:#999999;">
				<p style="margin-top:10px;color: #666; font-size:10px; border-top:1px solid #EBEBEB;padding-top:10px;">
					For details on your order status, to view tracking information, learn about returns and cancellations, and more, please visit our <a href="<?php print $help->getUrl();?>" style="color: #00559b; text-decoration:underline">Online Help</a>. Or call us at 800-274-6271, Monday thru Friday from 9:00 am thru 5:00 pm Eastern.<br />
				</p>

				<p style="margin-top:10px; color: #666; font-size:10px;">
					This order is subject to SafetySign.com <a href="<?php print $term->getUrl();?>" style="color: #00559b; text-decoration:underline">Terms and Conditions</a>.</p>
			</div>

		</div>


	</div>
</div>

<?php

		die();
	}

?>
<style>
.button{
	--moz-border-radius: 3px;
	-webkit-border-radius: 3px;
	background: #175892;
	border-color: #0f3d65;
	border-radius: 3px;
	border-width: 1px;
	border-style:solid;
	cursor: pointer;
	display: inline-block;
	font-size: 12px;
	font-weight: bold;
	height:24px;
	line-height: 24px;
	padding: 0 10px;
	text-decoration: none;
	text-shadow:none;
	text-align:center;
	margin: 0;
	color:#fff;
}
.button:hover{
	color: #fff;
	border-color: #3d7fa1 #13648c #095d87 #2f769a;
	background: #1885bf; /* Old browsers */
}
@media print{.hide-for-print{display:none;}}

</style>
<div class="hide-for-print" style="color: #666; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; font-size: 12px;">
	<div style="width: 640px;  margin: 0 auto; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; font-size: 12px; padding: 0 10px; color:#666; background-color:#FFF;height:49px;">
		<p style="font-size:12px;float:right;">
			<a class="button" href="javascript:window.print();" >
				Print Receipt
			</a>

			<a href="javascript:window.close();" class="button">
				Close Window
			</a>
		</p>
	</div>
</div>
<div style="color: #666; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; font-size: 12px;">
	<div style="width: 640px; border: 1px solid #ebebeb; margin: 0 auto; font-family: Helvetica, Verdana, Arial, Geneva, sans-serif; font-size: 12px; padding: 10px; color:#666; background-color:#FFF;">
		<div>
			<h3 style="text-align:center; padding: .5em; background: #CDDDED; color: #175892; margin-top:0; margin-bottom: 0;">
				Thank you for your order. We appreciate your business!
			</h3>
		</div>

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
									Order Invoice: <span><?php print $value['order_no'];?></span>
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
					Email Address
				</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">
					Order Date
				</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">
					Shipping Method
				</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">
					Status
				</th>
			</tr>

			<tr>
				<td style="padding:5px;">
					<?php
				if(mb_strlen($value['customers_email']) > 50 && mb_strlen($value['customers_email']) < 55){
					print mb_substr($value['customers_email'], 0, 49). "&hellip;";
				}elseif(mb_strlen($value['customers_email']) >= 55){
					print wordwrap($value['customers_email'], 50, "<br />", true);
				}else{
					print $value['customers_email'];
				}
				?>
				</td>
				<td style="padding:5px; border-left: 1px solid #ebebeb;">
					<?php print	$date_purchased=$ObjOrder->OrderPurchasedDate($value['date_purchased']);?></td>
				<td style="padding:5px; border-left: 1px solid #ebebeb;"><?php print $value['shipping_carrier']." ".$value['shipping_services'];?>
					<?php if (!empty($value['shipping_account'])) { ?><br>Account: <?php print $value['shipping_account']; } ?>
				</td>
				<td style="padding:5px; border-left: 1px solid #ebebeb;">
					<?php
						$status=$value['orders_status'];
						print $orderstatus=$ObjOrder->OrderStatus($status);
					?>
				</td>
			</tr>
		</table>

		<table id="ship-bill-info" cellspacing="0" style="margin-top:10px; width: 640px; font-size: 12px; border: 1px solid #ebebeb; color:#666;">
			<tr style="background: #E6EEF6; color:#175892;">
				<th style="padding-top: .5em; padding-bottom: .5em; text-align:left; padding-left:5px;">Shipping Address</th>
<?php
				if (mb_strtolower($value['ccType']) != 'paypal') {
?>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Billing Address</th>
<?php
				}
?>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Payment Method</th>
			</tr>

			<tr>
				<td style="padding:5px; line-height:1.4;">
					<?php print $value['shipping_name'];?><br />
					<?php if(!empty($value['shipping_company']))print $value['shipping_company']."<br/>";?>
					<?php print $value['shipping_street_address'];?><br />
					<?php if($value['shipping_suburb']){ print $value['shipping_suburb'];?><br /> <?php }?>

					<?php
					if(mb_strtolower($value['shipping_country']) == 'us' || mb_strtolower($value['shipping_country']) == 'ca'){
						print $value['shipping_city'];?>, <?php print $value['shipping_state'];?> <?php print $value['shipping_postcode'];
					}else{
						print $value['shipping_city'];?> <?php print $value['shipping_postcode'];
					}
					?><br />

					<?php $shipping_country=$objCountry->CountryCodeList($value['shipping_country']);
					if(!empty($shipping_country)) {print $shipping_country['countries_name'];?><br /> <?php }?>

					<?php if($value['shipping_phone']) {print $value['shipping_phone'];?><br /> <?php }?>
					<?php if($value['shipping_fax']) {print $value['shipping_fax'];?><br /> <?php }?>
					<?php
				if(mb_strlen($value['customers_email']) > 30 && mb_strlen($value['customers_email']) < 35){
					print mb_substr($value['customers_email'], 0, 29). "&hellip;";
				}elseif(mb_strlen($value['customers_email']) >= 35){
					print wordwrap($value['customers_email'], 30, "<br />", true);
				}else{
					print $value['customers_email'];
				}
				?><br />
				</td>

<?php
			if (mb_strtolower($value['ccType']) != 'paypal') {
?>
				<td style="padding:5px; border-left: 1px solid #ebebeb;line-height:1.4;">
					<?php print $value['billing_name'];?><br />
					<?php if(!empty($value['billing_company'])) { print $value['billing_company'].'<br />';}?>
					<?php print $value['billing_street_address'];?><br />
					<?php if($value['billing_suburb']) {print $value['billing_suburb'];?><br /> <?php }?>

					<?php
						if(mb_strtolower($value['billing_country']) =='us' || mb_strtolower($value['billing_country']) =='ca'){
							print $value['billing_city'];?>,  <?php print $value['billing_state'];?> <?php print $value['billing_postcode'];
						}else{
							print $value['billing_city'];?> <?php print $value['billing_postcode'];
						}
					?><br />


					<?php $billing_country=$objCountry->CountryCodeList($value['billing_country']);
					if(!empty($billing_country)) {print $billing_country['countries_name'];?><br /> <?php }?>

					<?php if($value['billingphone_display']) {print $value['billingphone_display'];?><br /> <?php }?>
					<?php if($value['billing_fax']) {print $value['billing_fax'];?><br /> <?php }?>
					<?php

					// If this isn't paypal, display the billing address
					if (mb_strtolower($value['ccType']) != 'paypal') {

						if(mb_strlen($value['customers_email']) > 30 && mb_strlen($value['customers_email']) < 35){
							print mb_substr($value['customers_email'], 0, 29). "&hellip;";
						}elseif(mb_strlen($value['customers_email']) >= 35){
							print wordwrap($value['customers_email'], 30, "<br />", true);
						}else{
							print $value['customers_email'];
						}
					}
				?><br />
				</td>
<?php
			}
?>

				<td style="padding:5px; border-left: 1px solid #ebebeb;">
					<table style="color:#666; font-size:12px;">
						<tr>
							<td style="padding:0 5px;line-height:1.4;"><strong>Payment Terms:</strong></td><td style="padding:0 5px;line-height:1.4;">
<?php
								if(mb_strtolower($value['ccType']) == 'brimar') {
									print "Net30 Account";
								} else if (mb_strtolower($value['ccType']) == 'paypal') {
									print "PayPal";
								} else {
									print 'Creditcard';
								}
?>
							</td>
						</tr>

<?php
					// If this isn't PayPal, display a credit card type
					if (mb_strtolower($value['ccType']) != 'paypal') {
?>
						<tr>

							<td style="padding:0 5px;line-height:1.4;">
								<strong>Card Type:</strong>
							</td>

							<td style="padding:0 5px;line-height:1.4;">
								<?php print $value['ccType'];?>
							</td>
						</tr>

						<tr>
							<td style="padding:0 5px;line-height:1.4;">
								<strong>Card Number:</strong>
							</td>

							<td style="padding:0 5px;line-height:1.4;"> **********
								<?php print $value['lastFourCcNum'];?>
							</td>
						</tr>
<?php
					}

					if(mb_strtolower($value['ccType']) != 'brimar' && mb_strtolower($value['ccType']) != 'paypal') {
?>
						<tr>
							<td style="padding:0 5px;line-height:1.4;">
								<strong>Card Expiration:</strong>
							</td>

							<td style="padding:0 5px;line-height:1.4;">
								<?php print $value['ccExpire'];?>
							</td>
						</tr>
<?php
					}
?>
						<tr>
							<td style="padding:0 5px;line-height:1.4;">
								<strong>Transaction: </strong>
							</td>

							<td style="padding:0 5px;line-height:1.4;">
								<?php if($status === "1" && mb_strtolower($value['ccType']) == 'paypal'){print "<span style='color:#bd1818;'>Pending*</span>";} else{print "Approved";}?>
							</td>
						</tr>

						<tr>
							<td style="padding:0 5px;line-height:1.4;">
								<strong>Amount Charged<?php if(mb_strtolower($value['ccType']) != 'paypal'){print "*";} ?>:</strong>
							</td>

							<td style="padding:0 5px;line-height:1.4;">
								$<?php print number_format($value['total_amount'], 2); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>

		<table cellspacing="0" style="margin-top:10px; width: 640px; font-size: 12px; border: 1px solid #ebebeb; color:#666;">
			<tr style="background: #E6EEF6; color:#175892;">
				<th style="padding-top: .5em; padding-bottom: .5em; text-align:left; padding-left:5px;">Item Image</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Description &amp; Size</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Qty</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Price</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Total</th>
			</tr>

<?php
			// Instantiate the cart from the order
			$customItemCount = $cart->getCustomCount();
			$stockItemCount = $cart->getStockCount();

			// Loop through the cart products
			foreach($cart->products as $product) {

				$cartid=$product->id;
				$sub_total=$sub_total+$product->totalPrice;
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
					print "<span class='bold'>Item #: </span>" . $product->skuCode . "<br />";
					print "<span class='bold'>Size: </span>".$product->size."<br />";
					print "<span class='bold'>Material: </span>".stripslashes($product->materialDescription)."<br/>";
					print (!empty($product->savingsPercentage) ? '<strong>YOU SAVED '.$product->savingsPercentage.'&#37;</strong>' : "");

					$s_id=$product->skuCode;
					$design_adjust="Design Adjustment:";
					//Output builder attributes
					if($product->type=='builder') {

						//Loop through each attribute for product
						foreach($product->settings as $setting) {
							$label="<p style=\"overflow:hidden;width:250px;\"><span class='bold'>".htmlspecialchars($setting['builderLabel'],ENT_QUOTES,"UTF-8").": </span>";
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
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>". $upcharge['type'] . ": </span>".htmlspecialchars($upcharge['name'], ENT_QUOTES, 'UTF-8')."</p>";
						}

						if ($product->designService) {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</p>";
						} else {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will print your design as shown.</p>";
						}

						if ($products->comments != '') {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Instructions: </span>".htmlspecialchars($product->comments, ENT_QUOTES, 'UTF-8')."</p>";
						}


					}//flash ends

					if($product->type=='streetname') {


						// Loop through the upcharges
						foreach($product->upcharges AS $upcharge) {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>" . $upcharge['type'] . ": </span>".htmlspecialchars($upcharge['name'],ENT_QUOTES,"UTF-8"). "</p>";
						}

						//Loop through each attribute
						foreach ($product->getAdditionalDetails() as $key => $att_value) {
							print  "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>".$key.": </span>".htmlspecialchars($att_value,ENT_QUOTES,"UTF-8"). "</p>";
						}

						//Custom image
						if (!empty($product->fileUpload['name']))
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Custom Image Uploaded:</span> Yes</p>";

						//Design adjustment
						if ($product->designService){
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</p>";
						} else {
							print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Design Adjustment: </span> We will print your design as shown.</p>";
						}

						if($product->comments!='') print "<p style=\"overflow:hidden;width:250px;\"><span class='bold'>Instructions: </span>".htmlspecialchars($product->comments,ENT_QUOTES,"UTF-8")."</p>";

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


	<?php
		if (!empty($value['purchase_order']) || !empty($value['tag_job'])) {
	?>
		<table cellspacing="0" style="margin-top:10px; width: 640px; font-size: 12px; border: 1px solid #ebebeb; color:#666;">
			<tr style="background: #E6EEF6; color:#175892;">
				<th style="text-align:left; padding:5px;">Purchase Order Number</th>
				<th style="text-align:left; padding:5px;border-left: 1px solid #EBEBEB;">Tag/Job Name</th>
			</tr>
			<tr>
				<td style="padding: 10px;"><?php print $value['purchase_order'];?></td>
				<td style="border-left: 1px solid #ebebeb; padding: 10px;"><?php print $value['tag_job'];?></td>
			</tr>
		</table>
<?php
		}

		// Natural business delay
		$delay = 1;

		// General production delay
		$delay +=  Settings::getSettingValue('productiondelay');

		// Add preset delay to product custom items
		if( $customItemCount > 0 ){

			$delay += Settings::getSettingValue('customproductdelay');
		}

		$pri = $delay ." business ".( $delay > 1 ? "days" : "day" );
?>

		<?php
		if ($value['expediated_shipping']) {
?>
			<h4 style="margin-bottom:2px; padding-bottom:2px">
				Expedited Shipping Request
			</h4>

			<div style="border: 1px solid #ccc; padding: 5px 10px;">
				<p><?php print htmlspecialchars($value['expediated_shipping'], ENT_QUOTES, 'UTF-8');?></p>
			</div>
<?php
		}
?>

<?php
		if($value['comments']) {
?>
			<h4 style="margin-bottom:2px; padding-bottom:2px">
				Order Comments
			</h4>

			<div style="border: 1px solid #ccc; padding: 5px 10px;">
				<p><?php print htmlspecialchars($value['comments'], ENT_QUOTES, 'UTF-8');?></p>
			</div>
<?php
		}
?>


		<div style="float:left;width:350px;">
			<?php if($value['shipping_services']!=='Customer Pickup' && (!$value['freight_shipment'])) { ?>
				<p style="margin-bottom:2px; padding-bottom:2px"><span style="font-weight:bold;font-size:14px;">
					Production Time Table</span>
				</p>
				<!-- Stock and Custom message -->
				<?php if($status === "1" && mb_strtolower($value['ccType']) == 'paypal'){print "<p style='margin-top:0; padding-top:0;'>After payment processes, your order will ship from our warehouse in " . $pri . ".</p>";}
					else{print "<p style='margin-top:0; padding-top:0;'>Your order ships from our warehouse in ". $pri . "</p>";}
				 ?>


				<? }?>
				<!-- Customer Pickup message -->
				<?php if($value['shipping_services']==='Customer Pickup') { ?>
				<p style="margin-top:0; padding-top:0;">Your order is being processed. You will be notified via email when it is ready to be picked up.</p>
				<?php }?>
				<?php if($value['freight_shipment'] && $value['shipping_services']!=='Customer Pickup'){?>
				<!-- Freight Shipping message -->
				<p style="margin-top:0; padding-top:0;">Your order is being held until Customer Service contacts you. You will not be charged until shipping is arranged.</p>
				<?php }
				if($value['tax_exempt']=='Y') {
				?>
						<p style="margin-top:0; padding-top:0;">Please fax your tax exempt certificate to 800-279-6897 within 24 hours or sales tax will be charged to your order.</p>
				<?php	}
				?>

				<?php
	if (!empty($value['shipping_account'])) {
?>
					<p style="margin-top:0; padding-top:0;"><span style="font-weight:bold;">Shipping Note:</span> Your order has been arranged to ship using your <?php echo $value['shipping_carrier']; ?> account number (<?php echo $value['shipping_account']; ?>). Our Customer Service team will give the shipment details to <?php echo $value['shipping_carrier']; ?> so they can bill you directly. We will contact you if for any reason shipping arrangements cannot be made using your account number.</p>
<?php
	}
?>

<?php if($status === "1" && mb_strtolower($value['ccType']) == 'paypal'){print "<span style='color:#bd1818;'>* Please check your PayPal account for payment status.</span>";} ?>

				<p style="font-size:10px;"><span style="font-weight:bold;">Note:</span> Because orders are processed immediately, we are unable to accomodate order changes or cancellations. Erroneously ordered items must be returned after delivery.
				</p>
			</div>

		<table style="float:right; font-size:12px; margin-top:10px; color: #175892;margin-bottom:20px;background: #E6EEF6;
padding: 15px 10px;">
			<tr>
				<td style="padding:5px 5px 0 5px;">
					<strong>Subtotal:</strong>
				</td>

				<td style="padding:5px 5px 0 5px; text-align: right;">
					$<?php print number_format($sub_total,2);?>
				</td>
			</tr>

<?php
			//If there was a coupon, output the discount
			if($value['coupon_value']!='0.00') {
?>
			<tr>
				<td style="padding:5px 5px 0 5px;"><strong>Discount:</strong></td><td style="padding:5px 5px 0 5px; text-align: right;"> -$<?php print $value['coupon_value'];?></td>
			</tr>
<?php
			}
?>
			<tr>
				<td style="padding:5px 5px 0 5px;">
<?php
				if($value['shipping_services']==='Customer Pickup'){
?>
					<strong>Customer Pickup:</strong>
<?php
				} else {
?>
					<strong>Shipping Charge:</strong>
<?
				}
?>
				</td>

				<td style="padding:5px 5px 0 5px; text-align: right;">
					$<?php print number_format($value['shipping_charges'],2);?>
				</td>
			</tr>

			<tr>
				<td style="padding:5px 5px 0 5px;">
					<strong>
<?php
						if($value['shipping_state']=='NJ' && $value['tax_exempt']=='N' ) {
?>
							NJ 7%
<?php
						}
?>
						Sales Tax:
					</strong>
				</td>

				<td style="padding:5px 5px 0 5px; text-align: right;">
					 $<?php print number_format($value['sales_tax'],2);?>
				</td>
			</tr>

			<tr>
				<td style="padding:10px 5px 0 5px;font-size:14px;">
					<strong>Invoice Total:</strong>
				</td>

				<td style="padding:10px 5px 0 5px; text-align: right;">
					 $<?php print number_format($value['total_amount'],2);?>
				</td>
			</tr>
		</table>





		<div style="clear:both;">


			<div>

				<div style="color: #666; border-top:1px solid #EBEBEB;">
					<p style="margin-bottom:2px; padding-bottom:2px">
						<span style="font-weight:bold;font-size:14px;">
							Please <a href="javascript:window.print();" style="color: #00559b; text-decoration:underline">print</a>
							and save this Receipt for your records.
						</span>
					</p>

					<p><strong>The transaction will appear on your bill/statement as "SafetySign.com".</strong></p>
<?php
					if($value['shipping_services']==='Customer Pickup'){
?>
						<p style="font-size:10px;">* You will be charged when your order is available for pick up.</p>
<?php
					} else {
							if(mb_strtolower($value['ccType']) != 'paypal'){ ?>
						<p style="font-size:10px;">* You will be charged when your order ships.</p>
<?php
						}
					}
?>
					<p style="font-size:10px;">Changes to your order may result in sales tax (when applicable) and/or shipping rate adjustments.</p>
				</div>

				<p style="margin-top:10px;color: #666; font-size:10px; border-top:1px solid #EBEBEB;padding-top:10px;">
					For details on your order status, to view tracking information, learn about returns and cancellations, and more, please visit our <a href="<?php print $help->getUrl();?>" style="color: #00559b; text-decoration:underline">Online Help</a>. Or call us at 800-274-6271, Monday thru Friday from 9:00 am thru 5:00 pm Eastern.<br />
				</p>

				<p style="margin-top:10px; color: #666; font-size:10px;">
					This order is subject to SafetySign.com <a href="<?php print $term->getUrl();?>" style="color: #00559b; text-decoration:underline">Terms and Conditions</a>.</p>
			</div>

		</div>

		<div style="margin-top:10px; background-color:#175892; color: #fff; text-align:right; padding: 10px; text-align: center;">
			&copy; 1988-<?php print date('Y');?> Brimar Industries, Inc. | All Rights Reserved
		</div>

	</div>
</div>

