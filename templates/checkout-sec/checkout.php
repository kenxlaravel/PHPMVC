<?php
	session_start();

 	if($error_array['error_msg'] || $error_msg_billing || $error_brimar) {
		print "<p class='error'>".$error_msg_billing.$error_array['error_msg'].$error_brimar;
		print "</p>";
	}
	//Get special messages for freight
 	$message = $ObjShoppingCart->getMessage();
	if(!empty($message['freight_item'])){
		print "<p class='success'><span class='bold'>Freight Shipment Notice:</span><br />".htmlspecialchars($message['freight_item'],ENT_QUOTES,"UTF-8")."</p>";
	}
?>
<?php
	if ($_SESSION['admin'] == 1 AND $_SESSION['adminID'] == $_SESSION['CID']) {

		echo "<p id='admin-bar' class='admin-notices pad-left-10'><span class='bold'>You are logged in as administrator:</span> " . $_SESSION['Useremail'] . "<br /><span class='bold'>You are checking out as user: </span>" . $_SESSION['adminAccount'] . "</p>";
	}
?>


<?php
	// If there are errors, loop through them and output them
	if (!empty($_SESSION['errors'])) {
		echo "<p class='error'>";

		$i = 1;

		foreach($_SESSION['errors'] AS $error) {
			echo ($i > 1  ? '<br />' : '') . $error;
			$i++;
		}

		echo "</p>";

		$_SESSION['errors'] = NULL;
	}


	// If there's a paypal error, output it
	if ($_SESSION['paypal_error'] > 0) {

		$paypal_error = $objOrders->getPaypalError($_SESSION['paypal_error']);

		echo "<p class='error'>" . $paypal_error . " (code: " . $_SESSION['paypal_error'] . ")</p>";

		$_SESSION['paypal_error'] = NULL;
		$_SESSION['paypal_error_short'] = NULL;
		$_SESSION['paypal_error_long'] = NULL;
		$_SESSION['paypal_error_severity_code'] = NULL;
	}
?>



<div id="place-order-container-bottom" class="place-order-container append-bottom row span-24">
	<div class="rounded-corner span-24">
		<?php
		if(isset($_SESSION['UserType'])) {
		?>
		<p class="h3">Our single page Check Out is easy to use!</p>
		<p class="h6">
<?php
		if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
			Simply fill out the information below, review your items, then click the Place Order button at the bottom of the page.
<?php
		} else {
?>
			Simply fill out the information below, review your items, then click the PayPal button at the bottom of the page.
<?php
		}
?>
		</p>

		<?php
		}
		?>
	</div>


</div>

<div class="form-container span-24 row append-bottom">
	<div id="email-address-container"  class="span-24">
		<p class="h4 h4-rev pad-left-10">1. Confirm the Email Address to be used for Order Confirmation</p>
		<div id="email-address" class="span-11 append-bottom prepend-top">
			<label for="email" class="required">Email </label>
			<input type="text" class="text required" id="email" name="email" value="<?php echo htmlspecialchars($prefill['email'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" />
		</div>
		<div class="span-12 append-bottom half" id="newsletter-signup">
			<input type="checkbox" class="checkbox" id="news-letter" name="news_letter" value="yes" checked="checked"/>
			<p><strong>Yes</strong> I want to know about new products and weekly offers from SafetySign.com. <br /> Send me weekly email promotions!</p>
		</div>

	</div>

</div>
<!--[if !IE]> /Email <![endif]-->

<div class="row span-24 append-bottom half">
	<div id="your-shipping-address" class="scrolling verification-group">
		<p class="h4 h4-rev pad-left-10">
<?php
		if (isset($_REQUEST['layout']) && $_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
			2. Enter Your Billing &amp; Shipping Information
<?php
		} else {
?>
			2. Enter Your Shipping Information
<?php
		}
?>
		</p>
		<div id="addresses">
<?php
		if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
			<div id="billing-add">
			 		<p class="h5 append-bottom">Billing Address</p>
			 		<div class="paypal-billing-wrapper" <?php if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false && ($default_payment_method != 'PayPal')) { echo 'style="display:none"'; } ?>>
						<div class="paypal-billing">
							<p class="h5">You have chosen to pay with PayPal.</p>
							<p>The address attached to your PayPal account will be used as your billing address.

						</div>
					</div>

				<div class="billing-form-wrapper">
			<?php if(sizeof($address_array)>1){?>

                <div id="saved-billing-address" class="wide-label append-bottom">
					<label for="default_billing_address" >Use Saved Address:</label>
					<select name="saved-billing" class="wide-dropdown" id="default_billing_address">
						<option value="">Saved Addresses</option>
						<?php foreach($address_array as $key=>$value){

							?>
                         <option <?php if($value['default_billing']=='1') print "selected";?>  value="<?php print $value['public_id'];?>">
                         	<?php print htmlspecialchars($value['street_address'],ENT_QUOTES,"UTF-8");?>
                         </option>
                         <?}?>
					</select>
				</div>
               <?php
               }
			   ?>
				<p class="clear special-note">Your billing name &amp; address must match your credit card information.</p>
				<div class="span-11 append-bottom prepend-top half">
					<div>
						<label for="bill-company">Company</label>
						<input type="text" class="text copy" id="bill-company" name="company" value="<?php echo htmlspecialchars($prefill['billing']['company'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="30" />
					</div>
					<div>
						<label for="bill-first-name" class="required">First Name</label>
						<input type="text" class="text required hastooltip copy" id="bill-first-name" name="firstname" value="<?php echo htmlspecialchars($prefill['billing']['firstname'], ENT_QUOTES, 'UTF-8'); ?>"  maxlength="20" />
					</div>
					<div>
						<label for="bill-last-name" class="required">Last Name</label>
						<input type="text" class="text required hastooltip copy" id="bill-last-name" name="lastname" value="<?php echo htmlspecialchars($prefill['billing']['lastname'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="20" />
					</div>
					<div class="phone-number">
						<label for="bill-phone-number"  class="required">Phone</label>
						<input type="text" class="text phone-input required copy " id="bill-phone-number" name="phonenumber" value="<?php echo htmlspecialchars($prefill['billing']['phone'], ENT_QUOTES, 'UTF-8'); ?>" />
					</div>
					<div>
						<label for="bill-fax-number">Fax</label>
						<input type="text" class="text fax-input" id="bill-fax-number" name="billfaxnumber" value="<?php echo htmlspecialchars($prefill['billing']['fax'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="15"/>
					</div>
					<!-- Simple Mod-->
					<div>
						<label for="bill-address1" class="required">Address Line 1</label>
						<input type="text" class="text required copy" id="bill-address1" name="address1" value="<?php echo htmlspecialchars($prefill['billing']['address1'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" />
					</div>
					<div>
						<label for="bill-address2">Address Line 2</label>
						<input type="text" class="text copy" id="bill-address2" name="address2"
								value="<?php echo htmlspecialchars($prefill['billing']['address2'], ENT_QUOTES, 'UTF-8');?>" maxlength="100" />
					</div>
					<div>
						<label for="bill-city" class="required">City</label>
						<input type="text" class="text required copy" id="bill-city" name="city2" value="<?php echo htmlspecialchars($prefill['billing']['city'], ENT_QUOTES, 'UTF-8')?>" maxlength="20" />
					</div>
					<div class="address-state-wrap">
						<label for="state" class="required">State/Province</label>
						<select name="state" id="state" class="text state-input required">
							<?php
							$state=$ObjUserAddress->listZones();
							foreach($state as $key => $value)
							{
							?>
							<option value="<?php print $value['zone_code'];?>"
								 <?php if((isset($_REQUEST['state']) && $_REQUEST['state']==$value['zone_code']) || ($billing_array['state']==$value['zone_code'] && $_REQUEST['state']=='') || ($_SESSION['checkout_form']['state'] == $value['zone_code'])) print "selected"; ?>>
								 <?php print htmlspecialchars($value['zone_name'],ENT_QUOTES,"UTF-8");?>
							</option>
							<?php
							}
					?>
						</select>
					</div>
					<div>
						<label for="bill-zip-code" class="required">Zip/Postal Code</label>
						<input type="text" class="text zip-input required copy" id="bill-zip-code" name="zipcode"
							value="<?php echo htmlspecialchars($prefill['billing']['zipcode'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="12" />
					</div>
					<div>
						<label for="bill-country">Country</label>
						<select name="country" class="country-select" id="bill-country">
<?php
						//Grab a list of available countries
						$countries = $ObjUserAddress->listCountries();

						//Use previous formdata to select country, and if not default to US
						if (isset($_SESSION['checkout_form']['country'])) {
							$prefill_country = $_SESSION['checkout_form']['country'];
						} else if (!empty($billing_array['country'])) {
							$prefill_country = $billing_array['country'];
						} else {
							$prefill_country = 'US';
						}

						foreach($countries AS $country) {
?>

							<option value="<?php echo $country['countries_iso_code_2']; ?>"
								<?php echo ((!isset($_REQUEST['country'])) && $country['countries_iso_code_2'] == $prefill_country ? ' selected' : '');
								 if( isset($_REQUEST['country']) && $country['countries_iso_code_2']==$_REQUEST['country'] ) print "selected"; ?>
								data-state-required=<?php echo json_encode($country['zone']);?>>
								<?php echo htmlspecialchars($country['countries_name'],ENT_QUOTES,"UTF-8"); ?></option>
<?php
						}
?>
						</select>
					</div>
					<div class="prepend-top span-10"> </div>
				</div>
				</div>

			</div>
<?php
			}
?>

			<div id="shipping-address">
			 	<p class="h5 append-bottom">Shipping Address</p>
                <?php
				if(sizeof($address_array)>1)
				{
				?>
				<div id="saved-shipping-address" class="wide-label append-bottom">
					<label for="default_shipping_address" >Use Saved Address:</label>
					<select name="saved-shipping" class="wide-dropdown" id="default_shipping_address">
						<option value="">Saved Addresses</option>
                        <?php
						foreach($address_array as $key => $ship_value)
						{
						?>
                  <option <?php if($ship_value['default_shipping']=='1') print "selected";?>  value="<?php print $ship_value['public_id'];?>">
                  			<?php print htmlspecialchars($ship_value['street_address'],ENT_QUOTES,"UTF-8");?></option>
                  		<?php
						}
						?>
					</select>
				</div>
				<?php
                }
				?>

				<p class="append-bottom bottom-space clear special-note">We do not ship to P.O. Boxes.</p>
				<div id="checkbox-copy-wrapper">
<?php
		if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
				<input type="checkbox" class="checkbox" id="copy-billing" name="copy_billing" value="Y"  <?php if($_POST['copy_billing']=="Y") print "Checked";?>/>
				<p id="checkbox-copy"><strong>Use my billing address as my shipping address</strong></p>
<?php
		}
?>
				</div>
				<div id="shipping-address-fields">
				<div>
					<label for="ship-company">Company</label>
					<input type="text" class="text" id="ship-company" name="shipcompany"
						 value="<?php echo htmlspecialchars($prefill['shipping']['company'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="30" />
				</div>
				<div>
					<label for="ship-first-name" class="required">First Name</label>
					<input type="text" class="text required" id="ship-first-name" name="shipfirstname"
						value="<?php echo htmlspecialchars($prefill['shipping']['firstname'], ENT_QUOTES, 'UTF-8'); ?>"  maxlength="20" />
				</div>
				<div>
					<label for="ship-last-name" class="required">Last Name</label>
					<input type="text" class="text required" id="ship-last-name" name="shiplastname"
						value="<?php echo htmlspecialchars($prefill['shipping']['lastname'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="20" />
				</div>
				<div class="phone-number">
					<label for="ship-phone-number"  class="required">Phone</label>
					<input type="text" class="text phone-input required copy " id="ship-phone-number" name="shipphonenumber"
						value="<?php echo htmlspecialchars($prefill['shipping']['phone'], ENT_QUOTES, 'UTF-8'); ?>" />
				</div>
				<div>
					<label for="ship-address1" class="required">Address Line 1</label>
					<input type="text" class="text required" id="ship-address1" name="shipaddress1"
						value="<?php echo htmlspecialchars($prefill['shipping']['address1'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" />
				</div>
				<div>
					<label for="ship-address2">Address Line 2</label>
					<input type="text" class="text" id="ship-address2" name="shipaddress2"
					value="<?php echo htmlspecialchars($prefill['shipping']['address2'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="100" />
				</div>
				<div>
					<label for="ship-city" class="required">City</label>
					<input type="text" class="text required" id="ship-city" name="shipcity"
						value="<?php echo htmlspecialchars($prefill['shipping']['city'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="20" />
				</div>
				<div>
					<label for="ship-state" class="required">State/Province</label>
				    <select name="sstate" id="ship-state" onchange="VerifyStateChange(this.value);">
				    	<option value=''></option>

                      <!--[if !IE]><option>select state</option><![endif]-->
                      <?php
                      	$state=$ObjUserAddress->listZones();
						//Grab a list of available countries
						$countries = $ObjUserAddress->listCountries();
				 		 foreach($state as $key  => $value)
						{
						?>
                      <option value="<?php print $value['zone_code'];?>"

                          <?php if($shipping_array['state']==$value['zone_code'] && $_REQUEST['state']=='') print "selected"; ?>>
                      	<?php print htmlspecialchars($value['zone_name'],ENT_QUOTES,"UTF-8");?>
                      </option>
                  <?php
						}
						?>
						<?php
						foreach($countries as $key => $value){
							 if($value['countries_iso_code_2']==$t_country){
								if($value['zone']=='false')
									$state='';
							}
						 }
						if($state==''){?><option value="" selected></option><?php }?>
                    </select>
				</div>
				<div id="zip-required">
					<label for="ship-zip-code" class="required">Zip/Postal Code</label>
					<input type="text" autocomplete="off" class="text zip-input required copy" id="ship-zip-code"
						name="shipzip"
						value="<?php echo htmlspecialchars($prefill['shipping']['zipcode'], ENT_QUOTES, 'UTF-8'); ?>" maxlength="12"/>
				</div>
				<div>
					<label for="ship-country">Country</label>
					<select name="shipcountry" id="ship-country" class="country-select">
					<?php

						//Use previous formdata to select country, and if not default to US
						if (isset($_SESSION['checkout_form']['shipcountry'])) {
							$prefill_shipcountry = $_SESSION['checkout_form']['shipcountry'];
						} elseif (!empty($shipping_array['country'])) {
							$prefill_shipcountry = $shipping_array['country'];
						} else {
							$prefill_shipcountry = 'US';
						}

						foreach($countries AS $country) {
?>							<option value="<?php echo $country['countries_iso_code_2']; ?>"	<?php
								if ($country['countries_iso_code_2'] == $prefill_shipcountry && (!isset($_REQUEST['shipcountry'])) ) print "selected";
								if ($country['countries_iso_code_2']==$_REQUEST['shipcountry'] && isset($_REQUEST['shipcountry'])) print "selected"; ?>
								data-state-required=<?php echo json_encode($country['zone']);?> >
								<?php echo htmlspecialchars($country['countries_name'],ENT_QUOTES,"UTF-8"); ?></option>
<?php
						}
						?>
					</select>
				</div>
				</div> <!-- for copying address from billing -->
				<div id="address-verification" class="prepend-top">
					<p class="span-6">Ensure the accurate delivery of your order by verifying your address.</p><button type="button" name="verify-address" id="address-verify-button" class="small blue button right-side" onclick="javascript:verifyShippingAddress('shipping');">Address Verification</button><div id="verifyaddress" class="prepend-top clear"> </div>
				</div>

			</div>
		</div>

<?php
	if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
		<!-- billing det-->
		<div id="addresses-details">

<?php
	}

	if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
			<div id="billing-det">
				<p class="h5 append-bottom">Payment Method</p>
					<div id="payment-method" style="z-index:9;">
					<div id="pay-by-this-method" class="append-bottom">
						<ul id="payment-method-chooser">
							<li id="credit" class="<?php if ( $default_payment_method === 'CreditCard' ) print "selected";?>"><a href="#pay-with-credit" rel="credit-card" class="" > Credit/Debit Card</a></li>
							<li id="brimar" class="<?php if ( $default_payment_method === 'Brimar' ) print "selected"; ?>"><a href="#pay-with-brimar" rel="net30-account" class=""> Brimar Net 30</a></li>
<?php
							if ($show_paypal == true) {
?>
								<li id="paypal" class="<?php if ( $default_payment_method === 'PayPal' ) print "selected"; ?>"><a href="#pay-with-paypal" rel="paypal" class="">PayPal</a></li>
<?php
							}
?>
						</ul>
					</div>
					<input type="hidden" id="payment-choice" name="payment" value="<?php echo htmlspecialchars(!empty($_REQUEST['payment']) ? $_REQUEST['payment'] : $default_payment_method, ENT_QUOTES, 'UTF-8'); ?>">
					<!--[if !IE]> Payment method inserted here via Ajax based on payment choice <![endif]-->
					<div id="insert-payment-method">
						<div id="credit-card" class="credit-cards payment-formlast append-bottom">
							<div>
								<div class="span-11 card-number <?php if ( $default_payment_method === 'Brimar' ) print "net30-card"; ?>">
									<label id="credit-card-number-label" for="credit-card-number" class="required"><span class="text">
										<?php if ( $default_payment_method === 'Brimar' ) print "Account"; else print "Card"; ?>
										Number</span> </label>
									<input name="<?php if ( $default_payment_method === 'Brimar' ) print "brimar_card_number"; else print "credit_card_number";?>" id="credit-card-number" class="text toggleccpayment required nospace" type="text" title=""
										value="<?php if ( $default_payment_method === 'Brimar' ) {
													print !empty($_REQUEST['brimar_card_number']) ? $_REQUEST['brimar_card_number'] : base64_decode($brimar_net['account_no']);
												} else {
													print $_REQUEST['credit_card_number'];
												} ?>"
										size="19" maxlength="16"  />
								</div>
								<div class="span-9 last" id="security-code-wrapper">
									<label for="security-code" class="required security-code">Security Code </label>

									<input name="<?php if ( $default_payment_method === 'Brimar' ) print "brimar_security_number"; else print "security_code"; ?>"
										id="security-code" class="text toggleccpayment required" type="text"  size="19"
										value="<?php if ( $default_payment_method === 'Brimar' ) {
													print !empty($_REQUEST['brimar_card_number']) ? $_REQUEST['brimar_security_number'] : base64_decode($brimar_net['security_code']);
												} else {
													print $_REQUEST['security_code'];
												} ?>"
										maxlength="4" />
									<div id="security-code-help-wrapper"> <a id="security-code-help" href="#help" class="help" <?php if ( $default_payment_method === 'CreditCard' ) print "style='display:none;'";?>>What's This?</a>

										<div id="security-code-help-information" class="notice message tooltip">
											<h4>Credit Card Security Code</h4>
											<dl id="checkout-help-instructions">
												<dt>Visa, Mastercard and Discover</dt>
												<dd>On most credit cards, including those issued by MasterCard and Visa, this number appears on the back of your card above your signature. It is commonly the last 3 digits following your credit card number.</dd>
												<dd id="mastercard" class="security-code-sample">Security Code</dd>
												<dt>American Express &amp; Optima</dt>
												<dd>On American Express and Optima cards, the code is four digits and appears above your credit card number.</dd>
												<dd id="amex" class="security-code-sample">Security Code</dd>
											</dl>
										</div>
									</div>
								</div>
							</div>
							<div id="credit-card-expires-container" class="span-9" <?php if ( $default_payment_method === 'Brimar' || $default_payment_method === 'PayPal') print "style='display:none;'";?> >
								<label for="credit-card-expires" class="">Expires</label>
								<select name="CCExpiresMonth"  class="toggleccpayment <?php if ( $default_payment_method === 'CreditCard' ) print "required"; ?>">
									<option value="">Month</option>
									<option value="01" <?php if($_REQUEST['CCExpiresMonth']=='01') { print 'selected';} ?> >01-January</option>
									<option value="02" <?php if($_REQUEST['CCExpiresMonth']=='02') { print 'selected';} ?> >02-February</option>
									<option value="03" <?php if($_REQUEST['CCExpiresMonth']=='03') { print 'selected';} ?> >03-March</option>
									<option value="04" <?php if($_REQUEST['CCExpiresMonth']=='04') { print 'selected';} ?> >04-April</option>
									<option value="05" <?php if($_REQUEST['CCExpiresMonth']=='05') { print 'selected';} ?> >05-May</option>
									<option value="06" <?php if($_REQUEST['CCExpiresMonth']=='06') { print 'selected';} ?> >06-June</option>
									<option value="07" <?php if($_REQUEST['CCExpiresMonth']=='07') { print 'selected';} ?> >07-July</option>
									<option value="08" <?php if($_REQUEST['CCExpiresMonth']=='08') { print 'selected';} ?> >08-August</option>
									<option value="09" <?php if($_REQUEST['CCExpiresMonth']=='09') { print 'selected';} ?> >09-September</option>
									<option value="10" <?php if($_REQUEST['CCExpiresMonth']=='10') { print 'selected';} ?> >10-October</option>
									<option value="11" <?php if($_REQUEST['CCExpiresMonth']=='11') { print 'selected';} ?> >11-November</option>
									<option value="12" <?php if($_REQUEST['CCExpiresMonth']=='12') { print 'selected';} ?> >12-December</option>
								</select>

								<select name="CCExpiresYear" id="credit-card-expires" class="toggleccpayment <?php if ( $default_payment_method === 'CreditCard' ) print "required"; ?>">
									<option value="">Year</option>

<?php
	// Loop through 15 years
	for ($i = 0; $i < 15; $i++) {

		// Grab the four-digit representation of the current year, e.g.: 2014 and add the current iteration
		$cc_year = date('Y') + $i;
?>
									<option value="<?php echo mb_substr($cc_year, -2); ?>" <?php if($_REQUEST['CCExpiresYear'] == mb_substr($cc_year, -2) ) { print 'selected';} ?> ><?php echo $cc_year; ?></option>
<?php
	}
?>

								</select>
							</div>
						</div>
						<div id="brimar-acct" class="prepend-top">
							<p id="net30-pitch" <?php if ( $default_payment_method === 'CreditCard' || $default_payment_method === 'PayPal') print "style='display:none;'";?> ><strong>Brimar Net30 Account Is ONLY for:</strong><br />
								1. Users that already have a Net 30 Account online at SafetySign.com (10 digit account number with 4 digit Security Code supplied by Brimar).<br />
								2. Users that have a Brimar Open Account and would like to use it online as well. Please e-mail or call us and we will e-mail you the 10 digit online account number and 4 digit security code when your account is set up.<br />
								3. Customers that would like to order with PO# instead of credit cards and would like to open a Net 30 Terms Account with Brimar Industries, Inc.<br />
								<br />
								<strong>Would you like to open a Net30 Account with Us?</strong><br />
								Fill out the online <a href="http://fs4.formsite.com/michaelbrimarcom/creditapp/index.html" target="_blank" rel="nofollow">credit application</a> and we&#8217;ll get back to you when your application has been processed. <br />
							</p>
							<p id="credit-card-statement" <?php if ( $default_payment_method === 'Brimar' || $default_payment_method === 'PayPal') print "style='display:none;'";?> >Charges to your credit card will appear on your Statement as SAFETYSIGN.COM</p>
						</div>
						<div id="paypal-acct" class="prepend-top" <?php if ( $default_payment_method === 'CreditCard' || $default_payment_method === 'Brimar') print "style='display:none;'";?> >
							<div class="span-3 paypal-logo"><!-- PayPal Logo --><table border="0" cellpadding="10" cellspacing="0" align="center"><tr><td align="center"><a href="https://www.paypal.com/webapps/mpp/paypal-popup" title="How PayPal Works" onclick="javascript:window.open('https://www.paypal.com/webapps/mpp/paypal-popup','WIPaypal','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700'); return false;"><img src="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" border="0" alt="PayPal Logo"></a></td></tr></table><!-- PayPal Logo --></div>
							<div class="span-8">
								<p id="paypal-info"><strong>You have chosen to check out via PayPal</strong><br />Please continue the checkout process. When you click the PayPal button at the bottom of the page, a box will appear to allow you to make payment via PayPal.</p>
								<p>When your transaction is complete, you will be directed to our Order Confirmation page.</p>
							</div>
						</div>
					</div>

		            <input type="hidden" name="savecreditcard" id="save-credit-card" class="first-margin"  value="N">
                </div>
			</div>
<?php
		}
?>
			<!-- shipping det-->

			<div id="shipping-det">
				<div class="span-11">
					<p class="h5 append-bottom left-side">Shipping Method</p>
					<button type="button" name="shipping-chooser" id="shippingchooser" class="small blue button right-side append-bottom prepend-top-5">Calculate Shipping</button>
					<?php
				 	//If user is an admin, show them extra freight information
					if ($_SESSION['admin'] === TRUE AND $_SESSION['adminID'] == $_SESSION['CID']) {

						//Grab an array of all the freight data
						$freightInfo = $ObjShoppingCart->getFreightInfo();

						//Set all the defaults
						$packageWeight = 0;
						$totalWeight = 0;
						$freightPackages = 0;
						$freightDim = 0;

						//Loop through all our data
						foreach ($freightInfo as $key => $value) {

						 	if ($value['number_pkgs'] > 0 ) {
								$freightPackages += $value['number_pkgs'];
						 	} else {
						 		$packageWeight += $value['true_weight'];
						 	}

							$totalWeight += $value['true_weight'];

							//If we ever have dimensional weight, increment $freightDim
							if (mb_strtolower($value['dim_weight_ups']) == 'y' || mb_strtolower($value['dim_weight_fedex']) == 'y' ) { $freightDim++; }
						}

						// get count of packages based on package weight
					 	$ObjShippingCharges = new ShippingCharges();
						if ( $packageWeight > 0) $freightPackages += $ObjShippingCharges->getWeight($packageWeight);

?>
						<div class="admin-notices append-bottom">
						<p class="font-18 bold">Admin Insight:</p>
						<ul class="bottom-space">

							<li><span class="bold">Total cart weight: </span><?php print htmlspecialchars($totalWeight,ENT_QUOTES,"UTF-8"); ?> lbs.</li>
							<li><span class="bold">Estimated shipping packages: </span><?php print htmlspecialchars($freightPackages,ENT_QUOTES,"UTF-8"); ?></li>
							<li><span class="bold">Irregular packages: </span><?php print htmlspecialchars($freightDim,ENT_QUOTES,"UTF-8"); ?></li>
						</ul>
						</div>

<?php
					}
?>

<?php
			if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
				</div>

<?php
			}
$ship_charge= $ObjShippingCharges->getShippingChargesBySession();
?>
	<div class="shipping-options-loader append-bottom prepend-2 clear <?php if(!empty($ship_charge)) echo 'hidden';?>" >
		<img src="/new_images/ajax-loader-blue.gif" class="left-side" />
		<p class="h4 left-side prepend-top pad-left-10">Retrieving shipping rates</p>
	</div>
<div id="shipping-options-wrapper" class="span-11">
	<div id="shipping-options" class="sm" ></div>


<?php
	// If UPS rates were returned and the checkout wasn't just submitted, store the default shipping method/rate in the database.
	if ($_POST['shipping_submit'] != 'Submit') {

		// Set global services_changess_pre (which is actually the shipping charge), from database.
		 $ObjShippingCharges->getShippingChargesBySession();
	}

?>
</div>

<div id="no-shipping-notice" <?php if(empty($ship_charge)) {echo "class=hidden"; }?> ><span>Please enter your shipping address to choose your shipping method.</span></div>

<div id="shipping-options20">

</div>
<!-- START CUSTOMER PICKUP CODE -->

		<div class="hidden">
		<div id="customer-pickup">
		<div style="margin:20px;padding:20px;width:450px;height:250px;" >
			<h4 style="float:none;margin-bottom:0;">Customer Pickup Information</h4>
			<p>You can pick up your order at our Garfield, NJ facility.</p>
			<div style="float:none;padding:5px 10px;margin:10px 0;background-color:#e6e6e6;text-align:center;">
				<p style="margin-bottom:0;"><strong>Estimated Availability Date</strong></p>
				<h5 style="float:none;">
				<?php
							$ObjShippingCharges=new ShippingCharges();
							if(!$arrival)
							{
								 $arrival_timestamp=$ObjShoppingCart->getEstimatedDate(1);
								 $arrival_date = substr($arrival_timestamp['shipdate_formatted'], 6, 2);
								$arrival_month = substr($arrival_timestamp['shipdate_formatted'], 4, 2);
								$arrival_year = substr($arrival_timestamp['shipdate_formatted'], 0, 4);

								$arrival_formatted = date("F jS, Y", mktime(0, 0, 0, $arrival_month, $arrival_date, $arrival_year));
								print htmlspecialchars($arrival_formatted,ENT_QUOTES,"UTF-8");

							if(!$arrival_formatted)
									print "Call for ETA";
							}
						else
							print htmlspecialchars($arrival,ENT_QUOTES,"UTF-8");?>
					</h5>
				<p>We will send you an email confirmation when your order is ready.</p>
			</div>
			<div style="width:50%;height:65px;float:left;">
				<p class="h6">Our facility is located at:</p>
				<p>64 Outwater Lane <br>
					Garfield, NJ 07026</p>
			</div>
			<div style="width:50%;height:65px;float:left;">
				<p class="h6">Our hours of operation are:</p>
				<p>9am - 5pm Eastern <br>
					Monday - Friday</p>
			</div>
			<p style="font-style:italic;">For your reference, this information will be included on your order confirmation. <br>
				If you have further questions, please contact us.</p>
		  </div>
		</div>
	</div>
<!-- END CUSTOMER PICKUP CODE -->


<div class="expediated-shipping  prepend-top span-11">

					<div>
						<p class="h6">* Need your items by a certain date? <span class="small">(optional)</span></p>
						<p>The provided arrival dates are estimates only. We will try to accommodate requests entered in the box below if you need your order by a specific date on or before the provided estimate.</p>
					</div>
					<div class="span-11" id="expediated-textbox">
						<textarea id="expediated-shipping-comments" name="expediated-shipping" class="text" rows="4" cols="6"><?php echo htmlspecialchars($prefill['expediated-shipping'], ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>
				</div>
<?php
			if(empty($message['freight_item'])){
?>

			<p class="pad-left-10 clear h6 customer-ship-account-trigger">  <a href="#customer-ship-account"> <?php echo (isset($_REQUEST['applied-shipping-account']) && mb_strlen(trim($_REQUEST['applied-shipping-account'])) > 0 ? "Using UPS / FedEx account " . trim(htmlspecialchars($_REQUEST['applied-shipping-account'], ENT_QUOTES , 'UTF-8') ) . ". Click to change." : "Click here to enter your own UPS or FedEx account number."); ?>   </a>  <span class="small"><?php echo (mb_strlen(trim($_REQUEST['applied-shipping-account'])) > 0 ? '' : '(optional)'); ?></span></p>

			<div class="pad-bot-15 pad-left-10 customer-ship-account <?php if($error_array['user_ship'] === TRUE ) print ""; else print 'hidden'?>" id="customer-ship-account">

			<?php
				if($error_array['user_ship'] == TRUE){
			?>
					<p id="customer-ship-account-error" class ="error">The account number you entered is invalid. Please enter your six-digit UPS or nine-digit FedEx account number.</p>
			<?php

				}else{
			?>
					<p id="customer-ship-account-error"></p>
			<?php

				}

				?>
					<p class="append-bottom">Enter your six-digit UPS or nine-digit FedEx account number below and we won’t charge you for shipping; the carrier will bill you directly at your negotiated rates. <span class="bold">After applying your account number, please select your desired shipping method.</span></p>

					<input type="text" name="shipping-account" size="65" id="shipping-account" class="left-side" value="<?php if($error_array['user_ship'] == TRUE) print htmlspecialchars($_REQUEST['applied-shipping-account'], ENT_QUOTES , 'UTF-8'); ?>" />
	                <input type="hidden" name="applied-shipping-account" id="applied-shipping-account" value="<?php print htmlspecialchars($prefill['shipping-account'], ENT_QUOTES, 'UTF-8'); ?>">
	                <button id="shipping-account-apply" type="button" class="blue button left-side"><?php echo (mb_strlen(trim($_REQUEST['applied-shipping-account'])) > 0 ? 'Update ' : 'Apply ');?>Account</button>
	                <div class="customer-ship-account-cancel fake-link first-margin prepend-top-5"><?php echo (mb_strlen(trim($_REQUEST['applied-shipping-account'])) > 0 ? 'Remove' : 'Cancel'); ?></div>

				</div>
<?php
			}
?>
		</div>
	</div>
</div>
<div class="row span-24 append-bottom">
	<div id="special-payment-info" class="scrolling verification-group">
		<p class="h4 h4-rev pad-left-10">3. Enter Any Special Payment Information</p>
<?php
$coupon_data= Checkout::getCouponBySession();

 ?>

		<div id="coupons" class="special-payment">
			<p class="h5 append-bottom">Do you have a Coupon?</p>
			<div id="coupon-code-container" class="">
				<div class="">
					<label for="coupon-code">Coupon Code:</label>

					<?php if ($_SESSION['admin'] === TRUE AND $_SESSION['adminID'] == $_SESSION['CID']) {

								$objUser = new User();
								//Get all currently valid coupons for dropdown
								$validCoupons = $objUser->getCoupons();
							?>
						<select name="couponcode" id="coupon-code" style="width: 275px;">
							<option value="">No coupon</option>
							<?php

								//Loop through all valid coupons as part of the select menu
								foreach ($validCoupons as $key=>$value) {
									echo "<option value='" . $value[0] . "'>" . $value[0] . $value[1] . "</option>";
								}
							?>
						</select>
					<?php } else { ?>
						<input type="text" id="coupon-code" class="text"  value="<?php
						if (isset($_SESSION['checkout_form']['couponcode'])) {
							print htmlspecialchars($_SESSION['checkout_form']['couponcode'], ENT_QUOTES, 'UTF-8');
						} elseif(isset($_REQUEST['couponcode']) ? $_REQUEST['couponcode'] : NULL) {
							print htmlspecialchars($_REQUEST['couponcode'], ENT_QUOTES , 'UTF-8');
						} elseif($coupon_data['coupon_number']) {
							print htmlspecialchars($coupon_data['coupon_number'],ENT_QUOTES,'UTF-8');
						}?>" name="couponcode" maxlength="30" />
					<?php } ?>
					<button type="button" name="coupon_submit" class="small blue button right-side last-margin prepend-top" onclick="CouponCheck();">Apply Coupon Code</button>

				   <span id="coupon_message"></span>
				  <span id="coupon_message_error"></span>

				</div>

			</div>
		</div>
		<div id="po-number" class="special-payment">
			<p class="h5 append-bottom">P. O. Number &amp; Tag/Job Name</p>
			<div class=" ">
				<label for="purchase_order">Purchase Order Number</label>
				<input type="text" class="text" id="purchase_order" name="purchase_order" value="<?php
				if (isset($_SESSION['checkout_form']['purchase_order'])) {
					print htmlspecialchars($_SESSION['checkout_form']['purchase_order'], ENT_QUOTES, 'UTF-8');
				} elseif(isset($_REQUEST['purchase_order']) ? $_REQUEST['purchase_order'] : NULL) {
					print $_REQUEST['purchase_order'] ;
				} else {
					print htmlspecialchars(isset($customer_info_data['purchase_order']) ? $customer_info_data['purchase_order'] : NULL, ENT_QUOTES , 'UTF-8');
				}?>" maxlength="40" />
			</div>
			<div class="">
				<label for="tag_job">Tag or Job Name</label>
				<input type="text" class="text" id="tag_job" name="tag_job" value="<?php

				if (isset($_SESSION['checkout_form']['tag_job'])) {

						print htmlspecialchars($_SESSION['checkout_form']['tag_job'], ENT_QUOTES, 'UTF-8');

				} else if(isset($_REQUEST['tag_job']) && $_REQUEST['tag_job']) {

                    print htmlspecialchars($_REQUEST['tag_job'], ENT_QUOTES, 'UTF-8');

				} else {

                    print htmlspecialchars(isset($customer_info_data['tag_job']) ? $customer_info_data['tag_job'] : NULL, ENT_QUOTES , 'UTF-8');
				}?>" maxlength="40" />
			</div>
		</div>
		<div id="tax-exempt" class="special-payment">
			<div class="conditional-tax">
			<p class="h5 append-bottom">Tax Exempt</p>
			<p>Note: Tax is only charged on orders shipped to New Jersey.</p>
			<div>
				<input type="radio" value="Y" name="tax_exempt_status" id="exempt" <?php if($customer_info_data['tax_exempt']=='Y' || $_SESSION['checkout_form']['tax_exempt_status'] == 'Y') print "checked";?>  onclick="setSaleTax(this.value);" />
				<span>Yes</span> </div>
			<div class="append-bottom">
				<input type="radio" value="N" name="tax_exempt_status"  id="not-exempt" <?php if( isset($customer_info_data) && $customer_info_data['tax_exempt']=='N' || $_SESSION['checkout_form']['tax_exempt_status'] == 'N') print "checked";if(!$customer_info_data['tax_exempt'] && !$_SESSION['checkout_form']['tax_exempt_status']) print "checked";?> onclick="setSaleTax(this.value);"  />
				<span>No</span> </div>
			<p id="tax-exempt-notice" class="noshow notice prepend-top half"><strong>If you are an NJ customer, please fax your tax exempt certificate to
				800-279-6897 within 24 hours or tax will be added to your order.</strong></p>
			</div>
		</div>
	</div>

</div>
<?php
$cartProducts = $ObjShoppingCart->products;
$count = count($cartProducts);
 	 if($count>0)
		 {

		 	$design_adjust='Design Adjustment:';
?>
</div>
<div class="row span-24">
	<p class="h4 h4-rev pad-left-10">4. Review Your Final Order</p>
	<div id="order-review" class="scrolling verification-group">
		<p class="h5 append-bottom">Order Summary</p>
		<div >
			<!-- This should display the same order summary as the verify page -->
			<div id="order-history-wrapper" class="half">

				<div id="order-history-header">
					<p id="sign-image">Product Image</p>
					<p class="item-descriptions">Item Description &amp; Size</p>
					<p class="qty-forms-in-shopping-cart">Quantity</p>
					<p class="price-per">Each</p>
					<p class="price-total">Price</p>
				</div>
				<div id="order-history" class="edit-info ">
<?php
					$i=-1;
			 		$row=0;
					$sub_total=0;

					foreach($cartProducts as $product) {
						$i++;
						$sku_code=$product->skuCode;
?>
					<dl class="items-list">
						<dt class="sign-thumb">
							<?php if ($product->type=='flash' || $product->type=='streetname') {	?>
							  	<img src="<?php print  CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.htmlspecialchars($product->customImage['customImage'],ENT_QUOTES,"UTF-8");?>" alt="<?php print htmlspecialchars($product->customImage['customImage'],ENT_QUOTES,"UTF-8");?>">
							<?php
							} if($product->type=='builder'){?>
	  							<img src="<?php	print htmlspecialchars($product->customImage['customImage'],ENT_QUOTES,"UTF-8");?>" >
							<?php }if($product->type=='stock'){ ?>
	   							<img src="<?php print htmlspecialchars($product->productImage,ENT_QUOTES,"UTF-8");?>">
							<?php
								}
							?><p><?php print htmlspecialchars($product->skuCode,ENT_QUOTES,"UTF-8");?></p>
						</dt>
						<dd class="item-descriptions">
							<p><span class="bold">Item # </span><?php print htmlspecialchars($product->skuCode,ENT_QUOTES,"UTF-8");?></p>
							<p><span class="bold">Size: </span><?php print htmlspecialchars($product->size,ENT_QUOTES,"UTF-8");?></p>
	 						<p><span class="bold">Material: </span><?php print htmlspecialchars($product->materialDescription,ENT_QUOTES,"UTF-8");?></p>
							<? if( $product->type == 'streetname' ) { ?>
								<p><span class="bold">Preview:</span> Image to the left is for text confirmation purposes only. <a href="<? echo "/images/help-elements/street-name-popups/" . $product->accuracyImage ?>" class="zoom underline no-wrap">Click here</a> to see an example of our print quality.</p>
							<? } ?>
	 					<?if(!empty($product->savingsPercentage)){?>
							<p><span class="cart-percentage">YOU SAVED <?php print htmlspecialchars($product->savingsPercentage,ENT_QUOTES,"UTF-8").'&#37;';?></span></p>
						<?}

							if($product->type=="flash"){ ?>
									<div class="span-8">
										<input name="expanddescription_<? print $i; ?>" id="expanddescription_<? print $i; ?>" type="button" value="+" class="showhidedes"/> <span>Additional Details </span>
									</div>
									<div class="span-8">
										<div id="hiddendes_<? print $i; ?>" style="display:none;">
											<ul>
<?php
												// Loop through the upcharges
												foreach($product->upcharges AS $upcharge) {
													print "<li><span class='bold'>" . $upcharge['type'] . ": </span>".htmlspecialchars($upcharge['name'],ENT_QUOTES,"UTF-8"). "</li>";
												}

												if ($product->designService) {
													print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
												} else {
													print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>";
												}

												if ($product->comments != '') {
													print "<li><span class='bold'>Instructions: </span>".htmlspecialchars($product->comments, ENT_QUOTES, 'UTF-8')."</li>";
												}
?>

											</ul>
										</div>
									</div>
<?php

							} else if($product->type=="builder") { ?>
								<div class="span-8">
									<input name="expanddescription_<? print $i; ?>" id="expanddescription_<? print $i; ?>" type="button" value="+" class="showhidedes"/> <span>Additional Details</span>
								</div>
								<div class="span-8">
									<div id="hiddendes_<? print $i; ?>" style="display:none;">
										<ul>
										<?php
											//Loop through each attribute for product
											foreach($product->settings as $setting) {
												$label="<li><span class='bold'>".htmlspecialchars($setting['builderLabel'],ENT_QUOTES,"UTF-8").": </span>";
												if ($setting['builderSettingDisplay'] == true) {
													if ( $setting['builderSubsetting'] == 'mountingoptions' || $setting['builderSubsetting'] == 'antigraffiti' || $setting['builderSetting'] == 'scheme' || $setting['builderSetting'] == 'layout' || $setting['builderSetting'] == 'text' || $setting['builderSetting'] == 'artwork' || $setting['builderSetting'] == 'upload' ) {
														print $label.htmlspecialchars($setting['builderValueText'],ENT_QUOTES,'UTF-8') . "</li>";
													}

												}
											}

											if ($product->designService) {
												print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
											} else {
												print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>";
											}

											if($product->comments != '') print "<li><span class='bold'>Instructions: </span>". $product->comments."</li>";
										?>
										</ul>
									</div>
								</div>
							<?php
							}else if($product->type=="streetname"){ ?>
								<div class="span-8">
									<input name="expanddescription_<? print $i; ?>" id="expanddescription_<? print $i; ?>" type="button" value="+" class="showhidedes"/> <span>Additional Details </span>
								</div>
								<div class="span-8">
									<div id="hiddendes_<? print $i; ?>" style="display:none;">
										<ul>
										<?php

											// Loop through the upcharges
											foreach($product->upcharges AS $upcharge) {
												print "<li><span class='bold'>" . $upcharge['type'] . ": </span>".htmlspecialchars($upcharge['name'],ENT_QUOTES,"UTF-8"). "</li>";
											}

											//Loop through each attribute
											foreach ($product->getAdditionalDetails() as $key => $att_value) {
												print  "<li><span class='bold'>".$key.": </span>".htmlspecialchars($att_value,ENT_QUOTES,"UTF-8"). "</li>";
											}

											//Custom image
											if (!empty($product->fileUpload['name']))
												print "<li><span class='bold'>Custom Image Uploaded:</span> Yes</li>";

											//Design adjustment
											if ($product->designService){
												print "<li><span class='bold'>Design Adjustment: </span> We will adjust your design for best appearance.</li>";
											} else {
												print "<li><span class='bold'>Design Adjustment: </span> We will print your design as shown.</li>";
											}

											//Comment
											if($product->comments!='') print "<li><span class='bold'>Instructions: </span>".htmlspecialchars($product->comments,ENT_QUOTES,"UTF-8")."</li>";

										?>
										</ul>
									</div>
								</div>
							<?php
							}

							?>
						</dd>
						<dd class="qty-forms-in-shopping-cart"><?php print htmlspecialchars($product->quantity,ENT_QUOTES,"UTF-8");?></dd>
						<dd class="price-per"><?php print "$".htmlspecialchars(number_format($product->unitPrice,2),ENT_QUOTES,"UTF-8");?></dd>
						<dd class="price-total "><?php print "$".htmlspecialchars(number_format($product->totalPrice,2),ENT_QUOTES,"UTF-8");?></dd>
					</dl>
	<?php
					}
	?>
				</div>
			</div>
		</div>
		<div id="edit-cart-button" class="span-12 prepend-12 last margin-right">
			<div id="edit-cart-warning" class="span-7">
				<h4>Warning!</h4>
				<p>Editing your shopping cart will clear any information you have entered on this page. </p>
				<a href="<?php print $cart_url;?>" class="small button blue prepend-2" id="continue-edit-cart">Continue Anyway</a> <a id="cancel-edit-cart" class="button red">Cancel</a> </div>
			<a class="small blue button right-side edit-cart-button" id="edit-shopping-cart-button">Edit Shopping Cart</a>

		</div>
	</div>
<?php

 }

?>


</div>

<?php
	//Check for admin, and output admin options
	if ( $_SESSION['admin'] === TRUE AND $_SESSION['adminID'] == $_SESSION['CID']) {
?>
		<div class="row span-24 append-bottom">
			<div id="admin-wrapper" class="scrolling verification-group admin-notices">
				<p class="h4 ">5. Special Administrative Options</p>
				<div id="adming-comments" for="admin_comments" class="span-14" style=" margin-bottom: 20px; margin-top: 20px; padding-left: 20px;">
					<p><strong>Administrator notes/comments:</strong></p>
					<input type="hidden" name="admin" value="true" />
					<input type="hidden" name="adminAccount" value="<?php echo $_SESSION['adminAccount']; ?>" />
					<textarea id="admin_comments" name="adminComment" class="text" rows="3" cols="6" style="height: 120px; width: 400px;"><?php if($_REQUEST['adminComment']!='') print htmlspecialchars($_REQUEST['adminComment'],ENT_QUOTES,'UTF-8');?></textarea>

				</div>
				<div class="span-8 prepend-top append-bottom half last">
					<div id="admin-referrer">
						<p style="margin-top:20px;"><strong>Referrer</strong></p>
						<select name="referrer" id="referrer" style="width: 275px;">
							<option value="">No referrer</option>
							<?php
								//Get all known referrers
								$referrers = $objUser->getReferrers();
								//Loop through all referrers as part of the select menu
								foreach ($referrers as $referrer=>$value) {
							?>
								<option value="<?php print $value;?>" <?php if($value == $_REQUEST['referrer'] && $_REQUEST['referrer']!='' ) { echo "selected"; } ?> > <?php print $value;?> </option>
							<?php

								}
							?>
						</select>
					</div>
				</div>


			</div>


		</div>

<?php
	}
?>

<div class="row span-24 append-bottom">
	<div id="place-your-order-wrapper" class="scrolling verification-group">
		<p class="h4 h4-rev pad-left-10"><?php echo (($_SESSION['admin'] === TRUE && $_SESSION['adminID'] == $_SESSION['CID']) ? '6' : '5'); ?>. Place Your Order</p>
		<!--[if !IE]> Confirm Amount <![endif]-->
		<div id="special-comments" for="special_comments" class="span-14">
			<p><strong>Do you have any special instructions or comments?</strong></p>
			<textarea id="special_comments" name="special_comments" class="text" rows="3" cols="6"><?php echo htmlspecialchars(isset($prefill['special_comments']) ? $prefill['special_comments'] : NULL, ENT_QUOTES, 'UTF-8'); ?></textarea><br />
			<div style="float: left;"><strong>Note:</strong> By placing an order on SafetySign.com, you agree to our <a href="/templates/help-sec/terms-conditions.php" id="terms-link">terms and conditions.</a></div>
		</div>
		<div id="shopping-cart-subtotal" class="span-9 prepend-top append-bottom half last">
			<div id="invoice-subtotals" class="last">
				<h6 id="shopping-cart-subtotal-text" class="span-4">Subtotal:</h6>
				<p class="span-4">
					<?php
					 $sub_total=$ObjShoppingCart->getSubtotal();
					if($sub_total>0)
					{
						print "$".number_format($sub_total,2);
					}
					?>
				</p>
				<hr />
				<div id="coupon_active">
					<h6 id="coupon" class="span-4">Discount:</h6>
					<p class="span-4"><span id="coupon_rate"></span><span id="coupon_rate_hide">
					<?php  print "-$".isset($coupon_data['coupon_value']) ? number_format($coupon_data['coupon_value'],2) : 0;?></span></p>

					<hr />
				</div>
<?php

$scode  = isset($shipping_array) ? $shipping_array['postcode'] : NULL;
$sstate = isset($shipping_array) ? $shipping_array['state'] : NULL;

if(isset($customer_info_data['tax_exempt']) && !$customer_info_data['tax_exempt'])

	$taxExempt ='N';
else

	$taxExempt ='Y';

$salestax=$ObjShoppingCart->getSalesTax($shipping_array, $taxExempt);
?>
			<h6 id="shipping" class="span-4"><?php if($_SESSION['shipping_services_pre']=='') print "Shipping Charges:"; else print $_SESSION['shipping_carrier_pre']." ".$_SESSION['shipping_services_pre'].':';?></h6>
				<h6 id="shipping_method" ></h6>

				<p class="span-2" > <span id="shipping_rate" ></span> <span id="shipping_rate_hide">
						<?php
					 	  if( isset($services) && $services==0 ) { $service= "$".number_format($services,2); print $service; }
						else { print "$".number_format($_SESSION['shipping_charges_pre'],2); }?></span> </p>
				<hr />
				<span class="conditional-tax"> <a href="#help" id="tax" class="help span-3"> <span>NJ 7% Tax:</span></a>
				<div id="tax-tooltip" class="notice last message tooltip">
					<p>We are required by law to collect sales tax on orders shipping to New Jersey.</p>
				</div>
				<p class="span-4"> <span id="sales_tax_new_hide"> <?php print "$".number_format($salestax,2);?></span> <span id="sales_tax_new"></span></p>
				<hr />
				</span>
				<h6 id="invoice-total" class="span-4 total-price">Invoice Total:</h6>
				<p class="span-4 total-price" id="total-price"><span id="txttotal"></span> <span id="totalamounthide">
				<?php
				$tax=isset($customer_info_data['tax_exempt']) ? $customer_info_data['tax_exempt'] : 'N';
				$order_total = Checkout::calculateTotal($ObjShoppingCart);

				if($order_total>0)
				{
					print "$".number_format($order_total,2);
				}
?>
					</span> </p>
			</div>
			<!-- repo version <div id="place-order-button-wrap"><button type="submit" name="submit" class="large orange button checkout right-side" value="Place Order" id="continue-order" >Place Order</button></div>-->

			<!--[if !IE]> Payment Method <![endif]-->
			<div id="place-order-button-wrap">

<?php
			if ($_REQUEST['layout'] != 'paypal' || $show_paypal == false) {
?>
				<button type="submit" name="submit" class="large orange button checkout right-side" value="Place Order" id="continue-order" >Place Order</button>

<?php
			}

				// Get shipping charges array from the user's session
				$shipping_value = $ObjShippingCharges->getShippingChargesBySession();

				if(!empty($coupon_data['coupon_value'])) {
					$sub_total=$sub_total-$coupon_data['coupon_value'];
				} else {
					$sub_total=$sub_total;
				}
?>

				<input type="hidden" name="shipping_method" value="<?php print $shipping_value['shipping_services_pre'];?>">
				<input type="hidden" name="shipping_rate" value="<?php print $shipping_value['shipping_charges_pre'];?>">
				<input type="hidden" name="shipping_carrier" value="<?php print $shipping_value['shipping_carrier_pre'];?>">
				<input type="hidden" name="coupon_rate" value="<?php print  $coupon_data['coupon_value'];?>">
				<input type="hidden" name="sub_total" value="<?php print $sub_total;?>">
				<input type="hidden" name="layout" value="<?php echo $_REQUEST['layout']; ?>">

<?php
			if ($show_paypal == true) {
?>
				<!-- Paypal Button -->
				<input type='image' class='paypal-place-order' name='submit' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif' border='0' align='top' alt='Check out with PayPal'/>
				<input type='hidden' id='paypal_submit' name='paypal_submit' value='<?php echo ($_REQUEST['layout'] == 'paypal' || $_REQUEST['paypal_submit'] == 1 ? 1 : 0); ?>' />
<?php
			}
?>

			</div>

		</div>
	</div>



</div>
</div>



</form>
