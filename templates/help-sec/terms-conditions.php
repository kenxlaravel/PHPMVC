<?php
	// HELP_INCLUDE will be defined if this file is included. Otherwise it is being accessed via a lightbox and we will have to include
	// a stylesheet and bs_common.
	if (!defined('HELP_INCLUDE')) {
		require_once($_SERVER['DOCUMENT_ROOT'] . '/bs_common.php');
?>
		<link rel="stylesheet" href="/styles/checkout/terms-conditions.css">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script>window.jQuery || document.write('<script src="<?php print URL_PREFIX; ?>/scripts/fallback_for_cdn/jquery.min.20130601.js"><\/script>')</script>
		<script>$(function () {
			$("a").on("click", function (event) {
				event.preventDefault();
				window.open($(this).attr("href"), "_blank");
			});
		});</script>

		<div class="grid_16 clearfix">
			<header class="clearfix">
				<h2 class="help-message grid_12 alpha">Terms &amp; Conditions</h2>
			</header>
		</div>
<?php
	}
?>

<div class="help-term prepend-top">
	<p class="h4">Satisfaction Guarantee</p>
	<p>Your business is important to us at <strong>SafetySign.com</strong>. We guarantee that every effort will be made to ensure your 100% satisfaction with our products, price, and service. Please contact <strong>SafetySign.com</strong> via phone, fax, or email with any questions or concerns. We're always happy to help.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Shortages</p>
	<p>Claims for shortages must be made within 30 days from the date of shipment. Claims made after 30 days will not be considered.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Shipping &amp; Freight</p>
	<p>All orders ship F.O.B. Garfield, New Jersey.</p>
	<p>United Parcel Service (UPS) and FedEx are the package services used by <strong>SafetySign.com</strong>. Expedited methods of freight are also available, such as next day air, and second day air. Please specify the shipping method when ordering, or <strong>SafetySign.com</strong> will ship the least expensive method, regardless of delivery time.</p>
	<p><strong>SafetySign.com</strong> is not responsible for lost or damaged freight. All packages are insured when they leave <strong>SafetySign.com</strong>. If a package is damaged in shipment, you should not accept the package without appropriate documentation of damage. <strong>SafetySign.com</strong> will be happy to work with you and the carrier to secure a settlement for damaged freight provided proper documentation has been made on receipt. Failure to document damage will make it impossible to secure an insurance claim on your behalf.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Pricing</p>
	<p>Prices contained on this web site are subject to change without notice.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Products</p>
	<p><strong>SafetySign.com</strong> reserves the right to change the specifications of its stock products without notice.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Custom Orders</p>
	<p><strong>SafetySign.com</strong> reserves the right to change the specifications of its custom products without notice.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Printing of Safetysign.com Logo and Part Number on Signs</p>
	<p><strong>All SafetySign.com</strong> products have the SafetySign.com logo and part number printed in small print on the front of the product. This is for both reordering purposes for the end user and as a recall mechanism to manage the return of product if necessary.</p>
	<p>Custom products can be printed without the safetysign.com logo and part number if requested at the time of ordering.  Stock products can be printed without the SafetySign.com logo at the time of ordering for an additional charge.  If an order for stock product is placed on our website with the request for no logo/part number, the customer will be contacted by our sales team to discuss these extra charges. Additionally, any stock product ordered without a SafetySign.com logo/part number will be considered custom products and are NOT RETURNABLE.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Minimum Order</p>
	<p><strong>SafetySign.com</strong> has a no minimum order for online purchases.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Payment Terms</p>
	<p>The following credit cards are accepted Visa, Mastercard, Discover Card and American Express in addition to PayPal. <strong>SafetySign.com</strong> also accepts <a href="<?php $net = new Page('net30'); print $net->getUrl();?>" target="_parent">Net 30</a> day terms with approved credit. To get started, fill out the online <a href="http://fs4.formsite.com/michaelbrimarcom/creditapp/index.html" target="_blank" rel="nofollow">credit application</a>.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Cancellation</p>
	<p>Once an order is placed it goes immediately into production. Therefore, we are unable to accomodate order changes or cancellations; erroneously ordered items must be returned after delivery.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Product Returns</p>
	<ul class="with-bullets">
		<li>Custom products cannot be return unless they were defective at the time of sale.</li>
		<li>Return of stock product is subjected to a 25% restocking charge.</li>
		<li>Return Authorization number must be obtained prior to return of any product.</li>
		<li>Return Authorization number is obtained by calling or emailing <strong>SafetySign.com </strong></li>
		<li>Packages returned without authorization have to be refused. No exceptions.</li>
		<li>Returnable products can be returned for either product credit or refund.</li>
		<li>Credit or refund is only available for stock products or defective custom product.</li>
		<li>Refund or credit will only be issued after return and inspection (approx. 1 to 2 weeks).</li>
		<li>All returned product are inspected upon receipt to confirm credit or refund amount.</li>
		<li>Products must be returned within 60 days of sale. No returns after 60 days</li>
		<li>Products must be in NEW resealable condition to receive a credit or refund.</li>
		<li>Products damaged in shipment are not covered by warranty.</li>
		<li>If return freight is lost or damaged in shipment it is not covered by warranty.</li>
		<li>UPS or FedEx call tags are provided only on <strong>SafetySign.com</strong> errors or defective products.</li>
	</ul>
	<div class="span-18 append-bottom">
	<div class="span-8">
		<h5>Return Address</h5>
		<hr />
		<address>
		<span>Brimar Industries, Inc</span><br />
		<span><strong>SafetySign.com</strong></span><br />
		<span>P.O. Box 467</span><br />
		<span>64 Outwater Lane Garfield, NJ 07026</span><br />
		<span><em>Attn: Customer Service</em></span>
		</address>
	</div>
	<div class="span-8 last">
		<h5>Toll Free Number</h5>
		<hr />
		<address>
		<span>1-800-274-6271</span>
		</address>
	</div>
	</div>
</div>
<div class="help-term prepend-top">
	<p class="h4">Product Warranty</p>
	<p><strong><strong>SafetySign.com</strong></strong> sells its products with the intent that they are free of defects in manufacturer and workmanship at the time of sale. <strong><strong>SafetySign.com</strong></strong> warrants that each of its products will be free of defects in material and workmanship under normal use and service. However, the obligation of <strong><strong>SafetySign.com</strong></strong> under this warranty shall be limited to the requirement that it makes good at <strong><strong>SafetySign.com's </strong></strong>place of business any part or parts which are returned to <strong><strong>SafetySign.com</strong></strong> within 30 days from the date of delivery to the purchaser. Such return of the product to <strong><strong>SafetySign.com</strong></strong> must be made with transportation charges prepaid. <strong><strong>SafetySign.com</strong></strong> will then examine the product to determine if the product is in fact defective. The aforesaid warranty is expressly in lieu of all other warranties expressed or implied along with all other obligations or liabilities on the part of <strong><strong>SafetySign.com</strong></strong>. <strong><strong>SafetySign.com</strong></strong> does not authorize anyone to obligate <strong><strong>SafetySign.com</strong></strong> in any way beyond the terms set forth above. <strong><strong>SafetySign.com</strong></strong> does not warrant any product which it has been the subject of misuse, negligence, accident, repair or alteration which, in the judgment of <strong><strong>SafetySign.com</strong></strong>, affects the product's stability or reliability.
	<p> THE WARRANTIES PROVIDED BY <strong><strong>SafetySign.com</strong></strong> AS REFERRED TO ABOVE SHALL BE THE SOLE AND EXCLUSIVE WARRANTIES. THERE SHALL BE NO OTHER WARRANTIES EXPRESSED OR IMPLIED, INCLUDING ANY IMPLIED WARRANTY OF MERCHANTABILITY OR FITNESS OR ANY OTHER OBLIGATION ON THE PART OF <strong><strong>SafetySign.com</strong></strong> WITH RESPECT TO PRODUCTS COVERED BY THIS AGREEMENT.
		IN NO EVENT SHALL THE WARRANTIES OF <strong><strong>SafetySign.com</strong></strong> REQUIRE MORE FROM <strong><strong>SafetySign.com</strong></strong> THAN THE REPAIR OR REPLACEMENT OF ANY PART OR PARTS WHICH ARE FOUND TO BE DEFECTIVE WITHIN THE EFFECTIVE PERIOD OF THE WARRANTY. <strong><strong>SafetySign.com</strong></strong> SHALL HAVE NO LIABILITY FOR ANY INCIDENTAL OR CONSEQUENTIAL DAMAGES.</p>
	<p> Any and all warranties or guarantees shall immediately cease and terminate as to any products or parts thereof which are altered, or modified, without the prior express and written consent of <strong><strong>SafetySign.com</strong></strong>.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Standards &amp; Sources of Information</p>
	<p>Many of the <strong>SafetySign.com</strong> products found on this website have been designed to comply with one or more industry standards.</p>
	<p>WHEN <strong>SafetySign.com</strong> STATES THAT A PRODUCT COMPLIES WITH A PARTICULAR STANDARD, WE ARE CONVEYING OUR GENERAL INTERPRETATION OF THAT STANDARD RELEVANT TO SIZE OF TEXT, USE OF GRAPHIC, AND THE LOCATION AND SIZE OF MESSAGE PANELS, ETC.</p>
	<p>SINCE WE HAVE NO KNOWLEDGE OF YOUR SPECIFIC FACILITY OR HAZARD, IT'S IMPOSSIBLE FOR <strong>SafetySign.com</strong> TO GUARANTEE THAT YOUR USE OF OUR PRODUCTS WILL COMPLY WITH ANY LAW OR STANDARD.
		THIS SITE IS NOT INTENDED AS A SUBSTITUTE FOR EXPERT ANALYSIS OR PROFESSIONAL CONSULTATION. THE INFORMATION IS ACCURATE TO THE BEST OF OUR KNOWLEDGE BASED ON OUR REVIEW OF THE INDUSTRY. <strong>SafetySign.com</strong> MAKES NO GUARANTEE OF INFORMATION ON THIS WEBSITE.</p>
	<p>EACH CUSTOMER IS RESPONSIBLE TO ENSURE THAT THE USE OF <strong>SafetySign.com</strong> PRODUCTS COMPLY WITH APPLICABLE LAWS AND STANDARDS. <strong>SafetySign.com</strong> ASSUMES NO LIABILITY OF INJURY OR DAMAGE AS A RESULT OF USING OUR PRODUCTS. SEE OUR FULL WARRANTY ABOVE.</p>
</div>
<div class="help-term prepend-top">
	<p class="h4">Privacy</p>
	<p>SafetySign.com does not sell, trade, or otherwise transfer to outside parties your personally identifiable information to any company or organization. This does not include trusted third parties who assist us in operating our website, conducting our business, or servicing you, so long as those parties agree to keep this information confidential. We may also release specific information about you, your company, or your account to comply with any valid legal process such as search warrant, subpoena, statute, or court order.</p>
	<p>We will never use your personally identifiable information to place you on any mailing list that does not belong to SafetySign.com&#8217;s parent company, Brimar Industries, Inc. If you choose to subscribe, we will enroll you to a promotional email list to receive discount offers and special deals. We may also send our customers notifications and direct mailing pieces about products or events that we think may be of interest to them. In addition, we may contact you about your account status, to confirm your registration, or regarding changes to the SafetySign.com Terms &amp; Conditions or Privacy Policy.</p>
	<p>Further information is available in our <a href="<?php $net = new Page('privacy-policy'); print $net->getUrl();?>">full privacy policy</a>.</p>
</div>
