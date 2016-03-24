<?php

	global $ObjAlternatePage;

	//Some flash tool stuff
	$layout = $ObjPageProduct->getDefaultFlashToolId();
	$_SESSION['layout'] = $layout;

	$products_id = $ObjPageProduct->getId();
	$_SESSION['products_id'] = $products_id;


	if(!isset($_REQUEST['design_id'])) $_SESSION['status']='add';
	else $_SESSION['status']='edit';

	//If there is an alternate product, instantiate it
	//TODO after integrated url parameters remove getAlternateBuilderProductId related function from class

   if( $ObjPageProduct->getAlternateBuilderProductId() ) {

		$ObjAlternatePage = new Page('product', $ObjPageProduct->getAlternateBuilderProductId());
		$ObjAlternatePageProduct = ProductPage::create($ObjPageProduct->getAlternateBuilderProductId());

	} else {

	$ObjAlternatePage = new Page('product', $ObjPageProduct->getAlternateFlashProductId());
	$ObjAlternatePageProduct = ProductPage::create($ObjPageProduct->getAlternateFlashProductId());

	}

	$renderBuilder = FALSE;
	$renderFlash = FALSE;

	//Logic for tool types, alternate products, and browser support
	if ($ObjPageProduct->getToolTypeName() == 'builder' || ( isset($tweakable) && isset($_GET['mode'])?$_GET['mode']:NULL == 'tweak' ) ) {

		if (isset($ObjAlternatePageProduct) && $ObjAlternatePageProduct->getToolTypeName() == 'flash') {

			if ($ObjPageProduct->isLightweight()) {

				if (!BUILDER_SUPPORTED) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
					header("Location: " . $ObjAlternatePage->getUrl());
					die();
				} else if (BUILDER_SUPPORTED) {
					showBuilderMessage();
					$renderBuilder = TRUE;
				}

			} else {

				if (!BUILDER_SUPPORTED || BUILDER_SUPPORT_PARTIAL) {
					header($_SERVER['SERVER_PROTOCOL'] . ' 302 Found', true, 302);
					header("Location: " . $ObjAlternatePage->getUrl());
					die();
				} else if (BUILDER_SUPPORTED && !BUILDER_SUPPORT_PARTIAL) {
					showBuilderMessage();
					$renderBuilder = TRUE;
				}

			}

		} else {

			if ($ObjPageProduct->isLightweight()) {

				if (!BUILDER_SUPPORTED) {
					showBrowserError();
				} else {
					$renderBuilder = TRUE;
				}

			} else {

				if (!BUILDER_SUPPORTED || BUILDER_SUPPORT_PARTIAL) {
					showBrowserError();
				} else if (BUILDER_SUPPORTED) {
					$renderBuilder = TRUE;
				}

			}

		}

	} else if ($ObjPageProduct->getToolTypeName() == 'flash') {

		if (isset($ObjAlternatePageProduct) && $ObjAlternatePageProduct->getToolTypeName() == 'builder') {

			if ($ObjAlternatePageProduct->isLightweight() == TRUE) {

				if (!BUILDER_SUPPORTED) {
					showBuilderTeaser();
					$renderFlash = TRUE;
				} else {
					showFlashMessage();
					$renderFlash = TRUE;
				}

			} else {

				if (!BUILDER_SUPPORTED || BUILDER_SUPPORT_PARTIAL) {
					$renderFlash = TRUE;
				} else if (BUILDER_SUPPORTED && !BUILDER_SUPPORT_PARTIAL) {
					showFlashMessage();
					$renderFlash = TRUE;
				}

			}

		} else {

			$renderFlash = TRUE;

		}

	}




	//Render the builder or flasht tool appropritely
	if ($renderBuilder) {

		//Load the builder
		if($tweakable){
			$builderid = $Product->getBuilderTweakToolBuilderRef();
		} elseif ($ObjPageProduct->getFlashToolBuilderId()) {
			$builderid = $ObjPageProduct->getFlashToolBuilderId();
		} else {
            $builderid = $ObjPageProduct->getDefaultBuilderRef();
        }

		$productid = PAGE_ID;

		$designid = isset($_GET['design'])? $_GET['design'] : NULL ;
	 	$tweakid = $Product->getBuilderTweakToolId();

		$productname = $ObjPageProduct->getDefaultProductName();
		$defaultsku = $ObjPageProduct->getDefaultPreconfiguredSkuId();

		$Builder = new Builder($builderid, $productname, $productid, $designid, $tweakid, FALSE, NULL, FALSE, $productStateParamters);
		echo $Builder->getHtml($ObjPageProduct->getDisclaimer());

		//Include product accessories
		//Include the material table/add to cart buttons
		echo Template::generate(
			"base-sec/builder_accessories",
			array (
				'page'           => $page,
				"ObjPageProduct" => $ObjPageProduct,
				'ObjProductAttributes' => $ObjProductAttributes,
				'ObjProductSubAttributes' => $ObjProductSubAttributes,
				'pathImgSmallProduct'     => $pathImgSmallProduct,
				'PathContentDetailTab'	  => $pathContentDetailTab,
				'PathContentMaterialsTab' => $pathContentMaterialsTab
			)
		);


	} else if ($renderFlash) {

		echo Template::generate(
			"../ac",
			array (
				'page'           => $page,
				"ObjPageProduct" => $ObjPageProduct,
				'ObjProductAttributes' => $ObjProductAttributes,
				'ObjProductSubAttributes' => $ObjProductSubAttributes,
				'ObjFlash' => $ObjFlash,
				'links' => $links,
				'pathCustomToolfont' => $pathCustomToolfont,
            	'pathCustomToolDesignsave' => $pathCustomToolDesignsave,
				'pathCustomToolclipart' => $pathCustomToolclipart
			)
		);

		//include_once($Path_Templates_Base."tutorial.php");

	}



	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// FUNCTIONS
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	function showBuilderMessage() {
		global $ObjAlternatePage;

?>
		<div class="builder-message span_16 ">
			<img src="<?php echo URL_PREFIX_HTTP."/images/new-banner.png"?>" />
			<p class="clearfix">Welcome To Our New Custom Tool! &nbsp;<span>(Looking for the old tool? <a href="<?php echo $ObjAlternatePage->getUrl(); ?>">Click here.</a>)</span>
			</p>
		</div>
<?php
	}


	function showFlashMessage() {

		global $ObjAlternatePage;
?>
		<div class="flash-message span_16 append-bottom">
			<img src="<?php print URL_PREFIX_HTTP; ?>/images/new-banner.png" />
			<p class="clearfix">
				Want To Try Our New Custom Tool? <a href="<?php echo $ObjAlternatePage->getUrl(); ?>">Click Here!</a>
			</p>
		</div>
<?php
	}


	function showBrowserError() {

		$contact_url = new Page('contact-us');
?>
		<div id="custom-tool-messages">
			<div class="builder-IE7-message span_16 prepend-top">
				<h4>Uh oh! Your browser does not support the new custom tool.</h4>
				<p>
					<?php  if (MSIE > 0) {?>
					It appears you are using Internet Explorer <?php echo MSIE;?>.
					<?php } ?>
					To use the new tool, why not <a href="http://www.whatbrowser.org/">upgrade to a modern browser</a>? </p>
				<p>By upgrading, not only will you be able to customize this item, you will have better security, load pages faster, and be able to view the web the way it's meant to be seen.</p>
				<div class="span_16 prepend-top">
					<h6>Unable to upgrade? We can help you order this item!</h6>
					<p>Simply <a href="<?php print $contact_url->getUrl();?>">contact us</a> and we can take your order.</p>
					</div>
				</div>
			</div>
		</div>
<?php
	}


	function showBuilderTeaser() {

		$contact_url = new Page('contact-us');
?>
		<div class="flash-IE7-message span_16 append-bottom">
			<p class="clearfix">Looking For Our New Custom Tool? <a href="#IE7-message" class="zoom">Click Here For More Information.</a></p>
		</div>
		<div id="IE7-message">
			<h4>Uh oh! Your browser does not support the new custom tool.</h4>
			<p><?php  if (MSIE > 0) {?>It appears you are using Internet Explorer <?php echo MSIE;?>.<?php } ?> To use the new tool, why not <a href="http://www.whatbrowser.org/">upgrade to a modern browser</a>? </p><p>By upgrading, not only will you be able to easily customize this item, you will have better security, load pages faster, and be able to view the web the way it's meant to be seen.</p>
			<div class="prepend-top">
				<h6>Unable to upgrade? We can help you order this item!</h6>
				<p>Simply <a href="<?php print $contact_url->getUrl();?>">contact us</a> and we can take your order.</p>

			</div>
		</div>
<?php
	}
?>
