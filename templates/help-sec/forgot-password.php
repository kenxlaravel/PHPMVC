<?php
	$link = new Page('customer-service');
?>

<div class="help-term prepend-top">
	<p class="h4">Forgot Password</p>
		<p>
			If you have forgotten your password or are having trouble accessing your account, we suggest you use the <a href="<?php echo $links['forgotpassword']; ?>">forgot password</a> form to automatically reset it.
		</p>
</div>


<div class="help-term prepend-top">
	<p class="h4">Contact Customer Service</p>
		<p>
			If you have tried resetting your password and are still having trouble accessing your account, or you have forgotten your username, please <a href="<?php echo $link->getUrl(); ?>">contact customer service</a> for assistance.
		</p>
</div>