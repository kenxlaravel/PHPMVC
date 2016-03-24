<?php
//Grab any error messages that have occurred
$errors = $objUser->getErrors();
$flags  = $objUser->getFlags();


//If the user is currently signed in, we will notify them and give them the option to sign out
if( isset($_SESSION['CID']) && $_SESSION['CID'] > 0 && $objUser->getCustomerTypeById($_SESSION['CID']) == 'U') {
?>
	<p class="h3 h3-rev pad-left-10 append-bottom">Sign Out</p>

	<div class="pad-left-10 append-bottom">
		<p class="h4">You are already signed in!</p>
		<p>Finished shopping? Want to sign in with a different email address?</p>
		<a href="<?php echo URL_PREFIX_HTTPS; ?>/sign-out" class="button orange">Sign Out</a>
	</div>
<?php
//The user is not signed in, show them the default template
} else {

	//Clear out the CID if there was one from a guest checkout
	$ObjSession->unsetSession();
?>

	<div class="account-form">

<?php

//If there are any errors
if( count($errors) == 1 ) {

    echo "<div class=\"error notice-icon\"><p>".$errors[0]."</p></div>";

}else if( count($errors) > 1 ) {

    echo "<div class=\"error notice-icon\"><p class=\"bold\">There were errors while processing your request. Please try again or <a href=''>contact Customer Service</a> for assistance.</p><ul>";

    foreach ($errors as $error) {

        echo "<li>".$error."</li>";
    }

    echo "</ul></div>";

}else{

    echo "<div class='error notice-icon hidden'></div>";
}

?>

	<div id="signin-group" class="append-bottom span-24 signin-page <?php if (isset($_REQUEST['checkout'])) { ?>signin-checkout<?php } ?>">
	<p class="h3 h3-rev pad-left-15"><i class="sprite sprite-lock-large-white"></i>Secure Sign In</p>


		<?php if (HTML_COMMENTS) {?><!-- NEW --><?php } ?>
		<div id="new-users-signup">
			<p class="h4">New Customers</p>
			<div class="form-container">
				<form accept-charset="utf-8" name="register" id="new-user-guest-checkout" class="signin-form contacting" method="post"  action="<?php echo $page->getUrl(); echo (isset($_REQUEST['checkout']) ? '?checkout' : ''); ?>">
			        <input type="hidden" name="formaction" value="new" />
					<div class="prepend-top half<?php if ($flags[1] == 1) { echo " error"; }?> control-wrap">
						<label><span>Email Address </span>
						<input type="email" class="text nospace" id="email" name="email" title="Enter your email address." value="<?php if( isset($_POST['email']) && $_POST['formaction'] == 'new') { echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); }?>" maxlength="100"></label>
					</div>

					<div class="control-wrap<?php if ($flags[2] == 1) { echo " error"; }?>">
						<div>
							<label><span>Password</span>
							<input type="password" title="Enter a password. Minimum eight characters long." class="text nospace" id="create-password"  name="pass1" required pattern=".{8}.*" /></label>
						</div>

						<div>
							<label><span>Re-type Password</span>
							<input type="password" title="Re-type the same password." class="text nospace" id="confirm-password" name="pass2" required pattern=".{8}.*" /></label>
						</div>
					</div>

					<div class="right-side prepend-top append-bottom">
<?php
		//If they came from the cart, the button will say checkout
		if (isset($_REQUEST['checkout'])) {
?>
						<input type="submit" name="signinsubmit" id="new_user_register" class="orange button right-side" value="Register &amp; Checkout"/>
<?php
		} else {
?>
						<input type="submit" name="signinsubmit" id="new_user_register" class="orange button right-side" value="Register &amp; Continue Shopping"/>
<?php
		}
?>

					</div>
				</form>
			</div>
			<div class="benefits">
				<p>Register to take advantage of our <strong>convenient features</strong> which include the ability to:</p>
				<ul class="no-margin-bottom">
					<li>Save shopping carts for later</li>
					<li>Email or print a saved shopping cart</li>
					<li>Use our Quick Checkout</li>
					<li>Track your order</li>
					<li>Save your custom designs</li>
					<li>Reorder previously purchased items</li>
					<li>View your order history</li>
				</ul>
			</div>

		</div>

				<?php if (HTML_COMMENTS) {?><!-- RETURNING --><?php } ?>

		<div id="returning-users-signin">
			<p class="h4">Returning Customers</p>
			<div class="form-container">

				<form accept-charset="utf-8" name="returningsignins" method="post" id="returning-user-signins" class="signin-form contacting" action="<?php echo $page->getUrl(); echo (isset($_REQUEST['checkout']) ? '?checkout' : '');?>">
		            <input type="hidden" name="formaction" value="returning" />

					<div class="prepend-top half <?php if( isset($flags[4]) && $flags[4] == 1) { echo "error"; }?> control-wrap">
						<label><span>Email Address</span>
						<input type="email" class="text nospace" id="username" name="email" required value="<?php if( isset($_POST['email']) && $_POST['formaction'] == 'returning') { echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); } else if (isset($username) && $username != '') { echo $username; }?>" maxlength="100" title="Enter your email address." /></label>
					</div>

					<div class="<?php if( isset($flags[5]) && $flags[5] == 1) { echo "error"; }?> control-wrap">
						<label><span>Password</span>
						<input type="password" class="text nospace" id="returning-user-password" name="pass1" value="<?php if(isset($_COOKIE["Pwd"])){ echo htmlspecialchars(base64_decode($_COOKIE["Pwd"]),ENT_QUOTES,'UTF-8'); }?>" title="Enter your password." /></label>
					</div>

					<div id="remember-password">
						<label class="special-note"><input name="remember" type="checkbox" id="remember" <?php if ((isset($_COOKIE['credentials']) && $_COOKIE['credentials'] != '') || $_POST['remember'] == 'Y') { echo 'checked'; } ?> value="Y" />
						Remember my email address.</label>
					</div>

					<div class="right-side half append-bottom">
<?php
		//If they came from the cart, the button will say checkout
		if (isset($_REQUEST['checkout'])) {
?>
						<input type="submit" name="signinsubmit" class="orange button sign-in-button right-side" value="Sign In &amp; Checkout"/>
<?php
		} else {
?>
						<input type="submit" name="signinsubmit" class="orange button sign-in-button right-side" value="Sign In &amp; Continue Shopping"/>
<?php
		}
?>
						<a href="<?php echo $forgot_password_page->getUrl();?>" id="forgot-password">Forgot Password?</a>
					</div>
				</form>

				<div class="benefits">
					<p><span>Please sign in before continuing for access to <strong>convenient features</strong> and <strong>quick checkout</strong>.</span> </p>
				</div>
			</div>
		</div>


<?php
		//If they came from the cart and the guest flag is set to 1, we will allow guest checkout
		if (isset($_REQUEST['checkout'])) {
?>

				<?php if (HTML_COMMENTS) {?><!-- GUEST --><?php } ?>

		<div id="guest-users-signin">
			<p class="h4">Guest Checkout</p>
			<div class="form-container">
				<form accept-charset="utf-8" name="register" id="guest-checkout" class="signin-form contacting" method="post"  action="<?php echo $page->getUrl(); echo (isset($_REQUEST['checkout']) ? '?checkout' : '');?>">
			        <input type="hidden" name="formaction" value="guest" />
					<div class="prepend-top half <?php if ($flags[6] == 1) { echo "error"; }?> control-wrap">
						<label><span>Email Address</span>
						<input type="email" class="text nospace" id="email" name="email" title="Enter your email address." value="<?php if ($_POST['email'] && $_POST['formaction'] == 'guest') { echo htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8'); }?>" maxlength="100"></label>
					</div>

					<div class="right-side prepend-top append-bottom">
						<input type="submit" name="signinsubmit" id="guest_user_unregister" class="orange button right-side" value="Checkout Without Registering"/>
					</div>
				</form>

				<div class="benefits">
					<p><span>Your email is required in order to send your order confirmation and receipt. Your information will be kept confidential.</span></p>
					<p >If you wish to take advantage of our <strong>convenient features</strong>, please <strong>Register</strong> as a New Customer or <strong>Sign In</strong> with your email &amp; password. </p>
				</div>
			</div>
		</div>

<?php
		}
?>

	</div>
</div>

<div id="security-blurb" class="clear top-space-10 first-margin">
	<div id="security-blurb-text" class="span-14 bottom-space-10">
		<p id="security-blurb-header" class="h4">Shop With Confidence</p>
		<p >Safetysign.com’s top priority is your security. We use the strongest security measures available to protect you and your personal information.<br /></p>
	</div>
	<div class="left-side prepend-top last-margin">
						<a id="bbb-checkout" title="Click for the Business Review of Brimar Industries, Inc, a Safety Equipment &amp; Clothing in Garfield NJ" href="http://www.bbb.org/new-jersey/business-reviews/safety-equipment-and-clothing/brimar-industries-inc-in-garfield-nj-90048124#sealclick"><img alt="Click for the BBB Business Review of this Safety Equipment &amp; Clothing in Garfield NJ" style="border: 0;" src="/new_images/bbbsealh1US.png" /></a>
					</div>
			<div class="left-side prepend-top">
						<a href="https://www.mcafeesecure.com/RatingVerify?ref=www.safetysign.com" target="_blank" id="mcafee-checkout"><img style="border:0;width:115px;height:32px" oncontextmenu="alert('Copying Prohibited by Law - McAfee Secure is a Trademark of McAfee, Inc.');" alt="McAfee Secure" src="//images.scanalert.com/meter/www.safetysign.com/22.gif"></a>
					</div>
</div>

<?php
	}
?>
