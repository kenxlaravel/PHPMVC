<?php


//Instantiate for home grid in case of no result
require_once $PathClasses."PHPXMLLib1.2b.php";

$objSubcategoryPage = SubcategoryPage::create(PAGE_ID);
$ObjPageHomepage = new PageHomepage();
$search = new Search();
$result  = $search->getSearchResults($_REQUEST['keywords'], $_SERVER['QUERY_STRING']);
$refineSub = '';
$keywords = (string)$_REQUEST['keywords'];
$currentPage = (int)$result['current_page'];
$trackingId = NEXTOPIA_PUBLIC_ID;
$pageNum = ( isset($_REQUEST['page']) ? $_REQUEST['page'] : NULL );
$pageLimit = $result['page_limit'];

if ( $result['result_found'] ) {
    $productRow = $result['product'];
}

if ( strpos($_SERVER['QUERY_STRING'],'Subcategory') > 0 ){
    $refineSub = strpos( $_SERVER['QUERY_STRING'],'Subcategory' );
}

if ( count($result['sidebar_array']) == 0 ) {
    $refineSub = 1;
}

if ( $refineSub != 0 ) {
    $perRow = 6;

} else {
    $perRow = 5;
}

$gridSize = 'large';
$showQuickview = TRUE;
$showProductNumber = TRUE;
$showFilter = TRUE;
$showSort = TRUE;
$sortMoreThan = Settings::getSettingValue('productsorting');

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

//Sorting
$manualSortBys = array( 'Relevance', 'Name' );
$sortPosition = strpos( $_SERVER['QUERY_STRING'], 'sort_by_field' );
$sortTypePosition = strpos( $_SERVER['QUERY_STRING'], 'SC', $sortPosition );

if ( $sortTypePosition == 0 ) {

    $sortType = 'ASC';

}

$sortAsc = strpos( $_SERVER['QUERY_STRING'], 'ASC', $sortPosition );

if ( $sortAsc > 0 ) {

    $sortType = 'DESC';

}

$sortDesc = strpos( $_SERVER['QUERY_STRING'], 'DESC', $sortPosition );

if ( $sortDesc > 0 ) {

    $sortType = 'ASC';

}

//Pagination
//Previous page
if ( $result['total_pages'] > 1 && $result['current_page'] > 1 ) {
    $PreviousPageUrl = preg_replace( $result['replace_page'], "page=" . ( $result['current_page'] - 1 ), $result['PageOneUrl'] );
}

$pagesList = array();

//List of pages in the pagination
if ( $result['total_pages'] < 10 && $result['total_pages'] > 1 ) {

    for ( $i = 1; $i <= $result['total_pages']; $i++ ) {

        if ( $i == $result['current_page'] ) {

            $pagesList[] = $i;

        } else {

            $pagesList[] = array(preg_replace($result['replace_page'], "page=" . $i, $result['PageOneUrl']), $i);

        }

    }

} elseif ( $result['total_pages'] > 1 ) {

    $PStart = $result['current_page'] - 4;

    if ( $PStart < 1 ) {
        $PStart = 1;
    }

    for ( $i = $PStart; $i < $result['current_page']; $i++ ) {

        $pagesList[] = array(preg_replace($result['replace_page'], "page=" . $i, $result['PageOneUrl']), $i);

    }

    $pagesList[] = $result['current_page'];
    $PEnd = $result['current_page'] + 5;

    if ( $PEnd > $result['total_pages'] ) {

        $PEnd = $result['total_pages'];

    }

    for ( $i = $result['current_page'] + 1; $i < $PEnd; $i++ ) {

        $pagesList[] = array(preg_replace($result['replace_page'], "page=" . $i, $result['PageOneUrl']), $i);

    }

}

//Next page
if ( $result['total_pages'] > 1 && $result['current_page'] < $result['total_pages'] ) {
    $NextPageUrl = preg_replace($result['replace_page'], "page=" . ($result['current_page'] + 1), $result['PageOneUrl']);
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

    $tmpProduct = ProductPage::create( (int) $value['products_id'] );

    //Set boolean values for possible product flags

    //Whether or not the product is tweakable
    if ( $tmpProduct->isTweakAble() ) {

        $tweakable = TRUE;

        $singleProductBanners[] = array('Tweakable', 'tweakable');

    }

    //Whether or not the product is multilingual
    if ( $tmpProduct->isMultiLingual() ) {

        $multilingual = TRUE;

        $singleProductBanners[] = array('Multilingual', 'multilingual');

    }

    //Whether or not the product is bilingual
    if ( $tmpProduct->isBiLingual() && !$tmpProduct->isMultiLingual() ) {

        $bilingual = TRUE;

        $singleProductBanners[] = array('Bilingual', 'bilingual');

    }

    //Whether or not the product is a best seller
    if ( $tmpProduct->getDefaultBestSeller() ) {

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

    $productRow[$key]['productURL'] = $tmpProduct->getUrl();

    $productBanners[] = $singleProductBanners;

    $products[] = $tmpProduct;

}

if ( $tweakable == TRUE || $multilingual == TRUE || $onSale == TRUE || $bestSeller == TRUE || $bilingual == TRUE || $luminous == TRUE || $hasCompliances == TRUE ) {
    $hasFilters = TRUE;
}

$count = count($productRow);

//If we are showing more than one product grid on this page, this will make sure to show the column-2 wrapper only once

$multipleGrids = false;

if ( count($detailedSectionProducts) > 0 ) {

    $multipleGrids = true;

}


$templateInfo = array (
    'page'                          => $page,
    'order'                         => $order,
    'objOrder'                      => $objOrder,
    'chatStatus'                    => $chatStatus,
    'breadcrumbs'                   => $page->getBreadCrumbs(),
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
    'website'                       => website,
    'ObjShoppingCart'               => $ObjShoppingCart,
    'ObjMenu'                       => $ObjMenu,
    'ObjPageProduct'                => $ObjPageProduct,
    'ObjPageHomepage'               => $ObjPageHomepage,
    'objSubcategoryPage'            => $objSubcategoryPage,
    'result'                        => $result,
    'productRow'                    => $productRow,
    'products'                      => $products,
    'productBanners'                => $productBanners,
    'refine_sub'                    => $refineSub,
    'search'                        => $search,
    'links'                         => $links,
    'objCountry'                    => $objCountry,
    'Path_Templates_Base'           => $Path_Templates_Base,
    'FrontEndTemplateIncluder'      => $FrontEndTemplateIncluder,
    'gridSize'                      => $gridSize,
    'showQuickview'                 => $showQuickview,
    'showProductNumber'             => $showProductNumber,
    'perRow'                        => $perRow,
    'showFilter'                    => $showFilter,
    'showSort'                      => $showSort,
    'sortMoreThan'                  => $sortMoreThan,
    'hasFilters'                    => $hasFilters,
    'count'                         => $count,
    'multipleGrids'                 => $multipleGrids,
    'multilingual'                  => $multilingual,
    'bilingual'                     => $bilingual,
    'tweakable'                     => $tweakable,
    'bestSeller'                    => $bestSeller,
    'onSale'                        => $onSale,
    'luminous'                      => $luminous,
    'productComplianceList'         => $productComplianceList,
    'productComplianceChecklist'    => $productComplianceChecklist,
    'luminousChecklist'             => $luminousChecklist,
    'manualSortBys'                 => $manualSortBys,
    'sortPosition'                  => $sortPosition,
    'sortTypePosition'              => $sortTypePosition,
    'sortType'                      => $sortType,
    'sortAsc'                       => $sortAsc,
    'sortDesc'                      => $sortDesc,
    'keywords'                      => $keywords,
    'currentPage'                   => $currentPage,
    'trackingId'                    => $trackingId,
    'pageNum'                       => $pageNum,
    'pageLimit'                     => $pageLimit,
    'PreviousPageUrl'               => $PreviousPageUrl,
    'NextPageUrl'                   => $NextPageUrl,
    'pagesList'                     => $pagesList
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}

echo Template::generate('global/search-template', $templateInfo);