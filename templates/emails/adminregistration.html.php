<table border='0' width='600px'>
	<tr>
		<td>Hello,</td>
	</tr>
	<tr>
		<td>You are receiving this email because a representative on the <a href='<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>'>SafetySign.com</a> web site created a new account for you.</td>
	</tr>
	<tr>
		<td>
			<p>Here is the information you need in order to sign in on <a href="<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>">SafetySign.com</a>.</p>
			<p style="padding-top:10px;padding-bottom:10px;">Email: <?php echo htmlspecialchars($username,ENT_QUOTES,"UTF-8"); ?><br />
			Password: <?php echo htmlspecialchars($password,ENT_QUOTES,"UTF-8"); ?></p>
			<br />
		</td>
	</tr>
	<tr>
		<td><p style="padding-bottom:10px;">When logging into your account, please note that the login information (user name and password) is case-sensitive. Make sure that all characters are typed using the correct case when entering the username and password.</p></td>
	</tr>
	<tr>
		<td style="border-top:1px solid black;padding-top:10px;padding-bottom:10px;">
			<p>For more immediate service call customer service toll free at 800-274-6271, Monday - Friday between the hours of 9:00am and 5:00pm EST.</p>
			<p>You can visit our web site at <a href="<?php echo htmlspecialchars(URL_PREFIX_HTTP, ENT_QUOTES, 'UTF-8'); ?>">http://www.safetysign.com/</a>
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