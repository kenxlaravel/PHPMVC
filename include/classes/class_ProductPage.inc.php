<?php

/**
 * Class ProductPage
 */
class ProductPage extends Page {

    /**
     * Type of page we are one
     */
    const TYPE = 'product';

    /**
     * Type of parent page we are on.
     */
    const PARENT_TYPE = NULL;

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "
                SELECT p.id AS id, p.default_builder_ref, p.default_best_seller, p.active, p.product_number, p.default_product_name, p.default_subtitle, p.by_legend, p.artwork_description,
                p.display_number, p.note, p.custom, p.free_form, p.has_arrows, p.has_symbols, p.viewing_distance_standard_id, p.description, p.streetsign_note, p.compliance_file,
                p.size_intro, p.size_outro, p.material_intro, p.material_outro, p.printing_intro, p.fonts_description, p.installation_intro, p.installation_question_id,
                p.show_crosshatch_test_info, p.show_text_size, p.advertise, p.searchable, p.new_until, p.expiration, p.on_sale, p.savings, p.page_title, p.page_subtitle,
                p.meta_description, p.meta_keywords, p.search_keywords, p.sitemap_show, p.url_slug, p.canonical_page_url_id, p.custom_alternative_page_type, p.custom_alternative_page_id,
                p.page_priority, p.builder_tweak_tool_id, p.product_page_template_id, xq.language_count, p.header_id, p.language_id, p.change_frequency_id, p.compliance_tab_position,
                p.size_tab_position, p.installation_tab_position, p.material_tab_position, p.printing_tab_position, p.show_printing_info, p.show_material_illustrations,
                p.default_preconfigured_sku_id, p.default_translation_family_id, p.default_tool_type_id, p.default_flash_tool_id, p.default_streetsign_tool_id, p.default_landing_id,
                p.default_subcategory_id, p.search_thumbnail, tt.name AS toolTypeName, bt.id as tweak_id, b.lightweight, p.details_tab_content, p.materials_tab_content, b.flash_tool_id, bt.disclaimer,
                ft.builder_id, GROUP_CONCAT(psk.sku_id) AS array_product_skus

                FROM bs_products p

                LEFT JOIN bs_builders b ON (b.builder_ref = p.default_builder_ref AND b.active = TRUE)
                LEFT JOIN bs_flash_tools ft ON (ft.id = p.default_flash_tool_id AND ft.active = TRUE)
                LEFT JOIN bs_product_builders pb ON (pb.product_id = p.id AND pb.builder_ref = b.builder_ref)
                LEFT JOIN bs_tool_types tt ON (p.default_tool_type_id = tt.id)
                LEFT JOIN bs_builder_tweak_tools bt ON ( p.builder_tweak_tool_id = bt.id )
                LEFT JOIN bs_product_skus psk ON (psk.product_id = p.id)

                LEFT JOIN (SELECT x.language_count, tfp.product_id FROM bs_translation_family_products tfp
                    INNER JOIN (SELECT COUNT(DISTINCT xtfp.product_id) AS language_count, xtfp.translation_family_id FROM bs_translation_family_products xtfp
                    INNER JOIN bs_products p ON (p.id = xtfp.product_id) GROUP BY xtfp.translation_family_id) x ON (x.translation_family_id = tfp.translation_family_id)) xq
                ON (xq.product_id = p.id) WHERE p.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id;";

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var int $defaultBuilderRef
     */
    private $defaultBuilderRef;

    /**
     * @var bool|int $defaultBestSeller
     */
    private $defaultBestSeller;

    /**
     * @var bool|int $active
     */
    private $active;

    /**
     * @var int $productNumber
     */
    private $productNumber;

    /**
     * @var string $defaultProductName;
     */
    private $defaultProductName;

    /**
     * @var object $Product
     */
    private $Product;

    /**
     * @var string $defaultSubtitle
     */
    private $defaultSubtitle;

    /**
     * @var string $byLegend
     */
    private $byLegend;

    /**
     * @var string $artworkDescription
     */
    private $artworkDescription;

    /**
     * @var bool|int $displayNumber
     */
    private $displayNumber;

    /**
     * @var int $builderTweakToolId
     */
    private $builderTweakToolId;

    /**
     * @var string $note
     */
    private $note;

    /**
     * @var bool|int $custom
     */
    private $custom;

    /**
     * @var bool|int $freeForm
     */
    private $freeForm;

    /**
     * @var bool|int $hasArrows
     */
    private $hasArrows;

    /**
     * @var bool|int $hasSymbals
     */
    private $hasSymbols;

    /**
     * @var bool|int $viewingDistanceStandardId
     */
    private $viewingDistanceStandardId;

    /**
     * @var string $description
     */
    private $description;

    /**
     * @var bool|int $multiLingual
     */
    private $multiLingual;

    /**
     * @var bool|int $biLingual
     */
    private $biLingual;

    /**
     * @var string $streetsignNote;
     */
    private $streetsignNote;

    /**
     * @var string $complianceFile
     */
    private $complianceFile;

    /**
     * @var string $sizeIntro
     */
    private $sizeIntro;

    /**
     * @var string $sizeOutro
     */
    private $sizeOutro;

    /**
     * @var string $materialIntro
     */
    private $materialIntro;

    /**
     * @var string $materialOutro
     */
    private $materialOutro;

    /**
     * @var string $printingIntro
     */
    private $printingIntro;

    /**
     * @var string $fontsDescription
     */
    private $fontsDescription;

    /**
     * @var string $installationIntro
     */
    private $installationIntro;

    /**
     * @var array $installationQuestionId
     */
    private $installationQuestionId;

    /**
     * @var bool|int $showCrosshatchTestInfo
     */
    private $showCrosshatchTestInfo;

    /**
     * @var bool|int $showTextSize
     */
    private $showTextSize;

    /**
     * @var bool|int $advertise
     */
    private $advertise;

    /**
     * @var bool|int $searchable
     */
    private $searchable;

    /**
     * @var bool|int $newUntil
     */
    private $newUntil;

    /**
     * @var string $expiration
     */
    private $expiration;

    /**
     * @var bool|int $onSale
     */
    private $onSale;

    /**
     * @var bool|int $savings
     */
    private $savings;

    /**
     * @var string $pageTitle
     */
    private $pageTitle;

    /**
     * @var string $pagesubtitle
     */
    private $pageSubtitle;

    /**
     * @var string $metaDescription
     */
    private $metaDescription;

    /**
     * @var string $metaKeywords
     */
    private $metaKeywords;

    /**
     * @var string $searchKeywords
     */
    private $searchKeywords;

    /**
     * @var bool|int $sitemapShow
     */
    private $sitemapShow;

    /**
     * @var string $urlSlug
     */
    private $urlSlug;

    /**
     * @var int $canonicalPageUrlId
     */
    private $canonicalPageUrlId;

    /**
     * @var string $customAlternativePageType
     */
    private $customAlternativePageType;

    /**
     * @var int $customAlternativePageId
     */
    private $customAlternativePageId;

    /**
     * @var int $pagePriority
     */
    private $pagePriority;

    /**
     * @var int $tweakId
     */
    private $tweakId;

    /**
     * @var int $productPageTemplateId
     */
    private $productPageTemplateId;

    /**
     * @var array $productSkuIds
     */
    private $productSkuIds;

    /**
     * @var int $headerId
     */
    private $headerId;

    /**
     * @var int $languageId
     */
    private $languageId;

    /**
     * @var int $changeFrequencyId
     */
    private $changeFrequencyId;

    /**
     * @var int $complianceTabPosition
     */
    private $complianceTabPosition;

    /**
     * @var int $sizeTabPosition
     */
    private $sizeTabPosition;

    /**
     * @var int $installationTabPosition
     */
    private $installationTabPosition;

    /**
     * @var int $materialTabPosition
     */
    private $materialTabPosition;

    /**
     * @var int $printingTabPosition
     */
    private $printingTabPosition;

    /**
     * @var bool|int $showPrintingInfo
     */
    private $showPrintingInfo;

    /**
     * @var bool|int $showMaterialIllustrations
     */
    private $showMaterialIllustrations;

    /**
     * @var int $defaultPreconfiguredSkuId
     */
    private $defaultPreconfiguredSkuId;

    /**
     * @var int $defaultTranslationFamilyId
     */
    private $defaultTranslationFamilyId;

    /**
     * @var int $defaultToolTypeId
     */
    private $defaultToolTypeId;

    /**
     * @var int $defaultFlashToolId
     */
    private $defaultFlashToolId;

    /**
     * @var int $defaultStreetsignToolId
     */
    private $defaultStreetsignToolId;

    /**
     * @var int $defaultLandingId
     */
    private $defaultLandingId;

    /**
     * @var array $imagePath
     */
    private $imagePath;

    /**
     * @var int $defaultSubcategoryId
     */
    private $defaultSubcategoryId;

    /**
     * @var string $searchThumbnail
     */
    private $searchThumbnail;

    /**
     * @var string $toolTypeName
     */
    private $toolTypeName;

    /**
     * @var bool $lightweight
     */
    private $lightweight;

    private $disclaimer;

    private $detailsTabContent;

    private $materialsTabContent;

    private $builderFlashToolId;

    private $flashToolBuilderId;

    /**
     * @param int $id
     */
    public function __construct($id) {

        $this->setId($id);

        if( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP." AND p.id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if( $query->execute() ) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    $this->storeCache($data); //Cache data so we don't have to retrieve from database again
                }
            }

            $this->imagePath['grid']             = IMAGE_URL_PREFIX.'/images/catlog/product/small/';
            $this->imagePath['medium']           = IMAGE_URL_PREFIX.'/images/catlog/product/medium/';
            $this->imagePath['large']            = IMAGE_URL_PREFIX.'/images/catlog/product/large/';
            $this->imagePath['zoom']             = IMAGE_URL_PREFIX.'/images/catlog/product/large/';
            $this->imagePath['upload_temp']      = APP_ROOT."/upload/temp/";
            $this->imagePath['upload_perm']      = APP_ROOT."/upload/";
            $this->imagePath['streetname_small'] = URL_PREFIX_HTTP."/design/save/previews/small/";

            /* SET CLASS PROPERTIES FROM $data */
            $this->setDefaultBuilderRef($data['default_builder_ref'])->setDefaultBestSeller($data['default_best_seller'])->setActive($data['active'])
                ->setProductNumber($data['product_number'])->setDefaultProductName($data['default_product_name'])->setDefaultSubtitle($data['default_subtitle'])
                ->setByLegend($data['by_legend'])->setArtworkDescription($data['artwork_description'])->setDisplayNumber($data['display_number'])->setNote($data['note'])
                ->setCustom($data['custom'])->setFreeForm($data['free_form'])->setHasArrows($data['has_arrows'])->setHasSymbols($data['has_symbols'])
                ->setViewingDistanceStandardId($data['viewing_distance_standard_id'])->setDescription($data['description'])->setStreetsignNote($data['streetsign_note'])
                ->setComplianceFile($data['compliance_file'])->setSizeIntro($data['size_intro'])->setSizeOutro($data['size_outro'])->setMaterialIntro($data['material_intro'])
                ->setMaterialOutro($data['material_outro'])->setPrintingIntro($data['printing_intro'])->setFontsDescription($data['fonts_description'])
                ->setInstallationIntro($data['installation_intro'])->setInstallationQuestionId($data['installation_question_id'])
                ->setShowCrosshatchTestInfo($data['show_crosshatch_test_info'])->setShowTextSize($data['show_text_size'])->setAdvertise($data['advertise'])
                ->setSearchable($data['searchable'])->setNewUntil($data['new_until'])->setExpiration($data['expiration'])->setOnSale($data['on_sale'])
                ->setSavings($data['savings'])->setPageTitle($data['page_title'])->setPageSubtitle($data['default_subtitle'])->setMetaDescription($data['meta_description'])
                ->setMetaKeywords($data['meta_keywords'])->setSearchKeywords($data['search_keywords'])->setSitemapShow($data['sitemap_show'])->setUrlSlug($data['url_slug'])
                ->setCanonicalPageUrlId($data['canonical_page_url_id'])->setMultiLingual($data['language_count'])->setBiLingual($data['language_id'])->setToolTypeName($data['toolTypeName'])
                ->setTweakId($data['tweak_id'])->setCustomAlternativePageType($data['custom_alternative_page_type'])->setCustomAlternativePageId($data['custom_alternative_page_id'])
                ->setPagePriority($data['page_priority'])->setBuilderTweakToolId($data['builder_tweak_tool_id'])->setProductPageTemplateId($data['product_page_template_id'])
                ->setHeaderId($data['header_id'])->setLanguageId($data['language_id'])->setChangeFrequencyId($data['change_frequency_id'])
                ->setComplianceTabPosition($data['compliance_tab_position'])->setSizeTabPosition($data['size_tab_position'])->setInstallationTabPosition($data['installation_tab_position'])
                ->setMaterialTabPosition($data['material_tab_position'])->setPrintingTabPosition($data['printing_tab_position'])->setShowPrintingInfo($data['show_printing_info'])
                ->setShowMaterialIllustrations($data['show_material_illustrations'])->setDefaultPreconfiguredSkuId($data['default_preconfigured_sku_id'])
                ->setDefaultTranslationFamilyId($data['default_translation_family_id'])->setDefaultToolTypeId($data['default_tool_type_id'])->setProductSkuIds($data['array_product_skus'])
                ->setDefaultFlashToolId($data['default_flash_tool_id'])->setDefaultStreetsignToolId($data['default_streetsign_tool_id'])->setDefaultLandingId($data['default_landing_id'])
                ->setDefaultSubcategoryId($data['default_subcategory_id'])->setSearchThumbnail($data['search_thumbnail'])->setLightweight($data['lightweight'])->setDisclaimer($data['disclaimer'])
                ->setDetailsTabContent($data['details_tab_content'])->setMaterialsTabContent($data['materials_tab_content'])->setBuilderFlashToolId($data['flash_tool_id'])->setFlashToolBuilderId($data['builder_id']);

        }else{

            // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load properties: \''.$this->getId().'\' is not a valid ID number.');
        }
        // Pass the info up to the parent cart product
        parent::__construct(self::TYPE, $this->getId());
    }

    /*************************************************
     * Start Setters
     **************************************************
     * Set the Product Page Ids
     *
     * @param int $id
     * @return ProductPage()
     */
    public function setId($id) {

        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
        return $this;
    }


    /**
     * @param $active
     * @return ProductPage()
     */
    public function setActive($active) {

        $this->active = $active ? TRUE : FALSE;
        return $this;
    }


    /**
     * @param $tweakId
     * @return ProductPage()
     */
    public function setTweakId($tweakId) {

        $this->tweakId = isset($tweakId) && is_numeric($tweakId) && $tweakId > 0 ? (int) $tweakId : NULL;
        return $this;
    }

    public function setProductSkuIds($productSkuIds) {

        $this->productSkuIds = $productSkuIds;
        return $this;
    }

    public function setBiLingual($languageId) {

        $this->biLingual = (int) $languageId == 3 ? TRUE : FALSE;
        return $this;

    }

    public function setMultiLingual($multiLingual) {

        $this->multiLingual = $multiLingual > 1 ? TRUE : FALSE;
        return $this;
    }

    public function setLightweight($lightweight) {

        $this->lightweight = $lightweight > 1 ? TRUE : FALSE;
        return $this;
    }

    /**
     * @param bool $advertise
     * @return ProductPage()
     */
    public function setAdvertise($advertise = FALSE) {

        $this->advertise = $advertise ? TRUE : FALSE;
        return $this;
    }


    public function setToolTypeName($toolTypeName) {
        $this->toolTypeName = isset($toolTypeName) ? trim($toolTypeName) : NULL;
        return $this;
    }

    /**
     * @param $artworkDescription
     * @return ProductPage()
     */
    public function setArtworkDescription($artworkDescription) {

        $this->artworkDescription = isset($artworkDescription) ? trim($artworkDescription) : NULL;
        return $this;
    }


    /**
     * @param $builderTweakToolId
     * @return ProductPage()
     */
    public function setBuilderTweakToolId($builderTweakToolId) {

        $this->builderTweakToolId = isset($builderTweakToolId) && is_numeric($builderTweakToolId)
        && $builderTweakToolId > 0 ? (int) $builderTweakToolId : NULL;
        return $this;
    }


    /**
     * @param $byLegend
     * @return ProductPage()
     */
    public function setByLegend($byLegend) {

        $this->byLegend = $byLegend;
        return $this;
    }


    /**
     * @param $canonicalPageUrlId
     * @return ProductPage()
     */
    public function setCanonicalPageUrlId($canonicalPageUrlId) {

        $this->canonicalPageUrlId = isset($canonicalPageUrlId) && is_numeric($canonicalPageUrlId)
        && $canonicalPageUrlId > 0 ? (int) $canonicalPageUrlId : NULL;
        return $this;
    }


    /**
     * @param $changeFrequencyId
     * @return ProductPage()
     */
    public function setChangeFrequencyId($changeFrequencyId) {

        $this->changeFrequencyId = isset($changeFrequencyId) && is_numeric($changeFrequencyId)
        && $changeFrequencyId > 0 ? (int) $changeFrequencyId : NULL;
        return $this;
    }


    /**
     * @param $complianceFile
     * @return ProductPage()
     */
    public function setComplianceFile($complianceFile) {

        $this->complianceFile = $complianceFile;
        return $this;
    }


    /**
     * @param $complianceTabPosition
     * @return ProductPage()
     */
    public function setComplianceTabPosition($complianceTabPosition) {

        $this->complianceTabPosition = $complianceTabPosition;
        return $this;
    }


    /**
     * @param $custom
     * @return ProductPage()
     */
    public function setCustom($custom = FALSE) {

        $this->custom = (bool)$custom;
        return $this;
    }


    /**
     * @param $customAlternativePageId
     * @return ProductPage()
     */
    public function setCustomAlternativePageId($customAlternativePageId) {

        $this->customAlternativePageId = isset($customAlternativePageId) && is_numeric($customAlternativePageId)
        && $customAlternativePageId > 0 ? (int) $customAlternativePageId : NULL;
        return $this;
    }


    /**
     * @param $customAlternativePageType
     * @return ProductPage()
     */
    public function setCustomAlternativePageType($customAlternativePageType) {

        $this->customAlternativePageType = $customAlternativePageType;
        return $this;
    }


    /**
     * @param $defaultBestSeller
     * @return ProductPage()
     */
    public function setDefaultBestSeller($defaultBestSeller) {

        $this->defaultBestSeller = $defaultBestSeller;
        return $this;
    }

    /**
     * @param $defaultBuilderRef
     * @return ProductPage()
     */
    public function setDefaultBuilderRef($defaultBuilderRef) {

        $this->defaultBuilderRef = $defaultBuilderRef;
        return $this;
    }


    /**
     * @param $defaultFlashToolId
     * @return ProductPage()
     */
    public function setDefaultFlashToolId($defaultFlashToolId) {

        $this->defaultFlashToolId = isset($defaultFlashToolId) && is_numeric($defaultFlashToolId)
        && $defaultFlashToolId > 0 ? (int) $defaultFlashToolId : NULL;
        return $this;
    }


    /**
     * @param $searchThumbnail
     * @return ProductPage()
     */
    public function setSearchThumbnail($searchThumbnail) {

        $this->searchThumbnail = !empty($searchThumbnail) ? $searchThumbnail : NULL;
        return $this;
    }


    /**
     * @param $defaultLandingId
     * @return ProductPage()
     */
    public function setDefaultLandingId($defaultLandingId) {

        $this->defaultLandingId = isset($defaultLandingId) && is_numeric($defaultLandingId)
        && $defaultLandingId > 0 ? (int) $defaultLandingId : NULL;
        return $this;
    }


    /**
     * @param $defaultPreconfiguredSkuId
     * @return ProductPage()
     */
    public function setDefaultPreconfiguredSkuId($defaultPreconfiguredSkuId) {

        $this->defaultPreconfiguredSkuId = isset($defaultPreconfiguredSkuId) && is_numeric($defaultPreconfiguredSkuId)
        && $defaultPreconfiguredSkuId > 0 ? (int) $defaultPreconfiguredSkuId : NULL;
        return $this;
    }


    /**
     * @param $defaultProductName
     * @return ProductPage()
     */
    public function setDefaultProductName($defaultProductName) {

        $this->defaultProductName = !empty($defaultProductName) ? $defaultProductName : NULL;
        return $this;
    }


    /**
     * @param $defaultStreetsignToolId
     * @return ProductPage()
     */
    public function setDefaultStreetsignToolId($defaultStreetsignToolId) {

        $this->defaultStreetsignToolId = isset($defaultStreetsignToolId) && is_numeric($defaultStreetsignToolId)
        && $defaultStreetsignToolId > 0 ? (int) $defaultStreetsignToolId : NULL;
        return $this;
    }


    /**
     * @param $defaultSubcategoryId
     * @return ProductPage()
     */
    public function setDefaultSubcategoryId($defaultSubcategoryId) {

        $this->defaultSubcategoryId = isset($defaultSubcategoryId) && is_numeric($defaultSubcategoryId)
        && $defaultSubcategoryId> 0 ?(int) $defaultSubcategoryId : NULL;
        return $this;
    }


    /**
     * @param $defaultSubtitle
     * @return ProductPage()
     */
    public function setDefaultSubtitle($defaultSubtitle) {

        $this->defaultSubtitle = $defaultSubtitle;
        return $this;
    }


    /**
     * @param $defaultToolTypeId
     * @return ProductPage()
     */
    public function setDefaultToolTypeId($defaultToolTypeId) {

        $this->defaultToolTypeId = isset($defaultToolTypeId) && is_numeric($defaultToolTypeId) && $defaultToolTypeId > 0 ? (int) $defaultToolTypeId : NULL;
        return $this;
    }


    /**
     * @param $defaultTranslationFamilyId
     * @return ProductPage()
     */
    public function setDefaultTranslationFamilyId($defaultTranslationFamilyId) {

        $this->defaultTranslationFamilyId = isset($defaultTranslationFamilyId) && is_numeric($defaultTranslationFamilyId) &&
        $defaultTranslationFamilyId > 0 ? (int) $defaultTranslationFamilyId : NULL;
        return $this;
    }


    /**
     * @param $description
     * @return ProductPage()
     */
    public function setDescription($description) {

        $this->description = $description;
        return $this;
    }


    /**
     * @param $displayNumber
     * @return ProductPage()
     */
    public function setDisplayNumber($displayNumber) {

        $this->displayNumber = $displayNumber;
        return $this;
    }


    /**
     * @param $viewingDistanceStandardId
     * @return ProductPage()
     */
    public function setViewingDistanceStandardId($viewingDistanceStandardId) {

        $this->viewingDistanceStandardId = isset($viewingDistanceStandardId) && is_numeric($viewingDistanceStandardId)
        && $viewingDistanceStandardId > 0 ? (int) $viewingDistanceStandardId : NULL;
        return $this;
    }


    /**
     * @param $expiration
     * @return ProductPage()
     */
    public function setExpiration($expiration) {

        $this->expiration = $expiration;
        return $this;
    }


    /**
     * @param $fontsDescription
     * @return ProductPage()
     */
    public function setFontsDescription($fontsDescription) {

        $this->fontsDescription = $fontsDescription;
        return $this;
    }


    /**
     * @param $freeForm
     * @return ProductPage()
     */
    public function setFreeForm($freeForm) {

        $this->freeForm = $freeForm;
        return $this;
    }

    /**
     * @param $hasArrows
     * @return ProductPage()
     */
    public function setHasArrows($hasArrows = FALSE) {

        $this->hasArrows = (bool)$hasArrows;
        return $this;
    }


    /**
     * @param $hasSymbols
     * @return ProductPage()
     */
    public function setHasSymbols($hasSymbols = FALSE) {

        $this->hasSymbols = (bool)$hasSymbols;
        return $this;
    }


    /**
     * @param $headerId
     * @return ProductPage()
     */
    public function setHeaderId($headerId) {

        $this->headerId = isset($headerId) && is_numeric($headerId) && $headerId > 0 ? (int) $headerId : NULL;
        return $this;
    }


    /**
     * @param $installationIntro
     * @return ProductPage()
     */
    public function setInstallationIntro($installationIntro) {

        $this->installationIntro = $installationIntro;
        return $this;
    }


    /**
     * @param $installationQuestionId
     * @return ProductPage()
     */
    public function setInstallationQuestionId($installationQuestionId) {

        $this->installationQuestionId = isset($installationQuestionId) && is_numeric($installationQuestionId)
        && $installationQuestionId > 0 ? (int) $installationQuestionId : NULL;
        return $this;
    }


    /**
     * @param $installationTabPosition
     * @return ProductPage()
     */
    public function setInstallationTabPosition($installationTabPosition) {

        $this->installationTabPosition = $installationTabPosition;
        return $this;
    }


    /**
     * @param $languageId
     * @return ProductPage()
     */
    public function setLanguageId($languageId) {

        $this->languageId = isset($languageId) && is_numeric($languageId) && $languageId > 0 ? (int) $languageId : NULL;
        return $this;
    }


    /**
     * @param $materialIntro
     * @return ProductPage()
     */
    public function setMaterialIntro($materialIntro) {

        $this->materialIntro = isset($materialIntro) ? trim($materialIntro) : NULL;
        return $this;
    }


    /**
     * @param $materialOutro
     * @return ProductPage()
     */
    public function setMaterialOutro($materialOutro) {

        $this->materialOutro = isset($materialOutro) ? trim($materialOutro) : NULL;
        return $this;
    }


    /**
     * @param $materialTabPosition
     * @return ProductPage()
     */
    public function setMaterialTabPosition($materialTabPosition) {

        $this->materialTabPosition = $materialTabPosition;
        return $this;
    }


    /**
     * @param $metaDescription
     * @return ProductPage()
     */
    public function setMetaDescription($metaDescription) {

        $this->metaDescription = $metaDescription;
        return $this;
    }


    /**
     * @param $metaKeywords
     * @return ProductPage()
     */
    public function setMetaKeywords($metaKeywords) {

        $this->metaKeywords = $metaKeywords;
        return $this;
    }


    /**
     * @param $newUntil
     * @return ProductPage()
     */
    public function setNewUntil($newUntil) {

        $this->newUntil = $newUntil;
        return $this;
    }


    /**
     * @param $note
     * @return ProductPage()
     */
    public function setNote($note) {

        $this->note = $note;
        return $this;
    }

    /**
     * @param bool $onSale
     * @return ProductPage()
     */
    public function setOnSale($onSale = FALSE) {

        $this->onSale = $onSale ? TRUE : FALSE;
        return $this;
    }


    /**
     * @param $pagePriority
     * @return ProductPage()
     */
    public function setPagePriority($pagePriority) {

        $this->pagePriority = $pagePriority;
        return $this;
    }

    /**
     * @param $pageSubtitle
     * @return ProductPage()
     */
    public function setPageSubtitle($pageSubtitle) {

        $this->pageSubtitle = $pageSubtitle;
        return $this;
    }

    /**
     * @param $pageTitle
     * @return ProductPage()
     */
    public function setPageTitle($pageTitle) {

        $this->pageTitle = $pageTitle;
        return $this;
    }


    /**
     * @param $printingIntro
     * @return ProductPage()
     */
    public function setPrintingIntro($printingIntro) {

        $this->printingIntro = $printingIntro;
        return $this;
    }


    /**
     * @param $printingTabPosition
     * @return ProductPage()
     */
    public function setPrintingTabPosition($printingTabPosition) {

        $this->printingTabPosition = $printingTabPosition;
        return $this;
    }


    /**
     * @param $productNumber
     * @return ProductPage()
     */
    public function setProductNumber($productNumber) {

        $this->productNumber = $productNumber;
        return $this;
    }


    /**
     * @param $productPageTemplateId
     * @return ProductPage()
     */
    public function setProductPageTemplateId($productPageTemplateId) {

        $this->productPageTemplateId = $productPageTemplateId;
        return $this;
    }


    /**
     * @param $savings
     * @return ProductPage()
     */
    public function setSavings($savings) {

        $this->savings = $savings;
        return $this;
    }


    /**
     * @param $searchable
     * @return ProductPage()
     */
    public function setSearchable($searchable) {

        $this->searchable = $searchable;
        return $this;
    }


    /**
     * @param $searchKeywords
     * @return ProductPage()
     */
    public function setSearchKeywords($searchKeywords) {

        $this->searchKeywords = $searchKeywords;
        return $this;
    }


    /**
     * @param $showCrosshatchTestInfo
     * @return ProductPage()
     */
    public function setShowCrosshatchTestInfo($showCrosshatchTestInfo) {

        $this->showCrosshatchTestInfo = $showCrosshatchTestInfo;
        return $this;
    }


    /**
     * @param $showMaterialIllustrations
     * @return ProductPage()
     */
    public function setShowMaterialIllustrations($showMaterialIllustrations) {

        $this->showMaterialIllustrations = $showMaterialIllustrations;
        return $this;
    }


    /**
     * @param $showPrintingInfo
     * @return ProductPage()
     */
    public function setShowPrintingInfo($showPrintingInfo) {

        $this->showPrintingInfo = $showPrintingInfo;
        return $this;
    }


    /**
     * @param $showTextSize
     * @return ProductPage()
     */
    public function setShowTextSize($showTextSize) {

        $this->showTextSize = $showTextSize;
        return $this;
    }


    /**
     * @param $sitemapShow
     * @return ProductPage()
     */
    public function setSitemapShow($sitemapShow) {

        $this->sitemapShow = $sitemapShow;
        return $this;
    }


    /**
     * @param $sizeIntro
     * @return ProductPage()
     */
    public function setSizeIntro($sizeIntro) {

        $this->sizeIntro = $sizeIntro;
        return $this;
    }


    /**
     * @param $sizeOutro
     * @return ProductPage()
     */
    public function setSizeOutro($sizeOutro) {

        $this->sizeOutro = $sizeOutro;
        return $this;
    }


    /**
     * @param $sizeTabPosition
     * @return ProductPage()
     */
    public function setSizeTabPosition($sizeTabPosition) {

        $this->sizeTabPosition = $sizeTabPosition;
        return $this;
    }


    /**
     * @param $streetsignNote
     * @return ProductPage()
     */
    public function setStreetsignNote($streetsignNote) {

        $this->streetsignNote = $streetsignNote;
        return $this;
    }

    /**
     * @param $urlSlug
     * @return ProductPage()
     */
    public function setUrlSlug($urlSlug) {

        $this->urlSlug = $urlSlug;
        return $this;
    }

    public function setDisclaimer($disclaminer) {

        $this->disclaimer = trim($disclaminer);
        return $this;
    }

    public function setDetailsTabContent($detailsTabContent) {
        $this->detailsTabContent = trim($detailsTabContent);
        return $this;
    }

    public function setMaterialsTabContent($materialsTabContent) {
        $this->materialsTabContent = trim($materialsTabContent);
        return $this;
    }

    public function setBuilderFlashToolId($builderFlashToolId){
        $this->builderFlashToolId = (int) $builderFlashToolId;
        return $this;
    }

    public function setFlashToolBuilderId($flashToolBuilderId) {
        $this->flashToolBuilderId = $flashToolBuilderId;
        return $this;
    }

    /*************************************************
     * Start Getters
     **************************************************
     **
     * @return int $id
     */
    public function getId() {

        return $this->id;
    }


    /**
     * @return bool|int
     */
    public function getAdvertise() {

        return $this->advertise;
    }


    /**
     * @return bool|int
     */
    public function isActive() {

        return $this->active;
    }


    /**
     * @return string
     */
    public function getArtworkDescription() {

        return $this->artworkDescription;
    }


    /**
     * @return int
     */
    public function getBuilderTweakToolId() {

        return $this->builderTweakToolId;
    }


    /**
     * @return string
     */
    public function getByLegend() {

        return $this->byLegend;
    }


    /**
     * @return int
     */
    public function getCanonicalPageUrlId() {

        return $this->canonicalPageUrlId;
    }


    /**
     * @return int
     */
    public function getChangeFrequencyId() {

        return $this->changeFrequencyId;
    }

    /**
     * @return string
     */
    public function getComplianceFile() {

        return $this->complianceFile;
    }


    /**
     * @return int
     */
    public function getComplianceTabPosition() {

        return $this->complianceTabPosition;
    }


    /**
     * @return bool|int
     */
    public function getCustom() {

        return $this->custom;
    }

    /**
     * @return mixed
     */
    public function getCustomAlternativePageId() {
        return $this->customAlternativePageId;
    }


    /**
     * @return string
     */
    public function getCustomAlternativePageType() {

        return $this->customAlternativePageType;
    }


    /**
     * @return bool|int
     */
    public function getDefaultBestSeller() {

        return $this->defaultBestSeller;
    }


    /**
     * @return int
     */
    public function getDefaultBuilderRef() {

        return $this->defaultBuilderRef;
    }

    public function getPageType() {
        return self::TYPE;
    }

    /**
     * @return int
     */
    public function getDefaultFlashToolId() {

        return $this->defaultFlashToolId;
    }

    /**
     * @return string
     */
    public function getSearchThumbnail() {

        return $this->searchThumbnail;
    }


    /**
     * @return int
     */
    public function getDefaultLandingId() {

        return $this->defaultLandingId;
    }

    /**
     * @return int
     */
    public function getDefaultPreconfiguredSkuId() {

        return $this->defaultPreconfiguredSkuId;
    }

    /**
     * @return mixed
     */
    public function getDefaultProductName() {

        return $this->defaultProductName;
    }

    /**
     * @return mixed
     */
    public function getDefaultStreetsignToolId() {

        return $this->defaultStreetsignToolId;
    }

    public function isBiLingual() {
        return $this->biLingual;
    }

    public function isMultiLingual() {
        return $this->multiLingual;
    }

    public function isLightweight () {
        return $this->lightweight;
    }

    /**
     * @return mixed
     */
    public function getDefaultSubcategoryId() {

        return $this->defaultSubcategoryId;
    }

    /**
     * @return mixed
     */
    public function getDefaultSubtitle() {

        return $this->defaultSubtitle;
    }

    /**
     * @return mixed
     */
    public function getDefaultToolTypeId() {

        return $this->defaultToolTypeId;
    }

    /**
     * @return mixed
     */
    public function getDefaultTranslationFamilyId() {

        return $this->defaultTranslationFamilyId;
    }

    /**
     * @return mixed
     */
    public function getDescription() {

        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getDisplayNumber() {

        return $this->displayNumber;
    }

    /**
     * @return mixed
     */
    public function getViewingDistanceStandardId() {

        return $this->viewingDistanceStandardId;
    }

    /**
     * @return mixed
     */
    public function getExpiration() {

        return $this->expiration;
    }

    /**
     * @return mixed
     */
    public function getFontsDescription() {

        return $this->fontsDescription;
    }

    /**
     * @return mixed
     */
    public function getFreeForm() {

        return $this->freeForm;
    }

    /**
     * @return mixed
     */
    public function getHasArrows() {

        return $this->hasArrows;
    }

    /**
     * @return mixed
     */
    public function getHasSymbols() {

        return $this->hasSymbols;
    }

    /**
     * @return mixed
     */
    public function getHeaderId() {

        return $this->headerId;
    }

    /**
     * @return mixed
     */
    public function getInstallationIntro() {

        return $this->installationIntro;
    }

    /**
     * @return mixed
     */
    public function getInstallationQuestionId() {

        return $this->installationQuestionId;
    }

    /**
     * @return mixed
     */
    public function getInstallationTabPosition() {

        return $this->installationTabPosition;
    }

    /**
     * @return mixed
     */
    public function getLanguageId() {

        return $this->languageId;
    }

    /**
     * @return mixed
     */
    public function getMaterialIntro() {

        return $this->materialIntro;
    }

    /**
     * @return mixed
     */
    public function getMaterialOutro() {

        return $this->materialOutro;
    }

    /**
     * @return mixed
     */
    public function getMaterialTabPosition() {

        return $this->materialTabPosition;
    }

    /**
     * @return mixed
     */
    public function getMetaDescription() {

        return $this->metaDescription;
    }

    /**
     * @return mixed
     */
    public function getMetaKeywords() {

        return $this->metaKeywords;
    }

    /**
     * @return mixed
     */
    public function getNewUntil() {

        return $this->newUntil;
    }

    /**
     * @return mixed
     */
    public function getNote() {

        return $this->note;
    }

    /**
     * @return mixed
     */
    public function getOnSale() {

        return $this->onSale;
    }

    /**
     * @return mixed
     */
    public function getPagePriority() {

        return $this->pagePriority;
    }

    /**
     * @return mixed
     */
    public function getPageSubtitle() {

        return $this->pageSubtitle;
    }

    /**
     * @return mixed
     */
    public function getPageTitle() {

        return $this->pageTitle;
    }

    /**
     * @return mixed
     */
    public function getPrintingIntro() {

        return $this->printingIntro;
    }

    public function getProductSkuIds() {
        return $this->productSkuIds;
    }

    /**
     * @return mixed
     */
    public function getPrintingTabPosition() {

        return $this->printingTabPosition;
    }

    /**
     * @return mixed
     */
    public function getProductNumber() {

        return $this->productNumber;
    }

    /**
     * @return mixed
     */
    public function getProductPageTemplateId() {

        return $this->productPageTemplateId;
    }

    /**
     * @return mixed
     */
    public function getSavings() {

        return $this->savings;
    }

    /**
     * @return mixed
     */
    public function getSearchable() {

        return $this->searchable;
    }

    /**
     * @return mixed
     */
    public function getSearchKeywords() {

        return $this->searchKeywords;
    }

    /**
     * @return mixed
     */
    public function getShowCrosshatchTestInfo() {

        return $this->showCrosshatchTestInfo;
    }

    /**
     * @return mixed
     */
    public function getShowMaterialIllustrations() {

        return $this->showMaterialIllustrations;
    }

    /**
     * @return mixed
     */
    public function getShowPrintingInfo() {

        return $this->showPrintingInfo;
    }

    /**
     * @return mixed
     */
    public function getShowTextSize() {

        return $this->showTextSize;
    }

    /**
     * @return mixed
     */
    public function getSitemapShow() {

        return $this->sitemapShow;
    }

    /**
     * @return mixed
     */
    public function getSizeIntro() {

        return $this->sizeIntro;
    }

    /**
     * @return mixed
     */
    public function getSizeOutro() {

        return $this->sizeOutro;
    }

    public function getImagePath($pathType) {
        return $this->imagePath[$pathType];
    }

    /**
     * Return the type of the page
     *
     * @return string self::TYPE
     */
    public function getType() {
        return self::TYPE;
    }

    /**
     * @return mixed
     */
    public function getSizeTabPosition() {

        return $this->sizeTabPosition;
    }

    public function getTweakId() {
        return $this->tweakId;
    }

    /**
     * @return mixed
     */
    public function getUrlSlug() {

        return $this->urlSlug;
    }

    /**
     * @return mixed
     */
    public function getStreetsignNote() {

        return $this->streetsignNote;
    }

    public function getDisclaimer() {
        return $this->disclaimer;
    }

    /**
     * Return an instance of Product
     *
     * @return Product
     */
    public function getProduct() {

        if( empty($this->Product) ) {
            $this->Product = Product::create($this->getId());
        }
        return $this->Product;
    }

    public function getDetailsTabContent() {
        return $this->detailsTabContent;
    }

    public function getMaterialsTabContent() {
        return $this->materialsTabContent;
    }

    /**
     * Only called when no parent page id is specified in the request
     * Instantiate subcategory page if exists, otherwise instantiate Landing.
     *
     * @return Page()  [Either SubcategoryPage LandingPage, or NULL]
     */
    public function getDefaultParentPage() {

        if( $this->defaultSubcategoryId > 0 ) {

            return new SubcategoryPage($this->defaultSubcategoryId);

        }else if( $this->defaultLandingId > 0 ) {

            return new LandingPage($this->defaultLandingId);

        }else{

            return NULL;
        }
    }

    /**
     * Gets everything from bs_products_sku_description based on the product number
     * @return    array    sku_description array
     */
    function getProductsSkuDescription() {

        $sql = Connection::getHandle()->prepare(
            "SELECT * FROM bs_products p

                     INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
                     INNER JOIN bs_skus sk ON (ps.sku_id = sk.id)

                     WHERE p.product_number = ?

                     AND (sk.inventory > 0 OR sk.limited_inventory = 0) AND sk.active = 1 AND p.active = 1
                     ORDER BY position "
        );

        $sql->execute(array($this->getProductNumber()));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

            $result[] = $row;
        }

        return $result;

    }

    /**
     * Get a specific product price
     *
     * @param null|string $skuName
     * @param null|string $productNumber
     * @return array $result
     */
    function getProductFromPrice($skuName = NULL, $productNumber = NULL) {

        $data = array ();

        $query = Connection::getHandle()->prepare(
            "SELECT sku.*, pr.*, pt.price FROM bs_products p
                LEFT JOIN bs_product_skus ps ON (ps.product_id = p.id)
                LEFT JOIN bs_skus sku ON (sku.id = ps.sku_id AND sku.active = 1)
                LEFT JOIN bs_pricing pr ON (pr.id = sku.pricing_id)
                LEFT JOIN bs_pricing_tiers pt ON (pt.pricing_id = pr.id)
            WHERE sku.name = :skuName AND p.product_number = :productNumber AND p.active = 1 ORDER BY pt.minimum_quantity"
        );

        $query->bindParam(":skuName", $skuName, PDO::PARAM_STR);
        $query->bindParam(":productNumber", $productNumber, PDO::PARAM_STR);

        if( $query->execute() ) {

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $data[] = $row;
            }
        }

        return $data;
    }


    public function getFromProductsPriceWithQuantity($productNumber = NULL, $qtys = 0, $skuCode = NULL) {

        $data = array();

        $query = Connection::getHandle()->prepare(
            "SELECT bp.*, bs.*, pt.price AS price, bs.requires_freight AS freight_shipping, pt.minimum_quantity AS quantity
            FROM bs_pricing bp

             LEFT JOIN bs_skus bs ON (bp.id = bs.pricing_id)
             LEFT JOIN bs_pricing_tiers pt ON(pt.pricing_id = bp.id)
             INNER JOIN bs_product_skus ps ON (bs.id = ps.sku_id)
             INNER JOIN bs_products p ON (ps.product_id = p.id)

             WHERE p.product_number = :productNumber AND pt.minimum_quantity <= :qtys AND bs.active = 1
             AND bs.name = :skuCode ORDER BY quantity DESC LIMIT 1"
        );

        $query->bindParam(":productNumber", $productNumber, PDO::PARAM_STR);
        $query->bindParam(":qtys", $qtys, PDO::PARAM_INT);
        $query->bindParam(":skuCode", $skuCode, PDO::PARAM_STR);

        if( $query->execute() ) {

            $data = $query->fetch(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    /**
     * Get a products attributes
     *
     * @param string $materialCode
     * @return array $data
     */
    public function getProductAttributeList($materialCode = "") {

        $data = array();

        $query = Connection::getHandle()->prepare(

            "SELECT
                psd.id AS sku_id, psd.name AS sku_code, psd.inventory AS inventory, psd.active AS active,
                psd.limited_inventory AS limited_inventory, com.name AS ul_recognized, psd.requires_freight AS freight_shipping,
                psd.max_chars_upper AS max_chars_upper, psd.made_to_order AS made_to_order, psd.absolute_maximum AS absolute_maximum,

                ps.position AS position, adv.`id` AS google_category_id, psd.lead_time AS lead_time,
                mat.name AS material, mg.`description` AS material_description, psd.streetsign_accessory_display AS streetsign_display,
                price.material_code AS material_code, price.price_rank, size.name AS size,
                p.product_number, p.savings AS sale_percentage, p.expiration AS expiration_date, ps.product_id

            FROM bs_skus psd

                INNER JOIN bs_product_skus ps ON (ps.sku_id = psd.id)
                INNER JOIN bs_products p ON (ps.product_id = p.id)
                INNER JOIN bs_sizes size ON (psd.size_id = size.id)

                LEFT JOIN bs_pricing price ON (psd.pricing_id = price.id)
                LEFT JOIN bs_materials mat ON (psd.material_id = mat.id)
                LEFT JOIN bs_material_groups mg ON (mat.material_group_id = mg.id)
                LEFT JOIN bs_advertising_categories adv ON (psd.advertising_category_id = adv.id)
                LEFT JOIN bs_sku_compliances sc ON( psd.id = sc.sku_id)
                LEFT JOIN bs_compliances com ON (com.id = sc.compliances_id)

            WHERE p.product_number = :productNumber AND psd.active = 1 AND p.active = 1 AND
                price.material_code = :materialCode AND p.id = :id LIMIT 1"
        );

        $query->bindParam(":productNumber", $this->getProductNumber(), PDO::PARAM_STR);
        $query->bindParam(":id", $this->getId(), PDO::PARAM_INT);
        $query->bindParam(":materialCode", $materialCode, PDO::PARAM_STR);

        if( $query->execute() ) {

            $data = $query->fetch(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    public function getToolTypeName() {
        return $this->toolTypeName;
    }

    public function isTweakAble() {

        return !is_null($this->getToolTypeName()) && $this->getBuilderTweakToolId() > 0 ? TRUE : FALSE;

    }

    function ProductAttributes() {

        $result = array();

        $query = Connection::getHandle()->prepare(

            "SELECT *, ps.product_id FROM bs_skus s

                     INNER JOIN bs_product_skus ps ON(ps.sku_id = s.id)
                     LEFT JOIN bs_products p ON(p.id = ps.product_id)

                     WHERE p.id = :product_id AND s.active = 1 AND (s.limited_inventory = 0 OR s.inventory > 0)"
        );

        $query->bindParam(":product_id", $this->getId(), PDO::PARAM_INT);

        if( $query->execute() ) {

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {

                $result[] = $row;
            }
        }

        return $result;
    }

    /**
     * Retrieves a list of accessories for the current product
     */
    public function getProductAccessoriesList() {
        
        $sql = Connection::getHandle()->prepare("SELECT p.id as product_id, pa.id as accessory_product_id, afp.position
                                                    FROM bs_products as p INNER JOIN bs_accessory_families as af ON p.accessory_family_id = af.id
                                                    INNER JOIN bs_accessory_family_products as afp ON af.id = afp.accessory_family_id
                                                    INNER JOIN bs_products AS pa ON afp.product_id = pa.id
                                                    WHERE p.active = TRUE AND pa.active = TRUE AND p.id = ?
                                                    GROUP BY pa.id ORDER BY position");
        $sql->execute(array($this->getId()));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;

    }

    public function getAccessories($accessory_id) {

        $sql = Connection::getHandle()->prepare("SELECT m.name AS material,
											   sku.inventory AS inventory,
											   sku.limited_inventory AS limited_inventory,
											   p.id AS products_id,
											   p.product_number AS product_number,
											   p.default_product_name AS product_nickname,
										 	   p.by_legend AS by_legend,
										 	   ps.position AS position,
										 	   sku.small_image AS image1_thumbnail
										 FROM bs_products p
										 INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
										 INNER JOIN bs_skus sku ON (sku.id = ps.sku_id AND sku.active = TRUE)
										 INNER JOIN bs_materials m ON (m.id = sku.material_id)
										 WHERE p.id = ?
                                         AND p.active= TRUE
                                         AND ( p.expiration IS NULL OR p.expiration = '0000-00-00' OR p.expiration > CURDATE() )
										 ORDER BY sku.id ASC
										 LIMIT 1");
        $sql->execute(array($accessory_id));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

    public function getProductGridDisplayCompliances() {

        $complianceList = array();
        $complianceListDisplay = array();

        $productSkusList = explode(",", $this->getProductSkuIds());

        if ( !empty($productSkusList) ) {

            $tempDisplayList = array();
            $tempCompList = array();

            foreach ( $productSkusList as $sku ) {

                $productSku = Sku::create($sku);

                $tempComplianceList = $productSku->getCompliances();

                foreach ( $tempComplianceList as $compliance ) {

                    if ( $compliance->getProductGridDisplay() == TRUE ) {

                        $tempDisplayList[$productSku->getId()][$compliance->getId()][] = $compliance;
                        $tempCompList[$compliance->getId()][] = $compliance;

                    }

                    if ( (count($tempCompList[$compliance->getId()]) >= count($productSkusList)/2) && !in_array($compliance, $complianceListDisplay) ) {

                        $complianceListDisplay[] = $compliance;

                    }

                }

            }

            $complianceList = $complianceListDisplay;

        }

        return $complianceList;

    }

    public function listAccessories() {

        //Get accessories
        $sql = Connection::getHandle()->prepare("SELECT  p2.id AS id,
										t.pagetype as pagetype,
										TRUE as validity,
										p2.default_product_name AS nickname,
										p2.default_product_name AS name,
										u.url AS short_url,
										t.template_secure AS secure,
										t.template_filename AS filename,
										p2.page_title AS title,
										p2.meta_description AS meta_description,
										p2.meta_keywords AS meta_keywords,
										h.name AS heading,
										t.allow_target AS allow_target,
										t.requires_login AS requires_login,
										t.disallow_guests AS disallow_guests,
										p2.sitemap_show AS visibility,
										p2.page_priority AS priority,
										cf.name AS change_frequency,
										cat.name AS category_proper,
										grp.name AS grouping_proper,
										sub.name AS subcategory_proper,
										sku.small_image,
										sku.inventory AS inventory,
										sku.limited_inventory AS limited_inventory,
										xq.languages AS `languages`,
										xq.`language` AS `language`,
										xq.language_count AS language_count,
										b.lightweight,
										CASE WHEN p2.new_until > CURDATE() THEN p2.new_until
											ELSE '0'
										END AS expiration,
										p2.*
										FROM (
                                                SELECT DISTINCT p.id as product_id, pa.id as accessory_id, afp.position
                                                FROM bs_products as p INNER JOIN bs_accessory_families as af ON p.accessory_family_id = af.id
                                                INNER JOIN bs_accessory_family_products as afp ON af.id = afp.accessory_family_id
                                                INNER JOIN bs_products AS pa ON afp.product_id = pa.id
											    WHERE p.id = ? AND p.active = TRUE AND pa.active = TRUE
												ORDER BY afp.position, pa.id
                                        ) q
										JOIN bs_pagetypes t
										LEFT JOIN bs_products p2 ON (p2.id = q.accessory_id)
										LEFT JOIN bs_product_skus ps ON (ps.product_id = p2.id)
										LEFT JOIN bs_headers h ON (h.id = p2.header_id)
										LEFT JOIN bs_change_frequencies cf ON (cf.id = p2.change_frequency_id)
										LEFT JOIN bs_skus sku ON (sku.id = ps.sku_id AND sku.active = TRUE)
										LEFT JOIN bs_builders b ON (b.builder_ref = p2.default_builder_ref AND b.active = TRUE)
										LEFT JOIN (SELECT x.languages, x.language, x.language_count, tfp.product_id
                                FROM bs_translation_family_products tfp
                                INNER JOIN (SELECT GROUP_CONCAT(DISTINCT l.name) AS languages, l.name AS language, COUNT(DISTINCT l.name) AS language_count, xtfp.translation_family_id
                                FROM bs_translation_family_products xtfp
                                INNER JOIN bs_products p ON (p.id = xtfp.product_id) INNER JOIN bs_languages l ON (l.id = p.language_id)
                                GROUP BY xtfp.translation_family_id) x ON (x.translation_family_id = tfp.translation_family_id)) xq
										ON (xq.product_id = p2.id)
										LEFT JOIN bs_page_urls u ON (u.id = p2.canonical_page_url_id AND u.pagetype = t.pagetype AND u.pageid = p2.id)
										LEFT JOIN bs_subcategory_products sp ON (sp.product_id = p2.id)
										LEFT JOIN bs_subcategories sub ON (sub.id = sp.subcategory_id)
										LEFT JOIN bs_groupings grp ON (grp.id = sub.grouping_id)
										LEFT JOIN bs_categories cat ON (cat.id = grp.category_id)
										GROUP BY p2.id
										HAVING SUM(IF(sku.inventory > 0 OR sku.limited_inventory = FALSE, 1, 0)) > 0
										ORDER BY q.position");
        $sql->execute(array($this->getId()));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $accessories[] = $row;
        }

        $related_product = array();
        //Get related products
        $sql2 = Connection::getHandle()->prepare("SELECT p.id AS canonical, sku.small_image, pr.title AS by_legend, pr.subtitle AS product_nickname, p.savings
                                                    FROM bs_product_recommendations pr
                                                    LEFT JOIN bs_products p ON (p.id = pr.product_id AND p.active = TRUE)
                                                    INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
                                                    INNER JOIN bs_skus sku ON (sku.id = ps.sku_id AND sku.active =TRUE)
                                                    LEFT JOIN bs_subcategory_products sp ON (sp.product_id = p.id)
                                                    LEFT JOIN bs_subcategories s ON (s.id = sp.subcategory_id AND s.active = TRUE)
                                                    LEFT JOIN bs_groupings g ON (g.id = s.grouping_id AND g.active = TRUE)
                                                    LEFT JOIN bs_categories cat ON (cat.id = g.category_id AND cat.active = TRUE)
                                                    WHERE p.id = ?
                                                    AND (p.expiration IS NULL OR p.expiration = '0000-00-00' OR p.expiration > CURDATE() )
                                                    AND pr.active = TRUE
                                                    GROUP BY p.product_number
                                                    HAVING SUM(IF(sku.inventory > 0 OR sku.limited_inventory = FALSE, 1, 0)) > 0");

        $sql2->execute(array($this->getId()));

        while($row2 = $sql2->fetch(PDO::FETCH_ASSOC)){
            $related_product[] = $row2;
        }

        $result['related_products'] = $related_product;
        $result['accessories'] = $accessories;

        return $result;
    }

    function streetsignProductAttributesAccessoriesList($accessoryProductNumber) {

        $results = array();
        $sql = Connection::getHandle()->prepare("SELECT sku.id as sku_id, sku.name AS sku_code, sku.material_id, sku.streetsign_accessory_display, p.product_number, pt.price, pt.minimum_quantity, pt.pricing_id, sku.accessory_size_description AS size_name
                                                FROM bs_products p
                                                INNER JOIN bs_product_skus ps ON (ps.product_id = p.id)
                                                INNER JOIN bs_skus sku ON (sku.id = ps.sku_id)
                                                INNER JOIN bs_pricing pr ON (pr.id = sku.pricing_id)
                                                INNER JOIN bs_pricing_tiers pt ON (pt.pricing_id = pr.id)
                                                LEFT JOIN bs_sizes size ON (size.id = sku.size_id AND size.active = TRUE)
                                                WHERE p.product_number = ? AND pt.active = TRUE
                                                AND sku.streetsign_accessory_display = 1 GROUP BY sku.id ORDER BY pt.price;");

        $sql->execute(array($accessoryProductNumber));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $results[] = $row;
        }

        return $results;

    }

    public function getBuilderFlashToolId(){
        return $this->builderFlashToolId;
    }

    public function getFlashToolBuilderId(){
        return $this->flashToolBuilderId;
    }

    public function getBuilderrefById($builderId = NULL ){

        $sql_name = "SELECT builder_ref FROM bs_builders WHERE id = :id AND active = TRUE";
        $sql_row = Connection::getHandle()->prepare($sql_name);
        $sql_row->bindParam(':id', $builderId, PDO::PARAM_INT);

        if( $sql_row->execute() ) {

            $data = $sql_row->fetch(PDO::FETCH_ASSOC);

        }

        return $data['builder_ref'];

    }

    public function getNoneTweakableDisclaimer() {

        $sql = "SELECT disclaimer FROM bs_product_builders WHERE builder_ref = ? AND product_id = ?";
        $sql = Connection::getHandle()->prepare($sql);
        $sql->execute(array($this->getDefaultBuilderRef(),$this->getId()));

        if( $sql->execute() ) {

            $data = $sql->fetch(PDO::FETCH_ASSOC);

        }

        return $data['disclaimer'];

    }


    /**
     * @param null $id
     * @return ProductPage
     */
    public static function create($id = NULL) {
        return new self($id);
    }
}