<?php
	// Handlebars templates
	$FrontEndTemplateIncluder->addHandlebarsTemplate('cartCommunicationError');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoadingMessage');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('freightShipmentNotice');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('inventoryAlert');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('cartCommunicationError');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderError');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderLoading');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderResults');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoadingMessage');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartConflict');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartError');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartLoading');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('saveCartSuccess');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDeleteConfirmation');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDeleting');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetails');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetailsErrorMessage');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartDetailsLoadingMessage');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartError');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartLoading');
	$FrontEndTemplateIncluder->addHandlebarsTemplate('savedCartMergeConflict');


	//Include the template files
	include_once($PathTemplates."head.php");
		include_once($PathTemplates."header.php");
		include_once($PathTemplates."notices.php");
		include_once($PathTemplates."breadcrumbs.php");
			include_once($PathTemplates."openwrap.php");
				include($Path_Templates_MyAccount."savedcarts.php");
			include_once($PathTemplates."closewrap.php");
			include_once($PathTemplates."header-content.php");
		include_once($PathTemplates."footer.php");
	include_once($PathTemplates."foot.php");
?>
