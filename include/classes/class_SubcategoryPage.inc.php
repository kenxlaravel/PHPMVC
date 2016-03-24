<?php


/**
 * Class SubcategoryPage
 */
class SubcategoryPage extends Page
{

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "
                    SELECT sub.id AS id, sub.name, sub.slug, sub.active, sub.position, sub.category_name, sub.grouping_id AS grouping_id,
                         sub.grouping_name AS grouping_name,
                         sub.template, sub.page_title, sub.page_heading, sub.meta_keywords, sub.meta_description, sub.image, sub.snippet,
                         sub.description_text_html, sub.intro_supplement_html, sub.popup_html, sub.description_image, sub.image_template,
                         sub.description_more_info_html, sub.grid_header, sub.grid_intro, sub.grid_size, sub.grid_alignment,
                         sub.special_header_class_name, sub.show_product_number, sub.show_quickview, sub.show_filter, sub.show_sort,
                         sub.geotarget_state_list_header, sub.geotarget_state_list_intro, sub.geotarget_dropdown_snippet, sub.geotarget_dropdown_button,
                         sub.canonical_page_url, sub.ppc_campaign, sub.ppc_adgroup, sub.created_by, sub.created_date, sub.modified_by,
                         sub.modified_date, sub.sitemap_page_change_frequency, sub.sitemap_page_priority, sub.sitemap_show,
                         subg.subcategory_id AS geo_target_subcategory_id, subg.target AS target, subg.grid_header AS geo_grid_header,
                         subg.grid_intro AS geo_grid_intro,

                         IF(sub.id = 208, TRUE, FALSE) AS federal,

                         GROUP_CONCAT(DISTINCT subd.id) AS subcategory_detail_ids,
                         GROUP_CONCAT(DISTINCT subp.product_id) AS productId,
                         GROUP_CONCAT(DISTINCT subg.id) AS subcategory_geotarget_ids,

						  c.id AS category_id

                    FROM `bs_subcategories` sub

                    LEFT JOIN bs_subcategories_detailed subd ON (subd.subcategory_id = sub.id)
                    LEFT JOIN bs_subcategories_geotargeted subg ON (subg.subcategory_id = sub.id)
                    LEFT JOIN bs_subcategory_products subp ON (subp.subcategory_id = sub.id)
                    LEFT JOIN bs_groupings g ON (g.id = sub.grouping_id)
                    LEFT JOIN bs_categories c ON (c.id = g.category_id)

                    WHERE sub.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
     * Defines the type of page that we are on
     */
    const TYPE = 'subcategory';

    /**
     * Defines the type of parent that we are coming from
     */
    const PARENT_TYPE = "grouping";

    /**
     * DB field: bs_subcategories.name
     *
     * @var int $id
     */
    private $id;

    /**
     * DB field: bs_subcategories.name
     *
     * @var string $name
     */
    private $name;

    /**
     * DB field: bs_subcategories.slug
     *
     * @var string $slug
     */
    private $slug;

    /**
     * DB field: bs_subcategories.active
     *
     * @var int|bool $active
     */
    private $active;

    /**
     * DB field: bs_subcategories.position
     *
     * @var int $position
     */
    private $position;

    /**
     * DB field: bs_subcategories.category_name
     *
     * @var string $categoryName
     */
    private $categoryName;

    /**
     * DB field: bs_subcategories.grouping_id
     *
     * @var int $groupingId
     */
    private $groupingId;

    /**
     * DB field: bs_subcategories.grouping_name
     *
     * @var string $groupingName
     */
    private $groupingName;

    /**
     * DB field: bs_subcategories.template
     *
     * @var string $template
     */
    private $template;

    /**
     * DB field: bs_subcategories.page_title
     *
     * @var string $pageTitle
     */
    private $pageTitle;

    /**
     * DB field: bs_subcategories.page_heading
     *
     * @var string $pageHeading
     */
    private $pageHeading;

    /**
     * DB field: bs_subcategories.meta_keywords
     *
     * @var string $metaKeywords
     */
    private $metaKeywords;

    /**
     * DB field: bs_subcategories.meta_description
     *
     * @var string $metaDescription
     */
    private $metaDescription;

    /**
     * DB field: bs_subcategories.image
     *
     * @var string $image
     */
    private $image;

    /**
     * DB field: bs_subcategories.snippet
     *
     * @var string $snippet
     */
    private $snippet;

    /**
     * DB field: bs_subcategories_geotargeted.subcategory_id
     *
     * @var int $geoTargetSubcategoryId
     */
    private $geoTargetSubcategoryId;

    /**
     * DB field: bs_subcategories.description_text_html
     *
     * @var string $descriptionTextHtml
     */
    private $descriptionTextHtml;

    /**
     * DB field: bs_subcategories.intro_supplement_html
     *
     * @var string $introSupplementHtml
     */
    private $introSupplementHtml;

    /**
     * DB field: bs_subcategories.popup_html
     *
     * @var string $popupHtml
     */
    private $popupHtml;

    /**
     * DB field: bs_subcategories.description_image
     *
     * @var string $descriptionImage
     */
    private $descriptionImage;

    /**
     * DB field: bs_subcategories.image_template
     *
     * @var string $imageTemplate
     */
    private $imageTemplate;

    /**
     * DB field: bs_subcategories.description_more_info_html
     *
     * @var string $descriptionMoreInfoHtml
     */
    private $descriptionMoreInfoHtml;

    /**
     * DB field: bs_subcategories.grid_header
     *
     * @var string $gridHeader
     */
    private $gridHeader;

    /**
     * DB field: bs_subcategories_geotargeted.grid_header
     *
     * @var string $geoGridHeader
     */
    private $geoGridHeader;

    /**
     * DB field: bs_subcategories.grid_intro
     *
     * @var string $gridIntro
     */
    private $gridIntro;

    /**
     * DB field: bs_subcategories_geotargeted.grid_intro
     *
     * @var string $gridIntro
     */
    private $geoGridIntro;

    /**
     * DB field: bs_subcategories.grid_size
     *
     * @var string $gridSize
     */
    private $gridSize;

    /**
     * DB field: bs_subcategories.grid_alignment
     *
     * @var string $gridAlignment
     */
    private $gridAlignment;

    /**
     * DB field: bs_subcategories.special_header_class_name
     *
     * @var string $specialHeaderClassName
     */
    private $specialHeaderClassName;

    /**
     * DB field: bs_subcategories.show_product_number
     *
     * @var int|bool $showProductNumber
     */
    private $showProductNumber;

    /**
     * DB field: bs_subcategories.show_quickview
     *
     * @var $showQuickview
     */
    private $showQuickview;

    /**
     * DB field: bs_subcategories.show_filter
     *
     * @var $showFilter
     */
    private $showFilter;

    /**
     * DB field: bs_subcategories.show_sort
     *
     * @var $showSort
     */
    private $showSort;

    /**
     * DB field: bs_subcategories.geotarget_state_list_header
     *
     * @var $geotargetStateListHeader
     */
    private $geotargetStateListHeader;

    /**
     * DB field: bs_subcategories.geotarget_state_list_intro
     *
     * @var $geotargetStateListIntro
     */
    private $geotargetStateListIntro;

    /**
     * DB field: bs_subcategories.geotarget_dropdown_snippet
     *
     * @var string $geotargetDropdownSnippet
     */
    private $geotargetDropdownSnippet;

    /**
     * DB field: bs_subcategories.geotarget_dropdown_button
     *
     * @var int $geotargetDropdownButton
     */
    private $geotargetDropdownButton;

    /**
     * DB field: bs_subcategories.canonical_page_url
     *
     * @var int $canonicalPageUrl
     */
    private $canonicalPageUrl;

    /**
     * DB field: bs_subcategories.ppc_campaign
     *
     * @var string $ppcCampaign
     */
    private $ppcCampaign;

    /**
     * DB field: bs_subcategories.ppc_adgroup
     *
     * @var string $ppcAdgroup
     */
    private $ppcAdgroup;

    /**
     * DB field: bs_subcategories.created_by
     *
     * @var int $createdBy
     */
    private $createdBy;

    /**
     * DB field: bs_subcategories.created_date
     *
     * @var string $createdDate
     */
    private $createdDate;

    /**
     * DB field: bs_subcategories.modified_by
     *
     * @var int $modifiedBy
     */
    private $modifiedBy;

    /**
     * DB field: bs_subcategories.modified_date
     *
     * @var  string $modifiedDate
     */
    private $modifiedDate;

    /**
     * DB field: bs_subcategories.sitemap_page_change_frequency
     *
     * @var float $sitemapPageChangeFrequency
     */
    private $sitemapPageChangeFrequency;

    /**
     * DB field: bs_subcategories.sitemap_page_priority
     *
     * @var string $sitemapPagePriority
     */
    private $sitemapPagePriority;

    /**
     * DB field: bs_subcategories.sitemap_show
     *
     * @var int|bool $sitemapShow
     */
    private $sitemapShow;

    /**
     * @var array $subcategoryDetailIds
     */
    private $subcategoryDetailIds;

    /**
     * @var array $subcategoryGeoIds
     */

    private $subcategoryGeoIds;

    /**
     * @var $target
     */
    private $target;

    /**
     * @var bool $isFederal
     */
    private $federalEnabled;

    /**
     * Constructor
     *
     * @param string $id
     */
    public function __construct($id, $geotarget = false)
    {
        $this->setId($id);

        if (!is_null($this->getId())) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

            if( empty($data) ) {

                if ( !$geotarget ) {

                    $query = Connection::getHandle()->prepare( self::FULL_TABLE_DUMP . " AND sub.id =  :id GROUP BY sub.id " );

                } else {

                    $query = Connection::getHandle()->prepare( self::FULL_TABLE_DUMP . " AND subg.id =  :id GROUP BY sub.id " );

                }

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    // Cache data so we don't have to retrieve from database again
                    $this->storeCache($data);
                }
            }

            $this->setName($data['name'])->setSlug($data['slug'])->setActive($data['active'])->setPosition($data['position'])
                ->setCategoryName($data['category_name'])->setGroupingId($data['grouping_id'])->setGroupingName($data['grouping_name'])
                ->setTemplate($data['template'])->setPageTitle($data['page_title'])->setPageHeading($data['page_heading'])->setFederal($data['federal'])
                ->setMetaKeywords($data['meta_keywords'])->setMetaDescription($data['meta_description'])->setImage($data['image'])
                ->setSnippet($data['snippet'])->setDescriptionTextHtml($data['description_text_html'])->setIntroSupplementHtml($data['intro_supplement_html'])
                ->setPopupHtml($data['popup_html'])->setDescriptionImage($data['description_image'])->setImageTemplate($data['image_template'])
                ->setDescriptionMoreInfoHtml($data['description_more_info_html'])->setGridHeader($data['grid_header'])->setGeoGridHeader($data['geo_grid_header'])
                ->setGridIntro($data['grid_intro'])->setGeoGridIntro($data['geo_grid_intro'])
                ->setGridSize($data['grid_size'])->setGridAlignment($data['grid_alignment'])->setSpecialHeaderClassName($data['special_header_class_name'])
                ->setShowProductNumber($data['show_product_number'])->setShowQuickview($data['show_quickview'])->setShowFilter($data['show_filter'])
                ->setShowSort($data['show_sort'])->setGeotargetStateListHeader($data['geotarget_state_list_header'])->setGeotargetStateListIntro($data['geotarget_state_list_intro'])
                ->setGeotargetDropdownSnippet($data['geotarget_dropdown_snippet'])->setGeotargetDropdownButton($data['geotarget_dropdown_button'])
                ->setCanonicalPageUrl($data['canonical_page_url'])->setPpcCampaign($data['ppc_campaign'])->setPpcAdgroup($data['ppc_adgroup'])->setCreatedBy($data['created_by'])
                ->setCreatedDate($data['created_date'])->setModifiedBy($data['modified_by'])->setModifiedDate($data['modified_date'])
                ->setSitemapPageChangeFrequency($data['sitemap_page_change_frequency'])->setSitemapPagePriority($data['sitemap_page_priority'])->setSitemapShow($data['sitemap_show'])
                ->setSubcategoryDetailIds(isset($data['subcategory_detail_ids'])? $data['subcategory_detail_ids'] : NULL)->setSubcategoryGeoIds(isset($data['subcategory_geotarget_ids'])? $data['subcategory_geotarget_ids']:NULL)
                ->setGeoTargetSubcategoryId(isset($data['geo_target_subcategory_id'])?$data['geo_target_subcategory_id'] : NULL )->setGeotarget(isset($data['target'])?$data['target']:NULL)
                ->setCategoryId(isset($data['category_id'])? $data['category_id']:NULL);

            //Set the image paths
            $this->imagePath['grid'] = IMAGE_URL_PREFIX . '/images/catlog/product/small/';
            $this->imagePath['description'] = IMAGE_URL_PREFIX;

        } else {
            // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load properties: \'' . $this->getId() . '\' is not a valid ID number.');

        }


        // Pass the info up to the parent page
        parent::__construct(self::TYPE, $this->getId());
    }

    /*************************************************
     * Start Setters
     **************************************************/
    /**
     * Set the subcategory id
     *
     * @param $id
     * @return SubcategoryPage()
     */
    private function setId($id)
    {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
    }

    /**
     * Set the name of the subcategory
     *
     * @param $name
     * @return SubcategoryPage()
     */
    public function setName($name = "")
    {
        $this->name = (isset($name) ? trim($name) : NULL);
        return $this;
    }

    /**
     * Set fedral data
     *
     * @param bool $federal
     */
    public function setFederal($federal) {
        $this->federalEnabled = (bool)$federal;
        return $this;
    }

    /**
     * Set current subcategory page slug
     *
     * @param $slug
     * @return SubcategoryPage()
     */
    public function setSlug($slug = "")
    {
        $this->slug = !empty($slug) ? trim($slug) : NULL;
        return $this;
    }

    /**
     * Set enable or disable the subcategory
     *
     * @param $active
     * @return SubcategoryPage()
     */
    public function setActive($active = FALSE)
    {
        $this->active = (bool)$active;
        return $this;
    }

    /**
     * Set position of the subcategory in grouping page
     *
     * @param $position
     * @return SubcategoryPage()
     */
    public function setPosition($position)
    {
        $this->position = (int)$position;
        return $this;
    }

    /**
     *  Set the name of the category
     *
     * @param $categoryName
     * @return SubcategoryPage()
     */
    public function setCategoryName($categoryName = "")
    {
        $this->categoryName = (isset($categoryName) ? trim($categoryName) : NULL);
        return $this;

    }

    /**
     * Set the subcategory grouping id
     *
     * @param $groupingId
     * @return SubcategoryPage()
     */
    public function setGroupingId($groupingId = NULL)
    {
        $this->groupingId = isset($groupingId) && is_numeric($groupingId) && $groupingId > 0 ? (int)$groupingId : NULL;
        return $this;
    }

    /**
     * Set the subcategory grouping name
     *
     * @param $groupingName
     * @return SubcategoryPage()
     */
    public function setGroupingName($groupingName)
    {
        $this->groupingName = !empty($groupingName) ? trim($groupingName) : NULL;
        return $this;
    }

    /**
     * Set the subcategory single geotarget id
     *
     * @param $geoTargetSubcategoryId
     * @return Subcategory()
     */
    public function setGeoTargetSubcategoryId($geoTargetSubcategoryId)
    {
        $this->geoTargetSubcategoryId = isset($geoTargetSubcategoryId) && is_numeric($geoTargetSubcategoryId)
        && $geoTargetSubcategoryId > 0 ? (int)$geoTargetSubcategoryId : NULL;
        return $this;
    }

    /**
     * Set the subcategory template
     *
     * @param $template
     * @return SubcategoryPage()
     */
    public function setTemplate($template)
    {
        $this->template = !empty($template) ? trim($template) : NULL;
        return $this;
    }

    /**
     * Set the page title of current subcategory
     *
     * @param $pageTitle
     * @return SubcategoryPage()
     */
    public function setPageTitle($pageTitle = "")
    {
        $this->pageTitle = (isset($pageTitle) ? trim($pageTitle) : NULL);
        return $this;
    }

    /**
     * Set the page heading of current subcategory
     *
     * @param $pageHeading
     * @return SubcategoryPage()
     *
     */
    public function setPageHeading($pageHeading = "")
    {
        $this->pageHeading = (isset($pageHeading) ? trim($pageHeading) : NULL);
        return $this;

    }

    /**
     * Set meta keywords of current subcategory
     *
     * @param $metaKeywords
     * @return SubcategoryPage()
     *
     */
    public function setMetaKeywords($metaKeywords = "")
    {
        $this->metaKeywords = (isset($metaKeywords) ? trim($metaKeywords) : NULL);
        return $this;
    }

    /**
     * Set the meta description of current subcategory
     *
     * @param $metaDescription
     * @return SubcategoryPage()
     *
     */
    public function setMetaDescription($metaDescription = "")
    {
        $this->metaDescription = (isset($metaDescription) ? trim($metaDescription) : NULL);
        return $this;
    }

    /**
     * Set the grid image of subcategory
     *
     * @param $image
     * @return SubcategoryPage()
     *
     */
    public function setImage($image = "")
    {
        $this->image = (isset($image) ? trim($image) : NULL);
        return $this;
    }

    /**
     * Set the snippet of current subcategory
     *
     * @param $snippet
     * @return SubcategoryPage()
     *
     */
    public function setSnippet($snippet = "")
    {
        $this->snippet = (isset($snippet) ? trim($snippet) : NULL);
        return $this;
    }

    /**
     * Set the description html of current subcategory
     *
     * @param $descriptionTextHtml
     * @return SubcategoryPage()
     *
     */
    public function setDescriptionTextHtml($descriptionTextHtml = "")
    {
        $this->descriptionTextHtml = (isset($descriptionTextHtml) ? trim($descriptionTextHtml) : NULL);
        return $this;
    }

    /**
     * Set the intro html of current subcategory
     *
     * @param $introSupplementHtml
     * @return SubcategoryPage()
     *
     */
    public function setIntroSupplementHtml($introSupplementHtml = "")
    {
        $this->introSupplementHtml = (isset($introSupplementHtml) ? trim($introSupplementHtml) : NULL);
        return $this;
    }

    /**
     * Set the popup html of current subcategory
     *
     * @param $popupHtml
     * @return SubcategoryPage()
     *
     */
    public function setPopupHtml($popupHtml = "")
    {
        $this->popupHtml = (isset($popupHtml) ? trim($popupHtml) : NULL);
        return $this;
    }

    /**
     * Set the description image of current subcategory
     *
     * @param $descriptionImage
     * @return SubcategoryPage()
     *
     */
    public function setDescriptionImage($descriptionImage = "")
    {
        $this->descriptionImage = (isset($descriptionImage) ? trim($descriptionImage) : NULL);
        return $this;
    }

    /**
     * Set the image template of current subcategory
     *
     * @param $imageTemplate
     * @return SubcategoryPage()
     *
     */
    public function setImageTemplate($imageTemplate = "")
    {
        $this->imageTemplate = isset($imageTemplate) ? trim($imageTemplate) . '-banner' : 'basic-banner';
        return $this;
    }

    /**
     * Set description for more info of current subcategory
     *
     * @param $descriptionMoreInfoHtml
     * @return SubcategoryPage()
     *
     */
    public function setDescriptionMoreInfoHtml($descriptionMoreInfoHtml = "")
    {
        $this->descriptionMoreInfoHtml = (isset($descriptionMoreInfoHtml) ? trim($descriptionMoreInfoHtml) : NULL);
        return $this;
    }

    /**
     * Set grid header of current subcategory
     *
     * @param $gridHeader
     * @return SubcategoryPage()
     *
     */
    public function setGridHeader($gridHeader = "")
    {
        $this->gridHeader = (isset($gridHeader) ? trim($gridHeader) : NULL);
        return $this;
    }

    /**
     * Set geotarget grid header of current subcategory
     *
     * @param $geoGridHeader
     * @return SubcategoryPage()
     *
     */
    public function setGeoGridHeader($geoGridHeader = "")
    {
        $this->geoGridHeader = (isset($geoGridHeader) ? trim($geoGridHeader) : NULL);
        return $this;
    }

    /**
     * Set grid intro of current subcategory
     *
     * @param $gridIntro
     * @return SubcategoryPage()
     *
     */
    public function setGridIntro($gridIntro = "")
    {
        $this->gridIntro = (isset($gridIntro) ? trim($gridIntro) : NULL);
        return $this;
    }

    /**
     * Set geotarget grid intro of current subcategory
     *
     * @param $gridIntro
     * @return SubcategoryPage()
     *
     */
    public function setGeoGridIntro($geoGridIntro = "")
    {
        $this->geoGridIntro = (isset($geoGridIntro) ? trim($geoGridIntro) : NULL);
        return $this;
    }

    /**
     * Set grid size of current subcategory
     *
     * @param $gridSize
     * @return SubcategoryPage()
     *
     */
    public function setGridSize($gridSize = "")
    {
        $this->gridSize = (isset($gridSize) ? trim($gridSize) : NULL);
        return $this;
    }

    /**
     * Set grid alignment of current subcategory
     *
     * @param $gridAlignment
     * @return SubcategoryPage()
     *
     */
    public function setGridAlignment($gridAlignment = "")
    {
        $this->gridAlignment = (isset($gridAlignment) ? trim($gridAlignment) : NULL);
        return $this;
    }

    /**
     * Set special header class name of current subcategory
     *
     * @param $specialHeaderClassName
     * @return SubcategoryPage()
     *
     */
    public function setSpecialHeaderClassName($specialHeaderClassName = "")
    {
        $this->specialHeaderClassName = (isset($specialHeaderClassName) ? trim($specialHeaderClassName) : NULL);
        return $this;
    }

    /**
     * Set grid intro of current subcategory
     *
     * @param $showProductNumber
     * @return SubcategoryPage()
     *
     */
    public function setShowProductNumber($showProductNumber = FALSE)
    {

        $this->showProductNumber = (bool)$showProductNumber;
        return $this;
    }

    /**
     * Set show quick view of current subcategory
     *
     * @param $showQuickview
     * @return SubcategoryPage()
     *
     */
    public function setShowQuickview($showQuickview = FALSE)
    {
        $this->showQuickview = (bool)$showQuickview;
        return $this;
    }

    /**
     * Set show filter of current subcategory
     *
     * @param $showFilter
     * @return SubcategoryPage()
     *
     */
    public function setShowFilter($showFilter = FALSE)
    {
        $this->showFilter = (bool)$showFilter;
        return $this;
    }

    /**
     * Set show sort of current subcategory
     *
     * @param $showSort
     * @return SubcategoryPage()
     *
     */
    public function setShowSort($showSort = FALSE)
    {
        $this->showSort = (bool)$showSort;
        return $this;
    }

    /**
     * Set geotarget state list header of current subcategory
     *
     * @param $geotargetStateListHeader
     * @return SubcategoryPage()
     *
     */
    public function setGeotargetStateListHeader($geotargetStateListHeader = "")
    {
        $this->geotargetStateListHeader = (isset($geotargetStateListHeader) ? trim($geotargetStateListHeader) : NULL);
        return $this;
    }

    /**
     * Set geotarget state list intro of current subcategory
     *
     * @param $geotargetStateListIntro
     * @return SubcategoryPage()
     *
     */
    public function setGeotargetStateListIntro($geotargetStateListIntro = "")
    {
        $this->geotargetStateListIntro = (isset($geotargetStateListIntro) ? trim($geotargetStateListIntro) : NULL);
        return $this;
    }

    /**
     * Set geotarget dropdown snippet of current subcategory
     *
     * @param $geotargetDropdownSnippet
     * @return SubcategoryPage()
     *
     */
    public function setGeotargetDropdownSnippet($geotargetDropdownSnippet = "")
    {
        $this->geotargetDropdownSnippet = (isset($geotargetDropdownSnippet) ? trim($geotargetDropdownSnippet) : NULL);
        return $this;
    }

    /**
     * Set geotarget dropdown button of current subcategory
     *
     * @param $geotargetDropdownButton
     * @return SubcategoryPage()
     *
     */
    public function setGeotargetDropdownButton($geotargetDropdownButton = "")
    {
        $this->geotargetDropdownButton = (isset($geotargetDropdownButton) ? $geotargetDropdownButton : NULL);
        return $this;
    }

    /**
     * Set canonical page url of current subcategory
     *
     * @param $canonicalPageUrl
     * @return SubcategoryPage()
     *
     */
    public function setCanonicalPageUrl($canonicalPageUrl = NULL)
    {
        $this->canonicalPageUrl = isset($canonicalPageUrl) && is_numeric($canonicalPageUrl) && $canonicalPageUrl > 0 ? (int)$canonicalPageUrl : NULL;
        return $this;
    }

    /**
     * Set ppc campaign of current subcategory
     *
     * @param $ppcCampaign
     * @return SubcategoryPage()
     *
     */
    public function setPpcCampaign($ppcCampaign = "")
    {
        $this->ppcCampaign = (isset($ppcCampaign) ? trim($ppcCampaign) : NULL);
        return $this;
    }

    /**
     * Set ppc group of current subcategory
     *
     * @param $ppcAdgroup
     * @return SubcategoryPage()
     *
     */
    public function setPpcAdgroup($ppcAdgroup = "")
    {
        $this->ppcAdgroup = (isset($ppcAdgroup) ? trim($ppcAdgroup) : NULL);
        return $this;

    }

    /**
     * Set create by of current subcategory
     *
     * @param $createdBy
     * @return SubcategoryPage()
     *
     */
    public function setCreatedBy($createdBy = NULL)
    {
        $this->createdBy = isset($createdBy) && is_numeric($createdBy) && $createdBy > 0 ? (int)$createdBy : NULL;
        return $this;
    }

    /**
     * Set create date of current subcategory
     *
     * @param $createdDate
     * @return SubcategoryPage()
     *
     */
    public function setCreatedDate($createdDate = NULL)
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * Set modified by of current subcategory
     *
     * @param $modifiedBy
     * @return SubcategoryPage()
     *
     */
    public function setModifiedBy($modifiedBy = NULL)
    {
        $this->createdBy = isset($modifiedBy) && is_numeric($modifiedBy) && $modifiedBy > 0 ? (int)$modifiedBy : NULL;
        return $this;
    }

    /**
     * Set modified date of current subcategory
     *
     * @param $modifiedDate
     * @return SubcategoryPage()
     *
     */
    public function setModifiedDate($modifiedDate = NULL)
    {
        $this->modifiedDate = $modifiedDate;
        return $this;
    }

    /**
     * Set sitemap change frequency of current subcategory
     *
     * @param $sitemapPageChangeFrequency
     * @return SubcategoryPage()
     *
     */
    public function setSitemapPageChangeFrequency($sitemapPageChangeFrequency = NULL)
    {
        $this->sitemapPageChangeFrequency = isset($sitemapPageChangeFrequency) && is_numeric($sitemapPageChangeFrequency)
        && $sitemapPageChangeFrequency > 0 ? (int)$sitemapPageChangeFrequency : NULL;
        return $this;
    }

    /**
     * Set sitemap priority of current subcategory
     *
     * @param $sitemapPagePriority
     * @return SubcategoryPage()
     *
     */
    public function setSitemapPagePriority($sitemapPagePriority)
    {
        $this->sitemapPagePriority = $sitemapPagePriority;
        return $this;
    }

    /**
     * Set sitemap show of current subcategory
     *
     * @param $sitemapShow
     * @return SubcategoryPage()
     *
     */
    public function setSitemapShow($sitemapShow = FALSE)
    {
        $this->sitemapShow = (bool)$sitemapShow;
        return $this;
    }

    /**
     * Set the Subcategory Detail Ids
     *
     * @param $subcategoryDetailIds
     * @return $this
     */
    public function setSubcategoryDetailIds($subcategoryDetailIds)
    {
        $this->subcategoryDetailIds = !empty($subcategoryDetailIds) ? $subcategoryDetailIds : FALSE;
        return $this;
    }

    /**
     * Set the Subcategory Geotarget Ids
     *
     * @param $subcategoryGeoIds
     * @return $this
     */
    public function setSubcategoryGeoIds($subcategoryGeoIds)
    {
        $this->subcategoryGeoIds = !empty($subcategoryGeoIds) ? $subcategoryGeoIds : FALSE;
        return $this;
    }

    /**
     * Set the target
     *
     * @param $target
     * @return $this
     */
    public function setGeotarget($target){
        $this->target = !empty($target) ? trim($target) : NULL;
        return $this;
    }

    public function setCategoryId($categoryId){
        $this->categoryId = (int) $categoryId;
        return $this;
    }

    /*************************************************
     * Start Getters
     **************************************************
     * Get the Subcategory Geotarget Ids
     *
     * @return array
     */
    public function getSubcategoryGeo()
    {

        return $this->subcategoryGeoIds;
    }

    /**
     * Get the Subcategory Detail Ids
     *
     * @return array
     */
    public function getSubcategoryDetail()
    {

        return $this->subcategoryDetailIds;
    }

    /**
     * Get the grouping name
     *
     * @return string
     */
    public function getGroupingName()
    {
        return $this->groupingName;
    }

    /**
     * Get the template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Get the grid header
     *
     * @return string
     */
    public function getGridHeader()
    {
        return $this->gridHeader;
    }

    /**
     * Get the geotarget grid header
     *
     * @return string
     */
    public function getGeoGridHeader()
    {
        return $this->geoGridHeader;
    }

    /**
     * Get the grid intro
     *
     * @return string
     */
    public function getGridIntro()
    {
        return $this->gridIntro;
    }

    /**
     * Get the geotarget grid intro
     *
     * @return string
     */
    public function getGeoGridIntro()
    {
        return $this->geoGridIntro;
    }

    /**
     * Get the grid size
     *
     * @return int
     */
    public function getGridSize()
    {
        return $this->gridSize;
    }

    /**
     * Get grid alignment
     *
     * @return int
     */
    public function getGridAlignment()
    {
        return $this->gridAlignment;
    }

    /**
     * Get the product number
     *
     * @return int
     */
    public function getShowProductNumber()
    {
        return $this->showProductNumber;
    }

    /**
     * @return string
     */
    public function getSpecialHeaderClassName()
    {

        return $this->specialHeaderClassName;
    }

    /**
     * @return string
     */
    public function getSnippet()
    {

        return $this->snippet;
    }

    /**
     * @return string
     */
    public function getSlug()
    {

        return $this->slug;
    }

    /**
     * @return bool|int
     */
    public function getSitemapShow()
    {

        return $this->sitemapShow;
    }

    /**
     * @return string
     */
    public function getSitemapPagePriority()
    {

        return $this->sitemapPagePriority;
    }

    public function getGeoTargetSubcategoryId()
    {
        return $this->geoTargetSubcategoryId;
    }

    /**
     * @return float
     */
    public function getSitemapPageChangeFrequency()
    {

        return $this->sitemapPageChangeFrequency;
    }

    /**
     * @return string
     */
    public function getPpcCampaign()
    {

        return $this->ppcCampaign;
    }

    /**
     * @return string
     */
    public function getPpcAdgroup()
    {

        return $this->ppcAdgroup;
    }

    /**
     * @return int
     */
    public function getPosition()
    {

        return $this->position;
    }

    /**
     * @return string
     */
    public function getPopupHtml()
    {

        return $this->popupHtml;
    }

    /**
     * @return string
     */
    public function getPageTitle()
    {

        return $this->pageTitle;
    }

    /**
     * @return string
     */
    public function getPageHeading()
    {

        return $this->pageHeading;
    }

    /**
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * @return string
     */
    public function getModifiedDate()
    {

        return $this->modifiedDate;
    }

    /**
     * @return int
     */
    public function getModifiedBy()
    {

        return $this->modifiedBy;
    }

    /**
     * @return string
     */
    public function getMetaKeywords()
    {

        return $this->metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaDescription()
    {

        return $this->metaDescription;
    }

    /**
     * @return string
     */
    public function getIntroSupplementHtml()
    {

        return $this->introSupplementHtml;
    }

    /**
     * @return string
     */
    public function getImageTemplate()
    {

        return $this->imageTemplate;
    }

    /**
     * @return string
     */
    public function getImage()
    {

        return $this->image;
    }

    /**
     * @return int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * @return int
     */
    public function getGroupingId()
    {

        return $this->groupingId;
    }

    /**
     * @return string
     */
    public function getDescriptionTextHtml()
    {

        return $this->descriptionTextHtml;
    }

    /**
     * @return string
     */
    public function getDescriptionMoreInfoHtml()
    {

        return $this->descriptionMoreInfoHtml;
    }

    /**
     * @return string
     */
    public function getDescriptionImage()
    {

        return $this->descriptionImage;
    }

    /**
     * @return string
     */
    public function getCreatedDate()
    {

        return $this->createdDate;
    }

    /**
     * @return int
     */
    public function getCreatedBy()
    {

        return $this->createdBy;
    }

    /**
     * @return string
     */
    public function getCategoryName()
    {

        return $this->categoryName;
    }

    /**
     * @return int
     */
    public function getCanonicalPageUrl()
    {

        return $this->canonicalPageUrl;
    }

    /**
     * @return bool|int
     */
    public function getActive()
    {

        return $this->active;
    }


    /**
     * @return mixed
     */
    public function getShowQuickview()
    {
        return $this->showQuickview;
    }


    /**
     * Get show filter
     *
     * @return bool|int
     */
    public function getShowFilter()
    {
        return $this->showFilter;
    }


    /**
     * Get show sort
     *
     * @return bool|int
     */
    public function getShowSort()
    {
        return $this->showSort;
    }

    /**
     * Get geo target state listing header
     *
     * @return string
     */
    public function getGeotargetStateListHeader()
    {
        return $this->geotargetStateListHeader;
    }


    /**
     * Get geotarget state lis intro
     *
     * @return string
     */
    public function getGeotargetStateListIntro()
    {
        return $this->geotargetStateListIntro;
    }

    /**
     * Get geo target dropdown snippet
     *
     * @return string
     */
    public function getGeotargetDropdownSnippet()
    {
        return $this->geotargetDropdownSnippet;
    }


    /**
     * Get geo target dropdown buttton
     *
     * @return string
     */
    public function getGeotargetDropdownButton()
    {
        return $this->geotargetDropdownButton;
    }

    public function getCategoryId(){
        return $this->categoryId;
    }


    /**
     * Create a self object of Subcategory with out an instance
     *
     * @param null $id
     * @return SubcategoryPage
     */
    public static function create($id = NULL, $geotarget = false)
    {
        return new self($id, $geotarget);
    }


    public function getType()
    {
        return self::TYPE;
    }

    /**
     * @return array
     */
    public function getProductBySubCategoryId()
    {

        $data = array();
        $query = Connection::getHandle()->prepare(
            "SELECT product_id FROM bs_subcategory_products WHERE subcategory_id = :id "
        );

        $query->bindParam(":id", $this->getId(), PDO::PARAM_INT);

        if ($query->execute()) {

            $data[] = $query->fetch(PDO::FETCH_ASSOC);
        }

        return $data;
    }

    public function getTarget(){
        return $this->target;
    }

    public function isFederal() {
        return $this->federalEnabled;
    }


    /**
     * Converts an integer number into a string of words
     *
     * @param     int $number Number to be converted
     * @return    string                String converted to words
     */
    public function convertNumberToWords($number) {

        $hyphen = '-';
        $conjunction = ' and ';
        $separator = ', ';
        $negative = 'negative ';
        $decimal = ' point ';
        $dictionary = array (
            0                   => 'zero',
            1                   => 'one',
            2                   => 'two',
            3                   => 'three',
            4                   => 'four',
            5                   => 'five',
            6                   => 'six',
            7                   => 'seven',
            8                   => 'eight',
            9                   => 'nine',
            10                  => 'ten',
            11                  => 'eleven',
            12                  => 'twelve',
            13                  => 'thirteen',
            14                  => 'fourteen',
            15                  => 'fifteen',
            16                  => 'sixteen',
            17                  => 'seventeen',
            18                  => 'eighteen',
            19                  => 'nineteen',
            20                  => 'twenty',
            30                  => 'thirty',
            40                  => 'fourty',
            50                  => 'fifty',
            60                  => 'sixty',
            70                  => 'seventy',
            80                  => 'eighty',
            90                  => 'ninety',
            100                 => 'hundred',
            1000                => 'thousand',
            1000000             => 'million',
            1000000000          => 'billion',
            1000000000000       => 'trillion',
            1000000000000000    => 'quadrillion',
            1000000000000000000 => 'quintillion'
        );
        if( !is_numeric($number) ) {
            return FALSE;
        }
        if( ($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX ) {
            // overflow
            trigger_error(
                'convert_number_to_words only accepts numbers between -'.PHP_INT_MAX.' and '.PHP_INT_MAX, E_USER_WARNING
            );

            return FALSE;
        }
        if( $number < 0 ) {
            return $negative.convert_number_to_words(abs($number));
        }
        $string = $fraction = NULL;
        if( strpos($number, '.') !== FALSE ) {
            list($number, $fraction) = explode('.', $number);
        }
        switch (TRUE) {
            case $number < 21:
                $string = $dictionary[$number];
                break;
            case $number < 100:
                $tens = ((int) ($number / 10)) * 10;
                $units = $number % 10;
                $string = $dictionary[$tens];
                if( $units ) {
                    $string .= $hyphen.$dictionary[$units];
                }
                break;
            case $number < 1000:
                $hundreds = $number / 100;
                $remainder = $number % 100;
                $string = $dictionary[$hundreds].' '.$dictionary[100];
                if( $remainder ) {
                    $string .= $conjunction.convert_number_to_words($remainder);
                }
                break;
            default:
                $baseUnit = pow(1000, floor(log($number, 1000)));
                $numBaseUnits = (int) ($number / $baseUnit);
                $remainder = $number % $baseUnit;
                $string = convert_number_to_words($numBaseUnits).' '.$dictionary[$baseUnit];
                if( $remainder ) {
                    $string .= $remainder < 100 ? $conjunction : $separator;
                    $string .= convert_number_to_words($remainder);
                }
                break;
        }
        if( NULL !== $fraction && is_numeric($fraction) ) {
            $string .= $decimal;
            $words = array ();
            foreach (str_split((string) $fraction) as $number) {
                $words[] = $dictionary[$number];
            }
            $string .= implode(' ', $words);
        }

        return $string;
    }
}