<?php


/**
 * Class GeotargetPage
 */
class GeotargetPage extends Page {

    /**
     * Current page type
     */
    const TYPE = 'geotarget';

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "
            SELECT t.active, t.subcategory_id, s.name AS name, t.id AS id, t.target AS target, t.slug AS slug, s.grouping_id AS grouping_id, s.template AS template,
            t.page_title AS page_title, t.page_heading AS page_heading, t.meta_keywords AS meta_keywords, s.intro_supplement_html AS intro_supplement_html,
            t.meta_description AS meta_description, s.image AS image, s.snippet AS snippet, t.description_text_html AS description_text_html,
            t.description_image AS description_image, t.description_more_info_html AS description_more_info_html, t.grid_size AS geotarget_grid_size,
            s.geotarget_state_list_header AS geotarget_state_list_header, s.geotarget_state_list_intro AS geotarget_state_list_intro,
            t.subcategory_name as subcategory_name, t.canonical_page_url AS canonical_page_url, t.sitemap_page_change_frequency AS sitemap_page_change_frequency,
            s.geotarget_dropdown_snippet AS geotarget_dropdown_snippet, s.geotarget_dropdown_button AS geotarget_dropdown_button, s.popup_html AS popup_html,
            s.grid_header AS grid_header, s.grid_intro AS grid_intro, s.grid_size AS grid_size, s.grid_alignment AS grid_alignment, t.sitemap_page_priority AS sitemap_page_priority,
            s.show_product_number AS show_product_number, s.show_quickview AS show_quickview, s.show_filter AS show_filter, s.show_sort AS show_sort,
            t.special_header_class_name AS special_header_class_name, t.federal_enabled AS federal_enabled, t.grid_header AS geotarget_grid_header,
            t.grid_intro AS geotarget_grid_intro, t.grid_size AS geotarget_grid_size, t.grid_alignment AS geotarget_grid_alignment, t.sitemap_show AS sitemap_show,
            t.show_product_number AS geotarget_show_product_number, t.show_quickview AS geotarget_show_quickview, t.show_filter AS geotarget_show_filter,
            t.show_sort AS geotarget_show_sort

            FROM bs_subcategories_geotargeted t

            LEFT JOIN bs_subcategories s ON (t.subcategory_id = s.id) WHERE t.active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
     * Constant used to identify the parent's current page that is being executed
     */
    const PARENT_TYPE = NULL;

    /**
     * DB column: bs_subcategories_geotargeted.id
     *
     * @var int $id
     */
    private $id;

    /**
     * Holds the name of Geotarget page
     *
     * @var string $name
     */
    private $name;

    /**
     * DB column: bs_subcategories_geotargeted.target
     *
     * @var string $target
     */
    private $target;

    /**
     * DB column: bs_subcategories_geotargeted.slug
     *
     * @var string $slug
     */
    private $slug;

    /**
     * DB column: bs_subcategories_geotargeted.active
     *
     * @var bool|int $active
     */
    private $active;

    /**
     * Check if this Geotarget record needs to be forced
     *
     * @var bool|int
     */
    private $force = FALSE;

    /**
     * DB column: bs_subcategories_geotargeted.subcategory_id
     *
     * @var int $subcategoryId
     */
    private $subcategoryId;

    /**
     * DB column: bs_subcategories_geotargeted.subcategory_name
     *
     * @var string $subcategoryName
     */
    private $subcategoryName;

    /**
     * DB column: bs_subcategories_geotargeted.description_text_html
     *
     * @var string $descriptionTextHtml
     */
    private $descriptionTextHtml;

    /**
     * DB column: bs_subcategories_geotargeted.description_image
     *
     * @var string $descriptionImage
     */
    private $descriptionImage;

    /**
     * DB column: bs_subcategories_geotargeted.description_more_info_html
     *
     * @var string $descriptionMoreInfoHtml
     */
    private $descriptionMoreInfoHtml;

    /**
     * DB column: bs_subcategories_geotargeted.page_title
     *
     * @var string $pageTitle
     */
    private $pageTitle;

    /**
     * DB column: bs_subcategories_geotargeted.page_heading
     *
     * @var string $pageHeading
     */
    private $pageHeading;

    /**
     * DB column: bs_subcategories_geotargeted.meta_keywords
     *
     * @var string $metaKeywords
     */
    private $metaKeywords;

    /**
     * DB column: bs_subcategories_geotargeted.meta_description
     *
     * @var string $metaDescription
     */
    private $metaDescription;

    /**
     * DB column: bs_subcategories_geotargeted.grid_size
     *
     * @var string $gridSize
     */
    private $gridSize;

    /**
     * DB column: bs_subcategories_geotargeted.grid_alignment
     *
     * @var string $gridAlignment
     */
    private $gridAlignment;

    /**
     * DB column: bs_subcategories_geotargeted.special_header_class_name
     *
     * @var string $specialHeaderClassName
     */
    private $specialHeaderClassName;

    /**
     * DB column: bs_subcategories_geotargeted.show_product_number
     *
     * @var bool|int $showProductNumber
     */
    private $showProductNumber;

    /**
     * DB column: bs_subcategories_geotargeted.show_quickview
     *
     * @var bool|int $showQuickview
     */
    private $showQuickview;

    /**
     * DB column: bs_subcategories_geotargeted.show_filter
     *
     * @var bool|int $showFilter
     */
    private $showFilter;

    /**
     * DB column: bs_subcategories_geotargeted.show_sort
     *
     * @var bool|int $showSort
     */
    private $showSort;

    /**
     * DB column: bs_subcategories_geotargeted.federal_enabled
     *
     * @var bool|int $federalEnabled
     */
    private $federalEnabled;

    /**
     * DB column: bs_subcategories_geotargeted.grid_header
     *
     * @var string $gridHeader
     */
    private $gridHeader;

    /**
     * DB column: bs_subcategories_geotargeted.grid_intro
     *
     * @var string $gridIntro
     */
    private $gridIntro;

    /**
     * DB column: bs_subcategories_geotargeted.canonical_page_url
     *
     * @var int $canonicalPageUrl
     */
    private $canonicalPageUrl;

    /**
     * DB column: bs_subcategories_geotargeted.sitemap_page_change_frequency
     *
     * @var int $sitemapPageChangeFrequency
     */
    private $sitemapPageChangeFrequency;

    /**
     * DB column: bs_subcategories_geotargeted.sitemap_page_priority
     *
     * @var string $sitemapPagePriority
     */
    private $sitemapPagePriority;

    /**
     * DB column: bs_subcategories_geotargeted.sitemap_show
     *
     * @var bool|int $sitemapShow
     */
    private $sitemapShow;

    /**
     * DB column: bs_subcategories.intro_supplement_html
     *
     * @var string $introSupplementHtml
     */
    private $introSupplementHtml;

    /**
     * DB column: bs_subcategories.popup_html
     *
     * @var string $popupHtml
     */
    private $popupHtml;

    /**
     * Hold our image paths
     *
     * @var string $image
     */
    public $image;

    /**
     * @var string $imageTemplate
     */
    private $imageTemplate = 'basic-banner';

    /**
     * Our class constructor
     *
     * @param int $id
     */
    public function __construct($id) {

        $this->setId($id);

        if( !is_null($this->getId()) ) {

            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());

            // Attempt to get data from cache
            $data = $this->getCache();

            if ( empty($data) ) {

                //Set the image paths
                $this->imagePath['grid'] = IMAGE_URL_PREFIX . '/images/catlog/product/small/';
                $this->imagePath['description'] = IMAGE_URL_PREFIX;

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND t.id = :id ");

                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);

                    // Cache data so we don't have to retrieve from database again
                    $this->storeCache($data);
                }
            }

            $this->setTarget($data['target'])->setSlug($data['slug'])->setActive($data['active'])->setSubcategoryId($data['subcategory_id'])
                 ->setSubcategoryName($data['subcategory_name'])->setDescriptionTextHtml($data['description_text_html'])->setPopupHtml($data['popup_html'])
                 ->setDescriptionImage($data['description_image'])->setDescriptionMoreInfoHtml($data['description_more_info_html'])
                 ->setPageTitle($data['page_title'])->setPageHeading($data['page_heading'])->setMetaKeywords($data['meta_keywords'])
                 ->setMetaDescription($data['meta_description'])->setGridSize($data['grid_size'])->setGridAlignment($data['grid_alignment'])
                 ->setSpecialHeaderClassName($data['special_header_class_name'])->setShowProductNumber($data['show_product_number'])
                 ->setShowQuickview($data['show_quickview'])->setShowFilter($data['show_filter'])->setShowSort($data['show_sort'])
                 ->setFederalEnabled($data['federal_enabled'])->setGridHeader($data['grid_header'])->setGridIntro($data['grid_intro'])
                 ->setCanonicalPageUrl($data['canonical_page_url'])->setSitemapPageChangeFrequency($data['sitemap_page_change_frequency'])
                 ->setSitemapPagePriority($data['sitemap_page_priority'])->setSitemapShow($data['sitemap_show'])->setName($data['name'])
                 ->setIntroSupplementHtml($data['intro_supplement_html']);
        }else{
            // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load properties: \''.$this->getId().'\' is not a valid ID number.');

        }
        // Pass the info up to the parent page
        parent::__construct(self::TYPE, $this->getId());
    }

    /*************************************************************
     *  Setters                                                 **
     *************************************************************
     *
     * Set the Geotarget page id
     *
     * @param $id
     * @return GeotargetPage()
     */
    private function setId($id) {

        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
    }

    /**
     * Set the Geotarget Page name
     *
     * @param $name
     * @return GeotargetPage()
     */
    public function setName($name) {

        $this->name = !empty($name) ? trim($name) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget page target
     *
     * @param $target
     * @return GeotargetPage()
     */
    public function setTarget($target) {

        $this->target = !empty($target) ? trim($target) : NULL;
        return $this;
    }

    /**
     * Set Geotarget page subcategory id
     *
     * @param $subcategoryId
     * @return GeotargetPage()
     */
    public function setSubcategoryId($subcategoryId) {

        $this->subcategoryId = isset($subcategoryId) && is_numeric($subcategoryId) && $subcategoryId > 0 ? (int) $subcategoryId : NULL;

        return $this;
    }

    /**
     * Set Geotarget page $introSupplementHtml
     *
     * @param $introSupplementHtml
     * @return GeotargetPage
     */
    public function setIntroSupplementHtml($introSupplementHtml) {

        $this->introSupplementHtml = !empty($introSupplementHtml) ? trim($introSupplementHtml) : NULL;
        return $this;
    }

    /**
     * Set Geotarget page $popupHtml
     *
     * @param $popupHtml
     * @return $this
     */
    public function setPopupHtml($popupHtml) {

        $this->popupHtml = !empty($popupHtml) ? trim($popupHtml) : NULL;
        return $this;
    }

    /**
     * Set Geotarget page subcategory name
     *
     * @param $subcategoryName
     * @return GeotargetPage()
     */
    public function setSubcategoryName($subcategoryName) {

        $this->subcategoryName = !empty($subcategoryName) ? trim($subcategoryName) : NULL;
        return $this;
    }

    /**
     * Set Geotarget page federal visibility
     *
     * @param $federalEnabled
     * @return GeotargetPage()
     */
    public function setFederalEnabled($federalEnabled = FALSE) {

        $this->federalEnabled = (bool)$federalEnabled;
        return $this;
    }

    /**
     * Set the Geotarget Page url slug
     *
     * @param $slug
     * @return GeotargetPage
     */
    public function setSlug($slug) {

        $this->slug = !empty($slug) ? trim($slug) : NULL;
        return $this;
    }

    /**
     * Do we really need this? We are currently only returning records that are true.
     *
     * @param $active
     * @return GeotargetPage
     */
    private function setActive($active) {

        $this->active = (bool)$active;
        return $this;
    }

    /**
     * Set the description html text for the Geotarget Page
     *
     * @param $descriptionTextHtml
     * @return GeotargetPage
     */
    private function setDescriptionTextHtml($descriptionTextHtml) {

        $this->descriptionTextHtml = !empty($descriptionTextHtml) ? trim($descriptionTextHtml) : NULL;
        return $this;
    }

    /**
     * Set the image description for the Geotarget Page
     *
     * @param $descriptionImage
     * @return GeotargetPage
     */
    private function setDescriptionImage($descriptionImage) {

        $this->descriptionImage = !empty($descriptionImage) ? trim($descriptionImage) : NULL;
        return $this;
    }

    /**
     * Set the html description for the Geotarget Page
     *
     * @param $descriptionMoreInfoHtml
     * @return GeotargetPage
     */
    private function setDescriptionMoreInfoHtml($descriptionMoreInfoHtml) {

        $this->descriptionMoreInfoHtml = !empty($descriptionMoreInfoHtml) ? trim($descriptionMoreInfoHtml) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget page title
     *
     * @param $pageTitle
     * @return GeotargetPage
     */
    private function setPageTitle($pageTitle) {

        $this->pageTitle = !empty($pageTitle) ? trim($pageTitle) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget page heading
     *
     * @param $pageHeading
     * @return GeotargetPage
     */
    private function setPageHeading($pageHeading) {

        $this->pageHeading = !empty($pageHeading) ? trim($pageHeading) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget page metakeywords
     *
     * @param $metaKeywords
     * @return GeotargetPage
     */
    private function setMetaKeywords($metaKeywords) {

        $this->metaKeywords = !empty($metaKeywords) ? trim($metaKeywords) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget Metadescription
     *
     * @param $metaDescription
     * @return GeotargetPage
     */
    private function setMetaDescription($metaDescription) {

        $this->metaDescription = !empty($metaDescription) ? trim($metaDescription) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget grid size
     *
     * @param $gridSize
     * @return GeotargetPage
     */
    private function setGridSize($gridSize) {

        $this->gridSize = $gridSize;
        return $this;
    }

    /**
     * Set the Geotarget alignment
     *
     * @param $gridAlignment
     * @return GeotargetPage
     */
    private function setGridAlignment($gridAlignment) {

        $this->gridAlignment = $gridAlignment;
        return $this;
    }

    /**
     * Set the Geotarget special header class name
     *
     * @param $specialHeaderClassName
     * @return GeotargetPage
     */
    private function setSpecialHeaderClassName($specialHeaderClassName) {

        $this->specialHeaderClassName = !empty($specialHeaderClassName) ? trim($specialHeaderClassName) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget Page product number visibility
     *
     * @param $showProductNumber
     * @return GeotargetPage
     */
    private function setShowProductNumber($showProductNumber) {

        $this->showProductNumber = $showProductNumber;
        return $this;
    }

    /**
     * Set the Geotarget page quick view
     *
     * @param $showQuickview
     * @return GeotargetPage
     */
    private function setShowQuickview($showQuickview) {

        $this->showQuickview = (bool) $showQuickview;
        return $this;
    }

    /**
     * Set the Geotarget page filter visibility
     *
     * @param $showFilter
     * @return GeotargetPage
     */
    private function setShowFilter($showFilter) {

        $this->showFilter = (bool) $showFilter;
        return $this;
    }

    /**
     * Set the Geotarget shorting
     *
     * @param $showSort
     * @return GeotargetPage
     */
    private function setShowSort($showSort) {

        $this->showSort = (bool) $showSort;
        return $this;
    }

    /**
     * Set the Geotarget page grid header
     *
     * @param $gridHeader
     * @return GeotargetPage
     */
    private function setGridHeader($gridHeader) {

        $this->gridHeader = !empty($gridHeader) ? trim($gridHeader) : NULL;
        return $this;
    }

    /**
     * Set the GeotargetPage grid intro
     *
     * @param $gridIntro
     * @return GeotargetPage
     */
    private function setGridIntro($gridIntro) {

        $this->gridIntro = !empty($gridIntro) ? trim($gridIntro) : NULL;
        return $this;
    }

    /**
     * Set the Geotarget Page canonicalPageUrl
     *
     * @param $canonicalPageUrl
     * @return GeotargetPage
     */
    private function setCanonicalPageUrl($canonicalPageUrl) {

        $this->canonicalPageUrl = $canonicalPageUrl;
        return $this;
    }

    /**
     * Set the Geotarget Page frequency
     *
     * @param $sitemapPageChangeFrequency
     * @return GeotargetPage
     */
    private function setSitemapPageChangeFrequency($sitemapPageChangeFrequency) {

        $this->sitemapPageChangeFrequency = $sitemapPageChangeFrequency;
        return $this;
    }

    /**
     * Set the Geotarget Page priority
     *
     * @param $sitemapPagePriority
     * @return GeotargetPage
     */
    private function setSitemapPagePriority($sitemapPagePriority) {

        $this->sitemapPagePriority = $sitemapPagePriority;
        return $this;
    }

    /**
     * Set the Geotarget sitemap visibility
     *
     * @param $sitemapShow
     * @return GeotargetPage
     */
    private function setSitemapShow($sitemapShow) {

        $this->sitemapShow = (bool) $sitemapShow;
        return $this;
    }

    /*************************************************************
     *  Getters                                                  *
     *************************************************************
     *
     * Get the Geotarget page id
     *
     * @return int $id
     */
    public function getId() {

        return $this->id;
    }

    /**
     * Get the Geotarget page name
     *
     * @return string $name
     */
    public function name() {

        return $this->name;
    }

    /**
     * Get Geotarget Page $target;
     *
     * @return string $target;
     */
    public function getTarget() {

        return $this->target;
    }

    /**
     * Get the Geotarget page image template
     *
     * @return string $imageTemplate
     */
    public function getImageTemplate() {

        return $this->imageTemplate;
    }

    /**
     * Get Geotarget Page $slug;
     *
     * @return string $slug;
     */
    public function getSlug() {

        return $this->slug;
    }

    /**
     * Get Geotarget Page $active;
     *
     * @return bool|int $active;
     */
    public function getActive() {

        return $this->active;
    }

    /**
     * Get Geotarget Page $subcategoryId;
     *
     * @return int $subcategoryId;
     */
    public function getSubcategoryId() {

        return $this->subcategoryId;
    }

    /**
     * Get Geotarget Page $subcategoryName;
     *
     * @return string $subcategoryName;
     */
    public function getSubcategoryName() {

        return $this->subcategoryName;
    }

    /**
     * Get Geotarget Page $descriptionTextHtml;
     *
     * @return string $descriptionTextHtml;
     */
    public function getDescriptionTextHtml() {

        return $this->descriptionTextHtml;
    }

    /**
     * Get Geotarget Page $descriptionImage;
     *
     * @return string $descriptionImage;
     */
    public function getDescriptionImage() {

        return $this->descriptionImage;
    }

    /**
     * Get Geotarget Page $descriptionMoreInfoHtml;
     *
     * @return string $descriptionMoreInfoHtml;
     */
    public function getDescriptionMoreInfoHtml() {

        return $this->descriptionMoreInfoHtml;
    }

    /**
     * Get Geotarget Page $pageTitle;
     *
     * @return string $pageTitle;
     */
    public function getPageTitle() {

        return $this->pageTitle;
    }

    /**
     * Get Geotarget Page $pageHeading;
     *
     * @return string $pageHeading;
     */
    public function getPageHeading() {

        return $this->pageHeading;
    }

    /**
     * Get Geotarget Page $metaKeywords;
     *
     * @return string $metaKeywords;
     */
    public function getMetaKeywords() {

        return $this->metaKeywords;
    }

    /**
     * Get Geotarget Page $metaDescription;
     *
     * @return string $metaDescription;
     */
    public function getMetaDescription() {

        return $this->metaDescription;
    }

    /**
     * Get Geotarget Page $gridSize;
     *
     * @return string $gridSize;
     */
    public function getGridSize() {

        return $this->gridSize;
    }

    /**
     * Get Geotarget Page $gridAlignment;
     *
     * @return string $gridAlignment;
     */
    public function getGridAlignment() {

        return $this->gridAlignment;
    }

    /**
     * Get Geotarget Page $specialHeaderClassName;
     *
     * @return string $specialHeaderClassName;
     */
    public function getSpecialHeaderClassName() {

        return $this->specialHeaderClassName;
    }

    /**
     * Get Geotarget Page $showProductNumber;
     *
     * @return bool|int $showProductNumber;
     */
    public function getShowProductNumber() {

        return $this->showProductNumber;
    }

    /**
     * Get Geotarget Page $showQuickview;
     *
     * @return bool|int $showQuickview;
     */
    public function getShowQuickview() {

        return $this->showQuickview;
    }

    /**
     * Get Geotarget Page $showFilter;
     *
     * @return bool|int $showFilter;
     */
    public function getShowFilter() {

        return $this->showFilter;
    }

    /**
     * Get Geotarget Page $showSort;
     *
     * @return bool|int $showSort;
     */
    public function getShowSort() {

        return $this->showSort;
    }

    /**
     * Get Geotarget Page $federalEnabled;
     *
     * @return bool|int $federalEnabled;
     */
    public function getFederalEnabled() {

        return $this->federalEnabled;
    }

    /**
     * Get Geotarget Page $gridHeader;
     *
     * @return string $gridHeader;
     */
    public function getGridHeader() {

        return $this->gridHeader;
    }

    /**
     * Get Geotarget Page $gridIntro;
     *
     * @return string $gridIntro;
     */
    public function getGridIntro() {

        return $this->gridIntro;
    }

    /**
     * Get Geotarget Page $canonicalPageUrl;
     *
     * @return int $canonicalPageUrl;
     */
    public function getCanonicalPageUrl() {

        return $this->canonicalPageUrl;
    }

    /**
     * Get Geotarget Page $sitemapPageChangeFrequency;
     *
     * @return int $sitemapPageChangeFrequency;
     */
    public function getSitemapPageChangeFrequency() {

        return $this->sitemapPageChangeFrequency;
    }

    /**
     * Get GeotargetPage $setIntroSupplementHtml
     *
     * @return string
     */
    public function getIntroSupplementHtml() {

        return $this->introSupplementHtml;
    }

    /**
     * Get GeotargetPage $setPopupHtml
     *
     * @return string $setPopupHtml;
     */
    public function getPopupHtml() {

        return $this->popupHtml;
    }

    /**
     * Get Geotarget Page $sitemapPagePriority;
     *
     * @return int $sitemapPagePriority;
     */
    public function getSitemapPagePriority() {

        return $this->sitemapPagePriority;
    }

    /**
     * Get Geotarget Page $sitemapShow;
     *
     * @return bool|int $sitemapShow;
     */
    public function getSitemapShow() {

        return $this->sitemapShow;
    }

    /**
     * Get the type of this class
     *
     * @return string self::TYPE
     */
    public function getType() {
        return self::TYPE;
    }


    /**
     * Gets a list of zones for a geotarget
     *
     * @param     int $id ID of geotarget
     * @return    array           Array of zones
     */
    public function getGeotargetList($id) {

        $sql = Connection::getHandle()->prepare("SELECT g.*, z.zone_name AS zone_name FROM bs_subcategories_geotargeted g
								  LEFT JOIN bs_zones z ON (g.target = z.zone_code)
								  WHERE g.subcategory_id = ?
								  AND g.active = 1
								  ORDER BY z.zone_name");
        $sql->execute(array($id));

        while ($row = $sql->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Create a static instances in this realm..
     *
     * @param null|int $id
     * @return GeotargetPage
     */
    public static function create($id = NULL) {
        return new self($id);
    }
}