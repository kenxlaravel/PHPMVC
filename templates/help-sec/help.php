<div class="help-term prepend-top">
	<p class="h3 prepend-top">Welcome to the Help Center</p>
	<p> Here you can get answers to your questions about how the site works, how to use our custom tools, and how to contact us if you need further assistance. </p>
</div>
<div class="help-term prepend-top">
<?php
	$listings = $ObjHelp->getListings('main_page');
	$per_column = ceil(count($listings)/2);
	$count = 0;

	foreach ($listings AS $listing) {
		$count++;

		if ($count == 1) {
?>
			<div style="width:340px;float:left;">
<?php
		}

		if ($count == $per_column+1) {
?>
			</div><div style="width:340px;float:left;">
<?php
		}
?>
				<div class="help-index-block">
					<p class="h4"><?php echo htmlspecialchars($listing['name'], ENT_QUOTES, 'UTF-8'); ?></p>
					<p><?php echo htmlspecialchars($listing['main_page_description'], ENT_QUOTES, 'UTF-8'); ?></p>
					<div>
						<ul>
<?php
						foreach ($listing['sections'] AS $value) {

							if ($value['name'] != 'Help') {
								$link = new Page('help', $value['id']);
?>
								<li><a href="<?php echo htmlspecialchars($link->getUrl(), ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($value['name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
<?php
							}
						}
?>
						</ul>
					</div>
				</div>
<?php
		if ($count == count($listings)) {
?>
				<div class="help-index-block">
					<p class="h4">Customer Service</p>
					<p>800-274-6271 &bull; Mon-Fri &bull; 9:00–5:00 EST</p>
				</div>
			</div>
<?php
		}

	}
?>

</div>
<div class="help-term prepend-top">
	<p class="h3 prepend-top">Our Most Frequently Asked Questions</p>
	<p class="h5">How do I contact customer service?</p>
	<p>SafetySign.com has a knowledgeable team of customer service representatives that you can reach toll free at 800-274-6271 (973-340-7889) between the hours of 9:00am &amp; 5:00pm EST.</p>
	<p>This is not a call center; you can speak to the same person each time you call. If they can&rsquo;t answer your question, they are backed up by managers with combined experience of over 75 years.</p>
	<p class="h5">Why should I order from SafetySign.com?</p>
	<p>We&rsquo;re the experts, not just another e-commerce site; you can select from hundreds of stock products or easily create a custom sign, label or tag. SafetySign.com employs the latest technologies on our web site and throughout our manufacturing process.</p>
	<p>We&rsquo;re not distributors selling only the most popular products. We&rsquo;re manufacturers specializing in quality industrial signage and labeling, with a commitment to offering the largest selection of stock and custom products at the lowest possible prices.</p>
	<p class="h5">Does SafetySign.com make other products?</p>
	<p>If you don&rsquo;t find the exact product you require we&rsquo;d be happy to quote on your exact item. More than 50% of SafetySign.com work is custom.</p>
	<p>We pride ourselves in providing solutions, not just products. Our manufacturing includes: screen, flexographic &amp; digital printing, laser &amp; rotary engraving, hot stamping, die cutting and laminating.</p>
<?php
	$link = new Page('faqs');
?>
	<p><a href="<?php echo $link->getUrl(); ?>" class="button blue prepend-top">Read More FAQs</a></p>
</div>
