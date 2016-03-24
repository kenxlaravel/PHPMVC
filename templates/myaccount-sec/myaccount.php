<?php

$last = FALSE;
?>

<div id="column-1" class="column span-24">
	<p class="h3 h3-rev pad-left-10">My Account</p>
	<div class="right-side prepend-top no-js-visible">
		<ul>
			<li class="left-side"><a href="#account">Account Information</a>&nbsp;|&nbsp;</li>
			<li class="left-side"><a href="#address-list">Saved Addresses</a>&nbsp;|&nbsp;</li>
			<li class="left-side"><a href="#edit-addresses">Edit Addresses</a>&nbsp;|&nbsp;</li>
			<li class="left-side"><a href="#net-30">Brimar Net 30</a>&nbsp;|&nbsp;</li>
			<li class="left-side"><a href="#order-history">Order History</a>&nbsp;|&nbsp;</li>
			<li class="left-side"><a href="#saved-custom">Saved Custom Items</a></li>
		</ul>
	</div>

<?php
	//Display any instructions
	if (!empty($_SESSION['instructions'])) {
?>
		<div class='notice notice-icon prepend-top'><span><?php echo $_SESSION['instructions']; ?></span></div>
<?php
	}

	//If there are more than one error, display them as a list
	if (isset($_SESSION['errors']) && count($_SESSION['errors']) > 1) {
?>
		<div class='error notice-icon prepend-top'><p>There were errors in processing your request. Please try again.</p>
			<ul >
<?php
			foreach($_SESSION['errors'] AS $key => $error) {
?>
				<li><?php echo $error; ?></li>
<?php
			}
?>
			</ul>
		</div>
<?php
	//If there is only one error
	} else if (isset($_SESSION['errors']) && count($_SESSION['errors']) == 1) {
?>
		<div class='error clear prepend-top'><span><?php echo $_SESSION['errors'][0]; ?></span></div>
<?php
	}

	//Display any successes
	if (isset($_SESSION['successes']) && count($_SESSION['successes']) > 0) {
		foreach($_SESSION['successes'] AS $key => $success) {
?>
			<div class='success clear prepend-top'><span><?php echo $success; ?></span></div>
<?php
		}
	}
?>


	<div class="myaccount-section container clear" id="account">
		<div class="edit-info">
			<p class="font-16 left-side">You are currently signed in as <span class="bold"><?php print htmlspecialchars($customerData,ENT_QUOTES,'UTF-8');?></span></p>
			<p class="left-side prepend-top-5 first-margin">
				<span><a href="#edit-email" id="edit_email_link" class="underline">Update Email</a></span> |
				<span><a href="#edit-password" id="edit_email_password_link" class="underline">Update Password</a></span>
			</p>

			<div id="edit-email" class="form-wrapper no-js-visible">
				<form accept-charset="utf-8" id="edit-email-data" method="post" action="<?php echo URL_PREFIX_HTTPS; ?>/process/account.php">
					<div>
						<label for="new-email">New Email Address: </label>
						<input type="text" id="new-email" name="createusername" value="<?php echo isset($_SESSION['validate']['createusername']) ? $_SESSION['validate']['createusername'] : NULL; ?>" tabindex="1">
					</div>
					<div>
						<label for="confirm-username">Confirm Email: </label>
						<input type="text" id="confirm-username" name="confirmusername" value="<?php echo isset($_SESSION['validate']['confirmusername']) ? $_SESSION['validate']['confirmusername'] : NULL; ?>" tabindex="2">
					</div>
					<div class="edit-buttons">
						<button type="button" name="cancel_new_email" value="Cancel" class="button cancel" tabindex="4">Cancel</button>
						<button type="submit" name="change_email" value="Update Email Address" class="blue button" tabindex="3">Update Email Address</button>
					</div>
				</form>
			</div>
			<div id="edit-password" class="form-wrapper no-js-visible">
				<form accept-charset="utf-8" id="edit-password-data" method="post" action="<?php echo URL_PREFIX_HTTPS; ?>/process/account.php">
					<div>
						<label for="old-password">Confirm Old Password: </label>
						<input type="password" class="text" id="old-password" name="oldpassword" value="" tabindex="5" autocomplete="off">
					</div>
					<div>
						<label for="create-password">New Password: </label>
						<input type="password" class="text" id="create-password" name="createpassword" value="" tabindex="6">
					</div>
					<div>
						<label for="confirm-password">Confirm Password: </label>
						<input type="password" class="text" id="confirm-password" name="confirmpassword" value="" tabindex="7">
					</div>
					<div class="edit-buttons">
						<button type="button" name="cancel_change_password" value="Cancel" class="button cancel" tabindex="9">Cancel</button>
						<button type="submit" name="change_password" value="Update Password" class="blue button" tabindex="8">Update Password</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<!-- Shipping Address -->

	<div class="myaccount-section container" id="address-list">
		<p class="h4 left-side append-bottom">Saved Addresses (Billing &amp; Shipping)</p>
		<div class="addressbook-errors"></div>
<?php
		//Grab an array of saved addresses for the current user
		$address_array = $ObjAddresses->listAddresses();

		if (!empty($address_array)) {
			$i = 0;
			$total = count($address_array);
?>
			<form accept-charset='utf-8' method='post' action='<?php echo URL_PREFIX_HTTPS; ?>/process/account.php' id="edit-shipping-form">
<?php
			//Loop through and display the addresses
			foreach($address_array as $key => $address) {
				if ($i%3 == 0 AND $i != 0) {
					$last = true;
				}
				if ($i%4 == 0 AND $i != 0) {
?>
				</div>
				<div class="row clear">
<?php
				}

				if ($i == 0) {
?>
				<div class="row clear">
<?php
				}

?>					<!-- Need to indicate which is the current default shipping and billing addresses -->
					<div class="ma-address append-bottom <?php if ($last == true) { echo 'last'; } if ($address['default_shipping'] == 1) { ?> address-defaultshipping<?php } if ($address['default_billing'] == 1){ ?> address-defaultbilling<?php } ?>" data-address-id="<?php echo $address['public_id']; ?>">
						<div>
							<address class="address-info">
								<p class="shipcompany"><?php echo isset($address['company']) ? $address['company'] : NULL; ?></p>
								<p><span class="shipfirstname name"><?php echo isset($address['first_name']) ? $address['first_name'] : NULL; ?></span> <span class="shiplastname name"><?php echo $address['last_name']; ?></span></p>
								<p class="shipaddress1"><?php echo isset($address['street_address']) ? $address['street_address'] : NULL; ?></p>
								<p class="shipaddress2"><?php echo isset($address['suburb']) ? $address['suburb'] : NULL; ?></p>
								<p><span class="shipcity"><?php echo isset($address['city']) ? $address['city'] : NULL; ?></span><?php echo (!empty($address['state']) ? ', ' : ''); ?><span class="shipstate"> <?php echo $address['state']; ?></span> <span class="shipzip"><?php echo $address['postcode']; ?></span></p>
								<p class="shipcountry"><?php
									$address_country=$objCountry->CountryCodeList($address['country']);
									if(!empty($address_country)) { echo $address_country['countries_name'] ; }
								 ?></p>

								<p class="shipzip"><?php echo isset($address['phone']) ? $address['phone'] : NULL; ?></p>
								<p class="shipzip"><?php echo isset($address['fax']) ? $address['fax'] : NULL; ?></p>
							</address>

							<input type='hidden' name='address_id' value='<?php echo isset($address['public_id']) ? $address['public_id'] : NULL; ?>' />
							<input type='hidden' name='quick-change' value='1' />

							<!-- TO DO: check the change for this section on new version -->

							<p class="clear prepend-top"><a class="update-address-button" href="#update-address-<?php echo isset($address['public_id']) ? $address['public_id'] : NULL; ?>">Update</a> | <a href="process/account.php?delete-address=<?php echo isset($address['public_id']) ? $address['public_id'] : NULL; ?>" class="delete-address-button">Delete</a></p>
							<div class="prepend-top">
								<label><input type="radio" name="default_shipping" value="<?php echo isset($address['public_id']) ? $address['public_id'] : NULL; ?>" class="default-shipping" <?php echo ($address['default_shipping'] == 1 ? ' checked' : ''); ?>/> Default Shipping</label>
								<label><input type="radio" name="default_billing" value="<?php echo isset($address['public_id']) ? $address['public_id'] : NULL; ?>" class="default-billing" <?php echo ($address['default_billing'] == 1 ? ' checked' : ''); ?>/> Default Billing</label>
							</div>
						</div>
					</div>
<?php
				$i++;
				$last = false;
				if ($i == $total) {
?>
					<div id="add-address-block" class="ma-new-address append-bottom last no-js-hidden">
						<p><a href="#add-address-form" class="addressbook-add-address">+ Add A New Address</a></p>
					</div>
				</div>
<?php
				}
			}
?>

				<div class="clear no-js-visible">
						<button id="default-update" type="submit" class="button blue">Confirm New Default Addresses</button>
				</div>
			</form>

<?php
		} else {
?>
			<p class="clear">You have no saved addresses. <a href="#add-address-form" class="underline addressbook-add-address">+ Add A New Address</a></p>
<?php
		}
?>
	</div>

	<div class="myaccount-section container no-js-visible" id="add-address">

		<div class="form-wrapper" id="add-address-form">
<p class="h4 append-bottom left-side">Add A New Address</p><p class="right-side required-text">*required</p>
			<form accept-charset="utf-8" class="clear" id="edit-login-data" method="post" action="<?php echo URL_PREFIX_HTTPS; ?>/process/account.php">

				<div>
					<label>Company</label>
					<input type="text" name="company" value="<?php echo isset($_SESSION['validate']['company']) ? $_SESSION['validate']['company'] : NULL; ?>">
				</div>
				<div>
					<label>First Name <span class="required-text">*</span></label>
					<input type="text" name="first" value="<?php echo isset($_SESSION['validate']['first']) ? $_SESSION['validate']['first'] : NULL; ?>">
				</div>
				<div>
					<label>Last Name <span class="required-text">*</span></label>
					<input type="text" name="last" value="<?php echo isset($_SESSION['validate']['last']) ? $_SESSION['validate']['last'] : NULL; ?>">
				</div>
				<div>
					<label>Address Line 1 <span class="required-text">*</span></label>
					<input type="text" name="address1" value="<?php echo isset($_SESSION['validate']['address1']) ? $_SESSION['validate']['address1'] : NULL; ?>">
				</div>
				<div>
					<label>Address Line 2</label>
					<input type="text" name="address2" value="<?php echo isset($_SESSION['validate']['address2']) ? $_SESSION['validate']['address2'] : NULL; ?>">
				</div>
				<div>
					<label>City <span class="required-text">*</span></label>
					<input type="text" name="city" value="<?php echo isset($_SESSION['validate']['city']) ? $_SESSION['validate']['city'] : NULL; ?>">
				</div>
				<div class="address-state-wrap">
					<label>State/Province</label>
					<select name="state" value="">

						<option value="" <?php echo (isset($_SESSION['validate']['state']) && $_SESSION['validate']['state'] == "" || empty($_SESSION['validate']['state']) ? 'selected' : ''); ?>></option>
<?php
						//Grab a list of available countries
						$zones = $ObjAddresses->listZones();

						foreach($zones AS $zone) {
?>
							<option value="<?php echo $zone['zone_code']; ?>" <?php echo (isset($_SESSION['validate']['state']) && $_SESSION['validate']['state'] == $zone['zone_code'] ? 'selected' : ''); ?>><?php echo $zone['zone_name']; ?></option>
<?php
						}
?>
					</select>
				</div>
				<div>
					<label>Zip/Postal Code <span class="required-text">*</span></label>
					<input type="text" name="zip" value="<?php echo isset($_SESSION['validate']) ? $_SESSION['validate']['zip'] : NULL; ?>">
				</div>
				<div>
					<label>Country <span class="required-text">*</span></label>
					<select name="country" class="address-country-select" value="">
<?php
						//Grab a list of available countries
						$countries = $ObjAddresses->listCountries();

						//Use previous formdata to select country, and if not default to US
						if (!empty($_SESSION['validate']['country'])) {
							$prefill = $_SESSION['validate']['country'];
						} else {
							$prefill = 'US';
						}

						foreach($countries AS $country) {
?>

							<option value="<?php echo $country['countries_iso_code_2']; ?>"<?php echo ($country['countries_iso_code_2'] == $prefill ? ' selected' : ''); ?> data-state-required="<?php echo $country['zone']; ?>"><?php echo $country['countries_name']; ?></option>
<?php
						}
?>
					</select>
				</div>
				<div>
					<label>Phone <span class="required-text">*</span></label>
					<input type="text" name="phone" value="<?php echo isset($_SESSION['validate']['phone']) ? $_SESSION['validate']['phone'] : NULL; ?>">
				</div>

				<div>
					<label>Fax</label>
					<input type="text" name="fax" value="<?php echo isset($_SESSION['validate']['fax']) ? $_SESSION['validate']['fax'] : NULL; ?>">
				</div>

				<div class="checkbox-wrapper">
					<label class="checkbox"><input type="checkbox" name="default_shipping" value="1"<?php echo (isset($address['default_shipping']) && $address['default_shipping'] == 1 || isset($_SESSION['validate']['default_shipping']) && $_SESSION['validate']['default_shipping'] == 1 ? ' checked' : ''); ?>> Default Shipping</label>
					<label class="checkbox"><input type="checkbox" name="default_billing" value="1"<?php echo (isset($address['default_billing']) && $address['default_billing'] == 1 || isset($_SESSION['validate']['default_billing']) && $_SESSION['validate']['default_billing'] == 1 ? ' checked' : ''); ?>> Default Billing</label>
				</div>

				<input type="hidden" name="address" value="modify" />
				<div class="edit-buttons">
					<button type="button" name="cancel_change_password" value="Cancel" class="button cancel" tabindex="-1">Cancel</button>
					<button type="submit" name="modify_address" value="Add to addresses" class="blue button">Add New Address</button>
				</div>
			</form>
		</div>
	</div>

	<div class="myaccount-section container no-js-visible" id="edit-addresses">
		<p class="h4 clear append-bottom no-js-visible">Edit Address</p>
<?php
		if (!empty($address_array)) {

			//Loop through and display the addresses
			foreach($address_array as $key => $address) {
?>
			<!-- TO DO: address number needs to be iterated -->
			<div id="update-address-<?php echo $address['public_id']; ?>" class="form-wrapper" data-address-id="<?php echo isset($address['public_id']) ? $address['public_id'] : NULL; ?>">
<p class="h4 append-bottom left-side">Edit Address</p><p class="right-side required-text">*required</p>				<form accept-charset="utf-8" method="post" class="clear" action="<?php echo URL_PREFIX_HTTPS; ?>/process/account.php">
					<div>
						<label>Company</label>
						<input type="text" name="company" value="<?php echo $address['company']; ?>">
					</div>
					<div>
						<label>First Name <span class="required-text">*</span></label>
						<input type="text" name="first" value="<?php echo $address['first_name']; ?>">
					</div>
					<div>
						<label>Last Name <span class="required-text">*</span></label>
						<input type="text" name="last" value="<?php echo $address['last_name']; ?>">
					</div>
					<div>
						<label>Address Line 1 <span class="required-text">*</span></label>
						<input type="text" name="address1" value="<?php echo $address['street_address']; ?>">
					</div>
					<div>
						<label>Address Line 2</label>
						<input type="text" name="address2" value="<?php echo $address['suburb']; ?>">
					</div>
					<div>
						<label>City <span class="required-text">*</span></label>
						<input type="text" name="city" value="<?php echo $address['city']; ?>">
					</div>
					<div class="address-state-wrap">
						<label>State/Province</label>
						<select name="state" value="">

						<option value="" selected></option>
<?php
						//Grab a list of available countries
						$zones = $ObjAddresses->listZones();

						foreach($zones AS $zone) {
?>
							<option value="<?php echo $zone['zone_code']; ?>"<?php echo ($address['state'] == $zone['zone_code'] ? ' selected' : ''); ?>><?php echo $zone['zone_name']; ?></option>
<?php
						}
?>
						</select>
					</div>
					<div>
						<label>Zip/Postal Code <span class="required-text">*</span></label>
						<input type="text" name="zip" value="<?php echo $address['postcode']; ?>">
					</div>
					<div>
						<label>Country <span class="required-text">*</span></label>
						<select name="country" class="address-country-select" value="">
<?php
							//Grab a list of available countries
							$countries = $ObjAddresses->listCountries();

							//Use previous formdata to select country, and if not default to US
							if (!empty($address['country'])) {
								$prefill = $address['country'];
							} else {
								$prefill = 'US';
							}

							foreach($countries AS $country) {
?>
								<option value="<?php echo $country['countries_iso_code_2']; ?>"<?php echo ($country['countries_iso_code_2'] == $prefill ? ' selected' : ''); ?> data-state-required="<?php echo $country['zone']; ?>"><?php echo $country['countries_name']; ?></option>
<?php
							}
?>
						</select>
					</div>
					<div>
						<label>Phone <span class="required-text">*</span></label>
						<input type="text" name="phone" value="<?php echo $address['phone']; ?>">
					</div>
					<div>
						<label>Fax</label>
						<input type="text" name="fax" value="<?php echo $address['fax']; ?>">
					</div>
					<div class="checkbox-wrapper">
						<label class="checkbox"><input type="checkbox" name="default_shipping" class="default-shipping" value="1"<?php echo ($address['default_shipping'] == 1 ? ' checked' : ''); ?>> Default Shipping</label>
						<label class="checkbox"><input type="checkbox" name="default_billing" class="default-billing" value="1"<?php echo ($address['default_billing'] == 1 ? ' checked' : ''); ?>> Default Billing</label>
					</div>

					<input type="hidden" name="address_id" value="<?php echo $address['public_id']; ?>" />
					<input type="hidden" name="address" value="modify" />

					<div class="edit-buttons">
						<button type="button" name="cancel_change_password" value="Cancel" class="button cancel" tabindex="-1">Cancel</button>
						<button type="submit" name="modify_address" value="Add to addresses" class="blue button">Update Address</button>
					</div>
				</form>
			</div>
<?php
			}
		}
?>
	</div>


	<div class="myaccount-section container" id="net-30">
		<p class="h4 left-side append-bottom">Brimar Net 30 Accounts</p>

<?php
					$net30 = $objUser->getLinkedNet30(isset($_SESSION['CID']) ? $_SESSION['CID'] : NULL);
					if (!empty($net30)) {
?>
		<div class="form-wrapper clear">
			<p class="clear">You are currently linked to Net30 account: <span class="bold"><?php echo base64_decode($net30['account_no']); ?></span></p>
					<form accept-charset="utf-8" method="post" action="<?php echo URL_PREFIX_HTTPS; ?>/process/account.php">
						<button type="submit" name="unlink_net30" value="Unlink Net30" class="blue button">Unlink This Account</button>
					</form>
		</div>
<?php
					} else {
?>
						<p class="left-side prepend-top-5 first-margin"><a href="#edit-netthirty" id="edit_netthirty" class="underline">+ Link A Net 30 Account</a></p>
						<p class="clear">You do not currently have a Brimar Net 30 account linked to this email address.</p>
<?php
					}
?>
		<div id="edit-netthirty" class="form-wrapper no-js-visible">
			<form accept-charset="utf-8" method="post" action="<?php echo URL_PREFIX_HTTPS; ?>/process/account.php">
				<div>
					<label for="account-number">Account Number</label>
					<input type="text" id="account-number" name="account" value="" tabindex="10">
				</div>
				<div>
					<label for="code">Security Code</label>
					<input type="text" id="code" name="code" value="" tabindex="11">
				</div>
				<div class="edit-buttons">
					<button type="button" name="cancel_link_net30" value="Cancel" class="button cancel" tabindex="13">Cancel</button>
					<button type="submit" name="link_net30" value="Add Account" class="blue button" tabindex="12"><?php if (!empty($net30)) { ?>Update Account<?php } else { ?>Add Brimar Net30<?php } ?></button>
				</div>
			</form>
		</div>
	</div>


<?php if($saved_carts > 0){
	include_once($Path_Templates_MyAccount.'savedcarts.php');
}else{
	$link = new Page('savedcarts');
	?>
	<div class="myaccount-section container save-cart-wrapper" id="saved-carts">
		<p class="h4 append-bottom">Saved Shopping Carts</p>
		<?
		if ($ObjShoppingCart instanceof Cart) {
			$totalQuantity = (int) $ObjShoppingCart->getTotalQuantity();
		} else {
			$totalQuantity = 0;
		}

		if ($totalQuantity > 0){ ?>
		<p>You have items in your cart. Would you like to save it?</p>
		<p><a href="" class="button blue save-cart">Save Shopping Cart</a></p>
		<? }else{ ?>
		<p>Add items to your cart and then visit the Shopping Cart to save carts.</p>
		<?php } ?>
		<form method="POST" action="/save-cart" class="save-cart-dialog">
			<p class="h3">Save Cart</p>
			<p>Give your cart a name and click the Save button below to save it to your account.</p>
			<input type="text" class="text cart-name" name="keywords" placeholder="Enter Cart Name" value="" size="100">
			<div class="prepend-top note-wrapper">
				<span>Notes</span> <span class="note-text">(optional)</span>
				<textarea type="text" class="clear save-notes" name="keywords" value="" size="255"></textarea>
			</div>
			<div class="save-cart-controls prepend-top">
				<p class="left-side show-notes"><a href="#" class="underline">Add a note</a> <span class="note-text">(optional)</span></p>

				<div class="right-side">

					<span class="button save-cancel">Cancel</span> <button type="submit" class="button green save-cart" >Save Cart</button>
				</div>
			</div>
		</form>
	</div>
	<?php	}

	if ($order_total > 0) {

		include_once($Path_Templates_MyAccount.'orderhistory.php');
	}else{ ?>

	<div class="myaccount-section container" id="order-history">
		<p class="h4 append-bottom">Order History</p>
		<p>You have no previous orders. Once you have placed an order, you can view the details here.</p>
	</div>
	<?php }

	$customproduct=$ObjCustomProduct->GetCustomProductList($CID);
	$count_custom_item=count($customproduct);
	$i = 1;

	if ($count_custom_item > 0) {
?>
	<!-- Saved Custom Signs -->
	<div class="myaccount-section container" id="saved-custom">
		<p class="h4 append-bottom clear">Saved Custom Items</p>
		<div class="saved-design-viewer span-23 text-center no-js-hidden">
<?php
				//Loop through all saved designs
				foreach($customproduct as $key => $value) {

					//If this is divisible by 18 and is NOT the first product, close the last group of 18 and open a new one
					if ($i%19 == 0 AND $i != 1) {
						echo "</ul>";
						echo "<ul class='items'>";
					//If this is the first product, start a group of 18
					} else if ($i == 1) {
						echo "<ul class='items'>";
					}

					$pid = (int)$value['product_id'];
                    $cpi = (int)$value['custom_product_id'];
?>
					<li class="sign-container">
						<div class="thumbnail append-bottom">
							<img src="<?= CUSTOM_IMAGE_URL_PREFIX.'/design/save/previews/small/'.$value['custom_image'];?>">
						</div>
						<form accept-charset="utf-8" name="frmcustomitem<?= $key;?>" method="post" action="<?php echo URL_PREFIX_HTTPS; ?>/process/account.php">
							<a href="/save-design?pid=<?= urlencode($pid);?>&cpi=<?= $cpi;?>" class="blue button order-btn">View/Order</a>
							<input type="hidden" name="custom_product_id" value="<?= $cpi;?>">
							<input type="hidden" name="custom_product" value="Delete Design">
						</form>
						<p class="prepend-top-5"><a href="#delete" onclick="document.frmcustomitem<?= $key;?>.submit();" class="custom-sign-delete">
							x Delete
						</a></p>
					</li>
<?php
					//Close out the last 18 section
					if ($i == $count_custom_item) {
						echo "</ul>";
					}

					//Increment the counter
					$i++;

				} //end foreach loop
?>
		</div>

	</div>
<?php
	}
?>
</div>

<?php
	//Unset any messages/form validation so the user only sees them once
	unset($_SESSION['successes']);
	unset($_SESSION['errors']);
	unset($_SESSION['validate']);
	unset($_SESSION['instructions']);
?>
