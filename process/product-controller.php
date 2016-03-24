<?php

$contact_url    = Page::create('contact-us');

# Get custom product CPI if any, make sure we are getting integers.
$cpi            = NULL;
$custom_product = NULL;
$productId      = $page->getId ();

if( isset($_GET['cpi']) && is_numeric ($_GET['cpi']) && $_GET['cpi'] > 0 ) {

    $cpi = $_GET['cpi'];
    $customProduct['active'] = 1;
    $customProduct = $ObjCustomProduct->GetCustomProduct ($cpi);
    $productId = isset($customProduct['products_id']) ? (int) $customProduct['products_id'] : $page->getId();
}

//instantiate objects with default value
$ObjProduct     = Product::create ($productId, $customProduct);
$ObjPageProduct = ProductPage::create ($productId);
$ObjStreetsign  = StreetNameTool::create ((int) $ObjPageProduct->getDefaultStreetsignToolId ());

# Get product state parameter from url
$productStateParameters = array();

// If product state parameters is available decode it and use it to get overwrite values
if( !empty($_GET['s']) ) {

    $productStateParameters = ProductStateParameter::decode ($_GET['s']);

    if( isset($productStateParameters['sourceProduct']) ) {
        $productId = $productStateParameters['sourceProduct'];
    }

    $ObjProduct = Product::create ($productId, $customProduct, $productStateParameters);
    $ObjPageProduct = ProductPage::create ($ObjProduct->getId ());
    $ObjStreetsign = StreetNameTool::create ((int) $ObjProduct->getStreetsignToolId ());
}

$ObjSku = Sku::create((int) $ObjProduct->getPreconfiguredSkuId());

$CustomProductObj        = new CustomProduct();
$ObjFlash                = new FlashDesign();
$ObjProductAttributes    = new ProductAttributes();
$ObjProductSubAttributes = new ProductSubAttributes($ObjSku->getName(), $ObjPageProduct->getProductNumber());

$breadcrumbs  = $page->getBreadCrumbs();
$productno    = $ObjPageProduct->getProductNumber();
$multilingual = $ObjPageProduct->isMultiLingual();
$tweakable    = $ObjPageProduct->isTweakAble();

$productName   = trim($ObjProduct->getProductName());
$subtitle      = trim($ObjPageProduct->getPageSubtitle());
$stid          = (int) $ObjProduct->getStreetsignToolId();
$landingId     = (int) $ObjProduct->getLandingId();
$subcategoryId = (int) $ObjProduct->getSubcategoryId();
$bestSeller    = (bool) $ObjProduct->getBestSeller();

$sizeTable = $ObjProduct->getSizeTable ();
$translationFamilyId = (int) $ObjProduct->getTranslationFamilyId();

$preconfSkue = $ObjProduct->getPreconfiguredSkuId();

$pageTabs = array();

#Product Page Tabs
if( !is_null($ObjProduct->getComplianceTabPosition ()) ) $pageTabs[] = array("position" => $ObjProduct->getComplianceTabPosition (), "name" => "Compliance");
if( !is_null($ObjProduct->getSizeTabPosition ()) )       $pageTabs[] = array("position" => $ObjProduct->getSizeTabPosition (), "name" => "Size");
if( !is_null($ObjProduct->getMaterialTabPosition ()) )   $pageTabs[] = array("position" => $ObjProduct->getMaterialTabPosition (), "name" => "Material");
if( !is_null($ObjProduct->getPrintingTabPosition ()) )   $pageTabs[] = array("position" => $ObjProduct->getPrintingTabPosition (), "name" => "Printing");

sort($pageTabs);

//$product_langauge = $ObjPageProduct->getLanguages();

//Check if the language change form was submitted
if( isset($_POST['language']) ) {

    //Get the URL
    $link = Page::create(
        'product', $ObjPageProduct->getProductIDByLanguageAndProductNumber($productno, $_POST['language'])
    );

    $url = $link->getUrl();

    if( $url != FALSE ) {
        header("Location: ".$url); die();
    }
}

$objOrder = new Orders();
$order = $objOrder->getCustomerOrder($orderno);

#Translations
$translationsFamily = $ObjProduct->getTranslationFamilies()->getAvailableLanguages();
$translations       = array();
$currentTrans       = 0;
//
//if( isset($translationsFamily[$ObjProduct->getId()]) ) {
//
//    unset($translationsFamily[$ObjProduct->getId()]);
//    $currentTrans = 1;
//}

foreach($translationsFamily as $productId => $languages) {

    if( !is_null($languages) ) {

        $stateParameters['stockTool'] = isset($productStateParameters['stockTool']) ? $productStateParameters['stockTool'] : 1;
        $stateParameters['sourceProduct'] = $productId;
        $stateParameters['preconfiguredSku'] = Product::create ($productId)->getPreconfiguredSku ();
        $stateParameters['translationFamily'] = !empty($productStateParameters['translationFamily']) ?
                            $productStateParameters['translationFamily'] : $ObjProduct->getTranslationFamilyId();
        $stateParameters['sourceSubcategoryProduct'] = $productStateParameters['sourceSubcategoryProduct'];

        if( method_exists($languages['language'], "getName")  ) {

            $translations[$productId] = array (
                "name"  => $languages['language']->getName (),
                "count" => $languages['language']->getCount () + $currentTrans,
                "url"   => $languages['product_url'] . "?s=".ProductStateParameter::encode($stateParameters)
            );
        }
    }
}

$sizeMaterials = $ObjProduct->getSizeTable();

#Compliacnes
$productCompliances = array ();

foreach ($sizeMaterials['groupedSizes'] as $sizeId => $sizeCompliances) {

    $skuId = $sizeCompliances['sku'];

    foreach ($sizeCompliances['compliances'] as $complianceId => $compliance) {

        $complianceGroupId = $compliance->getComplianceGroup()->getId();

        if( !in_array($compliance, $productCompliances) ) {

            $productCompliances[$complianceGroupId] = $compliance;
       }
    }
}

#Unique Materials
$tempMaterials   = array();
$uniqueMaterials = array ();

#Sort the size table materials
$sizeTable['materials'] = PropertySort::create ("cposition")->multiSortByValuesObj ($sizeTable['materials']);

foreach ($sizeTable['materials'] as $materialIndex => $materialGroup) {

    foreach($materialGroup as $index => $singleMaterials) {

        foreach ($singleMaterials as $key => $uMaterials) {

            if( !is_null($uMaterials) && !array_key_exists ($uMaterials->getId (), $tempMaterials) && !is_null ($uMaterials->getName ()) ) {

                $tempMaterials[$uMaterials->getId ()]['id'] = $uMaterials->getId ();
                $tempMaterials[$uMaterials->getId ()]['name'] = $uMaterials->getName ();
                $tempMaterials[$uMaterials->getId ()]['cposition']  = $uMaterials->getCategoryPosition ();
                $tempMaterials[$uMaterials->getId ()]['gposition']  = $uMaterials->getGroupPosition ();
            }
        }
    }
}

$uniqueMaterials = PropertySort::create ("cposition")->multiSortByValues ($tempMaterials);


#ReflectivityDialog


#Unique Prices Per Material
$priceMaterials = array ();

foreach ($sizeMaterials['groupedSizes'] AS $sizeId => $sizes) {

    foreach ($sizes['materials'] AS $materialId => $materials) {

        $priceMaterials[$sizeId]['materials'][$materialId]     = $materials['name'];
        $priceMaterials[$sizeId]['packaging'][$materialId]     = $materials['package'];
        $priceMaterials[$sizeId]['skuName'][$materialId]       = $materials['skuName'];
        $priceMaterials[$sizeId]['mountingHoles'][$materialId] = isset($materials['mountingHoles']) ? $materials['mountingHoles'][0]->getName() : NULL;
    }
}

$uniqueSizes = array();

foreach($sizeTable['groupedSizes'] as $sizes) {

    $sizeId = !is_null($sizes['size']) && !empty($sizes) ? $sizes['size']->getId () : NULL;

    if( !empty($sizeId)) {

        $uniqueSizes[$sizeId] = $sizes['size'];
    }
}


//@todo: Optimize me, no need for so many loops, We can use $ObjProduct->getSkus()->getLaminate()

#uniqueLaminate
$materialTab     = $ObjProduct->getMaterialTable();
$uniqueLaminates = array();
$priceLaminates  = array();

foreach($materialTab['productMaterials'] as $productmaterialIndex => $productMaterials) {

    foreach($productMaterials['materialGroups'] as $materialGroupIndex => $materialGroups) {

        foreach($materialGroups['materials'] as $materialId => $materials) {

            $materialTab['productMaterials'][$productmaterialIndex][$materialGroupIndex][$materialId] =
                                PropertySort::create ("cposition")->multiSortByValuesObj ($sizeTable['materials']);

            foreach ($materials['sizeLaminateGroups'] as $sizeLaminateGroups) {

                $id = 0;

                foreach ($sizeLaminateGroups['sizes'] as $sizes) {

                    $id = !is_null($sizes) ? $sizes->getId() : $materialId;
                }

                if( isset($sizeLaminateGroups['laminates']) ) {

                    foreach ($sizeLaminateGroups['laminates'] as $laminateId => $laminates) {

                        $uniqueLaminates[$laminateId]['name']     = $laminates->getName();
                        $uniqueLaminates[$laminateId]['position'] = $laminates->getPosition ();

                        $priceMaterials[$id]['laminates'][$sizeLaminateGroups['material_id']] =
                                            !is_null($laminates->getName()) ? $laminates->getName() : array();
                    }
                }
            }
        }
    }
}

#Product Recommendations
$recommendedProduct = array();

if( !is_null($ObjProduct->getProductRecommendations ()) ) {

    foreach ($ObjProduct->getProductRecommendations () as $productRecommendations) {

        if( !empty($productRecommendations) ) {

            foreach ($productRecommendations->getRecommendProduct () as $product) {

                $recommendedProduct[] = $product;
            }
        }
    }
}

#Product Accessory (Family)
$accessoryFamilies = array();

foreach($ObjProduct->getAccessoryFamily() as $accessoryFamily) {

    $accessoryFamilies[] = $accessoryFamily;
}

#Unique MountingHoles
$uniqueMountingHole    = array ();
$uniqueCornerRadius    = array ();
$priceTblMountingHoles = array ();

foreach ($sizeTable['mountingHoles'] as $mountingHoleIndex => $mountingHoles) {

    foreach ($mountingHoles as $grpIndex => $grpMountingHole) {

        foreach($grpMountingHole['cornerRadius'] as $cornerRadius) {

            $uniqueCornerRadius[] = $cornerRadius;

        }

        foreach ($grpMountingHole['mountingHoles'] as $key => $mountingHole) {

            if( method_exists($mountingHole, "getId") && !in_array ($mountingHole->getId (), $uniqueMountingHole) ) {

                if( !is_null($mountingHole->getName()) ) {

                    $uniqueMountingHole[$mountingHole->getId ()]['name'] = $mountingHole->getName ();
                    $uniqueMountingHole[$mountingHole->getId ()]['position'] = $mountingHole->getPosition ();
                    $priceTblMountingHoles[$mountingHoleIndex] = $mountingHole->getName ();
                }
            }
        }
    }
}

#Product Collection
$productCollections = array();

foreach ($ObjProduct->getProductCollections() as $collectionId => $collections) {

    $productCollections['collections']['name'] = $collections->getName();

    foreach ($collections->getProductCollectionProducts() as $collectionProductId => $collectionProduct) {

        $productCollections['collections']['products'][$collectionProduct->getCollectionProductId ()] =
            array (
                "id"        => $collectionProduct->getId (),
                "name"      => $collectionProduct->getName (),
                "subtitle"  => $collectionProduct->getSubtitle (),
                "position"  => $collectionProduct->getProductPosition (),
                "productId" => $collectionProduct->getCollectionProductId (),
                "link"      => ProductPage::create($collectionProduct
                                       ->getCollectionProductId ())->getUrl()
            );
    }
}

#Sort Product Collections
uasort($productCollections['collections']['products'], array (
        PropertySort::create ("position"), "sortByValueArr"
));


#Sort Mounting Holes
uasort ($uniqueMountingHole, array (PropertySort::create ("position"), "sortByValueArr"));

#Sort Laminates
uasort ($uniqueLaminates, array (PropertySort::create ("position"), "sortByValueArr"));

#Sort Material Table Category
uasort ($materialTab['productMaterials'], array ("PropertySort", "sortMaterialCategoryTableByValueObj"));

#SkuSon
$skuSon  = array();
$skuJson = array();

$skuSon['custom']            = $ObjProduct->getCustom();
$skuJson['cpi_value']        = $cpi;
$skuJson['product_id']       = $ObjProduct->getId();
$skuJson['custom']           = $ObjProduct->getCustom ();
$skuJson['urlState']         = json_encode ($_GET['s']);
$skuJson['preConfiguredSku'] = $preconfSkue;
$skuJson['skus']             = array();

foreach ($ObjProduct->getSkus() AS $skuIndex => $skus) {

    $pricing   = array ();
    $compliace = array();
    $sizeId    = !is_null($skus->getSizeId()) ? $skus->getSizeId() : $skus->getId();

    if( !is_null($skus->getPricing()) ) {

        foreach ($skus->getPricing()->getPriceTiers() AS $prices) {

            foreach ($prices AS $index => $price) {

                $pricing[$sizeId][] = array(
                     "minimumQuantity" => $price->getMinimumQuantity(),
                     "price"           => $price->getPrice()
                 );

                $priceMaterials[$sizeId]['prices'] = $pricing;
            }
        }
    }

    foreach($skus->getCompliances() as $complianceGroups) {

        $compliace[$skus->getId()][$complianceGroups->getComplianceGroupId()] = $complianceGroups->getComplianceGroup()->getId();
    }

    $packaging = $skus->getPackagings();

    $inclusionNote[$skus->getId()] = $skus->getPackageInclusionNote ();


    //Used for the skuSon
    $skuSon['skus'][$skus->getId()] =  array (
            "id"                        => $skus->getId(),
            "sizeId"                    => !is_null($skus->getSize()) ? $skus->getSize()->getId() : NULL,
            "materialId"                => $skus->getMaterialId(),
            "laminateId"                => $skus->getLaminateId(),
            "mountingHoleArrangementId" => $skus->getMountingHoleArrangementId(),
            "inStock"                   => $skus->isInStock(),
            "onSale"                    => $ObjProduct->getOnSale(),
            "inventory"                 => $skus->getInventory(),
            "skuCode"                   => $skus->getName(),
            "image"                     => $skus->getSmallImage(),
            "freightRequired"           => $skus->getRequiresFreight(),
            "innerUnits"                => $skus->getInnerunits(),
            "packagingId"               => $skus->getPackagingId(),
            "isTranslatable"            => $isTranslatable,
            "packageInclusionNote"      => $inclusionNote[$skus->getId ()],
            "dedicatedPackageCount"     => $skus->getDedicatedPackageCount(),
            "type"                      => ucfirst($skus->getSkuTypeName()),
            'complianceIds'             => $compliace[$skus->getId()],
            "packageName"               => !is_null($packaging) ? $packaging->getName() : "",
            "packagePlural"             => !is_null($packaging) ? $packaging->getPluralName() : "",
            "pricing"                   => $pricing[$sizeId]
    );

    //Used for the price table
    $priceTables[$skus->getSizeId()] = array(
        "size" => !is_null($skus->getSize()) ? $skus->getSize()->getName() : NULL,
        "skus" => array (
            "sku_id"    => $skus->getId(),
            "materials" => $priceMaterials[$skus->getSizeId()],
        ),
    );
}

sort($priceTables);

#Re-index the array
foreach($skuSon['skus'] as $skuId => $skus){

    if( !isset($skuJson['skus'][$skuId]) && !array_search($skus['id'], $skuJson['skus'][$skuId]) ) {
        $skuJson['skus'][]      = $skus; //Used for JS
        $skuSon['skus'][$skuId] = $skus;
    }
}

$templateInfo = array(
    'page'                     => $page,
    'objUser'                  => $objUser,
    'order'                    => $order,
    'objOrder'                 => $objOrder,
    'chatStatus'               => $chatStatus,
    'breadcrumbs'              => $breadcrumbs,
    'result'                   => $result,
    'URL_PREFIX'               => URL_PREFIX,
    'URL_PREFIX_HTTP'          => URL_PREFIX_HTTP,
    'URL_PREFIX_HTTPS'         => URL_PREFIX_HTTPS,
    'ENT_QUOTES'               => ENT_QUOTES,
    'PAGE_TYPE'                => PAGE_TYPE,
    'PAGE_ID'                  => PAGE_ID,
    'HTML_COMMENTS'            => HTML_COMMENTS,
    'NEXTOPIA_PUBLIC_ID'       => NEXTOPIA_PUBLIC_ID,
    'SHOPPER_APPROVED_SITE_ID' => SHOPPER_APPROVED_SITE_ID,
    'IMAGE_URL_PREFIX'         => IMAGE_URL_PREFIX,
    'CUSTOM_IMAGE_URL_PREFIX'  => CUSTOM_IMAGE_URL_PREFIX,
    'website'                  => website,
    'pageTabs'                 => $pageTabs,
    'links'                    => $links,
    'ObjShoppingCart'          => $ObjShoppingCart,
    'ObjMenu'                  => $ObjMenu,
    'ObjPageProduct'           => $ObjPageProduct,
    'ObjStreetsign'            => $ObjStreetsign,
    'ObjProductAttributes'     => $ObjProductAttributes,
    'ObjProductSubAttributes'  => $ObjProductSubAttributes,
    'FrontEndTemplateIncluder' => $FrontEndTemplateIncluder,
    'uniqueMountingHoles'      => $uniqueMountingHole,
    'Product'                  => $ObjProduct,
    'ObjFlash'                 => $ObjFlash,
    'CustomProductObj'         => $CustomProductObj,
    'productno'                => $productno,
    'multilingual'             => $multilingual,
    'tweakable'                => $tweakable,
    'uniqueMaterials'          => $uniqueMaterials,
    'uniqueLaminates'          => $uniqueLaminates,
    'productSkus'              => $ObjProduct->getSkus(),
    'productCompliances'       => $productCompliances,
    'skuJson'                  => json_encode($skuJson),
    'skuSon'                   => $skuSon,
    'productAccessories'       => $accessoryFamilies,
    'materialTab'              => $materialTab,
    'sizeTab'                  => $sizeTable,
    'priceTables'              => $priceTables,
    'productRecommendations'   => array_slice ($recommendedProduct, 0, 3),
    'productCollections'       => $productCollections,
     'uniqueSizes'             => $uniqueSizes,
    'pathCustomToolfont'       => $resultsOfGlobalController['Path_Custom_Tool_font'],
    'pathCustomToolDesignsave' => $resultsOfGlobalController['Path_Custom_Tool_Design_save'],
    'pathCustomToolclipart'    => $resultsOfGlobalController['Path_Custom_Tool_clipart'],
    'pathComplianceDialog'     => $resultsOfGlobalController['PathComplianceDialog'],
    'pathContentMaterialsTab'  => $resultsOfGlobalController['Path_Content_Materials_Tab'],
    'pathImgSmallProduct'      => $resultsOfGlobalController['Path_Img_Small_product'],
    'customProduct'            => $customProduct,
    'stid'                     => $stid,
    'defaultSkuId'             => $skuId,
    "translationFams"          => $translations,
    'bestSeller'               => $bestSeller,
    'productName'              => $productName,
    'subtitle'                 => $subtitle,
    'translationFamilyId'      => $translationFamilyId,
    'landingId'                => $landingId,
    'subcategoryId'            => $subcategoryId,
    'productStateParameters'    => $productStateParameters
);

if( !empty($resultsOfGlobalController) ) {

    foreach ($resultsOfGlobalController as $name => $value) {

        $templateInfo[(string) $name] = $value;
    }
}
echo Template::generate('base-sec/product-template', $templateInfo);




