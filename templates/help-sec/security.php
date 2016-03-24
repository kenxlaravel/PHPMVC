<?php
	$forgotpassword_page = new Page('forgotpassword');
?>

<div class="help-term prepend-top">
	<p class="h4">Can I safely place an order online?</p>
	<p>Yes, SafetySign.com is a secure e-commerce site where your credit card information and passwords are entered on secure webpages. A secure webpage is a webpage which is encrypted. A secure page makes it safe to enter credit card information without concern that someone &ndash; other than the intended recipient &ndash; will see your information. You can tell if a webpage is secure by looking for<strong> https://</strong> in the URL and looking for the little gold lock at the bottom of the webpage.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Who will have access to my credit card information?</p>
	<p>The only people who have access to your credit card information are banking institutions and their intermediaries. The website and hosting company do not store the information in a format that can be directly read by people. All the information is received encrypted and remains encrypted.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">What security is in place to safeguard my personal information?</p>
	<ul class="with-bullets">
		<li>Total security includes firewalls, intruder detection, testing, and physical security.</li>
		<li>Layered redundant hardware and constantly updated security measures protect all servers.</li>
		<li>Security robots and Intruder Detection Systems patrol the network to prevent theft and vandalism.</li>
		<li>All our systems are constantly scanned, poked, and prodded for security weaknesses.</li>
		<li>Our systems are repeatedly tested and certified secure by some of the best 3rd party security organizations operating today. These organizations try to hack our networks, scan for weaknesses, and inspect for problems. When they come up empty-handed, they certify us as hacker safe.</li>
		<li>All logs are constantly reviewed by experts for tampering and suspicious activity missed by security systems.
		<li>This site is regularly swept for viruses and Trojans.</li>
		<li>All email servers are scanned for viruses.</li>
		<li>All equipment is maintained at the most current level of security.</li>
		<li>Magnetic card and palm scan required for access to any server or networking equipment.</li>
		<li>24/7 physical securities including video surveillance by onsite personnel and security guards.</li>
		<li>24/7 systems monitoring by onsite personnel</li>
	</ul>
</div>
<div class="help-term prepend-top">
	<p class="h4">What do I do if I forget my password?</p>
	<p>If you have forgotten your username and password, you can use the password recovery option (<a href="<?php echo $forgotpassword_page->getUrl(); ?>">click here to recover password</a>). This option will email a copy of your username and password to the email address on-file. The process is quick and easy. It will take less than a few minutes to receive your username and password by email. Click on the link for &quot;Forgot Password&quot;. This hyper link can be found below the log-in entry form.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">How do I update my account?</p>
	<p>You can update your account information by using the MyAccount web page. Your account information is also automatically updated with each order you place.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Is my login information case sensitive?</p>
	<p> Yes, your login information is case sensitive. </p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Will safetysign.com share my personal information?</p>
	<p>SafetySign.com does not rent or sell customer information. For more information on our privacy policy, click on the
		<a href="<?php	$privacy = new Page('privacy-policy');
						print $privacy->getUrl();
		?>">privacy policy</a> link on the bottom of each page. </p>
</div>
