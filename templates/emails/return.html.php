<table border='0' width='600px' style="padding-bottom:10px;">
	<tr>
		<td>
			<p>Hello <?php print htmlspecialchars($name,ENT_QUOTES,"UTF-8");?>,</p>
			<p>Thank you for contacting <a href="<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>">SafetySign.com</a> regarding your return. A Customer Service Representative will get back to you as soon as possible. Due to the high volume of communications we receive, a reply may take 1 business day.</p>
		</td>
	</tr>
	<tr>
		<td><p>For your records, here is a copy of the information you have provided:</p></td>
	</tr>
	<tr>
		<td>Name:<br>
		<?php print htmlspecialchars($name,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td>Email Address:<br>
		<?php print htmlspecialchars($email,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td>Company:<br>
		<?php print htmlspecialchars($company ,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td>Phone:<br> 
		<?php print htmlspecialchars($phone ,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td>Choose One:<br>
		<?php print htmlspecialchars($reason,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td>Order Number:<br>
		<?php print htmlspecialchars($orderno,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td style="padding-bottom:10px;">Message: <pre><?php print htmlspecialchars($comments,ENT_QUOTES,"UTF-8");?></pre></td>
	</tr>
	<tr>
		<td style="border-top:1px solid black;padding-top:.5em;">
			<p>For more immediate service call customer service toll free at 800-274-6271, Monday - Friday between the hours of 9:00am and 5:00pm EST.</p>
			<p>You can visit our web site at <a href='<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>'>http://www.safetysign.com/</a>
			</p>
			<p>Sincerely,<br />
				SafetySign.com Customer Service
			</p>
		</td>
	</tr>
</table>
<table>
	<tr>
		<td style="border-top:1px solid #000;padding-top:10px;">
			<p>We thank you for your business and welcome questions or comments.</p>
			<p>
				<strong>SafetySign.com</strong><br>
				Brimar Industries<br>
				P.O. Box 467<br>
				64 Outwater Lane<br>
				Garfield, NJ 07026<br>
			</p>
			<p><strong>Contact Customer Service:</strong><br>
				Phone: 800-274-6271<br>
				Fax: 800-279-6897<br>
				E-mail: <?php print EMAIL_SERVICE;?>
			</p>
			<p>
				<strong>Hours of Operation:</strong><br>
				9am - 5pm Eastern<br>
				Monday - Friday
			</p>
		</td>
	</tr>
</table>