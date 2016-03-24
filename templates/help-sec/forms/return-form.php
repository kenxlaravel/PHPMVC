<?php if($error_msg){print "<div class='error-message-php'><span>$error_msg</span></div>";}?>
<?php if($_GET[action]!='submit'){?>

<p class="h3 h3-rev pad-left-15">Start a Return</p>
<div class="sidebar">
	<p>Please fill out the form below to request a <strong>Return Authorization Number</strong>.</p><p>Please do not return any products
		until you receive this <strong>Return Authorization Number</strong>. For more information please read our <a href="/help-returns.php"><strong>Return Policy</strong></a>.</p>
</div>
<div class="form-container span-16 prepend-1 last prepend-top">
	<form accept-charset="utf-8" id="contact-form" class="contacting inline prepend-top append-bottom" action="<?php print websitehttps;?>returns.php" method="post" >
		<div class="error-message"><span></span></div>
		<div class="span-9 ">
			<label for="contact_name" class="required span-3">Name</label>
			<input type="text" class="text required copy span-5 last" id="contact_name" name="contact_name"  maxlength="30" value="<?php print htmlspecialchars($_POST['contact_name'],ENT_QUOTES,'UTF-8');?>"   />
		</div>
		<div class="span-9">
			<label for="contact_email" class="required span-3">Email</label>
			<input type="text" class="text required copy span-5 last" id="contact_email" name="contact_email"  maxlength="50" value="<?php print htmlspecialchars($_POST['contact_email'],ENT_QUOTES,'UTF-8');?>" />
		</div>
		<div class="span-9">
			<label for="contact_company" class="span-3">Company</label>
			<input type="text" class="text copy span-5 last" id="contact_company" name="contact_company"  maxlength="30" value="<?php print htmlspecialchars($_POST['contact_company'],ENT_QUOTES,'UTF-8');?>" />
		</div>
		<div class="span-9">
			<label for="contact_phone" class="span-3">Phone</label>
			<input type="text" class="text copy required span-5 last" id="contact_phone" name="contact_phone"  maxlength="15" value="<?php print htmlspecialchars($_POST['contact_phone'],ENT_QUOTES,'UTF-8');?>" />
		</div>
		<div class="span-9">
			<label for="department" class="span-3">Choose One</label>
			<select name="return-reason-selector" id="return-reason-selector" class="span-5 last">
				<option value="damaged" <?php if($_POST['return-reason-selector']=="damaged") print "selected";?>>Item was damaged</option>
				<option value="wrong_item" <?php if($_POST['return-reason-selector']=="wrong_item") print "selected";?>>Wrong Item</option>
				<option value="do not need" <?php if($_POST['return-reason-selector']=="do not need") print "selected";?>>Do not need these items</option>
				<option value="other" <?php if($_POST['return-reason-selector']=="other") print "selected";?>>Other: Please state reason below</option>
			</select>
		</div>
		<div id="other-reason-for-return" class="span-16 noshow append-bottom">
			<label for="question" class="span-3">Reason for return</label>
			<textarea id="message" name="message" class="span-12 last" rows="6" cols="6"><?php print htmlspecialchars($_POST['message'],ENT_QUOTES,'UTF-8');?></textarea>
		</div>
		<div class="span-9 last">
			<label for="order_number" class="span-3">Order Number</label>
			<input type="text" class="text copy required span-5 last" id="order_number" name="order_number"  maxlength="15" value="<?php print htmlspecialchars($_POST['order_number'],ENT_QUOTES,'UTF-8');?>" />
		</div>
		
		<div class="span-16 last">
			<div class="span-3">
					<label for="security_code">Security Code </label>
			</div>
			<div class="captcha-wrapper append-bottom prepend-top">
				<div class="span-8 last">
					<div class="span-3 prepend-top"> <img src="captcha/CaptchaSecurityImages.php?width=100&height=40&characters=5" /> </div>
					<div class="span-5 last">
						<p>Please enter the security code:</p>
						<input type="text" id="security_code" class="text required copy" name="security_code" value="<?php print htmlspecialchars($_POST['security_code'],ENT_QUOTES,'UTF-8');?>">
					</div>
				</div>
			</div>
		</div>
		<div class="span-3 prepend-3 prepend-top">
			<button type="submit" name="submit" class="contact orange button" value="Send Message" >Send Message</button>
		</div>
	</form>
	<?php }?>
	<?php if($_GET[action]=='submit'){?>
	<div class="loading"><!--<img src="new_images/ajaxloader/ajax-loader-bw-large.gif" alt="loading..." />--></div>
	<div class="thankyou">
		<h1>Thank you</h1>
		<p>A Customer Service Person will respond back to within the next 24 hours.</p>
	</div>
	<?php 
}
?>
	<?php if($_GET[action]!='submit'){?>
</div>
<?php 
}
?>
