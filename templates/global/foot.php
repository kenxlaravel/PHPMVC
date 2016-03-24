<?php if (HTML_COMMENTS) {?><!-- BEGIN FOOT --><?php } ?>

	<?php // Front-end Templates ?>
	<?php echo $FrontEndTemplateIncluder->getHtml(); ?>

	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?php print URL_PREFIX; ?>/scripts/fallback_for_cdn/jquery.min.20130601.js"><\/script>')</script>
	<script src="<?php print URL_PREFIX; ?>/scripts/plugins.20140703.js"></script>
	<script src="//www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>

	<?php if ( $page->getType() == 'product' && ( $ObjPageProduct->getCustom() == TRUE || $ObjPageProduct->getTweakId() > 0 ) ) { ?>
		<script src="//ajax.googleapis.com/ajax/libs/webfont/1.1.0/webfont.js"></script>
		<script>window.WebFont || document.write('<script src="<?php print URL_PREFIX; ?>/scripts/fallback_for_cdn/webfont.20130601.js"><\/script>')</script>
	<?php }	?>

	<script src="<?php print URL_PREFIX; ?>/scripts/global.js?201527041010"></script>

<?php

	if ($page->getType() == 'product') {

		if ($ObjPageProduct->getToolTypeName() == 'streetname') {

?>
			<script src="<?php print URL_PREFIX; ?>/scripts/streetname.20140904.js"></script>
<?php

		} elseif($ObjPageProduct->getToolTypeName() == 'builder' || $ObjPageProduct->getTweakId() > 0) {

?>
			<script src="<?php print URL_PREFIX; ?>/scripts/builder.min.20150423.js"></script>

			<?php
		}

		echo "<script src = '".URL_PREFIX. "/scripts/product.page.js' ></script>";
	}
?>


	<?php // Google Analytics, Chartbeat, Google +1, Google Trusted Stores, and Acquisio Async Script Loaders ?>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		var _sf_async_config={};_sf_async_config.uid=16447,_sf_async_config.domain="safetysign.com",_sf_async_config.useCanonical=!0,function(){function a(){window._sf_endpt=(new Date).getTime();var a=document.createElement("script");a.setAttribute("src",("https:"==document.location.protocol?"https://a248.e.akamai.net/chartbeat.download.akamai.com/102508/":"http://static.chartbeat.com/")+"js/chartbeat.js"),document.body.appendChild(a)}var b=window.onload;window.onload="function"!=typeof window.onload?a:function(){b(),a()}}();
		(function(a){var b=a.createElement("script");b.src="https://apis.google.com/js/plusone.js";var c=a.getElementsByTagName("script")[0];c.parentNode.insertBefore(b,c)})(document);
		var gts=gts||[];gts.push(["id","156538"]),gts.push(["google_base_subaccount_id","1126860"]),gts.push(["google_base_country","US"]),gts.push(["google_base_language","EN"]),function(){var t="https:"==document.location.protocol?"https://":"http://",e=document.createElement("script");e.type="text/javascript",e.async=!0,e.src=t+"www.googlecommerce.com/trustedstores/gtmp_compiled.js";var s=document.getElementsByTagName("script")[0];s.parentNode.insertBefore(e,s)}();
		var ATRK_CLIENT_ID="389C9EEA",ATRK_PROTOCOL="https:"==document.location.protocol?"https://":"http://";document.write(unescape("%3Cscript type='text/javascript' src='"+ATRK_PROTOCOL+"js.acq.io/ATRK_"+ATRK_CLIENT_ID+"_min.js' %3E%3C/script%3E"));
	</script>

	<?php // Google Analytics Page Tracking ?>
	<script>ga("create","UA-219423-1","auto"),ga("send","pageview");</script>

<?php

if ($page->getNickname() == 'order-confirmation') {

	$thisorder = end($order);

	// Get the necessary information for the Shopper Approved survey.
	$shopperApprovedData = array(
		'site' => SHOPPER_APPROVED_SITE_ID,
		'orderid' => (string) $thisorder['order_no'],
		'name' => $thisorder['shipping_first_name'] . ' ' . $thisorder['shipping_last_name'],
		'email' => (string) $thisorder['customers_email'],
		'country' => (string) $thisorder['shipping_country'],
		'state' => (string) $thisorder['shipping_state'],
		'days' => ceil((strtotime($thisorder['shipping_arrival_estimate'])-strtotime(date('Y-m-d')))/86400)
	);

?>

	<?php // Display the Shopper Approved survey (asynchronous). ?>
	<script>
		function saLoadScript(a){var b=window.document.createElement("script");b.src=a,b.type="text/javascript",document.getElementsByTagName("head")[0].appendChild(b)}
		var sa_values = <?php echo json_encode($shopperApprovedData); ?>, d = new Date;
		if ( d.getTime() - 172800000 > 1394124529000 ) {
			saLoadScript("//www.shopperapproved.com/thankyou/inline/<?php echo SHOPPER_APPROVED_SITE_ID;?>.js");
		} else {
			saLoadScript("//direct.shopperapproved.com/thankyou/inline/<?php echo SHOPPER_APPROVED_SITE_ID;?>.js?d=" + d.getTime());
		}
	</script>

<?php


	// The order confirmation page reports conversions if an order was placed and the conversion hasn't already been reported (e.g. page refreshes, bookmarks, etc.).
	if ( count($order) > 0 && !$objOrder->getTrackingFlag($thisorder['orders_id']) ) {

		// Instantiate the cart for this order
		$cart = Cart::getCartFromOrderId($thisorder['orders_id']);

		// Get the details of the order to be tracked.
		$trackinginfo = $objOrder->GetOrderTrackingDetails($thisorder['orders_id']);

		// Prepare the Nextopia, Google Analytics, Google Trusted Stores, and Acquisio tracking data arrays.
		$nxt_tracking = array();
		$ga_tracking_products = array();
		$gts_tracking_products = array();
		$acq_tracking_products = array();

		// Add the necessary details of the order to the Google Analytics tracking data array.
		$ga_tracking = array(
			'id' => (string) $trackinginfo['orderno'],                                   // Transaction ID
			'affiliation' => 'safetysign.com',                                           // Store name
			'revenue' => number_format($trackinginfo['total'], 2, '.', ''),              // Total
			'shipping' => number_format($trackinginfo['shipping'], 2, '.', ''),          // Shipping
			'tax' => number_format($trackinginfo['tax'], 2, '.', '')                     // Tax
		);

		// Add the necessary details of the order to the Google AdWords tracking data array.
		$adwords_tracking = array(
			'value' => (float) $trackinginfo['total']                                    // Value
		);

		// Add the necessary details of the order to the Microsoft AdCenter array.
		$msft_tracking = array(
			'ti' => '4028827',                                                           // Tag ID
			'Ver' => '2',                                                                // Tag Version
			'ec' => 'Conversion',                                                        // Event Category
			'gv' => number_format($trackinginfo['total'], 2, '.', '')                    // Goal Value
		);

		// Add the necessary details of the order to the Nextopia tracking data array.
		$nxt_tracking[] = array(
			(string) $trackinginfo['orderno'],                                           // Order ID
			'safetysign.com',                                                            // Website URL
			number_format($trackinginfo['total'], 2, '.', ''),                           // Total
			number_format($trackinginfo['tax'], 2, '.', ''),                             // Tax
			number_format($trackinginfo['shipping'], 2, '.', ''),                        // Shipping
			(string) $trackinginfo['city'],                                              // City
			(string) $trackinginfo['state'],                                             // State
			(string) $trackinginfo['country']                                            // Country
		);

		// Add the necessary details of the order to the Google Trusted Stores tracking data array.
		$gts_tracking = array(
			'id' => (string) $trackinginfo['orderno'],                                   // Order ID
			'domain' => 'www.safetysign.com',                                            // Website URL
			'email' => (string) $value['customers_email'],                               // Customer's email address
			'country' => (string) $trackinginfo['country'],                              // Country
			'currency' => 'USD',                                                         // Currency
			'total' => number_format($trackinginfo['total'], 2),                         // Total
			'discounts' => number_format(0 - $trackinginfo['coupon'], 2),                // Discount
			'shipping-total' => number_format($trackinginfo['shipping'], 2),             // Shipping
			'tax-total' => number_format($trackinginfo['tax'], 2),                       // Tax
			'est-ship-date' => (string) $trackinginfo['shipping_pickup_estimate'],       // Estimated ship date
			'has-preorder' => 'N',                                                       // Preorder
			'has-digital' => 'N'                                                         // Digital goods
		);

		// Add the necessary details of the order to the Acquisio tracking data array.
		$acq_tracking = array(
			'ConversionCode' => 'purchase',                                              // Conversion Type
			'TransId' => (string) $trackinginfo['orderno'],                              // Transaction ID
			'Total' => number_format($trackinginfo['total'], 2),                         // Total
			'Currency' => 'USD',                                                         // Currency
			'Tax' => number_format($trackinginfo['tax'], 2),                             // Tax
			'Shipping' => number_format($trackinginfo['shipping'], 2),                   // Shipping
			'Units' => 0                                                                 // Units (increments in loop below)
		);

		foreach ($cart->products as $product) {

			// Add the necessary details of each item in the order to the Google Analytics tracking data array.
			$ga_tracking_products[] = array(
				'id' => (string) $trackinginfo['orderno'],                               // Transaction ID
				'name' => (string) $product->number,                                     // Product name
				'sku' => (string) $product->skuCode,                                     // SKU
				'category' => (string) $product->subcategoryName,                        // Category
				'price' => number_format($product->unitPrice, 2, '.', ''),               // Unit price
				'quantity' => number_format($product->quantity, 0, '.', '')              // Quantity
			);

			// Add the necessary details of each item in the order to the Nextopia tracking data array.
			$nxt_tracking[] = array(
				(string) $trackinginfo['orderno'],                                       // Order ID
				(string) $product->skuCode,                                              // SKU
				(string) $product->productNumber,                                               // Product name
				(string) $product->subcategoryName,                                      // Category
				number_format($product->unitPrice, 2, '.', ''),                          // Price
				number_format($product->quantity, 0, '.', '')                            // Quantity
			);

			// Add the necessary details of each item in the order to the Google Trusted Stores tracking product data array.
			$gts_tracking_products[] = array(
				'name' => (string) $product->skuCode,                                    // SKU
				'price' => number_format($product->unitPrice, 2),                        // Price
				'quantity' => number_format($product->quantity, 0, '.', ''),             // Quantity
				'prodsearch-id' => 'ss' . $product->skuId,                               // AdWords Product Search Item ID
				'prodsearch-store-id' => '1126860',                                      // AdWords Product Search Store ID
				'prodsearch-country' => 'US',                                            // AdWords Product Search Country
				'prodsearch-language' => 'en'                                            // AdWords Product Search Language
			);

			// Add the necessary details of each item in the order to the Acquisio tracking product data array. Also increment the total item count.
			$acq_tracking_products[] = array(
				preg_replace('/[^A-Za-z0-9_-]/', '_', $product->skuCode),              // SKU
				number_format($product->unitPrice, 2, '.', ''),                        // Unit price
				number_format($product->quantity, 0, '.', '')                          // Quantity
			);
			$acq_tracking['Units'] += $product->quantity;

		}

?>

	<?php // Google Analytics Ecommerce Tracking ?>
	<script>ga("require","ecommerce","ecommerce.js"),ga("ecommerce:addTransaction",<?php echo json_encode($ga_tracking); ?>),<?php foreach ( $ga_tracking_products as $ga_tracking_product ) { ?>ga("ecommerce:addItem",<?php echo json_encode($ga_tracking_product); ?>),<?php } ?>ga("ecommerce:send");</script>

	<?php // Google AdWords Conversion Tracking ?>
	<script>var google_conversion_id=1072500077,google_conversion_language="en",google_conversion_format="3",google_conversion_color="ffffff",google_conversion_label="j3FQCNTilAYQ7Zq0_wM",google_conversion_value=<?php echo json_encode($adwords_tracking['value']); ?>,google_conversion_currency="USD",google_remarketing_only=!1;</script>
	<script src="//www.googleadservices.com/pagead/conversion.js"></script>
	<noscript><div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/1072500077/?<?php echo htmlspecialchars(http_build_query(array( 'value' => $adwords_tracking['value'], 'currency_code' => 'USD', 'label' => 'j3FQCNTilAYQ7Zq0_wM', 'guid' => 'ON', 'script' => 0 )), ENT_QUOTES, 'UTF-8'); ?>"></div></noscript>

	<?php // Microsoft AdCenter Conversion Tracking ?>
	<script>window.uetq=window.uetq||[],window.uetq.push({ec:"Conversion",gv:<?php echo json_encode($trackinginfo['total']); ?>});</script>
	<noscript><img src="//bat.bing.com/action/0?<?php echo htmlspecialchars(http_build_query($msft_tracking), ENT_QUOTES, 'UTF-8'); ?>" height="0" width="0" style="display:none; visibility: hidden;"></noscript>

	<?php // Google Trusted Stores Conversion Tracking ?>
	<div id="gts-order" style="display: none;">
	<?php foreach ( $gts_tracking as $gts_property => $gts_value ) { ?>
		<span id="gts-o-<?php echo htmlspecialchars($gts_property, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($gts_value, ENT_QUOTES, 'UTF-8'); ?></span>
	<?php } ?>
	<?php foreach ( $gts_tracking_products as $gts_product ) { ?>
		<span class="gts-item">
		<?php foreach ( $gts_product as $gts_product_property => $gts_product_value ) { ?>
			<span class="gts-i-<?php echo htmlspecialchars($gts_product_property, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($gts_product_value, ENT_QUOTES, 'UTF-8'); ?></span>
		<?php } ?>
		</span>
	<?php } ?>
	</div>

	<?php // Nextopia Conversion Tracking ?>
	<form accept-charset="utf-8" style="display:none;" name="nxtpform"><textarea id="nxtpta"><?php foreach ($nxt_tracking as $nxt_tracking_line) { echo htmlspecialchars(implode('|', $nxt_tracking_line), ENT_QUOTES, 'UTF-8') . "\n"; } ?></textarea></form>
	<script>(function(){var a="https://analytics.nextopia.net/p.php",b=<?php echo json_encode((string) NEXTOPIA_PUBLIC_ID); ?>,c="1";info=new Image(1,1),info.src=a+"?y="+b+"&z="+c+"&x="+escape(document.nxtpform.nxtpta.value)})();</script>

	<?php // Acquisio Conversion Tracking ?>
	<script>"undefined"!=typeof ATRKtracker&&null!==ATRKtracker&&(ATRKtracker.setConversionCode(<?php echo json_encode((string) $acq_tracking['ConversionCode']); ?>),ATRKtracker.setTransId(<?php echo json_encode((string) $acq_tracking['TransId']); ?>),ATRKtracker.setTotal(<?php echo json_encode((string) $acq_tracking['Total']); ?>),ATRKtracker.setCurrency(<?php echo json_encode((string) $acq_tracking['Currency']); ?>),ATRKtracker.setTax(<?php echo json_encode((string) $acq_tracking['Tax']); ?>),ATRKtracker.setShipping(<?php echo json_encode((string) $acq_tracking['Shipping']); ?>),ATRKtracker.setUnits(<?php echo json_encode((string) $acq_tracking['Units']); ?>),<?php foreach ( $acq_tracking_products as $acq_tracking_product ) { echo 'ATRKtracker.addItem(' . json_encode(implode('|', $acq_tracking_product)) . '),'; } ?>ATRKtracker.logConversion())</script>

<?php

		$objOrder->setTrackingFlag($thisorder['orders_id']);

	}

}

?>

	<?php // Google AdWords Remarketing Tag ?>
	<script>var google_conversion_id=1072500077,google_custom_params=window.google_tag_params,google_remarketing_only=!0;</script>
	<script src="//www.googleadservices.com/pagead/conversion.js"></script>
	<noscript><div style="display:inline;"><img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/1072500077/?value=0&amp;guid=ON&amp;script=0"></div></noscript>

	<?php // Microsoft AdCenter Site-Wide Tracking ?>
	<script>!function(e,n,t,a,o){var d,c,i
	e[o]=e[o]||[],d=function(){var n={ti:"4028827"};n.q=e[o],e[o]=new UET(n),e[o].push("pageLoad")},c=n.createElement(t),c.src=a,c.async=1,c.onload=c.onreadystatechange=function(){var e=this.readyState;e&&"loaded"!==e&&"complete"!==e||(d(),c.onload=c.onreadystatechange=null)},i=n.getElementsByTagName(t)[0],i.parentNode.insertBefore(c,i)}(window,document,"script","//bat.bing.com/bat.js","uetq");</script>
	<noscript><img src="//bat.bing.com/action/0?ti=4028827&amp;Ver=2" height="0" width="0" style="display:none; visibility: hidden;"></noscript>

	</body>
</html>

<?php

//Flush the buffer
ob_end_flush();