<?php
$count=count($order);
if($count>0)
{
	foreach($order as $key => $value){}

	$resellerRatings = array(
		'track' => isset($value['newsletter']) ? !!$value['newsletter'] : NULL,
		'email' => $value['customers_email']
	);
?>

<div class="span-18" id="order-confirmation-body">
	<div class="pad-left-15 pad-right-15 top-space">

		<p class="h3 pad-bot-10">Your order has been processed successfully. Thank you for your business!</p>
		<?php if($value['tax_exempt']=='Y'){?>
		<p class="success append-bottom">
			<span class="bold">Tax Exempt Reminder</span><br />
			<span>Please fax your tax exempt certificate to 800-279-6897 within 24 hours or sales tax will be charged to your order.</span>
		</p>
		<?php } ?>
		<?php if($value['freight_shipment']=='1' && $value['shipping_services']!=='Customer Pickup'){?>
		<p class="success append-bottom">
			<span class="bold">Freight Shipment Reminder</span><br />
			<span>Your order is being held until Customer Service contacts you. You will not be charged until shipping is arranged.</span>
		</p>
		<?php } ?>

		<? // Display a proofing delay notice when necessary. ?>
		<? if ( $value['proofs_requested'] ) : ?>
			<p class="success append-bottom">
				<span class="bold">It looks like you requested a proof.</span><br />
				<span>A customer service representative will email your proof to <?=htmlspecialchars($value['customers_email'], ENT_QUOTES, 'UTF-8')?> within 3 business days. Note that to allow time for the proofing process, <? if ( mb_strtolower($value['shipping_services'])=='customer pickup' ) : ?>your new estimated pickup date is <span class="bold"><?=htmlspecialchars(date('F jS, Y', strtotime($value['shipping_arrival_estimate'])), ENT_QUOTES, 'UTF-8')?></span><? else: ?>your new estimated ship date is <span class="bold"><?=htmlspecialchars(date('F jS, Y', strtotime($value['shipping_pickup_estimate'])), ENT_QUOTES, 'UTF-8')?></span> and your new estimated arrival date is <span class="bold"><?=htmlspecialchars(date('F jS, Y', strtotime($value['shipping_arrival_estimate'])), ENT_QUOTES, 'UTF-8')?></span><? endif; ?>.<br><br><em>Don't need a proof? No problem, just give us a call at 800-274-6271 and let us know.</em></span>
			</p>
		<? endif; ?>

		<div class="prepend-5 span-9">
				<div class="append-bottom"><span class="bold">Order Number:</span> <span><?php print $value['order_no'];?></span></div>
				<div class="append-bottom"><span class="bold">Order Total:</span> <span><?php print '$'.number_format($value['total_amount'],2);?></span></div>
				<div class="append-bottom"><span class="bold">Shipping:</span> <span><?php if($value['shipping_services']=='Customer Pickup') { ?><a href="#customer-pickup-information">Customer Pickup</a><?php } else { print $value['shipping_carrier']." ". $value['shipping_services']; }?></span></div>
				<div class="append-bottom"><span class="bold">Confirmation Email:</span> <span><?php
				if(mb_strlen($value['customers_email']) > 50 && mb_strlen($value['customers_email']) < 55){
					print mb_substr($value['customers_email'], 0, 49). "&hellip;";
				}elseif(mb_strlen($value['customers_email']) >= 55){
					print wordwrap($value['customers_email'], 50, "<br />", true);
				}else{
					print $value['customers_email'];
				}
				?></span></div>
				<?php
		if (!empty($value['shipping_account'])) {
?>
				<div class="append-bottom"><span class="bold">UPS/FedEx Account:</span> <span><?php print $value['shipping_account'];?></span></div>
<?php
		}
?>
		</div>

		<div class="pad-bot-10 prepend-3 text-center span-11">
			<p class="append-bottom"><a href="<?php print $invoice->getUrl().'?orderno=' . $value['order_no']; ?>" class="button blue" target="_blank">Print Your Invoice</a></p>
			<p class="special-note">Your order confirmation will be emailed shortly. To ensure you properly receive this email, and your tracking number, please add orders@safetysign.com to your contact list.</p>

<?php
		if($value['orders_status']==="1") {

			if (mb_strtolower($value['shipping_services'])=='customer pickup') {
?>

				<div class="error"><p><span class="bold">Payment Pending:</span> Please refer to your PayPal account for payment status. <br />
					After payment is processed, your order will be shipped in 1 business day for stock items and 3-5 days for custom items.</p>
					<p class="prepend-top-5"><span class="bold">Your new estimated shipping date is:</span> <?php echo $value['shipping_pickup_estimate']; ?><br />
						<span class="bold">Your new estimated arrival date is:</span> <?php echo $value['shipping_arrival_estimate']; ?></p></div>
<?php
			} else {
?>
				<div class="error"><p><span class="bold">Payment Pending:</span> Please refer to your PayPal account for payment status. <br />
					After payment is processed, your order will be ready for pickup in 1 business day for stock items and 3-5 days for custom items.</p>
						<span class="bold">Your new estimated pickup date is:</span> <?php echo $value['shipping_arrival_estimate']; ?></p></div>
<?php
			}
		}

		if (!empty($value['shipping_account'])) {
?>
		<div class="special-note">
			<p><span class="bold">Please Note:</span>
				Your order has been arranged to ship using your <?php echo $value['shipping_carrier']; ?> account number (<?php echo $value['shipping_account']; ?>). Our Customer Service team will give the shipment details to <?php echo $value['shipping_carrier']; ?> so they can bill you directly. We will contact you if for any reason shipping arrangements cannot be made using your account number.
			</p>
		</div>
<?php
		}
?>
		<p class="special-note"><a href="<?php print $home->getUrl();?>">Return to the Homepage >></a></p>
		<div class="shopperapproved">
   	    	<div id="outer_shopper_approved"></div>
		</div>
	</div>
 		<?php   if( isset($new['count']) && $new['count'] == 0 && $_SESSION['UserType'] != 'U' ){?>
		<div class="notice-neutral hidden-overflow clear guest-registration-form">
				<div class="left-side">
					<p class="no-margin-bottom bold">You're just one step away from signing up for an account on SafetySign.com!</p>
					<p class="no-margin-bottom">Track your order history, save shopping carts for later, email or print saved shopping carts, <br />save custom designs, and save your addresses.</p>
				</div>
				<div class="span-4 prepend-top-5 last text-center pad-left-20">
					<a href="#password-popup" class="button blue guest-registration-password">Create A Password</a>
				</div>
			<div class="hidden">
				 <div id="password-popup">
				 	<p class="h4">Just enter a new password to complete your registration!</p>
				 	<p class="special-note append-bottom">(Passwords should be at least 8 characters long and contain a captial letter and punctuation)</p>
					<form accept-charset="utf-8" id="edit-password-data" method="post" action="process/account.php">
						<input type="hidden" class="text nospace" id="email" name="email" value="<?php print $value['customers_email'];?>">
						<input type="hidden" class="text nospace" id="orderno" name="orderno" value="<?php print $value['order_no'];?>">
						<input type="hidden" class="text nospace" id="cid" name="cid" value="<?php print $value['customers_id'];?>">
						<div class="clear span-8">
							<label for="create-password" class="clear"><span>New Password:</span>
								<input type="password" class="text" id="create-password" name="createpassword" value="" tabindex="6">
							</label>
						</div>
						<div class="clear append-bottom span-8">
							<label for="confirm-password" class="clear"><span>Confirm Password:</span>
								<input type="password" class="text" id="confirm-password" name="confirmpassword" value="" tabindex="7">
							</label>
						</div>
						<div class="clear">
						<div class="button guest-registration-cancel last-margin">Cancel</div>
							<input type="submit" class="button blue first-margin" name="update" value="Register">
						</div>
					</form>
				</div>
			</div>
		</div>

		<?php }?>
<?php if(($value['shipping_services'])=='Customer Pickup')
{?>
		<div id="customer-pickup-information" class="clear bottom-space-10 hidden-overflow">

						<div class="span-10">
							<p class="h3">Customer Pickup Information</p>
							<p class="append-bottom">You have chosen to pick up your order at our Garfield, NJ facility.</p>
							<div class="notice-neutral text-center top-space bottom-space">
								<p class="bold no-margin-bottom">Estimated Availability Date</p>
								<p class="h4"><?=htmlspecialchars(date('F jS, Y', strtotime($value['shipping_arrival_estimate'])), ENT_QUOTES, 'UTF-8')?></p>
								<p class="no-margin-bottom">We will email you when your order is ready to be picked up.</p>
							</div>
							<div class="pad-right-15 bottom-space">
								<div class="left-side">
									<p class="bold no-margin-bottom">Our facility is located at:</p>
									<p>64 Outwater Lane <br>
										Garfield, NJ 07026</p>
								</div>
								<div class="right-side">
									<p class="bold no-margin-bottom">Our hours of operation are:</p>
									<p>9am - 5pm Eastern <br>
										Monday - Friday</p>
								</div>
							</div>
							<p class="clear special-note">For your reference, this information will be included on your order confirmation.
								If you have further questions, please contact us.</p>
								<a href="http://maps.google.com/maps?q=Brimar+Industries,+Inc,+Outwater+Lane,+Garfield,+NJ&amp;hl=en&amp;sll=40.07304,-74.724323&amp;sspn=5.085688,8.239746&amp;oq=brimar+Industries&amp;hq=Brimar+Industries,+Inc,&amp;hnear=Outwater+Ln,+Garfield,+Bergen,+New+Jersey&amp;t=m&amp;z=16" alt="Brimar Industries, Inc. 64 Outwater Lane, Garfield, NJ 07026" target="_blank">Get directions to our facility >></a>
						</div>
						<div class="span-6 last">
							<a href="http://maps.google.com/maps?q=Brimar+Industries,+Inc,+Outwater+Lane,+Garfield,+NJ&amp;hl=en&amp;sll=40.07304,-74.724323&amp;sspn=5.085688,8.239746&amp;oq=brimar+Industries&amp;hq=Brimar+Industries,+Inc,&amp;hnear=Outwater+Ln,+Garfield,+Bergen,+New+Jersey&amp;t=m&amp;z=16" style="text-decoration: underline;color: #175892;"  alt="Brimar Industries, Inc. 64 Outwater Lane, Garfield, NJ 07026" target="_blank">
								<img src="/new_images/google-staticmap-255x250.gif" class="top-space pad-left-15"></a>

						</div>
		</div>
		<?php }?>
	</div>
</div>

<div class="span-6 last" id="order-confirmation-sidebar">
	<iframe src="//www.facebook.com/plugins/likebox.php?href=http%3A%2F%2Fwww.facebook.com%2Fsafetysign&amp;width=200&amp;height=258&amp;show_faces=true&amp;colorscheme=light&amp;stream=false&amp;show_border=true&amp;header=false&amp;appId=389577761160826" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:200px; height:258px;" allowTransparency="true"></iframe>
	<a href="http://twitter.com/safetysigncom"><img src="/new_images/twitter-banner.png" class="bottom-space top-space"></a>

	<p class="h5 h5-rev pad-left-10">Order FAQ</p>
	<ul class="pad-left-10 top-space last">
		<li><a href="<?php print $shipping->getUrl();?>">Shipping and Delivery</a></li>
		<li><a href="<?php print $term->getUrl();?>">Terms and Conditions</a></li>
		<li><a href="<?php print $return->getUrl();?>">Return Policy</a></li>
		<li><a href="<?php print $contact->getUrl();?>">Contact Customer Service</a></li>
	</ul>

	<?php if($_SESSION['UserType']!='G'){?>
	<p class="h5 h5-rev pad-left-10">Order and Account Information</p>
	<ul class="pad-left-10 top-space last">
		<li><a href="<?php print $tracking->getUrl()."?orderno=".$value['order_no'];?>">Track Your Order</a></li>
		<li><a href="<?php print $account->getUrl();?>">Edit Account Info</a></li>
		<li><a href="<?php print $history->getUrl();?>">View Order History</a></li>
	</ul>
	<?php }?>
</div>
<?php

}
