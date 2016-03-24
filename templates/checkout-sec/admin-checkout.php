<?php
	session_start();

	$ref_url = $_SERVER['REQUEST_URI'];
	$parsed_ref_url = parse_url($ref_url);
	$query = $parsed_ref_url['query'];

	if (!empty($query)) {
		$query = '?' . $query;
	}


	if ($objUser->checkAdmin()) {
?>

<style type="text/css">
	.admin-checkout {
		padding: 40px 100px;
		margin: 0 auto;
		width: 450px;
	}

	.admin-checkout label {
		display: inline !important;
		padding: 0 .5em 0 0;
		width:360px;
		float: none;
	}

	.admin-checkout input { margin:0; }

	.admin-checkout .instructions {
		font-size: 20px;
		line-height: 28px;
		text-align: center;
	}

	.admin-checkout .submit { text-align: right; }

	.admin-checkout fieldset { display: block; }

	.admin-checkout .outer { width: 450px; }

	.admin-checkout .inner { float: center; }

	.admin-checkout fieldset div input[type="text"] {
		width:200px;
		margin-bottom:5px;
	}

	.admin-checkout #admincheckout-new-create {
		margin-left:150px;
		padding-top:10px;
	}

	.admin-checkout .submit{
		float:right;
		width:85px;
	}

	.no-borderradius .outer div { float:none; }
</style>

<div class="admin-checkout">
	<?php
		// Listing of possible error flags:
				// $errors[0] = No customer email entered
				// $errors[1] = No new email entered
				// $errors[2] =
				// $errors[3] = Nothing selected
				// $errors[4] = No customer exists with those credentials
				// $errors[5] = The guest account could not be created
				// $errors[6] = That customer account already exists
	?>

	<p class="instructions">You are signed in as an administrator. <br />
		How would you like to check out?</p>
	<br />

	<?php if ($_SESSION['errors']['admin_error'][3] === true) { echo "<div class='error'>Nothing selected. Please choose from the options below:</div>"; } ?>
	<?php if ($_SESSION['errors']['admin_error'][4] === true) { echo "<div class='error'>No customer with that email address exists.</div>"; } ?>
	<?php if ($_SESSION['errors']['admin_error'][5] === true) { echo "<div class='error'>An error occurred creating the guest account.<br />The email may already be in use.<br /> Please try a different email address</div>"; } ?>
	<?php if ($_SESSION['errors']['admin_error'][6] === true) { echo "<div class='error'>That customer account already exists</div>"; } ?>
	<form accept-charset="utf-8" method="post" action="/process/admin_checkout.php<?php echo $query; ?>">
		<input type="hidden" name="admincheckouttype" value="customer" />
		<fieldset class="customer outer">
			<legend>Check out using an existing customer account.</legend>
			<div>
				<label for="admincheckout-customer-email">Customer Email Address</label>
				<input type="text" name="admincheckout-customer-email" id="admincheckout-customer-email" <?php if($_SESSION['errors']['admin_error'][0] === true OR $_SESSION['errors']['admin_error'][4] === true){ echo "style='border: 1px solid #DA0000;'"; }?>/>
			</div>
			<input type="hidden" name="submit" value="1" />
			<div class="submit">
				<input type="submit" class="button orange" name="submit" value="Check Out" />
			</div>
		</fieldset>
	</form>
	<form accept-charset="utf-8" method="post" action="/process/admin_checkout.php<?php echo $query; ?>">
		<input type="hidden" name="admincheckouttype" value="new" />
		<fieldset class="new outer">
			<legend>Check out as a new customer.</legend>

			<div>
				<div>
					<label for="admincheckout-new-email">Customer Email Address</label>
					<input type="text" name="admincheckout-new-email" id="admincheckout-new-email" <?php if($_SESSION['errors']['admin_error'][1] === true OR $_SESSION['errors']['admin_error'][5] === true){ echo "style='border: 1px solid #DA0000;'"; }?>/>
				</div>
				<div>
					<input type="checkbox" name="admincheckout-new-create" id="admincheckout-new-create" />
					<label for="admincheckout-new-create">Create an account for this user.</label>
				</div>
			</div>
			<input type="hidden" name="submit" value="1" />
			<div class="submit">
				<input type="submit" class="button orange" name="submit" value="Check Out" />
			</div>
		</fieldset>
	</form>
	<form accept-charset="utf-8" method="post" action="/process/admin_checkout.php<?php echo $query; ?>">
		<input type="hidden" name="admincheckouttype" value="regular" />
		<fieldset class="regular outer">
			<legend>Check out with my account.</legend>
			<div>
				<label for="admincheckouttype-regular">Check out regularly using my administrator account.</label>
			</div>
			<input type="hidden" name="submit" value="1" />
			<div class="submit">
				<input type="submit" class="button orange" name="submit" value="Check Out" />
			</div>
		</fieldset>
	</form>

</div>

<?php
	} else {
		$checkout = Page::create('checkout');
		header("Location:".$checkout->getUrl().$query);
	}
?>