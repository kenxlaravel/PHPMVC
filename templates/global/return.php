<?php

$flag_return=1;

if(isset($_REQUEST['submit']) && $_REQUEST['submit']) {
    $error = $ObjContactUs->getReturnCheck();
    //check for successful return
    if( empty($error) ) {
        $flag_return = 2;
    }
}

if(isset($error) && (!empty($error))){
	?>

	<div class='error notice-icon'>
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

if( $flag_return==1 ){

	?>
	<style>
	#return {
		display:none;
	}

	</style>
	<p class="h3 h3-rev pad-left-15 append-bottom">Start a Return</p>
	<p class="pad-left-10">Fill out the form below to request a <span class="bold">Return Authorization Number</span>.</p>
	<p class="pad-left-10">Please do not return any products
		until you receive this <span class="bold">Return Authorization Number</span>. For more information please read our <a href="/help-returns.php"><span class="bold">Return Policy</span></a>.</p>
		<div class="form-container span-12 prepend-top pad-left-10">
			<form accept-charset="utf-8" id="contact-form" class="contacting inline prepend-top append-bottom return-form" action="<?php print htmlspecialchars($url,ENT_QUOTES,"UTF-8");?>"  method="post" >
				<div class="error-message"><span></span></div>
				<div>
					<label for="contact_name" class="required ">Name</label>
					<input type="text" class="text required copy" id="contact_name" name="contact_name"  maxlength="30" value="<?php print htmlspecialchars(isset($_REQUEST['contact_name']) ? $_REQUEST['contact_name'] : NULL,ENT_QUOTES,'UTF-8');?>"   />
				</div>
				<input type="text" id="return" name="return" autocomplete="off">

				<div>
					<label for="contact_email" class="required ">Email</label>
					<input type="text" class="text required copy" id="contact_email" name="contact_email"  maxlength="50" value="<?php print htmlspecialchars(isset($_REQUEST['contact_email']) ? $_REQUEST['contact_email'] : NULL,ENT_QUOTES,'UTF-8');?>" />
				</div>

				<div>
					<label for="contact_company">Company</label>
					<input type="text" class="text copy" id="contact_company" name="contact_company"  maxlength="30" value="<?php print htmlspecialchars(isset($_REQUEST['contact_company']) ? $_REQUEST['contact_company'] : NULL,ENT_QUOTES,'UTF-8');?>" />
				</div>

				<div>
					<label for="contact_phone">Phone</label>
					<input type="text" class="text copy required" id="contact_phone" name="contact_phone"  maxlength="15" value="<?php print htmlspecialchars(isset($_REQUEST['contact_phone']) ? $_REQUEST['contact_phone'] : NULL,ENT_QUOTES,'UTF-8');?>" />
				</div>


				<div>
					<label for="order_number">Order Number</label>
					<input type="text" class="text copy required" id="order_number" name="order_number"  maxlength="15" value="<?php print htmlspecialchars(isset($_REQUEST['order_number']) ? $_REQUEST['order_number'] : NULL,ENT_QUOTES,'UTF-8');?>" />
				</div>

				<div class="append-bottom">
					<label for="department">Choose One</label>
					<select name="return-reason-selector" id="return-reason-selector">
						<option value="damaged" <?php if(isset($_REQUEST['return-reason-selector']) && $_REQUEST['return-reason-selector']=="damaged") print "selected";?>>Item was damaged</option>
						<option value="wrong item" <?php if(isset($_REQUEST['return-reason-selector']) && $_REQUEST['return-reason-selector']=="wrong item") print "selected";?>>Wrong Item</option>
						<option value="do not need" <?php if(isset($_REQUEST['return-reason-selector']) && $_REQUEST['return-reason-selector']=="do not need") print "selected";?>>Do not need these items</option>
						<option value="other" <?php if(isset($_REQUEST['return-reason-selector']) && $_REQUEST['return-reason-selector']=="other") print "selected";?>>Other: Please state reason below</option>
					</select>
				</div>

				<div id="other-reason-for-return" class="noshow append-bottom">
					<label for="question">Reason for return</label>
					<textarea id="message" name="message" class="left-side" rows="6" cols="6"><?php print htmlspecialchars(isset($_REQUEST['message']) ? $_REQUEST['message'] : NULL,ENT_QUOTES,'UTF-8');?></textarea>
				</div>

				<div class="recaptcha" id="return-recaptcha" data-recaptcha-key="<?php print RECAPTCHA_PUBLIC_KEY;?>">
					<?php
				//hard-code part of recaptchalib file the function of recaptcha_get_html().
				 $server = "//www.google.com/recaptcha/api"; 
				 $pubkey = RECAPTCHA_PUBLIC_KEY;
				 ?>
				<noscript>
      			<iframe src="<?php print $server;?>/noscript?k=<?php print $pubkey;?>" height="300" width="500" frameborder="0"></iframe>
      			<br/>
      			<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>
      			<input type="hidden" name="recaptcha_response_field" value="manual_challenge"/>
  				</noscript>
				</div>
				<div class="right-side">
					<button type="submit" name="submit" class="contact orange button" value="Send Message" >Send Message</button>
				</div>
			</form>
		</div>
		<?php
	}
	if($flag_return==2){
		?>
		<p class="h3 h3-rev pad-left-15 append-bottom">Thank You</p>
		<p class="pad-left-10">A Customer Service Person will respond back to you within the next business day.</p>
		<?php }