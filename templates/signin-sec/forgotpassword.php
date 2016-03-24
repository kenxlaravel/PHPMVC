
<p class="h3 h3-rev pad-left-10 append-bottom">Forgot your password?</p>

<?php

    $errors = array();
    $success_msg = "";

	//Output errors if we have any
	if (count($errors) > 0) {
		echo "<p class='error'>" . $errors[0] . "</p>";
	}

	//Grab any error messages that have occurred
	$errors = $objUser->getErrors();
	$flags = $objUser->getFlags();

	//If there are any errors
	if (count($errors) == 1) {
		echo "<div class=\"error\"><p>" . $errors[0] . "</p></div>";
	} else if (count($errors) > 1) {
		echo "<div class=\"error\"><p>There were errors in processing your request.</p><ul>";
		foreach ($errors as $error) { echo "<li>" . $error . "</li>"; }
		echo "</ul></div>";
	} else {
		echo "<div class='error hidden'></div>";
	}

	//Check if a password reset confirmation hash exists, is active, and has not expired
	if (!empty($_REQUEST['confirm']) && $objUser->validateHash($_REQUEST['confirm']) > 0) {
?>
<div class="pad-left-10">
		<p>To reset your password, enter and confirm your new password below:</p>
			<div class="span-7">
				<form accept-charset="utf-8" name="password-reset" method="post" action="<?php echo $page->getUrl(); ?>">
					<div class="<?php if ($flags[1] == 1) { echo " error"; }?> append-bottom forgot-password-wrapper ">

						<input type="hidden" name="confirm" value="<?php echo $_REQUEST['confirm']; ?>" />
							<label><span>Password:</span> <input type="password" title="Enter a password. Minimum eight characters long." class="text nospace" name="pass1" required pattern=".{8}.*" /></label>
							<label><span>Re-type Password:</span> <input type="password" title="Re-type the same password." class="text nospace" id="confirm-password" name="pass2" required pattern=".{8}.*" /></label>
					</div>

					<input type="submit" name="newpasswordsubmit" id="password_reset" class="orange button right-side" value="Reset Password"/>
				</form>
			</div>
</div>
<?php
	} else {

		//If there is a confirmation, throw an error that it is invalid
		if (!empty($_GET['confirm'])) {
?>
			<div class="error">This page has expired! If you were attempting to reset your password, please resubmit the form below, check your email, and try again.</div>
<?php
		}

		if (isset($_GET['action']) && $_GET['action'] == 'update') {
?>
			<div class="pad-left-10">An email has been sent to the address provided. Please check your inbox for further instructions.</div>
<?php
		} else {
?>			<div class="pad-left-10">
					<p>Enter your email address and instructions on how to reset your password will be sent to you.</p>

			<form accept-charset="utf-8" name="forgotpassword" class="append-bottom" method="post"
		    action="<?php echo $page->getUrl(); ?>" id="forgotpassword">

					<label for="username">Email Address</label>
				    <input type="text" class="text required" id="username" name="username" value="<?php
					if(!$success_msg) { print htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : NULL,ENT_QUOTES,'UTF-8'); }?>" maxlength="100" />
				    <input type="submit" name="signinsubmit" class="orange button" value="Reset My Password"/>

		    </form>
</div>
<?php
		}

	}
?>

