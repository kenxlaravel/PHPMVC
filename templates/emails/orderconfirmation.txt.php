Greetings from SafetySign.com
This is your order confirmation email. Please read it carefully.

---------------------------------------------------------------------------------------------------------------------------
<?php

		if(!empty($freight) && $shipmethod!=='Customer Pickup'){
				echo 'Freight Reminder: ';
				echo $freight."\n";
			}
			if(!empty($tax_exempt)){
				echo 'Tax Exempt Reminder: ';
				echo $tax_exempt."\n";
			}
?>

Click to load printable receipt for your order:<?php print "\n".$invoice_url."\n";?>
---------------------------------------------------------------------------------------------------------------------------

Order No: <?php print $orderno."\n";?>
Order Date: <?php print $order_date."\n";?>
Shipping Method: <?php print $shipping_carrier . " " . $shipmethod."\n";?>
<?php
	if (!empty($shipping_account)) {
?>
Shipping Account: <?php print $shipping_account ."\n";?>

Please Note: Your order has been arranged to ship using your <?php echo $shipping_carrier; ?> account number (<?php echo $shipping_account; ?>). Our Customer Service team will give the shipment details to <?php echo $shipping_carrier; ?> so they can bill you directly. We will contact you if for any reason shipping arrangements cannot be made using your account number.

<?php

	}

	// Display a proofing notice when necessary.
	if ( $proofsRequested ) :

?>

It looks like you requested a proof. A customer service representative will email your proof to this email address within 3 business days.
Don't need a proof? No problem, just give us a call at 800-274-6271 and let us know.

<?php

	endif;

	if($orderstatus === '1' && mb_strtolower($paymentinfo['cardtype']) == 'paypal'){

		if (mb_strtolower($shipmethod == 'customer pickup')) {
?>
			Payment Pending: Please refer to your PayPal account for payment status.
			After payment is processed, your order will be available for pickup in 1 business day for stock items and 3-5 days for custom items.</p>
			Your new estimated pickup date is: <?php print $shipping_arrival_estimate; ?>
<?php
		} else {
?>
			Payment Pending: Please refer to your PayPal account for payment status.
			After payment is processed, your order will be shipped in 1 business day for stock items and 3-5 days for custom items.</p>
			Your new estimated shipping date is: <?php print $shipping_pickup_estimate; ?>
			Your new estimated arrival date is: <?php print $shipping_arrival_estimate; ?>
<?php
		}
	}
?>

---------------------------------------------------------------------------------------------------------------------------
Ship To Address
---------------------------------------------------------------------------------------------------------------------------
<?php print $shipaddress['ship_name']."\n";?>
<?php if(!empty($shipaddress['shipping_company'])) { print $shipaddress['shipping_company']."\n"; }?>
<?php print $shipaddress['shipping_street_address']."\n";?>
<?php if(!empty($shipaddress['shipping_suburb'])) { print $shipaddress['shipping_suburb']."\n"; } ?>
<?php
	if(mb_strtolower($shipaddress['shipping_country_code']) == 'us' || mb_strtolower($shipaddress['shipping_country_code']) == 'ca' ) {
		print $shipaddress['shipping_city'] .",". $shipaddress['shipping_state']." ". $shipaddress['shipping_postcode']."\n";
	}else{
		print $shipaddress['shipping_city'] ." ". $shipaddress['shipping_postcode']."\n";
	}
?>
<?php if(!empty($shipaddress['shipping_country'])) {print $shipaddress['shipping_country']."\n"; } ?>
<?php if(!empty($shipaddress['shipping_phone'])) {print $shipaddress['shipping_phone']."\n"; }?>
<?php if(!empty($shipaddress['shipping_fax'])) { print $shipaddress['shipping_fax']."\n";}?>
<?php
	if (mb_strtolower($paymentinfo['cardtype']) != 'paypal') {
?>

---------------------------------------------------------------------------------------------------------------------------
Bill To Address
---------------------------------------------------------------------------------------------------------------------------
<?php print $billaddress['bill_name']."\n";?>
<?php if(!empty($billaddress['billing_company'])) { print $billaddress['billing_company']."\n"; } ?>
<?php print $billaddress['billing_street_address']."\n";?>
<?php if(!empty($billaddress['billing_suburb'])) { print $billaddress['billing_suburb']."\n";}?>
<?php
if(mb_strtolower($billaddress['billing_country_code']) == 'us' || mb_strtolower($billaddress['billing_country_code']) == 'ca' ) {
	print $billaddress['billing_city'] .",". $billaddress['billing_state']." ". $billaddress['billing_postcode']."\n";
}else{
	print $billaddress['billing_city'] ." ". $billaddress['billing_postcode']."\n";
}
?>
<?php print $billaddress['billing_country']."\n";?>
<?php print $billaddress['billing_phone']."\n";?>
<?php print $customer_email."\n";?>
<?php
	}
?>

---------------------------------------------------------------------------------------------------------------------------
Payment Info
---------------------------------------------------------------------------------------------------------------------------
<?php if(mb_strtolower($paymentinfo['cardtype']) == 'brimar' || mb_strtolower($paymentinfo['cardtype']) == 'paypal') print 'Payment Type: '; else print 'Card Type: '; print $paymentinfo['cardtype']."\n";?>
<?php if(mb_strtolower($paymentinfo['cardtype']) != 'paypal') {?>Card No:**********<?php print $paymentinfo['cardNum']."\n";} ?>
<?php if(mb_strtolower($paymentinfo['cardtype'])!='brimar' && mb_strtolower($paymentinfo['cardtype'])!='paypal'){ ?> Card Expiration : <?php print $paymentinfo['expiration']."\n";}?>

<?php
if($orderstatus === '1' && mb_strtolower($paymentinfo['cardtype']) == 'paypal'){
?>
Transaction: Pending
<?php
} else {
?>
Transaction: Approved
<?php
}
?>
Amount Charged: <?php print $paymentinfo['total_amount']."\n";?>
---------------------------------------------------------------------------------------------------------------------------
<?php
	foreach ($cart as $key => $value){
?>

<?php if($value['sku_code']) echo 'Item #'.$value['sku_code'] . "\n";
foreach($value as $cart_key=>$cvalue){
	if($cart_key=='size') echo 'Size: '.$cvalue ."\n";
	if($cart_key=='material') echo 'Material: '.$cvalue."\n";
	if($cart_key=='sale_percentage' && !empty($cvalue)) echo 'You saved' . htmlspecialchars($cvalue,ENT_QUOTES,"UTF-8") . '%';
	if($cart_key=='attribute'){if(!empty($cvalue)){foreach($cvalue as $sub_key=>$att_value){echo $sub_key.': '.$att_value."\n";}}}
	if($cart_key=='builder_attributes'){
		if(!empty($cvalue)){
			foreach ($cvalue as $subkey =>$builder ){
				$label=$builder['builder_label'];
				if($builder['builder_setting_display']=='Y'){if ( $builder['builder_subsetting'] == 'mountingoptions' || $builder['builder_subsetting'] == 'antigraffiti' || $builder['builder_setting'] == 'scheme' || $builder['builder_setting'] == 'layout' || $builder['builder_setting'] == 'text' || $builder['builder_setting'] == 'artwork' ||  $builder['builder_setting'] == 'upload' ){echo $label.': '.$builder['builder_value_text']."\n";}}
				if ( $builder['builder_subsetting'] == 'upload') {
					echo $builder['builder_label'] . ': ' . $builder['upload_name'] . PHP_EOL;
				}
			}
		}
	}
if($cart_key=='stock_custom') $stock_custom=$cvalue;

if(($cart_key=='file_name') && !empty($cvalue)){
	echo "Custom Image Uploaded: Yes\n";
}
if($cart_key=='design_service'){
	if($cvalue==TRUE && $stock_custom=='C'){
		echo 'Design Adjustment: We will adjust your design for best appearance.'."\n";
	}else if($cvalue==FALSE && $stock_custom=='C'){
		echo 'Design Adjustment: We will print your design as shown.'."\n";
	}
}
if(($cart_key=='comment') && ($cvalue!=NULL)){
	echo 'Instructions: '.$cvalue."\n";
}
if(($cart_key=='builder_comment') && ($cvalue!=NULL)){
	echo 'Instructions: '.$cvalue."\n";
}
}
if($value['quantity']) echo 'Quantity: '.$value['quantity']."\n";
if($value['price']) echo 'Price: $'.$value['price']."\n";
if($value['total']) echo 'Total: $'.$value['total']."\n-----------------------------------------------------------------------------------------------------------------------------";
	}
?>

<?php if($purchase_order || $tag_job){ ?>--------------------------------------------------------------------------------------------------------------------------- <?php }?>
<?php if($purchase_order){
	echo "Purchase Order Number: ".$purchase_order."\n";
}?>
<?php if($tag_job){
	echo "Tag/Job Name: ".$tag_job."\n";
}?>

Order Subtotal: $<?php print $subtotal."\n";?>
<?php if($coupon_value!='0.00'){
	echo "Discount: -$".$coupon_value."\n";
}
?>
Shipping Charge: $<?php print $shippingcharge."\n";?>
Sales Tax: $<?php print $salestax."\n";?>
Invoice Total: $<?php print $invoicetotal."\n";?>

<?php
if($expediated_shipping){
	echo "Need your stuff shipped faster? ".$expediated_shipping."\n";
}

if($comments){
echo "Your Comments: " . $comments . "\n";
}
?>
---------------------------------------------------------------------------------------------------------------------------

Thank you and please come visit us again at www.SafetySign.com
<?php if ( $shipmethod == 'Customer Pickup' ) { ?>

Please check this order for accuracy and contact us immediately if any information is incorrect.

Your order should be available for pickup on <?php echo $arrival_date; ?>.
We'll send you an email when your order# <?php echo $orderno; ?> is ready to pick up.

<?php
if (mb_strtolower($paymentinfo['cardtype']) != 'paypal') {
echo "* You will be charged when your order is ready for pickup.";
}
?>

You can access the following information on our website:
<?php if ( !$guest ) { ?>
My Account - Change any of your personal information
<?php echo $account_url."\n"; ?>
<?php } ?>
<?php
} else {
?>
When your order #<?php echo $orderno; ?> has shipped, we will e-mail you the tracking number and the ability
to track the shipment's up-to-the-moment progress!

Please check this order for accuracy and contact us immediately if any information is incorrect.

Production Time Table
Stock Items ship from our warehouse the same or next business day, Custom items ship in 3 to 5 days.

<?php
if (mb_strtolower($paymentinfo['cardtype']) != 'paypal') {
	echo "* You will be charged when your order ships.";
}
?>

Note: Because orders are processed immediately, we are unable to accomodate order changes or cancellations; erroneously ordered items must be returned after delivery.
You can access the following information on our website:

<?php if (!$guest) { ?>
My Account - Change any of your personal information <?php echo $account_url."\n"; ?>
<?php } ?>

Order Tracking - Check online the status of your order.
<?php echo $track_url; ?>
<?php } ?>

----------------------------------------------------------------------------

We thank you for your business and welcome questions or comments.

SAFETYSIGN.COM
----------------------------------
Brimar Industries
P.O. Box 467
64 Outwater Lane
Garfield, NJ 07026

Contact Customer Service
----------------------------------
Phone: 800-274-6271
Fax: 800-279-6897
E-mail: <?php print EMAIL_SERVICE;?>

Hours of Operation
----------------------------------
9am - 5pm Eastern
Monday - Friday
