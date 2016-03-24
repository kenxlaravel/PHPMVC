<?php

// Template the page
echo Template::generate(
    "global/head", array (
        'page'              => $page,
        'ObjPageProduct'    => $ObjPageProduct,
        'chatStatus'        => $chatStatus,
        'URL_PREFIX'        => URL_PREFIX,
        'ENT_QUOTES'        => ENT_QUOTES,
        'PAGE_TYPE'         => PAGE_TYPE,
        'HTML_COMMENTS'     => HTML_COMMENTS
    )
);
echo Template::generate("global/header", array ('page' => $page, 'HTML_COMMENTS' => HTML_COMMENTS, 'ENT_QUOTES' => ENT_QUOTES));

echo Template::generate("global/notices", array ());
echo Template::generate("global/breadcrumbs",
    array (
        'links'             => $links,
        'breadcrumbs'       => $page->getBreadCrumbs(),
        'result'            => $result,
        'URL_PREFIX_HTTP'   => URL_PREFIX_HTTP,
        'ENT_QUOTES'        => ENT_QUOTES,
        'PAGE_TYPE'         => PAGE_TYPE
    )
);
echo Template::generate("global/openwrap", array ('HTML_COMMENTS' => HTML_COMMENTS));

echo Template::generate(
    "base-sec/description", array (
        'objSubcategoryPage'    => $objSubcategoryPage,
        'page'                  => $page,
        'productRow'            => $productRow,
        'PAGE_TYPE'             => PAGE_TYPE,
        'Path_Templates_Base'   => $Path_Templates_Base,
        'IMAGE_URL_PREFIX'      => IMAGE_URL_PREFIX,
        'ENT_QUOTES'            => ENT_QUOTES,
        'PAGE_TYPE'             => PAGE_TYPE
    )
);

echo Template::generate(
    "base-sec/sidebar", array (
        'breadcrumbs'           => $page->getBreadCrumbs(),
        'objSubcategoryPage'    => $objSubcategoryPage,
        'objGroupingPage'       => $objGroupingPage,
        'objCategoryPage'       => $objCategoryPage,
        'page'                  => $page,
        'PAGE_ID'               => PAGE_ID
    )
);

//Show detailed sections, if any
if ( count($detailedSections) > 0 ) {

    $i = 0;

    foreach( $detailedSections AS $detailedSection ) {

        if ( $i > 0 ) {
            $firstGrid = false;

            if ( count($productRow) > 0 )
                $lastGrid = false;
            else {
                if ($i == count($detailedSection) - 1) {
                    $lastGrid = true;
                }
            }
        }
        else {
            $firstGrid = true;
            $lastGrid = false;
        }

        echo Template::generate(
            "base-sec/productgrid", array(
                'gridSize'                      => $detailedSection['grid_size'],
                'showQuickview'                 => $detailedSection['show_quickview'],
                'showProductNumber'             => $detailedSection['show_product_number'],
                'perRow'                        => $detailedSection['per_row'],
                'showFilter'                    => $detailedSection['show_filter'],
                'showSort'                      => $detailedSection['show_short'],
                'sortMoreThan'                  => $sortMoreThan,
                'count'                         => count($detailedSectionProducts[$i]),
                'hasFilters'                    => $detailedHasFilters[$i],
                'multilingual'                  => $detailedMultilingual[$i],
                'bilingual'                     => $detailedBilingual[$i],
                'tweakable'                     => $detailedTweakable[$i],
                'bestSeller'                    => $detailedBestSeller[$i],
                'onSale'                        => $detailedOnSale[$i],
                'luminous'                      => $detailedLuminous,
                'gridHeader'                    => $detailedSection['name'],
                'gridIntro'                     => $gridIntro,
                'detailedSection'               => $detailedSection,
                'products'                      => $detailedSecProducts[$i],
                'productRow'                    => $detailedSectionProducts[$i],
                'productBanners'                => $detailedProductBanners[$i],
                'productComplianceList'         => $detailedProductComplianceList[$i],
                'productComplianceChecklist'    => $detailedProductComplianceChecklist[$i],
                'luminousChecklist'             => $detailedLuminousChecklist[$i],
                'objLandingPage'                => $objLandingPage,
                'Path_Templates_Base'           => $Path_Templates_Base,
                'objSubcategoryPage'            => $objSubcategoryPage,
                'page'                          => $page,
                'pageType'                      => $pageType,
                'pageId'                        => $pageId,
                'type'                          => 'detailed',
                'detailsDiv'                    => $showDetailsDiv[$i],
                'featuresDiv'                   => $showFeatureDiv[$i],
                'totalZones'                    => $totalZones,
                'subcategoryLink'               => $subcategoryLink,
                'geoZones'                      => $geoZones,
                'zonesList'                     => $zonesList,
                'perColumn'                     => $perColumn,
                'firstGrid'                     => $firstGrid,
                'lastGrid'                      => $lastGrid
            )
        );

        $i++;

    }

}

if ( count($detailedSections) == 0 )
    $firstGrid = true;
else
    $firstGrid = false;

if ( count($productRow) > 0 )
    $lastGrid = true;
else
    $lastGrid = false;

echo Template::generate(
    "base-sec/productgrid", array (
        'gridSize'                      => $gridSize,
        'showQuickview'                 => $showQuickview,
        'showProductNumber'             => $showProductNumber,
        'perRow'                        => $perRow,
        'showFilter'                    => $showFilter,
        'showSort'                      => $showSort,
        'sortMoreThan'                  => $sortMoreThan,
        'count'                         => $count,
        'hasFilters'                    => $hasFilters,
        'multilingual'                  => $multilingual,
        'bilingual'                     => $bilingual,
        'tweakable'                     => $tweakable,
        'bestSeller'                    => $bestSeller,
        'luminous'                      => $luminous,
        'onSale'                        => $onSale,
        'productComplianceList'         => $productComplianceList,
        'productComplianceChecklist'    => $productComplianceChecklist,
        'luminousChecklist'             => $luminousChecklist,
        'gridHeader'                    => $gridHeader,
        'gridIntro'                     => $gridIntro,
        'products'                      => $products,
        'productRow'                    => $productRow,
        'productBanners'                => $productBanners,
        'objLandingPage'                => $objLandingPage,
        'Path_Templates_Base'           => $Path_Templates_Base,
        'objSubcategoryPage'            => $objSubcategoryPage,
        'page'                          => $page,
        'pageType'                      => $pageType,
        'pageId'                        => $pageId,
        'multipleGrids'                 => $multipleGrids,
        'totalZones'                    => $totalZones,
        'subcategoryLink'               => $subcategoryLink,
        'geoZones'                      => $geoZones,
        'zonesList'                     => $zonesList,
        'perColumn'                     => $perColumn,
        'firstGrid'                     => $firstGrid,
        'lastGrid'                      => $lastGrid
    )
);

echo Template::generate("global/closewrap", array ('HTML_COMMENTS' => HTML_COMMENTS));
echo Template::generate(
    "global/header-content", array (
        'links'                        => $links,
        'FrontEndTemplateIncluder'     => $FrontEndTemplateIncluder,
        'ObjShoppingCart'              => $ObjShoppingCart,
        'ObjMenu'                      => $ObjMenu,
        'ENT_QUOTES'                   => ENT_QUOTES,
        'IMAGE_URL_PREFIX'             => IMAGE_URL_PREFIX,
        'URL_PREFIX_HTTPS'             => URL_PREFIX_HTTPS,
        'website'                      => website
    )
);
echo Template::generate("global/footer", array (
        'page'              => $page,
        'links'             => $links,
        'URL_PREFIX_HTTP'   => URL_PREFIX_HTTP,
        'HTML_COMMENTS'     => HTML_COMMENTS,
        'URL_PREFIX'        => URL_PREFIX
    )
);

echo Template::generate(
    "global/foot", array (
        'page'                         => $page,
        'ObjPageProduct'               => $ObjPageProduct,
        'URL_PREFIX'                   => URL_PREFIX,
        'NEXTOPIA_PUBLIC_ID'           => NEXTOPIA_PUBLIC_ID,
        'SHOPPER_APPROVED_SITE_ID'     => SHOPPER_APPROVED_SITE_ID,
        'order'                        => $order,
        'objOrder'                     => $objOrder,
        'FrontEndTemplateIncluder'     => $FrontEndTemplateIncluder
    )
);