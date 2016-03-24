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

////Builder OR flash tool
if( ($ObjPageProduct->getToolTypeName() == 'builder' || (($ObjPageProduct->isTweakAble()) && isset($_GET['mode'])? $_GET['mode']:NULL == 'tweak') || ($ObjPageProduct->getToolTypeName() == 'flash')) && !isset($_GET['cpi'])) {

    //Include our custom tool logic. This file will handle loading the builder, flash tool, checking for
    //support, and displaying various messages about support and alternatives
    echo Template::generate(
        "base-sec/custom_tools", array (
            'page'                      => $page,
            "ObjPageProduct"            => $ObjPageProduct,
            'ObjProductAttributes'      => $ObjProductAttributes,
            'ObjProductSubAttributes'   => $ObjProductSubAttributes,
            'priceTables'               => $priceTables,
            'multilingual'              => $multilingual,
            'customProduct'             => $customProduct,
            'tweakable'                 => $tweakable,
            'Product'                   => $Product,
            'ObjFlash'                  => $ObjFlash,
            'pageTabs'                  => $pageTabs,
            'links'                     => $links,
            'sizeradio'                 => 0,
            'productSizes'              => $sizeTab,
            'productAccessories'        => $productAccessories,
            'sizeArray'                 => $sizeTab['groupedSizes'],
            'skuSon'                    => $skuSon,
            'totalSizes'                => count ($sizeTab['groupedSizes']),
            'productRecommendations'    => $productRecommendations,
            'pathCustomToolfont'        => $pathCustomToolfont,
            'materialTab'               => $materialTab,
            'pathCustomToolDesignsave'  => $pathCustomToolDesignsave,
            'pathCustomToolclipart'     => $pathCustomToolclipart,
            'pathImgSmallProduct'       => $pathImgSmallProduct,
            'pathComplianceDialog'      => $pathComplianceDialog,
            'pathContentMaterialsTab'   => $pathContentMaterialsTab,
            'productStateParameters'    => $productStateParameters
        )
    );


}else if( $ObjPageProduct->getToolTypeName() == 'streetname' ) {

    echo Template::generate(
        "base-sec/streetname", array (
            'page'                    => $page,
            "ObjPageProduct"          => $ObjPageProduct,
            'productno'               => $productno,
            'CustomProductObj'        => $CustomProductObj,
            'ObjStreetsign'           => $ObjStreetsign,
            'ObjProductAttributes'    => $ObjProductAttributes,
            'ObjProductSubAttributes' => $ObjProductSubAttributes,
            'multilingual'            => $multilingual,
            'sizeradio'               => 0,
            'productSizes'            => $sizeTab,
            'productAccessories'      => $productAccessories,
            'sizeArray'               => $sizeTab['groupedSizes'],
            'skuSon'                  => $skuSon,
            'totalSizes'              => count ($sizeTab['groupedSizes']),
            'productRecommendations'  => $productRecommendations,
            'tweakable'               => $tweakable,
            'materialTab'             => $materialTab,
            'pageTabs'                => $pageTabs,
            'Product'                 => $Product,
            'pathComplianceDialog'    => $pathComplianceDialog,
            'pathImgSmallProduct'     => $pathImgSmallProduct,
            'defaultSkuId'            => $defaultSkuId,
            'productSkus'             => $productSkus,
            'productCompliances'      => $productCompliances,
            'stid'                    => $stid,
            'skuJson'                 => $skuJson,
            'skuSon'                  => $skuSon,
            'priceTables'             => $priceTables,
            'customProduct'           => $customProduct,
            'best_seller'             => $bestSeller,
            'product_name'            => $productName,
            'subtitle'                => $subtitle,
            'translation_family_id'   => $translationFamilyId,
            'landing_id'              => $landingId,
            'subcategory_id'          => $subcategoryId,
            'productStateParameters'   => $productStateParameters
        )
    );

}else{

    echo Template::generate(
        "base-sec/product-page-skuer", array (
      //  "base-sec/product",
            'page'                   => $page,
            'sizeArray'              => $sizeTab['groupedSizes'],
            'skuJson'                => $skuJson,
            'skuSon'                 => $skuSon,
            'sizeTab'                => $sizeTab,
            'defaultSkuId'           => $defaultSkuId,
            "ObjPageProduct"         => $ObjPageProduct,
            'productno'              => $productno,
            'CustomProductObj'       => $CustomProductObj,
            'multilingual'           => $multilingual,
            'tweakable'              => $tweakable,
            'customProduct'          => $customProduct,
            'materialTab'            => $materialTab,
            'productCollections'     => $productCollections,
            'sizeradio'              => 0,
            "translationFams"        => $translationFams,
            'priceTables'            => $priceTables,
            'uniqueMountingHoles'    => $uniqueMountingHoles,
            'uniqueLaminates'        => $uniqueLaminates,
            'uniqueMaterials'        => $uniqueMaterials,
            'uniqueSizes'            => $uniqueSizes,
            'pageTabs'               => $pageTabs,
            'productCompliances'     => $productCompliances,
            'productAccessories'     => $productAccessories,
            'totalSizes'             => count ($sizeTab['groupedSizes']),
            'productRecommendations' => $productRecommendations,
            'pathComplianceDialog'   => $pathComplianceDialog,
            'Product'                => $Product,
            "breadcrumbs"            => $page->getBreadcrumbs (),
            'productStateParameters'  => $productStateParameters
        )
    );

   // echo Template::generate("base-sec/product-header", array ('product' => $Product));
}

$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderLoading');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoaderResults');
$FrontEndTemplateIncluder->addHandlebarsTemplate('cartLoadingMessage');
$FrontEndTemplateIncluder->addHandlebarsTemplate('addedToCartError');
$FrontEndTemplateIncluder->addHandlebarsTemplate('addedToCartSuccess');
$FrontEndTemplateIncluder->addHandlebarsTemplate('miniCart');


if( $ObjPageProduct->getToolTypeName() != 'builder' && ((!$ObjPageProduct->isTweakAble()) && $_GET['mode'] != 'tweak') && ($ObjPageProduct->getToolTypeName() != 'flash' && isset($_REQUEST['cpi']))) {

    echo Template::generate("base-sec/material-tab", array ('materialTab' => $materialTab));
    echo Template::generate("base-sec/size-tab", array ('sizeTab' => $sizeTab));
}

echo Template::generate("global/closewrap", array ('HTML_COMMENTS' => HTML_COMMENTS));

echo Template::generate(
    "global/header-content",  array (
        'links'                    => $links,
        'FrontEndTemplateIncluder' => $FrontEndTemplateIncluder,
        'ObjShoppingCart'          => $ObjShoppingCart,
        'ObjMenu'                  => $ObjMenu,
        'ENT_QUOTES'               => ENT_QUOTES,
        'IMAGE_URL_PREFIX'         => IMAGE_URL_PREFIX,
        'URL_PREFIX_HTTPS'         => URL_PREFIX_HTTPS,
        'website'                  => website
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