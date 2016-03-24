<table border='0' width='600px' style="padding-bottom:10px;">
	<tr>
		<td>
			<p>The following information was received from the SafetySign.com contact form:</p>
		</td>
	</tr>
	<tr>
		<td>Name:<br>
		<?php print htmlspecialchars($name,ENT_QUOTES,"UTF-8") ;?> </td>
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
		<?php print htmlspecialchars($phone,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td>Department:<br>
		<?php print htmlspecialchars($department ,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td>Contact me via:<br>
		<?php print htmlspecialchars($contact_me,ENT_QUOTES,"UTF-8");?></td>
	</tr>
	<tr>
		<td style="padding-bottom:10px;">Comments:<br>
		<pre><?php print htmlspecialchars($comment,ENT_QUOTES,"UTF-8");?></pre></td>
	</tr>
</table>
