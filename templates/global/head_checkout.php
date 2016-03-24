<?php

ob_start();

?><!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie10 lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie10 lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie10 lt-ie9" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html class="no-js" lang="en"><!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<script>var _sf_startpt=(new Date).getTime();</script>
		<title><?php print htmlspecialchars($page->getTitle(), ENT_QUOTES, 'UTF-8'); ?></title>
		<?php if ($page->getMetaDescription()) { ?><meta name="description" content="<?php print htmlspecialchars($page->getMetaDescription(), ENT_QUOTES, 'UTF-8'); ?>"><?php } ?>
		<?php if ($page->getMetaKeywords()) { ?><meta name="keywords" content="<?php print htmlspecialchars($page->getMetaKeywords(), ENT_QUOTES, 'UTF-8'); ?>"><?php } ?>

		<?php // Styles ?>
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/blueprint/screen.20130820.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/styles-new.20140718.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/landing.20130820.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/checkout.20140904.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/formalize.20130820.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/fancybox.20130820.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/custom-product.20130820.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/custom-thumbnails.20130820.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/adabystate-product-list.20130820.css" />
		<link rel="stylesheet" href="<?php print $websitedir;?>styles/checkout/product-browse-template-one.20130820.css" />
		<style>#___plusone_0 { position: absolute !important; }</style>
		<!--[if IE]><link rel="stylesheet" href="<?php print $websitedir;?>styles/blueprint/ie.20130820.css" /><![endif]-->

		<?php // Canonical ?>
		<?php if ($page->getCanonicalUrl()) { ?><link rel="canonical" href="<?php print htmlspecialchars($page->getCanonicalUrl(), ENT_QUOTES, 'UTF-8');?>"><?php } ?>

		<?php // Scripts ?>
		<script src="<?php print $websitedir; ?>scripts/checkout/modernizr.20130820.js"></script>
		<script src="<?php print $websitedir; ?>scripts/checkout/LAB.min.20130820.js"></script>
		<script>var _sf_startpt=(new Date()).getTime()</script>

		<?php // More scripts, via LAB on the checkout or regularly elsewhere

		?>
		 <script>
		$LAB.setOptions({AlwaysPreserveOrder:true})
			.script("<?php print $websitedir; ?>scripts/checkout/fallback_for_cdn/jquery.min.20130820.js").wait()
			.script("<?php print $websitedir; ?>scripts/checkout/fallback_for_cdn/jquery-ui-1.8.20.custom.min.20130820.js")
			.script("<?php print $websitedir; ?>scripts/checkout/jquery.validate.min.20130820.js")
			.script("<?php print $websitedir; ?>scripts/checkout/search-autocomplete.20130820.js")
			.script("<?php print $websitedir; ?>scripts/checkout/browserTouchSupport.20130820.js")
			.script("<?php print $websitedir; ?>min/?g=js")
			.script("<?php print $websitedir; ?>scripts/checkout/jquery.autotab-1.1b.20130820.js")
			.script("<?php print $websitedir; ?>scripts/checkout/global.20140904.js")
			.script("<?php print $websitedir; ?>scripts/checkout/new-checkout.20150102.js")
	</script>


	</head>
