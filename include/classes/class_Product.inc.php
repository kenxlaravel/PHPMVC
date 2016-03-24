<?php

class Product extends CacheableEntity {

	/**
	 * File Upload path
	 */
	const UPLOADPATH = "{APP_ROOT}/upload/";

	/**
	 * Image path
	 */
	const IMAGEPATH  = "http://www.safetysign.com/images/catlog/product/medium/";

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
    **/
    const FULL_TABLE_DUMP = "
            SELECT


            sp.product_name AS subcategory_product_name, sp.best_seller AS subcategory_best_seller, sp.builder_ref AS subcategory_builder_ref,
		    sp.flash_tool_id AS subcategory_flash_tool_id, sp.image AS subcategory_product_image,
		    sp.preconfigured_sku_id AS subcategory_product_preconfigured_sku_id, sp.product_id AS subcategory_product_id,
		    sp.streetsign_tool_id AS subcategory_product_streetsign_tool_id, sp.subcategory_id AS subcategory_id,
			sp.subtitle AS subcategory_product_subtitle, sp.tool_type_id AS subcategory_product_tool_type_id, sp.translation_family_id AS subcategory_product_translation_family_id,
		    lp.flash_tool_id AS landing_product_flash_tool_id, lp.image AS landing_product_image, lp.preconfigured_sku_id AS landing_product_preconfigured_sku_id, lp.product_id AS landing_product_id,
			lp.best_seller AS landing_product_best_seller, lp.builder_ref AS landing_product_builder_ref, lp.product_name AS landing_product_name,
			lp.streetsign_tool_id AS landing_product_streetsign_tool_id, lp.landing_id AS landing_id, lp.subtitle AS landing_product_subtitle, lp.tool_type_id AS landing_product_tool_type_id, lp.translation_family_id AS landing_product_translation_family_id,


            product.id AS id, product.default_builder_ref, product.default_best_seller, product.is_manufactured,
			product.active, product.product_number, product.default_product_name,
			product.default_subtitle, product.by_legend, product.artwork_description,
			product.display_number, product.note, product.custom, product.free_form,
			product.has_arrows, product.has_symbols, product.viewing_distance_standard_id,
			product.description, product.streetsign_note, product.compliance_file,
			product.size_intro, product.size_outro, product.material_intro,
			product.material_outro, product.printing_intro, product.fonts_description,
			product.installation_intro, product.installation_question_id,
			product.show_crosshatch_test_info, product.show_text_size, product.advertise,
			product.searchable, product.new_until, product.expiration, product.on_sale,
			product.savings, product.page_title, product.page_subtitle, product.meta_description,
			product.meta_keywords, product.search_keywords, product.sitemap_show,
			product.url_slug, product.canonical_page_url_id,
			product.custom_alternative_page_type, product.custom_alternative_page_id,
			product.custom_alternative_tool_type_id,
			product.custom_alternative_preconfigured_sku_id, product.custom_alternative_builder_ref,
			product.custom_alternative_flash_tool_id, product.custom_alternative_streetsign_tool_id,
			product.page_priority, product.builder_tweak_tool_id, product.product_page_template_id,
			product.header_id, product.language_id, product.change_frequency_id,
			product.compliance_tab_position, product.size_tab_position,
			product.material_tab_position, product.printing_tab_position, product.show_printing_info,
			product.show_material_illustrations, product.default_preconfigured_sku_id,
			product.default_translation_family_id, product.default_tool_type_id,
			product.default_streetsign_tool_id, product.default_landing_id, product.default_subcategory_id,
			product.accessory_family_id AS accessory_family_id, vds.name as vDistance_name,
			product.installation_tab_position, product.default_flash_tool_id,
			vds.description as vDistance_description, vds.formula as vDistance_formula,
			btt.builder_ref AS builder_tweak_tool_builder_ref, skus.large_image as image,


			GROUP_CONCAT(DISTINCT recommendations.id) AS array_recom_ids,
			GROUP_CONCAT(DISTINCT pst.streetsign_tool_id) AS array_streetsign_ids,
			GROUP_CONCAT(DISTINCT ps.sku_id) AS array_sku_ids,
            GROUP_CONCAT(DISTINCT pr.material_code) AS array_materialCodes,
            GROUP_CONCAT(DISTINCT pt.price) AS array_price,
            GROUP_CONCAT(DISTINCT afp.id) AS accessory_family_id_array,
            GROUP_CONCAT(DISTINCT collection.product_collection_id) as product_collection_ids


            FROM bs_products AS product

            LEFT JOIN bs_product_recommendations AS recommendations ON (recommendations.product_id = product.id and recommendations.active = 1)
            LEFT JOIN bs_product_skus AS ps ON (ps.product_id = product.id)
			LEFT JOIN bs_product_streetsign_tools pst ON (pst.product_id = product.id)
			LEFT JOIN bs_accessory_family_products afp ON (product.accessory_family_id = afp.accessory_family_id)
			LEFT JOIN bs_product_collection_products collection ON (collection.product_id = product.id)
			LEFT JOIN bs_viewing_distance_standards vds ON(vds.id = product.viewing_distance_standard_id)
			LEFT JOIN bs_builder_tweak_tools btt ON (btt.id = product.builder_tweak_tool_id AND btt.active = 1)

			LEFT JOIN bs_subcategory_products sp ON (product.id = sp.product_id)
			LEFT JOIN bs_landing_products lp ON (product.id = lp.product_id)

			INNER JOIN bs_skus skus ON (ps.sku_id = skus.id AND skus.active = 1)
            LEFT JOIN bs_pricing pr ON (skus.pricing_id = pr.id)
            LEFT JOIN bs_pricing_tiers pt ON (pr.id = pt.pricing_id) WHERE product.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     **/
    const ADDITIONAL_CLAUSES = " GROUP BY id ";

	/**
	 * ID of the product
	 * DB column: bs_products.id.
	 *
	 * @var int $id
	 **/
	private $id;

	/**
	 * DB Column: bs_product.default_best_seller / bs_subcategory_products.best_seller
	 *
	 * @var bool $defaultBestSeller
	 * */
    private $bestSeller;

	/**
	 * DB Column: bs_product.active
	 *
	 * @var bool $active
	 * */
	private $active;

	/**
	 * DB Column: bs_product.product_number
	 *
	 * @var string $productNumber
	 * */
	private $productNumber;

	/**
	 * @var string $productName;
	 */
    private $productName;

	/**
	 * DB Column: bs_product.default_subtitle
	 *
	 * @var string $defaultSubtitle
	 * */
    private $subtitle;

    private $toolTypeId;

    private $preconfiguredSkuId;

    private $streetsignToolId;

    private $landingId;

    private $subcategoryId;

    private $translationFamilyId;

    /**
     * @var float $customAlternativePageId
     */
    private $customAlternativePageId;

	/**
	 * DB Column: bs_product.by_legend
	 *
	 * @var string $byLegend
	 * */
	private $byLegend;

	/**
	 * DB Column: bs_product.artwork_description
	 *
	 * @var string $artworkDescription
	 * */
	private $artworkDescription;

    /**
     * @var array $recommendedProducts
     */
    private $recommendedProducts;

	/**
	 * DB Column: bs_product.display_number
	 *
	 * @var string $displayNumber
	 * */
	private $displayNumber;

	/**
	 * DB Column: bs_product.note
	 *
	 * @var string $note
	 * */
	private $note;

	/**
	 * DB Column: bs_product.custom
	 *
	 * @var bool $custom
	 * */
	private $custom;

	/**
	 * DB Column: bs_product.free_form
	 *
	 * @var bool $freeForm
	 * */
	private $freeForm;

	/**
	 * DB Column: bs_product.has_arrows
	 *
	 * @var bool $hasArrows
	 * */
	private $hasArrows;

	/**
	 * DB Column: bs_product.has_symbols
	 *
	 * @var bool $hasSymbols
	 * */
	private $hasSymbols;

	/**
	 * @var float $vdFormula
	 */
	private $vdFormula;

	/**
	 * DB Column: bs_product.viewing_distance_standard_id
	 *
	 * @var int $viewingDistanceStandardId
	 * */
	private $viewingDistanceStandardId;

	/**
	 * DB Column: bs_product.description
	 *
	 * @var string $description
	 * */
	private $description;

	/**
	 * DB Column: bs_product.streetsign_note
	 *
	 * @var string $streetsignNote
	 * */
	private $streetsignNote;

	/**
	 * DB Column: bs_product.compliance_file
	 *
	 * @var string $complianceFile
	 * */
	private $complianceFile;

	/**
	 * @var int $cononicalPageUrlId
	 */
	private $cononicalPageUrlId;

	/**
	 * DB Column: bs_product.size_intro
	 *
	 * @var string $sizeIntro
	 * */
	private $sizeIntro;

	/**
	 * DB Column: bs_product.size_outro
	 *
	 * @var string $sizeOutro
	 * */
	private $sizeOutro;

	/**
	 * DB Column: bs_product.material_intro
	 *
	 * @var string $materialIntro
	 * */
	private $materialIntro;

	/**
	 * DB Column: bs_product.material_outro
	 *
	 * @var string $materialOutro
	 * */
	private $materialOutro;

	/**
	 * @var int $streetnameToolId
	 */
	private $streetnameToolId;

	/**
	 * DB Column: bs_product.printing_intro
	 *
	 * @var string $printingIntro
	 * */
	private $printingIntro;

	/**
	 * DB Column: bs_product.fonts_description
	 *
	 * @var string $fontsDescription
	 * */
	private $fontsDescription;

	/**
	 * DB Column: bs_product.installation_intro
	 *
	 * @var string $installationIntro
	 * */
	private $installationIntro;

	/**
	* DB Column: bs_product.installation_question_id
	*
	* @var int $installationQuestionId
	*/
	private $installationQuestionId;

	/**
	 * Holds object of class InstallationQuestion()
	 * DB Column: bs_product.installation_question_id
	 *
	 * @filesource class_InstallationQuestion.inc.php
	 * @var object $InstallationQuestion
	 * */
	private $InstallationQuestion;

	/**
	 * DB Column: bs_product.show_crosshatch_test_info
	 *
	 * @var int|bool $showCrosshatchTestInfo
	 * */
	private $showCrosshatchTestInfo;

	/**
	 * DB Column: bs_product.show_text_size
	 *
	 * @var int|bool $showTextSize
	 * */
	private $showTextSize;

	/**
	 * DB Column: bs_product.advertise
	 *
	 * @var int|bool $advertise
	 * */
	private $advertise;

	/**
	 * DB Column: bs_product.searchable
	 *
	 * @var int|bool $searchable
	 * */
	private $searchable;

	/**
	 * DB Column: bs_product.new_until
	 *
	 * @var string date $newUntil
	 * */
	private $newUntil;

	/**
	 * DB Column: bs_product.expiration
	 *
	 * @var string date $expiration
	 * */
	private $expiration;

	/**
	 * DB Column: bs_product.on_sale
	 *
	 * @var int|bool $onSale
	 * */
	private $onSale;

	/**
	 * DB Column: bs_product.savings
	 *
	 * @var float $savings
	 * */
	private $savings;

	/**
	 * DB Column: bs_product.page_title
	 *
	 * @var string $pageTitle
	 * */
	private $pageTitle;

	/**
	 * @var int $customAlternativeToolTypeId
	 */
	private $customAlternativeToolTypeId;

	/**
	 * DB Column: bs_product.page_subtitle
	 *
	 * @var string $pageSubtitle
	 * */
	private $pageSubtitle;

	/**
	 * DB Column: bs_product.meta_description
	 *
	 * @var string $metaDescription
	 * */
	private $metaDescription;

	/**
	 * DB Column: bs_product.meta_keywords
	 *
	 * @var string $metaKeywords
	 * */
	private $metaKeywords;

	/**
	 * DB Column: bs_product.search_keywords
	 *
	 * @var string $searchKeywords
	 * */
	private $searchKeywords;

	/**
	 * DB Column: bs_product.sitemap_show
	 *
	 * @var int|bool $sitemapShow
	 * */
	private $sitemapShow;

	/**
	 * DB Column: bs_product.url_slug
	 *
	 * @var string $urlSlug
	 * */
	private $urlSlug;

	/**
	 * DB Column: bs_Product.canonical_page_url_id
	 *
	 * @var PageProduct $canonicalPageUrl
	 * */
	private $CanonicalPage;

	/**
	 * DB Column: bs_product.custom_alternative_page_type
	 *
	 * @var string $customAlternativePageType
	 * */
	private $customAlternativePageType;

	/**
	 * DB Column: bs_product.custom_alternative_page_id
	 *
	 * @var Page $customAlternativePage
	 * */
	private $customAlternativePage;

	/**
	 * DB Column: bs_product.page_priority
	 *
	 * @var float $pagePriority
	 * */
	private $pagePriority;

	/**
	 * DB Column: bs_product.builder_tweak_tool_id
	 *
	 * @var int $builderTweakToolId
	 * */
	private $builderTweakToolId;

	/**
	 * Holds object of ProductPageTemplate()
	 * DB Column: bs_product.product_page_template_id
	 *
	 * @filesource class_ProductPageTemplate.inc.php
	 * @var object $productPageTemplate
	 * */
	private $ProductPageTemplate;

	/**
	 * DB Column: bs_product.header_id
	 *
	 * @var int $headerId
	 * */
	private $Header;

    /**
     * @var int $languageId
     */
    private $languageId;

	/**
	 * Holds object of class Language()
	 * DB Column: bs_product.language_id
	 *
	 * @var Language object $language
	 * */
	private $language;

	/**
	 * @var array $imageArray
	 */
	private $imageArray = array();

	/**
	 * DB Column: bs_product.change_frequency_id
	 *
	 * @var int $changeFrequencyId
	 * */
	private $ChangeFrequency;

	/**
	 * DB Column: bs_product.compliance_tab_position
	 *
	 * @var int $complianceTabPosition
	 * */
	private $complianceTabPosition;

	/**
	 * DB Column: bs_product.size_tab_position
	 *
	 * @var int $sizeTabPosition
	 * */
	private $sizeTabPosition;

	/**
	 * DB Column: bs_product.installation_tab_position
	 *
	 * @var int $installationTabPosition
	 * */
	private $installationTabPosition;

	/**
	 * DB Column: bs_product.material_tab_position
	 *
	 * @var int $materialTabPosition
	 * */
	private $materialTabPosition;

	/**
	 * DB Column: bs_product.printing_tab_position
	 *
	 * @var int $printingTabPosition
	 * */
	private $printingTabPosition;

	/**
	 * DB Column: bs_product.show_printing_info
	 *
	 * @var int|bool $showPrintingInfo
	 * */
	private $showPrintingInfo;

	/**
	 * DB Column: bs_product.show_material_illustrations
	 *
	 * @var int|bool $showMaterialIllustrations
	 * */
	private $showMaterialIllustrations;

	/**
	 * An array of Font objects.
	 * Array is composed of font_id from DB column: bs_product_fonts.font_id belonging to the product id.
	 *
	 * @var array $fonts
	 */
	private $fonts;

	/**
	* Array of product collection ids
	* DB table: bs_product_collections.
	*
	* @var array $productCollectionIds
	*/
	private $productCollectionIds;

	/**
	 * An array of ProductCollection objects.
	 * DB table: bs_product_collections.
	 * @var int $productCollections
	 */
	private $productCollections;

	/**
	* Array of product recommendation ids.
	* DB table: bs_product_recommendations.
	*
	* @var array $productRecommendationIds.
	*/
	private $productRecommendationIds;

	/**
	 * @var int $fflashToolIds
	 */
	private $flashToolIds;

    private $FlashToolId;

	/**
	 * An array of ProductRecommendation objects.
	 * DB table: bs_product_recommendations.
	 *
	 * @var object $productRecommendations
	 */
	private $productRecommendations;

	/**
	* Array of translation family ids.
	* DB table: bs_translation_family_products.
	*
	* @var array $translationFamilies.
	*/
	private $translationFamilyIds;

	/**
	 * An array of ToolType objects.
	 *
	 * @var array $toolTypes
	 */
	private $toolTypes;

	/**
	 * @var string $productPage
	 */
	private $productPage;

	/**
	 * Holds object of class FlashTool().
	 *
	 * @filesource class_FlashTool.inc.php
	 * @var array $flashTools
	 */
	private $flashTools;

	/**
	 * An array of StreetNameTool() objects.
	 * DB table: bs_product_streetsign_tools.
	 *
	 * @filesource class_StreetNameTool.inc.php
	 * @var array $streetsignTools
	 */
	private $streetsignTools;

	/**
	 * An array of BuilderProduct() objects
	 * DB table: bs_product_builders.
	 *
	 * @filesource class_BuilderProduct.inc.php
	 * @var array $builderRefs
	 */
    private $builderRef;

	/**
	 * @var int $builderRefIds
	 */
	private $builderRefIds;

	/**
	 * DB Column: bs_product.search_thumbnail
	 *
	 * @var array searchThumbnail
	 * */
	private $searchThumbnail = array();

	/**
	* Array of sku Ids
	* DB table: bs_skus.id
	*
	* @var array $skuIds
	*/
	private $skuIds;

	/**
	 * Sku object
	 * DB column: bs_products.default_preconfigured_sku_id.
	 *
	 * @var int $preconfiguredSku
	 */
	private $preconfiguredSku;

    /**
     * @var int $productPageTemplateId
     */
    private $productPageTemplateId;

	/**
	 * @var
	 */
	private $skus;

	/**
	 * @var int $accessoryFamilyId
	 */
	private $accessoryFamilyId;

	/**
	 * @var array $accessoryFamilies
	 */
	private $accessoryFamilies = array();

	/**
	 * @var
	 */
	private $builderTweakToolBuilderRef;

    /**
     * @var array|null
     */
    private $stateParameters;

    /**
     * @var int|bool $isManufactured
     */
    private $isManufactured;


	/**
	 * Construct will handle setting calling
	 * the setters methods
	 *
	 * @param int $id
	 * @param string $custom
	 * @param array $stateParameters
	 * @throws Error if $id is not set
	 */
	public function  __construct ($id = NULL, $custom = NULL, $stateParameters = NULL) {

		$this->setId($id);
		//Attempt to get data from cache

		if( !is_null($this->getId()) ) {

            CacheableEntity::__construct(get_class($this), $this->getId());

            $data = $this->getCache();

			if ( empty($data) ) {

                if( isset($stateParameters['sourceSubcategoryProduct']) ){

                    $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND sp.subcategory_id = ? AND sp.id = ?");
                    $query->execute(array($stateParameters['breadcrumbSubcategory'], $stateParameters['sourceSubcategoryProduct'], PDO::PARAM_INT));

                } else if( isset($stateParameters['sourceLandingProduct']) )  {

                    $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND lp.landing_id = ? AND lp.id = ?");
                    $query->execute(array($stateParameters['breadcrumbLanding'], $stateParameters['sourceLandingProduct'], PDO::PARAM_INT));

                } else {

                    $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND product.id = :id ");
                    $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);
                }

                if( $query->execute() ) $data = $query->fetch (PDO::FETCH_ASSOC);

                //Subcategory Fallback to Default
                if( isset($stateParameters['sourceSubcategoryProduct']) && empty($data['subcategory_id']) ) {

                    $query = Connection::getHandle ()->prepare (self::FULL_TABLE_DUMP." AND product.id = :id ");
                    $query->bindParam (':id', $this->getId (), PDO::PARAM_INT);

                    if( $query->execute () ) $data = $query->fetch (PDO::FETCH_ASSOC);
                }

                $this->storeCache ($data);
            }

            $this->setSkus ($data['array_sku_ids'])
                 ->setActive ($data['active'])
                 ->setProductPage ($data['id'])
                 ->setProductRecommendationIds ($data['array_recom_ids'])
                 ->setProductNumber ($data['product_number'])
                 ->setByLegend ($data['by_legend'])
                 ->setArtworkDescription ($data['artwork_description'])
                 ->setDisplayNumber ($data['display_number'])
                 ->setNote ($data['note'])
                 ->setIsManufactured ($data['is_manufactured'])
                 ->setCustom ($data['custom'])
                 ->setFreeForm ($data['free_form'])
                 ->setAccessoryFamilyId ($data['accessory_family_id_array'])
                 ->setHasArrows ($data['has_arrows'])
                 ->setHasSymbols ($data['has_symbols'])
                 ->setViewingDistanceStandardId ($data['viewing_distance_standard_id'])
                 ->setDescription ($data['description'])
                 ->setStreetsignNote ($data['streetsign_note'])
                 ->setComplianceFile ($data['compliance_file'])
                 ->setSizeIntro ($data['size_intro'])
                 ->setSizeOutro ($data['size_outro'])
                 ->setMaterialIntro ($data['material_intro'])
                 ->setMaterialOutro ($data['material_outro'])
                 ->setPrintingIntro ($data['printing_intro'])
                 ->setPreconfiguredSku ($data['default_preconfigured_sku_id'])
                 ->setFontsDescription ($data['fonts_description'])
                 ->setInstallationIntro ($data['installation_intro'])
                 ->setInstallationQuestionId ($data['installation_question_id'])
                 ->setShowCrosshatchTestInfo ($data['show_crosshatch_test_info'])
                 ->setShowTextSize ($data['show_text_size'])
                 ->setAdvertise ($data['advertise'])
                 ->setSearchable ($data['searchable'])
                 ->setNewUntil ($data['new_until'])
                 ->setExpiration ($data['expiration'])
                 ->setOnSale ($data['on_sale'])
                 ->setSavings ($data['savings'])
                 ->setPageTitle ($data['page_title'])
                 ->setPageSubtitle ($data['page_subtitle'])
                 ->setMetaDescription ($data['meta_description'])
                 ->setMetaKeywords ($data['meta_keywords'])
                 ->setSearchKeywords ($data['search_keywords'])
                 ->setSitemapShow ($data['sitemap_show'])
                 ->setUrlSlug ($data['url_slug'])
                 ->setCanonicalPage ($data['canonical_page_url_id'])
                 ->setCustomAlternativePageType ($data['custom_alternative_page_type'])
                 ->setCustomAlternativeToolTypeId ($data['custom_alternative_tool_type_id'])
                 ->setCustomAlternativePage ($data['custom_alternative_page_id'])
                 ->setPagePriority ($data['page_priority'])
                 ->setBuilderTweakToolId ($data['builder_tweak_tool_id'])
                 ->setProductPageTemplate ($data['product_page_template_id'])
                 ->setHeader ($data['header_id'])
                 ->setLanguage ($data['language_id'])
                 ->setChangeFrequency ($data['change_frequency_id'])
                 ->setComplianceTabPosition ($data['compliance_tab_position'])
                 ->setSizeTabPosition ($data['size_tab_position'])
                 ->setMaterialTabPosition ($data['material_tab_position'])
                 ->setPrintingTabPosition ($data['printing_tab_position'])
                 ->setShowPrintingInfo ($data['show_printing_info'])
                 ->setShowMaterialIllustrations ($data['show_material_illustrations'])
                 ->setStreetsignTools ($data['array_streetsign_ids'])
                 ->setProductCollectionIds ($data['product_collection_ids'])
                 ->setInstallationTabPosition ($data['installation_tab_position'])
                 ->setFormula ($data['vDistance_formula'])
                 ->setBuilderTweakToolBuilderRef ($data['builder_tweak_tool_builder_ref']);

            if( isset($stateParameters['sourceSubcategoryProduct']) && !empty($data['subcategory_id']) ){

                $this->setProductName($data['subcategory_product_name'])
                     ->setBestSeller($data['subcategory_best_seller'])
                     ->setBuilderRef($data['subcategory_builder_ref'])
                     ->setFlashToolId($data['subcategory_flash_tool_id'])
                     ->setProductImage($data['subcategory_product_image'])
                     ->setPreconfiguredSkuId($data['subcategory_product_preconfigured_sku_id'])
                     ->setStreetsignToolId($data['subcategory_product_streetsign_tool_id'])
                     ->setSubcategoryId($data['subcategory_id'])
                     ->setSubtitle($data['subcategory_product_subtitle'])
                     ->setToolTypeId($data['subcategory_product_tool_type_id'])
                     ->setTranslationFamilyId($data['subcategory_product_translation_family_id']);

            }else if( isset($stateParameters['sourceLandingProduct']) && !empty($data['landing_id']) ) {

               $this->setProductName($data['landing_product_name'])
                    ->setBestSeller($data['landing_product_best_seller'])
                    ->setBuilderRef($data['landing_builder_ref'])
                    ->setFlashToolId($data['landing_flash_tool_id'])
                    ->setProductImage($data['landing_product_image'])
                    ->setPreconfiguredSkuId($data['landing_product_preconfigured_sku_id'])
                    ->setStreetsignToolId($data['landing_product_streetsign_tool_id'])
                    ->setLandingId($data['landing_id'])
                    ->setSubtitle($data['landing_product_subtitle'])
                    ->setToolTypeId($data['landing_product_tool_type_id'])
                    ->setTranslationFamilyId($data['landing_product_translation_family_id']);

			}else {

				$this->setProductName($data['default_product_name'])
					 ->setBestSeller($data['default_best_seller'])
					 ->setBuilderRef($data['default_builder_ref'])
					 ->setFlashToolId($data['default_flash_tool_id'])
					 ->setProductImage($data['image'])
					 ->setPreconfiguredSkuId(!empty($stateParameters['preconfiguredSku']) ?
                         $stateParameters['preconfiguredSku'] : $data['default_preconfigured_sku_id']
                     )
					 ->setStreetsignToolId($data['default_streetsign_tool_id'])
					 ->setLandingId($data['default_landing_id'])
					 ->setSubcategoryId ($data['default_subcategory_id'])
					 ->setSubtitle($data['default_subtitle'])
					 ->setToolTypeId($data['default_tool_type_id'])
					 ->setTranslationFamilyId(!empty($stateParameters['translationFamily']) ?
                          $stateParameters['translationFamily'] : $data['default_translation_family_id']);
			}

            $this->stateParameters = $stateParameters;
        }
	}

	/*************************************************
	* Start Setters
	**************************************************/
	/**
	* Set privately the $id and return $this
	*
	* @param int $id
	* @return object Return current class object
	*/
	public function setId($id) {

		$this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

    /**
     * @param $isManufactured
     * @return Product()
     */
    public function setIsManufactured ($isManufactured) {
        $this->isManufactured = $isManufactured;
        return $this;
    }

	/**
	 * @param int $accessoryFamilyId
	 * @return Product()
	 */
	public function setAccessoryFamilyId($accessoryFamilyId) {

		$this->accessoryFamilyId = !empty($accessoryFamilyId) ? explode(",", $accessoryFamilyId) : NULL;
		return $this;
	}

	/**
	 *
	 * @param $formula
	 * @return Product()
	 */
	public function setFormula($formula) {

		$this->vdFormula = !empty($formula) ? trim($formula) : NULL;
		return $this;
	}

	/**
	 * @param $customAlternativeToolTypeId
	 * @return $this
	 */
	public function setCustomAlternativeToolTypeId($customAlternativeToolTypeId) {

		$this->customAlternativeToolTypeId = isset($customAlternativeToolTypeId) && is_numeric($customAlternativeToolTypeId)
												&& $customAlternativeToolTypeId > 0 ? (int) $customAlternativeToolTypeId : NULL;
		return $this;
	}

	/**
	 * FormatGrouping: Explode string in to an array
	 *
	 * @method
	 * @param string $skus A stirng containing comma separated values
	 * @return Product() Return a self instance of the class.
	 */
	public function setSkus($skus) {

		//Explode string in to an array
		$this->skuIds = !empty($skus) ? explode(",", $skus) : NULL;
		return $this;
	}

	public function setSubcategoryId($subcategoryId){

        $this->subcategoryId = isset($subcategoryId) && is_numeric($subcategoryId) &&
        $subcategoryId > 0 ? (int) $subcategoryId : NULL;
        return $this;
    }

	public function setLandingId($landingId){

        $this->landingId = isset($landingId) && is_numeric($landingId) &&
        $landingId > 0 ? (int) $landingId : NULL;
        return $this;
    }

	/**
     * Set the product page
     *
     * @param $id
     * @return Product()
     */
	public function setProductPage($id) {

		$this->productPage = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
		return $this;
	}

	public function setBestSeller($bestSeller) {

        $this->bestSeller = $bestSeller ? TRUE : FALSE;
        return $this;
    }

	/**
     * Set / Enable or disable the product
     *
     * @param $active
     * @return Product()
     */
	public function setActive($active) {

		$this->active = $active ? TRUE: FALSE;
		return $this;
	}

    /**
     * Set the product number
     *
     * @param $productNumber
     * @return Product()
     */
	public function setProductNumber($productNumber) {

		$this->productNumber = isset($productNumber) ? $productNumber : NULL;

		return $this;
	}

	public function setProductName($productName){

        $this->productName = !empty($productName) ? trim($productName) : NULL;

        return $this;
    }

	public function setSubtitle($subtitle) {

        $this->subtitle = !empty($subtitle) ? trim($subtitle) : NULL;

        return $this;
    }

	/**
     * Set the product Legend
     *
     * @param $byLegend
     * @return Product()
     */
	public function setByLegend($byLegend) {

		$this->byLegend = !empty($byLegend) ? $byLegend : NULL;

		return $this;
	}

    /**
     * Set the product description
     *
     * @param $artworkDescription
     * @return Product()
     */
	public function setArtworkDescription($artworkDescription) {

		$this->artworkDescription = !empty($artworkDescription) ? $artworkDescription : NULL;

		return $this;
	}

    /**
     * Set the product display number
     *
     * @param $displayNumber
     * @return Product()
     */
	public function setDisplayNumber($displayNumber) {

		$this->displayNumber = !empty($displayNumber) ? trim($displayNumber) : NULL;

		return $this;
	}

    /**
     * Set the products note
     *
     * @param $note
     * @return Product()
     */
	public function setNote($note) {

		$this->note = !empty($note) ? trim($note) : NULL;

		return $this;
	}

	public function setPreconfiguredSkuId($preconfiguredSku) {

        $this->preconfiguredSkuId = isset($preconfiguredSku) && is_numeric($preconfiguredSku) &&
        $preconfiguredSku > 0 ? (int) $preconfiguredSku : NULL;
        return $this;
    }


	/**
     * Set the product custom value
     *
     * @param $custom
     * @return Product()
     */
    public function setCustom($custom) {

		$this->custom = $custom ? TRUE : FALSE;

		return $this;
	}

    /**
     * @param $freeForm
     * @return Product()
     */
    public function setFreeForm($freeForm) {

		$this->freeForm = $freeForm ? TRUE : FALSE;

		return $this;
	}

    /**
     * Set product $hasArrows
     *
     * @param $hasArrows
     * @return Product()
     */
    public function setHasArrows($hasArrows) {

		$this->hasArrows = $hasArrows ? TRUE : FALSE;

		return $this;
	}

    /**
     * Set the product $hasSymbols
     *
     * @param $hasSymbols
     * @return Product()
     */
    public function setHasSymbols($hasSymbols) {

		$this->hasSymbols = $hasSymbols ? TRUE : FALSE;

		return $this;
	}

    /**
     * Set the product $viewingDistanceStandardId
     *
     * @param $viewingDistanceStandardId
     * @return Product()
     */
    public function setViewingDistanceStandardId($viewingDistanceStandardId) {

		$this->viewingDistanceStandardId = $viewingDistanceStandardId;

		return $this;
	}

    /**
     * Set the product $description
     *
     * @param $description
     * @return Product()
     */
    public function setDescription($description) {

		$this->description = !empty($description) ? trim($description) : NULL;
		return $this;
	}

    /**
     * Set the product $streetsignNote
     *
     * @param $streetsignNote
     * @return Product()
     */
    public function setStreetsignNote($streetsignNote) {

		$this->streetsignNote = !empty($streetsignNote) ? trim($streetsignNote) : NULL;

		return $this;
	}

    /**
     * Set the product $complianceFile
     *
     * @param $complianceFile
     * @return Product()
     */
    public function setComplianceFile($complianceFile) {

		$this->complianceFile = !empty($complianceFile) ? trim($complianceFile) : NULL;

		return $this;
	}

    /**
     * Set product $sizeIntro
     *
     * @param $sizeIntro
     * @return Product()
     */
    public function setSizeIntro($sizeIntro) {

		$this->sizeIntro = !empty($sizeIntro) ? trim($sizeIntro) : NULL;

		return $this;
	}

    /**
     * Set product $sizeOutro
     *
     * @param $sizeOutro
     * @return Product()
     */
    public function setSizeOutro($sizeOutro) {

		$this->sizeOutro = !empty($sizeOutro) ? trim($sizeOutro) : NULL;

		return $this;
	}

    /**
     * Set the product $materialIntro
     *
     * @param $materialIntro
     * @return Product()
     */
    public function setMaterialIntro($materialIntro) {

		$this->materialIntro = !empty($materialIntro) ? trim($materialIntro) : NULL;

		return $this;
	}

    /**
     * Set the product $materialOutro
     *
     * @param $materialOutro
     * @return Product()
     */
    public function setMaterialOutro($materialOutro) {

		$this->materialOutro = !empty($materialOutro) ? trim($materialOutro) : NULL;

		return $this;
	}

    /**
     * Set the product $printingIntro
     *
     * @param $printingIntro
     * @return Product()
     */
    public function setPrintingIntro($printingIntro) {

		$this->printingIntro = !empty($printingIntro) ? trim($printingIntro) : NULL;

		return $this;
	}

    /**
     * Set the product $fontsDescription
     *
     * @param $fontsDescription
     * @return Product()
     */
    public function setFontsDescription($fontsDescription) {

		$this->fontsDescription = !empty($fontsDescription) ? trim($fontsDescription) : NULL;

		return $this;
	}

    /**
     * Set the product $installationIntro
     *
     * @param $installationIntro
     * @return Product()
     */
    public function setInstallationIntro($installationIntro) {

		$this->installationIntro = !empty($setInstallationIntro) ? trim($installationIntro) : NULL;

		return $this;
	}

    /**
     * Set the product $installationQuestionId
     *
     * @param $installationQuestionId
     * @return Product()
     */
	public function setInstallationQuestion($installationQuestionId) {

		$this->installationQuestionId = !empty($installationQuestionId) ? trim($installationQuestionId) : NULL;;

		return $this;
	}

    /**
     * Set the product $showCrosshatchTestInfo
     *
     * @param $showCrosshatchTestInfo
     * @return Product()
     */
    public function setShowCrosshatchTestInfo($showCrosshatchTestInfo) {

		$this->showCrosshatchTestInfo = $showCrosshatchTestInfo ? TRUE : FALSE;

		return $this;
	}

    /**
     * @param $showTextSize
     * @return Product()
     */
    public function setShowTextSize($showTextSize) {

		$this->showTextSize = (bool) $showTextSize;
		return $this;
	}

    /**
     * @param $advertise
     * @return Product()
     */
    public function setAdvertise($advertise) {

		$this->advertise = $advertise ? TRUE : FALSE;

		return $this;
	}

    /**
     * @param $searchable
     * @return Product()
     */
    public function setSearchable($searchable) {

		$this->searchable = $searchable ? TRUE : FALSE;

		return $this;
	}

    /**
     * @param $newUntil
     * @return Product()
     */
    public function setNewUntil($newUntil) {

		$this->newUntil = $newUntil;

		return $this;
	}

    /**
     * @param $expiration
     * @return Product()
     */
    public function setExpiration($expiration) {

		$this->expiration = $expiration;

		return $this;
	}

    /**
     * @param $onSale
     * @return Product()
     */
    public function setOnSale($onSale) {

		$this->onSale = $onSale ? TRUE : FALSE;

		return $this;
	}

    /**
     * @param $savings
     * @return Product()
     */
    public function setSavings($savings) {

		$this->savings = isset($savings) && is_float($savings) ? (float) $savings : NULL;

		return $this;
	}

    /**
     * @param $pageTitle
     * @return Product()
     */
    public function setPageTitle($pageTitle) {

		$this->pageTitle = !empty($pageTitle) ? trim($pageTitle) : NULL;

		return $this;
	}

    /**
     * @param $pageSubtitle
     * @return Product()
     */
    public function setPageSubtitle($pageSubtitle) {

		$this->pageSubtitle = !empty($pageSubtitle) ? trim($pageSubtitle) : NULL;

		return $this;
	}

    /**
     * @param $metaDescription
     * @return Product()
     */
    public function setMetaDescription($metaDescription) {

		$this->metaDescription = !empty($metaDescription) ? trim($metaDescription) : NULL;

		return $this;
	}

    /**
     * @param $metaKeywords
     * @return Product()
     */
    public function setMetaKeywords($metaKeywords) {

		$this->metaKeywords = !empty($metaKeywords) ? trim($metaKeywords) : NULL;

		return $this;
	}

    /**
     * @param $searchKeywords
     * @return Product()
     */
    public function setSearchKeywords($searchKeywords) {

		$this->searchKeywords = !empty($searchKeywords) ? trim($searchKeywords) : NULL;

		return $this;
	}

    /**
     * @param $sitemapShow
     * @return Product()
     */
    public function setSitemapShow($sitemapShow) {

		$this->sitemapShow = $sitemapShow ? (bool) $sitemapShow : FALSE;

		return $this;
	}

    /**
     * @param $urlSlug
     * @return Product()
     */
    public function setUrlSlug($urlSlug) {

		$this->urlSlug = !empty($urlSlug) ? (string) $urlSlug : NULL;

		return $this;
	}

    /**
     * @param $canonicalPageUrlId
     * @return Product()
     */
    public function setCanonicalPage($canonicalPageUrlId) {

		$this->cononicalPageUrlId = isset($canonicalPageUrlId) && is_numeric($canonicalPageUrlId) &&
										  $canonicalPageUrlId > 0 ? (int) $canonicalPageUrlId : NULL;
		return $this;
	}

    /**
     * @param $compactSizeControl
     * @return Product()
     */
    public function setCompactSizeControl($compactSizeControl) {

		$this->compactSizeControl = $compactSizeControl ?  TRUE : FALSE;

		return $this;
	}

    /**
     * Set the product custom alternative page type
     *
     * @param $customAlternativePageType
     * @return Product()
     */
    public function setCustomAlternativePageType($customAlternativePageType) {

		$this->customAlternativePageType = !empty($customAlternativePageType) ? (string) $customAlternativePageType : NULL;

		return $this;
	}

    /**
     * Set the product custom alternative page id
     *
     * @param $customAlternativePageId
     * @return Product()
     */
    public function setCustomAlternativePage($customAlternativePageId) {

		$this->customAlternativePageId = isset($customAlternativePageId) && is_numeric($customAlternativePageId) &&
												$customAlternativePageId > 0 ? (int) $customAlternativePageId : NULL;
		return $this;
	}

    /**
     * Set the product page priority
     *
     * @param $pagePriority
     * @return Product()
     */
    public function setPagePriority($pagePriority) {

		$this->pagePriority = isset($pagePriority) && is_float($pagePriority) ? (float) $pagePriority : NULL;
		return $this;
	}

    /**
     * Set the product builder tool id
     *
     * @param $builderTweakToolId
     * @return Product()
     */
    public function setBuilderTweakToolId($builderTweakToolId) {

		$this->builderTweakToolId = isset($builderTweakToolId) && is_numeric($builderTweakToolId) &&
										  $builderTweakToolId > 0 ? (int) $builderTweakToolId : NULL;
		return $this;
	}

    /**
     * Set the product page template id
     *
     * @param $productPageTemplateId
     * @return Product()
     */
    public function setProductPageTemplate($productPageTemplateId) {

		$this->productPageTemplateId = isset($productPageTemplateId) && is_numeric($productPageTemplateId) &&
											 $productPageTemplateId > 0 ? (int) $productPageTemplateId : NULL;
		return $this;
	}

    /**
     * Set the product header id
     *
     * @param $headerId
     * @return Product()
     */
    public function setHeader($headerId) {

		$this->Header = isset($headerId) && is_numeric($headerId) && $headerId > 0 ? (int) $headerId : NULL;
		return $this;
	}

    /**
     * Set the product language
     *
     * @param $languageId
     * @return Product()
     */
    public function setLanguage($languageId) {

		$this->languageId = isset($languageId) && is_numeric($languageId) &&
								  $languageId > 0 ? (int) $languageId : NULL;
		return $this;
	}

    /**
     * Set the product frequency id
     *
     * @param $changeFrequencyId
     * @return Product()
     */
    public function setChangeFrequency($changeFrequencyId) {

        //@TODO: Move me to the get method, and only assign the property here.
		$this->ChangeFrequency = isset($changeFrequencyId) && is_numeric($changeFrequencyId) &&
			  							$changeFrequencyId > 0 ? (int) $changeFrequencyId : NULL;
		return $this;
	}

    /**
     * Set the product compliance tab position
     *
     * @param $complianceTabPosition
     * @return Product()
     */
    public function setComplianceTabPosition($complianceTabPosition) {

		$this->complianceTabPosition = !empty($complianceTabPosition) ? (int) $complianceTabPosition : NULL;
		return $this;
	}

    /**
     * Set the product size tab position
     *
     * @param $sizeTabPosition
     * @return Product()
     */
    public function setSizeTabPosition($sizeTabPosition) {

		$this->sizeTabPosition = !empty($sizeTabPosition) ? (int) $sizeTabPosition : NULL;
		return $this;
	}

    /**
     * Set the product Installation tab position
     *
     * @param $installationTabPosition
     * @return Product()
     */
    public function setInstallationTabPosition($installationTabPosition) {

		$this->installationTabPosition =  !empty($installationTabPosition) ? (int) $installationTabPosition : NULL;
		return $this;
	}

    /**
     * Set the product material tab position
     *
     * @param $materialTabPosition
     * @return Product()
     */
    public function setMaterialTabPosition($materialTabPosition) {

		$this->materialTabPosition =  !empty($materialTabPosition) ? (int) $materialTabPosition : NULL;
		return $this;
	}

    /**
     * Set the product printing tab position
     *
     * @param $printingTabPosition
     * @return Product()
     */
    public function setPrintingTabPosition($printingTabPosition) {

		$this->printingTabPosition = !empty($printingTabPosition) ? (int) $printingTabPosition : NULL;
		return $this;
	}

    /**
     * Set the product printing info
     *
     * @param $showPrintingInfo
     * @return Product()
     */
    public function setShowPrintingInfo($showPrintingInfo) {

		$this->showPrintingInfo = $showPrintingInfo ? TRUE : FALSE;
		return $this;
	}

    /**
     * Set the product material illustration
     *
     * @param $showMaterialIllustrations
     * @return Product()
     */
    public function setShowMaterialIllustrations($showMaterialIllustrations) {

		$this->showMaterialIllustrations = $showMaterialIllustrations ? TRUE : FALSE;
		return $this;
	}

//	public function setCustomImage($)

    /**
     * Set the product default image
     *
     * @param $productImage
     * @return Product()
     */
    public function setProductImage($productImage) {

        $this->imageArray['grid']   = IMAGE_URL_PREFIX.'/images/catlog/product/small/'  . trim($productImage);
        $this->imageArray['medium'] = IMAGE_URL_PREFIX.'/images/catlog/product/medium/' . trim($productImage);
        $this->imageArray['large']  = IMAGE_URL_PREFIX.'/images/catlog/product/large/' .  trim($productImage);
        $this->imageArray['zoom']   = IMAGE_URL_PREFIX.'/images/catlog/product/large/' .  trim($productImage);

        //For custom image we will pass the value later.
        $this->imageArray['custom'] = IMAGE_URL_PREFIX.'/design/save/previews/';
        $this->imageArray['cZoom']  = IMAGE_URL_PREFIX.'/design/save/previews/medium/';

		return $this;
	}

	/**
     * Set the product tool type ids
     *
     * @param $toolTypeIds
     * @return Product()
     */
    public function setToolTypes($toolTypeIds) {

		foreach ( $toolTypeIds AS $id ) {
            //@TODO: move object instantiation to the getmethod
			$this->toolTypes[$id] = ToolType::create($id);
		}

		return $this;
	}


	public function setToolTypeId($toolTypeId) {

        $this->toolTypeId = isset($toolTypeId) && is_numeric($toolTypeId) &&
        $toolTypeId > 0 ? (int) $toolTypeId : NULL;
        return $this;
    }


	/**
     * Set the product flash tool
     *
     * @param $flashToolIds
     * @return Product()
     */
    public function setFlashTools($flashToolIds) {

		$this->flashToolIds = NULL;

		if( isset($flashToolIds) && is_numeric($flashToolIds) && $flashToolIds > 0 ) {

			$this->flashToolIds[] = (int) $flashToolIds;
		}

		return $this;
	}

	public function setFlashToolId($flashToolId) {

        $this->flashToolId = NULL;

        if( isset($flashToolId) && is_numeric($flashToolId) && $flashToolId > 0 ) {

            $this->flashToolId = (int) $flashToolId;
        }

        return $this;
    }

	public function getPreconfiguredSkuId(){
        return $this->preconfiguredSkuId;
    }

    /**
     * Set the product street sign tool id
     *
     * @param $streetsignToolIds
     * @return Product()
     */
    public function setStreetsignTools($streetsignToolIds) {

		$this->streetsignToolIds = !empty($streetsignToolIds)? explode(',', $streetsignToolIds) : NULL;

		return $this;
	}

    public function setStreetsignToolId($streetsignToolId) {

        $this->streetsignToolId = isset($streetsignToolId) && is_numeric($streetsignToolId) &&
        $streetsignToolId > 0 ? (int) $streetsignToolId : NULL;
        return $this;
    }

	/**
     * Set the product builder refs
     *
     * @param $builderRefs
     * @return Product()
     */
    public function setBuilderRefs($builderRefs) {

		$this->builderRefIds[] = $builderRefs;

		return $this;
	}


	public function setBuilderRef($builderRef) {
        $this->builderRef = $builderRef;
        return $this;
    }

    /**
     * Set the product preconfigured sku
     *
     * @param $skuId
     * @return Product()
     */
    public function setPreconfiguredSku($skuId) {

		$this->preconfiguredSku = isset($skuId) && is_numeric($skuId) && $skuId > 0 ? (int) $skuId : NULL;

		return $this;
	}

    /**
     * Set the product sku ids
     *
     * @param $skuIds
     */
    public function setSkuIds($skuIds) {

		$this->skuIds[] = $skuIds;
	}

    /**
     * Set the product recommendation
     *
     * @param $ids
     * @return Product()
     */
    public function setProductRecommendationIds($ids) {

		$this->productRecommendationIds = array();

		if( isset($ids) ) $this->productRecommendationIds = explode(",", $ids);

		return $this;
	}


    /**
     * @param $id
     * @return Product()
     */
    public function setInstallationQuestionId($id) {

		$this->installationQuestionId = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;

		return $this;
	}

    /**
     * @param $ids
     * @return Product()
     */
    public function setTranslationFamilyIds($ids) {

		$this->translationFamilyIds = isset($ids) && is_numeric($ids) && $ids > 0 ? (int) $ids : NULL;

		return $this;
	}

    public function setTranslationFamilyId($translationFamilyId){

        $this->translationFamilyId = isset($translationFamilyId) && is_numeric($translationFamilyId) && $translationFamilyId > 0 ? (int) $translationFamilyId : NULL;

        return $this;
    }

    /**
     * @param array $productCollectionIds
     * @return Product()
     */
    public function setProductCollectionIds($productCollectionIds) {

		$this->productCollectionIds = !empty($productCollectionIds) ? explode(",", $productCollectionIds) : NULL;

		return $this;
	}

	public function setBuilderTweakToolBuilderRef($builderTweakToolBuilderRef) {

		$this->builderTweakToolBuilderRef = !empty($builderTweakToolBuilderRef) ? trim($builderTweakToolBuilderRef) : NULL;
		return $this;
	}


	/*************************************************
	* Start Getters
	**************************************************/
    /**
     * @return int
     */
    public function getId() { return $this->id; }

    /**
     * @return mixed
     */
    public function getProductRecommendations() {

		if ( empty($this->recommendedProducts) && isset($this->productRecommendationIds) ) {

			foreach ( $this->productRecommendationIds AS $id ) {

				if( ProductRecommendation::create($id)->isActive() ) {

					$this->recommendedProducts[] = ProductRecommendation::create ($id);
				}
			}
		}

		return !empty($this->recommendedProducts) ? $this->recommendedProducts : NULL;
	}



    /**
     * @return mixed
     */
    public function getSkus() {

		// Instantiate sku objects if none exist
		if ( isset($this->skuIds) ) {

			foreach ( $this->skuIds AS $id ) {

				$this->skus[] = Sku::create((int)$id);
			}
		}

		return $this->skus;
	}

    /**
     * @return array|object
     */
    public function getInstallationQuestion() {

		if ( !empty($this->installationQuestionId) ) {

			$this->InstallationQuestion[] = InstallationQuestion::create($this->installationQuestionId);
		}

		return $this->InstallationQuestion;
	}

	/**
     * @return array
     */
    public function getTranslationFamilies() {

		return TranslationFamily::create($this->getTranslationFamilyId());
	}

    /**
     * @return int
     */
    public function getProductCollections() {

		if ( empty($this->productCollections) && $this->productCollectionIds ) {

			foreach( $this->productCollectionIds AS $collectionId ) {

				$this->productCollections[$collectionId] = ProductCollection::create($collectionId);
			}
		}

		return $this->productCollections;
	}

    /**
     * @return array
     */
    public function getBuilderRefs() {

		if( !empty($this->builderRefIds) ) {

			foreach ( $this->builderRefIds AS $ref ) {

				$this->builderRefs[$ref] = BuilderTool::create($ref);
			}
		}

		return $this->builderRefs;
	}

    public function getBuilderRef(){ return $this->builderRef; }

    /**
     * @return Page
     */
    public function getProductPage() {

		return ProductPage::create($this->getId());
	}


	/**
	 * @return mixed
	 */
    public function getBestSeller() {
		return $this->bestSeller;
	}

    /**
     * @return bool
     */
    public function getActive() { return $this->active; }

    /**
     * @return string
     */
    public function getProductNumber() { return $this->productNumber; }

	/**
	 * Get the product image
	 *
	 * @param string|null $dimention
	 * @return bool
	 */
    public function getProductImage($dimention = NULL) {

		if( array_key_exists ($dimention, $this->imageArray) ) {

			return $this->imageArray[$dimention];
		}

		return FALSE;
	}

    /**
     * @return string
     */
    public function getByLegend() { return $this->byLegend; }

    /**
     * @return string
     */
    public function getArtworkDescription() { return $this->artworkDescription; }

    /**
     * @return string
     */
    public function getDisplayNumber() { return $this->displayNumber; }

    /**
     * @return string
     */
    public function getNote() { return $this->note; }

    /**
     * @return bool
     */
    public function getCustom() { return $this->custom; }

    /**
     * @return bool
     */
    public function getFreeForm() { return $this->freeForm; }

    /**
     * @return bool
     */
    public function getHasArrows() { return $this->hasArrows; }

    /**
     * @return bool
     */
    public function getHasSymbols() { return $this->hasSymbols; }

    /**
     * @return bool
     */
    public function getViewingDistanceStandardId() { return $this->viewingDistanceStandardId; }

    /**
     * @return string
     */
    public function getDescription() { return $this->description; }

    /**
     * @return string
     */
    public function getStreetsignNote() { return $this->streetsignNote; }

    /**
     * @return string
     */
    public function getComplianceFile() { return $this->complianceFile; }

    /**
     * @return string
     */
    public function getSizeIntro() { return $this->sizeIntro; }

    /**
     * @return string
     */
    public function getSizeOutro() { return $this->sizeOutro; }

    /**
     * @return string
     */
    public function getMaterialIntro() { return $this->materialIntro; }

    /**
     * @return string
     */
    public function getMaterialOutro() { return $this->materialOutro; }

    /**
     * @return string
     */
    public function getPrintingIntro() { return $this->printingIntro; }

    /**
     * @return string
     */
    public function getFontsDescription() { return $this->fontsDescription; }

    /**
     * @return string
     */
    public function getInstallationIntro() { return $this->installationIntro; }

    /**
     * @return bool|int
     */
    public function getShowCrosshatchTestInfo() { return $this->showCrosshatchTestInfo; }

    /**
     * @return bool|int
     */
    public function getShowTextSize() { return $this->showTextSize; }

    /**
     * @return bool|int
     */
    public function getAdvertise() { return $this->advertise; }

    /**
     * @return bool|int
     */
    public function getSearchable() { return $this->searchable; }

    /**
     * @return string
     */
    public function getNewUntil() { return $this->newUntil; }

    /**
     * @return string
     */
    public function getExpiration() { return $this->expiration; }

    /**
     * @return bool|int
     */
    public function getOnSale() { return $this->onSale; }

    /**
     * @return float
     */
    public function getSavings() { return $this->savings; }

    /**
     * @return string
     */
    public function getPageTitle() { return $this->pageTitle; }

    /**
     * @return string
     */
    public function getPageSubtitle() { return $this->pageSubtitle; }

    /**
     * @return string
     */
    public function getMetaDescription() { return $this->metaDescription; }

    /**
     * @return string
     */
    public function getMetaKeywords() { return $this->metaKeywords; }

    /**
     * @return string
     */
    public function getSearchKeywords() { return $this->searchKeywords; }

    /**
     * @return bool|int
     */
    public function getSitemapShow() { return $this->sitemapShow; }

    /**
     * @return string
     */
    public function getUrlSlug() { return $this->urlSlug; }

    /**
     * @return array
     */
    public function getStreetSignTools() {

		if( !is_null($this->streetsignToolIds) ) {

			foreach( $this->streetsignToolIds AS $id ) {

				$this->streetsignTools[] = StreetNameTool::create($id);
			}
		}

		return $this->streetsignTools;
	}

    /**
     * @return PageProduct
     */
    public function getCanonicalPage() {

		if( !is_null($this->cononicalPageUrlId) ) {

			$this->CanonicalPage = PageUrls::create($this->cononicalPageUrlId); //@todo: what is Pageurl?
		}

		return $this->CanonicalPage;
	}

    /**
     * @return array
     */
    public function getFlashTools() {

		if( !empty($flashToolIds) ) {

			foreach( $flashToolIds AS $id ) $this->flashTools[$id] = FlashTool::create($id);
		}

		return $this->flashTools;
	}

    /**
     * @return string
     */
    public function getCustomAlternativePageType() { return $this->customAlternativePageType; }

    /**
     * @return mixed
     */
    public function getCustomAlternativePageId() { return $this->customAlternativePageId; }


	public function getCustomAlternativeToolTypeId() {

		return $this->customAlternativeToolTypeId;
	}

	/**
	 * @return bool|int
	 */
	public function isDesignable() {

		$alternativePageType = $this->getCustomAlternativePageType();
		$alternativeToolType = $this->getCustomAlternativeToolTypeId();
		$alternativePage 	 = $this->getCustomAlternativePageId();

		if( !is_null($alternativePageType) && !is_null($alternativeToolType) && !is_null($alternativePage) ) {

			return TRUE;
		}

		return FALSE;
	}

    /**
     * @return Page
     */
    public function getCustomAlternativePage() {

		if ( empty($this->customAlternativePage) ) {

			$this->customAlternativePage = Page::getRelevantPage(

				$this->getCustomAlternativePageType(), $this->getCustomAlternativePageId()
			);
		}

		return $this->customAlternativePage;
	}

    /**
     * @return float
     */
    public function getPagePriority() { return $this->pagePriority; }

    /**
     * @return int
     */
    public function getBuilderTweakToolId() { return $this->builderTweakToolId; }

    /**
     * @return object|ProductPageTemplate
     */
    public function getProductPageTemplate() {

		if( !is_null($this->productPageTemplateId) ) {

			$this->ProductPageTemplate = ProductPageTemplate::create($this->productPageTemplateId);
		}

		return $this->ProductPageTemplate;
	}

    /**
     * @return Header|int
     */
    public function getHeader() {

		if( !is_null($this->Header) ) {

			$this->Header = Header::create($this->headerId);
		}

		return $this->Header;
	}

    /**
     * @return Language
     */
    public function getLanguage() {

		if($this->languageId) {

			$this->language = Language::create($this->languageId);
		}

		return $this->language;
	}

    /**
     * @return mixed
     */
    public function getChangeFrequency() { return $this->changeFrequency; }

    /**
     * @return int
     */
    public function getComplianceTabPosition() { return $this->complianceTabPosition; }

    /**
     * @return int
     */
    public function getSizeTabPosition() { return $this->sizeTabPosition; }

    /**
     * @return int
     */
    public function getInstallationTabPosition() { return $this->installationTabPosition; }

    /**
     * @return int
     */
    public function getMaterialTabPosition() { return $this->materialTabPosition; }

    /**
     * @return int
     */
    public function getPrintingTabPosition() { return $this->printingTabPosition; }

    /**
     * @return bool|int
     */
    public function getShowPrintingInfo() { return $this->showPrintingInfo; }

    /**
     * @return bool|int
     */
    public function getShowMaterialIllustrations() { return $this->showMaterialIllustrations; }

    /**
     * @return int $preconfiguredSku
     */
    public function getPreconfiguredSku() {

		if( !is_null($this->preconfiguredSku) ) {

			return $this->preconfiguredSku;
		}
	}

	/**
	 * @return mixed
	 */
    public function getFlashToolId(){
        return $this->flashToolId;
    }

	/**
	 * @return mixed
	 */
    public function getStreetsignToolId() { return $this->streetsignToolId; }

	/**
	 * @return mixed
	 */
    public function getLandingId() { return $this->landingId; }

	/**
	 * @return mixed
	 */
    public function getSubcategoryId() { return $this->subcategoryId; }

	/**
	 * @return mixed
	 */
    public function getProductSubtitle() {
		return $this->subtitle;
	}

	/**
	 * @return mixed
	 */
    public function getToolTypeId() { return $this->toolTypeId; }

	/**
	 * @return mixed
	 */
    public function getTranslationFamilyId() { return $this->translationFamilyId; }

	/**
	 * @return int
	 */
	public function getAccessoryFamilyId() {

		return $this->accessoryFamilyId;
	}

	public function isTranslatable() {

		if( count($this->getTranslationFamilyId()) >= 1 ) {

			return TRUE;
		}

		return FALSE;
	}

	/**
	 * @return array
	 */
	public function getAccessoryFamily() {

		if( !is_null($this->getAccessoryFamilyId()) ) {

			foreach($this->getAccessoryFamilyId() as $accessoryFamilies) {

				$this->accessoryFamilies[] = AccessoryFamilyProduct::create($accessoryFamilies, $this->getId());
			}
		}

		return $this->accessoryFamilies;
	}

    /**
	 * @param string $dimention
     * @return string
     */
    public function getImages($dimention) {

		if( array_key_exists($dimention, $this->imageArray) ) {

			return $this->imageArray[$dimention];
		}

		return false;
	}


    // Get the product id by product number for url redirect
    /**
     * @param null $productNumber
     * @return int
     */
    public static function getProductIdByProductNumber($productNumber = NULL) {

        $query = Connection::getHandle()
                    ->prepare("SELECT id FROM bs_products WHERE product_number = :productNumber AND active = TRUE");

        $query->bindParam(':productNumber', $productNumber, PDO::PARAM_STR);

        if( $query->execute() ) {

            $data = $query->fetch(PDO::FETCH_ASSOC);
            return (int)$data['id'];
        }
    }

	public function getSizeTable() {

		$productMatrls = array();
		$productSizes  = array();

		foreach( $this->getSkus() as $skuId => $sku ) {

			if( !is_null($sku->getSize()) ) {

				$productSizes[$sku->getSize()->getId()] = $sku->getSize()->getName();

				if( !isset($productMatrls[$sku->getSize()->getId()]) ) {

					$productMatrls[$sku->getSize()->getId()][] = $sku->getMaterial()->getName();

				}else if( !in_array($sku->getMaterial()->getName(), $productMatrls[$sku->getSize()->getId()]) ) {

					$productMatrls[$sku->getSize()->getId()][] = $sku->getMaterial()->getName();
				}
			}
		}

		/****************************************************************************************/
		// SIZE TAB STRUCTURE
		/****************************************************************************************/
		$sizes = array ();
		$rowSpan = array ();
		$colspan = 0;
		$groupedSkus = array ();
		$uniqueMHoles = array ();
		$groupMaterials = array ();
        $diagrams       = array();

		$options = array (
            'displaySizes'            => FALSE,
            'displayWidths'           => FALSE,
            'displayHeights'          => FALSE,
            'displaytextSize'         => FALSE,
            'displayDiameter'        => FALSE,
            'displayDepths'           => FALSE,
            'displayLengths'          => FALSE,
            'displayVolumes'          => FALSE,
            'displayMaxViewing'       => FALSE,
            'displayMaterials'        => FALSE,
            'displayBluePrints'       => FALSE,
            'displayCornerRadiuses'   => FALSE,
            'displayMountingHoles'    => FALSE,
            'displayMinPipeDiameters' => FALSE,
            'displayMaxPipeDiameters' => FALSE,
        );

		foreach ($this->getSkus() as $key => $Sku) {

            $Material = $Sku->getMaterial ();
            $materialId = $Material->getId ();

			if( is_null($Material->getId ()) ) {
				$Material = NULL;
				$materialId = $sku->getId();
			}

            $MountingHoleArrangement = !is_null ($Sku->getMountingHoleArrangement ()) ? $Sku->getMountingHoleArrangement () : array ();

            $MountingHoleArrangementId = !empty($MountingHoleArrangement) ? $MountingHoleArrangement->getId () : NULL;

            $CornerRadius 	= $Sku->getCornerRadius ();
            $cornerRadiusId = !is_null ($CornerRadius) ? $CornerRadius->getId () : NULL;
            $cornerRadius 	= !is_null ($CornerRadius) ? $CornerRadius : NULL;

            $Size = !is_null($Sku->getSize ()) ? $Sku->getSize () : NULL;
            $sizeId = !is_null ($Size) || !empty($Size) ? $Size->getId () : $skuId;
            $uniqueMHoles[$sizeId] = array ();

            $textHeight = TextHeight::createFromProductAndSizeIds ($this->getId (), $sizeId);

            $materials[$materialId] = $Material;

			$sizes[$sizeId] = !is_null($Size) && !empty($Size) ? $Size : NULL;

            $mountingHoleArrangements[$MountingHoleArrangementId] = $MountingHoleArrangement;

            $groupedSkus[$sizeId]['rowSpan'] = 0;
            $groupedSkus[$sizeId]['sku'] 	 = $Sku->getId ();
            $groupedSkus[$sizeId]['size'] 	 = !is_null($Size) ? $Size : NULL;
            $groupedSkus[$sizeId]['pricing'] = $Sku->getPricing ();
            $groupedSkus[$sizeId]['materials'][$materialId]['cornerRadii'][] = $cornerRadius;
            $groupedSkus[$sizeId]['materials'][$materialId]['name'] = !is_null($Material) ? $Material->getName () : NULL;
            $groupedSkus[$sizeId]['textSize']    = !is_null ($textHeight) ? $textHeight : NULL;
            $groupedSkus[$sizeId]['compliances'] = !is_null ($Sku->getComplianceIds ()) ? $Sku->getCompliances () : NULL;

            if( !is_null ($textHeight) ) {

                $options['displaytextSize']   = TRUE;
                $options['displayMaxViewing'] = TRUE;
            }


            if( !is_null($Size) ) $options['displaySizes'] = TRUE;


			if( !is_null($cornerRadiusId) ) $options['displayCornerRadiuses'] = TRUE;
            if( !is_null($materialId) )     $options['displayMaterials']      = TRUE;

            if( !is_null($Size) && !is_null($Size->getWidth ()) )    $options['displayWidths']   = TRUE;
            if( !is_null($Size) && !is_null($Size->getHeight ()) )   $options['displayHeights']  = TRUE;
            if( !is_null($Size) && !is_null($Size->getVolume ()) )   $options['displayVolumes']  = TRUE;
            if( !is_null($Size) && !is_null($Size->getDepth ()) )    $options['displayDepths']   = TRUE;
            if( !is_null($Size) && !is_null($Size->getDiameter ()) ) $options['displayDiameter'] = TRUE;
            if( !is_null($Size) && !is_null($Size->getLength ()) )   $options['displayLengths']  = TRUE;

            if( !is_null($Size) && !is_null($Size->getMaxPipeDiameter ()) ) $options['displayMaxPipeDiameters'] = TRUE;
            if( !is_null($Size) && !is_null($Size->getMinPipeDiameter ()) ) $options['displayMinPipeDiameters'] = TRUE;


            if( !is_null ($MountingHoleArrangementId) ) {

                if( !in_array ($MountingHoleArrangement, $groupedSkus[$sizeId]['materials'][$materialId]['mountingHoles'])) {

                    $groupedSkus[$sizeId]['materials'][$materialId]['mountingHoles'][] = $MountingHoleArrangement;
                }

                $options['displayMountingHoles'] = TRUE;
            }

            $groupedSkus[$sizeId]['materials'][$materialId]['package'] = $Sku->getPackageInclusionNote ();
            $groupedSkus[$sizeId]['materials'][$materialId]['skuName'] = $Sku->getName ();

            if( !is_null ($cornerRadiusId) && !is_null ($MountingHoleArrangementId) ) {

                $bluePrint = Blueprint::create (NULL, $sizeId, $cornerRadiusId, $MountingHoleArrangementId);

                if( !is_null ($bluePrint->getImageFile ()) && !is_null ($cornerRadiusId) ) {

                    $groupedSkus[$sizeId]['materials'][$materialId]['blueprint'][$MountingHoleArrangementId] = $bluePrint;
                    $options['displayBluePrints'] = TRUE;
                }
            }

            foreach ($groupedSkus[$sizeId]['materials'] as $materialId => $material) {

                if( isset($material['mountingHoles']) || isset($material['cornerRadii']) ) {

                    if( count ($material['cornerRadii']) > 1 ) {

                        $material['cornerRadii'] = array($material['cornerRadii'][0]);
                    }

                    $mountingHoleCornerRadiiCombo = array (

                        'mountingHoles' => isset($material['mountingHoles']) ? $material['mountingHoles'] : array (array ()),
                        'cornerRadius'  => $material['cornerRadii'],
						'blueprint'		=> $material['blueprint'],
                    );
                }

                if( !in_array ($mountingHoleCornerRadiiCombo, $uniqueMHoles[$sizeId]) ) {

                    $uniqueMHoles[$sizeId][] = $mountingHoleCornerRadiiCombo;
                }
            }
        }

        foreach ($groupedSkus as $sizeId => $size) {

            foreach ($size['materials'] as $materialId => $material) {

                if( isset($material['mountingHoles']) || isset($material['cornerRadii']) ) {

                    if( count ($material['cornerRadii']) > 1 ) {

                        $material['cornerRadii'] = array($material['cornerRadii'][0]);

                    }

					$mHoleCRadiiCombo = array (

                        'mountingHoles' => isset($material['mountingHoles']) ? $material['mountingHoles'] : array (array ()),
                        'cornerRadius'  => $material['cornerRadii'],
                        'blueprint' => $material['blueprint'],
                    );

                }

                $hasSize = !is_null ($size['size']) ? $size['size']->getId () : $sizeId;

                $uniqueMHoleIndex = array_search ($mHoleCRadiiCombo, $uniqueMHoles[$hasSize]);
                $groupMaterials[$sizeId][$uniqueMHoleIndex][] = $materials[$materialId];

                $size['mountingHoleIndexes'][] = $uniqueMHoleIndex;

                unset($uniqueMHoleIndex);
            }
        }

        foreach ($groupedSkus as $key => $size) {

            unset($size['materials']);
        }

        foreach ($groupedSkus as $sizeId => $sizes) {

            foreach ($groupMaterials[$sizeId] as $materialGroupIndex => $materialGroups) {

                if( isset($uniqueMHoles[$sizeId][$materialGroupIndex]) ) {

                    if( !isset($rowSpan[$sizeId]) ) {

                        $rowSpan[$sizeId] = 0;
                    }

                    if( count ($uniqueMHoles[$sizeId][$materialGroupIndex]['mountingHoles']) >= 1 ) {

                        foreach ($uniqueMHoles[$sizeId][$materialGroupIndex]['mountingHoles'] as $mhIndex => $mountingHole) {

                            $rowSpan[$sizeId] += 1;
                        }
                    }
				}
            }

//            if( count ($groupMaterials[$sizeId]) > $rowSpan[$sizeId] ) {

                $rowSpan[$sizeId] = count ($groupMaterials[$sizeId]);
//            }

            $groupedSkus[$sizeId]['rowSpan'] = $rowSpan[$sizeId] ;
        }

        foreach ($options as $isVisible) { if( $isVisible ) { $colspan += 1; }}


		uasort ($groupedSkus, array ($this, 'sortbySizes'));

		$sizeTab = array (
            'diagrams'      => $diagrams,
			'materials'     => $groupMaterials,
			'groupedSizes'  => $groupedSkus,
			'mountingHoles' => $uniqueMHoles,
			'colspan'       => $colspan - 1,
			'options'       => $options,
			'intro'         => $this->getSizeIntro(),
			'outro'         => $this->getSizeOutro(),
		);

		return $sizeTab;
	}

	/**
	 * @return array
	 */
	public function getMaterialTable() {


		/****************************************************************************************/
		// MATERIAL TAB STRUCTURE
		/****************************************************************************************/

		$displayMaterials			= FALSE;
		$displayCategory 			= FALSE;
		$displayThickness 			= FALSE;
		$displayDurability 			= FALSE;
		$displayTemperatureRange 	= FALSE;
		$displayChemicalResistance  = FALSE;
		$displayReflectivity 		= FALSE;
		$displayAvailableSizes 		= FALSE;
		$displayOverlaminates 		= FALSE;
        $groupedMaterialCategories  = array();
		$sizeLaminateGroups		= array();

		foreach ( $this->getSkus() AS $Sku ) {

			$MaterialCategory = NULL;
			$materialCategoryId = NULL;

			if ( !is_null($Sku->getMaterial ()) && !is_null($Sku->getMaterial()->getMaterialGroup()) ) {

				$MaterialCategory 	= $Sku->getMaterial()->getMaterialGroup()->getMaterialCategory();
				$materialCategoryId = $Sku->getMaterial()->getMaterialGroup()->getMaterialCategoryId();
				$displayMaterials	= TRUE;
			}

			$Size = $Sku->getSize();
			$sizeId = $Sku->getSizeId();
			$Material = $Sku->getMaterial();
			$materialId = $Sku->getMaterialId();
			$MaterialGroup = $Sku->getMaterial()->getMaterialGroup();
			$materialGroupId = $Sku->getMaterial()->getMaterialGroupId();
			$Reflectivity = $Sku->getMaterial()->getReflectivity();
			$reflectivityId = ($Reflectivity instanceof Reflectivity ? $Reflectivity->getId() : NULL);
			$Laminate =  $Sku->getLaminate();
			$laminateId = ($Laminate instanceof Laminate ? $Laminate->getId() : NULL );


			if ( !is_null($Material->getThickness()) ) { $displayThickness = TRUE; }
			if ( !is_null($Material->getDurability()) ) { $displayDurability = TRUE; }
			if ( !is_null($Material->getServiceTemperatureRange()) ) { $displayTemperatureRange = TRUE; }
			if ( !is_null($Material->getChemicalResistance()) ) { $displayChemicalResistance = TRUE; }
			if ( $Reflectivity instanceof Reflectivity ) { $displayReflectivity = TRUE; }
			if ( !is_null($Size) && !is_null($Size->getName()) ) { $displayAvailableSizes = TRUE; }
			if ( !is_null($Laminate) ) { $displayOverlaminates = TRUE; }


			// Add Material Category
           if( !is_null($materialCategoryId) ) {

			   if( !in_array ($MaterialCategory, $groupedMaterialCategories[$materialCategoryId]['materialCategory']) ) {
				   $groupedMaterialCategories[$materialCategoryId]['materialCategory'] = $MaterialCategory;
			   }

			   // Add Material Group
			   if( !in_array ($MaterialGroup, $groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]) ) {

				   $groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materialGroup'] = $MaterialGroup;

				   uasort($groupedMaterialCategories[$materialCategoryId]['materialGroups'],
						  array ("PropertySort", "sortMaterialGroupTableByValue")
				   );
			   }

			   // Add Material
			   if( !in_array ($Material,$groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materials'][$materialId])) {
				   $groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materials'][$materialId]['material'] = $Material;
			   }

			   // Add Reflectivity
			   if( $Reflectivity instanceof Reflectivity ) {
				   $groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materials'][$materialId]['reflectivity'] = $Reflectivity;
			   }

			   // Add size
			   if( !in_array ($Size, $groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materials'][$materialId]['sizes'][$sizeId])) {

				   $groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materials'][$materialId]['sizes'][$sizeId]['size'] = $Size;

				   uasort($groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materials'][$materialId]['sizes'], array ($this, 'sortbySizes'));
			   }
			   // Add Laminates
			   if( $Laminate instanceof Laminate ) {
				   $groupedMaterialCategories[$materialCategoryId]['materialGroups'][$materialGroupId]['materials'][$materialId]['sizes'][$sizeId]['laminates'][$laminateId] = $Laminate;
			   }
		   }
		}


		/****** Determine whether to show Material Category or not ******/

		// Check if there are more than one categories
		$categoryCount = count($groupedMaterialCategories);


		// If not, we don't need to display the Category header
		if ($categoryCount <= 0 ) {

			$displayCategory = FALSE;

		} else {

			// otherwise loop through the Categories and see if ANY of them have more than one Material Group
			foreach ( $groupedMaterialCategories AS $materialCategory ) {

				$groupCount = count ($materialCategory['materialGroups']);

				// If there is more than one Category and at least one Category has more than one Material Group, we need to display Category Header
				if ( $groupCount >= 1 ) {
					$displayCategory = TRUE;
				}
			}
		}

		/******************************************************/
		// Collapse sizes into size groups when applicable ( If none have laminates, or if one or two sizes have
		// the same set of laminates )
		foreach ( $groupedMaterialCategories AS $index => &$materialCategory ) {

			foreach ( $materialCategory['materialGroups'] AS &$materialList ) {

				foreach ( $materialList['materials'] AS &$material ) {

					foreach ( $material['sizes'] AS $size ) {

						$groupFound = FALSE;

						foreach ( $sizeLaminateGroups AS $index => $sizeLaminate ) {

							if ( $size['laminates'] == $sizeLaminate['laminates'] ) {

								$sizeLaminateGroups[$index]['sizes'][] = $size['size'];
								$groupFound = TRUE;
								break;
							}

						}

						if( !$groupFound ) {

							$sizeLaminateGroups[] 	= array('sizes' => !is_null($size['size']) ? array($size['size']) : NULL,
															 'material_id' => $materialId,
															 'laminates' => $size['laminates']
							);

						}

					}

					// Unset current structure, swap in new one
					unset($material['sizes']);
					$material['sizeLaminateGroups'] = $sizeLaminateGroups;
					unset($sizeLaminateGroups);

				}

			}

		}

		/*******************************************************/

		/************** /Determine Row spans/ and /Sort Materials by thickness/ ********************/

		/**
		 * Sort Materials by thickness within each material group
		 * @param $a [First material to compare]
		 * @param $b [Secoond material to compare]
		 *
		 * @return int [-1 if $a < $b, 1 if $a > $b, 0 if both are equal]
		 */

		foreach ( $groupedMaterialCategories AS $index => &$materialCategory) {

			foreach ( $materialCategory['materialGroups'] AS &$materialList ) {

				// Determine correct ordering materials within material group
				usort($materialList['materials'], array($this, 'sortByThickness'));

				foreach ( $materialList['materials'] AS &$material ) {

					// Determine material and size field row spans  (They are both equal)
					$material['materialRowSpan'] = count($material['sizeLaminateGroups']);

					// Determine material group field row span
					$materialList['materialGroupRowSpan'] += count($material['sizeLaminateGroups']);

				}

			}

		}

		/*****************************************************/
		// Data to populate to size tab
		/*****************************************************/
		$materialTab = array(
			 'intro'					 => $this->getMaterialIntro(),
			 'displayMaterials'			 => $displayMaterials,
			 'displayCategory' 			 => $displayCategory,
			 'displayThickness' 		 => $displayThickness,
			 'displayDurability' 		 => $displayDurability,
			 'displayTemperatureRange' 	 => $displayTemperatureRange,
			 'displayChemicalResistance' => $displayChemicalResistance,
			 'displayReflectivity' 		 => $displayReflectivity,
			 'displayAvailableSizes' 	 => $displayAvailableSizes,
			 'displayOverlaminates' 	 => $displayOverlaminates,
			 'productMaterials' 		 => $groupedMaterialCategories,
			 'outro'					 => $this->getMaterialOutro()
		);

		return $materialTab;
	}

	public function sortbySizes ($a, $b) {

		return $a['size']->getPosition () > $b['size']->getPosition ();
	}

	public function sortByThickness($a, $b) {

		return ($a["material"]->getThickness() < $b["material"]->getThickness()) ? -1 : (($a["material"]->getThickness() > $b["material"]->getThickness()) ? 1 : 0);
	}

	function getCustomProductSizeFilterList() {

		$sql = Connection::getHandle()->prepare("
					SELECT sku.*, s.name AS size FROM bs_skus sku
					INNER JOIN bs_product_skus ps ON (ps.sku_id = sku.id)
					INNER JOIN bs_sizes s ON (s.id = sku.size_id)
					WHERE ps.product_id = ? AND sku.active = 1 GROUP BY sku.size_id ORDER BY ps.position"
		);

		$sql->execute(array($this->getId()));

		while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {

			$results[] = $row;
		}

		return $results;
	}

	/**
	 * This will override the current default product name to the actual product name
	 * if no product name is avail than it will use the default.
	 *
	 * @return string $productName
	 */
	public function getProductName() {
		return $this->productName;
	}

    /**
     * @return bool|int
     */
    public function getIsManufactured () {
        return $this->isManufactured;
    }

	/**
	 * Returns true if product is expired; false if not
	 * @param     string    The date format (usually '-')
	 * @return    boolean  	Whether product is expired or not
	 */
	public function isExpired($expiration_date){

		$expired = ($expiration_date <= date("Y-m-d") && $expiration_date !== NULL && $expiration_date != '0000-00-00' ? TRUE : FALSE);
		return $expired;
	}

	/**
	 * @return float
	 */
	public function getFormula() {
		return $this->vdFormula;
	}

	/**
	 * @return mixed
	 */
	public function getBuilderTweakToolBuilderRef(){
		return $this->builderTweakToolBuilderRef;
	}

    public function getDesignYourOwnCustomLink(){

        $sql = Connection::getHandle()->prepare("
					SELECT p.custom_alternative_page_type, tt.name AS custom_alternative_tool_type_name,
					p.custom_alternative_page_id, p.custom_alternative_preconfigured_sku_id, b.id AS custom_alternative_builder_id,
					p.custom_alternative_builder_ref, p.custom_alternative_flash_tool_id, p.custom_alternative_streetsign_tool_id
                    FROM bs_products p
                    LEFT JOIN bs_tool_types tt ON (p.custom_alternative_tool_type_id = tt.id AND tt.active = TRUE)
                    LEFT JOIN bs_builders b ON (b.builder_ref = p.custom_alternative_builder_ref AND b.active = TRUE)
                    WHERE p.id = ? AND p.active = TRUE");

         $sql->execute(array($this->getId()));

        $row = $sql->fetch(PDO::FETCH_ASSOC);

        if ( !empty($row['custom_alternative_page_type']) ){

            if ($row['custom_alternative_page_type'] == "product") {

                $this->stateParameters['sourceProduct'] = $row['custom_alternative_page_id'];

                if ($row['custom_alternative_tool_type_name'] == "builder") {

                    $this->stateParameters['easyDesignTool'] = $row['custom_alternative_builder_id'];

                } elseif ($row['custom_alternative_tool_type_name'] == "flash") {

                    $this->stateParameters['advancedDesignTool'] = $row['custom_alternative_flash_tool_id'];

                } elseif ($row['custom_alternative_tool_type_name'] == "streetname") {

                    $this->stateParameters['streetSignDesignTool'] = $row['custom_alternative_streetsign_tool_id'];

                } else {

                    $this->stateParameters['stockTool'] = 1;
                }

                if (!empty($row['custom_alternative_preconfigured_sku_id'])) {

                    $this->stateParameters['preconfiguredSku'] = $row['custom_alternative_preconfigured_sku_id'];
                }

                $link = Page::create('product', $row['custom_alternative_page_id']);

            } else {

                $link = Page::create('subcategory', $row['custom_alternative_page_id']);
            }

            return $link->getUrl().'?s='.ProductStateParameter::encode($this->stateParameters);

        }

        return false;
    }


    /**
     * Create an instance of Product
	 *
     * @param null $id
	 * @param null|string $parameters
	 * @param null|mixed $stateParameters
     * @return Product
     */
    public static function create($id = NULL, $parameters = NULL, $stateParameters = NULL) {
		return new self($id, $parameters, $stateParameters);
	}
}