<!DOCTYPE html>
<?php

	/**
	 * head.php is included on every page, and outputs the doctype through to the closing </head>
	 *
	 * This file is meant to be unbalanced, as the html tag is closed in foot.php
	 *
	 * @author  Jason Hodulik  <jasonh@brimar.com>
	 * @author  Daniel Hennion <daniel@brimar.com>
	 * @since   09.28.2012
	 */

	//Start the output buffer, and flush it in the foot
	ob_start();

?>

<!--[if lt IE 7]> <html class="no-js lt-ie10 lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie10 lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie10 lt-ie9" lang="en"> <![endif]-->
<!--[if IE 9]> <html class="no-js lt-ie10" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
	<meta charset="utf-8">
	<script>var _sf_startpt=(new Date).getTime();</script>

	<title><?php print htmlspecialchars($page->getTitle(), ENT_QUOTES, 'UTF-8'); ?></title>

	<?php if ($page->getMetaDescription()) { ?><meta name="description" content="<?php print htmlspecialchars($page->getMetaDescription(), ENT_QUOTES, 'UTF-8'); ?>"><?php } ?>
	<?php if ($page->getMetaKeywords()) { ?><meta name="keywords" content="<?php print htmlspecialchars($page->getMetaKeywords(), ENT_QUOTES, 'UTF-8'); ?>"><?php } ?>
	<?php if ($page->getCanonicalUrl()) { ?><link rel="canonical" href="<?php print htmlspecialchars($page->getCanonicalUrl(), ENT_QUOTES, 'UTF-8');?>"><?php } ?>


	<link rel="stylesheet" href="<?php echo URL_PREFIX; ?>/styles/blueprint/screen.20130820.css">
	<link rel="stylesheet" href="<?php echo URL_PREFIX; ?>/styles/global.css?20152804842">
<?php
	//If this is a product page, we have a few more things to load and a few conditions to check
	if (PAGE_TYPE == 'product') {

		if($ObjPageProduct->getToolTypeName() == 'streetname') {
?>
			<link rel="stylesheet" href="<?php print URL_PREFIX;?>/styles/streetname.20130820.css" />
<?php
		}

		if($ObjPageProduct->getToolTypeName() == 'builder' || $ObjPageProduct->getTweakId() > 0 ) {
?>
			<link rel="stylesheet" href="<?php print URL_PREFIX;?>/styles/builder.20150423.css">
			<link rel="stylesheet" href="<?php print URL_PREFIX;?>/styles/builderfonts.20130820.css" />
<?php
		}
	}
?>
		<script src="<?php print URL_PREFIX;?>/scripts/modernizr.20130820.js"></script>

</head>

<body itemscope="itemscope" itemtype="http://schema.org/WebPage" class="<?php echo ( isset($chatStatus['company']) && $chatStatus['company'] ? 'chat-available' : 'chat-unavailable' ); ?>">

<?php if (HTML_COMMENTS) {?><!-- END HEAD --><?php } ?>