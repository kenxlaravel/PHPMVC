<?php

/**
 * Class CategoryPage
 */
class CategoryPage extends Page {

    /**
     *  The page type for this class.
     */
    const TYPE = 'category';

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
    const FULL_TABLE_DUMP = "SELECT id, name, short_name, slug, active, position, page_title, page_heading, meta_keywords,
                                meta_description, image, description_text_html, intro_supplement_html, popup_html, description_image,
                                image_template, description_more_info_html, special_header_class_name, canonical_page_url,
                                ppc_campaign, ppc_adgroup, created_by, created_date, modified_by, modified_date,
                                sitemap_page_change_frequency, sitemap_page_priority, sitemap_show
                             FROM bs_categories WHERE active = 1 ";

    /**
     * Extra query parameter used with $FULL_TABLE_DUMP
     */
    const ADDITIONAL_CLAUSES = "GROUP BY id";

    /**
     * DB column: bs_categories.id
     *
     * @var int $id
     */
    private $id;

    /**
     * DB column: bs_categories.short_name
     *
     * @var string $shortName ;
     */
    private $shortName;

    /**
     * DB column: bs_Categories.name
     *
     * @var string $name
     */
    private $name;

    /**
     * @var string $slug
     */
    private $slug;

    /**
     * @var int|bool
     */
    private $active;

    /**
     * @var int $position
     */
    private $position;

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
     * @var array $image
     */
    public $imagePath;

    /**
     * @var string $descriptionTextHtml
     */
    private $descriptionTextHtml;

    /**
     * @var string $introSupplementHtml
     */
    private $introSupplementHtml;

    /**
     * @var string $popupHtml
     */
    private $popupHtml;

    /**
     * @var string $descriptionImage
     */
    private $descriptionImage;

    /**
     * @var string $imageTemplate
     */
    private $imageTemplate;

    /**
     * @var string $descriptionMoreInfoHtml
     */
    private $descriptionMoreInfoHtml;

    /**
     * @var string $specialHeaderClassName
     */
    private $specialHeaderClassName;

    /**
     * @var string $canonicalPageUrl
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
     * @var string $createdBy
     */
    private $createdBy;

    /**
     * @var string $createdDate
     */
    private $createdDate;

    /**
     * @var string $modifiedBy
     */
    private $modifiedBy;

    /**
     * @var string $modifiedDate
     */
    private $modifiedDate;

    /**
     * @var string $sitemapPageChangeFrequency
     */
    private $sitemapPageChangeFrequency;

    /**
     * @var string $sitemapPagePriority
     */
    private $sitemapPagePriority;

    /**
     * @var string $sitemapShow
     */
    private $sitemapShow;


    /**
     * Our construct
     *
     * @param string $id
     */
    public function __construct($id) {

        $this->setId($id);

        if( !is_null($this->getId()) ) {

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
            $this->setName($data['name'])->setShortName($data['short_name'])
                 ->setSlug($data['slug'])->setActive($data['active'])->setPosition($data['position'])
                 ->setPageTitle($data['page_title'])->setPageHeading($data['page_heading'])->setMetaKeywords($data['meta_keywords'])
                 ->setMetaDescription($data['meta_description'])->setImage($data['image'])->setDescriptionTextHtml($data['description_text_html'])
                 ->setIntroSupplementHtml($data['intro_supplement_html'])->setPopupHtml($data['popup_html'])->setDescriptionImage($data['description_image'])
                 ->setImageTemplate($data['image_template'])->setDescriptionMoreInfoHtml($data['description_more_info_html'])
                 ->setSpecialHeaderClassName($data['special_header_class_name'])->setCanonicalPageUrl($data['canonical_page_url'])
                 ->setPpcCampaign($data['ppc_campaign'])->setPpcAdgroup($data['ppc_adgroup'])->setCreatedBy($data['created_by'])
                 ->setCreatedDate($data['created_date'])->setModifiedBy($data['modified_by'])->setModifiedDate($data['modified_date'])
                 ->setSitemapPageChangeFrequency($data['sitemap_page_change_frequency'])->setSitemapPagePriority($data['sitemap_page_priority'])
                 ->setSitemapShow($data['sitemap_show']);
        } else {

            // Trigger a notice if an invalid ID was supplied.
            trigger_error('Cannot load properties: \'' . $this->getId() . '\' is not a valid ID number.');
        }

        // Pass the info up to the parent page
        parent::__construct(self::TYPE, $this->getId());
    }

    /**********************************************/
    /*** Start Setters                      *******/
    /**********************************************/
    /**
     * Set the Category page id
     *
     * @param $id
     * @return CategoryPage()
     */
    private function setId($id) {
        $this->id = isset($id) && is_numeric($id) && $id > 0 ? (int)$id : NULL;
        return $this;
    }

    /**
     * Set $shortName
     *
     * @param $shortName
     * @return CategoryPage()
     */
    public function setShortName($shortName) {
        $this->shortName = !empty($shortName) ? trim($shortName) : NULL;
        return $this;
    }

    /**
     * Set $active value
     *
     * @param bool|int $active
     * @return CategoryPage
     */
    public function setActive($active) {
        $this->active = $active;
        return $this;
    }

    /**
     * Set $canonicalPageUrl
     *
     * @param string $canonicalPageUrl
     * @return CategoryPage
     */
    public function setCanonicalPageUrl($canonicalPageUrl) {
        $this->canonicalPageUrl = !empty($canonicalPageUrl) ? trim($canonicalPageUrl) : NULL;
        return $this;
    }

    /**
     * Set $createdBy
     *
     * @param string $createdBy
     * @return CategoryPage
     */
    public function setCreatedBy($createdBy) {
        $this->createdBy = $createdBy;
        return $this;
    }

    /**
     * Set $createdDate
     *
     * @param string $createdDate
     * @return $this
     */
    public function setCreatedDate($createdDate) {
        $this->createdDate = $createdDate;
        return $this;
    }

    /**
     * Set $descriptionImage
     *
     * @param string $descriptionImage
     * @return CategoryPage
     */
    public function setDescriptionImage($descriptionImage) {
        $this->descriptionImage = !empty($descriptionImage) ? trim($descriptionImage) : NULL;
        return $this;
    }

    /**
     * Set $descriptionMoreInfoHtml
     *
     * @param string $descriptionMoreInfoHtml
     * @return CategoryPage
     */
    public function setDescriptionMoreInfoHtml($descriptionMoreInfoHtml) {
        $this->descriptionMoreInfoHtml = !empty($descriptionMoreInfoHtml) ? trim($descriptionMoreInfoHtml) : NULL;
        return $this;
    }

    /**
     * Set $descriptionTextHtml
     *
     * @param string $descriptionTextHtml
     * @return CategoryPage
     */
    public function setDescriptionTextHtml($descriptionTextHtml) {
        $this->descriptionTextHtml = !empty($descriptionTextHtml) ? trim($descriptionTextHtml) : NULL;
        return $this;
    }

    /**
     * Set all image paths
     *
     * @param string $image
     * @return CategoryPage
     */
    public function setImage($image) {
        $this->imagePath = $image;
        return $this;
    }

    /**
     * Set the image template file
     *
     * @param string $imageTemplate
     * @return CategoryPage
     */
    public function setImageTemplate($imageTemplate) {

        $this->imageTemplate = isset($imageTemplate) ? trim($imageTemplate).'-banner' : 'basic-banner';

        return $this;
    }

    /**
     * Set $introSupplementHtml
     *
     * @param string $introSupplementHtml
     * @return CategoryPage
     */
    public function setIntroSupplementHtml($introSupplementHtml) {
        $this->introSupplementHtml = !empty($introSupplementHtml) ? trim($introSupplementHtml) : NULL;
        return $this;
    }

    /**
     * Set $metaDescription
     *
     * @param string $metaDescription
     * @return CategoryPage
     */
    public function setMetaDescription($metaDescription) {
        $this->metaDescription = !empty($metaDescription) ? trim($metaDescription) : NULL;
        return $this;
    }

    /**
     * Set $metaKeywords
     *
     * @param string $metaKeywords
     * @return CategoryPage
     */
    public function setMetaKeywords($metaKeywords) {
        $this->metaKeywords = !empty($metaKeywords) ? trim($metaKeywords) : NULL;
        return $this;
    }

    /**
     * Set $modifiedBy
     *
     * @param string $modifiedBy
     * @return CategoryPage
     */
    public function setModifiedBy($modifiedBy) {
        $this->modifiedBy = $modifiedBy;
        return $this;
    }

    /**
     * Set $modifiedDate
     *
     * @param string $modifiedDate
     * @return CategoryPage
     */
    public function setModifiedDate($modifiedDate) {
        $this->modifiedDate = $modifiedDate;
        return $this;
    }

    /**
     * Set $name
     *
     * @param string $name
     * @return CategoryPage
     */
    public function setName($name) {
        $this->name = !empty($name) ? trim($name) : NULL;
        return $this;
    }

    /**
     * Set $pageHeading
     *
     * @param string $pageHeading
     * @return CategoryPage
     */
    public function setPageHeading($pageHeading) {
        $this->pageHeading = !empty($pageHeading) ? trim($pageHeading) : NULL;
        return $this;
    }

    /**
     * Set $pageTitle
     *
     * @param string $pageTitle
     * @return CategoryPage
     */
    public function setPageTitle($pageTitle) {
        $this->pageTitle = !empty($pageTitle) ? trim($pageTitle) : NULL;
        return $this;
    }

    /**
     * Set $popupHtml
     *
     * @param string $popupHtml
     * @return CategoryPage
     */
    public function setPopupHtml($popupHtml) {
        $this->popupHtml = !empty($popupHtml) ? trim($popupHtml) : NULL;
        return $this;
    }

    /**
     * Set $position
     *
     * @param int $position
     * @return CategoryPage
     */
    public function setPosition($position) {
        $this->position = $position;
        return $this;
    }

    /**
     * Set $ppcAdgroup
     *
     * @param string $ppcAdgroup
     * @return CategoryPage
     */
    public function setPpcAdgroup($ppcAdgroup) {
        $this->ppcAdgroup = $ppcAdgroup;
        return $this;
    }

    /**
     * Set $ppcCampaign
     *
     * @param string $ppcCampaign
     * @return CategoryPage
     */
    public function setPpcCampaign($ppcCampaign) {
        $this->ppcCampaign = $ppcCampaign;
        return $this;
    }

    /**
     * Set $sitemapPageChangeFrequency
     *
     * @param string $sitemapPageChangeFrequency
     * @return CategoryPage
     */
    public function setSitemapPageChangeFrequency($sitemapPageChangeFrequency) {
        $this->sitemapPageChangeFrequency = $sitemapPageChangeFrequency;
        return $this;
    }

    /**
     * Set $sitemapPagePriority
     *
     * @param string $sitemapPagePriority
     * @return CategoryPage
     */
    public function setSitemapPagePriority($sitemapPagePriority) {
        $this->sitemapPagePriority = $sitemapPagePriority;
        return $this;
    }

    /**
     * Set $sitemapShow
     *
     * @param string $sitemapShow
     * @return CategoryPage
     */
    public function setSitemapShow($sitemapShow) {
        $this->sitemapShow = !empty($sitemapShow) ? trim($sitemapShow) : NULL;
        return $this;
    }

    /**
     * Set the $slug
     *
     * @param string $slug
     * @return CategoryPage
     */
    public function setSlug($slug) {
        $this->slug = !empty($slug) ? trim($slug) : NULL;
        return $this;
    }

    /**
     * Set $specialHeaderClassName
     *
     * @param string $specialHeaderClassName
     * @return CategoryPage
     */
    public function setSpecialHeaderClassName($specialHeaderClassName) {
        $this->specialHeaderClassName = !empty($specialHeaderClassName) ? trim($specialHeaderClassName) : NULL;
        return $this;
    }

    /**********************************************/
    /*** Start Getters                      *******/
    /**********************************************/
    /**
     * Get the CategoryPage id
     *
     * @return int $id;
     */
    public function getId() {
        return (int)$this->id;
    }

    /**
     * Get $active value
     *
     * @return bool|int
     */
    public function isActive() {
        return $this->active;
    }

    /**
     * Get $canonicallPageUrl
     *
     * @return string
     */
    public function getCanonicalPageUrl() {
        return $this->canonicalPageUrl;
    }

    /**
     * Get $createdBy
     *
     * @return string
     */
    public function getCreatedBy() {
        return $this->createdBy;
    }

    /**
     * Get $createdDate
     *
     * @return string
     */
    public function getCreatedDate() {
        return $this->createdDate;
    }

    /**
     * Get $descriptionImage
     *
     * @return string
     */
    public function getDescriptionImage() {
        return $this->descriptionImage;
    }

    /**
     * Get $descriptionMoreInfoHtml
     *
     * @return string
     */
    public function getDescriptionMoreInfoHtml() {
        return $this->descriptionMoreInfoHtml;
    }

    /**
     * Get $descriptionTextHtml
     *
     * @return string
     */
    public function getDescriptionTextHtml() {
        return $this->descriptionTextHtml;
    }

    /**
     * Get $imagePath
     *
     * @return string
     */
    public function getImage() {
        return $this->imagePath;
    }

    /**
     * Get $imageTemplate file
     *
     * @return string
     */
    public function getImageTemplate() {
        return $this->imageTemplate;
    }

    /**
     * Get $introSupplementHtml value
     *
     * @return string
     */
    public function getIntroSupplementHtml() {
        return $this->introSupplementHtml;
    }

    /**
     * Get $metaDescription
     *
     * @return string
     */
    public function getMetaDescription() {
        return $this->metaDescription;
    }

    /**
     * Get $metaKeywords
     *
     * @return string
     */
    public function getMetaKeywords() {
        return $this->metaKeywords;
    }

    /**
     * Get $modifiedBy
     *
     * @return string
     */
    public function getModifiedBy() {
        return $this->modifiedBy;
    }

    /**
     * Get $modifiedDate
     *
     * @return string
     */
    public function getModifiedDate() {
        return $this->modifiedDate;
    }

    /**
     * Get $name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get $pageHeading
     *
     * @return string
     */
    public function getPageHeading() {
        return $this->pageHeading;
    }

    /**
     * Get $pageTitle
     *
     * @return string
     */
    public function getPageTitle() {
        return $this->pageTitle;
    }

    /**
     * Get $popupHtml
     *
     * @return string
     */
    public function getPopupHtml() {
        return $this->popupHtml;
    }

    /**
     * Get $position
     *
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Get $ppcAdgroup
     *
     * @return string
     */
    public function getPpcAdgroup() {
        return $this->ppcAdgroup;
    }

    /**
     * Get $ppcCampaign
     *
     * @return string
     */
    public function getPpcCampaign() {
        return $this->ppcCampaign;
    }

    /**
     * Get $sitemapPageChangeFrequency
     *
     * @return string
     */
    public function getSitemapPageChangeFrequency() {
        return $this->sitemapPageChangeFrequency;
    }

    /**
     * Get $sitemapPagePriority
     *
     * @return string
     */
    public function getSitemapPagePriority() {
        return $this->sitemapPagePriority;
    }

    /**
     * Get $sitemapShow
     *
     * @return string
     */
    public function getSitemapShow() {
        return $this->sitemapShow;
    }

    /**
     * Get $slug
     *
     * @return string
     */
    public function getSlug() {
        return $this->slug;
    }

    /**
     * Get $specialHeaderClassName
     *
     * @return string
     */
    public function getSpecialHeaderClassName() {
        return $this->specialHeaderClassName;
    }

    /**
     * Get shortname for the Category Page
     *
     * @return string $shortName
     */
    public function getShortName() {
        return $this->shortName;
    }

    /**
     * @TODO: Implement cache system to this method
     *
     * Used to get listings based off of a location on the page. Example: 'grid' will return all
     * categories that appear on the category page grouping grid
     *
     * @param     string $location The location on the page that we need listings for
     * @return    array     $refid       An array of all the listings
     */
    public function getListings($location, $refid = NULL) {

        $stmt = NULL;
        $results = NULL;

        switch ($location) {

            case 'grid':

                $sql = "SELECT t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
                         c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
                         c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                         c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login, t.disallow_guests AS disallow_guests,
                         c.sitemap_show AS visibility, c.sitemap_page_priority AS priority, c.sitemap_page_change_frequency AS change_frequency, c.*
                        FROM bs_groupings c JOIN bs_pagetypes t
                        LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
                        LEFT JOIN bs_categories cat ON (cat.id = c.category_id)
                        LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
                        WHERE cat.active = TRUE AND c.active = TRUE AND cat.id = ? AND t.pagetype = 'grouping'
                        GROUP BY c.id ORDER BY c.position ASC, c.name ASC";


                break;

            case 'sidebar':

                $sql = "SELECT
                         t.pagetype AS type, c.id AS id, n.nickname AS nickname, IF(COUNT(c.id) > 0, TRUE, FALSE) AS validity,
                         c.name AS name, u.url AS short_url, t.template_secure AS secure, c.slug AS slug, t.template_filename AS filename,
                         c.id AS canonical, c.page_title AS title, c.meta_description AS meta_description, c.meta_keywords AS meta_keywords,
                         c.page_heading AS heading, t.allow_target AS allow_target, t.requires_login AS requires_login, t.disallow_guests AS disallow_guests,
                         c.sitemap_show AS visibility, c.sitemap_page_priority AS priority,c.sitemap_page_change_frequency AS change_frequency, c.accessory AS accessory
                        FROM bs_groupings c JOIN bs_pagetypes t
                         LEFT JOIN bs_page_urls u ON (u.id = c.canonical_page_url AND u.pagetype = t.pagetype AND u.pageid = c.id)
                         LEFT JOIN bs_categories cat ON (cat.id = c.category_id)
                         LEFT JOIN bs_page_nicknames n ON (t.pagetype = n.pagetype AND c.id = n.pageid)
                         WHERE cat.active = TRUE AND c.active = TRUE AND cat.id = ? AND t.pagetype = 'grouping' GROUP BY c.id ORDER BY c.accessory ASC,
                         c.name ASC";


                break;
        }

        $stmt = Connection::getHandle()->prepare($sql);

        if( $stmt->execute(array((int) $this->id)) ) {

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

                $results[] = $row;
            }

        }
        return $results;
    }

    /**
     * Create a self instance
     *
     * @param null $id
     * @return CategoryPage
     */
    public static function create($id = NULL) {
        return new self($id);
    }
}