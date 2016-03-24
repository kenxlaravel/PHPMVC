<?php


/**
 * Class LandingPage
 */
class LandingPage extends Page {

    /**
     * Type of page that we
     */
	const TYPE  = 'landing';

    /**
     *  Holds the parent page type
     */
    const PARENT_TYPE = NULL;

    /**
     * Constant used for two purposes
     *
     * - Getting the record from the database
     * - FULL_TABLE_DUMP is a keyword used for our cache system so it can know what query to run
     */
    const FULL_TABLE_DUMP = "SELECT id, name, short_name, slug, active, page_title, page_heading, meta_keywords,
                                meta_description, description_text_html, intro_supplement_html, popup_html, description_image,
                                image_template, description_more_info_html, grid_alternate, product_grid_header, product_grid_intro,
                                product_grid_size, show_product_number, show_quickview, show_filter, show_sort, special_header_class_name,
                                canonical_page_url, ppc_campaign, ppc_adgroup, created_date, created_by, modified_date, modified_by,
                                sitemap_page_change_frequency, sitemap_page_priority, sitemap_show
                             FROM `bs_landings` WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
     * @var int $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * @var string $shortName
     */
    private $shortName;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var int|bool $active
     */
    private $active;

    /**
     * @var string $pageTitle
     */
    private $pageTitle;

    /**
     * @var string $pageHeading
     */
    private $pageHeading;

    /**
     * @var string $metaKeywords
     */
    private $metaKeywords;

    /**
     * @var string $metaDescription
     */
    private $metaDescription;

    /**
     * @var string descriptionTextHtml
     */
    private $descriptionTextHtml;

    /**
     * @var string introSupplementHtml
     */
    private $introSupplementHtml;

    /**
     * @var string popupHtml
     */
    private $popupHtml;

    /**
     * @var string $descriptionImage
     */
    private $descriptionImage;

    /**
     * @var string $imageTemplate;
     */
    private $imageTemplate;

    /**
     * @var string $descriptionMoreInfoHtml
     */
    private $descriptionMoreInfoHtml;

    /**
     * @var string $gridAlternate
     */
    private $gridAlternate;

    /**
     * @var string $productGridHeader
     */
    private $productGridHeader;

    /**
     * @var string $productGridIntro;
     */
    private $productGridIntro;

    /**
     * @var string $productGridSize
     */
    private $productGridSize;

    /**
     * @var int $showProductNumber
     */
    private $showProductNumber;

    /**
     * @var int $showQuickview
     */
    private $showQuickview;

    /**
     * @var int|bool $showFilter
     */
    private $showFilter;

    /**
     * @var int|bool
     */
    private $showSort;

    /**
     * @var string $specialHeaderClassName
     */
    private $specialHeaderClassName;

    /**
     * @var int $canonicalPageUrl
     */
    private $canonicalPageUrl;

    /**
     * @var string $ppcCampaign
     */
    private $ppcCampaign;

    /**
     * @var string $ppcAdgroup
     */
    private $ppcAdgroup;

    /**
     * @var string $createdDate
     */
    private $createdDate;

    /**
     * @var int $createdBy
     */
    private $createdBy;

    /**
     * @var string $modifiedDate
     */
    private $modifiedDate;

    /**
     * @var int $modifiedBy
     */
    private $modifiedBy;

    /**
     * @var int $sitemapPageChangeFrequency
     */
    private $sitemapPageChangeFrequency;

    /**
     * @var int $sitemapPagePriority;
     */
    private $sitemapPagePriority;

    /**
     * @var int|bool $sitemapShow
     */
    private $sitemapShow;


    /**
     * @param string $id
     */
	public function __construct($id) {

		$this->setId($id);

        if ( !is_null($this->getId()) ) {
            // Set cache object
            CacheableEntity::__construct(get_class($this), $this->getId());
            // Attempt to get data from cache
            $data = $this->getCache();

            if( empty($data) ) {

                $query = Connection::getHandle()->prepare(self::FULL_TABLE_DUMP . " AND id = :id ");
                $query->bindParam(':id', $this->getId(), PDO::PARAM_INT);

                if ($query->execute()) {

                    $data = $query->fetch(PDO::FETCH_ASSOC);
                    // Cache data so we don't have to retrieve from database again
                    $this->storeCache($data);
                }
            }

            /* SET CLASS PROPERTIES FROM $data */
            $this->setName($data['name'])
                ->setShortName($data['short_name'])
                ->setSlug($data['slug'])
                ->setActive($data['active'])
                ->setPageTitle($data['page_title'])
                ->setPageHeading($data['page_heading'])
                ->setMetaKeywords($data['meta_keywords'])
                ->setMetaDescription($data['meta_description'])
                ->setDescriptionTextHtml($data['description_text_html'])
                ->setIntroSupplementHtml($data['intro_supplement_html'])
                ->setPopupHtml($data['popup_html'])
                ->setDescriptionImage($data['description_image'])
                ->setImageTemplate($data['image_template'])
                ->setDescriptionMoreInfoHtml($data['description_more_info_html'])
                ->setGridAlternate($data['grid_alternate'])
                ->setProductGridHeader($data['product_grid_header'])
                ->setProductGridIntro($data['product_grid_intro'])
                ->setProductGridSize($data['product_grid_size'])
                ->setShowProductNumber($data['show_product_number'])
                ->setShowQuickview($data['show_quickview'])
                ->setShowFilter($data['show_filter'])
                ->setShowSort($data['show_sort'])
                ->setSpecialHeaderClassName($data['special_header_class_name'])
                ->setCanonicalPageUrl($data['canonical_page_url'])
                ->setPpcCampaign($data['ppc_campaign'])
                ->setPpcAdgroup($data['ppc_adgroup'])
                ->setCreatedDate($data['created_date'])
                ->setCreatedBy($data['created_by'])
                ->setModifiedDate($data['modified_date'])
                ->setModifiedBy($data['modified_by'])
                ->setSitemapPageChangeFrequency($data['sitemap_page_change_frequency'])
                ->setSitemapPagePriority($data['sitemap_page_priority'])
                ->setSitemapShow($data['sitemap_show']);

        } else {

            // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load properties: \'' . $this->getId() . '\' is not a valid ID number.');
        }


        // Pass the info up to the parent page
		parent::__construct(self::TYPE, $this->getId());
}

	/********** Setters ****************
     *
     * @param [int] $id
     * @return LandingPage() instance
     */
    public function setId($id) {

        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int) $id : NULL;
        return $this;
    }

    /**
     * @param $name
     * @return LandingPage()
     */
    public function setName($name) {
        $this->name = !empty($name) ? trim($name) : NULL;
        return $this;
    }

    /**
     * @param $shortName
     * @return LandingPage()
     */
    public function setShortName($shortName) {
        $this->shortName = !empty($shortName) ? trim($shortName) : NULL;
        return $this;
    }

    /**
     * @param $slug
     * @return LandingPage()
     */
    public function setSlug($slug) {
        $this->slug = !empty($slug) ? trim($slug) : NULL;
        return $this;
    }

    /**
     * @param $active
     * @return LandingPage()
     */
    public function setActive($active = FALSE) {
        $this->active = (bool) $active;
        return $this;
    }

    /**
     * @param $pageTitle
     * @return LandingPage()
     */
    public function setPageTitle($pageTitle) {
        $this->pageTitle = !empty($pageTitle) ? trim($pageTitle) : NULL;
        return $this;
    }

    /**
     * @param $pageHeading
     * @return LandingPage()
     */
    public function setPageHeading($pageHeading) {
        $this->pageHeading = !empty($pageHeading) ? trim($pageHeading) : NULL;
        return $this;
    }

    /**
     * @param $metaKeywords
     * @return LandingPage()
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = !empty($metaKeywords) ? trim($metaKeywords) : NULL;
        return $this;
    }

    /**
     * @param $metaDescription
     * @return LandingPage()
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = !empty($metaDescription) ? trim($metaDescription) : NULL;
        return $this;
    }

    /**
     * @param $descriptionTextHtml
     * @return LandingPage()
     */
    public function setDescriptionTextHtml($descriptionTextHtml) {
        $this->descriptionTextHtml = !empty($descriptionTextHtml) ? trim($descriptionTextHtml) : NULL;
        return $this;
    }

    /**
     * @param $introSupplementHtml
     * @return LandingPage()
     */
    public function setIntroSupplementHtml($introSupplementHtml) {
        $this->introSupplementHtml = $introSupplementHtml;
        return $this;
    }

    /**
     * @param $popupHtml
     * @return LandingPage()
     */
    public function setPopupHtml($popupHtml) {
        $this->popupHtml = $popupHtml;
        return $this;
    }

    /**
     * @param $descriptionImage
     * @return LandingPage()
     */
    public function setDescriptionImage($descriptionImage) {
        $this->descriptionImage = $descriptionImage;
        return $this;
    }

    /**
     * @param $imageTemplate
     * @return LandingPage()
     */
    public function setImageTemplate($imageTemplate) {
        $this->imageTemplate = isset($imageTemplate) ? trim($imageTemplate).'-banner' : 'basic-banner';
        return $this;
    }

    /**
     * @param $descriptionMoreInfoHtml
     * @return LandingPage()
     */
    public function setDescriptionMoreInfoHtml($descriptionMoreInfoHtml) {
        $this->descriptionMoreInfoHtml = $descriptionMoreInfoHtml;
        return $this;
    }

    /**
     * @param $gridAlternate
     * @return LandingPage()
     */
    public function setGridAlternate($gridAlternate) {
        $this->gridAlternate = $gridAlternate;
        return $this;
    }

    /**
     * @param $productGridHeader
     * @return LandingPage()
     */
    public function setProductGridHeader($productGridHeader) {
        $this->productGridHeader = !empty($productGridHeader) ? trim($productGridHeader) : NULL;
        return $this;
    }

    /**
     * @param $productGridIntro
     * @return LandingPage()
     */
    public function setProductGridIntro($productGridIntro) {
        $this->productGridIntro = !empty($productGridIntro) ? trim($productGridIntro) : NULL;
        return $this;
    }

    /**
     * @param $productGridSize
     * @return LandingPage()
     */
    public function setProductGridSize($productGridSize) {
        $this->productGridSize = $productGridSize;
        return $this;
    }

    /**
     * @param $showProductNumber
     * @return LandingPage()
     */
    public function setShowProductNumber($showProductNumber) {
        $this->showProductNumber = $showProductNumber;
        return $this;
    }

    /**
     * @param $showQuickview
     * @return LandingPage()
     */
    public function setShowQuickview($showQuickview) {
        $this->showQuickview = $showQuickview;
        return $this;
    }

    /**
     * @param $showFilter
     * @return LandingPage()
     */
    public function setShowFilter($showFilter) {
        $this->showFilter = $showFilter;
        return $this;
    }

    /**
     * @param $showSort
     * @return LandingPage()
     */
    public function setShowSort($showSort) {
        $this->showSort = $showSort;
        return $this;
    }

    /**
     * @param $specialHeaderClassName
     * @return LandingPage()
     */
    public function setSpecialHeaderClassName($specialHeaderClassName) {
        $this->specialHeaderClassName = $specialHeaderClassName;
        return $this;
    }

    /**
     * @param $canonicalPageUrl
     * @return LandingPage()
     */
    public function setCanonicalPageUrl($canonicalPageUrl) {
        $this->canonicalPageUrl = $canonicalPageUrl;
        return $this;
    }

    /**
     * @param $ppcCampaign
     * @return LandingPage()
     */
    public function setPpcCampaign($ppcCampaign) {
        $this->ppcCampaign = $ppcCampaign;
        return $this;
    }

    /**
     * @param $ppcAdgroup
     * @return LandingPage()
     */
    public function setPpcAdgroup($ppcAdgroup) {
        $this->ppcAdgroup = $ppcAdgroup;
        return $this;
    }

    /**
     * @param $createdDate
     * @return LandingPage()
     */
    public function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * @param $createdBy
     * @return LandingPage()
     */
    public function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * @param $modifiedDate
     * @return LandingPage()
     */
    public function setModifiedDate($modifiedDate) {
        $this->modifiedDate = $modifiedDate;
        return $this;
    }

    /**
     * @param $modifiedBy
     * @return LandingPage()
     */
    public function setModifiedBy($modifiedBy) {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    /**
     * @param $sitemapPageChangeFrequency
     * @return LandingPage()
     */
    public function setSitemapPageChangeFrequency($sitemapPageChangeFrequency) {
        $this->sitemapPageChangeFrequency = $sitemapPageChangeFrequency;
        return $this;
    }

    /**
     * @param $sitemapPagePriority
     * @return LandingPage()
     */
    public function setSitemapPagePriority($sitemapPagePriority) {
        $this->sitemapPagePriority = $sitemapPagePriority;
        return $this;
    }

    /**
     * @param $sitemapShow
     * @return LandingPage()
     */
    public function setSitemapShow($sitemapShow) {
        $this->sitemapShow = $sitemapShow;
        return $this;
    }


    /********* Getters *******/

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getShortName() {
        return $this->shortName;
    }

    /**
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * @return bool|int
     */
    public function getActive() {
        return $this->active;
    }

    /**
     * @return string
     */
    public function getPageTitle() {
        return $this->pageTitle;
    }

    /**
     * @return string
     */
    public function getPageHeading() {
        return $this->pageHeading;
    }

    /**
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    /**
     * @return string
     */
    public function getDescriptionTextHtml() {
        return $this->descriptionTextHtml;
    }

    /**
     * @return string
     */
    public function getIntroSupplementHtml() {
        return $this->introSupplementHtml;
    }

    /**
     * @return string
     */
    public function getPopupHtml() {
        return $this->popupHtml;
    }

    /**
     * @return string
     */
    public function getDescriptionImage() {
        return $this->descriptionImage;
    }

    /**
     * @return string
     */
    public function getImageTemplate() {
        return $this->imageTemplate;
    }

    /**
     * @return string
     */
    public function getDescriptionMoreInfoHtml() {
        return $this->descriptionMoreInfoHtml;
    }

    /**
     * @return string
     */
    public function getGridAlternate() {
        return $this->gridAlternate;
    }

    /**
     * @return string
     */
    public function getProductGridHeader() {
        return $this->productGridHeader;
    }

    /**
     * @return string
     */
    public function getProductGridIntro() {
        return $this->productGridIntro;
    }

    /**
     * @return string
     */
    public function getProductGridSize() {
        return $this->productGridSize;
    }

    /**
     * @return int
     */
    public function getShowProductNumber() {
        return $this->showProductNumber;
    }

    /**
     * @return int
     */
    public function getShowQuickview() {
        return $this->showQuickview;
    }

    /**
     * @return bool|int
     */
    public function getShowFilter() {
        return $this->showFilter;
    }

    /**
     * @return bool|int
     */
    public function getShowSort() {
        return $this->showSort;
    }

    /**
     * @return string
     */
    public function getSpecialHeaderClassName() {
        return $this->specialHeaderClassName;
    }

    /**
     * @return int
     */
    public function getCanonicalPageUrl() {
        return $this->canonicalPageUrl;
    }

    /**
     * @return string
     */
    public function getPpcCampaign() {
        return $this->ppcCampaign;
    }

    /**
     * @return string
     */
    public function getPpcAdgroup() {
        return $this->ppcAdgroup;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate() {
        return $this->createdDate;
    }

    /**
     * @return int
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * @return string
     */
    public function getModifiedDate() {
        return $this->modifiedDate;
    }

    /**
     * @return int
     */
    public function getModifiedBy() {
        return $this->modifiedBy;
    }

    /**
     * @return int
     */
    public function getSitemapPageChangeFrequency() {
        return $this->sitemapPageChangeFrequency;
    }

    /**
     * @return int
     */
    public function getSitemapPagePriority() {
        return $this->sitemapPagePriority;
    }

    /**
     * @return bool|int
     */
    public function getSitemapShow() {
        return $this->sitemapShow;
    }

    /**
     * Used to get the top subcategory listings for a landing page
     * @return array $productRow - an array of all the listings
     */
    public function getListings() {

        $productRow = array();

        $sql = Connection::getHandle()->prepare("SELECT *
							FROM bs_landing_grid
							WHERE active = TRUE
							AND landing_id = :id
							ORDER BY position");

        $sql->bindParam(":id", $this->getId(), PDO::PARAM_INT);

        if ( $sql->execute() ) {

            while ( $row = $sql->fetch( PDO::FETCH_ASSOC ) ) {

                $productRow[] = $row;

            }

            return $productRow;

        }

    }

    /**
     * @param null $id
     * @return LandingPage
     */
    public static function create($id = NULL) {

        return new self($id);
    }

}