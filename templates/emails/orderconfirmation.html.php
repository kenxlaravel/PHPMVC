<table border='0' width='600px'>
	<tr>
		<td style="padding-bottom:10px;"><p><strong>Greetings from <a href="<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>">SafetySign.com</a></strong></p>
			<p>This is your order confirmation email. Please read it carefully.</p>
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<?php
			if(!empty($freight) && $shipmethod!=='Customer Pickup'){
				print 'Freight Reminder: ';
				print $freight."<br/>";
			}
			if(!empty($tax_exempt)){
				print 'Tax Exempt Reminder: ';
				print $tax_exempt."<br/>";
			}

			echo "<p>Click to load printable receipt for your order:<br /> <a href='".htmlspecialchars($invoice_url,ENT_QUOTES,"UTF-8")."' target=\"_blank\" >SafetySign.com Order #".htmlspecialchars($orderno, ENT_QUOTES, 'UTF-8')."</a></p>";
			?>
			<p>Order No: <?php print htmlspecialchars($orderno,ENT_QUOTES,"UTF-8");?></p>
			<p>Order Date: <?php print htmlspecialchars($order_date,ENT_QUOTES,"UTF-8");?></p>
			<p>Shipping Method: <?php print htmlspecialchars($shipping_carrier . " " . $shipmethod,ENT_QUOTES,"UTF-8");?></p>
<?php
			if (!empty($shipping_account)) {
?>
			<p>Shipping Account: <?php print htmlspecialchars($shipping_account,ENT_QUOTES,"UTF-8"); ?></p>
			<p>Please Note: Your order has been arranged to ship using your <?php echo $shipping_carrier; ?> account number (<?php echo $shipping_account; ?>). Our Customer Service team will give the shipment details to <?php echo $shipping_carrier; ?> so they can bill you directly. We will contact you if for any reason shipping arrangements cannot be made using your account number.</p>
<?php
			}
?>

			<? // Display a proofing notice when necessary. ?>
			<? if ( $proofsRequested ) : ?>
				<p>It looks like you requested a proof. A customer service representative will email your proof to this email address within 3 business days.<br><br><em>Don't need a proof? No problem, just give us a call at 800-274-6271 and let us know.</em></p>
			<? endif; ?>

<?php
			if($orderstatus === '1' && mb_strtolower($paymentinfo['cardtype']) == 'paypal'){

				if (mb_strtolower($shipmethod) == 'customer pickup') {
?>
					<p><strong>Payment Pending:</strong> Please refer to your PayPal account for payment status. After payment is processed, your order will be available for pickup in 1 business day for stock items and 3-5 days for custom items.</p>
						<p><strong>Your new estimated pickup date is:</strong> <?php echo $shipping_arrival_estimate; ?></p>
<?php
				} else {
?>
					<p><strong>Payment Pending:</strong> Please refer to your PayPal account for payment status. After payment is processed, your order will be shipped in 1 business day for stock items and 3-5 days for custom items.</p>
						<p><strong>Your new estimated shipping date is:</strong> <?php echo $shipping_pickup_estimate; ?><br />
							<strong>Your new estimated arrival date is:</strong> <?php echo $shipping_arrival_estimate; ?></p>
<?php
				}

			}
?>

		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p><strong>Ship To Address</strong></p>
			<p><?php print htmlspecialchars($shipaddress['ship_name'],ENT_QUOTES,"UTF-8");?><br />
			<?php if(!empty($shipaddress['shipping_company']) ) { print htmlspecialchars($shipaddress['shipping_company'],ENT_QUOTES,"UTF-8").'<br />'; } ?>
			<?php print htmlspecialchars($shipaddress['shipping_street_address'],ENT_QUOTES,"UTF-8")."<br/>";?>
			<?php if(!empty($shipaddress['shipping_suburb'])) { print htmlspecialchars($shipaddress['shipping_suburb'],ENT_QUOTES,"UTF-8")."<br/>"; } ?>
			<?php
				if(mb_strtolower($shipaddress['shipping_country_code']) == 'us' || mb_strtolower($shipaddress['shipping_country_code']) == 'ca' ) {
					print htmlspecialchars($shipaddress['shipping_city'],ENT_QUOTES,"UTF-8") .", ". htmlspecialchars($shipaddress['shipping_state'],ENT_QUOTES,"UTF-8")." ". htmlspecialchars($shipaddress['shipping_postcode'],ENT_QUOTES,"UTF-8")."<br/>";
				}else{
					print htmlspecialchars($shipaddress['shipping_city'],ENT_QUOTES,"UTF-8") ." ". htmlspecialchars($shipaddress['shipping_postcode'],ENT_QUOTES,"UTF-8")."<br/>";
				}
				?>
			<?php if(!empty($shipaddress['shipping_country'])) { print htmlspecialchars($shipaddress['shipping_country'],ENT_QUOTES,"UTF-8")."<br/>";} ?>
			<?php if(!empty($shipaddress['shipping_phone'])) {print htmlspecialchars($shipaddress['shipping_phone'],ENT_QUOTES,"UTF-8")."<br/>";} ?>
			<?php if(!empty($shipaddress['shipping_fax'])) { print htmlspecialchars($shipaddress['shipping_fax'],ENT_QUOTES,"UTF-8");?> <?php } ?>
		</p>
		</td>
	</tr>
	<tr>
<?php
	if (mb_strtolower($paymentinfo['cardtype']) != 'paypal') {
?>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p><strong>Bill To Address</strong></p>
			<p><?php print htmlspecialchars($billaddress['bill_name'],ENT_QUOTES,"UTF-8");?><br />
			<?php if(!empty($billaddress['billing_company'])) { print htmlspecialchars($billaddress['billing_company'],ENT_QUOTES,"UTF-8")."<br />"; } ?>
			<?php print htmlspecialchars($billaddress['billing_street_address'],ENT_QUOTES,"UTF-8")."<br/>";?>
			<?php if(!empty($billaddress['billing_suburb'])) { print htmlspecialchars($billaddress['billing_suburb'],ENT_QUOTES,"UTF-8")."<br/>";?> <?php } ?>
			<?php
				if(mb_strtolower($billaddress['billing_country_code']) == 'us' || mb_strtolower($billaddress['billing_country_code']) == 'ca' ) {
					print htmlspecialchars($billaddress['billing_city'],ENT_QUOTES,"UTF-8") .", ". htmlspecialchars($billaddress['billing_state'],ENT_QUOTES,"UTF-8")." ". htmlspecialchars($billaddress['billing_postcode'],ENT_QUOTES,"UTF-8");
				}else{
					print htmlspecialchars($billaddress['billing_city'],ENT_QUOTES,"UTF-8") ." ". htmlspecialchars($billaddress['billing_postcode'],ENT_QUOTES,"UTF-8");
				}
			?><br />

			<?php print htmlspecialchars($billaddress['billing_country'],ENT_QUOTES,"UTF-8");?><br />
			<?php print htmlspecialchars($billaddress['billing_phone'],ENT_QUOTES,"UTF-8");?><br />
			<?php print htmlspecialchars($customer_email,ENT_QUOTES,"UTF-8");?>
		</p>
		</td>
<?php
	}
?>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p><strong>Payment Info</strong></p>
			<p><?php if(mb_strtolower($paymentinfo['cardtype']) == 'brimar' || mb_strtolower($paymentinfo['cardtype']) == 'paypal') print 'Payment Type: '; else print 'Card Type: ';?><?php print htmlspecialchars($paymentinfo['cardtype'],ENT_QUOTES,"UTF-8");?></p>
<?php
		if (mb_strtolower($paymentinfo['cardtype']) != 'paypal') {
?>
			<p>
<?php
					if(mb_strtolower($paymentinfo['cardtype']) == 'brimar')
						print 'Account Number: ';
					else
						print 'Card No: ';
?>
					**********
<?php
					print htmlspecialchars($paymentinfo['cardNum'], ENT_QUOTES, "UTF-8");
?>
			</p>
			<p><?php if($paymentinfo['cardtype']!='Brimar'){ ?> Card Expiration : <?php print htmlspecialchars($paymentinfo['expiration'],ENT_QUOTES,"UTF-8"); ?> <?php }?></p>
<?php
		}

		if($orderstatus === '1' && mb_strtolower($paymentinfo['cardtype']) == 'paypal'){
?>
			<p>Transaction: Pending</p>
<?php
		} else {
?>
			<p>Transaction: Approved</p>
<?php
		}
?>
			<p>Amount Charged: <?php print '$'.htmlspecialchars(number_format($paymentinfo['total_amount'],2),ENT_QUOTES,"UTF-8");?></p>
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<table border='0' cellpadding='0' cellspacing='0' >

				<tr>
					<td style="border-bottom:1px dotted #000; padding:5px;"><strong>Item #</strong></td>
					<td style="border-bottom:1px dotted #000; padding:5px;"><strong>Description &amp; Size</strong></td>
					<td style="border-bottom:1px dotted #000; padding:5px;"><strong>Qty</strong></td>
					<td style="border-bottom:1px dotted #000; padding:5px;"><strong>Price</strong></td>
					<td style="border-bottom:1px dotted #000; padding:5px;"><strong>Total</strong></td>
				</tr>
				<?php
				foreach ($cart as $key => $value){
					?>
					<tr>
						<td style="border-bottom:1px dotted #000; border-right:1px dotted #000; border-left:1px dotted #000;padding:5px;"><?php if($value['sku_code']) echo htmlspecialchars($value['sku_code'],ENT_QUOTES,"UTF-8") ;?></td>
						<td style="border-bottom:1px dotted #000; border-right:1px dotted #000;padding:5px;">
							<?php
							foreach($value as $cart_key=>$cvalue){

								if($cart_key=='size') echo '<p><strong>Size:</strong><br/>' . htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8") . '</p>';
								if($cart_key=='material') echo '<p><strong>Material:</strong><br/>' . htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8") . '</p>';
								if($cart_key=='sale_percentage' && !empty($cvalue)) echo '<p><strong>You saved ' . htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8") . '&#37;</strong></p>';
								if($cart_key=='attribute' && !empty($cvalue)){
									foreach($cvalue as $sub_key=>$att_value) {
										if (!empty($sub_key)) {
											echo '<p><strong>' .htmlspecialchars($sub_key,ENT_QUOTES,"UTF-8") .':</strong><br/>'. htmlspecialchars($att_value,ENT_QUOTES,"UTF-8").'</p>';
										} else {
											echo '<p>'. htmlspecialchars($att_value,ENT_QUOTES,"UTF-8").'</p>';
										}
									}
								}

								if($cart_key=='builder_attributes') {

									if(!empty($cvalue)) {

										foreach ($cvalue as $subkey =>$builder ) {

											$label=$builder['builder_label'];
											if($builder['builder_setting_display']=='Y'){

												if ( $builder['builder_subsetting'] == 'mountingoptions' || $builder['builder_subsetting'] == 'antigraffiti' || $builder['builder_setting'] == 'scheme' || $builder['builder_setting'] == 'layout' || $builder['builder_setting'] == 'text' || $builder['builder_setting'] == 'artwork' ||  $builder['builder_setting'] == 'upload' )
												{
													echo '<p><strong>'.htmlspecialchars($label,ENT_QUOTES,"UTF-8").':</strong><br/>'.htmlspecialchars($builder['builder_value_text'],ENT_QUOTES,"UTF-8").'</p>';
												}

												if($builder['builder_setting']=='upload'){
													echo '<p><strong>'.htmlspecialchars($label,ENT_QUOTES,"UTF-8").':</strong><br/>'.htmlspecialchars($builder['upload_name'],ENT_QUOTES,"UTF-8").'</p>';
												}
											}

										}
									}
								}

								if($cart_key=='stock_custom') $stock_custom=$cvalue;

								if(($cart_key=='file_name') && (!empty($cvalue))){
									echo '<p><strong>Custom Image Uploaded:</strong> Yes</p>';
								}
								if($cart_key=='design_service')	{
									if($cvalue == TRUE && $stock_custom=='C'){
										echo '<p><strong>Design Adjustment:</strong><br/>We will adjust your design for best appearance.</p>';
									}else if($cvalue == FALSE && $stock_custom=='C'){
										echo '<p><strong>Design Adjustment:</strong><br/>We will print your design as shown.</p>';
									}
								}
								if(($cart_key=='comment') && (!empty($cvalue))){
									echo '<p><strong>Instructions: </strong><br/>'. htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8").'</p>';
								}

							}
							?>
						</td>
						<td style="border-bottom:1px dotted #000; border-right:1px dotted #000;padding:5px;">
							<?php if($value['quantity']) echo htmlspecialchars($value['quantity'],ENT_QUOTES,"UTF-8");?>
						</td>

						<td style="border-bottom:1px dotted #000; border-right:1px dotted #000;padding:5px;">
							<?php if($value['price']) echo '$'.htmlspecialchars(number_format($value['price'],2),ENT_QUOTES,"UTF-8");?>
						</td>

						<td style="border-bottom:1px dotted #000; border-right:1px dotted #000;padding:5px;">
							<?php if($value['total']) echo '$'.htmlspecialchars(number_format($value['total'],2),ENT_QUOTES,"UTF-8");?>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</td>
	</tr>
	<?php if($purchase_order || $tag_job){ ?>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p><?php if($purchase_order){
				echo "Purchase Order Number: ".htmlspecialchars($purchase_order,ENT_QUOTES,"UTF-8");
			}?></p>
			<p><?php if($tag_job){
				echo "Tag/Job Name: ".htmlspecialchars($tag_job,ENT_QUOTES,"UTF-8");
			}?></p>
		</td>
	</tr>
	<?php } ?>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
				<p>Order Subtotal: $<?php print htmlspecialchars(number_format($subtotal,2),ENT_QUOTES,"UTF-8");?></p>
				<?php if($coupon_value!='0.00'){
					echo "<p> Discount: -$".htmlspecialchars(number_format($coupon_value,2),ENT_QUOTES,"UTF-8")."</p>";
				}
				?>
				<p>Shipping Charge: $<?php print htmlspecialchars(number_format($shippingcharge,2),ENT_QUOTES,"UTF-8");?></p>
				<p>Sales Tax: $<?php print htmlspecialchars(number_format($salestax,2),ENT_QUOTES,"UTF-8");?></p>
				<p>Invoice Total: $<?php print htmlspecialchars(number_format($invoicetotal,2),ENT_QUOTES,"UTF-8");?></p>
				<?php
				if($expediated_shipping){
					echo "<p> Need your stuff shipped faster? <br />".htmlspecialchars($expediated_shipping,ENT_QUOTES,"UTF-8")."</p>";
				}
				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php if($comments){
					echo "<p>Your Comments<br/>".htmlspecialchars($comments,ENT_QUOTES,"UTF-8")."</p><br/>";
				}
				?>
			</td>
		</tr>
		<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
				Thank you and please come visit us again at <a href='<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>'>http://www.safetysign.com/</a><br/>
			</td>
		</tr>
		<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
				<?php
				if($shipmethod=='Customer Pickup'){
					echo "<p>Please check this order for accuracy and contact us immediately if any information is incorrect.</p>
					Your order should be available for pickup on $arrival_date.<br/>
					We'll send you an email when your order# $orderno is ready to pick up.<br />";


					if (mb_strtolower($paymentinfo['cardtype']) != 'paypal') {
						echo "* You will be charged when your order is ready for pickup.<br />";
					}


					echo "</p>";
					echo "<p>You can access the following information on our website:<br />";

					if ($_SESSION['UserType']=='U') {
						echo  "My Account - Change any of your personal information<br />";
						?>
						<a href='<?php print htmlspecialchars($account_url,ENT_QUOTES,"UTF-8");?>'><?php print htmlspecialchars($account_url,ENT_QUOTES,"UTF-8");?></a>
						<?php
					}
					echo "</p>";

				} else {

					echo "<p>When your order #$orderno has shipped, we will e-mail you the tracking number and the ability
					to track the shipment's \"up-to-the-moment\" progress!<br /><br />
					Please check this order for accuracy and contact us immediately if any information is incorrect.</p>";

					if($shipmethod!=='Customer Pickup' && (!empty($freight))) {
						echo "<p><strong>Production Time Table</strong><br />
						Ships From Warehouse: Stock Items ship next day, while custom items ship in 3 to 5 days<p>";
					}

					if($shipmethod==='Customer Pickup') {
						echo "<p>Your order is being processed. You will be notified via email when it is ready to be picked up.</p>";
					}

					if (mb_strtolower($paymentinfo['cardtype']) != 'paypal') {
						echo "<p>* You will be charged when your order ships.</p>";
					}

					echo "<p>Note: Because orders are processed immediately, we are unable to accomodate order changes or cancellations; erroneously ordered items must be returned after delivery.</p>
					<p>You can access the following information on our website:<br />";
					if ($_SESSION['UserType']=='U') {
						echo "My Account - Change any of your personal information<br /><a href='".htmlspecialchars($account_url,ENT_QUOTES,"UTF-8")."'>".htmlspecialchars($account_url,ENT_QUOTES,"UTF-8")."</a>";
					echo "</p>";
					}
					echo "<p>Order Tracking - Check the status of your order online.<br />";
					echo '<a href='.htmlspecialchars($track_url,ENT_QUOTES,"UTF-8").'>'.htmlspecialchars($track_url,ENT_QUOTES,"UTF-8").'</a></p>';


				}
			?>
			</td>
		</tr>
	</table>
	<table>
		<tr>
			<td style="border-top:1px solid #000;padding-top:10px;">
				<p>We thank you for your business and welcome questions or comments.</p>
				<p>
					<strong>SafetySign.com</strong><br>
					Brimar Industries<br>
					P.O. Box 467<br>
					64 Outwater Lane<br>
					Garfield, NJ 07026<br>
				</p>
				<p><strong>Contact Customer Service:</strong><br>
					Phone: 800-274-6271<br>
					Fax: 800-279-6897<br>
					E-mail: <?php print EMAIL_SERVICE;?>
				</p>
				<p>
					<strong>Hours of Operation:</strong><br>
					9am - 5pm Eastern<br>
					Monday - Friday
				</p>
			</td>
		</tr>
	</table>