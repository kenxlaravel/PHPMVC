<?php
	//This file is designed to be loaded on every page below the header but above the content as a universal way to
	//display errors
?>

<noscript> <div class="browser-script-errors"><p class="javascript-disabled error">JavaScript is not enabled. To use SafetySign.com, please <a class="bold" href="http://enable-javascript.com/" target="_blank" >enable JavaScript</a>.</p></div> </noscript>

<!--[if lt IE 7]> <div class="browser-script-errors"><p class="outdated-browser error">You are using an outdated browser. To improve your experience on SafetySign.com, please <a class="bold" href="http://www.whatbrowser.org/" target="_blank" >upgrade your browser</a>.</p></div> <![endif]-->


<?php
	//If there are any notices, we are going to loop through them and display them all
	if ( !empty($_SESSION['notices']) ) {

		if (in_array('timeouterror', $_SESSION['notices'])) {
?>
			<div class="notice notice-icon">
				For your security, you've been signed out due to inactivity.
			</div>
<?php
		} else {

			$notice_count = array_count_values($_SESSION['notices']);

			foreach ($notice_count as $notice => $count) {


				//Add to cart failure
				if ($notice === 'addtocartfail') {
?>
					<div class="error notice-icon">
						Could not add item to cart. Please check that you've entered a valid quantity and try again, or contact customer service for assistance.
					</div>
<?php
				}


				if ($notice === 'passwordreset') {
?>
					<div class="success notice-icon">
						Your password has been successfully reset, and you are now logged into your account.
					</div>
<?php
				}


				if ($notice === 'accountcreated') {
?>
					<div class="success notice-icon">
						Your account has been successfully created, and you are now signed in.
					</div>
<?php
				}


				if ($notice === 'loggedout') {
?>
					<div class="success">
						You have been successfully signed out.
					</div>
<?php
				}


				if ($notice === 'guestlogin') {
?>
					<div class="success notice-icon">
						You are now signed in as a guest.
					</div>
<?php
				}


				if ($notice === 'savedesign-signin') {
?>
					<div class="notice notice-icon">
						You must sign in before you may save a design to your account.
					</div>
<?php
				}


				if ($notice === 'savedesign-saved') {

					if ($count > 1) {
?>
						<div class="success notice-icon">
							Your designs have been successfully saved to your account.
						</div>
<?php
					} else {
?>
						<div class="success notice-icon">
							Your design has been successfully saved to your account.
						</div>
<?php
					}
				}


				if ($notice === 'savedesign-failed') {
					$contact_page = new Page('contact-us');
?>
					<div class="error notice-icon">
						We could not save your design. Please <a href="<?php echo $contact_page->getUrl(); ?>">Contact customer service</a> for assistance.
					</div>
<?php
				}


				if ($notice === 'reordered') {

					if ($count > 1) {
?>
						<div class="success notice-icon">
							Your items have been added to your cart.
						</div>
<?php
					} else {
?>
						<div class="success notice-icon">
							Your item has been added to your cart.
						</div>
<?php
					}
				}


				if ($notice === 'reorder_fail') {

					if ($count > 1) {
?>
						<div class="error notice-icon">
							There was a problem while trying to reorder the items.
						</div>
<?php
					} else {
?>
						<div class="error notice-icon">
							There was a problem while trying to reorder the item.
						</div>
<?php
					}
				}
			}
		}
	}
?>



<?php
	//Clear any session notices, as the user has already seen them at this point
	$_SESSION['notices'] = NULL;
?>