<?php


include_once( $Path_Templates_Base."geotarget_state_list.php" );

//Instantiate needed classes
$objGeotargetPage   = GeotargetPage::create($page->getId());
$objSubcategoryPage = SubcategoryPage::create($objGeotargetPage->getSubcategoryId());
$objCategoryPage    = CategoryPage::create($objSubcategoryPage->getCategoryId());

$geoId = $objGeotargetPage->getId();

$geoSubRow = ProductGrid::create(PAGE_ID, $geoId, $objGeotargetPage->getTarget());

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

//Get products for the current geotarget
$productRow = $geoSubRow->getListings();

//Get federal signs
$federalSigns = ProductGrid::create($objGeotargetPage->getSubcategoryId(), null, null, true)->getListings();

//Geotarget States List
$flag = 0;

//Get the current page zone, and a list of all zones
$zoneId = (PAGE_TYPE == 'subcategory' ? PAGE_ID : $objGeotargetPage->getSubcategoryId());
$link = new Page('subcategory', $zoneId);
$subcategoryLink = $link->getUrl();
$geoZones = $objGeotargetPage->getGeotargetList($zoneId);
$totalZones = count($geoZones);

//If there are 15 or less zones uses a three column layout
if ( $totalZones <= 15 ) {

    //Number of zones in each column
    $perColumn = ceil($totalZones / 3);

    //More than 15 zones uses a five column layout
} else {

    //Number of zones in each column
    $perColumn = ceil($totalZones / 5);

    //Special case for numbers that break the column logic (16 is the only confirmed case)
    if ( ceil($totalZones / $perColumn) < 5 ) {
        $flag = 1;
    }

}

$gridSize = $objSubcategoryPage->getGridSize();
$showQuickview = $objSubcategoryPage->getShowQuickview();
$showProductNumber = $objSubcategoryPage->getShowProductNumber();
$perRow = 5;
$showFilter = $objSubcategoryPage->getShowFilter();
$showSort = $objSubcategoryPage->getShowSort();
$sortMoreThan = Settings::getSettingValue('productsorting');

$hasFilters = false;
$federalHasFilters = false;

$gridHeader = SubcategoryPage::create($geoId, true)->getGeoGridHeader();
$gridIntro = SubcategoryPage::create($geoId, true)->getGeoGridIntro();

$products = array();
$federalProducts = array();
$productBanners = array();
$federalProductBanners = array();
$productComplianceList = array();
$luminousChecklist = array();
$productComplianceChecklist = array();
$federalProductComplianceList = array();
$federalProductComplianceChecklist = array();
$federalLuminousChecklist = array();

//We are going to loop through all the products once, and create a list of all the filters we
//need. We will then render the proper filter bars, and then loop through the products again
//for the final listing.
foreach ( (array) $productRow as $key => $value ) {

    $singleProductBanners = array();
    $state = array();

    $tmpProduct = ProductPage::create( (int) $value['products_id'] );

    //Set boolean values for possible product flags

    //Whether or not the product is tweakable
    if ( $value['is_tweakable']  ) {

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
    if ( $value['best_seller']  ) {

        $bestSeller = TRUE;

        $singleProductBanners[] = array('Best Seller', 'best-seller');

    }

    //Whether or not the product is on on sale
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
    $state['sourceSubcategoryProduct'] = (int) $value['product_subcategory_id'];
    $state['breadcrumbSubcategory'] = (int) $value['subcategory_id'];

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

if ( $tweakable == TRUE || $multilingual == TRUE || $onSale == TRUE || $bestSeller == TRUE || $bilingual == TRUE || $luminous == TRUE || $hasCompliances == TRUE ) {
    $hasFilters = true;
}

$federalMultilingual = FALSE;
$federalTweakable = FALSE;
$federalBestSeller = FALSE;
$federalOnSale = FALSE;
$federalHasCompliances = FALSE;

//We are going to loop through all the products once, and create a list of all the filters we
//need. We will then render the proper filter bars, and then loop through the products again
//for the final listing.
foreach ( (array) $federalSigns as $key => $value ) {

    $singleProductBanners = array();
    $state = array();

    $tmpProduct = ProductPage::create( (int) $value['products_id'] );

    //Set boolean values for possible product flags

    //Whether or not the product is tweakable
    if ( $value['is_tweakable'] ) {

        $federalTweakable = TRUE;

        $singleProductBanners[] = array('Tweakable', 'tweakable');

    } else {

        if ( !$value['is_custom'] ) {
            $state['stockTool'] = 1;
        }

    }

    //Whether or not the product is multilingual
    if ( $tmpProduct->isMultiLingual() ) {

        $federalMultilingual = TRUE;

        $singleProductBanners[] = array('Multilingual', 'multilingual');

        $state['translationFamily'] = (int) $value['translation_family_id'];

    }

    //Whether or not the product is bilingual
    if ( $tmpProduct->isBiLingual() && !$tmpProduct->isMultiLingual() ) {

        $federalBilingual = TRUE;

        $singleProductBanners[] = array('Bilingual', 'bilingual');

        $state['translationFamily'] = (int) $value['translation_family_id'];

    }

    //Whether or not the product is a best seller
    if ( $value['best_seller'] ) {

        $federalBestSeller = TRUE;

        $singleProductBanners[] = array('Best Seller', 'best-seller');

    }

    //Whether or not the product is on sale
    if ( $tmpProduct->getOnSale() ) {

        $federalOnSale = TRUE;

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

        $federalLuminous = TRUE;

        $singleProductBanners[] = array('Glow-in-the-Dark', 'glow');

        $federalLuminousChecklist[] = $tmpProduct->getId();

    }

    //Compliance List
    $complianceList = $tmpProduct->getProductGridDisplayCompliances();

    if ( !empty($complianceList) ) {

        foreach ( $complianceList as $compliance ) {

            if ( !in_array($compliance, $federalProductComplianceList) ) {

                $federalProductComplianceList[] = $compliance;

            }

            $productCompliance = $tmpProduct->getId().'_'.$compliance->getId();

            if ( !in_array($productCompliance, $federalProductComplianceChecklist) ) {

                $federalProductComplianceChecklist[] = $productCompliance;

            }

            if ( $compliance->getName() == 'UL® Recognized' ) {

                $singleProductBanners[] = array($compliance->getName(), 'ul-recognized');

            } else {

                $singleProductBanners[] = array($compliance->getName());

            }

        }

        $federalHasCompliances = TRUE;

    }

    $state['preconfiguredSku'] = (int) $value['preconfigured_sku_id'];
    $state['sourceSubcategoryProduct'] = (int) $value['product_subcategory_id'];
    $state['breadcrumbSubcategory'] = (int) $value['subcategory_id'];

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

    $federalSigns[$key]['productURL'] = $productURL;

    $federalProductBanners[] = $singleProductBanners;

    $federalProducts[] = $tmpProduct;

}

if ( $federalTweakable == TRUE || $federalMultilingual == TRUE || $federalOnSale == TRUE || $federalBestSeller == TRUE || $federalBilingual == TRUE || $federalLuminous == TRUE || $federalHasCompliances == TRUE ) {
    $federalHasFilters = true;
}

$count = count($productRow);

$firstGrid = true;

if ( count($federalSigns) == 0 )
    $lastGrid = true;
else
    $lastGrid = false;

$templateInfo = array (
    'page'                              => $page,
    'order'                             => $order,
    'objOrder'                          => $objOrder,
    'chatStatus'                        => $chatStatus,
    'breadcrumbs'                       => $page->getBreadCrumbs(),
    'result'                            => $result,
    'URL_PREFIX'                        => URL_PREFIX,
    'URL_PREFIX_HTTP'                   => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'                  => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                        => ENT_QUOTES,
    'pageType'                          => PAGE_TYPE,
    'pageId'                            => PAGE_ID,
    'HTML_COMMENTS'                     => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'                => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID'          => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'                  => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'           => CUSTOM_IMAGE_URL_PREFIX,
    'website'                           => website,
    'links'                             => $links,
    'ObjShoppingCart'                   => $ObjShoppingCart,
    'ObjMenu'                           => $ObjMenu,
    'ObjPageProduct'                    => $ObjPageProduct,
    'FrontEndTemplateIncluder'          => $FrontEndTemplateIncluder,
    'objCategoryPage'                   => $objCategoryPage,
    'Path_Templates_Base'               => $Path_Templates_Base,
    'objSubcategoryPage'                => $objSubcategoryPage,
    'objGeotargetPage'                  => $objGeotargetPage,
    'productRow'                        => $productRow,
    'federalSigns'                      => $federalSigns,
    'federalProducts'                   => $federalProducts,
    'gridSize'                          => $gridSize,
    'showQuickview'                     => $showQuickview,
    'showProductNumber'                 => $showProductNumber,
    'perRow'                            => $perRow,
    'showFilter'                        => $showFilter,
    'showSort'                          => $showSort,
    'sortMoreThan'                      => $sortMoreThan,
    'count'                             => $count,
    'hasFilters'                        => $hasFilters,
    'federalHasFilters'                 => $federalHasFilters,
    'multilingual'                      => $multilingual,
    'bilingual'                         => $bilingual,
    'tweakable'                         => $tweakable,
    'bestSeller'                        => $bestSeller,
    'onSale'                            => $onSale,
    'luminous'                          => $luminous,
    'productComplianceList'             => $productComplianceList,
    'productComplianceChecklist'        => $productComplianceChecklist,
    'luminousChecklist'                 => $luminousChecklist,
    'federalMultilingual'               => $federalMultilingual,
    'federalBilingual'                  => $federalBilingual,
    'federalTweakable'                  => $federalTweakable,
    'federalBestSeller'                 => $federalBestSeller,
    'federalOnSale'                     => $federalOnSale,
    'federalLuminous'                   => $federalLuminous,
    'productBanners'                    => $productBanners,
    'federalProductBanners'             => $federalProductBanners,
    'federalProductComplianceList'      => $federalProductComplianceList,
    'federalProductComplianceChecklist' => $federalProductComplianceChecklist,
    'federalLuminousChecklist'          => $federalLuminousChecklist,
    'gridHeader'                        => $gridHeader,
    'gridIntro'                         => $gridIntro,
    'products'                          => $products,
    'totalZones'                        => $totalZones,
    'firstGrid'                         => $firstGrid,
    'lastGrid'                          => $lastGrid
);

if( !empty($resultsOfGlobalController) ) {

    foreach($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string)$name] = $value;
    }
}

echo Template::generate('base-sec/geotarget-template', $templateInfo);