<?php


//Instantiate needed classes
$objSubcategoryPage = SubcategoryPage::create(PAGE_ID);
$objLandingPage = LandingPage::create(PAGE_ID);
$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

$productRow = ProductGrid::create(PAGE_ID)->getListings();

$gridSize = $objLandingPage->getProductGridSize();
$showQuickview = $objLandingPage->getShowQuickview();
$showProductNumber = $objLandingPage->getShowProductNumber();
$perRow = 6;
$showFilter = $objLandingPage->getShowFilter();
$showSort = $objLandingPage->getShowSort();
$sortMoreThan = Settings::getSettingValue('productsorting');

$hasFilters = false;

$gridHeader = $objLandingPage->getProductGridHeader();
$gridIntro = $objLandingPage->getProductGridIntro();

$products = array();
$productBanners = array();
$productComplianceList = array();
$productComplianceChecklist = array();
$luminousChecklist = array();

//We are going to loop through all the products once, and create a list of all the filters we
//need. We will then render the proper filter bars, and then loop through the products again
//for the final listing.
foreach ( (array) $productRow as $key => $value ) {

    $singleProductBanners = array();
    $state = array();

    $tmpProduct = ProductPage::create( (int) $value['products_id'] );

    //Set boolean values for possible product flags

    //Whether or not the product is tweakable
    if ( $value['is_tweakable'] ) {

        $tweakable = TRUE;

        $singleProductBanners[] = array('Tweakable', 'tweakable');

    } else {

        if ( !$value['is_custom'] ) {
            $state['stockTool'] = 1;
        }

    }

    //Whether or not the product is multilingual
    if ( $tmpProduct->isMultiLingual() ) {

        $multilingual = TRUE;

        $singleProductBanners[] = array('Multilingual', 'multilingual');

        $state['translationFamily'] = (int) $value['translation_family_id'];

    }

    //Whether or not the product is bilingual
    if ( $tmpProduct->isBiLingual() && !$tmpProduct->isMultiLingual() ) {

        $bilingual = TRUE;

        $singleProductBanners[] = array('Bilingual', 'bilingual');

        $state['translationFamily'] = (int) $value['translation_family_id'];

    }

    //Whether or not the product is a best seller
    if ( $value['best_seller'] ) {

        $bestSeller = TRUE;

        $singleProductBanners[] = array('Best Seller', 'best-seller');

    }

    //Whether or not the product is on sale
    if ( $tmpProduct->getOnSale() ) {

        $onSale = TRUE;

    }

    //Whether or not the product is a glow-in-the-dark
    $productSkusList = explode(",", $tmpProduct->getProductSkuIds());
    $isLuminousSku = FALSE;

    if ( !empty($productSkusList) ) {

        foreach ( $productSkusList as $sku ) {

            $productSku = Sku::create($sku);

            $material = $productSku->getMaterial();

            if ( $material->isLuminous() ) {
                $isLuminousSku = TRUE;

                break;
            } else {
                continue;
            }

        }

    }

    if ( $isLuminousSku ) {

        $luminous = TRUE;

        $singleProductBanners[] = array('Glow-in-the-Dark', 'glow');

        $luminousChecklist[] = $tmpProduct->getId();

    }

    //Compliance List
    $complianceList = $tmpProduct->getProductGridDisplayCompliances();

    if ( !empty($complianceList) ) {

        foreach ( $complianceList as $compliance ) {

            if ( !in_array($compliance, $productComplianceList) ) {

                $productComplianceList[] = $compliance;

            }

            $productCompliance = $tmpProduct->getId().'_'.$compliance->getId();

            if ( !in_array($productCompliance, $productComplianceChecklist) ) {

                $productComplianceChecklist[] = $productCompliance;

            }

            if ( $compliance->getName() == 'UL® Recognized' ) {

                $singleProductBanners[] = array($compliance->getName(), 'ul-recognized');

            } else {

                $singleProductBanners[] = array($compliance->getName());

            }

        }

        $hasCompliances = TRUE;

    }

    $state['preconfiguredSku'] = (int) $value['preconfigured_sku_id'];
    $state['sourceLandingProduct'] = (int) $value['product_landing_id'];
    $state['breadcrumbLanding'] = (int) $value['landing_id'];

    if ( $value['tool_type_name'] == 'builder' || $value['is_tweakable'] ) {
        $state['easyDesignTool'] = $value['builder_id'];
    }

    if ( $value['tool_type_name'] == 'flash' ) {
        $state['advancedDesignTool'] = $value['flash_tool_id'];
    }

    if ( $value['tool_type_name'] == 'streetname' ) {
        $state['streetSignDesignTool'] = $value['streetsign_tool_id'];
    }

    $productURL = $tmpProduct->getUrl().'?s='.ProductStateParameter::encode($state);

    $productRow[$key]['productURL'] = $productURL;

    $productBanners[] = $singleProductBanners;

    $products[] = $tmpProduct;

}

if ( $tweakable == TRUE || $multilingual == TRUE || $onSale == TRUE || $bestSeller == TRUE || $bilingual == TRUE || $luminous == TRUE || $hasCompliances == TRUE) {
    $hasFilters = true;
}

$count = count($productRow);

//Get landing page top grid
$topGridListings = $objLandingPage->getListings();
$topGridListingsTotal = count($topGridListings) - 1;
$landingTopGridListings = array();
$gridListingsCount = 0;

if ( count($topGridListings) > 0 ) {

    foreach ($topGridListings as $listing) {

        $link = Page::create($listing['type'], $listing['ref_id']);
        $tmpUrl = $link->getUrl();

        $listing['link'] = $tmpUrl;

        $landingTopGridListings[] = $listing;
    }

}

$templateInfo = array (
    'page'                          => $page,
    'order'                         => $order,
    'objOrder'                      => $objOrder,
    'chatStatus'                    => $chatStatus,
    'breadcrumbs'                   => $page->getBreadCrumbs(),
    'result'                        => $result,
    'URL_PREFIX'                    => URL_PREFIX,
    'URL_PREFIX_HTTP'               => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'              => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                    => ENT_QUOTES,
    'pageType'                      => PAGE_TYPE,
    'pageId'                        => PAGE_ID,
    'HTML_COMMENTS'                 => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'            => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID'      => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'              => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'       => CUSTOM_IMAGE_URL_PREFIX,
    'website'                       => website,
    'links'                         => $links,
    'ObjShoppingCart'               => $ObjShoppingCart,
    'ObjMenu'                       => $ObjMenu,
    'ObjPageProduct'                => $ObjPageProduct,
    'FrontEndTemplateIncluder'      => $FrontEndTemplateIncluder,
    'objLandingPage'                => $objLandingPage,
    'objSubcategoryPage'            => $objSubcategoryPage,
    'Path_Templates_Base'           => $Path_Templates_Base,
    'productRow'                    => $productRow,
    'detailedSectionProducts'       => $detailedSectionProducts,
    'detailedSections'              => $detailedSections,
    'gridSize'                      => $gridSize,
    'showQuickview'                 => $showQuickview,
    'showProductNumber'             => $showProductNumber,
    'perRow'                        => $perRow,
    'showFilter'                    => $showFilter,
    'showSort'                      => $showSort,
    'multilingual'                  => $multilingual,
    'bilingual'                     => $bilingual,
    'tweakable'                     => $tweakable,
    'bestSeller'                    => $bestSeller,
    'onSale'                        => $onSale,
    'luminous'                      => $luminous,
    'productComplianceList'         => $productComplianceList,
    'productComplianceChecklist'    => $productComplianceChecklist,
    'luminousChecklist'             => $luminousChecklist,
    'gridHeader'                    => $gridHeader,
    'gridIntro'                     => $gridIntro,
    'products'                      => $products,
    'detailedProducts'              => $detailedProducts,
    'productBanners'                => $productBanners,
    'sortMoreThan'                  => $sortMoreThan,
    'hasFilters'                    => $hasFilters,
    'count'                         => $count,
    'showDetailsDiv'                => $showDetailsDiv,
    'showFeatureDiv'                => $showFeatureDiv,
    'multipleGrids'                 => $multipleGrids,
    'landingTopGridListings'        => $landingTopGridListings,
    'topGridListingsTotal'          => $topGridListingsTotal,
    'gridListingsCount'             => $gridListingsCount
);

if (!empty($resultsOfGlobalController)) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string)$name] = $value;
    }
}

echo Template::generate('base-sec/landing-template', $templateInfo);