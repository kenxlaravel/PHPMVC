<?php
$flag_enter=1;
$error = "";
$contact_name = "";
$contact_email = "";
$contact_company ="";
$contact_phone = "";
require_once(APP_ROOT."/recaptchalib.php");

if(isset($_REQUEST['submit'])){

	$error=$objContactUs->getContactCheck();

	//check for successful return
	if(empty($error)){
		$flag_enter=2;
	}
}

if($error!=1 && (!empty($error))){
	?>

	<div class='error  notice-icon'>
		<p class="bold"><?php if(count($error)>1){?>There were errors <?php } else {?> There was an error <?php }?> processing your request. If you continue to have problems, please <a href="">contact Customer Service</a> for assistance.</p>
		<ul>
			<?php
			foreach ($error as $key => $value) {
				print "<li>" . $value . "</li>";
			}
			$flag_enter=1;
			?>
		</ul>
	</div>

	<?php
}
if( $flag_enter==1 && $error!=1){

	?>
	<p class="h3 h3-rev pad-left-15 append-bottom">Contact Us</p>

	<div class="pad-left-10 span-18">
		<p>We do our best to reply to our customers in a timely fashion, however due to high volume e-mails and calls our reply to your e-mail may take up to 24 hours, if answer to your inquiry is urgent, please give us a call at <span class="bold">1-800-274-6271</span> instead.</p>
		<p><span class="bold">You can also email us directly at <a href="mailto:<?php print EMAIL_SALES;?>" style="font-weight: bold;"><?php print EMAIL_SALES;?></a></span></p>
		<p>Looking for our address and directions to our facility? You can find more information on our <span style="font-weight:bold;"><a href="/help-general.php">About Us</a></span> page.</p>
	</div>

	<div class="form-container span-12 pad-left-10">
		<form accept-charset="utf-8" id="contact-form" class="contacting inline prepend-top append-bottom" action="<?php echo htmlspecialchars($link,ENT_QUOTES,"UTF-8");?>" method="post" />

			<div>
				<label for="contact_name" class="required">Name</label>
				<input type="text" class="text required copy" id="contact_name" name="contact_name"  maxlength="30" value="<?php echo htmlspecialchars(isset($_REQUEST['contact_name'])? $_REQUEST['contact_name'] : NULL, ENT_QUOTES , 'UTF-8');?>" />
			</div>
			<input type="text" id="contact" name="contact" autocomplete="off">

			<div>
				<label for="contact_email" class="required">Email</label>
				<input type="text" class="text required copy" id="contact_email" name="contact_email"  maxlength="50" value="<?php echo htmlspecialchars(isset($_REQUEST['contact_email'])? $_REQUEST['contact_email'] : NULL, ENT_QUOTES , 'UTF-8');?>" />
			</div>

			<div>
				<label for="contact_company">Company</label>
				<input type="text" class="text copy" id="contact_company" name="contact_company"  maxlength="30" value="<?php echo htmlspecialchars(isset($_REQUEST['contact_company'])? $_REQUEST['contact_company'] : NULL , ENT_QUOTES , 'UTF-8');?>" />
			</div>

			<div>
				<label for="contact_phone">Phone</label>
				<input type="text" class="text copy required" id="contact_phone" name="contact_phone"  maxlength="15" value="<?php echo htmlspecialchars(isset($_REQUEST['contact_phone'])? $_REQUEST['contact_phone'] : NULL, ENT_QUOTES , 'UTF-8');?>" />
			</div>

			<div>
				<label for="department">Department</label>
				<select name="contact_department">
					<option value="sales" <?php if($_REQUEST['contact_department']=='sales') echo "selected";?>>Web Sales</option>
					<option value="customer_service" <?php if($_REQUEST['contact_department']=='customer_service')echo "selected";?>>Customer Service</option>
					<option value="billing" <?php if($_REQUEST['contact_department']=='billing')echo "selected";?>>Billing and Credit</option>
				</select>
			</div>

			<div class="clear">
				<label class="multiline-label">Contact me via</label>
				<label class="radio-label"><input id="preferred-email" type="radio" name="preferred_method_of_contact" value="Contact by email" checked />Email</label>
				<label class="radio-label"><input id="preferred-phone" type="radio" name="preferred_method_of_contact" value="Contact by phone">Phone</label>
			</div>

			<div>
				<label for="question" class="required" >Message</label>
				<textarea id="message" name="message" class="required" rows="6" cols="6"><?php echo htmlspecialchars(isset($_REQUEST['message'])? $_REQUEST['message'] : NULL, ENT_QUOTES , 'UTF-8');?></textarea>
			</div>
			<div class="recaptcha" id="contact-recaptcha" data-recaptcha-key="<?php echo RECAPTCHA_PUBLIC_KEY; ?>">
				<?php
				//hard-code part of recaptchalib file the function of recaptcha_get_html().
				 $server = "//www.google.com/recaptcha/api";
				 $pubkey = RECAPTCHA_PUBLIC_KEY;
				 ?>
				<noscript>
      			<iframe src="<?php echo $server;?>/noscript?k=<?php echo $pubkey;?>" height="300" width="500" frameborder="0"></iframe>
      			<br/>
      			<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
      			<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
  				</noscript>
			</div>
			<div class="right-side">
				<button type="submit" name="submit" class="contact noimage orange button" value="Send Message">Send Message</button>
			</div>

		</form>
	</div>
		<?php
	}
	if($flag_enter==2){
		?>
		<p class="h3 h3-rev pad-left-15 append-bottom">Thank You</p>
		<p class="pad-left-10">A Customer Service Person will respond back to you within the next business day.</p>

		<?php }
