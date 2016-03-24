<?php
# Global controller
session_start();

mb_internal_encoding('UTF-8');

$Path = dirname(__FILE__)."/include/";
$msie = NULL;


//include_once($Path."config.php");

////////////////////////////////////////////////////////////////////////////////////////////////////
//  PATH DEFINITIONS
////////////////////////////////////////////////////////////////////////////////////////////////////

$livesitedir = ".safetysign.com/";

	$resultsOfGlobalController = array(
		"websitedir"                      => "/",
		"livesitedir"                     => $livesitedir,
		"PathClasses"                     => APP_ROOT."/include/classes/",
		"PathTemplates"                   => APP_ROOT."/templates/global/",
		"Path_Templates_Base"             => APP_ROOT."/templates/base-sec/",
		"Path_Templates_Shopping_Cart"    => APP_ROOT."/templates/shoppingcart-sec/",
		"Path_Templates_Signin"           => APP_ROOT."/templates/signin-sec/",
		"Path_Templates_Checkout"         => APP_ROOT."/templates/checkout-sec/",
		"Path_Templates_MyAccount"        => APP_ROOT."/templates/myaccount-sec/",
		"Path_Templates_Help"             => APP_ROOT."/templates/help-sec/",
		"Path_Templates_Tracking"         => APP_ROOT."/templates/tracking-sec/",
		"Path_Templates_Promo"            => APP_ROOT."/templates/promo-sec/",
		"Path_process"                    => APP_ROOT."/process/",
		"Path_Templates_Detail_Content"   => APP_ROOT."/templates/detailtab-sec/",
		"Path_Templates_Ups"              => APP_ROOT."/templates/ups-sec/",
		"Path_Root"                       => "",
		"Path_Img"                        => website."images/",
		"Path_Assets"                     => APP_ROOT."/assets/",
		"Path_Img_catalog"                => website."images/catlog/",
		"Path_Img_main_category"          => website."images/catlog/main_category/",
		"Path_Img_seo_landing"            => website."images/catlog/seo_landing/",
		"Path_Img_category"               => website."images/catlog/category/",
		"Path_Img_product"                => website."images/catlog/product/",
		"Path_Img_Font_product"           => WEBSITE_DIR."content/",
		"Path_Img_Small_product"          => WEBSITE_DIR."images/catlog/product/small/",
		"Path_Img_Small_product_live"     => $livesitedir."images/catlog/product/small/",
		"Path_Img_Medium_product"         => website."images/catlog/product/medium/",
		"Path_Img_Large_product"          => website."images/catlog/product/large/",
		"Path_Img_FrontDrop_product"      => website."images/catlog/product/frontdrop/",
		"Path_Img_Swf_product"            => website."images/catlog/product/swf/",
		"Path_Custom_Tool_Design_save"    => website."design/save/",
		"Path_Custom_Tool_Design_zips"    => website."design/save/zips/",
		"Path_Custom_Tool_font"           => website."images/fonts/",
		"Path_Custom_Tool_clipart"        => website."images/clipart/",
		"Path_Custom_Tool_color"          => website."images/colors/",
		"Path_Img_Custom_Small_product"   => "design/save/previews/small/",
		"Path_Img_Custom_Medium_product"  => "design/save/previews/medium/",
		"Path_Img_Custom_Large_product"   => "design/save/previews/",
		"Path_Content_Detail_Tab"         => APP_ROOT."/content/detailtab/",
		"pathBuilderMaterialDialog"       => APP_ROOT."/static/builder-material-dialogs/",
		"PathContentMaterialsTab"         => APP_ROOT."/content/material-tabs/",
		"PathComplianceDialog"            => APP_ROOT."/static/compliance-tabs/",
		"Path_Content_Materials_Tab"      => APP_ROOT."/content/materialstab/",
		"Path_Img_Custom_zip_pdf"         => "design/save/zips/",
		"Path_Img_tips"                   => website."images/tips/",
		"Path_Featured_Product"           => APP_ROOT."/content/featuredproduct/",
		"Path_Related_Categories_Product" => APP_ROOT."/content/relatedcategories/",
		"Path_logs"                       => APP_ROOT."/logs/",
		"limit_subdomain"                 => "4",
		"ObjConnection"                   => NULL,
	);

	if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {

		$resultsOfGlobalController["http_subdomain"] = "https://images";
	} else {

        $resultsOfGlobalController["http_subdomain"] = "http://images";
	}

	$resultsOfGlobalController["main_category_subdomain_path"] = ".safetysign.com/images/catlog/main_category/";
	$resultsOfGlobalController["product_small_subdomain_path"] = ".safetysign.com/images/catlog/product/small/";



////////////////////////////////////////////////////////////////////////////////////////////////////
//  EMAIL DEFINITIONS
////////////////////////////////////////////////////////////////////////////////////////////////////
/*
	define("Forgot_Password_Email","sales@safetysign.com",true);
	define("Order_Email","orders@safetysign.com",true);
	define("Web_Sales_Email","sales@brimar.com",true);
	define("Customer_Service_Email","sales@brimar.com",true);
	define("Billing_Credit_Email","Joanna@brimar.com",true);
	define("Return_Email","sales@brimar.com",true);
	define("Survey_Email","mery@brimar.com",true);
	define("Common_Email","michael@brimar.com",true);
	define("Web_Sales","sales@safetysign.com",true);
	define("Web_Master","jasonh@brimar.com",true);

////////////////////////////////////////////////////////////////////////////////////////////////////
//  CONSTANT DEFINITIONS
////////////////////////////////////////////////////////////////////////////////////////////////////

	define("PathTemplatesCompanylogo","images/companylogo/",true);
	define("Path_Ada_State","/images/state/",true);
	define("File_Label","File",true);
	define("customcopy1_label","Custom Copy1",true);
	define("customcopy2_label","Custom Copy2",true);
	define("streetnum_label","Street Num",true);
	define("mountingoption_label","Mounting Option",true);
	define("background_label","Background",true);
	define("prefix_label","Prefix",true);
	define("suffix_label","Suffix",true);
	define("leftarrow_label","Left Arrow",true);
	define("rightarrow_label","Right Arrow",true);
	define("position_label","Position",true);
	define("color_label","Color",true);
	define("font_label","Font",true);
	define("fontcolor_label","Font Color",true);
	define("customcopy1size_label","Costom Copy1 Size",true);
	define("Custom_Copy2_Size","Costom Copy2 Size",true);
	define("arrow_label","Arrow",true);
	define("arrowcolor_label","Arrow Color",true);
	define("imageposition_label","Image Position",true);
	define("arrowposition_label","Arrow Position",true);
	define("upsservice_name","UPS Ground(Manual)",true);
	define("upsservice_charge","10.00",true);*/

////////////////////////////////////////////////////////////////////////////////////////////////////
//  CLASS INSTANTIATION
////////////////////////////////////////////////////////////////////////////////////////////////////
	//include_once($resultsOfGlobalController['PathClasses']."class_Menu.inc.php");
	//include_once($PathClasses."class_shipping_charges.inc.php");
	//require $PathClasses."class_custom_product.inc.php";
	//require $PathClasses."class_user.inc.php";
	//require $PathClasses."class_streetsign.inc.php";
	//require $PathClasses."class_product_attributes.inc.php";
	//require($PathClasses."class_product_sub_attributes.inc.php");
	//require $PathClasses."class_session.inc.php";
	//include $PathClasses."class_countries.inc.php";
	//require $PathClasses."class_flash.inc.php";
	//require_once $resultsOfGlobalController['PathClasses'].'class_FrontEndTemplateIncluder.inc.php';



	// Get the chat status.
	$resultsOfGlobalController['chat'] = new Chat();
	$resultsOfGlobalController['chatStatus'] = $resultsOfGlobalController['chat']->getStatus();

	if( !($resultsOfGlobalController['ObjConnection'] instanceof Connection) ) {

        $resultsOfGlobalController['ObjConnection'] = new Connection();
    }

    Connection::getHandle();

    $resultsOfGlobalController['ObjMenu']                   = new Menu();
	$resultsOfGlobalController['ObjShoppingCart']           = Cart::getFromSession(TRUE);
	$resultsOfGlobalController['ObjShippingCharges']        = new ShippingCharges();
	$resultsOfGlobalController['ObjCustomProduct']          = new CustomProduct();
	$resultsOfGlobalController['objUser']                   = new User();
	$resultsOfGlobalController['ObjProductAttributes']      = new ProductAttributes();
	$resultsOfGlobalController['ObjSession']                = new Session();
	$resultsOfGlobalController['objCountry']                = new Countries();
	$resultsOfGlobalController['ObjEmail']                  = new Email();
	$resultsOfGlobalController['ObjPageProduct']			= ProductPage::create(PAGE_ID);
	$resultsOfGlobalController['ObjContactUs']              = new ContactUs();
	$resultsOfGlobalController['objOrder']					= new Orders();
	$resultsOfGlobalController['FrontEndTemplateIncluder']  = new FrontEndTemplateIncluder();
    $resultsOfGlobalController['ObjFlashDesign']            = new FlashDesign();

////////////////////////////////////////////////////////////////////////////////////////////////////
//  USER SESSION AND BROWSER CAPABILITY
////////////////////////////////////////////////////////////////////////////////////////////////////

	//Session updating and checking
	$resultsOfGlobalController['ObjSession']->heartbeat(isset($page) ? $page : NULL);

	// Update bs_sessions table
	Session::updateDatabase();



	//Get the user agent
	$agent = $_SERVER['HTTP_USER_AGENT'];

	//Get the IE version if IE
	if ($pos = strpos($agent, 'MSIE')) {
		$msie = substr($agent, $pos+5, 3);
	}

	define('MSIE', ($msie > 0 ? $msie : 0));

	//Check if the user's version of IE is compatible
	if (isset($msie) && $msie > 0 && $msie < 8) {
		define('BUILDER_SUPPORTED', FALSE);
	} else {
		define('BUILDER_SUPPORTED', TRUE);
		if (isset($msie) && $msie > 0 && $msie < 9) {
			define('BUILDER_SUPPORT_PARTIAL', TRUE);
		} else {
			define('BUILDER_SUPPORT_PARTIAL', FALSE);
		}
	}



	//Instantiate all the links we will need for the header and footers
	$link_home                 = new Page('home');
	$link_account              = new Page('my-account');
	$link_tracking             = new Page('tracking');
	$link_orderhistory         = new Page('orderhistory');
	$link_signin               = new Page('sign-in');
	$link_forgotpassword       = new Page('forgotpassword');
	$link_contact              = new Page('contact-us');
	$link_cart                 = new Page('cart');
	$link_help                 = new Page('help');
	$link_help_about           = new Page('about');
	$link_help_faqs            = new Page('faqs');
	$link_help_custom_products = new Page('customizing');
	$link_help_shipping        = new Page('shipping');
	$link_privacy              = new Page('privacy-policy');
	$link_returns              = new Page('returns');
	$link_terms                = new Page('terms-conditions');
	$link_sitemap              = new Page('sitemap');
	$link_net30                = new Page('net30');
	$link_credit_app           = new Page('creditapp');
	$link_search               = new Page('search');
	$link_distributor          = new Page('distributors');
	$link_ansi_revision        = new Page('ansi-revision');
	$link_ansi_safety_labels   = new page('ansi-safety-labels');
	$link_ansi_safety_headers  = new page('ansi-safety-headers');
	$link_ansi_safety_symbols  = new page('ansi-safety-symbols');
	$link_saved_carts          = new page('savedcarts');

	//Create an array of all the links we need for the header and footers
	$links = array(
            'home'                 =>   dirname($link_home->getUrl()),
            'account'              =>   $link_account->getUrl(),
            'tracking'             =>   $link_tracking->getUrl(),
            'orderhistory'         =>   $link_orderhistory->getUrl(),
            'signin'               =>   $link_signin->getUrl(),
            'forgotpassword'       =>   $link_forgotpassword->getUrl(),
            'contact'              =>   $link_contact->getUrl(),
            'cart'                 =>   $link_cart->getUrl(),
            'help'                 =>   $link_help->getUrl(),
            'help-about'           =>   $link_help_about->getUrl(),
            'help-faqs'            =>   $link_help_faqs->getUrl(),
            'help-custom-products' =>   $link_help_custom_products->getUrl(),
            'help-shipping'        =>   $link_help_shipping->getUrl(),
            'privacy'              =>   $link_privacy->getUrl(),
            'returns'              =>   $link_returns->getUrl(),
            'terms'                =>   $link_terms->getUrl(),
            'sitemap'              =>   $link_sitemap->getUrl(),
            'net30'                =>   $link_net30->getUrl(),
            'creditapp'            =>   $link_credit_app->getUrl(),
            'search'               =>   $link_search->getUrl(),
            'distributor'          =>   $link_distributor->getUrl(),
            'ansi_revision'        =>   $link_ansi_revision->getUrl(),
            'ansi_safety_labels'   =>   $link_ansi_safety_labels->getUrl(),
            'ansi_safety_headers'  =>   $link_ansi_safety_headers->getUrl(),
            'ansi_safety_symbols'  =>   $link_ansi_safety_symbols->getUrl(),
            'savedcarts'           =>   $link_saved_carts->getUrl());

	$resultsOfGlobalController["links"] = $links;