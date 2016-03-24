<?php
	$account= new Page('my-account');
	$helpSavingCarts = new Page('help-saving-carts');
?>
<div class="help-term prepend-top">
	<p class="h4">Credit Cards</p>
	<p>The following major credit cards are all accepted: </p>
	<ul class="with-bullets">
		<li>Visa</li>
		<li>Mastercard</li>
		<li>Discover Card</li>
		<li>American Express</li>
	</ul>
	<p>PayPal is also accepted as a form of payment.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Net30</p>
	<p>SafetySign.com accepts Net 30 day terms with approved credit. To get started, fill out the online <a href="http://fs4.formsite.com/michaelbrimarcom/creditapp/index.html" target="_blank" rel="nofollow">credit application</a>.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">How do I order a stock product?</p>
	<p>You can search for products in a variety of ways. Type a word, phrase, or part number in the search window found at the top of every page and click &quot;<strong>Find</strong>&quot;. </p>
	<p>You can browse by category and choose from the dropdown of related groupings shown on the left side of page. </p>
	<p>Once you see the desired sign/product, select it and you can view the product&rsquo;s range of sizes and materials. </p>
	<p>Type a quantity adjacent to the material you&rsquo;d like and click the &quot;add to cart&quot; button. The item will be added to the shopping cart.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">How do I order a customized product?</p>
	<p>Every category displayed on the top navigation will open up a page full of signs ready to be customized.</p>
	<p>To create custom signs, an application on the website will open. To be able to utlize this custom tool / wizard, you must have installed the Adobe Flash Player version 10. Most computers come with it installed, some older computer will need to install it or update it to version 10. Click here to download the <a href="http://get.adobe.com/flashplayer/" target="_blank">Adobe Flash Player</a></p>
	<p>Creating custom signs have never been easier or more exciting. To learn step by step sign customization <a href="<?php $customizing= new Page('customizing'); print $customizing->getUrl();?>">click here</a></p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Can I save a shopping cart for later?</p>
	<p>Yes, you can. If you are signed in, just click the "Save Cart" button on the shopping Cart. If you don't have an account, you'll be prompted to create one before you can save. See our "<a href="<?php echo $helpSavingCarts->getUrl(); ?>">Saving your Shopping Cart</a>" page for more information.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Can I add special instructions to an order?</p>
	<p>Yes, you can add special instruction to an order on the checkout page along with your shipping information. Special instruction can also be added onto each individual custom product.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Which credit cards does SafetySign.com accept?</p>
	<p>American Express, Visa, Master Card, Discover, we also accept PayPal for credit card transaction as well as Brimar Net 30 terms. Refer to <a href="<?php $net= new Page('net30'); print $net->getUrl();?>">&quot;How to open a Net 30 Account&quot;</a> page for more information.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Can I view a previous order?</p>
	<p>If you created an account during or prior to checkout, your order history is tied to your account. If you checked out as a guest, you cannot view your order history. Previous orders can be viewed by going to order history on the <a href="<?php print $account->getUrl();?>">my account</a> page. Clicking on any of the hyperlinks in the order history area will load a printable copy of the e-receipt for the order. This e-receipt contains all the information in the system about the order except order status. The order status or tracking number is display next to the order number.</p>
	<p>Guest checkout does not offer the option to view or to track your order. Because of this, we recommend opening an account at checkout. Having an account allows you to view, track, and reorder previously ordered products.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Can I reorder products?</p>
	<p>Using the reorder page, you can quickly reorder items that you have previously purchased. A button which loads the reorder page is located at the top of your my account page.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">How can I see my order history?</p>
	<p>You can view previous orders by going to the order history page. Order history can be found on the <a href="<?php print $account->getUrl();?>">my account</a> page.</p>
	<p>Clicking on any of the hyperlinks in the order history area will load a printable copy of the e-receipt for the order. This e-receipt contains all the information in the system about the order except status. The order status or tracking number is display next to the order number. </p>
	<p>Guest checkout does not offer the option to view or to track your order. Because of this, we recommend opening an account at checkout. Having an account allows you to view, track, and reorder previously ordered products.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Will I receive an invoice in the mail?</p>
	<p>Not automatically. A printable receipt is available at the end of the checkout process. This receipt contains all information about your order. If you would like an invoice mailed, please request it when you place your order in the special instructions field.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">What will be charged to my credit card, PayPal, or Brimar Net30 account?</p>
	<p>Safetysign.com will charge you the exact amount that appears on your order confirmation for product, tax, and shipping. If you select tax-exempt status and do not provide a tax exempt certificate within 24 hours, tax will be added onto your charges.</p>
</div>
