<?php
/**
 * @author     Daniel <danielm@brimar.com>
 * @author     Ken <ken@brimar.com>
 * @author     Ali <ali@brimar.com>
 * @author     Anu <abu@brimar.com>
 * @copyright  Brimar Industries
 * @since      Class available since Nov 17, 2014
 * @version    ?? :: PHP 5.2.4
 **/

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
    "base-sec/description",
    array (
        'productRow'            => $geoSubRow,
        'federalSigns'          => $federalSigns,
        'objGeotargetPage'      => $objGeotargetPage,
        'page'                  => $page,
        'Path_Templates_Base'   => $Path_Templates_Base,
        'IMAGE_URL_PREFIX'      => IMAGE_URL_PREFIX,
        'ENT_QUOTES'            => ENT_QUOTES,
        'PAGE_TYPE'             => PAGE_TYPE
    )
);
echo Template::generate(
    "base-sec/sidebar",
    array (
        'breadcrumbs'           => $page->getBreadCrumbs(),
        'objCategoryPage'       => $objCategoryPage,
        'objSubcategoryPage'    => $objSubcategoryPage,
        'page'                  => $page,
        'ENT_QUOTES'            => ENT_QUOTES,
        'PAGE_TYPE'             => PAGE_TYPE
    )
);

echo Template::generate(
    "base-sec/productgrid",
    array (
        'objGeotargetPage'      => $objGeotargetPage,
        'objSubcategoryPage'    => $objSubcategoryPage,
        'objCategoryPage'       => $objCategoryPage,
        'page'                  => $page,
        'Path_Templates_Base'   => $Path_Templates_Base,
        'productRow'            => $productRow,
        'gridSize'              => $gridSize,
        'showQuickview'         => $showQuickview,
        'showProductNumber'     => $showProductNumber,
        'perRow'                => $perRow,
        'showFilter'            => $showFilter,
        'showSort'              => $showSort,
        'sortMoreThan'          => $sortMoreThan,
        'count'                 => $count,
        'hasFilters'            => $hasFilters,
        'multilingual'          => $multilingual,
        'bilingual'             => $bilingual,
        'tweakable'             => $tweakable,
        'bestSeller'            => $bestSeller,
        'onSale'                => $onSale,
        'luminous'              => $luminous,
        'productComplianceList' => $productComplianceList,
        'luminousChecklist'     => $luminousChecklist,
        'productBanners'        => $productBanners,
        'gridHeader'            => $gridHeader,
        'gridIntro'             => $gridIntro,
        'products'              => $products,
        'totalZones'            => $totalZones,
        'pageType'              => $pageType,
        'pageId'                => $pageId,
        'firstGrid'             => $firstGrid,
        'lastGrid'              => $lastGrid
    )
);

//Display Federal Signs
if ( count($federalSigns) > 0 ) {

    $lastGrid = true;

    echo Template::generate(
        "base-sec/productgrid",
        array (
            'objGeotargetPage'              => $objGeotargetPage,
            'objSubcategoryPage'            => $objSubcategoryPage,
            'objCategoryPage'               => $objCategoryPage,
            'page'                          => $page,
            'Path_Templates_Base'           => $Path_Templates_Base,
            'productRow'                    => $federalSigns,
            'gridSize'                      => $objGeotargetPage->getGridSize(),
            'showQuickview'                 => $objGeotargetPage->getShowQuickview(),
            'showProductNumber'             => $objGeotargetPage->getShowProductNumber(),
            'perRow'                        => $perRow,
            'showFilter'                    => $objGeotargetPage->getShowFilter(),
            'showSort'                      => $objGeotargetPage->getShowSort(),
            'sortMoreThan'                  => $sortMoreThan,
            'count'                         => $count,
            'hasFilters'                    => $federalHasFilters,
            'multilingual'                  => $federalMultilingual,
            'bilingual'                     => $federalBilingual,
            'tweakable'                     => $federalTweakable,
            'bestSeller'                    => $federalBestSeller,
            'onSale'                        => $federalOnSale,
            'luminous'                      => $federalLuminous,
            'productBanners'                => $federalProductBanners,
            'productComplianceList'         => $federalProductComplianceList,
            'productComplianceChecklist'    => $federalProductComplianceChecklist,
            'luminousChecklist'             => $federalLuminousChecklist,
            'gridHeader'                    => $objGeotargetPage->getGridHeader(),
            'gridIntro'                     => $objGeotargetPage->getGridIntro(),
            'products'                      => $federalProducts,
            'totalZones'                    => $totalZones,
            'pageType'                      => $pageType,
            'pageId'                        => $pageId,
            'firstGrid'                     => $firstGrid,
            'lastGrid'                      => $lastGrid

        )
    );

}

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
        'URL_PREFIX'        => URL_PREFIX,
        'showOrderHistory'  => (isset($_SESSION['cid']) && User::getOrderCount($_SESSION['cid']) && $_SESSION['UserType']=='U') ? TRUE : FALSE
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
