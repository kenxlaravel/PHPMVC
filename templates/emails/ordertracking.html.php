
<table border='0' width='600px'>
	<tr>
		<td style="padding-bottom:10px"><p><strong>Your <a href="<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>">SafetySign.com</a> order has shipped!</strong></p>
		</td>
	</tr>
	<tr>
		<td>
			<p>Order No: <?php print htmlspecialchars($orderno,ENT_QUOTES,"UTF-8");?></p>
			<p>Shipped Date: <?php print htmlspecialchars($shipdate,ENT_QUOTES,"UTF-8");?></p>
			<p>Shipping Method: <?php print htmlspecialchars($shipping_carrier . " " . $shipmethod,ENT_QUOTES,"UTF-8");?></p>
			<p>Status: <?php print htmlspecialchars($status,ENT_QUOTES,"UTF-8");?></p>
			<?php if (!empty($shipping_account)) {?><p>Shipping Account: <?php print htmlspecialchars($shipping_account,ENT_QUOTES,"UTF-8");?></p><?php } ?>
			<p><?php if(mb_strtolower($shipping_carrier) == 'ups') { ?>UPS Tracking Number: <?php print htmlspecialchars($trackingnumber,ENT_QUOTES,"UTF-8");?><?php } else if(mb_strtolower($shipping_carrier) == 'fedex' ) { ?>FedEx Tracking Number #<?php print htmlspecialchars($trackingnumber,ENT_QUOTES,"UTF-8");?><?php } ?></p>
		</td>
	</tr>
	<tr>
		<td>
			<p>You can track the status of this order by clicking on the following link. <br />
			<a href="<?php print $track_url; ?>"><?php print $track_url;?></a></p>
			<p><?php if(mb_strtolower($value['shipping_carrier']) =='fedex' && mb_strtolower($value['shipping_services'])!= 'smartpost'){ ?>To schedule delivery and sign up for text alerts <a href="http://www.fedex.com/us/delivery/" class="underline" target="_blank">visit FedEx.com</a>.<?php } ?></p>
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p><strong>Ship To Address</strong></p>
			<?php print htmlspecialchars($shipaddress['ship_name'],ENT_QUOTES,"UTF-8");?><br />
			<?php if(!empty($shipaddress['shipping_company'])) { print htmlspecialchars($shipaddress['shipping_company'],ENT_QUOTES,"UTF-8")."<br />"; }?>
			<?php print htmlspecialchars($shipaddress['shipping_street_address'],ENT_QUOTES,"UTF-8")."<br/>";?>
			<?php if(!empty($shipaddress['shipping_suburb'])) { print htmlspecialchars($shipaddress['shipping_suburb'],ENT_QUOTES,"UTF-8")."<br/>";?> <?php } ?>
			<?php
				if(mb_strtolower($shipaddress['shipping_country_code']) == 'us' || mb_strtolower($shipaddress['shipping_country_code']) == 'ca' ) {
					print htmlspecialchars($shipaddress['shipping_city'],ENT_QUOTES,"UTF-8") .",". htmlspecialchars($shipaddress['shipping_state'],ENT_QUOTES,"UTF-8")." ". htmlspecialchars($shipaddress['shipping_postcode'],ENT_QUOTES,"UTF-8");
				}else{
					print htmlspecialchars($shipaddress['shipping_city'],ENT_QUOTES,"UTF-8") ." ". htmlspecialchars($shipaddress['shipping_postcode'],ENT_QUOTES,"UTF-8");
				}
			?><br/>

			<?php print htmlspecialchars($shipaddress['shipping_country'],ENT_QUOTES,"UTF-8");?><br />

			<?php print htmlspecialchars($shipaddress['shipping_phone'],ENT_QUOTES,"UTF-8");?><br />
			<?php if(!empty($shipaddress['shipping_fax'])) { print htmlspecialchars($shipaddress['shipping_fax'],ENT_QUOTES,"UTF-8");?> <?php } ?>
			</p>
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p><strong>Bill To Address</strong></p>
			<?php print htmlspecialchars($billaddress['bill_name'],ENT_QUOTES,"UTF-8");?><br />
			<?php if(!empty($billaddress['billing_company'])) { print htmlspecialchars($billaddress['billing_company'],ENT_QUOTES,"UTF-8")."<br />"; }?>
			<?php print htmlspecialchars($billaddress['billing_street_address'],ENT_QUOTES,"UTF-8")."<br />";?>
			<?php if(!empty($billaddress['billing_suburb'])) { print htmlspecialchars($billaddress['billing_suburb'],ENT_QUOTES,"UTF-8")."<br/>";?> <?php } ?>
			<?php
				if(mb_strtolower($billaddress['billing_country_code']) == 'us' || mb_strtolower($billaddress['billing_country_code']) == 'ca' ) {
					print htmlspecialchars($billaddress['billing_city'],ENT_QUOTES,"UTF-8") .",". htmlspecialchars($billaddress['billing_state'],ENT_QUOTES,"UTF-8")." ". htmlspecialchars($billaddress['billing_postcode'],ENT_QUOTES,"UTF-8");
				}else{
					print htmlspecialchars($billaddress['billing_city'],ENT_QUOTES,"UTF-8") ." ". htmlspecialchars($billaddress['billing_postcode'],ENT_QUOTES,"UTF-8");
				}
			?><br />


			<?php print htmlspecialchars($billaddress['billing_country'],ENT_QUOTES,"UTF-8");?><br />
			<?php print htmlspecialchars($billaddress['billing_phone'],ENT_QUOTES,"UTF-8");?><br />
			<?php print htmlspecialchars($customer_email,ENT_QUOTES,"UTF-8");?>
		</p>
		</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p><strong>Payment Info</strong></p>
			<p>Payment Type: <?php print htmlspecialchars($paymentinfo['cardtype'],ENT_QUOTES,"UTF-8");?></p>
			<?php if(mb_strtolower($paymentinfo['cardtype']) != 'paypal'){ ?><p><?php if (mb_strtolower($paymentinfo['cardtype']) == 'brimar') {
																							print "Account Number: ";
																					  } else if (mb_strtolower($paymentinfo['cardtype']) == 'credit') {
																							print "Card No: ";
																					 }?>**********<?php print htmlspecialchars($paymentinfo['cardNum'],ENT_QUOTES,"UTF-8");?> </p> <?}?>


			<?php if(mb_strtolower($paymentinfo['cardtype']) == 'credit'){ ?><p> Card Expiration: <?php print htmlspecialchars($paymentinfo['expiration'],ENT_QUOTES,"UTF-8"); ?> </p><?php }?>
			<p>Transaction: Approved</p>
			<p>Amount Charged: $<?php print htmlspecialchars(number_format($paymentinfo['total_amount'],2),ENT_QUOTES,"UTF-8");?></p>
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
								if($cart_key=='size') echo htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8").'<br/>';
								if($cart_key=='material') echo htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8").'<br/>';
								if($cart_key=='sale_percentage' && !empty($cvalue)) echo '<p><strong>You saved' . htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8") . '&#37;</strong></p>';
								if($cart_key=='attribute'){
									if(!empty($cvalue)){

										foreach($cvalue as $sub_key=>$att_value){
											echo '<p><strong>'.htmlspecialchars($sub_key,ENT_QUOTES,"UTF-8") .':</strong><br/>'. htmlspecialchars($att_value,ENT_QUOTES,"UTF-8") . '</p>';
										}
									}
								}
								if($cart_key=='builder_attributes'){
									if(!empty($cvalue)){

										foreach ($cvalue as $subkey =>$builder ){
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
								if($cart_key=='design_service')	{
									if($cvalue == TRUE && $stock_custom=='C'){
										echo '<p><strong>Design Adjustment:</strong> We will adjust your design for best appearance.</p>';
									}else if($cvalue == FALSE && $stock_custom=='C'){
										echo '<p><strong>Design Adjustment:</strong> We will print your design as shown.</p>';
									}
								}
								if(($cart_key=='comment') && (!empty($cvalue))){
									echo '<p><strong>Instructions: </strong>'. htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8").'</p>';
								}
								if(($cart_key=='builder_comment') && (!empty($cvalue))){
									echo '<p><strong>Instructions: </strong>'. htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8").'</p>';
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
			<p>Shipping Charge: $<?php print htmlspecialchars(number_format($shippingcharge,2),ENT_QUOTES,"UTF-8");?></p>
			<p>Sales Tax: $<?php print htmlspecialchars(number_format($salestax,2),ENT_QUOTES,"UTF-8");?></p>
			<p>Invoice Total: $<?php print htmlspecialchars(number_format($invoicetotal,2),ENT_QUOTES,"UTF-8");?></p>
			<?php
				if($comments!=''){
					print "<p>Your Comments<br />". htmlspecialchars($comments,ENT_QUOTES,"UTF-8")."</p>";
				}
			?>
</td>
	</tr>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;padding-bottom:10px;">
			<p>Thank you and please come visit us again at <a href="<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>">http://www.SafetySign.com</a></p>
			<p>Please check this order for accuracy and contact us immediately if any information is incorrect.</p>
			<?php
			if(!$guest) {?>
				<p>My Account - Change any of your personal information<br />
				<a href='<?php print htmlspecialchars($account_url,ENT_QUOTES,"UTF-8");?>'><?php print htmlspecialchars($account_url,ENT_QUOTES,"UTF-8");?></a>
				</p>
			<?php
				}
			?>
			Order Tracking - Check the status of your order.
			<a href="<?php print htmlspecialchars($track_url,ENT_QUOTES,"UTF-8");?>"><?php print htmlspecialchars($track_url,ENT_QUOTES,"UTF-8");?></a>

		</td>
	</tr>
</table>

<table>
	<tr>
		<td style="border-top:1px solid #0000;padding-top:10px;">
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