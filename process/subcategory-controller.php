<?php


//Instantiate necessary classes to pass on to template
$objSubcategoryPage = SubcategoryPage::create(PAGE_ID);

$productRow = ProductGrid::create(PAGE_ID)->getListings();

$objGeotargetPage = GeotargetPage::create($objSubcategoryPage->getGeoTargetSubcategoryId());

$objGroupingPage = GroupingPage::create($objSubcategoryPage->getGroupingId());
$objCategoryPage = CategoryPage::create($objGroupingPage->getCategoryId());
$objLandingPage  = LandingPage::create(PAGE_ID);

$objOrder = new Orders();

$order = $objOrder->getCustomerOrder($orderno);

//Geotarget States List
$flag = 0;

//Get the current page zone, and a list of all zones
$zoneId = PAGE_ID;
$link = Page::create('subcategory', $zoneId);
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

$zonesList = array();

//List of zones

//Loop through all the zones
foreach ( $geoZones AS $zone ) {

    //Instantiate a new page for the current zone
    $link = Page::create('geotarget', $zone['id']);

    $arr = array();

    $arr['link'] = $link->getUrl();
    $arr['zone_name'] = $zone['zone_name'];

    $zonesList[] = $arr;

}

$gridSize = $objSubcategoryPage->getGridSize();
$showQuickview = $objSubcategoryPage->getShowQuickview();
$showProductNumber = $objSubcategoryPage->getShowProductNumber();
$perRow = 5;
$showFilter = $objSubcategoryPage->getShowFilter();
$showSort = $objSubcategoryPage->getShowSort();
$sortMoreThan = Settings::getSettingValue('productsorting');

$detailedHasFilters = array();
$showDetailsDiv = array();
$showFeatureDiv = array();

$gridHeader = $objSubcategoryPage->getGridHeader();
$gridIntro = $objSubcategoryPage->getGridIntro();

$detailed = array();
$detailedProducts = array();
$detailedSections = array();
$detailedSectionProducts = array();
$detailedSecProducts = array();
$detailedMultilingual = array();
$detailedBilingual = array();
$detailedTweakable = array();
$detailedBestSeller = array();
$detailedOnSale = array();
$detailedLuminous = array();
$detailedProductBanners = array();
$detailedProductComplianceList = array();
$detailedProductComplianceChecklist = array();
$detailedLuminousChecklist = array();

$i = 0;

//Check if there are detailed sections
if ( $objSubcategoryPage->getSubcategoryDetail() ) {

    $detailed = ProductGrid::create( PAGE_ID, null, null, false, true )->getListings();
    $detailedProducts = ProductGrid::create( PAGE_ID, null, null, false, false, true )->getListings();

    //Loop through each detailed section
    foreach( $detailed AS $key => $detailedSection ) {

        //Whether or not to display the details div
        $showDetailsDiv[] = ((!empty($detailedSection['grid_subhead']) || !empty($detailedSection['description']) || (!empty($detailedSection['more_info_text']) && !empty($detailedSection['more_info_href']))) && $detailedSection['per_row'] < 4 ? TRUE : FALSE);

        //Whether or not to display the features div
        $showFeatureDiv[] = (end($showDetailsDiv) || !empty($detailedSection['image']) ? TRUE : FALSE);

        //Clear out any products from previous grids
        $sectionProducts = NULL;

        //Loop through products and only grab the ones for that detailed section
        foreach ($detailedProducts AS $product) {

            //Check to see if this product goes in this section
            if ($product['subcategory_detailed_id'] == $detailedSection['products_id']) {

                $singleProductBanners = array();
                $state = array();

                $tmpProduct = ProductPage::create((int)$product['products_id']);

                //Set boolean values for possible product flags

                //Whether or not the product is a best seller
                if ( $product['best_seller'] ) {

                    $detailedBestSeller[$i][] = TRUE;

                    $singleProductBanners[] = array('Best Seller', 'best-seller');

                }

                //Whether or not the  product is tweakable
                if ( $product['is_tweakable'] ) {

                    $detailedTweakable[$i][] = TRUE;

                    $singleProductBanners[] = array('Tweakable', 'tweakable');

                } else {

                    if ( !$product['is_custom'] ) {
                        $state['stockTool'] = 1;
                    }

                }

                //Whether or not the product is multilingual
                if ( $tmpProduct->isMultiLingual() ) {

                    $detailedMultilingual[$i][] = TRUE;

                    $singleProductBanners[] = array('Multilingual', 'multilingual');

                    $state['translationFamily'] = (int) $product['translation_family_id'];

                }

                //Whether or not the  product is bilingual
                if ( $tmpProduct->isBiLingual() && !$tmpProduct->isMultiLingual() ) {

                    $detailedBilingual[$i][] = TRUE;

                    $singleProductBanners[] = array('Bilingual', 'bilingual');

                    $state['translationFamily'] = (int) $product['translation_family_id'];

                }

                //Whether or not the product is on on sale
                if ( $tmpProduct->getOnSale() ) {

                    $detailedOnSale[$i][] = TRUE;

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

                    $detailedLuminous[$i][] = TRUE;

                    $singleProductBanners[] = array('Glow-in-the-Dark', 'glow');

                    $detailedLuminousChecklist[$i][] = $tmpProduct->getId();

                }

                //Compliance List
                $complianceList = $tmpProduct->getProductGridDisplayCompliances();

                if ( !empty($complianceList) ) {

                    foreach ( $complianceList as $compliance ) {

                        if ( !in_array($compliance, $detailedProductComplianceList[$i]) ) {

                            $detailedProductComplianceList[$i][] = $compliance;

                        }

                        $productCompliance = $tmpProduct->getId().'_'.$compliance->getId();

                        if ( !in_array($productCompliance, $detailedProductComplianceChecklist[$i]) ) {

                            $detailedProductComplianceChecklist[$i][] = $productCompliance;

                        }

                        if ( $compliance->getName() == 'UL® Recognized' ) {

                            $singleProductBanners[] = array($compliance->getName(), 'ul-recognized');

                        } else {

                            $singleProductBanners[] = array($compliance->getName());

                        }

                    }

                }

                $state['preconfiguredSku'] = (int) $product['preconfigured_sku_id'];
                $state['sourceSubcategoryProduct'] = (int) $product['product_subcategory_id'];
                $state['breadcrumbSubcategory'] = (int) $product['subcategory_id'];
                $state['streetSignDesignTool'] = 1;

                if ( $product['tool_type_name'] == 'builder' || $product['is_tweakable'] ) {
                    $state['easyDesignTool'] = $product['builder_id'];
                }

                if ( $product['tool_type_name'] == 'flash' ) {
                    $state['advancedDesignTool'] = $product['flash_tool_id'];
                }

                if ( $product['tool_type_name'] == 'streetname' ) {
                    $state['streetSignDesignTool'] = $product['streetsign_tool_id'];
                }

                $productURL = $tmpProduct->getUrl().'?s='.ProductStateParameter::encode($state);

                $product['productURL'] = $productURL;

                $sectionProducts[] = $product;

                if ( count($detailedTweakable[$i]) > 0 || count($detailedMultilingual[$i]) > 0 || count($detailedOnSale[$i]) > 0 || count($detailedBestSeller[$i]) > 0 || count($detailedBilingual[$i]) > 0 ||
                    count($detailedLuminous[$i]) > 0  || count($detailedProductComplianceList[$i]) > 0 ) {
                    $detailedHasFilters[$i][] = TRUE;
                }

                $detailedProductBanners[$i][] = $singleProductBanners;

                $detailedSecProducts[$i][] = $tmpProduct;

            }

        }

        $detailedSectionProducts[] = $sectionProducts;

        $detailedSections[] = $detailedSection;

        $i++;

    }

}

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

    //Whether or not the product is a best seller
    if ( $value['best_seller'] ) {

        $bestSeller = TRUE;

        $singleProductBanners[] = array('Best Seller', 'best-seller');

    }

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

if ( $tweakable == TRUE || $multilingual == TRUE || $onSale == TRUE || $bestSeller == TRUE || $bilingual == TRUE || $luminous || $hasCompliances == TRUE ) {
    $hasFilters = TRUE;
}

$count = count($productRow);

$templateInfo = array (
    'page'                                  => $page,
    'objUser'                               => $objUser,
    'order'                                 => $order,
    'objOrder'                              => $objOrder,
    'chatStatus'                            => $chatStatus,
    'breadcrumbs'                           => $page->getBreadCrumbs(),
    'result'                                => $result,
    'URL_PREFIX'                            => URL_PREFIX,
    'URL_PREFIX_HTTP'                       => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'                      => URL_PREFIX_HTTPS,
    'ENT_QUOTES'                            => ENT_QUOTES,
    'pageType'                              => PAGE_TYPE,
    'pageId'                                => PAGE_ID,
    'HTML_COMMENTS'                         => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'                    => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID'              => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'                      => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'               => CUSTOM_IMAGE_URL_PREFIX,
    'website'                               => website,
    'links'                                 => $links,
    'ObjShoppingCart'                       => $ObjShoppingCart,
    'ObjMenu'                               => $ObjMenu,
    'ObjPageProduct'                        => $ObjPageProduct,
    'objGeotargetPage'                      => isset($objGeotargetPage) ? $objGeotargetPage : NULL,
    'objCategoryPage'                       => $objCategoryPage,
    'objGroupingPage'                       => $objGroupingPage,
    'objLandingPage'                        => $objLandingPage,
    'objSubcategoryPage'                    => $objSubcategoryPage,
    'FrontEndTemplateIncluder'              => $FrontEndTemplateIncluder,
    'productRow'                            => $productRow,
    'detailedSectionProducts'               => $detailedSectionProducts,
    'detailedSections'                      => $detailedSections,
    'gridSize'                              => $gridSize,
    'showQuickview'                         => $showQuickview,
    'showProductNumber'                     => $showProductNumber,
    'perRow'                                => $perRow,
    'showFilter'                            => $showFilter,
    'showSort'                              => $showSort,
    'multilingual'                          => $multilingual,
    'bilingual'                             => $bilingual,
    'tweakable'                             => $tweakable,
    'bestSeller'                            => $bestSeller,
    'luminous'                              => $luminous,
    'onSale'                                => $onSale,
    'productComplianceList'                 => $productComplianceList,
    'productComplianceChecklist'            => $productComplianceChecklist,
    'luminousChecklist'                     => $luminousChecklist,
    'detailedMultilingual'                  => $detailedMultilingual,
    'detailedBilingual'                     => $detailedBilingual,
    'detailedTweakable'                     => $detailedTweakable,
    'detailedBestSeller'                    => $detailedBestSeller,
    'detailedOnSale'                        => $detailedOnSale,
    'detailedLuminous'                      => $detailedLuminous,
    'gridHeader'                            => $gridHeader,
    'gridIntro'                             => $gridIntro,
    'products'                              => $products,
    'productBanners'                        => $productBanners,
    'detailedSecProducts'                   => $detailedSecProducts,
    'detailedProductBanners'                => $detailedProductBanners,
    'detailedProductComplianceList'         => $detailedProductComplianceList,
    'detailedProductComplianceChecklist'    => $detailedProductComplianceChecklist,
    'detailedLuminousChecklist'             => $detailedLuminousChecklist,
    'sortMoreThan'                          => $sortMoreThan,
    'hasFilters'                            => $hasFilters,
    'detailedHasFilters'                    => $detailedHasFilters,
    'count'                                 => $count,
    'showDetailsDiv'                        => $showDetailsDiv,
    'showFeatureDiv'                        => $showFeatureDiv,
    'multipleGrids'                         => $multipleGrids,
    'totalZones'                            => $totalZones,
    'subcategoryLink'                       => $subcategoryLink,
    'geoZones'                              => $geoZones,
    'zonesList'                             => $zonesList,
    'perColumn'                             => $perColumn
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('base-sec/subcategory-template', $templateInfo);